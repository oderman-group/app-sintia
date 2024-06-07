<?php
include("session-compartida.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0058';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

function posicion ($conexion, $config, $idRegistro, $posicionInicial, $docente) {
    global $filtroMT;

    try {
        $registro = CargaAcademica::consultarPosicionCarga($config, $docente, $idRegistro, $posicionInicial, $filtroMT);

        $update = "car_posicion_docente=" . $posicionInicial . "";
        CargaAcademica::actualizarCargaPorID($config, $idRegistro, $update);

        if( !empty($registro) ) {
            return posicion($conexion, $config, $registro['car_id'], $posicionInicial + 1, $docente);
        }

        return 1;
    } catch (Exception $e) {
        return $e;
    }
}

echo posicion($conexion, $config, $_GET["idCarga"], $_GET["posicionNueva"], $_GET["docente"]);