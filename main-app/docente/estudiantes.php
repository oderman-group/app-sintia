<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0010';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
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
                                <div class="page-title"><?=$frases[55][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-4 col-lg-3">
									
									<?php include("info-carga-actual.php");?>
							
									<?php include("filtros-cargas.php");?>
									
									<?php include("../compartido/publicidad-lateral.php");?>

								</div>
								
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[55][$datosUsuarioActual['uss_idioma']];?></header>
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
														<th><?=$frases[49][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[241][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[61][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[138][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[118][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[54][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
													 INNER JOIN usuarios ON uss_id=mat_id_usuario
													 WHERE mat_grado='".$datosCargaActual[2]."' AND mat_grupo='".$datosCargaActual[3]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido, mat_segundo_apellido, mat_nombres");
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$consultaGenero=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_id='".$resultado[8]."'");
														 $genero = mysqli_fetch_array($consultaGenero, MYSQLI_BOTH);
														//DEFINITIVAS
														$carga = $cargaConsultaActual;
														$periodo = $periodoConsultaActual;
														$estudiante = $resultado[0];
														include("../definitivas.php");
														if($definitiva<$config[5] and $definitiva!="") $colorNota = $config[6]; elseif($definitiva>=$config[5]) $colorNota = $config[7]; else {$colorNota = 'black'; $definitiva='';}
														 
														$colorEstudiante = '#000;';
														if($resultado['mat_inclusion']==1){$colorEstudiante = 'blue;';} 
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['mat_id'];?></td>
														<td>
															<?=$resultado['mat_documento'];?>
															
														</td>
														<td style="color: <?=$colorEstudiante;?>">
															<img src="../files/fotos/<?=$resultado['uss_foto'];?>" width="50">
															<?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?>
														</td>
														<td><?=$genero[1];?></td>
														<td><a href="calificaciones-estudiante.php?usrEstud=<?=$resultado['mat_id_usuario'];?>&periodo=<?=$periodoConsultaActual;?>&carga=<?=$cargaConsultaActual;?>" style="text-decoration:underline; color:<?=$colorNota;?>;"><?=$definitiva;?></a></td>
														<td>
															<div class="btn-group">
																	  <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual[8]];?></button>
																	  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																		  <i class="fa fa-angle-down"></i>
																	  </button>
																	  <ul class="dropdown-menu" role="menu">
																		<?php if(!isset($_SESSION['admin'])){?>
																	  <li><a href="auto-login.php?user=<?=$resultado['mat_id_usuario'];?>">Autologin</a></li>
																	  <?php }?>

															<?php if($datosCargaActual['car_director_grupo']==1){?>
																		  <li><a href="reportes-lista.php?est=<?=$resultado['mat_id_usuario'];?>&filtros=1">R. Disciplina</a></li>
																		  <li><a href="aspectos-estudiantiles.php?idR=<?=$resultado['mat_id_usuario'];?>">Ficha estudiantil</a></li>
															<?php }?>
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