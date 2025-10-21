<?php
$busqueda = '';
if (!empty($_GET['busqueda'])) {
  $busqueda = base64_decode($_GET['busqueda']);
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
  OR gra_nombre LIKE '%" . $busqueda . "%'
  )";
}
$curso = '';
if (!empty($_GET['curso'])) {
    $curso = base64_decode($_GET['curso']);
}
$resp = '';
if (!empty($_GET['resp'])) {
    $resp = base64_decode($_GET['resp']);
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
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?resp=<?= base64_encode($resp); ?>&curso=<?= base64_encode($grado['gra_id']); ?>&busqueda=<?= $busqueda; ?>" <?= $estiloResaltado; ?>><?= $grado['gra_nombre']; ?></a>
          <?php } ?>
          <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
          Filtrar por Respuesta
          <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?resp=<?= base64_encode(1); ?>&curso=<?= base64_encode($curso); ?>&busqueda=<?= $busqueda; ?>" <?= $estiloResaltado; ?>>SI</a>
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?resp=<?= base64_encode(2); ?>&curso=<?= base64_encode($curso); ?>&busqueda=<?= $busqueda; ?>" <?= $estiloResaltado; ?>>NO</a>
          <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>

    </ul>

    <form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
      <input type="hidden" name="curso" value="<?= base64_encode($curso); ?>" />
      <?php
      if (!empty($_GET["resp"])) {
      ?>
        <input type="hidden" name="resp" value="<?= $_GET['resp']; ?>" />
      <?php
      }
      ?>
      <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?= $busqueda; ?>">
      <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
    </form>

  </div>
</nav>