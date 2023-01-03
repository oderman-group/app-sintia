<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0006';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");?>
<script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js"></script>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
	
<?php
$consultaDatos=mysqli_query($conexion, "SELECT * FROM academico_actividad_tareas WHERE tar_id='".$_GET["idR"]."' AND tar_estado=1");
$datosConsulta = mysqli_fetch_array($consultaDatos, MYSQLI_BOTH);

?>

	<input type="hidden" id="idR" name="idR" value="<?=$_GET["idR"];?>">
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
                                <div class="page-title"><?=$datosConsulta['tar_titulo'];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="actividades.php"><?=$frases[112][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$datosConsulta['tar_titulo'];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">

							<div class="col-md-3">
								
									<div class="panel">
										<header class="panel-heading panel-heading-blue"><?=$datosCargaActual['mat_nombre'];?> </header>
										<div class="panel-body">
											<div class="card">
											<div class="card-head card-topline-aqua">
												<header><?=$datosConsulta['tar_titulo'];?></header>
											</div>
											<div class="card-body no-padding height-9">
												<div class="profile-desc">
													<?=$datosConsulta['tar_descripcion'];?>
												</div>
												<ul class="list-group list-group-unbordered">
													<li class="list-group-item">
														<b><?=$frases[130][$datosUsuarioActual[8]];?> </b>
														<div class="profile-desc-item pull-right"><?=$datosConsulta['tar_fecha_disponible'];?></div>
													</li>
													<li class="list-group-item">
														<b><?=$frases[131][$datosUsuarioActual[8]];?> </b>
														<div class="profile-desc-item pull-right"><?=$datosConsulta['tar_fecha_entrega'];?></div>
													</li>
												</ul>

											</div>
										</div>
										</div>
                                    </div>	
								
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[112][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$evaluacionesEnComun = mysqli_query($conexion, "SELECT * FROM academico_actividad_tareas
											WHERE tar_id_carga='".$cargaConsultaActual."' AND tar_periodo='".$periodoConsultaActual."' AND tar_id!='".$_GET["idR"]."' AND tar_estado=1
											ORDER BY tar_id DESC
											");
											while($evaComun = mysqli_fetch_array($evaluacionesEnComun, MYSQLI_BOTH)){
											?>
												<p><a href="actividades-entregas.php?idR=<?=$evaComun['tar_id'];?>"><?=$evaComun['tar_nombre'];?></a></p>
											<?php }?>
										</div>
                                    </div>
								
								<?php include("../compartido/publicidad-lateral.php");?>

									
							</div>
							
							<div class="col-md-9">
								
								
								
								<div class="row" style="margin-bottom: 10px;">
									<div class="col-sm-12">
										<a href="actividades.php" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i>Regresar</a>
										
									</div>
								</div>
								
								<span id="respuestaGuardar"></span>
								
								<div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$evaluacion['eva_nombre'];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[61][$datosUsuarioActual[8]];?></th>
														<th>Enviada</th>
														<th>Hace</th>
														<th>Descargar</th>
														<th>Comentario</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas
													 INNER JOIN usuarios ON uss_id=mat_id_usuario
													 WHERE mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido, mat_segundo_apellido, mat_nombres");
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$consultaDatos1=mysqli_query($conexion, "SELECT ent_fecha, MOD(TIMESTAMPDIFF(MINUTE, ent_fecha, now()),60), MOD(TIMESTAMPDIFF(SECOND, ent_fecha, now()),60), ent_archivo, ent_comentario, ent_archivo2, ent_archivo3 FROM academico_actividad_tareas_entregas 
														WHERE ent_id_estudiante='".$resultado['mat_id']."' AND ent_id_actividad='".$_GET["idR"]."'");
														 $datos1 = mysqli_fetch_array($consultaDatos1, MYSQLI_BOTH);
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?></td>
														<td><?=$datos1['ent_fecha'];?></td>
														<td><?php if($datos1[1]>0){echo $datos1[1]." Min. y ";} if($datos1[2]>0){echo $datos1[2]." Seg.";}?></td>
														<td>
														<?php if($datos1['ent_archivo']!="" and file_exists('../files/tareas-entregadas/'.$datos1['ent_archivo'])){?>
															<a href="../files/tareas-entregadas/<?=$datos1['ent_archivo'];?>" target="_blank">Archivo 1</a><br>
														<?php }?>
															
														<?php if($datos1['ent_archivo2']!="" and file_exists('../files/tareas-entregadas/'.$datos1['ent_archivo2'])){?>
															<a href="../files/tareas-entregadas/<?=$datos1['ent_archivo2'];?>" target="_blank">Archivo 2</a><br>
														<?php }?>
															
														<?php if($datos1['ent_archivo3']!="" and file_exists('../files/tareas-entregadas/'.$datos1['ent_archivo3'])){?>
															<a href="../files/tareas-entregadas/<?=$datos1['ent_archivo3'];?>" target="_blank">Archivo 3</a><br>
														<?php }?>	
														
														</td>
														<td style="font-size: 11px;"><?=$datos1['ent_comentario'];?></td>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
													  ?>
                                                </tbody>
                                            </table>
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
        <!-- Common js-->
		<script src="../../config-general/assets/js/app.js" ></script>
		<!-- notifications -->
		<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
		<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
		<!-- data tables -->
		<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
		<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
		<script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
        
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