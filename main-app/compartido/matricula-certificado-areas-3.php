<?php
date_default_timezone_set("America/Bogota");
include("session-compartida.php");
$idPaginaInterna = 'DT0225';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$Plataforma = new Plataforma;

// Configuraciones para manejo de archivos grandes
set_time_limit(300);
ini_set('memory_limit', '256M');

$id="";
if(isset($_REQUEST["id"])){$id=base64_decode($_REQUEST["id"]);}
$desde="";
if(isset($_REQUEST["desde"])){$desde=base64_decode($_REQUEST["desde"]);}
$hasta="";
if(isset($_REQUEST["hasta"])){$hasta=base64_decode($_REQUEST["hasta"]);}
$estampilla="";
if(isset($_REQUEST["estampilla"])){$estampilla=base64_decode($_REQUEST["estampilla"]);}

// Opción para mostrar encabezado (por defecto true, para papel membrete usar false)
$mostrarEncabezado = true;
if(isset($_REQUEST["sin_encabezado"])){
    $sinEncabezadoDecoded = base64_decode($_REQUEST["sin_encabezado"]);
    if($sinEncabezadoDecoded == "1"){
        $mostrarEncabezado = false;
    }
}

// Optimización: Cachear tipos de notas para evitar consultas repetidas
$notasCualitativasCache = [];

// Obtener nombre de la ciudad desde el código (info_ciudad ahora guarda el código)
if (!empty($informacion_inst["info_ciudad"]) && is_numeric($informacion_inst["info_ciudad"])) {
	$consultaCiudad = mysqli_query($conexion, "SELECT ciu_nombre, dep_nombre 
		FROM ".BD_ADMIN.".localidad_ciudades 
		INNER JOIN ".BD_ADMIN.".localidad_departamentos ON dep_id = ciu_departamento 
		WHERE ciu_id = " . intval($informacion_inst["info_ciudad"]) . " 
		LIMIT 1");
	if ($consultaCiudad && mysqli_num_rows($consultaCiudad) > 0) {
		$datosCiudad = mysqli_fetch_array($consultaCiudad, MYSQLI_BOTH);
		$informacion_inst["ciu_nombre"] = $datosCiudad["ciu_nombre"];
		$informacion_inst["dep_nombre"] = $datosCiudad["dep_nombre"];
	}
}

// Cargar tipos de notas para usar en determinarRango
$tiposNotas = [];
?>
<!doctype html>
<html class="no-js" lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
	<title>Certificado de Estudios - SINTIA</title>
	
	<style>
		/* ============================
		   ESTILOS GENERALES
		   ============================ */
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Arial', 'Times New Roman', serif;
			font-size: 11pt;
			line-height: 1.6;
			color: #000;
			background-color: #fff;
			padding: 20px;
		}

		.container-certificado {
			max-width: 850px;
			margin: 0 auto;
			background: white;
			padding: 30px;
		}

		/* ============================
		   BOTONES DE ACCIÓN
		   ============================ */
		.botones-accion {
			position: fixed;
			bottom: 30px;
			right: 30px;
			z-index: 1000;
			display: flex;
			flex-direction: column;
			gap: 10px;
		}

		.btn-flotante {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			padding: 10px 20px;
			border: 1px solid #999;
			border-radius: 4px;
			font-size: 13px;
			font-weight: 500;
			cursor: pointer;
			transition: all 0.2s ease;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			min-width: 140px;
			text-decoration: none;
			background: white;
			color: #333;
		}

		.btn-print {
			border-color: #2c3e50;
			color: #2c3e50;
		}

		.btn-print:hover {
			background: #2c3e50;
			color: white;
		}

		.btn-close {
			border-color: #7f8c8d;
			color: #7f8c8d;
		}

		.btn-close:hover {
			background: #7f8c8d;
			color: white;
		}

		/* ============================
		   ENCABEZADO
		   ============================ */
		.header-institucional {
			text-align: justify;
			margin: 40px 0 30px 0;
			line-height: 1.8;
			font-size: 11pt;
		}

		.texto-centrado {
			text-align: center;
			font-weight: bold;
			font-size: 14pt;
			margin: 25px 0;
			letter-spacing: 2px;
		}

		.texto-estudiante {
			text-align: justify;
			margin: 20px 0;
			line-height: 1.8;
			font-size: 11pt;
		}

		/* ============================
		   TABLAS
		   ============================ */
		.titulo-grado {
			text-align: left;
			font-weight: bold;
			font-size: 11pt;
			margin: 20px 0 10px 0;
		}

		.tabla-calificaciones {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
			font-size: 11pt;
		}

		.tabla-calificaciones th,
		.tabla-calificaciones td {
			border: 1px solid #000;
			padding: 8px 12px;
		}

		.tabla-calificaciones th {
			background-color: #e9ecef;
			font-weight: bold;
			text-align: center;
			font-size: 11pt;
		}

		.tabla-calificaciones td {
			vertical-align: middle;
		}

		.tabla-calificaciones tr.fila-area {
			background-color: #EAEAEA;
			font-weight: normal;
		}

		.tabla-calificaciones tr.fila-materia {
			background-color: white;
		}

		.tabla-calificaciones tfoot td {
			background-color: #f8f9fa;
			padding: 15px;
			font-size: 10pt;
			line-height: 1.8;
		}

		.tabla-calificaciones tfoot mark {
			background-color: #fff3cd;
			padding: 2px 5px;
			border-radius: 3px;
		}

		/* ============================
		   MENSAJES DE PROMOCIÓN
		   ============================ */
		.mensaje-promocion {
			text-align: center;
			font-weight: bold;
			font-style: italic;
			font-size: 11pt;
			margin: 20px 0;
			padding: 15px;
			border: 1px solid #dee2e6;
			background: #f8f9fa;
		}

		.mensaje-promovido {
			border-left: 4px solid #27ae60;
			background: #d4edda;
		}

		.mensaje-no-promovido {
			border-left: 4px solid #e74c3c;
			background: #f8d7da;
		}

		.mensaje-retirado {
			border-left: 4px solid #f39c12;
			background: #fff3cd;
		}

		/* ============================
		   SECCIÓN DE NIVELACIONES
		   ============================ */
		.seccion-nivelaciones {
			margin: 20px 0;
			padding: 15px;
			background: #fff8e1;
			border: 1px solid #ffc107;
			border-radius: 4px;
		}

		.seccion-nivelaciones p {
			margin-bottom: 8px;
			line-height: 1.6;
		}

		/* ============================
		   PIE DEL CERTIFICADO
		   ============================ */
		.pie-certificado {
			font-size: 11pt;
			text-align: justify;
			line-height: 1.8;
			margin: 25px 0;
		}

		/* ============================
		   FIRMAS
		   ============================ */
		.tabla-firmas {
			width: 100%;
			border-collapse: collapse;
			margin-top: 40px;
		}

		.tabla-firmas td {
			text-align: center;
			vertical-align: bottom;
			padding: 10px 20px;
		}

		.firma-linea {
			border-top: 1px solid #000;
			width: 60%;
			margin: 50px auto 5px auto;
		}

		.firma-nombre {
			font-weight: bold;
			font-size: 10pt;
			margin-top: 5px;
		}

		.firma-cargo {
			font-size: 9pt;
			color: #555;
		}

		/* ============================
		   ESTILOS DE IMPRESIÓN
		   ============================ */
		@media print {
			@page {
				size: letter;
				margin: 1.5cm;
			}

			body {
				background-color: white;
				padding: 0;
			}

			.container-certificado {
				padding: 0;
			}

			.botones-accion {
				display: none !important;
			}

			.tabla-calificaciones th {
				background-color: #e9ecef !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.tabla-calificaciones tr.fila-area {
				background-color: #EAEAEA !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.tabla-calificaciones tfoot td {
				background-color: #f8f9fa !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.tabla-calificaciones tfoot mark {
				background-color: #fff3cd !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-promocion {
				background: #f8f9fa !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-promovido {
				background: #d4edda !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-no-promovido {
				background: #f8d7da !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-retirado {
				background: #fff3cd !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.seccion-nivelaciones {
				background: #fff8e1 !important;
				border-color: #ffc107 !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			/* Evitar saltos de página inapropiados */
			.titulo-grado,
			.mensaje-promocion,
			.pie-certificado,
			.header-institucional {
				page-break-inside: avoid;
			}

			.tabla-calificaciones {
				page-break-inside: auto;
			}

			.tabla-calificaciones tfoot {
				page-break-inside: avoid;
			}

			.tabla-firmas {
				page-break-inside: avoid;
			}
		}
	</style>
</head>

<body>
	<!-- Botones de acción -->
	<div class="botones-accion">
		<button class="btn-flotante btn-print" onclick="window.print()">
			<span>■</span>
			<span>Imprimir</span>
		</button>
		<button class="btn-flotante btn-close" onclick="window.close()">
			<span>×</span>
			<span>Cerrar</span>
		</button>
	</div>

	<div class="container-certificado">
		<?php if($mostrarEncabezado) { ?>
			<!-- Encabezado institucional -->
			<div class="header-institucional">
				EL SUSCRITO RECTOR DE <b><?= strtoupper($informacion_inst["info_nombre"] ?? 'LA INSTITUCIÓN') ?></b> DEL MUNICIPIO DE <?= !empty($informacion_inst["ciu_nombre"]) ? strtoupper($informacion_inst["ciu_nombre"]) : 'N/A' ?>, CON
				RECONOCIMIENTO OFICIAL SEGÚN RESOLUCIÓN <?= strtoupper($informacion_inst["info_resolucion"] ?? 'N/A') ?>, EMANADA DE LA SECRETARÍA
				DE EDUCACIÓN DEPARTAMENTAL DE <?= strtoupper($informacion_inst["dep_nombre"] ?? 'N/A') ?>, CON DANE <?= $informacion_inst["info_dane"] ?? 'N/A' ?> Y NIT <?= $informacion_inst["info_nit"] ?? 'N/A' ?>, CELULAR <?= $informacion_inst["info_telefono"] ?? 'N/A' ?>.
			</div>
		<?php } ?>

		<p class="texto-centrado" <?= !$mostrarEncabezado ? 'style="margin-top: 40px;"' : ''; ?>><b>C E R T I F I C A</b></p>

		<?php
		$meses = array(" ", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		$horas = array('CERO', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ');
		
		// Obtener datos del estudiante del año actual (donde sabemos que existe) para información general
		$estudianteActual = Estudiantes::obtenerDatosEstudiante($id, $config['conf_agno']);
		if (empty($estudianteActual) || !is_array($estudianteActual)) {
			// Si no existe en el año actual, intentar obtener del último año disponible
			$estudianteActual = Estudiantes::obtenerDatosEstudiante($id, $hasta);
		}
		
		// Obtener nombre desde el año actual
		$nombre = "";
		if (!empty($estudianteActual) && is_array($estudianteActual)) {
			$nombre = Estudiantes::NombreCompletoDelEstudiante($estudianteActual);
		}
		
		$restaAgnos = ($hasta - $desde) + 1;
		$i = 1;
		$inicio = $desde;

		while ($i <= $restaAgnos) {
			// Optimización: Obtener datos del estudiante
			$matricula = Estudiantes::obtenerDatosEstudiante($id, $inicio);
			
			// Validar que el estudiante exista
			if (empty($matricula) || !is_array($matricula)) {
				$i++;
				$inicio++;
				continue;
			}
			
			// Cargar tipos de notas para este año si aún no se han cargado
			if(empty($tiposNotas)){
				$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $inicio);
				if($cosnultaTiposNotas){
					while ($row = $cosnultaTiposNotas->fetch_assoc()) {
						$tiposNotas[] = $row;
					}
				}
			}
			$gradoActual = $matricula['mat_grado'];
			$grupoActual = $matricula['mat_grupo'];

			// Determinar tipo de educación
			switch ($matricula["gra_nivel"]) {
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
		?>
			<div class="texto-estudiante">
				Que <b><?= $nombre ?></b>, identificado con documento número <?= strtoupper($matricula["mat_documento"] ?? 'N/A'); ?>, cursó y aprobó, en esta
				Institución Educativa, el grado <b><?= strtoupper($matricula["gra_nombre"]); ?></b> en año lectivo <?= $inicio; ?> de Educación <?= $educacion?> en la sede PRINCIPAL, con intensidad horaria de acuerdo al <?= $informacion_inst["info_decreto_plan_estudio"] ?? 'decreto vigente' ?>.
			</div>

			<div class="titulo-grado">
				<?= strtoupper($matricula["gra_nombre"]); ?> <?= $inicio; ?>
			</div>

			<table class="tabla-calificaciones">
				<thead>
					<tr>
						<th style="width: 55%; text-align: left;">ASIGNATURAS</th>
						<th style="width: 10%;">I.H</th>
						<th style="width: 15%;">DEFINITIVA</th>
						<th style="width: 20%;">NIVEL DE DESEMPEÑO</th>
					</tr>
				</thead>
				<tbody>
					<?php
					// Optimización: Obtener todas las asignaturas del curso
					$consultaAreas = Asignaturas::consultarAsignaturasCurso($conexion, $config, $gradoActual, $grupoActual, $inicio);
					$numAreas = mysqli_num_rows($consultaAreas);
					$sumaPromedioGeneral = 0;
					$materiasPerdidas = 0;

					while($datosAreas = mysqli_fetch_array($consultaAreas, MYSQLI_BOTH)){
						// Consultar materias del área
						$consultaMaterias = CargaAcademica::consultaMaterias($config, $config["conf_periodos_maximos"], $matricula['mat_id'], $datosAreas['car_curso'], $datosAreas['car_grupo'], $datosAreas['ar_id'], $inicio);
						
						// Calcular promedio del área usando calcularPromedioAreaCompleto (considera ponderado/simple)
						$periodosArray = [];
						$periodosMaximos = !empty($config['conf_periodos_maximos']) ? (int)$config['conf_periodos_maximos'] : 4;
						for($p = 1; $p <= $periodosMaximos; $p++){
							$periodosArray[] = $p;
						}
						$promedioAreaCompleto = Boletin::calcularPromedioAreaCompleto($config, $matricula['mat_id'], $datosAreas['ar_id'], $periodosArray, $datosAreas['car_curso'], $datosAreas['car_grupo'], $inicio);
						$notaAreaAcumulada = $promedioAreaCompleto['acumulado'];
						
						$notaArea = 0;
						$notaAreasPeriodos = 0;

						while($datosMaterias = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH)){
							// Director de grupo
							if($datosMaterias["car_director_grupo"]==1){
								$idDirector=$datosMaterias["car_docente"];
							}

							$background = '';
							$ih = $datosMaterias["car_ih"];
							
							// Si hay múltiples materias en el área, mostrarlas
							if($datosAreas['numMaterias'] > 1){
								$notaMateriasPeriodosTotal = 0;
								$ultimoPeriodo = $config["conf_periodos_maximos"];
								
								// Calcular promedio de la materia por periodos
								for($p = 1; $p <= $config["conf_periodos_maximos"]; $p++){
									$datosPeriodos = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $matricula['mat_id'], $datosMaterias["car_id"], $inicio);
									$notaMateriasPeriodos = !empty($datosPeriodos['bol_nota']) ? round($datosPeriodos['bol_nota'], 1) : 0;
									$notaMateriasPeriodosTotal += $notaMateriasPeriodos;

									if (empty($datosPeriodos['bol_periodo'])){
										$ultimoPeriodo -= 1;
									}
								}

								// Promedio acumulado de la materia
								$notaAcomuladoMateria = 0;
								if ($ultimoPeriodo > 0) {
									$notaAcomuladoMateria = $notaMateriasPeriodosTotal / $ultimoPeriodo;
								}
								
								// Obtener desempeño correcto usando obtenerDatosTipoDeNotas (usar valor numérico)
								$notaAcomuladoMateriaNum = (float)$notaAcomuladoMateria;
								$notaAcomuladoMateriaFormateada = Boletin::notaDecimales($notaAcomuladoMateriaNum);
								$cacheKey = $config['conf_notas_categoria'] . '_' . $notaAcomuladoMateriaNum . '_' . $inicio;
								if (!isset($notasCualitativasCache[$cacheKey])) {
									$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAcomuladoMateriaNum, $inicio);
								}
								$estiloNotaAcomuladoMaterias = $notasCualitativasCache[$cacheKey];
								
								// Validar que no sea null y tenga el campo notip_nombre
								if(empty($estiloNotaAcomuladoMaterias) || !is_array($estiloNotaAcomuladoMaterias)){
									$estiloNotaAcomuladoMaterias = ['notip_nombre' => ''];
								}
								if(empty($estiloNotaAcomuladoMaterias['notip_nombre'])){
									// Si no hay desempeño, usar determinarRango como fallback
									if(!empty($tiposNotas)){
										$estiloNotaAcomuladoMaterias = Boletin::determinarRango($notaAcomuladoMateriaNum, $tiposNotas);
									} else {
										$estiloNotaAcomuladoMaterias = ['notip_nombre' => 'N/A'];
									}
								}
								
							?>
								<tr class="fila-materia">
									<td style="padding-left: 25px;">
										<?=$datosMaterias['mat_nombre']?>
										<?php 
										// Mostrar porcentaje solo si el usuario es DEVELOPER
										if($datosUsuarioActual['uss_tipo'] == TIPO_DEV && !empty($datosMaterias['mat_valor'])){
											echo ' (' . $datosMaterias['mat_valor'] . '%)';
										}
										?>
									</td>
									<td style="text-align: center;"><?=$datosMaterias['car_ih']?></td>
									<td style="text-align: center;"><?=$notaAcomuladoMateriaFormateada?></td>
									<td style="text-align: center;"><?=!empty($estiloNotaAcomuladoMaterias['notip_nombre']) ? strtoupper($estiloNotaAcomuladoMaterias['notip_nombre']) : 'N/A'?></td>
								</tr>
							<?php
								$ih = "";
								$ausencia = "";
								$background = 'class="fila-area"';
							}

							// Nota para las áreas
							if(!empty($datosMaterias['notaArea'])) {
								$notaArea += round($datosMaterias['notaArea'], 1);
							}
						}
					?>
						<!-- Fila del área -->
						<tr <?=$background?>>
							<td><?=$datosAreas['ar_nombre']?></td>
							<td style="text-align: center;"><?=$ih?></td>
							<?php
								// Usar el promedio calculado con calcularPromedioAreaCompleto (ya considera ponderado/simple)
								$notaAcomuladoArea = $notaAreaAcumulada;
								
								// Obtener desempeño correcto usando obtenerDatosTipoDeNotas (usar valor numérico)
								$notaAcomuladoAreaNum = (float)$notaAcomuladoArea;
								$notaAcomuladoAreaFormateada = Boletin::notaDecimales($notaAcomuladoAreaNum);
								$cacheKey = $config['conf_notas_categoria'] . '_' . $notaAcomuladoAreaNum . '_' . $inicio;
								if (!isset($notasCualitativasCache[$cacheKey])) {
									$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAcomuladoAreaNum, $inicio);
								}
								$estiloNotaAcomuladoAreas = $notasCualitativasCache[$cacheKey];
								
								// Validar que no sea null y tenga el campo notip_nombre
								if(empty($estiloNotaAcomuladoAreas) || !is_array($estiloNotaAcomuladoAreas)){
									$estiloNotaAcomuladoAreas = ['notip_nombre' => ''];
								}
								if(empty($estiloNotaAcomuladoAreas['notip_nombre'])){
									// Si no hay desempeño, usar determinarRango como fallback
									if(!empty($tiposNotas)){
										$estiloNotaAcomuladoAreas = Boletin::determinarRango($notaAcomuladoAreaNum, $tiposNotas);
									} else {
										$estiloNotaAcomuladoAreas = ['notip_nombre' => 'N/A'];
									}
								}

								if($notaAcomuladoAreaNum < $config['conf_nota_minima_aprobar']){
									$materiasPerdidas++;
								}
							?>
							<td style="text-align: center;"><?=$notaAcomuladoAreaFormateada?></td>
							<td style="text-align: center;"><?=!empty($estiloNotaAcomuladoAreas['notip_nombre']) ? strtoupper($estiloNotaAcomuladoAreas['notip_nombre']) : 'N/A'?></td>
						</tr>
					<?php
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4" style="text-align: center;">
							<mark>
								<?php
								// Optimización: Obtener estilos de notas una sola vez
								$consultaEstiloNota = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $inicio);
								$numEstiloNota = mysqli_num_rows($consultaEstiloNota);
								$estilosTexto = [];
								
								while($estiloNota = mysqli_fetch_array($consultaEstiloNota, MYSQLI_BOTH)){
									$estilosTexto[] = strtoupper($estiloNota['notip_nombre']) . ": " . $estiloNota['notip_desde'] . " - " . $estiloNota['notip_hasta'];
								}
								echo implode(" / ", $estilosTexto);
								
								echo "<br>";

								// Porcentajes de áreas
								$consultaMaterias = CargaAcademica::consultaMateriasAreas($config, $gradoActual, $grupoActual, $inicio);
								$numMaterias = mysqli_num_rows($consultaMaterias);
								$areaAnterior = null;
								$valorAreas = "PORCENTAJES ÁREAS:";
								
								while($datosArea = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH)){
									$diagonal = " ";
									
									if(!is_null($areaAnterior) && $areaAnterior != $datosArea['mat_area']){
										$diagonal = " // ";
									}
									
									$areaAnterior = $datosArea['mat_area'];
									$valorAreas .= $diagonal . strtoupper($datosArea['mat_nombre']) . " (" . $datosArea['mat_valor'] . ")";
								}
								echo $valorAreas;
								?>
							</mark>
						</td>
					</tr>
				</tfoot>
			</table>

			<?php
			// Nivelaciones
			$nivelaciones = Calificaciones::consultarNivelacionesEstudiante($conexion, $config, $id, $inicio);
			$numNiv = mysqli_num_rows($nivelaciones);

			if ($numNiv > 0) {
			?>
				<div class="seccion-nivelaciones">
					<p style="font-weight: bold; margin-bottom: 10px;">El(la) Estudiante niveló las siguientes materias:</p>
					<?php while ($niv = mysqli_fetch_array($nivelaciones, MYSQLI_BOTH)) { ?>
						<p>
							<b><?= strtoupper($niv["mat_nombre"]) ?> (<?= $niv["niv_definitiva"] ?>)</b> 
							Según acta <?= $niv["niv_acta"] ?> en la fecha de <?= $niv["niv_fecha_nivelacion"] ?>
						</p>
					<?php } ?>
				</div>
			<?php
			}

			// Determinar promoción
			$cargasAcademicasC = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
			$materiasPerdidas = 0;
			$vectorMP = array();
			$periodoFinal = $config['conf_periodos_maximos'];
			
			while ($cargasC = mysqli_fetch_array($cargasAcademicasC, MYSQLI_BOTH)) {
				$boletinC = Boletin::traerDefinitivaBoletinCarga($config, $cargasC["car_id"], $id, $inicio);
				$notaC = !empty($boletinC['promedio']) ? round($boletinC['promedio'], 1) : 0;
				
				if ($notaC < $config[5]) {
					$vectorMP[$materiasPerdidas] = $cargasC["car_id"];
					$materiasPerdidas++;
				}

				if ($boletinC['periodo'] < $config['conf_periodos_maximos']){
					$periodoFinal = $boletinC['periodo'];
				}
			}

			// Verificar nivelaciones
			$niveladas = 0;
			if ($materiasPerdidas > 0) {
				for ($m = 0; $m < $materiasPerdidas; $m++) {
					$nMP = Calificaciones::validarMateriaNivelada($conexion, $config, $id, $vectorMP[$m], $inicio);
					if (mysqli_num_rows($nMP) > 0) {
						$niveladas++;
					}
				}
			}

			// Verificar si hay notas en el último periodo configurado
			$tieneNotasUltimoPeriodo = false;
			$ultimoPeriodo = $config["conf_periodos_maximos"];
			$cargasParaVerificar = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
			while ($cargaVerificar = mysqli_fetch_array($cargasParaVerificar, MYSQLI_BOTH)) {
				$notaUltimoPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $ultimoPeriodo, $id, $cargaVerificar["car_id"], $inicio);
				if (!empty($notaUltimoPeriodo['bol_nota'])) {
					$tieneNotasUltimoPeriodo = true;
					break;
				}
			}

			// Mensaje de promoción (solo si hay notas en el último periodo)
			if ($tieneNotasUltimoPeriodo) {
				$claseMensaje = 'mensaje-promocion';
				if($materiasPerdidas == 0 || $niveladas >= $materiasPerdidas){
					$msj = "EL (LA) ESTUDIANTE " . $nombre . " FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
					$claseMensaje .= ' mensaje-promovido';
				} else {
					$msj = "EL (LA) ESTUDIANTE " . $nombre . " NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
					$claseMensaje .= ' mensaje-no-promovido';
				}

				if ($periodoFinal < $config["conf_periodos_maximos"] && $matricula["mat_estado_matricula"] == CANCELADO) {
					$msj = "EL(LA) ESTUDIANTE " . $nombre . " FUE RETIRADO SIN FINALIZAR AÑO LECTIVO";
					$claseMensaje = 'mensaje-promocion mensaje-retirado';
				}
				?>
				<div class="<?= $claseMensaje; ?>"><?= $msj; ?></div>
			<?php } ?>

		<?php
			$inicio++;
			$i++;
		}
		?>

		<!-- PIE DEL CERTIFICADO -->
		<?php if (date('m') < 10) {
			$mes = substr(date('m'), 1);
		} else {
			$mes = date('m');
		} ?>
		
		<div class="pie-certificado">
			Se expide en <?= !empty($informacion_inst["ciu_nombre"]) ? ucwords(strtolower($informacion_inst["ciu_nombre"])) : 'la ciudad'; ?> el <?= date("d"); ?> de <?= $meses[$mes]; ?> de <?= date("Y"); ?>, con destino al
			interesado. <?php if ($config['conf_estampilla_certificados'] == SI) { echo "Se anula estampilla número <mark style='background: #fff3cd; padding: 2px 5px;'>".$estampilla."</mark>, según ordenanza 012/05 y decreto 005/06."; } ?>
		</div>

		<!-- FIRMA -->
		<table class="tabla-firmas">
			<tr>
				<td style="width: 100%;">
					<?php
					$nombreRector = 'RECTOR(A)';
					if (!empty($informacion_inst["info_rector"])) {
						$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
						if (!empty($rector)) {
							$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
							if(!empty($rector["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $rector['uss_firma'])){
								echo '<img class="firma-imagen" src="../files/fotos/'.$rector["uss_firma"].'" alt="Firma Rector" style="max-width: 100px; height: auto; margin-bottom: 10px;">';
							}
						}
					}
					?>
					<div class="firma-linea"></div>
					<div class="firma-nombre"><?= strtoupper($nombreRector) ?></div>
					<div class="firma-cargo">Rector(a)</div>
				</td>
			</tr>
		</table>
	</div>

	<?php 
	include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
	?>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Atajo de teclado para imprimir
			document.addEventListener('keydown', function(e) {
				if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
					e.preventDefault();
					window.print();
				}
			});
		});
	</script>
</body>
</html>
