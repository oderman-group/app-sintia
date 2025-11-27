# 🎨 Variantes y Personalizaciones Avanzadas - Botón de Ayuda Flotante

Este documento contiene ejemplos de variantes y personalizaciones avanzadas del botón de ayuda flotante para diferentes casos de uso.

## 📋 Índice

1. [Variante Minimalista](#variante-minimalista)
2. [Variante con Chat Integrado](#variante-con-chat-integrado)
3. [Variante con Búsqueda](#variante-con-búsqueda)
4. [Variante Modo Oscuro](#variante-modo-oscuro)
5. [Variante Expandible](#variante-expandible)
6. [Variante con Badge Animado](#variante-con-badge-animado)
7. [Integración con WhatsApp](#integración-con-whatsapp)
8. [Integración con Telegram](#integración-con-telegram)

---

## 🎯 Variante Minimalista

Para un diseño más discreto y minimalista:

### CSS Modificado

```css
/* Botón minimalista - más pequeño y discreto */
.help-float-btn-minimal {
    width: 50px;
    height: 50px;
    background: #ffffff;
    color: #667eea;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 2px solid #f1f3f5;
}

.help-float-btn-minimal:hover {
    background: #667eea;
    color: #ffffff;
    border-color: #667eea;
}

/* Menú minimalista */
.help-menu-minimal {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.help-menu-minimal .help-menu-item {
    padding: 10px 14px;
    border-radius: 6px;
}

.help-menu-minimal .help-menu-icon {
    width: 32px;
    height: 32px;
    font-size: 16px;
}
```

---

## 💬 Variante con Chat Integrado

Integra un widget de chat directamente en el menú:

### HTML Adicional

```html
<div class="help-chat-widget" id="helpChatWidget">
    <div class="help-chat-header">
        <div class="help-chat-avatar">
            <img src="avatar-soporte.jpg" alt="Soporte">
            <span class="help-chat-status online"></span>
        </div>
        <div class="help-chat-info">
            <h5>Soporte SINTIA</h5>
            <p class="status-text">En línea</p>
        </div>
        <button class="help-chat-close" onclick="closeChatWidget()">
            <i class="fa fa-times"></i>
        </button>
    </div>
    
    <div class="help-chat-messages" id="chatMessages">
        <div class="help-chat-message bot">
            <div class="message-avatar">
                <i class="fa fa-robot"></i>
            </div>
            <div class="message-content">
                <p>👋 ¡Hola! Soy el asistente virtual de SINTIA. ¿En qué puedo ayudarte hoy?</p>
                <span class="message-time">Ahora</span>
            </div>
        </div>
        
        <!-- Mensajes dinámicos se agregarán aquí -->
    </div>
    
    <div class="help-chat-quick-replies">
        <button class="quick-reply" onclick="sendQuickReply('Tengo un problema técnico')">
            🐛 Problema técnico
        </button>
        <button class="quick-reply" onclick="sendQuickReply('¿Cómo uso esta función?')">
            ❓ Cómo usar
        </button>
        <button class="quick-reply" onclick="sendQuickReply('Necesito soporte')">
            🆘 Necesito ayuda
        </button>
    </div>
    
    <div class="help-chat-input">
        <textarea 
            id="chatInput" 
            placeholder="Escribe tu mensaje..."
            rows="1"
            onkeydown="handleChatKeydown(event)"
        ></textarea>
        <button class="send-message" onclick="sendChatMessage()">
            <i class="fa fa-paper-plane"></i>
        </button>
    </div>
</div>
```

### CSS para Chat Widget

```css
.help-chat-widget {
    position: fixed;
    bottom: 115px;
    right: 40px;
    width: 380px;
    height: 600px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px) scale(0.9);
    transition: all 0.3s ease;
    z-index: 1000;
}

.help-chat-widget.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.help-chat-header {
    display: flex;
    align-items: center;
    padding: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px 16px 0 0;
}

.help-chat-avatar {
    position: relative;
    width: 45px;
    height: 45px;
    margin-right: 12px;
}

.help-chat-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.help-chat-status {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.help-chat-status.online {
    background: #10b981;
}

.help-chat-status.offline {
    background: #ef4444;
}

.help-chat-info {
    flex: 1;
}

.help-chat-info h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.help-chat-info .status-text {
    margin: 0;
    font-size: 12px;
    opacity: 0.9;
}

.help-chat-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    transition: background 0.2s ease;
}

.help-chat-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

.help-chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background: #f9fafb;
}

.help-chat-message {
    display: flex;
    margin-bottom: 16px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.help-chat-message.bot {
    flex-direction: row;
}

.help-chat-message.user {
    flex-direction: row-reverse;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    margin: 0 8px;
    flex-shrink: 0;
}

.help-chat-message.bot .message-avatar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.help-chat-message.user .message-avatar {
    background: #e2e8f0;
    color: #4a5568;
}

.message-content {
    background: white;
    padding: 10px 14px;
    border-radius: 12px;
    max-width: 75%;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.help-chat-message.user .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.message-content p {
    margin: 0;
    font-size: 14px;
    line-height: 1.5;
}

.message-time {
    display: block;
    font-size: 11px;
    color: #9ca3af;
    margin-top: 4px;
}

.help-chat-message.user .message-time {
    color: rgba(255, 255, 255, 0.7);
}

.help-chat-quick-replies {
    display: flex;
    gap: 8px;
    padding: 12px 16px;
    background: white;
    border-top: 1px solid #e5e7eb;
    overflow-x: auto;
}

.quick-reply {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s ease;
}

.quick-reply:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.help-chat-input {
    display: flex;
    align-items: center;
    padding: 16px;
    background: white;
    border-radius: 0 0 16px 16px;
    border-top: 1px solid #e5e7eb;
}

.help-chat-input textarea {
    flex: 1;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 10px 16px;
    font-size: 14px;
    resize: none;
    font-family: inherit;
    max-height: 100px;
}

.help-chat-input textarea:focus {
    outline: none;
    border-color: #667eea;
}

.send-message {
    width: 40px;
    height: 40px;
    margin-left: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s ease;
}

.send-message:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.send-message:active {
    transform: scale(0.95);
}
```

### JavaScript para Chat Widget

```javascript
function toggleChatWidget() {
    const chatWidget = document.getElementById('helpChatWidget');
    chatWidget.classList.toggle('active');
}

function closeChatWidget() {
    document.getElementById('helpChatWidget').classList.remove('active');
}

function sendChatMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (message) {
        addMessageToChat('user', message);
        input.value = '';
        
        // Simular respuesta del bot
        setTimeout(() => {
            addMessageToChat('bot', 'Gracias por tu mensaje. Un agente te responderá pronto.');
        }, 1000);
    }
}

function addMessageToChat(type, text) {
    const messagesContainer = document.getElementById('chatMessages');
    const now = new Date();
    const timeString = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    
    const messageHTML = `
        <div class="help-chat-message ${type}">
            <div class="message-avatar">
                ${type === 'bot' ? '<i class="fa fa-robot"></i>' : '<i class="fa fa-user"></i>'}
            </div>
            <div class="message-content">
                <p>${text}</p>
                <span class="message-time">${timeString}</span>
            </div>
        </div>
    `;
    
    messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function sendQuickReply(text) {
    addMessageToChat('user', text);
    
    // Respuestas automáticas basadas en el mensaje
    setTimeout(() => {
        let response = '¿Puedes darme más detalles sobre tu consulta?';
        
        if (text.includes('técnico')) {
            response = 'Entiendo que tienes un problema técnico. ¿Podrías describir qué está sucediendo?';
        } else if (text.includes('función')) {
            response = '¿Qué función específica te gustaría aprender a usar?';
        } else if (text.includes('soporte')) {
            response = 'Estoy aquí para ayudarte. ¿Qué necesitas?';
        }
        
        addMessageToChat('bot', response);
    }, 1000);
}

function handleChatKeydown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendChatMessage();
    }
}
```

---

## 🔍 Variante con Búsqueda

Agrega un campo de búsqueda en el menú:

### HTML Adicional

```html
<div class="help-search-container">
    <input 
        type="text" 
        id="helpSearch" 
        class="help-search-input" 
        placeholder="Buscar ayuda..."
        oninput="searchHelp(this.value)"
    >
    <i class="fa fa-search help-search-icon"></i>
    
    <div class="help-search-results" id="helpSearchResults">
        <!-- Resultados de búsqueda aparecerán aquí -->
    </div>
</div>
```

### CSS para Búsqueda

```css
.help-search-container {
    padding: 12px 16px;
    border-bottom: 2px solid #f1f3f5;
    position: relative;
}

.help-search-input {
    width: 100%;
    padding: 10px 40px 10px 14px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.help-search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.help-search-icon {
    position: absolute;
    right: 28px;
    top: 50%;
    transform: translateY(-50%);
    color: #cbd5e0;
    pointer-events: none;
}

.help-search-results {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.help-search-results.active {
    max-height: 300px;
    overflow-y: auto;
    margin-top: 8px;
}

.help-search-result-item {
    padding: 10px 12px;
    background: #f9fafb;
    border-radius: 8px;
    margin-bottom: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.help-search-result-item:hover {
    background: #f3f4f6;
    transform: translateX(3px);
}

.help-search-result-title {
    font-size: 14px;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 4px 0;
}

.help-search-result-excerpt {
    font-size: 12px;
    color: #718096;
    margin: 0;
}

.help-search-no-results {
    padding: 20px;
    text-align: center;
    color: #a0aec0;
}
```

### JavaScript para Búsqueda

```javascript
// Base de datos de búsqueda (esto podría venir de una API)
const helpSearchDatabase = [
    {
        title: 'Cómo crear un nuevo usuario',
        category: 'Usuarios',
        content: 'Para crear un nuevo usuario, ve al módulo de Usuarios y haz clic en "Agregar Usuario"...',
        url: '/manual/usuarios/crear-usuario'
    },
    {
        title: 'Cómo calificar estudiantes',
        category: 'Calificaciones',
        content: 'Accede al módulo de Calificaciones, selecciona el curso y la materia...',
        url: '/manual/calificaciones/calificar'
    },
    {
        title: 'Configurar notificaciones',
        category: 'Configuración',
        content: 'En Configuración > Notificaciones puedes personalizar tus alertas...',
        url: '/manual/configuracion/notificaciones'
    },
    // ... más artículos
];

function searchHelp(query) {
    const resultsContainer = document.getElementById('helpSearchResults');
    
    if (query.length < 2) {
        resultsContainer.classList.remove('active');
        return;
    }
    
    const results = helpSearchDatabase.filter(item => 
        item.title.toLowerCase().includes(query.toLowerCase()) ||
        item.content.toLowerCase().includes(query.toLowerCase()) ||
        item.category.toLowerCase().includes(query.toLowerCase())
    );
    
    if (results.length > 0) {
        resultsContainer.innerHTML = results.map(item => `
            <div class="help-search-result-item" onclick="openHelpArticle('${item.url}')">
                <h6 class="help-search-result-title">${highlightQuery(item.title, query)}</h6>
                <p class="help-search-result-excerpt">${item.category} • ${highlightQuery(item.content.substring(0, 60), query)}...</p>
            </div>
        `).join('');
        resultsContainer.classList.add('active');
    } else {
        resultsContainer.innerHTML = '<div class="help-search-no-results">No se encontraron resultados</div>';
        resultsContainer.classList.add('active');
    }
}

function highlightQuery(text, query) {
    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<strong style="color: #667eea;">$1</strong>');
}

function openHelpArticle(url) {
    window.open(url, '_blank');
    toggleHelpMenu();
}
```

---

## 🌙 Variante Modo Oscuro

Adaptación automática para tema oscuro:

### CSS Modo Oscuro

```css
/* Detección de modo oscuro del sistema */
@media (prefers-color-scheme: dark) {
    .help-menu {
        background: #1a202c;
        color: #e2e8f0;
    }
    
    .help-menu-header {
        border-bottom-color: #2d3748;
    }
    
    .help-menu-title {
        color: #f7fafc;
    }
    
    .help-menu-subtitle {
        color: #cbd5e0;
    }
    
    .help-menu-item {
        color: #e2e8f0;
    }
    
    .help-menu-item:hover {
        background: #2d3748;
    }
    
    .help-menu-item-title {
        color: #f7fafc;
    }
    
    .help-menu-item-desc {
        color: #a0aec0;
    }
    
    .help-menu-footer {
        border-top-color: #2d3748;
        background: #1a202c;
    }
    
    .help-menu-footer-text {
        color: #718096;
    }
    
    .help-overlay {
        background: rgba(0, 0, 0, 0.6);
    }
}

/* Clase para forzar modo oscuro */
body.dark-mode .help-menu {
    background: #1a202c;
    color: #e2e8f0;
}

/* ... resto de estilos modo oscuro */
```

---

## 🔔 Variante con Badge Animado

Badge con animaciones avanzadas:

### HTML del Badge

```html
<span class="help-badge-advanced">
    <span class="badge-number">3</span>
    <span class="badge-pulse"></span>
    <span class="badge-pulse-2"></span>
</span>
```

### CSS del Badge Animado

```css
.help-badge-advanced {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 28px;
    height: 28px;
}

.badge-number {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #ff4757 0%, #ff6348 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    z-index: 2;
    box-shadow: 0 2px 8px rgba(255, 71, 87, 0.4);
    animation: badgeBounce 0.5s ease infinite alternate;
}

.badge-pulse,
.badge-pulse-2 {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid #ff4757;
    opacity: 0;
    animation: badgePulse 2s ease-out infinite;
}

.badge-pulse-2 {
    animation-delay: 1s;
}

@keyframes badgeBounce {
    from {
        transform: scale(1);
    }
    to {
        transform: scale(1.1);
    }
}

@keyframes badgePulse {
    0% {
        transform: scale(1);
        opacity: 0.8;
    }
    100% {
        transform: scale(2);
        opacity: 0;
    }
}
```

---

## 💚 Integración con WhatsApp

Agregar opción de contacto directo por WhatsApp:

### JavaScript para WhatsApp

```javascript
function contactarPorWhatsApp() {
    const numeroWhatsApp = '573001234567'; // Número con código de país
    const mensaje = encodeURIComponent('Hola, necesito ayuda con SINTIA');
    const url = `https://wa.me/${numeroWhatsApp}?text=${mensaje}`;
    window.open(url, '_blank');
    
    $.toast({
        heading: 'WhatsApp',
        text: 'Abriendo WhatsApp...',
        position: 'bottom-right',
        icon: 'success',
        hideAfter: 2000,
        loaderBg: '#25D366'
    });
}
```

---

## 📱 Integración con Telegram

Opción de contacto por Telegram:

### JavaScript para Telegram

```javascript
function contactarPorTelegram() {
    const usernameTelegram = 'SintiaSoporte';
    const url = `https://t.me/${usernameTelegram}`;
    window.open(url, '_blank');
    
    $.toast({
        heading: 'Telegram',
        text: 'Abriendo Telegram...',
        position: 'bottom-right',
        icon: 'info',
        hideAfter: 2000,
        loaderBg: '#0088cc'
    });
}
```

---

## 🎯 Conclusión

Estas variantes y personalizaciones demuestran la flexibilidad del componente de ayuda flotante. Puedes mezclar y combinar elementos de diferentes variantes según las necesidades específicas de tu institución.

### Recomendaciones:

1. **Mantén la simplicidad**: No agregues demasiadas opciones que puedan confundir al usuario
2. **Prueba en diferentes dispositivos**: Asegúrate de que funcione bien en móviles y tablets
3. **Recopila feedback**: Pregunta a los usuarios qué opciones son más útiles
4. **Actualiza regularmente**: Mantén el contenido de ayuda actualizado con las últimas versiones
5. **Monitorea el uso**: Rastrea qué opciones se usan más para optimizar el menú

---

*Documento creado por el Equipo de Desarrollo SINTIA - Octubre 2025*

