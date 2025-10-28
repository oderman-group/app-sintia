<?php
session_start();
include("../../config-general/config.php");
require_once '../class/Tables/BDT_clases_feedback.php';
require_once(ROOT_PATH."/main-app/class/Plataforma.php");

$tableName = BDT_ClasesFeedback::getTableName();

$respuesta = [
    'titulo'  => 'Error',
    'estado'  => 'error',
    'mensaje' => 'Error desconocido'
];

// Debug: Log de todos los POST recibidos
error_log('POST recibido: ' . print_r($_POST, true));

// Validar que los parámetros requeridos estén presentes
if (empty($_POST["claseId"]) || empty($_POST["usuarioActual"]) || empty($_POST["star"])) {
    $respuesta = [
        'titulo'  => 'Error',
        'estado'  => 'error',
        'mensaje' => 'Parámetros incompletos. Clase: '.(!empty($_POST["claseId"]) ? 'OK' : 'FALTA').', Usuario: '.(!empty($_POST["usuarioActual"]) ? 'OK' : 'FALTA').', Estrella: '.(!empty($_POST["star"]) ? 'OK' : 'FALTA').' | POST completo: '.json_encode($_POST)
    ];
    echo json_encode($respuesta);
    exit();
}

try {
    // claseId es un string (ej: 'CLA41USU311'), no un int
    $claseId = isset($_POST["claseId"]) ? trim($_POST["claseId"]) : '';
    // usuarioActual puede ser string o int dependiendo del formato
    $usuarioActual = isset($_POST["usuarioActual"]) ? trim($_POST["usuarioActual"]) : '';
    
    // Asegurar que el comentario se reciba correctamente como string
    // IMPORTANTE: No hacer intval ni conversiones numéricas en el comentario
    $comment = '';
    if (isset($_POST["comment"])) {
        // Obtener el comentario directamente - NO hacer trim todavía para preservar espacios
        $comment = $_POST["comment"];
        // Verificar el tipo antes de procesar
        error_log('🔍 Comment RAW tipo: ' . gettype($comment) . ', valor: [' . var_export($comment, true) . ']');
        
        // Convertir explícitamente a string usando concatenación para forzar tipo
        $comment = '' . $comment;
        // Ahora sí hacer trim
        $comment = trim($comment);
    }
    
    $star = isset($_POST["star"]) ? intval($_POST["star"]) : 0;
    
    error_log('📊 Parámetros recibidos RAW: ' . print_r($_POST, true));
    error_log('📊 Parámetros procesados:');
    error_log('   - claseId: [' . $claseId . '] tipo: ' . gettype($claseId));
    error_log('   - usuarioActual: [' . $usuarioActual . '] tipo: ' . gettype($usuarioActual));
    error_log('   - star: [' . $star . '] tipo: ' . gettype($star));
    error_log('   - comment: [' . substr($comment, 0, 100) . '] tipo: ' . gettype($comment) . ' longitud: ' . strlen($comment));
    
    // Validar que la estrella esté en el rango válido (1-5)
    if ($star < 1 || $star > 5) {
        throw new Exception("La valoración debe estar entre 1 y 5 estrellas. Valor recibido: ".$star);
    }
    
    // Validar que claseId no esté vacío
    if (empty($claseId)) {
        throw new Exception("El ID de la clase no puede estar vacío.");
    }
    
    // Validar que usuarioActual no esté vacío
    if (empty($usuarioActual)) {
        throw new Exception("El ID del usuario no puede estar vacío.");
    }
    
    // Preparar la consulta SQL usando mysqli_real_escape_string para el comentario (más seguro)
    // Nota: fcls_id_clase es VARCHAR(45), fcls_usuario es VARCHAR(45)
    $commentEscaped = mysqli_real_escape_string($conexion, $comment);
    
    error_log('🔧 Valores a insertar:');
    error_log('   - claseId: [' . $claseId . ']');
    error_log('   - institucion: [' . $config['conf_id_institucion'] . ']');
    error_log('   - usuarioActual: [' . $usuarioActual . ']');
    error_log('   - comment: [' . substr($comment, 0, 50) . '] longitud: ' . strlen($comment));
    error_log('   - commentEscaped: [' . substr($commentEscaped, 0, 50) . '] longitud: ' . strlen($commentEscaped));
    error_log('   - star: [' . $star . ']');
    
    $sql = "INSERT INTO ".BD_ACADEMICA.".{$tableName}(fcls_id_clase, fcls_id_institucion, fcls_usuario, fcls_comentario, fcls_star) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
	fcls_comentario = VALUES(fcls_comentario),
	fcls_star = VALUES(fcls_star),
	fcls_fecha_actualizacion = CURRENT_TIMESTAMP";
    
    // Preparar la sentencia
    $stmt = mysqli_prepare($conexion, $sql);

    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
    }

    // Vincular los parámetros según tipos (orden de las columnas en la INSERT):
    // 1. s: claseId (VARCHAR) -> $claseId
    // 2. i: institucion (INT) -> $config['conf_id_institucion']
    // 3. s: usuarioActual (VARCHAR) -> $usuarioActual
    // 4. s: comment (LONGTEXT) - DEBE SER STRING -> $commentBind
    // 5. i: star (INT) -> $star
    // FORMATO: "s i s s i" = "sissi" (NO "sisis" que estaba mal)
    
    // Asegurar que el comentario sea string antes de bind
    $commentBind = (string) $commentEscaped;
    
    // Verificar que commentBind es string y no está vacío de manera sospechosa
    if ($comment !== '' && $commentBind === '') {
        error_log('⚠️ ADVERTENCIA: El comentario se perdió durante el escape!');
        error_log('   Original: [' . $comment . ']');
        error_log('   Después de escape: [' . $commentBind . ']');
    }
    
    // CORRECCIÓN: El formato correcto es "sissi" no "sisis"
    // s = claseId, i = institucion, s = usuarioActual, s = comment, i = star
    mysqli_stmt_bind_param($stmt, "sissi", $claseId, $config['conf_id_institucion'], $usuarioActual, $commentBind, $star);
    
    error_log('✅ Query bind_param ejecutado con formato "sissi". Tipo de commentBind: ' . gettype($commentBind) . ', valor: [' . substr($commentBind, 0, 50) . ']');

    // Ejecutar la consulta
    $resultado = mysqli_stmt_execute($stmt);

    if (!$resultado) {
        error_log('❌ Error en execute: ' . mysqli_stmt_error($stmt));
        error_log('❌ Error en conexión: ' . mysqli_error($conexion));
        throw new Exception("Error al ejecutar la consulta: " . mysqli_stmt_error($stmt));
    }
    
    // Verificar qué se insertó realmente
    $affectedRows = mysqli_stmt_affected_rows($stmt);
    error_log('✅ Registros afectados: ' . $affectedRows);
    
    // Cerrar el statement antes de verificar
    mysqli_stmt_close($stmt);
    
    // Verificar qué se guardó realmente en la BD
    $verificarQuery = "SELECT fcls_comentario, fcls_star FROM ".BD_ACADEMICA.".{$tableName} 
        WHERE fcls_id_clase = ? AND fcls_id_institucion = ? AND fcls_usuario = ? 
        LIMIT 1";
    $stmtVerificar = mysqli_prepare($conexion, $verificarQuery);
    mysqli_stmt_bind_param($stmtVerificar, "sis", $claseId, $config['conf_id_institucion'], $usuarioActual);
    mysqli_stmt_execute($stmtVerificar);
    $resultadoVerificar = mysqli_stmt_get_result($stmtVerificar);
    $registroGuardado = mysqli_fetch_assoc($resultadoVerificar);
    mysqli_stmt_close($stmtVerificar);
    
    if ($registroGuardado) {
        error_log('🔍 Verificación en BD:');
        error_log('   - fcls_comentario guardado: [' . $registroGuardado['fcls_comentario'] . '] longitud: ' . strlen($registroGuardado['fcls_comentario']));
        error_log('   - fcls_star guardado: [' . $registroGuardado['fcls_star'] . ']');
        error_log('   - Comentario esperado: [' . $comment . ']');
        
        if ($registroGuardado['fcls_comentario'] !== $comment && $registroGuardado['fcls_comentario'] == '0') {
            error_log('⚠️ PROBLEMA DETECTADO: El comentario se guardó como "0" en lugar del texto original!');
        }
    }

    // La inserción se realizó con éxito
    $respuesta = [
        'titulo'  => 'Excelente',
        'estado'  => 'success',
        'mensaje' => 'El feedback fue guardado correctamente.'
    ];

    echo json_encode($respuesta);

} catch (Exception $e) {
    // Manejar errores
    $logError = Plataforma::soloRegistroErrores($e);
    //echo $logError;
    $respuesta = [
        'titulo'  => 'Error',
        'estado'  => 'error',
        'mensaje' => 'Ha ocurrido un error mientras se intenta guardar el feedback. <br> Código del registro de error: <b>'.$logError.'</b>'
    ];

    echo json_encode($respuesta);

}
