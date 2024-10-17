<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0043';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/compartido/socket.php");
$usuariosClase = new UsuariosFunciones;
$archivoSubido = new Archivos;

$estado = 1;
if ($datosUsuarioActual['uss_tipo'] == 4) {
    $estado = 0;
}

$destinatarios=!empty($_POST["destinatarios"]) ? implode(',',$_POST["destinatarios"]) : "1,2,3,4,5";

$global=!empty($_POST["global"]) ? $_POST["global"] : "NO";
$video2=!empty($_POST["video2"]) ? $_POST["video2"] : "";

$imagen = '';
if (!empty($_FILES['imagen']['name'])) {
    $archivoSubido->validarArchivo($_FILES['imagen']['size'], $_FILES['imagen']['name']);
    $explode=explode(".", $_FILES['imagen']['name']);
    $extension = end($explode);
    $imagen = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_img_') . "." . $extension;
    $destino = "../files/publicaciones";
    $localFilePath = $_FILES['imagen']['tmp_name'];// Ruta del archivo local que deseas subir	
	$cloudFilePath = FILE_PUBLICACIONES.$imagen;// Ruta en el almacenamiento en la nube de Firebase donde deseas almacenar el archivo
	$storage->getBucket()->upload(fopen($localFilePath, 'r'), ['name' => $cloudFilePath	]);
    // move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . "/" . $imagen);
}
$archivo = '';
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
}

$findme   = '?v=';
$pos = strpos($_POST["video"], $findme) + 3;
$video = substr($_POST["video"], $pos, 11);
$notificar=!empty($_POST["notificar"]) ? 1 : 0;
try{
    mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".social_noticias(not_titulo, not_descripcion, not_usuario, not_fecha, not_estado, not_para, not_imagen, not_archivo, not_keywords, not_url_imagen, not_video, not_id_categoria_general, not_video_url, not_institucion, not_year, not_global, not_enlace_video2, not_descripcion_pie,not_notificar)
    VALUES('" . mysqli_real_escape_string($conexion,$_POST["titulo"]) . "', '" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "', '" . $_SESSION["id"] . "',now(), '" . $estado . "', '" . $destinatarios . "', '" . $imagen . "', '" . $archivo . "', '" . $_POST["keyw"] . "', '" . mysqli_real_escape_string($conexion,$_POST["urlImagen"]) . "', '" . $video . "', '" . $_POST["categoriaGeneral"] . "', '" . mysqli_real_escape_string($conexion,$_POST["video"]) . "','" . $config['conf_id_institucion'] . "','" . $_SESSION["bd"] . "','" . $global . "', '" . $video2 . "', '" . mysqli_real_escape_string($conexion,$_POST["contenidoPie"]) . "','".$notificar."')");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$idRegistro = mysqli_insert_id($conexion);
try{
    mysqli_query($conexion, "DELETE FROM ".$baseDatosServicios.".social_noticias_cursos WHERE notpc_noticia='" . $idRegistro . "'");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

if(!empty($_POST["cursos"])){
    $cont = count($_POST["cursos"]);
    $i = 0;
    while ($i < $cont) {
        try{
            mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".social_noticias_cursos(notpc_noticia, notpc_curso, notpc_institucion, notpc_year)VALUES('" . $idRegistro . "','" . $_POST["cursos"][$i] . "','" . $config['conf_id_institucion'] . "','" . $_SESSION["bd"] . "')");
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
                    id_noticia  : "' . $idRegistro . '",
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