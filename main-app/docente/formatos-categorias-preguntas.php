<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0060';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
	
	<?php
	$consultaEvaluacion=mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones 
	WHERE eva_id='".$_GET["idE"]."' AND eva_estado=1");
	$evaluacion = mysqli_fetch_array($consultaEvaluacion, MYSQLI_BOTH);

	
	//Cantidad de preguntas de la evaluación
	$preguntasConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluacion_preguntas
	INNER JOIN academico_actividad_preguntas ON preg_id=evp_id_pregunta
	WHERE evp_id_evaluacion='".$_GET["idE"]."'
	ORDER BY preg_id DESC");
	
	$cantPreguntas = mysqli_num_rows($preguntasConsulta);

	?>

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
                                <div class="page-title"><?=$evaluacion['eva_nombre'];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="formatos.php"><?=$frases[221][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i>
								<li><a class="parent-item" href="formatos-categorias.php?idF=<?=$_GET["idF"];?>"><?=$frases[222][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$evaluacion['eva_nombre'];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">

							<div class="col-md-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?></header>
                                        <div class="panel-body">
											
										</div>
									</div>
								
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[222][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$evaluacionesEnComun = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones
											WHERE eva_formato='".$_GET["idF"]."' AND eva_id!='".$_GET["idE"]."' AND eva_estado=1
											ORDER BY eva_id DESC");
											while($evaComun = mysqli_fetch_array($evaluacionesEnComun, MYSQLI_BOTH)){
											?>
												<p><a href="formatos-categorias-preguntas.php?idE=<?=$evaComun['eva_id'];?>&idF=<?=$_GET["idF"];?>"><?=$evaComun['eva_nombre'];?></a></p>
											<?php }?>
										</div>
                                    </div>
									
							</div>
							
							<div class="col-md-6" style="width: 100%; height: 800px; overflow-y: scroll;">
								
								
								
								<div class="row" style="margin-bottom: 10px;">
									<div class="col-sm-12">
										<a href="evaluaciones.php" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i>Regresar</a>
										
										<div class="btn-group">
											<a href="formatos-categorias-preguntas-agregar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>&idE=<?=$_GET["idE"];?>&idF=<?=$_GET["idF"];?>" id="addRow" class="btn deepPink-bgcolor"><i class="fa fa-plus"></i> Agregar pregunta </a>
										</div>
									</div>
								</div>
								

											<?php
											$contPreguntas = 1;
											while($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)){
												$colorDefault = 'blue';
												if($preguntas['preg_critica']==1) $colorDefault = 'red';
											?>
												<div class="panel" id="pregunta<?=$preguntas['preg_id'];?>">
													<div class="card-head">
																		<button type="button" id ="panel-<?=$preguntas['preg_id'];?>"
																		   class = "mdl-button mdl-js-button mdl-button--icon pull-right" 
																		   data-upgraded = ",MaterialButton">
																		   <i class = "material-icons">more_vert</i>
																		</button>

																		<ul class = "mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect"
																		   data-mdl-for="panel-<?=$preguntas['preg_id'];?>">
																		   <li class = "mdl-menu__item"><a href="formatos-categorias-preguntas-editar.php?idR=<?=$preguntas['preg_id'];?>&idE=<?=$_GET["idE"];?>&idF=<?=$_GET["idF"];?>"><i class="fa fa-edit"></i> Editar pregunta</a></li>
																		   <li class = "mdl-menu__item"><a href="#" name="guardar.php?get=27&idP=<?=$preguntas['preg_id'];?>&idE=<?=$_GET["idE"];?>" onClick="deseaEliminar(this)"><i class="fa fa-trash"></i>Eliminar pregunta</a></li>
																		</ul>
													</div>
													
													<header class="panel-heading panel-heading-<?=$colorDefault;?>"><?php echo $preguntas['preg_descripcion'];?></header>
													<div class="panel-body"></div>
												</div>
											<?php
												$contPreguntas ++;
											}
											?>

								</div>
						

									
								<div class="col-md-3">
									<?php include("../compartido/publicidad-lateral.php");?>
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
        
        <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
        <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
		<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
        <!-- bootstrap -->
        <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
        <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
        <!-- Common js-->
		<script src="../../config-general/assets/js/app.js" ></script>
		<!-- notifications -->
		<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
		<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
        
        <script src="../../config-general/assets/js/layout.js" ></script>
		<script src="../../config-general/assets/js/theme-color.js" ></script>
		<!-- Material -->
		<script src="../../config-general/assets/plugins/material/material.min.js"></script>
		<script src="../../config-general/assets/js/pages/material-select/getmdl-select.js" ></script>
		<script  src="../../config-general/assets/plugins/material-datetimepicker/moment-with-locales.min.js"></script>
		<script  src="../../config-general/assets/plugins/material-datetimepicker/bootstrap-material-datetimepicker.js"></script>
		<script  src="../../config-general/assets/plugins/material-datetimepicker/datetimepicker.js"></script>
		<!-- end js include path -->
		
		
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/course_details.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:31:36 GMT -->
</html>