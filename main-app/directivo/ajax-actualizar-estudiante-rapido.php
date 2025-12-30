<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");

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
        
        // Verificar variables de sesión
        if (!isset($config['conf_id_institucion'])) {
            jsonResponse(['success' => false, 'message' => 'Error de configuración de institución.']);
        }
        
        if (!isset($_SESSION["bd"])) {
            jsonResponse(['success' => false, 'message' => 'Error de configuración de año.']);
        }
        
        // Obtener datos del POST y convertir tipos apropiados
        $matId = $_POST['mat_id'] ?? null;
        $tipoDocumento = $_POST['tipo_documento'] ?? '';
        // lugar_expedicion y lugar_nacimiento ahora vienen como IDs de ciudad
        $lugarExpedicion = !empty($_POST['lugar_expedicion']) ? (int)$_POST['lugar_expedicion'] : null;
        $primerNombre = trim($_POST['primer_nombre'] ?? '');
        $segundoNombre = trim($_POST['segundo_nombre'] ?? '');
        $primerApellido = trim($_POST['primer_apellido'] ?? '');
        $segundoApellido = trim($_POST['segundo_apellido'] ?? '');
        $fechaNacimiento = $_POST['fecha_nacimiento'] ?? '';
        // lugar_nacimiento ahora viene como ID de ciudad
        $lugarNacimiento = !empty($_POST['lugar_nacimiento']) ? (int)$_POST['lugar_nacimiento'] : null;
        $genero = $_POST['genero'] ?? '';
        $religion = $_POST['religion'] ?? '';
        $direccion = trim($_POST['direccion'] ?? '');
        $barrio = trim($_POST['barrio'] ?? '');
        $ciudadResidencia = trim($_POST['ciudad_residencia'] ?? '');
        $celular = trim($_POST['celular'] ?? '');
        $celular2 = trim($_POST['celular2'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $estrato = $_POST['estrato'] ?? '';
        $tipoEstudiante = $_POST['tipo_estudiante'] ?? '';
        $inclusion = $_POST['inclusion'] ?? '0';
        $tipoSangre = $_POST['tipo_sangre'] ?? '';
        $eps = trim($_POST['eps'] ?? '');
        
        // Los valores ya vienen como IDs desde el frontend, solo necesitamos convertirlos a enteros
        $tipoDocumentoInt = !empty($tipoDocumento) ? (int)$tipoDocumento : null;
        $generoInt = !empty($genero) ? (int)$genero : null;
        $religionInt = !empty($religion) ? (int)$religion : null;
        $estratoInt = !empty($estrato) ? (int)$estrato : null;
        $tipoEstudianteInt = !empty($tipoEstudiante) ? (int)$tipoEstudiante : null;
        $inclusionInt = !empty($inclusion) ? (int)$inclusion : 0;
        $tipoSangreInt = !empty($tipoSangre) ? (int)$tipoSangre : null;
        
        // Validar campos obligatorios
        if (empty($matId) || empty($primerNombre) || empty($primerApellido)) {
            jsonResponse(['success' => false, 'message' => 'ID de matrícula, primer nombre y primer apellido son obligatorios.']);
        }
        
        // Validar formato de fecha si se proporciona
        if (!empty($fechaNacimiento)) {
            $fechaTimestamp = strtotime($fechaNacimiento);
            if ($fechaTimestamp === false) {
                jsonResponse(['success' => false, 'message' => 'Formato de fecha inválido.']);
            }
            
            // Validar que la fecha de nacimiento no sea futura ni menor de 1 año
            $fechaMinima = strtotime('-1 year');
            $fechaMaxima = time(); // Fecha actual
            
            if ($fechaTimestamp > $fechaMinima) {
                jsonResponse(['success' => false, 'message' => 'La fecha de nacimiento no puede ser futura ni menor de 1 año.']);
            }
        }
        
        // Validar email si se proporciona
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'Formato de email inválido.']);
        }
        
        // Obtener datos del estudiante para obtener mat_id_usuario y documento actual
        $datosEstudiante = Estudiantes::obtenerDatosEstudiante($matId);
        if (empty($datosEstudiante)) {
            jsonResponse(['success' => false, 'message' => 'Estudiante no encontrado.']);
        }
        
        $matIdUsuario = !empty($datosEstudiante['mat_id_usuario']) ? $datosEstudiante['mat_id_usuario'] : null;
        $documentoActual = !empty($datosEstudiante['mat_documento']) ? $datosEstudiante['mat_documento'] : '';
        
        // Crear consulta SQL para actualizar
        // mat_lugar_expedicion y mat_lugar_nacimiento ahora almacenan IDs de ciudad
        $sql = "UPDATE ".BD_ACADEMICA.".academico_matriculas SET 
                    mat_tipo_documento = :tipoDocumento,
                    mat_lugar_expedicion = :lugarExpedicion,
                    mat_nombres = :nombres,
                    mat_nombre2 = :nombre2,
                    mat_primer_apellido = :apellido1,
                    mat_segundo_apellido = :apellido2,
                    mat_fecha_nacimiento = :fechaNacimiento,
                    mat_lugar_nacimiento = :lugarNacimiento,
                    mat_genero = :genero,
                    mat_religion = :religion,
                    mat_direccion = :direccion,
                    mat_barrio = :barrio,
                    mat_ciudad_residencia = :ciudadResidencia,
                    mat_celular = :celular,
                    mat_celular2 = :celular2,
                    mat_telefono = :telefono,
                    mat_email = :email,
                    mat_estrato = :estrato,
                    mat_tipo = :tipoEstudiante,
                    mat_inclusion = :inclusion,
                    mat_tipo_sangre = :tipoSangre,
                    mat_eps = :eps
                WHERE mat_id = :id 
                AND mat_eliminado = 0
                AND institucion = :institucion
                AND year = :year";
        
        $stmt = $conexionPDO->prepare($sql);
        
        // Bind de parámetros
        $stmt->bindParam(':tipoDocumento', $tipoDocumentoInt, PDO::PARAM_INT);
        $stmt->bindParam(':lugarExpedicion', $lugarExpedicion, PDO::PARAM_INT);
        $stmt->bindParam(':nombres', $primerNombre, PDO::PARAM_STR);
        $stmt->bindParam(':nombre2', $segundoNombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido1', $primerApellido, PDO::PARAM_STR);
        $stmt->bindParam(':apellido2', $segundoApellido, PDO::PARAM_STR);
        $stmt->bindParam(':fechaNacimiento', $fechaNacimiento, PDO::PARAM_STR);
        $stmt->bindParam(':lugarNacimiento', $lugarNacimiento, PDO::PARAM_INT);
        $stmt->bindParam(':genero', $generoInt, PDO::PARAM_INT);
        $stmt->bindParam(':religion', $religionInt, PDO::PARAM_INT);
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':barrio', $barrio, PDO::PARAM_STR);
        $stmt->bindParam(':ciudadResidencia', $ciudadResidencia, PDO::PARAM_STR);
        $stmt->bindParam(':celular', $celular, PDO::PARAM_STR);
        $stmt->bindParam(':celular2', $celular2, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':estrato', $estratoInt, PDO::PARAM_INT);
        $stmt->bindParam(':tipoEstudiante', $tipoEstudianteInt, PDO::PARAM_INT);
        $stmt->bindParam(':inclusion', $inclusionInt, PDO::PARAM_INT);
        $stmt->bindParam(':tipoSangre', $tipoSangreInt, PDO::PARAM_INT);
        $stmt->bindParam(':eps', $eps, PDO::PARAM_STR);
        $stmt->bindParam(':id', $matId, PDO::PARAM_STR);
        $stmt->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
        
        // Ejecutar consulta
        $resultado = $stmt->execute();
        
        if ($resultado === false) {
            jsonResponse(['success' => false, 'message' => 'No se pudo actualizar el estudiante en la base de datos.']);
        }
        
        $filasAfectadas = $stmt->rowCount();
        
        if ($filasAfectadas === 0) {
            jsonResponse(['success' => false, 'message' => 'No se encontró el estudiante o no hubo cambios en los datos.']);
        }
        
        // Sincronizar campos compartidos con la tabla usuarios (si existe mat_id_usuario)
        if (!empty($matIdUsuario)) {
            try {
                // Obtener documento actualizado (si no cambió, usar el que ya tenía)
                // Necesitamos obtener el documento actualizado desde la BD o usar el documento actual
                // Como el documento no se actualiza en la edición rápida, usamos el documento actual
                
                // Construir array de actualización para usuarios (solo campos compartidos)
                $updateUsuario = [];
                
                // Solo actualizar campos que fueron modificados o que siempre deben sincronizarse
                if (!empty($fechaNacimiento)) {
                    $updateUsuario['uss_fecha_nacimiento'] = $fechaNacimiento;
                }
                if (!empty($primerNombre)) {
                    $updateUsuario['uss_nombre'] = $primerNombre;
                }
                if ($segundoNombre !== null) {
                    $updateUsuario['uss_nombre2'] = $segundoNombre;
                }
                if (!empty($primerApellido)) {
                    $updateUsuario['uss_apellido1'] = $primerApellido;
                }
                if ($segundoApellido !== null) {
                    $updateUsuario['uss_apellido2'] = $segundoApellido;
                }
                if ($email !== null) {
                    $updateUsuario['uss_email'] = strtolower($email);
                }
                if ($tipoDocumentoInt !== null) {
                    $updateUsuario['uss_tipo_documento'] = $tipoDocumentoInt;
                }
                if ($celular !== null) {
                    $updateUsuario['uss_celular'] = $celular;
                }
                if ($telefono !== null) {
                    $updateUsuario['uss_telefono'] = $telefono;
                }
                if ($direccion !== null) {
                    $updateUsuario['uss_direccion'] = $direccion;
                }
                if ($lugarExpedicion !== null) {
                    $updateUsuario['uss_lugar_expedicion'] = $lugarExpedicion;
                }
                if ($generoInt !== null) {
                    $updateUsuario['uss_genero'] = $generoInt;
                }
                // Documento y usuario siempre se sincronizan (para mantener consistencia)
                // El documento no se cambia en edición rápida, pero se asegura que esté sincronizado
                if (!empty($documentoActual)) {
                    $updateUsuario['uss_usuario'] = $documentoActual;
                    $updateUsuario['uss_documento'] = $documentoActual;
                }
                
                // Actualizar tabla usuarios con los campos compartidos
                // Se actualizan todos los campos compartidos para mantener sincronización completa
                if (!empty($updateUsuario)) {
                    UsuariosPadre::actualizarUsuarios($config, $matIdUsuario, $updateUsuario);
                }
            } catch (Exception $e) {
                // Registrar error pero no fallar la actualización principal
                error_log("Error al sincronizar datos con tabla usuarios: " . $e->getMessage());
                // Continuar, la actualización de academico_matriculas ya se hizo
            }
        }
        
        jsonResponse(['success' => true, 'message' => 'Estudiante actualizado correctamente. Se modificaron ' . $filasAfectadas . ' registro(s).']);
        
    } catch (Exception $e) {
        error_log("Error al actualizar estudiante: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>
