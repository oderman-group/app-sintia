<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0042';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php 
$consultaHorario=mysqli_query($conexion, "SELECT hor_id_carga, hor_dia, hor_desde, hor_hasta FROM academico_horarios WHERE hor_id=".$_GET["id"].";");
$rHorario=mysqli_fetch_array($consultaHorario, MYSQLI_BOTH);
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
                                <div class="page-title">Editar Horarios</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="cargas-horarios.php?id=<?=$_GET["id"];?>" onClick="deseaRegresar(this)">Horarios</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar Horarios</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">


                        </div>
						
                        <div class="col-sm-9">


								<div class="panel">
									<header class="panel-heading panel-heading-purple">Horarios</header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="cargas-horarios-actualizar.php" method="post">
                                        <input type="hidden" name="idH" value="<?=$_GET["id"];?>">
                                        <input type="hidden" name="idC" value="<?=$rHorario["hor_id_carga"];?>">
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Dia</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="diaH" required>
                                                    <option value="">Seleccione una opci n</option>
                                                    <option value="1" <?php if($rHorario["hor_dia"]==1){echo "selected";}?>>Domingos</option>
                                                    <option value="2" <?php if($rHorario["hor_dia"]==2){echo "selected";}?>>Lunes</option>
                                                    <option value="3" <?php if($rHorario["hor_dia"]==3){echo "selected";}?>>Martes</option>
                                                    <option value="4" <?php if($rHorario["hor_dia"]==4){echo "selected";}?>>Miercoles</option>
                                                    <option value="5" <?php if($rHorario["hor_dia"]==5){echo "selected";}?>>Jueves</option>
                                                    <option value="6" <?php if($rHorario["hor_dia"]==6){echo "selected";}?>>Viernes</option>
                                                    <option value="7" <?php if($rHorario["hor_dia"]==7){echo "selected";}?>>Sabados</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Inicio</label>
											<div class="col-sm-2">
                                                <input name="inicioH" data-format="hh:mm:ss" type="time" class="form-control" value="<?=$rHorario["hor_desde"]?>">
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Fin</label>
											<div class="col-sm-2">
                                                <input name="finH" data-format="hh:mm:ss" type="time" class="form-control" value="<?=$rHorario["hor_hasta"]?>">
											</div>
										</div>


										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
										
										<a href="#" name="cargas-horarios.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
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