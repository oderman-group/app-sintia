
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
 
      <?php if(Modulos::validarSubRol(['DT0145'])){?>
        <li class="nav-item"> <a class="nav-link" href="cursos-promocionar-estudiantes-detalles.php" style="color:<?=$Plataforma->colorUno;?>;">Promocionar Estudiantes</a></li>
      <?php }?>

      <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0063','DT0210','DT0211'])){?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:<?=$Plataforma->colorUno;?>;">
              Más opciones
            <span class="fa fa-angle-down"></span>
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php if(Modulos::validarSubRol(['DT0063'])){?>
            <a class="dropdown-item" href="cursos-intensidad.php">I.H por curso</a>
            <?php } if(Modulos::validarSubRol(['DT0210'])){?>
            <a class="dropdown-item" href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas ejecutar esta acción?','question','cursos-cambiar-matricula.php')">Poner en $0 la matricula</a>
            <?php } if(Modulos::validarSubRol(['DT0211'])){?>
            <a class="dropdown-item" href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas ejecutar esta acción?','question','cursos-cambiar-pension.php')">Poner en $0 la pensión</a>
            <?php }?>
          <div class="dropdown-divider"></div>
          </div>
        </li>
        <li class="nav-item"> <a class="nav-link" href="#">|</a></li>
      <?php }?>

      <?php if(array_key_exists(10,$arregloModulos) ){?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
          Filtrar por tipo
        <span class="fa fa-angle-down"></span>
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">	
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?tipo=<?=base64_encode(GRADO_GRUPAL);?>">Grupal</a>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?tipo=<?=base64_encode(GRADO_INDIVIDUAL);?>">Individual</a>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>">Todos</a>
          </div>
        </li>
      <?php }?>

    </ul>


  </div>
</nav>