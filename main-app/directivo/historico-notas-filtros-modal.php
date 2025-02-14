<?php include("session.php"); ?>
<?php
require_once(ROOT_PATH . "/main-app/class/Grupos.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");
$idPaginaInterna = 'DT0346';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

?>
<style>
    .switchToggle .slider.round:before {
        border-radius: 50%;
        height: 15px;
        width: 15px;
    }

    .switchToggle input:checked+.slider:before {
        -webkit-transform: translateX(36px);
    }

    .slider {
        width: 50px;
        height: 20px;
    }
</style>
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
    type="text/css" />
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<div class="row">
    <div class="col-4">
        <header class="panel-heading panel-heading-purple ">Filtros</header>
        <div class="panel-body">
            <form name="formularioGuardar" id="formularioGuardar" action="../compartido/historial-notas.php"
                method="post" target="_blank">

                <div class="form-group row">
                    <label class="col-12 control-label">Curso</label>
                    <div class="col-12">
                        <select class="form-control  select2" style="width:100%;" name="grado" id="grado" required
                            onchange="habilitarGrupos()">
                            <option value="">Seleccione una opción</option>
                            <?php
                            $opcionesConsulta = Grados::traerGradosInstitucion($config);
                            while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                                $disabled = '';
                                if ($opcionesDatos['gra_estado'] == '0')
                                    $disabled = 'disabled';
                                ?>
                                <option value="<?= $opcionesDatos['gra_id']; ?>" <?= $disabled; ?>>
                                    <?= $opcionesDatos['gra_id'] . ". " . strtoupper($opcionesDatos['gra_nombre']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-12 control-label">Grupos</label>
                    <div class="col-12">
                        <span id="mensajeGrupos" style="color: #6017dc; display:none;">Espere un momento mientras se
                            cargan los grupos.</span>
                        <select class="form-control  select2" style="width:100%;" id="grupo" name="grupo" disabled
                            onchange="listarEstudiantes()">

                        </select>
                    </div>
                </div>
                <div class="form-group row"  style="display: none;">
                    <div class="col-12">
                <select class="form-control  select2 " name="estudiantes[]" multiple id="estudiantes"> </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-12 control-label">Tipo de generarcion</label>
                    <div class="col-12">
                        <select class="form-control  select2" style="width:100%;" name="formato" id="tipoGeneracion">
                            <option value="1">Modo Edicion</option>
                            <option value="2">Generar Excel</option>
                        </select>


                    </div>
                </div>
                <div class="form-group row">
                    <input type="submit" class="col-12 btn btn-primary" id="generarInforme" disabled
                        value="Generar informe">&nbsp;
                </div>
            </form>

        </div>
    </div>
    <div class="col-8">
        <header class="panel-heading panel-heading-purple ">ESTUDAINTES </header>
        <div class="panel-body">
            <span id="mensajeEstudiantes" style="color: #6017dc; display:none;">Espere un momento mientras se
                cargan los Estudiantes.</span>
            <table id="tablaEstudiantes" class="display" style="width:100%;">
                <thead>
                    <tr>
                        <th width="10px">
                            <div class="input-group spinner col-12">
                                <label id="lblCantSeleccionados" type="text" style="text-align: center;"></label>
                                </br>
                                <label class="switchToggle" title="Seleccionar todos">
                                    <input type="checkbox" id="checkTodos"
                                        onChange="seleccionarCheck('tablaEstudiantes','selecionado','lblCantSeleccionados',this.checked)"
                                        value="1">
                                    <span class="slider aqua round"></span>
                                </label>
                            </div>
                        </th>
                        <th width="50px">#</th>
                        <th width="100px">ID</th>
                        <th width="600px">Nombre Completo</th>
                    </tr>
                </thead>
                <tbody id="result">
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    selectcurso       = $('#grado');
    selectgrupos      = $('#grupo');
    selectEstudiantes = $('#estudiantes');

    $('#tablaEstudiantes').DataTable();


    document.getElementById("formularioGuardar").addEventListener("submit", function (event) {
        // Prevenir el envío automático del formulario
        event.preventDefault();

        // Obtener los IDs seleccionados
        ids = getSelecionados('tablaEstudiantes', 'selecionado', 'lblCantSeleccionados');
        selectEstudiantes.empty();
        if (ids.length > 0) {
            ids.forEach(element => {
                console.log(element);
                let nuevaOpcion = new Option(element, element, true, true);
                selectEstudiantes.append(nuevaOpcion);
            });
        } else {
            Swal.fire(
                {
                    title: "No tiene datos selecionado!",
                    icon: "question",
                    draggable: true
                }
            );
        }



        // Enviar el formulario manualmente
        this.submit();
    });

    async function habilitarGrupos() {
        let curso = selectcurso.val();
        let url = "../compartido/ajax_grupos_curso.php";
        let data = {
            "cursos": curso
        };
        let grupos = document.getElementById('grupo');
        $('#mensajeGrupos').show();
        grupos.setAttribute('disabled', 'true');
        document.getElementById('generarInforme').setAttribute('disabled', 'true');

        selectgrupos.empty();
        resultado = await metodoFetchAsync(url, data, 'json', false);
        resultData = resultado["data"];
        if (resultData["ok"]) {
            resultData["result"];
            // Itera sobre el JSON y añade cada opción
            let OpcionSeleccion = new Option("Seleccione Curso", "", true, false);
            selectgrupos.append(OpcionSeleccion);
            resultData["result"].forEach(function (opcion) {
                let nuevaOpcion = new Option(opcion.gru_nombre, opcion.car_grupo, false, false);
                selectgrupos.append(nuevaOpcion);
            });
            grupos.removeAttribute('disabled');
            $('#mensajeGrupos').hide();
        }
    }


    async function listarEstudiantes() {
        const table = $('#tablaEstudiantes').DataTable();
        table.clear().draw();

        document.getElementById('lblCantSeleccionados').textContent = '';

        let checkTodos = document.getElementById('checkTodos');
        checkTodos.checked = false;


        $('#mensajeEstudiantes').show();

        let url = "../compartido/ajax_estudiantes_curso_grupos.php";
        let data = {
            "cursos": selectcurso.val(),
            "grupos": selectgrupos.val()
        };

        console.log('data' + data);
        resultado = await metodoFetchAsync(url, data, 'json', false);
        resultData = resultado["data"];

        if (resultData["ok"]) {
            resultData["result"];
            console.log('Número de columnas:', table.columns().count());
            // Itera sobre el JSON y añade cada opción
            resultData["result"].forEach(function (opcion) {
                table.row.add([
                    "<div class='input-group spinner col-sm-10'><label class='switchToggle'><input type='checkbox' onChange=\"getSelecionados('tablaEstudiantes','selecionado','lblCantSeleccionados')\" id='" + opcion['mat_id'] + "_select' name='selecionado'><span class='slider aqua round'></span></label></div>",
                    opcion['mat_id'],
                    opcion['mat_documento'],
                    opcion['nombre_completo']
                ]).draw();

                checkTodos.checked = true;
                seleccionarCheck('tablaEstudiantes', 'selecionado', 'lblCantSeleccionados', true);
                $('#mensajeEstudiantes').hide();
                document.getElementById('generarInforme').removeAttribute('disabled');
            });
        }
    }
</script>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
<script src="../../config-general/assets/js/pages/table/table_data.js"></script>