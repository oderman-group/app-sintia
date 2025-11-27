<?php
header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");

try {
    if (!Modulos::validarSubRol(['DT0064'])) {
        throw new Exception('No tienes permisos para modificar porcentajes de períodos.');
    }
    
    if (!Modulos::validarPermisoEdicion()) {
        throw new Exception('No tienes permisos de edición.');
    }
    
    $cursoId = isset($_POST['curso_id']) ? trim($_POST['curso_id']) : '';
    $periodo = isset($_POST['periodo']) ? (int)$_POST['periodo'] : 0;
    $porcentaje = isset($_POST['porcentaje']) ? (float)$_POST['porcentaje'] : 0;
    $gvpId = isset($_POST['gvp_id']) ? trim($_POST['gvp_id']) : '';
    
    if (empty($cursoId)) {
        throw new Exception('ID de curso no proporcionado.');
    }
    
    if ($periodo < 1 || $periodo > 10) {
        throw new Exception('Período inválido.');
    }
    
    if ($porcentaje < 0 || $porcentaje > 100) {
        throw new Exception('Porcentaje debe estar entre 0 y 100.');
    }
    
    $institucion = (int)$_SESSION["idInstitucion"];
    $year = (int)$_SESSION['bd'];
    
    // Validar que la suma de porcentajes no exceda 100%
    // Obtener todos los porcentajes existentes para este curso
    $sqlSumaPorcentajes = "SELECT COALESCE(SUM(gvp_valor), 0) as suma_total 
                           FROM " . BD_ACADEMICA . ".academico_grados_periodos 
                           WHERE gvp_grado = ? AND institucion = ? AND year = ?";
    $parametrosSuma = [$cursoId, $institucion, $year];
    $resultadoSuma = BindSQL::prepararSQL($sqlSumaPorcentajes, $parametrosSuma);
    $filaSuma = mysqli_fetch_array($resultadoSuma, MYSQLI_BOTH);
    $sumaActual = (float)($filaSuma['suma_total'] ?? 0);
    
    // Obtener el porcentaje actual del período que se está guardando (si existe)
    $sqlPorcentajeActual = "SELECT gvp_valor FROM " . BD_ACADEMICA . ".academico_grados_periodos 
                            WHERE gvp_grado = ? AND gvp_periodo = ? AND institucion = ? AND year = ?";
    $parametrosPorcentajeActual = [$cursoId, $periodo, $institucion, $year];
    $resultadoPorcentajeActual = BindSQL::prepararSQL($sqlPorcentajeActual, $parametrosPorcentajeActual);
    $filaPorcentajeActual = mysqli_fetch_array($resultadoPorcentajeActual, MYSQLI_BOTH);
    $porcentajeActual = !empty($filaPorcentajeActual['gvp_valor']) ? (float)$filaPorcentajeActual['gvp_valor'] : 0;
    
    // Calcular la nueva suma (restar el porcentaje actual y sumar el nuevo)
    $nuevaSuma = $sumaActual - $porcentajeActual + $porcentaje;
    
    // Validar que no exceda 100%
    if ($nuevaSuma > 100.01) { // Permitir un pequeño margen de error por redondeo
        throw new Exception('La suma de porcentajes excedería el 100%. Suma actual: ' . number_format($sumaActual, 2) . '%, nuevo valor: ' . number_format($porcentaje, 2) . '%, total sería: ' . number_format($nuevaSuma, 2) . '%');
    }
    
    // Validar si hay registros académicos en general (no por curso específico)
    $hayNotasRegistradas = Grados::hayRegistrosAcademicos($config);
    
    if ($hayNotasRegistradas) {
        throw new Exception('No se pueden modificar los porcentajes porque ya existen registros académicos en el sistema.');
    }
    
    // Siempre verificar si existe un registro para este período (independientemente del gvp_id enviado)
    $sqlCheck = "SELECT gvp_id FROM " . BD_ACADEMICA . ".academico_grados_periodos 
                 WHERE gvp_grado = ? AND gvp_periodo = ? AND institucion = ? AND year = ?";
    $parametrosCheck = [$cursoId, $periodo, $institucion, $year];
    $resultadoCheck = BindSQL::prepararSQL($sqlCheck, $parametrosCheck);
    $filaCheck = mysqli_fetch_array($resultadoCheck, MYSQLI_BOTH);
    
    global $conexion;
    
    if (!empty($filaCheck['gvp_id'])) {
        // Actualizar registro existente
        $sql = "UPDATE " . BD_ACADEMICA . ".academico_grados_periodos 
                SET gvp_valor = ? 
                WHERE gvp_id = ? AND institucion = ? AND year = ?";
        $parametros = [$porcentaje, $filaCheck['gvp_id'], $institucion, $year];
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $gvpId = $filaCheck['gvp_id'];
        
        // Verificar que el UPDATE se ejecutó correctamente
        // Para UPDATE, mysqli_stmt_get_result puede retornar false incluso si fue exitoso
        // Verificamos si hay errores de MySQL en su lugar
        if (mysqli_error($conexion)) {
            throw new Exception('Error al actualizar el porcentaje del período: ' . mysqli_error($conexion));
        }
    } else {
        // Generar id_nuevo (gvp_id) para el nuevo registro
        global $conexionPDO;
        if (empty($conexionPDO)) {
            $conexionPDO = Conexion::newConnection('PDO');
        }
        
        $idNuevo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_grados_periodos');
        
        if (empty($idNuevo)) {
            throw new Exception('Error al generar el código del registro.');
        }
        
        // Insertar nuevo registro incluyendo gvp_id (id_nuevo)
        // Nota: gvp_id puede ser int (AUTO_INCREMENT) o varchar (id_nuevo) dependiendo de la configuración de la tabla
        $stmt = mysqli_prepare($conexion, "INSERT INTO " . BD_ACADEMICA . ".academico_grados_periodos 
                (gvp_id, gvp_grado, gvp_periodo, gvp_valor, institucion, year) 
                VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            // gvp_id puede ser string (id_nuevo) o int, gvp_grado puede ser string (GRA111), gvp_periodo es int, gvp_valor es double, institucion es int, year es int
            // Intentar con string primero, si falla será porque gvp_id es int y se generará automáticamente
            mysqli_stmt_bind_param($stmt, "ssidis", $idNuevo, $cursoId, $periodo, $porcentaje, $institucion, $year);
            mysqli_stmt_execute($stmt);
            
            // Obtener el ID numérico del registro insertado (si existe)
            $gvpIdNum = mysqli_insert_id($conexion);
            mysqli_stmt_close($stmt);
            
            // Usar el id_nuevo generado como gvp_id
            $gvpId = $idNuevo;
            
            // Verificar que el INSERT se ejecutó correctamente
            if (mysqli_error($conexion)) {
                throw new Exception('Error al guardar el porcentaje del período: ' . mysqli_error($conexion));
            }
        } else {
            throw new Exception('Error al preparar la consulta de inserción: ' . mysqli_error($conexion));
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Porcentaje del período ' . $periodo . ' guardado correctamente.',
        'gvp_id' => $gvpId
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

