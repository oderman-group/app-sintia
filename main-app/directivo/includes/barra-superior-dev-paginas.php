<?php
$busqueda = '';
if (!empty($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
    $filtro .= " AND (
        pagp_id LIKE '%" . $busqueda . "%' 
        OR pagp_pagina LIKE '%" . $busqueda . "%' 
        OR pagp_ruta LIKE '%".$busqueda."%' 
        OR pagp_palabras_claves LIKE '%".$busqueda."%' 
        OR pagp_descripcion LIKE '%".$busqueda."%' 
        OR mod_nombre LIKE '%".$busqueda."%' 
        OR pes_nombre LIKE '%".$busqueda."%' 
        )";
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
                    Filtrar Por Usuario
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                    try{
                        $consultaFiltro = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_perfiles WHERE pes_disponible = 1");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    while ($datosFiltro = mysqli_fetch_array($consultaFiltro, MYSQLI_BOTH)) {
                        $estiloResaltado = '';
                        if ($datosFiltro['pes_id'] == $ussDev) $estiloResaltado = 'style="color: ' . $Plataforma->colorUno . ';"';
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?uss=<?= base64_encode($datosFiltro['pes_id']); ?>&modulo=<?= base64_encode($moduloDev); ?>&busqueda=<?= $busqueda; ?>" <?= $estiloResaltado; ?>><?= $datosFiltro['pes_nombre']; ?></a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#FFF;">
                    Filtrar Por Modulo
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                    try{
                        $consultaFiltro = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".modulos");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    while ($datosFiltro = mysqli_fetch_array($consultaFiltro, MYSQLI_BOTH)) {
                        $estiloResaltado = '';
                        if ($datosFiltro['mod_id'] == $moduloDev) $estiloResaltado = 'style="color: ' . $Plataforma->colorUno . ';"';
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?uss=<?= base64_encode($ussDev); ?>&modulo=<?= base64_encode($datosFiltro['mod_id']); ?>&busqueda=<?= $busqueda; ?>" <?= $estiloResaltado; ?>><?= $datosFiltro['mod_nombre']; ?></a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
                </div>
            </li>

        </ul>

        <form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
            <input type="hidden" name="uss" value="<?= $ussDev; ?>"/>
            <input type="hidden" name="modulo" value="<?= $moduloDev; ?>"/>
            <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?= $busqueda; ?>">
            <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
        </form>

    </div>
</nav>