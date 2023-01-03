<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0054';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaDatos=mysqli_query($conexion, "SELECT * FROM general_folders WHERE fold_id='".$_GET["idR"]."' AND fold_categoria=2");
$datosConsulta = mysqli_fetch_array($consultaDatos, MYSQLI_BOTH);

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
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
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
                                <div class="page-title"><?=$frases[165][$datosUsuarioActual[8]];?> <?=$frases[216][$datosUsuarioActual[8]];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <?php
								if(is_numeric($_GET["idR"])){	
									$idFolderActual = $_GET["idR"];
									$var = 1;
									$i=0;
									$vectorDatos = array();
									while($var==1){
                                        $consultaCarpetaActual=mysqli_query($conexion, "SELECT fold_id, fold_padre FROM general_folders WHERE fold_id='".$idFolderActual."' AND fold_estado=1");
										$carpetaActual = mysqli_fetch_array($consultaCarpetaActual, MYSQLI_BOTH);
										$vectorDatos[$i] = $carpetaActual['fold_id'];
										if($carpetaActual['fold_padre']!="" and $carpetaActual['fold_padre']!='0'){
											$idFolderActual = $carpetaActual['fold_padre'];
										}else{$var = 2;}
										$i++;
									}
									?>
									
									<li><a class="parent-item" href="cargas-carpetas.php"><?=$frases[216][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
									<?php
									$cont = count($vectorDatos);
									$cont = $cont - 1;
									while($cont>0){
                                        $consultaCarpetaActual=mysqli_query($conexion, "SELECT * FROM general_folders WHERE fold_id='".$vectorDatos[$cont]."' AND fold_estado=1");
										$carpetaActual = mysqli_fetch_array($consultaCarpetaActual, MYSQLI_BOTH);

									?>
											<li><a class="parent-item" href="cargas-carpetas.php?carpeta=<?=$carpetaActual['fold_id'];?>"><?=$carpetaActual['fold_nombre'];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
									<?php
										$cont--;
									}
								}
								?>
                                <li class="active"><?=$frases[165][$datosUsuarioActual[8]];?> <?=$frases[216][$datosUsuarioActual[8]];?></li>
                            </ol>
                        </div>
                    </div>
                    <?php include("../compartido/cargas-carpetas-editar-contenido.php");?>

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
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>