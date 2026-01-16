<?php
include("session.php");
$idPaginaInterna = 'DT0278';
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
                                <div class="page-title">Transferir entre Cuentas Bancarias</div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cuentas-bancarias.php" onClick="deseaRegresar(this)">Cuentas Bancarias</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Transferir entre Cuentas</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                            <div class="panel">
                                <header class="panel-heading panel-heading-purple">Transferir entre Cuentas Bancarias</header>
                                <div class="panel-body">
									<form name="formularioTransferir" action="cuentas-bancarias-transferir-guardar.php" method="post" id="formTransferir">
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Cuenta Origen <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select name="cuenta_origen" id="cuenta_origen" class="form-control select2" required <?=$disabledPermiso;?>>
                                                    <option value="">-- Seleccione una cuenta --</option>
                                                    <?php
                                                    $consultaCuentas = Movimientos::listarCuentasBancarias($conexion, $config);
                                                    while($cuenta = mysqli_fetch_array($consultaCuentas, MYSQLI_BOTH)){
                                                        $saldoInfo = Movimientos::calcularSaldoCuentaBancaria($conexion, $config, $cuenta['cba_id']);
                                                        $saldoInicial = floatval($cuenta['cba_saldo_inicial'] ?? 0);
                                                        $ingresos = floatval($saldoInfo['ingresos'] ?? 0);
                                                        $egresos = floatval($saldoInfo['egresos'] ?? 0);
                                                        $transferenciasEnviadas = floatval($saldoInfo['transferencias_enviadas'] ?? 0);
                                                        $transferenciasRecibidas = floatval($saldoInfo['transferencias_recibidas'] ?? 0);
                                                        $saldoDisponible = ($saldoInicial + $ingresos) - $egresos - $transferenciasEnviadas + $transferenciasRecibidas;
                                                        $activa = ($cuenta['cba_activa'] == 1) ? '' : ' (Inactiva)';
                                                        echo '<option value="'.htmlspecialchars($cuenta['cba_id']).'" data-saldo="'.$saldoDisponible.'">'.htmlspecialchars($cuenta['cba_nombre']).' - Saldo: $'.number_format($saldoDisponible, 0, ",", ".").$activa.'</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <small class="form-text text-muted" id="saldo_origen_info"></small>
                                            </div>
                                        </div>

										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Cuenta Destino <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select name="cuenta_destino" id="cuenta_destino" class="form-control select2" required <?=$disabledPermiso;?>>
                                                    <option value="">-- Seleccione una cuenta --</option>
                                                    <?php
                                                    mysqli_data_seek($consultaCuentas, 0);
                                                    while($cuenta = mysqli_fetch_array($consultaCuentas, MYSQLI_BOTH)){
                                                        $activa = ($cuenta['cba_activa'] == 1) ? '' : ' (Inactiva)';
                                                        echo '<option value="'.htmlspecialchars($cuenta['cba_id']).'">'.htmlspecialchars($cuenta['cba_nombre']).$activa.'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Monto <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="number" name="monto" id="monto" class="form-control" step="0.01" min="0.01" required <?=$disabledPermiso;?> placeholder="0.00">
                                            </div>

                                            <label class="col-sm-2 control-label">Fecha <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="date" name="fecha" id="fecha" class="form-control" required <?=$disabledPermiso;?> value="<?=date('Y-m-d')?>" max="<?=date('Y-m-d')?>">
                                                <small class="form-text text-muted">No se permiten fechas futuras. Puede registrar transferencias pasadas.</small>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Observaciones</label>
                                            <div class="col-sm-10">
                                                <textarea name="observaciones" class="form-control" rows="3" <?=$disabledPermiso;?> placeholder="Observaciones sobre la transferencia"></textarea>
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
    <script>
        $(document).ready(function() {
            // Actualizar información de saldo cuando se selecciona cuenta origen
            $('#cuenta_origen').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var saldo = parseFloat(selectedOption.data('saldo')) || 0;
                $('#saldo_origen_info').text('Saldo disponible: $' + saldo.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
            });

            // Validar que cuenta origen y destino sean diferentes
            $('#cuenta_destino').on('change', function() {
                var cuentaOrigen = $('#cuenta_origen').val();
                var cuentaDestino = $(this).val();
                if (cuentaOrigen === cuentaDestino && cuentaOrigen !== '') {
                    alert('La cuenta origen y destino no pueden ser la misma.');
                    $(this).val('');
                }
            });

            // Validar monto no exceda saldo disponible
            $('#monto').on('blur', function() {
                var monto = parseFloat($(this).val()) || 0;
                var selectedOption = $('#cuenta_origen').find('option:selected');
                var saldoDisponible = parseFloat(selectedOption.data('saldo')) || 0;
                
                if (monto > saldoDisponible) {
                    alert('El monto excede el saldo disponible de la cuenta origen. Saldo disponible: $' + saldoDisponible.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
                    $(this).val('');
                }
            });

            // Validar fecha no sea futura
            $('#fecha').on('change', function() {
                var fechaSeleccionada = new Date($(this).val());
                var fechaActual = new Date();
                fechaActual.setHours(0, 0, 0, 0);
                
                if (fechaSeleccionada > fechaActual) {
                    alert('No se permiten fechas futuras. Puede registrar transferencias pasadas, pero no futuras.');
                    $(this).val('<?=date('Y-m-d')?>');
                }
            });

            // Validar formulario antes de enviar
            $('#formTransferir').on('submit', function(e) {
                var cuentaOrigen = $('#cuenta_origen').val();
                var cuentaDestino = $('#cuenta_destino').val();
                var monto = parseFloat($('#monto').val()) || 0;
                var fechaSeleccionada = new Date($('#fecha').val());
                var fechaActual = new Date();
                fechaActual.setHours(0, 0, 0, 0);
                
                if (cuentaOrigen === cuentaDestino) {
                    e.preventDefault();
                    alert('La cuenta origen y destino no pueden ser la misma.');
                    return false;
                }
                
                if (monto <= 0) {
                    e.preventDefault();
                    alert('El monto debe ser mayor a cero.');
                    return false;
                }
                
                // Validar fecha no sea futura
                if (fechaSeleccionada > fechaActual) {
                    e.preventDefault();
                    alert('No se permiten fechas futuras. Puede registrar transferencias pasadas, pero no futuras.');
                    return false;
                }
                
                var selectedOption = $('#cuenta_origen').find('option:selected');
                var saldoDisponible = parseFloat(selectedOption.data('saldo')) || 0;
                
                if (monto > saldoDisponible) {
                    e.preventDefault();
                    alert('El monto excede el saldo disponible de la cuenta origen. Saldo disponible: $' + saldoDisponible.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
                    return false;
                }
            });
        });
    </script>
    <!-- end js include path -->
</body>
</html>
