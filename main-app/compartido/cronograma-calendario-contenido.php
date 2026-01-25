<div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[245][$datosUsuarioActual['uss_idioma']];?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
								<?php if($datosUsuarioActual['uss_tipo']==TIPO_ESTUDIANTE){?>
                                	<li class="active"><?=$frases[245][$datosUsuarioActual['uss_idioma']];?></li>
								<?php }?>
								
								<?php if($datosUsuarioActual['uss_tipo']==TIPO_ACUDIENTE){?>
                                	<li><a class="parent-item" href="notas-actuales.php?usrEstud=<?=base64_encode($_GET["usrEstud"]);?>">Defintivas actuales</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                	<li class="active"><?=$frases[111][$datosUsuarioActual['uss_idioma']];?></li>
								<?php }?>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-md-12">
							<?php if($datosUsuarioActual['uss_tipo']==TIPO_ESTUDIANTE){?>
							<!-- Filtro de carga -->
							<div class="card-box" style="margin-bottom: 20px;">
								<div class="card-body">
									<div class="form-group row">
										<label class="col-sm-2 control-label" style="padding-top: 8px;">
											<i class="fa fa-filter"></i> Filtrar por materia:
										</label>
										<div class="col-sm-4">
											<select id="filtroCarga" class="form-control" onchange="filtrarCalendario()">
												<option value="">Todas las materias</option>
												<?php
												require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
												$idGrado = !empty($datosEstudianteActual['mat_grado']) ? (string)$datosEstudianteActual['mat_grado'] : '';
												$idGrupo = !empty($datosEstudianteActual['mat_grupo']) ? (string)$datosEstudianteActual['mat_grupo'] : '';
												$cCargasFiltro = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $idGrado, $idGrupo);
												$cargaFiltroActual = !empty($_GET['filtro_carga']) ? base64_decode($_GET['filtro_carga']) : '';
												while($cargaFiltro = mysqli_fetch_array($cCargasFiltro, MYSQLI_BOTH)){
													if($cargaFiltro['car_curso_extension']==1){
														$cursoExt = CargaAcademica::validarCursosComplementario($conexion, $config, $datosEstudianteActual['mat_id'], $cargaFiltro['car_id']);
														if($cursoExt==0){continue;}
													}
													$selected = ($cargaFiltro['car_id'] == $cargaFiltroActual) ? 'selected' : '';
													echo '<option value="'.base64_encode($cargaFiltro['car_id']).'" '.$selected.'>'.$cargaFiltro['mat_nombre'].'</option>';
												}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<?php }?>
							
                             <div class="card-box">
                                 <div class="card-head">
                                     <header><?=$frases[245][$datosUsuarioActual['uss_idioma']];?></header>
                                 </div>
								 
								 
                                 <div class="card-body">
                                 	<div class="panel-body">
                                       <div id="calendar" class="has-toolbar"> </div>
                                    </div>
                                 </div>
                             </div>
                         </div>
                    </div>
					
					<script>
					function filtrarCalendario(){
						var cargaSeleccionada = document.getElementById('filtroCarga').value;
						var url = '<?php echo $_SERVER['PHP_SELF']; ?>';
						var params = [];
						
						// Preservar usrEstud si existe (para acudientes)
						<?php if(!empty($_GET['usrEstud'])){ ?>
						params.push('usrEstud=<?php echo urlencode($_GET['usrEstud']); ?>');
						<?php } ?>
						
						// Agregar filtro de carga si está seleccionado
						if(cargaSeleccionada){
							params.push('filtro_carga=' + encodeURIComponent(cargaSeleccionada));
						}
						
						if(params.length > 0){
							url += '?' + params.join('&');
						}
						
						window.location.href = url;
					}
					</script>
                </div>