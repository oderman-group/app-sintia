<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0049';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaDatosBD=mysqli_query($conexion, "SELECT * FROM academico_formatos WHERE form_id='".$_GET["idF"]."'");
$datosConsultaBD = mysqli_fetch_array($consultaDatosBD, MYSQLI_BOTH);
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
                                <div class="page-title"><?=$frases[225][$datosUsuarioActual[8]];?>: <b><?=$datosConsultaBD['form_nombre'];?></b></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="formatos.php"><?=$frases[221][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[225][$datosUsuarioActual[8]];?>: <b><?=$datosConsultaBD['form_nombre'];?></b></li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<?php include("info-carga-actual.php");?>
									
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=strtoupper($frases[221][$datosUsuarioActual['uss_idioma']]);?> </header>
										<div class="panel-body">
											<p>Puedes cambiar a otro formato rápidamente para monitorear a los evaluados.</p>
											<?php
											$registrosEnComun = mysqli_query($conexion, "SELECT * FROM academico_formatos 
											WHERE form_id!='".$_GET["idF"]."' ORDER BY form_id DESC");
											while($regComun = mysqli_fetch_array($registrosEnComun, MYSQLI_BOTH)){
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?idF=<?=$regComun['form_id'];?>"><?=$regComun['form_nombre'];?></a></p>
											<?php }?>
										</div>
                                    </div>
									
									<?php include("../compartido/publicidad-lateral.php");?>
									
								</div>
									
								<div class="col-md-8 col-lg-9">
									
									<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="guardar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" method="post">
										<input type="hidden" value="42" name="id">
										<input type="hidden" value="<?=$_GET["idF"];?>" name="idF">
										
											<div class="form-group row">
                                            <label class="col-sm-2 control-label">Evaluado</label>
                                            <div class="col-sm-10">
												<?php
												$datosConsulta = mysqli_query($conexion, "SELECT * FROM usuarios
												WHERE uss_tipo=4");
												?>
                                                <select class="form-control  select2" name="evaluado" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
													?>
                                                    	<option value="<?=$datos['uss_id'];?>" <?php if($datos['uss_id']==$_GET["idUsr"]){echo "selected";}?>><?=$datos['uss_nombre']?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
                                </div>
                            </div>
									
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$datosConsultaBD['form_nombre'];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>

                                        <div class="card-body">

                                        <div class="table-responsive">
                                            <table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[139][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[108][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consultaCat = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones 
													 WHERE eva_formato='".$_GET["idF"]."'");
													 $contCat = 1;
													 $contReg = 1;
													 while($resultadoCat = mysqli_fetch_array($consultaCat, MYSQLI_BOTH)){
													 ?>
													<tr>
                                                        <td colspan="3" style="text-align: center; font-weight: bold;"><?=$resultadoCat['eva_nombre'];?></td>
                                                    </tr>
													<?php
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_preguntas 
													 INNER JOIN academico_actividad_evaluacion_preguntas ON evp_id_pregunta=preg_id AND evp_id_evaluacion='".$resultadoCat['eva_id']."'
													 ORDER BY preg_id DESC");
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $colorDefault = 'blue';
														 if($resultado['preg_critica']==1) $colorDefault = 'red';
														 if($calificacion['act_registrada']==1){
															 //Consulta de calificaciones si ya la tienen puestas.
                                                             $consultaNotas=mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones_resultados
															 WHERE cal_id_estudiante=".$resultado[0]." AND cal_id_actividad='".$_GET["idR"]."'");
															 $notas = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);
														 }
													 ?>
                                                    
													<tr style="color: <?=$colorDefault ;?>;">
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['preg_descripcion'];?></td>
														<td><input type="text" style="text-align: center;" size="5" maxlength="3" name="P<?=$contReg;?>" autocomplete="off"></td>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
													$contCat++;
												 }
												?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
										
										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;

												<a href="#" name="formatos.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
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
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
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