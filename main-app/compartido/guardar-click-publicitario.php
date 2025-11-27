<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0030';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if (!empty($_GET["usrAct"])) {
    $usuarioActivo = $_GET["usrAct"];
} elseif (!empty($_SESSION["id"])) {
    $usuarioActivo = $_SESSION["id"];
}

if (!empty($_GET["idIns"])) {
    $idInst = $_GET["idIns"];
} else {
    $idInst = $config['conf_id_institucion'];
}


// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $sql = "INSERT INTO " . $baseDatosServicios . ".publicidad_estadisticas(
        pest_publicidad, pest_institucion, pest_usuario, pest_pagina, pest_ubicacion, 
        pest_fecha, pest_ip, pest_accion
    ) VALUES (?, ?, ?, ?, ?, now(), ?, 2)";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_GET["idPub"], PDO::PARAM_STR);
    $stmt->bindParam(2, $idInst, PDO::PARAM_INT);
    $stmt->bindParam(3, $usuarioActivo, PDO::PARAM_STR);
    $stmt->bindParam(4, $_GET["idPag"], PDO::PARAM_STR);
    $stmt->bindParam(5, $_GET["idUb"], PDO::PARAM_STR);
    $stmt->bindParam(6, $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
    $stmt->execute();
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

if (!empty($_GET["url"])) $URL = $_GET["url"];
else $URL = $_SERVER["HTTP_REFERER"];

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $URL . '";</script>';
exit();