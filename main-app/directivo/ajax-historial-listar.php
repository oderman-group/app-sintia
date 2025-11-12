<?php
/**
 * LISTAR HISTORIAL DE ACCIONES CON PAGINACIÓN Y FILTROS
 * Endpoint para cargar historial dinámicamente
 */

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");

// Verificar permisos
Modulos::verificarPermisoDev();

// Parámetros de paginación
$pagina = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
$porPagina = isset($_POST['porPagina']) ? (int)$_POST['porPagina'] : 50;
$inicio = ($pagina - 1) * $porPagina;

// Parámetros de filtros
$busqueda = isset($_POST['busqueda']) ? mysqli_real_escape_string($conexion, trim($_POST['busqueda'])) : '';
$institucion = isset($_POST['institucion']) ? mysqli_real_escape_string($conexion, $_POST['institucion']) : '';
$usuario = isset($_POST['usuario']) ? mysqli_real_escape_string($conexion, $_POST['usuario']) : '';
$pagina_id = isset($_POST['pagina_id']) ? mysqli_real_escape_string($conexion, $_POST['pagina_id']) : '';
$fechaDesde = isset($_POST['fechaDesde']) ? mysqli_real_escape_string($conexion, $_POST['fechaDesde']) : '';
$fechaHasta = isset($_POST['fechaHasta']) ? mysqli_real_escape_string($conexion, $_POST['fechaHasta']) : '';

// Construir filtros
$filtros = [];
$filtros[] = "ins_enviroment = '" . ENVIROMENT . "'";

if (!empty($busqueda)) {
    $filtros[] = "(pagp_pagina LIKE '%$busqueda%' OR hil_url LIKE '%$busqueda%' OR hil_ip LIKE '%$busqueda%' OR hil_so LIKE '%$busqueda%')";
}

if (!empty($institucion) && $institucion !== 'todos') {
    $filtros[] = "hil_institucion = '$institucion'";
}

if (!empty($usuario) && $usuario !== 'todos') {
    $filtros[] = "hil_usuario = '$usuario'";
}

if (!empty($pagina_id) && $pagina_id !== 'todos') {
    $filtros[] = "hil_titulo = '$pagina_id'";
}

if (!empty($fechaDesde)) {
    $filtros[] = "DATE(hil_fecha) >= '$fechaDesde'";
}

if (!empty($fechaHasta)) {
    $filtros[] = "DATE(hil_fecha) <= '$fechaHasta'";
}

$whereClause = count($filtros) > 0 ? "WHERE " . implode(" AND ", $filtros) : "";

try {
    // Contar total de registros
    $consultaTotal = mysqli_query($conexion, "
        SELECT COUNT(*) as total 
        FROM " . $baseDatosServicios . ".seguridad_historial_acciones
        INNER JOIN " . $baseDatosServicios . ".instituciones ON ins_id = hil_institucion
        LEFT JOIN " . $baseDatosServicios . ".paginas_publicidad ON pagp_id = hil_titulo
        $whereClause
    ");
    $totalRegistros = mysqli_fetch_array($consultaTotal, MYSQLI_BOTH)['total'];
    $totalPaginas = ceil($totalRegistros / $porPagina);
    
    // Obtener registros con paginación
    $consulta = mysqli_query($conexion, "
        SELECT 
            h.hil_id, h.hil_usuario, h.hil_url, h.hil_titulo, h.hil_fecha,
            h.hil_ip, h.hil_so, h.hil_institucion, h.hil_pagina_anterior,
            h.hil_tiempo_carga, h.hil_usuario_autologin, h.hil_momento,
            h.hil_uso_memoria_mb, h.hil_pico_memoria_mb,
            i.ins_nombre, i.ins_siglas, i.ins_bd, i.ins_year_default,
            p.pagp_pagina, p.pagp_id
        FROM " . $baseDatosServicios . ".seguridad_historial_acciones h
        INNER JOIN " . $baseDatosServicios . ".instituciones i ON i.ins_id = h.hil_institucion
        LEFT JOIN " . $baseDatosServicios . ".paginas_publicidad p ON p.pagp_id = h.hil_titulo
        $whereClause
        ORDER BY h.hil_id DESC
        LIMIT $inicio, $porPagina
    ");
    
    $registros = [];
    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
        // Obtener nombre del usuario
        $responsable = "Sistema";
        if ($resultado['hil_usuario'] != 0) {
            try {
                $consultaResponsable = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(
                    "AND uss_id='" . $resultado['hil_usuario'] . "'", 
                    $resultado['hil_institucion'], 
                    $resultado['ins_year_default']
                );
                $datosResponsable = mysqli_fetch_array($consultaResponsable, MYSQLI_BOTH);
                if ($datosResponsable) {
                    $responsable = UsuariosPadre::nombreCompletoDelUsuario($datosResponsable);
                }
            } catch (Exception $e) {
                $responsable = "Usuario #" . $resultado['hil_usuario'];
            }
        }
        
        // Obtener usuario autologin
        $ussAutologin = "NO";
        if ($resultado['hil_usuario_autologin'] != 0) {
            try {
                $consultaUssAutologin = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(
                    "AND uss_id='" . $resultado['hil_usuario_autologin'] . "'", 
                    $resultado['hil_institucion'], 
                    $resultado['ins_year_default']
                );
                $datosUssAutologin = mysqli_fetch_array($consultaUssAutologin, MYSQLI_BOTH);
                if ($datosUssAutologin) {
                    $ussAutologin = UsuariosPadre::nombreCompletoDelUsuario($datosUssAutologin);
                }
            } catch (Exception $e) {
                $ussAutologin = "Usuario #" . $resultado['hil_usuario_autologin'];
            }
        }
        
        $registros[] = [
            'hil_id' => $resultado['hil_id'],
            'hil_usuario' => $resultado['hil_usuario'],
            'hil_url' => $resultado['hil_url'],
            'hil_titulo' => $resultado['hil_titulo'],
            'hil_fecha' => $resultado['hil_fecha'],
            'hil_ip' => $resultado['hil_ip'],
            'hil_so' => $resultado['hil_so'],
            'hil_institucion' => $resultado['hil_institucion'],
            'hil_pagina_anterior' => $resultado['hil_pagina_anterior'],
            'hil_tiempo_carga' => $resultado['hil_tiempo_carga'],
            'hil_usuario_autologin' => $resultado['hil_usuario_autologin'],
            'hil_momento' => $resultado['hil_momento'],
            'hil_uso_memoria_mb' => $resultado['hil_uso_memoria_mb'],
            'hil_pico_memoria_mb' => $resultado['hil_pico_memoria_mb'],
            'ins_nombre' => $resultado['ins_nombre'],
            'ins_siglas' => $resultado['ins_siglas'],
            'ins_bd' => $resultado['ins_bd'],
            'ins_year_default' => $resultado['ins_year_default'],
            'pagp_pagina' => $resultado['pagp_pagina'],
            'pagp_id' => $resultado['pagp_id'],
            'responsable' => $responsable,
            'uss_autologin' => $ussAutologin
        ];
    }
    
    echo json_encode([
        'success' => true,
        'registros' => $registros,
        'paginacion' => [
            'paginaActual' => $pagina,
            'porPagina' => $porPagina,
            'totalRegistros' => $totalRegistros,
            'totalPaginas' => $totalPaginas
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar historial: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

