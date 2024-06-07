<?php require_once("../class/servicios/CargaServicios.php"); ?>
<?php require_once("../class/servicios/MediaTecnicaServicios.php"); ?>
<?php require_once("../class/servicios/GradoServicios.php"); ?>
<?php require_once("../class/CargaAcademica.php"); ?>
<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$cCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosEstudianteActual['mat_grado'], $datosEstudianteActual['mat_grupo']);
											$nCargas = mysqli_num_rows($cCargas);
											while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
												//Verificar si el estudiante está matriculado en cursos de extensión o complementarios
												if($rCargas['car_curso_extension']==1){
													$cursoExt = CargaAcademica::validarCursosComplementario($conexion, $config, $datosEstudianteActual['mat_id'], $rCargas['car_id']);
													if($cursoExt==0){continue;}
												}
												
												if($rCargas['car_id']==$cargaConsultaActual) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" <?=$estiloResaltado;?>><?=strtoupper($rCargas['mat_nombre']);?></a></p>
											<?php }?>
										</div>
                                    </div>
									<?php if (array_key_exists(10, $arregloModulos)) { 
										$parametros = [
											'matcur_id_matricula' 	=> $datosEstudianteActual["mat_id"],
											'matcur_id_institucion' => $config['conf_id_institucion'],
											'matcur_years' 			=> $config['conf_agno']
										];
										$listaCursosMediaTecnica = MediaTecnicaServicios::listar($parametros);
										if(!empty($listaCursosMediaTecnica)){ 
										foreach ($listaCursosMediaTecnica as $dato) {
											$cursoMediaTecnica = GradoServicios::consultarCurso($dato["matcur_id_curso"]);

										?>
										
										<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$cursoMediaTecnica['gra_nombre'];?> </header>
											<div class="panel-body">
												<?php $parametros = [
													'matcur_id_matricula' => $datosEstudianteActual["mat_id"],
													'matcur_id_curso'     => $dato["matcur_id_curso"],
													'matcur_id_institucion' => $config['conf_id_institucion'],
													'matcur_years' 			=> $config['conf_agno']
													];
												$listacargaMediaTecnica = MediaTecnicaServicios::listarMaterias($parametros);
												if ($listacargaMediaTecnica != null) { 
													foreach ($listacargaMediaTecnica as $cargaMediaTecnica) {
													 if($cargaMediaTecnica['car_id']==$cargaConsultaActual) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';?>
													<p><a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=$cargaMediaTecnica['car_id'];?>&periodo=<?=$periodoConsultaActual;?>" <?=$estiloResaltado;?>><?=strtoupper($cargaMediaTecnica['mat_nombre']);?></a></p>
													<?php }?>													
												<?php } else {?>
													<p> El curso <?=$cursoMediaTecnica["gra_nombre"]?>  no tiene carga academica.</p>
												<?php }?>
											
											</div>
										</div>
										<?php }}?>
                                    
									<?php } ?>