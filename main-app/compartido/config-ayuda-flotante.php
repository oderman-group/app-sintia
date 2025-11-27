<?php
/**
 * Configuración del Botón de Ayuda Flotante
 * 
 * Este archivo contiene la configuración centralizada para el botón de ayuda flotante.
 * Modifica las URLs y opciones según las necesidades de tu institución.
 * 
 * @author ODERMAN - Equipo SINTIA
 * @version 1.0.0
 */

// ============================================================
// CONFIGURACIÓN DE URLs
// ============================================================

// URL base del portal de soporte
define('HELP_URL_SOPORTE', 'https://wa.me/573006075800');

// URL del manual de usuario
define('HELP_URL_MANUAL', 'https://docs.google.com/document/d/1ZgtUFs0WJQD797Dp5fy8T-lsUs4BddArW-49mAi5JkQ/edit?usp=sharing');

// URL de preguntas frecuentes
define('HELP_URL_FAQ', 'https://ayuda.plataformasintia.com/');

// URL para reportar problemas
define('HELP_URL_REPORTE', 'https://forms.gle/1NpXSwyqoomKdch76');

// URL de video tutoriales
define('HELP_URL_VIDEOS', 'https://ayuda.plataformasintia.com/');

// URL del centro de recursos
define('HELP_URL_RECURSOS', 'como-empezar.php');

// ============================================================
// CONFIGURACIÓN DE OPCIONES DE MENÚ
// ============================================================

// Array con la configuración de cada opción del menú
$helpMenuOptions = [
    'contactar_soporte' => [
        'enabled' => true,
        'title' => 'Contactar Soporte',
        'description' => 'Habla con nuestro equipo',
        'icon' => 'fa fa-headphones',
        'color_start' => '#667eea',
        'color_end' => '#764ba2',
        'url' => HELP_URL_SOPORTE,
        'use_chat' => true, // Si está true, usa el chat interno si está disponible
        'order' => 1
    ],
    'manual_usuario' => [
        'enabled' => true,
        'title' => 'Manual de Usuario',
        'description' => 'Guías y tutoriales',
        'icon' => 'fa fa-book',
        'color_start' => '#f093fb',
        'color_end' => '#f5576c',
        'url' => HELP_URL_MANUAL,
        'order' => 2
    ],
    'preguntas_frecuentes' => [
        'enabled' => true,
        'title' => 'Preguntas Frecuentes',
        'description' => 'Respuestas rápidas',
        'icon' => 'fa fa-question-circle',
        'color_start' => '#4facfe',
        'color_end' => '#00f2fe',
        'url' => HELP_URL_FAQ,
        'order' => 3
    ],
    'reportar_problema' => [
        'enabled' => true,
        'title' => 'Reportar Problema',
        'description' => 'Envía un reporte técnico',
        'icon' => 'fa fa-bug',
        'color_start' => '#43e97b',
        'color_end' => '#38f9d7',
        'url' => HELP_URL_REPORTE,
        'order' => 4
    ],
    'video_tutoriales' => [
        'enabled' => true,
        'title' => 'Video Tutoriales',
        'description' => 'Aprende visualmente',
        'icon' => 'fa fa-play-circle',
        'color_start' => '#fa709a',
        'color_end' => '#fee140',
        'url' => HELP_URL_VIDEOS,
        'order' => 5
    ],
    'centro_recursos' => [
        'enabled' => true,
        'title' => 'Cómo empezar',
        'description' => 'Documentación completa',
        'icon' => 'fa fa-graduation-cap',
        'color_start' => '#30cfd0',
        'color_end' => '#330867',
        'url' => HELP_URL_RECURSOS,
        'order' => 6
    ]
];

// ============================================================
// CONFIGURACIÓN DE APARIENCIA
// ============================================================

// Posición del botón (en píxeles desde la derecha)
define('HELP_POSITION_RIGHT', 120);

// Posición del botón (en píxeles desde abajo)
define('HELP_POSITION_BOTTOM', 40);

// Tamaño del botón en píxeles
define('HELP_BUTTON_SIZE', 60);

// Mostrar animación de latido inicial
define('HELP_SHOW_HEARTBEAT', true);

// Duración de la animación de latido (en milisegundos)
define('HELP_HEARTBEAT_DURATION', 6000);

// ============================================================
// CONFIGURACIÓN DE NOTIFICACIONES
// ============================================================

// Mostrar badge con número de notificaciones nuevas
define('HELP_SHOW_BADGE', false);

// Número de notificaciones (puedes obtenerlo de la BD)
function getHelpNotificationsCount() {
    // Implementar lógica para obtener el número de notificaciones
    // Por ejemplo, nuevas actualizaciones del manual, nuevos videos, etc.
    return 0;
}

// ============================================================
// CONFIGURACIÓN DE TRACKING
// ============================================================

// Habilitar tracking de analytics
define('HELP_ENABLE_ANALYTICS', false);

// Categoría de eventos en Google Analytics
define('HELP_ANALYTICS_CATEGORY', 'Help Center');

// ============================================================
// CONFIGURACIÓN DE PERMISOS
// ============================================================

// Tipos de usuario que pueden ver el botón de ayuda
$helpAllowedUserTypes = [
    TIPO_DEV,
    TIPO_DIRECTIVO,
    TIPO_DOCENTE,
    TIPO_ACUDIENTE,
    TIPO_ESTUDIANTE
];

// Función para verificar si el usuario puede ver el botón de ayuda
function canShowHelpButton($userType) {
    global $helpAllowedUserTypes;
    return in_array($userType, $helpAllowedUserTypes);
}

// ============================================================
// CONFIGURACIÓN DE MENSAJES
// ============================================================

// Título del menú de ayuda
define('HELP_MENU_TITLE', 'Centro de Ayuda');

// Subtítulo del menú de ayuda
define('HELP_MENU_SUBTITLE', '¿En qué podemos ayudarte?');

// Texto del footer del menú
define('HELP_MENU_FOOTER_TEXT', '¿Necesitas ayuda inmediata?');

// Texto del enlace del footer
define('HELP_MENU_FOOTER_LINK_TEXT', 'Visita nuestro portal de soporte');

// Tooltip del botón principal
define('HELP_BUTTON_TOOLTIP', 'Centro de Ayuda');

// ============================================================
// CONFIGURACIÓN DE RESPONSIVE
// ============================================================

// Posición en móviles (desde abajo en píxeles)
define('HELP_MOBILE_POSITION_BOTTOM', 110);

// Posición en móviles (desde la derecha en píxeles)
define('HELP_MOBILE_POSITION_RIGHT', 20);

// Tamaño del botón en móviles
define('HELP_MOBILE_BUTTON_SIZE', 55);

// ============================================================
// FUNCIONES AUXILIARES
// ============================================================

/**
 * Obtiene las opciones de menú habilitadas y ordenadas
 * @return array Opciones de menú ordenadas
 */
function getEnabledHelpMenuOptions() {
    global $helpMenuOptions;
    
    // Filtrar solo las opciones habilitadas
    $enabled = array_filter($helpMenuOptions, function($option) {
        return $option['enabled'] === true;
    });
    
    // Ordenar por el campo 'order'
    usort($enabled, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    
    return $enabled;
}

/**
 * Genera el HTML para las opciones del menú
 * @return string HTML de las opciones
 */
function generateHelpMenuOptionsHTML() {
    $options = getEnabledHelpMenuOptions();
    $html = '';
    
    $index = 1;
    foreach ($options as $key => $option) {
        $html .= sprintf(
            '<a href="#" class="help-menu-item" onclick="helpMenuAction(\'%s\', event)" data-index="%d">
                <div class="help-menu-icon" style="background: linear-gradient(135deg, %s 0%%, %s 100%%);">
                    <i class="%s"></i>
                </div>
                <div class="help-menu-content">
                    <h5 class="help-menu-item-title">%s</h5>
                    <p class="help-menu-item-desc">%s</p>
                </div>
                <i class="fa fa-chevron-right help-menu-arrow"></i>
            </a>',
            $key,
            $index,
            $option['color_start'],
            $option['color_end'],
            $option['icon'],
            $option['title'],
            $option['description']
        );
        $index++;
    }
    
    return $html;
}

/**
 * Genera el objeto JavaScript con las configuraciones
 * @return string JavaScript object
 */
function generateHelpConfigJS() {
    global $helpMenuOptions;
    
    $config = [
        'options' => $helpMenuOptions,
        'analytics' => HELP_ENABLE_ANALYTICS,
        'analyticsCategory' => HELP_ANALYTICS_CATEGORY
    ];
    
    return json_encode($config);
}

// ============================================================
// CONFIGURACIÓN PERSONALIZADA POR INSTITUCIÓN (OPCIONAL)
// ============================================================

/**
 * Obtiene configuración personalizada por institución
 * Permite a cada institución tener sus propias URLs de ayuda
 * 
 * @param int $institutionId ID de la institución
 * @return array|null Configuración personalizada o null
 */
function getInstitutionHelpConfig($institutionId) {
    // Implementar lógica para obtener configuración personalizada de la BD
    // Por ejemplo:
    /*
    global $conexion;
    $query = "SELECT help_config FROM instituciones WHERE ins_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $institutionId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return json_decode($row['help_config'], true);
    }
    */
    return null;
}

?>

