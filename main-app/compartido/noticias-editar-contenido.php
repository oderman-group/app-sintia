					<div class="row">
                        <div class="col-sm-9">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header><?=$frases[217][$datosUsuarioActual[8]];?></header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
										<input type="hidden" name="id" value="4">
										<input type="hidden" name="idR" value="<?=$_GET["idR"];?>">
                                        
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[127][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="titulo" class="form-control" value="<?=$datosConsulta['not_titulo'];?>">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[50][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">
                                                <textarea name="contenido" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"><?=$datosConsulta['not_descripcion'];?></textarea>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[211][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-6">
                                                <input type="file" name="imagen" class="form-control">
                                            </div>
											<?php if($datosConsulta[7]!=""){?>
												<div class="item col-sm-4">
													<img src="../files/publicaciones/<?=$datosConsulta[7];?>" alt="<?=$datosConsulta['not_titulo'];?>" width="50">
													<a href="#" name="../compartido/guardar.php?get=11&idR=<?=$datosConsulta['not_id'];?>" onClick="deseaEliminar(this)"><i class="fa fa-trash"></i></a>
												</div>
												<p>&nbsp;</p>
											<?php }?>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[213][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="urlImagen" class="form-control" value="<?=$datosConsulta['not_url_imagen'];?>">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[214][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="video" class="form-control" value="<?=$datosConsulta['not_video_url'];?>">
                                            </div>
											<?php if($datosConsulta['not_video']!=""){?>
													<div class="col-sm-4">
														<iframe width="100" height="80" src="https://www.youtube.com/embed/<?=$datosConsulta['not_video'];?>?rel=0&amp;" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen volume="0"></iframe>
													</div>
											<?php }?>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[224][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">
                                                <?php
												$datosConsultaBD = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_categorias
												WHERE gcat_activa=1
												");
												?>
                                                <select class="form-control  select2" name="categoriaGeneral" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($datosBD = mysqli_fetch_array($datosConsultaBD, MYSQLI_BOTH)){
													?>
                                                    	<option value="<?=$datosBD['gcat_id'];?>" <?php if($datosBD['gcat_id']==$datosConsulta['not_id_categoria_general'])echo "selected";?>><?=$datosBD['gcat_nombre']?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>

											
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Palabras claves</label>
											<div class="col-sm-10">
												<input type="text" name="keyw" class="tags tags-input" data-type="tags" value="<?=$datosConsulta['not_keywords'];?>" />
											</div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[128][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-6">
                                                <input type="file" name="archivo" class="form-control">
                                            </div>
											<?php if($datosConsulta['not_archivo']!=""){?>
												<div class="col-sm-4">
													<a href="../files/publicaciones/<?=$datosConsulta['not_archivo'];?>" target="_blank"><i class="fa fa-download"></i> Descargar Archivo</a>
											</div>
												<p>&nbsp;</p>
											<?php }?>
                                        </div>
										<h4 align="center" style="font-weight: bold;">FILTROS</h4>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[75][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">
                                                <select id="multiple" class="form-control select2-multiple" multiple>
                                                  <option value="5">Directivos</option>
                                                  <option value="2">Docentes</option>
												  <option value="3">Acudientes</option>
												  <option value="4">Estudiantes</option>	
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
												<label class="col-sm-2 control-label"><?=$frases[5][$datosUsuarioActual[8]];?></label>
												<div class="col-sm-10">
													<select id="multiple" class="form-control select2-multiple" multiple name="cursos[]">
													<?php
													$infoConsulta = mysqli_query($conexion, "SELECT * FROM academico_grados");
													while($infoDatos = mysqli_fetch_array($infoConsulta, MYSQLI_BOTH)){
														$existe = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM social_noticias_cursos WHERE notpc_noticia='".$_GET["idR"]."' AND notpc_curso='".$infoDatos['gra_id']."'"));
														
													?>	
													  <option value="<?=$infoDatos['gra_id'];?>" <?php if($existe>0){echo "selected";}?>><?=strtoupper($infoDatos['gra_nombre']);?></option>
													<?php }?>	
													</select>
												</div>
											</div>
										
										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
										
										<a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>

                                    </form>
                                </div>
                            </div>
                        </div>
						
                        <div class="col-sm-3">
                            <?php include("../compartido/publicidad-lateral.php");?>
                        </div>
						
                    </div>