<?php include("session.php");?>
<?php $idPaginaInterna = 'ES0055';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
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
                                <div class="page-title">Como Empezar</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">

                        <div class="col-md-12">

                            <div class="row">
								
								<div class="col-md-8">
									<div class="panel">
										<header class="panel-heading panel-heading-blue"><?=$frases[357][$datosUsuarioActual['uss_idioma']];?></header>
										<div class="panel-body">
                                            <p>
                                                <b>1.</b> <?=$frases[358][$datosUsuarioActual['uss_idioma']];?><br>
                                                <mark><?=$frases[356][$datosUsuarioActual['uss_idioma']];?> -> <?=$frases[73][$datosUsuarioActual['uss_idioma']];?></mark>
                                            </p>

                                            <p>
                                                <b>2.</b> <?=$frases[359][$datosUsuarioActual['uss_idioma']];?><br>
                                                <mark><?=$frases[356][$datosUsuarioActual['uss_idioma']];?> -> <?=$frases[88][$datosUsuarioActual['uss_idioma']];?></mark>
                                            </p>

                                            <p>
                                                <b>3.</b> <?=$frases[360][$datosUsuarioActual['uss_idioma']];?><br>
                                                <mark><?=$frases[356][$datosUsuarioActual['uss_idioma']];?> -> (<?=$frases[60][$datosUsuarioActual['uss_idioma']];?>, <?=$frases[104][$datosUsuarioActual['uss_idioma']];?>, <?=$frases[105][$datosUsuarioActual['uss_idioma']];?>, <?=$frases[74][$datosUsuarioActual['uss_idioma']];?>)</mark>
                                            </p>
                                            
										</div>
                                	</div>
								</div>

                                <div class="col-md-4">
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[361][$datosUsuarioActual['uss_idioma']];?></header>
										<div class="panel-body">
                                            <div style="position: relative; padding-bottom: 56.25%; height: 0;"><iframe src="https://www.loom.com/embed/a1559aa348c6446cb002e212779252df" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe></div>
										</div>
                                	</div>
								</div>

									
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
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
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