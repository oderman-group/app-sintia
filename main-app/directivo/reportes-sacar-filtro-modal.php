<?php
include("session.php");
$idPaginaInterna = 'DT0116';
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
} ?>
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<div class="card card-box">
    <div class="card-head">
        <header><?= $frases[249][$datosUsuarioActual['uss_idioma']]; ?></header>
    </div>
    <div class="card-body " id="bar-parent6">
        <form class="form-horizontal" action="../compartido/reporte-disciplina-sacar.php" method="post" enctype="multipart/form-data" target="_blank">
            <input type="hidden" name="id" value="12">


            <div class="form-group row">
                <label class="col-sm-2 control-label"><?= $frases[26][$datosUsuarioActual['uss_idioma']]; ?></label>
                <div class="col-sm-10">
                    <select class="form-control  select2 select-grado-reportes" style="width: 100%;" name="grado">
                        <option value="">Seleccione una opción</option>
                        <?php
                        $datosConsulta = Grados::traerGradosInstitucion($config);
                        while ($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $datos['gra_id']; ?>"><?= $datos['gra_nombre'] ?></option>
                        <?php } ?>
                    </select>
                    <small class="text-muted">Opcional. Úsalo si deseas filtrar por curso específico.</small>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label"><?= $frases[250][$datosUsuarioActual['uss_idioma']]; ?></label>
                <div class="col-sm-10">
                    <select class="form-control  select2 select-grupo-reportes" style="width: 100%;" name="grupo">
                        <option value="">Seleccione una opción</option>
                        <?php
                        $datosConsulta = Grupos::listarGrupos();
                        while ($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $datos['gru_id']; ?>"><?= $datos['gru_nombre'] ?></option>
                        <?php } ?>
                    </select>
                    <small class="text-muted">Opcional. Se recomienda escogerlo junto al grado solo cuando necesites filtrar estudiantes.</small>
                </div>
            </div>





            <div class="form-group row">
                <label class="col-sm-2 control-label">Desde</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" name="desde" value="<?= date("Y"); ?>-01-01">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Hasta</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" name="hasta" value="<?= date("Y-m-d"); ?>">
                </div>
            </div>

            <hr>
            <h4 style="color: darkblue;">Filtros Opcionales</h4>
            <div class="form-group row">
                <label class="col-sm-2 control-label"><?= $frases[55][$datosUsuarioActual['uss_idioma']]; ?></label>
                <div class="col-sm-10">
                    <select class="form-control  select2 select-estudiantes-reportes" style="width: 100%;" name="est" multiple disabled>
                        <option value=""><?= $frases[83][$datosUsuarioActual['uss_idioma']] ?? 'Seleccione una opción'; ?></option>
                    </select>
                    <span style="color: darkblue;">Seleccione solo una opción de este listado. Los estudiantes se habilitan después de elegir grado y grupo.</span>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label"><?= $frases[248][$datosUsuarioActual['uss_idioma']]; ?></label>
                <div class="col-sm-10">
                    <select name="falta" class="form-control select2" style="width: 100%;">
                        <option value="">Seleccione una opción</option>
                        <?php
                        try {
                            $datosConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disciplina_faltas 
                                                    INNER JOIN ".BD_DISCIPLINA.".disciplina_categorias ON dcat_id=dfal_id_categoria AND dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$_SESSION["bd"]}
                                                    WHERE dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]}");
                        } catch (Exception $e) {
                            include("../compartido/error-catch-to-report.php");
                        }
                        while ($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $datos['dfal_id']; ?>"><?= $datos['dfal_codigo'] . ". " . $datos['dfal_nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>


            <div class="form-group row">
                <label class="col-sm-2 control-label"><?= $frases[75][$datosUsuarioActual['uss_idioma']]; ?></label>
                <div class="col-sm-10">
                    <?php
                    $datosConsulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND (uss_tipo = ".TIPO_DOCENTE." OR uss_tipo = ".TIPO_DIRECTIVO.")
                    ORDER BY uss_tipo, uss_nombre");
                    ?>
                    <select class="form-control  select2" style="width: 100%;" name="usuario">
                        <option value="">Seleccione una opción</option>
                        <?php
                        while ($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $datos['uss_id']; ?>"><?= UsuariosPadre::nombreCompletoDelUsuario($datos); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>


            <input type="submit" class="btn btn-primary" value="Sacar reporte">&nbsp;

        </form>
    </div>
</div>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js" ></script>

<script>
$(function(){
    function resetSelectEstudiantes($form, mensaje){
        var $sel = $form.find('.select-estudiantes-reportes');
        $sel.prop('disabled', true);
        $sel.html('<option value="">' + mensaje + '</option>');
        $sel.val(null).trigger('change');
    }

    function cargarEstudiantesPorGradoGrupo($form){
        var grado = $form.find('.select-grado-reportes').val();
        var grupo = $form.find('.select-grupo-reportes').val();
        if(!grado || !grupo){
            resetSelectEstudiantes($form, 'Seleccione un grado y grupo primero');
            return;
        }

        resetSelectEstudiantes($form, 'Cargando estudiantes...');

        $.ajax({
            url: 'ajax-estudiantes-por-grado-grupo.php',
            type: 'POST',
            dataType: 'json',
            data: { grado: grado, grupo: grupo },
            success: function(resp){
                if(resp.success && resp.estudiantes.length > 0){
                    var $sel = $form.find('.select-estudiantes-reportes');
                    $sel.empty();
                    $sel.append('<option value="">Seleccione una opción</option>');
                    resp.estudiantes.forEach(function(item){
                        $sel.append('<option value="'+item.id+'">'+item.nombre+'</option>');
                    });
                    $sel.prop('disabled', false);
                    $sel.trigger('change');
                }else{
                    resetSelectEstudiantes($form, resp.message || 'No se encontraron estudiantes con esos filtros');
                }
            },
            error: function(){
                resetSelectEstudiantes($form, 'Error al cargar estudiantes. Intenta nuevamente.');
            }
        });
    }

    $(document).on('change', '.select-grado-reportes, .select-grupo-reportes', function(){
        var $form = $(this).closest('form');
        cargarEstudiantesPorGradoGrupo($form);
    });

    $(document).on('change', '.select-estudiantes-reportes', function(){
        limitarSeleccion(this);
    });

    $('.select-estudiantes-reportes').each(function(){
        resetSelectEstudiantes($(this).closest('form'), 'Seleccione un grado y grupo primero');
    });
});
</script>