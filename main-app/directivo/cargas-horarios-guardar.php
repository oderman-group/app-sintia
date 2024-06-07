<?php 
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0184';
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
if (trim($_POST["idH"]) == "" or trim($_POST["inicioH"]) == "" or trim($_POST["finH"]) == "") {
	include("../compartido/guardar-historial-acciones.php");
	echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
	exit();
}

$numero = (count($_POST["diaH"]));
$contador = 0;
while ($contador < $numero) {
	CargaAcademica::guardarHorariosCargas($conexion, $conexionPDO, $config, $_POST["diaH"][$contador], $_POST);
	$contador++;
}

include("../compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="cargas-horarios.php?id=' . base64_encode($_POST["idH"]) . '";</script>';
exit();