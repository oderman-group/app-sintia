<?php

/**
 * Extractor de textos para traducción
 * Escanea la carpeta main-app/directivo y encuentra textos en español
 * 
 * USO: php scripts/extraer-traducciones-directivo.php
 */

class ExtractorTraducciones {
    
    private $directorio;
    private $textosEncontrados = [];
    private $estadisticas = [
        'total_archivos' => 0,
        'archivos_con_textos' => 0,
        'total_textos' => 0
    ];
    
    public function __construct($directorio) {
        $this->directorio = $directorio;
    }
    
    /**
     * Escanear directorio completo
     */
    public function escanear() {
        $archivos = $this->obtenerArchivosPhp($this->directorio);
        $this->estadisticas['total_archivos'] = count($archivos);
        
        echo "📂 Escaneando " . count($archivos) . " archivos PHP en directivo/...\n\n";
        
        foreach ($archivos as $archivo) {
            $this->analizarArchivo($archivo);
        }
        
        $this->generarReporte();
    }
    
    /**
     * Obtener todos los archivos PHP del directorio
     */
    private function obtenerArchivosPhp($dir) {
        $archivos = [];
        $items = glob($dir . '/*');
        
        foreach ($items as $item) {
            if (is_file($item) && pathinfo($item, PATHINFO_EXTENSION) === 'php') {
                $archivos[] = $item;
            } elseif (is_dir($item) && basename($item) !== '.' && basename($item) !== '..') {
                $archivos = array_merge($archivos, $this->obtenerArchivosPhp($item));
            }
        }
        
        return $archivos;
    }
    
    /**
     * Analizar un archivo PHP
     */
    private function analizarArchivo($archivo) {
        $contenido = file_get_contents($archivo);
        $nombreArchivo = str_replace($this->directorio . '/', '', $archivo);
        $textosArchivo = [];
        
        // Patrón 1: Placeholders en inputs/textareas
        preg_match_all('/placeholder\s*=\s*["\']([^"\']+)["\']/i', $contenido, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $texto) {
                if ($this->esTextoEspanol($texto)) {
                    $textosArchivo[] = [
                        'tipo' => 'placeholder',
                        'texto' => $texto,
                        'clave_sugerida' => $this->generarClave($texto)
                    ];
                }
            }
        }
        
        // Patrón 2: Títulos, alt, aria-label
        preg_match_all('/(?:title|alt|aria-label)\s*=\s*["\']([^"\']+)["\']/i', $contenido, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $texto) {
                if ($this->esTextoEspanol($texto)) {
                    $textosArchivo[] = [
                        'tipo' => 'atributo',
                        'texto' => $texto,
                        'clave_sugerida' => $this->generarClave($texto)
                    ];
                }
            }
        }
        
        // Patrón 3: Textos entre etiquetas HTML comunes
        preg_match_all('/<(?:button|a|h[1-6]|label|span|p|div)(?:[^>]*)>([^<]{3,})<\//i', $contenido, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $texto) {
                $texto = trim($texto);
                if ($this->esTextoEspanol($texto) && strlen($texto) > 3) {
                    $textosArchivo[] = [
                        'tipo' => 'html_content',
                        'texto' => $texto,
                        'clave_sugerida' => $this->generarClave($texto)
                    ];
                }
            }
        }
        
        // Patrón 4: Echo con strings literales
        preg_match_all('/echo\s+["\']([^"\']{5,})["\']/i', $contenido, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $texto) {
                if ($this->esTextoEspanol($texto)) {
                    $textosArchivo[] = [
                        'tipo' => 'echo',
                        'texto' => $texto,
                        'clave_sugerida' => $this->generarClave($texto)
                    ];
                }
            }
        }
        
        if (!empty($textosArchivo)) {
            $this->textosEncontrados[$nombreArchivo] = $textosArchivo;
            $this->estadisticas['archivos_con_textos']++;
            $this->estadisticas['total_textos'] += count($textosArchivo);
        }
    }
    
    /**
     * Verificar si un texto está en español (contiene acentos o ñ, o palabras comunes)
     */
    private function esTextoEspanol($texto) {
        // Si contiene acentos o ñ
        if (preg_match('/[áéíóúñÁÉÍÓÚÑ]/u', $texto)) {
            return true;
        }
        
        // Palabras comunes en español
        $palabrasEspanol = [
            'agregar', 'nuevo', 'editar', 'eliminar', 'guardar', 'cancelar',
            'buscar', 'exportar', 'imprimir', 'estudiante', 'curso', 'docente',
            'matrícula', 'boletin', 'calificaciones', 'usuarios', 'por', 'para',
            'del', 'los', 'las', 'aquí', 'puede', 'puedes'
        ];
        
        $textoMin = mb_strtolower($texto);
        foreach ($palabrasEspanol as $palabra) {
            if (strpos($textoMin, $palabra) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generar una clave sugerida a partir del texto
     */
    private function generarClave($texto) {
        // Limpiar el texto
        $texto = strip_tags($texto);
        $texto = preg_replace('/[^\w\s]/u', '', $texto);
        $texto = mb_strtolower($texto);
        $texto = trim($texto);
        
        // Convertir espacios en guiones bajos
        $texto = preg_replace('/\s+/', '_', $texto);
        
        // Limitar longitud
        if (strlen($texto) > 50) {
            $palabras = explode('_', $texto);
            $texto = implode('_', array_slice($palabras, 0, 4));
        }
        
        return $texto;
    }
    
    /**
     * Generar reporte de textos encontrados
     */
    private function generarReporte() {
        echo "\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "                    REPORTE DE EXTRACCIÓN                       \n";
        echo "═══════════════════════════════════════════════════════════════\n\n";
        
        echo "📊 ESTADÍSTICAS:\n";
        echo "   • Total de archivos escaneados: " . $this->estadisticas['total_archivos'] . "\n";
        echo "   • Archivos con textos en español: " . $this->estadisticas['archivos_con_textos'] . "\n";
        echo "   • Total de textos encontrados: " . $this->estadisticas['total_textos'] . "\n\n";
        
        echo "═══════════════════════════════════════════════════════════════\n\n";
        
        // Mostrar primeros 10 archivos con más textos
        echo "📁 TOP 10 ARCHIVOS CON MÁS TEXTOS PARA TRADUCIR:\n\n";
        
        $archivosOrdenados = $this->textosEncontrados;
        uasort($archivosOrdenados, function($a, $b) {
            return count($b) - count($a);
        });
        
        $contador = 1;
        foreach (array_slice($archivosOrdenados, 0, 10, true) as $archivo => $textos) {
            echo "   {$contador}. {$archivo} (" . count($textos) . " textos)\n";
            
            // Mostrar primeros 3 textos del archivo
            foreach (array_slice($textos, 0, 3) as $item) {
                $textoCorto = strlen($item['texto']) > 60 ? substr($item['texto'], 0, 60) . '...' : $item['texto'];
                echo "      • \"{$textoCorto}\"\n";
                echo "        → Clave sugerida: {$item['clave_sugerida']}\n";
            }
            
            if (count($textos) > 3) {
                echo "      ... y " . (count($textos) - 3) . " textos más\n";
            }
            echo "\n";
            $contador++;
        }
        
        echo "═══════════════════════════════════════════════════════════════\n\n";
        
        // Guardar reporte completo en JSON
        $this->guardarReporteJSON();
    }
    
    /**
     * Guardar reporte completo en formato JSON
     */
    private function guardarReporteJSON() {
        $reporte = [
            'fecha_generacion' => date('Y-m-d H:i:s'),
            'estadisticas' => $this->estadisticas,
            'textos_encontrados' => $this->textosEncontrados
        ];
        
        $archivoReporte = dirname(__DIR__) . '/config-general/traducciones/reporte-extraccion.json';
        file_put_contents(
            $archivoReporte,
            json_encode($reporte, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        
        echo "💾 Reporte completo guardado en: config-general/traducciones/reporte-extraccion.json\n\n";
    }
}

// Ejecutar el extractor
$directorioDirectivo = dirname(__DIR__) . '/main-app/directivo';

if (!is_dir($directorioDirectivo)) {
    die("❌ Error: No se encontró el directorio {$directorioDirectivo}\n");
}

$extractor = new ExtractorTraducciones($directorioDirectivo);
$extractor->escanear();

echo "✅ Proceso completado exitosamente.\n";
echo "   Usa los resultados para crear las traducciones en:\n";
echo "   • config-general/traducciones/ES.json\n";
echo "   • config-general/traducciones/EN.json\n\n";


