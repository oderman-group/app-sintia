<?php
session_start();
include("../../config-general/config.php");
require_once("../class/UsuariosPadre.php");

header('Content-Type: application/json');

$term = isset($_GET['term']) ? mysqli_real_escape_string($conexion, $_GET['term']) : '';
$todosUsuarios = isset($_GET['todos']) && $_GET['todos'] == '1';

try {
	// Si se solicita listar todos los usuarios (para crear nueva transacción)
	if ($todosUsuarios) {
		$sql = "SELECT DISTINCT uss.uss_id, 
			COALESCE(TRIM(uss.uss_nombre), '') as uss_nombre, 
			COALESCE(TRIM(uss.uss_nombre2), '') as uss_nombre2, 
			COALESCE(TRIM(uss.uss_apellido1), '') as uss_apellido1, 
			COALESCE(TRIM(uss.uss_apellido2), '') as uss_apellido2, 
			COALESCE(TRIM(pes.pes_nombre), 'N/A') as pes_nombre
			FROM ".BD_GENERAL.".usuarios uss
			LEFT JOIN ".BD_ADMIN.".general_perfiles pes ON pes.pes_id = uss.uss_tipo
			WHERE uss.institucion = {$config['conf_id_institucion']} 
			AND uss.year = {$_SESSION["bd"]}";
	} else {
		// Consultar solo usuarios que tienen facturas asociadas (comportamiento por defecto)
		$sql = "SELECT DISTINCT uss.uss_id, 
			COALESCE(TRIM(uss.uss_nombre), '') as uss_nombre, 
			COALESCE(TRIM(uss.uss_nombre2), '') as uss_nombre2, 
			COALESCE(TRIM(uss.uss_apellido1), '') as uss_apellido1, 
			COALESCE(TRIM(uss.uss_apellido2), '') as uss_apellido2, 
			COALESCE(TRIM(pes.pes_nombre), 'N/A') as pes_nombre
			FROM ".BD_GENERAL.".usuarios uss
			INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_usuario = uss.uss_id
			INNER JOIN ".BD_ADMIN.".general_perfiles pes ON pes.pes_id = uss.uss_tipo
			WHERE uss.institucion = {$config['conf_id_institucion']} 
			AND uss.year = {$_SESSION["bd"]}
			AND fc.institucion = {$config['conf_id_institucion']} 
			AND fc.year = {$_SESSION["bd"]}";
	}
	
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
			// Asegurar que todos los campos estén presentes y limpios
			$datoLimpio = [
				'uss_id' => $dato['uss_id'] ?? '',
				'uss_nombre' => isset($dato['uss_nombre']) ? trim($dato['uss_nombre']) : '',
				'uss_nombre2' => isset($dato['uss_nombre2']) ? trim($dato['uss_nombre2']) : '',
				'uss_apellido1' => isset($dato['uss_apellido1']) ? trim($dato['uss_apellido1']) : '',
				'uss_apellido2' => isset($dato['uss_apellido2']) ? trim($dato['uss_apellido2']) : '',
			];
			
			// Usar directamente el método de la clase UsuariosPadre
			$nombreCompleto = UsuariosPadre::nombreCompletoDelUsuario($datoLimpio);
			$pesNombre = isset($dato["pes_nombre"]) ? trim($dato["pes_nombre"]) : 'N/A';
			$label = $nombreCompleto . " - " . $pesNombre;
			$response[] = ["value" => $dato["uss_id"], "label" => $label];
		}
	}
	
	echo json_encode($response);
} catch (Exception $e) {
	echo json_encode([]);
}