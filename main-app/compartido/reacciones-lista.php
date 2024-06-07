<?php
$animate = "";
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
    require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
    include_once(ROOT_PATH . "/main-app/class/SocialReacciones.php");
    $rName = array("", "Me gusta", "Me encanta", "Me divierte", "Me entristece");
    $rIcons = array("", "fa-thumbs-o-up", "fa-heart", "fa-smile-o", "fa-frown-o");
    $rNameClass = array("", "me_gusta", "me_encanta", "me_divierte", "me_entristece");
    $resultado['not_id']=$_POST["id"];
}
$parametros = ["npr_noticia" => $resultado['not_id']];
$consultaReaccionesagrupadas = SocialReacciones::contarReacciones($parametros);
if ($consultaReaccionesagrupadas) {
    foreach ($consultaReaccionesagrupadas as $reaccion) {
        $claseReaccion = $rNameClass[$reaccion["npr_reaccion"]];
?>
    <div class="popover-content <?= $claseReaccion ?>">
        <i class="fa <?= $rIcons[$reaccion["npr_reaccion"]]; ?>"></i> <?= $rName[$reaccion["npr_reaccion"]]; ?> (<?= $reaccion["cantidad"]; ?>)
    </div>
<?php
    };
}
?>