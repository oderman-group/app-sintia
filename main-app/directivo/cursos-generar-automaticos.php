<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

// Asegurar que tenemos la conexión PDO
if(!isset($conexionPDO)){
    $conexionPDO = Conexion::newConnection('PDO');
}

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0188';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción.'
    ]);
    exit();
}

// Validar que se hayan recibido los niveles
if(empty($_POST['niveles'])){
    echo json_encode([
        'success' => false,
        'message' => 'Debes seleccionar al menos un nivel.'
    ]);
    exit();
}

$niveles = $_POST['niveles'];

// Definir los cursos por nivel
$cursosDefinicion = [
    'preescolar' => [
        ['codigo' => 'PARVULOS', 'nombre' => 'Párvulos', 'vocal' => 'P1'],
        ['codigo' => 'PREJARDIN', 'nombre' => 'Prejardín', 'vocal' => 'P2'],
        ['codigo' => 'JARDIN', 'nombre' => 'Jardín', 'vocal' => 'P3'],
        ['codigo' => 'TRANSICION', 'nombre' => 'Transición', 'vocal' => 'P4']
    ],
    'primaria' => [
        ['codigo' => '1', 'nombre' => 'Primero', 'vocal' => 'A'],
        ['codigo' => '2', 'nombre' => 'Segundo', 'vocal' => 'B'],
        ['codigo' => '3', 'nombre' => 'Tercero', 'vocal' => 'C'],
        ['codigo' => '4', 'nombre' => 'Cuarto', 'vocal' => 'D'],
        ['codigo' => '5', 'nombre' => 'Quinto', 'vocal' => 'E']
    ],
    'secundaria' => [
        ['codigo' => '6', 'nombre' => 'Sexto', 'vocal' => 'F'],
        ['codigo' => '7', 'nombre' => 'Séptimo', 'vocal' => 'G'],
        ['codigo' => '8', 'nombre' => 'Octavo', 'vocal' => 'H'],
        ['codigo' => '9', 'nombre' => 'Noveno', 'vocal' => 'I']
    ],
    'media' => [
        ['codigo' => '10', 'nombre' => 'Décimo', 'vocal' => 'J'],
        ['codigo' => '11', 'nombre' => 'Undécimo', 'vocal' => 'K']
    ]
];

try {
    $cursosGenerados = 0;
    $errores = 0;
    $nombresGenerados = [];
    
    // Recorrer los niveles seleccionados
    foreach($niveles as $nivel){
        if(!isset($cursosDefinicion[$nivel])){
            continue;
        }
        
        $cursos = $cursosDefinicion[$nivel];
        
        // Crear cada curso del nivel
        foreach($cursos as $curso){
            try {
                // Verificar si el curso ya existe usando una consulta directa
                $sqlVerificar = "SELECT gra_id FROM ".BD_ACADEMICA.".academico_grados WHERE gra_codigo=? AND institucion=? AND year=?";
                $stmtVerificar = $conexionPDO->prepare($sqlVerificar);
                $stmtVerificar->execute([$curso['codigo'], $config['conf_id_institucion'], $_SESSION["bd"]]);
                $cursoExistente = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
                
                if($cursoExistente){
                    // El curso ya existe, saltar
                    continue;
                }
                
                // Preparar datos del curso (igual que en cursos-guardar.php)
                $parametros = [
                    $curso['codigo'],                           // gra_codigo
                    $curso['nombre'],                           // gra_nombre
                    '1',                                        // gra_formato_boletin (por defecto 1)
                    '0',                                        // gra_valor_matricula
                    '0',                                        // gra_valor_pension
                    1,                                          // gra_estado (activo)
                    1,                                          // gra_grado_siguiente (por defecto 1)
                    $config['conf_periodos_maximos'],          // gra_periodos
                    GRADO_GRUPAL,                               // gra_tipo (grupal por defecto)
                    $config['conf_id_institucion'],             // institucion
                    $_SESSION["bd"],                            // year
                    '',                                         // gra_overall_description
                    '',                                         // gra_course_content
                    '0',                                        // gra_price
                    '0',                                        // gra_minimum_quota
                    '0',                                        // gra_maximum_quota
                    '0',                                        // gra_duration_hours
                    0,                                          // gra_auto_enrollment
                    0                                           // gra_active
                    // NOTA: El método guardarCurso genera automáticamente el gra_id y lo agrega al final
                ];
                
                // Guardar curso (mismo formato que cursos-guardar.php línea 45)
                $resultado = Grados::guardarCurso(
                    $conexionPDO, 
                    "gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, gra_estado, gra_grado_siguiente, gra_periodos, gra_tipo, institucion, year, gra_overall_description, gra_course_content, gra_price, gra_minimum_quota, gra_maximum_quota, gra_duration_hours, gra_auto_enrollment, gra_active, gra_id",
                    $parametros
                );
                
                $cursosGenerados++;
                $nombresGenerados[] = $curso['nombre'];
                
            } catch(Exception $e) {
                $errores++;
                error_log("Error al generar curso {$curso['nombre']}: " . $e->getMessage());
            }
        }
    }
    
    // Guardar historial de acciones
    include("../compartido/guardar-historial-acciones.php");
    
    if($cursosGenerados > 0){
        $mensaje = "Se generaron exitosamente <strong>{$cursosGenerados} cursos</strong>";
        if($errores > 0){
            $mensaje .= " y hubo {$errores} error(es)";
        }
        $mensaje .= ".<br><br>Cursos creados: <strong>" . implode(", ", $nombresGenerados) . "</strong>";
        
        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'generados' => $cursosGenerados,
            'errores' => $errores
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo generar ningún curso. Es posible que los cursos seleccionados ya existan.'
        ]);
    }
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al generar los cursos: ' . $e->getMessage()
    ]);
}
exit();

