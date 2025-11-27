<?php 
include("session.php");
$idPaginaInterna = 'DT0137';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once("../class/Estudiantes.php");
require_once("../class/Grados.php");
require_once("../class/Grupos.php");
require_once("../class/Usuarios.php");
require_once("../class/UsuariosPadre.php");
require_once("../class/App/Administrativo/Usuarios_Por_Estudiantes.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
require_once("../class/servicios/UsuarioServicios.php");
require_once("../class/servicios/MatriculaServicios.php");

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}

// Obtener datos del acudiente actual
$acudienteActual = UsuarioServicios::consultar(base64_decode($_GET['id']));
$acudienteId = $acudienteActual["uss_id"];

// Obtener estudiantes asociados al acudiente actual
$parametros = array(
    "upe_id_usuario" => $acudienteId,
    "institucion" => $config['conf_id_institucion'],
    "year" => $_SESSION["bd"]
);
$listaAcudidos = UsuarioServicios::listarUsuariosEstudiante($parametros);
$estudiantesAsociados = [];
if (!empty($listaAcudidos)) {
    foreach($listaAcudidos as $acudido){
        $estudiantesAsociados[] = $acudido["upe_id_estudiante"];
    }
}

// Obtener grados para filtros
$grados = Grados::traerGradosInstitucion($config);

// SOLO cargar inicialmente los estudiantes asociados al acudiente (optimización)
$estudiantesConDatos = [];
if (!empty($estudiantesAsociados)) {
    // Construir filtro para obtener solo los estudiantes asociados
    $filtroAsociados = " AND mat_id IN ('" . implode("','", $estudiantesAsociados) . "')";
    $listaEstudiantesAsociados = Estudiantes::estudiantesMatriculados($filtroAsociados, $_SESSION["bd"]);
    
    while($estudiante = mysqli_fetch_array($listaEstudiantesAsociados, MYSQLI_BOTH)){
        $estudianteId = $estudiante['mat_id'];
        
        // Obtener datos del grado
        $gradoNombre = '';
        if (!empty($estudiante['mat_grado'])) {
            $gradoData = Grados::obtenerGrado($estudiante['mat_grado']);
            if (!empty($gradoData)) {
                $gradoNombre = $gradoData['gra_nombre'];
            }
        }
        
        // Obtener datos del grupo
        $grupoNombre = '';
        if (!empty($estudiante['mat_grupo'])) {
            $grupoData = Grupos::obtenerGrupo($estudiante['mat_grupo']);
            if (!empty($grupoData)) {
                $grupoNombre = $grupoData['gru_nombre'];
            }
        }
        
        // Obtener cada parte del nombre para búsqueda individual (en minúsculas para búsqueda)
        $primerNombre = !empty($estudiante['mat_nombres']) ? strtolower(trim($estudiante['mat_nombres'])) : '';
        $segundoNombre = !empty($estudiante['mat_nombre2']) ? strtolower(trim($estudiante['mat_nombre2'])) : '';
        $primerApellido = !empty($estudiante['mat_primer_apellido']) ? strtolower(trim($estudiante['mat_primer_apellido'])) : '';
        $segundoApellido = !empty($estudiante['mat_segundo_apellido']) ? strtolower(trim($estudiante['mat_segundo_apellido'])) : '';
        
        $estudiantesConDatos[] = [
            'mat_id' => $estudianteId,
            'nombre' => Estudiantes::NombreCompletoDelEstudiante($estudiante),
            'primer_nombre' => $primerNombre,
            'segundo_nombre' => $segundoNombre,
            'primer_apellido' => $primerApellido,
            'segundo_apellido' => $segundoApellido,
            'documento' => $estudiante['mat_documento'],
            'grado' => $estudiante['mat_grado'],
            'grado_nombre' => $gradoNombre,
            'grupo' => $estudiante['mat_grupo'],
            'grupo_nombre' => $grupoNombre,
            'asociado' => true, // Todos estos están asociados
            'acudiente_nombre' => ''
        ];
    }
}
?>

<!--bootstrap -->
<link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!-- dropzone -->
<link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<style>
    .estudiante-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: #fff;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .estudiante-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .estudiante-card.asociado {
        border-color: #28a745;
        background: #f0f9f4;
    }
    
    .estudiante-datos {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .estudiante-datos .icono-check {
        color: #28a745;
        font-size: 20px;
    }
    
    .estudiante-info {
        flex: 1;
    }
    
    .estudiante-nombre {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }
    
    .estudiante-detalles {
        font-size: 13px;
        color: #666;
        margin-bottom: 3px;
    }
    
    .estudiante-acudiente {
        font-size: 12px;
        color: #999;
        font-style: italic;
        margin-top: 5px;
    }
    
    .btn-accion {
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .btn-agregar {
        background: #28a745;
        color: white;
    }
    
    .btn-agregar:hover {
        background: #218838;
    }
    
    .btn-quitar {
        background: #dc3545;
        color: white;
    }
    
    .btn-quitar:hover {
        background: #c82333;
    }
    
    .contador-estudiantes {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        display: flex;
        gap: 20px;
        align-items: center;
    }
    
    .contador-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .contador-item strong {
        color: #495057;
    }
    
    .filtros-container {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .filtros-row {
        display: flex;
        gap: 15px;
        align-items: end;
        flex-wrap: wrap;
    }
    
    .filtro-item {
        flex: 1;
        min-width: 200px;
    }
</style>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">Acudidos</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="usuarios.php?cantidad=10&tipo=3" onClick="deseaRegresar(this)">Usuarios</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Acudidos</li>
                            </ol>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-12">
                        <?php include("../../config-general/mensajes-informativos.php"); ?>
                    </div>
                    <div class="col-sm-3">
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple"><b>Datos del acudiente</b></header>
                            <div class="panel-body">
                                <div class="form-group row">
                                    <label class="col-sm-3 control-label "><b>Nombre: </b></label>
                                    <label class="col-sm-9 control-label"><?= UsuarioServicios::nombres($acudienteActual) ?></label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 control-label"><b>Apellido:</b></label>
                                    <label class="col-sm-9 control-label"><?= UsuarioServicios::apellidos($acudienteActual) ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-9">
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple"><b>Acudidos</b></header>
                            <div class="panel-body">
                                <!-- Contador -->
                                <div class="contador-estudiantes">
                                    <div class="contador-item">
                                        <strong>Visibles:</strong> <span id="contador-visibles">0</span>
                                    </div>
                                    <div class="contador-item">
                                        <strong>Total:</strong> <span id="contador-total"><?= count($estudiantesConDatos) ?></span> <small style="color:#999;">(cargados)</small>
                                    </div>
                                    <div class="contador-item">
                                        <strong>Asociados:</strong> <span id="contador-asociados"><?= count($estudiantesAsociados) ?></span>
                                    </div>
                                </div>
                                
                                <!-- Filtros y búsqueda -->
                                <div class="filtros-container">
                                    <div class="filtros-row">
                                        <div class="filtro-item">
                                            <label><b>Buscar:</b></label>
                                            <input type="text" id="busqueda-estudiantes" class="form-control" placeholder="Nombre o documento...">
                                        </div>
                                        <div class="filtro-item">
                                            <label><b>Grado:</b></label>
                                            <select id="filtro-grado" class="form-control">
                                                <option value="">Todos los grados</option>
                                                <?php
                                                mysqli_data_seek($grados, 0);
                                                while($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)){
                                                    echo '<option value="'.$grado['gra_id'].'">'.$grado['gra_nombre'].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="filtro-item">
                                            <label><b>Grupo:</b></label>
                                            <select id="filtro-grupo" class="form-control">
                                                <option value="">Todos los grupos</option>
                                            </select>
                                        </div>
                                        <div class="filtro-item">
                                            <button type="button" class="btn btn-secondary" id="btn-limpiar-filtros">
                                                <i class="fa fa-times"></i> Limpiar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Lista de estudiantes -->
                                <div id="lista-estudiantes">
                                    <?php foreach($estudiantesConDatos as $est): ?>
                                        <div class="estudiante-card asociado" 
                                             data-estudiante-id="<?= $est['mat_id'] ?>"
                                             data-nombre="<?= htmlspecialchars(strtolower($est['nombre']), ENT_QUOTES, 'UTF-8') ?>"
                                             data-primer-nombre="<?= htmlspecialchars($est['primer_nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                             data-segundo-nombre="<?= htmlspecialchars($est['segundo_nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                             data-primer-apellido="<?= htmlspecialchars($est['primer_apellido'], ENT_QUOTES, 'UTF-8') ?>"
                                             data-segundo-apellido="<?= htmlspecialchars($est['segundo_apellido'], ENT_QUOTES, 'UTF-8') ?>"
                                             data-documento="<?= htmlspecialchars(strtolower($est['documento']), ENT_QUOTES, 'UTF-8') ?>"
                                             data-grado="<?= $est['grado'] ?>"
                                             data-grupo="<?= $est['grupo'] ?>">
                                            <div class="estudiante-datos">
                                                <i class="fa fa-check-circle icono-check"></i>
                                                <div class="estudiante-info">
                                                    <div class="estudiante-nombre"><?= htmlspecialchars($est['nombre']) ?></div>
                                                    <div class="estudiante-detalles">
                                                        <i class="fa fa-id-card"></i> Documento: <?= is_numeric($est['documento']) ? number_format((float)$est['documento'], 0, '.', '.') : htmlspecialchars($est['documento']) ?>
                                                    </div>
                                                    <div class="estudiante-detalles">
                                                        <i class="fa fa-graduation-cap"></i> <?= htmlspecialchars($est['grado_nombre']) ?> - <?= htmlspecialchars($est['grupo_nombre']) ?>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn-accion btn-quitar" data-estudiante-id="<?= $est['mat_id'] ?>">
                                                        <i class="fa fa-times"></i> Quitar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Mensaje cuando no hay estudiantes asociados -->
                                <?php if(empty($estudiantesConDatos)): ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> Este acudiente no tiene estudiantes asociados. Use los filtros para buscar y asociar estudiantes.
                                    </div>
                                <?php endif; ?>

                                <!-- Formulario oculto para guardar -->
                                <form name="formularioGuardar" action="usuarios-acudidos-actualizar.php" method="post" id="form-guardar">
                                    <input type="hidden" value="<?= base64_decode($_GET['id']) ?>" name="id">
                                    <div id="inputs-acudidos"></div>
                                </form>
                                
                                <!-- Botón de guardar -->
                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-12 text-right">
                                        <button type="button" class="btn btn-primary" id="btn-guardar-cambios" <?= $disabledPermiso ?>>
                                            <i class="fa fa-save"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    (function() {
                        'use strict';
                        
                        // Esperar a que jQuery esté disponible
                        if (typeof jQuery === 'undefined') {
                            console.error('jQuery no está disponible');
                            return;
                        }
                        
                            jQuery(document).ready(function($) {
                            let estudiantesAsociados = <?= json_encode($estudiantesAsociados) ?>;
                            let hayFiltrosActivos = false;
                            
                            // Guardar el HTML inicial de los estudiantes asociados para restaurar después
                            const htmlInicialEstudiantes = $('#lista-estudiantes').html();
                            
                            // Función para actualizar contador
                            function actualizarContador() {
                                const visibles = $('.estudiante-card:visible').length;
                                const totalCargados = $('.estudiante-card').length;
                                const contadorVisibles = $('#contador-visibles');
                                const contadorTotal = $('#contador-total');
                                const contadorAsociados = $('#contador-asociados');
                                
                                if (contadorVisibles.length) contadorVisibles.text(visibles);
                                if (contadorTotal.length) contadorTotal.text(totalCargados);
                                if (contadorAsociados.length) contadorAsociados.text(estudiantesAsociados.length);
                            }
                            
                            // Función para actualizar inputs del formulario
                            function actualizarInputsFormulario() {
                                const container = $('#inputs-acudidos');
                                if (!container.length) return;
                                
                                container.empty();
                                estudiantesAsociados.forEach(function(id) {
                                    container.append('<input type="hidden" name="acudidos[]" value="' + id + '">');
                                });
                            }
                            
                            // Función para guardar cambios
                            function guardarCambios() {
                                actualizarInputsFormulario();
                                const form = $('#form-guardar');
                                if (form.length) {
                                    form.submit();
                                }
                            }
                            
                            // Agregar estudiante
                            $(document).on('click', '.btn-agregar', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                const $btn = $(this);
                                const estudianteId = $btn.data('estudiante-id');
                                
                                if (!estudianteId) {
                                    console.error('No se encontró el ID del estudiante');
                                    return;
                                }
                                
                                if (!estudiantesAsociados.includes(estudianteId)) {
                                    estudiantesAsociados.push(estudianteId);
                                    const card = $btn.closest('.estudiante-card');
                                    
                                    card.addClass('asociado');
                                    $btn.removeClass('btn-agregar').addClass('btn-quitar')
                                        .html('<i class="fa fa-times"></i> Quitar');
                                    
                                    const datosDiv = card.find('.estudiante-info');
                                    if (datosDiv.length && !datosDiv.find('.icono-check').length) {
                                        datosDiv.prepend('<i class="fa fa-check-circle icono-check"></i>');
                                    }
                                    
                                    // Remover información de acudiente
                                    card.find('.estudiante-acudiente').remove();
                                    
                                    actualizarContador();
                                    actualizarInputsFormulario();
                                }
                            });
                            
                            // Quitar estudiante
                            $(document).on('click', '.btn-quitar', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                const $btn = $(this);
                                const estudianteId = $btn.data('estudiante-id');
                                
                                if (!estudianteId) {
                                    console.error('No se encontró el ID del estudiante');
                                    return;
                                }
                                
                                estudiantesAsociados = estudiantesAsociados.filter(function(id) {
                                    return id !== estudianteId;
                                });
                                
                                const card = $btn.closest('.estudiante-card');
                                card.removeClass('asociado');
                                $btn.removeClass('btn-quitar').addClass('btn-agregar')
                                    .html('<i class="fa fa-plus"></i> Asociar');
                                card.find('.icono-check').remove();
                                
                                // Si no hay filtros activos, ocultar la tarjeta
                                if (!hayFiltrosActivos) {
                                    card.hide();
                                }
                                
                                actualizarContador();
                                actualizarInputsFormulario();
                            });
                            
                            // Botón guardar cambios
                            const btnGuardar = $('#btn-guardar-cambios');
                            if (btnGuardar.length) {
                                btnGuardar.on('click', function(e) {
                                    e.preventDefault();
                                    guardarCambios();
                                });
                            }
                            
                            // Búsqueda
                            const busquedaInput = $('#busqueda-estudiantes');
                            if (busquedaInput.length) {
                                busquedaInput.on('keyup', function() {
                                    filtrarEstudiantes();
                                });
                            }
                            
                            // Filtros
                            $('#filtro-grado, #filtro-grupo').on('change', function() {
                                filtrarEstudiantes();
                            });
                            
                            // Limpiar filtros
                            const btnLimpiar = $('#btn-limpiar-filtros');
                            if (btnLimpiar.length) {
                                btnLimpiar.on('click', function(e) {
                                    e.preventDefault();
                                    
                                    // Limpiar campos de filtro
                                    $('#busqueda-estudiantes').val('');
                                    $('#filtro-grado, #filtro-grupo').val('');
                                    hayFiltrosActivos = false;
                                    
                                    // Restaurar el HTML inicial (solo estudiantes asociados)
                                    const listaEstudiantes = $('#lista-estudiantes');
                                    listaEstudiantes.html(htmlInicialEstudiantes);
                                    
                                    // Actualizar contadores
                                    actualizarContador();
                                    actualizarInputsFormulario();
                                });
                            }
                            
                            // Función para cargar estudiantes desde el servidor
                            let cargandoEstudiantes = false;
                            function cargarEstudiantesFiltrados() {
                                if (cargandoEstudiantes) return;
                                
                                const busqueda = busquedaInput.length ? busquedaInput.val().trim() : '';
                                const filtroGrado = $('#filtro-grado').val();
                                const filtroGrupo = $('#filtro-grupo').val();
                                
                                // Determinar si hay filtros activos
                                hayFiltrosActivos = !!(busqueda || filtroGrado || filtroGrupo);
                                
                                // Si no hay filtros activos, solo mostrar los asociados que ya están cargados
                                if (!hayFiltrosActivos) {
                                    $('.estudiante-card').each(function() {
                                        const card = $(this);
                                        if (card.hasClass('asociado')) {
                                            card.show();
                                        } else {
                                            card.hide();
                                        }
                                    });
                                    actualizarContador();
                                    return;
                                }
                                
                                // Si hay filtros, hacer petición AJAX
                                cargandoEstudiantes = true;
                                const listaEstudiantes = $('#lista-estudiantes');
                                
                                // Mostrar indicador de carga
                                listaEstudiantes.html('<div class="text-center" style="padding: 40px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Cargando estudiantes...</div>');
                                
                                $.ajax({
                                    url: 'ajax-estudiantes-filtrados.php',
                                    method: 'POST',
                                    data: {
                                        acudiente_id: '<?= $acudienteId ?>',
                                        busqueda: busqueda,
                                        grado: filtroGrado,
                                        grupo: filtroGrupo
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        cargandoEstudiantes = false;
                                        
                                        if (response.error) {
                                            listaEstudiantes.html('<div class="alert alert-danger">Error al cargar estudiantes: ' + response.error + '</div>');
                                            return;
                                        }
                                        
                                        // Limpiar lista actual
                                        listaEstudiantes.empty();
                                        
                                        // Agregar estudiantes recibidos
                                        if (response.estudiantes && response.estudiantes.length > 0) {
                                            response.estudiantes.forEach(function(est) {
                                                const estaAsociado = est.asociado || estudiantesAsociados.includes(est.mat_id);
                                                const cardHtml = `
                                                    <div class="estudiante-card ${estaAsociado ? 'asociado' : ''}" 
                                                         data-estudiante-id="${est.mat_id}"
                                                         data-nombre="${est.nombre.toLowerCase()}"
                                                         data-primer-nombre="${est.primer_nombre}"
                                                         data-segundo-nombre="${est.segundo_nombre}"
                                                         data-primer-apellido="${est.primer_apellido}"
                                                         data-segundo-apellido="${est.segundo_apellido}"
                                                         data-documento="${est.documento ? est.documento.toLowerCase() : ''}"
                                                         data-grado="${est.grado || ''}"
                                                         data-grupo="${est.grupo || ''}">
                                                        <div class="estudiante-datos">
                                                            ${estaAsociado ? '<i class="fa fa-check-circle icono-check"></i>' : ''}
                                                            <div class="estudiante-info">
                                                                <div class="estudiante-nombre">${est.nombre}</div>
                                                                <div class="estudiante-detalles">
                                                                    <i class="fa fa-id-card"></i> Documento: ${est.documento && !isNaN(est.documento) ? parseFloat(est.documento).toLocaleString('es-ES') : est.documento}
                                                                </div>
                                                                <div class="estudiante-detalles">
                                                                    <i class="fa fa-graduation-cap"></i> ${est.grado_nombre} - ${est.grupo_nombre}
                                                                </div>
                                                                ${!estaAsociado ? `<div class="estudiante-acudiente"><i class="fa fa-user"></i> Acudiente: ${est.acudiente_nombre}</div>` : ''}
                                                            </div>
                                                            <div>
                                                                ${estaAsociado ? 
                                                                    `<button type="button" class="btn-accion btn-quitar" data-estudiante-id="${est.mat_id}"><i class="fa fa-times"></i> Quitar</button>` :
                                                                    `<button type="button" class="btn-accion btn-agregar" data-estudiante-id="${est.mat_id}"><i class="fa fa-plus"></i> Asociar</button>`
                                                                }
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                                                listaEstudiantes.append(cardHtml);
                                            });
                                        } else {
                                            listaEstudiantes.html('<div class="alert alert-info"><i class="fa fa-info-circle"></i> No se encontraron estudiantes con los filtros aplicados.</div>');
                                        }
                                        
                                        actualizarContador();
                                    },
                                    error: function(xhr, status, error) {
                                        cargandoEstudiantes = false;
                                        listaEstudiantes.html('<div class="alert alert-danger">Error al cargar estudiantes. Por favor, intente nuevamente.</div>');
                                        console.error('Error AJAX:', error);
                                    }
                                });
                            }
                            
                            // Función de filtrado (mantener para compatibilidad, pero ahora usa AJAX)
                            function filtrarEstudiantes() {
                                // Usar debounce para evitar múltiples peticiones
                                clearTimeout(window.filtroTimeout);
                                window.filtroTimeout = setTimeout(function() {
                                    cargarEstudiantesFiltrados();
                                }, 500); // Esperar 500ms después del último cambio
                            }
                            
                            // Inicializar
                            actualizarContador();
                            actualizarInputsFormulario();
                        });
                    })();
                </script>
                </div>
                <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>
