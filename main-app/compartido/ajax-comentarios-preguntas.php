<?php
include("session-compartida.php");
require_once(ROOT_PATH . "/main-app/class/Clases.php");
$input = json_decode(file_get_contents("php://input"), true);
if (!empty($input)) {
	$_POST = $input;
}
Modulos::validarAccesoDirectoPaginas();
$filtro = "";
if (!empty($_POST["usuario"]) && $_POST["usuario"] != 0) {
	$filtro = "AND cpp.cpp_usuario = '" . $_POST["usuario"] . "'";
}
$filtro .= " AND (TRIM(cpp.cpp_padre) = ''  OR LENGTH(cpp.cpp_padre) < 0)";

$preguntasConsulta = Clases::traerPreguntasClases($conexion, $config, $_POST["claseId"], $filtro);
$usuarioActual = $_POST["usuarioActual"];

if ($preguntasConsulta) {

	$i = 0;
	foreach ($preguntasConsulta as $preguntasDatos) {
		$nivel = 0;
		$indice = $i;
		include 'clase-comentario.php';
		$i++;
	};
}
