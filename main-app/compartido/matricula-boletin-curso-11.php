<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
require_once("../class/Estudiantes.php");
require_once("../class/Boletin.php");
$Plataforma = new Plataforma;

$year = $agnoBD;
if (isset($_GET["year"])) {
    $year = $_GET["year"];
}
$BD = $_SESSION["inst"] . "_" . $year;
$modulo = 1;

$periodoActual = $_GET["periodo"];
if ($_GET["periodo"] == "") {
    $periodoActual = 1;
}

switch($periodoActual){
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
$colspan=5+$celdas;
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<?php
if (!empty($_GET["id"])) {
    $filtro .= " AND mat_id='" . $_GET["id"] . "'";
}
if (!empty($_REQUEST["curso"])) {
    $filtro .= " AND mat_grado='" . $_REQUEST["curso"] . "'";
}
if (!empty($_REQUEST["grupo"])) {
    $filtro .= " AND mat_grupo='" . $_REQUEST["grupo"] . "'";
}
$contadorEstudiantes=0;
$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $BD);
$numeroEstudiantes = mysqli_num_rows($matriculadosPorCurso);
while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {
    //contador materias
    $contPeriodos = 0;
    $contadorIndicadores = 0;
    $materiasPerdidas = 0;
    //======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
    $consultaEstudiantes = Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos[0],$BD);
    $numEstudiantes = mysqli_num_rows($consultaEstudiantes);
    $datosEstudiantes = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH);
    //METODO QUE ME TRAE EL NOMBRE COMPLETO DEL ESTUDIANTE
    $nombreEstudainte=Estudiantes::NombreCompletoDelEstudiante($datosEstudiantes);
    if ($numEstudiantes == 0) {
    ?>
        <script type="text/javascript">
            window.close();
        </script>
    <?php
        exit();
    }
    $contadorPeriodos = 0;
    $contp = 1;
    $puestoCurso = 0;
    $puestos = Boletin::obtenerPuestoYpromedioEstudiante($_GET["periodo"], $matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $BD);
    
    while($puesto = mysqli_fetch_array($puestos, MYSQLI_BOTH)){
        if($puesto['bol_estudiante']==$matriculadosDatos['mat_id']){
            $puestoCurso = $puesto['puesto'];
        }
        $contp ++;
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Boletín</title>
        <!-- favicon -->
        <link rel="shortcut icon" href="../sintia-icono.png" />
        <style>
            #saltoPagina {
                PAGE-BREAK-AFTER: always;
            }
        </style>
    </head>

    <body style="font-family:Arial;">
        <?php
        //CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
        $consultaAreaEstudiante = Boletin::obtenerAreasDelEstudiante($matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $BD);
        ?>
        <div align="center" style="margin-bottom:20px;">
            <img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" height="50"><br>
            <?= $informacion_inst["info_nombre"] ?><br>BOLETÍN DE CALIFICACIONES<br>
        </div>
        <table width="100%" cellspacing="5" cellpadding="5" border="0" rules="none">
            <tr>
                <td>Documento:<br> <?= number_format($datosEstudiantes["mat_documento"], 0, ",", "."); ?></td>
                <td>Nombre:<br> <?= $nombreEstudainte; ?></td>
                <td>Grado:<br> <?= $datosEstudiantes["gra_nombre"] . " " . $datosEstudiantes["gru_nombre"]; ?></td>
                <td>Puesto Curso:<br> <?=$puestoCurso?></td>    
            </tr>
            <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?= $informacion_inst["info_nombre"] ?></td>
                <td>Periodo (Año):<br> <b><?= $periodoActuales . " (" . $year . ")"; ?></b></td>
                <td>Fecha Impresión:<br> <?= date("d/m/Y H:i:s"); ?></td>
            </tr>
        </table>
        <table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
                <tr style="font-weight:bold; background-color:#00adefad; border-color:#000; color:#000; font-size:12px;">
                    <td width="1%" align="center" rowspan="2">Nº</td>
                    <td width="20%" align="center" rowspan="2">AREAS/ ASIGNATURAS</td>
                    <td width="2%" align="center" rowspan="2">I.H</td>
                    <?php for ($j = 1; $j <= $periodoActual; $j++) { ?>
                        <td width="2%" align="center" colspan="2"><a href="<?= $_SERVER['PHP_SELF']; ?>?id=<?= $matriculadosDatos[0]; ?>&periodo=<?= $j ?>" style="color:#000; text-decoration:none;">Periodo <?= $j ?></a></td>
                    <?php } ?>
                    <td width="3%" colspan="3" align="center">Acumulado</td>
                </tr>

                <tr style="font-weight:bold; text-align:center; background-color:#00adefad; border-color:#000; color:#000; font-size:12px;">
                    <?php for ($j = 1; $j <= $periodoActual; $j++) { ?>
                        <td width="1%">Nota</td>
                        <td width="1%">Desempeño</td>
                    <?php } ?>
                    <td width="1%">Nota</td>
                    <td width="1%">Desempeño</td>
                </tr>
            <?php
            $contador = 1;
            $ausPer1Total=0;
            $ausPer2Total=0;
            $ausPer3Total=0;
            $ausPer4Total=0;
            $sumAusenciasTotal=0;
            $sumaNota = 0;
            while ($area = mysqli_fetch_array($consultaAreaEstudiante, MYSQLI_BOTH)) {
                switch($periodoActual){
                    case 1:
                        $condicion = "1";
                        $condicion2 = "1";
                        break;
                    case 2:
                        $condicion = "1,2";
                        $condicion2 = "2";
                        break;
                    case 3:
                        $condicion = "1,2,3";
                        $condicion2 = "3";
                        break;
                    case 4:
                        $condicion = "1,2,3,4";
                        $condicion2 = "4";
                        break;
                }
                //CONSULTA QUE ME TRAE EL NOMBRE Y EL PROMEDIO DEL AREA
                $consultanombrePromedioArea = Boletin::obtenerDatosDelArea($matriculadosDatos[0], $area["ar_id"], $condicion, $BD);

                //CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
                $consultaDefinitivaNombreMateria = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos[0], $area["ar_id"], $condicion, $BD);

                //CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
                $consultaDefinitivaPeriodo = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos[0], $area["ar_id"], $condicion, $BD);
                
                //CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
                $consultaMateriaIndicadores = Boletin::obtenerIndicadoresPorMateria($datosEstudiantes["mat_grado"], $datosEstudiantes["mat_grupo"], $area["ar_id"], $condicion, $matriculadosDatos[0], $condicion2, $BD);

                $numIndicadores = mysqli_num_rows($consultaMateriaIndicadores);
                $resultadoNotaArea = mysqli_fetch_array($consultanombrePromedioArea, MYSQLI_BOTH);
                $numfilasNotaArea = mysqli_num_rows($consultanombrePromedioArea);
                if ($numfilasNotaArea > 0) {
            ?>
                
                
                    <tr style="background-color: #EAEAEA" style="font-size:12px;">
                        <td colspan="<?=$colspan?>" style="font-size:12px; font-weight:bold;"><?php echo $resultadoNotaArea["ar_nombre"]; ?></td>
                    </tr>
                    <?php
                    while ($materia = mysqli_fetch_array($consultaDefinitivaNombreMateria, MYSQLI_BOTH)) {

                        $sumAusencias=0;
                        $ausPer1=0;
                        $ausPer2=0;
                        $ausPer3=0;
                        $ausPer4=0;
                        for($j = 1; $j <= $periodoActual; $j++){
        
                            $consultaDatosAusencias= Boletin::obtenerDatosAusencias($datosEstudiantes['gra_id'], $materia['mat_id'], $j, $datosEstudiantes['mat_id'], $BD);
                            $datosAusencias = mysqli_fetch_array($consultaDatosAusencias, MYSQLI_BOTH);
        
                            if($datosAusencias['sumAus']>0){
                                switch($j){
                                    case 1:
                                        $ausPer1+=$datosAusencias['sumAus'];
                                        break;
                                    case 2:
                                        $ausPer2+=$datosAusencias['sumAus'];
                                        break;
                                    case 3:
                                        $ausPer3+=$datosAusencias['sumAus'];
                                        break;
                                    case 4:
                                        $ausPer4+=$datosAusencias['sumAus'];
                                        break;
                                }
                                $sumAusencias+=$datosAusencias['sumAus'];
                            }
                        }

                        $contadorPeriodos = 0;
                        mysqli_data_seek($consultaDefinitivaPeriodo, 0);
                    ?>
                        <tr bgcolor="#EAEAEA">
                            <td align="center"><?= $contador; ?></td>
                            <td style="font-size:12px; background:#EAEAEA; font-weight:bold;"><?php echo $materia["mat_nombre"]; ?></td>
                            <td align="center" style="font-size:12px;background:#EAEAEA;"><?php echo $area["car_ih"]; ?></td>
                            <?php
                                $promedioMateria = 0;
                                for ($j = 1; $j <= $periodoActual; $j++) {

                                    //CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA POR PERIODO
                                    $consultaNotaMateriaIndicadoresxPeriodo = Boletin::obtenerIndicadoresDeMateriaPorPeriodo($datosEstudiantes["mat_grado"], $datosEstudiantes["mat_grupo"], $area["ar_id"], $j, $matriculadosDatos[0], $BD);

                                    $numIndicadoresPorPeriodo=mysqli_num_rows($consultaNotaMateriaIndicadoresxPeriodo);
                                    $sumaNotaEstudiante=0;
                                    while ($datosIndicadores = mysqli_fetch_array($consultaNotaMateriaIndicadoresxPeriodo, MYSQLI_BOTH)) {
                                        if ($datosIndicadores["mat_id"] == $materia["mat_id"]) {
                                                $nota = $datosIndicadores["nota"];
                                        }
    
                                        $sumaNotaEstudiante += $nota;
                                    }
                                    
                                    $estudianteNota=0;
                                    if($numIndicadoresPorPeriodo!=0){
                                        $estudianteNota=($sumaNotaEstudiante/$numIndicadoresPorPeriodo);
                                    }
                                    $notaEstudiante = round($estudianteNota, 2);
                                    
                                    $notaEstudiante= Boletin::agregarDecimales($notaEstudiante);

                                    $desempenoNotaP = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaEstudiante, $BD);

                                    $promedioMateria += $notaEstudiante;
                                    $sumaNota += $notaEstudiante;
                            ?>
                                <td align="center" style=" font-size:12px;"><?=$notaEstudiante;?></td>
                                <td align="center" style=" font-size:12px;"><?=$desempenoNotaP['notip_nombre'];?></td>
                            <?php
                                }
                                $promedioMateria = round($promedioMateria / ($j - 1), 2);
                                $promedioMateriaFinal = $promedioMateria;

                                $consultaNivelacion = Boletin::obtenerNivelaciones($datosCargas['car_id'], $matriculadosDatos['mat_id'], $BD);
                                $nivelacion = mysqli_fetch_array($consultaNivelacion, MYSQLI_BOTH);
        
                                // SI PERDIÓ LA MATERIA A FIN DE AÑO
                                if ($promedioMateria < $config["conf_nota_minima_aprobar"]) {
                                    if ($nivelacion['niv_definitiva'] >= $config["conf_nota_minima_aprobar"]) {
                                        $promedioMateriaFinal = $nivelacion['niv_definitiva'];
                                    } else {
                                        $materiasPerdidas++;
                                    }
                                }
                                
                                $promedioMateriaFinal= Boletin::agregarDecimales($promedioMateriaFinal);

                                $promediosMateriaEstiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioMateriaFinal, $BD);
                            ?>
                            <td align="center" style=" font-size:12px;"><?=$promedioMateriaFinal;?></td>
                            <td align="center" style=" font-size:12px;"><?=$promediosMateriaEstiloNota['notip_nombre'];?></td>
                        </tr>
                        <?php
                        if ($numIndicadores > 0) {
                            mysqli_data_seek($consultaMateriaIndicadores, 0);
                            $contadorIndicadores = 1;
                            while ($indicadores = mysqli_fetch_array($consultaMateriaIndicadores, MYSQLI_BOTH)) {
                                if ($indicadores["mat_id"] == $materia["mat_id"]) {
                        ?>
                                    <tr bgcolor="#FFF" style="font-size:12px;">
                                        <td colspan="3" style="font-size:12px; padding-left: 5px;"><?=$contadorIndicadores.". ".$indicadores["ind_nombre"]?></td>
                                        <?php
                                        for ($j = 1; $j <= $periodoActual; $j++) {
                                            $leyendaRI = '';
                                            $notaIndicador='';
                                            $desempeno='';
                                            if ($j == $periodoActual) {

                                                $consultaRecuperacionIndicador = Boletin::obtenerRecuperacionPorIndicador($matriculadosDatos[0], $materia["car_id"], $j, $indicadores["ind_id"], $BD);
                                                $recuperacionIndicador = mysqli_fetch_array($consultaRecuperacionIndicador, MYSQLI_BOTH);

                                                $notaIndicador = round($indicadores["nota"], 2);
                                                if ($recuperacionIndicador['rind_nota'] > $indicadores["nota"]) {
                                                    $notaIndicador = round($recuperacionIndicador['rind_nota'], 2);
                                                    $leyendaRI = '<br><span style="color:navy; font-size:9px;">Recuperdo.</span>';
                                                }
                                
                                                $notaIndicador= Boletin::agregarDecimales($notaIndicador);

                                                $desempenoNotaP = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaIndicador, $BD);
                                                $desempeno=$desempenoNotaP['notip_nombre'];
                                            }
                                        ?>
                                            <td align="center" style=" font-size:12px;"><?= $notaIndicador . "<br>" . $leyendaRI; ?></td>
                                            <td align="center" style=" font-size:12px;"><?=$desempeno;?></td>
                                        <?php
                                            }
                                        ?>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                        <?php
                                    $contadorIndicadores++;
                                } //fin if
                            }
                        }
                        $consultaObsevacion=Boletin::obtenerObservaciones($materia["car_id"], $_GET["periodo"], $matriculadosDatos[0], $BD);
                        $observacion = mysqli_fetch_array($consultaObsevacion, MYSQLI_BOTH);
                        if ($observacion['bol_observaciones_boletin'] != "") {
                            ?>
                            <tr>
                                <td colspan="<?=$colspan?>">
                                    <h5 align="center" style="margin: 0">Observaciones</h5>
                                    <p style="margin: 0 0 0 10px; font-size: 11px; font-style: italic;">
                                        <?= $observacion['bol_observaciones_boletin']; ?>
                                    </p>
                                </td>
                            </tr>
                        
                    <?php
                        }
                        $contador++;
                        $ausPer1Total+=$ausPer1;
                        $ausPer2Total+=$ausPer2;
                        $ausPer3Total+=$ausPer3;
                        $ausPer4Total+=$ausPer4;
                        $sumAusenciasTotal+=$sumAusencias;
                    } //while fin materias
                    ?>
            <?php }
            } //while fin areas
            ?>
            <tr bgcolor="#EAEAEA" style="font-size:12px; text-align:center;">
                <td colspan="3" style="text-align:left;  font-size:12px;">PROMEDIO GENERAL</td>

                <?php
                $promedioFinal = 0;
                for ($j = 1; $j <= $periodoActual; $j++) {
                    $promediosPeriodos = ($sumaNota/($contador-1));
                    $promediosPeriodos = round($promediosPeriodos, 2);

                    $promediosEstiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promediosPeriodos, $BD);
                ?>

                    <td style=" font-size:12px;"><?= $promediosPeriodos; ?></td>
                    <td style=" font-size:12px;"><?= $promediosEstiloNota['notip_nombre']; ?></td>
                <?php 
                    $promedioFinal +=$promediosPeriodos;
                } 

                    $promedioFinal = round($promedioFinal/$periodoActual,2);

                    $promedioFinalEstiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioFinal, $BD);
                ?>
                <td style=" font-size:12px;"><?=$promedioFinal;?></td>
                <td style=" font-size:12px;"><?= $promedioFinalEstiloNota['notip_nombre']; ?></td>
            </tr>

            <tr bgcolor="#EAEAEA" style="font-size:12px;  text-align:center;">
                <td colspan="3" style="text-align:left;">AUSENCIAS</td>
                <?php
                for ($j = 1; $j <= $periodoActual; $j++) {
                    switch($j){
                        case 1:
                            echo '<td>'.$ausPer1Total.' Aus.</td><td>&nbsp;</td>';
                            break;
                        case 2:
                            echo '<td>'.$ausPer2Total.' Aus.</td><td>&nbsp;</td>';
                            break;
                        case 3:
                            echo '<td>'.$ausPer3Total.' Aus.</td><td>&nbsp;</td>';
                            break;
                        case 4:
                            echo '<td>'.$ausPer4Total.' Aus.</td><td>&nbsp;</td>';
                            break;
                    }
                }
                ?>
                <td><?=$sumAusenciasTotal?> Aús.</td>
                <td>&nbsp;</td>
            </tr>
            
        </table>
        <p>&nbsp;</p>
        <table width="100%" cellspacing="2" cellpadding="2" rules="all" border="1">
            <tr>
                <td style=" font-size:12px;">Tabla de desempeño:</td>
                <?php
                    $consulta = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $BD);
                    while($estiloNota = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                ?>
                    <td align="center" style="font-size:12px;"><?=$estiloNota['notip_nombre'].": ".$estiloNota['notip_desde']." - ".$estiloNota['notip_hasta'];?></td>
                <?php
                    }
                ?>
            </tr>
        </table>
        <p>&nbsp;</p>
        <?php
        $cndisciplina = Boletin::obtenerNotaDisciplina($matriculadosDatos[0], $condicion, $BD);
        if (@mysqli_num_rows($cndisciplina) > 0) {
        ?>
            <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
                <tr style=" background:#00adefad; border-color:#036; font-size:12px; text-align:center">
                    <td colspan="3">OBSERVACIONES DE CONVIVENCIA</td>
                </tr>
                <tr style=" background:#EAEAEA; color:#000; font-size:12px; text-align:center">
                    <td width="8%">Periodo</td>
                    <td>Observaciones</td>
                </tr>
                <?php
                while ($rndisciplina = mysqli_fetch_array($cndisciplina, MYSQLI_BOTH)) {

                    $desempenoND = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $rndisciplina["dn_nota"], $BD);
                ?>
                    <tr align="center" style=" font-size:12px;">
                        <td><?= $rndisciplina["dn_periodo"] ?></td>
                        <td align="left"><?= $rndisciplina["dn_observacion"] ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
        <p>&nbsp;</p>
        <table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
            <tr>
                <td align="center"><br>_________________________________<br><?= strtoupper("");?><br>Rector(a)</td>
                <td align="center">
                    <p style="height:0px;"></p>_________________________________<br><?= strtoupper("");?><br>Director(a) de grupo
                </td>
            </tr>
        </table>

        <?php
            $contadorEstudiantes++;
            if($contadorEstudiantes!=$numeroEstudiantes && empty($_GET['id'])){
        ?>
        <div id="saltoPagina"></div>
    <?php
            }
    } // FIN DE TODOS LOS MATRICULADOS
    ?>
    <script type="application/javascript">
        print();
    </script>
    </body>
    </html>