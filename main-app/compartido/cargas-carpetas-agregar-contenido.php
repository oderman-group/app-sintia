<div class="row">
						
						<div class="col-sm-3">
						<?php 
							//DOCENTES
							if($datosUsuarioActual[3]==2){?>
							<?php include("info-carga-actual.php");?>
						<?php }?>
							
							<?php include("../compartido/publicidad-lateral.php");?>
                        </div>
						
                        <div class="col-sm-6">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="../compartido/guardar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" method="post" enctype="multipart/form-data">
										<input type="hidden" value="3" name="id">
										<input type="hidden" value="<?=$cargaConsultaActual;?>" name="idRecursoP">
										<input type="hidden" value="2" name="idCategoria">

											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[53][$datosUsuarioActual[8]];?></label>
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
												<label class="col-sm-3 control-label"><?=$frases[318][$datosUsuarioActual[8]];?></label>
												<div class="col-sm-9">
													<input type="text" name="nombre" class="form-control" autocomplete="off" required>
												</div>
											</div>
											</div>
											
										
											<div id="archivo" style="display: none;">
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[128][$datosUsuarioActual[8]];?></label>
												<div class="col-sm-9">
													<input type="file" name="archivo" class="form-control">
												</div>
											</div>
											</div>
										
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[229][$datosUsuarioActual[8]];?></label>
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
															<option value="<?=$datos[0];?>" <?php if($datos[0]==$idFolderCarpetaActual){echo "selected";}?>><?=$datos['fold_nombre']?></option>
														<?php }?>
													</select>
												</div>
											</div>
										
											<div class="form-group row">
												<label class="col-sm-3 control-label"><?=$frases[227][$datosUsuarioActual[8]];?></label>
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
												<label class="col-sm-3 control-label"><?=$frases[228][$datosUsuarioActual[8]];?></label>
												<div class="col-sm-9">
													<input type="text" name="keyw" class="tags tags-input" data-type="tags" />
												</div>
											</div>
										


										<input type="submit" class="btn btn-primary" value="<?=$frases[41][$datosUsuarioActual[8]];?>">&nbsp;
										
										<a href="javascript:history.go(-1);" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i><?=$frases[184][$datosUsuarioActual[8]];?></a>
                                    </form>
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-3">

						<?php include("../compartido/publicidad-lateral.php");?>

                        </div>
						
                    </div>