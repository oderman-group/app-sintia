<?php include("session.php");?>
<?php include("verificar-usuario.php");?>
<?php $idPaginaInterna = 'ES0006';
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-pagina-bloqueada.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");?>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
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
                                <div class="page-title"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[106][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
											<?php
											$porcentaje = 0;
											for($i=1; $i<=$datosEstudianteActual['gra_periodos']; $i++){
												$periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosEstudianteActual['mat_grado'], $i);
												
												$porcentajeGrado=25;
												if(!empty($periodosCursos['gvp_valor'])){
													$porcentajeGrado=$periodosCursos['gvp_valor'];
												}
												
												if($i==$periodoConsultaActual) $estiloResaltadoP = 'style="color: orange;"'; else $estiloResaltadoP = '';
											?>
												<p>
													<a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($i);?>" <?=$estiloResaltadoP;?>><?=strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]);?> <?=$i;?> (<?=$porcentajeGrado;?>%)</a>
												</p>
											<?php }?>
										
										</div>
									</div>
								
							
									<?php include("filtro-cargas.php");?>
									
								</div>
									
								<div class="col-md-9">
									<div class="card card-box">
										<div class="card-head">
											<header><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></header>
											<div class="tools">
												<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
												<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
												<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
											</div>
										</div>
										<div class="card-body" id="line-parent">
											<div class="panel-group accordion" id="accordion3">
												<?php
												$consulta= Evaluaciones::consultaEvaluacionCargasPeriodos($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
												  while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													  if(!empty($resultado['eva_clave'])) $ulrEva = 'evaluaciones-clave.php'; else $ulrEva = 'evaluaciones-realizar.php';
													
													//Cantidad de preguntas de la evaluación
													$cantPreguntas = Evaluaciones::numeroPreguntasEvaluacion($conexion, $config, $resultado['eva_id']);
													  
													  //Obtener los datos si ya ha realizado la evaluación
													  $datosTerminada = Evaluaciones::traerDatosEvaluacionTerminada($conexion, $config, $resultado['eva_id'], $datosEstudianteActual['mat_id']);
													  
													  //respuestas
														$respuestasEvaluacion = Evaluaciones::traerRespuestaEvaluacion($conexion, $config, $resultado['eva_id'], $datosEstudianteActual['mat_id']);
												 ?>
												  <div class="panel panel-default">
													  <div class="panel-heading panel-heading-gray">
														  <h4 class="panel-title">
															  <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse<?=$resultado['eva_id'];?>"> 
																  <?=$resultado['eva_nombre'];?> 
																  <?php if(!empty($datosTerminada[0])){?><i class="fa fa-check-circle"></i><?php }?> 
															  </a>
														  </h4>
													  </div>
													  <div id="collapse<?=$resultado['eva_id'];?>" class="panel-collapse collapse">
														  <div class="panel-body">
															  <p><?=$resultado['eva_descripcion'];?></p>
															  
															  <p>
																  <b><?=$frases[139][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$cantPreguntas;?><br>
																  <b><?=$frases[130][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$resultado['eva_desde'];?><br>
																  <b><?=$frases[131][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$resultado['eva_hasta'];?>
															  </p>
															  
															  <p>
																  <?php if(empty($datosTerminada[0])){?>
																  	<a class="btn red" href="<?=$ulrEva;?>?idE=<?=base64_encode($resultado['eva_id']);?>"><?=strtoupper($frases[129][$datosUsuarioActual['uss_idioma']]);?></a>
																  <?php }else{?>
																  <hr>
															  		<h4>RESULTADOS</h4>
																	<b>Iniciaste:</b> <?=$datosTerminada['epe_inicio'];?><br>
															  		<b>Finalizaste:</b> <?=$datosTerminada['epe_fin'];?><br>
															  		<b>Preguntas correctas:</b> <?=$respuestasEvaluacion[0];?><br>
															  		<b>Preguntas incorrectas:</b> <?=$respuestasEvaluacion[1];?><br>
															  		<b>Preguntas sin contestar:</b> <?=$respuestasEvaluacion[2];?><br>
															  
															  		<p><a class="btn blue" href="evaluaciones-ver.php?idE=<?=base64_encode($resultado['eva_id']);?>"><?=strtoupper($frases[154][$datosUsuarioActual['uss_idioma']]);?></a></p>
															  
																  <?php }?>
															  </p>
															  
														  </div>
													  </div>
												  </div>
												<?php }?>
												
											  </div>
										</div>
									</div>
                                </div>
						
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
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
</body>

</html>