<?php
include("session.php");
$idPaginaInterna = 'DT0305';
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");

$consultaIngresosEgresos = Movimientos::TotalIngresosEgresos($conexion, $config);
$chart1Labels = [];
$chart1Ingresos = [];
$chart1Egresos = [];
$chart1Facturado = [];
$chart1AbonosVentas = [];
$chart1PagosCompras = [];
$ingresosTotales = 0;
$egresosTotales = 0;
$facturadoTotal = 0;
$abonosVentasTotales = 0;
$pagosComprasTotales = 0;
if ($consultaIngresosEgresos && mysqli_num_rows($consultaIngresosEgresos) > 0) {
    while ($row = mysqli_fetch_assoc($consultaIngresosEgresos)) {
        $mesIndex = (int)$row['mes'];
        $chart1Labels[] = isset($mesesAgno[$mesIndex]) ? $mesesAgno[$mesIndex] : $row['mes'];
        $ingreso = (float)$row['totalIngresos'];
        $egreso = (float)$row['totalEgresos'];
        $facturado = (float)$row['totalFacturado'];
        $abonadoVentas = (float)$row['totalAbonosVentas'];
        $abonadoCompras = (float)$row['totalAbonosEgreso'];
        $chart1Ingresos[] = $ingreso;
        $chart1Egresos[] = $egreso;
        $chart1Facturado[] = $facturado;
        $chart1AbonosVentas[] = $abonadoVentas;
        $chart1PagosCompras[] = $abonadoCompras;
        $ingresosTotales += $ingreso;
        $egresosTotales += $egreso;
        $facturadoTotal += $facturado;
        $abonosVentasTotales += $abonadoVentas;
        $pagosComprasTotales += $abonadoCompras;
    }
}

$consultaCuentasPorCobrar = Movimientos::cuentasPorCobrar($conexion, $config);
$chart2Labels = [];
$chart2PorCobrar = [];
$totalPendienteCobro = 0;
if ($consultaCuentasPorCobrar && mysqli_num_rows($consultaCuentasPorCobrar) > 0) {
    while ($row = mysqli_fetch_assoc($consultaCuentasPorCobrar)) {
        $mesIndex = (int)$row['mes'];
        $chart2Labels[] = isset($mesesAgno[$mesIndex]) ? $mesesAgno[$mesIndex] : $row['mes'];
        $pendiente = max((float)$row['totalPorCobrar'], 0);
        $chart2PorCobrar[] = $pendiente;
        $totalPendienteCobro += $pendiente;
    }
}

$consultaMejoresClientes = Movimientos::mejorCliente($conexion, $config);
$chart3Labels = [];
$chart3Data = [];
if ($consultaMejoresClientes && mysqli_num_rows($consultaMejoresClientes) > 0) {
while ($row = mysqli_fetch_assoc($consultaMejoresClientes)) {
        $chart3Labels[] = UsuariosPadre::nombreCompletoDelUsuario($row);
        $chart3Data[] = (float)$row['totalPagado'];
    }
$chart3Labels = array_slice($chart3Labels, 0, 3);
$chart3Data = array_slice($chart3Data, 0, 3);
}
$totalClientesActivos = count($chart3Labels);

$consultaItemsMasVendidos = Movimientos::itemsMasVendidos($conexion, $config);
$chart4Labels = [];
$chart4Data = [];
if ($consultaItemsMasVendidos && mysqli_num_rows($consultaItemsMasVendidos) > 0) {
    while ($row = mysqli_fetch_assoc($consultaItemsMasVendidos)) {
        $chart4Labels[] = $row['name'];
        $chart4Data[] = (int)$row['cantidadTotal'];
    }
}

?>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../css/movimientos-reportes.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <div class="page-content-wrapper finance-dashboard">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title"><?=$frases[427][$datosUsuarioActual['uss_idioma']];?></div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>
				
				<!-- Descripción de la página -->
				<div class="row mb-3">
					<div class="col-md-12">
						<p class="text-muted" style="font-size: 14px; line-height: 1.6;">
							<i class="fa fa-info-circle text-info"></i> 
							<?=__('financiero.reportes_graficos_descripcion');?>
						</p>
					</div>
				</div>

    <div class="finance-summary-grid">
                    <div class="finance-summary-card">
                        <div class="icon-badge badge-ingresos"><i class="fa fa-arrow-up"></i></div>
                        <span>Ingresos acumulados</span>
                        <h3>$<?=number_format($ingresosTotales, 0, ",", ".");?></h3>
                    </div>
                    <div class="finance-summary-card">
                        <div class="icon-badge badge-egresos"><i class="fa fa-arrow-down"></i></div>
                        <span>Egresos acumulados</span>
                        <h3>$<?=number_format($egresosTotales, 0, ",", ".");?></h3>
                    </div>
                    <div class="finance-summary-card">
                        <div class="icon-badge badge-cobrar"><i class="fa fa-hourglass-half"></i></div>
                        <span>Total por cobrar</span>
                        <h3>$<?=number_format($totalPendienteCobro, 0, ",", ".");?></h3>
                    </div>
                    <div class="finance-summary-card">
                        <div class="icon-badge badge-clientes"><i class="fa fa-users"></i></div>
                        <span>Clientes con compras</span>
                        <h3><?=number_format($totalClientesActivos, 0, ",", ".");?></h3>
                    </div>
        <div class="finance-summary-card">
            <div class="icon-badge" style="background: linear-gradient(135deg,#6366f1 0%, #312e81 100%);"><i class="fa fa-file-invoice"></i></div>
            <span>Facturado total</span>
            <h3>$<?=number_format($facturadoTotal, 0, ",", ".");?></h3>
        </div>
        <div class="finance-summary-card">
            <div class="icon-badge" style="background: linear-gradient(135deg,#0ea5e9 0%, #0369a1 100%);"><i class="fa fa-hand-holding-usd"></i></div>
            <span>Cobros (abonos ventas)</span>
            <h3>$<?=number_format($abonosVentasTotales, 0, ",", ".");?></h3>
        </div>
        <div class="finance-summary-card">
            <div class="icon-badge" style="background: linear-gradient(135deg,#f97316 0%, #9a3412 100%);"><i class="fa fa-money-bill-wave"></i></div>
            <span>Pagos compras</span>
            <h3>$<?=number_format($pagosComprasTotales, 0, ",", ".");?></h3>
        </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row clearfix">
                            <div class="col-12 col-sm-12 col-lg-6">
                                <div class="card finance-chart-card">
                                    <div class="card-head">
                                        <header>INGRESOS/GASTOS</header>
                                        <div class="tools">
                                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="recent-report__chart">
                                            <canvas id="chart1" style="min-height: 365px;">
                                            </canvas>
                                        </div>
                                        <div class="chart-legend-custom">
                                            <span class="legend-ingresos">Ingresos</span>
                                            <span class="legend-egresos">Egresos</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-lg-6">
                                <div class="card finance-chart-card">
                                    <div class="card-head">
                                        <header>CUENTAS POR COBRAR</header>
                                        <div class="tools">
                                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="recent-report__chart">
                                            <canvas id="chart2" style="min-height: 365px;">
                                            </canvas>
                                        </div>
                                        <div class="chart-legend-custom">
                                            <span class="legend-porcobrar">Saldo pendiente</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-12 col-sm-12 col-lg-6">
                                <div class="card finance-chart-card">
                                    <div class="card-head">
                                        <header>MEJORES CLIENTES</header>
                                        <div class="tools">
                                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="recent-report__chart">
                                            <canvas id="chart3" style="min-height: 365px;">
                                            </canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 col-sm-12 col-lg-6">
                                <div class="card finance-chart-card">
                                    <div class="card-head">
                                        <header>ITEMS MÁS VENDIDOS</header>
                                        <div class="tools">
                                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="recent-report__chart">
                                            <canvas id="chart4" style="min-height: 365px;">
                                            </canvas>
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

<script>
    const chart1Labels = <?php echo json_encode($chart1Labels, JSON_UNESCAPED_UNICODE); ?>;
    const chart1Ingresos = <?php echo json_encode($chart1Ingresos); ?>;
    const chart1Egresos = <?php echo json_encode($chart1Egresos); ?>;
    const chart1Facturado = <?php echo json_encode($chart1Facturado); ?>;
    const chart1AbonosVentas = <?php echo json_encode($chart1AbonosVentas); ?>;
    const chart1PagosCompras = <?php echo json_encode($chart1PagosCompras); ?>;
    const chart2Labels = <?php echo json_encode($chart2Labels, JSON_UNESCAPED_UNICODE); ?>;
    const chart2PorCobrar = <?php echo json_encode($chart2PorCobrar); ?>;
    const chart3Labels = <?php echo json_encode($chart3Labels, JSON_UNESCAPED_UNICODE); ?>;
    const chart3Data = <?php echo json_encode($chart3Data); ?>;
    const chart4Labels = <?php echo json_encode($chart4Labels, JSON_UNESCAPED_UNICODE); ?>;
    const chart4Data = <?php echo json_encode($chart4Data); ?>;

    const hasChart1Data = chart1Labels.length > 0;
    const ctx1 = document.getElementById('chart1');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: chart1Labels,
            datasets: [
                {
                    label: 'Ingresos',
                    backgroundColor: '#34d399',
                    borderColor: '#34d399',
                    data: chart1Ingresos,
                    borderRadius: 6,
                    borderWidth: 1
                },
                {
                    label: 'Egresos',
                    backgroundColor: '#f97316',
                    borderColor: '#f97316',
                    data: chart1Egresos,
                    borderRadius: 6,
                    borderWidth: 1
                },
                {
                    label: 'Facturado',
                    type: 'line',
                    data: chart1Facturado,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.15)',
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2
                },
                {
                    label: 'Cobros (abonos ventas)',
                    type: 'line',
                    data: chart1AbonosVentas,
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14,165,233,0.15)',
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2
                },
                {
                    label: 'Pagos compras',
                    type: 'line',
                    data: chart1PagosCompras,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249,115,22,0.12)',
                    tension: 0.35,
                    fill: false,
                    borderWidth: 2,
                    borderDash: [6,4]
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive: true,
            plugins: {
                title: {
                    display: !hasChart1Data,
                    text: 'No se encontraron registros'
                }
            }
        }
    });

    const hasChart2Data = chart2Labels.length > 0;
    const ctx2 = document.getElementById('chart2');
    new Chart(ctx2, {
        type: 'bar',
        data: {
        labels: chart2Labels,
        datasets: [
            {
                label: 'Total por cobrar',
                data: chart2PorCobrar,
                backgroundColor: '#facc15',
                borderColor: '#facc15',
                borderRadius: 6,
                borderWidth: 1
            }
        ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive: true,
            plugins: {
                title: {
                    display: !hasChart2Data,
                    text: 'No se encontraron registros'
                }
            }
        }
    });

    const hasChart3Data = chart3Labels.length > 0;
    const ctx3 = document.getElementById('chart3');
    new Chart(ctx3, {
        type: 'doughnut',
        data: {
        labels: chart3Labels,
        datasets: [
            {
                label: 'Total neto de facturas cobradas',
                data: chart3Data,
                backgroundColor: [
                    '#6366f1','#34d399','#f97316','#60a5fa','#facc15','#f472b6','#2dd4bf','#a855f7'
                ],
                borderWidth: 0
            }
        ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                title: {
                    display: !hasChart3Data,
                    text: 'No se encontraron registros'
                }
            }
        }
    });

    const hasChart4Data = chart4Labels.length > 0;
    const ctx4 = document.getElementById('chart4');
    new Chart(ctx4, {
        type: 'doughnut',
        data: {
            labels: chart4Labels,
            datasets: [
                {
                    label: 'Unidades Vendidas',
                    data: chart4Data,
                    backgroundColor: [
                        '#64748b','#0ea5e9','#f97316','#10b981','#a855f7','#f59e0b','#ef4444','#22d3ee'
                    ],
                    borderWidth: 0
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: !hasChart4Data,
                    text: 'No se encontraron registros'
                }
            }
        }
    });
</script>


<!-- end js include path -->
</body>

</html>