<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0034';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>


    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							
                        </div>
                    </div>
					<?php include(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                    <?php include("includes/barra-superior-informacion-actual.php"); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
									
								<div class="col-md-12">
									<nav>
										<div class="nav nav-tabs" id="nav-tab" role="tablist">

											<a class="nav-item nav-link" id="nav-indicadores-tab" data-toggle="tab" href="#nav-indicadores" role="tab" aria-controls="nav-indicadores" aria-selected="true" onClick="listarInformacion('listar-indicadores.php', 'nav-indicadores')">Indicadores</a>

											<a class="nav-item nav-link" id="nav-notas-indicador-tab" data-toggle="tab" href="#nav-notas-indicador" role="tab" aria-controls="nav-notas-indicador" aria-selected="true" onClick="listarInformacion('listar-notas-indicadores.php', 'nav-notas-indicador')">Notas por indicador</a>

										</div>
									</nav>

									<div class="tab-content" id="nav-tabContent">
										
										<div class="tab-pane fade" id="nav-indicadores" role="tabpanel" aria-labelledby="nav-indicadores-tab"></div>

										<div class="tab-pane fade" id="nav-notas-indicador" role="tabpanel" aria-labelledby="nav-notas-indicador-tab"></div>

									</div>

                                </div>

								<script>
										document.addEventListener('DOMContentLoaded', function() {
											
											// Obtén la cadena de búsqueda de la URL
											var queryString = window.location.search;

											// Crea un objeto URLSearchParams a partir de la cadena de búsqueda
											var params = new URLSearchParams(queryString);
											var tab = params.get('tab');
											
											if ( tab == 2 ) {
												listarInformacion('listar-notas-indicadores.php', 'nav-notas-indicador');
												document.getElementById('nav-notas-indicador-tab').classList.add('active');
												document.getElementById('nav-notas-indicador').classList.add('show', 'active');
											}
											else {
												listarInformacion('listar-indicadores.php', 'nav-indicadores');
												document.getElementById('nav-indicadores-tab').classList.add('active');
												document.getElementById('nav-indicadores').classList.add('show', 'active');
											}

											
										});
									</script>
									<script type="text/javascript">
									function recargarInclude() {
										var xhr = new XMLHttpRequest();
										xhr.open("GET", "listar-indicadores-tbody.php", true);
										xhr.onreadystatechange = function() {
											if (xhr.readyState == 4 && xhr.status == 200) {
												document.getElementById("contenido-dinamico").innerHTML = xhr.responseText;
											}
										};
										xhr.send();
									}

									function generarIndicadores() {
										var cantidad = document.getElementById('maxidicadores').value;
										var asignatura = document.getElementById('asignatura').value;
										var curso = document.getElementById('curso').value;
										if (parseInt(cantidad) > 0 && parseInt(cantidad) < 8) {
											var buscar = "regalame una lista de " + cantidad + " indicadores para la asignatura " + asignatura + " del curso " + curso + " en colombia el resultado en formato JSON, con un nodo con nombre indicadores y cada indicador en un nodo con nombre descripcion";
											document.getElementById("gifCarga").style.display = "block";
											var data = {
												'metodo': '<?php echo TEXT_TO_TEXT ?>',
												'valor': buscar
											};
											console.log(buscar);
											fetch('../openAi/metodos.php', {
													method: 'POST', // or 'PUT'
													body: JSON.stringify(data), // data can be `string` or {object}!
													headers: {
														'Content-Type': 'application/json'
													},
												})
												.then((res) => res.json())
												.catch((error) => console.error('Error:', error))
												.then(
													function(response) {
														{
															console.log(response);
															if (response["ok"]) {
																var data = {
																	'valor': response["valor"]
																};
																fetch('indicadores-guardar-fetch.php', {
																	method: 'POST', // or 'PUT'
																	body: JSON.stringify(data), // data can be `string` or {object}!
																	headers: {
																		'Content-Type': 'text/html'
																	},
																}).then(
																	function(response) {
																		console.log(response);
																		recargarInclude();
																																			
																		document.getElementById("porcentajeActual").innerText= "100%";
																		document.getElementById("gifCarga").style.display = "none";
																	}
																);
															}
														};
													});

										}else{
											Swal.fire({
													position: "top-end",
													icon: "warning",
													title: 'El rango  para crear indicadores es de una cantidad entre 0 a 8',
													showConfirmButton: false,
													timer: 150000
												});
										}


									}
								</script>
							
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    <!-- start js include path -->
	<script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js"></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js"></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
	
</body>

</html>