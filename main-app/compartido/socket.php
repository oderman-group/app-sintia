<script src="https://cdn.socket.io/3.1.3/socket.io.min.js" integrity="sha384-cPwlPLvBTa3sKAgddT6krw0cJat7egBga3DJepJyrLl4Q9/5WLra3rrnMcyTyOnh" crossorigin="anonymous"></script>
<script>
	var socket = io('<?= URL_API ?>', {
		transports: ['websocket', 'polling', 'flashsocket']
	});
	var chat_remite_usuario = '<?= $idSession ?>';
	socket.emit('join', "sala_" + chat_remite_usuario);
	socket.on("ver_noticia_<?= $config['conf_id_institucion']?>", async (body) => {
		console.log(body);
		abrirModal("Nueva Noticia", "../compartido/noticia-modal.php", body);
	});
	socket.on("ver_noticia", async (body) => {
		console.log(body);
		abrirModal("Nueva Noticia", "../compartido/noticia-modal.php", body);
	});
</script>