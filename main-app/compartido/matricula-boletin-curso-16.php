<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
    exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Ausencias.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");

$year = $_SESSION["bd"];
if (isset($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}



if (!empty($_GET["curso"])) {
    $grado = base64_decode($_GET["curso"]);
}

$grupo = 1;
if (!empty($_GET["grupo"])) {
    $grupo = base64_decode($_GET["grupo"]);
}

$periodos = [];
if (empty($_GET["periodo"])) {
    $periodoSeleccionado = 1;
    $periodo = "1";
} else {
    $periodo = base64_decode($_GET["periodo"]);
    $periodoSeleccionado = base64_decode($_GET["periodo"]);
}
for ($i = 1; $i <= $periodoSeleccionado; $i++) {
    $periodos[$i] = $i;
}

if (!empty($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}
$idEstudiante = '';
if (!empty($_GET["id"])) {

    $filtro = " AND mat_id='" . base64_decode($_GET["id"]) . "'";
    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    Utilidades::validarInfoBoletin($matriculadosPorCurso);
    $estudiante = $matriculadosPorCurso->fetch_assoc();
    if (!empty($estudiante)) {
        $idEstudiante = $estudiante["mat_id"];
        $grado        = $estudiante["mat_grado"];
        $grupo        = $estudiante["mat_grupo"];
    } 
}


$tamañoLogo = $_SESSION['idInstitucion'] == ICOLVEN ? 100 : 50;


if ($periodoSeleccionado == 1) $periodoActuales = "Primero";

if ($periodoSeleccionado == 2) $periodoActuales = "Segundo";

if ($periodoSeleccionado == 3) $periodoActuales = "Tercero";

if ($periodoSeleccionado == 4) $periodoActuales = "Final";



?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
    <style>
        #saltoPagina {
            PAGE-BREAK-AFTER: always;
        }
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
</head>

<body style="font-family:Arial; font-size:9px;">

    <?php
    $listaDatos = [];
    $tiposNotas = [];
    
    $cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
    while ($row = $cosnultaTiposNotas->fetch_assoc()) {
        $tiposNotas[] = $row;
    }
    if (!empty($grado) && !empty($grupo) && !empty($periodos) && !empty($year)) {
        $datos = Boletin::datosBoletinPeriodos($grado,  $grupo, $periodos, $year, $idEstudiante);
        Utilidades::validarInfoBoletin($datos);
        while ($row = $datos->fetch_assoc()) {
            $listaDatos[] = $row;
        }
    }

    $indicadoresCarga = [];
    $conCargas = CargaAcademica::traerIndicadoresCargasPorCursoGrupo($config, $grado, $grupo, $periodoSeleccionado, $year);
    while ($row = mysqli_fetch_array($conCargas, MYSQLI_BOTH)) {
        $indicadoresCarga[] = $row;
    }

    $puestosCurso = [];
    $consultaPuestos = Boletin::obtenerPuestoYpromedioEstudiante($periodo, $grado, $grupo, $year);
    while ($puesto = mysqli_fetch_array($consultaPuestos, MYSQLI_BOTH)) {
        $puestosCurso[$puesto['bol_estudiante']] = $puesto['puesto'];
    }

    ?>


    <?php include("../compartido/agrupar-datos-boletin-periodos.php") ?>

    <body style="font-family:Arial; font-size:9px;"></body>
    <?php
    foreach ($estudiantes  as $estudiante) {
        $totalNotasPeriodo = [];
        $fallasPeriodo     = [];
        $totalIh           = 0;
        $contarCargas      = 0;
        $materiasPerdidas  = 0;
    ?>
        <div>
            <div style="float:left; width:50%"><img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" width="80"></div>
            <div style="float:right; width:50%">
                <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all">
                    <tr>
                        <td>C&oacute;digo:<br> <?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?></td>
                        <td>Nombre:<br> <?= $estudiante["nombre"] ?></td>
                        <td>Grado:<br> <?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></td>
                        <td>Puesto Curso:<br> <?= isset($puestosCurso[$estudiante["mat_id"]]) ? $puestosCurso[$estudiante["mat_id"]] : 'N/A'; ?><?= " de " . count($puestosCurso); ?></td>
                    </tr>

                    <tr>
                        <td>Jornada:<br> Mañana</td>
                        <td>Sede:<br> <?= $informacion_inst["info_nombre"] ?></td>
                        <td>Periodo:<br> <b><?= $periodoSeleccionado . " (" . $year . ")"; ?></b></td>
                        <td>Fecha Impresión:<br> <?= date("d/m/Y H:i:s"); ?></td>
                    </tr>
                </table>
                <p>&nbsp;</p>
            </div>
        </div>
        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
            <thead>
                <tr style="font-weight:bold; text-align:center;">
                    <td width="20%" rowspan="2">ASIGNATURAS</td>
                    <td width="2%" rowspan="2">I.H.</td>

                    <?php for ($j = 1; $j <= $periodoSeleccionado; $j++) { ?>
                        <td width="3%" colspan="3"> Periodo <?= $j ?></td>
                    <?php } ?>
                    <td width="3%" colspan="3">Final</td>
                </tr>
                <tr style="font-weight:bold; text-align:center;">
                    <?php for ($j = 1; $j <= $periodoSeleccionado; $j++) { ?>
                        <td width="3%">Fallas</td>
                        <td width="3%">Nota</td>
                        <td width="3%">Nivel</td>
                    <?php } ?>
                    <td width="3%">Nota</td>
                    <td width="3%">Nivel</td>
                    <td width="3%">Hab</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($areas[$estudiante["mat_id"]]  as  $area) {  ?>
                    <?php foreach ($cargas[$estudiante["mat_id"]][$area["ar_id"]]  as  $carga) {
                        $fondoFila = ($carga["nro"] % 2 == 1) ? '#EAEAEA' : '#FFF';
                        $totalIh += $carga['car_ih'];
                        $contarCargas++;
                    ?>
                        <tr style="background:<?= $fondoFila; ?>">
                            <td><?= $carga['mat_nombre']; ?></td>
                            <td align="center"><?= $carga['car_ih']; ?></td>
                            <?php
                            $promedioMateria = 0;
                            for ($j = 1; $j <= $periodoSeleccionado; $j++) {
                                $nota = isset($notasPeriodos[$estudiante["mat_id"]][$area["ar_id"]][$carga["car_id"]][$j]["bol_nota"])
                                    ? $notasPeriodos[$estudiante["mat_id"]][$area["ar_id"]][$carga["car_id"]][$j]["bol_nota"]
                                    : 0;
                                $fallas = isset($notasPeriodos[$estudiante["mat_id"]][$area["ar_id"]][$carga["car_id"]][$j]["aus_ausencias"])
                                    ? $notasPeriodos[$estudiante["mat_id"]][$area["ar_id"]][$carga["car_id"]][$j]["aus_ausencias"]
                                    : 0;
                                $desempeno = Boletin::determinarRango($nota, $tiposNotas);
                                $promedioMateria += $nota;
                                if (isset($totalNotasPeriodo[$j])) {
                                    $totalNotasPeriodo[$j] += $nota;
                                } else {
                                    $totalNotasPeriodo[$j] = $nota;
                                }
                                if (isset($fallasPeriodo[$j])) {
                                    $fallasPeriodo[$j] += $fallas;
                                } else {
                                    $fallasPeriodo[$j] = $fallas;
                                }
                            ?>
                                <td align="center"><?= $fallas == 0 ? '' : intval($fallas); ?></td>
                                <td align="center"><?= $nota   == 0 ? '' : number_format($nota,$config['conf_decimales_notas']); ?></td>
                                <td align="center"><?= $desempeno["notip_nombre"] ?></td>
                            <?php }
                            $promedioFinal = round(($promedioMateria / $periodoSeleccionado), 2);	// SI PERDIÓ LA MATERIA A FIN DE AÑO
                            if($promedioMateria<$config["conf_nota_minima_aprobar"]){
                                $notaNivelacion=isset($nivelacion[$estudiante["mat_id"]][$carga["car_id"]]['niv_definitiva'])?
                                $nivelacion[$estudiante["mat_id"]][$carga["car_id"]]['niv_definitiva']:0;                                ;
                                if($notaNivelacion>=$config["conf_nota_minima_aprobar"]){
                                    $promedioMateriaFinal = $notaNivelacion;
                                }else{
                                    $materiasPerdidas++;
                                }	
                            }
                            $desempenoFinal = Boletin::determinarRango($promedioFinal, $tiposNotas);
                            ?>
                            <td align="center"><?= $promedioFinal   == 0 ? '' : number_format($promedioFinal,$config['conf_decimales_notas']); ?></td>
                            <td align="center"><?= $desempenoFinal["notip_nombre"]  ?></td>
                            <td align="center">&nbsp;</td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="font-weight:bold; text-align:center;">
                    <td style="text-align:left;">PROMEDIO / TOTAL</td>
                    <td><?= $totalIh; ?></td>
                    <?php
                    $promedioTotal = 0;
                    for ($j = 1; $j <= $periodoSeleccionado; $j++) {
                        $promedioGeneral = round($totalNotasPeriodo[$j] / $contarCargas, 2);
                        $promedioTotal += $promedioGeneral;
                        $desempenoGeneral = Boletin::determinarRango($promedioGeneral, $tiposNotas);
                    ?>
                        <td><?= $fallasPeriodo[$j] == 0 ? '' : '-' ?></td>
                        <td><?= $promedioGeneral ?></td>
                        <td><?= $desempenoGeneral["notip_nombre"] ?></td>
                    <?php } ?>
                    <?php
                    $promedioTotal = round($promedioTotal / $periodoSeleccionado, 2);
                    $desempenoTotal = Boletin::determinarRango($promedioTotal, $tiposNotas); ?>
                    <td><?=$promedioTotal   == 0 ? '' : number_format($promedioTotal,$config['conf_decimales_notas']);  ?></td>
                    <td><?= $desempenoTotal["notip_nombre"] ?></td>
                    <td>-</td>
                </tr>
            </tfoot>

        </table>
        <p>&nbsp;</p>
        <table width="100%" cellspacing="5" cellpadding="5" rules="none" border="0">
            <tr>
                <td width="40%">
                    ________________________________________________________________<br>
                    <?php if (!empty($directorGrupo['uss_nombre'])) echo strtoupper($directorGrupo['uss_nombre']); ?><br>
                    DIRECTOR DE CURSO
                </td>
                <td width="20%">
                    <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
                        <?php
                        $contador = 1;
                        foreach ($tiposNotas as $desemp) {
                            if ($contador % 2 == 1) {
                                $fondoFila = '#EAEAEA';
                            } else {
                                $fondoFila = '#FFF';
                            } ?>
                            <tr style="background:<?= $fondoFila; ?>">
                                <td><?= $desemp['notip_nombre']; ?></td>
                                <td align="center"><?= $desemp['notip_desde'] . " - " . $desemp['notip_hasta']; ?></td>
                            </tr>
                        <?php $contador++;
                        } ?>
                    </table>
                </td>

                <?php
                $msjPromocion = '';
                if ($periodoSeleccionado == $config['conf_periodos_maximos']) {
                    if ($materiasPerdidas == 0) {
                        $msjPromocion = 'PROMOVIDO';
                    } else {
                        $msjPromocion = 'NO PROMOVIDO';
                    }
                }

                ?>
                <td width="60%">
                    <p style="font-weight:bold;">Observaciones: <b><?= $msjPromocion; ?></b></p>
                    ______________________________________________________________________<br><br>
                    ______________________________________________________________________<br><br>
                    ______________________________________________________________________
                </td>
            </tr>
        </table>
        <div id="saltoPagina"></div>
        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
            <thead>
                <tr style="font-weight:bold; text-align:center;">
                    <td width="30%">Asignaturas</td>
                    <td width="70%">Contenidos Evaluados</td>
                </tr>
            </thead>

            <?php
            foreach ($indicadoresCarga as $datosCargas) {
            ?>
                <tbody>
                    <tr style="color:#585858;">
                        <td><?= $datosCargas['mat_nombre']; ?><br>
                            <span style="color:#C1C1C1;"><?= UsuariosPadre::nombreCompletoDelUsuario($datosCargas); ?></span>
                        </td>
                        <td><?= $datosCargas['ind_nombre']; ?></td>
                    </tr>
                </tbody>
            <?php
            }
            ?>
        </table>
    <?php }; ?>
    <div id="saltoPagina"></div>


</body>

<script type="application/javascript">
    print();
</script>

</html>