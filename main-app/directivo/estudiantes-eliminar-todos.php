<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
require_once(ROOT_PATH."/main-app/class/Foros.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0161';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

Evaluaciones::eliminarResultados($conexion, $config);

Foros::eliminarTodosComentario($conexion, $config);

Foros::eliminarTodasRespuestas($conexion, $config);

Actividades::eliminarActividadesEntregasTodas($conexion, $config);

Boletin::eliminarNotasBoletinInstitucion($config);

Calificaciones::eliminarCalificacionesInstitucion($config);

Ausencias::eliminarAusenciasInstitucion($config);

Estudiantes::eliminarTodasMatriculas($config['conf_id_institucion']);

Calificaciones::eliminarTodasNivelaciones($conexion, $config);

Calificaciones::eliminarTodasNotaRecuperacion($conexion, $config);

try{
	mysqli_query($conexion, "DELETE FROM ".BD_DISCIPLINA.".disciplina_matricula_condicional WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
try{
	mysqli_query($conexion, "DELETE FROM ".BD_DISCIPLINA.".disciplina_reportes WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
try{
	mysqli_query($conexion, "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
try{
	mysqli_query($conexion, "DELETE FROM ".$baseDatosServicios.".general_resultados");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

UsuariosPadre::eliminarUsuarioPorTipoUsuario($config, 4);

try{
	mysqli_query($conexion, "DELETE FROM ".BD_GENERAL.".usuarios_por_estudiantes WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
exit();