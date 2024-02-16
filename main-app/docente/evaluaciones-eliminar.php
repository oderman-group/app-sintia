<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0140';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");

$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}

Evaluaciones::eliminarPreguntasEvaluacion($conexion, $config, $idR);

//Eliminamos los archivos de respuestas de las preguntas de esta evaluacion.
Evaluaciones::traerResultadoEvaluacion($conexion, $config, $idR);

$rutaEntregas = ROOT_PATH."/main-app/files/evaluaciones";
while($registroEntregas = mysqli_fetch_array($rEntregas, MYSQLI_BOTH)){
    if(file_exists($ruta."/".$registro['res_archivo'])){
        unlink($ruta."/".$registro['res_archivo']);	
    }
}

Evaluaciones::eliminarResultadosEvaluacion($conexion, $config, $idR);

Evaluaciones::eliminarEstudiantesEvaluacion($conexion, $config, $idR);

Evaluaciones::eliminarEvaluacion($conexion, $config, $idR);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="evaluaciones.php?error=ER_DT_3";</script>';
exit();