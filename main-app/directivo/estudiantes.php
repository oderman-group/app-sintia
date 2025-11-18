<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0001';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once("../class/Estudiantes.php");
require_once("../class/servicios/GradoServicios.php"); 
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");

// Validar solo parámetros GET que no sean arrays (para evitar error con filtros)
$parametrosSimples = array_filter($_GET, function($value) {
    return !is_array($value);
});
if(!empty($parametrosSimples)){
    Utilidades::validarParametros($parametrosSimples);
}

if (isset($_GET['mode']) && $_GET['mode'] === 'DEV') {
	$redis = RedisInstance::getRedisInstance();

	$arrayTest = [
		[
			'Nombre' => 'Jhon',
			'Edad'   => 33,
			'Genero' => 'M'
		],
		[
			'Nombre' => 'Michelle',
			'Edad'   => 24,
			'Genero' => 'F'
		],
	];

	$redis->set('jhonky', json_encode($arrayTest));
	//echo $redis->ttl('jhonky'); exit();
	print_r(json_decode($redis->get('jhonky'), true));
	echo "<hr>";
	
	$redis->lPush("estudiantes", "Jhon");
	$redis->lPush("estudiantes", "Cristal");
	$redis->lPush("estudiantes", "Michelle");

	$estudiantes = $redis->lRange("estudiantes", 0, 2);
	
	foreach($estudiantes as $valor) {
		echo $valor."<br>";
	}

	exit();
}

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

//$redis = RedisInstance::getRedisInstance();

$jQueryTable = '';
if($config['conf_doble_buscador'] == 1) {
	$jQueryTable = 'id="example1"';
}
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css"/>
	<!-- select2 -->
	<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
	<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
	
	<style>
		/* ========================================
		   ESTILOS RESPONSIVOS PARA TABLA DE ESTUDIANTES
		   ======================================== */
		
		/* Z-index para dropdowns - solo aumentar el z-index sin cambiar el posicionamiento */
		.dropdown-menu {
			z-index: 10000 !important;
			max-height: 400px;
			overflow-y: auto;
			overflow-x: hidden;
		}
		
		/* Mejorar el scroll del dropdown */
		.dropdown-menu::-webkit-scrollbar {
			width: 6px;
		}
		
		.dropdown-menu::-webkit-scrollbar-track {
			background: #f1f1f1;
			border-radius: 10px;
		}
		
		.dropdown-menu::-webkit-scrollbar-thumb {
			background: #888;
			border-radius: 10px;
		}
		
		.dropdown-menu::-webkit-scrollbar-thumb:hover {
			background: #555;
		}
		
		/* Permitir que el dropdown se muestre correctamente */
		table.dataTable tbody td:last-child {
			overflow: visible;
		}
		
		/* ========================================
		   BOTÓN DE ACCIONES CON TRES PUNTOS
		   ======================================== */
		
		/* Botón de tres puntos verticales */
		.btn-acciones-menu {
			background: transparent;
			border: none;
			padding: 4px 8px;
			cursor: pointer;
			border-radius: 4px;
			transition: all 0.2s ease;
			font-size: 18px;
			color: #666;
		}
		
		.btn-acciones-menu:hover {
			background: #f5f5f5;
			color: #333;
		}
		
		.btn-acciones-menu:active {
			background: #e0e0e0;
		}
		
		/* Panel flotante de acciones (estilo minimalista vertical) */
		.acciones-panel {
			display: none;
			position: fixed;
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 4px 20px rgba(0,0,0,0.15);
			border: 1px solid #e0e0e0;
			padding: 8px 0;
			min-width: 240px;
			max-width: 280px;
			max-height: 400px;
			overflow-y: auto;
			z-index: 10000;
			animation: slideIn 0.15s ease-out;
		}
		
		@keyframes slideIn {
			from {
				opacity: 0;
				transform: scale(0.95) translateY(-10px);
			}
			to {
				opacity: 1;
				transform: scale(1) translateY(0);
			}
		}
		
		.acciones-panel.show {
			display: block;
		}
		
		/* Lista vertical de opciones */
		.acciones-list {
			list-style: none;
			padding: 0;
			margin: 0;
		}
		
		/* Item de acción individual (estilo lista) */
		.accion-item {
			display: flex;
			align-items: center;
			padding: 12px 16px;
			cursor: pointer;
			transition: all 0.15s ease;
			text-decoration: none;
			color: #333;
			border-left: 3px solid transparent;
		}
		
		.accion-item:hover {
			background: #f8f9fa;
			border-left-color: #667eea;
			text-decoration: none;
			color: #333;
		}
		
		.accion-item:active {
			background: #e9ecef;
		}
		
		.accion-icon {
			width: 32px;
			height: 32px;
			border-radius: 6px;
			display: flex;
			align-items: center;
			justify-content: center;
			margin-right: 12px;
			font-size: 14px;
			color: #fff;
			flex-shrink: 0;
		}
		
		.accion-name {
			font-size: 14px;
			color: #333;
			font-weight: 400;
			line-height: 1.4;
			flex: 1;
		}
		
		.accion-item:hover .accion-name {
			font-weight: 500;
		}
		
		/* Separador sutil entre grupos de acciones */
		.accion-separator {
			height: 1px;
			background: #e9ecef;
			margin: 4px 0;
		}
		
		/* Primera y última opción con bordes redondeados */
		.accion-item:first-child {
			border-top-left-radius: 8px;
			border-top-right-radius: 8px;
		}
		
		.accion-item:last-child {
			border-bottom-left-radius: 8px;
			border-bottom-right-radius: 8px;
		}
		
		/* Overlay para cerrar el panel */
		.acciones-overlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 9999;
		}
		
		.acciones-overlay.show {
			display: block;
		}
		
		/* Scrollbar personalizado para el panel */
		.acciones-panel::-webkit-scrollbar {
			width: 6px;
		}
		
		.acciones-panel::-webkit-scrollbar-track {
			background: #f1f1f1;
			border-radius: 10px;
		}
		
		.acciones-panel::-webkit-scrollbar-thumb {
			background: #888;
			border-radius: 10px;
		}
		
		.acciones-panel::-webkit-scrollbar-thumb:hover {
			background: #555;
		}
		
		/* Contenedor responsivo con scroll horizontal */
		.table-responsive-estudiantes {
			width: 100%;
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
			margin-bottom: 15px;
		}
		
		/* Tabla con encabezados fijos */
		.table-estudiantes-wrapper {
			position: relative;
			max-height: calc(100vh - 350px);
			overflow-y: auto;
			overflow-x: auto;
			border: 1px solid #dee2e6;
			border-radius: 4px;
		}
		
		.table-estudiantes-wrapper table {
			margin-bottom: 0;
		}
		
		/* Encabezados fijos */
		.table-estudiantes-wrapper thead th {
			position: sticky;
			top: 0;
			z-index: 10;
			background-color: #f8f9fa;
			border-bottom: 2px solid #dee2e6;
			box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
		}
		
		/* Ajustes para DataTables con encabezados fijos */
		.dataTables_wrapper .table-estudiantes-wrapper {
			clear: both;
		}
		
		/* Responsividad para botones de acciones */
		.btn-group {
			display: inline-flex;
			flex-wrap: wrap;
			gap: 5px;
		}
		
		.btn-group .btn {
			flex: 0 0 auto;
			margin-bottom: 5px;
		}
		
		/* Contenedor de barra de herramientas */
		.d-flex.justify-content-between.align-items-center {
			flex-wrap: wrap;
			gap: 10px;
		}
		
		.d-flex.justify-content-between.align-items-center > .btn-group {
			flex: 0 1 auto;
		}
		
		/* Estilos para filas seleccionadas - Edición Masiva de Estudiantes */
		.estudiante-row-selected {
			background-color: #e3f2fd !important;
			transition: background-color 0.3s ease;
		}
		
		.estudiante-row-selected:hover {
			background-color: #bbdefb !important;
		}
		
		/* ========================================
		   RESPONSIVIDAD MÓVIL
		   ======================================== */
		
		/* Pantallas grandes - mantener botones en línea */
		@media (min-width: 992px) {
			.d-flex.justify-content-between.align-items-center {
				flex-wrap: nowrap;
			}
			
			.btn-group {
				flex-wrap: nowrap;
			}
		}
		
		/* Tablets y menores */
		@media (max-width: 991px) {
			.page-title-breadcrumb {
				flex-direction: column;
				align-items: flex-start !important;
			}
			
			.btn-group {
				margin-bottom: 10px;
			}
			
			.table-estudiantes-wrapper {
				max-height: calc(100vh - 400px);
			}
			
			/* Hacer que el contenedor principal de botones sea más flexible */
			.d-flex.justify-content-between.align-items-center {
				flex-direction: column;
				align-items: flex-start !important;
			}
			
			.d-flex.justify-content-between.align-items-center > * {
				width: 100%;
				margin-bottom: 10px;
			}
			
			.d-flex.justify-content-between.align-items-center > .btn-group {
				justify-content: flex-start;
			}
		}
		
		/* Móviles */
		@media (max-width: 767px) {
			/* Hacer botones más pequeños en móvil */
			.btn-group .btn {
				padding: 8px 12px;
				font-size: 13px;
			}
			
			/* Permitir que los botones dentro de btn-group se envuelvan */
			.btn-group {
				display: flex;
				flex-wrap: wrap;
				gap: 5px;
			}
			
			/* Botones dropdown más compactos */
			.btn-group .dropdown-toggle {
				padding: 8px 10px;
			}
			
			.btn-group .dropdown-toggle .caret {
				margin-left: 5px;
			}
			
			/* Ajustar encabezados de tabla */
			.table-estudiantes-wrapper thead th {
				padding: 8px 5px;
				font-size: 12px;
				white-space: nowrap;
			}
			
			.table-estudiantes-wrapper tbody td {
				padding: 8px 5px;
				font-size: 13px;
			}
			
			/* Ajustar altura máxima en móvil */
			.table-estudiantes-wrapper {
				max-height: calc(100vh - 450px);
			}
			
			/* Hacer que los filtros sean apilables */
			.card-filtros .form-group {
				margin-bottom: 15px;
			}
		}
		
		/* Móviles pequeños */
		@media (max-width: 575px) {
			.table-estudiantes-wrapper thead th {
				padding: 6px 3px;
				font-size: 11px;
			}
			
			.table-estudiantes-wrapper tbody td {
				padding: 6px 3px;
				font-size: 12px;
			}
			
			/* Hacer checkboxes más pequeños en móvil */
			.table-estudiantes-wrapper input[type="checkbox"] {
				transform: scale(0.9);
			}
			
			/* Botones del dropdown más compactos */
			.dropdown-menu {
				font-size: 13px;
			}
		}
		
		/* ========================================
		   MEJORAS DE UX
		   ======================================== */
		
		/* Indicador visual de scroll disponible */
		.table-estudiantes-wrapper::after {
			content: '';
			position: sticky;
			right: 0;
			top: 0;
			height: 100%;
			width: 20px;
			background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
			pointer-events: none;
			z-index: 5;
		}
		
		/* Scroll suave */
		.table-estudiantes-wrapper {
			scroll-behavior: smooth;
		}
		
		/* Hover mejorado para filas */
		.table-estudiantes-wrapper tbody tr:hover {
			background-color: #f5f5f5;
			transition: background-color 0.2s ease;
		}
		
		/* Mantener el color de selección sobre el hover */
		.table-estudiantes-wrapper tbody tr.estudiante-row-selected:hover {
			background-color: #bbdefb !important;
		}
		
		/* ========================================
		   ESTILOS PARA MODAL DE EDICIÓN MASIVA
		   ======================================== */
		
		/* Estilos mejorados para el modal de edición masiva */
		#editarMasivoEstudiantesModal .form-group {
			margin-bottom: 20px;
		}
		
		#editarMasivoEstudiantesModal .form-group label {
			font-weight: 600;
			font-size: 14px;
			margin-bottom: 8px;
			color: #495057;
			display: block;
		}
		
		#editarMasivoEstudiantesModal .form-control,
		#editarMasivoEstudiantesModal .select2-container {
			width: 100% !important;
			min-height: 38px;
		}
		
		#editarMasivoEstudiantesModal .select2-container .select2-selection--single {
			height: 38px !important;
			padding: 6px 12px;
			border: 1px solid #ced4da;
			border-radius: 4px;
		}
		
		#editarMasivoEstudiantesModal .select2-container--default .select2-selection--single .select2-selection__rendered {
			line-height: 26px;
			color: #495057;
		}
		
		#editarMasivoEstudiantesModal .select2-container--default .select2-selection--single .select2-selection__arrow {
			height: 36px;
		}
		
		#editarMasivoEstudiantesModal input[type="number"] {
			height: 38px;
			padding: 6px 12px;
			font-size: 14px;
		}
		
		#editarMasivoEstudiantesModal .row {
			margin-bottom: 10px;
		}
		
		#editarMasivoEstudiantesModal hr {
			margin: 25px 0;
			border-top: 2px solid #dee2e6;
		}
		
		#editarMasivoEstudiantesModal h5 {
			margin-bottom: 15px;
			font-size: 16px;
			font-weight: 600;
		}
		
		/* ========================================
		   RESPONSIVIDAD PARA MODALES
		   ======================================== */
		
		/* Hacer modales responsivos */
		@media (max-width: 767px) {
			.modal-dialog {
				margin: 10px;
				max-width: calc(100% - 20px);
			}
			
			.modal-lg {
				max-width: calc(100% - 20px);
			}
			
			#editarMasivoEstudiantesModal .form-group label {
				font-size: 13px;
			}
			
			#editarMasivoEstudiantesModal .form-control,
			#editarMasivoEstudiantesModal .select2-container {
				min-height: 36px;
			}
			
			#editarMasivoEstudiantesModal .alert {
				font-size: 13px;
				padding: 10px;
			}
		}
		
		/* ========================================
		   MEJORAS ADICIONALES DE RESPONSIVIDAD
		   ======================================== */
		
		/* Card de filtros responsivo */
		@media (max-width: 767px) {
			.card-filtros .col-md-4,
			.card-filtros .col-md-3 {
				width: 100%;
				padding: 0 15px;
			}
			
			.card-filtros .row {
				margin: 0;
			}
		}
		
		/* Barra de herramientas responsiva */
		@media (max-width: 767px) {
			.btn-toolbar {
				flex-direction: column;
			}
			
			.btn-toolbar .btn-group {
				width: 100%;
				margin-bottom: 10px;
			}
		}
		
		/* Ajuste de paginación en móvil */
		@media (max-width: 575px) {
			.pagination {
				font-size: 12px;
			}
			
			.pagination .page-link {
				padding: 5px 10px;
			}
		}
		
		/* ========================================
		   ESTILOS PARA BOTONES DESHABILITADOS
		   ======================================== */
		
		/* Botones deshabilitados con cursor not-allowed */
		button[disabled], button:disabled {
			cursor: not-allowed !important;
			opacity: 0.6;
		}
		
		/* Tooltip mejorado para botones deshabilitados */
		button[disabled]:hover, button:disabled:hover {
			opacity: 0.6;
		}
	</style>
</head>
<!-- END HEAD -->
<?php
	include("../compartido/body.php");
	include("usuarios-bloquear-modal.php");
?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php"); //1 por otimizar, parece estar repetida ?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <!-- Token CSRF para operaciones de eliminación -->
                    <?php echo Csrf::campoHTML(); ?>
                    
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[209][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php"); //1 por otimizar, parece estar repetida ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descripción de la página -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p class="text-muted" style="font-size: 14px; line-height: 1.6;">
                                <i class="fa fa-info-circle text-info"></i> 
                                <?=__('estudiantes.descripcion_pagina');?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">
								<?php include("../../config-general/mensajes-informativos.php"); ?>
								<span id="respuestaCambiarEstado"></span>

								<?php 
								//include("includes/barra-superior-matriculas.php");	
								// $matKeys = array_slice($keys, $inicio, $registros);
								// foreach ($matKeys as $matKey){
								// 	$matData = $redis->get($matKey);
								// 	$resultado = json_decode($matData, true);
								// }
								// print_r($resultado); exit();
								?>
								
								<?php
								 $filtro="";
								 
								 // Incluir archivo para obtener el número de registros ANTES de usarlo en los botones
								 $cursoActual = '';
								 include("includes/consulta-paginacion-estudiantes.php");
								 
								 // Verificar si hay cursos/grados creados
								 $cursosConsulta = Grados::listarGrados(1);
								 $numCursosCreados = mysqli_num_rows($cursosConsulta);
								?>

									<?php
									if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_API_SION_ACADEMICA)) {
										if (isset($_GET['msgsion'])) {
											$aler = 'alert-danger';
											$mensajeSion = 'Por favor, verifique todos los datos del estudiante y llene los campos vacios.';

											if($_GET['msgsion']!=''){
												$aler = 'alert-success';
												$mensajeSion = base64_decode($_GET['msgsion']);

												if (base64_decode($_GET['stadsion']) != true) {
													$aler='alert-danger';
												}
											}
									?>
										<div class="alert alert-block <?=$aler;?>">
											<button type="button" class="close" data-dismiss="alert">×</button>
											<h4 class="alert-heading">SION!</h4>
											<p><?=$mensajeSion;?></p>
										</div>
									<?php 
										}
									}
									if (isset($_GET['msgsintia'])) {
										$aler='alert-success';

										if ($_GET['stadsintia']!=true) {
											$aler='alert-danger';
										}
									?>
									<div class="alert alert-block <?=$aler;?>">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<h4 class="alert-heading">SINTIA!</h4>
										<p><?=$_GET['msgsintia'];?></p>
									</div>
									<?php }?>

									<!-- Advertencia si no hay cursos creados -->
									<?php if($numCursosCreados == 0){ ?>
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
										<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> No hay cursos/grados creados</h4>
										<p class="mb-2">
											<strong>Atención:</strong> Antes de agregar estudiantes, debes crear al menos un curso/grado en el sistema.
										</p>
										<hr>
										<p class="mb-0">
											<i class="fa fa-info-circle"></i> 
											Para crear cursos, dirígete a: 
											<a href="cursos.php" class="alert-link font-weight-bold">
												<i class="fa fa-graduation-cap"></i> Gestión de Cursos/Grados
											</a>
										</p>
									</div>
									<?php } ?>

									<!-- Barra de herramientas superior -->
									<div class="row mb-3">
										<div class="col-sm-12">
											<div class="d-flex justify-content-between align-items-center">
												<!-- Botones principales -->
												<div class="btn-group">
													<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0084'])){?>
														<?php if($numCursosCreados > 0){ ?>
															<a href="estudiantes-agregar.php" class="btn deepPink-bgcolor">
																<i class="fa fa-plus"></i> Agregar Estudiante
															</a>
														<?php } else { ?>
															<button type="button" class="btn deepPink-bgcolor" disabled title="Debes crear al menos un curso antes de agregar estudiantes">
																<i class="fa fa-plus"></i> Agregar Estudiante
															</button>
														<?php } ?>
													<?php }?>
													
													<?php if(Modulos::validarSubRol(['DT0077'])){?>
														<?php if($numCursosCreados > 0){ ?>
															<a href="estudiantes-importar-excel.php" class="btn btn-success">
																<i class="fa fa-file-excel"></i> Importar desde Excel
															</a>
														<?php } else { ?>
															<button type="button" class="btn btn-success" disabled title="Debes crear al menos un curso antes de importar estudiantes">
																<i class="fa fa-file-excel"></i> Importar desde Excel
															</button>
														<?php } ?>
													<?php }?>
													
													<?php if($numRegistros > 0){ ?>
														<!-- Botón de Edición Masiva - Solo visible cuando hay selección -->
														<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0078'])){?>
															<button type="button" id="editarMasivoEstudiantesBtn" class="btn btn-warning" style="display:none;">
																<i class="fa fa-edit"></i> Editar Seleccionados
															</button>
														<?php }?>
														
														<!-- Menú Más Opciones (incluye Promedios) -->
														<?php if(Modulos::validarSubRol(['DT0002', 'DT0080', 'DT0075'])){?>
															<div class="btn-group" role="group">
																<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
																	<i class="fa fa-list"></i> Más Opciones <span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<?php if(Modulos::validarSubRol(['DT0002'])){?>
																		<li><a href="estudiantes-promedios.php"><i class="fa fa-chart-line"></i> Promedios</a></li>
																		<li class="divider"></li>
																	<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0080'])){?>
																		<li><a href="estudiantes-consolidado-final.php"><i class="fa fa-file-alt"></i> Consolidado Final</a></li>
																	<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0075'])){?>
																		<li><a href="estudiantes-nivelaciones.php"><i class="fa fa-balance-scale"></i> Nivelaciones</a></li>
																	<?php }?>
																</ul>
															</div>
														<?php }?>
														
														<!-- Acciones Masivas -->
														<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0212', 'DT0213', 'DT0214', 'DT0215', 'DT0175', 'DT0216', 'DT0149'])){?>
															<div class="btn-group" role="group">
																<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
																	<i class="fa fa-tools"></i> Acciones Masivas <span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<?php if(Modulos::validarSubRol(['DT0212'])){?>
																		<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-matricular-todos.php')"><i class="fa fa-check-circle"></i> Matricular a Todos</a></li>
																	<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0213'])){?>
																		<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-matriculas-cancelar.php')"><i class="fa fa-times-circle"></i> Cancelar a Todos</a></li>
																	<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0214'])){?>
																		<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-grupoa-todos.php')"><i class="fa fa-users"></i> Asignar a Todos al Grupo A</a></li>
																		<li class="divider"></li>
																	<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0215'])){?>
																		<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Esta opción removerá a todos lo estudiantes que no estén en estado Matriculado, desea continuar?','question','estudiantes-inactivos-remover.php')"><i class="fa fa-trash"></i> Remover Estudiantes Inactivos</a></li>
																		<li class="divider"></li>
																	<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0175'])){?>
																		<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-documento-usuario-actualizar.php')"><i class="fa fa-id-card"></i> Documento como Usuario</a></li>
																	<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0216'])){?>
																		<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-crear-usuarios.php')"><i class="fa fa-key"></i> Generar Credenciales</a></li>
																	<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0149'])){?>
																		<li><a href="filtro-general-folio.php"><i class="fa fa-file-pdf"></i> Generar Folios</a></li>
																	<?php }?>
																</ul>
															</div>
														<?php }?>
													<?php } ?>
												</div>
												
												<?php if($numRegistros > 0){ ?>
													<!-- Botones del lado derecho -->
													<div class="d-flex align-items-center" style="gap: 8px;">
														<!-- Botón de exportar a Excel -->
														<button type="button" class="btn btn-success" id="btnExportarExcel">
															<i class="fa fa-file-excel"></i> Exportar a Excel
														</button>
														
														<!-- Botón de filtros -->
														<button type="button" class="btn btn-outline-secondary" id="btnToggleFiltros">
															<i class="fa fa-filter"></i> Filtros y Búsqueda
														</button>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>
											
											<!-- Filtros Mejorados con Multiselect (Colapsable) -->
											<div class="card card-topline-purple mb-3" id="cardFiltros" style="display: none;">
												<div class="card-body">
													<h5 class="mb-3"><i class="fa fa-filter"></i> Filtros y Búsqueda Avanzada</h5>
													
													<!-- Buscador General Potente -->
													<div class="row mb-3">
														<div class="col-md-12">
															<div class="form-group">
																<label><i class="fa fa-search"></i> Buscar Estudiante</label>
																<div class="input-group">
																	<input type="text" id="filtro_busqueda" class="form-control" placeholder="Buscar por nombre completo, apellidos, documento, email o usuario...">
																	<div class="input-group-append">
																		<button class="btn btn-primary" type="button" id="btnBuscar">
																			<i class="fa fa-search"></i> Buscar
																		</button>
																	</div>
																</div>
																<small class="form-text text-muted">
																	<i class="fa fa-info-circle"></i> <strong>Búsqueda potente:</strong> Escribe cualquier combinación de nombres y apellidos, documento, email o usuario. 
																	Ejemplo: "Juan Pérez", "María", "12345678", etc. Presiona Enter o haz clic en "Buscar".
																</small>
															</div>
														</div>
													</div>
													
													<hr>
													
													<!-- Filtros Multiselect -->
													<div class="row">
														<div class="col-md-4">
															<div class="form-group">
																<label><i class="fa fa-graduation-cap"></i> Cursos</label>
																<select id="filtro_cursos" class="form-control select2-multiple" multiple="multiple" style="width: 100%;">
																	<?php
																	$grados = Grados::listarGrados(1);
																	while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
																	?>
																		<option value="<?=$grado['gra_id'];?>"><?=$grado['gra_nombre'];?></option>
																	<?php }?>
																</select>
															</div>
														</div>
														
														<div class="col-md-4">
															<div class="form-group">
																<label><i class="fa fa-users"></i> Grupos</label>
																<select id="filtro_grupos" class="form-control select2-multiple" multiple="multiple" style="width: 100%;">
																	<?php
																	$grupos = Grupos::listarGrupos();
																	while ($gru = mysqli_fetch_array($grupos, MYSQLI_BOTH)) {
																	?>
																		<option value="<?=$gru['gru_id'];?>"><?=$gru['gru_nombre'];?></option>
																	<?php }?>
																</select>
															</div>
														</div>
														
														<div class="col-md-4">
															<div class="form-group">
																<label><i class="fa fa-info-circle"></i> Estados</label>
																<select id="filtro_estados" class="form-control select2-multiple" multiple="multiple" style="width: 100%;">
																	<?php
																	foreach ($estadosMatriculasEstudiantes as $clave => $valor) {
																	?>
																		<option value="<?=$clave;?>"><?=$valor;?></option>
																	<?php }?>
																</select>
															</div>
														</div>
													</div>
													
													<!-- Filtros por Fecha de Matrícula -->
													<div class="row mt-3">
														<div class="col-md-6">
															<div class="form-group">
																<label><i class="fa fa-calendar"></i> Fecha de Matrícula (Desde)</label>
																<input type="date" id="filtro_fecha_desde" class="form-control" max="<?= date('Y-m-d'); ?>" placeholder="Fecha desde">
																<small class="form-text text-muted">
																	<i class="fa fa-info-circle"></i> Filtra estudiantes matriculados desde esta fecha
																</small>
															</div>
														</div>
														
														<div class="col-md-6">
															<div class="form-group">
																<label><i class="fa fa-calendar"></i> Fecha de Matrícula (Hasta)</label>
																<input type="date" id="filtro_fecha_hasta" class="form-control" max="<?= date('Y-m-d'); ?>" placeholder="Fecha hasta">
																<small class="form-text text-muted">
																	<i class="fa fa-info-circle"></i> Filtra estudiantes matriculados hasta esta fecha (no puede ser mayor a hoy)
																</small>
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="col-md-12 text-right">
															<button type="button" class="btn btn-secondary" id="btnLimpiarFiltros">
																<i class="fa fa-eraser"></i> Limpiar Todo
															</button>
														</div>
													</div>
												</div>
											</div>
											
                                    <div class="card card-topline-purple">
                                        <div class="card-body">
											
											
											
                                        <div>
											<!-- Contenedor responsivo con encabezados fijos -->
											<div class="table-estudiantes-wrapper">
												<div id="gifCarga" class="gif-carga">
													<img  alt="Cargando...">
												</div>
                                    			<table <?=$jQueryTable;?> class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th><input type="checkbox" id="selectAllEstudiantes" title="Seleccionar todos"></th>
                                                        <th>ID</th>
              <th>Bloq.</th>
              <th><?=$frases[246][$datosUsuarioActual['uss_idioma']];?></th>
              <th><?=$frases[241][$datosUsuarioActual['uss_idioma']];?></th>
              <th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
              <th><?=$frases[26][$datosUsuarioActual['uss_idioma']];?></th>
              <th>Usuario</th>
              <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="matriculas_result">
													<?php
													// Variables ya inicializadas en la línea 676-677
													// include("includes/consulta-paginacion-estudiantes.php"); - Ya no es necesario, se incluye arriba
													$filtroLimite = 'LIMIT '.$inicio.','.$registros;
													
													$selectSql = [
														"mat.mat_id","mat.id_nuevo AS mat_id_nuevo","mat.mat_documento","mat.mat_compromiso",
														"mat.mat_estado_matricula","mat.mat_id_usuario","mat.mat_grado","mat.mat_grupo",
														"mat.mat_inclusion","mat.mat_foto","mat.mat_acudiente",
														"mat.mat_matricula","mat.mat_codigo_tesoreria","mat.mat_folio",
														"mat.mat_valor_matricula","mat.mat_fecha",
														"mat.mat_nombres","mat.mat_nombre2","mat.mat_primer_apellido","mat.mat_segundo_apellido",
														"uss.uss_id","uss.uss_usuario","uss.uss_bloqueado",
														"gra.gra_nombre","gra.gra_formato_boletin",
														"gru.gru_nombre"
													];

													$consulta = Estudiantes::listarEstudiantes(0, $filtro, $filtroLimite, $cursoActual, null, $selectSql);
													
													$contReg = 1;

													$index = 0;
													$arraysDatos = array();
													if (!empty($consulta)) {
														while ($fila = $consulta->fetch_assoc()) {
															$arraysDatos[$index] = $fila;
															$index++;
														}
														$consulta->free();
													}
													$lista = $arraysDatos;
													$data["data"] =$lista;
													include(ROOT_PATH . "/main-app/class/componentes/result/matriculas-tbody.php");
													  ?>
                                                </tbody>
                                            </table>
                                            </div><!-- Cierre table-estudiantes-wrapper -->
                                            </div>
                                        </div>
                                    </div>
                      				<?php include("enlaces-paginacion.php");?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- select2 -->
	<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
	<!-- SweetAlert2 -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- end js include path -->
	<script>
		console.log('==========================================');
		console.log('INICIO DE SCRIPTS DE ESTUDIANTES.PHP - EDICIÓN MASIVA');
		console.log('jQuery disponible:', typeof $ !== 'undefined');
		console.log('Bootstrap modal disponible:', typeof $.fn.modal === 'function');
		console.log('==========================================');
		
		// Manejar errores de scripts externos para que no bloqueen la página
		window.addEventListener('error', function(e) {
			console.error('Error global capturado:', e.message, e.filename, e.lineno);
			// Silenciar errores de modal/tooltip que bloquean la ejecución
			if (e.message && (e.message.includes('modal') || e.message.includes('tooltip') || e.message.includes('addEventListener'))) {
				console.warn('Error silenciado para no bloquear la página:', e.message);
				e.preventDefault();
				return true;
			}
		});
		
		// Variables globales para edición masiva
		var selectedEstudiantes = [];
		
		try {
			console.log('Inicializando funcionalidad de edición masiva de estudiantes...');
			
			// Manejar selección de todos los estudiantes (excluyendo los deshabilitados)
			$('#selectAllEstudiantes').on('change', function() {
				var isChecked = $(this).is(':checked');
				// Solo seleccionar checkboxes que no estén deshabilitados
				$('.estudiante-checkbox:not(:disabled)').prop('checked', isChecked).trigger('change');
			});
			
			// Manejar selección individual de estudiantes
			$(document).on('change', '.estudiante-checkbox', function() {
				var row = $(this).closest('tr');
				var estudianteId = $(this).val();
				
				if ($(this).is(':checked')) {
					row.addClass('estudiante-row-selected');
					if (selectedEstudiantes.indexOf(estudianteId) === -1) {
						selectedEstudiantes.push(estudianteId);
					}
				} else {
					row.removeClass('estudiante-row-selected');
					selectedEstudiantes = selectedEstudiantes.filter(id => id !== estudianteId);
				}
				
				// Actualizar checkbox "Seleccionar todos" solo considerando los checkboxes habilitados
				var totalHabilitados = $('.estudiante-checkbox:not(:disabled)').length;
				var totalSeleccionados = $('.estudiante-checkbox:checked:not(:disabled)').length;
				$('#selectAllEstudiantes').prop('checked', totalHabilitados > 0 && totalSeleccionados === totalHabilitados);
				toggleActionButtons();
			});
			
			function toggleActionButtons() {
				var hasSelection = selectedEstudiantes.length > 0;
				// Mostrar/ocultar el botón en lugar de deshabilitarlo
				if (hasSelection) {
					$('#editarMasivoEstudiantesBtn').fadeIn(200);
				} else {
					$('#editarMasivoEstudiantesBtn').fadeOut(200);
				}
			}
			
			// Manejar clic del botón de edición masiva
			console.log('Enlazando evento click al botón con delegation...');
			console.log('Botón existe:', $('#editarMasivoEstudiantesBtn').length > 0);
			$(document).on('click', '#editarMasivoEstudiantesBtn', function(e) {
				console.log('=== BOTÓN EDICIÓN MASIVA ESTUDIANTES CLICKEADO ===');
				e.preventDefault();
				e.stopPropagation();
				
				if (selectedEstudiantes.length === 0) {
					$.toast({
						heading: 'Advertencia',
						text: 'Por favor selecciona al menos un estudiante.',
						showHideTransition: 'slide',
						icon: 'warning',
						position: 'top-right'
					});
					return;
				}
				
				// Actualizar contador de estudiantes seleccionados
				$('#numeroEstudiantesSeleccionados').text(selectedEstudiantes.length);
				
				// Cargar opciones de grados y grupos
				cargarOpcionesGradosGrupos();
				
				// Mostrar modal primero
				$('#editarMasivoEstudiantesModal').modal('show');
			});
			
			// Cargar opciones de grados y grupos
			function cargarOpcionesGradosGrupos() {
				// Cargar grados
				$.ajax({
					url: 'ajax-cargar-grados.php',
					type: 'GET',
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							var gradoSelect = $('#grado');
							gradoSelect.empty().append('<option value="">-- No modificar --</option>');
							response.data.forEach(function(grado) {
								gradoSelect.append('<option value="' + grado.id + '">' + grado.nombre + '</option>');
							});
						}
					}
				});
				
				// Cargar grupos
				$.ajax({
					url: 'ajax-cargar-grupos.php',
					type: 'GET',
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							var grupoSelect = $('#grupo');
							grupoSelect.empty().append('<option value="">-- No modificar --</option>');
							response.data.forEach(function(grupo) {
								grupoSelect.append('<option value="' + grupo.id + '">' + grupo.nombre + '</option>');
							});
						}
					}
				});
			}
			
			// Inicializar Select2 DESPUÉS de que el modal esté completamente visible
			$('#editarMasivoEstudiantesModal').on('shown.bs.modal', function () {
				$('.select2-modal').select2({
					width: '100%',
					placeholder: 'Seleccionar...',
					allowClear: true,
					language: {
						noResults: function() {
							return "No se encontraron resultados";
						},
						searching: function() {
							return "Buscando...";
						}
					}
				});
			});
			
			// Manejar clic del botón de aplicar cambios masivos
			console.log('Enlazando evento click al botón de aplicar cambios...');
			$(document).on('click', '#btnConfirmarEdicionMasivaEstudiantes', function(e) {
				console.log('=== BOTÓN APLICAR CAMBIOS ESTUDIANTES CLICKEADO ===');
				e.preventDefault();
				e.stopPropagation();
				
				console.log('=== FORMULARIO DE EDICIÓN MASIVA ESTUDIANTES ENVIADO ===');
				
				var camposAActualizar = {};
				var hayCambios = false;
				
				// Obtener valores directamente de los campos (para manejar Select2 correctamente)
				var estadoMatricula = $('#estadoMatricula').val();
				var estrato = $('#estrato').val();
				var grado = $('#grado').val();
				var grupo = $('#grupo').val();
				var generarUsuario = $('#generarUsuario').is(':checked') ? '1' : '';
				
				console.log('Valores del formulario:');
				console.log('- Estado Matrícula:', estadoMatricula);
				console.log('- Estrato:', estrato);
				console.log('- Grado:', grado);
				console.log('- Grupo:', grupo);
				console.log('- Generar Usuario:', generarUsuario);
				
				// Agregar campos no vacíos
				if (estadoMatricula && estadoMatricula !== '') {
					camposAActualizar.estadoMatricula = estadoMatricula;
					console.log('✓ Estado Matrícula agregado:', estadoMatricula);
					hayCambios = true;
				}
				
				if (estrato && estrato !== '') {
					camposAActualizar.estrato = estrato;
					console.log('✓ Estrato agregado:', estrato);
					hayCambios = true;
				}
				
				if (grado && grado !== '') {
					camposAActualizar.grado = grado;
					console.log('✓ Grado agregado:', grado);
					hayCambios = true;
				}
				
				if (grupo && grupo !== '') {
					camposAActualizar.grupo = grupo;
					console.log('✓ Grupo agregado:', grupo);
					hayCambios = true;
				}
				
				if (generarUsuario === '1') {
					camposAActualizar.generarUsuario = generarUsuario;
					console.log('✓ Generar Usuario agregado:', generarUsuario);
					hayCambios = true;
				}
				
				console.log('Campos finales a actualizar:', camposAActualizar);
				console.log('Hay cambios:', hayCambios);
				
				if (!hayCambios) {
					$.toast({
						heading: 'Advertencia',
						text: 'Por favor selecciona al menos un campo para modificar.',
						showHideTransition: 'slide',
						icon: 'warning',
						position: 'top-right'
					});
					return;
				}
				
				// Aplicar cambios directamente sin confirmación adicional
				aplicarEdicionMasivaEstudiantes(camposAActualizar);
			});
			
			function aplicarEdicionMasivaEstudiantes(camposAActualizar) {
				console.log('Iniciando edición masiva de estudiantes...');
				console.log('Estudiantes seleccionados:', selectedEstudiantes);
				console.log('Campos a actualizar:', camposAActualizar);
				
				// Mostrar loader en el botón
				var btnOriginal = $('#btnConfirmarEdicionMasivaEstudiantes').html();
				$('#btnConfirmarEdicionMasivaEstudiantes').html('<i class="fa fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
				
				// Toast de proceso
				$.toast({
					heading: 'Procesando',
					text: 'Actualizando ' + selectedEstudiantes.length + ' estudiante(s)...',
					showHideTransition: 'slide',
					icon: 'info',
					position: 'top-right',
					hideAfter: false,
					loader: true,
					loaderBg: '#3498db'
				});
				
				$.ajax({
					url: 'estudiantes-editar-masivo.php',
					type: 'POST',
					data: {
						estudiantes: selectedEstudiantes,
						campos: camposAActualizar
					},
					dataType: 'json',
					success: function(response) {
						console.log('Respuesta del servidor:', response);
						
						$('#btnConfirmarEdicionMasivaEstudiantes').html(btnOriginal).prop('disabled', false);
						
						// Cerrar el toast de procesamiento
						$('.jq-toast-wrap').remove();
						
					if (response.success) {
						// Cerrar modal
						$('#editarMasivoEstudiantesModal').modal('hide');
						
						// Construir mensaje de éxito
						var mensaje = response.message || 'Operación completada exitosamente.';
						
						// Agregar detalles adicionales si están disponibles
						if (response.usuarios_generados > 0) {
							mensaje += ' Se generaron ' + response.usuarios_generados + ' usuarios.';
						}
						
						// Mostrar mensaje de éxito
						$.toast({
							heading: '¡Éxito!',
							text: mensaje,
							showHideTransition: 'slide',
							icon: 'success',
							position: 'top-right',
							hideAfter: 6000
						});
						
						// Recargar solo la tabla de estudiantes sin recargar toda la página
						setTimeout(function() {
							recargarTablaEstudiantes();
						}, 500);
						
					} else {
							$.toast({
								heading: 'Error',
								text: response.message || 'No se pudo actualizar ningún estudiante.',
								showHideTransition: 'slide',
								icon: 'error',
								position: 'top-right',
								hideAfter: 5000
							});
						}
					},
					error: function(xhr, status, error) {
						console.error('Error AJAX:', error);
						console.error('Response:', xhr.responseText);
						
						$('#btnConfirmarEdicionMasivaEstudiantes').html(btnOriginal).prop('disabled', false);
						
						// Cerrar el toast de procesamiento
						$('.jq-toast-wrap').remove();
						
						$.toast({
							heading: 'Error',
							text: 'Error de conexión al servidor.',
							showHideTransition: 'slide',
							icon: 'error',
							position: 'top-right',
							hideAfter: 5000
						});
					}
				});
			}
			
			// Función para recargar la tabla de estudiantes sin recargar toda la página
			function recargarTablaEstudiantes() {
				console.log('Recargando tabla de estudiantes...');
				
				// Mostrar loader
				$('#gifCarga').show();
				
				// Obtener filtros actuales
				var cursos = $('#filtro_cursos').val() || [];
				var grupos = $('#filtro_grupos').val() || [];
				var estados = $('#filtro_estados').val() || [];
				var busqueda = $('#filtro_busqueda').val() || '';
				var fechaDesde = $('#filtro_fecha_desde').val() || '';
				var fechaHasta = $('#filtro_fecha_hasta').val() || '';
				
				$.ajax({
					url: 'ajax-filtrar-estudiantes.php',
					type: 'POST',
					data: {
						cursos: cursos,
						grupos: grupos,
						estados: estados,
						busqueda: busqueda,
						fechaDesde: fechaDesde,
						fechaHasta: fechaHasta
					},
					dataType: 'json',
					success: function(response) {
						$('#gifCarga').hide();
						
						if (response.success) {
							// Actualizar el contenido de la tabla
							$('#matriculas_result').html(response.html);
							
							// Limpiar las selecciones
							selectedEstudiantes = [];
							$('#selectAllEstudiantes').prop('checked', false);
							toggleActionButtons();
							
							console.log('Tabla de estudiantes recargada exitosamente');
						} else {
							console.error('Error al recargar tabla:', response.error);
							$.toast({
								heading: 'Advertencia',
								text: 'Los cambios se aplicaron pero hubo un problema al actualizar la vista.',
								showHideTransition: 'slide',
								icon: 'warning',
								position: 'top-right',
								hideAfter: 3000
							});
						}
					},
					error: function(xhr, status, error) {
						$('#gifCarga').hide();
						console.error('Error al recargar tabla:', error);
						// No mostrar error ya que los cambios sí se aplicaron
					}
				});
			}
			
			// Limpiar selección al cerrar el modal
			$('#editarMasivoEstudiantesModal').on('hidden.bs.modal', function () {
				$('.select2-modal').select2('destroy');
			});
			
		} catch(error) {
			console.error('Error en funcionalidad de edición masiva de estudiantes:', error);
			console.log('Continuando con el resto de la página...');
		}
		
		$(function () {
			$('[data-toggle="popover"]').popover();
		});

		$('.popover-dismiss').popover({trigger: 'focus'});
		
		// === Filtros Mejorados con Multiselect ===
		
		$(document).ready(function() {
			// Toggle de los filtros
			$('#btnToggleFiltros').on('click', function() {
				const card = $('#cardFiltros');
				const icon = $(this).find('i');
				
				if (card.is(':visible')) {
					card.slideUp(300);
					icon.removeClass('fa-chevron-up').addClass('fa-filter');
					$(this).removeClass('btn-primary').addClass('btn-outline-secondary');
				} else {
					card.slideDown(300);
					icon.removeClass('fa-filter').addClass('fa-chevron-up');
					$(this).removeClass('btn-outline-secondary').addClass('btn-primary');
				}
			});
			
			// Inicializar Select2 en los filtros
			$('.select2-multiple').select2({
				placeholder: "Seleccione una o más opciones",
				allowClear: true,
				language: {
					noResults: function() {
						return "No se encontraron resultados";
					},
					searching: function() {
						return "Buscando...";
					}
				}
			});
			
			// Función para aplicar filtros
			function aplicarFiltros() {
				const cursos = $('#filtro_cursos').val() || [];
				const grupos = $('#filtro_grupos').val() || [];
				const estados = $('#filtro_estados').val() || [];
				const busqueda = $('#filtro_busqueda').val() || '';
				const fechaDesde = $('#filtro_fecha_desde').val() || '';
				const fechaHasta = $('#filtro_fecha_hasta').val() || '';
				
				console.log('Aplicando filtros:', { cursos, grupos, estados, busqueda, fechaDesde, fechaHasta });
				
				// Mostrar loader
				$('#gifCarga').show();
				$('#matriculas_result').html('<tr><td colspan="9" class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Cargando...</td></tr>');
				
				// Enviar AJAX
				$.ajax({
					url: 'ajax-filtrar-estudiantes.php',
					type: 'POST',
					data: {
						cursos: cursos,
						grupos: grupos,
						estados: estados,
						busqueda: busqueda,
						fechaDesde: fechaDesde,
						fechaHasta: fechaHasta
					},
					dataType: 'json',
					success: function(response) {
						console.log('Respuesta del filtro:', response);
						
						$('#gifCarga').hide();
						
						if (response.success) {
							// Insertar el HTML
							$('#matriculas_result').html(response.html);
							
							// Forzar que TODAS las filas expandibles estén completamente ocultas
							$('#matriculas_result tr.expandable-row').each(function() {
								$(this).hide();
								$(this).css('display', 'none');
								$(this).attr('style', 'display: none !important;');
							});
							
							// Resetear todos los botones al estado inicial
							$('.expand-btn').removeClass('text-primary').addClass('text-secondary');
							$('.expand-btn i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
							
							console.log('Filas expandibles después de filtrar:', $('#matriculas_result tr.expandable-row').length);
							console.log('Filas visibles:', $('#matriculas_result tr.expandable-row:visible').length);
							
							// Mostrar mensaje de resultados
							let mensajeResultado = 'Se encontraron ' + response.total + ' estudiante(s)';
							if ($('#filtro_busqueda').val()) {
								mensajeResultado += ' con "' + $('#filtro_busqueda').val() + '"';
							}
							
							$.toast({
								heading: 'Filtros Aplicados',
								text: mensajeResultado,
								position: 'top-right',
								loaderBg: '#26c281',
								icon: 'success',
								hideAfter: 3000
							});
						} else {
							$.toast({
								heading: 'Error',
								text: response.error || 'Error al aplicar filtros',
								position: 'top-right',
								loaderBg: '#bf441d',
								icon: 'error',
								hideAfter: 5000
							});
							
							$('#matriculas_result').html('<tr><td colspan="9" class="text-center text-danger">Error al cargar los datos</td></tr>');
						}
					},
					error: function(xhr, status, error) {
						console.error('Error AJAX:', status, error);
						console.error('Response:', xhr.responseText);
						
						$('#gifCarga').hide();
						
						$.toast({
							heading: 'Error de Conexión',
							text: 'No se pudo conectar con el servidor',
							position: 'top-right',
							loaderBg: '#bf441d',
							icon: 'error',
							hideAfter: 5000
						});
						
						$('#matriculas_result').html('<tr><td colspan="9" class="text-center text-danger">Error de conexión</td></tr>');
					}
				});
			}
			
			// Aplicar filtros al hacer clic en el botón
			$('#btnAplicarFiltros').on('click', function() {
				aplicarFiltros();
			});
			
			// Limpiar filtros
			$('#btnLimpiarFiltros').on('click', function() {
				$('#filtro_cursos').val(null).trigger('change');
				$('#filtro_grupos').val(null).trigger('change');
				$('#filtro_estados').val(null).trigger('change');
				$('#filtro_busqueda').val('');
				$('#filtro_fecha_desde').val('');
				$('#filtro_fecha_hasta').val('');
				
				// Recargar la página para mostrar todos los estudiantes
				location.reload();
			});
			
			// Aplicar filtros automáticamente al cambiar las opciones
			$('.select2-multiple').on('change', function() {
				// Aplicar filtros después de un breve delay para evitar múltiples llamadas
				clearTimeout(window.filtroTimeout);
				window.filtroTimeout = setTimeout(function() {
					aplicarFiltros();
				}, 500);
			});
			
			// Búsqueda al hacer clic en el botón
			$('#btnBuscar').on('click', function() {
				aplicarFiltros();
			});
			
			// Búsqueda al presionar Enter
			$('#filtro_busqueda').on('keypress', function(e) {
				if (e.which === 13) { // Enter key
					e.preventDefault();
					aplicarFiltros();
				}
			});
			
			// Validación y aplicación de filtros al cambiar las fechas
			$('#filtro_fecha_desde, #filtro_fecha_hasta').on('change', function() {
				validarFechas();
				clearTimeout(window.filtroTimeout);
				window.filtroTimeout = setTimeout(function() {
					aplicarFiltros();
				}, 500);
			});
			
			// Función para validar fechas y evitar errores humanos
			function validarFechas() {
				const fechaDesde = $('#filtro_fecha_desde').val();
				const fechaHasta = $('#filtro_fecha_hasta').val();
				const hoy = new Date().toISOString().split('T')[0]; // Formato YYYY-MM-DD
				
				// Si hay fecha desde, actualizar el atributo min de fecha hasta
				if (fechaDesde) {
					$('#filtro_fecha_hasta').attr('min', fechaDesde);
				} else {
					$('#filtro_fecha_hasta').removeAttr('min');
				}
				
				// Validar que fecha hasta no sea mayor a hoy
				if (fechaHasta && fechaHasta > hoy) {
					$('#filtro_fecha_hasta').val(hoy);
					$.toast({
						heading: 'Atención',
						text: 'La fecha hasta no puede ser mayor a hoy. Se ajustó automáticamente.',
						showHideTransition: 'slide',
						icon: 'warning',
						position: 'top-right',
						hideAfter: 3000
					});
				}
				
				// Validar que fecha hasta sea mayor o igual a fecha desde
				if (fechaDesde && fechaHasta && fechaHasta < fechaDesde) {
					$('#filtro_fecha_hasta').val(fechaDesde);
					$.toast({
						heading: 'Atención',
						text: 'La fecha hasta debe ser mayor o igual a la fecha desde. Se ajustó automáticamente.',
						showHideTransition: 'slide',
						icon: 'warning',
						position: 'top-right',
						hideAfter: 3000
					});
				}
			}
			
			// Cerrar dropdowns al hacer scroll dentro del contenedor de la tabla
			$('.table-estudiantes-wrapper').on('scroll', function() {
				// Cerrar todos los dropdowns abiertos
				$('.dropdown-menu.show').each(function() {
					var $dropdown = $(this).closest('.dropdown, .btn-group');
					$dropdown.find('[data-toggle="dropdown"]').dropdown('toggle');
				});
			});
			
			// ========================================
			// SISTEMA DE PANEL DE ACCIONES FLOTANTE
			// ========================================
			
			// Variable global para almacenar el panel actual
			window.currentAccionesPanel = null;
			
			// Función para mostrar el panel de acciones
			window.mostrarPanelAcciones = function(btn, estudianteId) {
				// Cerrar cualquier panel abierto
				cerrarPanelAcciones();
				
				// Crear el overlay
				var overlay = $('<div class="acciones-overlay show"></div>');
				$('body').append(overlay);
				
				// Obtener el contenido del dropdown correspondiente
				var dropdownMenu = $('#Acciones_' + estudianteId);
				if (!dropdownMenu.length) {
					console.error('No se encontró el menú de acciones para el estudiante:', estudianteId);
					return;
				}
				
				// Crear el panel
				var panel = $('<div class="acciones-panel show"></div>');
				var lista = $('<div class="acciones-list"></div>');
				
				// Mapeo de iconos y colores por acción (más sutiles)
				var accionesConfig = {
					'Editar matrícula': { icon: 'fa-edit', color: '#667eea' },
					'Edición rápida': { icon: 'fa-bolt', color: '#f5576c' },
					'Transferir a SION': { icon: 'fa-exchange-alt', color: '#00f2fe' },
					'Cambiar de grupo': { icon: 'fa-users', color: '#38f9d7' },
					'Editar usuario': { icon: 'fa-user-edit', color: '#fa709a' },
					'Retirar': { icon: 'fa-user-times', color: '#ee5a6f' },
					'Restaurar': { icon: 'fa-undo', color: '#96fbc4' },
					'Reservar cupo': { icon: 'fa-bookmark', color: '#fdbb2d' },
					'Eliminar': { icon: 'fa-trash', color: '#eb3349' },
					'Generar usuario': { icon: 'fa-user-plus', color: '#6a11cb' },
					'Autologin': { icon: 'fa-sign-in-alt', color: '#37ecba' },
					'Boletín': { icon: 'fa-file-alt', color: '#667eea' },
					'Libro Final': { icon: 'fa-book', color: '#a18cd1' },
					'Informe parcial': { icon: 'fa-chart-line', color: '#84fab0' },
					'Hoja de matrícula': { icon: 'fa-file-contract', color: '#ffecd2' },
					'SION - Estado de cuenta': { icon: 'fa-money-bill-wave', color: '#a1c4fd' },
					'Ficha estudiantil': { icon: 'fa-id-card', color: '#fccb90' },
					'Adjuntar documentos': { icon: 'fa-paperclip', color: '#e0c3fc' }
				};
				
				// Convertir los items del dropdown en items de lista vertical
				dropdownMenu.find('li').each(function() {
					var link = $(this).find('a');
					if (link.length) {
						var texto = link.text().trim();
						var href = link.attr('href');
						var onclick = link.attr('onclick');
						
						// Buscar configuración de icono
						var config = null;
						for (var key in accionesConfig) {
							if (texto.includes(key)) {
								config = accionesConfig[key];
								break;
							}
						}
						
						// Configuración por defecto si no se encuentra
						if (!config) {
							config = { icon: 'fa-cog', color: '#95a5a6' };
						}
						
						// Crear el item
						var item = $('<a class="accion-item"></a>');
						if (href && href !== 'javascript:void(0);') {
							item.attr('href', href);
							if (link.attr('target')) {
								item.attr('target', link.attr('target'));
							}
						} else if (onclick) {
							item.attr('href', 'javascript:void(0);');
							item.attr('onclick', onclick);
						}
						
						// Icono con color sólido
						var iconDiv = $('<div class="accion-icon"></div>').css('background', config.color);
						iconDiv.html('<i class="fa ' + config.icon + '"></i>');
						
						var nameSpan = $('<span class="accion-name"></span>').text(texto);
						
						item.append(iconDiv).append(nameSpan);
						
						// Al hacer clic, cerrar el panel
						item.on('click', function() {
							cerrarPanelAcciones();
						});
						
						lista.append(item);
					}
				});
				
				panel.append(lista);
				
				// Posicionar el panel cerca del botón
				var btnOffset = $(btn).offset();
				var btnHeight = $(btn).outerHeight();
				var btnWidth = $(btn).outerWidth();
				
				// Agregar el panel al body temporalmente para obtener sus dimensiones
				$('body').append(panel);
				
				var panelWidth = panel.outerWidth();
				var panelHeight = panel.outerHeight();
				var windowWidth = $(window).width();
				var windowHeight = $(window).height();
				
				// Calcular posición óptima (alineado a la derecha del botón)
				var topPos = btnOffset.top;
				var leftPos = btnOffset.left - panelWidth - 5;
				
				// Si se sale por la izquierda, mostrar a la derecha del botón
				if (leftPos < 20) {
					leftPos = btnOffset.left + btnWidth + 5;
				}
				
				// Si se sale por la derecha, alinearlo al borde derecho
				if (leftPos + panelWidth > windowWidth - 20) {
					leftPos = windowWidth - panelWidth - 20;
				}
				
				// Ajustar verticalmente si se sale por abajo
				if (topPos + panelHeight > windowHeight - 20) {
					topPos = windowHeight - panelHeight - 20;
				}
				
				// Ajustar verticalmente si se sale por arriba
				if (topPos < 20) {
					topPos = 20;
				}
				
				// Aplicar posición
				panel.css({
					top: topPos + 'px',
					left: leftPos + 'px'
				});
				
				// Guardar referencia
				window.currentAccionesPanel = panel;
				
				// Cerrar al hacer clic en el overlay
				overlay.on('click', cerrarPanelAcciones);
			};
			
			// Función para cerrar el panel
			window.cerrarPanelAcciones = function() {
				$('.acciones-overlay').remove();
				if (window.currentAccionesPanel) {
					window.currentAccionesPanel.remove();
					window.currentAccionesPanel = null;
				}
			};
			
			// Cerrar al hacer scroll
			$('.table-estudiantes-wrapper, window').on('scroll', function() {
				if (window.currentAccionesPanel) {
					cerrarPanelAcciones();
				}
			});
			
			// Cerrar al presionar ESC
			$(document).on('keydown', function(e) {
				if (e.key === 'Escape' && window.currentAccionesPanel) {
					cerrarPanelAcciones();
				}
			});
			
			// ========================================
			// SISTEMA DE EXPORTACIÓN A EXCEL
			// ========================================
			
			// Manejar clic en botón de exportar
			$('#btnExportarExcel').on('click', function() {
				$('#modalExportarExcel').modal('show');
			});
			
			// Seleccionar todos los campos
			$('#btnSeleccionarTodos').on('click', function() {
				$('.campo-exportar').prop('checked', true);
			});
			
			// Deseleccionar todos los campos
			$('#btnDeseleccionarTodos').on('click', function() {
				$('.campo-exportar').prop('checked', false);
			});
			
			// Confirmar exportación
			$('#btnConfirmarExportar').on('click', function() {
				// Obtener campos seleccionados
				var camposSeleccionados = [];
				$('.campo-exportar:checked').each(function() {
					camposSeleccionados.push($(this).val());
				});
				
				if (camposSeleccionados.length === 0) {
					$.toast({
						heading: 'Advertencia',
						text: 'Por favor selecciona al menos un campo para exportar.',
						showHideTransition: 'slide',
						icon: 'warning',
						position: 'top-right'
					});
					return;
				}
				
				// Obtener filtros actuales
				var cursos = $('#filtro_cursos').val() || [];
				var grupos = $('#filtro_grupos').val() || [];
				var estados = $('#filtro_estados').val() || [];
				var busqueda = $('#filtro_busqueda').val() || '';
				
				// Cerrar modal
				$('#modalExportarExcel').modal('hide');
				
				// Mostrar mensaje de procesamiento
				$.toast({
					heading: 'Generando Excel',
					text: 'Por favor espera mientras se genera el archivo...',
					showHideTransition: 'slide',
					icon: 'info',
					position: 'top-right',
					hideAfter: false,
					loader: true,
					loaderBg: '#26c281'
				});
				
				// Obtener filtros adicionales
				var fechaDesde = $('#filtro_fecha_desde').val() || '';
				var fechaHasta = $('#filtro_fecha_hasta').val() || '';
				
				// Construir URL con parámetros
				var url = 'estudiantes-exportar-excel.php?';
				url += 'campos=' + encodeURIComponent(JSON.stringify(camposSeleccionados));
				
				if (cursos.length > 0) {
					url += '&cursos=' + encodeURIComponent(JSON.stringify(cursos));
				}
				if (grupos.length > 0) {
					url += '&grupos=' + encodeURIComponent(JSON.stringify(grupos));
				}
				if (estados.length > 0) {
					url += '&estados=' + encodeURIComponent(JSON.stringify(estados));
				}
				if (busqueda) {
					url += '&busqueda=' + encodeURIComponent(busqueda);
				}
				if (fechaDesde) {
					url += '&fecha_desde=' + encodeURIComponent(fechaDesde);
				}
				if (fechaHasta) {
					url += '&fecha_hasta=' + encodeURIComponent(fechaHasta);
				}
				
				// Abrir en nueva ventana para descargar
				window.open(url, '_blank');
				
				// Cerrar toast después de un breve delay
				setTimeout(function() {
					$('.jq-toast-wrap').remove();
					$.toast({
						heading: 'Éxito',
						text: 'El archivo Excel se está generando. Si no se descarga automáticamente, revisa tu configuración de descargas.',
						showHideTransition: 'slide',
						icon: 'success',
						position: 'top-right',
						hideAfter: 5000
					});
				}, 1000);
			});
		});
	</script>
	
	<!-- Modal de Edición Masiva de Estudiantes -->
	<div class="modal fade" id="editarMasivoEstudiantesModal" tabindex="-1" role="dialog" aria-labelledby="editarMasivoEstudiantesModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editarMasivoEstudiantesModalLabel">
						<i class="fa fa-users"></i> Edición Masiva de Estudiantes
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="editarMasivoEstudiantesForm" method="post" action="javascript:void(0);">
					<div class="modal-body">
						<div class="alert alert-info">
							<i class="fa fa-info-circle"></i> 
							<strong>Instrucciones:</strong> Los campos que dejes en blanco o sin seleccionar NO serán modificados. 
							Solo se actualizarán los campos que completes.
						</div>
						
						<div id="contadorEstudiantesSeleccionados" class="alert alert-warning">
							<i class="fa fa-users"></i> 
							<strong>Estudiantes seleccionados:</strong> <span id="numeroEstudiantesSeleccionados">0</span>
						</div>
						
						<!-- Estado de Matrícula -->
						<div class="form-group">
							<label for="estadoMatricula">Estado de Matrícula</label>
							<select class="form-control select2-modal" id="estadoMatricula" name="estadoMatricula">
								<option value="">-- No modificar --</option>
								<?php foreach ($estadosMatriculasEstudiantes as $clave => $valor) { ?>
									<option value="<?= $clave; ?>"><?= $valor; ?></option>
								<?php } ?>
							</select>
						</div>
						
						<!-- Estrato -->
						<div class="form-group">
							<label for="estrato">Estrato</label>
							<select class="form-control select2-modal" id="estrato" name="estrato">
								<option value="">-- No modificar --</option>
								<?php
								$opcionesEstrato = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=3");
								while($o = mysqli_fetch_array($opcionesEstrato, MYSQLI_BOTH)){
									echo '<option value="'.$o[0].'">'.$o[1].'</option>';
								}
								?>
							</select>
						</div>
						
						<hr>
						
						<h5><i class="fa fa-graduation-cap"></i> Cambios Académicos</h5>
						<div class="alert alert-warning">
							<i class="fa fa-exclamation-triangle"></i>
							<strong>Importante:</strong> Los cambios de grado y grupo solo se aplicarán a estudiantes que NO tengan notas registradas.
						</div>
						
						<!-- Grado -->
						<div class="form-group">
							<label for="grado">Grado</label>
							<select class="form-control select2-modal" id="grado" name="grado">
								<option value="">-- No modificar --</option>
								<!-- Se cargarán dinámicamente -->
							</select>
						</div>
						
						<!-- Grupo -->
						<div class="form-group">
							<label for="grupo">Grupo</label>
							<select class="form-control select2-modal" id="grupo" name="grupo">
								<option value="">-- No modificar --</option>
								<!-- Se cargarán dinámicamente -->
							</select>
						</div>
						
						<hr>
						
						<h5><i class="fa fa-user-plus"></i> Generación de Usuarios</h5>
						
						<!-- Generar Usuario -->
						<div class="form-group">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="generarUsuario" name="generarUsuario" value="1">
								<label class="form-check-label" for="generarUsuario">
									<strong>Generar usuario para estudiantes sin usuario</strong>
								</label>
							</div>
							<small class="form-text text-muted">
								Se generará automáticamente un usuario basado en el documento de identidad.
							</small>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">
							<i class="fa fa-times"></i> Cancelar
						</button>
						<button type="button" class="btn btn-warning" id="btnConfirmarEdicionMasivaEstudiantes">
							<i class="fa fa-save"></i> Aplicar Cambios Masivos
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<!-- Modal de Exportar a Excel -->
	<div class="modal fade" id="modalExportarExcel" tabindex="-1" role="dialog" aria-labelledby="modalExportarExcelLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalExportarExcelLabel">
						<i class="fa fa-file-excel"></i> Exportar Estudiantes a Excel
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="alert alert-info">
						<i class="fa fa-info-circle"></i> 
						<strong>Información:</strong> Se exportarán los estudiantes que se muestran actualmente en la tabla, respetando los filtros aplicados. 
						Selecciona los campos que deseas incluir en el archivo Excel.
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<h6><i class="fa fa-user"></i> Información Básica</h6>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_id" value="id" checked>
								<label class="form-check-label" for="campo_id">ID</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_nombre_completo" value="nombre_completo" checked>
								<label class="form-check-label" for="campo_nombre_completo">Nombre Completo</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_documento" value="documento" checked>
								<label class="form-check-label" for="campo_documento">Documento</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_tipo_documento" value="tipo_documento">
								<label class="form-check-label" for="campo_tipo_documento">Tipo de Documento</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_usuario" value="usuario">
								<label class="form-check-label" for="campo_usuario">Usuario</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_email" value="email">
								<label class="form-check-label" for="campo_email">Email</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_telefono" value="telefono">
								<label class="form-check-label" for="campo_telefono">Teléfono</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_genero" value="genero">
								<label class="form-check-label" for="campo_genero">Género</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_fecha_nacimiento" value="fecha_nacimiento">
								<label class="form-check-label" for="campo_fecha_nacimiento">Fecha de Nacimiento</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_lugar_nacimiento" value="lugar_nacimiento">
								<label class="form-check-label" for="campo_lugar_nacimiento">Lugar de Nacimiento</label>
							</div>
						</div>
						
						<div class="col-md-6">
							<h6><i class="fa fa-graduation-cap"></i> Información Académica</h6>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_grado" value="grado" checked>
								<label class="form-check-label" for="campo_grado">Grado</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_grupo" value="grupo" checked>
								<label class="form-check-label" for="campo_grupo">Grupo</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_estado_matricula" value="estado_matricula" checked>
								<label class="form-check-label" for="campo_estado_matricula">Estado de Matrícula</label>
							</div>
							
							<h6 class="mt-3"><i class="fa fa-home"></i> Información de Residencia</h6>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_direccion" value="direccion">
								<label class="form-check-label" for="campo_direccion">Dirección</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_barrio" value="barrio">
								<label class="form-check-label" for="campo_barrio">Barrio</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_estrato" value="estrato">
								<label class="form-check-label" for="campo_estrato">Estrato</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_lugar_expedicion" value="lugar_expedicion">
								<label class="form-check-label" for="campo_lugar_expedicion">Lugar de Expedición</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_tipo_sangre" value="tipo_sangre">
								<label class="form-check-label" for="campo_tipo_sangre">Tipo de Sangre</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_folio" value="folio">
								<label class="form-check-label" for="campo_folio">Folio</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_codigo_tesoreria" value="codigo_tesoreria">
								<label class="form-check-label" for="campo_codigo_tesoreria">Código de Tesorería</label>
							</div>
							
							<h6 class="mt-3"><i class="fa fa-user-friends"></i> Información de Acudiente</h6>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_acudiente_nombre" value="acudiente_nombre">
								<label class="form-check-label" for="campo_acudiente_nombre">Nombre del Acudiente</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_acudiente_documento" value="acudiente_documento">
								<label class="form-check-label" for="campo_acudiente_documento">Documento del Acudiente</label>
							</div>
							<div class="form-check">
								<input class="form-check-input campo-exportar" type="checkbox" id="campo_acudiente_email" value="acudiente_email">
								<label class="form-check-label" for="campo_acudiente_email">Email del Acudiente</label>
							</div>
						</div>
					</div>
					
					<hr>
					
					<div class="row">
						<div class="col-md-12">
							<button type="button" class="btn btn-sm btn-outline-secondary" id="btnSeleccionarTodos">
								<i class="fa fa-check-square"></i> Seleccionar Todos
							</button>
							<button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeseleccionarTodos">
								<i class="fa fa-square"></i> Deseleccionar Todos
							</button>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="button" class="btn btn-success" id="btnConfirmarExportar">
						<i class="fa fa-file-excel"></i> Exportar
					</button>
				</div>
			</div>
		</div>
	</div>
</body>

</html>