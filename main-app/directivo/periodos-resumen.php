<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0111';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<script type="text/javascript">
  function def(enviada){
  var nota = enviada.value;
  var codEst = enviada.id;
  var per = enviada.name;
  var carga = <?=$cargaConsultaActual;?>;
 if (nota><?=$config[4];?> || isNaN(nota) || nota < <?=$config[3];?>) {alert('Ingrese un valor numerico entre <?=$config[3];?> y <?=$config[4];?>'); return false;}	
	  $('#respRP').empty().hide().html("esperando...").show(1);
		datos = "nota="+(nota)+
				   "&per="+(per)+
				   "&codEst="+(codEst)+
				   "&carga="+(carga);
			   $.ajax({
				   type: "POST",
				   url: "ajax-periodos-registrar.php",
				   data: datos,
				   success: function(data){
				   $('#respRP').empty().hide().html(data).show(1);
				   }
			   });

	}
	
function niv(enviada){
  var nota = enviada.value;
  var codEst = enviada.id;
  var per = enviada.name;
  var carga = <?=$cargaConsultaActual;?>;
 if (nota><?=$config[4];?> || isNaN(nota) || nota < <?=$config[3];?>) {alert('Ingrese un valor numerico entre <?=$config[3];?> y <?=$config[4];?>'); return false;}	
	  $('#respRP').empty().hide().html("esperando...").show(1);
		datos = "nota="+(nota)+
				   "&per="+(per)+
				   "&codEst="+(codEst)+
				   "&carga="+(carga);
			   $.ajax({
				   type: "POST",
				   url: "ajax-nivelaciones-registrar.php",
				   data: datos,
				   success: function(data){
				   $('#respRP').empty().hide().html(data).show(1);
				   }
			   });

	}
</script>
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
                                <div class="page-title"><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></div>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="cargas.php" onClick="deseaRegresar(this)"><?=$frases[12][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
											<p><b><?=$frases[117][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[120][$datosUsuarioActual['uss_idioma']];?></p>
											
											<p><b><?=$frases[118][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[121][$datosUsuarioActual['uss_idioma']];?></p>
										</div>
									</div>
									
									<?php include("info-carga-actual.php");?>
									
									<?php include("filtros-cargas.php");?>
									
									<?php include("../compartido/publicidad-lateral.php");?>

									
								</div>
									
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                        <div class="table-responsive">
											
											<span id="respRP"></span>
                                            
											<table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;">#</th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>

														<?php
															$p = 1;
															while($p<=$datosCargaActual['gra_periodos']){
																$consultaPeriodosCursos=mysqli_query($conexion, "SELECT * FROM academico_grados_periodos WHERE gvp_grado='".$datosCargaActual['car_curso']."' AND gvp_periodo='".$p."'");
																$periodosCursos = mysqli_fetch_array($consultaPeriodosCursos, MYSQLI_BOTH);
																echo '<th style="text-align:center;">'.$p.'P<br>('.$periodosCursos['gvp_valor'].'%)</th>';
																$p++;
															}
														?> 
														<th style="text-align:center;"><?=$frases[117][$datosUsuarioActual['uss_idioma']];?></th>
														<th style="text-align:center;"><?=$frases[118][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$contReg = 1; 
													$consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
													WHERE mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													?>
                                                    
													<tr>
                                                        <td style="text-align:center;"><?=$contReg;?></td>
														<td><?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?></td>

														<?php
														 $definitiva = 0;
														 $sumatoria = 0;
														 $decimal = 0;
														 $n = 0;
														 for($i=1; $i<=$datosCargaActual['gra_periodos']; $i++){
															$consultaPeriodosCursos=mysqli_query($conexion, "SELECT * FROM academico_grados_periodos WHERE gvp_grado='".$datosCargaActual['car_curso']."' AND gvp_periodo='".$i."'");
															$periodosCursos = mysqli_fetch_array($consultaPeriodosCursos, MYSQLI_BOTH);
															 $decimal = $periodosCursos['gvp_valor']/100;
															 
															//LAS CALIFICACIONES
															$notasConsulta = mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante=".$resultado['mat_id']." AND bol_carga=".$cargaConsultaActual." AND bol_periodo=".$i);
															$notasResultado = mysqli_fetch_array($notasConsulta, MYSQLI_BOTH);
															$numN = mysqli_num_rows($notasConsulta);
															if($numN){
																$n++;
																$definitiva += $notasResultado[4]*$decimal;
															}
															if($notasResultado[4]<$config[5] and $notasResultado[4]!="")$color = $config[6]; elseif($notasResultado[4]>=$config[5]) $color = $config[7];
															if($notasResultado[5]==2) $tipo = '<span style="color:red; font-size:9px;">'.$frases[123][$datosUsuarioActual['uss_idioma']].'</span>'; elseif($notasResultado[5]==1) $tipo = '<span style="color:blue; font-size:9px;">'.$frases[122][$datosUsuarioActual['uss_idioma']].'</span>'; else $tipo='';


														?>
															<td style="text-align:center;">
																<a href="calificaciones-estudiante.php?usrEstud=<?=$resultado['mat_id_usuario'];?>&periodo=<?=$i;?>&carga=<?=$cargaConsultaActual;?>" style="text-decoration:underline; color:<?=$color;?>;"><?=$notasResultado[4]."</a><br>".$tipo;?><br>
																<?php if($notasResultado[4]!=""){?>
																	<input size="5" name="<?=$i?>" id="<?=$resultado['mat_id'];?>" value="" onChange="def(this)" tabindex="2" style="text-align: center;"><br>
																	<span style="font-size:9px; color:rgb(0,0,153);"><?php echo $notasResultado[6];?></span>
																<?php }?>
															</td>
														<?php		
														 }
															$consultaN = mysqli_query($conexion, "SELECT * FROM academico_nivelaciones WHERE niv_cod_estudiante=".$resultado['mat_id']." AND niv_id_asg=".$cargaConsultaActual);
															
															$numN = mysqli_num_rows($consultaN);
															$rN = mysqli_fetch_array($consultaN, MYSQLI_BOTH);
															if($numN==0){
																if($n>0)
																	$definitiva = round(($definitiva), $config['conf_decimales_notas']);
																	$tN = '<span style="color:blue; font-size:9px;">'.$frases[122][$datosUsuarioActual['uss_idioma']].'</span>';
															}else{
																$definitiva = $rN[3];
																$tN = '<span style="color:red; font-size:9px;">'.$frases[124][$datosUsuarioActual['uss_idioma']].'</span>';
															}
														 if($definitiva<$config[5])$color = $config[6]; elseif($definitiva>=$config[5]) $color = $config[7];
														 
														 //CALCULAR NOTA MINIMA EN EL ULTIMO PERIODO PARA APROBAR LA MATERIA
														 //PREGUNTAMOS SI ESTAMOS EN EL PERIODO PENULTIMO O ULTIMO
														 if($config[2]==$datosCargaActual['gra_periodos']){
															 $notaMinima = ($config[5]-$definitiva);
															 $consultaPeriodosCursos2=mysqli_query($conexion, "SELECT * FROM academico_grados_periodos WHERE gvp_grado='".$datosCargaActual['car_curso']."' AND gvp_periodo='".$datosCargaActual['gra_periodos']."'");
															 $periodosCursos2 = mysqli_fetch_array($consultaPeriodosCursos2, MYSQLI_BOTH);
															 $decimal2 = $periodosCursos2['gvp_valor']/100;
															 $notaMinima = round(($notaMinima / $decimal2), $config['conf_decimales_notas']);
															 if($notaMinima<=0){
																$notaMinima = "-";
																$colorFaltante = "green";
															 }else{
																if($notaMinima<=$config[4]) $colorFaltante = "blue"; else $colorFaltante = "red"; 
															 }
														 }else{
															$notaMinima = "-";
															$colorFaltante = "black";
														}
														?>

														<td style="text-align:center; color:<?=$colorFaltante;?>; font-weight:bold;"><?=$notaMinima;?></td>

														<td style="text-align:center; color:<?=$color;?>;">
															<?=$definitiva."<br>".$tN;?><br>
															<?php
															if($n==$datosCargaActual['gra_periodos']) $e = ''; else $e = 'disabled';
															?>
															<input size="5" name="<?=$i?>" id="<?=$resultado[0];?>" value="" onChange="niv(this)" tabindex="2" <?=$e;?> style="font-size: 13px; text-align: center;">
														</td>
                                                    </tr>
													<?php
														$contReg++;
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