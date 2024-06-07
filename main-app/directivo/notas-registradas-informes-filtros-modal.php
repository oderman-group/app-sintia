<?php
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
} ?>

<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="panel">
    <header class="panel-heading panel-heading-purple">POR CURSO </header>
    <div class="panel-body">
    <form name="formularioGuardar" action="../compartido/reporte-notas.php" method="post" target="_blank">

        <div class="form-group row">
            <label class="col-sm-2 control-label">Curso</label>
            <div class="col-sm-8">
                <select class="form-control  select2" style="width: 810.666px;" name="grado" id="grado" required onchange="habilitarGrupoPeriodo()">
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
                <select class="form-control  select2" style="width: 810.666px;" id="grupo" name="grupo" onchange="traerCargas()" disabled>
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
                <select class="form-control  select2" style="width: 810.666px;" onchange="traerCargas()" name="per" id="periodo" required disabled>
                    <option value="">Seleccione una opción</option>
                    <?php
                    $p = 1;
                    while ($p <= $config[19]) {
                        echo '<option value="' . $p . '">Periodo ' . $p . '</option>';
                        $p++;
                    }
                    ?>
                </select>
                <span id="mensaje" style="color: #6017dc; display:none;">Espere un momento por favor.</span>
            </div>
        </div>

        <div class="form-group row" id="carga-container">
            <label class="col-sm-2 control-label">Carga</label>
            <div class="col-sm-8">
                <select class="form-control  select2" style="width: 810.666px;" name="carga" id="carga" required>
                </select>
                <script type="application/javascript">
                    $(document).ready(traerCargas(document.getElementById('grupo')));

                    function habilitarGrupoPeriodo() {
                        var curso = document.getElementById('grado').value;
                        var grupo = document.getElementById('grupo');
                        var periodo = document.getElementById('periodo');

                        if (curso) {
                            grupo.removeAttribute('disabled');
                            periodo.removeAttribute('disabled');
                            traerCargas(grupo);
                        } else {
                            periodo.setAttribute('disabled', true);
                            grupo.setAttribute('disabled', true);
                            $('#carga-container').hide();
                        }
                    }

                    function traerCargas(enviada) {
                        var grado = $('#grado').val();
                        var grupo = $('#grupo').val();
                        var periodo = $('#periodo').val();
                        if (grado === "" || grupo === "") {
                            $('#carga-container').hide();
                            return;
                        }

                        datos = "grado=" + (grado) +
                            "&grupo=" + (grupo) +
                            "&periodo=" + (periodo);
                        console.log(datos);
                        $('#mensaje').show();
                        $.ajax({
                            type: "POST",
                            url: "ajax-traer-cargas.php",
                            data: datos,
                            success: function(response) {
                                $('#mensaje').hide();
                                $('#carga-container').show();
                                $('#carga').empty();
                                $('#carga').append(response);
                            }
                        });
                    }
                </script>

            </div>
        </div>


        <input type="submit" class="btn btn-primary" value="Generar informe">&nbsp;
    </form>

    </div>
</div>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>