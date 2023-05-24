<?php
include("session.php");
include("../modelo/conexion.php");
require_once("../class/Estudiantes.php");


$_POST["ciudadR"] = trim($_POST["ciudadR"]);

//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
if(trim($_POST["nDoc"])=="" or trim($_POST["apellido1"])=="" or trim($_POST["nombres"])=="" or trim($_POST["grado"])=="" or trim($_POST["documentoA"])==""){

	echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_4&tipoD='.$_POST["tipoD"].'&documento='.$_POST["nDoc"].'&religion='.$_POST["religion"].'&email='.$_POST["email"].'&direcion='.$_POST["direccion"].'&barrio='.$_POST["barrio"].'&telefono='.$_POST["telefono"].'&celular='.$_POST["celular"].'&estrato='.$_POST["estrato"].'&genero='.$_POST["genero"].'&nacimiento='.$_POST["fNac"].'&apellido1='.$_POST["apellido1"].'&apellido2='.$_POST["apellido2"].'&nombre='.$_POST["nombres"].'&grado='.$_POST["grado"].'&grupo='.$_POST["grupo"].'&tipoE='.$_POST["tipoEst"].'&lugarEx='.$_POST["lugarD"].'&lugarNac='.$_POST["lNac"].'&matricula='.$_POST["matricula"].'&folio='.$_POST["folio"].'&tesoreria='.$_POST["codTesoreria"].'&vaMatricula='.$_POST["va_matricula"].'&inclusion='.$_POST["inclusion"].'&extran='.$_POST["extran"].'&tipoSangre='.$_POST["tipoSangre"].'&eps='.$_POST["eps"].'&celular2='.$_POST["celular2"].'&ciudadR='.$_POST["ciudadR"].'&nombre2='.$_POST["nombre2"].'&documentoA='.$_POST["documentoA"].'&nombreA='.$_POST["nombreA"].'&ocupacionA='.$_POST["ocupacionA"].'&generoA='.$_POST["generoA"].'&expedicionA='.$_POST["lugardA"].'&tipoDocA='.$_POST["tipoDAcudiente"].'&apellido1A='.$_POST["apellido1A"].'&apellido2A='.$_POST["apellido2A"].'&nombre2A='.$_POST["nombre2A"].'&matestM='.$_POST["matestM"].'";</script>';
	exit();
}
//VALIDAMOS QUE EL ESTUDIANTE NO SE ENCUENTRE CREADO
$valiEstudiante = Estudiantes::validarExistenciaEstudiante($_POST["nDoc"]);
if($valiEstudiante > 0){

	echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_5&tipoD='.$_POST["tipoD"].'&documento='.$_POST["nDoc"].'&religion='.$_POST["religion"].'&email='.$_POST["email"].'&direcion='.$_POST["direccion"].'&barrio='.$_POST["barrio"].'&telefono='.$_POST["telefono"].'&celular='.$_POST["celular"].'&estrato='.$_POST["estrato"].'&genero='.$_POST["genero"].'&nacimiento='.$_POST["fNac"].'&apellido1='.$_POST["apellido1"].'&apellido2='.$_POST["apellido2"].'&nombre='.$_POST["nombres"].'&grado='.$_POST["grado"].'&grupo='.$_POST["grupo"].'&tipoE='.$_POST["tipoEst"].'&lugarEx='.$_POST["lugarD"].'&lugarNac='.$_POST["lNac"].'&matricula='.$_POST["matricula"].'&folio='.$_POST["folio"].'&tesoreria='.$_POST["codTesoreria"].'&vaMatricula='.$_POST["va_matricula"].'&inclusion='.$_POST["inclusion"].'&extran='.$_POST["extran"].'&tipoSangre='.$_POST["tipoSangre"].'&eps='.$_POST["eps"].'&celular2='.$_POST["celular2"].'&ciudadR='.$_POST["ciudadR"].'&nombre2='.$_POST["nombre2"].'&documentoA='.$_POST["documentoA"].'&nombreA='.$_POST["nombreA"].'&ocupacionA='.$_POST["ocupacionA"].'&generoA='.$_POST["generoA"].'&expedicionA='.$_POST["lugardA"].'&tipoDocA='.$_POST["tipoDAcudiente"].'&apellido1A='.$_POST["apellido1A"].'&apellido2A='.$_POST["apellido2A"].'&nombre2A='.$_POST["nombre2A"].'&matestM='.$_POST["matestM"].'";</script>';
	exit();
}

$result_numMat = strtotime("now");

//Establecer valores por defecto cuando los campos vengan vacíos
$_POST["email"]    = strtolower($_POST["email"]);
$_POST["result_numMat"]    =$result_numMat;
if($_POST["va_matricula"]=="") $_POST["va_matricula"] = 0;
if($_POST["grupo"]=="")        $_POST["grupo"]        = 4;
if($_POST["tipoEst"]=="")      $_POST["tipoEst"]      = 128;
if($_POST["fNac"]=="")         $_POST["fNac"]         = '2000-01-01';
if($_POST["tipoD"]=="")        $_POST["tipoD"]        = 107;
if($_POST["genero"]=="")       $_POST["genero"]       = 126;

if($_POST["religion"]=="")     $_POST["religion"]     = 112;
if($_POST["estrato"]=="")      $_POST["estrato"]      = 116;
if($_POST["extran"]=="")       $_POST["extran"]       = 0;
if($_POST["inclusion"]=="")    $_POST["inclusion"]    = 0;


//Api solo para Icolven
if($config['conf_id_institucion']==1){
	require_once("apis-sion-create-student.php");
}
$procedencia=$_POST["lNacM"];
if(!empty($_POST["ciudadPro"]) && !is_numeric($_POST["ciudadPro"])){
	$procedencia=$_POST["ciudadPro"];
}
$_POST["procedencia"]=$procedencia;

try{
	$acudienteConsulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_usuario='".$_POST["documentoA"]."'");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
$acudienteNum = mysqli_num_rows($acudienteConsulta);
$acudienteDatos = mysqli_fetch_array($acudienteConsulta, MYSQLI_BOTH);
//PREGUNTAMOS SI EL ACUDIENTE EXISTE
if ($acudienteNum > 0) {	
	$idAcudiente = $acudienteDatos[0];
} else {
	// //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if(trim($_POST["documentoA"])=="" or trim($_POST["nombresA"])==""){

		echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_6&tipoD='.$_POST["tipoD"].'&documento='.$_POST["nDoc"].'&religion='.$_POST["religion"].'&email='.$_POST["email"].'&direcion='.$_POST["direccion"].'&barrio='.$_POST["barrio"].'&telefono='.$_POST["telefono"].'&celular='.$_POST["celular"].'&estrato='.$_POST["estrato"].'&genero='.$_POST["genero"].'&nacimiento='.$_POST["fNac"].'&apellido1='.$_POST["apellido1"].'&apellido2='.$_POST["apellido2"].'&nombre='.$_POST["nombres"].'&grado='.$_POST["grado"].'&grupo='.$_POST["grupo"].'&tipoE='.$_POST["tipoEst"].'&lugarEx='.$_POST["lugarD"].'&lugarNac='.$_POST["lNac"].'&matricula='.$_POST["matricula"].'&folio='.$_POST["folio"].'&tesoreria='.$_POST["codTesoreria"].'&vaMatricula='.$_POST["va_matricula"].'&inclusion='.$_POST["inclusion"].'&extran='.$_POST["extran"].'&tipoSangre='.$_POST["tipoSangre"].'&eps='.$_POST["eps"].'&celular2='.$_POST["celular2"].'&ciudadR='.$_POST["ciudadR"].'&nombre2='.$_POST["nombre2"].'&documentoA='.$_POST["documentoA"].'&nombreA='.$_POST["nombreA"].'&ocupacionA='.$_POST["ocupacionA"].'&generoA='.$_POST["generoA"].'&expedicionA='.$_POST["lugardA"].'&tipoDocA='.$_POST["tipoDAcudiente"].'&apellido1A='.$_POST["apellido1A"].'&apellido2A='.$_POST["apellido2A"].'&nombre2A='.$_POST["nombre2A"].'&matestM='.$_POST["matestM"].'";</script>';
		exit();
	}
}


//CREAMOS EL REGISTRO
try{
	Estudiantes::guardarDatos($_POST);
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

echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.$idEstudiante.'&stadsion='.$estado.'&msgsion='.$mensaje.'&stadsintia='.$estadoSintia.'&msgsintia='.$mensajeSintia.'";</script>';
exit();