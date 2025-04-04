<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0022';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");?>

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
                                <div class="page-title"><?=$frases[167][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
					<?php include("includes/barra-superior-informacion-actual.php"); ?>
                    <div class="row">
						
                        <div class="col-md-12">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="importar-info-detalles.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" method="post">
										
										<p style="color: darkblue;"><?=$frases[376][$datosUsuarioActual['uss_idioma']];?></p>	
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="cargaImportar" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$consulta = CargaAcademica::traerCargasDocentes($config, $_SESSION["id"]);
													while($datos = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$infoActual = '';
														if($datos['car_id']==$cargaConsultaActual) $infoActual = ' - Actualmente estás en esta carga.';
													?>
                                                    	<option value="<?=$datos['car_id'];?>"><?=strtoupper($datos['mat_nombre']." (".$datos['gra_nombre']." ".$datos['gru_nombre']).")".$infoActual;?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[27][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="periodoImportar" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$p=1;
													while($p<=$datosCargaActual['gra_periodos']){
														$infoActual = '';
														if($p==$periodoConsultaActual) $infoActual = ' - Actualmente estás en este periodo.';
													?>
                                                    	<option value="<?=$p;?>"><?="PERIODO ".$p."".$infoActual;?></option>
													<?php $p++;}?>
                                                </select>
                                            </div>
                                        </div>
										
										<p style="color: darkblue;"><?=$frases[377][$datosUsuarioActual['uss_idioma']];?></p>	
										
										<div class="form-group row">
											<label class="col-sm-2 control-label"><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></label>
											<div class="input-group spinner col-sm-10">
												<label class="switchToggle">
													<input type="checkbox" name="indicadores" value="1">
													<span class="slider red round"></span>
												</label>
											</div>
										</div>
										<p><mark><?=$frases[378][$datosUsuarioActual['uss_idioma']];?></mark></p>
										<div class="form-group row">
											<label class="col-sm-2 control-label"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></label>
											<div class="input-group spinner col-sm-10">
												<label class="switchToggle">
													<input type="checkbox" name="calificaciones" value="1">
													<span class="slider red round"></span>
												</label>
											</div>
										</div>

										<?php if(array_key_exists(11, $arregloModulos)){?>
											<div class="form-group row">
												<label class="col-sm-2 control-label"><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="input-group spinner col-sm-10">
													<label class="switchToggle">
														<input type="checkbox" name="clases" value="1">
														<span class="slider red round"></span>
													</label>
												</div>
											</div>
										<?php }?>
										
										<!--
										<div class="form-group row">
											<label class="col-sm-2 control-label"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></label>
											<div class="input-group spinner col-sm-10">
												<label class="switchToggle">
													<input type="checkbox" name="evaluaciones" value="1">
													<span class="slider red round"></span>
												</label>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label"><?=$frases[112][$datosUsuarioActual['uss_idioma']];?></label>
											<div class="input-group spinner col-sm-10">
												<label class="switchToggle">
													<input type="checkbox" name="actividades" value="1">
													<span class="slider red round"></span>
												</label>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label"><?=$frases[113][$datosUsuarioActual['uss_idioma']];?></label>
											<div class="input-group spinner col-sm-10">
												<label class="switchToggle">
													<input type="checkbox" name="foros" value="1">
													<span class="slider red round"></span>
												</label>
											</div>
										</div>
										-->
										

										<p><mark><?=$frases[379][$datosUsuarioActual['uss_idioma']];?></mark></p>
										
										<input type="submit" class="btn btn-primary" value="Continuar">&nbsp;
										
                                    </form>
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