<?php
$explode = explode("main-app/", $_SERVER["REQUEST_URI"]);
$urlOrigen = $explode[1];
?>
<div class="modal fade" id="modalComprarPaquete" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	<div class="modal-dialog"  style="max-width: 1350px!important;">
		<div class="modal-content">

		<div class="modal-header">
			<h4 class="modal-title" id="tituloPaquete" align="center"></h4>
			<a href="#" data-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-compra-paquete"><i class="fa fa-window-close"></i></a>
		</div>

		<div class="modal-body" align="center">
			<img id="imgPaquete" src="" style="width: 100%;" alt="Imagen de Paquetes">
			<p>
				<b id="tituloDescripcionPaquete"></b><br>
				<span id="descripcionPaquete"></span>
			</p>

		</div>

		<div class="modal-footer">
			<form action="../pagos-online/index.php" method="post" target="_target">
				<input type="hidden" class="form-control" name="idUsuario" value="<?=$datosUsuarioActual['uss_id'];?>">
				<input type="hidden" class="form-control" name="emailUsuario" value="<?=$datosUsuarioActual['uss_email'];?>">
				<input type="hidden" class="form-control" name="documentoUsuario" value="<?=$datosUsuarioActual['uss_documento'];?>">
				<input type="hidden" class="form-control" name="nombreUsuario" value="<?=UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual);?>">
				<input type="hidden" class="form-control" name="celularUsuario" value="<?=$datosUsuarioActual['uss_celular'];?>">
				<input type="hidden" class="form-control" name="idInstitucion" value="<?=$config['conf_id_institucion'];?>">
				<input type="hidden" class="form-control" name="monto" id="montoPaquete" value="">
				<input type="hidden" class="form-control" name="nombre" id="nombrePaquete" value="">
				<input type="hidden" class="form-control" name="idPaquete" id="idPaquete" value="">
				<input type="hidden" class="form-control" name="tipoPaquete" id="tipoPaquete" value="">
				<input type="hidden" class="form-control" name="url_origen" id="url_origen" value="<?=REDIRECT_ROUTE."/".$urlOrigen;?>">

				<button type="submit" class="btn btn-info"><i class="fa fa-credit-card" aria-hidden="true"></i>ADQUIRIR PAQUETE</button>
			</form>
			<a href="#" id="enlaceWhatsappPaquete" target="_blank" class="btn btn-success">CONTACTAR CON UN ASESOR</a>
		</div>

		</div>
	</div>
</div>