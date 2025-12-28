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

// Separar ítems por tipo y calcular totales
$itemsDebito = [];
$itemsCredito = [];
$totalDebitos = 0;
$totalCreditos = 0;

foreach ($items as $item) {
    $precio = floatval($item['price'] ?? 0);
    $cantidad = floatval($item['cantity'] ?? 0);
    $descuento = floatval($item['discount'] ?? 0);
    $taxFee = floatval($item['tax_fee'] ?? 0);
    $subtotal = $precio * $cantidad * (1 - $descuento / 100);
    if (!empty($item['tax']) && $taxFee > 0) {
        $subtotal *= (1 + $taxFee / 100);
    }
    
    $itemType = $item['item_type'] ?? 'D';
    $item['subtotal_calculado'] = $subtotal;
    
    if ($itemType == 'C') {
        $itemsCredito[] = $item;
        $totalCreditos += $subtotal;
    } else {
        $itemsDebito[] = $item;
        $totalDebitos += $subtotal;
    }
}

// Calcular total neto: fcu_valor + débitos - créditos
$valorAdicional = floatval($factura['fcu_valor'] ?? 0);
$totalNeto = $valorAdicional + ($totalDebitos - $totalCreditos);
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
                <?php if ($valorAdicional > 0) { ?>
                <li><strong>Valor adicional:</strong> $<?= number_format((float)$valorAdicional, 0, ",", ".") ?></li>
                <?php } ?>
                <li><strong>Total neto:</strong> $<?= number_format((float)$totalNeto, 0, ",", ".") ?></li>
                <li><strong>Total abonado:</strong> $<?= number_format((float)$totalAbonos, 0, ",", ".") ?></li>
                <li><strong>Saldo pendiente:</strong> $<?= number_format((float)$porCobrar, 0, ",", ".") ?></li>
            </ul>
        </div>
    </div>
    <?php if (!empty($items)) { 
        // Calcular total neto de items (solo para mostrar, el total real ya incluye fcu_valor)
        $totalNetoItems = $totalDebitos - $totalCreditos;
    ?>
    <div style="margin-top: 20px;">
        <?php if (!empty($itemsDebito)) { ?>
        <h5 style="font-weight: 600; color: #667eea; margin-bottom: 10px;">Cargos</h5>
        <div class="table-responsive" style="margin-bottom: 20px;">
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
                    <?php foreach ($itemsDebito as $item) { ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name'] ?? $item['description'] ?? 'N/A') ?></td>
                        <td><?= number_format(floatval($item['cantity'] ?? 0), 0, ",", ".") ?></td>
                        <td>$<?= number_format((float)($item['price'] ?? 0), 0, ",", ".") ?></td>
                        <td><?= number_format((float)($item['discount'] ?? 0), 0, ",", ".") ?>%</td>
                        <td><?= !empty($item['tax_fee']) && $item['tax_fee'] > 0 ? number_format($item['tax_fee'], 0, ",", ".").'%' : 'N/A' ?></td>
                        <td>$<?= number_format((float)$item['subtotal_calculado'], 0, ",", ".") ?></td>
                    </tr>
                    <?php } ?>
                    <tr style="background:#f8f9fa; font-weight:600;">
                        <td colspan="5" align="right">Subtotal Cargos:</td>
                        <td>$<?= number_format((float)$totalDebitos, 0, ",", ".") ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php } ?>
        
        <?php if (!empty($itemsCredito)) { ?>
        <h5 style="font-weight: 600; color: #27ae60; margin-bottom: 10px;">Descuentos Aplicados</h5>
        <div class="table-responsive" style="margin-bottom: 20px;">
            <table class="table table-sm table-bordered">
                <thead style="background:#e8f5e9;">
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
                    <?php foreach ($itemsCredito as $item) { ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name'] ?? $item['description'] ?? 'N/A') ?></td>
                        <td><?= number_format(floatval($item['cantity'] ?? 0), 0, ",", ".") ?></td>
                        <td>$<?= number_format((float)($item['price'] ?? 0), 0, ",", ".") ?></td>
                        <td><?= number_format((float)($item['discount'] ?? 0), 0, ",", ".") ?>%</td>
                        <td><?= !empty($item['tax_fee']) && $item['tax_fee'] > 0 ? number_format($item['tax_fee'], 0, ",", ".").'%' : 'N/A' ?></td>
                        <td style="color: #27ae60;">-$<?= number_format((float)$item['subtotal_calculado'], 0, ",", ".") ?></td>
                    </tr>
                    <?php } ?>
                    <tr style="background:#e8f5e9; font-weight:600;">
                        <td colspan="5" align="right">Total Descuentos:</td>
                        <td style="color: #27ae60;">-$<?= number_format((float)$totalCreditos, 0, ",", ".") ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php } ?>
        
        <div style="margin-top: 15px; padding: 10px; background-color: #f0f0f0; border-radius: 5px;">
            <table style="width: 100%;">
                <tr>
                    <td align="right" style="padding-right: 20px;"><strong>Total Cargos:</strong></td>
                    <td align="right" style="width: 150px;">$<?= number_format((float)$totalDebitos, 0, ",", ".") ?></td>
                </tr>
                <?php if ($valorAdicional > 0) { ?>
                <tr>
                    <td align="right" style="padding-right: 20px;"><strong>Valor Adicional:</strong></td>
                    <td align="right" style="width: 150px;">$<?= number_format((float)$valorAdicional, 0, ",", ".") ?></td>
                </tr>
                <?php } ?>
                <?php if ($totalCreditos > 0) { ?>
                <tr>
                    <td align="right" style="padding-right: 20px; color: #27ae60;"><strong>Total Descuentos:</strong></td>
                    <td align="right" style="width: 150px; color: #27ae60;">-$<?= number_format((float)$totalCreditos, 0, ",", ".") ?></td>
                </tr>
                <?php } ?>
                <tr style="border-top: 2px solid #333;">
                    <td align="right" style="padding-right: 20px; padding-top: 10px;"><strong>Total Neto:</strong></td>
                    <td align="right" style="width: 150px; padding-top: 10px; font-size: 1.1em;"><strong>$<?= number_format((float)$totalNeto, 0, ",", ".") ?></strong></td>
                </tr>
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

