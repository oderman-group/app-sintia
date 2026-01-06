<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0277';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$loteId = '';
if (!empty($_GET['id'])) {
	$loteId = base64_decode($_GET['id']);
}

if (empty($loteId)) {
	echo '<script type="text/javascript">alert("ID de lote no válido"); window.location.href="lotes-facturacion.php";</script>';
	exit();
}

$datosLote = Movimientos::traerDatosLote($conexion, $config, $loteId);
if (empty($datosLote)) {
	echo '<script type="text/javascript">alert("Lote no encontrado"); window.location.href="lotes-facturacion.php";</script>';
	exit();
}

$criterios = json_decode($datosLote['lote_criterios'], true);
$itemsLote = json_decode($datosLote['lote_items'], true);
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
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
                                <div class="page-title">Detalle del Lote de Facturación</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="movimientos.php" onClick="deseaRegresar(this)">Movimientos</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li><a class="parent-item" href="lotes-facturacion.php">Lotes de Facturación</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Detalle del Lote</li>
                            </ol>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                        <?php include("../../config-general/mensajes-informativos.php"); ?>
									<?php include("../compartido/publicidad-lateral.php");?>
                                    
                                    <!-- Información del Lote -->
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Información del Lote</header>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="40%">Nombre del Lote:</th>
                                                            <td><?=htmlspecialchars($datosLote['lote_nombre']);?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Fecha de Creación:</th>
                                                            <td><?=date('d/m/Y H:i', strtotime($datosLote['lote_fecha']));?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tipo de Grupo:</th>
                                                            <td><?=htmlspecialchars($datosLote['lote_tipo_grupo']);?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Usuario Creador:</th>
                                                            <td><?=htmlspecialchars($datosLote['usuario_creador_nombre'] ?? 'N/A');?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Estado:</th>
                                                            <td>
                                                                <?php
                                                                $estadoClass = '';
                                                                $estadoTexto = $datosLote['lote_estado'];
                                                                switch($datosLote['lote_estado']) {
                                                                    case 'COMPLETADO':
                                                                        $estadoClass = 'label-success';
                                                                        $estadoTexto = 'Completado';
                                                                        break;
                                                                    case 'PROCESANDO':
                                                                        $estadoClass = 'label-warning';
                                                                        $estadoTexto = 'Procesando';
                                                                        break;
                                                                    case 'ERROR':
                                                                        $estadoClass = 'label-danger';
                                                                        $estadoTexto = 'Error';
                                                                        break;
                                                                    case 'PENDIENTE':
                                                                        $estadoClass = 'label-info';
                                                                        $estadoTexto = 'Pendiente';
                                                                        break;
                                                                }
                                                                ?>
                                                                <span class="label <?=$estadoClass;?>"><?=$estadoTexto;?></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Total Facturas:</th>
                                                            <td><strong><?=$datosLote['lote_total_facturas'];?></strong></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h4>Criterios de Selección:</h4>
                                                    <?php if (!empty($criterios)) { ?>
                                                        <ul>
                                                            <?php if (!empty($criterios['grados'])) { ?>
                                                                <li><strong>Grados:</strong> <?=implode(', ', $criterios['grados']);?></li>
                                                            <?php } ?>
                                                            <?php if (!empty($criterios['grupos'])) { ?>
                                                                <li><strong>Grupos:</strong> <?=implode(', ', $criterios['grupos']);?></li>
                                                            <?php } ?>
                                                            <?php if (!empty($criterios['estado'])) { ?>
                                                                <li><strong>Estado:</strong> <?=$criterios['estado'] == 1 ? 'Activo' : 'Inactivo';?></li>
                                                            <?php } ?>
                                                        </ul>
                                                    <?php } ?>
                                                    
                                                    <?php if (!empty($datosLote['lote_observaciones'])) { ?>
                                                        <h4>Observaciones:</h4>
                                                        <p><?=nl2br(htmlspecialchars($datosLote['lote_observaciones']));?></p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Facturas del Lote -->
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Facturas Generadas</header>
                                        </div>
                                        <div class="card-body">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th>Código</th>
														<th>Fecha</th>
														<th>Usuario</th>
														<th>Valor Factura</th>
														<th>Total Items</th>
														<th>Abonado</th>
														<th>Por Cobrar</th>
														<th>Estado</th>
														<th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$consultaFacturas = Movimientos::listarFacturasPorLote($conexion, $config, $loteId);
													$contReg = 1;
													$totalFacturado = 0;
													$totalAbonado = 0;
													while($factura = mysqli_fetch_array($consultaFacturas, MYSQLI_BOTH)){
														$fechaBD = new DateTime($factura['fcu_fecha']);
														$fecha = $fechaBD->format('d/m/Y');
														
														$valorAdicional = floatval($factura['fcu_valor'] ?? 0);
														$totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $factura['fcu_id'], $valorAdicional);
														// Usar el método centralizado para calcular el total abonado (consistencia)
														$abonado = Movimientos::calcularTotalAbonado($conexion, $config, $factura['fcu_id']);
														$porCobrar = $totalNeto - $abonado;
														
														$totalFacturado += $totalNeto;
														$totalAbonado += $abonado;
														
														// Determinar estado de la factura (ya filtradas anuladas y en proceso)
														$estadoFactura = $factura['fcu_status'] ?? POR_COBRAR;
														$estadoClass = 'label-warning'; // Por defecto
														$estadoTexto = 'Por Cobrar'; // Por defecto
														
														switch($estadoFactura) {
															case COBRADA:
																$estadoClass = 'label-success';
																$estadoTexto = 'Cobrada';
																break;
															case POR_COBRAR:
															default:
																$estadoClass = 'label-warning';
																$estadoTexto = 'Por Cobrar';
																break;
														}
														
														$facturaIdEncoded = base64_encode($factura['fcu_id']);
														
														// Variables para la tabla
														$valorFactura = $totalNeto; // El valor de la factura es el total neto calculado
														$totalItems = 0; // Esta columna se mantiene vacía como en el tfoot
													?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=htmlspecialchars($factura['fcu_consecutivo'] ?? 'N/A');?></td>
														<td><?=$fecha;?></td>
														<td><?=htmlspecialchars($factura['usuario_nombre'] ?? 'N/A');?></td>
														<td>$<?=number_format($valorFactura, 0, ',', '.');?></td>
														<td>$<?=number_format($totalItems, 0, ',', '.');?></td>
														<td>$<?=number_format($abonado, 0, ',', '.');?></td>
														<td>$<?=number_format($porCobrar, 0, ',', '.');?></td>
														<td><span class="label <?=$estadoClass;?>"><?=$estadoTexto;?></span></td>
														<td>
															<a href="movimientos.php?id=<?=$facturaIdEncoded;?>" class="btn btn-sm btn-info" title="Ver factura">
																<i class="fa fa-eye"></i> Ver
															</a>
														</td>
                                                    </tr>
													<?php
														$contReg++;
													}
													?>
                                                </tbody>
												<tfoot>
													<tr style="background-color: #f5f5f5; font-weight: bold;">
														<td colspan="4" align="right"><strong>TOTALES:</strong></td>
														<td><strong>$<?=number_format($totalFacturado, 0, ',', '.');?></strong></td>
														<td></td>
														<td><strong>$<?=number_format($totalAbonado, 0, ',', '.');?></strong></td>
														<td><strong>$<?=number_format($totalFacturado - $totalAbonado, 0, ',', '.');?></strong></td>
														<td colspan="2"></td>
													</tr>
												</tfoot>
                                            </table>
                                        </div>
                                    </div>
                        </div>
                    </div>
                </div>
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
    <!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
    <script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js"></script>
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


