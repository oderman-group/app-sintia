<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0283'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
}
$idRecurrente = Utilidades::generateCode("PRG");
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
                            <div class="page-title"><?= $frases[56][$datosUsuarioActual['uss_idioma']]; ?> <?= $frases[139][$datosUsuarioActual['uss_idioma']]; ?></div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="preguntas.php" onClick="deseaRegresar(this)"><?= $frases[139][$datosUsuarioActual['uss_idioma']]; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active"><?= $frases[56][$datosUsuarioActual['uss_idioma']]; ?> <?= $frases[139][$datosUsuarioActual['uss_idioma']]; ?></li>
                        </ol>
                    </div>
                </div>
                <div class="row">

                    <div class="col-sm-12">
                        <?php
                        include("../../config-general/mensajes-informativos.php");
                        ?>
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple"><?= $frases[139][$datosUsuarioActual['uss_idioma']]; ?> </header>
                            <div class="panel-body">


                                <form name="formularioGuardar" action="pregunta-guardar.php" method="post">
                                    <input type="hidden" value="<?= $idRecurrente; ?>" name="id" id="idTransaction">
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label"><?= $frases[50][$datosUsuarioActual['uss_idioma']]; ?><span style="color: red;">(*)</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" row="2" name="descripcion" required class="form-control" <?= $disabledPermiso; ?>>
                                        </div>



                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label"><?=$frases[53][$datosUsuarioActual['uss_idioma']];?> <?=$frases[139][$datosUsuarioActual['uss_idioma']];?></label>
                                        <div class="col-sm-2">
                                            <select class="form-control  select2" name="tipo_pregunta" <?= $disabledPermiso; ?>>
                                                <option value="<?=TEXT?>"><?=$frases[421][$datosUsuarioActual['uss_idioma']];?></option>
                                                <option value="<?=MULTIPLE?>"><?=$frases[422][$datosUsuarioActual['uss_idioma']];?></option>
                                                <option value="<?=SINGLE?>"><?=$frases[423][$datosUsuarioActual['uss_idioma']];?></option>
                                            </select>
                                        </div>



                                    </div>
                                    <div class="form-group row">                                        
                                        <div class="col-sm-2 card-head" style=" border-bottom: 0px rgba(0, 0, 0, 0.2);">
                                            <header>
                                                <label class="switchToggle">
                                                    <input name="visible" type="checkbox" checked >
                                                    <span class="slider green round"></span>
                                                </label>
                                            </header>
                                            Visible
                                        </div>
                                        <div class="col-sm-2 card-head" style=" border-bottom: 0px rgba(0, 0, 0, 0.2);">
                                            <header>
                                                <label class="switchToggle">
                                                    <input name="obligatoria" type="checkbox"  >
                                                    <span class="slider green round"></span>
                                                </label>
                                            </header>
                                            <?=$frases[420][$datosUsuarioActual['uss_idioma']];?>
                                        </div>
                                    </div>
                                    
                                    <div class="text-left">
                                        <?php $botones = new botonesGuardar("preguntas.php",Modulos::validarPermisoEdicion()); ?>
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