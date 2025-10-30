<?php
include("session.php");
$idPaginaInterna = 'DT0025';

Modulos::validarAccesoDirectoPaginas();
if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $idReporte = base64_decode($_GET["idR"]);
    $sql = "UPDATE ".BD_DISCIPLINA.".disciplina_reportes 
            SET dr_aprobacion_estudiante=1, dr_aprobacion_estudiante_fecha=now() 
            WHERE dr_id=? AND institucion=? AND year=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $idReporte, PDO::PARAM_STR);
    $stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="reportes-lista.php";</script>';
exit();