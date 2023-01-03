<?php
if(($datosUsuarioActual[3]==3 or $datosUsuarioActual[3]==4) and $config['conf_sin_nota_numerica']==1){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=218";</script>';
	exit();
}
?>
<div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
												<p>
													<b><?=$frases[117][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[120][$datosUsuarioActual['uss_idioma']];?>
												</p>
											
												<p>
													<b><?=$frases[118][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[121][$datosUsuarioActual['uss_idioma']];?>
												</p>
										</div>
									</div>
									
									<?php
									if($datosUsuarioActual[3]!=4){
									?>

									<div class="panel">
											<header class="panel-heading panel-heading-yellow">INFORMACIÓN DE CONSULTA</header>

											<div class="panel-body">
												<ul class="list-group list-group-unbordered">
													<li class="list-group-item">
														<b>ESTUDIANTE</b> 
														<div class="profile-desc-item pull-right"><?=strtoupper($datosEstudianteActual[3]." ".$datosEstudianteActual[4]." ".$datosEstudianteActual[5]);?></div>
													</li>
													
												</ul>

											</div>
										</div>
									<?php }?>

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
                                        <div class="card-body ">
                                        <div class="table-responsive">
                                            <table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;">#</th>
														<th style="text-align:center;"><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th style="text-align:center;"><?=$frases[116][$datosUsuarioActual['uss_idioma']];?></th>
														<?php
															$p = 1;
															while($p<=$datosEstudianteActual['gra_periodos']){
																$periodosCursos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_grados_periodos
																WHERE gvp_grado='".$datosEstudianteActual['mat_grado']."' AND gvp_periodo='".$p."'
																"), MYSQLI_BOTH);
																echo '<th style="text-align:center;">'.$p.'P<br>('.$periodosCursos['gvp_valor'].'%)</th>';
																$p++;
															}
														?> 
														<th style="text-align:center;"><?=$frases[117][$datosUsuarioActual['uss_idioma']];?></th>
														<?php if($datosUsuarioActual[3]!=3 and $datosUsuarioActual[3]!=4){?>
															<th style="text-align:center;"><?=$frases[118][$datosUsuarioActual['uss_idioma']];?></th>
														<?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$contReg = 1; 
													$cCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='".$datosEstudianteActual[6]."' AND car_grupo='".$datosEstudianteActual[7]."'");
													while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
														$cDatos = mysqli_query($conexion, "SELECT mat_id, mat_nombre, gra_codigo, gra_nombre, uss_id, uss_nombre FROM academico_materias, academico_grados, usuarios WHERE mat_id='".$rCargas[4]."' AND gra_id='".$rCargas[2]."' AND uss_id='".$rCargas[1]."'");
														$rDatos = mysqli_fetch_array($cDatos, MYSQLI_BOTH);
													?>
                                                    
													<tr>
                                                        <td style="text-align:center;"><?=$contReg;?></td>
														<td style="text-align:center;"><?=$rCargas[0];?></td>
														<td><?=$rDatos[1];?></td>

														<?php
														 $definitiva = 0;
														 $sumatoria = 0;
														 $decimal = 0;
														 $sumaPorcentaje = 0;
														 $n = 0;
														 for($i=1; $i<=$datosEstudianteActual['gra_periodos']; $i++){
															
															 $periodosCursos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_grados_periodos
															WHERE gvp_grado='".$datosEstudianteActual['mat_grado']."' AND gvp_periodo='".$i."'
															"), MYSQLI_BOTH);
															 $decimal = $periodosCursos['gvp_valor']/100;
															 
															//LAS CALIFICACIONES
															$notasConsulta = mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante=".$datosEstudianteActual[0]." AND bol_carga=".$rCargas[0]." AND bol_periodo=".$i);
															$notasResultado = mysqli_fetch_array($notasConsulta, MYSQLI_BOTH);
															$numN = mysqli_num_rows($notasConsulta);
															if($numN){
																$n++;
																$definitiva += $notasResultado[4]*$decimal;
																$sumaPorcentaje += $decimal;
															}
															if($notasResultado[4]<$config[5] and $notasResultado[4]!="")$color = $config[6]; elseif($notasResultado[4]>=$config[5]) $color = $config[7];
															if($notasResultado[5]==2) $tipo = '<span style="color:red; font-size:9px;">'.$frases[123][$datosUsuarioActual['uss_idioma']].'</span>'; elseif($notasResultado[5]==1) $tipo = '<span style="color:blue; font-size:9px;">'.$frases[122][$datosUsuarioActual['uss_idioma']].'</span>'; else $tipo='';

														?>
															<td style="text-align:center;">
																<a href="calificaciones.php?carga=<?=$rCargas[0];?>&periodo=<?=$i;?>&usrEstud=<?=$_GET["usrEstud"];?>" style="color:<?=$color;?>; text-decoration:underline;"><?=$notasResultado[4]."<br>".$tipo;?></a>
															</td>
														<?php		
														 }
															@$definitiva = ($definitiva / $sumaPorcentaje);
															$consultaN = mysqli_query($conexion, "SELECT * FROM academico_nivelaciones WHERE niv_cod_estudiante=".$datosEstudianteActual[0]." AND niv_id_asg=".$rCargas[0]);
															
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
														 if($config[2]==$datosEstudianteActual['gra_periodos']){
															 $notaMinima = ($config[5]-$definitiva);
															 $periodosCursos2 = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_grados_periodos
															 WHERE gvp_grado='".$datosEstudianteActual['mat_grado']."' AND gvp_periodo='".$datosEstudianteActual['gra_periodos']."'
															 "), MYSQLI_BOTH);
															 $decimal2 = $periodosCursos2['gvp_valor']/100;
															 
															@$notaMinima = round(($notaMinima / $decimal2), $config['conf_decimales_notas']); 
															 
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
														
														<?php if($datosUsuarioActual[3]!=3 and $datosUsuarioActual[3]!=4){?>
															<td style="text-align:center; color:<?=$color;?>;">
																<?=$definitiva."<br>".$tN;?>
															</td>
														<?php }?>
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