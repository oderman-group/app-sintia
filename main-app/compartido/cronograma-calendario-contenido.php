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
                    <?php 
                    // Inicializar variables si no están definidas
                    if(!isset($totalActividades)) $totalActividades = 0;
                    if(!isset($totalPendientes)) $totalPendientes = 0;
                    if(!isset($totalHoy)) $totalHoy = 0;
                    ?>
                    <?php if($datosUsuarioActual['uss_tipo']==TIPO_ESTUDIANTE){?>
                    <!-- Tarjetas de resumen de actividades -->
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-4">
                            <div class="card-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                <div class="card-body" style="padding: 20px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div>
                                            <h3 style="margin: 0; font-size: 32px; font-weight: 700;"><?= $totalActividades; ?></h3>
                                            <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">Total de Actividades</p>
                                        </div>
                                        <div style="font-size: 48px; opacity: 0.3;">
                                            <i class="fa fa-calendar-check"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);">
                                <div class="card-body" style="padding: 20px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div>
                                            <h3 style="margin: 0; font-size: 32px; font-weight: 700;"><?= $totalPendientes; ?></h3>
                                            <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">Actividades Pendientes</p>
                                        </div>
                                        <div style="font-size: 48px; opacity: 0.3;">
                                            <i class="fa fa-clock"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);">
                                <div class="card-body" style="padding: 20px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div>
                                            <h3 style="margin: 0; font-size: 32px; font-weight: 700;"><?= $totalHoy; ?></h3>
                                            <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">Actividades de Hoy</p>
                                        </div>
                                        <div style="font-size: 48px; opacity: 0.3;">
                                            <i class="fa fa-calendar-day"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }?>
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