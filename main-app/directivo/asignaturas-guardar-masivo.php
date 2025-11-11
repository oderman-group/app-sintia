<?php 
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0180';

if(!Modulos::validarSubRol([$idPaginaInterna])){
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción.'
    ]);
    exit();
}

require_once(ROOT_PATH."/main-app/class/Asignaturas.php");

// Validar que se hayan recibido los datos
if(empty($_POST['asignaturas'])){
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos requeridos.'
    ]);
    exit();
}

$asignaturas = $_POST['asignaturas'];
$area = isset($_POST['area']) && !empty($_POST['area']) ? $_POST['area'] : null;

// Validar que el área exista solo si se proporcionó
if($area !== null){
    require_once(ROOT_PATH."/main-app/class/Areas.php");
    $consultaArea = Areas::traerDatosArea($config, $area);
    // El método retorna un array, no mysqli_result
    if(empty($consultaArea) || $consultaArea === false){
        echo json_encode([
            'success' => false,
            'message' => 'El área seleccionada no existe.'
        ]);
        exit();
    }
}

try {
    $guardadas = 0;
    $errores = 0;
    $nombresGuardados = [];
    
    foreach($asignaturas as $asignatura){
        // Validar que tenga al menos el nombre
        if(empty(trim($asignatura['nombre']))){
            $errores++;
            continue;
        }
        
        // Preparar datos para guardar
        $datosAsignatura = [
            'nombreM' => trim($asignatura['nombre']),
            'siglasM' => !empty(trim($asignatura['siglas'])) ? trim($asignatura['siglas']) : substr(trim($asignatura['nombre']), 0, 3),
            'areaM' => $area !== null ? $area : '', // Si no hay área, enviar vacío
            'sumarPromedio' => isset($asignatura['promedio']) ? $asignatura['promedio'] : SI,
            'porcenAsigna' => 100 // Por defecto 100%
        ];
        
        try {
            $codigo = Asignaturas::guardarAsignatura($conexion, $conexionPDO, $config, $datosAsignatura);
            $guardadas++;
            $nombresGuardados[] = $datosAsignatura['nombreM'];
        } catch(Exception $e) {
            $errores++;
            error_log("Error al guardar asignatura: " . $e->getMessage());
        }
    }
    
    // Guardar historial de acciones
    include("../compartido/guardar-historial-acciones.php");
    
    if($guardadas > 0){
        $mensaje = "Se guardaron exitosamente {$guardadas} asignatura(s)";
        if($errores > 0){
            $mensaje .= " y hubo {$errores} error(es)";
        }
        $mensaje .= ". Asignaturas creadas: " . implode(", ", $nombresGuardados);
        
        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'guardadas' => $guardadas,
            'errores' => $errores
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo guardar ninguna asignatura. Verifica los datos e intenta nuevamente.'
        ]);
    }
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar las asignaturas: ' . $e->getMessage()
    ]);
}
exit();

