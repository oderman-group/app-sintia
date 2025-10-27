<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0035';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<style>
/* Overlay de bloqueo mientras se guarda */
#overlay-guardando-nota {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(30, 41, 59, 0.85);
	z-index: 99999;
	backdrop-filter: blur(6px);
}

.overlay-content-nota {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background: white;
	padding: 45px 50px;
	border-radius: 20px;
	text-align: center;
	box-shadow: 0 20px 60px rgba(0,0,0,0.4);
	min-width: 320px;
}

.overlay-content-nota .spinner {
	width: 70px;
	height: 70px;
	border: 5px solid #e2e8f0;
	border-top-color: #667eea;
	border-radius: 50%;
	margin: 0 auto 25px;
	animation: spin 1s linear infinite;
}

@keyframes spin {
	to { transform: rotate(360deg); }
}

.overlay-content-nota h3 {
	color: #2d3748;
	margin: 0 0 10px 0;
	font-size: 22px;
	font-weight: 700;
}

.overlay-content-nota p {
	color: #718096;
	margin: 0;
	font-size: 15px;
}
</style>

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>

<!-- Overlay de bloqueo mientras se guarda la nota -->
<div id="overlay-guardando-nota">
	<div class="overlay-content-nota">
		<div class="spinner"></div>
		<h3>💾 Guardando Nota...</h3>
		<p>Por favor espera, no cierres esta ventana</p>
	</div>
</div>

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
                                <div class="page-title"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></div>
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

											<a class="nav-item nav-link" id="nav-calificaciones-tab" data-toggle="tab" href="#nav-calificaciones" role="tab" aria-controls="nav-calificaciones" aria-selected="true" onClick="listarInformacion('listar-calificaciones.php', 'nav-calificaciones')">Calificaciones</a>

											<a class="nav-item nav-link" id="nav-calificaciones-todas-tab" data-toggle="tab" href="#nav-calificaciones-todas" role="tab" aria-controls="nav-calificaciones-todas" aria-selected="true" onClick="listarInformacion('listar-calificaciones-todas.php', 'nav-calificaciones-todas')">Resumen de notas</a>
											
											<?php if(isset($datosCargaActual) && $datosCargaActual['car_observaciones_boletin']==1){?>
												<a class="nav-item nav-link" id="nav-observaciones-tab" data-toggle="tab" href="#nav-observaciones" role="tab" aria-controls="nav-observaciones" aria-selected="true" onClick="listarInformacion('listar-observaciones.php', 'nav-observaciones')">Observaciones</a>
											<?php }?>

											<a class="nav-item nav-link" id="nav-periodos-resumen-tab" data-toggle="tab" href="#nav-periodos-resumen" role="tab" aria-controls="nav-periodos-resumen" aria-selected="true" onClick="listarInformacion('listar-periodos-resumen.php', 'nav-periodos-resumen')">Resumen por periodos</a>

										</div>
									</nav>

									<div class="tab-content" id="nav-tabContent">
										
										<div class="tab-pane fade" id="nav-calificaciones" role="tabpanel" aria-labelledby="nav-calificaciones-tab"></div>

										<div class="tab-pane fade" id="nav-calificaciones-todas" role="tabpanel" aria-labelledby="nav-calificaciones-todas-tab"></div>

										<div class="tab-pane fade" id="nav-observaciones" role="tabpanel" aria-labelledby="nav-observaciones-tab"></div>

										<div class="tab-pane fade" id="nav-periodos-resumen" role="tabpanel" aria-labelledby="nav-periodos-resumen-tab"></div>

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
												listarInformacion('listar-calificaciones-todas.php', 'nav-calificaciones-todas');
												document.getElementById('nav-calificaciones-todas-tab').classList.add('active');
												document.getElementById('nav-calificaciones-todas').classList.add('show', 'active');
											}
											else if ( tab == 3 ) {
												listarInformacion('listar-observaciones.php', 'nav-observaciones');
												document.getElementById('nav-observaciones-tab').classList.add('active');
												document.getElementById('nav-observaciones').classList.add('show', 'active');
											}
											else if ( tab == 4 ) {
												listarInformacion('listar-periodos-resumen.php', 'nav-periodos-resumen');
												document.getElementById('nav-periodos-resumen-tab').classList.add('active');
												document.getElementById('nav-periodos-resumen').classList.add('show', 'active');
											}
											else {
												listarInformacion('listar-calificaciones.php', 'nav-calificaciones');
												document.getElementById('nav-calificaciones-tab').classList.add('active');
												document.getElementById('nav-calificaciones').classList.add('show', 'active');
											}

											
										});
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
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
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