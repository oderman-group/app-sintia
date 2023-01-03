<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0121';?>
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
                                <div class="page-title">Reserva de cupos</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								<?php
									$filtro = '';
									$filtroMat = '';
									if(isset($_GET["curso"]) AND is_numeric($_GET["curso"])){$filtroMat .= " AND mat_grado='".$_GET["curso"]."'";}
									if(isset($_GET["resp"]) AND is_numeric($_GET["resp"])){$filtro .= " AND genc_respuesta='".$_GET["resp"]."'";}
						
									$SQL = "SELECT * FROM general_encuestas
									INNER JOIN academico_matriculas ON mat_id=genc_estudiante $filtroMat
									INNER JOIN academico_grados ON gra_id=mat_grado
									INNER JOIN academico_grupos ON gru_id=mat_grupo
									WHERE genc_id=genc_id $filtro";
								?>
								
								
								
								<div class="col-md-4 col-lg-3">
									<h4 align="center"><?=strtoupper($frases[205][$datosUsuarioActual[8]]);?></h4>
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$cursos = mysqli_query($conexion, "SELECT * FROM academico_grados
											WHERE gra_estado=1
											ORDER BY gra_vocal");
											while($curso = mysqli_fetch_array($cursos, MYSQLI_BOTH)){
												$consultaEstudianteGrado=mysqli_query($conexion, "SELECT count(mat_id) FROM academico_matriculas WHERE mat_eliminado=0 AND mat_grado='".$curso['gra_id']."'");
												$estudiantesPorGrado = mysqli_fetch_array($consultaEstudianteGrado, MYSQLI_BOTH);
												if(isset($_GET["curso"]) AND $curso['gra_id']==$_GET["curso"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
											
												<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$curso['gra_id'];?>" <?=$estiloResaltado;?>><?=strtoupper($curso['gra_nombre']);?></a></div>
																</div>
															</div>
														</div>
											<?php }?>
											<p align="center"><a href="reservar-cupo.php">VER TODOS</a></p>
										</div>
                                    </div>
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Reserva de cupos</header>
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
														<th>ID</th>
														<th>Fecha</th>
														<th>Estudiante</th>
														<th>Grado</th>
														<th>Respuesta</th>
														<th>Motivo</th>
													</tr>
												</thead>
                                                <tbody>
													<?php
													$respuestas = array("","SI","NO");
													$consulta = mysqli_query($conexion, $SQL);
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){				
													?>
													<tr>
														<td><?=$resultado['genc_id'];?></td>
														<td><?=$resultado['genc_fecha'];?></td>
														<td><?=strtoupper($resultado['mat_primer_apellido']." ".$resultado['mat_nombres']);?></td>
														<td><?=strtoupper($resultado['gra_nombre']." ".$resultado['gru_nombre']);?></td>
														<td><?=$respuestas[$resultado['genc_respuesta']];?></td>
														<td><?=$resultado['genc_comentario'];?></td>
													</tr>
													<?php }?>
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
             <?php// include("../compartido/panel-configuracion.php");?>
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