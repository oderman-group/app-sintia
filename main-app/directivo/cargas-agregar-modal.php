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

<div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h4 class="modal-title"><i class="fa fa-plus-circle"></i> <?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?></h4>
    <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
</div>

<div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
    <form name="formularioGuardar" id="formAgregarCarga" action="cargas-guardar.php" method="post">

        <!-- Sección: Información Básica -->
        <h5 class="mb-3"><i class="fa fa-info-circle"></i> Información Básica</h5>
        
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
                        <option value="">Seleccione uno o más cursos</option>
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
                        <option value="">Seleccione uno o más grupos</option>
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
                    <label>Asignatura(s) (Área) <span class="text-danger">*</span></label>
                    <select class="form-control select2-modal" name="asignatura[]" required multiple <?= $disabledPermiso; ?>>
                        <option value="">Seleccione una o más asignaturas</option>
                        <?php
                        $opcionesConsulta = Asignaturas::consultarTodasAsignaturas($conexion, $config);
                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $opcionesDatos['mat_id']; ?>"><?= $opcionesDatos['mat_nombre'] . " (" . $opcionesDatos['mat_valor'] . "%) - " . $opcionesDatos['ar_nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
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
            
            <div class="col-md-4">
                <div class="form-group">
                    <label>Director de Grupo <span class="text-danger">*</span></label>
                    <select class="form-control select2-modal" name="dg" required <?= $disabledPermiso; ?>>
                        <option value="0" selected>NO</option>
                        <option value="1">SI</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label>Intensidad Horaria <span class="text-danger">*</span></label>
                    <input type="number" name="ih" class="form-control" min="1" required <?= $disabledPermiso; ?>>
                </div>
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

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Temática del periodo </label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="tematica" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opción</option>
                            <option value="1">SI</option>
                            <option value="0" selected>NO</option>
                        </select>

                        <span class="text-info">Si selecciona SI, el docente podrá agregar la temática del periodo en la sección de clases.</span>

                    </div>

                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Observaciones en boletín </label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="observacionesBoletin" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opción</option>
                            <option value="1">SI</option>
                            <option value="0" selected>NO</option>
                        </select>

                        <span class="text-info">Si selecciona SI, el docente podrá colocar observaciones que aparecerán en el boletín de los estudiantes.</span>

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
    });
    
    // Limpiar Select2 al cerrar el modal
    $('#nuevaCargModal').on('hidden.bs.modal', function () {
        $('.select2-modal').select2('destroy');
        $('#formAgregarCarga')[0].reset();
    });
});
</script>