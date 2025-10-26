<?php
/**
 * CREAR INSTITUCIÓN Y USUARIO - ENVIAR CÓDIGO
 * Este endpoint crea la institución y el usuario TEMPORAL
 * y envía un código de verificación
 */

header('Content-Type: application/json; charset=UTF-8');
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Notificacion.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

$notificacion = new Notificacion();
$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);

if (!$conexion) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]);
    exit();
}

mysqli_set_charset($conexion, "utf8mb4");

// Crear conexión PDO para Utilidades::getNextIdSequence
try {
    $conexionPDO = new PDO(
        "mysql:host={$servidorConexion};dbname={$baseDatosServicios};charset=utf8mb4",
        $usuarioConexion,
        $claveConexion,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión PDO: ' . $e->getMessage()
    ]);
    exit();
}

$fecha = date("Y-m-d");
$fechaCompleta = date("Y-m-d H:i:s");
$year = date("Y");

// Obtener datos del formulario
$nombre = isset($_POST['nombre']) ? mysqli_real_escape_string($conexion, trim($_POST['nombre'])) : '';
$apellidos = isset($_POST['apellidos']) ? mysqli_real_escape_string($conexion, trim($_POST['apellidos'])) : '';
$email = isset($_POST['email']) ? mysqli_real_escape_string($conexion, trim($_POST['email'])) : '';
$celular = isset($_POST['celular']) ? mysqli_real_escape_string($conexion, trim($_POST['celular'])) : '';
$nombreIns = isset($_POST['nombreIns']) ? mysqli_real_escape_string($conexion, trim($_POST['nombreIns'])) : '';
$siglasInst = isset($_POST['siglasInst']) ? mysqli_real_escape_string($conexion, trim($_POST['siglasInst'])) : strtoupper(substr($nombreIns, 0, 4));
$ciudad = isset($_POST['ciudad']) ? mysqli_real_escape_string($conexion, trim($_POST['ciudad'])) : '';
$cargo = isset($_POST['cargo']) ? mysqli_real_escape_string($conexion, trim($_POST['cargo'])) : '';
$modulosSeleccionados = isset($_POST['modulos']) ? $_POST['modulos'] : [];

// Validar datos mínimos
if (empty($nombre) || empty($email) || empty($nombreIns)) {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos requeridos'
    ]);
    exit();
}

$bdInstitucion = BD_PREFIX . $siglasInst;

try {
    mysqli_query($conexion, "BEGIN");
    
    // CREAR LA INSTITUCIÓN
    $dataToInsert = [
        'ins_nombre' => $nombreIns,
        'ins_fecha_inicio' => $fechaCompleta,
        'ins_telefono_principal' => $celular,
        'ins_contacto_principal' => $nombre . " " . $apellidos,
        'ins_cargo_contacto' => $cargo,
        'ins_celular_contacto' => $celular,
        'ins_email_contacto' => $email,
        'ins_enviroment' => ENVIROMENT,
        'ins_estado' => 1,
        'ins_bd' => $bdInstitucion,
        'ins_bloqueada' => 0,
        'ins_years' => $year . "," . $year,
        'ins_notificaciones_acudientes' => 0,
        'ins_siglas' => $siglasInst,
        'ins_id_plan' => 1,
        'ins_year_default' => $year,
        'ins_tipo' => SCHOOL
    ];
    
    $query = "INSERT INTO " . BD_ADMIN . ".instituciones (";
    $columns = array_keys($dataToInsert);
    $values = array_values($dataToInsert);
    $query .= implode(', ', $columns);
    $query .= ") VALUES (";
    $query .= "'" . implode("', '", $values) . "'";
    $query .= ")";
    
    mysqli_query($conexion, $query);
    $idInsti = mysqli_insert_id($conexion);
    
    // CREAR REGISTRO EN general_informacion
    $queryGeneralInfo = "INSERT INTO " . BD_ADMIN . ".general_informacion (
        info_institucion, 
        info_year,
        info_nombre,
        info_telefono,
        info_ciudad,
        info_nit,
        info_dane,
        info_direccion,
        info_clase,
        info_caracter,
        info_calendario,
        info_jornada,
        info_horario,
        info_niveles,
        info_modalidad,
        info_propietario,
        info_logo
    ) VALUES (
        '{$idInsti}',
        '{$year}',
        '{$nombreIns}',
        '{$celular}',
        " . (!empty($ciudad) ? "'{$ciudad}'" : "NULL") . ",
        '',
        '',
        '',
        'Privado',
        'Mixto',
        'A',
        'Completa',
        '7:00 AM - 2:00 PM',
        'Preescolar, Básica, Media',
        'Académico',
        '{$nombre} {$apellidos}',
        'sintia-logo-2023.png'
    )";
    
    mysqli_query($conexion, $queryGeneralInfo);
    error_log("Registro creado en general_informacion para institución ID: " . $idInsti);
    
    // CREAR REGISTRO EN configuracion
    $queryConfiguracion = "INSERT INTO " . BD_ADMIN . ".configuracion (
        conf_id_institucion,
        conf_agno,
        conf_periodo,
        conf_nota_desde,
        conf_nota_hasta,
        conf_nota_minima_aprobar,
        conf_color_perdida,
        conf_color_ganada,
        conf_periodos_maximos,
        conf_num_registros,
        conf_decimales_notas,
        conf_agregar_porcentaje_asignaturas,
        conf_notas_categoria,
        conf_ancho_imagen,
        conf_alto_imagen,
        conf_mostrar_nombre,
        conf_max_peso_archivos,
        conf_informe_parcial,
        conf_calificaciones_acudientes,
        conf_mostrar_calificaciones_estudiantes,
        conf_orden_nombre_estudiantes,
        conf_editar_definitivas_consolidado,
        conf_cambiar_nombre_usuario,
        conf_cambiar_clave_estudiantes,
        conf_permiso_descargar_boletin,
        conf_certificado,
        conf_firma_estudiante_informe_asistencia,
        conf_permiso_edicion_years_anteriores,
        conf_porcentaje_completo_generar_informe,
        conf_ver_promedios_sabanas_docentes,
        conf_forma_mostrar_notas,
        conf_mostrar_encabezado_informes,
        conf_mostrar_pasos_matricula,
        conf_reporte_sabanas_nota_indocador,
        conf_doble_buscador,
        conf_libro_final,
        conf_estampilla_certificados,
        conf_mostrar_estudiantes_cancelados,
        conf_formato_boletin,
        conf_promedio_libro_final,
        conf_ocultar_panel_lateral_notas_estudiantes,
        conf_firma_inasistencia_planilla_notas_doc,
        conf_permiso_eliminar_cargas,
        conf_observaciones_multiples_comportamiento,
        conf_activar_encuesta,
        conf_base_datos,
        conf_puede_cambiar_grado_y_grupo
    ) VALUES (
        '{$idInsti}',
        '{$year}',
        1,
        1.0,
        5.0,
        3.0,
        '#ff0000',
        '#00aa00',
        4,
        20,
        2,
        'NO',
        1,
        150,
        150,
        1,
        5,
        0,
        1,
        1,
        1,
        0,
        'NO',
        'SI',
        0,
        1,
        1,
        0,
        1,
        1,
        'CUANTITATIVA',
        1,
        0,
        0,
        'NO',
        1,
        'NO',
        'NO',
        7,
        'TODOS_PERIODOS',
        0,
        'SI',
        'NO',
        0,
        0,
        '{$bdInstitucion}',
        0
    )";
    
    mysqli_query($conexion, $queryConfiguracion);
    error_log("Registro creado en configuracion para institución ID: " . $idInsti . " año: " . $year);
    
    // CREAR USUARIO DIRECTIVO
    $ussID = Utilidades::getNextIdSequence($conexionPDO, BD_GENERAL, 'usuarios');
    $usuarioLogin = 'directivo-' . $idInsti;
    
    // Log de debug
    error_log("uss_id generado: " . $ussID);
    error_log("Usuario login: " . $usuarioLogin);

    $queryUsuario = "INSERT INTO " . BD_GENERAL . ".usuarios(
        uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_apellido1, uss_estado, 
        uss_foto, uss_portada, uss_idioma, uss_tema, uss_email, uss_celular, 
        uss_genero, uss_bloqueado, institucion, year
    ) VALUES (
        '{$ussID}',
        '{$usuarioLogin}',
        SHA1('12345678'),
        5,
        '{$nombre}',
        '{$apellidos}',
        0,
        'default.png',
        'default.png',
        1,
        'orange',
        '{$email}',
        '{$celular}',
        126,
        0,
        '{$idInsti}',
        '{$year}'
    )";
    
    error_log("Query usuario: " . $queryUsuario);
    
    $resultUsuario = mysqli_query($conexion, $queryUsuario);
    
    if (!$resultUsuario) {
        throw new Exception("Error al crear usuario: " . mysqli_error($conexion));
    }
    
    // Como estamos usando $ussID generado manualmente, NO necesitamos mysqli_insert_id
    // $idUsuario es igual a $ussID
    $idUsuario = $ussID;
    
    error_log("Usuario creado con uss_id: " . $ussID);
    
    // GUARDAR MÓDULOS SELECCIONADOS
    if (!empty($modulosSeleccionados)) {
        $modulosInsertar = "";
        foreach ($modulosSeleccionados as $idModulo) {
            $idModuloSafe = mysqli_real_escape_string($conexion, $idModulo);
            $modulosInsertar .= "({$idInsti},{$idModuloSafe}),";
        }
        $modulosInsertar = substr($modulosInsertar, 0, -1);
        mysqli_query($conexion, "INSERT INTO " . BD_ADMIN . ".instituciones_modulos (ipmod_institucion,ipmod_modulo) VALUES {$modulosInsertar}");
    }
    
    mysqli_query($conexion, "COMMIT");
    
    // ENVIAR CÓDIGO DE VERIFICACIÓN
    $data = [
        'usuario_nombre' => $nombre . ' ' . $apellidos,
        'institucion_id' => $idInsti,
        'usuario_id' => $ussID,
        'year' => $year,
        'asunto' => 'Código de Verificación: ',  // El código se agrega automáticamente al asunto
        'body_template_route' => ROOT_PATH . '/config-general/template-email-codigo-verificacion.php',
        'usuario_email' => $email,
        'telefono' => $celular,
        'datos_codigo' => [],
    ];
    
    $canal = Notificacion::CANAL_EMAIL;
    $datosCodigo = $notificacion->enviarCodigoNotificacion($data, $canal, Notificacion::PROCESO_ACTIVAR_CUENTA);
    
    // Debug log
    error_log("Código generado y enviado: " . $datosCodigo['codigo']);
    
    // Log de confirmación
    error_log("========================================");
    error_log("CUENTA CREADA - DATOS DEL REGISTRO:");
    error_log("========================================");
    error_log("Nombre ingresado: " . $nombre . " " . $apellidos);
    error_log("Email ingresado: " . $email);
    error_log("Institución ingresada: " . $nombreIns);
    error_log("Ciudad ingresada: " . $ciudad);
    error_log("Cargo ingresado: " . $cargo);
    error_log("----------------------------------------");
    error_log("Institución creada ID: " . $idInsti);
    error_log("Usuario uss_id generado: " . $ussID);
    error_log("Usuario login: " . $usuarioLogin);
    error_log("Código enviado: " . $datosCodigo['codigo']);
    error_log("ID Registro código: " . $datosCodigo['idRegistro']);
    error_log("========================================");
    
    $respuesta = [
        'success' => true,
        'message' => 'Código enviado exitosamente',
        'usuarioEmail' => $email,
        'usuarioNombre' => $nombre . ' ' . $apellidos,
        'institucionId' => $idInsti,
        'institucionNombre' => $nombreIns,  // Agregado para debug
        'usuarioId' => $ussID,               // uss_id generado desde PHP (IMPORTANTE)
        'year' => $year,
        'telefono' => $celular,
        'datosCodigo' => $datosCodigo
    ];
    
    error_log("Respuesta JSON que se enviará:");
    error_log(json_encode($respuesta, JSON_PRETTY_PRINT));
    
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    mysqli_query($conexion, "ROLLBACK");
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear la cuenta: ' . $e->getMessage()
    ]);
}

exit();

