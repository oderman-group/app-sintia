<?php 
include("session.php");
include("../modelo/conexion.php");
require_once("../class/Estudiantes.php");

//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
if(trim($_POST["nDoc"])=="" or trim($_POST["apellido1"])=="" or trim($_POST["nombres"])==""){
	echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.$_POST["id"].'&error=ER_DT_4";</script>';
	exit();
}
$validacionEstudiante = Estudiantes::validarRepeticionDocumento($_POST["nDoc"], $_POST["id"]);

if($validacionEstudiante > 0){
	echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.$_POST["id"].'&documento='.$_POST["nDoc"].'&error=ER_DT_11";</script>';
	exit();
}

if($config['conf_id_institucion']==1){
	require_once("apis-sion-modify-student.php");
}

$_POST["ciudadR"] = trim($_POST["ciudadR"]);
if($_POST["va_matricula"]==""){$_POST["va_matricula"]=0;}

$procedencia=$_POST["lNac"];
if(!empty($_POST["ciudadPro"]) && !is_numeric($_POST["ciudadPro"])){
	$procedencia=$_POST["ciudadPro"];
}
if (!empty($_FILES['fotoMat']['name'])) {
	$explode = explode(".", $_FILES['fotoMat']['name']);
	$extension = end($explode);

	if($extension != 'jpg' && $extension != 'png'){
		echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.$_POST["id"].'&error=ER_DT_8";</script>';
		exit();
	}

	$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_img_') . "." . $extension;
	$destino = "../files/fotos";
	move_uploaded_file($_FILES['fotoMat']['tmp_name'], $destino . "/" . $archivo);
	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_foto='" . $archivo . "' WHERE mat_id='" . $_POST["id"] . "'");
	mysqli_query($conexion, "UPDATE usuarios SET uss_foto='" . $archivo . "' WHERE uss_id='" . $_POST["idU"] . "'");
}

try{
	mysqli_query($conexion, "UPDATE academico_matriculas SET 
	mat_tipo_documento='".$_POST["tipoD"]."', 
	mat_documento='".$_POST["nDoc"]."', 
	mat_religion='".$_POST["religion"]."', 
	mat_email='".strtolower($_POST["email"])."', 
	mat_direccion='".$_POST["direccion"]."', 
	mat_barrio='".$_POST["barrio"]."', 
	mat_telefono='".$_POST["telefono"]."', 
	mat_celular='".$_POST["celular"]."', 
	mat_estrato='".$_POST["estrato"]."', 
	mat_genero='".$_POST["genero"]."', 
	mat_fecha_nacimiento='".$_POST["fNac"]."', 
	mat_primer_apellido='".$_POST["apellido1"]."', 
	mat_segundo_apellido='".$_POST["apellido2"]."', 
	mat_nombres='".$_POST["nombres"]."', 
	mat_grado='".$_POST["grado"]."', 
	mat_grupo='".$_POST["grupo"]."', 
	mat_tipo='".$_POST["tipoEst"]."',
	mat_lugar_expedicion='".$_POST["lugarD"]."',
	mat_lugar_nacimiento='".$procedencia."',
	mat_estado_matricula=".$_POST["matestM"].", 
	mat_matricula='".$_POST["matricula"]."', 
	mat_folio='".$_POST["folio"]."', 
	mat_codigo_tesoreria='".$_POST["codTesoreria"]."', 
	mat_valor_matricula='".$_POST["va_matricula"]."', 
	mat_inclusion='".$_POST["inclusion"]."', 
	mat_extranjero='".$_POST["extran"]."', 
	mat_fecha=now(), 
	mat_numero_matricula='".$_POST["NumMatricula"]."', 
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
	mat_estado_agno='".$_POST["estadoAgno"]."',
	mat_tipo_sangre='".$_POST["tipoSangre"]."', 
	mat_eps='".$_POST["eps"]."', 
	mat_celular2='".$_POST["celular2"]."', 
	mat_ciudad_residencia='".$_POST["ciudadR"]."', 
	mat_nombre2='".$_POST["nombre2"]."',
	mat_tipo_matricula='".$_POST["tipoMatricula"]."'

	WHERE mat_id=".$_POST["id"].";");

} catch (Exception $e) {
    echo 'Excepción capturada: ',  $e->getMessage(), "\n";
	exit();
}
//Insertamos las matrículas Adicionales
try{
	foreach ($_POST["cursosAdicionales"] as $clave => $cursoId) {
		$ver=$cursoId;
	// mysqli_query($conexion, "INSERT INTO mediatecnica_matricula_cursos(
	// 	matcur_id_curso, 
	// 	matcur_id_matricula,
	// 	matcur_id_institusion,
	// 	matcur_years
	// 	)
	// 	VALUES(
	// 	".$result_numMat.",
	// 	".$result_numMat.",
	// 	".$config['conf_id_institucion'].",
	// 	".$result_numMat."
	// 	)
	// ");
		
	}

} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

try {
	mysqli_query($conexion, "UPDATE usuarios SET uss_usuario='".$_POST["nDoc"]."' 
	WHERE uss_id='".$_POST["idU"]."'");
} catch (Exception $e) {
    echo 'Excepción capturada: ',  $e->getMessage(), "\n";
	exit();
}	

//ACTUALIZAR EL ACUDIENTE 1	
if($_POST["documentoA"]!=""){

	try {
		$consultaAcudiente=mysqli_query($conexion, "SELECT * FROM usuarios 
		WHERE uss_usuario='".$_POST["documentoA"]."'");
		$acudiente = mysqli_fetch_array($consultaAcudiente, MYSQLI_BOTH);
	} catch (Exception $e) {
		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		exit();
	}		

	try {
		mysqli_query($conexion, "UPDATE academico_matriculas SET mat_acudiente='".$acudiente['uss_id']."' 
		WHERE mat_id='".$_POST["id"]."'");
	} catch (Exception $e) {
		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		exit();
	}	

	try {
		mysqli_query($conexion, "DELETE FROM usuarios_por_estudiantes 
		WHERE upe_id_estudiante='".$_POST["id"]."'");
	} catch (Exception $e) {
		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		exit();
	}	

	try {
		mysqli_query($conexion, "INSERT INTO usuarios_por_estudiantes(upe_id_usuario, upe_id_estudiante)VALUES('".$acudiente['uss_id']."', '".$_POST["id"]."')");
	} catch (Exception $e) {
		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		exit();
	}	

	try {
		mysqli_query($conexion, "UPDATE usuarios SET 
		uss_usuario   		 = '".$_POST["documentoA"]."', 
		uss_nombre    		 = '".$_POST["nombreA"]."', 
		uss_email     		 = '".$_POST["email"]."', 
		uss_ocupacion 		 = '".$_POST["ocupacionA"]."', 
		uss_genero    		 = '".$_POST["generoA"]."', 
		uss_celular   		 = '".$_POST["celular"]."', 
		uss_lugar_expedicion = '".$_POST["lugardA"]."', 
		uss_tipo_documento   = '".$_POST["tipoDAcudiente"]."', 
		uss_direccion        = '".$_POST["direccion"]."', 
		uss_apellido1 		 = '".$_POST["apellido1A"]."', 
		uss_apellido2		 = '".$_POST["apellido2A"]."', 
		uss_nombre2			 = '".$_POST["nombre2A"]."', 
		uss_documento		 = '".$_POST["documentoA"]."' 
		WHERE uss_id='".$acudiente['uss_id']."'");
	} catch (Exception $e) {
		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		exit();
	}	
		
}


//ACTUALIZAR EL ACUDIENTE 2
if($_POST["idAcudiente2"]!=""){

	try {
		mysqli_query($conexion, "UPDATE usuarios SET 
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
		WHERE uss_id='".$_POST["documentoA2"]."'");
	} catch (Exception $e) {
		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		exit();
	}	
		
}else {
	if($_POST["documentoA2"]!=""){

		try {
			mysqli_query($conexion, "INSERT INTO usuarios(uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_ocupacion, uss_email, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_portada, uss_idioma, uss_tema, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento)VALUES('".$_POST["documentoA2"]."','".$clavePorDefectoUsuarios."',3,'".$_POST["nombreA2"]."',0,'".$_POST["ocupacionA2"]."','".$_POST["email"]."',0,'".$_POST["generoA2"]."','".$_POST["celular"]."', 'default.png', 'default.png', 1, 'green', '".$_POST["lugardA2"]."', '".$_POST["direccion"]."', '".$_POST["apellido1A2"]."', '".$_POST["apellido2A2"]."', '".$_POST["nombre2A2"]."','".$_POST["documentoA2"]."')");
			
			$idAcudiente2 = mysqli_insert_id($conexion);
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
			exit();
		}	
		
		try {
			mysqli_query($conexion, "UPDATE academico_matriculas SET mat_acudiente2='".$idAcudiente2."'
			WHERE mat_id=".$_POST["id"]);
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
			exit();
		}	
			
	}
}
$estadoSintia=true;
$mensajeSintia='La información del estudiante se actualizó correctamente en SINTIA.';

echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.$_POST["id"].'&stadsion='.$estado.'&msgsion='.$mensaje.'&stadsintia='.$estadoSintia.'&msgsintia='.$mensajeSintia.'";</script>';
exit();