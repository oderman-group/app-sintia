/**
 * Fix para Bootstrap Datetimepicker - Reemplazar Glyphicons con Font Awesome
 * Este script reemplaza los iconos de glyphicon con iconos de Font Awesome
 */

(function($) {
    'use strict';
    
    // Función para reemplazar los iconos
    function fixDatepickerIcons() {
        // Esperar a que el datepicker se haya renderizado
        setTimeout(function() {
            // Reemplazar icono de flecha izquierda (prev)
            $('.datetimepicker table th.prev, .bootstrap-datetimepicker-widget table th.prev').each(function() {
                var $this = $(this);
                // Vaciar contenido y agregar icono de FA
                $this.html('<i class="fa fa-chevron-left"></i>');
            });
            
            // Reemplazar icono de flecha derecha (next)
            $('.datetimepicker table th.next, .bootstrap-datetimepicker-widget table th.next').each(function() {
                var $this = $(this);
                // Vaciar contenido y agregar icono de FA
                $this.html('<i class="fa fa-chevron-right"></i>');
            });
            
            // Reemplazar icono de subir (para vista de horas/minutos)
            $('.datetimepicker .glyphicon-chevron-up').each(function() {
                $(this).removeClass('glyphicon glyphicon-chevron-up').addClass('fa fa-chevron-up');
            });
            
            // Reemplazar icono de bajar (para vista de horas/minutos)
            $('.datetimepicker .glyphicon-chevron-down').each(function() {
                $(this).removeClass('glyphicon glyphicon-chevron-down').addClass('fa fa-chevron-down');
            });
        }, 100);
    }
    
    // Ejecutar al cargar el documento
    $(document).ready(function() {
        fixDatepickerIcons();
        
        // También ejecutar cada vez que se muestre el datepicker
        $(document).on('dp.show show.bs.datetimepicker', function() {
            fixDatepickerIcons();
        });
        
        // MutationObserver para detectar cuando se agrega el calendario al DOM
        if (typeof MutationObserver !== 'undefined') {
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        for (var i = 0; i < mutation.addedNodes.length; i++) {
                            var node = mutation.addedNodes[i];
                            if (node.nodeType === 1) { // Element node
                                if ($(node).hasClass('datetimepicker') || $(node).hasClass('bootstrap-datetimepicker-widget')) {
                                    fixDatepickerIcons();
                                }
                                // También buscar dentro del nodo agregado
                                if ($(node).find('.datetimepicker, .bootstrap-datetimepicker-widget').length > 0) {
                                    fixDatepickerIcons();
                                }
                            }
                        }
                    }
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    });
    
    // Ejecutar también cuando se hace clic en un campo de fecha
    $(document).on('focus', '.form_date input, .form_datetime input, .form_time input', function() {
        setTimeout(fixDatepickerIcons, 150);
    });
    
})(jQuery);

