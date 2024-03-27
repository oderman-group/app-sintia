<?php
$animate = "";
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
    require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
    include_once(ROOT_PATH . "/main-app/class/SocialReacciones.php");
    $rName = array("", "Me gusta", "Me encanta", "Me divierte", "Me entristece");
    $rIcons = array("", "fa-thumbs-o-up", "fa-heart", "fa-smile-o", "fa-frown-o");
    $rNameClass = array("", "me_gusta", "me_encanta", "me_divierte", "me_entristece");
    $resultado['not_id'] = $_POST["id"];
}
$parametros = ["npr_noticia" => $resultado['not_id']];
$reaccionesLista = SocialReacciones::contarReacciones($parametros);
?>
<form class="px-4 py-3">
    <ul class="nav nav-pills mb-3" id="pills-tab-<?= $resultado['not_id'] ?>" role="tablist">
        <?php
        if ($reaccionesLista) {
            $cont = 0;
            $active = "";
            foreach ($reaccionesLista as $reaccion) {
                $claseReaccion = $rNameClass[$reaccion["npr_reaccion"]];
                $active = ($cont == 0) ? "active" : "";
        ?>
                <li class="nav-item">
                    <a class="<?= $claseReaccion ?> nav-link <?= $active ?> " id="<?= $claseReaccion ?>-tab-<?= $resultado['not_id'] ?>" data-toggle="pill" href="#pills-<?= $claseReaccion ?>-<?= $resultado['not_id'] ?>" role="tab" aria-controls="pills-<?= $claseReaccion ?>-<?= $resultado['not_id'] ?>" aria-selected="false">
                        <i class="fa <?= $rIcons[$reaccion["npr_reaccion"]] ?>"></i>
                    </a>
                </li>
        <?php
                $cont++;
            }
        };
        ?>
    </ul>
    <div class="tab-content" id="pills-tabContent-<?= $resultado['not_id'] ?>">
        <?php
        if ($reaccionesLista) {
            $cont = 0;
            $active = "";
            foreach ($reaccionesLista as $reaccion) {
                $claseReaccion = $rNameClass[$reaccion["npr_reaccion"]];
                $parametros["npr_reaccion"] = $reaccion["npr_reaccion"];
                $reaccionesUsuarioLista = SocialReacciones::listar($parametros);
                $active = ($cont == 0) ? "active" : "";
        ?>
                <div class="tab-pane fade show  animate__animated animate__fadeInRight <?= $active ?>" id="pills-<?= $claseReaccion ?>-<?= $resultado['not_id'] ?>" role="tabpanel" aria-labelledby="<?= $claseReaccion ?>-tab-<?= $resultado['not_id'] ?>">
                    <ul class="list-group">
                        <?php
                        if ($reaccionesUsuarioLista) {
                            foreach ($reaccionesUsuarioLista as $usuario) {
                                $fotoUsrReaccion = Usuarios::verificarFoto($usuario['uss_foto']);
                        ?>


                                <li class="list-group-item"> <img src="<?= $fotoUsrReaccion; ?>" class="img-circle user-img-circle" style="margin-right: 10px;" height="30" width="30" /><?= $usuario["uss_nombre"] ?></li>

                        <?php
                            }
                        };
                        ?>
                    </ul>
                </div>
        <?php
                $cont++;
            }
        };
        ?>
    </div>


</form>