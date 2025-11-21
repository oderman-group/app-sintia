<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Estudiante.php");

header('Content-Type: application/json');

try {
    $estudiantes = isset($_POST['estudiantes']) ? $_POST['estudiantes'] : [];
    
    if (empty($estudiantes) || !is_array($estudiantes)) {
        echo json_encode([
            'success' => false,
            'error' => 'No se proporcionaron estudiantes para validar'
        ]);
        exit;
    }
    
    $estudiantesConNotas = [];
    $puedeModificarGradoGrupo = $config['conf_puede_cambiar_grado_y_grupo'] == 1;
    
    foreach ($estudiantes as $idEstudiante) {
        if (empty($idEstudiante)) {
            continue;
        }
        
        // Crear instancia del estudiante
        $EstudianteObj = new Administrativo_Usuario_Estudiante(['mat_id' => $idEstudiante]);
        $tieneRegistrosAcademicos = (bool) $EstudianteObj->tieneRegistrosAcademicos();
        
        if ($tieneRegistrosAcademicos && !$puedeModificarGradoGrupo) {
            $estudiantesConNotas[] = $idEstudiante;
        }
    }
    
    $tieneAlgunoConNotas = count($estudiantesConNotas) > 0;
    
    echo json_encode([
        'success' => true,
        'tieneNotas' => $tieneAlgunoConNotas,
        'estudiantesConNotas' => $estudiantesConNotas,
        'cantidadConNotas' => count($estudiantesConNotas),
        'puedeModificarGradoGrupo' => $puedeModificarGradoGrupo
    ]);
    
} catch (Exception $e) {
    error_log('Error en ajax-validar-notas-estudiantes.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al validar notas: ' . $e->getMessage()
    ]);
}
?>

