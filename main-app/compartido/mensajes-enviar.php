<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0040';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH.'/main-app/class/EnviarEmail.php');
require_once(ROOT_PATH."/main-app/class/App/Comunicativo/Social_Email.php");
$usuariosClase = new UsuariosFunciones;

try{
    $remitente = UsuariosPadre::sesionUsuario($_SESSION["id"]);
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$cont = count($_POST["para"]);
$i = 0;
while ($i < $cont) {

    try{
        $destinatario = UsuariosPadre::sesionUsuario($_POST["para"][$i]);
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }

    $datos = [
        'ema_de'             => $_SESSION["id"],
        'ema_para'           => $_POST["para"][$i],
        'ema_asunto'         => mysqli_real_escape_string($conexion,$_POST["asunto"]),
        'ema_contenido'      => mysqli_real_escape_string($conexion,$_POST["contenido"]),
        'ema_fecha'          => date("Y-m-d h:i:s"),
        'ema_visto'          => 0,
        'ema_eliminado_de'   => 0,
        'ema_eliminado_para' => 0,
        'ema_institucion'    => $config['conf_id_institucion'],
        'ema_year'           => $_SESSION["bd"]
    ];

    try{
        Comunicativo_Social_Email::Insert($datos, BD_ADMIN);
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
    $i++;

    if ($_POST["para"][$i] == 1) {
        //INICIO ENVÍO DE MENSAJE
        $tituloMsj = $_POST["asunto"];
        $contenidoMsj = '
            <p style="color:navy;">
            Hola ' . strtoupper($destinatario['uss_nombre']) . ', has recibido un mensaje a través de la plataforma SINTIA.<br>
            <b>Remitente:</b> ' . strtoupper($remitente['uss_nombre']) . '.
            </p>

            <p>' . $_POST["contenido"] . '</p>
        ';

        $data = [
            'contenido_msj'   => $contenidoMsj,
            'usuario_email'    => 'tecmejia2010@gmail.com',
            'usuario_nombre'   => 'Jhon Oderman'
        ];
        $asunto = $tituloMsj;
        $bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-2.php';
        
        EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute,null,null);
    }
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'mensajes.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
exit();