    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<!-- dropzone -->
    <link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
    <!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
    <!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<!-- END HEAD -->
<?php 
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
    $input = json_decode(file_get_contents("php://input"), true);
    if (!empty($input)) {
        $_GET = $input;
    }
}
require_once(ROOT_PATH."/main-app/class/Grados.php");
?>

            
            <div class="card-body " id="bar-parent6">
                <form class="form-horizontal" action="../compartido/noticias-guardar.php" method="post" enctype="multipart/form-data">
               
                    <div class="form-group row">
                        <label class="col-sm-2 control-label"><?= $frases[127][$datosUsuarioActual['uss_idioma']]; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label"><?= $frases[50][$datosUsuarioActual['uss_idioma']]; ?></label>
                        <div class="col-sm-10">
                            <textarea name="contenido" id="editor1" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Descripción final 
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Este texto se verá reflejado al final de la publicación, después de la imagen o video (si has incluido uno de estos elementos en la publicación)."><i class="fa fa-info"></i></button>
                        </label>
                        <div class="col-sm-10">
                            <textarea name="contenidoPie" id="editor2" class="form-control" rows="3" style="margin-top: 0px; margin-bottom: 0px; height: 70px; resize: none;" required></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label"><?= $frases[211][$datosUsuarioActual['uss_idioma']]; ?></label>
                        <div class="col-sm-6">
                            <input type="file" name="imagen" class="form-control" onChange="validarPesoArchivo(this)" accept=".png, .jpg, .jpeg">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label"><?= $frases[213][$datosUsuarioActual['uss_idioma']]; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="urlImagen" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label"><?= $frases[214][$datosUsuarioActual['uss_idioma']]; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="video" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label"><?= $frases[224][$datosUsuarioActual['uss_idioma']]; ?></label>
                        <div class="col-sm-10">
                            <?php
                            $datosConsulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".general_categorias
												WHERE gcat_activa=1
												");
                            ?>
                            <select class="form-control  select2" style="width: 100%" name="categoriaGeneral" required>
                                <option value="">Seleccione una opción</option>
                                <?php
                                while ($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)) {
                                ?>
                                    <option value="<?= $datos['gcat_id']; ?>" <?php if ($datos['gcat_id'] == 15) echo "selected"; ?>><?= $datos['gcat_nombre'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-2 control-label"><?= $frases[228][$datosUsuarioActual['uss_idioma']]; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="keyw" class="tags tags-input" data-type="tags" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label"><?= $frases[128][$datosUsuarioActual['uss_idioma']]; ?></label>
                        <div class="col-sm-6">
                            <input type="file" name="archivo" onChange="validarPesoArchivo(this)"  class="form-control">
                        </div>
                    </div>

                    <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DEV){ ?>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label">ID Video Loom</label>
                            <div class="col-sm-10">
                                <input type="text" name="video2" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Noticia Global?</label>
                            <div class="col-sm-2">
                                <select class="form-control  select2" style="width: 100%" name="global">
                                    <option value="">Seleccione una opción</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" >Notificar en tiempo real?</label>
                            <div class="col-sm-10">
                                <div class="col-sm-2 card-head" data-toggle="tooltip" title="Notificará la noticia en tiempo real a todos los usuarios conectados " style=" border-bottom: 0px rgba(0, 0, 0, 0.2);">
                                    <header>
                                        <label class="switchToggle">
                                            <input name="notificar" type="checkbox" >
                                            <span class="slider green round"></span>
                                        </label>
                                    </header>
                                </div>
                            </div>
                         </div>
                    <?php } ?>

                    <h4 align="center" style="font-weight: bold;"><?= $frases[205][$datosUsuarioActual['uss_idioma']]; ?></h4>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label"><?= $frases[75][$datosUsuarioActual['uss_idioma']]; ?></label>
                        <div class="col-sm-10">
                            <select id="multiple" style="width: 100%" class="form-control select2-multiple" multiple name="destinatarios[]">
                                <option value="">Seleccione una opción</option>
                                <?php
                                    try{
                                        $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_perfiles");
                                    } catch (Exception $e) {
                                        include("../compartido/error-catch-to-report.php");
                                    }
                                    while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                        if($opcionesDatos['pes_id'] == TIPO_DEV && $datosUsuarioActual['uss_tipo']!=TIPO_DEV){continue;}
                                ?>
                                    <option value="<?=$opcionesDatos['pes_id'];?>"><?=$opcionesDatos['pes_nombre'];?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label"><?= $frases[5][$datosUsuarioActual['uss_idioma']]; ?></label>
                        <div class="col-sm-10">
                            <select style="width: 100%" id="multiple" class="form-control select2-multiple" multiple name="cursos[]">
                                <?php
                                $infoConsulta = Grados::traerGradosInstitucion($config);
                                while ($infoDatos = mysqli_fetch_array($infoConsulta, MYSQLI_BOTH)) {
                                ?>
                                    <option value="<?= $infoDatos['gra_id']; ?>"><?= strtoupper($infoDatos['gra_nombre']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn  btn-info">
                        <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
                    </button>

                </form>
            </div>
     
  
   <!-- start js include path -->

    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    
<script>
   
    CKEDITOR.replace('editor1');
    CKEDITOR.replace('editor2');
</script>
