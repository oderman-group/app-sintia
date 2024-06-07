<?php
include("session.php");
$idPaginaInterna = 'DT0076';
include("../compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
	$disabledPermiso = "disabled";
}
$consultaCurso = Grados::obtenerDatosGrados($_REQUEST["curso"]);
$curso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);
?>
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
	function niv(enviada) {
		var nota = enviada.value;
		var codEst = enviada.id;
		var carga = enviada.name;
		var op = enviada.alt;
		if (op == 1) {
			if (alertValidarNota(nota)) {
				return false;
			}
		}
		$('#resp').empty().hide().html("Esperando...").show(1);
		datos = "nota=" + (nota) +
			"&carga=" + (carga) +
			"&codEst=" + (codEst) +
			"&op=" + (op);
		$.ajax({
			type: "POST",
			url: "../compartido/ajax-nivelaciones-registrar.php",
			data: datos,
			success: function(data) {
				$('#resp').empty().hide().html(data).show(1);
			}
		});

	}
</script>
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
		background: blue;
		position: sticky !important;
		z-index: 4;
		left: 0;
	}

	.css_nombre {
		background: red;
		position: sticky !important;
		z-index: 4;
		left: 40px;
	}

	.scrollable-table td:nth-child(1),
	.scrollable-table td:nth-child(2) {
		background-color: #f2f2f2;
		position: sticky;
		z-index: 3;
	}

	.scrollable-table tbody td:nth-child(1) {
		left: 0;
	}

	.scrollable-table tbody td:nth-child(2) {
		left: 40px;
	}
</style>
</head>
<!-- END HEAD -->

<body Style="font-family: Arial;">

		<?php
		$curso = Grados::obtenerGrado($_REQUEST["curso"]);
		$consultaGrupo = Grupos::obtenerDatosGrupos($_REQUEST["grupo"]);
		$grupo = mysqli_fetch_array($consultaGrupo, MYSQLI_BOTH);
		?>
		<div class="row">

			<div class="col-md-12 col-lg-12">



				<div class="card card-topline-purple">
					<div class="card-head">
						<header><b>Nivelaciones Curso:</b> <?= $curso['gra_nombre']; ?>&nbsp;&nbsp;&nbsp; <b>Grupo:</b> <?= $grupo['gru_nombre']; ?></header>
						<div class="tools">
							<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
							<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
							<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
						</div>
					</div>
					<div class="card-body">

						<div class="row" style="margin-bottom: 10px;">
							<div class="col-sm-12">
								<div class="btn-group">
									<a href="../compartido/informe-nivelaciones.php?curso=<?= base64_encode($_REQUEST["curso"]); ?>&grupo=<?= base64_encode($_REQUEST["grupo"]); ?>" id="addRow" class="btn deepPink-bgcolor" target="_blank">
										Sacar Informe
									</a>
								</div>
								<div class="btn-group">

									<a class="btn alert alert-block alert-info" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
										<i class="fa fa-eye"></i>Informacion Importante!
									</a>
								</div>
							</div>

						</div>
						<div class="row">
							<div row="col-sm-12">
								<div id="resp"></div>
								<div class="collapse" id="collapseExample">
									<div class="alert alert-block alert-info">
										<h4 class="alert-heading">Información importante!</h4>
										<p>Digite la Nivelación, el acta y la fecha para cada estudiante en la materia correspondiente y pulse Enter o simplemente cambie de casilla para que los cambios se guarden automaticamente.</p>
										<p style="font-weight:bold;">Por favor despu&eacute;s de digitar cada dato, espere un momento a que el sistema le indique que estos se guadaron y prosiga.</p>
										<p style="font-weight:bold;">Para ver los cambios reflejados en pantalla debe actualizar (Tecla F5) la página.</p>
									</div>
								</div>
							</div>
						</div>

						<div class="table-container">
							<table id="example1" class="scrollable-table" cellspacing="5" cellpadding="5" rules="all" style="
 							 border:solid; 
 							 border-color:<?= $Plataforma->colorUno; ?>; 
 						 	font-size:11px;">
								<thead>
									<tr>
										<th rowspan="2" style="font-weight:bold;background:<?= $Plataforma->colorUno; ?>; color:#FFF;" class="css_doc">Mat</th>
										<th rowspan="2" style="font-weight:bold;background:<?= $Plataforma->colorUno; ?>; color:#FFF;" class="css_nombre">Estudiante</th>
										<?php
										//SACAMOS EL NUMERO DE CARGAS O MATERIAS QUE TIENE UN CURSO PARA QUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
										$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_REQUEST["curso"], $_REQUEST["grupo"]);
										$numCargasPorCurso = mysqli_num_rows($cargas);
										while ($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
										?>
											<th style="font-weight:bold;background:<?= $Plataforma->colorUno; ?>; color:#FFF;" colspan="3" width="5%"><?= $carga['mat_nombre']; ?></th>
										<?php
										}
										?>
										<th rowspan="2" style="font-weight:bold;background:<?= $Plataforma->colorUno; ?>; color:#FFF;text-align:center;" >PROM</th>
									</tr>

									<tr>
										<?php
										$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_REQUEST["curso"], $_REQUEST["grupo"]);
										while ($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
										?>
											<th style="text-align:center;background-color: #f2f2f2;">DEF</th>
											<th style="text-align:center;background-color: #f2f2f2;">Acta</th>
											<th style="text-align:center;background-color: #f2f2f2;">Fecha</th>
										<?php
										}
										?>
									</tr>

								</thead>
								<tbody>
									<?php
									$filtroAdicional = "AND mat_grado='" . $_REQUEST['curso'] . "' AND mat_grupo='" . $_REQUEST['grupo'] . "' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
									$consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$curso,$_REQUEST["grupo"]);
									while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
										$nombre = Estudiantes::NombreCompletoDelEstudiante($resultado);
										$defPorEstudiante = 0;
									?>
										<tr id="data1" class="odd gradeX">
											<td style="font-size:9px;"><?= $resultado['mat_documento']; ?></td>
											<td style="font-size:9px;"><?= $nombre ?></td>
											<?php
											$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_REQUEST["curso"], $_REQUEST["grupo"]);
											while ($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
												$p = 1;
												$defPorMateria = 0;
												//PERIODOS DE CADA MATERIA
												while ($p <= $config[19]) {
													$boletin = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $resultado['mat_id'], $carga['car_id']);
													if (!empty($boletin['bol_nota'])) {
														if ($boletin['bol_nota'] < $config[5]) $color = $config[6];
														elseif ($boletin['bol_nota'] >= $config[5]) $color = $config[7];
														$defPorMateria += $boletin['bol_nota'];
													}
													$p++;
												}
												$defPorMateria = round($defPorMateria / $config[19], 2);
												//CONSULTAR NIVELACIONES
												$consultaNiv = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $resultado['mat_id'], $carga['car_id']);
												$cNiv = mysqli_fetch_array($consultaNiv, MYSQLI_BOTH);
												if (!empty($cNiv['niv_definitiva']) && $cNiv['niv_definitiva'] > $defPorMateria) {
													$defPorMateria = $cNiv['niv_definitiva'];
													$msj = 'Nivelación';
												} else {
													$defPorMateria = $defPorMateria;
													$msj = '';
												}
												//DEFINITIVA DE CADA MATERIA
												if ($defPorMateria < $config[5] and $defPorMateria != "") $color = $config[6];
												elseif ($defPorMateria >= $config[5]) $color = $config[7];
											?>
												<td style="text-align:center; background:#FFC;"><input style="text-align:center; width:40px; font-weight:bold; color:<?= $color; ?>" value="<?= $defPorMateria; ?>" id="<?= $resultado['mat_id']; ?>" name="<?= $carga['car_id']; ?>" alt="1" onChange="niv(this)" <?= $disabledPermiso; ?>><br>
													<?php if (!empty($cNiv['niv_id'])) { ?>
														<span style="font-size:10px; color:rgb(255,0,0);"><?= $msj; ?></span><br>
														<a href="javascript:void(0);" 
														onClick="sweetConfirmacion('Alerta!','Desea eliminar este registro?','question','estudiantes-nivelaciones-eliminar.php?idNiv=<?= $cNiv['niv_id']; ?>&curso=<?= $_REQUEST["curso"]; ?>&grupo=<?= $_REQUEST["grupo"]; ?>')"
														><img src="../files/iconos/1363803022_001_052.png"></a>
													<?php } ?>
												</td>
												<td style="text-align:center;"><input style="text-align:center; width:40px;" value="<?php if (!empty($cNiv['niv_acta'])) echo $cNiv['niv_acta']; ?>" id="<?= $resultado['mat_id']; ?>" name="<?= $carga['car_id']; ?>" alt="2" onChange="niv(this)" <?= $disabledPermiso; ?>></td>
												<td style="text-align:center;"><input type="date" style="text-align:center; width:150px;" value="<?php if (!empty($cNiv['niv_fecha_nivelacion'])) echo $cNiv['niv_fecha_nivelacion']; ?>" id="<?= $resultado['mat_id']; ?>" name="<?= $carga['car_id']; ?>" alt="3" onChange="niv(this)" <?= $disabledPermiso; ?>></td>
											<?php
												//DEFINITIVA POR CADA ESTUDIANTE DE TODAS LAS MATERIAS Y PERIODOS
												$defPorEstudiante += $defPorMateria;
											}
											$defPorEstudiante = round($defPorEstudiante / $numCargasPorCurso, 2);
											if ($defPorEstudiante < $config[5] and $defPorEstudiante != "") $color = $config[6];
											elseif ($defPorEstudiante >= $config[5]) $color = $config[7];
											?>
											<td style="text-align:center; width:40px; font-weight:bold; color:<?= $color; ?>"><?= $defPorEstudiante; ?></td>
										</tr>
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

</body>

</html>