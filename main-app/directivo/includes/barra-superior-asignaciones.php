<?php
  $filtro = '';
  $busqueda='';
  if (!empty($_GET['busqueda'])) {
      $busqueda = $_GET['busqueda'];
      $filtro .= " AND (
        uss_nombre LIKE '%".$busqueda."%' 
        OR uss_nombre2 LIKE '%".$busqueda."%' 
        OR uss_apellido1 LIKE '%".$busqueda."%' 
        OR uss_apellido2 LIKE '%".$busqueda."%' 
        OR uss_usuario LIKE '%".$busqueda."%' 
        OR uss_email LIKE '%".$busqueda."%'
        OR uss_documento LIKE '%".$busqueda."%'
        OR CONCAT(TRIM(uss_nombre), ' ',TRIM(uss_apellido1), ' ', TRIM(uss_apellido2)) LIKE '%".$busqueda."%'
        OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1), TRIM(uss_apellido2)) LIKE '%".$busqueda."%'
        OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_apellido1)) LIKE '%".$busqueda."%'
        OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1)) LIKE '%".$busqueda."%'
      )";
  }
  $tipo = '';
  if (!empty($_GET['tipo'])) {
      $tipo = base64_decode($_GET['tipo']);
      $filtro .= " AND gal_tipo='".$tipo."'";
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
            Filtrar por tipo de encuesta
		  <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?idE=<?=base64_encode($idE);?>&tipo=<?=base64_encode(DIRECTIVO);?>&busqueda=<?=$busqueda;?>" <?=$tipo == DIRECTIVO ? 'style="color: '.$Plataforma->colorUno.';"' : "";?>><?=DIRECTIVO;?></a>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?idE=<?=base64_encode($idE);?>&tipo=<?=base64_encode(DOCENTE);?>&busqueda=<?=$busqueda;?>" <?=$tipo == DOCENTE ? 'style="color: '.$Plataforma->colorUno.';"' : "";?>><?=DOCENTE;?></a>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?idE=<?=base64_encode($idE);?>&tipo=<?=base64_encode(AREA);?>&busqueda=<?=$busqueda;?>" <?=$tipo == AREA ? 'style="color: '.$Plataforma->colorUno.';"' : "";?>><?=AREA;?></a>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?idE=<?=base64_encode($idE);?>&tipo=<?=base64_encode(MATERIA);?>&busqueda=<?=$busqueda;?>" <?=$tipo == MATERIA ? 'style="color: '.$Plataforma->colorUno.';"' : "";?>><?=MATERIA;?></a>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?idE=<?=base64_encode($idE);?>&tipo=<?=base64_encode(CURSO);?>&busqueda=<?=$busqueda;?>" <?=$tipo == CURSO ? 'style="color: '.$Plataforma->colorUno.';"' : "";?>><?=CURSO;?></a>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?idE=<?=base64_encode($idE);?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>

      <?php if (!empty($filtro)) { ?>
          <li class="nav-item"> <a class="nav-link" href="javascript:void(0);" style="color:<?= $Plataforma->colorUno; ?>;">|</a></li>

          <li class="nav-item"> <a class="nav-link" href="<?= $_SERVER['PHP_SELF']; ?>?idE=<?=base64_encode($idE);?>" style="color:<?= $Plataforma->colorUno; ?>;">Quitar filtros</a></li>
      <?php } ?>

    </ul>

    <form class="form-inline my-2 my-lg-0" action="asignaciones.php" method="get">
        <input type="hidden" name="idE" value="<?=base64_encode($idE);?>"/>
        <input type="hidden" name="tipo" value="<?=base64_encode($tipo);?>"/>
        <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?=$busqueda;?>">
      <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
    </form>

  </div>
</nav>