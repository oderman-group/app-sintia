<?php
/**
 * Clase de Sanitización y Validación
 * 
 * Proporciona métodos para sanitizar y validar datos de entrada del usuario
 * para prevenir XSS, SQL Injection y otras vulnerabilidades.
 */
class Sanitizacion {
    
    /**
     * Sanitiza texto para output HTML
     * Previene XSS
     * 
     * @param string $texto Texto a sanitizar
     * @return string Texto sanitizado
     */
    public static function html($texto) {
        if (is_null($texto)) return '';
        return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitiza para atributos HTML
     * 
     * @param string $texto Texto a sanitizar
     * @return string Texto sanitizado
     */
    public static function atributo($texto) {
        if (is_null($texto)) return '';
        return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitiza para JavaScript
     * 
     * @param mixed $data Dato a sanitizar
     * @return string JSON sanitizado
     */
    public static function js($data) {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
    
    /**
     * Sanitiza para SQL (usar SOLO con prepared statements)
     * 
     * @param string $texto Texto a sanitizar
     * @param mysqli $conexion Conexión de base de datos
     * @return string Texto escapado
     */
    public static function sql($texto, $conexion) {
        if (is_null($texto)) return '';
        return mysqli_real_escape_string($conexion, $texto);
    }
    
    /**
     * Limpia input de usuario (elimina tags HTML)
     * 
     * @param string $texto Texto a limpiar
     * @param int $maxLongitud Longitud máxima permitida
     * @return string Texto limpio
     */
    public static function input($texto, $maxLongitud = 255) {
        if (is_null($texto)) return '';
        $texto = trim(strip_tags($texto));
        if(strlen($texto) > $maxLongitud) {
            $texto = substr($texto, 0, $maxLongitud);
        }
        return $texto;
    }
    
    /**
     * Sanitiza para URLs
     * 
     * @param string $url URL a sanitizar
     * @return string URL sanitizada
     */
    public static function url($url) {
        if (is_null($url)) return '';
        return filter_var($url, FILTER_SANITIZE_URL);
    }
    
    /**
     * Sanitiza email
     * 
     * @param string $email Email a sanitizar
     * @return string Email sanitizado
     */
    public static function email($email) {
        if (is_null($email)) return '';
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
}

/**
 * Clase de Validación
 */
class Validador {
    
    /**
     * Valida email
     * 
     * @param string $email Email a validar
     * @return bool True si es válido
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valida entero con rango opcional
     * 
     * @param mixed $valor Valor a validar
     * @param int|null $min Valor mínimo
     * @param int|null $max Valor máximo
     * @return int|false Entero validado o false
     */
    public static function entero($valor, $min = null, $max = null) {
        $valor = filter_var($valor, FILTER_VALIDATE_INT);
        if($valor === false) return false;
        
        if($min !== null && $valor < $min) return false;
        if($max !== null && $valor > $max) return false;
        
        return $valor;
    }
    
    /**
     * Valida texto con longitud
     * 
     * @param string $texto Texto a validar
     * @param int $minLongitud Longitud mínima
     * @param int $maxLongitud Longitud máxima
     * @return bool True si es válido
     */
    public static function texto($texto, $minLongitud = 1, $maxLongitud = 255) {
        $longitud = strlen($texto);
        return $longitud >= $minLongitud && $longitud <= $maxLongitud;
    }
    
    /**
     * Valida URL
     * 
     * @param string $url URL a validar
     * @return bool True si es válida
     */
    public static function url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Valida fecha en formato Y-m-d
     * 
     * @param string $fecha Fecha a validar
     * @return bool True si es válida
     */
    public static function fecha($fecha) {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }
    
    /**
     * Valida que solo contenga caracteres alfanuméricos
     * 
     * @param string $texto Texto a validar
     * @return bool True si es alfanumérico
     */
    public static function alfanumerico($texto) {
        return ctype_alnum($texto);
    }
}

