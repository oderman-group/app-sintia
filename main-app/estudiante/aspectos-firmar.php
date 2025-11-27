<?php
include("session.php");
include("verificar-usuario.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'ES0059';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$sql = "UPDATE ".BD_DISCIPLINA.".disiplina_nota 
	        SET dn_aprobado=1, dn_fecha_aprobado=now()
	        WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $_POST["estudiante"], PDO::PARAM_STR);
	$stmt->bindParam(2, $_POST["periodo"], PDO::PARAM_INT);
	$stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt->execute();
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="aspectos.php";</script>';
exit();