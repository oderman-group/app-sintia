<?php include("session.php");?>
<?php
// $_SESSION["bd"] = date("Y");
?>
<?php include("verificar-usuario.php");?>
<?php include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'ES0010';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php require_once("../class/servicios/CargaServicios.php"); ?>
<?php require_once("../class/servicios/MediaTecnicaServicios.php"); ?>
<?php require_once("../class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php"); ?>
<?php
$cargaE="";
if(!empty($_GET["carga"])){ $cargaE=base64_decode($_GET["carga"]);}

$periodoE="";
if(!empty($_GET["periodo"])){ $periodoE=base64_decode($_GET["periodo"]);}

if(!empty($cargaE)){
	setcookie("cargaE",$cargaE);
	setcookie("periodoE",$periodoE);
	
	$enlaceNext = 'calificaciones.php';
	if($config['conf_sin_nota_numerica']==1){
		$enlaceNext = 'matricula.php';
	}
	if($config['conf_mostrar_calificaciones_estudiantes']!=1){
		$enlaceNext = 'matricula.php';
	}
	echo '<script type="text/javascript">window.location.href="'.$enlaceNext.'?carga='.base64_encode($cargaE).'&periodo='.base64_encode($periodoE).'";</script>';
	exit();
}
?>

<?php
if($config['conf_activar_encuesta']==1){
	$respuesta = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_encuestas 
	WHERE genc_estudiante='".$datosEstudianteActual['mat_id']."' AND genc_institucion={$config['conf_id_institucion']} AND genc_year={$_SESSION["bd"]}"));
	if($respuesta==0 and $datosEstudianteActual['mat_grado']!=11){
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
							$cCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosEstudianteActual['mat_grado'], $datosEstudianteActual['mat_grupo']);
							$nCargas = mysqli_num_rows($cCargas);
							$mensajeCargas = new Cargas;
							$mensajeCargas->verificarNumCargas($nCargas, $datosUsuarioActual['uss_idioma']);
						?>
                     <!-- start course list -->
                     <div class="row">
									<?php
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
										//Verificar si el estudiante está matriculado en cursos de extensión o complementarios
										if($rCargas['car_curso_extension']==1){
											$cursoExt = CargaAcademica::validarCursosComplementario($conexion, $config, $datosEstudianteActual['mat_id'], $rCargas['car_id']);
											if($cursoExt==0){continue;}
										}
									    
										$ultimoAcceso = 'Nunca';
										$fondoCargaActual = '#FFF';
										
										$consultaHistorial = CargaAcademica::accesoCargasEstudiante($conexion, $config, $rCargas['car_id'], $datosEstudianteActual['mat_id']);
										$cargaHistorial = mysqli_fetch_array($consultaHistorial, MYSQLI_BOTH);
										if(!empty($cargaHistorial['carpa_id'])){
											$ultimoAcceso = "(".$cargaHistorial['carpa_cantidad'].") ".$cargaHistorial['carpa_ultimo_acceso'];
										}
										if(!empty($_COOKIE["cargaE"]) && $rCargas['car_id']==$_COOKIE["cargaE"]){
											$fondoCargaActual = 'cornsilk';
										}
										//DEFINITIVAS
										$carga = $rCargas['car_id'];
										$periodo = $rCargas['car_periodo'];
										$estudiante = $datosEstudianteActual['mat_id'];
										include("../definitivas.php");

										$definitivaFinal=$definitiva;
										if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
											$estiloNotaDefinitiva = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
											$definitivaFinal= !empty($estiloNotaDefinitiva['notip_nombre']) ? $estiloNotaDefinitiva['notip_nombre'] : "";
										}
									?>
						 <div class="col-lg-3 col-md-6 col-12 col-sm-6"> 
							
							<div class="blogThumb" style="background-color:<?=$fondoCargaActual;?>;">
								<div class="thumb-center">
									<a href="cargas.php?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($rCargas['car_periodo']);?>"><img class="img-responsive" alt="user" src="../../config-general/assets/img/course/course1.jpg"></a>
								</div>
	                        	<div class="course-box">
	                        	<h4><a href="cargas.php?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($rCargas['car_periodo']);?>" style="text-decoration: underline;"><?=strtoupper($rCargas['mat_nombre']);?></a></h4>
		                            <div class="text-muted">
										<span class="m-r-10" style="font-size: 10px;"><?=$ultimoAcceso;?></span> 
										
		                            	<?php if($datosUsuarioActual['uss_bloqueado']!=1 and $config['conf_sin_nota_numerica']!=1){?><a class="course-likes m-l-10" href="#"> <?=$definitivaFinal;?></a><?php }?>
										
		                            </div>
		                            <p><span><i class="fa fa-clock-o"></i> <?=$frases[101][$datosUsuarioActual['uss_idioma']];?>: <?=$rCargas['car_periodo'];?></span></p>
		                            <p><span><i class="fa  fa-user"></i> <?=$frases[28][$datosUsuarioActual['uss_idioma']];?>: <?=UsuariosPadre::nombreCompletoDelUsuario($rCargas);?></span></p>
									
									<!--
		                            <a href="cargas.php?carga=<?=$rCargas['car_id'];?>&periodo=<?=$rCargas['car_periodo'];?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-info"><?=$frases[103][$datosUsuarioActual['uss_idioma']];?></a>
									-->
									
	                        	</div>
	                        </div>	
								 
                    	</div>
						 <?php }?> 
	                    
			        </div>
			        <!-- End course list -->
			        <?php if (array_key_exists(10, $arregloModulos)) { ?>
						<?php
						$parametros = [
							'matcur_id_matricula' => $datosEstudianteActual["mat_id"],
							'matcur_id_institucion' => $config['conf_id_institucion'],
							'matcur_years' => $config['conf_agno']
						];
						$listaCursosMediaTecnica = MediaTecnicaServicios::listar($parametros);
						if(!empty($listaCursosMediaTecnica)){ echo '<hr  noshade="noshade" size="3" width="100%" />';
						foreach ($listaCursosMediaTecnica as $dato) {
							$cursoMediaTecnica = GradoServicios::consultarCurso($dato["matcur_id_curso"]); ?>			

							<div class="row">
								<div class="col-12">
									<div class="page-title"><?= $cursoMediaTecnica["gra_nombre"]; ?></div>
								</div>
							</div>							
							<div class="row">
							<?php
							$parametros = [
								'matcur_id_matricula' => $datosEstudianteActual["mat_id"],
								'matcur_id_curso'     => $dato["matcur_id_curso"],
								'matcur_id_grupo'     => $dato["matcur_id_grupo"],
								'matcur_id_institucion' => $config['conf_id_institucion'],
								'matcur_years' => $config['conf_agno']
							];
							$listacargaMediaTecnica = MediaTecnicaServicios::listarMaterias($parametros);
							if ($listacargaMediaTecnica != null) { 
								foreach ($listacargaMediaTecnica as $cargaMediaTecnica) {
									$fondoCargaActual = '#FFF';
									if(!empty($_COOKIE["cargaE"]) && $cargaMediaTecnica["car_id"]==$_COOKIE["cargaE"]){
										$fondoCargaActual = 'cornsilk';
									}
								?>
								<div class="col-lg-3 col-md-6 col-6 col-sm-6">
									<div class="blogThumb" style="background-color:<?= $fondoCargaActual; ?>;">
										<div class="thumb-center">
											<a href="cargas.php?carga=<?= base64_encode($cargaMediaTecnica["car_id"]); ?>&periodo=<?= base64_encode($cargaMediaTecnica["car_periodo"]); ?>"><img class="img-responsive" alt="user" src="../../config-general/assets/img/course/course1.jpg"></a>
										</div>
										<div class="course-box">
											<h4><a href="cargas.php?carga=<?= base64_encode($cargaMediaTecnica["car_id"]); ?>&periodo=<?= base64_encode($cargaMediaTecnica["car_periodo"]); ?>" style="text-decoration: underline;"><?= strtoupper($cargaMediaTecnica['mat_nombre']); ?></a></h4>
											<div class="text-muted">
												<span class="m-r-10" style="font-size: 10px;"><?= $ultimoAcceso; ?></span>

												<?php if ($datosUsuarioActual['uss_bloqueado'] != 1 and $config['conf_sin_nota_numerica'] != 1) { ?><a class="course-likes m-l-10" href="#"> <?= $definitiva; ?></a><?php } ?>

											</div>
											<p><span><i class="fa fa-clock-o"></i> <?= $frases[101][$datosUsuarioActual['uss_idioma']]; ?>: <?= $cargaMediaTecnica['car_periodo']; ?></span></p>
											<p><span><i class="fa  fa-user"></i> <?= $frases[28][$datosUsuarioActual['uss_idioma']]; ?>: <?= UsuariosPadre::nombreCompletoDelUsuario($cargaMediaTecnica); ?></span></p>


										</div>
									</div>
								</div>
								<?php }} else {
								echo '
									<div class="col-12">
										<div class="alert alert-danger">
											<i class="icon-exclamation-sign"></i><strong>INFORMACIÓN:</strong> El curso de <b>' . $cursoMediaTecnica["gra_nombre"] . '</b> no tiene cargas academicas asignadas aún.
										</div>
									</div>
									';
							} ?>
							</div>
							<?php }} ?>
					<?php } ?>
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