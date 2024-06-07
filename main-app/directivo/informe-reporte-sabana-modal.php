<?php
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
?>

<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<div class="panel">
    <header class="panel-heading panel-heading-purple">POR CURSO </header>
    <div class="panel-body">
        <form name="formularioGuardar" action="../compartido/reportes-sabanas.php" method="post" target="_blank">
            <div class="form-group row">
                <label class="col-sm-2 control-label">Curso</label>
                <div class="col-sm-8">
                    <select class="form-control  select2" style="width: 100%;" name="curso" required>
                        <option value="">Seleccione una opción</option>
                        <?php
                        $opcionesConsulta = Grados::traerGradosInstitucion($config);
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                            $disabled = '';
                            if ($opcionesDatos['gra_estado'] == '0') $disabled = 'disabled';
                        ?>
                            <option value="<?= $opcionesDatos['gra_id']; ?>" <?= $disabled; ?>><?= $opcionesDatos['gra_id'] . ". " . strtoupper($opcionesDatos['gra_nombre']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Grupo</label>
                <div class="col-sm-4">
                    <select class="form-control  select2" style="width: 100%;" name="grupo">
                        <option value="">Seleccione una opción</option>
                        <?php
                        $opcionesConsulta = Grupos::traerGrupos($conexion, $config);
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $opcionesDatos['gru_id']; ?>"><?= $opcionesDatos['gru_id'] . ". " . strtoupper($opcionesDatos['gru_nombre']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>


            <div class="form-group row">
                <label class="col-sm-2 control-label">Periodo</label>
                <div class="col-sm-4">
                    <select class="form-control  select2" style="width: 100%;" name="per" required>
                        <option value="">Seleccione una opción</option>
                        <?php
                        $p = 1;
                        while ($p <= $config[19]) {
                            echo '<option value="' . $p . '">Periodo ' . $p . '</option>';
                            $p++;
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Año</label>
                <div class="col-sm-4">
                    <select class="form-control  select2" style="width: 100%;" name="year" required>
                        <option value="">Seleccione una opción</option>
                        <?php
                        while ($yearStart <= $yearEnd) {
                            if ($_SESSION["bd"] == $yearStart)
                                echo "<option value='" . $yearStart . "' selected style='color:blue;'>" . $yearStart . "</option>";
                            else
                                echo "<option value='" . $yearStart . "'>" . $yearStart . "</option>";
                            $yearStart++;
                        }
                        ?>
                    </select>
                </div>
            </div>

            <input type="submit" class="btn btn-primary" value="Generar Sabana">&nbsp;

        </form>
    </div>
</div>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>