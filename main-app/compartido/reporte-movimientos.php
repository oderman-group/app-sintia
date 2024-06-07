<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0240';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/UsuariosPadre.php");
?>
<head>
	<title>Movimientos Financieros</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>
<body style="font-family:Arial;">
  <?php
    $nombreInforme = "INFORME DE MOVIMIENTOS";
    include("../compartido/head-informes.php");
  ?>
  <table bgcolor="#FFFFFF" width="100%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?= $Plataforma->colorUno; ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                        <th>Detalle</th>
                                        <th>Valor</th>
                                        <th><?=$frases[53][$datosUsuarioActual['uss_idioma']];?></th>
                                        <th>Forma de pago</th>
                                        <th>Observaciones</th>
                                        <th>Cerrado</th>
  </tr>
                  <?php
									$consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_anulado=0 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
                  $cont=0;
									while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                    $u = UsuariosPadre::sesionUsuario($resultado['fcu_usuario']);
                    $cerrado = !empty($resultado['fcu_cerrado_usuario']) ? UsuariosPadre::sesionUsuario($resultado['fcu_cerrado_usuario']) : array();
                    $nombreCompleto = UsuariosPadre::nombreCompletoDelUsuario($u);
									?>
  <tr style="font-size:13px;">
      <td><?=$resultado['fcu_id'];?></td>
                                        <td><?=$nombreCompleto;?></td>
                                        <td><?=$resultado['fcu_fecha'];?></td>
                                        <td><?=$resultado['fcu_detalle'];?></td>
                                        <td>$<?=number_format($resultado['fcu_valor'],2,",",".");?></td>
                                        <td><?=$tipoEstadoFinanzas[$resultado['fcu_tipo']];?></td>
                                        <td><?=$formasPagoFinanzas[$resultado['fcu_forma_pago']] ?? "N/A";?></td>
                                        
                                        <td><?=$resultado['fcu_observaciones'];?></td>
                                        <td><?=$resultado['fcu_cerrado'];?> <br> <?php if(isset($cerrado[4])) echo strtoupper($cerrado['uss_nombre']);?></td>
</tr>
  <?php
  $cont++;
  }//Fin mientras que
  ?>
  </table>
  </center>
    <?php include("../compartido/footer-informes.php"); ?>
</body>
</html>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>