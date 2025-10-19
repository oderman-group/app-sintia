<?php
require_once("servicios/Servicios.php");
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

class DocumentManager
{
    // La constante MAXIMO_PESO_ARCHIVO_MB debe estar definida, aquí usamos un placeholder.
    // Asumo que esta constante contiene el tamaño máximo en bytes.
    const MAXIMO_PESO_ARCHIVO_BYTES = 5242880; // 5 * 1024 * 1024 bytes (5MB)
    const MAXIMO_PESO_ARCHIVO_MB = 5; // Para mensajes de error
    
    // Lista Maestra de Tipos MIME permitidos y sus extensiones.
    // Esta es la fuente única de verdad para la validación de archivos.
    const ALLOWED_MIMES = [
        // Documentos Comunes
        'application/pdf' => 'pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx', // Word DOCX
        'text/plain' => 'txt', // Texto simple
        
        // Excel
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx', // Excel XLSX
        'application/vnd.ms-excel' => 'xls', // Excel XLS (Formato antiguo)
        
        // Imágenes
        'image/jpeg' => 'jpg', // Incluye .jpg, .jpeg y .jfif
        'image/png'  => 'png',
        
        // Archivos Comprimidos
        'application/zip' => 'zip',
        'application/x-rar-compressed' => 'rar', // RAR
        'application/gzip' => 'gz', // Gzip
    ];

    /**
     * Función auxiliar para manejar de forma segura la subida de un único archivo.
     * Realiza validación de MIME, tamaño, guarda el archivo y devuelve el nombre seguro.
     *
     * @param array $fileData Array de $_FILES['nombre_campo']
     * @param string $destinationDir Directorio de destino
     * @param string $filePrefix Prefijo para el nombre único (ej: 'pyz_')
     * @return string|null El nombre del archivo seguro si tiene éxito, o null en caso de error.
     */
    public static function processUploadedFile(array $fileData, string $destinationDir, string $filePrefix): ?string
    {
        // 1. Verificar el estado inicial del archivo subido
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            // No se subió ningún archivo, o hubo un error de servidor/PHP.
            return null; 
        }

        // 2. Validación de tamaño (comparando con la constante de la clase)
        if ($fileData['size'] > self::MAXIMO_PESO_ARCHIVO_BYTES) {
            // Muestra mensaje de error y retorna null, siguiendo el patrón de la lógica original.
            echo "El tamaño del archivo '" . $fileData['name'] . "' excede el límite permitido (" . self::MAXIMO_PESO_ARCHIVO_MB . "MB).";
            return null;
        }

        // 3. Obtener el Tipo MIME real usando la extensión Fileinfo (CRUCIAL para seguridad)
        if (!extension_loaded('fileinfo')) {
            echo "Error Fatal: La extensión 'fileinfo' de PHP no está habilitada. La validación segura es imposible.";
            exit(); // Detenemos la ejecución si no podemos validar de forma segura.
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type_real = finfo_file($finfo, $fileData['tmp_name']);
        finfo_close($finfo);

        // 4. Validación del Tipo MIME contra la lista maestra
        if (!array_key_exists($mime_type_real, self::ALLOWED_MIMES)) {
            echo "El tipo de archivo real (MIME: " . $mime_type_real . ") para '" . $fileData['name'] . "' no está permitido. Tipos permitidos: " . implode(', ', array_keys(self::ALLOWED_MIMES));
            return null;
        }
        
        // 5. Obtener la extensión segura y verdadera
        $extension_verdadera = self::ALLOWED_MIMES[$mime_type_real];

        // 6. Crear el directorio si no existe
        if (!file_exists($destinationDir)) {
            // Usamos 0777 para permisos, aunque 0755 es a menudo preferible en entornos de producción.
            if (!mkdir($destinationDir, 0777, true)) {
                echo "Error: No se pudo crear el directorio de destino: " . $destinationDir;
                return null;
            }
        }

        // 7. Generar nombre de archivo único y seguro
        $nombre_archivo_seguro = uniqid($filePrefix . '_') . "." . $extension_verdadera;
        $ruta_final = $destinationDir . "/" . $nombre_archivo_seguro;
        
        // Nota: Se omite @unlink porque el nombre es único (uniqid).

        // 8. Mover el archivo subido
        if (move_uploaded_file($fileData['tmp_name'], $ruta_final)) {
            echo "El archivo '" . $fileData['name'] . "' se movió correctamente como: " . $nombre_archivo_seguro . "<br>";
            return $nombre_archivo_seguro;
        } else {
            echo "Hubo un error al mover el archivo '" . $fileData['name'] . "'. Error de permisos o I/O.<br>";
            return null;
        }
    }

}