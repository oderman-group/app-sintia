<?php
require_once(ROOT_PATH."/main-app/class/Grados.php");

$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}

$condicionAdicional = '';
if($datosUsuarioActual['uss_tipo'] != 1) {
    $condicionAdicional = "AND not_usuario='".$_SESSION["id"]."'";
}

$consultaNoticias=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".social_noticias WHERE not_id='".$idR."' {$condicionAdicional} AND not_estado!=2 AND not_year='" . $_SESSION["bd"] . "'");
$datosConsulta = mysqli_fetch_array($consultaNoticias, MYSQLI_BOTH);
?>
					<div class="row">
                        <div class="col-sm-9">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header><?=$frases[217][$datosUsuarioActual['uss_idioma']];?></header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/noticias-actualizar.php" method="post" enctype="multipart/form-data" >
										<input type="hidden" name="idR" value="<?=$idR;?>">
                                        
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[127][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="titulo" class="form-control" value="<?=$datosConsulta['not_titulo'];?>">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <textarea name="contenido" id="editor1" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"><?=$datosConsulta['not_descripcion'];?></textarea>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Descripción Final 
                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Este texto se verá reflejado al final de la publicación, después de la imagen o video (si has incluido uno de estos elementos en la publicación)."><i class="fa fa-info"></i></button>
                                            </label>
                                            <div class="col-sm-10">
                                                <textarea name="contenidoPie" id="editor2" class="form-control" rows="3" style="margin-top: 0px; margin-bottom: 0px; height: 70px; resize: none;"><?=$datosConsulta['not_descripcion_pie'];?></textarea>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[211][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-6">
                                                <input type="file" name="imagen" class="form-control">
                                            </div>
											<?php
                                                $urlImagen= $storage->getBucket()->object(FILE_PUBLICACIONES.$datosConsulta["not_imagen"])->signedUrl(new DateTime('tomorrow')); 
                                                $existe=$storage->getBucket()->object(FILE_PUBLICACIONES.$datosConsulta["not_imagen"])->exists();                                               
                                                if(!empty($datosConsulta['not_imagen']) &&  $existe){
                                                $arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
                                                $arrayDatos = json_encode($arrayEnviar);
                                                $objetoEnviar = htmlentities($arrayDatos);
                                            ?>
												<div class="item col-sm-4" id="reg<?=$datosConsulta['not_id']?>">
													<img src="<?=$urlImagen?>" alt="<?=$datosConsulta['not_titulo'];?>" width="50">
													<a href="#" title="<?=$objetoEnviar;?>" id="<?=$datosConsulta['not_id'];?>" name="../compartido/noticias-eliminar-imagen.php?idR=<?=base64_encode($datosConsulta['not_id']);?>" onClick="deseaEliminar(this)"><i class="fa fa-trash"></i></a>
												</div>
												<p>&nbsp;</p>
											<?php }?>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[213][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="urlImagen" class="form-control" value="<?=$datosConsulta['not_url_imagen'];?>">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[214][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="video" class="form-control" value="<?=$datosConsulta['not_video_url'];?>">
                                            </div>
											<?php if(!empty($datosConsulta['not_video'])){?>
													<div class="col-sm-4">
														<iframe width="100" height="80" src="https://www.youtube.com/embed/<?=$datosConsulta['not_video'];?>?rel=0&amp;" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen volume="0"></iframe>
													</div>
											<?php }?>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[224][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <?php
												$datosConsultaBD = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_categorias
												WHERE gcat_activa=1
												");
												?>
                                                <select class="form-control  select2" style="width: 100%" name="categoriaGeneral" required>
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
                                            <label class="col-sm-2 control-label"><?=$frases[128][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-6">
                                                <input type="file" name="archivo" class="form-control">
                                            </div>
											<?php 
                                            $url= $storage->getBucket()->object(FILE_PUBLICACIONES.$datosConsulta["not_archivo"])->signedUrl(new DateTime('tomorrow'));
                                            $existe=$storage->getBucket()->object(FILE_PUBLICACIONES.$datosConsulta["not_archivo"])->exists();
                                            if(!empty($datosConsulta['not_archivo']) && $existe){?>
												<div class="col-sm-4">
													<a href="<?=$url?>" target="_blank"><i class="fa fa-download"></i> Descargar Archivo</a>
											</div>
												<p>&nbsp;</p>
											<?php }?>
                                        </div>

                                        <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DEV){ ?>
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">ID Video Loom</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="video2" class="form-control" value="<?=$datosConsulta['not_enlace_video2'];?>">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Noticia Global?</label>
                                                <div class="col-sm-10">
                                                    <select class="form-control  select2" style="width: 100%" name="global">
                                                        <option value="">Seleccione una opción</option>
                                                        <option value="SI"<?php if($datosConsulta['not_global']=="SI")echo "selected";?>>SI</option>
                                                        <option value="NO"<?php if($datosConsulta['not_global']=="NO")echo "selected";?>>NO</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label" >Notificar en tiempo real?</label>
                                                <div class="col-sm-10">
                                                    <div class="col-sm-2 card-head" data-toggle="tooltip" title="Notificará la noticia en tiempo real a todos los usuarios conectados " style=" border-bottom: 0px rgba(0, 0, 0, 0.2);">
                                                    <label class="switchToggle">
                                                                <input name="notificar" type="checkbox" <?php if ($datosConsulta['not_notificar'] == 1) {
                                                                                                            echo "checked";
                                                                                                        } ?>>
                                                                <span class="slider green round"></span>
                                                            </label>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

										<h4 align="center" style="font-weight: bold;">FILTROS</h4>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[75][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <select  class="form-control select2-multiple" style="width: 100%" multiple name="destinatarios[]">
                                                    <?php
                                                        $destinatarios=(!empty($datosConsulta['not_para']) && $datosConsulta['not_para']!="1,2,3,4,5") ? explode(',',$datosConsulta['not_para']) : "";
                                                        try{
                                                            $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_perfiles");
                                                        } catch (Exception $e) {
                                                            include("../compartido/error-catch-to-report.php");
                                                        }
                                                        while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                            if($opcionesDatos['pes_id'] == TIPO_DEV && $datosUsuarioActual['uss_tipo']!=TIPO_DEV){continue;}
                                                            $selected=($destinatarios!="" && in_array($opcionesDatos['pes_id'], $destinatarios)) ? "selected" : "";
                                                    ?>
                                                        <option value="<?=$opcionesDatos['pes_id'];?>" <?=$selected;?>><?=$opcionesDatos['pes_nombre'];?></option>
                                                    <?php }?>	
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
												<label class="col-sm-2 control-label"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-10">
													<select  class="form-control select2-multiple" style="width: 100%" multiple name="cursos[]">
													<?php
                                                    $infoConsulta = Grados::traerGradosInstitucion($config);
													while($infoDatos = mysqli_fetch_array($infoConsulta, MYSQLI_BOTH)){
														$existe = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".social_noticias_cursos WHERE notpc_noticia='".$idR."' AND notpc_curso='".$infoDatos['gra_id']."'"));
														
													?>	
													  <option value="<?=$infoDatos['gra_id'];?>" <?php if($existe>0){echo "selected";}?>><?=strtoupper($infoDatos['gra_nombre']);?></option>
													<?php }?>	
													</select>
												</div>
											</div>
                                        <button type="submit" class="btn  btn-info">
                                           <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
									    </button>
										
										<a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>

                                    </form>
                                </div>
                            </div>
                        </div>
						
                        <div class="col-sm-3">
                            <?php include("../compartido/publicidad-lateral.php");?>
                        </div>
						
                    </div>
<script src="../ckeditor/ckeditor.js"></script>

<script>
    // Replace the <textarea id="editor1"> with a CKEditor 4
    // instance, using default configuration.
    CKEDITOR.replace( 'editor1' );
    CKEDITOR.replace( 'editor2' );
</script>