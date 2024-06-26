<?php include("session.php");?>
<?php include("verificar-usuario.php");?>
<?php $idPaginaInterna = 'ES0018';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/Actividades.php");
$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}
$actividad = Actividades::traerDatosActividades($conexion, $config, $idR);

if($actividad[0]==""){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=105";</script>';
	exit();
}

$fechas = Actividades::traerFechaActividadEstudiante($conexion, $config, $idR);
if($fechas[0]<0){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=206&fechaD='.$actividad['tar_fecha_disponible'].'&diasF='.$fechas[0].'";</script>';
	exit();
}

require_once("../class/CargaAcademica.php");
$infoCargaActual = CargaAcademica::cargasDatosEnSesion($cargaConsultaActual, $datosCargaActual['car_docente']);
$cantEstudiantesConsulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($infoCargaActual['datosCargaActual']);
$cantEstudiantes = mysqli_num_rows($cantEstudiantesConsulta);

include '../class/Tables/BDT_academico_actividad_tareas_entregas.php';
$predicado = [
	'ent_id_actividad' => $idR
];

$numEntregas = BDT_AcademicoActividadTareasEntregas::numRows($predicado);

$porcentajeEnviadas = ($numEntregas / $cantEstudiantes) * 100;
$porcentajeRestante = 100 - $porcentajeEnviadas;
$porcentajeEnviadas = round($porcentajeEnviadas,2);
$porcentajeRestante = round($porcentajeRestante,2);
?>
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
                                <div class="page-title"><?=$actividad['tar_titulo'];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="actividades.php"><?=$frases[112][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$actividad['tar_titulo'];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN PROFILE SIDEBAR -->
                            <div class="profile-sidebar">

                                <div class="card">
                                    <div class="card-head card-topline-aqua">
                                        <header><?=$frases[119][$datosUsuarioActual['uss_idioma']];?></header>
                                    </div>
                                    <div class="card-body no-padding height-9">
                                        <div class="work-monitor work-progress">
                                            <div class="states">
                                                <div class="info">
                                                    <div class="desc pull-left">Enviadas </div>
                                                    <div class="percent pull-right"><?=$porcentajeEnviadas;?>%</div>
                                                </div>
												
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajeEnviadas;?>%">
                                                        <span class="sr-only"><?=$porcentajeEnviadas;?>% </span>
                                                    </div>
                                                </div>
												
                                            </div>
											
                                            <div class="states">
                                                <div class="info">
                                                    <div class="desc pull-left">faltantes</div>
                                                    <div class="percent pull-right"><?=$porcentajeRestante;?>%</div>
                                                </div>
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajeRestante;?>%">
                                                        <span class="sr-only"><?=$porcentajeRestante;?>% </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								<?php include("../compartido/publicidad-lateral.php");?>
								
                            </div>
                            <!-- END BEGIN PROFILE SIDEBAR -->
                            <!-- BEGIN PROFILE CONTENT -->
                            <div class="profile-content">
                                <div class="row">
                                     <div class="card w-100">
                                         <div class="card-topline-aqua">
                                             <header></header>
                                         </div>
											<div class="white-box">
					                            <!-- Nav tabs -->
					                            <div class="p-rl-20">
						                            <ul class="nav customtab nav-tabs" role="tablist">
						                                <li class="nav-item"><a href="#tab1" class="nav-link active"  data-toggle="tab" ><?=$frases[196][$datosUsuarioActual['uss_idioma']];?></a></li>
						                                <li class="nav-item"><a href="#tab2" class="nav-link" data-toggle="tab"><?=$frases[197][$datosUsuarioActual['uss_idioma']];?></a></li>
						                            </ul>
					                            </div>
					                            <!-- Tab panes -->
					                            <div class="tab-content">
					                                <div class="tab-pane active fontawesome-demo" id="tab1">
															<div id="biography" >
							                                    <div class="row">
							                                        <div class="col-md-3 col-6 b-r"> <strong><?=$frases[127][$datosUsuarioActual['uss_idioma']];?></strong>
							                                            <br>
							                                            <p class="text-muted"><?=$actividad['tar_titulo'];?></p>
							                                        </div>
							                                        <div class="col-md-3 col-6 b-r"> <strong><?=$frases[130][$datosUsuarioActual['uss_idioma']];?></strong>
							                                            <br>
							                                            <p class="text-muted"><?=$actividad['tar_fecha_disponible'];?></p>
							                                        </div>
							                                        <div class="col-md-3 col-6 b-r"> <strong><?=$frases[131][$datosUsuarioActual['uss_idioma']];?></strong>
							                                            <br>
							                                            <p class="text-muted"><?=$actividad['tar_fecha_entrega'];?></p>
							                                        </div>
							                                        <div class="col-md-3 col-6"> <strong>Impedir restrasos?</strong>
							                                            <br>
							                                            <p class="text-muted"><?=$opcionSINO[$actividad['tar_impedir_retrasos']];?></p>
							                                        </div>
							                                    </div>

							                                    <h4 class="font-bold"><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></h4>
																<hr>
							                                    <p><?=$actividad['tar_descripcion'];?></p>
							                                    <br>
							                                    <h4 class="font-bold"><?=$frases[198][$datosUsuarioActual['uss_idioma']];?></h4>
							                                    <hr>
							                                    <ul>
																	<?php
																	$url1= $storage->getBucket()->object(FILE_TAREAS.$actividad["tar_archivo"])->signedUrl(new DateTime('tomorrow'));
																	$url2= $storage->getBucket()->object(FILE_TAREAS.$actividad["tar_archivo2"])->signedUrl(new DateTime('tomorrow'));
																	$url3= $storage->getBucket()->object(FILE_TAREAS.$actividad["ar_archivo3"])->signedUrl(new DateTime('tomorrow'));
																	$existe1=$storage->getBucket()->object(FILE_TAREAS.$actividad["tar_archivo"])->exists();
																	$existe2=$storage->getBucket()->object(FILE_TAREAS.$actividad["tar_archivo2"])->exists();
																	$existe3=$storage->getBucket()->object(FILE_TAREAS.$actividad["ar_archivo3"])->exists();
																	?>
							                                        <?php if(!empty($actividad['tar_archivo'])  and $existe1){?><li><a href="<?=$url1?>" target="_blank"><?=$actividad['tar_archivo'];?></a></li><?php }?>
																	<?php if(!empty($actividad['tar_archivo2']) and $existe2){?><li><a href="<?=$url2;?>" target="_blank"><?=$actividad['tar_archivo2'];?></a></li><?php }?>
																	<?php if(!empty($actividad['tar_archivo3']) and $existe3){?><li><a href="<?=$url3;?>" target="_blank"><?=$actividad['tar_archivo3'];?></a></li><?php }?>
							                                    </ul>
							                                    
							                                    <br>
							                                </div>
													</div>
					                                <div class="tab-pane" id="tab2">
														<div class="container-fluid">
		                                                    <div class="row">
		                                                        <div class="full-width p-rl-20">
		                                                            <div class="panel">
																		<p><?=$frases[199][$datosUsuarioActual['uss_idioma']];?></p>
		                                                                <?php if($fechas[1]<=0 or $actividad['tar_impedir_retrasos']==0){?>
																		<form id="form_subir" action="actividades-enviar.php" method="post" enctype="multipart/form-data">
																			<input type="hidden" name="idR" value="<?=$idR;?>">
		                                                                    
																			<p><textarea class="form-control border border-primary" name="comentario" rows="2" placeholder="<?=$frases[204][$datosUsuarioActual['uss_idioma']];?>"></textarea></p>
																			
																			<h4><mark>Puedes subir hasta 3 archivos si es necesario.</mark></h4>
																			<p>
																				Archivo 1:<br>
																				<input type="file" name="file" class="default" onChange="archivoPeso(this)">
																			</p>
																			
																			
																			<p>
																				Archivo 2:<br>
																				<input type="file" name="file2" class="default" onChange="archivoPeso(this)">
																			</p>
																			
																			<p>
																				Archivo 3:<br>
																				<input type="file" name="file3" class="default" onChange="archivoPeso(this)">
																			</p>
																			
																			<p>
																				<div class="progress">
																				  <div class="progress-bar progress-bar-striped bg-success" id="barra_estado" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%</div>
																				</div>
																			</p>
																			
																			<p><input type="submit" class="btn btn-info" value="<?=$frases[197][$datosUsuarioActual['uss_idioma']];?>"></p>
																			
		                                                                </form>
																		<?php }else{
																			echo "<span style='color:red;'>La fecha límite para enviar esta actividad ya pasó.</span>";
																		}?>
		                                                            </div>
																	<?php
																	$enviada = Actividades::consultarEntregas($conexion, $config, $datosEstudianteActual['mat_id'], $idR);
																	if(!empty($enviada[0])){
																	?>
																		<div class="panel">
																			<h4 class="font-bold"><?=$frases[200][$datosUsuarioActual['uss_idioma']];?></h4>
																			<hr>
																			<p><?=$frases[203][$datosUsuarioActual['uss_idioma']];?></p>
																			
																			<p><b><?=$frases[202][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$enviada['ent_fecha'];?> </p>
																			
																			<?php 
																			$url1= $storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$enviada["ent_archivo"])->signedUrl(new DateTime('tomorrow'));
																			$url2= $storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$enviada["ent_archivo2"])->signedUrl(new DateTime('tomorrow'));
																			$url3= $storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$enviada["ent_archivo3"])->signedUrl(new DateTime('tomorrow'));
																			$existe1=$storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$enviada["ent_archivo"])->exists();
																			$existe2=$storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$enviada["ent_archivo2"])->exists();
																			$existe3=$storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$enviada["ent_archivo3"])->exists();
																			
																			if(!empty($enviada['ent_archivo']) and $existe1){?>
																			<p><b><?=$frases[128][$datosUsuarioActual['uss_idioma']];?> 1:</b> 
																				<a href="<?=$url1?>" target="_blank"><?=$enviada['ent_archivo'];?> </a>
																			</p>
																			<?php }?>
																			
																			<?php if(!empty($enviada['ent_archivo2']) and $existe2){?>
																			<p><b><?=$frases[128][$datosUsuarioActual['uss_idioma']];?> 2:</b> 
																				<a href="<?=$url2?>"" target="_blank"><?=$enviada['ent_archivo2'];?> </a>
																			</p>
																			<?php }?>
																			
																			<?php if(!empty($enviada['ent_archivo3']) and $existe3){?>
																			<p><b><?=$frases[128][$datosUsuarioActual['uss_idioma']];?> 3:</b> 
																				<a href="<?=$url3?>" target="_blank"><?=$enviada['ent_archivo3'];?> </a>
																			</p>
																			<?php }?>
																			
																			<p><b><?=$frases[201][$datosUsuarioActual['uss_idioma']];?>:</b><br> <?=$enviada['ent_comentario'];?> </p>
																		</div>
																	<?php }?>
																	
		                                                        </div>
																
		                                                	</div>
														</div>
													</div>
					                            </div>
					                        </div>
                                         </div>
                                     </div>
                                </div>
                                <!-- END PROFILE CONTENT -->
                            </div>
                        </div>
                    </div>
                <!-- end page content -->
                <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
		
<script>
											document.addEventListener("DOMContentLoaded", () =>{
												let form = document.getElementById("form_subir");

												form.addEventListener("submit", function(event) {
													event.preventDefault();

													subir_archivos(this);
												});
											});

											function subir_archivos(form){
												let barra_estado = form.children[0];

												let peticion = new XMLHttpRequest();

												peticion.upload.addEventListener("progress", (event) => {
													let porcentaje = Math.round((event.loaded / event.total) * 100);

													document.getElementById("barra_estado").innerHTML = porcentaje+"%";
													document.getElementById("barra_estado").style.width = porcentaje+"%";

												});

												peticion.addEventListener("load", () => {
													document.getElementById("barra_estado").innerHTML = "Archivos subidos totalmente(100%)";
													if (peticion.status >= 200 && peticion.status < 300) {
														var respuesta = peticion.responseText;
														console.log(respuesta); 
													} else {
														console.error('Error en la solicitud:', peticion.status, peticion.statusText);
													}
													setTimeout(redirect(), 5000);
													
													function redirect(){
														location.reload();
													}

												});

												peticion.open("POST", "actividades-enviar.php");
												peticion.send(new FormData(form));

											}

										</script>

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
		<!-- notifications -->
		<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
		<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
        
        <script src="../../config-general/assets/js/layout.js" ></script>
		<script src="../../config-general/assets/js/theme-color.js" ></script>
		<!-- Material -->
		<script src="../../config-general/assets/plugins/material/material.min.js"></script>
        <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/student_profile.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:31:36 GMT -->
</html>