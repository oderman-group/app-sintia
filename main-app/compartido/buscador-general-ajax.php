<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");

// Verificar permisos
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$busqueda = isset($_REQUEST["query"]) ? trim($_REQUEST["query"]) : '';
$limite = 15; // Límite de resultados por categoría

// Si la búsqueda está vacía, retornar array vacío
if (empty($busqueda) || strlen($busqueda) < 2) {
    header('Content-Type: application/json');
    echo json_encode([
        'usuarios' => [],
        'estudiantes' => [],
        'asignaturas' => [],
        'areas' => [],
        'cursos' => [],
        'paginas' => [],
        'query' => $busqueda
    ]);
    exit();
}

$resultados = [
    'usuarios' => [],
    'estudiantes' => [],
    'asignaturas' => [],
    'areas' => [],
    'cursos' => [],
    'paginas' => [],
    'query' => $busqueda
];

try {
    // BÚSQUEDA DE USUARIOS (solo para Directivos y Devs) - TODAS LAS COMBINACIONES
    if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) {
        $busquedaLike = "%".$busqueda."%";
        
        $consultaUsuarios = mysqli_query($conexion, "SELECT 
            uss_id, 
            uss_nombre, 
            uss_nombre2,
            uss_apellido1, 
            uss_apellido2,
            uss_foto, 
            uss_tipo,
            uss_usuario,
            uss_email,
            uss_documento
        FROM ".BD_GENERAL.".usuarios 
        WHERE uss_bloqueado=0 
        AND uss_tipo != ".TIPO_ESTUDIANTE."
        AND institucion={$config['conf_id_institucion']} 
        AND year={$_SESSION["bd"]} 
        AND (
            uss_nombre LIKE '".$busquedaLike."' 
            OR uss_nombre2 LIKE '".$busquedaLike."' 
            OR uss_apellido1 LIKE '".$busquedaLike."' 
            OR uss_apellido2 LIKE '".$busquedaLike."' 
            OR uss_usuario LIKE '".$busquedaLike."'
            OR uss_email LIKE '".$busquedaLike."'
            OR uss_documento LIKE '".$busquedaLike."'
            -- Todas las combinaciones posibles de nombres y apellidos
            OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_apellido1)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_apellido2)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_nombre2)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_nombre2), ' ', TRIM(uss_apellido1)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_nombre2), ' ', TRIM(uss_apellido2)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_apellido1), ' ', TRIM(uss_apellido2)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_nombre2), ' ', TRIM(uss_apellido1), ' ', TRIM(uss_apellido2)) LIKE '".$busquedaLike."'
            -- Combinaciones con apellidos primero
            OR CONCAT(TRIM(uss_apellido1), ' ', TRIM(uss_nombre)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_apellido1), ' ', TRIM(uss_apellido2)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_apellido1), ' ', TRIM(uss_apellido2), ' ', TRIM(uss_nombre)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_apellido1), ' ', TRIM(uss_apellido2), ' ', TRIM(uss_nombre), ' ', TRIM(uss_nombre2)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_apellido1), ' ', TRIM(uss_nombre), ' ', TRIM(uss_nombre2)) LIKE '".$busquedaLike."'
            -- Combinaciones sin espacios
            OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(uss_apellido1), TRIM(uss_nombre)) LIKE '".$busquedaLike."'
        )
        ORDER BY uss_nombre ASC 
        LIMIT ".$limite);
        
        while ($usuario = mysqli_fetch_array($consultaUsuarios, MYSQLI_BOTH)) {
            $nombreCompleto = UsuariosPadre::nombreCompletoDelUsuario($usuario);
            
            // Determinar tipo de usuario
            $tipoUsuario = '';
            $colorTipo = '';
            $iconoTipo = '';
            switch ($usuario['uss_tipo']) {
                case TIPO_DEV:
                    $tipoUsuario = 'Desarrollador';
                    $colorTipo = '#667eea';
                    $iconoTipo = 'fa-code';
                    break;
                case TIPO_DIRECTIVO:
                    $tipoUsuario = 'Directivo';
                    $colorTipo = '#4facfe';
                    $iconoTipo = 'fa-user-tie';
                    break;
                case TIPO_DOCENTE:
                    $tipoUsuario = 'Docente';
                    $colorTipo = '#43e97b';
                    $iconoTipo = 'fa-chalkboard-teacher';
                    break;
                case TIPO_ACUDIENTE:
                    $tipoUsuario = 'Acudiente';
                    $colorTipo = '#fa709a';
                    $iconoTipo = 'fa-users';
                    break;
                case TIPO_ESTUDIANTE:
                    $tipoUsuario = 'Estudiante';
                    $colorTipo = '#f093fb';
                    $iconoTipo = 'fa-graduation-cap';
                    break;
            }
            
            // Determinar URL de edición
            $url = '';
            if ($usuario['uss_tipo'] == TIPO_ESTUDIANTE) {
                $url = 'estudiantes-editar.php?id='.base64_encode($usuario['uss_usuario']);
            } else {
                $url = 'usuarios-editar.php?id='.base64_encode($usuario['uss_id']);
            }
            
            $foto = !empty($usuario['uss_foto']) ? $usuario['uss_foto'] : 'default.png';
            
            $resultados['usuarios'][] = [
                'id' => $usuario['uss_id'],
                'nombre' => $nombreCompleto,
                'tipo' => $tipoUsuario,
                'tipoColor' => $colorTipo,
                'tipoIcono' => $iconoTipo,
                'foto' => $foto,
                'email' => $usuario['uss_email'],
                'documento' => $usuario['uss_documento'],
                'url' => $url
            ];
        }
    }
    
    // BÚSQUEDA DE ESTUDIANTES (solo para Directivos y Devs) - CON TODAS LAS COMBINACIONES
    if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) {
        $busquedaLike = "%".$busqueda."%";
        
        $consultaEstudiantes = mysqli_query($conexion, "SELECT 
            mat_id,
            mat_matricula,
            mat_nombres, 
            mat_nombre2,
            mat_primer_apellido, 
            mat_segundo_apellido,
            mat_foto,
            mat_documento,
            mat_email,
            mat_estado_matricula
        FROM ".BD_ACADEMICA.".academico_matriculas 
        WHERE institucion={$config['conf_id_institucion']} 
        AND year={$_SESSION["bd"]}
        AND (mat_estado_matricula = 1 OR mat_estado_matricula = 2)
        AND (
            mat_nombres LIKE '".$busquedaLike."' 
            OR mat_nombre2 LIKE '".$busquedaLike."' 
            OR mat_primer_apellido LIKE '".$busquedaLike."' 
            OR mat_segundo_apellido LIKE '".$busquedaLike."'
            OR mat_documento LIKE '".$busquedaLike."'
            OR mat_email LIKE '".$busquedaLike."'
            OR mat_matricula LIKE '".$busquedaLike."'
            -- Todas las combinaciones posibles
            OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_primer_apellido)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_segundo_apellido)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_nombre2)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_nombre2), ' ', TRIM(mat_primer_apellido)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_nombre2), ' ', TRIM(mat_segundo_apellido)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_primer_apellido), ' ', TRIM(mat_segundo_apellido)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_nombre2), ' ', TRIM(mat_primer_apellido), ' ', TRIM(mat_segundo_apellido)) LIKE '".$busquedaLike."'
            -- Con apellidos primero
            OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_nombres)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_segundo_apellido)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombres)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombres), ' ', TRIM(mat_nombre2)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_nombres), ' ', TRIM(mat_nombre2)) LIKE '".$busquedaLike."'
            -- Sin espacios
            OR CONCAT(TRIM(mat_nombres), TRIM(mat_primer_apellido)) LIKE '".$busquedaLike."'
            OR CONCAT(TRIM(mat_primer_apellido), TRIM(mat_nombres)) LIKE '".$busquedaLike."'
        )
        ORDER BY mat_nombres ASC 
        LIMIT ".$limite);
        
        while ($estudiante = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH)) {
            $nombreCompleto = trim($estudiante['mat_nombres']);
            if (!empty($estudiante['mat_nombre2'])) {
                $nombreCompleto .= ' ' . trim($estudiante['mat_nombre2']);
            }
            if (!empty($estudiante['mat_primer_apellido'])) {
                $nombreCompleto .= ' ' . trim($estudiante['mat_primer_apellido']);
            }
            if (!empty($estudiante['mat_segundo_apellido'])) {
                $nombreCompleto .= ' ' . trim($estudiante['mat_segundo_apellido']);
            }
            
            $foto = !empty($estudiante['mat_foto']) ? $estudiante['mat_foto'] : 'default.png';
            
            $estadoMatricula = '';
            switch ($estudiante['mat_estado_matricula']) {
                case 1:
                    $estadoMatricula = 'Matriculado';
                    break;
                case 2:
                    $estadoMatricula = 'Asistente';
                    break;
                case 3:
                    $estadoMatricula = 'Cancelado';
                    break;
                case 4:
                    $estadoMatricula = 'Retirado';
                    break;
            }
            
            $resultados['estudiantes'][] = [
                'id' => $estudiante['mat_id'],
                'matricula' => $estudiante['mat_matricula'],
                'nombre' => $nombreCompleto,
                'foto' => $foto,
                'email' => $estudiante['mat_email'],
                'documento' => $estudiante['mat_documento'],
                'estado' => $estadoMatricula,
                'url' => 'estudiantes-editar.php?id='.base64_encode($estudiante['mat_id'])
            ];
        }
    }
    
    // BÚSQUEDA DE ASIGNATURAS (solo para Directivos y Devs)
    if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) {
        $busquedaLike = "%".$busqueda."%";
        
        $consultaAsignaturas = mysqli_query($conexion, "SELECT 
            mat_id, 
            mat_nombre,
            mat_valor,
            mat_estado,
            mat_siglas,
            mat_codigo
        FROM ".BD_ACADEMICA.".academico_materias 
        WHERE institucion={$config['conf_id_institucion']} 
        AND year={$_SESSION["bd"]}
        AND (
            mat_nombre LIKE '".$busquedaLike."' 
            OR mat_id LIKE '".$busquedaLike."'
            OR mat_siglas LIKE '".$busquedaLike."'
            OR mat_codigo LIKE '".$busquedaLike."'
        )
        ORDER BY mat_nombre ASC 
        LIMIT ".$limite);
        
        while ($asignatura = mysqli_fetch_array($consultaAsignaturas, MYSQLI_BOTH)) {
            $resultados['asignaturas'][] = [
                'id' => $asignatura['mat_id'],
                'nombre' => $asignatura['mat_nombre'],
                'estado' => $asignatura['mat_estado'] == 1 ? 'Activa' : 'Inactiva',
                'valor' => $asignatura['mat_valor'],
                'codigo' => $asignatura['mat_codigo'],
                'siglas' => $asignatura['mat_siglas'],
                'url' => 'asignaturas-editar.php?id='.base64_encode($asignatura['mat_id'])
            ];
        }
    }
    
    // BÚSQUEDA DE ÁREAS (solo para Directivos y Devs)
    if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) {
        $busquedaLike = "%".$busqueda."%";
        
        $consultaAreas = mysqli_query($conexion, "SELECT 
            ar_id, 
            ar_nombre,
            ar_estado
        FROM ".BD_ACADEMICA.".academico_areas 
        WHERE institucion={$config['conf_id_institucion']} 
        AND year={$_SESSION["bd"]}
        AND (
            ar_nombre LIKE '".$busquedaLike."' 
            OR ar_id LIKE '".$busquedaLike."'
        )
        ORDER BY ar_nombre ASC 
        LIMIT ".$limite);
        
        while ($area = mysqli_fetch_array($consultaAreas, MYSQLI_BOTH)) {
            $resultados['areas'][] = [
                'id' => $area['ar_id'],
                'nombre' => $area['ar_nombre'],
                'estado' => $area['ar_estado'] == 1 ? 'Activa' : 'Inactiva',
                'url' => 'areas-editar.php?id='.base64_encode($area['ar_id'])
            ];
        }
    }
    
    // BÚSQUEDA DE CURSOS/GRADOS (solo para Directivos y Devs)
    if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) {
        $busquedaLike = "%".$busqueda."%";
        
        $consultaCursos = mysqli_query($conexion, "SELECT 
            gra_id, 
            gra_nombre,
            gra_codigo,
            gra_estado,
            gra_formato_boletin
        FROM ".BD_ACADEMICA.".academico_grados 
        WHERE institucion={$config['conf_id_institucion']} 
        AND year={$_SESSION["bd"]}
        AND (
            gra_nombre LIKE '".$busquedaLike."' 
            OR gra_codigo LIKE '".$busquedaLike."'
            OR gra_id LIKE '".$busquedaLike."'
            OR gra_formato_boletin LIKE '".$busquedaLike."'
        )
        ORDER BY gra_nombre ASC 
        LIMIT ".$limite);
        
        while ($curso = mysqli_fetch_array($consultaCursos, MYSQLI_BOTH)) {
            $resultados['cursos'][] = [
                'id' => $curso['gra_id'],
                'nombre' => $curso['gra_nombre'],
                'codigo' => $curso['gra_codigo'],
                'estado' => $curso['gra_estado'] == 1 ? 'Activo' : 'Inactivo',
                'url' => 'cursos-editar.php?id='.base64_encode($curso['gra_id'])
            ];
        }
    }
    
    // BÚSQUEDA DE PÁGINAS (para todos los usuarios)
    $busquedaLike = "%".$busqueda."%";
    
    $consultaPaginas = mysqli_query($conexion, "SELECT 
        pagp_id,
        pagp_pagina,
        pagp_descripcion,
        pagp_ruta,
        pagp_parametro,
        pagp_palabras_claves
    FROM ".$baseDatosServicios.".paginas_publicidad 
    WHERE pagp_navegable=1 
    AND (pagp_tipo_usuario='".$datosUsuarioActual['uss_tipo']."' OR pagp_tipo_usuario=".TIPO_DEV.")
    AND (
        pagp_pagina LIKE '".$busquedaLike."' 
        OR pagp_ruta LIKE '".$busquedaLike."' 
        OR pagp_palabras_claves LIKE '".$busquedaLike."'
        OR pagp_descripcion LIKE '".$busquedaLike."'
    )
    ORDER BY pagp_pagina ASC 
    LIMIT ".$limite);
    
    while ($pagina = mysqli_fetch_array($consultaPaginas, MYSQLI_BOTH)) {
        $ruta = $pagina['pagp_ruta'];
        if ($pagina['pagp_parametro'] != 1) {
            $ruta = "page-info.php?idmsg=303&idPagina='".$pagina['pagp_id']."'";
        }
        
        $resultados['paginas'][] = [
            'id' => $pagina['pagp_id'],
            'nombre' => $pagina['pagp_pagina'],
            'descripcion' => $pagina['pagp_descripcion'],
            'url' => $ruta
        ];
    }
    
} catch (Exception $e) {
    $resultados['error'] = 'Error en la búsqueda: ' . $e->getMessage();
}

// Devolver los resultados como JSON
header('Content-Type: application/json');
echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
exit();

