<?php
include("session.php");

$idPaginaInterna = 'DV0036';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");

try{
    $consulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".seguridad_historial_acciones
    INNER JOIN ".$baseDatosServicios.".instituciones ON ins_id=hil_institucion AND ins_enviroment='".ENVIROMENT."'
    LEFT JOIN ".$baseDatosServicios.".paginas_publicidad ON pagp_id=hil_titulo
    WHERE hil_id='".base64_decode($_GET['id'])."'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$datosHistorial = mysqli_fetch_array($consulta, MYSQLI_BOTH);

$responsable="";
if($datosHistorial['hil_usuario']!=0){
    $consultaResponsable = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$datosHistorial['hil_usuario']."'", $datosHistorial['ins_id'], $agnoBD);
    $numDatosResponsable=mysqli_num_rows($consultaResponsable);
    if($numDatosResponsable>0){
        $datosResponsable = mysqli_fetch_array($consultaResponsable, MYSQLI_BOTH);
        $responsable=UsuariosPadre::nombreCompletoDelUsuario($datosResponsable)."(".$datosResponsable['pes_nombre'].")";
    }
}

$ussAutologin="NO";
if($datosHistorial['hil_usuario_autologin']!=0){
    $consultaUssAutologin = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$datosHistorial['hil_usuario_autologin']."'", $datosHistorial['ins_id'], $agnoBD);
    $numUssAutologin=mysqli_num_rows($consultaUssAutologin);
    $ussAutologin="Usuario no encontrado";
    if($numUssAutologin>0){
        $datosUssAutologin = mysqli_fetch_array($consultaUssAutologin, MYSQLI_BOTH);
        $ussAutologin=UsuariosPadre::nombreCompletoDelUsuario($datosUssAutologin)."(".$datosUssAutologin['pes_nombre'].")";
    }
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
                            <div class="page-title">Historial de Acciones Detalles</div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="dev-historial-acciones.php" onClick="deseaRegresar(this)">Historial de acciones</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Historial de Acciones Detalles</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple">Detalles</header>
                            <div class="panel-body">
                                <form name="formularioGuardar">

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">ID Reporte</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" value="<?= $datosHistorial['hil_id']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Fecha Reporte</label>
                                        <div class="col-sm-4">
                                            <input type="datetime" class="form-control" value="<?= $datosHistorial['hil_fecha']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Institución</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" value="<?= $datosHistorial['ins_siglas']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Usuario Responsable</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" value="<?= $responsable; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Autologin</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" value="<?= $ussAutologin; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Pagina</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" value="<?= $datosHistorial['pagp_pagina']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">URL Pagina</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" value="<?= $datosHistorial['hil_url']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Tiempo de Carga</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" value="<?= $datosHistorial['hil_tiempo_carga']; ?> S" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">URL Procedencia</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" value="<?= $datosHistorial['hil_pagina_anterior']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">IP</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" value="<?= $datosHistorial['hil_ip']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Sistema Operativo</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" value="<?= $datosHistorial['hil_so']; ?>" readonly>
                                        </div>
                                    </div>
                                    <?php $botones = new botonesGuardar("dev-historial-acciones.php",false); ?>
                                    
                                </form>
                            </div>
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

    <!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

    </html>