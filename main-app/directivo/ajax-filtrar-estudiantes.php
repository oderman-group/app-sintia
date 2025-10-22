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
    
    // Construir filtro SQL
    $filtro = "";
    
    // Filtro de búsqueda general POTENTE
    if (!empty($busqueda)) {
        $busquedaEscape = mysqli_real_escape_string($conexion, $busqueda);
        
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
            uss.uss_usuario LIKE '%{$busquedaEscape}%'
        )";
    }
    
    // Filtro de cursos (múltiple)
    if (!empty($cursos) && is_array($cursos)) {
        $cursosStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($cursos), $conexion), $cursos));
        $filtro .= " AND mat_grado IN ('{$cursosStr}')";
    }
    
    // Filtro de grupos (múltiple)
    if (!empty($grupos) && is_array($grupos)) {
        $gruposStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($grupos), $conexion), $grupos));
        $filtro .= " AND mat_grupo IN ('{$gruposStr}')";
    }
    
    // Filtro de estados (múltiple)
    if (!empty($estados) && is_array($estados)) {
        $estadosStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($estados), $conexion), $estados));
        $filtro .= " AND mat_estado_matricula IN ('{$estadosStr}')";
    }
    
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
    $data["data"] = $estudiantes;
    $data["dataTotal"] = count($estudiantes);
    
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
        'total' => count($estudiantes),
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

