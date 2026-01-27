<?php
include("session.php");
$idPaginaInterna = 'DT0082';
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
?>
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="col-sm-12">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Genera certificados académicos por áreas o por materias. Selecciona el rango de años y el estudiante.
    </div>

    <div class="panel">
        <header class="panel-heading panel-heading-purple">
            <i class="fas fa-certificate"></i> Generar Certificado
        </header>
        <div class="panel-body">
            <form id="formCertificado" method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank">
                <!-- Campos hidden para desde, hasta y años a certificar -->
                <input type="hidden" name="desde" id="hiddenDesde" value="">
                <input type="hidden" name="hasta" id="hiddenHasta" value="">
                <input type="hidden" name="anios" id="hiddenAnios" value="">
                
                <!-- Tipo de Certificado: Radio Buttons -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-list"></i> Tipo de Certificado <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <div class="radio">
                            <label>
                                <input type="radio" name="tipo_certificado" id="tipoAreas" value="areas" checked>
                                <strong>Certificado por Áreas</strong> - Muestra las áreas académicas
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="tipo_certificado" id="tipoMaterias" value="materias">
                                <strong>Certificado por Materias</strong> - Muestra las materias individuales
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Estilo de Certificado (solo para áreas) -->
                <div class="form-group row" id="grupoEstiloCertificado">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-palette"></i> Estilo de Certificado <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control select2" id="tipoCertificado" name="certificado" required>
                            <option value="1" <?php if($config['conf_certificado']==1){ echo "selected";} ?>>Certificado 1</option>
                            <option value="2" <?php if($config['conf_certificado']==2){ echo "selected";} ?>>Certificado 2</option>
                            <option value="3" <?php if($config['conf_certificado']==3){ echo "selected";} ?>>Certificado 3</option>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Seleccione el formato del certificado por áreas
                        </small>
                    </div>
                </div>

                <!-- Rango de Años -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-calendar-alt"></i> Rango de Años <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <div class="row">
                            <div class="col-sm-5">
                                <label class="control-label">Desde:</label>
                                <select id="desdeAnio" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php
                                    $yearStartTemp = $yearStart;
                                    while ($yearStartTemp <= $yearEnd) {
                                        echo "<option value='" . $yearStartTemp . "'>" . $yearStartTemp . "</option>";
                                        $yearStartTemp++;
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-5">
                                <label class="control-label">Hasta:</label>
                                <select id="hastaAnio" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php
                                    $yearStartTemp = $yearArray[0];
                                    while ($yearStartTemp <= $yearEnd) {
                                        echo "<option value='" . $yearStartTemp . "'>" . $yearStartTemp . "</option>";
                                        $yearStartTemp++;
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label">&nbsp;</label>
                                <button type="button" class="btn btn-info btn-block" id="btnCargarEstudiantes" onclick="cargarEstudiantesPorRangoAnios()" disabled title="Buscar estudiantes" aria-label="Buscar estudiantes">
                                    <i class="fas fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div id="rangoAniosResumen" class="mt-2 p-2 rounded" style="background: #e8f4fd; border: 1px solid #b8daff; font-size: 0.95em; display: none;">
                            <strong><i class="fas fa-calendar-check"></i> Rango seleccionado:</strong>
                            <span id="rangoAniosTexto">—</span>
                        </div>
                        <small class="form-text text-muted mt-1">
                            <i class="fas fa-info-circle"></i> Seleccione el rango de años y haga clic en "Buscar" para cargar los estudiantes
                        </small>
                    </div>
                </div>

                <!-- Años a certificar (subset del rango) -->
                <div class="form-group row" id="grupoAniosCertificar" style="display: none;">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-calendar-week"></i> Años a certificar <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <p class="form-control-static small text-muted mb-2">
                            Marque solo los años que desea incluir en el certificado. Por defecto se incluyen todos los del rango.
                        </p>
                        <div id="contenedorAniosCertificar" style="display: flex; flex-wrap: wrap; gap: 0.5rem 1rem;"></div>
                        <div class="mt-1">
                            <a href="#" id="btnSeleccionarTodosAnios" class="small">Seleccionar todos</a>
                            <span style="margin: 0 0.5rem;">|</span>
                            <a href="#" id="btnDesmarcarTodosAnios" class="small">Desmarcar todos</a>
                        </div>
                        <small class="form-text text-muted mt-1">
                            <i class="fas fa-info-circle"></i> Debe marcar al menos un año
                        </small>
                    </div>
                </div>

                <!-- Estudiante -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-user-graduate"></i> Estudiante <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select id="selectEstudiante" class="form-control select2" name="id" required disabled>
                            <option value="">Primero seleccione el rango de años y haga clic en "Buscar"</option>
                        </select>
                        <div id="loadingEstudiantes" style="display: none; margin-top: 10px;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando estudiantes...
                        </div>
                    </div>
                </div>

                <!-- Formato de Impresión (solo para áreas) -->
                <div class="form-group row" id="grupoFormatoImpresion">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-heading"></i> Formato de Impresión
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control select2" name="sin_encabezado">
                            <option value="0" selected>Con encabezado</option>
                            <option value="1">Sin encabezado (Papel membrete)</option>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Seleccione "Sin encabezado" si va a imprimir en papel membrete
                        </small>
                    </div>
                </div>

                <!-- Estampilla (si está habilitada) -->
                <?php if ($config['conf_estampilla_certificados'] == SI) { ?>
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-stamp"></i> Estampilla o referente de pago
                    </label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="estampilla" value="" placeholder="Ingrese el número de estampilla">
                    </div>
                </div>
                <?php } ?>

                <!-- Botón Generar -->
                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-certificate"></i> Generar Certificado
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para inicializar el modal de certificados
window.inicializarModalCertificados = function() {
    // Manejar cambio de tipo de certificado
    $('input[name="tipo_certificado"]').off('change').on('change', function() {
        var tipo = $(this).val();
        
        if (tipo === 'areas') {
            // Mostrar campos específicos de áreas
            $('#grupoEstiloCertificado').show();
            $('#grupoFormatoImpresion').show();
            $('#tipoCertificado').prop('required', true);
        } else {
            // Ocultar campos específicos de áreas
            $('#grupoEstiloCertificado').hide();
            $('#grupoFormatoImpresion').hide();
            $('#tipoCertificado').prop('required', false);
        }
    });
    
    // Disparar evento inicial para configurar estado
    $('input[name="tipo_certificado"]:checked').trigger('change');
    
    // Actualizar resumen visible del rango de años
    function actualizarResumenRangoAnios() {
        var desde = $('#desdeAnio').val();
        var hasta = $('#hastaAnio').val();
        var $resumen = $('#rangoAniosResumen');
        var $texto = $('#rangoAniosTexto');
        
        if (desde && hasta && parseInt(desde) <= parseInt(hasta)) {
            $texto.text('Desde ' + desde + ' hasta ' + hasta);
            $resumen.show();
        } else {
            $texto.text('—');
            $resumen.hide();
        }
    }
    
    // Rellenar y mostrar/ocultar "Años a certificar" según el rango
    function actualizarAniosCertificar() {
        var desde = parseInt($('#desdeAnio').val(), 10);
        var hasta = parseInt($('#hastaAnio').val(), 10);
        var $grupo = $('#grupoAniosCertificar');
        var $cont = $('#contenedorAniosCertificar');
        $cont.empty();
        $('#hiddenAnios').val('');
        
        if (!desde || !hasta || desde > hasta) {
            $grupo.hide();
            return;
        }
        
        var y;
        for (y = desde; y <= hasta; y++) {
            $cont.append(
                $('<label class="checkbox-inline" style="margin-right: 12px;"></label>')
                    .append($('<input type="checkbox" class="anio-certificar">').attr('value', y))
                    .append(document.createTextNode(' ' + y))
            );
        }
        $cont.find('.anio-certificar').prop('checked', true);
        $grupo.show();
    }
    
    $('#btnSeleccionarTodosAnios').off('click').on('click', function(e) {
        e.preventDefault();
        $('#contenedorAniosCertificar .anio-certificar').prop('checked', true);
    });
    $('#btnDesmarcarTodosAnios').off('click').on('click', function(e) {
        e.preventDefault();
        $('#contenedorAniosCertificar .anio-certificar').prop('checked', false);
    });
    
    // Manejar cambio de años: bloquear siempre estudiantes y obligar a buscar de nuevo
    $('#desdeAnio, #hastaAnio').off('change').on('change', function() {
        var desde = $('#desdeAnio').val();
        var hasta = $('#hastaAnio').val();
        
        // Actualizar campos hidden
        $('#hiddenDesde').val(desde);
        $('#hiddenHasta').val(hasta);
        
        actualizarResumenRangoAnios();
        actualizarAniosCertificar();
        
        // Siempre bloquear estudiantes al cambiar años (obligar a filtrar de nuevo)
        var $selectEst = $('#selectEstudiante');
        $selectEst.prop('disabled', true).val('');
        if ($selectEst.data('select2')) {
            try { $selectEst.select2('destroy'); } catch (e) {}
        }
        $selectEst.empty().append('<option value="">Haga clic en Buscar para cargar los estudiantes</option>');
        if (typeof $ !== 'undefined' && $.fn.select2 && $('#ModalCentralizado').length) {
            $selectEst.select2({
                dropdownParent: $('#ModalCentralizado .modal-content'),
                width: '100%',
                minimumResultsForSearch: 0
            });
        }
        
        if (desde && hasta && parseInt(desde) <= parseInt(hasta)) {
            $('#btnCargarEstudiantes').prop('disabled', false);
        } else {
            $('#btnCargarEstudiantes').prop('disabled', true);
        }
    });
    
    // Ejecutar al iniciar por si hay valores precargados
    actualizarResumenRangoAnios();
    actualizarAniosCertificar();
    
    // Manejar envío del formulario
    $('#formCertificado').off('submit').on('submit', function(e) {
        var tipo = $('input[name="tipo_certificado"]:checked').val();
        var actionUrl = '';
        var desde = $('#desdeAnio').val();
        var hasta = $('#hastaAnio').val();
        var estudiante = $('#selectEstudiante').val();
        
        if (!desde || !hasta) {
            e.preventDefault();
            alert('Por favor seleccione el rango de años completo');
            return false;
        }
        
        if (!estudiante) {
            e.preventDefault();
            alert('Por favor seleccione un estudiante');
            return false;
        }
        
        var anios = [];
        $('#contenedorAniosCertificar .anio-certificar:checked').each(function() {
            anios.push(parseInt($(this).val(), 10));
        });
        anios.sort(function(a, b) { return a - b; });
        
        if (anios.length === 0) {
            e.preventDefault();
            alert('Debe marcar al menos un año a certificar');
            return false;
        }
        
        $('#hiddenDesde').val(desde);
        $('#hiddenHasta').val(hasta);
        $('#hiddenAnios').val(anios.join(','));
        
        if (tipo === 'areas') {
            actionUrl = 'estudiantes-formato-certificado.php';
        } else {
            actionUrl = '../compartido/matricula-certificado.php';
        }
        
        $(this).attr('action', actionUrl);
    });
};

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    // Si el modal ya está abierto, inicializar
    if ($('#formCertificado').length > 0) {
        window.inicializarModalCertificados();
    }
});
</script>
