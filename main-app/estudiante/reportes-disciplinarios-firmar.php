<?php
include("session.php");
include("verificar-usuario.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'ES0060';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

$id=base64_decode($_GET["id"]);

// Migrado a PDO - Consulta preparada
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$sql = "UPDATE ".BD_DISCIPLINA.".disciplina_reportes 
	        SET dr_aprobacion_estudiante=1, dr_aprobacion_estudiante_fecha=now() 
	        WHERE dr_id=? AND institucion=? AND year=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $id, PDO::PARAM_STR);
	$stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt->execute();
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="reportes-disciplinarios.php";</script>';
exit();