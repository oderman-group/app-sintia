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
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");

$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}

$calificacion = Indicadores::traerDatosIndicadorRelacion($idR);
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
                                <li><a class="parent-item" href="indicadores.php"><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
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
												 $TablaNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"]);
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
											$registrosEnComun = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
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
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[108][$datosUsuarioActual['uss_idioma']];?><br>Indicador</th>
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
														$consultaNotas = Indicadores::consultaRecuperacionIndicadorPeriodo($config, $idR, $resultado['mat_id'], $cargaConsultaActual, $periodoConsultaActual);
														$notas = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);
														

														//Promedio nota indicador según nota de actividades relacionadas
														$notaIndicador = Calificaciones::consultaNotaIndicadoresPromedio($config, $idR, $cargaConsultaActual, $resultado['mat_id'], $periodoConsultaActual);
														 
														$notaRecuperacion = "";
														if(!empty($notas['rind_nota']) && $notas['rind_nota']>$notas['rind_nota_original'] and $notas['rind_nota']>$notaIndicador[0]){
															$notaRecuperacion = $notas['rind_nota'];
															
															//Color nota
															if(!empty($notaRecuperacion) && $notaRecuperacion<$config[5]) $colorNota = $config[6]; elseif(!empty($notaRecuperacion) && $notaRecuperacion>=$config[5]) $colorNota = $config[7];
														}
														$notasResultado = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoConsultaActual, $resultado['mat_id'], $cargaConsultaActual);
														 
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
															<input type="text" style="text-align: center; color:<?=$colorNota;?>" size="5" maxlength="3" value="<?=$notaRecuperacion;?>" name="<?=$notas['rind_nota_actual'];?>" step="<?=$cargaConsultaActual;?>_<?=$periodoConsultaActual;?>" id="<?=$resultado['mat_id'];?>" alt="<?=$idR;?>" title="<?=($calificacion['ipc_valor']/100);?>" onChange="recuperarIndicador(this)" tabindex="<?=$contReg;?>">
															<?php }?>
															
															
															<?php if(!empty($notas['cal_nota'])){?>
															<a href="#" name="calificaciones-nota-eliminar.php?id=<?=base64_encode($notas['cal_id']);?>" onClick="deseaEliminar(this)">X</a>
															<?php }?>
															<br><span style="text-decoration:underline; color:<?=$colorNota;?>; margin-left: 15px" id="CU<?=$resultado['mat_id'].$cargaConsultaActual;?>"><?=$estiloNotaRecuperacionFinal?></span>
														</td>
														
														<td>
															<?php
																if(!empty($notasResultado['bol_nota'])){
														 
																	if($notasResultado['bol_nota']<$config[5])$color = $config[6]; elseif($notasResultado['bol_nota']>=$config[5]) $color = $config[7]; 

																	$notasResultadoFinal=$notasResultado['bol_nota'];
																	$atributosA='style="text-decoration:underline; color:'.$color.';"';
																	if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
																		$atributosA='tabindex="0" role="button" data-toggle="popover" data-trigger="hover" data-content="<b>Nota Cuantitativa:</b><br>'.$notasResultado['bol_nota'].'" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000; color:'.$color.';"';

																		$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasResultado['bol_nota']);
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