<?php
if(isset($_POST["consultas"])){
	//TODAS LAS CARGAS Y TODOS LOS PERIODOS
	if($_POST["carga"]==0 and $_POST["periodo"]==0){
		header("Location:../compartido/reporte-resumen-periodos.php?estudiante=".$_POST["estudiante"]."&nombre=".$_POST["nombre"]);
		exit();
	}
	//TODAS LAS CARGAS Y UN PERIODO ESPECIFICO
	elseif($_POST["carga"]==0 and $_POST["periodo"]!=0){
		header("Location:../compartido/reporte-notas-todas.php?estudiante=".$_POST["estudiante"]."&periodo=".$_POST["periodo"]."&nombre=".$_POST["nombre"]);
		exit();
	}
	//UNA CARGA ESPECIFICA Y TODOS LOS PERIODOS
	elseif($_POST["carga"]!=0 and $_POST["periodo"]==0){
		header("Location:../compartido/reporte-resumen-periodos.php?estudiante=".$_POST["estudiante"]."&nombre=".$_POST["nombre"]);
		exit();
	}
	//UNA CARGA Y UN PERIODO ESPECIFICO
	elseif($_POST["carga"]!=0 and $_POST["periodo"]!=0){
		header("Location:../compartido/reporte-notas-estudiante.php?estudiante=".$_POST["estudiante"]."&periodo=".$_POST["periodo"]."&carga=".$_POST["carga"]."&nombre=".$_POST["nombre"]);
		exit();
	}
}
?>
<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0079';?>
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
                                <div class="page-title">Consultas</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li class="active">Consultas</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">


                        </div>
						
                        <div class="col-sm-9">
                          
                                <?php
                                if(isset($_POST["idE"]) or isset($_GET["idE"])){
                                    $consultaE = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_matricula='".$_POST["idE"]."' OR mat_id='".$_GET["idE"]."'");
                                    $e = mysqli_fetch_array($consultaE, MYSQLI_BOTH);
                                }
                                ?>

								<div class="panel">
									<header class="panel-heading panel-heading-purple">Consultas</header>
                                	<div class="panel-body">

                                   
                                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="form-horizontal" enctype="multipart/form-data">
										
												
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">C&oacute;digo Estudiantil</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="idE" class="form-control" autocomplete="off"  value="<?=$e[1];?>">
                                            </div>

                                            <input type="submit" class="btn btn-info" value="Consultar">
                                        </div>	
                                    </form>

                                    <hr>

                                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank">
                                        <input type="hidden" value="<?=$e[0];?>" name="estudiante">
                                        <input type="hidden" value="8" name="id">
										
											
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Estudiante</label>
                                            <div class="col-sm-2">
                                                <input type="text" name="codigoE" class="form-control" autocomplete="off" value="<?=$e[1];?>" readonly>
                                            </div>
                                            
                                            <div class="col-sm-4">
                                                <input type="text" name="nombre" class="form-control" autocomplete="off" value="<?=$e['mat_primer_apellido']." ".$e['mat_segundo_apellido']." ".$e['mat_nombres']." ".$e['mat_nombre2'];?>" readonly>
                                            </div>
                                        </div>

										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Carga Acad&eacute;mica</label> 
                                          	<?php 
											$consulta_cargas = mysqli_query($conexion, "SELECT * FROM academico_cargas, academico_materias WHERE car_curso='".$e[6]."' AND car_grupo='".$e[7]."' AND mat_id=car_materia");
											?>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="carga" required>
                                                <option value="0"></option>
                                                <?php 
                                                    while($c = mysqli_fetch_array($consulta_cargas, MYSQLI_BOTH)){
                                                        echo '<option value="'.$c[0].'">COD. '.$c[0].' - '.$c["mat_nombre"].'</option>';	
                                                    }
												 ?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Periodo</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="periodo" required>
                                                <option value="0"></option>
                                                <?php
                                                    $p = 1;
                                                    while($p<=$config[19]){
                                                        echo '<option value="'.$p.'">Periodo '.$p.'</option>';
                                                        $p++;
                                                    }
                                                ?>
                                                </select>
                                            </div>
                                        </div>

                                        <input type="submit" class="btn btn-success" value="Consultar Informe" name="consultas">
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