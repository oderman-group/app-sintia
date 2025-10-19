<div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[216][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                
								<?php
                                $idFolderCarpetaActual="";
                                if(!empty($_GET["carpeta"])){ $idFolderCarpetaActual=base64_decode($_GET["carpeta"]);}
								if(is_numeric($idFolderCarpetaActual)){
									$idFolderActual = $idFolderCarpetaActual;
									$var = 1;
									$i=0;
									$vectorDatos = array();
									while($var==1){
										$carpetaActual = mysqli_fetch_array(mysqli_query($conexion, "SELECT fold_id, fold_padre FROM ".$baseDatosServicios.".general_folders WHERE fold_id='".$idFolderActual."' AND fold_estado=1 AND fold_year='" . $_SESSION["bd"] . "'"), MYSQLI_BOTH);
										$vectorDatos[$i] = $carpetaActual['fold_id'];
										if(!empty($carpetaActual['fold_padre']) and $carpetaActual['fold_padre']!='0'){
											$idFolderActual = $carpetaActual['fold_padre'];
										}else{$var = 2;}
										$i++;
									}
									?>
									
									<li><a class="parent-item" href="cargas-carpetas.php"><?=$frases[216][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
									<?php
									$cont = count($vectorDatos);
									$cont = $cont - 1;
									while($cont>=0){
										$carpetaActual = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_folders WHERE fold_id='".$vectorDatos[$cont]."' AND fold_estado=1 AND fold_year='" . $_SESSION["bd"] . "'"), MYSQLI_BOTH);
										if($cont>0){
									?>
											<li><a class="parent-item" href="cargas-carpetas.php?carpeta=<?=base64_encode($carpetaActual['fold_id']);?>"><?=$carpetaActual['fold_nombre'];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
									<?php
										}else{
									?>
											<li class="active"><?=$carpetaActual['fold_nombre'];?></li>
									<?php
										}
										$cont--;
									}
								}
								?>
                                
                            </ol>
                        </div>
                    </div>
                   
					<?php 
					if($datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE){
						include("includes/barra-superior-informacion-actual.php"); 
					}
					?>
                    <div class="row">
                    	<div class="col-sm-3">
							<div class="panel">
										<header class="panel-heading panel-heading-blue"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></header>
                                        <div class="panel-body">
											<form action="<?=$_SERVER['PHP_SELF'];?>" method="get">
	
											<div class="form-group row">
												<div class="col-sm-8">
													<input type="text" name="busqueda" class="form-control" value="<?php if(!empty($_GET["busqueda"])){ echo $_GET["busqueda"];}?>" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>...">
												</div>
												<div class="col-sm-4">
													<input type="submit" class="btn btn-primary" value="<?=$frases[8][$datosUsuarioActual['uss_idioma']];?>">
												</div>
											</div>
											</form>
											<?php if(!empty($_GET["busqueda"])){?><div align="center"><a href="<?=$_SERVER['PHP_SELF'];?>"><?=$frases[230][$datosUsuarioActual['uss_idioma']];?></a></div><?php }?>
										</div>
									</div>
                        </div>
						
                        <div class="col-sm-9">
							
							<?php $carpeta=""; if(!empty($idFolderCarpetaActual) && is_numeric($idFolderCarpetaActual)){ $carpeta=$idFolderCarpetaActual; ?>
								<a href="javascript:history.go(-1);" class="btn btn-secondary"><i class="fa fa-long-arrow-left"></i><?=$frases[184][$datosUsuarioActual['uss_idioma']];?></a>
							<?php }?>
							
							<a href="cargas-carpetas-agregar.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>&carpeta=<?=base64_encode($carpeta);?>" class="btn btn-pink"><i class="fa fa-plus-circle"></i><?=$frases[231][$datosUsuarioActual['uss_idioma']];?></a>
							<p>&nbsp;</p>
                       	 	<!-- start widget -->
							<div class="state-overview">
									
									<h3 style="color: black;"><i class="fa fa-folder"></i> <?=strtoupper($frases[232][$datosUsuarioActual['uss_idioma']]);?></h3>
									<div class="row">
										<?php
										$filtro = '';
										if(!empty($idFolderCarpetaActual) && is_numeric($idFolderCarpetaActual)){$filtro .= " AND fold_padre='".$idFolderCarpetaActual."'";}
										if(!empty($_GET["busqueda"])){$filtro .= " AND (fold_nombre LIKE '%".$_GET["busqueda"]."%' OR fold_keywords LIKE '%".$_GET["busqueda"]."%')";}
										$carpetas = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_folders 
										WHERE fold_id_recurso_principal='".$cargaConsultaActual."' AND fold_propietario='".$_SESSION["id"]."' AND fold_activo=1 AND fold_year='" . $_SESSION["bd"] . "' AND fold_categoria=2 AND fold_estado=1 $filtro
										ORDER BY fold_tipo, fold_nombre
										");
										while($carpeta = mysqli_fetch_array($carpetas, MYSQLI_BOTH)){
											$compartidoNum = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_folders_usuarios_compartir WHERE fxuc_folder='".$carpeta['fold_id']."'"));
											
											$numRecursos = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_folders WHERE fold_padre='".$carpeta['fold_id']."' AND fold_estado=1 AND fold_year='" . $_SESSION["bd"] . "'"));
											if(!empty($idFolderCarpetaActual) && !is_numeric($idFolderCarpetaActual) and !empty($carpeta['fold_padre']) and $carpeta['fold_padre']!="0" and empty($_GET["busqueda"])) continue;
										?>
										
										<?php if($carpeta['fold_tipo']==1){?>
										<div class="col-xl-3 col-md-6 col-12" title="<?=$carpeta['fold_nombre'];?>">
										  <div class="info-box bg-b-green">
											<span class="info-box-icon push-bottom"><i class="fa fa-folder"></i></span>
											<div class="info-box-content">
											  <span class="info-box-text"><a href="cargas-carpetas.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>&carpeta=<?=base64_encode($carpeta['fold_id']);?>" style="color: white;"><?=$carpeta['fold_nombre'];?></a></span>
											
											  <span class="info-box-number"><?=$numRecursos;?></span>
											  <div class="progress">
												<div class="progress-bar" style="width: 15%"></div>
											  </div>
												
											 <p align="right">
												 <?php if($compartidoNum>0){?>
												 	<i class="fa fa-share-alt pull-left" style="color: black;" title="Compartido con <?=$compartidoNum;?> usuarios"></i>
												 <?php }?>
												 
												 <a href="cargas-carpetas-editar.php?idR=<?=base64_encode($carpeta['fold_id']);?>&carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" style="color: black;"><i class="fa fa-edit"></i></a>
												 
												 <a href="#" name="../compartido/carpetas/cargas-carpetas-eliminar.php?idR=<?=base64_encode($carpeta['fold_id']);?>" onClick="deseaEliminar(this)" style="color: black;"><i class="fa fa-trash-o"></i></a>
											</p>	
												
											</div>
											<!-- /.info-box-content -->
										  </div>
										  <!-- /.info-box -->
										</div>
										
										<?php }else{?>
										<div class="col-xl-3 col-md-6 col-12" title="<?=$carpeta['fold_nombre'];?>">
										  <div class="info-box">
											<span class="info-box-icon push-bottom"><i class="fa fa-file-text-o"></i></span>
											<div class="info-box-content">
											  <span class="info-box-text"><a href="../files/archivos/<?=$carpeta['fold_nombre'];?>" style="font-size: 12px;" target="_blank"><?=$carpeta['fold_nombre'];?></a></span>
											
											
												<span class="info-box-number">&nbsp;</span>
												<div class="progress">
													<div class="progress-bar" style="width: 0%"></div>
												</div>
												
												<p align="right">
													<a href="cargas-carpetas-editar.php?idR=<?=base64_encode($carpeta['fold_id']);?>&carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" style="color: black;"><i class="fa fa-edit"></i></a>
													
													<a href="#" name="../compartido/carpetas/cargas-carpetas-eliminar.php?idR=<?=$carpeta['fold_id'];?>" onClick="deseaEliminar(this)" style="color: black;"><i class="fa fa-trash-o"></i></a>
												</p>
												
											</div>
											<!-- /.info-box-content -->
										  </div>
										  <!-- /.info-box -->
										</div>
										<?php }?>
										
										
										<?php }?>
										

									  </div>
								
								
									<h3 style="color: black;"><i class="fa fa-share-alt-square"></i> <?=strtoupper($frases[233][$datosUsuarioActual['uss_idioma']]);?></h3>
									<div class="row">
										
										<?php
										$carpetasCompartidas = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_folders
										INNER JOIN ".$baseDatosServicios.".general_folders_usuarios_compartir ON (fxuc_folder=fold_id OR fxuc_folder=fold_padre) AND fxuc_usuario='".$_SESSION["id"]."'
										WHERE fold_activo=1 AND fold_categoria=2 AND fold_estado=1 AND fold_year='" . $_SESSION["bd"] . "' $filtro
										ORDER BY fold_tipo, fold_nombre
										");
										while($carpetaCompartida = mysqli_fetch_array($carpetasCompartidas, MYSQLI_BOTH)){
											$numRecursosCompartido = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_folders WHERE fold_padre='".$carpetaCompartida['fold_id']."' AND fold_estado=1 AND fold_year='" . $_SESSION["bd"] . "'"));
											if(!is_numeric($idFolderCarpetaActual) and !empty($carpetaCompartida['fold_padre']) and $carpetaCompartida['fold_padre']!="0" and $_GET["busqueda"]=="" and $carpetaCompartida['fxuc_folder']!=$carpetaCompartida['fold_id']) continue;
										?>
										
										<?php if($carpetaCompartida['fold_tipo']==1){?>
										<div class="col-xl-3 col-md-6 col-12" title="<?=$carpetaCompartida['fold_nombre'];?>">
										  <div class="info-box bg-b-blue">
											<span class="info-box-icon push-bottom"><i class="fa fa-folder"></i></span>
											<div class="info-box-content">
											  <span class="info-box-text"><a href="cargas-carpetas.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>&carpeta=<?=base64_encode($carpetaCompartida['fold_id']);?>" style="color: white;"><?=$carpetaCompartida['fold_nombre'];?></a></span>
											
											  <span class="info-box-number"><?=$numRecursosCompartido;?></span>
											  <div class="progress">
												<div class="progress-bar" style="width: 15%"></div>
											  </div>
												
											<!--	
											 <p align="right">
												 <a href="cargas-carpetas-editar.php?idR=<?=$carpetaCompartida['fold_id'];?>&carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" style="color: black;"><i class="fa fa-edit"></i></a>
												 
												 <a href="#" name="../compartido/carpetas/cargas-carpetas-eliminar.php?idR=<?=$carpetaCompartida['fold_id'];?>" onClick="deseaEliminar(this)" style="color: black;"><i class="fa fa-trash-o"></i></a>
											</p>
											-->
												
											</div>
											<!-- /.info-box-content -->
										  </div>
										  <!-- /.info-box -->
										</div>
										
										<?php }else{?>
										<div class="col-xl-3 col-md-6 col-12" title="<?=$carpetaCompartida['fold_nombre'];?>">
										  <div class="info-box">
											<span class="info-box-icon push-bottom"><i class="fa fa-file-text-o"></i></span>
											<div class="info-box-content">
											  <span class="info-box-text"><a href="../files/archivos/<?=$carpetaCompartida['fold_nombre'];?>" style="font-size: 12px;" target="_blank"><?=$carpetaCompartida['fold_nombre'];?></a></span>
											
											
												<span class="info-box-number">&nbsp;</span>
												<div class="progress">
													<div class="progress-bar" style="width: 0%"></div>
												</div>
												
												<!--
												<p align="right">
													<a href="cargas-carpetas-editar.php?idR=<?=$carpetaCompartida['fold_id'];?>&carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" style="color: black;"><i class="fa fa-edit"></i></a>
													
													<a href="#" name="../compartido/carpetas/cargas-carpetas-eliminar.php?idR=<?=$carpetaCompartida['fold_id'];?>" onClick="deseaEliminar(this)" style="color: black;"><i class="fa fa-trash-o"></i></a>
												</p>
												-->
												
											</div>
											<!-- /.info-box-content -->
										  </div>
										  <!-- /.info-box -->
										</div>
										<?php }?>
										
										
										<?php }?>
										

									  </div>
								
								
								
								</div>
							<!-- end widget -->
                        </div>
						
                    </div>
                     <!-- Chart end -->

                </div>