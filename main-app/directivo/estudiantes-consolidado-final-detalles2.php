<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0081'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php"); ?>
<?php
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
$year = $agnoBD;
?>

<?php
$consultaCurso = Grados::obtenerDatosGrados($_POST["curso"]);
$curso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);

$consultaGrupo = Grupos::obtenerDatosGrupos($_POST["grupo"]);
$grupo = mysqli_fetch_array($consultaGrupo, MYSQLI_BOTH);

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
	$disabledPermiso = "disabled";
}
?>
<style type="text/css">
	/* body{
		overflow: hidden;
	} */
	.table-container {
		height: 600px;
		/* Altura del contenedor */
		overflow: auto;
	}

	.scrollable-table {
		border-collapse: collapse;
	}

	/* .table-container table {
  border-collapse: separate;
  border-spacing: 0;
} */

	.table-container table thead {
		position: -webkit-sticky;
		position: sticky;
		top: 0;
		z-index: 4;
	}


	.css_doc {
		position: sticky !important;
		z-index: 4;
		left: 0;
	}

	.css_nombre {
		position: sticky !important;
		z-index: 4;
		left: 64px;
	}

/* Fija la ultima columna de thhead*/
	.css_prom {
		position: sticky;
		z-index: 4;
		right: 0;
	}


	.scrollable-table tbody td:nth-child(1),
	.scrollable-table tbody td:nth-child(2) {
		background-color: #f2f2f2;
		position: sticky;
		z-index: 3;
	}

	.scrollable-table tbody td:nth-child(1) {
		left: 0;
	}

	.scrollable-table tbody td:nth-child(2) {
		left: 63px;
	}
	/* Fija la ultima columna de thbody*/
	/* .scrollable-table tbody td:last-child {
		background-color: #f2f2f2;
		position: sticky;
		right: 0;
		z-index: 3;
	} */
</style>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<!-- data tables -->
<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>

<script src="../../config-general/assets/js/pages/table/table_data.js"></script>
<script type="text/javascript">
	function def(enviada) {
		var nota = enviada.value;
		var codEst = enviada.id;
		var carga = enviada.name;
		var per = enviada.alt;
		if (alertValidarNota(nota)) {
			return false;
		}
		$('#resp').empty().hide().html("Esperando...").show(1);
		datos = "nota=" + (nota) +
			"&carga=" + (carga) +
			"&codEst=" + (codEst) +
			"&per=" + (per);
		$.ajax({
			type: "POST",
			url: "../compartido/ajax-periodos-registrar.php",
			data: datos,
			success: function(data) {
				$('#resp').empty().hide().html(data).show(1);
			}
		});

	}
</script>

</head>

<body style="font-family:Arial;">


	<div class="row">

		<div class="col-md-8 col-lg-12">
			<div class="card card-topline-purple">
				<div class="card-head">
					<header>Consolidado Final
					<b>Curso:</b> <?php if (isset($curso['gra_nombre'])) {
												echo $curso['gra_nombre'];
											} ?>&nbsp;&nbsp;&nbsp; <b>Grupo:</b> <?php if (isset($grupo['gru_nombre'])) {
																						echo $grupo['gru_nombre'];
																					} ?>
					</header>

				</div>
				<div class="card-body">

					
					
					<input type="hidden" name="periodo" value="<?php if (isset($_POST["periodo"])) {
																	echo $_POST["periodo"];
																} ?>" id="periodo">

					<div class="row" style="margin-bottom: 10px;">
						
						<div class="col-sm-4">
							<div class="btn-group">
								<a href="../compartido/informe-consolidad-final.php?curso=<?= base64_encode($_POST["curso"]); ?>&grupo=<?= base64_encode($_POST["grupo"]); ?>" id="addRow" class="btn deepPink-bgcolor" target="_blank">
									Sacar Informe
								</a>
							</div>
							<div class="btn-group">
							
								<a class="btn alert alert-block alert-warning" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
									<i class="fa fa-eye"></i>Informacion Importante!
								</a>								
							</div>
						</div>



					</div>
					<div row="col-sm-4">
					<span id="resp"></span>
						<div class="collapse" id="collapseExample">
									<div class="alert alert-block alert-warning">
										<h4 class="alert-heading">Información importante!</h4>
										<p> 1 ): Digite la nota para cada estudiante en el periodo y materia correspondiente y pulse Enter o simplemente cambie de casilla para que los cambios se guarden automaticamente.</p>
						<p style="font-weight:bold;">Por favor despu&eacute;s de digitar una nota, espere un momento a que el sistema le indique que la nota se guard&oacute; y prosiga con la siguiente.</p>
										<p> 2): La definitiva de cada materia se obtiene del promedio de los periodos. Para que esta definitiva pueda ser correcta debe estar la nota de todos los periodos registada.</p>
										
									</div>
								</div>
					</div>


					<div class="table-container">
						<?php try { ?>

							<table  id="example1" width="100%" class="scrollable-table" cellspacing="5" cellpadding="5" rules="all" style="
 							 border:solid; 
 							 border-color:<?= $Plataforma->colorUno; ?>; 
 						 	font-size:11px;">
								<thead>
									<tr style="font-weight:bold; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
										<th rowspan="2" class="css_doc"  style="font-weight:bold;background:<?= $Plataforma->colorUno; ?>; color:#FFF;" width="100px">Doc</th>
										<th rowspan="2" class="css_nombre" style="font-weight:bold; background:<?= $Plataforma->colorUno; ?>; color:#FFF;" width="400px">Estudiante</th>
										<?php
										//SACAMOS EL NUMERO DE CARGAS O MATERIASQUE TIENE UN CURSO PARAQUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
										$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_POST["curso"], $_POST["grupo"]);
										$numCargasPorCurso = mysqli_num_rows($cargas);
										while ($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
										?>
											<th width="<?= ($config[19] + 1) * 50; ?>px" style="font-size:9px; text-align:center; border:groove;" colspan="<?= $config[19] + 1; ?>"><?= $carga['mat_nombre']; ?></th>
										<?php
										}
										?>
										<th rowspan="2" width="100px"  style="z-index: 4;text-align:center; background-color:<?= $Plataforma->colorUno; ?>">PROM</th>
									</tr>

									<tr>
										<?php
										$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_POST["curso"], $_POST["grupo"]);
										while ($carga = mysqli_fetch_array($cargas)) {
											$p = 1;
											//PERIODOS DE CADA MATERIA
											while ($p <= $config[19]) {
												echo '<th  style="text-align:center;background-color: #f2f2f2;" >' . $p . '</th>';
												$p++;
											}
											//DEFINITIVA DE CADA MATERIA
											echo '<th   style="text-align:center; background:#FFC">DEF</th>';
										}
										?>
									</tr>

								</thead>
								<!-- END -->
								<!-- BEGIN -->
								<tbody>
									<?php
									$filtro = " AND mat_grado='" . $_POST["curso"] . "' AND mat_grupo='" . $_POST["grupo"] . "' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
									$consulta =Estudiantes::listarEstudiantesEnGrados($filtro,"",$curso,$_POST["grupo"]);
									//PRIMER PUESTO
									$primerPuestoNota = 0;
									$primerPuestoNombre = '';
									$primerPuestoID = 0;
									//SEGUNDO PUESTO
									$segundoPuestoNota = 0;
									$segundoPuestoNombre = '';
									$segundoPuestoID = 0;
									while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
										$defPorEstudiante = 0;
									?>
										<tr style="border-color:<?= $Plataforma->colorDos; ?>;">
											<td style="font-size:9px;" scope="row" width="100px"><?= $resultado['mat_documento']; ?></td>
											<td style="font-size:9px;" scope="row" width="400px"><?= Estudiantes::NombreCompletoDelEstudiante($resultado); ?></td>
											<?php
											$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_POST["curso"], $_POST["grupo"]);
											while ($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
												$p = 1;
												$defPorMateria = 0;
												//PERIODOS DE CADA MATERIA
												while ($p <= $config[19]) {
													$boletin = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $resultado['mat_id'], $carga['car_id']);
													if (isset($boletin['bol_nota']) and $boletin['bol_nota'] < $config[5] and $boletin['bol_nota'] != "") $color = $config[6];
													elseif (isset($boletin['bol_nota']) and $boletin['bol_nota'] >= $config[5]) $color = $config[7];
													if (isset($boletin['bol_nota'])) {
														$defPorMateria += $boletin['bol_nota'];
													}
													if (isset($boletin['bol_tipo']) and $boletin['bol_tipo'] == 1) $tipo = '<span style="color:blue; font-size:9px;">Normal</span>';
													elseif (isset($boletin['bol_tipo']) and $boletin['bol_tipo'] == 2) $tipo = '<span style="color:red; font-size:9px;">Recuperaci&oacute;n Per.</span>';
													elseif (isset($boletin['bol_tipo']) and $boletin['bol_tipo'] == 3) $tipo = '<span style="color:red; font-size:9px;">Recuperaci&oacute;n Ind.</span>';
													elseif (isset($boletin['bol_tipo']) and $boletin['bol_tipo'] == 4) $tipo = '<span style="color:red; font-size:9px;">Directivo</span>';

													else $tipo = '';
													//DEFINITIVA DE CADA PERIODO

													$disabled = "";
													if ((isset($boletin['bol_nota']) and ($boletin['bol_nota'] != "" or $carga['car_periodo'] <= $p)) and $config['conf_editar_definitivas_consolidado'] != true) {
														$disabled = "disabled";
													}
											?>
													<td style="text-align:center;" width="30px">
														<input style="text-align:center; width:30px; 
																color:<?= $color; ?>" 
																value="<?php if (isset($boletin['bol_nota'])) {echo $boletin['bol_nota'];} ?>" 
																name="<?= $carga['car_id']; ?>" id="<?= $resultado['mat_id']; ?>" 
																onChange="def(this)" 
																alt="<?= $p; ?>" 
																title="Materia: <?= $carga['mat_nombre']; ?> - Periodo: <?= $p; ?>" 
																<?= $disabled; ?> 
																<?= $disabledPermiso; ?>
														/>
														<br><?= $tipo;?>
													</td>
												<?php
													$p++;
												}
												$defPorMateria = round($defPorMateria / $config[19], 2);
												//DEFINITIVA DE CADA MATERIA
												if ($defPorMateria < $config[5] and $defPorMateria != "") $color = $config[6];
												elseif ($defPorMateria >= $config[5]) $color = $config[7];
												//CONSULTAR NIVELACIONES
												$consultaNiv = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $resultado['mat_id'], $carga['car_id']);
												$cNiv = mysqli_fetch_array($consultaNiv, MYSQLI_BOTH);
												if (isset($cNiv['niv_definitiva']) and $cNiv['niv_definitiva'] > $defPorMateria) {
													$defPorMateria = $cNiv['niv_definitiva'];
													$msj = 'Nivelación';
												} else {
													$defPorMateria = $defPorMateria;
													$msj = '';
												}
												?>
												<td style="text-align:center; background:#FFC;" width="30px">
													<input style="text-align:center; width:30px; font-weight:bold; color:<?= $color; ?>" 
														value="<?php if (isset($defPorMateria)) {echo $defPorMateria;} ?>"
														disabled
													>
													<br>
													<span style="font-size:10px; color:rgb(255,0,0); font-weight:bold;">
														<?php if (isset($msj)) {echo $msj;} ?>
														<br>
														<?php if (isset($cNiv['niv_acta']) and isset($cNiv['niv_fecha_nivelacion'])) {echo "Acta " . $cNiv['niv_acta'] . " de " . $cNiv['niv_fecha_nivelacion'];} ?>
													</span>
											    </td>
											<?php
												//DEFINITIVA POR CADA ESTUDIANTE DE TODAS LAS MATERIAS Y PERIODOS
												$defPorEstudiante += $defPorMateria;
											}
											$defPorEstudiante = round($defPorEstudiante / $numCargasPorCurso, 2);
											if ($defPorEstudiante < $config[5] and $defPorEstudiante != "") $color = $config[6];
											elseif ($defPorEstudiante >= $config[5]) $color = $config[7];
											?>
											<td style="text-align:center; font-weight:bold; color:<?= $color; ?>"><?= $defPorEstudiante; ?></td>
										</tr>
									<?php
									}
									?>

								</tbody>
							</table>

						<?php
						} catch (Exception $e) {
							include("../compartido/error-catch-to-report.php");
						}
						?>
					</div>
				</div>
			</div>
		</div>






	</div>
</body>

</html>