<?php
include("../session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0024';
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
    try{
        mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".general_folders SET fold_nombre='" . $archivo . "' WHERE fold_id='" . $_POST["idR"] . "'");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}

try{
    mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".general_folders SET fold_nombre='" . $archivo . "', fold_padre='" . $_POST["padre"] . "', fold_tipo='" . $_POST["tipo"] . "', fold_keywords='" . $_POST["keyw"] . "', fold_fecha_modificacion=now() WHERE fold_id='" . $_POST["idR"] . "'");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

try{
    mysqli_query($conexion, "DELETE FROM ".$baseDatosServicios.".general_folders_usuarios_compartir WHERE fxuc_folder='" . $_POST["idR"] . "'");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}
if(!empty($_POST["compartirCon"])){
    $cont = count($_POST["compartirCon"]);
    $i = 0;
    while ($i < $cont) {
        try{
            mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".general_folders_usuarios_compartir(fxuc_folder, fxuc_usuario, fxuc_institucion, fxuc_year)VALUES('" . $_POST["idR"] . "','" . $_POST["compartirCon"][$i] . "','" . $config['conf_id_institucion'] . "','" . $_SESSION["bd"] . "')");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
        $i++;
    }
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'cargas-carpetas.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();