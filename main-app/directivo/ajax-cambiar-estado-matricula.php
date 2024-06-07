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

$update = "mat_estado_matricula=".$_POST["nuevoEstado"]."";
Estudiantes::actualizarMatriculasPorId($config, $_POST["idEstudiante"], $update);
?>  
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <p>
        El estado de la matrícula fue cambiado a <b><?=$estadosMatriculasEstudiantes[$_POST["nuevoEstado"]];?></b> correctamente.
    </p>
</div>
<?php
require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>