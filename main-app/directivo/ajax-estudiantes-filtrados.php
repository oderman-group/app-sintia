<?php
include("session.php");
require_once("../class/Estudiantes.php");
require_once("../class/Grados.php");
require_once("../class/Grupos.php");
require_once("../class/Usuarios.php");
require_once("../class/UsuariosPadre.php");
require_once("../class/servicios/UsuarioServicios.php");

// Validar que sea una petición AJAX
if (!isset($_POST['acudiente_id'])) {
    echo json_encode(['error' => 'Parámetros inválidos']);
    exit();
}

$acudienteId = $_POST['acudiente_id'];
$busqueda = !empty($_POST['busqueda']) ? trim($_POST['busqueda']) : '';
$filtroGrado = !empty($_POST['grado']) ? $_POST['grado'] : '';
$filtroGrupo = !empty($_POST['grupo']) ? $_POST['grupo'] : '';

// Obtener estudiantes asociados al acudiente actual
$parametros = array(
    "upe_id_usuario" => $acudienteId,
    "institucion" => $config['conf_id_institucion'],
    "year" => $_SESSION["bd"]
);
$listaAcudidos = UsuarioServicios::listarUsuariosEstudiante($parametros);
$estudiantesAsociados = [];
if (!empty($listaAcudidos)) {
    foreach($listaAcudidos as $acudido){
        $estudiantesAsociados[] = $acudido["upe_id_estudiante"];
    }
}

// Construir filtro para la consulta
$filtro = '';
if ($filtroGrado) {
    $filtro .= " AND mat_grado='" . mysqli_real_escape_string($conexion, $filtroGrado) . "'";
}
if ($filtroGrupo) {
    $filtro .= " AND mat_grupo='" . mysqli_real_escape_string($conexion, $filtroGrupo) . "'";
}

// Obtener estudiantes según filtros
$listaEstudiantes = Estudiantes::estudiantesMatriculados($filtro, $_SESSION["bd"]);

// Preparar datos de estudiantes
$estudiantesConDatos = [];
while($estudiante = mysqli_fetch_array($listaEstudiantes, MYSQLI_BOTH)){
    $estudianteId = $estudiante['mat_id'];
    $estaAsociado = in_array($estudianteId, $estudiantesAsociados);
    
    // Aplicar filtro de búsqueda si existe
    if ($busqueda) {
        $busquedaLower = strtolower($busqueda);
        $nombreCompleto = strtolower(Estudiantes::NombreCompletoDelEstudiante($estudiante));
        $primerNombre = !empty($estudiante['mat_nombres']) ? strtolower(trim($estudiante['mat_nombres'])) : '';
        $segundoNombre = !empty($estudiante['mat_nombre2']) ? strtolower(trim($estudiante['mat_nombre2'])) : '';
        $primerApellido = !empty($estudiante['mat_primer_apellido']) ? strtolower(trim($estudiante['mat_primer_apellido'])) : '';
        $segundoApellido = !empty($estudiante['mat_segundo_apellido']) ? strtolower(trim($estudiante['mat_segundo_apellido'])) : '';
        $documento = !empty($estudiante['mat_documento']) ? strtolower(trim($estudiante['mat_documento'])) : '';
        
        // Dividir búsqueda en palabras
        $palabrasBusqueda = array_filter(explode(' ', $busquedaLower));
        $coincide = true;
        
        foreach ($palabrasBusqueda as $palabra) {
            if (strpos($nombreCompleto, $palabra) === false &&
                strpos($primerNombre, $palabra) === false &&
                strpos($segundoNombre, $palabra) === false &&
                strpos($primerApellido, $palabra) === false &&
                strpos($segundoApellido, $palabra) === false &&
                strpos($documento, $palabra) === false) {
                $coincide = false;
                break;
            }
        }
        
        if (!$coincide) {
            continue; // Saltar este estudiante si no coincide con la búsqueda
        }
    }
    
    // Obtener acudiente del estudiante (si no está asociado al acudiente actual)
    $acudienteEstudiante = null;
    $nombreAcudiente = "Sin acudiente";
    if (!$estaAsociado && !empty($estudiante['mat_acudiente'])) {
        $acudienteEstudiante = Usuarios::obtenerDatosUsuario($estudiante['mat_acudiente']);
        if (!empty($acudienteEstudiante)) {
            $nombreAcudiente = UsuariosPadre::nombreCompletoDelUsuario($acudienteEstudiante);
        }
    }
    
    // Obtener datos del grado
    $gradoNombre = '';
    if (!empty($estudiante['mat_grado'])) {
        $gradoData = Grados::obtenerGrado($estudiante['mat_grado']);
        if (!empty($gradoData)) {
            $gradoNombre = $gradoData['gra_nombre'];
        }
    }
    
    // Obtener datos del grupo
    $grupoNombre = '';
    if (!empty($estudiante['mat_grupo'])) {
        $grupoData = Grupos::obtenerGrupo($estudiante['mat_grupo']);
        if (!empty($grupoData)) {
            $grupoNombre = $grupoData['gru_nombre'];
        }
    }
    
    // Obtener cada parte del nombre para búsqueda individual (en minúsculas para búsqueda)
    $primerNombre = !empty($estudiante['mat_nombres']) ? strtolower(trim($estudiante['mat_nombres'])) : '';
    $segundoNombre = !empty($estudiante['mat_nombre2']) ? strtolower(trim($estudiante['mat_nombre2'])) : '';
    $primerApellido = !empty($estudiante['mat_primer_apellido']) ? strtolower(trim($estudiante['mat_primer_apellido'])) : '';
    $segundoApellido = !empty($estudiante['mat_segundo_apellido']) ? strtolower(trim($estudiante['mat_segundo_apellido'])) : '';
    
    $estudiantesConDatos[] = [
        'mat_id' => $estudianteId,
        'nombre' => Estudiantes::NombreCompletoDelEstudiante($estudiante),
        'primer_nombre' => $primerNombre,
        'segundo_nombre' => $segundoNombre,
        'primer_apellido' => $primerApellido,
        'segundo_apellido' => $segundoApellido,
        'documento' => $estudiante['mat_documento'],
        'grado' => $estudiante['mat_grado'],
        'grado_nombre' => $gradoNombre,
        'grupo' => $estudiante['mat_grupo'],
        'grupo_nombre' => $grupoNombre,
        'asociado' => $estaAsociado,
        'acudiente_nombre' => $nombreAcudiente
    ];
}

header('Content-Type: application/json');
echo json_encode([
    'estudiantes' => $estudiantesConDatos,
    'total' => count($estudiantesConDatos)
]);
exit();
