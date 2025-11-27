<?php
$input = json_decode(file_get_contents("php://input"), true);
if (!empty($input)) {
    $_POST = $input;
}
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0026';
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/Clases.php");
$usuariosClase = new UsuariosFunciones;
$response = array();
try {
    // ✅ guardarPreguntasClases ahora retorna el código
    $codigo = Clases::guardarPreguntasClases($conexion, $config, $_POST);
    $url = $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'], 'clases-ver.php');
    $response["ok"] = true;
    $response["codigo"] = base64_encode($codigo);
    $response["nivel"] = !empty($_POST['nivel']) ? $_POST['nivel'] : 0;
    $response["msg"] = "Se guardó comentario con código {$codigo} exitosamente !";
    
    error_log("✅ Comentario guardado exitosamente - Código: $codigo");
    
    if (!empty($_POST['idPadre'])) {
        $parametros = ["cpp_id_clase" => $_POST['idClase'], "institucion" => $config['conf_id_institucion'], "year" => $_SESSION["bd"], "cpp_padre" => $_POST['idPadre']];
        $response["padre"] = $_POST['idPadre'];
        $response["cantidad"] = Clases::contar($parametros);
        error_log("📊 Comentario hijo - Padre: {$_POST['idPadre']}, Cantidad de respuestas: {$response['cantidad']}");
    } else {
        // ✅ Filtro mejorado para incluir IS NULL
        $parametros = ["cpp_id_clase" => $_POST['idClase'], "institucion" => $config['conf_id_institucion'], "year" => $_SESSION["bd"]];
        $filtro = " AND (TRIM(cpp_padre) = ''  OR LENGTH(cpp_padre) < 0 OR cpp_padre IS NULL)";
        $response["cantidad"] = Clases::contar($parametros, $filtro);
        error_log("📊 Comentario padre - Cantidad total de comentarios padre: {$response['cantidad']}");
    }
} catch (Exception $e) {
    $response["ok"] = false;
    $response["msg"] = $e->getMessage();
    error_log("❌ Error al guardar comentario: " . $e->getMessage());
    include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
}
include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
echo json_encode($response);
exit();
