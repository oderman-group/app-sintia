<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0068';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");?>
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
													<?php if(array_key_exists(12, $arregloModulos)){?>
														<th style="text-align: center;">EVALUACIONES</th>
													<?php }?>
													<?php if(array_key_exists(11, $arregloModulos)){?>
														<th style="text-align: center;">CLASES</th>
													<?php }?>
													<?php if(array_key_exists(15, $arregloModulos)){?>
														<th style="text-align: center;">CRONOGRAMA</th>
													<?php }?>
													<?php if(array_key_exists(13, $arregloModulos)){?>
														<th style="text-align: center;">FOROS</th>
													<?php }?>
													<?php if(array_key_exists(14, $arregloModulos)){?>
														<th style="text-align: center;">TAREAS</th>
													<?php }?>
													  
												  </tr>
												</thead>
                                                <tbody>
													<?php
													$contReg = 1; 
													$consulta = CargaAcademica::traerCargasDocentes($config, $_SESSION["id"]);
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$consultaNumerosCargas=mysqli_query($conexion, "SELECT
														(SELECT COUNT(ipc_id) FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga='".$resultado['car_id']."' AND ipc_periodo='".$resultado['car_periodo']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
														(SELECT COUNT(act_id) FROM ".BD_ACADEMICA.".academico_actividades WHERE act_id_carga='".$resultado['car_id']."' AND act_periodo='".$resultado['car_periodo']."' AND act_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
														(SELECT COUNT(eva_id) FROM ".BD_ACADEMICA.".academico_actividad_evaluaciones WHERE eva_id_carga='".$resultado['car_id']."' AND eva_periodo='".$resultado['car_periodo']."' AND eva_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
														(SELECT COUNT(cls_id) FROM ".BD_ACADEMICA.".academico_clases WHERE cls_id_carga='".$resultado['car_id']."' AND cls_periodo='".$resultado['car_periodo']."' AND cls_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
														(SELECT COUNT(cro_id) FROM ".BD_ACADEMICA.".academico_cronograma WHERE cro_id_carga='".$resultado['car_id']."' AND cro_periodo='".$resultado['car_periodo']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
														(SELECT COUNT(foro_id) FROM ".BD_ACADEMICA.".academico_actividad_foro WHERE foro_id_carga='".$resultado['car_id']."' AND foro_periodo='".$resultado['car_periodo']."' AND foro_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
														(SELECT COUNT(tar_id) FROM ".BD_ACADEMICA.".academico_actividad_tareas WHERE tar_id_carga='".$resultado['car_id']."' AND tar_periodo='".$resultado['car_periodo']."' AND tar_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]})");
														$numerosCargas = mysqli_fetch_array($consultaNumerosCargas, MYSQLI_BOTH);
													?>
                                                    
													<tr>
                                                        <td style="text-align:center;"><?=$contReg;?></td>
														<td><?=strtoupper($resultado['mat_nombre']." (".$resultado['gra_nombre']." ".$resultado['gru_nombre'].") - ".$resultado['car_periodo']." Periodo ");?></td>
											
														<td align="center"><a href="indicadores.php?carga=<?=base64_encode($resultado['car_id']);?>&periodo=<?=base64_encode($resultado['car_periodo']);?>&get=<?=base64_encode(100);?>" style="text-decoration: underline;"><?=$numerosCargas[0];?></a></td>
                                        				<td align="center"><a href="calificaciones.php?carga=<?=base64_encode($resultado['car_id']);?>&periodo=<?=base64_encode($resultado['car_periodo']);?>&get=<?=base64_encode(100);?>" style="text-decoration: underline;"><?=$numerosCargas[1];?></a></td>
														<?php if(array_key_exists(12, $arregloModulos)){?>
															<td align="center"><a href="evaluaciones.php?carga=<?=base64_encode($resultado['car_id']);?>&periodo=<?=base64_encode($resultado['car_periodo']);?>&get=<?=base64_encode(100);?>" style="text-decoration: underline;"><?=$numerosCargas[2];?></a></td>
														<?php }?>

														<?php if(array_key_exists(11, $arregloModulos)){?>
                                        					<td align="center"><a href="clases.php?carga=<?=base64_encode($resultado['car_id']);?>&periodo=<?=base64_encode($resultado['car_periodo']);?>&get=<?=base64_encode(100);?>" style="text-decoration: underline;"><?=$numerosCargas[3];?></a></td>
														<?php }?>

														<?php if(array_key_exists(15, $arregloModulos)){?>
                                        					<td align="center"><a href="cronograma-calendario.php?carga=<?=base64_encode($resultado['car_id']);?>&periodo=<?=base64_encode($resultado['car_periodo']);?>&get=<?=base64_encode(100);?>" style="text-decoration: underline;"><?=$numerosCargas[4];?></a></td>
														<?php }?>

														<?php if(array_key_exists(13, $arregloModulos)){?>
															<td align="center"><a href="foros.php?carga=<?=base64_encode($resultado['car_id']);?>&periodo=<?=base64_encode($resultado['car_periodo']);?>&get=<?=base64_encode(100);?>" style="text-decoration: underline;"><?=$numerosCargas[5];?></a></td>
														<?php }?>

														<?php if(array_key_exists(14, $arregloModulos)){?>
                                        					<td align="center"><a href="actividades.php?carga=<?=base64_encode($resultado['car_id']);?>&periodo=<?=base64_encode($resultado['car_periodo']);?>&get=<?=base64_encode(100);?>" style="text-decoration: underline;"><?=$numerosCargas[6];?></a></td>
														<?php }?>
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