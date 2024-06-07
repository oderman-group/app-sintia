<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0118';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
require_once(ROOT_PATH."/main-app/class/Cronograma.php");

$idInsercion = Cronograma::guardarCronograma($conexion, $conexionPDO, $config, $_POST, $cargaConsultaActual, $periodoConsultaActual);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="cronograma-calendario.php?success=SC_DT_1&id='.base64_encode($idInsercion).'";</script>';
exit();