<?php
/**
 * Wrapper JSON seguro - Captura TODO el output
 */

// Configuración estricta
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Capturar absolutamente todo
ob_start();

// Buffer de seguridad adicional
ob_start();

$finalResponse = [
    'success' => false,
    'message' => 'Error desconocido',
    'debug' => []
];

try {
    // Incluir dependencias
    include("session.php");
    
    if (!defined('BD_GENERAL')) {
        require_once(ROOT_PATH."/conexion.php");
    }
    
    require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
    require_once(ROOT_PATH."/main-app/class/Modulos.php");
    
    // Limpiar cualquier output de los includes
    ob_end_clean();
    ob_start();
    
    Modulos::verificarPermisoDev();
    
    date_default_timezone_set("America/New_York");
    
    // Verificar conexión
    if (!isset($conexion)) {
        throw new Exception('Conexión a base de datos no disponible');
    }
    
    // Verificar constantes
    if (!defined('BD_GENERAL') || !defined('BD_ACADEMICA') || !defined('BD_ADMIN')) {
        throw new Exception('Constantes de base de datos no definidas');
    }
    
    // Iniciar transacción
    if (!mysqli_query($conexion, "BEGIN")) {
        throw new Exception('No se pudo iniciar transacción: ' . mysqli_error($conexion));
    }
    
    $nueva = $_POST['tipoInsti'] ?? '';
    $fecha = date("Y-m-d");
    $fechaCompleta = date("Y-m-d H:i:s");
    
    if ($nueva == 0) {
        // ============================================
        // RENOVACIÓN - Ya funciona bien
        // ============================================
        throw new Exception('La renovación debe usar el otro endpoint');
        
    } else {
        // ============================================
        // NUEVA INSTITUCIÓN
        // ============================================
        
        $siglasBD = trim($_POST['siglasBD'] ?? '');
        $nombreInsti = trim($_POST['nombreInsti'] ?? '');
        $siglasInst = trim($_POST['siglasInst'] ?? '');
        $year = $_POST['yearN'] ?? date('Y');
        $bdInstitucion = BD_PREFIX.$siglasBD;
        
        // Validar datos requeridos
        if (empty($siglasBD) || empty($nombreInsti) || empty($siglasInst)) {
            throw new Exception('Datos básicos incompletos');
        }
        
        // PASO 1: CREAR LA INSTITUCIÓN
        $columnas = [
            'ins_nombre', 'ins_fecha_inicio', 'ins_telefono_principal', 'ins_contacto_principal',
            'ins_celular_contacto', 'ins_email_contacto', 'ins_enviroment', 'ins_estado',
            'ins_bd', 'ins_bloqueada', 'ins_years', 'ins_notificaciones_acudientes',
            'ins_siglas', 'ins_fecha_renovacion', 'ins_id_plan', 'ins_year_default'
        ];
        
        $valores = [
            mysqli_real_escape_string($conexion, $nombreInsti),
            $fechaCompleta,
            mysqli_real_escape_string($conexion, $_POST['celular'] ?? ''),
            mysqli_real_escape_string($conexion, $_POST['nombre1']." ".$_POST['nombre2']." ".$_POST['apellido1']." ".$_POST['apellido2']),
            mysqli_real_escape_string($conexion, $_POST['celular'] ?? ''),
            mysqli_real_escape_string($conexion, $_POST['email'] ?? ''),
            ENVIROMENT,
            1,
            mysqli_real_escape_string($conexion, $bdInstitucion),
            0,
            $year . "," . $year,
            0,
            mysqli_real_escape_string($conexion, $siglasInst),
            $fechaCompleta,
            1,
            $year
        ];
        
        $query = "INSERT INTO ".BD_ADMIN.".instituciones (" . implode(', ', $columnas) . ") 
                  VALUES ('" . implode("', '", $valores) . "')";
        
        if (!mysqli_query($conexion, $query)) {
            throw new Exception('Error al crear institución: ' . mysqli_error($conexion));
        }
        
        $idInsti = mysqli_insert_id($conexion);
        if (!$idInsti) {
            throw new Exception('No se pudo obtener el ID de la institución');
        }
        
        // PASO 2: ASIGNAR MÓDULOS (solo visibles, activos y con mod_default_install = 1)
        $consultaModulos = mysqli_query($conexion, "SELECT mod_id FROM ".BD_ADMIN.".modulos 
            WHERE mod_estado = 1 
            AND mod_visible = 1 
            AND mod_default_install = 1");
        if ($consultaModulos && mysqli_num_rows($consultaModulos) > 0) {
            $valoresModulos = [];
            while ($modulo = mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)) {
                $valoresModulos[] = "($idInsti, ".$modulo['mod_id'].")";
            }
            
            if (!empty($valoresModulos)) {
                $sqlModulos = "INSERT INTO ".BD_ADMIN.".instituciones_modulos (ipmod_institucion, ipmod_modulo) 
                               VALUES " . implode(',', $valoresModulos);
                
                if (!mysqli_query($conexion, $sqlModulos)) {
                    throw new Exception('Error al asignar módulos: ' . mysqli_error($conexion));
                }
            }
        }
        
        // PASO 3: CREAR CONFIGURACIÓN
        $sqlConfig = "INSERT INTO ".BD_ADMIN.".configuracion (
            conf_agno, conf_periodo, conf_nota_desde, conf_nota_hasta, conf_nota_minima_aprobar,
            conf_color_perdida, conf_color_ganada, conf_periodos_maximos, conf_notas_categoria,
            conf_id_institucion, conf_base_datos, conf_num_registros, conf_decimales_notas,
            conf_max_peso_archivos, conf_informe_parcial, conf_ver_observador, conf_ficha_estudiantil,
            conf_solicitar_acudiente_2, conf_mostrar_campos, conf_calificaciones_acudientes,
            conf_mostrar_calificaciones_estudiantes, conf_orden_nombre_estudiantes,
            conf_editar_definitivas_consolidado, conf_observaciones_multiples_comportamiento,
            conf_cambiar_nombre_usuario, conf_cambiar_clave_estudiantes, conf_permiso_descargar_boletin,
            conf_certificado, conf_firma_estudiante_informe_asistencia, conf_permiso_edicion_years_anteriores,
            conf_porcentaje_completo_generar_informe, conf_ver_promedios_sabanas_docentes,
            conf_forma_mostrar_notas, conf_mostrar_encabezado_informes, conf_mostrar_pasos_matricula,
            conf_reporte_sabanas_nota_indocador, conf_doble_buscador, conf_libro_final,
            conf_estampilla_certificados, conf_mostrar_estudiantes_cancelados, conf_formato_boletin,
            conf_promedio_libro_final, conf_ocultar_panel_lateral_notas_estudiantes,
            conf_firma_inasistencia_planilla_notas_doc, conf_puede_cambiar_grado_y_grupo,
            conf_color_borde, conf_color_encabezado, conf_tam_borde, conf_ancho_imagen, conf_alto_imagen,
            conf_mostrar_nombre, conf_permiso_eliminar_cargas, conf_agregar_porcentaje_asignaturas
        ) VALUES (
            '".$year."', 1, 1, 5, 3, '#e10000', '#0000d5', 4, 1, '".$idInsti."', 
            '".mysqli_real_escape_string($conexion, $bdInstitucion)."', 20, 2, '5', 0, 0, 0, 'NO', 
            1, 1, 1, 1, 0, 0, 'SI', 'SI', 1, 1, 1, 1, 1, 1, 'CUANTITATIVA', 1, 0, 0, '0', 1, 
            'NO', 'NO', 7, 'TODOS_PERIODOS', 0, 'SI', 0, '#000000', '#ff0080', 1, '200', '150', 1, 'NO', 'NO'
        )";
        
        if (!mysqli_query($conexion, $sqlConfig)) {
            throw new Exception('Error al crear configuración: ' . mysqli_error($conexion));
        }
        
        // PASO 4: INFORMACIÓN GENERAL
        $sqlInfo = "INSERT INTO ".BD_ADMIN.".general_informacion (
            info_rector, info_secretaria_academica, info_logo, info_nit, info_nombre,
            info_direccion, info_telefono, info_clase, info_caracter, info_calendario,
            info_jornada, info_horario, info_niveles, info_modalidad, info_propietario,
            info_coordinador_academico, info_tesorero, info_institucion, info_year
        ) VALUES (
            '2', '2', 'sintia-logo-2023.png', '0000000000-0', '".mysqli_real_escape_string($conexion, $nombreInsti)."',
            'Cra 00 # 00-00', '(000)000-0000', 'Privado', 'Mixto', 'A', 'Mañana',
            '6:00 am - 12:30 pm', 'Preescolar, Basica, Media', 'Academica', 
            'PROPIETARIO PRUEBA', '2', '2', '".$idInsti."', '".$year."'
        )";
        
        if (!mysqli_query($conexion, $sqlInfo)) {
            throw new Exception('Error al crear información general: ' . mysqli_error($conexion));
        }
        
        // PASO 5: CURSOS
        $sqlGrados = "INSERT INTO ".BD_ACADEMICA.".academico_grados(
            gra_id, gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, 
            gra_estado, institucion, year, gra_grado_siguiente, gra_vocal, gra_nivel, gra_grado_anterior, 
            gra_periodos, gra_tipo
        ) VALUES 
        ('1','0','PRIMERO',8,0,0,1,'".$idInsti."','".$year."','2',NULL,2,'15',4,'grupal'),
        ('2','0','SEGUNDO',8,0,0,1,'".$idInsti."','".$year."','3',NULL,2,'1',4,'grupal'),
        ('3','0','TERCERO',8,0,0,1,'".$idInsti."','".$year."','4',NULL,2,'2',4,'grupal'),
        ('4','0','CUARTO',8,0,0,1,'".$idInsti."','".$year."','5',NULL,2,'3',4,'grupal'),
        ('5','0','QUINTO',8,0,0,1,'".$idInsti."','".$year."','6',NULL,2,'4',4,'grupal'),
        ('6','0','SEXTO',8,0,0,1,'".$idInsti."','".$year."','7',NULL,3,'5',4,'grupal'),
        ('7','0','SEPTIMO',8,0,0,1,'".$idInsti."','".$year."','8',NULL,3,'6',4,'grupal'),
        ('8','0','OCTAVO',8,0,0,1,'".$idInsti."','".$year."','9',NULL,3,'7',4,'grupal'),
        ('9','0','NOVENO',8,0,0,1,'".$idInsti."','".$year."','10',NULL,3,'8',4,'grupal'),
        ('10','0','DECIMO',8,0,0,1,'".$idInsti."','".$year."','11',NULL,4,'9',4,'grupal'),
        ('11','0','UNDECIMO',8,0,0,1,'".$idInsti."','".$year."','0',NULL,4,'10',4,'grupal'),
        ('12','0','PARVULOS',8,0,0,1,'".$idInsti."','".$year."','13',NULL,1,'0',4,'grupal'),
        ('13','0','PREJARDIN',8,0,0,1,'".$idInsti."','".$year."','14',NULL,1,'12',4,'grupal'),
        ('14','0','JARDIN',8,0,0,1,'".$idInsti."','".$year."','15',NULL,1,'13',4,'grupal'),
        ('15','0','TRANSICION',8,0,0,1,'".$idInsti."','".$year."','1',NULL,1,'14',4,'grupal')";
        
        if (!mysqli_query($conexion, $sqlGrados)) {
            throw new Exception('Error al crear cursos: ' . mysqli_error($conexion));
        }
        
        // PASO 6: GRUPOS
        $sqlGrupos = "INSERT INTO ".BD_ACADEMICA.".academico_grupos(
            gru_id, gru_codigo, gru_nombre, institucion, year
        ) VALUES 
        ('1',1267,'A','".$idInsti."','".$year."'),
        ('2',1268,'B','".$idInsti."','".$year."'),
        ('3',1269,'C','".$idInsti."','".$year."'),
        ('4',1270,'Sin grupo','".$idInsti."','".$year."')";
        
        if (!mysqli_query($conexion, $sqlGrupos)) {
            throw new Exception('Error al crear grupos: ' . mysqli_error($conexion));
        }
        
        // PASO 7: CATEGORIAS DE NOTAS
        $sqlCategorias = "INSERT INTO ".BD_ACADEMICA.".academico_categorias_notas(
            catn_id, catn_nombre, institucion, year
        ) VALUES 
        ('1','Desempeños (Bajo a Superior)','".$idInsti."','".$year."'),
        ('2','Letras (D a E)','".$idInsti."','".$year."'),
        ('3','Numerica de 0 a 100','".$idInsti."','".$year."'),
        ('4','Caritas (Llorando - Contento)','".$idInsti."','".$year."')";
        
        if (!mysqli_query($conexion, $sqlCategorias)) {
            throw new Exception('Error al crear categorías de notas: ' . mysqli_error($conexion));
        }
        
        // PASO 8: TIPOS DE NOTAS
        $sqlTipos = "INSERT INTO ".BD_ACADEMICA.".academico_notas_tipos(
            notip_id, notip_nombre, notip_desde, notip_hasta, notip_categoria, notip_imagen, institucion, year
        ) VALUES 
        ('1','Bajo',1.00,3.49,'1','bajo.png','".$idInsti."','".$year."'),
        ('2','Basico',3.50,3.99,'1','bas.png','".$idInsti."','".$year."'),
        ('3','Alto',4.00,4.59,'1','alto.png','".$idInsti."','".$year."'),
        ('4','Superior',4.60,5.00,'1','sup.png','".$idInsti."','".$year."')";
        
        if (!mysqli_query($conexion, $sqlTipos)) {
            throw new Exception('Error al crear tipos de notas: ' . mysqli_error($conexion));
        }
        
        // PASO 9: AREAS
        $sqlAreas = "INSERT INTO ".BD_ACADEMICA.".academico_areas(
            ar_id, ar_nombre, ar_posicion, institucion, year
        ) VALUES ('1','AREA DE PRUEBA',1,'".$idInsti."','".$year."')";
        
        if (!mysqli_query($conexion, $sqlAreas)) {
            throw new Exception('Error al crear áreas: ' . mysqli_error($conexion));
        }
        
        // PASO 10: MATERIAS
        $sqlMaterias = "INSERT INTO ".BD_ACADEMICA.".academico_materias(
            mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area, institucion, year
        ) VALUES ('1','1','MATERIA DE PRUEBA','PRU','1','".$idInsti."','".$year."')";
        
        if (!mysqli_query($conexion, $sqlMaterias)) {
            throw new Exception('Error al crear materias: ' . mysqli_error($conexion));
        }
        
        // PASO 11: USUARIOS
        $clave = '12345678';
        $nombre1 = mysqli_real_escape_string($conexion, $_POST['nombre1'] ?? '');
        $nombre2 = mysqli_real_escape_string($conexion, $_POST['nombre2'] ?? '');
        $apellido1 = mysqli_real_escape_string($conexion, $_POST['apellido1'] ?? '');
        $apellido2 = mysqli_real_escape_string($conexion, $_POST['apellido2'] ?? '');
        $email = mysqli_real_escape_string($conexion, $_POST['email'] ?? '');
        $celular = mysqli_real_escape_string($conexion, $_POST['celular'] ?? '');
        $tipoDoc = mysqli_real_escape_string($conexion, $_POST['tipoDoc'] ?? '');
        $documento = mysqli_real_escape_string($conexion, $_POST['documento'] ?? '');
        $usuarioAcceso = mysqli_real_escape_string($conexion, $_POST['usuarioAcceso'] ?? $documento."-".$idInsti);
        $enviarCorreoBienvenida = ($_POST['enviarCorreoBienvenida'] ?? '0') === '1';
        
        $sqlUsuarios = "INSERT INTO ".BD_GENERAL.".usuarios(
            uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_nombre2, uss_apellido1, 
            uss_apellido2, uss_estado, uss_foto, uss_idioma, uss_tema, uss_ocupacion, uss_email, 
            uss_permiso1, uss_celular, uss_genero, uss_bloqueado, uss_tipo_documento, uss_documento, 
            institucion, year, uss_portada, uss_tema_sidebar, uss_tema_header, uss_tema_logo
        ) VALUES 
        ('1','sintia-".$idInsti."',SHA1('sintia2014$'),1,'ADMINISTRACIÓN',NULL,'SINTIA',NULL,
        0,'default.png',1,'orange','Administrador','soporte@plataformasintia.com',1298,
        '(313) 591-2073',126,0,NULL,NULL,'".$idInsti."','".$year."','default.png', 'white-sidebar-color', 'header-white', 'logo-white'),
        
        ('2','".$usuarioAcceso."',SHA1('".$clave."'),5,'".$nombre1."',
        '".$nombre2."','".$apellido1."','".$apellido2."',
        0,'default.png',1,'orange','DIRECTIVO','".$email."',1298,'".$celular."',
        126,0,'".$tipoDoc."','".$documento."','".$idInsti."','".$year."','default.png', 'white-sidebar-color', 'header-white', 'logo-white'),
        
        ('3','pruebaDC-".$idInsti."',SHA1('".$clave."'),2,'USUARIO',NULL,'DOCENTE',NULL,
        0,'default.png',1,'orange','DOCENTE',NULL,0,NULL,126,0,NULL,NULL,'".$idInsti."','".$year."','default.png', 'white-sidebar-color', 'header-white', 'logo-white'),
        
        ('4','pruebaAC-".$idInsti."',SHA1('".$clave."'),3,'USUARIO',NULL,'ACUDIENTE',NULL,
        0,'default.png',1,'orange','ACUDIENTE',NULL,0,NULL,126,0,NULL,NULL,'".$idInsti."','".$year."','default.png', 'white-sidebar-color', 'header-white', 'logo-white'),
        
        ('5','pruebaES-".$idInsti."',SHA1('".$clave."'),4,'USUARIO',NULL,'ESTUDIANTE',NULL,
        0,'default.png',1,'orange','ESTUDIANTE',NULL,0,NULL,126,0,NULL,NULL,'".$idInsti."','".$year."','default.png', 'white-sidebar-color', 'header-white', 'logo-white')";
        
        if (!mysqli_query($conexion, $sqlUsuarios)) {
            throw new Exception('Error al crear usuarios: ' . mysqli_error($conexion));
        }
        
        // PASO 12: MATRICULAS
        $sqlMatriculas = "INSERT INTO ".BD_ACADEMICA.".academico_matriculas(
            mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, 
            mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_lugar_nacimiento, 
            mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_religion, mat_direccion, 
            mat_barrio, mat_estrato, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, 
            mat_acudiente, mat_privilegio1, mat_privilegio2, mat_privilegio3, institucion, year
        ) VALUES (
            '1','00001','0000-00-00 00:00:00','PRUEBA','DE','ESTUDIANTE','1','1',126,'1993-10-21',
            '1',108,'0000000000','1',111,'Cra 00 #00-00','B. Prueba',116,129,1,'5',0,'4',0,'0',0,
            '".$idInsti."','".$year."'
        )";
        
        if (!mysqli_query($conexion, $sqlMatriculas)) {
            throw new Exception('Error al crear matrículas: ' . mysqli_error($conexion));
        }
        
        // PASO 13: USUARIOS POR ESTUDIANTES
        $sqlUPE = "INSERT INTO ".BD_GENERAL.".usuarios_por_estudiantes(
            upe_id, upe_id_usuario, upe_id_estudiante, upe_parentezco, institucion, year
        ) VALUES ('1','4','1','Padre','".$idInsti."','".$year."')";
        
        if (!mysqli_query($conexion, $sqlUPE)) {
            throw new Exception('Error al crear relación usuarios-estudiantes: ' . mysqli_error($conexion));
        }
        
        // PASO 14: CARGAS
        $sqlCargas = "INSERT INTO ".BD_ACADEMICA.".academico_cargas(
            car_id, car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, 
            car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable, 
            institucion, year, car_configuracion, car_valor_indicador, car_posicion_docente, 
            car_permiso2, car_maximos_indicadores, car_maximas_calificaciones, 
            car_indicador_automatico, car_estado
        ) VALUES (
            '1','3','1','1','1',1,1,1,'3',2,'0000-00-00 00:00:00',2,'".$idInsti."','".$year."',
            0,0,1,0,10,100,NULL,'SINTIA'
        )";
        
        if (!mysqli_query($conexion, $sqlCargas)) {
            throw new Exception('Error al crear cargas: ' . mysqli_error($conexion));
        }
        
        // PASO 15: Enviar correo de bienvenida (si está marcado)
        $mensajeCorreo = '';
        $correoExitoso = false;
        
        if ($enviarCorreoBienvenida) {
            try {
                $data = [
                    'institucion_id'   => $idInsti,
                    'institucion_agno' => $year,
                    'institucion_nombre' => $nombreInsti,
                    'usuario_id'       => '2',
                    'usuario_email'    => $email,
                    'usuario_nombre'   => trim($nombre1." ".$nombre2." ".$apellido1." ".$apellido2),
                    'usuario_usuario'  => $usuarioAcceso,
                    'usuario_clave'    => $clave,
                    'url_acceso'       => REDIRECT_ROUTE.'/index.php?inst='.base64_encode($idInsti).'&year='.base64_encode($year)
                ];
                $asunto = 'Bienvenido a la Plataforma SINTIA - Credenciales de Acceso';
                $bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-bienvenida.php';
                
                // EnviarEmail::enviar() retorna void, lanza excepción si falla
                EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
                
                // Si llegamos aquí, el correo se envió exitosamente (no hubo excepción)
                $mensajeCorreo = '✉️ Correo de bienvenida enviado exitosamente a '.$email;
                $correoExitoso = true;
                
            } catch(Exception $emailError) {
                // Email opcional - no detener el proceso si falla
                $mensajeCorreo = '⚠️ No se pudo enviar el correo de bienvenida. Comunica las credenciales manualmente.';
                $correoExitoso = false;
                
                // Log del error para debugging
                error_log("Error al enviar correo de bienvenida - Institución: ".$idInsti." - Email: ".$email." - Error: ".$emailError->getMessage());
            }
        }
        
        $finalResponse['institucionId'] = $idInsti;
        $finalResponse['usuario'] = $usuarioAcceso;
        $finalResponse['clave'] = $clave;
        $finalResponse['email'] = $email;
        $finalResponse['message'] = 'Institución creada exitosamente';
        $finalResponse['correoEnviado'] = $correoExitoso; // true solo si se envió sin errores
        $finalResponse['mensajeCorreo'] = $mensajeCorreo;
        $finalResponse['nota'] = 'IMPORTANTE: Guarda estas credenciales en un lugar seguro.';
    }
    
    // Confirmar transacción
    if (!mysqli_query($conexion, "COMMIT")) {
        throw new Exception('Error al confirmar transacción: ' . mysqli_error($conexion));
    }
    
    $finalResponse['success'] = true;
    
} catch(Exception $e) {
    // Revertir en caso de error
    if (isset($conexion)) {
        @mysqli_query($conexion, "ROLLBACK");
    }
    
    $finalResponse['success'] = false;
    $finalResponse['message'] = 'Error: ' . $e->getMessage();
    $finalResponse['error_details'] = [
        'code' => $e->getCode(),
        'line' => $e->getLine(),
        'file' => basename($e->getFile()),
        'trace' => explode("\n", $e->getTraceAsString())
    ];
}

// Limpiar TODO el buffer
while (ob_get_level()) {
    ob_end_clean();
}

// Enviar SOLO JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($finalResponse, JSON_PRETTY_PRINT);
exit;

