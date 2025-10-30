<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0043';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");
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

// Extraer ID de YouTube (funciona con URL completa o ID directo)
$videoInput = isset($_POST["video"]) ? $_POST["video"] : '';
$video = '';

if (!empty($videoInput)) {
    $videoInput = trim($videoInput);
    
    // Si ya es un ID directo (11 caracteres)
    if (strlen($videoInput) == 11 && !strpos($videoInput, '/') && !strpos($videoInput, '?')) {
        $video = $videoInput;
    } else {
        // Extraer de URL completa
        $findme = '?v=';
        $pos = strpos($videoInput, $findme);
        if ($pos !== false) {
            $video = substr($videoInput, $pos + 3, 11);
        } else {
            // Intentar con youtu.be
            $findme2 = 'youtu.be/';
            $pos2 = strpos($videoInput, $findme2);
            if ($pos2 !== false) {
                $video = substr($videoInput, $pos2 + 9, 11);
            }
        }
    }
}
$notificar = !empty($_POST["notificar"]) ? 1 : 0;
$keyw = isset($_POST["keyw"]) ? $_POST["keyw"] : '';
$urlImagen = isset($_POST["urlImagen"]) ? $_POST["urlImagen"] : '';
$contenidoPie = isset($_POST["contenidoPie"]) ? $_POST["contenidoPie"] : '';
$videoUrl = !empty($video) ? $videoInput : ''; // Guardar la URL/ID original si hay video

try{
    // Query segura con PDO prepared statement (patrón del proyecto)
    $sql = "INSERT INTO ".$baseDatosServicios.".social_noticias(not_titulo, not_descripcion, not_usuario, not_fecha, not_estado, not_para, not_imagen, not_archivo, not_keywords, not_url_imagen, not_video, not_id_categoria_general, not_video_url, not_institucion, not_year, not_global, not_enlace_video2, not_descripcion_pie, not_notificar)
    VALUES(?, ?, ?, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $conexionPDO = Conexion::newConnection('PDO');
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_POST["titulo"], PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["contenido"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_SESSION["id"], PDO::PARAM_STR);
    $stmt->bindParam(4, $estado, PDO::PARAM_INT);
    $stmt->bindParam(5, $destinatarios, PDO::PARAM_STR);
    $stmt->bindParam(6, $imagen, PDO::PARAM_STR);
    $stmt->bindParam(7, $archivo, PDO::PARAM_STR);
    $stmt->bindParam(8, $keyw, PDO::PARAM_STR);
    $stmt->bindParam(9, $urlImagen, PDO::PARAM_STR);
    $stmt->bindParam(10, $video, PDO::PARAM_STR);
    $stmt->bindParam(11, $_POST["categoriaGeneral"], PDO::PARAM_STR);
    $stmt->bindParam(12, $videoUrl, PDO::PARAM_STR);
    $stmt->bindParam(13, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(14, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->bindParam(15, $global, PDO::PARAM_STR);
    $stmt->bindParam(16, $video2, PDO::PARAM_STR);
    $stmt->bindParam(17, $contenidoPie, PDO::PARAM_STR);
    $stmt->bindParam(18, $notificar, PDO::PARAM_INT);
    
    $stmt->execute();
    $idRegistro = $conexionPDO->lastInsertId();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$idRegistro = isset($idRegistro) ? $idRegistro : $conexionPDO->lastInsertId();

try{
    // Query segura con PDO prepared statement
    $sql = "DELETE FROM ".$baseDatosServicios.".social_noticias_cursos WHERE notpc_noticia=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $idRegistro, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

if(!empty($_POST["cursos"]) && is_array($_POST["cursos"])){
    try{
        // Query segura con PDO prepared statement
        $sql = "INSERT INTO ".$baseDatosServicios.".social_noticias_cursos(notpc_noticia, notpc_curso, notpc_institucion, notpc_year) VALUES(?, ?, ?, ?)";
        $stmt = $conexionPDO->prepare($sql);
        
        foreach($_POST["cursos"] as $curso){
            $stmt->bindParam(1, $idRegistro, PDO::PARAM_INT);
            $stmt->bindParam(2, $curso, PDO::PARAM_STR);
            $stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->execute();
        }
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
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