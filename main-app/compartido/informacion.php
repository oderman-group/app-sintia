<?php
include("../modell/conexion.php");
$informacionInstitucion = mysqli_query($conexion, "SELECT * FROM general_informacion WHERE info_id=1");
$infoI = mysqli_fetch_array($informacionInstitucion, MYSQLI_BOTH);
?>
