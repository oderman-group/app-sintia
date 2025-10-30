<?php
include("../session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0023';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
$usuariosClase = new UsuariosFunciones;
$archivoSubido = new Archivos;

$archivo = $_POST["nombre"];
if (!empty($_FILES['archivo']['name'])) {
    $archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
    $explode=explode(".", $_FILES['archivo']['name']);
    $extension = end($explode);
    $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_fileFolder_') . "." . $extension;
    $destino = ROOT_PATH."/main-app/files/archivos";
    move_uploaded_file($_FILES['archivo']['tmp_name'], $destino . "/" . $archivo);
}
// Migrado a PDO - Consultas preparadas
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    
    $sql = "INSERT INTO ".$baseDatosServicios.".general_folders(
        fold_nombre, fold_padre, fold_activo, fold_fecha_creacion, fold_propietario, 
        fold_id_recurso_principal, fold_categoria, fold_tipo, fold_estado, fold_keywords, 
        fold_institucion, fold_year
    ) VALUES (?, ?, 1, now(), ?, ?, ?, ?, 1, ?, ?, ?)";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $archivo, PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["padre"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_SESSION["id"], PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST["idRecursoP"], PDO::PARAM_STR);
    $stmt->bindParam(5, $_POST["idCategoria"], PDO::PARAM_STR);
    $stmt->bindParam(6, $_POST["tipo"], PDO::PARAM_STR);
    $stmt->bindParam(7, $_POST["keyw"], PDO::PARAM_STR);
    $stmt->bindParam(8, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(9, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
    
    $idRegistro = $conexionPDO->lastInsertId();
    
    $sqlDelete = "DELETE FROM ".$baseDatosServicios.".general_folders_usuarios_compartir WHERE fxuc_folder=?";
    $stmtDelete = $conexionPDO->prepare($sqlDelete);
    $stmtDelete->bindParam(1, $idRegistro, PDO::PARAM_INT);
    $stmtDelete->execute();
    
    if(!empty($_POST["compartirCon"])){
        $sqlInsert = "INSERT INTO ".$baseDatosServicios.".general_folders_usuarios_compartir(
            fxuc_folder, fxuc_usuario, fxuc_institucion, fxuc_year
        ) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conexionPDO->prepare($sqlInsert);
        
        foreach ($_POST["compartirCon"] as $usuario) {
            $stmtInsert->bindParam(1, $idRegistro, PDO::PARAM_INT);
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