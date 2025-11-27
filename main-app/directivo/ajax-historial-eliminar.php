<?php
/**
 * ELIMINAR REGISTROS DEL HISTORIAL DE ACCIONES
 * Permite eliminar individual, por lote o todos los registros
 */

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");

// Verificar permisos
Modulos::verificarPermisoDev();

try {
    $accion = $_POST['accion'] ?? ''; // 'individual', 'lote', 'todos'
    $ids = $_POST['ids'] ?? []; // Array de IDs para eliminar
    
    if (empty($accion)) {
        throw new Exception('Acción no especificada');
    }
    
    $registrosEliminados = 0;
    
    switch ($accion) {
        case 'individual':
            // Eliminar un solo registro
            if (empty($ids) || !is_array($ids) || count($ids) !== 1) {
                throw new Exception('ID de registro no proporcionado');
            }
            
            $id = (int)$ids[0];
            $sql = "DELETE FROM " . $baseDatosServicios . ".seguridad_historial_acciones WHERE hil_id = $id";
            
            if (mysqli_query($conexion, $sql)) {
                $registrosEliminados = mysqli_affected_rows($conexion);
            } else {
                throw new Exception('Error al eliminar: ' . mysqli_error($conexion));
            }
            break;
            
        case 'lote':
            // Eliminar múltiples registros seleccionados
            if (empty($ids) || !is_array($ids)) {
                throw new Exception('No se proporcionaron IDs para eliminar');
            }
            
            $idsLimpios = array_map('intval', $ids);
            $idsString = implode(',', $idsLimpios);
            
            $sql = "DELETE FROM " . $baseDatosServicios . ".seguridad_historial_acciones WHERE hil_id IN ($idsString)";
            
            if (mysqli_query($conexion, $sql)) {
                $registrosEliminados = mysqli_affected_rows($conexion);
            } else {
                throw new Exception('Error al eliminar lote: ' . mysqli_error($conexion));
            }
            break;
            
        case 'todos':
            // Eliminar TODOS los registros (con confirmación adicional)
            $confirmacion = $_POST['confirmacion'] ?? '';
            
            if ($confirmacion !== 'CONFIRMAR_ELIMINAR_TODO') {
                throw new Exception('Confirmación requerida para eliminar todos los registros');
            }
            
            // Aplicar filtros si existen (para eliminar solo los filtrados)
            $institucion = isset($_POST['institucion']) ? mysqli_real_escape_string($conexion, $_POST['institucion']) : '';
            $filtros = [];
            
            if (!empty($institucion) && $institucion !== 'todos') {
                $filtros[] = "hil_institucion = '$institucion'";
            }
            
            $whereClause = count($filtros) > 0 ? "WHERE " . implode(" AND ", $filtros) : "";
            
            $sql = "DELETE FROM " . $baseDatosServicios . ".seguridad_historial_acciones $whereClause";
            
            if (mysqli_query($conexion, $sql)) {
                $registrosEliminados = mysqli_affected_rows($conexion);
            } else {
                throw new Exception('Error al eliminar todos: ' . mysqli_error($conexion));
            }
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
    // Registrar la eliminación en logs
    error_log("HISTORIAL ELIMINADO - Acción: $accion - Registros: $registrosEliminados - Usuario: " . $_SESSION['id']);
    
    echo json_encode([
        'success' => true,
        'message' => "✅ Se eliminaron $registrosEliminados registro(s) exitosamente",
        'registros_eliminados' => $registrosEliminados
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

