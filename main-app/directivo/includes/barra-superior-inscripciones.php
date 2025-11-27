<?php
$busqueda = '';
if (!empty($_GET['busqueda'])) {
  $busqueda = $_GET['busqueda'];
  $filtro .= " AND (
  mat_id LIKE '%" . $busqueda . "%' 
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
  OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_primer_apellido)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(mat_nombres), '', TRIM(mat_primer_apellido)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(mat_primer_apellido), '', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_nombre2)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_segundo_apellido)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(mat_nombre2), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombre2)) LIKE '%".$busqueda."%'
  OR CONCAT(TRIM(mat_nombre2), ' ', TRIM(mat_segundo_apellido)) LIKE '%".$busqueda."%'
  OR gra_nombre LIKE '%" . $busqueda . "%'
  OR asp_email_acudiente LIKE '%" . $busqueda . "%'
  OR asp_nombre_acudiente LIKE '%" . $busqueda . "%'
  OR asp_nombre LIKE '%" . $busqueda . "%'
  OR asp_documento_acudiente LIKE '%" . $busqueda . "%'
  )";
}
$curso = '';
if (!empty($_GET['curso'])) {
  $curso = base64_decode($_GET['curso']);
  $filtro .= " AND asp_grado='".$curso."'";
}
$estado = '';
if (!empty($_GET['estado'])) {
  $estado = base64_decode($_GET['estado']);
  $filtro .= " AND asp_estado_solicitud='".$estado."'";
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
          <?=$frases[412][$datosUsuarioActual['uss_idioma']];?>
          <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <?php
          $grados = Grados::listarGrados(1);
          while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
            $estiloResaltado = '';
            if ($grado['gra_id'] == $curso) $estiloResaltado = 'style="color: ' . $Plataforma->colorUno . ';"';
          ?>
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?estado=<?= base64_encode($estado); ?>&curso=<?= base64_encode($grado['gra_id']); ?>&busqueda=<?= $busqueda; ?>" <?= $estiloResaltado; ?>><?= $grado['gra_nombre']; ?></a>
          <?php } ?>
          <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#FFF;">
          Filtrar por estado
          <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <?php
            foreach($ordenReal as $clave) {
            $estiloResaltado = '';
            if ($clave == $estado) $estiloResaltado = 'style="color: ' . $Plataforma->colorUno . ';"';
          ?>
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?estado=<?= base64_encode($clave); ?>&curso=<?= base64_encode($curso); ?>&busqueda=<?= $busqueda; ?>" <?= $estiloResaltado; ?>><?=$estadosSolicitud[$clave]?></a>
          <?php } ?>
          <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>

      <?php if (!empty($filtro)) { ?>
          <li class="nav-item"> <a class="nav-link" href="javascript:void(0);" style="color:<?= $Plataforma->colorUno; ?>;">|</a></li>

          <li class="nav-item"> <a class="nav-link" href="<?= $_SERVER['PHP_SELF']; ?>" style="color:<?= $Plataforma->colorUno; ?>;">Quitar filtros</a></li>
      <?php } ?>

    </ul>

    <form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
      <input type="hidden" name="curso" value="<?= base64_encode($curso); ?>" />
      <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?=$busqueda;?>">
      <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
    </form>

  </div>
</nav>