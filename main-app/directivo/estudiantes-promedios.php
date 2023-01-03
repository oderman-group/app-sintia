<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0002';?>
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
                                <div class="page-title"><?=$frases[247][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-3">
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$cursos = mysqli_query($conexion, "SELECT * FROM academico_grados
											WHERE gra_estado=1
											ORDER BY gra_vocal");
											while($curso = mysqli_fetch_array($cursos, MYSQLI_BOTH)){
												if(isset($_GET["curso"])&&$curso['gra_id']==$_GET["curso"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$curso['gra_id'];?>&grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&orden=<?php if (isset($_GET["orden"])){echo $_GET["orden"];}?>" <?=$estiloResaltado;?>><?=strtoupper($curso['gra_nombre']);?></a></p>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&orden=<?php if (isset($_GET["orden"])){echo $_GET["orden"];}?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Grupos </header>
										<div class="panel-body">
											<?php
											$grupos = mysqli_query($conexion, "SELECT * FROM academico_grupos");
											while($grupo = mysqli_fetch_array($grupos, MYSQLI_BOTH)){
												if(isset($_GET["grupo"])&&$grupo['gru_id']==$_GET["grupo"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$grupo['gru_id'];?>&curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&orden=<?php if (isset($_GET["orden"])){echo $_GET["orden"];}?>" <?=$estiloResaltado;?>><?=strtoupper($grupo['gru_nombre']);?></a></p>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&orden=<?php if (isset($_GET["orden"])){echo $_GET["orden"];}?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Periodos </header>
										<div class="panel-body">
											<?php
											for($i=1; $i<=4; $i++){
												if(isset($_GET["periodo"])&&$i==$_GET["periodo"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?periodo=<?=$i;?>&grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&orden=<?php if (isset($_GET["orden"])){echo $_GET["orden"];}?>&carga=<?php if (isset($_GET["carga"])){echo $_GET["carga"];}?>?>" <?=$estiloResaltado;?>><?=$i." periodo";?></a></p>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&orden=<?php if (isset($_GET["orden"])){echo $_GET["orden"];}?>&carga=<?php if (isset($carga['car_id'])){echo $carga['car_id'];}?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									
									
									
									
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								
								<div class="col-md-6">
                                    <div class="panel">
										<header class="panel-heading panel-heading-blue">PROMEDIOS GENERALES </header>
										<div class="panel-body">
											<?php
											$filtro = '';
											if(isset($_GET["curso"])&&is_numeric($_GET["curso"])){$filtro .= " AND mat_grado='".$_GET["curso"]."'";}
											if(isset($_GET["grupo"])&&is_numeric($_GET["grupo"])){$filtro .= " AND mat_grupo='".$_GET["grupo"]."'";}
											
											$filtroBoletin = '';
											if(isset($_GET["periodo"])&&is_numeric($_GET["periodo"])){$filtroBoletin .= " AND bol_periodo='".$_GET["periodo"]."'";}
											if(isset($_GET["carga"])&&is_numeric($_GET["carga"])){$filtroBoletin .= " AND bol_carga='".$_GET["carga"]."'";}
											
											$filtroLimite = '';
											if(isset($_GET["cantidad"])&&is_numeric($_GET["cantidad"])){$filtroLimite = "LIMIT 0,".$_GET["cantidad"];}
											
											$filtroOrden ='DESC';
											if(isset($_GET["orden"])&&$_GET["orden"]!=""){$filtroOrden = $_GET["orden"];}
											
											$destacados = mysqli_query($conexion, "SELECT ROUND(AVG(bol_nota),".$config['conf_decimales_notas'].") AS promedio, bol_estudiante, mat_nombres, mat_primer_apellido, mat_segundo_apellido, mat_grado FROM academico_boletin
											INNER JOIN academico_matriculas ON mat_id=bol_estudiante $filtro AND mat_eliminado=0
											WHERE bol_id=bol_id $filtroBoletin
											GROUP BY bol_estudiante ORDER BY promedio $filtroOrden
											$filtroLimite");
											$contP = 1;
											while($dest = mysqli_fetch_array($destacados, MYSQLI_BOTH)){
												$porcentaje = ($dest['promedio']/$config['conf_nota_hasta'])*100;
												if($dest['promedio'] < $config['conf_nota_minima_aprobar']) $colorGrafico = 'danger'; else $colorGrafico = 'info';
											?>
														<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><?="<b>".$contP.".</b> ".strtoupper($dest['mat_nombres']." ".$dest['mat_primer_apellido']);?>: <b><?=$dest['promedio'];?></b></div>
																	<div class="percent pull-right"><?=$porcentaje;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-<?=$colorGrafico;?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentaje;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>	
											<?php $contP++;}?>
										</div>
                                    </div>
                                </div>
								
								<div class="col-md-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Cantidades </header>
										<div class="panel-body">
											<?php
											for($i=10; $i<=50; $i=$i+10){
												if(isset($_GET["cantidad"])&&$i==$_GET["cantidad"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?cantidad=<?=$i;?>&grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&orden=<?php if (isset($_GET["orden"])){echo $_GET["orden"];}?>&carga=<?php if (isset($_GET["carga"])){echo $_GET["carga"];}?>?>" <?=$estiloResaltado;?>><?=$i." estudiantes";?></a></p>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&orden=<?php if (isset($_GET["orden"])){echo $_GET["orden"];}?>&carga=<?php if (isset($carga['car_id'])){echo $carga['car_id'];}?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Orden </header>
										<div class="panel-body">
											<p><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&orden=DESC&carga=<?php if (isset($_GET["carga"])){echo $_GET["carga"];}?>?>" <?php if($filtroOrden=='DESC') echo 'style="color: orange;"';?> >De mayor a menor</a></p>
											<p><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&orden=ASC&carga=<?php if (isset($_GET["carga"])){echo $_GET["carga"];}?>?>" <?php if($filtroOrden=='ASC') echo 'style="color: orange;"';?>>De menor a mayor</a></p>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&carga=<?php if (isset($carga['car_id'])){echo $carga['car_id'];}?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									<?php if(isset($_GET["curso"])&&is_numeric($_GET["curso"]) and isset($_GET["grupo"])&&is_numeric($_GET["grupo"])){?>
										<div class="panel">
											<header class="panel-heading panel-heading-purple"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?> </header>
											<div class="panel-body">
												<?php
												$cargas = mysqli_query($conexion, "SELECT * FROM academico_cargas 
												INNER JOIN academico_materias ON mat_id=car_materia
												WHERE car_curso='".$_GET["curso"]."' AND car_grupo='".$_GET["grupo"]."'
												ORDER BY mat_nombre");
												while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
													if($carga['car_id']==$_GET["carga"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
												?>
													<p><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&orden=<?php if (isset($_GET["orden"])){echo $_GET["orden"];}?>&carga=<?php if (isset($carga['car_id'])){echo $carga['car_id'];}?>" <?=$estiloResaltado;?>><?=strtoupper($carga['mat_nombre']);?></a></p>
												<?php }?>
												<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?php if (isset($_GET["grupo"])){echo $_GET["grupo"];}?>&curso=<?php if (isset($_GET["curso"])){echo $_GET["curso"];}?>&periodo=<?php if (isset($_GET["periodo"])){echo $_GET["periodo"];}?>&cantidad=<?php if (isset($_GET["cantidad"])){echo $_GET["cantidad"];}?>&orden=<?php if (isset($_GET["orden"])){echo $_GET["orden"];}?>">VER TODOS</a></p>
											</div>
										</div>
									<?php }?>
									
									
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