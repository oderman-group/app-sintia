<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0105';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");

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
                                <div class="page-title">Importar saldos</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="movimientos.php" onClick="deseaRegresar(this)"><?=$frases[95][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Importar saldos</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <?php include("../../config-general/mensajes-informativos.php"); ?>
						<div class="col-sm-3">
							<div class="panel">
								<header class="panel-heading panel-heading-blue">Paso a paso</header>
                                <div class="panel-body">
                                    <p><b>1.</b> Descargue la plantilla de excel.</p>
                                    <p><b>2.</b> Llene los campos de los usuarios en el orden que la plantilla los solicita.</p>
                                    <p><b>3.</b> Finalmente cargue la plantilla en el campo que dice <mark>Subir la planilla lista</mark> y dele click al botón importar saldos.</p>
                                </div>

                                <header class="panel-heading panel-heading-blue">Consideraciones</header>
                                <div class="panel-body">
                                    <b>PARA LA PLANTILLA</b></br>
                                    <p><b>-></b> Tenga en cuenta, para importar los movimientos, los siguientes campos son obligatorios, el ID de usuario, saldo y tipo de movimiento.</p>
                                    <b>PARA EL FORMULARIO</b></br>
                                    <p><b>-></b> En el campo <mark>Qué acción desea ejecutar?</mark>. Desbe escoger si bloquear o no los usuarios con deudas.</p>
                                    <p><b>-></b> En el campo <mark>Qué dato está en la primera columna de excel?</mark>. Debe especificar qué ID de usuario uso en la plantilla de excel, recuerde que puede ser el documento o el código de tesorería <mark>(sólo si todos son estudiantes)</mark>.<br>Este campo no debe llevar puntos ni coma.</p>
                                </div>
                            </div>
                        </div>
						
                        <div class="col-sm-9">

                            <div class="panel">
                                <header class="panel-heading panel-heading-purple">Importar saldos </header>
                                <div class="panel-body">
									<form name="formularioGuardar" action="excel-importar-movimientos.php" method="post" enctype="multipart/form-data">

                                        <div class="form-group row">
                                            <label class="col-sm-3 control-label">Descargar formato de plantilla</label>
                                            <div class="col-sm-9">
                                                <a href="../files/excel/saldos_usuarios_sintia.xlsx" target="_blank">Plantilla Financiera</a>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 control-label">Subir la planilla lista <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-6">
                                                <input type="file" class="form-control" name="planilla" required <?=$disabledPermiso;?>>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 control-label">Coloque el número de la última fila hasta donde quiere que el archivo sea leido <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="number" class="form-control" name="filaFinal" placeholder="200" required <?=$disabledPermiso;?>><br>
                                                <span style="font-size: 12px; color:#6017dc;">Fila hasta donde hay información de los usuarios. Esto se usa para evitar que se lean filas que no tienen información.</span>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-3 control-label">Qué acción desea ejecutar? <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-6">
                                                <select class="form-control select2" name="accion" require <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1">No hacer nada</option>
													<option value="2">Bloquear usuarios con deuda</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-3 control-label">Qué dato está en la primera columna de excel? <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-6">
                                                <select class="form-control select2" name="datoID" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1">Documento</option>
													<option value="2">Código de tesorería</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-3 control-label">Detalle <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="detalle" class="form-control" autocomplete="off" required <?=$disabledPermiso;?>>
                                            </div>
                                        </div>

                                      
                                        <?php $botones = new botonesGuardar("movimientos.php",Modulos::validarPermisoEdicion(),"Importar saldos"); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page content -->
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