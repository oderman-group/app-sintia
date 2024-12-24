<?php
session_start();
$idPaginaInterna = 'CM0007';

$acceso = explode("/", $_SERVER['HTTP_REFERER']);

if (count($acceso) > 5) {
    $carpeta_actual = $acceso[5] ;
}

include_once("../../config-general/config.php");
require_once("../class/Sysjobs.php");

Modulos::validarAccesoDirectoPaginas();

$parametros = [
    "carga"   => base64_decode($_GET['carga']),
    "periodo" => base64_decode($_GET['periodo']),
    "grado"   => base64_decode($_GET['grado']),
    "grupo"   => base64_decode($_GET['grupo'])
];

try {
    $mensaje = SysJobs::registrar(JOBS_TIPO_GENERAR_INFORMES, JOBS_PRIORIDAD_BAJA, $parametros);
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");



echo '<script type="text/javascript">window.location.href="../'.$carpeta_actual.'/cargas.php";</script>';

exit();