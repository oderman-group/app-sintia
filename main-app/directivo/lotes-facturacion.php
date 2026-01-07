<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0277';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

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
                                <div class="page-title">Lotes de Facturación</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="movimientos.php" onClick="deseaRegresar(this)">Movimientos</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Lotes de Facturación</li>
                            </ol>
                        </div>
                    </div>
					
					<!-- Descripción de la página -->
					<div class="row mb-3">
						<div class="col-md-12">
							<p class="text-muted" style="font-size: 14px; line-height: 1.6;">
								<i class="fa fa-info-circle text-info"></i> 
								<?=__('financiero.lotes_facturacion_descripcion');?>
							</p>
						</div>
					</div>

                    <div class="row">
                        <div class="col-md-12">
                        <?php include("../../config-general/mensajes-informativos.php"); ?>
									<?php include("../compartido/publicidad-lateral.php");?>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Lotes de Facturación</header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th>Nombre del Lote</th>
														<th>Fecha</th>
														<th>Tipo de Grupo</th>
														<th>Usuario Creador</th>
														<th>Facturas Generadas</th>
														<th>Cobradas</th>
														<th>Por Cobrar</th>
														<th>Estado</th>
														<th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$consulta = Movimientos::listarLotesFacturacion($conexion, $config);
													$contReg = 1;
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$fechaBD = new DateTime($resultado['lote_fecha']);
														$fecha = $fechaBD->format('d/m/Y H:i');
														
														$estadoClass = '';
														$estadoTexto = $resultado['lote_estado'];
														switch($resultado['lote_estado']) {
															case 'COMPLETADO':
																$estadoClass = 'label-success';
																$estadoTexto = 'Completado';
																break;
															case 'PROCESANDO':
																$estadoClass = 'label-warning';
																$estadoTexto = 'Procesando';
																break;
															case 'ERROR':
																$estadoClass = 'label-danger';
																$estadoTexto = 'Error';
																break;
															case 'PENDIENTE':
																$estadoClass = 'label-info';
																$estadoTexto = 'Pendiente';
																break;
														}
														
														$loteIdEncoded = base64_encode($resultado['id']);
													?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=htmlspecialchars($resultado['lote_nombre']);?></td>
														<td><?=$fecha;?></td>
														<td><?=htmlspecialchars($resultado['lote_tipo_grupo']);?></td>
														<td><?=htmlspecialchars($resultado['usuario_creador_nombre'] ?? 'N/A');?></td>
														<td><?=$resultado['facturas_generadas'] ?? 0;?></td>
														<td><span class="label label-success"><?=$resultado['facturas_cobradas'] ?? 0;?></span></td>
														<td><span class="label label-warning"><?=$resultado['facturas_por_cobrar'] ?? 0;?></span></td>
														<td><span class="label <?=$estadoClass;?>"><?=$estadoTexto;?></span></td>
														<td>
															<a href="lote-detalle.php?id=<?=$loteIdEncoded;?>" class="btn btn-sm btn-info" title="Ver detalles">
																<i class="fa fa-eye"></i> Ver Detalles
															</a>
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
    <script src="../../config-general/assets/js/pages/table/table_data.js"></script>
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


