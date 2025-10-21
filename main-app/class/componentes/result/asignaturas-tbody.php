<?php
$contReg = 1;
$filtro = '';

if (isset($_GET["area"]) && is_numeric(base64_decode($_GET["area"]))) {
	$filtro .= " AND am.mat_area='".base64_decode($_GET["area"])."'";
}

$consulta = Asignaturas::consultarTodasAsignaturas($conexion, $config, $filtro);

while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	$numeros = CargaAcademica::contarCargasMaterias($config, $resultado['mat_id']);
?>
<tr id="ASIG<?= $resultado['mat_id']; ?>">
	<td><button class="btn btn-sm btn-info expand-btn" data-id="<?= $resultado['mat_id']; ?>" title="Ver detalles"><i class="fa fa-plus"></i></button></td>
	<td><?=$contReg;?></td>
	<td><?=$resultado['mat_id'];?></td>
	<td>
		<span class="materia-nombre-display" 
			  data-id="<?= $resultado['mat_id']; ?>"
			  data-nombre="<?= htmlspecialchars($resultado['mat_nombre'], ENT_QUOTES); ?>"
			  style="cursor: pointer; border-bottom: 1px dotted #999;"
			  title="Clic para editar">
			<?=$resultado['mat_nombre'];?>
		</span>
		<i class="fa fa-edit text-muted ml-1" style="font-size: 0.8em;"></i>
	</td>
	<?php if($config['conf_agregar_porcentaje_asignaturas']=='SI'){ ?>
		<td>
			<span class="materia-valor-display" 
				  data-id="<?= $resultado['mat_id']; ?>"
				  data-valor="<?= htmlspecialchars($resultado['mat_valor'], ENT_QUOTES); ?>"
				  style="cursor: pointer; border-bottom: 1px dotted #999;"
				  title="Clic para editar">
				<?=$resultado['mat_valor'];?>%
			</span>
			<i class="fa fa-edit text-muted ml-1" style="font-size: 0.8em;"></i>
		</td>
	<?php }?>	
	<td><?=$resultado['ar_nombre'];?></td>
	<?php 
		$cargas = $numeros[0];
		if (Modulos::validarSubRol(['DT0032'])) {
			$cargas='<a href="cargas.php?asignatura='.base64_encode($resultado['mat_id']).'" class="text-dark">'.$numeros[0].'</a>';
		}
	?>
	<td><span class="badge badge-warning"><?=$cargas?></span></td>
	
	<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0021','DT0151'])){?>
		<td>
			<div class="btn-group">
				<button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
				<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
					<i class="fa fa-angle-down"></i>
				</button>
				<ul class="dropdown-menu" role="menu">
					<?php if(Modulos::validarSubRol(['DT0021'])){?>
						<li><a href="javascript:void(0);" class="btn-editar-asignatura-modal" data-asignatura-id="<?=$resultado['mat_id'];?>"><i class="fa fa-edit"></i> Edición rápida</a></li>
						<li><a href="asignaturas-editar.php?id=<?=base64_encode($resultado['mat_id']);?>"><i class="fa fa-pencil"></i> <?=$frases[165][$datosUsuarioActual['uss_idioma']];?> completa</a></li>
					<?php } if($numeros[0]==0 && Modulos::validarSubRol(['DT0151'])){?>
						<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este registro?','question','asignaturas-eliminar.php?id=<?=base64_encode($resultado['mat_id']);?>')">Eliminar</a></li>
					<?php } ?>
				</ul>
			</div>
		</td>
	<?php }?>
</tr>

<!-- Fila expandible -->
<tr id="expand-<?= $resultado['mat_id']; ?>" class="expandable-row" style="display: none;">
	<td colspan="<?= ($config['conf_agregar_porcentaje_asignaturas']=='SI' ? 8 : 7); ?>">
		<div class="p-4">
			<div class="row">
				<!-- Información de la Asignatura -->
				<div class="col-md-4">
					<h6 class="text-primary mb-3"><i class="fa fa-book"></i> Información de la Asignatura</h6>
					<p class="mb-2"><strong>ID:</strong><br><span class="text-muted"><?= $resultado['mat_id']; ?></span></p>
					<p class="mb-2"><strong>Nombre:</strong><br><span class="text-muted"><?= $resultado['mat_nombre']; ?></span></p>
					<?php if($config['conf_agregar_porcentaje_asignaturas']=='SI'){ ?>
						<p class="mb-2"><strong>Valor (%):</strong><br><span class="text-muted"><?= $resultado['mat_valor']; ?>%</span></p>
					<?php } ?>
					<p class="mb-2"><strong>Área:</strong><br><span class="text-muted"><?= $resultado['ar_nombre'] ?? 'Sin área'; ?></span></p>
					<p class="mb-2"><strong>Total Cargas:</strong><br><span class="badge badge-info"><?= $numeros[0]; ?></span></p>
				</div>
				
				<!-- Cargas Académicas -->
				<div class="col-md-8">
					<h6 class="text-success mb-3"><i class="fa fa-users"></i> Cargas Académicas Asociadas</h6>
					<div id="cargas-materia-<?= $resultado['mat_id']; ?>">
						<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando cargas académicas...</p>
					</div>
				</div>
			</div>
		</div>
	</td>
</tr>

<?php 
	$contReg++;
}
?>

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

.materia-nombre-display,
.materia-valor-display {
	padding: 2px 6px;
	border-radius: 4px;
	transition: background-color 0.2s ease;
}

.materia-nombre-display:hover,
.materia-valor-display:hover {
	background-color: #e3f2fd;
}

.carga-item {
	background: white;
	padding: 10px 15px;
	margin-bottom: 8px;
	border-radius: 6px;
	border-left: 4px solid #007bff;
	box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.carga-item:hover {
	box-shadow: 0 2px 6px rgba(0,0,0,0.1);
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
				icon.removeClass('fa-minus').addClass('fa-plus');
				button.removeClass('btn-warning').addClass('btn-info');
			});
		} else {
			// Expand with animation
			row.slideDown(300, function() {
				icon.removeClass('fa-plus').addClass('fa-minus');
				button.removeClass('btn-info').addClass('btn-warning');
				
				// Cargar cargas académicas si aún no se han cargado
				var cargasContainer = $('#cargas-materia-' + id);
				if (cargasContainer.find('.fa-spinner').length > 0) {
					cargarCargasMateria(id);
				}
			});
		}
	});
	
	// Edición inline del nombre
	$(document).on('click', '.materia-nombre-display', function() {
		var span = $(this);
		var materiaId = span.data('id');
		var nombreActual = span.data('nombre');
		
		var input = $('<input type="text" class="form-control form-control-sm" style="display:inline-block; width:auto; min-width:200px;">');
		input.val(nombreActual);
		
		span.replaceWith(input);
		input.focus();
		input.select();
		
		// Guardar al perder el foco o presionar Enter
		input.on('blur keypress', function(e) {
			if (e.type === 'blur' || e.which === 13) {
				e.preventDefault();
				var nuevoNombre = input.val().trim();
				
				if (nuevoNombre && nuevoNombre !== nombreActual) {
					guardarCambioMateria(materiaId, 'nombre', nuevoNombre, input, span);
				} else {
					input.replaceWith(span);
				}
			}
		});
		
		// Cancelar con ESC
		input.on('keydown', function(e) {
			if (e.which === 27) {
				input.replaceWith(span);
			}
		});
	});
	
	// Edición inline del valor
	$(document).on('click', '.materia-valor-display', function() {
		var span = $(this);
		var materiaId = span.data('id');
		var valorActual = span.data('valor');
		
		var input = $('<input type="number" class="form-control form-control-sm" style="display:inline-block; width:80px;" min="0" max="100" step="0.01">');
		input.val(valorActual);
		
		span.replaceWith(input);
		input.focus();
		input.select();
		
		// Guardar al perder el foco o presionar Enter
		input.on('blur keypress', function(e) {
			if (e.type === 'blur' || e.which === 13) {
				e.preventDefault();
				var nuevoValor = input.val().trim();
				
				if (nuevoValor && nuevoValor !== valorActual) {
					guardarCambioMateria(materiaId, 'valor', nuevoValor, input, span);
				} else {
					input.replaceWith(span);
				}
			}
		});
		
		// Cancelar con ESC
		input.on('keydown', function(e) {
			if (e.which === 27) {
				input.replaceWith(span);
			}
		});
	});
});

// Función para cargar cargas académicas de una materia
function cargarCargasMateria(materiaId) {
	var container = $('#cargas-materia-' + materiaId);
	
	$.ajax({
		url: 'ajax-obtener-cargas-materia.php',
		method: 'POST',
		data: { materia_id: materiaId },
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				renderizarCargas(response.cargas, container);
			} else {
				container.html('<p class="text-danger"><i class="fa fa-exclamation-triangle"></i> Error: ' + (response.message || 'Error desconocido') + '</p>');
			}
		},
		error: function(xhr, status, error) {
			console.error('Error al cargar cargas:', error);
			container.html('<p class="text-danger"><i class="fa fa-exclamation-triangle"></i> Error de conexión</p>');
		}
	});
}

// Función para renderizar cargas
function renderizarCargas(cargas, container) {
	var html = '';
	
	if (cargas.length === 0) {
		html = '<p class="text-muted text-center"><em>No hay cargas académicas asociadas a esta materia</em></p>';
	} else {
		html = '<div class="cargas-list">';
		cargas.forEach(function(carga) {
			html += '<div class="carga-item">';
			html += '   <div class="d-flex justify-content-between align-items-center">';
			html += '       <div>';
			html += '           <strong>' + carga.docente + '</strong>';
			html += '           <br><small class="text-muted">' + carga.curso + ' - Periodo ' + carga.periodo + '</small>';
			html += '       </div>';
			html += '       <div class="text-right">';
			html += '           <span class="badge badge-primary">IH: ' + carga.ih + '</span>';
			if (carga.director_grupo == 'SI') {
				html += '       <span class="badge badge-success ml-1">Director</span>';
			}
			html += '       </div>';
			html += '   </div>';
			html += '</div>';
		});
		html += '</div>';
	}
	
	container.html(html);
}

// Función para guardar cambios de materia (inline edit)
function guardarCambioMateria(materiaId, campo, nuevoValor, inputElement, spanElement) {
	// Mostrar indicador de carga
	inputElement.prop('disabled', true).css('opacity', '0.5');
	
	$.ajax({
		url: 'ajax-actualizar-materia-campo.php',
		method: 'POST',
		data: {
			materia_id: materiaId,
			campo: campo,
			valor: nuevoValor
		},
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				// Actualizar el span con el nuevo valor
				if (campo === 'nombre') {
					spanElement.text(nuevoValor);
					spanElement.data('nombre', nuevoValor);
				} else if (campo === 'valor') {
					spanElement.text(nuevoValor + '%');
					spanElement.data('valor', nuevoValor);
				}
				
				// Reemplazar input por span actualizado
				inputElement.replaceWith(spanElement);
				
				// Mostrar notificación de éxito
				$.toast({
					heading: 'Éxito',
					text: response.message || 'Materia actualizada correctamente',
					position: 'top-right',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 3000
				});
			} else {
				// Mostrar error y revertir
				inputElement.replaceWith(spanElement);
				
				$.toast({
					heading: 'Error',
					text: response.message || 'No se pudo actualizar la materia',
					position: 'top-right',
					loaderBg: '#bf441d',
					icon: 'error',
					hideAfter: 5000
				});
			}
		},
		error: function(xhr, status, error) {
			console.error('Error al actualizar materia:', error);
			inputElement.replaceWith(spanElement);
			
			$.toast({
				heading: 'Error',
				text: 'Error de conexión. Intente nuevamente.',
				position: 'top-right',
				loaderBg: '#bf441d',
				icon: 'error',
				hideAfter: 5000
			});
		}
	});
}
</script>


