<?php
// Configuración segura de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');

// TEMPORAL: Desactivar OPcache para este archivo (eliminar después de probar)
if (function_exists('opcache_invalidate')) {
    opcache_invalidate(__FILE__, true);
}

// LOG MUY TEMPRANO - Versión del archivo
error_log("🔵🔵🔵 REGISTRO-GUARDAR.PHP - VERSIÓN ACTUALIZADA - ".date('Y-m-d H:i:s')." 🔵🔵🔵");

session_start();
require_once("../conexion.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/Csrf.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
require_once(ROOT_PATH . "/main-app/class/Notificacion.php");

$notificacion = new Notificacion();

// LOG DE INICIO (ANTES de CSRF para ver si llega al archivo)
error_log("========================================");
error_log("INICIO DE REGISTRO-GUARDAR.PHP");
error_log("POST recibido: " . print_r(array_keys($_POST), true));
error_log("Verificando usosSintia en POST: " . (isset($_POST['usosSintia']) ? 'SÍ EXISTE' : 'NO EXISTE'));
if(isset($_POST['usosSintia'])) {
	error_log("usosSintia recibido: " . print_r($_POST['usosSintia'], true));
}
error_log("========================================");

// VALIDAR TOKEN CSRF
verificarTokenCSRF(false); // false = no es AJAX, redirige si falla

error_log("CSRF validado correctamente, continuando...");

/**
 * VALIDACIÓN DE RECAPTCHA v3
 */
$recaptchaToken = isset($_POST['recaptchaToken']) ? $_POST['recaptchaToken'] : '';
$recaptchaValid = false;

if (!empty($recaptchaToken)) {
    $secretKey = '6LfH9KkqAAAAAI3vc_wWTW0EfV0qGVs2cVXe8gGc'; // Clave secreta de reCAPTCHA
    
    $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptchaData = [
        'secret' => $secretKey,
        'response' => $recaptchaToken,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptchaData)
        ]
    ];
    
    $context = stream_context_create($options);
    $verify = file_get_contents($recaptchaUrl, false, $context);
    $captchaSuccess = json_decode($verify);
    
    // Verificar score de reCAPTCHA (debe ser > 0.5 para considerarse humano)
    if ($captchaSuccess->success && $captchaSuccess->score >= 0.5) {
        $recaptchaValid = true;
    }
}

// Si la validación de reCAPTCHA falla, registrar advertencia pero continuar
// (reCAPTCHA es opcional para no bloquear registros legítimos por problemas técnicos)
if (!$recaptchaValid) {
    error_log("Advertencia: reCAPTCHA no validado para registro - IP: " . $_SERVER['REMOTE_ADDR']);
}

/**
 * VALIDACIÓN DE CÓDIGO DE VERIFICACIÓN
 */
$idRegistro = isset($_POST['idRegistro']) ? (int)$_POST['idRegistro'] : 0;

if ($idRegistro > 0) {
    $consultaCodigo = mysqli_query($conexion, "SELECT * FROM " . BD_GENERAL . ".usuarios_codigo_verificacion 
        WHERE codv_id='{$idRegistro}' AND codv_verificado=1 AND codv_usuario_asociado IS NULL LIMIT 1");
    
    if (mysqli_num_rows($consultaCodigo) == 0) {
        $_SESSION['mensajeError'] = "Código de verificación no válido. Por favor verifica tu email.";
        header("Location: registro.php?" . http_build_query($_POST));
        exit();
    }
    
    $datosCodigoVerificacion = mysqli_fetch_array($consultaCodigo, MYSQLI_BOTH);
    
    // Verificar que el código no haya expirado
    $fechaExpiracion = strtotime($datosCodigoVerificacion['codv_fecha_expiracion']);
    if (time() > $fechaExpiracion) {
        $_SESSION['mensajeError'] = "El código de verificación ha expirado. Por favor solicita uno nuevo.";
        header("Location: registro.php?" . http_build_query($_POST));
        exit();
    }
} else {
    $_SESSION['mensajeError'] = "Código de verificación requerido.";
    header("Location: registro.php?" . http_build_query($_POST));
    exit();
}

$fecha=date("Y-m-d");
$fechaCompleta = date("Y-m-d H:i:s");
$nombreInsti = $_POST['nombreIns'];
$siglasInst = $_POST['siglasInst'];
$year = date("Y");
$bdInstitucion=BD_PREFIX.$siglasInst;

// CAPTURAR USOS DE SINTIA TEMPRANO (antes de transacción para no perderlos)
error_log("========== CAPTURANDO USOS DE SINTIA ==========");
error_log("¿Existe usosSintia en POST? " . (isset($_POST['usosSintia']) ? 'SÍ' : 'NO'));

$usosSintia = isset($_POST['usosSintia']) && is_array($_POST['usosSintia']) ? $_POST['usosSintia'] : [];
error_log("Usos capturados (count): " . count($usosSintia));
error_log("Usos capturados (array): " . json_encode($usosSintia));

$usosSintiaTextos = [];
$mapeoUsos = [
	'academico' => 'Gestión Académica',
	'administrativo' => 'Gestión Administrativa',
	'comunicacion' => 'Comunicación',
	'integral' => 'Gestión Integral'
];

foreach($usosSintia as $uso) {
	error_log("Procesando uso: " . $uso);
	if(isset($mapeoUsos[$uso])) {
		$usosSintiaTextos[] = $mapeoUsos[$uso];
		error_log("Texto agregado: " . $mapeoUsos[$uso]);
	}
}

$usoSintiaTexto = !empty($usosSintiaTextos) ? implode(', ', $usosSintiaTextos) : 'No especificado';
error_log("TEXTO FINAL DE USOS: " . $usoSintiaTexto);
error_log("===============================================");

try {
	mysqli_query($conexion, "BEGIN");

	//CREAMOS LA INSTITUCIÓN
	try{			
		// Definir un array asociativo con los campos y valores a insertar
		$dataToInsert = array(
			'ins_nombre' => $nombreInsti,
			'ins_fecha_inicio' => $fechaCompleta,
			'ins_telefono_principal' => $_POST['celular'],
			'ins_contacto_principal' => $_POST['nombre']." ".$_POST['apellidos'],
			'ins_cargo_contacto' => $_POST['cargo'],
			'ins_celular_contacto' => $_POST['celular'],
			'ins_email_contacto' => $_POST['email'],
			'ins_enviroment' => ENVIROMENT,
			'ins_estado' => 1,
			'ins_bd' => $bdInstitucion,
			'ins_bloqueada' => 0,
			'ins_years' => $year . "," . $year,
			'ins_notificaciones_acudientes' => 0,
			'ins_siglas' => $siglasInst,
			'ins_id_plan' => 1, // Plan básico por defecto
			'ins_year_default' => $year,
			'ins_tipo' => SCHOOL
		);

		// Crear la consulta SQL
		$query = "INSERT INTO ".BD_ADMIN.".instituciones (";
		$columns = array_keys($dataToInsert);
		$values = array_values($dataToInsert);

		$query .= implode(', ', $columns);
		$query .= ") VALUES (";
		$query .= "'" . implode("', '", $values) . "'";
		$query .= ")";

		// Ejecutar la consulta SQL
		mysqli_query($conexion, $query);

	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}
	$idInsti = mysqli_insert_id($conexion);
	
	error_log("INSTITUCIÓN CREADA CON ID: " . $idInsti);
	error_log("Procediendo a relacionar módulos...");

	//RELACIONAR MÓDULOS (solo visibles, activos y con mod_default_install = 1)
	try{
		require_once(ROOT_PATH."/main-app/class/Conexion.php");
		
		error_log("Auto-registro - Iniciando relación de módulos para institución: " . $idInsti);
		
		// Solo instalar módulos que estén visibles, activos y con mod_default_install = 1
		$consultaModulos = mysqli_query($conexion, "SELECT mod_id FROM ".BD_ADMIN.".modulos 
			WHERE mod_estado = 1 
			AND mod_visible = 1 
			AND mod_default_install = 1");
		
		if (!$consultaModulos) {
			error_log("Auto-registro - Error en query de módulos: " . mysqli_error($conexion));
			throw new Exception('Error al consultar módulos: ' . mysqli_error($conexion));
		}
		
		$numModulos = mysqli_num_rows($consultaModulos);
		error_log("Auto-registro - Módulos encontrados (visibles, activos y con mod_default_install=1): " . $numModulos);
		
		if ($consultaModulos && $numModulos > 0) {
			$valoresModulos = [];
			while ($modulo = mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)) {
				$valoresModulos[] = "($idInsti, ".$modulo['mod_id'].")";
			}
			
			error_log("Auto-registro - Valores a insertar: " . count($valoresModulos));
			
			if (!empty($valoresModulos)) {
				$sqlModulos = "INSERT INTO ".BD_ADMIN.".instituciones_modulos (ipmod_institucion, ipmod_modulo) 
							   VALUES " . implode(',', $valoresModulos);
				
				error_log("Auto-registro - SQL módulos: " . substr($sqlModulos, 0, 200) . "...");
				
				$resultadoModulos = mysqli_query($conexion, $sqlModulos);
				
				if (!$resultadoModulos) {
					error_log("Auto-registro - Error al insertar módulos: " . mysqli_error($conexion));
					throw new Exception('Error al insertar módulos: ' . mysqli_error($conexion));
				}
				
				$filasAfectadas = mysqli_affected_rows($conexion);
				error_log("Auto-registro - Módulos insertados: " . $filasAfectadas);
			}
		} else {
			error_log("Auto-registro - ADVERTENCIA: No se encontraron módulos activos");
		}
	} catch (Exception $e) {
		error_log("Auto-registro - EXCEPCIÓN en módulos: " . $e->getMessage());
		echo $e->getMessage();
		exit();
	}

	//AÑADIR AQUI CONSULTA PARA GUARDAR PAQUETES

	//CREAMOS CONFIGURACIÓN DE LA INSTITUCIÓN
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ADMIN.".configuracion (conf_agno,conf_periodo,conf_nota_desde,conf_nota_hasta,conf_nota_minima_aprobar,conf_color_perdida,conf_color_ganada,conf_saldo_pendiente,conf_num_restaurar,conf_restaurar_cantidad,conf_color_borde,conf_color_encabezado,conf_tam_borde,conf_num_materias_perder_agno,conf_inicio_matrucula,conf_fin_matricula,conf_apertura_academica,conf_clausura_academica,conf_periodos_maximos,conf_num_indicadores,conf_valor_indicadores,conf_notas_categoria,conf_id_institucion,conf_base_datos,conf_servidor,conf_num_registros,conf_agregar_porcentaje_asignaturas,conf_fecha_parcial,conf_descripcion_parcial,conf_ancho_imagen,conf_alto_imagen,conf_mostrar_nombre,conf_deuda,conf_permiso_eliminar_cargas,conf_concepto,conf_inicio_recibos_ingreso,conf_inicio_recibos_egreso,conf_decimales_notas,conf_activar_encuesta,conf_sin_nota_numerica,conf_numero_factura,conf_max_peso_archivos,conf_informe_parcial,conf_ver_observador,conf_ficha_estudiantil,conf_solicitar_acudiente_2,conf_mostrar_campos,conf_calificaciones_acudientes,conf_mostrar_calificaciones_estudiantes,conf_orden_nombre_estudiantes,conf_editar_definitivas_consolidado,conf_observaciones_multiples_comportamiento,conf_cambiar_nombre_usuario,conf_cambiar_clave_estudiantes,conf_permiso_descargar_boletin,conf_certificado,conf_firma_estudiante_informe_asistencia,conf_permiso_edicion_years_anteriores,conf_porcentaje_completo_generar_informe,conf_ver_promedios_sabanas_docentes) VALUES ('".$year."',1,1,5,3,'#e10000','#0000d5',NULL,NULL,NULL,'#000000','#ff0080',1,3,'".$fecha."','".$fecha."','".$fecha."','".$fecha."',4,NULL,NULL,NULL,'".$idInsti."','".$bdInstitucion."',NULL,20,'NO',NULL,NULL,'200','150',1,NULL,'NO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'5',0,0,0,'NO',1,1,1,1,0,0,'SI','SI',1,1,1,1,1,1)");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}

	//CREAMOS LA NUEVA INFORMACIÓN DE LA INSTITUCIÓN
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ADMIN.".general_informacion (info_rector,info_secretaria_academica,info_logo,info_nit,info_nombre,info_direccion,info_telefono,info_clase,info_caracter,info_calendario,info_jornada,info_horario,info_niveles,info_modalidad,info_propietario,info_coordinador_academico,info_tesorero,info_dane,info_ciudad,info_resolucion,info_decreto_plan_estudio,info_institucion,info_year) VALUES ('2','2','sintia-logo-2023.png','0000000000-0','".$nombreInsti."','Cra 00 # 00-00','(000)000-0000','Privado','Mixto','A','Mañana','6:00 am - 12:30 pm','Preescolar, Basica, Media','Academica','PROPIETARIO PRUEBA','2','2', NULL, NULL, NULL, NULL,'".$idInsti."','".$year."')");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}

	//CURSOS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_grados(gra_id, gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, gra_estado, institucion, year, gra_grado_siguiente, gra_vocal, gra_nivel, gra_grado_anterior, gra_periodos, gra_nota_minima, gra_tipo) VALUES 
		('1','0','PRIMERO',8,0,0,1,'".$idInsti."','".$year."','2',NULL,NULL,'15',4,NULL,'grupal'),
		('2','0','SEGUNDO',8,0,0,1,'".$idInsti."','".$year."','3',NULL,NULL,'1',4,NULL,'grupal'),
		('3','0','TERCERO',8,0,0,1,'".$idInsti."','".$year."','4',NULL,NULL,'2',4,NULL,'grupal'),
		('4','0','CUARTO',8,0,0,1,'".$idInsti."','".$year."','5',NULL,NULL,'3',4,NULL,'grupal'),
		('5','0','QUINTO',8,0,0,1,'".$idInsti."','".$year."','6',NULL,NULL,'4',4,NULL,'grupal'),
		('6','0','SEXTO',8,0,0,1,'".$idInsti."','".$year."','7',NULL,NULL,'5',4,NULL,'grupal'),
		('7','0','SEPTIMO',8,0,0,1,'".$idInsti."','".$year."','8',NULL,NULL,'6',4,NULL,'grupal'),
		('8','0','OCTAVO',8,0,0,1,'".$idInsti."','".$year."','9',NULL,NULL,'7',4,NULL,'grupal'),
		('9','0','NOVENO',8,0,0,1,'".$idInsti."','".$year."','10',NULL,NULL,'8',4,NULL,'grupal'),
		('10','0','DECIMO',8,0,0,1,'".$idInsti."','".$year."','11',NULL,NULL,'9',4,NULL,'grupal'),
		('11','0','UNDECIMO',8,0,0,1,'".$idInsti."','".$year."','0',NULL,NULL,'10',4,NULL,'grupal'),
		('12','0','PARVULOS',8,0,0,1,'".$idInsti."','".$year."','13',NULL,NULL,'0',4,NULL,'grupal'),
		('13','0','PREJARDIN',8,0,0,1,'".$idInsti."','".$year."','14',NULL,NULL,'12',4,NULL,'grupal'),
		('14','0','JARDIN',8,0,0,1,'".$idInsti."','".$year."','15',NULL,NULL,'13',4,NULL,'grupal'),
		('15','0','TRANSICION',8,0,0,1,'".$idInsti."','".$year."','1',NULL,NULL,'14',4,NULL,'grupal')
		");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}

	//GRUPOS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_grupos(gru_id, gru_codigo, gru_nombre, gru_jornada, gru_horario, institucion, year) VALUES 
		('1',1267,'A',NULL,NULL,'".$idInsti."','".$year."'),
		('2',1268,'B',NULL,NULL,'".$idInsti."','".$year."'),
		('3',1269,'C',NULL,NULL,'".$idInsti."','".$year."'),
		('4',1270,'Sin grupo',NULL,NULL,'".$idInsti."','".$year."')
		");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}

	//CATEGORIA NOTAS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_categorias_notas(catn_id, catn_nombre, institucion, year) VALUES ('1','Desempeños (Bajo a Superior)','".$idInsti."','".$year."'),('2','Letras (D a E)','".$idInsti."','".$year."'),('3','Numerica de 0 a 100','".$idInsti."','".$year."'),('4','Caritas (Llorando - Contento)','".$idInsti."','".$year."')
		");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}

	//TIPOS DE NOTAS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_notas_tipos(notip_id, notip_nombre, notip_desde, notip_hasta, notip_categoria, notip_nombre2, notip_imagen, institucion, year) VALUES ('1','Bajo',1.00,3.49,'1',NULL,'bajo.png','".$idInsti."','".$year."'),('2','Basico',3.50,3.99,'1',NULL,'bas.png','".$idInsti."','".$year."'),('3','Alto',4.00,4.59,'1',NULL,'alto.png','".$idInsti."','".$year."'),('4','Superior',4.60,5.00,'1',NULL,'sup.png','".$idInsti."','".$year."')
		");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}
	
	//AREAS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_areas(ar_id, ar_nombre, ar_posicion, institucion, year) VALUES ('1','AREA DE PRUEBA',1,'".$idInsti."','".$year."')");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}
	
	//MATERIAS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_materias(mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area, institucion, year, mat_oficial, mat_portada, mat_valor) VALUES ('1','1','MATERIA DE PRUEBA','PRU','1','".$idInsti."','".$year."',NULL,NULL,NULL)");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}
	
	//TODOS LOS USUARIOS (con tema blanco por defecto)
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_tipo_documento, uss_documento, uss_tema_sidebar, uss_tema_header, uss_tema_logo, institucion, year) VALUES 
		('1','sintia-".$idInsti."',SHA1('sintia2014$'),1,'ADMINISTRACIÓN', NULL, 'SINTIA', NULL,0,'default.png','default.png',1,'orange','','Administrador','soporte@plataformasintia.com','2022-12-06',1298,'(313) 591-2073',126,'2023-01-26 05:56:36','2023-01-26 05:55:46','853755',0, NULL, NULL,'white-sidebar-color','header-white','logo-white','".$idInsti."','".$year."'),
		('2','directivo-".$idInsti."',SHA1('12345678'),5,'".$_POST['nombre']."',NULL,'".$_POST['apellidos']."',NULL,0,'default.png','default.png',1,'orange','','DIRECTIVO', '".$_POST['email']."',NULL, 1298, '".$_POST['celular']."',126,NULL,NULL,NULL,0, NULL, NULL,'white-sidebar-color','header-white','logo-white','".$idInsti."','".$year."'),
		('3','pruebaDC-".$idInsti."',SHA1('12345678'),2,'USUARIO', NULL,'DOCENTE', NULL,0,'default.png','default.png',1,'orange','','DOCENTE',NULL,NULL,0,NULL,126,NULL,NULL,NULL,0, NULL, NULL,'white-sidebar-color','header-white','logo-white','".$idInsti."','".$year."'),
		('4','pruebaAC-".$idInsti."',SHA1('12345678'),3,'USUARIO', NULL,'ACUDIENTE', NULL,0,'default.png','default.png',1,'orange','','ACUDIENTE',NULL,NULL,0,NULL,126,NULL,NULL,NULL,0, NULL, NULL,'white-sidebar-color','header-white','logo-white','".$idInsti."','".$year."'),
		('5','pruebaES-".$idInsti."',SHA1('12345678'),4,'USUARIO', NULL,'ESTUDIANTE', NULL,0,'default.png','default.png',1,'orange','','ESTUDIANTE',NULL,NULL,0,NULL,126,NULL,NULL,NULL,0, NULL, NULL,'white-sidebar-color','header-white','logo-white','".$idInsti."','".$year."');");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}
	
	//TODOS LAS MATRICULAS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_matriculas(mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_lugar_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_religion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, mat_foto, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, mat_email, mat_acudiente, mat_privilegio1, mat_privilegio2, mat_privilegio3, institucion, year) VALUES ('1','00001','0000-00-00 00:00:00','PRUEBA','DE','ESTUDIANTE','1','1',126,'1993-10-21','1',108,'0000000000','1',111,'Cra 00 #00-00','B. Prueba',NULL,NULL,116,NULL,129,1,'5',0,NULL,'4',0,'0',0,'".$idInsti."','".$year."')");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}
	
	//TODOS LOS USUARIOS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios_por_estudiantes(upe_id, upe_id_usuario, upe_id_estudiante, upe_parentezco, institucion, year) VALUES 
		('1', '4', '1', 'Padre', '".$idInsti."', '".$year."');");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}

	//CARGAS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_cargas(car_id, car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable, institucion, year, car_configuracion, car_valor_indicador, car_posicion_docente, car_primer_acceso_docente, car_ultimo_acceso_docente, car_permiso2, car_maximos_indicadores, car_maximas_calificaciones, car_fecha_generar_informe_auto, car_fecha_automatica, car_evidencia, car_saberes_indicador, car_inicio, car_fin, car_indicador_automatico, car_observaciones_boletin, car_tematica, car_curso_extension) VALUES ('1','3','1','1','1',1,1,1,'3',2,'0000-00-00 00:00:00',2,'".$idInsti."','".$year."',0,0,1,NULL,NULL,0,10,100,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}

	//DEMO
	try{
		mysqli_query($conexion, "INSERT INTO demo(demo_fecha_ingreso, demo_usuario, demo_ip, demo_cantidad, demo_correo_enviado, demo_fecha_ultimo_correo, demo_nocorreos, demo_plan, demo_institucion)VALUES(now(), '2', '" . $_SERVER["REMOTE_ADDR"] . "', 0, 1, now(), 0, '1', '".$idInsti."')");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}

	mysqli_query($conexion, "COMMIT");

} catch(Exception $e){
	mysqli_query($conexion, "ROLLBACK");
	echo $e->getMessage();
	exit();
}

$data = [
	'institucion_id'   => $idInsti,
	'institucion_agno' => $year,
	'institucion_nombre' => $nombreInsti,
	'usuario_id'       => '2',
	'usuario_email'    => $_POST['email'],
	'usuario_nombre'   => $_POST["nombre"]." ".$_POST["apellidos"],
	'usuario_usuario'  => "directivo-".$idInsti,
	'usuario_clave'    => '12345678',
	'uso_sintia'       => $usoSintiaTexto, // MÚLTIPLES usos separados por coma
	'url_acceso'       => REDIRECT_ROUTE.'/index.php?inst='.base64_encode($idInsti).'&year='.base64_encode($year)
];

// Log para debugging del correo
error_log("Auto-registro - Data del correo: " . print_r($data, true));

$asunto = $_POST["nombre"] . ', Bienvenido a la Plataforma SINTIA';
$bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-bienvenida.php';

try {
	EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute,null,null);
	error_log("Auto-registro - Correo de bienvenida enviado exitosamente a: " . $_POST['email']);
} catch(Exception $emailError) {
	error_log("Auto-registro - ERROR al enviar correo: " . $emailError->getMessage());
	// No detener el proceso si falla el correo
}

$datos = [
	'codv_usuario_asociado'    	=> '2',
	'institucion'       		=> $idInsti,
	'year'       				=> $year
];

$predicado = [
	'codv_id' => $_REQUEST["idRegistro"]
];

$notificacion->actualizarCodigo($datos, $predicado);

//FIN ENVÍO DE MENSAJE
echo '<script type="text/javascript">window.location.href="bienvenida.php?inf=' . base64_encode(serialize($data)) . '";</script>';
exit();