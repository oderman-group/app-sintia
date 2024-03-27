<?php
$animate ="";
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
    require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
    include_once(ROOT_PATH . "/main-app/class/SocialComentarios.php");
    $input = json_decode(file_get_contents("php://input"), true);
    $parametros = ["ncm_id" => $input["idComentario"]];
    $comentario = SocialComentarios::consultar($parametros);
    $resultado['not_id'] = $comentario["ncm_noticia"];
    $resultado['uss_id'] = $comentario["not_usuario"];
    $animate = "animate__animated animate__flipInX";
}
$fotoPerfilUsr = Usuarios::verificarFoto($comentario['uss_foto']);
?>
<li id="comenttario-id-<?= $comentario['ncm_id']; ?>" class="<?= $animate ?>">
    <div class="comment-main-level">
        <!-- Avatar -->
        <div class="comment-avatar"><img alt="" class="img-circle " src="<?= $fotoPerfilUsr; ?>" /></div>
        <!-- Contenedor del Comentario -->
        <div class="comment-box">
            <div class="comment-head">
                <h6 class="comment-name <?= $comentario['uss_id'] == $resultado['uss_id'] ? 'by-author' : ""; ?>"><a href="javascript(0)"><?= UsuariosPadre::nombreCompletoDelUsuario($comentario); ?></a></h6>
                <span><?= $comentario['ncm_fecha']; ?></span>
                <i class="fa fa-reply" data-bs-toggle="collapse" data-bs-target="#div-respuesta-<?= $comentario['ncm_id']; ?>" aria-expanded="false" aria-controls="div-respuesta-<?= $comentario['ncm_id']; ?>"></i>
            </div>
            <div class="comment-content">
                <?= $comentario['ncm_comentario']; ?>
                <?php
                $parametros = ["ncm_noticia" => $resultado['not_id'], "ncm_padre" =>  $comentario['ncm_id']];
                $numRespuestas = SocialComentarios::contar($parametros);
                ?>
                <div class="card-body" style="font-size: 11px;">
                    <a id="cantidad-respuestas-<?=$comentario['ncm_id'] ?>" class="pull-right" data-bs-toggle="collapse" data-bs-target="#lista-respuesta-<?= $comentario['ncm_id']; ?>" aria-expanded="false" aria-controls="lista-respuesta-<?= $comentario['ncm_id']; ?>">

                        <?php if ($numRespuestas > 0) { ?> <?= $numRespuestas ?>
                            Respuestas
                            <i class="fa fa-comments-o" aria-hidden="true"></i>
                        <?php } ?>

                    </a>
                </div>

            </div>
            <div class="collapse" id="div-respuesta-<?= $comentario['ncm_id']; ?>">
                <div class="input-group">
                    <textarea id="respuesta-<?= $resultado['not_id']; ?>-<?= $comentario['ncm_id']; ?>" class="form-control" rows="2" placeholder="<?= UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual); ?> DICE..." style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                    <button class="input-group-text btn btn-primary " type="button" onclick="enviarComentario('<?= $resultado['not_id'] ?>','respuesta','<?= $comentario['ncm_id']; ?>')"><i class="fa fa-send" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </div>
    <ul id="lista-respuesta-<?= $comentario['ncm_id']; ?>" class="comments-list reply-list collapse">
        <?php
        $parametros = ["ncm_noticia" => $resultado['not_id'], "ncm_padre" =>  $comentario['ncm_id']];
        $respuestas = SocialComentarios::listar($parametros);
        if ($respuestas) {
            foreach ($respuestas as $respuesta) {
                include 'respuesta-li.php';
            };
        } ?>
    </ul>

</li>