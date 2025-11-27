<?php
include("../modelo/conexion.php");
include("../class/Utilidades.php");

$options = '';
$maxPeriodos = 4; // Default number of periods
// If you have access to $config, use $config['conf_periodos_maximos'] or $config[19] as mentioned
for ($i = 1; $i <= $maxPeriodos; $i++) {
    $options .= "<option value='$i'>Período $i</option>";
}

echo $options;
?>