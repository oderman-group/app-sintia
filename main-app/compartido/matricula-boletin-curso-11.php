<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once ROOT_PATH."/main-app/class/Indicadores.php";
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$Plataforma = new Plataforma;

$year = $_SESSION["bd"];
if (isset($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}
$modulo = 1;

$periodoActual = base64_decode($_GET["periodo"]);
if (empty($_GET["periodo"])) {
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
$filtro = "";
if (!empty($_GET["id"])) {
    $filtro .= " AND mat_id='" . base64_decode($_GET["id"]) . "'";
}
if (!empty($_REQUEST["curso"])) {
    $filtro .= " AND mat_grado='" . base64_decode($_REQUEST["curso"]) . "'";
}
if (!empty($_REQUEST["grupo"])) {
    $filtro .= " AND mat_grupo='" . base64_decode($_REQUEST["grupo"]) . "'";
}
$contadorEstudiantes=0;
$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
Utilidades::validarInfoBoletin($matriculadosPorCurso);
$numeroEstudiantes = mysqli_num_rows($matriculadosPorCurso);
while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {
    $gradoActual = $matriculadosDatos['mat_grado'];
    $grupoActual = $matriculadosDatos['mat_grupo'];
    //contador materias
    $contPeriodos = 0;
    $contadorIndicadores = 0;
    $materiasPerdidas = 0;
    //======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
    $consultaEstudiantes = Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
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
    $puestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual, $matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $year);
    
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
        $consultaAreaEstudiante = Boletin::obtenerAreasDelEstudiante($matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $year);
        ?>
        <div align="center" style="margin-bottom:20px;">
            <img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" height="50"><br>
            <?= $informacion_inst["info_nombre"] ?><br>BOLETÍN DE CALIFICACIONES<br>
        </div>
        <table width="100%" cellspacing="5" cellpadding="5" border="0" rules="none">
            <tr>
                <td>Documento:<br> <?=strpos($datosEstudiantes["mat_documento"], '.') !== true && is_numeric($datosEstudiantes["mat_documento"]) ? number_format($datosEstudiantes["mat_documento"],0,",",".") : $datosEstudiantes["mat_documento"];?></td>
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
        <table width="100%" style="margin-bottom: 15px;" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
                <tr style="font-weight:bold; background-color:#00adefad; border-color:#000; color:#000; font-size:12px;">
                    <td width="1%" align="center" rowspan="2">Nº</td>
                    <td width="20%" align="center" rowspan="2">AREAS/ ASIGNATURAS</td>
                    <td width="2%" align="center" rowspan="2">I.H</td>
                    <?php for ($j = 1; $j <= $periodoActual; $j++) { ?>
                        <td width="2%" align="center" colspan="2"><a href="<?= $_SERVER['PHP_SELF']; ?>?id=<?= $matriculadosDatos['mat_id']; ?>&periodo=<?= $j ?>" style="color:#000; text-decoration:none;">Periodo <?= $j ?></a></td>
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
            $matPromedio = 0;
            $ausPer1Total=0;
            $ausPer2Total=0;
            $ausPer3Total=0;
            $ausPer4Total=0;
            $sumAusenciasTotal=0;
            $sumaNotaP1 = 0;
            $sumaNotaP2 = 0;
            $sumaNotaP3 = 0;
            $sumaNotaP4 = 0;
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
                $consultanombrePromedioArea = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $area["ar_id"], $condicion, $year);

                //CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
                $consultaDefinitivaNombreMateria = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $area["ar_id"], $condicion, $year);

                //CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
                $consultaDefinitivaPeriodo = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $area["ar_id"], $condicion, $year);
                
                //CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
                $consultaMateriaIndicadores = Boletin::obtenerIndicadoresPorMateria($datosEstudiantes["mat_grado"], $datosEstudiantes["mat_grupo"], $area["ar_id"], $condicion, $matriculadosDatos['mat_id'], $condicion2, $year);

                $numIndicadores = mysqli_num_rows($consultaMateriaIndicadores);
                $resultadoNotaArea = mysqli_fetch_array($consultanombrePromedioArea, MYSQLI_BOTH);
                $numfilasNotaArea = mysqli_num_rows($consultanombrePromedioArea);
                if ($numfilasNotaArea > 0) {
            ?>
                
                
                    <tr style="background-color: #EAEAEA" style="font-size:12px;">
                        <td colspan="<?=$colspan?>" style="font-size:12px; font-weight:bold;"><?=$resultadoNotaArea["ar_nombre"]; ?></td>
                    </tr>
                    <?php
                    while ($materia = mysqli_fetch_array($consultaDefinitivaNombreMateria, MYSQLI_BOTH)) {
                        //DIRECTOR DE GRUPO
                        if($materia["car_director_grupo"]==1){
                            $idDirector=$materia["car_docente"];
                        }

                        $sumAusencias=0;
                        $ausPer1=0;
                        $ausPer2=0;
                        $ausPer3=0;
                        $ausPer4=0;
                        for($j = 1; $j <= $periodoActual; $j++){
        
                            $consultaDatosAusencias= Boletin::obtenerDatosAusencias($datosEstudiantes['gra_id'], $materia['mat_id'], $j, $datosEstudiantes['mat_id'], $year);
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
                        <tr>
                            <td align="center"><?= $contador; ?></td>
                            <td style="font-size:12px; font-weight:bold;"><?=$materia["mat_nombre"]; ?></td>
                            <td align="center" style="font-size:12px;"><?=$materia["car_ih"]; ?></td>
                            <?php
                                $promedioMateria = 0;
                                for ($j = 1; $j <= $periodoActual; $j++) {

                                    //CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA POR PERIODO
                                    $consultaNotaMateriaIndicadoresxPeriodo = Boletin::obtenerIndicadoresDeMateriaPorPeriodo($datosEstudiantes["mat_grado"], $datosEstudiantes["mat_grupo"], $area["ar_id"], $j, $matriculadosDatos['mat_id'], $year);

                                    $numIndicadoresPorPeriodo=mysqli_num_rows($consultaNotaMateriaIndicadoresxPeriodo);
                                    $sumaNotaEstudiante=0;
                                    while ($datosIndicadores = mysqli_fetch_array($consultaNotaMateriaIndicadoresxPeriodo, MYSQLI_BOTH)) {
                                        $nota=0;
                                        if ($datosIndicadores["mat_id"] == $materia["mat_id"]) {
                                            $nota = ($datosIndicadores["nota"]*($datosIndicadores["ipc_valor"]/100));
                                        }
                                        $sumaNotaEstudiante += $nota;
                                    }
                                    
                                    $estudianteNota=0;
                                    if($numIndicadoresPorPeriodo!=0){
                                        $estudianteNota=$sumaNotaEstudiante;
                                    }
                                    $notaEstudiante = round($estudianteNota, 2);
                                    
                                    $notaEstudiante= Boletin::agregarDecimales($notaEstudiante);

                                    $desempenoNotaP = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaEstudiante,$year);
                                    $desempenoNotaPFinal= !empty($desempenoNotaP['notip_nombre']) ? $desempenoNotaP['notip_nombre'] : "";

                                    $promedioMateria += $notaEstudiante;
                                    if ($materia["mat_sumar_promedio"] == SI) {
                                        switch($j){
                                            case 1:
                                                $sumaNotaP1 += $notaEstudiante;
                                                break;
                                            case 2:
                                                $sumaNotaP2 += $notaEstudiante;
                                                break;
                                            case 3:
                                                $sumaNotaP3 += $notaEstudiante;
                                                break;
                                            case 4:
                                                $sumaNotaP4 += $notaEstudiante;
                                                break;
                                        }
                                    }
                            ?>
                                <td align="center" style=" font-size:12px;"><?=$notaEstudiante;?></td>
                                <td align="center" style=" font-size:12px;"><?=$desempenoNotaPFinal;?></td>
                            <?php
                                }
                                if ($materia["mat_sumar_promedio"] == SI) {
                                    $matPromedio ++;
                                }
                                $promedioMateria = round($promedioMateria / ($j - 1), 2);
                                $promedioMateriaFinal = $promedioMateria;

                                $consultaNivelacion = Boletin::obtenerNivelaciones($materia['car_id'], $matriculadosDatos['mat_id'], $year);
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

                                $promediosMateriaEstiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioMateriaFinal,$year);
                                $promediosMateriaEstiloNotaFinal= !empty($promediosMateriaEstiloNota['notip_nombre']) ? $promediosMateriaEstiloNota['notip_nombre'] : "";
                            ?>
                            <td align="center" style=" font-size:12px;"><?=$promedioMateriaFinal;?></td>
                            <td align="center" style=" font-size:12px;"><?=$promediosMateriaEstiloNotaFinal;?></td>
                        </tr>
                        <?php
                        if($materia['car_observaciones_boletin'] == 1) {
                            $consultaObsevacion=Boletin::obtenerObservaciones($materia["car_id"], $periodoActual, $matriculadosDatos['mat_id'], $year);
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
                    switch($j){
                        case 1:
                            $promediosPeriodos = ($sumaNotaP1/$matPromedio);
                            break;
                        case 2:
                            $promediosPeriodos = ($sumaNotaP2/$matPromedio);
                            break;
                        case 3:
                            $promediosPeriodos = ($sumaNotaP3/$matPromedio);
                            break;
                        case 4:
                            $promediosPeriodos = ($sumaNotaP4/$matPromedio);
                            break;
                    }
                    $promediosPeriodos = round($promediosPeriodos, 2);

                    $promediosEstiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promediosPeriodos,$year);
                    $promediosEstiloNotaFinal= !empty($promediosEstiloNota['notip_nombre']) ? $promediosEstiloNota['notip_nombre'] : "";
                ?>

                    <td style=" font-size:12px;"><?= $promediosPeriodos; ?></td>
                    <td style=" font-size:12px;"><?= $promediosEstiloNotaFinal; ?></td>
                <?php 
                    $promedioFinal +=$promediosPeriodos;
                } 

                    $promedioFinal = round($promedioFinal/$periodoActual,2);

                    $promedioFinalEstiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioFinal,$year);
                    $promedioFinalEstiloNotaFinal= !empty($promedioFinalEstiloNota['notip_nombre']) ? $promedioFinalEstiloNota['notip_nombre'] : "";
                ?>
                <td style=" font-size:12px;"><?=$promedioFinal;?></td>
                <td style=" font-size:12px;"><?= $promedioFinalEstiloNotaFinal; ?></td>
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
        <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
            <tr>
                <td style=" font-size:12px;">Tabla de desempeño:</td>
                <?php
                    $consulta = Boletin::listarTipoDeNotas($config["conf_notas_categoria"],$year);
                    while($estiloNota = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                ?>
                    <td align="center" style="font-size:12px;"><?=$estiloNota['notip_nombre'].": ".$estiloNota['notip_desde']." - ".$estiloNota['notip_hasta'];?></td>
                <?php
                    }
                ?>
            </tr>
        </table>
        <?php
        $cndisciplina = Boletin::obtenerNotaDisciplina($matriculadosDatos['mat_id'], $condicion, $year);
        if (@mysqli_num_rows($cndisciplina) > 0) {
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
                while ($rndisciplina = mysqli_fetch_array($cndisciplina, MYSQLI_BOTH)) {

                    $desempenoND = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $rndisciplina["dn_nota"],$year);
                    $desempenoNDFinal= !empty($desempenoND['notip_nombre']) ? $desempenoND['notip_nombre'] : "";
                ?>
                    <tr align="center" style=" font-size:12px;">
                        <td><?= $rndisciplina["dn_periodo"] ?></td>
                        <td align="left"><?= $rndisciplina["dn_observacion"] ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
        <!--******FIRMAS******-->   

        <table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
            <tr>
                <td align="center">
                    <?php
                        $rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
                        $nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
                        if(!empty($rector["uss_firma"])){
                            echo '<img src="../files/fotos/'.$rector["uss_firma"].'" width="100"><br>';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>';
                        }
                    ?>
                    <p style="height:0px;"></p>_________________________________<br>
                    <p>&nbsp;</p>
                    <!-- <?=$nombreRector?><br> -->
                    Rector(a)
                </td>
                <td align="center">
                    <?php
                        $directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
                        $nombreDirectorGrupo = UsuariosPadre::nombreCompletoDelUsuario($directorGrupo);
                        if(!empty($directorGrupo["uss_firma"])){
                            echo '<img src="../files/fotos/'.$directorGrupo["uss_firma"].'" width="100"><br>';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>';
                        }
                    ?>
                    <p style="height:0px;"></p>_________________________________<br>
                    <p>&nbsp;</p>
                    <!-- <?=$nombreDirectorGrupo?><br> -->
                    Director(a) de grupo
                </td>
            </tr>
        </table>

        <div id="saltoPagina"></div>
        <!--******SEGUNDA PAGINA******-->
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <!--******INDICADORES POR ASIGNATURA******-->

        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1" align="center" style="font-size:10px;">
            <thead>
                <tr style="font-weight:bold; text-align:center; background-color: #00adefad;">
                    <td width="30%">Asignatura</td>
                    <td width="70%">Indicadores de desempeño</td>
                </tr>
            </thead>

            <?php
            $conCargasDos = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $gradoActual, $grupoActual, $year);
            while ($datosCargasDos = mysqli_fetch_array($conCargasDos, MYSQLI_BOTH)) {

                
            ?>
                <tbody>
                    <tr style="color:#000;">
                        <td><?= $datosCargasDos['mat_nombre']; ?><br><span style="color:#C1C1C1;"><?= UsuariosPadre::nombreCompletoDelUsuario($datosCargasDos); ?></span></td>
                        <td>
                        
                            <?php
                            //INDICADORES
                            $indicadores = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $datosCargasDos['car_id'], $periodoActual, $year);
                            while ($indicador = mysqli_fetch_array($indicadores, MYSQLI_BOTH)) {
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

        <?php
            $contadorEstudiantes++;
            if($contadorEstudiantes!=$numeroEstudiantes && empty($_GET['id'])){
        ?>
        <div id="saltoPagina"></div>
    <?php
            }
    } // FIN DE TODOS LOS MATRICULADOS
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    ?>
    <script type="application/javascript">
        print();
    </script>
    </body>
    </html>