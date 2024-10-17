<?php
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
    $input = json_decode(file_get_contents("php://input"), true);
    if (!empty($input)) {
        $_GET = $input;
    }
}
include_once("../class/SocialNoticias.php");
include_once("../compartido/sintia-funciones.php");

$noticia = SocialNoticias::consultarNoticia($_GET["id_noticia"]);
$fotoUsr = UsuariosFunciones::verificarFoto($noticia['uss_foto']);
?>

<div class="col-sm-12 animate__animated animate__pulse animate__delay-1s animate__repeat-2 ">

    <div class="card-head">

        <header><?= $noticia['not_titulo'] ?></header>
      
   
    </div>

    <div class="user-panel">
        <div class="pull-left image">
            <img src=".<?= $fotoUsr; ?>" class="img-circle user-img-circle" alt="User Image" height="50" width="50">
        </div>
        <div class="pull-left info">
            <p><a href="<?= $_SERVER['PHP_SELF']; ?>?usuario=<?= base64_encode($noticia['uss_id']); ?>"><?= $noticia['uss_nombre']; ?></a><br><span style="font-size: 11px;"><?= $noticia['not_fecha']; ?></span></p>
        </div>
    </div>



    <div class="panel-body">
        <p><?= $noticia['not_descripcion']; ?></p>
        <?php
        $urlImagen = $storage->getBucket()->object(FILE_PUBLICACIONES . $noticia["not_imagen"])->signedUrl(new DateTime('tomorrow'));
        $existe = $storage->getBucket()->object(FILE_PUBLICACIONES . $noticia["not_imagen"])->exists();
        if ($noticia['not_imagen'] != "" and $existe) { ?>
            <div class="item">
                <a><img class="imagenes" src="<?= $urlImagen ?>" alt="<?= $noticia['not_titulo']; ?>"></ah>
            </div>
            <p>&nbsp;</p>
        <?php } ?>
        <?php if (!empty($noticia['not_video'])) { ?>
            <div>
                <iframe width="450" height="400" src="https://www.youtube.com/embed/<?= $noticia['not_video']; ?>?rel=0&amp;" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen volume="0"></iframe>
            </div>
            <p>&nbsp;</p>
        <?php } ?>
        <?php if (!empty($noticia['not_enlace_video2'])) { ?>
            <div style="position: relative; padding-bottom: 56.25%; height: 0;">
                <iframe src="https://www.loom.com/embed/<?= $noticia['not_enlace_video2']; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
            </div>
            <p>&nbsp;</p>
        <?php } ?>
        <?php
        $urlArchivo = $storage->getBucket()->object(FILE_PUBLICACIONES . $noticia["not_archivo"])->signedUrl(new DateTime('tomorrow'));
        $existeArchivo = $storage->getBucket()->object(FILE_PUBLICACIONES . $noticia["not_archivo"])->exists();
        if ($noticia['not_archivo'] != "" and $existeArchivo) { ?>
            <div align="right">
                <a href="<?= $urlArchivo ?>" target="_blank"><i class="fa fa-download"></i> Descargar Archivo</a>
            </div>
            <p>&nbsp;</p>
        <?php } ?>

        <?php if (!empty($noticia['not_descripcion_pie'])) {
            echo $noticia['not_descripcion_pie'];
        } ?>


    </div>



</div>