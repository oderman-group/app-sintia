<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0039';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
$usuariosClase = new UsuariosFunciones;

// Migrado a PDO - Consultas preparadas
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');
$idMensaje = base64_decode($_GET["idR"]);

if (base64_decode($_GET["elm"]) == 1) {
    try{
        $sql = "UPDATE ".$baseDatosServicios.".social_emails SET ema_eliminado_de=1 WHERE ema_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $idMensaje, PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
} else {
    try{
        $sql = "UPDATE ".$baseDatosServicios.".social_emails SET ema_eliminado_para=1 WHERE ema_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $idMensaje, PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'mensajes.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
exit();