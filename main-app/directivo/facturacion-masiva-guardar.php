<?php
include("session.php");
$idPaginaInterna = 'DT0277';
require_once(ROOT_PATH."/main-app/class/Movimientos.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

Modulos::validarAccesoDirectoPaginas();

if(!Modulos::validarSubRol([$idPaginaInterna])){
	require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Incluir historial después de las validaciones para evitar redirecciones prematuras
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Validar campos requeridos
if(empty($_POST["lote_nombre"]) || empty($_POST["tipo_grupo"]) || empty($_POST["items"])){
	require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">alert("Debe completar todos los campos requeridos"); window.location.href="facturacion-masiva.php";</script>';
	exit();
}

// Preparar datos del lote
$datosLote = [
	'lote_nombre' => $_POST["lote_nombre"],
	'tipo_grupo' => $_POST["tipo_grupo"],
	'criterios' => [],
	'lote_observaciones' => !empty($_POST["lote_observaciones"]) ? $_POST["lote_observaciones"] : ''
];

// Preparar criterios según tipo de grupo
if ($_POST["tipo_grupo"] === 'ESTUDIANTES') {
	if (empty($_POST["grados"]) || !is_array($_POST["grados"])) {
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">alert("Debe seleccionar al menos un grado para estudiantes"); window.location.href="facturacion-masiva.php";</script>';
		exit();
	}
	
	$datosLote['criterios']['grados'] = $_POST["grados"];
	if (!empty($_POST["grupos"]) && is_array($_POST["grupos"])) {
		$datosLote['criterios']['grupos'] = $_POST["grupos"];
	}
} else {
	if (!empty($_POST["estado_usuario"])) {
		$datosLote['criterios']['estado'] = $_POST["estado_usuario"];
	}
}

// Preparar ítems y cantidades
$items = $_POST["items"];
$cantidades = !empty($_POST["cantidades"]) ? $_POST["cantidades"] : array_fill(0, count($items), 1);

// Generar facturas masivas
$resultado = Movimientos::generarFacturasMasivas($conexion, $config, $datosLote, $items, $cantidades);

if ($resultado['success']) {
	$mensaje = "Se generaron {$resultado['total_facturas']} facturas exitosamente.";
	if (!empty($resultado['errores'])) {
		$mensaje .= "\\n\\nErrores encontrados: " . count($resultado['errores']);
		$mensaje .= "\\n\\nDetalles de errores:\\n" . implode("\\n", array_slice($resultado['errores'], 0, 5));
		if (count($resultado['errores']) > 5) {
			$mensaje .= "\\n... y " . (count($resultado['errores']) - 5) . " errores más.";
		}
	}
	require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">alert("' . addslashes($mensaje) . '"); window.location.href="movimientos.php";</script>';
} else {
	require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	$mensajeError = "Error al generar facturas masivas.\\n\\nErrores:\\n" . implode("\\n", array_slice($resultado['errores'], 0, 10));
	if (count($resultado['errores']) > 10) {
		$mensajeError .= "\\n... y " . (count($resultado['errores']) - 10) . " errores más.";
	}
	echo '<script type="text/javascript">alert("' . addslashes($mensajeError) . '"); window.location.href="facturacion-masiva.php";</script>';
}
exit();

