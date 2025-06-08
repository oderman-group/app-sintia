<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0350'; ?>
<?php include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php"); ?>
<?php include(ROOT_PATH."/main-app/compartido/head.php");
require_once ROOT_PATH . '/main-app/class/App/Comunicativo/Usuarios_Notificaciones.php';

$parametrosObligatorios =["id"];

Utilidades::validarParametros($_GET,$parametrosObligatorios);
if (!Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$id="";
if(!empty($_GET["id"])){ $id=base64_decode($_GET["id"]);}


$Plataforma = new Plataforma;

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
							<div class="page-title"><?= $frases[433][$datosUsuarioActual['uss_idioma']]; ?></div>
							<?php include("../compartido/texto-manual-ayuda.php"); ?>
						</div>
					</div>
				</div>
				<input type="text" class="form-control d-none" id="txtId" value="<?= $id; ?>">
				<input type="text" class="form-control d-none" id="txtIdInstitucion" value="<?= $_SESSION["idInstitucion"]; ?>">
				<input type="text" class="form-control d-none" id="txtIdBd" value="<?= $_SESSION["bd"]; ?>">
				<div class="row">
					<div class="col-md-12">
						<div class="row">

							<div class="col-md-12">
								<?php include(ROOT_PATH."/config-general/mensajes-informativos.php");
								
								?>

								<div class="card card-topline-purple">
									<div class="card-head">
										<header><?= $frases[433][$datosUsuarioActual['uss_idioma']]; ?></header>
										<div class="tools">
											<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
											<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
											<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
										</div>
									</div>
									<div class="card-body">

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
																					Acciones
																			<span class="fa fa-angle-down"></span>
																			
																		</a>
																		<div class="dropdown-menu" aria-labelledby="navbarDropdown">
																			<a class="dropdown-item"
																				href="javascript:void(0);"
																				onClick="actualizarSuscriptorTodos(true)">
																				Suscribir todos
																			</a>
																			<a class="dropdown-item"
																				href="javascript:void(0);"
																				onClick="actualizarSuscriptorTodos(false)">
																				Desuscribir todos
																			</a>
																		</div>
																	</li>
																</ul>
															</div>
														</th>
														<th>ID</th>
														<th>Usuario (REP)</th>
														<th>Nombre</th>														
														<th>Bloqueado</th>
														<th>Estado</th>
													</tr>
												</thead>
												<tbody>
												<script type="text/javascript">document.getElementById("overlay").style.display = "flex"</script>
													<?php													
													$lista = Comunicativo_Usuarios_Notificaciones::ObtenerUsuariosDirectivosSuscripcion($_SESSION["bd"],$_SESSION["idInstitucion"]);
													$contReg = 1;
													
													foreach ($lista as $usuario) {														

														$bgColor = '';														
														$chekedBloqueado = '';
														if ($usuario['uss_bloqueado'] == 1) {
															$chekedBloqueado = 'checked';
															$bgColor = '#ff572238';
														}	
														?>
														<tr id="reg<?= $usuario['uss_id']; ?>"
															style="background-color:<?= $bgColor; ?>;">
															<td><?= $contReg; ?></td>
															<td>
																<div class="input-group spinner col-sm-10">
																	<label class="switchToggle">
																		<input type="checkbox"
																				onChange="actualizarSuscriptor(this)"
																				id="<?= $usuario['uss_id'];?>_select"
																				name="suscrito" <?= $usuario['upn_id'] > 0 ? 'checked' : '' ?> >
																		<span class="slider aqua round"></span>
																	</label>
																</div>
															</td>
															<td><?= $usuario['uss_id']; ?></td>
															<td><?= $usuario['uss_usuario']; ?></td>
															<td><?= $usuario['uss_nombre']; ?></td>															
															<td>
																<?= $usuario['uss_bloqueado'] == 1 ? 'SI' : 'NO' ?> 
															</td>
															<td> 
																<?= $usuario['uss_estado'] == 1 ? 'Activo' : 'Inactivo'; ?> 
															</td>

														</tr>
													<?php 
														$contReg++;
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

<script src="../js/tipos-notificaciones-suscribir.js" ></script>
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

</body>

</html>