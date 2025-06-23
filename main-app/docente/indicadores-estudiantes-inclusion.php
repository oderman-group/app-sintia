<?php
include("session.php");
$idPaginaInterna = 'DC0150';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
require_once("../class/Estudiantes.php");
include("../compartido/head.php");

include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

require_once ROOT_PATH . '/main-app/class/App/Academico/Indicador_Estudiantes_Inclusion.php';

$idIndicadorNuevo="";
$idIndicador="";
if(!empty($_GET["idIndicadorNuevo"])){ $idIndicadorNuevo=base64_decode($_GET["idIndicadorNuevo"]);}
if(!empty($_GET["idIndicador"])){ $idIndicador=base64_decode($_GET["idIndicador"]);}

$indicador = Indicadores::traerDatosIndicador($conexion, $config, $idIndicador);

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
                                <div class="page-title"><?=$frases[55][$datosUsuarioActual['uss_idioma']];?> inclusión</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <?php include("includes/barra-superior-informacion-actual.php"); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[55][$datosUsuarioActual['uss_idioma']];?> inclusión</header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">

										<div class="row">
											<div class="col-sm-2">
												<div class="btn-group">
													<a href='indicadores.php' class='btn btn-secondary'  style='text-transform:uppercase;'><i class='fa fa-long-arrow-left'></i><?=$frases[184][$datosUsuarioActual['uss_idioma']]?></a>													
													<?php 
														$idModal = "indicadorEstudianteInclusionModal";
														include("indicadores-estudiantes-inclusion-agregar-modal.php");
													?>
												</div>
											</div>
											<div class="col-sm-10">
												<input type="text" class="form-control" value="<?= $indicador['ind_nombre']; ?>" readonly>
											</div>
										</div>
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[241][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[138][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$consulta = Academico_Indicadores_Estudiantes_Inclusion::obtenerEstudiantesInclusionxIdIndicador($idIndicadorNuevo);
													 $contReg = 1;
													 foreach ($consulta as $resultado){							
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['mat_id'];?></td>
														<td>
															<?=$resultado['mat_documento'];?>
															
														</td>
														<td>
															<?=Estudiantes::NombreCompletoDelEstudiante(Estudiantes::obtenerDatosEstudiante($resultado['mat_id']));?>
															<br>
															<i style="color: blue;"><?=$resultado['indicador_inclusion'];?></i>
														</td>
														<td><?=$resultado['genero'];?></td>
														<td>
															<div class="btn-group">
																<button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
																<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	<i class="fa fa-angle-down"></i>
																</button>
																<ul class="dropdown-menu" role="menu">
																	<li><a href="javascript:void(0);" onclick="btnEditarClic('<?= $resultado['aii_id']; ?>','<?= $resultado['mat_id']; ?>','<?= $indicador['ipc_indicador']; ?>')"><?= $frases[375][$datosUsuarioActual['uss_idioma']]; ?></a></li>                                                  
																	<?php if ($resultado['aii_id'] > 0) { ?>
                                                                    	<li><a href="javascript:void(0);" onclick="btnEliminarClic('<?= $resultado['aii_id']; ?>')"><?= $frases[174][$datosUsuarioActual['uss_idioma']]; ?></a></li>
																	<?php } ?>
																</ul>
															</div>								
														</td>
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

	<script src="../js/indicadores-estudiantes-inclusion.js" ></script>

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