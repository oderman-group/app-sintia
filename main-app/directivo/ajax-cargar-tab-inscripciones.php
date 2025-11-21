<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Inscripciones.php");
include(ROOT_PATH."/config-general/config-admisiones.php");

header('Content-Type: application/json');

try {
    $tab = isset($_POST['tab']) ? $_POST['tab'] : 'visibles';
    $pagina = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
    
    // Construir filtro según el tab
    if ($tab === 'ocultos') {
        $filtro = " AND (asp.asp_oculto=1)";
    } else {
        $filtro = " AND (asp.asp_oculto IS NULL OR asp.asp_oculto=0)";
    }
    
    // Calcular paginación
    $registros = !empty($config['conf_num_registros']) ? (int)$config['conf_num_registros'] : 20;
    $inicio = (($pagina - 1) * $registros);
    $filtroLimite = 'LIMIT '.$inicio.','.$registros;
    
    // Contar total de registros (sin LIMIT)
    $consultaTotal = Estudiantes::listarMatriculasAspirantes($config, $filtro, '', '', []);
    $numRegistros = mysqli_num_rows($consultaTotal);
    if ($consultaTotal) {
        mysqli_free_result($consultaTotal);
    }
    
    // Campos a seleccionar
    $selectSql = [
        "mat_id", "mat_documento", "gra_nombre",
        "asp_observacion", "asp_nombre_acudiente", "asp_celular_acudiente",
        "asp_documento_acudiente", "asp_id", "asp_fecha", "asp_comprobante", "mat_nombres",
        "asp_agno", "asp_email_acudiente", "asp_estado_solicitud", "mat_nombre2", "mat_primer_apellido", "mat_segundo_apellido",
        "mat.*", "asp.*"
    ];
    
    // Consultar inscripciones con LIMIT
    $consulta = Estudiantes::listarMatriculasAspirantes($config, $filtro, $filtroLimite, '', $selectSql);
    
    $listaInscripciones = [];
    if (!empty($consulta)) {
        while ($fila = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
            $listaInscripciones[] = $fila;
        }
    }
    
    // Preparar datos para el componente
    $data["data"] = $listaInscripciones;
    $contReg = 1;
    $mostrarOcultos = ($tab === 'ocultos');
    
    $configAdmisiones = Inscripciones::configuracionAdmisiones($conexion, $baseDatosAdmisiones, $config['conf_id_institucion'], $_SESSION["bd"]);
    
    // Capturar el HTML generado por el componente
    ob_start();
    
    if (empty($data["data"])) {
        $mensaje = $mostrarOcultos ? 'No hay inscripciones ocultas en este momento.' : 'No hay inscripciones visibles en este momento.';
        echo '<tr><td colspan="12" class="text-center">
            <i class="fa fa-info-circle fa-2x text-info"></i><br>
            <strong>' . $mensaje . '</strong>
        </td></tr>';
    } else {
        include(ROOT_PATH . "/main-app/class/componentes/result/inscripciones-tbody.php");
    }
    
    $html = ob_get_clean();
    
    // LIMPIAR el HTML: Eliminar todo lo que no sean filas <tr>
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
    
    // Calcular información de paginación
    $totalPaginas = ceil($numRegistros / $registros);
    $fin = ($inicio + $registros);
    if ($pagina == $totalPaginas) {
        $fin = $numRegistros;
    }
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'total' => $numRegistros,
        'tab' => $tab,
        'pagina' => $pagina,
        'totalPaginas' => $totalPaginas,
        'mostrando' => ($inicio + 1) . ' a ' . $fin . ' de ' . $numRegistros
    ]);
    
} catch (Exception $e) {
    error_log('Error en ajax-cargar-tab-inscripciones.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al cargar los datos: ' . $e->getMessage()
    ]);
}
?>

