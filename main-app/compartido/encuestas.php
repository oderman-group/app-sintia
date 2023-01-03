							<?php
							$encuesta = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".encuestas WHERE adenc_estado=1"), MYSQLI_BOTH);
							if(isset($encuesta['adenc_id']) AND $encuesta['adenc_id']!=""){
								$numRespUsuario = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".comentarios 
								WHERE adcom_institucion='".$config['conf_id_institucion']."' AND adcom_usuario='".$_SESSION["id"]."' AND adcom_id_encuesta='".$encuesta['adenc_id']."'
								"));
							?>
							<div class="panel">
								<header class="panel-heading panel-heading-purple">Encuesta</header>
                                <div class="panel-body">
									
									
									<h3 class="text-info"><?=$encuesta['adenc_nombre'];?></h3>
									
									<?php if($encuesta['adenc_imagen']!=""){?>
										<div class="item" align="center" style="margin-bottom: 10px;"><img src="http://plataformasintia.com/files-general/encuestas/<?=$encuesta['adenc_imagen'];?>"></div>
									<?php }?>
									
									<form class="form-horizontal" action="../compartido/guardar.php" method="post">
										<input type="hidden" name="id" value="11">
										<input type="hidden" name="encuesta" value="<?=$encuesta['adenc_id'];?>">
										<?php
										$opcionesR = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".encuestas_opciones WHERE adencop_encuesta='".$encuesta['adenc_id']."'");
										while($opciones = mysqli_fetch_array($opcionesR, MYSQLI_BOTH)){
											$numGeneral = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".comentarios 
											WHERE adcom_respuesta='".$opciones['adencop_id']."'
											"));
											
											$numRsp = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".comentarios 
											WHERE adcom_institucion='".$config['conf_id_institucion']."' AND adcom_usuario='".$_SESSION["id"]."' AND adcom_respuesta='".$opciones['adencop_id']."'
											"));
										?>
											<div class="form-group row">
												<div class="col-sm-12">
													<input type="radio" name="respuesta" value="<?=$opciones['adencop_id'];?>" <?php if($numRsp>0){echo "checked";} if($numRespUsuario>0 and $numRsp==0){echo "disabled";} ?> />
													<?=$opciones['adencop_opcion'];?>
													<?php if($numRespUsuario>0){echo "(".$numGeneral." votos)";}?>
												</div>
											</div>
										<?php }?>
										
										<?php if($numRespUsuario==0){?>
										<div class="form-group">
											<div class="offset-md-3 col-md-6">
												<button type="submit" class="btn btn-info">Responder</button>
											</div>
										</div>
										<?php }else{?>
											<p class="text-success">
												<i class="fa fa-thumbs-up"></i>
												Muchas gracias por tu respuesta.
											</p>
										<?php }?>
									</form>
									
								</div>
							</div>
							<?php }?>