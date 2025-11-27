<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");
require_once(ROOT_PATH . "/main-app/class/Grupos.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");

function jsonResponse($data) {
    while (ob_get_level()) { ob_end_clean(); }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cargaId = $_POST['carga_id'] ?? null;
        
        if (empty($cargaId)) {
            jsonResponse(['success' => false, 'message' => 'ID de carga es obligatorio.']);
        }
        
        // Obtener datos de la carga
        $datosCarga = CargaAcademica::traerCargaMateriaPorID($config, $cargaId);
        
        if (!$datosCarga) {
            jsonResponse(['success' => false, 'message' => 'Carga no encontrada.']);
        }
        
        // Obtener listas para los selects
        $docentes = [];
        $docentesConsulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=2 ORDER BY uss_nombre");
        while($docente = mysqli_fetch_array($docentesConsulta, MYSQLI_BOTH)){
            $docentes[] = [
                'id' => $docente['uss_id'],
                'nombre' => $docente['uss_usuario']." - ".UsuariosPadre::nombreCompletoDelUsuario($docente),
                'bloqueado' => $docente['uss_bloqueado']
            ];
        }
        
        $grados = [];
        $gradosConsulta = Grados::traerGradosInstitucion($config);
        while($grado = mysqli_fetch_array($gradosConsulta, MYSQLI_BOTH)){
            $grados[] = [
                'id' => $grado['gra_id'],
                'nombre' => $grado['gra_id'].". ".strtoupper($grado['gra_nombre']),
                'estado' => $grado['gra_estado']
            ];
        }
        
        $grupos = [];
        $gruposConsulta = Grupos::listarGrupos();
        while($grupo = mysqli_fetch_array($gruposConsulta, MYSQLI_BOTH)){
            $grupos[] = [
                'id' => $grupo['gru_id'],
                'nombre' => $grupo['gru_id'].". ".strtoupper($grupo['gru_nombre'])
            ];
        }
        
        $asignaturas = [];
        $asignaturasConsulta = Asignaturas::consultarTodasAsignaturas($conexion, $config);
        while($asignatura = mysqli_fetch_array($asignaturasConsulta, MYSQLI_BOTH)){
            $asignaturas[] = [
                'id' => $asignatura['mat_id'],
                'nombre' => $asignatura['mat_id'].". ".strtoupper($asignatura['mat_nombre']." (".$asignatura['ar_nombre'].")")
            ];
        }
        
        jsonResponse([
            'success' => true, 
            'carga' => $datosCarga,
            'listas' => [
                'docentes' => $docentes,
                'grados' => $grados,
                'grupos' => $grupos,
                'asignaturas' => $asignaturas,
                'periodos' => intval($config[19])
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Error al obtener datos de carga: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>

