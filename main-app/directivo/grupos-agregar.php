<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0196'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php"); 

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}?>
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
                            <div class="page-title"><?=$frases[231][$datosUsuarioActual['uss_idioma']]." ". $frases[254][$datosUsuarioActual['uss_idioma']]; ?></div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <?php include("../../config-general/mensajes-informativos.php"); ?>
                                <div class="panel">
                                    <header class="panel-heading panel-heading-purple"><?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </header>
                                    <div class="panel-body">
                                        <form name="formularioGuardar" action="grupos-guardar.php" method="post">
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Codigo Gupo <span style="color: red;">(*)</span></label>
                                                <div class="col-sm-10">
                                                    <input type="number"  name="codigoG" class="form-control" required <?=$disabledPermiso;?>>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Nombre Gupo <span style="color: red;">(*)</span></label>
                                                <div class="col-sm-10">
                                                    <input type="text"  name="nombreG" class="form-control" required <?=$disabledPermiso;?>>
                                                </div>
                                            </div>

                                            <?php $botones = new botonesGuardar("grupos.php",Modulos::validarPermisoEdicion()); ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
        <!-- data tables -->
        <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
        <script src="../../config-general/assets/js/pages/table/table_data.js"></script>
        <!-- Common js-->
        <script src="../../config-general/assets/js/app.js"></script>
        <script src="../../config-general/assets/js/layout.js"></script>
        <script src="../../config-general/assets/js/theme-color.js"></script>
        <!-- notifications -->
        <script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
        <script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
        <!-- Material -->
        <script src="../../config-general/assets/plugins/material/material.min.js"></script>
        <!-- end js include path -->
        </body>

        </html>