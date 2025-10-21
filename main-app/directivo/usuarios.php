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

<style>
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

								<?php include("includes/barra-superior-usuarios.php");

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

										<div class="row" style="margin-bottom: 10px;">
											<div class="col-sm-12">
												<div class="btn-group">
													<?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0123'])) { ?>
														<a href="usuarios-agregar.php" id="addRow"
															class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
													<?php } ?>
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
															<div class="input-group spinner col-sm-10">
																<label class="switchToggle"
																		title="Seleccionar todos">
																	<input  type="checkbox"
																			onChange="seleccionarCheck('example1','selecionado','lblCantSeleccionados',this.checked)" value="1"  <?= $disabledPermiso; ?>>
																	<span class="slider aqua round"></span>
																</label>
															</div>
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
																<button class="btn btn-sm btn-info expand-btn"
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
																	<i class="fa fa-plus"></i>
																</button>
															</td>
															<td><?= $contReg; ?></td>
															<td>
																<div class="input-group spinner col-sm-10">
																	<label class="switchToggle">
																		<input type="checkbox"
																				onChange="getSelecionados('example1','selecionado','lblCantSeleccionados')"
																				id="<?= $usuario['uss_id']; ?>_select"
																				name="selecionado">
																		<span class="slider aqua round"></span>
																	</label>
																</div>
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
				icon.removeClass('fa-minus').addClass('fa-plus');
				button.removeClass('btn-warning').addClass('btn-info');
			} else {
				// Open this row
				row.child(formatDetails(foto, nombre, usuario, email, fechaNacimiento, tipo, estado, ultimoIngreso, bloqueado, numCarga, cantidadAcudidos, tieneMatricula, tipoUsuario, userId, telefono, direccion, ocupacion, genero, fechaRegistro, documento, tipoDocumento, lugarExpedicion, intentosFallidos)).show();
				icon.removeClass('fa-plus').addClass('fa-minus');
				button.removeClass('btn-info').addClass('btn-warning');
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
});
</script>

</body>

</html>