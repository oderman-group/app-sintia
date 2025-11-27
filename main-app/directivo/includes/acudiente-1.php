											<?php 
											if(!empty($datosEstudianteActual["mat_acudiente"])){
												$acudiente = UsuariosPadre::sesionUsuario($datosEstudianteActual["mat_acudiente"]);
											}
											?>
                                            
											<h2><b>ACUDIENTE 1</b></h2>
											<input type="hidden" name="usuarioAcudiente" value="<?php if(isset($acudiente['uss_usuario'])){ echo $acudiente['uss_usuario'];}?>">

											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo de documento</label>
												<div class="col-sm-3">
													<?php $tiposDocumento = $opcionesGeneralesPorGrupo[1] ?? []; ?>
													<select class="form-control" name="tipoDAcudiente" <?=$disabledPermiso;?>>
														<?php foreach($tiposDocumento as $opcion){
															$selected = (isset($acudiente["uss_tipo_documento"]) && $opcion['ogen_id']==$acudiente["uss_tipo_documento"]) ? 'selected' : '';
															echo '<option value="'.$opcion['ogen_id'].'" '.$selected.'>'.$opcion['ogen_nombre'].'</option>';
														}?>
													</select>
												</div>
												
												<label class="col-sm-2 control-label">Documento <span style="color: red;">(*)</span></label>
												<div class="col-sm-3">
													<input type="text" name="documentoA" required class="form-control" autocomplete="off" value="<?php if(isset($acudiente['uss_documento'])){ echo $acudiente['uss_documento'];}?>" <?=$disabledPermiso;?>>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de expedición</label>
												<div class="col-sm-3">
													<select class="form-control" name="lugardA" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach(($catalogoCiudades ?? []) as $ciudad){ ?>
														<option value="<?=$ciudad['ciu_id'];?>" <?php if(isset($acudiente["uss_lugar_expedicion"])&&$ciudad['ciu_id']==$acudiente["uss_lugar_expedicion"]){echo "selected";}?>><?=$ciudad['ciu_nombre'].", ".$ciudad['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>	

												<label class="col-sm-2 control-label">Ocupaci&oacute;n</label>
												<div class="col-sm-3">
													<input type="text" name="ocupacionA" class="form-control" autocomplete="off" value="<?php if(isset($acudiente["uss_ocupacion"])){ echo $acudiente["uss_ocupacion"];}?>" <?=$disabledPermiso;?>>
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Primer Apellido</label>
												<div class="col-sm-3">
													<input type="text" name="apellido1A" class="form-control" autocomplete="off" value="<?php if(isset($acudiente["uss_apellido1"])){ echo $acudiente["uss_apellido1"];}?>" <?=$disabledPermiso;?>>
												</div>
																							
												<label class="col-sm-2 control-label">Segundo Apellido</label>
												<div class="col-sm-3">
													<input type="text" name="apellido2A" class="form-control" autocomplete="off" value="<?php if(isset($acudiente["uss_apellido2"])){ echo $acudiente["uss_apellido2"];}?>" <?=$disabledPermiso;?>>
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Nombre <span style="color: red;">(*)</span></label>
												<div class="col-sm-3">
													<input type="text" name="nombreA" required class="form-control" autocomplete="off" value="<?php if(isset($acudiente["uss_nombre"])){ echo $acudiente["uss_nombre"];}?>" <?=$disabledPermiso;?>>
												</div>
																								
												<label class="col-sm-2 control-label">Otro Nombre</label>
												<div class="col-sm-3">
													<input type="text" name="nombre2A" class="form-control" autocomplete="off" value="<?php if(isset($acudiente["uss_nombre2"])){ echo $acudiente["uss_nombre2"];}?>" <?=$disabledPermiso;?>>
												</div>
											</div>	
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Genero</label>
												<div class="col-sm-3">
													<?php $opcionesGenero = $opcionesGeneralesPorGrupo[4] ?? []; ?>
													<select class="form-control" name="generoA" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach($opcionesGenero as $opcion){
															$selected = (isset($acudiente['uss_genero']) && $opcion['ogen_id']==$acudiente['uss_genero']) ? 'selected' : '';
															echo '<option value="'.$opcion['ogen_id'].'" '.$selected.'>'.$opcion['ogen_nombre'].'</option>';
														}?>
													</select>
												</div>
											</div>