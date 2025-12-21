var wizard = $("#wizard").steps();
 
// Add step
wizard.steps("add", {
    title: "HTML code", 
    content: "<strong>HTML code</strong>"
});

var form = $("#example-advanced-form").show();

form.steps({
    headerTag: "h3",
    bodyTag: "fieldset",
    transitionEffect: "slideLeft",
    labels: {
        finish: "Guardar matrícula",
        next: "Siguiente",
        previous: "Anterior"
    },
    onInit: function (event, currentIndex) {
        // Agregar el botón extra al iniciar el wizard
        addExtraButton();
        // Asegurar botón Siguiente habilitado inicialmente
        setNextEnabled(true);
        // Guardar etiqueta por defecto del botón Siguiente
        setNextDefaultLabel();
    },
    onStepChanging: function (event, currentIndex, newIndex)
    {
        // Prevenir doble avance
        setNextEnabled(false);
        // Allways allow previous action even if the current form is not valid!
        if (currentIndex > newIndex)
        {
            setNextEnabled(true);
            return true;
        }
        // Forbid next action on "Warning" step if the user is to young
        if (newIndex === 3 && Number($("#age-2").val()) < 18)
        {
            return false;
        }
        // Needed in some cases if the user went back (clean up)
        if (currentIndex < newIndex)
        {
            // To remove error styles
            form.find(".body:eq(" + newIndex + ") label.error").remove();
            form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
        }
        form.validate().settings.ignore = ":disabled,:hidden";
        var isValid = form.valid();
        if (!isValid) {
            // Enfocar y hacer scroll al primer error
            var $firstError = form.find(".error:input, select.error, textarea.error").first();
            if ($firstError.length) {
                try {
                    // Expandir sección si está colapsada (usa h4.section-toggle en la misma página)
                    var $row = $firstError.closest('.row');
                    var $prevToggle = $row.prevAll('div.row').find('h4.section-toggle').first();
                    if ($prevToggle.length) {
                        var $ind = $prevToggle.find('.toggle-indicator');
                        // Si está colapsado, simular click para expandir
                        var $startRow = $prevToggle.parent().parent();
                        var $next = $startRow.next();
                        if ($next.is(':hidden')) { $prevToggle.trigger('click'); }
                    }
                } catch(e){}
                $('html, body').animate({ scrollTop: Math.max(0, $firstError.offset().top - 120) }, 250);
                $firstError.trigger('focus');
            }
            // Rehabilitar el botón para permitir reintentos
            setNextEnabled(true);
            // Contar errores visibles del paso y reflejar en el botón
            try {
                var $body = form.find('.body').eq(currentIndex);
                var $errorInputs = $body.find('.error:input, select.error, textarea.error');
                var count = $errorInputs.length;
                if (count > 0) {
                    setNextLabel(nextDefaultLabel + ' (' + count + ' errores)');
                    renderStepErrorSummary($body, $errorInputs);
                }
            } catch(e) {}
        }
        return isValid;
    },
    onStepChanged: function (event, currentIndex, priorIndex)
    {
        // Used to skip the "Warning" step if the user is old enough.
        if (currentIndex === 2 && Number($("#age-2").val()) >= 18)
        {
            form.steps("next");
        }
        // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
        if (currentIndex === 2 && priorIndex === 3)
        {
            form.steps("previous");
        }

        // Asegurarse de que el botón extra esté en cada paso
        addExtraButton();
        // Rehabilitar botón siguiente post-cambio
        setNextEnabled(true);
        // Restaurar etiqueta por defecto
        setNextLabel(nextDefaultLabel);
        // Limpiar resumen de errores del paso anterior y actual
        try {
            form.find('.body').eq(priorIndex).find('.step-error-summary').remove();
            form.find('.body').eq(currentIndex).find('.step-error-summary').remove();
        } catch(e) {}
    },
    onFinishing: function (event, currentIndex)
    {
        form.validate().settings.ignore = ":disabled";
        var isValid = form.valid();
        if (!isValid) {
            var $firstError = form.find(".error:input, select.error, textarea.error").first();
            if ($firstError.length) {
                try {
                    var $row = $firstError.closest('.row');
                    var $prevToggle = $row.prevAll('div.row').find('h4.section-toggle').first();
                    if ($prevToggle.length) {
                        var $startRow = $prevToggle.parent().parent();
                        var $next = $startRow.next();
                        if ($next.is(':hidden')) { $prevToggle.trigger('click'); }
                    }
                } catch(e){}
                $('html, body').animate({ scrollTop: Math.max(0, $firstError.offset().top - 120) }, 250);
                $firstError.trigger('focus');
            }
            setNextEnabled(true);
            try {
                var $body = form.find('.body').eq(currentIndex);
                var $errorInputs = $body.find('.error:input, select.error, textarea.error');
                if ($errorInputs.length) {
                    renderStepErrorSummary($body, $errorInputs);
                }
            } catch(e) {}
        }
        return isValid;
    },
    onFinished: function (event, currentIndex)
    {
        // Usar el submit de jQuery para que el handler de fetch intercepte y muestre el progreso
        form.trigger('submit');
    }
}).validate({
    errorClass: 'text-danger',
    errorElement: 'small',
    highlight: function(element){
        var $el = $(element);
        $el.attr('aria-invalid','true')
           .addClass('is-invalid')
           .closest('.form-group')
           .addClass('has-error');
    },
    unhighlight: function(element){
        var $el = $(element);
        $el.attr('aria-invalid','false')
           .removeClass('is-invalid')
           .closest('.form-group')
           .removeClass('has-error');
    },
    errorPlacement: function(error, element){
        error.addClass('d-block');
        var $container = element.closest('.col-sm-4, .col-sm-3, .col-sm-2, .col-sm-6');
        if($container.length){ error.appendTo($container); } else { error.insertAfter(element); }
    },
    rules: {
        // Reglas específicas adicionales pueden declararse aquí si se requiere
    }
});

// Helper: habilitar/deshabilitar botón Siguiente
function setNextEnabled(enabled) {
    try {
        var $next = $(".actions a[href$='#next']");
        if (!$next.length) return;
        if (enabled) {
            $next.css({'pointer-events':'auto', 'opacity':'', 'cursor':''}).attr('aria-disabled','false').removeClass('disabled');
        } else {
            $next.css({'pointer-events':'none', 'opacity':'.6', 'cursor':'not-allowed'}).attr('aria-disabled','true').addClass('disabled');
        }
    } catch(e) {}
}

// Helper: almacenar y actualizar etiqueta del botón Siguiente
var nextDefaultLabel = 'Siguiente';
function setNextDefaultLabel(){
    try {
        var $next = $(".actions a[href$='#next']");
        if ($next.length) { nextDefaultLabel = $.trim($next.text()) || nextDefaultLabel; }
    } catch(e) {}
}
function setNextLabel(label){
    try {
        var $next = $(".actions a[href$='#next']");
        if ($next.length) { $next.text(label); }
    } catch(e) {}
}

// Helper: construir y renderizar resumen de errores del paso
function renderStepErrorSummary($body, $errorInputs) {
    try {
        $body.find('.step-error-summary').remove();
        var items = [];
        $errorInputs.each(function(){
            var $el = $(this);
            var label = '';
            // Buscar label asociado por for=id
            var id = $el.attr('id');
            if (id) {
                var byFor = $body.find("label[for='"+id+"']").first();
                if (byFor.length) { label = $.trim(byFor.text()); }
            }
            if (!label) {
                // Buscar label anterior en el mismo grupo
                var $group = $el.closest('.form-group');
                var $prevLabel = $group.find('label').first();
                if ($prevLabel.length) { label = $.trim($prevLabel.text()); }
            }
            if (!label) { label = ($el.attr('name') || 'Campo'); }
            items.push('<li>'+ $('<div>').text(label).html() +'</li>');
        });
        if (!items.length) return;
        var html = '<div class="step-error-summary alert alert-danger" role="alert" style="margin-bottom:15px;">'+
                   '<strong>Por favor corrige los siguientes campos:</strong><ul style="margin:8px 0 0 18px;">'+ items.join('') +'</ul></div>';
        $body.prepend(html);
    } catch(e) {}
}

// Función para agregar el botón extra en el primer <li> solo si el div #extraButtonTrigger está presente
function addExtraButton() {
    // Verificar si el div #extraButtonTrigger existe en la página
    var trigger = $("#extraButtonTrigger");
    if (trigger.length > 0 && $("#extraButtonLi").length === 0) {
        // Crear un nuevo <li> para el botón extra
        var extraButtonLi = $("<li>", { id: "extraButtonLi", class: "extra-button" });

        // Crear el botón extra dentro del nuevo <li>
        $("<a>", {
            id: "extraButton",
            href: trigger.data("url"),
            text: trigger.data("text"),
            class: trigger.data("btn")
        }).appendTo(extraButtonLi);

        // Insertar el nuevo <li> con el botón extra al inicio de la lista de acciones
        $(".actions ul").prepend(extraButtonLi);
    }
}

$("#example-vertical").steps({
    headerTag: "h3",
    bodyTag: "section",
    transitionEffect: "slideLeft",
    stepsOrientation: "vertical"
});