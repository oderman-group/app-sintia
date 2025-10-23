<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0126'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php");
require_once '../class/Estudiantes.php';
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
$Plataforma = new Plataforma;

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
	$disabledPermiso = "disabled";
}
?>
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">

<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css"
	rel="stylesheet" type="text/css" />
<!-- select2 -->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<style>
/* ========================================
   ESTILOS PARA FILAS SELECCIONADAS
   ======================================== */

/* Fila seleccionada - Consistente con página de estudiantes */
.usuario-row-selected {
	background-color: #e3f2fd !important;
	transition: background-color 0.3s ease;
}

.usuario-row-selected:hover {
	background-color: #bbdefb !important;
}

/* Checkbox de selección */
.usuario-checkbox {
	cursor: pointer;
	width: 18px;
	height: 18px;
	margin: 0;
}

/* Checkbox "Seleccionar todos" */
#selectAllUsuarios {
	cursor: pointer;
	width: 18px;
	height: 18px;
	margin: 0;
}

/* ========================================
   ESTILOS EXISTENTES
   ======================================== */

.expand-btn {
	transition: all 0.3s ease;
	padding: 4px 8px;
	font-size: 14px;
}

.expand-btn:hover {
	text-decoration: none;
	transform: scale(1.2);
}

.expand-btn:focus {
	outline: none;
	box-shadow: none;
}

.expandable-content {
	border-radius: 8px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.1);
	margin: 10px 0;
}

.info-item {
	padding: 5px 0;
	border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
	border-bottom: none;
}

.info-item strong {
	font-size: 0.9em;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.expandable-content .badge {
	font-size: 0.8em;
	padding: 4px 8px;
}

.expandable-content img {
	border: 3px solid #fff;
	box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.expandable-content .alert {
	border-radius: 6px;
	margin-bottom: 0;
}
</style>
</head>
<!-- END HEAD -->
<?php
	include("../compartido/body.php");
	include("usuarios-bloquear-modal.php");
?>
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
					<div class="page-title-breadcrumb">
						<div class=" pull-left">
							<div class="page-title"><?= $frases[75][$datosUsuarioActual['uss_idioma']]; ?></div>
							<?php include("../compartido/texto-manual-ayuda.php"); ?>
						</div>
					</div>
				</div>
				
				<!-- Descripción de la página -->
				<div class="row mb-3">
					<div class="col-md-12">
						<p class="text-muted" style="font-size: 14px; line-height: 1.6;">
							<i class="fa fa-info-circle text-info"></i> 
							Administra los usuarios de la plataforma. Aquí puedes crear, editar y gestionar docentes, directivos, estudiantes y acudientes. 
							Utiliza el buscador para encontrar usuarios por nombre o datos de acceso. Expande cada registro para ver información detallada del usuario.
						</p>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="row">

							<div class="col-md-12">
								<?php include("../../config-general/mensajes-informativos.php");
								// require "../../config-general/google-translate-php-master/vendor/autoload.php";
								// use Stichoza\GoogleTranslate\GoogleTranslate;
								// $tr = new GoogleTranslate();
								// $tr->setSource('es'); // Traducir del inglés
								// $tr->setTarget('en'); // Al español
								// echo $tr->translate('Más Acciones'); // Hola Mundo
								
								?>

								<?php 
								// Barra superior antigua - removida
								// include("includes/barra-superior-usuarios.php");
								?>

								<div class="card card-topline-purple">
									<div class="card-head">
										<header><?= $frases[75][$datosUsuarioActual['uss_idioma']]; ?></header>
										<div class="tools">
											<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
											<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
											<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
										</div>
									</div>
									<div class="card-body">

										<!-- Barra de herramientas superior -->
										<div class="row mb-3">
											<div class="col-sm-12">
												<div class="d-flex justify-content-between align-items-center">
													<!-- Botones principales -->
													<div class="btn-group">
														<?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0123'])) { ?>
															<button type="button" class="btn deepPink-bgcolor" data-toggle="modal" data-target="#modalAgregarUsuario">
																<i class="fa fa-plus"></i> <?=__('usuarios.agregar_nuevo');?>
															</button>
														<?php } ?>
														
														<!-- Más Acciones -->
														<?php if(Modulos::validarPermisoEdicion()){?>
															<div class="btn-group" role="group">
																<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
																	<i class="fa fa-tools"></i> Más Acciones <span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Desea Bloquear a todos los estudiantes?','question','usuarios-bloquear.php?tipo=<?=base64_encode(4)?>')"><i class="fa fa-lock"></i> Bloquear Estudiantes</a></li>
																	<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Desea Desbloquear a todos los estudiantes?','question','usuarios-desbloquear.php?tipo=<?=base64_encode(4)?>')"><i class="fa fa-unlock"></i> Desbloquear Estudiantes</a></li>
																	<li class="divider"></li>
																	<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Desea Bloquear a todos los docentes?','question','usuarios-bloquear.php?tipo=<?=base64_encode(2)?>')"><i class="fa fa-lock"></i> Bloquear Docentes</a></li>
																	<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Desea Desbloquear a todos los docentes?','question','usuarios-desbloquear.php?tipo=<?=base64_encode(2)?>')"><i class="fa fa-unlock"></i> Desbloquear Docentes</a></li>
																	<?php if(Modulos::validarSubRol(['DT0125'])) {?>
																		<li class="divider"></li>
																		<li><a href="usuarios-importar-excel.php"><i class="fa fa-file-excel"></i> Importar Usuarios</a></li>
																	<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0144'])) {?>
																		<li><a href="usuarios-generar-clave-filtros.php"><i class="fa fa-key"></i> Generar Contraseña Masiva</a></li>
																	<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0201'])) {?>
																		<li class="divider"></li>
																		<li><a href="usuarios-anios.php"><i class="fa fa-calendar-alt"></i> Consultar Todos los Años</a></li>
																	<?php }?>
																</ul>
															</div>
														<?php }?>
													</div>
													
													<!-- Botón de filtros -->
													<button type="button" class="btn btn-outline-secondary" id="btnToggleFiltrosUsuarios">
														<i class="fa fa-filter"></i> Filtros Avanzados
													</button>
												</div>
											</div>
										</div>
										
										<!-- Panel de Filtros Colapsable -->
										<div class="card card-topline-purple mb-3" id="cardFiltrosUsuarios" style="display: none;">
											<div class="card-body">
												<h5 class="mb-3"><i class="fa fa-filter"></i> Filtros Avanzados</h5>
												
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label><i class="fa fa-user-tag"></i> Tipo de Usuario</label>
															<select id="filtro_usuarios_tipo" class="form-control select2-multiple-usuarios" multiple="multiple" style="width: 100%;">
																<?php
																try{
																	$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_perfiles");
																	while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
																?>
																	<option value="<?=$opcionesDatos['pes_id'];?>"><?=$opcionesDatos['pes_nombre'];?></option>
																<?php }
																} catch (Exception $e) {}
																?>
															</select>
														</div>
													</div>
													
													<div class="col-md-6">
														<div class="form-group">
															<label><i class="fa fa-toggle-on"></i> Estado</label>
															<select id="filtro_usuarios_estado" class="form-control select2-multiple-usuarios" multiple="multiple" style="width: 100%;">
																<option value="1">Activo</option>
																<option value="0">Inactivo</option>
															</select>
														</div>
													</div>
												</div>
												
												<div class="row">
													<div class="col-md-12 text-right">
														<button type="button" class="btn btn-secondary" id="btnLimpiarFiltrosUsuarios">
															<i class="fa fa-eraser"></i> Limpiar Filtros
														</button>
													</div>
												</div>
											</div>
										</div>

										<span id="respuestaGuardar"></span>

										<div class="table-scrollable">
											<table id="example1" class="display" style="width:100%;">
												<thead>
													<tr>
														<th></th>
														<th>#</th>
														<th>
															<div class="col-sm-12">
																<ul class="navbar-nav mr-auto">
																	<li class="nav-item dropdown">

																		<a class="nav-link dropdown-toggle"
																					href="javascript:void(0);"
																					id="navbarDropdown" role="button"
																					data-toggle="dropdown"
																					aria-haspopup="true"
																					aria-expanded="false"
																					style="color:<?= $Plataforma->colorUno; ?>;">
																					Seleccionados
																					<label id="lblCantSeleccionados" type="text" style="text-align: center;"></label>
																			<span class="fa fa-angle-down"></span>

																		</a>
																		<?php if (Modulos::validarPermisoEdicion()) { ?>
																			<div class="dropdown-menu" aria-labelledby="navbarDropdown">
																				<a class="dropdown-item"
																					href="javascript:void(0);"
																					onClick="actualizarBloqueo(true)">
																					Bloquear
																				</a>
																				<a class="dropdown-item"
																					href="javascript:void(0);"
																					onClick="actualizarBloqueo(false)">
																					Desbloquear
																				</a>
																			</div>
																		<?php } ?>
																	</li>
																</ul>
															</div>
															<input type="checkbox" id="selectAllUsuarios" title="Seleccionar todos" <?= $disabledPermiso; ?>>
														</th>
														<th>Bloq.</th>
														<th>ID</th>
														<th>Usuario (REP)</th>
														<th>Nombre</th>
														<th><?= $frases[53][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></th>
													</tr>
												</thead>
												<tbody>
												<script type="text/javascript">document.getElementById("overlay").style.display = "flex"</script>
													<?php
													$permisoHistorial = Modulos::validarSubRol(['DT0327']);
													$permisoPlantilla = Modulos::validarSubRol(['DT0239']);
													$tipo = empty($_GET['tipo']) ? "" : base64_decode($_GET['tipo']);
													$filtroLimite = '';
													$selectSql = [
														"uss_id",
														"uss_usuario",
														"uss_email",
														"uss_fecha_nacimiento",
														"uss_nombre",
														"uss_nombre2",
														"uss_foto",
														"uss_estado",
														"uss_apellido1",
														"uss_ultimo_ingreso",
														"uss_apellido2",
														"uss_tipo",
														"uss_permiso1",
														"pes_nombre",
														"uss_bloqueado",
														"uss_ultimo_ingreso",
														"uss_telefono",
														"uss_direccion",
														"uss_ocupacion",
														"uss_genero",
														"uss_fecha_registro",
														"uss_documento",
														"uss_tipo_documento",
														"uss_lugar_expedicion",
														"uss_intentos_fallidos",
														"ogen_genero.ogen_nombre as genero_nombre"
													];
													$tipos = empty($tipo) ? [TIPO_DEV,TIPO_DOCENTE,TIPO_DIRECTIVO,TIPO_CLIENTE,TIPO_PROVEEDOR] : [$tipo];
													$tipos = empty($tipo) ? [TIPO_DEV,TIPO_DOCENTE,TIPO_DIRECTIVO,TIPO_CLIENTE,TIPO_PROVEEDOR] : [$tipo];
													$lista = Usuarios::listar($selectSql, $tipos, "uss_id");
													$contReg = 1;
													
													foreach ($lista as $usuario) {
														$bgColor = '';
														if ($usuario['uss_bloqueado'] == 1)
															$bgColor = '#ff572238';

														$cheked = '';
														if ($usuario['uss_bloqueado'] == 1) {
															$cheked = 'checked';
														}

														$mostrarNumAcudidos = '';
														if (isset($usuario['cantidad_acudidos']) && $usuario['uss_tipo'] == TIPO_ACUDIENTE ) {
															$mostrarNumAcudidos = '<br><span style="font-size:9px; color:darkblue">(' . $usuario['cantidad_acudidos'] . ')  Acudidos)</span>';
														}

														$mostrarNumCargas = '';
														if (isset($usuario['cantidad_cargas'])  && $usuario['uss_tipo'] == TIPO_DOCENTE) {
															$numCarga         =  $usuario['cantidad_cargas'];
															$mostrarNumCargas = '<br><span style="font-size:9px; color:darkblue">(' . $usuario['cantidad_cargas'] . ' Cargas)</span>';
														}

														$tieneMatricula = '';
														$backGroundMatricula = '';
														if ($usuario['uss_tipo'] == TIPO_ESTUDIANTE) {
															$tieneMatricula = $usuario['mat_id_usuario'];
															if (empty($usuario['mat_id_usuario'])) {
																$backGroundMatricula = 'style="background-color:gold;" class="animate__animated animate__pulse animate__delay-2s" data-toggle="tooltip" data-placement="right" title="Este supuesto estudiante no cuenta con un registro en las matrículas."';
															}
														}

														$managerPrimary = '';
														if ($usuario['uss_permiso1'] == CODE_PRIMARY_MANAGER && $usuario['uss_tipo'] == TIPO_DIRECTIVO) {
															$managerPrimary = '<i class="fa fa-user-circle text-primary" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Director principal"></i> ';
														}

														$fotoUsuario = $usuariosClase->verificarFoto($usuario['uss_foto']);
														$estadoUsuario = !empty($usuario['uss_estado']) ? $opcionEstado[$usuario['uss_estado']] : '';
														?>
														<tr id="reg<?= $usuario['uss_id']; ?>"
															style="background-color:<?= $bgColor; ?>;">
															<td>
																<button class="btn btn-sm btn-link text-secondary expand-btn"
																	data-id="<?= $usuario['uss_id']; ?>"
																	data-foto="<?= $fotoUsuario; ?>"
																	data-nombre="<?= UsuariosPadre::nombreCompletoDelUsuario($usuario); ?>"
																	data-usuario="<?= $usuario['uss_usuario']; ?>"
																	data-email="<?= $usuario['uss_email'] ?: 'No registrado'; ?>"
																	data-fecha-nacimiento="<?= $usuario['uss_fecha_nacimiento'] ?: 'No registrada'; ?>"
																	data-tipo="<?= $usuario['pes_nombre']; ?>"
																	data-estado="<?= $estadoUsuario ?: 'No definido'; ?>"
																	data-ultimo-ingreso="<?= $usuario['uss_ultimo_ingreso'] ?: 'Nunca'; ?>"
																	data-bloqueado="<?= $usuario['uss_bloqueado'] ? 'Sí' : 'No'; ?>"
																	data-num-carga="<?= isset($numCarga) ? $numCarga : ''; ?>"
																	data-cantidad-acudidos="<?= isset($usuario['cantidad_acudidos']) ? $usuario['cantidad_acudidos'] : ''; ?>"
																	data-tiene-matricula="<?= $tieneMatricula ? 'Activa' : 'Sin matrícula registrada'; ?>"
																	data-tipo-usuario="<?= $usuario['uss_tipo']; ?>"
																	data-telefono="<?= $usuario['uss_telefono'] ?: 'No registrado'; ?>"
																	data-direccion="<?= $usuario['uss_direccion'] ?: 'No registrada'; ?>"
																	data-ocupacion="<?= $usuario['uss_ocupacion'] ?: 'No registrada'; ?>"
																	data-genero="<?= $usuario['genero_nombre'] ?: 'No especificado'; ?>"
																	data-fecha-registro="<?= $usuario['uss_fecha_registro'] ?: 'No registrada'; ?>"
																	data-documento="<?= $usuario['uss_documento'] ?: 'No registrado'; ?>"
																	data-tipo-documento="<?= $usuario['uss_tipo_documento'] ?: 'No especificado'; ?>"
																	data-lugar-expedicion="<?= $usuario['uss_lugar_expedicion'] ?: 'No especificado'; ?>"
																	data-intentos-fallidos="<?= $usuario['uss_intentos_fallidos'] ?: '0'; ?>"
																	title="Ver detalles">
																	<i class="fa fa-chevron-right"></i>
																</button>
															</td>
															<td><?= $contReg; ?></td>
															<td>
																<input type="checkbox" 
																	   class="usuario-checkbox" 
																	   value="<?= $usuario['uss_id']; ?>"
																	   id="<?= $usuario['uss_id']; ?>_select">
															</td>
															<td>
																<?php if (Modulos::validarPermisoEdicion() && ($usuario['uss_tipo'] != TIPO_DIRECTIVO || $usuario['uss_permiso1'] != CODE_PRIMARY_MANAGER)) { ?>
																	<div class="input-group spinner col-sm-10">
																		<label class="switchToggle">
																			<input type="checkbox"
																				id="<?= $usuario['uss_id']; ?>" name="bloqueado"
																				value="1" onChange="ajaxBloqueoDesbloqueo(this)"
																				<?= $cheked; ?> 		<?= $disabledPermiso; ?>>
																			<span class="slider red round"></span>
																		</label>
																	</div>
																<?php } ?>
															</td>
															<td><?= $usuario['uss_id']; ?></td>
															<td><?= $usuario['uss_usuario']; ?></td>
															<td><?= $managerPrimary; ?><?= UsuariosPadre::nombreCompletoDelUsuario($usuario); ?>
															</td>
															<td <?= $backGroundMatricula ??=null; ?>>
																<?= $usuario['pes_nombre'] . "" . $mostrarNumCargas . "" . $mostrarNumAcudidos; ?>
															</td>
															<td>
																<div class="btn-group">
																	<button type="button"
																		class="btn btn-primary">Acciones</button>
																	<button type="button"
																		class="btn btn-primary dropdown-toggle m-r-20"
																		data-toggle="dropdown">
																		<i class="fa fa-angle-down"></i>
																	</button>
																	<ul class="dropdown-menu" role="menu">
																	<?php if (Modulos::validarPermisoEdicion()) { ?>

																		<?php
																		if (($usuario['uss_tipo'] == TIPO_ESTUDIANTE && !empty($tieneMatricula)) || $usuario['uss_tipo'] != TIPO_ESTUDIANTE) {
																			if (Modulos::validarSubRol(['DT0124']) && ($usuario['uss_tipo'] != TIPO_DIRECTIVO || $usuario['uss_permiso1'] != CODE_PRIMARY_MANAGER)) {
																		?>
																				<li><a href="javascript:void(0);" class="btn-editar-usuario-modal" data-usuario-id="<?=$usuario['uss_id'];?>"><i class="fa fa-edit"></i> Edición rápida</a></li>
																				<li><a href="usuarios-editar.php?id=<?= base64_encode($usuario['uss_id']); ?>"><i class="fa fa-pencil"></i> Editar completa</a></li>
																		<?php }
																		}
																		?>


																		<?php
																		if (
																			($datosUsuarioActual['uss_tipo'] == TIPO_DEV && $usuario['uss_tipo'] != TIPO_DEV) ||
																			($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && $usuario['uss_tipo'] != TIPO_DEV && $usuario['uss_tipo'] != TIPO_DIRECTIVO && !isset($_SESSION['admin']) && !isset($_SESSION['devAdmin']))
																		) {
																			if ($usuario['uss_tipo'] == TIPO_ESTUDIANTE && !empty($tieneMatricula) || $usuario['uss_tipo'] != TIPO_ESTUDIANTE) {
																		?>
																				<li><a href="auto-login.php?user=<?= base64_encode($usuario['uss_id']); ?>&tipe=<?= base64_encode($usuario['uss_tipo']); ?>">Autologin</a></li>
																		<?php
																			}
																		}
																		?>

																		<?php if ($usuario['uss_tipo'] == TIPO_ACUDIENTE && Modulos::validarSubRol(['DT0137'])) { ?>
																			<li><a href="usuarios-acudidos.php?id=<?= base64_encode($usuario['uss_id']); ?>">Acudidos</a></li>
																		<?php } ?>

																		<?php if ((isset($numCarga) && $numCarga == 0 && $usuario['uss_tipo'] == TIPO_DOCENTE) || $usuario['uss_tipo'] == TIPO_ACUDIENTE || ($usuario['uss_tipo'] == TIPO_ESTUDIANTE && empty($tieneMatricula)) || $usuario['uss_tipo'] == TIPO_CLIENTE || $usuario['uss_tipo'] == TIPO_PROVEEDOR) { ?>
																			<li><a href="javascript:void(0);" title="<?= $objetoEnviar; ?>" name="usuarios-eliminar.php?id=<?= base64_encode($usuario['uss_id']); ?>" onClick="deseaEliminar(this)" id="<?= $usuario['uss_id']; ?>">Eliminar</a></li>
																		<?php } ?>
																	<?php } ?>

																	<?php if ($usuario['uss_tipo'] == TIPO_DOCENTE && $numCarga > 0 && $permisoPlantilla) { ?>
																		<li><a href="../compartido/planilla-docentes.php?docente=<?= base64_encode($usuario['uss_id']); ?>" target="_blank">Planillas de las cargas</a></li>
																	<?php } ?>

																	<?php if (($datosUsuarioActual['uss_tipo'] == TIPO_DEV && $usuario['uss_tipo'] != TIPO_DEV) ||
																			($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && $usuario['uss_tipo'] != TIPO_DEV && $usuario['uss_tipo'] != TIPO_DIRECTIVO) && $permisoHistorial) { ?>
																		<li><a href="../compartido/informe-historial-ingreso.php?id=<?= base64_encode($usuario['uss_id']); ?>" target="_blank">Historial de Ingreso</a></li>
																	<?php } ?>

																</ul>
																</div>
															</td>

														</tr>
														<?php $contReg++;
													}?>
													<script type="text/javascript">document.getElementById("overlay").style.display = "none";</script>
												</tbody>

											</table>

										</div>

									</div>
								</div>
							</div>




						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end page content -->
		<?php // include("../compartido/panel-configuracion.php");
		?>
	</div>
	<!-- end page container -->
	<?php include("../compartido/footer.php"); ?>
</div>
<script src="../js/Usuarios.js" ></script>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- data tables -->
<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
<script src="../../config-general/assets/js/pages/table/table_data.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!-- select2 -->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<!-- end js include path -->
<style>
    .sorting_1 {
		background-color: red !important;
    }

  </style>
<script>

	$(function () {
		$('[data-toggle="popover"]').popover();
	});

	$('.popover-dismiss').popover({
		trigger: 'focus'
	});

	// DataTable initialization with child rows
	$(document).ready(function() {
		if (!$.fn.DataTable.isDataTable('#example1')) {
			var table = $('#example1').DataTable({
				"columnDefs": [
					{
						"targets": 0,
						"orderable": false,
						"searchable": false
					}
				],
				"order": [[1, 'asc']]
			});
		} else {
			var table = $('#example1').DataTable();
		}

		// Expandable rows functionality using DataTable child rows
		$('#example1 tbody').on('click', '.expand-btn', function () {
			var tr = $(this).closest('tr');
			var row = table.row(tr);
			var button = $(this);
			var icon = button.find('i');

			// Get data from button attributes
			var foto = button.data('foto');
			var nombre = button.data('nombre');
			var usuario = button.data('usuario');
			var email = button.data('email');
			var fechaNacimiento = button.data('fecha-nacimiento');
			var tipo = button.data('tipo');
			var estado = button.data('estado');
			var ultimoIngreso = button.data('ultimo-ingreso');
			var bloqueado = button.data('bloqueado');
			var numCarga = button.data('num-carga');
			var cantidadAcudidos = button.data('cantidad-acudidos');
			var tieneMatricula = button.data('tiene-matricula');
			var tipoUsuario = button.data('tipo-usuario');
			var userId = button.data('id');
			var telefono = button.data('telefono');
			var direccion = button.data('direccion');
			var ocupacion = button.data('ocupacion');
			var genero = button.data('genero');
			var fechaRegistro = button.data('fecha-registro');
			var documento = button.data('documento');
			var tipoDocumento = button.data('tipo-documento');
			var lugarExpedicion = button.data('lugar-expedicion');
			var intentosFallidos = button.data('intentos-fallidos');

			if (row.child.isShown()) {
				// This row is already open - close it
				row.child.hide();
				icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
				button.removeClass('text-primary').addClass('text-secondary');
			} else {
				// Open this row
				row.child(formatDetails(foto, nombre, usuario, email, fechaNacimiento, tipo, estado, ultimoIngreso, bloqueado, numCarga, cantidadAcudidos, tieneMatricula, tipoUsuario, userId, telefono, direccion, ocupacion, genero, fechaRegistro, documento, tipoDocumento, lugarExpedicion, intentosFallidos)).show();
				icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
				button.removeClass('text-secondary').addClass('text-primary');
			}
		});
	});

	function formatDetails(foto, nombre, usuario, email, fechaNacimiento, tipo, estado, ultimoIngreso, bloqueado, numCarga, cantidadAcudidos, tieneMatricula, tipoUsuario, userId, telefono, direccion, ocupacion, genero, fechaRegistro, documento, tipoDocumento, lugarExpedicion, intentosFallidos) {
		var estadoBadgeClass = estado !== 'No definido' ? 'success' : 'warning';
		var bloqueadoBadgeClass = bloqueado === 'Sí' ? 'danger' : 'success';
		var matriculaAlertClass = tieneMatricula === 'Activa' ? 'success' : 'warning';
		var intentosBadgeClass = parseInt(intentosFallidos) > 3 ? 'danger' : (parseInt(intentosFallidos) > 0 ? 'warning' : 'success');

		var html = '<div class="expandable-content bg-light border">' +
			'<div class="row no-gutters">' +
				'<div class="col-md-3 text-center p-3">' +
					'<img src="' + foto + '" class="img-fluid rounded-circle shadow-sm" style="max-width: 120px; max-height: 120px;" alt="Foto de ' + nombre + '">' +
					'<h6 class="mt-2 text-primary font-weight-bold">' + nombre + '</h6>' +
					'<small class="text-muted">' + tipo + '</small>' +
				'</div>' +
				'<div class="col-md-9 p-3">' +
					'<div class="row">' +
						'<div class="col-md-6">' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">ID:</strong>' +
								'<span class="badge badge-secondary ml-2">#' + userId + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Usuario:</strong>' +
								'<span class="text-dark">@' + usuario + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Email:</strong>' +
								'<span class="text-dark">' + email + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Teléfono:</strong>' +
								'<span class="text-dark">' + telefono + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Documento:</strong>' +
								'<span class="text-dark">' + tipoDocumento + ' ' + documento + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Lugar Expedición:</strong>' +
								'<span class="text-dark">' + lugarExpedicion + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Fecha de Nacimiento:</strong>' +
								'<span class="text-dark">' + fechaNacimiento + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Género:</strong>' +
								'<span class="text-dark">' + genero + '</span>' +
							'</div>' +
						'</div>' +
						'<div class="col-md-6">' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Estado:</strong>' +
								'<span class="badge badge-' + estadoBadgeClass + '">' + estado + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Bloqueado:</strong>' +
								'<span class="badge badge-' + bloqueadoBadgeClass + '">' + bloqueado + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Último Ingreso:</strong>' +
								'<span class="text-dark">' + ultimoIngreso + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Fecha Registro:</strong>' +
								'<span class="text-dark">' + fechaRegistro + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Intentos Fallidos:</strong>' +
								'<span class="badge badge-' + intentosBadgeClass + '">' + intentosFallidos + '</span>' +
							'</div>' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Ocupación:</strong>' +
								'<span class="text-dark">' + ocupacion + '</span>' +
							'</div>' +
						'</div>' +
					'</div>' +
					'<div class="row mt-2">' +
						'<div class="col-12">' +
							'<div class="info-item mb-2">' +
								'<strong class="text-muted">Dirección:</strong>' +
								'<span class="text-dark">' + direccion + '</span>' +
							'</div>' +
						'</div>' +
					'</div>';

		if (tipoUsuario == 2 && numCarga) { // TIPO_DOCENTE
			html += '<div class="row mt-2">' +
				'<div class="col-12">' +
					'<div class="alert alert-info py-2">' +
						'<i class="fa fa-book mr-2"></i>' +
						'<strong>Cargas Académicas:</strong> ' + numCarga + ' asignadas' +
					'</div>' +
				'</div>' +
			'</div>';
		}

		if (tipoUsuario == 4 && cantidadAcudidos) { // TIPO_ACUDIENTE
			html += '<div class="row mt-2">' +
				'<div class="col-12">' +
					'<div class="alert alert-success py-2">' +
						'<i class="fa fa-users mr-2"></i>' +
						'<strong>Estudiantes a Cargo:</strong> ' + cantidadAcudidos + ' estudiantes' +
					'</div>' +
				'</div>' +
			'</div>';
		}

		if (tipoUsuario == 3) { // TIPO_ESTUDIANTE
			html += '<div class="row mt-2">' +
				'<div class="col-12">' +
					'<div class="alert alert-' + matriculaAlertClass + ' py-2">' +
						'<i class="fa fa-graduation-cap mr-2"></i>' +
						'<strong>Matrícula:</strong> ' + tieneMatricula +
					'</div>' +
				'</div>' +
			'</div>';
		}

		html += '</div></div></div>';

		return html;
	}

	function actualizarBloqueo(bloqueo){
		console.log(bloqueo);
		let selecionados=getSelecionados('example1','selecionado','lblCantSeleccionados');
		if(selecionados.length>0){
			sweetConfirmacion(
					'Alerta!',
					'Desea '+(bloqueo?'Bloquear':'Desbloquear')+' a todos los usuarios seleccionados?',
					'question',
					'usuarios-bloquear.php',
					true,
					null,
					'POST',
					{ 
					  bloquear: bloqueo, 
					  usuarios:getSelecionados('example1','selecionado','lblCantSeleccionados'),
				      tipo:'<?= $_GET["tipo"] ??=null ?>'
					},
					'marcarbloqueados'
		           );
		}else{
			Swal.fire(
						{
						title: "No tiene datos selecionado!",
						icon: "question",
						draggable: true
						}
					);
		}
		
	}

	function marcarbloqueados(result,data,respuetaSweet) {
		let  resultado = result["data"];
		const table = $('#example1').DataTable();
		const rows = table.rows().nodes(); // Obtén todas las filas
		if(resultado["ok"]){
			let usuarios = data["usuarios"];
			let bloquear = data["bloquear"];
    		
			usuarios.forEach(element => {				
				const selectedCheckboxes = table.rows().nodes().to$().find('input[id="'+element+'"]');
    			selectedCheckboxes.prop('checked', bloquear); 
				const trElement = selectedCheckboxes.closest('tr');
				trElement.css('background-color', '#ff572238'); // Cambia el color del <tr>
				
			});
			$.toast({
					heading: 'Acción realizada',
					text: resultado["msg"],
					position: 'bottom-right',
					showHideTransition: 'slide',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 5000,
					stack: 6
					});
		};
		console.log(data);

	};
</script>

<!-- Modal para edición rápida de usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-edit"></i> Edición Rápida de Usuario</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="formEditarUsuario" action="usuarios-update.php" method="post">
				<div class="modal-body">
					<div id="usuarioLoader" class="text-center" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-3x"></i>
						<p>Cargando datos...</p>
					</div>
					
					<div id="usuarioFormulario" style="display:none;">
						<input type="hidden" id="edit_idR" name="idR">
						<input type="hidden" id="edit_tipoUsuario" name="tipoUsuario">
						
						<div class="form-group">
							<label>Usuario</label>
							<input type="text" class="form-control" id="edit_usuario" name="usuario" readonly>
						</div>
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Primer Nombre <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="edit_nombre" name="nombre" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Segundo Nombre</label>
									<input type="text" class="form-control" id="edit_nombre2" name="nombre2">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Primer Apellido <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="edit_apellido1" name="apellido1" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Segundo Apellido</label>
									<input type="text" class="form-control" id="edit_apellido2" name="apellido2">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label>Email</label>
							<input type="email" class="form-control" id="edit_email" name="email">
						</div>
						
						<div class="form-group">
							<label>Celular</label>
							<input type="text" class="form-control" id="edit_celular" name="celular">
						</div>
						
						<!-- Campos adicionales requeridos con valores por defecto -->
						<input type="hidden" name="documento" id="edit_documento">
						<input type="hidden" name="tipoD" value="1">
						<input type="hidden" name="genero" id="edit_genero">
						<input type="hidden" name="lExpedicion" value="">
						<input type="hidden" name="direccion" id="edit_direccion">
						<input type="hidden" name="telefono" id="edit_telefono">
						<input type="hidden" name="ocupacion" value="">
						<input type="hidden" name="intentosFallidos" value="0">
						<input type="hidden" name="cambiarClave" value="0">
					</div>
					
					<div id="usuarioError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i> <span id="errorMensajeUsuario"></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-save"></i> Guardar Cambios
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal para Agregar Usuario Completo -->
<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
				<h4 class="modal-title"><i class="fa fa-user-plus"></i> Agregar Nuevo Usuario</h4>
				<button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
			</div>
			<form id="formAgregarUsuario" action="usuarios-guardar.php" method="post">
				<div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
					
					<!-- Credenciales de acceso -->
					<h5 class="mb-3"><i class="fa fa-key"></i> Credenciales de Acceso</h5>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Tipo de Usuario <span class="text-danger">*</span></label>
								<?php
								try{
									$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_perfiles");
								} catch (Exception $e) {
									include("../compartido/error-catch-to-report.php");
								}
								?>
								<select class="form-control" name="tipoUsuario" id="modal_tipoUsuario" required>
									<option value="">Seleccione una opción</option>
									<?php
									while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
										if(($opcionesDatos['pes_id'] == TIPO_DEV || $opcionesDatos['pes_id'] == TIPO_ESTUDIANTE ) 
										&& $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
											continue;
										}
									?>
									<option value="<?=$opcionesDatos['pes_id'];?>"><?=$opcionesDatos['pes_nombre'];?></option>
									<?php }?>
								</select>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label>Usuario de Acceso <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="usuario" id="modal_usuario" pattern="[A-Za-z0-9]+" required>
								<small id="modal_validacion_usuario" class="form-text"></small>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Contraseña <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="clave" value="<?=CLAVE_SUGERIDA;?>" required>
								<small class="form-text text-muted">Mínimo 8 caracteres, máximo 20</small>
							</div>
						</div>
					</div>
					
					<hr>
					
					<!-- Datos personales -->
					<h5 class="mb-3"><i class="fa fa-id-card"></i> Datos Personales</h5>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Tipo de Documento</label>
								<?php
								try{
									$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=1");
								} catch (Exception $e) {
									include("../compartido/error-catch-to-report.php");
								}
								?>
								<select class="form-control" name="tipoD">
									<option value="">Seleccione una opción</option>
									<?php while($o = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){?>
									<option value="<?=$o['ogen_id'];?>"><?=$o['ogen_nombre'];?></option>
									<?php }?>
								</select>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label>Número de Documento</label>
								<input type="text" class="form-control" name="documento" id="modal_documento">
								<small id="modal_validacion_documento" class="form-text"></small>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Primer Nombre <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="nombre" pattern="^[A-Za-zñÑ]+$" required>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label>Segundo Nombre</label>
								<input type="text" class="form-control" name="nombre2">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Primer Apellido</label>
								<input type="text" class="form-control" name="apellido1" pattern="^[A-Za-zñÑ]+$">
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label>Segundo Apellido</label>
								<input type="text" class="form-control" name="apellido2">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Email</label>
								<input type="email" class="form-control" name="email">
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label>Celular</label>
								<input type="text" class="form-control" name="celular">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Género <span class="text-danger">*</span></label>
								<?php
								try{
									$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
								} catch (Exception $e) {
									include("../compartido/error-catch-to-report.php");
								}
								?>
								<select class="form-control" name="genero" required>
									<option value="">Seleccione una opción</option>
									<?php while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){?>
									<option value="<?=$opcionesDatos['ogen_id'];?>"><?=$opcionesDatos['ogen_nombre'];?></option>
									<?php }?>
								</select>
							</div>
						</div>
					</div>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-primary" id="btnModalGuardarUsuario">
						<i class="fa fa-save"></i> Guardar Usuario
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$(document).on('click', '.btn-editar-usuario-modal', function() {
		var usuarioId = $(this).data('usuario-id');
		
		$('#usuarioLoader').show();
		$('#usuarioFormulario').hide();
		$('#usuarioError').hide();
		$('#modalEditarUsuario').modal('show');
		
		$.ajax({
			url: 'ajax-obtener-datos-usuario.php',
			type: 'POST',
			data: { usuario_id: usuarioId },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var usuario = response.usuario;
					$('#edit_idR').val(usuario.uss_id);
					$('#edit_tipoUsuario').val(usuario.uss_tipo);
					$('#edit_usuario').val(usuario.uss_usuario);
					$('#edit_nombre').val(usuario.uss_nombre || '');
					$('#edit_nombre2').val(usuario.uss_nombre2 || '');
					$('#edit_apellido1').val(usuario.uss_apellido1 || '');
					$('#edit_apellido2').val(usuario.uss_apellido2 || '');
					$('#edit_email').val(usuario.uss_email || '');
					$('#edit_celular').val(usuario.uss_celular || '');
					$('#edit_documento').val(usuario.uss_documento || '');
					$('#edit_genero').val(usuario.uss_genero || '');
					$('#edit_direccion').val(usuario.uss_direccion || '');
					$('#edit_telefono').val(usuario.uss_telefono || '');
					
					$('#usuarioLoader').hide();
					$('#usuarioFormulario').show();
				} else {
					$('#usuarioLoader').hide();
					$('#errorMensajeUsuario').text(response.message);
					$('#usuarioError').show();
				}
			},
			error: function() {
				$('#usuarioLoader').hide();
				$('#errorMensajeUsuario').text('Error de conexión');
				$('#usuarioError').show();
			}
		});
	});
	
	$('#formEditarUsuario').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			success: function() {
				$.toast({
					heading: 'Éxito',
					text: 'Usuario actualizado correctamente',
					position: 'top-right',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 2000
				});
				$('#modalEditarUsuario').modal('hide');
				setTimeout(function() { location.reload(); }, 1000);
			},
			error: function() {
				$.toast({
					heading: 'Error',
					text: 'Error al actualizar',
					position: 'top-right',
					loaderBg: '#bf441d',
					icon: 'error'
				});
			}
		});
	});
	
	// === Funcionalidad Modal Agregar Usuario Completo ===
	
	let usuarioValidado = false;
	let documentoValidado = true; // Por defecto true porque es opcional
	
	// Validar usuario en tiempo real
	let timeoutValidacionUsuario;
	$('#modal_usuario').on('keyup', function() {
		clearTimeout(timeoutValidacionUsuario);
		const usuario = $(this).val().trim();
		usuarioValidado = false;
		
		if (usuario.length === 0) {
			$('#modal_validacion_usuario').html('').removeClass('text-success text-danger');
			return;
		}
		
		if (usuario.length < 3) {
			$('#modal_validacion_usuario').html('<small>Mínimo 3 caracteres</small>').removeClass('text-success').addClass('text-danger');
			return;
		}
		
		// Validar caracteres permitidos
		if (!/^[A-Za-z0-9]+$/.test(usuario)) {
			$('#modal_validacion_usuario').html('<small>Solo letras y números</small>').removeClass('text-success').addClass('text-danger');
			return;
		}
		
		$('#modal_validacion_usuario').html('<small><i class="fa fa-spinner fa-spin"></i> Validando...</small>').removeClass('text-success text-danger');
		
		timeoutValidacionUsuario = setTimeout(function() {
			$.ajax({
				url: 'ajax-validar-usuario.php',
				type: 'POST',
				data: { usuario: usuario },
				dataType: 'json',
				success: function(response) {
					console.log('Respuesta validación usuario:', response);
					if (response.error) {
						console.error('Error de validación:', response.error);
						if (response.debug) {
							console.error('Debug:', response.debug);
						}
						$('#modal_validacion_usuario').html('<small><i class="fa fa-exclamation-triangle"></i> Error al validar</small>').removeClass('text-success text-danger').addClass('text-warning');
						usuarioValidado = true; // Permitir continuar con advertencia
					} else if (response.existe) {
						$('#modal_validacion_usuario').html('<small><i class="fa fa-times"></i> ' + response.mensaje + '</small>').removeClass('text-success text-warning').addClass('text-danger');
						usuarioValidado = false;
					} else {
						$('#modal_validacion_usuario').html('<small><i class="fa fa-check"></i> Usuario disponible</small>').removeClass('text-danger text-warning').addClass('text-success');
						usuarioValidado = true;
					}
				},
				error: function(xhr, status, error) {
					console.error('Error AJAX en validación usuario:');
					console.error('Status:', status);
					console.error('Error:', error);
					console.error('Response:', xhr.responseText);
					$('#modal_validacion_usuario').html('<small><i class="fa fa-exclamation-triangle"></i> Error de conexión</small>').removeClass('text-danger text-success').addClass('text-warning');
					usuarioValidado = true; // Permitir continuar si hay error de conexión
				}
			});
		}, 500);
	});
	
	// Validar documento en tiempo real
	let timeoutValidacionDocumento;
	$('#modal_documento').on('keyup', function() {
		clearTimeout(timeoutValidacionDocumento);
		const documento = $(this).val().trim();
		documentoValidado = true; // Por defecto true porque es opcional
		
		if (documento.length === 0) {
			$('#modal_validacion_documento').html('').removeClass('text-success text-danger');
			return;
		}
		
		if (documento.length < 5) {
			$('#modal_validacion_documento').html('').removeClass('text-success text-danger');
			return;
		}
		
		$('#modal_validacion_documento').html('<small><i class="fa fa-spinner fa-spin"></i> Validando...</small>').removeClass('text-success text-danger');
		
		timeoutValidacionDocumento = setTimeout(function() {
			$.ajax({
				url: '../js/validaciones-usuario.php',
				type: 'POST',
				data: { documento: documento, idUsuario: 0 },
				dataType: 'json',
				success: function(response) {
					console.log('Respuesta validación documento:', response);
					if (response.existe) {
						$('#modal_validacion_documento').html('<small><i class="fa fa-times"></i> Este documento ya existe</small>').removeClass('text-success').addClass('text-danger');
						documentoValidado = false;
					} else {
						$('#modal_validacion_documento').html('<small><i class="fa fa-check"></i> Documento disponible</small>').removeClass('text-danger').addClass('text-success');
						documentoValidado = true;
					}
				},
				error: function(xhr, status, error) {
					console.error('Error en validación documento:', status, error);
					$('#modal_validacion_documento').html('').removeClass('text-success text-danger');
					documentoValidado = true; // Permitir continuar si hay error de conexión
				}
			});
		}, 500);
	});
	
	// Limpiar formulario al cerrar modal
	$('#modalAgregarUsuario').on('hidden.bs.modal', function() {
		$('#formAgregarUsuario')[0].reset();
		$('#modal_validacion_usuario').html('').removeClass('text-success text-danger text-warning');
		$('#modal_validacion_documento').html('').removeClass('text-success text-danger');
		usuarioValidado = false;
		documentoValidado = true;
	});
	
	// Guardar usuario
	$('#formAgregarUsuario').on('submit', function(e) {
		e.preventDefault();
		
		console.log('Intentando guardar usuario...');
		console.log('Usuario validado:', usuarioValidado);
		console.log('Documento validado:', documentoValidado);
		
		// Validar que el usuario esté disponible
		if (!usuarioValidado) {
			$.toast({
				heading: 'Error',
				text: 'Debe ingresar un usuario válido y disponible',
				position: 'top-right',
				loaderBg: '#bf441d',
				icon: 'error',
				hideAfter: 3000
			});
			return false;
		}
		
		// Validar que el documento esté disponible (si se ingresó)
		if (!documentoValidado) {
			$.toast({
				heading: 'Error',
				text: 'El documento ingresado ya existe',
				position: 'top-right',
				loaderBg: '#bf441d',
				icon: 'error',
				hideAfter: 3000
			});
			return false;
		}
		
		// Deshabilitar botón mientras se guarda
		const $btnGuardar = $('#btnModalGuardarUsuario');
		$btnGuardar.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
		
		console.log('Enviando formulario...');
		
		// El formulario se enviará normalmente al action="usuarios-guardar.php"
		this.submit();
	});
	
	// ========================================
	// MANEJO DE SELECCIÓN CON CHECKBOXES
	// ========================================
	
	var selectedUsuarios = [];
	
	// Manejar selección de todos los usuarios
	$('#selectAllUsuarios').on('change', function() {
		var isChecked = $(this).is(':checked');
		$('.usuario-checkbox').prop('checked', isChecked).trigger('change');
	});
	
	// Manejar selección individual de usuarios
	$(document).on('change', '.usuario-checkbox', function() {
		var row = $(this).closest('tr');
		var usuarioId = $(this).val();
		
		if ($(this).is(':checked')) {
			row.addClass('usuario-row-selected');
			if (selectedUsuarios.indexOf(usuarioId) === -1) {
				selectedUsuarios.push(usuarioId);
			}
		} else {
			row.removeClass('usuario-row-selected');
			selectedUsuarios = selectedUsuarios.filter(id => id !== usuarioId);
		}
		
		// Actualizar checkbox "Seleccionar todos"
		$('#selectAllUsuarios').prop('checked', 
			$('.usuario-checkbox:checked').length === $('.usuario-checkbox').length && 
			$('.usuario-checkbox').length > 0
		);
		
		// Actualizar contador
		actualizarContadorSeleccionados();
	});
	
	function actualizarContadorSeleccionados() {
		var count = selectedUsuarios.length;
		$('#lblCantSeleccionados').text(count > 0 ? '(' + count + ')' : '');
	}
	
	// Función para obtener usuarios seleccionados (compatible con código existente)
	function getSelecionados(tableId, checkboxName, labelId) {
		selectedUsuarios = [];
		$('.usuario-checkbox:checked').each(function() {
			selectedUsuarios.push($(this).val());
		});
		actualizarContadorSeleccionados();
		return selectedUsuarios;
	}
	
	// Hacer la función global para compatibilidad
	window.getSelecionados = getSelecionados;
	
	// ========================================
	// === Filtros Avanzados para Usuarios ===
	// ========================================
	
	// Toggle del panel de filtros
	$('#btnToggleFiltrosUsuarios').on('click', function() {
		const card = $('#cardFiltrosUsuarios');
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
	$('.select2-multiple-usuarios').select2({
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
	
	// Función para aplicar filtros de usuarios
	function aplicarFiltrosUsuarios() {
		const tipos = $('#filtro_usuarios_tipo').val() || [];
		const estados = $('#filtro_usuarios_estado').val() || [];
		
		console.log('Aplicando filtros usuarios:', { tipos, estados });
		
		// Mostrar loader en la tabla
		$('tbody').html('<tr><td colspan="8" class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Cargando...</td></tr>');
		
		// Enviar AJAX
		$.ajax({
			url: 'ajax-filtrar-usuarios.php',
			type: 'POST',
			data: {
				tipos: tipos,
				estados: estados
			},
			dataType: 'json',
			success: function(response) {
				console.log('Respuesta del filtro usuarios:', response);
				
				if (response.success) {
					// Insertar el HTML
					$('tbody').html(response.html);
					
					// Mostrar mensaje de resultados
					$.toast({
						heading: 'Filtros Aplicados',
						text: 'Se encontraron ' + response.total + ' usuario(s)',
						position: 'top-right',
						loaderBg: '#26c281',
						icon: 'success',
						hideAfter: 3000
					});
				} else {
					console.error('Error del servidor:', response.error);
					
					$.toast({
						heading: 'Error',
						text: response.error || 'Error al aplicar filtros',
						position: 'top-right',
						loaderBg: '#bf441d',
						icon: 'error',
						hideAfter: 5000
					});
					
					$('tbody').html('<tr><td colspan="8" class="text-center text-danger">Error al cargar los datos</td></tr>');
				}
			},
			error: function(xhr, status, error) {
				console.error('Error AJAX usuarios:', status, error);
				console.error('Response:', xhr.responseText);
				
				$.toast({
					heading: 'Error de Conexión',
					text: 'No se pudo conectar con el servidor',
					position: 'top-right',
					loaderBg: '#bf441d',
					icon: 'error',
					hideAfter: 5000
				});
				
				$('tbody').html('<tr><td colspan="8" class="text-center text-danger">Error de conexión</td></tr>');
			}
		});
	}
	
	// Limpiar filtros de usuarios
	$('#btnLimpiarFiltrosUsuarios').on('click', function() {
		$('#filtro_usuarios_tipo').val(null).trigger('change');
		$('#filtro_usuarios_estado').val(null).trigger('change');
		
		// Recargar la página para mostrar todos los usuarios
		location.reload();
	});
	
	// Aplicar filtros automáticamente al cambiar las opciones
	$('.select2-multiple-usuarios').on('change', function() {
		clearTimeout(window.filtroUsuariosTimeout);
		window.filtroUsuariosTimeout = setTimeout(function() {
			aplicarFiltrosUsuarios();
		}, 500);
	});
});
</script>

</body>

</html>