<?php
$filtro = '';
$tipo = '';
if(!empty($_GET["tipo"])){ $tipo = base64_decode($_GET["tipo"]); $filtro .= " AND uss_tipo='".$tipo."'";}
$busqueda = '';
if (isset($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
    $filtro .= " AND (
      uss_id LIKE '%".$busqueda."%' 
      OR uss_nombre LIKE '%".$busqueda."%' 
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
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #41c4c4;">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="navbar-collapse" id="navbarSupportedContent">

  <ul class="navbar-nav mr-auto">
    <?php if(Modulos::validarPermisoEdicion()){?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:<?=$Plataforma->colorUno;?>;">
            Más acciones
            <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="javascript:void(0);" 
          onClick="sweetConfirmacion('Alerta!','Desea Bloquear a todos los estudiantes?','question','usuarios-bloquear.php?tipo=<?=base64_encode(4)?>')"
          >Bloquear estudiantes</a>
          <a class="dropdown-item" href="javascript:void(0);" 
          onClick="sweetConfirmacion('Alerta!','Desea Desbloquear a todos los estudiantes?','question','usuarios-desbloquear.php?tipo=<?=base64_encode(4)?>')"
          >Desbloquear estudiantes</a>
          <a class="dropdown-item" href="javascript:void(0);" 
          onClick="sweetConfirmacion('Alerta!','Desea Bloquear a todos los docentes?','question','usuarios-bloquear.php?tipo=<?=base64_encode(2)?>')"
          >Bloquear docentes</a>
          <a class="dropdown-item" href="javascript:void(0);" 
          onClick="sweetConfirmacion('Alerta!','Desea Desbloquear a todos los docentes?','question','usuarios-desbloquear.php?tipo=<?=base64_encode(2)?>')"
          >Desbloquear docentes</a>
          <a>&nbsp;</a>
          <?php 
          if(Modulos::validarPermisoEdicion()) {
            if(Modulos::validarSubRol(['DT0125']) ) {?>
              <a class="dropdown-item" href="usuarios-importar-excel.php">Importar usuarios</a>
          <?php 
            } 
            if(Modulos::validarSubRol(['DT0144']) ) {?>
              <a class="dropdown-item" href="usuarios-generar-clave-filtros.php">Generar contraseña masiva</a>
          <?php 
            } 
          }
          ?>
          <?php if(Modulos::validarSubRol(['DT0201']) ) {?>
          <a class="dropdown-item" href="usuarios-anios.php">Consultar todos los años</a>
          <?php }?>
        </div>
      </li>

      <li class="nav-item"> <a class="nav-link" href="javascript:void(0);">|</a></li>
    <?php }?>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#FFF;">
        Filtrar por tipo de usuario
		  <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <?php
            $tiposUsuarios = TipoUsuario::listarTiposUsuarios($baseDatosServicios, $conexionPDO);
            while($tipoUsuario = $tiposUsuarios->fetch()){
                $estiloResaltado = '';
                if($tipoUsuario['pes_id'] == $tipo) $estiloResaltado = 'style="color: '.$Plataforma->colorUno.';"';
                if($tipoUsuario['pes_id'] != TIPO_ESTUDIANTE ) {
            ?>	
            <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?tipo=<?=base64_encode($tipoUsuario['pes_id']);?>&busqueda=<?=$busqueda?>" <?=$estiloResaltado;?>><?=$tipoUsuario['pes_nombre'];?></a>
            <?php }?>
        <?php }?>
        <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>

      <?php if (!empty($filtro)) { ?>
        <li class="nav-item"> <a class="nav-link" href="javascript:void(0);" style="color:<?= $Plataforma->colorUno; ?>;">|</a></li>

        <li class="nav-item"> <a class="nav-link" href="<?= $_SERVER['PHP_SELF']; ?>" style="color:<?= $Plataforma->colorUno; ?>;">Quitar filtros</a></li>
      <?php } ?>
  </ul> 

    <form class="form-inline my-2 my-lg-0" action="usuarios.php" method="get">
        <!-- <input type="hidden" name="tipo" value="<?= base64_encode($tipo); ?>"/>
        <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?=$busqueda?>">
      <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button> -->
    </form>

  </div>
</nav>