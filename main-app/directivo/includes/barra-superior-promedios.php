
<?php
    $filtro = '';
    $curso = '';
    if(isset($_GET["curso"]) && is_numeric(base64_decode($_GET["curso"]))){
        $curso = base64_decode($_GET["curso"]);
        $filtro .= " AND mat_grado='".$curso."'";
    }
    $grupo = '';
    if(isset($_GET["grupo"]) && is_numeric(base64_decode($_GET["grupo"]))){
        $grupo = base64_decode($_GET["grupo"]);
        $filtro .= " AND mat_grupo='".$grupo."'";
    }
    
    $filtroBoletin = '';
    $periodo = '';
    if(isset($_GET["periodo"]) && is_numeric(base64_decode($_GET["periodo"]))){
        $periodo = base64_decode($_GET["periodo"]);
        $filtroBoletin .= " AND bol_periodo='".$periodo."'";
    }
    $carga = '';
    if(isset($_GET["carga"]) && is_numeric(base64_decode($_GET["carga"]))){
        $carga = base64_decode($_GET["carga"]);
        $filtroBoletin .= " AND bol_carga='".$carga."'";
    }
    
    $filtroLimite = '';
    $cantidad = '';
    if(isset($_GET["cantidad"]) && is_numeric(base64_decode($_GET["cantidad"]))){
        $cantidad = base64_decode($_GET["cantidad"]);
        $filtroLimite = "LIMIT 0,".$cantidad;
    }
    
    $filtroOrden ='DESC';
    if(!empty($_GET["orden"])){
        $filtroOrden = base64_decode($_GET["orden"]);
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
        while($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)){
            $estiloResaltado = '';
            if($grado['gra_id'] == $curso) $estiloResaltado = 'style="color: '.$Plataforma->colorUno.';"';
        ?>	
            <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=base64_encode($grado['gra_id']);?>&grupo=<?=base64_encode($grupo);?>&periodo=<?=base64_encode($periodo);?>&cantidad=<?=base64_encode($cantidad);?>&orden=<?=base64_encode($filtroOrden);?>" <?=$estiloResaltado;?>><?=$grado['gra_nombre'];?></a>
        <?php }?>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
            Filtrar por grupo
		  <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <?php
        $grupos = Grupos::listarGrupos();
        while($grupoJson = mysqli_fetch_array($grupos, MYSQLI_BOTH)){
            $estiloResaltado = '';
            if($grupoJson['gru_id'] == $grupo) $estiloResaltado = 'style="color: '.$Plataforma->colorUno.';"';
        ?>	
            <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=base64_encode($grupoJson['gru_id']);?>&curso=<?=base64_encode($curso);?>&periodo=<?=base64_encode($periodo);?>&cantidad=<?=base64_encode($cantidad);?>&orden=<?=base64_encode($filtroOrden);?>" <?=$estiloResaltado;?>><?=$grupoJson['gru_nombre'];?></a>
        <?php }?>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
            Filtrar por periodos
		  <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <?php
        $p = 1;
        while($p <= $config['conf_periodos_maximos']){
            $estiloResaltado = '';
            if($p == $periodo) $estiloResaltado = 'style="color: '.$Plataforma->colorUno.';"';
        ?>	
            <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?periodo=<?=base64_encode($p);?>&grupo=<?=base64_encode($grupo);?>&curso=<?=base64_encode($curso);?>&cantidad=<?=base64_encode($cantidad);?>&orden=<?=base64_encode($filtroOrden);?>" <?=$estiloResaltado;?>>Periodo <?=$p;?></a>
        <?php $p++; }?>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>" style="font-weight: bold; text-align: center;">VER TODO</a>
        </div>
      </li>


      
      <li class="nav-item"> <a class="nav-link" href="javascript:void(0);">|</a></li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
        Cantidades
		  <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">	
        <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?cantidad=<?=base64_encode(10)?>&periodo=<?=base64_encode($periodo);?>&grupo=<?=base64_encode($grupo);?>&curso=<?=base64_encode($curso);?>&orden=<?=base64_encode($filtroOrden);?>" <?=$estiloResaltado;?>>10 estudiantes</a>
        <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?cantidad=<?=base64_encode(20)?>&periodo=<?=base64_encode($periodo);?>&grupo=<?=base64_encode($grupo);?>&curso=<?=base64_encode($curso);?>&orden=<?=base64_encode($filtroOrden);?>" <?=$estiloResaltado;?>>20 estudiantes</a>
        <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?cantidad=<?=base64_encode(30)?>&periodo=<?=base64_encode($periodo);?>&grupo=<?=base64_encode($grupo);?>&curso=<?=base64_encode($curso);?>&orden=<?=base64_encode($filtroOrden);?>" <?=$estiloResaltado;?>>30 estudiantes</a>
        <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?cantidad=<?=base64_encode(40)?>&periodo=<?=base64_encode($periodo);?>&grupo=<?=base64_encode($grupo);?>&curso=<?=base64_encode($curso);?>&orden=<?=base64_encode($filtroOrden);?>" <?=$estiloResaltado;?>>40 estudiantes</a>
        <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?cantidad=<?=base64_encode(50)?>&periodo=<?=base64_encode($periodo);?>&grupo=<?=base64_encode($grupo);?>&curso=<?=base64_encode($curso);?>&orden=<?=base64_encode($filtroOrden);?>" <?=$estiloResaltado;?>>50 estudiantes</a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
        Orden
		  <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">	
        <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?orden=<?=base64_encode('DESC')?>&periodo=<?=base64_encode($periodo);?>&grupo=<?=base64_encode($grupo);?>&curso=<?=base64_encode($curso);?>&cantidad=<?=base64_encode($cantidad);?>" <?=$estiloResaltado;?>>De mayor a menor</a>
        <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?orden=<?=base64_encode('ASC')?>&periodo=<?=base64_encode($periodo);?>&grupo=<?=base64_encode($grupo);?>&curso=<?=base64_encode($curso);?>&cantidad=<?=base64_encode($cantidad);?>" <?=$estiloResaltado;?>>De menor a mayor</a>
        </div>
      </li>

        <?php if (!empty($filtro) || !empty($filtroBoletin) || !empty($filtroLimite)) { ?>
            <li class="nav-item"> <a class="nav-link" href="javascript:void(0);" style="color:<?= $Plataforma->colorUno; ?>;">|</a></li>

            <li class="nav-item"> <a class="nav-link" href="<?= $_SERVER['PHP_SELF']; ?>" style="color:<?= $Plataforma->colorUno; ?>;">Quitar filtros</a></li>
        <?php } ?>

    </ul>

  </div>
</nav>