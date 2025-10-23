<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0001';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once("../class/Estudiantes.php");
require_once("../class/servicios/GradoServicios.php"); 
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");


Utilidades::validarParametros($_GET);

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

									<!-- Barra de herramientas superior -->
									<div class="row mb-3">
										<div class="col-sm-12">
											<div class="d-flex justify-content-between align-items-center">
												<!-- Botones principales -->
												<div class="btn-group">
													<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0084'])){?>
														<a href="estudiantes-agregar.php" class="btn deepPink-bgcolor">
															<i class="fa fa-plus"></i> Agregar Estudiante
														</a>
													<?php }?>
													
													<!-- Botón de Edición Masiva -->
													<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0078'])){?>
														<button type="button" id="editarMasivoEstudiantesBtn" class="btn btn-warning" disabled>
															<i class="fa fa-edit"></i> Editar Seleccionados
														</button>
													<?php }?>
													
													<?php if(Modulos::validarSubRol(['DT0002'])){?>
														<a href="estudiantes-promedios.php" class="btn btn-info">
															<i class="fa fa-chart-line"></i> Promedios
														</a>
													<?php }?>
													
													<!-- Menú Matrículas -->
													<?php if(Modulos::validarSubRol(['DT0077', 'DT0080', 'DT0075'])){?>
														<div class="btn-group" role="group">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
																<i class="fa fa-list"></i> Menú Matrículas <span class="caret"></span>
															</button>
															<ul class="dropdown-menu">
																<?php if(Modulos::validarSubRol(['DT0077'])){?>
																	<li><a href="estudiantes-importar-excel.php"><i class="fa fa-file-excel"></i> Importar desde Excel</a></li>
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
													
													<!-- Más Opciones -->
													<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0212', 'DT0213', 'DT0214', 'DT0215', 'DT0175', 'DT0216', 'DT0149'])){?>
														<div class="btn-group" role="group">
															<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
																<i class="fa fa-tools"></i> Más Opciones <span class="caret"></span>
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
												</div>
												
												<!-- Botón de filtros -->
												<button type="button" class="btn btn-outline-secondary" id="btnToggleFiltros">
													<i class="fa fa-filter"></i> Filtros y Búsqueda
												</button>
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
													
													include("includes/consulta-paginacion-estudiantes.php");
													$filtroLimite = 'LIMIT '.$inicio.','.$registros;
													
													$selectSql = ["mat.*",
																  "uss.uss_id","uss.uss_usuario","uss.uss_bloqueado",
																  "gra_nombre","gru_nombre","gra_formato_boletin",
																  "acud.uss_nombre","acud.uss_nombre2","acud.uss_nombre2", "mat.id_nuevo AS mat_id_nuevo",
																  "og_tipo_doc.ogen_nombre as tipo_doc_nombre",
																  "og_genero.ogen_nombre as genero_nombre",
																  "og_estrato.ogen_nombre as estrato_nombre",
																  "og_tipo_sangre.ogen_nombre as tipo_sangre_nombre"];

													$consulta = Estudiantes::listarEstudiantes(0, $filtro, $filtroLimite, $cursoActual,null,$selectSql);
													
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
			
			// Manejar selección de todos los estudiantes
			$('#selectAllEstudiantes').on('change', function() {
				var isChecked = $(this).is(':checked');
				$('.estudiante-checkbox').prop('checked', isChecked).trigger('change');
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
				
				$('#selectAllEstudiantes').prop('checked', $('.estudiante-checkbox:checked').length === $('.estudiante-checkbox').length);
				toggleActionButtons();
			});
			
			function toggleActionButtons() {
				var hasSelection = selectedEstudiantes.length > 0;
				$('#editarMasivoEstudiantesBtn').prop('disabled', !hasSelection);
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
				
				$.ajax({
					url: 'ajax-filtrar-estudiantes.php',
					type: 'POST',
					data: {
						cursos: cursos,
						grupos: grupos,
						estados: estados,
						busqueda: busqueda
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
				
				console.log('Aplicando filtros:', { cursos, grupos, estados, busqueda });
				
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
						busqueda: busqueda
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
</body>

</html>