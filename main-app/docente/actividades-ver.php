<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0002';?>
<?php include("../compartido/head.php");?>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
<?php
$consultaActivad=mysqli_query($conexion, "SELECT * FROM academico_actividad_tareas 
WHERE tar_id='".$_GET["idR"]."' AND tar_estado=1");
$actividad = mysqli_fetch_array($consultaActivad, MYSQLI_BOTH);

if($actividad[0]==""){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=105";</script>';
	exit();
}
$consultaFecha=mysqli_query($conexion, "SELECT DATEDIFF(tar_fecha_disponible, now()), DATEDIFF(tar_fecha_entrega, now()) FROM academico_actividad_tareas 
WHERE tar_id='".$_GET["idR"]."' AND tar_estado=1");
$fechas = mysqli_fetch_array($consultaFecha, MYSQLI_BOTH);
if($fechas[0]>0){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=206&fechaD='.$actividad['tar_fecha_disponible'].'&diasF='.$fechas[0].'";</script>';
	exit();
}
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
                                <div class="page-title"><?=$actividad['tar_titulo'];?></div>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="actividades.php"><?=$frases[112][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$actividad['tar_titulo'];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN PROFILE SIDEBAR -->
                            <div class="profile-sidebar">

                                <div class="card">
                                    <div class="card-head card-topline-aqua">
                                        <header>Información</header>
                                    </div>
                                    <div class="card-body no-padding height-9">
                                        <div class="work-monitor work-progress">
                                            <div class="states">
                                                <div class="info">
                                                    <div class="desc pull-left">Enviadas</div>
                                                    <div class="percent pull-right">30%</div>
                                                </div>
												
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 30%">
                                                        <span class="sr-only">90% </span>
                                                    </div>
                                                </div>
												
                                            </div>
											
                                            <div class="states">
                                                <div class="info">
                                                    <div class="desc pull-left">faltantes</div>
                                                    <div class="percent pull-right">70%</div>
                                                </div>
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 70%">
                                                        <span class="sr-only">85% </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END BEGIN PROFILE SIDEBAR -->
                            <!-- BEGIN PROFILE CONTENT -->
                            <div class="profile-content">
                                <div class="row">
                                     <div class="card">
                                         <div class="card-topline-aqua">
                                             <header></header>
                                         </div>
											<div class="white-box">
					                            <!-- Nav tabs -->
					                            <div class="p-rl-20">
						                            <ul class="nav customtab nav-tabs" role="tablist">
						                                <li class="nav-item"><a href="#tab1" class="nav-link active"  data-toggle="tab" >Detalles</a></li>
						                                <li class="nav-item"><a href="#tab2" class="nav-link" data-toggle="tab">Enviar actividad</a></li>
						                            </ul>
					                            </div>
					                            <!-- Tab panes -->
					                            <div class="tab-content">
					                                <div class="tab-pane active fontawesome-demo" id="tab1">
															<div id="biography" >
							                                    <div class="row">
							                                        <div class="col-md-3 col-6 b-r"> <strong>Titulo</strong>
							                                            <br>
							                                            <p class="text-muted"><?=$actividad['tar_titulo'];?></p>
							                                        </div>
							                                        <div class="col-md-3 col-6 b-r"> <strong>Disponible desde</strong>
							                                            <br>
							                                            <p class="text-muted"><?=$actividad['tar_fecha_disponible'];?></p>
							                                        </div>
							                                        <div class="col-md-3 col-6 b-r"> <strong>Disponible hasta</strong>
							                                            <br>
							                                            <p class="text-muted"><?=$actividad['tar_fecha_entrega'];?></p>
							                                        </div>
							                                        <div class="col-md-3 col-6"> <strong>Retrasos</strong>
							                                            <br>
							                                            <p class="text-muted"><?=$actividad['tar_impedir_retrasos'];?></p>
							                                        </div>
							                                    </div>

							                                    <h4 class="font-bold">Descripción</h4>
																<hr>
							                                    <p><?=$actividad['tar_descripcion'];?></p>
							                                    <br>
							                                    <h4 class="font-bold">Archivos adjuntos</h4>
							                                    <hr>
							                                    <ul>
							                                        <?php if($actividad['tar_archivo']!=""){?><li><a href="../files/tareas/<?=$actividad['tar_archivo'];?>" target="_blank"><?=$actividad['tar_archivo'];?></a></li><?php }?>
																	<?php if($actividad['tar_archivo2']!=""){?><li><a href="../files/tareas/<?=$actividad['tar_archivo2'];?>" target="_blank"><?=$actividad['tar_archivo2'];?></a></li><?php }?>
																	<?php if($actividad['tar_archivo3']!=""){?><li><a href="../files/tareas/<?=$actividad['tar_archivo3'];?>" target="_blank"><?=$actividad['tar_archivo3'];?></a></li><?php }?>
							                                    </ul>
							                                    
							                                    <br>
							                                </div>
													</div>
					                                <div class="tab-pane" id="tab2">
														<div class="container-fluid">
		                                                    <div class="row">
		                                                        <div class="full-width p-rl-20">
		                                                            <div class="panel">
																		<p>Desde esta opción puedes enviar la actividad pendiente. También puedes hacer un comentario, si lo deseas, sobre la actividad enviada para que el docente lo tenga presente.</p>
		                                                                <?php if($fechas[1]>=0){?>
																		<form action="guardar.php" method="post" enctype="multipart/form-data">
																			<input type="hidden" name="id" value="10">
																			<input type="hidden" name="idR" value="<?=$_GET["idR"];?>">
		                                                                    <p><textarea class="form-control p-text-area" name="comentario" rows="2" placeholder="Haz un comentario..."></textarea></p>
																			
																			<p><input type="file" name="file" class="default"></p>
																			
																			<p><input type="submit" class="btn btn-info" value="Enviar actividad"></p>
																			
		                                                                </form>
																		<?php }else{
																			echo "<span style='color:red;'>La fecha límite para enviar esta actividad ya pasó.</span>";
																		}?>
		                                                            </div>
																	<?php
																	$consultaEntrega=mysqli_query($conexion, "SELECT * FROM academico_actividad_tareas_entregas WHERE ent_id_actividad='".$_GET["idR"]."' AND ent_id_estudiante='".$datosEstudianteActual['mat_id']."'");
																	$enviada = mysqli_fetch_array($consultaEntrega, MYSQLI_BOTH);
																	if($enviada[0]!=""){
																	?>
																		<div class="panel">
																			<h4 class="font-bold">Archivo enviado</h4>
																			<hr>
																			<p>Ya has enviado un archivo relacionado a esta actividad. Si lo deseas reemplazar, puedes repetir el proceso inicial.</p>
																			<p><b>Fecha de envío:</b> <?=$enviada['ent_fecha'];?> </p>
																			<p><b>Archivo:</b> <a href="../files/tareas/<?=$enviada['ent_archivo'];?>" target="_blank"><?=$enviada['ent_archivo'];?> </a></p>
																			<p><b>Comentario:</b><br> <?=$enviada['ent_comentario'];?> </p>
																		</div>
																	<?php }?>
																	
		                                                        </div>
																
		                                                	</div>
														</div>
													</div>
					                            </div>
					                        </div>
                                         </div>
                                     </div>
                                </div>
                                <!-- END PROFILE CONTENT -->
                            </div>
                        </div>
                    </div>
                <!-- end page content -->
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
        
        <script src="../../config-general/assets/js/layout.js" ></script>
		<script src="../../config-general/assets/js/theme-color.js" ></script>
		<!-- Material -->
		<script src="../../config-general/assets/plugins/material/material.min.js"></script>
        <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/student_profile.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:31:36 GMT -->
</html>