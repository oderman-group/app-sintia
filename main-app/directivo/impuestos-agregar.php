<?php
include("session.php");
$idPaginaInterna = 'DT0295';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/compartido/head.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}?>

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
<?php require_once(ROOT_PATH."/main-app/compartido/body.php");?>
    <div class="page-wrapper">
        <?php require_once(ROOT_PATH."/main-app/compartido/encabezado.php");?>
		
        <?php require_once(ROOT_PATH."/main-app/compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php require_once(ROOT_PATH."/main-app/compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[420][$datosUsuarioActual['uss_idioma']];?></div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="impuestos.php" onClick="deseaRegresar(this)"><?=$frases[420][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[420][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                            <div class="panel">
                                <header class="panel-heading panel-heading-purple"><?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[420][$datosUsuarioActual['uss_idioma']];?></header>
                                <div class="panel-body">
									<form name="formularioGuardar" action="impuestos-guardar.php" method="post" enctype="multipart/form-data">

										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[294][$datosUsuarioActual['uss_idioma']];?> <?=$frases[420][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select class="form-control select2" name="typeTax" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="<?=IVA?>" >IVA</option>
                                                    <option value="<?=ICO?>" >ICO</option>
                                                    <option value="<?=ICUI?>" >ICUI</option>
                                                    <option value="<?=OTRO?>" >OTRO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[187][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="text" name="name" class="form-control" required <?=$disabledPermiso;?>>
                                            </div>
										</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[421][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="text" name="fee" class="form-control" onchange="validarInput(this)" required <?=$disabledPermiso;?>>
                                            </div>
                                            <span id="resp" style="display:none"></span>
										</div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <textarea cols="80" name="description" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?>></textarea>
                                            </div>
                                        </div>
                                        
                                        <a href="javascript:void(0);" name="impuestos.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
                                        <?php if(Modulos::validarPermisoEdicion()){?>
                                            <button type="submit" class="btn  btn-info" id="btnEnviar">
                                                <i class="fa fa-save" aria-hidden="true"></i> <?=$frases[419][$datosUsuarioActual['uss_idioma']];?> 
                                            </button>
                                        <?php }?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <!-- end page container -->
        <?php require_once(ROOT_PATH."/main-app/compartido/footer.php");?>
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

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>