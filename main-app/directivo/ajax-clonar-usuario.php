<?php
/**
 * CLONAR USUARIO ENTRE INSTITUCIONES
 * Procesa la clonación de un usuario de una institución a otra
 */

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");

// Verificar que el usuario sea tipo DEV
if ($datosUsuarioActual['uss_tipo'] != TIPO_DEV) {
    echo json_encode([
        'success' => false,
        'message' => 'No tiene permisos para realizar esta acción'
    ]);
    exit();
}

// Validar CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || !Csrf::validarToken($csrfToken)) {
    echo json_encode([
        'success' => false,
        'message' => 'Token de seguridad inválido'
    ]);
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    // Obtener parámetros
    // uss_id es alfanumérico, no numérico
    $usuarioId = isset($_POST['usuario_id']) ? trim($_POST['usuario_id']) : '';
    $institucionDestino = isset($_POST['institucion']) ? (int)$_POST['institucion'] : 0;
    $yearDestino = isset($_POST['year']) ? (int)$_POST['year'] : 0;
    
    // Validar parámetros
    if (empty($usuarioId)) {
        $response['message'] = 'ID de usuario inválido';
        echo json_encode($response);
        exit();
    }
    
    if ($institucionDestino <= 0) {
        $response['message'] = 'Debe seleccionar una institución destino';
        echo json_encode($response);
        exit();
    }
    
    if ($yearDestino <= 0) {
        $response['message'] = 'Debe seleccionar un año destino';
        echo json_encode($response);
        exit();
    }
    
    // Primero, buscar el usuario original en la BD para obtener su institución y año
    // Buscar en todas las instituciones y años posibles
    // uss_id es alfanumérico, usar comillas en la consulta
    $usuarioIdEscaped = mysqli_real_escape_string($conexion, $usuarioId);
    $consultaUsuarioOriginal = mysqli_query($conexion, "
        SELECT uss_id, uss_usuario, institucion, year
        FROM " . BD_GENERAL . ".usuarios 
        WHERE uss_id = '{$usuarioIdEscaped}'
        LIMIT 1
    ");
    
    if (mysqli_num_rows($consultaUsuarioOriginal) == 0) {
        $response['message'] = 'Usuario no encontrado';
        echo json_encode($response);
        exit();
    }
    
    $datosUsuarioOriginal = mysqli_fetch_array($consultaUsuarioOriginal, MYSQLI_ASSOC);
    $institucionOrigen = (int)$datosUsuarioOriginal['institucion'];
    $yearOrigen = (int)$datosUsuarioOriginal['year'];
    $usuarioNombre = mysqli_real_escape_string($conexion, $datosUsuarioOriginal['uss_usuario']);
    
    // Verificar que la institución destino existe
    $consultaInst = mysqli_query($conexion, "
        SELECT ins_id, ins_nombre, ins_years 
        FROM " . $baseDatosServicios . ".instituciones 
        WHERE ins_id = {$institucionDestino} 
        AND ins_estado = 1 
        AND ins_enviroment = '" . ENVIROMENT . "'
    ");
    
    if (mysqli_num_rows($consultaInst) == 0) {
        $response['message'] = 'Institución destino no encontrada o inactiva';
        echo json_encode($response);
        exit();
    }
    
    $datosInstitucion = mysqli_fetch_array($consultaInst, MYSQLI_ASSOC);
    
    // Verificar que el año destino está en el rango de años de la institución
    $yearsRange = explode(',', $datosInstitucion['ins_years']);
    $yearStart = (int)$yearsRange[0];
    $yearEnd = (int)$yearsRange[1];
    
    if ($yearDestino < $yearStart || $yearDestino > $yearEnd) {
        $response['message'] = 'El año seleccionado no está disponible para esta institución';
        echo json_encode($response);
        exit();
    }
    
    // Verificar si el usuario ya existe en la institución y año destino
    $consultaUsuarioExistente = mysqli_query($conexion, "
        SELECT uss_id 
        FROM " . BD_GENERAL . ".usuarios 
        WHERE uss_usuario = '{$usuarioNombre}'
        AND institucion = {$institucionDestino}
        AND year = {$yearDestino}
    ");
    
    if (mysqli_num_rows($consultaUsuarioExistente) > 0) {
        $response['message'] = 'Ya existe un usuario con el mismo nombre de usuario en la institución y año seleccionados';
        echo json_encode($response);
        exit();
    }
    
    // Obtener todos los campos del usuario original directamente de la BD
    // Usar la institución y año del usuario original encontrado
    // uss_id es alfanumérico, usar comillas en la consulta
    $consultaUsuarioCompleto = mysqli_query($conexion, "
        SELECT * 
        FROM " . BD_GENERAL . ".usuarios 
        WHERE uss_id = '{$usuarioIdEscaped}'
        AND institucion = {$institucionOrigen}
        AND year = {$yearOrigen}
    ");
    
    if (mysqli_num_rows($consultaUsuarioCompleto) == 0) {
        $response['message'] = 'No se encontraron los datos completos del usuario en la institución y año originales';
        echo json_encode($response);
        exit();
    }
    
    $usuarioCompleto = mysqli_fetch_array($consultaUsuarioCompleto, MYSQLI_ASSOC);
    
    // Generar nuevo uss_id usando Utilidades::getNextIdSequence
    // Necesitamos conexión PDO para esta función
    if (!isset($conexionPDO)) {
        $conexionPDO = Conexion::newConnection('PDO');
    }
    $nuevoUssId = Utilidades::getNextIdSequence($conexionPDO, BD_GENERAL, 'usuarios');
    
    // Preparar datos para insertar - copiar todos los campos excepto uss_id, institucion, year
    // Actualizar campos específicos: institucion, year, fecha_registro, responsable_registro
    // Resetear: ultimo_ingreso, ultima_salida, intentos_fallidos
    $campos = [];
    $valores = [];
    
    // Lista de campos que son fechas/datetime y deben ir entre comillas
    $camposFecha = ['uss_fecha_nacimiento', 'uss_fecha_registro', 'uss_ultimo_ingreso', 'uss_ultima_salida', 'uss_ultima_actualizacion'];
    
    // Lista de campos que son numéricos y no deben ir entre comillas
    $camposNumericos = ['uss_tipo', 'uss_estado', 'uss_idioma', 'uss_permiso1', 'uss_genero', 
                        'uss_bloqueado', 'uss_responsable_registro', 'uss_estado_civil', 'uss_preguntar_animo',
                        'uss_mostrar_mensajes', 'uss_profesion', 'uss_estado_laboral', 'uss_nivel_academico',
                        'uss_tiene_hijos', 'uss_numero_hijos', 'uss_lugar_nacimiento', 'uss_tipo_negocio',
                        'uss_estrato', 'uss_tipo_vivienda', 'uss_medio_transporte', 'uss_notificacion',
                        'uss_mostrar_edad', 'uss_version1_menu', 'uss_solicitar_datos', 'uss_institucion_municipio',
                        'uss_intentos_fallidos', 'uss_tipo_documento'];
    
    // Agregar el nuevo uss_id primero
    $campos[] = 'uss_id';
    $valores[] = "'" . mysqli_real_escape_string($conexion, $nuevoUssId) . "'";
    
    foreach ($usuarioCompleto as $campo => $valor) {
        // Saltar campos que no deben copiarse o que se actualizarán
        if ($campo == 'uss_id' || $campo == 'id_nuevo') {
            continue; // uss_id ya agregamos el nuevo, id_nuevo se genera automáticamente
        }
        
        if ($campo == 'institucion') {
            $campos[] = 'institucion';
            $valores[] = $institucionDestino;
        } elseif ($campo == 'year') {
            $campos[] = 'year';
            $valores[] = $yearDestino;
        } elseif ($campo == 'uss_fecha_registro') {
            $campos[] = 'uss_fecha_registro';
            $valores[] = "'" . date("Y-m-d H:i:s") . "'"; // Nueva fecha de registro con comillas
        } elseif ($campo == 'uss_responsable_registro') {
            $campos[] = 'uss_responsable_registro';
            $valores[] = (int)$_SESSION["id"]; // Número sin comillas
        } elseif ($campo == 'uss_ultimo_ingreso' || $campo == 'uss_ultima_salida') {
            $campos[] = $campo;
            $valores[] = 'NULL'; // NULL sin comillas
        } elseif ($campo == 'uss_intentos_fallidos') {
            $campos[] = $campo;
            $valores[] = 0; // Número sin comillas
        } elseif ($campo == 'uss_bloqueado') {
            $campos[] = $campo;
            $valores[] = 0; // Siempre establecer en 0 para usuarios nuevos
        } else {
            $campos[] = $campo;
            
            // Manejar valores NULL
            if ($valor === null || $valor === '' || $valor === '0000-00-00' || $valor === '0000-00-00 00:00:00') {
                $valores[] = 'NULL';
            } elseif (in_array($campo, $camposFecha)) {
                // Es un campo de fecha, siempre con comillas
                $valores[] = "'" . mysqli_real_escape_string($conexion, $valor) . "'";
            } elseif (in_array($campo, $camposNumericos)) {
                // Es un campo numérico, sin comillas
                $valores[] = (int)$valor;
            } else {
                // Es un string, con comillas
                $valores[] = "'" . mysqli_real_escape_string($conexion, $valor) . "'";
            }
        }
    }
    
    // Construir query de inserción
    $camposStr = implode(', ', $campos);
    $valoresStr = implode(', ', $valores);
    
    $sqlInsert = "INSERT INTO " . BD_GENERAL . ".usuarios ({$camposStr}) VALUES ({$valoresStr})";
    
    // Ejecutar inserción
    if (mysqli_query($conexion, $sqlInsert)) {
        // Usar el uss_id generado, no mysqli_insert_id (que no funciona con uss_id alfanumérico)
        $nuevoUsuarioId = $nuevoUssId;
        
        // Guardar historial de acciones (solo guardar, sin validaciones de permisos)
        // Definir idPaginaInterna para el historial
        $idPaginaInterna = 'DT0126'; // Mismo ID que usuarios.php
        
        // Capturar output del historial para que no interfiera con el JSON
        $error_reporting_original = error_reporting();
        error_reporting(0);
        ob_start();
        @include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
        ob_end_clean();
        error_reporting($error_reporting_original);
        
        $response['success'] = true;
        $response['message'] = 'Usuario clonado exitosamente';
        $response['nuevo_usuario_id'] = $nuevoUsuarioId;
        $response['institucion_destino'] = $datosInstitucion['ins_nombre'];
        $response['year_destino'] = $yearDestino;
    } else {
        $response['message'] = 'Error al clonar el usuario: ' . mysqli_error($conexion);
    }
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
