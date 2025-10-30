<?php
/**
 * VALIDAR CÓDIGO Y COMPLETAR REGISTRO
 * Valida el código de verificación y activa la cuenta
 */

// Configurar zona horaria de Colombia
date_default_timezone_set('America/Bogota');

header('Content-Type: application/json; charset=UTF-8');
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH . "/main-app/class/Notificacion.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");

$notificacion = new Notificacion();
$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);

if (!$conexion) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión'
    ]);
    exit();
}

// Obtener datos
$codigo = isset($_POST['code']) ? trim($_POST['code']) : '';
$idRegistro = isset($_POST['idRegistro']) ? (int)$_POST['idRegistro'] : 0;
$usuarioId = isset($_POST['usuarioId']) ? $_POST['usuarioId'] : '';  // VARCHAR, NO convertir a int
$institucionId = isset($_POST['institucionId']) ? (int)$_POST['institucionId'] : 0;

// Log de debug completo
error_log("========================================");
error_log("VALIDACIÓN DE CÓDIGO - DATOS RECIBIDOS");
error_log("========================================");
error_log("POST completo: " . print_r($_POST, true));
error_log("----------------------------------------");
error_log("Código recibido: " . $codigo);
error_log("ID Registro (codv_id): " . $idRegistro);
error_log("Usuario ID (uss_id) recibido: " . $usuarioId);
error_log("Usuario ID tipo: " . gettype($usuarioId));
error_log("Institución ID: " . $institucionId);
error_log("========================================");

if (empty($codigo) || $idRegistro == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Código o ID de registro inválido'
    ]);
    exit();
}

try {
    // Iniciar transacción
    mysqli_query($conexion, "BEGIN");
    
    $codigoEscaped = mysqli_real_escape_string($conexion, $codigo);
    
    // Buscar el código en la base de datos
    $consulta = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".codigos_verificacion 
        WHERE codv_id='{$idRegistro}' AND codv_usuario_asociado IS NOT NULL AND codv_activo=1 
        ORDER BY codv_fecha_registro DESC LIMIT 1");
    
    error_log("Consulta código ejecutada. Registros encontrados: " . mysqli_num_rows($consulta));
    
    if (mysqli_num_rows($consulta) == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Código no encontrado o ya fue utilizado'
        ]);
        exit();
    }
    
    $registro = mysqli_fetch_array($consulta, MYSQLI_BOTH);
    
    // Verificar si el código ha expirado (10 minutos)
    $fechaRegistro = strtotime($registro['codv_fecha_registro']);
    $tiempoTranscurrido = time() - $fechaRegistro;
    $tiempoMaximo = 10 * 60;
    
    if ($tiempoTranscurrido > $tiempoMaximo) {
        echo json_encode([
            'success' => false,
            'message' => 'El código ha expirado. Por favor solicita uno nuevo.',
            'expired' => true
        ]);
        exit();
    }
    
    // Verificar el código
    if ($registro['codv_codigo_verificacion'] !== $codigo) {
        // Incrementar intentos fallidos
        mysqli_query($conexion, "UPDATE " . BD_ADMIN . ".codigos_verificacion SET 
            codv_intentos_fallidos=codv_intentos_fallidos+1 
            WHERE codv_id='{$idRegistro}'");
        
        $intentosFallidos = (int)$registro['codv_intentos_fallidos'] + 1;
        
        if ($intentosFallidos >= 5) {
            mysqli_query($conexion, "UPDATE " . BD_ADMIN . ".codigos_verificacion SET 
                codv_activo=0 
                WHERE codv_id='{$idRegistro}'");
            
            echo json_encode([
                'success' => false,
                'message' => 'Has excedido el número máximo de intentos.',
                'max_attempts' => true
            ]);
            exit();
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Código incorrecto. Intentos restantes: ' . (5 - $intentosFallidos)
        ]);
        exit();
    }
    
    // Código correcto - USAR TRANSACCIÓN COMPLETA
    
    // 1. Marcar código como usado
    $updateCodigo = mysqli_query($conexion, "UPDATE " . BD_ADMIN . ".codigos_verificacion SET 
        codv_activo=0,
        codv_fecha_uso=NOW()
        WHERE codv_id='{$idRegistro}'");
    
    if (!$updateCodigo) {
        mysqli_query($conexion, "ROLLBACK");
        error_log("Error al actualizar código: " . mysqli_error($conexion));
        echo json_encode([
            'success' => false,
            'message' => 'Error al marcar código como usado'
        ]);
        exit();
    }
    
    // 2. Buscar usuario por uss_id (el generado manualmente desde PHP)
    // IMPORTANTE: uss_id es VARCHAR, necesita comillas
    $usuarioIdEscaped = mysqli_real_escape_string($conexion, $usuarioId);
    
    error_log("Buscando usuario con uss_id (VARCHAR): " . $usuarioId);
    error_log("Tipo de dato: " . gettype($usuarioId));
    
    $queryBuscarUsuario = "SELECT 
        u.uss_id, u.uss_nombre, u.uss_apellido1, u.uss_email, u.uss_usuario, u.year, u.institucion,
        i.ins_id, i.ins_nombre
        FROM " . BD_GENERAL . ".usuarios u 
        INNER JOIN " . BD_ADMIN . ".instituciones i ON u.institucion = i.ins_id 
        WHERE u.uss_id = '{$usuarioIdEscaped}' LIMIT 1";
    
    error_log("Query para buscar usuario: " . $queryBuscarUsuario);
    
    $consultaUsuario = mysqli_query($conexion, $queryBuscarUsuario);
    
    if (!$consultaUsuario) {
        mysqli_query($conexion, "ROLLBACK");
        error_log("ERROR EN QUERY: " . mysqli_error($conexion));
        echo json_encode([
            'success' => false,
            'message' => 'Error en consulta SQL'
        ]);
        exit();
    }
    
    if (mysqli_num_rows($consultaUsuario) == 0) {
        mysqli_query($conexion, "ROLLBACK");
        error_log("ERROR: Usuario no encontrado con uss_id: " . $usuarioId);
        
        // Buscar si existe algún usuario para debug
        $debugQuery = mysqli_query($conexion, "SELECT uss_id, uss_usuario FROM " . BD_GENERAL . ".usuarios LIMIT 5");
        error_log("Primeros 5 usuarios en BD para debug:");
        while ($row = mysqli_fetch_assoc($debugQuery)) {
            error_log("  - uss_id: " . $row['uss_id'] . ", uss_usuario: " . $row['uss_usuario']);
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Error: Usuario no encontrado (uss_id: ' . $usuarioId . ')'
        ]);
        exit();
    }
    
    $datosUsuario = mysqli_fetch_array($consultaUsuario, MYSQLI_BOTH);
    error_log("========================================");
    error_log("USUARIO ENCONTRADO:");
    error_log("- uss_id: " . $datosUsuario['uss_id']);
    error_log("- uss_usuario: " . $datosUsuario['uss_usuario']);
    error_log("- uss_nombre: " . $datosUsuario['uss_nombre'] . " " . $datosUsuario['uss_apellido1']);
    error_log("- uss_email: " . $datosUsuario['uss_email']);
    error_log("- ins_nombre: " . $datosUsuario['ins_nombre']);
    error_log("========================================");
    
    // 3. Activar cuenta del usuario (usando uss_id VARCHAR con comillas)
    $updateUsuario = mysqli_query($conexion, "UPDATE " . BD_GENERAL . ".usuarios 
        SET uss_estado = 1, uss_bloqueado = 0 
        WHERE uss_id = '{$usuarioIdEscaped}'");
    
    if (!$updateUsuario) {
        mysqli_query($conexion, "ROLLBACK");
        error_log("Error al activar usuario: " . mysqli_error($conexion));
        echo json_encode([
            'success' => false,
            'message' => 'Error al activar la cuenta del usuario'
        ]);
        exit();
    }
    
    // 4. Actualizar general_informacion con el rector y secretario
    $queryUpdateInfo = "UPDATE " . BD_ADMIN . ".general_informacion 
                        SET info_rector = '{$usuarioIdEscaped}',
                            info_secretaria_academica = '{$usuarioIdEscaped}'
                        WHERE info_institucion = '{$datosUsuario['institucion']}' 
                        AND info_year = '{$datosUsuario['year']}'";
    
    if(!mysqli_query($conexion, $queryUpdateInfo)) {
        throw new Exception('Error al actualizar general_informacion: ' . mysqli_error($conexion));
    }
    error_log("general_informacion actualizado con rector/secretario: " . $usuarioIdEscaped);
    
    // 5. Commit de la transacción
    mysqli_query($conexion, "COMMIT");
    error_log("Transacción completada exitosamente");
    
    // 6. Preparar datos para email y bienvenida
    // IMPORTANTE: Usar los datos del usuario recién activado, NO de otra consulta
    
    // Recuperar usos de SINTIA desde la sesión si existe
    $usoSintiaTexto = 'No especificado';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['usosSintiaTemp'])) {
        $usoSintiaTexto = $_SESSION['usosSintiaTemp'];
        unset($_SESSION['usosSintiaTemp']); // Limpiar después de usar
        error_log("🔵 Usos SINTIA recuperados de sesión: " . $usoSintiaTexto);
    }
    
    $dataEmail = [
        'institucion_id' => $datosUsuario['institucion'],
        'institucion_agno' => $datosUsuario['year'],
        'institucion_nombre' => $datosUsuario['ins_nombre'],
        'usuario_id' => $datosUsuario['uss_id'],
        'usuario_email' => $datosUsuario['uss_email'],
        'usuario_nombre' => $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'],
        'usuario_usuario' => $datosUsuario['uss_usuario'],
        'usuario_clave' => '12345678',
        'uso_sintia' => $usoSintiaTexto
    ];
    
    // Guardar en sesión para la página de bienvenida
    // (La sesión ya está iniciada arriba)
    $_SESSION['datosRegistroCompletado'] = $dataEmail;
    
    // Log de datos que se envían
    error_log("========================================");
    error_log("ENVIANDO EMAIL DE BIENVENIDA");
    error_log("========================================");
    error_log("Datos del usuario desde BD:");
    error_log("- uss_nombre: " . $datosUsuario['uss_nombre']);
    error_log("- uss_apellido1: " . $datosUsuario['uss_apellido1']);
    error_log("- uss_email: " . $datosUsuario['uss_email']);
    error_log("- uss_usuario: " . $datosUsuario['uss_usuario']);
    error_log("- ins_nombre: " . $datosUsuario['ins_nombre']);
    error_log("----------------------------------------");
    error_log("Array dataEmail que se envía:");
    error_log("- usuario_nombre: " . $dataEmail['usuario_nombre']);
    error_log("- usuario_email: " . $dataEmail['usuario_email']);
    error_log("- usuario_usuario: " . $dataEmail['usuario_usuario']);
    error_log("- institucion_nombre: " . $dataEmail['institucion_nombre']);
    error_log("- uso_sintia: " . $dataEmail['uso_sintia']);
    error_log("========================================");
    
    $asunto = $datosUsuario['uss_nombre'] . ', Bienvenido a la Plataforma SINTIA';
    $bodyTemplateRoute = ROOT_PATH . '/config-general/plantilla-email-bienvenida.php';
    
    try {
        EnviarEmail::enviar($dataEmail, $asunto, $bodyTemplateRoute, null, null);
        error_log("Email de bienvenida enviado exitosamente");
    } catch (Exception $e) {
        error_log("Error al enviar email de bienvenida: " . $e->getMessage());
        // No fallar por el email, la cuenta ya está activada
    }
    
    // Log final de verificación
    error_log("========================================");
    error_log("DATOS FINALES PARA BIENVENIDA:");
    error_log("- usuario_nombre: " . $dataEmail['usuario_nombre']);
    error_log("- usuario_email: " . $dataEmail['usuario_email']);
    error_log("- usuario_usuario: " . $dataEmail['usuario_usuario']);
    error_log("- institucion_nombre: " . $dataEmail['institucion_nombre']);
    error_log("- usuario_clave: " . $dataEmail['usuario_clave']);
    error_log("- uso_sintia: " . $dataEmail['uso_sintia']);
    error_log("========================================");
    
    echo json_encode([
        'success' => true,
        'message' => '¡Cuenta activada exitosamente!',
        'redirect' => 'bienvenida.php' // Usar SESSION en lugar de parámetro GET
    ]);
    
} catch (Exception $e) {
    // Rollback en caso de error
    mysqli_query($conexion, "ROLLBACK");
    error_log("ERROR en validación: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al validar: ' . $e->getMessage()
    ]);
}

exit();

