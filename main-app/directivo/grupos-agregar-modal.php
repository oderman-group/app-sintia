<?php $idPaginaInterna = 'DT0196'; ?>
<?php 

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
} ?>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="panel">
                <header class="panel-heading panel-heading-purple"><?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </header>
                <div class="panel-body">
                    <form name="formularioGuardar" action="grupos-guardar.php" method="post">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Codigo Gupo <span style="color: red;">(*)</span></label>
                            <div class="col-sm-10">
                                <input type="number" name="codigoG" class="form-control" required <?= $disabledPermiso; ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Nombre Gupo <span style="color: red;">(*)</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="nombreG" class="form-control" required <?= $disabledPermiso; ?>>
                            </div>
                        </div>

                        <?php  
                        $botones = new botonesGuardar(null,Modulos::validarPermisoEdicion()); ?>
                    </form>
                </div>
            </div>
</body>

</html>