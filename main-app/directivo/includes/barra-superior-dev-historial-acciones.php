<?php
$busqueda = '';
if (isset($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
    $filtro .= " AND (
        hil_id LIKE '%" . $busqueda . "%' 
        OR hil_url LIKE '%" . $busqueda . "%' 
        OR ins_nombre LIKE '%" . $busqueda . "%' 
        OR ins_siglas LIKE '%" . $busqueda . "%'
        OR hil_titulo LIKE '%" . $busqueda . "%'
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
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
                    Filtrar por institución
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                    try{
                        $instituciones = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".instituciones WHERE 
                        ins_estado = 1 AND ins_enviroment='".ENVIROMENT."'");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    while ($datosInsti = mysqli_fetch_array($instituciones, MYSQLI_BOTH)) {
                        $estiloResaltado = '';
                        if ($datosInsti['ins_id'] == $instID) $estiloResaltado = 'style="color: ' . $Plataforma->colorUno . ';"';
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?insti=<?= base64_encode($datosInsti['ins_id']); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&busqueda=<?= $busqueda; ?>&year=<?= base64_encode($year); ?>&mes=<?=base64_encode($mes)?>" <?= $estiloResaltado; ?>><?= $datosInsti['ins_siglas']; ?></a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
                    Filtrar por Fecha
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    
                    <form class="dropdown-item" method="get" action="<?= $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="insti" value="<?= base64_encode($instID); ?>"/>
                        <input type="hidden" name="busqueda" value="<?= $busqueda; ?>"/>
                        <input type="hidden" name="year" value="<?= base64_encode($year); ?>"/>
                        <input type="hidden" name="mes" value="<?= base64_encode($mes); ?>"/>
                        <label>Fecha Desde:</label>
                        <input type="date" class="form-control" placeholder="desde"  name="desde" value="<?= $desde; ?>"/>

                        <label>Hasta</label>
                        <input type="date" class="form-control" placeholder="hasta"  name="hasta" value="<?= $hasta; ?>"/>
                        
                        <input type="submit" class="btn deepPink-bgcolor" name="fFecha" value="Filtrar" style="margin: 5px;">
                    </form>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
                    Filtrar por año
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                        $yearStartC=$yearStart;
                        $yearEndC=$yearEnd;
                        while($yearStartC <= $yearEndC){
                            $estiloResaltado = '';
                            if ($yearStartC == $year){ 
                                $estiloResaltado = 'style="color: ' . $Plataforma->colorUno . ';"';
                            }
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?insti=<?= base64_encode($instID); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&busqueda=<?= $busqueda; ?>&year=<?= base64_encode($yearStartC); ?>&mes=<?=base64_encode($mes)?>" <?= $estiloResaltado; ?>><?= $yearStartC; ?></a>
                    <?php 
                            $yearStartC++;
                        } 
                    ?>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
                    Filtrar por mes
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                        foreach ($mesesAgno as $key => $value) {
                                $estiloResaltado="";
                                if($key==$mes){ $estiloResaltado = 'style="color: ' . $Plataforma->colorUno . ';"';}
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?insti=<?= base64_encode($instID); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&busqueda=<?= $busqueda; ?>&year=<?=base64_encode($year); ?>&mes=<?=base64_encode($key)?>" <?= $estiloResaltado; ?>><?=$value?></a>
                    <?php
                        }
                    ?>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
                </div>
            </li>

        </ul>

        <form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
            <input type="hidden" name="insti" value="<?= base64_encode($instID); ?>"/>
            <input type="hidden" name="desde" value="<?= $desde; ?>"/>
            <input type="hidden" name="hasta" value="<?= $hasta; ?>"/>
            <input type="hidden" name="year" value="<?= base64_encode($year); ?>"/>
            <input type="hidden" name="mes" value="<?= base64_encode($mes); ?>"/>
            <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?= $busqueda; ?>">
            <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
        </form>

    </div>
</nav>