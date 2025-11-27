<?php
include("session.php");
require_once(ROOT_PATH . "/main-app/class/EvaluacionGeneral.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0315';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

if(!empty($_POST["preguntas"])){
    $preguntasEvaluacion = EvaluacionGeneral::traerPreguntasEvaluacion($conexion, $config, $_POST['idE']);
    $idPreguntas = array();
    foreach ($preguntasEvaluacion as $arrayPreguntas) {
        $idPreguntas[] = $arrayPreguntas['gep_id_pregunta'];
    }
    
    // Migrado a PDO - Consultas preparadas
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    
    $resultadoAgregar= array_diff($_POST["preguntas"],$idPreguntas);
    if($resultadoAgregar){
        $sqlInsert = "INSERT INTO ".BD_ADMIN.".general_evaluaciones_preguntas(gep_id_evaluacion, gep_id_pregunta) VALUES (?, ?)";
        $stmtInsert = $conexionPDO->prepare($sqlInsert);
        foreach ($resultadoAgregar as $idPreguntaAgregar) {
            try{
                $stmtInsert->bindParam(1, $_POST["idE"], PDO::PARAM_STR);
                $stmtInsert->bindParam(2, $idPreguntaAgregar, PDO::PARAM_STR);
                $stmtInsert->execute();
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
        }
    }

    $resultadoEliminar= array_diff($idPreguntas,$_POST["preguntas"]);
    if($resultadoEliminar){
        $sqlDelete = "DELETE FROM ".BD_ADMIN.".general_evaluaciones_preguntas WHERE gep_id_evaluacion=? AND gep_id_pregunta=?";
        $stmtDelete = $conexionPDO->prepare($sqlDelete);
        foreach ($resultadoEliminar as $idPreguntaEliminar) {
            try{
                $stmtDelete->bindParam(1, $_POST["idE"], PDO::PARAM_STR);
                $stmtDelete->bindParam(2, $idPreguntaEliminar, PDO::PARAM_STR);
                $stmtDelete->execute();
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
        }
    }
}else{
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sqlDeleteAll = "DELETE FROM ".BD_ADMIN.".general_evaluaciones_preguntas WHERE gep_id_evaluacion=?";
        $stmtDeleteAll = $conexionPDO->prepare($sqlDeleteAll);
        $stmtDeleteAll->bindParam(1, $_POST["idE"], PDO::PARAM_STR);
        $stmtDeleteAll->execute();
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="evaluaciones.php?success=SC_DT_2&id='.base64_encode($_POST["idE"]).'";</script>';
exit();
