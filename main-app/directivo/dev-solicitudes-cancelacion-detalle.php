<?php
include("session.php");

$idPaginaInterna = 'DV0039';

include("../compartido/historial-acciones-guardar.php");
Modulos::verificarPermisoDev();
include("../compartido/head.php");

require_once("../class/Solicitudes.php");
$solicitudActual = Solicitudes::consultar(base64_decode($_GET["id"]));


?>
<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
                            <div class="page-title">Detalle solicitud de cancelacion</div>
                            <?php include("../../config-general/mensajes-informativos.php"); ?>
                        </div>
                    </div>
                </div>

                <div class="panel">
                            <header class="panel-heading panel-heading-purple">Detalle</header>
                            <div class="panel-body">
                                <form action="solicitud-cancelacion-actualizar.php" method="post" enctype="multipart/form-data">
                                    <i class="bi bi-eye-slash"></i>
                                    <div class="form-group row">
                                        <label class="col-sm-2 ">ID Reporte:</label>
                                        <div class="col-sm-1">
                                            <input type="text" name="id" class="form-control" value="<?= $solicitudActual['solcan_id']; ?>" readonly>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="material-icons">date_range</i></span>
                                                </div>
                                                <input type="datetime" class="form-control" value="<?= $solicitudActual['solcan_fecha_creacion']; ?>" readonly>
                                            </div>

                                        </div>
                                    </div>



                                    <div class="form-group row">
                                        <label class="col-sm-2 ">Institución:</label>
                                        
                                        <div class="col-sm-4">
                                            <input type="text" name="ins_id" class="form-control" value="<?= $solicitudActual['ins_id']; ?>" readonly hidden>
                                            <input type="text" name="ins_contacto" class="form-control" value="<?= $solicitudActual['ins_contacto_principal']; ?>" readonly>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-mobile-phone"></i></span>
                                                </div>
                                                <input type="text" class="form-control" value="<?= $solicitudActual['ins_celular_contacto']; ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="material-icons">email</i></span>
                                                </div>
                                                <input type="text" name="ins_email_contacto" class="form-control" value="<?= $solicitudActual['ins_email_contacto']; ?>" readonly>
                                            </div>
                                        </div>

                                    </div>



                                    <div class="form-group row">
                                        <label class="col-sm-2 ">Usuario que solicita: </label>
                                        <div class="col-sm-4">
                                            <input type="text" name="uss_id" class="form-control" value="<?= $solicitudActual['uss_id']; ?>" readonly hidden>
                                            <input type="text" class="form-control" value="<?= $solicitudActual['uss_nombre']; ?> " readonly>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-mobile-phone"></i></span>
                                                </div>
                                                <input type="text" class="form-control" value="<?= $solicitudActual['uss_celular']; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="material-icons">email</i></span>
                                                </div>
                                                <input type="text" class="form-control" value="<?= $solicitudActual['uss_email']; ?>" readonly>
                                            </div>

                                        </div>
                                    </div>




                                    <div class="form-group row">
                                        <label class="col-sm-2 ">Motivo de cancelacion:</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" readonly rows="3">
                                              <?= $solicitudActual['solcan_motivo']; ?>
                                             </textarea>
                                        </div>
                                    </div>



                                    <div class="form-group row">
                                        <label class="col-sm-2 ">Respuesta:</label>
                                        <div class="col-sm-10">
                                            <textarea cols="80" id="editor1" name="respuesta" rows="10" required><?= $solicitudActual['solcan_respuesta']; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 ">Estado: </label>
                                        <div class="col-sm-6">
                                            <select class="form-control" name="estado">

                                                <option value="<?= SOLICITUD_CANCELACION_PENDIENTE ?>" <?php if (
                                                                                                            $solicitudActual['solcan_estado'] != SOLICITUD_CANCELACION_APROBADO
                                                                                                            && $solicitudActual['solcan_estado'] != SOLICITUD_CANCELACION_CANCELADO
                                                                                                        ) {
                                                                                                            echo "selected";
                                                                                                        } ?>>Pendiente</option>
                                                <option value="<?= SOLICITUD_CANCELACION_APROBADO ?>" <?php if ($solicitudActual['solcan_estado'] == SOLICITUD_CANCELACION_APROBADO) {
                                                                                                            echo "selected";
                                                                                                        } ?>>Apropado</option>
                                                <option value="<?= SOLICITUD_CANCELACION_CANCELADO ?>" <?php if ($solicitudActual['solcan_estado'] == SOLICITUD_CANCELACION_CANCELADO) {
                                                                                                            echo "selected";
                                                                                                        } ?>>Cancelado</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class=" col-md-9">
                                            <?php $botones = new botonesGuardar("dev-solicitudes-cancelacion.php",true,"Actualizar solicitud"); ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
            </div>
        </div>
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
