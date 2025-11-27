<fieldset>
											
										<?php if(!empty($datosEstudianteActual['mat_foto'])) {?>
										<div class="form-group row">
                                            <div class="col-sm-4" style="margin: 0 auto 10px">
												<div class="item">
													<img src="../files/fotos/<?=$datosEstudianteActual['mat_foto'];?>" width="150" height="150" />
												</div>
                                            </div>
                                        </div>
										<?php }?>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[219][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <input type="file" name="fotoMat" class="form-control" accept=".png, .jpg, .jpeg" <?=$disabledPermiso;?> onChange="validarPesoArchivo(this)">
                                                <span style="color: #6017dc;">La foto debe estar en formato JPG, JPEG o PNG.</span>
                                            </div>
                                        </div>
										<hr>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Código del Sistema</label>
												<div class="col-sm-2">
													<input type="text" name="matricula" class="form-control" readonly autocomplete="off" value="<?=$datosEstudianteActual['mat_matricula'];?>"  <?=$disabledPermiso;?>>
												</div>
												
												<label class="col-sm-2 control-label">Fecha de Matr&iacute;cula</label>
												<div class="col-sm-2">
													<input type="text" name="fechaMatricula" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_fecha'];?>" disabled>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Número de matrícula</label>
												<div class="col-sm-4">
													<input type="text" name="NumMatricula" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual["mat_numero_matricula"];?>" <?=$disabledPermiso;?>>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo de documento</label>
												<div class="col-sm-2">
													<?php $tiposDocumento = $opcionesGeneralesPorGrupo[1] ?? []; ?>
													<select class="form-control  select2" name="tipoD" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach($tiposDocumento as $opcion){
															$selected = ($opcion['ogen_id']==$datosEstudianteActual['mat_tipo_documento']) ? 'selected' : '';
															echo '<option value="'.$opcion['ogen_id'].'" '.$selected.'>'.$opcion['ogen_nombre'].'</option>';
														}?>
													</select>
												</div>
												
												<label class="col-sm-2 control-label">Número de documento <span style="color: red;">(*)</span></label>
												<div class="col-sm-2">
													<input type="text" name="nDoc" id="nDoc" required class="form-control" autocomplete="off" onChange="validarEstudiante(this)" value="<?=$datosEstudianteActual['mat_documento'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>	
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de expedición</label>
												<div class="col-sm-4">
													<?php $listaCiudades = $catalogoCiudades ?? []; ?>
													<select class="form-control  select2" name="lugarD" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach($listaCiudades as $ciudad){ ?>
															<option value="<?=$ciudad['ciu_id'];?>" <?php if($ciudad['ciu_id']==$datosEstudianteActual["mat_lugar_expedicion"]){echo "selected";}?>><?=$ciudad['ciu_nombre'].", ".$ciudad['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Folio</label>
												<div class="col-sm-2">
													<input type="text" name="folio" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_folio'];?>" <?=$disabledPermiso;?>>
												</div>
												
												<label class="col-sm-2 control-label">Codigo Tesoreria</label>
												<div class="col-sm-2">
													<input type="text" name="codTesoreria" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_codigo_tesoreria'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Primer apellido <span style="color: red;">(*)</span></label>
												<div class="col-sm-2">
													<input type="text" name="apellido1" id="apellido1" required class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_primer_apellido'];?>" <?=$disabledPermiso;?>>
												</div>
												
												<label class="col-sm-2 control-label">Segundo apellido</label>
												<div class="col-sm-2">
													<input type="text" name="apellido2" id="apellido2" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_segundo_apellido'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Primer Nombre <span style="color: red;">(*)</span></label>
												<div class="col-sm-2">
													<input type="text" name="nombres" id="nombres" required class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_nombres'];?>" <?=$disabledPermiso;?>>
												</div>

												<label class="col-sm-2 control-label">Otro Nombre</label>
												<div class="col-sm-2">
													<input type="text" name="nombre2" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_nombre2'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Email</label>
												<div class="col-sm-6">
													<input type="text" name="email" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_email'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
											
											<div class="form-group row" id="fNacGroup">
												<label class="col-sm-2 control-label">Fecha de nacimiento</label>
												<div class="col-sm-4">
													<div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd" data-date-enddate="<?=date('Y-m-d', strtotime('-1 year'));?>">
													<input class="form-control" size="16" type="text" value="<?=$datosEstudianteActual['mat_fecha_nacimiento'];?>" <?=$disabledPermiso;?> readonly aria-describedby="fNacError" aria-invalid="false">
													<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
													</div>
													<small id="fNacError" class="text-danger" style="display:none;">La fecha de nacimiento no puede ser futura ni menor de 1 año.</small>
												</div>
												<input type="hidden" id="dtp_input1" name="fNac" value="<?=$datosEstudianteActual['mat_fecha_nacimiento'];?>">
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de Nacimiento</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="lNac" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach($listaCiudades as $ciudad){ ?>
															<option value="<?=$ciudad['ciu_id'];?>" <?php if($ciudad['ciu_id']==$datosEstudianteActual['mat_lugar_nacimiento']){echo "selected";}?>><?=$ciudad['ciu_nombre'].", ".$ciudad['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
												
												<?php 
													$lugarPro="";
													if(!is_numeric($datosEstudianteActual['mat_lugar_nacimiento'])){
														$lugarPro=$datosEstudianteActual['mat_lugar_nacimiento'];
													}
												?>
												<label class="col-sm-2 control-label">Ciudad de Procedencia</label>
												<div class="col-sm-4" >
													<input type="text" name="ciudadPro" class="form-control" autocomplete="off" value="<?=$lugarPro;?>" <?=$disabledPermiso;?>>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Genero</label>
												<?php $opcionesGenero = $opcionesGeneralesPorGrupo[4] ?? []; ?>
												<div class="col-sm-4">
													<select class="form-control  select2" name="genero" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach($opcionesGenero as $opcion){
															$selected = ($opcion['ogen_id']==$datosEstudianteActual['mat_genero']) ? 'selected' : '';
															echo '<option value="'.$opcion['ogen_id'].'" '.$selected.'>'.$opcion['ogen_nombre'].'</option>';
														}?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Grupo Sanguineo</label>
												<div class="col-sm-2">
													<input type="text" name="tipoSangre" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual["mat_tipo_sangre"];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">EPS</label>
												<div class="col-sm-2">
													<input type="text" name="eps" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual["mat_eps"];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estudiante de Inclusión</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="inclusion" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_inclusion']==1){echo "selected";}?>>Si</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_inclusion']==0){echo "selected";}?>>No</option>
													</select>
												</div>
												
												<label class="col-sm-2 control-label">Religi&oacute;n</label>
												<?php $opcionesReligion = $opcionesGeneralesPorGrupo[2] ?? []; ?>
												<div class="col-sm-2">
													<select class="form-control  select2" name="religion" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach($opcionesReligion as $opcion){
															$selected = ($opcion['ogen_id']==$datosEstudianteActual['mat_religion']) ? 'selected' : '';
															echo '<option value="'.$opcion['ogen_id'].'" '.$selected.'>'.$opcion['ogen_nombre'].'</option>';
														}?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Extranjero?</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="extran"  onChange="mostrar(this)" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_extranjero']==1){echo "selected";}?>>Si</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_extranjero']==0){echo "selected";}?>>No</option>
													</select>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Grupo étnico?</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="grupoEtnico" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
                                                        <option value="1" <?php if ($datosEstudianteActual['mat_etnia'] == 1) echo "selected"; ?>>Ninguno</option>
                                                        <option value="2" <?php if ($datosEstudianteActual['mat_etnia'] == 2) echo "selected"; ?>>Negro, mulato, afrocolombiano o afrodescendiente</option>
                                                        <option value="3" <?php if ($datosEstudianteActual['mat_etnia'] == 3) echo "selected"; ?>>Raizal del archipielago de San Andrés, providencia y Santa Catalina</option>
                                                        <option value="4" <?php if ($datosEstudianteActual['mat_etnia'] == 4) echo "selected"; ?>>Indigenas</option>
                                                        <option value="5" <?php if ($datosEstudianteActual['mat_etnia'] == 5) echo "selected"; ?>>Rom (Gitano)</option>
                                                        <option value="6" <?php if ($datosEstudianteActual['mat_etnia'] == 6) echo "selected"; ?>>Palenquero de San Basilio</option>
													</select>
												</div>
											</div>
											
											<?php
											$discapacidades = [
												1 => 'Ninguna',
												2 => 'Fisica',
												3 => 'Auditiva',
												4 => 'Visual',
												5 => 'Sordoceguera',
												6 => 'Intelectual/Cognitiva',
												7 => 'Psicosocial (mental)',
												8 => 'Multiple',
												9 => 'Autismo (transtorno del espectro autista - TEA) *',
												10 => 'Transtornos específicos de aprendizaje o del comportamiento',
												11 => 'Sordomudo *',
											];
											?>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Limitación o discapacidad</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="discapacidad" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach ($discapacidades as $id => $discapacidad) :?>
                                                            <option value="<?php echo $id;?>" <?php if ($datosEstudianteActual['mat_tiene_discapacidad'] == $id) echo "selected";?>><?php echo $discapacidad;?></option>
                                                        <?php endforeach;?>
													</select>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo de situación</label>
												<div class="col-sm-2">
													<select class="form-control" name="tipoSituacion" required <?=$disabledPermiso;?>>
                                                        <option value="1" <?php if ($datosEstudianteActual['mat_tipo_situacion'] == 1) echo "selected"; ?>>Ninguna</option>
                                                        <option value="2" <?php if ($datosEstudianteActual['mat_tipo_situacion'] == 2) echo "selected"; ?>>Desplazado, victima del conflicto</option>
                                                        <option value="3" <?php if ($datosEstudianteActual['mat_tipo_situacion'] == 3) echo "selected"; ?>>Desmovilizado del conflicto armado</option>
                                                    </select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Direcci&oacute;n</label>
												<div class="col-sm-4">
													<input type="text" name="direccion" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_direccion'];?>" <?=$disabledPermiso;?>>
												</div>
												<div class="col-sm-4">
													<input type="text" name="barrio" class="form-control" placeholder="Barrio" autocomplete="off" value="<?=$datosEstudianteActual['mat_barrio'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Ciudad de residencia</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="ciudadR" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach($listaCiudades as $ciudad){
															$codigo = trim($ciudad['ciu_codigo']);
															$selected = ($codigo == $datosEstudianteActual['mat_ciudad_residencia']) ? 'selected' : '';
														?>
														<option value="<?=$codigo;?>" <?=$selected;?>><?=$ciudad['ciu_nombre'].", ".$ciudad['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estrato</label>
												<?php $opcionesEstrato = $opcionesGeneralesPorGrupo[3] ?? []; ?>
												<div class="col-sm-2">
													<select class="form-control  select2" name="estrato" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach($opcionesEstrato as $opcion){
															$selected = ($opcion['ogen_id']==$datosEstudianteActual['mat_estrato']) ? 'selected' : '';
															echo '<option value="'.$opcion['ogen_id'].'" '.$selected.'>'.$opcion['ogen_nombre'].'</option>';
														}?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Contactos</label>
												<div class="col-sm-2">
													<input type="text" name="telefono" class="form-control" placeholder="Telefono" <?=$_SESSION['idInstitucion'] != ICOLVEN ? 'data-mask="999-9999"' : "";?> autocomplete="off" value="<?=$datosEstudianteActual['mat_telefono'];?>" <?=$disabledPermiso;?>>
												</div>
												<div class="col-sm-2">
													<input type="text" name="celular" class="form-control" placeholder="celular" <?=$_SESSION['idInstitucion'] != ICOLVEN ? 'data-mask="(999) 999-9999"' : "";?> autocomplete="off" value="<?=$datosEstudianteActual['mat_celular'];?>" <?=$disabledPermiso;?>>
												</div>
												<div class="col-sm-2">
													<input type="text" name="celular2" class="form-control" placeholder="celular #2" <?=$_SESSION['idInstitucion'] != ICOLVEN ? 'data-mask="(999) 999-9999"' : "";?> autocomplete="off" value="<?=$datosEstudianteActual['mat_celular2'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>	

											
											<?php include("includes/pasos-matricula.php");?>

										</fieldset>

