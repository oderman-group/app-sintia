<script type="application/javascript">
	// Variable para controlar si ya se inicializó el evento
	var modalCentralizadoInitialized = false;
	
	async function abrirModal(titulo, url, data, timeout, width = '1350px') {
		const contenido = document.getElementById('contenidoCentralizado');
		var overlay = document.getElementById("overlay");

		if (overlay) {
			document.getElementById("overlay").style.display = "flex";
		}

		contenido.innerHTML = "";
		resultado = await metodoFetchAsync(url, data, 'html', false);
		resultData = resultado["data"];

		contenido.innerHTML = resultData;
		ejecutarScriptsCargados(contenido);

		document.getElementById('tituloModal').textContent = titulo;

		if (overlay) {
			document.getElementById("overlay").style.display = "none";
		}
		$('#ModalCentralizado .modal-dialog').css('width', width);
		
		// Mostrar el modal
		$('#ModalCentralizado').modal('show');
		
		// Esperar un momento para que el DOM se actualice y luego inicializar select2
		setTimeout(function() {
			initializeSelect2InModal('ModalCentralizado');
			
			// Auto-cargar datos para modales específicos que usan ModalCentralizado
			// Detectar qué modal es por el título o URL
			if (url && url.includes('sabana')) {
				if (typeof window.autoCargarSabana === 'function') {
					window.autoCargarSabana();
				}
			}
			if (url && url.includes('libro-cursos')) {
				if (typeof window.autoCargarLibro === 'function') {
					window.autoCargarLibro();
				}
			}
		}, 100);

		if (timeout) {
			setTimeout(function() {
				$('#ModalCentralizado').modal('hide');
			}, timeout);
		}
	}
	
	// Función para inicializar select2 en modales
	function initializeSelect2InModal(modalId) {
		$('#' + modalId + ' select.select2').each(function() {
			var $select = $(this);
			
			// Destruir instancia anterior si existe
			if ($select.data('select2')) {
				try {
					$select.select2('destroy');
				} catch(e) {
					// Ignorar errores si ya fue destruido
				}
			}
			
			// Configuración de select2 optimizada para modales
			$select.select2({
				dropdownParent: $('#' + modalId + ' .modal-content'),
				width: '100%',
				minimumResultsForSearch: 0, // Siempre mostrar el buscador
				language: {
					noResults: function() {
						return "No se encontraron resultados";
					},
					searching: function() {
						return "Buscando...";
					}
				},
				placeholder: "Seleccione una opción"
			});
		});
	}
	
	// Inicializar evento de limpieza solo una vez
	$(document).ready(function() {
		if (!modalCentralizadoInitialized) {
			$('#ModalCentralizado').on('hidden.bs.modal', function () {
				$('#ModalCentralizado select.select2').each(function() {
					if ($(this).data('select2')) {
						try {
							$(this).select2('destroy');
						} catch(e) {
							// Ignorar errores
						}
					}
				});
				// Limpiar el contenido
				$('#contenidoCentralizado').html('');
			});
			modalCentralizadoInitialized = true;
		}
	});

	//ejecutar los scripts del string
	function ejecutarScriptsCargados(elemento) {
		var scripts = elemento.getElementsByTagName('script');

		for (var i = 0; i < scripts.length; i++) {
			var script = document.createElement('script');
			// Si el script tiene un atributo src, cargamos el script externo
			if (scripts[i].src) {
				script.src = scripts[i].src;
				script.async = false; // Esto asegura que los scripts se ejecuten en el orden correcto
			} else {
				script.textContent = scripts[i].textContent;
			}

			// Agregamos el script al <head> para que se ejecute
			document.head.appendChild(script);
		}
	}
    // Función para calcular el z-index más alto 
	function getMaxZIndex() {
		let maxZIndex = 0;
		const allElements = document.getElementsByTagName('*');

		for (let i = 0; i < allElements.length; i++) {
			const zIndex = window.getComputedStyle(allElements[i]).zIndex;

			if (!isNaN(zIndex)) {
				maxZIndex = Math.max(maxZIndex, parseInt(zIndex, 10));
			}
		}
		return maxZIndex;
	}
	// Función para asignar el z-index más alto al modal
	function setModalZIndex(modalId) {
		// Obtener el z-index más alto de la página
		const highestZIndex = getMaxZIndex();
		// Asignar un z-index más alto al modal (por ejemplo, sumamos 10 al mayor valor encontrado)
		const modal = document.getElementById(modalId);
		modal.style.setProperty('z-index', highestZIndex + 10, 'important'); 
	}
</script>
<div class="modal fade" id="ModalCentralizado" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="basicModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable">
		<div class="modal-content">

			<div class="modal-header panel-heading-purple">
				<h4 class="modal-title" id="tituloModal">TITULO MODAL</h4>
				<a href="#" data-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-compra-modulo"><i class="fa fa-window-close"></i></a>
			</div>

			<div class="modal-body">
				<div id="contenidoCentralizado"></div>
			</div>

		</div>
	</div>
</div>
