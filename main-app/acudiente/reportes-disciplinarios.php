<?php include("session.php");?>
<?php $idPaginaInterna = 'AC0028';?>
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
                                <div class="page-title"><?=$frases[163][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
									
								<div class="col-md-9">
									
									<?php if($_GET["req"]==1){?>
										<div class="card card-topline-red">
											<div class="card-head">
												<header>Firma digital</header>
											</div>
											<div class="card-body">
												<p>Al hacer click en el botón <b>Enviar firma y comentario</b> está aceptando el reporte realizado a su acudido. Los comentarios colocados, llegarán a la persona encargada para ser tenidos en cuenta. Para firmar de forma digital sólo haga click en el botón <b>Enviar firma y comentario</b> y listo.</p>
												<form class="form-horizontal" action="guardar.php" method="get">
													<input type="hidden" name="get" value="1">
													<input type="hidden" name="id" value="<?=$_GET["id"];?>">
													<div class="form-group row">
														<div class="col-sm-12">
															<textarea name="comentario" class="form-control" rows="3" placeholder="Si desea puede escribir aquí un comentario" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
														</div>
													</div>

													<div class="form-group">
														<div class="offset-md-3 col-md-9">
															<button type="submit" class="btn btn-info">Enviar firma y comentario</button>
															<button type="reset" class="btn btn-default"><?=$frases[171][$datosUsuarioActual[8]];?></button>
														</div>
													</div>
												</form>
											</div>
										</div>
									<?php }?>
									
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[163][$datosUsuarioActual['uss_idioma']];?></header>
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
														<th><?=$frases[51][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[222][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[248][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Observaciones</th>
														<th title="Firma y aprobación del estudiante">F.E</th>
														<th title="Firma y aprobación del acudiente">F.A</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $filtro = '';
													 if($_GET["new"]==1){$filtro .= " AND dr_aprobacion_estudiante=0";}
													
													 $consulta = mysqli_query($conexion, "SELECT * FROM disciplina_reportes
													 INNER JOIN disciplina_faltas ON dfal_id=dr_falta
													 INNER JOIN disciplina_categorias ON dcat_id=dfal_id_categoria
													 INNER JOIN usuarios ON uss_id=dr_usuario
													 WHERE dr_estudiante='".$_GET["usrEstud"]."'
													 $filtro
													 ");
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['dr_fecha'];?></td>
														<td><?=$resultado['dcat_nombre'];?></td>
														<td><?=$resultado['dfal_codigo'];?></td>
														<td><?=$resultado['dfal_nombre'];?></td>
														<td><?=$resultado['dr_observaciones'];?></td>
														<td>
															<?php if($resultado['dr_aprobacion_estudiante']==0){?>
																-
															<?php }else{?>
																<i class="fa fa-check-circle" title="<?=$resultado['dr_aprobacion_estudiante_fecha'];?>"></i>
															<?php }?>
														</td>
														<td>
															<?php if($resultado['dr_aprobacion_acudiente']==0){?> 
																<a href="reportes-disciplinarios.php?usrEstud=<?=$_GET["usrEstud"];?>&req=1&id=<?=$resultado['dr_id'];?>">Firmar</a>
															<?php } else{?>
																<i class="fa fa-check-circle" title="<?=$resultado['dr_aprobacion_acudiente_fecha'];?>"></i>
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
                                        </div>
                                    </div>
                                </div>
								
								<div class="col-md-3">
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