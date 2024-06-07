<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0126'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php");
require_once '../class/Estudiantes.php';
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

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
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />


<script type="text/javascript">
	function guardarAjax(datos) {
		var idR = datos.id;
		var valor = 0;

		if (document.getElementById(idR).checked) {
			valor = 1;
			document.getElementById("reg" + idR).style.backgroundColor = "#ff572238";
		} else {
			valor = 0;
			document.getElementById("reg" + idR).style.backgroundColor = "white";
		}
		var operacion = 1;

		$('#respuestaGuardar').empty().hide().html("").show(1);
		datos = "idR=" + (idR) +
			"&valor=" + (valor) +
			"&operacion=" + (operacion);
		$.ajax({
			type: "POST",
			url: "ajax-guardar.php",
			data: datos,
			success: function(data) {
				$('#respuestaGuardar').empty().hide().html(data).show(1);
			}
		});
	}
</script>
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

								<?php include("includes/barra-superior-usuarios.php"); ?>

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
														<a href="usuarios-agregar.php" id="addRow" class="btn deepPink-bgcolor">
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
														<th>Bloq.</th>
														<th>ID</th>
														<th>Usuario (REP)</th>
														<th>Nombre</th>
														<th><?= $frases[53][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th>Último ingreso</th>
														<th><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></th>
													</tr>
												</thead>



												<?php
												include("includes/consulta-paginacion-usuarios.php");
												try {
													$consulta = mysqli_query($conexion, "SELECT * FROM " . BD_GENERAL . ".usuarios uss
														INNER JOIN " . $baseDatosServicios . ".general_perfiles ON pes_id=uss_tipo
														WHERE uss_id!='{$_SESSION["id"]}' AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]} $filtro
														ORDER BY uss_id
														LIMIT $inicio,$registros;");
												} catch (Exception $e) {
													include("../compartido/error-catch-to-report.php");
												}
												$contReg = 1;
												$bloqueado = array("NO", "SI");
												while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
													if ($resultado['uss_tipo'] == TIPO_DEV && $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
														continue;
													}

													$mostrarNumAcudidos = '';
													if ($resultado['uss_tipo'] == TIPO_ACUDIENTE) {
														try {
															$consultaUsuarioAcudiente = mysqli_query($conexion, "SELECT * FROM " . BD_GENERAL . ".usuarios_por_estudiantes upe
																INNER JOIN " . BD_ACADEMICA . ".academico_matriculas mat ON mat_id=upe_id_estudiante AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
																WHERE upe_id_usuario='" . $resultado['uss_id'] . "' AND upe_id_estudiante IS NOT NULL AND upe.institucion={$config['conf_id_institucion']} AND upe.year={$_SESSION["bd"]}");
														} catch (Exception $e) {
															include("../compartido/error-catch-to-report.php");
														}
														$num = mysqli_num_rows($consultaUsuarioAcudiente);

														$mostrarNumAcudidos = '<br><span style="font-size:9px; color:darkblue">(' . $num . ' Acudidos)</span>';
													}

													$backGroundMatricula = '';
													if ($resultado['uss_tipo'] == TIPO_ESTUDIANTE) {
														$tieneMatricula = Estudiantes::obtenerDatosEstudiantePorIdUsuario($resultado['uss_id']);
														if (empty($tieneMatricula)) {
															$backGroundMatricula = 'style="background-color:gold;" class="animate__animated animate__pulse animate__delay-2s" data-toggle="tooltip" data-placement="right" title="Este supuesto estudiante no cuenta con un registro en las matrículas."';
														}
													}

													$arrayEnviar = array("tipo" => 1, "descripcionTipo" => "Para ocultar fila del registro.");
													$arrayDatos = json_encode($arrayEnviar);
													$objetoEnviar = htmlentities($arrayDatos);

													$bgColor = '';
													if ($resultado['uss_bloqueado'] == 1) $bgColor = '#ff572238';

													$cheked = '';
													if ($resultado['uss_bloqueado'] == 1) {
														$cheked = 'checked';
													}


													$mostrarNumCargas = '';
													if ($resultado['uss_tipo'] == TIPO_DOCENTE) {
														$consultaNumCarga = CargaAcademica::traerCargasDocentes($config, $resultado['uss_id']);
														$numCarga = mysqli_num_rows($consultaNumCarga);

														$mostrarNumCargas = '<br><span style="font-size:9px; color:darkblue">(' . $numCarga . ' Cargas)</span>';
													}

													try {
														$consultaUsuariosRepetidos = mysqli_query($conexion, "SELECT count(uss_usuario) as rep 
															FROM " . BD_GENERAL . ".usuarios 
															WHERE uss_usuario='" . $resultado['uss_usuario'] . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}
															GROUP BY uss_usuario
															");
													} catch (Exception $e) {
														include("../compartido/error-catch-to-report.php");
													}
													$usuarioRepetido = mysqli_fetch_array($consultaUsuariosRepetidos, MYSQLI_BOTH);
													$avisoRepetido = null;
													if ($usuarioRepetido['rep'] > 1) $avisoRepetido = 'style="background-color:gold;"';

													$fotoUsuario = $usuariosClase->verificarFoto($resultado['uss_foto']);

													$estadoUsuario = !empty($resultado['uss_estado']) ? $opcionEstado[$resultado['uss_estado']] : '';

													$infoTooltip = "
														<p>
															<img src='{$fotoUsuario}' class='img-thumbnail' width='120px;' height='120px;'>
														</p>
														<b>Sesión:</b><br>
														{$estadoUsuario}<br>
														<b>Último ingreso:</b><br>
														{$resultado['uss_ultimo_ingreso']}<br><br>
														<b>Email:</b><br>
														{$resultado['uss_email']}<br>
														<b>Fecha de nacimiento:</b><br>
														{$resultado['uss_fecha_nacimiento']}
														";

													$managerPrimary = '';
													if ($resultado['uss_permiso1'] == CODE_PRIMARY_MANAGER && $resultado['uss_tipo'] == TIPO_DIRECTIVO) {
														$managerPrimary = '<i class="fa fa-user-circle text-primary" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Director principal"></i> ';
													}
												?>
													<tr id="reg<?= $resultado['uss_id']; ?>" style="background-color:<?= $bgColor; ?>;">
														<td><?= $contReg; ?></td>
														<td>
															<?php if (Modulos::validarPermisoEdicion() && ($resultado['uss_tipo'] != TIPO_DIRECTIVO || $resultado['uss_permiso1'] != CODE_PRIMARY_MANAGER)) { ?>
																<div class="input-group spinner col-sm-10">
																	<label class="switchToggle">
																		<input type="checkbox" id="<?= $resultado['uss_id']; ?>" name="bloqueado" value="1" onChange="guardarAjax(this)" <?= $cheked; ?> <?= $disabledPermiso; ?>>
																		<span class="slider red round"></span>
																	</label>
																</div>
															<?php } ?>
														</td>
														<td><?= $resultado['uss_id']; ?></td>
														<td <?= $avisoRepetido ?>>
															<?= $resultado['uss_usuario']; ?>
															<?php if ($usuarioRepetido['rep'] > 1) {
																echo " (" . $usuarioRepetido['rep'] . ")";
															} ?>
														</td>
														<td><?= $managerPrimary; ?><a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="<?= UsuariosPadre::nombreCompletoDelUsuario($resultado); ?>" data-content="<?= $infoTooltip; ?>" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000;"><?= UsuariosPadre::nombreCompletoDelUsuario($resultado); ?></a></td>
														<td <?= $backGroundMatricula; ?>><?= $resultado['pes_nombre'] . "" . $mostrarNumCargas . "" . $mostrarNumAcudidos; ?></td>
														<td>
															<span style="font-size: 11px;"><?= $resultado['uss_ultimo_ingreso']; ?></span>
														</td>

														<td>
															<div class="btn-group">
																<button type="button" class="btn btn-primary">Acciones</button>
																<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	<i class="fa fa-angle-down"></i>
																</button>
																<ul class="dropdown-menu" role="menu">
																	<?php if (Modulos::validarPermisoEdicion()) { ?>

																		<?php
																		if (($resultado['uss_tipo'] == TIPO_ESTUDIANTE && !empty($tieneMatricula)) || $resultado['uss_tipo'] != TIPO_ESTUDIANTE) {
																			if (Modulos::validarSubRol(['DT0124']) && ($resultado['uss_tipo'] != TIPO_DIRECTIVO || $resultado['uss_permiso1'] != CODE_PRIMARY_MANAGER)) {
																		?>
																				<li><a href="usuarios-editar.php?id=<?= base64_encode($resultado['uss_id']); ?>">Editar</a></li>
																		<?php }
																		}
																		?>


																		<?php
																		if (
																			($datosUsuarioActual['uss_tipo'] == TIPO_DEV && $resultado['uss_tipo'] != TIPO_DEV) ||
																			($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && $resultado['uss_tipo'] != TIPO_DEV && $resultado['uss_tipo'] != TIPO_DIRECTIVO && !isset($_SESSION['admin']) && !isset($_SESSION['devAdmin']))
																		) {
																			if ($resultado['uss_tipo'] == TIPO_ESTUDIANTE && !empty($tieneMatricula) || $resultado['uss_tipo'] != TIPO_ESTUDIANTE) {
																		?>
																				<li><a href="auto-login.php?user=<?= base64_encode($resultado['uss_id']); ?>&tipe=<?= base64_encode($resultado['uss_tipo']); ?>">Autologin</a></li>
																		<?php
																			}
																		}
																		?>

																		<?php if ($resultado['uss_tipo'] == TIPO_ACUDIENTE && Modulos::validarSubRol(['DT0137'])) { ?>
																			<li><a href="usuarios-acudidos.php?id=<?= base64_encode($resultado['uss_id']); ?>">Acudidos</a></li>
																		<?php } ?>

																		<?php if ((isset($numCarga) && $numCarga == 0 && $resultado['uss_tipo'] == TIPO_DOCENTE) || $resultado['uss_tipo'] == TIPO_ACUDIENTE || ($resultado['uss_tipo'] == TIPO_ESTUDIANTE && empty($tieneMatricula)) || $resultado['uss_tipo'] == TIPO_CLIENTE || $resultado['uss_tipo'] == TIPO_PROVEEDOR) { ?>
																			<li><a href="javascript:void(0);" title="<?= $objetoEnviar; ?>" name="usuarios-eliminar.php?id=<?= base64_encode($resultado['uss_id']); ?>" onClick="deseaEliminar(this)" id="<?= $resultado['uss_id']; ?>">Eliminar</a></li>
																		<?php } ?>
																	<?php } ?>

																	<?php if ($resultado['uss_tipo'] == TIPO_DOCENTE && $numCarga > 0 && Modulos::validarSubRol(['DT0239'])) { ?>
																		<li><a href="../compartido/planilla-docentes.php?docente=<?= base64_encode($resultado['uss_id']); ?>" target="_blank">Planillas de las cargas</a></li>
																	<?php } ?>

																	<?php if (($datosUsuarioActual['uss_tipo'] == TIPO_DEV && $resultado['uss_tipo'] != TIPO_DEV) ||
																			($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && $resultado['uss_tipo'] != TIPO_DEV && $resultado['uss_tipo'] != TIPO_DIRECTIVO) && Modulos::validarSubRol(['DT0327'])) { ?>
																		<li><a href="../compartido/informe-historial-ingreso.php?id=<?= base64_encode($resultado['uss_id']); ?>" target="_blank">Historial de Ingreso</a></li>
																	<?php } ?>

																</ul>
															</div>
														</td>
													</tr>
												<?php
													$contReg++;
												}
												?>

											</table>

										</div>

									</div>
								</div>
								<?php include("enlaces-paginacion.php"); ?>
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

<script>
	$(function() {
		$('[data-toggle="popover"]').popover();
	});

	$('.popover-dismiss').popover({
		trigger: 'focus'
	});
</script>
</body>

</html>