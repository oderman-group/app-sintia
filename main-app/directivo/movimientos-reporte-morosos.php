<?php
include("session.php");
$idPaginaInterna = 'DT0104';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

require_once(ROOT_PATH . "/main-app/class/Movimientos.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");

$filtro = '';

if (!empty($_GET['usuario'])) {
	$usuarioFiltro = base64_decode($_GET['usuario']);
	if (!empty($usuarioFiltro)) {
		$usuarioSeguro = mysqli_real_escape_string($conexion, $usuarioFiltro);
		$filtro .= " AND fc.fcu_usuario = '{$usuarioSeguro}'";
	}
}

if (!empty($_GET['tipo'])) {
	$tipoFiltro = base64_decode($_GET['tipo']);
	if ($tipoFiltro !== '') {
		$tipoSeguro = intval($tipoFiltro);
		$filtro .= " AND fc.fcu_tipo = {$tipoSeguro}";
	}
}

if (!empty($_GET['estadoFil'])) {
	$estadoFiltro = base64_decode($_GET['estadoFil']);
	if ($estadoFiltro !== '') {
		$estadoSeguro = mysqli_real_escape_string($conexion, $estadoFiltro);
		$filtro .= " AND fc.fcu_status = '{$estadoSeguro}'";
	}
}

if (!empty($_GET['fecha'])) {
	$fechaFiltro = base64_decode($_GET['fecha']);
	if (!empty($fechaFiltro)) {
		$fechaSeguro = mysqli_real_escape_string($conexion, $fechaFiltro);
		$filtro .= " AND fc.fcu_fecha = '{$fechaSeguro}'";
	}
}

if (!empty($_GET["desde"]) && !empty($_GET["hasta"])) {
	$desde = mysqli_real_escape_string($conexion, $_GET["desde"]);
	$hasta = mysqli_real_escape_string($conexion, $_GET["hasta"]);
	$filtro .= " AND (fc.fcu_fecha BETWEEN '{$desde}' AND '{$hasta}' OR fc.fcu_fecha LIKE '%{$hasta}%')";
}

$mostrarAnuladas = !empty($_GET['mostrarAnuladas']) && $_GET['mostrarAnuladas'] == '1';
if (!$mostrarAnuladas) {
	$filtro .= " AND fc.fcu_anulado = 0";
}

// Morosos: se enfoca en facturas de venta por cobrar
$filtro .= " AND fc.fcu_tipo = 1";

$morosos = [];
$totalPendiente = 0;
$totalFacturas = 0;

try {
	$consulta = mysqli_query(
		$conexion,
		"SELECT fc.*, uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2, uss.uss_email, uss.uss_documento, uss.uss_celular
		FROM " . BD_FINANCIERA . ".finanzas_cuentas fc
		LEFT JOIN " . BD_GENERAL . ".usuarios uss
			ON uss.uss_id = fc.fcu_usuario
			AND uss.institucion = {$config['conf_id_institucion']}
			AND uss.year = {$_SESSION["bd"]}
		WHERE fc.institucion = {$config['conf_id_institucion']}
		  AND fc.year = {$_SESSION["bd"]}
		  {$filtro}
		ORDER BY fc.fcu_fecha DESC"
	);
} catch (Exception $e) {
	$consulta = false;
	include("../compartido/error-catch-to-report.php");
}

if ($consulta) {
	while ($fila = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
		$vlrAdicional = !empty($fila['fcu_valor']) ? $fila['fcu_valor'] : 0;
		$totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $fila['fcu_id'], $vlrAdicional);
		$totalAbonos = Movimientos::calcularTotalAbonado($conexion, $config, $fila['fcu_id']);
		$saldoPendiente = $totalNeto - $totalAbonos;

		if ($saldoPendiente > 0.5) {
			$morosos[] = [
				'id' => $fila['fcu_id'],
				'consecutivo' => $fila['id_nuevo'] ?? $fila['fcu_id'],
				'fecha' => $fila['fcu_fecha'],
				'detalle' => $fila['fcu_detalle'],
				'cliente' => UsuariosPadre::nombreCompletoDelUsuario($fila),
				'correo' => $fila['uss_email'] ?? '',
				'documento' => $fila['uss_documento'] ?? '',
				'telefono' => $fila['uss_celular'] ?? '',
				'total' => $totalNeto,
				'abonos' => $totalAbonos,
				'saldo' => $saldoPendiente
			];

			$totalPendiente += $saldoPendiente;
			$totalFacturas++;
		}
	}
}

$totalPendienteFormateado = '$' . number_format($totalPendiente ?? 0, 0, ",", ".");
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Informe de morosos</title>
	<link rel="shortcut icon" href="../sintia-icono.png" />
	<style>
		body {
			font-family: Arial, sans-serif;
			color: #333;
			margin: 20px;
		}
		h1, h2 {
			margin: 0;
		}
		.header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 20px;
		}
		.header .info {
			font-size: 14px;
			line-height: 1.4;
		}
		.btn-print {
			padding: 8px 16px;
			border: none;
			background: #4CAF50;
			color: #fff;
			border-radius: 4px;
			cursor: pointer;
		}
		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
		}
		th, td {
			border: 1px solid #ddd;
			padding: 8px;
			font-size: 13px;
		}
		th {
			background: #f5f5f5;
			text-align: left;
		}
		tbody tr:nth-child(even) {
			background: #fafafa;
		}
		.summary {
			margin-top: 20px;
			font-size: 16px;
			font-weight: bold;
		}
		@media print {
			.btn-print {
				display: none;
			}
			body {
				margin: 10px;
			}
		}
	</style>
</head>
<body>
	<div class="header">
		<div>
			<h1>Informe de morosos</h1>
			<p class="info">
				<?= htmlspecialchars($informacion_inst["info_nombre"] ?? ''); ?><br>
				NIT: <?= htmlspecialchars($informacion_inst["info_nit"] ?? ''); ?><br>
				Teléfono: <?= htmlspecialchars($informacion_inst["info_telefono"] ?? ''); ?>
			</p>
		</div>
		<button class="btn-print" onclick="window.print()">Imprimir</button>
	</div>

	<h2>Resumen</h2>
	<p class="summary">
		Facturas con saldo pendiente: <?= $totalFacturas; ?> |
		Valor total pendiente: <?= $totalPendienteFormateado; ?>
	</p>

	<table>
		<thead>
			<tr>
				<th>Consecutivo</th>
				<th>Fecha</th>
				<th>Cliente</th>
				<th>Documento</th>
				<th>Contacto</th>
				<th>Correo</th>
				<th>Detalle</th>
				<th>Total</th>
				<th>Abonado</th>
				<th>Saldo pendiente</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($morosos)) { ?>
				<tr>
					<td colspan="10" style="text-align: center;">No se encontraron facturas con saldo pendiente bajo los filtros seleccionados.</td>
				</tr>
			<?php } else { ?>
				<?php foreach ($morosos as $dato) { ?>
					<tr>
						<td><?= htmlspecialchars($dato['consecutivo']); ?></td>
						<td><?= htmlspecialchars($dato['fecha']); ?></td>
						<td><?= htmlspecialchars($dato['cliente']); ?></td>
						<td><?= htmlspecialchars($dato['documento']); ?></td>
						<td><?= htmlspecialchars($dato['telefono']); ?></td>
						<td><?= htmlspecialchars($dato['correo']); ?></td>
					<td><?= htmlspecialchars($dato['detalle']); ?></td>
					<td>$<?= number_format($dato['total'] ?? 0, 0, ",", "."); ?></td>
					<td>$<?= number_format($dato['abonos'] ?? 0, 0, ",", "."); ?></td>
					<td>$<?= number_format($dato['saldo'] ?? 0, 0, ",", "."); ?></td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</body>
</html>

