<?php include_once("session.php");?>
<?php $idPaginaInterna = 'AC0003';?>
<?php include_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");?>
<?php include_once(ROOT_PATH."/main-app/compartido/head.php");?>

</head>
 <!-- END HEAD -->
<?php include_once(ROOT_PATH."/main-app/compartido/body.php");?>

    <div class="page-wrapper">
		
        <?php include_once(ROOT_PATH."/main-app/compartido/encabezado.php");?>
		
        <?php include_once(ROOT_PATH."/main-app/compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			
			<?php include_once(ROOT_PATH."/main-app/compartido/menu.php");?>
			
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
					<!--
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">SINTIA Marketplace</div>
								<?php //include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>-->
					
					<?php include_once(ROOT_PATH."/main-app/compartido/marketplace-contenido.php");?>
			        
                </div>
            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include_once(ROOT_PATH."/main-app/compartido/footer.php");?>
    </div>
    <!-- start js include path -->
    <script src="<?=BASE_URL;?>/config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="<?=BASE_URL;?>/config-general/assets/plugins/popper/popper.js" ></script>
    <script src="<?=BASE_URL;?>/config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="<?=BASE_URL;?>/config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="<?=BASE_URL;?>/config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="<?=BASE_URL;?>/config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <script src="<?=BASE_URL;?>/config-general/assets/plugins/sparkline/jquery.sparkline.js" ></script>
	<script src="<?=BASE_URL;?>/config-general/assets/js/pages/sparkline/sparkline-data.js" ></script>
    <!-- Common js-->
	<script src="<?=BASE_URL;?>/config-general/assets/js/app.js" ></script>
    <script src="<?=BASE_URL;?>/config-general/assets/js/layout.js" ></script>
    <script src="<?=BASE_URL;?>/config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="<?=BASE_URL;?>/config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="<?=BASE_URL;?>/config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
    <!-- material -->
    <script src="<?=BASE_URL;?>/config-general/assets/plugins/material/material.min.js"></script>
    <!-- chart js -->
    <script src="<?=BASE_URL;?>/config-general/assets/plugins/chart-js/Chart.bundle.js" ></script>
    <script src="<?=BASE_URL;?>/config-general/assets/plugins/chart-js/utils.js" ></script>
    <script src="<?=BASE_URL;?>/config-general/assets/js/pages/chart/chartjs/home-data.js" ></script>
    <!-- summernote -->
    <script src="<?=BASE_URL;?>/config-general/assets/plugins/summernote/summernote.js" ></script>
    <script src="<?=BASE_URL;?>/config-general/assets/js/pages/summernote/summernote-data.js" ></script>

    <!-- end js include path -->
  </body>

</html>