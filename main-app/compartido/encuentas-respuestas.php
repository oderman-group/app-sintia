<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0029';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
if (!empty(trim($_POST["respuesta"]))) {
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sql = "INSERT INTO " . $baseDatosServicios . ".comentarios(
            adcom_institucion, adcom_usuario, adcom_fecha, adcom_respuesta, adcom_tipo, adcom_id_encuesta
        ) VALUES (?, ?, now(), ?, 1, ?)";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(2, $_SESSION["id"], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST["respuesta"], PDO::PARAM_STR);
        $stmt->bindParam(4, $_POST["encuesta"], PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
exit();