<?php include("session.php"); ?>

<?php $idPaginaInterna = 'DC0079'; ?>

<?php include("../compartido/historial-acciones-guardar.php"); ?>

<?php include("verificar-carga.php"); ?>

<?php include("../compartido/head.php"); ?>

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

							<div class="page-title"><?= $frases[252][$datosUsuarioActual['uss_idioma']]; ?></div>

						</div>

					</div>

				</div>



				<div class="row">

					<div class="col-md-12">

						<div class="row">



								<div class="col-md-6 col-lg-4">

								<?php include("info-carga-actual.php"); ?>

								<?php include("filtros-cargas.php"); ?>

								<?php include("../compartido/publicidad-lateral.php"); ?>



								</div>



							<div class="col-md-12 col-lg-8">

								<div class="card card-topline-purple">

									<div class="card-head">

										<header><?= $frases[252][$datosUsuarioActual['uss_idioma']]; ?></header>

										<div class="tools">

											<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>

											<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>

											<a class="t-close btn-color fa fa-times" href="javascript:;"></a>

										</div>

									</div>

									<div class="card-body">



										<div class="table-responsive">



											<table class="table table-striped custom-table table-hover">

												<thead>

													<tr>

														<th style="width: 50px;">#</th>

														<th style="width: 400px;"><?= $frases[61][$datosUsuarioActual[8]]; ?></th>

														<?php

														$cA = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
														INNER JOIN academico_indicadores ON ind_id=ipc_indicador
														WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "'");

														while ($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)) {

															echo '<th style="text-align:center; font-size:11px; width:100px;"><a href="indicadores-editar.php?idR=' . $rA['ipc_id'] . '">' . $rA['ind_nombre'] . '<br>

														' . $rA['ind_id'] . '<br>

														(' . $rA['ipc_valor'] . '%)</a>

														</th>';
														}

														?>

														<th style="text-align:center; width:60px;">%</th>

														<th style="text-align:center; width:60px;"><?= $frases[118][$datosUsuarioActual[8]]; ?></th>

													</tr>

												</thead>

												<tbody>

													<?php

													$contReg = 1;

													$consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas

													INNER JOIN usuarios ON uss_id=mat_id_usuario

													WHERE mat_grado='" . $datosCargaActual['car_curso'] . "' AND mat_grupo='" . $datosCargaActual['car_grupo'] . "' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido, mat_segundo_apellido, mat_nombres");

													while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {

														//DEFINITIVAS

														$carga = $cargaConsultaActual;

														$periodo = $periodoConsultaActual;

														$estudiante = $resultado[0];

														include("../definitivas.php");



														$colorEstudiante = '#000;';

														if ($resultado['mat_inclusion'] == 1) {
															$colorEstudiante = 'blue;';
														}

													?>



														<tr>

															<td style="text-align:center;" style="width: 100px;"><?= $contReg; ?></td>

															<td style="color: <?= $colorEstudiante; ?>">

																<img src="../files/fotos/<?= $resultado['uss_foto']; ?>" width="50">

																<?= strtoupper($resultado[3] . " " . $resultado[4] . " " . $resultado[5]); ?>

															</td>



															<?php

															$cA = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
															INNER JOIN academico_indicadores ON ind_id=ipc_indicador
															WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "'");

															while ($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)) {

																//LAS CALIFICACIONES
																$consultaSumaNotas=mysqli_query($conexion, "SELECT SUM(cal_nota * (act_valor/100)) FROM academico_calificaciones
																INNER JOIN academico_actividades ON act_id=cal_id_actividad AND act_id_tipo='" . $rA['ipc_indicador'] . "' AND act_periodo='" . $periodoConsultaActual . "' AND act_id_carga='" . $cargaConsultaActual . "' AND act_estado=1
																WHERE cal_id_estudiante=" . $resultado[0]);
																$sumaNotas = mysqli_fetch_array($consultaSumaNotas, MYSQLI_BOTH);

																$notasResultado = round($sumaNotas[0] / ($rA['ipc_valor'] / 100), $config['conf_decimales_notas']);

															?>

																<td style="width: 100px; text-align:center;">
																	<a href="calificaciones-estudiante.php?usrEstud=<?= $resultado['mat_id_usuario']; ?>&periodo=<?= $periodoConsultaActual; ?>&carga=<?= $cargaConsultaActual; ?>&indicador=<?= $rA['ipc_indicador']; ?>" style="color:<?php if ($notasResultado < $config[5] and $notasResultado != "") echo $config[6];
																																																																						elseif ($notasResultado >= $config[5]) echo $config[7];
																																																																						else echo "black"; ?>; text-decoration:underline;"><?= $notasResultado; ?></a>
																</td>

															<?php

															}

															if ($definitiva < $config[5] and $definitiva != "") $colorDef = $config[6];
															elseif ($definitiva >= $config[5]) $colorDef = $config[7];
															else $colorDef = "black";

															?>



															<td style="text-align:center;"><?= $porcentajeActual; ?></td>

															<td style="color:<?php if ($definitiva < $config[5] and $definitiva != "") echo $config[6];
																				elseif ($definitiva >= $config[5]) echo $config[7];
																				else echo "black"; ?>; text-align:center; font-weight:bold;"><a href="calificaciones-estudiante.php?usrEstud=<?= $resultado['mat_id_usuario']; ?>&periodo=<?= $periodoConsultaActual; ?>&carga=<?= $cargaConsultaActual; ?>" style="text-decoration:underline; color:<?= $colorDef; ?>;"><?= $definitiva; ?></a></td>

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