<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0082';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
//ESTUDIANTE ACTUAL
$consultaEstudianteActual = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_id='".$_GET["id"]."'");

$numEstudianteActual = mysqli_num_rows($consultaEstudianteActual);
$datosEstudianteActual = mysqli_fetch_array($consultaEstudianteActual, MYSQLI_BOTH);
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
                                <div class="page-title">Certificado</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li class="active">Certificado</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">


                        </div>
						
                        <div class="col-sm-9">

                                <?php
                                $ext='';
                                if($config['conf_id_institucion'] == 20){
                                    $ext='-2';
                                }
                                ?>
								<div class="panel">
									<header class="panel-heading panel-heading-purple">Certificado por áreas</header>
                                	<div class="panel-body">
                                        <form action="../compartido/matricula-certificado-areas<?=$ext; ?>.php" method="post" class="form-horizontal" enctype="multipart/form-data"  target="_blank">
                                            
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Estudiante</label>
                                                <div class="col-sm-10">
                                                    <select class="form-control  select2" name="id" required>
                                                    <option value=""></option>
                                                    <?php 
                                                    $c=mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_eliminado=0 ORDER BY mat_primer_apellido") ;
                                                    while($r=mysqli_fetch_array($c, MYSQLI_BOTH)){
                                                    ?>
                                                        <option value="<?php echo $r['mat_id']; ?>"><?php echo strtoupper($r["mat_primer_apellido"].' '.$r["mat_segundo_apellido"].' '.$r["mat_nombres"]);?></option>
                                                    <?php 
                                                    }
                                                    ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Desde que año</label>
                                                <div class="col-sm-10">
                                                    <select class="form-control  select2" name="desde" required>
                                                    <option value=""></option>
													<?php
                                                    $cont=1;
                                                    $con = date("Y")-7;
                                                    while($cont<=9)
                                                    {
                                                        if($_SESSION["bd"]==$con)
                                                            echo "<option value='".$con."' selected style='color:blue;'>".$con."</option>";
                                                        else
                                                            echo "<option value='".$con."'>".$con."</option>";
                                                        $con++;
                                                        $cont++;
                                                    }
                                                    ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Hasta que año</label>
                                                <div class="col-sm-10">
                                                    <select class="form-control  select2" name="hasta" required>
                                                    <option value=""></option>
													<?php
                                                    $cont=1;
                                                    $con = date("Y")-7;
                                                    while($cont<=9)
                                                    {
                                                        if($_SESSION["bd"]==$con)
                                                            echo "<option value='".$con."' selected style='color:blue;'>".$con."</option>";
                                                        else
                                                            echo "<option value='".$con."'>".$con."</option>";
                                                        $con++;
                                                        $cont++;
                                                    }
                                                    ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <input type="submit" class="btn btn-primary" value="Generar Certificado">
                                        </form>
                                    </div>
                                </div>

                                <div class="panel">
									<header class="panel-heading panel-heading-purple">Certificado por materias</header>
                                	<div class="panel-body">
                                        <form action="../compartido/matricula-certificado.php" method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank">
                                            
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Estudiante</label>
                                                <div class="col-sm-10">
                                                    <select class="form-control  select2" name="id" required>
                                                    <option value=""></option>
                                                    <?php 
                                                    $c=mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_eliminado=0 ORDER BY mat_primer_apellido") ;
                                                    while($r=mysqli_fetch_array($c, MYSQLI_BOTH)){
                                                    ?>
                                                        <option value="<?php echo $r['mat_id']; ?>"><?php echo strtoupper($r["mat_primer_apellido"].' '.$r["mat_segundo_apellido"].' '.$r["mat_nombres"]);?></option>
                                                    <?php 
                                                    }
                                                    ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Desde que año</label>
                                                <div class="col-sm-10">
                                                    <select class="form-control  select2" name="desde" required>
                                                    <option value=""></option>
													<?php
                                                    $cont=1;
                                                    $con = date("Y")-7;
                                                    while($cont<=9)
                                                    {
                                                        if($_SESSION["bd"]==$con)
                                                            echo "<option value='".$con."' selected style='color:blue;'>".$con."</option>";
                                                        else
                                                            echo "<option value='".$con."'>".$con."</option>";
                                                        $con++;
                                                        $cont++;
                                                    }
                                                    ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Hasta que año</label>
                                                <div class="col-sm-10">
                                                    <select class="form-control  select2" name="hasta" required>
                                                    <option value=""></option>
													<?php
                                                    $cont=1;
                                                    $con = date("Y")-7;
                                                    while($cont<=9)
                                                    {
                                                        if($_SESSION["bd"]==$con)
                                                            echo "<option value='".$con."' selected style='color:blue;'>".$con."</option>";
                                                        else
                                                            echo "<option value='".$con."'>".$con."</option>";
                                                        $con++;
                                                        $cont++;
                                                    }
                                                    ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <input type="submit" class="btn btn-primary" value="Generar Certificado">
                                        </form>
                                    </div>
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