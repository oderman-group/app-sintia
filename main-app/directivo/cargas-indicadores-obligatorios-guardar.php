<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0185';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["nombre"]) == "" or trim($_POST["valor"]) == "") {
		include("../compartido/guardar-historial-acciones.php");
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	
	try{
		$consultaInd=mysqli_query($conexion, "SELECT sum(ind_valor)+" . $_POST["valor"] . " FROM ".BD_ACADEMICA.".academico_indicadores where ind_obligatorio=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
	$ind = mysqli_fetch_array($consultaInd, MYSQLI_BOTH);

	if ($ind[0] > 100) {
		include("../compartido/guardar-historial-acciones.php");
		echo "<span style='font-family:Arial; color:red;'>Los valores de los indicadores no deben superar el 100%.</samp>";
		exit();
	}
	$codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_indicadores');

	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_indicadores(ind_id, ind_nombre, ind_valor, ind_obligatorio, institucion, year)VALUES('".$codigo."', '" . mysqli_real_escape_string($conexion,$_POST["nombre"]) . "','" . $_POST["valor"] . "',1, {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cargas-indicadores-obligatorios.php";</script>';
	exit();