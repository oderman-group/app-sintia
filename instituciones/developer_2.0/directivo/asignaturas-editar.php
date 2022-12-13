<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0021';?>
<?php include("verificar-permiso-pagina.php");?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>

	<!--bootstrap -->
    <link href="../../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<!-- Theme Styles -->
    <link href="../../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<!-- dropzone -->
    <link href="../../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
    <!--tagsinput-->
    <link href="../../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
    <!--select2-->
    <link href="../../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
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
						
						<div class="col-sm-3">


                        </div>
						
                        <div class="col-sm-9">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                    <?php
                                    $r_materia=mysql_fetch_array(mysql_query("SELECT mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area FROM academico_materias WHERE mat_id=".$_GET["id"].";",$conexion));
                                    ?>
                                   
									<form name="formularioGuardar" action="asignaturas-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" value="<?=$_GET["id"]?>" name="idM">
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Codigo</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="codigoM" class="form-control" value="<?=$r_materia["mat_codigo"]?>">
                                            </div>
                                        </div>
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre de la Asignatura</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="nombreM" class="form-control" value="<?=$r_materia["mat_nombre"]?>">
                                            </div>
                                        </div>
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Siglas</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="siglasM" class="form-control" value="<?=$r_materia["mat_siglas"]?>">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Area acad&eacute;mica</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="areaM" required>
                                                    <option value="">Seleccione una opci n</option>
                                                <?php
                                                $c_areas=mysql_query("SELECT ar_id, ar_nombre, ar_posicion FROM academico_areas;",$conexion);
                                                while($r_a=mysql_fetch_array($c_areas)){
                                                    if($r_materia["mat_area"]==$r_a["ar_id"]){
                                                        echo'<option value="'.$r_a["ar_id"].'" selected>'.$r_a["ar_nombre"].'</option>';
                                                        }else{
                                                    echo'<option value="'.$r_a["ar_id"].'">'.$r_a["ar_nombre"].'</option>';
                                                        }
                                                    }
                                                ?>
                                                </select>
                                            </div>
                                        </div>


										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
                                    </form>
                                </div>
                            </div>
                        </div>
						
                    </div>

                </div>
                <!-- end page content -->
             <?php include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    <!-- start js include path -->
    <script src="../../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <script src="../../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <script src="../../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../../config-general/assets/js/app.js" ></script>
    <script src="../../../config-general/assets/js/layout.js" ></script>
	<script src="../../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>