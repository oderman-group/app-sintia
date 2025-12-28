<?php
/**
 * Clase para centralizar la gestión de medios de pago
 * 
 * Esta clase proporciona métodos estáticos para obtener la lista de medios de pago
 * y generar elementos HTML de selección de forma consistente en toda la aplicación.
 */
class MediosPago {
    
    /**
     * Obtiene la lista completa de medios de pago
     * 
     * @return array Array asociativo con código => nombre de cada medio de pago
     */
    public static function obtenerMediosPago(): array {
        return [
            'EFECTIVO' => 'Efectivo',
            'CHEQUE' => 'Cheque',
            'T_DEBITO' => 'T. Débito',
            'T_CREDITO' => 'T. Crédito',
            'TRANSFERENCIA' => 'Transferencia',
            'CONSIGNACION' => 'Consignación',
            'CRIPTOMONEDAS' => 'Criptomonedas',
            'OTROS' => 'Otros medios de pago'
        ];
    }
    
    /**
     * Genera las opciones HTML para un elemento <select>
     * 
     * @param string|null $valorSeleccionado Valor que debe estar seleccionado (opcional)
     * @param bool $incluirOpcionVacia Si es true, incluye una opción vacía al inicio
     * @param string $textoOpcionVacia Texto para la opción vacía (por defecto "Seleccione una opción")
     * @return string HTML con las opciones del select
     */
    public static function generarOpcionesSelect(
        ?string $valorSeleccionado = null,
        bool $incluirOpcionVacia = true,
        string $textoOpcionVacia = 'Seleccione una opción'
    ): string {
        $html = '';
        
        if ($incluirOpcionVacia) {
            $html .= '<option value="">' . htmlspecialchars($textoOpcionVacia) . '</option>' . "\n";
        }
        
        foreach (self::obtenerMediosPago() as $codigo => $nombre) {
            $selected = ($valorSeleccionado === $codigo) ? ' selected' : '';
            $html .= '<option value="' . htmlspecialchars($codigo) . '"' . $selected . '>' . 
                     htmlspecialchars($nombre) . '</option>' . "\n";
        }
        
        return $html;
    }
    
    /**
     * Obtiene el nombre de un medio de pago por su código
     * 
     * @param string $codigo Código del medio de pago
     * @return string|null Nombre del medio de pago o null si no existe
     */
    public static function obtenerNombrePorCodigo(string $codigo): ?string {
        $medios = self::obtenerMediosPago();
        return $medios[$codigo] ?? null;
    }
    
    /**
     * Valida si un código de medio de pago es válido
     * 
     * @param string $codigo Código a validar
     * @return bool True si el código es válido, false en caso contrario
     */
    public static function esCodigoValido(string $codigo): bool {
        return array_key_exists($codigo, self::obtenerMediosPago());
    }
}


