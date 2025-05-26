<?php
$active = "active";

$input = json_decode(file_get_contents("php://input"), true);
if (!empty($input)) {
	$datos = $input;
	$active = "";
	$estudiantes = $datos["data"];
	$periodos_cant = $datos["periodos"];
	$year = $datos["year"];
	$grado = $datos["grado"];
	$grupo = $datos["grupo"];
	$curso["year"] = $year;
	$curso["curso"] = $grado;
	$curso["grupo"] = $grupo;

	$estudiante = $estudiantes[$datos["estudiante"]];
	$estudiante["year"] = $year;
	$estudiante["gra_id"] = $grado;
	$estudiante["gru_id"] = $grupo;
	$estudiante["cursos"][$grado . "-" . $grupo . "-" . $year] =
		[
			"curso" => $grado,
			"grupo" => $grupo,
			"year" => $year
		];
	$periodos = [];

	for ($i = 1; $i <= $periodos_cant; $i++) {
		$periodos[$i] = $i;
	}
	include("session-compartida.php");
	$tiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year)->fetch_all(MYSQLI_ASSOC);
	$llave_curso_final = $estudiante["mat_id"] . "-" . $year . "-" . $grado . "-" . $grupo;

	function retornarColor($valor, bool $recuperado = false)
{
	$color = "";
	$valor ??= 0;

	if ($valor <= 5) {
		$color = "bg-danger";
	} else if ($valor > 5 && $valor < 50) {
		$color = "bg-warning";
	} elseif ($valor > 50 && $valor < 99) {
		$color = "";
	} elseif ($valor >= 100) {
		$color = "bg-success";
	}
	if ($recuperado) {
		$color = "bg-success";
	}

	return $color;
}

}

?>
<?php foreach ($estudiante["cursos"] as $curso) { ?>
	<?php foreach ($periodos as $periodo) {
		$llave_curso_periodo_defaul = $estudiante["mat_id"] . "-" . $estudiante["year"] . "-" . $estudiante["gra_id"] . "-" . $estudiante["gru_id"] . "-1";
		$llave_curso_periodo = $estudiante["mat_id"] . "-" . $curso["year"] . "-" . $curso["curso"] . "-" . $curso["grupo"] . "-" . $periodo;
		$llave_curso = $estudiante["mat_id"] . "-" . $curso["year"] . "-" . $curso["curso"] . "-" . $curso["grupo"];
		?>
		<?php if ($llave_curso_periodo == $estudiante["mat_id"] . "-" . $year . "-" . $grado . "-" . $grupo . "-" . $periodo) { ?>
			<div class="tab-pane panel-<?= $estudiante["mat_id"] ?> fade show <?php if ($llave_curso_periodo == $llave_curso_periodo_defaul) {
				   echo $active;
			   } ?>" id="contend-<?= $llave_curso_periodo ?>" role="tabpanel" aria-labelledby="btn-<?= $llave_curso_periodo ?>">

				<table class="table table-striped table-bordered">
					<thead>
						<tr class="table-active" style="border-style: hidden;">
							<th scope="col" weight="10%" style="font-weight:bold; text-align:center;vertical-align: middle;">
								AREAS
							</th>
							<th scope="col" weight="70%"
								style="font-weight:bold; font-size: 16px;text-align:center;vertical-align: middle;">
								ASIGNATURAS
							</th>
							<th scope="col" weight="20%"
								style="font-weight:bold; font-size: 16px;text-align:center;vertical-align: middle;">
								NOTA AREA
							</th>
						</tr>
					</thead>
					<tbody id="tbody-<?= $llave_curso_periodo ?>">
						<!-- AREAS -->
						<?php foreach ($estudiante["areas"] as $area) {
							$llave_curso_periodo_area = $llave_curso_periodo . "-" . $area["ar_id"];
							?>
							<tr>
								<td class=" col-3" style="font-weight:bold; font-size: 13px;text-align:left;vertical-align: middle;"
									scope="row">
									<?= strtoupper($area['ar_nombre']); ?>

								</td>
								<td class="col-7" style="vertical-align: middle;">
									<!-- ASIGNATURAS -->
									<div class="card">
										<?php foreach ($area["cargas"] as $carga) {
											$llave_curso_periodo_area_carga = $llave_curso_periodo_area . "-" . $carga["car_id"];
											?>

											<div style="cursor:pointer;font-weight:bold;font-size: 12px"
												class="card-header list-group-item d-flex justify-content-between">

												<span class="col-2 toggle-collapse"
													data-target="#carga_<?= $llave_curso_periodo_area_carga ?>">
													<?= $carga['mat_valor'] ?>%
												</span>
												<span class="col-6 toggle-collapse"
													data-target="#carga_<?= $llave_curso_periodo_area_carga ?>">
													<?= strtoupper($carga['mat_nombre']); ?>
													<div class="progress" style="margin-right: 20px;">
														<div class="progress-bar progress-bar-striped <?= retornarColor($carga['periodos'][$periodo]["porcentaje_carga_realizado"], $carga['periodos'][$periodo]["bol_tipo"] == "2") ?> "
															role="progressbar"
															style="width:  <?= $carga['periodos'][$periodo]["bol_tipo"] == "2" ? 100 : $carga['periodos'][$periodo]["porcentaje_carga_realizado"] ?>%;"
															aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
															<?= $carga['periodos'][$periodo]["bol_tipo"] == "2" ? 100 : $carga['periodos'][$periodo]["porcentaje_carga_realizado"] ?>%
														</div>
														<div class="progress-bar progress-bar-striped bg-danger" role="progressbar"
															style="width: <?= $carga['periodos'][$periodo]["bol_tipo"] == "2" ? 0 : 100 - $carga['periodos'][$periodo]["porcentaje_carga_realizado"] ?>%"
															aria-valuenow="<?= 100 - $carga['periodos'][$periodo]["porcentaje_carga_realizado"] ?>"
															aria-valuemin="0" aria-valuemax="100"></div>
													</div>
												</span>
												<span class="col-4">
													<?php if (!empty($carga['periodos'][$periodo]["bol_tipo"]) && $carga['periodos'][$periodo]["bol_tipo"] == "2") { ?>

														<div class="col-12">
															<div class="input-group">

																<?php if (!empty($carga['periodos'][$periodo]["bol_nota_anterior"])) { ?>
																	<input type="number" class="form-control"
																		style="height: 30px;font-size: 11px;color:red;border-radius: .25rem .0rem .0rem .0rem"
																		data-bs-toggle='popover' data-bs-placement='left'
																		data-bs-trigger='hover focus' data-bs-content="Nota anterior" readonly
																		value="<?= Boletin::notaDecimales($carga['periodos'][$periodo]["bol_nota_anterior"]) ?>" />
																<?php } ?>

																<input type="number" class="form-control" data-bs-toggle='popover'
																	data-bs-placement='right' data-bs-trigger='hover focus'
																	data-bs-content="Nota recuperada"
																	style="height: 30px;font-size: 11px;color:blue;border-radius: .0rem .25rem .0rem .0rem"
																	readonly
																	value="<?= Boletin::notaDecimales($carga['periodos'][$periodo]["bol_nota"]) ?>" />
															</div>
														</div>
														<div class="col-12">
															<div class="input-group">
																<?php if (!empty($carga['periodos'][$periodo]["bol_nota_anterior"])) { ?>
																	<input type="text" class="form-control"
																		style="height: 20px;font-size: 10px;color:red;border-top:0px;border-radius: .0rem .0rem .0rem .25rem;"
																		readonly
																		value="<?= Boletin::determinarRango($carga['periodos'][$periodo]["bol_nota_anterior"], $tiposNotas)["notip_nombre"] ?>" />
																<?php } ?>

																<input type="text" class="form-control"
																	style="height: 20px;font-size: 10px;color:blue;border-top:0px;border-radius: .0rem .0rem .25rem .0rem;"
																	readonly
																	value="<?= Boletin::determinarRango($carga['periodos'][$periodo]["bol_nota"], $tiposNotas)["notip_nombre"] ?>" />
															</div>
														</div>
													<?php } else { ?>
														<?php $tooltipCarga = ($carga['periodos'][$periodo]['bol_nota'] != $carga['periodos'][$periodo]['nota_carga_calculada'] && $carga['periodos'][$periodo]['nota_carga_calculada'] > $carga['periodos'][$periodo]['bol_nota']) ? " data-bs-toggle='popover' data-bs-placement='right' data-bs-trigger='hover focus' title='Nota carga calculada' data-bs-content='{$carga['periodos'][$periodo]['nota_carga_calculada']}' " : ""; ?>
														<input type="number" class="form-control" <?= $tooltipCarga ?>
															style="height: 30px;font-size: 14px;border-radius: .25rem .25rem .0rem .0rem"
															readonly
															value="<?= Boletin::notaDecimales($carga['periodos'][$periodo]["bol_nota"]) ?>" />


														<input type="text" class="form-control"
															style="height: 20px;font-size: 12px;border-top:0px;border-radius: .0rem .0rem .25rem .25rem;"
															readonly
															value="<?= Boletin::determinarRango($carga['periodos'][$periodo]["bol_nota"], $tiposNotas)["notip_nombre"] ?>">

													<?php } ?>
												</span>
											</div>
											<div class="collapse-container" id="carga_<?= $llave_curso_periodo_area_carga ?>">
												<!-- INDICADORES -->
												<table class="table" width="100%" cellspacing="5" cellpadding="5" rules="all">
													<thead class="toggle-collapse"
														data-target="#carga_<?= $llave_curso_periodo_area_carga ?>">
														<tr class="table-active"
															style="height: 30px; font-weight: bold;font-size: 11px;text-align: center;padding: 5px;cursor:pointer;border-style: hidden;">
															<td> INDICADORES</td>
														</tr>
													</thead>
													<tbody>
														<tr style="font-weight: bold;font-size: 11px;">
															<td>
																<?php foreach ($carga['periodos'][$periodo]["indicadores"] as $indicador) {
																	$llave_curso_periodo_area_carga_indicador = $llave_curso_periodo_area_carga . "-" . $indicador["ind_id"]; ?>
																	<div style="cursor:pointer"
																		class="card-header list-group-item d-flex justify-content-between align-items-center">

																		<span class="col-2 toggle-collapse"
																			data-target="#indicador_<?= $llave_curso_periodo_area_carga_indicador ?>"><?= $indicador['ipc_valor'] ?>%</span>
																		<span class="col-7 toggle-collapse"
																			data-target="#indicador_<?= $llave_curso_periodo_area_carga_indicador ?>"
																			style="font-weight: lighter;font-size: 11px;">
																			<?= strtolower($indicador["ind_nombre"]); ?>

																			<div class="progress-indicador"
																				style="height: 5px;margin-right: 20px;">
																				<div class="progress-bar progress-bar-striped <?= retornarColor($indicador["progreso_indicador"], $indicador['recuperado']) ?>"
																					role="progressbar"
																					style="width: <?= $indicador['recuperado'] ? 100 : $indicador["progreso_indicador"] ?>%"
																					aria-valuenow="<?= $indicador["progreso_indicador"] ?>"
																					aria-valuemin="0" aria-valuemax="100"></div>
																				<div class="progress-bar progress-bar-striped bg-danger"
																					role="progressbar"
																					style="width: <?= 100 - $indicador["progreso_indicador"] ?>%"
																					aria-valuenow="<?= 100 - $indicador["progreso_indicador"] ?>"
																					aria-valuemin="0" aria-valuemax="100"></div>
																			</div>
																		</span>
																		<span class="col-3">

																			<?php if ($indicador['recuperado']) { ?>
																				<div class="col-12">
																					<div class="input-group">
																						<input type="number" class="form-control" readonly
																							tabindex="0" data-bs-placement="left"
																							data-bs-toggle="popover" data-bs-trigger="hover focus"
																							data-bs-content="Nota anterior"
																							style="height: 25px;font-size: 10px;color:red;border-radius: .25rem .0rem .0rem .0rem;"
																							value="<?= Boletin::notaDecimales($indicador['nota_indicador']) ?>" />
																						<input type="number" class="form-control" readonly
																							tabindex="0" data-bs-placement="right"
																							data-bs-toggle="popover" data-bs-trigger="hover focus"
																							data-bs-content="Recuperado"
																							style="height: 25px;font-size: 10px;color:blue;border-radius: .0rem .25rem .0rem .0rem;"
																							value="<?= Boletin::notaDecimales($indicador['nota_indicador_recuperado']) ?>" />
																					</div>
																				</div>
																				<div class="col-12">
																					<div class="input-group">
																						<input type="text" class="form-control" readonly
																							tabindex="0" data-bs-placement="left"
																							data-bs-toggle="popover" data-bs-trigger="hover focus"
																							data-bs-content="Nota anterior"
																							style="height: 15px;font-size: 10px;color:red;border-top:0px;border-radius: .0rem .0rem .0rem .25rem;"
																							value="<?= Boletin::determinarRango($indicador['nota_indicador'], $tiposNotas)["notip_nombre"] ?>" />
																						<input type="text" class="form-control" readonly
																							tabindex="0" data-bs-placement="right"
																							data-bs-toggle="popover" data-bs-trigger="hover focus"
																							data-bs-content="Recuperado"
																							style="height: 15px;font-size: 10px;color:blue;border-top:0px;border-radius: .0rem .0rem .25rem .0rem;"
																							value="<?= Boletin::determinarRango($indicador['nota_indicador_recuperado'], $tiposNotas)["notip_nombre"] ?>" />
																					</div>
																				</div>
																			<?php } else { ?>
																				<div class="input-group">
																					<input type="number" class="form-control" readonly
																						style="height: 25px;font-size: 13px;"
																						value="<?= Boletin::notaDecimales($indicador['nota_indicador']) ?>" />
																					<input type="text" class="form-control" readonly
																						style="height: 25px;font-size: 13px;"
																						value="<?= Boletin::determinarRango($indicador['nota_indicador'], $tiposNotas)["notip_nombre"] ?>" />
																				</div>
																			<?php } ?>

																		</span>
																	</div>
																	<div class="collapse-container"
																		id="indicador_<?= $llave_curso_periodo_area_carga_indicador ?>">
																		<!-- ACTIVIDADES -->
																		<table class="table table-striped table-bordered"
																			 width="100%" rules="all">
																			<thead class="toggle-collapse"
																				data-target="#indicador_<?= $llave_curso_periodo_area_carga_indicador ?>">
																				<tr class="table-active"
																					style="font-weight: bold;font-size: 11px;text-align: center;padding: 5px;cursor:pointer;border-style: hidden;">
																					<td colspan="3"> ACTIVIDADES</td>
																				</tr>
																			</thead>
																			<?php foreach ($indicador['actividades'] as $actividad) {
																				?>
																				<tr style="height: 30px;font-size: 11px; ">
																					<td width="30px"> 
																						<span class="col-2"><?= $actividad['act_valor'] ?>%</span>
																					</td>
																					<td width="400px"
																						style="height: 30px;font-weight: lighter;font-size: 10px">
																						<?= $actividad['act_descripcion'] ?>
																					</td>
																					<td width="100px">
																						<input type="number" class="form-control" readonly
																							style="height: 25px;font-size: 12px;"
																							value="<?= $actividad['cal_nota'] ?>">
																					</td>
																				</tr>
																			<?php } ?>
																		</table>

																	</div>
																<?php } ?>
															</td>
														</tr>
													</tbody>
												</table>
											</div>

										<?php } ?>
									</div>
								</td>
								<td class="col-2" style="height: 30px; font-weight: bold;font-size: 16px;vertical-align: middle;">
									<?php $tooltipArea = ($area['periodos'][$periodo]['nota_area'] != $area['periodos'][$periodo]['nota_area_calculada'] && $area['periodos'][$periodo]['nota_area_calculada'] > $area['periodos'][$periodo]['nota_area']) ? " data-bs-toggle='popover' data-bs-placement='left' data-bs-trigger='hover focus' title='Nota Area calculada' data-bs-content='{$area['periodos'][$periodo]['nota_area_calculada']}' " : ""; ?>
									<input type="number" class="form-control"
										style="height: 60px;font-size: 30px;border-radius: .25rem .25rem .0rem .0rem" readonly
										<?= $tooltipArea ?>
										value="<?= Boletin::notaDecimales($area['periodos'][$periodo]["nota_area"]) ?>">
									<input type="text" class="form-control"
										style="height: 30px;font-size: 20px;border-top:0px;border-radius: .0rem .0rem .25rem .25rem;"
										readonly <?= $tooltipArea ?>
										value="<?= Boletin::determinarRango($area['periodos'][$periodo]["nota_area"], $tiposNotas)["notip_nombre"] ?>">

								</td>
							</tr>

						<?php } ?>

					</tbody>
				</table>
			</div>
		<?php } ?>
	<?php } ?>
<?php } ?>
<div class="tab-pane panel-<?= $estudiante["mat_id"] ?> fade show" id="contend-<?= $llave_curso_final ?>-final"
	role="tabpanel" aria-labelledby="btn-<?= $llave_curso_final ?>-final">

	<table class="table table-striped table-bordered">
		<thead>
			<tr style="font-weight:bold; text-align:center;" class="table-active" style="border-style: hidden;">
				<th scope="col" weight="70%"
					style="font-weight:bold; font-size: 16px;text-align:center;vertical-align: middle;">
					ASIGNATURAS
				</th>
				<?php foreach ($estudiante["periodos"] as $periodo) {
					?>
					<th scope="col" weight="100px"
						style="font-weight:bold; font-size: 16px;text-align:center;vertical-align: middle;">
						P <?= $periodo["periodo"] ?> (<?= $periodo["porcentaje_periodo"] ?>%)
					</th>
				<?php } ?>
				<th scope="col" weight="70%"
					style="font-weight:bold; font-size: 16px;text-align:center;vertical-align: middle;">
					NOTA FINAL
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($estudiante["areas"] as $area) { ?>
				<?php foreach ($area["cargas"] as $carga) { ?>
					<tr>
						<td>
							<span class="col-2 toggle-collapse">
								<?= $carga['mat_nombre'] ?>
							</span>
						</td>
						<?php foreach ($estudiante["periodos"] as $periodo) {
							?>
							<td scope="col" weight="100px"
								style="font-weight:bold; font-size: 16px;text-align:center;vertical-align: middle;">
								<span class="col-4">
									<div class="input-group">
										<?php $tooltipCarga = $carga['periodos'][$periodo["periodo"]]['bol_nota'] != $carga['periodos'][$periodo["periodo"]]['nota_carga_calculada'] ? " data-bs-toggle='popover' data-bs-placement='right' data-bs-trigger='hover focus' title='Nota carga calculada' data-bs-content='{$carga['periodos'][$periodo["periodo"]]['nota_carga_calculada']}' " : ""; ?>
										<input type="number" class="form-control" <?= $tooltipCarga ?>
											style="height: 40px;font-size: 16px;border-radius: .25rem .0rem .0rem .0rem;" readonly
											value="<?=  Boletin::notaDecimales($carga['periodos'][$periodo["periodo"]]["bol_nota"])  ?>" />
											<input type="text" class="form-control" <?= $tooltipCarga ?>
											style="height: 40px;font-size: 16px;border-radius: .0rem .25rem .0rem .0rem;" readonly
											value="<?= Boletin::determinarRango($carga['periodos'][$periodo["periodo"]]["bol_nota"], $tiposNotas)["notip_nombre"]   ?>" />

									</div>
								</span>
							</td>
						<?php } ?>
						<td>


							<div class="input-group">
								<input type="number" class="form-control" readonly data-bs-toggle="popover"
									style="height: 30px;font-size: 20px;border-radius: .25rem .0rem .0rem .0rem;"
									value="<?= Boletin::notaDecimales($carga['nota_final']) ?>" />
								<input type="text" class="form-control" readonly data-bs-toggle="popover"
									style="height: 30px;font-size: 15px;border-radius: .0rem .25rem .0rem .0rem;"
									value="<?= Boletin::determinarRango($carga['nota_final'], $tiposNotas)["notip_nombre"] ?>" />
							</div>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>

</div>