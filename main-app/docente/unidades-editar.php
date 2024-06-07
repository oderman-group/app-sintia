<?php
include("session.php");
$idPaginaInterna = 'DC0096';
require_once(ROOT_PATH."/main-app/class/Unidades.php");
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
include("../compartido/head.php");

$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}

$datosUnidad = Unidades::consultarUnidadesPorID($conexion, $idR);
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
	<?php
	include("../compartido/encabezado.php");
	include("../compartido/panel-color.php");
	?>
	<!-- start page container -->
	<div class="page-container">
		<?php include("../compartido/menu.php"); ?>
		<!-- start page content -->
		<div class="page-content-wrapper">
			<div class="page-content">
				<div class="page-bar">
					<div class="page-title-breadcrumb">
						<div class=" pull-left">
							<div class="page-title"><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?> <?= $frases[374][$datosUsuarioActual['uss_idioma']]; ?></div>
							<?php include("../compartido/texto-manual-ayuda.php"); ?>
						</div>
						<ol class="breadcrumb page-breadcrumb pull-right">
							<li><a class="parent-item" href="#" name="clases.php" onClick="deseaRegresar(this)"><?= $frases[374][$datosUsuarioActual['uss_idioma']]; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
							<li class="active"><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?> <?= $frases[374][$datosUsuarioActual['uss_idioma']]; ?></li>
						</ol>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<?php include("info-carga-actual.php"); ?>
						<div class="panel">
							<header class="panel-heading panel-heading-purple"><?= $frases[374][$datosUsuarioActual['uss_idioma']]; ?> </header>
							<div class="panel-body">
								<?php
								$unidadesEnComun = Unidades::consultarUnidadesDiferentes($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $idR);
								while ($uniComun = mysqli_fetch_array($unidadesEnComun, MYSQLI_BOTH)) {
								?>
									<p><a href="unidades-editar.php?idR=<?= base64_encode($uniComun['uni_id']); ?>"><?= $uniComun['uni_nombre']; ?></a></p>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="col-sm-9">
						<div class="panel">
							<header class="panel-heading panel-heading-purple"><?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </header>
							<div class="panel-body">
								<form name="formularioGuardar" action="unidades-actualizar.php?carga=<?= base64_encode($cargaConsultaActual); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>" method="post">
									<input type="hidden" value="<?= $datosUnidad['id_nuevo']; ?>" name="idR">

									<div class="form-group row">
										<label class="col-sm-2 control-label">Nombre:</label>
										<div class="col-sm-10">
											<input type="text" name="nombre" class="form-control" autocomplete="off" value="<?= $datosUnidad['uni_nombre']; ?>" required>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-sm-2 control-label">Descripción:</label>
										<div class="col-sm-10">
											<textarea id="editor1" name="contenido" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"><?=$datosUnidad['uni_descripcion'];?></textarea>
										</div>
									</div>
									<?php 
                            				$botones = new botonesGuardar("clases.php",Modulos::validarPermisoEdicion()); ?>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
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
    <script src="../ckeditor/ckeditor.js"></script>

	<script>
		// Replace the <textarea id="editor1"> with a CKEditor 4
		// instance, using default configuration.
		CKEDITOR.replace( 'editor1' );
	</script>
	</body>

	<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

	</html>