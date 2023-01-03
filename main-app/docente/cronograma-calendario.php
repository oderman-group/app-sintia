<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0015';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
	<!-- full calendar -->
    <link href="../../config-general/assets/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
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
                                <div class="page-title"><?=$frases[245][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="cronograma.php"><?=$frases[111][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[245][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-md-9 col-sm-12">
							
							<p>
							<?php
											if(
												($periodoConsultaActual<=$datosCargaActual['gra_periodos'] and ($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1)) 
																	
												or($periodoConsultaActual<=$datosCargaActual['gra_periodos'] and $porcentajeRestante>0)
												)
											{
											?>
											
													<div class="btn-group">
														<a href="cronograma-agregar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" id="addRow" class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
													</div>
													
													
											<?php
											}
											?>
							</p>
							
                             <div class="card-box">
                                 <div class="card-head">
                                     <header><?=$frases[245][$datosUsuarioActual['uss_idioma']];?></header>
                                 </div>
								 
								 
                                 <div class="card-body">
                                 	<div class="panel-body">
                                       <div id="calendar" class="has-toolbar"> </div>
                                    </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-3 col-sm-12">
                             
							 <!--
							 <div class="card-box">
                                 <div class="card-head">
                                     <header>Draggable Events</header>
                                 </div>
                                 <div class="card-body ">
                                 	<div id="external-events">
                                        <form class="inline-form">
                                            <input type="text" value="" class="form-control" placeholder="Event Title..." id="event_title" />
                                            <br/>
                                            <a href="javascript:;" id="event_add" class="btn deepPink-bgcolor"> Add Event </a>
                                        </form>
                                        <hr/>
                                        <div id="event_box" class="mg-bottom-10"></div>
                                        <label class="rt-chkbox rt-chkbox-single rt-chkbox-outline" for="drop-remove"> remove after drop
                                            <input type="checkbox" class="group-checkable" id="drop-remove" />
                                            <span></span>
                                        </label>
                                        <hr class="visible-xs" /> 
                                    </div>
                                 </div>
                             </div>
							 -->
							 
							 <?php include("../compartido/publicidad-lateral.php");?>
							 
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
    <script src="../../config-general/assets/plugins/jquery-ui/jquery-ui.min.js" ></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <!-- calendar -->
    <script src="../../config-general/assets/plugins/moment/moment.min.js" ></script>
    <script src="../../config-general/assets/plugins/fullcalendar/fullcalendar.min.js" ></script>
    <!--<script src="../../config-general/assets/js/pages/calendar/calendar.min.js" ></script>-->

	<?php include("calendario-js.php");?>
		
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/calendar.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:16 GMT -->
</html>