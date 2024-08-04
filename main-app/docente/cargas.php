<?php
include("session.php");
$idPaginaInterna = 'DC0033';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once("../class/UsuariosPadre.php");
require_once("../class/Estudiantes.php");
require_once("../class/Sysjobs.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$datosCargaActual = null;

if( !empty($_SESSION["infoCargaActual"]) ) {
	$datosCargaActual = $_SESSION["infoCargaActual"]['datosCargaActual'];
}
?>
</head>
<style>
	.alert-warning-select {
    color: #4f3e0d;
    background-color: #f5c426;
    border-color: #ffeeba;
}

.elemento-draggable {
    cursor: grab;
}
</style>
 <!-- END HEAD -->
<?php include("../compartido/body.php");?>

<div id="overlayInforme">
	<div id="loader"></div>
	<div id="loading-text">Generando informe…</div>
</div>


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
                                <div class="page-title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                   
                   
                     <!-- start course list -->
                     <div class="row">
						 
						 <div class="col-sm-12">
						 <?php include("../../config-general/mensajes-informativos.php"); ?>

							 <?php
							$cCargas = CargaAcademica::traerCargasDocentes($config, $_SESSION["id"]);
							$cargasCont = 1;
							$nCargas = mysqli_num_rows($cCargas);
							$mensajeCargas = new Cargas;
							$mensajeCargas->verificarNumCargas($nCargas);
							if ($nCargas > 0) {
							?>
								
							 <p>
								 	<a href="../compartido/planilla-docentes.php?docente=<?=base64_encode($_SESSION["id"]);?>" target="_blank" style="text-decoration: underline;">Imprimir todas mis planillas</a>
									&nbsp;&nbsp;|&nbsp;&nbsp;
									 <a href="../compartido/planilla-docentes-notas.php?docente=<?=base64_encode($_SESSION["id"]);?>" target="_blank" style="text-decoration: underline;">Imprimir planillas con resumen de notas</a>
									 &nbsp;&nbsp;|&nbsp;&nbsp;
									 <a href="cargas-general.php" style="text-decoration: underline;">Ir a vista general</a>
									 &nbsp;&nbsp;|&nbsp;&nbsp;
									 <a href="javascript:void(0);" onClick="fetchGeneral('../compartido/progreso-docentes.php?modal=1', 'Progreso de los docentes')" style="text-decoration: underline;">Ver progreso de los docentes</a>
							 </p>
							 <?php }?>
							 <div class="row" id="sortable-container">
								 
								 
								 
									<?php
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
									    $ultimoAcceso = 'Nunca';
										$fondoCargaActual = '#FFF';
										$seleccionado=false;

										if(!empty($rCargas['car_ultimo_acceso_docente'])){$ultimoAcceso = $rCargas['car_ultimo_acceso_docente'];}
										if(!empty($_COOKIE["carga"]) && $rCargas['car_id']==$_COOKIE["carga"]){$fondoCargaActual = 'cornsilk'; $seleccionado=true;}
										
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
												$parametros = array(
													"carga" =>$rCargas["car_id"],
													"periodo" =>$rCargas["car_periodo"],
													"grado" => $rCargas["car_curso"],
													"grupo"=>$rCargas["car_grupo"]
												);
												
												$parametrosBuscar = array(
													"tipo" =>JOBS_TIPO_GENERAR_INFORMES,
													"responsable" => $_SESSION['id'],
													"parametros" => json_encode($parametros),
													"agno"=>$config['conf_agno']
												);
												$buscarJobs=SysJobs::consultar($parametrosBuscar);
												$jobsEncontrado = mysqli_fetch_array($buscarJobs, MYSQLI_BOTH);

												$configGenerarJobs=$config['conf_porcentaje_completo_generar_informe'];

												$consultaListaEstudantesSinNotas =Estudiantes::listarEstudiantesNotasFaltantes($rCargas["car_id"],$rCargas["car_periodo"],$rCargas["gra_tipo"]);
												$numSinNotas=mysqli_num_rows($consultaListaEstudantesSinNotas);

												$btnGenerarInforme='
                                                                <div class="btn-group mt-2">
                                                                    <button type="button" class="btn red">Generar Informe</button>
                                                                    <button type="button" class="btn red dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
                                                                        <li>
																			<a 
																				rel="'.$configGenerarJobs.'-'.$numSinNotas.'-1" 
																				data-toggle="tooltip" 
																				data-placement="right" 
																				title="Lo hará usted manualmente como siempre." 
																				href="javascript:void(0);" 
																				name="../compartido/generar-informe.php?
																					carga='.base64_encode($rCargas["car_id"]).'
																					&periodo='.base64_encode($rCargas["car_periodo"]).'
																					&grado='.base64_encode($rCargas["car_curso"]).'
																					&grupo='.base64_encode($rCargas["car_grupo"]).'
																					&tipoGrado='.base64_encode($rCargas["gra_tipo"]).'
																				" 
																				onclick="mensajeGenerarInforme(this)"
																			>
																				Forma tradicional
																			</a>
																		</li>
                                                                    </ul>
                                                                </div>
															';

												$alertaNotasFaltantes='
															<div class="alert alert-danger mt-2" role="alert" style="margin-right: 20px;">
																<a target="_blank" href="calificaciones-faltantes.php?carga='.base64_encode($rCargas["car_id"]).'&periodo='.base64_encode($rCargas["car_periodo"]).'&get='.base64_encode(100).'">El informe no se puede generar, coloque las notas a todos los estudiantes para generar el informe.</a>
															</div>
															';
												
												if(empty($jobsEncontrado)){
													$mensajeI = $btnGenerarInforme;
													if($configGenerarJobs==1 && $numSinNotas>0){
														$mensajeI = $alertaNotasFaltantes;
													}
												}else{
													$intento = intval($jobsEncontrado["job_intentos"]);
													switch($jobsEncontrado["job_estado"]){
														case JOBS_ESTADO_ERROR:														
															if($configGenerarJobs == 1) {
																$mensajeI = '<div class="alert alert-danger mt-3" role="alert" style="margin-right: 20px;">'.$jobsEncontrado["job_mensaje"].'</div>';
															} else {
																$mensajeI = $btnGenerarInforme
																	.'<div class="alert alert-info mt-3" role="alert" style="margin-right: 20px;">Por favor, vuelva a intentarlo!</div>';
															}
														break;

														case JOBS_ESTADO_PENDIENTE:
															if($intento==0){
																$mensajeI ='<div class="alert alert-success mt-3" role="alert" style="margin-right: 20px;">'.$jobsEncontrado["job_mensaje"].'</div>';
															}elseif($intento>0 && $seleccionado){
																$mensajeI ='<div class="alert alert-warning-select mt-3" role="alert" style="margin-right: 20px;">'.$jobsEncontrado["job_mensaje"].' <br><br>(La plataforma ha echo <b>'.$intento.'</b> intentos.)</div>';
															}elseif($intento>0){
																$mensajeI ='<div class="alert alert-warning mt-3" role="alert" style="margin-right: 20px;">'.$jobsEncontrado["job_mensaje"].' <br><br>(La plataforma ha echo <b>'.$intento.'</b> intentos.)</div>';
															}
														break;

														case JOBS_ESTADO_PROCESO:
															$mensajeI ='<div class="alert alert-success mt-3" role="alert" style="margin-right: 20px;">El informe está en proceso.</div>';
														break;

														default:
															$mensajeI = $btnGenerarInforme;
															if($configGenerarJobs==1 && $numSinNotas>0){
																$mensajeI = $alertaNotasFaltantes;
															}
															break;
													}
												}
												
											  }	
										}
										
										$induccionEntrar = '';
										$induccionSabanas = '';
										if($cargasCont == 1){
											$induccionEntrar = 'data-hint="Haciendo click sobre el nombre o sobre la imagen puedes entrar a administrar esta carga académica."';
											$induccionSabanas = 'data-hint="Puedes ver las sábanas de cada uno de los periodos pasados."';
										}

										$cantEstudiantesConsulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($rCargas);
										$cantEstudiantes = mysqli_num_rows($cantEstudiantesConsulta);

										$marcaMediaTecnica = '';
										if($rCargas['gra_tipo'] == GRADO_INDIVIDUAL) {
											$marcaMediaTecnica = '<i class="fa fa-bookmark" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Media técnica"></i> ';
										}

										$marcaDG = '';
										if($rCargas['car_director_grupo']==1){
											$marcaDG = '<i class="fa fa-star text-info" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Director de grupo"></i> ';
										}
									?>
						 <div class="col-lg-3 col-md-6 col-12 col-sm-6 sortable-item elemento-draggable" draggable="true" id="carga-<?=$rCargas['car_id'];?>"> 
							<div class="blogThumb" style="background-color:<?=$fondoCargaActual;?>;">
								<div class="thumb-center">
									<a href="cargas-seleccionar.php?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($rCargas['car_periodo']);?>" title="Entrar">
										<img class="img-responsive" alt="user" src="../../config-general/assets/img/course/course1.jpg">
									</a>	
								</div>
	                        	<div class="course-box">
	                        	<h5 <?=$induccionEntrar;?>><a href="cargas-seleccionar.php?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($rCargas['car_periodo']);?>" title="Entrar" style="text-decoration: underline;"><?=strtoupper($rCargas['mat_nombre']);?></a></h5>
		                            
									<p>
										<span> <b><?=$marcaDG ." ".$marcaMediaTecnica."".$frases[164][$datosUsuarioActual['uss_idioma']];?>:</b> <?=strtoupper($rCargas['gra_nombre']." ".$rCargas['gru_nombre'])." <b>(".$cantEstudiantes." Est.)</b> ";?></span>
									</p>
									
									
									<p align="center" <?=$induccionSabanas;?>>
                                      	<?php for($i=1; $i<$rCargas["car_periodo"]; $i++){?><a href="../compartido/informes-generales-sabanas.php?curso=<?=base64_encode($rCargas["car_curso"]);?>&grupo=<?=base64_encode($rCargas["car_grupo"]);?>&per=<?=base64_encode($i);?>" target="_blank" style="text-decoration:underline; color:#00F;" title="Sabanas"><?=$i;?></a>&nbsp;&nbsp;&nbsp;&nbsp;<?php }?>
                                    </p>
									
		                            
									
									<div class="text">
										<span class="m-r-10" style="font-size: 10px;"><b>Notas:</b> <?=$spcd[0];?>% / <?=$spcr[0];?>% | <b>Periodo:</b> <?=$rCargas['car_periodo'];?> | <b>Posición:</b> <?=$rCargas['car_posicion_docente'];?></span> 
		                            </div>
									
									<span id="mensajeI<?=$rCargas['car_id']?>"><?=$mensajeI;?></span>
									
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

<script>

const sortableContainer = document.getElementById("sortable-container");
let draggedItem = null;
let fromIndex, toIndex;
let idCarga;
let target;
let docente = '<?=$_SESSION["id"];?>';

sortableContainer.addEventListener("dragstart", (e) => {
    draggedItem = e.target;
    fromIndex = Array.from(sortableContainer.children).indexOf(draggedItem);
	idCarga = e.target.id.split('-')[1];
	
	target = e.target;

	target.style.backgroundColor = "#f0f0f0";
	target.style.transition = "all 0.2s ease";
});

sortableContainer.addEventListener("dragover", (e) => {
    e.preventDefault();
    const targetItem = e.target;
    if (targetItem.classList.contains("sortable-item")) {
        toIndex = Array.from(sortableContainer.children).indexOf(targetItem);
    }
});

sortableContainer.addEventListener("drop", (e) => {
    e.preventDefault();
    if (fromIndex > -1 && toIndex > -1) {
        if (fromIndex < toIndex) {
            sortableContainer.insertBefore(draggedItem, sortableContainer.children[toIndex].nextSibling);
        } else {
            sortableContainer.insertBefore(draggedItem, sortableContainer.children[toIndex]);
        }
    }
	target = e.target;

	target.style.backgroundColor = "initial";
	target.style.transition = "initial";

	if(typeof toIndex === undefined) {
		toIndex = 1;
	} else {
		toIndex ++;
	}

	cambiarPosicion(idCarga, toIndex, docente);
});

// Prevenir eventos por defecto
document.addEventListener("dragover", (e) => {
    e.preventDefault();
});

</script>
  </body>

</html>