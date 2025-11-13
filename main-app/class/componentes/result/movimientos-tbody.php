<?php
if (!empty($data["dataTotal"])) {
	require_once("../Movimientos.php");
	require_once("../UsuariosPadre.php");
	require_once("../Modulos.php");
}
$contReg = 1;
$estadosCuentas = array("", "Fact. Venta", "Fact. Compra");
$estadoFil      = !empty($filtros["estado"]) ? $filtros["estado"] : "";
$tipo           = !empty($filtros["tipo"]) ? $filtros["tipo"] : "";
$desde          = !empty($filtros["desde"]) ? $filtros["desde"] : "";
$hasta          = !empty($filtros["hasta"]) ? $filtros["hasta"] : "";
foreach ($data["data"] as $resultado) {
	$bgColor = '';

	if ($resultado['fcu_anulado'] == 1) {
		$bgColor = '#ff572238';
	}

	$bgColorEstado = '#eeff0038';
	$estado = 'Por Cobrar';

	if ($resultado['fcu_status'] == COBRADA) {
		$bgColorEstado = '#00F13A38';
		$estado = 'Cobrada';
	}

	$vlrAdicional = !empty($resultado['fcu_valor']) ? $resultado['fcu_valor'] : 0;
	$totalNeto    = Movimientos::calcularTotalNeto($conexion, $config, $resultado['fcu_id'], $vlrAdicional);
	$abonos       = Movimientos::calcularTotalAbonado($conexion, $config, $resultado['fcu_id']);
	$porCobrar    = $totalNeto - $abonos;
	$usuario      = UsuariosPadre::nombreCompletoDelUsuario($resultado);
	$correoUsuario = trim($resultado['uss_email'] ?? '');
	$esFacturaCompra = ((int)$resultado['fcu_tipo'] === 2);
	$totalFormateado = number_format((float)$totalNeto, 0, ",", ".");
	$prefijoTotal = '$';
	if ($esFacturaCompra && (float)$totalNeto > 0) {
		$prefijoTotal = '-$';
	}
	$totalTexto = $prefijoTotal . $totalFormateado;
	$claseTotal = 'total-neto-cell';
	if ($esFacturaCompra) {
		$claseTotal .= ' total-compra';
	}
	$saldoPendiente = max(0, (float)$porCobrar);
	$saldoPendienteFormateado = number_format($saldoPendiente, 0, ",", ".");
	$tipoFacturaTexto = $estadosCuentas[$resultado['fcu_tipo']] ?? 'N/D';
	$claseBadgeTipo = ($resultado['fcu_tipo'] == 1) ? 'badge badge-tipo-venta' : 'badge badge-tipo-compra';
	$puedeAgregarAbono = ($resultado['fcu_anulado'] != 1 && $saldoPendiente > 0);
	$puedeRecordar = ($resultado['fcu_tipo'] == 1 && $resultado['fcu_anulado'] != 1 && $saldoPendiente > 0);
?>
	<tr id="reg<?= $resultado['fcu_id']; ?>" style="background-color:<?= $bgColor; ?>;" class="movimiento-row" data-factura-id="<?= $resultado['fcu_id']; ?>">
		<td><i class="fa fa-chevron-right detalle-movimiento-btn" data-id="<?= $resultado['fcu_id']; ?>"></i></td>
		<td align="center">
			<input type="checkbox"
				class="factura-checkbox"
				data-factura="<?= $resultado['fcu_id']; ?>"
				data-consecutivo="<?= htmlspecialchars($resultado['id_nuevo_movimientos'], ENT_QUOTES); ?>"
				data-usuario="<?= htmlspecialchars($usuario, ENT_QUOTES); ?>"
				data-email="<?= htmlspecialchars($correoUsuario, ENT_QUOTES); ?>"
				data-saldo="<?= $saldoPendiente; ?>"
				<?= $puedeRecordar ? '' : 'disabled'; ?>>
		</td>
		<td><?= $contReg; ?></td>
		<td><?= $resultado['id_nuevo_movimientos']; ?></td>
		<td>
			<a href="<?= $_SERVER['PHP_SELF']; ?>?estadoFil=<?= base64_encode($estadoFil); ?>&usuario=<?= base64_encode($usuario) ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?= base64_encode($tipo); ?>&fecha=<?= base64_encode($resultado['fcu_fecha']); ?>" style="text-decoration: underline;"><?= $resultado['fcu_fecha']; ?></a>
		</td>
		<td><?= $resultado['fcu_detalle']; ?></td>
		<td id="totalNeto<?= $resultado['fcu_id']; ?>" class="<?= $claseTotal; ?>" data-tipo="<?= $resultado['fcu_tipo'] ?>" data-anulado="<?= $resultado['fcu_anulado'] ?>" data-total-neto="<?= (float)$totalNeto ?>"><?= $totalTexto ?></td>
		<td id="abonos<?= $resultado['fcu_id']; ?>" data-abonos="<?= (float)$abonos ?>">$<?= !empty($abonos) ? number_format((float)$abonos, 0, ",", ".") : 0 ?></td>
		<td id="porCobrar<?= $resultado['fcu_id']; ?>" data-por-cobrar="<?= (float)$porCobrar ?>">$<?= !empty($porCobrar) ? number_format((float)$porCobrar, 0, ",", ".") : 0 ?></td>
		<td>
			<a href="<?= $_SERVER['PHP_SELF']; ?>?estadoFil=<?= base64_encode($estadoFil); ?>&usuario=<?= base64_encode($usuario); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?= base64_encode($resultado['fcu_tipo']); ?>&fecha=<?= base64_encode($fecha); ?>" style="text-decoration: none;">
				<span class="<?= $claseBadgeTipo; ?>"><?= $tipoFacturaTexto; ?></span>
			</a>
		</td>
		<td>
			<a href="<?= $_SERVER['PHP_SELF']; ?>?estadoFil=<?= base64_encode($estadoFil); ?>&usuario=<?= base64_encode($resultado['uss_id']); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?= base64_encode($tipo); ?>&fecha=<?= base64_encode($fecha); ?>" style="text-decoration: underline;"><?= $usuario; ?></a>
		</td>
		<td align="center" style="background-color:<?= $bgColorEstado; ?>; color: black;"><?= $estado ?></td>
		<?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0128', 'DT0089'])) { ?>
			<td style="position: relative;">
				<div class="btn-group" style="position: static;">
					<button type="button" class="btn btn-primary"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></button>
					<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu" role="menu" style="z-index: 999999 !important;">
						<?php if (Modulos::validarSubRol(['DT0128'])) { ?>
							<li><a href="movimientos-editar.php?id=<?= base64_encode($resultado['fcu_id']); ?>"><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?></a></li>
						<?php } ?>
						<li><a href="javascript:void(0);" onClick="verAbonosFactura('<?= $resultado['fcu_id']; ?>','<?= htmlspecialchars($resultado['id_nuevo_movimientos'], ENT_QUOTES); ?>')">Ver abonos</a></li>
						<?php if ($resultado['fcu_anulado'] != 1 && $abonos <= 0 && $resultado['fcu_status'] == POR_COBRAR && Modulos::validarSubRol(['DT0089'])) { ?>
							<li id="anulado<?= $resultado['fcu_id']; ?>"><a href="javascript:void(0);" onClick="anularMovimiento(this)" data-id-registro="<?= $resultado['fcu_id']; ?>" data-id-usuario="<?= $resultado['uss_id']; ?>">Anular</a></li>
						<?php } ?>
						<li><a href="javascript:void(0);" onClick="sincronizarAbonos('<?= $resultado['fcu_id']; ?>','<?= htmlspecialchars($resultado['id_nuevo_movimientos'], ENT_QUOTES); ?>')">Sync abonos</a></li>
						<?php if ($puedeAgregarAbono) { ?>
							<li><a href="javascript:void(0);" onClick="abrirModalAbonoRapido('<?= $resultado['fcu_id']; ?>', '<?= htmlspecialchars($resultado['id_nuevo_movimientos'], ENT_QUOTES); ?>', '<?= $saldoPendienteFormateado; ?>')">Agregar abono</a></li>
						<?php } ?>
						<?php if ($puedeRecordar) { ?>
							<li><a href="javascript:void(0);" onClick="enviarRecordatorioFactura('<?= $resultado['fcu_id']; ?>')">Enviar recordatorio</a></li>
						<?php } ?>
						<?php if ($resultado['fcu_tipo'] == 1 && $resultado['fcu_anulado'] != 1 && $saldoPendiente > 0) { ?>
							<li><a href="javascript:void(0);" onClick="bloquearUsuarioFactura('<?= $resultado['uss_id']; ?>','<?= $resultado['fcu_id']; ?>','<?= htmlspecialchars($resultado['id_nuevo_movimientos'], ENT_QUOTES); ?>','<?= $saldoPendienteFormateado; ?>')">Bloquear usuario</a></li>
						<?php } ?>
						<?php if (Modulos::validarSubRol(['DT0255'])) { ?>
							<li><a href="movimientos-factura-venta.php?id=<?= base64_encode($resultado['fcu_id']); ?>" target="_blank"><?= $frases[57][$datosUsuarioActual['uss_idioma']]; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</td>
		<?php } ?>
	</tr>
<?php $contReg++;
} ?>