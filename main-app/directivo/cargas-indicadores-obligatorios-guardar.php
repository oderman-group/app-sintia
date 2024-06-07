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
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["nombre"]) == "" or trim($_POST["valor"]) == "") {
		include("../compartido/guardar-historial-acciones.php");
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	
	$ind = Indicadores::consultarValorIndicadoresObligatorios();

	if (($ind[0] + $_POST["valor"]) > 100) {
		echo "<span style='font-family:Arial; color:red;'>Los valores de los indicadores no deben superar el 100%.</samp>";
		exit();
	}
	
	$codigo = Indicadores::guardarIndicador($conexionPDO, "ind_nombre, ind_valor, ind_obligatorio, institucion, year, ind_id", [mysqli_real_escape_string($conexion,$_POST["nombre"]),$_POST["valor"],1, $config['conf_id_institucion'], $_SESSION["bd"]]);
	

	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cargas-indicadores-obligatorios.php";</script>';
	exit();