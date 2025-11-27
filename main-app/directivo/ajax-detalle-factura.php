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

require_once(ROOT_PATH."/main-app/class/Movimientos.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

$idFactura = $_GET['idFactura'] ?? '';

if (empty($idFactura)) {
    echo json_encode([
        'success' => false,
        'message' => 'Factura no especificada.'
    ]);
    exit();
}

$detalles = Movimientos::obtenerDetallesFactura($conexion, $config, $idFactura);

if (empty($detalles['factura'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Factura no encontrada.'
    ]);
    exit();
}

$factura = $detalles['factura'];
$items = $detalles['items'];

$totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $idFactura, floatval($factura['fcu_valor'] ?? 0));
$totalAbonos = Movimientos::calcularTotalAbonado($conexion, $config, $idFactura);
$porCobrar = $totalNeto - $totalAbonos;

$responsable = UsuariosPadre::nombreCompletoDelUsuario([
    'uss_nombre'    => $factura['uss_nombre'] ?? '',
    'uss_nombre2'   => $factura['uss_nombre2'] ?? '',
    'uss_apellido1' => $factura['uss_apellido1'] ?? '',
    'uss_apellido2' => $factura['uss_apellido2'] ?? '',
]);

ob_start();
?>
<div class="detalle-factura-wrapper" style="padding: 15px;">
    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 220px;">
            <h5 style="font-weight: 600; color: #667eea;">Información de la factura</h5>
            <ul style="list-style:none; padding:0; margin:0;">
                <li><strong>Código:</strong> <?= htmlspecialchars($factura['id_nuevo'] ?? '') ?></li>
                <li><strong>Fecha:</strong> <?= htmlspecialchars($factura['fcu_fecha'] ?? '') ?></li>
                <li><strong>Detalle:</strong> <?= htmlspecialchars($factura['fcu_detalle'] ?? 'N/A') ?></li>
                <li><strong>Usuario:</strong> <?= $responsable ?: 'N/A' ?></li>
                <li><strong>Estado:</strong> <?= htmlspecialchars($factura['fcu_status'] ?? '') ?></li>
            </ul>
        </div>
        <div style="flex: 1; min-width: 220px;">
            <h5 style="font-weight: 600; color: #667eea;">Resumen financiero</h5>
            <ul style="list-style:none; padding:0; margin:0;">
                <li><strong>Total neto:</strong> $<?= number_format((float)$totalNeto, 0, ",", ".") ?></li>
                <li><strong>Total abonado:</strong> $<?= number_format((float)$totalAbonos, 0, ",", ".") ?></li>
                <li><strong>Saldo pendiente:</strong> $<?= number_format((float)$porCobrar, 0, ",", ".") ?></li>
            </ul>
        </div>
    </div>
    <?php if (!empty($items)) { ?>
    <div style="margin-top: 20px;">
        <h5 style="font-weight: 600; color: #667eea;">Items</h5>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio unit.</th>
                        <th>Descuento</th>
                        <th>Impuesto</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalItems = 0;
                    foreach ($items as $item) { 
                        $precio = floatval($item['price'] ?? 0);
                        $cantidad = floatval($item['cantity'] ?? 0);
                        $descuento = floatval($item['discount'] ?? 0);
                        $taxFee = floatval($item['tax_fee'] ?? 0);
                        $subtotal = $precio * $cantidad * (1 - $descuento / 100);
                        if (!empty($item['tax']) && $taxFee > 0) {
                            $subtotal *= (1 + $taxFee / 100);
                        }
                        $totalItems += $subtotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['description'] ?? $item['item_name'] ?? 'N/A') ?></td>
                        <td><?= number_format($cantidad, 0, ",", ".") ?></td>
                        <td>$<?= number_format((float)$precio, 0, ",", ".") ?></td>
                        <td><?= number_format((float)$descuento, 0, ",", ".") ?>%</td>
                        <td><?= $taxFee > 0 ? number_format($taxFee, 0, ",", ".").'%' : 'N/A' ?></td>
                        <td>$<?= number_format((float)$subtotal, 0, ",", ".") ?></td>
                    </tr>
                    <?php } ?>
                    <tr style="background:#f8f9fa; font-weight:600;">
                        <td colspan="5" align="right">Total items:</td>
                        <td>$<?= number_format((float)$totalItems, 0, ",", ".") ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php } else { ?>
        <div style="margin-top:15px; color:#6c757d;">Esta factura no tiene items asociados.</div>
    <?php } ?>
</div>
<?php
$html = ob_get_clean();

echo json_encode([
    'success' => true,
    'html' => $html
]);

