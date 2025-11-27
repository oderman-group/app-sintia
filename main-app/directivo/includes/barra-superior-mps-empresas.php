<?php
$filtro = '';
$busqueda = '';
if (!empty($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
    $filtro .= " AND (
        emp_id LIKE '%" . $busqueda . "%' 
        OR emp_nombre LIKE '%" . $busqueda . "%' 
        OR svcat_nombre LIKE '%" . $busqueda . "%' 
        OR ins_nombre LIKE '%" . $busqueda . "%' 
        )";
}

$cat = '';
if (!empty($_GET["cat"])) {
    $cat = base64_decode($_GET["cat"]);
    $filtro .= " AND svcat_id='" . $cat . "'";
}

$insti = '';
if (!empty($_GET["insti"])) {
    $insti = base64_decode($_GET["insti"]);
    $filtro .= " AND emp_institucion='" . $insti . "'";
}
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#FFF;">
                    Filtrar por categorías
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                    $categorias = mysqli_query($conexion, "SELECT * FROM " . $baseDatosMarketPlace . ".servicios_categorias");
                    while ($cate = mysqli_fetch_array($categorias, MYSQLI_BOTH)) {
                        $estiloResaltado = '';
                        if (!empty($_GET["cat"]) && $cat==$cate[0]){ $estiloResaltado = 'style="color: orange;"';}
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?insti=<?= base64_encode($insti); ?>&cat=<?= base64_encode($cate[0]); ?>&busqueda=<?= $busqueda; ?>" <?= $estiloResaltado; ?>><span><?= strtoupper($cate['svcat_nombre']); ?></span></a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;"><?= strtoupper($frases[180][$datosUsuarioActual['uss_idioma']]); ?></a>
                </div>
            </li>
            
            <li class="nav-item"> <a class="nav-link" href="javascript:void(0);">|</a></li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#FFF;">
                    Filtrar por Institución
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                    $categorias = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".instituciones WHERE ins_enviroment='".ENVIROMENT."'");
                    while ($instit = mysqli_fetch_array($categorias, MYSQLI_BOTH)) {
                        $estiloResaltado = '';
                        if (!empty($_GET["insti"]) && $insti==$instit[0]){ $estiloResaltado = 'style="color: orange;"';}
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?insti=<?= base64_encode($instit[0]); ?>&cat=<?= base64_encode($cat); ?>&busqueda=<?= $busqueda; ?>" <?= $estiloResaltado; ?>><span><?= strtoupper($instit['ins_siglas']); ?></span></a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;"><?= strtoupper($frases[180][$datosUsuarioActual['uss_idioma']]); ?></a>
                </div>
            </li>

            <?php if (!empty($filtro)) { ?>
                <li class="nav-item"> <a class="nav-link" href="javascript:void(0);" style="color:<?= $Plataforma->colorUno; ?>;">|</a></li>

                <li class="nav-item"> <a class="nav-link" href="mps-empresas.php" style="color:<?= $Plataforma->colorUno; ?>;">Quitar filtros</a></li>
            <?php } ?>

        </ul>

        <form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
            <input type="hidden" name="insti" value="<?= base64_encode($insti); ?>" />
            <input type="hidden" name="cat" value="<?= base64_encode($cat); ?>" />
            <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?=$busqueda?>">
            <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
        </form>

    </div>
</nav>