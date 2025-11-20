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

// Obtener nive_id de la tabla academico_niveles
function obtenerNiveId($nivelNombre, $conexionPDO) {
    $sql = "SELECT nive_id FROM " . BD_ACADEMICA . ".academico_niveles 
            WHERE LOWER(nive_nombre) LIKE ? OR LOWER(nive_nombre2) LIKE ? 
            LIMIT 1";
    $busqueda = '%' . strtolower($nivelNombre) . '%';
    $stmt = $conexionPDO->prepare($sql);
    $stmt->execute([$busqueda, $busqueda]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    return !empty($fila['nive_id']) ? (int)$fila['nive_id'] : null;
}

// Mapeo de niveles educativos a nombres en academico_niveles
$mapaNiveles = [
    'preescolar' => ['Preescolar', 'Preescolar', 1], // nombre, nombre alternativo, nivel numérico fallback
    'primaria' => ['Básica Primaria', 'Primaria', 2],
    'secundaria' => ['Básica Secundaria', 'Secundaria', 3],
    'media' => ['Media', 'Media', 4]
];

// Definir los cursos por nivel con información completa
// Las vocales se asignarán automáticamente de forma consecutiva (a-z)
$cursosDefinicion = [
    'preescolar' => [
        ['codigo' => 'PARVULOS', 'nombre' => 'Párvulos', 'numero' => 12, 'siguiente_numero' => 13],
        ['codigo' => 'PREJARDIN', 'nombre' => 'Prejardín', 'numero' => 13, 'siguiente_numero' => 14],
        ['codigo' => 'JARDIN', 'nombre' => 'Jardín', 'numero' => 14, 'siguiente_numero' => 15],
        ['codigo' => 'TRANSICION', 'nombre' => 'Transición', 'numero' => 15, 'siguiente_numero' => 1]
    ],
    'primaria' => [
        ['codigo' => '1', 'nombre' => 'Primero', 'numero' => 1, 'siguiente_numero' => 2],
        ['codigo' => '2', 'nombre' => 'Segundo', 'numero' => 2, 'siguiente_numero' => 3],
        ['codigo' => '3', 'nombre' => 'Tercero', 'numero' => 3, 'siguiente_numero' => 4],
        ['codigo' => '4', 'nombre' => 'Cuarto', 'numero' => 4, 'siguiente_numero' => 5],
        ['codigo' => '5', 'nombre' => 'Quinto', 'numero' => 5, 'siguiente_numero' => 6]
    ],
    'secundaria' => [
        ['codigo' => '6', 'nombre' => 'Sexto', 'numero' => 6, 'siguiente_numero' => 7],
        ['codigo' => '7', 'nombre' => 'Séptimo', 'numero' => 7, 'siguiente_numero' => 8],
        ['codigo' => '8', 'nombre' => 'Octavo', 'numero' => 8, 'siguiente_numero' => 9],
        ['codigo' => '9', 'nombre' => 'Noveno', 'numero' => 9, 'siguiente_numero' => 10]
    ],
    'media' => [
        ['codigo' => '10', 'nombre' => 'Décimo', 'numero' => 10, 'siguiente_numero' => 11],
        ['codigo' => '11', 'nombre' => 'Undécimo', 'numero' => 11, 'siguiente_numero' => 0] // 0 = no hay siguiente
    ]
];

try {
    $cursosGenerados = 0;
    $errores = 0;
    $nombresGenerados = [];
    
    // Primero, crear un mapa de todos los cursos que se van a generar para calcular gra_grado_siguiente
    // y asignar vocales consecutivas (a-z) en el orden correcto: Preescolar → Primaria → Secundaria → Media
    $todosLosCursos = [];
    $contadorVocal = 0; // Contador para asignar vocales consecutivas (a=0, b=1, c=2, etc.)
    $vocales = range('a', 'z'); // Array con todas las letras del alfabeto
    
    // Ordenar los niveles en el orden correcto para asignar vocales consecutivas
    $ordenNiveles = ['preescolar', 'primaria', 'secundaria', 'media'];
    $nivelesOrdenados = [];
    foreach($ordenNiveles as $nivelOrdenado){
        if(in_array($nivelOrdenado, $niveles)){
            $nivelesOrdenados[] = $nivelOrdenado;
        }
    }
    
    // Si hay niveles que no están en el orden estándar, agregarlos al final
    foreach($niveles as $nivel){
        if(!in_array($nivel, $nivelesOrdenados)){
            $nivelesOrdenados[] = $nivel;
        }
    }
    
    foreach($nivelesOrdenados as $nivel){
        if(!isset($cursosDefinicion[$nivel])){
            continue;
        }
        $cursos = $cursosDefinicion[$nivel];
        foreach($cursos as $curso){
            // Asignar vocal consecutiva
            $curso['vocal'] = isset($vocales[$contadorVocal]) ? strtoupper($vocales[$contadorVocal]) : null;
            $contadorVocal++;
            $todosLosCursos[$curso['numero']] = $curso;
        }
    }
    
    // Obtener nive_id para cada nivel educativo
    $niveIds = [];
    foreach($mapaNiveles as $key => $info){
        $niveId = obtenerNiveId($info[0], $conexionPDO);
        if(empty($niveId)){
            $niveId = obtenerNiveId($info[1], $conexionPDO); // Intentar con nombre alternativo
        }
        $niveIds[$key] = !empty($niveId) ? $niveId : $info[2]; // Usar fallback si no se encuentra
    }
    
    // Recorrer los niveles seleccionados
    foreach($niveles as $nivel){
        if(!isset($cursosDefinicion[$nivel])){
            continue;
        }
        
        $cursos = $cursosDefinicion[$nivel];
        $niveId = isset($niveIds[$nivel]) ? $niveIds[$nivel] : null;
        
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
                
                // Calcular gra_grado_siguiente: buscar el gra_id del curso siguiente por su código
                $graGradoSiguiente = null;
                if($curso['siguiente_numero'] > 0 && isset($todosLosCursos[$curso['siguiente_numero']])){
                    $cursoSiguiente = $todosLosCursos[$curso['siguiente_numero']];
                    // Buscar si el curso siguiente ya existe o se creará
                    $sqlSiguiente = "SELECT gra_id FROM ".BD_ACADEMICA.".academico_grados WHERE gra_codigo=? AND institucion=? AND year=?";
                    $stmtSiguiente = $conexionPDO->prepare($sqlSiguiente);
                    $stmtSiguiente->execute([$cursoSiguiente['codigo'], $config['conf_id_institucion'], $_SESSION["bd"]]);
                    $siguienteExistente = $stmtSiguiente->fetch(PDO::FETCH_ASSOC);
                    // Si no existe, se creará después, así que lo dejamos null por ahora
                    // Se actualizará en una segunda pasada
                }
                
                // Calcular gra_grado_anterior: buscar el curso anterior (lógica inversa de gra_grado_siguiente)
                $graGradoAnterior = null;
                foreach($todosLosCursos as $num => $c){
                    if($c['siguiente_numero'] == $curso['numero']){
                        // Este curso es el anterior (su siguiente_numero apunta al curso actual)
                        $sqlAnterior = "SELECT gra_id FROM ".BD_ACADEMICA.".academico_grados WHERE gra_codigo=? AND institucion=? AND year=?";
                        $stmtAnterior = $conexionPDO->prepare($sqlAnterior);
                        $stmtAnterior->execute([$c['codigo'], $config['conf_id_institucion'], $_SESSION["bd"]]);
                        $anteriorExistente = $stmtAnterior->fetch(PDO::FETCH_ASSOC);
                        if($anteriorExistente){
                            $graGradoAnterior = $anteriorExistente['gra_id'];
                        }
                        // Si no existe, se actualizará después en la segunda pasada
                        break;
                    }
                }
                
                // Preparar datos del curso
                $parametros = [
                    $curso['codigo'],                           // gra_codigo
                    $curso['nombre'],                           // gra_nombre
                    '1',                                        // gra_formato_boletin (por defecto 1)
                    '0',                                        // gra_valor_matricula
                    '0',                                        // gra_valor_pension
                    1,                                          // gra_estado (activo)
                    $graGradoSiguiente,                        // gra_grado_siguiente (se actualizará después)
                    isset($curso['vocal']) ? $curso['vocal'] : null, // gra_vocal (asignada consecutivamente a-z)
                    $niveId,                                    // gra_nivel (nive_id de academico_niveles)
                    $graGradoAnterior,                          // gra_grado_anterior (se actualizará después)
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
                
                // Guardar curso
                $graIdGenerado = Grados::guardarCurso(
                    $conexionPDO, 
                    "gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, gra_estado, gra_grado_siguiente, gra_vocal, gra_nivel, gra_grado_anterior, gra_periodos, gra_tipo, institucion, year, gra_overall_description, gra_course_content, gra_price, gra_minimum_quota, gra_maximum_quota, gra_duration_hours, gra_auto_enrollment, gra_active, gra_id",
                    $parametros
                );
                
                // Guardar el gra_id generado en el curso para referencia
                $curso['gra_id'] = $graIdGenerado;
                
                $cursosGenerados++;
                $nombresGenerados[] = $curso['nombre'];
                
            } catch(Exception $e) {
                $errores++;
                error_log("Error al generar curso {$curso['nombre']}: " . $e->getMessage());
            }
        }
    }
    
    // Segunda pasada: actualizar gra_grado_siguiente y gra_grado_anterior con los gra_id reales
    foreach($niveles as $nivel){
        if(!isset($cursosDefinicion[$nivel])){
            continue;
        }
        $cursos = $cursosDefinicion[$nivel];
        foreach($cursos as $curso){
            try {
                // Obtener el gra_id del curso actual
                $sqlActual = "SELECT gra_id FROM ".BD_ACADEMICA.".academico_grados WHERE gra_codigo=? AND institucion=? AND year=?";
                $stmtActual = $conexionPDO->prepare($sqlActual);
                $stmtActual->execute([$curso['codigo'], $config['conf_id_institucion'], $_SESSION["bd"]]);
                $cursoActual = $stmtActual->fetch(PDO::FETCH_ASSOC);
                
                if(!$cursoActual){
                    continue;
                }
                
                $graIdActual = $cursoActual['gra_id'];
                
                // Actualizar gra_grado_siguiente
                if($curso['siguiente_numero'] > 0 && isset($todosLosCursos[$curso['siguiente_numero']])){
                    $cursoSiguiente = $todosLosCursos[$curso['siguiente_numero']];
                    $sqlSiguiente = "SELECT gra_id FROM ".BD_ACADEMICA.".academico_grados WHERE gra_codigo=? AND institucion=? AND year=?";
                    $stmtSiguiente = $conexionPDO->prepare($sqlSiguiente);
                    $stmtSiguiente->execute([$cursoSiguiente['codigo'], $config['conf_id_institucion'], $_SESSION["bd"]]);
                    $siguiente = $stmtSiguiente->fetch(PDO::FETCH_ASSOC);
                    
                    if($siguiente){
                        $sqlUpdate = "UPDATE ".BD_ACADEMICA.".academico_grados SET gra_grado_siguiente=? WHERE gra_id=? AND institucion=? AND year=?";
                        $stmtUpdate = $conexionPDO->prepare($sqlUpdate);
                        $stmtUpdate->execute([$siguiente['gra_id'], $graIdActual, $config['conf_id_institucion'], $_SESSION["bd"]]);
                    }
                }
                
                // Actualizar gra_grado_anterior (lógica inversa: si el siguiente de A es B, entonces el anterior de B es A)
                foreach($todosLosCursos as $num => $c){
                    if($c['siguiente_numero'] == $curso['numero']){
                        // Este curso es el anterior (su siguiente_numero apunta al curso actual)
                        $sqlAnterior = "SELECT gra_id FROM ".BD_ACADEMICA.".academico_grados WHERE gra_codigo=? AND institucion=? AND year=?";
                        $stmtAnterior = $conexionPDO->prepare($sqlAnterior);
                        $stmtAnterior->execute([$c['codigo'], $config['conf_id_institucion'], $_SESSION["bd"]]);
                        $anterior = $stmtAnterior->fetch(PDO::FETCH_ASSOC);
                        
                        if($anterior){
                            $sqlUpdate = "UPDATE ".BD_ACADEMICA.".academico_grados SET gra_grado_anterior=? WHERE gra_id=? AND institucion=? AND year=?";
                            $stmtUpdate = $conexionPDO->prepare($sqlUpdate);
                            $stmtUpdate->execute([$anterior['gra_id'], $graIdActual, $config['conf_id_institucion'], $_SESSION["bd"]]);
                        }
                        break;
                    }
                }
                
            } catch(Exception $e) {
                error_log("Error al actualizar relaciones del curso {$curso['nombre']}: " . $e->getMessage());
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

