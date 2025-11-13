<?php
include("session.php");

$idPaginaInterna = 'DV0005';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");

$Plataforma = new Plataforma;
?>
<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    function guardarAjax(datos){ 
        var idR = datos.id;
        var valor = 0;

        if(document.getElementById(idR).checked){
            valor = 1;
            document.getElementById("Reg"+idR).style.backgroundColor="#ff572238";
        }else{
            valor = 0;
            document.getElementById("Reg"+idR).style.backgroundColor="white";
        }
        var operacion = 3;

        $('#respuestaGuardar').empty().hide().html("").show(1);
            datos = "idR="+(idR)+
                    "&valor="+(valor)+
                    "&operacion="+(operacion);
                    $.ajax({
                        type: "POST",
                        url: "ajax-guardar.php",
                        data: datos,
                        success: function(data){
                        $('#respuestaGuardar').empty().hide().html(data).show(1);
                        }
                    });
    }
</script>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title"><?=$frases[399][$datosUsuarioActual['uss_idioma']];?></div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                include("../../config-general/mensajes-informativos.php");
                                ?>
                                <span id="respuestaGuardar"></span>
                                
                                <!-- Barra de Filtros Moderna -->
                                <div class="card card-topline-purple mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Búsqueda -->
                                            <div class="col-md-4 mb-3">
                                                <label><i class="fa fa-search"></i> Buscar</label>
                                                <input type="text" id="filtro_busqueda" class="form-control" placeholder="Nombre, código, contacto..." />
                                            </div>
                                            
                                            <!-- Filtro Plan -->
                                            <div class="col-md-2 mb-3">
                                                <label><i class="fa fa-certificate"></i> Plan</label>
                                                <select id="filtro_plan" class="form-control">
                                                    <option value="todos">Todos</option>
                                                    <?php
                                                    try{
                                                        $planes = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".planes_sintia WHERE plns_tipo='".PLANES."' ORDER BY plns_nombre");
                                                        while ($plan = mysqli_fetch_array($planes, MYSQLI_BOTH)) {
                                                            echo '<option value="'.$plan['plns_id'].'">'.$plan['plns_nombre'].'</option>';
                                                        }
                                                    } catch (Exception $e) {
                                                        echo "<!-- Error cargando planes -->";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <!-- Filtro Estado -->
                                            <div class="col-md-2 mb-3">
                                                <label><i class="fa fa-toggle-on"></i> Estado</label>
                                                <select id="filtro_estado" class="form-control">
                                                    <option value="todos">Todos</option>
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Filtro Bloqueado -->
                                            <div class="col-md-2 mb-3">
                                                <label><i class="fa fa-lock"></i> Bloqueado</label>
                                                <select id="filtro_bloqueado" class="form-control">
                                                    <option value="todos">Todos</option>
                                                    <option value="0">No bloqueado</option>
                                                    <option value="1">Bloqueado</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Registros por página -->
                                            <div class="col-md-2 mb-3">
                                                <label><i class="fa fa-list"></i> Mostrar</label>
                                                <select id="registros_por_pagina" class="form-control">
                                                    <option value="20">20 registros</option>
                                                    <option value="50">50 registros</option>
                                                    <option value="100">100 registros</option>
                                                    <option value="200">200 registros</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12 text-right">
                                                <button type="button" id="btn_limpiar_filtros" class="btn btn-secondary">
                                                    <i class="fa fa-eraser"></i> Limpiar Filtros
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-topline-purple">
                                    <div class="card-head">
                                        <header>
                                            <?=$frases[399][$datosUsuarioActual['uss_idioma']];?>
                                            <span id="info_registros" class="badge badge-info ml-2">Cargando...</span>
                                        </header>
                                        <div class="tools">
                                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;" onclick="cargarInstituciones()"></a>
                                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Indicador de carga -->
                                        <div id="loading_instituciones" class="text-center py-5" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Cargando...</span>
                                            </div>
                                            <p class="mt-3">Cargando instituciones...</p>
                                        </div>

                                        <!-- Barra de acciones masivas -->
                                        <div id="barra_acciones_masivas" class="mb-3" style="display: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px; border-radius: 10px; color: white;">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <strong><i class="fa fa-check-square"></i> <span id="contador_seleccionados">0</span> institución(es) seleccionada(s)</strong>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <button type="button" class="btn btn-sm" onclick="eliminarInstitucionesSeleccionadas()" style="background: #ef4444; color: white; border: none; padding: 8px 20px; border-radius: 8px;">
                                                        <i class="fa fa-trash"></i> Eliminar seleccionadas
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 40px;">
                                                            <input type="checkbox" id="selectAllInstituciones" title="Seleccionar todas">
                                                        </th>
                                                        <th>#</th>
                                                        <th>Bloq</th>
                                                        <th>Cod</th>
                                                        <th>Fecha Inicio</th>
                                                        <th>Nombre Institución</th>
                                                        <th>Contacto Principal</th>
                                                        <th>Plan</th>
                                                        <th>Espacio (GB)</th>
                                                        <th>Fecha Renovación</th>
                                                        <th>Estado</th>
                                                        <th><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tabla_instituciones">
                                                    <!-- Se cargará dinámicamente con AJAX -->
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Paginación -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div id="info_paginacion" class="text-muted"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <nav aria-label="Paginación">
                                                    <ul id="paginacion_controles" class="pagination justify-content-end">
                                                        <!-- Se generará dinámicamente -->
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page container -->
    <?php include("../compartido/footer.php"); ?>
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>

<!-- Script de paginación y filtros de instituciones -->
<script>
let paginaActual = 1;
let registrosPorPagina = 20;
let timeoutBusqueda = null;

$(document).ready(function() {
    // Cargar instituciones al iniciar
    cargarInstituciones();
    
    // Búsqueda en tiempo real con delay
    $('#filtro_busqueda').on('keyup', function() {
        clearTimeout(timeoutBusqueda);
        timeoutBusqueda = setTimeout(function() {
            paginaActual = 1;
            cargarInstituciones();
        }, 500);
    });
    
    // Cambio en filtros - recargar inmediatamente
    $('#filtro_plan, #filtro_estado, #filtro_bloqueado').on('change', function() {
        paginaActual = 1;
        cargarInstituciones();
    });
    
    // Cambio en registros por página
    $('#registros_por_pagina').on('change', function() {
        registrosPorPagina = parseInt($(this).val());
        paginaActual = 1;
        cargarInstituciones();
    });
    
    // Limpiar filtros
    $('#btn_limpiar_filtros').on('click', function() {
        $('#filtro_busqueda').val('');
        $('#filtro_plan').val('todos');
        $('#filtro_estado').val('todos');
        $('#filtro_bloqueado').val('todos');
        $('#registros_por_pagina').val('20');
        paginaActual = 1;
        registrosPorPagina = 20;
        cargarInstituciones();
    });
});

function cargarInstituciones() {
    $('#loading_instituciones').show();
    $('#tabla_instituciones').html('');
    
    $.ajax({
        url: 'ajax-instituciones-listar.php',
        type: 'POST',
        dataType: 'json',
        data: {
            pagina: paginaActual,
            porPagina: registrosPorPagina,
            busqueda: $('#filtro_busqueda').val(),
            plan: $('#filtro_plan').val(),
            estado: $('#filtro_estado').val(),
            bloqueado: $('#filtro_bloqueado').val()
        },
        success: function(response) {
            $('#loading_instituciones').hide();
            
            if (response.success) {
                renderizarInstituciones(response.instituciones, response.paginacion);
            } else {
                mostrarError(response.message || 'Error al cargar instituciones');
            }
        },
        error: function(xhr, status, error) {
            $('#loading_instituciones').hide();
            console.error('Error AJAX:', error);
            mostrarError('Error de conexión al cargar instituciones');
        }
    });
}

function renderizarInstituciones(instituciones, paginacion) {
    let html = '';
    
    if (instituciones.length === 0) {
        html = '<tr><td colspan="12" class="text-center py-5"><i class="fa fa-inbox fa-3x text-muted mb-3"></i><br><h5>No se encontraron instituciones</h5><p class="text-muted">Intenta ajustar los filtros de búsqueda</p></td></tr>';
    } else {
        let inicio = (paginacion.paginaActual - 1) * paginacion.porPagina;
        
        instituciones.forEach(function(inst, index) {
            let numFila = inicio + index + 1;
            let estado = inst.ins_estado == 1 ? 'Activo' : 'Inactivo';
            let espacio = inst.plns_espacio_gb ? inst.plns_espacio_gb + 'GB' : '';
            let bgColor = inst.ins_bloqueada == 1 ? '#ff572238' : 'transparent';
            let checked = inst.ins_bloqueada == 1 ? 'checked' : '';
            let planNombre = inst.plns_nombre || '<span class="text-muted">Sin plan</span>';
            
            html += `
                <tr id="Reg${inst.ins_id}" style="background-color:${bgColor};">
                    <td align="center">
                        <input type="checkbox" class="institucion-checkbox" 
                            data-id="${inst.ins_id}" 
                            data-nombre="${inst.ins_nombre.replace(/"/g, '&quot;')}"
                            data-bd="${inst.ins_bd || ''}"
                            data-contacto="${inst.ins_contacto_principal || ''}">
                    </td>
                    <td>${numFila}</td>
                    <td>
                        <div class="input-group spinner col-sm-10">
                            <label class="switchToggle">
                                <input type="checkbox" id="${inst.ins_id}" name="bloqueado" value="1" onChange="guardarAjax(this)" ${checked}>
                                <span class="slider red round"></span>
                            </label>
                        </div>
                    </td>
                    <td>${inst.ins_id}</td>
                    <td>${inst.ins_fecha_inicio || '-'}</td>
                    <td><strong>${inst.ins_nombre}</strong></td>
                    <td>${inst.ins_contacto_principal || '-'}</td>
                    <td>${planNombre}</td>
                    <td>${espacio}</td>
                    <td>${inst.ins_fecha_renovacion || '-'}</td>
                    <td><span class="badge badge-${inst.ins_estado == 1 ? 'success' : 'danger'}">${estado}</span></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></button>
                            <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="dev-instituciones-editar-v2.php?id=${btoa(inst.ins_id)}">Editar</a></li>
                                <li><a href="dev-instituciones-configuracion.php?id=${btoa(inst.ins_id)}"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?></a></li>
                                <li><a href="dev-instituciones-Informacion.php?id=${btoa(inst.ins_id)}">Información</a></li>
                                <li><a href="auto-login-dev.php?user=${btoa('1')}&idInstitucion=${btoa(inst.ins_id)}&bd=${btoa(inst.ins_bd)}&yearDefault=${btoa(inst.ins_year_default)}">Autologin</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0);" onclick="eliminarInstitucionIndividual('${inst.ins_id}', '${inst.ins_nombre.replace(/'/g, "\\'")}', '${inst.ins_bd || ''}', '${inst.ins_contacto_principal || ''}')" style="color: #ef4444;"><i class="fa fa-trash"></i> Eliminar</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#tabla_instituciones').html(html);
    renderizarPaginacion(paginacion);
    actualizarInfoRegistros(paginacion);
}

function renderizarPaginacion(paginacion) {
    let html = '';
    
    // Botón Anterior
    if (paginacion.paginaActual > 1) {
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${paginacion.paginaActual - 1})">«</a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">«</span></li>`;
    }
    
    // Números de página
    let inicio = Math.max(1, paginacion.paginaActual - 2);
    let fin = Math.min(paginacion.totalPaginas, paginacion.paginaActual + 2);
    
    if (inicio > 1) {
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(1)">1</a></li>`;
        if (inicio > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = inicio; i <= fin; i++) {
        if (i === paginacion.paginaActual) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${i})">${i}</a></li>`;
        }
    }
    
    if (fin < paginacion.totalPaginas) {
        if (fin < paginacion.totalPaginas - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${paginacion.totalPaginas})">${paginacion.totalPaginas}</a></li>`;
    }
    
    // Botón Siguiente
    if (paginacion.paginaActual < paginacion.totalPaginas) {
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${paginacion.paginaActual + 1})">»</a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">»</span></li>`;
    }
    
    $('#paginacion_controles').html(html);
}

function actualizarInfoRegistros(paginacion) {
    let inicio = (paginacion.paginaActual - 1) * paginacion.porPagina + 1;
    let fin = Math.min(paginacion.paginaActual * paginacion.porPagina, paginacion.totalRegistros);
    
    $('#info_registros').text(`${paginacion.totalRegistros} instituciones`);
    $('#info_paginacion').html(`Mostrando <strong>${inicio}</strong> a <strong>${fin}</strong> de <strong>${paginacion.totalRegistros}</strong> registros`);
}

function irAPagina(pagina) {
    paginaActual = pagina;
    cargarInstituciones();
    // Scroll al inicio de la tabla
    $('html, body').animate({
        scrollTop: $("#tabla_instituciones").offset().top - 100
    }, 300);
}

function mostrarError(mensaje) {
    $('#tabla_instituciones').html(`
        <tr>
            <td colspan="12" class="text-center py-5">
                <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <br>
                <h5>Error</h5>
                <p class="text-muted">${mensaje}</p>
            </td>
        </tr>
    `);
}

// Manejo de selección de checkboxes
$(document).on('change', '#selectAllInstituciones', function() {
    const checked = $(this).is(':checked');
    $('.institucion-checkbox').prop('checked', checked);
    actualizarBarraAccionesMasivas();
});

$(document).on('change', '.institucion-checkbox', function() {
    actualizarBarraAccionesMasivas();
    const total = $('.institucion-checkbox').length;
    const seleccionadas = $('.institucion-checkbox:checked').length;
    $('#selectAllInstituciones').prop('indeterminate', seleccionadas > 0 && seleccionadas < total);
    $('#selectAllInstituciones').prop('checked', seleccionadas === total && total > 0);
});

function actualizarBarraAccionesMasivas() {
    const seleccionadas = $('.institucion-checkbox:checked').length;
    $('#contador_seleccionados').text(seleccionadas);
    if (seleccionadas > 0) {
        $('#barra_acciones_masivas').slideDown(200);
    } else {
        $('#barra_acciones_masivas').slideUp(200);
    }
}

function obtenerInstitucionesSeleccionadas() {
    const instituciones = [];
    $('.institucion-checkbox:checked').each(function() {
        instituciones.push({
            id: $(this).data('id'),
            nombre: $(this).data('nombre'),
            bd: $(this).data('bd'),
            contacto: $(this).data('contacto')
        });
    });
    return instituciones;
}

function eliminarInstitucionIndividual(id, nombre, bd, contacto) {
    const instituciones = [{
        id: id,
        nombre: nombre,
        bd: bd,
        contacto: contacto
    }];
    abrirModalEliminarInstituciones(instituciones);
}

function eliminarInstitucionesSeleccionadas() {
    const instituciones = obtenerInstitucionesSeleccionadas();
    if (instituciones.length === 0) {
        alert('No has seleccionado ninguna institución.');
        return;
    }
    abrirModalEliminarInstituciones(instituciones);
}

function abrirModalEliminarInstituciones(instituciones) {
    $('#instituciones_a_eliminar').val(JSON.stringify(instituciones));
    
    let nombresHtml = '<ul style="max-height: 200px; overflow-y: auto;">';
    instituciones.forEach(function(inst) {
        nombresHtml += `<li><strong>${inst.nombre}</strong> (ID: ${inst.id})</li>`;
    });
    nombresHtml += '</ul>';
    $('#lista_instituciones_eliminar').html(nombresHtml);
    $('#contador_instituciones_eliminar').text(instituciones.length);
    
    // Resetear checkboxes
    $('#eliminar_usuarios').prop('checked', false);
    $('#eliminar_configuracion').prop('checked', false);
    $('#eliminar_academico').prop('checked', false);
    $('#eliminar_financiero').prop('checked', false);
    $('#eliminar_otros').prop('checked', false);
    $('#eliminar_institucion_completa').prop('checked', false);
    
    $('#modalEliminarInstituciones').modal('show');
}

function procesarEliminacionInstituciones() {
    const institucionesJSON = $('#instituciones_a_eliminar').val();
    if (!institucionesJSON) {
        alert('No hay instituciones para eliminar.');
        return;
    }
    
    const instituciones = JSON.parse(institucionesJSON);
    
    // Obtener opciones seleccionadas
    const opcionesEliminar = {
        usuarios: $('#eliminar_usuarios').is(':checked'),
        configuracion: $('#eliminar_configuracion').is(':checked'),
        academico: $('#eliminar_academico').is(':checked'),
        financiero: $('#eliminar_financiero').is(':checked'),
        otros: $('#eliminar_otros').is(':checked'),
        institucion_completa: $('#eliminar_institucion_completa').is(':checked')
    };
    
    // Si no seleccionó ninguna opción
    if (!opcionesEliminar.usuarios && !opcionesEliminar.configuracion && 
        !opcionesEliminar.academico && !opcionesEliminar.financiero && 
        !opcionesEliminar.otros && !opcionesEliminar.institucion_completa) {
        alert('Debes seleccionar al menos una opción de datos a eliminar.');
        return;
    }
    
    // Confirmación final
    let mensaje = `⚠️ ADVERTENCIA: Esta acción es IRREVERSIBLE\n\n`;
    mensaje += `Se eliminarán ${instituciones.length} institución(es) con los siguientes datos:\n\n`;
    if (opcionesEliminar.institucion_completa) {
        mensaje += `✓ TODOS LOS DATOS (institución completa)\n`;
    } else {
        if (opcionesEliminar.usuarios) mensaje += `✓ Usuarios\n`;
        if (opcionesEliminar.configuracion) mensaje += `✓ Configuración\n`;
        if (opcionesEliminar.academico) mensaje += `✓ Datos académicos\n`;
        if (opcionesEliminar.financiero) mensaje += `✓ Datos financieros\n`;
        if (opcionesEliminar.otros) mensaje += `✓ Otros datos\n`;
    }
    mensaje += `\nSe creará un archivo de respaldo y se notificará a info@oderman-group.com\n\n`;
    mensaje += `¿Estás COMPLETAMENTE SEGURO de continuar?`;
    
    if (!confirm(mensaje)) {
        return;
    }
    
    // Cerrar modal y mostrar loading
    $('#modalEliminarInstituciones').modal('hide');
    $('#loading_instituciones').show();
    
    $.ajax({
        url: 'dev-instituciones-eliminar.php',
        type: 'POST',
        dataType: 'json',
        data: {
            instituciones: JSON.stringify(instituciones),
            opciones: JSON.stringify(opcionesEliminar)
        },
        success: function(response) {
            $('#loading_instituciones').hide();
            
            if (response.success) {
                let mensaje = '✅ ' + response.message;
                
                // Mostrar errores detallados si existen
                if (response.errores && response.errores.length > 0) {
                    mensaje += '\n\n⚠️ ERRORES ENCONTRADOS:\n';
                    response.errores.forEach(function(error, index) {
                        mensaje += '\n' + (index + 1) + '. ' + error;
                    });
                }
                
                alert(mensaje);
                
                // Recargar instituciones
                $('.institucion-checkbox').prop('checked', false);
                $('#selectAllInstituciones').prop('checked', false);
                actualizarBarraAccionesMasivas();
                cargarInstituciones();
            } else {
                let mensajeError = '❌ ' + (response.message || 'Error al eliminar instituciones');
                
                // Agregar detalles de error si existen
                if (response.errores && response.errores.length > 0) {
                    mensajeError += '\n\nDETALLES:\n';
                    response.errores.forEach(function(error, index) {
                        mensajeError += '\n' + (index + 1) + '. ' + error;
                    });
                }
                
                console.error('Response completo:', response);
                alert(mensajeError);
            }
        },
        error: function(xhr, status, error) {
            $('#loading_instituciones').hide();
            console.error('Error AJAX:', error);
            console.error('Response text:', xhr.responseText);
            
            let mensajeError = '❌ Error de conexión al eliminar instituciones\n\n';
            mensajeError += 'Detalles técnicos:\n';
            mensajeError += 'Status: ' + status + '\n';
            mensajeError += 'Error: ' + error;
            
            alert(mensajeError);
        }
    });
}

// Manejar checkbox de "Eliminar todo"
$(document).on('change', '#eliminar_institucion_completa', function() {
    const checked = $(this).is(':checked');
    if (checked) {
        $('#eliminar_usuarios, #eliminar_configuracion, #eliminar_academico, #eliminar_financiero, #eliminar_otros')
            .prop('checked', true)
            .prop('disabled', true);
    } else {
        $('#eliminar_usuarios, #eliminar_configuracion, #eliminar_academico, #eliminar_financiero, #eliminar_otros')
            .prop('disabled', false);
    }
});
</script>

<!-- Modal Eliminar Instituciones -->
<div class="modal fade" id="modalEliminarInstituciones" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                <h5 class="modal-title">
                    <i class="fa fa-exclamation-triangle"></i> Eliminar Instituciones
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="instituciones_a_eliminar">
                
                <div class="alert alert-danger">
                    <strong><i class="fa fa-exclamation-triangle"></i> ADVERTENCIA:</strong> Esta acción es IRREVERSIBLE.
                </div>
                
                <div class="mb-4">
                    <h5>Se eliminarán <strong><span id="contador_instituciones_eliminar">0</span></strong> institución(es):</h5>
                    <div id="lista_instituciones_eliminar" style="background: #f9fafb; padding: 15px; border-radius: 8px; max-height: 150px; overflow-y: auto;"></div>
                </div>
                
                <hr>
                
                <h5 class="mb-3">Selecciona qué datos deseas eliminar:</h5>
                
                <div class="form-check mb-3" style="padding-left: 2rem;">
                    <input class="form-check-input" type="checkbox" id="eliminar_institucion_completa">
                    <label class="form-check-label" for="eliminar_institucion_completa" style="font-weight: 700; color: #dc2626;">
                        <i class="fa fa-exclamation-circle"></i> Eliminar TODA la institución (registro + todos los datos)
                    </label>
                    <small class="d-block text-muted ml-4">Esto marcará automáticamente todas las opciones debajo</small>
                </div>
                
                <hr>
                
                <h6 class="mb-3" style="color: #6b7280;">O selecciona componentes específicos:</h6>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="eliminar_usuarios">
                            <label class="form-check-label" for="eliminar_usuarios">
                                <i class="fa fa-users"></i> Usuarios (estudiantes, docentes, etc.)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="eliminar_configuracion">
                            <label class="form-check-label" for="eliminar_configuracion">
                                <i class="fa fa-cog"></i> Configuración del sistema
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="eliminar_academico">
                            <label class="form-check-label" for="eliminar_academico">
                                <i class="fa fa-graduation-cap"></i> Datos académicos (calificaciones, cargas, etc.)
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="eliminar_financiero">
                            <label class="form-check-label" for="eliminar_financiero">
                                <i class="fa fa-dollar-sign"></i> Datos financieros (facturas, pagos, etc.)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="eliminar_otros">
                            <label class="form-check-label" for="eliminar_otros">
                                <i class="fa fa-database"></i> Otros datos (mensajes, notificaciones, etc.)
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-4">
                    <strong><i class="fa fa-info-circle"></i> Proceso de eliminación:</strong>
                    <ol class="mb-0 mt-2">
                        <li>Se creará un archivo .txt de respaldo con los datos de la institución</li>
                        <li>Se eliminarán los datos seleccionados de las bases de datos</li>
                        <li>Se enviará un correo a <strong>info@oderman-group.com</strong> con el archivo adjunto</li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="procesarEliminacionInstituciones()">
                    <i class="fa fa-trash"></i> Confirmar eliminación
                </button>
            </div>
        </div>
    </div>
</div>

</script>
<!-- end js include path -->
</body>

</html>