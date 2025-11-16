<?php
header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");

try {
    if (!Modulos::validarSubRol(['DT0062'])) {
        throw new Exception('No tienes permisos para ver detalles de cursos.');
    }

    $cursoId = isset($_POST['curso_id']) ? trim($_POST['curso_id']) : '';

    if ($cursoId === '') {
        throw new Exception('ID de curso no proporcionado.');
    }

    // Asegurar que el ID se trate como string (IDs alfanuméricos)
    $cursoId   = mysqli_real_escape_string($conexion, $cursoId);
    $instId    = (int)$config['conf_id_institucion'];
    $yearActual = (int)$_SESSION['bd'];

    $stats = [
        'matriculados'     => 0,
        'asistentes'       => 0,
        'cancelados'       => 0,
        'no_matriculados'  => 0,
        'inscritos'        => 0,
        'total'            => 0,
    ];

    // Contar estudiantes por estado de matrícula para el curso (grado)
    $sql = "SELECT mat_estado_matricula, COUNT(*) AS total
            FROM " . BD_ACADEMICA . ".academico_matriculas
            WHERE mat_grado = '$cursoId'
              AND mat_eliminado = 0
              AND institucion = $instId
              AND year = $yearActual
            GROUP BY mat_estado_matricula";

    $resultado = mysqli_query($conexion, $sql);

    if ($resultado) {
        while ($fila = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
            $estado = (int)$fila['mat_estado_matricula'];
            $total  = (int)$fila['total'];

            switch ($estado) {
                case MATRICULADO:
                    $stats['matriculados'] = $total;
                    break;
                case ASISTENTE:
                    $stats['asistentes'] = $total;
                    break;
                case CANCELADO:
                    $stats['cancelados'] = $total;
                    break;
                case ELIMINADO:
                    // No se muestra en stats de curso aquí
                    break;
                default:
                    // Usamos 4 como "no matriculado" y 5 como "inscrito" si están definidos en constantes
                    if (defined('NO_MATRICULADO') && $estado === NO_MATRICULADO) {
                        $stats['no_matriculados'] = $total;
                    } elseif (defined('INSCRIPCION') && $estado === INSCRIPCION) {
                        $stats['inscritos'] = $total;
                    }
                    break;
            }

            $stats['total'] += $total;
        }
        mysqli_free_result($resultado);
    }

    echo json_encode([
        'success' => true,
        'stats'   => $stats,
        'cursoId' => $cursoId,
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}


