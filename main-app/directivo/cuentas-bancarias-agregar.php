<?php
include("session.php");
$idPaginaInterna = 'DT0279';
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
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
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
                                <div class="page-title">Agregar Cuenta Bancaria</div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cuentas-bancarias.php" onClick="deseaRegresar(this)">Cuentas Bancarias</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Agregar Cuenta Bancaria</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                            <div class="panel">
                                <header class="panel-heading panel-heading-purple">Agregar Cuenta Bancaria</header>
                                <div class="panel-body">
									<form name="formularioGuardar" action="cuentas-bancarias-guardar.php" method="post" enctype="multipart/form-data">

										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="cba_nombre" class="form-control" required <?=$disabledPermiso;?> placeholder="Ej: Bancolombia 1, Caja Metálica 1">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Banco</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="cba_banco" class="form-control" <?=$disabledPermiso;?> placeholder="Ej: Bancolombia, Banco de Occidente">
                                            </div>

                                            <label class="col-sm-2 control-label">Número de Cuenta</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="cba_numero_cuenta" class="form-control" <?=$disabledPermiso;?> placeholder="Número de cuenta">
                                            </div>
										</div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Tipo <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select name="cba_tipo" class="form-control" required <?=$disabledPermiso;?>>
                                                    <option value="AHORROS">Ahorros</option>
                                                    <option value="CORRIENTE">Corriente</option>
                                                    <option value="NEOQUI">Nequi</option>
                                                    <option value="DAVIPLATA">Daviplata</option>
                                                    <option value="CAJA_METALICA">Caja Metálica</option>
                                                    <option value="OTRO">Otro</option>
                                                </select>
                                            </div>

                                            <label class="col-sm-2 control-label">Método de Pago <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select name="cba_metodo_pago_asociado" class="form-control select2" required <?=$disabledPermiso;?>>
                                                    <?php
                                                    require_once(ROOT_PATH."/main-app/class/MediosPago.php");
                                                    echo MediosPago::generarOpcionesSelect(null, true, 'Seleccione un método');
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Saldo Inicial</label>
                                            <div class="col-sm-4">
                                                <input type="number" name="cba_saldo_inicial" class="form-control" step="0.01" min="0" value="0" <?=$disabledPermiso;?> placeholder="0.00">
                                            </div>

                                            <label class="col-sm-2 control-label">Estado</label>
                                            <div class="col-sm-4">
                                                <select name="cba_activa" class="form-control" <?=$disabledPermiso;?>>
                                                    <option value="1" selected>Activa</option>
                                                    <option value="0">Inactiva</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-12 control-label">Observaciones</label>
                                            <div class="col-sm-12">
                                                <textarea name="cba_observaciones" class="form-control" rows="3" <?=$disabledPermiso;?>></textarea>
                                            </div>
                                        </div>
                                        
                                        <?php $botones = new botonesGuardar("cuentas-bancarias.php",Modulos::validarPermisoEdicion()); ?>
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
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
</body>
</html>


