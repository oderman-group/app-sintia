<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<head>
	<title>SINTIA | Comprobante</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="100" width="500"><br>
    <!--<?=$informacion_inst["info_nombre"]?><br>-->
    COMPROBANTE DE TRANSACCIÓN</br>
</div>   
<?php
    $consultaComprobante=mysqli_query($conexion, "SELECT * FROM finanzas_cuentas WHERE fcu_id='".$_GET["id"]."'");
	$comprobante = mysqli_fetch_array($consultaComprobante, MYSQLI_BOTH);
    $consultaU=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$comprobante[6]."'");
	$u = mysqli_fetch_array($consultaU, MYSQLI_BOTH);
	$nombreCompleto = strtoupper($u[4]);
	switch($comprobante[4]){case 1: $tipo = "Ingreso"; break; case 2: $tipo = "Egreso"; break; case 3: $tipo = "Cuenta por cobrar"; break; case 4: $tipo = "Cuenta por pagar"; break;}
	switch($comprobante[8]){case 1: $forma = "Efectivo"; break; case 2: $forma = "Cheque"; break; case 3: $forma = "T. D&eacute;bito"; break; case 4: $forma = "T. Cr&eacute;dito"; break; case 5: $forma = "N/A"; break; default: $forma = "N/A"; break;}
?>
  <table bgcolor="#FFFFFF" width="70%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
	
    <tr>
    	<td>Referencia</td>
        <td><?=$comprobante[0];?></td>
    </tr>
    <tr>
    	<td>Fecha</td>
        <td><?=$comprobante[1];?></td>
    </tr>
    <tr>
    	<td>Detalle</td>
        <td><?=$comprobante[2];?></td>
    </tr>
    <tr>
    	<td>Valor</td>
        <td>$<?=number_format($comprobante[3],2,",",".");?></td>
    </tr>
    <tr>
    	<td>Forma de pago</td>
        <td><?=$forma;?></td>
    </tr>
    <tr>
    	<td>Tipo de transacci&oacute;n</td>
        <td><?=$tipo;?></td>
    </tr>
    <tr>
    	<td>Nombre</td>
        <td><?=$nombreCompleto;?></td>
    </tr>
    <tr>
    	<td>Observaciones</td>
        <td><?=$comprobante[5];?></td>
    </tr>
    
  </table>
  </center>
	<div align="center" style="font-size:10px; margin-top:10px;">
      <img src="../files/images/sintia.png" height="50" width="100"><br>
      SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
     </div>
     <script>print();</script>
</body>
</html>


