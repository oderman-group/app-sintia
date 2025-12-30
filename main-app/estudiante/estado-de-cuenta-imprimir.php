<?php include("session.php");?>
<?php include("verificar-usuario.php");?>
<?php $idPaginaInterna = 'ES0012';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php require_once(ROOT_PATH."/main-app/class/Movimientos.php"); ?>
<?php require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php"); ?>
<?php require_once(ROOT_PATH."/main-app/class/Estudiantes.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Cuenta - <?= htmlspecialchars($datosEstudianteActual['mat_nombre'] ?? 'Estudiante') ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            font-size: 14px;
            margin-top: 10px;
        }
        .info-estudiante {
            margin-bottom: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .info-estudiante h2 {
            margin-top: 0;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .info-estudiante table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-estudiante td {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .info-estudiante td:first-child {
            font-weight: bold;
            width: 200px;
        }
        .resumen-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }
        .resumen-card {
            flex: 1;
            min-width: 200px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
        }
        .resumen-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
        }
        .resumen-card .valor {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .resumen-card.saldo-positivo .valor {
            color: #28a745;
        }
        .resumen-card.saldo-negativo .valor {
            color: #dc3545;
        }
        .tabla-facturas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .tabla-facturas th {
            background: #333;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #333;
        }
        .tabla-facturas td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .tabla-facturas tr:nth-child(even) {
            background: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-venta {
            background: #17a2b8;
            color: white;
        }
        .badge-compra {
            background: #ffc107;
            color: #333;
        }
        .badge-porcobrar {
            background: #ffc107;
            color: #333;
        }
        .badge-cobrada {
            background: #28a745;
            color: white;
        }
        .badge-proceso {
            background: #17a2b8;
            color: white;
        }
        .badge-anulada {
            background: #dc3545;
            color: white;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #333;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .no-print {
            margin-bottom: 20px;
        }
        .btn-print {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-print:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button class="btn-print" onclick="window.print()">
            <i class="fa fa-print"></i> Imprimir
        </button>
    </div>

    <div class="header">
        <h1>ESTADO DE CUENTA</h1>
        <div class="subtitle"><?= htmlspecialchars($config['conf_nombre_institucion'] ?? 'Institución') ?> - Año <?= $_SESSION['bd'] ?></div>
        <div class="subtitle">Fecha de generación: <?= date('d/m/Y H:i:s') ?></div>
    </div>

    <div class="info-estudiante">
        <h2>Información del Estudiante</h2>
        <table>
            <tr>
                <td>Nombre completo:</td>
                <td><?= htmlspecialchars(UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual)) ?></td>
            </tr>
            <tr>
                <td>Documento:</td>
                <td><?= htmlspecialchars($datosUsuarioActual['uss_documento'] ?? 'N/A') ?></td>
            </tr>
            <?php if (!empty($datosEstudianteActual['mat_grado']) || !empty($datosEstudianteActual['mat_grupo'])): ?>
            <tr>
                <td>Grado/Grupo:</td>
                <td><?= htmlspecialchars($datosEstudianteActual['mat_grado'] ?? '') ?>/<?= htmlspecialchars($datosEstudianteActual['mat_grupo'] ?? '') ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <?php
    // Calcular resumen
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
        $tipoFactura = (int)($factura['fcu_tipo'] ?? 1);
        
        // Si es factura de compra (tipo 2), el valor se invierte (le deben al usuario)
        if ($tipoFactura == FACTURA_COMPRA) {
            $totalNeto = -abs($totalNeto);
        }
        
        $porCobrar = $totalNeto - $abonos;
        
        $totalFacturado += $totalNeto;
        $totalAbonado += $abonos;
        
        if ($porCobrar > 0) {
            $totalPorCobrar += $porCobrar;
        }
    }
    
    $saldo = $totalAbonado - $totalFacturado;
    ?>

    <div class="resumen-cards">
        <div class="resumen-card">
            <h3>Total Facturado</h3>
            <div class="valor">$<?= number_format((float)$totalFacturado, 0, ",", ".") ?></div>
        </div>
        <div class="resumen-card">
            <h3>Total Abonado</h3>
            <div class="valor">$<?= number_format((float)$totalAbonado, 0, ",", ".") ?></div>
        </div>
        <div class="resumen-card">
            <h3>Por Cobrar/Pagar</h3>
            <div class="valor">$<?= number_format((float)$totalPorCobrar, 0, ",", ".") ?></div>
        </div>
        <div class="resumen-card <?= $saldo >= 0 ? 'saldo-positivo' : 'saldo-negativo' ?>">
            <h3>Saldo</h3>
            <div class="valor">
                <?php if ($saldo >= 0): ?>
                    $<?= number_format((float)$saldo, 0, ",", ".") ?> (Saldo a favor)
                <?php else: ?>
                    $<?= number_format((float)abs($saldo), 0, ",", ".") ?> (Deuda)
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h2 style="margin-bottom: 15px;">Detalle de Facturas</h2>
    <table class="tabla-facturas">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Consecutivo</th>
                <th>Detalle</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th class="text-right">Total</th>
                <th class="text-right">Abonado</th>
                <th class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            mysqli_data_seek($consultaFacturas, 0);
            $contReg = 1;
            while($resultado = mysqli_fetch_array($consultaFacturas, MYSQLI_BOTH)){
                $vlrAdicional = !empty($resultado['fcu_valor']) ? $resultado['fcu_valor'] : 0;
                $totalNeto    = Movimientos::calcularTotalNeto($conexion, $config, $resultado['fcu_id'], $vlrAdicional);
                $abonos       = Movimientos::calcularTotalAbonado($conexion, $config, $resultado['fcu_id']);
                $tipoFactura = (int)($resultado['fcu_tipo'] ?? 1);
                
                $totalNetoMostrar = $totalNeto;
                if ($tipoFactura == FACTURA_COMPRA) {
                    $totalNetoMostrar = -abs($totalNeto);
                }
                
                $porCobrar = $totalNetoMostrar - $abonos;
                
                $estado = $resultado['fcu_status'] ?? '';
                $estadoTexto = '';
                $estadoClass = '';
                switch($estado) {
                    case 'POR_COBRAR':
                        $estadoTexto = 'Por Cobrar';
                        $estadoClass = 'badge-porcobrar';
                        break;
                    case 'COBRADA':
                        $estadoTexto = 'Cobrada';
                        $estadoClass = 'badge-cobrada';
                        break;
                    case 'EN_PROCESO':
                        $estadoTexto = 'En Proceso';
                        $estadoClass = 'badge-proceso';
                        break;
                    case 'ANULADA':
                        $estadoTexto = 'Anulada';
                        $estadoClass = 'badge-anulada';
                        break;
                    default:
                        $estadoTexto = $estado;
                        $estadoClass = '';
                }
            ?>
            <tr>
                <td class="text-center"><?= $contReg ?></td>
                <td><?= htmlspecialchars($resultado['fcu_fecha'] ?? '') ?></td>
                <td><?= htmlspecialchars($resultado['fcu_consecutivo'] ?? $resultado['fcu_id'] ?? '') ?></td>
                <td><?= htmlspecialchars($resultado['fcu_detalle'] ?? '') ?></td>
                <td class="text-center">
                    <span class="badge <?= $tipoFactura == FACTURA_COMPRA ? 'badge-compra' : 'badge-venta' ?>">
                        <?= $tipoFactura == FACTURA_COMPRA ? 'Compra' : 'Venta' ?>
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge <?= $estadoClass ?>"><?= htmlspecialchars($estadoTexto) ?></span>
                </td>
                <td class="text-right">
                    <?php if ($tipoFactura == FACTURA_COMPRA): ?>
                        -$<?= number_format($totalNeto, 0, ",", ".") ?>
                    <?php else: ?>
                        $<?= number_format($totalNetoMostrar, 0, ",", ".") ?>
                    <?php endif; ?>
                </td>
                <td class="text-right">$<?= number_format($abonos, 0, ",", ".") ?></td>
                <td class="text-right" style="<?= $porCobrar > 0 ? 'color: #dc3545;' : ($porCobrar < 0 ? 'color: #28a745;' : '') ?>">
                    <?php if ($porCobrar < 0): ?>
                        -$<?= number_format(abs($porCobrar), 0, ",", ".") ?>
                    <?php else: ?>
                        $<?= number_format($porCobrar, 0, ",", ".") ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php 
                $contReg++;
            }
            ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Este documento fue generado automáticamente el <?= date('d/m/Y') ?> a las <?= date('H:i:s') ?></p>
        <p><?= htmlspecialchars($config['conf_nombre_institucion'] ?? 'Institución') ?></p>
    </div>
</body>
</html>

