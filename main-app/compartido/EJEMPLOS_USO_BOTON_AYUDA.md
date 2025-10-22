# 💻 Ejemplos Prácticos de Uso - Botón de Ayuda Flotante

Este documento contiene ejemplos prácticos y código listo para usar para diferentes escenarios comunes.

## 📋 Índice

1. [Ejemplo 1: Personalizar URLs](#ejemplo-1-personalizar-urls)
2. [Ejemplo 2: Agregar Nueva Opción](#ejemplo-2-agregar-nueva-opción)
3. [Ejemplo 3: Cambiar Posición](#ejemplo-3-cambiar-posición)
4. [Ejemplo 4: Mostrar Solo en Ciertas Páginas](#ejemplo-4-mostrar-solo-en-ciertas-páginas)
5. [Ejemplo 5: Integrar con API de Soporte](#ejemplo-5-integrar-con-api-de-soporte)
6. [Ejemplo 6: Notificaciones Personalizadas](#ejemplo-6-notificaciones-personalizadas)
7. [Ejemplo 7: Modal de Formulario](#ejemplo-7-modal-de-formulario)
8. [Ejemplo 8: Diferentes Estilos por Rol](#ejemplo-8-diferentes-estilos-por-rol)

---

## Ejemplo 1: Personalizar URLs

### Escenario
Quieres que cada opción del menú redirija a tus propias URLs personalizadas.

### Solución

Edita las funciones en `boton-ayuda-flotante.php`:

```javascript
function abrirManualAyuda(event) {
    event.preventDefault();
    toggleHelpMenu();
    
    // URL personalizada de tu institución
    const urlManual = 'https://mi-institucion.edu.co/ayuda/manual';
    window.open(urlManual, '_blank');
    
    $.toast({
        heading: 'Manual de Usuario',
        text: 'Abriendo manual personalizado...',
        position: 'bottom-right',
        icon: 'success',
        hideAfter: 2000,
        loaderBg: '<?= $colorPrimario ?>'
    });
}

function abrirPreguntasFrecuentes(event) {
    event.preventDefault();
    toggleHelpMenu();
    
    // URL de tu sistema de preguntas frecuentes
    const urlFAQ = 'https://mi-institucion.edu.co/ayuda/faq';
    window.open(urlFAQ, '_blank');
}

// Repite para cada función...
```

---

## Ejemplo 2: Agregar Nueva Opción

### Escenario
Quieres agregar una opción para "Capacitación en Vivo" al menú.

### Solución

1. **Agregar HTML** en `boton-ayuda-flotante.php`:

```html
<a href="#" class="help-menu-item" onclick="abrirCapacitacionVivo(event)">
    <div class="help-menu-icon">
        <i class="fa fa-video-camera"></i>
    </div>
    <div class="help-menu-content">
        <h5 class="help-menu-item-title">Capacitación en Vivo</h5>
        <p class="help-menu-item-desc">Sesiones con expertos</p>
    </div>
    <i class="fa fa-chevron-right help-menu-arrow"></i>
</a>
```

2. **Agregar CSS** para el color del ícono:

```css
.help-menu-item:nth-child(7) .help-menu-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
```

3. **Agregar JavaScript**:

```javascript
function abrirCapacitacionVivo(event) {
    event.preventDefault();
    toggleHelpMenu();
    
    // Abrir página de capacitaciones
    window.open('https://mi-institucion.edu.co/capacitaciones', '_blank');
    
    $.toast({
        heading: 'Capacitación en Vivo',
        text: 'Próxima sesión: Viernes 3:00 PM',
        position: 'bottom-right',
        icon: 'info',
        hideAfter: 4000,
        loaderBg: '#667eea'
    });
}
```

---

## Ejemplo 3: Cambiar Posición

### Escenario
El botón de ayuda choca con otro elemento en tu página.

### Solución A: Posición Inferior Izquierda

```css
.help-float-container {
    bottom: 40px;
    left: 40px;  /* Cambiar de 'right' a 'left' */
    right: auto;
}
```

### Solución B: Esquina Superior Derecha

```css
.help-float-container {
    top: 80px;    /* Cambiar 'bottom' por 'top' */
    right: 40px;
    bottom: auto;
}
```

### Solución C: Centrado en la Parte Inferior

```css
.help-float-container {
    bottom: 40px;
    left: 50%;
    right: auto;
    transform: translateX(-50%);
}
```

---

## Ejemplo 4: Mostrar Solo en Ciertas Páginas

### Escenario
Quieres que el botón aparezca solo en páginas específicas.

### Solución

Modifica `footer.php`:

```php
<?php
// Lista de páginas donde mostrar el botón de ayuda
$paginasConAyuda = ['DT0001', 'DC0001', 'AC0001', 'ES0001'];

// Verificar si estamos en una página permitida
if (isset($idPaginaInterna) && in_array($idPaginaInterna, $paginasConAyuda)) {
    include_once(ROOT_PATH."/main-app/compartido/boton-ayuda-flotante.php");
}
?>
```

### Alternativa: Ocultar en Ciertas Páginas

```php
<?php
// Lista de páginas donde NO mostrar el botón
$paginasSinAyuda = ['login', 'registro', 'recuperar-clave'];

// Mostrar si no está en la lista de exclusión
if (!isset($idPaginaInterna) || !in_array($idPaginaInterna, $paginasSinAyuda)) {
    include_once(ROOT_PATH."/main-app/compartido/boton-ayuda-flotante.php");
}
?>
```

---

## Ejemplo 5: Integrar con API de Soporte

### Escenario
Quieres enviar tickets de soporte directamente desde el botón.

### Solución

1. **Crear función de envío**:

```javascript
function reportarProblema(event) {
    event.preventDefault();
    toggleHelpMenu();
    
    // Recopilar información del contexto
    const datosReporte = {
        usuario_id: '<?php echo $_SESSION["id"]; ?>',
        usuario_nombre: '<?php echo $datosUsuarioActual["uss_nombre"]; ?>',
        pagina_actual: window.location.href,
        navegador: navigator.userAgent,
        timestamp: new Date().toISOString()
    };
    
    // Mostrar modal o formulario
    mostrarModalReporte(datosReporte);
}

function mostrarModalReporte(datos) {
    Swal.fire({
        title: 'Reportar un Problema',
        html: `
            <div style="text-align: left;">
                <label>Describe el problema:</label>
                <textarea id="descripcionProblema" class="swal2-textarea" 
                          placeholder="Describe lo que sucedió..." rows="4"></textarea>
                
                <label style="margin-top: 15px; display: block;">Tipo de problema:</label>
                <select id="tipoProblema" class="swal2-select">
                    <option value="tecnico">Técnico</option>
                    <option value="acceso">Problema de acceso</option>
                    <option value="datos">Error en datos</option>
                    <option value="otro">Otro</option>
                </select>
                
                <label style="margin-top: 15px; display: block;">Prioridad:</label>
                <select id="prioridadProblema" class="swal2-select">
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="critica">Crítica</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Enviar Reporte',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const descripcion = document.getElementById('descripcionProblema').value;
            const tipo = document.getElementById('tipoProblema').value;
            const prioridad = document.getElementById('prioridadProblema').value;
            
            if (!descripcion) {
                Swal.showValidationMessage('Por favor describe el problema');
                return false;
            }
            
            return { descripcion, tipo, prioridad };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            enviarReporteAPI({
                ...datos,
                ...result.value
            });
        }
    });
}

function enviarReporteAPI(reporte) {
    // Mostrar loading
    Swal.fire({
        title: 'Enviando reporte...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar a la API
    fetch('<?= BASE_URL ?>/main-app/compartido/enviar-reporte-soporte.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(reporte)
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            icon: 'success',
            title: '¡Reporte Enviado!',
            text: `Tu ticket #${data.ticket_id} ha sido creado. Te contactaremos pronto.`,
            confirmButtonText: 'Entendido'
        });
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo enviar el reporte. Intenta nuevamente.',
            confirmButtonText: 'OK'
        });
        console.error('Error:', error);
    });
}
```

2. **Crear archivo PHP** (`enviar-reporte-soporte.php`):

```php
<?php
include("../session.php");
header('Content-Type: application/json');

try {
    // Recibir datos
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar datos
    if (empty($data['descripcion'])) {
        throw new Exception('Descripción requerida');
    }
    
    // Generar ticket ID
    $ticketId = 'TKT-' . date('Ymd') . '-' . rand(1000, 9999);
    
    // Insertar en base de datos
    $sql = "INSERT INTO soporte_tickets (
        ticket_id,
        usuario_id,
        usuario_nombre,
        tipo,
        prioridad,
        descripcion,
        pagina,
        navegador,
        fecha_creacion,
        estado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'abierto')";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        'ssssssss',
        $ticketId,
        $data['usuario_id'],
        $data['usuario_nombre'],
        $data['tipo'],
        $data['prioridad'],
        $data['descripcion'],
        $data['pagina_actual'],
        $data['navegador']
    );
    
    if ($stmt->execute()) {
        // Enviar email al equipo de soporte
        enviarEmailSoporte($ticketId, $data);
        
        echo json_encode([
            'success' => true,
            'ticket_id' => $ticketId,
            'message' => 'Ticket creado exitosamente'
        ]);
    } else {
        throw new Exception('Error al crear el ticket');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function enviarEmailSoporte($ticketId, $data) {
    // Implementar envío de email
    // Usar PHPMailer o el sistema de emails de SINTIA
}
?>
```

---

## Ejemplo 6: Notificaciones Personalizadas

### Escenario
Quieres diferentes estilos de notificaciones según la acción.

### Solución

```javascript
// Notificación de éxito
function notificacionExito(titulo, mensaje) {
    $.toast({
        heading: titulo,
        text: mensaje,
        position: 'bottom-right',
        icon: 'success',
        hideAfter: 3000,
        loaderBg: '#10b981',
        showHideTransition: 'slide'
    });
}

// Notificación de error
function notificacionError(titulo, mensaje) {
    $.toast({
        heading: titulo,
        text: mensaje,
        position: 'bottom-right',
        icon: 'error',
        hideAfter: 5000,
        loaderBg: '#ef4444',
        showHideTransition: 'fade'
    });
}

// Notificación de información
function notificacionInfo(titulo, mensaje) {
    $.toast({
        heading: titulo,
        text: mensaje,
        position: 'bottom-right',
        icon: 'info',
        hideAfter: 4000,
        loaderBg: '#3b82f6',
        showHideTransition: 'plain'
    });
}

// Notificación con acción
function notificacionConAccion(titulo, mensaje, textoBoton, callback) {
    const toast = $.toast({
        heading: titulo,
        text: mensaje + '<br><button onclick="' + callback + '" style="margin-top:10px; padding:5px 15px; background:#fff; border:none; border-radius:5px; cursor:pointer;">' + textoBoton + '</button>',
        position: 'bottom-right',
        icon: 'info',
        hideAfter: false, // No ocultar automáticamente
        loaderBg: '#667eea'
    });
}

// Uso
notificacionExito('¡Perfecto!', 'Tu reporte ha sido enviado');
notificacionError('Error', 'No se pudo conectar al servidor');
notificacionInfo('Nueva Actualización', 'Hay una nueva versión del manual disponible');
notificacionConAccion('Actualización', 'Nueva función disponible', 'Ver Más', 'verNuevaFuncion()');
```

---

## Ejemplo 7: Modal de Formulario

### Escenario
Quieres que "Contactar Soporte" abra un modal en lugar de redirigir.

### Solución

```javascript
function contactarSoporte(event) {
    event.preventDefault();
    toggleHelpMenu();
    
    Swal.fire({
        title: '📞 Contactar Soporte',
        html: `
            <div style="text-align: left; padding: 10px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        Nombre Completo
                    </label>
                    <input id="nombreContacto" type="text" 
                           class="swal2-input" 
                           value="<?php echo $datosUsuarioActual['uss_nombre']; ?>"
                           style="margin: 0; width: 100%;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        Email
                    </label>
                    <input id="emailContacto" type="email" 
                           class="swal2-input" 
                           value="<?php echo $datosUsuarioActual['uss_email']; ?>"
                           style="margin: 0; width: 100%;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        Asunto
                    </label>
                    <select id="asuntoContacto" class="swal2-select" 
                            style="margin: 0; width: 100%;">
                        <option value="">Selecciona un asunto</option>
                        <option value="soporte_tecnico">Soporte Técnico</option>
                        <option value="consulta_general">Consulta General</option>
                        <option value="sugerencia">Sugerencia</option>
                        <option value="reclamo">Reclamo</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        Mensaje
                    </label>
                    <textarea id="mensajeContacto" class="swal2-textarea" 
                              placeholder="Describe tu consulta en detalle..." 
                              rows="5"
                              style="margin: 0; width: 100%;"></textarea>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        ¿Cómo prefieres que te contactemos?
                    </label>
                    <div style="display: flex; gap: 15px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="metodoContacto" value="email" checked>
                            <span style="margin-left: 5px;">Email</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="metodoContacto" value="telefono">
                            <span style="margin-left: 5px;">Teléfono</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="metodoContacto" value="whatsapp">
                            <span style="margin-left: 5px;">WhatsApp</span>
                        </label>
                    </div>
                </div>
            </div>
        `,
        width: '600px',
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-paper-plane"></i> Enviar Mensaje',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#667eea',
        preConfirm: () => {
            const nombre = document.getElementById('nombreContacto').value;
            const email = document.getElementById('emailContacto').value;
            const asunto = document.getElementById('asuntoContacto').value;
            const mensaje = document.getElementById('mensajeContacto').value;
            const metodo = document.querySelector('input[name="metodoContacto"]:checked').value;
            
            // Validaciones
            if (!nombre || !email || !asunto || !mensaje) {
                Swal.showValidationMessage('Por favor completa todos los campos');
                return false;
            }
            
            if (!validarEmail(email)) {
                Swal.showValidationMessage('Email inválido');
                return false;
            }
            
            return { nombre, email, asunto, mensaje, metodo };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            enviarFormularioContacto(result.value);
        }
    });
}

function validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function enviarFormularioContacto(datos) {
    Swal.fire({
        title: 'Enviando...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('<?= BASE_URL ?>/main-app/compartido/procesar-contacto-soporte.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Mensaje Enviado!',
                text: 'Nos pondremos en contacto contigo pronto. Ticket: ' + data.ticket_id,
                confirmButtonText: 'Perfecto',
                confirmButtonColor: '#10b981'
            });
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error al Enviar',
            text: error.message || 'Intenta nuevamente más tarde',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#ef4444'
        });
    });
}
```

---

## Ejemplo 8: Diferentes Estilos por Rol

### Escenario
Quieres que cada tipo de usuario vea un color o estilo diferente.

### Solución

```php
<?php
// En boton-ayuda-flotante.php
$tipoUsuario = $datosUsuarioActual['uss_tipo'];

// Colores según el tipo de usuario
switch($tipoUsuario) {
    case TIPO_DIRECTIVO:
        $colorPrimario = '#8b5cf6'; // Púrpura
        $colorSecundario = '#7c3aed';
        break;
    case TIPO_DOCENTE:
        $colorPrimario = '#3b82f6'; // Azul
        $colorSecundario = '#2563eb';
        break;
    case TIPO_ACUDIENTE:
        $colorPrimario = '#10b981'; // Verde
        $colorSecundario = '#059669';
        break;
    case TIPO_ESTUDIANTE:
        $colorPrimario = '#f59e0b'; // Naranja
        $colorSecundario = '#d97706';
        break;
    default:
        $colorPrimario = '#667eea';
        $colorSecundario = '#764ba2';
}
?>
```

---

## 🎯 Conclusión

Estos ejemplos cubren los casos de uso más comunes. Puedes combinarlos y adaptarlos según tus necesidades específicas.

### Recursos Adicionales

- 📖 [Documentación Completa](./BOTON_AYUDA_FLOTANTE.md)
- 🎨 [Variantes de Diseño](./BOTON_AYUDA_VARIANTES.md)
- 📘 [Guía Rápida](./README_BOTON_AYUDA.md)

---

*¿Necesitas un ejemplo específico? Contacta al equipo de desarrollo SINTIA.*

