<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0227';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
    exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/compartido/overlay.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Tables/BDT_configuracion.php");

$year = $_SESSION["bd"];

if (isset($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}
if (isset($_POST["year"])) {
    $year = $_POST["year"];
}

$periodoFinal = $config['conf_periodos_maximos'];

$grado = 1;
if (!empty($_GET["curso"])) {
    $grado = base64_decode($_GET["curso"]);
}
if (isset($_POST["curso"])) {
    $grado = $_POST["curso"];
}

$grupo = 1;
if (!empty($_GET["grupo"])) {
    $grupo = base64_decode($_GET["grupo"]);
}
if (!empty($_POST["grupo"])) {
    $grupo = $_POST["grupo"];
}

$idEstudiante = '';
if (isset($_POST["id"])) {
    $idEstudiante = $_POST["id"];
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
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <title>Libro Final</title>
    <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
    <link href="../css/ButomDowloadPdf.css" rel="stylesheet" type="text/css" />
	<script src="../js/ButomDowloadPdf.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    	<!-- notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- favicon -->
    <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>" />
    <style>
       

        #saltoPagina {
            PAGE-BREAK-AFTER: always;
        }
        #guardarPDF {
            cursor: pointer;
        }

       
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
</head>
<?php
// Cosnultas iniciales
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
$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);







if ($grado >= 12 && $grado <= 15) {
    $educacion = "PREESCOLAR";
} elseif ($grado >= 1 && $grado <= 5) {
    $educacion = "PRIMARIA";
} elseif ($grado >= 6 && $grado <= 9) {
    $educacion = "SECUNDARIA";
} elseif ($grado >= 10 && $grado <= 11) {
    $educacion = "MEDIA";
}

?>

<body style="font-family:Arial; font-size:9px;">

    <div id="contenido" >
    <p>&nbsp;</p>
        <?php foreach ($estudiantes as $estudiante) {
            $totalNotasPeriodo = [];
            ?>
            <div class="page" style="margin-left: 50px;margin-right: 50px;">
                 <!-- <h1>Página <?= $estudiante["nro"]?></h1> -->
                <div style="margin: 15px 0;">
                    <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" style="font-size: 13px;">
                        <tr>
                            <td rowspan="3" width="20%"><img
                                    src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" width="100%"></td>
                            <td align="center" rowspan="3" width="25%">
                                <h3 style="font-weight:bold; color: #00adefad; margin: 0">
                                    <?= strtoupper($informacion_inst["info_nombre"]) ?>
                                </h3><br>
                                <?= $informacion_inst["info_direccion"] ?><br>
                                Informes: <?= $informacion_inst["info_telefono"] ?>
                            </td>
                            <td>Código:<br> <b style="color: #00adefad;"><?= $estudiante["mat_id"]; ?></b></td>
                            <td>Nombre:<br> <b style="color: #00adefad;"><?= $estudiante["nombre"] ?></b></td>
                        </tr>
                        <tr>
                            <td>Curso:<br> <b style="color: #00adefad;"><?= strtoupper($estudiante["gra_nombre"]) ?></b>
                            </td>
                            <td>Sede:<br> <b
                                    style="color: #00adefad;"><?= strtoupper($informacion_inst["info_nombre"]) ?></b></td>
                        </tr>
                        <tr>
                            <td>Jornada:<br> <b
                                    style="color: #00adefad;"><?= strtoupper($informacion_inst["info_jornada"]) ?></b></td>
                            <td>Documento:<br> <b style="color: #00adefad;">BOLETÍN DEFINITIVO DE NOTAS - EDUCACIÓN BÁSICA
                                    <?= strtoupper($educacion) ?></b></td>
                        </tr>
                    </table>
                    <p>&nbsp;</p>
                </div>
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
                <table width="100%" rules="all" border="1" style="font-size: 15px;">
                    <thead>
                        <tr style="font-weight:bold; text-align:center;">
                            <td width="20%" rowspan="2">ASIGNATURAS</td>
                            <td width="3%" rowspan="2">I.H</td>
                            <td width="3%" colspan="4" style="background-color: #00adefad;"><a href="#"
                                    style="color:#000; text-decoration:none;">Periodo Cursados</a></td>
                            <td width="3%" colspan="2"><a href="#" style="color:#000; text-decoration:none;">DEFINITIVA</a>
                            </td>
                        </tr>
                        <tr style="font-weight:bold; text-align:center;">
                            <?php
                            for ($i = 1; $i <= $periodoFinal; $i++) {
                                ?>
                                <td width="3%" style="background-color: #00adefad;"><?= $i ?></td>
                                <?php
                            }
                            ?>
                            <td width="3%">DEF</td>
                            <td width="3%">Desempeño</td>
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
                                    <td style="<?= $cargaStyle ?>  padding-left: 10px;"> <?= $nombre ?></td>
                                    <td style="<?= $cargaStyle ?>" align="center"><?= $carga['car_ih'] ?></td>
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
                                        <td align="center" align="center"
                                            style=" <?= $background ?>;<?= $cargaStyle ?> font-size:12px;">
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
                                    <td align="center" style=" font-size:12px;"><?= $notaAcumulada <= 0 ? '' : $notaAcumulada ?>
                                    </td>
                                    <td align="center" style=" font-size:12px;">
                                        <?= $notaAcumulada <= 0 ? '' : $desempenoAcumulado["notip_nombre"] ?>
                                    </td>
                                </tr>
                            <?php }
                            if ($ihArea != $carga['car_ih']) { ?>
                                <tr>
                                    <td <?= $style ?>><?= $area["ar_nombre"] ?></td>
                                    <td align="center" <?= $style ?>><?= $ihArea ?></td>
                                    <?php
                                    $notaAreAcumulada = 0;
                                    $periodoAreaCalcular = $config["conf_periodos_maximos"];
                                    for ($j = 1; $j <= $periodoFinal; $j++) {
                                        $notaAreAcumulada += $notaAre[$j]; ?>
                                        <td align="center" <?= $style ?>>
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

                                    <td align="center" <?= $style ?>><?= $notaAreAcumulada <= 0 ? '' : $notaAreAcumulada ?></td>
                                    <td align="center" <?= $style ?>>
                                        <?= $notaAreAcumulada <= 0 ? '' : $desenpenioAreAcumulado["notip_nombre"] ?>
                                    </td>
                                </tr>
                            <?php }
                        } ?>
                    </tbody>
                    <tfoot style="font-size: 13px;">
                        <tr style="font-weight:bold;background: #EAEAEA;font-size: 15px">
                            <td colspan="2">PROMEDIO GENERAL</td>
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
                                <td align="center"><?= $acumuladoPj <= 0 ? '' : $acumuladoPj ?> </td>
                            <?php }

                            $periodoCalcularFinal = $estudiante['mat_estado_matricula'] == CANCELADO && $config["conf_promedio_libro_final"] == BDT_Configuracion::PERIODOS_CURSADOS ? $periodoCalcular : $config["conf_periodos_maximos"];
                            $promedioFinal = round($promedioFinal / $periodoCalcularFinal, $config['conf_decimales_notas']);

                            $desempenoAcumuladoTotal = Boletin::determinarRango($promedioFinal, $tiposNotas);
                            ?>
                            <td align="center"><?= $promedioFinal <= 0 ? '' : $promedioFinal ?></td>
                            <td align="center"><?= $desempenoAcumuladoTotal["notip_nombre"] ?></td>
                        </tr>
                        <tr style="color:#000;">
                            <td style="padding-left: 10px;" colspan="8">
                                <p>&nbsp;</p>
                                <h4 style="font-weight:bold; color: #00adefad;"><b>Observación definitiva:</b></h4>
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
                                echo "<span style='padding-left: 10px;'>" . $msj . "</span>";
                                ?>
                                <p>&nbsp;</p>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <p>&nbsp;</p>
                <!--******FIRMAS******-->
                <table style="text-align:center; font-size:10px;">
                    <tr>
                        <td align="left">
                            <?php
                            $rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
                            $nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
                            if (!empty($rector["uss_firma"]) && file_exists(ROOT_PATH . '/main-app/files/fotos/' . $rector['uss_firma'])) {
                                echo '<div align="center"> <img src="../files/fotos/' . $rector["uss_firma"] . '" width="100"> </div> ';
                            } else {
                                echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>';
                            }
                            ?>
                            </p>_________________________________<br>                           
                            <?= $nombreRector ?><br>
                            Rector(a)
                        </td>
                    </tr>
                </table>
                <p>&nbsp;</p>
            </div>
             <div id="saltoPagina"></div>
        <?php } ?>
    </div>
    <input type="button" class="btn  btn-flotante btn-with-icon" id="guardarPDF" onclick="generatePDF('contenido','LIBRO_FINAL_F2',20)"
        value="Descargar PDF">
    </input>
</body>


</html>