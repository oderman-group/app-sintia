<?php
include("session.php");
$idPaginaInterna = 'DT0332';
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

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
                            <div class="page-title">CONSUMO DEL PLAN</div>
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
                                        <header>USUARIOS</header>
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
                                        <header>USO DEL DISCO</header>
                                        <div class="tools">
                                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php include(ROOT_PATH."/main-app/compartido/peso.php");?>
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
        $datosPlan = Plataforma::traerDatosPlanes($conexion, $datosUnicosInstitucion['ins_id_plan']);
        $datosPaquetes = Plataforma::contarDatosPaquetes($datosUnicosInstitucion['ins_id'], PAQUETES);
        $totalDirectivos = $datosPlan['plns_cant_directivos'] + $datosPaquetes['plns_cant_directivos'];
        $totalDocentes = $datosPlan['plns_cant_docentes'] + $datosPaquetes['plns_cant_docentes'];
        $totalEstudianteAcudientes = $datosPlan['plns_cant_estudiantes'] + $datosPaquetes['plns_cant_estudiantes'];

        
        $directivosCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 5);
        $docentesCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 2);
        $acudientesCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 3);
        $estudiantesCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 4);
        $estudianteAcudientesCreados = $acudientesCreados + $estudiantesCreados;

        
        $restanteDirectivos = !empty($datosPlan['plns_cant_directivos']) ? ($totalDirectivos - $directivosCreados) : 0;
        $restanteDocentes = !empty($datosPlan['plns_cant_docentes']) ? ($totalDocentes - $docentesCreados) : 0;
        $restanteEstudianteAcudientes = $totalEstudianteAcudientes - $estudianteAcudientesCreados;

        $infinitos = $datosUnicosInstitucion['ins_id_plan'] == 3 ? " (Infinitos)" : "";
    ?>
    const ctx1 = document.getElementById('chart1');

    const data = {
        labels: ['Directivos restantes<?=$infinitos?>', 'Directivos creados', 'Docentes restantes<?=$infinitos?>', 'Docentes creados', 'Estudiantes y acudientes restantes', 'Estudiantes y acudientes creados'],
        datasets: [
            {
                backgroundColor: ['hsl(180, 100%, 60%)', 'hsl(180, 100%, 35%)'],
                data: [<?=$restanteDirectivos?>, <?=$directivosCreados?>]
            },
            {
                backgroundColor: ['hsl(0, 100%, 60%)', 'hsl(0, 100%, 35%)'],
                data: [<?=$restanteDocentes?>, <?=$docentesCreados?>]
            },
            {
                backgroundColor: ['hsl(100, 100%, 60%)', 'hsl(100, 100%, 35%)'],
                data: [<?=$restanteEstudianteAcudientes?>, <?=$estudianteAcudientesCreados?>]
            }
        ]
    };
    const config = {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                    generateLabels: function(chart) {
                        // Get the default label list
                        const original = Chart.overrides.pie.plugins.legend.labels.generateLabels;
                        const labelsOriginal = original.call(this, chart);

                        // Build an array of colors used in the datasets of the chart
                        let datasetColors = chart.data.datasets.map(function(e) {
                            return e.backgroundColor;
                        });
                        datasetColors = datasetColors.flat();

                        // Modify the color and hide state of each label
                        labelsOriginal.forEach(label => {
                            // There are twice as many labels as there are datasets. This converts the label index into the corresponding dataset index
                            label.datasetIndex = (label.index - label.index % 2) / 2;

                            // The hidden state must match the dataset's hidden state
                            label.hidden = !chart.isDatasetVisible(label.datasetIndex);

                            // Change the color to match the dataset
                            label.fillStyle = datasetColors[label.index];
                        });

                        return labelsOriginal;
                    }
                    },
                    onClick: function(mouseEvent, legendItem, legend) {
                        // toggle the visibility of the dataset from what it currently is
                        legend.chart.getDatasetMeta(
                            legendItem.datasetIndex
                        ).hidden = legend.chart.isDatasetVisible(legendItem.datasetIndex);
                        legend.chart.update();
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const labelIndex = (context.datasetIndex * 2) + context.dataIndex;
                            return context.chart.data.labels[labelIndex] + ': ' + context.formattedValue;
                        }
                    }
                }
            }
        },
    };
    new Chart(ctx1, config);
</script>


<!-- end js include path -->
</body>

</html>