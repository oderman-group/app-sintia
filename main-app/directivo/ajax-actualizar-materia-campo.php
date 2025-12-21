<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");

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
        $materiaId = $_POST['materia_id'] ?? null;
        $campo = $_POST['campo'] ?? null;
        $valor = trim($_POST['valor'] ?? '');
        
        // Validar campos obligatorios
        if (empty($materiaId) || empty($campo) || empty($valor)) {
            jsonResponse(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
        }
        
        // Validar que el campo sea uno permitido
        $camposPermitidos = ['nombre', 'valor'];
        if (!in_array($campo, $camposPermitidos)) {
            jsonResponse(['success' => false, 'message' => 'Campo no permitido para edición.']);
        }
        
        // Mapear campo a columna de la base de datos
        $columnaBD = ($campo === 'nombre') ? 'mat_nombre' : 'mat_valor';
        
        // Validar valor según el campo
        if ($campo === 'valor') {
            // Validar que sea numérico y esté en rango 0-100
            if (!is_numeric($valor) || $valor < 0 || $valor > 100) {
                jsonResponse(['success' => false, 'message' => 'El valor debe ser un número entre 0 y 100.']);
            }
        } else if ($campo === 'nombre') {
            // Validar longitud del nombre
            if (strlen($valor) < 2 || strlen($valor) > 100) {
                jsonResponse(['success' => false, 'message' => 'El nombre debe tener entre 2 y 100 caracteres.']);
            }
        }
        
        // Actualizar el campo de la materia
        $sql = "UPDATE ".BD_ACADEMICA.".academico_materias 
                SET $columnaBD = :valor 
                WHERE mat_id = :materia_id 
                AND institucion = :institucion 
                AND year = :year";
        
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->bindParam(':materia_id', $materiaId, PDO::PARAM_STR);
        $stmt->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
        
        error_log("Actualizando materia - ID: $materiaId, Campo: $columnaBD, Valor: $valor, Institución: " . $config['conf_id_institucion'] . ", Year: " . $_SESSION["bd"]);
        
        $resultado = $stmt->execute();
        
        if ($resultado === false) {
            jsonResponse(['success' => false, 'message' => 'No se pudo actualizar la materia.']);
        }
        
        $filasAfectadas = $stmt->rowCount();
        
        error_log("Filas afectadas en actualización de materia: " . $filasAfectadas);
        
        if ($filasAfectadas === 0) {
            jsonResponse(['success' => false, 'message' => 'No se realizaron cambios. Verifique que la materia exista en su institución y año.']);
        }
        
        $nombreCampo = ($campo === 'nombre') ? 'nombre' : 'valor';
        
        jsonResponse([
            'success' => true, 
            'message' => 'El ' . $nombreCampo . ' de la materia ha sido actualizado correctamente.'
        ]);
        
    } catch (Exception $e) {
        error_log("Error al actualizar campo de materia: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>


