<?php
if (!empty($_GET['busqueda'])) {
  $busqueda = $_GET['busqueda'];
  $filtro .= " AND (
  soli_mensaje LIKE '%" . $busqueda . "%'
  OR uss_id LIKE '%".$busqueda."%' 
  OR uss_nombre LIKE '%".$busqueda."%' 
  OR uss_nombre2 LIKE '%".$busqueda."%' 
  OR uss_apellido1 LIKE '%".$busqueda."%' 
  OR uss_apellido2 LIKE '%".$busqueda."%' 
  OR uss_usuario LIKE '%".$busqueda."%' 
  OR uss_email LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(uss_nombre), ' ',TRIM(uss_apellido1), ' ', TRIM(uss_apellido2)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1), TRIM(uss_apellido2)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_apellido1)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1)) LIKE '%".$busqueda."%'
  OR mat_id LIKE '%" . $busqueda . "%' 
  OR mat_nombres LIKE '%" . $busqueda . "%' 
  OR mat_nombre2 LIKE '%" . $busqueda . "%' 
  OR mat_primer_apellido LIKE '%" . $busqueda . "%' 
  OR mat_segundo_apellido LIKE '%" . $busqueda . "%' 
  OR mat_documento LIKE '%" . $busqueda . "%' 
  OR mat_email LIKE '%" . $busqueda . "%'
  OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombres)) LIKE '%" . $busqueda . "%'
  OR CONCAT(TRIM(mat_primer_apellido), TRIM(mat_segundo_apellido), TRIM(mat_nombres)) LIKE '%" . $busqueda . "%'
  OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_nombres)) LIKE '%" . $busqueda . "%'
  OR CONCAT(TRIM(mat_primer_apellido), TRIM(mat_nombres)) LIKE '%" . $busqueda . "%'
  )";
}
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
    </ul>

    <form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
      <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?php if (isset($_GET['busqueda'])) echo $_GET['busqueda']; ?>">
      <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
    </form>

  </div>
</nav>