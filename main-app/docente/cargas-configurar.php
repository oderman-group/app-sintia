<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0031';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");?>

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
                                <div class="page-title">Configuración de carga</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <?php include("includes/barra-superior-informacion-actual.php"); ?>
                    <div class="row">
						
                        <div class="col-sm-12">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header>Configuración de carga</header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form action="cargas-configurar-actualizar.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" method="post">
                                        <input type="hidden" value="<?=$datosCargaActual['car_valor_indicador'];?>" name="valorIndicadorActual">
                                        <input type="hidden" value="<?=$datosCargaActual['car_configuracion'];?>" name="valorCalificacionActual">
										
                                        <div class="form-group row">
                                            <label class="col-sm-4 control-label">ID carga</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$datosCargaActual['car_id'];?>" class="form-control" disabled>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">Primer acceso</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$datosCargaActual['car_primer_acceso_docente'];?>" class="form-control" disabled>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">Último acceso</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$datosCargaActual['car_ultimo_acceso_docente'];?>" class="form-control" disabled>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">Asignatura</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=strtoupper($datosCargaActual['mat_nombre']);?>" class="form-control" disabled>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">Curso</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=strtoupper($datosCargaActual['gra_nombre']." ".$datosCargaActual['gru_nombre']);?>" disabled class="form-control">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">Periodo</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$periodoConsultaActual;?>" disabled class="form-control">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">I.H</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$datosCargaActual['car_ih'];?>" disabled class="form-control">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">Director de grupo</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$dgArray[$datosCargaActual['car_director_grupo']];?>" disabled class="form-control">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">MAX. INDICADORES</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$datosCargaActual['car_maximos_indicadores'];?>" disabled class="form-control">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">MAX. CALIFICACIONES</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$datosCargaActual['car_maximas_calificaciones'];?>" disabled class="form-control">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">% Indicadores</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="indicadores">
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1" <?php if($datosCargaActual['car_valor_indicador']==1) echo "selected";?>>Manual</option>
                                                    <option value="0" <?php if($datosCargaActual['car_valor_indicador']=='0') echo "selected";?>>Automático</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">% Calificaciones</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="calificaciones">
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1" <?php if($datosCargaActual['car_configuracion']==1) echo "selected";?>>Manual</option>
                                                    <option value="0" <?php if($datosCargaActual['car_configuracion']=='0') echo "selected";?>>Automático</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">G. INFORME AUTOMÁTICO</label>
                                            <div class="col-sm-4">
                                                <input type="date" value="<?=$datosCargaActual['car_fecha_generar_informe_auto'];?>" name="fechaInforme" class="form-control">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">Posición</label>
                                            <div class="col-sm-4">
                                                <input type="number" value="<?=$datosCargaActual['car_posicion_docente'];?>" name="posicion" class="form-control">
                                            </div>
                                        </div>
										
										<div class="text-left">
                                            <a href="javascript:history.go(-1);" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i>Regresar</a>

                                            <button type="submit" class="btn  btn-info">
                                                <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
                                            </button>
                                        </div>
										
										

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