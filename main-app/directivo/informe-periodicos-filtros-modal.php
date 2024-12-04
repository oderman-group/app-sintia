<?php include("session.php");?>
<?php
require_once(ROOT_PATH . "/main-app/class/Grupos.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");
$idPaginaInterna = 'DT0340';

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
        <form name="formularioGuardar" action="../compartido/excel-informe-periodico.php" method="post" target="_blank">

            <div class="form-group row">
                <label class="col-sm-2 control-label">Curso</label>
                <div class="col-sm-8">
                    <select class="form-control  select2" style="width: 810.666px;" name="gradoInforme" id="gradoInforme" required onchange="habilitarGrupos()">
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
                <label class="col-sm-2 control-label">Grupos</label>
                <div class="col-sm-4">
                    <span id="mensajeGrupos" style="color: #6017dc; display:none;">Espere un momento mientras se cargan los grupos.</span>
                    <select class="form-control  select2" style="width: 810.666px;" id="gruposInforme" name="gruposInforme[]" multiple disabled onchange="habilitarMaterias()">

                    </select>

                </div>
            </div>

            <div class="form-group row" id="carga-container">
                <label class="col-sm-2 control-label">Materias</label>
                <div class="col-sm-8">
                    <span id="mensajeMaterias" style="color: #6017dc; display:none;">Espere un momento mientras se cargan las materias.</span>
                    <select class="form-control  select2" style="width: 810.666px;" name="materiasInforme[]" id="materiasInforme" multiple disabled>
                    </select>


                </div>
            </div>
            <input type="submit" class="btn btn-primary" value="Generar informe">&nbsp;
        </form>

    </div>
</div>
<script>
    var selectcurso = $('#gradoInforme');
    var selectgrupos = $('#gruposInforme');
    var selectmaterias = $('#materiasInforme');
    async function habilitarGrupos() {
        var curso = selectcurso.val();
        var url = "../compartido/ajax_grupos_curso.php";
        var data = {
            "cursos": curso
        };
        $('#mensajeGrupos').show();
        selectgrupos.empty();
        selectmaterias.empty();
        resultado = await metodoFetchAsync(url, data, 'json', false);
        resultData = resultado["data"];
        if (resultData["ok"]) {
            resultData["result"];
            // Itera sobre el JSON y añade cada opción
            resultData["result"].forEach(function(opcion) {
                var nuevaOpcion = new Option(opcion.gru_nombre, opcion.car_grupo, false, false);
                selectgrupos.append(nuevaOpcion);
            });
            var grupos = document.getElementById('gruposInforme');
            grupos.removeAttribute('disabled');
            $('#mensajeGrupos').hide();
        }
    }
    async function habilitarMaterias() {
        var url = "../compartido/ajax_materias_curso_grupos.php";
        var data = {
            "cursos": selectcurso.val(),
            "grupos": selectgrupos.val()
        };
        $('#mensajeMaterias').show();
        selectmaterias.empty();
        resultado = await metodoFetchAsync(url, data, 'json', false);
        resultData = resultado["data"];
        if (resultData["ok"]) {
            resultData["result"];
            // Itera sobre el JSON y añade cada opción
            resultData["result"].forEach(function(opcion) {
                var nuevaOpcion = new Option(opcion.mat_id + '. ' + opcion.mat_nombre, opcion.mat_id, false, false);
                selectmaterias.append(nuevaOpcion);
            });
            $('#mensajeMaterias').hide();
            var materias = document.getElementById('materiasInforme');
            materias.removeAttribute('disabled');
        }
    }
</script>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>