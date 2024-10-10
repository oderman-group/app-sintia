<?php

require_once("../../class/CargaAcademica.php");

if (!empty($filtrosDecode['curso'])) {
    $filtro .= " AND car_curso='" . $filtrosDecode['curso'] . "'";
}
if (!empty($filtrosDecode["grupo"])) {
    $filtro .= " AND car_grupo='" . $filtrosDecode["grupo"] . "'";
}
if (!empty($filtrosDecode["docente"])) {
    $filtro .= " AND car_docente='" .$filtrosDecode["docente"]. "'";
}
if (!empty($filtrosDecode["asignatura"])) {
    $filtro .= " AND car_materia='" . $filtrosDecode["asignatura"] . "'";
}

$selectSql = ["car_id","car_periodo","car_curso","car_ih","car_permiso2",
			  "car_indicador_automatico","car_maximos_indicadores",
			  "car_docente","gra_tipo","am.mat_id",
			  "car_maximas_calificaciones","car_director_grupo","uss_nombre",
			  "uss_nombre2","uss_apellido1","uss_apellido2","gra_id","gra_nombre",
			  "gru_nombre","mat_nombre","mat_valor","car_grupo","car_director_grupo"];
$filtroLimite = '';

$result = CargaAcademica::listarCargas($conexion, $config,  "", $filtro, "mat_id, car_grupo", $filtroLimite, $valor, $filtro2,$selectSql);
$index = 0;


$arraysDatos = [];

if (!empty($result)) {
    while ($fila = $result->fetch_assoc()) {
        $arraysDatos[$index] = $fila;
        $index++;
    }
}

$lista = $arraysDatos;
