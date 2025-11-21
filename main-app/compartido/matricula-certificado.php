<?php
// Configuraciones para manejo de reportes grandes
set_time_limit(300);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

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
require_once(ROOT_PATH."/main-app/class/Plataforma.php");

$modulo = 1;

$Plataforma = new Plataforma();

// Cache para notas cualitativas (optimización)
$notasCualitativasCache = [];
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
    
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container-certificado {
            max-width: 21cm;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-certificado {
            margin-bottom: 20px;
            text-align: justify;
            font-size: 11px;
            line-height: 1.5;
        }
        .texto-centrado {
            text-align: center;
            font-weight: bold;
            margin: 20px 0 15px;
            font-size: 14px;
            letter-spacing: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table td, table th {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        .mensaje-promocion {
            font-weight: bold;
            font-style: italic;
            font-size: 12px;
            margin: 15px 0;
            text-align: left;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #333;
        }
        .tabla-firmas {
            margin-top: 60px;
            border: none;
            width: 100%;
        }
        .tabla-firmas td {
            border: none;
            text-align: center;
            vertical-align: top;
            width: 50%;
            padding: 20px;
        }
        .firma-linea {
            border-top: 1px solid #000;
            width: 70%;
            margin: 0 auto;
            padding-top: 5px;
        }
        .firma-nombre {
            font-weight: bold;
            font-size: 11px;
            margin-top: 5px;
        }
        .firma-cargo {
            font-size: 10px;
            margin-top: 3px;
            color: #666;
        }
        
        /* Botones flotantes */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }
        .btn-print, .btn-close {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .btn-print {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
        }
        .btn-close {
            background: #f44336;
            color: white;
        }
        .btn-close:hover {
            background: #da190b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(244, 67, 54, 0.4);
        }
        
        @media print {
            body {
                margin: 1.5cm;
                background-color: white;
                padding: 0;
            }
            .container-certificado {
                max-width: 100%;
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: letter;
                margin: 1.5cm;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
    </style>

</head>



<body style="font-family:Arial;">

    <!-- Botones de Acción -->
    <div class="no-print">
        <button class="btn-print" onclick="window.print();">
            <i class="fa fa-print"></i> Imprimir
        </button>
        <button class="btn-close" onclick="window.close();">
            <i class="fa fa-times"></i> Cerrar
        </button>
    </div>

    <div class="container-certificado">

    <?php
     $nombreInforme = "CERTIFICADO DE ESTUDIOS" . "<br>" . " No. 12114";
     include("head-informes.php") ?>

    <div class="header-certificado">

        CÓDIGO DEL DANE <?= !empty($informacion_inst["info_dane"]) ? htmlspecialchars($informacion_inst["info_dane"]) : 'N/A'; ?></b><br><br>

        Los suscritos Rector y Secretaria del <b><?= !empty($informacion_inst["info_nombre"]) ? htmlspecialchars($informacion_inst["info_nombre"]) : 'Institución Educativa'; ?></b>, establecimiento de carácter <?= !empty($informacion_inst["info_caracter"]) ? htmlspecialchars($informacion_inst["info_caracter"]) : 'público'; ?>, calendario <?= !empty($informacion_inst["info_calendario"]) ? htmlspecialchars($informacion_inst["info_calendario"]) : 'A'; ?>, con sus estudios aprobados de Primaria y Bachillerato, según Resolución <?= !empty($informacion_inst["info_resolucion"]) ? htmlspecialchars($informacion_inst["info_resolucion"]) : 'N/A'; ?>.

    </div>



    <p class="texto-centrado">C E R T I F I C A N</p>



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

    // Validar parámetros requeridos
    if (!isset($_POST["id"]) || !isset($_POST["desde"]) || !isset($_POST["hasta"])) {
        echo '<div style="text-align: center; padding: 50px; font-family: Arial;">
            <h2>Error: Parámetros incompletos</h2>
            <p>No se recibieron todos los parámetros necesarios para generar el certificado.</p>
            <button onclick="window.close()">Cerrar</button>
        </div>';
        exit();
    }

    $restaAgnos = ($_POST["hasta"] - $_POST["desde"]) + 1;

    $i = 1;

    $inicio = $_POST["desde"];

    $grados = "";

	// Obtener datos del estudiante del año actual (donde sabemos que existe) para información general
	$estudianteActual = Estudiantes::obtenerDatosEstudiante($_POST["id"], $config['conf_agno']);
	if (empty($estudianteActual) || !is_array($estudianteActual)) {
		// Si no existe en el año actual, intentar obtener del último año disponible
		$estudianteActual = Estudiantes::obtenerDatosEstudiante($_POST["id"], $_POST["hasta"]);
	}
	
	// Obtener nombre y tipo de educación desde el año actual
	$nombre = "";
	$educacion = "básica";
	if (!empty($estudianteActual) && is_array($estudianteActual)) {
		$nombre = Estudiantes::NombreCompletoDelEstudiante($estudianteActual);
		
		switch (!empty($estudianteActual["gra_nivel"]) ? $estudianteActual["gra_nivel"] : '') {
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
	}

    while ($i <= $restaAgnos) {
	$estudiante = Estudiantes::obtenerDatosEstudiante($_POST["id"],$inicio);
	
	// Validar que el estudiante exista en este año
	if (empty($estudiante) || !is_array($estudiante)) {
		?>
		<div style="padding: 15px; margin: 20px 0; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
			<strong>Nota:</strong> El estudiante no tiene registro en el año <?= $inicio; ?>. Se omite este año y se continúa con el siguiente.
		</div>
		<?php
		$inicio++;
		$i++;
		continue;
	}

	// El tipo de educación ya se obtuvo del año actual, no es necesario recalcularlo

        if ($i < $restaAgnos)

            $grados .= strtoupper(!empty($estudiante["gra_nombre"]) ? $estudiante["gra_nombre"] : 'Grado '.$i) . ", ";

        else

            $grados .= strtoupper(!empty($estudiante["gra_nombre"]) ? $estudiante["gra_nombre"] : 'Grado '.$i);

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
	
	// Validar que el estudiante exista
	if (empty($matricula)) {
	    echo '<div style="text-align: center; padding: 50px; font-family: Arial;">
	        <h2>Error: Estudiante no encontrado</h2>
	        <p>No se encontró la matrícula del estudiante con el ID especificado en el año '.$inicio.'</p>
	        <button onclick="window.close()">Cerrar</button>
	    </div>';
	    exit();
	}

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
			    $cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio, "");
                $materiasPerdidas = 0;

				$horasT = 0;
				$periodoFinal = $config['conf_periodos_maximos'];
                while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

                    //OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES
				    $boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $_POST["id"], $inicio);

                $nota = 0;
                if(!empty($boletin['promedio'])){
                    $nota = (float)$boletin['promedio'];
                }

                if ($nota < $config[5]) {
                    $materiasPerdidas++;
                }

                if (!empty($boletin['periodo']) && $boletin['periodo'] < $config['conf_periodos_maximos']){
                    $periodoFinal = $boletin['periodo'];
                }

                $notaFinal = $nota;
                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                    $cacheKey = $config['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
                    if (!isset($notasCualitativasCache[$cacheKey])) {
                        $notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);
                    }
                    $estiloNota = $notasCualitativasCache[$cacheKey];
                    $notaFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                } else {
                    $notaFinal = Boletin::notaDecimales($nota);
                }

                ?>

                    <tr>

                        <td><?= strtoupper($cargas["mat_nombre"]); ?></td>

                        <td style="text-align: center;"><?= $notaFinal; ?></td>

                        <td style="text-align: center;"><?= $cargas["car_ih"] . " (" . $horas[$cargas["car_ih"]] . ")"; ?></td>

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
			    $cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $inicio, "");
                while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

                    //OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES
				    $boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $_POST["id"], $inicio);

                $nota = 0;
                if(!empty($boletin['promedio'])){
                    $nota = (float)$boletin['promedio'];
                }

                $notaFinal = $nota;
                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                    $cacheKey = $config['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
                    if (!isset($notasCualitativasCache[$cacheKey])) {
                        $notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);
                    }
                    $estiloNota = $notasCualitativasCache[$cacheKey];
                    $notaFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                } else {
                    $notaFinal = Boletin::notaDecimales($nota);
                }

                ?>

                    <tr>

                        <td><?= strtoupper($cargas["mat_nombre"]); ?></td>

                        <td style="text-align: center;"><?= $notaFinal; ?></td>

                        <td style="text-align: center;"><?= $cargas["car_ih"] . " (" . $horas[$cargas["car_ih"]] . ")"; ?></td>

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
				// Verificar si hay notas en el último periodo configurado
				$tieneNotasUltimoPeriodo = false;
				$ultimoPeriodo = $config["conf_periodos_maximos"];
				$cargasParaVerificar = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
				while ($cargaVerificar = mysqli_fetch_array($cargasParaVerificar, MYSQLI_BOTH)) {
					$notaUltimoPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $ultimoPeriodo, $_POST["id"], $cargaVerificar["car_id"], $inicio);
					if (!empty($notaUltimoPeriodo['bol_nota'])) {
						$tieneNotasUltimoPeriodo = true;
						break;
					}
				}

				// Mensaje de promoción (solo si hay notas en el último periodo)
				if ($tieneNotasUltimoPeriodo) {
					if($materiasPerdidas == 0 || $numNiv >= $materiasPerdidas){
						$msj = "<center>EL (LA) ESTUDIANTE ".$nombre." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>"; 
					} else {
						$msj = "<center>EL (LA) ESTUDIANTE ".$nombre." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
					}

					if ($periodoFinal < $config["conf_periodos_maximos"] && $matricula["mat_estado_matricula"] == CANCELADO) {
						$msj = "<center>EL(LA) ESTUDIANTE ".$nombre." FUE RETIRADO SIN FINALIZAR AÑO LECTIVO</center>";
					}
					
					if ($numNiv == 0) { 
						?><div align="left" style="font-weight:bold; font-style:italic; font-size:12px; margin-bottom:20px;"><?= $msj; ?></div><?php 
					}
				}
            ?>



            <!-- SI ESTÁ EN EL AÑO ACTUAL Y ESTE NO HA TERMINADO -->

        <?php } else { ?>

            <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">

                <tr style="font-weight:bold; text-align:center;">

                    <td>ÁREAS/ASIGNATURAS</td>

                    <td style="text-align: center;">HS</td>

                    <?php

                    $p = 1;

                    //PERIODOS

                    while ($p <= $config[19]) {

                        echo '<td style="text-align: center;">' . $p . 'P</td>';

                        $p++;
                    }

                    ?>

                    <td style="text-align: center;">DEF</td>

                    <td>DESEMPEÑO</td>

                </tr>

                <?php

                //SELECCION LAS CARGAS DEL ESTUDIANTE, MATERIAS, AREAS
			    $cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio, "");
                $materiasPerdidas = 0;
                $horasT = 0;
				$periodoFinal = $config['conf_periodos_maximos'];
                while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

                    //OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES
				    $boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $_POST["id"], $inicio);

                $nota = 0;
                if(!empty($boletin['promedio'])){
                    $nota = (float)$boletin['promedio'];
                }
                $notaFormateada = Boletin::notaDecimales($nota);
                
				if ($nota < $config[5]) {
					$materiasPerdidas++;
				}

                if (!empty($boletin['periodo']) && $boletin['periodo'] < $config['conf_periodos_maximos']){
                    $periodoFinal = $boletin['periodo'];
                }

                $cacheKey = $config['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
                if (!isset($notasCualitativasCache[$cacheKey])) {
                    $notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);
                }
                $desempeno = $notasCualitativasCache[$cacheKey];

                ?>

                    <tr style="text-align:center;">

                        <td style="text-align:left;"><?= strtoupper($cargas["mat_nombre"]); ?></td>

                        <td style="text-align: center;"><?= $cargas["car_ih"]; ?></td>

                        <?php

                        $horasT += $cargas["car_ih"];
                        $p = 1;

                        //PERIODOS

                        while ($p <= $config[19]) {
                            $notasPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $_POST["id"], $cargas["car_id"], $inicio);

                            $notasPeriodoFinal = '';
                            if(!empty($notasPeriodo['bol_nota'])){
                                $notaPeriodoNum = (float)$notasPeriodo['bol_nota'];
                                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                    $cacheKey = $config['conf_notas_categoria'] . '_' . $notaPeriodoNum . '_' . $inicio;
                                    if (!isset($notasCualitativasCache[$cacheKey])) {
                                        $notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaPeriodoNum, $inicio);
                                    }
                                    $estiloNota = $notasCualitativasCache[$cacheKey];
                                    $notasPeriodoFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                } else {
                                    $notasPeriodoFinal = Boletin::notaDecimales($notaPeriodoNum);
                                }
                            }

                            echo '<td style="text-align: center;">' . htmlspecialchars($notasPeriodoFinal) . '</td>';

                            $p++;
                        }

                        ?>

                        <td style="text-align: center;"><?= $notaFormateada; ?></td>

                        <td><?= !empty($desempeno['notip_nombre']) ? htmlspecialchars($desempeno['notip_nombre']) : 'N/A'; ?></td>

                    </tr>

                <?php

                }

                //MEDIA TECNICA
                if (array_key_exists(10, $_SESSION["modulos"])){
                    $consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config,$inicio,$_POST["id"]);
                    while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
                        if(!empty($datosEstudianteActualMT)){

                //SELECCION LAS CARGAS DEL ESTUDIANTE, MATERIAS, AREAS
			    $cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $inicio, "");
                while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

                    //OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES
				    $boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $_POST["id"], $inicio);

                $nota = 0;
                if(!empty($boletin['promedio'])){
                    $nota = (float)$boletin['promedio'];
                }
                $notaFormateada = Boletin::notaDecimales($nota);

                $cacheKey = $config['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
                if (!isset($notasCualitativasCache[$cacheKey])) {
                    $notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);
                }
                $desempeno = $notasCualitativasCache[$cacheKey];

                ?>

                    <tr style="text-align:center;">

                        <td style="text-align:left;"><?= strtoupper($cargas["mat_nombre"]); ?></td>

                        <td style="text-align: center;"><?= $cargas["car_ih"]; ?></td>

                        <?php

                        $p = 1;

                        //PERIODOS

                        while ($p <= $config[19]) {
                            $notasPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $_POST["id"], $cargas["car_id"], $inicio);

                            $notasPeriodoFinal = '';
                            if(!empty($notasPeriodo['bol_nota'])){
                                $notaPeriodoNum = (float)$notasPeriodo['bol_nota'];
                                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                    $cacheKey = $config['conf_notas_categoria'] . '_' . $notaPeriodoNum . '_' . $inicio;
                                    if (!isset($notasCualitativasCache[$cacheKey])) {
                                        $notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaPeriodoNum, $inicio);
                                    }
                                    $estiloNota = $notasCualitativasCache[$cacheKey];
                                    $notasPeriodoFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                } else {
                                    $notasPeriodoFinal = Boletin::notaDecimales($notaPeriodoNum);
                                }
                            }

                            echo '<td style="text-align: center;">' . htmlspecialchars($notasPeriodoFinal) . '</td>';

                            $p++;
                        }

                        ?>

                        <td style="text-align: center;"><?= $notaFormateada; ?></td>

                        <td><?= !empty($desempeno['notip_nombre']) ? htmlspecialchars($desempeno['notip_nombre']) : 'N/A'; ?></td>

                    </tr>

                <?php

                }}}}

                ?>



            </table>
            <?php
			// Verificar si hay notas en el último periodo configurado
			$tieneNotasUltimoPeriodo = false;
			$ultimoPeriodo = $config["conf_periodos_maximos"];
			$cargasParaVerificar = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
			while ($cargaVerificar = mysqli_fetch_array($cargasParaVerificar, MYSQLI_BOTH)) {
				$notaUltimoPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $ultimoPeriodo, $_POST["id"], $cargaVerificar["car_id"], $inicio);
				if (!empty($notaUltimoPeriodo['bol_nota'])) {
					$tieneNotasUltimoPeriodo = true;
					break;
				}
			}

			// Mensaje de promoción (solo si hay notas en el último periodo)
			if ($tieneNotasUltimoPeriodo) {
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
    PLAN DE ESTUDIOS: <?= !empty($informacion_inst["info_decreto_plan_estudio"]) ? htmlspecialchars($informacion_inst["info_decreto_plan_estudio"]) : 'N/A'; ?>. Intensidad horaria <?= $horasT; ?> horas semanales de 55 minutos.<br>

    Se expide el presente certificado en <?= !empty($informacion_inst["ciu_nombre"]) ? ucwords(strtolower(htmlspecialchars($informacion_inst["ciu_nombre"]))) : 'la ciudad'; ?> el <?= date("d"); ?> de <?= $meses[$mes]; ?> de <?= date("Y"); ?>.





    <!-- Firmas -->
    <table class="tabla-firmas">
        <tr>
            <td>
                <?php
                    $rector = [];
                    $nombreRector = 'N/A';
                    if(!empty($informacion_inst["info_rector"])){
                        $rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
                        $nombreRector = !empty($rector) ? UsuariosPadre::nombreCompletoDelUsuario($rector) : 'N/A';
                    }
                    if(!empty($rector["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $rector['uss_firma'])){
                        echo '<img src="../files/fotos/'.htmlspecialchars($rector["uss_firma"]).'" width="120" style="margin-bottom: 10px;"><br>';
                    }
                ?>
                <div class="firma-linea"></div>
                <div class="firma-nombre"><?= htmlspecialchars($nombreRector); ?></div>
                <div class="firma-cargo">Rector(a)</div>
            </td>
            <td>
                <?php
                    $secretaria = [];
                    $nombreSecretaria = 'N/A';
                    if(!empty($informacion_inst["info_secretaria_academica"])){
                        $secretaria = Usuarios::obtenerDatosUsuario($informacion_inst["info_secretaria_academica"]);
                        $nombreSecretaria = !empty($secretaria) ? UsuariosPadre::nombreCompletoDelUsuario($secretaria) : 'N/A';
                    }
                    if(!empty($secretaria["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $secretaria['uss_firma'])){
                        echo '<img src="../files/fotos/'.htmlspecialchars($secretaria["uss_firma"]).'" width="120" style="margin-bottom: 10px;"><br>';
                    }
                ?>
                <div class="firma-linea"></div>
                <div class="firma-nombre"><?= htmlspecialchars($nombreSecretaria); ?></div>
                <div class="firma-cargo">Secretario(a) Académico</div>
            </td>
        </tr>
    </table>

    </div> <!-- Cierre container-certificado -->

    <?php 
        include("../compartido/footer-informes.php");
        include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    ?>
    
    <script type="text/javascript">
        // Atajo de teclado para imprimir
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });
        });
    </script>



</body>

</html>