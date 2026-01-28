<div class="modal fade bd-example-modal-lg" id="<?= $idModal; ?>" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true"<?php if (!empty($modalBackdropStatic) && $idModal === 'nuevaCargModal') { ?> data-backdrop="static" data-keyboard="false"<?php } ?>>
	<div class="modal-dialog modal-lg" style="max-width: 1350px!important;" role="document">
		<div class="modal-content shadow" id="contenidoModal<?= $idModal; ?>" style="max-width: 1350px!important;">
			<?php include($contenido) ?>
			<div class="modal-footer">
				<a href="#" data-dismiss="modal" class="btn btn-danger">
				<i class="fa fa fa-window-close"></i> Cerrar
				</a>
			</div>
		</div>
	</div>
	<script>
    	$('#<?= $idModal; ?>').on('shown.bs.modal', function () {
			const form = document.querySelector('#form-<?= $idModal ?>');

			if (form) {
				console.log('Formulario encontrado-<?= $idModal ?>');
				form.addEventListener('submit', function(event) {
					event.preventDefault();
					console.log('Formulario enviado-<?= $idModal ?>');

					const formData = new FormData(form);
					const data = new URLSearchParams();
					for (const pair of formData) {
						data.append(pair[0], pair[1]);
					}

					fetch(form.action, {
						method: form.method,
						body: data,
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded'
						}
					})
					.then(response => response.text())
					.then(responseText => {
						window.location.href = responseText;
					})
					.catch(error => console.error('Error:', error));
				});
			} else {
				console.error('Formulario no encontrado-<?= $idModal ?>');
			}
		});
	</script>
</div>