<?php
/**
 * Archivo AJAX para obtener los detalles de una actividad
 * Usado por acudientes para mostrar detalles en un modal
 */

include("../compartido/session-compartida.php");
header('Content-Type: application/json');

if(empty($_GET['tipo']) || empty($_GET['params'])){
    echo json_encode(['success' => false, 'message' => 'Par?metros requeridos']);
    exit();
}

$tipo = $_GET['tipo'];
$paramsJson = $_GET['params'];

// Log para depuraci?n
error_log("Tipo recibido: " . $tipo);
error_log("Params JSON recibido: " . $paramsJson);

$params = json_decode($paramsJson, true);

// Si json_decode falla, intentar decodificar manualmente
if(!$params && !empty($paramsJson)){
    // Intentar decodificar como si fuera una cadena URL-encoded
    parse_str(urldecode($paramsJson), $params);
}

// Si a?n no hay params, intentar parsear directamente
if(!$params && !empty($paramsJson)){
    $params = [];
    // Intentar extraer par?metros de diferentes formas
    if(strpos($paramsJson, '{') === 0){
        // Es JSON
        $params = json_decode($paramsJson, true);
    } else {
        // Es query string
        parse_str($paramsJson, $params);
    }
}

if(!$params || !is_array($params)){
    error_log("Error: No se pudieron decodificar los parámetros. JSON recibido: " . $paramsJson);
    echo json_encode([
        'success' => false, 
        'message' => 'Error al decodificar parámetros',
        'debug' => [
            'paramsJson' => $paramsJson,
            'tipo' => $tipo
        ]
    ]);
    exit();
}

error_log("Parámetros decodificados: " . print_r($params, true));

// Verificar acceso del acudiente si hay usrEstud
$usrEstud = '';
if(!empty($_GET['usrEstud'])){
    $usrEstud = base64_decode($_GET['usrEstud']);
    
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
}

require_once(ROOT_PATH."/main-app/class/Cronograma.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$detalle = [];

try {
    switch($tipo){
        case 'cronograma':
            if(!empty($params['idR'])){
                $idR = base64_decode($params['idR']);
                $cronograma = Cronograma::buscarCronograma($conexion, $config, $idR);
                
                if($cronograma && !empty($cronograma['cro_id'])){
                    $fecha = !empty($cronograma['cro_fecha']) ? $cronograma['cro_fecha'] : '';
                    if(empty($fecha) && !empty($cronograma['agno']) && !empty($cronograma['mes']) && !empty($cronograma['dia'])){
                        $fecha = $cronograma['agno'].'-'.str_pad($cronograma['mes'], 2, '0', STR_PAD_LEFT).'-'.str_pad($cronograma['dia'], 2, '0', STR_PAD_LEFT);
                    }
                    
                    // Formatear fecha para mostrar
                    if(!empty($fecha) && $fecha != '0000-00-00'){
                        try {
                            $fechaObj = new DateTime($fecha);
                            $fechaFormateada = $fechaObj->format('d/m/Y');
                        } catch(Exception $e) {
                            $fechaFormateada = $fecha;
                        }
                    } else {
                        $fechaFormateada = '';
                    }
                    
                    $detalle = [
                        'titulo' => $cronograma['cro_tema'],
                        'descripcion' => '', // Los cronogramas no tienen descripción separada, el tema es el contenido
                        'fecha' => $fechaFormateada,
                        'recursos' => !empty($cronograma['cro_recursos']) ? $cronograma['cro_recursos'] : ''
                    ];
                }
            }
            break;
            
        case 'tarea':
            if(!empty($params['idR'])){
                $idR = base64_decode($params['idR']);
                $tarea = Actividades::traerDatosActividades($conexion, $config, $idR);
                
                if($tarea && !empty($tarea['tar_id'])){
                    // Formatear fecha
                    $fechaFormateada = '';
                    if(!empty($tarea['tar_fecha_entrega']) && $tarea['tar_fecha_entrega'] != '0000-00-00'){
                        try {
                            $fechaObj = new DateTime($tarea['tar_fecha_entrega']);
                            $fechaFormateada = $fechaObj->format('d/m/Y');
                        } catch(Exception $e) {
                            $fechaFormateada = $tarea['tar_fecha_entrega'];
                        }
                    }
                    
                    $detalle = [
                        'titulo' => $tarea['tar_titulo'],
                        'descripcion' => !empty($tarea['tar_descripcion']) ? $tarea['tar_descripcion'] : '',
                        'fecha' => $fechaFormateada
                    ];
                }
            }
            break;
            
        case 'examen':
            if(!empty($params['idR'])){
                $idR = base64_decode($params['idR']);
                $evaluacion = Evaluaciones::consultaEvaluacion($conexion, $config, $idR);
                
                if($evaluacion && !empty($evaluacion['eva_id'])){
                    // Formatear fecha
                    $fechaFormateada = '';
                    if(!empty($evaluacion['eva_hasta']) && $evaluacion['eva_hasta'] != '0000-00-00 00:00:00'){
                        try {
                            $fechaObj = new DateTime($evaluacion['eva_hasta']);
                            $fechaFormateada = $fechaObj->format('d/m/Y H:i');
                        } catch(Exception $e) {
                            $fechaFormateada = $evaluacion['eva_hasta'];
                        }
                    }
                    
                    $detalle = [
                        'titulo' => $evaluacion['eva_nombre'],
                        'descripcion' => !empty($evaluacion['eva_descripcion']) ? $evaluacion['eva_descripcion'] : '',
                        'fecha' => $fechaFormateada
                    ];
                }
            }
            break;
            
        case 'clase':
            if(!empty($params['idR'])){
                $idR = base64_decode($params['idR']);
                $clase = Clases::traerDatosClases($conexion, $config, $idR);
                
                if($clase && !empty($clase['cls_id'])){
                    // Formatear fecha
                    $fechaFormateada = '';
                    if(!empty($clase['cls_fecha']) && $clase['cls_fecha'] != '0000-00-00'){
                        try {
                            $fechaObj = new DateTime($clase['cls_fecha']);
                            $fechaFormateada = $fechaObj->format('d/m/Y');
                        } catch(Exception $e) {
                            $fechaFormateada = $clase['cls_fecha'];
                        }
                    }
                    
                    $detalle = [
                        'titulo' => $clase['cls_tema'],
                        'descripcion' => !empty($clase['cls_descripcion']) ? $clase['cls_descripcion'] : '',
                        'fecha' => $fechaFormateada
                    ];
                }
            }
            break;
            
        case 'actividad_calificable':
            // Para actividades calificables, mostrar información básica
            if(!empty($params['carga']) && !empty($params['periodo'])){
                $idCarga = base64_decode($params['carga']);
                $periodo = base64_decode($params['periodo']);
                
                // Intentar obtener la actividad calificable
                $consultaActividad = Actividades::consultaActividadesCarga($config, $idCarga, $periodo);
                $actividad = null;
                while($act = mysqli_fetch_array($consultaActividad, MYSQLI_BOTH)){
                    if(!empty($act['act_fecha']) && $act['act_fecha'] != '0000-00-00'){
                        $actividad = $act;
                        break; // Tomar la primera actividad con fecha
                    }
                }
                
                if($actividad){
                    $nombreActividad = !empty($actividad['act_descripcion']) ? $actividad['act_descripcion'] : 'Actividad Calificable';
                    
                    // Formatear fecha
                    $fechaFormateada = '';
                    if(!empty($actividad['act_fecha']) && $actividad['act_fecha'] != '0000-00-00'){
                        try {
                            $fechaObj = new DateTime($actividad['act_fecha']);
                            $fechaFormateada = $fechaObj->format('d/m/Y');
                        } catch(Exception $e) {
                            $fechaFormateada = $actividad['act_fecha'];
                        }
                    }
                    
                    $detalle = [
                        'titulo' => $nombreActividad,
                        'descripcion' => '',
                        'fecha' => $fechaFormateada
                    ];
                } else {
                    $detalle = [
                        'titulo' => 'Actividad Calificable',
                        'descripcion' => '',
                        'fecha' => ''
                    ];
                }
            }
            break;
    }
    
    if(empty($detalle)){
        error_log("No se encontraron detalles. Tipo: " . $tipo . ", Parámetros: " . print_r($params, true));
        echo json_encode([
            'success' => false, 
            'message' => 'No se encontraron detalles para esta actividad',
            'debug' => [
                'tipo' => $tipo,
                'params' => $params
            ]
        ]);
        exit();
    }
    
    error_log("Detalle encontrado: " . print_r($detalle, true));
    
    echo json_encode([
        'success' => true,
        'detalle' => $detalle
    ]);
    
} catch(Exception $e) {
    error_log("Excepción en ajax-detalle-actividad.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener detalles: ' . $e->getMessage()
    ]);
}
