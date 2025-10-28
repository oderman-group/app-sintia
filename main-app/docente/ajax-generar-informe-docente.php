<?php
// Iniciar captura de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0033';

require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_academico_cargas.php");
require_once(ROOT_PATH."/main-app/modelo/conexion.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");

// Configurar respuesta JSON
header('Content-Type: application/json; charset=utf-8');

// Asegurar que conexionPDO esté disponible
if (!isset($conexionPDO)) {
    $conexionPDO = Conexion::newConnection('PDO');
}

try {
    error_log("🔵 =================================================");
    error_log("🔵 INICIO - Generación asíncrona de informe (Docente)");
    error_log("🔵 =================================================");
    
    // Validar parámetros
    $idCarga = !empty($_POST['carga']) ? base64_decode($_POST['carga']) : null;
    $periodo = !empty($_POST['periodo']) ? (int)base64_decode($_POST['periodo']) : null;
    $grado = !empty($_POST['grado']) ? base64_decode($_POST['grado']) : null;
    $grupo = !empty($_POST['grupo']) ? base64_decode($_POST['grupo']) : null;
    $tipoGrado = !empty($_POST['tipoGrado']) ? base64_decode($_POST['tipoGrado']) : null;
    $area = !empty($_POST['area']) ? base64_decode($_POST['area']) : null;
    $valorAsignatura = !empty($_POST['valorAsignatura']) ? base64_decode($_POST['valorAsignatura']) : null;
    
    error_log("📊 Parámetros recibidos (POST):");
    error_log("  - POST completo: " . print_r($_POST, true));
    error_log("  - Carga (raw): " . (!empty($_POST['carga']) ? $_POST['carga'] : 'VACÍO'));
    error_log("  - Periodo (raw): " . (!empty($_POST['periodo']) ? $_POST['periodo'] : 'VACÍO'));
    error_log("  - Grado (raw): " . (!empty($_POST['grado']) ? $_POST['grado'] : 'VACÍO'));
    error_log("  - Grupo (raw): " . (!empty($_POST['grupo']) ? $_POST['grupo'] : 'VACÍO'));
    error_log("  - TipoGrado (raw): " . (!empty($_POST['tipoGrado']) ? $_POST['tipoGrado'] : 'VACÍO'));
    
    error_log("📊 Parámetros decodificados:");
    error_log("  - Carga: " . ($idCarga ?: 'NULL'));
    error_log("  - Periodo: " . ($periodo ?: 'NULL'));
    error_log("  - Grado: " . ($grado ?: 'NULL'));
    error_log("  - Grupo: " . ($grupo ?: 'NULL'));
    error_log("  - TipoGrado: " . ($tipoGrado ?: 'NULL'));
    
    // Validar parámetros con mensaje detallado
    $parametrosFaltantes = [];
    if (!$idCarga) $parametrosFaltantes[] = 'carga';
    if (!$periodo) $parametrosFaltantes[] = 'periodo';
    if (!$grado) $parametrosFaltantes[] = 'grado';
    if (!$grupo) $parametrosFaltantes[] = 'grupo';
    if (!$tipoGrado) $parametrosFaltantes[] = 'tipoGrado';
    
    if (!empty($parametrosFaltantes)) {
        throw new Exception("Parámetros incompletos. Faltan: " . implode(', ', $parametrosFaltantes) . 
                           ". Recibidos: Carga=" . ($idCarga ? 'OK' : 'FALTA') . 
                           ", Periodo=" . ($periodo ? 'OK' : 'FALTA') . 
                           ", Grado=" . ($grado ? 'OK' : 'FALTA') . 
                           ", Grupo=" . ($grupo ? 'OK' : 'FALTA') . 
                           ", TipoGrado=" . ($tipoGrado ? 'OK' : 'FALTA'));
    }
    
    // ============================================
    // VALIDACIÓN 1: Estudiantes con notas faltantes
    // ============================================
    if ($config['conf_porcentaje_completo_generar_informe'] == Boletin::GENERAR_CON_PORCENTAJE_COMPLETO) {
        $consultaListaEstudantesError = Estudiantes::listarEstudiantesNotasFaltantes($idCarga, $periodo, $tipoGrado);
        
        if (mysqli_num_rows($consultaListaEstudantesError) > 0) {
            throw new Exception("Hay estudiantes con notas faltantes. No se puede generar el informe.");
        }
    }
    
    // Obtener datos de la carga
    $datosCargaActual = CargaAcademica::traerCargaMateriaPorID($config, $idCarga);
    if (empty($datosCargaActual)) {
        throw new Exception("Carga académica no encontrada con ID: $idCarga");
    }
    
    error_log("✅ Carga encontrada: {$datosCargaActual['car_id']} - {$datosCargaActual['mat_nombre']}");
    
    // Preparar datos para consultar estudiantes
    $datosCarga = [
        'car_curso' => $grado,
        'car_grupo' => $grupo,
        'gra_tipo'  => $tipoGrado
    ];
    
    // Obtener estudiantes
    $consultaEstudiantes = Estudiantes::listarEstudiantesConInfoBasica($datosCarga);
    
    // Validar configuración de porcentaje de peso sobre las áreas
    $obtenerNotaEquivalente = $config['conf_agregar_porcentaje_asignaturas'] == 'SI' && !Asignaturas::hayAsignaturaSinValor() ? true : false;
    
    // Contadores para el resumen
    $contadores = [
        'insertados' => [],
        'actualizados' => [],
        'omitidos' => [],
        'errores' => [],
        'sin_cambios' => []
    ];
    
    // Procesar cada estudiante
    $contBol = 1;
    while ($resultado = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH)) {
        $idEstudiante = $resultado['mat_id'];
        
        try {
            // ==========================================
            // Preparar variables para definitivas.php
            // ==========================================
            $carga = $idCarga; // ID de la carga (variable que definitivas.php espera)
            $periodo = $periodo; // Periodo actual
            $estudiante = $idEstudiante; // ID del estudiante (variable que definitivas.php espera)
            
            // Limpiar variables de cálculos previos
            unset($definitiva, $porcentajeActual);
            
            error_log("📝 Procesando estudiante: $idEstudiante");
            
            // Incluir definitivas.php para calcular la nota
            ob_start();
            try {
                include(ROOT_PATH."/main-app/definitivas.php");
                $output = ob_get_clean();
                
                // Si hay salida, puede ser un error
                if (!empty($output)) {
                    error_log("⚠️ Salida de definitivas.php: $output");
                }
            } catch (Exception $e) {
                ob_end_clean();
                throw new Exception("Error al incluir definitivas.php: " . $e->getMessage());
            }
            
            // Validar que $definitiva y $porcentajeActual estén definidas
            if (!isset($definitiva)) {
                $contadores['errores'][] = [
                    'id' => $idEstudiante,
                    'nombre' => Estudiantes::NombreCompletoDelEstudiante($resultado),
                    'error' => 'No se pudo calcular la definitiva'
                ];
                error_log("❌ Definitiva no calculada para estudiante: $idEstudiante");
                continue;
            }
            
            if (!isset($porcentajeActual)) {
                $contadores['errores'][] = [
                    'id' => $idEstudiante,
                    'nombre' => Estudiantes::NombreCompletoDelEstudiante($resultado),
                    'error' => 'No se pudo calcular el porcentaje de notas'
                ];
                error_log("❌ Porcentaje no calculado para estudiante: $idEstudiante");
                continue;
            }
            
            // Limpiar formato de definitiva (quitar .0 si es entero)
            $definitivaNumerica = is_numeric($definitiva) ? floatval($definitiva) : 0;
            
            error_log("📊 Estudiante $idEstudiante - Definitiva: $definitivaNumerica, Porcentaje: {$porcentajeActual}%");
            
            // ============================================
            // VALIDACIÓN 2: Omitir estudiantes con porcentaje incompleto
            // ============================================
            //Consultamos si tiene registros en el boletín
            $boletinDatos = Boletin::traerNotaBoletinCargaPeriodo($config, $periodo, $idEstudiante, $carga);
            
            //Validar la configuración que omite a los estudiantes que NO tienen el 100% de calificaciones
            //Verificamos si el porcentaje actual es menor al mínimo permitido y que el estudiante NO tenga nota registrada previamente
            if (
                $config['conf_porcentaje_completo_generar_informe'] == Boletin::OMITIR_ESTUDIANTES_CON_PORCENTAJE_INCOMPLETO && 
                $porcentajeActual < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME && 
                empty($boletinDatos['bol_nota'])
            ) {
                $contadores['omitidos'][] = [
                    'id' => $idEstudiante,
                    'nombre' => Estudiantes::NombreCompletoDelEstudiante($resultado),
                    'razon' => "Notas incompletas ({$porcentajeActual}%)",
                    'porcentaje' => $porcentajeActual
                ];
                error_log("⏭️ Omitido: " . Estudiantes::NombreCompletoDelEstudiante($resultado) . " - Porcentaje: {$porcentajeActual}%");
                continue;
            }
            
            // Si la configuración requiere 100% y el estudiante no lo tiene, NO procesar
            if (
                $config['conf_porcentaje_completo_generar_informe'] == Boletin::GENERAR_CON_PORCENTAJE_COMPLETO && 
                $porcentajeActual < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME
            ) {
                // Este caso ya fue validado al inicio, pero por seguridad lo validamos aquí también
                $contadores['omitidos'][] = [
                    'id' => $idEstudiante,
                    'nombre' => Estudiantes::NombreCompletoDelEstudiante($resultado),
                    'razon' => "Notas incompletas ({$porcentajeActual}%) - Configuración requiere 100%",
                    'porcentaje' => $porcentajeActual
                ];
                error_log("⏭️ Omitido (requiere 100%): " . Estudiantes::NombreCompletoDelEstudiante($resultado) . " - Porcentaje: {$porcentajeActual}%");
                continue;
            }
            
            // ============================================
            // LÓGICA ESPECIAL PARA ICOLVEN (Notas por indicador)
            // ============================================
            if ($informacion_inst["info_institucion"] == ICOLVEN) {
                //Vamos a obtener las definitivas por cada indicador y la definitiva general de la asignatura
                $notasPorIndicador = Calificaciones::traerNotasPorIndicador($config, $carga, $idEstudiante, $periodo);
                $sumaNotaIndicador = 0; 
                
                while ($notInd = mysqli_fetch_array($notasPorIndicador, MYSQLI_BOTH)) {
                    $consultaNum = Indicadores::consultaRecuperacionIndicadorPeriodo($config, $notInd[1], $idEstudiante, $carga, $periodo);
                    $num = mysqli_num_rows($consultaNum);
                    
                    $sumaNotaIndicador += $notInd[0];
                    
                    if ($num == 0) {
                        Indicadores::eliminarRecuperacionIndicadorPeriodo($config, $notInd[1], $idEstudiante, $carga, $periodo);				
                        Indicadores::guardarRecuperacionIndicador($conexionPDO, $config, $idEstudiante, $carga, $notInd[0], $notInd[1], $periodo, $notInd[2]);
                    } else {
                        Indicadores::actualizarRecuperacionIndicador($config, $idEstudiante, $carga, $notInd[0], $notInd[1], $periodo, $notInd[2]);
                    }
                } 
                
                $sumaNotaIndicador = round($sumaNotaIndicador, 1);
            } else {
                $sumaNotaIndicador = round($definitivaNumerica, 1);
            }
            
            // ============================================
            // DETERMINAR CASO DE ACTUALIZACIÓN
            // ============================================
            $caso = 1; //Inserta la definitiva que viene normal 
            
            if (!empty($boletinDatos['bol_id'])) {
                //Si ya existe un registro previo de definitiva TIPO 1
                if ($boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_NORMAL) {
                    
                    if($boletinDatos['bol_nota'] != $definitivaNumerica || $boletinDatos['bol_porcentaje'] != $porcentajeActual) {
                        $caso = 2; //Se cambia la definitiva que tenía por la que viene. Sea menor o mayor.
                    } else {
                        $updateBoletin = [
                            'bol_estado' => Boletin::ESTADO_GENERADO,
                        ];
                        
                        $where = "bol_id='{$boletinDatos['bol_id']}'";
                        
                        Boletin::actualizarBoletin($config, $updateBoletin, $where);
                        
                        //No se hacen cambios. Todo sigue igual
                        $contadores['sin_cambios'][] = [
                            'id' => $idEstudiante,
                            'nombre' => Estudiantes::NombreCompletoDelEstudiante($resultado),
                            'nota' => $definitivaNumerica
                        ];
                        error_log("✓ Sin cambios: " . Estudiantes::NombreCompletoDelEstudiante($resultado) . " - Nota: $definitivaNumerica");
                        continue;
                    }
                    
                } elseif ($boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_RECUPERACION_PERIODO) {
                    //Si ya existe un registro previo de recuperación de periodo TIPO 2
                    
                    //Si la definitiva que viene está perdida 
                    if ($definitivaNumerica < $config['conf_nota_minima_aprobar']) {
                        $updateBoletin = [
                            'bol_estado' => Boletin::ESTADO_GENERADO,
                        ];
                        
                        $where = "bol_id='{$boletinDatos['bol_id']}'";
                        
                        Boletin::actualizarBoletin($config, $updateBoletin, $where);
                        
                        //No se hacen cambios. Todo sigue igual
                        $contadores['sin_cambios'][] = [
                            'id' => $idEstudiante,
                            'nombre' => Estudiantes::NombreCompletoDelEstudiante($resultado),
                            'nota' => $definitivaNumerica
                        ];
                        error_log("✓ Sin cambios (recuperación): " . Estudiantes::NombreCompletoDelEstudiante($resultado));
                        continue;
                    } else {
                        $caso = 4; //Se reemplaza la nota de recuperación actual por la definitiva que viene. Igual está ganada y no requiere de recuperación.
                    }
                    
                } elseif (($boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_RECUPERACION_INDICADOR || $boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_DIRECTIVA)) {
                    //Si ya existe un registro previo de recuperación por Indicadores TIPO 3
                    $caso = 5; //Se actualiza la definitiva que viene y se cambia la recuperación del Indicador a nota anterior. 
                }
            }
            
            // ============================================
            // EJECUTAR ACTUALIZACIÓN/INSERCIÓN SEGÚN CASO
            // ============================================
            if ($caso == 2 || $caso == 4 || $caso == 5) {
                // CASOS 2, 4, 5: Actualizar registro existente
                
                if (!empty($boletinDatos['bol_historial_actualizacion']) && $boletinDatos['bol_historial_actualizacion'] != NULL) {
                    $actualizacion = json_decode($boletinDatos['bol_historial_actualizacion'], true);
                } else {
                    $actualizacion = [];
                }
                
                $fecha = $boletinDatos['bol_fecha_registro'];
                
                if (!empty($boletinDatos['bol_ultima_actualizacion']) && $boletinDatos['bol_ultima_actualizacion'] != NULL) {
                    $fecha = $boletinDatos['bol_ultima_actualizacion'];
                }
                
                $nuevoArray = [
                    "nota_anterior"          => $boletinDatos['bol_nota'],
                    "fecha_de_actualizacion" => $fecha,
                    "porcentaje"             => $boletinDatos['bol_porcentaje'],
                    "caso"                   => $caso
                ];
                
                $numActualizacion = $boletinDatos['bol_actualizaciones'] + 1;
                $actualizacion[$numActualizacion] = $nuevoArray;
                
                $update = [
                    'bol_nota_anterior'           => $boletinDatos['bol_nota'],
                    'bol_nota'                    => $definitivaNumerica,
                    'bol_nota_indicadores'        => $sumaNotaIndicador,
                    'bol_tipo'                    => 1,
                    'bol_observaciones'           => 'Reemplazada',
                    'bol_porcentaje'              => $porcentajeActual,
                    'bol_historial_actualizacion' => json_encode($actualizacion),
                    'bol_estado'                  => Boletin::ESTADO_GENERADO,
                ];
                
                Boletin::actualizarNotaBoletin($config, $boletinDatos['bol_id'], $update);
                
                $contadores['actualizados'][] = [
                    'id' => $idEstudiante,
                    'nombre' => Estudiantes::NombreCompletoDelEstudiante($resultado),
                    'nota' => round($definitivaNumerica, 2),
                    'nota_anterior' => round($boletinDatos['bol_nota'], 2),
                    'caso' => $caso,
                    'porcentaje' => $porcentajeActual
                ];
                error_log("🔄 Actualizado (caso $caso): " . Estudiantes::NombreCompletoDelEstudiante($resultado) . " - Nota: $definitivaNumerica");
                
            } elseif ($caso == 1) {
                // CASO 1: Insertar nuevo registro
                
                //Eliminamos por si acaso hay algún registro
                if (!empty($boletinDatos['bol_id'])) {
                    Boletin::eliminarNotaBoletinID($config, $boletinDatos['bol_id']);
                }
                
                if($obtenerNotaEquivalente) {
                    $notaEquivalente = isset($valorAsignatura) && is_numeric($valorAsignatura) && $valorAsignatura > 0 ? 
                                       $definitivaNumerica * ($valorAsignatura / 100) : 0;
                } else {
                    $notaEquivalente = null;
                }
                
                //INSERTAR LOS DATOS EN LA TABLA BOLETIN
                Boletin::guardarNotaBoletin(
                    $conexionPDO, 
                    "
                        bol_carga, 
                        bol_estudiante, 
                        bol_periodo, 
                        bol_nota, 
                        bol_tipo, 
                        bol_fecha_registro, 
                        bol_actualizaciones, 
                        bol_nota_indicadores, 
                        bol_porcentaje, 
                        institucion, 
                        year, 
                        bol_estado, 
                        bol_area,
                        bol_valor_asignatura,
                        bol_nota_equivalente,
                        bol_id
                    ", 
                    [
                        $carga, 
                        $idEstudiante, 
                        $periodo, 
                        $definitivaNumerica, 
                        1, 
                        date("Y-m-d H:i:s"), 
                        0, 
                        $sumaNotaIndicador, 
                        $porcentajeActual, 
                        $config['conf_id_institucion'], 
                        $_SESSION["bd"], 
                        Boletin::ESTADO_GENERADO,
                        $area,
                        $valorAsignatura,
                        $notaEquivalente
                    ]
                );
                
                $contadores['insertados'][] = [
                    'id' => $idEstudiante,
                    'nombre' => Estudiantes::NombreCompletoDelEstudiante($resultado),
                    'nota' => round($definitivaNumerica, 2),
                    'porcentaje' => $porcentajeActual
                ];
                error_log("➕ Insertado: " . Estudiantes::NombreCompletoDelEstudiante($resultado) . " - Nota: $definitivaNumerica");
                
                $contBol++;
            }
            
        } catch (Exception $e) {
            $contadores['errores'][] = [
                'id' => $idEstudiante,
                'nombre' => Estudiantes::NombreCompletoDelEstudiante($resultado),
                'error' => $e->getMessage()
            ];
            error_log("❌ Error al procesar " . Estudiantes::NombreCompletoDelEstudiante($resultado) . ": " . $e->getMessage());
            error_log("❌ Stack trace: " . $e->getTraceAsString());
        }
    }
    
    // ============================================
    // ACTUALIZAR ESTADO DE LA CARGA ACADÉMICA
    // ============================================
    $periodoSiguiente = $periodo + 1;
    $carHistoricoArray = [];
    
    try {
        $predicado = [
            'car_id'      => $idCarga,
            'institucion' => $config['conf_id_institucion'],
            'year'        => $_SESSION["bd"],
        ];
        
        $campos = "car_periodo, car_estado, car_historico";
        
        $datosCargaActualConsulta = BDT_AcademicoCargas::select($predicado, $campos, BD_ACADEMICA);
        $datosCargaActualArray = $datosCargaActualConsulta->fetchAll();
        $carHistoricoCampo = $datosCargaActualArray[0]['car_historico'];
        
        if (!empty($carHistoricoCampo)) {
            $carHistoricoArray = json_decode($carHistoricoCampo, true);
            $keys = array_keys($carHistoricoArray);
            $lastKey = end($keys);
            $lastCarHistoricoArray = $carHistoricoArray[$lastKey];
            
            if (
                $datosCargaActualArray[0]['car_estado'] == BDT_AcademicoCargas::ESTADO_DIRECTIVO && 
                !empty($lastCarHistoricoArray['car_periodo_anterior']) && 
                $lastCarHistoricoArray['car_periodo_anterior'] != $datosCargaActualArray[0]['car_periodo']
            ) {
                $periodoSiguiente = $lastCarHistoricoArray['car_periodo_anterior'];
            }
        }
        
        $carHistoricoArray[$idCarga.':'.time()] = [
            'car_periodo_anterior' => $datosCargaActualArray[0]['car_periodo'],
            'car_estado_anterior'  => $datosCargaActualArray[0]['car_estado'],
            'car_forma_generacion' => BDT_AcademicoCargas::GENERACION_MANUAL,
        ];
    } catch (PDOException $e) {
        error_log("⚠️ Error al actualizar historial de carga: " . $e->getMessage());
        // No lanzar excepción, es opcional
    }
    
    $update = [
        'car_estado'    => BDT_AcademicoCargas::ESTADO_SINTIA,
        'car_periodo'   => $periodoSiguiente,
        'car_historico' => json_encode($carHistoricoArray),
    ];
    
    CargaAcademica::actualizarCargaPorID($config, $idCarga, $update);
    
    // Preparar respuesta
    $totalProcesados = count($contadores['insertados']) + count($contadores['actualizados']);
    $totalOmitidos = count($contadores['omitidos']);
    $totalErrores = count($contadores['errores']);
    $totalSinCambios = count($contadores['sin_cambios']);
    
    error_log("📊 Resumen de generación:");
    error_log("  ✅ Insertados: " . count($contadores['insertados']));
    error_log("  🔄 Actualizados: " . count($contadores['actualizados']));
    error_log("  ⏭️ Omitidos: $totalOmitidos");
    error_log("  ❌ Errores: $totalErrores");
    error_log("  ✓ Sin cambios: $totalSinCambios");
    error_log("🔵 FIN - Generación completada");
    error_log("🔵 =================================================");
    
    // Respuesta exitosa - incluir información de la carga actualizada
    echo json_encode([
        'success' => true,
        'message' => 'Informe generado exitosamente',
        'data' => [
            'total_procesados' => $totalProcesados,
            'total_omitidos' => $totalOmitidos,
            'total_errores' => $totalErrores,
            'total_sin_cambios' => $totalSinCambios,
            'insertados' => $contadores['insertados'],
            'actualizados' => $contadores['actualizados'],
            'omitidos' => $contadores['omitidos'],
            'errores' => $contadores['errores'],
            'sin_cambios' => $contadores['sin_cambios'],
            'carga_actualizada' => true,
            'nuevo_periodo' => $periodoSiguiente,
            'carga_id' => $idCarga
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Throwable $e) {
    // Capturar cualquier error, incluyendo errores fatales
    error_log("❌ ERROR FATAL en generación de informe: " . $e->getMessage());
    error_log("❌ Tipo: " . get_class($e));
    error_log("❌ Archivo: " . $e->getFile() . " - Línea: " . $e->getLine());
    error_log("❌ Trace: " . $e->getTraceAsString());
    
    // Asegurar que siempre se retorne JSON válido
    http_response_code(500);
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al generar el informe: ' . $e->getMessage(),
        'error' => $e->getMessage(),
        'type' => get_class($e),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    
    exit();
}

exit();
