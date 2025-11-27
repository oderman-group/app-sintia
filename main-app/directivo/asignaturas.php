<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0020';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
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
                                <div class="page-title"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">
                                <?php include("../../config-general/mensajes-informativos.php"); ?>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[73][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="d-flex justify-content-between align-items-center flex-wrap">
														<!-- Botón de agregar -->
														<div class="mb-2">
															<div class="btn-group">
																<?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0022'])) { ?>
																<a href="javascript:void(0);" data-toggle="modal" data-target="#nuevaAsigModal" class="btn deepPink-bgcolor">
																   <?=$frases[231][$datosUsuarioActual['uss_idioma']];?> <i class="fa fa-plus"></i>
																</a>
																<?php } ?>
																
																<?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0180'])) { ?>
																<button type="button" class="btn btn-info" id="btnAgregarAsignaturasMasivo">
																	<i class="fa fa-book"></i> Agregar Masivo
																</button>
																<?php } ?>
															</div>
															
															<?php
															// Incluir modal después de los botones para no afectar el renderizado
															if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0022'])) {
																$idModal = "nuevaAsigModal";
																$contenido = "../directivo/asignaturas-agregar-modal.php";
																include("../compartido/contenido-modal.php");
															}
															?>
														</div>
														
														<!-- Buscador -->
														<div class="mb-2" style="min-width: 300px;">
															<div class="input-group">
																<input type="text" id="buscar_asignatura" class="form-control" placeholder="<?=__('asignaturas.buscar_placeholder');?>">
																<div class="input-group-append">
																	<button class="btn btn-primary" type="button" id="btnBuscarAsignatura">
																		<i class="fa fa-search"></i>
																	</button>
																	<button class="btn btn-secondary" type="button" id="btnLimpiarBusqueda" title="<?=__('general.limpiar_busqueda');?>">
																		<i class="fa fa-eraser"></i>
																	</button>
																</div>
															</div>
															<small class="form-text text-muted"><?=__('asignaturas.buscar_ayuda');?></small>
														</div>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[73][$datosUsuarioActual['uss_idioma']];?></th>
														<?php if($config['conf_agregar_porcentaje_asignaturas']=='SI'){ ?>
															<th>Valor(%)</th>
														<?php }?>	
														<th><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Influye en Promedio</th>
														<th>Cargas</th>
														<?php if(Modulos::validarPermisoEdicion()){?>
															<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
														<?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php include(ROOT_PATH . "/main-app/class/componentes/result/asignaturas-tbody.php"); ?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								<div class="col-md-4 col-lg-3">
									<?php include("../compartido/publicidad-lateral.php");?>
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
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- select2 -->
	<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
	<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
	<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- end js include path -->

<!-- Modal para edición rápida de asignatura -->
<div class="modal fade" id="modalEditarAsignatura" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-edit"></i> Edición Rápida de Asignatura</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="formEditarAsignatura" action="asignaturas-actualizar.php" method="post">
				<div class="modal-body">
					<div id="asignaturaLoader" class="text-center" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-3x"></i>
						<p>Cargando datos...</p>
					</div>
					
					<div id="asignaturaFormulario" style="display:none;">
						<input type="hidden" id="edit_idM" name="idM">
						
						<div class="form-group">
							<label>Código</label>
							<input type="text" class="form-control" id="edit_codigoM" name="codigoM">
						</div>
						
						<div class="form-group">
							<label>Nombre de la Asignatura <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="edit_nombreM" name="nombreM" required>
						</div>
						
						<div class="form-group">
							<label>Siglas</label>
							<input type="text" class="form-control" id="edit_siglasM" name="siglasM">
						</div>
						
						<div class="form-group">
							<label>Área Académica <span class="text-danger">*</span></label>
							<select class="form-control" id="edit_areaM" name="areaM" required>
								<option value="">Seleccione...</option>
							</select>
						</div>
						
						<div class="form-group">
							<label>Sumar en promedio general? <span class="text-danger">*</span></label>
							<select class="form-control" id="edit_sumarPromedio" name="sumarPromedio" required>
								<option value="">Seleccione...</option>
								<option value="SI">SI</option>
								<option value="NO">NO</option>
							</select>
						</div>
						
						<?php if($config['conf_agregar_porcentaje_asignaturas']=='SI'){ ?>
						<div class="form-group">
							<label>Porcentaje (%)</label>
							<input type="number" class="form-control" id="edit_porcenAsigna" name="porcenAsigna" min="0" max="100">
						</div>
						<?php } ?>
					</div>
					
					<div id="asignaturaError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i> <span id="errorMensajeAsignatura"></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-save"></i> Guardar Cambios
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal de Agregar Asignaturas Masivo -->
<div class="modal fade" id="modalAgregarAsignaturasMasivo" tabindex="-1" role="dialog" aria-labelledby="modalAgregarAsignaturasMasivoLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info text-white">
				<h5 class="modal-title" id="modalAgregarAsignaturasMasivoLabel">
					<i class="fa fa-book"></i> Agregar Asignaturas Rápidamente
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formAgregarAsignaturasMasivo">
				<div class="modal-body">
					<div class="alert alert-info">
						<i class="fa fa-info-circle"></i> 
						<strong>Instrucciones:</strong> Completa el nombre de cada asignatura y selecciona si influye en el promedio. 
						Puedes agregar más filas usando el botón <i class="fa fa-plus-circle"></i>.
					</div>
					
					<!-- Contenedor de filas de asignaturas -->
					<div id="contenedorAsignaturas">
						<!-- Primera fila (template) -->
						<div class="row asignatura-row mb-3" data-index="0">
							<div class="col-md-5">
								<label>Nombre de la Asignatura <span class="text-danger">*</span></label>
								<input type="text" class="form-control asignatura-nombre" name="asignaturas[0][nombre]" placeholder="Ej: Matemáticas" required>
							</div>
							<div class="col-md-3">
								<label>Siglas <span class="text-muted">(opcional)</span></label>
								<input type="text" class="form-control asignatura-siglas" name="asignaturas[0][siglas]" placeholder="Ej: MAT" maxlength="5">
							</div>
							<div class="col-md-3">
								<label>Influye en Promedio</label>
								<select class="form-control asignatura-promedio" name="asignaturas[0][promedio]">
									<option value="<?=SI?>" selected>Sí</option>
									<option value="<?=NO?>">No</option>
								</select>
							</div>
							<div class="col-md-1 d-flex align-items-end">
								<button type="button" class="btn btn-danger btn-sm btn-eliminar-fila" title="Eliminar fila" style="margin-bottom: 0;">
									<i class="fa fa-trash"></i>
								</button>
							</div>
						</div>
					</div>
					
					<!-- Botón para agregar más filas -->
					<div class="row">
						<div class="col-md-12">
							<button type="button" class="btn btn-success btn-sm" id="btnAgregarFilaAsignatura">
								<i class="fa fa-plus-circle"></i> Agregar otra asignatura
							</button>
						</div>
					</div>
					
					<hr>
					
					<!-- Selector de área (opcional) -->
					<div class="row">
						<div class="col-md-12">
							<div class="alert alert-warning">
								<i class="fa fa-info-circle"></i> 
								<strong>Área Académica (Opcional):</strong> Puedes asignar un área a todas las asignaturas o dejarlas sin área para agruparlas después.
							</div>
							<div class="form-group">
								<label>Área Académica <span class="text-muted">(Opcional)</span></label>
								<select class="form-control select2-modal-asignaturas" id="areaAsignaturas" name="area">
									<option value="">Sin área (asignar después)</option>
									<?php
									require_once(ROOT_PATH."/main-app/class/Areas.php");
									$cAreas = Areas::traerAreasInstitucion($config);
									while ($rA = mysqli_fetch_array($cAreas, MYSQLI_BOTH)) {
										echo '<option value="' . $rA["ar_id"] . '">' . $rA["ar_nombre"] . '</option>';
									}
									?>
								</select>
								<small class="form-text text-muted">
									<i class="fa fa-info-circle"></i> Si no ves áreas disponibles, primero debes <a href="areas.php" target="_blank">crear al menos un área</a>.
								</small>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-info" id="btnGuardarAsignaturasMasivo">
						<i class="fa fa-save"></i> Guardar Todas las Asignaturas
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<style>
	/* Estilos para el modal de asignaturas */
	.asignatura-row {
		padding: 15px;
		background: #f8f9fa;
		border-radius: 8px;
		border: 1px solid #dee2e6;
		margin-bottom: 15px;
	}
	
	.asignatura-row:hover {
		background: #e9ecef;
		border-color: #adb5bd;
	}
	
	.btn-eliminar-fila {
		height: 38px;
		width: 38px;
		padding: 0;
	}
	
	#modalAgregarAsignaturasMasivo .modal-body {
		max-height: 600px;
		overflow-y: auto;
	}
	
	#contenedorAsignaturas {
		max-height: 400px;
		overflow-y: auto;
		margin-bottom: 15px;
	}
</style>

<script>
$(document).ready(function() {
	// ========================================
	// SISTEMA DE AGREGAR ASIGNATURAS MASIVO
	// ========================================
	
	var contadorFilasAsignatura = 1;
	
	// Abrir modal de agregar asignaturas
	$('#btnAgregarAsignaturasMasivo').on('click', function() {
		$('#modalAgregarAsignaturasMasivo').modal('show');
	});
	
	// Inicializar Select2 cuando se abre el modal
	$('#modalAgregarAsignaturasMasivo').on('shown.bs.modal', function () {
		$('.select2-modal-asignaturas').select2({
			width: '100%',
			placeholder: 'Seleccione un área (opcional)',
			allowClear: true,
			dropdownParent: $('#modalAgregarAsignaturasMasivo'),
			language: {
				noResults: function() {
					return "No se encontraron resultados";
				}
			}
		});
	});
	
	// Generar siglas automáticamente al escribir el nombre
	$(document).on('input', '.asignatura-nombre', function() {
		var nombre = $(this).val();
		var siglas = nombre.substring(0, 3).toUpperCase();
		var row = $(this).closest('.asignatura-row');
		row.find('.asignatura-siglas').val(siglas);
	});
	
	// Agregar nueva fila de asignatura
	$('#btnAgregarFilaAsignatura').on('click', function() {
		var nuevaFila = `
			<div class="row asignatura-row mb-3" data-index="${contadorFilasAsignatura}">
				<div class="col-md-5">
					<label>Nombre de la Asignatura <span class="text-danger">*</span></label>
					<input type="text" class="form-control asignatura-nombre" name="asignaturas[${contadorFilasAsignatura}][nombre]" placeholder="Ej: Matemáticas" required>
				</div>
				<div class="col-md-3">
					<label>Siglas <span class="text-muted">(opcional)</span></label>
					<input type="text" class="form-control asignatura-siglas" name="asignaturas[${contadorFilasAsignatura}][siglas]" placeholder="Ej: MAT" maxlength="5">
				</div>
				<div class="col-md-3">
					<label>Influye en Promedio</label>
					<select class="form-control asignatura-promedio" name="asignaturas[${contadorFilasAsignatura}][promedio]">
						<option value="<?=SI?>" selected>Sí</option>
						<option value="<?=NO?>">No</option>
					</select>
				</div>
				<div class="col-md-1 d-flex align-items-end">
					<button type="button" class="btn btn-danger btn-sm btn-eliminar-fila" title="Eliminar fila" style="margin-bottom: 0;">
						<i class="fa fa-trash"></i>
					</button>
				</div>
			</div>
		`;
		
		$('#contenedorAsignaturas').append(nuevaFila);
		contadorFilasAsignatura++;
		
		// Scroll suave hacia la nueva fila
		$('#contenedorAsignaturas').animate({
			scrollTop: $('#contenedorAsignaturas')[0].scrollHeight
		}, 300);
	});
	
	// Eliminar fila de asignatura
	$(document).on('click', '.btn-eliminar-fila', function() {
		var totalFilas = $('.asignatura-row').length;
		
		if (totalFilas === 1) {
			$.toast({
				heading: 'Advertencia',
				text: 'Debe haber al menos una asignatura para agregar.',
				showHideTransition: 'slide',
				icon: 'warning',
				position: 'top-right'
			});
			return;
		}
		
		$(this).closest('.asignatura-row').fadeOut(300, function() {
			$(this).remove();
		});
	});
	
	// Enviar formulario de asignaturas masivo
	$('#formAgregarAsignaturasMasivo').on('submit', function(e) {
		e.preventDefault();
		
		// El área ahora es opcional, no necesitamos validarla
		var area = $('#areaAsignaturas').val() || ''; // Puede estar vacía
		
		// Recopilar todas las asignaturas
		var asignaturas = [];
		var esValido = true;
		
		$('.asignatura-row').each(function() {
			var nombre = $(this).find('.asignatura-nombre').val().trim();
			var siglas = $(this).find('.asignatura-siglas').val().trim();
			var promedio = $(this).find('.asignatura-promedio').val();
			
			if (!nombre) {
				esValido = false;
				$(this).find('.asignatura-nombre').addClass('is-invalid');
			} else {
				$(this).find('.asignatura-nombre').removeClass('is-invalid');
				
				// Si no tiene siglas, generar automáticamente
				if (!siglas) {
					siglas = nombre.substring(0, 3).toUpperCase();
				}
				
				asignaturas.push({
					nombre: nombre,
					siglas: siglas,
					promedio: promedio
				});
			}
		});
		
		if (!esValido) {
			$.toast({
				heading: 'Error',
				text: 'Por favor completa el nombre de todas las asignaturas.',
				showHideTransition: 'slide',
				icon: 'error',
				position: 'top-right'
			});
			return;
		}
		
		if (asignaturas.length === 0) {
			$.toast({
				heading: 'Error',
				text: 'Debes agregar al menos una asignatura.',
				showHideTransition: 'slide',
				icon: 'error',
				position: 'top-right'
			});
			return;
		}
		
		// Deshabilitar botón y mostrar loader
		var btnOriginal = $('#btnGuardarAsignaturasMasivo').html();
		$('#btnGuardarAsignaturasMasivo').html('<i class="fa fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);
		
		// Enviar datos por AJAX
		$.ajax({
			url: 'asignaturas-guardar-masivo.php',
			type: 'POST',
			data: {
				asignaturas: asignaturas,
				area: area
			},
			dataType: 'json',
			success: function(response) {
				$('#btnGuardarAsignaturasMasivo').html(btnOriginal).prop('disabled', false);
				
				if (response.success) {
					// Cerrar modal
					$('#modalAgregarAsignaturasMasivo').modal('hide');
					
					// Limpiar formulario
					$('#formAgregarAsignaturasMasivo')[0].reset();
					$('#contenedorAsignaturas').html(`
						<div class="row asignatura-row mb-3" data-index="0">
							<div class="col-md-5">
								<label>Nombre de la Asignatura <span class="text-danger">*</span></label>
								<input type="text" class="form-control asignatura-nombre" name="asignaturas[0][nombre]" placeholder="Ej: Matemáticas" required>
							</div>
							<div class="col-md-3">
								<label>Siglas <span class="text-muted">(opcional)</span></label>
								<input type="text" class="form-control asignatura-siglas" name="asignaturas[0][siglas]" placeholder="Ej: MAT" maxlength="5">
							</div>
							<div class="col-md-3">
								<label>Influye en Promedio</label>
								<select class="form-control asignatura-promedio" name="asignaturas[0][promedio]">
									<option value="<?=SI?>" selected>Sí</option>
									<option value="<?=NO?>">No</option>
								</select>
							</div>
							<div class="col-md-1 d-flex align-items-end">
								<button type="button" class="btn btn-danger btn-sm btn-eliminar-fila" title="Eliminar fila" style="margin-bottom: 0;">
									<i class="fa fa-trash"></i>
								</button>
							</div>
						</div>
					`);
					contadorFilasAsignatura = 1;
					
					// Mostrar mensaje de éxito
					$.toast({
						heading: '¡Éxito!',
						text: response.message,
						showHideTransition: 'slide',
						icon: 'success',
						position: 'top-right',
						hideAfter: 5000
					});
					
					// Recargar la página después de 1 segundo
					setTimeout(function() {
						location.reload();
					}, 1000);
				} else {
					$.toast({
						heading: 'Error',
						text: response.message || 'No se pudieron guardar las asignaturas.',
						showHideTransition: 'slide',
						icon: 'error',
						position: 'top-right',
						hideAfter: 5000
					});
				}
			},
			error: function(xhr, status, error) {
				console.error('Error AJAX:', error);
				console.error('Response:', xhr.responseText);
				
				$('#btnGuardarAsignaturasMasivo').html(btnOriginal).prop('disabled', false);
				
				$.toast({
					heading: 'Error',
					text: 'Error de conexión al servidor.',
					showHideTransition: 'slide',
					icon: 'error',
					position: 'top-right',
					hideAfter: 5000
				});
			}
		});
	});
	
	// Limpiar Select2 al cerrar el modal
	$('#modalAgregarAsignaturasMasivo').on('hidden.bs.modal', function () {
		$('.select2-modal-asignaturas').select2('destroy');
	});
	
	// ========================================
	// FIN SISTEMA AGREGAR ASIGNATURAS MASIVO
	// ========================================
	
	$(document).on('click', '.btn-editar-asignatura-modal', function() {
		var asignaturaId = $(this).data('asignatura-id');
		
		$('#asignaturaLoader').show();
		$('#asignaturaFormulario').hide();
		$('#asignaturaError').hide();
		$('#modalEditarAsignatura').modal('show');
		
		$.ajax({
			url: 'ajax-obtener-datos-asignatura.php',
			type: 'POST',
			data: { asignatura_id: asignaturaId },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var asignatura = response.asignatura;
					
					$('#edit_idM').val(asignatura.mat_id);
					$('#edit_codigoM').val(asignatura.mat_codigo || '');
					$('#edit_nombreM').val(asignatura.mat_nombre);
					$('#edit_siglasM').val(asignatura.mat_siglas || '');
					$('#edit_sumarPromedio').val(asignatura.mat_sumar_promedio || 'SI');
					$('#edit_porcenAsigna').val(asignatura.mat_valor || '');
					
					// Llenar select de áreas
					$('#edit_areaM').empty().append('<option value="">Seleccione...</option>');
					response.areas.forEach(function(area) {
						var selected = (area.id == asignatura.mat_area) ? 'selected' : '';
						$('#edit_areaM').append('<option value="' + area.id + '" ' + selected + '>' + area.nombre + '</option>');
					});
					
					$('#asignaturaLoader').hide();
					$('#asignaturaFormulario').show();
				} else {
					$('#asignaturaLoader').hide();
					$('#errorMensajeAsignatura').text(response.message);
					$('#asignaturaError').show();
				}
			},
			error: function() {
				$('#asignaturaLoader').hide();
				$('#errorMensajeAsignatura').text('Error de conexión');
				$('#asignaturaError').show();
			}
		});
	});
	
	$('#formEditarAsignatura').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			success: function() {
				$.toast({
					heading: 'Éxito',
					text: 'Asignatura actualizada correctamente',
					position: 'top-right',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 2000
				});
				$('#modalEditarAsignatura').modal('hide');
				setTimeout(function() { location.reload(); }, 1000);
			},
			error: function() {
				$.toast({
					heading: 'Error',
					text: 'Error al actualizar',
					position: 'top-right',
					loaderBg: '#bf441d',
					icon: 'error'
				});
			}
		});
	});
	
	// === Buscador de Asignaturas ===
	
	// Función para buscar asignaturas
	function buscarAsignatura() {
		var busqueda = $('#buscar_asignatura').val().toLowerCase().trim();
		
		if (busqueda === '') {
			// Si está vacío, mostrar todas las filas
			$('#example1 tbody tr').show();
			return;
		}
		
		var encontrados = 0;
		
		// Recorrer todas las filas de la tabla
		$('#example1 tbody tr').each(function() {
			var fila = $(this);
			
			// Saltar las filas expandibles (detalles)
			if (fila.hasClass('expandable-row')) {
				return;
			}
			
			// Obtener el texto de todas las columnas visibles
			var textoFila = '';
			fila.find('td').each(function() {
				// Excluir la columna de acciones y el botón de expandir
				if (!$(this).find('.dropdown').length && !$(this).find('.expand-btn').length) {
					textoFila += $(this).text().toLowerCase() + ' ';
				}
			});
			
			// Verificar si el texto de la fila contiene la búsqueda
			if (textoFila.indexOf(busqueda) !== -1) {
				fila.show();
				encontrados++;
			} else {
				fila.hide();
			}
		});
		
		// Mostrar mensaje si no hay resultados
		if (encontrados === 0) {
			$.toast({
				heading: 'Sin resultados',
				text: 'No se encontraron asignaturas con: "' + $('#buscar_asignatura').val() + '"',
				position: 'top-right',
				loaderBg: '#f1c40f',
				icon: 'warning',
				hideAfter: 3000
			});
		} else {
			$.toast({
				heading: 'Búsqueda completada',
				text: 'Se encontraron ' + encontrados + ' asignatura(s)',
				position: 'top-right',
				loaderBg: '#26c281',
				icon: 'success',
				hideAfter: 2000
			});
		}
	}
	
	// Botón de buscar
	$('#btnBuscarAsignatura').on('click', function() {
		buscarAsignatura();
	});
	
	// Enter en el campo de búsqueda
	$('#buscar_asignatura').on('keypress', function(e) {
		if (e.which === 13) { // Enter key
			e.preventDefault();
			buscarAsignatura();
		}
	});
	
	// Botón de limpiar búsqueda
	$('#btnLimpiarBusqueda').on('click', function() {
		$('#buscar_asignatura').val('');
		
		// Mostrar todas las filas principales
		$('#example1 tbody tr').each(function() {
			var fila = $(this);
			
			if (fila.hasClass('expandable-row')) {
				// Ocultar filas expandibles
				fila.hide();
			} else {
				// Mostrar filas principales
				fila.show();
				
				// Resetear el botón de expandir
				var btn = fila.find('.expand-btn');
				var icon = btn.find('i');
				icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
				btn.removeClass('text-primary').addClass('text-secondary');
			}
		});
		
		$.toast({
			heading: 'Búsqueda limpiada',
			text: 'Mostrando todas las asignaturas',
			position: 'top-right',
			loaderBg: '#3498db',
			icon: 'info',
			hideAfter: 2000
		});
	});
});
</script>

</body>

</html>