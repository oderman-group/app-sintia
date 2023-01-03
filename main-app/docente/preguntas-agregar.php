<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0023';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaEvaluacion=mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones 
WHERE eva_id='".$_GET["idE"]."' AND eva_estado=1");
$evaluacion = mysqli_fetch_array($consultaEvaluacion, MYSQLI_BOTH);

//Cantidad de preguntas de la evaluación
$preguntasConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluacion_preguntas
INNER JOIN academico_actividad_preguntas ON preg_id=evp_id_pregunta
WHERE evp_id_evaluacion='".$_GET["idE"]."'
ORDER BY preg_id DESC");

$cantPreguntas = mysqli_num_rows($preguntasConsulta);
?>

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<!-- dropzone -->
    <link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
    <!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
    <!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
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
                                <div class="page-title"><?=$frases[56][$datosUsuarioActual[8]];?> <?=$frases[139][$datosUsuarioActual[8]];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="evaluaciones-preguntas.php?idE=<?=$_GET["idE"];?>" onClick="deseaRegresar(this)"><?=$frases[139][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[56][$datosUsuarioActual[8]];?> <?=$frases[139][$datosUsuarioActual[8]];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">


						<div class="panel">
										<header class="panel-heading panel-heading-blue"><?=strtoupper($datosCargaActual['mat_nombre']." (".$datosCargaActual['gra_nombre']." ".$datosCargaActual['gru_nombre'].")");?> </header>
										<div class="panel-body">
											<div class="card">
											<div class="card-head card-topline-aqua">
												<header><?=$evaluacion['eva_nombre'];?></header>
											</div>
											<div class="card-body no-padding height-9">
												<div class="profile-desc">
													<?=$evaluacion['eva_descripcion'];?>
												</div>
												<ul class="list-group list-group-unbordered">
													<li class="list-group-item">
														<b><?=$frases[130][$datosUsuarioActual[8]];?> </b>
														<div class="profile-desc-item pull-right"><?=$evaluacion['eva_desde'];?></div>
													</li>
													<li class="list-group-item">
														<b><?=$frases[131][$datosUsuarioActual[8]];?> </b>
														<div class="profile-desc-item pull-right"><?=$evaluacion['eva_hasta'];?></div>
													</li>
												</ul>

											</div>
										</div>
										</div>
                                    </div>

							
                            <div class="panel">
								<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                <div class="panel-body">
									<p><b>Banco de datos:</b> Tienes la opción de usar información que ya existe y así no tengas que escribir todo de nuevo. <mark>Sólo debes usar una de las 2 alternativas:</mark> o llenas la información desde cero o escoges la existente. Si usas las 2, <mark>el banco de datos tendrá prioridad</mark> y esta será lo que el sistema use.<br>
									<mark> - MIO :</mark> Significa que la información fue creada por ti.
									</p>
									<p><b>Compartir:</b> Compartir la información <mark>es una manera de colaborar con tus colegas.</mark> La información irá al banco de datos y podrá ser usada por ti o por otros colegas tuyos más adelante. En caso de que no desees compartirla puedes dar click sobre el botón para que se desactive y la información sólo puedas verla tú.</p>
								</div>
							</div>
                        </div>
						
                        <div class="col-sm-9">
								
								<?php if($_GET["idMsg"]==1){?>
									<p class="text-success">La evaluación fue creada correctamente. El siguiente paso es crear las preguntas o utilizar algunas existentes del banco de datos. <b>Empieza ahora!</b></p>
											
								<?php }?>
							

								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="guardar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" method="post" enctype="multipart/form-data">
										<input type="hidden" value="7" name="id">
										<input type="hidden" value="<?=$_GET["idE"];?>" name="idE">

										<div id="infoCero">
											<p style="color: blue;">Puedes llenar toda la información desde cero.</p>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Pregunta</label>
												<div class="col-sm-10">
													<textarea name="contenido" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" placeholder="Escriba aquí la pregunta..." required></textarea>
												</div>
											</div>
											

												<div class="form-group row">
													<label class="col-sm-2 control-label">Puntos</label>
													<div class="col-sm-2">
														<input type="text" name="valor" class="form-control" autocomplete="off" required>
													</div>
												</div>
											
											<p class="text-warning">Opcional.</p>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Adjuntar un archivo</label>
												<div class="col-sm-6">
													<input type="file" name="file" class="form-control" autocomplete="off" onClick="tipoPregunta(3)">
												</div>
											</div>


											<div class="form-group row">
												<label class="col-sm-2 control-label">Compartir</label>
												<div class="input-group spinner col-sm-10">
													<label class="switchToggle">
														<input type="checkbox" name="compartir" value="1" checked>
														<span class="slider red round"></span>
													</label>
												</div>
											 </div>
										</div>	
										
										
										<p style="color: blue;">Ó si quieres puedes usar el <b>banco de datos</b>. Tal vez te sirva algo de lo que ya existe.</p>
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><b>Banco de datos</b></label>
                                            <div class="col-sm-10">
												<?php
												$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_preguntas 
												WHERE preg_id_carga='".$cargaConsultaActual."'");
												?>
                                                <select class="form-control  select2" name="bancoDatos" onChange="avisoBancoDatos(this)">
                                                    <option value="">Seleccione una opción</option>
													<option value="0" selected>--Ninguno--</option>
													<?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
													?>
                                                    	<option value="<?=$opcionesDatos['preg_id'];?>"><?=$opcionesDatos['preg_descripcion']." (".$opcionesDatos['preg_valor']."Ptos.)";?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>


                                </div>
                            </div>
							
							<div id="infoCeroDos">
								
								<div class="panel">
										<header class="panel-heading panel-heading-purple">Opciones de respuestas </header>
										<div class="panel-body">
											
												<div class="form-group row">
													<label class="col-sm-2 control-label">Tipo de pregunta</label>
													<div class="col-sm-2">
														<input type="radio" name="opcionR" id="opr1" value="1" checked onClick="tipoPregunta(1)"> Selección Multiple
													</div>
													<div class="col-sm-2">
														<input type="radio" name="opcionR" id="opr2" value="2" onChange="tipoPregunta(2)"> Falso y verdadero
													</div>
													<div class="col-sm-2">
														<input type="radio" name="opcionR" id="opr3" value="3" onChange="tipoPregunta(3)"> Adjuntar archivo
													</div>
												</div>
											
												<div id="multiple">
													<div class="form-group row">
														<label class="col-sm-2 control-label">A</label>
														<div class="col-sm-8">
															<input type="text" name="r1" class="form-control" autocomplete="off">
														</div>
														<div class="col-sm-2">
															<label class="switchToggle">
																<input type="checkbox" name="c1" value="1">
																<span class="slider red round"></span>
															</label>
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-2 control-label">B</label>
														<div class="col-sm-8">
															<input type="text" name="r2" class="form-control" autocomplete="off">
														</div>
														<div class="col-sm-2">
															<label class="switchToggle">
																<input type="checkbox" name="c2" value="1">
																<span class="slider red round"></span>
															</label>
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-2 control-label">C</label>
														<div class="col-sm-8">
															<input type="text" name="r3" class="form-control" autocomplete="off">
														</div>
														<div class="col-sm-2">
															<label class="switchToggle">
																<input type="checkbox" name="c3" value="1">
																<span class="slider red round"></span>
															</label>
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-2 control-label">D</label>
														<div class="col-sm-8">
															<input type="text" name="r4" class="form-control" autocomplete="off">
														</div>
														<div class="col-sm-2">
															<label class="switchToggle">
																<input type="checkbox" name="c4" value="1">
																<span class="slider red round"></span>
															</label>
														</div>
													</div>
											
											</div>
											
											<div id="verdadero" style="display: none;">
												<div class="form-group row">
														<label class="col-sm-2 control-label">A</label>
														<div class="col-sm-8">
															<input type="text" name="rv1" value="Verdadero" class="form-control" autocomplete="off">
														</div>
														<div class="col-sm-2">
															<label class="switchToggle">
																<input type="checkbox" name="cv1" value="1">
																<span class="slider red round"></span>
															</label>
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-2 control-label">B</label>
														<div class="col-sm-8">
															<input type="text" name="rv2" value="Falso" class="form-control" autocomplete="off">
														</div>
														<div class="col-sm-2">
															<label class="switchToggle">
																<input type="checkbox" name="cv2" value="1">
																<span class="slider red round"></span>
															</label>
														</div>
													</div>
												</div>
											
											
											<div id="archivo" style="display: none;">
												
												<p style="color: navy;">
													El estudiante deberá montar un archivo con las indicaciones de esta pregunta.<br>
													Esta tipo de preguntas deberá calificarlas manualmente el docente.
												</p>
													
											</div>

									</div>
								</div>
							</div>
									
									<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;

												<a href="#" name="evaluaciones-preguntas.php?idE=<?=$_GET["idE"];?>" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
										</form>
							
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
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>