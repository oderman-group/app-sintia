<?php
/**
 * Archivo AJAX para obtener los eventos del calendario de un estudiante
 * Usado por acudientes para mostrar el cronograma en un modal
 */

include("../compartido/session-compartida.php");
header('Content-Type: application/json');

if(empty($_GET['usrEstud'])){
    echo json_encode(['success' => false, 'message' => 'Parámetro usrEstud requerido']);
    exit();
}

$usrEstud = base64_decode($_GET['usrEstud']);

// Verificar que el acudiente tenga acceso a este estudiante
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

$usrEstudEsc = mysqli_real_escape_string($conexion, $usrEstud);
$idAcudienteEsc = mysqli_real_escape_string($conexion, $datosUsuarioActual['uss_id']);
$institucionEsc = (int)$config['conf_id_institucion'];
$yearEsc = mysqli_real_escape_string($conexion, $_SESSION["bd"]);

$consultaEstudiante = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas 
    WHERE mat_id_usuario='".$usrEstudEsc."' AND mat_acudiente='".$idAcudienteEsc."' 
    AND institucion=".$institucionEsc." AND year='".$yearEsc."'");

if(mysqli_num_rows($consultaEstudiante) == 0){
    echo json_encode(['success' => false, 'message' => 'No tiene acceso a este estudiante']);
    exit();
}

$datosEstudiante = mysqli_fetch_array($consultaEstudiante, MYSQLI_BOTH);

// Establecer el estudiante actual para el cálculo de actividades
$datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($datosEstudiante['mat_id']);

// Obtener eventos directamente en formato JSON
require_once(ROOT_PATH."/main-app/class/Cronograma.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$usrEstudParam = $_GET['usrEstud'];

// Verificar si hay un filtro de carga seleccionado
$cargaFiltro = '';
if(!empty($_GET['filtro_carga'])){
    $cargaFiltro = base64_decode($_GET['filtro_carga']);
}

// Obtener todas las cargas del estudiante
$idGrado = !empty($datosEstudianteActual['mat_grado']) ? (string)$datosEstudianteActual['mat_grado'] : '';
$idGrupo = !empty($datosEstudianteActual['mat_grupo']) ? (string)$datosEstudianteActual['mat_grupo'] : '';

$cCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $idGrado, $idGrupo);

$eventosArray = [];
$totalActividades = 0;
$totalPendientes = 0;
$totalHoy = 0;
$fechaHoy = new DateTime();
$fechaHoy->setTime(0, 0, 0);

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
        $fechaCronograma = new DateTime($resultado["agno"].'-'.($resultado["mes"]+1).'-'.$resultado["dia"]);
        $fechaCronograma->setTime(0, 0, 0);
        
        $totalActividades++;
        if($fechaCronograma >= $fechaHoy){
            $totalPendientes++;
        }
        if($fechaCronograma->format('Y-m-d') == $fechaHoy->format('Y-m-d')){
            $totalHoy++;
        }
        
        // Formatear fecha para mostrar
        $fechaFormateada = '';
        $fecha = $resultado["agno"].'-'.str_pad($resultado["mes"]+1, 2, '0', STR_PAD_LEFT).'-'.str_pad($resultado["dia"], 2, '0', STR_PAD_LEFT);
        if(!empty($fecha) && $fecha != '0000-00-00'){
            try {
                $fechaObj = new DateTime($fecha);
                $fechaFormateada = $fechaObj->format('d/m/Y');
            } catch(Exception $e) {
                $fechaFormateada = $fecha;
            }
        }
        
        $eventosArray[] = [
            'title' => $resultado["cro_tema"] . ' - ' . $materiaNombre,
            'start' => $resultado["agno"].'-'.str_pad($resultado["mes"]+1, 2, '0', STR_PAD_LEFT).'-'.str_pad($resultado["dia"], 2, '0', STR_PAD_LEFT).'T06:00:00',
            'backgroundColor' => $resultado["cro_color"],
            'borderColor' => $resultado["cro_color"],
            'textColor' => '#ffffff',
            'url' => '../estudiante/cronograma-detalles.php?idR='.base64_encode($resultado["cro_id"]).'&usrEstud='.$usrEstudParam.'&carga='.base64_encode($idCarga).'&periodo='.base64_encode($periodo),
            'tipo' => 'cronograma',
            'detalle' => [
                'titulo' => $resultado["cro_tema"],
                'descripcion' => '',
                'fecha' => $fechaFormateada,
                'recursos' => !empty($resultado["cro_recursos"]) ? $resultado["cro_recursos"] : ''
            ]
        ];
    }
    
    // 2. TAREAS PARA LA CASA
    $consultaTareas = Actividades::actividadesCargasPeriodos($conexion, $config, $idCarga, $periodo);
    while($tarea = mysqli_fetch_array($consultaTareas, MYSQLI_BOTH)){
        if(!empty($tarea['tar_fecha_entrega']) && $tarea['tar_fecha_entrega'] != '0000-00-00'){
            $fechaEntrega = new DateTime($tarea['tar_fecha_entrega']);
            $fechaEntrega->setTime(0, 0, 0);
            
            $totalActividades++;
            if($fechaEntrega >= $fechaHoy){
                $totalPendientes++;
            }
            if($fechaEntrega->format('Y-m-d') == $fechaHoy->format('Y-m-d')){
                $totalHoy++;
            }
            
            // Formatear fecha para mostrar
            $fechaFormateada = '';
            if(!empty($tarea['tar_fecha_entrega']) && $tarea['tar_fecha_entrega'] != '0000-00-00'){
                try {
                    $fechaObj = new DateTime($tarea['tar_fecha_entrega']);
                    $fechaFormateada = $fechaObj->format('d/m/Y');
                } catch(Exception $e) {
                    $fechaFormateada = $tarea['tar_fecha_entrega'];
                }
            }
            
            $eventosArray[] = [
                'title' => $tarea['tar_titulo'] . ' - ' . $materiaNombre,
                'start' => $tarea['tar_fecha_entrega'].'T23:59:00',
                'backgroundColor' => '#3498db',
                'borderColor' => '#2980b9',
                'textColor' => '#ffffff',
                'url' => '../estudiante/actividades-ver.php?idR='.base64_encode($tarea['tar_id']).'&usrEstud='.$usrEstudParam.'&carga='.base64_encode($idCarga).'&periodo='.base64_encode($periodo),
                'tipo' => 'tarea',
                'detalle' => [
                    'titulo' => $tarea['tar_titulo'],
                    'descripcion' => !empty($tarea['tar_descripcion']) ? $tarea['tar_descripcion'] : '',
                    'fecha' => $fechaFormateada
                ]
            ];
        }
    }
    
    // 3. EXÁMENES PROGRAMADOS
    $consultaEvaluaciones = Evaluaciones::consultaEvaluacionCargasPeriodos($conexion, $config, $idCarga, $periodo);
    while($evaluacion = mysqli_fetch_array($consultaEvaluaciones, MYSQLI_BOTH)){
        if(!empty($evaluacion['eva_hasta']) && $evaluacion['eva_hasta'] != '0000-00-00 00:00:00'){
            $fechaHasta = new DateTime($evaluacion['eva_hasta']);
            $fechaHastaSolo = clone $fechaHasta;
            $fechaHastaSolo->setTime(0, 0, 0);
            
            $totalActividades++;
            if($fechaHastaSolo >= $fechaHoy){
                $totalPendientes++;
            }
            if($fechaHastaSolo->format('Y-m-d') == $fechaHoy->format('Y-m-d')){
                $totalHoy++;
            }
            
            // Formatear fecha para mostrar
            $fechaFormateada = '';
            if(!empty($evaluacion['eva_hasta']) && $evaluacion['eva_hasta'] != '0000-00-00 00:00:00'){
                try {
                    $fechaObj = new DateTime($evaluacion['eva_hasta']);
                    $fechaFormateada = $fechaObj->format('d/m/Y H:i');
                } catch(Exception $e) {
                    $fechaFormateada = $evaluacion['eva_hasta'];
                }
            }
            
            $eventosArray[] = [
                'title' => $evaluacion['eva_nombre'] . ' - ' . $materiaNombre,
                'start' => $evaluacion['eva_hasta'],
                'backgroundColor' => '#e74c3c',
                'borderColor' => '#c0392b',
                'textColor' => '#ffffff',
                'url' => '../estudiante/evaluaciones-ver.php?idR='.base64_encode($evaluacion['eva_id']).'&usrEstud='.$usrEstudParam.'&carga='.base64_encode($idCarga).'&periodo='.base64_encode($periodo),
                'tipo' => 'examen',
                'detalle' => [
                    'titulo' => $evaluacion['eva_nombre'],
                    'descripcion' => !empty($evaluacion['eva_descripcion']) ? $evaluacion['eva_descripcion'] : '',
                    'fecha' => $fechaFormateada
                ]
            ];
        }
    }
    
    // 4. CLASES PROGRAMADAS
    $consultaClases = Clases::traerClasesCargaPeriodo($conexion, $config, $idCarga, $periodo);
    while($clase = mysqli_fetch_array($consultaClases, MYSQLI_BOTH)){
        if(!empty($clase['cls_fecha']) && $clase['cls_fecha'] != '0000-00-00'){
            $fechaClase = new DateTime($clase['cls_fecha']);
            $fechaClase->setTime(0, 0, 0);
            
            $totalActividades++;
            if($fechaClase >= $fechaHoy){
                $totalPendientes++;
            }
            if($fechaClase->format('Y-m-d') == $fechaHoy->format('Y-m-d')){
                $totalHoy++;
            }
            
            // Formatear fecha para mostrar
            $fechaFormateada = '';
            if(!empty($clase['cls_fecha']) && $clase['cls_fecha'] != '0000-00-00'){
                try {
                    $fechaObj = new DateTime($clase['cls_fecha']);
                    $fechaFormateada = $fechaObj->format('d/m/Y');
                } catch(Exception $e) {
                    $fechaFormateada = $clase['cls_fecha'];
                }
            }
            
            $eventosArray[] = [
                'title' => $clase['cls_tema'] . ' - ' . $materiaNombre,
                'start' => $clase['cls_fecha'].'T08:00:00',
                'backgroundColor' => '#27ae60',
                'borderColor' => '#229954',
                'textColor' => '#ffffff',
                'url' => '../estudiante/clases-ver.php?idR='.base64_encode($clase['cls_id']).'&usrEstud='.$usrEstudParam.'&carga='.base64_encode($idCarga).'&periodo='.base64_encode($periodo),
                'tipo' => 'clase',
                'detalle' => [
                    'titulo' => $clase['cls_tema'],
                    'descripcion' => !empty($clase['cls_descripcion']) ? $clase['cls_descripcion'] : '',
                    'fecha' => $fechaFormateada
                ]
            ];
        }
    }
    
    // 5. ACTIVIDADES CALIFICABLES
    $consultaActividades = Actividades::consultaActividadesCarga($config, $idCarga, $periodo);
    while($actividad = mysqli_fetch_array($consultaActividades, MYSQLI_BOTH)){
        if(!empty($actividad['act_fecha']) && $actividad['act_fecha'] != '0000-00-00'){
            $fechaActividad = new DateTime($actividad['act_fecha']);
            $fechaActividad->setTime(0, 0, 0);
            
            $totalActividades++;
            if($fechaActividad >= $fechaHoy){
                $totalPendientes++;
            }
            if($fechaActividad->format('Y-m-d') == $fechaHoy->format('Y-m-d')){
                $totalHoy++;
            }
            
            $nombreActividad = !empty($actividad['act_descripcion']) ? $actividad['act_descripcion'] : 'Actividad Calificable';
            if(strlen($nombreActividad) > 50){
                $nombreActividad = substr($nombreActividad, 0, 47) . '...';
            }
            
            // Formatear fecha para mostrar
            $fechaFormateada = '';
            if(!empty($actividad['act_fecha']) && $actividad['act_fecha'] != '0000-00-00'){
                try {
                    $fechaObj = new DateTime($actividad['act_fecha']);
                    $fechaFormateada = $fechaObj->format('d/m/Y');
                } catch(Exception $e) {
                    $fechaFormateada = $actividad['act_fecha'];
                }
            }
            
            $eventosArray[] = [
                'title' => $nombreActividad . ' - ' . $materiaNombre,
                'start' => $actividad['act_fecha'].'T10:00:00',
                'backgroundColor' => '#9b59b6',
                'borderColor' => '#8e44ad',
                'textColor' => '#ffffff',
                'url' => '../estudiante/calificaciones.php?carga='.base64_encode($idCarga).'&periodo='.base64_encode($periodo).'&usrEstud='.$usrEstudParam,
                'tipo' => 'actividad_calificable',
                'detalle' => [
                    'titulo' => $nombreActividad,
                    'descripcion' => '',
                    'fecha' => $fechaFormateada
                ]
            ];
        }
    }
}

echo json_encode([
    'success' => true,
    'eventos' => $eventosArray,
    'totalActividades' => $totalActividades,
    'totalPendientes' => $totalPendientes,
    'totalHoy' => $totalHoy
]);
