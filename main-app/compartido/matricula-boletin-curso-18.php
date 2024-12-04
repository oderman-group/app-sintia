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


if (!empty($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}
$idEstudiante = '';
if (!empty($_GET["id"])) {

    $filtro = " AND mat_id='" . base64_decode($_GET["id"]) . "'";
    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    Utilidades::validarInfoBoletin($datos);
    $estudiante = $matriculadosPorCurso->fetch_assoc();
    if (!empty($estudiante)) {
        $idEstudiante = $estudiante["mat_id"];
        $grado        = $estudiante["mat_grado"];
        $grupo        = $estudiante["mat_grupo"];
    } 
}

$tamañoLogo = $_SESSION['idInstitucion'] == ICOLVEN ? 100 : 50;
$periodoFinal= ($periodoSeleccionado == $config['conf_periodos_maximos']);

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
    <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
    <title>Boletín</title>
    <style>
        #saltoPagina {
            PAGE-BREAK-AFTER: always;
        }
    </style>
</head>
<?php
// Cosnultas iniciales
$listaDatos = [];
$tiposNotas = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
    $tiposNotas[] = $row;
}

if (!empty($grado) && !empty($grupo) && !empty($periodoSeleccionado) && !empty($year)) {
    $datos = Boletin::datosBoletin($grado, $grupo, $periodos, $year, $idEstudiante,false);
    while ($row = $datos->fetch_assoc()) {
        $listaDatos[] = $row;
    }
}
$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);

$puestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoSeleccionado, $grado, $grupo, $year);
$puestoCurso = [];
while ($puesto = mysqli_fetch_array($puestos, MYSQLI_BOTH)) {
    $puestoCurso[$puesto['bol_estudiante']] = $puesto['puesto'];
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
    $listaCargas[] = $row;
}
$colspan = 5 + $celdas;
?>

<?php include("../compartido/agrupar-datos-boletin-periodos-mejorado.php") ?>

<body style="font-family:Arial;">
    <?php foreach ($estudiantes  as  $estudiante) {
        $totalNotasPeriodo = [];
        $fallasPeriodo     = [];
        $materiasPerdidas  =0;
    ?>
        <div align="center" style="margin-bottom:20px;">
            <img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" height="50"><br>
            <?= $informacion_inst["info_nombre"] ?><br>BOLETÍN DE CALIFICACIONES<br>
        </div>
        <table width="100%" cellspacing="5" cellpadding="5" border="0" rules="none">
            <tr>
                <td>Documento:<br> <?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?></td>
                <td>Nombre:<br> <?= $estudiante["nombre"]; ?></td>
                <td>Grado:<br> <?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></td>
                <td>Puesto Curso:<br> <?= isset($puestoCurso[$estudiante["mat_id"]]) ? $puestoCurso[$estudiante["mat_id"]] : 'N/A' ?></td>
            </tr>
            <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?= $informacion_inst["info_nombre"] ?></td>
                <td>Periodo (Año):<br> <b><?= $periodoActuales . " (" . $year . ")"; ?></b></td>
                <td>Fecha Impresión:<br> <?= date("d/m/Y H:i:s"); ?></td>
            </tr>
        </table>
        <br>
        <table width="100%" style="margin-bottom: 15px;" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
            <tr style="font-weight:bold; background-color:#00adefad; border-color:#000; color:#000; font-size:12px;">
                <td width="1%" align="center" rowspan="2">Nº</td>
                <td width="20%" align="center" rowspan="2">AREAS/ ASIGNATURAS</td>
                <td width="2%" align="center" rowspan="2">I.H</td>
                <?php for ($j = 1; $j <= $periodoSeleccionado; $j++) { ?>
                    <td width="2%" align="center" colspan="2">Periodo <?= $j ?></td>
                <?php } ?>
                <td width="3%" colspan="3" align="center">Acumulado</td>
            </tr>
            <tr style="font-weight:bold; text-align:center; background-color:#00adefad; border-color:#000; color:#000; font-size:12px;">
                <?php for ($j = 1; $j <= $periodoSeleccionado; $j++) { ?>
                    <td width="1%">Nota</td>
                    <td width="1%">Desempeño</td>
                <?php } ?>
                <td width="1%">Nota</td>
                <td width="1%">Desempeño</td>
            </tr>
            <?php
            $cantidadMaterias = 0;
            foreach ($estudiante["areas"]  as  $area) {  ?>
                <tr style="background-color: #EAEAEA" style="font-size:12px;">
                    <td colspan="<?= $colspan ?>" style="font-size:12px; font-weight:bold;"><?= $area["ar_nombre"]; ?></td>
                </tr>
                <?php
                foreach ($area["cargas"]  as  $carga) {
                    $cantidadMaterias++; ?>
                    <tr>
                        <td align="center"><?= $carga["nro"]; ?></td>
                        <td style="font-size:12px; font-weight:bold;"><?= $carga["mat_nombre"]; ?></td>
                        <td align="center" style="font-size:12px;"><?= $carga["car_ih"]; ?></td>
                        <?php
                        $promedioMateria = 0;
                        for ($j = 1; $j <= $periodoSeleccionado; $j++) {
                            Utilidades::valordefecto($carga["periodos"][$j]['bol_tipo'],'1');
                            Utilidades::valordefecto($carga["periodos"][$j]['bol_nota'],0);
                            $recupero = $carga["periodos"][$j]['bol_tipo'] == '2';
                            $nota     = $carga["periodos"][$j]["bol_nota"];
                            $nota     = Boletin::notaDecimales($nota);
                            $desempeno = Boletin::determinarRango($nota , $tiposNotas);

                        ?>
                            <td align="center" style="font-size:12px;<?= $recupero ? 'color: #2b34f4;" title="Nota del periodo Recuperada ' . $carga['periodos'][$j]['bol_nota_anterior'] . '"' : '' ?>">
                            <?= $nota  ?>
                            </td>
                            <td align="center" style=" font-size:12px;"><?= $desempeno["notip_nombre"] ?></td>
                        <?php }
                        $notaAcumulada = $carga["nota_carga_acumulada"];
                        $notaAcumulada = Boletin::notaDecimales($notaAcumulada);
                        $desempenoAcumulado = Boletin::determinarRango($notaAcumulada, $tiposNotas);
                        if ($notaAcumulada  < $config['conf_nota_minima_aprobar']) {
                            $materiasPerdidas++;
                        }
                        ?>
                        <td align="center" style=" font-size:12px;font-weight:bold; "><?=  $notaAcumulada ?></td>
                        <td align="center" style=" font-size:12px;font-weight:bold; "><?= $desempenoAcumulado["notip_nombre"] ?></td>
                    </tr>
                    <?php if (!empty($notasPeriodos[$estudiante["mat_id"]][$area["ar_id"]][$carga["car_id"]][$periodoSeleccionado]["bol_observaciones_boletin"])) { ?>
                        <tr>
                            <td colspan="<?= $colspan ?>">
                                <h5 align="center" style="margin: 0">Observaciones</h5>
                                <p style="margin: 0 0 0 10px; font-size: 11px; font-style: italic;">
                                    <?= $notasPeriodos[$estudiante["mat_id"]][$area["ar_id"]][$carga["car_id"]][$periodoSeleccionado]["bol_observaciones_boletin"] ?>
                                </p>
                            </td>
                        </tr>

                    <?php } ?>

                <?php } ?>
            <?php } ?>
            <tr bgcolor="#EAEAEA" style="font-size:12px;font-weight:bold; text-align:center;">
                <td colspan="3" style="text-align:left; font-weight:bold; font-size:12px;">PROMEDIO GENERAL</td>

                
                <?php 
                $promedioFinal     = 0; 
                $porcentajePeriodo = 0;
                for ($i = 1; $i <= $periodoSeleccionado; $i++) {
                    $periodo=$estudiante["promedios_generales"][$i] ;
                    Utilidades::valordefecto($periodo["nota_materia_promedio"],0);
                    $promedio          =  $periodo["nota_materia_promedio"] ;
                    $promedio          =  Boletin::notaDecimales($promedio, $tiposNotas);
                    $porcentajePeriodo =  $periodo["porcentaje_periodo"] ;
                    $promedioFinal     += $periodoFinal? $promedio * ($porcentajePeriodo/100): $promedio/$periodoSeleccionado;
                    ?>
                        <td style=" font-size:12px;font-weight:bold;"><?= $promedio ?></td>
                        <td style=" font-size:12px;font-weight:bold;"><?= Boletin::determinarRango($promedio, $tiposNotas)["notip_nombre"]; ?></td>
                      
                      
					<?php } ?>
                <?php
                $promedioFinal=Boletin::notaDecimales($promedioFinal, $tiposNotas);
                ?>
                <td style=" font-size:12px;"><?= $promedioFinal ?></td>
                <td style=" font-size:12px;"><?= Boletin::determinarRango($promedioFinal, $tiposNotas)["notip_nombre"]; ?></td>
            </tr>
            <tr bgcolor="#EAEAEA" style="font-size:12px; text-align:center;">
                <td colspan="3" style="text-align:left;  font-size:12px;">AUSENCIAS</td>

                <?php $fallasFinal=0;
                  foreach ($estudiante["promedios_generales"] as $promedios_generales) {
                    $suma_ausencias =  $promedios_generales["suma_ausencias"] ;
                    $fallasFinal    += $suma_ausencias;
                ?>

                    <td colspan="2" style=" font-size:12px;"><?= $suma_ausencias ?> Aús.</td>
                <?php
                }
                ?>
                <td colspan="2" style=" font-size:12px;"><?= $fallasFinal ?> Aús.</td>
            </tr>
        </table>
        <td>&nbsp;</td>
        <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
            <tr>
                <td style=" font-size:12px;">Tabla de desempeño:</td>
                <?php
                $consulta = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
                while ($estiloNota = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                ?>
                    <td align="center" style="font-size:12px;"><?= $estiloNota['notip_nombre'] . ": " . $estiloNota['notip_desde'] . " - " . $estiloNota['notip_hasta']; ?></td>
                <?php
                }
                ?>
            </tr>
        </table>
        <?php if (!empty($observacionesConvivencia[$estudiante["mat_id"]])) {
            usort($observacionesConvivencia[$estudiante["mat_id"]], function ($a, $b) {
                return $a['periodo'] - $b['periodo']; // Orden ascendente por 'periodo'
            });
        ?>
            <table width="100%" style="margin-top: 15px;" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
                <tr style=" background:#00adefad; border-color:#036; font-size:12px; text-align:center">
                    <td colspan="3">OBSERVACIONES DE CONVIVENCIA</td>
                </tr>
                <tr style=" background:#EAEAEA; color:#000; font-size:12px; text-align:center">
                    <td width="8%">Periodo</td>
                    <td>Observaciones</td>
                </tr>
                <?php
                foreach ($observacionesConvivencia[$estudiante["mat_id"]] as $observacion) {
                    if ($observacion["estudiante"] == $estudiante["mat_id"]) {
                ?>
                        <tr align="center" style="font-size:12px; height:20px;">
                            <td style="font-weight:bold;"><?= $observacion["periodo"] ?></td>
                            <td align="left"  style="padding-left:5px;"><?= $observacion["observacion"] ?></td>
                        </tr>

                <?php  }
                } ?>
            </table>
        <?php } ?>
        <p >&nbsp;</p>
        <div style="font-weight:bold;">
        <?= Boletin::mensajeFinalEstudainte($periodoSeleccionado,$materiasPerdidas,$estudiante["nombre"],$estudiante["genero"],$promedioFinal)?>
        </div>
        <?php include("../compartido/firmas-informes.php");?>
        <p>&nbsp;</p>
        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1" align="center" style="font-size:10px;">
            <thead>
                <tr style="font-weight:bold; text-align:center; background-color: #00adefad;">
                    <td width="30%">Asignatura</td>
                    <td width="70%">Indicadores de desempeño</td>
                </tr>
            </thead>

            <?php
            foreach ($listaCargas as $carga) { ?>
                <tbody>
                    <tr style="color:#000;">
                        <td><?= $carga['mat_nombre']; ?><br><span style="color:#C1C1C1;"><?= UsuariosPadre::nombreCompletoDelUsuario($carga); ?></span></td>
                        <td>
                            <?php
                            //INDICADORES
                            foreach ($carga["indicadores"] as $indicador) { ?>
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
        <?php 
        
        include("../compartido/footer-informes.php");      
        ?>
        <div id="saltoPagina"></div>
    <?php  }  ?>
</body>

<script type="application/javascript">
    print();
</script>

</html>