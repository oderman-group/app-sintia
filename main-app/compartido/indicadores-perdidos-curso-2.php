<?php 
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");


if (empty($_REQUEST["periodo"])) {

    $periodoActual = 1;
} else {

    $periodoActual = base64_decode($_REQUEST["periodo"]);
}


if ($periodoActual == 1) $periodoActuales = "Primero";

if ($periodoActual == 2) $periodoActuales = "Segundo";

if ($periodoActual == 3) $periodoActuales = "Tercero";

if ($periodoActual == 4) $periodoActuales = "Cuarto";

?>

    <a href="indicadores-perdidos-curso.php?curso=<?php if(!empty($_REQUEST["curso"])) echo $_REQUEST["curso"];?>&periodo=<?=base64_encode(1)?>">Periodo 1</a>&nbsp;&nbsp;
    <a href="indicadores-perdidos-curso.php?curso=<?php if(!empty($_REQUEST["curso"])) echo $_REQUEST["curso"];?>&periodo=<?=base64_encode(2)?>">Periodo 2</a>&nbsp;&nbsp;
    <a href="indicadores-perdidos-curso.php?curso=<?php if(!empty($_REQUEST["curso"])) echo $_REQUEST["curso"];?>&periodo=<?=base64_encode(3)?>">Periodo 3</a>&nbsp;&nbsp;
    <a href="indicadores-perdidos-curso.php?curso=<?php if(!empty($_REQUEST["curso"])) echo $_REQUEST["curso"];?>&periodo=<?=base64_encode(4)?>">Periodo 4</a>&nbsp;&nbsp;

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>

<?php

$filtro = "";
if (!empty($_REQUEST["id"])) {

    $filtro .= " AND mat_id='" . base64_decode($_REQUEST["id"]) . "'";
}

if (!empty($_REQUEST["curso"])) {

    $filtro .= " AND mat_grado='" . base64_decode($_REQUEST["curso"]) . "'";
}



$matriculadosPorCurso = mysqli_query($conexion,"SELECT * FROM ".BD_ACADEMICA.".academico_matriculas 

WHERE mat_eliminado=0 $filtro AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}

GROUP BY mat_id

ORDER BY mat_grupo, mat_primer_apellido");



while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {

    //contador materias

    $cont_periodos = 0;

    $contador_indicadores = 0;

    $materiasPerdidas = 0;

    //======================= DATOS DEL ESTUDIANTE MATRICULADO =========================

    $usr = mysqli_query($conexion,"SELECT * FROM ".BD_ACADEMICA.".academico_matriculas am

INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON mat_grupo=gru.gru_id AND gru.institucion={$config['conf_id_institucion']} AND gru.year={$_SESSION["bd"]}

INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON mat_grado=gra_id AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]} 
WHERE mat_id='" . $matriculadosDatos['mat_id']."' AND am.institucion={$config['conf_id_institucion']} AND am.year={$_SESSION["bd"]}");

    $num_usr = mysqli_num_rows($usr);

    $datos_usr = mysqli_fetch_array($usr, MYSQLI_BOTH);

    if ($num_usr == 0) {
        continue;
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

        <title>Indicadores perdidos</title>

        <style>
            #saltoPagina {

                PAGE-BREAK-AFTER: always;

            }
        </style>

    </head>



    <body style="font-family:Arial;">

    

        <?php

        //CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE

        $consulta_mat_area_est = mysqli_query($conexion,"SELECT ar_id, car_ih FROM ".BD_ACADEMICA.".academico_cargas car

		INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car.car_materia AND am.institucion={$config['conf_id_institucion']} AND am.year={$_SESSION["bd"]}

		INNER JOIN ".BD_ACADEMICA.".academico_areas ar ON ar.ar_id= am.mat_area AND ar.institucion={$config['conf_id_institucion']} AND ar.year={$_SESSION["bd"]}

		WHERE  car_curso='" . $datos_usr["mat_grado"] . "' AND car_grupo='" . $datos_usr["mat_grupo"] . "' AND car.institucion={$config['conf_id_institucion']} AND car.year={$_SESSION["bd"]} 
        GROUP BY ar.ar_id ORDER BY ar.ar_posicion ASC;");

        //$numero_periodos=$config["conf_periodos_maximos"];

        $numero_periodos = $config["conf_periodo"];

        ?>

        <div align="center" style="margin-bottom:20px;">

        </div>





        <table width="100%" cellspacing="5" cellpadding="5" border="0" rules="none">

            <tr>

                <td>Documento:<br> <?= number_format($datos_usr["mat_documento"], 0, ",", "."); ?></td>

                <td>Nombre:<br> <?= Estudiantes::NombreCompletoDelEstudiante($datos_usr) ?></td>

                <td>Grado:<br> <?= $datos_usr["gra_nombre"] . " " . $datos_usr["gru_nombre"]; ?></td>

                <td>Periodo:<br> <b><?= $periodoActuales . " (" . date("Y") . ")"; ?></b></td>

            </tr>

        </table>

        <p>&nbsp;</p>



        <table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">

            <tr style="font-weight:bold; background-color:#4c9858; border-color:#000; height:40px; color:#000; font-size:12px;">

                <td width="2%" align="center">NO</td>

                <td width="20%" align="center">ASIGNATURAS</td>


                <td width="2%" align="center">NOTA</td>

            </tr>



            <!-- Aca ira un while con los indiracores, dentro de los cuales debera ir otro while con las notas de los indicadores-->

            <?php

            $contador = 1;

            while ($fila = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)) {



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
                $consulta_notdef_area = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion);

                //CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
                $consulta_a_mat = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion);

                //CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
                $consulta_a_mat_per = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion);

                //CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
                $consulta_a_mat_indicadores = Boletin::obtenerIndicadoresPorMateria($datos_usr["mat_grado"], $datos_usr["mat_grupo"], $fila["ar_id"], $condicion, $matriculadosDatos['mat_id'], $condicion2);

                $numIndicadores = mysqli_num_rows($consulta_a_mat_indicadores);



                $resultado_not_area = mysqli_fetch_array($consulta_notdef_area, MYSQLI_BOTH);

                $numfilas_not_area = mysqli_num_rows($consulta_notdef_area);

                if ($numfilas_not_area > 0) {

            



                    while ($fila2 = mysqli_fetch_array($consulta_a_mat, MYSQLI_BOTH)) {

                        $contador_periodos = 0;

                        mysqli_data_seek($consulta_a_mat_per, 0);

                    ?>

                        <tr bgcolor="#EAEAEA" style="font-size:12px;">

                            <td align="center"><?= $contador; ?></td>

                            <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;"><?php echo $fila2["mat_nombre"]; ?></td>


                            <td>&nbsp;</td>

                        </tr>

                        <?php

                        if ($numIndicadores > 0) {

                            mysqli_data_seek($consulta_a_mat_indicadores, 0);

                            $contador_indicadores = 0;

                            while ($fila4 = mysqli_fetch_array($consulta_a_mat_indicadores, MYSQLI_BOTH)) {

                                if ($fila4["mat_id"] == $fila2["mat_id"]) {

                                    $recuperacionIndicador = mysqli_fetch_array(mysqli_query($conexion,"SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion WHERE rind_estudiante='".$matriculadosDatos['mat_id']."' AND rind_carga='".$fila2["car_id"]."' AND rind_periodo='".$periodoActual."' AND rind_indicador='".$fila4["ind_id"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}"), MYSQLI_BOTH);

                                    

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

                                    if($nota_indicador >= $config['conf_nota_minima_aprobar']){continue;}

                        ?>

                                    <tr bgcolor="#FFF" style="font-size:12px;">

                                        <td align="center">&nbsp;</td>

                                        <td style="font-size:12px; height:15px;"><?php echo $contador_indicadores . "." . $fila4["ind_nombre"]; ?></td>


                                        <td align="center" style="font-weight:bold; font-size:12px;"><?= $nota_indicador." ".$leyendaRI; ?></td>

                                    </tr>

                        <?php

                                } //fin if

                            }
                        }

                        ?>




                    <?php

                        $contador++;
                    } //while fin materias

                    ?>

            <?php }
            } //while fin areas

            ?>

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