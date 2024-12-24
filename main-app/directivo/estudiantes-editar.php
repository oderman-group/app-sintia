<?php
include("session.php");
$idPaginaInterna = 'DT0078';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");

$parametrosObligatorios =["id"];

Utilidades::validarParametros($_GET,$parametrosObligatorios);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
require_once("../class/Estudiantes.php");
require_once("../class/servicios/GradoServicios.php");

$idMatricula="";
if(!empty($_GET["id"])){
	$idMatricula=base64_decode($_GET["id"]);
	$datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($idMatricula);
} else if(!empty($_GET["idUsuario"])){ 
	$idUsuario=base64_decode($_GET["idUsuario"]);
	$datosEstudianteActual = Estudiantes::obtenerDatosEstudiantePorIdUsuario($idUsuario);
	$idMatricula=$datosEstudianteActual["mat_id"];
}

if( empty($datosEstudianteActual) ){
	echo '<script type="text/javascript">window.location.href="estudiantes.php?error=ER_DT_16";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
?>

	<!-- steps -->
	<link rel="stylesheet" href="../../config-general/assets/plugins/steps/steps.css"> 
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />

	<!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">

    <script type="application/javascript">
		function validarEstudiante(enviada){
			var nDoct = enviada.value;
			var idEstudiante = <?php echo $datosEstudianteActual["mat_id"];?>;

			if(nDoct!=""){
				$('#nDocu').empty().hide().html("Validando documento...").show(1);

				datos = "nDoct="+(nDoct)+"&idEstudiante="+(idEstudiante);
					$.ajax({
					type: "POST",
					url: "ajax-estudiantes-editar.php",
					data: datos,
					success: function(data){
						$('#nDocu').empty().hide().html(data).show(1);
					}

				});

			}
		}
	</script>

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <!-- start header -->
		<?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">Editar matrículas</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="estudiantes.php" onClick="deseaRegresar(this)">Matrículas</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar matrículas</li>
                            </ol>
                        </div>
                    </div>

					<div class="row mb-3">
                    	<div class="col-sm-12">
							<div class="btn-group">
								<?php if(Modulos::validarPermisoEdicion()){?>
									<a href="estudiantes-agregar.php" id="addRow" class="btn deepPink-bgcolor">
										Agregar nuevo <i class="fa fa-plus"></i>
									</a>
								<?php }?>
							</div>
						</div>
					</div>

                    <span style="color: blue; font-size: 15px;" id="nDocu"></span>
                    <!-- wizard with validation-->
                    <div class="row">
                    	<div class="col-sm-12">
							<?php include("../../config-general/mensajes-informativos.php"); ?>
							<?php
							if($config['conf_id_institucion'] == ICOLVEN){
								if(isset($_GET['msgsion']) AND $_GET['msgsion']!=''){
									$aler='alert-success';
									$mensajeSion=base64_decode($_GET['msgsion']);
									if(base64_decode($_GET['stadsion'])!=true){
										$aler='alert-danger';
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
							if(isset($_GET['msgsintia'])){
								$aler='alert-success';
								if(base64_decode($_GET['stadsintia'])!=true){
								$aler='alert-danger';
								}
							?>
							<div class="alert alert-block <?=$aler;?>">
								<button type="button" class="close" data-dismiss="alert">×</button>
								<h4 class="alert-heading">SINTIA!</h4>
								<p><?=base64_decode($_GET['msgsintia']);?></p>
							</div>
							<?php }?>
                             <div class="card-box">
                                 <div class="card-head">
                                     <header>Matrículas</header>
                                 </div>
                                 <div class="card-body">
                                 	<form name="example_advanced_form" id="example-advanced-form" action="estudiantes-actualizar.php" method="post" enctype="multipart/form-data">
									<input type="hidden" name="id" value="<?=$idMatricula;?>">
									<input type="hidden" name="idU" value="<?=$datosEstudianteActual["mat_id_usuario"];?>">
									  
										<h3>Información personal</h3>
									    <?php include("includes/info-personal.php");?>
										
										<h3>Información académica</h3>
										<?php include("includes/info-academica.php");?>
											
										<h3>Información del Acudiente</h3>
										<fieldset>
											<?php include("includes/acudiente-1.php");?>

											<?php include("includes/acudiente-2.php");?>
											
										</fieldset>
										
									</form>
                                 </div>
                             </div>
                         </div>
                    </div>
					
					<div id="wizard" style="display: none;"></div>
                     
                </div>
            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <!-- start footer -->
        <?php include("../compartido/footer.php");?>
        <!-- end footer -->
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
	<script src="../../config-general/assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <!-- steps -->
    <script src="../../config-general/assets/plugins/steps/jquery.steps.js" ></script>
    <script src="../../config-general/assets/js/pages/steps/steps-data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>

	<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
    <!-- end js include path -->

</body>

</html>