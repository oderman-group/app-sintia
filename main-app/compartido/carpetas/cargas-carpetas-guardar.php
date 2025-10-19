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
try{
    mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".general_folders(fold_nombre, fold_padre, fold_activo, fold_fecha_creacion, fold_propietario, fold_id_recurso_principal, fold_categoria, fold_tipo, fold_estado, fold_keywords, fold_institucion, fold_year)
    VALUES('" . $archivo . "', '" . $_POST["padre"] . "', 1, now(), '" . $_SESSION["id"] . "', '" . $_POST["idRecursoP"] . "', '" . $_POST["idCategoria"] . "', '" . $_POST["tipo"] . "', 1, '" . $_POST["keyw"] . "','" . $config['conf_id_institucion'] . "','" . $_SESSION["bd"] . "')");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}
$idRegistro = mysqli_insert_id($conexion);

try{
    mysqli_query($conexion, "DELETE FROM ".$baseDatosServicios.".general_folders_usuarios_compartir WHERE fxuc_folder='" . $idRegistro . "'");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}
if(!empty($_POST["compartirCon"])){
    $cont = count($_POST["compartirCon"]);
    $i = 0;
    while ($i < $cont) {
        try{
            mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".general_folders_usuarios_compartir(fxuc_folder, fxuc_usuario, fxuc_institucion, fxuc_year)VALUES('" . $idRegistro . "','" . $_POST["compartirCon"][$i] . "','" . $config['conf_id_institucion'] . "','" . $_SESSION["bd"] . "')");
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