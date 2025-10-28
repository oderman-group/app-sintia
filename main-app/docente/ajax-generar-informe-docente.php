<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0033';

require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

// Configurar respuesta JSON
header('Content-Type: application/json; charset=utf-8');

try {
    error_log("🔵 =================================================");
    error_log("🔵 INICIO - Generación asíncrona de informe (Docente)");
    error_log("🔵 =================================================");
    
    // Validar parámetros
    $carga = !empty($_POST['carga']) ? base64_decode($_POST['carga']) : null;
    $periodo = !empty($_POST['periodo']) ? (int)base64_decode($_POST['periodo']) : null;
    $grado = !empty($_POST['grado']) ? base64_decode($_POST['grado']) : null;
    $grupo = !empty($_POST['grupo']) ? base64_decode($_POST['grupo']) : null;
    
    error_log("📊 Parámetros recibidos:");
    error_log("  - Carga: $carga");
    error_log("  - Periodo: $periodo");
    error_log("  - Grado: $grado");
    error_log("  - Grupo: $grupo");
    
    if (!$carga || !$periodo || !$grado || !$grupo) {
        throw new Exception("Parámetros incompletos");
    }
    
    // Obtener datos de la carga
    $datosCargaActual = CargaAcademica::traerCargaMateriaPorID($config, $carga);
    if (empty($datosCargaActual)) {
        throw new Exception("Carga académica no encontrada");
    }
    
    error_log("✅ Carga encontrada: {$datosCargaActual['car_materia']} - {$datosCargaActual['mat_nombre']}");
    
    // Obtener estudiantes
    $consultaEstudiantes = Estudiantes::listarEstudiantesConInfoBasica($datosCargaActual);
    
    // Convertir mysqli_result a array
    $estudiantes = [];
    while ($est = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH)) {
        $estudiantes[] = $est;
    }
    
    $numEstudiantes = count($estudiantes);
    error_log("👥 Estudiantes encontrados: $numEstudiantes");
    
    // Contadores para el resumen
    $contadores = [
        'insertados' => [],
        'actualizados' => [],
        'omitidos' => [],
        'errores' => []
    ];
    
    // Procesar cada estudiante
    foreach ($estudiantes as $datosEstudiante) {
        $idEstudiante = $datosEstudiante['mat_id'];
        
        try {
            // ==========================================
            // Re-asignar variables CRÍTICAS para definitivas.php
            // ==========================================
            $carga = $datosCargaActual['car_id']; // ID de la carga
            $periodo = $periodo; // Periodo actual
            $estudiante = $idEstudiante; // ID del estudiante (variable que definitivas.php espera)
            
            error_log("📝 Procesando estudiante: $idEstudiante");
            
            // Incluir definitivas.php para calcular la nota
            ob_start();
            include(ROOT_PATH."/main-app/definitivas.php");
            ob_end_clean();
            
            // Validar que $definitiva y $porcentajeActual estén definidas
            if (!isset($definitiva)) {
                $contadores['errores'][] = [
                    'id' => $idEstudiante,
                    'nombre' => Estudiantes::NombreCompletoDelEstudiante($datosEstudiante),
                    'error' => 'No se pudo calcular la definitiva'
                ];
                error_log("❌ Definitiva no calculada para estudiante: $idEstudiante");
                continue;
            }
            
            if (!isset($porcentajeActual)) {
                $contadores['errores'][] = [
                    'id' => $idEstudiante,
                    'nombre' => Estudiantes::NombreCompletoDelEstudiante($datosEstudiante),
                    'error' => 'No se pudo calcular el porcentaje de notas'
                ];
                error_log("❌ Porcentaje no calculado para estudiante: $idEstudiante");
                continue;
            }
            
            error_log("📊 Estudiante $idEstudiante - Definitiva: $definitiva, Porcentaje: {$porcentajeActual}%");
            
            // Verificar si tiene notas completas
            if ($porcentajeActual < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
                $nombreEstudiante = Estudiantes::NombreCompletoDelEstudiante($datosEstudiante);
                $contadores['omitidos'][] = [
                    'id' => $idEstudiante,
                    'nombre' => $nombreEstudiante,
                    'razon' => "Notas incompletas ({$porcentajeActual}%)"
                ];
                error_log("⏭️ Omitido: $nombreEstudiante - Porcentaje: {$porcentajeActual}%");
                continue;
            }
            
            // Verificar si ya existe el boletín (orden correcto: config, periodo, estudiante, carga)
            $boletinExistente = Boletin::traerNotaBoletinCargaPeriodo($config, $periodo, $idEstudiante, $carga);
            
            $nombreEstudiante = Estudiantes::NombreCompletoDelEstudiante($datosEstudiante);
            
            if (!empty($boletinExistente)) {
                // Actualizar
                Boletin::actualizarNotaBoletin($config, $periodo, $definitiva, $carga, $idEstudiante);
                $contadores['actualizados'][] = [
                    'id' => $idEstudiante,
                    'nombre' => $nombreEstudiante,
                    'nota' => round($definitiva, 2)
                ];
                error_log("🔄 Actualizado: $nombreEstudiante - Nota: $definitiva");
            } else {
                // Insertar
                Boletin::guardarNotaBoletin($conexionPDO, $periodo, $definitiva, $carga, $idEstudiante, $_SESSION["bd"]);
                $contadores['insertados'][] = [
                    'id' => $idEstudiante,
                    'nombre' => $nombreEstudiante,
                    'nota' => round($definitiva, 2)
                ];
                error_log("➕ Insertado: $nombreEstudiante - Nota: $definitiva");
            }
            
        } catch (Exception $e) {
            $nombreEstudiante = Estudiantes::NombreCompletoDelEstudiante($datosEstudiante);
            $contadores['errores'][] = [
                'id' => $idEstudiante,
                'nombre' => $nombreEstudiante,
                'error' => $e->getMessage()
            ];
            error_log("❌ Error al procesar $nombreEstudiante: " . $e->getMessage());
        }
    }
    
    // Preparar respuesta
    $totalProcesados = count($contadores['insertados']) + count($contadores['actualizados']);
    $totalOmitidos = count($contadores['omitidos']);
    $totalErrores = count($contadores['errores']);
    
    error_log("📊 Resumen de generación:");
    error_log("  ✅ Insertados: " . count($contadores['insertados']));
    error_log("  🔄 Actualizados: " . count($contadores['actualizados']));
    error_log("  ⏭️ Omitidos: $totalOmitidos");
    error_log("  ❌ Errores: $totalErrores");
    error_log("🔵 FIN - Generación completada");
    error_log("🔵 =================================================");
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Informe generado exitosamente',
        'data' => [
            'total_procesados' => $totalProcesados,
            'total_omitidos' => $totalOmitidos,
            'total_errores' => $totalErrores,
            'insertados' => $contadores['insertados'],
            'actualizados' => $contadores['actualizados'],
            'omitidos' => $contadores['omitidos'],
            'errores' => $contadores['errores']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("❌ ERROR FATAL en generación de informe: " . $e->getMessage());
    error_log("❌ Trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al generar el informe: ' . $e->getMessage(),
        'error' => $e->getMessage()
    ]);
}

exit();

