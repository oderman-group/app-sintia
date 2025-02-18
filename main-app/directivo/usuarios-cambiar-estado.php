<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0087';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario_Bloqueado.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo 2;
	exit();
}

if (base64_decode($_GET["lock"]) == 1) $estado = 0;
else $estado = 1;

$update = ['uss_bloqueado' => $estado];
UsuariosPadre::actualizarUsuarios($config, base64_decode($_GET["idR"]), $update);

if (!empty($_GET["motivo"]) && $estado == 1) {
	
	$datosMotivo = [
		'usblo_id_usuario'    => base64_decode($_GET["idR"]),
		'usblo_motivo'        => $_GET["motivo"],
		'usblo_responsable'   => $_SESSION["id"],
		'usblo_institucion'   => $_SESSION['idInstitucion'],
		'usblo_year'          => $_SESSION["bd"],
		'usblo_forma_creacion'=> Administrativo_Usuario_Usuario_Bloqueado::MATRICULA
	];
	Administrativo_Usuario_Usuario_Bloqueado::Insert($datosMotivo, BD_ADMIN);
}

include("../compartido/guardar-historial-acciones.php");
echo $estado;