<?php
require_once(ROOT_PATH."/main-app/class/Actividades.php");
$spcd = Actividades::consultarPorcentajeActividades($config, $cargaSP, $periodoSP);
$spcr = Actividades::consultarPorcentajeActividadesRegistradas($config, $cargaSP, $periodoSP);
?>