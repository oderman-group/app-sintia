<?php
session_start();
include("../../config-general/config.php");
require_once("../class/UsuariosPadre.php");

header('Content-Type: application/json');

$term = isset($_GET['term']) ? mysqli_real_escape_string($conexion, $_GET['term']) : '';

try {
	// Consultar solo usuarios que tienen facturas asociadas
	$sql = "SELECT DISTINCT uss.uss_id, uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2, pes.pes_nombre
		FROM ".BD_GENERAL.".usuarios uss
		INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_usuario = uss.uss_id
		INNER JOIN ".BD_ADMIN.".general_perfiles pes ON pes.pes_id = uss.uss_tipo
		WHERE uss.institucion = {$config['conf_id_institucion']} 
		AND uss.year = {$_SESSION["bd"]}
		AND fc.institucion = {$config['conf_id_institucion']} 
		AND fc.year = {$_SESSION["bd"]}";
	
	// Agregar filtro de búsqueda si hay término
	if (!empty($term)) {
		$sql .= " AND (
			TRIM(uss.uss_nombre) LIKE '%{$term}%'
			OR TRIM(uss.uss_nombre2) LIKE '%{$term}%'
			OR TRIM(uss.uss_apellido1) LIKE '%{$term}%'
			OR TRIM(uss.uss_apellido2) LIKE '%{$term}%'
			OR CONCAT(TRIM(uss.uss_nombre), ' ', TRIM(uss.uss_nombre2), ' ', TRIM(uss.uss_apellido1), ' ', TRIM(uss.uss_apellido2)) LIKE '%{$term}%'
			OR CONCAT(TRIM(uss.uss_nombre), TRIM(uss.uss_nombre2), TRIM(uss.uss_apellido1), TRIM(uss.uss_apellido2)) LIKE '%{$term}%'
			OR CONCAT(TRIM(uss.uss_apellido1), ' ', TRIM(uss.uss_apellido2), ' ', TRIM(uss.uss_nombre), ' ', TRIM(uss.uss_nombre2)) LIKE '%{$term}%'
			OR CONCAT(TRIM(uss.uss_apellido1), TRIM(uss.uss_apellido2), TRIM(uss.uss_nombre), TRIM(uss.uss_nombre2)) LIKE '%{$term}%'
		)";
	}
	// Si no hay término, devolver todos los usuarios (limitado a 50)
	
	$sql .= " ORDER BY uss.uss_apellido1, uss.uss_apellido2, uss.uss_nombre LIMIT 50";
	
	$lista = mysqli_query($conexion, $sql);
	$response = [];
	
	if ($lista) {
		while($dato = mysqli_fetch_array($lista, MYSQLI_BOTH)){
			$nombre = UsuariosPadre::nombreCompletoDelUsuario($dato) . " - " . $dato["pes_nombre"];
			$response[] = ["value" => $dato["uss_id"], "label" => $nombre];
		}
	}
	
	echo json_encode($response);
} catch (Exception $e) {
	echo json_encode([]);
}