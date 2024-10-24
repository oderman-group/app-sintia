<?php
include("session.php");
$idPaginaInterna = 'DT0343';
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH . "/main-app/compartido/head.php");
require_once(ROOT_PATH . "/main-app/class/Disciplina.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");
require_once(ROOT_PATH . "/main-app/class/Grupos.php");
require_once(ROOT_PATH . "/main-app/class/RedisInstance.php");


Utilidades::validarParametros($_GET);

if (!Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
?>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); //6 consultas para optmizar: Enuar 
?>
<div class="page-wrapper">
	<?php include("../compartido/encabezado.php"); //1 por otimizar, parece estar repetida 
	?>

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
							<div class="page-title"><?= $frases[234][$datosUsuarioActual['uss_idioma']]; ?></div>
							<?php include("../compartido/texto-manual-ayuda.php"); //1 por otimizar, parece estar repetida 
							?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="row">

							<div class="col-md-12">
								<?php include("../../config-general/mensajes-informativos.php"); ?>
								<span id="respuestaCambiarEstado"></span>
								<?php
								$filtro = "";
								include("includes/barra-superior-comportamiento.php");
								?>
								<div class="card card-topline-purple" name="elementoGlobalBloquear">
									<div class="card-head">
										<header><?= $frases[234][$datosUsuarioActual['uss_idioma']]; ?></header>
										<div class="tools">
											<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
											<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
											<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
										</div>
									</div>
									<div class="card-body">

										<div class="row" style="margin-bottom: 10px;">
											<table id="example1" class="display" style="width:100%;">
												<div id="gifCarga" class="gif-carga">
													<img alt="Cargando...">
												</div>
												<thead>
													<tr>
														<th>ID</th>
														<th><?= $frases[61][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th><?= $frases[50][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th><?= $frases[108][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th><?= $frases[27][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th><?= $frases[383][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></th>
													</tr>
												</thead>
												<tbody id="comportamiento_result">
													<?php
													$selectSql = [
														"dn.id_nuevo",
														"dn.dn_id",
														"dn.dn_observacion",
														"dn.dn_nota",
														"dn.dn_fecha",
														"dn.dn_periodo",
														"mat.mat_foto",
														"mat.mat_nombres",
														"mat.mat_nombre2",
														"mat.mat_primer_apellido",
														"mat.mat_segundo_apellido",
														"gra_nombre",
														"gru_nombre",
														"mate.mat_nombre",
														"uss.uss_id",
														"uss.uss_nombre",
														"uss.uss_nombre2",
														"uss.uss_apellido1",
														"uss.uss_apellido2"
													];
													$consulta = Disciplina::listarComportamiento($filtro, "", null, $selectSql);
													$contReg = 1;
													$index = 0;
													$arraysDatos = array();
													if (!empty($consulta instanceof PDOStatement)) {
														while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
															$arraysDatos[$index] = $fila;
															$index++;
														}
														$consulta->closeCursor();
													}
													$lista = $arraysDatos;
													$data["data"] = $lista;
													include(ROOT_PATH . "/main-app/class/componentes/result/comportamiento-tbody.php");
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
		<?php // include("../compartido/panel-configuracion.php");
		?>
	</div>
	<!-- end page container -->
	<?php include("../compartido/footer.php"); ?>
	<script src="../js/Comportamiento.js"></script>
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- data tables -->
<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
<script src="../../config-general/assets/js/pages/table/table_data.js"></script>
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
<script>
	$(function() {
		$('[data-toggle="popover"]').popover();
	});

	$('.popover-dismiss').popover({
		trigger: 'focus'
	});
</script>
</body>

</html>