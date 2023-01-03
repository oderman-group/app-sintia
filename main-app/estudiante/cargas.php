<?php include("session.php");?>
<?php
$_SESSION["bd"] = date("Y");
?>
<?php include("verificar-usuario.php");?>
<?php include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'ES0010';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php
if(isset($_GET["carga"]) and $_GET["carga"]!="" and is_numeric($_GET["carga"])){
	setcookie("cargaE",$_GET["carga"]);
	setcookie("periodoE",$_GET["periodo"]);
	
	$enlaceNext = 'calificaciones.php';
	if($config['conf_sin_nota_numerica']==1){$enlaceNext = 'calificaciones.php';}
	echo '<script type="text/javascript">window.location.href="'.$enlaceNext.'?carga='.$_GET["carga"].'&periodo='.$_GET["periodo"].'";</script>';
	exit();
}
?>

<?php
if($config['conf_activar_encuesta']==1){
	$respuesta = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM general_encuestas 
	WHERE genc_estudiante='".$datosEstudianteActual['mat_id']."'"));
	if($respuesta==0 and $datosEstudianteActual[6]!=11){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=214";</script>';
		exit();	
	}
}
?>

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
                                <div class="page-title"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                   
						<?php
						$cCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas 
						INNER JOIN academico_materias ON mat_id=car_materia
						INNER JOIN academico_grados ON gra_id=car_curso
						INNER JOIN usuarios ON uss_id=car_docente
						WHERE car_curso='".$datosEstudianteActual[6]."' AND car_grupo='".$datosEstudianteActual[7]."'");
						$nCargas = mysqli_num_rows($cCargas);
						$mensajeCargas = new Cargas;
						$mensajeCargas->verificarNumCargas($nCargas);
						?>

					
                   
                     <!-- start course list -->
                     <div class="row">
									<?php
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
										//Verificar si el estudiante está matriculado en cursos de extensión o complementarios
										if($rCargas['car_curso_extension']==1){
											$cursoExt = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM academico_cargas_estudiantes WHERE carpest_carga='".$rCargas['car_id']."' AND carpest_estudiante='".$datosEstudianteActual['mat_id']."' AND carpest_estado=1"));
											if($cursoExt==0){continue;}
										}
									    
										$ultimoAcceso = 'Nunca';
										$fondoCargaActual = '#FFF';
										$cargaHistorial = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_cargas_acceso 
										WHERE carpa_id_carga='".$rCargas[0]."' AND carpa_id_estudiante='".$datosEstudianteActual['mat_id']."'"), MYSQLI_BOTH);
										if($cargaHistorial['carpa_id']!=""){
											$ultimoAcceso = "(".$cargaHistorial['carpa_cantidad'].") ".$cargaHistorial['carpa_ultimo_acceso'];
										}
										if($rCargas[0]==$_COOKIE["cargaE"]){
											$fondoCargaActual = 'cornsilk';
										}
										//PLAN DE CLASE
										$Cpc = mysqli_query($conexion, "SELECT * FROM academico_pclase WHERE pc_id_carga='".$rCargas[0]."' AND pc_periodo='".$_COOKIE["periodoE"]."'");
									    $Rpc = mysqli_fetch_array($Cpc, MYSQLI_BOTH);
									    $Npc = mysqli_num_rows($Cpc);
										//DEFINITIVAS
										$carga = $rCargas[0];
										$periodo = $rCargas['car_periodo'];
										$estudiante = $datosEstudianteActual[0];
										include("../definitivas.php");
									?>
						 <div class="col-lg-3 col-md-6 col-12 col-sm-6"> 
							
							<div class="blogThumb" style="background-color:<?=$fondoCargaActual;?>;">
								<div class="thumb-center">
									<a href="cargas.php?carga=<?=$rCargas[0];?>&periodo=<?=$rCargas[5];?>"><img class="img-responsive" alt="user" src="../../config-general/assets/img/course/course1.jpg"></a>
								</div>
	                        	<div class="course-box">
	                        	<h4><a href="cargas.php?carga=<?=$rCargas[0];?>&periodo=<?=$rCargas[5];?>" style="text-decoration: underline;"><?=strtoupper($rCargas['mat_nombre']);?></a></h4>
		                            <div class="text-muted">
										<span class="m-r-10" style="font-size: 10px;"><?=$ultimoAcceso;?></span> 
										
		                            	<?php if($datosUsuarioActual['uss_bloqueado']!=1 and $config['conf_sin_nota_numerica']!=1){?><a class="course-likes m-l-10" href="#"> <?=$definitiva;?></a><?php }?>
										
		                            </div>
		                            <p><span><i class="fa fa-clock-o"></i> <?=$frases[101][$datosUsuarioActual['uss_idioma']];?>: <?=$rCargas['car_periodo'];?></span></p>
		                            <p><span><i class="fa  fa-user"></i> <?=$frases[28][$datosUsuarioActual['uss_idioma']];?>: <?=strtoupper($rCargas['uss_nombre']);?></span></p>
									
									<!--
		                            <a href="cargas.php?carga=<?=$rCargas[0];?>&periodo=<?=$rCargas[5];?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-info"><?=$frases[103][$datosUsuarioActual['uss_idioma']];?></a>
									-->
									
	                        	</div>
	                        </div>	
								 
                    	</div>
						 <?php }?> 
	                    
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