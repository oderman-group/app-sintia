<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0104'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH . "/main-app/class/Movimientos.php");
$queryString = $_SERVER['QUERY_STRING'];// Parsear la cadena de consulta y almacenar los parámetros en un array
parse_str($queryString, $parametros);// Convertir el array a JSON
$filtros_json = json_encode($parametros);
if (!Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
} ?>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css"/>
<!-- Select2 -->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- Movimientos Mejorado CSS -->
<link href="../css/movimientos-mejorado.css" rel="stylesheet" type="text/css" />

</head>

<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
	<?php include("../compartido/encabezado.php"); ?>

	<?php include("../compartido/panel-color.php"); ?>
	<!-- start page container -->
	<div class="page-container">
		<?php include("../compartido/menu.php"); ?>
		<!-- start page content -->
		<div class="page-content-wrapper">
			<div class="page-content">
				<div class="page-bar">
					<div class="page-title-breadcrumb">
						<div class=" pull-left">
							<div class="page-title"><?= $frases[95][$datosUsuarioActual['uss_idioma']]; ?></div>
							<?php include("../compartido/texto-manual-ayuda.php"); ?>
						</div>
					</div>
				</div>

				<?php
				// Calcular KPIs usando los mismos métodos que la tabla (calcularTotalNeto y calcularTotalAbonado)
				$kpis = array(
					'totalVentas' => 0,
					'totalCompras' => 0,
					'totalPorCobrar' => 0,
					'totalCobrado' => 0
				);
				
				try {
					// Obtener todas las facturas no anuladas (o según filtro)
					$filtroAnuladas = '';
					if (empty($_GET['mostrarAnuladas']) || $_GET['mostrarAnuladas'] != '1') {
						$filtroAnuladas = " AND fcu_anulado=0";
					}
					
					$consultaFacturas = mysqli_query($conexion, "SELECT fcu_id, fcu_tipo, fcu_valor, fcu_status 
					FROM " . BD_FINANCIERA . ".finanzas_cuentas 
					WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} $filtroAnuladas");
					
					if ($consultaFacturas) {
						while ($factura = mysqli_fetch_array($consultaFacturas, MYSQLI_BOTH)) {
							$vlrAdicional = !empty($factura['fcu_valor']) ? $factura['fcu_valor'] : 0;
							$totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $factura['fcu_id'], $vlrAdicional);
							$abonos = Movimientos::calcularTotalAbonado($conexion, $config, $factura['fcu_id']);
							$porCobrar = $totalNeto - $abonos;
							
							if ($factura['fcu_tipo'] == 1) {
								// Factura Venta
								$kpis['totalVentas'] += $totalNeto;
								// Sumar TODOS los abonos de facturas de venta (independientemente del estado)
								$kpis['totalCobrado'] += $abonos;
								// Sumar solo el por cobrar de facturas con estado POR_COBRAR
								if ($factura['fcu_status'] == POR_COBRAR) {
									$kpis['totalPorCobrar'] += $porCobrar;
								}
							} else if ($factura['fcu_tipo'] == 2) {
								// Factura Compra
								$kpis['totalCompras'] += $totalNeto;
							}
						}
					}
				} catch (Exception $e) {
					include("../compartido/error-catch-to-report.php");
				}
				?>

				<!-- KPIs Dashboard -->
				<div class="row">
					<div class="col-lg-3 col-md-6">
						<div class="panel kpi-card ventas">
							<div class="panel-body" style="padding: 20px;">
								<div class="row">
									<div class="col-xs-4">
										<i class="fa fa-arrow-circle-up kpi-icon" style="color: #00c292;"></i>
									</div>
									<div class="col-xs-8 text-right">
										<div class="kpi-label">Fact. Venta</div>
										<div class="kpi-value">$<?= number_format($kpis['totalVentas'] ?? 0, 0, ",", "."); ?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6">
						<div class="panel kpi-card compras">
							<div class="panel-body" style="padding: 20px;">
								<div class="row">
									<div class="col-xs-4">
										<i class="fa fa-arrow-circle-down kpi-icon" style="color: #ff5722;"></i>
									</div>
									<div class="col-xs-8 text-right">
										<div class="kpi-label">Fact. Compra</div>
										<div class="kpi-value">$<?= number_format($kpis['totalCompras'] ?? 0, 0, ",", "."); ?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6">
						<div class="panel kpi-card por-cobrar">
							<div class="panel-body" style="padding: 20px;">
								<div class="row">
									<div class="col-xs-4">
										<i class="fa fa-clock-o kpi-icon" style="color: #ffc107;"></i>
									</div>
									<div class="col-xs-8 text-right">
										<div class="kpi-label">Por Cobrar</div>
										<div class="kpi-value">$<?= number_format($kpis['totalPorCobrar'] ?? 0, 0, ",", "."); ?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6">
						<div class="panel kpi-card cobrado">
							<div class="panel-body" style="padding: 20px;">
								<div class="row">
									<div class="col-xs-4">
										<i class="fa fa-check-circle kpi-icon" style="color: #03a9f4;"></i>
									</div>
									<div class="col-xs-8 text-right">
										<div class="kpi-label">Cobrado</div>
										<div class="kpi-value">$<?= number_format($kpis['totalCobrado'] ?? 0, 0, ",", "."); ?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">

						<?php include("../../config-general/mensajes-informativos.php"); ?>

						<?php include("includes/barra-superior-movimientos-financieros-componente.php"); ?>

						<?php include("../compartido/publicidad-lateral.php"); ?>

						<div class="card card-topline-purple table-responsive-custom" style="position: relative;">
							<div class="card-head">
								<header><?= $frases[95][$datosUsuarioActual['uss_idioma']]; ?></header>
								<div class="tools">
									<?php if (Modulos::validarPermisoEdicion() &&  Modulos::validarSubRol(['DT0106'])) { 
										$colorPrimario = isset($Plataforma->colorUno) ? $Plataforma->colorUno : '#667eea';
										$colorSecundario = isset($Plataforma->colorDos) ? $Plataforma->colorDos : '#764ba2';
									?>
									<button class="quick-action-btn" onclick="abrirModalAgregarMovimiento()" title="Agregar nueva transacción" style="background: linear-gradient(135deg, <?= $colorPrimario ?> 0%, <?= $colorSecundario ?> 100%);">
										<i class="fa fa-plus"></i>
									</button>
									<?php } ?>
									<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
									<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
									<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
								</div>
							</div>
							<div class="card-body">
								<div id="gifCarga" class="gif-carga" style="display:none;">
									<img alt="Cargando..." src="../../config-general/assets/images/loading.gif">
								</div>
								<table class="display" style="width:100%;" id="tablaItems">
									<thead>
										<tr>
											<th>#</th>
											<th><?= $frases[49][$datosUsuarioActual['uss_idioma']]; ?></th>
											<th>Fecha</th>
											<th>Detalle</th>
											<th><?= $frases[107][$datosUsuarioActual['uss_idioma']]; ?></th>
											<th><?= $frases[417][$datosUsuarioActual['uss_idioma']]; ?></th>
											<th><?= $frases[418][$datosUsuarioActual['uss_idioma']]; ?></th>
											<th>Tipo</th>
											<th>Usuario</th>
											<th><?= $frases[246][$datosUsuarioActual['uss_idioma']]; ?></th>
											<?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0128', 'DT0089'])) { ?>
												<th><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></th>
											<?php } ?>
										</tr>
									</thead>
									<tbody id="movimientos_result">
										<?php
										include("includes/consulta-paginacion-movimientos.php");

										try {
											// Aplicar filtro de anuladas si no se está mostrando (ya está en $filtro desde barra-superior)
											$consulta = mysqli_query($conexion, "SELECT fc.*, uss.*, fc.id_nuevo AS id_nuevo_movimientos FROM " . BD_FINANCIERA . ".finanzas_cuentas fc
														INNER JOIN " . BD_GENERAL . ".usuarios uss 
															ON uss_id=fcu_usuario 
															AND uss.institucion={$config['conf_id_institucion']} 
															AND uss.year={$_SESSION["bd"]}
														WHERE 
															fcu_id=fcu_id 
														AND fc.institucion={$config['conf_id_institucion']} 
														AND fc.year={$_SESSION["bd"]} 
														$filtro
														ORDER BY fc.id_nuevo DESC
														");
										} catch (Exception $e) {
											include("../compartido/error-catch-to-report.php");
										}
										$data =$barraSuperior->builderArray($consulta);
										include("../class/componentes/result/movimientos-tbody.php");
										
										?>
									</tbody>
									<script>
										$(document).ready(totalizarMovimientos);
									</script>
								</table>
							</div>
						</div>
						<?php //include("enlaces-paginacion.php"); ?>

						<!-- Resumen rápido debajo de la tabla -->
						<div class="row" style="margin-top: 20px;">
							<div class="col-md-6">
								<div class="panel" style="border-left: 4px solid #00c292;">
									<header class="panel-heading" style="background: #f8f9fa; font-weight: bold;">TOTAL FACT. VENTA</header>
									<div class="panel-body">
										<table style="width: 100%;" align="center">
											<tr>
												<td style="padding: 8px;">TOTAL FACTURAS:</td>
												<td align="right" style="font-weight: bold;" id="totalNetoVenta">$0</td>
											</tr>
											<tr>
												<td style="padding: 8px;">TOTAL COBRADO:</td>
												<td align="right" style="font-weight: bold; color: #00c292;" id="abonosNetoVenta">$0</td>
											</tr>
											<tr style="font-size: 16px; font-weight:bold; border-top: 2px solid #eee;">
												<td style="padding: 8px;">TOTAL POR COBRAR:</td>
												<td align="right" style="color: #ffc107;" id="porCobrarNetoVenta">$0</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel" style="border-left: 4px solid #ff5722;">
									<header class="panel-heading" style="background: #f8f9fa; font-weight: bold;">TOTAL FACT. COMPRA</header>
									<div class="panel-body">
										<table style="width: 100%;" align="center">
											<tr>
												<td style="padding: 8px;">TOTAL FACTURAS:</td>
												<td align="right" style="font-weight: bold;" id="totalNetoCompra">$0</td>
											</tr>
											<tr>
												<td style="padding: 8px;">TOTAL PAGADO:</td>
												<td align="right" style="font-weight: bold; color: #00c292;" id="abonosNetoCompra">$0</td>
											</tr>
											<tr style="font-size: 16px; font-weight:bold; border-top: 2px solid #eee;">
												<td style="padding: 8px;">TOTAL POR PAGAR:</td>
												<td align="right" style="color: #ffc107;" id="porCobrarNetoCompra">$0</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<!-- Modal Agregar Movimiento Rápido -->
<div class="modal fade" id="modalAgregarMovimiento" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header modal-header-custom">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">
					<i class="fa fa-plus-circle"></i> Agregar Nueva Transacción
				</h4>
			</div>
			<form id="formAgregarMovimiento" action="movimientos-guardar.php" method="post">
				<?php 
				$codigoUnico = Utilidades::generateCode("FCN");
				?>
				<input type="hidden" value="<?=$codigoUnico?>" name="idU" id="idTransactionModal">
				<input type="hidden" value="<?=TIPO_FACTURA;?>" name="typeTransaction">
				
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Usuario <span style="color: red;">*</span></label>
								<select class="form-control select2-modal" name="usuario" id="select_usuario_modal" required>
									<option value="">Seleccione un usuario...</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Fecha <span style="color: red;">*</span></label>
								<input type="date" name="fecha" class="form-control" required value="<?=date('Y-m-d');?>">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Tipo de movimiento <span style="color: red;">*</span></label>
								<select class="form-control" name="tipo" required>
									<option value="">Seleccione una opción</option>
									<option value="1">Fact. Venta</option>
									<option value="2">Fact. Compra</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Medio de pago <span style="color: red;">*</span></label>
								<select class="form-control" name="forma" required>
									<option value="">Seleccione una opción</option>
									<option value="1">Efectivo</option>
									<option value="2">Cheque</option>
									<option value="3">T. Débito</option>
									<option value="4">T. Crédito</option>
									<option value="5">Transferencia</option>
									<option value="6">No aplica</option>
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label>Descripción general <span style="color: red;">*</span></label>
								<input type="text" name="detalle" class="form-control" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Valor adicional</label>
								<input type="number" min="0" name="valor" class="form-control" value="0" required>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>
									<input name="abonoAutomatico" type="checkbox" value="1">
									Añadir Abono Automático
								</label>
								<small class="help-block">Marcar si la transacción ya está pagada</small>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Observaciones</label>
								<textarea name="obs" class="form-control" rows="3" placeholder="Información adicional..."></textarea>
							</div>
						</div>
					</div>

					<div class="alert alert-info">
						<i class="fa fa-info-circle"></i> 
						<strong>Nota:</strong> Para agregar items detallados, guarde primero y luego edite la transacción.
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn deepPink-bgcolor">
						<i class="fa fa-save"></i> Guardar Transacción
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	function mostrarResultado(dato) {
		console.log(dato);
		$(document).ready(totalizarMovimientos);
	}

	function abrirModalAgregarMovimiento() {
		$('#modalAgregarMovimiento').modal('show');
		// Inicializar select2 para el usuario en el modal
		setTimeout(function() {
			$('#select_usuario_modal').select2({
				placeholder: 'Seleccione el usuario...',
				dropdownParent: $('#modalAgregarMovimiento'),
				ajax: {
					type: 'GET',
					url: '../compartido/ajax-listar-usuarios.php',
					processResults: function(data) {
						data = JSON.parse(data);
						return {
							results: $.map(data, function(item) {
								return {
									id: item.value,
									text: item.label
								}
							})
						};
					}
				}
			});
		}, 300);
	}

	// Asegurar que el dropdown de acciones se posicione correctamente
	$(document).ready(function() {
		// Manejar el evento cuando se abre un dropdown
		$(document).on('show.bs.dropdown', '#tablaItems .btn-group', function(e) {
			var $dropdown = $(this).find('.dropdown-menu');
			var $btnGroup = $(this);
			
			// Asegurar que el td tenga overflow visible
			$btnGroup.closest('td').css({
				'overflow': 'visible',
				'position': 'relative'
			});
			
			// Configurar el dropdown
			$dropdown.css({
				'z-index': '10000',
				'position': 'absolute',
				'top': '100%',
				'left': '0',
				'margin-top': '0.125rem',
				'transform': 'none'
			});
		});

		// Asegurar z-index cuando DataTables se inicializa o redibuja
		if ($.fn.DataTable.isDataTable('#tablaItems')) {
			var table = $('#tablaItems').DataTable();
			table.on('draw', function() {
				$('#tablaItems tbody tr td:last-child').css({
					'overflow': 'visible',
					'position': 'relative'
				});
				$('#tablaItems .btn-group .dropdown-menu').css({
					'z-index': '10000',
					'position': 'absolute',
					'top': '100%',
					'left': '0'
				});
			});
		}
		
		// También aplicar después de que se carga la tabla inicialmente
		setTimeout(function() {
			$('#tablaItems tbody tr td:last-child').css({
				'overflow': 'visible',
				'position': 'relative'
			});
		}, 500);
	});
</script>
<!-- end page content -->
<?php // include("../compartido/panel-configuracion.php");
?>
</div>
<!-- end page container -->
<?php include("../compartido/footer.php"); ?>
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- data tables -->
<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
<script src="../../config-general/assets/js/pages/table/table_data.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!-- Select2 -->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<!-- Movimientos JS -->
<script src="../js/Movimientos.js"></script>
<!-- end js include path -->
</body>

</html>