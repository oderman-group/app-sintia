<?php include("session.php");?>
<?php $idPaginaInterna = 'AC0029';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
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
                                <div class="page-title">SINTIA Marketplace</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
								<p><i class="fa fa-thumbs-o-up"></i> <?=$frases[176][$datosUsuarioActual[8]];?></p>
                            </div>
                        </div>
                    </div>
                   
                   
                     <!-- start course list -->
                     <div class="row">
						 <div class="col-sm-3">
							 	<!--
							 	<div class="panel">
									<header class="panel-heading panel-heading-green"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                      <div class="panel-body">
										<p><?=$frases[177][$datosUsuarioActual[8]];?></p>
									    <p><?=$frases[178][$datosUsuarioActual[8]];?></p>
									  </div>
								</div>-->
							 
							 
							 	<p align="center">
									<a href="empresas-agregar.php" class="btn btn-danger btn-lg"><i class="fa fa-shopping-cart"></i> VENDE TUS PRODUCTOS AQUÍ </a>
							 	</p>
						 	
							 	<div class="panel" data-hint="Haciendo click sobre cualquier opción se filtrará la información y sólo te aparecerán los servicios de la categoría seleccionada.">
										<header class="panel-heading panel-heading-purple"><?=$frases[179][$datosUsuarioActual[8]];?></header>
										<div class="panel-body">
											<?php
											$categorias = mysqli_query($conexion, "SELECT * FROM ".$baseDatosMarketPlace.".servicios_categorias");
											while($cat = mysqli_fetch_array($categorias, MYSQLI_BOTH)){
												if($cat['svcat_id']==$_GET["cat"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p>
													<a href="<?=$_SERVER['PHP_SELF'];?>?cat=<?=$cat['svcat_id'];?>" <?=$estiloResaltado;?>>
														<i class="fa <?=$cat['svcat_icon'];?>"></i>
														<span <?=$estiloDG;?>><?=strtoupper($cat['svcat_nombre']);?></span>
													</a>
												</p>
											<?php }?>
												<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>"><?=strtoupper($frases[180][$datosUsuarioActual[8]]);?></span></a></p>
										</div>
                                    </div>
					
									
                            		
									<?php include("../compartido/publicidad-lateral.php");?>
								
                        	
							 
						 </div>
						 
						 <div class="col-sm-9">
							<div class="row"> 
						<?php
						$filtro = '';
						if($_GET["cat"]!=""){$filtro .= " AND excat_categoria='".$_GET["cat"]."'";}
						if(is_numeric($_GET["emp"])){$filtro .= " AND emp_id='".$_GET["emp"]."'";}
						$serviciosConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosMarketPlace.".empresas_categorias
						INNER JOIN ".$baseDatosMarketPlace.".empresas ON emp_id=excat_empresa AND emp_estado=1
						INNER JOIN ".$baseDatosMarketPlace.".servicios_categorias ON svcat_id=excat_categoria AND svcat_activa=1
						WHERE excat_id=excat_id $filtro
						");
						while($datosConsulta = mysqli_fetch_array($serviciosConsulta, MYSQLI_BOTH)){
							$logo = 'course3.jpg';
							if($datosConsulta['emp_logo']!=""){$logo = $datosConsulta['emp_logo'];}
						?>
						 <div class="col-lg-3 col-md-6 col-12 col-sm-6"> 
							<div class="blogThumb">
								<div class="thumb-center"><img class="img-responsive" alt="user" src="http://plataformasintia.com/files-general/empresas/<?=$logo;?>" width="640" height="421"></div>
	                        	<div class="course-box">
	                        	<h4><?=strtoupper($datosConsulta['emp_nombre']);?></h4>
		                            <div class="text-muted">
										<span class="m-r-10" style="font-size: 10px;"><i class="fa <?=$datosConsulta['svcat_icon'];?>"></i> <?=$datosConsulta['svcat_nombre'];?></span> 
		                            	<?php if($datosConsulta['emp_verificada']==1){?><a class="course-likes m-l-10" style="color: slateblue;" title="<?=$frases[185][$datosUsuarioActual[8]];?>"><i class="fa fa-check-circle"></i></a><?php }?>
		                            </div>
									<?php 
									if(is_numeric($_GET["emp"])){
										$consultaVisita = mysqli_query($conexion, "SELECT * FROM ".$baseDatosMarketPlace.".empresas_visitas 
										WHERE exvis_empresa='".$_GET["emp"]."' AND exvis_usuario='".$_SESSION["id"]."' AND exvis_institucion='".$config['conf_id_institucion']."'");
										
										$numVisita = mysqli_num_rows($consultaVisita);
										$datoVisita = mysqli_fetch_array($consultaVisita, MYSQLI_BOTH);
										if($numVisita>0){
											mysqli_query($conexion, "UPDATE ".$baseDatosMarketPlace.".empresas_visitas SET exvis_cantidad=exvis_cantidad+1 
											WHERE exvis_usuario='".$_SESSION["id"]."' AND exvis_institucion='".$config['conf_id_institucion']."'");
											
										}else{
											mysqli_query($conexion, "INSERT INTO ".$baseDatosMarketPlace.".empresas_visitas(exvis_empresa, exvis_institucion, exvis_usuario, exvis_fecha, exvis_cantidad)VALUES('".$_GET["emp"]."', '".$config['conf_id_institucion']."', '".$_SESSION["id"]."', now(), 1)");
											
										}
									?>
										<p><span><i class="fa fa-envelope-o"></i> <b><?=$frases[181][$datosUsuarioActual[8]];?>:</b> <?=$datosConsulta['emp_email'];?></span></p>
										<p><span><i class="fa fa-phone"></i> <b><?=$frases[182][$datosUsuarioActual[8]];?>:</b> <?=$datosConsulta['emp_telefono'];?></span></p>
										<a href="servicios.php" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i> <?=$frases[184][$datosUsuarioActual[8]];?></a>
										<?php if($datosConsulta['emp_web']!=""){?>
											<a href="<?=$datosConsulta['emp_web'];?>" target="_blank" class="btn btn-primary"><i class="fa fa-globe"></i> <?=$frases[183][$datosUsuarioActual[8]];?></a>
										<?php }?>
									<?php }else{?>
										<p><span><i class="fa fa-envelope-o"></i> <b><?=$frases[181][$datosUsuarioActual[8]];?>:</b> <?=substr($datosConsulta['emp_email'],0,5);?>**@****</span></p>
										<p><span><i class="fa fa-phone"></i> <b><?=$frases[182][$datosUsuarioActual[8]];?>:</b> <?=substr($datosConsulta['emp_telefono'],0,3);?>*******</span></p>
										<a href="servicios.php?emp=<?=$datosConsulta['emp_id'];?>" class="btn btn-info"><i class="fa fa-search-plus"></i> <?=$frases[154][$datosUsuarioActual[8]];?></a>
									<?php }?>
									
		                            
									
	                        	</div>
	                        </div>	
                    	</div>
					<?php }?>
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