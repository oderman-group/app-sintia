<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0116';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
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
                            <div class="pull-left">
                                <div class="page-title"><?=$frases[249][$datosUsuarioActual[8]];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header><?=$frases[249][$datosUsuarioActual[8]];?></header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/reporte-disciplina-sacar.php" method="post" enctype="multipart/form-data" target="_blank">
										<input type="hidden" name="id" value="12">

										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[26][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">
                                                <?php
												$datosConsulta = mysqli_query($conexion, "SELECT * FROM academico_grados
												WHERE gra_estado=1");
												?>
                                                <select class="form-control  select2" name="grado" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
													?>
                                                    	<option value="<?=$datos['gra_id'];?>"><?=$datos['gra_nombre']?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[250][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">
                                                <?php
												$datosConsulta = mysqli_query($conexion, "SELECT * FROM academico_grupos");
												?>
                                                <select class="form-control  select2" name="grupo" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
													?>
                                                    	<option value="<?=$datos['gru_id'];?>"><?=$datos['gru_nombre']?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										
										
										
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Desde</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control" name="desde" value="<?=date("Y");?>-01-01">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Hasta</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control" name="hasta" value="<?=date("Y-m-d");?>">
                                            </div>
                                        </div>
										
										<hr>
										<h4 style="color: darkblue;">Filtros Opcionales</h4>
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[55][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">
                                                <?php
												$datosConsulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
												INNER JOIN usuarios ON uss_id=mat_id_usuario
												WHERE (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
												?>
                                                <select class="form-control  select2" name="est">
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
													?>
                                                    	<option value="<?=$datos['uss_id'];?>"><?=$datos['uss_nombre']?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[248][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">
                                                <select name="falta" class="form-control select2">
													<option value="">Seleccione una opción</option>
												<?php
												$datosConsulta = mysqli_query($conexion, "SELECT * FROM disciplina_faltas 
												INNER JOIN disciplina_categorias ON dcat_id=dfal_id_categoria");
												while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
												?>	
                                                  <option value="<?=$datos['dfal_id'];?>"><?=$datos['dfal_codigo'].". ".$datos['dfal_nombre'];?></option>	
												<?php }?>	
                                                </select>
                                            </div>
                                        </div>
										
										
										<div class="form-group row">
												<label class="col-sm-2 control-label"><?=$frases[75][$datosUsuarioActual[8]];?></label>
												<div class="col-sm-10">
													<?php
													$datosConsulta = mysqli_query($conexion, "SELECT * FROM usuarios
													WHERE (uss_tipo=2 OR uss_tipo=5)
													ORDER BY uss_tipo, uss_nombre");
													?>
													<select class="form-control  select2" name="usuario">
														<option value="">Seleccione una opción</option>
														<?php
														while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$datos['uss_id'];?>"><?=$datos['uss_nombre']?></option>
														<?php }?>
													</select>
												</div>
											</div>
										
										
										<input type="submit" class="btn btn-primary" value="Sacar reporte">&nbsp;
										
										<a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>

                                    </form>
                                </div>
                            </div>
                        </div>
						
                        <div class="col-sm-3">
                            <?php include("../compartido/publicidad-lateral.php");?>
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

</html>