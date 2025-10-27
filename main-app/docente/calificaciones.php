<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0035';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");

// Obtener valores para validaciones del modal
$valores = Actividades::consultarValores($config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajeRestante = 100 - $valores[0];
?>
<style>
/* Overlay de bloqueo mientras se guarda */
#overlay-guardando-nota {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(30, 41, 59, 0.85);
	z-index: 99999;
	backdrop-filter: blur(6px);
}

.overlay-content-nota {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background: white;
	padding: 45px 50px;
	border-radius: 20px;
	text-align: center;
	box-shadow: 0 20px 60px rgba(0,0,0,0.4);
	min-width: 320px;
}

.overlay-content-nota .spinner {
	width: 70px;
	height: 70px;
	border: 5px solid #e2e8f0;
	border-top-color: #667eea;
	border-radius: 50%;
	margin: 0 auto 25px;
	animation: spin 1s linear infinite;
}

@keyframes spin {
	to { transform: rotate(360deg); }
}

.overlay-content-nota h3 {
	color: #2d3748;
	margin: 0 0 10px 0;
	font-size: 22px;
	font-weight: 700;
}

.overlay-content-nota p {
	color: #718096;
	margin: 0;
	font-size: 15px;
}
</style>

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>

<!-- Overlay de bloqueo mientras se guarda la nota -->
<div id="overlay-guardando-nota">
	<div class="overlay-content-nota">
		<div class="spinner"></div>
		<h3>💾 Guardando Nota...</h3>
		<p>Por favor espera, no cierres esta ventana</p>
	</div>
</div>

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
                                <div class="page-title"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
					<?php include(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                    <?php include("includes/barra-superior-informacion-actual.php"); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
									
								<div class="col-md-12">

									<nav>
										<div class="nav nav-tabs" id="nav-tab" role="tablist">

											<a class="nav-item nav-link" id="nav-calificaciones-tab" data-toggle="tab" href="#nav-calificaciones" role="tab" aria-controls="nav-calificaciones" aria-selected="true" onClick="listarInformacion('listar-calificaciones.php', 'nav-calificaciones')">Calificaciones</a>

											<a class="nav-item nav-link" id="nav-calificaciones-todas-tab" data-toggle="tab" href="#nav-calificaciones-todas" role="tab" aria-controls="nav-calificaciones-todas" aria-selected="true" onClick="listarInformacion('listar-calificaciones-todas.php', 'nav-calificaciones-todas')">Resumen de notas</a>
											
											<?php if(isset($datosCargaActual) && $datosCargaActual['car_observaciones_boletin']==1){?>
												<a class="nav-item nav-link" id="nav-observaciones-tab" data-toggle="tab" href="#nav-observaciones" role="tab" aria-controls="nav-observaciones" aria-selected="true" onClick="listarInformacion('listar-observaciones.php', 'nav-observaciones')">Observaciones</a>
											<?php }?>

											<a class="nav-item nav-link" id="nav-periodos-resumen-tab" data-toggle="tab" href="#nav-periodos-resumen" role="tab" aria-controls="nav-periodos-resumen" aria-selected="true" onClick="listarInformacion('listar-periodos-resumen.php', 'nav-periodos-resumen')">Resumen por periodos</a>

										</div>
									</nav>

									<div class="tab-content" id="nav-tabContent">
										
										<div class="tab-pane fade" id="nav-calificaciones" role="tabpanel" aria-labelledby="nav-calificaciones-tab"></div>

										<div class="tab-pane fade" id="nav-calificaciones-todas" role="tabpanel" aria-labelledby="nav-calificaciones-todas-tab"></div>

										<div class="tab-pane fade" id="nav-observaciones" role="tabpanel" aria-labelledby="nav-observaciones-tab"></div>

										<div class="tab-pane fade" id="nav-periodos-resumen" role="tabpanel" aria-labelledby="nav-periodos-resumen-tab"></div>

									</div>

                                </div>

								<script>
									document.addEventListener('DOMContentLoaded', function() {
										console.log('🔵 Inicializando tabs de calificaciones');
										
										// Obtén la cadena de búsqueda de la URL
										var queryString = window.location.search;
										console.log('URL params:', queryString);

										// Crea un objeto URLSearchParams a partir de la cadena de búsqueda
										var params = new URLSearchParams(queryString);
										var tab = params.get('tab');
										
										if ( tab == 2 ) {
											console.log('📂 Cargando tab 2');
											listarInformacion('listar-calificaciones-todas.php', 'nav-calificaciones-todas');
											document.getElementById('nav-calificaciones-todas-tab').classList.add('active');
											document.getElementById('nav-calificaciones-todas').classList.add('show', 'active');
										}
										else if ( tab == 3 ) {
											console.log('📂 Cargando tab 3');
											listarInformacion('listar-observaciones.php', 'nav-observaciones');
											document.getElementById('nav-observaciones-tab').classList.add('active');
											document.getElementById('nav-observaciones').classList.add('show', 'active');
										}
										else if ( tab == 4 ) {
											console.log('📂 Cargando tab 4');
											listarInformacion('listar-periodos-resumen.php', 'nav-periodos-resumen');
											document.getElementById('nav-periodos-resumen-tab').classList.add('active');
											document.getElementById('nav-periodos-resumen').classList.add('show', 'active');
										}
										else {
											console.log('📂 Cargando tab 1');
											listarInformacion('listar-calificaciones.php', 'nav-calificaciones');
											document.getElementById('nav-calificaciones-tab').classList.add('active');
											document.getElementById('nav-calificaciones').classList.add('show', 'active');
										}
									});
								</script>
							
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    
    <!-- MODAL AGREGAR ACTIVIDAD -->
<div class="modal fade" id="modalAgregarActividad" tabindex="-1" role="dialog" aria-labelledby="modalAgregarActividadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <!-- Header del Modal -->
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 25px 30px;">
                <h4 class="modal-title" id="modalAgregarActividadLabel" style="font-weight: 700; font-size: 1.5rem; margin: 0;">
                    <i class="fa fa-plus-circle"></i> Agregar Nueva Actividad
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1; text-shadow: none; font-size: 2rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body del Modal -->
            <div class="modal-body" style="padding: 30px; background: #f8f9fa;">
                <form id="formAgregarActividad">
                    <input type="hidden" name="carga" value="<?=$cargaConsultaActual;?>">
                    <input type="hidden" name="periodo" value="<?=$periodoConsultaActual;?>">
                    
                    <!-- Descripción -->
                    <div class="form-group">
                        <label style="font-weight: 600; color: #2d3748; font-size: 0.95rem; margin-bottom: 8px;">
                            <i class="fa fa-file-text-o text-primary"></i> Descripción de la Actividad
                            <span style="color: #dc3545;">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="contenido" 
                            id="contenido" 
                            class="form-control" 
                            placeholder="Ej: Quiz capítulo 3, Taller de laboratorio, Examen final..."
                            style="border-radius: 10px; padding: 12px 15px; border: 2px solid #e2e8f0; font-size: 0.95rem;"
                            required
                        >
                    </div>

                    <!-- Fecha -->
                    <?php if($datosCargaActual['car_fecha_automatica'] != 1){ ?>
                    <div class="form-group">
                        <label style="font-weight: 600; color: #2d3748; font-size: 0.95rem; margin-bottom: 8px;">
                            <i class="fa fa-calendar text-info"></i> Fecha
                            <span style="color: #dc3545;">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="fecha" 
                            id="fecha" 
                            class="form-control" 
                            value="<?=date('Y-m-d');?>"
                            style="border-radius: 10px; padding: 12px 15px; border: 2px solid #e2e8f0; font-size: 0.95rem;"
                            required
                        >
                    </div>
                    <?php } else { ?>
                    <input type="hidden" name="fecha" value="<?=date('Y-m-d');?>">
                    <?php } ?>

                    <!-- Indicador -->
                    <?php if($datosCargaActual['car_indicador_automatico'] != 1){ ?>
                    <div class="form-group">
                        <label style="font-weight: 600; color: #2d3748; font-size: 0.95rem; margin-bottom: 8px;">
                            <i class="fa fa-list-alt text-success"></i> Indicador
                            <span style="color: #dc3545;">*</span>
                        </label>
                        <select 
                            class="form-control select2-modal" 
                            name="indicador" 
                            id="indicador"
                            style="border-radius: 10px; padding: 12px 15px; border: 2px solid #e2e8f0; font-size: 0.95rem;"
                            required
                        >
                            <option value="">Seleccione un indicador</option>
                            <?php
                            $indicadoresConsulta = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
                            while($indicadoresDatos = mysqli_fetch_array($indicadoresConsulta, MYSQLI_BOTH)){
                            ?>
                                <option value="<?=$indicadoresDatos['ai_ind_id'];?>">
                                    <?=$indicadoresDatos['ind_nombre']." (".$indicadoresDatos['ipc_valor']."%)"?>
                                </option>
                            <?php }?>
                        </select>
                    </div>
                    <?php } else { 
                        $indDef = Indicadores::consultarIndicadoresDefinitivos();
                        $indicadorAuto = !empty($indDef['ind_id']) ? $indDef['ind_id'] : null;
                        
                        if (empty($indDef['ind_id'])) {
                    ?>
                        <div class="alert alert-danger" style="border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Atención:</strong> No hay indicador definitivo configurado. No podrás crear actividades hasta que se configure.
                        </div>
                    <?php 
                        }
                    ?>
                    <input type="hidden" name="indicador" value="<?=$indicadorAuto;?>">
                    <?php } ?>

                    <!-- Evidencia -->
                    <?php if($datosCargaActual['car_evidencia'] == 1){ ?>
                    <div class="form-group">
                        <label style="font-weight: 600; color: #2d3748; font-size: 0.95rem; margin-bottom: 8px;">
                            <i class="fa fa-paperclip text-warning"></i> Evidencia
                            <span style="color: #dc3545;">*</span>
                        </label>
                        <select 
                            class="form-control select2-modal" 
                            name="evidencia" 
                            id="evidencia"
                            style="border-radius: 10px; padding: 12px 15px; border: 2px solid #e2e8f0; font-size: 0.95rem;"
                            required
                        >
                            <option value="">Seleccione una evidencia</option>
                            <?php
                            $evidenciasConsulta = Calificaciones::traerEvidenciasInstitucion($config);
                            while($evidenciasDatos = mysqli_fetch_array($evidenciasConsulta, MYSQLI_BOTH)){
                            ?>
                                <option value="<?=$evidenciasDatos['evid_id'];?>">
                                    <?=$evidenciasDatos['evid_nombre']." (".$evidenciasDatos['evid_valor']."%)"?>
                                </option>
                            <?php }?>
                        </select>
                    </div>
                    <?php } else { ?>
                    <input type="hidden" name="evidencia" value="0">
                    <?php } ?>

                    <!-- Valor (solo si es configuración manual) -->
                    <?php if($datosCargaActual['car_configuracion'] == 1){ ?>
                    <div class="alert" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                        <i class="fa fa-info-circle text-warning"></i>
                        <strong>Importante:</strong> Este valor no debe superar al valor del indicador al que pertenece.
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 600; color: #2d3748; font-size: 0.95rem; margin-bottom: 8px;">
                            <i class="fa fa-percent text-danger"></i> Valor (%)
                            <span style="color: #dc3545;">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="valor" 
                            id="valor" 
                            class="form-control" 
                            placeholder="Ej: 20"
                            min="1"
                            max="100"
                            step="0.01"
                            style="border-radius: 10px; padding: 12px 15px; border: 2px solid #e2e8f0; font-size: 0.95rem;"
                            required
                        >
                        <small class="form-text text-muted">
                            <i class="fa fa-lightbulb-o"></i> Porcentaje disponible del indicador seleccionado
                        </small>
                    </div>
                    <?php } ?>

                    <!-- Compartir con otros docentes -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox" style="padding-left: 0;">
                            <label style="font-weight: 500; color: #64748b; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input 
                                    type="checkbox" 
                                    name="compartir" 
                                    value="1"
                                    style="width: 20px; height: 20px; cursor: pointer;"
                                >
                                <span>
                                    <i class="fa fa-share-alt text-primary"></i> 
                                    Compartir esta actividad con otros docentes de esta materia
                                </span>
                            </label>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Footer del Modal -->
            <div class="modal-footer" style="border-top: 2px solid #e2e8f0; padding: 20px 30px; background: white;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px; padding: 10px 25px; font-weight: 600;">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarActividad" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 10px 30px; font-weight: 600;">
                    <i class="fa fa-save"></i> Guardar Actividad
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- Select2 -->
	<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
    
    <!-- Script para Modal de Actividades -->
    <script>
    (function() {
        'use strict';
        
        // Esperar a que jQuery y todos los plugins estén cargados
        $(document).ready(function() {
            
            console.log('✅ Script de modal de actividades cargado');
            
            // ==========================================
            // INICIALIZAR SELECT2 EN EL MODAL
            // ==========================================
            $('#modalAgregarActividad').on('shown.bs.modal', function () {
                console.log('📂 Modal abierto, inicializando Select2');
                
                $('.select2-modal').select2({
                    dropdownParent: $('#modalAgregarActividad'),
                    width: '100%'
                });
                
                // Enfocar el primer campo
                setTimeout(function() {
                    $('#contenido').focus();
                }, 300);
            });

            // ==========================================
            // LIMPIAR FORMULARIO AL CERRAR MODAL
            // ==========================================
            $('#modalAgregarActividad').on('hidden.bs.modal', function () {
                console.log('❌ Modal cerrado, limpiando formulario');
                
                $('#formAgregarActividad')[0].reset();
                
                if (typeof $('.select2-modal').select2 === 'function') {
                    $('.select2-modal').val(null).trigger('change');
                }
                
                // Remover bordes de validación
                $('#formAgregarActividad input, #formAgregarActividad select').css('border', '');
            });

            // ==========================================
            // GUARDAR ACTIVIDAD VÍA AJAX
            // ==========================================
            $('#btnGuardarActividad').on('click', function() {
                const $btn = $(this);
                const $form = $('#formAgregarActividad');
                
                console.log('💾 Click en guardar actividad');
                
                // Validar formulario
                if (!$form[0].checkValidity()) {
                    $form[0].reportValidity();
                    return;
                }

                // Obtener datos del formulario
                const formData = $form.serialize();
                
                console.log('🔵 Iniciando guardado de actividad:', formData);

                // Deshabilitar botón y mostrar loading
                $btn.prop('disabled', true);
                const textOriginal = $btn.html();
                $btn.html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

                // Mostrar overlay
                const overlay = $('#overlay-guardando-nota');
                if (overlay.length) {
                    overlay.find('h3').text('💾 Creando Actividad...');
                    overlay.find('p').text('Por favor espera, estamos guardando la actividad');
                    overlay.show();
                }

                // Petición AJAX
                $.ajax({
                    type: 'POST',
                    url: 'ajax-calificaciones-agregar.php',
                    data: formData,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {
                        console.log('✅ Respuesta del servidor:', response);

                        if (response.success) {
                            // ✅ ÉXITO: Toast de confirmación
                            $.toast({
                                heading: '✅ Actividad Creada',
                                text: response.message || 'La actividad se creó correctamente',
                                position: 'top-right',
                                loaderBg: '#28a745',
                                icon: 'success',
                                hideAfter: 4000,
                                stack: 1
                            });

                            // Cerrar modal
                            $('#modalAgregarActividad').modal('hide');

                            // Recargar la lista de calificaciones
                            setTimeout(function() {
                                // Determinar qué tab está activo
                                if ($('#nav-calificaciones').hasClass('active')) {
                                    listarInformacion('listar-calificaciones.php', 'nav-calificaciones');
                                } else if ($('#nav-calificaciones-todas').hasClass('active')) {
                                    listarInformacion('listar-calificaciones-todas.php', 'nav-calificaciones-todas');
                                } else {
                                    // Por defecto recargar la página
                                    window.location.reload();
                                }
                            }, 500);

                        } else {
                            // ❌ ERROR del servidor
                            console.error('❌ Error del servidor:', response.message);
                            
                            $.toast({
                                heading: '❌ Error al Crear',
                                text: response.message || 'No se pudo crear la actividad',
                                position: 'top-right',
                                loaderBg: '#dc3545',
                                icon: 'error',
                                hideAfter: 7000,
                                stack: 1
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error en petición AJAX:', {
                            status: status,
                            error: error,
                            statusCode: xhr.status,
                            response: xhr.responseText
                        });

                        let mensajeError = 'Error desconocido al crear la actividad';
                        
                        // Intentar parsear la respuesta JSON
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                mensajeError = response.message;
                            }
                        } catch (e) {
                            // Si no es JSON, usar mensajes por defecto
                            if (status === 'timeout') {
                                mensajeError = 'La conexión tardó demasiado. Verifica tu internet.';
                            } else if (status === 'error') {
                                mensajeError = 'Error de conexión. Verifica tu internet y reintenta.';
                            } else if (xhr.status === 500) {
                                mensajeError = 'Error del servidor. Contacta al administrador.';
                            } else if (xhr.status === 404) {
                                mensajeError = 'No se encontró el archivo del servidor.';
                            }
                        }

                        $.toast({
                            heading: '❌ Error de Conexión',
                            text: mensajeError,
                            position: 'top-right',
                            loaderBg: '#dc3545',
                            icon: 'error',
                            hideAfter: 8000,
                            stack: 1
                        });
                    },
                    complete: function() {
                        // Restaurar botón
                        $btn.prop('disabled', false);
                        $btn.html(textOriginal);

                        // Ocultar overlay
                        const overlay = $('#overlay-guardando-nota');
                        if (overlay.length) {
                            overlay.find('h3').text('💾 Guardando Nota...');
                            overlay.find('p').text('Por favor espera, no cierres esta ventana');
                            overlay.hide();
                        }
                    }
                });
            });

            // ==========================================
            // ATAJOS DE TECLADO EN EL MODAL
            // ==========================================
            $('#modalAgregarActividad').on('keydown', function(e) {
                // Enter en el formulario dispara guardar
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    $('#btnGuardarActividad').click();
                }
                
                // Escape cierra el modal
                if (e.key === 'Escape') {
                    $('#modalAgregarActividad').modal('hide');
                }
            });

        });
    })();
    </script>

</body>

</html>