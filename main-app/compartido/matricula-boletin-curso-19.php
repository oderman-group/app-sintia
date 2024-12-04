<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
    exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Disciplina.php");

$year = $_SESSION["bd"];
if (isset($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}

if (empty($_GET["periodo"])) {

    $periodoSeleccionado = 1;
} else {

    $periodoSeleccionado = base64_decode($_GET["periodo"]);
}

if (!empty($_GET["curso"])) {
    $grado = base64_decode($_GET["curso"]);
}

$grupo = 1;
if (!empty($_GET["grupo"])) {
    $grupo = base64_decode($_GET["grupo"]);
}

if (!empty($_GET["periodo"])) {
    $periodo = base64_decode($_GET["periodo"]);
}

if (!empty($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}
$idEstudiante = '';
if (!empty($_GET["id"])) {

    $filtro               = " AND mat_id='" . base64_decode($_GET["id"]) . "'";
    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    Utilidades::validarInfoBoletin($matriculadosPorCurso);
    $estudiante           = $matriculadosPorCurso->fetch_assoc();
    if (!empty($estudiante)) {
        $idEstudiante = $estudiante["mat_id"];
        $grado        = $estudiante["mat_grado"];
        $grupo        = $estudiante["mat_grupo"];
    } else {
        echo "Excepción catpurada: Estudiante no encontrado ";
        exit();
    }
}
$ultimoPeriodoAreas = $config["conf_periodos_maximos"];
$tamañoLogo         = $_SESSION['idInstitucion'] == ICOLVEN ? 100 : 50;

switch ($periodoSeleccionado) {
    case 1:
        $periodoActuales = "Primero";
        $celdas = 2;
        break;
    case 2:
        $periodoActuales = "Segundo";
        $celdas = 4;
        break;
    case 3:
        $periodoActuales = "Tercero";
        $celdas = 6;
        break;
    case 4:
        $periodoActuales = "Final";
        $celdas = 8;
        break;
}

$periodos = [];
for ($i = 1; $i <= $periodoSeleccionado; $i++) {
    $periodos[$i] = $i;
}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <title>Boletín</title>
    <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
    <!-- favicon -->
    <link rel="shortcut icon" href="../sintia-icono.png" />
    <style>
        #saltoPagina {
            PAGE-BREAK-AFTER: always;
        }
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
</head>
<?php
// Cosnultas iniciales
$listaDatos         = [];
$tiposNotas         = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
    $tiposNotas[] = $row;
}

if (!empty($grado) && !empty($grupo) && !empty($periodo) && !empty($year)) {
    $datos = Boletin::datosBoletinPeriodos($grado, $grupo, $periodos, $year, $idEstudiante);
    Utilidades::validarInfoBoletin($datos);
    while ($row = $datos->fetch_assoc()) {
        $listaDatos[] = $row;
    }
}
$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);

$consultaPuestos = Boletin::obtenerPuestoYpromedioEstudiante($periodo, $grado, $grupo, $year);
$puestosCurso = [];
while ($puesto = mysqli_fetch_array($consultaPuestos, MYSQLI_BOTH)) {
    $puestosCurso[$puesto['bol_estudiante']] = $puesto['puesto'];
}

$puestosInstitucion = [];
$consultaPuestosInstitucion = Boletin::obtenerPuestoEstudianteEnInstitucion($periodo, $year);
while ($puesto = mysqli_fetch_array($consultaPuestosInstitucion, MYSQLI_BOTH)) {
    $puestosInstitucion[$puesto['bol_estudiante']] = $puesto['puesto'];
}

$listaCargas = [];
$conCargasDos = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $grado, $grupo, $year);
while ($row = $conCargasDos->fetch_assoc()) {

    $indicadores = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $row['car_id'], $periodoSeleccionado, $year);
    $listaIndicadores = [];
    while ($row2 = $indicadores->fetch_assoc()) {
        $listaIndicadores[] = $row2;
    }
    $row['indicadores'] = $listaIndicadores;
    $listaCargas[]      = $row;
}

$periodosCursados = $periodoSeleccionado - 1;
$colspan = 7 + $periodosCursados;

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

<?php include("../compartido/agrupar-datos-boletin-periodos.php") ?>

<body style="font-family:Arial;">
    <?php foreach ($estudiantes  as  $estudiante) {
        $totalNotasPeriodo = [];
    ?>
        <div style="margin: 15px 0;">
            <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" style="font-size: 13px;">
                <tr>
                    <td rowspan="2" width="20%"><img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" width="100%"></td>
                    <td align="center" rowspan="2" width="25%">
                        <h3 style="font-weight:bold; color: #00adefad; margin: 0"><?= strtoupper($informacion_inst["info_nombre"]) ?></h3><br>
                        <?= $informacion_inst["info_direccion"] ?><br>
                        Informes: <?= $informacion_inst["info_telefono"] ?><br><br>
                        AÑO LECTIVO: <?= $year ?>
                    </td>
                    <td>Documento:<br> <b style="color: #00adefad;"><?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?></b></td>
                    <td>Nombre:<br> <b style="color: #00adefad;"><?= $estudiante["nombre"] ?></b></td>
                    <td>Grado:<br> <b style="color: #00adefad;"><?= strtoupper($estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]) ?></b></td>
                </tr>
                <tr>
                    <td>E. Básica:<br> <b style="color: #00adefad;"><?= $educacion ?></b></td>
                    <td>Sede:<br> <b style="color: #00adefad;"><?= strtoupper($informacion_inst["info_nombre"]) ?></b></td>
                    <td>Jornada:<br> <b style="color: #00adefad;"><?= strtoupper($informacion_inst["info_jornada"]) ?></b></td>
                </tr>
            </table>
            <p>&nbsp;</p>
        </div>
        <table width="100%" cellspacing="5" cellpadding="5" rules="all" style="font-size: 13px;">
            <tr style="text-align:center; font-size: 13px;">
                <td style="color: #b2adad;">
                    <?php
                    $numEstiloNota = count($tiposNotas);
                    $i = 1;
                    foreach ($tiposNotas as $desempeno) {
                        $diagonal = " / ";
                        if ($i == $numEstiloNota) {
                            $diagonal = "";
                        }
                        echo $desempeno['notip_nombre'] . ": " . $desempeno['notip_desde'] . " - " . $desempeno['notip_hasta'] . $diagonal;
                        $i++;
                    }
                    ?>
                </td>
            </tr>
        </table>
        <table width="100%" rules="all" border="1" style="font-size: 15px;">
            <thead style="background-color: #00adefad;">
                <tr style="font-weight:bold; text-align:center;">
                    <td width="20%" rowspan="2">ASIGNATURAS</td>
                    <td width="3%" rowspan="2">I.H</td>
                    <?php
                    if ($periodoSeleccionado != 1) {
                    ?>
                        <td width="3%" colspan="<?= $periodosCursados ?>"><a href="#" style="color:#000; text-decoration:none;">Periodo Cursados</a></td>
                    <?php
                    }
                    ?>
                    <td width="3%" colspan="2">Periodo Actual (<?= strtoupper($periodoActuales) ?>)</td>
                    <td width="3%" colspan="3">TOTAL ACUMULADO</td>
                </tr>
                <tr style="font-weight:bold; text-align:center;">
                    <?php
                    for ($i = 1; $i <= $periodoSeleccionado; $i++) {
                        if ($i != $periodoSeleccionado) {
                    ?>
                            <td width="3%"><?= $i ?></td>
                        <?php
                        } else {
                        ?>
                            <td width="3%">Nota</td>
                            <td width="3%">Desempeño</td>
                    <?php
                        }
                    }
                    ?>
                    <td width="3%">Fallas</td>
                    <td width="3%">Nota</td>
                    <td width="3%">Desempeño</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $cantidadAreas = 0;
                foreach ($areas[$estudiante["mat_id"]]  as  $area) {
                    $cantidadAreas++;
                    $ihArea       = 0;
                    $notaAre      = [];
                    $fallasArea   = 0;
                    $desenpenioAre;

                ?>

                    <?php
                    foreach ($cargas[$estudiante["mat_id"]][$area["ar_id"]] as $carga) {
                        $promedioMateria  = 0;
                        $fallasAcumuladas = 0;
                        $ihArea           += $carga['car_ih'];
                        $style = "style='font-weight:bold;background: #EAEAEA;'";
                        $cargaStyle = '';
                        $styleborder= '';
                    ?>
                    <?php if (count($cargas[$estudiante["mat_id"]][$area["ar_id"]]) > 1) { 
                          $nombre= $carga["mat_nombre"];
                          $styleborder= 'border-bottom: hidden;';
                          } else {
                          $nombre= $area["ar_nombre"]; 
                          $cargaStyle = 'font-weight:bold;';
                          
                          } ?>
                        <tr style="<?= $styleborder ?>">
                            <td style="<?= $cargaStyle ?>"> <?= $nombre ?></td>                           
                            <td style="<?= $cargaStyle ?>" align="center"><?= $carga['car_ih'] ?></td>
                            <?php
                            for ($j = 1; $j <= $periodoSeleccionado; $j++) {
                                $nota = isset($notasPeriodos[$estudiante["mat_id"]][$area["ar_id"]][$carga["car_id"]][$j]["bol_nota"])
                                    ? $notasPeriodos[$estudiante["mat_id"]][$area["ar_id"]][$carga["car_id"]][$j]["bol_nota"]
                                    : 0;
                                $fallas = isset($notasPeriodos[$estudiante["mat_id"]][$area["ar_id"]][$carga["car_id"]][$j]["aus_ausencias"])
                                    ? $notasPeriodos[$estudiante["mat_id"]][$area["ar_id"]][$carga["car_id"]][$j]["aus_ausencias"]
                                    : 0;
                                $nota                  = Boletin::agregarDecimales($nota);
                                $desempeno             = Boletin::determinarRango($nota, $tiposNotas);
                                $promedioMateria       += $nota;
                                $fallasAcumuladas      += $fallas;
                                $fallasArea            += $fallas;
                                $porcentajeMateria = !empty($carga['mat_valor']) ? $carga['mat_valor'] : 100;
                                if (isset($notaAre[$j])) {
                                    $notaAre[$j]           += $nota * ($porcentajeMateria / 100);
                                } else {
                                    $notaAre[$j]           =  $nota * ($porcentajeMateria / 100);
                                }

                                if (isset($totalNotasPeriodo[$j])) {
                                    $totalNotasPeriodo[$j] += $nota * ($porcentajeMateria / 100);
                                } else {
                                    $totalNotasPeriodo[$j] =  $nota * ($porcentajeMateria / 100);
                                }
                                $background = $j != $periodoSeleccionado ? 'background: #9ed8ed;' : '';
                            ?>
                                <td align="center" align="center" style=" <?= $background ?>;<?= $cargaStyle ?> font-size:12px;"><?= $nota   == 0 ? '' : number_format($nota, $config['conf_decimales_notas']); ?></td>
                            <?php }
                            ?>
                            <td align="center"><?= $nota   == 0 ? '' : $desempeno['notip_nombre'] ?></td>
                            <?php
                            $notaAcumulada = $promedioMateria / $config['conf_periodos_maximos'];
                            $notaAcumulada = round($notaAcumulada, 2);
                            $desempenoAcumulado = Boletin::determinarRango($notaAcumulada, $tiposNotas); ?>
                            <td align="center"> <?= $fallasAcumuladas   == 0 ? '' : $fallasAcumuladas; ?></td>
                            <td align="center" style=" font-size:12px;"><?= $notaAcumulada   == 0 ? '' : number_format($notaAcumulada, $config['conf_decimales_notas']); ?></td>
                            <td align="center" style=" font-size:12px;"><?= $notaAcumulada   == 0 ? '' : $desempenoAcumulado["notip_nombre"] ?></td>
                        </tr>
                    <?php }
                    if ($ihArea != $carga['car_ih']) { ?>
                        <tr>
                            <td <?= $style ?>><?= $area["ar_nombre"] ?></td>
                            <td align="center" <?= $style ?>><?= $ihArea ?></td>
                            <?php
                            $notaAreAcumulada = 0;
                            for ($j = 1; $j <= $periodoSeleccionado; $j++) {
                                $notaAreAcumulada += $notaAre[$j];
                                $desenpenioAre = Boletin::determinarRango($notaAre[$j], $tiposNotas); ?>
                                <td align="center" <?= $style ?>><?= number_format($notaAre[$j], $config['conf_decimales_notas']); ?></td>

                            <?php }
                            $notaAreAcumulada       = number_format($notaAreAcumulada / $config['conf_periodos_maximos'], $config['conf_decimales_notas']);
                            $desenpenioAreAcumulado = Boletin::determinarRango($notaAreAcumulada, $tiposNotas);
                            ?>
                            <td align="center" <?= $style ?>><?= $desenpenioAre['notip_nombre'] ?></td>
                            <td align="center" <?= $style ?>><?= $fallasArea ?></td>
                            <td align="center" <?= $style ?>><?= $notaAreAcumulada ?></td>
                            <td align="center" <?= $style ?>><?= $desempenoAcumulado["notip_nombre"] ?></td>
                        </tr>
                <?php }
                } ?>
            </tbody>
            <tfoot style="font-weight:bold; font-size: 15px;">
                <tr style="font-weight:bold;background: #EAEAEA">
                    <td colspan="2">PROMEDIO GENERAL</td>
                    <?php
                    $promedioFinal = 0;
                    for ($j = 1; $j <= $periodoSeleccionado; $j++) {
                        $acumuladoPj = ($totalNotasPeriodo[$j] / $cantidadAreas);
                        $acumuladoPj = round($acumuladoPj, 2);
                        $promedioFinal += $acumuladoPj;
                        $desempenoAcumuladoTotal = Boletin::determinarRango($acumuladoPj, $tiposNotas);

                    ?>
                        <td align="center"><?= number_format($acumuladoPj, $config['conf_decimales_notas']) ?> </td>
                    <?php } ?>
                    <td align="center"><?= $desempenoAcumuladoTotal["notip_nombre"] ?></td>
                    <td align="center"></td>
                    <td align="center"></td>
                    <td align="center"></td>
                </tr>
            </tfoot>
        </table>
        <p>&nbsp;</p>
        <table style="font-size: 15px;" width="80%" cellspacing="5" cellpadding="5" rules="all" border="1" align="right">
            <tr style="background-color: #EAEAEA;">

                <td align="center" width="40%">Puesto en el curso <b><?= isset($puestosCurso[$estudiante["mat_id"]]) ? $puestosCurso[$estudiante["mat_id"]] : 'N/A' ?></b> entre <b><?= count($puestosCurso) ?></b> Estudiantes.</td>
                <td align="center" width="40%">Puesto en el colegio <b><?= isset($puestosInstitucion[$estudiante["mat_id"]]) ? $puestosInstitucion[$estudiante["mat_id"]] : 'N/A' ?></b> entre <b><?= count($puestosInstitucion) ?></b> Estudiantes.</td>
            </tr>
        </table>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <?php if (!empty($observacionesConvivencia[$estudiante["mat_id"]])) {
            usort($observacionesConvivencia[$estudiante["mat_id"]], function ($a, $b) {
                return $a['periodo'] - $b['periodo']; // Orden ascendente por 'periodo'
            });
        ?>
            <table width="100%" style="margin-top: 15px;" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
                <thead>
                    <tr style="font-weight:bold; text-align:left; background-color: #00adefad;">
                        <td><b>Observaciones:</b></td>
                    </tr>
                </thead>

                <?php
                if ($config['conf_observaciones_multiples_comportamiento'] == '1') { ?>
                    <tr style="color:#000;">
                        <td style="padding-left: 20px;">
                            <?php
                            $cndisiplina = mysqli_query($conexion, "SELECT * FROM " . BD_DISCIPLINA . ".disiplina_nota WHERE dn_cod_estudiante='" . $estudiante['mat_id'] . "' AND dn_periodo='" . $periodoSeleccionado . "' AND institucion={$config['conf_id_institucion']} AND year=" . $config['conf_agno'] . "");
                            while ($rndisiplina = mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)) {

                                if (!empty($rndisiplina['dn_observacion'])) {
                                    if ($config['conf_observaciones_multiples_comportamiento'] == '1') {
                                        $explode = explode(",", $rndisiplina['dn_observacion']);
                                        $numDatos = count($explode);
                                        for ($i = 0; $i < $numDatos; $i++) {
                                            $observaciones = Disciplina::traerDatosObservacion($config, $explode[$i], "obser_descripcion");
                                            echo "- " . $observaciones['obser_descripcion'] . "<br> ";
                                        }
                                    } else {
                                        echo "- " . $rndisiplina["dn_observacion"] . "<br>";
                                    }
                                }
                            }
                            if ($periodoSeleccionado == $config["conf_periodos_maximos"] && $ultimoPeriodoAreas < $config["conf_periodos_maximos"]) {
                                echo "ESTUDIANTE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
                            }
                            ?>
                            <p>&nbsp;</p>
                        </td>
                    </tr>
                <?php } else { ?>
                    <tr style=" background:#EAEAEA; color:#000; font-size:12px; text-align:center">
                        <td width="8%">Periodo</td>
                        <td>Observaciones</td>
                    </tr> <?php
                            foreach ($observacionesConvivencia[$estudiante["mat_id"]] as $observacion) {
                                $observacionString = "";
                                if ($observacion["estudiante"] == $estudiante["mat_id"]) { ?>
                            <tr align="center" style="font-size:12px; height:16px;">
                                <td style="font-weight:bold;"><?= $observacion["periodo"] ?></td>
                                <td align="left"><?= $observacionString ?></td>
                            </tr>

                <?php  }
                            }
                        } ?>

            </table>
        <?php } ?>
        <p>&nbsp;</p>
        <div id="saltoPagina"></div>
        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1" align="center" style="font-size:10px;">
            <thead>
                <tr style="font-weight:bold; text-align:center; background-color: #00adefad;">
                    <td width="30%">Asignatura</td>
                    <td width="70%">Indicadores de desempeño</td>
                </tr>
            </thead>

            <?php
            foreach ($listaCargas as $carga) {
            ?>
                <tbody>
                    <tr style="color:#000;">
                        <td><?= $carga['mat_nombre']; ?><br><span style="color:#C1C1C1;"><?= UsuariosPadre::nombreCompletoDelUsuario($carga); ?></span></td>
                        <td>
                            <?php
                            //INDICADORES
                            foreach ($carga["indicadores"] as $indicador) {
                            ?>
                                <?= $indicador['ind_nombre']; ?><br>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            <?php
            }
            ?>
        </table>
        <p>&nbsp;</p>
        <?php include("../compartido/firmas-informes.php") ?>
        <?php include("../compartido/footer-informes.php") ?>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <div id="saltoPagina"></div>
    <?php  }  ?>
</body>

<script type="application/javascript">
    print();
</script>

</html>