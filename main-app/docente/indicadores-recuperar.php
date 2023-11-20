<?php
include("session.php");
$idPaginaInterna = 'DC0077';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");

//Hay acciones que solo son permitidos en periodos diferentes al actual.
include("verificar-periodos-iguales.php");
include("../compartido/head.php");

require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}

$consultaCalificaciones=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores ai
INNER JOIN ".BD_ACADEMICA.".academico_indicadores_carga ipc ON ipc.ipc_indicador=ai.ind_id AND ipc.institucion={$config['conf_id_institucion']} AND ipc.year={$_SESSION["bd"]}
WHERE ai.ind_id='".$idR."' AND ai.institucion={$config['conf_id_institucion']} AND ai.year={$_SESSION["bd"]}");
$calificacion = mysqli_fetch_array($consultaCalificaciones, MYSQLI_BOTH);
?>
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
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
                                <div class="page-title"><?=$calificacion['ind_nombre']." (".$calificacion['ipc_valor']."%)";?></div>
								
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="indicadores.php"><?=$frases[63][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$calificacion['ind_nombre'];?></li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<?php include("info-carga-actual.php");?>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">TABLA DE VALORES</header>

										<div class="panel-body">
											  <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
												<!-- BEGIN -->
												<thead>
												  <tr>
													<th>Desde</th>
													<th>Hasta</th>
													<th>Resultado</th>
												  </tr>
												</thead>
												<tbody>
												 <?php
												 $TablaNotas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_notas_tipos WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
												 while($tabla = mysqli_fetch_array($TablaNotas, MYSQLI_BOTH)){
												 ?>
												  <tr id="data1" class="odd grade">

													<td><?=$tabla["notip_desde"];?></td>
													<td><?=$tabla["notip_hasta"];?></td>
													<td><?=$tabla["notip_nombre"];?></td>
												  </tr>
												  <?php }
													mysqli_free_result($TablaNotas);
													?>
												</tbody>
											  </table>
										</div>
										
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=strtoupper($frases[63][$datosUsuarioActual['uss_idioma']]);?> </header>
										<div class="panel-body">
											<p>Puedes cambiar a otro indicador rápidamente para calificar a tus estudiantes o hacer modificaciones de notas.</p>
											<?php
											$registrosEnComun = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga ipc
											INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=ipc.ipc_indicador AND ai.institucion={$config['conf_id_institucion']} AND ai.year={$_SESSION["bd"]}
											WHERE ipc.ipc_carga='".$cargaConsultaActual."' AND ipc.ipc_periodo='".$periodoConsultaActual."' AND ipc.ipc_indicador!='".$idR."' AND ipc.institucion={$config['conf_id_institucion']} AND ipc.year={$_SESSION["bd"]}
											ORDER BY ipc.ipc_id DESC
											");
											while($regComun = mysqli_fetch_array($registrosEnComun, MYSQLI_BOTH)){
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?idR=<?=base64_encode($regComun['ipc_indicador']);?>"><?=$regComun['ind_nombre']." (".$regComun['ipc_valor']."%)";?></a></p>
											<?php }
											mysqli_free_result($registrosEnComun);
											?>
										</div>
                                    </div>
									
									<?php include("../compartido/publicidad-lateral.php");?>
									
								</div>
									
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
										
										
									
										
                                        <div class="card-body">
											
											
										<span style="color: blue; font-size: 15px;" id="respRC"></span>
											
											
                                        <div class="table-responsive">
                                            <table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th>Cod</th>
														<th><?=$frases[61][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[108][$datosUsuarioActual[8]];?><br>Indicador</th>
														<th>Recup.<br>Indicador</th>
														<th>DEF.<br>PERIODO <?=$periodoConsultaActual;?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
													 $contReg = 1;
													 $colorNota = "black";
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 
														//Consulta de recuperaciones si ya la tienen puestas.
														$consultaNotas=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion WHERE rind_estudiante=".$resultado[0]." AND rind_indicador='".$idR."' AND rind_periodo='".$periodoConsultaActual."' AND rind_carga='".$cargaConsultaActual."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
														$notas = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);
														

														//Promedio nota indicador según nota de actividades relacionadas
														$consultaNotaIndicador=mysqli_query($conexion, "SELECT ROUND(SUM(cal_nota*(act_valor/100)) / SUM(act_valor/100),2) FROM ".BD_ACADEMICA.".academico_calificaciones aac
														INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id=aac.cal_id_actividad AND aa.act_estado=1 AND aa.act_id_tipo='".$idR."' AND aa.act_periodo='".$periodoConsultaActual."' AND aa.act_id_carga='".$cargaConsultaActual."' AND aa.institucion={$config['conf_id_institucion']} AND aa.year={$_SESSION["bd"]}
														WHERE aac.cal_id_estudiante='".$resultado['mat_id']."' AND aac.institucion={$config['conf_id_institucion']} AND aac.year={$_SESSION["bd"]}");
														$notaIndicador = mysqli_fetch_array($consultaNotaIndicador, MYSQLI_BOTH);
														 
														$notaRecuperacion = "";
														if(!empty($notas['rind_nota']) && $notas['rind_nota']>$notas['rind_nota_original'] and $notas['rind_nota']>$notaIndicador[0]){
															$notaRecuperacion = $notas['rind_nota'];
															
															//Color nota
															if(!empty($notaRecuperacion) && $notaRecuperacion<$config[5]) $colorNota = $config[6]; elseif(!empty($notaRecuperacion) && $notaRecuperacion>=$config[5]) $colorNota = $config[7];
														}
														 $consultaNotasResultado=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante='".$resultado['mat_id']."' AND bol_carga=".$cargaConsultaActual." AND bol_periodo=".$periodoConsultaActual);
														$notasResultado = mysqli_fetch_array($consultaNotasResultado, MYSQLI_BOTH);
														 
														if(!empty($notaIndicador[0]) && $notaIndicador[0]<$config[5])$color = $config[6]; elseif(!empty($notaIndicador[0]) && $notaIndicador[0]>=$config[5]) $color = $config[7]; 
														 
														 
														 $colorEstudiante = '#000;';
														 if($resultado['mat_inclusion']==1){$colorEstudiante = 'blue;';}

														$notaIndicadorFinal=$notaIndicador[0];
														$atributosA='style="text-decoration:underline; color:'.$color.';"';
														if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
															$atributosA='tabindex="0" role="button" data-toggle="popover" data-trigger="hover" data-content="<b>Nota Cuantitativa:</b><br>'.$notaIndicador[0].'" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000; color:'.$color.';"';

															$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaIndicador[0]);
															$notaIndicadorFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
														}
													 ?>
                                                    
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['mat_id'];?></td>
														<td style="color: <?=$colorEstudiante;?>">
															<img src="../files/fotos/<?=$resultado['uss_foto'];?>" width="50">
															<?=Estudiantes::NombreCompletoDelEstudiante($resultado);?>
														</td>
														<td>
															<a href="calificaciones-estudiante.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>&periodo=<?=base64_encode($periodoConsultaActual);?>&carga=<?=base64_encode($cargaConsultaActual);?>&indicador=<?=$_GET["idR"];?>" <?=$atributosA?>>
																<?=$notaIndicadorFinal;?>
															</a>	
														</td>
														<td>
															<?php 
															$estiloNotaRecuperacionFinal="";
														 	if(empty($notaIndicador[0])){
																echo "<span title='No hay notas relacionadas este indicador. Revise las actividades.'>-</span>";	
															}
															elseif(empty($notas['rind_id'])){
																echo "<span title='No hay un registro de definitiva para este Indicador. Por favor Genere Informe.'>?</span>";	
															}
															elseif($notaIndicador[0]>=$config[5]){
																echo "";	
															}
															else{
																if($config['conf_forma_mostrar_notas'] == CUALITATIVA){		
																	$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaRecuperacion);
																	$estiloNotaRecuperacionFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
																}	
															?>
															<input type="text" style="text-align: center; color:<?=$colorNota;?>" size="5" maxlength="3" value="<?=$notaRecuperacion;?>" name="<?=$notas['rind_nota_actual'];?>" step="<?=$cargaConsultaActual;?>-<?=$periodoConsultaActual;?>" id="<?=$resultado['mat_id'];?>" alt="<?=$idR;?>" title="<?=($calificacion['ipc_valor']/100);?>" onChange="recuperarIndicador(this)" tabindex="<?=$contReg;?>">
															<?php }?>
															
															
															<?php if(!empty($notas['cal_nota'])){?>
															<a href="#" name="calificaciones-nota-eliminar.php?id=<?=base64_encode($notas['cal_id']);?>" onClick="deseaEliminar(this)">X</a>
															<?php }?>
															<br><span style="text-decoration:underline; color:<?=$colorNota;?>; margin-left: 15px" id="CU<?=$resultado['mat_id'].$cargaConsultaActual;?>"><?=$estiloNotaRecuperacionFinal?></span>
														</td>
														
														<td>
															<?php
																if(!empty($notasResultado[4])){
														 
																	if($notasResultado[4]<$config[5])$color = $config[6]; elseif($notasResultado[4]>=$config[5]) $color = $config[7]; 

																	$notasResultadoFinal=$notasResultado[4];
																	$atributosA='style="text-decoration:underline; color:'.$color.';"';
																	if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
																		$atributosA='tabindex="0" role="button" data-toggle="popover" data-trigger="hover" data-content="<b>Nota Cuantitativa:</b><br>'.$notasResultado[4].'" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000; color:'.$color.';"';

																		$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasResultado[4]);
																		$notasResultadoFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
																	}
															?>
																<a href="calificaciones-estudiante.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>&periodo=<?=base64_encode($periodoConsultaActual);?>&carga=<?=base64_encode($cargaConsultaActual);?>" <?=$atributosA?>><?=$notasResultadoFinal;?></a>
															<?php }?>
														</td>
														
                                                    </tr>
													<?php 
														 $contReg++;
													  }
													mysqli_free_result($consulta);
															
													  ?>
                                                </tbody>
                                            </table>
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
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!-- end js include path -->

	<script>
		$(function () {
			$('[data-toggle="popover"]').popover();
		});

		$('.popover-dismiss').popover({trigger: 'focus'});
	</script>
</body>

</html>