<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Areas.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0179';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción.'
    ]);
    exit();
}

// Validar que se hayan recibido los datos
if(empty($_POST['areas'])){
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos requeridos.'
    ]);
    exit();
}

$areas = $_POST['areas'];

try {
    $guardadas = 0;
    $errores = 0;
    $nombresGuardados = [];
    
    foreach($areas as $area){
        // Validar que tenga los campos obligatorios
        if(empty(trim($area['nombre'])) || empty(trim($area['posicion']))){
            $errores++;
            continue;
        }
        
        // Validar que la posición sea un número válido
        if(!is_numeric($area['posicion']) || intval($area['posicion']) < 1){
            $errores++;
            continue;
        }
        
        try {
            $codigo = Areas::guardarArea(
                $conexionPDO, 
                "ar_nombre, ar_posicion, institucion, year, ar_id", 
                [
                    trim($area['nombre']), 
                    intval($area['posicion']), 
                    $config['conf_id_institucion'], 
                    $_SESSION["bd"]
                ]
            );
            $guardadas++;
            $nombresGuardados[] = trim($area['nombre']);
        } catch(Exception $e) {
            $errores++;
            error_log("Error al guardar área: " . $e->getMessage());
        }
    }
    
    // Guardar historial de acciones
    include("../compartido/guardar-historial-acciones.php");
    
    if($guardadas > 0){
        $mensaje = "Se guardaron exitosamente {$guardadas} área(s)";
        if($errores > 0){
            $mensaje .= " y hubo {$errores} error(es)";
        }
        $mensaje .= ". Áreas creadas: " . implode(", ", $nombresGuardados);
        
        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'guardadas' => $guardadas,
            'errores' => $errores
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo guardar ninguna área. Verifica los datos e intenta nuevamente.'
        ]);
    }
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar las áreas: ' . $e->getMessage()
    ]);
}
exit();

