<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Foros.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0162';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

$idE="";
if(!empty($_GET["idE"])){ $idE=base64_decode($_GET["idE"]);}
$idU="";
if(!empty($_GET["idU"])){ $idU=base64_decode($_GET["idU"]);}

Evaluaciones::eliminarResultadosEstudiante($conexion, $config, $idE);

Foros::eliminarComentarioEstudiante($conexion, $config, $idE);

Foros::eliminarRespuestaEstudiante($conexion, $config, $idE);

Actividades::eliminarActividadesEntregasEstudiante($conexion, $config, $idE);

Boletin::eliminarNotasBoletinEstudiante($config, $idE);

Calificaciones::eliminarCalificacionEstudiante($config, $idE);

Ausencias::eliminarAusenciasEstudiantes($config, $idE);

$update = "mat_eliminado=1";
Estudiantes::actualizarMatriculasPorId($config, $idE, $update);

Calificaciones::eliminarNivelacionEstudiante($conexion, $config, $idE);

Calificaciones::eliminarNotaRecuperacionEstudiante($conexion, $config, $idE);

try{
    mysqli_query($conexion, "DELETE FROM ".BD_DISCIPLINA.".disciplina_matricula_condicional WHERE cond_estudiante='" . $idE . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
try{
    mysqli_query($conexion, "DELETE FROM ".BD_DISCIPLINA.".disciplina_reportes WHERE dr_estudiante='" . $idE . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
try{
    mysqli_query($conexion, "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='" . $idE . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
try{
    mysqli_query($conexion, "DELETE FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='" . $idU . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
try{
    mysqli_query($conexion, "DELETE FROM ".$baseDatosServicios.".general_resultados WHERE resg_id_usuario='" . $idU . "'");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
try{
    mysqli_query($conexion, "DELETE FROM ".$baseDatosServicios.".seguridad_historial_acciones WHERE hil_usuario='" . $idU . "'");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
try{
    mysqli_query($conexion, "DELETE FROM ".$baseDatosServicios.".social_noticias WHERE not_usuario='" . $idU . "'");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

UsuariosPadre::eliminarUsuarioPorID($config, $idU);

try{
    mysqli_query($conexion, "DELETE FROM ".BD_GENERAL.".usuarios_por_estudiantes WHERE upe_id_estudiante='" . $idE . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
try{
    mysqli_query($conexion, "DELETE FROM ".$baseDatosServicios.".social_emails WHERE ema_de='" . $idU . "' OR ema_para='" . $idU . "'");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="estudiantes.php?success=SC_DT_3&id='.$_GET["idE"].'";</script>';
exit();