<?php
$animate = "";
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
    require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
    include_once(ROOT_PATH . "/main-app/class/SocialComentarios.php");
    include_once(ROOT_PATH . "/main-app/class/SocialReacciones.php");
    $input = json_decode(file_get_contents("php://input"), true);
    $page = $input["pagina"];
}
?>
<?php
$arrayEnviar = array("tipo" => 4, "descripcionTipo" => "Para ocultar fila del registro.");
$arrayDatos = json_encode($arrayEnviar);
$objetoEnviar = htmlentities($arrayDatos);
?>
<?php
$filtro = '';
if (!empty($_GET["busqueda"])) {
    $filtro .= " AND (not_titulo LIKE '%" . $_GET["busqueda"] . "%') OR (not_descripcion LIKE '%" . $_GET["busqueda"] . "%') OR (not_keywords LIKE '%" . $_GET["busqueda"] . "%')";
}
if (!empty($_GET["usuario"])) {
    $filtro .= " AND not_usuario='" . base64_decode($_GET["usuario"]) . "'";
}


$consulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".social_noticias
											LEFT JOIN " . BD_GENERAL . ".usuarios uss ON uss_id=not_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
											WHERE (not_estado=1 or (not_estado=0 and not_usuario='" . $_SESSION["id"] . "')) 
											AND (not_para LIKE '%" . $datosUsuarioActual['uss_tipo'] . "%' OR not_usuario='" . $_SESSION["id"] . "')
											AND not_year='" . $_SESSION["bd"] . "' AND (not_institucion='" . $config['conf_id_institucion'] . "' OR not_global = 'SI')
											$filtro                                            
											ORDER BY not_id DESC
                                            LIMIT 5 OFFSET $page
											");
$not = 1;
$contReg = 1;
while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
    $colorFondo = 'style="background: #FFF;"';
    if ($resultado['not_estado'] == 0) {
        $colorFondo = 'style="background: #999; opacity:0.7;"';
    }


    $parametros = ["npr_noticia" => $resultado['not_id']];
    $numReacciones = intval(SocialReacciones::contar($parametros));
    $parametros["npr_usuario"] = $_SESSION["id"];
    $usrReacciones = SocialReacciones::consultar($parametros);

    if ($datosUsuarioActual['uss_tipo'] == 4) {
        include("verificar-usuario.php");
        $noticiasCursos = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".social_noticias_cursos WHERE notpc_noticia='" . $resultado['not_id'] . "'");
        $notCursoNum = mysqli_num_rows($noticiasCursos);
        if ($notCursoNum > 0) {
            $noticiaPermitida = 0;
            while ($notCursosInfo = mysqli_fetch_array($noticiasCursos, MYSQLI_BOTH)) {
                if ($notCursosInfo['notpc_curso'] == $datosEstudianteActual['mat_grado']) {
                    $noticiaPermitida = 1;
                }
            }
            if ($noticiaPermitida == 0) continue;
        }
    }


    $fotoUsr = Usuarios::verificarFoto($resultado['uss_foto']);

    $clasesNoticiaGlobal = '';
    if ($resultado['not_global'] == 'SI') {
        $clasesNoticiaGlobal = ' border border-info animate__animated animate__pulse animate__delay-1s animate__repeat-2 mt-5 mb-5';
        $colorFondo = 'style="background: #FFFBE4;"';
    }


?>
    <div id="PUB<?= $resultado['not_id']; ?>" class="row">

        <div class="col-sm-12">
            <div id="PANEL<?= $resultado['not_id']; ?>" class="panel <?= $clasesNoticiaGlobal; ?>" <?= $colorFondo; ?>>

                <div class="card-head">

                    <header><?= $resultado['not_titulo']; ?></header>

                    <?php if ($_SESSION["id"] == $resultado['not_usuario'] || $datosUsuarioActual['uss_tipo'] == 1 || $datosUsuarioActual['uss_tipo'] == 5) { ?>

                        <div class="dropdown  pull-right">
                            <button type="button" id="panel-<?= $resultado['not_id']; ?>" class="dropdown-toggle mdl-button mdl-js-button mdl-button--icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="panel-<?= $resultado['not_id']; ?>">
                                <a class="dropdown-item" href="javascript:void(0);" id="<?= $resultado['not_id']; ?>|1" name="../compartido/noticias-gestionar.php?e=<?= base64_encode(1) ?>&idR=<?= base64_encode($resultado['not_id']); ?>" onClick="ocultarNoticia(this)">
                                    <i class="fa fa-eye"></i><?= $frases[172][$datosUsuarioActual['uss_idioma']]; ?></a>
                                <a class="dropdown-item" href="javascript:void(0);" id="<?= $resultado['not_id']; ?>|2" name="../compartido/noticias-gestionar.php?e=<?= base64_encode(0) ?>&idR=<?= base64_encode($resultado['not_id']); ?>" onClick="ocultarNoticia(this)">
                                    <i class="fa fa-eye-slash"></i><?= $frases[173][$datosUsuarioActual['uss_idioma']]; ?>
                                </a>
                                <?php if ($_SESSION["id"] == $resultado['not_usuario'] || $datosUsuarioActual['uss_tipo'] == 1) { ?>
                                    <a class="dropdown-item" href="noticias-editar.php?idR=<?= base64_encode($resultado['not_id']); ?>"><i class="fa fa-pencil-square-o"></i><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?></a>
                                    <a class="dropdown-item" href="javascript:void(0);" title="<?= $objetoEnviar; ?>" id="<?= $resultado['not_id']; ?>" name="../compartido/noticias-gestionar.php?e=<?= base64_encode(2) ?>&idR=<?= base64_encode($resultado['not_id']); ?>" onClick="deseaEliminar(this)"><i class="fa fa-trash"></i><?= $frases[174][$datosUsuarioActual['uss_idioma']]; ?></a>
                                <?php } ?>
                            </div>
                        </div>

                    <?php } ?>
                </div>

                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="<?= $fotoUsr; ?>" class="img-circle user-img-circle" alt="User Image" height="50" width="50" />
                    </div>
                    <div class="pull-left info">
                        <p><a href="<?= $_SERVER['PHP_SELF']; ?>?usuario=<?= base64_encode($resultado['uss_id']); ?>"><?= $resultado['uss_nombre']; ?></a><br><span style="font-size: 11px;"><?= $resultado['not_fecha']; ?></span></p>
                    </div>
                </div>

                <script>
                    var images = document.getElementsByClassName('imagenes');
                    var modal = document.getElementById('myModal');
                    var modalImg = document.getElementById("img");
                    var captionText = document.getElementById("caption");
                    for (var i = 0; i < images.length; i++) {
                        images[i].onclick = function() {
                            modal.style.display = "block";
                            modalImg.src = this.src;
                            modalImg.alt = this.alt;

                        }
                    }
                    var span = document.getElementsByClassName("close")[0];
                    span.onclick = function() {
                        modal.style.display = "none";
                    }
                    window.onclick = function(event) {
                        if (event.target == document.getElementById("myModal"))
                            modal.style.display = "none";
                    }
                </script>

                <div id="myModal" class="modal">
                    <span class="close"></span>
                    <img class="modal-content" id="img">
                    <div id="caption"></div>
                </div>

                <div class="panel-body">
                    <p><?= $resultado['not_descripcion']; ?></p>
                    <?php
                    $urlImagen = $storage->getBucket()->object(FILE_PUBLICACIONES . $resultado["not_imagen"])->signedUrl(new DateTime('tomorrow'));
                    $existe = $storage->getBucket()->object(FILE_PUBLICACIONES . $resultado["not_imagen"])->exists();
                    if ($resultado['not_imagen'] != "" and $existe) { ?>
                        <div class="item"><a><img class="imagenes" src="<?= $urlImagen ?>" alt="<?= $resultado['not_titulo']; ?>"></ah>
                        </div>
                        <p>&nbsp;</p>
                    <?php } ?>

                    <?php if (!empty($resultado['not_video'])) { ?>
                        <div>
                            <iframe width="450" height="400" src="https://www.youtube.com/embed/<?= $resultado['not_video']; ?>?rel=0&amp;" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen volume="0"></iframe>
                        </div>
                        <p>&nbsp;</p>
                    <?php } ?>

                    <?php if (!empty($resultado['not_enlace_video2'])) { ?>
                        <div style="position: relative; padding-bottom: 56.25%; height: 0;">
                            <iframe src="https://www.loom.com/embed/<?= $resultado['not_enlace_video2']; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
                        </div>
                        <p>&nbsp;</p>
                    <?php } ?>

                    <?php
                    $urlArchivo = $storage->getBucket()->object(FILE_PUBLICACIONES . $resultado["not_archivo"])->signedUrl(new DateTime('tomorrow'));
                    $existeArchivo = $storage->getBucket()->object(FILE_PUBLICACIONES . $resultado["not_archivo"])->exists();
                    if ($resultado['not_archivo'] != "" and $existeArchivo) { ?>
                        <div align="right">
                            <a href="<?= $urlArchivo ?>" target="_blank"><i class="fa fa-download"></i> Descargar Archivo</a>
                        </div>
                        <p>&nbsp;</p>
                    <?php } ?>

                    <?php if (!empty($resultado['not_descripcion_pie'])) {
                        echo $resultado['not_descripcion_pie'];
                    } ?>

                </div>

                <div id="car-body-<?= $resultado['not_id']; ?>" class="card-body">
                    <?php
                    $rName = array("", "Me gusta", "Me encanta", "Me divierte", "Me entristece");
                    $rIcons = array("", "fa-thumbs-o-up", "fa-heart", "fa-smile-o", "fa-frown-o");
                    $rNameClass = array("", "me_gusta", "me_encanta", "me_divierte", "me_entristece");
                    $claseReaccion = "";
                    if (isset($usrReacciones['npr_reaccion']) and $usrReacciones['npr_reaccion'] != "") {
                        $reaccionP = $usrReacciones['npr_reaccion'];
                        $claseReaccion = $rNameClass[$usrReacciones["npr_reaccion"]];
                    } else {
                        $reaccionP = 1;
                    }
                    ?>
                    <div class="dropdown  pull-left">
                        <a id="panel-<?= $resultado['not_id']; ?>-reaccion" style="margin-right: 10px;" class="dropdown-toggle <?= $claseReaccion; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fa <?= $rIcons[$reaccionP]; ?>"></i> <?= $rName[$reaccionP]; ?></a>
                        <ul id="opciones-<?= $resultado['not_id']; ?>" class="dropdown-menu" data-mdl-for="panel-<?= $resultado['not_id']; ?>-reaccion">
                            <?php
                            include '../compartido/reacciones-lista-opciones.php';
                            ?>
                        </ul>

                    </div>


                    <div id="dropdown-<?= $resultado['not_id']; ?>" class="dropdown  pull-left show">
                        <?php if ($numReacciones > 0) { ?>
                            <a id="reacciones-<?= $resultado['not_id']; ?>" name="<?= $resultado['not_id'] ?>" class="dropbtn" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <?= number_format($numReacciones, 0, ",", "."); ?> reacciones</a>
                            <div id="reacciones-content-<?= $resultado['not_id']; ?>" class="dropdown-content">
                                <?php
                                include '../compartido/reacciones-lista.php';
                                ?>
                            </div>
                            <div id="dropdown-reacciones-usuarios-<?= $resultado['not_id']; ?>" style="width:400px ;" class="dropdown-menu animate__animated animate__fadeInUp" aria-labelledby="reacciones-<?= $resultado['not_id']; ?>">
                                <?php
                                include '../compartido/reacciones-lista-usuarios.php';
                                ?>
                            </div>
                        <?php } ?>
                    </div>


                    <?php
                    $parametros = ["ncm_noticia" => $resultado['not_id'], "ncm_padre" => 0];
                    $numcomentarios = SocialComentarios::contar($parametros);
                    ?>
                    <a id="comentarios-<?= $resultado['not_id']; ?>" class="pull-right" data-bs-toggle="collapse" data-bs-target="#collapseExample-<?= $resultado['not_id']; ?>" aria-expanded="false" aria-controls="collapseExample-<?= $resultado['not_id']; ?>">

                        <?php if ($numcomentarios > 0) { ?> <?= $numcomentarios ?> <?php } ?> Comentarios
                        <i class="fa fa-comments-o" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="collapse" id="collapseExample-<?= $resultado['not_id']; ?>">
                    <div class="card-body">
                        <div class="input-group">

                            <textarea id="comentario-<?= $resultado['not_id']; ?>" class="form-control" rows="2" placeholder="<?= UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual); ?> DICE..." style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>

                            <button class="input-group-text btn btn-primary " type="button" onclick="enviarComentario('<?= $resultado['not_id'] ?>','comentario')"><i class="fa fa-send" aria-hidden="true"></i></button>
                        </div>
                        <ul id="comments-list-<?= $resultado['not_id'] ?>" class="comments-list">
                            <?php
                            $parametros = ["ncm_noticia" => $resultado['not_id'], "ncm_padre" => 0];
                            $comentarios = SocialComentarios::listar($parametros);
                            if ($comentarios) {
                                foreach ($comentarios as $comentario) {
                                    include '../compartido/comentario-li.php';
                                };
                            } ?>
                        </ul>

                    </div>
                </div>

            </div>
        </div>
    </div>



<?php
    $not++;
    $contReg++;
}
?>
</div>