<?php
include("session.php");
require_once ROOT_PATH."/main-app/class/Modulos.php";

header('Content-Type: application/json');

$response = ['success' => false, 'data' => null, 'message' => ''];

try {
    // Validar permisos
    Modulos::verificarPermisoDev();
    
    // Recibir ID de institución
    $institucionId = isset($_POST['institucion_id']) ? intval($_POST['institucion_id']) : 0;
    
    if ($institucionId <= 0) {
        $response['message'] = 'ID de institución no válido';
        echo json_encode($response);
        exit();
    }
    
    // Consultar datos de la institución
    $consultaInst = mysqli_query($conexion, "SELECT ins.*, 
        ciu.ciu_nombre, dep.dep_nombre,
        pl.plns_nombre as plan_nombre, pl.plns_espacio_gb
        FROM " . $baseDatosServicios . ".instituciones ins
        LEFT JOIN " . $baseDatosServicios . ".localidad_ciudades ciu ON ciu.ciu_id = ins.ins_ciudad
        LEFT JOIN " . $baseDatosServicios . ".localidad_departamentos dep ON dep.dep_id = ciu.ciu_departamento
        LEFT JOIN " . $baseDatosServicios . ".planes_sintia pl ON pl.plns_id = ins.ins_id_plan AND pl.plns_tipo='" . PLANES . "'
        WHERE ins.ins_id = {$institucionId} AND ins.ins_enviroment='" . ENVIROMENT . "'");
    
    if (mysqli_num_rows($consultaInst) == 0) {
        $response['message'] = 'Institución no encontrada';
        echo json_encode($response);
        exit();
    }
    
    $datosInstitucion = mysqli_fetch_array($consultaInst, MYSQLI_ASSOC);
    
    // Consultar módulos asignados
    $consultaModulos = mysqli_query($conexion, "SELECT ipmod_modulo FROM " . BD_ADMIN . ".instituciones_modulos 
        WHERE ipmod_institucion = {$institucionId}");
    
    $modulosAsignados = [];
    while ($modulo = mysqli_fetch_array($consultaModulos, MYSQLI_ASSOC)) {
        $modulosAsignados[] = intval($modulo['ipmod_modulo']);
    }
    
    $datosInstitucion['modulos_asignados'] = $modulosAsignados;
    $datosInstitucion['total_modulos'] = count($modulosAsignados);
    
    // Consultar total de módulos disponibles
    $consultaTotalModulos = mysqli_query($conexion, "SELECT COUNT(*) as total FROM " . BD_ADMIN . ".modulos WHERE mod_estado = 1");
    $totalModulos = mysqli_fetch_array($consultaTotalModulos, MYSQLI_ASSOC);
    $datosInstitucion['total_modulos_disponibles'] = intval($totalModulos['total']);
    
    $response['success'] = true;
    $response['data'] = $datosInstitucion;
    $response['message'] = 'Datos obtenidos correctamente';
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>


