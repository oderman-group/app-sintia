<?php
if( !empty($_GET["modal"]) && $_GET["modal"] == 1 ) {
	include("../docente/session.php");
	$idPaginaInterna = 'CM0008';
	include("historial-acciones-guardar.php");
	require_once("../class/UsuariosPadre.php");
	require_once("../class/Estudiantes.php");
	require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
	require_once(ROOT_PATH."/main-app/class/CargaAcademicaOptimizada.php");
} else {
	// Asegurar que las clases necesarias estén disponibles cuando se incluye directamente
	if (!class_exists('BindSQL')) {
		require_once(ROOT_PATH."/main-app/class/BindSQL.php");
	}
	if (!class_exists('CargaAcademicaOptimizada')) {
		require_once(ROOT_PATH."/main-app/class/CargaAcademicaOptimizada.php");
	}
	// Asegurar que conexionPDO esté disponible para obtenerDatosAdicionalesCarga
	global $conexionPDO;
	if (!isset($conexionPDO) || $conexionPDO === null) {
		require_once(ROOT_PATH."/main-app/class/Conexion.php");
		$conexionPDO = Conexion::newConnection('PDO');
	}
}
?>
<div class="panel">
											<header class="panel-heading panel-heading-secondary" style="font-size: 18px;"><i class="fa fa-signal"></i> PROGRESO DE DOCENTES</header>

											<div class="panel-body">
												<p style="color: #333; font-size: 14px;">Aquí se muestra el progreso general que cada uno de los docentes lleva en cuanto al registro de sus calificaciones para este <b>periodo <?=$config['conf_periodo'];?></b>.<br>
												<span style="color: #007bff; font-size: 14px;"><i class="fa fa-trophy"></i> <b>FELICITAMOS A LOS PRIMEROS LUGARES</b> <i class="fa fa-trophy"></i></span><br>
												<span style="color: #28a745; font-size: 14px;"><b>¡APRESÚRATE TÚ TAMBIÉN!</b></span>
												</p>
												
												<?php
												// Obtener todos los docentes
												$filtroDocentes = " AND uss_tipo=".TIPO_DOCENTE." AND (uss_bloqueado='0' OR uss_bloqueado IS NULL) ORDER BY uss_nombre";
												$docentesProgreso = UsuariosPadre::obtenerTodosLosDatosDeUsuarios($filtroDocentes);
												
												$profes = array();
												$profesNombre = array();
												
												while($docProgreso = mysqli_fetch_array($docentesProgreso, MYSQLI_BOTH)){
													$nombreDocente = UsuariosPadre::nombreCompletoDelUsuario($docProgreso);
													$idDocente = trim($docProgreso['uss_id']); // Asegurar que no haya espacios
													
													// Obtener todas las cargas del docente para el periodo actual usando consulta SQL directa
													$sqlCargas = "SELECT car_id, car_periodo 
																FROM ".BD_ACADEMICA.".academico_cargas 
																WHERE car_docente = ? 
																AND car_periodo = ? 
																AND institucion = ? 
																AND year = ? 
																AND car_activa = 1";
													
													$parametrosCargas = [$idDocente, $config['conf_periodo'], $config['conf_id_institucion'], $_SESSION["bd"]];
													$resultadoCargas = BindSQL::prepararSQL($sqlCargas, $parametrosCargas);
													
													$totalDeclaradas = 0;
													$totalRegistradas = 0;
													$numCargas = 0;
													
													// Procesar cada carga del docente
													while($carga = mysqli_fetch_array($resultadoCargas, MYSQLI_BOTH)){
														$cargaId = $carga['car_id'];
														$periodo = $carga['car_periodo'];
														
														// Obtener datos de progreso de la carga usando el mismo método que el reporte de directivo
														$datosCarga = CargaAcademicaOptimizada::obtenerDatosAdicionalesCarga($config, $cargaId, $periodo);
														
														$actividadesDeclaradas = floatval($datosCarga['actividades_totales'] ?? 0);
														$actividadesRegistradas = floatval($datosCarga['actividades_registradas'] ?? 0);
														
														// Contar todas las cargas, sumando sus actividades (incluso si son 0)
														// Esto asegura que todas las cargas se cuenten para el promedio
														$totalDeclaradas += $actividadesDeclaradas;
														$totalRegistradas += $actividadesRegistradas;
														$numCargas++;
													}
													
													// Calcular el promedio: (promedio_declaradas + promedio_registradas) / 2
													$sumasProgreso = 0;
													if($numCargas > 0){
														$promedioDeclaradas = $totalDeclaradas / $numCargas;
														$promedioRegistradas = $totalRegistradas / $numCargas;
														$sumasProgreso = round(($promedioDeclaradas + $promedioRegistradas) / 2, 2);
													}
													
													// Si el progreso es 0 pero tiene cargas, verificar si hay calificaciones directamente
													// Esto ayuda a detectar casos donde hay notas pero las actividades no están marcadas correctamente
													if($sumasProgreso == 0 && $numCargas > 0){
														// Verificar si hay calificaciones registradas para este docente en el periodo
														$sqlCalificaciones = "SELECT COUNT(DISTINCT cal.cal_id_actividad) as actividades_con_notas
																			FROM ".BD_ACADEMICA.".academico_calificaciones cal
																			INNER JOIN ".BD_ACADEMICA.".academico_actividades act 
																				ON act.act_id = cal.cal_id_actividad
																				AND act.act_periodo = ?
																				AND act.act_estado = 1
																				AND act.institucion = ?
																				AND act.year = ?
																			INNER JOIN ".BD_ACADEMICA.".academico_cargas car
																				ON car.car_id = act.act_id_carga
																				AND car.car_docente = ?
																				AND car.car_periodo = ?
																				AND car.institucion = ?
																				AND car.year = ?
																			WHERE cal.institucion = ?
																			AND cal.year = ?";
														
														$parametrosCalif = [
															$config['conf_periodo'], $config['conf_id_institucion'], $_SESSION["bd"],
															$idDocente, $config['conf_periodo'], $config['conf_id_institucion'], $_SESSION["bd"],
															$config['conf_id_institucion'], $_SESSION["bd"]
														];
														
														$resultadoCalif = BindSQL::prepararSQL($sqlCalificaciones, $parametrosCalif);
														$filaCalif = mysqli_fetch_array($resultadoCalif, MYSQLI_BOTH);
														$actividadesConNotas = intval($filaCalif['actividades_con_notas'] ?? 0);
														
														// Si hay actividades con notas, calcular un progreso mínimo
														if($actividadesConNotas > 0){
															// Estimar progreso basado en actividades con notas vs total de cargas
															$progresoEstimado = min(100, ($actividadesConNotas / $numCargas) * 10); // Mínimo 10% por actividad con notas
															$sumasProgreso = round($progresoEstimado, 2);
														}
													}
													
													// Incluir docentes que tengan al menos alguna actividad declarada o registrada
													if($sumasProgreso > 0){
														$profes[$idDocente] = $sumasProgreso;
														$profesNombre[$idDocente] = $nombreDocente;
													}
												}
												
												arsort($profes);
												$contP = 1;
												// Obtener ID del docente actual si está disponible
												$idDocenteActual = isset($_SESSION['id']) ? $_SESSION['id'] : (isset($datosUsuarioActual['uss_id']) ? $datosUsuarioActual['uss_id'] : null);
												
												foreach ($profes as $key => $val) {
													if($val <= 50) $colorGrafico = 'danger';
													if($val > 50 and $val <80) $colorGrafico = 'warning';
													if($val > 80) $colorGrafico = 'success';
													
													// Verificar si es el docente actual para destacarlo
													$esDocenteActual = ($idDocenteActual && $key == $idDocenteActual);
													$claseDestacado = $esDocenteActual ? ' style="background-color: #e3f2fd; border-left: 4px solid #2196f3; padding-left: 10px;"' : '';
												?>
													<div class="work-monitor work-progress"<?=$claseDestacado;?>>
															<div class="states">
																<div class="info">
																	<div class="desc pull-left">
																		<?php if($esDocenteActual) { ?>
																			<b><?=$contP;?>.</b> <strong style="color: #2196f3;"><?=$profesNombre[$key];?> (TÚ)</strong>
																		<?php } else { ?>
																			<b><?=$contP;?>.</b> <?=$profesNombre[$key];?>
																		<?php } ?>
																	</div>
																	<div class="percent pull-right"><?=$val;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-<?=$colorGrafico;?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$val;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
												<?php
													$contP++;
												}
												?>
												
												<p style="color: #007bff; font-size: 14px; margin-top: 15px;">Los docentes que no aparecen en este listado es porque aún no han iniciado este proceso. Los instamos a iniciar pronto.</p>
											</div>
										</div>
