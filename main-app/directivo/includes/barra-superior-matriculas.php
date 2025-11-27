<?php
  $filtro = '';
  $busqueda='';
  if (!empty($_GET['busqueda'])) {
      $busqueda = $_GET['busqueda'];
      $filtro .= " AND (
      mat_id LIKE '%".$busqueda."%' 
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
      OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_primer_apellido)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_nombres), '', TRIM(mat_primer_apellido)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_primer_apellido), '', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_nombre2)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_segundo_apellido)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_nombre2), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombre2)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_segundo_apellido), ' ', TRIM(mat_primer_apellido)) LIKE '%".$busqueda."%'
      OR CONCAT(TRIM(mat_nombre2), ' ', TRIM(mat_segundo_apellido)) LIKE '%".$busqueda."%'
      )";
      
  }

  $curso = '';
  $cursoActual = '';
  if (!empty($_GET['curso'])) {
      $curso = base64_decode($_GET['curso']);
      $cursoActual=GradoServicios::consultarCurso($curso);
  }

  $quitarFiltro = '';
  if (!empty($_GET['quitar'])) {
      $quitarFiltro = base64_decode($_GET['quitar']);
  }

  $keys = $redis->keys("MATRI_".$_SESSION['inst'].":*");
  if (empty($keys) || (!empty($_GET['busqueda']) && $quitarFiltro==0) || (empty($_GET['busqueda']) && !empty($_GET['quitar']) && $quitarFiltro==1)) {
      $redis->flushDB();
      $consulta = Estudiantes::listarEstudiantes(0, $filtro, '',$cursoActual);
      if (mysqli_num_rows($consulta) > 0) {
          while($matData = mysqli_fetch_assoc($consulta)){
              $redis->set("MATRI_".$_SESSION['inst'].":".$matData['mat_id'], json_encode($matData));
          }
      }
      $keys = $redis->keys("MATRI_".$_SESSION['inst'].":*");
  }
  
  $estadoM = '';
  if (!empty($_GET['curso']) || !empty($_GET['estadoM'])) {
      if (!empty($_GET['estadoM'])) {
          $estadoM = base64_decode($_GET['estadoM']);
      }
      // Filtra las claves según los criterios
      $keys = array_filter($keys, function ($key) use ($redis, $curso, $estadoM) {
          $matData = $redis->get($key);
          $resultado = json_decode($matData, true);

          if (!empty($curso) && !empty($estadoM)) {
              return $resultado['mat_grado'] == $curso && $resultado['mat_estado_matricula'] == $estadoM;
          }

          if (!empty($curso) && empty($estadoM)) {
              return $resultado['mat_grado'] == $curso;
          }

          if (empty($curso) && !empty($estadoM)) {
              return $resultado['mat_estado_matricula'] == $estadoM;
          }
      });
  }
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
 
      <?php if(Modulos::validarSubRol(['DT0002'])){?>
      <li class="nav-item"> <a class="nav-link" href="estudiantes-promedios.php" style="color:<?=$Plataforma->colorUno;?>;">Promedios estudiantiles</a></li>
      <?php }?>

      <?php if(Modulos::validarSubRol(['DT0077','DT0080','DT0075'])){?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:<?=$Plataforma->colorUno;?>;">
            Menú matrículas
		  <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <?php if(Modulos::validarSubRol(['DT0077'])){?>
        <a class="dropdown-item" href="estudiantes-importar-excel.php">Importar matrículas desde Excel</a>
        <?php } if(Modulos::validarSubRol(['DT0080'])){?>
        <a class="dropdown-item" href="estudiantes-consolidado-final.php">Consolidado final</a>
        <?php } if(Modulos::validarSubRol(['DT0075'])){?>
        <a class="dropdown-item" href="estudiantes-nivelaciones.php">Nivelaciones</a>
        <?php }?>
        </div>
      </li>
      <?php }?>

      <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0212','DT0213','DT0214','DT0215','DT0175','DT0216','DT0149'])){?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:<?=$Plataforma->colorUno;?>;">
            Más opciones
		  <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <?php if(Modulos::validarSubRol(['DT0212'])){?>
          <a class="dropdown-item" href="javascript:void(0);"
          onClick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-matricular-todos.php')">Matricular a todos</a>
          <?php } if(Modulos::validarSubRol(['DT0213'])){?>
          <a class="dropdown-item" href="javascript:void(0);"
          onClick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-matriculas-cancelar.php')">Cancelar a todos</a>
          <?php } if(Modulos::validarSubRol(['DT0214'])){?>
          <a class="dropdown-item" href="javascript:void(0);" 
          onClick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-grupoa-todos.php')">Asignar a todos al grupo A</a>
          <?php } if(Modulos::validarSubRol(['DT0215'])){?>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="javascript:void(0);" 
          onClick="sweetConfirmacion('Alerta!','Esta opción removerá a todos lo estudiantes que no estén en estado Matriculado, desea continuar?','question','estudiantes-inactivos-remover.php')"
          >Remover estudiantes Inactivos este año</a></a>
          <?php } if(Modulos::validarSubRol(['DT0175'])){?>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="javascript:void(0);" 
          onClick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-documento-usuario-actualizar.php')">Colocar documento como usuario de acceso</a>
          <?php } if(Modulos::validarSubRol(['DT0216'])){?>
          <a class="dropdown-item" href="javascript:void(0);" 
          onClick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-crear-usuarios.php')">Verificar y generar credenciales a estudiantes</a>
          <?php } if(Modulos::validarSubRol(['DT0149'])){?>
          <a class="dropdown-item" href="filtro-general-folio.php">Generar Folios</a>
          <?php }?>
        </div>
      </li>
      <?php }?>

      <li class="nav-item"> <a class="nav-link" href="javascript:void(0);">|</a></li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
            Filtrar por curso
		  <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <?php
        $grados = Grados::listarGrados(1);
        while($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)){
            $estiloResaltado = '';
            if($grado['gra_id'] == $curso) $estiloResaltado = 'style="color: '.$Plataforma->colorUno.';"';
        ?>	
            <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?estadoM=<?=base64_encode($estadoM);?>&curso=<?=base64_encode($grado['gra_id']);?>&busqueda=<?=$busqueda;?>" <?=$estiloResaltado;?>><?=$grado['gra_nombre'];?></a>
        <?php }?>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
        Filtrar por estados
		  <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <?php foreach( $estadosMatriculasEstudiantes as $clave => $valor ) {?>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?estadoM=<?=base64_encode($clave)?>&curso=<?=base64_encode($curso);?>&busqueda=<?=$busqueda;?>" <?php if($estadoM==$clave) echo 'style="color: '.$Plataforma->colorUno.';"';?>><?=$valor;?></a>
        <?php }?>
        <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>

      <?php 
          if (!empty($filtro) || !empty($curso) || !empty($estadoM)){
            $quitar='';
            if (!empty($filtro)){ $quitar='?quitar='.base64_encode(1); }
      ?>
          <li class="nav-item"> <a class="nav-link" href="javascript:void(0);" style="color:<?= $Plataforma->colorUno; ?>;">|</a></li>

          <li class="nav-item"> <a class="nav-link" href="<?= $_SERVER['PHP_SELF'].$quitar; ?>" style="color:<?= $Plataforma->colorUno; ?>;">Quitar filtros</a></li>
      <?php } ?>

    </ul>

    <form class="form-inline my-2 my-lg-0" action="estudiantes.php" method="get">
        <input type="hidden" name="curso" value="<?=base64_encode($curso);?>"/>
        <input type="hidden" name="estadoM" value="<?=base64_encode($estadoM);?>"/>
        <input type="hidden" name="quitar" value="<?=base64_encode(0);?>"/>
        <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?=$busqueda;?>">
      <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
    </form>

  </div>
</nav>