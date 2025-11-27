<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
Modulos::verificarPermisoDev();

$idPaginaInterna = 'DV0072';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $sql = "UPDATE ".$baseDatosServicios.".opciones_generales SET ogen_nombre=?, ogen_grupo=? WHERE ogen_id=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_POST["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["grupo"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_POST["idogen"], PDO::PARAM_STR);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="configuracion-opciones-generales.php"</script>';
exit();