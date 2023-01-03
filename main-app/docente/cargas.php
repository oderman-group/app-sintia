<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0033';?>
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
                                <div class="page-title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?> (<a href="cargas-general.php" style="text-decoration: underline;">Ir a vista general</a>)</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                   
                   
                     <!-- start course list -->
                     <div class="row">
						 
						 <div class="col-sm-12">

							 <?php
							 $cCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas 
							 INNER JOIN academico_materias ON mat_id=car_materia
							 INNER JOIN academico_grados ON gra_id=car_curso
							 INNER JOIN academico_grupos ON gru_id=car_grupo
							 WHERE car_docente='".$_SESSION["id"]."'
							 ORDER BY car_posicion_docente, car_curso, car_grupo, mat_nombre
							 ");
							  $cargasCont = 1;
							 $nCargas = mysqli_num_rows($cCargas);
							 $mensajeCargas = new Cargas;
							 $mensajeCargas->verificarNumCargas($nCargas);
							 ?>
								
							 <p>
								 	<a href="../compartido/planilla-docentes.php?docente=<?=$_SESSION["id"];?>" target="_blank" style="color: blue; text-decoration: underline;">Imprimir todas mis planillas</a>
							 </p>
							 <div class="row">
								 
								 
								 
									<?php
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
									    $ultimoAcceso = 'Nunca';
										$fondoCargaActual = '#FFF';

										if($rCargas['car_ultimo_acceso_docente']!=""){$ultimoAcceso = $rCargas['car_ultimo_acceso_docente'];}
										if($rCargas[0]==$_COOKIE["carga"]){$fondoCargaActual = 'cornsilk';}
										
										$cargaSP = $rCargas["car_id"];
										$periodoSP = $rCargas["car_periodo"];
										include("../suma-porcentajes.php");
										
										if($rCargas["car_periodo"]>$rCargas["gra_periodos"]){
											$mensajeI = "<span style='color:blue;'>Terminado</span>";
										  }else{
											  if($spcr[0]<96){
													$mensajeI = $spcr[0];
											  }elseif($rCargas["car_permiso1"]==0){
												$mensajeI = 'Sin permiso para generar';
											  }else{
												  $mensajeI = '<a href="../compartido/generar-informe.php?carga='.$rCargas["car_id"].'&periodo='.$rCargas["car_periodo"].'&grado='.$rCargas["car_curso"].'&grupo='.$rCargas["car_grupo"].'" class="btn red">Generar Informe</a>';
											  }	
										}
										
										$induccionEntrar = '';
										$induccionSabanas = '';
										if($cargasCont == 1){
											$induccionEntrar = 'data-hint="Haciendo click sobre el nombre o sobre la imagen puedes entrar a administrar esta carga académica."';
											$induccionSabanas = 'data-hint="Puedes ver las sábanas de cada uno de los periodos pasados."';
										}
									?>
						 <div class="col-lg-3 col-md-6 col-12 col-sm-6"> 
							<div class="blogThumb" style="background-color:<?=$fondoCargaActual;?>;">
								<div class="thumb-center">
									<a href="guardar.php?get=100&carga=<?=$rCargas[0];?>&periodo=<?=$rCargas[5];?>" title="Entrar">
										<img class="img-responsive" alt="user" src="../../config-general/assets/img/course/course1.jpg">
									</a>	
								</div>
	                        	<div class="course-box">
	                        	<h4 <?=$induccionEntrar;?>><a href="guardar.php?get=100&carga=<?=$rCargas[0];?>&periodo=<?=$rCargas[5];?>" title="Entrar" style="text-decoration: underline;"><?="[".$rCargas['car_id']."] ".strtoupper($rCargas['mat_nombre']);?></a></h4>
		                            
									<p>
										<a href="../compartido/planilla-docentes.php?carga=<?=$rCargas['car_id'];?>" title="Planilla" target="_blank"><img src="../files/iconos/emblem-library.png" width="25"></a>&nbsp;
										<span><i class="fa  fa-group"></i> <b><?=$frases[164][$datosUsuarioActual[8]];?>:</b> <?=strtoupper($rCargas['gra_nombre']." ".$rCargas['gru_nombre']);?></span>
									</p>
									
									
									<p align="center" <?=$induccionSabanas;?>>
                                      	<?php for($i=1; $i<$rCargas["car_periodo"]; $i++){?><a href="../compartido/informes-generales-sabanas.php?curso=<?=$rCargas["car_curso"];?>&grupo=<?=$rCargas["car_grupo"];?>&per=<?=$i;?>" target="_blank" style="text-decoration:underline; color:#00F;" title="Sabanas"><?=$i;?></a>&nbsp;&nbsp;&nbsp;&nbsp;<?php }?>
                                    </p>
									
		                            
									<!--
									<p><span><i class="fa fa-clock-o"></i> <b><?=$frases[101][$datosUsuarioActual[8]];?>:</b> <?=$rCargas['car_periodo'];?></span></p>
									
									
									<p><span><i class="fa fa-hourglass-half"></i> <b>Notas declaradas:</b> </span></p>
									<p><span><i class="fa fa-hourglass-half"></i> <b>Notas registradas:</b> </span></p>
									-->

									<!--
		                            <div align="center">
										<a href="guardar.php?get=100&carga=<?=$rCargas[0];?>&periodo=<?=$rCargas[5];?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-info"><?=$frases[103][$datosUsuarioActual['uss_idioma']];?></a>
									</div>
									-->
									
									<div class="text">
										<span class="m-r-10" style="font-size: 10px;"><b>Notas:</b> <?=$spcd[0];?>% / <?=$spcr[0];?>% | <b>Periodo:</b> <?=$rCargas['car_periodo'];?> | <b>Posición:</b> <?=$rCargas['car_posicion_docente'];?></span> 

		                            	<?php if($rCargas['car_director_grupo']==1){?><br><a class="course-likes m-l-10" style="color: slateblue;"><i class="fa fa-user-circle-o"></i> Director de grupo</a><?php }?>
		                            </div>
									
									<p><?=$mensajeI;?></p>
									
	                        	</div>
	                        </div>	
                    	</div>
						 <?php 
								$cargasCont ++;
							}
						 ?>
						
							 </div>
						</div>		 
	                    
			        </div>
					
					<div class="row">
						 
						 <div class="col-sm-12">
						 	<?php include("../compartido/progreso-docentes.php");?>
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