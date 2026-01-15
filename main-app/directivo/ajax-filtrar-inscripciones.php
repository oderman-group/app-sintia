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
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    if ($page < 1) { $page = 1; }

    $registrosPorPagina = !empty($config['conf_num_registros']) ? (int)$config['conf_num_registros'] : 20;
    if ($registrosPorPagina < 1) { $registrosPorPagina = 20; }

    $buildPaginationHtml = function(int $paginaActual, int $totalRegistros, int $porPagina) {
        if ($totalRegistros <= 0) {
            return '';
        }
        $totalPaginas = (int)ceil($totalRegistros / $porPagina);
        if ($totalPaginas < 1) { $totalPaginas = 1; }
        if ($paginaActual > $totalPaginas) { $paginaActual = $totalPaginas; }

        $inicio = (($paginaActual - 1) * $porPagina);
        $fin = min($inicio + $porPagina, $totalRegistros);
        $ant = $paginaActual - 1;
        $sig = $paginaActual + 1;

        ob_start();
        ?>
        <div style="text-align:center">
            <ul class="pagination pg-dark justify-content-center pb-5 pt-5 mb-0" style="float: none; padding-bottom: 5px!important;">
                <li class="page-item">
                    <?php if ($paginaActual > 1) { ?>
                        <a class="page-link js-insc-page" data-page="<?= (int)$ant; ?>" href="#">Previous</a>
                    <?php } else { ?>
                        <span class="page-link">Previous</span>
                    <?php } ?>
                </li>
                <?php
                for ($i = 1; $i <= $totalPaginas; $i++) {
                    if ($i == 1 || $i == $totalPaginas || ($i >= $paginaActual - 2 && $i <= $paginaActual + 2)) {
                        if ($i == $paginaActual) {
                            ?>
                            <li class="page-item active" style="padding-left: 5px!important;">
                                <a class="page-link"><?= (int)$i; ?></a>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" style="padding-left: 5px!important;">
                                <a class="page-link js-insc-page" data-page="<?= (int)$i; ?>" href="#"><?= (int)$i; ?></a>
                            </li>
                            <?php
                        }
                    } elseif (($i == 2 && $paginaActual > 3) || ($i == $totalPaginas - 1 && $paginaActual < $totalPaginas - 2)) {
                        ?>
                        <li class="page-item" style="padding-left: 5px!important;">
                            <span class="page-link">...</span>
                        </li>
                        <?php
                    }
                }
                ?>
                <li class="page-item" style="padding-left: 5px!important;">
                    <?php if ($paginaActual < $totalPaginas) { ?>
                        <a class="page-link js-insc-page" data-page="<?= (int)$sig; ?>" href="#">Next</a>
                    <?php } else { ?>
                        <span class="page-link">Next</span>
                    <?php } ?>
                </li>
            </ul>
            <p>Mostrando <?= (int)($inicio + 1); ?> a <?= (int)$fin; ?> de <?= (int)$totalRegistros; ?> resultados totales</p>
        </div>
        <?php
        return ob_get_clean();
    };
    
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
    // Paginación (sobre la lista ya deduplicada)
    $totalFiltrado = count($listaInscripciones);
    $totalPaginas = $totalFiltrado > 0 ? (int)ceil($totalFiltrado / $registrosPorPagina) : 0;
    if ($totalPaginas > 0 && $page > $totalPaginas) { $page = $totalPaginas; }
    $inicio = ($page - 1) * $registrosPorPagina;
    if ($inicio < 0) { $inicio = 0; }
    $data["data"] = array_slice($listaInscripciones, $inicio, $registrosPorPagina);

    // Para numeración si el componente lo respeta
    $contReg = $inicio + 1;
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
        'total' => $totalFiltrado,
        'page' => $page,
        'totalPages' => $totalPaginas,
        'paginationHtml' => $buildPaginationHtml($page, $totalFiltrado, $registrosPorPagina),
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

