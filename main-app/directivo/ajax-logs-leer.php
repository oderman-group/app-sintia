<?php
/**
 * LEER Y PAGINAR ARCHIVOS DE LOG
 * Endpoint para visualizador de logs con filtros y paginación
 */

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");

// Verificar permisos de desarrollador
Modulos::verificarPermisoDev();

try {
    // Parámetros
    $archivoLog = $_POST['archivo'] ?? '';
    $pagina = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
    $porPagina = isset($_POST['porPagina']) ? (int)$_POST['porPagina'] : 100;
    $busqueda = $_POST['busqueda'] ?? '';
    $nivelFiltro = $_POST['nivel'] ?? 'todos'; // todos, error, warning, info
    
    if (empty($archivoLog)) {
        throw new Exception('Archivo de log no especificado');
    }
    
    // Determinar ruta del archivo según el tipo
    $rutaArchivo = obtenerRutaArchivo($archivoLog);
    
    if (!file_exists($rutaArchivo)) {
        throw new Exception('Archivo de log no encontrado: ' . basename($rutaArchivo));
    }
    
    // Leer archivo completo
    $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if ($lineas === false) {
        throw new Exception('Error al leer el archivo de log');
    }
    
    // Invertir para mostrar los más recientes primero
    $lineas = array_reverse($lineas);
    
    // Filtrar por búsqueda
    if (!empty($busqueda)) {
        $lineas = array_filter($lineas, function($linea) use ($busqueda) {
            return stripos($linea, $busqueda) !== false;
        });
        $lineas = array_values($lineas); // Reindexar
    }
    
    // Filtrar por nivel
    if ($nivelFiltro !== 'todos') {
        $lineas = array_filter($lineas, function($linea) use ($nivelFiltro) {
            switch ($nivelFiltro) {
                case 'error':
                    return stripos($linea, 'error') !== false || 
                           stripos($linea, 'fatal') !== false ||
                           stripos($linea, 'exception') !== false ||
                           stripos($linea, '❌') !== false;
                case 'warning':
                    return stripos($linea, 'warning') !== false ||
                           stripos($linea, 'warn') !== false ||
                           stripos($linea, '⚠️') !== false;
                case 'info':
                    return stripos($linea, 'info') !== false ||
                           stripos($linea, 'notice') !== false ||
                           stripos($linea, '✅') !== false ||
                           stripos($linea, 'ℹ️') !== false ||
                           stripos($linea, '🔵') !== false;
                default:
                    return true;
            }
        });
        $lineas = array_values($lineas); // Reindexar
    }
    
    $totalLineas = count($lineas);
    $totalPaginas = ceil($totalLineas / $porPagina);
    
    // Paginar
    $inicio = ($pagina - 1) * $porPagina;
    $lineasPaginadas = array_slice($lineas, $inicio, $porPagina);
    
    // Procesar líneas para agregar metadata
    $lineasProcesadas = [];
    foreach ($lineasPaginadas as $index => $linea) {
        $tipo = detectarTipoLog($linea);
        $lineasProcesadas[] = [
            'numero' => $inicio + $index + 1,
            'contenido' => $linea,
            'tipo' => $tipo,
            'timestamp' => extraerTimestamp($linea)
        ];
    }
    
    // Información del archivo
    $infoArchivo = [
        'nombre' => basename($rutaArchivo),
        'tamano' => formatearTamano(filesize($rutaArchivo)),
        'ultima_modificacion' => date('Y-m-d H:i:s', filemtime($rutaArchivo))
    ];
    
    echo json_encode([
        'success' => true,
        'lineas' => $lineasProcesadas,
        'paginacion' => [
            'paginaActual' => $pagina,
            'porPagina' => $porPagina,
            'totalLineas' => $totalLineas,
            'totalPaginas' => $totalPaginas
        ],
        'archivo' => $infoArchivo
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Obtener ruta del archivo según el tipo
 */
function obtenerRutaArchivo($archivo) {
    switch ($archivo) {
        // Logs de errores por ambiente
        case 'errores_local':
            return ROOT_PATH . '/config-general/errores_local.log';
        case 'errores_dev':
            return ROOT_PATH . '/config-general/errores_dev.log';
        case 'errores_prod':
            return ROOT_PATH . '/config-general/errores_prod.log';
        case 'errores_copy_prod':
            return ROOT_PATH . '/config-general/errores_copy_prod.log';
        case 'errores_default_env':
            return ROOT_PATH . '/config-general/errores_default_env.log';
            
        // Error logs antiguos por carpeta
        case 'error_log_directivo':
            return ROOT_PATH . '/main-app/directivo/error_log';
        case 'error_log_docente':
            return ROOT_PATH . '/main-app/docente/error_log';
        case 'error_log_estudiante':
            return ROOT_PATH . '/main-app/estudiante/error_log';
        case 'error_log_acudiente':
            return ROOT_PATH . '/main-app/acudiente/error_log';
        case 'error_log_compartido':
            return ROOT_PATH . '/main-app/compartido/error_log';
            
        default:
            throw new Exception('Tipo de log no reconocido');
    }
}

/**
 * Detectar tipo de log (error, warning, info)
 */
function detectarTipoLog($linea) {
    $lineaLower = strtolower($linea);
    
    // Errores
    if (strpos($lineaLower, 'error') !== false || 
        strpos($lineaLower, 'fatal') !== false ||
        strpos($lineaLower, 'exception') !== false ||
        strpos($linea, '❌') !== false) {
        return 'error';
    }
    
    // Warnings
    if (strpos($lineaLower, 'warning') !== false || 
        strpos($lineaLower, 'warn') !== false ||
        strpos($linea, '⚠️') !== false) {
        return 'warning';
    }
    
    // Info/Success
    if (strpos($lineaLower, 'info') !== false || 
        strpos($lineaLower, 'notice') !== false ||
        strpos($linea, '✅') !== false ||
        strpos($linea, 'ℹ️') !== false ||
        strpos($linea, '🔵') !== false) {
        return 'info';
    }
    
    return 'default';
}

/**
 * Extraer timestamp de la línea de log
 */
function extraerTimestamp($linea) {
    // Buscar patrón [DD-MMM-YYYY HH:MM:SS]
    if (preg_match('/\[(\d{2}-\w{3}-\d{4} \d{2}:\d{2}:\d{2}[^\]]*)\]/', $linea, $matches)) {
        return $matches[1];
    }
    
    // Buscar patrón [YYYY-MM-DD HH:MM:SS]
    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $linea, $matches)) {
        return $matches[1];
    }
    
    return null;
}

/**
 * Formatear tamaño de archivo
 */
function formatearTamano($bytes) {
    $unidades = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    
    while ($bytes >= 1024 && $i < count($unidades) - 1) {
        $bytes /= 1024;
        $i++;
    }
    
    return round($bytes, 2) . ' ' . $unidades[$i];
}

