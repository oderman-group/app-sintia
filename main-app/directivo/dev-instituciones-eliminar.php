<?php
/**
 * ELIMINAR INSTITUCIONES
 * Endpoint para eliminar instituciones y sus datos asociados de forma selectiva
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
    require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    
    // Verificar permisos
    Modulos::verificarPermisoDev();
    
    error_log("=== INICIO ELIMINAR INSTITUCIONES ===");
    
    // Obtener datos del POST
    $institucionesJSON = $_POST['instituciones'] ?? '';
    $opcionesJSON = $_POST['opciones'] ?? '';
    
    if (empty($institucionesJSON) || empty($opcionesJSON)) {
        throw new Exception('Datos incompletos para procesar la eliminación');
    }
    
    $instituciones = json_decode($institucionesJSON, true);
    $opciones = json_decode($opcionesJSON, true);
    
    if (empty($instituciones) || !is_array($instituciones)) {
        throw new Exception('No se recibieron instituciones válidas');
    }
    
    error_log("Instituciones a eliminar: " . count($instituciones));
    error_log("Opciones: " . print_r($opciones, true));
    
    $respaldosCreados = [];
    $institucionesEliminadas = [];
    $errores = [];
    
    // Procesar cada institución
    foreach ($instituciones as $inst) {
        $insId = $inst['id'];
        $insNombre = $inst['nombre'];
        $insBd = $inst['bd'];
        
        error_log("Procesando institución ID: $insId - $insNombre");
        
        try {
            // 1. CREAR ARCHIVO DE RESPALDO
            $archivoRespaldo = generarArchivoRespaldo($conexion, $config, $baseDatosServicios, $insId, $insNombre, $insBd, $opciones);
            $respaldosCreados[] = $archivoRespaldo;
            
            // 2. ELIMINAR DATOS SEGÚN OPCIONES SELECCIONADAS
            $datosEliminados = eliminarDatosInstitucion($conexion, $config, $baseDatosServicios, $insId, $insBd, $opciones);
            
            $institucionesEliminadas[] = [
                'id' => $insId,
                'nombre' => $insNombre,
                'bd' => $insBd,
                'archivo_respaldo' => $archivoRespaldo,
                'datos_eliminados' => $datosEliminados
            ];
            
            error_log("✅ Institución $insNombre eliminada exitosamente");
            
        } catch (Exception $e) {
            error_log("❌ Error eliminando institución $insNombre: " . $e->getMessage());
            $errores[] = "Institución $insNombre: " . $e->getMessage();
        }
    }
    
    // 3. ENVIAR CORREO DE NOTIFICACIÓN
    if (!empty($institucionesEliminadas)) {
        try {
            enviarNotificacionEliminacion($institucionesEliminadas, $respaldosCreados);
            error_log("✅ Correo de notificación enviado");
        } catch (Exception $e) {
            error_log("⚠️ No se pudo enviar correo de notificación: " . $e->getMessage());
            // No detener el proceso si falla el correo
        }
    }
    
    // Registrar en historial
    try {
        $idPaginaInterna = 'DV0005';
        $error_reporting_original = error_reporting();
        error_reporting(0);
        ob_start();
        @include("../compartido/historial-acciones-guardar.php");
        ob_end_clean();
        error_reporting($error_reporting_original);
    } catch (Exception $e) {
        error_log("Error en historial: " . $e->getMessage());
    }
    
    error_log("=== FIN ELIMINACIÓN INSTITUCIONES ===");
    
    if (ob_get_level()) ob_clean();
    
    $mensajeExito = count($institucionesEliminadas) . ' institución(es) eliminada(s) exitosamente.';
    if (!empty($errores)) {
        $mensajeExito .= ' Se encontraron ' . count($errores) . ' error(es).';
    }
    
    $response = json_encode([
        'success' => true,
        'message' => $mensajeExito,
        'eliminadas' => count($institucionesEliminadas),
        'errores' => $errores,
        'respaldos' => $respaldosCreados
    ], JSON_UNESCAPED_UNICODE);
    
    echo $response;
    
    if (ob_get_level()) ob_end_flush();
    exit();
    
} catch (Exception $e) {
    error_log("=== ERROR ELIMINAR INSTITUCIONES ===");
    error_log("Mensaje: " . $e->getMessage());
    
    if (ob_get_level()) ob_clean();
    
    $response = json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    
    echo $response;
    
    if (ob_get_level()) ob_end_flush();
    exit();
}

/**
 * Genera un archivo .txt de respaldo con todos los datos de la institución
 */
function generarArchivoRespaldo($conexion, $config, $baseDatosServicios, $insId, $insNombre, $insBd, $opciones) {
    $rutaRespaldos = ROOT_PATH . '/files-general/respaldos-instituciones';
    if (!file_exists($rutaRespaldos)) {
        mkdir($rutaRespaldos, 0777, true);
    }
    
    $nombreArchivo = 'respaldo_inst_' . $insId . '_' . date('Y-m-d_His') . '.txt';
    $rutaCompleta = $rutaRespaldos . '/' . $nombreArchivo;
    
    $contenido = "=====================================================\n";
    $contenido .= "RESPALDO DE INSTITUCIÓN ELIMINADA\n";
    $contenido .= "=====================================================\n";
    $contenido .= "Fecha de eliminación: " . date('Y-m-d H:i:s') . "\n";
    $contenido .= "Usuario responsable: " . ($_SESSION['id'] ?? 'N/A') . " - " . ($_SESSION['nombre'] ?? 'N/A') . "\n";
    $contenido .= "=====================================================\n\n";
    
    // Datos de la institución
    $consulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".instituciones 
        WHERE ins_id = '$insId' AND ins_enviroment = '" . ENVIROMENT . "'");
    
    if ($consulta && $row = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
        $contenido .= "DATOS DE LA INSTITUCIÓN:\n";
        $contenido .= "-----------------------------------------------------\n";
        foreach ($row as $campo => $valor) {
            if (!is_numeric($campo)) {
                $contenido .= str_pad($campo, 30) . ": " . ($valor ?? 'NULL') . "\n";
            }
        }
        $contenido .= "\n";
    }
    
    // Estadísticas según opciones seleccionadas
    $contenido .= "DATOS A ELIMINAR:\n";
    $contenido .= "-----------------------------------------------------\n";
    
    if ($opciones['usuarios'] || $opciones['institucion_completa']) {
        $consultaUsuarios = mysqli_query($conexion, "SELECT COUNT(*) as total FROM " . BD_GENERAL . ".usuarios 
            WHERE institucion = '$insId'");
        $totalUsuarios = ($consultaUsuarios && $row = mysqli_fetch_array($consultaUsuarios)) ? $row['total'] : 0;
        $contenido .= "Usuarios: $totalUsuarios\n";
    }
    
    if ($opciones['configuracion'] || $opciones['institucion_completa']) {
        $consultaConfig = mysqli_query($conexion, "SELECT COUNT(*) as total FROM " . BD_ADMIN . ".configuracion 
            WHERE conf_id_institucion = '$insId'");
        $totalConfig = ($consultaConfig && $row = mysqli_fetch_array($consultaConfig)) ? $row['total'] : 0;
        $contenido .= "Registros de configuración: $totalConfig\n";
    }
    
    if ($opciones['academico'] || $opciones['institucion_completa']) {
        $consultaCargas = mysqli_query($conexion, "SELECT COUNT(*) as total FROM " . BD_ACADEMICA . ".academico_cargas 
            WHERE institucion = '$insId'");
        $totalCargas = ($consultaCargas && $row = mysqli_fetch_array($consultaCargas)) ? $row['total'] : 0;
        $contenido .= "Cargas académicas: $totalCargas\n";
    }
    
    if ($opciones['financiero'] || $opciones['institucion_completa']) {
        $consultaFacturas = mysqli_query($conexion, "SELECT COUNT(*) as total FROM " . BD_FINANCIERA . ".finanzas_cuentas 
            WHERE institucion = '$insId'");
        $totalFacturas = ($consultaFacturas && $row = mysqli_fetch_array($consultaFacturas)) ? $row['total'] : 0;
        $contenido .= "Facturas financieras: $totalFacturas\n";
    }
    
    $contenido .= "\n";
    $contenido .= "OPCIONES DE ELIMINACIÓN SELECCIONADAS:\n";
    $contenido .= "-----------------------------------------------------\n";
    $contenido .= "Eliminar usuarios: " . ($opciones['usuarios'] ? 'SÍ' : 'NO') . "\n";
    $contenido .= "Eliminar configuración: " . ($opciones['configuracion'] ? 'SÍ' : 'NO') . "\n";
    $contenido .= "Eliminar académico: " . ($opciones['academico'] ? 'SÍ' : 'NO') . "\n";
    $contenido .= "Eliminar financiero: " . ($opciones['financiero'] ? 'SÍ' : 'NO') . "\n";
    $contenido .= "Eliminar otros: " . ($opciones['otros'] ? 'SÍ' : 'NO') . "\n";
    $contenido .= "Eliminar institución completa: " . ($opciones['institucion_completa'] ? 'SÍ' : 'NO') . "\n";
    $contenido .= "\n";
    $contenido .= "=====================================================\n";
    $contenido .= "FIN DEL RESPALDO\n";
    $contenido .= "=====================================================\n";
    
    file_put_contents($rutaCompleta, $contenido);
    
    return $rutaCompleta;
}

/**
 * Elimina datos de la institución según las opciones seleccionadas
 */
function eliminarDatosInstitucion($conexion, $config, $baseDatosServicios, $insId, $insBd, $opciones) {
    $datosEliminados = [];
    
    // Usar PDO para transacciones
    $conexionPDO = Conexion::newConnection('PDO');
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    try {
        $conexionPDO->beginTransaction();
        
        // USUARIOS
        if ($opciones['usuarios'] || $opciones['institucion_completa']) {
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_GENERAL . ".usuarios WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['usuarios'] = $stmt->rowCount();
        }
        
        // CONFIGURACIÓN
        if ($opciones['configuracion'] || $opciones['institucion_completa']) {
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ADMIN . ".configuracion WHERE conf_id_institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['configuracion'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ADMIN . ".general_informacion WHERE info_institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['general_informacion'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ADMISIONES . ".config_instituciones WHERE cfgi_id_institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['config_instituciones_admisiones'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_FINANCIERA . ".configuration WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['configuration_financiera'] = $stmt->rowCount();
        }
        
        // ACADÉMICO
        if ($opciones['academico'] || $opciones['institucion_completa']) {
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ACADEMICA . ".academico_cargas WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['cargas'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ACADEMICA . ".academico_actividades WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['actividades'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ACADEMICA . ".academico_calificaciones WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['calificaciones'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ACADEMICA . ".academico_materias WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['materias'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ACADEMICA . ".academico_grados WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['grados'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ACADEMICA . ".academico_grupos WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['grupos'] = $stmt->rowCount();
        }
        
        // FINANCIERO
        if ($opciones['financiero'] || $opciones['institucion_completa']) {
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_FINANCIERA . ".finanzas_cuentas WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['facturas'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_FINANCIERA . ".payments_invoiced WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['payments_invoiced'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_FINANCIERA . ".transaction_items WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['transaction_items'] = $stmt->rowCount();
        }
        
        // OTROS DATOS
        if ($opciones['otros'] || $opciones['institucion_completa']) {
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ADMIN . ".general_alertas WHERE alr_institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['alertas'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_DISCIPLINA . ".disciplina_reportes WHERE institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['reportes_disciplina'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . BD_ADMIN . ".seguridad_historial_acciones WHERE hil_institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['historial_acciones'] = $stmt->rowCount();
        }
        
        // ELIMINAR REGISTRO DE INSTITUCIÓN (si se seleccionó institución completa)
        if ($opciones['institucion_completa']) {
            // Primero eliminar registros relacionados en instituciones_periodos
            $stmt = $conexionPDO->prepare("DELETE FROM " . $baseDatosServicios . ".instituciones_periodos WHERE inspp_institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['instituciones_periodos'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . $baseDatosServicios . ".instituciones_modulos WHERE ipmod_institucion = ?");
            $stmt->execute([$insId]);
            $datosEliminados['instituciones_modulos'] = $stmt->rowCount();
            
            $stmt = $conexionPDO->prepare("DELETE FROM " . $baseDatosServicios . ".instituciones 
                WHERE ins_id = ? AND ins_enviroment = ?");
            $stmt->execute([$insId, ENVIROMENT]);
            $datosEliminados['registro_institucion'] = $stmt->rowCount();
        }
        
        $conexionPDO->commit();
    } catch (Exception $e) {
        $conexionPDO->rollBack();
        throw new Exception("Error al eliminar datos de BD: " . $e->getMessage());
    }
    
    return $datosEliminados;
}

/**
 * Envía correo de notificación a info@oderman-group.com
 */
function enviarNotificacionEliminacion($institucionesEliminadas, $respaldosCreados) {
    global $config;
    
    $dataEmail = [
        'institucion_id' => $config['conf_id_institucion'] ?? 1,
        'institucion_agno' => date('Y'),
        'institucion_nombre' => 'SISTEMA SINTIA',
        'usuario_email' => 'info@oderman-group.com',
        'usuario_nombre' => 'Equipo ODERMAN',
        'usuario_id' => $_SESSION['id'] ?? 1,
        'instituciones_eliminadas' => $institucionesEliminadas,
        'total_eliminadas' => count($institucionesEliminadas),
        'fecha_eliminacion' => date('Y-m-d H:i:s'),
        'responsable' => $_SESSION["id"] . " - ". $_SESSION['datosUsuario']['uss_nombre'] ?? 'N/A'
    ];
    
    $asunto = '🗑️ Eliminación de Instituciones - SINTIA';
    $bodyTemplateRoute = ROOT_PATH . '/config-general/plantilla-email-eliminacion-instituciones.php';
    
    // Enviar con archivos de respaldo adjuntos
    EnviarEmail::enviar($dataEmail, $asunto, $bodyTemplateRoute, null, $respaldosCreados);
}
?>

