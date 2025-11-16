<?php
include("session.php");
$idPaginaInterna = 'DC0006';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
require_once("../class/Estudiantes.php");
include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");

$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}

$datosConsulta = Actividades::traerDatosActividades($conexion, $config, $idR);
?>
<script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js"></script>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
	<input type="hidden" id="idR" name="idR" value="<?=$idR;?>">
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
                                <li><a class="parent-item" href="actividades.php"><?=$frases[112][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
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
														<b><?=$frases[130][$datosUsuarioActual['uss_idioma']];?> </b>
														<div class="profile-desc-item pull-right"><?=$datosConsulta['tar_fecha_disponible'];?></div>
													</li>
													<li class="list-group-item">
														<b><?=$frases[131][$datosUsuarioActual['uss_idioma']];?> </b>
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
											$evaluacionesEnComun = Actividades::actividadesCargasPeriodosDiferente($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $idR);
											while($evaComun = mysqli_fetch_array($evaluacionesEnComun, MYSQLI_BOTH)){
											?>
												<p><a href="actividades-entregas.php?idR=<?=base64_encode($evaComun['tar_id']);?>"><?=$evaComun['tar_titulo'];?></a></p>
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
                                            <header><?=$datosConsulta['tar_titulo'];?></header>
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
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Enviada</th>
														<th>Hace</th>
														<th>Descargar</th>
														<th>Comentario</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
													 $contReg  = 1;

													 // ============================================
													 // PRE-CARGAR ENTREGAS DE LA ACTIVIDAD (MAPA)
													 // ============================================
													 $entregasMapa = Actividades::traerEntregasActividadMapa($config, $idR);

													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$datos1 = $entregasMapa[$resultado['mat_id']] ?? null;
														if ($datos1){
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>
														<td><?=$datos1['ent_fecha'];?></td>
														<td><?php if($datos1[1]>0){echo $datos1[1]." Min. y ";} if($datos1[2]>0){echo $datos1[2]." Seg.";}?></td>
														<td>
														<?php 
														$url= $storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$datos1["ent_archivo"])->signedUrl(new DateTime('tomorrow')); 
														$existe=$storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$datos1["ent_archivo"])->exists();
														$url2= $storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$datos1["ent_archivo2"])->signedUrl(new DateTime('tomorrow')); 
														$existe2=$storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$datos1["ent_archivo2"])->exists();
														$url3= $storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$datos1["ent_archivo3"])->signedUrl(new DateTime('tomorrow')); 
														$existe3=$storage->getBucket()->object(FILE_TAREAS_ENTREGADAS.$datos1["ent_archivo3"])->exists();
														if(!empty($datos1['ent_archivo']) and $existe){?>
															<a href="<?=$url?>" target="_blank">Archivo 1</a><br>
														<?php }?>
															
														<?php if(!empty($datos1['ent_archivo2']) and $existe2){?>
															<a href="<?=$url2?>" target="_blank">Archivo 2</a><br>
														<?php }?>
															
														<?php if(!empty($datos1['ent_archivo3']) and $existe3){?>
															<a href="<?=$url3?>" target="_blank">Archivo 3</a><br>
														<?php }?>	
														
														</td>
														<td style="font-size: 11px;"><?=$datos1['ent_comentario'];?></td>
                                                    </tr>
													<?php 
														 $contReg++;
													  }}
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


</html>