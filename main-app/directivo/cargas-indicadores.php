<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0034';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>
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
                                <div class="page-title"><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cargas.php" onClick="deseaRegresar(this)"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								
								
								<div class="col-md-4 col-lg-3">
														
									<h4 align="center"><?=strtoupper($frases[205][$datosUsuarioActual['uss_idioma']]);?></h4>
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[106][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
											<?php
											for($i=1; $i<=$datosCargaActual['gra_periodos']; $i++){
												$periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $i);
												
												$porcentajeGrado=25;
												if(!empty($periodosCursos['gvp_valor'])){
													$porcentajeGrado=$periodosCursos['gvp_valor'];
												}

												if($i==$datosCargaActual['car_periodo']) $msjPeriodoActual = '- ACTUAL'; else $msjPeriodoActual = '';
												if($i==$periodoConsultaActual) $estiloResaltadoP = 'style="color: orange;"'; else $estiloResaltadoP = '';
											?>
												<p>
													<a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=$_GET['carga'];?>&periodo=<?=base64_encode($i);?>&docente=<?=$_GET["docente"];?>&get=<?=base64_encode(100);?>" <?=$estiloResaltadoP;?>><?=strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]);?> <?=$i;?> (<?=$porcentajeGrado;?>%) <?=$msjPeriodoActual;?></a>
											
												</p>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=$_GET['carga'];?>&docente=<?=$_GET["docente"];?>">VER TODOS</a></p>
										</div>
									</div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											try{
												$cCargas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
												INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion={$config['conf_id_institucion']} AND am.year={$_SESSION["bd"]}
												INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]}
												INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion={$config['conf_id_institucion']} AND gru.year={$_SESSION["bd"]}
												WHERE car_docente='".$datosCargaActual['car_docente']."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$_SESSION["bd"]}
												ORDER BY car_posicion_docente, car_curso, car_grupo, am.mat_nombre");
											} catch (Exception $e) {
												include("../compartido/error-catch-to-report.php");
											}
											$nCargas = mysqli_num_rows($cCargas);
											while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
												if($rCargas['car_id']==$cargaConsultaActual) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
												if($rCargas['car_director_grupo']==1) {$estiloDG = 'style="font-weight: bold;"'; $msjDG = ' - D.G';} else {$estiloDG = ''; $msjDG = '';}
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($periodoConsultaActual);?>&docente=<?=base64_encode($rCargas['car_docente']);?>&get=<?=base64_encode(100);?>" <?=$estiloResaltado;?>><span <?=$estiloDG;?>><?=$rCargas['car_posicion_docente'];?>. <?=strtoupper($rCargas['mat_nombre']);?> (<?=strtoupper($rCargas['gra_nombre']." ".$rCargas['gru_nombre']);?>) <?=$msjDG;?></span></a></p>
											<?php }?>
										</div>
                                    </div>
									
									<?php include("../compartido/publicidad-lateral.php");?>
									
								</div>
								
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											<!--
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="btn-group">
														<a href="cargas-indicadores-agregar.php?carga=<?=$_GET["carga"];?>&docente=<?=$_GET["docente"];?>" id="addRow" class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
													</div>
												</div>
											</div>
											-->
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Indicador</th>
														<th>Valor</th>
														<th>Periodo</th>
														<th>Creado</th>
														<th>#ACTV</th>
														<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0040','DT0039','DT0087'])){?>
															<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
														<?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$filtro = '';
													if(!empty($_GET["periodo"])){$filtro .= " AND ipc.ipc_periodo='".$periodoConsultaActual."'";}
													$consulta = Indicadores::consultarIndicador($conexion, $config, $cargaConsultaActual, $filtro);
													$contReg = 1;
													$sino = array("NO","SI");
													$sumaPorcentaje = 0;
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$consultaNumActividades = Actividades::consultaActividadesCargaIndicador($config, $resultado['ipc_indicador'], $cargaConsultaActual, $resultado['ipc_periodo']);
														$numActividades = mysqli_num_rows($consultaNumActividades);
														
														$sumaPorcentaje += $resultado['ipc_valor'];
													?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['ipc_indicador'];?></td>
														<td><?=$resultado['ind_nombre'];?></td>
														<td align="center"><?=$resultado['ipc_valor'];?>%</td>
														<td align="center"><?=$resultado['ipc_periodo'];?></td>
														<td align="center"><?=$sino[$resultado['ipc_creado']];?></td>
														<td align="center"><?=$numActividades;?></td>
														
														<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0040','DT0039','DT0087'])){?>
															<td>
																<div class="btn-group">
																	<button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
																	<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																		<i class="fa fa-angle-down"></i>
																	</button>
																	<ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0040'])){?>
																		<li><a href="cargas-indicadores-agregar.php?carga=<?=$_GET["carga"];?>&periodo=<?=base64_encode($resultado['ipc_periodo']);?>&docente=<?=$_GET["docente"];?>"><?=$frases[231][$datosUsuarioActual['uss_idioma']];?></a></li>
																		<?php } if(Modulos::validarSubRol(['DT0039'])){?>
																		<li><a href="cargas-indicadores-editar.php?idR=<?=base64_encode($resultado['ipc_id']);?>&carga=<?=$_GET["carga"];?>&periodo=<?=base64_encode($resultado['ipc_periodo']);?>&docente=<?=$_GET["docente"];?>"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?></a></li>
																		<?php } if(Modulos::validarSubRol(['DT0087'])){?>
																		<li><a href="javascript:void(0);" name="cargas-indicadores-eliminar.php?idR=<?=base64_encode($resultado['ipc_id']);?>&idIndicador=<?=base64_encode($resultado['ipc_indicador']);?>&carga=<?=$_GET["carga"];?>&periodo=<?=base64_encode($resultado['ipc_periodo']);?>&docente=<?=$_GET["docente"];?>" onClick="deseaEliminar(this)"><?=$frases[174][$datosUsuarioActual['uss_idioma']];?></a></li>
                                                        				<?php }?>
																	</ul>
																</div>
															</td>
														<?php }?>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
													  ?>
                                                </tbody>
												<?php if(!empty($_GET["periodo"])){?>
												<tfoot>
													<tr>
														<td colspan="3"></td>
														<td align="center"><?=$sumaPorcentaje;?>%</td>
														<td colspan="4"></td>
													</tr>
												</tfoot>
												<?php }?>
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