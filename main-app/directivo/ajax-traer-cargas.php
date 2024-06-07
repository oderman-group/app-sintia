<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$consultaCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_POST["grado"], $_POST["grupo"]);
while($datosCargas = mysqli_fetch_array($consultaCargas, MYSQLI_BOTH)){
    $nombre=$datosCargas['uss_nombre']." ".$datosCargas['uss_nombre2']." ".$datosCargas['uss_apellido1']." ".$datosCargas['uss_apellido1'];
    echo '<option value="'.$datosCargas['car_id'].'">['.$datosCargas['car_id'].'] - '.$datosCargas['mat_nombre'].' ('.strtoupper($nombre).')</option>';
}