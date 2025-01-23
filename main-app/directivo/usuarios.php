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
														<th>Último ingreso</th>
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
														"uss_ultimo_ingreso"
													];
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
														if (!empty($usuario['cantidad_acudidos']) && $usuario['uss_tipo'] == TIPO_ACUDIENTE ) {
															$mostrarNumAcudidos = '<br><span style="font-size:9px; color:darkblue">(' . $usuario['cantidad_acudidos'] . ')  Acudidos)</span>';
														}

														$mostrarNumCargas = '';
														if (!empty($usuario['cantidad_cargas'])  && $usuario['uss_tipo'] == TIPO_DOCENTE) {
															$numCarga         =  $usuario['cantidad_cargas'];
															$mostrarNumCargas = '<br><span style="font-size:9px; color:darkblue">(' . $usuario['cantidad_cargas'] . ' Cargas)</span>';
														}

														

														$managerPrimary = '';
														if ($usuario['uss_permiso1'] == CODE_PRIMARY_MANAGER && $usuario['uss_tipo'] == TIPO_DIRECTIVO) {
															$managerPrimary = '<i class="fa fa-user-circle text-primary" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Director principal"></i> ';
														}

														$fotoUsuario = $usuariosClase->verificarFoto($usuario['uss_foto']);
														$estadoUsuario = !empty($usuario['uss_estado']) ? $opcionEstado[$usuario['uss_estado']] : '';

														$infoTooltip = "
														<p>
															<img src='{$fotoUsuario}' class='img-thumbnail' width='120px;' height='120px;'>
														</p>
														<b>Sesión:</b><br>
														{$estadoUsuario}<br>
														<b>Último ingreso:</b><br>
														{$usuario['uss_ultimo_ingreso']}<br><br>
														<b>Email:</b><br>
														{$usuario['uss_email']}<br>
														<b>Fecha de nacimiento:</b><br>
														{$usuario['uss_fecha_nacimiento']}
														";
														?>
														<tr id="reg<?= $usuario['uss_id']; ?>"
															style="background-color:<?= $bgColor; ?>;">
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
															<td><?= $managerPrimary; ?>
																<a tabindex="0" role="button" data-toggle="popover"
																	data-trigger="focus"
																	title="<?= UsuariosPadre::nombreCompletoDelUsuario($usuario); ?>"
																	data-content="<?= $infoTooltip; ?>" data-html="true"
																	data-placement="top"
																	style="border-bottom: 1px dotted #000;"><?= UsuariosPadre::nombreCompletoDelUsuario($usuario); ?></a>
															</td>
															<td >
																<?= $usuario['pes_nombre'] . "" . $mostrarNumCargas . "" . $mostrarNumAcudidos; ?>
															</td>
															<td><span
																	style="font-size: 11px;"><?= $usuario['uss_ultimo_ingreso']; ?></span>
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
																				<li><a href="usuarios-editar.php?id=<?= base64_encode($usuario['uss_id']); ?>">Editar</a></li>
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
		background-color: #red !important;
    }

  </style>
<script>

	$(function () {
		$('[data-toggle="popover"]').popover();
	});

	$('.popover-dismiss').popover({
		trigger: 'focus'
	});

	function actualizarBloqueo(bloqueo){
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
				      tipo:'<?= $_GET["tipo"] ?>'
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

	function marcarbloqueados(result,data) {
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
</body>

</html>