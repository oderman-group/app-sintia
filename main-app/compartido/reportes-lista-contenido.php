<?php require_once("../class/Estudiantes.php"); ?>
<div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[97][$datosUsuarioActual['uss_idioma']];?> </div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
							<?php
								$filtro = '';
								include("../directivo/includes/barra-superior-reportes-lista.php");
							?>
                            <div class="row">
								
								<div class="col-md-12">
									<?php if(!empty($_GET["filtros"]) && base64_decode($_GET["filtros"])==1){?>
									<p style="background-color: antiquewhite; color: darkblue; padding: 5px;">
									Estás viendo este listado con filtros; para verlo completo quita los filtros.
									<a href="reportes-lista.php">Quitar filtros</a>
									</p>	
									<?php }?>


									<?php
									 if($datosUsuarioActual['uss_tipo']==2 and !isset($_GET["fest"])){?>

									<div class="alert alert-info">

										<i class="icon-exclamation-sign"></i><strong>INFORMACIÓN:</strong> Usted aquí solo verá los reportes disciplinarios que usted haya realizado a los estudiantes.

									</div>
								<?php }?>


									<?php if((!empty($datosCargaActual['car_director_grupo']) && $datosCargaActual['car_director_grupo']==1) && Modulos::validarPermisoEdicion()){?>
									<form class="form-horizontal" action="../compartido/reporte-disciplina-sacar.php" method="post" enctype="multipart/form-data" target="_blank">
										<input type="hidden" name="id" value="12">
										<input type="hidden" name="grado" value="<?=$datosCargaActual['car_curso'];?>">
										<input type="hidden" name="grupo" value="<?=$datosCargaActual['car_grupo'];?>">
										<input type="hidden" name="desde" value="<?=date("Y");?>-01-01">
										<input type="hidden" name="hasta" value="<?=date("Y-m-d");?>">

										<?php if(Modulos::validarPermisoEdicion()){?>
											<input type="submit" class="btn btn-primary" value="Ver reporte a mis estudiantes">&nbsp;
										<?php }?>
									</form>
									<?php }?>

                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[97][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[222][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[248][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[186][$datosUsuarioActual['uss_idioma']];?></th>
														<th title="Firma y aprobación del estudiante">F.E</th>
														<th title="Firma y aprobación del acudiente">F.A</th>
														<th>Comentario</th>
														<?php if(Modulos::validarPermisoEdicion()){?>
															<th>&nbsp;</th>
														<?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													if(!empty($_GET["est"])){$filtro .= " AND dr_estudiante='".base64_decode($_GET["est"])."'";}
													if(!empty($_GET["falta"])){$filtro .= " AND dr_falta='".base64_decode($_GET["falta"])."'";}
												
													if($datosUsuarioActual['uss_tipo']!=5 and !isset($_GET["fest"])){
													$filtro .= " AND dr_usuario='".$_SESSION["id"]."'";
													}

													include("../directivo/includes/consulta-paginacion-reportes-lista.php");
													
													$consulta = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disciplina_reportes dr
													INNER JOIN ".BD_DISCIPLINA.".disciplina_faltas ON dfal_id=dr_falta AND dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]}
													INNER JOIN ".BD_DISCIPLINA.".disciplina_categorias ON dcat_id=dfal_id_categoria AND dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$_SESSION["bd"]}
													INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat_id_usuario=dr_estudiante AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
													LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat_grado AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]}
													LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat_grupo AND gru.institucion={$config['conf_id_institucion']} AND gru.year={$_SESSION["bd"]}
													LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=dr_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
													WHERE dr_id=dr_id AND dr.institucion={$config['conf_id_institucion']} AND dr.year={$_SESSION["bd"]} $filtro
													LIMIT $inicio,$registros");
													$contReg = 1;
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													?>
                                                    
													<tr id="reg<?=$resultado['dr_id'];?>">
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['dr_fecha'];?></td>
														<td><a href="reportes-lista.php?est=<?=base64_encode($resultado['mat_id_usuario']);?>&filtros=<?=base64_encode(1);?>"><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></a><br><?=$resultado['gra_nombre']." ".$resultado['gru_nombre'];?></td>
														<td><?=$resultado['dcat_nombre'];?></td>
														<td><?=$resultado['dfal_codigo'];?></td>
														<td><a href="reportes-lista.php?falta=<?=base64_encode($resultado['dfal_id']);?>&filtros=<?=base64_encode(1);?>"><?=$resultado['dfal_nombre'];?></a></td>
														<td><?=UsuariosPadre::nombreCompletoDelUsuario($resultado);?></td>
														<td>
															<?php if($resultado['dr_aprobacion_estudiante']==0){ echo "-"; }else{?>
																<i class="fa fa-check-circle" title="<?=$resultado['dr_aprobacion_estudiante_fecha'];?>"></i>
															<?php }?>
														</td>
														<td>
															<?php if($resultado['dr_aprobacion_acudiente']==0){ echo "-"; }else{?>
																<i class="fa fa-check-circle" title="<?=$resultado['dr_aprobacion_acudiente_fecha'];?>"></i>
															<?php }?>
														</td>
														<td>
															<?php if(!empty($resultado['dr_comentario'])){?>
																<i class="fa fa-eye" title="<?=$resultado['dr_comentario'];?>"></i>
															<?php }?>
														</td>
														<?php if(Modulos::validarPermisoEdicion()){?>
															<td>
															<?php if(Modulos::validarSubRol(['DT0025', 'DT0056', 'DT0055', 'DT0054', 'DT0026'])) {?>
																<?php
																	$arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
																	$arrayDatos = json_encode($arrayEnviar);
																	$objetoEnviar = htmlentities($arrayDatos);
																	?>
																
																	<div class="btn-group">
																		<button type="button" class="btn btn-primary">Acciones</button>
																		<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																			<i class="fa fa-angle-down"></i>
																		</button>
																		<ul class="dropdown-menu" role="menu">
																		<?php if( Modulos::validarSubRol(['DT0025']) ){?>
																			<li><a href="reportes-firmar-estudiante.php?idR=<?=base64_encode($resultado['dr_id']);?>">Firmar por el estudiante</a></li>
																		<?php }?>

																		<?php if( Modulos::validarSubRol(['DT0056']) ){?>
																			<li><a href="reportes-firmar-acudiente.php?idR=<?=base64_encode($resultado['dr_id']);?>">Firmar por el acudiente</a></li>
																		<?php }?>

																		<?php if( Modulos::validarSubRol(['DT0055']) ){?>
																			<li><a href="reportes-firma-quitar-estudiante.php?idR=<?=base64_encode($resultado['dr_id']);?>">Quitar firma estudiante</a></li>
																		<?php }?>

																		<?php if( Modulos::validarSubRol(['DT0054']) ){?>
																			<li><a href="reportes-firmar-quitar-acudiente.php?idR=<?=base64_encode($resultado['dr_id']);?>">Quitar firma acudiente</a></li>
																		<?php }?>
																			
																			<?php if( ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) && Modulos::validarSubRol(['DT0026'])){?>

																				<li><a href="#" title="<?=$objetoEnviar;?>" id="<?=$resultado['dr_id'];?>" name="reportes-eliminar.php?idR=<?=base64_encode($resultado['dr_id']);?>" onClick="deseaEliminar(this)">Eliminar</a></li>
																				
																			<?php }?>

																		</ul>
																	</div>
																<?php }?>
															</td>
														<?php }?>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
													  ?>
                                                </tbody>
                                            </table>
											<?php 
                            				$botones = new botonesGuardar("estudiantes.php", false); ?>
                                            </div>
                                        </div>
                                    </div>
                      				<?php include("../directivo/enlaces-paginacion.php");?>
                                </div>
								
							
                            </div>
                        </div>
                    </div>
                </div>