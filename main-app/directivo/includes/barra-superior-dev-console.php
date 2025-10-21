<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="navbar-collapse" id="navbarSupportedContent">

    <ul class="navbar-nav mr-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
          Filtrar por carpetas
          <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php $estiloResaltado = 'style="color: ' . $Plataforma->colorUno . ';"'; ?>
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?carpeta=<?=base64_encode(1)?>" <?php if ($carpeta==1){ echo $estiloResaltado;} ?>>Directivo</a>
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?carpeta=<?=base64_encode(2)?>" <?php if ($carpeta==2){ echo $estiloResaltado;} ?>>Docente</a>
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?carpeta=<?=base64_encode(3)?>" <?php if ($carpeta==3){ echo $estiloResaltado;} ?>>Estudiante</a>
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?carpeta=<?=base64_encode(4)?>" <?php if ($carpeta==4){ echo $estiloResaltado;} ?>>Acudiente</a>
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?carpeta=<?=base64_encode(5)?>" <?php if ($carpeta==5){ echo $estiloResaltado;} ?>>Compartido</a>
        </div>
      </li>
    </ul>
  </div>
</nav>