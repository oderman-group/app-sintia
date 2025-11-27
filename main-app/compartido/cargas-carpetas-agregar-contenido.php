<?php 
					if($datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE){
						include("includes/barra-superior-informacion-actual.php"); 
					}
					?>
<div class="row">
						
						
                        <div class="col-sm-12">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="../compartido/carpetas/cargas-carpetas-guardar.php" method="post" enctype="multipart/form-data">
										<input type="hidden" value="<?=$cargaConsultaActual;?>" name="idRecursoP">
										<input type="hidden" value="2" name="idCategoria">

											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[53][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<select class="form-control  select2" name="tipo" required onChange="tipoFolder(this)">
														<option value="">Seleccione una opción</option>
														<option value="1" selected>Carpeta</option>
														<option value="2">Archivo</option>
													</select>
												</div>
											</div>
											
											<div id="nombreCarpeta">
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[318][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<input type="text" name="nombre" class="form-control" autocomplete="off" required>
												</div>
											</div>
											</div>
											
										
											<div id="archivo" style="display: none;">
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[128][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<input type="file" name="archivo" class="form-control">
												</div>
											</div>
											</div>
										
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[229][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<?php
													$consulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_folders 
													WHERE fold_id_recurso_principal='".$cargaConsultaActual."' AND fold_propietario='".$_SESSION["id"]."' AND fold_activo=1 AND fold_categoria=2 AND fold_tipo=1 AND fold_estado=1 AND fold_year='" . $_SESSION["bd"] . "'
													ORDER BY fold_tipo, fold_nombre");
													?>
													<select class="form-control  select2" name="padre" required>
														<option value="0">--Raiz--</option>
														<?php
														while($datos = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$datos['fold_id'];?>" <?php if($datos['fold_id']==$idFolderCarpetaActual){echo "selected";}?>><?=$datos['fold_nombre']?></option>
														<?php }?>
													</select>
												</div>
											</div>
										
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[227][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-9">
													<select id="select_usuario" class="form-control select2-multiple" multiple name="compartirCon[]">
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
													<input type="text" name="keyw" class="tags tags-input" data-type="tags" />
												</div>
											</div>
										


											<a href="javascript:history.go(-1);" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i><?=$frases[184][$datosUsuarioActual['uss_idioma']];?></a>
											<button type="submit" class="btn  btn-info">
												<i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
											</button>
										
										
                                    </form>
                                </div>
                            </div>
                        </div>
						
                    </div>