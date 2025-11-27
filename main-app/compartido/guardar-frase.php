<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0038';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $sql = "INSERT INTO " . $baseDatosServicios . ".publicidad_guardadas(
        psave_publicidad, psave_institucion, psave_usuario, psave_fecha
    ) VALUES (?, ?, ?, now())";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_GET["idPub"], PDO::PARAM_STR);
    $stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(3, $_SESSION["id"], PDO::PARAM_STR);
    $stmt->execute();
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.close();</script>';
exit();