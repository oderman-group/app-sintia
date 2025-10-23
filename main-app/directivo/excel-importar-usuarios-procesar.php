<?php
header('Content-Type: application/json');

include("session.php");
require_once("../class/Usuarios.php");
require '../../librerias/Excel/vendor/autoload.php';
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    // Log inicial
    error_log("=== INICIO PROCESAMIENTO EXCEL ===");
    error_log("BD_GENERAL: " . BD_GENERAL);
    error_log("conf_id_institucion: " . $config['conf_id_institucion']);
    error_log("SESSION bd: " . $_SESSION['bd']);
    error_log("clavePorDefectoUsuarios: " . $clavePorDefectoUsuarios);
    
    // Validar que se recibió el archivo
    if (!isset($_FILES['planilla']) || $_FILES['planilla']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se recibió ningún archivo o hubo un error al subirlo.');
    }
    
    $temName = $_FILES['planilla']['tmp_name'];
    $archivo = $_FILES['planilla']['name'];
    $destino = "../files/excel/";
    $explode = explode(".", $archivo);
    $extension = end($explode);
    $fullArchivo = uniqid('importado_').".".$extension;
    $nombreArchivo = $destino.$fullArchivo;
    
    // Validar extensión
    if (!in_array(strtolower($extension), ['xlsx', 'xls'])) {
        throw new Exception('El archivo debe ser formato Excel (.xlsx o .xls)');
    }
    
    // Mover archivo
    if (!move_uploaded_file($temName, $nombreArchivo)) {
        throw new Exception('No se pudo guardar el archivo subido.');
    }
    
    // Cargar el archivo Excel
    $documento = IOFactory::load($nombreArchivo);
    $hojaActual = $documento->getSheet(0);
    $numFilas = $hojaActual->getHighestDataRow();
    
    // Contadores
    $exitosos = 0;
    $omitidos = 0;
    $errores = 0;
    $detalles = [];
    
    // Preparar SQL para inserción masiva
    $valoresInsert = [];
    $usuariosCreados = [];
    
    // Recorrer filas (empezar desde fila 2, asumiendo que fila 1 es encabezado)
    error_log("Total filas a procesar: " . $numFilas);
    for ($fila = 2; $fila <= $numFilas; $fila++) {
        try {
            // Leer columnas en el orden especificado
            $tipoUsuario = trim($hojaActual->getCell('A' . $fila)->getValue());
            $apellido1 = trim($hojaActual->getCell('B' . $fila)->getValue());
            $apellido2 = trim($hojaActual->getCell('C' . $fila)->getValue());
            $nombre1 = trim($hojaActual->getCell('D' . $fila)->getValue());
            $nombre2 = trim($hojaActual->getCell('E' . $fila)->getValue());
            $usuario = trim($hojaActual->getCell('F' . $fila)->getValue());
            $documento = trim($hojaActual->getCell('G' . $fila)->getValue());
            
            error_log("Fila $fila - Tipo: '$tipoUsuario', Apellido1: '$apellido1', Nombre1: '$nombre1', Doc: '$documento'");
            
            // Validar campos requeridos
            if (empty($tipoUsuario) || empty($apellido1) || empty($nombre1) || empty($documento)) {
                $detalles[] = [
                    'fila' => $fila,
                    'usuario' => $usuario ?: $documento,
                    'nombre' => "$nombre1 $apellido1",
                    'estado' => 'error',
                    'estadoTexto' => 'Error',
                    'mensaje' => 'Faltan campos requeridos (Tipo, Apellido1, Nombre1 o Documento)'
                ];
                $errores++;
                continue;
            }
            
            // Validar tipo de usuario
            if (!in_array($tipoUsuario, ['2', '3', '5'])) {
                $detalles[] = [
                    'fila' => $fila,
                    'usuario' => $usuario ?: $documento,
                    'nombre' => "$nombre1 $apellido1",
                    'estado' => 'error',
                    'estadoTexto' => 'Error',
                    'mensaje' => "Tipo de usuario inválido ($tipoUsuario). Debe ser 2, 3 o 5"
                ];
                $errores++;
                continue;
            }
            
            // Si no se proporciona usuario, usar el documento
            if (empty($usuario)) {
                $usuario = $documento;
            }
            
            // Sanitizar documento (quitar espacios, puntos, comas)
            $documento = preg_replace('/[^0-9]/', '', $documento);
            
            if (empty($documento)) {
                $detalles[] = [
                    'fila' => $fila,
                    'usuario' => $usuario,
                    'nombre' => "$nombre1 $apellido1",
                    'estado' => 'error',
                    'estadoTexto' => 'Error',
                    'mensaje' => 'Documento inválido (debe contener números)'
                ];
                $errores++;
                continue;
            }
            
            // Verificar si el usuario ya existe
            $consultaExiste = mysqli_query($conexion, "SELECT uss_id FROM ".BD_GENERAL.".usuarios 
                WHERE uss_documento = '$documento' 
                AND institucion = {$config['conf_id_institucion']} 
                AND year = {$_SESSION['bd']}");
            
            if (mysqli_num_rows($consultaExiste) > 0) {
                $detalles[] = [
                    'fila' => $fila,
                    'usuario' => $usuario,
                    'nombre' => "$nombre1 $apellido1",
                    'estado' => 'warning',
                    'estadoTexto' => 'Omitido',
                    'mensaje' => 'El usuario ya existe en la plataforma'
                ];
                $omitidos++;
                continue;
            }
            
            // Escapar valores
            $usuario = mysqli_real_escape_string($conexion, $usuario);
            $nombre1 = mysqli_real_escape_string($conexion, $nombre1);
            $nombre2 = mysqli_real_escape_string($conexion, $nombre2);
            $apellido1 = mysqli_real_escape_string($conexion, $apellido1);
            $apellido2 = mysqli_real_escape_string($conexion, $apellido2);
            $documento = mysqli_real_escape_string($conexion, $documento);
            
            // Contraseña por defecto
            $claveEncriptada = SHA1($clavePorDefectoUsuarios);
            
            // Generar ID único para este usuario específico
            $idRegistro = Utilidades::getNextIdSequence($conexion, BD_GENERAL, 'usuarios').$fila;
            error_log("Generado ID único para fila $fila: $idRegistro");
            
            // Preparar valores para inserción
            $valoresInsert[] = "(
                '$idRegistro',
                '$usuario',
                '$claveEncriptada',
                '$tipoUsuario',
                '$nombre1',
                '0',
                '',
                '',
                '126',
                'default.png',
                'default.png',
                '1',
                'blue',
                '0',
                '0',
                now(),
                '{$_SESSION['id']}',
                '0',
                '',
                '',
                '',
                '105',
                '$apellido1',
                '$apellido2',
                '$nombre2',
                '$documento',
                '{$config['conf_id_institucion']}',
                '{$_SESSION['bd']}'
            )";
            
            $usuariosCreados[] = [
                'fila' => $fila,
                'usuario' => $usuario,
                'nombre' => "$nombre1 $nombre2 $apellido1 $apellido2",
                'documento' => $documento,
                'tipo' => $tipoUsuario
            ];
            
            $exitosos++;
            
            $detalles[] = [
                'fila' => $fila,
                'usuario' => $usuario,
                'nombre' => "$nombre1 $apellido1",
                'estado' => 'success',
                'estadoTexto' => 'Exitoso',
                'mensaje' => 'Usuario importado correctamente'
            ];
            
        } catch (Exception $e) {
            $detalles[] = [
                'fila' => $fila,
                'usuario' => $usuario ?? 'N/A',
                'nombre' => isset($nombre1) && isset($apellido1) ? "$nombre1 $apellido1" : 'N/A',
                'estado' => 'error',
                'estadoTexto' => 'Error',
                'mensaje' => 'Error: ' . $e->getMessage()
            ];
            $errores++;
            error_log("Error procesando fila $fila: " . $e->getMessage());
        }
    }
    
    // Insertar todos los usuarios en una sola query
    error_log("Valores a insertar: " . count($valoresInsert));
    if (count($valoresInsert) > 0) {
        $sql = "INSERT INTO ".BD_GENERAL.".usuarios(
            uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_email, 
            uss_celular, uss_genero, uss_foto, uss_portada, uss_idioma, uss_tema, uss_permiso1, 
            uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_intentos_fallidos, 
            uss_tema_sidebar, uss_tema_header, uss_tema_logo, uss_tipo_documento, uss_apellido1, 
            uss_apellido2, uss_nombre2, uss_documento, institucion, year
        ) VALUES " . implode(", ", $valoresInsert);
        
        error_log("SQL Query: " . $sql);
        $resultado = mysqli_query($conexion, $sql);
        
        if (!$resultado) {
            error_log("Error SQL: " . mysqli_error($conexion));
            throw new Exception('Error al insertar usuarios en la base de datos: ' . mysqli_error($conexion));
        } else {
            error_log("Insert exitoso. Filas afectadas: " . mysqli_affected_rows($conexion));
        }
    } else {
        error_log("No hay valores para insertar");
    }
    
    // Eliminar archivo temporal
    if (file_exists($nombreArchivo)) {
        unlink($nombreArchivo);
    }
    
    // Log final
    error_log("=== RESULTADOS FINALES ===");
    error_log("Exitosos: $exitosos");
    error_log("Omitidos: $omitidos");
    error_log("Errores: $errores");
    error_log("Total: " . ($exitosos + $omitidos + $errores));
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'exitosos' => $exitosos,
        'omitidos' => $omitidos,
        'errores' => $errores,
        'total' => $exitosos + $omitidos + $errores,
        'detalles' => $detalles,
        'usuarios_creados' => $usuariosCreados,
        'message' => "Se importaron $exitosos usuarios exitosamente"
    ]);
    
} catch (Exception $e) {
    error_log("Error general en importación: " . $e->getMessage());
    
    // Eliminar archivo si existe
    if (isset($nombreArchivo) && file_exists($nombreArchivo)) {
        unlink($nombreArchivo);
    }
    
    echo json_encode([
        'success' => false,
        'exitosos' => 0,
        'omitidos' => 0,
        'errores' => 0,
        'total' => 0,
        'detalles' => [],
        'message' => $e->getMessage()
    ]);
}
?>

