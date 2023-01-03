<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0029';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaCalificacion=mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id='".$_GET["idR"]."' AND act_estado=1");
$calificacion = mysqli_fetch_array($consultaCalificacion, MYSQLI_BOTH);

$consultaValor=mysqli_query($conexion, "SELECT
(SELECT sum(act_valor) FROM academico_actividades 
WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1),
(SELECT count(*) FROM academico_actividades 
WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1)
");
$valores = mysqli_fetch_array($consultaValor, MYSQLI_BOTH);
$porcentajeRestante = 100 - $valores[0];
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
                                <div class="page-title"><?=$frases[165][$datosUsuarioActual[8]];?> <?=$frases[6][$datosUsuarioActual[8]];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="calificaciones.php" onClick="deseaRegresar(this)"><?=$frases[6][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[165][$datosUsuarioActual[8]];?> <?=$frases[6][$datosUsuarioActual[8]];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">


							<?php include("info-carga-actual.php");?>
							
							<div class="panel">
								<header class="panel-heading panel-heading-purple"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?> </header>
								<div class="panel-body">
										<?php
										$enComun = mysqli_query($conexion, "SELECT * FROM academico_actividades
										WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_id!='".$_GET["idR"]."' AND act_estado=1
										");
										while($regComun = mysqli_fetch_array($enComun, MYSQLI_BOTH)){
										?>
										<p><a href="calificaciones-editar.php?idR=<?=$regComun['act_id'];?>"><?=$regComun['act_descripcion'];?></a></p>
										<?php }?>
									</div>
							 </div>	

                        </div>
						
                        <div class="col-sm-9">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="guardar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" method="post">
										<input type="hidden" value="12" name="id">
										<input type="hidden" value="<?=$calificacion['act_id'];?>" name="idR">
										<input type="hidden" value="<?=$calificacion['act_valor'];?>" name="valorCalificacion">
										

											<div class="form-group row">
												<label class="col-sm-2 control-label">Descripción</label>
												<div class="col-sm-10">
													<input type="text" name="contenido" value="<?=$calificacion['act_descripcion'];?>" class="form-control" autocomplete="off" required>
												</div>
											</div>
											
											<div class="form-group row">
													<label class="col-sm-2 control-label">Fecha</label>
													<div class="col-sm-4">
														<input type="date" name="fecha" value="<?=$calificacion['act_fecha'];?>" class="form-control" autocomplete="off" required>
													</div>
											</div>
											
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Indicador</label>
                                            <div class="col-sm-10">
												<?php
												$indicadoresConsulta = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga
												INNER JOIN academico_indicadores ON ind_id=ipc_indicador
												WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."'
												");
												?>
                                                <select class="form-control  select2" name="indicador" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($indicadoresDatos = mysqli_fetch_array($indicadoresConsulta, MYSQLI_BOTH)){
														$select = '';
														if($indicadoresDatos['ind_id']==$calificacion['act_id_tipo']) $select = 'selected';
													?>
                                                    	<option value="<?=$indicadoresDatos['ind_id'];?>" <?=$select;?>><?=$indicadoresDatos['ind_nombre']." (".$indicadoresDatos['ipc_valor']."%)"?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<?php if($datosCargaActual['car_evidencia']==1){?>
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Evidencia</label>
                                            <div class="col-sm-10">
												<?php
												$evidenciasConsulta = mysqli_query($conexion, "SELECT * FROM academico_evidencias
												");
												?>
                                                <select class="form-control  select2" name="evidencia" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($evidenciasDatos = mysqli_fetch_array($evidenciasConsulta, MYSQLI_BOTH)){
														$select = '';
														if($evidenciasDatos[0]==$calificacion['act_id_evidencia']) $select = 'selected';
													?>
                                                    	<option value="<?=$evidenciasDatos[0];?>" <?=$select;?>><?=$evidenciasDatos['evid_nombre']." (".$evidenciasDatos['evid_valor']."%)"?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										<?php }else{?>
										<input type="hidden" name="evidencia" class="form-control" value="<?=$calificacion['act_id_evidencia'];?>">
										<?php }?>

											<?php if($datosCargaActual['car_configuracion']==1){?>
												<p><mark>Este valor no debe superar al valor del indicador al que pertenece.</mark></p>
												<div class="form-group row">
													<label class="col-sm-2 control-label">Valor (%)</label>
													<div class="col-sm-2">
														<input type="text" name="valor" value="<?=$calificacion['act_valor'];?>" class="form-control" autocomplete="off" required>
													</div>
												</div>
											<?php }else{?>
												<div class="form-group row">
													<label class="col-sm-2 control-label">Valor (%) <mark>Automático</mark></label>
													<div class="col-sm-2">
														<input type="text" value="<?=$calificacion['act_valor'];?>" class="form-control" autocomplete="off" readonly>
													</div>
												</div>
											<?php }?>

										



										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
										
										<a href="#" name="calificaciones.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
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