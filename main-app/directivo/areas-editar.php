<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0018';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Areas.php");
Utilidades::validarParametros($_GET);
if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}

$rCargas = Areas::traerDatosArea($config, base64_decode($_GET["id"]));
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
                                <div class="page-title">Editar Areas</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="areas.php" onClick="deseaRegresar(this)"><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar Areas</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
                        <div class="col-sm-12">
                                <?php include("../../config-general/mensajes-informativos.php"); ?>


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">
									<form name="formularioGuardar" action="areas-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" value="<?=base64_decode($_GET["id"])?>" name="idA">
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre del Areas</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="nombreA" class="form-control" value="<?=$rCargas["ar_nombre"] ?>" <?=$disabledPermiso;?> required>
                                            </div>
                                        </div>	
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Posición</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="posicionA" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
                                                    $cPosicionA = Areas::traerAreasInstitucion($config, $rCargas["ar_id"]);
                                                    $numDatos=mysqli_num_rows($cPosicionA);
                                                    $cont=0;
                                                    while($rPos=mysqli_fetch_array($cPosicionA, MYSQLI_BOTH)){
                                                        $cont++;
                                                        $posciones[$cont]=$rPos["ar_posicion"];
                                                    }
                                                    $cond=0;
                                                    $exist=0;
                                                    for($i=1;$i<=(20+$cond);$i++){
                                                        if($numDatos>0){
                                                            for($j=0;$j<=count($posciones);$j++){
                                                                if($i==$posciones[$j]){
                                                                    $exist=1;
                                                                } 
                                                            }
                                                        }
                                                        if($exist!=1){
                                                            if($rCargas["ar_posicion"]==$i){
                                                            echo '<option value="'.$i.'" selected>'.$i.'</option>';	
                                                            }else{
                                                               echo '<option value="'.$i.'">'.$i.'</option>';  
                                                            }
                                                        }else{
                                                        $cond++;
                                                        }
                                                        $exist=0;
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                    <?php $botones = new botonesGuardar("areas.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
						
                    </div>

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