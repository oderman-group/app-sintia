<script type="application/javascript">
	async function abrirModal(titulo, url, data, timeout,width = '1350px') {
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


		contenido.innerHTML = resultData;
		ejecutarScriptsCargados(contenido);

		document.getElementById('tituloModal').textContent = titulo;

		if (overlay) {
			document.getElementById("overlay").style.display = "none";
		}
		$('#ModalCentralizado .modal-dialog').css('width', width);
		$('#ModalCentralizado').modal('show');

		if (timeout) {
			setTimeout(function() {
				$('#ModalCentralizado').modal('hide'); // Cierra el modal
			}, timeout);
		}


	}

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
<div class="modal fade" id="ModalCentralizado" tabindex="-1"   role="dialog" data-backdrop="static" aria-labelledby="basicModal" aria-hidden="true">
	<div class="modal-dialog" style="max-width: 1350px">
		<div class="modal-content" style="border-radius: 20px;max-width: 1350px!important; ">

			<div class="modal-header panel-heading-purple">
				<h4 class="modal-title " id="tituloModal">TITULO MODAL</h4>
				<a href="#" data-dismiss="modal" data-bs-dismiss="modal" data-bs-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-compra-modulo"><i class="fa fa-window-close"></i></a>
			</div>

			<div class="modal-body">
				<div id="contenidoCentralizado"></div>
			</div>

		</div>
	</div>
</div>