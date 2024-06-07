<?php
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

require_once '../class/UsuariosPadre.php';

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
} ?>
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />


<div class="panel">
    <header class="panel-heading panel-heading-purple">Transferir Cargas </header>
    <div class="panel-body">


        <form name="formularioGuardar" action="cargas-transferir.php" method="post">

            <div class="form-group row">
                <label class="col-sm-2 control-label">Del Docente</label>
                <div class="col-sm-4">
                    <select style="width: 100%" class="form-control  select2" name="de" required <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una opción</option>
                        <?php
                        $docen = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=".TIPO_DOCENTE);
                        while ($nDocen = mysqli_fetch_array($docen, MYSQLI_BOTH)) {
                            echo "<option value='" . $nDocen["uss_id"] . "'>" . UsuariosPadre::nombreCompletoDelUsuario($nDocen) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Al Docente</label>
                <div class="col-sm-4">
                    <select  style="width: 100%" class="form-control  select2" name="para" required <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una opción</option>
                        <?php
                        $docen = $docen = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=".TIPO_DOCENTE);
                        while ($nDocen = mysqli_fetch_array($docen, MYSQLI_BOTH)) {
                            echo "<option value='" . $nDocen["uss_id"] . "'>" . UsuariosPadre::nombreCompletoDelUsuario($nDocen) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <?php $botones = new botonesGuardar("",Modulos::validarPermisoEdicion()); ?>
        </form>
    </div>
</div>


<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>