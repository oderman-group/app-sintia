<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0083';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
require_once("../class/Estudiantes.php");
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
                                <div class="page-title">Cambiar de grupo</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li class="active">Cambiar de grupo</li>
                            </ol>
                        </div>
                    </div>
                    <?php include("../../config-general/mensajes-informativos.php"); ?>
                    <div class="row">
					
                        <div class="col-sm-12">
                        
                                <?php
                                    $id="";
                                    if(!empty($_GET["id"])){ $id=base64_decode($_GET["id"]);}
                                    $e = Estudiantes::obtenerDatosEstudiante($id);
                                ?>

								<div class="panel">
									<header class="panel-heading panel-heading-purple">Cambiar de grupo</header>
                                	<div class="panel-body">

                                   
                                    <form action="estudiantes-cambiar-grupo-estudiante.php" method="post" class="form-horizontal" enctype="multipart/form-data">
                                        <input type="hidden" value="<?=$e['mat_id'];?>" name="estudiante">
										
											
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Estudiante</label>
                                            <div class="col-sm-1">
                                                <input type="text" name="codigoE" class="form-control" autocomplete="off" value="<?=$e['mat_id'];?>" readonly>
                                            </div>
                                            
                                            <div class="col-sm-9">
                                                <input type="text" name="nombre" class="form-control" autocomplete="off" value="<?=Estudiantes::NombreCompletoDelEstudiante($e);?>" readonly>
                                            </div>
                                        </div>

										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Curso</label> 
                                            
                                            <?php 
                                            $gradoActual=Grados::obtenerGrado($e["mat_grado"]);											
											?>
                                            <div class="col-sm-1">
                                            <input type="text" name="cursoNuevo" class="form-control" autocomplete="off" value="<?= $gradoActual["gra_id"]?>" readonly> 
                                            </div>
                                            <div class="col-sm-9">
                                            <input type="text"  class="form-control" autocomplete="off" value="<?= $gradoActual["gra_nombre"]?>" readonly> 
                                            </div>
                                           
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Grupo</label>
                                          	<?php 
                                            try{
                                                $consulta_cargas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_grupos WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
                                            } catch (Exception $e) {
                                                include("../compartido/error-catch-to-report.php");
                                            }
											?>
                                            <div class="col-sm-9">
                                                <select class="form-control  select2" name="grupoNuevo" required>
                                                <option value="0"></option>
                                                 <?php 
												 while($c = mysqli_fetch_array($consulta_cargas, MYSQLI_BOTH)){
												 	if($c["gru_id"]==$e['mat_grupo'])
														echo '<option value="'.$c["gru_id"].'" selected style="color:blue; font-weight:bold;">Actual: '.$c["gru_nombre"].'</option>';	
													else
														echo '<option value="'.$c["gru_id"].'">'.$c["gru_nombre"].'</option>'; 
												 }
												 ?>
                                                </select>
                                            </div>
                                        </div>

                                        <input type="submit" class="btn btn-success" value="Hacer cambio" name="consultas">
                                        <a href="javascript:void(0);" name="estudiantes.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
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
</html>