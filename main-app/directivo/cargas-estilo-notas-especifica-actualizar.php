<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0168';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
// PHP 8.1+: trim(null) está deprecated, por eso normalizamos a string vacío.
$nombreCN = trim((string)($_POST["nombreCN"] ?? ''));
$ndesdeCN = trim((string)($_POST["ndesdeCN"] ?? ''));
$nhastaCN = trim((string)($_POST["nhastaCN"] ?? ''));
$idCN     = trim((string)($_POST["idCN"] ?? ''));

if ($nombreCN === "" || $ndesdeCN === "" || $nhastaCN === "" || $idCN === "") {
	echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.<br>
		<a href='javascript:history.go(-1)'>[Volver al formulario]</a></samp>";
	exit();
}

CargaAcademica::actualizarTipoNota($conexion, $config, $_POST);

	include("../compartido/guardar-historial-acciones.php");

	echo '<script type="text/javascript">window.location.href="cargas-estilo-notas-especifica.php?id=' . $_POST["idCN"] . '";</script>';
	exit();