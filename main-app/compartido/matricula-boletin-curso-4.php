<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
    

$year=$_SESSION["bd"];
if(isset($_GET["year"])){
$year=base64_decode($_GET["year"]);
}

$modulo = 1;
if(empty($_GET["periodo"])){
	$periodoActual = 1;
}else{
	$periodoActual = base64_decode($_GET["periodo"]);
}

//$periodoActual=2;

if ($periodoActual == 1) $periodoActuales = "Primero";

if ($periodoActual == 2) $periodoActuales = "Segundo";

if ($periodoActual == 3) $periodoActuales = "Tercero";

if ($periodoActual == 4) $periodoActuales = "Final";

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

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro,$year);
Utilidades::validarInfoBoletin($matriculadosPorCurso);
while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {

    //contador materias

    $cont_periodos = 0;

    $contador_indicadores = 0;

    $materiasPerdidas = 0;

    //======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
    $usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
    $num_usr = mysqli_num_rows($usr);

    $datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
    $nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);

    if ($num_usr == 0) {

?>

        <script type="text/javascript">
            window.close();
        </script>

    <?php

        exit();
    }



    $contador_periodos = 0;

    ?>

    <!doctype html>

    <!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->

    <!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->

    <!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->

    <!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->

    <!--[if gt IE 8]><!-->

    <html class="no-js" lang="en">

    <!--<![endif]-->



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
        //CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
        $consulta_mat_area_est = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
        
        $numero_periodos = $config["conf_periodo"];
        ?>

        <div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="200"><br>
    <!-- <?=$informacion_inst["info_nombre"]?><br>
    BOLETÍN DE CALIFICACIONES<br> -->

        </div>





        <table width="100%" cellspacing="5" cellpadding="5" border="0" rules="none">

            <tr>

                <td>Documento:<br> <?=strpos($datosUsr["mat_documento"], '.') !== true && is_numeric($datosUsr["mat_documento"]) ? number_format($datosUsr["mat_documento"],0,",",".") : $datosUsr["mat_documento"];?></td>

                <td>Nombre:<br> <?=$nombre?></td>

                <td>Grado:<br> <?= $datosUsr["gra_nombre"] . " " . $datosUsr["gru_nombre"]; ?></td>

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

            <tr style="font-weight:bold; background-color:#75c3ff; border-color:#000; height:40px; color:#000; font-size:12px;">

                <td width="2%" align="center">NO</td>

                <td width="20%" align="center">AREAS/ ASIGNATURAS</td>

                <td width="2%" align="center">I.H</td>

                <td width="2%" align="center">NOTA</td>

                <td width="2%" align="center">DEF.</td>

            </tr>



            <!-- Aca ira un while con los indiracores, dentro de los cuales debera ir otro while con las notas de los indicadores-->

            <?php

            $contador = 1;

            while ($fila = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)) {
                //DIRECTOR DE GRUPO
                if($fila["car_director_grupo"]==1){
                    $idDirector=$fila["car_docente"];
                }

                $sumaValorMaterias = 0;

                if ($periodoActual == 1) {

                    $condicion = "1";

                    $condicion2 = "1";
                }

                if ($periodoActual == 2) {

                    $condicion = "1,2";

                    $condicion2 = "2";
                }

                if ($periodoActual == 3) {

                    $condicion = "1,2,3";

                    $condicion2 = "3";
                }

                if ($periodoActual == 4) {

                    $condicion = "1,2,3,4";

                    $condicion2 = "4";
                }



                //CONSULTA QUE ME TRAE EL NOMBRE Y EL PROMEDIO DEL AREA
                $consulta_notdef_area = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

                //CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
                $consulta_a_mat = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

                //CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
                $consulta_a_mat_per = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

                //CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
                $consulta_a_mat_indicadores = Boletin::obtenerIndicadoresPorMateria($datosUsr["mat_grado"], $datosUsr["mat_grupo"], $fila["ar_id"], $condicion, $matriculadosDatos['mat_id'], $condicion2, $year);

                $numIndicadores = mysqli_num_rows($consulta_a_mat_indicadores);

                $resultado_not_area = mysqli_fetch_array($consulta_notdef_area, MYSQLI_BOTH);

                $numfilas_not_area = mysqli_num_rows($consulta_notdef_area);

                if ($numfilas_not_area > 0) { 
                    $sumaValorMaterias = Asignaturas::sumarValorAsignaturasArea($conexion, $config, $matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $resultado_not_area["ar_id"], $year);
            ?>

                    <tr style="background-color: #f9dcca" style="font-size:12px;">

                        <td colspan="2" style="font-size:12px; height:25px; font-weight:bold;"><?php echo $resultado_not_area["ar_nombre"]." (".$sumaValorMaterias[0]."%)"; ?></td>

                        <td align="center" style="font-weight:bold; font-size:12px;"></td>

                        <td>&nbsp;</td>

                        <td>&nbsp;</td>

                    </tr>

                    <?php

                    $sumaNotasPorArea = 0;

                    while ($fila2 = mysqli_fetch_array($consulta_a_mat, MYSQLI_BOTH)) {

                        $contador_periodos = 0;

                        mysqli_data_seek($consulta_a_mat_per, 0);

                        $datosBoletin = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);


                        $notaFinal = Boletin::obtenerPromedioPorTodasLasCargas($matriculadosDatos['mat_id'], $fila2["car_id"], $year);


                        //Calculo
                        $sumaNotasPorArea += !empty($datosBoletin['bol_nota']) && !empty($fila2["mat_valor"]) ? $datosBoletin['bol_nota'] * ($fila2["mat_valor"] / 100) : 0;

                        $notaBoletin=$datosBoletin['bol_nota'];
                        $notaDefFinal=$notaFinal['def'];
                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                            $notaBoletin= $datosBoletin['notip_nombre'];
                            $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaFinal['def'], $year);
                            $notaDefFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                        }

                    ?>

                        <tr bgcolor="#EAEAEA" style="font-size:12px;">

                            <td align="center"><?= $contador; ?></td>

                            <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;"><?php echo "[".$fila2["mat_id"]."] ".$fila2["mat_nombre"]." (".$fila2["mat_valor"]."%)"; ?></td>

                            <td align="center" style="font-weight:bold; font-size:12px;background:#EAEAEA;"><?php echo $fila["car_ih"]; ?></td>

                            <td align="center" style="font-weight:bold; font-size:14px;background:#EAEAEA;"><?=$notaBoletin;?></td>

                            <td align="center" style="font-weight:bold; font-size:15px;background:#EAEAEA;"><?=$notaDefFinal;?></td>

                        </tr>

                        <?php

                        if ($numIndicadores > 0) {

                            mysqli_data_seek($consulta_a_mat_indicadores, 0);

                            $contador_indicadores = 0;

                            while ($fila4 = mysqli_fetch_array($consulta_a_mat_indicadores, MYSQLI_BOTH)) {

                                if ($fila4["mat_id"] == $fila2["mat_id"]) {
                                    $consultaRecuperacion = Indicadores::consultaRecuperacionIndicadorPeriodo($config, $fila4["ind_id"], $matriculadosDatos['mat_id'], $fila2["car_id"], $periodoActual, $year);
                                    $recuperacionIndicador = mysqli_fetch_array($consultaRecuperacion, MYSQLI_BOTH);

                                    $contador_indicadores++;
                                    $leyendaRI = '';
                                    if(!empty($recuperacionIndicador['rind_nota']) && $recuperacionIndicador['rind_nota']>$fila4["nota"]){
                                        $nota_indicador = round($recuperacionIndicador['rind_nota'], 1);
                                        $leyendaRI = '<br><span style="color:navy; font-size:9px;">Recuperdo.</span>';
                                    }else{
                                        $nota_indicador = round($fila4["nota"], 1);
                                    }

                                    

                                    if ($nota_indicador == 1)    $nota_indicador = "1.0";

                                    if ($nota_indicador == 2)    $nota_indicador = "2.0";

                                    if ($nota_indicador == 3)    $nota_indicador = "3.0";

                                    if ($nota_indicador == 4)    $nota_indicador = "4.0";

                                    if ($nota_indicador == 5)    $nota_indicador = "5.0";

                                    $notaIndicadorFinal=$nota_indicador;
                                    if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                        $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota_indicador, $year);
                                        $notaIndicadorFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                    }

                        ?>

                                    <tr bgcolor="#FFF" style="font-size:12px;">

                                        <td align="center">&nbsp;</td>

                                        <td style="font-size:12px; height:15px;"><?php echo $contador_indicadores . "." . $fila4["ind_nombre"]; ?></td>

                                        <td>&nbsp;</td>

                                        <td align="center" style="font-weight:bold; font-size:12px;"><?= $notaIndicadorFinal." ".$leyendaRI; ?></td>

                                        <td>&nbsp;</td>

                                    </tr>

                        <?php

                                } //fin if

                            }
                        }

                        ?>





                        <!-- observaciones de la asignatura-->
                        <?php
                        $observacion = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);

                        if (!empty($observacion['bol_observaciones_boletin'])) {

                        ?>

                            <tr>

                                <td colspan="5">

                                    <h5 align="center">Observaciones</h5>

                                    <p style="margin-left: 5px; font-size: 11px; margin-top: -10px; margin-bottom: 5px; font-style: italic;">

                                        <?= $observacion['bol_observaciones_boletin']; ?>

                                    </p>

                                </td>

                            </tr>

                        <?php } ?>

                    <?php

                        $contador++;
                    } //while fin materias

                    ?>

            <?php }
            } //while fin areas

            ?>

        </table>



        <p>&nbsp;</p>

        <?php

        $cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='" . $matriculadosDatos['mat_id'] . "' AND institucion={$config['conf_id_institucion']} AND year={$year} AND dn_periodo in(" . $condicion . ");");

        if (@mysqli_num_rows($cndisiplina) > 0) {

        ?>

            <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">

                <tr style="font-weight:bold; background:#4c9858; border-color:#036; height:40px; font-size:12px; text-align:center">

                    <td colspan="3">OBSERVACIONES DE CONVIVENCIA</td>

                </tr>

                <tr style="font-weight:bold; background:#e0e0153b; height:25px; color:#000; font-size:12px; text-align:center">

                    <td width="8%">Periodo</td>

                    <!--<td width="8%">Nota</td>-->

                    <td>Observaciones</td>

                </tr>

                <?php

                while ($rndisiplina = mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)) {
                    $desempenoND = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $rndisiplina["dn_nota"], $year);

                ?>

                    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">

                        <td><?= $rndisiplina["dn_periodo"] ?></td>

                        <!--<td><?= $desempenoND['notip_nombre'] ?></td>-->

                        <td align="left"><?= $rndisiplina["dn_observacion"] ?></td>

                    </tr>

                <?php } ?>

            </table>

        <?php } ?>

        <!--<hr align="center" width="100%">-->

        <div align="center">

            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="text-align:center; font-size:12px;">

                <tr>

                    <td style="font-weight:bold;" align="left"><?php if ($num_observaciones > 0) { ?>

                            COMPORTAMIENTO:

                        <?php } ?>

                        <b><u>

                                <!-- <?= strtoupper($r_diciplina[3]); ?> -->

                            </u></b><br>

                        <?php

                        ?></td>

                </tr>

            </table>

            <?php

            //print_r($vectorT);

            ?>

        </div>

        <!--

<div>

<table width="100%" cellspacing="0" cellpadding="0"  border="0" style="text-align:center; font-size:12px;">

  <tr>

    <td style="font-weight:bold;" align="left">

    OBSERVACIONES:_____________________________________________________________________________________________________________<br><br>

    ____________________________________________________________________________________________________________________________<br><br>

    ____________________________________________________________________________________________________________________________<br>

    </td>

  </tr>

</table>



</div>

-->




        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>   
        <!--******FIRMAS******-->   

        <table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
            <tr>
                <td align="center">
                    <?php
                        $directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
                        $nombreDirectorGrupo = UsuariosPadre::nombreCompletoDelUsuario($directorGrupo);
                        if(!empty($directorGrupo["uss_firma"])){
                            echo '<img src="../files/fotos/'.$directorGrupo["uss_firma"].'" width="100"><br>';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>';
                        }
                    ?>
                    <p style="height:0px;"></p>_________________________________<br>
                    <p>&nbsp;</p>
                    <?=$nombreDirectorGrupo?><br>
                    Director(a) de grupo
                </td>
                <td align="center">
                    <?php
                        $rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
                        $nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
                        if(!empty($rector["uss_firma"])){
                            echo '<img src="../files/fotos/'.$rector["uss_firma"].'" width="100"><br>';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>';
                        }
                    ?>
                    <p style="height:0px;"></p>_________________________________<br>
                    <p>&nbsp;</p>
                    <?=$nombreRector?><br>
                    Rector(a)
                </td>
            </tr>
        </table>





        <!--

<br>

<div align="center">

<table width="100%" cellspacing="0" cellpadding="0"  border="1" style="text-align:center; font-size:8px; background:#FFFFCC;">

  <tr style="text-transform:uppercase;">

    <td style="font-weight:bold;" align="right">ESCALA NACIONAL</td><td>Desempe&ntilde;o Superior</td><td>Desempe&ntilde;o Alto</td><td>Desempe&ntilde;o B&aacute;sico</td><td>Desempe&ntilde;o Bajo</td>

  </tr>

  

  <tr>

  	<td style="font-weight:bold;" align="right">RANGO INSTITUCIONAL</td>

  	<td>NO HAY</td><td>NO HAY</td><td>NO HAY</td><td>NO HAY</td>  

  </tr>



</table>

-->



        </div>

        <?php

        /*if ($periodoActual == 4) {

            if ($materiasPerdidas >= $config["conf_num_materias_perder_agno"])

                $msj = "<center>EL (LA) ESTUDIANTE " . strtoupper($datosUsr['mat_primer_apellido'] . " " . $datosUsr['mat_segundo_apellido'] . " " . $datosUsr["mat_nombres"]) . " NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";

            elseif ($materiasPerdidas < $config["conf_num_materias_perder_agno"] and $materiasPerdidas > 0)

                $msj = "<center>EL (LA) ESTUDIANTE " . strtoupper($datosUsr['mat_primer_apellido'] . " " . $datosUsr['mat_segundo_apellido'] . " " . $datosUsr["mat_nombres"]) . " DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";

            else

                $msj = "<center>EL (LA) ESTUDIANTE " . strtoupper($datosUsr['mat_primer_apellido'] . " " . $datosUsr['mat_segundo_apellido'] . " " . $datosUsr["mat_nombres"]) . " FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";
        }*/

        ?>

        <p align="center">

            <div style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-style:italic; font-size:12px;" align="center">

                <?= $msj; ?>

            </div>

        </p>



        <div align="center" style="font-size:10px; margin-top:5px; margin-bottom: 10px;">

            <img src="https://plataformasintia.com/images/logo.png" height="50"><br>

            ESTE DOCUMENTO FUE GENERADO POR:<br>

            SINTIA - SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL

        </div>



        <div id="saltoPagina"></div>

    <?php

} // FIN DE TODOS LOS MATRICULADOS
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

    ?>

    <script type="application/javascript">
        print();
    </script>

    </body>



    </html>