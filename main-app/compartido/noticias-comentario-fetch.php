<?php
include("session-compartida.php");
$input = json_decode(file_get_contents("php://input"), true);
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0022';
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
include(ROOT_PATH . "/main-app/class/SocialComentarios.php");
$usuariosClase = new Usuarios;
$idnotica = $input['id'];
$comentario = $input['comentario'];
$tipo = $input['tipo'];
$padre = empty($input['padre'])?0:$input['padre'];
$response = array();
try {
    $idNotify = SocialComentarios::guardar($idnotica,$comentario,$padre);
    $parametros =["ncm_noticia"=>$idnotica,"ncm_padre"=>$padre];
    $numcomentarios = SocialComentarios::contar($parametros);     

    $response["ok"] = true;
    $response["msg"] = $tipo.' registrado con exito!';
    $response["idNotica"] = $idnotica;
    $response["padre"] = $padre;
    $response["tipo"] = $tipo;
    $response["idComentario"] = $idNotify;
    $response["cantidad"] =  $numcomentarios;
} catch (Exception $e) {
    $response["ok"] = false;
    $response["msg"] = $e;
    include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
}
echo json_encode($response);
