<?php

/**
 * Sistema de traducción moderno para SINTIA
 * Soporta múltiples idiomas con fallback inteligente
 * 
 * @author SINTIA Dev Team
 * @version 1.0
 */
class Traductor {
    
    private static $traducciones = [];
    private static $idioma = 'ES';
    private static $cacheActivo = true;
    private static $dirTraducciones = '';
    private static $registrarPendientes = true;
    
    /**
     * Inicializar el sistema de traducción
     */
    public static function inicializar($idioma = 'ES', $activarCache = true) {
        self::$idioma = strtoupper($idioma);
        self::$cacheActivo = $activarCache;
        self::$dirTraducciones = dirname(__FILE__) . '/traducciones/';
        
        // Crear directorio si no existe
        if (!is_dir(self::$dirTraducciones)) {
            mkdir(self::$dirTraducciones, 0755, true);
        }
        
        self::cargarTraducciones();
    }
    
    /**
     * Cargar traducciones desde archivo JSON
     */
    private static function cargarTraducciones() {
        $archivo = self::$dirTraducciones . self::$idioma . '.json';
        
        if (file_exists($archivo)) {
            $contenido = file_get_contents($archivo);
            self::$traducciones = json_decode($contenido, true) ?? [];
        } else {
            self::$traducciones = [];
        }
    }
    
    /**
     * Función principal de traducción
     * 
     * @param string $clave - Clave de traducción (ej: 'estudiantes.agregar_nuevo')
     * @param array $variables - Variables a reemplazar (ej: ['nombre' => 'Juan'])
     * @param string $contexto - Contexto adicional (opcional)
     * @return string - Texto traducido
     */
    public static function traducir($clave, $variables = [], $contexto = '') {
        // Construir clave completa
        $claveCompleta = $contexto ? "{$contexto}.{$clave}" : $clave;
        
        // Buscar traducción
        if (isset(self::$traducciones[$claveCompleta])) {
            return self::reemplazarVariables(self::$traducciones[$claveCompleta], $variables);
        }
        
        // Intentar sin contexto si no se encuentra
        if ($contexto && isset(self::$traducciones[$clave])) {
            return self::reemplazarVariables(self::$traducciones[$clave], $variables);
        }
        
        // Registrar como pendiente si está activado
        if (self::$registrarPendientes) {
            self::registrarPendiente($claveCompleta, $clave);
        }
        
        // Devolver fallback formateado
        return self::formatearFallback($clave);
    }
    
    /**
     * Alias corto para traducir
     */
    public static function t($clave, $variables = [], $contexto = '') {
        return self::traducir($clave, $variables, $contexto);
    }
    
    /**
     * Formatear clave como texto legible cuando no existe traducción
     */
    private static function formatearFallback($clave) {
        // Eliminar contexto si existe
        if (strpos($clave, '.') !== false) {
            $partes = explode('.', $clave);
            $clave = end($partes);
        }
        
        // Convertir "agregar_nuevo_estudiante" en "Agregar Nuevo Estudiante"
        $texto = str_replace(['_', '-'], ' ', $clave);
        return ucwords($texto);
    }
    
    /**
     * Reemplazar variables en el texto traducido
     */
    private static function reemplazarVariables($texto, $variables) {
        if (empty($variables)) {
            return $texto;
        }
        
        foreach ($variables as $key => $value) {
            $texto = str_replace(['{' . $key . '}', '{{' . $key . '}}'], $value, $texto);
        }
        
        return $texto;
    }
    
    /**
     * Registrar traducción pendiente para procesarla después
     */
    private static function registrarPendiente($claveCompleta, $claveOriginal) {
        $archivo = self::$dirTraducciones . 'pendientes.json';
        
        // Cargar pendientes existentes
        $pendientes = [];
        if (file_exists($archivo)) {
            $contenido = file_get_contents($archivo);
            $pendientes = json_decode($contenido, true) ?? [];
        }
        
        // Agregar si no existe
        if (!isset($pendientes[$claveCompleta])) {
            // Obtener información del archivo que lo llamó
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $caller = isset($backtrace[2]) ? $backtrace[2] : [];
            
            $pendientes[$claveCompleta] = [
                'clave_original' => $claveOriginal,
                'archivo' => isset($caller['file']) ? basename($caller['file']) : 'desconocido',
                'linea' => isset($caller['line']) ? $caller['line'] : 0,
                'fecha_deteccion' => date('Y-m-d H:i:s'),
                'idioma_actual' => self::$idioma
            ];
            
            // Guardar
            file_put_contents(
                $archivo, 
                json_encode($pendientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        }
    }
    
    /**
     * Cambiar idioma en tiempo real
     */
    public static function cambiarIdioma($idioma) {
        self::$idioma = strtoupper($idioma);
        self::cargarTraducciones();
    }
    
    /**
     * Obtener idioma actual
     */
    public static function getIdioma() {
        return self::$idioma;
    }
    
    /**
     * Verificar si existe una traducción
     */
    public static function existe($clave, $contexto = '') {
        $claveCompleta = $contexto ? "{$contexto}.{$clave}" : $clave;
        return isset(self::$traducciones[$claveCompleta]);
    }
    
    /**
     * Obtener todas las traducciones cargadas
     */
    public static function getTraducciones() {
        return self::$traducciones;
    }
    
    /**
     * Agregar traducción manualmente
     */
    public static function agregarTraduccion($clave, $texto, $idioma = null) {
        $idioma = $idioma ?? self::$idioma;
        $archivo = self::$dirTraducciones . $idioma . '.json';
        
        $traducciones = [];
        if (file_exists($archivo)) {
            $contenido = file_get_contents($archivo);
            $traducciones = json_decode($contenido, true) ?? [];
        }
        
        $traducciones[$clave] = $texto;
        
        file_put_contents(
            $archivo,
            json_encode($traducciones, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        
        // Recargar si es el idioma actual
        if ($idioma === self::$idioma) {
            self::cargarTraducciones();
        }
    }
    
    /**
     * Activar/desactivar registro de pendientes
     */
    public static function setRegistroPendientes($activar) {
        self::$registrarPendientes = $activar;
    }
}

/**
 * Función helper global para traducción rápida
 * Uso: echo __('estudiantes.agregar_nuevo');
 */
if (!function_exists('__')) {
    function __($clave, $variables = [], $contexto = '') {
        return Traductor::t($clave, $variables, $contexto);
    }
}

/**
 * Función helper para traducción con idioma específico
 */
if (!function_exists('___')) {
    function ___($clave, $idioma, $variables = []) {
        $idiomaActual = Traductor::getIdioma();
        Traductor::cambiarIdioma($idioma);
        $resultado = Traductor::t($clave, $variables);
        Traductor::cambiarIdioma($idiomaActual);
        return $resultado;
    }
}


