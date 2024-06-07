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
    $parametros["npr_noticia"] = $resultado['not_id'];
    $parametros["npr_usuario"] = $_SESSION["id"];
    $usrReacciones = SocialReacciones::consultar($parametros);
    
}
$i = 1;
while ($i <= 4) {
    if (!empty($usrReacciones['npr_reaccion']) && $i == $usrReacciones['npr_reaccion']) {
        $li_seleccion = $rNameClass[$usrReacciones["npr_reaccion"]];
    } else {
        $li_seleccion = '';
    }
?>
    <li class="dropdown-item <?= $li_seleccion; ?>" style="cursor: pointer; color:#6d84b4;" onclick="reaccionar('<?= $resultado['not_id']; ?>','<?= $i ?>','','<?= $datosUsuarioActual['uss_nombre']; ?>','<?= $_SESSION['id'] ?>')">
        <i class="fa <?= $rIcons[$i]; ?>"></i><?= $rName[$i]; ?></a>
    </li>
<?php $i++;
} ?>