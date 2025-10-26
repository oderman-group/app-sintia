<?php
/**
 * GENERAR INFORME MANUAL - VERSIÓN ASÍNCRONA
 * Genera el informe sin recargar la página
 */

include("session.php");
header('Content-Type: application/json; charset=UTF-8');
require_once(ROOT_PATH."/main-app/class/Modulos.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_academico_cargas.php");
require_once ROOT_PATH."/main-app/class/Asignaturas.php";
require_once(ROOT_PATH."/main-app/class/Sysjobs.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademicaOptimizada.php");

try {
    // Log inicial para debugging
    error_log("=== INICIO GENERACIÓN DE INFORME ===");
    
    // Obtener datos de la petición
    $input = json_decode(file_get_contents('php://input'), true);
    
    error_log("Datos recibidos: " . json_encode($input));
    
    $carga = isset($input['carga']) ? $input['carga'] : '';
    $periodo = isset($input['periodo']) ? (int)$input['periodo'] : 0;
    $grado = isset($input['grado']) ? $input['grado'] : ''; // Puede ser string o int
    $grupo = isset($input['grupo']) ? $input['grupo'] : ''; // Puede ser string o int
    $tipoGrado = isset($input['tipoGrado']) ? $input['tipoGrado'] : ''; // Puede ser string (GRADO_GRUPAL, etc)
    
    error_log("Parámetros procesados - Carga: {$carga}, Periodo: {$periodo}, Grado: {$grado}, Grupo: {$grupo}, Tipo: {$tipoGrado}");
    
    // Validar datos requeridos
    $datosFaltantes = [];
    if (empty($carga)) $datosFaltantes[] = 'ID de carga';
    if (empty($periodo)) $datosFaltantes[] = 'Periodo';
    if (empty($grado)) $datosFaltantes[] = 'Grado';
    if (empty($grupo)) $datosFaltantes[] = 'Grupo';
    
    if (!empty($datosFaltantes)) {
        $mensajeError = "❌ Datos incompletos para generar el informe.\n\n";
        $mensajeError .= "Faltan los siguientes datos:\n";
        $mensajeError .= "• " . implode("\n• ", $datosFaltantes);
        $mensajeError .= "\n\nPor favor recarga la página e intenta nuevamente.";
        
        error_log("ERROR: Datos faltantes - " . implode(", ", $datosFaltantes));
        throw new Exception($mensajeError);
    }
    
    // Validar permisos
    error_log("Validando permisos...");
    error_log("Usuario en sesión: " . ($_SESSION['id'] ?? 'no definido'));
    error_log("Tipo de usuario: " . ($_SESSION['tipo'] ?? 'no definido'));
    
    $tienePermiso = Modulos::validarSubRol(['DT0237']);
    error_log("Resultado validación permiso DT0237: " . ($tienePermiso ? 'SI' : 'NO'));
    
    if (!$tienePermiso) {
        throw new Exception('❌ No tienes permisos para generar informes.\n\nEste usuario no tiene el permiso DT0237 asignado.\n\nContacta al administrador para que te otorgue este permiso.');
    }
    
    error_log("✓ Permisos validados correctamente");
    
    $conexionPDO = Conexion::newConnection('PDO');
    
    // ==========================================
    // VALIDACIÓN 1: Verificar si hay un job en proceso o completado
    // ==========================================
    // NOTA: Esta validación está comentada temporalmente porque la tabla sys_background_jobs no existe
    // Si se implementa el sistema de jobs en background, descomentar este código
    /*
    try {
        $sqlJobs = "SELECT * FROM " . BD_ADMIN . ".sys_background_jobs 
                    WHERE job_carga_id = ? AND job_periodo = ? 
                    AND institucion = ? AND year = ?
                    ORDER BY job_fecha_creacion DESC LIMIT 1";
        
        $stmtJobs = $conexionPDO->prepare($sqlJobs);
        $stmtJobs->execute([$carga, $periodo, $config['conf_id_institucion'], $_SESSION['bd']]);
        $job = $stmtJobs->fetch(PDO::FETCH_ASSOC);
        
        if ($job) {
            switch ($job['job_estado']) {
                case JOBS_ESTADO_PENDIENTE:
                    throw new Exception('Ya hay un informe pendiente de generación para esta carga. Por favor espera...');
                case JOBS_ESTADO_PROCESO:
                    throw new Exception('El informe se está generando en este momento. Por favor espera...');
                case JOBS_ESTADO_PROCESADO:
                    throw new Exception('El informe ya fue generado para este periodo. Si necesitas regenerarlo, contacta al administrador.');
                case JOBS_ESTADO_ERROR:
                    // Permitir reintentar si hubo error
                    error_log("Reintentando generación después de error: " . $job['job_mensaje']);
                    break;
            }
        }
    } catch (PDOException $e) {
        // Si la tabla no existe, continuar sin validar jobs
        error_log("Advertencia: No se pudo verificar jobs en background (tabla no existe): " . $e->getMessage());
    }
    */
    error_log("Validación de jobs en background omitida (tabla no existe)");
    
    // ==========================================
    // VALIDACIÓN 2: Verificar porcentajes de actividades (declaradas y registradas)
    // ==========================================
    $datosActividades = CargaAcademicaOptimizada::obtenerDatosAdicionalesCarga($config, $carga, $periodo);
    
    $actividadesDeclaradas = $datosActividades['actividades_totales'];
    $actividadesRegistradas = $datosActividades['actividades_registradas'];
    
    if ($actividadesDeclaradas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
        throw new Exception(
            "No se puede generar el informe. Las actividades declaradas no completan el 100%.\n" .
            "Actual: {$actividadesDeclaradas}%\n" .
            "Por favor declara todas las actividades antes de generar el informe."
        );
    }
    
    if ($actividadesRegistradas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
        throw new Exception(
            "No se puede generar el informe. Las actividades registradas no completan el 100%.\n" .
            "Actual: {$actividadesRegistradas}%\n" .
            "Por favor registra todas las actividades antes de generar el informe."
        );
    }
    
    // ==========================================
    // VALIDACIÓN 3: Verificar configuración de estudiantes con notas completas
    // ==========================================
    $numEstudiantesSinNotas = $datosActividades['estudiantes_sin_nota'];
    
    if ($config['conf_porcentaje_completo_generar_informe'] == Boletin::GENERAR_CON_PORCENTAJE_COMPLETO) {
        if ($numEstudiantesSinNotas > 0) {
            // Obtener lista detallada de estudiantes sin notas
            $consultaListaEstudantesError = Estudiantes::listarEstudiantesNotasFaltantes($carga, $periodo, $tipoGrado);
            
            if (mysqli_num_rows($consultaListaEstudantesError) > 0) {
                $estudiantesFaltantes = [];
                while ($est = mysqli_fetch_array($consultaListaEstudantesError, MYSQLI_BOTH)) {
                    $estudiantesFaltantes[] = UsuariosPadre::nombreCompletoDelUsuario($est);
                }
                
                throw new Exception(
                    "🔒 No se puede generar el informe según la configuración de tu institución.\n\n" .
                    "La configuración requiere que TODOS los estudiantes tengan el 100% de sus notas.\n\n" .
                    "Estudiantes con notas pendientes: " . count($estudiantesFaltantes) . "\n" .
                    "Ejemplos: " . implode(', ', array_slice($estudiantesFaltantes, 0, 3)) . 
                    (count($estudiantesFaltantes) > 3 ? ', ...' : '') . "\n\n" .
                    "Por favor completa todas las notas antes de generar el informe."
                );
            }
        }
    }
    
    // Configuración OK - informar al usuario qué sucederá
    $mensajeConfiguracion = '';
    if ($config['conf_porcentaje_completo_generar_informe'] == Boletin::OMITIR_ESTUDIANTES_CON_PORCENTAJE_INCOMPLETO) {
        if ($numEstudiantesSinNotas > 0) {
            $mensajeConfiguracion = "Se omitirán {$numEstudiantesSinNotas} estudiante(s) sin el 100% de notas.";
        }
    } elseif ($config['conf_porcentaje_completo_generar_informe'] == Boletin::GENERAR_CON_CUALQUIER_PORCENTAJE) {
        $mensajeConfiguracion = "Se generará el informe con el porcentaje actual de cada estudiante.";
    }
    
    $datosCarga = [
        'car_curso' => $grado,
        'car_grupo' => $grupo,
        'gra_tipo'  => $tipoGrado
    ];
    
    // Consultamos los estudiantes del grado y grupo
    $consulta = Estudiantes::listarEstudiantesConInfoBasica($datosCarga);
    
    // Validar configuración de porcentaje de peso sobre las áreas
    $obtenerNotaEquivalente = $config['conf_agregar_porcentaje_asignaturas'] == 'SI' && !Asignaturas::hayAsignaturaSinValor() ? true : false;
    
    $area = null;
    $valorAsignatura = null;
    $notaEquivalente = null;
    $contProcesados = 0;
    $contOmitidos = 0;
    
    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
        // DEFINITIVAS
        $estudiante = $resultado['mat_id'];
        include("../definitivas.php");
        
        // Consultamos si tiene registros en el boletín
        $boletinDatos = Boletin::traerNotaBoletinCargaPeriodo($config, $periodo, $estudiante, $carga);
        
        // Validar la configuración que omite a los estudiantes que NO tienen el 100%
        if (
            $config['conf_porcentaje_completo_generar_informe'] == Boletin::OMITIR_ESTUDIANTES_CON_PORCENTAJE_INCOMPLETO && 
            $porcentajeActual < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME && 
            empty($boletinDatos['bol_nota'])
        ) {
            $contOmitidos++;
            continue;
        }
        
        // Procesar notas por indicador si es ICOLVEN
        if ($informacion_inst["info_institucion"] == ICOLVEN) {
            $notasPorIndicador = Calificaciones::traerNotasPorIndicador($config, $carga, $resultado['mat_id'], $periodo);
            $sumaNotaIndicador = 0; 
            
            while ($notInd = mysqli_fetch_array($notasPorIndicador, MYSQLI_BOTH)) {
                $consultaNum = Indicadores::consultaRecuperacionIndicadorPeriodo($config, $notInd[1], $resultado['mat_id'], $carga, $periodo);
                $num = mysqli_num_rows($consultaNum);
                
                $sumaNotaIndicador += $notInd[0];
                
                if ($num == 0) {
                    Indicadores::eliminarRecuperacionIndicadorPeriodo($config, $notInd[1], $resultado['mat_id'], $carga, $periodo);				
                    Indicadores::guardarRecuperacionIndicador($conexionPDO, $config, $resultado['mat_id'], $carga, $notInd[0], $notInd[1], $periodo, $notInd[2]);
                } else {
                    Indicadores::actualizarRecuperacionIndicador($config, $resultado['mat_id'], $carga, $notInd[0], $notInd[1], $periodo, $notInd[2]);
                }
            } 
            $sumaNotaIndicador = round($sumaNotaIndicador, 1);
        } else {
            $sumaNotaIndicador = round($definitiva, 1);
        }
        
        $caso = 1; // Inserta la definitiva que viene normal 
        
        if (!empty($boletinDatos['bol_id'])) {
            // Si ya existe un registro previo
            if ($boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_NORMAL) {
                if($boletinDatos['bol_nota'] != $definitiva || $boletinDatos['bol_porcentaje'] != $porcentajeActual) {
                    $caso = 2; // Se cambia la definitiva
                } else {
                    $updateBoletin = ['bol_estado' => Boletin::ESTADO_GENERADO];
                    $where = "bol_id='{$boletinDatos['bol_id']}'";
                    Boletin::actualizarBoletin($config, $updateBoletin, $where);
                    $contProcesados++;
                    continue;
                }
            } elseif ($boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_RECUPERACION_PERIODO) {
                if ($definitiva < $config[5]) {
                    $updateBoletin = ['bol_estado' => Boletin::ESTADO_GENERADO];
                    $where = "bol_id='{$boletinDatos['bol_id']}'";
                    Boletin::actualizarBoletin($config, $updateBoletin, $where);
                    $contProcesados++;
                    continue;
                } else {
                    $caso = 4;
                }
            } elseif (($boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_RECUPERACION_INDICADOR || $boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_DIRECTIVA)) {
                $caso = 5;
            }
        }
        
        if ($caso == 2 || $caso == 4 || $caso == 5) {
            // Actualizar nota existente
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
                "nota_anterior" => $boletinDatos['bol_nota'],
                "fecha_de_actualizacion" => $fecha,
                "porcentaje" => $boletinDatos['bol_porcentaje'],
                "caso" => $caso
            ];
            
            $numActualizacion = $boletinDatos['bol_actualizaciones'] + 1;
            $actualizacion[$numActualizacion] = $nuevoArray;
            
            $update = [
                'bol_nota_anterior' => $boletinDatos['bol_nota'],
                'bol_nota' => $definitiva,
                'bol_nota_indicadores' => $sumaNotaIndicador,
                'bol_tipo' => 1,
                'bol_observaciones' => 'Reemplazada',
                'bol_porcentaje' => $porcentajeActual,
                'bol_historial_actualizacion' => json_encode($actualizacion),
                'bol_estado' => Boletin::ESTADO_GENERADO,
            ];
            
            Boletin::actualizarNotaBoletin($config, $boletinDatos['bol_id'], $update);
            
        } elseif ($caso == 1) {
            // Insertar nueva nota
            if (!empty($boletinDatos['bol_id'])) {
                Boletin::eliminarNotaBoletinID($config, $boletinDatos['bol_id']);
            }
            
            if($obtenerNotaEquivalente) {
                $cargaConsulta = CargaAcademica::traerCargasMateriasPorID($config, $carga);
                $cargaDatos = mysqli_fetch_array($cargaConsulta, MYSQLI_BOTH);
                $area = $cargaDatos['car_saberes_area'];
                $valorAsignatura = !empty($cargaDatos['mat_valor']) ? $cargaDatos['mat_valor'] : 100;
                $notaEquivalente = ($definitiva * $valorAsignatura) / 100;
            }
            
            Boletin::guardarNotaBoletin(
                $conexionPDO, 
                "bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo, bol_fecha_registro, bol_actualizaciones, bol_nota_indicadores, bol_porcentaje, institucion, year, bol_estado, bol_area, bol_valor_asignatura, bol_nota_equivalente, bol_id", 
                [
                    $carga, $estudiante, $periodo, $definitiva, 1, date("Y-m-d H:i:s"), 0, $sumaNotaIndicador, $porcentajeActual, 
                    $config['conf_id_institucion'], $_SESSION["bd"], Boletin::ESTADO_GENERADO, $area, $valorAsignatura, $notaEquivalente
                ]
            );
        }
        
        $contProcesados++;
    }
    
    // Actualizar periodo de la carga
    $periodoSiguiente = $periodo + 1;
    $carHistoricoArray = [];
    
    $predicado = [
        'car_id' => $carga,
        'institucion' => $config['conf_id_institucion'],
        'year' => $_SESSION["bd"],
    ];
    
    $campos = "car_periodo, car_estado, car_historico";
    $datosCargaActualConsulta = BDT_AcademicoCargas::select($predicado, $campos, BD_ACADEMICA);
    $datosCargaActual = $datosCargaActualConsulta->fetchAll();
    $carHistoricoCampo = $datosCargaActual[0]['car_historico'];
    
    if (!empty($carHistoricoCampo)) {
        $carHistoricoArray = json_decode($carHistoricoCampo, true);
        $keys = array_keys($carHistoricoArray);
        $lastKey = end($keys);
        $lastCarHistoricoArray = $carHistoricoArray[$lastKey];
        
        if (
            $datosCargaActual[0]['car_estado'] == BDT_AcademicoCargas::ESTADO_DIRECTIVO && 
            !empty($lastCarHistoricoArray['car_periodo_anterior']) && 
            $lastCarHistoricoArray['car_periodo_anterior'] != $datosCargaActual[0]['car_periodo']
        ) {
            $periodoSiguiente = $lastCarHistoricoArray['car_periodo_anterior'];
        }
    }
    
    $carHistoricoArray[$carga.':'.time()] = [
        'car_periodo_anterior' => $datosCargaActual[0]['car_periodo'],
        'car_estado_anterior' => $datosCargaActual[0]['car_estado'],
        'car_forma_generacion' => BDT_AcademicoCargas::GENERACION_MANUAL,
    ];
    
    $update = [
        'car_estado' => BDT_AcademicoCargas::ESTADO_SINTIA,
        'car_periodo' => $periodoSiguiente,
        'car_historico' => json_encode($carHistoricoArray),
    ];
    
    CargaAcademica::actualizarCargaPorID($config, $carga, $update);
    
    // Guardar historial
    include("../compartido/guardar-historial-acciones.php");
    
    echo json_encode([
        'success' => true,
        'message' => 'Informe generado exitosamente',
        'mensaje_configuracion' => $mensajeConfiguracion,
        'estudiantes_procesados' => $contProcesados,
        'estudiantes_omitidos' => $contOmitidos,
        'periodo_procesado' => $periodo,
        'periodo_siguiente' => $periodoSiguiente,
        'carga_id' => $carga,
        'actividades_declaradas' => $actividadesDeclaradas,
        'actividades_registradas' => $actividadesRegistradas
    ]);
    
} catch (Exception $e) {
    error_log("=== ERROR AL GENERAR INFORME ===");
    error_log("Mensaje: " . $e->getMessage());
    error_log("Archivo: " . $e->getFile());
    error_log("Línea: " . $e->getLine());
    error_log("Trace: " . $e->getTraceAsString());
    
    // Devolver error formateado para el usuario
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_details' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Error $e) {
    error_log("=== ERROR FATAL AL GENERAR INFORME ===");
    error_log("Mensaje: " . $e->getMessage());
    error_log("Archivo: " . $e->getFile());
    error_log("Línea: " . $e->getLine());
    error_log("Trace: " . $e->getTraceAsString());
    
    // Devolver error fatal formateado para el usuario
    echo json_encode([
        'success' => false,
        'message' => 'Error fatal en el sistema: ' . $e->getMessage(),
        'error_details' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'type' => 'Fatal Error'
        ]
    ], JSON_UNESCAPED_UNICODE);
}

exit();

