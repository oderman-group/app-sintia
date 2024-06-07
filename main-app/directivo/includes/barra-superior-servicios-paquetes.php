<?php
$filtro = '';
$busqueda = '';
if (!empty($_GET["busqueda"])) {
    $busqueda = $_GET['busqueda'];
    $filtro .= " AND (plns_nombre LIKE '%" . $busqueda . "%' OR plns_tipo LIKE '%" . $busqueda . "%')";
}
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #41c4c4;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

        </ul>

        <form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
            <?php if (!empty($filtro)) { ?>
                <a class="btn bg-orange my-2 my-sm-0 mr-2" href="<?= $_SERVER['PHP_SELF']; ?>">Borrar Busqueda</a>
            <?php } ?>
            <input class="form-control mr-sm-2" type="search" placeholder="Búscar Paquete..." aria-label="Search" name="busqueda" value="<?= $busqueda; ?>">
            <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
        </form>

    </div>
</nav>