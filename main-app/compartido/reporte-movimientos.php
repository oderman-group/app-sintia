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
        <th>Consecutivo</th>
        <th>Usuario</th>
        <th>Fecha</th>
        <th>Detalle</th>
        <th>Total Neto</th>
        <th>Abonos</th>
        <th>Por Cobrar</th>
        <th>Tipo</th>
        <th>Estado</th>
        <th>Forma de pago</th>
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
                  
									$consulta = mysqli_query($conexion, "SELECT fc.*, uss.* FROM ".BD_FINANCIERA.".finanzas_cuentas fc
										LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss.uss_id=fc.fcu_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
										WHERE fc.institucion={$config['conf_id_institucion']} AND fc.year={$_SESSION["bd"]}
										{$filtro}
										ORDER BY fc.id_nuevo DESC");
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
                    $totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $resultado['fcu_id'], $vlrAdicional);
                    $abonos = Movimientos::calcularTotalAbonado($conexion, $config, $resultado['fcu_id']);
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
      <td><?=$resultado['fcu_id'];?></td>
      <td><?=$resultado['id_nuevo'] ?? $resultado['fcu_id'];?></td>
      <td><?=$nombreCompleto;?></td>
      <td><?=$resultado['fcu_fecha'];?></td>
      <td><?=$resultado['fcu_detalle'];?></td>
      <td><?=$prefijoTotal.$totalNetoFormateado;?></td>
      <td>$<?=$abonosFormateado;?></td>
      <td><?=$prefijoPorCobrar.$porCobrarFormateado;?></td>
      <td><?=$tipoFacturaTexto;?></td>
      <td><?=$estado;?></td>
      <td><?=$formasPagoFinanzas[$resultado['fcu_forma_pago']] ?? "N/A";?></td>
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
      <td colspan="5" align="right"><strong>TOTALES:</strong></td>
      <td><strong>$<?=$totalGeneralNetoFormateado;?></strong></td>
      <td><strong>$<?=$totalGeneralAbonosFormateado;?></strong></td>
      <td><strong>$<?=$totalGeneralPorCobrarFormateado;?></strong></td>
      <td colspan="5"></td>
  </tr>
  <?php
  ?>
  </table>
  </center>
    <?php include("../compartido/footer-informes.php"); ?>
</body>
</html>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>