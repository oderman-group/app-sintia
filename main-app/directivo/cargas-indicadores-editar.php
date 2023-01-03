<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0039';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php include("verificar-carga.php");?>
<?php
$consultaIndicadores=mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga
INNER JOIN academico_indicadores ON ind_id=ipc_indicador
WHERE ipc_id='".$_GET["idR"]."'");
$indicador = mysqli_fetch_array($consultaIndicadores, MYSQLI_BOTH);

$consultaSumaIndicadores=mysqli_query($conexion, "SELECT
(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=0),
(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1),
(SELECT count(*) FROM academico_indicadores_carga 
WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1)");
$sumaIndicadores = mysqli_fetch_array($consultaSumaIndicadores, MYSQLI_BOTH);
$porcentajePermitido = 100 - $sumaIndicadores[0];
$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);
$porcentajeRestante = ($porcentajeRestante + $indicador['ipc_valor']);
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
                                <div class="page-title"><?=$frases[165][$datosUsuarioActual[8]];?> <?=$frases[63][$datosUsuarioActual[8]];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="cargas-indicadores.php?carga=<?=$_GET["carga"];?>&docente=<?=$_GET["docente"];?>" onClick="deseaRegresar(this)"><?=$frases[63][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[165][$datosUsuarioActual[8]];?> <?=$frases[63][$datosUsuarioActual[8]];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">

                        </div>
						
                        <div class="col-sm-9">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="guardar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>&docente=<?=$_GET["docente"];?>" method="post">
										<input type="hidden" value="56" name="id">
										<input type="hidden" value="<?=$indicador['ipc_id'];?>" name="idR">
										<input type="hidden" value="<?=$indicador['ipc_indicador'];?>" name="idInd">
										<input type="hidden" value="<?=$indicador['ipc_valor'];?>" name="valorIndicador">

											<div class="form-group row">
												<label class="col-sm-2 control-label">ID Relación</label>
												<div class="col-sm-2">
													<input type="text" name="idRelacion" class="form-control" value="<?=$indicador['ipc_indicador'];?>" autocomplete="off" disabled>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">ID Indicador</label>
												<div class="col-sm-2">
													<input type="text" name="idIndicador" class="form-control" value="<?=$indicador['ipc_id'];?>" autocomplete="off" disabled>
												</div>
											</div>
										
											<div class="form-group row">
												<label class="col-sm-2 control-label">Descripción</label>
												<div class="col-sm-10">
													<input type="text" name="contenido" class="form-control" value="<?=$indicador['ind_nombre'];?>" autocomplete="off" required>
												</div>
											</div>

											
												<p><b>Valor máximo restante:</b> <?=$porcentajeRestante;?>%. <mark>Si superas este valor, el sistema lo ajustará automáticamente.</mark></p>
												<div class="form-group row">
													<label class="col-sm-2 control-label">Valor (%)</label>
													<div class="col-sm-2">
														<input type="text" name="valor" class="form-control" value="<?=$indicador['ipc_valor'];?>" autocomplete="off" required>
													</div>
												</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Creado por el docente</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="creado" required>
                                                    <option value="0">Seleccione una opción</option>
													<option value="1" <?php if($indicador['ipc_creado']==1) echo "selected";?>>SI</option>
													<option value="0" <?php if($indicador['ipc_creado']=='0') echo "selected";?>>NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<?php if($datosCargaActual['car_saberes_indicador']==1){?>
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Tipo de evaluación</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="saberes" required>
                                                    <option value="0">Seleccione una opción</option>
													<option value="1" <?php if($indicador['ipc_evaluacion']==1) echo "selected";?>>Saber saber (55%)</option>
													<option value="2" <?php if($indicador['ipc_evaluacion']==2) echo "selected";?>>Saber hacer (35%)</option>
													<option value="3" <?php if($indicador['ipc_evaluacion']==3) echo "selected";?>>Saber ser (10%)</option>
                                                </select>
                                            </div>
                                        </div>
										<?php }else{?>
										<input type="hidden" name="saberes" class="form-control" value="<?=$indicador['ipc_evaluacion'];?>">
										<?php }?>


										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
										
										<a href="#" name="cargas-indicadores.php?carga=<?=$_GET["carga"];?>&docente=<?=$_GET["docente"];?>" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
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