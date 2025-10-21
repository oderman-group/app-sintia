<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0032';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$Plataforma = new Plataforma;

Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

require_once("../class/Estudiantes.php");
require_once("../class/Sysjobs.php");
$jQueryTable = '';
if($config['conf_doble_buscador'] == 1) {
	$jQueryTable = 'id="example1"';
}
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
	<div id="overlayInforme">
		<div id="loader"></div>
		<div id="loading-text">Generando informe…</div>
	</div>
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
                                <div class="page-title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								
								
								<?php 
								$filtro = '';
								$curso = '';
								if(!empty($_GET["curso"])){ $curso = base64_decode($_GET['curso']); $filtro .= " AND car_curso='".$curso."'";}
								if(!empty($_GET["grupo"])){$filtro .= " AND car_grupo='".base64_decode($_GET["grupo"])."'";}
								if(!empty($_GET["docente"])){$filtro .= " AND car_docente='".base64_decode($_GET["docente"])."'";}
								if(!empty($_GET["asignatura"])){$filtro .= " AND car_materia='".base64_decode($_GET["asignatura"])."'";}

								//include("includes/cargas-filtros.php");
								?>
								
								<div class="col-md-12">
								<?php
									include("../../config-general/mensajes-informativos.php");
									include("includes/barra-superior-cargas-componente.php");									
								?>

                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></header>
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
														<?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0052'])) { ?>
											                                              <a href="javascript:void(0);" data-toggle="modal" data-target="#nuevaCargModal" class="btn deepPink-bgcolor">
														   <?=$frases[231][$datosUsuarioActual['uss_idioma']];?> <i class="fa fa-plus"></i>
											                                              </a>
											                                              <?php
											                                              $idModal = "nuevaCargModal";
											                                              $contenido = "../directivo/cargas-agregar-modal.php";
											                                              include("../compartido/contenido-modal.php");
											                                              } ?>
											                                              <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0032'])) { ?>
											                                              <button type="button" id="moverCargasBtn" class="btn deepPink-bgcolor" disabled>Mover Cargas Seleccionadas</button>
											                                              <?php } ?>
													</div>
												</div>
											</div>
											
                                        <div>
                                    		<table id="example1" class="display"  style="width:100%;">
												<div id="gifCarga" class="gif-carga">
													<img  alt="Cargando...">
												</div>
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th><input type="checkbox" id="selectAllCargas"></th>
                                                        <th>#</th>
             <th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
             <th>Docente</th>
             <th>Curso</th>
             <th>Asignatura</th>
             <th>I.H</th>
             <th>Periodo Actual</th>
                                            <th style="text-align:center;">NOTAS<br>Declaradas - Registradas</th>
             <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
             </tr>
            </thead>
													<tbody id="cargas_result">
													<?php
													require_once(ROOT_PATH."/main-app/class/CargaAcademicaOptimizada.php");
													
													// Usar paginación por defecto para mejorar rendimiento
													// NOTA: Para mejorar aún más el rendimiento, ejecuta los índices en:
													// documents/database/indices-optimizacion-cargas.sql
													$filtroLimite = 'LIMIT 0, 200';  // Cargar máximo 200 registros inicialmente
													
													$selectSql = ["car.car_id","car.car_periodo","car.car_curso","car.car_ih","car.car_permiso2",
																	"car.car_indicador_automatico","car.car_maximos_indicadores",
																	"car.car_docente","gra.gra_tipo","am.mat_id",
																	"car.car_maximas_calificaciones","car.car_director_grupo","uss.uss_nombre",
																	"uss.uss_id","uss.uss_nombre2","uss.uss_apellido1","uss.uss_apellido2","gra.gra_id","gra.gra_nombre",
																	"gru.gru_nombre","am.mat_nombre","am.mat_valor","car.car_grupo","car.car_director_grupo", "car.car_activa",
																	"car.id_nuevo AS id_nuevo_carga"];
													
													// Usar método optimizado sin subqueries pesadas
													$busqueda = CargaAcademicaOptimizada::listarCargasOptimizado($conexion, $config, "", $filtro, "car.car_id", $filtroLimite,"",array(),$selectSql);
    												$contReg = 1;
													$index = 0;
													$arraysDatos = array();																									
													while ($fila = $busqueda->fetch_assoc()) {
														$arraysDatos[$index] = $fila;
														$index++;
													}
													$lista = $arraysDatos;
													$data["data"] =$lista;
													include("../class/componentes/result/cargas-tbody.php");
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
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <!-- <script src="../../config-general/assets/js/pages/table/table_data.js" ></script> -->
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
	<script>
		$(function () {
			$('[data-toggle="popover"]').popover();
		});

		$('.popover-dismiss').popover({trigger: 'focus'});

		// DataTable initialization with child rows
		$(document).ready(function() {
			if (!$.fn.DataTable.isDataTable('#example1')) {
				var table = $('#example1').DataTable({
					"columnDefs": [
						{
							"targets": 0,
							"orderable": false,
							"searchable": false
						},
						{
							"targets": 1,
							"orderable": false,
							"searchable": false
						}
					],
					"order": [[2, 'asc']],
					"language": {
						"lengthMenu": "Mostrar _MENU_ registros por página",
						"zeroRecords": "No se encontraron resultados",
						"info": "Mostrando página _PAGE_ de _PAGES_",
						"infoEmpty": "No hay registros disponibles",
						"infoFiltered": "(filtrado de _MAX_ registros totales)",
						"search": "Buscar:",
						"paginate": {
							"first": "Primero",
							"last": "Último",
							"next": "Siguiente",
							"previous": "Anterior"
						}
					},
					"initComplete": function(settings, json) {
						// Attach expand button events initially
						var table = this.api();
						var expandedRows = {}; // Track expanded rows by cargaId

						$('#example1 tbody').on('click', '.expand-btn', function () {
							var button = $(this);
							var cargaId = button.data('id');
							var icon = button.find('i');

							// Get data from button attributes
							var codigo = button.data('codigo');
							var docente = button.data('docente');
							var curso = button.data('curso');
							var asignatura = button.data('asignatura');
							var ih = button.data('ih');
							var periodo = button.data('periodo');
							var actividades = button.data('actividades');
							var actividadesRegistradas = button.data('actividades-registradas');
							var directorGrupo = button.data('director-grupo');
							var permiso2 = button.data('permiso2');
							var indicadorAutomatico = button.data('indicador-automatico');
							var maxIndicadores = button.data('max-indicadores');
							var maxCalificaciones = button.data('max-calificaciones');
							var cantidadEstudiantes = button.data('cantidad-estudiantes');
							var activa = button.data('activa');

							// Find the current row in the table
							var tr = $(this).closest('tr');
							var row = table.row(tr);

							// Check if this row is currently expanded
							var isExpanded = expandedRows[cargaId] || false;

							if (isExpanded) {
								// This row is already open - close it
								try {
									if (row && row.child && typeof row.child === 'function') {
										row.child.hide();
									} else {
										// Fallback: remove any child rows manually
										tr.next('tr').remove();
									}
									expandedRows[cargaId] = false;
									icon.removeClass('fa-minus').addClass('fa-plus');
									button.removeClass('btn-warning').addClass('btn-info');
								} catch (error) {
									console.error('Error hiding child row:', error);
									// Fallback cleanup
									tr.next('tr').remove();
									expandedRows[cargaId] = false;
									icon.removeClass('fa-minus').addClass('fa-plus');
									button.removeClass('btn-warning').addClass('btn-info');
								}
							} else {
								// Open this row
								try {
									if (row && row.child && typeof row.child === 'function') {
										row.child(formatDetailsCargas(codigo, docente, curso, asignatura, ih, periodo, actividades, actividadesRegistradas, directorGrupo, permiso2, indicadorAutomatico, maxIndicadores, maxCalificaciones, cantidadEstudiantes, activa, cargaId)).show();
									} else {
										// Fallback: insert child row manually
										$(formatDetailsCargas(codigo, docente, curso, asignatura, ih, periodo, actividades, actividadesRegistradas, directorGrupo, permiso2, indicadorAutomatico, maxIndicadores, maxCalificaciones, cantidadEstudiantes, activa, cargaId)).insertAfter(tr);
									}
									expandedRows[cargaId] = true;
									icon.removeClass('fa-plus').addClass('fa-minus');
									button.removeClass('btn-info').addClass('btn-warning');
								} catch (error) {
									console.error('Error showing child row:', error);
									// Fallback insertion
									$(formatDetailsCargas(codigo, docente, curso, asignatura, ih, periodo, actividades, actividadesRegistradas, directorGrupo, permiso2, indicadorAutomatico, maxIndicadores, maxCalificaciones, cantidadEstudiantes, activa, cargaId)).insertAfter(tr);
									expandedRows[cargaId] = true;
									icon.removeClass('fa-plus').addClass('fa-minus');
									button.removeClass('btn-info').addClass('btn-warning');
								}
							}
						});
					}
				});
			} else {
				var table = $('#example1').DataTable();
			}

		});

		function formatDetailsCargas(codigo, docente, curso, asignatura, ih, periodo, actividades, actividadesRegistradas, directorGrupo, permiso2, indicadorAutomatico, maxIndicadores, maxCalificaciones, cantidadEstudiantes, activa, cargaId) {
			var activaBadgeClass = activa == 1 ? 'success' : 'warning';
			var activaText = activa == 1 ? 'Activa' : 'Inactiva';

			var html = '<tr class="expandable-row">' +
				'<td colspan="11" class="expandable-content bg-light border">' +
					'<div class="row no-gutters">' +
						'<div class="col-md-12 p-3">' +
							'<div class="row">' +
								'<div class="col-md-6">' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Código:</strong>' +
										'<span class="badge badge-secondary ml-2">' + codigo + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Docente:</strong>' +
										'<span class="text-dark">' + docente + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Curso:</strong>' +
										'<span class="text-dark">' + curso + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Asignatura:</strong>' +
										'<span class="text-dark">' + asignatura + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">I.H:</strong>' +
										'<span class="text-dark">' + ih + '</span>' +
									'</div>' +
								'</div>' +
								'<div class="col-md-6">' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Periodo Actual:</strong>' +
										'<span class="badge badge-info">' + periodo + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Actividades Declaradas:</strong>' +
										'<span class="text-dark">' + actividades + '%</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Actividades Registradas:</strong>' +
										'<span class="text-dark">' + actividadesRegistradas + '%</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Director de Grupo:</strong>' +
										'<span class="badge badge-' + (directorGrupo === 'Si' ? 'success' : 'secondary') + '">' + directorGrupo + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Estado:</strong>' +
										'<span class="badge badge-' + activaBadgeClass + '">' + activaText + '</span>' +
									'</div>' +
								'</div>' +
							'</div>' +
							'<div class="row mt-2">' +
								'<div class="col-12">' +
									'<div class="alert alert-info py-2">' +
										'<i class="fa fa-users mr-2"></i>' +
										'<strong>Estudiantes:</strong> ' + cantidadEstudiantes + ' estudiantes matriculados' +
									'</div>' +
								'</div>' +
							'</div>' +
							'<div class="row mt-2">' +
								'<div class="col-md-6">' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Puede editar en otros periodos:</strong>' +
										'<span class="badge badge-' + (permiso2 === 'Si' ? 'success' : 'secondary') + '">' + permiso2 + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Indicadores automáticos:</strong>' +
										'<span class="badge badge-' + (indicadorAutomatico === 'Si' ? 'success' : 'secondary') + '">' + indicadorAutomatico + '</span>' +
									'</div>' +
								'</div>' +
								'<div class="col-md-6">' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Máx. Indicadores:</strong>' +
										'<span class="text-dark">' + maxIndicadores + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Máx. Calificaciones:</strong>' +
										'<span class="text-dark">' + maxCalificaciones + '</span>' +
									'</div>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</td>' +
			'</tr>';

			return html;
		}

		// JavaScript for bulk move cargas
		var selectedCargas = [];

		$('#selectAllCargas').on('change', function() {
			if (this.checked) {
				$('.carga-checkbox').prop('checked', true);
				selectedCargas = $('.carga-checkbox').map(function() { return this.value; }).get();
			} else {
				$('.carga-checkbox').prop('checked', false);
				selectedCargas = [];
			}
			toggleMoverBtn();
		});

		$(document).on('change', '.carga-checkbox', function() {
			if (this.checked) {
				selectedCargas.push(this.value);
			} else {
				selectedCargas = selectedCargas.filter(id => id !== this.value);
			}
			$('#selectAllCargas').prop('checked', $('.carga-checkbox:checked').length === $('.carga-checkbox').length);
			toggleMoverBtn();
		});

		function toggleMoverBtn() {
			$('#moverCargasBtn').prop('disabled', selectedCargas.length === 0);
		}

		$('#moverCargasBtn').on('click', function() {
			loadPeriodos();
		});

		function loadPeriodos() {
			$.ajax({
				url: 'ajax-get-periodos.php',
				type: 'GET',
				success: function(data) {
					$('#nuevoPeriodo').html(data);
					$('#moverCargasModal').modal('show');
					attachConfirmEvent();
				},
				error: function(xhr, status, error) {
					console.error('Error loading periods:', error);
					// Fallback: provide default periods
					var defaultOptions = '';
					for (var i = 1; i <= 4; i++) {
						defaultOptions += '<option value="' + i + '">Período ' + i + '</option>';
					}
					$('#nuevoPeriodo').html(defaultOptions);
					$('#moverCargasModal').modal('show');
					attachConfirmEvent();
				}
			});
		}

		function attachConfirmEvent() {
			$('#confirmarMover').on('click', function() {
				var nuevoPeriodo = $('#nuevoPeriodo').val();
				if (!nuevoPeriodo) {
					$.toast({
						heading: 'Error',
						text: 'Por favor seleccione un período.',
						showHideTransition: 'slide',
						icon: 'error'
					});
					return;
				}
				$.ajax({
					url: 'cargas-mover.php',
					type: 'POST',
					data: { cargas: selectedCargas, periodo: nuevoPeriodo },
					success: function(response) {
						var res;
						if (typeof response === 'object') {
							res = response;
						} else {
							try {
								res = JSON.parse(response);
							} catch (e) {
								$.toast({
									heading: 'Error',
									text: 'Respuesta inválida del servidor.',
									showHideTransition: 'slide',
									icon: 'error'
								});
								return;
							}
						}
						if (res.success) {
							$('#moverCargasModal').modal('hide');
							$.toast({
								heading: 'Éxito',
								text: 'Las cargas académicas han sido movidas exitosamente.',
								showHideTransition: 'slide',
								icon: 'success'
							});
							setTimeout(function() {
								location.reload();
							}, 2000);
						} else {
							$.toast({
								heading: 'Error',
								text: 'Hubo un error al mover las cargas.',
								showHideTransition: 'slide',
								icon: 'error'
							});
						}
					},
					error: function(xhr, status, error) {
						console.error('Error moving cargas:', error);
						$.toast({
							heading: 'Error',
							text: 'Error de conexión al servidor.',
							showHideTransition: 'slide',
							icon: 'error'
						});
					}
				});
			});
		}

		$('#confirmarMover').on('click', function() {
			alert('Button clicked, selectedCargas: ' + selectedCargas.length + ', periodo: ' + $('#nuevoPeriodo').val());
			var nuevoPeriodo = $('#nuevoPeriodo').val();
			if (!nuevoPeriodo) {
				alert('Seleccione un período.');
				return;
			}
			alert('Sending AJAX');
			$.ajax({
				url: 'cargas-mover.php',
				type: 'POST',
				data: { cargas: selectedCargas, periodo: nuevoPeriodo },
				success: function(response) {
					alert('Response: ' + response);
					try {
						var res = JSON.parse(response);
						if (res.success) {
							$('#moverCargasModal').modal('hide');
							alert('Success');
							location.reload();
						} else {
							alert('Error: ' + res.error);
						}
					} catch (e) {
						alert('Invalid JSON: ' + response);
					}
				},
				error: function(xhr, status, error) {
					alert('AJAX Error: ' + error);
				}
			});
		});
	</script>
	<style>
	    .sorting_1 {
			background-color: red !important;
	    }

		.expandable-content {
			padding: 15px;
			margin: 10px 0;
			border-radius: 5px;
			width: 100%;
			box-sizing: border-box;
		}

		.expandable-row {
			background-color: #f8f9fa !important;
		}

		.expandable-row .expandable-content {
			border-left: 3px solid #007bff;
			border-radius: 0;
		}

		.info-item {
			display: flex;
			align-items: flex-start;
			margin-bottom: 8px;
		}

		.info-item strong {
			min-width: 180px;
			margin-right: 10px;
			flex-shrink: 0;
		}

		.info-item span {
			flex: 1;
			word-wrap: break-word;
		}

	  </style>

	<!-- Modal for moving cargas -->
	<div class="modal fade" id="moverCargasModal" tabindex="-1" role="dialog" aria-labelledby="moverCargasModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="moverCargasModalLabel">Mover Cargas Seleccionadas</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="moverCargasForm">
						<div class="form-group">
							<label for="nuevoPeriodo">Seleccionar Nuevo Período</label>
							<select class="form-control" id="nuevoPeriodo" name="nuevoPeriodo" required>
								<!-- options will be loaded via JS -->
							</select>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" id="confirmarMover">Mover</button>
				</div>
			</div>
		</div>
	</div>

<script>
$(document).ready(function() {
	// Lazy loading de notas declaradas y registradas
	$(document).on('click', '.btn-cargar-notas', function() {
		var btn = $(this);
		var cargaId = btn.data('carga-id');
		var periodo = btn.data('periodo');
		var tdActividades = $('.td-actividades-' + cargaId);
		
		// Mostrar indicador de carga
		btn.html('<i class="fa fa-spinner fa-spin"></i> Cargando...').prop('disabled', true);
		
		// Hacer petición AJAX
		$.ajax({
			url: 'ajax-obtener-datos-adicionales-carga.php',
			type: 'POST',
			data: {
				carga_id: cargaId,
				periodo: periodo
			},
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var datos = response.datos;
					var actividadesTotales = datos.actividades_totales || 0;
					var actividadesRegistradas = datos.actividades_registradas || 0;
					
					// Actualizar la celda con los datos reales
					var nuevoContenido = actividadesTotales + '%&nbsp;&nbsp;-&nbsp;&nbsp;' + actividadesRegistradas + '%';
					
					<?php if ($permisoReportesNotas) { ?>
					// Si tiene permiso, convertir a enlace
					nuevoContenido = '<a href="../compartido/reporte-notas.php?carga=' + btoa(cargaId) + '&per=' + btoa(periodo) + '" target="_blank" style="text-decoration:underline; color:#00F;" title="Calificaciones">' + nuevoContenido + '</a>';
					<?php } ?>
					
					tdActividades.html(nuevoContenido);
					
					// Mostrar notificación de éxito
					$.toast({
						heading: 'Éxito',
						text: 'Datos cargados correctamente',
						position: 'top-right',
						loaderBg: '#26c281',
						icon: 'success',
						hideAfter: 2000
					});
				} else {
					btn.html('<i class="fa fa-exclamation-triangle"></i> Error').removeClass('btn-info').addClass('btn-danger');
					$.toast({
						heading: 'Error',
						text: response.message || 'Error al cargar datos',
						position: 'top-right',
						loaderBg: '#bf441d',
						icon: 'error',
						hideAfter: 3000
					});
				}
			},
			error: function(xhr, status, error) {
				console.error('Error AJAX:', error);
				btn.html('<i class="fa fa-exclamation-triangle"></i> Error').removeClass('btn-info').addClass('btn-danger');
				$.toast({
					heading: 'Error',
					text: 'Error de conexión al servidor',
					position: 'top-right',
					loaderBg: '#bf441d',
					icon: 'error',
					hideAfter: 3000
				});
			}
		});
	});
});
</script>

</body>

</html>