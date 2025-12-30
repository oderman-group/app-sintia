<?php
include("session.php");
$idPaginaInterna = 'DT0264';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
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
                                <div class="page-title"><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li class="active"><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                            
                            <?php
                                            // Calcular resúmenes por método de pago y por tipo de factura (venta/compra)
                                            $resumenMetodos = [];
                                            $totalGeneralVentas = 0;
                                            $totalGeneralCompras = 0;
                                            $totalAbonosVentas = 0;
                                            $totalAbonosCompras = 0;
                                            
                                            try {
                                                // Resumen por método de pago (abonos a facturas de venta)
                                                $consultaResumenVentas = mysqli_query($conexion, "SELECT 
                                                    pi.payment_method,
                                                    COUNT(DISTINCT pi.id) as cantidad_abonos,
                                                    SUM(COALESCE(CAST(pi.payment AS DECIMAL(10,2)), 0)) as total_abonado
                                                FROM ".BD_FINANCIERA.".payments_invoiced pi
                                                INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_id=pi.invoiced
                                                    AND fc.institucion=pi.institucion AND fc.year=pi.year
                                                WHERE pi.is_deleted = 0 
                                                    AND pi.institucion = {$config['conf_id_institucion']} 
                                                    AND pi.year = {$_SESSION["bd"]}
                                                    AND pi.type_payments = 'INVOICE'
                                                    AND fc.fcu_tipo = 1
                                                GROUP BY pi.payment_method
                                                ORDER BY total_abonado DESC");
                                                
                                                if ($consultaResumenVentas) {
                                                    while ($resumen = mysqli_fetch_array($consultaResumenVentas, MYSQLI_BOTH)) {
                                                        $metodo = $resumen['payment_method'] ?? 'Sin método';
                                                        $cantidad = intval($resumen['cantidad_abonos']);
                                                        $total = floatval($resumen['total_abonado']);
                                                        
                                                        if (!isset($resumenMetodos[$metodo])) {
                                                            $resumenMetodos[$metodo] = [
                                                                'cantidad_ventas' => 0,
                                                                'total_ventas' => 0,
                                                                'cantidad_compras' => 0,
                                                                'total_compras' => 0
                                                            ];
                                                        }
                                                        
                                                        $resumenMetodos[$metodo]['cantidad_ventas'] += $cantidad;
                                                        $resumenMetodos[$metodo]['total_ventas'] += $total;
                                                        
                                                        $totalGeneralVentas += $total;
                                                        $totalAbonosVentas += $cantidad;
                                                    }
                                                }
                                                
                                                // Resumen por método de pago (abonos a facturas de compra)
                                                $consultaResumenCompras = mysqli_query($conexion, "SELECT 
                                                    pi.payment_method,
                                                    COUNT(DISTINCT pi.id) as cantidad_abonos,
                                                    SUM(COALESCE(CAST(pi.payment AS DECIMAL(10,2)), 0)) as total_abonado
                                                FROM ".BD_FINANCIERA.".payments_invoiced pi
                                                INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_id=pi.invoiced
                                                    AND fc.institucion=pi.institucion AND fc.year=pi.year
                                                WHERE pi.is_deleted = 0 
                                                    AND pi.institucion = {$config['conf_id_institucion']} 
                                                    AND pi.year = {$_SESSION["bd"]}
                                                    AND pi.type_payments = 'INVOICE'
                                                    AND fc.fcu_tipo = 2
                                                GROUP BY pi.payment_method
                                                ORDER BY total_abonado DESC");
                                                
                                                if ($consultaResumenCompras) {
                                                    while ($resumen = mysqli_fetch_array($consultaResumenCompras, MYSQLI_BOTH)) {
                                                        $metodo = $resumen['payment_method'] ?? 'Sin método';
                                                        $cantidad = intval($resumen['cantidad_abonos']);
                                                        $total = floatval($resumen['total_abonado']);
                                                        
                                                        if (!isset($resumenMetodos[$metodo])) {
                                                            $resumenMetodos[$metodo] = [
                                                                'cantidad_ventas' => 0,
                                                                'total_ventas' => 0,
                                                                'cantidad_compras' => 0,
                                                                'total_compras' => 0
                                                            ];
                                                        }
                                                        
                                                        $resumenMetodos[$metodo]['cantidad_compras'] += $cantidad;
                                                        $resumenMetodos[$metodo]['total_compras'] += $total;
                                                        
                                                        $totalGeneralCompras += $total;
                                                        $totalAbonosCompras += $cantidad;
                                                    }
                                                }
                                                
                                                $totalGeneral = $totalGeneralVentas + $totalGeneralCompras;
                                                $totalAbonos = $totalAbonosVentas + $totalAbonosCompras;
                                                $diferencia = $totalGeneralVentas - $totalGeneralCompras; // Diferencia entre ingresos y egresos
                                                
                                            } catch (Exception $e) {
                                                include("../compartido/error-catch-to-report.php");
                                            }
                                            ?>

                            <!-- Resumen de Abonos - Cards principales -->
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col-md-3">
                                    <div style="background: #ffffff; padding: 20px; border-radius: 10px; text-align: center; border: 2px solid #2ecc71; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                        <div style="color: #2ecc71; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin-bottom: 5px;">
                                            <i class="fa fa-arrow-circle-down"></i> INGRESOS
                                        </div>
                                        <div style="color: #7f8c8d; font-size: 10px; font-weight: 600; margin-bottom: 10px;">(Abonos a Facturas de Venta)</div>
                                        <div style="color: #2ecc71; font-size: 28px; font-weight: 700; margin-top: 5px;">$<?=number_format(floatval($totalGeneralVentas ?? 0), 0, ",", ".")?></div>
                                        <div style="color: #95a5a6; font-size: 12px; margin-top: 8px;"><?=$totalAbonosVentas?> abonos</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div style="background: #ffffff; padding: 20px; border-radius: 10px; text-align: center; border: 2px solid #e74c3c; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                        <div style="color: #e74c3c; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin-bottom: 5px;">
                                            <i class="fa fa-arrow-circle-up"></i> EGRESOS
                                        </div>
                                        <div style="color: #7f8c8d; font-size: 10px; font-weight: 600; margin-bottom: 10px;">(Abonos a Facturas de Compra)</div>
                                        <div style="color: #e74c3c; font-size: 28px; font-weight: 700; margin-top: 5px;">$<?=number_format(floatval($totalGeneralCompras ?? 0), 0, ",", ".")?></div>
                                        <div style="color: #95a5a6; font-size: 12px; margin-top: 8px;"><?=$totalAbonosCompras?> abonos</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div style="background: #ffffff; padding: 20px; border-radius: 10px; text-align: center; border: 2px solid #3498db; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                        <div style="color: #3498db; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin-bottom: 5px;">
                                            <i class="fa fa-calculator"></i> TOTAL GENERAL MOVIDO
                                        </div>
                                        <div style="color: #7f8c8d; font-size: 10px; font-weight: 600; margin-bottom: 10px;">(Ingresos + Egresos)</div>
                                        <div style="color: #3498db; font-size: 28px; font-weight: 700; margin-top: 5px;">$<?=number_format(floatval($totalGeneral ?? 0), 0, ",", ".")?></div>
                                        <div style="color: #95a5a6; font-size: 12px; margin-top: 8px;"><?=$totalAbonos?> abonos</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div style="background: #ffffff; padding: 20px; border-radius: 10px; text-align: center; border: 2px solid <?=$diferencia >= 0 ? '#2ecc71' : '#e74c3c'?>; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                        <div style="color: <?=$diferencia >= 0 ? '#2ecc71' : '#e74c3c'?>; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin-bottom: 5px;">
                                            <i class="fa fa-balance-scale"></i> DIFERENCIA
                                        </div>
                                        <div style="color: #7f8c8d; font-size: 10px; font-weight: 600; margin-bottom: 10px;">(Ingresos - Egresos)</div>
                                        <div style="color: <?=$diferencia >= 0 ? '#2ecc71' : '#e74c3c'?>; font-size: 28px; font-weight: 700; margin-top: 5px;">$<?=number_format(floatval($diferencia ?? 0), 0, ",", ".")?></div>
                                        <div style="color: #95a5a6; font-size: 12px; margin-top: 8px;"><?=$diferencia >= 0 ? 'Superávit' : 'Déficit'?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen por métodos de pago -->
                            <?php if (!empty($resumenMetodos)) { ?>
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col-sm-12">
                                    <h5 style="margin-bottom: 15px; font-weight: 600; color: #2c3e50;">
                                        <i class="fa fa-bar-chart"></i> Resumen por Método de Pago
                                    </h5>
                            <div class="row">
                                        <?php 
                                        $coloresBorder = ['#3498db', '#f39c12', '#9b59b6', '#e67e22', '#1abc9c'];
                                        $i = 0;
                                        foreach ($resumenMetodos as $metodo => $datos) { 
                                            $colorBorder = $coloresBorder[$i % count($coloresBorder)];
                                            $totalMetodo = ($datos['total_ventas'] ?? 0) + ($datos['total_compras'] ?? 0);
                                            $cantidadMetodo = ($datos['cantidad_ventas'] ?? 0) + ($datos['cantidad_compras'] ?? 0);
                                            $porcentaje = $totalGeneral > 0 ? round(($totalMetodo / $totalGeneral) * 100, 1) : 0;
                                            
                                            // Calcular diferencia (ingresos - egresos)
                                            $diferenciaMetodo = ($datos['total_ventas'] ?? 0) - ($datos['total_compras'] ?? 0);
                                            
                                            // Determinar tipo: Ingresos, Egresos o Mixto
                                            $tieneVentas = ($datos['total_ventas'] ?? 0) > 0;
                                            $tieneCompras = ($datos['total_compras'] ?? 0) > 0;
                                            $tipoCard = '';
                                            $colorTipo = '';
                                            if ($tieneVentas && $tieneCompras) {
                                                $tipoCard = 'Mixto (Ingresos + Egresos)';
                                                $colorTipo = '#9b59b6';
                                            } elseif ($tieneVentas) {
                                                $tipoCard = 'Ingresos';
                                                $colorTipo = '#2ecc71';
                                            } elseif ($tieneCompras) {
                                                $tipoCard = 'Egresos';
                                                $colorTipo = '#e74c3c';
                                            }
                                            $i++;
                                        ?>
                                        <div class="col-md-3" style="margin-bottom: 15px;">
                                            <div style="background: #ffffff; padding: 20px; border-radius: 10px; text-align: center; border: 2px solid <?=$colorBorder?>; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                                <div style="color: <?=$colorBorder?>; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin-bottom: 5px;">
                                                    <?=$metodo?>
                                                </div>
                                                <div style="color: <?=$colorTipo?>; font-size: 10px; font-weight: 600; margin-bottom: 10px;">
                                                    <i class="fa fa-info-circle"></i> <?=$tipoCard?>
                                                </div>
                                                <div style="color: #2c3e50; font-size: 24px; font-weight: 700; margin-top: 5px;">$<?=number_format(floatval($totalMetodo), 0, ",", ".")?></div>
                                                <div style="color: #95a5a6; font-size: 11px; margin-top: 8px;">
                                                    <?=$cantidadMetodo?> abonos (<?=$porcentaje?>%)<br>
                                                    <small style="font-size: 10px; display: block; margin-top: 5px;">
                                                        <span style="color: #2ecc71;">Ingresos: $<?=number_format(floatval($datos['total_ventas'] ?? 0), 0, ",", ".")?></span><br>
                                                        <span style="color: #e74c3c;">Egresos: $<?=number_format(floatval($datos['total_compras'] ?? 0), 0, ",", ".")?></span><br>
                                                        <span style="color: <?=$diferenciaMetodo >= 0 ? '#2ecc71' : '#e74c3c'?>; font-weight: 600; margin-top: 3px; display: inline-block;">
                                                            <i class="fa fa-balance-scale"></i> Diferencia: $<?=number_format(floatval($diferenciaMetodo), 0, ",", ".")?>
                                                        </span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="row">
								<div class="col-md-8 col-lg-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="btn-group">
                                                        <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0265'])) { ?>
                                                            <a href="abonos-agregar.php" class="btn deepPink-bgcolor"> Agregar nuevo <i class="fa fa-plus"></i></a>
                                                        <?php } ?>
                                                        <button type="button" class="btn btn-info" onclick="abrirModalArqueoCaja()" title="Generar informe de arqueo de caja">
                                                            <i class="fa fa-file-text"></i> Arqueo de Caja
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
											
                                    <style>
                                        .expand-btn {
                                            cursor: pointer;
                                            transition: transform 0.2s ease;
                                            color: #667eea;
                                        }
                                        .expand-btn:hover {
                                            color: #764ba2;
                                        }
                                        .expand-btn.expanded {
                                            transform: rotate(90deg);
                                        }
                                        .abono-details-row {
                                            display: none;
                                        }
                                        .abono-details-row.show {
                                            display: table-row;
                                        }
                                        .abono-details-wrapper {
                                            background: #f8f9ff;
                                            border-radius: 12px;
                                            padding: 20px;
                                            border: 1px solid rgba(102, 126, 234, 0.2);
                                            box-shadow: 0 5px 18px rgba(102, 126, 234, 0.12);
                                        }
                                        .abono-details-section h6 {
                                            font-size: 14px;
                                            font-weight: 600;
                                            color: #667eea;
                                            margin-bottom: 12px;
                                            text-transform: uppercase;
                                            letter-spacing: 0.5px;
                                        }
                                        .abono-details-table table {
                                            width: 100%;
                                        }
                                        .abono-details-table th {
                                            background: #eef2ff;
                                            color: #454f63;
                                            font-weight: 600;
                                            font-size: 12px;
                                            text-transform: uppercase;
                                        }
                                        .abono-details-table td {
                                            font-size: 13px;
                                            vertical-align: middle;
                                        }
                                        .badge-estado {
                                            display: inline-block;
                                            padding: 4px 10px;
                                            border-radius: 999px;
                                            font-size: 11px;
                                            font-weight: 600;
                                            text-transform: uppercase;
                                        }
                                        .badge-estado.success {
                                            background: rgba(46, 204, 113, 0.12);
                                            color: #2ecc71;
                                        }
                                        .badge-estado.warning {
                                            background: rgba(241, 196, 15, 0.12);
                                            color: #f39c12;
                                        }
                                        .detalle-item-list {
                                            list-style: none;
                                            padding: 0;
                                            margin: 0;
                                        }
                                        .detalle-item-list li {
                                            margin-bottom: 6px;
                                            font-size: 13px;
                                        }
                                        .detalle-item-list span {
                                            font-weight: 600;
                                            color: #454f63;
                                        }
                                        .texto-secundario {
                                            color: #6c7293;
                                            font-size: 13px;
                                        }
                                        .abono-details-summary {
                                            background: linear-gradient(120deg, #667eea 0%, #764ba2 100%);
                                            color: #ffffff;
                                            border-radius: 12px;
                                            padding: 18px 20px;
                                            margin-bottom: 20px;
                                            display: flex;
                                            gap: 25px;
                                        }
                                        .abono-details-summary .item {
                                            flex: 1;
                                        }
                                        .abono-details-summary .item span {
                                            display: block;
                                            font-size: 12px;
                                            text-transform: uppercase;
                                            letter-spacing: 0.5px;
                                            opacity: 0.8;
                                        }
                                        .abono-details-summary .item strong {
                                            font-size: 18px;
                                        }
                                    </style>
									
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
														<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[383][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[424][$datosUsuarioActual['uss_idioma']];?></th>
                                                        <th>Factura</th>
														<th><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[414][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[345][$datosUsuarioActual['uss_idioma']];?></th>
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0267','DT0269'])){?>
                                                            <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $detallesAbonos = []; ?>
													<?php
                                                        $consulta= Movimientos::listarAbonos($conexion, $config);
                                                        $contReg = 1;
                                                        while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){

                                                            $datosAbonoCompleto = Movimientos::traerDatosAbonos($conexion, $config, (string)$resultado['id']);
                                                            $clienteNombre = '';
                                                            if (!empty($datosAbonoCompleto['cliente_nombre'])) {
                                                                $clienteNombre = strtoupper($datosAbonoCompleto['cliente_nombre']);
                                                            } else {
                                                                // Validar que invoiced no sea null antes de llamar a sesionUsuario
                                                                if (!empty($resultado["invoiced"])) {
                                                                    $datosCliente = UsuariosPadre::sesionUsuario((string)$resultado["invoiced"]);
                                                                $clienteNombre = UsuariosPadre::nombreCompletoDelUsuario($datosCliente);
                                                                } else {
                                                                    $clienteNombre = 'N/A';
                                                                }
                                                            }

                                                            $voucherAbono = $datosAbonoCompleto['voucher'] ?? $resultado['voucher'] ?? '';
                                                            $vaucher = 'N/A';
                                                            if (!empty($voucherAbono) && file_exists(ROOT_PATH.'/main-app/files/comprobantes/' . $voucherAbono)) {
                                                                $vaucher = '<a href="'.REDIRECT_ROUTE.'/files/comprobantes/'.$voucherAbono.'" target="_blank" class="link">'.$voucherAbono.'</a>';
                                                            }

                                                            $arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
                                                            $arrayDatos = json_encode($arrayEnviar);
                                                            $objetoEnviar = htmlentities($arrayDatos);

                                                            $tipoTransaccion = $datosAbonoCompleto['type_payments'] ?? '';
                                                            $observacionesTexto = trim(strip_tags($datosAbonoCompleto['observation'] ?? ''));
                                                            $notasTexto = trim(strip_tags($datosAbonoCompleto['note'] ?? ''));
                                                            $fechaRegistro = !empty($datosAbonoCompleto['registration_date']) ? $datosAbonoCompleto['registration_date'] : $resultado['registration_date'];

                                                            $facturasAsociadas = [];
                                                            $conceptosAsociados = [];
                                                            $totalFacturasNeto = 0;
                                                            $totalFacturasAbonos = 0;
                                                            $totalFacturasPorCobrar = 0;
                                                            $totalValorAplicado = 0;

                                                            if (!empty($resultado['id']) && $tipoTransaccion === 'INVOICE') {
                                                                try {
                                                                    $consultaFacturasAbono = mysqli_query($conexion, "SELECT pi.*, fc.fcu_id, fc.fcu_fecha, fc.fcu_detalle, fc.fcu_observaciones, fc.fcu_status, fc.fcu_consecutivo, fc.fcu_valor, fc.fcu_tipo
                                                                        FROM ".BD_FINANCIERA.".payments_invoiced pi
                                                                        INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_id=pi.invoiced
                                                                        WHERE pi.id='{$resultado['id']}'
                                                                        AND pi.institucion={$config['conf_id_institucion']}
                                                                        AND pi.year={$_SESSION["bd"]}
                                                                        AND fc.institucion={$config['conf_id_institucion']}
                                                                        AND fc.year={$_SESSION["bd"]}");
                                                                } catch (Exception $e) {
                                                                    $consultaFacturasAbono = false;
                                                                    include("../compartido/error-catch-to-report.php");
                                                                }

                       											if ($consultaFacturasAbono && mysqli_num_rows($consultaFacturasAbono) > 0) {
                                                                   while ($facturaAbono = mysqli_fetch_array($consultaFacturasAbono, MYSQLI_BOTH)) {
                                                                       $vlrAdicional = !empty($facturaAbono['fcu_valor']) ? floatval($facturaAbono['fcu_valor']) : 0;
                                                                       $totalNetoFactura = Movimientos::calcularTotalNeto($conexion, $config, $facturaAbono['fcu_id'], $vlrAdicional);
                                                                       $abonosFactura = Movimientos::calcularTotalAbonado($conexion, $config, $facturaAbono['fcu_id']);
                                                                       $porCobrarFactura = $totalNetoFactura - $abonosFactura;

                                                                       $totalFacturasNeto += $totalNetoFactura;
                                                                       $totalFacturasAbonos += $abonosFactura;
                                                                       $totalFacturasPorCobrar += $porCobrarFactura;
                                                                       $valorAplicado = floatval($facturaAbono['payment'] ?? 0);
                                                                       $totalValorAplicado += $valorAplicado;

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
                                                                           // continuar sin items
                                                                       }

                                                                       $facturasAsociadas[] = [
                                                                           'datos' => $facturaAbono,
                                                                           'total_neto' => $totalNetoFactura,
                                                                           'abonos' => $abonosFactura,
                                                                           'por_cobrar' => $porCobrarFactura,
                                                                           'valor_aplicado' => $valorAplicado,
                                                                           'items' => $itemsFactura,
                                                                           'fcu_tipo' => intval($facturaAbono['fcu_tipo'] ?? 1) // 1 = venta, 2 = compra
                                                                       ];
                                                                   }
                                                               }
                                                            }

                                                            if (!empty($resultado['cod_payment']) && $tipoTransaccion === 'ACCOUNT') {
                                                                $consultaConceptos = Movimientos::listarConceptos($conexion, $config, $resultado['cod_payment']);
                                                                if ($consultaConceptos && mysqli_num_rows($consultaConceptos) > 0) {
                                                                    while ($concepto = mysqli_fetch_array($consultaConceptos, MYSQLI_BOTH)) {
                                                                        $conceptosAsociados[] = $concepto;
                                                                    }
                                                                }
                                                            }

                                                            // Obtener el valor del abono específico (después de procesar facturas)
                                                            $valorAbono = floatval($resultado['payment'] ?? 0);
                                                            // Si no está en resultado, intentar desde datosAbonoCompleto
                                                            if ($valorAbono == 0 && !empty($datosAbonoCompleto['payment'])) {
                                                                $valorAbono = floatval($datosAbonoCompleto['payment']);
                                                            }
                                                            // Si aún no hay valor y hay facturas asociadas, usar el total aplicado
                                                            if ($valorAbono == 0 && !empty($totalValorAplicado)) {
                                                                $valorAbono = $totalValorAplicado;
                                                            }
                                                            // Si aún no hay valor, intentar desde valorAbono de datosAbonoCompleto
                                                            if ($valorAbono == 0 && !empty($datosAbonoCompleto['valorAbono'])) {
                                                                $valorAbono = floatval($datosAbonoCompleto['valorAbono']);
                                                            }

                                                            $accionesPermitidas = Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0267','DT0269']);
                                                            $colspanDetalle = $accionesPermitidas ? 10 : 9;
                                                    ?>
													<?php 
													$abonoAnulado = (!empty($resultado['is_deleted']) && $resultado['is_deleted'] == 1);
													$claseFilaAnulada = $abonoAnulado ? 'abono-anulado' : '';
													$estiloFilaAnulada = $abonoAnulado ? 'style="opacity: 0.6; background-color: #fff3cd;"' : '';
													?>
													<?php
													// Determinar el tipo de factura para el footer (solo para facturas de tipo INVOICE)
													$tipoFacturaAbono = null;
													if ($tipoTransaccion === 'INVOICE' && !empty($facturasAsociadas)) {
														// Si hay facturas asociadas, usar el tipo de la primera (en teoría todas deberían ser del mismo tipo)
														$tipoFacturaAbono = $facturasAsociadas[0]['fcu_tipo'] ?? 1;
													}
													?>
													<tr id="reg<?=$resultado['id'];?>" class="abono-row <?=$claseFilaAnulada?>" data-detail="detalle<?=$resultado['id'];?>" data-tipo-factura="<?=$tipoFacturaAbono?>" data-tipo-transaccion="<?=$tipoTransaccion?>" data-valor-abono="<?=$valorAbono?>" <?=$estiloFilaAnulada?>>
                                                        <td>
                                                            <i class="fa fa-chevron-right expand-btn" id="expand<?=$resultado['id'];?>"></i>
                                                        </td>
                                                        <td>
                                                            <?=$contReg;?>
                                                            <?php if ($abonoAnulado) { ?>
                                                                <span class="badge badge-warning" style="margin-left: 5px;" title="Abono anulado">ANULADO</span>
                                                            <?php } ?>
                                                        </td>
														<td><?=$resultado['registration_date'];?></td>
														<td><?=UsuariosPadre::nombreCompletoDelUsuario($resultado);?></td>
														<td><?=$clienteNombre;?></td>
                                                        <td>
                                                            <?php if (!empty($facturasAsociadas) && count($facturasAsociadas) == 1) {
                                                                $facturaId = $facturasAsociadas[0]['datos']['fcu_id'];
                                                                $facturaCodigo = $facturasAsociadas[0]['datos']['fcu_id'] ?? 'N/A';
                                                            ?>
                                                                <a href="movimientos-editar.php?id=<?=base64_encode($facturaId)?>" style="text-decoration: underline; color: #667eea; font-weight: 600;" title="Ver detalle de factura">
                                                                    <?=$facturaCodigo?>
                                                                </a>
                                                            <?php } elseif (!empty($facturasAsociadas) && count($facturasAsociadas) > 1) { ?>
                                                                <span style="color: #667eea; font-weight: 600;" title="Ver detalles expandidos"><?=count($facturasAsociadas)?> facturas</span>
                                                            <?php } else { ?>
                                                                <?=$resultado['fcu_id_factura'] ?? $resultado['numeroFactura'] ?? 'N/A';?>
                                                            <?php } ?>
                                                        </td>
														<td>$<?=number_format(floatval($valorAbono ?? 0),0,",",".")?></td>
														<td><?=$resultado['payment_method'];?></td>
														<td><?=$vaucher;?></td>
														
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0267','DT0269'])){?>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary" <?=$abonoAnulado ? 'disabled' : '';?>><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown" <?=$abonoAnulado ? 'disabled' : '';?>>
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0267']) && !$abonoAnulado){?>
                                                                            <li><a href="abonos-editar.php?id=<?=base64_encode($resultado['id']);?>"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?></a></li>
                                                                        <?php } if(Modulos::validarSubRol(['DT0269']) && !$abonoAnulado){?>
                                                                            <li><a href="abonos-anular.php?id=<?=base64_encode($resultado['id']);?>">Anular</a></li>
																		<?php } if( Modulos::validarSubRol(['DT0271']) ){?>
																			<li><a href="abonos-recibo-caja.php?id=<?=base64_encode($resultado['id']);?>" target="_blank"><?=$frases[57][$datosUsuarioActual['uss_idioma']];?></a></li>
																		<?php }?>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        <?php }?>
                                                    </tr>
                                                    <?php
                                                        ob_start();
                                                    ?>
                                                    <div class="abono-details-wrapper">
                                                        <div class="abono-details-summary">
                                                            <div class="item">
                                                                <span>Total del Abono</span>
                                                                <strong>$<?=number_format(floatval($valorAbono ?? 0), 0, ",", ".");?></strong>
                                                            </div>
                                                            <?php if ($tipoTransaccion === INVOICE && !empty($facturasAsociadas)) { ?>
                                                            <div class="item">
                                                                <span>Total Neto Facturas</span>
                                                                <strong>$<?=number_format(floatval($totalFacturasNeto ?? 0), 0, ",", ".");?></strong>
                                                            </div>
                                                            <div class="item">
                                                                <span>Saldo Pendiente</span>
                                                                <strong>$<?=number_format(floatval($totalFacturasPorCobrar ?? 0), 0, ",", ".");?></strong>
                                                            </div>
                                                            <?php } else { ?>
                                                            <div class="item">
                                                                <span>Método de Pago</span>
                                                                <strong><?=$resultado['payment_method'];?></strong>
                                                            </div>
                                                            <div class="item">
                                                                <span>Tipo Transacción</span>
                                                                <strong><?=($tipoTransaccion === INVOICE ? 'Factura de venta' : 'Cuenta contable');?></strong>
                                                            </div>
                                                            <?php } ?>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 abono-details-section">
                                                                <h6>Información del Abono</h6>
                                                                <ul class="detalle-item-list">
                                                                    <li><span>Código único:</span> <?=$resultado['id'] ?? $resultado['cod_payment'] ?? 'N/A';?></li>
                                                                    <li><span>Fecha de registro:</span> <?=$fechaRegistro ?? 'N/A';?></li>
                                                                    <li><span>Responsable:</span> <?=UsuariosPadre::nombreCompletoDelUsuario($resultado);?></li>
                                                                    <li><span>Tipo de transacción:</span> <?=($tipoTransaccion === INVOICE ? 'Factura de venta' : 'Cuenta contable');?></li>
                                                                    <li><span>Método de pago:</span> <?=$resultado['payment_method'] ?? 'N/A';?></li>
                                                                    <?php if (!empty($datosAbonoCompleto['payment_cuenta_bancaria_id'])) { ?>
                                                                    <li><span>Cuenta bancaria:</span> 
                                                                        <strong><?=htmlspecialchars($datosAbonoCompleto['cuenta_bancaria_nombre'] ?? 'N/A');?></strong>
                                                                        <?php if (!empty($datosAbonoCompleto['cuenta_bancaria_numero'])) { ?>
                                                                            <br><small class="texto-secundario">Número: <?=htmlspecialchars($datosAbonoCompleto['cuenta_bancaria_numero']);?></small>
                                                                        <?php } ?>
                                                                        <?php if (!empty($datosAbonoCompleto['cuenta_bancaria_banco'])) { ?>
                                                                            <br><small class="texto-secundario">Banco: <?=htmlspecialchars($datosAbonoCompleto['cuenta_bancaria_banco']);?></small>
                                                                        <?php } ?>
                                                                        <?php if (!empty($datosAbonoCompleto['cuenta_bancaria_tipo'])) { ?>
                                                                            <br><small class="texto-secundario">Tipo: <?=htmlspecialchars($datosAbonoCompleto['cuenta_bancaria_tipo']);?></small>
                                                                        <?php } ?>
                                                                    </li>
                                                                    <?php } else { ?>
                                                                    <li><span>Cuenta bancaria:</span> <span class="texto-secundario">No especificada</span></li>
                                                                    <?php } ?>
                                                                    <li><span>Comprobante:</span> <?=$vaucher !== 'N/A' ? $vaucher : '<span class="texto-secundario">No adjunto</span>';?></li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-6 abono-details-section">
                                                                <h6>Observaciones</h6>
                                                                <p class="texto-secundario"><?=!empty($observacionesTexto) ? nl2br(htmlspecialchars($observacionesTexto)) : 'No registradas';?></p>
                                                                <h6>Notas adicionales</h6>
                                                                <p class="texto-secundario"><?=!empty($notasTexto) ? nl2br(htmlspecialchars($notasTexto)) : 'No registradas';?></p>
                                                            </div>
                                                        </div>

                                                        <?php if ($tipoTransaccion === INVOICE && !empty($facturasAsociadas)) { ?>
                                                        <div class="abono-details-section abono-details-table" style="margin-top: 20px;">
                                                            <h6>Facturas Asociadas</h6>
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Cod. Factura</th>
                                                                        <th>Fecha</th>
                                                                        <th>Concepto</th>
                                                                        <th>Total Neto</th>
                                                                        <th>Abonado</th>
                                                                        <th>Por Cobrar</th>
                                                                        <th>Valor del abono</th>
                                                                        <th>Estado</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($facturasAsociadas as $factura) { 
                                                                        $datosFactura = $factura['datos'];
                                                                        $estado = $datosFactura['fcu_status'] ?? '';
                                                                        $badgeClass = ($estado == 'COBRADA') ? 'success' : 'warning';
                                                                        $estadoTexto = ($estado == 'COBRADA') ? 'Cobrada' : 'Por Cobrar';
                                                                    ?>
                                                                    <tr>
                                                                        <td><?=$datosFactura['fcu_id'] ?? 'N/A';?></td>
                                                                        <td><?=$datosFactura['fcu_fecha'] ?? 'N/A';?></td>
                                                                        <td><?=htmlspecialchars($datosFactura['fcu_detalle'] ?? 'Sin concepto');?></td>
                                                                        <td>$<?=number_format(floatval($factura['total_neto'] ?? 0), 0, ",", ".");?></td>
                                                                        <td style="color: green;">$<?=number_format(floatval($factura['abonos'] ?? 0), 0, ",", ".");?></td>
                                                                        <td style="color: #e74c3c;">$<?=number_format(floatval($factura['por_cobrar'] ?? 0), 0, ",", ".");?></td>
                                                                        <td>$<?=number_format(floatval($factura['valor_aplicado'] ?? 0), 0, ",", ".");?></td>
                                                                        <td><span class="badge-estado <?=$badgeClass;?>"><?=$estadoTexto;?></span></td>
                                                                    </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <?php } ?>

                                                        <?php if ($tipoTransaccion === ACCOUNT && !empty($conceptosAsociados)) { ?>
                                                        <div class="abono-details-section abono-details-table" style="margin-top: 20px;">
                                                            <h6>Conceptos Asociados</h6>
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Concepto</th>
                                                                        <th>Valor</th>
                                                                        <th>Cantidad</th>
                                                                        <th>Subtotal</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($conceptosAsociados as $concepto) { 
                                                                        $valorConcepto = floatval($concepto['price'] ?? $concepto['payment'] ?? 0);
                                                                        $cantidad = floatval($concepto['cantity'] ?? 1);
                                                                        $subtotal = floatval($concepto['subtotal'] ?? ($valorConcepto * $cantidad));
                                                                    ?>
                                                                    <tr>
                                                                        <td><?=htmlspecialchars($concepto['description'] ?? 'N/A');?></td>
                                                                        <td>$<?=number_format(floatval($valorConcepto ?? 0), 0, ",", ".");?></td>
                                                                        <td><?=number_format(floatval($cantidad ?? 0), 0, ",", ".");?></td>
                                                                        <td>$<?=number_format(floatval($subtotal ?? 0), 0, ",", ".");?></td>
                                                                    </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <?php
                                                        $detallesAbonos[$resultado['id']] = ob_get_clean();
                                                    ?>
													<?php 
														 $contReg++;
													  }
													  ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr style="background-color: #f0f9ff; border-top: 2px solid #2ecc71;">
                                                        <td colspan="6" align="right" style="font-weight: bold; padding: 8px; color: #2ecc71;">
                                                            <i class="fa fa-arrow-circle-down"></i> TOTAL ABONOS A FACTURAS DE VENTA (INGRESOS):
                                                        </td>
                                                        <td align="right" style="font-weight: bold; color: #2ecc71;" id="footerTotalVenta">$0</td>
                                                        <td colspan="3"></td>
                                                    </tr>
                                                    <tr style="background-color: #fff7ed; border-top: 1px solid #e74c3c;">
                                                        <td colspan="6" align="right" style="font-weight: bold; padding: 8px; color: #e74c3c;">
                                                            <i class="fa fa-arrow-circle-up"></i> TOTAL ABONOS A FACTURAS DE COMPRA (EGRESOS):
                                                        </td>
                                                        <td align="right" style="font-weight: bold; color: #e74c3c;" id="footerTotalCompra">$0</td>
                                                        <td colspan="3"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            </div>
                                            <div class="detalles-abonos-cache" style="display:none;">
                                                <?php foreach ($detallesAbonos as $idDetalle => $htmlDetalle) { ?>
                                                    <div id="detalle<?=$idDetalle;?>"><?=$htmlDetalle;?></div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								<div class="col-md-4 col-lg-3">
									<?php require_once(ROOT_PATH."/main-app/compartido/publicidad-lateral.php");?>
								</div>
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
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
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
    <script>
        /**
         * Función para formatear números con separadores de miles
         */
        function numberFormat(number, decimals = 0, decPoint = ',', thousandsSep = '.') {
            if (isNaN(number) || number === '' || number === null) {
                return '0';
            }
            number = parseFloat(number);
            var n = number.toFixed(decimals);
            var x = n.split('.');
            var x1 = x[0];
            var x2 = x.length > 1 ? decPoint + x[1] : '';
            var regex = /(\d+)(\d{3})/;
            while (regex.test(x1)) {
                x1 = x1.replace(regex, '$1' + thousandsSep + '$2');
            }
            return x1 + x2;
        }

        /**
         * Calcula y actualiza los totales del footer de abonos
         * Distingue entre abonos a facturas de venta (ingresos) y compra (egresos)
         */
        function totalizarAbonosFooter() {
            var tabla = document.getElementById('example1');
            if (!tabla) {
                return;
            }

            var totalVentas = 0;
            var totalCompras = 0;

            // Iterar sobre todas las filas visibles en DataTable
            var table = $('#example1').DataTable();
            table.rows({ search: 'applied' }).every(function() {
                var row = this.node();
                if (!row) return;

                // Saltar filas de detalles expandidas
                if ($(row).hasClass('child')) {
                    return;
                }

                // Obtener tipo de transacción y tipo de factura usando attr para valores null
                var tipoTransaccion = $(row).attr('data-tipo-transaccion');
                var tipoFacturaStr = $(row).attr('data-tipo-factura');
                var valorAbonoStr = $(row).attr('data-valor-abono');
                
                var tipoFactura = tipoFacturaStr !== null && tipoFacturaStr !== '' && tipoFacturaStr !== 'null' ? parseInt(tipoFacturaStr) : null;
                var valorAbono = valorAbonoStr ? parseFloat(valorAbonoStr) || 0 : 0;

                // Solo contar abonos a facturas (INVOICE), no cuentas contables (ACCOUNT)
                if (tipoTransaccion === 'INVOICE' && tipoFactura !== null && !isNaN(tipoFactura)) {
                    if (tipoFactura == 1) {
                        // Factura de venta (ingreso)
                        totalVentas += valorAbono;
                    } else if (tipoFactura == 2) {
                        // Factura de compra (egreso)
                        totalCompras += valorAbono;
                    }
                }
            });

            // Actualizar el footer
            document.getElementById('footerTotalVenta').textContent = '$' + numberFormat(totalVentas, 0, ',', '.');
            document.getElementById('footerTotalCompra').textContent = '$' + numberFormat(totalCompras, 0, ',', '.');
        }

        $(document).ready(function(){
            if ($.fn.DataTable.isDataTable('#example1')) {
                $('#example1').DataTable().destroy();
            }

            var table = $('#example1').DataTable({
                "columnDefs": [
                    { "orderable": false, "searchable": false, "targets": 0 }
                    <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0267','DT0269'])){?>,
                    { "orderable": false, "searchable": false, "targets": -1 }
                    <?php }?>
                ],
                "order": [[1, 'desc']],
                "drawCallback": function(settings) {
                    // Recalcular totales cada vez que se redibuja la tabla
                    totalizarAbonosFooter();
                }
            });

            // Calcular totales iniciales
            totalizarAbonosFooter();

            $('#example1 tbody').on('click', '.expand-btn', function () {
                var $icon = $(this);
                var tr = $icon.closest('tr');
                var row = table.row(tr);
                var detailId = tr.data('detail');
                var content = $('#' + detailId).html();

                if (row.child.isShown()) {
                    row.child.hide();
                    $icon.removeClass('expanded');
                } else {
                    row.child(content).show();
                    $icon.addClass('expanded');
                }
            });

            // Recalcular cuando se filtre o busque
            $('#example1').on('search.dt', function() {
                setTimeout(totalizarAbonosFooter, 100);
            });
        });
    </script>
    
    <!-- Modal para Arqueo de Caja -->
    <div class="modal fade" id="modalArqueoCaja" tabindex="-1" role="dialog" aria-labelledby="modalArqueoCajaLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalArqueoCajaLabel">Generar Arqueo de Caja</h4>
                </div>
                <div class="modal-body">
                    <form id="formArqueoCaja" method="GET" action="../compartido/reporte-arqueo-caja.php" target="_blank">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fechaDesde">Fecha Desde:</label>
                                    <input type="date" class="form-control" id="fechaDesde" name="desde" value="<?= date('Y-m-01') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fechaHasta">Fecha Hasta:</label>
                                    <input type="date" class="form-control" id="fechaHasta" name="hasta" value="<?= date('Y-m-t') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipoMovimiento">Tipo de Movimiento:</label>
                            <select class="form-control" id="tipoMovimiento" name="tipo">
                                <option value="">Todos</option>
                                <option value="<?= base64_encode('1') ?>">Solo Ingresos</option>
                                <option value="<?= base64_encode('2') ?>">Solo Egresos</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="metodosPago">Métodos de Pago (Seleccione uno o más):</label>
                            <select class="form-control" id="metodosPago" name="metodos_pago[]" multiple size="5" style="min-height: 120px;">
                                <?php
                                require_once(ROOT_PATH."/main-app/class/MediosPago.php");
                                $mediosPago = MediosPago::obtenerMediosPago();
                                foreach ($mediosPago as $codigo => $nombre) {
                                    echo '<option value="'.htmlspecialchars($codigo, ENT_QUOTES).'">'.htmlspecialchars($nombre, ENT_QUOTES).'</option>';
                                }
                                ?>
                            </select>
                            <small class="form-text text-muted">Mantenga presionada la tecla Ctrl (Windows) o Cmd (Mac) para seleccionar múltiples opciones. Deje sin seleccionar para incluir todos.</small>
                        </div>
                        <div class="form-group">
                            <label for="cuentasBancarias">Cuentas Bancarias (Seleccione una o más):</label>
                            <select class="form-control" id="cuentasBancarias" name="cuentas_bancarias[]" multiple size="5" style="min-height: 120px;">
                                <?php
                                $consultaCuentas = Movimientos::listarCuentasBancarias($conexion, $config, null, true);
                                if ($consultaCuentas) {
                                    while ($cuenta = mysqli_fetch_array($consultaCuentas, MYSQLI_BOTH)) {
                                        $nombreCuenta = $cuenta['cba_nombre'] . (!empty($cuenta['cba_banco']) ? ' - ' . $cuenta['cba_banco'] : '');
                                        echo '<option value="'.htmlspecialchars($cuenta['cba_id'], ENT_QUOTES).'">'.htmlspecialchars($nombreCuenta, ENT_QUOTES).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <small class="form-text text-muted">Mantenga presionada la tecla Ctrl (Windows) o Cmd (Mac) para seleccionar múltiples opciones. Deje sin seleccionar para incluir todas.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="generarArqueoCaja()">Generar Informe</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function abrirModalArqueoCaja() {
            $('#modalArqueoCaja').modal('show');
        }
        
        function generarArqueoCaja() {
            $('#formArqueoCaja').submit();
            $('#modalArqueoCaja').modal('hide');
        }
    </script>

</body>

</html>