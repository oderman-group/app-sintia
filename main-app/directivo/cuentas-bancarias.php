<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0278';?>
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
<?php require_once(ROOT_PATH."/main-app/compartido/body.php");?>
    <div class="page-wrapper">
        <?php require_once(ROOT_PATH."/main-app/compartido/encabezado.php");?>
		
        <?php require_once(ROOT_PATH."/main-app/compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php require_once(ROOT_PATH."/main-app/compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">Cuentas Bancarias</div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li class="active">Cuentas Bancarias</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								<div class="col-md-8 col-lg-12">
                                <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Cuentas Bancarias</header>
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
                                                        <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0279'])) { ?>
                                                            <a href="cuentas-bancarias-agregar.php" class="btn deepPink-bgcolor"> Agregar nueva <i class="fa fa-plus"></i></a>
                                                        <?php } ?>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th>ID</th>
														<th>Nombre</th>
														<th>Banco</th>
														<th>Número de Cuenta</th>
														<th>Tipo</th>
														<th>Método de Pago</th>
														<th>Saldo Inicial</th>
														<th>Ingresos</th>
														<th>Egresos</th>
														<th>Saldo Actual</th>
														<th>Estado</th>
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0280','DT0281'])){?>
                                                            <th>Acciones</th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
                                                        $consulta= Movimientos::listarCuentasBancarias($conexion, $config);
                                                        $contReg = 1;
                                                        while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                            $metodosPago = [
                                                                'EFECTIVO' => 'Efectivo',
                                                                'CHEQUE' => 'Cheque',
                                                                'T_DEBITO' => 'T. Débito',
                                                                'T_CREDITO' => 'T. Crédito',
                                                                'TRANSFERENCIA' => 'Transferencia',
                                                                'OTROS' => 'Otros'
                                                            ];
                                                            $metodoPagoTexto = $metodosPago[$resultado['cba_metodo_pago_asociado']] ?? $resultado['cba_metodo_pago_asociado'];
                                                            
                                                            // Validar si la cuenta bancaria está en uso
                                                            $cuentaEnUso = Movimientos::validarCuentaBancariaEnUso($conexion, $config, $resultado['cba_id']);
                                                            
                                                            // Calcular ingresos, egresos y saldo actual
                                                            $saldoInfo = Movimientos::calcularSaldoCuentaBancaria($conexion, $config, $resultado['cba_id']);
                                                            $saldoInicial = floatval($resultado['cba_saldo_inicial'] ?? 0);
                                                            $ingresos = floatval($saldoInfo['ingresos'] ?? 0);
                                                            $egresos = floatval($saldoInfo['egresos'] ?? 0);
                                                            $saldoActual = ($saldoInicial + $ingresos) - $egresos;
                                                    ?>
													<tr id="reg<?=$resultado['cba_id'];?>">
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['cba_id'];?></td>
														<td><?=$resultado['cba_nombre'];?></td>
														<td><?=$resultado['cba_banco'] ?? 'N/A';?></td>
														<td><?=$resultado['cba_numero_cuenta'] ?? 'N/A';?></td>
														<td><?=$resultado['cba_tipo'];?></td>
														<td><?=$metodoPagoTexto;?></td>
														<td>$<?=number_format($saldoInicial, 0, ",", ".");?></td>
														<td style="color: #2ecc71; font-weight: 600;">$<?=number_format($ingresos, 0, ",", ".");?></td>
														<td style="color: #e74c3c; font-weight: 600;">$<?=number_format($egresos, 0, ",", ".");?></td>
														<td style="color: <?=$saldoActual >= 0 ? '#2ecc71' : '#e74c3c'?>; font-weight: 700; font-size: 14px;">$<?=number_format($saldoActual, 0, ",", ".");?></td>
														<td>
															<?php 
																if ($resultado['cba_activa'] == 1) {
																	echo '<span class="label label-success">Activa</span>';
																} else {
																	echo '<span class="label label-danger">Inactiva</span>';
																}
															?>
														</td>
														
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0280','DT0281'])){?>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0280'])){?>
                                                                            <li><a href="cuentas-bancarias-editar.php?id=<?=base64_encode($resultado['cba_id']);?>"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?></a></li>
                                                                        <?php } if(Modulos::validarSubRol(['DT0281']) && !$cuentaEnUso){?>
                                                                            <li><a href="cuentas-bancarias-eliminar.php?id=<?=base64_encode($resultado['cba_id']);?>"><?=$frases[174][$datosUsuarioActual['uss_idioma']];?></a></li>
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
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								<div class="col-md-4 col-lg-3">
									<?php require_once(ROOT_PATH."/main-app/compartido/publicidad-lateral.php");?>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page container -->
        <?php require_once(ROOT_PATH."/main-app/compartido/footer.php");?>
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
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
    <script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js"></script>
    <!-- end js include path -->
</body>
</html>


