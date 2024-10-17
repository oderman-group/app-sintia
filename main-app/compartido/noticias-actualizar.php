<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0044';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/compartido/socket.php");
$usuariosClase = new UsuariosFunciones;
$archivoSubido = new Archivos;

$destinatarios=!empty($_POST["destinatarios"]) ? implode(',',$_POST["destinatarios"]) : "1,2,3,4,5";

$global=!empty($_POST["global"]) ? $_POST["global"] : "NO";
$video2=!empty($_POST["video2"]) ? $_POST["video2"] : "";

if (!empty($_FILES['imagen']['name'])) {
    $archivoSubido->validarArchivo($_FILES['imagen']['size'], $_FILES['imagen']['name']);
    $explode=explode(".", $_FILES['imagen']['name']);
    $extension = end($explode);
    $imagen = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_imgNoti_') . "." . $extension;
    $destino = "../files/publicaciones";
    $localFilePath = $_FILES['imagen']['tmp_name'];// Ruta del archivo local que deseas subir	
	$cloudFilePath = FILE_PUBLICACIONES.$imagen;// Ruta en el almacenamiento en la nube de Firebase donde deseas almacenar el archivo
	$storage->getBucket()->upload(fopen($localFilePath, 'r'), ['name' => $cloudFilePath	]);
    // move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . "/" . $imagen);
    try{
        mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".social_noticias SET not_imagen='" . $imagen . "' WHERE not_id='" . $_POST["idR"] . "'");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}
if (!empty($_FILES['archivo']['name'])) {
    $archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
    $explode=explode(".", $_FILES['archivo']['name']);
    $extension = end($explode);
    $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_fileNoti_') . "." . $extension;
    $destino = "../files/publicaciones";
    $localFilePath = $_FILES['archivo']['tmp_name'];// Ruta del archivo local que deseas subir	
	$cloudFilePath = FILE_PUBLICACIONES.$archivo;// Ruta en el almacenamiento en la nube de Firebase donde deseas almacenar el archivo
	$storage->getBucket()->upload(fopen($localFilePath, 'r'), ['name' => $cloudFilePath	]);
    // move_uploaded_file($_FILES['archivo']['tmp_name'], $destino . "/" . $archivo);
    try{
        mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".social_noticias SET not_archivo='" . $archivo . "' WHERE not_id='" . $_POST["idR"] . "'");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}

$findme   = '?v=';
$pos = strpos($_POST["video"], $findme) + 3;
$video = substr($_POST["video"], $pos, 11);
$notificar=!empty($_POST["notificar"]) ? 1 : 0;
try{
    mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".social_noticias SET not_titulo='" . mysqli_real_escape_string($conexion,$_POST["titulo"]) . "', not_descripcion='" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "',  not_keywords='" . mysqli_real_escape_string($conexion,$_POST["keyw"]) . "', not_url_imagen='" . mysqli_real_escape_string($conexion,$_POST["urlImagen"]) . "', not_video='" . $video . "', not_id_categoria_general='" . $_POST["categoriaGeneral"] . "', not_video_url='" . $_POST["video"] . "', not_para='" . $destinatarios . "', not_global='" . $global . "', not_enlace_video2='" . $video2 . "', not_descripcion_pie='" . mysqli_real_escape_string($conexion,$_POST["contenidoPie"]) . "',not_notificar='".$notificar."' WHERE not_id='" . $_POST["idR"] . "'");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

try{
    mysqli_query($conexion, "DELETE FROM ".$baseDatosServicios.".social_noticias_cursos WHERE notpc_noticia='" . $_POST["idR"] . "'");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}
if(!empty($_POST["cursos"])){
    $cont = count($_POST["cursos"]);
    $i = 0;
    while ($i < $cont) {
        try{
            mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".social_noticias_cursos(notpc_noticia, notpc_curso, notpc_institucion, notpc_year)VALUES('" . $_POST["idR"] . "','" . $_POST["cursos"][$i] . "','" . $config['conf_id_institucion'] . "','" . $_SESSION["bd"] . "')");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
        $i++;
    }
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'noticias.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
if ($notificar == 1) {
    echo '<script type="text/javascript">
    socket.emit("notificar_noticia", {
                    global      : "' . $global . '",
                    id_noticia  : "' . $_POST["idR"] . '",
                    institucion : "' . $config['conf_id_institucion'] . '",
                    year        : "' . $_SESSION["bd"] . '"
    });
    setTimeout(function() {
        window.location.href = "' . $url . '";
    }, 500);
    </script>';
}else{
    echo '<script type="text/javascript">window.location.href="' . $url . '";</script>'; 
}
