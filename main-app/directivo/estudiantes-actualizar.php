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

// Validar que el estudiante no esté en estado "En inscripción"
if (!empty($_POST["id"])) {
	$datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($_POST["id"]);
	if (!empty($datosEstudianteActual) && $datosEstudianteActual['mat_estado_matricula'] == Estudiantes::ESTADO_EN_INSCRIPCION) {
		echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&error=ER_DT_18&message='.urlencode('No se pueden realizar modificaciones a estudiantes en estado "En inscripción"').'";</script>';
		exit();
	}
	
	// Validar cambio de estado usando el método centralizado (DEV puede Matriculado → No matriculado)
	$permisoDev = isset($datosUsuarioActual['uss_tipo']) && (int)$datosUsuarioActual['uss_tipo'] === TIPO_DEV;
	if (!empty($datosEstudianteActual) && !empty($_POST["matestM"])) {
		$estadoActual = (int)$datosEstudianteActual['mat_estado_matricula'];
		$estadoNuevo = (int)$_POST["matestM"];
		
		$validacion = Estudiantes::validarCambioEstadoMatricula($estadoActual, $estadoNuevo, $permisoDev);
		
		if (!$validacion['valido']) {
			echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&error=ER_DT_19&message='.urlencode($validacion['mensaje']).'";</script>';
			exit();
		}
	}
}

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

if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_API_SION_ACADEMICA)) {
	require_once("apis-sion-modify-student.php");
}

$fechaNacimiento  = "";
$fechaNacimientoU = "";

$fNacNorm = Utilidades::normalizarFechaParaBD($_POST["fNac"] ?? '');

if ($fNacNorm === false) {
	echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&error=ER_DT_17&message='.urlencode('Formato de fecha de nacimiento inválido. Use dd-mm-aaaa o aaaa-mm-dd.').'";</script>';
	exit();
}

if ($fNacNorm !== null) {
	$fechaTimestamp = strtotime($fNacNorm);
	$fechaMinima = strtotime('-1 year');
	if ($fechaTimestamp > $fechaMinima) {
		echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&error=ER_DT_17&message='.urlencode('La fecha de nacimiento no puede ser futura ni menor de 1 año').'";</script>';
		exit();
	}
	$fechaNacimiento  = "mat_fecha_nacimiento='" . mysqli_real_escape_string($conexion, $fNacNorm) . "', ";
	$fechaNacimientoU = "uss_fecha_nacimiento='" . mysqli_real_escape_string($conexion, $fNacNorm) . "', ";
	$_POST["fNac"] = $fNacNorm;
}

$_POST["ciudadR"] = trim($_POST["ciudadR"]);

if ($_POST["va_matricula"] == "") {
	$_POST["va_matricula"] = 0;
}

$esMediaTecnica = isset($_POST["tipoMatricula"]) && $_POST["tipoMatricula"] !== '';

if (!$esMediaTecnica) {
	$datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($_POST["id"]);
	$_POST["tipoMatricula"]=$datosEstudianteActual["mat_tipo_matricula"];
}

if (empty($_POST["tipoMatricula"])) {
	$_POST["tipoMatricula"] = GRADO_GRUPAL;
}

$procedencia = $_POST["lNac"];

if (!empty($_POST["ciudadPro"]) && !is_numeric($_POST["ciudadPro"])) {
	$procedencia = $_POST["ciudadPro"];
}

if (!empty($_FILES['fotoMat']['name'])) {
	$explode   = explode(".", $_FILES['fotoMat']['name']);
	$extension = end($explode);

	// if($extension != 'jpg' && $extension != 'png' && $extension != 'jpeg'){
	// 	echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&error=ER_DT_8";</script>';
	// 	exit();
	// }

	$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_img_') . "." . $extension;
	$destino = "../files/fotos";
	move_uploaded_file($_FILES['fotoMat']['tmp_name'], $destino . "/" . $archivo);

	$update = ['mat_foto' => $archivo];
	Estudiantes::actualizarMatriculasPorId($config, $_POST["id"], $update);

    $update = ['uss_foto' => $archivo];
    UsuariosPadre::actualizarUsuarios($config, $_POST["idU"], $update);
}

try {
	Estudiantes::actualizarEstudiantes($conexionPDO, $_POST, $fechaNacimiento, $procedencia, $pasosMatricula);
} catch (Exception $e) {
	// Si hay una excepción relacionada con validación de estado, redirigir con el mensaje
	if (strpos($e->getMessage(), 'estado') !== false || strpos($e->getMessage(), 'Matriculado') !== false || 
	    strpos($e->getMessage(), 'Asistente') !== false || strpos($e->getMessage(), 'No matriculado') !== false) {
		echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&error=ER_DT_19&message='.urlencode($e->getMessage()).'";</script>';
		exit();
	}
	// Para otras excepciones, re-lanzar
	throw $e;
}

// Sincronizar campos compartidos con la tabla usuarios
$update = [
	'uss_fecha_nacimiento'	=> $fNacNorm !== null ? $fNacNorm : NULL,
	'uss_usuario'			=> $_POST["nDoc"],
    "uss_documento"			=> $_POST["nDoc"],
    "uss_nombre"			=> mysqli_real_escape_string($conexion, $_POST["nombres"]),
    "uss_nombre2"			=> mysqli_real_escape_string($conexion, $_POST["nombre2"] ?? ''),
    "uss_apellido1"			=> mysqli_real_escape_string($conexion, $_POST["apellido1"]),
    "uss_apellido2"			=> mysqli_real_escape_string($conexion, $_POST["apellido2"] ?? ''),
    "uss_email"				=> strtolower($_POST["email"] ?? ''),
    "uss_tipo_documento"	=> $_POST["tipoD"],
    "uss_celular"			=> $_POST["celular"] ?? '',
    "uss_telefono"			=> $_POST["telefono"] ?? '',
    "uss_direccion"			=> $_POST["direccion"] ?? '',
    "uss_lugar_expedicion"	=> $_POST["lugarD"] ?? '',
    "uss_genero"			=> $_POST["genero"] ?? ''
];

UsuariosPadre::actualizarUsuarios($config, $_POST["idU"], $update);

//ACTUALIZAR EL ACUDIENTE 1	
if ($_POST["documentoA"]!="") {

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

	if (!empty($acudiente)) {

		$update = [
			"uss_usuario" => $_POST["usuarioAcudiente"],
			"uss_nombre" => mysqli_real_escape_string($conexion, $_POST["nombreA"]),
			"uss_email" => $_POST["email"],
			"uss_ocupacion" => $_POST["ocupacionA"],
			"uss_genero" => $_POST["generoA"],
			"uss_celular" => $_POST["celular"],
			"uss_lugar_expedicion" => $_POST["lugardA"],
			"uss_tipo_documento" => $_POST["tipoDAcudiente"],
			"uss_direccion" => $_POST["direccion"],
			"uss_apellido1" => mysqli_real_escape_string($conexion, $_POST["apellido1A"]),
			"uss_apellido2" => mysqli_real_escape_string($conexion, $_POST["apellido2A"]),
			"uss_nombre2" => mysqli_real_escape_string($conexion, $_POST["nombre2A"]),
			"uss_documento" => $_POST["documentoA"]
		];
		UsuariosPadre::actualizarUsuarios($config, $acudiente['uss_id'], $update);
		$idAcudiente = $acudiente['uss_id'];
	} else {
		$fechaNANorm = Utilidades::normalizarFechaParaBD($_POST["fechaNA"] ?? '');
		$fechaNACol = ($fechaNANorm !== null && $fechaNANorm !== false) ? $fechaNANorm : '2000-01-01';
		$idAcudiente = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_idioma, uss_tipo_documento, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, uss_tema_sidebar, uss_tema_header, uss_tema_logo, institucion, year, uss_id", [$_POST["documentoA"], $clavePorDefectoUsuarios, 3, mysqli_real_escape_string($conexion,$_POST["nombreA"]), 0, $_POST["ocupacionA"], $_POST["email"], $fechaNACol, 0, $_POST["generoA"], $_POST["celular"], 'default.png', 1, $_POST["tipoDAcudiente"], $_POST["lugardA"], $_POST["direccion"], mysqli_real_escape_string($conexion,$_POST["apellido1A"]), mysqli_real_escape_string($conexion,$_POST["apellido2A"]), mysqli_real_escape_string($conexion,$_POST["nombre2A"]), $_POST["documentoA"], 'white-sidebar-color', 'header-white', 'logo-white', $config['conf_id_institucion'], $_SESSION["bd"]]);
	}

	$update = ['mat_acudiente' => $idAcudiente];
	Estudiantes::actualizarMatriculasPorId($config, $_POST["id"], $update);

	try {
		mysqli_query($conexion, "DELETE FROM ".BD_GENERAL.".usuarios_por_estudiantes 
		WHERE upe_id_estudiante='".$_POST["id"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	$idInsercion  =Utilidades::generateCode("UPE");

	// Migrado a PDO - Consulta preparada
	try {
		require_once(ROOT_PATH."/main-app/class/Conexion.php");
		$conexionPDO = Conexion::newConnection('PDO');
		$sql = "INSERT INTO ".BD_GENERAL.".usuarios_por_estudiantes(
		    upe_id, upe_id_usuario, upe_id_estudiante, institucion, year
		) VALUES (?, ?, ?, ?, ?)";
		$stmt = $conexionPDO->prepare($sql);
		$stmt->bindParam(1, $idInsercion, PDO::PARAM_STR);
		$stmt->bindParam(2, $idAcudiente, PDO::PARAM_STR);
		$stmt->bindParam(3, $_POST["id"], PDO::PARAM_STR);
		$stmt->bindParam(4, $config['conf_id_institucion'], PDO::PARAM_INT);
		$stmt->bindParam(5, $_SESSION["bd"], PDO::PARAM_INT);
		$stmt->execute();
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}	
}


//ACTUALIZAR EL ACUDIENTE 2
// Obtener datos del estudiante para validar que el acudiente 2 no sea el mismo que el acudiente 1
$datosEstudianteParaValidar = Estudiantes::obtenerDatosEstudiante($_POST["id"]);
$idAcudiente1Actual = !empty($datosEstudianteParaValidar['mat_acudiente']) ? $datosEstudianteParaValidar['mat_acudiente'] : null;

if (!empty($_POST["idAcudiente2"])) {
	// Validar que el acudiente 2 no sea el mismo que el acudiente 1
	if (!empty($idAcudiente1Actual) && $_POST["idAcudiente2"] == $idAcudiente1Actual) {
		// Si el ID del acudiente 2 es el mismo que el del acudiente 1, no hacer nada
		// Esto previene que se duplique el acudiente
	} else {
		$update = [
			"uss_usuario" => $_POST["documentoA2"],
			"uss_nombre" => mysqli_real_escape_string($conexion, $_POST["nombreA2"]),
			"uss_email" => $_POST["email"] ?? '',
			"uss_ocupacion" => $_POST["ocupacionA2"] ?? '',
			"uss_genero" => $_POST["generoA2"] ?? '',
			"uss_celular" => $_POST["celular"] ?? '',
			"uss_lugar_expedicion" => $_POST["lugardA2"] ?? '',
			"uss_direccion" => $_POST["direccion"] ?? '',
			"uss_apellido1" => mysqli_real_escape_string($conexion, $_POST["apellido1A2"] ?? ''),
			"uss_apellido2" => mysqli_real_escape_string($conexion, $_POST["apellido2A2"] ?? ''),
			"uss_nombre2" => mysqli_real_escape_string($conexion, $_POST["nombre2A2"] ?? ''),
			"uss_documento" => $_POST["documentoA2"]
		];

		UsuariosPadre::actualizarUsuarios($config, $_POST["idAcudiente2"], $update);
	}
} else {
	if (!empty($_POST["documentoA2"])) {
		// Validar que el documento del acudiente 2 sea diferente al del acudiente 1
		$documentoAcudiente1 = '';
		if (!empty($idAcudiente1Actual)) {
			try {
				$acudiente1Datos = Usuarios::obtenerDatosUsuario($idAcudiente1Actual);
				if (!empty($acudiente1Datos)) {
					$documentoAcudiente1 = $acudiente1Datos['uss_documento'] ?? '';
				}
			} catch (Exception $e) {
				include("../compartido/error-catch-to-report.php");
			}
		}
		
		// Si el documento del acudiente 2 es el mismo que el del acudiente 1, no crear/actualizar
		if (!empty($documentoAcudiente1) && $_POST["documentoA2"] == $documentoAcudiente1) {
			// No hacer nada, el acudiente 2 no puede tener el mismo documento que el acudiente 1
		} else {
			try {
				$existeAcudiente2 = Usuarios::validarExistenciaUsuario($_POST["documentoA2"]);
			} catch (Exception $e) {
				include("../compartido/error-catch-to-report.php");
			}

			if ($existeAcudiente2 > 0) {
				try {
					$acudiente2 = Usuarios::obtenerDatosUsuario($_POST["documentoA2"]);
				} catch (Exception $e) {
					include("../compartido/error-catch-to-report.php");
				}

				// Validar que el usuario encontrado no sea el acudiente 1
				if (!empty($acudiente2) && (!empty($idAcudiente1Actual) && $acudiente2['uss_id'] != $idAcudiente1Actual)) {
					$update = [
						"uss_usuario" => $_POST["documentoA2"],
						"uss_nombre" => mysqli_real_escape_string($conexion, $_POST["nombreA2"]),
						"uss_email" => $_POST["email"] ?? '',
						"uss_ocupacion" => $_POST["ocupacionA2"] ?? '',
						"uss_genero" => $_POST["generoA2"] ?? '',
						"uss_celular" => $_POST["celular"] ?? '',
						"uss_lugar_expedicion" => $_POST["lugardA2"] ?? '',
						"uss_direccion" => $_POST["direccion"] ?? '',
						"uss_apellido1" => mysqli_real_escape_string($conexion, $_POST["apellido1A2"] ?? ''),
						"uss_apellido2" => mysqli_real_escape_string($conexion, $_POST["apellido2A2"] ?? ''),
						"uss_nombre2" => mysqli_real_escape_string($conexion, $_POST["nombre2A2"] ?? ''),
						"uss_documento" => $_POST["documentoA2"]
					];
					UsuariosPadre::actualizarUsuarios($config, $acudiente2['uss_id'], $update);
					$idAcudiente2 = $acudiente2['uss_id'];
				} else if (!empty($acudiente2) && !empty($idAcudiente1Actual) && $acudiente2['uss_id'] == $idAcudiente1Actual) {
					// Si el usuario encontrado es el acudiente 1, no hacer nada
					$idAcudiente2 = null;
				} else {
					// Crear nuevo acudiente 2
					$idAcudiente2 = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_ocupacion, uss_email, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_portada, uss_idioma, uss_tema, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, year, uss_id", [$_POST["documentoA2"],$clavePorDefectoUsuarios,3,mysqli_real_escape_string($conexion,$_POST["nombreA2"]),0,$_POST["ocupacionA2"] ?? '',$_POST["email"] ?? '',0,$_POST["generoA2"] ?? '',$_POST["celular"] ?? '', 'default.png', 'default.png', 1, 'green', $_POST["lugardA2"] ?? '', $_POST["direccion"] ?? '', mysqli_real_escape_string($conexion,$_POST["apellido1A2"] ?? ''), mysqli_real_escape_string($conexion,$_POST["apellido2A2"] ?? ''), mysqli_real_escape_string($conexion,$_POST["nombre2A2"] ?? ''),$_POST["documentoA2"], $config['conf_id_institucion'], $_SESSION["bd"]]);
				}
			} else {
				// Crear nuevo acudiente 2
				$idAcudiente2 = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_ocupacion, uss_email, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_portada, uss_idioma, uss_tema, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, year, uss_id", [$_POST["documentoA2"],$clavePorDefectoUsuarios,3,mysqli_real_escape_string($conexion,$_POST["nombreA2"]),0,$_POST["ocupacionA2"] ?? '',$_POST["email"] ?? '',0,$_POST["generoA2"] ?? '',$_POST["celular"] ?? '', 'default.png', 'default.png', 1, 'green', $_POST["lugardA2"] ?? '', $_POST["direccion"] ?? '', mysqli_real_escape_string($conexion,$_POST["apellido1A2"] ?? ''), mysqli_real_escape_string($conexion,$_POST["apellido2A2"] ?? ''), mysqli_real_escape_string($conexion,$_POST["nombre2A2"] ?? ''),$_POST["documentoA2"], $config['conf_id_institucion'], $_SESSION["bd"]]);
			}

			// Solo actualizar mat_acudiente2 si se creó/actualizó un acudiente 2 válido y diferente al acudiente 1
			if (!empty($idAcudiente2) && (!empty($idAcudiente1Actual) && $idAcudiente2 != $idAcudiente1Actual || empty($idAcudiente1Actual))) {
				$update = ['mat_acudiente2' => $idAcudiente2];
				Estudiantes::actualizarMatriculasPorId($config, $_POST["id"], $update);
			}
		}
	}
}

include("../compartido/guardar-historial-acciones.php");

$estadoSintia  = true;
$mensajeSintia = 'La información del estudiante se actualizó correctamente en SINTIA.';

echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($_POST["id"]).'&stadsion='.base64_encode($estado).'&msgsion='.base64_encode($mensaje).'&stadsintia='.base64_encode($estadoSintia).'&msgsintia='.base64_encode($mensajeSintia).'";</script>';

exit();