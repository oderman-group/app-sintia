<?php
/**
 * Endpoint para edición masiva de cargas académicas
 * Permite actualizar múltiples cargas académicas con los mismos valores
 */

header('Content-Type: application/json; charset=utf-8');
include("session.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

// Asegurar que la conexión esté disponible
if (!isset($conexion)) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: No hay conexión a la base de datos.'
    ]);
    exit;
}

// Validar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido. Solo se acepta POST.'
    ]);
    exit;
}

try {
    // Log de inicio
    error_log("=== INICIO EDICIÓN MASIVA ===");
    error_log("POST recibido: " . print_r($_POST, true));
    error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
    error_log("Content-Type: " . (isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'NO DEFINIDO'));
    error_log("Raw input: " . file_get_contents('php://input'));
    
    // Validar que existan los datos necesarios
    if (!isset($_POST['cargas']) || !isset($_POST['campos'])) {
        error_log("ERROR: Datos incompletos - cargas: " . (isset($_POST['cargas']) ? 'SI' : 'NO') . ", campos: " . (isset($_POST['campos']) ? 'SI' : 'NO'));
        throw new Exception('Datos incompletos. Se requieren cargas y campos.');
    }

    $cargas = $_POST['cargas'];
    $campos = $_POST['campos'];
    
    error_log("Cargas recibidas: " . print_r($cargas, true));
    error_log("Campos recibidos: " . print_r($campos, true));
    error_log("Cargas después de asignar: " . print_r($cargas, true));
    error_log("Tipo de cargas después de asignar: " . gettype($cargas));

    // Validar que haya cargas para actualizar
    if (!is_array($cargas) || count($cargas) === 0) {
        throw new Exception('No se seleccionaron cargas para actualizar.');
    }

    // Validar que haya campos para actualizar
    if (!is_array($campos) || count($campos) === 0) {
        throw new Exception('No se especificaron campos para actualizar.');
    }

    // Sanitizar los IDs de las cargas (son strings, no números)
    $cargas = array_map(function($id) use ($conexion) {
        return mysqli_real_escape_string($conexion, trim($id));
    }, $cargas);
    
    // Remover valores vacíos
    $cargas = array_filter($cargas, function($id) {
        return !empty($id);
    });
    
    error_log("Cargas después de sanitizar: " . print_r($cargas, true));

    // Mapeo de nombres de campos del formulario a nombres de columnas en la BD
    $camposMapeados = [
        'periodo' => 'car_periodo',
        'docente' => 'car_docente',
        'curso' => 'car_curso',
        'grupo' => 'car_grupo',
        'asignatura' => 'car_materia',
        'ih' => 'car_ih',
        'dg' => 'car_director_grupo',
        'estado' => 'car_activa',
        'maxIndicadores' => 'car_maximos_indicadores',
        'maxActividades' => 'car_maximas_calificaciones',
        'indicadorAutomatico' => 'car_indicador_automatico',
        'tematica' => 'car_tematica',
        'observacionesBoletin' => 'car_observaciones_boletin'
    ];

    // Preparar array de actualización con nombres de columnas correctos
    $datosActualizar = [];
    foreach ($campos as $nombreCampo => $valor) {
        // Validar que el valor no esté vacío
        if (empty($valor) && $valor !== '0' && $valor !== 0) {
            error_log("Campo '$nombreCampo' está vacío, se omite");
            continue;
        }
        
        if (isset($camposMapeados[$nombreCampo])) {
            $columna = $camposMapeados[$nombreCampo];
            
            error_log("Procesando campo: $nombreCampo (columna: $columna), valor: $valor");
            
            // Sanitizar el valor según el tipo de campo
            $valorSanitizado = null;
            
            // Campos numéricos
            if (in_array($nombreCampo, ['periodo', 'curso', 'grupo', 'asignatura', 'ih', 'maxIndicadores', 'maxActividades'])) {
                $valorSanitizado = intval($valor);
                error_log("Campo numérico '$nombreCampo' sanitizado: $valorSanitizado");
                
                // Validar que sea un número válido (mayor a 0 excepto para algunos campos)
                if ($valorSanitizado <= 0 && !in_array($nombreCampo, ['maxIndicadores', 'maxActividades'])) {
                    error_log("ERROR: Campo '$nombreCampo' tiene valor inválido: $valorSanitizado");
                    continue;
                }
            }
            // Campos de texto (docente es un ID que puede ser alfanumérico o numérico)
            elseif ($nombreCampo === 'docente') {
                // El docente puede ser texto o número, mantenerlo como string
                $valorSanitizado = trim($valor);
                // Si es numérico, convertir a string sin escapar con comillas
                if (is_numeric($valorSanitizado)) {
                    $valorSanitizado = strval($valorSanitizado);
                }
                error_log("Campo docente sanitizado: '$valorSanitizado'");
            }
            // Campos booleanos (0 o 1)
            elseif (in_array($nombreCampo, ['dg', 'estado', 'indicadorAutomatico', 'tematica', 'observacionesBoletin'])) {
                $valorSanitizado = intval($valor);
                // Asegurar que sea solo 0 o 1
                $valorSanitizado = ($valorSanitizado === 1) ? 1 : 0;
                error_log("Campo booleano '$nombreCampo' sanitizado: $valorSanitizado");
            }
            else {
                // Por defecto, escapar como string
                $valorSanitizado = mysqli_real_escape_string($conexion, trim($valor));
                error_log("Campo string '$nombreCampo' sanitizado: '$valorSanitizado'");
            }
            
            $datosActualizar[$columna] = $valorSanitizado;
        } else {
            error_log("ADVERTENCIA: Campo '$nombreCampo' no tiene mapeo en camposMapeados");
        }
    }

    // Validar que haya campos válidos para actualizar
    if (count($datosActualizar) === 0) {
        error_log("ERROR: No hay campos válidos para actualizar");
        throw new Exception('No se encontraron campos válidos para actualizar.');
    }
    
    error_log("Datos a actualizar preparados: " . print_r($datosActualizar, true));
    error_log("Variable cargas antes del count: " . print_r($cargas, true));
    error_log("Tipo de variable cargas: " . gettype($cargas));
    error_log("Es array: " . (is_array($cargas) ? 'SI' : 'NO'));
    error_log("Total de cargas a actualizar: " . count($cargas));

    // Contador de actualizaciones exitosas
    $actualizadas = 0;
    $errores = [];

    // Actualizar cada carga académica
    foreach ($cargas as $idCarga) {
        try {
            error_log("Procesando carga ID: $idCarga");
            
            // Validar que el ID de carga sea válido (alfanumérico)
            if (empty($idCarga) || !preg_match('/^[A-Z0-9]+$/', $idCarga)) {
                $mensaje = "ID de carga inválido: $idCarga";
                error_log("ERROR: $mensaje");
                $errores[] = $mensaje;
                continue;
            }
            
            error_log("✓ ID de carga válido: $idCarga");

            // Actualizar la carga usando UPDATE directo
            $updateParts = [];
            foreach ($datosActualizar as $columna => $valor) {
                error_log("Preparando UPDATE para columna: $columna, valor: " . var_export($valor, true) . ", tipo: " . gettype($valor));
                
                // Para campos numéricos (excepto docente que puede ser alfanumérico)
                if (is_numeric($valor) && $columna !== 'car_docente') {
                    $updateParts[] = "$columna = $valor";
                } 
                // Para campos de texto o alfanuméricos
                else {
                    // Escapar el valor para evitar inyección SQL
                    $valorEscapado = mysqli_real_escape_string($conexion, $valor);
                    $updateParts[] = "$columna = '$valorEscapado'";
                }
            }
            
            if (empty($updateParts)) {
                error_log("ADVERTENCIA: No hay campos para actualizar en carga $idCarga");
                continue;
            }
            
            $updateString = implode(', ', $updateParts);
            
            // Construir query con el nombre completo de la base de datos
            error_log("Usando BD_ACADEMICA: " . BD_ACADEMICA);
            $sql = "UPDATE `" . BD_ACADEMICA . "`.`academico_cargas` 
                    SET $updateString 
                    WHERE car_id = '$idCarga' 
                    AND institucion = {$config['conf_id_institucion']} 
                    AND year = {$_SESSION['bd']}";
            
            error_log("SQL generado: $sql");
            
            // Ejecutar la query
            $resultado = mysqli_query($conexion, $sql);
            
            if ($resultado) {
                $filasAfectadas = mysqli_affected_rows($conexion);
                error_log("Query ejecutada exitosamente. Filas afectadas: $filasAfectadas");
                
                if ($filasAfectadas > 0) {
                    $actualizadas++;
                    error_log("✓ Carga $idCarga actualizada correctamente");
                } else {
                    error_log("⚠ Query exitosa pero ninguna fila fue afectada para carga $idCarga (puede que los valores ya fueran los mismos)");
                    $actualizadas++; // Contar como exitosa aunque no haya cambiado nada
                }
            } else {
                $errorMsg = mysqli_error($conexion);
                $errorNum = mysqli_errno($conexion);
                error_log("ERROR al ejecutar query para carga $idCarga");
                error_log("Error #$errorNum: $errorMsg");
                error_log("Query que falló: $sql");
                $errores[] = "Carga ID $idCarga: $errorMsg";
            }

        } catch (Exception $e) {
            $mensaje = "Error en carga ID $idCarga: " . $e->getMessage();
            error_log("EXCEPCIÓN: $mensaje");
            $errores[] = $mensaje;
        }
    }
    
    error_log("Resumen: $actualizadas de " . count($cargas) . " cargas actualizadas");
    if (!empty($errores)) {
        error_log("Errores encontrados: " . print_r($errores, true));
    }

    // Preparar respuesta
    $response = [
        'success' => true,
        'actualizadas' => $actualizadas,
        'total' => count($cargas),
        'campos_actualizados' => array_keys($datosActualizar)
    ];

    // Agregar información de errores si los hay
    if (count($errores) > 0) {
        $response['errores'] = $errores;
        $response['message'] = "Se actualizaron $actualizadas de " . count($cargas) . " cargas. Algunos registros tuvieron errores.";
    } else {
        $response['message'] = "Se actualizaron correctamente $actualizadas cargas académicas.";
    }

    // Si no se actualizó ninguna carga, marcar como error
    if ($actualizadas === 0) {
        $response['success'] = false;
        $response['message'] = "No se pudo actualizar ninguna carga. " . implode(', ', $errores);
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>

