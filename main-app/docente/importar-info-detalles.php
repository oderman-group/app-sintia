<?php
include("session.php");
$idPaginaInterna = 'DC0147';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
?>

<!--bootstrap -->
<link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!-- dropzone -->
<link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
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
							<div class="page-title">Detalles de importación</div>
							<?php include("../compartido/texto-manual-ayuda.php"); ?>
						</div>
						<ol class="breadcrumb page-breadcrumb pull-right">
							<li><a class="parent-item" href="javascript:void(0);" name="importar-info.php" onClick="deseaRegresar(this)">Importar Información</a>&nbsp;<i class="fa fa-angle-right"></i></li>
							<li class="active">Detalles de importación</li>
						</ol>
					</div>
				</div>
				<?php include("includes/barra-superior-informacion-actual.php"); ?>
				<div class="row">

					<div class="col-md-12">


						<div class="panel">
							<header class="panel-heading panel-heading-purple">Detalles de importación</header>
							<div class="panel-body">

								<?php
									$numImportación = 0;
									if(!empty($_POST["indicadores"]) && empty($_POST["calificaciones"])){
										//Consultamos los indicadores a importar
										$indImpConsulta = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $_POST["cargaImportar"], $_POST["periodoImportar"]);
										$numIndicadores = mysqli_num_rows($indImpConsulta);
										if ($numIndicadores > 0) {
											echo "<b>Indicadores a importar:</b><br>";
											$numIndicadores = 1;
											while($indImpDatos = mysqli_fetch_array($indImpConsulta, MYSQLI_BOTH)){
												echo "<span style='margin-left: 20px;'>".$numIndicadores.") ".$indImpDatos['ind_nombre']." (".$indImpDatos['ipc_valor']."%)</span><br>";
												$numIndicadores ++;
												$numImportación ++;
											}
										}
									}
									
									if(!empty($_POST["calificaciones"])){
										//Consultamos los indicadores a importar
										$indImpConsulta = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $_POST["cargaImportar"], $_POST["periodoImportar"]);
										$numIndicadores = mysqli_num_rows($indImpConsulta);
										if ($numIndicadores > 0) {
											echo "<b>Indicadores y calificaciones a importar:</b><br>";
											$contIndicadores = 1;

											while($indImpDatos = mysqli_fetch_array($indImpConsulta, MYSQLI_BOTH)){
												echo "<span'>".$contIndicadores.") ".$indImpDatos['ind_nombre']." (".$indImpDatos['ipc_valor']."%)</span><br>";

												//Consultamos las calificaciones del indicador a Importar
												$calImpConsulta = Actividades::traerActividadesCargaIndicador($config, $indImpDatos['ind_id'], $_POST["cargaImportar"], $_POST["periodoImportar"]);
												$numCalificaciones = mysqli_num_rows($calImpConsulta);
												if ($numCalificaciones > 0) {
													echo "<b style='margin-left: 40px;'>Calificaciones a importar de este indicador:</b><br>";
													$contCalificaciones = 1;

													while($calImpDatos = mysqli_fetch_array($calImpConsulta, MYSQLI_BOTH)){
														echo "<span style='margin-left: 40px;'>".$contCalificaciones.") ".$calImpDatos['act_descripcion']." (".$calImpDatos['act_valor']."%)</span><br>";
														$contCalificaciones ++;
													}
												} else {
													echo "<b style='margin-left: 40px;'>Este indicador no tiene calificaciones relacionadas.</b><br>";
												}
												$contIndicadores ++;
												$numImportación ++;
											}
										}
									}
									
									if(!empty($_POST["clases"])){
										//Consultamos las clases a Importar
										$claImpConsulta = Clases::traerClasesCargaPeriodo($conexion, $config, $_POST["cargaImportar"], $_POST["periodoImportar"]);

										$numClases = mysqli_num_rows($claImpConsulta);
										if ($numClases > 0) {
											echo "<br><b>Clases a importar:</b><br>";
											$contClases = 1;

											while($claImpDatos = mysqli_fetch_array($claImpConsulta, MYSQLI_BOTH)){
												echo "<span style='margin-left: 20px;'>".$contClases.") ".$claImpDatos['cls_tema']."</span><br>";
												$contClases ++;
												$numImportación ++;
											}
										}
									}
									
									if ($numImportación == 0) {
										echo "<b>La carga seleccionada no tiene información para importar en el periodo seleccionado, por favor verifique y vuelva a intentarlo.</b>";
									}
								?>

								<form name="formularioGuardar" action="importar-info-guardar.php?carga=<?= base64_encode($cargaConsultaActual); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>" method="post" style="margin-top: 20px;">
            						<input type="hidden" value="<?=$_POST['cargaImportar'];?>" name="cargaImportar">
            						<input type="hidden" value="<?=$_POST['periodoImportar'];?>" name="periodoImportar">
            						<input type="hidden" value="<?=!empty($_POST['indicadores']) && $_POST['indicadores'] == 1 ? $_POST['indicadores'] : 0;?>" name="indicadores">
            						<input type="hidden" value="<?=!empty($_POST['calificaciones']) && $_POST['calificaciones'] == 1 ? $_POST['calificaciones'] : 0;?>" name="calificaciones">
            						<input type="hidden" value="<?=!empty($_POST['clases']) && $_POST['clases'] == 1 ? $_POST['clases'] : 0;?>" name="clases">

									<a href="javascript:void(0);" name="importar-info.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
									<?php if ($numImportación > 0) { ?>
										<input type="submit" class="btn btn-primary" value="<?= $frases[167][$datosUsuarioActual['uss_idioma']]; ?>">&nbsp;
									<?php } ?>
								</form>
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
	<script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
	<script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" charset="UTF-8"></script>
	<script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js" charset="UTF-8"></script>
	<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
	<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js" charset="UTF-8"></script>
	<!-- Common js-->
	<script src="../../config-general/assets/js/app.js"></script>
	<script src="../../config-general/assets/js/layout.js"></script>
	<script src="../../config-general/assets/js/theme-color.js"></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
	<script src="../../config-general/assets/plugins/dropzone/dropzone.js"></script>
	<!--tags input-->
	<script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
	<script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js"></script>
	<!--select2-->
	<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
	<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
	<!-- end js include path -->
	</body>

	<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

	</html>