<?php
require_once(ROOT_PATH."/main-app/class/Movimientos.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
?>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="page-content">
    <div class="page-bar">
        <div class="page-title-breadcrumb">
            <div class=" pull-left">
                <div class="page-title">Dashboard Directivos</div>
                <?php include("../compartido/texto-manual-ayuda.php"); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="row clearfix">
                <!-- ESTUDIANTES POR CURSO -->
                <div class="col-12 col-sm-12 col-lg-6">
                    <div class="card">
                        <div class="card-head">
                            <header>ESTUDIANTES POR CURSO</header>
                            <div class="tools">
                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="recent-report__chart">
                                <canvas id="chartEstudiantesCurso" style="min-height: 365px;">
                                </canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PROMEDIO ESTUDIANTIL POR CURSO -->
                <div class="col-12 col-sm-12 col-lg-6">
                    <div class="card">
                        <div class="card-head">
                            <header>PROMEDIO ESTUDIANTIL POR CURSO</header>
                            <div class="tools">
                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="recent-report__chart">
                                <canvas id="chartPromediosCurso" style="min-height: 365px;">
                                </canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row clearfix">
                <!-- INGRESOS A LA PLATAFORMA POR TIPO DE USUARIO -->
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-head">
                            <header>INGRESOS A LA PLATAFORMA POR DÍA Y TIPO DE USUARIO</header>
                            <div class="tools">
                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="recent-report__chart">
                                <canvas id="chartIngresosPlataforma" style="min-height: 365px;">
                                </canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
<?php
// Función para obtener estudiantes por curso
function obtenerEstudiantesPorCurso($config) {
    global $conexion;
    $sql = "SELECT gra.gra_nombre, COUNT(mat.mat_id) as total_estudiantes
            FROM ".BD_ACADEMICA.".academico_grados gra
            LEFT JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat.mat_grado = gra.gra_id
                AND mat.mat_eliminado = 0
                AND mat.mat_estado_matricula IN (1, 2)
                AND mat.institucion = gra.institucion
                AND mat.year = gra.year
            WHERE gra.institucion = ?
                AND gra.year = ?
            GROUP BY gra.gra_id, gra.gra_nombre
            ORDER BY gra.gra_vocal, gra.gra_nombre";

    $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
    $consulta = BindSQL::prepararSQL($sql, $parametros);

    return $consulta;
}

// Función para obtener promedios por curso
function obtenerPromediosPorCurso($config) {
    global $conexion;
    $sql = "SELECT gra.gra_nombre,
                   ROUND(AVG(
                       CASE
                           WHEN bol_periodo = 1 THEN bol_nota
                           WHEN bol_periodo = 2 THEN bol_nota
                           WHEN bol_periodo = 3 THEN bol_nota
                           WHEN bol_periodo = 4 THEN bol_nota
                           ELSE 0
                       END
                   ), 1) as promedio_general
            FROM ".BD_ACADEMICA.".academico_grados gra
            LEFT JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat.mat_grado = gra.gra_id
                AND mat.mat_eliminado = 0
                AND mat.mat_estado_matricula IN (1, 2)
                AND mat.institucion = gra.institucion
                AND mat.year = gra.year
            LEFT JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol.bol_estudiante = mat.mat_id
                AND bol.institucion = mat.institucion
                AND bol.year = mat.year
            WHERE gra.institucion = ?
                AND gra.year = ?
                AND bol.bol_nota IS NOT NULL
            GROUP BY gra.gra_id, gra.gra_nombre
            ORDER BY gra.gra_vocal, gra.gra_nombre";

    $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
    $consulta = BindSQL::prepararSQL($sql, $parametros);

    return $consulta;
}

// Función para obtener ingresos a la plataforma por día y tipo de usuario
function obtenerIngresosPlataforma($config) {
    global $conexion;
    $sql = "SELECT
                DATE(uss_ultimo_ingreso) as fecha,
                CASE
                    WHEN uss_tipo = 1 THEN 'Dev'
                    WHEN uss_tipo = 2 THEN 'Docente'
                    WHEN uss_tipo = 3 THEN 'Acudiente'
                    WHEN uss_tipo = 4 THEN 'Estudiante'
                    WHEN uss_tipo = 5 THEN 'Directivo'
                    ELSE 'Otro'
                END as tipo_usuario,
                COUNT(*) as total_ingresos
            FROM ".BD_GENERAL.".usuarios
            WHERE institucion = ?
                AND year = ?
                AND uss_ultimo_ingreso IS NOT NULL
                AND DATE(uss_ultimo_ingreso) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(uss_ultimo_ingreso), uss_tipo
            ORDER BY fecha DESC, tipo_usuario";

    $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
    $consulta = BindSQL::prepararSQL($sql, $parametros);

    return $consulta;
}

// Obtener datos para estudiantes por curso
$consultaEstudiantesCurso = obtenerEstudiantesPorCurso($config);
$labelsEstudiantes = "";
$dataEstudiantes = "";
$titleEstudiantes = 'true';

if (mysqli_num_rows($consultaEstudiantesCurso) > 0) {
    $nombresEst = array();
    $datosEst = array();
    while ($row = mysqli_fetch_assoc($consultaEstudiantesCurso)) {
        $nombresEst[] = $row['gra_nombre'];
        $datosEst[] = $row['total_estudiantes'];
    }

    $labelsEstudiantes = '"' . implode('", "', $nombresEst) . '"';
    $dataEstudiantes = implode(", ", $datosEst);
    $titleEstudiantes = 'false';
}

// Obtener datos para promedios por curso
$consultaPromediosCurso = obtenerPromediosPorCurso($config);
$labelsPromedios = "";
$dataPromedios = "";
$titlePromedios = 'true';

if (mysqli_num_rows($consultaPromediosCurso) > 0) {
    $nombresProm = array();
    $datosProm = array();
    while ($row = mysqli_fetch_assoc($consultaPromediosCurso)) {
        $nombresProm[] = $row['gra_nombre'];
        $datosProm[] = $row['promedio_general'] ?: 0;
    }

    $labelsPromedios = '"' . implode('", "', $nombresProm) . '"';
    $dataPromedios = implode(", ", $datosProm);
    $titlePromedios = 'false';
}

// Obtener datos para ingresos a la plataforma
$consultaIngresos = obtenerIngresosPlataforma($config);
$fechas = array();
$tiposUsuario = array('Directivo', 'Docente', 'Acudiente', 'Estudiante', 'Dev', 'Otro');
$datosIngresos = array();

if (mysqli_num_rows($consultaIngresos) > 0) {
    while ($row = mysqli_fetch_assoc($consultaIngresos)) {
        $fecha = $row['fecha'];
        $tipo = $row['tipo_usuario'];
        $total = $row['total_ingresos'];

        if (!in_array($fecha, $fechas)) {
            $fechas[] = $fecha;
        }

        if (!isset($datosIngresos[$tipo])) {
            $datosIngresos[$tipo] = array();
        }

        $datosIngresos[$tipo][$fecha] = $total;
    }
}

$labelsIngresos = '"' . implode('", "', array_reverse($fechas)) . '"';
$datasetsIngresos = "";

$colores = array(
    'Directivo' => 'rgba(255, 99, 132, 0.8)',
    'Docente' => 'rgba(54, 162, 235, 0.8)',
    'Acudiente' => 'rgba(255, 205, 86, 0.8)',
    'Estudiante' => 'rgba(75, 192, 192, 0.8)',
    'Dev' => 'rgba(153, 102, 255, 0.8)',
    'Otro' => 'rgba(255, 159, 64, 0.8)'
);

foreach ($tiposUsuario as $tipo) {
    if (isset($datosIngresos[$tipo])) {
        $dataTipo = array();
        foreach (array_reverse($fechas) as $fecha) {
            $dataTipo[] = isset($datosIngresos[$tipo][$fecha]) ? $datosIngresos[$tipo][$fecha] : 0;
        }

        $datasetsIngresos .= "{
            label: '{$tipo}',
            data: [" . implode(", ", $dataTipo) . "],
            borderColor: '{$colores[$tipo]}',
            backgroundColor: '{$colores[$tipo]}',
            borderWidth: 1
        },";
    }
}

$datasetsIngresos = rtrim($datasetsIngresos, ',');
$titleIngresos = empty($fechas) ? 'true' : 'false';
?>

// Gráfico de estudiantes por curso
const ctxEstudiantes = document.getElementById('chartEstudiantesCurso');
new Chart(ctxEstudiantes, {
    type: 'bar',
    data: {
        labels: [<?=$labelsEstudiantes?>],
        datasets: [{
            label: 'Número de Estudiantes',
            data: [<?=$dataEstudiantes?>],
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        responsive: true,
        plugins: {
            title: {
                display: <?=$titleEstudiantes?>,
                text: 'No se encontraron datos de estudiantes'
            }
        }
    }
});

// Gráfico de promedios por curso
const ctxPromedios = document.getElementById('chartPromediosCurso');
new Chart(ctxPromedios, {
    type: 'line',
    data: {
        labels: [<?=$labelsPromedios?>],
        datasets: [{
            label: 'Promedio General',
            data: [<?=$dataPromedios?>],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                max: 5.0,
                ticks: {
                    stepSize: 0.5
                }
            }
        },
        responsive: true,
        plugins: {
            title: {
                display: <?=$titlePromedios?>,
                text: 'No se encontraron datos de calificaciones'
            }
        }
    }
});

// Gráfico de ingresos a la plataforma
const ctxIngresos = document.getElementById('chartIngresosPlataforma');
new Chart(ctxIngresos, {
    type: 'line',
    data: {
        labels: [<?=$labelsIngresos?>],
        datasets: [<?=$datasetsIngresos?>]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        responsive: true,
        plugins: {
            title: {
                display: <?=$titleIngresos?>,
                text: 'No se encontraron datos de ingresos a la plataforma'
            },
            legend: {
                position: 'top',
            }
        }
    }
});
</script>