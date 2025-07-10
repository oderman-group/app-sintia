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
?>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title"><?=$frases[427][$datosUsuarioActual['uss_idioma']];?></div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row clearfix">
                            <div class="col-12 col-sm-12 col-lg-6">
                                <div class="card">
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
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-lg-6">
                                <div class="card">
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-12 col-sm-12 col-lg-6">
                                <div class="card">
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
                                <div class="card">
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

    <?php
        $consultaIngresosEgresos = Movimientos::TotalIngresosEgresos($conexion, $config);
        $labels1 = "";
        $dataIngresos = "";
        $dataEgresos = "";
        $title1 = 'true';
        if (mysqli_num_rows($consultaIngresosEgresos) > 0) {
            $nombres1 = "";
            $ingresos = array();
            $egresos = array();
            while ($row = mysqli_fetch_assoc($consultaIngresosEgresos)) {
                $nombres1 .= "'".$mesesAgno[$row['mes']]."', ";
                $ingresos[] = $row['totalIngresos'] + $row['abonosRecibidos'];
                $egresos[] = $row['totalEgresos'];
            }

            $labels1 = substr($nombres1, 0, -2);
            $dataIngresos = implode(", ", $ingresos);
            $dataEgresos = implode(", ", $egresos);
            $title1 = 'false';
        }
    ?>
    const ctx1 = document.getElementById('chart1');
    new Chart(ctx1, {
        type: 'bar',
        data: {
        labels: [<?=$labels1?>],
        datasets: [
            {
                label: 'Ingresos',
                data: [<?=$dataIngresos?>],
                borderWidth: 1
            },
            {
                label: 'Egresos',
                data: [<?=$dataEgresos?>],
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
                    display: <?=$title1?>,
                    text: 'No se encontraron registros'
                }
            }
        }
    });

    <?php
        $consultaIngresosEgresos = Movimientos::cuentasPorCobrar($conexion, $config);
        $labels2 = "";
        $dataTotalPorCobrar = "";
        $title2 = 'true';
        if (mysqli_num_rows($consultaIngresosEgresos) > 0) {
            $nombres2 = "";
            $totalPorCobrar = array();
            while ($row = mysqli_fetch_assoc($consultaIngresosEgresos)) {
                $nombres2 .= "'".$mesesAgno[$row['mes']]."', ";
                $totalPorCobrar[] = $row['totalPorCobrar'];
            }

            $labels2 = substr($nombres2, 0, -2);
            $dataTotalPorCobrar = implode(", ", $totalPorCobrar);
            $title2 = 'false';
        }
    ?>
    const ctx2 = document.getElementById('chart2');
    new Chart(ctx2, {
        type: 'bar',
        data: {
        labels: [<?=$labels2?>],
        datasets: [
            {
                label: 'Total por cobrar',
                data: [<?=$dataTotalPorCobrar?>],
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
                    display: <?=$title2?>,
                    text: 'No se encontraron registros'
                }
            }
        }
    });

    <?php
        $consultaMejoresClientes = Movimientos::mejorCliente($conexion, $config);
        $labels3 = "";
        $data3 = "";
        $title3 = 'true';
        if (mysqli_num_rows($consultaMejoresClientes) > 0) {
            $nombres3 = "";
            $datos3 = array ();
            while ($row = mysqli_fetch_assoc($consultaMejoresClientes)) {
                $nombres3 .= "'".UsuariosPadre::nombreCompletoDelUsuario($row)."', ";
                $datos3[] = $row['totalPagado'];
            }

            $labels3 = substr($nombres3, 0, -2);
            $data3 = implode(", ", $datos3);
            $title3 = 'false';
        }
    ?>
    const ctx3 = document.getElementById('chart3');
    new Chart(ctx3, {
        type: 'doughnut',
        data: {
        labels: [<?=$labels3?>],
        datasets: [
            {
                label: 'Total neto de facturas cobradas',
                data: [<?=$data3?>],
                borderWidth: 1
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
                    display: <?=$title3?>,
                    text: 'No se encontraron registros'
                }
            }
        }
    });

    <?php
        $consultaItemsMasVendidos = Movimientos::itemsMasVendidos($conexion, $config);
        $labels4 = "";
        $data4 = "";
        $title4 = 'true';
        if (mysqli_num_rows($consultaItemsMasVendidos) > 0) {
            $nombres4 = "";
            $datos4 = array ();
            while ($row = mysqli_fetch_assoc($consultaItemsMasVendidos)) {
                $nombres4 .= "'".$row['name']."', ";
                $datos4[] = $row['cantidadTotal'];
            }

            $labels4 = substr($nombres4, 0, -2);
            $data4 = implode(", ", $datos4);
            $title4 = 'false';
        }
    ?>
    const ctx4 = document.getElementById('chart4');
    new Chart(ctx4, {
        type: 'doughnut',
        data: {
            labels: [<?=$labels4?>],
            datasets: [
                {
                    label: 'Unidades Vendidas',
                    data: [<?=$data4?>],
                    borderWidth: 1
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
                    display: <?=$title4?>,
                    text: 'No se encontraron registros'
                }
            }
        }
    });
</script>


<!-- end js include path -->
</body>

</html>