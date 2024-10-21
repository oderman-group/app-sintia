<?php $idPaginaInterna = 'DT0100';
require_once(ROOT_PATH . "/main-app/class/Grupos.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
require_once("../class/Estudiantes.php");
?>

<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<style>
    .select2 {
        width: 100% !important;
    }
    .modal {
    z-index: 1050 !important;
    outline: 0;
    overflow-y: auto!important
}
</style>

<!-- END HEAD -->
<div class="col-sm-12">
    <?php include("../../config-general/mensajes-informativos.php"); ?>
    <div class="panel">
        <header class="panel-heading panel-heading-purple">POR CURSO </header>
        <div class="panel-body">
            <form name="formularioGuardar" id="formularioGuardar1" action="informes-formato-boletin.php" method="post" onsubmit="return ejecutarAntesDeEnviar1();" target="_blank">
                <div class="form-group row">
                    <label class="col-sm-2 control-label">Escoja un Formato de Boletín</label>
                    <div class="col-sm-2">
                        <select id="tipoBoletin" class="form-control  select2" name="formatoB" onchange="cambiarTipo()">
                            <option value="">Seleccione una opción</option>
                            <?php
                            try {
                                $consultaBoletin = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".opciones_generales WHERE ogen_grupo=15");
                            } catch (Exception $e) {
                                include("../compartido/error-catch-to-report.php");
                            }
                            while ($datosBoletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH)) {
                            ?>
                                <option <?php if ($config['conf_formato_boletin'] == $datosBoletin['ogen_id']) {
                                            echo "selected";
                                        } ?> value="<?= $datosBoletin['ogen_id']; ?>"><?= $datosBoletin['ogen_nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="button" title="Ver formato del boletin" class="btn btn-sm" data-toggle="popover"><i class="fa fa-eye"></i></button>
                    <script>
                        $(document).ready(function() {
                            $('[data-toggle="popover"]').popover({
                                html: true, // Habilitar contenido HTML
                                content: function() {
                                    valor = document.getElementById("tipoBoletin");
                                    vacio = valor.value === null || valor.value === undefined || valor.value.trim() === '';
                                    if (!vacio) {
                                        return '<div id="myPopover" class="popover-content"><label id="lbl_tipo">Formato tipo ' + valor.value + '</label>' +
                                            '<img id="img-boletin" src="../files/images/boletines/tipo' + valor.value + '.png" class="w-100" />' +
                                            '</div>';
                                    } else {
                                        return '<div id="myPopover" class="popover-content"><label id="lbl_tipo">Seleccione un tipo de formato.</label>' +

                                            '</div>';
                                    }

                                }
                            });
                        });

                        function cambiarTipo() {
                            var imagen_boletin = document.getElementById('img-boletin');
                            if (imagen_boletin) {
                                var valor = document.getElementById("tipoBoletin");
                                var lbl_tipo = document.getElementById('lbl_tipo');
                                imagen_boletin.src = "../files/images/boletines/tipo" + valor.value + ".png";
                                lbl_tipo.textContent = 'Formato tipo ' + valor.value;
                            }
                        }
                    </script>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Curso</label>
                    <div class="col-sm-8">
                        <select class="form-control  select2" id="curso" name="curso" required>
                            <option value="">Seleccione una opción</option>
                            <?php
                            $opcionesConsulta = Grados::traerGradosInstitucion($config, GRADO_GRUPAL);
                            while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                                $disabled = '';
                                if ($opcionesDatos['gra_estado'] == '0') $disabled = 'disabled'; ?>
                                <option value="<?= $opcionesDatos['gra_id']; ?>" <?= $disabled; ?>><?= $opcionesDatos['gra_id'] . ". " . strtoupper($opcionesDatos['gra_nombre']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Grupo</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" id="grupo" name="grupo" required>
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
                        <select class="form-control  select2" style="z-index:10051 !important" id="periodo" name="periodo" required>
                            <option value="">Seleccione una opción</option>
                            <?php
                            $p = 1;
                            while ($p <= $config[19]) {
                                $selected = '';
                                if ($p == $config['conf_periodo']) {
                                    $selected = 'selected';
                                }
                                echo '<option value="' . $p . '" ' . $selected . '>Periodo ' . $p . '</option>';
                                $p++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label" >Año</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2"  style="z-index:10051 !important" name="year" id="year" required>
                            <option value="">Seleccione una opción</option>
                            <?php
                            $yearStartC = $yearStart;
                            $yearEndC = $yearEnd;
                            while ($yearStartC <= $yearEndC) {
                                if ($_SESSION["bd"] == $yearStartC)
                                    echo "<option value='" . $yearStartC . "' selected style='color:blue;'>" . $yearStartC . "</option>";
                                else
                                    echo "<option value='" . $yearStartC . "'>" . $yearStartC . "</option>";
                                $yearStartC++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <input type="submit" class="btn btn-primary" value="Generar Boletin">&nbsp;

            </form>
        </div>
    </div>
    <div class="panel">
        <header class="panel-heading panel-heading-red">POR ESTUDIANTE </header>
        <div class="panel-body">
            <form name="formularioGuardar" id="formularioGuardar2" action="informes-formato-boletin.php" method="post" onsubmit="return ejecutarAntesDeEnviar2();" target="_blank">
                <div class="form-group row">
                    <label class="col-sm-2 control-label">Escoja un Formato de Boletín</label>
                    <div class="col-sm-2">
                        <select id="tipoBoletinEst" class="form-control  select2"  name="formatoB" onchange="cambiarTipoEst()">
                            <option value="">Seleccione una opción</option>
                            <?php
                            try {
                                $consultaBoletin = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".opciones_generales WHERE ogen_grupo=15");
                            } catch (Exception $e) {
                                include("../compartido/error-catch-to-report.php");
                            }
                            while ($datosBoletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH)) {
                            ?>
                                <option <?php if ($config['conf_formato_boletin'] == $datosBoletin['ogen_id']) {
                                            echo "selected";
                                        } ?> value="<?= $datosBoletin['ogen_id']; ?>"><?= $datosBoletin['ogen_nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="button" title="Ver formato del boletin" class="btn btn-sm" data-toggle="popover_2"><i class="fa fa-eye"></i></button>
                    <script>
                        $(document).ready(function() {
                            $('[data-toggle="popover_2"]').popover({
                                html: true, // Habilitar contenido HTML
                                content: function() {
                                    valor = document.getElementById("tipoBoletinEst");
                                    vacio = valor.value === null || valor.value === undefined || valor.value.trim() === '';
                                    if (!vacio) {
                                        return '<div id="myPopover" class="popover-content"><label id="lbl_tipoEst">Formato tipo ' + valor.value + '</label>' +
                                            '<img id="img-boletinEst" src="../files/images/boletines/tipo' + valor.value + '.png" class="w-100" />' +
                                            '</div>';
                                    } else {
                                        return '<div id="myPopover" class="popover-content"><label id="lbl_tipoEst">Seleccione un tipo de formato.</label>' +

                                            '</div>';
                                    }
                                }
                            });
                        });

                        function cambiarTipoEst() {
                            var imagen_boletin = document.getElementById('img-boletinEst');
                            if (imagen_boletin) {
                                var valor = document.getElementById("tipoBoletinEst");
                                var lbl_tipoEst = document.getElementById('lbl_tipoEst');
                                imagen_boletin.src = "../files/images/boletines/tipo" + valor.value + ".png";
                                lbl_tipoEst.textContent = 'Formato tipo ' + valor.value;
                            }
                        }
                    </script>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Estudiante</label>
                    <div class="col-sm-8">

                        <select id="selectEstudiantes" class="form-control  select2" name="estudiante" multiple required>
                            <option value="">Seleccione una opción</option>
                            <?php
                            $grados = Grados::traerGradosInstitucion($config, GRADO_GRUPAL);
                            while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
                            ?>

                                <optgroup label="<?= $grado['gra_nombre']; ?>">
                                    <?php
                                    $filtro = ' AND mat_grado="' . $grado['gra_id'] . '"';
                                    $opcionesConsulta = Estudiantes::listarEstudiantesEnGrados($filtro, '');
                                    while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                                    ?>

                                        <option value="<?= $opcionesDatos['mat_id']; ?>">
                                            <?= "[" . $opcionesDatos['mat_id'] . "] " . strtoupper($opcionesDatos['mat_primer_apellido'] . " " . $opcionesDatos['mat_segundo_apellido'] . " " . $opcionesDatos['mat_nombres'] . " " . $opcionesDatos['mat_nombre2']); ?>
                                            - <?= strtoupper($opcionesDatos['gra_nombre'] . " " . $opcionesDatos['gru_nombre']); ?>
                                        </option>

                                    <?php } ?>

                                </optgroup>
                            <?php } ?>

                        </select>
                        <span style="color: darkblue;">Seleccione solo una opción de este listado.</span>
                    </div>
                </div>


                <div class="form-group row">
                    <label class="col-sm-2 control-label">Periodo</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="periodo" id="periodo2" required>
                            <option value="">Seleccione una opción</option>
                            <?php
                            $p = 1;
                            while ($p <= $config[19]) {
                                $selected = '';
                                if ($p == $config['conf_periodo']) {
                                    $selected = 'selected';
                                }

                                echo '<option value="' . $p . '" ' . $selected . '>Periodo ' . $p . '</option>';
                                $p++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Año</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="year" style="z-index:1051" id="year2" required>
                            <option value="">Seleccione una opción</option>
                            <?php
                            $yearStartE = $yearStart;
                            $yearEndE = $yearEnd;
                            while ($yearStartE <= $yearEndE) {
                                if ($_SESSION["bd"] == $yearStartE)
                                    echo "<option value='" . $yearStartE . "' selected style='color:blue;'>" . $yearStartE . "</option>";
                                else
                                    echo "<option value='" . $yearStartE . "'>" . $yearStartE . "</option>";
                                $yearStartE++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <input type="submit" class="btn btn-primary" value="Generar Boletin">&nbsp;
            </form>
        </div>
    </div>
</div>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
   <!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
<script>
    // Agregar el evento onchange al select
    var miSelect = document.getElementById('selectEstudiantes');
    miSelect.onchange = function() {
        limitarSeleccion(this);
    };
</script>
<script>
    // Función que se ejecuta antes de enviar el formulario
    function ejecutarAntesDeEnviar1() {
        var curso   = document.getElementById("curso").value;
        var grupo   = document.getElementById("grupo").value;
        var periodo = document.getElementById("periodo").value;
        var year    = document.getElementById("year").value;
        $.toast({
            heading            : 'Consultando Notas',
            position           : 'bottom-right',
            showHideTransition : 'slide',
            icon               : 'success',
            hideAfter          : 3500,
            stack              : 6
        });
        var data = {
            "curso": curso,
            "grupo": grupo,
            "periodo": periodo,
            "idEstudiante": '',
            "year": year
        };
        metodoFetch('../compartido/ajax_contar_notas_curso.php', data, 'json', false, 'enviarFormulario1');
        return false;
    }
    function enviarFormulario1(response) {
        if (response["ok"]) {
            resultData = response["result"];
            console.log(resultData);
            if (resultData["notas_registradas"] > 0) {
                document.getElementById('formularioGuardar1').submit();
            } else {
                var data = {
                    "idmsg": 306,
                    "msj": 'No se encontraron notas finales '
                };
                abrirModal('<?= $frases[115][$datosUsuarioActual['uss_idioma']]; ?>', '../compartido/page-info-modal.php', data, 5000, '800px');
            }
        }
    }
    // Función que se ejecuta antes de enviar el formulario
    function ejecutarAntesDeEnviar2() {
        
        var idEstudiante = document.getElementById("selectEstudiantes").value;
        var periodo      = document.getElementById("periodo2").value;
        var year         = document.getElementById("year2").value;
        $.toast({
            heading            : 'Consultando Notas',
            position           : 'bottom-right',
            showHideTransition : 'slide',
            icon               : 'success',
            hideAfter          : 3500,
            stack              : 6
        });
        var data = {
            "curso": '',
            "grupo": '',
            "periodo": periodo,
            "idEstudiante": idEstudiante,
            "year": year
        };
        metodoFetch('../compartido/ajax_contar_notas_curso.php', data, 'json', false, 'enviarFormulario2');
        return false;
    }
    
    function enviarFormulario2(response) {
        if (response["ok"]) {
            resultData = response["result"];
            console.log(resultData);
            if (resultData["notas_registradas"] > 0) {
                document.getElementById('formularioGuardar2').submit();
            } else {
                var data = {
                    "idmsg": 306,
                    "msj": 'No se encontraron notas finales '
                };
                abrirModal('<?= $frases[115][$datosUsuarioActual['uss_idioma']]; ?>', '../compartido/page-info-modal.php', data, 5000, '800px');
            }
        }
    }

</script>