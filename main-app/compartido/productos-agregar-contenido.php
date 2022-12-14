					<div class="row">
                        <div class="col-sm-9">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header>Registra tu producto o servicio</header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
										<input type="hidden" name="id" value="17">
                                        
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Titulo del producto (*)</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="nombre" class="form-control" required placeholder="Ejemplo: Tapaboca N95">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Descripción del producto (*)</label>
                                            <div class="col-sm-10">
                                                <textarea name="descripcion" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Precio del producto (*)</label>
                                            <div class="col-sm-4">
                                                <input type="number" name="precio" class="form-control" required placeholder="Ejemplo: 10000">
												<span style="color: navy;">Solamente digite el número sin puntos ni simbolos.</span><br>
												<span style="color: tomato;">Si desea recibir el pago en línea, el valor mínimo debe ser de $10.000 COP.</span>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Foto del producto</label>
                                            <div class="col-sm-6">
                                                <input type="file" name="imagen" class="form-control">
                                            </div>
                                        </div>

										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">URL video de youtube</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="video" class="form-control" placeholder="Ejemplo: https://www.youtube.com/watch?v=g2LSYPm7hR4">
                                            </div>
                                        </div>

											
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Categoría del producto (*)</label>
                                            <div class="col-sm-10">
                                                <?php
												$datosConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosMarketPlace.".categorias_productos");
												?>
                                                <select class="form-control  select2" name="categoria" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
													?>
                                                    	<option value="<?=$datos[0];?>"><?=$datos['catp_nombre']?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
	
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Palabras claves relacionadas al producto</label>
											<div class="col-sm-10">
												<input type="text" name="keyw" class="tags tags-input" data-type="tags" />
												<span style="color: navy;">Con estas palabras claves relacionadas pueden encontrar más fácil esta publicación.</span>
											</div>
                                        </div>
										
										
											

										
										<input type="submit" class="btn btn-primary" value="Registrar producto">&nbsp;
										
										<a href="#" name="marketplace.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>

                                    </form>
                                </div>
                            </div>
                        </div>
						
                        <div class="col-sm-3">
                            <div class="panel">
								<header class="panel-heading panel-heading-yellow">¿REQUIERES AYUDA?</header>

									<p><a href="https://youtu.be/cmsQDO9tIrQ?t=122" target="_blank">Ver tutorial de uso de MarketPlace</span></a></p>
									<p><a href="mensajes-redactar.php?para=1&asunto=REQUIERO ASESORÍA PARA USAR SINTIA MARKETPLACE">Solicitar asesoría</span></a></p>

									<p>
										<b>AYUDA SINTIA MARKETPLACE</b><br>
										<b>WhatsApp:</b> 300 60750800<br>
										<b>EMail:</b> company@plataformasintia.com<br>
									</p>
								
								</div>
							</div>	
                        </div>
						
                    </div>