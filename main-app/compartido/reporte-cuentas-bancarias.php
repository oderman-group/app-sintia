<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0240';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");
?>
<head>
	<title>Informe de Movimientos por Cuenta Bancaria</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>
<body style="font-family:Arial;">
  <?php
    // Construir título del informe con información de filtros
    $nombreInforme = "INFORME DE MOVIMIENTOS POR CUENTA BANCARIA";
    $infoFiltros = array();
    
    if (!empty($_GET['desde']) && !empty($_GET['hasta'])) {
        $infoFiltros[] = "Período: " . $_GET['desde'] . " - " . $_GET['hasta'];
    }
    
    if (!empty($_GET['cuenta_bancaria']) && trim($_GET['cuenta_bancaria']) !== '') {
        $cuentaBancariaId = mysqli_real_escape_string($conexion, $_GET['cuenta_bancaria']);
        $consultaCuenta = mysqli_query($conexion, "SELECT cba_nombre, cba_numero_cuenta, cba_banco 
            FROM ".BD_FINANCIERA.".finanzas_cuentas_bancarias 
            WHERE cba_id='{$cuentaBancariaId}' 
            AND institucion = {$config['conf_id_institucion']} 
            AND year = {$_SESSION["bd"]} 
            LIMIT 1");
        if ($consultaCuenta && mysqli_num_rows($consultaCuenta) > 0) {
            $cuentaInfo = mysqli_fetch_array($consultaCuenta, MYSQLI_BOTH);
            $nombreCuenta = $cuentaInfo['cba_nombre'];
            if (!empty($cuentaInfo['cba_numero_cuenta'])) {
                $nombreCuenta .= " (" . $cuentaInfo['cba_numero_cuenta'] . ")";
            }
            $infoFiltros[] = "Cuenta: " . $nombreCuenta;
        }
    }
    
    if (!empty($_GET['metodo_pago']) && trim($_GET['metodo_pago']) !== '') {
        $metodoPago = mysqli_real_escape_string($conexion, $_GET['metodo_pago']);
        $infoFiltros[] = "Método de Pago: " . $metodoPago;
    }
    
    if (!empty($infoFiltros)) {
        $nombreInforme .= " (" . implode(" | ", $infoFiltros) . ")";
    }
    
    include("../compartido/head-informes.php");
  ?>
  
  <?php
  // Validar que se proporcionen fechas
  if (empty($_GET['desde']) || empty($_GET['hasta'])) {
      echo '<div style="text-align: center; padding: 50px;">
          <h2>Error: Fechas Requeridas</h2>
          <p>Debe especificar un rango de fechas para generar el informe.</p>
          <button onclick="window.close()">Cerrar</button>
      </div>';
      exit();
  }
  
  $desde = mysqli_real_escape_string($conexion, $_GET['desde']);
  $hasta = mysqli_real_escape_string($conexion, $_GET['hasta']);
  
  // Construir filtros
  $filtroCuenta = '';
  if (!empty($_GET['cuenta_bancaria']) && trim($_GET['cuenta_bancaria']) !== '') {
      $cuentaBancariaId = mysqli_real_escape_string($conexion, $_GET['cuenta_bancaria']);
      $filtroCuenta = " AND p.payment_cuenta_bancaria_id = '{$cuentaBancariaId}'";
  }
  
  $filtroMetodo = '';
  if (!empty($_GET['metodo_pago']) && trim($_GET['metodo_pago']) !== '') {
      $metodoPago = mysqli_real_escape_string($conexion, $_GET['metodo_pago']);
      $filtroMetodo = " AND pi.payment_method = '{$metodoPago}'";
  }
  
  // Consulta para obtener movimientos agrupados por cuenta bancaria
  // Incluir tanto abonos (payments) como facturas (finanzas_cuentas) que tengan cuenta bancaria asociada
  ?>
  
  <table bgcolor="#FFFFFF" width="100%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?= $Plataforma->colorUno; ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
        <th>Cuenta Bancaria</th>
        <th>Banco</th>
        <th>Número de Cuenta</th>
        <th>Tipo</th>
        <th>Método de Pago</th>
        <th>Ingresos (Abonos)</th>
        <th>Egresos (Facturas)</th>
        <th>Neto</th>
        <th>Cantidad Mov.</th>
  </tr>
  <?php
  // Obtener todas las cuentas bancarias activas
  $consultaCuentas = mysqli_query($conexion, "SELECT cba_id, cba_nombre, cba_numero_cuenta, cba_banco, cba_tipo 
      FROM ".BD_FINANCIERA.".finanzas_cuentas_bancarias 
      WHERE cba_activa = 1 
      AND institucion = {$config['conf_id_institucion']} 
      AND year = {$_SESSION["bd"]}
      ORDER BY cba_nombre");
  
  $totalGeneralIngresos = 0;
  $totalGeneralEgresos = 0;
  $totalGeneralNeto = 0;
  $totalGeneralMovimientos = 0;
  
  while($cuenta = mysqli_fetch_array($consultaCuentas, MYSQLI_BOTH)) {
      $cuentaId = $cuenta['cba_id'];
      
      // Aplicar filtro de cuenta si está especificado
      if (!empty($_GET['cuenta_bancaria']) && trim($_GET['cuenta_bancaria']) !== '') {
          if ($cuentaId != $_GET['cuenta_bancaria']) {
              continue;
          }
      }
      
      // Calcular ingresos desde payments_invoiced consolidado
      $sqlIngresos = "SELECT 
          COALESCE(SUM(pi.payment), 0) as total_ingresos,
          COUNT(DISTINCT pi.id) as cantidad_ingresos
      FROM ".BD_FINANCIERA.".payments_invoiced pi
      WHERE pi.payment_cuenta_bancaria_id = '".mysqli_real_escape_string($conexion, $cuentaId)."'
          AND pi.is_deleted = 0
          AND pi.payment_tipo = 'INGRESO'
          AND DATE(pi.fecha_documento) BETWEEN '{$desde}' AND '{$hasta}'
          AND pi.institucion = {$config['conf_id_institucion']} 
          AND pi.year = {$_SESSION["bd"]}
          {$filtroMetodo}";
      
      $consultaIngresos = mysqli_query($conexion, $sqlIngresos);
      $datosIngresos = mysqli_fetch_array($consultaIngresos, MYSQLI_BOTH);
      $totalIngresos = floatval($datosIngresos['total_ingresos'] ?? 0);
      $cantidadIngresos = intval($datosIngresos['cantidad_ingresos'] ?? 0);
      
      // Calcular egresos desde finanzas_cuentas (facturas de compra)
      $sqlEgresos = "SELECT 
          COALESCE(SUM(fc.fcu_valor), 0) as total_egresos,
          COUNT(DISTINCT fc.fcu_id) as cantidad_egresos
      FROM ".BD_FINANCIERA.".finanzas_cuentas fc
      WHERE fc.fcu_cuenta_bancaria_id = '".mysqli_real_escape_string($conexion, $cuentaId)."'
          AND fc.fcu_anulado = 0
          AND fc.fcu_tipo = 2
          AND (fc.fcu_status IS NULL OR fc.fcu_status != '".EN_PROCESO."')
          AND DATE(fc.fcu_fecha) BETWEEN '{$desde}' AND '{$hasta}'
          AND fc.institucion = {$config['conf_id_institucion']} 
          AND fc.year = {$_SESSION["bd"]}";
      
      $consultaEgresos = mysqli_query($conexion, $sqlEgresos);
      $datosEgresos = mysqli_fetch_array($consultaEgresos, MYSQLI_BOTH);
      $totalEgresos = floatval($datosEgresos['total_egresos'] ?? 0);
      $cantidadEgresos = intval($datosEgresos['cantidad_egresos'] ?? 0);
      
      $neto = $totalIngresos - $totalEgresos;
      $cantidadTotal = $cantidadIngresos + $cantidadEgresos;
      
      // Solo mostrar si hay movimientos o si se está filtrando por esta cuenta
      if ($cantidadTotal > 0 || (!empty($_GET['cuenta_bancaria']) && $cuentaId == $_GET['cuenta_bancaria'])) {
          $totalGeneralIngresos += $totalIngresos;
          $totalGeneralEgresos += $totalEgresos;
          $totalGeneralNeto += $neto;
          $totalGeneralMovimientos += $cantidadTotal;
          
          // Obtener método de pago más usado (si hay filtro)
          $metodoPagoTexto = 'N/A';
          if (!empty($_GET['metodo_pago'])) {
              $metodoPagoTexto = htmlspecialchars($_GET['metodo_pago']);
          } else {
              // Obtener el método de pago más frecuente para esta cuenta
              $sqlMetodo = "SELECT payment_method, COUNT(*) as cantidad 
                  FROM ".BD_FINANCIERA.".payments_invoiced 
                  WHERE payment_cuenta_bancaria_id = '".mysqli_real_escape_string($conexion, $cuentaId)."'
                      AND is_deleted = 0
                      AND DATE(fecha_documento) BETWEEN '{$desde}' AND '{$hasta}'
                      AND institucion = {$config['conf_id_institucion']} 
                      AND year = {$_SESSION["bd"]}
                  GROUP BY payment_method 
                  ORDER BY cantidad DESC 
                  LIMIT 1";
              $consultaMetodo = mysqli_query($conexion, $sqlMetodo);
              if ($consultaMetodo && mysqli_num_rows($consultaMetodo) > 0) {
                  $metodoDatos = mysqli_fetch_array($consultaMetodo, MYSQLI_BOTH);
                  $metodoPagoTexto = htmlspecialchars($metodoDatos['payment_method'] ?? 'N/A');
              }
          }
          
          $tipoCuentaTexto = 'N/A';
          if (!empty($cuenta['cba_tipo'])) {
              switch($cuenta['cba_tipo']) {
                  case 'AHORROS':
                      $tipoCuentaTexto = 'Ahorros';
                      break;
                  case 'CORRIENTE':
                      $tipoCuentaTexto = 'Corriente';
                      break;
                  case 'NEOQUI':
                      $tipoCuentaTexto = 'Nequi';
                      break;
                  case 'DAVIPLATA':
                      $tipoCuentaTexto = 'Daviplata';
                      break;
                  case 'CAJA_METALICA':
                      $tipoCuentaTexto = 'Caja Metálica';
                      break;
                  default:
                      $tipoCuentaTexto = htmlspecialchars($cuenta['cba_tipo']);
              }
          }
  ?>
  <tr style="font-size:13px;">
      <td><?=htmlspecialchars($cuenta['cba_nombre']);?></td>
      <td><?=htmlspecialchars($cuenta['cba_banco'] ?? 'N/A');?></td>
      <td><?=htmlspecialchars($cuenta['cba_numero_cuenta'] ?? 'N/A');?></td>
      <td><?=$tipoCuentaTexto;?></td>
      <td><?=$metodoPagoTexto;?></td>
      <td align="right" style="color: green;">$<?=number_format($totalIngresos, 0, ",", ".");?></td>
      <td align="right" style="color: red;">$<?=number_format($totalEgresos, 0, ",", ".");?></td>
      <td align="right" style="font-weight: bold; <?=$neto >= 0 ? 'color: green;' : 'color: red;';?>">$<?=number_format($neto, 0, ",", ".");?></td>
      <td align="center"><?=$cantidadTotal;?></td>
  </tr>
  <?php
      }
  }
  ?>
  <tr style="font-weight:bold; font-size:13px; background:#f0f0f0;">
      <td colspan="5" align="right"><strong>TOTALES:</strong></td>
      <td align="right" style="color: green;"><strong>$<?=number_format($totalGeneralIngresos, 0, ",", ".");?></strong></td>
      <td align="right" style="color: red;"><strong>$<?=number_format($totalGeneralEgresos, 0, ",", ".");?></strong></td>
      <td align="right" style="font-weight: bold; <?=$totalGeneralNeto >= 0 ? 'color: green;' : 'color: red;';?>"><strong>$<?=number_format($totalGeneralNeto, 0, ",", ".");?></strong></td>
      <td align="center"><strong><?=$totalGeneralMovimientos;?></strong></td>
  </tr>
  </table>
  </center>
  
    <?php include("../compartido/footer-informes.php"); ?>
</body>
</html>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>

