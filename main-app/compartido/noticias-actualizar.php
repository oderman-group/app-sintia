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

// Migrado a PDO - Consultas preparadas para archivos
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');

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
        $sql = "UPDATE ".$baseDatosServicios.".social_noticias SET not_imagen=? WHERE not_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $imagen, PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["idR"], PDO::PARAM_STR);
        $stmt->execute();
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
        $sql = "UPDATE ".$baseDatosServicios.".social_noticias SET not_archivo=? WHERE not_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $archivo, PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["idR"], PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}

$findme   = '?v=';
$pos = strpos($_POST["video"], $findme) + 3;
$video = substr($_POST["video"], $pos, 11);
$notificar=!empty($_POST["notificar"]) ? 1 : 0;

// Migrado a PDO - Actualización de noticia
try{
    $sql = "UPDATE ".$baseDatosServicios.".social_noticias SET 
            not_titulo=?, not_descripcion=?, not_keywords=?, not_url_imagen=?, 
            not_video=?, not_id_categoria_general=?, not_video_url=?, not_para=?, 
            not_global=?, not_enlace_video2=?, not_descripcion_pie=?, not_notificar=? 
            WHERE not_id=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_POST["titulo"], PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["contenido"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_POST["keyw"], PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST["urlImagen"], PDO::PARAM_STR);
    $stmt->bindParam(5, $video, PDO::PARAM_STR);
    $stmt->bindParam(6, $_POST["categoriaGeneral"], PDO::PARAM_STR);
    $stmt->bindParam(7, $_POST["video"], PDO::PARAM_STR);
    $stmt->bindParam(8, $destinatarios, PDO::PARAM_STR);
    $stmt->bindParam(9, $global, PDO::PARAM_STR);
    $stmt->bindParam(10, $video2, PDO::PARAM_STR);
    $stmt->bindParam(11, $_POST["contenidoPie"], PDO::PARAM_STR);
    $stmt->bindParam(12, $notificar, PDO::PARAM_INT);
    $stmt->bindParam(13, $_POST["idR"], PDO::PARAM_STR);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

try{
    $sqlDelete = "DELETE FROM ".$baseDatosServicios.".social_noticias_cursos WHERE notpc_noticia=?";
    $stmtDelete = $conexionPDO->prepare($sqlDelete);
    $stmtDelete->bindParam(1, $_POST["idR"], PDO::PARAM_STR);
    $stmtDelete->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

if(!empty($_POST["cursos"])){
    $sqlInsert = "INSERT INTO ".$baseDatosServicios.".social_noticias_cursos(
        notpc_noticia, notpc_curso, notpc_institucion, notpc_year
    ) VALUES (?, ?, ?, ?)";
    $stmtInsert = $conexionPDO->prepare($sqlInsert);
    
    foreach ($_POST["cursos"] as $curso) {
        try{
            $stmtInsert->bindParam(1, $_POST["idR"], PDO::PARAM_STR);
            $stmtInsert->bindParam(2, $curso, PDO::PARAM_STR);
            $stmtInsert->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtInsert->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
            $stmtInsert->execute();
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
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
