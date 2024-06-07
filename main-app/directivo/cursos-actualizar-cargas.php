<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0172';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

	$cargas = CargaAcademica::traerCargasConIntensidad($config);
	while ($c = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {

		Grados::eliminarIntensidadMateriaCurso($conexion, $config, $c['car_curso'], $c['car_materia']);
		
		Grados::guardarIntensidadMateriaCurso($conexion, $conexionPDO, $config, $c['car_curso'], $c['car_materia'], $c['car_ih']);
		
	}
	include("../compartido/guardar-historial-acciones.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();