/**
 * Sistema de Validación de Fechas
 * Aplicable para páginas con campos "Desde" y "Hasta"
 * 
 * Uso:
 * 1. Agregar clases form_datetime_desde y form_datetime_hasta a los inputs
 * 2. Incluir este script después de jQuery y Bootstrap DateTimePicker
 * 3. Llamar a inicializarValidacionFechas() en $(document).ready()
 */

window.inicializarValidacionFechas = function(mostrarLogs = true) {
    if (mostrarLogs) {
        console.log('🔵 Inicializando validaciones de fechas');
    }
    
    // Obtener fecha actual (sin hora para comparación de días)
    var hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    
    // ==========================================
    // CONFIGURAR DATETIMEPICKER "DESDE"
    // ==========================================
    $('.form_datetime_desde').datetimepicker({
        format: 'dd MM yyyy - HH:ii p',
        autoclose: true,
        todayBtn: true,
        startDate: new Date(), // ✅ No permite fechas anteriores a hoy
        pickerPosition: "bottom-left",
        language: 'es',
        minuteStep: 5
    }).on('changeDate', function(e) {
        var fechaDesde = e.date;
        if (mostrarLogs) {
            console.log('📅 Fecha DESDE cambiada:', fechaDesde);
        }
        
        // Validar que no sea en el pasado
        if (fechaDesde < hoy) {
            $.toast({
                heading: '⚠️ Fecha No Válida',
                text: 'La fecha de inicio no puede ser anterior a hoy',
                position: 'top-right',
                loaderBg: '#f39c12',
                icon: 'warning',
                hideAfter: 4000
            });
            
            // Resetear a hoy
            $('.form_datetime_desde').datetimepicker('setDate', new Date());
            return;
        }
        
        // Actualizar fecha mínima del "hasta"
        var fechaDesdeConMargen = new Date(fechaDesde);
        fechaDesdeConMargen.setMinutes(fechaDesdeConMargen.getMinutes() + 1); // Al menos 1 minuto después
        
        $('.form_datetime_hasta').datetimepicker('setStartDate', fechaDesdeConMargen);
        
        // Validar fecha "hasta" actual
        validarFechas();
    });
    
    // ==========================================
    // CONFIGURAR DATETIMEPICKER "HASTA"
    // ==========================================
    $('.form_datetime_hasta').datetimepicker({
        format: 'dd MM yyyy - HH:ii p',
        autoclose: true,
        todayBtn: true,
        startDate: new Date(), // ✅ No permite fechas anteriores a hoy
        pickerPosition: "bottom-left",
        language: 'es',
        minuteStep: 5
    }).on('changeDate', function(e) {
        var fechaHasta = e.date;
        if (mostrarLogs) {
            console.log('📅 Fecha HASTA cambiada:', fechaHasta);
        }
        
        // Validar contra fecha "desde"
        validarFechas();
    });
    
    // ==========================================
    // FUNCIÓN DE VALIDACIÓN
    // ==========================================
    window.validarFechas = function() {
        var fechaDesdeStr = $('#dtp_input1').val();
        var fechaHastaStr = $('#dtp_input2').val();
        
        if (!fechaDesdeStr || !fechaHastaStr) {
            return true; // Si alguna está vacía, no validar aún
        }
        
        var fechaDesde = new Date(fechaDesdeStr);
        var fechaHasta = new Date(fechaHastaStr);
        
        if (mostrarLogs) {
            console.log('🔍 Validando fechas:', {
                desde: fechaDesdeStr,
                hasta: fechaHastaStr
            });
        }
        
        // Validación 1: Fecha "desde" no puede ser en el pasado
        if (fechaDesde < hoy) {
            mostrarError('desde', 'La fecha de inicio no puede ser anterior a hoy');
            $('.form_datetime_desde').datetimepicker('setDate', new Date());
            return false;
        }
        
        // Validación 2: Fecha "hasta" debe ser posterior a "desde"
        if (fechaHasta <= fechaDesde) {
            mostrarError('hasta', 'La fecha límite debe ser posterior a la fecha de inicio');
            
            // Ajustar automáticamente "hasta" para que sea 1 día después de "desde"
            var nuevaFechaHasta = new Date(fechaDesde);
            nuevaFechaHasta.setDate(nuevaFechaHasta.getDate() + 1);
            nuevaFechaHasta.setHours(23, 59, 59);
            
            $('.form_datetime_hasta').datetimepicker('setDate', nuevaFechaHasta);
            
            return false;
        }
        
        // ✅ Fechas válidas
        actualizarMensaje('valido');
        return true;
    };
    
    // ==========================================
    // MOSTRAR MENSAJES DE ERROR/VALIDACIÓN
    // ==========================================
    function mostrarError(campo, mensaje) {
        $.toast({
            heading: '⚠️ Fecha No Válida',
            text: mensaje,
            position: 'top-right',
            loaderBg: '#f39c12',
            icon: 'warning',
            hideAfter: 4000,
            stack: 1
        });
        
        if (mostrarLogs) {
            console.warn('⚠️ Validación de fecha:', mensaje);
        }
    }
    
    function actualizarMensaje(tipo) {
        var $mensaje = $('#mensajeFechaHasta');
        if ($mensaje.length === 0) return;
        
        if (tipo === 'valido') {
            $mensaje.removeClass('text-warning text-danger').addClass('text-success');
            $mensaje.html('<i class="fa fa-check-circle"></i> Fechas configuradas correctamente');
            
            setTimeout(function() {
                $mensaje.removeClass('text-success').addClass('text-warning');
                $mensaje.html('<i class="fa fa-exclamation-triangle"></i> La fecha límite debe ser posterior a la fecha de inicio');
            }, 3000);
        }
    }
    
    // ==========================================
    // VALIDAR INPUTS OCULTOS (por si editan manualmente)
    // ==========================================
    $('#dtp_input1, #dtp_input2').on('change', function() {
        if (mostrarLogs) {
            console.log('🔧 Input oculto modificado manualmente');
        }
        validarFechas();
    });
    
    // ==========================================
    // VALIDACIÓN FINAL ANTES DE ENVIAR
    // ==========================================
    $('form[name="formularioGuardar"]').on('submit', function(e) {
        if (mostrarLogs) {
            console.log('📝 Validando formulario antes de enviar...');
        }
        
        if (!validarFechas()) {
            e.preventDefault();
            e.stopPropagation();
            
            $.toast({
                heading: '❌ Error de Validación',
                text: 'Por favor corrige las fechas antes de continuar',
                position: 'top-right',
                loaderBg: '#dc3545',
                icon: 'error',
                hideAfter: 5000,
                stack: 1
            });
            
            return false;
        }
        
        if (mostrarLogs) {
            console.log('✅ Fechas validadas correctamente, enviando formulario');
        }
        return true;
    });
    
    // Validación inicial al cargar
    setTimeout(function() {
        validarFechas();
    }, 1000);
    
    if (mostrarLogs) {
        console.log('✅ Sistema de validación de fechas activado');
    }
};

