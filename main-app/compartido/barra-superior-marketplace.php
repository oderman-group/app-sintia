<?php
$filtro = '';

$cat = '';
if (!empty($_GET["cat"])) {
    $cat = base64_decode($_GET["cat"]);
    $filtro .= " AND prod_categoria='" . $cat . "'";
}

$company = '';
if (!empty($_GET["company"]) && is_numeric(base64_decode($_GET["company"]))) {
    $company = base64_decode($_GET["company"]);
    $filtro .= " AND prod_empresa='" . $company . "'";
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
            <?php if (empty($_SESSION["empresa"])) { ?>
                <li class="nav-item"><a href="empresas-agregar.php" class="nav-link" style="color:<?= $Plataforma->colorUno; ?>;"><i class="fa fa-shopping-cart"></i>Vende tus productos aqui</a></li>
            <?php } else { ?>
                <li class="nav-item"><a href="productos-agregar.php" class="nav-link" style="color:<?= $Plataforma->colorUno; ?>;"><i class="fa fa-plus"></i>Nuevo Producto</a></li>
            <?php } ?>

            <li class="nav-item"> <a class="nav-link" href="#" style="color:<?= $Plataforma->colorUno; ?>;">|</a></li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:<?= $Plataforma->colorUno; ?>;">Menú Marketplace<span class="fa fa-angle-down"></span></a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php if (!empty($_SESSION["empresa"])) { ?>
                        <a class="dropdown-item" href="productos-agregar.php">Agregar otro producto</span></a>
                        <a class="dropdown-item" href="marketplace.php?company=<?= base64_encode($_SESSION["empresa"]); ?>">Ver mis productos</span></a>
                    <?php } ?>
                        <a class="dropdown-item" href="marketplace.php">Ver todos los productos</span></a>
                        <a class="dropdown-item" href="mis-compras.php">Ver mis compras</span></a>
                        <hr>
                        <a class="dropdown-item" href="https://youtu.be/cmsQDO9tIrQ" target="_blank">Ver tutorial de uso de MarketPlace</span></a>
                        <a class="dropdown-item" href="mensajes-redactar.php?para=<?=base64_encode(1)?>&asunto=<?=base64_encode('REQUIERO ASESORÍA PARA USAR SINTIA MARKETPLACE')?>">Solicitar asesoría</span></a>
                </div>
            </li>

            <li class="nav-item"> <a class="nav-link" href="#">|</a></li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
                    Filtrar por categorías
                    <span class="fa fa-angle-down"></span>
                </a>
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
        </ul>

        <form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
            <input type="hidden" name="cat" value="<?= base64_encode($cat); ?>" />
            <input type="hidden" name="company" value="<?= base64_encode($company); ?>" />
            <input class="form-control mr-sm-2" type="search" placeholder="Búscar Producto..." aria-label="Search" name="busqueda" value="<?= $busqueda; ?>">
            <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
        </form>

    </div>
</nav>