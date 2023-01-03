<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0062';?>
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
                                <div class="page-title"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								
								
								<div class="col-md-4 col-lg-3">
									<div class="panel">
										<header class="panel-heading panel-heading-red">MENÚ <?=strtoupper($frases[5][$datosUsuarioActual['uss_idioma']]);?></header>
										<div class="panel-body">
											<p><a href="cursos-intensidad.php">I.H por curso</a></p>
											<p><a href="cursos-aplicar-formato.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Aplicar Formato 1</a></p>
											<p><a href="cursos-cambiar-matricula.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Poner en $0 la matricula</a></p>
											<p><a href="cursos-cambiar-pension.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Poner en $0 la pensión</a></p>
										</div>
                                	</div>

									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></header>
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
														<a href="cursos-agregar.php" id="addRow" class="btn deepPink-bgcolor">
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
														<th><?=$frases[5][$datosUsuarioActual[8]];?></th>
														<th>Formato boletín</th>
														<th>Matrícula</th>
														<th>Pensión</th>
														<th>#Periodos</th>
														<th>Siguiente</th>
                                        				<th>Grupos</th>
														<th><?=$frases[54][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php													
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_grados AS g1
													 INNER JOIN academico_grados AS g2 ON g2.gra_id=g1.gra_grado_siguiente
													 WHERE g1.gra_estado=1
													 ORDER BY g1.gra_vocal
													 ");
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><a href="../compartido/listado-estudiante.php?grado=<?=$resultado[0];?>" target="_blank" style="text-decoration:underline;" title="Imprimir lista de estudiantes"><?=$resultado[0];?></a></td>
														<td><a href="estudiantes.php?curso=<?=$resultado[0];?>" style="text-decoration: underline;"><?=$resultado[2];?></a></td>
														<td><?=$resultado[3];?></td>
														<td>$<?=number_format($resultado[4]);?></td>
														<td>$<?=number_format($resultado[5]);?></td>
														<td><?=$resultado[11];?></td>
														<td><?=$resultado[14];?></td>
														<td>
															<?php													
															$consultaGrupo = mysqli_query($conexion, "SELECT * FROM academico_grupos");
															$contReg = 1;
															while($resultadoG = mysqli_fetch_array($consultaGrupo, MYSQLI_BOTH)){
															?>
															<!-- Estos datos están quemados para no hacer un mientras que por cada curso. Lo ideal es consultar la tabla academico_grupos -->
															<a href="../compartido/informe-consolidado-perdidos.php?curso=<?=$resultado[0];?>&grupo=<?=$resultadoG[0];?>" style="text-decoration:underline;" target="_blank"><?=$resultadoG[2];?></a>
															<?php 
															}
															?>
														</td>
														
														<td>
															<div class="btn-group">
																  <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual[8]];?></button>
																  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	  <i class="fa fa-angle-down"></i>
																  </button>
																  <ul class="dropdown-menu" role="menu">
																	  <li><a href="cursos-editar.php?id=<?=$resultado[0];?>"><?=$frases[165][$datosUsuarioActual[8]];?></a></li>
																	  <li><a href="cursos-eliminar.php?id=<?=$resultado[0];?>">Eliminar</a></li>
																	  <li><a href="../compartido/matricula-boletin-curso-<?=$resultado[3];?>.php?curso=<?=$resultado[0];?>&periodo=<?=$config[2];?>" title="Imprimir boletin por curso" target="_blank">Boletin por curso</a></li>
																	  <li><a href="../compartido/matricula-libro-curso.php?curso=<?=$resultado[0];?>" title="Imprimir Libro por curso" target="_blank">Libro por curso</a></li>
																	  <li><a href="../compartido/matriculas-formato3-curso.php?curso=<?=$resultado[0];?>" title="Hoja de matrícula por curso" target="_blank">Matrícula por curso</a></li>
																	  <li><a href="cursos-promocionar-estudiantes.php?curso=<?=$resultado[0];?>" title="Promocionar estudiantes" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Promocionar estudiantes</a></li>
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