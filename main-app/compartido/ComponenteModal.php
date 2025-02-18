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
		?>
		<div class="modal fade" id="ComponeteModal-<?= $this->id ?>" tabindex="-1" role="dialog" aria-labelledby="basicModal"
			<?= $this->estatico ? 'data-backdrop="static" data-bs-backdrop="static"' : '' ?> aria-hidden="true">
			<div class="modal-dialog" style="max-width: 1350px!important;">
				<div class="modal-content" style="border-radius: 20px;max-width: 1350px!important; ">

					<div class="modal-header panel-heading-purple">
						<h4 class="modal-title " id="ComponeteModalTitulo-<?= $this->id ?>"><?= $this->titulo ?></h4>
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
		async function abrirModal_$id(data) {
		const contenido = document.getElementById('ComponeteModalContenido-$id');
		var overlay = document.getElementById('overlay');
		if (overlay) {
			document.getElementById('overlay').style.display = 'flex';
		}
		contenido.innerHTML = '';
		resultado = await metodoFetchAsync('$urlHtml',";

		$script  .= $data=='null' ? 'data' : $data ;

		$script  .= ", 'html', false);
		resultData = resultado['data'];

        contenido.innerHTML = resultData;
		ejecutarScriptsCargados(contenido);
        
		
		
		$('#ComponeteModal-$id .modal-dialog').css('width','$width');

		if (overlay) {
			document.getElementById('overlay').style.display = 'none';
		}

		$('#ComponeteModal-$id').modal('show');

		if ($AutoCerrado) {
			setTimeout(function() {
				$('#ComponeteModal-$id').modal('hide'); // Cierra el modal
			}, $timeOut);
		}
		}
		function cerrarModal_$id() {
			$('#ComponeteModal-$id').modal('hide');
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