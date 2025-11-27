<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

// Función para mostrar mensaje de error
function mostrarError($mensaje, $detalles = '') {
    $detallesHtml = !empty($detalles) ? '<br><br><strong>Detalles:</strong> '.htmlspecialchars($detalles) : '';
    $mensajeCompleto = addslashes($mensaje) . $detallesHtml;
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Error - Promoción de Estudiantes</title>
        <script src="../../config-general/assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    </head>
    <body>
    <script type="text/javascript">
        setTimeout(function() {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    html: "'.$mensajeCompleto.'",
                    confirmButtonText: "Entendido"
                }).then(() => {
                    window.location.href = "cursos-promocionar-estudiantes-detalles.php";
                });
            } else {
                alert("'.addslashes($mensaje).'");
                window.location.href = "cursos-promocionar-estudiantes-detalles.php";
            }
        }, 100);
    </script>
    </body>
    </html>';
    echo $html;
    exit();
}

// Función para mostrar mensaje de éxito
function mostrarExito($mensaje, $urlRedireccion, $promocionId = null) {
    $botonRevertir = '';
    if(!empty($promocionId) && isset($_SESSION['historial_promociones'][$promocionId])){
        $promocionIdEscapado = addslashes($promocionId);
        $botonRevertir = ',
                    showDenyButton: true,
                    denyButtonText: "Revertir Promoción",
                    denyButtonColor: "#ffc107",
                    confirmButtonText: "Continuar"';
    } else {
        $botonRevertir = ',
                    confirmButtonText: "Continuar"';
    }
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Promoción Exitosa</title>
        <script src="../../config-general/assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    </head>
    <body>
    <script type="text/javascript">
        setTimeout(function() {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: "success",
                    title: "¡Promoción Exitosa!",
                    html: "'.addslashes($mensaje).'"'.($promocionId ? ' + "<br><br><small class=\"text-muted\">Puedes revertir esta promoción desde la página de cursos.</small>"' : '').',
                    allowOutsideClick: false'.$botonRevertir.'
                }).then((result) => {
                    if (result.isDenied && "'.addslashes($promocionId ?? '').'") {
                        // Revertir promoción (codificar ID en base64)
                        var promocionIdBase64 = btoa("'.addslashes($promocionId ?? '').'");
                        Swal.fire({
                            title: "¿Revertir Promoción?",
                            html: "Esta acción revertirá la promoción y devolverá a los estudiantes a su curso anterior.<br><br><strong>¿Estás seguro de continuar?</strong>",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Sí, Revertir",
                            cancelButtonText: "Cancelar",
                            confirmButtonColor: "#ffc107",
                            cancelButtonColor: "#6c757d"
                        }).then((confirmResult) => {
                            if (confirmResult.isConfirmed) {
                                window.location.href = "cursos-revertir-promocion.php?id=" + promocionIdBase64;
                            } else {
                                window.location.href = "'.addslashes($urlRedireccion).'";
                            }
                        });
                    } else {
                        window.location.href = "'.addslashes($urlRedireccion).'";
                    }
                });
            } else {
                alert("'.addslashes($mensaje).'");
                window.location.href = "'.addslashes($urlRedireccion).'";
            }
        }, 100);
    </script>
    </body>
    </html>';
    echo $html;
    exit();
}

// Validar parámetros requeridos
if(empty($_POST["desde"]) || empty($_POST["para"])){
    mostrarError("Faltan parámetros requeridos para realizar la promoción.");
}

// Validar que hay estudiantes seleccionados
if(empty($_POST["estudiantes"]) || !is_array($_POST["estudiantes"])){
    mostrarError("No se seleccionaron estudiantes para promocionar.");
}

// Filtrar IDs válidos (eliminar valores vacíos, pero permitir alfanuméricos)
$estudiantesIds = array_filter($_POST["estudiantes"], function($id) {
    return !empty($id) && trim($id) !== '' && trim($id) !== '0';
});

// Reindexar el array
$estudiantesIds = array_values($estudiantesIds);

if(empty($estudiantesIds)){
    mostrarError("No se encontraron IDs válidos de estudiantes para promocionar. Por favor, selecciona los estudiantes nuevamente.");
}

// Validar que la conexión existe
if(!isset($conexion) || !$conexion){
    mostrarError("No se pudo establecer conexión con la base de datos.");
}

// Iniciar transacción para poder revertir en caso de error
if(!mysqli_begin_transaction($conexion)){
    mostrarError("No se pudo iniciar la transacción de base de datos.", mysqli_error($conexion));
}

try {
    // Guardar historial de cambios para reversión
    $historialPromocion = [];
    $filtro = " AND car_curso='".mysqli_real_escape_string($conexion, $_POST["desde"])."'";
    $numEstudiantesPromocionados = 0;
    $estudiantesConError = [];
    $estudiantesExitosos = [];
    
    foreach ($estudiantesIds as $idEstudiante) {
        // Limpiar y validar el ID (permitir alfanuméricos)
        $idEstudiante = trim($idEstudiante);
        
        // Validar que el ID no esté vacío
        if(empty($idEstudiante)){
            $estudiantesConError[] = [
                'id' => $idEstudiante,
                'nombre' => 'ID vacío',
                'error' => 'El ID del estudiante está vacío'
            ];
            continue;
        }
        
        try {
            // Obtener datos actuales del estudiante para el historial
            $datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($idEstudiante);
            
            if(empty($datosEstudianteActual)){
                $estudiantesConError[] = [
                    'id' => $idEstudiante,
                    'nombre' => 'ID: ' . $idEstudiante,
                    'error' => 'Estudiante no encontrado en la base de datos'
                ];
                continue;
            }
            
            $nombreEstudiante = Estudiantes::NombreCompletoDelEstudiante($datosEstudianteActual);
            
            // Determinar grupo destino
            $grupo = null;
            if(!empty($_POST["grupoPara"]) && trim($_POST["grupoPara"]) !== '' && trim($_POST["grupoPara"]) !== '0') {
                $grupo = trim($_POST["grupoPara"]);
            } elseif(!empty($_POST["grupo".$idEstudiante]) && trim($_POST["grupo".$idEstudiante]) !== '' && trim($_POST["grupo".$idEstudiante]) !== '0') {
                $grupo = trim($_POST["grupo".$idEstudiante]);
            } else {
                $grupo = $datosEstudianteActual['mat_grupo'] ?? null;
            }
            
            // Validar que el grupo existe
            if(empty($grupo) || trim($grupo) === '' || trim($grupo) === '0'){
                $estudiantesConError[] = [
                    'id' => $idEstudiante,
                    'nombre' => $nombreEstudiante,
                    'error' => 'No se especificó un grupo destino válido'
                ];
                continue;
            }
            
            // Validar que el grupo existe en la base de datos (IDs alfanuméricos)
            $grupoEscapado = mysqli_real_escape_string($conexion, $grupo);
            $consultaGrupo = mysqli_query($conexion, "SELECT gru_id FROM ".BD_ACADEMICA.".academico_grupos WHERE gru_id='".$grupoEscapado."' AND institucion=".$config['conf_id_institucion']." AND year=".$_SESSION['bd']);
            if(!$consultaGrupo || mysqli_num_rows($consultaGrupo) == 0){
                $estudiantesConError[] = [
                    'id' => $idEstudiante,
                    'nombre' => $nombreEstudiante,
                    'error' => 'El grupo destino (ID: '.htmlspecialchars($grupo).') no existe en la base de datos'
                ];
                continue;
            }
        
        // Guardar datos anteriores para historial
        $historialPromocion[] = [
            'estudiante_id' => $idEstudiante,
            'grado_anterior' => $datosEstudianteActual['mat_grado'],
            'grupo_anterior' => $datosEstudianteActual['mat_grupo'],
            'estado_anterior' => $datosEstudianteActual['mat_estado_matricula'],
            'grado_nuevo' => intval($_POST["para"]),
            'grupo_nuevo' => $grupo,
            'estado_nuevo' => !empty($_POST["estado".$idEstudiante]) ? 1 : $datosEstudianteActual['mat_estado_matricula']
        ];
        
            // Validar que el curso destino existe (IDs alfanuméricos)
            $cursoDestinoId = trim($_POST["para"]);
            $consultaGradoDestino = Grados::obtenerDatosGrados($cursoDestinoId);
            $gradoDestinoData = mysqli_fetch_array($consultaGradoDestino, MYSQLI_BOTH);
            if(empty($gradoDestinoData)){
                $estudiantesConError[] = [
                    'id' => $idEstudiante,
                    'nombre' => $nombreEstudiante,
                    'error' => 'El curso destino (ID: '.htmlspecialchars($cursoDestinoId).') no existe'
                ];
                continue;
            }
            
            // Actualizar matrícula del estudiante
            // Si no se especifica un estado nuevo, mantener el estado anterior
            $estadoMatricula = !empty($_POST["estado".$idEstudiante]) ? 1 : $datosEstudianteActual['mat_estado_matricula'];
            
            $update = [
                'mat_grado'            => $cursoDestinoId, 
                'mat_grupo'            => $grupo, 
                'mat_promocionado'     => 1, 
                'mat_estado_matricula' => $estadoMatricula
            ];
            
            // Intentar actualizar y capturar errores específicos
            try {
                // Ejecutar la actualización
                Estudiantes::actualizarMatriculasPorId($config, $idEstudiante, $update);
                
                // Verificar si hubo error en la consulta
                $errorDB = mysqli_error($conexion);
                if(!empty($errorDB)){
                    // Analizar el tipo de error
                    $mensajeError = 'Error de base de datos';
                    if(stripos($errorDB, 'foreign key') !== false){
                        $mensajeError = 'Violación de clave foránea: El grupo o curso destino no existe o hay dependencias';
                    } elseif(stripos($errorDB, 'duplicate') !== false){
                        $mensajeError = 'Registro duplicado: Ya existe un estudiante con estos datos';
                    } elseif(stripos($errorDB, 'constraint') !== false){
                        $mensajeError = 'Violación de restricción: Los datos no cumplen con las reglas de la base de datos';
                    } elseif(stripos($errorDB, 'cannot be null') !== false){
                        $mensajeError = 'Campo requerido: Falta un campo obligatorio';
                    }
                    
                    $estudiantesConError[] = [
                        'id' => $idEstudiante,
                        'nombre' => $nombreEstudiante,
                        'error' => $mensajeError . ' (' . htmlspecialchars($errorDB) . ')'
                    ];
                    continue;
                }
                
                // Verificar si se afectaron filas
                $filasAfectadas = mysqli_affected_rows($conexion);
                if($filasAfectadas === 0){
                    // Verificar si el estudiante realmente existe
                    $consultaExiste = mysqli_query($conexion, "SELECT mat_id, mat_grado, mat_grupo FROM ".BD_ACADEMICA.".academico_matriculas WHERE mat_id='".mysqli_real_escape_string($conexion, $idEstudiante)."' AND institucion=".$config['conf_id_institucion']." AND year=".$_SESSION['bd']);
                    
                    if(!$consultaExiste){
                        $errorConsulta = mysqli_error($conexion);
                        $estudiantesConError[] = [
                            'id' => $idEstudiante,
                            'nombre' => $nombreEstudiante,
                            'error' => 'Error al verificar existencia: ' . htmlspecialchars($errorConsulta)
                        ];
                        continue;
                    }
                    
                    if(mysqli_num_rows($consultaExiste) == 0){
                        $estudiantesConError[] = [
                            'id' => $idEstudiante,
                            'nombre' => $nombreEstudiante,
                            'error' => 'El estudiante no existe en la base de datos para esta institución y año'
                        ];
                    } else {
                        // El estudiante existe, pero no se actualizó
                        $datosActuales = mysqli_fetch_array($consultaExiste, MYSQLI_BOTH);
                        $razon = 'No se realizaron cambios';
                        if($datosActuales['mat_grado'] == $cursoDestinoId && $datosActuales['mat_grupo'] == $grupo){
                            $razon = 'Los datos ya están actualizados (mismo curso y grupo)';
                        } else {
                            $razon = 'No se pudo actualizar: posible restricción de base de datos o datos inválidos';
                        }
                        
                        $estudiantesConError[] = [
                            'id' => $idEstudiante,
                            'nombre' => $nombreEstudiante,
                            'error' => $razon
                        ];
                    }
                    continue;
                }
                
            } catch(Exception $eUpdate) {
                $estudiantesConError[] = [
                    'id' => $idEstudiante,
                    'nombre' => $nombreEstudiante,
                    'error' => 'Excepción al actualizar: ' . htmlspecialchars($eUpdate->getMessage())
                ];
                error_log("Error al promocionar estudiante {$idEstudiante}: " . $eUpdate->getMessage());
                continue;
            } catch(Error $eUpdate) {
                $estudiantesConError[] = [
                    'id' => $idEstudiante,
                    'nombre' => $nombreEstudiante,
                    'error' => 'Error fatal al actualizar: ' . htmlspecialchars($eUpdate->getMessage())
                ];
                error_log("Error fatal al promocionar estudiante {$idEstudiante}: " . $eUpdate->getMessage());
                continue;
            }
        
            // Si se relacionan cargas, actualizar boletines y calificaciones
            if (!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1) {
                $filtroCarga = $filtro;
                if(!empty($_POST["grupoDesde"]) && trim($_POST["grupoDesde"]) !== '' && trim($_POST["grupoDesde"]) !== '0') {
                    $filtroCarga .= " AND car_grupo='".mysqli_real_escape_string($conexion, trim($_POST["grupoDesde"]))."'";
                }
                
                $consultaCargas = CargaAcademica::listarCargas($conexion, $config, "", $filtroCarga, "mat_id, car_grupo");
                
                if($consultaCargas){
                    while($datosCarga = mysqli_fetch_array($consultaCargas, MYSQLI_BOTH)){
                        $cargaDestino = !empty($_POST["carga".$datosCarga['car_id']]) ? trim($_POST["carga".$datosCarga['car_id']]) : null;
                        
                        if($cargaDestino){
                            try {
                                // Actualizar boletín
                                $updateBoletin = [
                                    'bol_carga' => $cargaDestino
                                ];
                                Boletin::actualizarBoletinCargaEstudiante($config, $datosCarga['car_id'], $idEstudiante, $updateBoletin);
                                
                                // Transferir nivelaciones
                                Calificaciones::transferirNivelacion($conexion, $config, $cargaDestino, $datosCarga['car_id'], $idEstudiante);
                            } catch(Exception $eCarga) {
                                // Si falla la actualización de cargas, registrar pero no detener
                                error_log("Error al actualizar carga para estudiante {$idEstudiante}: " . $eCarga->getMessage());
                            }
                        }
                    }
                }
            }
            
            $numEstudiantesPromocionados++;
            $estudiantesExitosos[] = [
                'id' => $idEstudiante,
                'nombre' => $nombreEstudiante
            ];
            
        } catch(Exception $eEstudiante) {
            // Capturar errores específicos de cada estudiante
            $nombreEst = isset($nombreEstudiante) ? $nombreEstudiante : 'ID: ' . $idEstudiante;
            $estudiantesConError[] = [
                'id' => $idEstudiante,
                'nombre' => $nombreEst,
                'error' => 'Error al procesar: ' . $eEstudiante->getMessage()
            ];
            error_log("Error al promocionar estudiante {$idEstudiante}: " . $eEstudiante->getMessage());
            continue;
        }
    }
    
    // Guardar historial en sesión para posible reversión
    if(!isset($_SESSION['historial_promociones'])){
        $_SESSION['historial_promociones'] = [];
    }
    
    $idPromocion = uniqid('prom_', true);
    $_SESSION['historial_promociones'][$idPromocion] = [
        'fecha' => date('Y-m-d H:i:s'),
        'usuario' => $_SESSION['uss_id'] ?? null,
        'desde' => trim($_POST["desde"]),
        'para' => trim($_POST["para"]),
        'estudiantes' => $historialPromocion,
        'relacion_cargas' => !empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1
    ];
    
    // Confirmar transacción
    mysqli_commit($conexion);
    
    // Obtener nombres de cursos para el mensaje
    $consultaGradoActual = Grados::obtenerDatosGrados(trim($_POST["desde"]));
    $gradoActual = mysqli_fetch_array($consultaGradoActual, MYSQLI_BOTH);
    
    $consultaGrado = Grados::obtenerDatosGrados(trim($_POST["para"]));
    $gradoSiguiente = mysqli_fetch_array($consultaGrado, MYSQLI_BOTH);
    
    // Determinar el tipo de mensaje según los resultados
    if($numEstudiantesPromocionados == 0){
        // Si no se promovió ningún estudiante, mostrar error
        $mensajeError = "<strong>No se pudo promocionar ningún estudiante</strong> de <strong>".htmlspecialchars($gradoActual['gra_nombre'])."</strong> a <strong>".htmlspecialchars($gradoSiguiente['gra_nombre'])."</strong>.";
        
        if(!empty($estudiantesConError)){
            $mensajeError .= "<br><br><strong>Detalles de los errores:</strong><br><br>";
            $mensajeError .= "<div style='text-align: left; max-height: 300px; overflow-y: auto; padding: 10px; background: #f8f9fa; border-radius: 5px; margin-top: 10px;'>";
            foreach($estudiantesConError as $index => $errorEstudiante){
                $mensajeError .= "<div style='margin-bottom: 8px; padding: 8px; background: white; border-left: 3px solid #dc3545; border-radius: 3px;'>";
                $mensajeError .= "<strong>".($index + 1).". ".htmlspecialchars($errorEstudiante['nombre'])."</strong><br>";
                $mensajeError .= "<small style='color: #dc3545;'>".htmlspecialchars($errorEstudiante['error'])."</small>";
                $mensajeError .= "</div>";
            }
            $mensajeError .= "</div>";
        }
        
        mostrarError($mensajeError);
    } else {
        // Si se promovió al menos uno, mostrar éxito con advertencias si las hay
        $mensajeExito = "Se promocionaron exitosamente <strong>{$numEstudiantesPromocionados}</strong> estudiante(s) de <strong>".htmlspecialchars($gradoActual['gra_nombre'])."</strong> a <strong>".htmlspecialchars($gradoSiguiente['gra_nombre'])."</strong>.";
        
        if(!empty($estudiantesConError)){
            $mensajeExito .= "<br><br><strong>Advertencias:</strong> ".count($estudiantesConError)." estudiante(s) no pudieron ser promocionados:<br><br>";
            $mensajeExito .= "<div style='text-align: left; max-height: 300px; overflow-y: auto; padding: 10px; background: #f8f9fa; border-radius: 5px; margin-top: 10px;'>";
            foreach($estudiantesConError as $index => $errorEstudiante){
                $mensajeExito .= "<div style='margin-bottom: 8px; padding: 8px; background: white; border-left: 3px solid #ffc107; border-radius: 3px;'>";
                $mensajeExito .= "<strong>".($index + 1).". ".htmlspecialchars($errorEstudiante['nombre'])."</strong><br>";
                $mensajeExito .= "<small style='color: #dc3545;'>".htmlspecialchars($errorEstudiante['error'])."</small>";
                $mensajeExito .= "</div>";
            }
            $mensajeExito .= "</div>";
        }
        
        // Redirigir con mensaje de éxito (incluir ID de promoción para reversión, codificado en base64)
        $urlRedireccion = "cursos.php?success=SC_DT_7&curso=".base64_encode($gradoActual['gra_nombre'])."&siguiente=".base64_encode($gradoSiguiente['gra_nombre'])."&numEstudiantesPromocionados=".base64_encode($numEstudiantesPromocionados)."&promocionId=".base64_encode($idPromocion);
        mostrarExito($mensajeExito, $urlRedireccion, $idPromocion);
    }
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if(isset($conexion)){
        @mysqli_rollback($conexion);
    }
    
    // Log del error
    $errorMsg = $e->getMessage();
    $errorTrace = $e->getTraceAsString();
    error_log("Error en promoción de estudiantes: " . $errorMsg . "\nTrace: " . $errorTrace);
    
    if(function_exists('Utilidades::logError')){
        try {
            Utilidades::logError($e);
        } catch(Exception $logError) {
            // Si falla el log, continuar
        }
    }
    
    // Mensaje de error
    mostrarError("Ocurrió un error al procesar la promoción. Los cambios no se aplicaron.", $errorMsg);
} catch (Error $e) {
    // Capturar errores fatales de PHP 7+
    if(isset($conexion)){
        @mysqli_rollback($conexion);
    }
    
    $errorMsg = $e->getMessage();
    error_log("Error fatal en promoción de estudiantes: " . $errorMsg);
    mostrarError("Ocurrió un error fatal al procesar la promoción.", $errorMsg);
}

exit();