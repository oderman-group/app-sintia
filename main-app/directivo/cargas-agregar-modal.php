<?php
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
} ?>

<div class="modal-header d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h4 class="modal-title flex-grow-1 mb-0"><i class="fa fa-plus-circle"></i> <?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?></h4>
    <button type="button" class="close ml-auto p-0 border-0 bg-transparent" data-dismiss="modal" aria-label="Cerrar" style="color: white; font-size: 1.75rem; line-height: 1; opacity: 0.9;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
    <form name="formularioGuardar" id="formAgregarCarga" action="cargas-guardar.php" method="post">

        <!-- Secciรณn: Informaciรณn Bรกsica -->
        <h5 class="mb-3"><i class="fa fa-info-circle"></i> Informaciรณn Bรกsica</h5>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Docente <span class="text-danger">*</span></label>
                    <?php
                    $opcionesConsulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo = ".TIPO_DOCENTE." ORDER BY uss_nombre");
                    ?>
                    <select id="selectDocentes" class="form-control select2-modal" name="docente" required <?= $disabledPermiso; ?>>
                        <option value="">Seleccione un docente</option>
                        <?php
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                            $disabled = '';
                            if ($opcionesDatos['uss_bloqueado'] == 1) $disabled = 'disabled';
                        ?>
                            <option value="<?= $opcionesDatos['uss_id']; ?>" <?= $disabled; ?>><?= UsuariosPadre::nombreCompletoDelUsuario($opcionesDatos) . " (" . $opcionesDatos['uss_usuario'] . ")"; ?></option>
                        <?php } ?>
                    </select>
                    <small class="form-text text-muted">Seleccione solo un docente</small>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Curso(s) <span class="text-danger">*</span></label>
                    <select id="multiple1" class="form-control select2-modal" name="curso[]" required multiple <?= $disabledPermiso; ?>>
                        <option value="">Seleccione uno o mรกs cursos</option>
                        <?php
                        $opcionesConsulta = Grados::traerGradosInstitucion($config);
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                            $disabled = '';
                            if ($opcionesDatos['gra_estado'] == '0') $disabled = 'disabled';
                        ?>
                            <option value="<?= $opcionesDatos['gra_id']; ?>" <?= $disabled; ?>><?= $opcionesDatos['gra_nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label>Grupo(s) <span class="text-danger">*</span></label>
                    <select id="multiple" class="form-control select2-modal" name="grupo[]" required multiple <?= $disabledPermiso; ?>>
                        <option value="">Seleccione uno o mรกs grupos</option>
                        <?php
                        $opcionesConsulta = Grupos::listarGrupos();
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $opcionesDatos['gru_id']; ?>"><?= $opcionesDatos['gru_nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Asignatura(s) (ร�rea) <span class="text-danger">*</span> <span id="contadorAsignaturas" class="badge badge-info ml-2" style="font-size: 0.85rem;">0 seleccionadas</span></label>
                    <select id="selectAsignaturas" class="form-control select2-modal" name="asignatura[]" required multiple <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una o mรกs asignaturas</option>
                        <?php
                        $opcionesConsulta = Asignaturas::consultarTodasAsignaturas($conexion, $config);
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $opcionesDatos['mat_id']; ?>"><?= $opcionesDatos['mat_nombre'] . " (" . $opcionesDatos['mat_valor'] . "%) - " . $opcionesDatos['ar_nombre']; ?></option>
                        <?php } ?>
                    </select>
                    <small class="form-text text-muted">Asignaturas que se vincularán al docente en cada combinación</small>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Periodo <span class="text-danger">*</span></label>
                    <select class="form-control select2-modal" name="periodo" required <?= $disabledPermiso; ?>>
                        <option value="">Seleccione...</option>
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
        </div>

        <!-- Lista de combinaciones (curso × grupo × asignatura) con I.H y Director de grupo por fila -->
        <h5 class="mb-2 mt-3"><i class="fa fa-list"></i> Combinaciones a crear</h5>
        <p class="text-muted small mb-2">Se generan al seleccionar cursos, grupos y asignaturas. Defina I.H y si es director de grupo para cada una.</p>
        <div id="listaCombinaciones" class="mb-3" style="max-height: 220px; overflow-y: auto;">
            <div id="listaCombinacionesVacia" class="alert alert-secondary py-3 mb-0">
                <i class="fa fa-info-circle"></i> Seleccione al menos un curso, un grupo y una asignatura para ver las combinaciones.
            </div>
            <div id="listaCombinacionesFilas" class="list-group list-group-flush" style="display: none;"></div>
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
                            <option value="">Seleccione una opciรณn</option>
                            <option value="1" selected>Activa</option>
                            <option value="0">Inactiva</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">% Actividades</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="valorActividades" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opciรณn</option>
                            <option value="1">Manual</option>
                            <option value="0" selected>Automรกtico</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">% Indicadores</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="valorIndicadores" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opciรณn</option>
                            <option value="1">Manual</option>
                            <option value="0" selected>Automรกtico</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Permiso para generar informe</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="permiso1" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opciรณn</option>
                            <option value="1" selected>SI</option>
                            <option value="0">NO</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Permiso para editar en periodos anteriores</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="permiso2" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opciรณn</option>
                            <option value="1">SI</option>
                            <option value="0" selected>NO</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Indicador automรกtico </label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="indicadorAutomatico" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opciรณn</option>
                            <option value="1">SI</option>
                            <option value="0" selected>NO</option>
                        </select>

                        <span class="text-info">Si selecciona SI, el docente no llenarรก indicadores; solo las calificaciones. Habrรก un solo indicador definitivo con el 100%.</span>

                    </div>

                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Temรกtica del periodo </label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="tematica" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opciรณn</option>
                            <option value="1">SI</option>
                            <option value="0" selected>NO</option>
                        </select>

                        <span class="text-info">Si selecciona SI, el docente podrรก agregar la temรกtica del periodo en la secciรณn de clases.</span>

                    </div>

                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Observaciones en boletรญn </label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="observacionesBoletin" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opciรณn</option>
                            <option value="1">SI</option>
                            <option value="0" selected>NO</option>
                        </select>

                        <span class="text-info">Si selecciona SI, el docente podrรก colocar observaciones que aparecerรกn en el boletรญn de los estudiantes.</span>

                    </div>

                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Indicadores definidos por directivo </label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="indicadoresDirectivo" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opciรณn</option>
                            <option value="1">SI</option>
                            <option value="0" selected>NO</option>
                        </select>

                        <span class="text-info">Si selecciona SI, solo el directivo podrรก crear y gestionar los indicadores. El docente no podrรก crear, editar o eliminar indicadores en esta carga.</span>

                    </div>

                </div>

            </div>


    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fa fa-times"></i> Cancelar
    </button>
    <button type="submit" form="formAgregarCarga" class="btn btn-primary" <?= $disabledPermiso; ?>>
        <i class="fa fa-save"></i> Guardar Carga(s)
    </button>
</div>

<script>
$(document).ready(function() {
    function actualizarContadorAsignaturas() {
        var sel = $('#selectAsignaturas');
        var n = (sel.val() || []).length;
        $('#contadorAsignaturas').text(n + (n === 1 ? ' seleccionada' : ' seleccionadas'));
    }

    function obtenerOpciones(selector) {
        var data = $(selector).select2('data') || [];
        return data.filter(function(o) { return o.id && o.id !== ''; }).map(function(o) { return { id: o.id, text: o.text }; });
    }

    function construirListaCombinaciones() {
        var cursos = obtenerOpciones('#multiple1');
        var grupos = obtenerOpciones('#multiple');
        var asignaturas = obtenerOpciones('#selectAsignaturas');
        var $vacia = $('#listaCombinacionesVacia');
        var $filas = $('#listaCombinacionesFilas');
        $filas.empty();

        if (cursos.length === 0 || grupos.length === 0 || asignaturas.length === 0) {
            $vacia.show();
            $filas.hide();
            return;
        }
        $vacia.hide();
        $filas.show();

        var index = 0;
        cursos.forEach(function(curso) {
            grupos.forEach(function(grupo) {
                asignaturas.forEach(function(asig) {
                    var label = curso.text + ' · ' + grupo.text + ' · ' + asig.text;
                    var $item = $('<div class="list-group-item list-group-item-action d-flex flex-wrap align-items-center py-2"></div>');
                    $item.append('<span class="mr-2 mb-1 flex-grow-1 text-truncate" title="' + label.replace(/"/g, '&quot;') + '">' + (index + 1) + '. ' + label + '</span>');
                    var $ih = $('<input type="number" name="ih[]" class="form-control form-control-sm mx-1" min="1" placeholder="I.H" style="width: 70px;" required>');
                    var $dg = $('<select name="dg[]" class="form-control form-control-sm mx-1" required style="width: 80px;"><option value="0">NO</option><option value="1">SÍ</option></select>');
                    $item.append('<label class="mb-0 mr-1 small">I.H:</label>').append($ih).append('<label class="mb-0 mr-1 small">Dir. grupo:</label>').append($dg);
                    $filas.append($item);
                    index++;
                });
            });
        });
    }

    // Inicializar Select2 en el modal cuando se abre
    $('#nuevaCargModal').on('shown.bs.modal', function () {
        $('.select2-modal').select2({
            dropdownParent: $('#nuevaCargModal'),
            placeholder: "Seleccione una opción",
            allowClear: true,
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                searching: function() {
                    return "Buscando...";
                }
            }
        });
        actualizarContadorAsignaturas();
        construirListaCombinaciones();
        $('#multiple1, #multiple, #selectAsignaturas').off('change.agregarCarga').on('change.agregarCarga', function() {
            actualizarContadorAsignaturas();
            construirListaCombinaciones();
        });
    });

    $('#formAgregarCarga').on('submit', function() {
        var $filas = $('#listaCombinacionesFilas');
        if ($filas.is(':visible') && $filas.children().length === 0) {
            alert('Seleccione al menos un curso, un grupo y una asignatura.');
            return false;
        }
    });

    // Limpiar Select2 al cerrar el modal
    $('#nuevaCargModal').on('hidden.bs.modal', function () {
        $('.select2-modal').select2('destroy');
        $('#formAgregarCarga')[0].reset();
        $('#contadorAsignaturas').text('0 seleccionadas');
        $('#listaCombinacionesVacia').show();
        $('#listaCombinacionesFilas').hide().empty();
    });
});
</script>