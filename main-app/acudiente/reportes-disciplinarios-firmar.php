<?php 
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'AC0036';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$sql = "UPDATE ".BD_DISCIPLINA.".disciplina_reportes 
	        SET dr_aprobacion_acudiente=1, dr_aprobacion_acudiente_fecha=now(), dr_comentario=? 
	        WHERE dr_id=? AND institucion=? AND year=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $_POST["comentario"], PDO::PARAM_STR);
	$stmt->bindParam(2, $_POST["id"], PDO::PARAM_STR);
	$stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt->execute();
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="estudiantes.php";</script>';
exit();