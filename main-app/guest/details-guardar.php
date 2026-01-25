<?php

require_once("../class/Estudiantes.php");
require_once("../class/servicios/MediaTecnicaServicios.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
if (isset($_SESSION["id"]) and $_SESSION["id"] != "") {
	require_once(ROOT_PATH . "/main-app/modelo/conexion.php");
} else {
	$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
}

//VALIDAMOS QUE EL ESTUDIANTE NO SE ENCUENTRE CREADO
if (empty($_POST["tipoMatricula"])) {
	$_POST["tipoMatricula"] = GRADO_INDIVIDUAL;
}
if (empty($_POST["va_matricula"]))  $_POST["va_matricula"]  = 0;
if (empty($_POST["grupo"]))         $_POST["grupo"]         = 1;
if (empty($_POST["tipoEst"]))       $_POST["tipoEst"]       = 128;
if (empty($_POST["fNac"]))          $_POST["fNac"]          = '2000-01-01';
if (empty($_POST["tipoD"]))         $_POST["tipoD"]         = 107;
if (empty($_POST["genero"]))        $_POST["genero"]        = 126;


if (empty($_POST["religion"]))      $_POST["religion"]      = 112;
if (empty($_POST["estrato"]))       $_POST["estrato"]       = 116;
if (empty($_POST["extran"]))        $_POST["extran"]        = 0;
if (empty($_POST["inclusion"]))     $_POST["inclusion"]     = 0;

if (empty($_POST["celular"]))        $_POST["celular"]        = 00000;
if (empty($_POST["lugarD"]))        $_POST["lugarD"]        = "";
if (empty($_POST["direccion"]))        $_POST["direccion"]        = "";
if (empty($_POST["apellido2"]))        $_POST["apellido2"]        = "";
if (empty($_POST["nombre2"]))        $_POST["nombre2"]        = "";
$grupo = "4";
$idEstudianteU = Utilidades::generateCode("USS");



$consultaExisteUsuario = mysqli_query($conexion, "SELECT uss_usuario FROM " . BD_GENERAL . ".usuarios
WHERE uss_documento ='" . $_POST["identificacion"]  . "' AND institucion={$_POST['institucion']} AND year={$_POST["year"]}");
$numUsuario = mysqli_num_rows($consultaExisteUsuario);

//si no existe el usuario lo creamos
if (mysqli_num_rows($consultaExisteUsuario) == 0) {
	try {
		mysqli_query($conexion, "INSERT INTO " . BD_GENERAL . ".usuarios(uss_id, 
			uss_usuario, 
			uss_clave, 
			uss_tipo, 
			uss_nombre, 
			uss_estado, 
			uss_email, 
			uss_fecha_nacimiento, 
			uss_permiso1, 
			uss_genero, 
			uss_celular, 
			uss_foto, 
			uss_idioma, 
			uss_tipo_documento, 
			uss_lugar_expedicion, 
			uss_direccion, 
			uss_apellido1, 
			uss_apellido2, 
			uss_nombre2,
			uss_documento, 
			uss_tema_sidebar,
			uss_tema_header,
			uss_tema_logo, 
			uss_bloqueado, 
			institucion, year
			)VALUES('" . $idEstudianteU . "', 
			'" .	$_POST["usuario1"] . "',
			SHA1('" . $_POST["identificacion"] . "'),
			4,
			'" . $_POST["nombre"] . "',
			0,
			'" . strtolower($_POST["correo"]) . "',
			'" . $_POST["fNac"] . "',
			0,
			'" . $_POST["genero"] . "',
			'" . $_POST["celular"] . "', 
			'default.png', 
			1, 
			'" . $_POST["tipoD"] . "',
			'" . $_POST["lugarD"] . "', 
			'" . $_POST["direccion"] . "', 
			'" . $_POST["apellido"] . "', 
			'" . $_POST["apellido2"] . "', 
			'" . $_POST["nombre2"] . "',
			'" . $_POST["identificacion"] . "',
			'white-sidebar-color',
			'header-white',
			'logo-white', 
			0, 
			{$_POST["institucion"]}, {$_POST["year"]}
			)");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
}

//VALIDAMOS QUE EL ESTUDIANTE NO SE ENCUENTRE CREADO
$consultaExisteMatricula = mysqli_query($conexion, "SELECT * FROM " . BD_ACADEMICA . ".academico_matriculas
            WHERE (mat_id='" . $_POST["identificacion"] . "' || mat_documento='" . $_POST["identificacion"] . "') AND mat_eliminado=0 AND institucion={$_POST["institucion"]} AND year={$_POST["year"]}");
$matricula = mysqli_fetch_array($consultaExisteMatricula, MYSQLI_BOTH);
$numMatricula = mysqli_num_rows($consultaExisteMatricula);
//si no existe la matricula la creamos
if (mysqli_num_rows($consultaExisteMatricula) == 0) {
	$config['conf_id_institucion'] = $_POST["institucion"];
	$config['conf_agno'] = $_POST["year"];
	$_SESSION["bd"] = $_POST["year"];
	$codigoMAT = Utilidades::generateCode("MAT");
	$result_numMat = strtotime("now");

	$consulta = "INSERT INTO " . BD_ACADEMICA . ".academico_matriculas(
                mat_id,
				mat_matricula,
				mat_fecha, 
                mat_documento,
				mat_email, 
				mat_primer_apellido,
                mat_nombres,
				mat_grado, 
				mat_grupo,  
				mat_tipo_matricula, 
				institucion, 
				year)
                VALUES(
				'" . $codigoMAT . "', 
				'" . $result_numMat . "',
				now(),
				'" . $_POST["identificacion"] . "',
				'" . $_POST["correo"] . "',
				'" . $_POST["apellido"] . "',
				'" . $_POST["nombre"] . "',
				'" . $_POST["curso"] . "',
				'" . $grupo . "',
				'" . $_POST["tipoMatricula"] . "',
				'" . $_POST["institucion"] . "',
				'" . $_POST["year"] . "')";
	try {
		mysqli_query($conexion, $consulta);
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
} else {
	$codigoMAT = $matricula["mat_id"];
}
//Insertamos la matrícula en media tecnica
try {
	// consultamos si ya existe
	$parametros = [
		'matcur_institucion' => $_POST["institucion"],
		'matcur_years' => $_POST["year"],
		'matcur_id_curso' => $_POST["curso"],
		'matcur_id_matricula' => $matricula["mat_id"]
	];
	$matriculaCurso = MediaTecnicaServicios::consultar($parametros);
	if(empty($matriculaCurso)){
		MediaTecnicaServicios::guardarPorCurso($codigoMAT, $_POST["curso"], 1, ESTADO_CURSO_NO_APROBADO,$_POST["institucion"],$_POST["year"]);
	}
	
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}


$url = '../pagos-online/index.php';
// URL de la página a la que deseas enviar los datos POST

// Datos que deseas enviar (en este caso, los datos de $_POST)
$_POST["guest"]=true;
$_POST["idUsuario"]=$_POST["identificacion"];
$_POST["emailUsuario"]=$_POST["correo"];
$_POST["documentoUsuario"]=$_POST["identificacion"];
$_POST["nombreUsuario"]=$_POST["nombre"]." ".$_POST["apellido"];
$_POST["celularUsuario"]=$_POST["celular"];;
$_POST["idInstitucion"]=$_POST["institucion"];
$_POST["nombre"]="Cruso de ".$_POST["nombreCurso"];
$_POST["usuario"]=$_POST["usuario1"];
$_POST["matricula"]=$codigoMAT;

$_POST["url_origen"]=$_SERVER["HTTP_REFERER"];
$data = $_POST;

// Crear una cadena de consulta con los datos
$query_string = http_build_query($data);

// Redirigir a la página de destino con los datos en la URL
header("Location: $url?$query_string");
exit();
