<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0001';?>
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
                                <div class="page-title"><?=$frases[209][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								
								
								<div class="col-md-4 col-lg-3">
									<div class="panel">
										<header class="panel-heading panel-heading-red">MENÚ <?=strtoupper($frases[209][$datosUsuarioActual['uss_idioma']]);?></header>
										<div class="panel-body">
											<p><a href="estudiantes-promedios.php">Promedios estudiantiles</a></p>
											<p><a href="estudiantes-importar-excel.php">Imporatar matrículas excel</a></p>
											<p><a href="estudiantes-consolidado-final.php">Consolidado final</a></p>
											<p><a href="estudiantes-nivelaciones.php">Nivelaciones</a></p>
											<p><a href="estudiantes-certificados.php">Certificados</a></p>
											<hr>
											<p><a href="../compartido/reporte-estudiantes.php" target="_blank">Sacar Informe</a></p>
											<p><a href="../compartido/reporte-pasos.php" target="_blank">Informe pasos matrícula</a></p>
											<p><a href="../compartido/reporte-informe-parcial.php" target="_blank">Reporte informe parcial</a></p>
											<p><a href="../compartido/reporte-ver-observador.php" target="_blank">Reporte vista observador</a></p>
											<p><a href="../compartido/excel-estudiantes.php" target="_blank">Exportar Excel</a></p>
											<?php if(isset($_GET["curso"]) and is_numeric($_GET["curso"]) and isset($_GET["grupo"]) and is_numeric($_GET["grupo"])){?>
												<p><a href="../compartido/planilla-estudiantes.php?grado=<?=$_GET["curso"];?>&grupo=<?=$fgrupo;?>" target="_blank">Imprimir Planilla</a></p>
											<?php }elseif(isset($_GET["curso"]) and is_numeric($_GET["curso"])){ ?>
												<p><a href="../compartido/planilla-estudiantes.php?grado=<?=$_GET["curso"];?>" target="_blank">Imprimir Planilla</a></p>
											<?php }?>
											<hr>
											<p><a href="estudiantes-matricular-todos.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Matricular todos</a></p>
											<p><a href="estudiantes-matriculas-cancelar.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Cancelar todos</a></p>
											<p><a href="estudiantes-eliminar-todos.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Eliminar todos</a></p>
											<hr>
											<p><a href="estudiantes-nuevos-todos.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Todos nuevos</a></p>
											<p><a href="estudiantes-antiguos-todos.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Todos antiguos</a></p>
											<hr>
											<p><a href="estudiantes-grupoa-todos.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Asignar a todos el grupo A</a></p>
											<hr>
											<p><a href="estudiantes-documento-usuario-actualizar.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Colocar documento como usuario de acceso</a></p>
											<p><a href="estudiantes-crear-usuarios.php" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Comprobar y Crear usuario a estudiantes</a></p>
										</div>
                                	</div>
									
									
									<h4 align="center"><?=strtoupper($frases[205][$datosUsuarioActual[8]]);?></h4>
									<div class="panel">
										<?php
										$filtro = '';
										if(isset($_GET["curso"]) AND is_numeric($_GET["curso"])){$filtro .= " AND mat_grado='".$_GET["curso"]."'";$fcurso=$_GET["curso"];}
										if(isset($_GET["grupo"]) AND is_numeric($_GET["grupo"])){$filtro .= " AND mat_grupo='".$_GET["grupo"]."'";$fgrupo=$_GET["grupo"];}
										if(isset($_GET["genero"]) AND is_numeric($_GET["genero"])){$filtro .= " AND mat_genero='".$_GET["genero"]."'";$fgenero=$_GET["genero"];}
										
										$consultaEstadisticasEstudiantes=mysqli_query($conexion, "SELECT
										(SELECT count(mat_id) FROM academico_matriculas WHERE mat_eliminado=0),
										(SELECT count(mat_id) FROM academico_matriculas WHERE mat_eliminado=0 AND mat_estado_matricula=1 $filtro),
										(SELECT count(mat_id) FROM academico_matriculas WHERE mat_eliminado=0 AND mat_estado_matricula=2 $filtro),
										(SELECT count(mat_id) FROM academico_matriculas WHERE mat_eliminado=0 AND mat_estado_matricula=3 $filtro),
										(SELECT count(mat_id) FROM academico_matriculas WHERE mat_eliminado=0 AND mat_estado_matricula=4 $filtro),
										(SELECT count(mat_id) FROM academico_matriculas WHERE mat_eliminado=0 AND mat_genero=126),
										(SELECT count(mat_id) FROM academico_matriculas WHERE mat_eliminado=0 AND mat_genero=127)");
										$estadisticasEstudiantes = mysqli_fetch_array($consultaEstadisticasEstudiantes, MYSQLI_BOTH);
										$porcentajeMatriculados = round(($estadisticasEstudiantes[1]/$estadisticasEstudiantes[0])*100,2);
										$porcentajeAsistentes = round(($estadisticasEstudiantes[2]/$estadisticasEstudiantes[0])*100,2);
										$porcentajeCancelados = round(($estadisticasEstudiantes[3]/$estadisticasEstudiantes[0])*100,2);
										$porcentajeNoMatriculados = round(($estadisticasEstudiantes[4]/$estadisticasEstudiantes[0])*100,2);
										
										$porcentajeHombres = round(($estadisticasEstudiantes[5]/$estadisticasEstudiantes[0])*100,2);
										$porcentajeMujeres = round(($estadisticasEstudiantes[6]/$estadisticasEstudiantes[0])*100,2);
										?>
										<header class="panel-heading panel-heading-yellow">ESTADOS</header>
										<div class="panel-body">
													<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$fcurso;?>&grupo=<?=$fgrupo;?>&estadoM=1&genero=<?=$fgenero;?>">Matrículados: <b><?=$estadisticasEstudiantes[1];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajeMatriculados;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajeMatriculados;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											
													<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$fcurso;?>&grupo=<?=$fgrupo;?>&estadoM=2&genero=<?=$fgenero;?>">Asistentes: <b><?=$estadisticasEstudiantes[2];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajeAsistentes;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajeAsistentes;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											
													<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$fcurso;?>&grupo=<?=$fgrupo;?>&estadoM=3&genero=<?=$fgenero;?>">Cancelados: <b><?=$estadisticasEstudiantes[3];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajeCancelados;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajeCancelados;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											
													<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$fcurso;?>&grupo=<?=$fgrupo;?>&estadoM=4&genero=<?=$fgenero;?>">No matrículados: <b><?=$estadisticasEstudiantes[4];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajeNoMatriculados;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajeNoMatriculados;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
										</div>
                                	</div>
									
									
									<div class="panel">
												<?php if(isset($_GET["estadoM"])){$festadoM=$_GET["estadoM"];}?>
										<header class="panel-heading panel-heading-yellow">GÉNEROS</header>
										<div class="panel-body">
													<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$fcurso;?>&grupo=<?=$fgrupo;?>&estadoM=<?=$festadoM;?>&genero=126">Hombres: <b><?=$estadisticasEstudiantes[5];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajeHombres;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajeHombres;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											
													<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$fcurso;?>&grupo=<?=$fgrupo;?>&estadoM=<?=$festadoM;?>&genero=127">Mujeres: <b><?=$estadisticasEstudiantes[6];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajeMujeres;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajeMujeres;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
										</div>
                                	</div>
									
									
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
												$porcentajePorGrado = round(($estudiantesPorGrado[0]/$estadisticasEstudiantes[0])*100,2);
												if(isset($fcurso) AND $curso['gra_id']==$fcurso) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
											
												<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$curso['gra_id'];?>&grupo=<?=$fgrupo;?>&genero=<?=$fgenero;?>&estadoM=<?=$festadoM;?>" <?=$estiloResaltado;?>><?=strtoupper($curso['gra_nombre']);?>: <b><?=$estudiantesPorGrado[0];?></b></a></div>
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
											<p align="center"><a href="estudiantes.php">VER TODOS</a></p>
										</div>
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Grupos </header>
										<div class="panel-body">
											<?php
											$grupos = mysqli_query($conexion, "SELECT * FROM academico_grupos");
											while($grupo = mysqli_fetch_array($grupos, MYSQLI_BOTH)){
												if(isset($_GET["grupo"]) AND $grupo['gru_id']==$_GET["grupo"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$grupo['gru_id'];?>&curso=<?=$fcurso;?>&genero=<?=$fgenero;?>&estadoM=<?=$festadoM;?>" <?=$estiloResaltado;?>><?=strtoupper($grupo['gru_nombre']);?></a></p>
											<?php }?>
											<p align="center"><a href="estudiantes.php">VER TODOS</a></p>
										</div>
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Cantidades </header>
										<div class="panel-body">
											<?php
											for($i=10; $i<=100; $i=$i+10){
												if(isset($_GET["cantidad"]) AND $i==$_GET["cantidad"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$fgrupo;?>&curso=<?=$fcurso;?>&cantidad=<?=$i;?>&genero=<?=$fgenero;?>&estadoM=<?=$festadoM;?>" <?=$estiloResaltado;?>><?=$i." estudiantes";?></a></p>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$fcurso;?>&grupo=<?=$fgrupo;?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									
									
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								
								<div class="col-md-8 col-lg-9">
									<?php
										if(isset($_GET['msgsion']) AND $_GET['msgsion']==''){
										$aler='alert-danger';
										$mensajeSion='Por favor, verifique todos los datos del estudiante y llene los campos vacios.';
												?>
									<div class="alert alert-block <?=$aler;?>">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<h4 class="alert-heading">SION!</h4>
										<p><?=$mensajeSion;?></p>
									</div>
									<?php 
									}
										if(isset($_GET['msgsion']) AND $_GET['msgsion']!=''){
										$aler='alert-success';
										$mensajeSion=$_GET['msgsion'];
										if($_GET['stadsion']!=true){
											$aler='alert-danger';
										}
												?>
									<div class="alert alert-block <?=$aler;?>">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<h4 class="alert-heading">SION!</h4>
										<p><?=$mensajeSion;?></p>
									</div>
									<?php 
									}
									if(isset($_GET['msgsintia'])){
										$aler='alert-success';
										if($_GET['stadsintia']!=true){
										$aler='alert-danger';
										}
									?>
									<div class="alert alert-block <?=$aler;?>">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<h4 class="alert-heading">SINTIA!</h4>
										<p><?=$_GET['msgsintia'];?></p>
									</div>
									<?php }?>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[209][$datosUsuarioActual['uss_idioma']];?></header>
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
														<a href="estudiantes-agregar.php" id="addRow" class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                        				<th>Cod. Sistema</th>
														<th><?=$frases[246][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[241][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[61][$datosUsuarioActual[8]];?></th>
                                        				<th>Acudiente</th>
														<th><?=$frases[26][$datosUsuarioActual[8]];?></th>
														<th>Usuario</th>
														<th>Notify</th>
														<th><?=$frases[54][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													if(isset($_GET["estadoM"]) AND is_numeric($_GET["estadoM"])){$filtro .= " AND mat_estado_matricula='".$_GET["estadoM"]."'";}
													
													$filtroLimite = '';
													if(is_numeric($_GET["cantidad"])){$filtroLimite = "LIMIT 0,".$_GET["cantidad"];}
													
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas
													 INNER JOIN academico_grados ON gra_id=mat_grado
													 INNER JOIN academico_grupos ON gru_id=mat_grupo
													 INNER JOIN usuarios ON uss_id=mat_id_usuario
													 LEFT JOIN ".$baseDatosServicios.".opciones_generales ON ogen_id=mat_genero
													 WHERE mat_eliminado=0 $filtro
													 ORDER BY mat_primer_apellido
													 $filtroLimite");
													 $contReg = 1;
													$estadosMatriculas = array("","Matriculado","Asistente","Cancelado","No Matriculado");
													$estadosEtiquetas = array("","text-success","text-warning","text-danger","text-warning");
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$consultaAcudientes=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$resultado["mat_acudiente"]."'");
														$acudiente = mysqli_fetch_array($consultaAcudientes, MYSQLI_BOTH);
														$bgColor = '';
														if($resultado['uss_bloqueado']==1) $bgColor = '#ff572238';
													 ?>
													<tr style="background-color:<?=$bgColor;?>;">
														<td>
															<?php if($resultado["mat_compromiso"]==1){?>
																<a href="estudiantes-activar.php?id=<?=$resultado["mat_id"];?>" title="Activar para la matricula" onClick="if(!confirm('Esta seguro de ejecutar esta acción?')){return false;}"><img src="../files/iconos/msn_blocked.png" height="20" width="20"></a>
															<?php }else{?>
																<a href="estudiantes-bloquear.php?id=<?=$resultado["mat_id"];?>" title="Bloquear para la matricula" onClick="if(!confirm('Esta seguro de ejecutar esta acción?')){return false;}"><img src="../files/iconos/agt_action_success.png" height="20" width="20"></a>
															<?php }?>
															<a href="../compartido/informe-parcial.php?estudiante=<?=$resultado["mat_id"];?>" title="Informe Parcial" target="_blank" style="text-decoration:underline;"><?=$resultado["mat_id"];?></a>
														</td>
                                        				<td><a href="../compartido/matriculas-formato3.php?ref=<?=$resultado["mat_matricula"];?>" target="_blank" style="text-decoration:underline;"><?=$resultado["mat_matricula"];?></a></td>
														<td><span class="<?=$estadosEtiquetas[$resultado['mat_estado_matricula']];?>"><?=$estadosMatriculas[$resultado['mat_estado_matricula']];?></span></td>
														<td><?=$resultado['mat_documento'];?></td>
														<?php $nombre=strtoupper($resultado['mat_primer_apellido']." ".$resultado['mat_segundo_apellido']." ".$resultado['mat_nombres']." ".$resultado['mat_nombre2']); ?>
														<?php
														if($resultado["mat_inclusion"]==0){
															$color=$config[6];
															echo '<td style="color:'.$color.';"><a href="../compartido/carnet.php?id='.$resultado["mat_id"].'" target="_blank" style="text-decoration:underline;">'.$nombre.'</a><br><a href="mensajes-redactar.php?destino='.$resultado[23].'" style="text-decoration:underline;">Enviar mensaje</a></td>';
														}else{
															$color=$config[5];
															echo '<td style="color:'.$color.';"><a href="../compartido/carnet.php?id='.$resultado["mat_id"].'" target="_blank" style="text-decoration:underline;"><b>'.$nombre.'</b></a><br><a href="mensajes-redactar.php?destino='.$resultado[23].'" style="text-decoration:underline;">Enviar mensaje</a></td>';
														}
														if(isset($acudiente[0]) AND $acudiente[4]!=''){$nombreAcudiente=strtoupper($acudiente[4].' '.$acudiente["uss_nombre2"].' '.$acudiente["uss_apellido1"].' '.$acudiente["uss_apellido2"]); $idAcudiente=$acudiente[0];}
														?>
														<td><a href="usuarios-editar.php?id=<?=$idAcudiente;?>" style="text-decoration:underline; color:#0C0;"><?=$nombreAcudiente;?></a><?php if(isset($acudiente[4]) AND $acudiente[4]!=""){?><br><a href="mensajes-redactar.php?destino=<?=$acudiente[0];?>" style="text-decoration:underline;">Enviar mensaje</a><?php }?></td>
														<td><?=strtoupper($resultado['gra_nombre']." ".$resultado['gru_nombre']);?></td>
														<td>
															<?=$resultado['uss_usuario'];?>
														</td>
														<td><?=$resultado['mat_notificacion1'];?></td>
														<td>
															<div class="btn-group">
																<button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual[8]];?></button>
																<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	<i class="fa fa-angle-down"></i>
																</button>
																<ul class="dropdown-menu" role="menu">
																	<li><a href="estudiantes-editar.php?id=<?=$resultado['mat_id'];?>"><?=$frases[165][$datosUsuarioActual[8]];?></a></li>
																	<li><a href="estudiantes-crear-sion.php?id=<?=$resultado["mat_id"];?>" onClick="if(!confirm('Esta seguro que desea transferir este estudiante a SION?')){return false;}">Transferir a SION</a></li>
																	<li><a href="finanzas-cuentas.php?id=<?=$resultado["mat_id_usuario"];?>" target="_blank">Estado de cuenta</a></li>
																	<li><a href="guardar.php?get=17&idR=<?=$resultado['mat_id_usuario'];?>&lock=<?=$resultado['uss_bloqueado'];?>">Bloquear/Desbloquear</a></li>
																	<li><a href="aspectos-estudiantiles.php?idR=<?=$resultado['mat_id_usuario'];?>">Ficha estudiantil</a></li>
																	<li><a href="reportes-lista.php?est=<?=$resultado["mat_id_usuario"];?>" target="_blank">Disciplina</a></li>
																	<li><a href="estudiantes-consultar-notas.php?idE=<?=$resultado["mat_id"];?>" target="_blank">Consulta Académica</a></li>
																	<li><a href="estudiantes-eliminar.php?idE=<?=$resultado["mat_id"];?>&idU=<?=$resultado["mat_id_usuario"];?>" target="_blank" onClick="if(!confirm('Esta seguro de ejecutar esta acción?')){return false;}">Eliminar</a></li>
																	<li><a href="estudiantes-crear-usuario-estudiante.php?id=<?=$resultado["mat_id"];?>" target="_blank" onClick="if(!confirm('Esta seguro de ejecutar esta acción?')){return false;}">Generar usuario</a></li>
																	<li><a href="estudiantes-cambiar-grupo.php?id=<?=$resultado["mat_id"];?>" target="_blank">Cambiar de grupo</a></li>
																	<li><a href="estudiantes-retirar.php?id=<?=$resultado["mat_id"];?>" target="_blank">Retirar</a></li>
																	<hr>
																	<li><a href="../compartido/matricula-boletin-curso-<?=$resultado['gra_formato_boletin'];?>.php?id=<?=$resultado["mat_id"];?>&periodo=<?=$config[2];?>" target="_blank">Boletín</a></li>
																	<li><a href="../compartido/matricula-libro.php?id=<?=$resultado["mat_id"];?>&periodo=<?=$config[2];?>" target="_blank">Libro Final</a></li>
																	<hr>
																	<li><a href="estudiantes-reservar-cupo.php?idEstudiante=<?=$resultado["mat_id"];?>" onClick="if(!confirm('Esta seguro que desea reservar el cupo para este estudiante?')){return false;}">Reservar cupo</a></li>
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