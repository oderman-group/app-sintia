<?php
include("session.php");

// Página accesible desde Informes
$idPaginaInterna = 'DT0099';
if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
?>
	<!-- select2 -->
	<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
	<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>

<?php include("../compartido/body.php");?>
<div class="page-wrapper">
	<?php include("../compartido/encabezado.php");?>
	<?php include("../compartido/panel-color.php");?>

	<div class="page-container">
		<?php include("../compartido/menu.php");?>

		<div class="page-content-wrapper">
			<div class="page-content">
				<div class="page-bar">
					<div class="page-title-breadcrumb">
						<div class="pull-left">
							<div class="page-title"><i class="fa fa-file-signature"></i> Constancia de estudio</div>
							<?php include("../compartido/texto-manual-ayuda.php");?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="card card-box">
							<div class="card-head">
								<header>Seleccione los filtros</header>
							</div>
							<div class="card-body">
								<div class="alert alert-info">
									<i class="fa fa-info-circle"></i>
									Seleccione <b>año</b>, luego <b>curso</b> y finalmente el <b>estudiante</b> para generar la constancia.
								</div>

								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label><b>Año</b></label>
											<select class="form-control" id="ce_year">
												<option value="">Seleccione...</option>
												<?php
												$yearsRange = $_SESSION["datosUnicosInstitucion"]["ins_years"] ?? '';
												$parts = array_map('trim', explode(',', (string)$yearsRange));
												$yearStart = (int)($parts[0] ?? 0);
												$yearEnd = (int)($parts[1] ?? 0);
												if ($yearStart > 0 && $yearEnd > 0 && $yearEnd >= $yearStart) {
													for ($y = $yearEnd; $y >= $yearStart; $y--) {
														$selected = ((int)($_SESSION["bd"] ?? 0) === $y) ? 'selected' : '';
														echo "<option value=\"{$y}\" {$selected}>{$y}</option>";
													}
												}
												?>
											</select>
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label><b>Curso</b></label>
											<select class="form-control" id="ce_curso" disabled>
												<option value="">Primero seleccione un año</option>
											</select>
											<small id="ce_loading_cursos" style="display:none;">Cargando cursos...</small>
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label><b>Estudiante</b></label>
											<select class="form-control" id="ce_estudiante" disabled>
												<option value="">Primero seleccione un curso</option>
											</select>
											<small id="ce_loading_estudiantes" style="display:none;">Cargando estudiantes...</small>
										</div>
									</div>
								</div>

								<div class="row" style="margin-top: 10px;">
									<div class="col-md-12">
										<div id="ce_error" class="alert alert-danger" style="display:none;"></div>
										<button class="btn btn-primary" id="ce_generar" disabled>
											<i class="fa fa-print"></i> Generar constancia
										</button>
										<a href="informes-todos.php" class="btn btn-default" style="margin-left: 6px;">
											<i class="fa fa-arrow-left"></i> Volver a informes
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<?php include("../compartido/footer.php");?>
</div>

<!-- JS -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>

<script>
	$(function(){
		var $year = $('#ce_year');
		var $curso = $('#ce_curso');
		var $est = $('#ce_estudiante');
		var $btn = $('#ce_generar');
		var $err = $('#ce_error');

		function showError(msg){
			$err.text(msg).show();
		}
		function clearError(){
			$err.hide().text('');
		}
		function resetCursos(){
			$curso.empty().append('<option value="">Primero seleccione un año</option>').prop('disabled', true).val('');
			if ($curso.data('select2')) { try { $curso.select2('destroy'); } catch(e) {} }
		}
		function resetEstudiantes(){
			$est.empty().append('<option value="">Primero seleccione un curso</option>').prop('disabled', true).val('');
			if ($est.data('select2')) { try { $est.select2('destroy'); } catch(e) {} }
		}
		function updateBtn(){
			var ok = !!$year.val() && !!$curso.val() && !!$est.val();
			$btn.prop('disabled', !ok);
		}

		$year.select2({ width: '100%', placeholder: 'Seleccione un año' });

		$year.on('change', function(){
			clearError();
			resetCursos();
			resetEstudiantes();
			updateBtn();

			var yearVal = $year.val();
			if(!yearVal) return;

			$('#ce_loading_cursos').show();
			$.ajax({
				url: 'ajax-cargar-cursos-por-year.php',
				type: 'POST',
				dataType: 'json',
				data: { year: yearVal },
				success: function(resp){
					$('#ce_loading_cursos').hide();
					$curso.empty();

					if(resp && resp.success && resp.data && resp.data.length){
						$curso.append('<option value="">Seleccione un curso</option>');
						resp.data.forEach(function(c){
							$curso.append($('<option></option>').attr('value', c.id).text(c.texto_completo));
						});
						$curso.prop('disabled', false);
						$curso.select2({ width:'100%', placeholder: 'Seleccione un curso' });
					}else{
						$curso.append('<option value="">No hay cursos para el año seleccionado</option>');
						$curso.prop('disabled', true);
					}
				},
				error: function(){
					$('#ce_loading_cursos').hide();
					showError('Error al cargar cursos. Intenta nuevamente.');
				}
			});
		});

		$curso.on('change', function(){
			clearError();
			resetEstudiantes();
			updateBtn();

			var yearVal = $year.val();
			var cursoVal = $curso.val();
			if(!yearVal || !cursoVal) return;

			$('#ce_loading_estudiantes').show();
			$.ajax({
				url: 'ajax-cargar-estudiantes-por-year-grado.php',
				type: 'POST',
				dataType: 'json',
				data: { year: yearVal, grado: cursoVal },
				success: function(resp){
					$('#ce_loading_estudiantes').hide();
					$est.empty();

					if(resp && resp.success && resp.data && resp.data.length){
						$est.append('<option value="">Seleccione un estudiante</option>');
						resp.data.forEach(function(e){
							$est.append($('<option></option>').attr('value', e.id).text(e.texto_completo));
						});
						$est.prop('disabled', false);
						$est.select2({ width:'100%', placeholder: 'Seleccione un estudiante' });
					}else{
						$est.append('<option value="">No hay estudiantes para el curso seleccionado</option>');
						$est.prop('disabled', true);
					}
				},
				error: function(){
					$('#ce_loading_estudiantes').hide();
					showError('Error al cargar estudiantes. Intenta nuevamente.');
				}
			});
		});

		$est.on('change', updateBtn);

		$btn.on('click', function(){
			clearError();
			if(!$year.val() || !$curso.val() || !$est.val()){
				showError('Debes seleccionar año, curso y estudiante.');
				return;
			}

			var url = 'constancia-estudio-imprimir.php'
				+ '?year=' + encodeURIComponent($year.val())
				+ '&curso=' + encodeURIComponent($curso.val())
				+ '&id=' + encodeURIComponent($est.val());

			window.open(url, '_blank');
		});

		// Auto-cargar cursos si el año está preseleccionado
		if ($year.val()) {
			$year.trigger('change');
		}
	});
</script>

</body>
</html>

