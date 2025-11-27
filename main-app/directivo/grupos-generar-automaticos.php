<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

// Asegurar que tenemos las conexiones necesarias
if(!isset($conexionPDO)){
    $conexionPDO = Conexion::newConnection('PDO');
}

// Asegurar que tenemos la conexión mysqli
if(!isset($conexion)){
    require_once(ROOT_PATH."/main-app/modelo/conexion.php");
}

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0199';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción.'
    ]);
    exit();
}

// Validar que se haya recibido la cantidad
if(empty($_POST['cantidad']) || !is_numeric($_POST['cantidad'])){
    echo json_encode([
        'success' => false,
        'message' => 'Debes indicar cuántos grupos deseas generar.'
    ]);
    exit();
}

$cantidad = intval($_POST['cantidad']);

// Validar que la cantidad esté entre 1 y 10
if($cantidad < 1 || $cantidad > 10){
    echo json_encode([
        'success' => false,
        'message' => 'La cantidad de grupos debe estar entre 1 y 10.'
    ]);
    exit();
}

// Definir los grupos con letras A-J (letras solo para el nombre)
$letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
$gruposDefinicion = [];

for($i = 0; $i < $cantidad; $i++){
    $gruposDefinicion[] = [
        'nombre' => $letras[$i] // Solo nombre, el código se genera automáticamente
    ];
}

try {
    $gruposGenerados = 0;
    $errores = 0;
    $gruposExistentes = 0;
    $nombresGenerados = [];
    $nombresExistentes = [];
    $erroresDetalle = [];
    
    // Debug: registrar qué se va a crear
    error_log("=== INICIO GENERACIÓN DE GRUPOS ===");
    error_log("Cantidad solicitada: " . $cantidad);
    error_log("Grupos a intentar crear: " . json_encode($gruposDefinicion));
    error_log("Institución: " . $config['conf_id_institucion']);
    error_log("Year: " . $_SESSION["bd"]);
    
    foreach($gruposDefinicion as $index => $grupo){
        error_log("--- Procesando grupo #{$index}: {$grupo['nombre']} ---");
        try {
            // Verificar si el grupo ya existe por NOMBRE (no por código, porque cada código es único)
            $sqlVerificar = "SELECT gru_id FROM ".BD_ACADEMICA.".academico_grupos WHERE gru_nombre=? AND institucion=? AND year=?";
            $stmtVerificar = $conexionPDO->prepare($sqlVerificar);
            $stmtVerificar->execute([$grupo['nombre'], $config['conf_id_institucion'], $_SESSION["bd"]]);
            $grupoExistente = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if($grupoExistente){
                // El grupo ya existe, registrarlo y continuar con el siguiente
                error_log("Grupo {$grupo['nombre']} ya existe, omitiendo...");
                $gruposExistentes++;
                $nombresExistentes[] = $grupo['nombre'];
                continue; // Continuar con el siguiente grupo
            }
            
            error_log("Grupo {$grupo['nombre']} no existe, procediendo a crear...");
            
            // Generar código único basado en timestamp
            $codigoUnico = rand(10000,999999) . $index;
            
            // Preparar datos para guardar usando el mismo método que grupos-guardar.php
            $datosGrupo = [
                'codigoG' => $codigoUnico,        // Código único generado automáticamente
                'nombreG' => $grupo['nombre']      // Nombre es la letra (A, B, C, etc.)
            ];
            
            error_log("Datos a guardar: " . json_encode($datosGrupo));
            
            // Guardar grupo
            $codigo = Grupos::guardarGrupos($conexion, $conexionPDO, $config, $datosGrupo);
            
            error_log("Grupo {$grupo['nombre']} creado exitosamente con ID: {$codigo}");
            
            $gruposGenerados++;
            $nombresGenerados[] = $grupo['nombre'];
            
            // Pequeña pausa para asegurar códigos únicos
            usleep(1000); // 1 milisegundo
            
        } catch(Exception $e) {
            $errores++;
            $erroresDetalle[] = "Grupo {$grupo['nombre']}: " . $e->getMessage();
            error_log("Error al generar grupo {$grupo['nombre']}: " . $e->getMessage());
            // Continuar con el siguiente grupo aunque haya error
            continue;
        }
    }
    
    // Guardar historial de acciones
    include("../compartido/guardar-historial-acciones.php");
    
    if($gruposGenerados > 0 || $gruposExistentes > 0){
        $mensaje = "";
        
        // Mensaje de grupos generados
        if($gruposGenerados > 0){
            $mensaje .= "Se generaron exitosamente <strong>{$gruposGenerados} grupo(s) nuevo(s)</strong>: <strong>" . implode(", ", $nombresGenerados) . "</strong>";
        }
        
        // Mensaje de grupos existentes
        if($gruposExistentes > 0){
            if($gruposGenerados > 0){
                $mensaje .= "<br><br>";
            }
            $mensaje .= "Se omitieron <strong>{$gruposExistentes} grupo(s)</strong> que ya existían: <strong>" . implode(", ", $nombresExistentes) . "</strong>";
        }
        
        // Mensaje de errores si los hay
        if($errores > 0){
            $mensaje .= "<br><br><span class='text-warning'>⚠️ Hubo {$errores} error(es) al intentar crear algunos grupos.</span>";
            if(!empty($erroresDetalle)){
                $mensaje .= "<br><small>" . implode("<br>", $erroresDetalle) . "</small>";
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'generados' => $gruposGenerados,
            'existentes' => $gruposExistentes,
            'errores' => $errores
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo generar ningún grupo. Todos los grupos ya existen o hubo errores: ' . implode(", ", $erroresDetalle)
        ]);
    }
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al generar los grupos: ' . $e->getMessage()
    ]);
}
exit();

