<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

header('Content-Type: application/json');

try {
    // Recibir ID de la inscripción
    $idMatricula = isset($_POST['id']) ? $_POST['id'] : '';
    
    if (empty($idMatricula)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de matrícula no proporcionado'
        ]);
        exit();
    }
    
    // Actualizar el campo asp_oculto a 0 (visible) usando mysqli directamente
    $sql = "UPDATE ".BD_ADMISIONES.".aspirantes 
            SET asp_oculto = 0 
            WHERE asp_id = ? 
            AND asp_institucion = ? 
            AND asp_agno = ?";
    
    // Preparar la consulta
    $stmt = mysqli_prepare($conexion, $sql);
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . mysqli_error($conexion));
    }
    
    // Vincular parámetros
    mysqli_stmt_bind_param($stmt, 'iii', $idMatricula, $config['conf_id_institucion'], $_SESSION["bd"]);
    
    // Ejecutar
    $ejecutado = mysqli_stmt_execute($stmt);
    
    if (!$ejecutado) {
        throw new Exception('Error al ejecutar la consulta: ' . mysqli_stmt_error($stmt));
    }
    
    // Verificar filas afectadas
    $filasAfectadas = mysqli_stmt_affected_rows($stmt);
    
    mysqli_stmt_close($stmt);
    
    if ($filasAfectadas > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Inscripción restaurada correctamente',
            'affected_rows' => $filasAfectadas
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró la inscripción o ya estaba visible',
            'affected_rows' => 0
        ]);
    }
    
} catch (Exception $e) {
    error_log('Error en ajax-desocultar-inscripcion.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>

