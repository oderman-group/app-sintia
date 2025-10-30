<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0020';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
$usuariosClase = new UsuariosFunciones;

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $estado = base64_decode($_GET["e"]);
    $sql = "UPDATE ".$baseDatosServicios.".social_noticias SET not_estado=? WHERE not_usuario=? AND not_estado!=2";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $estado, PDO::PARAM_STR);
    $stmt->bindParam(2, $_SESSION["id"], PDO::PARAM_STR);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'noticias.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();