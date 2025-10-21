<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");

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
        $matId = $_POST['mat_id'] ?? null;
        $primerNombre = trim($_POST['primer_nombre'] ?? '');
        $segundoNombre = trim($_POST['segundo_nombre'] ?? '');
        $primerApellido = trim($_POST['primer_apellido'] ?? '');
        $segundoApellido = trim($_POST['segundo_apellido'] ?? '');
        $fechaNacimiento = $_POST['fecha_nacimiento'] ?? '';
        $genero = $_POST['genero'] ?? '';
        $direccion = trim($_POST['direccion'] ?? '');
        $barrio = trim($_POST['barrio'] ?? '');
        $celular = trim($_POST['celular'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $estrato = $_POST['estrato'] ?? '';
        $tipoSangre = $_POST['tipo_sangre'] ?? '';
        $eps = trim($_POST['eps'] ?? '');
        
        if (empty($matId) || empty($primerNombre) || empty($primerApellido)) {
            jsonResponse(['success' => false, 'message' => 'ID de matrícula, primer nombre y primer apellido son obligatorios.']);
        }
        
        // Crear consulta SQL directa para actualizar solo los campos necesarios
        $sql = "UPDATE ".BD_ACADEMICA.".academico_matriculas SET 
                    mat_nombres = :nombres,
                    mat_nombre2 = :nombre2,
                    mat_primer_apellido = :apellido1,
                    mat_segundo_apellido = :apellido2,
                    mat_fecha_nacimiento = :fechaNacimiento,
                    mat_genero = :genero,
                    mat_direccion = :direccion,
                    mat_barrio = :barrio,
                    mat_celular = :celular,
                    mat_telefono = :telefono,
                    mat_email = :email,
                    mat_estrato = :estrato,
                    mat_tipo_sangre = :tipoSangre,
                    mat_eps = :eps
                WHERE mat_id = :id 
                AND mat_eliminado = 0
                AND institucion = :institucion
                AND year = :year";
        
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(':nombres', $primerNombre, PDO::PARAM_STR);
        $stmt->bindParam(':nombre2', $segundoNombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido1', $primerApellido, PDO::PARAM_STR);
        $stmt->bindParam(':apellido2', $segundoApellido, PDO::PARAM_STR);
        $stmt->bindParam(':fechaNacimiento', $fechaNacimiento, PDO::PARAM_STR);
        $stmt->bindParam(':genero', $genero, PDO::PARAM_STR);
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':barrio', $barrio, PDO::PARAM_STR);
        $stmt->bindParam(':celular', $celular, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':estrato', $estrato, PDO::PARAM_STR);
        $stmt->bindParam(':tipoSangre', $tipoSangre, PDO::PARAM_STR);
        $stmt->bindParam(':eps', $eps, PDO::PARAM_STR);
        $stmt->bindParam(':id', $matId, PDO::PARAM_STR);
        $stmt->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
        
        $resultado = $stmt->execute();
        
        if ($resultado === false) {
            jsonResponse(['success' => false, 'message' => 'No se pudo actualizar el estudiante en la base de datos.']);
        }
        
        $filasAfectadas = $stmt->rowCount();
        
        if ($filasAfectadas === 0) {
            jsonResponse(['success' => false, 'message' => 'No se encontró el estudiante o no hubo cambios en los datos.']);
        }
        
        jsonResponse(['success' => true, 'message' => 'Estudiante actualizado correctamente. Se modificaron ' . $filasAfectadas . ' registro(s).']);
        
    } catch (Exception $e) {
        error_log("Error al actualizar estudiante: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor.']);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>
