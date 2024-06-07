<?php
include("session.php");
$idPaginaInterna = 'DV0064';
include("../compartido/historial-acciones-guardar.php");
Modulos::verificarPermisoDev();
include("../compartido/head.php");

try {
    $consulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosMarketPlace . ".productos WHERE prod_id='" . base64_decode($_GET["idR"]) . "'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}

$infoDatos = mysqli_fetch_array($consulta, MYSQLI_BOTH);
$foto = 'https://via.placeholder.com/510?text=Sin+Imagen';
if (!empty($infoDatos['prod_foto']) && file_exists('../files/marketplace/productos/'.$infoDatos['prod_foto'])) {
    $foto = '../files/marketplace/productos/'.$infoDatos['prod_foto'];
}
?>

<!--bootstrap -->
<link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!-- dropzone -->
<link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php
    include("../compartido/encabezado.php");
    include("../compartido/panel-color.php");
    ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title">Editar Productos</div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="mps-productos.php" onClick="deseaRegresar(this)"><?=$frases[410][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Editar Productos</li>
                        </ol>
                    </div>
                </div>
                <span style="color: blue; font-size: 15px;" id="resp"></span>
                <div class="row">
                    <div class="col-sm-12">
                        <?php include("../../config-general/mensajes-informativos.php"); ?>
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple"><?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </header>
                            <div class="panel-body">

                                <form name="formularioGuardar" action="mps-productos-actualizar.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" value="<?=base64_decode($_GET["idR"]);?>" name="idR">
										
                                    <div class="form-group row">
                                        <div class="col-sm-4" style="margin: 0 auto 10px">
                                            <div class="item">
                                                <img src="<?=$foto?>" alt="Imagen Producto" id="imgProd" width="300" height="300" />
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function cambiarProd() {
                                            let img = document.getElementById("imgProd");
                                            let input = document.getElementById("customFile");

                                            if(input.files[0]){
                                                img.src= URL.createObjectURL(input.files[0]);
                                            }
                                        }
                                    </script>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Foto del producto</label>
                                        <div class="col-sm-4">
                                            <input type="file" id="customFile" name="imagen" class="form-control" onchange="cambiarProd()">
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Titulo del producto <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="nombre" class="form-control" required placeholder="Ejemplo: Tapaboca N95" value="<?=$infoDatos['prod_nombre']?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Descripción del producto <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-10">
                                            <textarea name="descripcion" id="editor1" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required><?=$infoDatos['prod_descripcion']?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Existencia</label>
                                        <div class="col-sm-4">
                                            <input type="number" name="existencia" class="form-control" value="<?=$infoDatos['prod_existencias']?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Precio del producto <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                            <input type="number" name="precio" class="form-control" required placeholder="Ejemplo: 10000" value="<?=$infoDatos['prod_precio']?>">
                                            <span style="color: navy;">Solamente digite el número sin puntos ni simbolos.</span><br>
                                            <span style="color: tomato;">Si desea recibir el pago en línea, el valor mínimo debe ser de $10.000 COP.</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">URL video de youtube</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="video" class="form-control" placeholder="Ejemplo: https://www.youtube.com/watch?v=g2LSYPm7hR4" value="https://www.youtube.com/watch?v=<?=$infoDatos['prod_video']?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Categoría del producto <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-10">
                                            <select class="form-control  select2" name="categoria" required>
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                    $datosConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosMarketPlace.".categorias_productos WHERE catp_eliminado!=1");
                                                    while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
                                                        $selected='';
                                                        if($datos['catp_id']==$infoDatos['prod_categoria']){
                                                            $selected='selected';
                                                        }
                                                ?>
                                                    <option value="<?=$datos['catp_id'];?>" <?=$selected?>><?=$datos['catp_nombre']?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Empresa <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-10">
                                            <select class="form-control  select2" name="empresa" required>
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                    $datosConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosMarketPlace.".empresas WHERE emp_eliminado!=1");
                                                    while($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
                                                        $selected='';
                                                        if($datos['emp_id']==$infoDatos['prod_empresa']){
                                                            $selected='selected';
                                                        }
                                                ?>
                                                    <option value="<?=$datos['emp_id'];?>" <?=$selected?>><?=$datos['emp_nombre']?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Palabras claves relacionadas al producto</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="keyw" class="tags tags-input" data-type="tags" value="<?=$infoDatos['prod_keywords']?>" />
                                            <span style="color: navy;">Con estas palabras claves relacionadas pueden encontrar más fácil esta publicación.</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Estado</label>
                                        <div class="col-sm-2">
                                            <select class="form-control  select2" name="estado" required>
                                                <option value="">Seleccione una opción</option>
                                                <option value="1" <?php if($infoDatos['prod_activo']==1){ echo 'selected';}?>>Activo</option>
                                                <option value="0" <?php if($infoDatos['prod_activo']==0){ echo 'selected';}?>>Inactivo</option>
                                            </select>
                                        </div>
                                    </div>

                                    <?php $botones = new botonesGuardar("mps-productos.php",Modulos::validarPermisoEdicion()); ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php"); ?>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../config-general/assets/plugins/popper/popper.js"></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js" charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js" charset="UTF-8"></script>
    <!-- Common js-->
    <script src="../../config-general/assets/js/app.js"></script>
    <script src="../../config-general/assets/js/layout.js"></script>
    <script src="../../config-general/assets/js/theme-color.js"></script>
    <!-- notifications -->
    <script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
    <!-- Material -->
    <script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js"></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js"></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
    <!-- end js include path -->
    <script src="../ckeditor/ckeditor.js"></script>

    <script>
        // Replace the <textarea id="editor1"> with a CKEditor 4
        // instance, using default configuration.
        CKEDITOR.replace( 'editor1' );
    </script>
    </body>
</html>