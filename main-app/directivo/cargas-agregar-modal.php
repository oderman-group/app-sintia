<?php

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
    <header class="panel-heading panel-heading-purple"><?= $frases[119][$datosUsuarioActual[8]]; ?> </header>
    <div class="panel-body">


        <form name="formularioGuardar" action="cargas-guardar.php" method="post">

            <div class="form-group row">
                <label class="col-sm-2 control-label">Docente <span style="color: red;">(*)</span></label>
                <div class="col-sm-8">
                    <?php
                    $opcionesConsulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo = ".TIPO_DOCENTE." ORDER BY uss_nombre");
                    ?>
                    <select id="selectDocentes" class="form-control  select2" style="width: 100%" name="docente" required  multiple   <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una opción</option>
                        <?php
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                            $disabled = '';
                            if ($opcionesDatos['uss_bloqueado'] == 1) $disabled = 'disabled';
                        ?>
                            <option value="<?= $opcionesDatos[0]; ?>" <?= $disabled; ?>><?= $opcionesDatos['uss_usuario'] . " - " . UsuariosPadre::nombreCompletoDelUsuario($opcionesDatos); ?></option>
                        <?php } ?>
                    </select>
                    <span style="color: darkblue;">Seleccione solo una opción de este listado.</span>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Curso <span style="color: red;">(*)</span></label>
                <div class="col-sm-8">
                    <?php
                    try {
                        $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM academico_grados ORDER BY gra_vocal");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    ?>
                    <select id="multiple1" class="form-control  select2-multiple" style="width: 100%" name="curso[]" required multiple <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una opción</option>
                        <?php
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                            $disabled = '';
                            if ($opcionesDatos['gra_estado'] == '0') $disabled = 'disabled';
                        ?>
                            <option value="<?= $opcionesDatos[0]; ?>" <?= $disabled; ?>><?= $opcionesDatos['gra_id'] . ". " . strtoupper($opcionesDatos['gra_nombre']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Grupo <span style="color: red;">(*)</span></label>
                <div class="col-sm-8">
                    <?php
                    try {
                        $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM academico_grupos");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    ?>
                    <select id="multiple" class="form-control select2-multiple" style="width: 100%" name="grupo[]" required multiple <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una opción</option>
                        <?php
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $opcionesDatos[0]; ?>"><?= $opcionesDatos['gru_id'] . ". " . strtoupper($opcionesDatos['gru_nombre']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Asignatura (Área) <span style="color: red;">(*)</span></label>
                <div class="col-sm-8">
                    <?php
                    try {
                        $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM academico_materias
                                                    INNER JOIN academico_areas ON ar_id=mat_area ORDER BY mat_nombre");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    ?>
                    <select  class="form-control  select2-multiple" style="width: 100%" name="asignatura[]" required multiple <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una opción</option>
                        <?php
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $opcionesDatos[0]; ?>"><?= $opcionesDatos['mat_id'] . ". " . strtoupper($opcionesDatos['mat_nombre'] . " (" . $opcionesDatos['mat_valor'] . "%) (" . $opcionesDatos['ar_nombre'] . ")"); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Periodo <span style="color: red;">(*)</span></label>
                <div class="col-sm-4">
                    <select class="form-control  select2" style="width: 100%"   name="periodo" required <?= $disabledPermiso; ?>>
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
                <label class="col-sm-2 control-label">Director de grupo <span style="color: red;">(*)</span></label>
                <div class="col-sm-4">
                    <select class="form-control  select2" name="dg" required <?= $disabledPermiso; ?>>
                        <option value="0">Seleccione una opción</option>
                        <option value="1">SI</option>
                        <option value="0" selected>NO</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Intensidad H. <span style="color: red;">(*)</span></label>
                <div class="col-sm-2">
                    <input type="text" name="ih" class="form-control" <?= $disabledPermiso; ?>>
                </div>
            </div>

            <div style="display:none">
                <hr>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Max. Indicadores</label>
                    <div class="col-sm-2">
                        <input type="text" name="maxIndicadores" class="form-control" value="10" <?= $disabledPermiso; ?>>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Max. Actividades</label>
                    <div class="col-sm-2">
                        <input type="text" name="maxActividades" class="form-control" value="100" <?= $disabledPermiso; ?>>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Estado</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="estado" required <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opción</option>
                            <option value="1" selected>Activa</option>
                            <option value="0">Inactiva</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">% Actividades</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="valorActividades" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opción</option>
                            <option value="1">Manual</option>
                            <option value="0" selected>Automático</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">% Indicadores</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="valorIndicadores" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opción</option>
                            <option value="1">Manual</option>
                            <option value="0" selected>Automático</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Permiso para generar informe</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="permiso1" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opción</option>
                            <option value="1" selected>SI</option>
                            <option value="0">NO</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Permiso para editar en periodos anteriores</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="permiso2" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opción</option>
                            <option value="1">SI</option>
                            <option value="0" selected>NO</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Indicador automático </label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="indicadorAutomatico" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opción</option>
                            <option value="1">SI</option>
                            <option value="0" selected>NO</option>
                        </select>

                        <span class="text-info">Si selecciona SI, el docente no llenará indicadores; solo las calificaciones. Habrá un solo indicador definitivo con el 100%.</span>

                    </div>

                </div>

            </div>


            <?php if (Modulos::validarPermisoEdicion()) { ?>
                <button type="submit" class="btn  btn-info">
                    <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
                </button>
            <?php } ?>

        </form>
    </div>
</div>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>

<script>
// Agregar el evento onchange al select
var miSelect = document.getElementById('selectDocentes');
miSelect.onchange = function() {
    limitarSeleccion(this);
};
</script>