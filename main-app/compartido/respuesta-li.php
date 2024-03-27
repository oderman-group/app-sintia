<?php
$animate="";
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
    require_once(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
    include_once(ROOT_PATH . "/main-app/class/SocialComentarios.php");
    $input = json_decode(file_get_contents("php://input"), true);
    $parametros = ["ncm_id" => $input["idComentario"]];
    $respuesta = SocialComentarios::consultar($parametros);
    $resultado['not_id'] = $respuesta["ncm_noticia"];
    $resultado['uss_id'] = $respuesta["not_usuario"];
    $animate="animate__animated animate__flipInX";
}
$fotoPerfilUsr = Usuarios::verificarFoto($respuesta['uss_foto']);
?>

<li id="respuesta-id-<?= $respuesta['ncm_id']; ?>" class="<?= $animate?>">
    <!-- Avatar -->
    <div class="comment-avatar"><img src="<?=$fotoPerfilUsr;?>" alt=""></div>
    <!-- Contenedor del respuesta -->
    <div class="comment-box">
        <div class="comment-head">
            <h6 class="comment-name  <?= $respuesta['uss_id'] == $resultado['uss_id'] ? 'by-author' : ""; ?>"><a href="javascript(0)"><?= UsuariosPadre::nombreCompletoDelUsuario($respuesta); ?></a></h6>
            <span><?= $respuesta['ncm_fecha']; ?></span>
        </div>
        <div class="comment-content">
         <?= $respuesta['ncm_comentario']; ?>
        </div>
    </div>
</li>