<?php
if (!empty($data["dataTotal"])) {
	include(ROOT_PATH . "/config-general/config-admisiones.php");
	require_once("../Estudiantes.php");
}
$contReg = 1;
foreach ($data["data"] as $resultado) {
	$observacion = !empty($resultado['asp_observacion']) ? strip_tags($resultado['asp_observacion']) : "";
	$infoTooltipEstudiante = "<b>Nombre acudiente:</b><br>
                          {$resultado['asp_nombre_acudiente']}<br>
                          <b>Celular:</b><br>
                          {$resultado['asp_celular_acudiente']}<br>
                          <b>Documento:</b><br>
                          {$resultado['asp_documento_acudiente']}<br>
                          <b>Email:</b><br>
                          {$resultado['asp_email_acudiente']}<br><br>
                          <b>Observación:</b><br>
						  <span style='color:darkblue; font-size:11px; font-style:italic;'>{$observacion}</span>";
?>
	<tr id="registro_<?= $resultado["asp_id"]; ?>" class="odd gradeX">
		<td><button class="btn btn-sm btn-link text-secondary expand-btn" data-id="<?= $resultado['asp_id']; ?>" title="Ver detalles"><i class="fa fa-chevron-right"></i></button></td>
		<td><?= $contReg; ?></td>
		<td><?= $resultado["mat_id"]; ?></td>
		<td><?= $resultado["asp_id"]; ?></td>
		<td><?= $resultado["asp_fecha"]; ?></td>
		<td><?= $resultado["mat_documento"]; ?></td>
		<td><?= Estudiantes::NombreCompletoDelEstudiante($resultado); ?></td>
		<td><?= $resultado["asp_agno"]; ?></td>
		<td>
			<?php
			// Mapear estados a clases de badge
			$badgeClassesInscripcion = [
				1 => 'badge badge-warning',      // Pendiente
				2 => 'badge badge-info',         // En proceso
				3 => 'badge badge-primary',      // En revisión
				4 => 'badge badge-danger',       // Rechazada
				5 => 'badge badge-secondary',    // Cancelada
				6 => 'badge badge-success',      // Aprobada
				7 => 'badge badge-dark',         // Otro estado
			];
			$badgeClass = $badgeClassesInscripcion[$resultado["asp_estado_solicitud"]] ?? 'badge badge-secondary';
			?>
			<span class="<?= $badgeClass; ?>">
				<?= $estadosSolicitud[$resultado["asp_estado_solicitud"]]; ?>
			</span>
		</td>
		<td><a href="../admisiones/files/comprobantes/<?= $resultado["asp_comprobante"]; ?>" target="_blank" style="text-decoration: underline;"><?= $resultado["asp_comprobante"]; ?></a></td>
		<td><?= !empty($resultado["gra_nombre"]) ? $resultado["gra_nombre"] : '<span class="text-muted">Sin grado</span>'; ?></td>
		<td>
			<?php if (isset($mostrarOcultos) && $mostrarOcultos) { ?>
				<!-- Tab de OCULTOS: Solo botón para desocultar -->
				<button type="button" class="btn btn-success btn-sm" onclick="desocultarInscripcion('<?= $resultado["asp_id"]; ?>')">
					<i class="fa fa-eye"></i> Desocultar
				</button>
			<?php } else { ?>
				<!-- Tab de VISIBLES: Menú completo de acciones -->
				<div class="btn-group">
					<button type="button" class="btn btn-primary"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></button>
					<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
						<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="inscripciones-formulario.php?token=<?= md5($resultado["asp_id"]); ?>&id=<?= base64_encode($resultado["asp_id"]); ?>&idInst=<?= base64_encode($config["conf_id_institucion"]) ?>" target="_blank">Ver información</a></li>
						<li><a href="javascript:void(0);" onclick="abrirModalEdicionAspirante('<?= $resultado["asp_id"]; ?>', '<?= $resultado["mat_id"]; ?>')">Editar rápido</a></li>
						<li><a href="inscripciones-formulario-editar.php?token=<?= md5($resultado["asp_id"]); ?>&id=<?= base64_encode($resultado["asp_id"]); ?>&idInst=<?= base64_encode($config["conf_id_institucion"]) ?>" target="_blank">Editar completo</a></li>

						<?php if ($resultado["asp_estado_solicitud"] == 6) { ?>

							<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Va a eliminar la documentación de este aspirante. Recuerde descargarla primero. Esta acción es irreversible. Desea continuar?','question','inscripciones-eliminar-documentacion.php?matricula=<?= base64_encode($resultado["mat_id"]); ?>')">Borrar documentación</a></li>

							<?php if (!empty($configAdmisiones["cfgi_year_inscripcion"]) && $configAdmisiones["cfgi_year_inscripcion"] == $yearEnd && $configAdmisiones["cfgi_year_inscripcion"] != $agnoBD) { ?>

								<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Va a pasar este estudiante al <?= $configAdmisiones["cfgi_year_inscripcion"]; ?>. Desea continuar?','question','inscripciones-pasar-estudiante.php?matricula=<?= base64_encode($resultado["mat_id"]); ?>')">Pasar a <?= $configAdmisiones["cfgi_year_inscripcion"]; ?></a></li>

						<?php }
						} ?>

						<?php if ($resultado["asp_estado_solicitud"] == 1 or $resultado["asp_estado_solicitud"] == 2 or $resultado["asp_estado_solicitud"] == 7) { ?>
							<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Va a eliminar este aspirante. Esta acción es irreversible. Desea continuar?','question','inscripciones-eliminar-aspirante.php?matricula=<?= base64_encode($resultado["mat_id"]); ?>')">Eliminar aspirante</a></li>
						<?php } ?>
						
						<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!', 'Va a ocultar a este aspirante del listado. Desea continuar?', 'question','inscripciones-ocultar-aspirante.php?matricula=<?= base64_encode($resultado["mat_id"]); ?>&aspirante=<?= base64_encode($resultado["asp_id"]); ?>', true, <?=$resultado["asp_id"];?>)">Ocultar aspirante</a></li>
					</ul>
				</div>
			<?php } ?>
		</td>
	</tr>
	
	<!-- Fila expandible -->
	<tr id="expand-<?= $resultado['asp_id']; ?>" class="expandable-row" style="display: none;">
		<td colspan="12">
			<div class="p-4">
				<div class="row">
					<!-- Información del Aspirante -->
					<div class="col-md-4">
						<h6 class="text-primary mb-3"><i class="fa fa-user"></i> Información del Aspirante</h6>
						<div class="row">
							<div class="col-6">
								<p class="mb-2"><strong>Documento:</strong><br><span class="text-muted"><?= $resultado['mat_documento'] ?? 'No disponible'; ?></span></p>
								<p class="mb-2"><strong>Primer Nombre:</strong><br><span class="text-muted"><?= $resultado['mat_nombres'] ?? 'No disponible'; ?></span></p>
								<p class="mb-2"><strong>Segundo Nombre:</strong><br><span class="text-muted"><?= $resultado['mat_nombre2'] ?? 'No disponible'; ?></span></p>
								<p class="mb-2"><strong>Primer Apellido:</strong><br><span class="text-muted"><?= $resultado['mat_primer_apellido'] ?? 'No disponible'; ?></span></p>
							</div>
							<div class="col-6">
								<p class="mb-2"><strong>Segundo Apellido:</strong><br><span class="text-muted"><?= $resultado['mat_segundo_apellido'] ?? 'No disponible'; ?></span></p>
								<p class="mb-2"><strong>Fecha Nacimiento:</strong><br><span class="text-muted"><?= $resultado['mat_fecha_nacimiento'] ?? 'No disponible'; ?></span></p>
								<p class="mb-2"><strong>Lugar Nacimiento:</strong><br><span class="text-muted"><?= $resultado['mat_lugar_nacimiento'] ?? 'No disponible'; ?></span></p>
								<p class="mb-2"><strong>Celular:</strong><br><span class="text-muted"><?= $resultado['mat_celular'] ?? 'No disponible'; ?></span></p>
							</div>
						</div>
						<div class="row mt-2">
							<div class="col-12">
								<p class="mb-2"><strong>Email:</strong><br><span class="text-muted"><?= $resultado['mat_email'] ?? 'No disponible'; ?></span></p>
								<p class="mb-2"><strong>Dirección:</strong><br><span class="text-muted"><?= $resultado['mat_direccion'] ?? 'No disponible'; ?></span></p>
							</div>
						</div>
					</div>
					
					<!-- Información del Acudiente -->
					<div class="col-md-4">
						<h6 class="text-success mb-3"><i class="fa fa-users"></i> Información del Acudiente</h6>
						<p class="mb-2"><strong>Nombre:</strong><br><span class="text-muted"><?= $resultado['asp_nombre_acudiente'] ?? 'No disponible'; ?></span></p>
						<p class="mb-2"><strong>Documento:</strong><br><span class="text-muted"><?= $resultado['asp_documento_acudiente'] ?? 'No disponible'; ?></span></p>
						<p class="mb-2"><strong>Celular:</strong><br><span class="text-muted"><?= $resultado['asp_celular_acudiente'] ?? 'No disponible'; ?></span></p>
						<p class="mb-2"><strong>Email:</strong><br><span class="text-muted"><?= $resultado['asp_email_acudiente'] ?? 'No disponible'; ?></span></p>
						<p class="mb-2"><strong>Parentesco:</strong><br><span class="text-muted"><?= $resultado['asp_parentesco_acudiente'] ?? 'No disponible'; ?></span></p>
					</div>
					
					<!-- Información de la Solicitud -->
					<div class="col-md-4">
						<h6 class="text-info mb-3"><i class="fa fa-file-text"></i> Información de la Solicitud</h6>
						<p class="mb-2"><strong>Fecha Solicitud:</strong><br><span class="text-muted"><?= $resultado['asp_fecha'] ?? 'No disponible'; ?></span></p>
						<p class="mb-2"><strong>Año Solicitud:</strong><br><span class="text-muted"><?= $resultado['asp_agno'] ?? 'No disponible'; ?></span></p>
						<p class="mb-2"><strong>Grado Solicitado:</strong><br><span class="text-muted"><?= $resultado['gra_nombre'] ?? 'No disponible'; ?></span></p>
						<p class="mb-2"><strong>Estado:</strong><br>
							<span class="<?= $badgeClass; ?>">
								<?= $estadosSolicitud[$resultado["asp_estado_solicitud"]]; ?>
							</span>
						</p>
						<?php if (!empty($resultado['asp_observacion'])) { ?>
						<p class="mb-2"><strong>Observación:</strong><br><span class="text-muted" style="font-style: italic;"><?= strip_tags($resultado['asp_observacion']); ?></span></p>
						<?php } ?>
					</div>
				</div>
				
				<!-- Documentos Adjuntos -->
				<div class="row mt-3">
					<div class="col-12">
						<hr>
						<h6 class="text-warning mb-3"><i class="fa fa-paperclip"></i> Documentos Adjuntos</h6>
						<div class="row">
							<?php if (!empty($resultado['asp_comprobante'])) { ?>
							<div class="col-md-3">
								<p class="mb-2"><strong>Comprobante de Pago:</strong><br>
									<a href="../admisiones/files/comprobantes/<?= $resultado["asp_comprobante"]; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
										<i class="fa fa-download"></i> Descargar
									</a>
								</p>
							</div>
							<?php } ?>
							
							<?php if (!empty($resultado['asp_archivo1'])) { ?>
							<div class="col-md-3">
								<p class="mb-2"><strong>Documento 1:</strong><br>
									<a href="../admisiones/files/<?= $resultado["asp_archivo1"]; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
										<i class="fa fa-download"></i> Descargar
									</a>
								</p>
							</div>
							<?php } ?>
							
							<?php if (!empty($resultado['asp_archivo2'])) { ?>
							<div class="col-md-3">
								<p class="mb-2"><strong>Documento 2:</strong><br>
									<a href="../admisiones/files/<?= $resultado["asp_archivo2"]; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
										<i class="fa fa-download"></i> Descargar
									</a>
								</p>
							</div>
							<?php } ?>
							
							<?php if (!empty($resultado['asp_archivo3'])) { ?>
							<div class="col-md-3">
								<p class="mb-2"><strong>Documento 3:</strong><br>
									<a href="../admisiones/files/<?= $resultado["asp_archivo3"]; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
										<i class="fa fa-download"></i> Descargar
									</a>
								</p>
							</div>
							<?php } ?>
							
							<?php if (empty($resultado['asp_comprobante']) && empty($resultado['asp_archivo1']) && empty($resultado['asp_archivo2']) && empty($resultado['asp_archivo3'])) { ?>
							<div class="col-12">
								<p class="text-muted"><em>No hay documentos adjuntos</em></p>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>

<?php $contReg++;
} ?>

<style>
.expandable-row {
	background-color: #f8f9fa !important;
	border-left: 4px solid #007bff;
}

.expand-btn {
	transition: all 0.3s ease;
}

.expandable-row h6 {
	font-weight: 600;
	margin-bottom: 15px;
}

.expandable-row p {
	line-height: 1.6;
}

.expandable-row strong {
	color: #333;
	font-size: 0.85em;
}

.expandable-row .text-muted {
	color: #666 !important;
	font-size: 0.95em;
}
</style>

<script>
$(document).ready(function() {
	// Event listener para expandir/colapsar filas
	$(document).on('click', '.expand-btn', function() {
		var id = $(this).data('id');
		var row = $('#expand-' + id);
		var icon = $(this).find('i');
		var button = $(this);

		if (row.is(':visible')) {
			// Collapse with animation
			row.slideUp(300, function() {
				icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
				button.removeClass('text-primary').addClass('text-secondary');
			});
		} else {
			// Expand with animation
			row.slideDown(300, function() {
				icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
				button.removeClass('text-secondary').addClass('text-primary');
			});
		}
	});
});
</script>

<!-- Modal de Edición Rápida de Aspirante -->
<div class="modal fade" id="modalEdicionAspirante" tabindex="-1" role="dialog" aria-labelledby="modalEdicionAspiranteLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="modalEdicionAspiranteLabel">
					<i class="fa fa-edit"></i> Edición Rápida de Aspirante
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="formEdicionAspirante">
					<input type="hidden" id="asp_id_modal" name="asp_id">
					<input type="hidden" id="mat_id_modal" name="mat_id">
					
					<!-- Estado y Observación -->
					<div class="row">
						<div class="col-md-6">
							<h6 class="text-primary mb-3"><i class="fa fa-file-text"></i> Estado de la Solicitud</h6>
							
							<div class="form-group">
								<label for="estado_solicitud_modal">Estado <span class="text-danger">*</span></label>
								<select class="form-control" id="estado_solicitud_modal" name="estado_solicitud" required>
									<option value="">Seleccionar...</option>
									<?php foreach ($ordenReal as $clave) { ?>
										<option value="<?= $clave; ?>"><?= $estadosSolicitud[$clave]; ?></option>
									<?php } ?>
								</select>
							</div>
							
							<div class="form-group">
								<label for="enviar_correo_modal">Enviar correo al guardar</label>
								<select class="form-control" id="enviar_correo_modal" name="enviar_correo">
									<option value="2">NO</option>
									<option value="1">SI</option>
								</select>
								<small class="form-text text-muted">Si escoge que sí, se enviará un correo al acudiente con la observación y el estado de la solicitud.</small>
							</div>
						</div>
						
						<div class="col-md-6">
							<h6 class="text-success mb-3"><i class="fa fa-comment"></i> Observación</h6>
							
							<div class="form-group">
								<label for="observacion_modal">Observación</label>
								<textarea class="form-control" id="observacion_modal" name="observacion" rows="8" placeholder="Escriba aquí las observaciones..."></textarea>
								<small class="form-text text-muted">Esta observación será visible para el acudiente.</small>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					<i class="fa fa-times"></i> Cancelar
				</button>
				<button type="button" class="btn btn-primary" id="btnGuardarAspirante">
					<i class="fa fa-save"></i> Guardar Cambios
				</button>
			</div>
		</div>
	</div>
</div>

<script>
// Función auxiliar para limpiar HTML y obtener solo el texto
function stripHtmlTags(html) {
	if (!html) return '';
	
	// Crear un elemento temporal para parsear el HTML
	var tmp = document.createElement('div');
	tmp.innerHTML = html;
	
	// Obtener solo el texto sin etiquetas
	return tmp.textContent || tmp.innerText || '';
}

// Función para abrir modal de edición de aspirante
function abrirModalEdicionAspirante(aspId, matId) {
	// Limpiar formulario
	$('#formEdicionAspirante')[0].reset();
	$('#asp_id_modal').val(aspId);
	$('#mat_id_modal').val(matId);
	
	// Deshabilitar botón de guardar mientras carga
	$('#btnGuardarAspirante').html('<i class="fa fa-spinner fa-spin"></i> Cargando...').prop('disabled', true);
	
	// Obtener datos del aspirante via AJAX
	$.ajax({
		url: 'ajax-obtener-datos-aspirante.php',
		method: 'POST',
		data: { asp_id: aspId, mat_id: matId },
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				var aspirante = response.data;
				
				// Limpiar HTML de la observación para mostrar solo texto en el textarea
				var observacionTexto = stripHtmlTags(aspirante.asp_observacion || '');
				
				// Llenar formulario
				$('#estado_solicitud_modal').val(aspirante.asp_estado_solicitud || '');
				$('#observacion_modal').val(observacionTexto);
				
				// Si el estado es Aprobado (6), deshabilitar el campo de estado y mostrar advertencia
				if (aspirante.asp_estado_solicitud == 6) {
					$('#estado_solicitud_modal').prop('disabled', true);
					$('#btnGuardarAspirante').prop('disabled', true).html('<i class="fa fa-lock"></i> No se puede editar (Aprobado)');
					
					// Mostrar advertencia en el modal
					if ($('#advertenciaAprobado').length === 0) {
						$('#formEdicionAspirante').prepend(
							'<div id="advertenciaAprobado" class="alert alert-warning">' +
							'<i class="fa fa-exclamation-triangle"></i> ' +
							'<strong>Advertencia:</strong> Este aspirante ya está aprobado y no se puede modificar su estado.' +
							'</div>'
						);
					}
				} else {
					// Habilitar el campo de estado si no está aprobado
					$('#estado_solicitud_modal').prop('disabled', false);
					$('#btnGuardarAspirante').prop('disabled', false).html('<i class="fa fa-save"></i> Guardar Cambios');
					$('#advertenciaAprobado').remove();
				}
				
				// Mostrar modal
				$('#modalEdicionAspirante').modal('show');
			} else {
				alert('❌ Error al cargar datos del aspirante: ' + (response.message || 'Error desconocido'));
				$('#btnGuardarAspirante').html('<i class="fa fa-save"></i> Guardar Cambios').prop('disabled', false);
			}
		},
		error: function(xhr, status, error) {
			console.error('Error al cargar datos del aspirante:', error);
			alert('❌ Error de conexión. Intente nuevamente.');
			$('#btnGuardarAspirante').html('<i class="fa fa-save"></i> Guardar Cambios').prop('disabled', false);
		}
	});
}

// Función para guardar cambios del aspirante
function guardarCambiosAspirante() {
	// Validar formulario
	if (!$('#formEdicionAspirante')[0].checkValidity()) {
		$('#formEdicionAspirante')[0].reportValidity();
		return;
	}
	
	// Validar campo obligatorio
	var estadoSolicitud = $('#estado_solicitud_modal').val();
	
	if (!estadoSolicitud) {
		alert('El estado de solicitud es obligatorio.');
		return;
	}
	
	// Mostrar indicador de carga
	var saveBtn = $('#btnGuardarAspirante');
	var originalText = saveBtn.html();
	saveBtn.html('<i class="fa fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);
	
	// Recopilar datos del formulario
	var formData = {
		asp_id: $('#asp_id_modal').val(),
		mat_id: $('#mat_id_modal').val(),
		estado_solicitud: estadoSolicitud,
		observacion: $('#observacion_modal').val().trim(),
		enviar_correo: $('#enviar_correo_modal').val()
	};
	
	// Enviar datos por AJAX
	$.ajax({
		url: 'ajax-actualizar-aspirante-rapido.php',
		method: 'POST',
		data: formData,
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				// Cerrar modal
				$('#modalEdicionAspirante').modal('hide');
				
				// Mostrar mensaje de éxito
				alert('✅ ' + (response.message || 'Aspirante actualizado correctamente'));
				
				// Recargar la página para ver los cambios
				setTimeout(function() {
					location.reload();
				}, 1000);
			} else {
				// Mostrar mensaje de error
				alert('❌ Error: ' + (response.message || 'Error desconocido'));
			}
		},
		error: function(xhr, status, error) {
			console.error('Error en la petición:', error);
			alert('❌ Error de conexión. Intente nuevamente.');
		},
		complete: function() {
			saveBtn.html(originalText).prop('disabled', false);
		}
	});
}

// Event listener para el botón de guardar
$(document).ready(function() {
	$('#btnGuardarAspirante').on('click', function() {
		guardarCambiosAspirante();
	});
});
</script>