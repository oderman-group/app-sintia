<?php if(isset($publicidadPopUp['pubxub_id']) AND $publicidadPopUp['pubxub_id']!="" and $numMostrarPopUp<$publicidadPopUp['pubxub_muestras_popup']){
	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".publicidad_estadisticas(pest_publicidad, pest_institucion, pest_usuario, pest_pagina, pest_ubicacion, pest_fecha, pest_ip, pest_accion)
	VALUES('".$publicidadPopUp['pub_id']."', '".$config['conf_id_institucion']."', '".$_SESSION["id"]."', '".$idPaginaInterna."', 3, now(), '".$_SERVER["REMOTE_ADDR"]."', 1)");
	
?>
<div class="modal fade" id="modalAnuncios" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	   <div class="modal-dialog">
		  <div class="modal-content">
			 <div class="modal-body">
				 <?php if($publicidadPopUp['pub_titulo']!=""){?><h4><?=$publicidadPopUp['pub_titulo'];?></h4><?php }?>
				 <?php if($publicidadPopUp['pub_descripcion']!=""){?><p><?=$publicidadPopUp['pub_descripcion'];?></p><?php }?>
				 
				 <?php if($publicidadPopUp['pub_imagen']!=""){?>
					 <div class="item">
						 <a href="../compartido/guardar.php?get=14&idPag=<?=$idPaginaInterna;?>&idPub=<?=$publicidadPopUp['pub_id'];?>&idUb=3&url=<?=$publicidadPopUp['pub_url'];?>" target="_blank">
							 <img src="https://plataformasintia.com/files-general/pub/<?=$publicidadPopUp['pub_imagen'];?>" width="470">
						 </a>
					 </div>
					 <p>&nbsp;</p>
				 <?php }?>
				 
				 <?php if($publicidadPopUp['pub_video']!=""){?>
				 <p><iframe width="450" height="315" src="https://www.youtube.com/embed/<?=$publicidadPopUp['pub_video'];?>?rel=0&amp;mute=<?=$publicidadPopUp['pub_mute'];?>&start=<?=$publicidadPopUp['pub_start'];?>&end=<?=$publicidadPopUp['pub_end'];?>&autoplay=<?=$publicidadPopUp['pub_autoplay'];?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen volume="0"></iframe></p>
				 <p class="text-danger">Si desea cerrar este anuncio, por favor pause el video primero.</p>
				 <?php }?>
			</div>
			 <div class="modal-footer">
				<?php if($publicidadPopUp['pub_boton_accion']!=""){?>
					<a href="../compartido/guardar.php?get=14&idPag=<?=$idPaginaInterna;?>&idPub=<?=$publicidadPopUp['pub_id'];?>&idUb=3&url=<?=$publicidadPopUp['pub_url'];?>" class="btn btn-success" target="_blank"><?=$publicidadPopUp['pub_boton_accion'];?></a>
				 <?php }?>
				<a href="#" data-dismiss="modal" class="btn btn-danger">Cerrar</a>
		 	</div>
		  </div>
	   </div>
	</div>
<?php }?>


<?php
//Solicitar datos
if($datosUsuarioActual['uss_solicitar_datos']==1){
?>
<div class="modal fade" id="modalDatos" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	   <div class="modal-dialog"  style="max-width: 1350px!important;">
		  <div class="modal-content">
			 <div class="modal-body">
				 <div class="row">
				 <div class="col-sm-12">
							<div style="background-color: #fbbd01; color:black; padding: 10px;">
								<h4 style="font-weight: bold;">YA CASI ESTÁ LISTO EL DEMO  PARA TU INSTUCIÓN</h4>
								<?=strtoupper($datosUsuarioActual["uss_nombre"]);?>, Por favor, sólo completa estos datos que hacen falta y listo.<br>
								Muchas gracias!
							</div>
                            <div class="card card-box">
                                <div class="card-head">
                                    <header>Información</header>
                                </div>
                                <div class="card-body" id="bar-parent6">
                                    <form action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
										<input type="hidden" name="id" value="15">
										<input type="hidden" name="tipoUsuario" value="<?=$datosUsuarioActual['uss_tipo'];?>">
										
										
                                                <input type="hidden" value="<?=$datosUsuarioActual["uss_celular"];?>" name="celular">
										
										<div class="form-group row">
                                            <label class="col-sm-4 control-label">Nombre de la Institución</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="institucion" class="form-control" style="text-transform: uppercase;" required autofocus>
                                            </div>
                                        </div>

										<div class="form-group row">
                                            <label class="col-sm-4 control-label">Municipio de la Institución</label>
                                            <div class="col-sm-8">
                                                <select class="form-control  select2" name="instMunicipio" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
													INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
													");
													while($opg = mysql_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['ciu_id'];?>"><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
												
										<input type="submit" class="btn btn-primary" value="Guardar cambios y empezar!">&nbsp;
                                    </form>
                                </div>
                            </div>
                        </div>
				 </div>
				 
			 </div>
			  <!--
			 <div class="modal-footer">
				<a href="#" data-dismiss="modal" class="btn btn-danger">Cerrar</a>
		 	</div>
				-->
		  </div>
	   </div>
	</div>
<?php }?>


<?php
//felicitar por el cumpleaños
if(isset($cumpleUsuario['agno']) AND $cumpleUsuario['agno']!=""){
?>

<div class="modal fade" id="modalCumple" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	   <div class="modal-dialog"  style="max-width: 1350px!important;">
		  <div class="modal-content">
			  
			  <div class="modal-header">
				<h1 class="modal-title" align="center">FELIZ CUMPLEAÑOS A TI...</h1>
				<a href="#" data-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-2"><i class="fa fa-window-close"></i></a>
			  </div>
			  
			 <div class="modal-body" align="center">
                               
             	<p>
					<b><?=$datosUsuarioActual['uss_nombre'];?></b>, Queremos felicitarte en este día por motivo de tu cumple!<br>
					No todos los días se cumplen <b><?=$edadUsuario;?> AÑOS.</b><br>
					Oye, que Dios te bendiga grandemente y prospere tus planes.
				</p>
									
				<div align="center"><img src="https://plataformasintia.com/files-general/email/cumple1.jpg" width="500"></div>
									
				<p>
					De todos los que hacemos parte de <b>PLATAFORMA SINTIA Y GRUPO ODERMAN</b>...<br>
					...MUCHAS FELICIDADES!!!
				</p>

			 </div>
			  
			 <div class="modal-footer">
				<a href="#" data-dismiss="modal" class="btn btn-danger" id="boton-cerrar">CERRAR FELICITACIÓN</a>
		 	 </div>
			  
		  </div>
	   </div>
	</div>
<?php }?>

<?php
//Solicitar comentario
if($datosUsuarioActual['uss_preguntar_animo']==1){
?>
<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	   <div class="modal-dialog"  style="max-width: 1350px!important;">
		  <div class="modal-content">
			 <div class="modal-body">
				 <div class="row">
				 <div class="col-sm-12">
							<div style="background-color: #fbbd01; color:black; padding: 10px;">
								<h4 style="font-weight: bold;">Regalanos tu opinión</h4>
								Tu opinión o sugerencia sobre la plataforma SINTIA es muy importante<br>
								Muchas gracias!
							</div>
                            <div class="card card-box">
                                <div class="card-head">
                                    <header>Escribe tu opinión aquí abajo</header>
                                </div>
                                <div class="card-body" id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/guardar.php" method="post">
										<input type="hidden" name="id" value="10">
										<div class="form-group row">
											<div class="col-sm-12">
												<textarea name="contenido" class="form-control" rows="3" placeholder="¿Cuál es tu opinión o sugerencia sobre la plataforma SINTIA?" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
											</div>
										</div>

										<div class="form-group">
											<div class="offset-md-3 col-md-9">
												<button type="submit" class="btn btn-info">Enviar ahora</button>
												<button type="reset" class="btn btn-default"><?=$frases[171][$datosUsuarioActual[8]];?></button>
											</div>
										</div>
									</form>
                                </div>
                            </div>
					 		<div style="background-color: #ea4335; color:white; padding: 5px;">
								<h4 style="font-weight: bold;">#RETO</h4>
								Si quieres ir un poco más allá, te retamos a hacer un corto video, de máximo 60 segundos, con tu celular, dando tu opinión sobre la plataforma y nos lo envías al número de <b>Whatsapp 313 752 5894</b><br>
								Contamos contigo!
							</div>
                        </div>
				 </div>
				 
			 </div>
			 <div class="modal-footer">
			<a href="#" data-dismiss="modal" class="btn btn-danger" id="boton-cerrar-comentario">Cerrar</a>
		 </div>
		  </div>
	   </div>
	</div>
<?php }?>

<?php
//Mostrar a los directivos si tiene deuda
if($config['conf_deuda']==1 and $datosUsuarioActual['uss_tipo']==5){
?>
<div class="modal fade" id="modalDeuda" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	   <div class="modal-dialog"  style="max-width: 1350px!important;">
		  <div class="modal-content">
			 <div class="modal-body">
				 <div class="row">
				 <div class="col-sm-12">
                            <div class="card card-box">
                                <div class="card-head">
									<p style="text-align: right;"><a href="#" data-dismiss="modal" class="btn btn-danger" id="boton-cerrar-modal-deuda">X</a></p>
                                    <header>¡Saldo pendiente!</header>
                                </div>
								
                                <div class="card-body" id="bar-parent6">
									
									<div>
										
										
										<h4 style="color: tomato;">Esta información sólo la verá el personal directivo de la Institución.</h4>
                                      	
										<p>
											Le recordamos que la Institución tiene una factura pendiente de pago con nosotros. A continuación los detalles de la factura:
										</p>
										
										<p>
                                      		<b>NRO. FACTURA:</b> <?=$config['conf_numero_factura'];?><br />
											<b>CONCEPTO:</b> <?=$config['conf_concepto'];?><br />
                                        	<b>VALOR NETO:</b> $<?=number_format($config['conf_valor'],0,",",".");?> COP.
                                      	</p>
										
										<p>
											<form class="form-horizontal" action="http://oderman.com.co/sql.php" method="post" target="_blank">
												<input type="hidden" name="idSQL" value="4">
												<input type="hidden" name="factura" value="<?=$config['conf_numero_factura'];?>">
												<button type="submit" class="btn btn-success">PAGAR AHORA</button>
											</form>	
										</p>
										
										<p>
											ESTIMAD@ <b><?=$datosUsuarioActual['uss_nombre'];?></b>...<br>
											En ningún momento es nuestra intención causarles molestias.<br>
											Somos una empresa que dependemos del pago oportuno de nuestros clientes con el fin de prestar cada día un mejor servicio y también para cumplir con nuestras obligaciones financieras.
										</p>
										
                                      		<p>¡Muchas gracias por su atención!</p>
										
										<hr />
										  <p><b>FORMAS DE PAGO DISPONIBLES</b></p>

											<p><b>Bancolombia:</b> Cuenta de Ahorros Bancolombia <b># 431 565 88 254</b> a nombre de <b>JHON ODERMAN MEJIA</b> Identificado con C&eacute;dula 1.051.820.890.<br>
											Puede hacer el pago mediante una consignaci&oacute;n o transferencia bancaria.</p>

											<p> <b>Efecty y Gana:</b> Puedes hacer el giro a nombre de Jhon Oderman Mejía, con número de cédula 1.051.820.890.</p>

											<p> <b>Sitio Web:</b> Puede hacer su pago de manera virtual en nuestro sitio web <b>www.oderman.com.co</b>, opción pago de facturas, ingresa el número de la factura a pagar y continúa con el proceso.</p>

											<p align="center" style="font-size:10px; color:tomato;"> <b>SI AÚN TIENE DUDAS CON RESPECTO A ESTA FACTURA O AL PROCESO DE PAGO AGRADECEMOS COMUNICARSE CON NOSOTROS.<br>
											RECUERDE QUE EL NO PAGO A TIEMPO DE LAS FACTURAS PUEDE OCASIONAR SUSPENSIÓN EN LOS SERVICIOS PRESTADOS O COBRO ADICIONAL POR MORA.</b></p>
										
									</div>
									<!--
                                    <form class="form-horizontal" action="#../compartido/guardar.php" method="post">
										<input type="hidden" name="id" value="10">
										<div class="form-group row">
											<div class="col-sm-12">
												<textarea name="contenido" class="form-control" rows="3" placeholder="Si tienes algún comentario respecto a este cobro puedes hacernoslo saber..." style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
											</div>
										</div>

										<div class="form-group">
											<div class="offset-md-3 col-md-9">
												<button type="submit" class="btn btn-info">Enviar ahora</button>
											</div>
										</div>
									</form>
									-->
                                </div>
								
                            </div>
                        </div>
				 </div>
				 
			 </div>
			 <div class="modal-footer">
			<a href="#" data-dismiss="modal" class="btn btn-danger" id="boton-cerrar-modal-deuda2">Cerrar aviso</a>
		 </div>
		  </div>
	   </div>
	</div>
<?php }?>