/**
 * SISTEMA DE REGISTRO MODERNO CON VALIDACIONES EN TIEMPO REAL
 * Plataforma Educativa SINTIA
 * Versión 2.0
 */

// Variables globales
let currentStep = 1;
let idRegistro = null;
let countdownInterval = null;
let isEmailValid = false;
let isCodeVerified = false;
let attemptCount = 0;

// Configuración
const CONFIG = {
    RECAPTCHA_SITE_KEY: '6LfH9KkqAAAAABRKx0bvP7I6TH-r9K_zy6DxfzRO',
    CODE_EXPIRY_TIME: 600, // 10 minutos en segundos
    RESEND_DELAY: 60, // 60 segundos antes de poder reenviar
    EMAIL_DEBOUNCE_TIME: 800, // Esperar 800ms después de que el usuario deje de escribir
};

/**
 * Inicialización cuando el DOM está listo
 */
$(document).ready(function() {
    console.log('Inicializando formulario de registro...');
    
    initializeForm();
    initializeValidations();
    initializeCodeInputs();
    initializePlanSelection();
    attachEventListeners();
    
    console.log('Formulario inicializado correctamente');
});

/**
 * Inicializa el formulario y configuraciones básicas
 */
function initializeForm() {
    // Prevenir envío del formulario con Enter
    $('#registrationForm').on('submit', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Ejecutar reCAPTCHA cuando sea necesario (si está disponible)
    if (typeof grecaptcha !== 'undefined') {
        grecaptcha.ready(function() {
            console.log('reCAPTCHA está listo');
        });
    }
}

/**
 * Adjunta todos los event listeners a los botones
 */
function attachEventListeners() {
    // Botones de navegación
    $('#btnStep1Next').on('click', function() {
        console.log('Botón Step 1 Next clickeado');
        nextStep(2);
    });
    
    $('#btnStep2Prev').on('click', function() {
        console.log('Botón Step 2 Prev clickeado');
        prevStep(1);
    });
    
    $('#btnStep2Next').on('click', function() {
        console.log('Botón Step 2 Next clickeado');
        nextStep(3);
    });
    
    $('#btnStep3Prev').on('click', function() {
        console.log('Botón Step 3 Prev clickeado');
        prevStep(2);
    });
    
    // Botón de verificar código
    $('#btnVerificar').on('click', function() {
        console.log('Botón Verificar clickeado');
        verificarCodigo();
    });
    
    // Botón de reenviar código
    $('#btnReenviar').on('click', function() {
        console.log('Botón Reenviar clickeado');
        reenviarCodigo();
    });
    
    console.log('Event listeners adjuntados correctamente');
}

/**
 * Inicializa todas las validaciones en tiempo real
 */
function initializeValidations() {
    // Validación de nombre
    $('#nombre').on('input', function() {
        validateName($(this));
    });
    
    // Validación de apellidos
    $('#apellidos').on('input', function() {
        validateName($(this));
    });
    
    // Validación de email con debounce
    let emailTimeout;
    $('#email').on('input', function() {
        const $input = $(this);
        const email = $input.val().trim();
        
        clearTimeout(emailTimeout);
        
        // Si el campo está vacío, no mostrar nada
        if (email === '') {
            $input.removeClass('is-valid is-invalid');
            $input.siblings('small').hide();
            isEmailValid = false;
            return;
        }
        
        $input.removeClass('is-valid is-invalid');
        $input.siblings('small').text('Verificando disponibilidad...').show();
        
        emailTimeout = setTimeout(function() {
            validateEmail($input);
        }, CONFIG.EMAIL_DEBOUNCE_TIME);
    });
    
    // Validación de celular
    $('#celular').on('input', function() {
        validatePhone($(this));
    });
    
    // Validaciones del paso 2
    $('#nombreIns, #ciudad, #cargo').on('input', function() {
        validateRequired($(this));
    });
}

/**
 * Valida nombres (solo letras y espacios)
 */
function validateName($input) {
    const value = $input.val().trim();
    const nameRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
    
    if (value === '') {
        $input.removeClass('is-valid is-invalid');
        return false;
    }
    
    if (value.length < 2) {
        $input.removeClass('is-valid').addClass('is-invalid');
        $input.siblings('.invalid-feedback').text('Debe tener al menos 2 caracteres.');
        return false;
    }
    
    if (!nameRegex.test(value)) {
        $input.removeClass('is-valid').addClass('is-invalid');
        $input.siblings('.invalid-feedback').text('Solo se permiten letras y espacios.');
        return false;
    }
    
    $input.removeClass('is-invalid').addClass('is-valid');
    return true;
}

/**
 * Valida email y verifica disponibilidad
 */
async function validateEmail($input) {
    const email = $input.val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    // Ocultar mensaje de "verificando"
    $input.siblings('small').hide();
    
    if (email === '') {
        $input.removeClass('is-valid is-invalid');
        isEmailValid = false;
        return false;
    }
    
    if (!emailRegex.test(email)) {
        $input.removeClass('is-valid').addClass('is-invalid');
        $input.siblings('.invalid-feedback').text('Por favor ingrese un correo válido.');
        $input.siblings('small').hide();
        isEmailValid = false;
        return false;
    }
    
    // Verificar disponibilidad con el servidor
    try {
        const response = await fetch(`validar-registro.php?action=checkEmail&email=${encodeURIComponent(email)}`);
        
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        
        const data = await response.json();
        
        // Ocultar mensaje de verificando
        $input.siblings('small').hide();
        
        if (data.available) {
            $input.removeClass('is-invalid').addClass('is-valid');
            $input.siblings('.valid-feedback').text('¡Correo disponible!');
            isEmailValid = true;
            return true;
        } else {
            $input.removeClass('is-valid').addClass('is-invalid');
            $input.siblings('.invalid-feedback').text('Este correo ya está registrado.');
            isEmailValid = false;
            return false;
        }
    } catch (error) {
        console.error('Error validando email:', error);
        // En caso de error, asumir que el email es válido para no bloquear el registro
        $input.removeClass('is-valid is-invalid');
        $input.siblings('small').hide();
        isEmailValid = true; // Permitir continuar en caso de error del servidor
        return true;
    }
}

/**
 * Valida teléfono (10 dígitos)
 */
function validatePhone($input) {
    const phone = $input.val().trim();
    const phoneRegex = /^[0-9]{10}$/;
    
    if (phone === '') {
        $input.removeClass('is-valid is-invalid');
        return false;
    }
    
    if (!phoneRegex.test(phone)) {
        $input.removeClass('is-valid').addClass('is-invalid');
        $input.siblings('.invalid-feedback').text('Debe tener exactamente 10 dígitos.');
        return false;
    }
    
    $input.removeClass('is-invalid').addClass('is-valid');
    return true;
}

/**
 * Valida campos requeridos
 */
function validateRequired($input) {
    const value = $input.val().trim();
    
    if (value === '') {
        $input.removeClass('is-valid is-invalid');
        return false;
    }
    
    if (value.length < 3) {
        $input.removeClass('is-valid').addClass('is-invalid');
        return false;
    }
    
    $input.removeClass('is-invalid').addClass('is-valid');
    return true;
}

/**
 * Valida un paso completo del formulario
 */
function validateStep(step) {
    let isValid = true;
    
    if (step === 1) {
        // Validar datos personales
        isValid = validateName($('#nombre')) && isValid;
        isValid = validateName($('#apellidos')) && isValid;
        isValid = isEmailValid && isValid;
        isValid = validatePhone($('#celular')) && isValid;
        
        if (!isValid) {
            showAlert('Por favor complete correctamente todos los campos.', 'error');
        }
    } 
    else if (step === 2) {
        // Validar datos de institución
        isValid = validateRequired($('#nombreIns')) && isValid;
        isValid = validateRequired($('#ciudad')) && isValid;
        isValid = validateRequired($('#cargo')) && isValid;
        
        // Validar que se haya seleccionado al menos un módulo
        const modulosSeleccionados = $('input[name="modulos[]"]:checked').length;
        if (modulosSeleccionados === 0) {
            showAlert('Por favor seleccione al menos un módulo de su interés.', 'error');
            isValid = false;
        }
        
        if (!isValid) {
            showAlert('Por favor complete correctamente todos los campos y seleccione al menos un módulo.', 'error');
        }
    }
    
    return isValid;
}

/**
 * Navega al siguiente paso
 */
async function nextStep(step) {
    const previousStep = currentStep;
    
    // Validar paso actual antes de continuar
    if (!validateStep(previousStep)) {
        return;
    }
    
    // Si pasamos al paso 3, enviamos el código
    if (step === 3) {
        const success = await enviarCodigoVerificacion();
        if (!success) {
            return;
        }
    }
    
    // Ocultar paso actual
    $(`#step${previousStep}`).removeClass('active');
    $('.step-item').eq(previousStep - 1).removeClass('active').addClass('completed');
    
    // Mostrar nuevo paso
    currentStep = step;
    $(`#step${step}`).addClass('active');
    $('.step-item').eq(step - 1).addClass('active');
    
    // Actualizar barra de progreso
    updateProgressBar();
    
    // Scroll suave al inicio
    $('html, body').animate({ scrollTop: 0 }, 300);
}

/**
 * Navega al paso anterior
 */
function prevStep(step) {
    // Ocultar paso actual
    $(`#step${currentStep}`).removeClass('active');
    $('.step-item').eq(currentStep - 1).removeClass('active');
    
    // Mostrar paso anterior
    currentStep = step;
    $(`#step${step}`).addClass('active');
    $('.step-item').eq(step - 1).addClass('active').removeClass('completed');
    
    // Actualizar barra de progreso
    updateProgressBar();
    
    // Scroll suave al inicio
    $('html, body').animate({ scrollTop: 0 }, 300);
}

/**
 * Actualiza la barra de progreso
 */
function updateProgressBar() {
    const progress = ((currentStep - 1) / 2) * 100;
    $('#progressFill').css('width', progress + '%');
}

/**
 * Inicializa los inputs del código de verificación
 */
function initializeCodeInputs() {
    const inputs = $('.code-input');
    
    // Navegar entre inputs
    inputs.on('input', function() {
        const $this = $(this);
        const index = $this.data('index');
        const value = $this.val();
        
        // Solo permitir números
        if (!/^\d$/.test(value)) {
            $this.val('');
            return;
        }
        
        // Mover al siguiente input
        if (value && index < 5) {
            inputs.eq(index + 1).focus();
        }
        
        // Verificar automáticamente si todos están completos
        checkCodeComplete();
    });
    
    // Manejar backspace
    inputs.on('keydown', function(e) {
        const $this = $(this);
        const index = $this.data('index');
        
        if (e.key === 'Backspace' && !$this.val() && index > 0) {
            inputs.eq(index - 1).focus();
        }
    });
    
    // Manejar pegado de código
    inputs.first().on('paste', function(e) {
        e.preventDefault();
        const pastedData = e.originalEvent.clipboardData.getData('text');
        
        if (/^\d{6}$/.test(pastedData)) {
            pastedData.split('').forEach((char, index) => {
                inputs.eq(index).val(char);
            });
            inputs.last().focus();
            checkCodeComplete();
        } else {
            showAlert('Por favor pegue un código válido de 6 dígitos.', 'error');
        }
    });
}

/**
 * Verifica si el código está completo y lo valida automáticamente
 */
function checkCodeComplete() {
    const inputs = $('.code-input');
    let code = '';
    let isComplete = true;
    
    inputs.each(function() {
        const val = $(this).val();
        if (!val) {
            isComplete = false;
            return false;
        }
        code += val;
    });
    
    if (isComplete) {
        // Pequeño delay para mejor UX
        setTimeout(() => verificarCodigo(), 300);
    }
}

/**
 * Envía el código de verificación al email
 */
async function enviarCodigoVerificacion() {
    try {
        attemptCount++;
        
        // Obtener reCAPTCHA token
        const recaptchaToken = await getRecaptchaToken('send_code');
        
        const nombre = $('#nombre').val().trim();
        const apellidos = $('#apellidos').val().trim();
        const email = $('#email').val().trim();
        const celular = $('#celular').val().trim();
        
        // Actualizar email en el paso 3
        $('#emailDisplay').text(email);
        
        const response = await fetch('enviar-codigo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                nombre: nombre,
                apellidos: apellidos,
                email: email,
                celular: celular,
                recaptchaToken: recaptchaToken,
                attempt: attemptCount
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            idRegistro = data.idRegistro;
            $('#idRegistro').val(idRegistro);
            
            // Iniciar countdown
            startCountdown(CONFIG.CODE_EXPIRY_TIME);
            
            if (attemptCount > 1) {
                showAlert('Se ha enviado un nuevo código a tu correo electrónico.', 'success');
            }
            
            return true;
        } else {
            showAlert(data.message || 'Error al enviar el código. Por favor intenta de nuevo.', 'error');
            return false;
        }
    } catch (error) {
        console.error('Error enviando código:', error);
        showAlert('Error de conexión. Por favor verifica tu internet e intenta de nuevo.', 'error');
        return false;
    }
}

/**
 * Verifica el código ingresado
 */
async function verificarCodigo() {
    const inputs = $('.code-input');
    let code = '';
    
    inputs.each(function() {
        code += $(this).val();
    });
    
    if (code.length !== 6) {
        showAlert('Por favor ingrese el código completo de 6 dígitos.', 'error');
        return;
    }
    
    // Mostrar loading
    const $btnVerificar = $('#btnVerificar');
    $btnVerificar.prop('disabled', true);
    $('#verifySpinner').show();
    
    try {
        const response = await fetch('validar-codigo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                code: code,
                idRegistro: idRegistro
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            isCodeVerified = true;
            showAlert('¡Código verificado exitosamente! Procesando tu registro...', 'success');
            
            // Detener countdown
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            // Esperar 2 segundos y enviar formulario
            setTimeout(() => {
                submitForm();
            }, 2000);
        } else {
            showAlert(data.message || 'Código incorrecto. Por favor verifica e intenta de nuevo.', 'error');
            
            // Limpiar inputs
            inputs.val('');
            inputs.first().focus();
            
            // Habilitar botón de reenvío
            enableResendButton();
        }
    } catch (error) {
        console.error('Error verificando código:', error);
        showAlert('Error de conexión. Por favor intenta de nuevo.', 'error');
    } finally {
        $btnVerificar.prop('disabled', false);
        $('#verifySpinner').hide();
    }
}

/**
 * Reenvía el código de verificación
 */
async function reenviarCodigo() {
    $('#btnReenviar').prop('disabled', true);
    
    const success = await enviarCodigoVerificacion();
    
    if (success) {
        showAlert('Se ha enviado un nuevo código a tu correo electrónico.', 'success');
        
        // Limpiar inputs
        $('.code-input').val('');
        $('.code-input').first().focus();
        
        // Deshabilitar botón de reenvío temporalmente
        setTimeout(() => {
            $('#btnReenviar').prop('disabled', false);
        }, CONFIG.RESEND_DELAY * 1000);
    } else {
        $('#btnReenviar').prop('disabled', false);
    }
}

/**
 * Inicia el countdown del código
 */
function startCountdown(seconds) {
    let timeLeft = seconds;
    
    const updateCountdown = () => {
        const minutes = Math.floor(timeLeft / 60);
        const secs = timeLeft % 60;
        
        $('#countdown').text(`${minutes}:${secs.toString().padStart(2, '0')}`);
        
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            $('#countdown').parent().removeClass('alert-info').addClass('alert-danger');
            $('#countdown').parent().html('<i class="bi bi-exclamation-triangle me-2"></i>El código ha expirado');
            enableResendButton();
        }
        
        timeLeft--;
    };
    
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
    
    // Habilitar botón de reenvío después del delay
    setTimeout(() => {
        $('#btnReenviar').prop('disabled', false);
    }, CONFIG.RESEND_DELAY * 1000);
}

/**
 * Habilita el botón de reenvío
 */
function enableResendButton() {
    $('#btnReenviar').prop('disabled', false);
}

/**
 * Obtiene el token de reCAPTCHA (opcional - no bloquea el flujo)
 */
function getRecaptchaToken(action) {
    return new Promise((resolve, reject) => {
        // Si reCAPTCHA no está disponible, continuar sin él
        if (typeof grecaptcha === 'undefined') {
            console.warn('reCAPTCHA no disponible, continuando sin validación');
            resolve('RECAPTCHA_NOT_AVAILABLE');
            return;
        }
        
        try {
            grecaptcha.ready(function() {
                grecaptcha.execute(CONFIG.RECAPTCHA_SITE_KEY, { action: action })
                    .then(function(token) {
                        resolve(token);
                    })
                    .catch(function(error) {
                        console.warn('Error reCAPTCHA, continuando sin validación:', error);
                        resolve('RECAPTCHA_ERROR');
                    });
            });
        } catch (error) {
            console.warn('Error al inicializar reCAPTCHA:', error);
            resolve('RECAPTCHA_ERROR');
        }
    });
}

/**
 * Envía el formulario final
 */
async function submitForm() {
    try {
        // Obtener token final de reCAPTCHA
        const recaptchaToken = await getRecaptchaToken('submit_registration');
        $('#recaptchaToken').val(recaptchaToken);
        
        // Enviar formulario
        $('#registrationForm').off('submit').submit();
    } catch (error) {
        console.error('Error al enviar formulario:', error);
        showAlert('Error al procesar el registro. Por favor intenta de nuevo.', 'error');
    }
}

/**
 * Inicializa la selección de módulos
 */
function initializePlanSelection() {
    // Ya no se usa para planes, ahora es para módulos
    $('.modulo-card').on('click', function() {
        const checkbox = $(this).find('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked'));
        $(this).toggleClass('selected', checkbox.prop('checked'));
        updateModulosCounter();
    });
    
    // Evitar que el click en el checkbox duplique el toggle
    $('.modulo-card input[type="checkbox"]').on('click', function(e) {
        e.stopPropagation();
        $(this).closest('.modulo-card').toggleClass('selected', $(this).prop('checked'));
        updateModulosCounter();
    });
}

/**
 * Actualiza el contador de módulos seleccionados
 */
function updateModulosCounter() {
    const total = $('input[name="modulos[]"]:checked').length;
    $('#modulosCounter').text(total);
    
    if (total > 0) {
        $('#modulosSeleccionadosInfo').show();
    } else {
        $('#modulosSeleccionadosInfo').hide();
    }
}

/**
 * Muestra un mensaje de alerta
 */
function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success-custom' : 'alert-error-custom';
    const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
    
    const alertHtml = `
        <div class="alert alert-custom ${alertClass} animate__animated animate__fadeInDown" role="alert">
            <i class="bi ${icon} me-2"></i>${message}
        </div>
    `;
    
    $('#verificationMessage').html(alertHtml);
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        $('#verificationMessage .alert').addClass('animate__fadeOutUp');
        setTimeout(() => {
            $('#verificationMessage').empty();
        }, 500);
    }, 5000);
}

// Exponer funciones globalmente para onclick handlers
window.nextStep = nextStep;
window.prevStep = prevStep;
window.verificarCodigo = verificarCodigo;
window.reenviarCodigo = reenviarCodigo;

// Log de confirmación de carga
console.log('✅ registro-new.js cargado correctamente');
console.log('✅ nextStep está disponible:', typeof window.nextStep);
console.log('✅ prevStep está disponible:', typeof window.prevStep);

