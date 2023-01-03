<?php
include("session.php");
include("../modelo/conexion.php");

	$_POST["ciudadR"] = trim($_POST["ciudadR"]);

	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if(trim($_POST["tipoD"])=="" or trim($_POST["nDoc"])=="" or trim($_POST["genero"])=="" or trim($_POST["fNac"])=="" or trim($_POST["apellido1"])=="" or trim($_POST["apellido2"])=="" or trim($_POST["nombres"])=="" or trim($_POST["grado"])=="" or trim($_POST["tipoEst"])=="" or trim($_POST["documentoA"])==""){
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	//VALIDAMOS QUE EL ESTUDIANTE NO SE ENCUENTRE CREADO
	$valiEstudiante=mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_documento='".$_POST["nDoc"]."'");
	if(mysqli_num_rows($valiEstudiante)>0){
		echo "<span style='font-family:Arial; color:red;'>Este estudiante ya se ecuentra creado.</samp>";
		exit();
	}

	$consultaResult=mysqli_query($conexion, "SELECT MAX(mat_matricula)+1 AS num_mat FROM academico_matriculas");
	$result_numMat=mysql_fetch_array($consultaResult, MYSQLI_BOTH);
	if($result_numMat[0]=="") $result_numMat[0]=$config[1]."1";
	//COMPRBAR QUE NO SE VAYA A REPETIR EL NUMERO DE LA MATRICULA
	$i=1;
	while($i==1){
		$matriculados = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_matricula='".$result_numMat[0]."'");
		if($matriculadosNum = mysqli_num_rows($matriculados)>0){
			$result_numMat[0]++;
		}else{
			$i=0;
		}
	}
	if($_POST["va_matricula"]=="") $_POST["va_matricula"]=0;
	if($_POST["grupo"]=="") $_POST["grupo"]=4;

	require_once("apis-sion-create-student.php");

	//INSERTAMOS EL USUARIO ESTUDIANTE
	mysqli_query($conexion, "INSERT INTO usuarios(uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_portada, uss_idioma, uss_tema, uss_tipo_documento, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2)VALUES('".	$_POST["nDoc"]."','1234',4,'".$_POST["nombres"]."',0,'".strtolower($_POST["email"])."','".$_POST["fNac"]."',0,'".$_POST["genero"]."','".$_POST["celular"]."', 'default.png', 'default.png', 1, 'green','".$_POST["tipoD"]."','".$_POST["lugarD"]."', '".$_POST["direccion"]."', '".$_POST["apellido1"]."', '".$_POST["apellido2"]."', '".$_POST["nombre2"]."')");
	$idEstudianteU = mysqli_insert_id($conexion);

	$acudienteConsulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_usuario='".$_POST["documentoA"]."'");
	$acudienteNum = mysqli_num_rows($acudienteConsulta);
	$acudienteDatos = mysql_fetch_array($acudienteConsulta, MYSQLI_BOTH);
	//PREGUNTAMOS SI EL ACUDIENTE EXISTE
	if($acudienteNum>0){			
		$idAcudiente = $acudienteDatos[0];
		mysqli_query($conexion, "INSERT INTO academico_matriculas(mat_matricula, mat_fecha, mat_tipo_documento, mat_documento, mat_religion, mat_email, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, mat_genero, mat_fecha_nacimiento, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_tipo, mat_lugar_nacimiento, mat_lugar_expedicion, mat_acudiente, mat_estado_matricula, mat_id_usuario, mat_folio, mat_codigo_tesoreria, mat_valor_matricula, mat_inclusion, mat_extranjero, mat_tipo_sangre, mat_eps, mat_celular2, mat_ciudad_residencia, mat_nombre2)VALUES(".$result_numMat[0].",now(),".$_POST["tipoD"].",".$_POST["nDoc"].",".$_POST["religion"].",'".strtolower($_POST["email"])."','".$_POST["direccion"]."','".$_POST["barrio"]."','".$_POST["telefono"]."','".$_POST["celular"]."',".$_POST["estrato"].",".$_POST["genero"].", '".$_POST["fNac"]."', '".$_POST["apellido1"]."', '".$_POST["apellido2"]."', '".$_POST["nombres"]."','".$_POST["grado"]."','".$_POST["grupo"]."','".$_POST["tipoEst"]."','".$_POST["lNacM"]."','".$_POST["lugarD"]."',".$idAcudiente.",4, '".$idEstudianteU."', '".$_POST["folio"]."', '".$_POST["codTesoreria"]."', '".$_POST["va_matricula"]."', '".$_POST["inclusion"]."', '".$_POST["extran"]."', '".$_POST["tipoSangre"]."', '".$_POST["eps"]."', '".$_POST["celular2"]."', '".$_POST["ciudadR"]."', '".$_POST["nombre2"]."')");
		$idEstudiante = mysqli_insert_id($conexion);
			 		
	}
	//SI EL ACUDIENTE NO EXISTE, LO CREAMOS
	else{
		//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if(trim($_POST["documentoA"])=="" or trim($_POST["nombresA"])=="" or trim($_POST["generoA"])==""){
		echo "<span style='font-family:Arial; color:red;'>El acudiente no existe, por tanto debe llenar todos los campos para registrarlo.</samp>";
		exit();
	}
		mysqli_query($conexion, "INSERT INTO usuarios(uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_portada, uss_idioma, uss_tema, uss_tipo_documento, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2)VALUES('".$_POST["documentoA"]."','1234',3,'".$_POST["nombresA"]."',0,'".$_POST["ocupacionA"]."','".$_POST["email"]."','".$_POST["fechaNA"]."',0,'".$_POST["generoA"]."','".$_POST["celular"]."', 'default.png', 'default.png', 1, 'green','".$_POST["tipoDAcudiente"]."','".$_POST["lugarDa"]."', '".$_POST["direccion"]."', '".$_POST["apellido1A"]."', '".$_POST["apellido2A"]."', '".$_POST["nombre2A"]."')");
		
		$idAcudiente = mysqli_insert_id($conexion);
		
		mysqli_query($conexion, "INSERT INTO academico_matriculas(mat_matricula, mat_fecha, mat_tipo_documento, mat_documento, mat_religion, mat_email, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, mat_genero, mat_fecha_nacimiento, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_tipo, mat_lugar_nacimiento, mat_lugar_expedicion, mat_acudiente, mat_estado_matricula, mat_id_usuario, mat_folio, mat_codigo_tesoreria, mat_valor_matricula, mat_inclusion, mat_extranjero, mat_tipo_sangre, mat_eps, mat_celular2, mat_ciudad_residencia, mat_nombre2)VALUES(".$result_numMat[0].",now(),".$_POST["tipoD"].",".$_POST["nDoc"].",".$_POST["religion"].",'".strtolower($_POST["email"])."','".$_POST["direccion"]."','".$_POST["barrio"]."','".$_POST["telefono"]."','".$_POST["celular"]."',".$_POST["estrato"].",".$_POST["genero"].", '".$_POST["fNac"]."', '".$_POST["apellido1"]."', '".$_POST["apellido2"]."', '".$_POST["nombres"]."',".$_POST["grado"].",".$_POST["grupo"].",".$_POST["tipoEst"].",'".$_POST["lNacM"]."','".$_POST["lugarD"]."',".$idAcudiente.",4, '".$idEstudianteU."', '".$_POST["folio"]."', '".$_POST["codTesoreria"]."', '".$_POST["va_matricula"]."', '".$_POST["inclusion"]."', '".$_POST["extran"]."', '".$_POST["tipoSangre"]."', '".$_POST["eps"]."', '".$_POST["celular2"]."', '".$_POST["ciudadR"]."', '".$_POST["nombre2"]."')");
		$idEstudiante = mysqli_insert_id($conexion);
		
	}
	mysqli_query($conexion, "INSERT INTO usuarios_por_estudiantes(upe_id_usuario, upe_id_estudiante)VALUES('".$idAcudiente."', '".$idEstudiante."')");
	
	$idUsr = mysqli_insert_id($conexion);
	$estadoSintia=false;
	$mensajeSintia='El estudiante no pudo ser creado correctamente en SINTIA.';
	if(isset($idUsr) AND $idUsr!=''){
		$estadoSintia=true;
		$mensajeSintia='El estudiante fue creado correctamente en SINTIA.';
	}
	echo '<script type="text/javascript">window.location.href="estudiantes.php?stadsion='.$estado.'&msgsion='.$mensaje.'&stadsintia='.$estadoSintia.'&msgsintia='.$mensajeSintia.'";</script>';
	exit();