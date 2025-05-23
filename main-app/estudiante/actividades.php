<?php 
include("session.php");
include("verificar-usuario.php");
$idPaginaInterna = 'ES0023';

include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("../compartido/head.php");

require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_academico_asignaciones_estudiantes.php");
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
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
                                <div class="page-title"><?=$frases[112][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[106][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
											<?php
											$porcentaje = 0;
											for($i=1; $i<=$datosEstudianteActual['gra_periodos']; $i++){
                                                $periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosEstudianteActual['mat_grado'], $i);
                                                
                                                $porcentajeGrado=25;
                                                if(!empty($periodosCursos['gvp_valor'])){
                                                    $porcentajeGrado=$periodosCursos['gvp_valor'];
                                                }
												
												if($i==$periodoConsultaActual) $estiloResaltadoP = 'style="color: orange;"'; else $estiloResaltadoP = '';
											?>
												<p>
													<a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($i);?>" <?=$estiloResaltadoP;?>><?=strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]);?> <?=$i;?> (<?=$porcentajeGrado;?>%)</a>
												</p>
											<?php }?>
										
										</div>
									</div>
								
							
									
									<?php include("filtro-cargas.php");?>
									
									<?php include("../compartido/publicidad-lateral.php");?>
									
								</div>
									
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[112][$datosUsuarioActual['uss_idioma']];?></header>
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
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[127][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[128][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$consulta = Actividades::actividadesCargasPeriodos($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
													$contReg = 1; 

													while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                                                        $mostrarActividadEstudiante = BDT_AcademicoAsignacionesEstudiantes::mostrarActividadesEstudiante($datosEstudianteActual['mat_id'], $resultado['tar_id']);

                                                        if (!$mostrarActividadEstudiante) {
                                                            continue;
                                                        }

														$fd = mysqli_fetch_array(mysqli_query($conexion, "SELECT DATEDIFF('".$resultado['tar_fecha_entrega']."','".date("Y-m-d")."')"), MYSQLI_BOTH);
														$sd = mysqli_fetch_array(mysqli_query($conexion, "SELECT DATEDIFF('".$resultado['tar_fecha_disponible']."','".date("Y-m-d")."')"), MYSQLI_BOTH);
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['tar_id'];?></td>
														<td><?=$resultado['tar_titulo'];?></td>
														<td><?=$frases[125][$datosUsuarioActual['uss_idioma']];?>: <?=$resultado['tar_fecha_disponible'];?><br><?=$frases[126][$datosUsuarioActual['uss_idioma']];?>: <?=$resultado['tar_fecha_entrega'];?></td>
														
                                                        
                                                        <td>

                                                        <?php
                                                        $url= $storage->getBucket()->object(FILE_TAREAS.$resultado["tar_archivo"])->signedUrl(new DateTime('tomorrow'));
                                                        $existe=$storage->getBucket()->object(FILE_TAREAS.$resultado["tar_archivo"])->exists();
                                                        if($sd[0] <= 0){
                                                        if($resultado['tar_archivo']!=""  and $existe){
                                                        ?>

                                                            <a href="<?=$url?>" target="_blank">Descargar </a>

                                                        <?php 
                                                        }
                                                        }
                                                        ?>

                                                        </td>


														<td><a href="actividades-ver.php?idR=<?=base64_encode($resultado['tar_id']);?>"><?=$frases[154][$datosUsuarioActual['uss_idioma']];?></a></td>
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
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
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