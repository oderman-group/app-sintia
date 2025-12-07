<?php include("session.php");?>
<?php include("verificar-usuario.php");?>
<?php $idPaginaInterna = 'ES0019';
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<script type="text/javascript">
	function horas(){
  	var eva = <?=$_GET["idE"];?>;
	var time = 1;
	  $('#horas').empty().hide().html("...").show(1);
		datos = "eva="+(eva)+
				"&time="+(time);
			   $.ajax({
				   type: "POST",
				   url: "../compartido/ajax-evaluacion-tiempo.php",
				   data: datos,
				   success: function(data){
				   $('#horas').empty().hide().html(data).show(1);
				   }
			   });

	}
	setInterval('horas()',10000);
	
	function minutos(){
  	var eva = <?=$_GET["idE"];?>;
	var time = 2;
	  $('#minutos').empty().hide().html("...").show(1);
		datos = "eva="+(eva)+
				"&time="+(time);
			   $.ajax({
				   type: "POST",
				   url: "../compartido/ajax-evaluacion-tiempo.php",
				   data: datos,
				   success: function(data){
				   $('#minutos').empty().hide().html(data).show(1);
				   }
			   });

	}
	setInterval('minutos()',2000);
	
	
	function segundos(){
  	var eva = <?=$_GET["idE"];?>;
	var time = 3;
	  $('#segundos').empty().hide().html("...").show(1);
		datos = "eva="+(eva)+
				"&time="+(time);
			   $.ajax({
				   type: "POST",
				   url: "../compartido/ajax-evaluacion-tiempo.php",
				   data: datos,
				   success: function(data){
				   $('#segundos').empty().hide().html(data).show(1);
				   }
			   });

	}
	setInterval('segundos()',1000);
	
	window.onload = horas();
	window.onload = minutos();
	window.onload = segundos();
</script>

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
	
	?>

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
                        <div class="col-md-3">
                            <!-- BEGIN PROFILE SIDEBAR -->
                            <div class="profile-sidebar" style="position: sticky; top:0;">
                                <div class="card" >
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
                                                <div class="uppercase profile-stat-title" style="color: chartreuse;"> <span id="resp"></span> </div>
                                                <div class="uppercase profile-stat-text"> <?=$frases[141][$datosUsuarioActual['uss_idioma']];?> </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-6">
                                                <div class="uppercase profile-stat-title"> <span id="fin"></span> </div>
                                                <div class="uppercase profile-stat-text"> <?=$frases[142][$datosUsuarioActual['uss_idioma']];?> </div>
                                            </div>
                                        </div>
										
										<div class="row list-separated profile-stat">
											<div class="col-md-4 col-sm-4 col-6">
                                                <div class="uppercase profile-stat-title"> <span id="horas"></span> </div>
                                                <div class="uppercase profile-stat-text"> Horas </div>
                                            </div>
											<div class="col-md-4 col-sm-4 col-6">
                                                <div class="uppercase profile-stat-title"> <span id="minutos"></span> </div>
                                                <div class="uppercase profile-stat-text"> Minutos </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-6">
                                                <div class="uppercase profile-stat-title"> <span id="segundos"></span> </div>
                                                <div class="uppercase profile-stat-text"> Segundos </div>
                                            </div>

                                        </div>
										
                                    </div>
                                </div>
                            </div>
                            <!-- END BEGIN PROFILE SIDEBAR -->
                            </div>
						
							<div class="col-md-6">
									<form name="evaluacionEstudiante" action="evaluaciones-guardar-respuesta.php" method="post" enctype="multipart/form-data">
										<input type="hidden" id="envioauto" name="envioauto" value="0">
										<input type="hidden" name="idE" value="<?=$idE;?>">
										<input type="hidden" name="cantPreguntas" value="<?=$cantPreguntas;?>">
										
									
											<?php
											// OPTIMIZACIÓN: Pre-cargar todas las respuestas de todas las preguntas en un mapa
											// para evitar N+1 queries dentro del bucle
											$respuestasMapa = Evaluaciones::traerRespuestasEvaluacionMapa($conexion, $config, $idE);
											
											$contPreguntas = 1;
											$preguntasConsulta = Evaluaciones::preguntasEvaluacion($conexion, $config, $idE);
											while($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)){
												// OPTIMIZACIÓN: Obtener respuestas del mapa pre-cargado
												$respuestasArray = $respuestasMapa[$preguntas['preg_id']] ?? [];
												
												$cantRespuestas = count($respuestasArray);
												if($cantRespuestas==0) {
													echo "<hr><span style='color:red';>".$frases[146][$datosUsuarioActual['uss_idioma']].".</span>";
													continue;
												}
											?>
												
											<div class="panel">
												<header class="panel-heading panel-heading-blue"><?php echo $preguntas['preg_descripcion'];?> </header>
												<div class="panel-body">
													
													<?php 
													if($preguntas['preg_archivo']!="" and file_exists('../files/evaluaciones/'.$preguntas['preg_archivo'])){
														$extension = new SplFileInfo($preguntas['preg_archivo']);
														$ext = $extension->getExtension();
														if($ext == 'jpg' or $ext == 'jpeg' or $ext == 'png' or $ext == 'gif'){
															echo '
																<p align="center">
																	<a href="../files/evaluaciones/'.$preguntas['preg_archivo'].'" target="_blank">
																		<img src="../files/evaluaciones/'.$preguntas['preg_archivo'].'" width="200">
																	</a>
																</p>
															';
														}else{
													?>
														<hr>
														<p align="left"><b>Archivo adjunto:</b> <a href="../files/evaluaciones/<?=$preguntas['preg_archivo'];?>" target="_blank"><?=$preguntas['preg_archivo'];?></a></p>
													<?php 
														}
													
													}
													?>
													
											<?php 
												$contRespuestas = 1;
												// OPTIMIZACIÓN: Iterar sobre el array pre-cargado en lugar de hacer consulta
												foreach($respuestasArray as $respuestas){
											?>
												<div>
													
														<input type="hidden" value="<?=$preguntas['preg_id'];?>" name="P<?=$contPreguntas;?>">
														<?php if($preguntas['preg_tipo_pregunta']==3){?>
															<div style="display: none">
																<input type="radio" id="option-<?=$contPreguntas;?><?=$contRespuestas;?>" class="mdl-radio__button" name="R<?=$contPreguntas;?>" value="<?php echo $respuestas['resp_id'];?>" checked>
															</div>
															<input type="file" name="file<?=$preguntas['preg_id'];?>" class="form-control" autocomplete="off">
														<?php }else{?>
															<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-<?=$contPreguntas;?><?=$contRespuestas;?>">
																<input type="radio" id="option-<?=$contPreguntas;?><?=$contRespuestas;?>" class="mdl-radio__button" name="R<?=$contPreguntas;?>" value="<?php echo $respuestas['resp_id'];?>">
																
															</label>
															<span class="mdl-radio__label"><?php echo $respuestas['resp_descripcion'];?></span>
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
												$contPreguntas ++;
											}
											?>
											<hr>
											<?php
											//MOSTRAMOS EL BOTÓN FINALIZAR SÓLO SI HAY PREGUNTAS EN LA EVALUACIÓN
											if($cantPreguntas>0){
											?>
												<div align="right"><button id="btnEvaluacion" class="btn btn-primary" type="submit" onClick="if(!confirm('Te recomendamos verificar que todas las preguntas estén contestadas antes de enviar. Si ya lo hiciste puedes continuar. Deseas enviar la evaluación?')){return false;}"><?=$frases[140][$datosUsuarioActual['uss_idioma']];?></button></div>
											<?php }?>

									</form>
								
								</div>
						
								<div class="col-md-3">
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[143][$datosUsuarioActual['uss_idioma']];?></header>
                                        <div class="panel-body">
											<p><b>1.</b> <?=$frases[147][$datosUsuarioActual['uss_idioma']];?></p>
											
											<p><b>2.</b> <?=$frases[148][$datosUsuarioActual['uss_idioma']];?></p>
											
											<p><b>3.</b> <?=$frases[149][$datosUsuarioActual['uss_idioma']];?></p>
											
											<p><b>4.</b> <?=$frases[161][$datosUsuarioActual['uss_idioma']];?></p>
										</div>
									</div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
											
												<p><b><?=$frases[141][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[144][$datosUsuarioActual['uss_idioma']];?></p>
											
												<p><b><?=$frases[142][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[145][$datosUsuarioActual['uss_idioma']];?></p>
										</div>
									</div>
									
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
        <!-- end js include path -->
</body>


</html>