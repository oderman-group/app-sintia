<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once ROOT_PATH."/main-app/class/Modulos.php";
require_once(ROOT_PATH."/main-app/class/servicios/MediaTecnicaServicios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/AuditoriaLogger.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0192';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Verificar token CSRF
Csrf::verificar();

include("../compartido/historial-acciones-guardar.php");

$_POST["ciudadR"] = trim($_POST["ciudadR"]);

function isAjaxRequest(){
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
function jsonResponse($payload){
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit();
}
// Función auxiliar para codificar valores que pueden ser null
$encodeParam = function($value) {
    return base64_encode($value ?? '');
};

$parametrosPost='&tipoD='.$encodeParam($_POST["tipoD"] ?? null).'&documento='.$encodeParam($_POST["nDoc"] ?? null).'&religion='.$encodeParam($_POST["religion"] ?? null).'&email='.$encodeParam($_POST["email"] ?? null).'&direcion='.$encodeParam($_POST["direccion"] ?? null).'&barrio='.$encodeParam($_POST["barrio"] ?? null).'&telefono='.$encodeParam($_POST["telefono"] ?? null).'&celular='.$encodeParam($_POST["celular"] ?? null).'&estrato='.$encodeParam($_POST["estrato"] ?? null).'&genero='.$encodeParam($_POST["genero"] ?? null).'&nacimiento='.$encodeParam($_POST["fNac"] ?? null).'&apellido1='.$encodeParam($_POST["apellido1"] ?? null).'&apellido2='.$encodeParam($_POST["apellido2"] ?? null).'&nombre='.$encodeParam($_POST["nombres"] ?? null).'&grado='.$encodeParam($_POST["grado"] ?? null).'&grupo='.$encodeParam($_POST["grupo"] ?? null).'&tipoE='.$encodeParam($_POST["tipoEst"] ?? null).'&lugarEx='.$encodeParam($_POST["lugarD"] ?? null).'&lugarNac='.$encodeParam($_POST["lNac"] ?? null).'&matricula='.$encodeParam($_POST["matricula"] ?? null).'&folio='.$encodeParam($_POST["folio"] ?? null).'&tesoreria='.$encodeParam($_POST["codTesoreria"] ?? null).'&vaMatricula='.$encodeParam($_POST["va_matricula"] ?? null).'&inclusion='.$encodeParam($_POST["inclusion"] ?? null).'&extran='.$encodeParam($_POST["extran"] ?? null).'&tipoSangre='.$encodeParam($_POST["tipoSangre"] ?? null).'&eps='.$encodeParam($_POST["eps"] ?? null).'&celular2='.$encodeParam($_POST["celular2"] ?? null).'&ciudadR='.$encodeParam($_POST["ciudadR"] ?? null).'&nombre2='.$encodeParam($_POST["nombre2"] ?? null).'&documentoA='.$encodeParam($_POST["documentoA"] ?? null).'&nombreA='.$encodeParam($_POST["nombreA"] ?? null).'&ocupacionA='.$encodeParam($_POST["ocupacionA"] ?? null).'&generoA='.$encodeParam($_POST["generoA"] ?? null).'&expedicionA='.$encodeParam($_POST["lugardA"] ?? null).'&tipoDocA='.$encodeParam($_POST["tipoDAcudiente"] ?? null).'&apellido1A='.$encodeParam($_POST["apellido1A"] ?? null).'&apellido2A='.$encodeParam($_POST["apellido2A"] ?? null).'&nombre2A='.$encodeParam($_POST["nombre2A"] ?? null).'&matestM='.$encodeParam($_POST["matestM"] ?? null);

//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
if (trim($_POST["nDoc"])=="" or trim($_POST["apellido1"])=="" or trim($_POST["nombres"])=="" or trim($_POST["grado"])=="" or trim($_POST["documentoA"])=="") {
    if (isAjaxRequest()) {
        jsonResponse(['ok'=>false,'message'=>'Campos obligatorios faltantes. Verifique los resaltados.']);
    }
    include("../compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_4'.$parametrosPost.'";</script>';
    exit();
}

//VALIDAMOS QUE EL ESTUDIANTE NO SE ENCUENTRE CREADO
$valiEstudiante = Estudiantes::validarExistenciaEstudiante($_POST["nDoc"]);

if ($valiEstudiante > 0) {
    if (isAjaxRequest()) {
        jsonResponse(['ok'=>false,'message'=>'El estudiante ya existe con ese documento.','field'=>'nDoc']);
    }
    include("../compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_5'.$parametrosPost.'";</script>';
    exit();
}

$fNac = isset($_POST["fNac"]) ? trim($_POST["fNac"]) : '';
if ($fNac !== '') {
    $birth = DateTime::createFromFormat('Y-m-d', $fNac);
    $errors = DateTime::getLastErrors();
    $today = new DateTime('today');
    $maxDate = (clone $today)->modify('-1 year');

    $invalidFormat = !$birth || $errors['warning_count'] > 0 || $errors['error_count'] > 0;
    $tooRecent = !$invalidFormat && $birth > $maxDate; // futuro o menor de 1 año

    if ($invalidFormat || $tooRecent) {
        if (isAjaxRequest()) {
            jsonResponse(['ok'=>false,'message'=>'Fecha de nacimiento inválida o menor de 1 año.','field'=>'fNac']);
        }
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_FNAC'.$parametrosPost.'";</script>';
        exit();
    }
}

$result_numMat = strtotime("now");

if (empty($_POST["tipoMatricula"])) {
	$_POST["tipoMatricula"]=GRADO_GRUPAL;
}

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


//Api solo para MODULO_API_SION_ACADEMICA
$estado  ='';
$mensaje ='';

if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_API_SION_ACADEMICA)) {
	require_once("apis-sion-create-student.php");
}

$procedencia = $_POST["lNacM"];

if (!empty($_POST["ciudadPro"]) && !is_numeric($_POST["ciudadPro"])) {
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
        if (isAjaxRequest()) {
            jsonResponse(['ok'=>false,'message'=>'Datos del acudiente incompletos.','field'=>'documentoA']);
        }
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_6'.$parametrosPost.'";</script>';
        exit();
    }

	if(empty($_POST["generoA"]))		$_POST["generoA"]       = 126;
	if(empty($_POST["ocupacionA"]))		$_POST["ocupacionA"]    = 'NO REGISTRA OCUPACIÓN';
	if(empty($_POST["fechaNA"]))		$_POST["fechaNA"]       = '2000-01-01';
	if(empty($_POST["folio"]))       	$_POST["folio"]       	='';
	if(empty($_POST["codTesoreria"]))   $_POST["codTesoreria"]  = '';
	if(empty($_POST["tipoSangre"]))     $_POST["tipoSangre"]    = '';
	if(empty($_POST["eps"]))       		$_POST["eps"]       	= 126;
	if(empty($_POST["matestM"]))       	$_POST["matestM"]       = NO_MATRICULADO;

    $fechaNA = isset($_POST["fechaNA"]) ? trim($_POST["fechaNA"]) : '';

    if ($fechaNA !== '') {
        $birthA = DateTime::createFromFormat('Y-m-d', $fechaNA);
        $errorsA = DateTime::getLastErrors();
        $todayA = new DateTime('today');
        $maxDateA = (clone $todayA)->modify('-14 years');

        $invalidFormatA = !$birthA || $errorsA['warning_count'] > 0 || $errorsA['error_count'] > 0;
        $tooRecentA = !$invalidFormatA && $birthA > $maxDateA; // menor de 14 años

        if ($invalidFormatA || $tooRecentA) {
            if (isAjaxRequest()) {
                jsonResponse(['ok'=>false,'message'=>'La fecha de nacimiento del acudiente debe ser de al menos 14 años.','field'=>'fechaNA']);
            }
            include("../compartido/guardar-historial-acciones.php");
            echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_FNAC_A'.$parametrosPost.'";</script>';
            exit();
        }
    }

	$idAcudiente = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_idioma,uss_tipo_documento, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2,uss_documento, uss_tema_sidebar, uss_tema_header, uss_tema_logo, institucion, year, uss_id", [$_POST["documentoA"], $clavePorDefectoUsuarios, 3, mysqli_real_escape_string($conexion,$_POST["nombresA"]), 0, $_POST["ocupacionA"], $_POST["email"], $_POST["fechaNA"], 0, $_POST["generoA"], $_POST["celular"], 'default.png', 1, $_POST["tipoDAcudiente"], $_POST["lugarDa"], $_POST["direccion"], mysqli_real_escape_string($conexion,$_POST["apellido1A"]), mysqli_real_escape_string($conexion,$_POST["apellido2A"]), mysqli_real_escape_string($conexion,$_POST["nombre2A"]), 	$_POST["documentoA"], 'white-sidebar-color', 'header-white', 'logo-white', $config['conf_id_institucion'], $_SESSION["bd"]]);
}

// Validación básica de email en backend
if (!empty($_POST["email"]) && !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    if (isAjaxRequest()) {
        jsonResponse(['ok'=>false,'message'=>'El correo electrónico no es válido.','field'=>'email']);
    }
    include("../compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="estudiantes-agregar.php?error=ER_DT_EMAIL'.$parametrosPost.'";</script>';
    exit();
}

$idEstudianteU = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_genero, uss_celular, uss_foto, uss_idioma, uss_tipo_documento, uss_lugar_expedicion, uss_direccion, uss_apellido1, uss_apellido2, uss_nombre2,uss_documento, uss_tema_sidebar,uss_tema_header,uss_tema_logo, institucion, year, uss_id", [$_POST["nDoc"], $clavePorDefectoUsuarios, 4, mysqli_real_escape_string($conexion,$_POST["nombres"]), 0, strtolower($_POST["email"]), $_POST["fNac"], 0, $_POST["genero"], $_POST["celular"], 'default.png', 1, $_POST["tipoD"], $_POST["lugarD"], $_POST["direccion"], mysqli_real_escape_string($conexion,$_POST["apellido1"]), mysqli_real_escape_string($conexion,$_POST["apellido2"]), mysqli_real_escape_string($conexion,$_POST["nombre2"]), $_POST["nDoc"], 'white-sidebar-color', 'header-white', 'logo-white', $config['conf_id_institucion'], $_SESSION["bd"]]);

//Insertamos la matrícula
$idEstudiante = Estudiantes::insertarEstudiantes(
	$conexionPDO, 
	$_POST, 
	$idEstudianteU, 
	$result_numMat, 
	$procedencia, 
	$idAcudiente, 
	Estudiantes::CREAR_MATRICULA, 
	$config['conf_id_institucion'], 
	$_SESSION["bd"]
);

// Registrar auditoría de creación de estudiante
AuditoriaLogger::registrarCreacion(
	'ESTUDIANTES',
	$idEstudiante,
	'Creado estudiante: ' . $_POST["nombres"] . ' ' . $_POST["apellido1"] . ' (Doc: ' . $_POST["nDoc"] . ')',
	[
		'nombre_completo' => $_POST["nombres"] . ' ' . $_POST["apellido1"] . ' ' . $_POST["apellido2"],
		'documento' => $_POST["nDoc"],
		'tipo_documento' => $_POST["tipoD"],
		'email' => $_POST["email"],
		'grado' => $_POST["grado"],
		'grupo' => $_POST["grupo"],
		'numero_matricula' => $result_numMat
	]
);

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

// Si es una petición AJAX, devolver JSON para UX mejorado
if (isAjaxRequest()) {
    jsonResponse([
        'ok' => true,
        'id' => $idEstudiante,
        'editUrl' => 'estudiantes-editar.php?id='.base64_encode($idEstudiante),
        'sion' => ['estado' => $estado, 'mensaje' => $mensaje],
        'sintia' => ['estado' => $estadoSintia, 'mensaje' => $mensajeSintia]
    ]);
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="estudiantes-editar.php?id='.base64_encode($idEstudiante).'&stadsion='.base64_encode($estado).'&msgsion='.base64_encode($mensaje).'&stadsintia='.base64_encode($estadoSintia).'&msgsintia='.base64_encode($mensajeSintia).'";</script>';
exit();