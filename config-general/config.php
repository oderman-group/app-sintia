<?php
$tiempo_inicial = microtime(true);
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/modelo/conexion.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Modulos.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");

$arregloModulos = $_SESSION["modulos"];

$Utilidades = new Utilidades; 
$Plataforma = new Plataforma;

$config = RedisInstance::getSystemConfiguration();

$informacion_inst = $_SESSION["informacionInstConsulta"];

$datosUnicosInstitucion = $_SESSION["datosUnicosInstitucion"];
$_SESSION["datosUnicosInstitucion"]["config"] = $config;

$yearArray = explode(",", $datosUnicosInstitucion['ins_years']);
$yearStart = $yearArray[0];
$yearEnd = $yearArray[1];

//CONFIGURACIÓN GENERAL
$opcionSINO =  [
    0 => "NO",
    1 => "SI",
    null => "NO"
];
$mesesAgno = array("01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio","07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
$opcionEstado = array("INACTIVO", "ACTIVO");
$estadosMatriculasEstudiantes = [
    1 => "Matriculado",
    2 => "Asistente",
    3 => "Cancelado",
    4 => "No Matriculado",
    5 => "En inscripción"
];
$clavePorDefectoUsuarios = SHA1('12345678');
$estadosEtiquetasMatriculas = array("","text-success","text-warning","text-danger","text-warning");
$opcionesGenerales = array("","T. Documento","Religion","Estratos","Generos","Nuevo/Antiguo","Dias","Nivel Educativo","Estado Civil","Estado Laboral","T. de Empresa","Si/No","T. de Vivienda","T. de Trasporte","T. de Sangre","Boletines");

$fechaDeInicio     = strtotime('2023-04-07 21:00:00');
$timestampActual   = time();
$numeroEnteroUnico = $timestampActual - $fechaDeInicio;

$tipoEstadoFinanzas = array("","ABONO","PAGO REALIZADO A TI","COBRO","POR PAGARTE");
$formasPagoFinanzas = array("N/A","Efectivo","Cheque","T. D&eacute;bito","T. Cr&eacute;dito", "Transferencia", "N/A");

$estadosSolicitudes = [
    1 => 'Pendiente',
    2 => 'En proceso',
    3 => 'Aceptada',
    4 => 'Rechazada'
];


$filtroMT = null;
if( !array_key_exists(10, $_SESSION["modulos"]) ) { 
    $filtroMT = " AND gra_tipo ='".GRADO_GRUPAL."'";
}

require_once(ROOT_PATH . "/librerias/Firebase/vendor/autoload.php");
use Kreait\Firebase\Factory;
$factory = (new Factory)
	->withServiceAccount(ROOT_PATH . '/librerias/Firebase/key/firebase_credentials.json')
	->withDatabaseUri('https://sintia-app-default-rtdb.firebaseio.com');
$storage = $factory->createStorage();