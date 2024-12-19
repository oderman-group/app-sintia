<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Autenticate.php");

Modulos::validarAccesoDirectoPaginas();

$idPaginaInterna = 'DT0129';

$_SESSION['admin'] = $_SESSION['id'];

$_SESSION['id'] = base64_decode($_GET['user']);

$_SESSION["datosUsuario"] = UsuariosPadre::sesionUsuario($_SESSION['id']);

include("../compartido/guardar-historial-acciones.php");

$auth = Autenticate::getInstance();

switch (base64_decode($_GET['tipe'])) {
	case 2:
		$url = '../docente/cargas.php';

		if (isset($_GET['carga'])) {
			$url = '../docente/cargas-seleccionar.php?carga='.$_GET["carga"].'&periodo='.$_GET["periodo"];
		} else {
			$auth->limpiarCookiesDocentes();
			$_SESSION["infoCargaActual"] = '';
			unset($_SESSION["infoCargaActual"]);
		}

	break;

	case 3:
		$url = '../acudiente/estudiantes.php';
	break;

	case 4:
		$url = '../estudiante/cargas.php';
		$auth->limpiarCookiesEstudiantes();
	break;

	case 5:
		$url = '../directivo/noticias.php';
	break;

	default:
		$url = '../controlador/salir.php';
	break;
}

header("Location:".$url);