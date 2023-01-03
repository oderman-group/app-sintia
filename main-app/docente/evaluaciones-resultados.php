<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0016';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");?>
<script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js"></script>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
	
	<?php
	$consultaEvaluacion = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones 
	WHERE eva_id='".$_GET["idE"]."' AND eva_estado=1");
	$evaluacion = mysqli_fetch_array($consultaEvaluacion, MYSQLI_BOTH);

	
	//Cantidad de preguntas de la evaluación
	$preguntasConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluacion_preguntas
	INNER JOIN academico_actividad_preguntas ON preg_id=evp_id_pregunta
	WHERE evp_id_evaluacion='".$_GET["idE"]."'
	ORDER BY preg_id DESC
	");
	
	$cantPreguntas = mysqli_num_rows($preguntasConsulta);

	?>

	<input type="hidden" id="idE" name="idE" value="<?=$_GET["idE"];?>">
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
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="evaluaciones.php"><?=$frases[114][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$evaluacion['eva_nombre'];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">

							<div class="col-md-3">
								
									<div class="panel">
										<header class="panel-heading panel-heading-blue"><?=$datosCargaActual['mat_nombre'];?> </header>
										<div class="panel-body">
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

											</div>
										</div>
										</div>
                                    </div>	
								
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$evaluacionesEnComun = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones
											WHERE eva_id_carga='".$cargaConsultaActual."' AND eva_periodo='".$periodoConsultaActual."' AND eva_id!='".$_GET["idE"]."' AND eva_estado=1
											ORDER BY eva_id DESC
											");
											while($evaComun = mysqli_fetch_array($evaluacionesEnComun, MYSQLI_BOTH)){
											?>
												<p><a href="evaluaciones-resultados.php?idE=<?=$evaComun['eva_id'];?>"><?=$evaComun['eva_nombre'];?></a></p>
											<?php }?>
										</div>
                                    </div>
								
								<?php include("../compartido/publicidad-lateral.php");?>

									
							</div>
							
							<div class="col-md-9">
								
								
								
								<div class="row" style="margin-bottom: 10px;">
									<div class="col-sm-12">
										<a href="evaluaciones.php" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i>Regresar</a>
										
										<a href="#" class="btn btn-info" onClick="document.getElementById('exportarNotas').style.display='block';"><i class="fas fa-file-export"></i>Exportar Notas</a>
										
									</div>
								</div>
								
								<div class="alert alert-success" id="respuestaNotas" style="display: none;">
									<button type="button" class="close" data-dismiss="alert">&times;</button>
									<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Se han exportado correctamente las notas para los estudiantes que ya han terminado esta evaluación.
								</div>
								
								<div class="card card-topline-purple" style="display: none;" id="exportarNotas">
									<form name="formularioGuardar" action="evaluaciones-resultados.php?idE=<?=$_GET["idE"];?>" method="post">
										<input type="hidden" value="1" name="exportar">
                                        <div class="card-head">
                                            <header>Exportar notas</header>
											
                                        </div>
                                        <div class="card-body">
											<p style="color: navy;">Escoja la actividad a la cual desea exportar estas notas. Estas notas reemplazarán, en caso de existir, las que existan actualmente en la actividad que escoja.</p>
											<p style="color: tomato;">Solo aparecerán las actividades que están pendientes por notas.</p>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Actividades</label>
												<div class="col-sm-10">
													<?php
													$actividadesConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividades
													WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1 AND (act_registrada=0 OR act_registrada IS NULL)");
													?>
													<select class="form-control  select2" name="actividad" required>
														<option value="">Seleccione una opción</option>
														<?php
														while($actividadesDatos = mysqli_fetch_array($actividadesConsulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$actividadesDatos[0];?>"><?=$actividadesDatos['act_descripcion']." (".$actividadesDatos['act_valor']."%)"?></option>
														<?php }?>
													</select>
												</div>
											</div>
											<input type="submit" class="btn btn-danger" value="Exportar notas ahora">&nbsp;
										</div>
									</form>
								</div>
								
								<span id="respuestaGuardar"></span>
								
								<div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$evaluacion['eva_nombre'];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<p><mark>Recuerde que las preguntas abiertas no se están teniendo en cuenta. Esas deben ser calificadas manualmente.</mark></p>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[61][$datosUsuarioActual[8]];?></th>
														<th>Inicio</th>
														<th>Fin</th>
														<th>Tiempo</th>
														<th>#PC</th>
														<th>Puntos</th>
														<th>%</th>
														<th>Nota</th>
														<th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas
													 INNER JOIN usuarios ON uss_id=mat_id_usuario
													 WHERE mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido, mat_segundo_apellido, mat_nombres");
													 $contReg = 1;
													 $registroNotas = 0; 
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $consultaDatos1=mysqli_query($conexion, "SELECT epe_inicio, epe_fin, MOD(TIMESTAMPDIFF(MINUTE, epe_inicio, epe_fin),60), MOD(TIMESTAMPDIFF(SECOND, epe_inicio, epe_fin),60) FROM academico_actividad_evaluaciones_estudiantes 
														 WHERE epe_id_estudiante='".$resultado['mat_id']."' AND epe_id_evaluacion='".$_GET["idE"]."'");
														 $datos1 = mysqli_fetch_array($consultaDatos1, MYSQLI_BOTH);
														 $consultaDatos2=mysqli_query($conexion, "SELECT
														 (SELECT sum(preg_valor) FROM academico_actividad_preguntas
														 INNER JOIN academico_actividad_evaluacion_preguntas ON evp_id_pregunta=preg_id AND evp_id_evaluacion='".$_GET["idE"]."'),
 
														 (SELECT sum(preg_valor) FROM academico_actividad_preguntas
														 INNER JOIN academico_actividad_evaluaciones_resultados ON res_id_pregunta=preg_id AND res_id_evaluacion='".$_GET["idE"]."' AND res_id_estudiante='".$resultado['mat_id']."'
														 INNER JOIN academico_actividad_respuestas ON resp_id=res_id_respuesta AND resp_correcta=1),
														 
														 (SELECT count(preg_id) FROM academico_actividad_preguntas
														 INNER JOIN academico_actividad_evaluaciones_resultados ON res_id_pregunta=preg_id AND res_id_evaluacion='".$_GET["idE"]."' AND res_id_estudiante='".$resultado['mat_id']."'
														 INNER JOIN academico_actividad_respuestas ON resp_id=res_id_respuesta AND resp_correcta=1)
														 ");
														 $datos2 = mysqli_fetch_array($consultaDatos2, MYSQLI_BOTH);
														 
														 @$porcentaje = round(($datos2[1]/$datos2[0])*100,$config['conf_decimales_notas']);
														 $nota = round(($config['conf_nota_hasta']*($porcentaje/100)),$config['conf_decimales_notas']);
														 
														 if($nota<$config[5])$color = $config[6]; elseif($nota>=$config[5]) $color = $config[7];
														 
														 //Exportar las notas
														 if($_POST["exportar"]==1 and $nota!=""){
															 
															mysqli_query($conexion, "DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["actividad"]."' AND cal_id_estudiante='".$resultado[0]."'");
															
															 
															mysqli_query($conexion, "INSERT INTO academico_calificaciones(cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones)VALUES('".$resultado[0]."','".$nota."','".$_POST["actividad"]."', now(), 0)");
															
															
															 //Solo actuliza una vez que la actividad fue registrada.
															 if($registroNotas<1){
																mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["actividad"]."'");
																
															}
															 
															 $registroNotas ++;
															
														 }
													 ?>
													<tr>
                                                        <td align="center"><?=$contReg;?></td>
														<td><?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?></td>
														<td><?=$datos1['epe_inicio'];?></td>
														<td><?=$datos1['epe_fin'];?></td>
														<td><?php if($datos1[2]>0){echo $datos1[2]." Min. y ";} if($datos1[3]>0){echo $datos1[3]." Seg.";}?></td>
														<td><?php if($datos2[1]!=""){echo $datos2[2]."/".$cantPreguntas;}?></td>
														<td align="center"><?php if($datos2[1]!=""){echo $datos2[1]."/".$datos2[0];}?></td>
														<td align="center"><?php if($datos2[1]!=""){echo $porcentaje."%";}?></td>
														<td style="color: <?=$color;?>;" align="center"><?php if($datos2[1]!=""){echo $nota;}?></td>
														<td align="center">
														<?php if($datos2[1]!="" or $datos1['epe_inicio']!=""){?>
															
															<a href="evaluaciones-ver.php?idE=<?=$_GET["idE"];?>&usrEstud=<?=$resultado['mat_id_usuario'];?>" title="Ver resultados."><i class="fa fa-search-plus"></i></a>
															<?php 
																//Si está consultando periodos anteriores y tiene permiso de edición le mostramos opciones de edición. Estas variables vienen de la //pagina verificar-periodos-diferentes.php
																if($datosHistoricos['eva_periodo']==$periodoConsultaActual or $datosCargaActual['car_permiso2']==1){?>
																	<a href="#" name="guardar.php?get=28&idE=<?=$_GET["idE"];?>&idEstudiante=<?=$resultado['mat_id'];?>" onClick="deseaEliminar(this)"><i class="fa fa-eraser" title="Eliminar esta evaluación."></i></a>
															<?php }?>
															
															
														<?php }?>
														</td>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
														if($_POST["exportar"]==1 and $registroNotas>=1){
														?>
														<script>
															function enviarRespuesta(){
																document.getElementById("respuestaNotas").style.display="Block";
															}
															
															setTimeout(enviarRespuesta, 1000);
														</script>
														<?php
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
		<!-- data tables -->
		<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
		<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
		<script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
        
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