<?php
// Suprimir warnings de PHP para evitar que contaminen la respuesta JSON
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

// Limpiar cualquier output previo
ob_clean();
ob_start();

include("session.php");
require_once("../class/Usuarios.php");
require_once("../class/Estudiantes.php");
require '../../librerias/Excel/vendor/autoload.php';
require_once("../class/Sysjobs.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
Modulos::validarAccesoDirectoPaginas();

// Función para detectar si es una petición AJAX
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// Función para respuesta JSON
function jsonResponse($data) {
    // Limpiar cualquier output previo
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Limpiar headers previos
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
    }
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Función para validar archivo Excel
function validateExcelFile($file) {
    $errors = [];
    
    // Validar que se subió un archivo
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Error al subir el archivo";
        return $errors;
    }
    
    // Validar extensión
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (strtolower($extension) !== 'xlsx') {
        $errors[] = "Solo se permiten archivos Excel (.xlsx)";
        return $errors;
    }
    
    // Validar tamaño (máximo 10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        $errors[] = "El archivo es demasiado grande. Máximo 10MB permitido";
        return $errors;
    }
    
    // Validar que el archivo se puede leer
    try {
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        
        if ($highestRow < 3) {
            $errors[] = "El archivo no contiene datos válidos (mínimo 3 filas: fila 1 vacía + fila 2 cabeceras + datos)";
            return $errors;
        }
        
        // Validar cabeceras requeridas
        $requiredHeaders = [
            'Documento',
            'Primer Nombre', 
            'Primer Apellido',
            'Grado'
        ];
        
        $headers = [];
        for ($col = 1; $col <= 20; $col++) { // Revisar primeras 20 columnas
            $cellValue = $worksheet->getCellByColumnAndRow($col, 2)->getValue(); // Leer desde fila 2
            if ($cellValue) {
                $headers[] = trim($cellValue);
            }
        }
        
        $missingHeaders = [];
        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headers)) {
                $missingHeaders[] = $required;
            }
        }
        
        if (!empty($missingHeaders)) {
            $errors[] = "Faltan las siguientes cabeceras requeridas: " . implode(', ', $missingHeaders);
            return $errors;
        }
        
    } catch (Exception $e) {
        $errors[] = "Error al leer el archivo Excel: " . $e->getMessage();
        return $errors;
    }
    
    return $errors;
}

/**
 * Mapeo de tipos de documento desde Excel a códigos de opciones_generales
 * Códigos en BD_ADMIN.opciones_generales del 105 al 110
 */
function mapearTipoDocumento($iniciales) {
    $tiposDocumento = [
        'RC' => '108',    // Registro civil
        'TI' => '107',    // Tarjeta de identidad
        'CC' => '105',    // Cédula de ciudadanía
        'CE' => '109',    // Cédula de extranjería
        'PP' => '110',    // Pasaporte
        'PE' => '139',    // Pasaporte (alternativa)
        'NUIP' => '106'   // NUIP
    ];
    
    $inicialesOriginal = $iniciales;
    $iniciales = strtoupper(trim($iniciales));
    
    // Si ya es un código numérico, devolverlo
    if (is_numeric($iniciales) && intval($iniciales) >= 105 && intval($iniciales) <= 110) {
        error_log("Mapeo Tipo Doc: '$inicialesOriginal' ya es código -> mantiene '$iniciales'");
        return $iniciales;
    }
    
    // Mapear iniciales a código
    $codigo = isset($tiposDocumento[$iniciales]) ? $tiposDocumento[$iniciales] : '105';
    error_log("Mapeo Tipo Doc: '$inicialesOriginal' -> '$codigo'");
    
    return $codigo;
}

/**
 * Mapeo de estratos desde Excel a códigos de opciones_generales
 * Códigos en BD_ADMIN.opciones_generales del 114 al 125
 */
function mapearEstrato($numero) {
    $estratos = [
        '1' => '114',
        '2' => '115',
        '3' => '116',
        '4' => '117',
        '5' => '118',
        '6' => '119',
        '7' => '120',
        '8' => '121',
        '9' => '122',
        '10' => '123',
        '11' => '124',
        '12' => '125'
    ];
    
    $numeroOriginal = $numero;
    $numero = trim($numero);
    
    // Si está vacío, devolver null para que se inserte NULL en BD
    if (empty($numero)) {
        error_log("Mapeo Estrato: vacío -> null");
        return null;
    }
    
    // Si ya es un código numérico entre 114-125, devolverlo
    if (is_numeric($numero) && intval($numero) >= 114 && intval($numero) <= 125) {
        error_log("Mapeo Estrato: '$numeroOriginal' ya es código -> mantiene '$numero'");
        return $numero;
    }
    
    // Mapear número a código
    $codigo = isset($estratos[$numero]) ? $estratos[$numero] : null;
    if ($codigo !== null) {
        error_log("Mapeo Estrato: '$numeroOriginal' -> '$codigo'");
    } else {
        error_log("Mapeo Estrato: '$numeroOriginal' -> sin mapeo (null)");
    }
    
    return $codigo;
}

/**
 * Mapeo de géneros desde Excel a códigos de opciones_generales
 * Códigos en BD_ADMIN.opciones_generales: 126 (Masculino), 127 (Femenino)
 */
function mapearGenero($inicial) {
    $generos = [
        'M' => '126',    // Masculino
        'F' => '127'     // Femenino
    ];
    
    $inicialOriginal = $inicial;
    $inicial = strtoupper(trim($inicial));
    
    // Si está vacío, devolver null para que se inserte NULL en BD
    if (empty($inicial)) {
        error_log("Mapeo Género: vacío -> null");
        return null;
    }
    
    // Si ya es un código numérico (126 o 127), devolverlo
    if (is_numeric($inicial) && (intval($inicial) == 126 || intval($inicial) == 127)) {
        error_log("Mapeo Género: '$inicialOriginal' ya es código -> mantiene '$inicial'");
        return $inicial;
    }
    
    // Mapear inicial a código
    $codigo = isset($generos[$inicial]) ? $generos[$inicial] : null;
    if ($codigo !== null) {
        error_log("Mapeo Género: '$inicialOriginal' -> '$codigo'");
    } else {
        error_log("Mapeo Género: '$inicialOriginal' -> sin mapeo (null)");
    }
    
    return $codigo;
}

// Función para limpiar datos de Excel
function cleanExcelData($data) {
    // Manejar valores null
    if ($data === null) {
        return '';
    }
    
    if (is_string($data)) {
        // Remover TODOS los caracteres de control y espacios problemáticos de forma más agresiva
        $data = preg_replace('/[\x00-\x1F\x7F]/', '', $data); // Remover todos los caracteres de control
        
        // Remover caracteres específicos que causan problemas
        $data = str_replace(["\r\n", "\r", "\n", "\t", "\v", "\f"], '', $data);
        
        // Limpiar espacios múltiples
        $data = preg_replace('/\s+/', ' ', $data);
        
        // Limpiar caracteres especiales que pueden causar problemas en SQL
        $data = str_replace(['"', "'", '`', '\\'], '', $data);
        
        // Limpieza adicional para caracteres problemáticos
        $data = preg_replace('/[^\x20-\x7E]/', '', $data); // Solo caracteres ASCII imprimibles
        
        return trim($data);
    }
    return $data;
}

// Función para limpieza agresiva de datos para SQL
function sanitizeForSQL($data) {
    // Manejar valores null
    if ($data === null) {
        return '';
    }
    
    if (is_string($data)) {
        // Remover TODOS los caracteres de control y espacios problemáticos
        $data = preg_replace('/[\x00-\x1F\x7F]/', '', $data);
        
        // Remover caracteres específicos que causan problemas
        $data = str_replace(["\r\n", "\r", "\n", "\t", "\v", "\f"], '', $data);
        
        // Limpiar espacios múltiples
        $data = preg_replace('/\s+/', ' ', $data);
        
        // Escapar caracteres especiales para SQL
        $data = addslashes($data);
        
        return trim($data);
    }
    return $data;
}

// Función para debug de datos
function debugData($data, $label = '') {
    if ($label) {
        error_log("DEBUG $label: " . json_encode($data));
    }
    
    // Verificar si hay caracteres problemáticos en arrays
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $problematicChars = ["\r\n", "\r", "\n", "\t", "\v", "\f"];
                foreach ($problematicChars as $char) {
                    if (strpos($value, $char) !== false) {
                        error_log("PROBLEMA ENCONTRADO en '$label' campo '$key': Carácter problemático " . json_encode($char) . " en valor: " . json_encode($value));
                    }
                }
                
                // Verificar caracteres de control
                if (preg_match('/[\x00-\x1F\x7F]/', $value)) {
                    error_log("CARACTERE DE CONTROL ENCONTRADO en '$label' campo '$key': " . json_encode($value));
                }
            }
        }
    }
    
    // Verificar si hay caracteres problemáticos en strings
    if (is_string($data)) {
        $problematicChars = ["\r\n", "\r", "\n", "\t", "\v", "\f"];
        foreach ($problematicChars as $char) {
            if (strpos($data, $char) !== false) {
                error_log("PROBLEMA ENCONTRADO en '$label': Carácter problemático encontrado: " . json_encode($char));
                error_log("Datos originales: " . json_encode($data));
            }
        }
    }
}

// Función para convertir y validar fecha de Excel
function convertAndValidateExcelDate($fecha) {
    if (empty($fecha)) {
        return ['valid' => true, 'date' => null]; // Fecha vacía es válida (campo opcional)
    }
    
    // Si es un número (fecha serial de Excel)
    if (is_numeric($fecha)) {
        try {
            // Convertir número serial de Excel a fecha
            $excelEpoch = new DateTime('1900-01-01');
            $excelEpoch->add(new DateInterval('P' . (intval($fecha) - 2) . 'D'));
            
            return ['valid' => true, 'date' => $excelEpoch->format('Y-m-d')];
        } catch (Exception $e) {
            return ['valid' => false, 'date' => null, 'error' => 'Fecha serial inválida'];
        }
    }
    
    // Si es una fecha en formato string, validar formatos comunes
    $formatos = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'm/d/Y', 'm-d-Y'];
    
    foreach ($formatos as $formato) {
        $date = DateTime::createFromFormat($formato, $fecha);
        if ($date && $date->format($formato) === $fecha) {
            return ['valid' => true, 'date' => $date->format('Y-m-d')];
        }
    }
    
    return ['valid' => false, 'date' => null, 'error' => 'Formato de fecha no reconocido'];
}

// Función para procesar archivo inmediatamente
function processExcelImmediately($file, $actualizarCampo) {
    try {
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $results = [
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'errorDetails' => []
        ];
        
        // Obtener cabeceras
        $headers = [];
        for ($col = 1; $col <= 20; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, 2)->getValue(); // Leer desde fila 2
            if ($cellValue) {
                $headers[$col] = trim($cellValue);
            }
        }
        
        // Procesar filas (desde la fila 3) - detectar automáticamente filas válidas
        $maxRow = $worksheet->getHighestRow();
        
        // Primero detectar todas las filas válidas (con campos obligatorios B, C, E)
        $validRows = [];
        for ($row = 3; $row <= $maxRow; $row++) {
            // Leer campos obligatorios por índice de columna para evitar conflictos
            $documento = cleanExcelData(trim($worksheet->getCellByColumnAndRow(2, $row)->getValue() ?? '')); // Columna B
            $primerNombre = cleanExcelData(trim($worksheet->getCellByColumnAndRow(3, $row)->getValue() ?? '')); // Columna C
            $primerApellido = cleanExcelData(trim($worksheet->getCellByColumnAndRow(5, $row)->getValue() ?? '')); // Columna E
            
            // Una fila es válida si tiene los campos obligatorios: B, C, E
            if (!empty($documento) && !empty($primerNombre) && !empty($primerApellido)) {
                $validRows[] = $row;
            }
        }
        
        // Procesar solo las filas válidas detectadas
        foreach ($validRows as $row) {
            try {
                $rowData = [];
                
                // Mapear datos por índice de columna para evitar conflictos con nombres duplicados
                $rowData['Tipo de Documento'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(1, $row)->getValue() ?? '')); // Columna A
                $rowData['Documento'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(2, $row)->getValue() ?? '')); // Columna B - ESTUDIANTE
                $rowData['Primer Nombre'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(3, $row)->getValue() ?? '')); // Columna C
                $rowData['Segundo Nombre'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(4, $row)->getValue() ?? '')); // Columna D
                $rowData['Primer Apellido'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(5, $row)->getValue() ?? '')); // Columna E
                $rowData['Segundo Apellido'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(6, $row)->getValue() ?? '')); // Columna F
                $rowData['Genero'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(7, $row)->getValue() ?? '')); // Columna G
                $rowData['Fecha Nacimiento'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(8, $row)->getValue() ?? '')); // Columna H
                $rowData['Grado'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(9, $row)->getValue() ?? '')); // Columna I
                $rowData['Grupo'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(10, $row)->getValue() ?? '')); // Columna J
                $rowData['Direccion'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(11, $row)->getValue() ?? '')); // Columna K
                $rowData['Barrio'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(12, $row)->getValue() ?? '')); // Columna L
                $rowData['Celular'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(13, $row)->getValue() ?? '')); // Columna M
                $rowData['Email'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(14, $row)->getValue() ?? '')); // Columna N
                $rowData['Estrato'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(15, $row)->getValue() ?? '')); // Columna O
                $rowData['Grupo Sanguineo'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(16, $row)->getValue() ?? '')); // Columna P
                $rowData['EPS'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(17, $row)->getValue() ?? '')); // Columna Q
                
                // Datos del acudiente (opcional) - Columnas R a V
                $rowData['Documento Acudiente'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(18, $row)->getValue() ?? '')); // Columna R
                $rowData['Nombre Acudiente'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(19, $row)->getValue() ?? '')); // Columna S
                $rowData['Apellido Acudiente'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(20, $row)->getValue() ?? '')); // Columna T
                $rowData['Celular Acudiente'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(21, $row)->getValue() ?? '')); // Columna U
                $rowData['Email Acudiente'] = cleanExcelData(trim($worksheet->getCellByColumnAndRow(22, $row)->getValue() ?? '')); // Columna V
                
                // Validar campos obligatorios con mensajes específicos
                $erroresValidacion = [];
                
                // Columna A: Tipo de Documento (opcional pero recomendado)
                // Columna B: Documento (OBLIGATORIO) - ESTUDIANTE
                if (empty($rowData['Documento'])) {
                    $erroresValidacion[] = "Falta el número de documento del estudiante (columna B)";
                }
                
                // Columna C: Primer Nombre (OBLIGATORIO)
                if (empty($rowData['Primer Nombre'])) {
                    $erroresValidacion[] = "Falta el primer nombre (columna C)";
                }
                
                // Columna E: Primer Apellido (OBLIGATORIO)  
                if (empty($rowData['Primer Apellido'])) {
                    $erroresValidacion[] = "Falta el primer apellido (columna E)";
                }
                
                // Columna I: Grado (OBLIGATORIO)
                if (empty($rowData['Grado'])) {
                    $erroresValidacion[] = "Falta el grado (columna I)";
                }
                
                // Si hay errores de validación, reportar y continuar
                if (!empty($erroresValidacion)) {
                    $results['errors']++;
                    $results['errorDetails'][] = "Fila $row: " . implode(", ", $erroresValidacion);
                    continue;
                }
                
                // Validar formato de fecha si existe (Columna H) - OPCIONAL
                $fechaNacimiento = null;
                if (!empty($rowData['Fecha Nacimiento'])) {
                    $fechaResult = convertAndValidateExcelDate($rowData['Fecha Nacimiento']);
                    if ($fechaResult['valid']) {
                        $fechaNacimiento = $fechaResult['date'];
                    } else {
                        // Solo registrar advertencia, no impedir creación
                        $results['errorDetails'][] = "Fila $row: Advertencia - Fecha de nacimiento inválida (columna H): " . $fechaResult['error'] . ". Se omitirá este dato.";
                    }
                }
                
                // Verificar si el estudiante ya existe
                $documento = $rowData['Documento'];
                $existeEstudiante = Estudiantes::validarExistenciaEstudiante($documento);
                
                if ($existeEstudiante > 0) {
                    // Actualizar estudiante existente usando función segura
                    $resultadoActualizacion = updateExistingStudentSafe($documento, $rowData, $actualizarCampo, $fechaNacimiento);
                    if ($resultadoActualizacion === true) {
                        $results['updated']++;
                    } else {
                        $results['errors']++;
                        $results['errorDetails'][] = "Fila $row: Error al actualizar - " . $resultadoActualizacion;
                    }
                } else {
                    // Crear nuevo estudiante usando función segura
                    $resultadoCreacion = createNewStudentSafe($rowData, $fechaNacimiento);
                    if ($resultadoCreacion === true) {
                        $results['created']++;
                    } else {
                        $results['errors']++;
                        $results['errorDetails'][] = "Fila $row: Error al crear - " . $resultadoCreacion;
                    }
                }
                
            } catch (Exception $e) {
                $results['errors']++;
                $results['errorDetails'][] = "Fila $row: " . $e->getMessage();
            }
        }
        
        return $results;
        
    } catch (Exception $e) {
        throw new Exception("Error al procesar el archivo: " . $e->getMessage());
    }
}

// Función para crear nuevo estudiante con consulta SQL directa y segura
function createNewStudentSafe($rowData, $fechaNacimiento = null) {
    try {
        global $conexionPDO, $config;
        
        // Generar código de matrícula usando la función original
        $codigoMAT = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_matriculas');
        
        // Preparar datos con limpieza extrema y conversión de códigos
        $datosLimpios = [
            'tipoD' => mapearTipoDocumento(cleanExcelData($rowData['Tipo de Documento'] ?? 'CC')),
            'nDoc' => cleanExcelData($rowData['Documento']),
            'nombres' => cleanExcelData($rowData['Primer Nombre']),
            'apellido1' => cleanExcelData($rowData['Primer Apellido']),
            'nombre2' => cleanExcelData($rowData['Segundo Nombre'] ?? ''),
            'apellido2' => cleanExcelData($rowData['Segundo Apellido'] ?? ''),
            'grado' => cleanExcelData($rowData['Grado']),
            'grupo' => intval(cleanExcelData($rowData['Grupo'] ?? '1')),
            'fNac' => $fechaNacimiento,
            'genero' => mapearGenero(cleanExcelData($rowData['Genero'] ?? '')),  // Retorna null si está vacío
            'direccion' => cleanExcelData($rowData['Direccion'] ?? ''),
            'barrio' => cleanExcelData($rowData['Barrio'] ?? ''),
            'celular' => cleanExcelData($rowData['Celular'] ?? ''),
            'email' => cleanExcelData($rowData['Email'] ?? ''),
            'estrato' => mapearEstrato(cleanExcelData($rowData['Estrato'] ?? '')),  // Retorna null si está vacío
            'tipoSangre' => cleanExcelData($rowData['Grupo Sanguineo'] ?? ''),
            'eps' => cleanExcelData($rowData['EPS'] ?? '')
        ];
        
        // Verificar que no haya caracteres problemáticos
        foreach ($datosLimpios as $key => $value) {
            if (is_string($value) && preg_match('/[\r\n]/', $value)) {
                error_log("CARACTERE PROBLEMÁTICO DETECTADO en $key: " . json_encode($value));
                $datosLimpios[$key] = preg_replace('/[\r\n]/', '', $value);
            }
        }
        
        // Consulta SQL completamente segura
        $consulta = "INSERT INTO " . BD_ACADEMICA . ".academico_matriculas (
            mat_id, 
            mat_matricula, 
            mat_fecha, 
            mat_tipo_documento, 
            mat_documento, 
            mat_nombres, 
            mat_primer_apellido, 
            mat_segundo_apellido, 
            mat_nombre2,
            mat_grado, 
            mat_grupo, 
            mat_fecha_nacimiento, 
            mat_genero, 
            mat_direccion, 
            mat_barrio, 
            mat_celular, 
            mat_email, 
            mat_estrato, 
            mat_tipo_sangre, 
            mat_eps,
            mat_estado_agno, 
            institucion, 
            year, 
            mat_forma_creacion
        ) VALUES (
            :mat_id,
            :mat_matricula,
            NOW(),
            :tipoD,
            :nDoc,
            :nombres,
            :apellido1,
            :apellido2,
            :nombre2,
            :grado,
            :grupo,
            :fNac,
            :genero,
            :direccion,
            :barrio,
            :celular,
            :email,
            :estrato,
            :tipoSangre,
            :eps,
            3,
            :institucion,
            :year,
            :formaCreacion
        )";
        
        $stmt = $conexionPDO->prepare($consulta);
        
        // Bind parameters con variables para evitar problemas de referencia
        $matId = $codigoMAT;
        $matricula = '';
        $tipoD = $datosLimpios['tipoD'];
        $nDoc = $datosLimpios['nDoc'];
        $nombres = $datosLimpios['nombres'];
        $apellido1 = $datosLimpios['apellido1'];
        $apellido2 = $datosLimpios['apellido2'];
        $nombre2 = $datosLimpios['nombre2'];
        $grado = $datosLimpios['grado'];
        $grupo = $datosLimpios['grupo'];
        $fNac = $fechaNacimiento;
        $genero = $datosLimpios['genero'];
        $direccion = $datosLimpios['direccion'];
        $barrio = $datosLimpios['barrio'];
        $celular = $datosLimpios['celular'];
        $email = $datosLimpios['email'];
        $estrato = $datosLimpios['estrato'];
        $tipoSangre = $datosLimpios['tipoSangre'];
        $eps = $datosLimpios['eps'];
        $institucion = $config['conf_id_institucion'];
        $year = $config['conf_agno'];
        $formaCreacion = Estudiantes::IMPORTAR_EXCEL;
        
        $stmt->bindParam(':mat_id', $matId, PDO::PARAM_STR);
        $stmt->bindParam(':mat_matricula', $matricula, PDO::PARAM_STR);
        $stmt->bindParam(':tipoD', $tipoD, PDO::PARAM_STR);
        $stmt->bindParam(':nDoc', $nDoc, PDO::PARAM_STR);
        $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
        $stmt->bindParam(':apellido1', $apellido1, PDO::PARAM_STR);
        $stmt->bindParam(':apellido2', $apellido2, PDO::PARAM_STR);
        $stmt->bindParam(':nombre2', $nombre2, PDO::PARAM_STR);
        $stmt->bindParam(':grado', $grado, PDO::PARAM_STR);
        $stmt->bindParam(':grupo', $grupo, PDO::PARAM_INT);
        
        // Fecha de nacimiento puede ser NULL
        if ($fNac === null) {
            $stmt->bindParam(':fNac', $fNac, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':fNac', $fNac, PDO::PARAM_STR);
        }
        
        // Género puede ser NULL (si no viene en Excel o no se mapea)
        if ($genero === null) {
            $stmt->bindParam(':genero', $genero, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':genero', $genero, PDO::PARAM_INT);
        }
        
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':barrio', $barrio, PDO::PARAM_STR);
        $stmt->bindParam(':celular', $celular, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        
        // Estrato puede ser NULL (si no viene en Excel o no se mapea)
        if ($estrato === null) {
            $stmt->bindParam(':estrato', $estrato, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':estrato', $estrato, PDO::PARAM_INT);
        }
        
        $stmt->bindParam(':tipoSangre', $tipoSangre, PDO::PARAM_STR);
        $stmt->bindParam(':eps', $eps, PDO::PARAM_STR);
        $stmt->bindParam(':institucion', $institucion, PDO::PARAM_STR);
        $stmt->bindParam(':year', $year, PDO::PARAM_STR);
        $stmt->bindParam(':formaCreacion', $formaCreacion, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return true;
        } else {
            $errorInfo = $stmt->errorInfo();
            return "Error SQL: " . $errorInfo[2];
        }
        
    } catch (Exception $e) {
        return "Excepción: " . $e->getMessage();
    }
}

// Función para actualizar estudiante existente con consulta SQL directa y segura
function updateExistingStudentSafe($documento, $rowData, $actualizarCampo, $fechaNacimiento = null) {
    try {
        global $conexionPDO, $config;
        
        // Debug: Verificar parámetros recibidos
        error_log("DEBUG updateExistingStudentSafe - documento: $documento");
        error_log("DEBUG updateExistingStudentSafe - actualizarCampo: " . json_encode($actualizarCampo));
        error_log("DEBUG updateExistingStudentSafe - rowData: " . json_encode($rowData));
        
        if (empty($actualizarCampo)) {
            error_log("DEBUG updateExistingStudentSafe - ERROR: No hay campos seleccionados para actualizar");
            return "No hay campos seleccionados para actualizar";
        }
        
        // Preparar campos para actualizar
        $camposUpdate = [];
        $valores = [];
        
        foreach ($actualizarCampo as $campo) {
            switch ($campo) {
                case '1': // Grado
                    if (isset($rowData['Grado'])) {
                        $camposUpdate[] = "mat_grado = :grado";
                        $valores['grado'] = cleanExcelData($rowData['Grado']);
                    }
                    break;
                case '2': // Grupo
                    if (isset($rowData['Grupo'])) {
                        $camposUpdate[] = "mat_grupo = :grupo";
                        $valores['grupo'] = intval(cleanExcelData($rowData['Grupo']));
                    }
                    break;
                case '3': // Tipo de Documento
                    if (isset($rowData['Tipo de Documento'])) {
                        $camposUpdate[] = "mat_tipo_documento = :tipoD";
                        // Aplicar mapeo de tipo de documento
                        $valores['tipoD'] = mapearTipoDocumento(cleanExcelData($rowData['Tipo de Documento']));
                    }
                    break;
                case '5': // Segundo nombre del estudiante
                    if (isset($rowData['Segundo Nombre'])) {
                        $camposUpdate[] = "mat_nombre2 = :nombre2";
                        $valores['nombre2'] = cleanExcelData($rowData['Segundo Nombre']);
                    }
                    break;
                case '6': // Fecha de nacimiento
                    if ($fechaNacimiento) {
                        $camposUpdate[] = "mat_fecha_nacimiento = :fNac";
                        $valores['fNac'] = $fechaNacimiento;
                    }
                    break;
            }
        }
        
        if (empty($camposUpdate)) {
            error_log("DEBUG updateExistingStudentSafe - ERROR: No se prepararon campos para actualizar");
            return "No se prepararon campos para actualizar";
        }
        
        // Construir consulta SQL - usar la misma lógica que validarExistenciaEstudiante
        $consulta = "UPDATE " . BD_ACADEMICA . ".academico_matriculas SET " . 
                   implode(", ", $camposUpdate) . 
                   " WHERE (mat_documento = :documento OR mat_documento = :documentoSinPuntos) AND mat_eliminado = 0 AND institucion = :institucion AND year = :year";
        
        // Preparar documento con y sin puntos (como hace validarExistenciaEstudiante)
        $documentoSinPuntos = strpos($documento, '.') !== false ? str_replace('.', '', $documento) : $documento;
        
        error_log("DEBUG updateExistingStudentSafe - Consulta SQL: " . $consulta);
        error_log("DEBUG updateExistingStudentSafe - Valores: " . json_encode($valores));
        error_log("DEBUG updateExistingStudentSafe - Documento original: $documento");
        error_log("DEBUG updateExistingStudentSafe - Documento sin puntos: $documentoSinPuntos");
        error_log("DEBUG updateExistingStudentSafe - Institución: " . $config['conf_id_institucion']);
        error_log("DEBUG updateExistingStudentSafe - Año: " . $config['conf_agno']);
        
        // Debug: Verificar si el estudiante existe antes de actualizar
        try {
            $consultaVerificacion = "SELECT mat_id, mat_documento, mat_nombres, mat_primer_apellido, mat_tipo_documento, mat_nombre2, institucion, year, mat_eliminado FROM " . BD_ACADEMICA . ".academico_matriculas WHERE (mat_documento = :documento OR mat_documento = :documentoSinPuntos)";
            $stmtVerificacion = $conexionPDO->prepare($consultaVerificacion);
            $stmtVerificacion->bindParam(':documento', $documento, PDO::PARAM_STR);
            $stmtVerificacion->bindParam(':documentoSinPuntos', $documentoSinPuntos, PDO::PARAM_STR);
            $stmtVerificacion->execute();
            $resultadosVerificacion = $stmtVerificacion->fetchAll(PDO::FETCH_ASSOC);
            error_log("DEBUG updateExistingStudentSafe - Estudiantes encontrados con documento $documento: " . json_encode($resultadosVerificacion));
            
            // Debug: Verificar valores actuales vs nuevos
            if (!empty($resultadosVerificacion)) {
                $estudianteActual = $resultadosVerificacion[0];
                error_log("DEBUG updateExistingStudentSafe - Valores actuales del estudiante:");
                error_log("DEBUG updateExistingStudentSafe - mat_tipo_documento actual: '" . ($estudianteActual['mat_tipo_documento'] ?? 'NULL') . "'");
                error_log("DEBUG updateExistingStudentSafe - mat_nombre2 actual: '" . ($estudianteActual['mat_nombre2'] ?? 'NULL') . "'");
                error_log("DEBUG updateExistingStudentSafe - Valores nuevos a actualizar:");
                foreach ($valores as $key => $value) {
                    error_log("DEBUG updateExistingStudentSafe - $key nuevo: '$value'");
                }
            }
        } catch (Exception $e) {
            error_log("DEBUG updateExistingStudentSafe - Error en consulta de verificación: " . $e->getMessage());
        }
        
        $stmt = $conexionPDO->prepare($consulta);
        
        // Bind parameters
        foreach ($valores as $key => $value) {
            $stmt->bindParam(":$key", $value, PDO::PARAM_STR);
        }
        
        $stmt->bindParam(':documento', $documento, PDO::PARAM_STR);
        $stmt->bindParam(':documentoSinPuntos', $documentoSinPuntos, PDO::PARAM_STR);
        $institucionInt = intval($config['conf_id_institucion']);
        $stmt->bindParam(':institucion', $institucionInt, PDO::PARAM_INT);
        $stmt->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
        
        // Debug: Verificar valores exactos que se van a usar
        error_log("DEBUG updateExistingStudentSafe - Valores para bind:");
        error_log("DEBUG updateExistingStudentSafe - documento: '$documento'");
        error_log("DEBUG updateExistingStudentSafe - documentoSinPuntos: '$documentoSinPuntos'");
        error_log("DEBUG updateExistingStudentSafe - institucion: " . $config['conf_id_institucion'] . " (tipo: " . gettype($config['conf_id_institucion']) . ")");
        error_log("DEBUG updateExistingStudentSafe - year: '" . $config['conf_agno'] . "' (tipo: " . gettype($config['conf_agno']) . ")");
        
        if ($stmt->execute()) {
            $filasAfectadas = $stmt->rowCount();
            error_log("DEBUG updateExistingStudentSafe - Filas afectadas: $filasAfectadas");
            
            if ($filasAfectadas > 0) {
                return true;
            } else {
                return "No se encontró el estudiante o no hubo cambios";
            }
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("DEBUG updateExistingStudentSafe - Error SQL: " . json_encode($errorInfo));
            return "Error SQL: " . $errorInfo[2];
        }
        
    } catch (Exception $e) {
        error_log("DEBUG updateExistingStudentSafe - Excepción: " . $e->getMessage());
        return "Excepción al actualizar: " . $e->getMessage();
    }
}

// Manejo de la petición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Debug: Log de la petición
    error_log('AJAX Request recibida - Modo: ' . ($_POST['modo'] ?? 'no especificado'));
    
    try {
        // Verificar si es una petición de estado
    if (isset($_POST['action']) && $_POST['action'] === 'checkStatus') {
        $jobId = $_POST['jobId'];
        
        // Aquí verificarías el estado del job en la base de datos
        // Por simplicidad, simulamos que está completado
        jsonResponse([
            'completed' => true,
            'results' => [
                'created' => 5,
                'updated' => 3,
                'errors' => 1,
                'errorDetails' => ['Fila 10: Error de validación']
            ]
        ]);
    }
    
    // Validar archivo
    $fileErrors = validateExcelFile($_FILES['planilla']);
    if (!empty($fileErrors)) {
        jsonResponse([
            'success' => false,
            'message' => implode('. ', $fileErrors)
        ]);
    }
    
    // El sistema ahora detecta automáticamente las filas válidas
    
    // Obtener campos a actualizar
    $actualizarCampo = isset($_POST['actualizarCampo']) ? $_POST['actualizarCampo'] : [];
    
    // Debug: Verificar qué campos se están enviando
    error_log("DEBUG actualizarCampo recibido: " . json_encode($actualizarCampo));
    error_log("DEBUG POST completo: " . json_encode($_POST));
    
    // Determinar modo de procesamiento
    $modo = isset($_POST['modo']) ? $_POST['modo'] : 'job';
    
    if ($modo === 'inmediato') {
        // Procesamiento inmediato
        try {
            $results = processExcelImmediately($_FILES['planilla'], $actualizarCampo);
            
            jsonResponse([
                'success' => true,
                'results' => $results
            ]);
            
        } catch (Exception $e) {
            jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        
    } else {
        // Procesamiento con job (tradicional)
        $temName = $_FILES['planilla']['tmp_name'];
        $archivo = $_FILES['planilla']['name'];
        $destino = "../files/excel/";
        $explode = explode(".", $archivo);
        $extension = end($explode);
        $fullArchivo = uniqid('importado_').".".$extension;
        $nombreArchivo = $destino.$fullArchivo;
        
        if (move_uploaded_file($temName, $nombreArchivo)) {
            
            $parametros = array(
                "nombreArchivo" => $nombreArchivo,
                "actualizarCampo" => $actualizarCampo
            );
            
            try {
                $camposActualizar = "";
                $Separador = "";
                foreach ($actualizarCampo as $filtro) {
                    switch ($filtro) {
                        case '1':
                            $camposActualizar .= $Separador."Grado";
                            break;
                        case '2':
                            $camposActualizar .= $Separador."Grupo";
                            break;
                        case '3':
                            $camposActualizar .= $Separador."Tipo de Documento";
                            break;
                        case '4':
                            $camposActualizar .= $Separador."Acudiente";
                            break;
                        case '5':
                            $camposActualizar .= $Separador."Segundo nombre del estudiante";
                            break;
                        case '6':
                            $camposActualizar .= $Separador."Fecha de nacimiento";
                            break;
                    }
                    if ($Separador == "") {
                        $Separador = " , ";
                    }
                }
                
                if (!empty($actualizarCampo)) {
                    $camposActualizar = "Campos a actualizar (".$camposActualizar.")";
                }
                
                $mensaje = 'Se generó Jobs para importar excel del archivo ['.$archivo.'] '.$camposActualizar;
                $jobId = SysJobs::registrar(JOBS_TIPO_IMPORTAR_ESTUDIANTES_EXCEL, JOBS_PRIORIDAD_BAJA, $parametros, $mensaje);
                
                include("../compartido/guardar-historial-acciones.php");
                
                jsonResponse([
                    'success' => true,
                    'jobId' => $jobId,
                    'message' => $mensaje
    ]);

} catch (Exception $e) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Error al crear el job: ' . $e->getMessage()
                ]);
            }
        } else {
            jsonResponse([
                'success' => false,
                'message' => 'Error al subir el archivo'
            ]);
        }
    }
    
} catch (Exception $e) {
    // Capturar cualquier error general
    error_log('Error general en ajax-excel-importar-estudiantes.php: ' . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Error interno: ' . $e->getMessage()
    ]);
}
} else {
    jsonResponse([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>