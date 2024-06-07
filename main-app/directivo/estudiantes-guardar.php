<?php
include("session.php");
require_once("../class/Estudiantes.php");
require_once "../class/Modulos.php";
require_once("../class/servicios/MediaTecnicaServicios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0192';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

$_POST["ciudadR"] = trim($_POST["ciudadR"]);

$parametrosPost='&tipoD='.base64_encode($_POST["tipoD"]).'&documento='.base64_encode($_POST["nDoc"]).'&religion='.base64_encode($_POST["religion"]).'&email='.base64_encode($_POST["email"]).'&direcion='.base64_encode($_POST["direccion"]).'&barrio='.base64_encode($_POST["barrio"]).'&telefono='.base64_encode($_POST["telefono"]).'&celular='.base64_encode($_POST["celular"]).'&estrato='.base64_encode($_POST["estrato"]).'&genero='.base64_encode($_POST["genero"]).'&nacimiento='.base64_encode($_POST["fNac"]).'&apellido1='.base64_encode($_POST["apellido1"]).'&apellido2='.base64_encode($_POST["apellido2"]).'&nombre='.base64_encode($_POST["nombres"]).'&grado='.base64_encode($_POST["grado"]).'&grupo='.base64_encode($_POST["grupo"]).'&tipoE='.base64_encode($_POST["tipoEst"]).'&lugarEx='.base64_encode($_POST["lugarD"]).'&lugarNac='.base64_encode($_POST["lNac"]).'&matricula='.base64_encode($_POST["matricula"]).'&folio='.base64_encode($_POST["folio"]).'&tesoreria='.base64_encode($_POST["codTesoreria"]).'&vaMatricula='.base64_encode($_POST["va_matricula"]).'&inclusion='.base64_encode($_POST["inclusion"]).'&extran='.base64_encode($_POST["extran"]).'&tipoSangre='.base64_encode($_POST["tipoSangre"]).'&eps='.base64_encode($_POST["eps"]).'&celular2='.base64_encode($_POST["celular2"]).'&ciudadR='.base64_encode($_POST["ciudadR"]).'&nombre2='.base64_encode($_POST["nombre2"]).'&documentoA='.base64_encode($_POST["documentoA"]).'&nombreA='.base64_encode($_POST["nombreA"]).'&ocupacionA='.base64_encode($_POST["ocupacionA"]).'&generoA='.base64_encode($_POST["generoA"]).'&expedicionA='.base64_encode($_POST["lugardA"]).'&tipoDocA='.base64_encode($_POST["tipoDAcudiente"]).'&apellido1A='.base64_encode($_POST["apellido1A"]).'&apellido2A='.base64_encode($_POST["apellido2A"]).'&nombre2A='.base64_encode($_POST["nombre2A"]).'&matestM='.base64_encode($_POST["matestM"]);

//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
if(trim($_POST["nDoc"])=="" or trim($_POST["apellido1"])=="" or trim($_POST["nombres"])=="" or trim($_POST["grado"])=="" or trim($_POST["documentoA"])==""){

	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_4'.$parametrosPost.'";</script>';
	exit();
}
//VALIDAMOS QUE EL ESTUDIANTE NO SE ENCUENTRE CREADO
$valiEstudiante = Estudiantes::validarExistenciaEstudiante($_POST["nDoc"]);
if($valiEstudiante > 0){

	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_5'.$parametrosPost.'";</script>';
	exit();
}

$result_numMat = strtotime("now");


if(empty($_POST["tipoMatricula"])){ $_POST["tipoMatricula"]=GRADO_GRUPAL;}

//Establecer valores por defecto cuando los campos vengan vacíos
if(empty($_POST["va_matricula"]))  $_POST["va_matricula"]  = 0;
if(empty($_POST["grupo"]))         $_POST["grupo"]         = 4;
if(empty($_POST["tipoEst"]))       $_POST["tipoEst"]       = 128;
if(empty($_POST["fNac"]))          $_POST["fNac"]          = '2000-01-01';
if(empty($_POST["tipoD"]))         $_POST["tipoD"]         = 107;
if(empty($_POST["genero"]))        $_POST["genero"]        = 126;

if(empty($_POST["religion"]))      $_POST["religion"]      = 112;
if(empty($_POST["estrato"]))       $_POST["estrato"]       = 116;
if(empty($_POST["extran"]))        $_POST["extran"]        = 0;
if(empty($_POST["inclusion"]))     $_POST["inclusion"]     = 0;
if(empty($_POST["tipoMatricula"])) $_POST["tipoMatricula"] = GRADO_GRUPAL;


//Api solo para Icolven
$estado='';
$mensaje='';
if($config['conf_id_institucion'] == ICOLVEN){
	require_once("apis-sion-create-student.php");
}
$procedencia=$_POST["lNacM"];
if(!empty($_POST["ciudadPro"]) && !is_numeric($_POST["ciudadPro"])){
	$procedencia=$_POST["ciudadPro"];
}

$acudienteConsulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_usuario='".$_POST["documentoA"]."'");

$acudienteNum = mysqli_num_rows($acudienteConsulta);
$acudienteDatos = mysqli_fetch_array($acudienteConsulta, MYSQLI_BOTH);
//PREGUNTAMOS SI EL ACUDIENTE EXISTE
if ($acudienteNum > 0) {	
	$idAcudiente = $acudienteDatos['uss_id'];
} else {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if(trim($_POST["documentoA"])=="" or trim($_POST["nombresA"])==""){

		include("../compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_6'.$parametrosPost.'";</script>';
		exit();
	}

	if(empty($_POST["generoA"]))		$_POST["generoA"]       = 126;
	if(empty($_POST["ocupacionA"]))		$_POST["ocupacionA"]    = '';
	if(empty($_POST["fechaNA"]))		$_POST["fechaNA"]       = '2000-01-01';
	if(empty($_POST["folio"]))       	$_POST["folio"]       	='';
	if(empty($_POST["codTesoreria"]))   $_POST["codTesoreria"]  = '';
	if(empty($_POST["tipoSangre"]))     $_POST["tipoSangre"]    = '';
	if(empty($_POST["eps"]))       		$_POST["eps"]       	= 126;
	
	$idAcudiente = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_genero, uss_celular, uss_foto,uss_idioma,uss_tipo_documento, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2,uss_documento, uss_tema_sidebar, uss_tema_header, uss_tema_logo, institucion, year, uss_id", [$_POST["documentoA"], $clavePorDefectoUsuarios, 3, mysqli_real_escape_string($conexion,$_POST["nombresA"]), 0, $_POST["ocupacionA"], $_POST["email"], $_POST["fechaNA"], 0, $_POST["generoA"], $_POST["celular"], 'default.png', 1, $_POST["tipoDAcudiente"], $_POST["lugarDa"], $_POST["direccion"], mysqli_real_escape_string($conexion,$_POST["apellido1A"]), mysqli_real_escape_string($conexion,$_POST["apellido2A"]), mysqli_real_escape_string($conexion,$_POST["nombre2A"]), 	$_POST["documentoA"], 'cyan-sidebar-color', 'header-indigo', 'logo-indigo', $config['conf_id_institucion'], $_SESSION["bd"]]);
}

$idEstudianteU = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_idioma, uss_tipo_documento, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2,uss_documento, uss_tema_sidebar,uss_tema_header,uss_tema_logo, institucion, year, uss_id", [$_POST["nDoc"], $clavePorDefectoUsuarios, 4, mysqli_real_escape_string($conexion,$_POST["nombres"]), 0, strtolower($_POST["email"]), $_POST["fNac"], 0, $_POST["genero"], $_POST["celular"], 'default.png', 1, $_POST["tipoD"], $_POST["lugarD"], $_POST["direccion"], mysqli_real_escape_string($conexion,$_POST["apellido1"]), mysqli_real_escape_string($conexion,$_POST["apellido2"]), mysqli_real_escape_string($conexion,$_POST["nombre2"]), $_POST["nDoc"], 'cyan-sidebar-color', 'header-indigo', 'logo-indigo', $config['conf_id_institucion'], $_SESSION["bd"]]);

//Insertamos la matrícula
$idEstudiante = Estudiantes::insertarEstudiantes($conexionPDO, $_POST, $idEstudianteU, $result_numMat, $procedencia, $idAcudiente);

//Insertamos las matrículas Adicionales
if ($_POST["tipoMatricula"]==GRADO_INDIVIDUAL && !empty($_POST["cursosAdicionales"])) { 
	try{
		MediaTecnicaServicios::guardar($idEstudiante,$_POST["cursosAdicionales"],$config,$_POST["grupoMT"]);
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
}
$idInsercion=Utilidades::generateCode("UPE");
try{
	mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios_por_estudiantes(upe_id, upe_id_usuario, upe_id_estudiante, institucion, year)VALUES('" .$idInsercion . "', '".$idAcudiente."', '".$idEstudiante."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}


if(!isset($estado) AND !isset($mensaje)){
	$estado="";
	$mensaje="";
}
$idUsr = mysqli_insert_id($conexion);
$estadoSintia=false;
$mensajeSintia='El estudiante no pudo ser creado correctamente en SINTIA.';
if(isset($idUsr) AND $idUsr!=''){
	$estadoSintia=true;
	$mensajeSintia='El estudiante fue creado correctamente en SINTIA.';
}

include("../compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($idEstudiante).'&stadsion='.base64_encode($estado).'&msgsion='.base64_encode($mensaje).'&stadsintia='.base64_encode($estadoSintia).'&msgsintia='.base64_encode($mensajeSintia).'";</script>';
exit();