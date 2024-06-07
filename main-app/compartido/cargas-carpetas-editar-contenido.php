<div class="row">
						
						<div class="col-sm-3">

						<?php 
							//DOCENTES
							if($datosUsuarioActual['uss_tipo']==TIPO_DOCENTE){?>
							<?php include("info-carga-actual.php");?>
						<?php }?>
							
							<?php include("../compartido/publicidad-lateral.php");?>

                        </div>
						
                        <div class="col-sm-6">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="../compartido/cargas-carpetas-actualizar.php" method="post" enctype="multipart/form-data">
										<input type="hidden" value="<?=$cargaConsultaActual;?>" name="idRecursoP">
										<input type="hidden" value="2" name="idCategoria">
										<input type="hidden" value="<?=$idR;?>" name="idR">

											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[53][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<select class="form-control  select2" name="tipo" required onChange="tipoFolder(this)">
														<option value="">Seleccione una opción</option>
														<option value="1" <?php if($datosConsulta['fold_tipo']==1){echo "selected";}?>>Carpeta</option>
														<option value="2" <?php if($datosConsulta['fold_tipo']==2){echo "selected";}?>>Archivo</option>
													</select>
												</div>
											</div>
										
											
											<?php if($datosConsulta['fold_tipo']==2){?>
											
											<div id="nombreCarpeta" style="display: none;">
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[318][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<input type="text" name="nombre" class="form-control" value="<?=$datosConsulta['fold_nombre'];?>" autocomplete="off">
												</div>
											</div>
											</div>
										
											<div id="archivo">
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[128][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-6">
													<input type="file" name="archivo" class="form-control">
												</div>
												
												<div class="col-sm-3">
													<a href="../files/archivos/<?=$datosConsulta['fold_nombre'];?>" target="_blank"><i class="fa fa-download"></i> Descargar Archivo</a>
												</div>
												<p>&nbsp;</p>
											</div>
											</div>	

											<?php }else{?>
										
											<div id="nombreCarpeta">
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[318][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<input type="text" name="nombre" class="form-control" autocomplete="off" value="<?=$datosConsulta['fold_nombre'];?>" required>
												</div>
											</div>
											</div>
										
											<div id="archivo" style="display: none;">
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[128][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-6">
													<input type="file" name="archivo" class="form-control">
												</div>
											</div>
											</div>
											<?php }?>
											
										
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[229][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<?php
													$consulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_folders 
													WHERE fold_id_recurso_principal='".$cargaConsultaActual."' AND fold_propietario='".$_SESSION["id"]."' AND fold_activo=1 AND fold_categoria=2 AND fold_tipo=1 AND fold_estado=1 AND fold_year='" . $_SESSION["bd"] . "' AND fold_id!='".$idR."'
													ORDER BY fold_tipo, fold_nombre");
													?>
													<select class="form-control  select2" name="padre" required>
														<option value="0">--Raiz--</option>
														<?php
														while($datos = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$datos['fold_id'];?>" <?php if($datos['fold_id']==$datosConsulta['fold_padre']){echo "selected";}?>><?=$datos['fold_nombre']?></option>
														<?php }?>
													</select>
												</div>
											</div>
										
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[227][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<select id="select_usuario" class="form-control select2-multiple" multiple name="compartirCon[]">
														<?php
														$infoConsulta = mysqli_query($conexion, "SELECT fxuc_usuario FROM ".$baseDatosServicios.".general_folders_usuarios_compartir WHERE fxuc_folder='".$idR."' AND fxuc_institucion='".$config['conf_id_institucion']."' AND fxuc_year='".$config['conf_agno']."'");
														while($infoDatos = mysqli_fetch_array($infoConsulta, MYSQLI_BOTH)){

															$consultaExiste = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$infoDatos['fxuc_usuario']."'");
															$existe = mysqli_fetch_array($consultaExiste, MYSQLI_BOTH);

															if(!is_null($existe)){
																$nombre = UsuariosPadre::nombreCompletoDelUsuario($existe);
														?>	
														<option value="<?=$existe['uss_id'];?>" selected><?=$nombre." - ".$existe['pes_nombre'];?></option>
														<?php }}?>	
													</select>
												</div>
											</div>
											<script>          
												$(document).ready(function() {
													$('#select_usuario').select2({
													placeholder: 'Seleccione el usuario...',
													theme: "bootstrap",
													multiple: true,
														ajax: {
															type: 'GET',
															url: '../compartido/ajax-listar-usuarios.php',
															processResults: function(data) {
																data = JSON.parse(data);
																return {
																	results: $.map(data, function(item) {                                  
																		return {
																			id: item.value,
																			text: item.label
																		}
																	})
																};
															}
														}
													});
												});
											</script>
										
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[228][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<input type="text" name="keyw" class="tags tags-input" data-type="tags" value="<?=$datosConsulta['fold_keywords'];?>" />
												</div>
											</div>
										

										<a href="javascript:history.go(-1);" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i><?=$frases[184][$datosUsuarioActual['uss_idioma']];?></a>
										<input type="submit" class="btn btn-primary" value="<?=$frases[41][$datosUsuarioActual['uss_idioma']];?>">&nbsp;
										
										
                                    </form>
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-3">

						<?php include("../compartido/publicidad-lateral.php");?>

                        </div>
						
                    </div>