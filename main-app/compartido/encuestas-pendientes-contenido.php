<div class="page-bar">
	<div class="page-title-breadcrumb">
		<div class=" pull-left">
			<div class="page-title">Encuestas Pendientes</div>
			<?php include("../compartido/texto-manual-ayuda.php");?>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
	<?php include("../../config-general/mensajes-informativos.php"); ?>
				<?php include("../compartido/publicidad-lateral.php");?>
				<div class="card card-topline-purple">
					<div class="card-head">
						<header>Encuestas Pendientes</header>
						<div class="tools">
							<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
							<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
							<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
						</div>
					</div>
					<div class="card-body">
						<table id="example1" class="display" style="width:100%;">
							<thead>
								<tr>
									<th>#</th>
									<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
									<th>Evaluación</th>
									<th>Evaluado</th>
									<th>Obligatoria</th>
									<th>Estado</th>
									<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									require_once(ROOT_PATH."/main-app/class/Areas.php");
									if (!empty($_GET['asignacion'])){
										require_once(ROOT_PATH."/main-app/class/PreguntaGeneral.php");
										
										$idA= base64_decode($_GET['asignacion']);
										$obligatorias= base64_decode($_GET['obligatorias']);
										$numPreguntasRespondidas = PreguntaGeneral::terminoEncuesta($conexion, $config, $idA, $datosUsuarioActual['uss_id']);

										if ($numPreguntasRespondidas == $obligatorias){
											Asignaciones::actualizarEstadoAsignacion($conexion, $config, $idA, FINALIZADO);
										}
									}
									
									$consultaEncuestas = Asignaciones::traerAsignacionesUsuario($conexion, $config, $datosUsuarioActual['uss_id']);
									if(!empty($consultaEncuestas)){
										$contReg = 1;
										while($resultado = mysqli_fetch_array($consultaEncuestas, MYSQLI_BOTH)){

											$iniciadas = Asignaciones::consultarCantAsignacionesEmpezadas($conexion, $config, $resultado['gal_id']);
											if ($resultado['gal_limite_evaluadores'] != 0 && $iniciadas >= $resultado['gal_limite_evaluadores'] ) { continue; }

											$fechaBD = new DateTime($resultado['evag_fecha']);
											$fecha = $fechaBD->format('d/m/Y');
											$nombre = !empty($resultado['evag_nombre']) ? $resultado['evag_nombre'] : "";
											$obligatoria = !empty($resultado['evag_obligatoria']) ? $resultado['evag_obligatoria'] : "";
											
											switch ($resultado['epag_tipo']) {
												case CURSO:
													require_once(ROOT_PATH."/main-app/class/Grados.php");
													$datosEvaluado = Grados::obtenerGrado($resultado['epag_id_evaluado']);
													$nombreEvaluado = $datosEvaluado['gra_nombre'];
												break;

												case AREA:
													$datosEvaluado = Areas::traerDatosArea($config, $resultado['epag_id_evaluado']);
													$nombreEvaluado = $datosEvaluado['ar_nombre'];
												break;

												case MATERIA:
													require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");
													$datosEvaluado = Asignaturas::consultarDatosAsignatura($conexion, $config, $resultado['epag_id_evaluado']);
													$nombreEvaluado = $datosEvaluado['mat_nombre'];
												break;

												default:
													if($resultado['epag_tipo'] == DIRECTIVO || $resultado['epag_tipo'] == DOCENTE) {
														$datosEvaluado = UsuariosPadre::sesionUsuario($resultado['epag_id_evaluado']);
														$nombreEvaluado = UsuariosPadre::nombreCompletoDelUsuario($datosEvaluado);
													}
												break;
											}
								?>
								<tr id="reg<?=$resultado['evag_id'];?>">
									<td><?=$contReg;?></td>
									<td><?=$fecha?></td>
									<td><?=$nombre;?></td>
									<td><?=$nombreEvaluado;?></td>
									<td>
										<?php 
											if($obligatoria==1){?>
												<button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Es obligatoria"><i class="fa fa-lock"></i></button>
											<?php }else{?>
												<button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="No es obligatoria"><i class="fa fa-unlock" aria-hidden="true"></i></button>
										<?php }?>
									</td>
									<td><?=$resultado['epag_estado'];?></td>
									<td>
										<div class="btn-group">
											<button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
											<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
												<i class="fa fa-angle-down"></i>
											</button>
											<ul class="dropdown-menu" role="menu" style="z-index: 10000;">
													<li><a href="../compartido/encuestas-realizar.php?id=<?=base64_encode($resultado['epag_id']);?>">Realizar Encuesta</a></li>
											</ul>
										</div>
									</td>
								</tr>
								<?php 
										$contReg++;
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>