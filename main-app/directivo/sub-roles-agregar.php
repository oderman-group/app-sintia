<?php
include("session.php");

$idPaginaInterna = 'DT0206';

include("../compartido/historial-acciones-guardar.php");

include("../compartido/head.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

require_once("../class/SubRoles.php");

$listaRoles=SubRoles::listar();
$listaPaginas = SubRoles::listarPaginas();

// Agrupar páginas por módulo
$paginasPorModulo = [];
$totalPaginas = 0;
while ($pagina = mysqli_fetch_array($listaPaginas, MYSQLI_BOTH)) {
	$moduloId = $pagina['pagp_modulo'];
	$moduloNombre = $pagina['mod_nombre'];
	$moduloColor = $pagina['mod_color'] ?? '#667eea';
	
	if (!isset($paginasPorModulo[$moduloId])) {
		$paginasPorModulo[$moduloId] = [
			'nombre' => $moduloNombre,
			'color' => $moduloColor,
			'paginas' => []
		];
	}
	
	$paginasPorModulo[$moduloId]['paginas'][] = $pagina;
	$totalPaginas++;
}

?>
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<style>
/* ==================== ESTILOS MODERNOS ==================== */
body {
	background: #f5f7fa;
}

.page-content {
	background: transparent;
}

/* Header moderno */
.header-subroles {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	padding: 30px;
	border-radius: 15px 15px 0 0;
	color: white;
	margin-bottom: 0;
	box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.header-subroles h2 {
	margin: 0 0 10px 0;
	font-size: 28px;
	font-weight: 700;
	display: flex;
	align-items: center;
	gap: 15px;
}

.header-subroles p {
	margin: 0;
	opacity: 0.95;
	font-size: 15px;
}

/* Contenedor principal */
.container-subroles {
	background: white;
	border-radius: 0 0 15px 15px;
	box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
	overflow: hidden;
}

/* Sección de información básica */
.seccion-basica {
	padding: 30px;
	background: #f8f9fa;
	border-bottom: 2px solid #e2e8f0;
}

.form-group-modern {
	margin-bottom: 25px;
}

.form-group-modern label {
	font-weight: 600;
	color: #4a5568;
	margin-bottom: 10px;
	font-size: 15px;
	display: flex;
	align-items: center;
	gap: 8px;
}

.form-group-modern .input-group {
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
	border-radius: 10px;
	overflow: hidden;
}

.form-group-modern .form-control,
.form-group-modern .select2-container {
	min-height: 50px;
	border-radius: 10px;
	border: 2px solid #e2e8f0;
	font-size: 15px;
	transition: all 0.3s ease;
}

.form-group-modern .form-control:focus {
	border-color: #667eea;
	box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

/* Resumen de selección */
.resumen-seleccion {
	padding: 25px 30px;
	background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
	border-bottom: 3px solid #f59e0b;
	display: flex;
	align-items: center;
	justify-content: space-between;
	flex-wrap: wrap;
	gap: 15px;
}

.resumen-item {
	display: flex;
	align-items: center;
	gap: 10px;
}

.resumen-icon {
	width: 50px;
	height: 50px;
	border-radius: 12px;
	background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 24px;
	box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.resumen-texto h4 {
	margin: 0;
	font-size: 14px;
	color: #78350f;
	font-weight: 600;
	opacity: 0.8;
}

.resumen-texto .numero {
	font-size: 28px;
	font-weight: 700;
	color: #92400e;
	line-height: 1;
}

/* Filtros */
.filtros-container {
	padding: 25px 30px;
	background: white;
	border-bottom: 1px solid #e2e8f0;
}

.filtro-busqueda {
	position: relative;
}

.filtro-busqueda input {
	width: 100%;
	height: 45px;
	padding: 12px 45px 12px 20px;
	border: 2px solid #e2e8f0;
	border-radius: 10px;
	font-size: 15px;
	transition: all 0.3s ease;
}

.filtro-busqueda input:focus {
	border-color: #667eea;
	box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
	outline: none;
}

.filtro-busqueda i {
	position: absolute;
	right: 20px;
	top: 50%;
	transform: translateY(-50%);
	color: #94a3b8;
	font-size: 18px;
}

.filtros-rapidos {
	display: flex;
	gap: 10px;
	flex-wrap: wrap;
	margin-top: 15px;
}

.filtro-chip {
	padding: 8px 16px;
	border-radius: 20px;
	border: 2px solid #e2e8f0;
	background: white;
	font-size: 13px;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	display: flex;
	align-items: center;
	gap: 8px;
}

.filtro-chip:hover {
	border-color: #667eea;
	background: #f0f4ff;
	transform: translateY(-2px);
}

.filtro-chip.active {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	border-color: #667eea;
}

/* Acordeones de módulos */
.modulos-container {
	padding: 30px;
	max-height: 600px;
	overflow-y: auto;
}

.modulo-card {
	margin-bottom: 20px;
	border-radius: 12px;
	overflow: hidden;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
	border: 2px solid #e2e8f0;
	transition: all 0.3s ease;
}

.modulo-card:hover {
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
	transform: translateY(-2px);
}

.modulo-header {
	padding: 20px 25px;
	background: linear-gradient(135deg, var(--modulo-color, #667eea) 0%, var(--modulo-color-dark, #764ba2) 100%);
	color: white;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: space-between;
	transition: all 0.3s ease;
}

.modulo-header:hover {
	opacity: 0.95;
}

.modulo-info {
	display: flex;
	align-items: center;
	gap: 15px;
	flex: 1;
}

.modulo-icon {
	width: 45px;
	height: 45px;
	border-radius: 10px;
	background: rgba(255, 255, 255, 0.2);
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 20px;
}

.modulo-detalles h3 {
	margin: 0 0 5px 0;
	font-size: 18px;
	font-weight: 700;
}

.modulo-detalles p {
	margin: 0;
	opacity: 0.9;
	font-size: 13px;
}

.modulo-badges {
	display: flex;
	align-items: center;
	gap: 15px;
}

.badge-contador {
	background: rgba(255, 255, 255, 0.25);
	padding: 8px 15px;
	border-radius: 20px;
	font-weight: 600;
	font-size: 14px;
	backdrop-filter: blur(10px);
}

.badge-seleccionadas {
	background: rgba(16, 185, 129, 0.3);
	color: white;
	font-weight: 700;
}

.modulo-toggle {
	font-size: 24px;
	transition: transform 0.3s ease;
}

.modulo-toggle.active {
	transform: rotate(180deg);
}

/* Tabla de páginas dentro del módulo */
.modulo-body {
	background: white;
	max-height: 0;
	overflow: hidden;
	transition: max-height 0.4s ease;
}

.modulo-body.active {
	max-height: 1000px;
	padding: 20px 25px;
	border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.paginas-tabla {
	width: 100%;
	border-collapse: separate;
	border-spacing: 0 8px;
}

.paginas-tabla thead th {
	background: #f8f9fa;
	color: #64748b;
	font-weight: 600;
	font-size: 13px;
	padding: 12px 15px;
	text-align: left;
	border: none;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.paginas-tabla tbody tr {
	background: #f8f9fa;
	transition: all 0.2s ease;
}

.paginas-tabla tbody tr:hover {
	background: #e9ecef;
	transform: translateX(5px);
}

.paginas-tabla tbody td {
	padding: 15px;
	border: none;
}

.paginas-tabla tbody tr td:first-child {
	border-radius: 8px 0 0 8px;
}

.paginas-tabla tbody tr td:last-child {
	border-radius: 0 8px 8px 0;
}

/* Switch moderno */
.switch-container {
	display: flex;
	align-items: center;
	gap: 10px;
}

.switchToggle {
	position: relative;
	display: inline-block;
	width: 50px;
	height: 26px;
}

.switchToggle input {
	opacity: 0;
	width: 0;
	height: 0;
}

.slider {
	position: absolute;
	cursor: pointer;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #cbd5e0;
	transition: .3s;
}

.slider:before {
	position: absolute;
	content: "";
	height: 18px;
	width: 18px;
	left: 4px;
	bottom: 4px;
	background-color: white;
	transition: .3s;
}

input:checked + .slider {
	background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

input:checked + .slider:before {
	transform: translateX(24px);
}

.slider.round {
	border-radius: 26px;
}

.slider.round:before {
	border-radius: 50%;
}

/* Botones de acción */
.acciones-footer {
	padding: 25px 30px;
	background: #f8f9fa;
	border-top: 2px solid #e2e8f0;
	display: flex;
	justify-content: space-between;
	align-items: center;
	gap: 15px;
	flex-wrap: wrap;
}

.btn-guardar {
	background: linear-gradient(135deg, #10b981 0%, #059669 100%);
	color: white;
	border: none;
	padding: 14px 35px;
	border-radius: 10px;
	font-weight: 600;
	font-size: 16px;
	box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
	transition: all 0.3s ease;
	display: inline-flex;
	align-items: center;
	gap: 10px;
}

.btn-guardar:hover {
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
	color: white;
}

.btn-cancelar {
	background: #e2e8f0;
	color: #64748b;
	border: none;
	padding: 14px 30px;
	border-radius: 10px;
	font-weight: 600;
	font-size: 16px;
	transition: all 0.3s ease;
	text-decoration: none;
	display: inline-flex;
	align-items: center;
	gap: 10px;
}

.btn-cancelar:hover {
	background: #cbd5e0;
	color: #475569;
	text-decoration: none;
}

/* IDs y palabras clave */
.pagina-id {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 4px 10px;
	border-radius: 6px;
	font-weight: 600;
	font-size: 12px;
	display: inline-block;
}

.palabras-clave {
	color: #64748b;
	font-size: 13px;
	font-style: italic;
}

/* Scrollbar personalizado */
.modulos-container::-webkit-scrollbar {
	width: 8px;
}

.modulos-container::-webkit-scrollbar-track {
	background: #f1f1f1;
	border-radius: 10px;
}

.modulos-container::-webkit-scrollbar-thumb {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	border-radius: 10px;
}

.modulos-container::-webkit-scrollbar-thumb:hover {
	background: #667eea;
}

/* Responsive */
@media (max-width: 768px) {
	.header-subroles {
		padding: 20px;
	}
	
	.header-subroles h2 {
		font-size: 22px;
	}
	
	.resumen-seleccion {
		flex-direction: column;
		align-items: flex-start;
	}
	
	.modulo-header {
		flex-direction: column;
		align-items: flex-start;
		gap: 15px;
	}
	
	.modulo-badges {
		width: 100%;
		justify-content: space-between;
	}
	
	.acciones-footer {
		flex-direction: column-reverse;
	}
	
	.btn-guardar, .btn-cancelar {
		width: 100%;
		justify-content: center;
	}
}
</style>

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
	<?php include("../compartido/encabezado.php"); ?>

	<?php include("../compartido/panel-color.php"); ?>
	<!-- start page container -->
	<div class="page-container">
		<?php include("../compartido/menu.php"); ?>
		<!-- start page content -->
		<div class="page-content-wrapper">
			<div class="page-content">
				<div class="page-bar">
					<?php include("../compartido/texto-manual-ayuda.php");?>
					<div class="page-title-breadcrumb">
						<div class=" pull-left">
							<div class="page-title"><?=$frases[369][$datosUsuarioActual['uss_idioma']];?> Sub Rol</div>
						</div>
						<ol class="breadcrumb page-breadcrumb pull-right">
							<li><a class="parent-item" href="javascript:void(0);" name="sub-roles.php" onClick="deseaRegresar(this)">Sub Roles</a>&nbsp;<i class="fa fa-angle-right"></i></li>
							<li class="active"><?=$frases[369][$datosUsuarioActual['uss_idioma']];?>  Sub Rol</li>
						</ol>
					</div>
				</div>
				<?php include("../../config-general/mensajes-informativos.php"); ?>
				
				<form action="sub-roles-guardar.php" method="post" enctype="multipart/form-data">
					<!-- Header moderno -->
					<div class="header-subroles">
						<h2>
							<i class="fa fa-user-secret"></i>
							Crear Nuevo Sub Rol
						</h2>
						<p>Define un nombre, asigna usuarios y selecciona los permisos (páginas) organizados por módulos</p>
					</div>
					
					<div class="container-subroles">
						<!-- Sección de información básica -->
						<div class="seccion-basica">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group-modern">
										<label>
											<i class="fa fa-tag"></i>
											<?=$frases[187][$datosUsuarioActual['uss_idioma']];?> del sub rol
										</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fa fa-shield"></i></span>
											</div>
											<input type="text" class="form-control" name="nombre" placeholder="Ej: Coordinador Académico" required>
										</div>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group-modern">
										<label>
											<i class="fa fa-users"></i>
											Usuarios asignados
										</label>
										<select class="form-control select2" name="directivos[]" multiple>
											<option value="">Seleccione uno o más usuarios</option>
											<?php 
											$consultaDirectivos = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=".TIPO_DIRECTIVO." AND uss_bloqueado=0");
											while($directivos = mysqli_fetch_array($consultaDirectivos, MYSQLI_BOTH)){
											?>
												<option value="<?=$directivos["uss_id"];?>"><?=UsuariosPadre::nombreCompletoDelUsuario($directivos);?></option>
											<?php
												}
											?>
										</select>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Resumen de selección -->
						<div class="resumen-seleccion">
							<div class="resumen-item">
								<div class="resumen-icon">
									<i class="fa fa-file-text-o"></i>
								</div>
								<div class="resumen-texto">
									<h4>Páginas Seleccionadas</h4>
									<span class="numero" id="cantSeleccionadas">0</span>
									<span style="color: #78350f; font-weight: 600;"> / <?= $totalPaginas ?></span>
								</div>
							</div>
							
							<div class="resumen-item">
								<div class="resumen-icon">
									<i class="fa fa-cubes"></i>
								</div>
								<div class="resumen-texto">
									<h4>Módulos Activos</h4>
									<span class="numero" id="modulosActivos">0</span>
									<span style="color: #78350f; font-weight: 600;"> / <?= count($paginasPorModulo) ?></span>
								</div>
							</div>
						</div>
						
						<!-- Filtros -->
						<div class="filtros-container">
							<div class="filtro-busqueda">
								<input type="text" id="buscadorPaginas" placeholder="🔍 Buscar por nombre de página, módulo o palabra clave...">
								<i class="fa fa-search"></i>
							</div>
							
							<div class="filtros-rapidos">
								<button type="button" class="filtro-chip" data-action="toggle-all">
									<i class="fa fa-check-square-o"></i> Seleccionar Todas
								</button>
								<button type="button" class="filtro-chip" data-action="deselect-all">
									<i class="fa fa-square-o"></i> Deseleccionar Todas
								</button>
								<button type="button" class="filtro-chip" data-action="expand-all">
									<i class="fa fa-angle-down"></i> Expandir Todos
								</button>
								<button type="button" class="filtro-chip" data-action="collapse-all">
									<i class="fa fa-angle-up"></i> Contraer Todos
								</button>
							</div>
						</div>
						
						<!-- Módulos con acordeones -->
						<div class="modulos-container">
							<?php 
							$colorIndex = 0;
							$colores = [
								['color' => '#667eea', 'dark' => '#764ba2'],
								['color' => '#10b981', 'dark' => '#059669'],
								['color' => '#f59e0b', 'dark' => '#d97706'],
								['color' => '#ef4444', 'dark' => '#dc2626'],
								['color' => '#06b6d4', 'dark' => '#0891b2'],
								['color' => '#8b5cf6', 'dark' => '#7c3aed'],
								['color' => '#ec4899', 'dark' => '#db2777'],
								['color' => '#14b8a6', 'dark' => '#0d9488']
							];
							
							foreach ($paginasPorModulo as $moduloId => $modulo) {
								$colorModulo = $colores[$colorIndex % count($colores)];
								$colorIndex++;
								$cantidadPaginas = count($modulo['paginas']);
							?>
			<div class="modulo-card" data-modulo-id="<?= $moduloId ?>">
				<div class="modulo-header modulo-header-clickable" 
					 style="--modulo-color: <?= $colorModulo['color'] ?>; --modulo-color-dark: <?= $colorModulo['dark'] ?>"
					 data-modulo-id="<?= $moduloId ?>">
									<div class="modulo-info">
										<div class="modulo-icon">
											<i class="fa fa-cube"></i>
										</div>
										<div class="modulo-detalles">
											<h3><?= $modulo['nombre'] ?></h3>
											<p><?= $cantidadPaginas ?> página<?= $cantidadPaginas != 1 ? 's' : '' ?> disponible<?= $cantidadPaginas != 1 ? 's' : '' ?></p>
										</div>
									</div>
									<div class="modulo-badges">
										<span class="badge-contador badge-seleccionadas" id="badge-modulo-<?= $moduloId ?>">
											<span class="cant-seleccionadas-modulo">0</span> / <?= $cantidadPaginas ?>
										</span>
										<i class="fa fa-chevron-down modulo-toggle" id="toggle-<?= $moduloId ?>"></i>
									</div>
								</div>
								
								<div class="modulo-body" id="body-<?= $moduloId ?>">
									<table class="paginas-tabla">
										<thead>
											<tr>
												<th style="width: 80px;">
													<div class="switch-container">
														<label class="switchToggle">
															<input type="checkbox" class="toggle-modulo" data-modulo="<?= $moduloId ?>">
															<span class="slider green round"></span>
														</label>
													</div>
												</th>
												<th style="width: 80px;">ID</th>
												<th>Página</th>
												<th>Palabras Clave</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($modulo['paginas'] as $pagina) { ?>
											<tr class="pagina-row" 
												data-modulo="<?= $moduloId ?>" 
												data-pagina-id="<?= $pagina['pagp_id'] ?>"
												data-pagina-nombre="<?= strtolower($pagina['pagp_pagina']) ?>"
												data-modulo-nombre="<?= strtolower($modulo['nombre']) ?>"
												data-palabras-clave="<?= strtolower($pagina['pagp_palabras_claves']) ?>">
												<td>
													<div class="switch-container">
														<label class="switchToggle">
															<input type="checkbox" 
																   class="check pagina-check" 
																   data-id-rol="" 
																   data-modulo="<?= $moduloId ?>"
																   id="<?= $pagina['pagp_paginas_dependencia']; ?>" 
																   onchange="validarPaginasDependencia(this)" 
																   value="<?= $pagina['pagp_id']; ?>">
															<span class="slider green round"></span>
														</label>
													</div>
												</td>
												<td>
													<span class="pagina-id"><?= $pagina['pagp_id']; ?></span>
												</td>
												<td style="font-weight: 600; color: #2d3748;">
													<?= $pagina['pagp_pagina']; ?>
												</td>
												<td>
													<span class="palabras-clave"><?= $pagina['pagp_palabras_claves'] ?: '—'; ?></span>
												</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							<?php } ?>
						</div>
						
						<!-- Footer con botones -->
						<div class="acciones-footer">
							<a href="javascript:void(0);" name="sub-roles.php" onClick="deseaRegresar(this)" class="btn-cancelar">
								<i class="fa fa-times"></i>
								Cancelar
							</a>
							
							<button type="submit" class="btn-guardar">
								<i class="fa fa-save"></i>
								<?=$frases[41][$datosUsuarioActual['uss_idioma']];?>
							</button>
						</div>
					</div>
					
					<!-- Campo oculto con las páginas seleccionadas -->
					<select id="paginasSeleccionadas" style="display: none;" name="paginas[]" multiple></select>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- end page container -->
<?php include("../compartido/footer.php"); ?>
<script src="../js/Subroles.js"></script>
<script>
(function() {
	// Variable global para almacenar los elementos seleccionados
	if (typeof window.paginasSeleccionadas === 'undefined') {
		window.paginasSeleccionadas = new Set();
	}
	if (typeof window.modulosExpandidos === 'undefined') {
		window.modulosExpandidos = new Set();
	}

	// ==========================================
	// TOGGLE DE MÓDULOS (función global)
	// ==========================================
	window.toggleModulo = function(moduloId) {
		const body = document.getElementById('body-' + moduloId);
		const toggle = document.getElementById('toggle-' + moduloId);
		
		if (!body || !toggle) {
			console.log('Elementos no encontrados para módulo:', moduloId);
			return;
		}
		
		if (window.modulosExpandidos.has(moduloId)) {
			// Contraer
			body.classList.remove('active');
			toggle.classList.remove('active');
			window.modulosExpandidos.delete(moduloId);
		} else {
			// Expandir
			body.classList.add('active');
			toggle.classList.add('active');
			window.modulosExpandidos.add(moduloId);
		}
	};

	// ==========================================
	// ACTUALIZAR CONTADORES
	// ==========================================
	function actualizarContadores() {
		// Contador global
		const totalSeleccionadas = window.paginasSeleccionadas.size;
		const elemContador = document.getElementById('cantSeleccionadas');
		if (elemContador) {
			elemContador.textContent = totalSeleccionadas;
		}
		
		// Contadores por módulo
		const modulos = {};
		document.querySelectorAll('.pagina-check:checked').forEach(function(checkbox) {
			const moduloId = checkbox.getAttribute('data-modulo');
			modulos[moduloId] = (modulos[moduloId] || 0) + 1;
		});
		
		// Actualizar badges de módulos
		document.querySelectorAll('[id^="badge-modulo-"]').forEach(function(badge) {
			const moduloId = badge.id.replace('badge-modulo-', '');
			const cant = modulos[moduloId] || 0;
			const span = badge.querySelector('.cant-seleccionadas-modulo');
			if (span) {
				span.textContent = cant;
			}
		});
		
		// Módulos activos (que tienen al menos 1 selección)
		const modulosActivos = Object.keys(modulos).length;
		const elemModulos = document.getElementById('modulosActivos');
		if (elemModulos) {
			elemModulos.textContent = modulosActivos;
		}
		
		// Actualizar select oculto
		const select = document.getElementById('paginasSeleccionadas');
		if (select) {
			select.innerHTML = '';
			window.paginasSeleccionadas.forEach(function(pagina) {
				const option = document.createElement('option');
				option.value = pagina;
				option.selected = true;
				select.appendChild(option);
			});
		}
	}

	// ==========================================
	// MANEJO DE CHECKBOXES INDIVIDUALES
	// ==========================================
	function inicializarEventos() {
		console.log('Inicializando eventos de sub-roles agregar...');
		
		// ==========================================
		// CLICK EN HEADERS DE MÓDULOS PARA EXPANDIR/CONTRAER
		// ==========================================
		$('.modulo-header-clickable').off('click');
		$('.modulo-header-clickable').on('click', function() {
			const moduloId = $(this).attr('data-modulo-id');
			window.toggleModulo(moduloId);
		});
		
		// Escuchar cambios en checkboxes individuales
		$(document).off('change', '.pagina-check');
		$(document).on('change', '.pagina-check', function() {
			const page = this.value;
			const idRol = $(this).data('idRol') || '';
			
			if (this.checked) {
				agregarPagina(page, idRol);
				window.paginasSeleccionadas.add(page);
			} else {
				eliminarPagina(page, idRol);
				window.paginasSeleccionadas.delete(page);
			}
			
			actualizarContadores();
		});
		
		// Toggle de módulo completo
		$(document).off('change', '.toggle-modulo');
		$(document).on('change', '.toggle-modulo', function() {
			const moduloId = $(this).data('modulo');
			const checkboxes = $('.pagina-check[data-modulo="' + moduloId + '"]');
			const isChecked = this.checked;
			
			checkboxes.each(function() {
				this.checked = isChecked;
				const page = this.value;
				const idRol = $(this).data('idRol') || '';
				
				if (isChecked) {
					agregarPagina(page, idRol);
					window.paginasSeleccionadas.add(page);
				} else {
					eliminarPagina(page, idRol);
					window.paginasSeleccionadas.delete(page);
				}
			});
			
			actualizarContadores();
		});
		
		// ==========================================
		// FILTROS RÁPIDOS
		// ==========================================
		$('.filtro-chip').off('click');
		$('.filtro-chip').on('click', function() {
			const action = $(this).data('action');
			
			console.log('Filtro rápido:', action);
			
			switch(action) {
				case 'toggle-all':
					$('.pagina-check').each(function() {
						if (!this.checked) {
							this.checked = true;
							const page = this.value;
							const idRol = $(this).data('idRol') || '';
							agregarPagina(page, idRol);
							window.paginasSeleccionadas.add(page);
						}
					});
					actualizarContadores();
					break;
					
				case 'deselect-all':
					$('.pagina-check').each(function() {
						if (this.checked) {
							this.checked = false;
							const page = this.value;
							const idRol = $(this).data('idRol') || '';
							eliminarPagina(page, idRol);
							window.paginasSeleccionadas.delete(page);
						}
					});
					$('.toggle-modulo').prop('checked', false);
					actualizarContadores();
					break;
					
				case 'expand-all':
					$('.modulo-card').each(function() {
						const moduloId = $(this).attr('data-modulo-id');
						if (!window.modulosExpandidos.has(moduloId)) {
							window.toggleModulo(moduloId);
						}
					});
					break;
					
				case 'collapse-all':
					$('.modulo-card').each(function() {
						const moduloId = $(this).attr('data-modulo-id');
						if (window.modulosExpandidos.has(moduloId)) {
							window.toggleModulo(moduloId);
						}
					});
					break;
			}
		});
	}
	
	// ==========================================
	// BUSCADOR DE PÁGINAS
	// ==========================================
	function inicializarBuscador() {
		const buscador = document.getElementById('buscadorPaginas');
		if (buscador) {
			console.log('Buscador encontrado');
			
			// Remover listener anterior
			$(buscador).off('input');
			
			$(buscador).on('input', function() {
				const busqueda = this.value.toLowerCase().trim();
				
				document.querySelectorAll('.modulo-card').forEach(function(modulo) {
					const filas = modulo.querySelectorAll('.pagina-row');
					let hayCoincidencias = false;
					
					filas.forEach(function(fila) {
						// Usar getAttribute para asegurar compatibilidad
						const paginaNombre = (fila.getAttribute('data-pagina-nombre') || '').toLowerCase();
						const moduloNombre = (fila.getAttribute('data-modulo-nombre') || '').toLowerCase();
						const palabrasClave = (fila.getAttribute('data-palabras-clave') || '').toLowerCase();
						
						const coincide = busqueda === '' || 
										 paginaNombre.includes(busqueda) || 
										 moduloNombre.includes(busqueda) || 
										 palabrasClave.includes(busqueda);
						
						if (coincide) {
							fila.style.display = '';
							hayCoincidencias = true;
						} else {
							fila.style.display = 'none';
						}
					});
					
					// Mostrar u ocultar módulo según coincidencias
					if (hayCoincidencias || busqueda === '') {
						modulo.style.display = '';
					} else {
						modulo.style.display = 'none';
					}
				});
			});
		} else {
			console.warn('Buscador NO encontrado');
		}
	}
	
	// Ejecutar inicialización
	$(document).ready(function() {
		inicializarEventos();
		inicializarBuscador();
	});
})();
</script>

<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
<!-- end js include path -->

</body>

</html>
