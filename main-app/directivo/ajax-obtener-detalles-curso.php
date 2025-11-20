<?php
header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

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

    // Obtener información del curso
    $datosCurso = Grados::obtenerGrado($cursoId, $yearActual);
    $cursoInfo = [
        'curso_siguiente_id' => '',
        'curso_siguiente_nombre' => 'No definido',
        'curso_anterior_id' => '',
        'curso_anterior_nombre' => 'No definido',
        'nivel_educativo' => 'No definido',
        'numero_periodos' => 0,
    ];

    if ($datosCurso && !empty($datosCurso)) {
        // Obtener curso siguiente
        if (!empty($datosCurso['gra_grado_siguiente'])) {
            $cursoSiguiente = Grados::obtenerGrado($datosCurso['gra_grado_siguiente'], $yearActual);
            if ($cursoSiguiente && !empty($cursoSiguiente)) {
                $cursoInfo['curso_siguiente_id'] = $datosCurso['gra_grado_siguiente'];
                $cursoInfo['curso_siguiente_nombre'] = $cursoSiguiente['gra_nombre'] ?? 'No definido';
            }
        }

        // Obtener curso anterior
        if (!empty($datosCurso['gra_grado_anterior'])) {
            $cursoAnterior = Grados::obtenerGrado($datosCurso['gra_grado_anterior'], $yearActual);
            if ($cursoAnterior && !empty($cursoAnterior)) {
                $cursoInfo['curso_anterior_id'] = $datosCurso['gra_grado_anterior'];
                $cursoInfo['curso_anterior_nombre'] = $cursoAnterior['gra_nombre'] ?? 'No definido';
            }
        }

        // Mapear nivel educativo
        $nivel = !empty($datosCurso['gra_nivel']) ? (int)$datosCurso['gra_nivel'] : 0;
        switch ($nivel) {
            case 1:
                $cursoInfo['nivel_educativo'] = 'Educación Preescolar';
                break;
            case 2:
                $cursoInfo['nivel_educativo'] = 'Educación Básica Primaria';
                break;
            case 3:
                $cursoInfo['nivel_educativo'] = 'Educación Básica Secundaria';
                break;
            case 4:
                $cursoInfo['nivel_educativo'] = 'Educación Media';
                break;
            default:
                $cursoInfo['nivel_educativo'] = 'No definido';
                break;
        }
        
        // Obtener número de períodos
        $cursoInfo['numero_periodos'] = !empty($datosCurso['gra_periodos']) ? (int)$datosCurso['gra_periodos'] : 0;
    }

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
        'cursoInfo' => $cursoInfo,
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}


