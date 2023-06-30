<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0022';?>
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
                            <div class=" pull-left">
                                <div class="page-title">Agregar Asignatura</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="asignaturas.php" onClick="deseaRegresar(this)"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Agregar Asignatura</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
                        <div class="col-sm-12">
                                <?php include("../../config-general/mensajes-informativos.php"); ?>


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="asignaturas-guardar.php" method="post" enctype="multipart/form-data">
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre de la Asignatura <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="nombreM" class="form-control" onchange="generarSiglas(this)">
                                            </div>
                                        </div>

                                        <script type="text/javascript">
                                            function generarSiglas(datos){
                                                var asignatura = datos.value;
                                                var siglas = asignatura.substring(0, 3);
                                                document.getElementById("siglasM").value = siglas.toUpperCase();
                                            }
                                        </script>    
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre corto, Abreviatura o Siglas de la asignatura</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="siglasM" id="siglasM" class="form-control">
                                                <span style="color: #6017dc;">Este valor se usa para mostrar de forma abreviada el nombre de la asignatura en algunos informes.</span>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Área académica a la cual pertenece esta asignatura <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
                                                <select class="form-control  select2" name="areaM" required>
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    try{
                                                        $cAreas=mysqli_query($conexion, "SELECT ar_id, ar_nombre, ar_posicion FROM academico_areas;");
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
                                                    while($rA=mysqli_fetch_array($cAreas, MYSQLI_BOTH)){
                                                        echo'<option value="'.$rA["ar_id"].'">'.$rA["ar_nombre"].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>   
                                        
										<?php if($config['conf_agregar_porcentaje_asignaturas']=='SI'){ ?>
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Porcentaje</label>
                                                <div class="col-sm-4">
                                                    <input type="text" name="porcenAsigna" id="porcenAsigna" class="form-control">
                                                </div>
                                            </div>
                                        <?php } ?>


										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
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