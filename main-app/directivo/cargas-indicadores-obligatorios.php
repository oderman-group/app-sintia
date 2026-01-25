<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0035';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

Utilidades::validarParametros($_GET);

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
                                <div class="page-title">Indicadores Obligatorios</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cargas.php" onClick="deseaRegresar(this)">Cargas</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Indicadores Obligatorios</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
								<?php include("../compartido/publicidad-lateral.php");?>
								<div class="col-md-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Indicadores Obligatorios</header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="btn-group">
                                                        <?php if(Modulos::validarSubRol(['DT0038'])){?>
														<a href="cargas-indicadores-obligatorios-agregar.php" id="addRow" class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
                                                        <?php }?>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>C&oacute;digo</th>
                                                        <th>Nombre</th>
                                                        <th>Valor</th>
														<th>Cargas Asignadas</th>
														<th>Estado</th>
														<th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
                                                    $consulta = Indicadores::consultarIndicadoresObligatorios();
													$contReg = 1;
                                                    $sumaP = 0;
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                        $sumaP = $sumaP + $resultado['ind_valor'];
														
														// Verificar si el indicador está en uso (actividades registradas)
														$verificacionUso = Indicadores::verificarIndicadorEnUso($config, $resultado['ind_id']);
														
														// Verificar también si hay actividades relacionadas (aunque no estén registradas)
														// o si hay calificaciones asociadas a actividades de este indicador
														$sqlActividades = "SELECT COUNT(*) as total_actividades 
																		 FROM ".BD_ACADEMICA.".academico_actividades 
																		 WHERE act_id_tipo=? AND act_estado=1 AND institucion=? AND year=?";
														$parametrosActividades = [$resultado['ind_id'], $config['conf_id_institucion'], $_SESSION["bd"]];
														$resultadoActividades = BindSQL::prepararSQL($sqlActividades, $parametrosActividades);
														$datosActividades = mysqli_fetch_array($resultadoActividades, MYSQLI_BOTH);
														$totalActividades = !empty($datosActividades['total_actividades']) ? (int)$datosActividades['total_actividades'] : 0;
														
														// Verificar si hay calificaciones asociadas a actividades de este indicador
														$sqlCalificaciones = "SELECT COUNT(DISTINCT aac.cal_id) as total_calificaciones 
																			  FROM ".BD_ACADEMICA.".academico_actividades aa
																			  INNER JOIN ".BD_ACADEMICA.".academico_calificaciones aac ON aac.cal_id_actividad = aa.act_id AND aac.institucion = aa.institucion AND aac.year = aa.year
																			  WHERE aa.act_id_tipo=? AND aa.act_estado=1 AND aa.institucion=? AND aa.year=?";
														$parametrosCalificaciones = [$resultado['ind_id'], $config['conf_id_institucion'], $_SESSION["bd"]];
														$resultadoCalificaciones = BindSQL::prepararSQL($sqlCalificaciones, $parametrosCalificaciones);
														$datosCalificaciones = mysqli_fetch_array($resultadoCalificaciones, MYSQLI_BOTH);
														$totalCalificaciones = !empty($datosCalificaciones['total_calificaciones']) ? (int)$datosCalificaciones['total_calificaciones'] : 0;
														
														// El indicador está en uso si tiene actividades o calificaciones
														$enUso = $verificacionUso['enUso'] || $totalActividades > 0 || $totalCalificaciones > 0;
														
														$estadoTexto = $enUso ? '<span class="label label-danger">En Uso</span>' : '<span class="label label-success">Disponible</span>';
														$mensajeUso = '';
														if ($enUso) {
															$mensajeUso = 'Este indicador está en uso. ';
															if ($verificacionUso['totalActividades'] > 0) {
																$mensajeUso .= 'Tiene ' . $verificacionUso['totalActividades'] . ' actividad(es) registrada(s). ';
															}
															if ($totalActividades > $verificacionUso['totalActividades']) {
																$mensajeUso .= 'Tiene ' . ($totalActividades - $verificacionUso['totalActividades']) . ' actividad(es) adicional(es). ';
															}
															if ($totalCalificaciones > 0) {
																$mensajeUso .= 'Tiene ' . $totalCalificaciones . ' calificación(es) asociada(s). ';
															}
															$mensajeUso .= 'No puede ser eliminado.';
														}
														$estadoTooltip = $enUso ? 'title="' . htmlspecialchars($mensajeUso) . '"' : '';
														
														// Contar cargas asignadas (distintas cargas, no importa el período)
														$sqlCargas = "SELECT COUNT(DISTINCT ipc_carga) as total_cargas, COUNT(*) as total_relaciones 
																	 FROM ".BD_ACADEMICA.".academico_indicadores_carga 
																	 WHERE ipc_indicador=? AND ipc_creado=0 AND institucion=? AND year=?";
														$parametrosCargas = [$resultado['ind_id'], $config['conf_id_institucion'], $_SESSION["bd"]];
														$resultadoCargas = BindSQL::prepararSQL($sqlCargas, $parametrosCargas);
														$datosCargas = mysqli_fetch_array($resultadoCargas, MYSQLI_BOTH);
														$totalCargasAsignadas = !empty($datosCargas['total_cargas']) ? (int)$datosCargas['total_cargas'] : 0;
														$totalRelaciones = !empty($datosCargas['total_relaciones']) ? (int)$datosCargas['total_relaciones'] : 0;
													?>
													<tr>
                                                        <td><?=$contReg;?></td>
                                                        <td><?=$resultado['ind_id'];?></td>
                                                        <td><?=$resultado['ind_nombre'];?></td>
                                                        <td><?=$resultado['ind_valor'];?></td>
														<td>
															<?php if($totalCargasAsignadas > 0): ?>
																<span class="badge badge-info" title="Asignado a <?=$totalCargasAsignadas;?> carga(s) académica(s) en <?=$totalRelaciones;?> relación(es) carga/período"><?=$totalCargasAsignadas;?> carga(s)</span>
															<?php else: ?>
																<span class="text-muted">Sin asignar</span>
															<?php endif; ?>
														</td>
														<td <?=$estadoTooltip;?>><?=$estadoTexto;?></td>
														<td>
                                                            <?php 
                                                            // Verificar si hay opciones disponibles para mostrar
                                                            $tieneEditar = Modulos::validarSubRol(['DT0037']);
                                                            $tieneEliminar = Modulos::validarSubRol(['DT0157']) && !$enUso; // Solo mostrar eliminar si NO está en uso
                                                            
                                                            // Solo mostrar el botón si hay al menos una opción disponible
                                                            if ($tieneEditar || $tieneEliminar) {
                                                            ?>
															<div class="btn-group">
                                                                <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
                                                                <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                    <i class="fa fa-angle-down"></i>
                                                                </button>
                                                                <ul class="dropdown-menu" role="menu">
                                                                    <?php if($tieneEditar){?>
                                                                    <li><a href="cargas-indicadores-obligatorios-editar.php?id=<?=base64_encode($resultado['ind_id']);?>" <?php if($enUso) echo 'onclick="alert(\'Este indicador está en uso y no puede ser editado.\'); return false;"'; ?>><?=$frases[165][$datosUsuarioActual['uss_idioma']];?></a></li>
                                                                    <?php } if($tieneEliminar){?>
                                                                    <li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este registro?','question','cargas-indicadores-obligatorios-eliminar.php?idN=<?=base64_encode($resultado['ind_id']);?>')">Eliminar</a></li>	
                                                                    <?php }?>
                                                                </ul>
                                                            </div>
                                                            <?php } else { ?>
                                                                <span class="text-muted">-</span>
                                                            <?php }?>
														</td>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
													  ?>
                                                </tbody>
                                            </table>
                                           
                                            </div>
                                            <?php $botones = new botonesGuardar("cargas.php",false); ?>
                                        </div>
                                    </div>
                                </div>
								
								<div class="col-md-4 col-lg-3">
									<?php include("../compartido/publicidad-lateral.php");?>
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