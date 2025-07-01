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

// Crear un objeto DateTime a partir de la cadena de fecha y hora
$fechaBD = new DateTime($resultado['registration_date']);
$fechaReplace = $fechaBD->format('d/m/Y');


$resultadoFactura = UsuariosPadre::sesionUsuario($resultado['invoiced']);

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
                        <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="100%"><br><br>
                    </td>
                    <td align="center" width="40%">
                        <span style="font-weight:bold; margin: 0"><?=strtoupper($informacion_inst["info_nombre"])?></span><br>
                        NIT: <?=$informacion_inst["info_nit"]?><br>
                        <?=$informacion_inst["info_telefono"]?><br>
                        <?=$informacion_inst["info_direccion"]?>
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
                    <td align="left" colspan="3" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=UsuariosPadre::nombreCompletoDelUsuario($resultadoFactura)?></td>
                    <td align="center" width="20%" class="borde_superior_derecho" style="background-color: #a8a8a8; font-weight:bold;">FECHA</td>
                </tr>
                <tr>
                    <td align="right" width="20%" style="background-color: #a8a8a8; font-weight:bold;">DIRECCIÓN</td>
                    <td align="left" colspan="3" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=$resultadoFactura['uss_direccion']?></td>
                    <td align="center" rowspan="4" style="border: 1px solid #a8a8a8;"><?=$fechaReplace?></td>
                </tr>
                <tr>
                    <td align="right" width="20%" style="background-color: #a8a8a8; font-weight:bold;">CIUDAD</td>
                    <td align="left" colspan="3" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=$resultadoFactura['ciu_nombre']?></td>
                </tr>
                <tr>
                    <td align="right" width="20%" style="background-color: #a8a8a8; font-weight:bold;">TELÉFONO</td>
                    <td align="left" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?php echo $resultadoFactura['uss_celular']; if (!empty($resultadoFactura['uss_celular']) && !empty($resultadoFactura['uss_telefono'])) { echo "-"; } echo $resultadoFactura['uss_telefono']; ?></td>
                    <td align="right" width="20%" style="background-color: #a8a8a8; font-weight:bold;">MÉTODO DE PAGO</td>
                    <td align="left" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=$metodoPago?></td>
                </tr>
                <tr>
                    <td align="right" width="20%" class="borde_inferior_izquierdo" style="background-color: #a8a8a8; font-weight:bold;">CC/NIT</td>
                    <td align="left" style="padding-left: 10px; border: 1px solid #a8a8a8;"><?=$resultadoFactura['uss_documento']?></td>
                    <td align="right" width="20%" style="background-color: #a8a8a8; font-weight:bold;">CUENTA</td>
                    <td align="left" style="padding-left: 10px; border: 1px solid #a8a8a8;"></td>
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
                        <td><div style="height: 200px;">Abono a la factura no. <?=$resultado['numeroFactura'];?></div></td>
                        <td align="right" style="vertical-align: top;">$<?=number_format($resultado['valorAbono'], 0, ",", ".")?></td>
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
                                    <td align="left"><?=$resultado['observation'];?></td>
                                </tr>
                            </table>
                        </td>
                        <td width="20%">
                            <table width="100%">
                                <tr>
                                    <td align="center" width="30%" style="font-weight:bold;">Subtotal</td>
                                    <td align="right" width="70%"><?="$".number_format($resultado['valorAbono'], 0, ",", ".");?></td>
                                </tr>
                                <tr style="background-color: #a8a8a8;">
                                    <td align="center" width="30%">Total</td>
                                    <td align="right" width="70%"><?="$".number_format($resultado['valorAbono'], 0, ",", ".");?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tfoot>
            </table>  
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