<?php
/**
 * Clase de Headers de Seguridad HTTP
 * 
 * Implementa headers de seguridad esenciales para proteger la aplicación
 * contra ataques comunes como XSS, Clickjacking, MIME sniffing, etc.
 */
class SecurityHeaders {
    
    /**
     * Aplica todos los headers de seguridad recomendados
     * 
     * @param bool $strict Si es true, aplica política CSP más restrictiva
     * @return void
     */
    public static function aplicar($strict = false) {
        self::contentSecurityPolicy($strict);
        self::xFrameOptions();
        self::xContentTypeOptions();
        self::xXssProtection();
        self::referrerPolicy();
        self::permissionsPolicy();
        self::strictTransportSecurity();
    }
    
    /**
     * Content Security Policy (CSP) - Previene XSS
     * 
     * @param bool $strict Si es true, aplica política más restrictiva
     * @return void
     */
    public static function contentSecurityPolicy($strict = false) {
        if ($strict) {
            // Política más restrictiva (puede romper funcionalidad existente)
            header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; frame-ancestors 'self';");
        } else {
            // Política permisiva (compatible con código existente)
            // NOTA: Se recomienda refinar progresivamente
            header("Content-Security-Policy: default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'; frame-ancestors 'self';");
        }
    }
    
    /**
     * X-Frame-Options - Previene Clickjacking
     * 
     * @return void
     */
    public static function xFrameOptions() {
        header("X-Frame-Options: SAMEORIGIN");
    }
    
    /**
     * X-Content-Type-Options - Previene MIME sniffing
     * 
     * @return void
     */
    public static function xContentTypeOptions() {
        header("X-Content-Type-Options: nosniff");
    }
    
    /**
     * X-XSS-Protection - Activa filtro XSS del navegador (legacy)
     * 
     * @return void
     */
    public static function xXssProtection() {
        header("X-XSS-Protection: 1; mode=block");
    }
    
    /**
     * Referrer-Policy - Controla información en header Referer
     * 
     * @return void
     */
    public static function referrerPolicy() {
        header("Referrer-Policy: strict-origin-when-cross-origin");
    }
    
    /**
     * Permissions-Policy - Controla características del navegador
     * 
     * @return void
     */
    public static function permissionsPolicy() {
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    }
    
    /**
     * Strict-Transport-Security (HSTS) - Solo HTTPS
     * Solo aplica si está en HTTPS
     * 
     * @param int $maxAge Tiempo en segundos (default: 1 año)
     * @return void
     */
    public static function strictTransportSecurity($maxAge = 31536000) {
        // Solo aplicar si está en HTTPS
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header("Strict-Transport-Security: max-age={$maxAge}; includeSubDomains; preload");
        }
    }
}

// Auto-aplicar headers cuando se incluye el archivo (compatibilidad con código legacy)
SecurityHeaders::aplicar();

