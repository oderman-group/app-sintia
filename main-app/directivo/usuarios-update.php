<?php
include("session.php");
$idPaginaInterna = 'DT0131';
include("../compartido/sintia-funciones.php");
include("../compartido/guardar-historial-acciones.php");
require_once("../class/SubRoles.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

$archivoSubido = new Archivos;

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

if (!empty($_FILES['fotoUss']['name'])) {
	$archivoSubido->validarArchivo($_FILES['fotoUss']['size'], $_FILES['fotoUss']['name']);
	$explode = explode(".", $_FILES['fotoUss']['name']);
	$extension = end($explode);

	if($extension != 'jpg' && $extension != 'png'){
		echo '<script type="text/javascript">window.location.href="usuarios-editar.php?id='.base64_encode($_POST["idR"]).'&error=ER_DT_8";</script>';
		exit();
	}

	$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_img_') . "." . $extension;
	$destino = "../files/fotos";
	move_uploaded_file($_FILES['fotoUss']['tmp_name'], $destino . "/" . $archivo);

	$update = ['uss_foto' => $archivo];
	UsuariosPadre::actualizarUsuarios($config, $_POST["idR"], $update);
	
	if($_POST["tipoUsuario"]==4){
		$update = ['mat_foto' => $archivo];
		Estudiantes::actualizarMatriculasPorIdUsuario($config, $_POST["idR"], $update);
	}
}

$update = [
    "uss_usuario" => $_POST["usuario"],
    "uss_tipo" => $_POST["tipoUsuario"],
    "uss_nombre" => mysqli_real_escape_string($conexion, $_POST["nombre"]),
    "uss_email" => strtolower($_POST["email"]),
    "uss_genero" => $_POST["genero"],
    "uss_celular" => $_POST["celular"],
    "uss_ocupacion" => $_POST["ocupacion"],
    "uss_lugar_expedicion" => $_POST["lExpedicion"],
    "uss_direccion" => $_POST["direccion"],
    "uss_telefono" => $_POST["telefono"],
    "uss_intentos_fallidos" => $_POST["intentosFallidos"],
    "uss_tipo_documento" => $_POST["tipoD"],
    "uss_apellido1" => mysqli_real_escape_string($conexion, $_POST["apellido1"]),
    "uss_apellido2" => mysqli_real_escape_string($conexion, $_POST["apellido2"]),
    "uss_nombre2" => mysqli_real_escape_string($conexion, $_POST["nombre2"]),
    "uss_documento" => $_POST["documento"]
];
UsuariosPadre::actualizarUsuarios($config, $_POST["idR"], $update);

if (!empty($_POST["clave"]) && $_POST["cambiarClave"] == 1) {

	$validarClave=validarClave($_POST["clave"]);
	if($validarClave!=true){
		echo '<script type="text/javascript">window.location.href="usuarios-editar.php?error=5&id='.base64_encode($_POST["idR"]).'";</script>';
		exit();
	}

	$claveEncriptada = SHA1($_POST["clave"]);

	$update = [
		'uss_clave' 			=> $claveEncriptada,
		'uss_intentos_fallidos' => 0
	];
	UsuariosPadre::actualizarUsuarios($config, $_POST["idR"], $update);

}


if ($_POST["tipoUsuario"] == 4) {
	$update = ['mat_email' => strtolower($_POST["email"])];
	Estudiantes::actualizarMatriculasPorIdUsuario($config, $_POST["idR"], $update);
}
try{
if(!empty($_POST["subroles"])){	
	$listaRoles=SubRoles::actualizarRolesUsuario($_POST["idR"],$_POST["subroles"]);
}else{
	$listaRoles=SubRoles::eliminarSubrolesUsuarios($_POST["idR"]);
}
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

echo '<script type="text/javascript">window.location.href="usuarios-editar.php?id='.base64_encode($_POST["idR"]).'&success=SC_DT_2";</script>';
exit();