<?php
if( !empty($_GET["modal"]) && $_GET["modal"] == 1 ) {
	include("../docente/session.php");
	$idPaginaInterna = 'CM0008';
	include("historial-acciones-guardar.php");
	require_once("../class/UsuariosPadre.php");
	require_once("../class/Estudiantes.php");
	require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

	$config = Plataforma::sesionConfiguracion();
	$_SESSION["configuracion"] = $config;
}
?>
<div class="panel">
											<header class="panel-heading panel-heading-blue"><i class="fa fa-signal"></i> PROGRESO DE DOCENTES</header>

											<div class="panel-body">
												<p class="text-danger">Aquí se muestra el progreso general que cada uno de los docentes lleva en cuanto al registro de sus calificaciones para este <b>periodo <?=$config['conf_periodo'];?></b>.<br>
												<span class="text-info"><i class="fa fa-trophy"></i> <b>FELICITAMOS A LOS PRIMEROS LUGARES</b> <i class="fa fa-trophy"></i></span><br>	
												<span class="text-success"><b>¡APRESÚRATE TÚ TAMBIÉN!</b></span>
												</p>
												
												<?php
												$docentesProgreso = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=".TIPO_DOCENTE." AND uss_bloqueado='0'
												ORDER BY uss_nombre");
												$profes = array();
												$profesNombre = array();
												while($docProgreso = mysqli_fetch_array($docentesProgreso, MYSQLI_BOTH)){
													$nombreDocente= UsuariosPadre::nombreCompletoDelUsuario($docProgreso);
													$datosProgreso = CargaAcademica::consultaProgresoDocentes($config, $docProgreso['uss_id']);
													$sumasProgreso = ($datosProgreso[1] + $datosProgreso[2])/2;
													if($datosProgreso[0]>0){
														$sumasProgreso = round($sumasProgreso / $datosProgreso[0],2);
													}
													
													if($sumasProgreso>0){
														$profes[$docProgreso['uss_id']] = $sumasProgreso;
														$profesNombre[$docProgreso['uss_id']] = $nombreDocente;
													}else{
														continue;
													}
														
													
												}
												
												arsort($profes);
												$contP = 1;
												foreach ($profes as $key => $val) {
													if($val <= 50) $colorGrafico = 'danger';
													if($val > 50 and $val <80) $colorGrafico = 'warning';
													if($val > 80) $colorGrafico = 'info';
												?>
													<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><?="<b>".$contP.".</b> ".$profesNombre[$key];?></div>
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
												
												<p class="text-info" style="margin-top: 15px;">Los docentes que no aparecen en este listado es porque aún no han iniciado este proceso. Los instamos a iniciar pronto.</p>
											</div>
										</div>
