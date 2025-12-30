<?php
/**
 * Cron Job para generar facturas desde facturas recurrentes
 * 
 * Este archivo debe ejecutarse diariamente mediante un cron job.
 * Ejemplo de configuración en crontab:
 * 0 0 * * * /usr/bin/php /ruta/al/proyecto/main-app/compartido/generar-facturas-jobs.php [--env=ENV]
 * 
 * O ejecutar cada hora:
 * 0 * * * * /usr/bin/php /ruta/al/proyecto/main-app/compartido/generar-facturas-jobs.php [--env=ENV]
 * 
 * Parámetros:
 *   --env=ENV    : Entorno (PROD, TEST, LOCAL) - default: PROD
 */

// Configurar para ejecución CLI
if (php_sapi_name() !== 'cli') {
    die("Este script solo puede ejecutarse desde línea de comandos.\n");
}

// Establecer variables $_SERVER necesarias para CLI
if (!isset($_SERVER['DOCUMENT_ROOT']) || empty($_SERVER['DOCUMENT_ROOT'])) {
    // DOCUMENT_ROOT debe apuntar al directorio padre de app-sintia (htdocs)
    // Si estamos en C:\xampp\htdocs\app-sintia\main-app\compartido, DOCUMENT_ROOT = C:\xampp\htdocs
    $scriptDir = __DIR__; // main-app/compartido
    // Normalizar separadores de ruta
    $scriptDir = str_replace('\\', '/', $scriptDir);
    // Subir dos niveles desde main-app/compartido para llegar a la raíz del proyecto
    // Luego subir un nivel más para llegar a htdocs (DOCUMENT_ROOT)
    $rootProject = dirname(dirname($scriptDir)); // app-sintia
    $_SERVER['DOCUMENT_ROOT'] = dirname($rootProject); // htdocs
    // Asegurar formato Windows si es necesario
    if (DIRECTORY_SEPARATOR === '\\') {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']);
    }
}

// Cambiar al directorio raíz del proyecto (app-sintia) para asegurar rutas relativas correctas
$rootProject = dirname(dirname(__DIR__)); // app-sintia
chdir($rootProject);

if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}
if (!isset($_SERVER['SERVER_PORT'])) {
    $_SERVER['SERVER_PORT'] = 80;
}
if (!isset($_SERVER['HTTPS'])) {
    $_SERVER['HTTPS'] = 'off';
}
if (!isset($_SERVER['REMOTE_ADDR'])) {
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}
if (!isset($_SERVER['HTTP_REFERER'])) {
    $_SERVER['HTTP_REFERER'] = '';
}
if (!isset($_SERVER['QUERY_STRING'])) {
    $_SERVER['QUERY_STRING'] = '';
}
if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    $_SERVER['HTTP_USER_AGENT'] = 'CLI-CronJob';
}

// Inicializar $_SESSION si no existe (necesario para algunos métodos)
if (!isset($_SESSION)) {
    $_SESSION = [];
}

// Parsear argumentos para establecer entorno
$entorno = 'PROD';

// Primero buscar --env para establecerlo antes de incluir constantes
foreach ($argv as $arg) {
    if (strpos($arg, '--env=') === 0) {
        $entorno = strtoupper(trim(substr($arg, 6)));
        break;
    }
}

// Validar que el entorno sea válido
if (!in_array($entorno, ['PROD', 'TEST', 'LOCAL'])) {
    $entorno = 'PROD';
}

// Establecer entorno como primer argumento para que constantes.php lo detecte
// IMPORTANTE: Establecer tanto en $argv como en $_SERVER['argv'] y también como variable global
$argv[1] = $entorno;
if (isset($_SERVER['argv'])) {
    $_SERVER['argv'][1] = $entorno;
}
// Establecer también como variable de entorno para asegurar que se lea correctamente
putenv("ENV={$entorno}");

// Incluir constantes y clases (debe ir después de establecer $argv[1])
// Ahora estamos en la raíz del proyecto (app-sintia), así que la ruta es relativa
require_once("config-general/constantes.php");

// Verificar que el entorno se estableció correctamente
if (defined('ENVIROMENT')) {
    echo "Entorno detectado: " . ENVIROMENT . "\n";
} else {
    echo "ADVERTENCIA: ENVIROMENT no está definido. Forzando PROD.\n";
    define('ENVIROMENT', 'PROD');
}

require_once(ROOT_PATH."/main-app/class/Movimientos.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

// Configurar zona horaria (Colombia)
date_default_timezone_set("America/Bogota");

// Obtener fecha actual
$diaActual = (int)date('d');
$mesActual = (int)date('m');
$yearActual = (int)date('Y');
$fechaActual = date('Y-m-d');
$diasMes = cal_days_in_month(CAL_GREGORIAN, $mesActual, $yearActual);

echo "[" . date('Y-m-d H:i:s') . "] Iniciando generación de facturas recurrentes...\n";
echo "Entorno: " . (defined('ENVIROMENT') ? ENVIROMENT : $entorno) . "\n";
echo "Fecha actual: {$fechaActual}\n";
echo "Día actual: {$diaActual}\n\n";

try {
    // Conectar a la base de datos usando la clase Conexion
    $conexion = Conexion::newConnection('MYSQL');
    if (!$conexion) {
        throw new Exception("No se pudo establecer conexión a la base de datos");
    }
    
    echo "Conexión a la base de datos establecida correctamente.\n";
    
    // Obtener todas las facturas recurrentes activas (no eliminadas)
    echo "Buscando facturas recurrentes activas...\n";
    $consultaJobs = Movimientos::listarRecurrentesJobs($conexion);
    
    if (!$consultaJobs) {
        throw new Exception("Error al consultar facturas recurrentes");
    }
    
    $totalRecurrentes = mysqli_num_rows($consultaJobs);
    echo "Se encontraron {$totalRecurrentes} facturas recurrentes activas.\n\n";
    
    if ($totalRecurrentes == 0) {
        echo "No hay facturas recurrentes para procesar.\n";
        exit(0);
    }
    
    $facturasGeneradas = 0;
    $errores = 0;
    $omitidas = 0;
    
    while($resultadoJobs = mysqli_fetch_array($consultaJobs, MYSQLI_BOTH)){
        
        // Verificar que la factura recurrente no esté eliminada
        if (!empty($resultadoJobs['is_deleted']) && $resultadoJobs['is_deleted'] == 1) {
            $omitidas++;
            continue; // Saltar facturas eliminadas
        }
        
        // Verificar que la fecha de inicio ya haya pasado
        if (!empty($resultadoJobs['date_start']) && $resultadoJobs['date_start'] > $fechaActual) {
            $omitidas++;
            continue; // Aún no es momento de generar
        }
        
        // Verificar que no haya fecha de finalización o que aún no haya pasado
        if (!empty($resultadoJobs['date_finish']) && $resultadoJobs['date_finish'] != "0000-00-00" && $resultadoJobs['date_finish'] != null && $resultadoJobs['date_finish'] < $fechaActual) {
            $omitidas++;
            continue; // Ya pasó la fecha de finalización
        }
        
        // Obtener los días configurados para facturación
        $diasEnMesRaw = !empty($resultadoJobs['days_in_month']) ? explode(",", $resultadoJobs['days_in_month']) : [];
        if (empty($diasEnMesRaw)) {
            $omitidas++;
            echo "[ID: {$resultadoJobs['id']}] Omitida: No hay días configurados.\n";
            continue; // No hay días configurados
        }
        
        // Convertir días a enteros para comparación correcta
        $diasEnMes = array_map('intval', $diasEnMesRaw);
        
        // Verificar si hoy es uno de los días configurados
        $indiceDiaActual = array_search($diaActual, $diasEnMes);
        $esDiaConfigurado = ($indiceDiaActual !== false);
        $debeGenerar = false;
        
        echo "[ID: {$resultadoJobs['id']}] Días configurados: " . implode(', ', $diasEnMes) . " | Día actual: {$diaActual} | next_generation_date: " . ($resultadoJobs['next_generation_date'] ?? 'NULL') . "\n";
        
        // Caso 1: Si hoy es uno de los días configurados y no hay próxima fecha programada (primera vez)
        if ($esDiaConfigurado && (empty($resultadoJobs['next_generation_date']) || $resultadoJobs['next_generation_date'] == "0000-00-00" || $resultadoJobs['next_generation_date'] == null)) {
            $debeGenerar = true;
            echo "  → Caso 1: Día configurado sin próxima fecha programada.\n";
        }
        // Caso 2: Si la próxima fecha de generación es hoy
        elseif (!empty($resultadoJobs['next_generation_date']) && $resultadoJobs['next_generation_date'] != "0000-00-00" && $resultadoJobs['next_generation_date'] == $fechaActual) {
            $debeGenerar = true;
            echo "  → Caso 2: Próxima fecha programada es hoy.\n";
        }
        // Caso 3: Si hoy es uno de los días configurados (incluso si hay próxima fecha, pero es hoy)
        elseif ($esDiaConfigurado) {
            $debeGenerar = true;
            echo "  → Caso 3: Día configurado encontrado.\n";
        }
        
        if ($debeGenerar) {
            // Iniciar transacción
            mysqli_autocommit($conexion, false);
            if (!mysqli_query($conexion, "START TRANSACTION")) {
                throw new Exception("Error al iniciar transacción: " . mysqli_error($conexion));
            }
            
            try {
                // Establecer variables de sesión necesarias para el método generarRecurrentes
                $_SESSION["id"] = $resultadoJobs['responsible_user'] ?? 'SYSTEM';
                $_SESSION["bd"] = $resultadoJobs['year'] ?? date('Y');
                $_SESSION["idInstitucion"] = $resultadoJobs['institucion'] ?? 1;
                
                echo "[ID: {$resultadoJobs['id']}] Generando factura para usuario: {$resultadoJobs['user']}...\n";
                
                // Generar la factura y obtener el fcu_id creado
                $fcuIdCreado = Movimientos::generarRecurrentes($conexion, $resultadoJobs);
                echo "  ✓ Factura generada exitosamente (ID: {$fcuIdCreado}).\n";
                
                
                // Calcular la próxima fecha de generación
                $totalDias = count($diasEnMes);
                $indiceSiguienteDia = ($indiceDiaActual !== false && $indiceDiaActual < ($totalDias - 1)) ? ($indiceDiaActual + 1) : 0;
                $diaSiguiente = (int)$diasEnMes[$indiceSiguienteDia];
                
                // Calcular el mes siguiente según la frecuencia
                $mesSiguiente = $mesActual;
                $yearSiguiente = $yearActual;
                
                // Si el siguiente día es menor que el actual, avanzar según la frecuencia
                if ($diaSiguiente < $diaActual || ($indiceDiaActual !== false && $indiceDiaActual == ($totalDias - 1))) {
                    $mesSiguiente = $mesActual + (int)$resultadoJobs['frequency'];
                    
                    // Ajustar año si es necesario
                    while ($mesSiguiente > 12) {
                        $mesSiguiente -= 12;
                        $yearSiguiente++;
                    }
                }
                
                // Asegurar que el día existe en el mes siguiente
                $diasEnMesSiguiente = cal_days_in_month(CAL_GREGORIAN, $mesSiguiente, $yearSiguiente);
                if ($diaSiguiente > $diasEnMesSiguiente) {
                    $diaSiguiente = $diasEnMesSiguiente; // Usar el último día del mes
                }
                
                $proximaFecha = sprintf("%04d-%02d-%02d", $yearSiguiente, $mesSiguiente, $diaSiguiente);
                
                // Obtener valores actuales de los campos de seguimiento desde $resultadoJobs (ya vienen del SELECT *)
                $idRecurrente = mysqli_real_escape_string($conexion, $resultadoJobs['id']);
                $institucion = (int)$resultadoJobs['institucion'];
                $year = mysqli_real_escape_string($conexion, $resultadoJobs['year']);
                
                // Incrementar cantidad de creaciones
                $cantidadCreaciones = (int)($resultadoJobs['cantidad_creaciones'] ?? 0) + 1;
                
                // Agregar el nuevo fcu_id a la lista de IDs creados (separados por comas)
                $idsFacturasActuales = !empty($resultadoJobs['ids_facturas_creadas']) && $resultadoJobs['ids_facturas_creadas'] !== null ? trim($resultadoJobs['ids_facturas_creadas']) : '';
                $idsFacturasNuevos = !empty($idsFacturasActuales) ? $idsFacturasActuales . ',' . $fcuIdCreado : (string)$fcuIdCreado;
                $idsFacturasEscapados = mysqli_real_escape_string($conexion, $idsFacturasNuevos);
                
                // Fecha/hora actual para fecha_ultima_generacion
                $fechaUltimaGeneracion = date('Y-m-d H:i:s');
                
                // Actualizar los campos de seguimiento y la próxima fecha de generación dentro de la misma transacción
                $updateQuery = "UPDATE `".BD_FINANCIERA."`.`recurring_invoices` 
                                SET `next_generation_date` = '{$proximaFecha}',
                                    `cantidad_creaciones` = {$cantidadCreaciones},
                                    `ids_facturas_creadas` = '{$idsFacturasEscapados}',
                                    `fecha_ultima_generacion` = '{$fechaUltimaGeneracion}'
                                WHERE `id` = '{$idRecurrente}' 
                                AND `institucion` = {$institucion} 
                                AND `year` = '{$year}'";
                
                $resultadoUpdate = mysqli_query($conexion, $updateQuery);
                
                if (!$resultadoUpdate) {
                    $errorMsg = mysqli_error($conexion);
                    $errorNum = mysqli_errno($conexion);
                    throw new Exception("Error al actualizar datos de seguimiento (Código: {$errorNum}): " . $errorMsg);
                }
                
                // Verificar cuántas filas se afectaron
                $filasAfectadas = mysqli_affected_rows($conexion);
                if ($filasAfectadas == 0) {
                    throw new Exception("El UPDATE no afectó ninguna fila. Verificar que el registro existe.");
                }
                
                // Si todo está bien, hacer commit de la transacción
                mysqli_commit($conexion);
                mysqli_autocommit($conexion, true);
                
                $facturasGeneradas++;
                echo "  → Próxima generación programada para: {$proximaFecha}\n";
                echo "  → Total de facturas creadas: {$cantidadCreaciones}\n";
                echo "  → Transacción completada exitosamente.\n";
                
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                mysqli_rollback($conexion);
                mysqli_autocommit($conexion, true);
                $errores++;
                echo "  ✗ Error al generar factura recurrente ID {$resultadoJobs['id']}: " . $e->getMessage() . "\n";
                echo "  → Transacción revertida.\n";
                error_log("Error al generar factura recurrente ID {$resultadoJobs['id']}: " . $e->getMessage());
            }
        } else {
            $omitidas++;
        }
    }
    
    // Mostrar resumen de resultados
    echo "\n";
    echo "========================================\n";
    echo "RESUMEN DE EJECUCIÓN\n";
    echo "========================================\n";
    echo "Facturas generadas: {$facturasGeneradas}\n";
    echo "Omitidas: {$omitidas}\n";
    echo "Errores: {$errores}\n";
    echo "Total procesadas: " . ($facturasGeneradas + $omitidas + $errores) . "\n";
    echo "========================================\n";
    
    // Log de resultados (opcional, para monitoreo)
    if ($facturasGeneradas > 0 || $errores > 0) {
        error_log("generar-facturas-jobs.php ejecutado: {$facturasGeneradas} facturas generadas, {$omitidas} omitidas, {$errores} errores");
    }
    
} catch (Exception $e) {
    echo "\nERROR CRÍTICO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    error_log("ERROR CRÍTICO en generar-facturas-jobs.php: " . $e->getMessage());
    exit(1);
}

exit(0);
