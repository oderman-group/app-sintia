<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0283'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH . "/main-app/class/Movimientos.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
}
$idRecurrente=Utilidades::generateCode("EV");
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
    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title"><?= $frases[56][$datosUsuarioActual['uss_idioma']]; ?> <?= $frases[114][$datosUsuarioActual['uss_idioma']]; ?></div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="evaluaciones.php" onClick="deseaRegresar(this)"><?= $frases[114][$datosUsuarioActual['uss_idioma']]; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active"><?= $frases[56][$datosUsuarioActual['uss_idioma']]; ?> <?= $frases[114][$datosUsuarioActual['uss_idioma']]; ?></li>
                        </ol>
                    </div>
                </div>
                <div class="row">

                    <div class="col-sm-12">
                        <?php
                        include("../../config-general/mensajes-informativos.php");
                        ?>
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple"><?= $frases[114][$datosUsuarioActual['uss_idioma']]; ?> </header>
                            <div class="panel-body">


                                <form name="formularioGuardar" action="evaluacion-guardar.php" method="post">
                                <input type="hidden" value="<?=$idRecurrente;?>" name="id" id="idTransaction">
                                    <div class="form-group row">
                                        <label class="col-sm-1 control-label"><?= $frases[187][$datosUsuarioActual['uss_idioma']]; ?><span style="color: red;">(*)</span></label>
                                        <div class="col-sm-11">
                                            <input type="text" name="nombre" required class="form-control" <?= $disabledPermiso; ?>>
                                        </div>
                                        
                                        

                                    </div>
                                    <div class="form-group row">
                                        
                                        <div class="col-sm-2">
                                        <label class="control-label"><?= $frases[51][$datosUsuarioActual['uss_idioma']]; ?><span style="color: red;">(*)</span> <i class="fa fa-question"></i></button></label>
                                            <input type="date" name="fecha" class="form-control" autocomplete="off" required value="<?= date('Y-m-d'); ?>" <?= $disabledPermiso; ?>>
                                        </div>
                                        
                                        <div class="col-sm-2">
                                        <label class="control-label">Obligatoria</label>
                                            <select class="form-control  select2" name="obligatoria" <?= $disabledPermiso; ?>>
                                                <option value="1"><?= $frases[275][$datosUsuarioActual['uss_idioma']]; ?></option>
                                                <option value="0"><?= $frases[276][$datosUsuarioActual['uss_idioma']]; ?></option>
                                            </select>
                                        </div>
                                       
                                        <div class="col-sm-2">
                                        <label class="control-label">Visible</label>
                                            <select class="form-control  select2" name="visible" <?= $disabledPermiso; ?>>
                                                <option value="1"><?= $frases[275][$datosUsuarioActual['uss_idioma']]; ?></option>
                                                <option value="0"><?= $frases[276][$datosUsuarioActual['uss_idioma']]; ?></option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                        <label class="control-label">Clave</label>
                                            <input type="text" name="clave" required class="form-control" <?= $disabledPermiso; ?>>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-sm-12 control-label"><?= $frases[50][$datosUsuarioActual['uss_idioma']]; ?></label>
                                        <div class="col-sm-12">
                                            <textarea cols="80" id="editor1" name="descripcion" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?= $disabledPermiso; ?>></textarea>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <a href="javascript:void(0);" name="evaluaciones.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i><?= $frases[184][$datosUsuarioActual['uss_idioma']]; ?></a>
                                        <?php if (Modulos::validarPermisoEdicion()) { ?>
                                            <button type="submit" class="btn  btn-info">
                                                <i class="fa fa-save" aria-hidden="true"></i> <?= $frases[419][$datosUsuarioActual['uss_idioma']]; ?>
                                            </button>
                                        <?php } ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <?php include("../compartido/publicidad-lateral.php"); ?>
                    </div>

                </div>

            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");
            ?>
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
        CKEDITOR.replace('editor1');
    </script>
    </body>


    </html>