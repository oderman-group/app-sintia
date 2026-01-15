<?php
include("session.php");
require_once("../class/Estudiantes.php");
require_once("../class/servicios/GradoServicios.php"); 
require_once(ROOT_PATH."/main-app/class/Grupos.php");

header('Content-Type: application/json');

try {
    // Recibir filtros
    $cursos = isset($_POST['cursos']) ? $_POST['cursos'] : [];
    $grupos = isset($_POST['grupos']) ? $_POST['grupos'] : [];
    $estados = isset($_POST['estados']) ? $_POST['estados'] : [];
    $busqueda = isset($_POST['busqueda']) ? trim($_POST['busqueda']) : '';
    $fechaDesde = isset($_POST['fechaDesde']) ? trim($_POST['fechaDesde']) : '';
    $fechaHasta = isset($_POST['fechaHasta']) ? trim($_POST['fechaHasta']) : '';
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
                        <a class="page-link js-estu-page" data-page="<?= (int)$ant; ?>" href="#">Previous</a>
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
                                <a class="page-link js-estu-page" data-page="<?= (int)$i; ?>" href="#"><?= (int)$i; ?></a>
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
                        <a class="page-link js-estu-page" data-page="<?= (int)$sig; ?>" href="#">Next</a>
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
    
    // Log de filtros recibidos
    error_log("FILTRAR-ESTUDIANTES: Filtros recibidos:");
    error_log("  - cursos: " . json_encode($cursos));
    error_log("  - grupos: " . json_encode($grupos));
    error_log("  - estados: " . json_encode($estados));
    error_log("  - busqueda: '$busqueda'");
    error_log("  - fechaDesde: '$fechaDesde'");
    error_log("  - fechaHasta: '$fechaHasta'");
    
    // Construir filtro SQL
    $filtro = "";
    
    // Filtro de búsqueda general POTENTE
    if (!empty($busqueda)) {
        // Normalizar espacios del término buscado (máximo 1 espacio)
        $busquedaNorm = preg_replace('/\s+/u', ' ', $busqueda);
        $busquedaNorm = trim($busquedaNorm);

        $busquedaEscape = mysqli_real_escape_string($conexion, $busquedaNorm);
        // Variante tolerante: convierte espacios en comodines para tolerar dobles espacios en BD (p.ej "mejia martinez" => "mejia%martinez")
        $busquedaLike = mysqli_real_escape_string($conexion, str_replace(' ', '%', $busquedaNorm));

        // Expresiones de nombre normalizadas (trim + concat_ws) para evitar dobles espacios entre campos vacíos
        $nombreNormal_orden1 = "CONCAT_WS(' ', NULLIF(TRIM(mat_nombres),''), NULLIF(TRIM(mat_nombre2),''), NULLIF(TRIM(mat_primer_apellido),''), NULLIF(TRIM(mat_segundo_apellido),''))";
        $nombreNormal_orden2 = "CONCAT_WS(' ', NULLIF(TRIM(mat_primer_apellido),''), NULLIF(TRIM(mat_segundo_apellido),''), NULLIF(TRIM(mat_nombres),''), NULLIF(TRIM(mat_nombre2),''))";
        
        // Búsqueda POTENTE y case-insensitive que busca en:
        // - 4 campos de nombres: mat_nombres, mat_nombre2, mat_primer_apellido, mat_segundo_apellido
        // - TODAS las combinaciones posibles de estos campos en CUALQUIER orden
        // - Documento, Email, Usuario
        // - Sin importar mayúsculas o minúsculas (LIKE es case-insensitive por defecto en MySQL)
        $filtro .= " AND (
            mat_nombres LIKE '%{$busquedaEscape}%' OR
            mat_nombre2 LIKE '%{$busquedaEscape}%' OR
            mat_primer_apellido LIKE '%{$busquedaEscape}%' OR
            mat_segundo_apellido LIKE '%{$busquedaEscape}%' OR
            
            CONCAT(mat_nombres, ' ', mat_nombre2) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_nombre2, ' ', mat_nombres) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_nombres, ' ', mat_primer_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_nombres, ' ', mat_segundo_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_nombre2, ' ', mat_primer_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_nombre2, ' ', mat_segundo_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_primer_apellido, ' ', mat_segundo_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_segundo_apellido, ' ', mat_primer_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_primer_apellido, ' ', mat_nombres) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_primer_apellido, ' ', mat_nombre2) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_segundo_apellido, ' ', mat_nombres) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_segundo_apellido, ' ', mat_nombre2) LIKE '%{$busquedaEscape}%' OR
            
            CONCAT(mat_nombres, ' ', mat_nombre2, ' ', mat_primer_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_nombres, ' ', mat_nombre2, ' ', mat_segundo_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_nombres, ' ', mat_primer_apellido, ' ', mat_segundo_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_nombre2, ' ', mat_primer_apellido, ' ', mat_segundo_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_primer_apellido, ' ', mat_nombres, ' ', mat_nombre2) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_primer_apellido, ' ', mat_segundo_apellido, ' ', mat_nombres) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_primer_apellido, ' ', mat_segundo_apellido, ' ', mat_nombre2) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_segundo_apellido, ' ', mat_primer_apellido, ' ', mat_nombres) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_segundo_apellido, ' ', mat_primer_apellido, ' ', mat_nombre2) LIKE '%{$busquedaEscape}%' OR
            
            CONCAT(mat_primer_apellido, ' ', mat_segundo_apellido, ' ', mat_nombres, ' ', mat_nombre2) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_nombres, ' ', mat_nombre2, ' ', mat_primer_apellido, ' ', mat_segundo_apellido) LIKE '%{$busquedaEscape}%' OR
            CONCAT(mat_segundo_apellido, ' ', mat_primer_apellido, ' ', mat_nombres, ' ', mat_nombre2) LIKE '%{$busquedaEscape}%' OR
            
            mat_documento LIKE '%{$busquedaEscape}%' OR
            mat_email LIKE '%{$busquedaEscape}%' OR
            mat_codigo_tesoreria LIKE '%{$busquedaEscape}%' OR
            uss.uss_usuario LIKE '%{$busquedaEscape}%' OR

            -- Comparación por nombre completo normalizado (evita dobles espacios entre campos)
            {$nombreNormal_orden1} LIKE '%{$busquedaEscape}%' OR
            {$nombreNormal_orden2} LIKE '%{$busquedaEscape}%' OR

            -- Variante tolerante a espacios extra en BD (espacios => %)
            {$nombreNormal_orden1} LIKE '%{$busquedaLike}%' OR
            {$nombreNormal_orden2} LIKE '%{$busquedaLike}%'
        )";
    }
    
    // Filtro de cursos (múltiple)
    if (!empty($cursos) && is_array($cursos)) {
        $cursosStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($cursos), $conexion), $cursos));
        $filtro .= " AND mat.mat_grado IN ('{$cursosStr}')";
    }
    
    // Filtro de grupos (múltiple)
    if (!empty($grupos) && is_array($grupos)) {
        $gruposStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($grupos), $conexion), $grupos));
        $filtro .= " AND mat.mat_grupo IN ('{$gruposStr}')";
    }
    
    // Filtro de estados (múltiple)
    if (!empty($estados) && is_array($estados)) {
        $estadosStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($estados), $conexion), $estados));
        $filtro .= " AND mat.mat_estado_matricula IN ('{$estadosStr}')";
    }
    
    // Filtro por fecha de matrícula (desde)
    if (!empty($fechaDesde)) {
        $fechaDesdeEscape = mysqli_real_escape_string($conexion, $fechaDesde);
        $filtro .= " AND DATE(mat.mat_fecha) >= '{$fechaDesdeEscape}'";
        error_log("FILTRAR-ESTUDIANTES: Aplicado filtro fecha desde: $fechaDesdeEscape");
    }
    
    // Filtro por fecha de matrícula (hasta)
    if (!empty($fechaHasta)) {
        $fechaHastaEscape = mysqli_real_escape_string($conexion, $fechaHasta);
        $filtro .= " AND DATE(mat.mat_fecha) <= '{$fechaHastaEscape}'";
        error_log("FILTRAR-ESTUDIANTES: Aplicado filtro fecha hasta: $fechaHastaEscape");
    }
    
    error_log("FILTRAR-ESTUDIANTES: Filtro SQL completo: $filtro");
    
    // Campos a seleccionar
    $selectSql = [
        "mat.*",
        "uss.uss_id",
        "uss.uss_usuario",
        "uss.uss_bloqueado",
        "gra_nombre",
        "gru_nombre",
        "gra_formato_boletin",
        "acud.uss_nombre",
        "acud.uss_nombre2",
        "mat.id_nuevo AS mat_id_nuevo",
        "og_tipo_doc.ogen_nombre as tipo_doc_nombre",
        "og_genero.ogen_nombre as genero_nombre",
        "og_estrato.ogen_nombre as estrato_nombre",
        "og_tipo_sangre.ogen_nombre as tipo_sangre_nombre"
    ];
    
    // Consultar estudiantes con filtros
    $consulta = Estudiantes::listarEstudiantes(0, $filtro, '', null, null, $selectSql);
    
    $estudiantes = [];
    if (!empty($consulta)) {
        while ($fila = $consulta->fetch_assoc()) {
            $estudiantes[] = $fila;
        }
        $consulta->free();
    }
    
    // Preparar datos para el componente (asegurando que todas las variables necesarias estén definidas)
    $totalFiltrado = count($estudiantes);
    $totalPaginas = $totalFiltrado > 0 ? (int)ceil($totalFiltrado / $registrosPorPagina) : 0;
    if ($totalPaginas > 0 && $page > $totalPaginas) { $page = $totalPaginas; }
    $inicio = ($page - 1) * $registrosPorPagina;
    if ($inicio < 0) { $inicio = 0; }

    $data["data"] = array_slice($estudiantes, $inicio, $registrosPorPagina);
    $data["dataTotal"] = $totalFiltrado;
    $contReg = $inicio + 1;
    
    // Variables adicionales que el componente necesita
    require_once(ROOT_PATH."/main-app/class/Modulos.php");
    require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
    require_once(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
    
    $usuariosClase = new UsuariosFunciones;
    $arregloModulos = array();
    $moduloConvivencia = false;
    $permisoReportes = false;
    
    // Capturar el HTML generado por el componente
    ob_start();
    include(ROOT_PATH . "/main-app/class/componentes/result/matriculas-tbody.php");
    $html = ob_get_clean();
    
    // LIMPIAR el HTML: Eliminar todo lo que no sean filas <tr>
    // Esto es crítico porque el componente incluye <link>, <style>, <script> que rompen la tabla
    
    // Eliminar todo antes del primer <tr
    if (preg_match('/<tr/i', $html, $matches, PREG_OFFSET_CAPTURE)) {
        $html = substr($html, $matches[0][1]);
    }
    
    // Eliminar todo después del último </tr>
    if (preg_match('/<\/tr>(?!.*<\/tr>)/is', $html, $matches, PREG_OFFSET_CAPTURE)) {
        $html = substr($html, 0, $matches[0][1] + strlen($matches[0][0]));
    }
    
    // Asegurar que las filas expandibles estén ocultas en el HTML
    $html = str_replace('class="expandable-row"', 'class="expandable-row" style="display: none;"', $html);
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'total' => $totalFiltrado,
        'page' => $page,
        'totalPages' => $totalPaginas,
        'paginationHtml' => $buildPaginationHtml($page, $totalFiltrado, $registrosPorPagina),
        'filtros' => [
            'cursos' => $cursos,
            'grupos' => $grupos,
            'estados' => $estados
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al filtrar: ' . $e->getMessage()
    ]);
}

