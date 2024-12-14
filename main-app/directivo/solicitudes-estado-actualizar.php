<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Solicitud_Desbloqueo/General_Solicitud.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0130';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

if( $_GET["estado"] == Administrativo_Solicitud_Desbloqueo_General_Solicitud::SOLICITUD_ACEPTADA ) {
    $update = ['uss_bloqueado' => 0];
    UsuariosPadre::actualizarUsuarios($config, $_GET["idUsuario"], $update);
}

try{
    mysqli_query($conexion, "UPDATE ".BD_GENERAL.".general_solicitudes SET soli_estado='" . $_GET["estado"] . "' 
    WHERE soli_id='" . $_GET["idRegistro"] . "'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}

if($_GET["estado"] == Administrativo_Solicitud_Desbloqueo_General_Solicitud::SOLICITUD_ACEPTADA || $_GET["estado"] == Administrativo_Solicitud_Desbloqueo_General_Solicitud::SOLICITUD_RECHAZADA) {
    require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
    require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario.php");

    $estados = [
        Administrativo_Solicitud_Desbloqueo_General_Solicitud::SOLICITUD_ACEPTADA   => 'Aceptada',
        Administrativo_Solicitud_Desbloqueo_General_Solicitud::SOLICITUD_RECHAZADA  => 'Rechazada'
    ];

    $predicado = [
        'uss_id'      => $_GET["idUsuario"],
        'institucion' => $_SESSION['idInstitucion'],
        'year'        => $_SESSION['bd']
    ];

    $campos    = "uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2, uss_email";
    $consulta  = Administrativo_Usuario_Usuario::Select($predicado, $campos, BD_GENERAL);
    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    $nombre    = UsuariosPadre::nombreCompletoDelUsuario($resultado);

    $data = [
        'institucion_id'   => $_SESSION["idInstitucion"],
        'institucion_agno' => $_SESSION["bd"],
        'usuario_id'       => $_GET["idUsuario"],
        'usuario_email'    => $resultado['uss_email'],
        'usuario_nombre'   => $nombre,
        'usuario_estado'   => $_GET["estado"],
        'motivo'           => $_GET["motivo"]
    ];
    $asunto = 'Solicitud de desbloqueo ' . $estados[$_GET["estado"]];
    $bodyTemplateRoute = ROOT_PATH.'/config-general/template-email-solicitud-desbloqueo.php';

    EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute,null,null);
}

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit();