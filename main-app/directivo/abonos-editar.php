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

// Los abonos no se pueden editar, solo lectura
$disabledPermiso = "disabled";
$soloLectura = true;

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

// Usar un nombre de variable único para evitar conflictos con archivos incluidos
$datosAbono = Movimientos::traerDatosAbonos($conexion, $config, $id);

// Validar que se encontró el abono
if (empty($datosAbono) || !is_array($datosAbono)) {
    echo '<script type="text/javascript">alert("Abono no encontrado. ID recibido: '.htmlspecialchars($id).'"); window.location.href="abonos.php";</script>';
    exit();
}

// Debug: Verificar qué datos se están obteniendo
error_log("Datos del abono cargados - ID: {$id}, Cliente: " . ($datosAbono['invoiced'] ?? 'N/A') . ", Método: " . ($datosAbono['payment_method'] ?? 'N/A') . ", Fecha: " . ($datosAbono['registration_date'] ?? 'N/A') . ", Tipo: " . ($datosAbono['type_payments'] ?? 'N/A'));

// Si no tiene cod_payment, puede ser un abono nuevo o con datos incompletos
// No bloqueamos la página, solo registramos el problema y continuamos
if (empty($datosAbono['cod_payment'])) {
    error_log("Advertencia: Abono ID {$id} no tiene cod_payment. Datos disponibles: " . print_r(array_keys($datosAbono), true));
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
											<?php if (!empty($datosAbono['cod_payment'])) { ?>
											<span class="info-badge badge-success">Código: <?=htmlspecialchars($datosAbono['cod_payment'])?></span>
											<?php } ?>
										</h4>
									</header>
                                	<div class="panel-body" style="padding: 25px;">
									<form name="formularioGuardar" action="abonos-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" value="<?=$id?>" name="id">
										<input type="hidden" value="<?=htmlspecialchars($datosAbono['cod_payment'] ?? '')?>" name="codigoUnico" id="idAbono">
										<input type="hidden" value="<?=htmlspecialchars($datosAbono['invoiced'] ?? '')?>" name="cliente">

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[383][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <?php 
                                                $nombreResponsable = 'N/A';
                                                if (!empty($datosAbono['uss_nombre']) || !empty($datosAbono['uss_apellido1'])) {
                                                    $nombreResponsable = trim(($datosAbono['uss_nombre'] ?? '') . ' ' . ($datosAbono['uss_nombre2'] ?? '') . ' ' . ($datosAbono['uss_apellido1'] ?? '') . ' ' . ($datosAbono['uss_apellido2'] ?? ''));
                                                }
                                                ?>
                                                <input type="text" class="form-control" value="<?=htmlspecialchars($nombreResponsable)?>" readonly>
                                            </div>

                                            <label class="col-sm-2 control-label"><?=$frases[51][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <?php 
                                                $fechaFormateada = '';
                                                if (!empty($datosAbono['registration_date'])) {
                                                    // Convertir fecha de MySQL a formato datetime-local
                                                    try {
                                                        $fecha = new DateTime($datosAbono['registration_date']);
                                                        $fechaFormateada = $fecha->format('Y-m-d\TH:i');
                                                    } catch (Exception $e) {
                                                        // Si falla, usar la fecha tal cual
                                                        $fechaFormateada = $datosAbono['registration_date'];
                                                    }
                                                } else {
                                                    // Si no hay fecha, usar la fecha actual
                                                    $fechaFormateada = date('Y-m-d\TH:i');
                                                }
                                                ?>
                                                <input type="datetime-local" name="fecha" class="form-control" value="<?=$fechaFormateada?>" disabled readonly style="background-color: #f5f5f5; cursor: not-allowed;">
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
                                                if (!empty($datosAbono['invoiced'])) {
                                                    $idCliente = $datosAbono['invoiced'];
                                                    
                                                    // Intentar construir el nombre del cliente con los datos obtenidos en la consulta
                                                    $nombreCliente = trim($datosAbono['cliente_nombre'] ?? '');
                                                    
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
                                                $metodoPagoActual = trim(strtoupper($datosAbono['payment_method'] ?? ''));
                                                ?>
                                                <select class="form-control" id="metodoPago" name="metodoPago" disabled style="background-color: #f5f5f5; cursor: not-allowed;">
                                                    <?php
                                                    require_once(ROOT_PATH."/main-app/class/MediosPago.php");
                                                    echo MediosPago::generarOpcionesSelect($metodoPagoActual ?? '', true, 'Seleccione una opción');
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <label class="col-sm-2 control-label">Cuenta Bancaria</label>
                                            <div class="col-sm-2">
                                                <?php 
                                                $cuentaBancariaActual = $datosAbono['payment_cuenta_bancaria_id'] ?? '';
                                                ?>
                                                <select class="form-control select2" id="cuenta_bancaria_abono" name="cuenta_bancaria_id" disabled style="background-color: #f5f5f5; cursor: not-allowed;">
                                                    <option value="">Seleccione una cuenta (opcional)</option>
                                                    <?php
                                                    if (!empty($cuentaBancariaActual)) {
                                                        // Cargar la cuenta bancaria actual
                                                        $consultaCuenta = mysqli_query($conexion, "SELECT cba_id, cba_nombre FROM ".BD_FINANCIERA.".finanzas_cuentas_bancarias 
                                                            WHERE cba_id='".mysqli_real_escape_string($conexion, $cuentaBancariaActual)."' 
                                                            AND institucion = {$config['conf_id_institucion']} 
                                                            AND year = {$_SESSION["bd"]} 
                                                            LIMIT 1");
                                                        if ($consultaCuenta && mysqli_num_rows($consultaCuenta) > 0) {
                                                            $cuentaActual = mysqli_fetch_array($consultaCuenta, MYSQLI_BOTH);
                                                            echo '<option value="'.htmlspecialchars($cuentaActual['cba_id']).'" selected>'.htmlspecialchars($cuentaActual['cba_nombre']).'</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <label class="col-sm-1 control-label"><?=$frases[345][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-3">
                                                <?php if (!empty($datosAbono['voucher']) and file_exists(ROOT_PATH.'/main-app/files/comprobantes/' . $datosAbono['voucher'])) { ?>
                                                    <div style="margin-bottom: 8px;">
                                                        <a href="<?= REDIRECT_ROUTE; ?>/files/comprobantes/<?= $datosAbono['voucher']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fa fa-file-pdf-o"></i> Ver Comprobante Actual
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                                <input type="file" name="comprobante" class="form-control" disabled style="background-color: #f5f5f5; cursor: not-allowed;">
                                                <small class="help-block text-muted">Los abonos no se pueden editar.</small>
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
                                                            <label style="font-weight: normal; padding: 10px 20px; border: 2px solid #667eea; border-radius: 5px; display: inline-block; min-width: 120px; <?= (!empty($datosAbono['type_payments']) && $datosAbono['type_payments'] == INVOICE) ? 'background: #667eea; color: white;' : 'background: white; color: #667eea;'; ?> <?=$disabledPermiso ? 'opacity: 0.6; cursor: not-allowed;' : '';?>">
                                                                <input type="radio" name="tipoTransaccion" <?= (!empty($datosAbono['type_payments']) && $datosAbono['type_payments'] == INVOICE) ? "checked" : ""; ?> id="opt1" value="<?=SI?>" disabled style="margin-right: 8px;"> SÍ
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <label style="font-weight: normal; padding: 10px 20px; border: 2px solid #667eea; border-radius: 5px; display: inline-block; min-width: 120px; <?= (!empty($datosAbono['type_payments']) && $datosAbono['type_payments'] == ACCOUNT) ? 'background: #667eea; color: white;' : 'background: white; color: #667eea;'; ?> <?=$disabledPermiso ? 'opacity: 0.6; cursor: not-allowed;' : '';?>">
                                                                <input type="radio" name="tipoTransaccion" <?= (!empty($datosAbono['type_payments']) && $datosAbono['type_payments'] == ACCOUNT) ? "checked" : ""; ?> id="opt2" value="<?=NO?>" disabled style="margin-right: 8px;"> NO
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="alert alert-warning" style="margin-top: 15px; text-align: center;">
                                                        <i class="fa fa-lock"></i> <strong>Modo Solo Lectura:</strong> Los abonos no se pueden editar. Esta es una vista de solo lectura.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="panel" id="divFacturas" style="display: <?= (!empty($datosAbono['type_payments']) && $datosAbono['type_payments'] == INVOICE) ? 'block' : 'none'; ?>; margin-top: 20px;">
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
                                                                if (!empty($id) && !empty($datosAbono['type_payments']) && $datosAbono['type_payments'] == INVOICE) {
                                                                    try {
                                                                        // Buscar todas las facturas asociadas a este abono
                                                                        // Buscar por id del registro O por payments (si hay múltiples facturas)
                                                                        $whereClause = "pi.id='".mysqli_real_escape_string($conexion, $id)."'";
                                                                        
                                                                        $sqlFacturas = "SELECT pi.*, fc.fcu_id, fc.fcu_fecha, fc.fcu_detalle, fc.fcu_observaciones, fc.fcu_status, fc.fcu_valor
                                                                        FROM ".BD_FINANCIERA.".payments_invoiced pi
                                                                        INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_id=pi.invoiced
                                                                        WHERE ({$whereClause})
                                                                        AND pi.institucion={$config['conf_id_institucion']} 
                                                                        AND pi.year={$_SESSION["bd"]}
                                                                        AND fc.institucion={$config['conf_id_institucion']} 
                                                                        AND fc.year={$_SESSION["bd"]}
                                                                        AND pi.is_deleted=0
                                                                        AND pi.type_payments='INVOICE'
                                                                        AND pi.invoiced IS NOT NULL";
                                                                        
                                                                        $consultaFacturasAbono = mysqli_query($conexion, $sqlFacturas);
                                                                        
                                                                        // Verificar si hubo error en la consulta
                                                                        if (!$consultaFacturasAbono) {
                                                                            $errorSQL = mysqli_error($conexion);
                                                                            error_log("Error SQL al cargar facturas del abono ID {$id}: {$errorSQL}. Query: {$sqlFacturas}");
                                                                            throw new Exception("Error en la consulta SQL: " . $errorSQL);
                                                                        }
                                                                        
                                                                        if (mysqli_num_rows($consultaFacturasAbono) > 0) {
                                                                            while ($facturaAbono = mysqli_fetch_array($consultaFacturasAbono, MYSQLI_BOTH)) {
                                                                                $vlrAdicional = !empty($facturaAbono['fcu_valor']) ? floatval($facturaAbono['fcu_valor']) : 0;
                                                                                // Usar el método centralizado para obtener todos los totales desglosados
                                                                                $totalesFactura = Movimientos::calcularTotalesFactura($conexion, $config, $facturaAbono['fcu_id'], $vlrAdicional);
                                                                                $totalNeto = $totalesFactura['total_neto'];
                                                                                $abonosFactura = Movimientos::calcularTotalAbonado($conexion, $config, $facturaAbono['fcu_id']);
                                                                                $porCobrarFactura = $totalNeto - $abonosFactura;
                                                                                
                                                                                // Obtener items de la factura ordenados (débitos primero, créditos después) e incluir application_time
                                                                                $itemsFactura = [];
                                                                                try {
                                                                                    // Usar item_name, item_type y application_time de transaction_items (copia histórica)
                                                                                    // Usar tax_name y tax_fee del snapshot (preferencia) con fallback a JOIN con taxes para compatibilidad
                                                                                    $consultaItems = mysqli_query($conexion, "SELECT ti.*, ti.item_name, ti.item_type, COALESCE(ti.application_time, 'ANTE_IMPUESTO') AS application_time, COALESCE(ti.tax_name, tax.type_tax) as tax_name, COALESCE(ti.tax_fee, tax.fee) as tax_fee 
                                                                                        FROM ".BD_FINANCIERA.".transaction_items ti
                                                                                        LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id=ti.tax AND tax.institucion={$config['conf_id_institucion']} AND tax.year={$_SESSION["bd"]}
                                                                                        WHERE ti.id_transaction='{$facturaAbono['fcu_id']}' AND ti.institucion={$config['conf_id_institucion']} AND ti.year={$_SESSION["bd"]}
                                                                                        ORDER BY ti.item_type ASC, ti.id_autoincremental");
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
                                                                        <span style="border-bottom: 0.5px dashed #000; cursor:help;"><?=$facturaAbono['fcu_id'] ?? '';?></span>
                                                                    </td>
                                                                    <td><?=$facturaAbono['fcu_fecha'] ?? '';?></td>
                                                                    <td id="totalNeto<?=$facturaAbono['fcu_id'];?>" data-total-neto="<?=$totalNeto?>">$<?=number_format($totalNeto, 0, ",", ".")?></td>
                                                                    <td style="color: green;" id="abonos<?=$facturaAbono['fcu_id'];?>" data-abonos="<?=$abonosFactura?>">$<?=number_format($abonosFactura, 0, ",", ".")?></td>
                                                                    <td style="color: red;" id="porCobrar<?=$facturaAbono['fcu_id'];?>" data-por-cobrar="<?=$porCobrarFactura?>">$<?=number_format($porCobrarFactura, 0, ",", ".")?></td>
                                                                    <td>
                                                                        <input type="number" min="0" step="0.01" class="form-control" 
                                                                               value="<?=$valorAbonado?>" 
                                                                               readonly 
                                                                               style="background-color: #f5f5f5; cursor: not-allowed;"
                                                                               title="Los valores de abono no pueden ser editados. Para modificar un abono, debe eliminarlo y crear uno nuevo.">
                                                                        <small class="text-muted" style="font-size: 10px;">No editable</small>
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
                                                                                        <i class="fa fa-calculator"></i> Resumen Financiero Detallado
                                                                                    </h6>
                                                                                    <table class="table table-sm">
                                                                                        <tr>
                                                                                            <td style="width: 50%; font-weight: 600;">Subtotal Bruto:</td>
                                                                                            <td>$<?=number_format(floatval($totalesFactura['subtotal_bruto'] ?? 0), 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">(-) Descuentos de Ítems:</td>
                                                                                            <td style="color: #ff5722;">-$<?=number_format(floatval($totalesFactura['descuentos_items'] ?? 0), 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">(-) Descuentos Comerciales:</td>
                                                                                            <td style="color: #ff5722;">-$<?=number_format(floatval($totalesFactura['descuentos_comerciales_globales'] ?? 0), 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">(=) Subtotal Gravable:</td>
                                                                                            <td style="font-weight: bold;">$<?=number_format(floatval($totalesFactura['subtotal_gravable'] ?? 0), 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">(+) Impuestos:</td>
                                                                                            <td>$<?=number_format(floatval($totalesFactura['impuestos'] ?? 0), 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">(=) Total Facturado:</td>
                                                                                            <td style="font-weight: bold;">$<?=number_format(floatval($totalesFactura['total_facturado'] ?? 0), 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">(-) Anticipos/Saldos a Favor:</td>
                                                                                            <td style="color: #ff5722;">-$<?=number_format(floatval($totalesFactura['anticipos_saldos_favor'] ?? 0), 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                        <?php if (floatval($totalesFactura['valor_adicional'] ?? 0) > 0) { ?>
                                                                                        <tr>
                                                                                            <td style="font-weight: 600;">Valor Adicional:</td>
                                                                                            <td>$<?=number_format(floatval($totalesFactura['valor_adicional'] ?? 0), 0, ",", ".")?></td>
                                                                                        </tr>
                                                                                        <?php } ?>
                                                                                        <tr style="border-top: 2px solid #667eea; background: #eef2ff;">
                                                                                            <td style="font-weight: 700;">(=) Total Neto:</td>
                                                                                            <td style="font-weight: 700;">$<?=number_format($totalNeto, 0, ",", ".")?></td>
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
                                                                                            foreach ($itemsFactura as $item) {
                                                                                                $precio = floatval($item['price'] ?? 0);
                                                                                                $cantidad = floatval($item['cantity'] ?? 0);
                                                                                                $descuento = floatval($item['discount'] ?? 0);
                                                                                                $taxFee = floatval($item['tax_fee'] ?? 0);
                                                                                                $subtotal = floatval($item['subtotal'] ?? 0);
                                                                                                
                                                                                                // Determinar si es crédito o débito
                                                                                                $itemType = $item['item_type'] ?? 'D';
                                                                                                $isCredito = ($itemType == 'C');
                                                                                                $applicationTime = $item['application_time'] ?? 'ANTE_IMPUESTO';
                                                                                                $rowClass = $isCredito ? 'item-credito' : '';
                                                                                                
                                                                                                // Nombre del item: priorizar item_name, luego description
                                                                                                $nombreItem = !empty($item['item_name']) ? $item['item_name'] : ($item['description'] ?? 'N/A');
                                                                                                if ($isCredito) {
                                                                                                    $textoApplicationTime = ($applicationTime == 'POST_IMPUESTO') ? 'Después del Impuesto' : 'Antes del Impuesto';
                                                                                                    $nombreItem .= ' <small style="color: #666; font-size: 0.85em;">(Crédito - ' . $textoApplicationTime . ')</small>';
                                                                                                }
                                                                                                
                                                                                                // Signo para el subtotal
                                                                                                $signoSubtotal = $isCredito ? '-' : '';
                                                                                            ?>
                                                                                            <tr class="<?=$rowClass;?>" data-item-type="<?=$itemType;?>">
                                                                                                <td><?=$nombreItem;?></td>
                                                                                                <td><?=number_format($cantidad, 0, ",", ".")?></td>
                                                                                                <td>$<?=number_format($precio, 0, ",", ".")?></td>
                                                                                                <td><?=$isCredito ? 'N/A' : number_format($descuento, 0, ",", ".") . '%';?></td>
                                                                                                <td><?=$isCredito ? 'N/A' : ($taxFee > 0 ? number_format($taxFee, 0, ",", ".").'%' : 'N/A');?></td>
                                                                                                <td style="font-weight: bold;" data-item-type="<?=$itemType;?>"><?=$signoSubtotal?>$<?=number_format(abs($subtotal), 0, ",", ".")?></td>
                                                                                            </tr>
                                                                                            <?php } ?>
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
                                                                        $errorMsg = $e->getMessage();
                                                                        $errorSQL = mysqli_error($conexion);
                                                                        error_log("Error al cargar facturas del abono ID {$id}: " . $errorMsg . " | SQL Error: " . $errorSQL);
                                                                ?>
                                                                <tr>
                                                                    <td colspan="7" align="center" style="padding: 20px; color: #dc3545;">
                                                                        <i class="fa fa-exclamation-triangle"></i> Error al cargar las facturas asociadas.<br>
                                                                        <small style="color: #999; margin-top: 5px; display: block;"><?=htmlspecialchars($errorMsg)?></small>
                                                                        <?php if (!empty($errorSQL)) { ?>
                                                                        <small style="color: #999; margin-top: 5px; display: block;">SQL: <?=htmlspecialchars($errorSQL)?></small>
                                                                        <?php } ?>
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

                                            <div class="panel" id="divCuentasContables" style="display: <?= (!empty($datosAbono['type_payments']) && $datosAbono['type_payments'] == ACCOUNT) ? 'block' : 'none'; ?>; margin-top: 20px;">
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
                                                                if (!empty($datosAbono['cod_payment'])) {
                                                                $consultaAbonos = Movimientos::listarConceptos($conexion, $config, $datosAbono['cod_payment']);
                                                                    if ($consultaAbonos && mysqli_num_rows($consultaAbonos) > 0) {
                                                                while ($datosAbonoAbonos = mysqli_fetch_array($consultaAbonos, MYSQLI_BOTH)) {
                                                                ?>
                                                                <tr id="reg<?=$datosAbonoAbonos['id'];?>">
                                                                    <td id="idConcepto"><?=$datosAbonoAbonos['id']?></td>
                                                                    <td>
                                                                        <div style="padding: 0px;">
                                                                            <select class="form-control  select2" style="width: 100%;" id="concepto" disabled style="background-color: #f5f5f5; cursor: not-allowed;" title="Los conceptos contables no pueden ser editados.">
                                                                                <option value="">Seleccione una opción</option>
                                                                                <option value="OTROS_INGRESOS" <?=$datosAbonoAbonos['invoiced'] == "OTROS_INGRESOS" ? "selected" : "";?>>Otros Ingresos</option>
                                                                            </select>
                                                                        </div>
                                                                        <small class="text-muted" style="font-size: 10px;">No editable</small>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" min="0" step="0.01" id="precio<?=$datosAbonoAbonos['id']?>" value="<?=floatval($datosAbonoAbonos['payment'] ?? 0)?>" class="form-control" readonly style="background-color: #f5f5f5; cursor: not-allowed;" title="Los valores no pueden ser editados.">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" min="0" step="1" id="cantidad<?=$datosAbonoAbonos['id']?>" value="<?=intval($datosAbonoAbonos['cantity'] ?? 1)?>" class="form-control" style="width: 80px;" readonly style="background-color: #f5f5f5; cursor: not-allowed;" title="Las cantidades no pueden ser editadas.">
                                                                    </td>
                                                                    <td>
                                                                        <textarea id="descrip<?=$datosAbonoAbonos['id']?>" cols="30" rows="2" class="form-control" readonly style="background-color: #f5f5f5; cursor: not-allowed;" title="Las descripciones no pueden ser editadas."><?=htmlspecialchars($datosAbonoAbonos['description'] ?? '')?></textarea>
                                                                    </td>
                                                                    <td id="subtotal<?=$datosAbonoAbonos['id']?>">$<?=number_format(floatval($datosAbonoAbonos['subtotal'] ?? 0), 0, ",", ".")?></td>
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
                                                <textarea cols="80" id="editor1" name="obser" class="form-control" rows="5" placeholder="Escribe las observaciones del abono..." style="margin-top: 0px; margin-bottom: 0px; resize: vertical; background-color: #f5f5f5; cursor: not-allowed;" disabled readonly><?=htmlspecialchars($datosAbono['observation'] ?? '')?></textarea>
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
                                                <textarea cols="80" id="editor2" name="notas" class="form-control" rows="5" placeholder="Escribe notas adicionales..." style="margin-top: 0px; margin-bottom: 0px; resize: vertical; background-color: #f5f5f5; cursor: not-allowed;" disabled readonly><?=htmlspecialchars($datosAbono['note'] ?? '')?></textarea>
                                            </div>
                                        </div>
                                        
                                       <?php 
                                       // Los abonos no se pueden editar, solo mostrar botón de regresar
                                       ?>
                                       <div class="form-group row">
                                           <div class="col-sm-12 text-center">
                                               <a href="abonos.php" class="btn btn-primary">
                                                   <i class="fa fa-arrow-left"></i> Volver a Abonos
                                               </a>
                                           </div>
                                       </div>
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
        // Los abonos están en modo solo lectura, no inicializar editores
        // CKEDITOR.replace( 'editor1' );
        // CKEDITOR.replace( 'editor2' );
        
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
        
        // Función para cargar todas las cuentas bancarias activas
        // Una misma cuenta bancaria puede registrar ingresos o egresos de diferentes tipos de pago
        function cargarCuentasBancariasAbono() {
            $('#cuenta_bancaria_abono').empty().append('<option value="">Seleccione una cuenta (opcional)</option>');
            
            var cuentaActual = '<?=htmlspecialchars($cuentaBancariaActual ?? '')?>';
            
            $.ajax({
                url: 'ajax-cargar-cuentas-bancarias.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.cuentas) {
                        $.each(response.cuentas, function(index, cuenta) {
                            var selected = (cuenta.id == cuentaActual) ? 'selected' : '';
                            $('#cuenta_bancaria_abono').append(
                                $('<option></option>')
                                    .attr('value', cuenta.id)
                                    .attr('selected', selected)
                                    .text(cuenta.nombre)
                            );
                        });
                    }
                },
                error: function() {
                    console.log('Error al cargar cuentas bancarias');
                }
            });
        }
        
        // Cargar cuentas bancarias al cargar la página
        $(document).ready(function() {
            cargarCuentasBancariasAbono();
        });
    </script>
    <script src="../js/Movimientos.js"></script>
</body>
</html>