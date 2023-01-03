<?php include("../directivo/session.php"); ?>
<?php include("../../config-general/config.php");?>
<?php
$modulo = 1;
if ($_GET["periodo"] == "") {
    $periodoActual = 1;
} else {
    $periodoActual = $_GET["periodo"];
}

if ($periodoActual == 1) $periodoActuales = "Primero";
if ($periodoActual == 2) $periodoActuales = "Segundo";
if ($periodoActual == 3) $periodoActuales = "Tercero";
if ($periodoActual == 4) $periodoActuales = "Final";
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if (is_numeric($_GET["id"])) {
    $filtro .= " AND mat_id='" . $_GET["id"] . "'";
}
if (is_numeric($_REQUEST["curso"])) {
    $filtro .= " AND mat_grado='" . $_REQUEST["curso"] . "'";
}
$matriculadosPorCurso = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
INNER JOIN academico_grados ON gra_id=mat_grado
INNER JOIN academico_grupos ON gru_id=mat_grupo
INNER JOIN academico_cargas ON car_curso=mat_grado AND car_grupo=mat_grupo AND car_director_grupo=1
INNER JOIN usuarios ON uss_id=car_docente
WHERE mat_eliminado=0 $filtro 
GROUP BY mat_id
ORDER BY mat_grupo, mat_primer_apellido");

while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {

    $gradoActual = $matriculadosDatos['mat_grado'];
    $grupoActual = $matriculadosDatos['mat_grupo'];

    //contadores
    $contador_periodos = 0;
    $contador_indicadores = 0;
    $materiasPerdidas = 0;
    if ($matriculadosDatos[0] == "") { ?>
        <script type="text/javascript">
            window.close();
        </script>
    <?php
        exit();
    }
    $contp = 1;
    $puestoCurso = 0;
    $puestos = mysqli_query($conexion, "SELECT mat_id, bol_estudiante, bol_carga, mat_nombres, mat_grado, bol_periodo, avg(bol_nota) as prom FROM academico_matriculas
INNER JOIN academico_boletin ON bol_estudiante=mat_id AND bol_periodo='" . $_GET["periodo"] . "'
WHERE  mat_grado='" . $matriculadosDatos['mat_grado'] . "' AND mat_grupo='" . $matriculadosDatos['mat_grupo'] . "' GROUP BY mat_id ORDER BY prom DESC");
    while ($puesto = mysqli_fetch_array($puestos, MYSQLI_BOTH)) {
        if ($puesto['bol_estudiante'] == $matriculadosDatos['mat_id']) {
            $puestoCurso = $contp;
        }
        $contp++;
    }

    $consultaNumMatriculados=mysqli_query($conexion, "SELECT * FROM academico_matriculas
    WHERE mat_eliminado=0 AND mat_grado='" . $matriculadosDatos['mat_grado'] . "' AND mat_grupo='" . $matriculadosDatos['mat_grupo'] . "'
    GROUP BY mat_id
    ORDER BY mat_grupo, mat_primer_apellido");
    $numMatriculados = mysqli_num_rows($consultaNumMatriculados);
    ?>
    <!doctype html>
    <html class="no-js" lang="en">

    <head>
        <title>Boletín Ellen Key</title>
        <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
        <style>
            #saltoPagina {
                PAGE-BREAK-AFTER: always;
            }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    </head>

    <body style="font-family:Arial; font-size:9px;">

        <div style="margin-bottom: 10px;">  

            <div align="center"><img src="encabezadoellen.png" width="95%"></div>

            <div>
                <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" style="font-size: 14px;">
                    <tr>
                        <td>Estudiante: <b><?= strtoupper($matriculadosDatos[3] . " " . $matriculadosDatos[4] . " " . $matriculadosDatos["mat_nombres"]); ?></b></td>
                        <td>Grado: <b><?= $matriculadosDatos["gra_nombre"] . " " . $matriculadosDatos["gru_nombre"]; ?></b></td>
                        <td>Periodo/Año: <b><?= $config["conf_periodo"] . " / " . date("Y") . ""; ?></b></td>
                    </tr>
                </table>
            </div>
        </div>

        <table width="100%" rules="all" border="1">
            <thead>
                <tr>
                    <td style="text-align: center; font-weight: bold; background-color: #00adefad; font-size: 13px;">INFORME PERIÓDICO DEL PROCESO DE DESARROLLO EDUCATIVO</td>
                </tr>
            </thead>
        </table>

        <table width="100%" rules="all" border="1">
            <thead>

                <tr style="font-weight:bold; text-align:center;">
                    <td width="20%" rowspan="2">ASIGNATURAS</td>
                    <td width="2%" rowspan="2">I.H.</td>

                    <?php for ($j = 1; $j <= $config["conf_periodo"]; $j++) { ?>
                        <td width="3%" colspan="2"><a href="<?= $_SERVER['PHP_SELF']; ?>?id=<?= $matriculadosDatos[0]; ?>&periodo=<?= $j ?>" style="color:#000; text-decoration:none;">Periodo <?= $j ?></a></td>
                    <?php } ?>
                    <td width="3%" colspan="3">Final</td>
                </tr>

                <tr style="font-weight:bold; text-align:center;">
                    <?php for ($j = 1; $j <= $config["conf_periodo"]; $j++) { ?>
                        <td width="3%">Nota</td>
                        <td width="3%">Nivel</td>
                    <?php } ?>
                    <td width="3%">Nota</td>
                    <td width="3%">Nivel</td>
                    <td width="3%">Hab</td>
                </tr>

            </thead>

            <?php
            $contador = 1;
            $conCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas
	INNER JOIN academico_materias ON mat_id=car_materia
	WHERE car_curso='" . $matriculadosDatos['mat_grado'] . "' AND car_grupo='" . $matriculadosDatos['mat_grupo'] . "'");
            while ($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)) {
                if ($contador % 2 == 1) {
                    $fondoFila = '#EAEAEA';
                } else {
                    $fondoFila = '#FFF';
                }
            ?>
                <tbody>
                    <tr style="background:<?= $fondoFila; ?>">
                        <td><?= $datosCargas['mat_nombre']; ?></td>
                        <td align="center"><?= $datosCargas['car_ih']; ?></td>
                        <?php
                        $promedioMateria = 0;
                        for ($j = 1; $j <= $config["conf_periodo"]; $j++) {

                            $consultaDatosBoletin=mysqli_query($conexion, "SELECT * FROM academico_boletin 
                            LEFT JOIN academico_notas_tipos ON notip_categoria='" . $config["conf_notas_categoria"] . "' AND bol_nota>=notip_desde AND bol_nota<=notip_hasta
                            WHERE bol_carga='" . $datosCargas['car_id'] . "' AND bol_estudiante='" . $matriculadosDatos['mat_id'] . "' AND bol_periodo='" . $j . "'");
                            $datosBoletin = mysqli_fetch_array($consultaDatosBoletin, MYSQLI_BOTH);


                            $promedioMateria += $datosBoletin['bol_nota'];

                            $notaBoletin = round($datosBoletin['bol_nota'], 1);

                            if($notaBoletin == '0'){$notaBoletin='0.0';}
                            if($notaBoletin == 1){$notaBoletin='1.0';}
                            if($notaBoletin == 2){$notaBoletin='2.0';}
                            if($notaBoletin == 3){$notaBoletin='3.0';}
                            if($notaBoletin == 4){$notaBoletin='4.0';}
                            if($notaBoletin == 5){$notaBoletin='5.0';}
                        ?>
                            <td align="center"><?= $notaBoletin; ?></td>
                            <td align="center"><img src="<?= $datosBoletin['notip_imagen']; ?>" width="15" height="15"></td>
                        <?php
                        }
                        $promedioMateria = round($promedioMateria / ($j - 1), 1);
                        $promedioMateriaFinal = $promedioMateria;
                        $consultaNivelacion=mysqli_query($conexion, "SELECT * FROM academico_nivelaciones WHERE niv_id_asg='" . $datosCargas['car_id'] . "' AND niv_cod_estudiante='" . $matriculadosDatos['mat_id'] . "'");
                        $nivelacion = mysqli_fetch_array($consultaNivelacion, MYSQLI_BOTH);

                        // SI PERDIÓ LA MATERIA A FIN DE AÑO
                        if ($promedioMateria < $config["conf_nota_minima_aprobar"]) {
                            if ($nivelacion['niv_definitiva'] >= $config["conf_nota_minima_aprobar"]) {
                                $promedioMateriaFinal = $nivelacion['niv_definitiva'];
                            } else {
                                $materiasPerdidas++;
                            }
                        }

                        $consultaPromediosMateriaEstiloNota=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
                        WHERE notip_categoria='" . $config["conf_notas_categoria"] . "' AND '" . $promedioMateriaFinal . "'>=notip_desde AND '" . $promedioMateriaFinal . "'<=notip_hasta");
                        $promediosMateriaEstiloNota = mysqli_fetch_array($consultaPromediosMateriaEstiloNota, MYSQLI_BOTH);

                            if($promedioMateriaFinal == '0'){$promedioMateriaFinal='0.0';}
                            if($promedioMateriaFinal == 1){$promedioMateriaFinal='1.0';}
                            if($promedioMateriaFinal == 2){$promedioMateriaFinal='2.0';}
                            if($promedioMateriaFinal == 3){$promedioMateriaFinal='3.0';}
                            if($promedioMateriaFinal == 4){$promedioMateriaFinal='4.0';}
                            if($promedioMateriaFinal == 5){$promedioMateriaFinal='5.0';}

                        ?>
                        <td align="center"><?= $promedioMateriaFinal; ?></td>
                        <td align="center"><img src="<?= $promediosMateriaEstiloNota['notip_imagen']; ?>" width="15" height="15"></td>
                        <td align="center">&nbsp;</td>
                    </tr>
                </tbody>
            <?php
                $contador++;
            }
            ?>
            <tfoot>
                <tr style="font-weight:bold; text-align:center;">
                    <td style="text-align:left;">PROMEDIO GENERAL</td>
                    <td>&nbsp;</td>

                    <?php
                    $promedioFinal = 0;
                    for ($j = 1; $j <= $config["conf_periodo"]; $j++) {
                        $consultaPromedioPeriodoTodos=mysqli_query($conexion, "SELECT ROUND(AVG(bol_nota), 1) as promedio FROM academico_boletin 
                        WHERE bol_estudiante='" . $matriculadosDatos['mat_id'] . "' AND bol_periodo='" . $j . "'");
                        $promediosPeriodos = mysqli_fetch_array($consultaPromedioPeriodoTodos, MYSQLI_BOTH);

                        $consultaSumaAusencias=mysqli_query($conexion, "SELECT sum(aus_ausencias) FROM academico_clases 
                        INNER JOIN academico_ausencias ON aus_id_clase=cls_id AND aus_id_estudiante='" . $matriculadosDatos['mat_id'] . "'
                        WHERE cls_periodo='" . $j . "'");
                        $sumaAusencias = mysqli_fetch_array($consultaSumaAusencias, MYSQLI_BOTH);

                        $consultaPromedioEstiloNota=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
                        WHERE notip_categoria='" . $config["conf_notas_categoria"] . "' AND '" . $promediosPeriodos['promedio'] . "'>=notip_desde AND '" . $promediosPeriodos['promedio'] . "'<=notip_hasta");
                        $promediosEstiloNota = mysqli_fetch_array($consultaPromedioEstiloNota, MYSQLI_BOTH);
                    ?>

                        <td><?= $promediosPeriodos['promedio']; ?></td>
                        <td><img src="<?= $promediosEstiloNota['notip_imagen']; ?>" width="15" height="15"></td>
                    <?php 
                        $promedioFinal +=$promediosPeriodos['promedio'];
                    } 

                        $promedioFinal = round($promedioFinal/$config["conf_periodo"],2);
                        $consultaPromedioFinalEstiloNota=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
                        WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND '".$promedioFinal."'>=notip_desde AND '".$promedioFinal."'<=notip_hasta",$conexion);
                        $promedioFinalEstiloNota = mysqli_fetch_array($consultaPromedioFinalEstiloNota, MYSQLI_BOTH);
                    ?>
                    <td><?=$promedioFinal;?></td>
                    <td><img src="<?= $promedioFinalEstiloNota['notip_imagen']; ?>" width="15" height="15"></td>
                    <td>-</td>
                </tr>
            </tfoot>
        </table>

        <table width="100%" rules="all" border="1" style=" font-size: 13px;">
            <thead>
                <tr>
                    <td style="text-align: center; font-weight: bold; background-color: #00adefad;" colspan="2">OBSERVACIONES DE CONVIVENCIA</td>
                </tr>

                <tr style="font-weight:bold; text-align:center;">
                    <td>PERIODO</td>
                    <td>OBSERVACIONES</td>
                </tr>

                <?php 
                $cndisiplina = mysqli_query($conexion, "SELECT * FROM disiplina_nota 
                WHERE dn_cod_estudiante='".$matriculadosDatos[0]."' AND dn_periodo<='".$_GET["periodo"]."'",$conexion);
                while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){
                ?>

                    <tr>
                        <td style="text-align: center;"><?=$rndisiplina["dn_periodo"];?></td>
                        <td><?=$rndisiplina["dn_observacion"];?></td>
                    </tr>

                <?php }?>

            </thead>
        </table>

        <p>&nbsp;</p>

        <table width="100%" cellspacing="2" cellpadding="2" rules="all" border="1">
            <thead>
                <tr>
                    <td style="width: 40%;">Dir. Curso <?= strtoupper($matriculadosDatos['uss_nombre']); ?></td>
                    <td style="width: 15%;"><img src="sup.png" width="10"> SUP 4.7 – 5.0 </td>
                    <td style="width: 15%;"><img src="alto.png" width="10"> ALT 4.0 – 4.6 </td>
                    <td style="width: 15%;"><img src="bas.png" width="10"> BAS 3.0 – 3.9 </td>
                    <td style="width: 15%;"><img src="bajo.png" width="10"> BAJ 1.0 – 2.9</td>
                </tr>

                <tr style="height: 70px;">
                    <td style="text-align: center;">
                    <p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
                    Firmas y Sellos Autorizados
                    </td>
                    <td colspan="4">

                        “El hogar es la primera escuela del niño. Practíquese en la casa la temperancia en todas las cosas, y apóyese al maestro que está tratando de brindar a sus hijos una verdadera educación” CDD (EGW)
                    </td>
                </tr>


            </thead>
        </table>

        <p>&nbsp;</p>
        



       <!-- <div id="saltoPagina"></div>-->

        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
            <thead>
                <tr style="font-weight:bold; text-align:center; background-color: #00adefad;">
                    <td width="30%">Asignatura</td>
                    <td width="70%">Indicadores de desempeño</td>
                </tr>
            </thead>

            <?php
            $conCargasDos = mysqli_query($conexion, "SELECT * FROM academico_cargas
	        INNER JOIN academico_materias ON mat_id=car_materia
	        WHERE car_curso='" . $gradoActual . "' AND car_grupo='" . $grupoActual . "'");
            while ($datosCargasDos = mysqli_fetch_array($conCargasDos, MYSQLI_BOTH)) {

                
            ?>
                <tbody>
                    <tr style="color:#000;">
                        <td><?= $datosCargasDos['mat_nombre']; ?><br><span style="color:#C1C1C1;"><?= $datosCargasDos['uss_nombre']; ?></span></td>
                        <td>
                        
                            <?php
                            //INDICADORES
                            $indicadores = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
		                    INNER JOIN academico_indicadores ON ind_id=ipc_indicador
		                    WHERE ipc_carga='" . $datosCargasDos['car_id'] . "' AND ipc_periodo='" . $_GET["periodo"] . "'");
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

        <p>&nbsp;</p>

        <table width="100%" cellspacing="3" cellpadding="3" rules="all" border="1">
            <thead>
                <tr>
                    <td style="text-align: center; font-weight: bold; font-size: medium;">CARRERA 13 # 15N -45 B. KENNEDY       TELEFONO 6406120</td>
                </tr>
            </thead>
        </table>

        <div id="saltoPagina"></div>
    <?php
} // FIN DE TODOS LOS MATRICULADOS
    ?>


    <script type="application/javascript">
        print();
    </script>


    </body>

    </html>