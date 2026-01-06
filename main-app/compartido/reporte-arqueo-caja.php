<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0264';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

// Obtener fechas desde GET o usar valores por defecto (mes actual)
$fechaDesde = !empty($_GET['desde']) ? $_GET['desde'] : date('Y-m-01');
$fechaHasta = !empty($_GET['hasta']) ? $_GET['hasta'] : date('Y-m-t');
$tipoMovimiento = null;
if (!empty($_GET['tipo'])) {
    $tipoFiltro = base64_decode($_GET['tipo']);
    if ($tipoFiltro !== '') {
        $tipoMovimiento = intval($tipoFiltro);
    }
}

// Obtener filtros de métodos de pago
$metodosPagoFiltro = [];
if (!empty($_GET['metodos_pago']) && is_array($_GET['metodos_pago'])) {
    foreach ($_GET['metodos_pago'] as $metodo) {
        $metodosPagoFiltro[] = mysqli_real_escape_string($conexion, $metodo);
    }
}

// Obtener filtros de cuentas bancarias
$cuentasBancariasFiltro = [];
if (!empty($_GET['cuentas_bancarias']) && is_array($_GET['cuentas_bancarias'])) {
    foreach ($_GET['cuentas_bancarias'] as $cuenta) {
        $cuentasBancariasFiltro[] = mysqli_real_escape_string($conexion, $cuenta);
    }
}

// Obtener datos del arqueo de caja
$arqueo = Movimientos::obtenerArqueoCajaPorMetodo(
    $conexion, 
    $config, 
    $fechaDesde, 
    $fechaHasta,
    $tipoMovimiento
);

// Aplicar filtros de métodos de pago y cuentas bancarias si están especificados
if (!empty($metodosPagoFiltro) || !empty($cuentasBancariasFiltro)) {
    $arqueoFiltrado = [
        'por_metodo' => [],
        'por_cuenta' => [],
        'total_general' => 0
    ];
    
    if (!empty($arqueo['por_metodo'])) {
        foreach ($arqueo['por_metodo'] as $metodoPago => $datosMetodo) {
            // Filtrar por método de pago si está especificado
            if (!empty($metodosPagoFiltro) && !in_array($metodoPago, $metodosPagoFiltro)) {
                continue;
            }
            
            // Filtrar cuentas bancarias si está especificado
            $cuentasFiltradas = [];
            foreach ($datosMetodo['cuentas'] as $cuenta) {
                if (!empty($cuentasBancariasFiltro)) {
                    $cuentaIdStr = (string)($cuenta['cuenta_id'] ?? '');
                    // Comparar tanto como string como número para mayor compatibilidad
                    if (in_array($cuentaIdStr, $cuentasBancariasFiltro) || 
                        in_array($cuenta['cuenta_id'], $cuentasBancariasFiltro) ||
                        in_array((int)$cuenta['cuenta_id'], array_map('intval', $cuentasBancariasFiltro))) {
                        $cuentasFiltradas[] = $cuenta;
                    }
                } else {
                    $cuentasFiltradas[] = $cuenta;
                }
            }
            
            if (!empty($cuentasFiltradas)) {
                // Recalcular totales del método con las cuentas filtradas
                $totalIngresosMetodo = 0;
                $totalEgresosMetodo = 0;
                $totalNetoMetodo = 0;
                
                foreach ($cuentasFiltradas as $cuenta) {
                    $totalIngresosMetodo += $cuenta['ingresos'];
                    $totalEgresosMetodo += $cuenta['egresos'];
                    $totalNetoMetodo += $cuenta['neto'];
                }
                
                $arqueoFiltrado['por_metodo'][$metodoPago] = [
                    'nombre' => $datosMetodo['nombre'],
                    'total_ingresos' => $totalIngresosMetodo,
                    'total_egresos' => $totalEgresosMetodo,
                    'total_neto' => $totalNetoMetodo,
                    'cuentas' => $cuentasFiltradas
                ];
                
                $arqueoFiltrado['total_general'] += $totalNetoMetodo;
            }
        }
    }
    
    $arqueo = $arqueoFiltrado;
}

$nombreInforme = "ARQUEO DE CAJA";
$infoFiltros = array();
$infoFiltros[] = "Período: " . date('d/m/Y', strtotime($fechaDesde)) . " - " . date('d/m/Y', strtotime($fechaHasta));

if ($tipoMovimiento == 1) {
    $infoFiltros[] = "Tipo: Solo Ingresos";
} elseif ($tipoMovimiento == 2) {
    $infoFiltros[] = "Tipo: Solo Egresos";
}

// Agregar información de filtros de métodos de pago
if (!empty($metodosPagoFiltro)) {
    require_once(ROOT_PATH."/main-app/class/MediosPago.php");
    $mediosPago = MediosPago::obtenerMediosPago();
    $nombresMetodos = [];
    foreach ($metodosPagoFiltro as $codigo) {
        if (isset($mediosPago[$codigo])) {
            $nombresMetodos[] = $mediosPago[$codigo];
        }
    }
    if (!empty($nombresMetodos)) {
        $infoFiltros[] = "Métodos: " . implode(", ", $nombresMetodos);
    }
}

// Agregar información de filtros de cuentas bancarias
if (!empty($cuentasBancariasFiltro)) {
    $consultaCuentas = Movimientos::listarCuentasBancarias($conexion, $config, null, true);
    $nombresCuentas = [];
    if ($consultaCuentas) {
        mysqli_data_seek($consultaCuentas, 0); // Reiniciar el puntero
        while ($cuenta = mysqli_fetch_array($consultaCuentas, MYSQLI_BOTH)) {
            if (in_array($cuenta['cba_id'], $cuentasBancariasFiltro)) {
                $nombreCuenta = $cuenta['cba_nombre'] . (!empty($cuenta['cba_banco']) ? ' - ' . $cuenta['cba_banco'] : '');
                $nombresCuentas[] = $nombreCuenta;
            }
        }
    }
    if (!empty($nombresCuentas)) {
        $infoFiltros[] = "Cuentas: " . implode(", ", $nombresCuentas);
    }
}

if (!empty($infoFiltros)) {
    $nombreInforme .= " (" . implode(" | ", $infoFiltros) . ")";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arqueo de Caja</title>
    <link href="../../config-general/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
            .container { width: 100% !important; }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        #contenido {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-informe {
            border-bottom: 3px solid <?= $Plataforma->colorUno; ?>;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo-container {
            text-align: center;
        }
        
        .info-institucion {
            text-align: center;
            margin-top: 15px;
        }
        
        .titulo-informe {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: <?= $Plataforma->colorUno; ?>;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .filtros-info {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .tabla-arqueo {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .tabla-arqueo thead {
            background: <?= $Plataforma->colorUno; ?>;
            color: white;
        }
        
        .tabla-arqueo th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
        }
        
        .tabla-arqueo td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }
        
        .tabla-arqueo tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        .tabla-arqueo tbody tr.subtotal {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .tabla-arqueo tbody tr.total-general {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 15px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .ingreso {
            color: #2ecc71;
            font-weight: 600;
        }
        
        .egreso {
            color: #e74c3c;
            font-weight: 600;
        }
        
        .neto-positivo {
            color: #2ecc71;
            font-weight: 700;
        }
        
        .neto-negativo {
            color: #e74c3c;
            font-weight: 700;
        }
        
        .resumen-cards {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .card-resumen {
            background: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            min-width: 200px;
            margin: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .card-resumen.ingresos {
            border-color: #2ecc71;
        }
        
        .card-resumen.egresos {
            border-color: #e74c3c;
        }
        
        .card-resumen.neto {
            border-color: #3498db;
        }
        
        .card-resumen .label {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .card-resumen .valor {
            font-size: 28px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .card-resumen.ingresos .valor {
            color: #2ecc71;
        }
        
        .card-resumen.egresos .valor {
            color: #e74c3c;
        }
        
        .card-resumen.neto .valor {
            color: #3498db;
        }
        
        .controles {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        
        .btn-accion {
            margin: 5px;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-accion:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .btn-imprimir {
            background: #3498db;
            color: white;
        }
        
        .btn-pdf {
            background: #e74c3c;
            color: white;
        }
        
        .sin-datos {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="controles no-print">
        <button class="btn-accion btn-imprimir" onclick="window.print()">
            <i class="fa fa-print"></i> Imprimir
        </button>
        <button class="btn-accion btn-pdf" onclick="generarPDF()">
            <i class="fa fa-file-pdf"></i> Descargar PDF
        </button>
    </div>
    
    <div id="contenido">
        <?php include("../compartido/head-informes.php"); ?>
        
        <div class="header-informe">
            <div class="titulo-informe">ARQUEO DE CAJA</div>
            <div class="filtros-info">
                <?php
                $filtrosTexto = [];
                $filtrosTexto[] = "Período: " . date('d/m/Y', strtotime($fechaDesde)) . " - " . date('d/m/Y', strtotime($fechaHasta));
                if ($tipoMovimiento == 1) {
                    $filtrosTexto[] = "Solo Ingresos";
                } elseif ($tipoMovimiento == 2) {
                    $filtrosTexto[] = "Solo Egresos";
                }
                if (!empty($metodosPagoFiltro)) {
                    require_once(ROOT_PATH."/main-app/class/MediosPago.php");
                    $mediosPago = MediosPago::obtenerMediosPago();
                    $nombresMetodos = [];
                    foreach ($metodosPagoFiltro as $codigo) {
                        if (isset($mediosPago[$codigo])) {
                            $nombresMetodos[] = $mediosPago[$codigo];
                        }
                    }
                    if (!empty($nombresMetodos)) {
                        $filtrosTexto[] = "Métodos: " . implode(", ", $nombresMetodos);
                    }
                }
                if (!empty($cuentasBancariasFiltro)) {
                    $consultaCuentas = Movimientos::listarCuentasBancarias($conexion, $config, null, true);
                    $nombresCuentas = [];
                    if ($consultaCuentas) {
                        mysqli_data_seek($consultaCuentas, 0);
                        while ($cuenta = mysqli_fetch_array($consultaCuentas, MYSQLI_BOTH)) {
                            $cuentaIdStr = (string)$cuenta['cba_id'];
                            if (in_array($cuentaIdStr, $cuentasBancariasFiltro) || in_array($cuenta['cba_id'], $cuentasBancariasFiltro)) {
                                $nombreCuenta = $cuenta['cba_nombre'] . (!empty($cuenta['cba_banco']) ? ' - ' . $cuenta['cba_banco'] : '');
                                $nombresCuentas[] = $nombreCuenta;
                            }
                        }
                    }
                    if (!empty($nombresCuentas)) {
                        $filtrosTexto[] = "Cuentas: " . implode(", ", $nombresCuentas);
                    }
                }
                echo implode(" | ", $filtrosTexto);
                ?>
            </div>
        </div>
        
        <?php 
        $totalGeneralIngresos = 0;
        $totalGeneralEgresos = 0;
        $totalGeneralNeto = 0;
        
        if (!empty($arqueo['por_metodo'])) {
            // Calcular totales generales
            foreach ($arqueo['por_metodo'] as $metodoPago => $datosMetodo) {
                $totalGeneralIngresos += $datosMetodo['total_ingresos'];
                $totalGeneralEgresos += $datosMetodo['total_egresos'];
                $totalGeneralNeto += $datosMetodo['total_neto'];
            }
        ?>
        
        <!-- Cards de resumen -->
        <div class="resumen-cards">
            <div class="card-resumen ingresos">
                <div class="label">Total Ingresos</div>
                <div class="valor ingreso">$<?= number_format($totalGeneralIngresos, 0, ",", ".") ?></div>
            </div>
            <div class="card-resumen egresos">
                <div class="label">Total Egresos</div>
                <div class="valor egreso">$<?= number_format($totalGeneralEgresos, 0, ",", ".") ?></div>
            </div>
            <div class="card-resumen neto">
                <div class="label">Neto</div>
                <div class="valor <?= $totalGeneralNeto >= 0 ? 'neto-positivo' : 'neto-negativo' ?>">
                    $<?= number_format($totalGeneralNeto, 0, ",", ".") ?>
                </div>
            </div>
        </div>
        
        <!-- Tabla de arqueo -->
        <table class="tabla-arqueo">
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th>Cuenta Bancaria</th>
                    <th class="text-right">Ingresos</th>
                    <th class="text-right">Egresos</th>
                    <th class="text-right">Neto</th>
                    <th class="text-center">Cant. Mov.</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalArqueoIngresos = 0;
                $totalArqueoEgresos = 0;
                $totalArqueoNeto = 0;
                
                foreach ($arqueo['por_metodo'] as $metodoPago => $datosMetodo) {
                    $filaMetodo = true;
                    $contadorCuentas = 0;
                    
                    foreach ($datosMetodo['cuentas'] as $cuenta) {
                        $totalArqueoIngresos += $cuenta['ingresos'];
                        $totalArqueoEgresos += $cuenta['egresos'];
                        $totalArqueoNeto += $cuenta['neto'];
                        $contadorCuentas++;
                ?>
                <tr>
                    <td><?= $filaMetodo ? htmlspecialchars($datosMetodo['nombre']) : ''; ?></td>
                    <td><?= htmlspecialchars($cuenta['cuenta_nombre']); ?></td>
                    <td class="text-right ingreso">$<?= number_format($cuenta['ingresos'], 0, ",", "."); ?></td>
                    <td class="text-right egreso">$<?= number_format($cuenta['egresos'], 0, ",", "."); ?></td>
                    <td class="text-right <?= $cuenta['neto'] >= 0 ? 'neto-positivo' : 'neto-negativo' ?>">
                        $<?= number_format($cuenta['neto'], 0, ",", "."); ?>
                    </td>
                    <td class="text-center"><?= $cuenta['cantidad']; ?></td>
                </tr>
                <?php
                        $filaMetodo = false;
                    }
                    
                    // Fila de subtotal por método
                    if ($contadorCuentas > 1) {
                ?>
                <tr class="subtotal">
                    <td colspan="2" class="text-right"><strong>Subtotal <?= htmlspecialchars($datosMetodo['nombre']); ?>:</strong></td>
                    <td class="text-right ingreso"><strong>$<?= number_format($datosMetodo['total_ingresos'], 0, ",", "."); ?></strong></td>
                    <td class="text-right egreso"><strong>$<?= number_format($datosMetodo['total_egresos'], 0, ",", "."); ?></strong></td>
                    <td class="text-right <?= $datosMetodo['total_neto'] >= 0 ? 'neto-positivo' : 'neto-negativo' ?>">
                        <strong>$<?= number_format($datosMetodo['total_neto'], 0, ",", "."); ?></strong>
                    </td>
                    <td></td>
                </tr>
                <?php
                    }
                }
                ?>
                <tr class="total-general">
                    <td colspan="2" class="text-right"><strong>TOTAL GENERAL:</strong></td>
                    <td class="text-right ingreso"><strong>$<?= number_format($totalArqueoIngresos, 0, ",", "."); ?></strong></td>
                    <td class="text-right egreso"><strong>$<?= number_format($totalArqueoEgresos, 0, ",", "."); ?></strong></td>
                    <td class="text-right <?= $totalArqueoNeto >= 0 ? 'neto-positivo' : 'neto-negativo' ?>">
                        <strong>$<?= number_format($totalArqueoNeto, 0, ",", "."); ?></strong>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        
        <?php } else { ?>
        <div class="sin-datos">
            <i class="fa fa-info-circle" style="font-size: 48px; margin-bottom: 15px;"></i>
            <p>No se encontraron datos de arqueo de caja para el período seleccionado.</p>
        </div>
        <?php } ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px;">
            Generado el <?= date('d/m/Y H:i:s') ?> por <?= htmlspecialchars($datosUsuarioActual['uss_nombre'] ?? 'Usuario') ?>
        </div>
    </div>
    
    <?php include("../compartido/footer-informes.php"); ?>
    
    <!-- Scripts para PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        function generarPDF() {
            const element = document.getElementById('contenido');
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'Arqueo_Caja_<?= date('Y-m-d', strtotime($fechaDesde)) ?>_<?= date('Y-m-d', strtotime($fechaHasta)) ?>.pdf',
                image: { 
                    type: 'jpeg', 
                    quality: 0.95 
                },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    allowTaint: false,
                    logging: false,
                    letterRendering: true
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'landscape',
                    compress: true
                },
                pagebreak: { 
                    mode: ['avoid-all', 'css', 'legacy']
                }
            };
            
            html2pdf()
                .set(opt)
                .from(element)
                .save()
                .then(() => {
                    console.log('PDF generado exitosamente');
                })
                .catch(err => {
                    console.error('Error al generar PDF:', err);
                    alert('Error al generar el PDF. Por favor, intente nuevamente.');
                });
        }
    </script>
</body>
</html>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>

