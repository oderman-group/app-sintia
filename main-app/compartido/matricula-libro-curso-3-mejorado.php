<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0227';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}

// Configuraciones PHP para evitar timeouts
set_time_limit(300); // 5 minutos
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/compartido/overlay.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Plataforma.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");
require_once(ROOT_PATH . "/main-app/class/Calificaciones.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
$Plataforma = new Plataforma;

$year = $_SESSION["bd"];
if (isset($_POST["year"]) && !empty($_POST["year"])) {
	$year = $_POST["year"];
	// Convertir POST a GET para mantener datos en URL
	$_GET["year"] = base64_encode($year);
} elseif (isset($_GET["year"]) && !empty($_GET["year"])) {
	$year = base64_decode($_GET["year"]);
}

$periodoActual = 4;
if (isset($_POST["periodo"]) && !empty($_POST["periodo"])) {
	$periodoActual = $_POST["periodo"];
	$_GET["periodo"] = base64_encode($periodoActual);
} elseif (isset($_GET["periodo"]) && !empty($_GET["periodo"])) {
	$periodoActual = base64_decode($_GET["periodo"]);
}

switch ($periodoActual) {
	case 1:
		$periodoActuales = "Primero";
		break;
	case 2:
		$periodoActuales = "Segundo";
		break;
	case 3:
		$periodoActuales = "Tercero";
		break;
	case 4:
		$periodoActuales = "Final";
		break;
	case 5:
		$periodoActual = 4;
		$periodoActuales = "Final";
		break;
}

//CONSULTA ESTUDIANTES MATRICULADOS
$curso = '';
if (isset($_POST["curso"]) && !empty($_POST["curso"])) {
	$curso = $_POST["curso"];
	$_GET["curso"] = base64_encode($curso);
} elseif (isset($_GET["curso"]) && !empty($_GET["curso"])) {
	$curso = base64_decode($_GET["curso"]);
}

$grupo = '';
if (isset($_POST["grupo"]) && !empty($_POST["grupo"])) {
	$grupo = $_POST["grupo"];
	$_GET["grupo"] = base64_encode($grupo);
} elseif (isset($_GET["grupo"]) && !empty($_GET["grupo"])) {
	$grupo = base64_decode($_GET["grupo"]);
}

$id = '';
if (isset($_POST["id"]) && !empty($_POST["id"])) {
	$id = $_POST["id"];
	$_GET["id"] = base64_encode($id);
} elseif (isset($_GET["id"]) && !empty($_GET["id"])) {
	$id = base64_decode($_GET["id"]);
}

if (!empty($id)) {
	$filtro               = " AND mat_id='" . $id . "'";
	$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
	$estudiante           = $matriculadosPorCurso->fetch_assoc();
	if (!empty($estudiante)) {
		$idEstudiante = $estudiante["mat_id"];
		$curso        = $estudiante["mat_grado"];
		$grupo        = $estudiante["mat_grupo"];
	}
}

$periodoFinal = $config["conf_periodos_maximos"];

$tiposNotas         = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
	$tiposNotas[] = $row;
}

$listaDatos = [];
$errorCarga = null;
$estudiantes = [];

if (!empty($curso) && !empty($periodoFinal) && !empty($year)) {
	try {
		// PRIMERO: Obtener TODOS los estudiantes matriculados (con o sin calificaciones)
		$filtroEstudiantes = " AND mat_grado='$curso' AND mat_eliminado=0";
		if (!empty($grupo)) {
			$filtroEstudiantes .= " AND mat_grupo='$grupo'";
		}
		if (!empty($id)) {
			$filtroEstudiantes .= " AND mat_id='$id'";
		}
		
		$matriculados = Estudiantes::estudiantesMatriculados($filtroEstudiantes, $year);
		$numMatriculados = 0;
		
		while ($est = mysqli_fetch_array($matriculados, MYSQLI_BOTH)) {
			$numMatriculados++;
			$nombreCompleto = Estudiantes::NombreCompletoDelEstudiante($est);
			
			// Inicializar estructura del estudiante
			$estudiantes[$est['mat_id']] = [
				'mat_id' => $est['mat_id'],
				'mat_matricula' => $est['mat_matricula'],
				'mat_numero_matricula' => $est['mat_numero_matricula'],
				'mat_folio' => $est['mat_folio'],
				'mat_primer_apellido' => $est['mat_primer_apellido'],
				'mat_segundo_apellido' => $est['mat_segundo_apellido'],
				'mat_nombres' => $est['mat_nombres'],
				'mat_nombre2' => $est['mat_nombre2'],
				'nombre' => $nombreCompleto,
				'gra_id' => $est['mat_grado'],
				'gra_nombre' => $est['gra_nombre'],
				'gru_nombre' => isset($est['gru_nombre']) ? $est['gru_nombre'] : '',
				'periodos' => $periodoFinal,
				'areas' => []
			];
		}
		
		error_log("Libro Final: Se encontraron $numMatriculados estudiantes matriculados en curso=$curso, grupo=$grupo, year=$year");
		
		// SEGUNDO: Si hay estudiantes, buscar sus calificaciones
		if (count($estudiantes) > 0) {
			$periodosArray = [];
			for ($i = 1; $i <= $periodoFinal; $i++) {
				$periodosArray[$i] = $i;
			}
			
			$datos = Boletin::datosBoletin($curso, $grupo, $periodosArray, $year, $id, false);
			
			if ($datos) {
				$numFilas = 0;
				while ($row = $datos->fetch_assoc()) {
					$listaDatos[] = $row;
					$numFilas++;
				}
				error_log("Libro Final: Se obtuvieron $numFilas filas de calificaciones");
				
				// Agrupar datos de calificaciones
				if (count($listaDatos) > 0) {
					include("../compartido/agrupar-datos-boletin-periodos-mejorado.php");
					error_log("Libro Final: Después de agrupar calificaciones, hay " . (isset($estudiantes) ? count($estudiantes) : 0) . " estudiantes");
				}
			} else {
				error_log("Libro Final: No se obtuvieron calificaciones, pero se mostrarán " . count($estudiantes) . " estudiantes sin notas");
			}
		} else {
			$errorCarga = "No hay estudiantes matriculados en curso='$curso', grupo='$grupo' para el año='$year'";
			error_log("Libro Final: " . $errorCarga);
		}
		
	} catch (Exception $e) {
		$errorCarga = $e->getMessage();
		error_log("Error en libro final: " . $errorCarga);
		error_log("Stack trace: " . $e->getTraceAsString());
	}
} else {
	$errorCarga = "Parámetros incompletos: curso='$curso', grupo='$grupo', periodoFinal='$periodoFinal', year='$year'";
	error_log("Libro Final: " . $errorCarga);
}

// Generar URL para Excel
$paramsExcel = http_build_query([
	'year' => base64_encode($year),
	'curso' => base64_encode($curso),
	'grupo' => base64_encode($grupo),
	'id' => !empty($id) ? base64_encode($id) : '',
	'periodo' => base64_encode($periodoActual)
]);
$urlExcel = "libro-final-exportar-excel.php?" . $paramsExcel;

// Convertir array asociativo a indexado para el foreach
if (isset($estudiantes) && is_array($estudiantes) && count($estudiantes) > 0) {
	$estudiantes = array_values($estudiantes);
}

// Variable para controlar si hay contenido
$hayContenido = isset($estudiantes) && is_array($estudiantes) && count($estudiantes) > 0;

// Debug: Mostrar información en comentario HTML
$debugInfo = [
	'hayContenido' => $hayContenido,
	'numEstudiantes' => count($estudiantes),
	'curso' => $curso,
	'grupo' => $grupo,
	'year' => $year,
	'id' => $id,
	'periodoFinal' => $periodoFinal,
	'metodo' => $_SERVER['REQUEST_METHOD'],
	'errorCarga' => $errorCarga,
	'numListaDatos' => count($listaDatos),
	'tieneAreas' => (count($estudiantes) > 0 && isset($estudiantes[0]['areas'])) ? 'Sí ('.count($estudiantes[0]['areas']).')' : 'No',
	'POST_data' => $_POST,
	'GET_data' => $_GET,
	'URL_completa' => $_SERVER['REQUEST_URI']
];
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Libro Final - <?= $informacion_inst["info_nombre"] ?></title>
	<link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
	
	<!-- Bootstrap CSS -->
    <link href="../../config-general/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<!-- Estilos personalizados -->
	<link href="../../config-general/assets/css/libro-final-styles.css" rel="stylesheet" type="text/css" />
	
	<!-- html2pdf -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
	
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	
	<!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
	<!-- Debug Info (visible en código fuente) -->
	<!-- DEBUG INFO:
		<?= print_r($debugInfo, true) ?>
	-->
	
	<!-- Loader -->
	<div class="loader" id="loader">
		<div class="loader-spinner"></div>
	</div>

	<!-- Contenedor Principal -->
	<div id="contenedor-principal">
		<?php if (!$hayContenido) { ?>
		<div style="text-align: center; padding: 60px 20px;">
			<i class="fas fa-info-circle" style="font-size: 64px; color: #3498db; margin-bottom: 20px;"></i>
			<h3 style="color: #2c3e50; margin-bottom: 10px;">No hay datos para mostrar</h3>
			<p style="color: #7f8c8d;">No se encontraron estudiantes con los parámetros seleccionados.</p>
			
			<!-- Info de debug -->
			<div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: left; max-width: 700px; margin-left: auto; margin-right: auto;">
				<strong style="color: #e74c3c;">Información de Debug:</strong><br>
				<small>
					<strong>Año:</strong> <?= $year ?><br>
					<strong>Curso:</strong> <?= !empty($curso) ? $curso : '<span style="color: red;">VACÍO</span>' ?><br>
					<strong>Grupo:</strong> <?= !empty($grupo) ? $grupo : '<span style="color: red;">VACÍO</span>' ?><br>
					<strong>ID Estudiante:</strong> <?= !empty($id) ? $id : 'N/A' ?><br>
					<strong>Método:</strong> <?= $_SERVER['REQUEST_METHOD'] ?><br>
					<strong>URL Completa:</strong> <?= $_SERVER['REQUEST_URI'] ?><br>
					<strong>POST Data:</strong> <?= !empty($_POST) ? json_encode($_POST) : 'VACÍO' ?><br>
					<strong>GET Data:</strong> <?= !empty($_GET) ? json_encode($_GET) : 'VACÍO' ?><br>
					<strong>Filas de datos obtenidas:</strong> <?= count($listaDatos) ?><br>
					<strong>Estudiantes procesados:</strong> <?= count($estudiantes) ?><br>
					<strong>Primer estudiante tiene áreas:</strong> <?= $debugInfo['tieneAreas'] ?><br>
					<strong>Periodo Final Config:</strong> <?= $periodoFinal ?><br>
					<?php if (!empty($errorCarga)) { ?>
					<strong style="color: #e74c3c;">Error:</strong> <span style="color: #e74c3c;"><?= htmlspecialchars($errorCarga) ?></span><br>
					<?php } ?>
				</small>
			</div>
			
			<div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 8px; max-width: 700px; margin-left: auto; margin-right: auto;">
				<small>
					<strong>💡 Criterio "No hay datos":</strong><br>
					Este mensaje aparece cuando <code>count($estudiantes) = 0</code><br><br>
					<strong>Posibles causas:</strong><br>
					• Curso o Grupo vacíos (parámetros no se guardaron)<br>
					• No existen calificaciones para este curso/grupo/año<br>
					• Hubo un error al consultar la base de datos<br>
					• La página no terminó de cargar (timeout o error PHP)
				</small>
			</div>
			
			<button onclick="window.close()" class="btn btn-primary" style="margin-top: 20px;">
				<i class="fas fa-arrow-left"></i> Volver
			</button>
		</div>
		<?php } else { ?>
		<div id="contenido">
			<?php 
			$contadorEstudiantes = 0;
			foreach ($estudiantes as $estudiante) {
				$contadorEstudiantes++;
				$nombre = Estudiantes::NombreCompletoDelEstudiante($estudiante);
				$materiasPerdidas = 0;
				
				// Debug: Verificar datos del estudiante
				if (empty($nombre) || $nombre === '') {
					error_log("Libro Final: Estudiante sin nombre - ID: {$estudiante['mat_id']}, Datos: " . json_encode($estudiante));
					$nombre = "ESTUDIANTE SIN NOMBRE (ID: {$estudiante['mat_id']})";
				}
				
				// Flush para mostrar contenido progresivamente
				if ($contadorEstudiantes == 1) {
					flush();
					if (function_exists('ob_flush')) {
						@ob_flush();
					}
				}
			?>
			<div class="pagina-estudiante">
				<!-- Encabezado del Informe -->
				<?php
				$nombreInforme = "REGISTRO DE VALORACIÓN - LIBRO FINAL";
				if ($config['conf_mostrar_encabezado_informes'] == 1) {
					include("../compartido/head-informes.php");
				} else {
				?>
					<div class="encabezado-informe">
						<img class="logo-institucion" src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" alt="Logo">
						<div class="titulo-informe"><?= strtoupper($informacion_inst["info_nombre"]) ?></div>
						<div style="font-size: 14pt; font-weight: 600; color: #7f8c8d; margin-top: 10px;">
							<?= $nombreInforme ?>
						</div>
					</div>
				<?php } ?>

				<!-- Información del Estudiante -->
				<div class="info-estudiante">
					<div class="info-estudiante-grid">
						<div class="info-item">
							<div class="info-label">Código</div>
							<div class="info-valor"><?= $estudiante["mat_matricula"]; ?></div>
						</div>
						<div class="info-item">
							<div class="info-label">Nombre Completo</div>
							<div class="info-valor"><?= strtoupper($nombre) ?></div>
						</div>
						<div class="info-item">
							<div class="info-label">Matrícula</div>
							<div class="info-valor"><?= $estudiante["mat_numero_matricula"]; ?></div>
						</div>
						<div class="info-item">
							<div class="info-label">Grado y Grupo</div>
							<div class="info-valor"><?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></div>
						</div>
						<div class="info-item">
							<div class="info-label">Periodo</div>
							<div class="info-valor"><?= strtoupper($periodoActuales); ?></div>
						</div>
						<div class="info-item">
							<div class="info-label">Folio</div>
							<div class="info-valor"><?= $estudiante["mat_folio"]; ?></div>
						</div>
					</div>
				</div>

				<!-- Tabla de Calificaciones -->
				<table class="tabla-calificaciones">
					<thead>
						<tr>
							<th class="col-asignatura">ÁREAS / ASIGNATURAS</th>
							<th class="col-ih">I.H</th>
							<th class="col-definitiva">DEF</th>
							<th class="col-desempeno">DESEMPEÑO</th>
							<th class="col-ausencias">AUS</th>
							<th class="col-observaciones">OBSERVACIONES</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if (isset($estudiante["areas"]) && count($estudiante["areas"]) > 0) {
							foreach ($estudiante["areas"] as $area) { 
						?>
							<!-- Fila del Área -->
							<tr class="fila-area">
								<td><?= strtoupper($area["ar_nombre"]); ?></td>
								<td></td>
								<td>
									<?php
									$notaArea = $area["nota_area_acumulada"];
									$notaAreaClase = '';
									
									if ($estudiante["gra_id"] > 11 && $config['conf_id_institucion'] != EOA_CIRUELOS) {
										$notaFA = ceil($notaArea);
										switch ($notaFA) {
											case 1: echo "D"; $notaAreaClase = 'nota-bajo'; break;
											case 2: echo "I"; $notaAreaClase = 'nota-bajo'; break;
											case 3: echo "A"; $notaAreaClase = 'nota-basico'; break;
											case 4: echo "S"; $notaAreaClase = 'nota-alto'; break;
											case 5: echo "E"; $notaAreaClase = 'nota-superior'; break;
										}
									} else {
										$notaAreaFormateada = Boletin::formatoNota($notaArea);
										echo $notaAreaFormateada;
										
										// Determinar clase según la nota
										if ($notaArea >= 4.5) $notaAreaClase = 'nota-superior';
										elseif ($notaArea >= 4.0) $notaAreaClase = 'nota-alto';
										elseif ($notaArea >= 3.0) $notaAreaClase = 'nota-basico';
										else $notaAreaClase = 'nota-bajo';
									}
									?>
								</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>

							<!-- Filas de Materias -->
							<?php foreach ($area["cargas"] as $carga) {
								$notaCarga = $carga["nota_carga_acumulada"];
								
								if ($notaCarga < $config['conf_nota_minima_aprobar']) {
									$materiasPerdidas++;
								}
								
								$notaCargaDeseno = Boletin::determinarRango($notaCarga, $tiposNotas);
								Utilidades::valordefecto($notaCargaDeseno["notip_desde"],0);
								Utilidades::valordefecto($notaCargaDeseno["notip_hasta"],1);
								
								$notaCargaClase = '';
								
								if ($notaCarga >= $notaCargaDeseno["notip_desde"] && $notaCarga <= $notaCargaDeseno["notip_hasta"]) {
									if ($estudiante["gra_id"] > 11 && $config['conf_id_institucion'] != EOA_CIRUELOS) {
										$notaFD = ceil($notaCarga);
										switch ($notaFD) {
											case 1:
												$notaCarga = "D";
												$notaCargaDeseno["notip_nombre"] = "BAJO";
												$notaCargaClase = 'nota-bajo';
												break;
											case 2:
												$notaCarga = "I";
												$notaCargaDeseno["notip_nombre"] = "BAJO";
												$notaCargaClase = 'nota-bajo';
												break;
											case 3:
												$notaCarga = "A";
												$notaCargaDeseno["notip_nombre"] = "BÁSICO";
												$notaCargaClase = 'nota-basico';
												break;
											case 4:
												$notaCarga = "S";
												$notaCargaDeseno["notip_nombre"] = "ALTO";
												$notaCargaClase = 'nota-alto';
												break;
											case 5:
												$notaCarga = "E";
												$notaCargaDeseno["notip_nombre"] = "SUPERIOR";
												$notaCargaClase = 'nota-superior';
												break;
										}
									} else {
										// Determinar clase según la nota
										if ($notaCarga >= 4.5) $notaCargaClase = 'nota-superior';
										elseif ($notaCarga >= 4.0) $notaCargaClase = 'nota-alto';
										elseif ($notaCarga >= 3.0) $notaCargaClase = 'nota-basico';
										else $notaCargaClase = 'nota-bajo';
									}
								}
							?>
							<tr class="fila-materia">
								<td class="col-asignatura"><?= $carga["mat_nombre"]; ?></td>
								<td class="col-ih"><?= $carga["car_ih"] ?></td>
								<td class="col-definitiva <?= $notaCargaClase ?>"><?= Boletin::formatoNota(3); ?></td>
								<td class="col-desempeno"><?= $notaCargaDeseno["notip_nombre"] ?></td>
								<td class="col-ausencias"><?= $carga["fallas"] ?></td>
								<td class="col-observaciones"></td>
							</tr>
							<?php } ?>
							
							<tr class="fila-separador"><td colspan="6"></td></tr>
						<?php 
							} // Fin foreach areas
						} else { 
							// Si no hay áreas/calificaciones, mostrar mensaje
						?>
							<tr>
								<td colspan="6" style="text-align: center; padding: 30px; color: #7f8c8d; font-style: italic;">
									<i class="fas fa-info-circle"></i> No hay calificaciones registradas para este estudiante en el periodo actual.
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>

				<!-- Mensaje de Promoción -->
				<?php
				$msj = "";
				$msjClase = "";
				
				// Solo mostrar mensaje de promoción si hay datos de periodos
				if (isset($estudiante["periodos"]) && $estudiante["periodos"] == $config["conf_periodos_maximos"]) {
					if ($materiasPerdidas >= $config["conf_num_materias_perder_agno"]) {
						$msj = "EL (LA) ESTUDIANTE " . strtoupper($nombre) . " NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
						$msjClase = "mensaje-no-promovido";
					} elseif ($materiasPerdidas < $config["conf_num_materias_perder_agno"] and $materiasPerdidas > 0) {
						$msj = "EL (LA) ESTUDIANTE " . strtoupper($nombre) . " DEBE NIVELAR LAS MATERIAS PERDIDAS";
						$msjClase = "mensaje-nivelar";
					} else {
						$msj = "EL (LA) ESTUDIANTE " . strtoupper($nombre) . " FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
						$msjClase = "mensaje-promovido";
					}

					if (isset($estudiante['mat_estado_matricula']) && $estudiante['mat_estado_matricula'] == CANCELADO) {
						$msj = "EL(LA) ESTUDIANTE FUE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
						$msjClase = "mensaje-no-promovido";
					}
				} elseif (!isset($estudiante["areas"]) || count($estudiante["areas"]) == 0) {
					// Si no hay calificaciones
					$msj = "No se han registrado calificaciones para este estudiante en el periodo actual.";
					$msjClase = "mensaje-nivelar";
				}
				
				if (!empty($msj)) {
				?>
				<div class="mensaje-promocion <?= $msjClase ?>">
					<?= $msj ?>
				</div>
				<?php } ?>

				<!-- Firmas -->
				<div class="seccion-firmas">
					<div class="contenedor-firmas">
						<!-- Rector -->
						<div class="firma-item">
							<?php
							$rector = UsuariosPadre::sesionUsuario($informacion_inst["info_rector"], "", $config['conf_id_institucion'], $year);
							$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
							if (!empty($rector["uss_firma"]) && file_exists(ROOT_PATH . '/main-app/files/fotos/' . $rector['uss_firma'])) {
								echo '<img class="firma-imagen" src="../files/fotos/' . $rector["uss_firma"] . '" alt="Firma Rector">';
							} else {
								echo '<div class="firma-espacio"></div>';
							}
							?>
							<div class="firma-linea"></div>
							<div class="firma-nombre"><?= strtoupper($nombreRector) ?></div>
							<div class="firma-cargo">Rector(a)</div>
						</div>

						<!-- Secretario Académico -->
						<div class="firma-item">
							<?php
							$secretario = UsuariosPadre::sesionUsuario($informacion_inst["info_secretaria_academica"], "", $config['conf_id_institucion'], $year);
							$nombreSecretario = UsuariosPadre::nombreCompletoDelUsuario($secretario);
							if (!empty($secretario["uss_firma"]) && file_exists(ROOT_PATH . '/main-app/files/fotos/' . $secretario['uss_firma'])) {
								echo '<img class="firma-imagen" src="../files/fotos/' . $secretario["uss_firma"] . '" alt="Firma Secretario">';
							} else {
								echo '<div class="firma-espacio"></div>';
							}
							?>
							<div class="firma-linea"></div>
							<div class="firma-nombre"><?= strtoupper($nombreSecretario) ?></div>
							<div class="firma-cargo">Secretario(a) Académico</div>
						</div>
					</div>
				</div>
			</div>
			<?php 
				// Flush cada 3 estudiantes para ir mostrando progreso
				if ($contadorEstudiantes % 3 == 0) {
					flush();
					if (function_exists('ob_flush')) {
						@ob_flush();
					}
				}
			} // Fin foreach estudiantes 
			?>
		</div>
		<?php } // Fin if hayContenido ?>
	</div>

	<!-- Controles de Exportación -->
	<?php if ($hayContenido) { ?>
	<div id="controles-exportacion" style="display: flex;">
		<button type="button" class="btn-flotante btn-excel" onclick="window.open('<?= $urlExcel ?>', '_blank')">
			<i class="fas fa-file-excel"></i>
			Exportar a Excel
		</button>
		<button type="button" class="btn-flotante btn-print" onclick="window.print()">
			<i class="fas fa-print"></i>
			Imprimir
		</button>
	</div>
	<?php } ?>

	<script>
		// Actualizar URL con parámetros GET sin recargar (para mantener al recargar F5)
		<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hayContenido) { ?>
		if (window.history && window.history.pushState) {
			const params = new URLSearchParams();
			<?php if (isset($_GET["year"])) { ?>params.append('year', '<?= $_GET["year"] ?>');<?php } ?>
			<?php if (isset($_GET["curso"])) { ?>params.append('curso', '<?= $_GET["curso"] ?>');<?php } ?>
			<?php if (isset($_GET["grupo"])) { ?>params.append('grupo', '<?= $_GET["grupo"] ?>');<?php } ?>
			<?php if (isset($_GET["id"]) && !empty($_GET["id"])) { ?>params.append('id', '<?= $_GET["id"] ?>');<?php } ?>
			<?php if (isset($_GET["periodo"])) { ?>params.append('periodo', '<?= $_GET["periodo"] ?>');<?php } ?>
			
			const newUrl = window.location.pathname + '?' + params.toString();
			window.history.pushState({path: newUrl}, '', newUrl);
		}
		<?php } ?>
		
		// Función para generar PDF
		function generarPDF() {
			// Verificar que html2pdf esté disponible
			if (typeof html2pdf === 'undefined') {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'La biblioteca html2pdf no está cargada. Por favor recarga la página.'
				});
				return;
			}
			
			const loader = document.getElementById('loader');
			const controles = document.getElementById('controles-exportacion');
			
			// Mostrar loader y ocultar controles
			if (loader) loader.classList.add('active');
			if (controles) controles.style.display = 'none';
			
			Swal.fire({
				title: 'Generando PDF...',
				html: 'Por favor espera mientras se genera el documento. Esto puede tardar unos momentos.',
				allowOutsideClick: false,
				allowEscapeKey: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});
			
			const element = document.getElementById('contenido');
			if (!element) {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'No se encontró el contenido para generar el PDF.'
				});
				if (loader) loader.classList.remove('active');
				if (controles) controles.style.display = 'flex';
				return;
			}
			
			const opt = {
				margin: [10, 10, 10, 10],
				filename: 'Libro_Final_<?= htmlspecialchars($curso, ENT_QUOTES, 'UTF-8') ?>_<?= htmlspecialchars($grupo, ENT_QUOTES, 'UTF-8') ?>_<?= date('Y-m-d') ?>.pdf',
				image: { 
					type: 'jpeg', 
					quality: 0.95 
				},
				html2canvas: { 
					scale: 1.5,
					useCORS: true,
					allowTaint: false,
					logging: false,
					letterRendering: true,
					windowWidth: element.scrollWidth,
					windowHeight: element.scrollHeight
				},
				jsPDF: { 
					unit: 'mm', 
					format: 'a4', 
					orientation: 'portrait',
					compress: true
				},
				pagebreak: { 
					mode: ['avoid-all', 'css', 'legacy'],
					before: '.pagina-estudiante',
					after: '.pagina-estudiante',
					avoid: ['img', '.info-estudiante', '.tabla-calificaciones']
				}
			};
			
			// Usar Promise para manejar mejor el flujo asíncrono
			html2pdf()
				.set(opt)
				.from(element)
				.save()
				.then(() => {
					if (loader) loader.classList.remove('active');
					if (controles) controles.style.display = 'flex';
					Swal.fire({
						icon: 'success',
						title: '¡PDF Generado!',
						text: 'El archivo se ha descargado correctamente.',
						timer: 2500,
						showConfirmButton: false
					});
				})
				.catch(err => {
					console.error('Error al generar PDF:', err);
					if (loader) loader.classList.remove('active');
					if (controles) controles.style.display = 'flex';
					Swal.fire({
						icon: 'error',
						title: 'Error al generar PDF',
						html: 'Hubo un problema al generar el PDF.<br><small>Error: ' + (err.message || 'Error desconocido') + '</small><br><br>Intenta usar el botón "Imprimir" como alternativa.',
						confirmButtonText: 'Aceptar'
					});
				});
		}

		// Animación de entrada
		document.addEventListener('DOMContentLoaded', function() {
			const paginas = document.querySelectorAll('.pagina-estudiante');
			paginas.forEach((pagina, index) => {
				setTimeout(() => {
					pagina.style.opacity = '0';
					pagina.style.transform = 'translateY(20px)';
					setTimeout(() => {
						pagina.style.transition = 'all 0.5s ease-out';
						pagina.style.opacity = '1';
						pagina.style.transform = 'translateY(0)';
					}, 50);
				}, index * 100);
			});
			
			// Asegurar que los botones de exportación sean visibles
			const controles = document.getElementById('controles-exportacion');
			if (controles) {
				setTimeout(() => {
					controles.style.display = 'flex';
					controles.style.opacity = '0';
					setTimeout(() => {
						controles.style.transition = 'opacity 0.5s ease-in';
						controles.style.opacity = '1';
					}, 100);
				}, 500);
			}
		});
	</script>

	<?php include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php"); ?>
</body>
</html>

