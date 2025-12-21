<?php
include("session.php");
$idPaginaInterna = 'DT0116';
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

header('Content-Type: application/json; charset=utf-8');

if(!Modulos::validarSubRol([$idPaginaInterna])){
    echo json_encode([
        'success' => false,
        'message' => 'Permisos insuficientes.'
    ]);
    exit();
}

$grado = isset($_POST['grado']) ? mysqli_real_escape_string($conexion, trim($_POST['grado'])) : '';
$grupo = isset($_POST['grupo']) ? mysqli_real_escape_string($conexion, trim($_POST['grupo'])) : '';

if($grado === '' || $grupo === ''){
    echo json_encode([
        'success' => false,
        'message' => 'Grado y grupo requeridos.'
    ]);
    exit();
}

$estudiantes = [];

try {
    $filtro = " AND mat.mat_grado='".$grado."' AND mat.mat_grupo='".$grupo."'";
    $consulta = Estudiantes::listarEstudiantesEnGrados($filtro);
    while($datos = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
        $estudiantes[] = [
            'id' => $datos['uss_id'],
            'nombre' => UsuariosPadre::nombreCompletoDelUsuario($datos)
        ];
    }
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
    echo json_encode([
        'success' => false,
        'message' => 'Error al consultar estudiantes.'
    ]);
    exit();
}

echo json_encode([
    'success' => true,
    'estudiantes' => $estudiantes
]);

