<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0225';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$id="";
if(isset($_REQUEST["id"])){$id=base64_decode($_REQUEST["id"]);}
$desde="";
if(isset($_REQUEST["desde"])){$desde=base64_decode($_REQUEST["desde"]);}
$hasta="";
if(isset($_REQUEST["hasta"])){$hasta=base64_decode($_REQUEST["hasta"]);}
$estampilla="";
if(isset($_REQUEST["estampilla"])){$estampilla=base64_decode($_REQUEST["estampilla"]);}

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
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
	<title>SINTIA - Certificados</title>

</head>



<body style="font-family:Arial;">



<?php
$nombreInforme = "CERTIFICADO DE ESTUDIOS "."<br>"."No. 12114";
include("../compartido/head-informes.php") ?>

	<div align="justify" style="margin-bottom:20px; margin-top:20px;">

		CÓDIGO DEL DANE <?= $informacion_inst["info_dane"] ?></b><br><br>

		Los suscritos Rector y Secretaria del <?= $informacion_inst["info_nombre"] ?>, establecimiento de carácter <?= $informacion_inst["info_caracter"] ?>, calendario <?= $informacion_inst["info_calendario"] ?>, con sus estudios aprobados de Primaria y Bachillerato, según Resolución <?= $informacion_inst["info_resolucion"] ?>.

	</div>



	<p align="center">C E R T I F I C A N</p>



	<?php
	$meses = array(" ", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
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

	$restaAgnos = ($hasta - $desde) + 1;

	$i = 1;

	$inicio = $desde;

	$grados = "";
	while ($i <= $restaAgnos) {	
	
	$estudiante = Estudiantes::obtenerDatosEstudiante($id,$inicio);
	$nombre = Estudiantes::NombreCompletoDelEstudiante($estudiante);

	switch ($estudiante["gra_nivel"]) {
		case PREESCOLAR: 
			$educacion = "PREESCOLAR"; 
		break;

		case BASICA_PRIMARIA: 
			$educacion = "BÁSICA PRIMARIA"; 
		break;

		case BASICA_SECUNDARIA: 
			$educacion = "BÁSICA SECUNDARIA"; 
		break;

		case MEDIA: 
			$educacion = "MEDIA"; 
		break;

		default: 
			$educacion = "BÁSICA"; 
		break;
	}										

		if ($i < $restaAgnos)

			$grados .= $estudiante["gra_nombre"] . ", ";

		else

			$grados .= $estudiante["gra_nombre"];

		$inicio++;

		$i++;
	}

	?>



    <p>Que, <b><?=$nombre?></b> cursó en esta Institución <b><?=strtoupper($grados);?> GRADO DE EDUCACIÓN <?=$educacion;?></b>  y obtuvo las siguientes calificaciones:</p>



	<?php

	$restaAgnos = ($hasta - $desde) + 1;

	$i = 1;

	$inicio = $desde;

	while ($i <= $restaAgnos) {

	//SELECCIONO EL ESTUDIANTE, EL GRADO Y EL GRUPO
	$matricula = Estudiantes::obtenerDatosEstudiante($id,$inicio);

	?>
		<p align="center" style="font-weight:bold;">
			<?= strtoupper(Utilidades::getToString($matricula["gra_nombre"])); ?> GRADO DE EDUCACIÓN <?=$educacion." ".$inicio?><br>
			MATRÍCULA <?= strtoupper(Utilidades::getToString($matricula["mat_matricula"])); ?> FOLIO <?= strtoupper(Utilidades::getToString($matricula["mat_folio"])); ?>
		</p>



		<?php
		$consultaConfig = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".configuracion WHERE conf_id_institucion='" . $_SESSION["idInstitucion"] . "' AND conf_agno='" . $inicio . "'");
		$configAA = mysqli_fetch_array($consultaConfig, MYSQLI_BOTH);
		if ($inicio < $config['conf_agno'] && $configAA['conf_periodo'] == 5) { ?>

			<table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">

				<tr style="font-weight:bold; font-size:11px;">

					<td>ÁREAS/ASIGNATURAS</td>

					<td>CALIFICACIONES</td>

					<td>HORAS</td>

				</tr>

				<?php

				//SELECCION LAS CARGAS DEL ESTUDIANTE, MATERIAS, AREAS
				$cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
				$materiasPerdidas = 0;
				$horasT = 0;
				while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

					//CONSULTAMOS LAS MATERIAS DEL AREA
					$materias = Asignaturas::consultarAsignaturasArea($conexion, $config, $matricula["gra_id"], $matricula["gru_id"], $cargas["ar_id"], $inicio);

					$numMat = mysqli_num_rows($materias);

					//REPETIMOS LAS CARGAS DONDE HAYA MATERIAS DE LA MISMA AREA Y LAS METEMOS EN UNA SOLA VARIABLE

					$mate = "";

					$j = 1;

					while ($mat = mysqli_fetch_array($materias, MYSQLI_BOTH)) {
						if ($j < $numMat) $mate .= "'" . $mat[0] . "',";
						else $mate .= "'" . $mat[0] . "'";
						$j++;
					}

					//OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES DE TODAS LAS MATERIAS DE UNA MISMA AREA
					$boletin = Boletin::obtenerPromedioDiferentesCargas($config, $id, $mate, $inicio);

					$nota = round($boletin[0], 1);
					for ($n = 0; $n <= 5; $n++) {
						if ($nota == $n) $nota = $nota . ".0";
					}
					$desempenoA = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);

				?>

					<tr style="font-size:11px; font-weight:bold;">

						<td><?= strtoupper($cargas["ar_nombre"]); ?></td>

						<td><?= $nota; ?> (<?= strtoupper($desempenoA['notip_nombre']); ?>)</td>

						<td><?= $cargas["car_ih"] . " (" . $horas[$cargas["car_ih"]] . ")"; ?></td>

					</tr>

					<?php
					$horasT += $cargas["car_ih"];
					//INCLUIR LA MATERIA, LA DEFINITIVA Y LA I.H POR CADA ÁREA
					$materiasDA = Asignaturas::consultarAsignaturaDefinitivaIntensidad($conexion, $config, $matricula["gra_id"], $matricula["mat_grado"], $matricula["gru_id"], $cargas["ar_id"], $inicio);

					while ($mda = mysqli_fetch_array($materiasDA, MYSQLI_BOTH)) {
						$notaDefMateria = Boletin::traerDefinitivaBoletinCarga($config, $mda["car_id"], $id, $inicio);
						$notaDefMateria = round($notaDefMateria['promedio'],1);
						for ($n = 0; $n <= 5; $n++) {
							if ($notaDefMateria == $n) $notaDefMateria = $notaDefMateria . ".0";
						}
						if ($notaDefMateria < $config[5]) {
							$materiasPerdidas++;
						}
						$desempeno = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaDefMateria, $inicio);
						//PARA PREESCOLARES
						if ($matricula["gra_id"] >= 12 and $matricula["gra_id"] <= 15) {
							$nota = ceil($nota);
							if ($notaDefMateria == 1) $notaDefMateria = 'DEFICIENTE';
							if ($notaDefMateria == 2) $notaDefMateria = 'INSUFICIENTE';
							if ($notaDefMateria == 3) $notaDefMateria = 'ACEPTABLE';
							if ($notaDefMateria == 4) $notaDefMateria = 'SOBRESALIENTE';
							if ($notaDefMateria == 5) $notaDefMateria = 'EXCELENTE';
						}
					?>
						<tr style="font-size:11px;">
							<td><?= $mda["mat_nombre"]; ?></td>
							<td><?= $notaDefMateria; ?> <?php if ($matricula["gra_id"] < 12) { ?> (<?= strtoupper($desempeno['notip_nombre']); ?>) <?php } ?></td>
							<td><?= $mda["ipc_intensidad"] . " (" . $horas[$mda["ipc_intensidad"]] . ")"; ?></td>
						</tr>
					<?php } ?>

				<?php

				}

                //MEDIA TECNICA
                if (array_key_exists(10, $_SESSION["modulos"])){
                    $consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config,$inicio,$id);
                    while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
                        if(!empty($datosEstudianteActualMT)){

				//SELECCION LAS CARGAS DEL ESTUDIANTE, MATERIAS, AREAS
				$cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $inicio);
				while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

					//CONSULTAMOS LAS MATERIAS DEL AREA
					$materias = Asignaturas::consultarAsignaturasArea($conexion, $config, $matricula["gra_id"], $matricula["gru_id"], $cargas["ar_id"], $inicio);

					$numMat = mysqli_num_rows($materias);

					//REPETIMOS LAS CARGAS DONDE HAYA MATERIAS DE LA MISMA AREA Y LAS METEMOS EN UNA SOLA VARIABLE

					$mate = "";

					$j = 1;

					while ($mat = mysqli_fetch_array($materias, MYSQLI_BOTH)) {
						if ($j < $numMat) $mate .= "'" . $mat[0] . "',";
						else $mate .= "'" . $mat[0] . "'";
						$j++;
					}

					//OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES DE TODAS LAS MATERIAS DE UNA MISMA AREA
					$boletin = Boletin::obtenerPromedioDiferentesCargas($config, $id, $mate, $inicio);

					$nota = round($boletin[0], 1);
					for ($n = 0; $n <= 5; $n++) {
						if ($nota == $n) $nota = $nota . ".0";
					}
					$desempenoA = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);

					$desempenoA['notip_nombre'] = $nota == 0 ? "Bajo" : $desempenoA['notip_nombre'];

				?>

					<tr style="font-size:11px; font-weight:bold;">

						<td><?= strtoupper($cargas["ar_nombre"]); ?></td>

						<td><?= $nota; ?> (<?= strtoupper($desempenoA['notip_nombre']); ?>)</td>

						<td><?= $cargas["car_ih"] . " (" . $horas[$cargas["car_ih"]] . ")"; ?></td>

					</tr>

					<?php
					//INCLUIR LA MATERIA, LA DEFINITIVA Y LA I.H POR CADA ÁREA
					$materiasDA = Asignaturas::consultarAsignaturaDefinitivaIntensidad($conexion, $config, $matricula["gra_id"], $matricula["mat_grado"], $matricula["gru_id"], $cargas["ar_id"], $inicio);

					while ($mda = mysqli_fetch_array($materiasDA, MYSQLI_BOTH)) {
						$notaDefMateria = Boletin::traerDefinitivaBoletinCarga($config, $mda["car_id"], $id, $inicio);
						$notaDefMateria = round($notaDefMateria['promedio'],1);
						for ($n = 0; $n <= 5; $n++) {
							if ($notaDefMateria == $n) $notaDefMateria = $notaDefMateria . ".0";
						}
						$desempeno = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaDefMateria, $inicio);
						//PARA PREESCOLARES
						if ($matricula["gra_id"] >= 12 and $matricula["gra_id"] <= 15) {
							$nota = ceil($nota);
							if ($notaDefMateria == 1) $notaDefMateria = 'DEFICIENTE';
							if ($notaDefMateria == 2) $notaDefMateria = 'INSUFICIENTE';
							if ($notaDefMateria == 3) $notaDefMateria = 'ACEPTABLE';
							if ($notaDefMateria == 4) $notaDefMateria = 'SOBRESALIENTE';
							if ($notaDefMateria == 5) $notaDefMateria = 'EXCELENTE';
						}
					?>
						<tr style="font-size:11px;">
							<td><?= $mda["mat_nombre"]; ?></td>
							<td><?= $notaDefMateria; ?> <?php if ($matricula["gra_id"] < 12) { ?> (<?= strtoupper($desempeno['notip_nombre']); ?>) <?php } ?></td>
							<td><?= $mda["ipc_intensidad"] . " (" . $horas[$mda["ipc_intensidad"]] . ")"; ?></td>
						</tr>
					<?php } ?>

				<?php

				}}}}

				?>



			</table>



			<p>&nbsp;</p>

			<?php
			$nivelaciones = Calificaciones::consultarNivelacionesEstudiante($conexion, $config, $id, $inicio);
			$numNiv = mysqli_num_rows($nivelaciones);

			if ($numNiv > 0) {

				echo "El(la) Estudiante niveló las siguientes materias:<br>";

				while ($niv = mysqli_fetch_array($nivelaciones, MYSQLI_BOTH)) {

					echo "<b>" . strtoupper($niv["mat_nombre"]) . " (" . $niv["niv_definitiva"] . ")</b> Segun acta " . $niv["niv_acta"] . " en la fecha de " . $niv["niv_fecha_nivelacion"] . "<br>";
				}
			}

			?>

			<?php
			// SABER QUE MATERIAS TIENE PERDIDAS
			$cargasAcademicasC = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);

			$materiasPerdidas = 0;
			$vectorMP = array();
			$periodoFinal = $config['conf_periodos_maximos'];
			while ($cargasC = mysqli_fetch_array($cargasAcademicasC, MYSQLI_BOTH)) {
				//OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES
				$boletinC = Boletin::traerDefinitivaBoletinCarga($config, $cargasC["car_id"], $id, $inicio);
				$notaC = round($boletinC['promedio'], 1);
				if ($notaC < $config[5]) {
					$vectorMP[$materiasPerdidas] = $cargasC["car_id"];
					$materiasPerdidas++;
				}

				if ($boletinC['periodo'] < $config['conf_periodos_maximos']){
					$periodoFinal = $boletinC['periodo'];
				}
			}
			//FIN DE LAS MATERIAS QUE
			if ($materiasPerdidas > 0) {
				$m = 0;
				$niveladas = 0;
				while ($m < $materiasPerdidas) {
					$nMP = Calificaciones::validarMateriaNivelada($conexion, $config, $id, $vectorMP[$m], $inicio);
					$numNivMP = mysqli_num_rows($nMP);
					if ($numNivMP > 0) {
						$niveladas++;
					}
					$m++;
				}
			}
			if($materiasPerdidas == 0 || $niveladas >= $materiasPerdidas){
				$msj = "<center>EL (LA) ESTUDIANTE ".$nombre." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>"; 
			} else {
				$msj = "<center>EL (LA) ESTUDIANTE ".$nombre." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
			}
	
			if ($periodoFinal < $config["conf_periodos_maximos"] && $matricula["mat_estado_matricula"] == CANCELADO) {
				$msj = "<center>EL(LA) ESTUDIANTE ".$nombre." FUE RETIRADO SIN FINALIZAR AÑO LECTIVO</center>";
			}
			?>

			<br>
			<div align="left" style="font-weight:bold; font-style:italic; font-size:12px; margin-bottom:10px;"><?= $msj; ?></div>



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
					$boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $id, $inicio);

					$nota = round($boletin['promedio'], 1);
					
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
							$notasPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $id, $cargas["car_id"], $inicio);

                            $notasPeriodoFinal='';
                            if(!empty($notasPeriodo['bol_nota'])){
                                $notasPeriodoFinal=$notasPeriodo['bol_nota'];
                                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                    $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasPeriodo['bol_nota']);
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
                    $consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config,$inicio,$id);
                    while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
                        if(!empty($datosEstudianteActualMT)){

				//SELECCION LAS CARGAS DEL ESTUDIANTE, MATERIAS, AREAS
				$cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $inicio);
				while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {

					//OBTENEMOS EL PROMEDIO DE LAS CALIFICACIONES
					$boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $id, $inicio);

					$nota = round($boletin[0], 1);

					$desempeno = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);

				?>

					<tr style="text-align:center;">

						<td style="text-align:left;"><?= strtoupper($cargas["mat_nombre"]); ?></td>

						<td><?= $cargas["car_ih"]; ?></td>

						<?php

						$p = 1;

						//PERIODOS

						while ($p <= $config[19]) {
							$notasPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $id, $cargas["car_id"], $inicio);

                            $notasPeriodoFinal='';
                            if(!empty($notasPeriodo['bol_nota'])){
                                $notasPeriodoFinal=$notasPeriodo['bol_nota'];
                                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                    $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasPeriodo['bol_nota']);
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
	<span style="font-size:16px; text-align:justify;">
		PLAN DE ESTUDIOS: <?= $informacion_inst["info_decreto_plan_estudio"] ?>. Intensidad horaria <?= $horasT; ?> horas semanales de 55 minutos.<br><br>
		Se expide el presente certificado en <?= ucwords(strtolower($informacion_inst["ciu_nombre"])) ?> el <?= date("d"); ?> de <?= $meses[$mes]; ?> de <?= date("Y"); ?>.
	</span>





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
include("footer-informes.php");
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
<script type="application/javascript">
	print();
</script>


</body>

</html>