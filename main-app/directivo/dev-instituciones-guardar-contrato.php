<?php
/**
 * GUARDAR CONTRATO DE INSTITUCIÓN
 * Endpoint para subir/actualizar el archivo de contrato
 */

// Limpiar cualquier output previo
if (ob_get_level()) ob_end_clean();
ob_start();

// Habilitar errores para debugging pero no mostrarlos
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Establecer header JSON
header('Content-Type: application/json; charset=UTF-8');

try {
    require_once("session.php");
    
    // Verificar permisos
    Modulos::verificarPermisoDev();
    
    error_log("=== INICIO GUARDAR CONTRATO INSTITUCIÓN ===");
    
    // Obtener ID de institución
    $ins_id = mysqli_real_escape_string($conexion, $_POST['ins_id'] ?? '');
    
    if (empty($ins_id)) {
        throw new Exception('ID de institución no proporcionado');
    }
    
    // Verificar que se envió un archivo
    if (empty($_FILES['ins_contrato_archivo']) || $_FILES['ins_contrato_archivo']['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception('No se recibió ningún archivo');
    }
    
    $archivo = $_FILES['ins_contrato_archivo'];
    
    // Verificar errores de subida
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores = [
            UPLOAD_ERR_INI_SIZE   => 'El archivo excede el tamaño máximo permitido por el servidor',
            UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el tamaño máximo permitido (10MB)',
            UPLOAD_ERR_PARTIAL    => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal en el servidor',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en disco',
            UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida del archivo'
        ];
        throw new Exception($errores[$archivo['error']] ?? 'Error desconocido al subir el archivo');
    }
    
    // Validar tamaño (máximo 10 MB)
    $maxSize = 10 * 1024 * 1024; // 10 MB en bytes
    if ($archivo['size'] > $maxSize) {
        throw new Exception('El archivo no puede superar los 10 MB. Tamaño actual: ' . number_format($archivo['size'] / 1024 / 1024, 2) . ' MB');
    }
    
    // Validar tipo de archivo
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $extensionesPermitidas = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    
    if (!in_array($extension, $extensionesPermitidas)) {
        throw new Exception('Formato de archivo no permitido. Solo se permiten: ' . implode(', ', $extensionesPermitidas));
    }
    
    // Validar MIME type adicional (seguridad)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);
    
    $mimePermitidos = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/jpg'
    ];
    
    if (!in_array($mimeType, $mimePermitidos)) {
        throw new Exception('El tipo de archivo no es válido');
    }
    
    // Crear carpeta si no existe
    $rutaDestino = ROOT_PATH . '/files-general/contratos';
    if (!file_exists($rutaDestino)) {
        if (!mkdir($rutaDestino, 0777, true)) {
            throw new Exception('No se pudo crear la carpeta de contratos');
        }
    }
    
    // Generar nombre único del archivo
    $nombreArchivo = 'contrato_inst_' . $ins_id . '_' . time() . '.' . $extension;
    $rutaCompleta = $rutaDestino . '/' . $nombreArchivo;
    
    // Obtener contrato anterior para eliminarlo
    $consultaAnterior = mysqli_query($conexion, "SELECT ins_contrato FROM " . $baseDatosServicios . ".instituciones 
        WHERE ins_id = '$ins_id' AND ins_enviroment = '" . ENVIROMENT . "'");
    $contratoAnterior = '';
    if ($consultaAnterior && $row = mysqli_fetch_array($consultaAnterior, MYSQLI_BOTH)) {
        $contratoAnterior = $row['ins_contrato'];
    }
    
    // Mover archivo a destino
    if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
        throw new Exception('No se pudo guardar el archivo en el servidor');
    }
    
    error_log("Archivo guardado: $rutaCompleta");
    
    // Actualizar base de datos
    $sql = "UPDATE " . $baseDatosServicios . ".instituciones 
            SET ins_contrato = '$nombreArchivo' 
            WHERE ins_id = '$ins_id' AND ins_enviroment = '" . ENVIROMENT . "'";
    
    if (!mysqli_query($conexion, $sql)) {
        // Si falla la BD, eliminar el archivo subido
        @unlink($rutaCompleta);
        throw new Exception('Error al actualizar la base de datos: ' . mysqli_error($conexion));
    }
    
    // Eliminar contrato anterior si existe y la actualización fue exitosa
    if (!empty($contratoAnterior)) {
        $rutaAnterior = $rutaDestino . '/' . $contratoAnterior;
        if (file_exists($rutaAnterior)) {
            @unlink($rutaAnterior);
            error_log("Contrato anterior eliminado: $contratoAnterior");
        }
    }
    
    // Registrar en historial
    try {
        $idPaginaInterna = 'DV0011';
        $error_reporting_original = error_reporting();
        error_reporting(0);
        ob_start();
        @include("../compartido/historial-acciones-guardar.php");
        ob_end_clean();
        error_reporting($error_reporting_original);
    } catch (Exception $e) {
        error_log("Error en historial: " . $e->getMessage());
    }
    
    error_log("=== FIN EXITOSO GUARDAR CONTRATO ===");
    
    // Limpiar buffer
    if (ob_get_level()) ob_clean();
    
    $response = json_encode([
        'success' => true,
        'message' => '✅ Contrato actualizado exitosamente',
        'archivo' => $nombreArchivo,
        'tamano' => number_format($archivo['size'] / 1024, 2) . ' KB'
    ], JSON_UNESCAPED_UNICODE);
    
    echo $response;
    
    if (ob_get_level()) ob_end_flush();
    exit();
    
} catch (Exception $e) {
    error_log("=== ERROR GUARDAR CONTRATO ===");
    error_log("Mensaje: " . $e->getMessage());
    
    if (ob_get_level()) ob_clean();
    
    $response = json_encode([
        'success' => false,
        'message' => '❌ ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    
    echo $response;
    
    if (ob_get_level()) ob_end_flush();
    exit();
}
?>

