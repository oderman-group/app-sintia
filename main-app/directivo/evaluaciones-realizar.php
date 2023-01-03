<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0088';?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<script type="text/javascript">
  function realizando(){
  	var eva = <?=$_GET["idE"];?>;
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
	setInterval('realizando()',5000);
	
	function finalizado(){
  	var eva = <?=$_GET["idE"];?>;	
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
	setInterval('finalizado()',5000);
	
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
	window.onload = realizando();
	window.onload = finalizado();
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
	$consultaEvaluacion=mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones 
	WHERE eva_id='".$_GET["idE"]."' AND eva_estado=1");
	$evaluacion = mysqli_fetch_array($consultaEvaluacion, MYSQLI_BOTH);

	if($evaluacion[0]==""){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=106";</script>';
		exit();
	}
	
	$consultaFecha=mysqli_query($conexion, "SELECT DATEDIFF(eva_desde, now()), DATEDIFF(eva_hasta, now()), TIMESTAMPDIFF(SECOND, NOW(), eva_desde), TIMESTAMPDIFF(SECOND, NOW(), eva_hasta) FROM academico_actividad_evaluaciones 
	WHERE eva_id='".$_GET["idE"]."' AND eva_estado=1");
	$fechas = mysqli_fetch_array($consultaFecha, MYSQLI_BOTH);
	if($fechas[2]>0){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=204&fechaD='.$evaluacion['eva_desde'].'&diasF='.$fechas[0].'&segundosF='.$fechas[2].'";</script>';
		exit();
	}
	if($fechas[3]<0){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=205&fechaH='.$evaluacion['eva_hasta'].'&diasP='.$fechas[1].'&segundosP='.$fechas[3].'";</script>';
		exit();
	}
	
	//Cantidad de preguntas de la evaluación
	$preguntasConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluacion_preguntas
	INNER JOIN academico_actividad_preguntas ON preg_id=evp_id_pregunta
	WHERE evp_id_evaluacion='".$_GET["idE"]."'
	");
	
	$cantPreguntas = mysqli_num_rows($preguntasConsulta);

	//Si la evaluación no tiene preguntas, lo mandamos para la pagina informativa
	if($cantPreguntas==0){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=101";</script>';
		exit();
	}

	//SABER SI EL ESTUDIANTE YA HIZO LA EVALUACION
	$consultaNume=mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones_resultados 
	WHERE res_id_evaluacion='".$_GET["idE"]."' AND res_id_estudiante='".$datosEstudianteActual[0]."'");
	$nume = mysqli_num_rows($consultaNume);
	
	if($nume>0){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=200";</script>';
		exit();
	}
	
	//CONSULTAMOS SI YA TIENE UNA SESIÓN ABIERTA EN ESTA EVALUACIÓN
	$consultaEstadoSesionEvaluacion=mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones_estudiantes 
	WHERE epe_id_evaluacion='".$_GET["idE"]."' AND epe_id_estudiante='".$datosEstudianteActual[0]."' AND epe_inicio IS NOT NULL AND epe_fin IS NULL");
	$estadoSesionEvaluacion = mysqli_num_rows($consultaEstadoSesionEvaluacion);
	if($estadoSesionEvaluacion>0){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=201";</script>';
		exit();
	}
	//BORRAMOS SI EXISTE Y LUEGO INSERTAMOS EL DATO DE QUE EL ESTUDIANTE INICIÓ LA EVALUACIÓN
	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluaciones_estudiantes WHERE epe_id_evaluacion='".$_GET["idE"]."' AND epe_id_estudiante='".$datosEstudianteActual[0]."'");
	
	mysqli_query($conexion, "INSERT INTO academico_actividad_evaluaciones_estudiantes(epe_id_estudiante, epe_id_evaluacion, epe_inicio)VALUES('".$datosEstudianteActual[0]."', '".$_GET["idE"]."', now())");
	

	//CUANTOS ESTÁN REALIZANDO LA EVALUACIÓN EN ESTE MOMENTO Y CUANTOS TERMINARON
	$consultaNumerosEvaluados=mysqli_query($conexion, "SELECT
	(SELECT count(epe_id) FROM academico_actividad_evaluaciones_estudiantes WHERE epe_id_evaluacion='".$_GET["idE"]."' AND epe_fin IS NULL),
	(SELECT count(epe_id) FROM academico_actividad_evaluaciones_estudiantes WHERE epe_id_evaluacion='".$_GET["idE"]."' AND epe_inicio IS NOT NULL AND epe_fin IS NOT NULL)");
	$Numerosevaluados = mysqli_fetch_array($consultaNumerosEvaluados, MYSQLI_BOTH);
	
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
                                <li><a class="parent-item" href="evaluaciones.php"><?=$frases[114][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$evaluacion['eva_nombre'];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <!-- BEGIN PROFILE SIDEBAR -->
                            <div class="profile-sidebar">
                                <div class="card card-topline-aqua">
                                    <div class="card-body no-padding height-9">
                                        <div class="row">
                                            <div class="course-picture">
                                                <img src="../../config-general/assets/img/course/course1.jpg" class="img-responsive" alt=""> </div>
                                        </div>
                                        <div class="profile-usertitle">
                                            <div class="profile-usertitle-name"> <?=$datosCargaActual['mat_nombre'];?> </div>
                                        </div>
                                        <!-- END SIDEBAR USER TITLE -->
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-head card-topline-aqua">
                                        <header><?=$evaluacion['eva_nombre'];?></header>
                                    </div>
                                    <div class="card-body no-padding height-9">
                                        <div class="profile-desc">
                                            <?=$evaluacion['eva_descripcion'];?>
                                        </div>
                                        <ul class="list-group list-group-unbordered">
                                            <li class="list-group-item">
                                                <b><?=$frases[130][$datosUsuarioActual[8]];?> </b>
                                                <div class="profile-desc-item pull-right"><?=$evaluacion['eva_desde'];?></div>
                                            </li>
                                            <li class="list-group-item">
                                                <b><?=$frases[131][$datosUsuarioActual[8]];?> </b>
                                                <div class="profile-desc-item pull-right"><?=$evaluacion['eva_hasta'];?></div>
                                            </li>
                                        </ul>
                                        <div class="row list-separated profile-stat">
                                            <div class="col-md-4 col-sm-4 col-6">
                                                <div class="uppercase profile-stat-title"> <?=$cantPreguntas;?> </div>
                                                <div class="uppercase profile-stat-text"> <?=$frases[139][$datosUsuarioActual[8]];?> </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-6">
                                                <div class="uppercase profile-stat-title" style="color: chartreuse;"> <span id="resp"></span> </div>
                                                <div class="uppercase profile-stat-text"> <?=$frases[141][$datosUsuarioActual[8]];?> </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-6">
                                                <div class="uppercase profile-stat-title"> <span id="fin"></span> </div>
                                                <div class="uppercase profile-stat-text"> <?=$frases[142][$datosUsuarioActual[8]];?> </div>
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
									<form action="guardar.php" method="post">
										<input type="hidden" name="id" value="9">
										<input type="hidden" name="idE" value="<?=$_GET["idE"];?>">
										<input type="hidden" name="cantPreguntas" value="<?=$cantPreguntas;?>">
										
									<div class="panel">
										<header class="panel-heading panel-heading-blue"><?=$frases[139][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
											<?php
											$contPreguntas = 1;
											while($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)){
												$respuestasConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_respuestas
												WHERE resp_id_pregunta='".$preguntas['preg_id']."'
												");
												
												$cantRespuestas = mysqli_num_rows($respuestasConsulta);
												if($cantRespuestas==0) {
													echo "<hr><span style='color:red';>".$frases[146][$datosUsuarioActual[8]].".</span>";
													continue;
												}
											?>
												<hr><div style="color: blue;">
												<?php echo "<span style='font-size:10px; color:black;'>Pregunta ".$contPreguntas." - ".$preguntas['preg_valor']." Puntos</span>". $preguntas['preg_descripcion'];?> 
												</div><hr>
											<?php 
												$contRespuestas = 1;
												while($respuestas = mysqli_fetch_array($respuestasConsulta, MYSQLI_BOTH)){
											?>
												<p>
													<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-<?=$contPreguntas;?><?=$contRespuestas;?>">
														<input type="hidden" value="<?=$preguntas['preg_id'];?>" name="P<?=$contPreguntas;?>">
														<input type="radio" id="option-<?=$contPreguntas;?><?=$contRespuestas;?>" class="mdl-radio__button" name="R<?=$contPreguntas;?>" value="<?php echo $respuestas['resp_id'];?>">
														<span class="mdl-radio__label"><?php echo $respuestas['resp_descripcion'];?></span>
													</label>
												</p>
											<?php
													$contRespuestas ++;
												}
												$contPreguntas ++;
											}
											?>
											<hr>
											<?php
											//MOSTRAMOS EL BOTÓN FINALIZAR SÓLO SI HAY PREGUNTAS EN LA EVALUACIÓN
											if($cantPreguntas>0){
											?>
												<div align="right"><button class="btn btn-primary" type="submit" onClick="if(!confirm('Te recomendamos verificar que todas las preguntas estén contestadas antes de enviar. Si ya lo hiciste puedes continuar. Deseas enviar la evaluación?')){return false;}"><?=$frases[140][$datosUsuarioActual[8]];?></button></div>
											<?php }?>
										</div>
									</div>
									</form>
								
								</div>
						
								<div class="col-md-3">
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[143][$datosUsuarioActual['uss_idioma']];?></header>
                                        <div class="panel-body">
											<p><b>1.</b> <?=$frases[147][$datosUsuarioActual[8]];?></p>
											
											<p><b>2.</b> <?=$frases[148][$datosUsuarioActual[8]];?></p>
											
											<p><b>3.</b> <?=$frases[149][$datosUsuarioActual[8]];?></p>
											
											<p><b>4.</b> <?=$frases[161][$datosUsuarioActual[8]];?></p>
										</div>
									</div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
											
												<p><b><?=$frases[141][$datosUsuarioActual[8]];?>:</b> <?=$frases[144][$datosUsuarioActual[8]];?></p>
											
												<p><b><?=$frases[142][$datosUsuarioActual[8]];?>:</b> <?=$frases[145][$datosUsuarioActual[8]];?></p>
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

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/course_details.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:31:36 GMT -->
</html>