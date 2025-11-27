/**
 * TOUR MODERNO DE SINTIA
 * Sistema de tour interactivo con diseño moderno usando Driver.js
 */

// Función principal para iniciar el tour
function iniciarTourModerno() {
    // Crear instancia de Driver.js con configuración personalizada
    const driver = window.driver.js.driver({
        showProgress: true,
        progressText: 'Paso {{current}} de {{total}}',
        nextBtnText: 'Siguiente →',
        prevBtnText: '← Anterior',
        doneBtnText: '¡Entendido! ✓',
        closeBtnAriaLabel: 'Cerrar',
        showButtons: ['next', 'previous', 'close'],
        
        // Animación suave
        animate: true,
        
        // Permitir cerrar con ESC
        allowClose: true,
        
        // Overlay oscuro
        overlayOpacity: 0.75,
        
        // Estilos personalizados
        popoverClass: 'sintia-tour-popover',
        
        // Callbacks
        onDestroyed: function() {
            console.log('Tour completado');
            
            // Guardar que el usuario ya vio el tour
            localStorage.setItem('tourSintiaVisto', 'true');
            
            // Mostrar mensaje de finalización
            mostrarMensajeTourCompletado();
        },
        
        onNextClick: function() {
            driver.moveNext();
        },
        
        onPrevClick: function() {
            driver.movePrevious();
        },
        
        // Pasos del tour
        steps: obtenerPasosTour()
    });
    
    // Iniciar el tour
    driver.drive();
}

// Función para obtener los pasos del tour según el tipo de usuario
function obtenerPasosTour() {
    const pasos = [];
    
    // Paso 1: Bienvenida
    pasos.push({
        popover: {
            title: '👋 ¡Bienvenido a SINTIA!',
            description: `
                <div style="text-align: center; padding: 20px 10px;">
                    <div style="font-size: 48px; margin-bottom: 15px;">🎓</div>
                    <h3 style="color: #667eea; margin-bottom: 10px;">Sistema Integral de Gestión Educativa</h3>
                    <p style="color: #6b7280; line-height: 1.6;">
                        Te daremos un recorrido rápido por las funciones principales de la plataforma.
                        Puedes saltar o cerrar este tour en cualquier momento presionando <kbd>ESC</kbd>.
                    </p>
                </div>
            `,
            side: 'left',
            align: 'start'
        }
    });
    
    // Paso 2: Menú lateral (si existe)
    if (document.querySelector('.sidemenu')) {
        pasos.push({
            element: '.sidemenu',
            popover: {
                title: '📋 Menú de Navegación',
                description: `
                    <p>Aquí encontrarás todas las opciones disponibles según tu rol y los módulos contratados.</p>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li>Expande las secciones haciendo clic en ellas</li>
                        <li>Las opciones activas se resaltan</li>
                        <li>Usa el ícono ☰ para colapsar el menú</li>
                    </ul>
                `,
                side: 'right',
                align: 'start'
            }
        });
    }
    
    // Paso 3: Buscador (si existe)
    if (document.querySelector('#buscador-general-container') || document.querySelector('.search-form-opened')) {
        pasos.push({
            element: '#buscador-general-container',
            popover: {
                title: '🔍 Búsqueda Rápida',
                description: `
                    <p>Encuentra rápidamente lo que necesitas:</p>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li>Busca estudiantes, docentes o cursos</li>
                        <li>Accede directamente a resultados</li>
                        <li>Funciona en tiempo real</li>
                    </ul>
                `,
                side: 'bottom',
                align: 'start'
            }
        });
    }
    
    // Paso 4: Correo Interno (si existe)
    if (document.querySelector('#header_inbox_bar') || document.querySelector('.dropdown-inbox')) {
        pasos.push({
            element: '#header_inbox_bar',
            popover: {
                title: '📧 Correo Interno',
                description: `
                    <p>Sistema de mensajería de la plataforma:</p>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li>Envía y recibe mensajes internos</li>
                        <li>Comunicación con docentes y directivos</li>
                        <li>Notificaciones en tiempo real</li>
                    </ul>
                `,
                side: 'bottom',
                align: 'end'
            }
        });
    }
    
    // Paso 5: Perfil de usuario
    const perfilUsuario = document.querySelector('li.dropdown-user[data-step="500"]');
    if (perfilUsuario) {
        pasos.push({
            element: 'li.dropdown-user[data-step="500"]',
            popover: {
                title: '👤 Tu Perfil',
                description: `
                    <p>Gestiona tu cuenta desde aquí:</p>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li><strong>Perfil:</strong> Edita tu información personal</li>
                        <li><strong>Cambiar clave:</strong> Actualiza tu contraseña</li>
                        <li><strong>Refrescar SINTIA:</strong> Limpia caché</li>
                        <li><strong>Salir:</strong> Cierra sesión de forma segura</li>
                    </ul>
                `,
                side: 'bottom',
                align: 'end'
            }
        });
    }
    
    // Paso 6: Contenido principal
    if (document.querySelector('.page-content')) {
        pasos.push({
            element: '.page-content',
            popover: {
                title: '📄 Área de Trabajo',
                description: `
                    <p>Este es tu espacio principal de trabajo donde:</p>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li>Visualizas toda la información</li>
                        <li>Realizas tareas y gestiones</li>
                        <li>Generas reportes e informes</li>
                        <li>Interactúas con el sistema</li>
                    </ul>
                `,
                side: 'left',
                align: 'start'
            }
        });
    }
    
    // Paso 7: Botón de ayuda flotante
    if (document.querySelector('.help-float-btn')) {
        pasos.push({
            element: '.help-float-btn',
            popover: {
                title: '💡 Centro de Ayuda',
                description: `
                    <p>Tu aliado en todo momento:</p>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li><strong>Tour SINTIA:</strong> Repetir este recorrido</li>
                        <li><strong>Soporte:</strong> Contacto directo con el equipo</li>
                        <li><strong>Manual:</strong> Documentación completa</li>
                        <li><strong>FAQs:</strong> Respuestas rápidas</li>
                        <li><strong>Videos:</strong> Tutoriales visuales</li>
                    </ul>
                    <div style="margin-top: 15px; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; color: white; text-align: center;">
                        <strong>Haz clic aquí siempre que necesites ayuda</strong>
                    </div>
                `,
                side: 'left',
                align: 'end'
            }
        });
    }
    
    // Paso final: Mensaje de despedida
    pasos.push({
        popover: {
            title: '🎉 ¡Tour Completado!',
            description: `
                <div style="text-align: center; padding: 20px 10px;">
                    <div style="font-size: 64px; margin-bottom: 15px;">🚀</div>
                    <h3 style="color: #10b981; margin-bottom: 10px;">¡Estás listo para empezar!</h3>
                    <p style="color: #6b7280; line-height: 1.6; margin-bottom: 15px;">
                        Ya conoces las funciones principales de SINTIA.
                        Recuerda que puedes acceder al Centro de Ayuda en cualquier momento.
                    </p>
                    <div style="background: #f0fdf4; border-left: 4px solid #10b981; padding: 12px; border-radius: 6px; text-align: left; margin-top: 15px;">
                        <p style="margin: 0; color: #065f46; font-size: 14px;">
                            <strong>💡 Consejo:</strong> Explora cada sección con calma y no dudes en consultar el manual si tienes dudas.
                        </p>
                    </div>
                </div>
            `
        }
    });
    
    return pasos;
}

// Mensaje de tour completado
function mostrarMensajeTourCompletado() {
    $.toast({
        heading: '✅ Tour Completado',
        text: 'Has terminado el recorrido por SINTIA. ¡Empieza a explorar!',
        position: 'bottom-right',
        icon: 'success',
        hideAfter: 5000,
        loaderBg: '#10b981',
        stack: 1
    });
}

// Auto-iniciar el tour si es la primera vez del usuario (opcional)
function verificarPrimeraVez() {
    const tourVisto = localStorage.getItem('tourSintiaVisto');
    const noMostrarMas = localStorage.getItem('tourSintiaNoMostrarMas');
    
    if (!tourVisto && !noMostrarMas) {
        // Mostrar mensaje de bienvenida y ofrecer iniciar el tour
        setTimeout(function() {
            if (confirm('¡Bienvenido a SINTIA!\n\n¿Te gustaría hacer un recorrido guiado por la plataforma?\n\nEste tour te mostrará las funciones principales en pocos minutos.')) {
                iniciarTourModerno();
            } else {
                // Preguntar si no quiere verlo más
                if (confirm('¿No deseas ver este mensaje de nuevo?')) {
                    localStorage.setItem('tourSintiaNoMostrarMas', 'true');
                }
            }
        }, 3000); // 3 segundos después de cargar
    }
}

// Resetear el tour (para desarrolladores)
function resetearTourSintia() {
    localStorage.removeItem('tourSintiaVisto');
    localStorage.removeItem('tourSintiaNoMostrarMas');
    console.log('Tour SINTIA reseteado. Recarga la página para ver el tour de nuevo.');
}

// Exponer funciones globalmente
window.iniciarTourModerno = iniciarTourModerno;
window.resetearTourSintia = resetearTourSintia;

