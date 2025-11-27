<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0112';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_academico_asignaciones_estudiantes.php");

include("verificar-carga.php");
include("verificar-periodos-diferentes.php");

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

Actividades::actualizarActividad($conexion, $config, $_POST, $_FILES, $storage);

try {
    $estudiantes = isset($_POST["estudiantes"]) ? $_POST["estudiantes"] : [];
    Actividades::actualizarAsignacionActividad($_POST["idR"], $estudiantes);
} catch (Exception $e) {
    include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
    exit();
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="actividades-editar.php?success=SC_DT_2&idR='.base64_encode($_POST["idR"]).'";</script>';
exit();