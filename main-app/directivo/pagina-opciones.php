<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0108';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
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
                                <div class="page-title"><?=strtoupper($datosCargaActual['mat_nombre']." (".$datosCargaActual['gra_nombre']." ".$datosCargaActual['gru_nombre'].")");?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                   
                   
                     <!-- start course list -->
                     <div class="row">
									
						 <div class="col-lg-3 col-md-6 col-12 col-sm-6"> 
							<div class="blogThumb">
								<div class="thumb-center"><a href="indicadores.php"><img class="img-responsive" alt="user" src="../files/modulos/mod2.jpg"></a></div>
	                        </div>	
                    	</div>
						 
						<div class="col-lg-3 col-md-6 col-12 col-sm-6"> 
							<div class="blogThumb">
								<div class="thumb-center"><a href="calificaciones.php"><img class="img-responsive" alt="user" src="../files/modulos/mod1.jpg"></a></div>
	                        </div>	
                    	</div>
						
						<div class="col-lg-3 col-md-6 col-12 col-sm-6"> 
							<div class="blogThumb">
								<div class="thumb-center"><a href="periodos-resumen.php"><img class="img-responsive" alt="user" src="../files/modulos/mod3.jpg"></a></div>
	                        </div>	
                    	</div>
						 
						<div class="col-lg-3 col-md-6 col-12 col-sm-6"> 
							<div class="blogThumb">
								<div class="thumb-center"><a href="#"><img class="img-responsive" alt="user" src="../files/modulos/mod4.jpg"></a></div>
	                        </div>	
                    	</div>
						  
	                    
			        </div>
			        <!-- End course list -->
			        
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
    <script src="../../config-general/assets/plugins/sparkline/jquery.sparkline.js" ></script>
	<script src="../../config-general/assets/js/pages/sparkline/sparkline-data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
    <script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
    <!-- material -->
    <script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- chart js -->
    <script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js" ></script>
    <script src="../../config-general/assets/plugins/chart-js/utils.js" ></script>
    <script src="../../config-general/assets/js/pages/chart/chartjs/home-data.js" ></script>
    <!-- summernote -->
    <script src="../../config-general/assets/plugins/summernote/summernote.js" ></script>
    <script src="../../config-general/assets/js/pages/summernote/summernote-data.js" ></script>
    <!-- end js include path -->
  </body>

</html>