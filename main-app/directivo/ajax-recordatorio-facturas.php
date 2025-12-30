<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0104';

header('Content-Type: application/json; charset=utf-8');

if (!Modulos::validarSubRol([$idPaginaInterna])) {
	echo json_encode([
		'success' => false,
		'message' => 'Acceso no autorizado.'
	]);
	exit();
}

require_once(ROOT_PATH . "/main-app/class/Movimientos.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/EnviarEmail.php");

$facturas = $_POST['facturas'] ?? [];
if (!is_array($facturas)) {
	$facturas = [$facturas];
}
$facturas = array_unique(array_filter(array_map('trim', $facturas)));

if (empty($facturas)) {
	echo json_encode([
		'success' => false,
		'message' => 'No se recibieron facturas para procesar.'
	]);
	exit();
}

$enviadas = [];
$omitidas = [];

foreach ($facturas as $facturaId) {
	if ($facturaId === '') {
		continue;
	}

	$facturaIdSeguro = mysqli_real_escape_string($conexion, $facturaId);
	$detalles = Movimientos::obtenerDetallesFactura($conexion, $config, $facturaIdSeguro);

	if (empty($detalles['factura'])) {
		$omitidas[] = [
			'factura' => $facturaId,
			'razon'   => 'Factura no encontrada'
		];
		continue;
	}

	$factura = $detalles['factura'];

	if ((int)$factura['fcu_anulado'] === 1) {
		$omitidas[] = [
			'factura' => $factura['fcu_consecutivo'] ?? $factura['fcu_id'],
			'razon'   => 'Factura anulada'
		];
		continue;
	}

	if ((int)$factura['fcu_tipo'] !== 1) {
		$omitidas[] = [
			'factura' => $factura['fcu_consecutivo'] ?? $factura['fcu_id'],
			'razon'   => 'Factura de compra'
		];
		continue;
	}

	$vlrAdicional = !empty($factura['fcu_valor']) ? $factura['fcu_valor'] : 0;
	$totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $factura['fcu_id'], $vlrAdicional);
	$totalAbonos = Movimientos::calcularTotalAbonado($conexion, $config, $factura['fcu_id']);
	$saldoPendiente = $totalNeto - $totalAbonos;

	if ($saldoPendiente <= 0.5) {
		$omitidas[] = [
			'factura' => $factura['fcu_consecutivo'] ?? $factura['fcu_id'],
			'razon'   => 'Saldo pendiente en cero'
		];
		continue;
	}

	$datosUsuario = UsuariosPadre::sesionUsuario($factura['fcu_usuario']);
	if (empty($datosUsuario)) {
		$omitidas[] = [
			'factura' => $factura['fcu_consecutivo'] ?? $factura['fcu_id'],
			'razon'   => 'Usuario no encontrado'
		];
		continue;
	}

	// Validar que el correo electrónico esté presente y sea válido
	$correoUsuario = !empty($datosUsuario['uss_email']) ? trim($datosUsuario['uss_email']) : '';
	
	// Validar formato de email usando la misma función que EnviarEmail
	// Esto evita que EnviarEmail::mensajeError() haga un redirect que rompe el flujo AJAX
	if (empty($correoUsuario) || !is_string($correoUsuario)) {
		$omitidas[] = [
			'factura' => $factura['fcu_consecutivo'] ?? $factura['fcu_id'],
			'razon'   => 'El usuario no tiene correo registrado'
		];
		continue;
	}
	
	// Validar formato usando la misma regex que EnviarEmail::validarEmail()
	$regex = "/^[A-z0-9\\._-]+@[A-z0-9][A-z0-9-]*(\\.[A-z0-9_-]+)*\\.([A-z]{2,6})$/";
	if (!preg_match($regex, $correoUsuario)) {
		$omitidas[] = [
			'factura' => $factura['fcu_consecutivo'] ?? $factura['fcu_id'],
			'razon'   => 'El correo del usuario no tiene un formato válido'
		];
		continue;
	}

	$nombreUsuario = UsuariosPadre::nombreCompletoDelUsuario($datosUsuario);
	$consecutivo = $factura['fcu_consecutivo'] ?? $factura['fcu_id'];
	$fechaFactura = !empty($factura['fcu_fecha']) ? $factura['fcu_fecha'] : '';
	$saldoFormateado = '$' . number_format($saldoPendiente, 0, ",", ".");
	$detalle = $factura['fcu_detalle'] ?? '';
	$asunto = 'Recordatorio de saldo pendiente - ' . $consecutivo;

	$dataEmail = [
		'institucion_id'      => $config['conf_id_institucion'],
		'institucion_nombre'  => $informacion_inst["info_nombre"] ?? 'Plataforma SINTIA',
		'usuario_id'          => $datosUsuario['uss_id'] ?? '',
		'usuario_nombre'      => $nombreUsuario,
		'usuario_email'       => $correoUsuario,
		'usuario2_email'      => $datosUsuario['uss_email2'] ?? '',
		'usuario3_email'      => $datosUsuario['uss_email3'] ?? '',
		'usuario2_nombre'     => $datosUsuario['uss_nombre2'] ?? '',
		'usuario3_nombre'     => $datosUsuario['uss_nombre3'] ?? '',
		'consecutivo_factura' => $consecutivo,
		'fecha_factura'       => $fechaFactura,
		'saldo_pendiente'     => $saldoPendiente,
		'saldo_formateado'    => $saldoFormateado,
		'detalle_factura'     => $detalle,
		'url_portal'          => REDIRECT_ROUTE
	];

	$bodyTemplateRoute = ROOT_PATH . '/config-general/plantilla-email-recordatorio-saldo.php';

	try {
		EnviarEmail::enviar($dataEmail, $asunto, $bodyTemplateRoute, null, null);
		$enviadas[] = [
			'factura' => $consecutivo,
			'destinatario' => $correoUsuario
		];
	} catch (Exception $e) {
		$mensajeError = $e->getMessage();
		// Si el error es de validación de email, usar un mensaje más corto
		if (strpos($mensajeError, 'correo electrónico inválido') !== false || strpos($mensajeError, 'Correo electrónico inválido') !== false) {
			$mensajeError = 'Correo electrónico inválido';
		}
		$omitidas[] = [
			'factura' => $consecutivo,
			'razon'   => 'Error al enviar: ' . $mensajeError
		];
	}
}

$totalEnviadas = count($enviadas);
$totalOmitidas = count($omitidas);

$mensaje = 'Recordatorios enviados: ' . $totalEnviadas;
if ($totalOmitidas > 0) {
	$mensaje .= ' | Omitidas: ' . $totalOmitidas;
}

echo json_encode([
	'success' => $totalEnviadas > 0,
	'message' => $mensaje,
	'detalle' => [
		'enviadas' => $enviadas,
		'omitidas' => $omitidas
	]
]);
exit();

