<?php
include("session-compartida.php");

$input = json_decode(file_get_contents("php://input"), true);
$response = [];

if (!empty($input)) {
    $_GET = $input;
}

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0040';

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH.'/main-app/class/EnviarEmail.php');
require_once(ROOT_PATH."/main-app/class/App/Comunicativo/Social_Email.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario.php");

$usuariosClase = new UsuariosFunciones;

try {
    $remitente = UsuariosPadre::sesionUsuario($_SESSION["id"]);
} catch (Exception $e) {
    $response['status'] = false;
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}


try {
    $destinatario = UsuariosPadre::sesionUsuario($_GET["receptor"]);
} catch (Exception $e) {
    $response['status'] = false;
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

try {
    /* 
    / Icolven compró la opción de envíar mensajes al correo de los usuarios.
    / Cuando el usuario que envía es de Icolven y no es un estudiante entonces
    / se envía dicho correo. O también cuando el mensaje es para SINTIA ADMIN.
    */
    if (
        (
            ($config['conf_id_institucion'] == ICOLVEN || $config['conf_id_institucion'] == DEVELOPER) 
            && $datosUsuarioActual['uss_tipo'] != TIPO_ESTUDIANTE
        )
        || $_GET["receptor"] == Administrativo_Usuario_Usuario::USUARIO_DEFAULT_SINTIA
    ) {
        //INICIO ENVÍO DE MENSAJE
        $tituloMsj    = $_GET["asunto"];
        $contenidoMsj = '
            <p style="color:navy;">
            Hola ' . strtoupper($destinatario['uss_nombre']) . ', has recibido un mensaje a través de la plataforma SINTIA.<br>
            <b>Remitente:</b> ' . strtoupper($remitente['uss_nombre']) . '.
            </p>

            <p>' . $_GET["contenido"] . '</p>
        ';

        //Consulta de datos para el usuario al cual se enviará el correo electrónico
        $predicadoUsuario = [
            'uss_id'      => $_GET["receptor"],
            'institucion' => $config['conf_id_institucion'],
            'year'        => $_SESSION["bd"]
        ];

        $consultaUsuario = Administrativo_Usuario_Usuario::Select($predicadoUsuario, '*', BD_GENERAL);
        $datosUsuario    = $consultaUsuario->fetch(PDO::FETCH_ASSOC);

        $data = [
            'contenido_msj'  => $contenidoMsj,
            'usuario_email'  => $datosUsuario['uss_email'],
            'usuario_nombre' => $datosUsuario['uss_nombre'],
            'institucion_id' => $config['conf_id_institucion'],
            'usuario_id'     => $_GET["receptor"]
        ];

        $asunto            = $tituloMsj;
        $bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-2.php';
        
        EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
    }

    $response['status'] = true;
} catch (Exception $e) {
    $response['status'] = false;
}

echo json_encode($response);
exit();