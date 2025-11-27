# 📚 Botón de Ayuda Flotante - SINTIA Support Center

## 🎯 Descripción General

El **Botón de Ayuda Flotante** es un componente profesional e interactivo que proporciona acceso rápido al centro de ayuda de SINTIA desde cualquier página de la plataforma. Cuenta con un diseño moderno, animaciones fluidas y se adapta automáticamente a los colores corporativos de cada institución.

## ✨ Características Principales

### 1. **Diseño Profesional**
- Botón flotante circular con gradiente personalizado
- Animaciones suaves y efectos de hover profesionales
- Menú desplegable con opciones organizadas
- Iconos coloridos para cada opción
- Totalmente responsive y adaptable a dispositivos móviles

### 2. **Personalización Automática**
- Se adapta automáticamente a los colores institucionales (`$Plataforma->colorUno` y `$Plataforma->colorDos`)
- Mantiene consistencia visual con el resto de la plataforma
- Animación de "latido" inicial para llamar la atención de nuevos usuarios

### 3. **Opciones de Ayuda**
El menú incluye 6 opciones principales:

1. **📞 Contactar Soporte**
   - Redirige al chat de atención si está disponible
   - Abre el portal de contacto en caso contrario
   
2. **📖 Manual de Usuario**
   - Acceso directo a guías y tutoriales
   - Documentación completa del sistema
   
3. **❓ Preguntas Frecuentes**
   - Respuestas rápidas a dudas comunes
   - Base de conocimiento organizada
   
4. **🐛 Reportar Problema**
   - Formulario para reportes técnicos
   - Incluye información contextual automática
   
5. **🎥 Video Tutoriales**
   - Biblioteca de videos instructivos
   - Aprendizaje visual paso a paso
   
6. **🎓 Centro de Recursos**
   - Documentación técnica completa
   - Recursos adicionales y material de apoyo

## 📋 Estructura de Archivos

```
main-app/compartido/
├── boton-ayuda-flotante.php    # Componente principal
├── footer.php                   # Integración del componente
└── BOTON_AYUDA_FLOTANTE.md     # Esta documentación
```

## 🔧 Instalación y Configuración

### Paso 1: Integración en el Footer

El componente ya está integrado en `footer.php`:

```php
<!-- Botón de Ayuda Flotante - Centro de Ayuda SINTIA -->
<?php include_once(ROOT_PATH."/main-app/compartido/boton-ayuda-flotante.php"); ?>
```

### Paso 2: Configuración de URLs

Para personalizar las URLs de destino de cada opción, edita las funciones JavaScript en `boton-ayuda-flotante.php`:

```javascript
function abrirManualAyuda(event) {
    // Personalizar URL del manual
    window.open('https://tu-dominio.com/manual-usuario', '_blank');
}

function abrirPreguntasFrecuentes(event) {
    // Personalizar URL de FAQ
    window.open('https://tu-dominio.com/faq', '_blank');
}

// ... y así sucesivamente para cada función
```

### Paso 3: Personalización de Colores

Los colores se obtienen automáticamente de la plataforma:

```php
$colorPrimario = isset($Plataforma->colorUno) ? $Plataforma->colorUno : '#667eea';
$colorSecundario = isset($Plataforma->colorDos) ? $Plataforma->colorDos : '#764ba2';
```

Si deseas usar colores diferentes, modifica estas líneas en el archivo.

## 🎨 Personalización Visual

### Posicionamiento del Botón

Por defecto, el botón se posiciona a la izquierda del botón de chat:

```css
.help-float-container {
    position: fixed;
    bottom: 40px;
    right: 120px; /* 120px desde la derecha */
    z-index: 999;
}
```

Para cambiar la posición, modifica los valores de `bottom` y `right`.

### Tamaño del Botón

```css
.help-float-btn {
    width: 60px;  /* Ancho */
    height: 60px; /* Alto */
    font-size: 26px; /* Tamaño del ícono */
}
```

### Colores de los Iconos del Menú

Cada opción del menú tiene su propio gradiente de color:

```css
/* Opción 1 - Contactar Soporte */
.help-menu-item:nth-child(1) .help-menu-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Opción 2 - Manual de Usuario */
.help-menu-item:nth-child(2) .help-menu-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

/* ... y así sucesivamente */
```

## 📱 Responsive Design

El componente es totalmente responsive y se adapta a dispositivos móviles:

```css
@media (max-width: 768px) {
    .help-float-container {
        right: 20px;
        bottom: 110px; /* Evita superposición con el botón de chat */
    }
    
    .help-float-btn {
        width: 55px;
        height: 55px;
    }
    
    .help-menu {
        min-width: 260px;
    }
}
```

## 🎭 Animaciones y Efectos

### Animación de Latido Inicial

El botón realiza una animación de "latido" durante los primeros 6 segundos para llamar la atención:

```javascript
setTimeout(function() {
    helpFloatBtn.classList.remove('first-time');
}, 6000);
```

### Efectos de Hover

- **Botón principal**: Escala y rotación al pasar el mouse
- **Items del menú**: Desplazamiento lateral y cambio de color
- **Iconos**: Rotación y escala al interactuar

### Transiciones Suaves

Todas las animaciones utilizan funciones de easing personalizadas:

```css
transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
```

## 🔌 Funciones JavaScript Principales

### `toggleHelpMenu()`
Alterna la visibilidad del menú de ayuda.

```javascript
function toggleHelpMenu() {
    helpMenu.classList.toggle('active');
    helpOverlay.classList.toggle('active');
    helpFloatBtn.classList.remove('first-time');
}
```

### Funciones de Acción

Cada opción del menú tiene su propia función:

- `contactarSoporte(event)` - Redirige al soporte
- `abrirManualAyuda(event)` - Abre el manual
- `abrirPreguntasFrecuentes(event)` - Abre FAQ
- `reportarProblema(event)` - Formulario de reporte
- `abrirVideotutoriales(event)` - Biblioteca de videos
- `abrirCentroRecursos(event)` - Centro de recursos

### Cerrar el Menú

El menú se puede cerrar de tres formas:

1. **Haciendo clic en el overlay**
```javascript
helpOverlay.addEventListener('click', function() {
    toggleHelpMenu();
});
```

2. **Presionando la tecla ESC**
```javascript
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && helpMenu.classList.contains('active')) {
        toggleHelpMenu();
    }
});
```

3. **Seleccionando una opción** (cierre automático)

## 📊 Analytics y Tracking (Opcional)

Puedes implementar tracking de analytics agregando código en la función:

```javascript
function trackHelpAction(action) {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'help_action', {
            'event_category': 'Help Center',
            'event_label': action
        });
    }
}
```

Luego llama a esta función desde cada acción:

```javascript
function abrirManualAyuda(event) {
    trackHelpAction('Manual de Usuario');
    // ... resto del código
}
```

## 🎯 Notificaciones con jQuery Toast

El componente utiliza jQuery Toast para mostrar notificaciones elegantes:

```javascript
$.toast({
    heading: 'Título',
    text: 'Mensaje de confirmación',
    position: 'bottom-right',
    icon: 'success', // success, info, warning, error
    hideAfter: 3000,
    loaderBg: '#667eea'
});
```

## 🐛 Badge de Notificación (Opcional)

Para mostrar un badge con el número de notificaciones nuevas, descomenta esta línea en el HTML:

```html
<!-- <span class="help-badge">1</span> -->
```

Y personaliza el número dinámicamente desde PHP:

```php
<?php if($numNotificacionesAyuda > 0): ?>
<span class="help-badge"><?= $numNotificacionesAyuda ?></span>
<?php endif; ?>
```

## 🔒 Seguridad

### Variables de Sesión

Las funciones JavaScript pueden acceder a información de sesión de forma segura:

```javascript
const usuarioId = '<?php echo isset($_SESSION["id"]) ? $_SESSION["id"] : ""; ?>';
const usuarioNombre = '<?php echo isset($datosUsuarioActual["uss_nombre"]) ? $datosUsuarioActual["uss_nombre"] : ""; ?>';
```

### Sanitización de URLs

Al enviar datos por URL, siempre usa `encodeURIComponent()`:

```javascript
const url = 'https://ejemplo.com/reporte?page=' + encodeURIComponent(paginaActual);
```

## 🚀 Mejoras Futuras

### Sugerencias para expandir funcionalidad:

1. **Chat en Vivo Integrado**
   - Widget de chat directo dentro del menú
   - Conexión con agentes de soporte en tiempo real

2. **Búsqueda de Ayuda**
   - Campo de búsqueda en el menú
   - Resultados instantáneos de la base de conocimiento

3. **Historial de Consultas**
   - Ver consultas anteriores del usuario
   - Acceso rápido a problemas resueltos

4. **Modo Oscuro**
   - Adaptación automática al tema del usuario
   - Variantes de color para modo nocturno

5. **Inteligencia Artificial**
   - Asistente virtual con IA
   - Respuestas automáticas a preguntas comunes

## 🎓 Mejores Prácticas

1. **Actualiza las URLs** regularmente para que apunten a recursos actualizados
2. **Mantén el contenido de ayuda** sincronizado con las últimas versiones
3. **Monitorea las métricas** de uso de cada opción para mejorar el servicio
4. **Recopila feedback** de los usuarios sobre la utilidad del centro de ayuda
5. **Prueba regularmente** en diferentes dispositivos y navegadores

## 🆘 Soporte y Contacto

Si necesitas ayuda con la implementación o personalización del componente:

- **Email**: soporte@plataformasintia.com
- **Portal**: https://plataformasintia.com/soporte
- **Documentación**: https://plataformasintia.com/docs

---

**Versión**: 1.0.0  
**Fecha de Creación**: Octubre 2025  
**Autor**: ODERMAN - Equipo de Desarrollo SINTIA  
**Última Actualización**: Octubre 2025

---

## 📝 Registro de Cambios

### v1.0.0 (Octubre 2025)
- ✅ Implementación inicial del botón de ayuda flotante
- ✅ Integración con colores institucionales
- ✅ Menú desplegable con 6 opciones principales
- ✅ Animaciones y efectos profesionales
- ✅ Diseño responsive para móviles
- ✅ Notificaciones con jQuery Toast
- ✅ Documentación completa

---

*Este componente es parte del ecosistema SINTIA y está diseñado para mejorar la experiencia del usuario proporcionando acceso rápido a recursos de ayuda.*

