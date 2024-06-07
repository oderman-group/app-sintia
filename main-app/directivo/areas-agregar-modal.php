<?php
require_once(ROOT_PATH."/main-app/class/Areas.php");
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
} ?>

<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="panel">
    <header class="panel-heading panel-heading-purple"><?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </header>
    <div class="panel-body">


        <form name="formularioGuardar" action="areas-guardar.php" method="post" enctype="multipart/form-data">

            <div class="form-group row">
                <label class="col-sm-2 control-label">Nombre del Área <span style="color: red;">(*)</span></label>
                <div class="col-sm-10">
                    <input type="text" name="nombreA" class="form-control" required <?= $disabledPermiso; ?>>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Orden o posición en los informes</label>
                <div class="col-sm-10">
                    <select class="form-control  select2" name="posicionA" required <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una opción</option>
                        <?php
                        $c_posicionA = Areas::traerAreasInstitucion($config);
                        $numDatos = mysqli_num_rows($c_posicionA);
                        $cont = 0;
                        while ($r_pos = mysqli_fetch_array($c_posicionA, MYSQLI_BOTH)) {
                            $cont++;
                            $posciones[$cont] = $r_pos["ar_posicion"];
                        }
                        $cond = 0;
                        for ($i = 1; $i <= (20 + $cond); $i++) {

                            $exist = 0;
                            if ($numDatos > 0) {
                                for ($j = 0; $j <= count($posciones); $j++) {
                                    if ($i == $posciones[$j]) {
                                        $exist = 1;
                                    }
                                }
                            }
                            if ($exist != 1) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            } else {
                                $cond++;
                            }
                        }
                        ?>
                    </select>
                    <span style="color: #6017dc;">Este número se usa para mostrar en una posición específica el área en los informes de la institución.</span>
                </div>
            </div>


            <?php $botones = new botonesGuardar(null,Modulos::validarPermisoEdicion()); ?>
        </form>
    </div>
    </body>

    <!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

    </html>