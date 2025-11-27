<?php
include("session.php");
$idPaginaInterna = 'DC0017';
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
include("../compartido/head.php");

$idE="";
if(!empty($_GET["idE"])){ $idE=base64_decode($_GET["idE"]);}
?>
<script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js"></script>

<script type="text/javascript">
function guardarAjax(datos) {
    var valor     = datos.value;
    var operacion = datos.alt;
    var pregunta  = datos.title;

    $('#respuestaGuardar').empty().hide().html("Guardando información, espere por favor...").show(1);

    datos = "valor="+(valor)+
    "&pregunta="+(pregunta)+
    "&operacion="+(operacion);

    $.ajax({
        type: "POST",
        url: "ajax-guardar.php",
        data: datos,
        success: function(data) {
            $('#respuestaGuardar').empty().hide().html(data).show(1);
        }
    });
}

function mostrarNuevaRespuesta(datos){ 
  	var id = "pr"+datos.id;
	document.getElementById(id).style.display="block";
}
	

  function realizando(){
  	var eva = '<?=$idE;?>';
	var consulta = 1;
	  $('#resp').empty().hide().html("...").show(1);
		datos = "eva="+(eva)+
				"&consulta="+(consulta);
			   $.ajax({
				   type: "POST",
				   url: "../compartido/ajax-evaluacion.php",
				   data: datos,
				   success: function(data){
				   $('#resp').empty().hide().html(data).show(1);
				   }
			   });

	}
	//setInterval('realizando()',5000);
	
	function finalizado(){
  	var eva = '<?=$idE;?>';	
	var consulta = 2;
	  $('#fin').empty().hide().html("...").show(1);
		datos = "eva="+(eva)+
				"&consulta="+(consulta);
			   $.ajax({
				   type: "POST",
				   url: "../compartido/ajax-evaluacion.php",
				   data: datos,
				   success: function(data){
				   $('#fin').empty().hide().html(data).show(1);
				   }
			   });

	}
	//setInterval('finalizado()',5000);
	
	window.onload = realizando();
	window.onload = finalizado();
</script>

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
	
	<?php
	$evaluacion = Evaluaciones::consultaEvaluacion($conexion, $config, $idE);

	//Cantidad de preguntas de la evaluación
	$cantPreguntas = Evaluaciones::numeroPreguntasEvaluacion($conexion, $config, $idE);

	?>

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
					<?php include(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                    <div class="row">

							<div class="col-md-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?></header>
                                        <div class="panel-body">
												<p><b><?=$frases[141][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[144][$datosUsuarioActual['uss_idioma']];?></p>
											
												<p><b><?=$frases[142][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[145][$datosUsuarioActual['uss_idioma']];?></p>
											
												<p><b>Respuesta correcta:</b> Haciendo click sobre el icono <i class="fa fa-exchange"></i> al lado de la respuesta para marcarlas como correctas o incorrectas. De color verde se resalatan las correctas y de color rojo las incorrectas.</p>
										</div>
									</div>
								
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$evaluacionesEnComun = Evaluaciones::consultaEvaluacionTodas($conexion, $config, $idE, $cargaConsultaActual, $periodoConsultaActual);
											while($evaComun = mysqli_fetch_array($evaluacionesEnComun, MYSQLI_BOTH)){
											?>
												<p><a href="evaluaciones-preguntas.php?idE=<?=base64_encode($evaComun['eva_id']);?>"><?=$evaComun['eva_nombre'];?></a></p>
											<?php }?>
										</div>
                                    </div>
									
							</div>
							
							<div class="col-md-6" style="width: 100%; height: 800px; overflow-y: scroll;">
								
								
								
								<div class="row" style="margin-bottom: 10px;">
									<div class="col-sm-12">
										<a href="evaluaciones.php" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i>Regresar</a>
										
										<div class="btn-group">
											<a href="preguntas-agregar.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>&idE=<?=$_GET["idE"];?>" id="addRow" class="btn deepPink-bgcolor"><i class="fa fa-plus"></i> Agregar pregunta </a>
										</div>
									</div>
								</div>
								
								<span id="respuestaGuardar"></span>
								
									<?php
									$arrayEnviar = array("tipo"=>3, "descripcionTipo"=>"Para ocultar fila del registro.");
									$arrayDatos = json_encode($arrayEnviar);
									$objetoEnviar = htmlentities($arrayDatos);
									?>
								
									<form action="#" method="post">
										<input type="hidden" name="id" value="9">
										<input type="hidden" name="idE" value="<?=$idE;?>">
										<input type="hidden" name="cantPreguntas" value="<?=$cantPreguntas;?>">
											<?php
											$puntosSumados = 0;
											$totalPuntos = 0;
											$contPreguntas = 1;
											
											// PRE-CARGAR TODAS LAS RESPUESTAS DE TODAS LAS PREGUNTAS
											// EN UNA SOLA CONSULTA PARA EVITAR N+1 QUERIES
											// ============================================
											$respuestasMapa = Evaluaciones::traerRespuestasEvaluacionMapa($conexion, $config, $idE);
											
											$preguntasConsulta = Evaluaciones::preguntasEvaluacion($conexion, $config, $idE);
											while($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)){
												// Obtener respuestas del mapa pre-cargado
												$respuestasPregunta = $respuestasMapa[$preguntas['preg_id']] ?? [];
												$cantRespuestas = count($respuestasPregunta);
												
												$totalPuntos +=$preguntas['preg_valor'];
											?>
												<div class="panel" id="pregunta<?=$preguntas['preg_id'];?>">
													<div class="card-head">
																		<button type="button" id ="panel-<?=$preguntas['preg_id'];?>"
																		   class = "mdl-button mdl-js-button mdl-button--icon pull-right" 
																		   data-upgraded = ",MaterialButton">
																		   <i class = "material-icons">more_vert</i>
																		</button>

																		<ul class = "mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect"
																		   data-mdl-for="panel-<?=$preguntas['preg_id'];?>">
																			
																			<?php if($preguntas['preg_tipo_pregunta']!=3){?>
																		   	<li class = "mdl-menu__item"><a href="#" id="<?=$preguntas['preg_id'];?>" onClick="mostrarNuevaRespuesta(this)"><i class="fa fa-plus-circle"></i> Agregar respuesta</a></li>
																			<?php }?>
																			
																		   <li class = "mdl-menu__item"><a href="preguntas-editar.php?idR=<?=base64_encode($preguntas['preg_id']);?>&idE=<?=$_GET["idE"];?>"><i class="fa fa-edit"></i> Editar pregunta</a></li>
																		   
																			<li class = "mdl-menu__item"><a href="#" title="<?=$objetoEnviar;?>" id="<?=$preguntas['preg_id'];?>" name="evaluaciones-preguntas-eliminar.php?idP=<?=base64_encode($preguntas['preg_id']);?>&idE=<?=$_GET["idE"];?>" onClick="deseaEliminar(this)"><i class="fa fa-trash"></i>Eliminar pregunta</a></li>
																		</ul>
													</div>
													
													<header class="panel-heading panel-heading-blue"><?php echo $preguntas['preg_descripcion'];?></header>
													<div class="panel-body">
														
													<?php 
													if(!empty($preguntas['preg_archivo']) and file_exists('../files/evaluaciones/'.$preguntas['preg_archivo'])){
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
														$arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
														$arrayDatos = json_encode($arrayEnviar);
														$objetoEnviar = htmlentities($arrayDatos);
														?>
														
											<?php 
												$contRespuestas = 1;
												// Iterar sobre el array pre-cargado en lugar de hacer consulta
												foreach($respuestasPregunta as $respuestas){
													if($respuestas['resp_correcta']==1) {$colorRespuesta = 'green';} else {$colorRespuesta = 'red';}
													if($respuestas['resp_correcta']==1 and !empty($compararRespuestas[0])){
														$puntosSumados += $preguntas['preg_valor'];
													}
											?>
												
														
												<p id="reg<?=$respuestas['resp_id'];?>">	
													<a href="#" title="<?=$objetoEnviar;?>" id="<?=$respuestas['resp_id'];?>" name="evaluaciones-respuestas-eliminar.php?idR=<?=base64_encode($respuestas['resp_id'] ?? '');?>&estado=<?=base64_encode($respuestas['resp_correcta'] ?? '');?>&preg=<?=base64_encode($preguntas['preg_id'] ?? '');?>&idE=<?=$_GET["idE"] ?? '';?>" onClick="deseaEliminar(this)"><i class="fa fa-times-circle"></i></a>
													
													<a href="evaluaciones-respuestas-cambiar-estado.php?idR=<?=base64_encode($respuestas['resp_id'] ?? '');?>&estado=<?=base64_encode($respuestas['resp_correcta'] ?? '');?>&preg=<?=base64_encode($preguntas['preg_id'] ?? '');?>&idE=<?=$_GET["idE"] ?? '';?>">
														<i class="fa fa-exchange"></i>
													</a>
													
													<input style="width: 100%; border: thin; border-style: solid; border-color: <?=$colorRespuesta;?>;" id="<?=$respuestas['resp_id'];?>" value="<?=$respuestas['resp_descripcion'];?>" title="<?=$preguntas['preg_id'];?>" alt="1" onChange="guardarAjax(this)">													
												</p>
											<?php
													$contRespuestas ++;
												}
											?>
													<div id="pr<?=$preguntas['preg_id'];?>" style="display: none;">
														<hr>
														<b>Nueva respuesta:</b><br>
														<input 
															size="65" 
															placeholder="Escriba la respuesta y Enter para guardar" 
															alt="2" 
															title="<?=$preguntas['preg_id'];?>" 
															onChange="guardarAjax(this)"
														>
													</div>
											<?php		
												$contPreguntas ++;
											?>
														<p align="right" style="font-size: 12px; color: cadetblue;"><?=$preguntas['preg_valor'];?> puntos</p>
														
													</div>
												</div>
											<?php			
											}
											?>

										
									</form>
								
								
								</div>
						

									
								<div class="col-md-3">
									<?php include("../compartido/publicidad-lateral.php");?>
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