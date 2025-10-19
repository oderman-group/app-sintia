<?php
include("bd-conexion.php");
include("php-funciones.php");

// ----------------------------------------------------
// 1. CONFIGURACIÓN DE SEGURIDAD
// ----------------------------------------------------

// Directorio donde se guardará el archivo
$destino = "files/comprobantes"; 

// Lista de Tipos MIME permitidos y sus extensiones correspondientes.
// Esto es CRUCIAL para la seguridad. Define solo los que realmente esperas.
$tipos_permitidos = [
    'application/pdf'                                                         => 'pdf',       // Archivos PDF
    'image/jpeg'                                                              => 'jpg',       // Incluye .jpg, .jpeg, y .jfif
    'image/png'                                                               => 'png',       // Imágenes PNG
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',      // Archivos Word (.docx)
    // También es buena práctica incluir el tipo MIME para JPEG que usan algunos navegadores:
    // 'image/pjpeg'                                                                  => 'jpg', 
    // ¡Añade otros tipos si son necesarios!
];

// ----------------------------------------------------
// 2. PROCESAMIENTO DEL ARCHIVO SUBIDO
// ----------------------------------------------------

if (!empty($_FILES['comprobante']['name']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {

    // A. Obtener el Tipo MIME real del archivo (el método seguro)
    // Usamos 'tmp_name' porque es la ruta temporal del archivo subido en el servidor.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_mime_real = finfo_file($finfo, $_FILES['comprobante']['tmp_name']);
    finfo_close($finfo);

    // B. Validación del Tipo MIME
    if (array_key_exists($tipo_mime_real, $tipos_permitidos)) {
        
        // 1. Obtener la extensión segura y verdadera
        $extension_verdadera = $tipos_permitidos[$tipo_mime_real];
        
        // 2. Generar un nombre de archivo único y seguro
        // Usamos la extensión verdadera que mapeamos del Tipo MIME.
        $nombre_archivo_seguro = uniqid('comp_'.$_POST['solicitud'].'_') . "." . $extension_verdadera;
        
        // 3. Mover el archivo subido
        $ruta_final = $destino . "/" . $nombre_archivo_seguro;
        
        // **ATENCIÓN:** Se elimina el @unlink() previo, ya que un archivo con 'uniqid()'
        // nunca existirá antes de ser movido.

        if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $ruta_final)) {
            
            // El archivo se movió correctamente, actualiza el nombre del archivo en la DB
            $comprobante_db = $nombre_archivo_seguro;

        } else {
            // Manejar error al mover el archivo (permisos, etc.)
            // Puedes asignar un valor nulo o un mensaje de error a $comprobante_db
            $comprobante_db = NULL; 
            echo "Error al guardar el archivo en el directorio de destino.";
        }

    } else {
        // Validación fallida: el tipo MIME es peligroso o no está permitido
        $comprobante_db = NULL;
        echo "Tipo de archivo NO permitido: " . $tipo_mime_real;
    }
} else {
    // No se subió ningún archivo o hubo un error de subida
    $comprobante_db = NULL; // O el valor que corresponda si no es obligatorio
}

// ----------------------------------------------------
// 3. PREPARACIÓN DE LA CONSULTA SQL
// ----------------------------------------------------

$sql = "UPDATE aspirantes SET asp_comprobante = :comprobante, asp_estado_solicitud = 1 WHERE asp_id = :idR";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(':idR', $_POST['solicitud'], PDO::PARAM_INT);
$stmt->bindParam(':comprobante', $comprobante_db, PDO::PARAM_STR);                              

$stmt->execute();

echo '<script type="text/javascript">window.location.href="respuestas-usuario.php?idInst='.$_REQUEST['idInst'].'&msg='.base64_encode(2).'";</script>';