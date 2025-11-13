<?php
include("session.php");
$idPaginaInterna = 'DT0267';
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

$resultado = Movimientos::traerDatosAbonos($conexion, $config, $id);

// Validar que se encontró el abono
if (empty($resultado) || !is_array($resultado)) {
    echo '<script type="text/javascript">alert("Abono no encontrado. ID recibido: '.htmlspecialchars($id).'"); window.location.href="abonos.php";</script>';
    exit();
}

// Debug: Verificar qué datos se están obteniendo
error_log("Datos del abono cargados - ID: {$id}, Cliente: " . ($resultado['invoiced'] ?? 'N/A') . ", Método: " . ($resultado['payment_method'] ?? 'N/A') . ", Fecha: " . ($resultado['registration_date'] ?? 'N/A') . ", Tipo: " . ($resultado['type_payments'] ?? 'N/A'));

// Si no tiene cod_payment, puede ser un abono nuevo o con datos incompletos
// No bloqueamos la página, solo registramos el problema y continuamos
if (empty($resultado['cod_payment'])) {
    error_log("Advertencia: Abono ID {$id} no tiene cod_payment. Datos disponibles: " . print_r(array_keys($resultado), true));
}
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
    <!-- Estilos mejorados para abonos -->
    <link href="../css/movimientos-mejorado.css" rel="stylesheet" type="text/css" />
    <style>
        .abono-form-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .abono-form-section h4 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
        }
        .panel-heading-purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .panel-heading-blue {
            background: linear-gradient(135deg, #03a9f4 0%, #00c292 100%);
            color: white;
        }
        input[type="number"], textarea, select {
            border-radius: 4px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        input[type="number"]:focus, textarea:focus, select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .table-scrollable table {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-scrollable table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            padding: 12px;
            border: none;
        }
        .table-scrollable table tbody td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .table-scrollable table tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
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
                                <div class="page-title"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?> <?=$frases[413][$datosUsuarioActual['uss_idioma']];?></div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="abonos.php" onClick="deseaRegresar(this)"><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?> <?=$frases[413][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                        <div class="col-sm-9">
								<div class="panel abono-form-section">
									<header class="panel-heading panel-heading-purple invoice-header-section">
										<h4 style="margin: 0; color: white;">
											<i class="fa fa-edit"></i> <?=$frases[165][$datosUsuarioActual['uss_idioma']];?> <?=$frases[413][$datosUsuarioActual['uss_idioma']];?>
											<?php if (!empty($resultado['cod_payment'])) { ?>
											<span class="info-badge badge-success">Código: <?=htmlspecialchars($resultado['cod_payment'])?></span>
											<?php } ?>
										</h4>
									</header>
                                	<div class="panel-body" style="padding: 25px;">
									<form name="formularioGuardar" action="abonos-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" value="<?=$id?>" name="id">
										<input type="hidden" value="<?=htmlspecialchars($resultado['cod_payment'] ?? '')?>" name="codigoUnico" id="idAbono">
										<input type="hidden" value="<?=htmlspecialchars($resultado['invoiced'] ?? '')?>" name="cliente">

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[383][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <?php 
                                                $nombreResponsable = 'N/A';
                                                if (!empty($resultado['uss_nombre']) || !empty($resultado['uss_apellido1'])) {
                                                    $nombreResponsable = trim(($resultado['uss_nombre'] ?? '') . ' ' . ($resultado['uss_nombre2'] ?? '') . ' ' . ($resultado['uss_apellido1'] ?? '') . ' ' . ($resultado['uss_apellido2'] ?? ''));
                                                }
                                                ?>
                                                <input type="text" class="form-control" value="<?=htmlspecialchars($nombreResponsable)?>" readonly>
                                            </div>

                                            <label class="col-sm-2 control-label"><?=$frases[51][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <?php 
                                                $fechaFormateada = '';
                                                if (!empty($resultado['registration_date'])) {
                                                    // Convertir fecha de MySQL a formato datetime-local
                                                    try {
                                                        $fecha = new DateTime($resultado['registration_date']);
                                                        $fechaFormateada = $fecha->format('Y-m-d\TH:i');
                                                    } catch (Exception $e) {
                                                        // Si falla, usar la fecha tal cual
                                                        $fechaFormateada = $resultado['registration_date'];
                                                    }
                                                } else {
                                                    // Si no hay fecha, usar la fecha actual
                                                    $fechaFormateada = date('Y-m-d\TH:i');
                                                }
                                                ?>
                                                <input type="datetime-local" name="fecha" class="form-control" value="<?=$fechaFormateada?>" <?=$disabledPermiso;?> required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[424][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span>
                                                <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="El cliente no puede ser modificado al editar un abono existente."><i class="fa fa-info"></i></button>
                                            </label>
                                            <div class="col-sm-10">
                                                <?php
                                                $nombreCliente = 'N/A';
                                                $idCliente = '';
                                                
                                                // Obtener el ID del cliente desde el abono
                                                if (!empty($resultado['invoiced'])) {
                                                    $idCliente = $resultado['invoiced'];
                                                    
                                                    // Intentar construir el nombre del cliente con los datos obtenidos en la consulta
                                                    $nombreCliente = trim($resultado['cliente_nombre'] ?? '');
                                                    
                                                    // Si aún no tenemos nombre, intentamos obtenerlo directamente de la base
                                                    if ($nombreCliente === '') {
                                                        try {
                                                            $datosClienteFetch = UsuariosPadre::sesionUsuario($idCliente);
                                                            if (!empty($datosClienteFetch) && is_array($datosClienteFetch)) {
                                                                $nombreCliente = UsuariosPadre::nombreCompletoDelUsuario($datosClienteFetch);
                                                            }
                                                        } catch (Exception $e) {
                                                            error_log("Error al obtener datos del cliente (fallback): " . $e->getMessage());
                                                        }
                                                    }
                                                    
                                                    if ($nombreCliente === '') {
                                                        $nombreCliente = "Usuario ID: {$idCliente} (No encontrado)";
                                                    }
                                                } else {
                                                    $nombreCliente = "No asignado";
                                                }
                                                ?>
                                                <input type="hidden" name="cliente" value="<?=htmlspecialchars($idCliente)?>" id="clienteId">
                                                <input type="text" class="form-control" value="<?=htmlspecialchars($nombreCliente)?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                                                <small class="help-block text-muted">El cliente no puede ser modificado al editar un abono existente.</small>
                                            </div>
                                        </div>

                                        <script>
                                            $(document).ready(function() {
                                                mostrarTipoTransaccion();
                                                
                                                // Verificar si ya hay facturas cargadas en el PHP (tipo INVOICE)
                                                var facturasCargadas = $('#mostrarFacturas tr.factura-row').length > 0;
                                                
                                                // Cargar facturas o conceptos según el tipo de transacción seleccionado
                                                var radios = document.getElementsByName('tipoTransaccion');
                                                
                                                for (var i = 0; i < radios.length; i++) {
                                                    if (radios[i].checked) {
                                                        var tipoSeleccionado = i + 1;
                                                        
                                                        // Si es tipo INVOICE y ya hay facturas cargadas, no recargar
                                                        if (tipoSeleccionado == 1 && facturasCargadas) {
                                                            // Solo mostrar el div de facturas, no recargar
                                                            document.getElementById("divFacturas").style.display="block";
                                                            document.getElementById("divCuentasContables").style.display="none";
                                                            // Actualizar el resumen
                                                            setTimeout(function() {
                                                                if (typeof totalizarAbonos === 'function') {
                                                                    totalizarAbonos();
                                                                }
                                                            }, 200);
                                                            break;
                                                        }
                                                        
                                                        // Pequeño delay para asegurar que el DOM esté listo
                                                        setTimeout(function(tipo) {
                                                            return function() {
                                                                tipoAbono(tipo);
                                                            };
                                                        }(tipoSeleccionado), 800);
                                                        break;
                                                    }
                                                }
                                        
                                        // Recalcular totales si ya vienen facturas desde PHP
                                        setTimeout(function() {
                                            if (typeof totalizarAbonos === 'function') {
                                                totalizarAbonos();
                                            }
                                        }, 300);
                                            });
                                            // El cliente ya no es editable, así que no necesitamos Select2 aquí
                                        </script>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[414][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <?php 
                                                $metodoPagoActual = trim(strtoupper($resultado['payment_method'] ?? ''));
                                                ?>
                                                <select class="form-control" id="metodoPago" name="metodoPago" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="EFECTIVO" <?= ($metodoPagoActual == "EFECTIVO") ? "selected" : ""; ?>>Efectivo</option>
                                                    <option value="CHEQUE" <?= ($metodoPagoActual == "CHEQUE") ? "selected" : ""; ?>>Cheque</option>
                                                    <option value="T_DEBITO" <?= ($metodoPagoActual == "T_DEBITO") ? "selected" : ""; ?>>T. Débito</option>
                                                    <option value="T_CREDITO" <?= ($metodoPagoActual == "T_CREDITO") ? "selected" : ""; ?>>T. Crédito</option>
                                                    <option value="TRANSFERENCIA" <?= ($metodoPagoActual == "TRANSFERENCIA") ? "selected" : ""; ?>>Transferencia</option>
                                                    <option value="OTROS" <?= ($metodoPagoActual == "OTROS") ? "selected" : ""; ?>>Otras Formas de pago</option>
                                                </select>
                                            </div>
                                            
                                            <label class="col-sm-2 control-label"><?=$frases[345][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <?php if (!empty($resultado['voucher']) and file_exists(ROOT_PATH.'/main-app/files/comprobantes/' . $resultado['voucher'])) { ?>
                                                    <div style="margin-bottom: 8px;">
                                                        <a href="<?= REDIRECT_ROUTE; ?>/files/comprobantes/<?= $resultado['voucher']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fa fa-file-pdf-o"></i> Ver Comprobante Actual
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                                <input type="file" name="comprobante" class="form-control" <?=$disabledPermiso;?>>
                                                <small class="help-block">Dejar vacío para mantener el comprobante actual</small>
                                            </div>
										</div>

                                        <div id="divTipoTransaccion" style="display: block;">
                                            <div class="panel" style="margin-top: 20px;">
                                                <header class="panel-heading panel-heading-blue">
                                                    <h5 style="margin: 0; color: white;">
                                                        <i class="fa fa-exchange"></i> Tipo de Transacción
                                                    </h5>
                                                </header>
                                                <div class="panel-body" style="text-align: center; padding: 25px;">
                                                    <span style="font-size: 17px; color: #333;">Ajustar este ingreso a una <b>factura de venta</b> existente en el sistema?</span><br>
                                                    <small style="color: #666;">Recuerda que puedes registrar un ingreso sin necesidad de que este asociado a una factura de venta</small><br><br>
                                                
                                                    <div class="form-group row" style="align-items: center; justify-content: center;">
                                                        <div class="col-sm-3">
                                                            <label style="font-weight: normal; cursor: pointer; padding: 10px 20px; border: 2px solid #667eea; border-radius: 5px; display: inline-block; min-width: 120px; <?= (!empty($resultado['type_payments']) && $resultado['type_payments'] == INVOICE) ? 'background: #667eea; color: white;' : 'background: white; color: #667eea;'; ?>">
                                                                <input type="radio" name="tipoTransaccion" <?= (!empty($resultado['type_payments']) && $resultado['type_payments'] == INVOICE) ? "checked" : ""; ?> id="opt1" value="<?=SI?>" onClick="tipoAbono(1)" <?=$disabledPermiso;?> style="margin-right: 8px;"> SÍ
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <label style="font-weight: normal; cursor: pointer; padding: 10px 20px; border: 2px solid #667eea; border-radius: 5px; display: inline-block; min-width: 120px; <?= (!empty($resultado['type_payments']) && $resultado['type_payments'] == ACCOUNT) ? 'background: #667eea; color: white;' : 'background: white; color: #667eea;'; ?>">
                                                                <input type="radio" name="tipoTransaccion" <?= (!empty($resultado['type_payments']) && $resultado['type_payments'] == ACCOUNT) ? "checked" : ""; ?> id="opt2" value="<?=NO?>" onClick="tipoAbono(2)" <?=$disabledPermiso;?> style="margin-right: 8px;"> NO
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="panel" id="divFacturas" style="display: <?= (!empty($resultado['type_payments']) && $resultado['type_payments'] == INVOICE) ? 'block' : 'none'; ?>; margin-top: 20px;">
                                                <header class="panel-heading panel-heading-blue">
                                                    <h5 style="margin: 0; color: white;">
                                                        <i class="fa fa-file-text-o"></i> Facturas Asociadas a este Abono
                                                    </h5>
                                                </header>
                                                <div class="panel-body" style="padding: 20px;">

                                                    <div class="table-scrollable">
                                                        <table class="display" style="width:100%;" id="tablaItems">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 40px;"></th>
                                                                    <th>Cod. Factura</th>
                                                                    <th>Fecha</th>
                                                                    <th><?=$frases[107][$datosUsuarioActual['uss_idioma']];?></th>
                                                                    <th><?=$frases[417][$datosUsuarioActual['uss_idioma']];?></th>
                                                                    <th><?=$frases[418][$datosUsuarioActual['uss_idioma']];?></th>
                                                                    <th>Valor Abonado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="mostrarFacturas">
                                                                <?php
                                                                // Cargar facturas asociadas a este abono
                                                                if (!empty($resultado['cod_payment']) && !empty($resultado['type_payments']) && $resultado['type_payments'] == INVOICE) {
                                                                    try {
                                                                        $consultaFacturasAbono = mysqli_query($conexion, "SELECT pi.*, fc.fcu_id, fc.fcu_fecha, fc.fcu_detalle, fc.fcu_observaciones, fc.fcu_status, fc.id_nuevo,
                                                                        fc.fcu_valor
                                                                        FROM ".BD_FINANCIERA.".payments_invoiced pi
                                                                        INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_id=pi.invoiced
                                                                        WHERE pi.payments='{$resultado['cod_payment']}' 
                                                                        AND pi.institucion={$config['conf_id_institucion']} 
                                                                        AND pi.year={$_SESSION["bd"]}
                                                                        AND fc.institucion={$config['conf_id_institucion']} 
                                                                        AND fc.year={$_SESSION["bd"]}");
                                                                        
                                                                        if ($consultaFacturasAbono && mysqli_num_rows($consultaFacturasAbono) > 0) {
                                                                            while ($facturaAbono = mysqli_fetch_array($consultaFacturasAbono, MYSQLI_BOTH)) {
                                                                                $vlrAdicional = !empty($facturaAbono['fcu_valor']) ? floatval($facturaAbono['fcu_valor']) : 0;
                                                                                $totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $facturaAbono['fcu_id'], $vlrAdicional);
                                                                                $abonosFactura = Movimientos::calcularTotalAbonado($conexion, $config, $facturaAbono['fcu_id']);
                                                                                $porCobrarFactura = $totalNeto - $abonosFactura;
                                                                                
                                                                                // Obtener items de la factura
                                                                                $itemsFactura = [];
                                                                                try {
                                                                                    $consultaItems = mysqli_query($conexion, "SELECT ti.*, tax.fee as tax_fee, tax.name as tax_name 
                                                                                        FROM ".BD_FINANCIERA.".transaction_items ti
                                                                                        LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id=ti.tax AND tax.institucion={$config['conf_id_institucion']} AND tax.year={$_SESSION["bd"]}
                                                                                        WHERE ti.id_transaction='{$facturaAbono['fcu_id']}' AND ti.institucion={$config['conf_id_institucion']} AND ti.year={$_SESSION["bd"]}");
                                                                                    if ($consultaItems) {
                                                                                        while ($item = mysqli_fetch_array($consultaItems, MYSQLI_BOTH)) {
                                                                                            $itemsFactura[] = $item;
                                                                                        }
                                                                                    }
                                                                                } catch (Exception $e) {
                                                                                    // Continuar sin items si hay error
                                                                                }
                                                                                
                                                                                $detalleFactura = htmlspecialchars($facturaAbono['fcu_detalle'] ?? '');
                                                                                $observacionesFactura = htmlspecialchars($facturaAbono['fcu_observaciones'] ?? '');
                                                                                $valorAbonado = floatval($facturaAbono['payment'] ?? 0);
                                                                ?>
                                                                <tr id="reg<?=$facturaAbono['fcu_id'];?>" class="factura-row">
                                                                    <td>
                                                                        <i class="fa fa-chevron-right expand-btn" onclick="toggleFacturaDetails('<?=$facturaAbono['fcu_id'];?>')" id="expand<?=$facturaAbono['fcu_id'];?>"></i>
                                                                    </td>
                                                                    <td title="<?=$detalleFactura;?>">
                                                                        <span style="border-bottom: 0.5px dashed #000; cursor:help;"><?=$facturaAbono['id_nuevo'] ?? '';?></span>
                                                                    </td>
                                                                    <td><?=$facturaAbono['fcu_fecha'] ?? '';?></td>
                                                                    <td id="totalNeto<?=$facturaAbono['fcu_id'];?>" data-total-neto="<?=$totalNeto?>">$<?=number_format($totalNeto, 0, ",", ".")?></td>
                                                                    <td style="color: green;" id="abonos<?=$facturaAbono['fcu_id'];?>" data-abonos="<?=$abonosFactura?>">$<?=number_format($abonosFactura, 0, ",", ".")?></td>
                                                                    <td style="color: red;" id="porCobrar<?=$facturaAbono['fcu_id'];?>" data-por-cobrar="<?=$porCobrarFactura?>">$<?=number_format($porCobrarFactura, 0, ",", ".")?></td>
                                                                    <td>
                                                                        <input type="number" min="0" step="0.01" class="form-control" 
                                                                               onchange="actualizarAbonado(this)" 
                                                                               data-id-factura="<?=$facturaAbono['fcu_id'];?>" 
                                                                               data-id-abono="<?=$resultado['cod_payment'];?>" 
                                                                               data-abono-anterior="<?=$valorAbonado?>" 
                                                                               value="<?=$valorAbonado?>" 
                                                                               <?=$disabledPermiso;?>>
                                                                    </td>
                                                                </tr>
                                                                <tr class="factura-details-row" id="details<?=$facturaAbono['fcu_id'];?>">
                                                                    <td colspan="7">
                                                                        <div class="factura-details-content">
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <h6 style="color: #667eea; font-weight: 600; margin-bottom: 10px;">
                                                                                        <i class="fa fa-info-circle"></i> Información de la Factura
                                                                                    </h6>
                                                                                    <table class="table table-sm" style="margin-bottom: 15px;">
                                                                                        <tr>
                                                                                            <td style="width: 40%; font-weight: 600;">Detalle:</td>
                                                                                            <td><?=$detalleFactura ?: 'N/A';?></td>
                                                                                        </tr>
                                                                                        <?php if (!empty($observacionesFactura)) { ?>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">Observaciones:</td>
                                                                                            <td><?=$observacionesFactura;?></td>
                                                                                        </tr>
                                                                                        <?php } ?>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">Estado:</td>
                                                                                            <td>
                                                                                                <?php 
                                                                                                $estado = $facturaAbono['fcu_status'] ?? '';
                                                                                                $badgeClass = ($estado == 'COBRADA') ? 'badge-success' : 'badge-warning';
                                                                                                $estadoTexto = ($estado == 'COBRADA') ? 'Cobrada' : 'Por Cobrar';
                                                                                                ?>
                                                                                                <span class="badge <?=$badgeClass?>"><?=$estadoTexto?></span>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <h6 style="color: #667eea; font-weight: 600; margin-bottom: 10px;">
                                                                                        <i class="fa fa-calculator"></i> Resumen Financiero
                                                                                    </h6>
                                                                                    <table class="table table-sm">
                                                                                        <tr>
                                                                                            <td style="width: 50%; font-weight: 600;">Total Neto:</td>
                                                                                            <td style="font-weight: bold;">$<?=number_format($totalNeto, 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">Total Abonado:</td>
                                                                                            <td style="color: green; font-weight: bold;">$<?=number_format($abonosFactura, 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">Por Cobrar:</td>
                                                                                            <td style="color: red; font-weight: bold;">$<?=number_format($porCobrarFactura, 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <?php if (!empty($itemsFactura)) { ?>
                                                                            <div class="row" style="margin-top: 15px;">
                                                                                <div class="col-md-12">
                                                                                    <h6 style="color: #667eea; font-weight: 600; margin-bottom: 10px;">
                                                                                        <i class="fa fa-list"></i> Items de la Factura
                                                                                    </h6>
                                                                                    <table class="table table-sm table-bordered">
                                                                                        <thead style="background: #f8f9fa;">
                                                                                            <tr>
                                                                                                <th>Descripción</th>
                                                                                                <th>Cantidad</th>
                                                                                                <th>Precio Unit.</th>
                                                                                                <th>Descuento</th>
                                                                                                <th>Impuesto</th>
                                                                                                <th>Subtotal</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <?php 
                                                                                            $subtotalItems = 0;
                                                                                            foreach ($itemsFactura as $item) {
                                                                                                $precio = floatval($item['price'] ?? 0);
                                                                                                $cantidad = floatval($item['cantity'] ?? 0);
                                                                                                $descuento = floatval($item['discount'] ?? 0);
                                                                                                $taxFee = floatval($item['tax_fee'] ?? 0);
                                                                                                $subtotal = $precio * $cantidad * (1 - $descuento / 100);
                                                                                                if ($item['tax'] != 0 && $taxFee > 0) {
                                                                                                    $subtotal = $subtotal * (1 + $taxFee / 100);
                                                                                                }
                                                                                                $subtotalItems += $subtotal;
                                                                                            ?>
                                                                                            <tr>
                                                                                                <td><?=htmlspecialchars($item['description'] ?? 'N/A');?></td>
                                                                                                <td><?=number_format($cantidad, 0, ",", ".")?></td>
                                                                                                <td>$<?=number_format($precio, 0, ",", ".")?></td>
                                                                                                <td><?=number_format($descuento, 0, ",", ".")?>%</td>
                                                                                                <td><?=$taxFee > 0 ? number_format($taxFee, 0, ",", ".").'%' : 'N/A';?></td>
                                                                                                <td style="font-weight: bold;">$<?=number_format($subtotal, 0, ",", ".")?></td>
                                                                                            </tr>
                                                                                            <?php } ?>
                                                                                            <tr style="background: #f8f9fa; font-weight: bold;">
                                                                                                <td colspan="5" align="right">Total Items:</td>
                                                                                                <td>$<?=number_format($subtotalItems, 0, ",", ".")?></td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                            }
                                                                        } else {
                                                                ?>
                                                                <tr>
                                                                    <td colspan="7" align="center" style="padding: 20px; color: #999;">
                                                                        <i class="fa fa-info-circle"></i> No hay facturas asociadas a este abono.
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                        }
                                                                    } catch (Exception $e) {
                                                                        error_log("Error al cargar facturas del abono: " . $e->getMessage());
                                                                ?>
                                                                <tr>
                                                                    <td colspan="7" align="center" style="padding: 20px; color: #999;">
                                                                        <i class="fa fa-exclamation-triangle"></i> Error al cargar las facturas asociadas.
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                    }
                                                                } else {
                                                                ?>
                                                                <tr>
                                                                    <td colspan="7" align="center" style="padding: 20px; color: #999;">
                                                                        <i class="fa fa-info-circle"></i> Seleccione un tipo de transacción para ver las facturas.
                                                                    </td>
                                                                </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="panel" id="divCuentasContables" style="display: <?= (!empty($resultado['type_payments']) && $resultado['type_payments'] == ACCOUNT) ? 'block' : 'none'; ?>; margin-top: 20px;">
                                                <header class="panel-heading panel-heading-blue">
                                                    <h5 style="margin: 0; color: white;">
                                                        <i class="fa fa-list"></i> A qué cuentas contables pertenece este ingreso?
                                                    </h5>
                                                </header>
                                                <div class="panel-body" style="padding: 20px;">

                                                    <div class="table-scrollable">
                                                        <table class="display" style="width:100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Concepto</th>
                                                                    <th>Valor</th>
                                                                    <th>Cant.</th>
                                                                    <th>Descripción</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                // Validar que cod_payment existe antes de usarlo
                                                                if (!empty($resultado['cod_payment'])) {
                                                                $consultaAbonos = Movimientos::listarConceptos($conexion, $config, $resultado['cod_payment']);
                                                                    if ($consultaAbonos && mysqli_num_rows($consultaAbonos) > 0) {
                                                                while ($resultadoAbonos = mysqli_fetch_array($consultaAbonos, MYSQLI_BOTH)) {
                                                                ?>
                                                                <tr id="reg<?=$resultadoAbonos['id'];?>">
                                                                    <td id="idConcepto"><?=$resultadoAbonos['id']?></td>
                                                                    <td>
                                                                        <div style="padding: 0px;">
                                                                            <select class="form-control  select2" style="width: 100%;" id="concepto" onchange="guardarNuevoConcepto(this)" <?=$disabledPermiso;?>>
                                                                                <option value="">Seleccione una opción</option>
                                                                                <option value="OTROS_INGRESOS" <?=$resultadoAbonos['invoiced'] == "OTROS_INGRESOS" ? "selected" : "";?>>Otros Ingresos</option>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" min="0" step="0.01" id="precio<?=$resultadoAbonos['id']?>" data-precio="<?=floatval($resultadoAbonos['payment'] ?? 0)?>" onchange="actualizarSubtotalConceptos('<?=$resultadoAbonos['id']?>')" value="<?=floatval($resultadoAbonos['payment'] ?? 0)?>" class="form-control" <?=$disabledPermiso;?>>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" min="0" step="1" id="cantidad<?=$resultadoAbonos['id']?>" data-cantidad="<?=intval($resultadoAbonos['cantity'] ?? 1)?>" onchange="actualizarSubtotalConceptos('<?=$resultadoAbonos['id']?>')" value="<?=intval($resultadoAbonos['cantity'] ?? 1)?>" class="form-control" style="width: 80px;" <?=$disabledPermiso;?>>
                                                                    </td>
                                                                    <td>
                                                                        <textarea id="descrip<?=$resultadoAbonos['id']?>" cols="30" rows="2" onchange="guardarDescripcionConcepto('<?=$resultadoAbonos['id']?>')" class="form-control" <?=$disabledPermiso;?>><?=htmlspecialchars($resultadoAbonos['description'] ?? '')?></textarea>
                                                                    </td>
                                                                    <td id="subtotal<?=$resultadoAbonos['id']?>">$<?=number_format(floatval($resultadoAbonos['subtotal'] ?? 0), 0, ",", ".")?></td>
                                                                </tr>
                                                                <?php 
                                                                        }
                                                                    } else {
                                                                ?>
                                                                <tr>
                                                                    <td colspan="6" align="center" style="padding: 20px; color: #999;">
                                                                        <i class="fa fa-info-circle"></i> No hay conceptos asociados a este abono.
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                    }
                                                                } else {
                                                                ?>
                                                                <tr>
                                                                    <td colspan="6" align="center" style="padding: 20px; color: #999;">
                                                                        <i class="fa fa-info-circle"></i> No hay código de pago disponible.
                                                                    </td>
                                                                </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">
                                                <?=$frases[109][$datosUsuarioActual['uss_idioma']];?>
                                                <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Observaciones generales del abono">
                                                    <i class="fa fa-info-circle"></i>
                                                </button>
                                            </label>
                                            <div class="col-sm-10">
                                                <textarea cols="80" id="editor1" name="obser" class="form-control" rows="5" placeholder="Escribe las observaciones del abono..." style="margin-top: 0px; margin-bottom: 0px; resize: vertical;" <?=$disabledPermiso;?>><?=htmlspecialchars($resultado['observation'] ?? '')?></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">
                                                <?=$frases[416][$datosUsuarioActual['uss_idioma']];?>
                                                <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Estas notas no se verán reflejadas en el comprobante.">
                                                    <i class="fa fa-info-circle"></i>
                                                </button>
                                            </label>
                                            <div class="col-sm-10">
                                                <textarea cols="80" id="editor2" name="notas" class="form-control" rows="5" placeholder="Escribe notas adicionales..." style="margin-top: 0px; margin-bottom: 0px; resize: vertical;" <?=$disabledPermiso;?>><?=htmlspecialchars($resultado['note'] ?? '')?></textarea>
                                            </div>
                                        </div>
                                        
                                       <?php $botones = new botonesGuardar("abonos.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-3">
                            <div class="panel">
                                <header class="panel-heading panel-heading-blue">TOTAL</header>
                                <div class="panel-body">
                                    <table style="width: 100%;" align="center">
                                        <tr>
                                            <td style="padding-right: 20px;">TOTAL:</td>
                                            <td align="left" id="totalNeto">$0</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 20px;">TOTAL. ABONOS:</td>
                                            <td align="left" id="abonosNeto">$0</td>
                                        </tr>
                                        <tr style="font-size: 15px; font-weight:bold;">
                                            <td style="padding-right: 20px;">TOTAL POR COBRAR:</td>
                                            <td align="left" id="porCobrarNeto">$0</td>
                                        </tr>
                                    </table>
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
    <!-- end js include path -->
    <script src="../ckeditor/ckeditor.js"></script>

    <script>
        CKEDITOR.replace( 'editor1' );
        CKEDITOR.replace( 'editor2' );
        
        // Función global para expandir/contraer detalles de facturas
        function toggleFacturaDetails(facturaId) {
            var detailsRow = document.getElementById('details' + facturaId);
            var expandBtn = document.getElementById('expand' + facturaId);
            
            if (detailsRow && expandBtn) {
                if (detailsRow.classList.contains('show')) {
                    detailsRow.classList.remove('show');
                    expandBtn.classList.remove('expanded');
                } else {
                    detailsRow.classList.add('show');
                    expandBtn.classList.add('expanded');
                }
            }
        }
    </script>
    <script src="../js/Movimientos.js"></script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>