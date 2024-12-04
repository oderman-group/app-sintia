<?php
session_start();
require_once("../conexion.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
require_once(ROOT_PATH . "/main-app/class/Notificacion.php");

$notificacion = new Notificacion();

$fecha=date("Y-m-d");
$fechaCompleta = date("Y-m-d H:i:s");
$nombreInsti = $_POST['nombreIns'];
$siglasInst = $_POST['siglasInst'];
$year = date("Y");
$bdInstitucion=BD_PREFIX.$siglasInst;

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
			'ins_cargo_contacto' => NULL,
			'ins_celular_contacto' => $_POST['celular'],
			'ins_email_contacto' => $_POST['email'],
			'ins_email_institucion' => NULL,
			'ins_ciudad' => NULL,
			'ins_enviroment' => ENVIROMENT,
			'ins_nit' => NULL,
			'ins_medio_info' => NULL,
			'ins_estado' => 1,
			'ins_url_acceso' => NULL,
			'ins_bd' => $bdInstitucion,
			'ins_deuda' => NULL,
			'ins_valor_deuda' => NULL,
			'ins_concepto_deuda' => NULL,
			'ins_bloqueada' => 0,
			'ins_years' => $year . "," . $year,
			'ins_notificaciones_acudientes' => 0,
			'ins_siglas' => $siglasInst,
			'ins_fecha_renovacion' => NULL,
			'ins_id_plan' => $_POST['plan'],
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

	//BUSCAMOS MODULOS DEL PLAN ESCOGIDO
	try{
		$consultaModulos = mysqli_query($conexion, "SELECT plns_modulos FROM ".BD_ADMIN.".planes_sintia WHERE plns_id='".$_POST['plan']."'");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}
	$datosModulos = mysqli_fetch_array($consultaModulos, MYSQLI_BOTH);
	$arrayModulos = explode(",", $datosModulos['plns_modulos']);
	$modulosInsertar = "";
	foreach ($arrayModulos as $idModulo) {
		$modulosInsertar .= '('.$idInsti.','.$idModulo.'),';
	}
	$modulosInsertar = substr($modulosInsertar,0,-1);

	//GUADAMOS LOS MODULOS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ADMIN.".instituciones_modulos (ipmod_institucion,ipmod_modulo) VALUES $modulosInsertar");
	} catch (Exception $e) {
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
	
	//TODOS LOS USUARIOS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_tipo_documento, uss_documento, institucion, year) VALUES 
		('1','sintia-".$idInsti."',SHA1('sintia2014$'),1,'ADMINISTRACIÓN', NULL, 'SINTIA', NULL,0,'default.png','default.png',1,'orange','','Administrador','soporte@plataformasintia.com','2022-12-06',1298,'(313) 591-2073',126,'2023-01-26 05:56:36','2023-01-26 05:55:46','853755',0, NULL, NULL,'".$idInsti."','".$year."'),
		('2','directivo-".$idInsti."',SHA1('12345678'),5,'".$_POST['nombre']."',NULL,'".$_POST['apellidos']."',NULL,0,'default.png','default.png',1,'orange','','DIRECTIVO', '".$_POST['email']."',NULL, 1298, '".$_POST['celular']."',126,NULL,NULL,NULL,0, NULL, NULL,'".$idInsti."','".$year."'),
		('3','pruebaDC-".$idInsti."',SHA1('12345678'),2,'USUARIO', NULL,'DOCENTE', NULL,0,'default.png','default.png',1,'orange','','DOCENTE',NULL,NULL,0,NULL,126,NULL,NULL,NULL,0, NULL, NULL,'".$idInsti."','".$year."'),
		('4','pruebaAC-".$idInsti."',SHA1('12345678'),3,'USUARIO', NULL,'ACUDIENTE', NULL,0,'default.png','default.png',1,'orange','','ACUDIENTE',NULL,NULL,0,NULL,126,NULL,NULL,NULL,0, NULL, NULL,'".$idInsti."','".$year."'),
		('5','pruebaES-".$idInsti."',SHA1('12345678'),4,'USUARIO', NULL,'ESTUDIANTE', NULL,0,'default.png','default.png',1,'orange','','ESTUDIANTE',NULL,NULL,0,NULL,126,NULL,NULL,NULL,0, NULL, NULL,'".$idInsti."','".$year."');");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}
	
	//TODOS LAS MATRICULAS
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_matriculas(mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_lugar_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_religion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, mat_foto, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, mat_email, mat_acudiente, mat_privilegio1, mat_privilegio2, mat_privilegio3, institucion, year) VALUES ('1','00001','0000-00-00 00:00:00','PRUEBA','DE','ESTUDIANTE','1','1',126,'1993-10-21','1',108,'0000000000','1',111,'Cra 00 #00-00','B. Prueba',NULL,NULL,116,NULL,129,1,'5',0,'notiene@notiene.com','4',0,'0',0,'".$idInsti."','".$year."')");
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
		mysqli_query($conexion, "INSERT INTO demo(demo_fecha_ingreso, demo_usuario, demo_ip, demo_cantidad, demo_correo_enviado, demo_fecha_ultimo_correo, demo_nocorreos, demo_plan, demo_institucion)VALUES(now(), '2', '" . $_SERVER["REMOTE_ADDR"] . "', 0, 1, now(), 0, '" . $_POST["plan"] . "', '".$idInsti."')");
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
	'usuario_id'       => '2',
	'usuario_email'    => $_POST['email'],
	'usuario_nombre'   => $_POST["nombre"]." ".$_POST["apellidos"],
	'usuario_usuario'  => "directivo-".$idInsti,
	'usuario_clave'    => '12345678'
];
$asunto = $_POST["nombre"] . ', Bienvenido a la Plataforma SINTIA';
$bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-bienvenida.php';

EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute,null,null);

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