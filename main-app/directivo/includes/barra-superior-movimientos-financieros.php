<?php
	$filtro = '';
	$busqueda = '';
	if (!empty($_GET['busqueda'])) {
		$busqueda = $_GET['busqueda'];
		$filtro .= " AND (
			uss_id LIKE '%".$busqueda."%' 
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
			OR fcu_detalle LIKE '%".$busqueda."%' 
			OR fcu_observaciones LIKE '%".$busqueda."%'
			OR fcu_id LIKE '%".$busqueda."%'
		)";
	}
	$usuario = '';
	if (!empty($_GET['usuario'])) {
		$usuario = base64_decode($_GET['usuario']);
		$filtro .= " AND fcu_usuario='".$usuario."'";
	}
	$tipo = '';
	if (!empty($_GET['tipo'])) {
		$tipo = base64_decode($_GET['tipo']);
		$filtro .= " AND fcu_tipo='".$tipo."'";
	}
	$estadoFil = '';
	if (!empty($_GET['estadoFil'])) {
		$estadoFil = base64_decode($_GET['estadoFil']);
		$filtro .= " AND fcu_status='".$estadoFil."'";
	}
	$estadoM = '';
	if (!empty($_GET['estadoM'])) {
		$estadoM = base64_decode($_GET['estadoM']);
		$filtro .= " AND mat_estado_matricula='".$estadoM."'";
	}
	$fecha = '';
	if (!empty($_GET['fecha'])) {
		$fecha = base64_decode($_GET['fecha']);
		$filtro .= " AND fcu_fecha='".$fecha."'";
	}
	$desde='';
	$hasta='';
	if (!empty($_GET["fFecha"]) || (!empty($_GET["desde"]) || !empty($_GET["hasta"]))) {
		$desde=$_GET["desde"];
		$hasta=$_GET["hasta"];
		$filtro .= " AND (fcu_fecha BETWEEN '" . $_GET["desde"] . "' AND '" . $_GET["hasta"] . "' OR fcu_fecha LIKE '%" . $_GET["hasta"] . "%')";
	}

	$estiloResaltadoFV = '';
	if (isset($_GET['tipo']) && $_GET['tipo'] == base64_encode(1)) $estiloResaltadoFV = 'style="color: '.$Plataforma->colorUno.';"';
	$estiloResaltadoFC = '';
	if (isset($_GET['tipo']) && $_GET['tipo'] == base64_encode(2)) $estiloResaltadoFC = 'style="color: '.$Plataforma->colorUno.';"';

	$estiloResaltadoCobrado = '';
	if (isset($_GET['estadoFil']) && $_GET['estadoFil'] == base64_encode(COBRADA)) $estiloResaltadoCobrado = 'style="color: '.$Plataforma->colorUno.';"';
	$estiloResaltadoPorCobrar = '';
	if (isset($_GET['estadoFil']) && $_GET['estadoFil'] == base64_encode(POR_COBRAR)) $estiloResaltadoPorCobrar = 'style="color: '.$Plataforma->colorUno.';"';
	$estiloResaltadoEnProceso = '';
	if (isset($_GET['estadoFil']) && $_GET['estadoFil'] == base64_encode(EN_PROCESO)) $estiloResaltadoEnProceso = 'style="color: '.$Plataforma->colorUno.';"';
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0105'])){?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:<?=$Plataforma->colorUno;?>;">
						Menú movimiento financiero
						<span class="fa fa-angle-down"></span>
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<?php if( Modulos::validarSubRol(['DT0105']) ){?>
						<a class="dropdown-item" href="movimientos-importar.php"><i class="fa fa-upload"></i> Importar saldos</a>
					<?php }?>
					<?php if( Modulos::validarSubRol(['DT0104']) ){?>
						<a class="dropdown-item" href="items.php"><i class="fa fa-list"></i> Administrar Items</a>
					<?php }?>
					<?php if( Modulos::validarSubRol(['DT0104']) && Modulos::validarPermisoEdicion() ){?>
						<a class="dropdown-item" href="facturacion-masiva.php"><i class="fa fa-file-text-o"></i> Facturación Masiva</a>
					<?php }?>
					<?php if( Modulos::validarSubRol(['DT0104']) && Modulos::validarPermisoEdicion() ){?>
						<a class="dropdown-item" href="cuentas-bancarias.php"><i class="fa fa-university"></i> Cuentas Bancarias</a>
					<?php }?>
					<?php if( Modulos::validarSubRol(['DT0240']) ){?>
						<a class="dropdown-item" href="javascript:void(0);" onclick="abrirModal('Informe de Movimientos','informes-movimientos-filtro-modal.php')"><i class="fa fa-file-pdf-o"></i> Informe con Arqueo de Caja</a>
						<a class="dropdown-item" href="javascript:void(0);" onclick="abrirModal('Informe por Cuenta Bancaria','informes-cuentas-bancarias-filtro-modal.php')"><i class="fa fa-university"></i> Informe por Cuenta Bancaria</a>
					<?php }?>
					</div>
				</li>

				<li class="nav-item"> <a class="nav-link" href="javascript:void(0);" style="color:#FFF;">|</a></li>
			<?php }?>

			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#FFF;">
					Filtrar por tipo
					<span class="fa fa-angle-down"></span>
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">	
					<a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?estadoFil=<?= base64_encode($estadoFil); ?>&usuario=<?= base64_encode($usuario); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?=base64_encode(1)?>&busqueda=<?= $busqueda; ?>&estadoM=<?= base64_encode($estadoM); ?>&fecha=<?= base64_encode($fecha); ?>" <?=$estiloResaltadoFV;?>>Fact. Venta</a>
					<a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?estadoFil=<?= base64_encode($estadoFil); ?>&usuario=<?= base64_encode($usuario); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?=base64_encode(2)?>&busqueda=<?= $busqueda; ?>&estadoM=<?= base64_encode($estadoM); ?>&fecha=<?= base64_encode($fecha); ?>" <?=$estiloResaltadoFC;?>>Fact. Compra</a>
					<a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>">Ver todos</a>

				</div>
			</li>

			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#FFF;">
					Filtrar por estado
					<span class="fa fa-angle-down"></span>
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">	
					<a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?estadoFil=<?= base64_encode(POR_COBRAR); ?>&usuario=<?= base64_encode($usuario); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?php if(isset($_GET["tipo"])) echo $_GET["tipo"];?>&busqueda=<?= $busqueda; ?>&estadoM=<?= base64_encode($estadoM); ?>&fecha=<?= base64_encode($fecha); ?>" <?=$estiloResaltadoPorCobrar;?>>Por Cobrar</a>
					<a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?estadoFil=<?= base64_encode(COBRADA); ?>&usuario=<?= base64_encode($usuario); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?php if(isset($_GET["tipo"])) echo $_GET["tipo"];?>&busqueda=<?= $busqueda; ?>&estadoM=<?= base64_encode($estadoM); ?>&fecha=<?= base64_encode($fecha); ?>" <?=$estiloResaltadoCobrado;?>>Cobradas</a>
					<a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>?estadoFil=<?= base64_encode(EN_PROCESO); ?>&usuario=<?= base64_encode($usuario); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?php if(isset($_GET["tipo"])) echo $_GET["tipo"];?>&busqueda=<?= $busqueda; ?>&estadoM=<?= base64_encode($estadoM); ?>&fecha=<?= base64_encode($fecha); ?>" <?=$estiloResaltadoEnProceso;?>>En Proceso</a>
					<a class="dropdown-item" href="<?=$_SERVER['PHP_SELF'];?>">Ver todos</a>

				</div>
			</li>

			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#FFF;">
					Filtrar por Fecha
					<span class="fa fa-angle-down"></span>
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					
					<form class="dropdown-item" method="get" action="<?= $_SERVER['PHP_SELF']; ?>">
						<input type="hidden" name="tipo" value="<?= base64_encode($tipo); ?>"/>
						<input type="hidden" name="busqueda" value="<?= $busqueda; ?>"/>
						<input type="hidden" name="usuario" value="<?= base64_encode($usuario); ?>"/>
						<input type="hidden" name="estadoM" value="<?= base64_encode($estadoM); ?>"/>
						<input type="hidden" name="fecha" value="<?= base64_encode($fecha); ?>"/>
						<input type="hidden" name="estadoFil" value="<?= base64_encode($estadoFil); ?>"/>
						<label>Fecha Desde:</label>
						<input type="date" class="form-control" placeholder="desde"  name="desde" value="<?= $desde; ?>"/>

						<label>Hasta</label>
						<input type="date" class="form-control" placeholder="hasta"  name="hasta" value="<?= $hasta; ?>"/>
						
						<input type="submit" class="btn deepPink-bgcolor" name="fFecha" value="Filtrar" style="margin: 5px;">
					</form>
					<a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>" style="font-weight: bold; text-align: center;">VER TODO</a>
				</div>
			</li>

			<?php if (!empty($filtro)) { ?>
				<li class="nav-item"> <a class="nav-link" href="javascript:void(0);" style="color:<?= $Plataforma->colorUno; ?>;">|</a></li>

				<li class="nav-item"> <a class="nav-link" href="<?= $_SERVER['PHP_SELF']; ?>" style="color:<?= $Plataforma->colorUno; ?>;">Quitar filtros</a></li>
			<?php } ?>
		</ul> 

		<form class="form-inline my-2 my-lg-0" action="<?= $_SERVER['PHP_SELF']; ?>" method="get">
			<input type="hidden" name="usuario" value="<?= base64_encode($usuario); ?>" />
			<input type="hidden" name="tipo" value="<?= base64_encode($tipo); ?>" />
			<input type="hidden" name="estadoM" value="<?= base64_encode($estadoM); ?>" />
			<input type="hidden" name="fecha" value="<?= base64_encode($fecha); ?>" />
			<input type="hidden" name="estadoFil" value="<?= base64_encode($estadoFil); ?>"/>
			<input type="hidden" name="desde" value="<?= $desde; ?>"/>
			<input type="hidden" name="hasta" value="<?= $hasta; ?>"/>
			<input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?= $busqueda; ?>">
			<button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
		</form>
	</div>
</nav>