<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0227';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
    exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Tables/BDT_configuracion.php");
require_once(ROOT_PATH . "/main-app/class/Instituciones.php");

// Cargar Dompdf
require_once(ROOT_PATH . "/librerias/ExcelPhp/vendor/autoload.php");
use Dompdf\Dompdf;
use Dompdf\Options;

$year = $_SESSION["bd"];

if (isset($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}
if (isset($_POST["year"])) {
    $year = base64_decode($_POST["year"]);
}

$periodoFinal = $config['conf_periodos_maximos'];

$grado = 1;
if (!empty($_GET["curso"])) {
    $grado = base64_decode($_GET["curso"]);
}
if (isset($_POST["curso"])) {
    $grado = base64_decode($_POST["curso"]);
}

$grupo = 1;
if (!empty($_GET["grupo"])) {
    $grupo = base64_decode($_GET["grupo"]);
}
if (!empty($_POST["grupo"])) {
    $grupo = base64_decode($_POST["grupo"]);
}

$idEstudiante = '';
if (isset($_POST["id"])) {
    $idEstudiante = base64_decode($_POST["id"]);
}

if (isset($_GET["id"])) {
    $idEstudiante = base64_decode($_GET["id"]);
}
if (!empty($idEstudiante)) {
    $filtro = " AND mat_id='" . $idEstudiante . "'";
    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    $estudiante = $matriculadosPorCurso->fetch_assoc();
    if (!empty($estudiante)) {
        $idEstudiante = $estudiante["mat_id"];
        $grado = $estudiante["mat_grado"];
        $grupo = $estudiante["mat_grupo"];
    }
}

// Consultas iniciales
$listaDatos = [];
$tiposNotas = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
    $tiposNotas[] = $row;
}

if (!empty($grado) && !empty($grupo) && !empty($periodoFinal) && !empty($year)) {
    $periodos = [];
    for ($i = 1; $i <= $periodoFinal; $i++) {
        $periodos[$i] = $i;
    }
    $datos = Boletin::datosBoletin($grado, $grupo, $periodos, $year, $idEstudiante);
    while ($row = $datos->fetch_assoc()) {
        $listaDatos[] = $row;
    }
    include("../compartido/agrupar-datos-boletin-periodos-mejorado.php");
}

if ($grado >= 12 && $grado <= 15) {
    $educacion = "PREESCOLAR";
} elseif ($grado >= 1 && $grado <= 5) {
    $educacion = "PRIMARIA";
} elseif ($grado >= 6 && $grado <= 9) {
    $educacion = "SECUNDARIA";
} elseif ($grado >= 10 && $grado <= 11) {
    $educacion = "MEDIA";
}

// Iniciar buffer de salida para capturar el HTML
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
        }
        .page {
            page-break-after: always;
            page-break-inside: avoid;
            margin: 0;
            padding: 10px 30px;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .page:last-child {
            page-break-after: auto;
        }
        .contenido-estudiante {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .tabla-principal {
            flex: 1;
            margin-bottom: 5px;
        }
        .firma-section {
            margin-top: auto;
            padding-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        .codigo-estudiante {
            max-width: 120px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        .header-table {
            margin-bottom: 3px;
        }
        .info-table {
            margin-bottom: 3px;
        }
        .main-table {
            margin-bottom: 3px;
        }
        .main-table td, .main-table th {
            padding: 2px;
            font-size: 7px;
        }
        .firma-table {
            margin-top: 5px;
            font-size: 8px;
        }
        p {
            margin: 2px 0;
        }
        h3 {
            margin: 2px 0;
            font-size: 10px;
        }
        h4 {
            margin: 2px 0;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <?php foreach ($estudiantes as $estudiante) {
        $totalNotasPeriodo = [];
        ?>
        <div class="page">
            <div class="contenido-estudiante">
            <div class="header-table" style="margin-bottom: 3px;">
                <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" style="font-size: 13px;">
                    <tr>
                        <td rowspan="3" width="20%">
                            <?php
                            $logoPath = ROOT_PATH . '/main-app/files/images/logo/' . $informacion_inst["info_logo"];
                            if (file_exists($logoPath)) {
                                $logoData = file_get_contents($logoPath);
                                $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                                $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
                                echo '<img src="' . htmlspecialchars($logoBase64, ENT_QUOTES) . '" width="100%">';
                            } else {
                                echo '<p>&nbsp;</p>';
                            }
                            ?>
                        </td>
                        <td align="center" rowspan="3" width="25%">
                            <h3 style="font-weight:bold; color: #00adefad; margin: 0">
                                <?= strtoupper($informacion_inst["info_nombre"]) ?>
                            </h3><br>
                            <?= $informacion_inst["info_direccion"] ?><br>
                            Informes: <?= $informacion_inst["info_telefono"] ?>
                        </td>
                        <td class="codigo-estudiante">Documento:<br> <b style="color: #00adefad;"><?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?></b></td>
                        <td>Nombre:<br> <b style="color: #00adefad;"><?= $estudiante["nombre"] ?></b></td>
                    </tr>
                    <tr>
                        <td>Curso:<br> <b style="color: #00adefad;"><?= strtoupper($estudiante["gra_nombre"]) ?></b></td>
                        <td>Sede:<br> <b style="color: #00adefad;"><?= strtoupper($informacion_inst["info_nombre"]) ?></b></td>
                    </tr>
                    <tr>
                        <td>Jornada:<br> <b style="color: #00adefad;"><?= strtoupper($informacion_inst["info_jornada"]) ?></b></td>
                        <td>Documento:<br> <b style="color: #00adefad;">BOLETÍN DEFINITIVO DE NOTAS - EDUCACIÓN BÁSICA <?= strtoupper($educacion) ?></b></td>
                    </tr>
                </table>
            </div>
            <div class="info-table">
            <table width="100%">
                <tr>
                    <td>
                        <div class="divBordeado">&nbsp;</div>
                    </td>
                </tr>
                <tr style="text-align:center; font-size: 13px;">
                    <td style="color: #b2adad;">
                        <?php
                        $consultaEstiloNota = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
                        $numEstiloNota = mysqli_num_rows($consultaEstiloNota);
                        $i = 1;
                        while ($estiloNota = mysqli_fetch_array($consultaEstiloNota, MYSQLI_BOTH)) {
                            $diagonal = " / ";
                            if ($i == $numEstiloNota) {
                                $diagonal = "";
                            }
                            echo $estiloNota['notip_nombre'] . ": " . $estiloNota['notip_desde'] . " - " . $estiloNota['notip_hasta'] . $diagonal;
                            $i++;
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr style="text-align:center; font-size: 20px; font-weight:bold;">
                    <td>AÑO LECTIVO: <?= $year ?></td>
                </tr>
            </table>
            </div>
            <div class="main-table">
            <table width="100%" rules="all" border="1" style="font-size: 8px;">
                <thead>
                    <tr style="font-weight:bold; text-align:center; font-size: 8px;">
                        <td width="25%" rowspan="2">ASIGNATURAS</td>
                        <td width="4%" rowspan="2">I.H</td>
                        <td colspan="<?= $periodoFinal ?>" style="background-color: #00adefad; font-size: 7px;">Periodo Cursados</td>
                        <td width="5%" colspan="2" style="font-size: 7px;">DEFINITIVA</td>
                    </tr>
                    <tr style="font-weight:bold; text-align:center; font-size: 7px;">
                        <?php
                        for ($i = 1; $i <= $periodoFinal; $i++) {
                            ?>
                            <td style="background-color: #00adefad; font-size: 7px;"><?= $i ?></td>
                            <?php
                        }
                        ?>
                        <td style="font-size: 7px;">DEF</td>
                        <td style="font-size: 7px;">Desempeño</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cantidadAreas = 0;
                    $materiasPerdidas = 0;
                    foreach ($estudiante["areas"] as $area) {
                        $cantidadAreas++;
                        $ihArea = 0;
                        $notaAre = [];
                        $desenpenioAre;
                        ?>

                        <?php
                        foreach ($area["cargas"] as $carga) {
                            $promedioMateria = 0;
                            $fallasAcumuladas = 0;
                            $ihArea += $carga['car_ih'];
                            $style = "style='font-weight:bold;background: #EAEAEA;padding-left: 10px;'";
                            $cargaStyle = '';
                            $styleborder = '';
                            ?>
                            <?php if (count($area["cargas"]) > 1) {
                                $nombre = $carga["mat_nombre"];
                                $styleborder = '';
                            } else {
                                $nombre = $area["ar_nombre"];
                                $cargaStyle = '';
                            } ?>
                            <tr style="<?= $styleborder ?>">
                                <td style="<?= $cargaStyle ?> padding-left: 5px; font-size: 7px;"> <?= $nombre ?></td>
                                <td style="<?= $cargaStyle ?> font-size: 7px;" align="center"><?= $carga['car_ih'] ?></td>
                                <?php
                                for ($j = 1; $j <= $periodoFinal; $j++) {
                                    $nota = isset($carga["periodos"][$j]["bol_nota"])
                                        ? $carga["periodos"][$j]["bol_nota"]
                                        : 0;
                                    $nota = Boletin::agregarDecimales($nota);
                                    $desempeno = Boletin::determinarRango($nota, $tiposNotas);
                                    $promedioMateria += $nota;
                                    $porcentajeMateria = !empty($carga['mat_valor']) ? $carga['mat_valor'] : 100;
                                    if (isset($notaAre[$j])) {
                                        $notaAre[$j] += $nota * ($porcentajeMateria / 100);
                                    } else {
                                        $notaAre[$j] = $nota * ($porcentajeMateria / 100);
                                    }

                                    if (isset($totalNotasPeriodo[$j])) {
                                        $totalNotasPeriodo[$j] += $nota * ($porcentajeMateria / 100);
                                    } else {
                                        $totalNotasPeriodo[$j] = $nota * ($porcentajeMateria / 100);
                                    }
                                    $background = 'background: #9ed8ed;';
                                    ?>
                                    <td align="center" style="<?= $background ?>;<?= $cargaStyle ?> font-size: 7px; padding: 1px;">
                                        <?= $nota == 0 ? '' : number_format($nota, $config['conf_decimales_notas']); ?>
                                    </td>
                                <?php }

                                $periodoCalcular = $estudiante['mat_estado_matricula'] == CANCELADO && $config["conf_promedio_libro_final"] == BDT_Configuracion::PERIODOS_CURSADOS ? COUNT($carga["periodos"]) : $config["conf_periodos_maximos"];
                                $notaAcumulada = $promedioMateria / $periodoCalcular;
                                $notaAcumulada = round($notaAcumulada, $config['conf_decimales_notas']);
                                $desempenoAcumulado = Boletin::determinarRango($notaAcumulada, $tiposNotas);
                                if ($notaAcumulada < $config['conf_nota_minima_aprobar']) {
                                    $materiasPerdidas++;
                                }
                                ?>
                                <td align="center" style="font-size: 7px; padding: 1px;"><?= $notaAcumulada <= 0 ? '' : $notaAcumulada ?></td>
                                <td align="center" style="font-size: 7px; padding: 1px;">
                                    <?= $notaAcumulada <= 0 ? '' : $desempenoAcumulado["notip_nombre"] ?>
                                </td>
                            </tr>
                        <?php }
                        if ($ihArea != $carga['car_ih']) { ?>
                            <tr>
                                <td <?= $style ?> style="font-size: 7px; padding: 2px;"><?= $area["ar_nombre"] ?></td>
                                <td align="center" <?= $style ?> style="font-size: 7px; padding: 2px;"><?= $ihArea ?></td>
                                <?php
                                $notaAreAcumulada = 0;
                                $periodoAreaCalcular = $config["conf_periodos_maximos"];
                                for ($j = 1; $j <= $periodoFinal; $j++) {
                                    $notaAreAcumulada += $notaAre[$j]; ?>
                                    <td align="center" <?= $style ?> style="font-size: 7px; padding: 1px;">
                                        <?= $notaAre[$j] <= 0 ? '' : number_format($notaAre[$j], $config['conf_decimales_notas']); ?>
                                    </td>

                                    <?php
                                    if ($notaAre[$j] <= 0) {
                                        $periodoAreaCalcular -= 1;
                                    }
                                }

                                $periodoAreaCalcular = $estudiante['mat_estado_matricula'] == CANCELADO && $config["conf_promedio_libro_final"] == BDT_Configuracion::PERIODOS_CURSADOS ? $periodoAreaCalcular : $config["conf_periodos_maximos"];
                                $notaAreAcumulada = number_format($notaAreAcumulada / $periodoAreaCalcular, $config['conf_decimales_notas']);
                                $desenpenioAreAcumulado = Boletin::determinarRango($notaAreAcumulada, $tiposNotas);

                                ?>

                                <td align="center" <?= $style ?> style="font-size: 7px; padding: 1px;"><?= $notaAreAcumulada <= 0 ? '' : $notaAreAcumulada ?></td>
                                <td align="center" <?= $style ?> style="font-size: 7px; padding: 1px;">
                                    <?= $notaAreAcumulada <= 0 ? '' : $desenpenioAreAcumulado["notip_nombre"] ?>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight:bold;background: #EAEAEA;font-size: 8px;">
                        <td colspan="2" style="padding: 2px;">PROMEDIO GENERAL</td>
                        <?php
                        $promedioFinal = 0;
                        $periodoCalcular = $config["conf_periodos_maximos"];
                        for ($j = 1; $j <= $periodoFinal; $j++) {
                            $acumuladoPj = ($totalNotasPeriodo[$j] / $cantidadAreas);
                            $acumuladoPj = round($acumuladoPj, $config['conf_decimales_notas']);
                            $promedioFinal += $acumuladoPj;

                            if ($acumuladoPj <= 0) {
                                $periodoCalcular -= 1;
                            }
                            ?>
                            <td align="center" style="font-size: 7px; padding: 1px;"><?= $acumuladoPj <= 0 ? '' : $acumuladoPj ?> </td>
                        <?php }

                        $periodoCalcularFinal = $estudiante['mat_estado_matricula'] == CANCELADO && $config["conf_promedio_libro_final"] == BDT_Configuracion::PERIODOS_CURSADOS ? $periodoCalcular : $config["conf_periodos_maximos"];
                        $promedioFinal = round($promedioFinal / $periodoCalcularFinal, $config['conf_decimales_notas']);

                        $desempenoAcumuladoTotal = Boletin::determinarRango($promedioFinal, $tiposNotas);
                        ?>
                        <td align="center" style="font-size: 7px; padding: 1px;"><?= $promedioFinal <= 0 ? '' : $promedioFinal ?></td>
                        <td align="center" style="font-size: 7px; padding: 1px;"><?= $desempenoAcumuladoTotal["notip_nombre"] ?></td>
                    </tr>
                    <tr style="color:#000;">
                        <td style="padding-left: 5px; padding-top: 2px; padding-bottom: 2px;" colspan="<?= $periodoFinal + 4 ?>">
                            <h4 style="font-weight:bold; color: #00adefad; margin: 2px 0; font-size: 8px;"><b>Observación definitiva:</b></h4>
                            <?php
                            if ($periodoFinal == $config["conf_periodos_maximos"]) {

                                if ($materiasPerdidas >= $config["conf_num_materias_perder_agno"]) {
                                    $msj = "EL(LA) ESTUDIANTE NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE.";
                                } elseif ($materiasPerdidas < $config["conf_num_materias_perder_agno"] && $materiasPerdidas > 0) {
                                    $msj = "EL(LA) ESTUDIANTE DEBE NIVELAR LAS MATERIAS PERDIDAS.";
                                } else {
                                    $msj = "EL(LA) ESTUDIANTE FUE PROMOVIDO(A) AL GRADO SIGUIENTE.";
                                }

                                if ($estudiante['mat_estado_matricula'] == CANCELADO && $periodoCalcularFinal < $config["conf_periodos_maximos"]) {
                                    $msj = "EL(LA) ESTUDIANTE FUE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
                                }
                            }
                            echo "<span style='padding-left: 5px; font-size: 7px;'>" . $msj . "</span>";
                            ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
            </div>
            <!--******FIRMAS******-->
            <div class="firma-section">
            <table class="firma-table" style="text-align:center; width:100%; margin-top: 3px;">
                <tr>
                    <td align="center" style="padding-top: 3px;">
                        <?php
                        // Obtener información de la institución para el año consultado
                        try {
                            $informacionInstYear = Instituciones::getGeneralInformationFromInstitution($config['conf_id_institucion'], $year);
                            $idRector = !empty($informacionInstYear["info_rector"]) ? $informacionInstYear["info_rector"] : null;
                        } catch (Exception $e) {
                            $idRector = null;
                        }
                        
                        // Obtener datos del rector del año consultado
                        $rector = null;
                        $nombreRector = '';
                        
                        if (!empty($idRector)) {
                            $rector = Usuarios::obtenerDatosUsuario($idRector);
                            $nombreRector = !empty($rector) ? UsuariosPadre::nombreCompletoDelUsuario($rector) : '';
                        }
                        
                        // Verificar y mostrar la firma
                        if (!empty($rector) && !empty($rector["uss_firma"])) {
                            $rutaArchivo = ROOT_PATH . '/main-app/files/fotos/' . $rector['uss_firma'];
                            // Verificar que el archivo existe físicamente
                            if (file_exists($rutaArchivo)) {
                                // Convertir a base64 para incluir en el PDF
                                $tipo = pathinfo($rutaArchivo, PATHINFO_EXTENSION);
                                $data = file_get_contents($rutaArchivo);
                                if ($data !== false) {
                                    $firmaBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode($data);
                                    echo '<img src="' . htmlspecialchars($firmaBase64, ENT_QUOTES) . '" width="80" style="max-height: 50px; object-fit: contain;"><br>';
                                } else {
                                    echo '<p style="margin: 5px 0;">&nbsp;</p>';
                                }
                        } else {
                            echo '<p style="margin: 5px 0;">&nbsp;</p>';
                        }
                        } else {
                            echo '<p style="margin: 5px 0;">&nbsp;</p>';
                        }
                        ?>
                        _________________________________<br>
                        <p style="margin: 2px 0;">&nbsp;</p>
                        <span style="font-size: 8px;"><?= $nombreRector ?></span><br>
                        <span style="font-size: 7px;">Rector(a)</span>
                    </td>
                </tr>
            </table>
            </div>
            </div>
        </div>
    <?php } ?>
</body>
</html>
<?php
$html = ob_get_clean();

// Configurar Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('chroot', ROOT_PATH);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Generar nombre del archivo
$nombreArchivo = 'LIBRO_FINAL_' . $grado . '_' . $grupo . '_' . $year . '.pdf';

// Enviar PDF al navegador
$dompdf->stream($nombreArchivo, array('Attachment' => 1));
exit();
?>

