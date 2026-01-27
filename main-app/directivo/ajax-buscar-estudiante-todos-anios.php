<?php
include("session.php");
require_once("../class/Estudiantes.php");

header('Content-Type: application/json');

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    $busqueda = isset($_POST['busqueda']) ? trim($_POST['busqueda']) : '';
    
    // Validar que haya al menos 3 caracteres para evitar consultas costosas
    if (strlen($busqueda) < 3) {
        $response['message'] = 'Escriba al menos 3 caracteres para buscar';
        echo json_encode($response);
        exit();
    }
    
    // Obtener años de la institución
    $insYears = $_SESSION["datosUnicosInstitucion"]["ins_years"] ?? '';
    if (empty($insYears)) {
        $response['message'] = 'No se encontraron años de la institución';
        echo json_encode($response);
        exit();
    }
    
    $yearArray = explode(",", $insYears);
    $yearStart = (int)trim($yearArray[0]);
    $yearEnd = (int)trim($yearArray[1] ?? $yearArray[0]);
    
    // Escapar búsqueda para SQL
    $busquedaEsc = mysqli_real_escape_string($conexion, $busqueda);
    $institucion = (int)$config['conf_id_institucion'];
    
    // Construir consulta para buscar en todos los años
    $aniosConsulta = [];
    for ($y = $yearStart; $y <= $yearEnd; $y++) {
        $aniosConsulta[] = "'" . mysqli_real_escape_string($conexion, $y) . "'";
    }
    $aniosIn = implode(',', $aniosConsulta);
    
    // Buscar estudiantes en todos los años
    // Usar mat_id_usuario o mat_documento para agrupar
    $sql = "SELECT DISTINCT
                COALESCE(mat.mat_id_usuario, CONCAT('doc_', mat.mat_documento)) as identificador,
                mat.mat_id_usuario,
                mat.mat_documento,
                mat.mat_id,
                mat.mat_nombres,
                mat.mat_nombre2,
                mat.mat_primer_apellido,
                mat.mat_segundo_apellido,
                mat.mat_matricula,
                mat.year,
                gra.gra_nombre,
                gru.gru_nombre
            FROM " . BD_ACADEMICA . ".academico_matriculas mat
            LEFT JOIN " . BD_ACADEMICA . ".academico_grados gra 
                ON gra.gra_id = mat.mat_grado 
                AND gra.institucion = mat.institucion 
                AND gra.year = mat.year
            LEFT JOIN " . BD_ACADEMICA . ".academico_grupos gru 
                ON gru.gru_id = mat.mat_grupo 
                AND gru.institucion = mat.institucion 
                AND gru.year = mat.year
            WHERE mat.mat_eliminado = 0 
            AND mat.institucion = {$institucion}
            AND mat.year IN ({$aniosIn})
            AND (
                mat.mat_id LIKE '%{$busquedaEsc}%' 
                OR mat.mat_nombres LIKE '%{$busquedaEsc}%' 
                OR mat.mat_nombre2 LIKE '%{$busquedaEsc}%' 
                OR mat.mat_primer_apellido LIKE '%{$busquedaEsc}%' 
                OR mat.mat_segundo_apellido LIKE '%{$busquedaEsc}%' 
                OR mat.mat_documento LIKE '%{$busquedaEsc}%' 
                OR mat.mat_matricula LIKE '%{$busquedaEsc}%'
                OR mat.mat_email LIKE '%{$busquedaEsc}%'
                OR CONCAT(TRIM(mat.mat_primer_apellido), ' ', TRIM(mat.mat_segundo_apellido), ' ', TRIM(mat.mat_nombres)) LIKE '%{$busquedaEsc}%'
                OR CONCAT(TRIM(mat.mat_primer_apellido), ' ', TRIM(mat.mat_nombres)) LIKE '%{$busquedaEsc}%'
                OR CONCAT(TRIM(mat.mat_nombres), ' ', TRIM(mat.mat_primer_apellido)) LIKE '%{$busquedaEsc}%'
            )
            ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres, mat.year DESC
            LIMIT 50";
    
    $consulta = mysqli_query($conexion, $sql);
    
    if (!$consulta) {
        throw new Exception('Error en la consulta: ' . mysqli_error($conexion));
    }
    
    // Agrupar por identificador (mat_id_usuario o documento)
    $estudiantesAgrupados = [];
    
    while ($row = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
        $identificador = $row['identificador'];
        
        if (!isset($estudiantesAgrupados[$identificador])) {
            $nombreCompleto = trim(
                ($row['mat_primer_apellido'] ?? '') . ' ' . 
                ($row['mat_segundo_apellido'] ?? '') . ' ' . 
                ($row['mat_nombres'] ?? '') . ' ' . 
                ($row['mat_nombre2'] ?? '')
            );
            
            $estudiantesAgrupados[$identificador] = [
                'mat_id_usuario' => $row['mat_id_usuario'],
                'mat_documento' => $row['mat_documento'],
                'mat_id' => $row['mat_id'], // ID del año más reciente
                'nombre_completo' => strtoupper(trim($nombreCompleto)),
                'grado_actual' => strtoupper($row['gra_nombre'] ?? ''),
                'grupo_actual' => strtoupper($row['gru_nombre'] ?? ''),
                'anios_matriculado' => [],
                'identificador' => $identificador
            ];
        }
        
        // Agregar año a la lista si no está ya
        $year = (int)$row['year'];
        if (!in_array($year, $estudiantesAgrupados[$identificador]['anios_matriculado'])) {
            $estudiantesAgrupados[$identificador]['anios_matriculado'][] = $year;
        }
    }
    
    // Procesar resultados finales
    $estudiantes = [];
    foreach ($estudiantesAgrupados as $estudiante) {
        sort($estudiante['anios_matriculado']);
        $anioMin = min($estudiante['anios_matriculado']);
        $anioMax = max($estudiante['anios_matriculado']);
        
        $textoDisplay = "[" . $estudiante['mat_id'] . "] " . $estudiante['nombre_completo'];
        if (!empty($estudiante['grado_actual'])) {
            $textoDisplay .= " - " . $estudiante['grado_actual'];
        }
        if (count($estudiante['anios_matriculado']) > 1) {
            $textoDisplay .= " (" . $anioMin . "-" . $anioMax . ")";
        } else {
            $textoDisplay .= " (" . $anioMin . ")";
        }
        
        $estudiantes[] = [
            'id' => $estudiante['mat_id'],
            'mat_id_usuario' => $estudiante['mat_id_usuario'],
            'mat_documento' => $estudiante['mat_documento'],
            'nombre_completo' => $estudiante['nombre_completo'],
            'grado_actual' => $estudiante['grado_actual'],
            'grupo_actual' => $estudiante['grupo_actual'],
            'anios_matriculado' => $estudiante['anios_matriculado'],
            'anio_minimo' => $anioMin,
            'anio_maximo' => $anioMax,
            'texto_display' => $textoDisplay,
            'identificador' => $estudiante['identificador']
        ];
    }
    
    $response['success'] = true;
    $response['data'] = $estudiantes;
    $response['message'] = count($estudiantes) . ' estudiante(s) encontrado(s)';
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
