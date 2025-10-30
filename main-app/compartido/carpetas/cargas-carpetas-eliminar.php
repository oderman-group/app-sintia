<?php
include("../session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0025';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
$usuariosClase = new UsuariosFunciones;
$archivoSubido = new Archivos;

// Migrado a PDO - Consultas preparadas
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $idFolder = base64_decode($_GET["idR"]);
    
    $sql = "UPDATE ".$baseDatosServicios.".general_folders 
            SET fold_estado='0', fold_fecha_eliminacion=now() 
            WHERE fold_padre=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $idFolder, PDO::PARAM_STR);
    $stmt->execute();
    
    $sql2 = "UPDATE ".$baseDatosServicios.".general_folders 
             SET fold_estado='0', fold_fecha_eliminacion=now() 
             WHERE fold_id=?";
    $stmt2 = $conexionPDO->prepare($sql2);
    $stmt2->bindParam(1, $idFolder, PDO::PARAM_STR);
    $stmt2->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'cargas-carpetas.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();