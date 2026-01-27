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

// Separar ítems por tipo (ya vienen ordenados: débitos primero, créditos después)
$itemsDebito = [];
$itemsCredito = [];

foreach ($items as $item) {
    $itemType = $item['item_type'] ?? 'D';
    
    if ($itemType == 'C') {
        $itemsCredito[] = $item;
    } else {
        $itemsDebito[] = $item;
    }
}

// Usar el método centralizado para calcular todos los totales correctamente
$valorAdicional = floatval($factura['fcu_valor'] ?? 0);
$totales = Movimientos::calcularTotalesFactura($conexion, $config, $idFactura, $valorAdicional, TIPO_FACTURA);
$totalAbonos = Movimientos::calcularTotalAbonado($conexion, $config, $idFactura);
$porCobrar = $totales['total_neto'] - $totalAbonos;

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
                <li><strong>Código:</strong> <?= htmlspecialchars((isset($factura['fcu_consecutivo']) && $factura['fcu_consecutivo'] !== '' && $factura['fcu_consecutivo'] !== null) ? $factura['fcu_consecutivo'] : ($factura['id_nuevo_movimientos'] ?? $factura['fcu_id'] ?? '')) ?></li>
                <li><strong>Fecha:</strong> <?= htmlspecialchars($factura['fcu_fecha'] ?? '') ?></li>
                <li><strong>Detalle:</strong> <?= htmlspecialchars($factura['fcu_detalle'] ?? 'N/A') ?></li>
                <li><strong>Usuario:</strong> <?= $responsable ?: 'N/A' ?></li>
                <li><strong>Estado:</strong> <?= htmlspecialchars($factura['fcu_status'] ?? '') ?></li>
            </ul>
        </div>
        <div style="flex: 1; min-width: 220px;">
            <h5 style="font-weight: 600; color: #667eea;">Resumen financiero</h5>
            <ul style="list-style:none; padding:0; margin:0;">
                <li><strong>Subtotal Bruto:</strong> $<?= number_format((float)$totales['subtotal_bruto'], 0, ",", ".") ?></li>
                <li><strong>Descuentos de Ítems:</strong> -$<?= number_format((float)$totales['descuentos_items'], 0, ",", ".") ?></li>
                <li><strong>Descuentos Comerciales:</strong> -$<?= number_format((float)$totales['descuentos_comerciales_globales'], 0, ",", ".") ?></li>
                <li><strong>Subtotal Gravable:</strong> $<?= number_format((float)$totales['subtotal_gravable'], 0, ",", ".") ?></li>
                <li><strong>Impuestos:</strong> $<?= number_format((float)$totales['impuestos'], 0, ",", ".") ?></li>
                <li><strong>Total Facturado:</strong> $<?= number_format((float)$totales['total_facturado'], 0, ",", ".") ?></li>
                <li><strong>Anticipos/Saldos a Favor:</strong> -$<?= number_format((float)$totales['anticipos_saldos_favor'], 0, ",", ".") ?></li>
                <?php if ($valorAdicional > 0) { ?>
                <li><strong>Valor adicional:</strong> $<?= number_format((float)$valorAdicional, 0, ",", ".") ?></li>
                <?php } ?>
                <li style="border-top: 1px solid #ddd; padding-top: 5px; margin-top: 5px;"><strong>Total Neto:</strong> $<?= number_format((float)$totales['total_neto'], 0, ",", ".") ?></li>
                <li><strong>Total abonado:</strong> $<?= number_format((float)$totalAbonos, 0, ",", ".") ?></li>
                <li><strong>Saldo pendiente:</strong> $<?= number_format((float)$porCobrar, 0, ",", ".") ?></li>
            </ul>
        </div>
    </div>
    <?php if (!empty($items)) { ?>
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
                    <?php foreach ($itemsDebito as $item) { 
                        $subtotalItem = floatval($item['subtotal'] ?? 0);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name'] ?? $item['description'] ?? 'N/A') ?></td>
                        <td><?= number_format(floatval($item['cantity'] ?? 0), 0, ",", ".") ?></td>
                        <td>$<?= number_format((float)($item['price'] ?? 0), 0, ",", ".") ?></td>
                        <td><?= number_format((float)($item['discount'] ?? 0), 0, ",", ".") ?>%</td>
                        <td><?= !empty($item['tax_fee']) && $item['tax_fee'] > 0 ? number_format($item['tax_fee'], 0, ",", ".").'%' : 'N/A' ?></td>
                        <td>$<?= number_format($subtotalItem, 0, ",", ".") ?></td>
                    </tr>
                    <?php } ?>
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
                    <?php foreach ($itemsCredito as $item) { 
                        $applicationTime = $item['application_time'] ?? 'ANTE_IMPUESTO';
                        $textoApplicationTime = ($applicationTime == 'POST_IMPUESTO') ? 'Después del Impuesto' : 'Antes del Impuesto';
                        $nombreItem = htmlspecialchars($item['item_name'] ?? $item['description'] ?? 'N/A');
                        $nombreItemConAplicacion = $nombreItem . ' <small style="color: #666; font-size: 0.85em;">(' . $textoApplicationTime . ')</small>';
                        $subtotalItem = floatval($item['subtotal'] ?? 0);
                    ?>
                    <tr>
                        <td><?= $nombreItemConAplicacion ?></td>
                        <td><?= number_format(floatval($item['cantity'] ?? 0), 0, ",", ".") ?></td>
                        <td>$<?= number_format((float)($item['price'] ?? 0), 0, ",", ".") ?></td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td style="color: #27ae60;">-$<?= number_format($subtotalItem, 0, ",", ".") ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
        
        <div style="margin-top: 15px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; border: 1px solid #dee2e6;">
            <h5 style="font-weight: 600; color: #667eea; margin-bottom: 15px;">Resumen de Totales</h5>
            <table style="width: 100%;">
                <tr>
                    <td align="right" style="padding-right: 20px; padding-bottom: 5px;"><strong>SUBTOTAL BRUTO:</strong></td>
                    <td align="right" style="width: 150px; padding-bottom: 5px;">$<?= number_format((float)$totales['subtotal_bruto'], 0, ",", ".") ?></td>
                </tr>
                <tr>
                    <td align="right" style="padding-right: 20px; padding-bottom: 5px; color: #ff5722;"><strong>(-) DESCUENTOS DE ÍTEMS:</strong></td>
                    <td align="right" style="width: 150px; padding-bottom: 5px; color: #ff5722;">-$<?= number_format((float)$totales['descuentos_items'], 0, ",", ".") ?></td>
                </tr>
                <tr>
                    <td align="right" style="padding-right: 20px; padding-bottom: 5px; color: #ff5722;"><strong>(-) DESCUENTOS COMERCIALES GLOBALES:</strong></td>
                    <td align="right" style="width: 150px; padding-bottom: 5px; color: #ff5722;">-$<?= number_format((float)$totales['descuentos_comerciales_globales'], 0, ",", ".") ?></td>
                </tr>
                <tr>
                    <td align="right" style="padding-right: 20px; padding-bottom: 5px;"><strong>(=) SUBTOTAL GRABABLE:</strong></td>
                    <td align="right" style="width: 150px; padding-bottom: 5px;">$<?= number_format((float)$totales['subtotal_gravable'], 0, ",", ".") ?></td>
                </tr>
                <tr>
                    <td align="right" style="padding-right: 20px; padding-bottom: 5px;"><strong>(+) IMPUESTOS:</strong></td>
                    <td align="right" style="width: 150px; padding-bottom: 5px;">$<?= number_format((float)$totales['impuestos'], 0, ",", ".") ?></td>
                </tr>
                <tr>
                    <td align="right" style="padding-right: 20px; padding-bottom: 5px;"><strong>(=) TOTAL FACTURADO:</strong></td>
                    <td align="right" style="width: 150px; padding-bottom: 5px;">$<?= number_format((float)$totales['total_facturado'], 0, ",", ".") ?></td>
                </tr>
                <tr>
                    <td align="right" style="padding-right: 20px; padding-bottom: 5px; color: #ff5722;"><strong>(-) ANTICIPOS O SALDOS A FAVOR:</strong></td>
                    <td align="right" style="width: 150px; padding-bottom: 5px; color: #ff5722;">-$<?= number_format((float)$totales['anticipos_saldos_favor'], 0, ",", ".") ?></td>
                </tr>
                <?php if ($valorAdicional > 0) { ?>
                <tr>
                    <td align="right" style="padding-right: 20px; padding-bottom: 5px;"><strong>VALOR ADICIONAL:</strong></td>
                    <td align="right" style="width: 150px; padding-bottom: 5px;">$<?= number_format((float)$valorAdicional, 0, ",", ".") ?></td>
                </tr>
                <?php } ?>
                <tr style="border-top: 2px solid #667eea;">
                    <td align="right" style="padding-right: 20px; padding-top: 10px;"><strong>(=) TOTAL NETO A PAGAR:</strong></td>
                    <td align="right" style="width: 150px; padding-top: 10px; font-size: 1.1em; color: #667eea;"><strong>$<?= number_format((float)$totales['total_neto'], 0, ",", ".") ?></strong></td>
                </tr>
                <tr>
                    <td align="right" style="padding-right: 20px; padding-top: 10px; padding-bottom: 5px;"><strong>Total abonado:</strong></td>
                    <td align="right" style="width: 150px; padding-top: 10px; padding-bottom: 5px; color: #28a745;">$<?= number_format((float)$totalAbonos, 0, ",", ".") ?></td>
                </tr>
                <tr>
                    <td align="right" style="padding-right: 20px;"><strong>Saldo pendiente:</strong></td>
                    <td align="right" style="width: 150px; color: #dc3545; font-weight: 600;">$<?= number_format((float)$porCobrar, 0, ",", ".") ?></td>
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

