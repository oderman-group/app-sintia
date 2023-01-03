<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0032';?>
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
                                <div class="page-title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								
								
								<div class="col-md-4 col-lg-3">
									<div class="panel">
										<header class="panel-heading panel-heading-red">MENÚ <?=strtoupper($frases[12][$datosUsuarioActual['uss_idioma']]);?></header>
										<div class="panel-body">
                                        	<p><a href="cargas-eliminar-todas.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Eliminar todas las cargas</p>
											<p><a href="cargas-transferir.php">Transferir cargas</a></p>
											<p><a href="cargas-estilo-notas.php">Estilo de notas</a></p>
											<p><a href="cargas-indicadores-obligatorios.php">Indicadores obligatorios</a></p>
										</div>
                                	</div>
									
									<?php
										$filtro = '';
										if(is_numeric($_GET["curso"])){$filtro .= " AND car_curso='".$_GET["curso"]."'";}
										if(is_numeric($_GET["grupo"])){$filtro .= " AND car_grupo='".$_GET["grupo"]."'";}
										if(is_numeric($_GET["docente"])){$filtro .= " AND car_docente='".$_GET["docente"]."'";}
										if(is_numeric($_GET["asignatura"])){$filtro .= " AND car_materia='".$_GET["asignatura"]."'";}
										
										$consultaEstadisticaCarga=mysqli_query($conexion, "SELECT (SELECT count(car_id) FROM academico_cargas)");
										$estadisticasCargas = mysqli_fetch_array($consultaEstadisticaCarga, MYSQLI_BOTH);
										?>
									
									<h4 align="center"><?=strtoupper($frases[205][$datosUsuarioActual[8]]);?></h4>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$cursos = mysqli_query($conexion, "SELECT * FROM academico_grados
											WHERE gra_estado=1
											ORDER BY gra_vocal
											");
											while($curso = mysqli_fetch_array($cursos, MYSQLI_BOTH)){
												$consultaEstudianteGrado=mysqli_query($conexion, "SELECT count(car_id) FROM academico_cargas WHERE car_curso='".$curso['gra_id']."'");
												$estudiantesPorGrado = mysqli_fetch_array($consultaEstudianteGrado, MYSQLI_BOTH);
												$porcentajePorGrado = round(($estudiantesPorGrado[0]/$estadisticasCargas[0])*100,2);
												if($curso['gra_id']==$_GET["curso"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
											
												<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$curso['gra_id'];?>&grupo=<?=$_GET["grupo"];?>&docente=<?=$_GET["docente"];?>&asignatura=<?=$_GET["asignatura"];?>" <?=$estiloResaltado;?>><?=strtoupper($curso['gra_nombre']);?>: <b><?=$estudiantesPorGrado[0];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajePorGrado;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajePorGrado;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$_GET["grupo"];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Grupos </header>
										<div class="panel-body">
											<?php
											$grupos = mysqli_query($conexion, "SELECT * FROM academico_grupos
											");
											while($grupo = mysqli_fetch_array($grupos, MYSQLI_BOTH)){
												if($grupo['gru_id']==$_GET["grupo"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$grupo['gru_id'];?>&curso=<?=$_GET["curso"];?>&docente=<?=$_GET["docente"];?>&asignatura=<?=$_GET["asignatura"];?>" <?=$estiloResaltado;?>><?=strtoupper($grupo['gru_nombre']);?></a></p>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$_GET['curso'];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[28][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$docentes = mysqli_query($conexion, "SELECT * FROM usuarios
											WHERE uss_tipo=2 AND uss_bloqueado=0
											ORDER BY uss_nombre
											");
											while($docente = mysqli_fetch_array($docentes, MYSQLI_BOTH)){
												$consultaCargaDocente=mysqli_query($conexion, "SELECT count(car_id) FROM academico_cargas WHERE car_docente='".$docente['uss_id']."'");
												$cargasPorDocente = mysqli_fetch_array($consultaCargaDocente, MYSQLI_BOTH);
												$porcentajePorGrado = round(($cargasPorDocente[0]/$estadisticasCargas[0])*100,2);
												if($docente['uss_id']==$_GET["docente"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
											
												<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$_GET['curso'];?>&grupo=<?=$_GET["grupo"];?>&docente=<?=$docente['uss_id'];?>&asignatura=<?=$_GET["asignatura"];?>" <?=$estiloResaltado;?>><?=strtoupper($docente['uss_nombre']);?>: <b><?=$cargasPorDocente[0];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajePorGrado;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajePorGrado;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$_GET["grupo"];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$docentes = mysqli_query($conexion, "SELECT * FROM academico_materias
											ORDER BY mat_nombre
											");
											while($docente = mysqli_fetch_array($docentes, MYSQLI_BOTH)){
												$consultaCargaDocente=mysqli_query($conexion, "SELECT count(car_id) FROM academico_cargas WHERE car_materia='".$docente['mat_id']."'");
												$cargasPorDocente = mysqli_fetch_array($consultaCargaDocente, MYSQLI_BOTH);
												$porcentajePorGrado = round(($cargasPorDocente[0]/$estadisticasCargas[0])*100,2);
												if($docente['mat_id']==$_GET["asignatura"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
											
												<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$_GET['curso'];?>&grupo=<?=$_GET["grupo"];?>&docente=<?=$_GET["docente"];?>&asignatura=<?=$docente['mat_id'];?>" <?=$estiloResaltado;?>><?=strtoupper($docente['mat_nombre']);?>: <b><?=$cargasPorDocente[0];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajePorGrado;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajePorGrado;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$_GET["grupo"];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Cantidades </header>
										<div class="panel-body">
											<?php
											for($i=10; $i<=100; $i=$i+10){
												if($i==$_GET["cantidad"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$_GET['grupo'];?>&curso=<?=$_GET["curso"];?>&cantidad=<?=$i;?>&docente=<?=$_GET["docente"];?>&asignatura=<?=$_GET["asignatura"];?>" <?=$estiloResaltado;?>><?=$i." cargas";?></a></p>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$_GET['curso'];?>&grupo=<?=$_GET["grupo"];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									
									
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></header>
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
														<a href="cargas-agregar.php" id="addRow" class="btn deepPink-bgcolor">
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
														<th>Docente</th>
														<th>Curso</th>
														<th>Asignatura</th>
														<th>I.H</th>
														<th>Periodo Actual</th>
                                        				<th style="text-align:center;">NOTAS<br>Declaradas - Registradas</th>
														<?php
														$p=1;
														while($p<=$config[19]){
															echo '<th style="text-align:center;">P'.$p.'</th>';
															$p++;
														}
														?>
														<th><?=$frases[54][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													if(is_numeric($_GET["estadoM"])){$filtro .= " AND mat_estado_matricula='".$_GET["estadoM"]."'";}
													
													$filtroLimite = '';
													if(is_numeric($_GET["cantidad"])){$filtroLimite = "LIMIT 0,".$_GET["cantidad"];}
													
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_cargas
													 INNER JOIN academico_grados ON gra_id=car_curso
													 INNER JOIN academico_grupos ON gru_id=car_grupo
													 INNER JOIN academico_materias ON mat_id=car_materia
													 INNER JOIN usuarios ON uss_id=car_docente
													 WHERE car_id=car_id $filtro
													 ORDER BY car_id
													 $filtroLimite");
													 $contReg = 1;
													$estadosMatriculas = array("","Matriculado","Asistente","Cancelado","No Matriculado");
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													$consultaCargaAcademica=mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_id='".$resultado[0]."'");
													$cargaAcademica = mysqli_fetch_array($consultaCargaAcademica, MYSQLI_BOTH);
													$cargaSP = $resultado[0];
													$periodoSP = $resultado['car_periodo'];
													include("../suma-porcentajes.php");
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><a href="../compartido/planilla-asistencia.php?grado=<?=$cargaAcademica["car_curso"];?>&grupo=<?=$cargaAcademica["car_grupo"];?>" target="_blank" style="text-decoration:underline; color:#00F;" title="Imprimir planilla Estudiantes"><?=$resultado['car_id'];?></a></td>
														<td><?=strtoupper($resultado['uss_nombre']);?></td>
														<td><?="[".$resultado['gra_id']."] ".strtoupper($resultado['gra_nombre']." ".$resultado['gru_nombre']);?></td>
														<td><?="[".$resultado['mat_id']."] ".strtoupper($resultado['mat_nombre']);?></td>
														<td><?=$resultado['car_ih'];?></td>
														<td><?=$resultado['car_periodo'];?></td>
                                        				<td><a href="../compartido/reporte-notas.php?carga=<?=$resultado[0];?>&per=<?=$resultado['car_periodo'];?>&grado=<?=$resultado["car_curso"];?>&grupo=<?=$resultado["car_grupo"];?>" target="_blank" style="text-decoration:underline; color:#00F;" title="Calificaciones"><?=$spcd[0];?>%&nbsp;&nbsp;-&nbsp;&nbsp;<?=$spcr[0];?>%</a></td>
														<?php
														//PERIODOS DE CADA MATERIA
														$p=1;
														while($p<=$config[19]){
														$ConsultaNumeroNotasBoletin=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_carga='".$resultado[0]."' AND bol_periodo='".$p."'");
														$numeroNotasBoletin = mysqli_num_rows($ConsultaNumeroNotasBoletin);
														$consultaPromedioPeriodo=mysqli_query($conexion, "SELECT avg(bol_nota) FROM academico_boletin WHERE bol_carga='".$resultado[0]."' AND bol_periodo='".$p."'");
														$promedioPeriodo = mysqli_fetch_array($consultaPromedioPeriodo, MYSQLI_BOTH);
														if($promedioPeriodo[0]<$config[5] and $promedioPeriodo[0]!="")$color = $config[6]; elseif($promedioPeriodo[0]>=$config[5]) $color = $config[7];
														echo '<td style="text-align:center; color:'.$color.'"><a href="../compartido/reportes-sabanas.php?curso='.$cargaAcademica["car_curso"].'&grupo='.$cargaAcademica["car_grupo"].'&per='.$p.'" target="_blank" style="text-decoration:underline; color:#00F;" data-toggle="popover" data-placement="top" title="Imprimir sabanas">'.$numeroNotasBoletin." - <b>".round($promedioPeriodo[0],2).'</b></a><br><br><a href="cargas-comportamiento.php?carga='.$resultado[0].'&periodo='.$p.'&grado='.$cargaAcademica[2].'&grupo='.$cargaAcademica[3].'" style="text-decoration:underline; color:red;">Comp.</a></td>';
														$p++;
														} ?>
														
														<td>
															<div class="btn-group">
																  <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual[8]];?></button>
																  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	  <i class="fa fa-angle-down"></i>
																  </button>
																  <ul class="dropdown-menu" role="menu">
																	  <li><a href="cargas-editar.php?idR=<?=$resultado['car_id'];?>"><?=$frases[165][$datosUsuarioActual[8]];?></a></li>
																	  <li><a href="cargas-horarios.php?id=<?=$resultado[0];?>" title="Ingresar horarios">Ingresar Horarios</a></li>
																	  <li><a href="periodos-resumen.php?carga=<?=$resultado[0];?>" title="Resumen Periodos"><?=$frases[84][$datosUsuarioActual[8]];?></a></li>
																	  <li><a href="cargas-indicadores.php?carga=<?=$resultado['car_id'];?>&docente=<?=$resultado['car_docente'];?>">Indicadores</a></li>
																	  <li><a href="cargas-eliminar.php?id=<?=$resultado[0];?>" title="Eliminar" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}"><?=$frases[174][$datosUsuarioActual[8]];?></a></li>
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