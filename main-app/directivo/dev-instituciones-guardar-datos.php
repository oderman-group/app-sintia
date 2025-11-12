<?php
/**
 * GUARDAR DATOS DE INSTITUCIÓN
 * Endpoint para actualizar todos los datos de una institución
 */

// Limpiar cualquier output previo
if (ob_get_level()) ob_end_clean();
ob_start();

// Habilitar errores para debugging pero no mostrarlos
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Establecer header JSON
header('Content-Type: application/json; charset=UTF-8');

try {
    require_once("session.php");
    
    // Verificar permisos
    Modulos::verificarPermisoDev();
    
    // Log de inicio
    error_log("=== INICIO GUARDAR DATOS INSTITUCIÓN ===");
    error_log("POST recibido: " . print_r($_POST, true));
    
    // Obtener datos del POST
    $ins_id = mysqli_real_escape_string($conexion, $_POST['ins_id'] ?? '');
    
    if (empty($ins_id)) {
        throw new Exception('ID de institución no proporcionado');
    }
    
    error_log("Institución ID: $ins_id");
    
    // Construir array de datos a actualizar
    $datosActualizar = [];
    
    // Campos de texto
    $camposTexto = [
        'ins_nombre', 'ins_siglas', 'ins_nit', 'ins_contacto_principal', 
        'ins_cargo_contacto', 'ins_telefono_principal', 'ins_celular_contacto',
        'ins_email_contacto', 'ins_email_institucion', 'ins_ciudad', 'ins_url_acceso',
        'ins_medio_info', 'ins_years', 'ins_concepto_deuda'
    ];
    
    foreach ($camposTexto as $campo) {
        if (isset($_POST[$campo])) {
            $valor = mysqli_real_escape_string($conexion, $_POST[$campo]);
            $datosActualizar[] = "$campo = '$valor'";
        }
    }
    
    // Campos de fecha - Convertir de DATE (YYYY-MM-DD) a DATETIME (YYYY-MM-DD HH:MM:SS)
    $camposFecha = ['ins_fecha_inicio', 'ins_fecha_renovacion'];
    foreach ($camposFecha as $campo) {
        if (isset($_POST[$campo]) && !empty($_POST[$campo])) {
            // El input date envía YYYY-MM-DD, necesitamos agregar hora para datetime
            $fechaOriginal = $_POST[$campo];
            
            // Validar que sea una fecha válida
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaOriginal)) {
                // Agregar hora 00:00:00 para el formato datetime
                $fechaDatetime = $fechaOriginal . ' 00:00:00';
                $valor = mysqli_real_escape_string($conexion, $fechaDatetime);
                $datosActualizar[] = "$campo = '$valor'";
                error_log("Fecha convertida para $campo: $fechaOriginal -> $fechaDatetime");
            } else {
                error_log("Formato de fecha inválido para $campo: $fechaOriginal");
            }
        }
    }
    
    // Campos numéricos
    $camposNumericos = [
        'ins_estado' => 'int',
        'ins_bloqueada' => 'int',
        'ins_deuda' => 'int',
        'ins_notificaciones_acudientes' => 'int',
        'ins_id_plan' => 'int',
        'ins_year_default' => 'int'
    ];
    
    foreach ($camposNumericos as $campo => $tipo) {
        if (isset($_POST[$campo])) {
            $valor = ($tipo === 'int') ? (int)$_POST[$campo] : (float)$_POST[$campo];
            $datosActualizar[] = "$campo = $valor";
        }
    }
    
    // Campo de valor de deuda (decimal)
    if (isset($_POST['ins_valor_deuda'])) {
        $valor = (float)$_POST['ins_valor_deuda'];
        $datosActualizar[] = "ins_valor_deuda = $valor";
    }
    
    // Validar que hay datos para actualizar
    if (empty($datosActualizar)) {
        throw new Exception('No hay datos para actualizar');
    }
    
    // Construir y ejecutar query
    $sql = "UPDATE " . $baseDatosServicios . ".instituciones SET ";
    $sql .= implode(', ', $datosActualizar);
    $sql .= " WHERE ins_id = '$ins_id' AND ins_enviroment = '" . ENVIROMENT . "'";
    
    error_log("SQL a ejecutar: $sql");
    
    if (!mysqli_query($conexion, $sql)) {
        $error = mysqli_error($conexion);
        error_log("Error MySQL: $error");
        throw new Exception('Error al actualizar en base de datos: ' . $error);
    }
    
    $filasAfectadas = mysqli_affected_rows($conexion);
    error_log("Filas afectadas: $filasAfectadas");
    
    // Registrar en historial de acciones
    try {
        // Definir variable requerida por el historial
        $idPaginaInterna = 'DV0011'; // ID de la página de edición de instituciones
        
        // Silenciar warnings temporalmente para el historial
        $error_reporting_original = error_reporting();
        error_reporting(0);
        
        // Capturar output del historial para que no interfiera con el JSON
        ob_start();
        @include("../compartido/historial-acciones-guardar.php");
        ob_end_clean();
        
        // Restaurar error reporting
        error_reporting($error_reporting_original);
    } catch (Exception $e) {
        error_log("Error en historial: " . $e->getMessage());
        // Restaurar error reporting en caso de excepción
        if (isset($error_reporting_original)) {
            error_reporting($error_reporting_original);
        }
        // No detener el proceso si falla el historial
    }
    
    error_log("=== FIN EXITOSO ===");
    
    // Limpiar cualquier output buffer antes de enviar JSON
    if (ob_get_level()) ob_clean();
    
    $response = json_encode([
        'success' => true,
        'message' => '✅ Datos de la institución actualizados exitosamente',
        'filas_afectadas' => $filasAfectadas
    ], JSON_UNESCAPED_UNICODE);
    
    echo $response;
    
    // Limpiar y enviar
    if (ob_get_level()) ob_end_flush();
    exit();
    
} catch (Exception $e) {
    error_log("=== ERROR CAPTURADO ===");
    error_log("Mensaje: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    
    // Limpiar cualquier output antes de enviar el error
    if (ob_get_level()) ob_clean();
    
    $response = json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_detallado' => $e->getMessage(),
        'archivo' => basename($e->getFile()),
        'linea' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
    
    echo $response;
    
    if (ob_get_level()) ob_end_flush();
    exit();
} catch (Throwable $t) {
    error_log("=== ERROR FATAL ===");
    error_log("Mensaje: " . $t->getMessage());
    
    // Limpiar cualquier output antes de enviar el error
    if (ob_get_level()) ob_clean();
    
    $response = json_encode([
        'success' => false,
        'message' => 'Error fatal: ' . $t->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    
    echo $response;
    
    if (ob_get_level()) ob_end_flush();
    exit();
}

