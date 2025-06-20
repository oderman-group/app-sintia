<?php
$busqueda = '';
if (!empty($_GET['busqueda'])) {
  $busqueda = $_GET['busqueda'];
  $filtro .= " AND (
      dr_observaciones LIKE '%".$busqueda."%'
      OR uss_id LIKE '%" . $busqueda . "%' 
      OR uss_nombre LIKE '%" . $busqueda . "%' 
      OR uss_nombre2 LIKE '%" . $busqueda . "%' 
      OR uss_apellido1 LIKE '%" . $busqueda . "%' 
      OR uss_apellido2 LIKE '%" . $busqueda . "%' 
      OR uss_usuario LIKE '%" . $busqueda . "%' 
      OR uss_email LIKE '%" . $busqueda . "%'
      OR CONCAT(TRIM(uss_nombre), ' ',TRIM(uss_apellido1), ' ', TRIM(uss_apellido2)) LIKE '%" . $busqueda . "%'
      OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1), TRIM(uss_apellido2)) LIKE '%" . $busqueda . "%'
      OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_apellido1)) LIKE '%" . $busqueda . "%'
      OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1)) LIKE '%" . $busqueda . "%'
      OR mat_id LIKE '%".$busqueda."%' 
      OR mat_nombres LIKE '%".$busqueda."%' 
      OR mat_nombre2 LIKE '%".$busqueda."%' 
      OR mat_primer_apellido LIKE '%".$busqueda."%' 
      OR mat_segundo_apellido LIKE '%".$busqueda."%' 
      OR mat_documento LIKE '%".$busqueda."%' 
      OR mat_email LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_primer_apellido), TRIM(mat_segundo_apellido), TRIM(mat_nombres)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_primer_apellido), TRIM(mat_nombres)) LIKE '%".$busqueda."%'
      OR gra_nombre LIKE '%".$busqueda."%' 
      OR dcat_nombre LIKE '%".$busqueda."%' 
      OR dfal_nombre LIKE '%".$busqueda."%'  
      )";
}
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #41c4c4;">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="navbar-collapse" id="navbarSupportedContent">

    <ul class="navbar-nav mr-auto">
    </ul>

    <form class="form-inline my-2 my-lg-0" action="<?=$_SERVER['PHP_SELF'];?>" method="get">
      <?php
        if(!empty($_GET["est"])){
      ?>
        <input type="hidden" name="est" value="<?= $_GET['est']; ?>"/>
      <?php
        }
        if(!empty($_GET["falta"])){
      ?>
        <input type="hidden" name="falta" value="<?= $_GET['falta']; ?>"/>
      <?php
        }
        if($datosUsuarioActual['uss_tipo']!=5 and !empty($_GET["fest"])){
      ?>
        <input type="hidden" name="fest" value="<?= $_GET['fest']; ?>"/>
      <?php
        }
      ?>
      <input type="hidden" name="filtros" value="<?= base64_encode(1); ?>"/>
      <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?=$busqueda; ?>">
      <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
    </form>

  </div>
</nav>