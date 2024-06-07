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

<div class="panel">
    <header class="panel-heading panel-heading-purple"><?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </header>
    <div class="panel-body">


        <form name="formularioGuardar" action="asignaturas-guardar.php" method="post" enctype="multipart/form-data">

            <div class="form-group row">
                <label class="col-sm-2 control-label">Nombre de la Asignatura <span style="color: red;">(*)</span></label>
                <div class="col-sm-8">
                    <input type="text" name="nombreM" required class="form-control" onchange="generarSiglas(this)" <?= $disabledPermiso; ?>>
                </div>
            </div>

            <script type="text/javascript">
                function generarSiglas(datos) {
                    var asignatura = datos.value;
                    var siglas = asignatura.substring(0, 3);
                    document.getElementById("siglasM").value = siglas.toUpperCase();
                }
            </script>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Nombre corto, Abreviatura o Siglas de la asignatura <span style="color: red;">(*)</span></label>
                <div class="col-sm-4">
                    <input type="text" name="siglasM" id="siglasM" required class="form-control" <?= $disabledPermiso; ?>>
                    <span style="color: #6017dc;">Este valor se usa para mostrar de forma abreviada el nombre de la asignatura en algunos informes.</span>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Área académica a la cual pertenece esta asignatura <span style="color: red;">(*)</span></label>
                <div class="col-sm-8">
                    <select class="form-control  select2" name="areaM" required <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una opción</option>
                        <?php
                        $cAreas = Areas::traerAreasInstitucion($config);
                        while ($rA = mysqli_fetch_array($cAreas, MYSQLI_BOTH)) {
                            echo '<option value="' . $rA["ar_id"] . '">' . $rA["ar_nombre"] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Sumar en promedio general?</label>
                <div class="col-sm-8">
                    <select class="form-control  select2" name="sumarPromedio" <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una opción</option>
                        <option value="<?=SI?>"><?=SI?></option>
                        <option value="<?=NO?>"><?=NO?></option>
                    </select>
                    <span style="color: #6017dc;">Deseas que esta asignatura cuente en la suma del promedio general en los informes?.</span>
                </div>
            </div>

            <?php if ($config['conf_agregar_porcentaje_asignaturas'] == 'SI') { ?>
                <div class="form-group row">
                    <label class="col-sm-2 control-label">Porcentaje</label>
                    <div class="col-sm-4">
                        <input type="text" name="porcenAsigna" id="porcenAsigna" class="form-control" <?= $disabledPermiso; ?>>
                    </div>
                </div>
            <?php } ?>


           <?php $botones = new botonesGuardar(null,Modulos::validarPermisoEdicion()); ?>
        </form>
    </div>
</div>