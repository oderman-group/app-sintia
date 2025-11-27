<?php
/**
 * DESBLOQUEAR IP O USUARIO
 * Elimina intentos fallidos para permitir acceso inmediato
 */

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/RateLimit.php");

// Verificar permisos de desarrollador
Modulos::verificarPermisoDev();

// Obtener datos
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : ''; // 'ip' o 'usuario'
$valor = isset($_POST['valor']) ? mysqli_real_escape_string($conexion, trim($_POST['valor'])) : '';

if (empty($tipo) || empty($valor)) {
    echo json_encode([
        'success' => false,
        'message' => 'Parámetros inválidos'
    ]);
    exit();
}

try {
    if ($tipo === 'ip') {
        // Eliminar intentos de una IP específica
        $query = "DELETE FROM " . BD_ADMIN . ".usuarios_intentos_fallidos WHERE uif_ip = '$valor'";
        mysqli_query($conexion, $query);
        
        $registrosEliminados = mysqli_affected_rows($conexion);
        
        error_log("🔓 DESBLOQUEO IP - IP: {$valor} | Registros eliminados: {$registrosEliminados} | Admin: " . $_SESSION['id']);
        
        echo json_encode([
            'success' => true,
            'message' => "✅ IP {$valor} desbloqueada. Se eliminaron {$registrosEliminados} registro(s).",
            'registros_eliminados' => $registrosEliminados
        ]);
        
    } elseif ($tipo === 'usuario') {
        // Buscar el uss_id del usuario
        $queryUsuario = "SELECT uss_id, institucion, year FROM " . BD_GENERAL . ".usuarios 
                        WHERE uss_usuario = '$valor' 
                        ORDER BY uss_id DESC LIMIT 1";
        
        $consultaUsuario = mysqli_query($conexion, $queryUsuario);
        
        if (mysqli_num_rows($consultaUsuario) == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
            exit();
        }
        
        $datosUsuario = mysqli_fetch_array($consultaUsuario, MYSQLI_BOTH);
        $ussId = $datosUsuario['uss_id'];
        
        // Eliminar intentos del usuario
        $query = "DELETE FROM " . BD_ADMIN . ".usuarios_intentos_fallidos WHERE uif_usuarios = '$ussId'";
        mysqli_query($conexion, $query);
        
        $registrosEliminados = mysqli_affected_rows($conexion);
        
        // Resetear contador en tabla usuarios
        mysqli_query($conexion, "UPDATE " . BD_GENERAL . ".usuarios 
                                SET uss_intentos_fallidos = 0 
                                WHERE uss_id = '$ussId' 
                                AND institucion = '" . $datosUsuario['institucion'] . "' 
                                AND year = '" . $datosUsuario['year'] . "'");
        
        error_log("🔓 DESBLOQUEO USUARIO - Usuario: {$valor} | uss_id: {$ussId} | Registros eliminados: {$registrosEliminados} | Admin: " . $_SESSION['id']);
        
        echo json_encode([
            'success' => true,
            'message' => "✅ Usuario {$valor} desbloqueado. Se eliminaron {$registrosEliminados} registro(s) y se reseteó el contador.",
            'registros_eliminados' => $registrosEliminados
        ]);
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Tipo de desbloqueo inválido'
        ]);
    }
    
} catch (Exception $e) {
    error_log("❌ ERROR DESBLOQUEO - Tipo: {$tipo} | Valor: {$valor} | Error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al desbloquear: ' . $e->getMessage()
    ]);
}

