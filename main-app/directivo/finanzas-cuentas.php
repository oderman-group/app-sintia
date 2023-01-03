<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0093';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaE = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_id_usuario='".$_GET["id"]."'");
$e = mysqli_fetch_array($consultaE, MYSQLI_BOTH);
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
                                <div class="page-title"><?=$frases[209][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								
								
								<div class="col-md-4 col-lg-3">
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head" style="display: flex;">
                                            <header><span class="hidden-phone">Estado de cuenta</span> <span class="hidden-phone">#<?=$e[1];?></span></header>
											<div class="btn-group" style="margin-left: auto; margin-right: 5px;">
												<a class="btn btn-success" href="../compartido/pazysalvo.php?id=<?=$_GET["id"];?>" target="_blank"><i class="icon-file"></i> Generar paz y salvo</a>
											</div>
											
											<div class="btn-group">
												<a class="btn btn-danger" href="movimientos-agregar.php"><i class="icon-pencil"></i> Agregar movimiento</a>
											</div>
                                        </div>
                                        <div class="card-body">
											
                                        <div class="table-scrollable">
                                    		<table id="" class="display" style="width:100%;">
												<thead>
													<tr>
														<th></th>
														<th></th>
														<th></th>
													</tr>
												</thead>
												<tbody style="text-align: right;">
													<tr class="warning">
														<td colspan="2"></td>
														<td><strong>A nombre de:  </strong><?=strtoupper($e['mat_primer_apellido']." ".$e['mat_segundo_apellido']." ".$e['mat_nombres']." ".$e['mat_nombre2']);?></td>
													</tr>
													<tr>
														<td colspan="2"></td>
														<td><strong><?=number_format($e[12],0,".",".");?></strong></td>
													</tr>
													<tr class="info">
														<td colspan="2"></td>
														<td><?=$e[15];?></strong></td>
													</tr>
													<tr>
														<td colspan="2"></td>
														<td><?=$e[17];?></td>
													</tr>
													<tr>
														<td colspan="2"></td>
														<td><?=$e[9];?></td>
													</tr>
												</tbody>
                                            </table>
                                    		<table id="" class="display" style="width:100%;">
												<thead>
													<tr>
													<th>#</th>
													<th>Fecha</th>
													<th>Detalle</th>
													<th>Valor</th>
													<th></th>
													</tr>
												</thead>
												<tbody>
												<?php
												$consulta = mysqli_query($conexion, "SELECT * FROM finanzas_cuentas WHERE fcu_usuario='".$_GET["id"]."' AND fcu_anulado=0 ORDER BY fcu_id DESC");
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													?>		
															<!-- BEGIN PRODUCT INFO -->
															<tr>
															<td><?=$resultado[0];?></td>
															<td><?=$resultado[1];?></td>
															<td><?=$resultado[2];?></td>
															<td>$<?=number_format($resultado[3],2,".",",");?></td>
															<td><a href="guardar.php?get=11&idM=<?=$resultado[0];?>&id=<?=$_GET["id"];?>" onClick="if(!confirm('Desea anular este movimiento?')){return false;}"><img src="../files/iconos/1363803022_001_052.png"></a></td>
															</tr>
															<!-- END PRODUCT INFO -->
													<?php 
													}
														$consultaC=mysqli_query($conexion, "SELECT sum(fcu_valor) FROM finanzas_cuentas WHERE fcu_usuario='".$_GET["id"]."' AND fcu_anulado=0 AND fcu_tipo=3");
														$c = mysqli_fetch_array($consultaC, MYSQLI_BOTH);
														$consultaA=mysqli_query($conexion, "SELECT sum(fcu_valor) FROM finanzas_cuentas WHERE fcu_usuario='".$_GET["id"]."' AND fcu_anulado=0 AND fcu_tipo=1");
														$a = mysqli_fetch_array($consultaA, MYSQLI_BOTH);
														$t = $a[0] - $c[0];
														if($t>=0) $color = 'blue'; else $color = 'red';
													?>
													<!-- END PRODUCT INFO NOT VISIBLE IN PHONES -->
												</tbody>
                                            </table>
                                    		<table id="" class="display" style="width:100%;">
												<thead>
													<tr>
													<th class="span4"></th>
													<th class="span4"></th>
													<th class="span4"></th>
													</tr>
												</thead>
												<tbody>
													<tr class="warning">
													<td colspan="2">Total cobros:</td>
													<td><strong>$<?=number_format($c[0],2,".",",");?></strong></td>
													</tr>
													<tr>
													<td colspan="2">Total abonos:</td>
													<td><strong>$<?=number_format($a[0],2,".",",");?></strong></td>
													</tr>
													<tr class="info">
													<td colspan="2">Saldo Actual:</td>
													<td style="color:<?=$color;?>"><strong>$<?=number_format($t,2,".",",");?></strong></td>
													</tr>
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