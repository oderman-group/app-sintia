<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0122';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");

// ============================================
// VALIDAR FECHAS EN EL BACKEND
// ============================================
$errorFechas = [];
if (empty($_POST['desde'])) {
    $errorFechas[] = 'La fecha de inicio es requerida';
}
if (empty($_POST['hasta'])) {
    $errorFechas[] = 'La fecha límite es requerida';
}

if (empty($errorFechas)) {
    try {
        $fechaDesde = new DateTime($_POST['desde']);
        $fechaHasta = new DateTime($_POST['hasta']);
        $fechaActual = new DateTime();
        $fechaActual->setTime(0, 0, 0);
        
        if ($fechaDesde->setTime(0, 0, 0) < $fechaActual) {
            $errorFechas[] = 'La fecha de inicio no puede ser anterior a hoy';
        }
        
        if ($fechaHasta <= $fechaDesde) {
            $errorFechas[] = 'La fecha límite debe ser posterior a la fecha de inicio';
        }
    } catch (Exception $e) {
        $errorFechas[] = 'Error al validar fechas: ' . $e->getMessage();
    }
}

if (!empty($errorFechas)) {
    echo '<script type="text/javascript">alert("ERROR: ' . addslashes(implode('\\n', $errorFechas)) . '"); window.history.back();</script>';
    exit();
}
// ============================================

$codigo=Evaluaciones::actualizarEvaluacion($conexion,$config,$_POST);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="evaluaciones.php?success=SC_DT_2&id='.base64_encode($_POST["idR"]).'";</script>';
exit();