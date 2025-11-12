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
?>
	<tr id="reg<?= $resultado['fcu_id']; ?>" style="background-color:<?= $bgColor; ?>;">
		<td><?= $contReg; ?></td>
		<td><?= $resultado['id_nuevo_movimientos']; ?></td>
		<td>
			<a href="<?= $_SERVER['PHP_SELF']; ?>?estadoFil=<?= base64_encode($estadoFil); ?>&usuario=<?= base64_encode($usuario) ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?= base64_encode($tipo); ?>&fecha=<?= base64_encode($resultado['fcu_fecha']); ?>" style="text-decoration: underline;"><?= $resultado['fcu_fecha']; ?></a>
		</td>
		<td><?= $resultado['fcu_detalle']; ?></td>
		<td id="totalNeto<?= $resultado['fcu_id']; ?>" data-tipo="<?= $resultado['fcu_tipo'] ?>" data-anulado="<?= $resultado['fcu_anulado'] ?>" data-total-neto="<?= $totalNeto ?>">$<?= !empty($totalNeto) ? number_format((float)$totalNeto, 0, ",", ".") : 0 ?></td>
		<td id="abonos<?= $resultado['fcu_id']; ?>" data-abonos="<?= $abonos ?>">$<?= !empty($abonos) ? number_format((float)$abonos, 0, ",", ".") : 0 ?></td>
		<td id="porCobrar<?= $resultado['fcu_id']; ?>" data-por-cobrar="<?= $porCobrar ?>">$<?= !empty($porCobrar) ? number_format((float)$porCobrar, 0, ",", ".") : 0 ?></td>
		<td>
			<a href="<?= $_SERVER['PHP_SELF']; ?>?estadoFil=<?= base64_encode($estadoFil); ?>&usuario=<?= base64_encode($usuario); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?= base64_encode($resultado['fcu_tipo']); ?>&fecha=<?= base64_encode($fecha); ?>" style="text-decoration: underline;"><?= $estadosCuentas[$resultado['fcu_tipo']]; ?></a>
		</td>
		<td>
			<a href="<?= $_SERVER['PHP_SELF']; ?>?estadoFil=<?= base64_encode($estadoFil); ?>&usuario=<?= base64_encode($resultado['uss_id']); ?>&desde=<?= $desde; ?>&hasta=<?= $hasta; ?>&tipo=<?= base64_encode($tipo); ?>&fecha=<?= base64_encode($fecha); ?>" style="text-decoration: underline;"><?= UsuariosPadre::nombreCompletoDelUsuario($resultado); ?></a>
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
						<?php if ($resultado['fcu_anulado'] != 1 && $abonos <= 0 && $resultado['fcu_status'] == POR_COBRAR && Modulos::validarSubRol(['DT0089'])) { ?>
							<li id="anulado<?= $resultado['fcu_id']; ?>"><a href="javascript:void(0);" onClick="anularMovimiento(this)" data-id-registro="<?= $resultado['fcu_id']; ?>" data-id-usuario="<?= $resultado['uss_id']; ?>">Anular</a></li>
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