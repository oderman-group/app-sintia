<?php require_once("../class/Estudiantes.php");

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}?>

					<div class="row">
                        <div class="col-sm-12">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header><?=$frases[96][$datosUsuarioActual['uss_idioma']];?></header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/reportes-guardar.php" method="post" enctype="multipart/form-data">

										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">  
                                                <input type="date" class="form-control" name="fecha" required value="<?=date("Y-m-d");?>" <?=$disabledPermiso;?>>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[55][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <?php
												$datosConsulta = Estudiantes::listarEstudiantesParaDocentes('');
												?>
                                                <select class="form-control  select2" name="estudiante" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
													?>
                                                    	<option value="<?=$datos['mat_id'];?>"><?="[".$datos['mat_id']."] ".Estudiantes::NombreCompletoDelEstudiante($datos);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[248][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <select id="multiple" name="faltas[]" class="form-control select2-multiple" multiple <?=$disabledPermiso;?> required>
												<?php
												$datosConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disciplina_faltas 
												INNER JOIN ".BD_DISCIPLINA.".disciplina_categorias ON dcat_id=dfal_id_categoria AND dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$_SESSION["bd"]}
												WHERE dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]}
												");
												while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
												?>	
                                                  <option value="<?=$datos['dfal_id'];?>"><?=$datos['dfal_codigo'].". ".$datos['dfal_nombre'];?></option>	
												<?php }?>	
                                                </select>
                                            </div>
                                        </div>
										
										<?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV){?>
											<div class="form-group row">
												<label class="col-sm-2 control-label"><?=$frases[75][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-10">
													<?php
													$datosConsulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND (uss_tipo = ".TIPO_DOCENTE." OR uss_tipo= ".TIPO_DIRECTIVO.")
													ORDER BY uss_tipo, uss_nombre");
													?>
													<select class="form-control  select2" name="usuario" required <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
														while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$datos['uss_id'];?>"><?=UsuariosPadre::nombreCompletoDelUsuario($datos);?></option>
														<?php }?>
													</select>
												</div>
											</div>
										<?php }else{?>
											<input type="hidden" name="usuario" value="<?=$_SESSION["id"];?>">
										<?php }?>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="contenido" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?>></textarea>
                                            </div>
                                        </div>
										
										<a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>

										<?php if(Modulos::validarPermisoEdicion()){?>
											<button type="submit" class="btn  btn-info">
												<i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
											</button>
										<?php }?>

                                    </form>
                                </div>
                            </div>
                        </div>
						
                        <div class="col-sm-3">
                            <?php include("../compartido/publicidad-lateral.php");?>
                        </div>
						
                    </div>