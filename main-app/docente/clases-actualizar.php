<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0112';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

require_once(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");

Clases::actualizarClase($conexion, $config, $_POST, $_FILES);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="clases.php?success=SC_DT_2&id='.base64_encode($_POST["idR"]).'&carga='.base64_encode($cargaConsultaActual).'&periodo='.base64_encode($periodoConsultaActual).'";</script>';
exit();