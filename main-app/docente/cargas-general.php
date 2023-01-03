<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0068';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
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
                                <div class="page-title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
									
								<div class="col-sm-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></header>
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
													<th>Carga académica</th>
													  
													<th style="text-align: center;">INDICADORES</th>
													<th style="text-align: center;">CALIFICACIONES</th>
													<th style="text-align: center;">EVALUACIONES</th>
													<th style="text-align: center;">CLASES</th>
													<th style="text-align: center;">CRONOGRAMA</th>
													<th style="text-align: center;">FOROS</th>
													<th style="text-align: center;">TAREAS</th>
													  
												  </tr>
												</thead>
                                                <tbody>
													<?php
													$contReg = 1; 
													$consulta = mysqli_query($conexion, "SELECT * FROM academico_cargas 
													INNER JOIN academico_materias ON mat_id=car_materia
													INNER JOIN academico_grados ON gra_id=car_curso
													INNER JOIN academico_grupos ON gru_id=car_grupo
													WHERE car_docente='".$_SESSION["id"]."'
													ORDER BY car_posicion_docente, car_curso, car_grupo, mat_nombre
													");
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$consultaNumerosCargas=mysqli_query($conexion, "SELECT
														(SELECT COUNT(ipc_id) FROM academico_indicadores_carga WHERE ipc_carga='".$resultado['car_id']."' AND ipc_periodo='".$resultado['car_periodo']."'),
														(SELECT COUNT(act_id) FROM academico_actividades WHERE act_id_carga='".$resultado['car_id']."' AND act_periodo='".$resultado['car_periodo']."' AND act_estado=1),
														(SELECT COUNT(eva_id) FROM academico_actividad_evaluaciones WHERE eva_id_carga='".$resultado['car_id']."' AND eva_periodo='".$resultado['car_periodo']."' AND eva_estado=1),
														(SELECT COUNT(cls_id) FROM academico_clases WHERE cls_id_carga='".$resultado['car_id']."' AND cls_periodo='".$resultado['car_periodo']."' AND cls_estado=1),
														(SELECT COUNT(cro_id) FROM academico_cronograma WHERE cro_id_carga='".$resultado['car_id']."' AND cro_periodo='".$resultado['car_periodo']."'),
														(SELECT COUNT(foro_id) FROM academico_actividad_foro WHERE foro_id_carga='".$resultado['car_id']."' AND foro_periodo='".$resultado['car_periodo']."' AND foro_estado=1),
														(SELECT COUNT(tar_id) FROM academico_actividad_tareas WHERE tar_id_carga='".$resultado['car_id']."' AND tar_periodo='".$resultado['car_periodo']."' AND tar_estado=1)");
														$numerosCargas = mysqli_fetch_array($consultaNumerosCargas, MYSQLI_BOTH);
													?>
                                                    
													<tr>
                                                        <td style="text-align:center;"><?=$contReg;?></td>
														<td><?=strtoupper($resultado['mat_nombre']." (".$resultado['gra_nombre']." ".$resultado['gru_nombre'].") - ".$resultado['car_periodo']." Periodo ");?></td>
											
														<td align="center"><a href="indicadores.php?carga=<?=$resultado['car_id'];?>&periodo=<?=$resultado['car_periodo'];?>" style="text-decoration: underline;"><?=$numerosCargas[0];?></a></td>
                                        				<td align="center"><a href="calificaciones.php?carga=<?=$resultado['car_id'];?>&periodo=<?=$resultado['car_periodo'];?>" style="text-decoration: underline;"><?=$numerosCargas[1];?></a></td>
														<td align="center"><a href="evaluaciones.php?carga=<?=$resultado['car_id'];?>&periodo=<?=$resultado['car_periodo'];?>" style="text-decoration: underline;"><?=$numerosCargas[2];?></a></td>
                                        				<td align="center"><a href="clases.php?carga=<?=$resultado['car_id'];?>&periodo=<?=$resultado['car_periodo'];?>" style="text-decoration: underline;"><?=$numerosCargas[3];?></a></td>
                                        				<td align="center"><a href="cronograma.php?carga=<?=$resultado['car_id'];?>&periodo=<?=$resultado['car_periodo'];?>" style="text-decoration: underline;"><?=$numerosCargas[4];?></a></td>
														<td align="center"><a href="foros.php?carga=<?=$resultado['car_id'];?>&periodo=<?=$resultado['car_periodo'];?>" style="text-decoration: underline;"><?=$numerosCargas[5];?></a></td>
                                        				<td align="center"><a href="actividades.php?carga=<?=$resultado['car_id'];?>&periodo=<?=$resultado['car_periodo'];?>" style="text-decoration: underline;"><?=$numerosCargas[6];?></a></td>
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
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
</body>

</html>