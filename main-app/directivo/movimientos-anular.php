<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/AuditoriaFinanciera.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0089';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Migrado a PDO - Consulta preparada
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	
	// Obtener datos anteriores para auditoría (antes del UPDATE - soft delete)
	$sqlAnterior = "SELECT fcu_fecha, fcu_detalle, fcu_tipo, fcu_observaciones, fcu_usuario, fcu_anulado, fcu_consecutivo, fcu_status, fcu_cerrado, fcu_fecha_cerrado, fcu_cerrado_usuario, institucion, year FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_id=? AND institucion=? AND year=? LIMIT 1";
	$stmtAnterior = $conexionPDO->prepare($sqlAnterior);
	$stmtAnterior->bindParam(1, $_GET["idR"], PDO::PARAM_STR);
	$stmtAnterior->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmtAnterior->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
	$stmtAnterior->execute();
	$datosAnteriores = $stmtAnterior->fetch(PDO::FETCH_ASSOC);
	
	// Realizar el soft delete (UPDATE fcu_anulado=1)
	$sql = "UPDATE ".BD_FINANCIERA.".finanzas_cuentas SET fcu_anulado=1 WHERE fcu_id=? AND institucion=? AND year=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $_GET["idR"], PDO::PARAM_STR);
	$stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt->execute();
	
	// Registrar en auditoría (soft delete se trata como UPDATE)
	if (!empty($datosAnteriores)) {
		$datosNuevos = $datosAnteriores;
		$datosNuevos['fcu_anulado'] = 1; // Cambio a anulado
		
		$camposModificados = [
			'fcu_anulado' => [
				'anterior' => $datosAnteriores['fcu_anulado'] ?? 0,
				'nuevo' => 1
			]
		];
		
		AuditoriaFinanciera::registrarActualizacion(
			'finanzas_cuentas',
			(string)$_GET["idR"],
			$datosAnteriores,
			$datosNuevos,
			$camposModificados
		);
	}
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="movimientos.php?usuario=' . base64_encode($_GET["id"]) . '";</script>';
exit();