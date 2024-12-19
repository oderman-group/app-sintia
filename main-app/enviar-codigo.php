<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Notificacion.php");

$notificacion = new Notificacion();

$data = [
    'usuario_nombre'      => $_REQUEST['nombre'] . ' ' . $_REQUEST['apellidos'],
    'institucion_id'      => 22,
    'usuario_id'          => 1,
    'year'                => date("Y"),
    'asunto'              => 'Código de Confirmación: ',
    'body_template_route' => ROOT_PATH .'/config-general/template-email-activar-cuenta-codigo.php',
    'usuario_email'       => $_REQUEST['email'],
    'telefono'            => $_REQUEST['celular'],
];

$canal = Notificacion::CANAL_EMAIL;

try {
    $datosCodigo = $notificacion->enviarCodigoNotificacion($data, $canal, Notificacion::PROCESO_ACTIVAR_CUENTA);
    $arrayIdInsercion=[
        "success"=>true,
        "message"=>"Código enviado exitosamente",
        "code"=>$datosCodigo
    ];
} catch (Exception $e) {
    $arrayIdInsercion=["success"=>false, "message"=>"Error al enviar el código: ".$e->getMessage()];
}
    
header('Content-Type: application/json');
echo json_encode($arrayIdInsercion);