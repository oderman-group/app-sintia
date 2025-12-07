			<!-- start page content -->
            <div class="page-content-wrapper">
				
				<?php
				require_once(ROOT_PATH."/main-app/class/Boletin.php");
				require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
				$idE="";
				if(!empty($_GET["idE"])){ $idE=base64_decode($_GET["idE"]);}
				$evaluacion = Evaluaciones::consultaEvaluacion($conexion, $config, $idE);

				//respuestas
				$respuestasEvaluacion = Evaluaciones::traerRespuestaEvaluacion($conexion, $config, $idE, $datosEstudianteActual['mat_id']);
				
				//CUANTOS ESTÁN REALIZANDO LA EVALUACIÓN EN ESTE MOMENTO Y CUANTOS TERMINARON
				$Numerosevaluados = Evaluaciones::consultarEvaluados($conexion, $config, $idE);

				//Cantidad de preguntas de la evaluación (solo las válidas con respuestas)
				$cantPreguntasTotal = Evaluaciones::numeroPreguntasEvaluacion($conexion, $config, $idE);
				// Contar solo preguntas válidas (con respuestas)
				$cantPreguntas = 0;
				$preguntasConsultaTemp = Evaluaciones::preguntasEvaluacion($conexion, $config, $idE);
				while($pregTemp = mysqli_fetch_array($preguntasConsultaTemp, MYSQLI_BOTH)){
					$respuestasConsultaTemp = Evaluaciones::traerRespuestaPregunta($conexion, $config, $pregTemp['preg_id']);
					$cantRespuestasTemp = mysqli_num_rows($respuestasConsultaTemp);
					if($cantRespuestasTemp > 0) {
						$cantPreguntas++;
					}
				}

				// Calcular respuestas correctas e incorrectas del estudiante ANTES de mostrar el panel
				$respuestasCorrectasEstudiante = 0;
				$respuestasIncorrectasEstudiante = 0;
				$puntosSumados = 0;
				$totalPuntos = 0;
				$preguntasConsultaPre = Evaluaciones::preguntasEvaluacion($conexion, $config, $idE);
				while($preguntasPre = mysqli_fetch_array($preguntasConsultaPre, MYSQLI_BOTH)){
					$respuestasConsultaPre = Evaluaciones::traerRespuestaPregunta($conexion, $config, $preguntasPre['preg_id']);
					$cantRespuestasPre = mysqli_num_rows($respuestasConsultaPre);
					if($cantRespuestasPre == 0) {
						continue;
					}
					
					$totalPuntos += $preguntasPre['preg_valor'];
					$respuestaEstudianteEncontrada = false;
					$respuestaEstudianteCorrecta = false;
					
					// Buscar qué respuesta seleccionó el estudiante
					while($respuestasPre = mysqli_fetch_array($respuestasConsultaPre, MYSQLI_BOTH)){
						$compararRespuestasPre = Evaluaciones::compararRespuestas($conexion, $config, $idE, $datosEstudianteActual['mat_id'], $preguntasPre['preg_id'], $respuestasPre['resp_id']);
						if(!empty($compararRespuestasPre['res_id'])) {
							$respuestaEstudianteEncontrada = true;
							if($respuestasPre['resp_correcta']==1) {
								$respuestaEstudianteCorrecta = true;
								$puntosSumados += $preguntasPre['preg_valor'];
							}
							break; // Solo puede haber una respuesta seleccionada
						}
					}
					
					// Si no encontró respuesta marcada, verificar si hay registro con res_id_respuesta=0
					if(!$respuestaEstudianteEncontrada) {
						$sqlVerificarSinRespuesta = "SELECT COUNT(*) FROM ".BD_ACADEMICA.".academico_actividad_evaluaciones_resultados 
							WHERE res_id_evaluacion=? AND res_id_estudiante=? AND res_id_pregunta=? AND res_id_respuesta='0' AND institucion=? AND year=?";
						$parametrosVerificar = [$idE, $datosEstudianteActual['mat_id'], $preguntasPre['preg_id'], $config['conf_id_institucion'], $_SESSION["bd"]];
						$resultadoVerificar = BindSQL::prepararSQL($sqlVerificarSinRespuesta, $parametrosVerificar);
						$filaVerificar = mysqli_fetch_array($resultadoVerificar, MYSQLI_NUM);
						if(isset($filaVerificar[0]) && $filaVerificar[0] > 0) {
							$respuestaEstudianteEncontrada = true;
							$respuestaEstudianteCorrecta = false;
						}
					}
					
					// Contar respuestas
					if($respuestaEstudianteEncontrada) {
						if($respuestaEstudianteCorrecta) {
							$respuestasCorrectasEstudiante++;
						} else {
							$respuestasIncorrectasEstudiante++;
						}
					}
				}
				
				// Calcular nota
				$nota = $totalPuntos > 0 ? round(($puntosSumados/$totalPuntos)*$config['conf_nota_hasta'],$config['conf_decimales_notas']) : 0;
				$notaFinal = $nota;
				$title = '';
				$style = '';
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
					$title = 'title="Nota Cuantitativa: '.$nota.'"';
					$style = 'style="font-size: 17px; margin-top: 13px"';
					$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota);
					$notaFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
				}

				//Si la evaluación no tiene preguntas, lo mandamos para la pagina informativa
				if($cantPreguntas==0){
					echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=101";</script>';
					exit();
				}

				//SABER SI EL ESTUDIANTE YA HIZO LA EVALUACION
				$nume = Evaluaciones::verificarEstudianteEvaluacion($conexion, $config, $idE, $datosEstudianteActual['mat_id']);
				
				if($nume==0){
					echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=203";</script>';
					exit();
				}

				//CONSULTAMOS SI YA TIENE UNA SESIÓN ABIERTA EN ESTA EVALUACIÓN
				$estadoSesionEvaluacion = Evaluaciones::consultarSessionEstudianteEvaluacion($conexion, $config, $idE, $datosEstudianteActual['mat_id']);
				if($estadoSesionEvaluacion>0){
					echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=201";</script>';
					exit();
				}
				?>

				<input type="hidden" id="idE" name="idE" value="<?=$idE;?>">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$evaluacion['eva_nombre'];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                            <?php 
							//ESTUDIANTES
							if($datosUsuarioActual['uss_tipo']==TIPO_ESTUDIANTE){?>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="evaluaciones.php"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$evaluacion['eva_nombre'];?></li>
                            </ol>
							<?php }?>
							
							<?php 
							//DOCENTES
							if($datosUsuarioActual['uss_tipo']==TIPO_DOCENTE){?>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="evaluaciones.php"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li><a class="parent-item" href="evaluaciones-resultados.php?idE=<?=$_GET["idE"];?>"><?=$evaluacion['eva_nombre'];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
								<li class="active"><?=strtoupper($datosEstudianteActual['mat_primer_apellido']." ".$datosEstudianteActual['mat_segundo_apellido']." ".$datosEstudianteActual['mat_nombres']);?></li>
                            </ol>
							<?php }?>
                        </div>
                    </div>
                    <!-- Panel colapsable con información de la evaluación -->
					<div class="card mb-4">
						<div class="card-header" style="cursor: pointer; background-color: #6c757d; color: white;" data-toggle="collapse" data-target="#infoEvaluacionCollapse" aria-expanded="false" aria-controls="infoEvaluacionCollapse">
							<h5 class="mb-0">
								<i class="fa fa-chevron-down" id="iconInfoEvaluacion" style="transition: transform 0.3s;"></i>
								Información de la Evaluación
							</h5>
						</div>
						<div class="collapse" id="infoEvaluacionCollapse">
							<div class="card-body">
								<div class="row">
									<!-- Columna izquierda: Información general -->
									<div class="col-md-6">
										<div class="panel mb-3">
											<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?></header>
											<div class="panel-body">
												<p><?=$frases[155][$datosUsuarioActual['uss_idioma']];?></p>
												<p>
													<b><?=$frases[141][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[144][$datosUsuarioActual['uss_idioma']];?>
												</p>
												<p>
													<b><?=$frases[142][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[145][$datosUsuarioActual['uss_idioma']];?>
												</p>
											</div>
										</div>

										<div class="panel mb-3">
											<header class="panel-heading panel-heading-purple"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?> </header>
											<div class="panel-body">
												<p><?=$frases[159][$datosUsuarioActual['uss_idioma']];?></p>
												<?php
												$evaluacionesEnComun = Evaluaciones::consultaEvaluacionTodas($conexion,$config, $idE, $cargaConsultaActual, $periodoConsultaActual);
												while($evaComun = mysqli_fetch_array($evaluacionesEnComun, MYSQLI_BOTH)){
													//SABER SI EL ESTUDIANTE YA HIZO LA EVALUACION
													$nume = Evaluaciones::verificarEstudianteEvaluacion($conexion, $config, $evaComun['eva_id'], $datosEstudianteActual['mat_id']);
													
													if($nume==0){continue;}
												?>
													<p><a href="evaluaciones-ver.php?idE=<?=base64_encode($evaComun['eva_id']);?>&usrEstud=<?=base64_encode($datosEstudianteActual['mat_id_usuario']);?>"><?=$evaComun['eva_nombre'];?></a></p>
												<?php }?>
											</div>
										</div>
									</div>

									<!-- Columna derecha: Resumen del estudiante -->
									<div class="col-md-6">
										<div class="card card-topline-aqua mb-3">
											<div class="card-body no-padding height-9">
												<div class="profile-usertitle">
													<div class="profile-usertitle-name"> <?=$datosCargaActual['mat_nombre'];?> </div>
												</div>
											</div>
										</div>
										<div class="card mb-3">
											<div class="card-head card-topline-aqua">
												<header><?=$evaluacion['eva_nombre'];?></header>
											</div>
											<div class="card-body no-padding height-9">
												<div class="profile-desc">
													<?=$evaluacion['eva_descripcion'];?>
												</div>
												<ul class="list-group list-group-unbordered">
													<li class="list-group-item">
														<b><?=$frases[130][$datosUsuarioActual['uss_idioma']];?> </b>
														<div class="profile-desc-item pull-right"><?=$evaluacion['eva_desde'];?></div>
													</li>
													<li class="list-group-item">
														<b><?=$frases[131][$datosUsuarioActual['uss_idioma']];?> </b>
														<div class="profile-desc-item pull-right"><?=$evaluacion['eva_hasta'];?></div>
													</li>
												</ul>

												<div class="row list-separated profile-stat">
													<div class="col-md-4 col-sm-4 col-6">
														<div class="uppercase profile-stat-title"> <?=$cantPreguntas;?> </div>
														<div class="uppercase profile-stat-text"> <?=$frases[139][$datosUsuarioActual['uss_idioma']];?> </div>
													</div>
													<div class="col-md-4 col-sm-4 col-6">
														<div class="uppercase profile-stat-title" style="color: chartreuse;"> <?=$Numerosevaluados[0];?> </div>
														<div class="uppercase profile-stat-text"> <?=$frases[141][$datosUsuarioActual['uss_idioma']];?> </div>
													</div>
													<div class="col-md-4 col-sm-4 col-6">
														<div class="uppercase profile-stat-title"> <?=$Numerosevaluados[1];?> </div>
														<div class="uppercase profile-stat-text"> <?=$frases[142][$datosUsuarioActual['uss_idioma']];?> </div>
													</div>
												</div>

												<div class="row list-separated profile-stat">
													<div class="col-md-4 col-sm-4 col-6">
														<div class="uppercase profile-stat-title"> <?=$respuestasCorrectasEstudiante;?> </div>
														<div class="uppercase profile-stat-text"> <?=$frases[156][$datosUsuarioActual['uss_idioma']];?> </div>
													</div>
													<div class="col-md-4 col-sm-4 col-6">
														<div class="uppercase profile-stat-title"> <?=$respuestasIncorrectasEstudiante;?> </div>
														<div class="uppercase profile-stat-text"> <?=$frases[157][$datosUsuarioActual['uss_idioma']];?> </div>
													</div>
													<div class="col-md-4 col-sm-4 col-6">
														<div class="uppercase profile-stat-title" <?=$title;?> <?=$style;?>> <?=$notaFinal;?> </div>
														<div class="uppercase profile-stat-text"> <?=$frases[108][$datosUsuarioActual['uss_idioma']];?> </div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
									<form action="guardar.php" method="post">
										<input type="hidden" name="id" value="9">
										<input type="hidden" name="idE" value="<?=$idE;?>">
										<input type="hidden" name="cantPreguntas" value="<?=$cantPreguntas;?>">
										
									
											<?php
											$puntosSumados = 0;
											$totalPuntos = 0;
											$arrayPreguntas = "";
											$arrayRespuestasCorrectas = "";
											$arrayRespuestasIncorrectas = "";
											$arrayColoresC = "";
											$arrayColoresI = "";
											$contPreguntas = 1;
											$respuestasCorrectasEstudiante = 0;
											$respuestasIncorrectasEstudiante = 0;
											$preguntasConsulta = Evaluaciones::preguntasEvaluacion($conexion, $config, $idE);
											// Arrays para el gráfico del estudiante actual
											$arrayRespuestasCorrectasEstudiante = "";
											$arrayRespuestasIncorrectasEstudiante = "";
											while($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)){
												$respuestasConsulta = Evaluaciones::traerRespuestaPregunta($conexion, $config, $preguntas['preg_id']);

												$cantRespuestas = mysqli_num_rows($respuestasConsulta);
												if($cantRespuestas==0) {
													// Omitir el mensaje y simplemente continuar sin mostrar la pregunta
													continue;
												}
												
												$respuestasXpregunta = Evaluaciones::respuestasXPreguntas($conexion, $config, $idE, $preguntas['preg_id']);
												
												$totalPuntos +=$preguntas['preg_valor'];
												$arrayPreguntas .= '"Pregunta '.$contPreguntas.'",';
												$arrayColoresC .= "'rgba(54, 162, 235, 0.8)',";
												$arrayColoresI .= "'rgba(255, 99, 132, 0.8)',";
												
												// La función ahora retorna un array [correctas, incorrectas]
												$correctas = isset($respuestasXpregunta[0]) ? (int)$respuestasXpregunta[0] : 0;
												$incorrectas = isset($respuestasXpregunta[1]) ? (int)$respuestasXpregunta[1] : 0;
												
												// Validar que el cálculo sea correcto: las incorrectas deben incluir todos los que no acertaron
												// Esto ya está siendo calculado correctamente en respuestasXPreguntas, pero verificamos
												$arrayRespuestasCorrectas .= $correctas.",";
												$arrayRespuestasIncorrectas .= $incorrectas.",";
												
												// Calcular respuestas del estudiante actual basándose en lo que realmente seleccionó
												$respuestaEstudianteEncontrada = false;
												$respuestaEstudianteCorrecta = false;
												
												// Buscar qué respuesta seleccionó el estudiante para esta pregunta
												$respuestasConsultaEstudiante = Evaluaciones::traerRespuestaPregunta($conexion, $config, $preguntas['preg_id']);
												while($respuestasEst = mysqli_fetch_array($respuestasConsultaEstudiante, MYSQLI_BOTH)){
													$compararRespuestasEst = Evaluaciones::compararRespuestas($conexion, $config, $idE, $datosEstudianteActual['mat_id'], $preguntas['preg_id'], $respuestasEst['resp_id']);
													if(!empty($compararRespuestasEst['res_id'])) {
														$respuestaEstudianteEncontrada = true;
														if($respuestasEst['resp_correcta']==1) {
															$respuestaEstudianteCorrecta = true;
														}
														break;
													}
												}
												
												// Si no encontró respuesta marcada, verificar si hay registro con res_id_respuesta=0
												if(!$respuestaEstudianteEncontrada) {
													$sqlVerificarSinRespuesta = "SELECT COUNT(*) FROM ".BD_ACADEMICA.".academico_actividad_evaluaciones_resultados 
														WHERE res_id_evaluacion=? AND res_id_estudiante=? AND res_id_pregunta=? AND res_id_respuesta='0' AND institucion=? AND year=?";
													$parametrosVerificar = [$idE, $datosEstudianteActual['mat_id'], $preguntas['preg_id'], $config['conf_id_institucion'], $_SESSION["bd"]];
													$resultadoVerificar = BindSQL::prepararSQL($sqlVerificarSinRespuesta, $parametrosVerificar);
													$filaVerificar = mysqli_fetch_array($resultadoVerificar, MYSQLI_NUM);
													if(isset($filaVerificar[0]) && $filaVerificar[0] > 0) {
														$respuestaEstudianteEncontrada = true;
														$respuestaEstudianteCorrecta = false;
													}
												}
												
												// Agregar datos del estudiante actual al array para el gráfico
												if($respuestaEstudianteEncontrada) {
													if($respuestaEstudianteCorrecta) {
														$arrayRespuestasCorrectasEstudiante .= "1,";
														$arrayRespuestasIncorrectasEstudiante .= "0,";
													} else {
														$arrayRespuestasCorrectasEstudiante .= "0,";
														$arrayRespuestasIncorrectasEstudiante .= "1,";
													}
												} else {
													$arrayRespuestasCorrectasEstudiante .= "0,";
													$arrayRespuestasIncorrectasEstudiante .= "0,";
												}
											?>
												<div class="panel">
													<header class="panel-heading panel-heading-blue"><?php echo $preguntas['preg_descripcion'];?> </header>
													<div class="panel-body">
											<?php 
												$contRespuestas = 1;
												while($respuestas = mysqli_fetch_array($respuestasConsulta, MYSQLI_BOTH)){
													$compararRespuestas = Evaluaciones::compararRespuestas($conexion, $config, $idE, $datosEstudianteActual['mat_id'], $preguntas['preg_id'], $respuestas['resp_id']);
													if(!empty($compararRespuestas['res_id'])) {
														$cheked = 'checked';
														// Si la respuesta seleccionada es correcta, sumar puntos
														if($respuestas['resp_correcta']==1) {
															$puntosSumados += $preguntas['preg_valor'];
														}
													} else {
														$cheked = '';
													}
													if($respuestas['resp_correcta']==1) {$colorRespuesta = 'green'; $label='(correcta)';} else {$colorRespuesta = 'red'; $label='(incorrecta)';}
											?>
												<div>
													<?php 
													if($preguntas['preg_tipo_pregunta']==3){
														if(!empty($compararRespuestas['res_archivo'])){
													?>
														<p style="color: navy; font-weight: bold;">El maestro debe ver el archivo y evaluar esta respuesta manualmente.</p>
														<a href="../files/evaluaciones/<?=$compararRespuestas['res_archivo'];?>" target="_blank"><?=$compararRespuestas['res_archivo'];?></a>

													<?php 
														}
													}else{
													?>
														<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-<?=$contPreguntas;?><?=$contRespuestas;?>">
															<input type="radio" id="option-<?=$contPreguntas;?><?=$contRespuestas;?>" class="mdl-radio__button" name="R<?=$contPreguntas;?>" value="<?php echo $respuestas['resp_id'];?>" <?=$cheked;?> disabled>
															
														</label>
														<span class="mdl-radio__label"><span style="color: <?=$colorRespuesta;?>;"><?php echo $respuestas['resp_descripcion'];?> <?=$label;?></span></span>
													<?php }?>	
												</div><hr>
											<?php
													$contRespuestas ++;
												}
											?>
														<p align="right" style="font-size: 12px; color: cadetblue;"><?=$preguntas['preg_valor'];?> puntos</p>
													</div>
												</div>	
											<?php
												// Las respuestas ya se contaron antes del panel, solo incrementamos el contador para el gráfico
												$contPreguntas ++;
											}
											$nota = round(($puntosSumados/$totalPuntos)*$config['conf_nota_hasta'],$config['conf_decimales_notas']);
											$arrayPreguntas = substr($arrayPreguntas,0,-1);
											$arrayRespuestasCorrectas = substr($arrayRespuestasCorrectas,0,-1);
											$arrayRespuestasIncorrectas = substr($arrayRespuestasIncorrectas,0,-1);
											$arrayColoresC = substr($arrayColoresC,0,-1);
											$arrayColoresI = substr($arrayColoresI,0,-1);
											$arrayRespuestasCorrectasEstudiante = substr($arrayRespuestasCorrectasEstudiante,0,-1);
											$arrayRespuestasIncorrectasEstudiante = substr($arrayRespuestasIncorrectasEstudiante,0,-1);

											$notaFinal=$nota;
											$title='';
											$style='';
											if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
												$title='title="Nota Cuantitativa: '.$nota.'"';
												$style='style="font-size: 17px; margin-top: 13px"';
												$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota);
												$notaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
											}
											?>

			
									</form>
								
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[160][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<p>Este gráfico muestra cuántos estudiantes, de los que ya finalizaron la evaluación, respondieron correcta o incorrectamente cada pregunta, y también tus respuestas.</p>
											<canvas id="myChart" width="400" height="400"></canvas>
											<script>
											var ctx = document.getElementById("myChart").getContext('2d');
											var myChart = new Chart(ctx, {
												
												type: 'bar',
												data: {
													labels: [<?=$arrayPreguntas;?>],
													datasets: [
													//ESTUDIANTES QUE ACERTARON
													{
														label: 'Estudiantes que acertaron',
														data: [<?=$arrayRespuestasCorrectas;?>],
														backgroundColor: 'rgba(54, 162, 235, 0.6)',
														borderColor: 'rgba(54, 162, 235, 1)',
														borderWidth: 1
													},
													//ESTUDIANTES QUE NO ACERTARON
													{
														label: 'Estudiantes que NO acertaron',
														data: [<?=$arrayRespuestasIncorrectas;?>],
														backgroundColor: 'rgba(255, 99, 132, 0.6)',
														borderColor: 'rgba(255, 99, 132, 1)',
														borderWidth: 1		   
													},
													//TU RESPUESTA CORRECTA
													{
														label: 'Tu respuesta correcta',
														data: [<?=$arrayRespuestasCorrectasEstudiante;?>],
														backgroundColor: 'rgba(75, 192, 192, 0.8)',
														borderColor: 'rgba(75, 192, 192, 1)',
														borderWidth: 2,
														type: 'line',
														fill: false,
														pointRadius: 6,
														pointHoverRadius: 8
													},
													//TU RESPUESTA INCORRECTA
													{
														label: 'Tu respuesta incorrecta',
														data: [<?=$arrayRespuestasIncorrectasEstudiante;?>],
														backgroundColor: 'rgba(255, 159, 64, 0.8)',
														borderColor: 'rgba(255, 159, 64, 1)',
														borderWidth: 2,
														type: 'line',
														fill: false,
														pointRadius: 6,
														pointHoverRadius: 8
													}
														
													]
												},
												options: {
													scales: {
														yAxes: [{
															ticks: {
																beginAtZero:true
															}
														}]
													},
													barPercentage: 0.5
												}
											});
											</script>

										</div>
									</div>
								
								
								</div>
						
								
						
                        </div>
                    </div>
            <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
		
		<script>
		// Rotar icono cuando se colapsa/expande el panel
		$(document).ready(function() {
			$('#infoEvaluacionCollapse').on('show.bs.collapse', function () {
				$('#iconInfoEvaluacion').css('transform', 'rotate(180deg)');
			});
			$('#infoEvaluacionCollapse').on('hide.bs.collapse', function () {
				$('#iconInfoEvaluacion').css('transform', 'rotate(0deg)');
			});
		});
		</script>