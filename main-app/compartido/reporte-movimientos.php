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

// Array para tipos de factura (igual que en movimientos-tbody.php)
$estadosCuentas = array("", "Fact. Venta", "Fact. Compra");
?>
<head>
	<title>Movimientos Financieros</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>
<body style="font-family:Arial;">
  <?php
    // Construir título del informe con información de filtros
    $nombreInforme = "INFORME DE MOVIMIENTOS";
    $infoFiltros = array();
    
    if (!empty($_GET['desde']) && !empty($_GET['hasta'])) {
        $infoFiltros[] = "Período: " . $_GET['desde'] . " - " . $_GET['hasta'];
    }
    
    if (!empty($_GET['tipo'])) {
        $tipoFiltro = base64_decode($_GET['tipo']);
        if ($tipoFiltro == 1) {
            $infoFiltros[] = "Tipo: Fact. Venta";
        } elseif ($tipoFiltro == 2) {
            $infoFiltros[] = "Tipo: Fact. Compra";
        }
    }
    
    if (!empty($_GET['estadoFil'])) {
        $estadoFiltro = base64_decode($_GET['estadoFil']);
        if ($estadoFiltro == POR_COBRAR) {
            $infoFiltros[] = "Estado: Por Cobrar";
        } elseif ($estadoFiltro == COBRADA) {
            $infoFiltros[] = "Estado: Cobrada";
        }
    }
    
    if (!empty($_GET['mostrarAnuladas']) && $_GET['mostrarAnuladas'] == '1') {
        $infoFiltros[] = "Incluye anuladas";
    }
    
    if (!empty($_GET['usuario'])) {
        $usuarioFiltro = base64_decode($_GET['usuario']);
        $usuarioDatos = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".mysqli_real_escape_string($conexion, $usuarioFiltro)."'");
        $usuarioInfo = mysqli_fetch_array($usuarioDatos, MYSQLI_BOTH);
        if ($usuarioInfo) {
            $nombreUsuario = UsuariosPadre::nombreCompletoDelUsuario($usuarioInfo);
            $infoFiltros[] = "Usuario: " . $nombreUsuario;
        }
    }
    
    if (!empty($infoFiltros)) {
        $nombreInforme .= " (" . implode(" | ", $infoFiltros) . ")";
    }
    
    include("../compartido/head-informes.php");
  ?>
  <table bgcolor="#FFFFFF" width="100%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?= $Plataforma->colorUno; ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
        <th>ID</th>
        <th>Usuario</th>
        <th>Fecha</th>
        <th>Detalle</th>
        <th>Total Neto</th>
        <th>Abonos</th>
        <th>Por Cobrar</th>
        <th>Tipo</th>
        <th>Estado</th>
        <th>Observaciones</th>
        <th>Cerrado</th>
  </tr>
                  <?php
                  // Aplicar filtros desde GET
                  $filtro = '';
                  
                  // Filtro de fechas
                  if (!empty($_GET['desde']) && !empty($_GET['hasta'])) {
                      $desde = mysqli_real_escape_string($conexion, $_GET['desde']);
                      $hasta = mysqli_real_escape_string($conexion, $_GET['hasta']);
                      $filtro .= " AND (fc.fcu_fecha BETWEEN '{$desde}' AND '{$hasta}' OR fc.fcu_fecha LIKE '%{$hasta}%')";
                  }
                  
                  // Filtro de tipo
                  if (!empty($_GET['tipo'])) {
                      $tipoFiltro = base64_decode($_GET['tipo']);
                      if ($tipoFiltro !== '') {
                          $tipoSeguro = intval($tipoFiltro);
                          $filtro .= " AND fc.fcu_tipo = {$tipoSeguro}";
                      }
                  }
                  
                  // Filtro de estado
                  if (!empty($_GET['estadoFil'])) {
                      $estadoFiltro = base64_decode($_GET['estadoFil']);
                      if ($estadoFiltro !== '') {
                          $estadoSeguro = mysqli_real_escape_string($conexion, $estadoFiltro);
                          $filtro .= " AND fc.fcu_status = '{$estadoSeguro}'";
                      }
                  }
                  
                  // Filtro de anuladas
                  $mostrarAnuladas = !empty($_GET['mostrarAnuladas']) && $_GET['mostrarAnuladas'] == '1';
                  if (!$mostrarAnuladas) {
                      $filtro .= " AND fc.fcu_anulado = 0";
                  }
                  
                  // Filtro de usuario/cliente
                  if (!empty($_GET['usuario']) && trim($_GET['usuario']) !== '') {
                      $usuarioFiltro = base64_decode($_GET['usuario']);
                      if ($usuarioFiltro !== false && $usuarioFiltro !== '' && trim($usuarioFiltro) !== '') {
                          $usuarioSeguro = mysqli_real_escape_string($conexion, trim($usuarioFiltro));
                          $filtro .= " AND fc.fcu_usuario = '{$usuarioSeguro}'";
                      }
                  }
                  
                  // Filtro de método de pago
                  if (!empty($_GET['metodo_pago']) && trim($_GET['metodo_pago']) !== '') {
                      $metodoPago = mysqli_real_escape_string($conexion, $_GET['metodo_pago']);
                  // NOTA: El filtro de forma de pago ya no aplica a finanzas_cuentas.
                  // El medio de pago ahora solo está en payments_invoiced (abonos).
                  // Si se necesita filtrar por método de pago, debe hacerse a través de los abonos asociados.
                  }
                  
                  // Filtro de cuenta bancaria
                  if (!empty($_GET['cuenta_bancaria']) && trim($_GET['cuenta_bancaria']) !== '') {
                      $cuentaBancaria = mysqli_real_escape_string($conexion, $_GET['cuenta_bancaria']);
                      $filtro .= " AND fc.fcu_cuenta_bancaria_id = '{$cuentaBancaria}'";
                  }
                  
									$consulta = mysqli_query($conexion, "SELECT fc.*, uss.* FROM ".BD_FINANCIERA.".finanzas_cuentas fc
										LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss.uss_id=fc.fcu_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
										WHERE fc.institucion={$config['conf_id_institucion']} AND fc.year={$_SESSION["bd"]}
										AND (fc.fcu_status IS NULL OR fc.fcu_status != '".EN_PROCESO."')
										{$filtro}
										ORDER BY fc.fcu_id DESC");
                  $cont=0;
                  $totalGeneralNeto = 0;
                  $totalGeneralAbonos = 0;
                  $totalGeneralPorCobrar = 0;
									while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                    $u = UsuariosPadre::sesionUsuario($resultado['fcu_usuario']);
                    $cerrado = !empty($resultado['fcu_cerrado_usuario']) ? UsuariosPadre::sesionUsuario($resultado['fcu_cerrado_usuario']) : array();
                    $nombreCompleto = UsuariosPadre::nombreCompletoDelUsuario($u);
                    
                    // Calcular total neto incluyendo items (igual que en movimientos-tbody.php)
                    $vlrAdicional = !empty($resultado['fcu_valor']) ? $resultado['fcu_valor'] : 0;
                    // Usar fcu_id como identificador
                    $idParaCalculo = $resultado['fcu_id'];
                    $totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $idParaCalculo, $vlrAdicional);
                    $abonos = Movimientos::calcularTotalAbonado($conexion, $config, $idParaCalculo);
                    $porCobrar = $totalNeto - $abonos;
                    
                    // Determinar tipo de factura (igual que en movimientos-tbody.php)
                    $tipoFacturaTexto = $estadosCuentas[$resultado['fcu_tipo']] ?? 'N/D';
                    
                    // Determinar estado
                    $estado = 'Por Cobrar';
                    if ($resultado['fcu_status'] == COBRADA) {
                        $estado = 'Cobrada';
                    }
                    
                    // Acumular totales generales
                    if ($resultado['fcu_tipo'] == 1) {
                        // Factura Venta
                        $totalGeneralNeto += $totalNeto;
                        $totalGeneralAbonos += $abonos;
                        if ($resultado['fcu_status'] == POR_COBRAR) {
                            $totalGeneralPorCobrar += max(0, $porCobrar);
                        }
                    } else if ($resultado['fcu_tipo'] == 2) {
                        // Factura Compra
                        $totalGeneralNeto -= $totalNeto;
                    }
                    
                    // Formatear valores
                    $totalNetoFormateado = number_format((float)$totalNeto, 0, ",", ".");
                    $abonosFormateado = number_format((float)$abonos, 0, ",", ".");
                    $porCobrarFormateado = number_format(max(0, (float)$porCobrar), 0, ",", ".");
                    
                    // Prefijo para facturas de compra
                    $prefijoTotal = ($resultado['fcu_tipo'] == 2 && $totalNeto > 0) ? '-$' : '$';
                    $prefijoPorCobrar = ($resultado['fcu_tipo'] == 2 && $porCobrar > 0) ? '-$' : '$';
									?>
  <tr style="font-size:13px;">
      <td><?= (isset($resultado['fcu_consecutivo']) && $resultado['fcu_consecutivo'] !== '' && $resultado['fcu_consecutivo'] !== null) ? (int)$resultado['fcu_consecutivo'] : $resultado['fcu_id']; ?></td>
      <td><?=$nombreCompleto;?></td>
      <td><?=$resultado['fcu_fecha'];?></td>
      <td><?=$resultado['fcu_detalle'];?></td>
      <td><?=$prefijoTotal.$totalNetoFormateado;?></td>
      <td>$<?=$abonosFormateado;?></td>
      <td><?=$prefijoPorCobrar.$porCobrarFormateado;?></td>
      <td><?=$tipoFacturaTexto;?></td>
      <td><?=$estado;?></td>
      <td><?=$resultado['fcu_observaciones'];?></td>
      <td><?=$resultado['fcu_cerrado'];?> <br> <?php if(isset($cerrado[4])) echo strtoupper($cerrado['uss_nombre']);?></td>
</tr>
  <?php
  $cont++;
  }//Fin mientras que
  
  // Mostrar totales generales
  $totalGeneralNetoFormateado = number_format($totalGeneralNeto, 0, ",", ".");
  $totalGeneralAbonosFormateado = number_format($totalGeneralAbonos, 0, ",", ".");
  $totalGeneralPorCobrarFormateado = number_format($totalGeneralPorCobrar, 0, ",", ".");
  ?>
  <tr style="font-weight:bold; font-size:13px; background:#f0f0f0;">
      <td colspan="4" align="right"><strong>TOTALES:</strong></td>
      <td><strong>$<?=$totalGeneralNetoFormateado;?></strong></td>
      <td><strong>$<?=$totalGeneralAbonosFormateado;?></strong></td>
      <td><strong>$<?=$totalGeneralPorCobrarFormateado;?></strong></td>
      <td colspan="4"></td>
  </tr>
  <?php
  ?>
  </table>
  </center>
  
  <?php
  // Mostrar arqueo de caja si está solicitado
  if (!empty($_GET['mostrarArqueo']) && $_GET['mostrarArqueo'] == '1' && !empty($_GET['desde']) && !empty($_GET['hasta'])) {
      $tipoMovimiento = null;
      if (!empty($_GET['tipo'])) {
          $tipoFiltro = base64_decode($_GET['tipo']);
          if ($tipoFiltro !== '') {
              $tipoMovimiento = intval($tipoFiltro);
          }
      }
      
      $arqueo = Movimientos::obtenerArqueoCajaPorMetodo(
          $conexion, 
          $config, 
          $_GET['desde'], 
          $_GET['hasta'],
          $tipoMovimiento
      );
      
      if (!empty($arqueo['por_metodo'])) {
  ?>
  <br><br>
  <table bgcolor="#FFFFFF" width="100%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?= $Plataforma->colorUno; ?>;" align="center">
  <tr style="font-weight:bold; font-size:14px; height:35px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
      <th colspan="6" style="text-align:center;">ARQUEO DE CAJA - AGRUPADO POR MÉTODO DE PAGO Y CUENTA BANCARIA</th>
  </tr>
  <tr style="font-weight:bold; font-size:12px; height:30px; background:#e0e0e0;">
      <th>Método de Pago</th>
      <th>Cuenta Bancaria</th>
      <th>Ingresos</th>
      <th>Egresos</th>
      <th>Neto</th>
      <th>Cantidad Mov.</th>
  </tr>
  <?php
      $totalArqueoIngresos = 0;
      $totalArqueoEgresos = 0;
      $totalArqueoNeto = 0;
      
      foreach ($arqueo['por_metodo'] as $metodoPago => $datosMetodo) {
          $filaMetodo = true;
          foreach ($datosMetodo['cuentas'] as $cuenta) {
              $totalArqueoIngresos += $cuenta['ingresos'];
              $totalArqueoEgresos += $cuenta['egresos'];
              $totalArqueoNeto += $cuenta['neto'];
  ?>
  <tr style="font-size:12px;">
      <td><?= $filaMetodo ? $datosMetodo['nombre'] : ''; ?></td>
      <td><?= htmlspecialchars($cuenta['cuenta_nombre']); ?></td>
      <td align="right">$<?= number_format($cuenta['ingresos'], 0, ",", "."); ?></td>
      <td align="right">$<?= number_format($cuenta['egresos'], 0, ",", "."); ?></td>
      <td align="right">$<?= number_format($cuenta['neto'], 0, ",", "."); ?></td>
      <td align="center"><?= $cuenta['cantidad']; ?></td>
  </tr>
  <?php
              $filaMetodo = false;
          }
          
          // Fila de subtotal por método
          if (count($datosMetodo['cuentas']) > 1) {
  ?>
  <tr style="font-weight:bold; font-size:12px; background:#f5f5f5;">
      <td colspan="2" align="right">Subtotal <?= $datosMetodo['nombre']; ?>:</td>
      <td align="right">$<?= number_format($datosMetodo['total_ingresos'], 0, ",", "."); ?></td>
      <td align="right">$<?= number_format($datosMetodo['total_egresos'], 0, ",", "."); ?></td>
      <td align="right">$<?= number_format($datosMetodo['total_neto'], 0, ",", "."); ?></td>
      <td></td>
  </tr>
  <?php
          }
      }
  ?>
  <tr style="font-weight:bold; font-size:13px; background:#d0d0d0;">
      <td colspan="2" align="right"><strong>TOTAL GENERAL:</strong></td>
      <td align="right"><strong>$<?= number_format($totalArqueoIngresos, 0, ",", "."); ?></strong></td>
      <td align="right"><strong>$<?= number_format($totalArqueoEgresos, 0, ",", "."); ?></strong></td>
      <td align="right"><strong>$<?= number_format($totalArqueoNeto, 0, ",", "."); ?></strong></td>
      <td></td>
  </tr>
  </table>
  <?php
      }
  }
  ?>
    <?php include("../compartido/footer-informes.php"); ?>
</body>
</html>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>