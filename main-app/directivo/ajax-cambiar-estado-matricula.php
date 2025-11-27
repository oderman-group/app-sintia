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

// Validar el cambio de estado usando el método centralizado
$validacion = Estudiantes::validarCambioEstadoMatricula($estadoActual, $estadoNuevo);

if (!$validacion['valido']) {
	echo '<div class="alert alert-danger">';
	echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
	echo '<p><strong>Error:</strong> ' . htmlspecialchars($validacion['mensaje'], ENT_QUOTES, 'UTF-8') . '</p>';
	echo '</div>';
	exit();
}

// Si la validación es exitosa, proceder con la actualización
$update = [
    'mat_estado_matricula' => $estadoNuevo
];
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