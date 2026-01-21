<?php
ob_start();
include("session.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0094'; // Mismo permiso que crear factura
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Validar que se proporcionó el ID de la factura original
if (empty($_GET['id'])) {
	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">alert("ID de factura no proporcionado."); window.location.href="movimientos.php";</script>';
	exit();
}

// Decodificar ID de la factura original
$idFacturaOriginal = base64_decode($_GET['id'], true);
if ($idFacturaOriginal === false || empty($idFacturaOriginal)) {
	$idFacturaOriginal = base64_decode($_GET['id']);
	if (empty($idFacturaOriginal)) {
		include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">alert("ID de factura inválido."); window.location.href="movimientos.php";</script>';
		exit();
	}
}

$idFacturaOriginal = (int)$idFacturaOriginal;

// Obtener parámetros opcionales (nuevo usuario y nuevo valor)
$nuevoUsuario = !empty($_GET['usuario']) ? $_GET['usuario'] : null;
$nuevoValor = null;
if (!empty($_GET['valor']) && is_numeric($_GET['valor'])) {
	$nuevoValor = (float)$_GET['valor'];
}

// Requerir conexión
require_once(ROOT_PATH."/main-app/class/Conexion.php");
if (!isset($conexion)) {
	$conexion = Conexion::newConnection('mysqli');
}

// Duplicar la factura
$resultado = Movimientos::duplicarFactura(
	$conexion,
	$config,
	$idFacturaOriginal,
	$nuevoUsuario,
	$nuevoValor
);

// Guardar historial de acciones
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

// Limpiar output buffer
ob_clean();

// Redirigir según el resultado
if ($resultado['success']) {
	// Redirigir a la página de edición de la nueva factura
	header("Location: movimientos-editar.php?success=SC_DT_DUPLICAR&id=".urlencode(base64_encode((string)$resultado['id'])));
	exit();
} else {
	// Mostrar error y redirigir a la lista
	$mensajeError = urlencode($resultado['message']);
	header("Location: movimientos.php?error=ER_DT_DUPLICAR&msg=".$mensajeError);
	exit();
}