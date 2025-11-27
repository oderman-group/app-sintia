<?php
$filtro = '';
$cat = '';
if (!empty($_GET["cat"])) {
    $cat = base64_decode($_GET["cat"]);
    $filtro .= " AND prod_categoria='" . $cat . "'";
}
$estado = '';
if (!empty($_GET["estado"])) {
    $estado = base64_decode($_GET["estado"]);
    $filtro .= " AND misc_estado_compra='" . $estado . "'";
}
$busqueda = '';
if (!empty($_GET["busqueda"])) {
    $busqueda = $_GET['busqueda'];
    $filtro .= " AND (prod_nombre LIKE '%" . $busqueda . "%' OR prod_descripcion LIKE '%" . $busqueda . "%' OR prod_keywords LIKE '%" . $busqueda . "%')";
}
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">Filtrar por categorías<span class="fa fa-angle-down"></span></a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                    $categorias = mysqli_query($conexion, "SELECT * FROM " . $baseDatosMarketPlace . ".categorias_productos");
                    while ($cate = mysqli_fetch_array($categorias, MYSQLI_BOTH)) {
                        $estiloResaltado = '';
                        if (!empty($_GET["cat"]) && $cat==$cate['catp_id']){ $estiloResaltado = 'style="color: orange;"';}
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?cat=<?= base64_encode($cate['catp_id']); ?>" <?= $estiloResaltado; ?>><span><?= strtoupper($cate['catp_nombre']); ?></span></a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;"><?= strtoupper($frases[180][$datosUsuarioActual['uss_idioma']]); ?></a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">Filtrar por Estado de compra<span class="fa fa-angle-down"></span></a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                    foreach($estadoCompra as $key => $value) {
                        $estiloResaltado = '';
                        if (!empty($_GET["estado"]) && $estado==$key){ $estiloResaltado = 'style="color: orange;"';}
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?estado=<?= base64_encode($key); ?>" <?= $estiloResaltado; ?>><span><?= strtoupper($value); ?></span></a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;"><?= strtoupper($frases[180][$datosUsuarioActual['uss_idioma']]); ?></a>
                </div>
            </li>

            <?php if (!empty($filtro)) { ?>
                <li class="nav-item"> <a class="nav-link" href="javascript:void(0);" style="color:<?= $Plataforma->colorUno; ?>;">|</a></li>

                <li class="nav-item"> <a class="nav-link" href="<?= $_SERVER['PHP_SELF']; ?>" style="color:<?= $Plataforma->colorUno; ?>;">Quitar filtros</a></li>
            <?php } ?>
        </ul>

        <form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
            <input type="hidden" name="cat" value="<?= base64_encode($cat); ?>" />
            <input type="hidden" name="estado" value="<?= base64_encode($estado); ?>" />
            <input class="form-control mr-sm-2" type="search" placeholder="Búscar Producto..." aria-label="Search" name="busqueda" value="<?= $busqueda; ?>">
            <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
        </form>

    </div>
</nav>