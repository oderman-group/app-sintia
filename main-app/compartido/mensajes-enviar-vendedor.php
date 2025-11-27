<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0041';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
$usuariosClase = new UsuariosFunciones;

try{
    $remitente = UsuariosPadre::sesionUsuario($_SESSION["id"]);
    $destinatario = UsuariosPadre::sesionUsuario($_POST["para"]);
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $sql = "INSERT INTO ".$baseDatosServicios.".social_emails(
        ema_de, ema_para, ema_asunto, ema_contenido, ema_fecha, ema_visto, 
        ema_eliminado_de, ema_eliminado_para, ema_institucion, ema_year
    ) VALUES (?, ?, ?, ?, now(), 0, 0, 0, ?, ?)";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_SESSION["id"], PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["destinoMarketplace"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_POST["asuntoMarketplace"], PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST["contenido"], PDO::PARAM_STR);
    $stmt->bindParam(5, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(6, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'marketplace.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
exit();