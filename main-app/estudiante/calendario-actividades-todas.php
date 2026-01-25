<?php
/**
 * Este archivo obtiene todas las actividades del estudiante para el calendario
 * Incluye: cronogramas, tareas, exámenes, clases y actividades calificables
 */

require_once(ROOT_PATH."/main-app/class/Cronograma.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

// Determinar el usrEstud para las URLs
$usrEstudParam = !empty($_GET["usrEstud"]) ? $_GET["usrEstud"] : base64_encode($datosEstudianteActual['mat_id_usuario']);

// Verificar si hay un filtro de carga seleccionado
$cargaFiltro = '';
if(!empty($_GET['filtro_carga'])){
    $cargaFiltro = base64_decode($_GET['filtro_carga']);
}

// Obtener todas las cargas del estudiante
$idGrado = !empty($datosEstudianteActual['mat_grado']) ? (string)$datosEstudianteActual['mat_grado'] : '';
$idGrupo = !empty($datosEstudianteActual['mat_grupo']) ? (string)$datosEstudianteActual['mat_grupo'] : '';

$cCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $idGrado, $idGrupo);

$eventos = "";
$contReg = 1;

// Array para almacenar todas las cargas válidas
$cargasValidas = [];

// Primero, obtener todas las cargas válidas del estudiante
while($cargaTemp = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
    // Verificar si el estudiante está matriculado en cursos de extensión o complementarios
    if($cargaTemp['car_curso_extension']==1){
        $cursoExt = CargaAcademica::validarCursosComplementario($conexion, $config, $datosEstudianteActual['mat_id'], $cargaTemp['car_id']);
        if($cursoExt==0){continue;}
    }
    
    // Si hay un filtro de carga, solo incluir la carga seleccionada
    if(!empty($cargaFiltro) && $cargaTemp['car_id'] != $cargaFiltro){
        continue;
    }
    
    $cargasValidas[] = $cargaTemp;
}

// Procesar cada carga y obtener todas las actividades
foreach($cargasValidas as $carga){
    $idCarga = $carga['car_id'];
    $periodo = $carga['car_periodo'];
    $materiaNombre = $carga['mat_nombre'];
    
    // 1. CRONOGRAMAS
    $consultaCronograma = Cronograma::traerDatosCronograma($conexion, $config, $idCarga, $periodo);
    while($resultado = mysqli_fetch_array($consultaCronograma, MYSQLI_BOTH)){
        $resultado["mes"]--;
        $eventos .= '
        {
            title: "'.addslashes($resultado["cro_tema"]).' - '.addslashes($materiaNombre).'",
            start: new Date('.$resultado["agno"].', '.$resultado["mes"].', '.$resultado["dia"].', 6, 0),
            backgroundColor: "'.$resultado["cro_color"].'",
            borderColor: "'.$resultado["cro_color"].'",
            textColor: "#ffffff",
            url: "cronograma-detalles.php?idR='.base64_encode($resultado["cro_id"]).'&usrEstud='.$usrEstudParam.'&carga='.base64_encode($idCarga).'&periodo='.base64_encode($periodo).'",
            tipo: "cronograma"
        },
        ';
    }
    
    // 2. TAREAS PARA LA CASA (academico_actividad_tareas)
    $consultaTareas = Actividades::actividadesCargasPeriodos($conexion, $config, $idCarga, $periodo);
    while($tarea = mysqli_fetch_array($consultaTareas, MYSQLI_BOTH)){
        if(!empty($tarea['tar_fecha_entrega']) && $tarea['tar_fecha_entrega'] != '0000-00-00'){
            $fechaEntrega = new DateTime($tarea['tar_fecha_entrega']);
            $eventos .= '
            {
                title: "'.addslashes($tarea['tar_titulo']).' - '.addslashes($materiaNombre).'",
                start: new Date('.$fechaEntrega->format('Y').', '.($fechaEntrega->format('m')-1).', '.$fechaEntrega->format('d').', 23, 59),
                backgroundColor: "#3498db",
                borderColor: "#2980b9",
                textColor: "#ffffff",
                url: "actividades-ver.php?idR='.base64_encode($tarea['tar_id']).'&usrEstud='.$usrEstudParam.'",
                tipo: "tarea"
            },
            ';
        }
    }
    
    // 3. EXÁMENES PROGRAMADOS (academico_actividad_evaluaciones)
    $consultaEvaluaciones = Evaluaciones::consultaEvaluacionCargasPeriodos($conexion, $config, $idCarga, $periodo);
    while($evaluacion = mysqli_fetch_array($consultaEvaluaciones, MYSQLI_BOTH)){
        if(!empty($evaluacion['eva_hasta']) && $evaluacion['eva_hasta'] != '0000-00-00 00:00:00'){
            $fechaHasta = new DateTime($evaluacion['eva_hasta']);
            $eventos .= '
            {
                title: "'.addslashes($evaluacion['eva_nombre']).' - '.addslashes($materiaNombre).'",
                start: new Date('.$fechaHasta->format('Y').', '.($fechaHasta->format('m')-1).', '.$fechaHasta->format('d').', '.$fechaHasta->format('H').', '.$fechaHasta->format('i').'),
                backgroundColor: "#e74c3c",
                borderColor: "#c0392b",
                textColor: "#ffffff",
                url: "evaluaciones-ver.php?idR='.base64_encode($evaluacion['eva_id']).'&usrEstud='.$usrEstudParam.'",
                tipo: "examen"
            },
            ';
        }
    }
    
    // 4. CLASES PROGRAMADAS (academico_clases)
    $consultaClases = Clases::traerClasesCargaPeriodo($conexion, $config, $idCarga, $periodo);
    while($clase = mysqli_fetch_array($consultaClases, MYSQLI_BOTH)){
        if(!empty($clase['cls_fecha']) && $clase['cls_fecha'] != '0000-00-00'){
            $fechaClase = new DateTime($clase['cls_fecha']);
            $eventos .= '
            {
                title: "'.addslashes($clase['cls_tema']).' - '.addslashes($materiaNombre).'",
                start: new Date('.$fechaClase->format('Y').', '.($fechaClase->format('m')-1).', '.$fechaClase->format('d').', 8, 0),
                backgroundColor: "#27ae60",
                borderColor: "#229954",
                textColor: "#ffffff",
                url: "clases-ver.php?idR='.base64_encode($clase['cls_id']).'&usrEstud='.$usrEstudParam.'",
                tipo: "clase"
            },
            ';
        }
    }
    
    // 5. ACTIVIDADES CALIFICABLES (academico_actividades)
    $consultaActividades = Actividades::consultaActividadesCarga($config, $idCarga, $periodo);
    while($actividad = mysqli_fetch_array($consultaActividades, MYSQLI_BOTH)){
        // Verificar si tiene fecha asignada
        if(!empty($actividad['act_fecha']) && $actividad['act_fecha'] != '0000-00-00'){
            $fechaActividad = new DateTime($actividad['act_fecha']);
            // Obtener el nombre del indicador si está disponible
            $nombreActividad = !empty($actividad['act_descripcion']) ? $actividad['act_descripcion'] : 'Actividad Calificable';
            // Limitar el nombre si es muy largo
            if(strlen($nombreActividad) > 50){
                $nombreActividad = substr($nombreActividad, 0, 47) . '...';
            }
            $eventos .= '
            {
                title: "'.addslashes($nombreActividad).' - '.addslashes($materiaNombre).'",
                start: new Date('.$fechaActividad->format('Y').', '.($fechaActividad->format('m')-1).', '.$fechaActividad->format('d').', 10, 0),
                backgroundColor: "#9b59b6",
                borderColor: "#8e44ad",
                textColor: "#ffffff",
                url: "calificaciones.php?carga='.base64_encode($idCarga).'&periodo='.base64_encode($periodo).'&usrEstud='.$usrEstudParam.'",
                tipo: "actividad_calificable"
            },
            ';
        }
    }
}

// Eliminar la última coma y espacios en blanco
$eventos = rtrim($eventos, ', ');
// Si no hay eventos, establecer un array vacío
if(empty(trim($eventos))){
    $eventos = '';
}
