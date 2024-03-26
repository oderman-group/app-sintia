<?php include("session.php"); ?>
<?php include("verificar-usuario.php"); ?>
<?php $idPaginaInterna = 'ES0007'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("verificar-carga.php"); ?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");?>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
	<?php include("../compartido/encabezado.php"); ?>

	<?php include("../compartido/panel-color.php"); ?>
	<!-- start page container -->
	<div class="page-container">
		<?php include("../compartido/menu.php"); ?>
		<!-- start page content -->
		<div class="page-content-wrapper">
			<div class="page-content">
				<div class="page-bar">
					<div class="page-title-breadcrumb">
						<div class=" pull-left">
							<div class="page-title"><?= $frases[63][$datosUsuarioActual['uss_idioma']]; ?></div>
							<?php include("../compartido/texto-manual-ayuda.php"); ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="row">

							<div class="col-md-4 col-lg-3">

								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?= $frases[106][$datosUsuarioActual['uss_idioma']]; ?> </header>
									<div class="panel-body">
										<?php
										$porcentaje = 0;
										for ($i = 1; $i <= $datosEstudianteActual['gra_periodos']; $i++) {
                                            $periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosEstudianteActual['mat_grado'], $i);
                                            
                                            $porcentajeGrado=25;
                                            if(!empty($periodosCursos['gvp_valor'])){
                                                $porcentajeGrado=$periodosCursos['gvp_valor'];
                                            }

											$notapp = mysqli_fetch_array(mysqli_query($conexion, "SELECT bol_nota FROM ".BD_ACADEMICA.".academico_boletin 
												WHERE bol_estudiante='" . $datosEstudianteActual['mat_id'] . "' AND bol_carga='" . $cargaConsultaActual . "' AND bol_periodo='" . $i . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}"), MYSQLI_BOTH);
											$porcentaje =0;
											if (!empty($notapp[0])){
												$porcentaje = ($notapp[0] / $config['conf_nota_hasta']) * 100;
											}
											if (!empty($notapp[0]) and $notapp[0] < $config['conf_nota_minima_aprobar']) $colorGrafico = 'danger';
											else $colorGrafico = 'info';
											if ($i == $periodoConsultaActual) $estiloResaltadoP = 'style="color: orange;"';
											else $estiloResaltadoP = '';
										?>
											<p>
												<a href="<?= $_SERVER['PHP_SELF']; ?>?carga=<?= base64_encode($cargaConsultaActual); ?>&periodo=<?= base64_encode($i); ?>" <?= $estiloResaltadoP; ?>><?= strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]); ?> <?= $i; ?> (<?= $porcentajeGrado; ?>%)</a>

												<?php
													if(!empty($notapp[0]) and $config['conf_sin_nota_numerica']!=1){

													$notaPorPeriodo=$notapp[0];
													if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
														$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notapp[0]);
														$notaPorPeriodo= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
													}
												?>
													<div class="work-monitor work-progress">
														<div class="states">
															<div class="info">
																<div class="desc pull-left"><b><?= $frases[62][$datosUsuarioActual['uss_idioma']]; ?>:</b>
																	<?= $notaPorPeriodo; ?>
																</div>
																<div class="percent pull-right"><?= $porcentaje; ?>%</div>
															</div>

															<div class="progress progress-xs">
																<div class="progress-bar progress-bar-<?= $colorGrafico; ?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?= $porcentaje; ?>%">
																	<span class="sr-only">90% </span>
																</div>
															</div>

														</div>
													</div>
												<?php } ?>

											</p>
											<hr>
										<?php } ?>

									</div>
								</div>

								<?php include("filtro-cargas.php"); ?>

								<?php include("../compartido/publicidad-lateral.php"); ?>

							</div>

							<div class="col-md-8 col-lg-9">
								<div class="card card-topline-purple">
									<div class="card-head">
										<header><?= $frases[63][$datosUsuarioActual['uss_idioma']]; ?></header>
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
														<th>#</th>
														<th><?= $frases[49][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th><?= $frases[50][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th align="center">%<br>Total</th>
														<th align="center">%<br>Actual</th>
														<th align="center" title="Nota según el porcentaje actual registrado.">Nota</th>
														<th align="center">Recup.</th>
													</tr>
												</thead>
												<tbody>
													<?php
													 $consulta = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
													$contReg = 1;
													while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {

														$sumaNotas = mysqli_fetch_array(mysqli_query($conexion, "SELECT SUM(cal_nota * (act_valor/100)), SUM(act_valor) FROM ".BD_ACADEMICA.".academico_calificaciones aac
														INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id=aac.cal_id_actividad AND aa.act_id_tipo='" . $resultado['ipc_indicador'] . "' AND aa.act_periodo='" . $periodoConsultaActual . "' AND aa.act_id_carga='" . $cargaConsultaActual . "' AND aa.act_estado=1 AND aa.institucion={$config['conf_id_institucion']} AND aa.year={$_SESSION["bd"]}
														WHERE aac.cal_id_estudiante='" . $datosEstudianteActual['mat_id']."' AND aac.institucion={$config['conf_id_institucion']} AND aac.year={$_SESSION["bd"]}"), MYSQLI_BOTH);

														$notasResultado = 0;
														if(!empty($sumaNotas[1])){
															$notasResultado = round($sumaNotas[0] / ($sumaNotas[1] / 100), $config['conf_decimales_notas']);
														}



														//Consulta de recuperaciones si ya la tienen puestas.
														$consultaNotas = Indicadores::consultaRecuperacionIndicadorPeriodo($config, $resultado['ipc_indicador'], $datosEstudianteActual['mat_id'], $cargaConsultaActual, $periodoConsultaActual);
														$notas = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);
														

														//Promedio nota indicador según nota de actividades relacionadas
														$notaIndicador = mysqli_fetch_array(mysqli_query($conexion, "SELECT ROUND(SUM(cal_nota*(act_valor/100)) / SUM(act_valor/100),2) FROM ".BD_ACADEMICA.".academico_calificaciones aac
														INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id=aac.cal_id_actividad AND aa.act_estado=1 AND aa.act_id_tipo='".$resultado['ipc_indicador']."' AND aa.act_periodo='".$periodoConsultaActual."' AND aa.act_id_carga='".$cargaConsultaActual."' AND aa.institucion={$config['conf_id_institucion']} AND aa.year={$_SESSION["bd"]}
														WHERE aac.cal_id_estudiante='".$datosEstudianteActual['mat_id']."' AND aac.institucion={$config['conf_id_institucion']} AND aac.year={$_SESSION["bd"]}"), MYSQLI_BOTH);
														 
														$notaRecuperacion = "";
														if(!empty($notas['rind_nota']) and $notas['rind_nota']>$notas['rind_nota_original'] and $notas['rind_nota']>$notaIndicador[0]){
															$notaRecuperacion = $notas['rind_nota'];
															
															//Color nota
															if($notaRecuperacion<$config[5] and $notaRecuperacion!="") $colorNota = $config[6]; elseif($notaRecuperacion>=$config[5]) $colorNota = $config[7];
														}

														$notaFinal=$notasResultado;
														if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
															$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasResultado);
															$notaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
														}

														$notaRecuperacionFinal=$notaRecuperacion;
														if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
															$estiloNotaRecuperacion = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaRecuperacion);
															$notaRecuperacionFinal= !empty($estiloNotaRecuperacion['notip_nombre']) ? $estiloNotaRecuperacion['notip_nombre'] : "";
														}

													?>
														<tr>
															<td><?= $contReg; ?></td>
															<td><?= $resultado['ipc_indicador']; ?></td>
															<td><?= $resultado['ind_nombre']; ?></td>
															<td align="center"><?= $resultado['ipc_valor']; ?>%</td>
															<td align="center"><?= $sumaNotas[1]; ?>%</td>

															<td align="center" style="width: 100px; text-align:center; color:<?php if ($notasResultado < $config[5] and $notasResultado != "") echo $config[6];
																																																																					elseif ($notasResultado >= $config[5]) echo $config[7];
																																																																					else echo "black"; ?>;">
																<?= $notaFinal; ?>
															</td>
															<td style="text-align: center; color:<?=$colorNota;?>"><?=$notaRecuperacionFinal;?></td>
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
		<?php // include("../compartido/panel-configuracion.php"); ?>
	</div>
	<!-- end page container -->
	<?php include("../compartido/footer.php"); ?>
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!-- end js include path -->

</body>

</html>