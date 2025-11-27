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
                            <div class="row">
								
								<div class="col-md-8 col-lg-12">
                                <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
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
													</div>
												</div>
											</div>

                                            <?php
                                            // Calcular resúmenes por método de pago
                                            $resumenMetodos = [];
                                            $totalGeneral = 0;
                                            $totalAbonos = 0;
                                            
                                            try {
                                                $consultaResumen = mysqli_query($conexion, "SELECT 
                                                    pay.payment_method,
                                                    COUNT(DISTINCT pay.id) as cantidad_abonos,
                                                    SUM(COALESCE(pi.payment, 0)) as total_abonado
                                                FROM ".BD_FINANCIERA.".payments pay
                                                LEFT JOIN ".BD_FINANCIERA.".payments_invoiced pi 
                                                    ON pi.payments = pay.cod_payment 
                                                    AND pi.institucion = pay.institucion 
                                                    AND pi.year = pay.year
                                                WHERE pay.is_deleted = 0 
                                                    AND pay.institucion = {$config['conf_id_institucion']} 
                                                    AND pay.year = {$_SESSION["bd"]}
                                                GROUP BY pay.payment_method
                                                ORDER BY total_abonado DESC");
                                                
                                                if ($consultaResumen) {
                                                    while ($resumen = mysqli_fetch_array($consultaResumen, MYSQLI_BOTH)) {
                                                        $metodo = $resumen['payment_method'] ?? 'Sin método';
                                                        $cantidad = intval($resumen['cantidad_abonos']);
                                                        $total = floatval($resumen['total_abonado']);
                                                        
                                                        $resumenMetodos[$metodo] = [
                                                            'cantidad' => $cantidad,
                                                            'total' => $total
                                                        ];
                                                        
                                                        $totalGeneral += $total;
                                                        $totalAbonos += $cantidad;
                                                    }
                                                }
                                            } catch (Exception $e) {
                                                include("../compartido/error-catch-to-report.php");
                                            }
                                            ?>

                                            <!-- Resumen por métodos de pago -->
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-sm-12">
                                                    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; padding: 20px;">
                                                        <h5 style="color: white; margin-bottom: 15px; font-weight: 600;">
                                                            <i class="fa fa-bar-chart"></i> Resumen de Abonos por Método de Pago
                                                        </h5>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div style="background: rgba(255,255,255,0.15); padding: 15px; border-radius: 8px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                                                                    <div style="color: rgba(255,255,255,0.8); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Total General</div>
                                                                    <div style="color: white; font-size: 24px; font-weight: 700; margin-top: 5px;">$<?=number_format($totalGeneral, 0, ",", ".")?></div>
                                                                    <div style="color: rgba(255,255,255,0.7); font-size: 11px; margin-top: 5px;"><?=$totalAbonos?> abonos</div>
                                                                </div>
                                                            </div>
                                                            <?php 
                                                            $coloresBg = ['rgba(46, 204, 113, 0.2)', 'rgba(52, 152, 219, 0.2)', 'rgba(241, 196, 15, 0.2)', 'rgba(155, 89, 182, 0.2)', 'rgba(230, 126, 34, 0.2)'];
                                                            $coloresBorder = ['rgba(46, 204, 113, 0.4)', 'rgba(52, 152, 219, 0.4)', 'rgba(241, 196, 15, 0.4)', 'rgba(155, 89, 182, 0.4)', 'rgba(230, 126, 34, 0.4)'];
                                                            $i = 0;
                                                            foreach ($resumenMetodos as $metodo => $datos) { 
                                                                $colorBg = $coloresBg[$i % count($coloresBg)];
                                                                $colorBorder = $coloresBorder[$i % count($coloresBorder)];
                                                                $porcentaje = $totalGeneral > 0 ? round(($datos['total'] / $totalGeneral) * 100, 1) : 0;
                                                                $i++;
                                                            ?>
                                                            <div class="col-md-3">
                                                                <div style="background: <?=$colorBg?>; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid <?=$colorBorder?>;">
                                                                    <div style="color: rgba(255,255,255,0.9); font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;"><?=$metodo?></div>
                                                                    <div style="color: white; font-size: 20px; font-weight: 700; margin-top: 5px;">$<?=number_format($datos['total'], 0, ",", ".")?></div>
                                                                    <div style="color: rgba(255,255,255,0.7); font-size: 11px; margin-top: 5px;"><?=$datos['cantidad']?> abonos (<?=$porcentaje?>%)</div>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                        </div>
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
                                                                $datosCliente = UsuariosPadre::sesionUsuario($resultado["invoiced"]);
                                                                $clienteNombre = UsuariosPadre::nombreCompletoDelUsuario($datosCliente);
                                                            }

                                                            $voucherAbono = $datosAbonoCompleto['voucher'] ?? $resultado['voucher'] ?? '';
                                                            $vaucher = 'N/A';
                                                            if (!empty($voucherAbono) && file_exists(ROOT_PATH.'/main-app/files/comprobantes/' . $voucherAbono)) {
                                                                $vaucher = '<a href="'.REDIRECT_ROUTE.'/files/comprobantes/'.$voucherAbono.'" target="_blank" class="link">'.$voucherAbono.'</a>';
                                                            }

                                                            $abonos = Movimientos::calcularTotalAbonadoCliente($conexion, $config, $resultado['invoiced'], $resultado['cod_payment']);

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

                                                            if (!empty($resultado['cod_payment']) && $tipoTransaccion === INVOICE) {
                                                                try {
                                                                    $consultaFacturasAbono = mysqli_query($conexion, "SELECT pi.*, fc.fcu_id, fc.fcu_fecha, fc.fcu_detalle, fc.fcu_observaciones, fc.fcu_status, fc.id_nuevo, fc.fcu_valor
                                                                        FROM ".BD_FINANCIERA.".payments_invoiced pi
                                                                        INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_id=pi.invoiced
                                                                        WHERE pi.payments='{$resultado['cod_payment']}'
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
                                                                           'items' => $itemsFactura
                                                                       ];
                                                                   }
                                                               }
                                                            }

                                                            if (!empty($resultado['cod_payment']) && $tipoTransaccion === ACCOUNT) {
                                                                $consultaConceptos = Movimientos::listarConceptos($conexion, $config, $resultado['cod_payment']);
                                                                if ($consultaConceptos && mysqli_num_rows($consultaConceptos) > 0) {
                                                                    while ($concepto = mysqli_fetch_array($consultaConceptos, MYSQLI_BOTH)) {
                                                                        $conceptosAsociados[] = $concepto;
                                                                    }
                                                                }
                                                            }

                                                            $accionesPermitidas = Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0267','DT0269']);
                                                            $colspanDetalle = $accionesPermitidas ? 10 : 9;
                                                    ?>
													<tr id="reg<?=$resultado['id'];?>" class="abono-row" data-detail="detalle<?=$resultado['id'];?>">
                                                        <td>
                                                            <i class="fa fa-chevron-right expand-btn" id="expand<?=$resultado['id'];?>"></i>
                                                        </td>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['registration_date'];?></td>
														<td><?=UsuariosPadre::nombreCompletoDelUsuario($resultado);?></td>
														<td><?=$clienteNombre;?></td>
                                                        <td>
                                                            <?php if (!empty($facturasAsociadas) && count($facturasAsociadas) == 1) {
                                                                $facturaId = $facturasAsociadas[0]['datos']['fcu_id'];
                                                                $facturaConsec = $facturasAsociadas[0]['datos']['id_nuevo'];
                                                            ?>
                                                                <a href="movimientos-editar.php?id=<?=base64_encode($facturaId)?>" style="text-decoration: underline; color: #667eea; font-weight: 600;" title="Ver detalle de factura">
                                                                    <?=$facturaConsec?>
                                                                </a>
                                                            <?php } else { ?>
                                                                <?=$resultado['numeroFactura'] ?? 'N/A';?>
                                                            <?php } ?>
                                                        </td>
														<td>$<?=number_format($abonos,0,",",".")?></td>
														<td><?=$resultado['payment_method'];?></td>
														<td><?=$vaucher;?></td>
														
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0267','DT0269'])){?>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0267'])){?>
                                                                            <li><a href="abonos-editar.php?id=<?=base64_encode($resultado['id']);?>"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?></a></li>
                                                                        <?php } if(Modulos::validarSubRol(['DT0269'])){?>
                                                                            <li><a href="javascript:void(0);" title="<?=$objetoEnviar;?>" id="<?=$resultado['id'];?>" name="abonos-eliminar.php?id=<?=base64_encode($resultado['id']);?>" onClick="deseaEliminar(this)"><?=$frases[174][$datosUsuarioActual['uss_idioma']];?></a></li>
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
                                                                <strong>$<?=number_format($abonos, 0, ",", ".");?></strong>
                                                            </div>
                                                            <?php if ($tipoTransaccion === INVOICE && !empty($facturasAsociadas)) { ?>
                                                            <div class="item">
                                                                <span>Total Neto Facturas</span>
                                                                <strong>$<?=number_format($totalFacturasNeto, 0, ",", ".");?></strong>
                                                            </div>
                                                            <div class="item">
                                                                <span>Saldo Pendiente</span>
                                                                <strong>$<?=number_format($totalFacturasPorCobrar, 0, ",", ".");?></strong>
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
                                                                    <li><span>Código único:</span> <?=$resultado['cod_payment'] ?? 'N/A';?></li>
                                                                    <li><span>Fecha de registro:</span> <?=$fechaRegistro ?? 'N/A';?></li>
                                                                    <li><span>Responsable:</span> <?=UsuariosPadre::nombreCompletoDelUsuario($resultado);?></li>
                                                                    <li><span>Tipo de transacción:</span> <?=($tipoTransaccion === INVOICE ? 'Factura de venta' : 'Cuenta contable');?></li>
                                                                    <li><span>Método de pago:</span> <?=$resultado['payment_method'] ?? 'N/A';?></li>
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
                                                                        <td><?=$datosFactura['id_nuevo'] ?? 'N/A';?></td>
                                                                        <td><?=$datosFactura['fcu_fecha'] ?? 'N/A';?></td>
                                                                        <td><?=htmlspecialchars($datosFactura['fcu_detalle'] ?? 'Sin concepto');?></td>
                                                                        <td>$<?=number_format($factura['total_neto'], 0, ",", ".");?></td>
                                                                        <td style="color: green;">$<?=number_format($factura['abonos'], 0, ",", ".");?></td>
                                                                        <td style="color: #e74c3c;">$<?=number_format($factura['por_cobrar'], 0, ",", ".");?></td>
                                                                        <td>$<?=number_format($factura['valor_aplicado'], 0, ",", ".");?></td>
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
                                                                        <td>$<?=number_format($valorConcepto, 0, ",", ".");?></td>
                                                                        <td><?=number_format($cantidad, 0, ",", ".");?></td>
                                                                        <td>$<?=number_format($subtotal, 0, ",", ".");?></td>
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
                "order": [[1, 'desc']]
            });

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
        });
    </script>

</body>

</html>