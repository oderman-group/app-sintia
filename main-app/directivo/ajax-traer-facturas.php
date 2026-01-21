<?php
include("session.php");
$idPaginaInterna = 'DT0270';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

$filtro='';
if (!empty($_REQUEST["idUsuario"])){
    $filtro = " AND fcu_usuario='".$_REQUEST["idUsuario"]."'";
}

$consulta = Movimientos::listarFacturas($conexion, $config, $filtro);
$numFacturas = mysqli_num_rows($consulta);

if ($numFacturas > 0) {
    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {

        // Validar que tenemos los datos necesarios
        if (empty($resultado['fcu_id'])) {
            continue; // Saltar esta iteración si no hay fcu_id
        }
        
        $vlrAdicional = !empty($resultado['fcu_valor']) ? floatval($resultado['fcu_valor']) : 0;
        // Usar el método centralizado para obtener todos los totales desglosados
        $totalesFactura = Movimientos::calcularTotalesFactura($conexion, $config, $resultado['fcu_id'], $vlrAdicional);
        $totalNeto = $totalesFactura['total_neto'];
        $abonos = Movimientos::calcularTotalAbonado($conexion, $config, $resultado['fcu_id']);
        
        // Asegurar que los valores sean números válidos
        $totalNeto = is_numeric($totalNeto) ? floatval($totalNeto) : 0;
        $abonos = is_numeric($abonos) ? floatval($abonos) : 0;
        $porCobrar = $totalNeto - $abonos;
        $porCobrar = is_numeric($porCobrar) ? floatval($porCobrar) : 0;
        
        $disabled = $porCobrar < 1 ? "disabled" : "";
        
        // Obtener items de la factura con nombre del item, application_time y ordenados (débitos primero)
        $itemsFactura = [];
        try {
            // Usar item_name, item_type y application_time de transaction_items (copia histórica)
            // Usar tax_name y tax_fee del snapshot (preferencia) con fallback a JOIN con taxes para compatibilidad
            $consultaItems = mysqli_query($conexion, "SELECT ti.*, 
                ti.item_name, 
                ti.item_type,
                COALESCE(ti.application_time, 'ANTE_IMPUESTO') AS application_time,
                COALESCE(ti.tax_name, tax.type_tax) as tax_name,
                COALESCE(ti.tax_fee, tax.fee) as tax_fee 
                FROM ".BD_FINANCIERA.".transaction_items ti
                LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id=ti.tax AND tax.institucion={$config['conf_id_institucion']} AND tax.year={$_SESSION["bd"]}
                WHERE ti.id_transaction='{$resultado['fcu_id']}' AND ti.institucion={$config['conf_id_institucion']} AND ti.year={$_SESSION["bd"]}
                ORDER BY ti.item_type ASC, ti.id_autoincremental");
            if ($consultaItems) {
                while ($item = mysqli_fetch_array($consultaItems, MYSQLI_BOTH)) {
                    $itemsFactura[] = $item;
                }
            }
        } catch (Exception $e) {
            // Continuar sin items si hay error
        }
        
        $detalleFactura = htmlspecialchars($resultado['fcu_detalle'] ?? '');
        $observacionesFactura = htmlspecialchars($resultado['fcu_observaciones'] ?? '');
?>
    <tr id="reg<?=$resultado['fcu_id'];?>" class="factura-row">
        <td>
            <i class="fa fa-chevron-right expand-btn" onclick="toggleFacturaDetails('<?=$resultado['fcu_id'];?>')" id="expand<?=$resultado['fcu_id'];?>"></i>
        </td>
        <td title="<?=$detalleFactura;?>">
            <span style="border-bottom: 0.5px dashed #000; cursor:help;"><?=$resultado['fcu_id'] ?? '';?></span>
        </td>
        <td><?=$resultado['fcu_fecha'] ?? '';?></td>
        <td id="totalNeto<?=$resultado['fcu_id'];?>" data-total-neto="<?=$totalNeto?>">$<?=number_format($totalNeto, 0, ",", ".")?></td>
        <td style="color: green;" id="abonos<?=$resultado['fcu_id'];?>" data-abonos="<?=$abonos?>">$<?=number_format($abonos, 0, ",", ".")?></td>
        <td style="color: red;" id="porCobrar<?=$resultado['fcu_id'];?>" data-por-cobrar="<?=$porCobrar?>">$<?=number_format($porCobrar, 0, ",", ".")?></td>
        <td>
            <input type="number" 
                name="abono_factura[<?=$resultado['fcu_id'];?>]"
                min="0" 
                max="<?=$porCobrar?>" 
                step="0.01" 
                class="form-control input-abono-factura" 
                onchange="actualizarAbonado(this)" 
                data-id-factura="<?=$resultado['fcu_id'];?>" 
                data-id-abono="<?=$_REQUEST["idAbono"];?>" 
                data-abono-anterior="0" 
                data-saldo-pendiente="<?=$porCobrar?>"
                value="0" 
                title="Saldo pendiente: $<?=number_format($porCobrar, 0, ",", ".")?>"
                <?=$disabled?>>
        </td>
    </tr>
    <tr class="factura-details-row" id="details<?=$resultado['fcu_id'];?>">
        <td colspan="7">
            <div class="factura-details-content">
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="color: #667eea; font-weight: 600; margin-bottom: 10px;">
                            <i class="fa fa-info-circle"></i> Información de la Factura
                        </h6>
                        <table class="table table-sm" style="margin-bottom: 15px;">
                            <tr>
                                <td style="width: 40%; font-weight: 600;">Detalle:</td>
                                <td><?=$detalleFactura ?: 'N/A';?></td>
                            </tr>
                            <?php if (!empty($observacionesFactura)) { ?>
                            <tr>
                                <td style="font-weight: 600;">Observaciones:</td>
                                <td><?=$observacionesFactura;?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td style="font-weight: 600;">Estado:</td>
                                <td>
                                    <?php 
                                    $estado = $resultado['fcu_status'] ?? '';
                                    $badgeClass = ($estado == 'COBRADA') ? 'badge-success' : 'badge-warning';
                                    $estadoTexto = ($estado == 'COBRADA') ? 'Cobrada' : 'Por Cobrar';
                                    ?>
                                    <span class="badge <?=$badgeClass?>"><?=$estadoTexto?></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 style="color: #667eea; font-weight: 600; margin-bottom: 10px;">
                            <i class="fa fa-calculator"></i> Resumen Financiero Detallado
                        </h6>
                        <table class="table table-sm">
                            <tr>
                                <td style="width: 50%; font-weight: 600;">Subtotal Bruto:</td>
                                <td>$<?=number_format(floatval($totalesFactura['subtotal_bruto'] ?? 0), 0, ",", ".")?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">(-) Descuentos de Ítems:</td>
                                <td style="color: #ff5722;">-$<?=number_format(floatval($totalesFactura['descuentos_items'] ?? 0), 0, ",", ".")?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">(-) Descuentos Comerciales:</td>
                                <td style="color: #ff5722;">-$<?=number_format(floatval($totalesFactura['descuentos_comerciales_globales'] ?? 0), 0, ",", ".")?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">(=) Subtotal Gravable:</td>
                                <td style="font-weight: bold;">$<?=number_format(floatval($totalesFactura['subtotal_gravable'] ?? 0), 0, ",", ".")?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">(+) Impuestos:</td>
                                <td>$<?=number_format(floatval($totalesFactura['impuestos'] ?? 0), 0, ",", ".")?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">(=) Total Facturado:</td>
                                <td style="font-weight: bold;">$<?=number_format(floatval($totalesFactura['total_facturado'] ?? 0), 0, ",", ".")?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">(-) Anticipos/Saldos a Favor:</td>
                                <td style="color: #ff5722;">-$<?=number_format(floatval($totalesFactura['anticipos_saldos_favor'] ?? 0), 0, ",", ".")?></td>
                            </tr>
                            <?php if (floatval($totalesFactura['valor_adicional'] ?? 0) > 0) { ?>
                            <tr>
                                <td style="font-weight: 600;">Valor Adicional:</td>
                                <td>$<?=number_format(floatval($totalesFactura['valor_adicional'] ?? 0), 0, ",", ".")?></td>
                            </tr>
                            <?php } ?>
                            <tr style="border-top: 2px solid #667eea; background: #eef2ff;">
                                <td style="font-weight: 700;">(=) Total Neto:</td>
                                <td style="font-weight: 700;">$<?=number_format($totalNeto, 0, ",", ".")?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">Total Abonado:</td>
                                <td style="color: green; font-weight: bold;">$<?=number_format($abonos, 0, ",", ".")?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">Por Cobrar:</td>
                                <td style="color: red; font-weight: bold;">$<?=number_format($porCobrar, 0, ",", ".")?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php if (!empty($itemsFactura)) { ?>
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <h6 style="color: #667eea; font-weight: 600; margin-bottom: 10px;">
                            <i class="fa fa-list"></i> Items de la Factura
                        </h6>
                        <table class="table table-sm table-bordered">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Descuento</th>
                                    <th>Impuesto</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($itemsFactura as $item) {
                                    $precio = floatval($item['price'] ?? 0);
                                    $cantidad = floatval($item['cantity'] ?? 0);
                                    $descuento = floatval($item['discount'] ?? 0);
                                    $taxFee = floatval($item['tax_fee'] ?? 0);
                                    $itemType = isset($item['item_type']) ? $item['item_type'] : 'D';
                                    $isCredito = ($itemType === 'C');
                                    $applicationTime = $item['application_time'] ?? 'ANTE_IMPUESTO';
                                    
                                    $subtotal = floatval($item['subtotal'] ?? 0);
                                    
                                    $rowClass = $isCredito ? 'item-credito' : '';
                                    $nombreItem = ($item['item_name'] ?? '') ?: ($item['description'] ?? 'N/A');
                                    if ($isCredito) {
                                        $textoApplicationTime = ($applicationTime == 'POST_IMPUESTO') ? 'Después del Impuesto' : 'Antes del Impuesto';
                                        $nombreItem .= ' <small style="color: #666; font-size: 0.85em;">(Crédito - ' . $textoApplicationTime . ')</small>';
                                    }
                                    $signoSubtotal = $isCredito ? '-' : '';
                                ?>
                                <tr class="<?=$rowClass;?>" data-item-type="<?=$itemType;?>">
                                    <td><?=$nombreItem;?></td>
                                    <td><?=number_format($cantidad, 0, ",", ".")?></td>
                                    <td>$<?=number_format($precio, 0, ",", ".")?></td>
                                    <td><?=$isCredito ? 'N/A' : number_format($descuento, 0, ",", ".") . '%';?></td>
                                    <td><?=$isCredito ? 'N/A' : ($taxFee > 0 ? number_format($taxFee, 0, ",", ".").'%' : 'N/A');?></td>
                                    <td style="font-weight: bold;"><?=$signoSubtotal;?>$<?=number_format(abs($subtotal), 0, ",", ".")?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php } ?>
            </div>
        </td>
    </tr>
<?php 
    }
} else {
?>
    <tr>
        <td colspan="7" align="center" style="font-size: 17px; font-weight:bold; padding: 20px;">No se encontraron facturas para este cliente...</td>
    </tr>
<?php 
}
?>
    <script>
        $(document).ready(function() {
            totalizarAbonos();
        });
        
        function toggleFacturaDetails(facturaId) {
            var detailsRow = document.getElementById('details' + facturaId);
            var expandBtn = document.getElementById('expand' + facturaId);
            
            if (detailsRow && expandBtn) {
                if (detailsRow.classList.contains('show')) {
                    detailsRow.classList.remove('show');
                    expandBtn.classList.remove('expanded');
                } else {
                    detailsRow.classList.add('show');
                    expandBtn.classList.add('expanded');
                }
            }
        }
    </script>
<?php 

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit();