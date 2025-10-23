<!-- Botón de Ayuda Flotante - SINTIA Support -->
<?php
// Obtener colores de la plataforma
$colorPrimario = isset($Plataforma->colorUno) ? $Plataforma->colorUno : '#667eea';
$colorSecundario = isset($Plataforma->colorDos) ? $Plataforma->colorDos : '#764ba2';
?>
<style>
    /* Contenedor del botón de ayuda */
    .help-float-container {
        position: fixed;
        bottom: 40px;
        right: 40px; /* Posicionado en la esquina inferior derecha */
        z-index: 999;
        font-family: 'Satoshi', 'Poppins', sans-serif;
    }

    /* Botón principal de ayuda */
    .help-float-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, <?= $colorPrimario ?> 0%, <?= $colorSecundario ?> 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .help-float-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .help-float-btn:hover::before {
        width: 100%;
        height: 100%;
    }

    .help-float-btn:hover {
        transform: scale(1.1) rotate(15deg);
        box-shadow: 0 6px 30px rgba(102, 126, 234, 0.6);
    }

    .help-float-btn i {
        position: relative;
        z-index: 1;
        transition: transform 0.3s ease;
    }

    .help-float-btn:hover i {
        transform: scale(1.1);
    }

    /* Badge de notificación (si hay alguna novedad) */
    .help-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff4757;
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        border: 2px solid white;
        animation: pulse-badge 2s infinite;
    }

    @keyframes pulse-badge {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.7);
        }
        50% {
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(255, 71, 87, 0);
        }
    }

    /* Menú desplegable de opciones */
    .help-menu {
        position: absolute;
        bottom: 75px;
        right: 0;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        padding: 8px;
        min-width: 280px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px) scale(0.9);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .help-menu.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    /* Header del menú */
    .help-menu-header {
        padding: 16px 20px;
        border-bottom: 2px solid #f1f3f5;
        margin-bottom: 8px;
    }

    .help-menu-title {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .help-menu-subtitle {
        font-size: 13px;
        color: #718096;
        margin: 4px 0 0 0;
    }

    /* Items del menú */
    .help-menu-item {
        display: flex;
        align-items: center;
        padding: 14px 16px;
        border-radius: 10px;
        text-decoration: none;
        color: #2d3748;
        transition: all 0.2s ease;
        margin-bottom: 4px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .help-menu-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transform: scaleY(0);
        transition: transform 0.2s ease;
    }

    .help-menu-item:hover {
        background: #f7fafc;
        transform: translateX(5px);
    }

    .help-menu-item:hover::before {
        transform: scaleY(1);
    }

    .help-menu-item:active {
        transform: translateX(5px) scale(0.98);
    }

    .help-menu-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-right: 14px;
        transition: all 0.2s ease;
    }

    /* Colores para cada ícono */
    .help-menu-item:nth-child(1) .help-menu-icon {
        background: linear-gradient(135deg, <?= $colorPrimario ?> 0%, <?= $colorSecundario ?> 100%);
        color: white;
    }

    .help-menu-item:nth-child(2) .help-menu-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .help-menu-item:nth-child(3) .help-menu-icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .help-menu-item:nth-child(4) .help-menu-icon {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }

    .help-menu-item:nth-child(5) .help-menu-icon {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }

    .help-menu-item:nth-child(6) .help-menu-icon {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        color: white;
    }

    .help-menu-item:hover .help-menu-icon {
        transform: rotate(10deg) scale(1.1);
    }

    .help-menu-content {
        flex: 1;
    }

    .help-menu-item-title {
        font-size: 15px;
        font-weight: 600;
        color: #2d3748;
        margin: 0 0 2px 0;
    }

    .help-menu-item-desc {
        font-size: 12px;
        color: #718096;
        margin: 0;
    }

    .help-menu-arrow {
        color: #cbd5e0;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .help-menu-item:hover .help-menu-arrow {
        color: <?= $colorPrimario ?>;
        transform: translateX(5px);
    }

    .help-menu-item::before {
        background: linear-gradient(135deg, <?= $colorPrimario ?> 0%, <?= $colorSecundario ?> 100%);
    }

    /* Footer del menú */
    .help-menu-footer {
        padding: 12px 16px;
        border-top: 2px solid #f1f3f5;
        margin-top: 8px;
        text-align: center;
    }

    .help-menu-footer-text {
        font-size: 11px;
        color: #a0aec0;
        margin: 0;
    }

    .help-menu-footer-link {
        color: <?= $colorPrimario ?>;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .help-menu-footer-link:hover {
        color: <?= $colorSecundario ?>;
        text-decoration: underline;
    }

    /* Overlay cuando el menú está abierto */
    .help-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.3);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 998;
        backdrop-filter: blur(2px);
    }

    .help-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* Animación de entrada */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .help-menu.active .help-menu-item {
        animation: slideInUp 0.3s ease forwards;
        opacity: 0;
    }

    .help-menu.active .help-menu-item:nth-child(1) { animation-delay: 0.1s; }
    .help-menu.active .help-menu-item:nth-child(2) { animation-delay: 0.15s; }
    .help-menu.active .help-menu-item:nth-child(3) { animation-delay: 0.2s; }
    .help-menu.active .help-menu-item:nth-child(4) { animation-delay: 0.25s; }
    .help-menu.active .help-menu-item:nth-child(5) { animation-delay: 0.3s; }
    .help-menu.active .help-menu-item:nth-child(6) { animation-delay: 0.35s; }

    /* Responsive */
    @media (max-width: 768px) {
        .help-float-container {
            right: 20px;
            bottom: 40px; /* Posición en móvil */
        }

        .help-float-btn {
            width: 55px;
            height: 55px;
            font-size: 24px;
        }

        .help-menu {
            right: -10px;
            min-width: 260px;
        }

        .help-menu-item {
            padding: 12px 14px;
        }

        .help-menu-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
    }

    /* Animación de latido para llamar la atención inicialmente */
    @keyframes heartbeat {
        0%, 100% { transform: scale(1); }
        10%, 30% { transform: scale(1.1); }
        20%, 40% { transform: scale(1); }
    }

    .help-float-btn.first-time {
        animation: heartbeat 2s ease-in-out 3;
    }
</style>

<!-- HTML del componente -->
<div class="help-overlay" id="helpOverlay"></div>

<div class="help-float-container">
    <!-- Menú de opciones -->
    <div class="help-menu" id="helpMenu">
        <div class="help-menu-header">
            <h4 class="help-menu-title">
                <i class="fa fa-life-ring"></i>
                Centro de Ayuda
            </h4>
            <p class="help-menu-subtitle">¿En qué podemos ayudarte?</p>
        </div>

        <a href="#" class="help-menu-item" onclick="contactarSoporte(event)">
            <div class="help-menu-icon">
                <i class="fa fa-headphones"></i>
            </div>
            <div class="help-menu-content">
                <h5 class="help-menu-item-title">Contactar Soporte</h5>
                <p class="help-menu-item-desc">Habla con nuestro equipo</p>
            </div>
            <i class="fa fa-chevron-right help-menu-arrow"></i>
        </a>

        <a href="#" class="help-menu-item" onclick="abrirManualAyuda(event)">
            <div class="help-menu-icon">
                <i class="fa fa-book"></i>
            </div>
            <div class="help-menu-content">
                <h5 class="help-menu-item-title">Manual de Usuario</h5>
                <p class="help-menu-item-desc">Guías y tutoriales</p>
            </div>
            <i class="fa fa-chevron-right help-menu-arrow"></i>
        </a>

        <a href="#" class="help-menu-item" onclick="abrirPreguntasFrecuentes(event)">
            <div class="help-menu-icon">
                <i class="fa fa-question-circle"></i>
            </div>
            <div class="help-menu-content">
                <h5 class="help-menu-item-title">Preguntas Frecuentes</h5>
                <p class="help-menu-item-desc">Respuestas rápidas</p>
            </div>
            <i class="fa fa-chevron-right help-menu-arrow"></i>
        </a>

        <a href="#" class="help-menu-item" onclick="reportarProblema(event)">
            <div class="help-menu-icon">
                <i class="fa fa-bug"></i>
            </div>
            <div class="help-menu-content">
                <h5 class="help-menu-item-title">Reportar Problema</h5>
                <p class="help-menu-item-desc">Envía un reporte técnico</p>
            </div>
            <i class="fa fa-chevron-right help-menu-arrow"></i>
        </a>

        <a href="#" class="help-menu-item" onclick="abrirVideotutoriales(event)">
            <div class="help-menu-icon">
                <i class="fa fa-play-circle"></i>
            </div>
            <div class="help-menu-content">
                <h5 class="help-menu-item-title">Video Tutoriales</h5>
                <p class="help-menu-item-desc">Aprende visualmente</p>
            </div>
            <i class="fa fa-chevron-right help-menu-arrow"></i>
        </a>

        <a href="#" class="help-menu-item" onclick="abrirCentroRecursos(event)">
            <div class="help-menu-icon">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <div class="help-menu-content">
                <h5 class="help-menu-item-title">Centro de Recursos</h5>
                <p class="help-menu-item-desc">Documentación completa</p>
            </div>
            <i class="fa fa-chevron-right help-menu-arrow"></i>
        </a>

        <div class="help-menu-footer">
            <p class="help-menu-footer-text">
                ¿Necesitas ayuda inmediata? <br>
                <a href="https://wa.me/573006075800" target="_blank" class="help-menu-footer-link">
                    Visita nuestro portal de soporte
                </a>
            </p>
        </div>
    </div>

    <!-- Botón principal -->
    <button class="help-float-btn first-time" id="helpFloatBtn" onclick="toggleHelpMenu()" title="Centro de Ayuda">
        <i class="fa fa-question-circle"></i>
        <!-- Badge opcional para notificaciones -->
        <!-- <span class="help-badge">1</span> -->
    </button>
</div>

<!-- JavaScript del componente -->
<script>
    // Variables globales
    const helpFloatBtn = document.getElementById('helpFloatBtn');
    const helpMenu = document.getElementById('helpMenu');
    const helpOverlay = document.getElementById('helpOverlay');

    // Toggle del menú de ayuda
    function toggleHelpMenu() {
        helpMenu.classList.toggle('active');
        helpOverlay.classList.toggle('active');
        helpFloatBtn.classList.remove('first-time'); // Remover animación de latido
    }

    // Cerrar menú al hacer clic en el overlay
    helpOverlay.addEventListener('click', function() {
        toggleHelpMenu();
    });

    // Cerrar menú con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && helpMenu.classList.contains('active')) {
            toggleHelpMenu();
        }
    });

    // Funciones para cada opción del menú
    function contactarSoporte(event) {
        event.preventDefault();
        toggleHelpMenu();
        
        // Mostrar modal o redirigir al chat de soporte
        $.toast({
            heading: 'Contactar Soporte',
            text: 'Redirigiendo al chat de soporte...',
            position: 'bottom-right',
            icon: 'info',
            hideAfter: 2000,
            loaderBg: '<?= $colorPrimario ?>'
        });

        // Redirigir al chat si existe
        <?php if(isset($_GET['idPaginaInterna']) && ($_GET['idPaginaInterna'] != 'DT0209' && $_GET['idPaginaInterna'] != 'DC0148')){ ?>
        setTimeout(function() {
            window.location.href = 'chat2.php';
        }, 2000);
        <?php } else { ?>
        setTimeout(function() {
            window.open('https://wa.me/573006075800', '_blank');
        }, 2000);
        <?php } ?>
    }

    function abrirManualAyuda(event) {
        event.preventDefault();
        toggleHelpMenu();
        
        // Abrir el manual de ayuda en una nueva ventana
        window.open('https://docs.google.com/document/d/1ZgtUFs0WJQD797Dp5fy8T-lsUs4BddArW-49mAi5JkQ/edit?usp=sharing', '_blank');
        
        $.toast({
            heading: 'Manual de Usuario',
            text: 'Abriendo el manual de usuario en una nueva ventana...',
            position: 'bottom-right',
            icon: 'success',
            hideAfter: 3000,
            loaderBg: '#f5576c'
        });
    }

    function abrirPreguntasFrecuentes(event) {
        event.preventDefault();
        toggleHelpMenu();
        
        // Abrir FAQ en una nueva ventana
        window.open('https://ayuda.plataformasintia.com/', '_blank');
        
        $.toast({
            heading: 'Preguntas Frecuentes',
            text: 'Abriendo las preguntas frecuentes...',
            position: 'bottom-right',
            icon: 'info',
            hideAfter: 3000,
            loaderBg: '#00f2fe'
        });
    }

    function reportarProblema(event) {
        event.preventDefault();
        toggleHelpMenu();
        
        // Abrir formulario de reporte
        const usuarioId = '<?php echo isset($_SESSION["id"]) ? $_SESSION["id"] : ""; ?>';
        const usuarioNombre = '<?php echo isset($datosUsuarioActual["uss_nombre"]) ? $datosUsuarioActual["uss_nombre"] : ""; ?>';
        const paginaActual = window.location.href;
        
        // Por ahora, abrir modal o redirigir a formulario
        $.toast({
            heading: 'Reportar Problema',
            text: 'Preparando formulario de reporte...',
            position: 'bottom-right',
            icon: 'warning',
            hideAfter: 2000,
            loaderBg: '#38f9d7'
        });
        
        setTimeout(function() {
            window.open('https://forms.gle/1NpXSwyqoomKdch76', '_blank');
        }, 2000);
    }

    function abrirVideotutoriales(event) {
        event.preventDefault();
        toggleHelpMenu();
        
        // Abrir página de video tutoriales
        window.open('https://ayuda.plataformasintia.com/', '_blank');
        
        $.toast({
            heading: 'Video Tutoriales',
            text: 'Abriendo la biblioteca de video tutoriales...',
            position: 'bottom-right',
            icon: 'success',
            hideAfter: 3000,
            loaderBg: '#fee140'
        });
    }

    function abrirCentroRecursos(event) {
        event.preventDefault();
        toggleHelpMenu();
        
        // Abrir centro de recursos
        window.open('como-empezar.php');
        
        $.toast({
            heading: 'Centro de Recursos',
            text: 'Accediendo al centro de recursos...',
            position: 'bottom-right',
            icon: 'info',
            hideAfter: 3000,
            loaderBg: '#330867'
        });
    }

    // Remover animación de latido después de 6 segundos
    setTimeout(function() {
        helpFloatBtn.classList.remove('first-time');
    }, 6000);

    // Tracking de analytics (opcional)
    function trackHelpAction(action) {
        // Implementar tracking con Google Analytics o similar
        if (typeof gtag !== 'undefined') {
            gtag('event', 'help_action', {
                'event_category': 'Help Center',
                'event_label': action
            });
        }
    }
</script>

