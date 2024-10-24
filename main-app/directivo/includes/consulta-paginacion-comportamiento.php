<?php
$nombrePagina = "comportamiento.php";
if (empty($_REQUEST["nume"])) {
    $_REQUEST["nume"] = base64_encode(1);
}
$consulta = Disciplina::listarComportamiento($filtro, '');
$numRegistros = !empty($consulta) ? mysqli_num_rows($consulta) : 0;
//$numRegistros = count($keys);
$registros = $config['conf_num_registros'];
$pagina = base64_decode($_REQUEST["nume"]);
$pagina = isset($_REQUEST['nume']) ? intval($pagina) : base64_encode(1);
if (is_numeric($pagina)) {
    $inicio = (($pagina - 1) * $registros);
} else {
    $inicio = 1;
}
