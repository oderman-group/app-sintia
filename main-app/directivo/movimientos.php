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
											<th style="width: 30px;"></th>
											<th style="width: 40px;"><input type="checkbox" id="selectAllFacturas" title="Seleccionar todas las facturas habilitadas"></th>
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
										$hayRegistros = !empty($data["data"]) && count($data["data"]) > 0;
										include("../class/componentes/result/movimientos-tbody.php");
										
										?>
									</tbody>
									<script>
										$(document).ready(totalizarMovimientos);
										// Verificar si hay registros al cargar la página
										var hayRegistrosInicial = <?= $hayRegistros ? 'true' : 'false'; ?>;
										if (!hayRegistrosInicial) {
											$('.movimientos-actions-bar button[onclick="bloquearUsuariosPendientes()"]').hide();
											$('.movimientos-actions-bar button[onclick="recordarSaldoSeleccionados()"]').hide();
											$('.movimientos-actions-bar a[href*="mostrarAnuladas"]').hide();
											$('.movimientos-actions-bar a[href*="movimientos-reporte-morosos"]').hide();
										}
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

<!-- Modal Ver Abonos de Factura -->
<div class="modal fade" id="modalAbonosFactura" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header modal-header-custom">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">
					<i class="fa fa-hand-holding-usd"></i> Abonos de la Factura <span id="modalAbonosFacturaTitulo"></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Fecha</th>
								<th>Código</th>
								<th>Valor</th>
								<th>Método</th>
								<th>Responsable</th>
								<th>Observaciones</th>
							</tr>
						</thead>
						<tbody id="listaAbonosFactura">
							<tr>
								<td colspan="6" align="center">Seleccione una factura para consultar sus abonos.</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="infoAbonosFactura" style="margin-top: 10px;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Abono Rápido -->
<div class="modal fade" id="modalAbonoRapido" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header modal-header-custom">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">
					<i class="fa fa-hand-holding-usd"></i> Abono rápido a factura <span id="modalAbonoRapidoTitulo"></span>
				</h4>
			</div>
			<form id="formAbonoRapido" enctype="multipart/form-data">
				<input type="hidden" name="idFactura" id="abonoRapidoIdFactura">
				<div class="modal-body">
					<div class="alert alert-info">
						Saldo pendiente actual: <strong id="abonoRapidoSaldoPendiente">$0</strong>
					</div>
					<div class="form-group">
						<label>Valor del abono</label>
						<input type="number" min="0" step="1" class="form-control" id="abonoRapidoValor" name="valor" required>
					</div>
					<div class="form-group">
						<label>Método de pago</label>
						<select class="form-control" id="abonoRapidoMetodo" name="metodo" required>
							<option value="">Seleccione...</option>
							<option value="EFECTIVO">Efectivo</option>
							<option value="CHEQUE">Cheque</option>
							<option value="T_DEBITO">T. Débito</option>
							<option value="T_CREDITO">T. Crédito</option>
							<option value="TRANSFERENCIA">Transferencia</option>
							<option value="OTROS">Otras formas</option>
						</select>
					</div>
					<div class="form-group">
						<label>Observaciones</label>
						<textarea class="form-control" id="abonoRapidoObservaciones" name="observaciones" rows="3"></textarea>
					</div>
					<div class="form-group">
						<label>Comprobante (opcional)</label>
						<input type="file" class="form-control" id="abonoRapidoComprobante" name="comprobante" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn deepPink-bgcolor">Guardar abono</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	function mostrarResultado(dato) {
		// Verificar y ocultar/mostrar botones después de actualizar la tabla
		setTimeout(function() {
			verificarYMostrarOcultarBotones();
		}, 100);
		console.log(dato);
		$(document).ready(totalizarMovimientos);
	}

	function abrirModalAgregarMovimiento() {
		$('#modalAgregarMovimiento').modal('show');
		// Inicializar select2 para el usuario en el modal
		setTimeout(function() {
			// Destruir Select2 si ya está inicializado
			if ($('#select_usuario_modal').hasClass('select2-hidden-accessible')) {
				$('#select_usuario_modal').select2('destroy');
			}
			
			$('#select_usuario_modal').select2({
				placeholder: 'Seleccione el usuario...',
				allowClear: true,
				dropdownParent: $('#modalAgregarMovimiento'),
				ajax: {
					type: 'GET',
					url: '../compartido/ajax-listar-usuarios.php',
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							term: params.term || ''
						};
					},
					processResults: function(data) {
						// El endpoint ya devuelve JSON parseado
						if (!data || !Array.isArray(data)) {
							return { results: [] };
						}
						return {
							results: $.map(data, function(item) {
								return {
									id: item.value,
									text: item.label
								}
							})
						};
					},
					cache: true
				},
				minimumInputLength: 0
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
<script>
	$(document).ready(function(){
		if ($.fn.DataTable.isDataTable('#tablaItems')) {
			$('#tablaItems').DataTable().destroy();
		}

		var totalColumnas = $('#tablaItems thead th').length;
		var columnDefs = [
			{ orderable: false, searchable: false, targets: 0 },
			{ orderable: false, searchable: false, targets: 1 }
		];
		if (totalColumnas > 1) {
			columnDefs.push({ orderable: false, searchable: false, targets: totalColumnas - 1 });
		}

		var tablaMovimientos = $('#tablaItems').DataTable({
			columnDefs: columnDefs,
			order: [[3, 'desc']]
		});

		// Función para verificar si hay registros y ocultar/mostrar botones de acciones
		function verificarYMostrarOcultarBotones() {
			var totalFilas = 0;
			
			// Intentar obtener el conteo desde DataTables si está inicializado
			if ($.fn.DataTable.isDataTable('#tablaItems')) {
				totalFilas = tablaMovimientos.rows({ filter: 'applied' }).count();
			} else {
				// Si DataTables no está inicializado, contar filas directamente
				totalFilas = $('#tablaItems tbody tr.movimiento-row').length;
			}
			
			var hayRegistros = totalFilas > 0;
			
			// Ocultar/mostrar botones de acciones en la barra superior
			if (hayRegistros) {
				$('.movimientos-actions-bar button[onclick="bloquearUsuariosPendientes()"]').show();
				$('.movimientos-actions-bar button[onclick="recordarSaldoSeleccionados()"]').show();
				$('.movimientos-actions-bar a[href*="mostrarAnuladas"]').show();
				$('.movimientos-actions-bar a[href*="movimientos-reporte-morosos"]').show();
			} else {
				$('.movimientos-actions-bar button[onclick="bloquearUsuariosPendientes()"]').hide();
				$('.movimientos-actions-bar button[onclick="recordarSaldoSeleccionados()"]').hide();
				$('.movimientos-actions-bar a[href*="mostrarAnuladas"]').hide();
				$('.movimientos-actions-bar a[href*="movimientos-reporte-morosos"]').hide();
			}
		}

		totalizarMovimientos();
		verificarYMostrarOcultarBotones();
		
	tablaMovimientos.on('draw', function(){
		totalizarMovimientos();
		actualizarEstadoSeleccionGeneral();
		verificarYMostrarOcultarBotones();
	});

		function actualizarEstadoSeleccionGeneral(){
			var $checkboxes = $('#tablaItems tbody .factura-checkbox:not(:disabled)');
			if ($checkboxes.length === 0) {
				$('#selectAllFacturas').prop({ checked: false, indeterminate: false });
				return;
			}
			var seleccionadas = $checkboxes.filter(':checked').length;
			$('#selectAllFacturas').prop('checked', seleccionadas === $checkboxes.length);
			$('#selectAllFacturas').prop('indeterminate', seleccionadas > 0 && seleccionadas < $checkboxes.length);
		}

		$('#selectAllFacturas').on('change', function(){
			var estado = $(this).is(':checked');
			$('#tablaItems tbody .factura-checkbox:not(:disabled)').prop('checked', estado);
		});

		$(document).on('change', '#tablaItems tbody .factura-checkbox', function(){
			actualizarEstadoSeleccionGeneral();
		});

		$('#tablaItems tbody').on('click', '.detalle-movimiento-btn', function () {
			var $btn = $(this);
			var tr = $btn.closest('tr');
			var row = tablaMovimientos.row(tr);
			var idFactura = $btn.data('id');

			if (row.child.isShown()) {
				row.child.hide();
				tr.removeClass('detalle-abierto');
				$btn.removeClass('expanded');
				totalizarMovimientos();
			} else {
				$btn.addClass('expanded');
				tr.addClass('detalle-abierto');
				row.child('<div class="detalle-factura-wrapper">Cargando detalles...</div>').show();
				$.getJSON('ajax-detalle-factura.php', { idFactura: idFactura })
					.done(function (resp) {
						if (resp && resp.success) {
							row.child(resp.html).show();
						} else {
							row.child('<div class="detalle-factura-wrapper">No se encontraron detalles para esta factura.</div>').show();
						}
						totalizarMovimientos();
						actualizarEstadoSeleccionGeneral();
					})
					.fail(function () {
						row.child('<div class="detalle-factura-wrapper">Error al cargar los detalles. Intenta nuevamente.</div>').show();
						totalizarMovimientos();
						actualizarEstadoSeleccionGeneral();
					});
			}
		});

		actualizarEstadoSeleccionGeneral();
	});

	function sincronizarAbonos(idFactura, consecutivo){
		if(!confirm('¿Sincronizar abonos de la factura '+consecutivo+'?')){
			return;
		}

		$.post('ajax-sync-abonos.php', { idFactura: idFactura })
			.done(function(resp){
				if(resp && resp.success){
					$.toast({
						heading: 'Sincronización completada',
						text: 'Estado actualizado: ' + resp.estado + '.',
						position: 'bottom-right',
						icon: 'success',
						hideAfter: 4000
					});
					location.reload();
				}else{
					$.toast({
						heading: 'No se pudo sincronizar',
						text: resp && resp.message ? resp.message : 'Error al sincronizar la factura.',
						position: 'bottom-right',
						icon: 'warning',
						hideAfter: 4000
					});
				}
			})
			.fail(function(){
				$.toast({
					heading: 'Error',
					text: 'Error inesperado al sincronizar.',
					position: 'bottom-right',
					icon: 'error',
					hideAfter: 4000
				});
			});
	}

	function verAbonosFactura(idFactura, consecutivo){
		$('#modalAbonosFacturaTitulo').text(consecutivo);
		$('#listaAbonosFactura').html('<tr><td colspan="6" align="center">Cargando abonos...</td></tr>');
		$('#infoAbonosFactura').html('');
		$('#modalAbonosFactura').modal('show');

		$.get('ajax-abonos-por-factura.php', { idFactura: idFactura })
			.done(function(response){
				if(!response || response.success !== true){
					$('#listaAbonosFactura').html('<tr><td colspan="6" align="center">No se pudieron obtener los abonos.</td></tr>');
					return;
				}

				if(response.data.length === 0){
					$('#listaAbonosFactura').html('<tr><td colspan="6" align="center">Esta factura no tiene abonos registrados.</td></tr>');
					return;
				}

				var total = 0;
				var rows = '';
				response.data.forEach(function(item){
					var valor = parseFloat(item.payment || 0);
					total += valor;
					rows += '<tr>' +
						'<td>'+ (item.registration_date || '') +'</td>' +
						'<td>'+ (item.cod_payment || '') +'</td>' +
						'<td>$'+ new Intl.NumberFormat("es-CO").format(valor) +'</td>' +
						'<td>'+ (item.payment_method || 'N/A') +'</td>' +
						'<td>'+ (item.responsible_name || 'N/A') +'</td>' +
						'<td>'+ (item.observation ? $('<div>').text(item.observation).html() : '—') +'</td>' +
					'</tr>';
				});

				$('#listaAbonosFactura').html(rows);
				$('#infoAbonosFactura').html('<strong>Total abonado: </strong>$' + new Intl.NumberFormat("es-CO").format(total));
			})
			.fail(function(){
				$('#listaAbonosFactura').html('<tr><td colspan="6" align="center">Error consultando los abonos.</td></tr>');
			});
	}

	var saldoPendienteActual = 0;

	function abrirModalAbonoRapido(idFactura, consecutivo, saldoPendiente){
		saldoPendienteActual = parseFloat(saldoPendiente.replace(/[^\d]/g, ''));
		$('#abonoRapidoIdFactura').val(idFactura);
		$('#modalAbonoRapidoTitulo').text(consecutivo);
		$('#abonoRapidoSaldoPendiente').text('$' + saldoPendiente);
		$('#abonoRapidoValor').val('');
		$('#abonoRapidoMetodo').val('');
		$('#abonoRapidoObservaciones').val('');
		$('#modalAbonoRapido').modal('show');
	}

	$('#formAbonoRapido').on('submit', function(e){
		e.preventDefault();
		var valor = parseFloat($('#abonoRapidoValor').val() || 0);
		if (isNaN(valor) || valor <= 0) {
			$.toast({
				heading: 'Valor inválido',
				text: 'Ingrese un valor de abono mayor a cero.',
				position: 'bottom-right',
				icon: 'warning',
				hideAfter: 4000
			});
			return;
		}
		if (valor > saldoPendienteActual) {
			$.toast({
				heading: 'Valor excedido',
				text: 'El abono no puede superar el saldo pendiente.',
				position: 'bottom-right',
				icon: 'warning',
				hideAfter: 4000
			});
			return;
		}

		var formData = new FormData(this);
		$.ajax({
			url: 'ajax-abono-rapido.php',
			method: 'POST',
			data: formData,
			processData: false,
			contentType: false
		})
		.done(function(resp){
				if(resp && resp.success){
					$('#modalAbonoRapido').modal('hide');
					$.toast({
						heading: 'Abono registrado',
						text: resp.message || 'El abono se registró correctamente.',
						position: 'bottom-right',
						icon: 'success',
						hideAfter: 4000
					});
					location.reload();
				}else{
					$.toast({
						heading: 'No se pudo guardar',
						text: resp && resp.message ? resp.message : 'No fue posible registrar el abono.',
						position: 'bottom-right',
						icon: 'warning',
						hideAfter: 5000
					});
				}
			})
			.fail(function(){
				$.toast({
					heading: 'Error',
					text: 'Ocurrió un error al registrar el abono.',
					position: 'bottom-right',
					icon: 'error',
					hideAfter: 4000
				});
			});
	});

	function bloquearUsuarioFactura(idUsuario, idFactura, consecutivo, saldo){
		if(!confirm('¿Bloquear al usuario asociado a la factura '+consecutivo+'? Saldo pendiente: $'+saldo)){
			return;
		}

		$.post('ajax-bloquear-usuario-factura.php', {
			usuario: idUsuario,
			factura: idFactura,
			saldo: saldo
		})
		.done(function(response){
			if(response && response.success){
				$.toast({
					heading: 'Usuario bloqueado',
					text: response.message || 'Se bloqueó correctamente.',
					position: 'bottom-right',
					icon: 'success',
					hideAfter: 4000
				});
			}else{
				$.toast({
					heading: 'Aviso',
					text: response && response.message ? response.message : 'No fue posible bloquear al usuario.',
					position: 'bottom-right',
					icon: 'warning',
					hideAfter: 4000
				});
			}
		})
		.fail(function(){
			$.toast({
				heading: 'Error',
				text: 'Ocurrió un error y no se pudo bloquear al usuario.',
				position: 'bottom-right',
				icon: 'error',
				hideAfter: 4000
			});
		});
	}

	function obtenerFacturasSeleccionadas(incluirTodas){
		var facturas = [];
		$('#tablaItems tbody tr').each(function(){
			var $fila = $(this);
			if ($fila.hasClass('child')) {
				return;
			}
			var $checkbox = $fila.find('.factura-checkbox');
			if ($checkbox.length === 0 || $checkbox.is(':disabled')) {
				return;
			}
			if (!incluirTodas && !$checkbox.is(':checked')) {
				return;
			}
			var facturaId = $checkbox.data('factura');
			if (!facturaId) {
				return;
			}
			facturas.push(facturaId);
		});
		return facturas;
	}

	function enviarRecordatoriosFacturas(ids){
		if (!ids || !ids.length) {
			$.toast({
				heading: 'Sin facturas',
				text: 'No hay facturas con saldo pendiente para enviar recordatorios.',
				position: 'bottom-right',
				icon: 'info',
				hideAfter: 4000
			});
			return;
		}

		$('#gifCarga').show();
		$.ajax({
			url: 'ajax-recordatorio-facturas.php',
			method: 'POST',
			data: { facturas: ids },
			dataType: 'json'
		}).done(function(resp){
			var mensaje = resp && resp.message ? resp.message : 'Se procesó la solicitud.';
			var omitidasInfo = '';
			if (resp && resp.detalle && resp.detalle.omitidas && resp.detalle.omitidas.length) {
				var razones = resp.detalle.omitidas.map(function(item){
					return (item.factura ? ('#' + item.factura + ': ') : '') + (item.razon || 'Omitida');
				}).join('<br>- ');
				omitidasInfo = '<br>Omitidas:<br>- ' + razones;
			}

			if(resp && resp.success){
				$.toast({
					heading: 'Recordatorios enviados',
					text: mensaje + omitidasInfo,
					position: 'bottom-right',
					icon: 'success',
					hideAfter: 6000
				});
			}else{
				$.toast({
					heading: 'Aviso',
					text: mensaje + omitidasInfo,
					position: 'bottom-right',
					loader:false,
					icon: 'warning',
					hideAfter: 6000
				});
			}
		}).fail(function(){
			$.toast({
				heading: 'Error',
				text: 'Ocurrió un error al enviar los recordatorios.',
				position: 'bottom-right',
				icon: 'error',
				hideAfter: 4000
			});
		}).always(function(){
			$('#gifCarga').hide();
		});
	}

	function enviarRecordatorioFactura(idFactura){
		if(!idFactura){ return; }
		if(!confirm('Se enviará un recordatorio de saldo para la factura seleccionada. ¿Deseas continuar?')){
			return;
		}
		enviarRecordatoriosFacturas([idFactura]);
	}

	function recordarSaldoSeleccionados(){
		var seleccionadas = obtenerFacturasSeleccionadas(false);
		var mensajeConfirmacion = '';

		if (seleccionadas.length === 0) {
			seleccionadas = obtenerFacturasSeleccionadas(true);
			if (seleccionadas.length === 0) {
				$.toast({
					heading: 'Sin facturas disponibles',
					text: 'No hay facturas con saldo pendiente para enviar recordatorio.',
					position: 'bottom-right',
					icon: 'info',
					hideAfter: 4000
				});
				return;
			}
			mensajeConfirmacion = 'No seleccionaste facturas. Se enviará recordatorio a ' + seleccionadas.length + ' factura(s) con saldo pendiente. ¿Deseas continuar?';
		} else {
			mensajeConfirmacion = 'Se enviará recordatorio a ' + seleccionadas.length + ' factura(s) seleccionada(s). ¿Deseas continuar?';
		}

		if(!confirm(mensajeConfirmacion)){
			return;
		}

		enviarRecordatoriosFacturas(seleccionadas);
	}

	function bloquearUsuariosPendientes(){
		if(!confirm('¿Bloquear a todos los usuarios con saldo pendiente por cobrar?')){
			return;
		}

		$.post('ajax-bloquear-usuarios-pendientes.php')
		.done(function(response){
			if(response && response.success){
				var bloqueados = response.usuariosBloqueados ? response.usuariosBloqueados.length : 0;
				$.toast({
					heading: 'Proceso completado',
					text: 'Usuarios evaluados: '+ (response.totalEvaluados || 0) +'. Bloqueados: '+ bloqueados +'.',
					position: 'bottom-right',
					icon: 'success',
					hideAfter: 5000
				});
			}else{
				$.toast({
					heading: 'Aviso',
					text: response && response.message ? response.message : 'No fue posible completar la acción.',
					position: 'bottom-right',
					icon: 'warning',
					hideAfter: 4000
				});
			}
		})
		.fail(function(){
			$.toast({
				heading: 'Error',
				text: 'Ocurrió un error bloqueando a los usuarios.',
				position: 'bottom-right',
				icon: 'error',
				hideAfter: 4000
			});
		});
	}
</script>
<!-- end js include path -->
</body>

</html>