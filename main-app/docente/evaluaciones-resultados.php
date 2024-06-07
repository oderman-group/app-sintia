<?php
include("session.php");
$idPaginaInterna = 'DC0016';
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
include("../compartido/head.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");


$idE="";
	if(!empty($_GET["idE"])){ $idE=base64_decode($_GET["idE"]);}

	$evaluacion = Evaluaciones::consultaEvaluacion($conexion, $config, $idE);

	$actividad=[];
	$ocultarExportacion="";
	$id_actividad="";
	if(!empty($evaluacion["eva_actividad"])){
		$id_actividad=$evaluacion["eva_actividad"];
	}
	if(!empty($_POST["actividad"])){
		$id_actividad=$evaluacion["eva_actividad"];
	}
	if(!empty($id_actividad)){
		$ocultarExportacion="hidden";
		$actividad = Actividades::consultarDatosActividades($config, $id_actividad);
	}
	
	//Cantidad de preguntas de la evaluación
	$cantPreguntas = Evaluaciones::numeroPreguntasEvaluacion($conexion, $config, $idE);
?>
<script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js"></script>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>

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
								<?php include("../compartido/texto-manual-ayuda.php");?>
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

											</div>
										</div>
										</div>
                                    </div>	
								
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$evaluacionesEnComun = Evaluaciones::consultaEvaluacionTodas($conexion, $config, $idE, $cargaConsultaActual, $periodoConsultaActual);
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
										
										<a href="#" id="btnExportar" <?=$ocultarExportacion?> class="btn btn-info" onClick="document.getElementById('exportarNotas').style.display='block';"><i class="fas fa-file-export"></i>Exportar Notas</a>
										
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
													$actividadesConsulta = Actividades::traerActividadesCarga($config, $cargaConsultaActual, $periodoConsultaActual);
													?>
													<select class="form-control  select2" name="actividad" required>
														<option value="">Seleccione una opción</option>
														<?php
														while($actividadesDatos = mysqli_fetch_array($actividadesConsulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$actividadesDatos['act_id'];?>"><?=$actividadesDatos['act_descripcion']." (".$actividadesDatos['act_valor']."%)"?></option>
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
											<p id="pExportada" <?=empty($ocultarExportacion)?"hidden":"" ?> >Esta evaluacion fue exportada a la actividad: <?= $actividad["act_descripcion"]?>.</p>
											<p><mark>Recuerde que las preguntas abiertas no se están teniendo en cuenta. Esas deben ser calificadas manualmente.</mark></p>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
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
													$consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
													 $contReg = 1;
													 $registroNotas = 0; 
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $datos1 = Evaluaciones::consultarTiempoEvaluacion($conexion, $config, $idE, $resultado['mat_id']);
														 
														 $datos2 = Evaluaciones::traerConteoPreguntas($conexion, $config, $idE, $resultado['mat_id']);
														 
														 if($datos2[0] > 0){
															$porcentaje = round(($datos2[1]/$datos2[0])*100,$config['conf_decimales_notas']);
														 }
														 
														 $nota = round(($config['conf_nota_hasta']*($porcentaje/100)),$config['conf_decimales_notas']);
														 
														 if($nota<$config[5])$color = $config[6]; elseif($nota>=$config[5]) $color = $config[7];
														 
														 //Exportar las notas
														 if(!empty($_POST["exportar"]) && $_POST["exportar"]==1 and !empty($nota)){
															Calificaciones::eliminarCalificacionActividadEstudiante($config, $_POST["actividad"], $resultado['mat_id']);
															
															Calificaciones::guardarNotaActividadEstudiante($conexionPDO, "cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones, institucion, year, cal_id", [$resultado['mat_id'],$nota,$_POST["actividad"], date("Y-m-d H:i:s"), 0, $config['conf_id_institucion'], $_SESSION["bd"]]);
															
															
															 //Solo actuliza una vez que la actividad fue registrada.
															 if($registroNotas<1){
																Actividades::marcarActividadRegistrada($config, $_POST["actividad"]);
																mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_actividad_evaluaciones SET eva_actividad='".$_POST["actividad"]."' WHERE eva_id='".$idE."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
																
															}
															
															
																
															
															 
															 $registroNotas ++;
															
														 }

														$notaFinal="";
														$title='';
														if(!empty($datos2[1]) && $config['conf_forma_mostrar_notas'] == CUALITATIVA){
															$notaFinal=$nota;
															$title='title="Nota Cuantitativa: '.$nota.'"';
															$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota);
															$notaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
														}
													 ?>
													<tr>
                                                        <td align="center"><?=$contReg;?></td>
														<td><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>
														<td><?php if(!empty($datos1['epe_inicio'])){ echo $datos1['epe_inicio'];}?></td>
														<td><?php if(!empty($datos1['epe_fin'])){ echo $datos1['epe_fin'];}?></td>
														<td><?php if(!empty($datos2[2]) && $datos1[2]>0){echo $datos1[2]." Min. y ";} if(!empty($datos2[3]) && $datos1[3]>0){echo $datos1[3]." Seg.";}?></td>
														<td><?php if(!empty($datos2[1])){echo $datos2[2]."/".$cantPreguntas;}?></td>
														<td align="center"><?php if(!empty($datos2[1])){echo $datos2[1]."/".$datos2[0];}?></td>
														<td align="center"><?php if(!empty($datos2[1])){echo $porcentaje."%";}?></td>
														<td style="color: <?=$color;?>;" <?=$title;?> align="center"><?=$notaFinal;?></td>
														<td align="center">
														<?php if(!empty($datos2[1]) or !empty($datos1['epe_inicio'])){?>
															
															<a href="evaluaciones-ver.php?idE=<?=$_GET["idE"];?>&usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>" title="Ver resultados."><i class="fa fa-search-plus"></i></a>
															<?php 
																//Si está consultando periodos anteriores y tiene permiso de edición le mostramos opciones de edición. Estas variables vienen de la //pagina verificar-periodos-diferentes.php
																if($datosHistoricos['eva_periodo']==$periodoConsultaActual or $datosCargaActual['car_permiso2']==1){?>
																	<a href="#" name="evaluaciones-eliminar-intento.php?idE=<?=$_GET["idE"];?>&idEstudiante=<?=base64_encode($resultado['mat_id']);?>" onClick="deseaEliminar(this)"><i class="fa fa-eraser" title="Eliminar esta evaluación."></i></a>
															<?php }?>
															
															
														<?php }?>
														</td>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
														if(!empty($_POST["exportar"]) && $_POST["exportar"]==1 and $registroNotas>=1){
														?>
														<script>
															function enviarRespuesta(){
																document.getElementById("respuestaNotas").style.display="Block";
																document.getElementById("btnExportar").style.display="none";
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