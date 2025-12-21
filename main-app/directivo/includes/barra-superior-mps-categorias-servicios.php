<?php
$filtro = '';
$busqueda = '';
if (!empty($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
    $filtro .= " AND (
        svcat_id LIKE '%" . $busqueda . "%' 
        OR svcat_nombre LIKE '%" . $busqueda . "%' 
        )";
}
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse" id="navbarSupportedContent">
        <?php if (!empty($filtro)) { ?>
            <a href="mps-categorias-servicios.php" class="btn deepPink-bgcolor my-2 my-sm-0" type="submit">Quitar filtro</a>
        <?php } ?>
        <ul class="navbar-nav mr-auto">
        </ul>

        <form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
            <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?=$busqueda?>">
            <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
        </form>

    </div>
</nav>