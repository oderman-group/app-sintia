<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademicaOptimizada.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");

header('Content-Type: application/json');

try {
    // Recibir filtros
    $cursos = isset($_POST['cursos']) ? $_POST['cursos'] : [];
    $grupos = isset($_POST['grupos']) ? $_POST['grupos'] : [];
    $docentes = isset($_POST['docentes']) ? $_POST['docentes'] : [];
    $periodos = isset($_POST['periodos']) ? $_POST['periodos'] : [];
    
    // Construir filtro SQL
    $filtro = "";
    
    // Filtro de cursos (múltiple)
    if (!empty($cursos) && is_array($cursos)) {
        $cursosStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($cursos), $conexion), $cursos));
        $filtro .= " AND car_curso IN ('{$cursosStr}')";
    }
    
    // Filtro de grupos (múltiple)
    if (!empty($grupos) && is_array($grupos)) {
        $gruposStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($grupos), $conexion), $grupos));
        $filtro .= " AND car_grupo IN ('{$gruposStr}')";
    }
    
    // Filtro de docentes (múltiple)
    if (!empty($docentes) && is_array($docentes)) {
        $docentesStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($docentes), $conexion), $docentes));
        $filtro .= " AND car_docente IN ('{$docentesStr}')";
    }
    
    // Filtro de periodos (múltiple)
    if (!empty($periodos) && is_array($periodos)) {
        $periodosStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($periodos), $conexion), $periodos));
        $filtro .= " AND car_periodo IN ('{$periodosStr}')";
    }
    
    // Campos a seleccionar
    $selectSql = [
        "car.car_id", "car.car_periodo", "car.car_curso", "car.car_ih", "car.car_permiso2",
        "car.car_indicador_automatico", "car.car_maximos_indicadores",
        "car.car_docente", "gra.gra_tipo", "am.mat_id",
        "car.car_maximas_calificaciones", "car.car_director_grupo", "uss.uss_nombre",
        "uss.uss_id", "uss.uss_nombre2", "uss.uss_apellido1", "uss.uss_apellido2", "gra.gra_id", "gra.gra_nombre",
        "gru.gru_nombre", "am.mat_nombre", "am.mat_valor", "car.car_grupo", "car.car_director_grupo", "car.car_activa",
        "car.id_nuevo AS id_nuevo_carga"
    ];
    
    // Consultar cargas con filtros (sin límite para mostrar todos los resultados filtrados)
    $consulta = CargaAcademicaOptimizada::listarCargasOptimizado($conexion, $config, "", $filtro, "car.car_id", "", "", array(), $selectSql);
    
    $cargas = [];
    if (!empty($consulta)) {
        while ($fila = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
            $cargas[] = $fila;
        }
    }
    
    // Preparar datos para el componente
    $data["data"] = $cargas;
    // NO establecer dataTotal para evitar que el componente intente cargar clases con rutas relativas
    // $data["dataTotal"] = count($cargas);
    $contReg = 1;
    
    // Variables necesarias para el componente (cargar ANTES del include)
    require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
    require_once(ROOT_PATH."/main-app/class/Modulos.php");
    require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
    require_once(ROOT_PATH."/main-app/class/Sysjobs.php");
    require_once(ROOT_PATH."/main-app/class/Boletin.php");
    
    // Definir todas las variables que el componente necesita
    $opcionSINO = array("" => "NO", "1" => "SI");
    $permisoReportesNotas = Modulos::validarSubRol(['DT0238']);
    $permisoedicion       = Modulos::validarSubRol(['DT0049', 'DT0148', 'DT0129']);
    $permisoEditar        = Modulos::validarSubRol(['DT0049']);
    $permisoEliminar      = Modulos::validarSubRol(['DT0148']);
    $permisoAutologin     = Modulos::validarSubRol(['DT0129']);
    $permisoHorarios      = Modulos::validarSubRol(['DT0041']);
    $permisoResumen       = Modulos::validarSubRol(['DT0111']);
    $permisoIndicadores   = Modulos::validarSubRol(['DT0034']);
    $permisoPlanilla      = Modulos::validarSubRol(['DT0239']);
    $permisoPlanillaNotas = Modulos::validarSubRol(['DT0237']);
    $permisoGenerarInforme = Modulos::validarSubRol(['DT0237']);
    $permisoComportamiento = Modulos::validarSubRol(['DT0343']);
    
    // Capturar el HTML generado por el componente
    ob_start();
    include(ROOT_PATH . "/main-app/class/componentes/result/cargas-tbody.php");
    $html = ob_get_clean();
    
    // LIMPIAR el HTML: Eliminar todo lo que no sean filas <tr>
    // Eliminar todo antes del primer <tr
    if (preg_match('/<tr/i', $html, $matches, PREG_OFFSET_CAPTURE)) {
        $html = substr($html, $matches[0][1]);
    }
    
    // Eliminar todo después del último </tr>
    if (preg_match('/<\/tr>(?!.*<\/tr>)/is', $html, $matches, PREG_OFFSET_CAPTURE)) {
        $html = substr($html, 0, $matches[0][1] + strlen($matches[0][0]));
    }
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'total' => count($cargas),
        'filtros' => [
            'cursos' => $cursos,
            'grupos' => $grupos,
            'docentes' => $docentes,
            'periodos' => $periodos
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al filtrar: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Error $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error fatal: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

