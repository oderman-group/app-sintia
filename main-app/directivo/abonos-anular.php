<?php
include("session.php");
$idPaginaInterna = 'DT0269';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}

$id = '';
if (!empty($_GET['id'])) {
    $idDecoded = base64_decode($_GET['id'], true);
    if ($idDecoded === false || empty($idDecoded)) {
        $idDecoded = base64_decode($_GET['id']);
    }
    $id = $idDecoded;
}

if (empty($id)) {
    echo '<script type="text/javascript">alert("ID de abono no proporcionado."); window.location.href="abonos.php";</script>';
    exit();
}

// Obtener datos del abono para mostrar información
$datosAbono = Movimientos::traerDatosAbonos($conexion, $config, $id);

if (empty($datosAbono) || !is_array($datosAbono)) {
    echo '<script type="text/javascript">alert("Abono no encontrado."); window.location.href="abonos.php";</script>';
    exit();
}

// Verificar si ya está anulado
if (!empty($datosAbono['is_deleted']) && $datosAbono['is_deleted'] == 1) {
    echo '<script type="text/javascript">alert("Este abono ya está anulado."); window.location.href="abonos.php";</script>';
    exit();
}
?>
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
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
                                <div class="page-title">Anular Abono</div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="abonos.php" onClick="deseaRegresar(this)"><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Anular Abono</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                            <div class="panel">
                                <header class="panel-heading panel-heading-purple">
                                    <h4 style="margin: 0; color: white;">
                                        <i class="fa fa-ban"></i> Anular Abono
                                    </h4>
                                </header>
                                <div class="panel-body" style="padding: 25px;">
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i> 
                                        <strong>Advertencia:</strong> Al anular un abono, este quedará marcado como anulado y no se podrá revertir esta acción. 
                                        El abono no se eliminará del sistema, pero quedará registrado como anulado para mantener la integridad de los registros contables.
                                    </div>
                                    
                                    <div class="panel" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                        <h5 style="margin-top: 0; color: #333;">Información del Abono</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Código:</strong> <?=htmlspecialchars($datosAbono['cod_payment'] ?? 'N/A')?></p>
                                                <p><strong>Fecha:</strong> <?=htmlspecialchars($datosAbono['registration_date'] ?? 'N/A')?></p>
                                                <p><strong>Cliente:</strong> <?=htmlspecialchars($datosAbono['cliente_nombre'] ?? 'N/A')?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Método de Pago:</strong> <?=htmlspecialchars($datosAbono['payment_method'] ?? 'N/A')?></p>
                                                <p><strong>Valor:</strong> $<?=number_format(floatval($datosAbono['valorAbono'] ?? 0), 0, ",", ".")?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <form name="formularioAnular" action="abonos-anular-guardar.php" method="post">
                                        <input type="hidden" name="id" value="<?=htmlspecialchars($id)?>">
                                        
                                        <div class="form-group">
                                            <label>Razón de Anulación <span style="color: red;">(*)</span></label>
                                            <textarea name="razon_anulacion" class="form-control" rows="4" required <?=$disabledPermiso;?> placeholder="Ingrese la razón por la cual se anula este abono..."></textarea>
                                            <small class="help-block">Este campo es obligatorio. La razón quedará registrada en el historial del abono.</small>
                                        </div>
                                        
                                        <?php $botones = new botonesGuardar("abonos.php",Modulos::validarPermisoEdicion()); ?>
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


