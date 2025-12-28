<?php require_once(ROOT_PATH."/main-app/class/Movimientos.php"); ?>
<style>
/* Cards superiores */
.estado-cuenta-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.estado-cuenta-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 20px;
    color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.estado-cuenta-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.estado-cuenta-card.verde {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.estado-cuenta-card.rojo {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.estado-cuenta-card.azul {
    background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
}

.estado-cuenta-card-header {
    font-size: 14px;
    font-weight: 600;
    opacity: 0.9;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.estado-cuenta-card-value {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

.estado-cuenta-card-footer {
    font-size: 12px;
    opacity: 0.8;
    margin-top: 8px;
}

/* Tabla al 100% */
.estado-cuenta-tabla-container {
    width: 100%;
    margin-top: 20px;
}

.detalle-movimiento-btn {
    cursor: pointer;
    color: #667eea;
    font-size: 14px;
    transition: transform 0.2s ease;
    padding: 5px;
}

.detalle-movimiento-btn:hover {
    color: #764ba2;
    transform: scale(1.2);
}

.detalle-movimiento-btn.expanded {
    transform: rotate(90deg);
}

.detalle-factura-wrapper {
    padding: 20px;
    background: #f8f9fa;
    border-left: 4px solid #667eea;
}

.detalle-factura-wrapper h5 {
    color: #667eea;
    font-weight: 600;
    margin-bottom: 15px;
}

.detalle-factura-wrapper ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.detalle-factura-wrapper li {
    padding: 5px 0;
    border-bottom: 1px solid #e0e0e0;
}

.detalle-factura-wrapper li:last-child {
    border-bottom: none;
}

.detalle-factura-wrapper strong {
    color: #333;
    min-width: 150px;
    display: inline-block;
}

/* Resumen tabla */
.resumen-tabla-container {
    margin-top: 20px;
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.resumen-tabla-container table {
    width: 100%;
}

.resumen-tabla-container th {
    background: #667eea;
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: 600;
}

.resumen-tabla-container td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
}

.resumen-tabla-container tr:last-child td {
    border-bottom: none;
    font-weight: 600;
    background: #f8f9fa;
}
</style>

<div class="row">
    <div class="col-md-12">
        <?php
        // Calcular resumen usando los mismos métodos que la tabla
        $consultaFacturas = mysqli_query($conexion, "SELECT * FROM " . BD_FINANCIERA . ".finanzas_cuentas fc
            WHERE fcu_usuario='{$_SESSION["id"]}' AND fcu_anulado=0
            AND fc.institucion={$_SESSION['idInstitucion']} 
            AND fc.year='{$_SESSION["bd"]}' 
            ORDER BY fc.fcu_id DESC");
        
        $totalFacturado = 0;
        $totalAbonado = 0;
        $totalPorCobrar = 0;
        
        while($factura = mysqli_fetch_array($consultaFacturas, MYSQLI_BOTH)){
            $vlrAdicional = !empty($factura['fcu_valor']) ? $factura['fcu_valor'] : 0;
            $totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $factura['fcu_id'], $vlrAdicional);
            $abonos = Movimientos::calcularTotalAbonado($conexion, $config, $factura['fcu_id']);
            $porCobrar = $totalNeto - $abonos;
            
            $totalFacturado += $totalNeto;
            $totalAbonado += $abonos;
            $totalPorCobrar += max(0, $porCobrar);
        }
        
        $saldo = $totalAbonado - $totalFacturado;
        $colorSaldo = 'black';
        $mensajeSaldo = $frases[309][$datosUsuarioActual['uss_idioma']];
        if($saldo > 0){
            $mensajeSaldo = $frases[310][$datosUsuarioActual['uss_idioma']];
            $colorSaldo = 'green';
        }
        if($saldo < 0){
            $mensajeSaldo = $frases[311][$datosUsuarioActual['uss_idioma']];
            $colorSaldo = 'red';
        }
        ?>
        
        <!-- Cards superiores -->
        <div class="estado-cuenta-cards">
            <div class="estado-cuenta-card azul">
                <div class="estado-cuenta-card-header"><?=strtoupper($frases[313][$datosUsuarioActual['uss_idioma']]);?></div>
                <div class="estado-cuenta-card-value" id="cardTotalFacturado">$<?=number_format((float)$totalFacturado, 0, ",", ".");?></div>
                <div class="estado-cuenta-card-footer">Total de facturas emitidas</div>
            </div>
            
            <div class="estado-cuenta-card verde">
                <div class="estado-cuenta-card-header"><?=strtoupper($frases[413][$datosUsuarioActual['uss_idioma']]);?></div>
                <div class="estado-cuenta-card-value" id="cardTotalAbonado">$<?=number_format((float)$totalAbonado, 0, ",", ".");?></div>
                <div class="estado-cuenta-card-footer">Total de pagos realizados</div>
            </div>
            
            <div class="estado-cuenta-card rojo">
                <div class="estado-cuenta-card-header"><?=strtoupper($frases[418][$datosUsuarioActual['uss_idioma']]);?></div>
                <div class="estado-cuenta-card-value" id="cardTotalPorCobrar">$<?=number_format((float)$totalPorCobrar, 0, ",", ".");?></div>
                <div class="estado-cuenta-card-footer">Pendiente por pagar</div>
            </div>
            
            <div class="estado-cuenta-card" style="background: linear-gradient(135deg, #<?=$colorSaldo == 'green' ? '11998e' : ($colorSaldo == 'red' ? 'eb3349' : '667eea');?> 0%, #<?=$colorSaldo == 'green' ? '38ef7d' : ($colorSaldo == 'red' ? 'f45c43' : '764ba2');?> 100%);">
                <div class="estado-cuenta-card-header"><?=strtoupper($frases[315][$datosUsuarioActual['uss_idioma']]);?></div>
                <div class="estado-cuenta-card-value" id="cardSaldo" style="color: white;">$<?=number_format((float)$saldo, 0, ",", ".");?></div>
                <div class="estado-cuenta-card-footer"><?=$mensajeSaldo;?></div>
            </div>
        </div>
        
        <?php if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_API_SION_ACADEMICA)) {?>
            <div align="center" style="margin-bottom: 20px;">
                <p><mark><?=$frases[316][$datosUsuarioActual['uss_idioma']];?>: <b><?php if(!empty($datosEstudianteActual['mat_codigo_tesoreria'])){ echo $datosEstudianteActual['mat_codigo_tesoreria'];}?></b>  (cuatro dígitos, sin el 0 a la izquierda).</mark></p>
                <p>
                    <a href="https://www.avalpaycenter.com/wps/portal/portal-de-pagos/web/pagos-aval/resultado-busqueda/realizar-pago-facturadores?idConv=00022724&origen=buscar" class="btn btn-info" target="_blank"><?=strtoupper($frases[317][$datosUsuarioActual['uss_idioma']]);?></a>
                    <a href="http://sion.icolven.edu.co/Services/ServiceIcolven.svc/GenerarEstadoCuenta/<?=$datosEstudianteActual['mat_codigo_tesoreria'];?>/<?=date('Y');?>" class="btn btn-success" target="_blank"><?=strtoupper($frases[104][$datosUsuarioActual['uss_idioma']]);?></a>
                </p>
            </div>
        <?php }?>
        
        <!-- Tabla de transacciones al 100% -->
        <div class="estado-cuenta-tabla-container">
            <div class="card card-topline-purple">
                <div class="card-head">
                    <header><?=$frases[104][$datosUsuarioActual['uss_idioma']];?></header>
                    <div class="tools">
                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                        <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-scrollable">
                        <table id="tablaMovimientos" class="display" style="width:100%;">
                            <thead>
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th>#</th>
                                    <th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
                                    <th><?=$frases[162][$datosUsuarioActual['uss_idioma']];?></th>
                                    <th><?= $frases[107][$datosUsuarioActual['uss_idioma']]; ?></th>
                                    <th><?= $frases[413][$datosUsuarioActual['uss_idioma']]; ?></th>
                                    <th><?= $frases[418][$datosUsuarioActual['uss_idioma']]; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                mysqli_data_seek($consultaFacturas, 0); // Resetear el puntero
                                $contReg = 1;
                                while($resultado = mysqli_fetch_array($consultaFacturas, MYSQLI_BOTH)){
                                    $vlrAdicional = !empty($resultado['fcu_valor']) ? $resultado['fcu_valor'] : 0;
                                    $totalNeto    = Movimientos::calcularTotalNeto($conexion, $config, $resultado['fcu_id'], $vlrAdicional);
                                    $abonos       = Movimientos::calcularTotalAbonado($conexion, $config, $resultado['fcu_id']);
                                    $porCobrar    = $totalNeto - $abonos;
                                    $colorValor = $porCobrar > 0 ? 'red' : 'black';
                                ?>
                                <tr class="movimiento-row" data-factura-id="<?= $resultado['fcu_id']; ?>">
                                    <td>
                                        <i class="fa fa-chevron-right detalle-movimiento-btn" data-id="<?= $resultado['fcu_id']; ?>"></i>
                                    </td>
                                    <td><?=$contReg;?></td>
                                    <td><?=$resultado['fcu_fecha'];?></td>
                                    <td><?=$resultado['fcu_detalle'];?></td>
                                    <td id="totalNeto<?= $resultado['fcu_id']; ?>" data-tipo="<?= $resultado['fcu_tipo'] ?>" data-total-neto="<?= $totalNeto ?>">$<?= !empty($totalNeto) ? number_format($totalNeto, 0, ",", ".") : 0 ?></td>
                                    <td id="abonos<?= $resultado['fcu_id']; ?>" data-abonos="<?= $abonos ?>">$<?= !empty($abonos) ? number_format($abonos, 0, ",", ".") : 0 ?></td>
                                    <td id="porCobrar<?= $resultado['fcu_id']; ?>" style="color:<?=$colorValor;?>;" data-por-cobrar="<?= $porCobrar ?>">$<?= !empty($porCobrar) ? number_format($porCobrar, 0, ",", ".") : 0 ?></td>
                                </tr>
                                <?php 
                                    $contReg++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla de resumen -->
        <div class="resumen-tabla-container">
            <h4 style="margin-bottom: 15px; color: #667eea;"><?=strtoupper($frases[312][$datosUsuarioActual['uss_idioma']]);?></h4>
            <table>
                <thead>
                    <tr>
                        <th><?=strtoupper($frases[313][$datosUsuarioActual['uss_idioma']]);?></th>
                        <th><?=strtoupper($frases[413][$datosUsuarioActual['uss_idioma']]);?></th>
                        <th><?=strtoupper($frases[418][$datosUsuarioActual['uss_idioma']]);?></th>
                        <th><?=strtoupper($frases[315][$datosUsuarioActual['uss_idioma']]);?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="resumenTotalFacturado">$<?=number_format((float)$totalFacturado, 0, ",", ".");?></td>
                        <td id="resumenTotalAbonado">$<?=number_format((float)$totalAbonado, 0, ",", ".");?></td>
                        <td id="resumenTotalPorCobrar">$<?=number_format((float)$totalPorCobrar, 0, ",", ".");?></td>
                        <td id="resumenSaldo" style="color: <?=$colorSaldo;?>; font-weight: 600;">$<?=number_format((float)$saldo, 0, ",", ".");?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    var tablaMovimientos = $('#tablaMovimientos').DataTable({
        "language": {
            "url": "../../config-general/assets/plugins/datatables/Spanish.json"
        },
        "order": [[2, "desc"]], // Ordenar por fecha descendente
        "pageLength": 25,
        "responsive": true
    });
    
    // Función para formatear números
    function numberFormat(number, decimals = 0, decPoint = ',', thousandsSep = '.') {
        if (isNaN(number) || number === '' || number === null) {
            return '';
        }
        number = parseFloat(number.toFixed(decimals));
        var parts = number.toString().split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);
        return parts.join(decPoint);
    }
    
    // Función para calcular y actualizar totales
    function totalizarMovimientos() {
        var totalFacturado = 0;
        var totalAbonado = 0;
        var totalPorCobrar = 0;
        
        $('#tablaMovimientos tbody tr.movimiento-row').each(function() {
            var $row = $(this);
            if ($row.hasClass('child')) {
                return; // Saltar filas de detalles
            }
            
            var $totalNeto = $row.find('td[data-total-neto]');
            var $abonos = $row.find('td[data-abonos]');
            var $porCobrar = $row.find('td[data-por-cobrar]');
            
            if ($totalNeto.length) {
                var total = parseFloat($totalNeto.attr('data-total-neto')) || 0;
                totalFacturado += total;
            }
            
            if ($abonos.length) {
                var abonos = parseFloat($abonos.attr('data-abonos')) || 0;
                totalAbonado += abonos;
            }
            
            if ($porCobrar.length) {
                var porCobrar = parseFloat($porCobrar.attr('data-por-cobrar')) || 0;
                totalPorCobrar += Math.max(0, porCobrar);
            }
        });
        
        var saldo = totalAbonado - totalFacturado;
        var colorSaldo = 'black';
        if (saldo > 0) colorSaldo = 'green';
        if (saldo < 0) colorSaldo = 'red';
        
        // Actualizar cards
        $('#cardTotalFacturado').text('$' + numberFormat(totalFacturado, 0, ',', '.'));
        $('#cardTotalAbonado').text('$' + numberFormat(totalAbonado, 0, ',', '.'));
        $('#cardTotalPorCobrar').text('$' + numberFormat(totalPorCobrar, 0, ',', '.'));
        $('#cardSaldo').text('$' + numberFormat(saldo, 0, ',', '.')).css('color', 'white');
        
        // Actualizar tabla de resumen
        $('#resumenTotalFacturado').text('$' + numberFormat(totalFacturado, 0, ',', '.'));
        $('#resumenTotalAbonado').text('$' + numberFormat(totalAbonado, 0, ',', '.'));
        $('#resumenTotalPorCobrar').text('$' + numberFormat(totalPorCobrar, 0, ',', '.'));
        $('#resumenSaldo').text('$' + numberFormat(saldo, 0, ',', '.')).css('color', colorSaldo);
    }
    
    // Manejar clic en botón de detalle
    $('#tablaMovimientos tbody').on('click', '.detalle-movimiento-btn', function () {
        var $btn = $(this);
        var tr = $btn.closest('tr');
        var row = tablaMovimientos.row(tr);
        var idFactura = $btn.data('id');
        
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('detalle-abierto');
            $btn.removeClass('expanded');
            totalizarMovimientos();
        } else {
            $btn.addClass('expanded');
            tr.addClass('detalle-abierto');
            row.child('<div class="detalle-factura-wrapper">Cargando detalles...</div>').show();
            
            $.getJSON('../compartido/ajax-detalle-factura-estudiante.php', { idFactura: idFactura })
                .done(function (resp) {
                    if (resp && resp.success) {
                        row.child(resp.html).show();
                    } else {
                        var mensaje = resp && resp.message ? resp.message : 'No se encontraron detalles para esta factura.';
                        row.child('<div class="detalle-factura-wrapper">' + mensaje + '</div>').show();
                    }
                    totalizarMovimientos();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.error('Error AJAX:', textStatus, errorThrown);
                    console.error('Response:', jqXHR.responseText);
                    var mensajeError = 'Error al cargar los detalles. Intenta nuevamente.';
                    if (jqXHR.responseText) {
                        try {
                            var errorResp = JSON.parse(jqXHR.responseText);
                            if (errorResp.message) {
                                mensajeError = errorResp.message;
                            }
                        } catch(e) {
                            // Si no es JSON, mostrar el texto de respuesta
                            if (jqXHR.responseText.length < 200) {
                                mensajeError = jqXHR.responseText;
                            }
                        }
                    }
                    row.child('<div class="detalle-factura-wrapper">' + mensajeError + '</div>').show();
                    totalizarMovimientos();
                });
        }
    });
    
    // Calcular totales al cargar
    totalizarMovimientos();
});
</script>
