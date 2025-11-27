<?php
include("session.php");

// Asegurar que las constantes de BD estén disponibles
if (!defined('BD_GENERAL')) {
    require_once(ROOT_PATH."/conexion.php");
}

header('Content-Type: application/json');

Modulos::verificarPermisoDev();

$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

try {
    $tipoInsti = $_POST['tipoInsti'] ?? '';
    
    if ($tipoInsti === '1') {
        // Validar nueva institución
        $required = ['nombreInsti', 'siglasInst', 'siglasBD', 'yearN', 'tipoDoc', 'documento', 'nombre1', 'apellido1', 'email'];
        
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $response['errors'][] = "Campo requerido: $field";
            }
        }
        
        if (!empty($response['errors'])) {
            $response['message'] = 'Faltan campos requeridos';
            echo json_encode($response);
            exit;
        }
        
        $siglasBD = trim($_POST['siglasBD']);
        $year = $_POST['yearN'];
        
        // Validar que no exista ya
        $consulta = mysqli_query($conexion, "SELECT ins_id 
            FROM ".BD_ADMIN.".instituciones 
            WHERE ins_bd = '".BD_PREFIX.$siglasBD."' 
            AND ins_enviroment='".ENVIROMENT."'");
        
        if (mysqli_num_rows($consulta) > 0) {
            $response['message'] = 'Ya existe una institución con estas siglas de BD';
            echo json_encode($response);
            exit;
        }
        
        // Validar email
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Formato de correo electrónico inválido';
            echo json_encode($response);
            exit;
        }
        
    } else {
        // Validar renovación
        $idInsti = $_POST['idInsti'] ?? '';
        $yearA = $_POST['yearA'] ?? '';
        
        if (empty($idInsti) || empty($yearA)) {
            $response['message'] = 'Institución y año son requeridos';
            echo json_encode($response);
            exit;
        }
        
        // Verificar que la institución existe
        $consulta = mysqli_query($conexion, "SELECT ins_id, ins_bd, ins_years 
            FROM ".BD_ADMIN.".instituciones 
            WHERE ins_id = '".$idInsti."' 
            AND ins_enviroment='".ENVIROMENT."'");
        
        if (mysqli_num_rows($consulta) === 0) {
            $response['message'] = 'Institución no encontrada';
            echo json_encode($response);
            exit;
        }
        
        $datosInsti = mysqli_fetch_assoc($consulta);
        $yearsArray = explode(',', $datosInsti['ins_years']);
        
        // Verificar que el año no exista ya
        if (in_array($yearA, $yearsArray)) {
            $response['message'] = 'El año ' . $yearA . ' ya existe para esta institución';
            echo json_encode($response);
            exit;
        }
        
        // Verificar que existe el año anterior
        $yearAnterior = $yearA - 1;
        if (!in_array($yearAnterior, $yearsArray)) {
            $response['message'] = 'No existe el año anterior ('.$yearAnterior.') para copiar los datos';
            echo json_encode($response);
            exit;
        }
    }
    
    $response['success'] = true;
    $response['message'] = 'Validación exitosa. Todo listo para proceder.';
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error en validación: ' . $e->getMessage();
    include("../compartido/error-catch-to-report.php");
}

echo json_encode($response);

