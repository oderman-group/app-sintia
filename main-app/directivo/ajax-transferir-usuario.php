<?php
/**
 * TRANSFERIR USUARIO ENTRE INSTITUCIONES
 * Procesa la transferencia (mover) de un usuario de una institución a otra
 */

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

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
    
    // Buscar el usuario original en la BD para obtener su institución y año actuales
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
    
    // Verificar que no se esté transfiriendo a la misma institución y año
    if ($institucionOrigen == $institucionDestino && $yearOrigen == $yearDestino) {
        $response['message'] = 'El usuario ya está en la institución y año seleccionados';
        echo json_encode($response);
        exit();
    }
    
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
        AND uss_id != '{$usuarioIdEscaped}'
    ");
    
    if (mysqli_num_rows($consultaUsuarioExistente) > 0) {
        $response['message'] = 'Ya existe un usuario con el mismo nombre de usuario en la institución y año seleccionados';
        echo json_encode($response);
        exit();
    }
    
    // Actualizar el usuario: cambiar institución y year, actualizar fecha_registro y responsable_registro
    // Resetear campos de sesión: ultimo_ingreso, ultima_salida, intentos_fallidos
    $fechaActual = date("Y-m-d H:i:s");
    $responsableRegistro = (int)$_SESSION["id"];
    
    $sqlUpdate = "UPDATE " . BD_GENERAL . ".usuarios SET 
        institucion = {$institucionDestino},
        year = {$yearDestino},
        uss_fecha_registro = '{$fechaActual}',
        uss_responsable_registro = {$responsableRegistro},
        uss_ultimo_ingreso = NULL,
        uss_ultima_salida = NULL,
        uss_intentos_fallidos = 0
        WHERE uss_id = '{$usuarioIdEscaped}'
        AND institucion = {$institucionOrigen}
        AND year = {$yearOrigen}";
    
    // Ejecutar actualización
    if (mysqli_query($conexion, $sqlUpdate)) {
        $filasAfectadas = mysqli_affected_rows($conexion);
        
        if ($filasAfectadas > 0) {
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
            $response['message'] = 'Usuario transferido exitosamente';
            $response['usuario_id'] = $usuarioId;
            $response['institucion_origen'] = $institucionOrigen;
            $response['year_origen'] = $yearOrigen;
            $response['institucion_destino'] = $datosInstitucion['ins_nombre'];
            $response['year_destino'] = $yearDestino;
        } else {
            $response['message'] = 'No se pudo transferir el usuario. Verifique que el usuario existe en la institución y año de origen.';
        }
    } else {
        $response['message'] = 'Error al transferir el usuario: ' . mysqli_error($conexion);
    }
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
