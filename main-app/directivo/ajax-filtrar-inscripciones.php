<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Inscripciones.php");
include(ROOT_PATH."/config-general/config-admisiones.php");

header('Content-Type: application/json');

try {
    // Recibir filtros
    $grados = isset($_POST['grados']) ? $_POST['grados'] : [];
    $estados = isset($_POST['estados']) ? $_POST['estados'] : [];
    $anios = isset($_POST['anios']) ? $_POST['anios'] : [];
    $busqueda = isset($_POST['busqueda']) ? trim($_POST['busqueda']) : '';
    $tabActivo = isset($_POST['tab']) ? $_POST['tab'] : 'visibles';
    
    // Construir filtro SQL base según el tab activo
    if ($tabActivo === 'ocultos') {
        $filtro = " AND (asp.asp_oculto=1)";
    } else {
        $filtro = " AND (asp.asp_oculto IS NULL OR asp.asp_oculto=0)";
    }
    
    // Filtro de grados (múltiple)
    if (!empty($grados) && is_array($grados)) {
        $gradosStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($grados), $conexion), $grados));
        $filtro .= " AND asp_grado IN ('{$gradosStr}')";
    }
    
    // Filtro de estados (múltiple)
    if (!empty($estados) && is_array($estados)) {
        $estadosStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($estados), $conexion), $estados));
        $filtro .= " AND asp_estado_solicitud IN ('{$estadosStr}')";
    }
    
    // Filtro de años (múltiple)
    if (!empty($anios) && is_array($anios)) {
        $aniosStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($anios), $conexion), $anios));
        $filtro .= " AND asp_agno IN ('{$aniosStr}')";
    }
    
    // Filtro de búsqueda general
    if (!empty($busqueda)) {
        $busquedaEscape = mysqli_real_escape_string($conexion, $busqueda);
        $filtro .= " AND (
            mat_nombres LIKE '%{$busquedaEscape}%' OR
            mat_nombre2 LIKE '%{$busquedaEscape}%' OR
            mat_primer_apellido LIKE '%{$busquedaEscape}%' OR
            mat_segundo_apellido LIKE '%{$busquedaEscape}%' OR
            mat_documento LIKE '%{$busquedaEscape}%' OR
            asp_email_acudiente LIKE '%{$busquedaEscape}%' OR
            asp_nombre_acudiente LIKE '%{$busquedaEscape}%' OR
            asp_documento_acudiente LIKE '%{$busquedaEscape}%' OR
            CONCAT_WS(' ', mat_nombres, mat_nombre2, mat_primer_apellido, mat_segundo_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT_WS(' ', mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_nombre2) LIKE '%{$busquedaEscape}%'
        )";
    }
    
    // Campos a seleccionar
    $selectSql = [
        "mat_id", "mat_documento", "gra_nombre",
        "asp_observacion", "asp_nombre_acudiente", "asp_celular_acudiente",
        "asp_documento_acudiente", "asp_id", "asp_fecha", "asp_comprobante", "mat_nombres",
        "asp_agno", "asp_email_acudiente", "asp_estado_solicitud", "mat_nombre2", "mat_primer_apellido", "mat_segundo_apellido",
        "mat.*", "asp.*"
    ];
    
    // Consultar inscripciones con filtros
    $consulta = Estudiantes::listarMatriculasAspirantes($config, $filtro, '', '', $selectSql);
    
    $listaInscripciones = [];
    $aspIdsVistos = []; // Para evitar duplicados
    if (!empty($consulta)) {
        while ($fila = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
            $aspId = $fila['asp_id'] ?? null;
            // Solo agregar si no hemos visto este asp_id antes
            if ($aspId && !in_array($aspId, $aspIdsVistos)) {
                $aspIdsVistos[] = $aspId;
                $listaInscripciones[] = $fila;
            }
        }
    }
    
    // Preparar datos para el componente
    $data["data"] = $listaInscripciones;
    $contReg = 1;
    $mostrarOcultos = ($tabActivo === 'ocultos'); // Contexto según el tab activo
    
    $configAdmisiones = Inscripciones::configuracionAdmisiones($conexion, $baseDatosAdmisiones, $config['conf_id_institucion'], $_SESSION["bd"]);
    
    // Capturar el HTML generado por el componente
    ob_start();
    include(ROOT_PATH . "/main-app/class/componentes/result/inscripciones-tbody.php");
    $html = ob_get_clean();
    
    // LIMPIAR el HTML: Eliminar todo lo que no sean filas <tr>
    // Eliminar <link>, <style> y <script> tags que rompen DataTable
    $html = preg_replace('/<link[^>]*>/is', '', $html);
    $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
    $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
    
    // Extraer solo las filas <tr>
    if (preg_match('/<tr/i', $html, $matches, PREG_OFFSET_CAPTURE)) {
        $html = substr($html, $matches[0][1]);
    }
    
    if (preg_match('/<\/tr>(?!.*<\/tr>)/is', $html, $matches, PREG_OFFSET_CAPTURE)) {
        $html = substr($html, 0, $matches[0][1] + strlen($matches[0][0]));
    }
    
    // Asegurar que las filas expandibles están ocultas
    $html = str_replace('style="display: table-row;"', 'style="display: none !important;"', $html);
    $html = str_replace('class="expandable-row"', 'class="expandable-row" style="display: none !important;"', $html);
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'total' => count($listaInscripciones),
        'filtros' => [
            'grados' => $grados,
            'estados' => $estados,
            'anios' => $anios,
            'busqueda' => $busqueda
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al filtrar: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Error $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error fatal: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

