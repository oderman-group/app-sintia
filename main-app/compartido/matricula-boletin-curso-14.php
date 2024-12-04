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

$year = $_SESSION["bd"];
if (isset($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}

if (empty($_GET["periodo"])) {

    $periodoActual = 1;
} else {

    $periodoActual = base64_decode($_GET["periodo"]);
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



if ($periodoActual == 1)
    $periodoActuales = "Primero";

if ($periodoActual == 2)
    $periodoActuales = "Segundo";

if ($periodoActual == 3)
    $periodoActuales = "Tercero";

if ($periodoActual == 4)
    $periodoActuales = "Final";

?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>



<!doctype html>

<html class="no-js" lang="en">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>

<head>

    <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">

    <title>Boletín</title>

    <style>
        #saltoPagina {

            PAGE-BREAK-AFTER: always;

        }
    </style>

</head>



<body style="font-family:Arial;">


    <?php
    
    $tiposNotas = [];
    $cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
    while ($row = $cosnultaTiposNotas->fetch_assoc()) {
        $tiposNotas[] = $row;
    }
    $listaDatos = [];
    $traerIndicadores=true;

    if (!empty($grado) && !empty($grupo) && !empty($periodo) && !empty($year)) {
        $periodosArray = [];
        for ($i = 1; $i <= $periodoActual; $i++) {
            $periodosArray[$i] = $i;
        }
       
        $datos = Boletin::datosBoletin($grado, $grupo, $periodosArray, $year, $idEstudiante, $traerIndicadores);
        while ($row = $datos->fetch_assoc()) {
            $listaDatos[] = $row;
        }
        include("../compartido/agrupar-datos-boletin-periodos-mejorado.php");
    }
    $rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);?>

    <?php foreach ($estudiantes as $estudiante) { ?>
        <div align="center" style="margin-bottom:20px;">
            <img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" width="<?= $tamañoLogo ?>%"><br>
            <!-- <?= $informacion_inst["info_nombre"] ?><br>
        BOLETÍN DE CALIFICACIONES<br> -->

        </div>
        <table width="100%" cellspacing="5" cellpadding="5" border="0" rules="none">
            <tr>
                <td>Documento:<br>
                    <?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?>
                </td>
                <td>Nombre:<br> <?= $estudiante["nombre"] ?></td>
                <td>Grado:<br> <?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?= $informacion_inst["info_nombre"] ?></td>
                <td>Periodo:<br> <b><?= $periodoActuales . " (" . $year . ")"; ?></b></td>
                <td>Fecha Impresión:<br> <?= date("d/m/Y H:i:s"); ?></td>
            </tr>
        </table>

        <p>&nbsp;</p>
        <table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">

            <tr
                style="font-weight:bold; background-color:#2e537dab; border-color:#000; height:40px; color:#000; font-size:12px;">
                <td width="2%" align="center">NO</td>
                <td width="20%" align="center">AREAS/ ASIGNATURAS</td>
                <td width="2%" align="center">I.H</td>
                <td width="2%" align="center">NOTA</td>
            </tr>
            <?php foreach ($estudiante["areas"] as $area) { ?>
                <tr style="background-color: #b9b91730" style="font-size:12px;">
                    <td colspan="2" style="font-size:12px; height:25px; font-weight:bold;padding-left: 10;">
                        <?php echo $area["ar_nombre"]; ?>
                    </td>
                    <td align="center" style="font-weight:bold; font-size:12px;"></td>
                    <td>&nbsp;</td>
                </tr>
                <?php foreach ($area["cargas"] as $carga) { ?>                    
                    <tr bgcolor="#EAEAEA" style="font-size:12px;">
                        <td align="center"><?= $carga["nro"] ?></td>
                        <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;padding-left: 10;"><?= $carga["mat_nombre"] ?></td>
                        <td align="center" style="font-weight:bold; font-size:12px;background:#EAEAEA;"><?= $carga["car_ih"]; ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php 
                        $contador_indicadores=1;
                        Utilidades::valordefecto($carga["periodos"][$periodoActual]['indicadores'],[]); 
                        foreach ($carga["periodos"][$periodoActual]['indicadores'] as $indicador) { 
                        $recuperoIndicador = $indicador["recuperado"];
                        ?> 
                        <tr bgcolor="#FFF" style="font-size:12px;">
                            <td align="center">&nbsp;</td>
                            <td style="font-size:12px; height:15px;"><?= $contador_indicadores . "." . $indicador["ind_nombre"]; ?></td>
                            <td>&nbsp;</td>
                            <td align="center" style="padding-left: 10;font-weight:bold; font-size:12px;<?= $recuperoIndicador ? 'color: #2b34f4;" title="Nota indicador recuperada ' . $indicador['valor_indicador'] . '"' : '' ?>"" <?= $indicador['ind_nombre']; ?> align="
												center"
                            >
                            <?= Boletin::formatoNota($indicador["nota_final"] , $tiposNotas) ?>
                            <?= $recuperoIndicador ?"<br><span style='color:navy; font-size:9px;'> Recuperdo. </span>":"" ?>
                            </td>
                        </tr>
                    <?php $contador_indicadores++; } ?>
                    <?php if(!empty($carga["periodos"][$periodoActual]['bol_observaciones_boletin'])) { ?>  
                    <tr>
                        <td colspan="4"> 
                            <h5 align="center">Observaciones</h5>
                            <p style="margin-left: 5px; font-size: 11px; margin-top: -10px; margin-bottom: 5px; font-style: italic;">
                                <?= $carga["periodos"][$periodoActual]['bol_observaciones_boletin'] ?>
                            </p>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <!-- MEDIA TECNICA -->
            <?php 
            if(!empty($estudiante["cursos_adicionales"])){
                foreach ($estudiante["cursos_adicionales"] as $curso) { ?>
                    <tr style="background-color: #b9b91730" style="font-size:12px;">
                        <td colspan="2" style="font-size:12px; height:25px; font-weight:bold;padding-left: 10;">
                            <?= $curso["gra_nombre"]; ?>
                        </td>
                        <td align="center" style="font-weight:bold; font-size:12px;"></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php foreach ($curso["cargas"] as $carga) { ?>                    
                        <tr bgcolor="#EAEAEA" style="font-size:12px;">
                            <td align="center"><?= $carga["nro"] ?></td>
                            <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;padding-left: 10;"><?= $carga["mat_nombre"] ?></td>
                            <td align="center" style="font-weight:bold; font-size:12px;background:#EAEAEA;"><?= $carga["car_ih"]; ?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php 
                            Utilidades::valordefecto($carga["periodos"][$periodoActual]['indicadores'],[]); 
                            foreach ($carga["periodos"][$periodoActual]['indicadores'] as $indicador) { 
                            $recuperoIndicador = $indicador["recuperado"];
                            ?> 
                            <tr bgcolor="#FFF" style="font-size:12px;">
                                <td align="center">&nbsp;</td>
                                <td style="font-size:12px; height:15px;"><?= $indicador["nro"] . "." . $indicador["ind_nombre"]; ?></td>
                                <td>&nbsp;</td>
                                <td align="center" style="padding-left: 10;font-weight:bold; font-size:12px;<?= $recuperoIndicador ? 'color: #2b34f4;" title="Nota indicador recuperada ' . $indicador['valor_indicador'] . '"' : '' ?>"" <?= $indicador['ind_nombre']; ?> align="
                                                    center"
                                >
                                <?= Boletin::formatoNota($indicador["nota_final"] , $tiposNotas) ?>
                                <?= $recuperoIndicador ?"<br><span style='color:navy; font-size:9px;'> Recuperdo. </span>":"" ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if(!empty($carga["periodos"][$periodoActual]['bol_observaciones_boletin'])) { ?>  
                        <tr>
                            <td colspan="4"> 
                                <h5 align="center">Observaciones</h5>
                                <p style="margin-left: 5px; font-size: 11px; margin-top: -10px; margin-bottom: 5px; font-style: italic;">
                                    <?= $carga["periodos"][$periodoActual]['bol_observaciones_boletin'] ?>
                                </p>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>    
        </table>
        <p>&nbsp;</p>
        <?php if(!empty($estudiante["observaciones_generales"] )) { ?>  
        <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
            <tr style="font-weight:bold; background:#2e537dab; border-color:#036; height:40px; font-size:12px; text-align:center">
                <td colspan="3">OBSERVACIONES DE CONVIVENCIA</td>
            </tr>
            <tr style="font-weight:bold; background:#b9b91730; height:25px; color:#000; font-size:12px; text-align:center">
                <td width="8%">Periodo</td>
                <td>Observaciones</td>
            </tr>
            <?php foreach ($estudiante["observaciones_generales"] as $observacion) { ?>
                <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">
                    <td><?= $observacion["periodo"] ?></td>
                    <td align="left"><?= $observacion["observacion"] ?></td>
                </tr>
            <?php } ?>            
        </table>
        <?php } ?>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <?php include("../compartido/firmas-informes.php") ?>
        <?php include("../compartido/footer-informes.php") ?>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <div id="saltoPagina"></div>
        <?php } ?>
    </div>
</body>

<script type="application/javascript">
    print();
</script>

</html>