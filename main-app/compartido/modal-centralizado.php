<script type="application/javascript">
	async function abrirModal(titulo, url, data) {
		const contenido = document.getElementById('contenidoCentralizado');
		var gifCarga = document.getElementById("gifCarga");

		if (gifCarga) {
			document.getElementById("gifCarga").style.display = "block";
		}

		contenido.innerHTML = "";
		resultado = await metodoFetchAsync(url, data, 'html', false);
		resultData = resultado["data"];

		contenido.innerHTML = resultData;
		ejecutarScriptsCargados(contenido);

		document.getElementById('tituloModal').textContent = titulo;

		if (gifCarga) {
			document.getElementById("gifCarga").style.display = "none";
		}
		$('#ModalCentralizado').modal('show');

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
</script>
<div class="modal fade" id="ModalCentralizado" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	<div class="modal-dialog" style="max-width: 1350px!important;">
		<div class="modal-content" style="border-radius: 20px;max-width: 1350px!important; ">

			<div class="modal-header panel-heading-purple">
				<h4 class="modal-title " id="tituloModal">TITULO MODAL</h4>
				<a href="#" data-dismiss="modal" data-bs-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-compra-modulo"><i class="fa fa-window-close"></i></a>
			</div>

			<div class="modal-body">
				<div id="contenidoCentralizado"></div>
			</div>

		</div>
	</div>
</div>