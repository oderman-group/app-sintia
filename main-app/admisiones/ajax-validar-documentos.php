<?php
// Evitar cualquier output antes del JSON
ob_start();

// Capturar errores y warnings
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    include("bd-conexion.php");
    require_once(ROOT_PATH."/main-app/class/Tables/BDT_aspirante.php");
} catch (Exception $e) {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'existe' => false,
        'mensaje' => 'Error al cargar las dependencias: ' . $e->getMessage(),
        'datos' => null
    ]);
    exit;
}

// Limpiar cualquier output que haya ocurrido
ob_clean();

header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'existe' => false,
    'mensaje' => '',
    'datos' => null
];

try {
    if (!isset($_POST['tipo']) || !isset($_POST['documento'])) {
        ob_clean();
        $response['mensaje'] = 'Parámetros incompletos';
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    $tipo = $_POST['tipo']; // 'estudiante' o 'acudiente'
    $documento = trim($_POST['documento']);
    $idInst = isset($_POST['idInst']) ? base64_decode($_POST['idInst']) : (isset($config['conf_id_institucion']) ? $config['conf_id_institucion'] : null);
    $year = isset($_POST['year']) ? $_POST['year'] : (isset($config["cfgi_year_inscripcion"]) ? $config["cfgi_year_inscripcion"] : date("Y"));
    
    if (empty($documento)) {
        ob_clean();
        $response['mensaje'] = 'Documento vacío';
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    // Validar que tenemos las conexiones necesarias
    if (!isset($pdoI) || !$pdoI) {
        ob_clean();
        $response['success'] = false;
        $response['mensaje'] = 'Error: No se pudo establecer conexión con la base de datos';
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    if ($tipo === 'estudiante') {
        // Validar en aspirantes para el año específico de la inscripción
        $estQuery = "SELECT * FROM aspirantes 
                     WHERE asp_documento = :documento 
                     AND asp_institucion = :institucion 
                     AND asp_agno = :year
                     AND asp_oculto = ".BDT_Aspirante::ESTADO_OCULTO_FALSO;
        
        $est = $pdo->prepare($estQuery);
        $est->bindParam(':documento', $documento, PDO::PARAM_STR);
        $est->bindParam(':institucion', $idInst, PDO::PARAM_INT);
        $est->bindParam(':year', $year, PDO::PARAM_INT);
        $est->execute();
        
        if ($est->rowCount() > 0) {
            ob_clean();
            $response['success'] = true;
            $response['existe'] = true;
            $response['mensaje'] = 'Este documento ya tiene una solicitud de admisión registrada para este año';
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
        
        // Función auxiliar para limpiar UTF-8
        function limpiarUTF8Estudiante($texto) {
            if (empty($texto) || !is_string($texto)) {
                return '';
            }
            $textoOriginal = $texto;
            $texto = @iconv('UTF-8', 'UTF-8//IGNORE', $texto);
            if ($texto === false) {
                $texto = mb_convert_encoding($textoOriginal, 'UTF-8', 'UTF-8');
            }
            if (!mb_check_encoding($texto, 'UTF-8')) {
                $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
                $texto = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $texto);
            }
            return trim($texto);
        }
        
        // Buscar en academico_matriculas (cualquier año) para obtener datos
        $matQuery = "SELECT mat_id, mat_nombres, mat_nombre2, mat_primer_apellido, mat_segundo_apellido, 
                            mat_tipo_documento, mat_documento, year as mat_year
                     FROM ".BD_ACADEMICA.".academico_matriculas 
                     WHERE mat_documento = :documento 
                     AND institucion = :institucion 
                     AND mat_eliminado = 0
                     ORDER BY year DESC
                     LIMIT 1";
        
        $mat = $pdoI->prepare($matQuery);
        $mat->bindParam(':documento', $documento, PDO::PARAM_STR);
        $mat->bindParam(':institucion', $idInst, PDO::PARAM_INT);
        $mat->execute();
        
        $datosMatricula = null;
        $existeEnYearInscripcion = false;
        
        if ($mat->rowCount() > 0) {
            $datosMatricula = $mat->fetch(PDO::FETCH_ASSOC);
            // Verificar si existe en el año de inscripción
            if ($datosMatricula['mat_year'] == $year) {
                $existeEnYearInscripcion = true;
            }
        }
        
        // Buscar en usuarios (estudiantes - tipo 4) (cualquier año) para obtener datos
        $usuQuery = "SELECT uss_id, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2, 
                            uss_tipo_documento, uss_documento, year as uss_year
                     FROM ".BD_GENERAL.".usuarios 
                     WHERE uss_documento = :documento 
                     AND uss_tipo = 4
                     AND institucion = :institucion
                     ORDER BY year DESC
                     LIMIT 1";
        
        $usu = $pdoI->prepare($usuQuery);
        $usu->bindParam(':documento', $documento, PDO::PARAM_STR);
        $usu->bindParam(':institucion', $idInst, PDO::PARAM_INT);
        $usu->execute();
        
        $datosUsuario = null;
        $existeUsuarioEnYearInscripcion = false;
        
        if ($usu->rowCount() > 0) {
            $datosUsuario = $usu->fetch(PDO::FETCH_ASSOC);
            // Verificar si existe en el año de inscripción
            if ($datosUsuario['uss_year'] == $year) {
                $existeUsuarioEnYearInscripcion = true;
            }
        }
        
        // Si existe en matrículas o usuarios, devolver los datos
        if ($datosMatricula || $datosUsuario) {
            // Usar datos de matrícula si están disponibles, sino de usuario
            $datos = $datosMatricula ? $datosMatricula : $datosUsuario;
            
            $response['success'] = true;
            $response['existe'] = true;
            
            if ($existeEnYearInscripcion || $existeUsuarioEnYearInscripcion) {
                $response['mensaje'] = 'Este documento ya está registrado para este año. Se cargarán los datos existentes.';
                $response['existe_en_year'] = true;
            } else {
                $response['mensaje'] = 'Este documento está registrado en otro año. Se cargarán los datos y se creará un nuevo registro para este año.';
                $response['existe_en_year'] = false;
            }
            
            // Limpiar y preparar datos
            $response['datos'] = [
                'tipo_documento' => limpiarUTF8Estudiante($datos['mat_tipo_documento'] ?? $datos['uss_tipo_documento'] ?? ''),
                'nombre1' => limpiarUTF8Estudiante($datos['mat_nombres'] ?? $datos['uss_nombre'] ?? ''),
                'nombre2' => limpiarUTF8Estudiante($datos['mat_nombre2'] ?? $datos['uss_nombre2'] ?? ''),
                'apellido1' => limpiarUTF8Estudiante($datos['mat_primer_apellido'] ?? $datos['uss_apellido1'] ?? ''),
                'apellido2' => limpiarUTF8Estudiante($datos['mat_segundo_apellido'] ?? $datos['uss_apellido2'] ?? ''),
                'mat_id' => $datosMatricula ? $datosMatricula['mat_id'] : null,
                'uss_id' => $datosUsuario ? $datosUsuario['uss_id'] : null,
                'year_existente' => $datos['mat_year'] ?? $datos['uss_year'] ?? null
            ];
            
            ob_clean();
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
        
        $response['success'] = true;
        $response['existe'] = false;
        $response['mensaje'] = 'Documento disponible';
        
    } elseif ($tipo === 'acudiente') {
        // Validar en usuarios (cualquier tipo primero para verificar existencia)
        // Buscar el documento en cualquier tipo de usuario de la institución
        $buscarQuery = "SELECT uss_id, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2, 
                               uss_email, uss_celular, uss_tipo_documento, uss_documento, uss_tipo
                        FROM ".BD_GENERAL.".usuarios 
                        WHERE uss_documento = :documento 
                        AND institucion = :institucion 
                        ORDER BY year DESC, uss_tipo ASC
                        LIMIT 1";
        
        try {
            $buscar = $pdoI->prepare($buscarQuery);
            $buscar->bindParam(':documento', $documento, PDO::PARAM_STR);
            $buscar->bindParam(':institucion', $idInst, PDO::PARAM_INT);
            $buscar->execute();
            
            $rowCount = $buscar->rowCount();
            
            // Log para debug
            error_log("DEBUG acudiente - Documento: $documento, Institución: $idInst, Resultados: $rowCount");
            
            if ($rowCount > 0) {
                try {
                    $datosUsuario = $buscar->fetch(PDO::FETCH_ASSOC);
                    
                    error_log("DEBUG - Después de fetch, tipo: " . gettype($datosUsuario));
                    
                    // Validar que fetch devolvió datos
                    if ($datosUsuario === false || !is_array($datosUsuario)) {
                        error_log("ERROR - fetch devolvió false o no es array");
                        ob_clean();
                        $response['success'] = false;
                        $response['existe'] = false;
                        $response['mensaje'] = 'Error al obtener los datos del usuario';
                        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        exit;
                    }
                    
                    // Log para debug - verificar cada campo individualmente
                    error_log("DEBUG acudiente - Tipo de dato: " . gettype($datosUsuario));
                    error_log("DEBUG acudiente - Es array: " . (is_array($datosUsuario) ? 'SI' : 'NO'));
                    error_log("DEBUG acudiente - Keys del array: " . implode(', ', array_keys($datosUsuario)));
                    
                    // Función auxiliar para limpiar UTF-8
                    function limpiarUTF8($texto) {
                        if (empty($texto) || !is_string($texto)) {
                            return '';
                        }
                        // Guardar el texto original
                        $textoOriginal = $texto;
                        // Primero intentar con iconv para eliminar caracteres inválidos
                        $texto = @iconv('UTF-8', 'UTF-8//IGNORE', $texto);
                        // Si iconv falla, usar mb_convert_encoding con el texto original
                        if ($texto === false) {
                            $texto = mb_convert_encoding($textoOriginal, 'UTF-8', 'UTF-8');
                        }
                        // Verificar que sea UTF-8 válido
                        if (!mb_check_encoding($texto, 'UTF-8')) {
                            // Forzar conversión eliminando caracteres inválidos
                            $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
                            $texto = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $texto);
                        }
                        return trim($texto);
                    }
                    
                    // Obtener valores de forma segura con verificación de existencia y limpieza UTF-8
                    $uss_id = '';
                    $uss_tipo = 0;
                    $uss_nombre = '';
                    $uss_nombre2 = '';
                    $uss_apellido1 = '';
                    $uss_apellido2 = '';
                    $uss_email = '';
                    $uss_celular = '';
                    $uss_tipo_documento = '';
                    
                    if (array_key_exists('uss_id', $datosUsuario)) {
                        $uss_id = limpiarUTF8((string)$datosUsuario['uss_id']);
                    }
                    if (array_key_exists('uss_tipo', $datosUsuario)) {
                        $uss_tipo = (int)$datosUsuario['uss_tipo'];
                    }
                    if (array_key_exists('uss_nombre', $datosUsuario)) {
                        $uss_nombre = limpiarUTF8((string)$datosUsuario['uss_nombre']);
                    }
                    if (array_key_exists('uss_nombre2', $datosUsuario)) {
                        $uss_nombre2 = limpiarUTF8((string)$datosUsuario['uss_nombre2']);
                    }
                    if (array_key_exists('uss_apellido1', $datosUsuario)) {
                        $uss_apellido1 = limpiarUTF8((string)$datosUsuario['uss_apellido1']);
                    }
                    if (array_key_exists('uss_apellido2', $datosUsuario)) {
                        $uss_apellido2 = limpiarUTF8((string)$datosUsuario['uss_apellido2']);
                    }
                    if (array_key_exists('uss_email', $datosUsuario)) {
                        $uss_email = limpiarUTF8((string)$datosUsuario['uss_email']);
                    }
                    if (array_key_exists('uss_celular', $datosUsuario)) {
                        $uss_celular = limpiarUTF8((string)$datosUsuario['uss_celular']);
                    }
                    if (array_key_exists('uss_tipo_documento', $datosUsuario)) {
                        $uss_tipo_documento = limpiarUTF8((string)$datosUsuario['uss_tipo_documento']);
                    }
                    
                    error_log("DEBUG - Valores extraídos - uss_id: $uss_id, uss_tipo: $uss_tipo, nombre: $uss_nombre");
                    
                    // Construir array de datos
                    $datosArray = [
                        'uss_id' => $uss_id,
                        'nombre1' => $uss_nombre,
                        'nombre2' => $uss_nombre2,
                        'apellido1' => $uss_apellido1,
                        'apellido2' => $uss_apellido2,
                        'email' => $uss_email,
                        'celular' => $uss_celular,
                        'tipo_documento' => $uss_tipo_documento
                    ];
                    
                    // Verificar json_encode del array de datos
                    $jsonDatos = json_encode($datosArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $jsonError = json_last_error();
                    if ($jsonError !== JSON_ERROR_NONE) {
                        error_log("ERROR json_encode datosArray - Código: $jsonError, Mensaje: " . json_last_error_msg());
                    } else {
                        error_log("DEBUG - Array de datos construido (longitud: " . strlen($jsonDatos) . "): " . substr($jsonDatos, 0, 200));
                    }
                    
                    // Si es acudiente (tipo 3), cargar los datos
                    if ($uss_tipo == 3) {
                        $response['success'] = true;
                        $response['existe'] = true;
                        $response['mensaje'] = 'Este documento ya está registrado. Se cargarán los datos del acudiente.';
                        $response['datos'] = $datosArray;
                    } else {
                        // El documento existe pero no es acudiente, aún así lo tratamos como existente
                        $response['success'] = true;
                        $response['existe'] = true;
                        $response['mensaje'] = 'Este documento ya está registrado en el sistema. Se cargarán los datos disponibles.';
                        $response['datos'] = $datosArray;
                    }
                    
                    // Verificar json_encode de la respuesta
                    $jsonResponse = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $jsonError = json_last_error();
                    if ($jsonError !== JSON_ERROR_NONE) {
                        error_log("ERROR json_encode response - Código: $jsonError, Mensaje: " . json_last_error_msg());
                        error_log("DEBUG - Response array: " . print_r($response, true));
                    } else {
                        error_log("DEBUG - Respuesta final construida (longitud: " . strlen($jsonResponse) . "): " . substr($jsonResponse, 0, 300));
                    }
                    
                    // Limpiar cualquier output antes de enviar JSON
                    ob_clean();
                    
                    // Verificar que no haya output antes
                    $outputBefore = ob_get_contents();
                    if (!empty($outputBefore)) {
                        error_log("WARNING - Hay output antes del JSON: " . substr($outputBefore, 0, 200));
                        ob_clean();
                    }
                    
                    echo $jsonResponse;
                    exit;
                    
                } catch (Exception $e) {
                    error_log("ERROR al procesar datos del usuario: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                    ob_clean();
                    $response['success'] = false;
                    $response['existe'] = false;
                    $response['mensaje'] = 'Error al procesar los datos: ' . $e->getMessage();
                    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
            } else {
                $response['success'] = true;
                $response['existe'] = false;
                $response['mensaje'] = 'Documento disponible';
            }
        } catch (PDOException $e) {
            error_log("ERROR en consulta acudiente: " . $e->getMessage());
            $response['success'] = false;
            $response['existe'] = false;
            $response['mensaje'] = 'Error al consultar la base de datos: ' . $e->getMessage();
        }
    } else {
        $response['mensaje'] = 'Tipo de validación inválido';
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['existe'] = false;
    $response['mensaje'] = 'Error en la validación: ' . $e->getMessage();
    error_log('Error en ajax-validar-documentos.php: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
}

// Limpiar cualquier output antes de enviar JSON
ob_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

