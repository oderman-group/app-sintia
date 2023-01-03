<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<head>
	<title>SINTIA | Saldos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    INFORME DE SALDOS</br>
</div>   
<?php
									if(isset($_GET["tipo"]) and $_GET["tipo"]!="" and is_numeric($_GET["tipo"])){
										$SQL = "SELECT * FROM usuarios INNER JOIN perfiles ON uss_tipo=pes_id WHERE uss_id!='".$_SESSION["id"]."' AND uss_tipo='".$_GET["tipo"]."'";
									}else{
										$SQL = "SELECT * FROM usuarios INNER JOIN perfiles ON uss_tipo=pes_id WHERE uss_id!='".$_SESSION["id"]."'";
									}
									//include("paginacion.php");
									?>
  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
	<th>ID</th>
                                        <th>Nombre</th>
                                         <th>Tipo</th>
                                        <th>Email</th>
                                        <th>Celular</th>
                                        <th>Saldo</th>
  </tr>
  <?php
									 $consulta = mysqli_query($conexion, $SQL);
									 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
										 if($resultado[5]==1) $s='<img src="../files/iconos/on.png">'; elseif($resultado[5]==0) $s='<img src="../files/iconos/off.png">'; else $s="-";
										 if($resultado[3]==5) $b = "bold"; else $b="";
										 if($resultado['uss_bloqueado']==1) $c = "#F00"; else $c="";
                     $consultaCobros=mysqli_query($conexion, "SELECT sum(fcu_valor) FROM finanzas_cuentas WHERE fcu_tipo=3 and fcu_anulado=0 AND fcu_usuario=".$resultado[0]."");
										 $cobros = mysqli_fetch_array($consultaCobros, MYSQLI_BOTH);
                     $consultaPagos=mysqli_query($conexion, "SELECT sum(fcu_valor) FROM finanzas_cuentas WHERE fcu_tipo=1 and fcu_anulado=0 AND fcu_usuario=".$resultado[0]."");
										 $pagos = mysqli_fetch_array($consultaPagos, MYSQLI_BOTH);
										 $estadoC = $pagos[0] - $cobros[0];
										 if($estadoC==0){continue;}
										 if($estadoC<0){$color = '#F00';}else{$color = '#090';}
									 ?>
  <tr style="font-size:13px;">
      <td><?=$resultado[0];?></td>
                                        <td><?=$resultado[4];?></td>
                                         <td><?=$resultado["pes_nombre"];?></td>
                                        <td><?=$resultado[12];?></td>
                                        <td><?=$resultado[15];?></td>
                                        <td style="color:<?=$color;?>;">$<?=number_format($estadoC,2,",",".");?></td>
</tr>
  <?php
  $cont++;
  }//Fin mientras que
  ?>
  </table>
  </center>
	<div align="center" style="font-size:10px; margin-top:10px;">
      <img src="../files/images/sintia.png" height="50" width="100"><br>
      SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
     </div>
</body>
</html>


