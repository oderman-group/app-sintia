<?php
include("../session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0024';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
$usuariosClase = new UsuariosFunciones;
$archivoSubido = new Archivos;

// Migrado a PDO - Consultas preparadas
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');

$archivo = $_POST["nombre"];
if (!empty($_FILES['archivo']['name'])) {
    $archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
    $explode=explode(".", $_FILES['archivo']['name']);
    $extension = end($explode);
    $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_fileFolder_') . "." . $extension;
    $destino = ROOT_PATH."/main-app/files/archivos";
    move_uploaded_file($_FILES['archivo']['tmp_name'], $destino . "/" . $archivo);
    try{
        $sql = "UPDATE ".$baseDatosServicios.".general_folders SET fold_nombre=? WHERE fold_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $archivo, PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["idR"], PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}

try{
    $sql = "UPDATE ".$baseDatosServicios.".general_folders 
            SET fold_nombre=?, fold_padre=?, fold_tipo=?, fold_keywords=?, fold_fecha_modificacion=now() 
            WHERE fold_id=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $archivo, PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["padre"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_POST["tipo"], PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST["keyw"], PDO::PARAM_STR);
    $stmt->bindParam(5, $_POST["idR"], PDO::PARAM_STR);
    $stmt->execute();
    
    $sqlDelete = "DELETE FROM ".$baseDatosServicios.".general_folders_usuarios_compartir WHERE fxuc_folder=?";
    $stmtDelete = $conexionPDO->prepare($sqlDelete);
    $stmtDelete->bindParam(1, $_POST["idR"], PDO::PARAM_STR);
    $stmtDelete->execute();
    
    if(!empty($_POST["compartirCon"])){
        $sqlInsert = "INSERT INTO ".$baseDatosServicios.".general_folders_usuarios_compartir(
            fxuc_folder, fxuc_usuario, fxuc_institucion, fxuc_year
        ) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conexionPDO->prepare($sqlInsert);
        
        foreach ($_POST["compartirCon"] as $usuario) {
            $stmtInsert->bindParam(1, $_POST["idR"], PDO::PARAM_STR);
            $stmtInsert->bindParam(2, $usuario, PDO::PARAM_STR);
            $stmtInsert->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtInsert->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
            $stmtInsert->execute();
        }
    }
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'cargas-carpetas.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();