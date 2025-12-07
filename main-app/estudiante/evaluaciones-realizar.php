<?php include("session.php");?>
<?php include("verificar-usuario.php");?>
<?php $idPaginaInterna = 'ES0019';
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>

<div class="modal fade" id="mostrarmodalZero" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-body">
            <h4>Tiempo agotado</h4>
            El tiempo para esta evaluación ha finalizado.  
     	</div>
         <div class="modal-footer">
        <a href="#" data-dismiss="modal" class="btn btn-danger">Cerrar</a>
     </div>
      </div>
   </div>
</div>
	
	<?php
	$idE="";
	if(!empty($_GET["idE"])){ $idE=base64_decode($_GET["idE"]);}
	
	$evaluacion = Evaluaciones::consultaEvaluacion($conexion, $config, $idE);

	if($evaluacion[0]==""){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=106";</script>';
		exit();
	}
	
	$fechas= Evaluaciones::fechaEvaluacion($conexion, $config, $idE);
	if($fechas[2]>0){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=204&fechaD='.$evaluacion['eva_desde'].'&diasF='.$fechas[0].'&segundosF='.$fechas[2].'";</script>';
		exit();
	}
	if($fechas[3]<0){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=205&fechaH='.$evaluacion['eva_hasta'].'&diasP='.$fechas[1].'&segundosP='.$fechas[3].'";</script>';
		exit();
	}
	
	//Cantidad de preguntas de la evaluación
	$cantPreguntas = Evaluaciones::numeroPreguntasEvaluacion($conexion, $config, $idE);

	//Si la evaluación no tiene preguntas, lo mandamos para la pagina informativa
	if($cantPreguntas==0){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=101";</script>';
		exit();
	}

	//SABER SI EL ESTUDIANTE YA HIZO LA EVALUACION
	$nume = Evaluaciones::verificarEstudianteEvaluacion($conexion, $config, $idE, $datosEstudianteActual['mat_id']);
	
	if($nume>0){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=200";</script>';
		exit();
	}
	
	//CONSULTAMOS SI YA TIENE UNA SESIÓN ABIERTA EN ESTA EVALUACIÓN
	$estadoSesionEvaluacion = Evaluaciones::consultarSessionEstudianteEvaluacion($conexion, $config, $idE, $datosEstudianteActual['mat_id']);
	if($estadoSesionEvaluacion>0){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=201";</script>';
		exit();
	}
	
	//BORRAMOS SI EXISTE Y LUEGO INSERTAMOS EL DATO DE QUE EL ESTUDIANTE INICIÓ LA EVALUACIÓN
	Evaluaciones::eliminarIntentos($conexion, $config, $idE, $datosEstudianteActual['mat_id']);
	
	Evaluaciones::guardarIntento($conexion, $config, $idE, $datosEstudianteActual['mat_id']);
	

	//CUANTOS ESTÁN REALIZANDO LA EVALUACIÓN EN ESTE MOMENTO Y CUANTOS TERMINARON
	$Numerosevaluados = Evaluaciones::consultarEvaluados($conexion, $config, $idE);
	
	// Obtener fecha de finalización de la evaluación para calcular tiempo restante
	$fechaHasta = $evaluacion['eva_hasta'];
	$fechaHastaTimestamp = strtotime($fechaHasta);
	?>
	
	<script type="text/javascript">
		// Fecha de finalización de la evaluación (desde PHP)
		var fechaHastaEvaluacion = new Date(<?=$fechaHastaTimestamp * 1000;?>); // Convertir timestamp a milisegundos
		var alerta5MinutosMostrada = false;
		var alerta2MinutosMostrada = false;
		var tiempoAgotadoMostrado = false;
		
		// Función para actualizar el contador de tiempo
		function actualizarTiempo() {
			var ahora = new Date();
			var diferencia = fechaHastaEvaluacion - ahora; // Diferencia en milisegundos
			
			if (diferencia <= 0) {
				// Tiempo agotado
				$('#horas').text('0');
				$('#minutos').text('0');
				$('#segundos').text('0').css('color', 'red');
				
				if (!tiempoAgotadoMostrado) {
					tiempoAgotadoMostrado = true;
					$("#mostrarmodalZero").modal("show");
					
					// Enviar automáticamente después de 10 segundos
					setTimeout(function() {
						document.getElementById('envioauto').value = 1;
						document.evaluacionEstudiante.submit();
					}, 10000);
				}
				return;
			}
			
			// Calcular horas, minutos y segundos
			var horas = Math.floor(diferencia / (1000 * 60 * 60));
			var minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
			var segundos = Math.floor((diferencia % (1000 * 60)) / 1000);
			
			// Actualizar en el DOM
			$('#horas').text(horas);
			
			// Colores para minutos según el tiempo restante
			var colorMinutos = 'green';
			if (horas == 0) {
				if (minutos > 20) {
					colorMinutos = 'green';
				} else if (minutos > 5) {
					colorMinutos = 'orange';
				} else {
					colorMinutos = 'red';
				}
			}
			$('#minutos').html('<span style="color:' + colorMinutos + '">' + minutos + '</span>');
			
			// Colores para segundos
			var colorSegundos = 'green';
			if (horas == 0 && minutos < 1) {
				if (segundos > 30) {
					colorSegundos = 'green';
				} else if (segundos > 15) {
					colorSegundos = 'orange';
				} else {
					colorSegundos = 'red';
				}
			}
			$('#segundos').html('<span style="color:' + colorSegundos + '">' + segundos + '</span>');
			
			// Alertas de tiempo
			if (horas == 0 && minutos == 5 && segundos == 0 && !alerta5MinutosMostrada) {
				alerta5MinutosMostrada = true;
				$.toast({
					heading: 'Tiempo restante',
					text: 'Te quedan 5 minutos para finalizar la evaluación y enviarla.',
					position: 'bottom-right',
					showHideTransition: 'slide',
					loaderBg: '#FFD913',
					icon: 'warning',
					hideAfter: false
				});
				// Reproducir sonido si existe
				var audio = new Audio('../../files-general/main-app/sonidos/alerta1.mp3');
				audio.play().catch(function() { console.log('No se pudo reproducir el audio'); });
			}
			
			if (horas == 0 && minutos == 2 && segundos == 0 && !alerta2MinutosMostrada) {
				alerta2MinutosMostrada = true;
				$.toast({
					heading: 'Tiempo restante',
					text: 'Te quedan 2 minutos para finalizar la evaluación y enviarla. Te recomendamos rectificar las preguntas rápidamente y enviar la evaluación. La evaluación se enviará automáticamente con las respuestas seleccionadas.',
					position: 'bottom-right',
					showHideTransition: 'slide',
					loaderBg: '#FFD913',
					icon: 'warning',
					hideAfter: false
				});
				// Reproducir sonido si existe
				var audio = new Audio('../../files-general/main-app/sonidos/alerta1.mp3');
				audio.play().catch(function() { console.log('No se pudo reproducir el audio'); });
			}
		}
		
		// Inicializar contador de tiempo
		$(document).ready(function(){
			actualizarTiempo(); // Primera actualización inmediata
			setInterval(actualizarTiempo, 1000); // Actualizar cada segundo
		});
	</script>

	<input type="hidden" id="idE" name="idE" value="<?=$idE;?>">
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$evaluacion['eva_nombre'];?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="evaluaciones.php"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$evaluacion['eva_nombre'];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Panel de Información Colapsable -->
                            <div class="card mb-4" style="border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); cursor: pointer; border: none;" data-toggle="collapse" data-target="#panelInfoEvaluacion" aria-expanded="false" aria-controls="panelInfoEvaluacion">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-white" style="font-weight: 700;">
                                            <i class="fa fa-info-circle"></i> Información de la Evaluación
                                        </h5>
                                        <i class="fa fa-chevron-down text-white" id="iconoPanelInfo" style="transition: transform 0.3s ease;"></i>
                                    </div>
                                </div>
                                <div class="collapse" id="panelInfoEvaluacion">
                                    <div class="card-body" style="background: #f8f9fa;">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <h6 class="text-primary" style="font-weight: 600;"><?=$evaluacion['eva_nombre'];?></h6>
                                                <p class="text-muted mb-0"><?=$evaluacion['eva_descripcion'];?></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="d-flex justify-content-between align-items-center p-3" style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                                    <div>
                                                        <small class="text-muted d-block"><?=$frases[130][$datosUsuarioActual['uss_idioma']];?></small>
                                                        <strong><?=$evaluacion['eva_desde'];?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="d-flex justify-content-between align-items-center p-3" style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                                    <div>
                                                        <small class="text-muted d-block"><?=$frases[131][$datosUsuarioActual['uss_idioma']];?></small>
                                                        <strong><?=$evaluacion['eva_hasta'];?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="text-center p-3" style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                                    <div class="h4 mb-1" style="color: #667eea; font-weight: 700;"><?=$cantPreguntas;?></div>
                                                    <small class="text-muted"><?=$frases[139][$datosUsuarioActual['uss_idioma']];?></small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="text-center p-3" style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                                    <div class="h4 mb-1" style="color: #28a745; font-weight: 700;"><span id="resp">0</span></div>
                                                    <small class="text-muted"><?=$frases[141][$datosUsuarioActual['uss_idioma']];?></small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="text-center p-3" style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                                    <div class="h4 mb-1" style="color: #17a2b8; font-weight: 700;"><span id="companerosRealizando"><?=$Numerosevaluados[0];?></span></div>
                                                    <small class="text-muted">Compañeros realizando</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="text-center p-3" style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                                    <div class="h4 mb-1" style="color: #6c757d; font-weight: 700;"><span id="fin"><?=$Numerosevaluados[1];?></span></div>
                                                    <small class="text-muted"><?=$frases[142][$datosUsuarioActual['uss_idioma']];?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 col-sm-4 mb-3">
                                                <div class="text-center p-3" style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                                    <div class="h4 mb-1" style="color: #667eea; font-weight: 700;"><span id="horas"></span></div>
                                                    <small class="text-muted">Horas</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 mb-3">
                                                <div class="text-center p-3" style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                                    <div class="h4 mb-1" style="color: #667eea; font-weight: 700;"><span id="minutos"></span></div>
                                                    <small class="text-muted">Minutos</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 mb-3">
                                                <div class="text-center p-3" style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                                    <div class="h4 mb-1" style="color: #667eea; font-weight: 700;"><span id="segundos"></span></div>
                                                    <small class="text-muted">Segundos</small>
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
								<?php
								// Preparar datos de preguntas en formato JSON para JavaScript
								$respuestasMapa = Evaluaciones::traerRespuestasEvaluacionMapa($conexion, $config, $idE);
								$preguntasArray = [];
								$contPreguntas = 1;
								$preguntasConsulta = Evaluaciones::preguntasEvaluacion($conexion, $config, $idE);
								
								while($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)){
									$respuestasArray = $respuestasMapa[$preguntas['preg_id']] ?? [];
									$cantRespuestas = count($respuestasArray);
									if($cantRespuestas==0) {
										continue;
									}
									
									$respuestasData = [];
									foreach($respuestasArray as $respuestas){
										$respuestasData[] = [
											'id' => $respuestas['resp_id'],
											'descripcion' => $respuestas['resp_descripcion']
										];
									}
									
									$preguntasArray[] = [
										'numero' => $contPreguntas,
										'id' => $preguntas['preg_id'],
										'descripcion' => $preguntas['preg_descripcion'],
										'archivo' => $preguntas['preg_archivo'],
										'tipo' => $preguntas['preg_tipo_pregunta'],
										'valor' => $preguntas['preg_valor'],
										'respuestas' => $respuestasData
									];
									$contPreguntas++;
								}
								
								// Mensajes motivacionales más variados y contextuales
								$mensajesMotivacionales = [
									'¡Excelente! Sigue así 💪',
									'¡Vas muy bien! Continúa 🚀',
									'¡Genial progreso! No te detengas ⭐',
									'¡Estás haciendo un gran trabajo! 🌟',
									'¡Sigue adelante! Estás cerca del final 🎯',
									'¡Increíble! Cada pregunta te acerca más al éxito 🏆',
									'¡Fantástico! Mantén ese ritmo 💯',
									'¡Bien hecho! Sigue concentrado 🎓',
									'¡Excelente trabajo! Estás en la recta final 🏁',
									'¡Sigue así! Estás demostrando tu conocimiento 📚',
									'¡Vas por buen camino! Sigue así 🌈',
									'¡Lo estás haciendo genial! No te rindas 🎨',
									'¡Cada respuesta cuenta! Sigue adelante 🔥',
									'¡Estás brillando! Continúa así ✨',
									'¡Muy bien! Tu esfuerzo se nota 🌻',
									'¡Sigue concentrado! Lo estás logrando 🎪',
									'¡Excelente! Tu dedicación se refleja en cada respuesta 🌺',
									'¡Vas increíble! Sigue con esa energía 💫',
									'¡Estás en la zona! Mantén el foco 🎯',
									'¡Sigue así! Cada paso te acerca a la meta 🏃'
								];
								?>
								
								<!-- Barra de Progreso Mejorada -->
								<div class="card mb-4" style="border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.1); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
									<div class="card-body p-4">
										<div class="d-flex justify-content-between align-items-center mb-3">
											<h5 class="mb-0 text-white" style="font-weight: 700; font-size: 18px;">
												<i class="fa fa-tasks"></i> Progreso de la Evaluación
											</h5>
											<span id="progresoTexto" class="badge badge-light" style="font-size: 16px; padding: 10px 20px; font-weight: 700;">
												0 / <?=$cantPreguntas;?>
											</span>
										</div>
										<div class="progress" style="height: 30px; border-radius: 20px; background-color: rgba(255,255,255,0.3); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
											<div id="barraProgreso" class="progress-bar progress-bar-striped progress-bar-animated" 
												 role="progressbar" style="width: 0%; transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1); background: linear-gradient(90deg, #fff 0%, #f0f0f0 100%); box-shadow: 0 2px 10px rgba(255,255,255,0.5);" 
												 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
												<span id="porcentajeProgreso" style="line-height: 30px; font-weight: 700; color: #667eea; font-size: 14px;">0%</span>
											</div>
										</div>
									</div>
								</div>
								
								<!-- Mensaje Motivacional Mejorado (entre barra de progreso y pregunta) -->
								<div id="mensajeMotivacional" class="alert alert-info mb-4 mensaje-motivacional" style="display: none; border: none; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); animation: slideInDown 0.5s ease;">
									<div class="d-flex align-items-center">
										<div class="mr-3" style="font-size: 32px;">🎉</div>
										<div>
											<h5 class="mb-1 text-white" style="font-weight: 700;">¡Excelente progreso!</h5>
											<p class="mb-0 text-white" id="textoMotivacional" style="font-size: 16px; opacity: 0.95;"></p>
										</div>
									</div>
								</div>
								
								<!-- Contenedor de Pregunta Actual con Animación -->
								<div id="contenedorPregunta" class="card pregunta-container" style="border: none; box-shadow: 0 4px 25px rgba(0,0,0,0.12); min-height: 450px; transition: all 0.4s ease; border-radius: 15px; overflow: hidden;">
									<div class="card-body p-5">
										<!-- La pregunta se cargará aquí dinámicamente -->
									</div>
								</div>
								
								<!-- Navegación Mejorada -->
								<div class="d-flex justify-content-between align-items-center mt-4 mb-4">
									<button id="btnAnterior" class="btn btn-outline-secondary btn-lg" onclick="preguntaAnterior()" disabled style="padding: 12px 30px; font-size: 16px; border-radius: 25px; transition: all 0.3s ease; font-weight: 600;">
										<i class="fa fa-arrow-left"></i> Anterior
									</button>
									
									<div class="text-center" id="indicadorPregunta" style="flex: 1; margin: 0 20px;">
										<span class="badge badge-primary" style="font-size: 15px; padding: 14px 25px; border-radius: 25px; font-weight: 600; letter-spacing: 0.5px;">
											Pregunta <span id="numeroPreguntaActual">1</span> de <span id="totalPreguntasValidas"><?=$cantPreguntas;?></span>
										</span>
									</div>
									
									<button id="btnSiguiente" class="btn btn-primary btn-lg" onclick="preguntaSiguiente()" style="padding: 12px 30px; font-size: 16px; border-radius: 25px; transition: all 0.3s ease; font-weight: 600; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
										Siguiente <i class="fa fa-arrow-right"></i>
									</button>
									
									<button id="btnFinalizar" class="btn btn-success btn-lg" onclick="finalizarEvaluacion()" style="display: none; padding: 12px 30px; font-size: 16px; border-radius: 25px; transition: all 0.3s ease; font-weight: 700; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);">
										<i class="fa fa-check-circle"></i> <?=$frases[140][$datosUsuarioActual['uss_idioma']];?>
									</button>
								</div>
								
								<!-- Formulario oculto para envío final -->
								<form name="evaluacionEstudiante" id="formEvaluacionFinal" action="evaluaciones-guardar-respuesta.php" method="post" enctype="multipart/form-data" style="display: none;">
									<input type="hidden" id="envioauto" name="envioauto" value="0">
									<input type="hidden" name="idE" value="<?=$idE;?>">
									<input type="hidden" name="cantPreguntas" value="<?=$cantPreguntas;?>">
								</form>
								
								<script>
								// Datos de preguntas en JavaScript
								var preguntas = <?=json_encode($preguntasArray, JSON_UNESCAPED_UNICODE);?>;
								var preguntaActual = 0;
								var respuestasGuardadas = {};
								var mensajesMotivacionales = <?=json_encode($mensajesMotivacionales, JSON_UNESCAPED_UNICODE);?>;
								var idEvaluacion = <?=json_encode($idE, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
								// totalPreguntas será el número de preguntas válidas (con respuestas)
								var totalPreguntas = preguntas.length;
								
								// Inicializar primera pregunta
								$(document).ready(function(){
									// Actualizar el total de preguntas válidas en el badge
									$('#totalPreguntasValidas').text(totalPreguntas);
									
									// Asegurar que el contenedor esté visible
									$('#contenedorPregunta').show();
									
									// Cargar primera pregunta
									if (preguntas.length > 0) {
										cargarPregunta(0);
									} else {
										$('#contenedorPregunta .card-body').html('<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No hay preguntas disponibles para esta evaluación.</div>');
									}
									
									actualizarProgreso();
									
									// Rotar ícono del panel colapsable
									$('#panelInfoEvaluacion').on('show.bs.collapse', function () {
										$('#iconoPanelInfo').css('transform', 'rotate(180deg)');
									});
									$('#panelInfoEvaluacion').on('hide.bs.collapse', function () {
										$('#iconoPanelInfo').css('transform', 'rotate(0deg)');
									});
								});
								</script>
							</div>
                        </div>
                    </div>
            <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>    
			</div>
        <!-- start js include path -->
        <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
        <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
        <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
		<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
        <!-- bootstrap -->
        <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
        <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
        <!-- Common js-->
		<script src="../../config-general/assets/js/app.js" ></script>
		
		<!-- notifications -->
		<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
		<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
        
        <script src="../../config-general/assets/js/layout.js" ></script>
		<script src="../../config-general/assets/js/theme-color.js" ></script>
		<!-- Material -->
		<script src="../../config-general/assets/plugins/material/material.min.js"></script>
		<script src="../../config-general/assets/js/pages/material-select/getmdl-select.js" ></script>
    	<script  src="../../config-general/assets/plugins/material-datetimepicker/moment-with-locales.min.js"></script>
		<script  src="../../config-general/assets/plugins/material-datetimepicker/bootstrap-material-datetimepicker.js"></script>
		<script  src="../../config-general/assets/plugins/material-datetimepicker/datetimepicker.js"></script>
        
		<!-- Scripts para evaluación interactiva -->
		<style>
		@keyframes slideInDown {
			from {
				opacity: 0;
				transform: translateY(-20px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
		
		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: scale(0.95);
			}
			to {
				opacity: 1;
				transform: scale(1);
			}
		}
		
		@keyframes pulse {
			0%, 100% {
				transform: scale(1);
			}
			50% {
				transform: scale(1.05);
			}
		}
		
		.pregunta-container {
			animation: fadeIn 0.5s ease;
		}
		
		.respuesta-option {
			transition: all 0.3s ease;
		}
		
		.respuesta-option:hover {
			transform: translateX(5px);
			box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
		}
		
		.respuesta-option input[type="radio"]:checked + label {
			font-weight: 700;
			color: #667eea;
		}
		
		.btn-lg:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;
		}
		
		.btn-lg:active {
			transform: translateY(0);
		}
		</style>
		
		<script>
		// Función para cargar una pregunta específica
		function cargarPregunta(indice) {
			if (indice < 0 || indice >= preguntas.length) {
				console.error('Índice de pregunta inválido:', indice);
				return;
			}
			
			if (!preguntas || preguntas.length === 0) {
				console.error('No hay preguntas disponibles');
				return;
			}
			
			preguntaActual = indice;
			var pregunta = preguntas[indice];
			
			if (!pregunta) {
				console.error('Pregunta no encontrada en índice:', indice);
				return;
			}
			
			var html = '';
			
			// Encabezado de la pregunta mejorado
			html += '<div class="mb-4" style="animation: fadeIn 0.5s ease;">';
			html += '<div class="d-flex align-items-center mb-3">';
			html += '<div class="mr-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 20px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">';
			html += (indice + 1);
			html += '</div>';
			html += '<div>';
			html += '<h4 class="mb-1" style="color: #495057; font-weight: 700; font-size: 22px;">';
			html += '<i class="fa fa-question-circle text-primary"></i> Pregunta ' + (indice + 1) + ' de ' + totalPreguntas;
			html += '</h4>';
			html += '<small class="text-muted" style="font-size: 14px;">Valor: ' + pregunta.valor + ' puntos</small>';
			html += '</div>';
			html += '</div>';
			html += '<div class="mt-4">';
			html += '<p class="lead" style="font-size: 19px; line-height: 1.8; color: #495057; font-weight: 500;">' + pregunta.descripcion + '</p>';
			html += '</div>';
			html += '</div>';
			
			// Archivo adjunto si existe
			if (pregunta.archivo != '' && pregunta.archivo != null) {
				var extension = pregunta.archivo.split('.').pop().toLowerCase();
				if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
					html += '<div class="text-center mb-4">';
					html += '<a href="../files/evaluaciones/' + pregunta.archivo + '" target="_blank">';
					html += '<img src="../files/evaluaciones/' + pregunta.archivo + '" class="img-fluid" style="max-width: 400px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
					html += '</a>';
					html += '</div>';
				} else {
					html += '<div class="alert alert-light mb-4">';
					html += '<i class="fa fa-paperclip"></i> <b>Archivo adjunto:</b> ';
					html += '<a href="../files/evaluaciones/' + pregunta.archivo + '" target="_blank">' + pregunta.archivo + '</a>';
					html += '</div>';
				}
			}
			
			// Respuestas mejoradas
			html += '<div class="respuestas-container mt-4">';
			if (pregunta.tipo == 3) {
				// Pregunta de tipo archivo
				html += '<div class="form-group p-4" style="background: #f8f9fa; border-radius: 12px; border: 2px dashed #667eea;">';
				html += '<label class="form-label" style="font-weight: 700; font-size: 16px; color: #495057;"><i class="fa fa-upload"></i> Sube tu archivo:</label>';
				html += '<input type="file" class="form-control mt-3" id="archivo_' + pregunta.id + '" onchange="guardarRespuestaArchivo(' + pregunta.id + ', this)" style="padding: 12px; border-radius: 8px;">';
				html += '<small class="form-text text-muted mt-2"><i class="fa fa-info-circle"></i> Selecciona el archivo que deseas subir como respuesta</small>';
				html += '</div>';
			} else {
				// Preguntas de opción múltiple con mejor diseño
				pregunta.respuestas.forEach(function(respuesta, index) {
					var respuestaGuardada = respuestasGuardadas[pregunta.id];
					var checked = (respuestaGuardada == respuesta.id) ? 'checked' : '';
					var borderColor = checked ? '#667eea' : '#e9ecef';
					var bgColor = checked ? '#f0f4ff' : 'transparent';
					
					var respuestaId = 'respuesta_' + pregunta.id + '_' + respuesta.id;
					// Escapar los IDs para JavaScript (agregar comillas)
					var preguntaIdEscapado = "'" + pregunta.id + "'";
					var respuestaIdEscapado = "'" + respuesta.id + "'";
					
					html += '<div class="form-check mb-3 p-4 respuesta-option" id="option_' + respuestaId + '" style="border: 2px solid ' + borderColor + '; border-radius: 12px; background-color: ' + bgColor + '; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.05);"';
					html += ' onmouseover="if(!document.getElementById(\'' + respuestaId + '\').checked){this.style.borderColor=\'#667eea\'; this.style.backgroundColor=\'#f8f9fa\'; this.style.boxShadow=\'0 4px 15px rgba(102, 126, 234, 0.2)\';}"';
					html += ' onmouseout="if(!document.getElementById(\'' + respuestaId + '\').checked){this.style.borderColor=\'#e9ecef\'; this.style.backgroundColor=\'transparent\'; this.style.boxShadow=\'0 2px 8px rgba(0,0,0,0.05)\';}"';
					html += ' onclick="document.getElementById(\'' + respuestaId + '\').checked=true; guardarRespuesta(' + preguntaIdEscapado + ', ' + respuestaIdEscapado + '); actualizarEstiloRespuesta(this);">';
					html += '<input class="form-check-input" type="radio" name="respuesta_' + pregunta.id + '" ';
					html += 'id="' + respuestaId + '" value="' + respuesta.id + '" ' + checked + ' style="width: 20px; height: 20px; cursor: pointer;" onchange="guardarRespuesta(' + preguntaIdEscapado + ', ' + respuestaIdEscapado + '); actualizarEstiloRespuesta(document.getElementById(\'option_' + respuestaId + '\'));">';
					html += '<label class="form-check-label ml-3" for="' + respuestaId + '" style="cursor: pointer; font-size: 17px; width: 100%; line-height: 1.6; color: ' + (checked ? '#667eea' : '#495057') + '; font-weight: ' + (checked ? '600' : '400') + ';">';
					html += '<span style="display: inline-block; width: 30px; height: 30px; background: ' + (checked ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : '#e9ecef') + '; border-radius: 50%; text-align: center; line-height: 30px; margin-right: 12px; color: ' + (checked ? 'white' : '#6c757d') + '; font-weight: 700; font-size: 14px;">' + String.fromCharCode(65 + index) + '</span>';
					html += respuesta.descripcion;
					html += '</label>';
					html += '</div>';
				});
			}
			html += '</div>';
			
			// Insertar HTML directamente
			var $contenedor = $('#contenedorPregunta .card-body');
			if ($contenedor.length === 0) {
				console.error('Contenedor de pregunta no encontrado');
				return;
			}
			
			$contenedor.html(html);
			
			// Asegurar que el contenedor esté visible
			$('#contenedorPregunta').show();
			
			// Aplicar estilo a la respuesta seleccionada si existe
			if (pregunta.tipo != 3) {
				// Esperar un momento para que el DOM se actualice
				setTimeout(function() {
					// Primero desmarcar todos los radio buttons de esta pregunta
					$('input[name="respuesta_' + pregunta.id + '"]').prop('checked', false);
					
					// Remover estilos de todas las opciones
					$('.respuesta-option').each(function() {
						var $radio = $(this).find('input[name="respuesta_' + pregunta.id + '"]');
						if ($radio.length > 0) {
							$(this).css({
								'border-color': '#e9ecef',
								'background-color': 'transparent',
								'box-shadow': '0 2px 8px rgba(0,0,0,0.05)'
							});
							$(this).find('label').css({
								'color': '#495057',
								'font-weight': '400'
							});
							$(this).find('span').css({
								'background': '#e9ecef',
								'color': '#6c757d'
							});
						}
					});
					
					// Luego marcar y estilizar solo la respuesta guardada
					var respuestaGuardada = respuestasGuardadas[pregunta.id];
					if (respuestaGuardada && respuestaGuardada != '0') {
						var $radioSeleccionado = $('input[name="respuesta_' + pregunta.id + '"][value="' + respuestaGuardada + '"]');
						if ($radioSeleccionado.length > 0) {
							$radioSeleccionado.prop('checked', true);
							var $respuestaSeleccionada = $radioSeleccionado.closest('.respuesta-option');
							if ($respuestaSeleccionada.length > 0) {
								actualizarEstiloRespuesta($respuestaSeleccionada[0]);
							}
						}
					}
				}, 150);
			}
			
			// Actualizar botones de navegación
			$('#btnAnterior').prop('disabled', indice === 0);
			$('#btnSiguiente').toggle(indice < preguntas.length - 1);
			$('#btnFinalizar').toggle(indice === preguntas.length - 1);
			
			// Actualizar número de pregunta actual
			$('#numeroPreguntaActual').text(indice + 1);
			
			// Reinicializar Material Design para los radio buttons
			if (typeof componentHandler !== 'undefined') {
				componentHandler.upgradeDom();
			}
		}
		
		// Función para actualizar estilo de respuesta seleccionada
		function actualizarEstiloRespuesta(elemento) {
			// Obtener el ID de la pregunta desde el elemento
			var preguntaId = $(elemento).find('input[type="radio"]').attr('name');
			if (preguntaId) {
				preguntaId = preguntaId.replace('respuesta_', '');
			}
			
			// Remover estilo de todas las respuestas de esta pregunta
			$(elemento).siblings().each(function() {
				$(this).css({
					'border-color': '#e9ecef',
					'background-color': 'transparent',
					'box-shadow': '0 2px 8px rgba(0,0,0,0.05)'
				});
				$(this).find('label').css({
					'color': '#495057',
					'font-weight': '400'
				});
				$(this).find('span').css({
					'background': '#e9ecef',
					'color': '#6c757d'
				});
			});
			
			// Aplicar estilo a la respuesta seleccionada
			$(elemento).css({
				'border-color': '#667eea',
				'background-color': '#f0f4ff',
				'box-shadow': '0 4px 15px rgba(102, 126, 234, 0.3)'
			});
			$(elemento).find('label').css({
				'color': '#667eea',
				'font-weight': '600'
			});
			$(elemento).find('span').css({
				'background': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
				'color': 'white'
			});
		}
		
		// Función para guardar respuesta (opción múltiple)
		function guardarRespuesta(idPregunta, idRespuesta) {
			// Actualizar el objeto de respuestas guardadas
			respuestasGuardadas[idPregunta] = idRespuesta;
			
			// Desmarcar visualmente todas las respuestas de esta pregunta primero
			$('input[name="respuesta_' + idPregunta + '"]').prop('checked', false);
			$('input[name="respuesta_' + idPregunta + '"]').closest('.respuesta-option').each(function() {
				$(this).css({
					'border-color': '#e9ecef',
					'background-color': 'transparent',
					'box-shadow': '0 2px 8px rgba(0,0,0,0.05)'
				});
				$(this).find('label').css({
					'color': '#495057',
					'font-weight': '400'
				});
				$(this).find('span').css({
					'background': '#e9ecef',
					'color': '#6c757d'
				});
			});
			
			// Marcar la nueva respuesta seleccionada
			var $radioSeleccionado = $('input[name="respuesta_' + idPregunta + '"][value="' + idRespuesta + '"]');
			if ($radioSeleccionado.length > 0) {
				$radioSeleccionado.prop('checked', true);
				var $respuestaSeleccionada = $radioSeleccionado.closest('.respuesta-option');
				if ($respuestaSeleccionada.length > 0) {
					actualizarEstiloRespuesta($respuestaSeleccionada[0]);
				}
			}
			
			// Guardar en servidor
			$.ajax({
				url: 'ajax-guardar-respuesta-individual.php',
				type: 'POST',
				data: {
					idE: idEvaluacion,
					idPregunta: idPregunta,
					idRespuesta: idRespuesta
				},
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						// Respuesta guardada correctamente - mostrar feedback visual sutil
						var $preguntaCard = $('#contenedorPregunta');
						$preguntaCard.css('box-shadow', '0 4px 25px rgba(40, 167, 69, 0.3)');
						setTimeout(function() {
							$preguntaCard.css('box-shadow', '0 4px 25px rgba(0,0,0,0.12)');
						}, 500);
					}
				},
				error: function() {
					console.error('Error al guardar respuesta');
				}
			});
			
			actualizarProgreso();
		}
		
		// Función para guardar respuesta de archivo
		function guardarRespuestaArchivo(idPregunta, inputFile) {
			var formData = new FormData();
			formData.append('idE', idEvaluacion);
			formData.append('idPregunta', idPregunta);
			formData.append('idRespuesta', '0');
			formData.append('archivo', inputFile.files[0]);
			
			$.ajax({
				url: 'ajax-guardar-respuesta-individual.php',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function(response) {
					if (response.success) {
						$.toast({
							heading: 'Archivo guardado',
							text: 'Tu archivo se ha guardado correctamente',
							position: 'top-right',
							loaderBg: '#26c281',
							icon: 'success',
							hideAfter: 2000
						});
						actualizarProgreso();
					}
				},
				error: function() {
					$.toast({
						heading: 'Error',
						text: 'No se pudo guardar el archivo',
						position: 'top-right',
						loaderBg: '#bf441d',
						icon: 'error',
						hideAfter: 3000
					});
				}
			});
		}
		
		// Función para avanzar a la siguiente pregunta
		function preguntaSiguiente() {
			if (preguntaActual < preguntas.length - 1) {
				// Verificar si hay respuesta guardada antes de avanzar
				var pregunta = preguntas[preguntaActual];
				
				// Verificar tanto en respuestasGuardadas como en el DOM (radio buttons)
				var tieneRespuestaGuardada = respuestasGuardadas[pregunta.id] && respuestasGuardadas[pregunta.id] != '0';
				var tieneRadioSeleccionado = $('input[name="respuesta_' + pregunta.id + '"]:checked').length > 0;
				
				if (!tieneRespuestaGuardada && !tieneRadioSeleccionado && pregunta.tipo != 3) {
					if (!confirm('No has seleccionado una respuesta para esta pregunta. ¿Deseas continuar de todas formas?')) {
						return;
					}
				}
				
				// Si hay radio seleccionado pero no está en respuestasGuardadas, guardarlo
				if (tieneRadioSeleccionado && !tieneRespuestaGuardada) {
					var idRespuestaSeleccionada = $('input[name="respuesta_' + pregunta.id + '"]:checked').val();
					guardarRespuesta(pregunta.id, idRespuestaSeleccionada);
				}
				
				// Efecto de fade out antes de cambiar
				$('#contenedorPregunta').fadeOut(200, function() {
					// Mostrar mensaje motivacional mejorado
					var mensajeAleatorio = mensajesMotivacionales[Math.floor(Math.random() * mensajesMotivacionales.length)];
					$('#textoMotivacional').text(mensajeAleatorio);
					$('#mensajeMotivacional').fadeIn(400);
					
					// Scroll suave hacia el mensaje motivacional
					$('html, body').animate({
						scrollTop: $('#mensajeMotivacional').offset().top - 20
					}, 600);
					
					// Cargar siguiente pregunta después de un breve delay
					setTimeout(function() {
						cargarPregunta(preguntaActual + 1);
						actualizarProgreso();
					}, 300);
					
					// Ocultar mensaje después de 3 segundos
					setTimeout(function() {
						$('#mensajeMotivacional').fadeOut(400);
					}, 3000);
				});
			}
		}
		
		// Función para volver a la pregunta anterior
		function preguntaAnterior() {
			if (preguntaActual > 0) {
				// Efecto de fade out antes de cambiar
				$('#contenedorPregunta').fadeOut(200, function() {
					cargarPregunta(preguntaActual - 1);
					actualizarProgreso();
					
					// Scroll suave hacia arriba
					$('html, body').animate({scrollTop: 0}, 500);
				});
			}
		}
		
		// Función para actualizar la barra de progreso
		function actualizarProgreso() {
			var respondidas = Object.keys(respuestasGuardadas).length;
			var porcentaje = totalPreguntas > 0 ? Math.round((respondidas / totalPreguntas) * 100) : 0;
			
			// Animación suave de la barra de progreso
			$('#barraProgreso').animate({
				width: porcentaje + '%'
			}, {
				duration: 800,
				easing: 'swing',
				step: function(now) {
					$(this).attr('aria-valuenow', Math.round(now));
				},
				complete: function() {
					$('#porcentajeProgreso').text(porcentaje + '%');
				}
			});
			
			$('#progresoTexto').text(respondidas + ' / ' + totalPreguntas);
			
			// Actualizar contador de respuestas en el sidebar con animación
			$('#resp').fadeOut(200, function() {
				$(this).text(respondidas).fadeIn(200);
			});
			
			// Cambiar color de la barra según el progreso
			if (porcentaje >= 75) {
				$('#barraProgreso').removeClass('bg-primary').css('background', 'linear-gradient(90deg, #28a745 0%, #20c997 100%)');
			} else if (porcentaje >= 50) {
				$('#barraProgreso').removeClass('bg-primary').css('background', 'linear-gradient(90deg, #17a2b8 0%, #20c997 100%)');
			} else {
				$('#barraProgreso').css('background', 'linear-gradient(90deg, #fff 0%, #f0f0f0 100%)');
			}
		}
		
		
		// Función para finalizar evaluación
		function finalizarEvaluacion() {
			// Guardar respuesta de la pregunta actual si hay una seleccionada
			var preguntaActualObj = preguntas[preguntaActual];
			if (preguntaActualObj) {
				var tieneRadioSeleccionado = $('input[name="respuesta_' + preguntaActualObj.id + '"]:checked').length > 0;
				if (tieneRadioSeleccionado && !respuestasGuardadas[preguntaActualObj.id]) {
					var idRespuestaSeleccionada = $('input[name="respuesta_' + preguntaActualObj.id + '"]:checked').val();
					guardarRespuesta(preguntaActualObj.id, idRespuestaSeleccionada);
					// Esperar un momento para que se guarde
					setTimeout(function() {
						procesarFinalizacion();
					}, 500);
					return;
				}
			}
			
			procesarFinalizacion();
		}
		
		// Función auxiliar para procesar la finalización
		function procesarFinalizacion() {
			var respondidas = Object.keys(respuestasGuardadas).length;
			
			if (respondidas < totalPreguntas) {
				if (!confirm('Tienes ' + (totalPreguntas - respondidas) + ' pregunta(s) sin responder. ¿Deseas finalizar la evaluación de todas formas?')) {
					return;
				}
			} else {
				if (!confirm('Te recomendamos verificar que todas las preguntas estén contestadas antes de enviar. Si ya lo hiciste puedes continuar. ¿Deseas enviar la evaluación?')) {
					return;
				}
			}
			
			// Limpiar campos anteriores si existen
			$('#formEvaluacionFinal input[name^="P"], #formEvaluacionFinal input[name^="R"]').remove();
			
			// Preparar formulario con todas las respuestas en orden secuencial
			// IMPORTANTE: El orden debe coincidir con el orden en que PHP procesa las preguntas
			// (solo las preguntas que tienen respuestas, en el mismo orden que aparecen en el array preguntas)
			var form = $('#formEvaluacionFinal');
			var contadorPregunta = 1;
			
			// Iterar sobre todas las preguntas (que ya están filtradas para tener respuestas)
			for (var i = 0; i < preguntas.length; i++) {
				var pregunta = preguntas[i];
				var respuestaId = respuestasGuardadas[pregunta.id];
				
				// Si no hay respuesta guardada, usar '0'
				if (!respuestaId || respuestaId === undefined || respuestaId === null || respuestaId === '') {
					respuestaId = '0';
				}
				
				// Agregar campos P (pregunta) y R (respuesta) con el contador secuencial
				// El contador debe coincidir con el $contPreguntas en PHP
				var campoP = $('<input>').attr({
					type: 'hidden',
					name: 'P' + contadorPregunta,
					value: pregunta.id
				});
				var campoR = $('<input>').attr({
					type: 'hidden',
					name: 'R' + contadorPregunta,
					value: respuestaId
				});
				
				form.append(campoP);
				form.append(campoR);
				
				contadorPregunta++;
			}
			
			// Enviar formulario
			form.submit();
		}
		</script>
        <!-- end js include path -->
</body>


</html>