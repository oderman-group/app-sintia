<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0055';?>
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
                   
					
					<script>
							function url(url){
								location.href = url;
							}
						</script>
					
					<div class="row">
						
								<?php if($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null){?>
						        <div class="col-xl-3 col-md-6 col-12" onClick="url('indicadores.php')" style="cursor: pointer;">
						          <div class="info-box bg-b-green">
						            <span class="info-box-icon push-bottom"><i class="fa fa-file-text"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Indicadores/Logros</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>
								<?php }else{?>


									<div class="col-xl-3 col-md-6 col-12" onClick="url('../compartido/planilla-definitivas-docentes.php?curso=<?=$datosCargaActual['car_curso'];?>&grupo=<?=$datosCargaActual['car_grupo'];?>&per=<?=$periodoConsultaActual;?>')" style="cursor: pointer;">
						          <div class="info-box bg-b-green">
						            <span class="info-box-icon push-bottom"><i class="fa fa-file-text"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Planilla definitivas</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>

								<?php }?>	
								
						
						        <div class="col-xl-3 col-md-6 col-12" onClick="url('calificaciones.php')" style="cursor: pointer;">
						          <div class="info-box bg-b-yellow">
						            <span class="info-box-icon push-bottom"><i class="fa fa-check-square-o"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Calificaciones</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>
						      
					
								<div class="col-xl-3 col-md-6 col-12" onClick="url('calificaciones-todas.php')" style="cursor: pointer;">
						          <div class="info-box bg-b-blue">
						            <span class="info-box-icon push-bottom"><i class="fa fa-check-square"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Resumen de notas</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>
					
					
								<div class="col-xl-3 col-md-6 col-12" onClick="url('clases.php')" style="cursor: pointer;">
						          <div class="info-box bg-b-pink">
						            <span class="info-box-icon push-bottom"><i class="fa fa-file-movie-o"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Clases</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>
					</div>
					
					
					<div class="row">
						
						        <div class="col-xl-3 col-md-6 col-12" onClick="url('evaluaciones.php')" style="cursor: pointer;">
						          <div class="info-box bg-purple">
						            <span class="info-box-icon push-bottom"><i class="fa fa-laptop"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Evaluaciones virtuales</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>

								
						
						        <div class="col-xl-3 col-md-6 col-12" onClick="url('actividades.php')" style="cursor: pointer;">
						          <div class="info-box bg-orange">
						            <span class="info-box-icon push-bottom"><i class="fa fa-tasks"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Tareas en casa</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>
						      
					
								<div class="col-xl-3 col-md-6 col-12" onClick="url('foros.php')" style="cursor: pointer;">
						          <div class="info-box bg-success">
						            <span class="info-box-icon push-bottom"><i class="fa fa-comments-o"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Foros</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>
					
					
								<div class="col-xl-3 col-md-6 col-12" onClick="url('periodos-resumen.php')" style="cursor: pointer;">
						          <div class="info-box bg-blue">
						            <span class="info-box-icon push-bottom"><i class="fa fa-bar-chart-o"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Resumen por periodos</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>
					</div>
					
					
					<div class="row">
						
						        <div class="col-xl-3 col-md-6 col-12" onClick="url('cargas-carpetas.php')" style="cursor: pointer;">
						          <div class="info-box bg-danger">
						            <span class="info-box-icon push-bottom"><i class="fa fa-folder"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Carpetas</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>

								
						
						        <div class="col-xl-3 col-md-6 col-12" onClick="url('estudiantes.php')" style="cursor: pointer;">
						          <div class="info-box bg-primary">
						            <span class="info-box-icon push-bottom"><i class="fa fa-group"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Estudiantes</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>
						      
					
								<div class="col-xl-3 col-md-6 col-12" onClick="url('cronograma.php')" style="cursor: pointer;">
						          <div class="info-box bg-warning">
						            <span class="info-box-icon push-bottom"><i class="fa fa-calendar"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Cronograma</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
						            </div>
						          </div>
						        </div>
					
					
								<div class="col-xl-3 col-md-6 col-12" onClick="url('importar-info.php')" style="cursor: pointer;">
						          <div class="info-box bg-info">
						            <span class="info-box-icon push-bottom"><i class="fa fa-cloud-upload"></i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Importar información</span>
										<span class="info-box-number">&nbsp;</span>
						              	<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
						              	<span class="progress-description">&nbsp;</span>
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