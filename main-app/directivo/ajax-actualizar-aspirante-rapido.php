<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Inscripciones.php");

function jsonResponse($data) {
    while (ob_get_level()) { ob_end_clean(); }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar que la conexión esté disponible
        if (!isset($conexionPDO)) {
            jsonResponse(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
        }
        
        // Obtener datos del POST
        $aspId = $_POST['asp_id'] ?? null;
        $matId = $_POST['mat_id'] ?? null;
        $estadoSolicitud = $_POST['estado_solicitud'] ?? null;
        $observacion = trim($_POST['observacion'] ?? '');
        $enviarCorreo = $_POST['enviar_correo'] ?? '2';
        
        // Validar campos obligatorios
        if (empty($aspId) || empty($matId) || empty($estadoSolicitud)) {
            jsonResponse(['success' => false, 'message' => 'ID de aspirante, matrícula y estado son obligatorios.']);
        }
        
        // Actualizar el estado y observación del aspirante
        $sqlAsp = "UPDATE ".BD_ADMISIONES.".aspirantes SET 
                    asp_estado_solicitud = :estado,
                    asp_observacion = :observacion
                   WHERE asp_id = :asp_id";
        
        $stmtAsp = $conexionPDO->prepare($sqlAsp);
        $stmtAsp->bindParam(':estado', $estadoSolicitud, PDO::PARAM_INT);
        $stmtAsp->bindParam(':observacion', $observacion, PDO::PARAM_STR);
        $stmtAsp->bindParam(':asp_id', $aspId, PDO::PARAM_INT);
        
        $resultadoAsp = $stmtAsp->execute();
        
        if ($resultadoAsp === false) {
            jsonResponse(['success' => false, 'message' => 'No se pudo actualizar el aspirante.']);
        }
        
        // Si se solicita enviar correo
        if ($enviarCorreo == '1') {
            // Obtener datos para el correo
            $sqlCorreo = "SELECT 
                            mat.mat_nombres, mat.mat_primer_apellido, mat.mat_segundo_apellido,
                            asp.asp_nombre_acudiente, asp.asp_email_acudiente,
                            asp.asp_estado_solicitud, asp.asp_observacion
                          FROM ".BD_ACADEMICA.".academico_matriculas mat
                          INNER JOIN ".BD_ADMISIONES.".aspirantes asp ON asp.asp_id = mat.mat_solicitud_inscripcion
                          WHERE asp.asp_id = :asp_id 
                          AND mat.mat_id = :mat_id
                          AND mat.institucion = :institucion
                          AND mat.year = :year";
            
            $stmtCorreo = $conexionPDO->prepare($sqlCorreo);
            $stmtCorreo->bindParam(':asp_id', $aspId, PDO::PARAM_INT);
            $stmtCorreo->bindParam(':mat_id', $matId, PDO::PARAM_STR);
            $stmtCorreo->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtCorreo->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
            $stmtCorreo->execute();
            $datosCorreo = $stmtCorreo->fetch(PDO::FETCH_ASSOC);
            
            // TODO: Implementar envío de correo
            // Por ahora solo registramos que se solicitó
            error_log("Se solicitó enviar correo al acudiente: " . $datosCorreo['asp_email_acudiente']);
        }
        
        jsonResponse(['success' => true, 'message' => 'Aspirante actualizado correctamente.']);
        
    } catch (Exception $e) {
        error_log("Error al actualizar aspirante: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>


