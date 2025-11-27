<?php
include("session.php");
require_once(ROOT_PATH . "/main-app/class/PreguntaGeneral.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0317';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

if(!empty($_POST["respuestas"])){
    $respuestasPregunta = PreguntaGeneral::traerRespuestasPreguntas($conexion, $config, $_POST['idP']);
    $idRespuestas = array();
    foreach ($respuestasPregunta as $arrayRespuesta) {
        $idRespuestas[] = $arrayRespuesta['gpr_id_respuesta'];
    }
    
    // Migrado a PDO - Consultas preparadas
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    
    $resultadoAgregar= array_diff($_POST["respuestas"],$idRespuestas);
    if($resultadoAgregar){
        $sqlInsert = "INSERT INTO ".BD_ADMIN.".general_preguntas_respuestas(gpr_id_pregunta, gpr_id_respuesta) VALUES (?, ?)";
        $stmtInsert = $conexionPDO->prepare($sqlInsert);
        foreach ($resultadoAgregar as $idRespuestaAgregar) {
            try{
                $stmtInsert->bindParam(1, $_POST["idP"], PDO::PARAM_STR);
                $stmtInsert->bindParam(2, $idRespuestaAgregar, PDO::PARAM_STR);
                $stmtInsert->execute();
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
        }
    }

    $resultadoEliminar= array_diff($idRespuestas,$_POST["respuestas"]);
    if($resultadoEliminar){
        $sqlDelete = "DELETE FROM ".BD_ADMIN.".general_preguntas_respuestas WHERE gpr_id_pregunta=? AND gpr_id_respuesta=?";
        $stmtDelete = $conexionPDO->prepare($sqlDelete);
        foreach ($resultadoEliminar as $idRespuestaEliminar) {
            try{
                $stmtDelete->bindParam(1, $_POST["idP"], PDO::PARAM_STR);
                $stmtDelete->bindParam(2, $idRespuestaEliminar, PDO::PARAM_STR);
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
        $sqlDeleteAll = "DELETE FROM ".BD_ADMIN.".general_preguntas_respuestas WHERE gpr_id_pregunta=?";
        $stmtDeleteAll = $conexionPDO->prepare($sqlDeleteAll);
        $stmtDeleteAll->bindParam(1, $_POST["idP"], PDO::PARAM_STR);
        $stmtDeleteAll->execute();
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="preguntas.php?success=SC_DT_2&id='.base64_encode($_POST["idP"]).'";</script>';
exit();
