<?php include("session.php"); ?>
<?php
$_SESSION["bd"] = 2022;
?>
<?php include("verificar-usuario.php"); ?>
<?php include("verificar-sanciones.php"); ?>
<?php $idPaginaInterna = 'ES0051'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php"); ?>

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
                            <div class="page-title">Firmar matrícula</div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>
                <div class="row">



                    <div class="col-sm-9">
                        <div class="card card-box">
                            <div class="card-head">
                                <header>Firmar matrícula</header>
                            </div>
                            <div class="card-body " id="bar-parent6">





                                <p>En este espacio puede adjuntar la firma digital, si no tiene firma digital puede realizar la firma en un papel blanco sin l&iacute;neas, ni cuadriculas; tomarle una foto con buena iluminaci&oacute;n y subirla a este medio.</p>



                                <form action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="25">

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Adjuntar firma</label>
                                        <div class="col-sm-4">
                                            <input type="file" name="archivo" class="form-control">
                                        </div>
                                        <div class="col-sm-4">
                                            <a href="../files/comprobantes/<?= $datosEstudianteActual["mat_firma_adjunta"]; ?>" target="_blank"><?= $datosEstudianteActual["mat_firma_adjunta"]; ?></a>
                                        </div>
                                    </div>



                                    <?php if ($datosEstudianteActual["mat_mayores14"] == 1 and $datosEstudianteActual["mat_hoja_firma"] == '0') { ?>
                                        <input type="submit" class="btn btn-primary" value="Enviar firma y finalizar proceso">&nbsp;
                                    <?php } ?>

                                </form>


                                <?php if ($datosEstudianteActual["mat_hoja_firma"] == 1 and $datosEstudianteActual["mat_estado_matricula"] == 4) { ?>
                                    <p align="center" style="margin-top:50px; font-size: 20px;">
                                        NOTA: Una vez la secretaria académica verifique y apruebe la matrícula le aparecerá el comprobante final.
                                    </p>
                                <?php } ?>

                                <?php if ($datosEstudianteActual["mat_hoja_firma"] == 1 and $datosEstudianteActual["mat_estado_matricula"] == 1) { ?>
                                    <p align="center" style="margin-top:50px;">
                                        <a href="comprobante-matricula.php?" style="text-decoration: underline; font-weight: bold; color:green;" target="_Blank">DESCARGAR COMPROBANTE</a>
                                    </p>
                                <?php } ?>


                                
                                    <!--
                                    <p align="center" style="margin-top:50px;">
                                        <a href="../files/lista_utiles_2022.pdf" style="text-decoration: underline; font-weight: bold; color:green;" target="_Blank">DESCARGAR LISTA DE ÚTILES ESCOLARES</a>
                                    </p>
                                -->
                                
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">

                        <?php include("../compartido/matricula-pasos.php"); ?>

                        <?php include("../compartido/modulo-frases-lateral.php"); ?>

                        <?php //include("../compartido/publicidad-lateral.php"); 
                        ?>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page content -->
        <?php // include("../compartido/panel-configuracion.php"); ?>
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
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

</html>