# Template para Edición Rápida - Sistema Sintia

Este documento sirve como guía para implementar edición rápida en cualquier módulo del sistema.

## ✅ Completado:
1. **Áreas** (areas.php) - ✅ Implementado
2. **Cargas** (cargas.php) - ✅ Implementado

## 📋 Patrón de implementación:

### 1. Crear archivo AJAX (ajax-obtener-datos-[entidad].php)
```php
<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);
ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/[Clase].php");

function jsonResponse($data) {
    while (ob_get_level()) { ob_end_clean(); }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['[entidad]_id'] ?? null;
        
        if (empty($id)) {
            jsonResponse(['success' => false, 'message' => 'ID es obligatorio.']);
        }
        
        $datos = [Clase]::traerDatos($config, $id);
        
        if (!$datos) {
            jsonResponse(['success' => false, 'message' => 'No encontrado.']);
        }
        
        jsonResponse(['success' => true, '[entidad]' => $datos]);
        
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>
```

### 2. Agregar botón en lista
```php
<li><a href="javascript:void(0);" class="btn-editar-[entidad]-modal" data-[entidad]-id="<?=$resultado['id'];?>">
    <i class="fa fa-edit"></i> Edición rápida
</a></li>
<li><a href="[entidad]-editar.php?id=<?=base64_encode($resultado['id']);?>">
    <i class="fa fa-pencil"></i> Editar completa
</a></li>
```

### 3. Agregar modal HTML (antes de </body>)
```html
<div class="modal fade" id="modalEditar[Entidad]" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-edit"></i> Edición Rápida de [Entidad]</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="formEditar[Entidad]" action="[entidad]-actualizar.php" method="post">
				<div class="modal-body">
					<div id="[entidad]Loader" class="text-center" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-3x"></i>
						<p>Cargando datos...</p>
					</div>
					
					<div id="[entidad]Formulario" style="display:none;">
						<!-- Campos del formulario -->
						<input type="hidden" id="edit_id" name="id">
						
						<div class="form-group">
							<label>Campo <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="edit_campo" name="campo" required>
						</div>
						
						<!-- Más campos según sea necesario -->
					</div>
					
					<div id="[entidad]Error" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i> <span id="errorMensaje[Entidad]"></span>
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
```

### 4. Agregar JavaScript (antes de </body>)
```javascript
<script>
$(document).ready(function() {
	// Edición rápida
	$(document).on('click', '.btn-editar-[entidad]-modal', function() {
		var id = $(this).data('[entidad]-id');
		
		$('#[entidad]Loader').show();
		$('#[entidad]Formulario').hide();
		$('#[entidad]Error').hide();
		$('#modalEditar[Entidad]').modal('show');
		
		$.ajax({
			url: 'ajax-obtener-datos-[entidad].php',
			type: 'POST',
			data: { [entidad]_id: id },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var datos = response.[entidad];
					$('#edit_id').val(datos.id);
					$('#edit_campo').val(datos.campo);
					// Llenar más campos...
					
					$('#[entidad]Loader').hide();
					$('#[entidad]Formulario').show();
				} else {
					$('#[entidad]Loader').hide();
					$('#errorMensaje[Entidad]').text(response.message);
					$('#[entidad]Error').show();
				}
			},
			error: function() {
				$('#[entidad]Loader').hide();
				$('#errorMensaje[Entidad]').text('Error de conexión');
				$('#[entidad]Error').show();
			}
		});
	});
	
	$('#formEditar[Entidad]').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			success: function() {
				$.toast({
					heading: 'Éxito',
					text: '[Entidad] actualizada correctamente',
					position: 'top-right',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 2000
				});
				$('#modalEditar[Entidad]').modal('hide');
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
```

## 📝 Notas importantes:
1. Reemplazar `[entidad]` con el nombre de la entidad (ej: area, grado, grupo, etc.)
2. Reemplazar `[Entidad]` con el nombre capitalizado (ej: Area, Grado, Grupo, etc.)
3. Reemplazar `[Clase]` con el nombre de la clase PHP (ej: Areas, Grados, Grupos, etc.)
4. Ajustar los campos del formulario según los campos reales de la entidad
5. Ajustar los nombres de los parámetros POST según lo que espera el archivo actualizar
6. NO usar Select2 a menos que ya esté cargado en la página
7. Usar selects nativos de Bootstrap para mejor compatibilidad

## ✅ Ventajas de este patrón:
- Rápido y eficiente
- No recarga la página completa
- Mantiene la posición en la lista
- Feedback visual inmediato
- Reutilizable en cualquier módulo

