<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0104';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
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
                                <div class="page-title"><?=$frases[95][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								
								
								<div class="col-md-4 col-lg-3">
									<div class="panel">
										<header class="panel-heading panel-heading-red">MENÚ <?=strtoupper($frases[95][$datosUsuarioActual['uss_idioma']]);?></header>
										<div class="panel-body">
											<p><a href="#">Sacar informe</a></p>
											<p><a href="#">Cobro masivo</a></p>
											<p><a href="#">Eliminar todo</a></p>
											<p><a href="movimientos-importar.php">Importar saldos</a></p>
										</div>
                                	</div>
									
									<?php
										$filtro = '';
										if(is_numeric($_GET["tipo"])){$filtro .= " AND fcu_tipo='".$_GET["tipo"]."'";}
										if(is_numeric($_GET["usuario"])){$filtro .= " AND fcu_usuario='".$_GET["usuario"]."'";}
										if($_GET["fecha"]!=""){$filtro .= " AND fcu_fecha='".$_GET["fecha"]."'";}
										$consultaEstadisticas=mysqli_query($conexion, "SELECT
										(SELECT sum(fcu_valor) FROM finanzas_cuentas WHERE fcu_tipo=1 AND fcu_anulado='0'),
										(SELECT sum(fcu_valor) FROM finanzas_cuentas WHERE fcu_tipo=2 AND fcu_anulado='0'),
										(SELECT sum(fcu_valor) FROM finanzas_cuentas WHERE fcu_tipo=3 AND fcu_anulado='0'),
										(SELECT sum(fcu_valor) FROM finanzas_cuentas WHERE fcu_tipo=4 AND fcu_anulado='0')");
										$estadisticasCuentas = mysqli_fetch_array($consultaEstadisticas, MYSQLI_BOTH);
										@$porcentajeIngreso = round(($estadisticasCuentas[0]/$estadisticasCuentas[2])*100,2);
										@$porcentajeEgreso = round(($estadisticasCuentas[1]/$estadisticasCuentas[3])*100,2);
										?>
									
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Resúmen </header>
										<div class="panel-body">

											
												<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?usuario=<?=$_GET["usuario"];?>&tipo=1" <?=$estiloResaltado;?>>Ingresos: <b>$<?=number_format($estadisticasCuentas[0],0,",",".");?></b></a></div>
																	<div class="percent pull-right" title="de los cobros realizados"><?=$porcentajeIngreso;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajeIngreso;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											
											<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?usuario=<?=$_GET["usuario"];?>&tipo=2" <?=$estiloResaltado;?>>Egreso: <b>$<?=number_format($estadisticasCuentas[1],0,",",".");?></b></a></div>
																	<div class="percent pull-right" title="de los pagos pendientes"><?=$porcentajeEgreso;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajeEgreso;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											
													<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?usuario=<?=$_GET["usuario"];?>&tipo=3" <?=$estiloResaltado;?>>Cuentas por cobrar: <b>$<?=number_format($estadisticasCuentas[2],0,",",".");?></b></a></div>
																</div>
															</div>
														</div>
											
											<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?usuario=<?=$_GET["usuario"];?>&tipo=4" <?=$estiloResaltado;?>>Cuentas por pagar: <b>$<?=number_format($estadisticasCuentas[3],0,",",".");?></b></a></div>
																</div>
															</div>
														</div>

											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									
									
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[95][$datosUsuarioActual['uss_idioma']];?></header>
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
														<a href="movimientos-agregar.php" id="addRow" class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual[8]];?></th>
														<th>Fecha</th>
														<th>Detalle</th>
														<th>Valor</th>
														<th>Tipo</th>
														<th>Usuario</th>
														<th><?=$frases[54][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													if(is_numeric($_GET["estadoM"])){$filtro .= " AND mat_estado_matricula='".$_GET["estadoM"]."'";}
													
													$filtroLimite = '';
													if(is_numeric($_GET["cantidad"])){$filtroLimite = "LIMIT 0,".$_GET["cantidad"];}
													
													 $consulta = mysqli_query($conexion, "SELECT * FROM finanzas_cuentas
													 INNER JOIN usuarios ON uss_id=fcu_usuario
													 WHERE fcu_id=fcu_id $filtro
													 ORDER BY fcu_id
													 $filtroLimite");
													 $contReg = 1;
													$estadosCuentas = array("","Ingreso","Egreso","Cobro (CPC)","Deuda (CPP)");
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $bgColor = '';
														 if($resultado['fcu_anulado']==1) $bgColor = 'sandybrown';
													 ?>
													<tr style="background-color:<?=$bgColor;?>;">
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['fcu_id'];?></td>
														<td>
															<a href="<?=$_SERVER['PHP_SELF'];?>?usuario=<?=$_GET["usuario"];?>&fecha=<?=$resultado['fcu_fecha'];?>&tipo=<?=$_GET["tipo"];?>" style="text-decoration: underline;"><?=$resultado['fcu_fecha'];?></a>
														</td>
														<td><?=$resultado['fcu_detalle'];?></td>
														<td>$<?=number_format($resultado['fcu_valor'],0,",",".");?></td>
														<td>
															<a href="<?=$_SERVER['PHP_SELF'];?>?usuario=<?=$_GET["usuario"];?>&tipo=<?=$resultado['fcu_tipo'];?>" style="text-decoration: underline;"><?=$estadosCuentas[$resultado['fcu_tipo']];?></a>
														</td>
														<td>
															<a href="<?=$_SERVER['PHP_SELF'];?>?usuario=<?=$resultado['uss_id'];?>" style="text-decoration: underline;"><?=strtoupper($resultado['uss_nombre']);?></a>
														</td>

														<td>
															<div class="btn-group">
																  <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual[8]];?></button>
																  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	  <i class="fa fa-angle-down"></i>
																  </button>
																  <ul class="dropdown-menu" role="menu">
																	  <li><a href="#"><?=$frases[165][$datosUsuarioActual[8]];?></a></li>
																	  <li><a href="guardar.php?get=11&idR=<?=$resultado['fcu_id'];?>">Anular</a></li>
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