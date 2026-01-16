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
                                <div class="page-title">Historial de Transferencias entre Cuentas</div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cuentas-bancarias.php" onClick="deseaRegresar(this)">Cuentas Bancarias</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Historial de Transferencias</li>
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
                                            <header>Historial de Transferencias entre Cuentas</header>
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
                                                        <a href="cuentas-bancarias.php" class="btn btn-info">
                                                            <i class="fa fa-arrow-left"></i> Volver a Cuentas Bancarias
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Formulario de Filtros -->
                                            <?php
                                            $cuentaOrigenFiltro = !empty($_GET['cuenta_origen']) ? $_GET['cuenta_origen'] : null;
                                            $cuentaDestinoFiltro = !empty($_GET['cuenta_destino']) ? $_GET['cuenta_destino'] : null;
                                            $fechaDesdeFiltro = !empty($_GET['fecha_desde']) ? $_GET['fecha_desde'] : null;
                                            $fechaHastaFiltro = !empty($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : null;
                                            $tieneFiltrosActivos = !empty($cuentaOrigenFiltro) || !empty($cuentaDestinoFiltro) || !empty($fechaDesdeFiltro) || !empty($fechaHastaFiltro);
                                            $borderColor = $tieneFiltrosActivos ? '#667eea' : '#dee2e6';
                                            $bgColor = $tieneFiltrosActivos ? '#f0f4ff' : '#f8f9fa';
                                            ?>
                                            <div class="row mb-3" style="margin-bottom: 20px; background: <?=$bgColor?>; padding: 15px; border-radius: 8px; border: 2px solid <?=$borderColor?>;">
                                                <div class="col-md-12">
                                                    <h6 style="margin-bottom: 15px; font-weight: 600; color: #495057;">
                                                        <i class="fa fa-filter"></i> Filtros de Búsqueda
                                                        <?php if ($tieneFiltrosActivos) { ?>
                                                            <span class="badge badge-primary" style="margin-left: 10px; font-size: 11px;">Activo</span>
                                                        <?php } ?>
                                                    </h6>
                                                    <form method="GET" action="" id="formFiltros">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="cuenta_origen">Cuenta Origen:</label>
                                                                    <select class="form-control" id="cuenta_origen" name="cuenta_origen">
                                                                        <option value="">-- Todas --</option>
                                                                        <?php
                                                                        $consultaCuentas = Movimientos::listarCuentasBancarias($conexion, $config);
                                                                        while($cuenta = mysqli_fetch_array($consultaCuentas, MYSQLI_BOTH)){
                                                                            $selected = ($cuentaOrigenFiltro == $cuenta['cba_id']) ? 'selected' : '';
                                                                            echo '<option value="'.htmlspecialchars($cuenta['cba_id']).'" '.$selected.'>'.htmlspecialchars($cuenta['cba_nombre']).'</option>';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="cuenta_destino">Cuenta Destino:</label>
                                                                    <select class="form-control" id="cuenta_destino" name="cuenta_destino">
                                                                        <option value="">-- Todas --</option>
                                                                        <?php
                                                                        mysqli_data_seek($consultaCuentas, 0);
                                                                        while($cuenta = mysqli_fetch_array($consultaCuentas, MYSQLI_BOTH)){
                                                                            $selected = ($cuentaDestinoFiltro == $cuenta['cba_id']) ? 'selected' : '';
                                                                            echo '<option value="'.htmlspecialchars($cuenta['cba_id']).'" '.$selected.'>'.htmlspecialchars($cuenta['cba_nombre']).'</option>';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label for="fecha_desde">Fecha Desde:</label>
                                                                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="<?= $fechaDesdeFiltro ?? ''; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label for="fecha_hasta">Fecha Hasta:</label>
                                                                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="<?= $fechaHastaFiltro ?? ''; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>&nbsp;</label>
                                                                    <div>
                                                                        <button type="submit" class="btn btn-primary" style="margin-right: 5px;">
                                                                            <i class="fa fa-search"></i> Filtrar
                                                                        </button>
                                                                        <a href="transferencias-cuentas.php" class="btn btn-secondary">
                                                                            <i class="fa fa-eraser"></i> Limpiar
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Fecha</th>
                                                        <th>Cuenta Origen</th>
                                                        <th>Cuenta Destino</th>
                                                        <th>Monto</th>
                                                        <th>Observaciones</th>
                                                        <th>Usuario</th>
                                                        <th>Fecha Registro</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
                                                        $consulta = Movimientos::listarTransferenciasCuentas(
                                                            $conexion, 
                                                            $config, 
                                                            $cuentaOrigenFiltro, 
                                                            $cuentaDestinoFiltro, 
                                                            $fechaDesdeFiltro, 
                                                            $fechaHastaFiltro
                                                        );
                                                        $contReg = 1;
                                                        while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                    ?>
													<tr>
                                                        <td><?=$contReg;?></td>
                                                        <td><?=date('d/m/Y', strtotime($resultado['tcb_fecha']));?></td>
                                                        <td><?=htmlspecialchars($resultado['cuenta_origen_nombre'] ?? 'N/A');?></td>
                                                        <td><?=htmlspecialchars($resultado['cuenta_destino_nombre'] ?? 'N/A');?></td>
                                                        <td style="font-weight: 600; color: #2c3e50;">$<?=number_format(floatval($resultado['tcb_monto']), 0, ",", ".");?></td>
                                                        <td><?=htmlspecialchars($resultado['tcb_observaciones'] ?? '');?></td>
                                                        <td><?=htmlspecialchars($resultado['usuario_nombre'] ?? 'N/A');?></td>
                                                        <td><?=date('d/m/Y H:i', strtotime($resultado['tcb_fecha_registro']));?></td>
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
