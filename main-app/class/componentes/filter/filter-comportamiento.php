<?php
require_once(ROOT_PATH . "/main-app/class/Disciplina.php");

if (!empty($filtrosDecode['carga'])) {
    $filtro .= " AND dn_id_carga='" . $filtrosDecode['carga'] . "'";
}

if (!empty($filtrosDecode['curso'])) {
    $filtro .= " AND car_curso='" . $filtrosDecode['curso'] . "'";
}

if (!empty($filtrosDecode['grupo'])) {
    $filtro .= " AND car_grupo='" . $filtrosDecode['grupo'] . "'";
    $grupo = $filtrosDecode['grupo'];
}

if (!empty($filtrosDecode['asignatura'])) {
    $filtro .= " AND car_materia='" . $filtrosDecode['asignatura'] . "'";
}

$filtroLimite = 'LIMIT 0' . ',' . $config['conf_num_registros'];

$result = Disciplina::listarComportamiento($filtro, $filtroLimite, $valor);

$index = 0;

$arraysDatos = [];

if (!empty($result)) {
    while ($fila = $result->fetch_assoc()) {
        $arraysDatos[$index] = $fila;
        $index++;
    }
}
$lista = $arraysDatos;
