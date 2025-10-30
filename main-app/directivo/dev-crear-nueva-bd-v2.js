/**
 * Sistema de Creación de Instituciones - Wizard Moderno
 * Validaciones en tiempo real y proceso asíncrono
 */

// Variables globales
let currentStep = 1;
const totalSteps = 5;
const formData = {
    tipoInsti: '',
    // Nueva institución
    nombreInsti: '',
    siglasInst: '',
    siglasBD: '',
    yearN: new Date().getFullYear(),
    // Renovación
    idInsti: '',
    yearA: new Date().getFullYear(),
    enviarCorreoRenovacion: '0',
    // Contacto (solo nueva)
    tipoDoc: '',
    documento: '',
    nombre1: '',
    nombre2: '',
    apellido1: '',
    apellido2: '',
    email: '',
    celular: '',
    usuarioAcceso: '',
    enviarCorreoBienvenida: '0'
};

// Timers para validaciones
const validationTimers = {};

/**
 * Inicializar wizard
 */
$(document).ready(function() {
    console.log('Wizard initialized');
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Validar campos en tiempo real
    setupRealTimeValidation();
    
    // Event listeners específicos
    $('#siglasBD').on('input', updateBDPreview);
    $('#idInsti').on('change', handleInstitucionChange);
    
    // Prevenir envío de formulario con Enter
    $(document).on('keypress', 'input', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            return false;
        }
    });
});

/**
 * Seleccionar opción en cards
 */
function selectOption(fieldId, value) {
    // Remover selección anterior
    $('.card-option').removeClass('selected');
    
    // Marcar como seleccionado
    const selectedOption = value === '1' ? 'nueva' : 'renovacion';
    $(`.card-option[data-option="${selectedOption}"]`).addClass('selected');
    
    // Guardar valor
    $(`#${fieldId}`).val(value);
    formData[fieldId] = value;
    
    // Habilitar botón siguiente
    enableNextButton();
}

/**
 * Navegación - Siguiente paso
 */
function nextStep() {
    if (!validateCurrentStep()) {
        showToast('error', 'Por favor completa todos los campos requeridos correctamente');
        return;
    }
    
    // Guardar datos del paso actual
    saveCurrentStepData();
    
    // Avanzar al siguiente paso
    currentStep++;
    
    // Casos especiales
    if (currentStep === 3 && formData.tipoInsti === '0') {
        // Si es renovación, saltar el paso 3 (contacto)
        currentStep = 4;
    }
    
    if (currentStep > totalSteps) {
        currentStep = totalSteps;
        return;
    }
    
    renderStep();
}

/**
 * Navegación - Paso anterior
 */
function previousStep() {
    if (currentStep <= 1) return;
    
    currentStep--;
    
    // Casos especiales
    if (currentStep === 3 && formData.tipoInsti === '0') {
        // Si es renovación, saltar el paso 3 hacia atrás
        currentStep = 2;
    }
    
    renderStep();
}

/**
 * Renderizar paso actual
 */
function renderStep() {
    // Actualizar indicadores de pasos
    $('.wizard-step').removeClass('active completed');
    $('.wizard-step').each(function() {
        const stepNum = parseInt($(this).data('step'));
        if (stepNum < currentStep) {
            $(this).addClass('completed');
        } else if (stepNum === currentStep) {
            $(this).addClass('active');
        }
    });
    
    // Mostrar sección correspondiente
    $('.wizard-section').removeClass('active');
    $(`.wizard-section[data-section="${currentStep}"]`).addClass('active');
    
    // Actualizar botones
    updateButtons();
    
    // Configuración especial por paso
    switch(currentStep) {
        case 2:
            showCorrectSection();
            break;
        case 3:
            // Contacto principal - solo para nuevas
            break;
        case 4:
            buildConfirmation();
            break;
        case 5:
            // Procesamiento - se maneja en procesarCreacion()
            break;
    }
    
    // Scroll al inicio
    $('.wizard-content').scrollTop(0);
}

/**
 * Actualizar botones de navegación
 */
function updateButtons() {
    const $btnPrev = $('#btnPrev');
    const $btnNext = $('#btnNext');
    const $btnSubmit = $('#btnSubmit');
    
    // Botón anterior
    if (currentStep === 1 || currentStep === 5) {
        $btnPrev.hide();
    } else {
        $btnPrev.show();
    }
    
    // Botón siguiente vs submit
    if (currentStep === 4) {
        $btnNext.hide();
        $btnSubmit.show();
    } else if (currentStep === 5) {
        $btnNext.hide();
        $btnSubmit.hide();
    } else {
        $btnNext.show();
        $btnSubmit.hide();
    }
}

/**
 * Mostrar sección correcta según tipo de institución
 */
function showCorrectSection() {
    if (formData.tipoInsti === '1') {
        $('#datosNuevaInstitucion').show();
        $('#datosRenovacion').hide();
    } else {
        $('#datosNuevaInstitucion').hide();
        $('#datosRenovacion').show();
    }
}

/**
 * Validar paso actual
 */
function validateCurrentStep() {
    switch(currentStep) {
        case 1:
            return formData.tipoInsti !== '';
        case 2:
            if (formData.tipoInsti === '1') {
                return validateNuevaInstitucion();
            } else {
                return validateRenovacion();
            }
        case 3:
            return validateContacto();
        case 4:
            return true; // Ya está validado en pasos anteriores
        default:
            return true;
    }
}

/**
 * Validar datos de nueva institución
 */
function validateNuevaInstitucion() {
    const required = ['nombreInsti', 'siglasInst', 'siglasBD', 'yearN'];
    let isValid = true;
    
    required.forEach(field => {
        const $input = $(`#${field}`);
        const value = $input.val().trim();
        
        if (!value) {
            markFieldError($input, 'Este campo es requerido');
            isValid = false;
        }
    });
    
    // Validación especial para siglasBD
    const siglasBD = $('#siglasBD').val().trim();
    if (siglasBD && !/^[a-z0-9_]+$/.test(siglasBD)) {
        markFieldError($('#siglasBD'), 'Solo letras minúsculas, números y guión bajo');
        isValid = false;
    }
    
    return isValid;
}

/**
 * Validar datos de renovación
 */
function validateRenovacion() {
    const $idInsti = $('#idInsti');
    const $yearA = $('#yearA');
    
    if (!$idInsti.val()) {
        markFieldError($idInsti, 'Debes seleccionar una institución');
        return false;
    }
    
    if (!$yearA.val()) {
        markFieldError($yearA, 'Debes especificar el año');
        return false;
    }
    
    return true;
}

/**
 * Validar datos de contacto
 */
function validateContacto() {
    // Solo se valida para nuevas instituciones
    if (formData.tipoInsti !== '1') {
        return true;
    }
    
    const required = ['tipoDoc', 'documento', 'nombre1', 'apellido1', 'email', 'usuarioAcceso'];
    let isValid = true;
    
    required.forEach(field => {
        const $input = $(`#${field}`);
        const value = $input.val().trim();
        
        if (!value) {
            markFieldError($input, 'Este campo es requerido');
            isValid = false;
        }
    });
    
    // Validar formato de email
    const email = $('#email').val().trim();
    if (email && !validateEmail(email)) {
        markFieldError($('#email'), 'Formato de correo inválido');
        isValid = false;
    }
    
    // Validar formato de usuario
    const usuario = $('#usuarioAcceso').val().trim();
    if (usuario && !/^[a-zA-Z0-9._-]+$/.test(usuario)) {
        markFieldError($('#usuarioAcceso'), 'Solo letras, números y guiones permitidos');
        isValid = false;
    }
    
    return isValid;
}

/**
 * Guardar datos del paso actual
 */
function saveCurrentStepData() {
    switch(currentStep) {
        case 1:
            formData.tipoInsti = $('#tipoInsti').val();
            break;
        case 2:
            if (formData.tipoInsti === '1') {
                formData.nombreInsti = $('#nombreInsti').val().trim();
                formData.siglasInst = $('#siglasInst').val().trim();
                formData.siglasBD = $('#siglasBD').val().trim();
                formData.yearN = $('#yearN').val();
            } else {
                formData.idInsti = $('#idInsti').val();
                formData.yearA = $('#yearA').val();
                formData.enviarCorreoRenovacion = $('#enviarCorreoRenovacion').is(':checked') ? '1' : '0';
            }
            break;
        case 3:
            formData.tipoDoc = $('#tipoDoc').val();
            formData.documento = $('#documento').val().trim();
            formData.nombre1 = $('#nombre1').val().trim();
            formData.nombre2 = $('#nombre2').val().trim();
            formData.apellido1 = $('#apellido1').val().trim();
            formData.apellido2 = $('#apellido2').val().trim();
            formData.email = $('#email').val().trim();
            formData.celular = $('#celular').val().trim();
            formData.usuarioAcceso = $('#usuarioAcceso').val().trim();
            formData.enviarCorreoBienvenida = $('#enviarCorreoBienvenida').is(':checked') ? '1' : '0';
            break;
    }
}

/**
 * Construir resumen de confirmación
 */
function buildConfirmation() {
    let html = '';
    
    if (formData.tipoInsti === '1') {
        // Nueva institución
        html = `
            <h3 style="color: #667eea; margin-bottom: 20px;">
                <i class="fa fa-building"></i> Nueva Institución
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <strong>Nombre:</strong><br>
                    <span style="font-size: 16px;">${formData.nombreInsti}</span>
                </div>
                <div>
                    <strong>Siglas:</strong><br>
                    <span style="font-size: 16px;">${formData.siglasInst}</span>
                </div>
                <div>
                    <strong>Base de Datos:</strong><br>
                    <code style="background: #e9ecef; padding: 4px 8px; border-radius: 4px;">
                        mobiliar_${formData.siglasBD}_${formData.yearN}
                    </code>
                </div>
                <div>
                    <strong>Año:</strong><br>
                    <span style="font-size: 16px;">${formData.yearN}</span>
                </div>
            </div>
            
            <hr style="margin: 30px 0; border-color: #e9ecef;">
            
            <h4 style="color: #667eea; margin-bottom: 15px;">
                <i class="fa fa-user"></i> Contacto Principal
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <strong>Nombre Completo:</strong><br>
                    <span style="font-size: 16px;">
                        ${formData.nombre1} ${formData.nombre2} ${formData.apellido1} ${formData.apellido2}
                    </span>
                </div>
                <div>
                    <strong>Documento:</strong><br>
                    <span style="font-size: 16px;">${formData.documento}</span>
                </div>
                <div>
                    <strong>Correo:</strong><br>
                    <span style="font-size: 16px;">${formData.email}</span>
                </div>
                <div>
                    <strong>Celular:</strong><br>
                    <span style="font-size: 16px;">${formData.celular || 'No especificado'}</span>
                </div>
                <div>
                    <strong>Usuario de Acceso:</strong><br>
                    <span style="font-size: 16px; color: #667eea; font-weight: 600;">${formData.usuarioAcceso}</span>
                </div>
                <div style="grid-column: 1 / -1;">
                    <strong>Correo de Bienvenida:</strong><br>
                    <span style="font-size: 16px;">
                        ${formData.enviarCorreoBienvenida === '1' ? 
                            '<span style="color: #28a745;"><i class="fa fa-check-circle"></i> Se enviará correo con credenciales</span>' : 
                            '<span style="color: #6c757d;"><i class="fa fa-times-circle"></i> No se enviará correo</span>'}
                    </span>
                </div>
            </div>
        `;
    } else {
        // Renovación
        const $selectedInsti = $('#idInsti option:selected');
        html = `
            <h3 style="color: #667eea; margin-bottom: 20px;">
                <i class="fa fa-refresh"></i> Renovación de Año
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <strong>Institución:</strong><br>
                    <span style="font-size: 16px;">${$selectedInsti.text()}</span>
                </div>
                <div>
                    <strong>ID:</strong><br>
                    <span style="font-size: 16px;">${formData.idInsti}</span>
                </div>
                <div>
                    <strong>Año a Crear:</strong><br>
                    <span style="font-size: 16px;">${formData.yearA}</span>
                </div>
                <div>
                    <strong>Año Anterior:</strong><br>
                    <span style="font-size: 16px;">${formData.yearA - 1}</span>
                </div>
            </div>
            
            <div style="margin-top: 30px; padding: 20px; background: #e7f3ff; border-left: 4px solid #2196F3; border-radius: 8px;">
                <strong>Se copiarán los siguientes datos del año ${formData.yearA - 1}:</strong>
                <ul style="margin: 15px 0 0; padding-left: 25px;">
                    <li>Cursos y grupos</li>
                    <li>Áreas y materias</li>
                    <li>Usuarios (docentes, estudiantes, acudientes)</li>
                    <li>Matrículas</li>
                    <li>Cargas académicas</li>
                    <li>Configuraciones institucionales</li>
                    <li>Información general de la institución</li>
                </ul>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: ${formData.enviarCorreoRenovacion === '1' ? '#d4edda' : '#f8f9fa'}; border-radius: 8px;">
                <strong>Correo de Confirmación:</strong><br>
                <span style="font-size: 16px;">
                    ${formData.enviarCorreoRenovacion === '1' ? 
                        '<span style="color: #28a745;"><i class="fa fa-check-circle"></i> Se enviará correo de confirmación al contacto principal</span>' : 
                        '<span style="color: #6c757d;"><i class="fa fa-times-circle"></i> No se enviará correo de confirmación</span>'}
                </span>
            </div>
        `;
    }
    
    $('#confirmacionResumen').html(html);
}

/**
 * Procesar creación
 */
function procesarCreacion() {
    // Deshabilitar botón
    $('#btnSubmit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
    
    // Ir al paso 5
    currentStep = 5;
    renderStep();
    
    // Iniciar proceso asíncrono
    startCreationProcess();
}

/**
 * Iniciar proceso de creación
 */
async function startCreationProcess() {
    try {
        addProgressLog('info', 'Iniciando proceso de creación...');
        console.log('Form Data:', formData);
        updateProgress(5);
        
        // Paso 1: Validar datos previos
        addProgressLog('info', 'Validando datos...');
        const validacion = await validateBeforeCreate();
        updateProgress(15);
        
        console.log('Validación:', validacion);
        
        if (!validacion.success) {
            throw new Error(validacion.message);
        }
        addProgressLog('success', 'Datos validados correctamente');
        
        // Paso 2: Crear/Renovar en BD
        addProgressLog('info', 'Creando estructura en base de datos...');
        console.log('Tipo de institución:', formData.tipoInsti);
        updateProgress(30);
        
        const resultado = await createInDatabase();
        console.log('Resultado creación:', resultado);
        updateProgress(90);
        
        if (!resultado.success) {
            throw new Error(resultado.message);
        }
        
        addProgressLog('success', 'Estructura creada exitosamente');
        updateProgress(100);
        
        // Mostrar resultado exitoso
        showSuccessResult(resultado);
        
    } catch (error) {
        console.error('Error completo en proceso:', error);
        console.error('Stack:', error.stack);
        addProgressLog('error', 'Error: ' + error.message);
        showErrorResult(error.message);
    }
}

/**
 * Validar antes de crear
 */
async function validateBeforeCreate() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'ajax-crear-bd-validar.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                resolve(response);
            },
            error: function(xhr, status, error) {
                reject(new Error('Error en validación: ' + error));
            }
        });
    });
}

/**
 * Crear en base de datos
 */
async function createInDatabase() {
    // Usar el endpoint v2 para nuevas instituciones (más robusto)
    const endpoint = formData.tipoInsti === '1' ? 'ajax-crear-bd-procesar-v2.php' : 'ajax-crear-bd-procesar.php';
    
    return new Promise((resolve, reject) => {
        $.ajax({
            url: endpoint,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                resolve(response);
            },
            error: function(xhr, status, error) {
                // Intentar parsear la respuesta para obtener más detalles
                let errorMessage = 'Error en creación: ' + error;
                
                console.log('XHR Status:', xhr.status);
                console.log('XHR Response Text:', xhr.responseText);
                console.log('Error Status:', status);
                console.log('Error:', error);
                
                try {
                    const responseText = xhr.responseText;
                    if (responseText) {
                        // Si empieza con HTML, extraer el mensaje
                        if (responseText.indexOf('<') === 0) {
                            errorMessage = 'El servidor devolvió HTML. Revisa la consola (F12) para ver el HTML completo.';
                            console.error('===== RESPUESTA HTML COMPLETA =====');
                            console.error(responseText);
                            console.error('===== FIN RESPUESTA HTML =====');
                        } else {
                            // Intentar parsear como JSON
                            const jsonResponse = JSON.parse(responseText);
                            if (jsonResponse.message) {
                                errorMessage = jsonResponse.message;
                            }
                            if (jsonResponse.error_details) {
                                console.error('Detalles del error:', jsonResponse.error_details);
                            }
                        }
                    }
                } catch (e) {
                    console.error('Error al parsear respuesta:', e);
                }
                
                reject(new Error(errorMessage));
            },
            // Progress tracking
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                return xhr;
            }
        });
    });
}

/**
 * Actualizar barra de progreso
 */
function updateProgress(percent) {
    $('#progressBar').css('width', percent + '%').text(percent + '%');
}

/**
 * Agregar log de progreso
 */
function addProgressLog(type, message) {
    const icons = {
        info: 'ℹ️',
        success: '✅',
        error: '❌'
    };
    
    const icon = icons[type] || 'ℹ️';
    const html = `
        <div class="progress-log-item ${type}">
            <span>${icon}</span>
            <span>${message}</span>
            <small style="margin-left: auto; color: #6c757d;">
                ${new Date().toLocaleTimeString()}
            </small>
        </div>
    `;
    
    $('#progressLog').append(html);
    
    // Auto-scroll
    const logContainer = $('#progressLog')[0];
    logContainer.scrollTop = logContainer.scrollHeight;
}

/**
 * Mostrar resultado exitoso
 */
function showSuccessResult(data) {
    const esNueva = formData.tipoInsti === '1';
    const html = `
        <div style="text-align: center; padding: 40px;">
            <div style="font-size: 72px; color: #28a745; margin-bottom: 20px;">
                ✅
            </div>
            <h2 style="color: #28a745; margin-bottom: 15px;">
                ¡Proceso Completado Exitosamente!
            </h2>
            <p style="font-size: 16px; color: #6c757d; margin-bottom: 30px;">
                ${esNueva ? 'La institución ha sido creada' : 'El año académico ha sido renovado'} correctamente.
            </p>
            
            ${data.nota ? `
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 16px; border-radius: 8px; margin: 20px 0; text-align: left;">
                <strong>⚠️ Nota Importante:</strong><br>
                ${data.nota}
            </div>
            ` : ''}
            
            ${data.institucionId ? `
            <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: left;">
                <strong>📋 Información de Acceso:</strong><br>
                <ul style="margin: 15px 0 0; padding-left: 25px;">
                    <li>ID de Institución: <strong>${data.institucionId}</strong></li>
                    ${data.usuario ? `<li>Usuario: <strong style="color: #667eea;">${data.usuario}</strong></li>` : ''}
                    ${data.clave ? `<li>Contraseña: <strong style="color: #667eea;">${data.clave}</strong></li>` : ''}
                    ${data.email ? `<li>Email: <strong>${data.email}</strong></li>` : ''}
                </ul>
            </div>
            ` : ''}
            
            ${data.mensajeCorreo ? `
            <div style="background: ${data.correoEnviado ? '#d4edda' : '#fff3cd'}; border-left: 4px solid ${data.correoEnviado ? '#28a745' : '#ffc107'}; padding: 16px; border-radius: 8px; margin: 20px 0; text-align: left;">
                ${data.mensajeCorreo}
            </div>
            ` : ''}
            
            ${data.usuario && data.clave ? `
            <div style="background: #e7f3ff; border-left: 4px solid #2196F3; padding: 16px; border-radius: 8px; margin: 20px 0; text-align: left;">
                <strong>💡 Recuerda:</strong><br>
                <ul style="margin: 10px 0 0; padding-left: 25px;">
                    <li><strong>Guarda estas credenciales</strong> en un lugar seguro</li>
                    <li>Puedes compartirlas con el usuario administrativo</li>
                    <li>La contraseña se puede cambiar después del primer ingreso</li>
                </ul>
            </div>
            ` : ''}
            
            <button type="button" class="btn-wizard btn-wizard-submit" onclick="redirectToInstitutions()">
                <i class="fa fa-arrow-right"></i>
                Ir a Instituciones
            </button>
        </div>
    `;
    
    $('#resultadoFinal').html(html).show();
}

/**
 * Mostrar resultado de error
 */
function showErrorResult(message) {
    const html = `
        <div style="text-align: center; padding: 40px;">
            <div style="font-size: 72px; color: #dc3545; margin-bottom: 20px;">
                ❌
            </div>
            <h2 style="color: #dc3545; margin-bottom: 15px;">
                Error en el Proceso
            </h2>
            <p style="font-size: 16px; color: #6c757d; margin-bottom: 30px;">
                ${message}
            </p>
            
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: left;">
                <strong>Recomendaciones:</strong><br>
                <ul style="margin: 15px 0 0; padding-left: 25px;">
                    <li>Verifica que todos los datos sean correctos</li>
                    <li>Asegúrate de que la institución/año no exista ya</li>
                    <li>Contacta al administrador si el problema persiste</li>
                </ul>
            </div>
            
            <button type="button" class="btn-wizard btn-wizard-prev" onclick="location.reload()">
                <i class="fa fa-refresh"></i>
                Intentar Nuevamente
            </button>
        </div>
    `;
    
    $('#resultadoFinal').html(html).show();
}

/**
 * Configurar validación en tiempo real
 */
function setupRealTimeValidation() {
    // Validación de siglas BD
    $('#siglasBD').on('input', function() {
        clearTimeout(validationTimers['siglasBD']);
        const $input = $(this);
        const value = $input.val().trim().toLowerCase();
        
        // Formatear automáticamente
        $input.val(value.replace(/[^a-z0-9_]/g, ''));
        
        if (value.length >= 3) {
            validationTimers['siglasBD'] = setTimeout(() => {
                validateBDSiglas(value);
            }, 500);
        }
    });
    
    // Validación de email
    $('#email').on('blur', function() {
        const $input = $(this);
        const email = $input.val().trim();
        
        if (email) {
            if (validateEmail(email)) {
                markFieldSuccess($input, 'Correo válido');
            } else {
                markFieldError($input, 'Formato de correo inválido');
            }
        }
    });
    
    // Validación de documento
    $('#documento').on('blur', function() {
        const $input = $(this);
        const doc = $input.val().trim();
        
        if (doc && formData.tipoInsti === '1') {
            validateDocumento(doc);
        }
    });
    
    // Validación de usuario de acceso
    $('#usuarioAcceso').on('input', function() {
        const $input = $(this);
        let value = $input.val();
        
        // Formatear automáticamente: solo permitir letras, números, puntos, guiones y guión bajo
        value = value.replace(/[^a-zA-Z0-9._-]/g, '');
        $input.val(value);
        
        if (value.trim()) {
            if (/^[a-zA-Z0-9._-]{3,}$/.test(value)) {
                markFieldSuccess($input, 'Usuario válido');
            } else if (value.length > 0 && value.length < 3) {
                markFieldError($input, 'Mínimo 3 caracteres');
            }
        }
    });
    
    // Validación al cambiar checkbox de correo
    $('#enviarCorreoBienvenida').on('change', function() {
        checkStepCompletion();
    });
    
    // Validación al cambiar campos
    $('.form-control-modern').on('input change', function() {
        const $input = $(this);
        if ($input.val().trim()) {
            $input.removeClass('error');
            $input.siblings('.validation-message').removeClass('error').hide();
        }
        
        // Verificar si puede habilitar el botón siguiente
        checkStepCompletion();
    });
}

/**
 * Validar siglas de BD
 */
function validateBDSiglas(siglas) {
    const $input = $('#siglasBD');
    const $icon = $input.siblings('.input-icon');
    
    $icon.removeClass().addClass('input-icon loading').html('<i class="fa fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: 'ajax-crear-bd-validar-siglas.php',
        type: 'POST',
        data: { 
            siglasBD: siglas,
            tipoInsti: formData.tipoInsti
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                markFieldSuccess($input, response.message || 'Siglas disponibles');
            } else {
                markFieldError($input, response.message || 'Siglas no disponibles');
            }
        },
        error: function() {
            $icon.removeClass().addClass('input-icon');
        }
    });
}

/**
 * Validar documento
 */
function validateDocumento(documento) {
    const $input = $('#documento');
    const $icon = $input.siblings('.input-icon');
    
    $icon.removeClass().addClass('input-icon loading').html('<i class="fa fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: 'ajax-crear-bd-validar-documento.php',
        type: 'POST',
        data: { documento: documento },
        dataType: 'json',
        success: function(response) {
            if (response.exists) {
                markFieldError($input, 'Este documento ya está registrado');
            } else {
                markFieldSuccess($input, 'Documento disponible');
            }
        },
        error: function() {
            $icon.removeClass().addClass('input-icon');
        }
    });
}

/**
 * Actualizar preview de BD
 */
function updateBDPreview() {
    const siglas = $('#siglasBD').val().trim() || '___';
    $('#bdPreview').text(siglas);
}

/**
 * Manejar cambio de institución
 */
function handleInstitucionChange() {
    const $select = $('#idInsti');
    const $option = $select.find('option:selected');
    
    if ($select.val()) {
        const years = $option.data('years');
        const yearArray = years ? years.split(',') : [];
        const yearActual = yearArray[yearArray.length - 1] || '';
        const yearNuevo = parseInt(yearActual) + 1;
        
        $('#yearA').val(yearNuevo);
        $('#yearInfo').html(`
            <i class="fa fa-info-circle"></i> 
            Año actual: <strong>${yearActual}</strong> | 
            Año a crear: <strong>${yearNuevo}</strong>
        `);
        
        markFieldSuccess($select, 'Institución seleccionada');
    }
    
    checkStepCompletion();
}

/**
 * Verificar si el paso actual está completo
 */
function checkStepCompletion() {
    let isComplete = false;
    
    switch(currentStep) {
        case 1:
            isComplete = formData.tipoInsti !== '';
            break;
        case 2:
            if (formData.tipoInsti === '1') {
                isComplete = $('#nombreInsti').val().trim() && 
                            $('#siglasInst').val().trim() && 
                            $('#siglasBD').val().trim() && 
                            $('#yearN').val();
            } else {
                isComplete = $('#idInsti').val() && $('#yearA').val();
            }
            break;
        case 3:
            isComplete = $('#tipoDoc').val() && 
                        $('#documento').val().trim() && 
                        $('#nombre1').val().trim() && 
                        $('#apellido1').val().trim() && 
                        $('#email').val().trim() && 
                        $('#usuarioAcceso').val().trim();
            break;
    }
    
    enableNextButton(isComplete);
}

/**
 * Habilitar/deshabilitar botón siguiente
 */
function enableNextButton(enable = true) {
    $('#btnNext').prop('disabled', !enable);
}

/**
 * Marcar campo con error
 */
function markFieldError($input, message) {
    $input.removeClass('success').addClass('error');
    
    const $icon = $input.siblings('.input-icon');
    $icon.removeClass().addClass('input-icon error').html('<i class="fa fa-times-circle"></i>');
    
    const $msg = $input.siblings('.validation-message');
    $msg.removeClass('success info').addClass('error').text(message).show();
}

/**
 * Marcar campo con éxito
 */
function markFieldSuccess($input, message = '') {
    $input.removeClass('error').addClass('success');
    
    const $icon = $input.siblings('.input-icon');
    $icon.removeClass().addClass('input-icon success').html('<i class="fa fa-check-circle"></i>');
    
    if (message) {
        const $msg = $input.siblings('.validation-message');
        $msg.removeClass('error info').addClass('success').text(message).show();
        setTimeout(() => $msg.fadeOut(), 3000);
    }
}

/**
 * Validar formato de email
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Mostrar toast notification
 */
function showToast(type, message) {
    const types = {
        success: { icon: 'success', heading: '¡Éxito!' },
        error: { icon: 'error', heading: 'Error' },
        warning: { icon: 'warning', heading: 'Advertencia' },
        info: { icon: 'info', heading: 'Información' }
    };
    
    const config = types[type] || types.info;
    
    $.toast({
        heading: config.heading,
        text: message,
        position: 'top-right',
        loaderBg: '#667eea',
        icon: config.icon,
        hideAfter: 5000,
        stack: 5
    });
}

/**
 * Redirigir a instituciones
 */
function redirectToInstitutions() {
    window.location.href = 'dev-instituciones.php';
}

