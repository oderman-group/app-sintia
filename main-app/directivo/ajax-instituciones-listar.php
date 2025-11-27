<?php
/**
 * LISTAR INSTITUCIONES CON PAGINACIÓN Y FILTROS
 * Endpoint para cargar instituciones dinámicamente
 */

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");

// Verificar permisos
Modulos::verificarPermisoDev();

// Parámetros de paginación
$pagina = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
$porPagina = isset($_POST['porPagina']) ? (int)$_POST['porPagina'] : 20;
$inicio = ($pagina - 1) * $porPagina;

// Parámetros de filtros
$busqueda = isset($_POST['busqueda']) ? mysqli_real_escape_string($conexion, trim($_POST['busqueda'])) : '';
$plan = isset($_POST['plan']) ? mysqli_real_escape_string($conexion, $_POST['plan']) : '';
$estado = isset($_POST['estado']) ? mysqli_real_escape_string($conexion, $_POST['estado']) : '';
$bloqueado = isset($_POST['bloqueado']) ? mysqli_real_escape_string($conexion, $_POST['bloqueado']) : '';

// Construir filtros
$filtros = [];
$filtros[] = "ins_enviroment = '" . ENVIROMENT . "'";

if (!empty($busqueda)) {
    $filtros[] = "(ins_nombre LIKE '%$busqueda%' OR ins_siglas LIKE '%$busqueda%' OR ins_contacto_principal LIKE '%$busqueda%' OR ins_id = '$busqueda')";
}

if (!empty($plan) && $plan !== 'todos') {
    $filtros[] = "ins_id_plan = '$plan'";
}

// Usar isset en lugar de !empty para permitir el valor "0" (Inactivo)
if (isset($estado) && $estado !== '' && $estado !== 'todos') {
    $filtros[] = "ins_estado = '$estado'";
}

// Usar isset en lugar de !empty para permitir el valor "0" (No bloqueado)
if (isset($bloqueado) && $bloqueado !== '' && $bloqueado !== 'todos') {
    $filtros[] = "ins_bloqueada = '$bloqueado'";
}

$whereClause = count($filtros) > 0 ? "WHERE " . implode(" AND ", $filtros) : "";

try {
    // Contar total de registros
    $consultaTotal = mysqli_query($conexion, "
        SELECT COUNT(*) as total 
        FROM " . $baseDatosServicios . ".instituciones 
        $whereClause
    ");
    $totalRegistros = mysqli_fetch_array($consultaTotal, MYSQLI_BOTH)['total'];
    $totalPaginas = ceil($totalRegistros / $porPagina);
    
    // Obtener instituciones con paginación
    $consulta = mysqli_query($conexion, "
        SELECT i.*, p.plns_nombre, p.plns_espacio_gb
        FROM " . $baseDatosServicios . ".instituciones i
        LEFT JOIN " . $baseDatosServicios . ".planes_sintia p ON p.plns_id = i.ins_id_plan
        $whereClause
        ORDER BY i.ins_id DESC
        LIMIT $inicio, $porPagina
    ");
    
    $instituciones = [];
    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
        $instituciones[] = [
            'ins_id' => $resultado['ins_id'],
            'ins_nombre' => $resultado['ins_nombre'],
            'ins_siglas' => $resultado['ins_siglas'],
            'ins_fecha_inicio' => $resultado['ins_fecha_inicio'],
            'ins_contacto_principal' => $resultado['ins_contacto_principal'],
            'ins_email_contacto' => $resultado['ins_email_contacto'],
            'ins_estado' => $resultado['ins_estado'],
            'ins_bloqueada' => $resultado['ins_bloqueada'],
            'ins_bd' => $resultado['ins_bd'],
            'ins_year_default' => $resultado['ins_year_default'],
            'ins_fecha_renovacion' => $resultado['ins_fecha_renovacion'],
            'ins_nit' => $resultado['ins_nit'],
            'plns_nombre' => $resultado['plns_nombre'],
            'plns_espacio_gb' => $resultado['plns_espacio_gb']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'instituciones' => $instituciones,
        'paginacion' => [
            'paginaActual' => $pagina,
            'porPagina' => $porPagina,
            'totalRegistros' => $totalRegistros,
            'totalPaginas' => $totalPaginas
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar instituciones: ' . $e->getMessage()
    ]);
}

