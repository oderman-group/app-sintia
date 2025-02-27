<style type="text/css">

		/* Para bloquear la pagina mientras carga un modal*/
#overlay {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.5); /* Fondo semitransparente */
	z-index: 9999;
	justify-content: center;
	align-items: center;
	flex-direction: column;
}

#loader {
	border: 6px solid #f3f3f3; /* Light gray */
	border-top: 6px solid #3498db; /* Blue */
	border-radius: 50%;
	width: 50px;
	height: 50px;
	animation: spin 2s linear infinite;
}

#loading-text {
	margin-top: 10px;
	color: white;
}

@keyframes spin {
	0% { transform: rotate(0deg); }
	100% { transform: rotate(360deg); }
}
</style>

<div id="overlay">
	<div id="loader"></div>
	<div id="loading-text">Cargando...</div>
</div>