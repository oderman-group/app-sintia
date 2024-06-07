<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0225';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$modulo = 1;

?>

<!doctype html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->

<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->

<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->

<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->

<head>

    <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
    <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
    <title>SINTIA - Certificados</title>

</head>



<body style="font-family:Arial;">


    <?php
     $nombreInforme = "CERTIFICADO DE ESTUDIOS" . "<br>" . " No. 12114";
     include("head-informes.php") ?>

    <div align="left" style="margin-bottom:20px;">

        CÓDIGO DEL DANE <?= $informacion_inst["info_dane"] ?></b><br><br>

        Los suscritos Rector y Secretaria del <b><?= $informacion_inst["info_nombre"] ?></b>, establecimiento de carácter <?= $informacion_inst["info_caracter"] ?>, calendario <?= $informacion_inst["info_calendario"] ?>, con sus estudios aprobados de Primaria y Bachillerato, según Resolución <?= $informacion_inst["info_resolucion"] ?>.

    </div>



    <p align="center">C E R T I F I C A N</p>



    <?php

    $meses = array(" ","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    $horas[0] = 'CERO';
    $horas[1] = 'UNO';
    $horas[2] = 'DOS';
    $horas[3] = 'TRES';
    $horas[4] = 'CUATRO';
    $horas[5] = 'CINCO';
    $horas[6] = 'SEIS';
    $horas[7] = 'SIETE';
    $horas[8] = 'OCHO';
    $horas[9] = 'NUEVE';
    $horas[10] = 'DIEZ';


    $restaAgnos = ($_POST["hasta"] - $_POST["desde"]) + 1;

    $i = 1;

    $inicio = $_POST["desde"];

    $grados = "";

    while ($i <= $restaAgnos) {
	$estudiante = Estudiantes::obtenerDatosEstudiante($_POST["id"],$inicio);
	$nombre = Estudiantes::NombreCompletoDelEstudiante($estudiante);

	switch ($estudiante["gra_nivel"]) {
		case PREESCOLAR: 
			$educacion = "preescolar"; 
		break;

		case BASICA_PRIMARIA: 
			$educacion = "básica primaria"; 
		break;

		case BASICA_SECUNDARIA: 
			$educacion = "básica secundaria"; 
		break;

		case MEDIA: 
			$educacion = "media"; 
		break;
		
		default: 
			$educacion = "básica"; 
		break;
	}

        if ($i < $restaAgnos)

            $grados .= strtoupper($estudiante["gra_nombre"]) . ", ";

        else

            $grados .= strtoupper($estudiante["gra_nombre"]);

        $inicio++;

        $i++;
    }

    ?>



    <p>Que, <b><?=$nombre?></b> cursó en esta Institución <b><?=$grados;?></b> grado(s) de educación <?=$educacion;?>  y obtuvo las siguientes calificaciones:</p>



    <?php

    $restaAgnos = ($_POST["hasta"] - $_POST["desde"]) + 1;

    $i = 1;

    $inicio = $_POST["desde"];

    while ($i <= $restaAgnos) {
	$matricula = Estudiantes::obtenerDatosEstudiante($_POST["id"],$inicio);

    ?>


        <p align="center" style="font-weight:bold;">
            <?= strtoupper(Utilidades::getToString($matricula["gra_nombre"])); ?> GRADO DE EDUCACIÓN <?=strtoupper($educacion)." ".$inicio?><br>
            MATRÍCULA <?= strtoupper(Utilidades::getToString($matricula["mat_matricula"])); ?> FOLIO <?= strtoupper(Utilidades::getToString($matricula["mat_folio"])); ?>
        </p>




        <?php
		$consultaConfig = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".configuracion WHERE conf_id_institucion='" . $_SESSION["idInstitucion"] . "' AND conf_agno='" . $inicio . "'");
		$configAA = mysqli_fetch_array($consultaConfig, MYSQLI_BOTH);
        if ($inicio < $config['conf_agno'] && $configAA['conf_periodo'] == 5) { ?>

            <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">

                <tr style="font-weight:bold;">

                    <td>ÁREAS/ASIGNATURAS</td>

                    <td>CALIFICACIONES</td>

                    <td>HORAS</td>

                </tr>

                <?php

                //SELECCION LAS CARGAS DEL ESTUDIANTE, MATERIAS, AREAS
			    $cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
                $materiasPerdidas = 0;

				$horasT = 0;
				$periodoFinal = $config['conf_periodos_maximos'];
                while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

                    //OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES
				    $boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $_POST["id"], $inicio);

                $nota = 0;
                if(!empty($boletin['promedio'])){
                    $nota = round($boletin['promedio'],1);
                }

                if ($nota < $config[5]) {
                    $materiasPerdidas++;
                }

                if ($boletin['periodo'] < $config['conf_periodos_maximos']){
                    $periodoFinal = $boletin['periodo'];
                }

                $notaFinal=$nota;
                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                    $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota,$inicio);
                    $notaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                }

                ?>

                    <tr>

                        <td><?= $cargas["car_id"] .") -". strtoupper($cargas["mat_nombre"]); ?></td>

                        <td><?= $notaFinal; ?></td>

                        <td><?= $cargas["car_ih"] . " (" . $horas[$cargas["car_ih"]] . ")"; ?></td>

                    </tr>

                <?php
                $horasT += $cargas["car_ih"];

                }

                //MEDIA TECNICA
                if (array_key_exists(10, $_SESSION["modulos"])){
                    $consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config,$inicio,$_POST["id"]);
                    while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
                        if(!empty($datosEstudianteActualMT)){
                //SELECCION LAS CARGAS DEL ESTUDIANTE, MATERIAS, AREAS DE MT
			    $cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $inicio);
                while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

                    //OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES
				    $boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $_POST["id"], $inicio);

                $nota = 0;
                if(!empty($boletin['promedio'])){
                    $nota = round($boletin['promedio'],1);
                }

                $notaFinal=$nota;
                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                    $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota,$inicio);
                    $notaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                }

                ?>

                    <tr>

                        <td><?= strtoupper($cargas["mat_nombre"]); ?></td>

                        <td><?= $notaFinal; ?></td>

                        <td><?= $cargas["car_ih"] . " (" . $horas[$cargas["car_ih"]] . ")"; ?></td>

                    </tr>

                <?php

                }}}}

                ?>



            </table>



            <p>&nbsp;</p>

            <?php
            $nivelaciones = Calificaciones::consultarNivelacionesEstudiante($conexion, $config, $_POST["id"], $inicio);
            $numNiv = mysqli_num_rows($nivelaciones);

            if ($numNiv > 0) {

                echo "El(la) Estudiante niveló las siguientes materias:<br>";

                while ($niv = mysqli_fetch_array($nivelaciones, MYSQLI_BOTH)) {

                    echo "<b>" . strtoupper($niv["mat_nombre"]) . " (" . $niv["niv_definitiva"] . ")</b> Segun acta " . $niv["niv_acta"] . " en la fecha de " . $niv["niv_fecha_nivelacion"] . "<br>";
                }
            }

            ?>



            <?php
                if($materiasPerdidas == 0 || $numNiv >= $materiasPerdidas){
                    $msj = "<center>EL (LA) ESTUDIANTE ".$nombre." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>"; 
                } else {
                    $msj = "<center>EL (LA) ESTUDIANTE ".$nombre." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
                }

                if ($periodoFinal < $config["conf_periodos_maximos"] && $matricula["mat_estado_matricula"] == CANCELADO) {
                    $msj = "<center>EL(LA) ESTUDIANTE ".$nombre." FUE RETIRADO SIN FINALIZAR AÑO LECTIVO</center>";
                }
            ?>



            <?php if ($numNiv == 0) { ?><div align="left" style="font-weight:bold; font-style:italic; font-size:12px; margin-bottom:20px;"><?= $msj; ?></div><?php } ?>



            <!-- SI ESTÁ EN EL AÑO ACTUAL Y ESTE NO HA TERMINADO -->

        <?php } else { ?>

            <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">

                <tr style="font-weight:bold; text-align:center;">

                    <td>ÁREAS/ASIGNATURAS</td>

                    <td>HS</td>

                    <?php

                    $p = 1;

                    //PERIODOS

                    while ($p <= $config[19]) {

                        echo '<td>' . $p . 'P</td>';

                        $p++;
                    }

                    ?>

                    <td>DEF</td>

                    <td>DESEMPEÑO</td>

                </tr>

                <?php

                //SELECCION LAS CARGAS DEL ESTUDIANTE, MATERIAS, AREAS
			    $cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
                $materiasPerdidas = 0;
                $horasT = 0;
				$periodoFinal = $config['conf_periodos_maximos'];
                while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

                    //OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES
				    $boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $_POST["id"], $inicio);

                $nota = 0;
                if(!empty($boletin['promedio'])){
                    $nota = round($boletin['promedio'],1);
                }
                
				if ($nota < $config[5]) {
					$materiasPerdidas++;
				}

                if ($boletin['periodo'] < $config['conf_periodos_maximos']){
                    $periodoFinal = $boletin['periodo'];
                }

                    $desempeno = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);

                ?>

                    <tr style="text-align:center;">

                        <td style="text-align:left;"><?= strtoupper($cargas["mat_nombre"]); ?></td>

                        <td><?= $cargas["car_ih"]; ?></td>

                        <?php

                        $horasT += $cargas["car_ih"];
                        $p = 1;

                        //PERIODOS

                        while ($p <= $config[19]) {
                            $notasPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $_POST["id"], $cargas["car_id"], $inicio);

                            $notasPeriodoFinal='';
                            if(!empty($notasPeriodo['bol_nota'])){
                                $notasPeriodoFinal=$notasPeriodo['bol_nota'];
                                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                    $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasPeriodo['bol_nota'],$inicio);
                                    $notasPeriodoFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                }
                            }

                            echo '<td>' . $notasPeriodoFinal . '</td>';

                            $p++;
                        }

                        ?>

                        <td><?= $nota; ?></td>

                        <td><?= $desempeno['notip_nombre']; ?></td>

                    </tr>

                <?php

                }

                //MEDIA TECNICA
                if (array_key_exists(10, $_SESSION["modulos"])){
                    $consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config,$inicio,$_POST["id"]);
                    while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
                        if(!empty($datosEstudianteActualMT)){

                //SELECCION LAS CARGAS DEL ESTUDIANTE, MATERIAS, AREAS
			    $cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $inicio);
                while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

                    //OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES
				    $boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $_POST["id"], $inicio);

                $nota = 0;
                if(!empty($boletin['promedio'])){
                    $nota = round($boletin['promedio'],1);
                }

                    $desempeno = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);

                ?>

                    <tr style="text-align:center;">

                        <td style="text-align:left;"><?= strtoupper($cargas["mat_nombre"]); ?></td>

                        <td><?= $cargas["car_ih"]; ?></td>

                        <?php

                        $p = 1;

                        //PERIODOS

                        while ($p <= $config[19]) {
                            $notasPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $_POST["id"], $cargas["car_id"], $inicio);

                            $notasPeriodoFinal='';
                            if(!empty($notasPeriodo['bol_nota'])){
                                $notasPeriodoFinal=$notasPeriodo['bol_nota'];
                                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                    $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasPeriodo['bol_nota'],$inicio);
                                    $notasPeriodoFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                }
                            }

                            echo '<td>' . $notasPeriodoFinal . '</td>';

                            $p++;
                        }

                        ?>

                        <td><?= $nota; ?></td>

                        <td><?= $desempeno['notip_nombre']; ?></td>

                    </tr>

                <?php

                }}}}

                ?>



            </table>
            <?php
            $msj='';
            if($materiasPerdidas == 0){
                $msj = "<center>EL (LA) ESTUDIANTE ".$nombre." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>"; 
            } else {
                $msj = "<center>EL (LA) ESTUDIANTE ".$nombre." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
            }
    
            if ($periodoFinal < $config["conf_periodos_maximos"] && $matricula["mat_estado_matricula"] == CANCELADO) {
                $msj = "<center>EL(LA) ESTUDIANTE ".$nombre." FUE RETIRADO SIN FINALIZAR AÑO LECTIVO</center>";
            }
            ?>
            <div align="left" style="font-weight:bold; font-style:italic; font-size:12px; margin-bottom:20px;"><?= $msj; ?></div>



        <?php } ?>







    <?php

        $inicio++;

        $i++;
    }

    ?>





    <p>&nbsp;</p>
	<?php if (date('m') < 10) {
		$mes = substr(date('m'), 1);
	} else {
		$mes = date('m');
	} ?>
    PLAN DE ESTUDIOS: <?= $informacion_inst["info_decreto_plan_estudio"] ?>. Intensidad horaria <?= $horasT; ?> horas semanales de 55 minutos.<br>

    Se expide el presente certificado en <?= ucwords(strtolower($informacion_inst["ciu_nombre"])) ?> el <?= date("d"); ?> de <?= $meses[$mes]; ?> de <?= date("Y"); ?>.





    <p>&nbsp;</p>

    <table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">

        <tr>

            <td align="center">
                <?php
                    $rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
                    $nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
                    if(!empty($rector["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $rector['uss_firma'])){
                        echo '<img src="../files/fotos/'.$rector["uss_firma"].'" width="100"><br>';
                    }else{
                        echo '<p>&nbsp;</p>
                            <p>&nbsp;</p>';
                    }
                ?>
                <p style="height:0px;"></p>_________________________________<br>
                <?=$nombreRector?><br>
                Rector(a)
            </td>

            <td align="center">
                <?php
                    $secretaria = Usuarios::obtenerDatosUsuario($informacion_inst["info_secretaria_academica"]);
                    $nombreSecretaria = UsuariosPadre::nombreCompletoDelUsuario($secretaria);
                    if(!empty($secretaria["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $secretaria['uss_firma'])){
                        echo '<img src="../files/fotos/'.$secretaria["uss_firma"].'" width="100"><br>';
                    }else{
                        echo '<p>&nbsp;</p>
                            <p>&nbsp;</p>';
                    }
                ?>
                <p style="height:0px;"></p>_________________________________<br>
                <?=$nombreSecretaria?><br>
                Secretario(a)
            </td>

        </tr>

    </table>






    <?php 
        include("../compartido/footer-informes.php");
        include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    ?>
	<script type="application/javascript">
		print();
	</script>



</body>

</html>