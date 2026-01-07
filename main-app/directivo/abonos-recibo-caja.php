<?php
include("session.php");
$idPaginaInterna = 'DT0271';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

$id = "";
if (!empty($_GET["id"])) {
    $id = base64_decode($_GET["id"]);
}

$resultado = Movimientos::traerDatosAbonos($conexion, $config, $id);

if (empty($resultado)) {
    echo '<script type="text/javascript">alert("No se encontraron datos del abono solicitado."); window.close();</script>';
    exit();
}

$fechaReplace = date('d/m/Y');
if (!empty($resultado['registration_date'])) {
    try {
        $fechaBD = new DateTime($resultado['registration_date']);
        $fechaReplace = $fechaBD->format('d/m/Y');
    } catch (Exception $e) {
        $fechaReplace = date('d/m/Y');
    }
}

// Datos del cliente (ya vienen en $resultado con prefijo cli_)
$nombreCliente    = trim(($resultado['cli_nombre'] ?? '') . ' ' . ($resultado['cli_nombre2'] ?? '') . ' ' . ($resultado['cli_apellido1'] ?? '') . ' ' . ($resultado['cli_apellido2'] ?? ''));
$nombreCliente    = !empty($nombreCliente) ? $nombreCliente : 'N/A';
$direccionCliente = $resultado['cli_direccion'] ?? 'N/A';
$ciudadCliente    = 'N/A'; // El campo ciudad no existe en la tabla usuarios
$celularCliente   = $resultado['cli_celular'] ?? '';
$telefonoCliente  = $resultado['cli_telefono'] ?? '';
$documentoCliente = $resultado['cli_documento'] ?? 'N/A';
$contactoCliente  = trim($celularCliente . ((!empty($celularCliente) && !empty($telefonoCliente)) ? ' - ' : '') . $telefonoCliente);
if ($contactoCliente === '') {
    $contactoCliente = 'N/A';
}

// Datos del abono
$numeroFactura = $resultado['numeroFactura'] ?? 'N/A';
$valorAbono    = (float)($resultado['valorAbono'] ?? 0);
$observacion   = strip_tags($resultado['observation'] ?? ''); // Limpiar HTML

// Datos de la cuenta bancaria
$cuentaBancaria = 'N/A';
if (!empty($resultado['cuenta_bancaria_nombre'])) {
    $cuentaBancaria = $resultado['cuenta_bancaria_nombre'];
    if (!empty($resultado['cuenta_bancaria_numero'])) {
        $cuentaBancaria .= ' - ' . $resultado['cuenta_bancaria_numero'];
    }
}

// Obtener el total de la factura usando el método correcto que calcula desde transaction_items
$idFactura = $resultado['invoiced'] ?? '';
$valorAdicionalFactura = (float)($resultado['fcu_valor_factura'] ?? 0);

// Calcular el total correcto de la factura sumando los items
$totalesFactura = Movimientos::calcularTotalesFactura($conexion, $config, $idFactura, $valorAdicionalFactura, TIPO_FACTURA);
$totalFactura = $totalesFactura['total_neto'];

// Calcular el total abonado y saldo restante
$totalAbonado = 0;
$historialAbonos = [];
if (!empty($idFactura)) {
    try {
        $consultaAbonos = mysqli_query($conexion, "SELECT 
            pi.id, 
            pi.payment,
            pi.fecha_registro,
            pi.observation
        FROM ".BD_FINANCIERA.".payments_invoiced pi
        WHERE pi.invoiced = '".mysqli_real_escape_string($conexion, $idFactura)."'
        AND pi.is_deleted = 0
        AND pi.institucion = {$config['conf_id_institucion']} 
        AND pi.year = {$_SESSION["bd"]}
        ORDER BY pi.fecha_registro ASC
        ");
        
        if ($consultaAbonos && mysqli_num_rows($consultaAbonos) > 0) {
            while ($abono = mysqli_fetch_array($consultaAbonos, MYSQLI_ASSOC)) {
                $totalAbonado += (float)($abono['payment'] ?? 0);
                $historialAbonos[] = $abono;
            }
        }
    } catch (Exception $e) {
        // Silenciar errores
    }
}

$saldoRestante = $totalFactura - $totalAbonado;

$infoLogo      = $informacion_inst["info_logo"] ?? '';
$infoNombre    = strtoupper((string)($informacion_inst["info_nombre"] ?? ''));
$infoNit       = $informacion_inst["info_nit"] ?? '';
$infoTelefono  = $informacion_inst["info_telefono"] ?? '';
$infoDireccion = $informacion_inst["info_direccion"] ?? '';

switch ($resultado['payment_method']) {
    case "EFECTIVO":
        $metodoPago = "Efectivo";
    break;

    case "CHEQUE":
        $metodoPago = "Cheque";
    break;

    case "T_DEBITO":
        $metodoPago = "T. Débito";
    break;

    case "T_CREDITO":
        $metodoPago = "T. Crédito";
    break;

    case "TRANSFERENCIA":
        $metodoPago = "Transferencia";
    break;

    default:
        $metodoPago = "Otras Formas";
    break;
}
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta name="description" content="Plataforma Educativa SINTIA | Para Colegios y Universidades" />
        <meta name="author" content="ODERMAN" />
        <title>RECIBO DE CAJA NO. <?=$resultado["id"]?></title>
        <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
        <!-- favicon -->
        <link rel="shortcut icon" href="../sintia-icono.png" />
        <style>
            #saltoPagina {
                PAGE-BREAK-AFTER: always;
            }

            .table_items {
                border-collapse: collapse;
            }

            .table_items th, .table_items td {
                border: 1px solid #a8a8a8;
            }

            .table_items tfoot td {
                border: none;
            }

            .table_datos {
                border-collapse: collapse;
            }

            .borde_superior_izquierdo {
                border-top-left-radius: 10px !important; /* Ajusta el radio según tus preferencias */
            }

            .borde_superior_derecho {
                border-top-right-radius: 10px !important; /* Ajusta el radio según tus preferencias */
            }

            .borde_inferior_izquierdo {
                border-bottom-left-radius: 10px !important; /* Ajusta el radio según tus preferencias */
            }

            .borde_inferior_derecho {
                border-bottom-right-radius: 10px !important; /* Ajusta el radio según tus preferencias */
            }

            .altura-especifica {
                height: 50px; /* Ajusta la altura según tus necesidades */
            }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    </head>
    <body style="font-family:Arial; font-size: 13px;">
        <div style="margin: 15px 0;">
            <table width="100%">
                <tr>
                    <td align="left" width="30%">
                        <?php if(!empty($infoLogo)){ ?>
                            <img src="../files/images/logo/<?=$infoLogo?>" width="100%"><br><br>
                        <?php } ?>
                    </td>
                    <td align="center" width="40%">
                        <span style="font-weight:bold; margin: 0"><?=htmlspecialchars($infoNombre)?></span><br>
                        NIT: <?=htmlspecialchars($infoNit)?><br>
                        <?=htmlspecialchars($infoTelefono)?><br>
                        <?=htmlspecialchars($infoDireccion)?>
                    </td>
                    <td align="center" width="30%">
                        <h2 style="margin: 0px; padding: 10px; background-color: #a8a8a8;">RECIBO DE CAJA</h2>
                        <h3 class="borde_inferior_izquierdo borde_inferior_derecho" style="margin: 0px; padding: 20px; background-color: #e3e3e3; font-weight:bold;"><b>No. <?=$resultado["id"]?></b></h3>
                    </td>
                </tr>
            </table>
            <table class="table_datos" style="font-size: 15px; margin-bottom: 5px;" width="100%">
                <tr>
                    <td align="right" width="20%" class="borde_superior_izquierdo" style="background-color: #a8a8a8; font-weight:bold;">SEÑOR(ES) </td>
                    <td align="left" colspan="3" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=htmlspecialchars($nombreCliente)?></td>
                    <td align="center" width="20%" class="borde_superior_derecho" style="background-color: #a8a8a8; font-weight:bold;">FECHA</td>
                </tr>
                <tr>
                    <td align="right" width="20%" style="background-color: #a8a8a8; font-weight:bold;">DIRECCIÓN</td>
                    <td align="left" colspan="3" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=htmlspecialchars($direccionCliente)?></td>
                    <td align="center" rowspan="4" style="border: 1px solid #a8a8a8;"><?=$fechaReplace?></td>
                </tr>
                <tr>
                    <td align="right" width="20%" style="background-color: #a8a8a8; font-weight:bold;">CIUDAD</td>
                    <td align="left" colspan="3" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=htmlspecialchars($ciudadCliente)?></td>
                </tr>
                <tr>
                    <td align="right" width="20%" style="background-color: #a8a8a8; font-weight:bold;">TELÉFONO</td>
                    <td align="left" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=htmlspecialchars($contactoCliente)?></td>
                    <td align="right" width="20%" style="background-color: #a8a8a8; font-weight:bold;">MÉTODO DE PAGO</td>
                    <td align="left" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=htmlspecialchars($metodoPago)?></td>
                </tr>
                <tr>
                    <td align="right" width="20%" class="borde_inferior_izquierdo" style="background-color: #a8a8a8; font-weight:bold;">CC/NIT</td>
                    <td align="left" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=htmlspecialchars($documentoCliente)?></td>
                    <td align="right" width="20%" style="background-color: #a8a8a8; font-weight:bold;">CUENTA</td>
                    <td align="left" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=htmlspecialchars($cuentaBancaria)?></td>
                </tr>
            </table>
            <table class="table_items" width="100%" style="font-size: 15px; height: 50%;">
                <thead style="background-color: #a8a8a8; font-weight:bold;" align="center">
                    <tr>
                        <th>CONCEPTO</th>
                        <th>VALOR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="padding: 10px;">
                                <strong>Abono a la factura no. <?=htmlspecialchars($numeroFactura)?></strong><br><br>
                                <strong>Total factura:</strong> $<?=number_format($totalFactura, 0, ",", ".")?><br>
                                <strong>Total abonado:</strong> $<?=number_format($totalAbonado, 0, ",", ".")?><br>
                                <strong>Saldo restante:</strong> $<?=number_format($saldoRestante, 0, ",", ".")?>
                            </div>
                        </td>
                        <td align="right" style="vertical-align: top;">$<?=number_format($valorAbono, 0, ",", ".")?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td width="80%">
                            <table width="100%">
                                <tr>
                                    <td align="center" style="background-color: #a8a8a8; font-weight:bold;">DETALLES</td>
                                </tr>
                                <tr>
                                    <td align="left"><?=nl2br(htmlspecialchars($observacion))?></td>
                                </tr>
                            </table>
                        </td>
                        <td width="20%">
                            <table width="100%">
                                <tr>
                                    <td align="center" width="30%" style="font-weight:bold;">Subtotal</td>
                                    <td align="right" width="70%"><?="$".number_format($valorAbono, 0, ",", ".");?></td>
                                </tr>
                                <tr style="background-color: #a8a8a8;">
                                    <td align="center" width="30%">Total</td>
                                    <td align="right" width="70%"><?="$".number_format($valorAbono, 0, ",", ".");?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tfoot>
            </table>
            
            <?php
            // Configuración para mostrar histórico de abonos (puede cambiar a false para ocultar)
            $mostrarHistorico = true;
            
            if ($mostrarHistorico && count($historialAbonos) > 0) {
            ?>
            <p>&nbsp;</p>
            <h4 style="margin: 10px 0; text-align: center;">HISTÓRICO DE ABONOS</h4>
            <table class="table_items" width="100%" style="font-size: 13px;">
                <thead style="background-color: #a8a8a8; font-weight:bold;" align="center">
                    <tr>
                        <th width="10%">No.</th>
                        <th width="20%">Fecha</th>
                        <th width="40%">Observación</th>
                        <th width="30%">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $numeroAbono = 1;
                    foreach ($historialAbonos as $abono) { 
                        $esAbonoActual = ($abono['id'] == $id);
                        $estilo = $esAbonoActual ? 'background-color: #ffffcc; font-weight: bold;' : '';
                        $fechaAbono = 'N/A';
                        if (!empty($abono['fecha_registro'])) {
                            try {
                                $fechaBD = new DateTime($abono['fecha_registro']);
                                $fechaAbono = $fechaBD->format('d/m/Y');
                            } catch (Exception $e) {
                                $fechaAbono = 'N/A';
                            }
                        }
                        $observacionAbono = strip_tags($abono['observation'] ?? 'Sin observación');
                    ?>
                    <tr style="<?=$estilo?>">
                        <td align="center"><?=$numeroAbono?><?=$esAbonoActual ? ' (Este)' : ''?></td>
                        <td align="center"><?=htmlspecialchars($fechaAbono)?></td>
                        <td align="left" style="padding-left: 5px;"><?=htmlspecialchars($observacionAbono)?></td>
                        <td align="right" style="padding-right: 10px;">$<?=number_format((float)$abono['payment'], 0, ",", ".")?></td>
                    </tr>
                    <?php 
                        $numeroAbono++;
                    } 
                    ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #e3e3e3; font-weight: bold;">
                        <td colspan="3" align="right" style="padding-right: 10px;">TOTAL ABONADO:</td>
                        <td align="right" style="padding-right: 10px;">$<?=number_format($totalAbonado, 0, ",", ".")?></td>
                    </tr>
                    <tr style="background-color: #a8a8a8; font-weight: bold;">
                        <td colspan="3" align="right" style="padding-right: 10px;">SALDO RESTANTE:</td>
                        <td align="right" style="padding-right: 10px;">$<?=number_format($saldoRestante, 0, ",", ".")?></td>
                    </tr>
                </tfoot>
            </table>
            <?php } ?>
            
            <p>&nbsp;</p>
            <!--******FIRMAS******-->
            <table width="80%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
                <tr>
                    <td align="center">
                        <?php
                            if(!empty($resultado["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $resultado['uss_firma'])){
                                echo '<img src="../files/fotos/'.$resultado["uss_firma"].'" width="100"><br>';
                            }else{
                                echo '<p>&nbsp;</p>
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>';
                            }
                        ?>
                        <p style="height:0px;"></p>__________________________________________<br>
                        ELABORADO POR
                    </td>
                    <td align="center">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p style="height:0px;"></p>__________________________________________<br>
                        ACEPTADA, FIRMA Y/O SELLO Y FECHA
                    </td>
                </tr>
            </table>
        </div>
        <?php include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); ?>
        <script type="application/javascript">
            print();
        </script>
    </body>
</html>