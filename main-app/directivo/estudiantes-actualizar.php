<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0174';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

require_once("../class/servicios/MediaTecnicaServicios.php");
//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
if(trim($_POST["nDoc"])=="" or trim($_POST["apellido1"])=="" or trim($_POST["nombres"])==""){
	echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&error=ER_DT_4";</script>';
	exit();
}
$validacionEstudiante = Estudiantes::validarRepeticionDocumento($_POST["nDoc"], $_POST["id"]);

if($validacionEstudiante > 0){
	echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&documento='.base64_encode($_POST["nDoc"]).'&error=ER_DT_11";</script>';
	exit();
}

$estado='';
$mensaje='';
$pasosMatricula='';
if($config['conf_mostrar_pasos_matricula'] == 1){
	$pasosMatricula="
		mat_iniciar_proceso='".$_POST["iniciarProceso"]."',
		mat_actualizar_datos='".$_POST["actualizarDatos"]."',
		mat_pago_matricula='".$_POST["pagoMatricula"]."',
		mat_contrato='".$_POST["contrato"]."',
		mat_pagare='".$_POST["pagare"]."',
		mat_compromiso_academico='".$_POST["compromisoA"]."',
		mat_compromiso_convivencia='".$_POST["compromisoC"]."',
		mat_manual='".$_POST["manual"]."',
		mat_mayores14='".$_POST["contrato14"]."',
		mat_compromiso_convivencia_opcion='".$_POST["compromisoOpcion"]."',
		mat_hoja_firma='".$_POST["firmaHoja"]."',
	";
}

if($config['conf_id_institucion'] == ICOLVEN){
	require_once("apis-sion-modify-student.php");
}
$fechaNacimiento="";
$fechaNacimientoU="";
if(!empty($_POST["fNac"])){
	$fechaNacimiento="mat_fecha_nacimiento='" . $_POST["fNac"] . "', ";
	$fechaNacimientoU="uss_fecha_nacimiento='" . $_POST["fNac"] . "', ";
}
$_POST["ciudadR"] = trim($_POST["ciudadR"]);
if($_POST["va_matricula"]==""){$_POST["va_matricula"]=0;}

$esMediaTecnica=!is_null($_POST["tipoMatricula"]);
if(!$esMediaTecnica){
	$datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($_POST["id"]);
	$_POST["tipoMatricula"]=$datosEstudianteActual["mat_tipo_matricula"];
}
if(empty($_POST["tipoMatricula"])){ $_POST["tipoMatricula"]=GRADO_GRUPAL;}

$procedencia=$_POST["lNac"];
if(!empty($_POST["ciudadPro"]) && !is_numeric($_POST["ciudadPro"])){
	$procedencia=$_POST["ciudadPro"];
}
if (!empty($_FILES['fotoMat']['name'])) {
	$explode = explode(".", $_FILES['fotoMat']['name']);
	$extension = end($explode);

	if($extension != 'jpg' && $extension != 'png'){
		echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&error=ER_DT_8";</script>';
		exit();
	}

	$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_img_') . "." . $extension;
	$destino = "../files/fotos";
	move_uploaded_file($_FILES['fotoMat']['tmp_name'], $destino . "/" . $archivo);

	$update = "mat_foto=" . $archivo . "";
	Estudiantes::actualizarMatriculasPorId($config, $_POST["id"], $update);

    $update = "uss_foto=" . $archivo . "";
    UsuariosPadre::actualizarUsuarios($config, $_POST["idU"], $update);
}

Estudiantes::actualizarEstudiantes($conexionPDO, $_POST, $fechaNacimiento, $procedencia, $pasosMatricula);

$update = "{$fechaNacimientoU} uss_usuario='".$_POST["nDoc"]."'";
UsuariosPadre::actualizarUsuarios($config, $_POST["idU"], $update);

//ACTUALIZAR EL ACUDIENTE 1	
if($_POST["documentoA"]!=""){

	$datosIdAcudiente = Estudiantes::obtenerDatosEstudiante($_POST["id"]);

	$usuarioAcudiente=$_POST["usuarioAcudiente"];
	if(!empty($datosIdAcudiente['mat_acudiente']) && $datosIdAcudiente['mat_acudiente']!=0){
		$usuarioAcudiente=$datosIdAcudiente['mat_acudiente'];
	}

	try {
		$acudiente = Usuarios::obtenerDatosUsuario($usuarioAcudiente);
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}		

	if(!empty($acudiente)){

		$update = "
			uss_usuario   		 = '".$_POST["usuarioAcudiente"]."', 
			uss_nombre    		 = '".mysqli_real_escape_string($conexion,$_POST["nombreA"])."', 
			uss_email     		 = '".$_POST["email"]."', 
			uss_ocupacion 		 = '".$_POST["ocupacionA"]."', 
			uss_genero    		 = '".$_POST["generoA"]."', 
			uss_celular   		 = '".$_POST["celular"]."', 
			uss_lugar_expedicion = '".$_POST["lugardA"]."', 
			uss_tipo_documento   = '".$_POST["tipoDAcudiente"]."', 
			uss_direccion        = '".$_POST["direccion"]."', 
			uss_apellido1 		 = '".mysqli_real_escape_string($conexion,$_POST["apellido1A"])."', 
			uss_apellido2		 = '".mysqli_real_escape_string($conexion,$_POST["apellido2A"])."', 
			uss_nombre2			 = '".mysqli_real_escape_string($conexion,$_POST["nombre2A"])."', 
			uss_documento		 = '".$_POST["documentoA"]."'
		";
		UsuariosPadre::actualizarUsuarios($config, $acudiente['uss_id'], $update);
		$idAcudiente = $acudiente['uss_id'];
	}else{
		$idAcudiente = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_idioma, uss_tipo_documento, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, uss_tema_sidebar, uss_tema_header, uss_tema_logo, institucion, year, uss_id", [$_POST["documentoA"], $clavePorDefectoUsuarios, 3, mysqli_real_escape_string($conexion,$_POST["nombreA"]), 0, $_POST["ocupacionA"], $_POST["email"], $_POST["fechaNA"], 0, $_POST["generoA"], $_POST["celular"], 'default.png', 1, $_POST["tipoDAcudiente"], $_POST["lugardA"], $_POST["direccion"], mysqli_real_escape_string($conexion,$_POST["apellido1A"]), mysqli_real_escape_string($conexion,$_POST["apellido2A"]), mysqli_real_escape_string($conexion,$_POST["nombre2A"]), $_POST["documentoA"], 'cyan-sidebar-color', 'header-indigo', 'logo-indigo', $config['conf_id_institucion'], $_SESSION["bd"]]);
	}

	$update = "mat_acudiente=".$idAcudiente."";
	Estudiantes::actualizarMatriculasPorId($config, $_POST["id"], $update);

	try {
		mysqli_query($conexion, "DELETE FROM ".BD_GENERAL.".usuarios_por_estudiantes 
		WHERE upe_id_estudiante='".$_POST["id"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}	

	$idInsercion=Utilidades::generateCode("UPE");
	try {
		mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios_por_estudiantes(upe_id, upe_id_usuario, upe_id_estudiante, institucion, year)VALUES('" .$idInsercion . "', '".$idAcudiente."', '".$_POST["id"]."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}	
}


//ACTUALIZAR EL ACUDIENTE 2
if(!empty($_POST["idAcudiente2"])){


    $update = "
		uss_usuario='".$_POST["documentoA2"]."', 
		uss_nombre='".$_POST["nombreA2"]."', 
		uss_email='".$_POST["email"]."', 
		uss_ocupacion='".$_POST["ocupacionA2"]."', 
		uss_genero='".$_POST["generoA2"]."', 
		uss_celular='".$_POST["celular"]."', 
		uss_lugar_expedicion='".$_POST["lugardA2"]."', 
		uss_direccion='".$_POST["direccion"]."', 
		uss_apellido1='".$_POST["apellido1A2"]."', 
		uss_apellido2='".$_POST["apellido2A2"]."', 
		uss_nombre2='".$_POST["nombre2A2"]."', 
		uss_documento= '".$_POST["documentoA2"]."'
	";
    UsuariosPadre::actualizarUsuarios($config, $_POST["idAcudiente2"], $update);
}else {
	if(!empty($_POST["documentoA2"])){
	
		try {
			$existeAcudiente2 = Usuarios::validarExistenciaUsuario($_POST["documentoA2"]);
		} catch (Exception $e) {
			include("../compartido/error-catch-to-report.php");
		}
	
		if($existeAcudiente2>0){
	
			try {
				$acudiente2 = Usuarios::obtenerDatosUsuario($_POST["documentoA2"]);
			} catch (Exception $e) {
				include("../compartido/error-catch-to-report.php");
			}


			$update = "
				uss_usuario='".$_POST["documentoA2"]."', 
				uss_nombre='".$_POST["nombreA2"]."', 
				uss_email='".$_POST["email"]."', 
				uss_ocupacion='".$_POST["ocupacionA2"]."', 
				uss_genero='".$_POST["generoA2"]."', 
				uss_celular='".$_POST["celular"]."', 
				uss_lugar_expedicion='".$_POST["lugardA2"]."', 
				uss_direccion='".$_POST["direccion"]."', 
				uss_apellido1='".$_POST["apellido1A2"]."', 
				uss_apellido2='".$_POST["apellido2A2"]."', 
				uss_nombre2='".$_POST["nombre2A2"]."', 
				uss_documento= '".$_POST["documentoA2"]."'
			";
			UsuariosPadre::actualizarUsuarios($config, $acudiente2['uss_id'], $update);
			$idAcudiente2 = $acudiente2['uss_id'];
		}else{
			$idAcudiente2 = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_ocupacion, uss_email, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_portada, uss_idioma, uss_tema, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, year, uss_id", [$_POST["documentoA2"],$clavePorDefectoUsuarios,3,$_POST["nombreA2"],0,$_POST["ocupacionA2"],$_POST["email"],0,$_POST["generoA2"],$_POST["celular"], 'default.png', 'default.png', 1, 'green', $_POST["lugardA2"], $_POST["direccion"], $_POST["apellido1A2"], $_POST["apellido2A2"], $_POST["nombre2A2"],$_POST["documentoA2"], $config['conf_id_institucion'], $_SESSION["bd"]]);
		}
	
		$update = "mat_acudiente2=".$idAcudiente2."";
		Estudiantes::actualizarMatriculasPorId($config, $_POST["id"], $update);
	}
}
include("../compartido/guardar-historial-acciones.php");

$estadoSintia=true;
$mensajeSintia='La información del estudiante se actualizó correctamente en SINTIA.';

echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&stadsion='.base64_encode($estado).'&msgsion='.base64_encode($mensaje).'&stadsintia='.base64_encode($estadoSintia).'&msgsintia='.base64_encode($mensajeSintia).'";</script>';
exit();