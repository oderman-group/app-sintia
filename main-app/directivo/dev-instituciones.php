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

                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" style="width:100%;">
                                                <thead>
                                                    <tr>
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
        html = '<tr><td colspan="11" class="text-center py-5"><i class="fa fa-inbox fa-3x text-muted mb-3"></i><br><h5>No se encontraron instituciones</h5><p class="text-muted">Intenta ajustar los filtros de búsqueda</p></td></tr>';
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
            <td colspan="11" class="text-center py-5">
                <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <br>
                <h5>Error</h5>
                <p class="text-muted">${mensaje}</p>
            </td>
        </tr>
    `);
}
</script>
<!-- end js include path -->
</body>

</html>