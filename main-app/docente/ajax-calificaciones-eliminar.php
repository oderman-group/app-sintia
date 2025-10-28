<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0132';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

header('Content-Type: application/json; charset=utf-8');

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");

try {
    if (empty($_GET["idR"])) {
        throw new Exception("ID de actividad no proporcionado");
    }

    $idActividad = base64_decode($_GET["idR"]);
    $idIndicador = "";
    if (!empty($_GET['idIndicador'])) {
        $idIndicador = base64_decode($_GET['idIndicador']);
    }

    // Obtener información de la actividad antes de eliminar
    $sqlActividad = "SELECT aa.*, ai.ind_nombre FROM ".BD_ACADEMICA.".academico_actividades aa
    INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=aa.act_id_tipo AND ai.institucion=aa.institucion AND ai.year=aa.year
    WHERE aa.act_id=? AND aa.institucion=? AND aa.year=?";
    $parametrosActividad = [$idActividad, $config['conf_id_institucion'], $_SESSION["bd"]];
    $consultaActividad = BindSQL::prepararSQL($sqlActividad, $parametrosActividad);
    $actividadDatos = mysqli_fetch_array($consultaActividad, MYSQLI_BOTH);
    
    if (empty($actividadDatos)) {
        throw new Exception("Actividad no encontrada");
    }

    $indicadoresDatos = Indicadores::consultaIndicadorPeriodo($conexion, $config, $idIndicador, $cargaConsultaActual, $periodoConsultaActual);

    // "Borramos" la actividad
    Actividades::eliminarActividadCalificaciones($config, $cargaConsultaActual, $periodoConsultaActual, $idActividad);

    // Si los valores de las calificaciones son de forma automática, recalculamos porcentajes
    $nuevosPorcentajes = [];
    if($datosCargaActual['car_configuracion']==0){
        //Actualizamos el valor de todas las actividades del indicador
        Calificaciones::actualizarValorCalificacionesDeUnIndicador($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $indicadoresDatos);
        
        // Obtener los nuevos porcentajes de las actividades restantes del mismo indicador
        $sqlNuevosPorcentajes = "SELECT act_id, act_valor FROM ".BD_ACADEMICA.".academico_actividades 
        WHERE act_id_tipo=? AND act_id_carga=? AND act_periodo=? AND act_estado=1 AND institucion=? AND year=?";
        $parametrosNuevos = [$idIndicador, $cargaConsultaActual, $periodoConsultaActual, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consultaNuevos = BindSQL::prepararSQL($sqlNuevosPorcentajes, $parametrosNuevos);
        while($nuevo = mysqli_fetch_array($consultaNuevos, MYSQLI_BOTH)){
            $nuevosPorcentajes[$nuevo['act_id']] = $nuevo['act_valor'];
        }
    } else {
        // Si es configuración manual, solo obtenemos las actividades restantes sin cambiar porcentajes
        $sqlNuevosPorcentajes = "SELECT act_id, act_valor FROM ".BD_ACADEMICA.".academico_actividades 
        WHERE act_id_carga=? AND act_periodo=? AND act_estado=1 AND institucion=? AND year=?";
        $parametrosNuevos = [$cargaConsultaActual, $periodoConsultaActual, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consultaNuevos = BindSQL::prepararSQL($sqlNuevosPorcentajes, $parametrosNuevos);
        while($nuevo = mysqli_fetch_array($consultaNuevos, MYSQLI_BOTH)){
            $nuevosPorcentajes[$nuevo['act_id']] = $nuevo['act_valor'];
        }
    }

    // Obtener el porcentaje total actualizado
    $valores = Actividades::consultarValores($config, $cargaConsultaActual, $periodoConsultaActual);
    $porcentajeTotal = isset($valores[0]) && $valores[0] !== null ? floatval($valores[0]) : 0.0;

    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

    echo json_encode([
        'success' => true,
        'message' => 'Actividad eliminada exitosamente',
        'data' => [
            'actividad_id' => $idActividad,
            'actividad_descripcion' => $actividadDatos['act_descripcion'],
            'actividad_fecha' => $actividadDatos['act_fecha'],
            'actividad_valor' => $actividadDatos['act_valor'],
            'actividad_id_tipo' => $actividadDatos['act_id_tipo'],
            'indicador_nombre' => $actividadDatos['ind_nombre'],
            'id_nuevo_act' => $actividadDatos['id_nuevo'],
            'nuevos_porcentajes' => $nuevosPorcentajes,
            'porcentaje_total' => round($porcentajeTotal, 2),
            'configuracion_automatica' => $datosCargaActual['car_configuracion'] == 0,
            'tiene_evidencia' => $datosCargaActual['car_evidencia'] == 1,
            'tiene_indicador_manual' => ($datosCargaActual['car_indicador_automatico'] == 0 || $datosCargaActual['car_indicador_automatico'] == null)
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar la actividad: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
exit();

