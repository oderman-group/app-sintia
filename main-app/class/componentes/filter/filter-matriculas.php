<?php
require_once("../../class/Estudiantes.php");
require_once("../../class/servicios/GradoServicios.php");

$cursoActual=null;

if (!empty($filtrosDecode['curso'])) {
    $filtro .= " AND mat_grado='" . $filtrosDecode['curso'] . "'";
    $curso = $filtrosDecode['curso'];
    $cursoActual=GradoServicios::consultarCurso($curso);
    if (!empty($cursoActual) && $cursoActual["gra_tipo"] == GRADO_INDIVIDUAL) {
        $filtro = "";
    }
}

if (!empty($filtrosDecode['estadoM'])) {
    $filtro .= " AND mat_estado_matricula='" . $filtrosDecode['estadoM'] . "'";
}

if (!empty($filtrosDecode['grupo'])) {
    $filtro .= " AND mat_grupo ='" . $filtrosDecode['grupo'] . "'";
}

$filtroLimite = 'LIMIT 0'.','.$config['conf_num_registros'];
													
$selectSql = ["mat.*",
            "uss.uss_id","uss.uss_usuario","uss.uss_bloqueado",
            "gra_nombre","gru_nombre","gra_formato_boletin",
            "acud.uss_nombre","acud.uss_nombre2","acud.uss_nombre2", "mat.id_nuevo AS mat_id_nuevo"];

$result = Estudiantes::listarEstudiantes(0, $filtro, $filtroLimite, $cursoActual, $valor, $selectSql);

$index = 0;

$arraysDatos = [];

if (!empty($result)) {
    while ($fila = $result->fetch_assoc()) {
        $arraysDatos[$index] = $fila;
        $index++;
    }
}
$lista = $arraysDatos;
