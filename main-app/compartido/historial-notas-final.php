<?php

$input = json_decode(file_get_contents("php://input"), true);
if (!empty($input)) {
	$datos = $input;
	$estudiantes = $datos["data"];
	$periodo = $datos["periodo"];
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
	
	include("session-compartida.php");
	$tiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year)->fetch_all(MYSQLI_ASSOC); 
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
                                      style="height: 40px;font-size: 16px;" readonly
                                      value="<?= $carga['periodos'][$periodo["periodo"]]["bol_nota"] ?>" />

                                  </div>
                                </span>
                              </td>
                            <?php } ?>
                            <td>
                              <input type="number" class="form-control" style="height: 50px;font-size: 30px;" readonly
                                value="<?= $carga['nota_final'] ?>" />
                            </td>
                          </tr>
                        <?php } ?>
                      <?php } ?>
                    </tbody>
                  </table>
	


	
