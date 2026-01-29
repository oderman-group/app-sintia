<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0217';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Obtener el estado actual del estudiante
$datosEstudiante = Estudiantes::obtenerDatosEstudiante($_POST["idEstudiante"]);
if (empty($datosEstudiante)) {
	echo '<div class="alert alert-danger">';
	echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
	echo '<p><strong>Error:</strong> No se encontró el estudiante.</p>';
	echo '</div>';
	exit();
}

$estadoActual = (int)$datosEstudiante['mat_estado_matricula'];
$estadoNuevo = (int)$_POST["nuevoEstado"];

$permisoDev = isset($datosUsuarioActual['uss_tipo']) && (int)$datosUsuarioActual['uss_tipo'] === TIPO_DEV;

// Validar el cambio de estado usando el método centralizado (DEV puede Matriculado → No matriculado)
$validacion = Estudiantes::validarCambioEstadoMatricula($estadoActual, $estadoNuevo, $permisoDev);

if (!$validacion['valido']) {
	echo '<div class="alert alert-danger">';
	echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
	echo '<p><strong>Error:</strong> ' . htmlspecialchars($validacion['mensaje'], ENT_QUOTES, 'UTF-8') . '</p>';
	echo '</div>';
	exit();
}

// Si la validación es exitosa, proceder con la actualización
// Detectar cambio de estado de no matriculado (4) a matriculado (1)
// o si mat_fecha está vacío/inválido y el estado es matriculado
$actualizarFechaMatricula = false;
if ($estadoActual === Estudiantes::ESTADO_NO_MATRICULADO && $estadoNuevo === Estudiantes::ESTADO_MATRICULADO) {
    $actualizarFechaMatricula = true;
} elseif ($estadoNuevo === Estudiantes::ESTADO_MATRICULADO) {
    // Si el estado nuevo es matriculado y la fecha está vacía o es inválida, actualizarla
    $fechaMatriculaActual = $datosEstudiante['mat_fecha'] ?? '';
    if (empty($fechaMatriculaActual) || $fechaMatriculaActual === '0000-00-00' || $fechaMatriculaActual === '0000-00-00 00:00:00') {
        $actualizarFechaMatricula = true;
    }
}

$update = [
    'mat_estado_matricula' => $estadoNuevo
];

// Si se debe actualizar la fecha de matrícula, agregarla al update
if ($actualizarFechaMatricula) {
    $update['mat_fecha'] = date('Y-m-d H:i:s');
}

Estudiantes::actualizarMatriculasPorId($config, $_POST["idEstudiante"], $update);
?>  
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <p>
        El estado de la matrícula fue cambiado a <b><?=$estadosMatriculasEstudiantes[$estadoNuevo];?></b> correctamente.
    </p>
</div>
<?php
require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>