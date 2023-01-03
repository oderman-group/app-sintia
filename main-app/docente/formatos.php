<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0047';?>
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
                                <div class="page-title"><?=$frases[221][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<?php include("info-carga-actual.php");?>
							
									<?php include("filtros-cargas.php");?>
									
								</div>
									
								<div class="col-md-4 col-lg-6">
									<div class="card card-box">
										<div class="card-head">
											<header><?=$frases[221][$datosUsuarioActual['uss_idioma']];?></header>
											<div class="tools">
												<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
												<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
												<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
											</div>
										</div>
										<div class="card-body" id="line-parent">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
														<div class="btn-group">
															<a href="formatos-agregar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" id="addRow" class="btn deepPink-bgcolor"><?=$frases[231][$datosUsuarioActual[8]];?> <i class="fa fa-plus"></i></a>
														</div>
												</div>
											</div>
											
											<div class="panel-group accordion" id="accordion3">
												<?php
												  $consulta = mysqli_query($conexion, "SELECT * FROM academico_formatos WHERE form_carga='".$cargaConsultaActual."'");
												  while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													$consultaCantCategoria=mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones WHERE eva_formato='".$resultado['form_id']."'");
													$cantCategorias = mysqli_num_rows($consultaCantCategoria);
												 ?>
												  <div class="panel panel-default">
													  <div class="panel-heading panel-heading-gray">
														  <h4 class="panel-title">
															  <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse<?=$resultado['form_id'];?>"> 
																  <?=$resultado['form_nombre'];?> (<?=$cantCategorias;?>)
															  </a>
														  </h4>
													  </div>
													  <div id="collapse<?=$resultado['form_id'];?>" class="panel-collapse collapse">
														  <div class="panel-body">
															  
															  <p>
																  <b><?=$frases[222][$datosUsuarioActual[8]];?>:</b> <?=$cantCategorias;?>
															  </p>
															  
															  <p> 
																  <a class="btn" href="formatos-categorias.php?idF=<?=$resultado['form_id'];?>"><i class="fa fa-list"></i> <?=$frases[222][$datosUsuarioActual[8]];?></a>
																  <a class="btn" href="monitoreos-realizar.php?idF=<?=$resultado['form_id'];?>"><i class="fa fa-check-square-o"></i> <?=$frases[225][$datosUsuarioActual[8]];?></a>
																  <a class="btn blue" href="formatos-editar.php?idR=<?=$resultado['form_id'];?>"><i class="fa fa-edit"></i></a>
																  <a class="btn red" href="#" name="guardar.php?get=30&idR=<?=$resultado['form_id'];?>" onClick="deseaEliminar(this)"><i class="fa fa-trash"></i></a>
															  </p>
															  
														  </div>
													  </div>
												  </div>
												<?php }?>
												
											  </div>
										</div>
									</div>
                                </div>
								
								<div class="col-md-4 col-lg-3">
									<?php include("../compartido/publicidad-lateral.php");?>
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
</body>

</html>