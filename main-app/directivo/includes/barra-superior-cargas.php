<?php
$busqueda = '';
if (!empty($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
    $filtro .= " AND (
        car_id LIKE '%" . $busqueda . "%' 
        OR uss_nombre LIKE '%".$busqueda."%' 
        OR uss_nombre2 LIKE '%".$busqueda."%' 
        OR uss_apellido1 LIKE '%".$busqueda."%' 
        OR uss_apellido2 LIKE '%".$busqueda."%' 
        OR gra_nombre LIKE '%" . $busqueda . "%' 
        OR mat_nombre LIKE '%" . $busqueda . "%'
        OR CONCAT(TRIM(uss_nombre), ' ',TRIM(uss_apellido1), ' ', TRIM(uss_apellido2)) LIKE '%".$busqueda."%'
        OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1), TRIM(uss_apellido2)) LIKE '%".$busqueda."%'
        OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_apellido1)) LIKE '%".$busqueda."%'
        OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1)) LIKE '%".$busqueda."%'
        )";
}
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

            <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0035','DT0142','DT0033','DT0044'])){?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:<?= $Plataforma->colorUno; ?>;">
                        Más opciones
                        <span class="fa fa-angle-down"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php if(Modulos::validarSubRol(['DT0035'])){?>
                        <a class="dropdown-item" href="cargas-indicadores-obligatorios.php">Indicadores obligatorios</a>
                        <?php } if(Modulos::validarSubRol(['DT0142'])){?>
                        <a class="dropdown-item" href="cargas-comportamiento-filtros.php">Notas de Comportamiento</a>
                        <div class="dropdown-divider"></div>
                        <?php } if(Modulos::validarSubRol(['DT0033'])){?>
                        <a class="dropdown-item" href="javascript:void(0);"  data-toggle="modal" data-target="#modalTranferirCargas"  >Transferir cargas</a>
                        <?php } if(Modulos::validarSubRol(['DT0044'])){?>
                        <a class="dropdown-item" href="cargas-estilo-notas.php">Estilo de notas</a>
                        <?php }?>

                    </div>
                </li>

                <li class="nav-item"> <a class="nav-link" href="javascript:void(0);">|</a></li>
                <?php
                $idModal = "modalTranferirCargas";
                $contenido = "../directivo/cargas-transferir-modal.php";
                include("../compartido/contenido-modal.php");
                } ?>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
                    Filtrar por curso
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                    $grados = Grados::listarGrados(1);
                    while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
                        $estiloResaltado = '';
                        if ($grado['gra_id'] == $curso) $estiloResaltado = 'style="color: ' . $Plataforma->colorUno . ';"';
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?curso=<?= base64_encode($grado['gra_id']); ?>&busqueda=<?=$busqueda;?>" <?= $estiloResaltado; ?>><?= $grado['gra_nombre']; ?></a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
                </div>
            </li>

            <?php if (!empty($filtro)) { ?>
                <li class="nav-item"> <a class="nav-link" href="javascript:void(0);" style="color:<?= $Plataforma->colorUno; ?>;">|</a></li>

                <li class="nav-item"> <a class="nav-link" href="<?= $_SERVER['PHP_SELF']; ?>" style="color:<?= $Plataforma->colorUno; ?>;">Quitar filtros</a></li>
            <?php } ?>
        </ul>

        <form class="form-inline my-2 my-lg-0" action="cargas.php" method="get">
            <input type="hidden" name="curso" value="<?= base64_encode($curso) ?>"/>
            <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?=$busqueda?>">
            <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
        </form>

    </div>
</nav>