<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/Notificacion.php");

$notificacion = new Notificacion();

if (!empty($_REQUEST['usuarioId'])) {
    $datosUsuario = Usuarios::buscarUsuarioIdNuevo($_REQUEST['usuarioId']);
}

$numeroCelular = preg_replace('/[()\s-]/', '', $datosUsuario['uss_celular']);

$data = [
    'usuario_nombre'      => $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'],
    'institucion_id'      => $datosUsuario['institucion'],
    'usuario_id'          => $datosUsuario['uss_id'],
    'year'                => $datosUsuario['year'],
    'asunto'              => 'Código de Confirmación: ',
    'body_template_route' => ROOT_PATH .'/config-general/template-email-recuperar-clave-codigo.php',
    'usuario_email'       => $datosUsuario['uss_email'],
    'telefono'            => $numeroCelular,
    'id_nuevo'            => $datosUsuario['id_nuevo'],
    'datos_codigo'        => [],
];

$canal = Notificacion::CANAL_SMS;

try {
    $datosCodigo = $notificacion->enviarCodigoNotificacion($data, $canal, Notificacion::PROCESO_RECUPERAR_CLAVE);
    $arrayIdInsercion=[
        "success"=>true,
        "message"=>"Código enviado exitosamente",
        "telefono"=> $numeroCelular,
        "code"=>$datosCodigo
    ];
} catch (Exception $e) {
    $arrayIdInsercion=["success"=>false, "message"=>"Error al enviar el código: ".$e->getMessage()];
}
    
header('Content-Type: application/json');
echo json_encode($arrayIdInsercion);