<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0046';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");

$disabled = '';
if( !CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) ) { 
    $disabled = 'disabled';
}
?>
<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">

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
                                <div class="page-title"><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></div>
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

											<a class="nav-item nav-link" id="nav-clases-tab" data-toggle="tab" href="#nav-clases" role="tab" aria-controls="nav-clases" aria-selected="true" onClick="guardarTabActivoClases(1); listarInformacion('lista-clases.php', 'nav-clases')">Clases</a>

											<a class="nav-item nav-link" id="nav-unidades-tab" data-toggle="tab" href="#nav-unidades" role="tab" aria-controls="nav-unidades" aria-selected="true" onClick="guardarTabActivoClases(2); listarInformacion('lista-unidades-tematicas.php', 'nav-unidades')">Unidades temáticas</a>

											<a class="nav-item nav-link" id="nav-plan-clase-tab" data-toggle="tab" href="#nav-plan-clase" role="tab" aria-controls="plan-clase" aria-selected="false" onClick="guardarTabActivoClases(3);">Plan de clase</a>

											<?php if(isset($datosCargaActual) && $datosCargaActual['car_tematica'] == 1){?>
												<a class="nav-item nav-link" id="nav-tematica-tab" data-toggle="tab" href="#nav-tematica" role="tab" aria-controls="plan-tematica" aria-selected="false" onClick="guardarTabActivoClases(4); listarInformacion('lista-tematica.php', 'nav-tematica')">Temática del periodo</a>
											<?php }?>

										</div>
									</nav>

									<div class="tab-content" id="nav-tabContent">
										
										<div class="tab-pane fade" id="nav-clases" role="tabpanel" aria-labelledby="nav-clases-tab"></div>

										<div class="tab-pane fade" id="nav-unidades" role="tabpanel" aria-labelledby="nav-unidades-tab"></div>

										<div class="tab-pane fade" id="nav-plan-clase" role="tabpanel" aria-labelledby="plan-clase-tab">
											<?php include "includes/plan-clases.php"; ?>
										</div>

										<div class="tab-pane fade" id="nav-tematica" role="tabpanel" aria-labelledby="nav-tematica-tab"></div>

									</div>

									<script>
										// ============================================
										// PERSISTENCIA DE TABS CON LOCALSTORAGE (CLASES)
										// ============================================
										function guardarTabActivoClases(tabNumero) {
											localStorage.setItem('clases_tab_activo', tabNumero);
											
											// Actualizar URL sin recargar
											var url = new URL(window.location);
											url.searchParams.set('tab', tabNumero);
											window.history.pushState({}, '', url);
											
											console.log('✅ Tab de clases guardado en localStorage:', tabNumero);
										}
										
										function obtenerTabACargarClases() {
											// 1. Prioridad: parámetro URL
											var params = new URLSearchParams(window.location.search);
											var tabURL = params.get('tab');
											if (tabURL !== null) {
												console.log('📂 Tab de clases desde URL:', tabURL);
												localStorage.setItem('clases_tab_activo', tabURL);
												return parseInt(tabURL);
											}
											
											// 2. Fallback: localStorage
											var tabLocalStorage = localStorage.getItem('clases_tab_activo');
											if (tabLocalStorage !== null) {
												console.log('📂 Tab de clases desde localStorage:', tabLocalStorage);
												return parseInt(tabLocalStorage);
											}
											
											// 3. Default: Tab 1
											console.log('📂 Tab de clases por defecto: 1');
											return 1;
										}
										
										document.addEventListener('DOMContentLoaded', function() {
											console.log('🔵 Inicializando tabs de clases');
											
											var tabACargar = obtenerTabACargarClases();
											
											switch(tabACargar) {
												case 2:
													console.log('📂 Cargando tab 2');
													listarInformacion('lista-unidades-tematicas.php', 'nav-unidades');
													document.getElementById('nav-unidades-tab').classList.add('active');
													document.getElementById('nav-unidades').classList.add('show', 'active');
													break;
												case 3:
													console.log('📂 Cargando tab 3');
													document.getElementById('nav-plan-clase-tab').classList.add('active');
													document.getElementById('nav-plan-clase').classList.add('show', 'active');
													break;
												case 4:
													console.log('📂 Cargando tab 4');
													listarInformacion('lista-tematica.php', 'nav-tematica');
													document.getElementById('nav-tematica-tab').classList.add('active');
													document.getElementById('nav-tematica').classList.add('show', 'active');
													break;
												default:
													console.log('📂 Cargando tab 1');
													listarInformacion('lista-clases.php', 'nav-clases');
													document.getElementById('nav-clases-tab').classList.add('active');
													document.getElementById('nav-clases').classList.add('show', 'active');
													break;
											}
										});
									</script>
									

								</div>
								
							
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

	<!-- calendar -->
<script src="../../config-general/assets/plugins/moment/moment.min.js" ></script>
<script src="../../config-general/assets/plugins/fullcalendar/fullcalendar.min.js" ></script>

<?php include("calendario-js.php");?>
</body>

</html>