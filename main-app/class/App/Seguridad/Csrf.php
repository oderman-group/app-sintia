<?php
/**
 * Clase de protección CSRF (Cross-Site Request Forgery)
 * 
 * Proporciona métodos para generar y validar tokens CSRF
 * que protegen contra ataques de falsificación de peticiones entre sitios.
 */
class Csrf {
    
    /**
     * Genera un token CSRF único para la sesión actual
     * 
     * @return string Token CSRF de 64 caracteres hexadecimales
     */
    public static function generarToken() {
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        
        // Regenerar token cada 2 horas por seguridad
        if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > 7200) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Valida un token CSRF recibido contra el token de la sesión
     * 
     * @param string $token Token a validar
     * @return bool True si el token es válido, False en caso contrario
     */
    public static function validarToken($token) {
        if (empty($token) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        // Usar hash_equals para prevenir timing attacks
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Genera el HTML de un campo hidden con el token CSRF
     * 
     * @return string HTML del campo input hidden
     */
    public static function campoHTML() {
        $token = self::generarToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Valida el token CSRF de una petición POST/GET y termina la ejecución si es inválido
     * 
     * @param bool $ajax Si es true, retorna JSON; si es false, muestra HTML
     * @return void
     */
    public static function verificar($ajax = false) {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        
        if (!self::validarToken($token)) {
            // Log del intento
            error_log("Intento de CSRF bloqueado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . 
                      " - URL: " . ($_SERVER['REQUEST_URI'] ?? 'unknown') . 
                      " - User: " . ($_SESSION['id'] ?? 'anonymous'));
            
            if ($ajax) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => '⚠️ Tu sesión ha expirado o el token de seguridad es inválido. La página se recargará automáticamente.',
                    'code' => 'CSRF_INVALID',
                    'reload' => true
                ]);
            } else {
                // Solo destruir sesión si existe una activa
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_destroy();
                }
                echo '<html><head><meta charset="UTF-8"></head><body>
                    <div style="text-align: center; padding: 50px; font-family: Arial;">
                        <h2>⚠️ Token de Seguridad Inválido</h2>
                        <p>Por razones de seguridad, esta acción no pudo ser completada.</p>
                        <p>Por favor, <a href="'.REDIRECT_ROUTE.'/index.php">regresa al inicio</a>.</p>
                    </div>
                    </body></html>';
            }
            exit();
        }
    }
    
    /**
     * Obtiene el token CSRF actual (útil para AJAX)
     * 
     * @return string Token CSRF actual
     */
    public static function obtenerToken() {
        return self::generarToken();
    }
}

/**
 * Funciones de compatibilidad con código legacy
 * Mantienen las funciones antiguas pero llaman a la clase
 */
if (!function_exists('generarTokenCSRF')) {
    function generarTokenCSRF() {
        return Csrf::generarToken();
    }
}

if (!function_exists('validarTokenCSRF')) {
    function validarTokenCSRF($token) {
        return Csrf::validarToken($token);
    }
}

if (!function_exists('campoTokenCSRF')) {
    function campoTokenCSRF() {
        return Csrf::campoHTML();
    }
}

if (!function_exists('verificarTokenCSRF')) {
    function verificarTokenCSRF($ajax = false) {
        Csrf::verificar($ajax);
    }
}

if (!function_exists('obtenerTokenCSRF')) {
    function obtenerTokenCSRF() {
        return Csrf::obtenerToken();
    }
}


