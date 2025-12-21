<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Areas.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

// Asegurar que tenemos la conexión PDO
if(!isset($conexionPDO)){
    $conexionPDO = Conexion::newConnection('PDO');
}

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0179';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción.'
    ]);
    exit();
}

// Definir áreas y asignaturas según Ley 115 de 1994
$areasLey115 = [
    [
        'nombre' => 'Ciencias Naturales y Educación Ambiental',
        'posicion' => 1,
        'asignaturas' => [
            ['nombre' => 'Biología', 'siglas' => 'BIO'],
            ['nombre' => 'Química', 'siglas' => 'QUI'],
            ['nombre' => 'Física', 'siglas' => 'FIS'],
            ['nombre' => 'Educación Ambiental', 'siglas' => 'AMB']
        ]
    ],
    [
        'nombre' => 'Ciencias Sociales, Historia, Geografía, Constitución Política y Democracia',
        'posicion' => 2,
        'asignaturas' => [
            ['nombre' => 'Ciencias Sociales', 'siglas' => 'SOC'],
            ['nombre' => 'Historia', 'siglas' => 'HIS'],
            ['nombre' => 'Geografía', 'siglas' => 'GEO'],
            ['nombre' => 'Constitución Política y Democracia', 'siglas' => 'CPD']
        ]
    ],
    [
        'nombre' => 'Educación Artística',
        'posicion' => 3,
        'asignaturas' => [
            ['nombre' => 'Artes Plásticas', 'siglas' => 'ART'],
            ['nombre' => 'Música', 'siglas' => 'MUS']
        ]
    ],
    [
        'nombre' => 'Educación Ética y en Valores Humanos',
        'posicion' => 4,
        'asignaturas' => [
            ['nombre' => 'Ética y Valores Humanos', 'siglas' => 'ETI']
        ]
    ],
    [
        'nombre' => 'Educación Física, Recreación y Deportes',
        'posicion' => 5,
        'asignaturas' => [
            ['nombre' => 'Educación Física', 'siglas' => 'EFI'],
            ['nombre' => 'Recreación y Deportes', 'siglas' => 'DEP']
        ]
    ],
    [
        'nombre' => 'Educación Religiosa',
        'posicion' => 6,
        'asignaturas' => [
            ['nombre' => 'Educación Religiosa', 'siglas' => 'REL']
        ]
    ],
    [
        'nombre' => 'Humanidades (Lengua Castellana e Idiomas Extranjeros)',
        'posicion' => 7,
        'asignaturas' => [
            ['nombre' => 'Lengua Castellana', 'siglas' => 'LEN'],
            ['nombre' => 'Idiomas Extranjeros', 'siglas' => 'IDI']
        ]
    ],
    [
        'nombre' => 'Matemáticas',
        'posicion' => 8,
        'asignaturas' => [
            ['nombre' => 'Matemáticas', 'siglas' => 'MAT'],
            ['nombre' => 'Álgebra', 'siglas' => 'ALG'],
            ['nombre' => 'Trigonometría', 'siglas' => 'TRI']
        ]
    ],
    [
        'nombre' => 'Tecnología e Informática',
        'posicion' => 9,
        'asignaturas' => [
            ['nombre' => 'Tecnología', 'siglas' => 'TEC'],
            ['nombre' => 'Informática', 'siglas' => 'INF']
        ]
    ],
    [
        'nombre' => 'Estudios Afrocolombianos',
        'posicion' => 10,
        'asignaturas' => [
            ['nombre' => 'Estudios Afrocolombianos', 'siglas' => 'AFR']
        ]
    ],
    [
        'nombre' => 'Ciencias Económicas y Políticas',
        'posicion' => 11,
        'asignaturas' => [
            ['nombre' => 'Ciencias Económicas', 'siglas' => 'ECO'],
            ['nombre' => 'Ciencias Políticas', 'siglas' => 'POL']
        ],
        'solo_media' => true // Solo para educación media
    ],
    [
        'nombre' => 'Filosofía',
        'posicion' => 12,
        'asignaturas' => [
            ['nombre' => 'Filosofía', 'siglas' => 'FIL']
        ],
        'solo_media' => true // Solo para educación media
    ]
];

try {
    $areasGeneradas = 0;
    $asignaturasGeneradas = 0;
    $errores = 0;
    $nombresAreas = [];
    $nombresAsignaturas = [];
    
    // Recorrer las áreas definidas
    foreach($areasLey115 as $areaData){
        try {
            // Verificar si el área ya existe
            $sqlVerificarArea = "SELECT ar_id FROM ".BD_ACADEMICA.".academico_areas WHERE ar_nombre=? AND institucion=? AND year=?";
            $stmtVerificarArea = $conexionPDO->prepare($sqlVerificarArea);
            $stmtVerificarArea->execute([$areaData['nombre'], $config['conf_id_institucion'], $_SESSION["bd"]]);
            $areaExistente = $stmtVerificarArea->fetch(PDO::FETCH_ASSOC);
            
            if($areaExistente){
                // El área ya existe, usar su ID para las asignaturas
                $areaId = $areaExistente['ar_id'];
            } else {
                // Crear el área
                $areaId = Areas::guardarArea(
                    $conexionPDO,
                    "ar_nombre, ar_posicion, institucion, year, ar_id",
                    [$areaData['nombre'], $areaData['posicion'], $config['conf_id_institucion'], $_SESSION["bd"]]
                );
                $areasGeneradas++;
                $nombresAreas[] = $areaData['nombre'];
            }
            
            // Crear las asignaturas del área
            foreach($areaData['asignaturas'] as $asignaturaData){
                try {
                    // Verificar si la asignatura ya existe
                    $sqlVerificarAsignatura = "SELECT mat_id FROM ".BD_ACADEMICA.".academico_materias WHERE mat_nombre=? AND mat_area=? AND institucion=? AND year=?";
                    $stmtVerificarAsignatura = $conexionPDO->prepare($sqlVerificarAsignatura);
                    $stmtVerificarAsignatura->execute([$asignaturaData['nombre'], $areaId, $config['conf_id_institucion'], $_SESSION["bd"]]);
                    $asignaturaExistente = $stmtVerificarAsignatura->fetch(PDO::FETCH_ASSOC);
                    
                    if($asignaturaExistente){
                        // La asignatura ya existe, saltar
                        continue;
                    }
                    
                    // Crear la asignatura
                    $POST = [
                        'nombreM' => $asignaturaData['nombre'],
                        'siglasM' => $asignaturaData['siglas'],
                        'areaM' => $areaId,
                        'porcenAsigna' => 100,
                        'sumarPromedio' => SI
                    ];
                    
                    // Obtener conexión mysqli si no está disponible
                    if(!isset($conexion)){
                        require_once(ROOT_PATH."/main-app/class/Conexion.php");
                        $conexion = Conexion::newConnection('MySQLi');
                    }
                    
                    $asignaturaId = Asignaturas::guardarAsignatura($conexion, $conexionPDO, $config, $POST);
                    $asignaturasGeneradas++;
                    $nombresAsignaturas[] = $asignaturaData['nombre'];
                    
                } catch(Exception $e) {
                    $errores++;
                    error_log("Error al generar asignatura {$asignaturaData['nombre']}: " . $e->getMessage());
                }
            }
            
        } catch(Exception $e) {
            $errores++;
            error_log("Error al generar área {$areaData['nombre']}: " . $e->getMessage());
        }
    }
    
    // Guardar historial de acciones
    include("../compartido/guardar-historial-acciones.php");
    
    if($areasGeneradas > 0 || $asignaturasGeneradas > 0){
        $mensaje = "Se generaron exitosamente <strong>{$areasGeneradas} áreas</strong> y <strong>{$asignaturasGeneradas} asignaturas</strong>";
        if($errores > 0){
            $mensaje .= " y hubo {$errores} error(es)";
        }
        $mensaje .= ".<br><br>";
        
        if($areasGeneradas > 0){
            $mensaje .= "<strong>Áreas creadas:</strong> " . implode(", ", $nombresAreas) . "<br>";
        }
        
        if($asignaturasGeneradas > 0){
            $mensaje .= "<strong>Asignaturas creadas:</strong> " . implode(", ", $nombresAsignaturas);
        }
        
        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'areas_generadas' => $areasGeneradas,
            'asignaturas_generadas' => $asignaturasGeneradas,
            'errores' => $errores
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo generar ninguna área o asignatura. Es posible que todas las áreas y asignaturas según Ley 115 ya existan.'
        ]);
    }
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al generar las áreas y asignaturas: ' . $e->getMessage()
    ]);
}
exit();

