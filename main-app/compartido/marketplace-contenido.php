<div class="row mb-4">
						<div class="col-sm-12">
							<img class="img-responsive" src="https://plataformasintia.com/files-general/main-app/marketplace/marketplace1.png">
						</div>
					</div>	
					                   
                   
                     <!-- start course list -->
                     <div class="row">
						 <div class="col-sm-3" style="width: 100%; height: 800px; overflow-y: scroll;">
							 	<!--
							 	<div class="panel">
									<header class="panel-heading panel-heading-green"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                      <div class="panel-body">
										<p><?=$frases[177][$datosUsuarioActual[8]];?></p>
									    <p><?=$frases[178][$datosUsuarioActual[8]];?></p>
									  </div>
								</div>-->
							 
							 
							 	<p align="center">
									<?php if($_SESSION["empresa"]==""){?>
										<a href="empresas-agregar.php" class="btn btn-danger btn-lg"><i class="fa fa-shopping-cart"></i> VENDE TUS PRODUCTOS AQUÍ </a>
									<?php }else{?>
										<a href="productos-agregar.php" class="btn btn-danger btn-lg"><i class="fa fa-plus"></i> NUEVO PRODUCTO </a>
									<?php }?>
							 	</p>
							 
							 <?php if($_SESSION["empresa"]!=""){?>
							 <div class="panel">
										<header class="panel-heading panel-heading-red">Menú Marketplace</header>
										<div class="panel-body">
											<p><a href="productos-agregar.php">Agregar otro producto</span></a></p>
											<p><a href="marketplace.php?company=<?=$_SESSION["empresa"];?>">Ver mis productos</span></a></p>
							 				<p><a href="marketplace.php">Ver todos los productos</span></a></p>
											<hr>
											<p><a href="https://youtu.be/cmsQDO9tIrQ" target="_blank">Ver tutorial de uso de MarketPlace</span></a></p>
											<p><a href="mensajes-redactar.php?para=1&asunto=REQUIERO ASESORÍA PARA USAR SINTIA MARKETPLACE">Solicitar asesoría</span></a></p>
										</div>
                                    </div>
								<?php }else{?>

								<div class="panel">
										<header class="panel-heading panel-heading-red">Menú Marketplace</header>
										<div class="panel-body">
							 				<p><a href="marketplace.php">Ver todos los productos</span></a></p>
											<hr>
											<p><a href="https://youtu.be/cmsQDO9tIrQ" target="_blank">Ver tutorial de uso de MarketPlace</span></a></p>
											<p><a href="mensajes-redactar.php?para=1&asunto=REQUIERO ASESORÍA PARA USAR SINTIA MARKETPLACE">Solicitar asesoría</span></a></p>
										</div>
                                    </div>
								<?php }?>
							 
							 	<div class="panel">
										<header class="panel-heading panel-heading-blue"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?> productos</header>
                                        <div class="panel-body">
											<form action="<?=$_SERVER['PHP_SELF'];?>" method="get">
											<div class="form-group row">
												<div class="col-sm-8">
													<input type="text" name="busqueda" class="form-control" value="<?=$_GET["busqueda"];?>" placeholder="<?=$frases[235][$datosUsuarioActual[8]];?>...">
												</div>
												<div class="col-sm-4">
													<input type="submit" class="btn btn-primary" value="<?=$frases[8][$datosUsuarioActual[8]];?>">
												</div>
											</div>
											</form>
											<?php if(isset($_GET["busqueda"])){?><div align="center"><a href="<?=$_SERVER['PHP_SELF'];?>"><?=$frases[230][$datosUsuarioActual['uss_idioma']];?></a></div><?php }?>
										</div>
									</div>
						 	
							 	<div class="panel">
										<header class="panel-heading panel-heading-purple">Categorías</header>
										<div class="panel-body">
											<?php
											$categorias = mysqli_query($conexion, "SELECT * FROM ".$baseDatosMarketPlace.".categorias_productos");
											while($cat = mysqli_fetch_array($categorias, MYSQLI_BOTH)){
												if($cat[0]==$_GET["cat"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p>
													<a href="<?=$_SERVER['PHP_SELF'];?>?cat=<?=$cat[0];?>" <?=$estiloResaltado;?>>
														<span <?=$estiloDG;?>><?=strtoupper($cat['catp_nombre']);?></span>
													</a>
												</p>
											<?php }?>
												<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>"><?=strtoupper($frases[180][$datosUsuarioActual[8]]);?></span></a></p>
										</div>
                                    </div>
								
                        	
							 
						 </div>
						 
						 <div class="col-sm-9">
							<div class="row"> 
						<?php
						$filtro = '';
						if($_GET["cat"]!=""){$filtro .= " AND prod_categoria='".$_GET["cat"]."'";}
						if(is_numeric($_GET["prod"])){$filtro .= " AND prod_id='".$_GET["prod"]."'";}
						if(is_numeric($_GET["company"])){$filtro .= " AND prod_empresa='".$_GET["company"]."'";}		
						
						if($_GET["busqueda"]!=""){$filtro .= " AND (prod_nombre LIKE '%".$_GET["busqueda"]."%' 
						OR prod_descripcion LIKE '%".$_GET["busqueda"]."%' 
						OR prod_keywords LIKE '%".$_GET["busqueda"]."%'
						)";}		
								
						$serviciosConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosMarketPlace.".productos
						INNER JOIN ".$baseDatosMarketPlace.".categorias_productos ON catp_id=prod_categoria
						INNER JOIN ".$baseDatosMarketPlace.".empresas ON emp_id=prod_empresa
						WHERE prod_id=prod_id $filtro
						");
						
						$numProductos = mysqli_num_rows($serviciosConsulta);
						if($numProductos==0){
							echo '
							<p style="padding:10px; color:tomato;">No se econtraron productos.</p>
							<p style="padding:10px;"><a href="marketplace.php">VER TODOS</a></p>
							';
						}		
						while($datosConsulta = mysqli_fetch_array($serviciosConsulta, MYSQLI_BOTH)){
							$foto = 'course3.jpg';
							if($datosConsulta['prod_foto']!=""){$foto = $datosConsulta['prod_foto'];}
							
							$class = 'col-lg-3 col-md-6 col-12 col-sm-6';
							if(is_numeric($_GET["prod"])){
								$class = 'col-sm-12';
							}
							
							
						//JSON PARA ELIMINAR
						$arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
						$arrayDatos = json_encode($arrayEnviar);
						$objetoEnviar = htmlentities($arrayDatos);
						?>
						 <div class="<?=$class;?>" id="reg<?=$datosConsulta['prod_id'];?>"> 
							<div class="blogThumb">
								<div class="thumb-center">
									<a href="marketplace.php?prod=<?=$datosConsulta['prod_id'];?>"><img class="img-responsive" src="../files/marketplace/productos/<?=$foto;?>"></a></div>
	                        	<div class="course-box">
	                        	<h5><a href="marketplace.php?prod=<?=$datosConsulta['prod_id'];?>"><?=strtoupper($datosConsulta['prod_nombre']);?></a></h5>
		                            <div class="text-muted">
										<span class="m-r-10" style="font-size: 10px;"> <?=$datosConsulta['catp_nombre'];?></span> 
		                            </div>
									<p><span style="font-weight: bold;"> $<?=number_format($datosConsulta['prod_precio'],0,",",".");?></span></p>
									<?php 
									if(is_numeric($_GET["prod"])){
										$consultaVisita = mysqli_query($conexion, "SELECT * FROM ".$baseDatosMarketPlace.".productos_visitas 
										WHERE pxvis_producto='".$_GET["prod"]."' AND pxvis_usuario='".$_SESSION["id"]."' AND pxvis_institucion='".$config['conf_id_institucion']."'");
										
										$numVisita = mysqli_num_rows($consultaVisita);
										$datoVisita = mysqli_fetch_array($consultaVisita, MYSQLI_BOTH);
										if($numVisita>0){
											mysqli_query($conexion, "UPDATE ".$baseDatosMarketPlace.".productos_visitas SET pxvis_cantidad=pxvis_cantidad+1 
											WHERE pxvis_usuario='".$_SESSION["id"]."' AND pxvis_institucion='".$config['conf_id_institucion']."' AND pxvis_producto='".$_GET["prod"]."'");
											
										}else{
											mysqli_query($conexion, "INSERT INTO ".$baseDatosMarketPlace.".productos_visitas(pxvis_producto, pxvis_institucion, pxvis_usuario, pxvis_fecha, pxvis_cantidad)VALUES('".$_GET["prod"]."', '".$config['conf_id_institucion']."', '".$_SESSION["id"]."', now(), 1)");
											
										}
									?>
										
										<p><?=$datosConsulta['prod_descripcion'];?></p>
									
										<hr>
										<p><span> <b>Negocio:</b><br> <?=$datosConsulta['emp_nombre'];?></span></p>
										<!--<p><span> <b>Teléfono:</b><br> <?=$datosConsulta['emp_telefono'];?></span></p>-->
										<p><span> <b>Email:</b><br> <?=$datosConsulta['emp_email'];?></span></p>
									
										<a href="marketplace.php" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i> <?=$frases[184][$datosUsuarioActual[8]];?></a>
										
										<a href="#" class="btn btn-info" name="<?=$datosConsulta['emp_usuario'];?>" title="<?=$datosConsulta['prod_nombre'];?>" onClick="msjMarketplace(this)"><i class="fa fa-envelope"></i> Enviar mensaje</a>
										
										<?php
										if($datosConsulta['prod_precio']>=10000){
										?>
											<a href="productos-comprar.php?id=<?=$datosConsulta['prod_id'];?>" class="btn btn-success"><i class="fa fa-money"></i> Comprar</a>
										<?php
											}
										?>
									
									<?php }else{
										if($datosConsulta['emp_id']==$_SESSION["empresa"]){
										?>
											<a href="#" class="btn btn-success"><i class="fa fa-edit"></i></a>
											<a href="#" title="<?=$objetoEnviar;?>" id="<?=$datosConsulta['prod_id'];?>" name="../compartido/guardar.php?get=25&idR=<?=$datosConsulta['prod_id'];?>" onClick="deseaEliminar(this)" class="btn btn-danger"><i class="fa fa-trash"></i></a>
										<?php
										}else{
											if($datosConsulta['prod_precio']>=10000){
										?>
											<a href="productos-comprar.php?id=<?=$datosConsulta['prod_id'];?>" class="btn btn-success"><i class="fa fa-money"></i> Comprar</a>
										<?php
											}
										?>
											<a href="#" class="btn btn-info" name="<?=$datosConsulta['emp_usuario'];?>" title="<?=$datosConsulta['prod_nombre'];?>" onClick="msjMarketplace(this)"><i class="fa fa-envelope"></i></a>
										<?php }?>
									
		                            
									
	                        	</div>
	                        </div>	
                    	</div>
							<?php } 
						
						}?>
						</div>
					</div>

						 
	                    
			        </div>
			        <!-- End course list -->