<?php
/**
 * VALIDACIONES AJAX PARA REGISTRO
 * Plataforma Educativa SINTIA
 * Versión 2.0
 */

header('Content-Type: application/json');
require_once("../conexion.php");

// Obtener la acción solicitada
$action = isset($_GET['action']) ? $_GET['action'] : '';

/**
 * Verifica si un email ya está registrado
 */
if ($action === 'checkEmail') {
    $email = isset($_GET['email']) ? trim($_GET['email']) : '';
    
    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'available' => false,
            'message' => 'Formato de email inválido'
        ]);
        exit();
    }
    
    try {
        // Verificar si el email ya existe en la base de datos
        $emailEscaped = mysqli_real_escape_string($conexion, $email);
        $consulta = mysqli_query($conexion, "SELECT uss_id FROM " . BD_GENERAL . ".usuarios WHERE uss_email='{$emailEscaped}' AND institucion IS NOT NULL LIMIT 1");
        
        $disponible = mysqli_num_rows($consulta) == 0;
        
        echo json_encode([
            'available' => $disponible,
            'message' => $disponible ? 'Email disponible' : 'Este email ya está registrado'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'available' => false,
            'message' => 'Error al verificar el email',
            'error' => $e->getMessage()
        ]);
    }
    exit();
}

/**
 * Verifica si un teléfono ya está registrado
 */
if ($action === 'checkPhone') {
    $phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
    
    // Validar formato de teléfono (10 dígitos)
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo json_encode([
            'available' => false,
            'message' => 'Formato de teléfono inválido'
        ]);
        exit();
    }
    
    try {
        $phoneEscaped = mysqli_real_escape_string($conexion, $phone);
        $consulta = mysqli_query($conexion, "SELECT uss_id FROM " . BD_GENERAL . ".usuarios WHERE uss_celular='{$phoneEscaped}' AND institucion IS NOT NULL LIMIT 1");
        
        $disponible = mysqli_num_rows($consulta) == 0;
        
        echo json_encode([
            'available' => $disponible,
            'message' => $disponible ? 'Teléfono disponible' : 'Este teléfono ya está registrado'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'available' => false,
            'message' => 'Error al verificar el teléfono',
            'error' => $e->getMessage()
        ]);
    }
    exit();
}

/**
 * Verifica si las siglas de la institución ya existen
 */
if ($action === 'checkInstitution') {
    $siglas = isset($_GET['siglas']) ? trim($_GET['siglas']) : '';
    
    if (empty($siglas)) {
        echo json_encode([
            'available' => false,
            'message' => 'Siglas vacías'
        ]);
        exit();
    }
    
    try {
        $siglasEscaped = mysqli_real_escape_string($conexion, $siglas);
        $consulta = mysqli_query($conexion, "SELECT ins_id FROM " . BD_ADMIN . ".instituciones WHERE ins_siglas='{$siglasEscaped}' LIMIT 1");
        
        $disponible = mysqli_num_rows($consulta) == 0;
        
        echo json_encode([
            'available' => $disponible,
            'message' => $disponible ? 'Nombre de institución disponible' : 'Ya existe una institución con ese nombre'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'available' => false,
            'message' => 'Error al verificar la institución',
            'error' => $e->getMessage()
        ]);
    }
    exit();
}

// Si no se especifica una acción válida
echo json_encode([
    'success' => false,
    'message' => 'Acción no válida'
]);
exit();

