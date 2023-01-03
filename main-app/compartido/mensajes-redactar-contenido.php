<div class="inbox">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-topline-gray">
                                <div class="card-body no-padding height-9">
									<div class="row">
			                            <div class="col-md-3">
				                                <div class="inbox-sidebar">
				                                    <a href="mensajes-redactar.php" data-title="Compose" class="btn red compose-btn btn-block">
				                                        <i class="fa fa-edit"></i> Redactar </a>
				                                    <ul class="inbox-nav inbox-divider">
				                                        <li class="active"><a href="mensajes.php"><iclass="fa fa-inbox"></i> Recibidos</a></li>
				                                        <li><a href="mensajes.php?opt=2"><i class="fa fa-envelope"></i> Enviados</a></li>
				                                    </ul>
				                                </div>
				                            </div>
			                            <div class="col-md-9">
			                                <div class="inbox-body">
		                                    <div class="inbox-body no-pad">
		                                        <div class="mail-list">
		                                            <div class="compose-mail">
		                                                <form method="post" action="../compartido/guardar.php">
															<input type="hidden" name="id" value="7">
															<label>Para:</label>
		                                                    <div class="form-group">
																<select id="multiple" class="form-control select2-multiple" multiple name="para[]" required>
																	<option value="">Seleccione una opción</option>
																<?php
																$datosConsulta = mysqli_query($conexion, "SELECT * FROM usuarios 
																LEFT JOIN perfiles ON pes_id=uss_tipo
																LEFT JOIN academico_matriculas ON mat_id_usuario=uss_id
																LEFT JOIN academico_grados ON gra_id=mat_grado
																ORDER BY uss_tipo, mat_grado
																");
																while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
																	
																?>
																  <option value="<?=$datos['uss_id'];?>" <?php if(isset($_GET["para"])&&$datos['uss_id']==$_GET["para"]){echo "selected";}?>><?=strtoupper($datos['uss_nombre']." (".$datos['pes_nombre']." ".$datos['gra_nombre'].")");?></option>	
																<?php }?>
																</select>
		                                                        
		                                                    </div>
															<label>Asunto:</label>
		                                                    <div class="form-group">
		                                                        <input type="text" tabindex="1" class="form-control" name="asunto" value="<?php if(isset($_GET["asunto"])){ echo $_GET["asunto"];}?>" required>
		                                                    </div>
		                                                    <div class="form-group">
																<textarea cols="80" id="editor1" name="contenido" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required>
																	<br>
																	<br>
																	<br>
																	--- --- ---
																	<p>    Cordialmente, </p>
																	<small><b><?=strtoupper($datosUsuarioActual[4].' '.$datosUsuarioActual["uss_nombre2"].' '.$datosUsuarioActual["uss_apellido1"].' '.$datosUsuarioActual["uss_apellido2"]);?></b></small>
																</textarea>
		                                                    </div>
															
		                                                    <div class="btn-group margin-top-20 ">
				                                                <button type="submit" class="btn btn-primary btn-sm margin-right-10"><i class="fa fa-check"></i> Enviar</button>
				                                                <button type="reset" class="btn btn-sm btn-default margin-right-10"><i class="fa fa-times"></i> Cancelar</button>
				                                            </div>
		                                                </form>
		                                            </div>
		                                        </div>
		                                    </div>
		                                </div>
			                            </div>
			                        </div>
								</div>
                            </div>
                        </div>
                    </div>
                </div>