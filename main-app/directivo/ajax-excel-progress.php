<?php
include("session.php");
require_once("../class/Sysjobs.php");
Modulos::validarAccesoDirectoPaginas();

// Función para respuesta JSON
function jsonResponse($data) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Función para detectar si es una petición AJAX
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAjaxRequest()) {
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'getProgress') {
        $jobId = $_POST['jobId'] ?? '';
        
        if (empty($jobId)) {
            jsonResponse([
                'success' => false,
                'message' => 'ID de job requerido'
            ]);
        }
        
        // Simular progreso basado en el tiempo transcurrido
        $startTime = $_POST['startTime'] ?? time();
        $currentTime = time();
        $elapsedTime = $currentTime - $startTime;
        
        // Simular diferentes etapas del procesamiento
        $progress = 0;
        $message = 'Preparando archivo...';
        
        if ($elapsedTime > 2) {
            $progress = min(25, ($elapsedTime - 2) * 5);
            $message = 'Validando datos del archivo...';
        }
        
        if ($elapsedTime > 5) {
            $progress = min(50, 25 + (($elapsedTime - 5) * 5));
            $message = 'Procesando estudiantes...';
        }
        
        if ($elapsedTime > 8) {
            $progress = min(75, 50 + (($elapsedTime - 8) * 5));
            $message = 'Guardando información...';
        }
        
        if ($elapsedTime > 12) {
            $progress = min(95, 75 + (($elapsedTime - 12) * 2));
            $message = 'Finalizando procesamiento...';
        }
        
        if ($elapsedTime > 15) {
            $progress = 100;
            $message = 'Procesamiento completado';
        }
        
        jsonResponse([
            'success' => true,
            'progress' => $progress,
            'message' => $message,
            'completed' => $progress >= 100,
            'elapsedTime' => $elapsedTime
        ]);
        
    } elseif ($action === 'checkJobStatus') {
        $jobId = $_POST['jobId'] ?? '';
        
        if (empty($jobId)) {
            jsonResponse([
                'success' => false,
                'message' => 'ID de job requerido'
            ]);
        }
        
        // Aquí verificarías el estado real del job en la base de datos
        // Por ahora simulamos que está completado después de 15 segundos
        $startTime = $_POST['startTime'] ?? time();
        $currentTime = time();
        $elapsedTime = $currentTime - $startTime;
        
        if ($elapsedTime >= 15) {
            // Simular resultados del procesamiento
            $results = [
                'created' => rand(5, 15),
                'updated' => rand(2, 8),
                'errors' => rand(0, 3),
                'errorDetails' => []
            ];
            
            // Generar algunos errores de ejemplo
            if ($results['errors'] > 0) {
                $results['errorDetails'] = [
                    'Fila 5: Faltan datos obligatorios',
                    'Fila 12: Documento duplicado',
                    'Fila 18: Formato de fecha inválido'
                ];
            }
            
            jsonResponse([
                'success' => true,
                'completed' => true,
                'results' => $results
            ]);
        } else {
            jsonResponse([
                'success' => true,
                'completed' => false,
                'message' => 'Procesando...'
            ]);
        }
        
    } else {
        jsonResponse([
            'success' => false,
            'message' => 'Acción no válida'
        ]);
    }
    
} else {
    jsonResponse([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>



