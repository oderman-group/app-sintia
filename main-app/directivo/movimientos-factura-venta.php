<?php
include("session.php");
$idPaginaInterna = 'DT0255';

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

$configFinanzas=Movimientos::configuracionFinanzas($conexion, $config);

try{
    $consulta = mysqli_query($conexion, "SELECT fcu.*, uss.*, ciu.*, dep.*, fcu.id_nuevo AS id_nuevo_finanzas  FROM ".BD_FINANCIERA.".finanzas_cuentas fcu
    INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=fcu_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
    LEFT JOIN ".BD_ADMIN.".localidad_ciudades ciu ON ciu_id=uss_lugar_nacimiento
    LEFT JOIN ".BD_ADMIN.".localidad_departamentos dep ON dep_id=ciu_departamento
    WHERE fcu_id='".$id."' AND fcu.institucion={$config['conf_id_institucion']} AND fcu.year={$_SESSION["bd"]}");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

$fecha        = explode ("-", $resultado['fcu_fecha']);
$dia          = $fecha[2];  
$mes          = $fecha[1];  
$year         = $fecha[0];
$fechaReplace = $dia.'/'.$mes.'/'.$year;

$tipoFactura    = 'Factura de venta original';
$tituloContacto = 'Facturar A';
$tituloGeneral  = 'FACTURA';

if ($resultado["fcu_tipo"] == FACTURA_COMPRA) {
    $tipoFactura    = 'Comprobante de compra original';
    $tituloContacto = 'Pagar A';
    $tituloGeneral  = 'COMPROBANTE';
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
        <title><?=$tipoFactura;?> No. <?=$resultado["id_nuevo_finanzas"];?> Para <?=UsuariosPadre::nombreCompletoDelUsuario($resultado);?> del <?=$fechaReplace;?>  </title>
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
                border: 1px solid #000;
            }

            .table_items tfoot td {
                border: none;
            }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        <link rel="stylesheet" href="../../config-general/assets/css/fuentes-factura.css" />
    </head>
    <body class="ff1" style="font-size: 13px;">
        <div style="margin: 15px 0;">
            <table width="100%">
                <tr>
                    <td align="left" width="55%">
                        <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="70%"><br><br>
                        <span style="font-weight:bold; margin: 0"><?=strtoupper($informacion_inst["info_nombre"] ?? '')?></span><br>
                        <span style="font-weight:bold; margin: 0">Nit: <?=$informacion_inst["info_nit"];?></span><br>
                        <?=$informacion_inst["info_direccion"]?><br>
                        Tel: <?=$informacion_inst["info_telefono"]?><br><br>
                        <table width="50%">
                            <tr>
                                <td style="border: 1px solid #000; padding: 5px; width: 35%; background-color: #e3e3e3;"><?=$tituloContacto;?>:</td>
                            </tr>
                            <tr>
                                <td>
                                    <?=UsuariosPadre::nombreCompletoDelUsuario($resultado)?><br>
                                    <b>C.C/NIT:</b> <?=$resultado['uss_documento']?><br>
                                    <b>CEL/TEL:</b> <?php echo $resultado['uss_celular']; if (!empty($resultado['uss_celular']) && !empty($resultado['uss_telefono'])) { echo "-"; } echo $resultado['uss_telefono']; ?><br>
                                    <?=$resultado['uss_direccion']?><br>
                                    <?php if (!empty($resultado['uss_lugar_nacimiento'])) { echo $resultado['ciu_nombre'].", ".$resultado['dep_nombre']; }?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td align="right" width="45%" style="vertical-align: top;">
                        <h1 style="margin: 0px; font-size: 50px;"><?=$tituloGeneral;?></h1>
                        <h3 style="margin: 0px; font-size: 13px;"><b>Número: <?=$resultado["id_nuevo_finanzas"];?></b></h3>
                        <h3 style="margin: 0px; font-size: 13px;">No responsable de IVA</h3>
                        <h3 style="margin: 0px; font-size: 13px;"><?=$tipoFactura;?></h3>
                    </td>
                </tr>
            </table>
            <p>&nbsp;</p>
            <table style="font-size: 15px; margin-bottom: 5px; border: 1px solid #000; border-collapse: collapse;" width="40%" align="right">
                <tr>
                    <td align="center" width="50%" style="background-color: #e3e3e3;">FECHA </td>
                    <td align="left" width="50%"><?=$fechaReplace?></td>
                </tr>
            </table>
            <table class="table_items" width="100%" style="font-size: 15px;">
                <thead style="background-color: #e3e3e3;" align="center">
                    <tr>
                        <th colspan="2">Descripción</th>
                        <th>Precio</th>
                        <th>Desc %</th>
                        <th>Impuesto</th>
                        <th  width="10%">Cant.</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                                                                
                        $itemsConsulta = Movimientos::listarItemsTransaction($conexion, $config, $id);

                        $subtotal=0;
                        $totalDescuento=0;
                        $totalImpuesto=0;
                        $arrayImpuestos = [];
                        $numItems=mysqli_num_rows($itemsConsulta);
                        if($numItems>0){
                            while ($fila = mysqli_fetch_array($itemsConsulta, MYSQLI_BOTH)) {

                                $resultadoTax = Movimientos::traerDatosImpuestos($conexion, $config, $fila['tax']);

                                $impuestoItem = $fila['tax'] != 0 ? $resultadoTax['type_tax']." (".$resultadoTax['fee']."%)" : "NINGUNO (0%)";
                    ?>
                        <tr>
                            <td colspan="2"><?=$fila['name'];?><?php if ( !empty($fila['description']) ){ echo "(".$fila['description'].")"; } ?></td>
                            <td style="text-align: right;">$<?=number_format($fila['priceTransaction'], 0, ",", ".")?></td>
                            <td style="text-align: center;"><?=$fila['discount']?>%</td>
                            <td style="text-align: right;"><?=$impuestoItem?></td>
                            <td style="text-align: center;"><?=$fila['cantity'];?></td>
                            <td style="text-align: right;">$<?=number_format($fila['subtotal'], 0, ",", ".")?></td>
                        </tr>
                    <?php 
                            $subtotal += $fila['priceTransaction'] * $fila['cantity'];

                            $descuento = ($fila['priceTransaction'] * ($fila['discount'] / 100));
                            $totalDescuento += $descuento;

                            $tax = !empty($resultadoTax['fee']) ? $resultadoTax['fee'] : 0;
                            $impuesto = (($fila['priceTransaction'] * $fila['cantity']) - $descuento) * ($tax / 100);
                            if ($fila['tax'] > 0) {
                                $datosImpuestos = [
                                    "name" =>$resultadoTax['type_tax'],
                                    "fee" =>$resultadoTax['fee'],
                                    "value" =>$impuesto
                                ];
                                $arrayImpuestos[] = $datosImpuestos;
                            }
                            $totalImpuesto += $impuesto;
                            }
                        }
                        if(empty($resultado['fcu_valor'])){ $resultado['fcu_valor']=0; }
                        $total= (($subtotal + $resultado['fcu_valor']) - $totalDescuento) + $totalImpuesto ;
                        $negativo = $totalDescuento > 0 ? "-" : "";
                        $numImpuesto = count($arrayImpuestos);
                        $colspan = 4 + $numImpuesto;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" rowspan="<?=$colspan?>">
                            <table>
                                <tr style="font-weight:bold;">
                                    <td style="border: 1px solid #000;">
                                        DETALLE: <?=$resultado['fcu_detalle']?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php if(!empty($configFinanzas['invoice_footer'])) echo $configFinanzas['invoice_footer'];?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td align="right" colspan="2" style="font-weight:bold;">SUBTOTAL:</td>
                        <td align="right" style="background-color: #e3e3e3; border: 1px solid #000; font-weight:bold;"><?="$".number_format($subtotal, 0, ",", ".");?></td>
                    </tr>
                    <tr>
                        <td align="right" colspan="2" style="font-weight:bold;">VLR. ADICIONAL:</td>
                        <td align="right" style="background-color: #e3e3e3; border: 1px solid #000; font-weight:bold;"><?="$".number_format($resultado['fcu_valor'], 0, ",", ".");?></td>
                    </tr>
                    <tr>
                        <td align="right" colspan="2" style="font-weight:bold;">DESCUENTO:</td>
                        <td align="right" style="background-color: #e3e3e3; border: 1px solid #000; font-weight:bold;"><?=$negativo."$".number_format($totalDescuento, 0, ",", ".");?></td>
                    </tr>
                    <?php
                    foreach ($arrayImpuestos as $datosImpuestos) {
                    ?>
                        <tr>
                            <td align="right" colspan="2" style="font-weight:bold;"><?=$datosImpuestos['name']." (".$datosImpuestos['fee']."%)";?></td>
                            <td align="right" style="background-color: #e3e3e3; border: 1px solid #000; font-weight:bold;"><?="$".number_format($datosImpuestos['value'], 0, ",", ".");?></td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr style="font-weight:bold;">
                        <td align="right" colspan="2">TOTAL NETO:</td>
                        <td align="right" style="background-color: #e3e3e3; border: 1px solid #000; "><?="$".number_format($total, 0, ",", ".");?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); ?>
        <script type="application/javascript">
            print();
        </script>
    </body>
</html>