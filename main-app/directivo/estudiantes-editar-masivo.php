<?php
header('Content-Type: application/json');

include("session.php");
require_once("../class/Estudiantes.php");
require_once("../class/UsuariosPadre.php");
require_once("../class/Boletin.php");
require_once("../class/Utilidades.php");
require_once ROOT_PATH.'/main-app/class/App/Administrativo/Usuario/Estudiante.php';

// Validar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido. Solo se acepta POST.'
    ]);
    exit;
}

try {
    // Log de inicio
    error_log("=== INICIO EDICIÓN MASIVA ESTUDIANTES ===");
    error_log("POST recibido: " . print_r($_POST, true));
    error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
    error_log("Content-Type: " . (isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'NO DEFINIDO'));
    error_log("Raw input: " . file_get_contents('php://input'));
    
    // Validar que existan los datos necesarios
    if (!isset($_POST['estudiantes']) || !isset($_POST['campos'])) {
        error_log("ERROR: Datos incompletos - estudiantes: " . (isset($_POST['estudiantes']) ? 'SI' : 'NO') . ", campos: " . (isset($_POST['campos']) ? 'SI' : 'NO'));
        throw new Exception('Datos incompletos. Se requieren estudiantes y campos.');
    }

    $estudiantes = $_POST['estudiantes'];
    $campos = $_POST['campos'];
    
    error_log("Estudiantes recibidos: " . print_r($estudiantes, true));
    error_log("Campos recibidos: " . print_r($campos, true));
    error_log("Estudiantes después de asignar: " . print_r($estudiantes, true));
    error_log("Tipo de estudiantes después de asignar: " . gettype($estudiantes));

    // Validar que haya estudiantes para actualizar
    if (!is_array($estudiantes) || count($estudiantes) === 0) {
        throw new Exception('No se seleccionaron estudiantes para actualizar.');
    }

    // Validar que haya campos para actualizar
    if (!is_array($campos) || count($campos) === 0) {
        throw new Exception('No se especificaron campos para actualizar.');
    }

    // Sanitizar los IDs de los estudiantes (son strings alfanuméricos)
    $estudiantes = array_map(function($id) use ($conexion) {
        return mysqli_real_escape_string($conexion, trim($id));
    }, $estudiantes);
    
    // Remover valores vacíos
    $estudiantes = array_filter($estudiantes, function($id) {
        return !empty($id);
    });
    
    error_log("Estudiantes después de sanitizar: " . print_r($estudiantes, true));

    // Mapeo de nombres de campos del formulario a nombres de columnas en la BD
    $camposMapeados = [
        'estadoMatricula' => 'mat_estado_matricula',
        'estrato' => 'mat_estrato',
        'grado' => 'mat_grado',
        'grupo' => 'mat_grupo'
    ];

    // Preparar array de actualización con nombres de columnas correctos
    $datosActualizar = [];
    foreach ($campos as $nombreCampo => $valor) {
        // Validar que el valor no esté vacío
        if (empty($valor) && $valor !== '0' && $valor !== 0) {
            error_log("Campo '$nombreCampo' está vacío, se omite");
            continue;
        }
        
        if (isset($camposMapeados[$nombreCampo])) {
            $columna = $camposMapeados[$nombreCampo];
            
            error_log("Procesando campo: $nombreCampo (columna: $columna), valor: $valor");
            
            // Sanitizar el valor según el tipo de campo
            $valorSanitizado = null;
            
            // Campo estrato es numérico
            if ($nombreCampo === 'estrato') {
                $valorSanitizado = intval($valor);
                error_log("Campo numérico '$nombreCampo' sanitizado: $valorSanitizado");
                
                // Validar que sea un número válido (mayor a 0)
                if ($valorSanitizado <= 0) {
                    error_log("ERROR: Campo '$nombreCampo' tiene valor inválido: $valorSanitizado");
                    continue;
                }
            }
            // Campos grado y grupo son alfanuméricos (ej: GRA411, GRU211)
            else if (in_array($nombreCampo, ['grado', 'grupo'])) {
                $valorSanitizado = mysqli_real_escape_string($conexion, trim($valor));
                error_log("Campo alfanumérico '$nombreCampo' sanitizado: '$valorSanitizado'");
                
                // Validar que no esté vacío y tenga formato válido
                if (empty($valorSanitizado) || !preg_match('/^[A-Z0-9]+$/', $valorSanitizado)) {
                    error_log("ERROR: Campo '$nombreCampo' tiene formato inválido: $valorSanitizado");
                    continue;
                }
            }
            // Estado de matrícula es numérico
            else if ($nombreCampo === 'estadoMatricula') {
                $valorSanitizado = intval($valor);
                error_log("Campo numérico '$nombreCampo' sanitizado: $valorSanitizado");
                
                // Validar que sea un número válido
                if ($valorSanitizado <= 0) {
                    error_log("ERROR: Campo '$nombreCampo' tiene valor inválido: $valorSanitizado");
                    continue;
                }
            }
            else {
                // Por defecto, escapar como string
                $valorSanitizado = mysqli_real_escape_string($conexion, trim($valor));
                error_log("Campo string '$nombreCampo' sanitizado: '$valorSanitizado'");
            }
            
            $datosActualizar[$columna] = $valorSanitizado;
        } else {
            error_log("ADVERTENCIA: Campo '$nombreCampo' no tiene mapeo en camposMapeados");
        }
    }

    // Validar que haya campos válidos para actualizar O que se vaya a generar usuarios
    $generarUsuarios = isset($campos['generarUsuario']) && $campos['generarUsuario'] == '1';
    
    if (count($datosActualizar) === 0 && !$generarUsuarios) {
        error_log("ERROR: No hay campos válidos para actualizar y no se solicitó generar usuarios");
        throw new Exception('No se especificaron campos para actualizar ni se solicitó generar usuarios.');
    }
    
    if (count($datosActualizar) === 0 && $generarUsuarios) {
        error_log("INFO: No hay campos para actualizar, pero se generarán usuarios");
    }
    
    error_log("Datos a actualizar preparados: " . print_r($datosActualizar, true));
    error_log("Variable estudiantes antes del count: " . print_r($estudiantes, true));
    error_log("Tipo de variable estudiantes: " . gettype($estudiantes));
    error_log("Es array: " . (is_array($estudiantes) ? 'SI' : 'NO'));
    error_log("Total de estudiantes a actualizar: " . count($estudiantes));

    // Contador de actualizaciones exitosas
    $actualizadas = 0;
    $errores = [];
    $estudiantesConNotas = 0;
    $permisoDev = isset($datosUsuarioActual['uss_tipo']) && (int)$datosUsuarioActual['uss_tipo'] === TIPO_DEV;

    // Actualizar cada estudiante (solo si hay campos para actualizar)
    foreach ($estudiantes as $idEstudiante) {
        try {
            error_log("Procesando estudiante ID: $idEstudiante");
            
            // Validar que el ID de estudiante sea válido (alfanumérico)
            if (empty($idEstudiante) || !preg_match('/^[A-Z0-9]+$/', $idEstudiante)) {
                $mensaje = "ID de estudiante inválido: $idEstudiante";
                error_log("ERROR: $mensaje");
                $errores[] = $mensaje;
                continue;
            }
            
            error_log("✓ ID de estudiante válido: $idEstudiante");

            // Si no hay campos para actualizar, pasar al siguiente estudiante
            if (count($datosActualizar) === 0) {
                error_log("INFO: No hay campos para actualizar en estudiante $idEstudiante, se omite actualización");
                continue;
            }

            // Obtener datos actuales del estudiante para validaciones
            $datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($idEstudiante);
            if (empty($datosEstudianteActual)) {
                $mensaje = "Estudiante no encontrado: $idEstudiante";
                error_log("ERROR: $mensaje");
                $errores[] = $mensaje;
                continue;
            }
            
            // Validar cambio de estado usando el método centralizado (DEV puede Matriculado → No matriculado)
            if (isset($datosActualizar['mat_estado_matricula'])) {
                $estadoActual = (int)$datosEstudianteActual['mat_estado_matricula'];
                $estadoNuevo = (int)$datosActualizar['mat_estado_matricula'];
                
                $validacion = Estudiantes::validarCambioEstadoMatricula($estadoActual, $estadoNuevo, $permisoDev);
                
                if (!$validacion['valido']) {
                    $mensaje = "Estudiante $idEstudiante: " . $validacion['mensaje'];
                    error_log("ERROR: $mensaje");
                    $errores[] = $mensaje;
                    
                    // Remover el estado de matrícula de los datos a actualizar
                    $datosActualizarEstudiante = $datosActualizar;
                    unset($datosActualizarEstudiante['mat_estado_matricula']);
                    
                    if (count($datosActualizarEstudiante) === 0) {
                        error_log("No hay campos para actualizar en estudiante $idEstudiante (solo estado de matrícula)");
                        continue;
                    }
                } else {
                    $datosActualizarEstudiante = $datosActualizar;
                }
            } else {
                $datosActualizarEstudiante = $datosActualizar;
            }
            
            // Verificar si el estudiante tiene registros académicos (para grado/grupo)
            $tieneRegistrosAcademicos = false;
            if (isset($datosActualizarEstudiante['mat_grado']) || isset($datosActualizarEstudiante['mat_grupo'])) {
                // Usar el método existente de la clase Estudiante
                $EstudianteObj = new Administrativo_Usuario_Estudiante(['mat_id' => $idEstudiante]);
                $tieneRegistrosAcademicos = (bool) $EstudianteObj->tieneRegistrosAcademicos();
                error_log("Estudiante $idEstudiante tiene registros académicos: " . ($tieneRegistrosAcademicos ? 'SI' : 'NO'));
                
                // Verificar también la configuración del sistema
                $puedeModificarGradoGrupo = $config['conf_puede_cambiar_grado_y_grupo'] == 1;
                error_log("Configuración permite cambiar grado/grupo con notas: " . ($puedeModificarGradoGrupo ? 'SI' : 'NO'));
                
                if ($tieneRegistrosAcademicos && !$puedeModificarGradoGrupo) {
                    $estudiantesConNotas++;
                    error_log("⚠️ Estudiante $idEstudiante tiene registros académicos y no se permite cambiar grado/grupo, se omiten cambios");
                    
                    // Remover grado y grupo de los datos a actualizar para este estudiante
                    unset($datosActualizarEstudiante['mat_grado']);
                    unset($datosActualizarEstudiante['mat_grupo']);
                    
                    if (count($datosActualizarEstudiante) === 0) {
                        error_log("No hay campos para actualizar en estudiante $idEstudiante (solo grado/grupo)");
                        continue;
                    }
                }
            }

            // Actualizar la matrícula usando UPDATE directo
            $updateParts = [];
            foreach ($datosActualizarEstudiante as $columna => $valor) {
                error_log("Preparando UPDATE para columna: $columna, valor: " . var_export($valor, true) . ", tipo: " . gettype($valor));
                
                // Para campos numéricos puros (solo estrato y estado de matrícula)
                if (in_array($columna, ['mat_estrato', 'mat_estado_matricula'])) {
                    $updateParts[] = "`$columna` = $valor";
                } else {
                    // Para campos string o alfanuméricos (grado, grupo, etc.)
                    $updateParts[] = "`$columna` = '$valor'";
                }
            }
            
            $updateString = implode(', ', $updateParts);
            
            // Construir query con el nombre completo de la base de datos
            error_log("Usando BD_ACADEMICA: " . BD_ACADEMICA);
            $sql = "UPDATE `" . BD_ACADEMICA . "`.`academico_matriculas` 
                    SET $updateString 
                    WHERE mat_id = '$idEstudiante' 
                    AND institucion = {$config['conf_id_institucion']} 
                    AND year = {$_SESSION['bd']}";
            
            error_log("SQL generado: $sql");
            
            // Ejecutar la query
            $resultado = mysqli_query($conexion, $sql);
            
            if ($resultado) {
                $filasAfectadas = mysqli_affected_rows($conexion);
                error_log("✓ Query ejecutada exitosamente. Filas afectadas: $filasAfectadas");
                
                if ($filasAfectadas > 0) {
                    $actualizadas++;
                    error_log("✓ Estudiante $idEstudiante actualizado exitosamente");
                } else {
                    error_log("⚠️ Query ejecutada pero no afectó filas para estudiante $idEstudiante");
                }
            } else {
                $error = mysqli_error($conexion);
                $mensaje = "Error en query para estudiante $idEstudiante: $error";
                error_log("ERROR: $mensaje");
                $errores[] = $mensaje;
            }
            
        } catch (Exception $e) {
            $mensaje = "Error procesando estudiante $idEstudiante: " . $e->getMessage();
            error_log("ERROR: $mensaje");
            $errores[] = $mensaje;
        }
    }
    
    error_log("Resumen: $actualizadas de " . count($estudiantes) . " estudiantes actualizados");
    error_log("Estudiantes con notas (grado/grupo omitidos): $estudiantesConNotas");
    error_log("Errores encontrados: " . print_r($errores, true));

    // Generar usuarios si se solicitó
    $usuariosGenerados = 0;
    $erroresUsuarios = [];
    if (isset($campos['generarUsuario']) && $campos['generarUsuario'] == '1') {
        error_log("=== INICIANDO GENERACIÓN MASIVA DE USUARIOS ===");
        
        foreach ($estudiantes as $idEstudiante) {
            try {
                // Obtener datos del estudiante
                $est = Estudiantes::obtenerDatosEstudiante($idEstudiante);
                
                if (empty($est)) {
                    error_log("⚠️ No se encontraron datos para estudiante $idEstudiante");
                    continue;
                }
                
                // Verificar si ya tiene usuario
                if (!empty($est['uss_usuario'])) {
                    error_log("⚠️ Estudiante $idEstudiante ya tiene usuario: " . $est['uss_usuario']);
                    continue;
                }
                
                // Verificar que tenga documento
                if (empty($est['mat_documento'])) {
                    error_log("⚠️ Estudiante $idEstudiante no tiene documento registrado");
                    $erroresUsuarios[] = "Estudiante $idEstudiante no tiene documento";
                    continue;
                }
                
                error_log("Generando usuario para estudiante $idEstudiante (documento: " . $est['mat_documento'] . ")");
                
                // Eliminar usuario existente con ese documento (si existe)
                UsuariosPadre::eliminarUsuarioPorUsuario($config, $est['mat_documento']);
                
                // Preparar datos para crear usuario
                $stringDeInsercion = "uss_usuario, uss_tipo_documento, uss_documento, uss_clave, uss_tipo, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_genero, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, institucion, year, uss_id";
                
                $datosParaInsertar = [
                    $est['mat_documento'],
                    $est['mat_tipo_documento'],
                    $est['mat_documento'], 
                    $clavePorDefectoUsuarios,
                    TIPO_ESTUDIANTE,
                    $est['mat_nombres'], 
                    $est['mat_nombre2'], 
                    $est['mat_primer_apellido'], 
                    $est['mat_segundo_apellido'],
                    0,
                    'default.png',
                    'default.png',
                    1,
                    'blue',
                    0,
                    'Estudiante',
                    $est['mat_email'],
                    $est['mat_fecha_nacimiento'],
                    $est['mat_genero'],
                    0,
                    date("Y-m-d H:i:s"),
                    $_SESSION["id"],
                    $config['conf_id_institucion'],
                    $_SESSION["bd"]
                ];
                
                // Guardar usuario
                $idUsuario = UsuariosPadre::guardarUsuario($conexionPDO, $stringDeInsercion, $datosParaInsertar);
                
                if ($idUsuario) {
                    // Actualizar la matrícula con el ID del usuario
                    $update = ['mat_id_usuario' => $idUsuario];
                    Estudiantes::actualizarMatriculasPorId($config, $idEstudiante, $update);
                    
                    $usuariosGenerados++;
                    error_log("✓ Usuario generado exitosamente para estudiante $idEstudiante (ID Usuario: $idUsuario, Usuario: " . $est['mat_documento'] . ")");
                } else {
                    error_log("ERROR: No se pudo crear el usuario para estudiante $idEstudiante");
                    $erroresUsuarios[] = "No se pudo crear usuario para estudiante $idEstudiante";
                }
                
            } catch (Exception $e) {
                $mensaje = "Error generando usuario para estudiante $idEstudiante: " . $e->getMessage();
                error_log("ERROR: $mensaje");
                $erroresUsuarios[] = $mensaje;
            }
        }
        
        error_log("=== FIN GENERACIÓN MASIVA DE USUARIOS ===");
        error_log("Usuarios generados: $usuariosGenerados de " . count($estudiantes));
    }

    // Preparar respuesta
    $response = [
        'success' => $actualizadas > 0 || $usuariosGenerados > 0,
        'actualizadas' => $actualizadas,
        'total' => count($estudiantes),
        'campos_actualizados' => array_keys($datosActualizar),
        'estudiantes_con_notas' => $estudiantesConNotas,
        'usuarios_generados' => $usuariosGenerados,
        'errores' => array_merge($errores, $erroresUsuarios)
    ];
    
    if ($actualizadas > 0 || $usuariosGenerados > 0) {
        $mensajes = [];
        
        if ($actualizadas > 0) {
            $mensajes[] = "Se actualizaron $actualizadas de " . count($estudiantes) . " estudiantes";
        }
        
        if ($usuariosGenerados > 0) {
            $mensajes[] = "Se generaron $usuariosGenerados usuarios";
        }
        
        $response['message'] = implode('. ', $mensajes) . '.';
        
        if ($estudiantesConNotas > 0) {
            $response['message'] .= " $estudiantesConNotas estudiantes tenían registros académicos, por lo que no se modificaron grado/grupo.";
        }
    } else {
        $response['message'] = 'No se pudo actualizar ningún estudiante. ' . implode(' ', array_merge($errores, $erroresUsuarios));
    }
    
    error_log("Respuesta final: " . json_encode($response));
    echo json_encode($response);

} catch (Exception $e) {
    error_log("ERROR GENERAL: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'actualizadas' => 0,
        'total' => 0
    ]);
}
?>
