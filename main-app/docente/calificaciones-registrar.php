<?php
include("session.php");
$idPaginaInterna = 'DC0021';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
include("../compartido/head.php");
require_once("../class/Estudiantes.php");
include("../compartido/sintia-funciones-js.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");

$idR="";
if (!empty($_GET["idR"])) { 
	$idR=base64_decode($_GET["idR"]);
}

$calificacion = Actividades::consultarDatosActividadesIndicador($config, $idR);
?>

<!-- Theme Styles -->

<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />

<style>
/* Overlay de bloqueo mientras se guarda */
#overlay-guardando-nota {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(30, 41, 59, 0.85);
	z-index: 99999;
	backdrop-filter: blur(6px);
}

.overlay-content-nota {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background: white;
	padding: 45px 50px;
	border-radius: 20px;
	text-align: center;
	box-shadow: 0 20px 60px rgba(0,0,0,0.4);
	min-width: 320px;
}

.overlay-content-nota .spinner {
	width: 70px;
	height: 70px;
	border: 5px solid #e2e8f0;
	border-top-color: #667eea;
	border-radius: 50%;
	margin: 0 auto 25px;
	animation: spin 1s linear infinite;
}

@keyframes spin {
	to { transform: rotate(360deg); }
}

.overlay-content-nota h3 {
	color: #2d3748;
	margin: 0 0 10px 0;
	font-size: 22px;
	font-weight: 700;
}

.overlay-content-nota p {
	color: #718096;
	margin: 0;
	font-size: 15px;
}
</style>

</head>

<!-- END HEAD -->

<?php include("../compartido/body.php");?>

<!-- Overlay de bloqueo mientras se guarda la nota -->
<div id="overlay-guardando-nota">
	<div class="overlay-content-nota">
		<div class="spinner"></div>
		<h3>💾 Guardando Nota...</h3>
		<p>Por favor espera, no cierres esta ventana</p>
	</div>
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

                                <div class="page-title"><?=$calificacion['act_descripcion']." (".$calificacion['act_valor']."%)";?></div>

								<p style="font-size: 13px; color: darkblue;"><?=$calificacion['ind_nombre'];?></p>

								<?php include("../compartido/texto-manual-ayuda.php");?>

                            </div>

							<ol class="breadcrumb page-breadcrumb pull-right">

                                <li><a class="parent-item" href="calificaciones.php"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>

                                <li class="active"><?=$calificacion['act_descripcion'];?></li>

                            </ol>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">

                            <div class="row">

                                

								<div class="col-md-4 col-lg-3">

									

									<?php include("info-carga-actual.php");?>

									<div class="panel">

										<header class="panel-heading panel-heading-purple">TABLA DE VALORES</header>

										<div class="panel-body">

											  <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">

												<!-- BEGIN -->

												<thead>

												  <tr>

													<th>Desde</th>

													<th>Hasta</th>

													<th>Resultado</th>

												  </tr>

												</thead>

												<tbody>

												 <?php
												 $TablaNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"]);
												 while($tabla = mysqli_fetch_array($TablaNotas, MYSQLI_BOTH)){

												 ?>

												  <tr id="data1" class="odd grade">



													<td><?=$tabla["notip_desde"];?></td>

													<td><?=$tabla["notip_hasta"];?></td>

													<td><?=$tabla["notip_nombre"];?></td>

												  </tr>

												  <?php }

													mysqli_free_result($TablaNotas);

													?>

												</tbody>

											  </table>

										</div>

										

                                    </div>

									

									<div class="panel">

										<header class="panel-heading panel-heading-purple"><?=strtoupper($frases[6][$datosUsuarioActual['uss_idioma']]);?> </header>

										<div class="panel-body">

											<p>Puedes cambiar a otra actividad rápidamente para calificar a tus estudiantes o hacer modificaciones de notas.</p>

											<?php
											$registrosEnComun = Actividades::consultaActividadesDiferentesCarga($config, $idR, $cargaConsultaActual, $periodoConsultaActual);
											while($regComun = mysqli_fetch_array($registrosEnComun, MYSQLI_BOTH)){

											?>

												<p><a href="<?=$_SERVER['PHP_SELF'];?>?idR=<?=base64_encode($regComun['act_id']);?>"><?=$regComun['act_descripcion']." (".$regComun['act_valor']."%)";?></a></p>

											<?php }

											mysqli_free_result($registrosEnComun);

											?>

										</div>

                                    </div>

									

								</div>

								<div class="col-md-8 col-lg-9">

                                    <div class="card card-topline-purple">

                                        <div class="card-head">

                                            <header><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></header>

                                            <div class="tools">

                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>

			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>

			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>

                                            </div>

                                        </div>

                                        <div class="card-body">

											<div class="row" style="margin-bottom: 10px;">

												<div class="col-sm-12" align="center">

													<p style="color: darkblue;">Utilice esta casilla para colocar la misma nota a todos los estudiantes. Esta opción <mark>reemplazará las notas existentes</mark> en esta actividad.</p>

													<input 
														type="text" 
														style="text-align: center; font-weight: bold;" 
														size="10" 
														title="0" 
														onChange="notasMasiva(this)" 
														name="<?=$idR;?>"
													>

												</div>

											</div>

										<span style="color: blue; font-size: 15px;" id="respRCT"></span>

                                        <div class="table-responsive">

                                            <table class="table table-striped custom-table table-hover" id="tabla_notas">

                                                <thead>

                                                    <tr>

                                                        <th>#</th>

														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>

														<th><?=$frases[108][$datosUsuarioActual['uss_idioma']];?></th>

														<?php if($config['conf_forma_mostrar_notas'] == CUALITATIVA){	?>
															<th>Nota<br>Cualitativa</th>
														<?php }	?>

														<th>Recup.</th>

														<th><?=$frases[109][$datosUsuarioActual['uss_idioma']];?></th>

                                                    </tr>

                                                </thead>

                                                <tbody>

													<?php
													if($datosCargaActual['gra_tipo'] == GRADO_INDIVIDUAL) {
														$consulta = Estudiantes::listarEstudiantesParaDocentesMT($datosCargaActual);
													} else {
														$consulta = Estudiantes::listarEstudiantesParaDocentes($filtroDocentesParaListarEstudiantes);
													}
													
													$contReg = 1;
													$colorNota = "black";
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){

														if ($calificacion['act_registrada']==1) {

															 //Consulta de calificaciones si ya la tienen puestas.
															$notas = Calificaciones::traerCalificacionActividadEstudiante($config, $idR, $resultado['mat_id']);

															if (!empty($notas['cal_nota']) && $notas['cal_nota'] < $config[5]) $colorNota = $config[6]; 
															elseif(!empty($notas['cal_nota']) && $notas['cal_nota'] >= $config[5]) $colorNota = $config[7];

														}

														$fotoEst = $usuariosClase->verificarFoto($resultado['uss_foto']);

													?>

													<?php

													$arrayEnviar = [
														"tipo"            => 2, 
														"descripcionTipo" => "Para ocultar la X y limpiar valor.", 
														"idInput"         => $resultado['mat_id']
													];

													$arrayDatos = json_encode($arrayEnviar);

													$objetoEnviar = htmlentities($arrayDatos);
                        
													$estiloNotaFinal="";
													if(!empty($notas['cal_nota']) && $config['conf_forma_mostrar_notas'] == CUALITATIVA){		
														$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notas['cal_nota']);
														$estiloNotaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
													}

													$notaActual = !empty($notas['cal_nota']) ? $notas['cal_nota'] : '';

													?>

													<tr id="fila_<?=$resultado['mat_id'];?>">

                                                        <td><?=$contReg;?></td>

														<td>

															<img src="<?=$fotoEst;?>" width="50">

															<?=Estudiantes::NombreCompletoDelEstudiante($resultado);?>

														</td>

														<td id="columna_<?=$resultado['mat_id'];?>">

															<input 
																type="text" 
																style="text-align: center; color:<?=$colorNota;?>" 
																size="5"
																value="<?php echo $notaActual;?>" 
																id="<?=$resultado['mat_id'];?>" 
																data-cod-estudiante="<?=$resultado['mat_id'];?>" 
																data-carga-actividad="<?=$cargaConsultaActual;?>"
																data-nota-anterior="<?php echo $notaActual;?>"
																data-color-nota-anterior="<?=$colorNota;?>"
																data-cod-nota="<?=$idR;?>"
																data-nombre-estudiante="<?=$resultado['mat_nombres']." ".$resultado['mat_primer_apellido'];?>"
																onChange="notasGuardar(this, 'fila_<?=$resultado['mat_id'];?>', 'tabla_notas')" 
																tabindex="<?=$contReg;?>"
															>

															<p id="CU<?=$resultado['mat_id'].$cargaConsultaActual;?>" style="font-size: 12px; color:<?=$colorNota;?>;"><?=$estiloNotaFinal?></p>

															<?php if (!empty($notas['cal_nota'])) {?>
															<a 
																href="#"
																title="<?=$objetoEnviar;?>" 
																id="<?=$notas['cal_id'];?>" 
																name="calificaciones-nota-eliminar.php?id=<?=base64_encode($notas['cal_id']);?>" onClick="deseaEliminar(this)"
																s
															>
																<i class="fa fa-trash"></i>
															</a>
														<?php }?>
														
														</td>

														<?php if($config['conf_forma_mostrar_notas'] == CUALITATIVA){	?>
															<td id="CU<?=$resultado['mat_id'].$cargaConsultaActual;?>" style="font-size: 12px; color:<?=$colorNota;?>"><?=$estiloNotaFinal?></td>
														<?php }	?>

														<td>

															<?php 
															$recuperacionVisibilidad = 'hidden';
															if (!empty($notas['cal_nota']) && $notas['cal_nota'] < $config[5]) {
																$recuperacionVisibilidad = 'visible';
															}
															?>

															<input
																data-id="recuperacion_<?=$resultado['mat_id'].$cargaConsultaActual;?>"
																type="text" 
																size="5" 
																step="<?=$cargaConsultaActual;?>"
																name="<?php if(!empty($notas['cal_nota'])) echo $notas['cal_nota'];?>" 
																id="<?=$resultado['mat_id'];?>" 
																alt="<?=$resultado['mat_nombres'];?>" 
																title="<?=$idR;?>" 
																onChange="notaRecuperacion(this)"
																style="
																	font-size: 13px; 
																	text-align: center;
																	visibility:<?=$recuperacionVisibilidad;?>;
																" 
															>

														</td>

														<td>
															<input 
																type="text" 
																value="<?php if(!empty($notas['cal_observaciones'])){ echo $notas['cal_observaciones'];}?>" 
																name="O<?=$contReg;?>" 
																id="<?=$resultado['mat_id'];?>" 
																alt="<?=$resultado['mat_nombres'];?>" 
																title="<?=$idR;?>" 
																onChange="guardarObservacion(this)" 
																tabindex="10<?=$contReg;?>"
															>
														</td>

                                                    </tr>

													<?php 
														$contReg++;
													}

													mysqli_free_result($consulta);
													?>

                                                </tbody>

                                            </table>

                                            </div>

                                        </div>

                                    </div>

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

    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>

    <!-- Common js-->

	<script src="../../config-general/assets/js/app.js" ></script>

    <script src="../../config-general/assets/js/layout.js" ></script>

	<script src="../../config-general/assets/js/theme-color.js" ></script>

	<!-- notifications -->

	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>

	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	

	<!-- Material -->

	<script src="../../config-general/assets/plugins/material/material.min.js"></script>

    <!--tags input-->

    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>

    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>

    <!-- end js include path -->

</body>



</html>