<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0017';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Areas.php");

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
                                <div class="page-title"><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-8 col-lg-12">
                                <?php include("../../config-general/mensajes-informativos.php"); ?>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></header>
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
                                                        <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0019'])) { ?>
                                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#nuevaAreaModal" class="btn deepPink-bgcolor">
                                                            <?=__('general.agregar_nuevo');?> <i class="fa fa-plus"></i>
                                                        </a>
                                                        <?php } ?>
														
														<?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0179'])) { ?>
														<button type="button" class="btn btn-info" id="btnAgregarAreasMasivo">
															<i class="fa fa-list"></i> Agregar Masivo
														</button>
														<?php } ?>
													</div>
													
													<?php
													// Incluir modal después de los botones para no afectar el renderizado
													if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0019'])) {
														$idModal = "nuevaAreaModal";
														$contenido = "../directivo/areas-agregar-modal.php";
														include("../compartido/contenido-modal.php");
													}
													?>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=__('general.posicion');?></th>
														<th><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=__('general.materias');?></th>
                                                        <?php if(Modulos::validarPermisoEdicion()){?>
														    <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
                                                    $consulta = Areas::traerAreasInstitucion($config);
                                                    $contReg = 1;
                                                    while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                    $numMaterias = Asignaturas::contarAsignaturasArea($conexion, $config, $resultado['ar_id']);
                                                    ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['ar_id'];?></td>
														<td><?=$resultado['ar_posicion'];?></td>
														<td><?=$resultado['ar_nombre'];?></td>
														<?php 
															$materias = $numMaterias[0];

															if (Modulos::validarSubRol(['DT0020'])) {
																$materias = '<a href="asignaturas.php?area='.base64_encode($resultado['ar_id']).'" class="text-dark">'.$numMaterias[0].'</a>';
															}
														?>
														<td><span class="badge badge-warning"><?=$materias;?></span></td>
														
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0018','DT0150'])){?>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0018'])){?>
                                                                            <li><a href="javascript:void(0);" class="btn-editar-area-modal" data-area-id="<?=$resultado['ar_id'];?>"><i class="fa fa-edit"></i> Edición rápida</a></li>
                                                                            <li><a href="areas-editar.php?id=<?=base64_encode($resultado['ar_id']);?>"><i class="fa fa-pencil"></i> <?=$frases[165][$datosUsuarioActual['uss_idioma']];?> completa</a></li>
                                                                        <?php } if($numMaterias[0]==0 && Modulos::validarSubRol(['DT0150'])){?><li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas eliminar esta area?','question','areas-eliminar.php?id=<?=base64_encode($resultado['ar_id']);?>')">Eliminar</a></li><?php }?>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        <?php }?>
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
    <!-- end js include path -->

<!-- Modal de Agregar Áreas Masivo -->
<div class="modal fade" id="modalAgregarAreasMasivo" tabindex="-1" role="dialog" aria-labelledby="modalAgregarAreasMasivoLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info text-white">
				<h5 class="modal-title" id="modalAgregarAreasMasivoLabel">
					<i class="fa fa-list"></i> Agregar Áreas Rápidamente
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formAgregarAreasMasivo">
				<div class="modal-body">
					<div class="alert alert-info">
						<i class="fa fa-info-circle"></i> 
						<strong>Instrucciones:</strong> Completa el nombre de cada área y su posición. 
						Puedes agregar más filas usando el botón <i class="fa fa-plus-circle"></i>.
					</div>
					
					<!-- Contenedor de filas de áreas -->
					<div id="contenedorAreas">
						<!-- Primera fila (template) -->
						<div class="row area-row mb-3" data-index="0">
							<div class="col-md-8">
								<label>Nombre del Área <span class="text-danger">*</span></label>
								<input type="text" class="form-control area-nombre" name="areas[0][nombre]" placeholder="Ej: Ciencias Naturales" required>
							</div>
							<div class="col-md-3">
								<label>Posición <span class="text-danger">*</span></label>
								<input type="number" class="form-control area-posicion" name="areas[0][posicion]" placeholder="1" min="1" value="1" required>
								<small class="text-muted">Orden de visualización</small>
							</div>
							<div class="col-md-1 d-flex align-items-end">
								<button type="button" class="btn btn-danger btn-sm btn-eliminar-fila-area" title="Eliminar fila" style="margin-bottom: 0;">
									<i class="fa fa-trash"></i>
								</button>
							</div>
						</div>
					</div>
					
					<!-- Botón para agregar más filas -->
					<div class="row">
						<div class="col-md-12">
							<button type="button" class="btn btn-success btn-sm" id="btnAgregarFilaArea">
								<i class="fa fa-plus-circle"></i> Agregar otra área
							</button>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-info" id="btnGuardarAreasMasivo">
						<i class="fa fa-save"></i> Guardar Todas las Áreas
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<style>
	/* Estilos para el modal de áreas */
	.area-row {
		padding: 15px;
		background: #f8f9fa;
		border-radius: 8px;
		border: 1px solid #dee2e6;
		margin-bottom: 15px;
	}
	
	.area-row:hover {
		background: #e9ecef;
		border-color: #adb5bd;
	}
	
	.btn-eliminar-fila-area {
		height: 38px;
		width: 38px;
		padding: 0;
	}
	
	#modalAgregarAreasMasivo .modal-body {
		max-height: 600px;
		overflow-y: auto;
	}
	
	#contenedorAreas {
		max-height: 400px;
		overflow-y: auto;
		margin-bottom: 15px;
	}
</style>

<!-- Modal para edición rápida de área -->
<div class="modal fade" id="modalEditarArea" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-edit"></i> Edición Rápida de Área</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="formEditarArea" action="areas-actualizar.php" method="post">
				<div class="modal-body">
					<div id="areaLoader" class="text-center" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-3x"></i>
						<p>Cargando datos...</p>
					</div>
					
					<div id="areaFormulario" style="display:none;">
						<input type="hidden" id="edit_idA" name="idA">
						
						<div class="form-group">
							<label>Nombre del Área <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="edit_nombreA" name="nombreA" required>
						</div>
						
						<div class="form-group">
							<label>Posición</label>
							<input type="number" class="form-control" id="edit_posicion" name="posicionA" min="1">
						</div>
					</div>
					
					<div id="areaError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i> <span id="errorMensajeArea"></span>
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

<script>
$(document).ready(function() {
	// ========================================
	// SISTEMA DE AGREGAR ÁREAS MASIVO
	// ========================================
	
	var contadorFilasArea = 1;
	
	// Abrir modal de agregar áreas
	$('#btnAgregarAreasMasivo').on('click', function() {
		$('#modalAgregarAreasMasivo').modal('show');
	});
	
	// Agregar nueva fila de área
	$('#btnAgregarFilaArea').on('click', function() {
		// Calcular automáticamente la próxima posición
		var ultimaPosicion = 0;
		$('.area-posicion').each(function() {
			var valor = parseInt($(this).val()) || 0;
			if (valor > ultimaPosicion) {
				ultimaPosicion = valor;
			}
		});
		var proximaPosicion = ultimaPosicion + 1;
		
		var nuevaFila = `
			<div class="row area-row mb-3" data-index="${contadorFilasArea}">
				<div class="col-md-8">
					<label>Nombre del Área <span class="text-danger">*</span></label>
					<input type="text" class="form-control area-nombre" name="areas[${contadorFilasArea}][nombre]" placeholder="Ej: Ciencias Naturales" required>
				</div>
				<div class="col-md-3">
					<label>Posición <span class="text-danger">*</span></label>
					<input type="number" class="form-control area-posicion" name="areas[${contadorFilasArea}][posicion]" placeholder="${proximaPosicion}" min="1" value="${proximaPosicion}" required>
					<small class="text-muted">Orden de visualización</small>
				</div>
				<div class="col-md-1 d-flex align-items-end">
					<button type="button" class="btn btn-danger btn-sm btn-eliminar-fila-area" title="Eliminar fila" style="margin-bottom: 0;">
						<i class="fa fa-trash"></i>
					</button>
				</div>
			</div>
		`;
		
		$('#contenedorAreas').append(nuevaFila);
		contadorFilasArea++;
		
		// Scroll suave hacia la nueva fila
		$('#contenedorAreas').animate({
			scrollTop: $('#contenedorAreas')[0].scrollHeight
		}, 300);
	});
	
	// Eliminar fila de área
	$(document).on('click', '.btn-eliminar-fila-area', function() {
		var totalFilas = $('.area-row').length;
		
		if (totalFilas === 1) {
			$.toast({
				heading: 'Advertencia',
				text: 'Debe haber al menos un área para agregar.',
				showHideTransition: 'slide',
				icon: 'warning',
				position: 'top-right'
			});
			return;
		}
		
		$(this).closest('.area-row').fadeOut(300, function() {
			$(this).remove();
		});
	});
	
	// Enviar formulario de áreas masivo
	$('#formAgregarAreasMasivo').on('submit', function(e) {
		e.preventDefault();
		
		// Recopilar todas las áreas
		var areas = [];
		var esValido = true;
		
		$('.area-row').each(function() {
			var nombre = $(this).find('.area-nombre').val().trim();
			var posicion = $(this).find('.area-posicion').val().trim();
			
			if (!nombre) {
				esValido = false;
				$(this).find('.area-nombre').addClass('is-invalid');
			} else {
				$(this).find('.area-nombre').removeClass('is-invalid');
			}
			
			if (!posicion || parseInt(posicion) < 1) {
				esValido = false;
				$(this).find('.area-posicion').addClass('is-invalid');
			} else {
				$(this).find('.area-posicion').removeClass('is-invalid');
				
				areas.push({
					nombre: nombre,
					posicion: parseInt(posicion)
				});
			}
		});
		
		if (!esValido) {
			$.toast({
				heading: 'Error',
				text: 'Por favor completa todos los campos obligatorios.',
				showHideTransition: 'slide',
				icon: 'error',
				position: 'top-right'
			});
			return;
		}
		
		if (areas.length === 0) {
			$.toast({
				heading: 'Error',
				text: 'Debes agregar al menos un área.',
				showHideTransition: 'slide',
				icon: 'error',
				position: 'top-right'
			});
			return;
		}
		
		// Deshabilitar botón y mostrar loader
		var btnOriginal = $('#btnGuardarAreasMasivo').html();
		$('#btnGuardarAreasMasivo').html('<i class="fa fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);
		
		// Enviar datos por AJAX
		$.ajax({
			url: 'areas-guardar-masivo.php',
			type: 'POST',
			data: {
				areas: areas
			},
			dataType: 'json',
			success: function(response) {
				$('#btnGuardarAreasMasivo').html(btnOriginal).prop('disabled', false);
				
				if (response.success) {
					// Cerrar modal
					$('#modalAgregarAreasMasivo').modal('hide');
					
					// Limpiar formulario
					$('#formAgregarAreasMasivo')[0].reset();
					$('#contenedorAreas').html(`
						<div class="row area-row mb-3" data-index="0">
							<div class="col-md-8">
								<label>Nombre del Área <span class="text-danger">*</span></label>
								<input type="text" class="form-control area-nombre" name="areas[0][nombre]" placeholder="Ej: Ciencias Naturales" required>
							</div>
							<div class="col-md-3">
								<label>Posición <span class="text-danger">*</span></label>
								<input type="number" class="form-control area-posicion" name="areas[0][posicion]" placeholder="1" min="1" value="1" required>
								<small class="text-muted">Orden de visualización</small>
							</div>
							<div class="col-md-1 d-flex align-items-end">
								<button type="button" class="btn btn-danger btn-sm btn-eliminar-fila-area" title="Eliminar fila" style="margin-bottom: 0;">
									<i class="fa fa-trash"></i>
								</button>
							</div>
						</div>
					`);
					contadorFilasArea = 1;
					
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
						text: response.message || 'No se pudieron guardar las áreas.',
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
				
				$('#btnGuardarAreasMasivo').html(btnOriginal).prop('disabled', false);
				
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
	
	// ========================================
	// FIN SISTEMA AGREGAR ÁREAS MASIVO
	// ========================================
	
	// Edición rápida de área
	$(document).on('click', '.btn-editar-area-modal', function() {
		var areaId = $(this).data('area-id');
		
		$('#areaLoader').show();
		$('#areaFormulario').hide();
		$('#areaError').hide();
		$('#modalEditarArea').modal('show');
		
		$.ajax({
			url: 'ajax-obtener-datos-area.php',
			type: 'POST',
			data: { area_id: areaId },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var area = response.area;
					$('#edit_idA').val(area.ar_id);
					$('#edit_nombreA').val(area.ar_nombre);
					$('#edit_posicion').val(area.ar_posicion || '');
					
					$('#areaLoader').hide();
					$('#areaFormulario').show();
				} else {
					$('#areaLoader').hide();
					$('#errorMensajeArea').text(response.message);
					$('#areaError').show();
				}
			},
			error: function() {
				$('#areaLoader').hide();
				$('#errorMensajeArea').text('Error de conexión');
				$('#areaError').show();
			}
		});
	});
	
	$('#formEditarArea').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			success: function() {
				$.toast({
					heading: 'Éxito',
					text: 'Área actualizada correctamente',
					position: 'top-right',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 2000
				});
				$('#modalEditarArea').modal('hide');
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
});
</script>

</body>

</html>