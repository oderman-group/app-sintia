<?php

class ComponenteModal
{
	private $urlHtml;
	private $id;
	private $titulo;
	private $data;
	private $timeOut;
	private $width;
	private $estatico;
	public function __construct($id, $titulo, $urlHtml, $data = null, $timeOut = null, $width = '1350px', $estatico = true)
	{
		$this->id = $id;
		$this->titulo = $titulo;
		$this->urlHtml = $urlHtml;
		$this->data = $data;
		$this->timeOut = $timeOut;
		$this->width = $width;
		$this->estatico = $estatico;

		self::crearfuncion();
		self::generarComponente();

	}
	public function generarComponente()
	{
		// Deshabilitar fade en modales problemáticos
		$fadeClass = ($this->id == 'boletines' || $this->id == 'insDocumentos') ? '' : 'fade';
		?>
		<div class="modal <?= $fadeClass ?>" id="ComponeteModal-<?= $this->id ?>" tabindex="-1" role="dialog" aria-labelledby="basicModal"
			<?= $this->estatico ? 'data-backdrop="static"' : '' ?> aria-hidden="true">
			<div class="modal-dialog modal-dialog-scrollable">
				<div class="modal-content">

					<div class="modal-header panel-heading-purple">
						<h4 class="modal-title" id="ComponeteModalTitulo-<?= $this->id ?>"><?= $this->titulo ?></h4>
						<a href="#" class="btn btn-danger" onclick="cerrarModal_<?= $this->id ?>()" aria-label="Close"
							id="boton-cerrar-compra-modulo"><i class="fa fa-window-close"></i></a>
					</div>

					<div class="modal-body">
						<div id="ComponeteModalContenido-<?= $this->id ?>"></div>
					</div>

				</div>
			</div>
		</div>
		<?php
	}
	public static function ejecutar($script)
	{
		echo "<script type='text/javascript'>{$script}</script>";
	}
	public static function mostrarAlerta($mensaje)
	{
		self::ejecutar("alert('{$mensaje}');");
	}

	public function crearfuncion()
	{

		$id          = $this->id;
		$urlHtml     = $this->urlHtml;
		$data        = empty($this->data) ? 'null' : json_encode($this->data);
		$width       = empty($this->width) ? '1350px' : $this->width;
		$AutoCerrado = empty($this->timeOut) ? 'false' : 'true';
		$timeOut     = empty($this->timeOut) ? '' : $this->timeOut;

		$script = "<script type='text/javascript'> 
		// Variables globales de control
		if (typeof window.modalsInitialized === 'undefined') {
			window.modalsInitialized = {};
		}
		if (typeof window.modalOpeningLocks === 'undefined') {
			window.modalOpeningLocks = {};
		}
		if (typeof window.modalLastOpened === 'undefined') {
			window.modalLastOpened = {};
		}
		
		async function abrirModal_$id(data) {
			try {
				// BLOQUEO 1: Si el modal ya está visible, NO hacer nada
				if ($('#ComponeteModal-$id').hasClass('show') || $('#ComponeteModal-$id').is(':visible')) {
					return false;
				}
				
				// BLOQUEO 2: Debounce - no permitir abrir más de 1 vez por segundo
				var now = Date.now();
				if (window.modalLastOpened['$id'] && (now - window.modalLastOpened['$id']) < 1000) {
					return false;
				}
				window.modalLastOpened['$id'] = now;
				
				// BLOQUEO 3: Lock de proceso
				if (window.modalOpeningLocks['$id']) {
					return false;
				}
				window.modalOpeningLocks['$id'] = true;
				
				const contenido = document.getElementById('ComponeteModalContenido-$id');
				var overlay = document.getElementById('overlay');
				
				// Mostrar overlay
				if (overlay) {
					overlay.style.display = 'flex';
				}

				// Limpiar COMPLETAMENTE el contenido previo
				contenido.innerHTML = '';
				
				// Destruir TODOS los select2 previos
				$('#ComponeteModal-$id select').each(function() {
					if ($(this).data('select2')) {
						try { $(this).select2('destroy'); } catch(e) {}
					}
				});
				
				// Cargar nuevo contenido
				resultado = await metodoFetchAsync('$urlHtml',";

		$script  .= $data=='null' ? 'data' : $data ;

		$script  .= ", 'html', false);
				resultData = resultado['data'];
				
				// PASO CRÍTICO: Limpiar TODO del HTML antes de insertarlo
				var parser = new DOMParser();
				var doc = parser.parseFromString(resultData, 'text/html');
				
				// Eliminar TODOS los scripts
				var scripts = doc.querySelectorAll('script');
				scripts.forEach(function(script) { script.remove(); });
				
				// Eliminar TODOS los links problemáticos
				var links = doc.querySelectorAll('link');
				links.forEach(function(link) { 
					if (link.href.includes('select2') || link.href.includes('jquery')) {
						link.remove(); 
					}
				});
				
				// Eliminar TODOS los estilos inline que puedan causar problemas
				var styles = doc.querySelectorAll('style');
				styles.forEach(function(style) { 
					if (style.textContent.includes('modal') || style.textContent.includes('z-index')) {
						style.remove();
					}
				});
				
				// Insertar SOLO el body limpio
				contenido.innerHTML = doc.body.innerHTML;
				
				// Configurar width
				$('#ComponeteModal-$id .modal-dialog').css('width','$width');

				// Ocultar overlay
				if (overlay) {
					overlay.style.display = 'none';
				}

				// CRÍTICO: Eliminar backdrops duplicados antes de mostrar el modal
				$('.modal-backdrop').remove();
				
				// Remover TODOS los eventos previos
				$('#ComponeteModal-$id').off('shown.bs.modal');
				$('#ComponeteModal-$id').off('show.bs.modal');
				
				// Mostrar el modal SIN backdrop para evitar duplicados
				$('#ComponeteModal-$id').modal({
					backdrop: false,  // Deshabilitamos el backdrop automático
					keyboard: true,
					focus: true,
					show: true
				});
				
				// Crear UN SOLO backdrop manualmente con opacidad fija y bloqueo TOTAL de eventos
				var backdropHtml = '<div class=\"modal-backdrop show\" id=\"backdrop-$id\"></div>';
				$('body').append(backdropHtml);
				
				// Aplicar estilos inline para asegurar que bloquee todo
				$('#backdrop-$id').css({
					'opacity': '0.5',
					'position': 'fixed',
					'top': '0',
					'left': '0',
					'width': '100vw',
					'height': '100vh',
					'background-color': 'rgb(0, 0, 0)',
					'z-index': '1040',
					'pointer-events': 'auto',
					'cursor': 'default'
				});
				
				// Bloquear scroll del body
				$('body').css('overflow', 'hidden');
				
				// Agregar clase modal-open al body
				$('body').addClass('modal-open');
				
				// Inicializar select2 después de un delay fijo
				setTimeout(function() {
					$('#ComponeteModal-$id select.select2').each(function() {
						var \$sel = $(this);
						if (\$sel.data('select2')) {
							try { \$sel.select2('destroy'); } catch(e) {}
						}
						\$sel.select2({
							dropdownParent: $('#ComponeteModal-$id .modal-content'),
							width: '100%',
							minimumResultsForSearch: 0,
							placeholder: 'Seleccione una opción',
							language: {
								noResults: function() { return 'No se encontraron resultados'; },
								searching: function() { return 'Buscando...'; }
							}
						});
					});
					
					// Auto-cargar datos según el modal específico
					if ('$id' === 'boletines' && typeof window.autoCargarSiYearSeleccionado === 'function') {
						window.autoCargarSiYearSeleccionado();
					}
					
					// Liberar bloqueo
					window.modalOpeningLocks['$id'] = false;
					
					// Disparar evento personalizado para indicar que el modal está listo
					$(document).trigger('modalReady', ['$id']);
				}, 600);

				if ($AutoCerrado) {
					setTimeout(function() {
						$('#ComponeteModal-$id').modal('hide');
					}, $timeOut);
				}
				
			} catch(error) {
				console.error('Error abriendo modal $id:', error);
				window.modalOpeningLocks['$id'] = false;
				if (overlay) {
					overlay.style.display = 'none';
				}
			}
		}
		
		// Inicializar evento de limpieza solo una vez
		if (!window.modalsInitialized['modal_$id']) {
			$('#ComponeteModal-$id').on('hidden.bs.modal', function () {
				// Liberar bloqueo inmediatamente
				window.modalOpeningLocks['$id'] = false;
				
				// CRÍTICO: Eliminar el backdrop manual
				$('#backdrop-$id').remove();
				$('.modal-backdrop').remove();
				
				// Restaurar scroll del body
				$('body').removeClass('modal-open');
				$('body').css('overflow', '');
				$('body').css('padding-right', '');
				
				// Destruir select2
				$('#ComponeteModal-$id select').each(function() {
					if ($(this).data('select2')) {
						try { $(this).select2('destroy'); } catch(e) {}
					}
				});
				
				// Destruir popovers
				$('#ComponeteModal-$id [data-toggle=\"popover\"], #ComponeteModal-$id [data-toggle=\"popover_2\"]').each(function() {
					try { $(this).popover('dispose'); } catch(e) {}
				});
				
				// Limpiar contenido
				$('#ComponeteModalContenido-$id').html('');
				
				// Resetear variables
				Object.keys(window).forEach(function(key) {
					if (key.startsWith('executed_')) {
						delete window[key];
					}
				});
			});
			
			// También liberar bloqueo si hay error al mostrar el modal
			$('#ComponeteModal-$id').on('show.bs.modal', function(e) {
				// Si por alguna razón se intenta abrir cuando ya está abierto, prevenir
				if ($(this).hasClass('show')) {
					e.preventDefault();
					e.stopPropagation();
					return false;
				}
			});
			
			window.modalsInitialized['modal_$id'] = true;
		}
		function cerrarModal_$id() {
			// Cerrar modal
			$('#ComponeteModal-$id').modal('hide');
			
			// Limpiar backdrop inmediatamente
			setTimeout(function() {
				$('#backdrop-$id').remove();
				$('.modal-backdrop').remove();
				$('body').removeClass('modal-open');
				$('body').css('overflow', '');
				$('body').css('padding-right', '');
			}, 100);
		}
		</script>";
		echo $script;
	}

	public function getMetodoAbrirModal($data = null): string
	{
		$this->data = !empty($data) ? $data : $this->data;
		$methodo="abrirModal_$this->id($this->data)";
		return $methodo;
	}

	
	public function cerraModal(): string
	{
		$methodo="cerrarModal_$this->id()";
		return $methodo;
	}
}
?>