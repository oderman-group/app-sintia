<?php include("session.php");?>
<?php $idPaginaInterna = 'AC0022';?>
<?php include("../compartido/head.php");?>
<?php 
require_once(ROOT_PATH."/main-app/class/Movimientos.php"); 
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Modulos.php");

// Obtener información del estudiante para código de tesorería (ICOLVEN)
$codigoTesoreria = '';
if($config['conf_id_institucion'] == ICOLVEN) {
    $consultaEstudiante = Estudiantes::listarEstudiantesParaAcudientes($datosUsuarioActual['uss_id']);
    if(mysqli_num_rows($consultaEstudiante) > 0) {
        $primerEstudiante = mysqli_fetch_array($consultaEstudiante, MYSQLI_BOTH);
        if(!empty($primerEstudiante['mat_codigo_tesoreria'])) {
            $codigoTesoreria = $primerEstudiante['mat_codigo_tesoreria'];
        }
    }
}
?>
<style>
    /* Variables CSS */
    :root {
        --primary-color: #2d3e50;
        --secondary-color: #41c1ba;
        --accent-color: #f39c12;
        --success-color: #27ae60;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --info-color: #3498db;
        --light-bg: #f8f9fa;
        --card-shadow: 0 2px 12px rgba(0,0,0,0.08);
        --card-shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Header Moderno */
    .estado-cuenta-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a252f 100%);
        border-radius: 15px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
        color: white;
    }

    .estado-cuenta-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .estado-cuenta-header p {
        margin: 8px 0 0 0;
        opacity: 0.9;
        font-size: 15px;
    }

    /* Cards de Resumen Financiero */
    .resumen-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .resumen-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        border-left: 5px solid;
        position: relative;
        overflow: hidden;
    }

    .resumen-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        opacity: 0.1;
        transform: translate(30px, -30px);
    }

    .resumen-card.deuda {
        border-left-color: var(--danger-color);
    }

    .resumen-card.deuda::before {
        background: var(--danger-color);
    }

    .resumen-card.recibido {
        border-left-color: var(--info-color);
    }

    .resumen-card.recibido::before {
        background: var(--info-color);
    }

    .resumen-card.saldo {
        border-left-color: var(--success-color);
    }

    .resumen-card.saldo.negativo {
        border-left-color: var(--danger-color);
    }

    .resumen-card.saldo.negativo::before {
        background: var(--danger-color);
    }

    .resumen-card.saldo::before {
        background: var(--success-color);
    }

    .resumen-card-label {
        font-size: 14px;
        font-weight: 600;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .resumen-card-value {
        font-size: 32px;
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }

    .resumen-card-message {
        font-size: 13px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #ecf0f1;
        color: #7f8c8d;
        font-style: italic;
    }

    /* Buscador */
    .search-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-input-modern {
        width: 100%;
        padding: 15px 50px 15px 20px;
        border: 2px solid #e0e6ed;
        border-radius: 12px;
        font-size: 16px;
        transition: var(--transition);
        background: #f8f9fa;
    }

    .search-input-modern:focus {
        outline: none;
        border-color: var(--secondary-color);
        background: white;
        box-shadow: 0 0 0 4px rgba(65, 193, 186, 0.1);
    }

    .search-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #95a5a6;
        font-size: 20px;
    }

    /* Tabla Moderna */
    .table-modern-container {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }

    .table-modern thead {
        background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
        color: white;
    }

    .table-modern thead th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .table-modern tbody tr {
        border-bottom: 1px solid #ecf0f1;
        transition: var(--transition);
    }

    .table-modern tbody tr:hover {
        background: #f8f9fa;
    }

    .table-modern tbody td {
        padding: 15px;
        font-size: 14px;
        color: #555;
    }

    .badge-financiero {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-pendiente {
        background: #ffeaa7;
        color: #d63031;
    }

    .badge-pagado {
        background: #d5f4e6;
        color: #00b894;
    }

    /* Estilos para expandir detalles */
    .detalle-movimiento-btn {
        cursor: pointer;
        color: var(--secondary-color);
        font-size: 14px;
        transition: transform 0.2s ease;
        padding: 5px;
    }

    .detalle-movimiento-btn:hover {
        color: var(--primary-color);
        transform: scale(1.2);
    }

    .detalle-movimiento-btn.expanded {
        transform: rotate(90deg);
    }

    .detalle-factura-wrapper {
        padding: 20px;
        background: #f8f9fa;
        border-left: 4px solid var(--secondary-color);
    }

    .detalle-factura-wrapper h5 {
        color: var(--secondary-color);
        font-weight: 600;
        margin-bottom: 15px;
    }

    .detalle-factura-wrapper ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .detalle-factura-wrapper li {
        padding: 5px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .detalle-factura-wrapper li:last-child {
        border-bottom: none;
    }

    .detalle-factura-wrapper strong {
        color: #333;
        min-width: 150px;
        display: inline-block;
    }

    /* Botones de Acción Especiales */
    .acciones-especiales {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
        border-left: 4px solid var(--warning-color);
    }

    .acciones-especiales h4 {
        margin: 0 0 15px 0;
        color: #856404;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-modern {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
    }

    .btn-info-modern {
        background: linear-gradient(135deg, var(--info-color), #2980b9);
        color: white;
    }

    .btn-info-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-success-modern {
        background: linear-gradient(135deg, var(--success-color), #229954);
        color: white;
    }

    .btn-success-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Estado Vacío */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        display: none;
    }

    .empty-state.show {
        display: block;
    }

    .empty-state-icon {
        font-size: 80px;
        color: #bdc3c7;
        margin-bottom: 20px;
    }

    .empty-state-title {
        font-size: 24px;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    .empty-state-text {
        color: #7f8c8d;
        font-size: 16px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .resumen-cards {
            grid-template-columns: 1fr;
        }

        .estado-cuenta-header {
            padding: 20px;
        }

        .estado-cuenta-header h1 {
            font-size: 22px;
        }

        .table-modern-container {
            padding: 15px;
            overflow-x: auto;
        }

        .table-modern {
            min-width: 600px;
        }
    }
</style>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <?php include("../../config-general/mensajes-informativos.php"); ?>
                    
                    <?php
                    // Calcular resumen financiero usando los mismos métodos que la tabla
                    $consultaFacturas = mysqli_query($conexion, "SELECT * FROM " . BD_FINANCIERA . ".finanzas_cuentas fc
                        WHERE fcu_usuario='{$_SESSION["id"]}' AND fcu_anulado=0
                        AND fc.institucion={$_SESSION['idInstitucion']} 
                        AND fc.year='{$_SESSION["bd"]}' 
                        ORDER BY fc.id_nuevo DESC");
                    
                    $totalFacturado = 0;
                    $totalAbonado = 0;
                    $totalPorCobrar = 0;
                    
                    while($factura = mysqli_fetch_array($consultaFacturas, MYSQLI_BOTH)){
                        $vlrAdicional = !empty($factura['fcu_valor']) ? $factura['fcu_valor'] : 0;
                        $totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $factura['fcu_id'], $vlrAdicional);
                        $abonos = Movimientos::calcularTotalAbonado($conexion, $config, $factura['fcu_id']);
                        $porCobrar = $totalNeto - $abonos;
                        
                        $totalFacturado += $totalNeto;
                        $totalAbonado += $abonos;
                        $totalPorCobrar += max(0, $porCobrar);
                    }
                    
                    $deuda = $totalFacturado;
                    $recibido = $totalAbonado;
                    $saldo = ($recibido - $deuda);
                    
                    $mensajeSaldo = $frases[309][$datosUsuarioActual['uss_idioma']];
                    $saldoClass = 'saldo';
                    if($saldo > 0) {
                        $mensajeSaldo = $frases[310][$datosUsuarioActual['uss_idioma']];
                        $saldoClass = 'saldo';
                    }
                    if($saldo < 0) {
                        $mensajeSaldo = $frases[311][$datosUsuarioActual['uss_idioma']];
                        $saldoClass = 'saldo negativo';
                    }
                    ?>
                    
                    <!-- Header -->
                    <div class="estado-cuenta-header">
                        <h1>
                            <i class="fa fa-money-check-alt"></i>
                            <?=$frases[104][$datosUsuarioActual['uss_idioma']];?>
                        </h1>
                        <p>Consulta tu información financiera y movimientos de cuenta</p>
                        <?php include("../compartido/texto-manual-ayuda.php"); ?>
                    </div>

                    <!-- Cards de Resumen -->
                    <div class="resumen-cards">
                        <div class="resumen-card deuda">
                            <div class="resumen-card-label">
                                <i class="fa fa-file-invoice-dollar"></i>
                                <?=strtoupper($frases[312][$datosUsuarioActual['uss_idioma']]);?>
                            </div>
                            <p class="resumen-card-value" id="cardTotalFacturado" style="color: var(--danger-color);">
                                $<?=number_format($deuda, 0, ",", ".");?>
                            </p>
                        </div>

                        <div class="resumen-card recibido">
                            <div class="resumen-card-label">
                                <i class="fa fa-hand-holding-usd"></i>
                                <?=strtoupper($frases[413][$datosUsuarioActual['uss_idioma']]);?>
                            </div>
                            <p class="resumen-card-value" id="cardTotalAbonado" style="color: var(--info-color);">
                                $<?=number_format($recibido, 0, ",", ".");?>
                            </p>
                        </div>

                        <div class="resumen-card <?=$saldoClass;?>">
                            <div class="resumen-card-label">
                                <i class="fa fa-wallet"></i>
                                <?=strtoupper($frases[315][$datosUsuarioActual['uss_idioma']]);?>
                            </div>
                            <p class="resumen-card-value" id="cardSaldo" style="color: <?= $saldo >= 0 ? 'var(--success-color)' : 'var(--danger-color)'; ?>;">
                                $<?=number_format($saldo, 0, ",", ".");?>
                            </p>
                            <div class="resumen-card-message">
                                <?=$mensajeSaldo;?>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Especiales -->
                    <?php if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_API_SION_ACADEMICA)) {?>
                    <div class="acciones-especiales">
                        <h4>
                            <i class="fa fa-info-circle"></i>
                            Información de Pago ICOLVEN
                        </h4>
                        <?php if(!empty($codigoTesoreria)) { ?>
                        <p><strong><?=$frases[316][$datosUsuarioActual['uss_idioma']];?>:</strong> 
                            <b><?=$codigoTesoreria;?></b>
                            (cuatro dígitos, sin el 0 a la izquierda).
                        </p>
                        <?php } ?>
                        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px;">
                            <a href="https://www.avalpaycenter.com/wps/portal/portal-de-pagos/web/pagos-aval/resultado-busqueda/realizar-pago-facturadores?idConv=00022724&origen=buscar" 
                               class="btn-modern btn-info-modern" target="_blank">
                                <i class="fa fa-credit-card"></i>
                                <?=strtoupper($frases[317][$datosUsuarioActual['uss_idioma']]);?>
                            </a>
                            <?php if(!empty($codigoTesoreria)) { ?>
                            <a href="http://sion.icolven.edu.co/Services/ServiceIcolven.svc/GenerarEstadoCuenta/<?=$codigoTesoreria;?>/<?=date('Y');?>" 
                               class="btn-modern btn-success-modern" target="_blank">
                                <i class="fa fa-file-pdf"></i>
                                <?=strtoupper($frases[104][$datosUsuarioActual['uss_idioma']]);?>
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php }?>

                    <!-- Buscador -->
                    <div class="search-container">
                        <div class="search-input-wrapper">
                            <input 
                                type="text" 
                                id="searchInput" 
                                class="search-input-modern" 
                                placeholder="Buscar movimientos por fecha, detalle o monto..."
                                autocomplete="off"
                            >
                            <i class="fa fa-search search-icon"></i>
                        </div>
                    </div>

                    <!-- Tabla de Movimientos -->
                    <div class="table-modern-container">
                        <h3 style="margin: 0 0 20px 0; color: var(--primary-color); display: flex; align-items: center; gap: 10px;">
                            <i class="fa fa-list-alt"></i>
                            Movimientos Financieros
                        </h3>
                        
                        <div style="overflow-x: auto;">
                            <table class="table-modern" id="movimientosTable">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;"></th>
                                        <th>#</th>
                                        <th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
                                        <th><?=$frases[162][$datosUsuarioActual['uss_idioma']];?></th>
                                        <th><?=$frases[107][$datosUsuarioActual['uss_idioma']];?></th>
                                        <th><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></th>
                                        <th><?=$frases[418][$datosUsuarioActual['uss_idioma']];?></th>
                                    </tr>
                                </thead>
                                <tbody id="movimientosTableBody">
                                    <?php
                                    mysqli_data_seek($consultaFacturas, 0); // Resetear el puntero
                                    $contReg = 1;
                                    $hayMovimientos = false;
                                    while($resultado = mysqli_fetch_array($consultaFacturas, MYSQLI_BOTH)){
                                        $hayMovimientos = true;
                                        $vlrAdicional = !empty($resultado['fcu_valor']) ? $resultado['fcu_valor'] : 0;
                                        $totalNeto    = Movimientos::calcularTotalNeto($conexion, $config, $resultado['fcu_id'], $vlrAdicional);
                                        $abonos       = Movimientos::calcularTotalAbonado($conexion, $config, $resultado['fcu_id']);
                                        $porCobrar    = $totalNeto - $abonos;
                                        
                                        $badgeClass = $porCobrar > 0 ? 'badge-pendiente' : 'badge-pagado';
                                        $badgeText = $porCobrar > 0 ? 'Pendiente' : 'Pagado';
                                    ?>
                                    <tr class="movimiento-row" 
                                        data-factura-id="<?= $resultado['fcu_id']; ?>"
                                        data-fecha="<?=strtolower($resultado['fcu_fecha']);?>"
                                        data-detalle="<?=strtolower($resultado['fcu_detalle']);?>"
                                        data-total="<?=$totalNeto;?>"
                                        data-abonos="<?=$abonos;?>"
                                        data-porcobrar="<?=$porCobrar;?>">
                                        <td>
                                            <i class="fa fa-chevron-right detalle-movimiento-btn" data-id="<?= $resultado['fcu_id']; ?>"></i>
                                        </td>
                                        <td><?=$contReg;?></td>
                                        <td>
                                            <strong><?=$resultado['fcu_fecha'];?></strong>
                                        </td>
                                        <td><?=$resultado['fcu_detalle'];?></td>
                                        <td id="totalNeto<?= $resultado['fcu_id']; ?>" data-tipo="<?= $resultado['fcu_tipo'] ?>" data-total-neto="<?= $totalNeto ?>">
                                            <strong>$<?=!empty($totalNeto) ? number_format($totalNeto, 0, ",", ".") : 0;?></strong>
                                        </td>
                                        <td id="abonos<?= $resultado['fcu_id']; ?>" data-abonos="<?= $abonos ?>">
                                            <span style="color: var(--info-color);">
                                                $<?=!empty($abonos) ? number_format($abonos, 0, ",", ".") : 0;?>
                                            </span>
                                        </td>
                                        <td id="porCobrar<?= $resultado['fcu_id']; ?>" data-por-cobrar="<?= $porCobrar ?>">
                                            <span class="badge-financiero <?=$badgeClass;?>" style="color: <?=$porCobrar > 0 ? 'var(--danger-color)' : 'var(--success-color)';?>; font-weight: 700;">
                                                $<?=!empty($porCobrar) ? number_format($porCobrar, 0, ",", ".") : 0;?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php 
                                        $contReg++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Estado Vacío -->
                        <?php if(!$hayMovimientos) { ?>
                        <div class="empty-state show">
                            <div class="empty-state-icon">
                                <i class="fa fa-receipt"></i>
                            </div>
                            <div class="empty-state-title">No hay movimientos registrados</div>
                            <div class="empty-state-text">Cuando se registren movimientos financieros, aparecerán aquí</div>
                        </div>
                        <?php } ?>
                    </div>

                    <!-- Estado Vacío para Búsqueda -->
                    <div class="empty-state" id="emptyState">
                        <div class="empty-state-icon">
                            <i class="fa fa-search"></i>
                        </div>
                        <div class="empty-state-title">No se encontraron movimientos</div>
                        <div class="empty-state-text">Intenta con otros términos de búsqueda</div>
                    </div>
                    
                </div>
            </div>
            <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    
    <script>
        // ============================================
        // FUNCIONALIDAD DE BÚSQUEDA
        // ============================================
        const searchInput = document.getElementById('searchInput');
        const movimientosTableBody = document.getElementById('movimientosTableBody');
        const movimientosTable = document.getElementById('movimientosTable');
        const emptyState = document.getElementById('emptyState');
        const allRows = document.querySelectorAll('.movimiento-row');
        
        let currentSearchTerm = '';
        
        function searchMovimientos() {
            let visibleCount = 0;
            
            if (allRows && allRows.length > 0) {
                allRows.forEach(row => {
                    const fecha = row.getAttribute('data-fecha') || '';
                    const detalle = row.getAttribute('data-detalle') || '';
                    const total = row.getAttribute('data-total') || '';
                    const abonos = row.getAttribute('data-abonos') || '';
                    const porCobrar = row.getAttribute('data-porcobrar') || '';
                    
                    // Verificar búsqueda
                    const searchText = currentSearchTerm.toLowerCase();
                    const matchesSearch = !searchText || 
                        fecha.includes(searchText) || 
                        detalle.includes(searchText) ||
                        total.includes(searchText.replace(/[^0-9]/g, '')) ||
                        abonos.includes(searchText.replace(/[^0-9]/g, '')) ||
                        porCobrar.includes(searchText.replace(/[^0-9]/g, ''));
                    
                    // Mostrar/ocultar
                    if (matchesSearch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
            
            // Mostrar estado vacío si no hay resultados
            if (emptyState) {
                if (visibleCount === 0 && currentSearchTerm !== '' && allRows.length > 0) {
                    emptyState.classList.add('show');
                    if (movimientosTable) movimientosTable.style.display = 'none';
                } else {
                    emptyState.classList.remove('show');
                    if (movimientosTable) movimientosTable.style.display = 'table';
                }
            }
        }
        
        // Event listener para búsqueda
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                currentSearchTerm = e.target.value;
                searchMovimientos();
            });
        }
        
        // ============================================
        // ATAJOS DE TECLADO
        // ============================================
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + F para enfocar búsqueda
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
            
            // Escape para limpiar búsqueda
            if (e.key === 'Escape' && searchInput === document.activeElement) {
                searchInput.value = '';
                currentSearchTerm = '';
                searchMovimientos();
                searchInput.blur();
            }
        });
        
        console.log('✨ Sistema de estado de cuenta cargado correctamente');
        if (allRows) {
            console.log('💰 Total de movimientos:', allRows.length);
        }
        
        // ============================================
        // FUNCIONALIDAD DE EXPANDIR DETALLES
        // ============================================
        function numberFormat(number, decimals = 0, decPoint = ',', thousandsSep = '.') {
            if (isNaN(number) || number === '' || number === null) {
                return '';
            }
            number = parseFloat(number.toFixed(decimals));
            var parts = number.toString().split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);
            return parts.join(decPoint);
        }
        
        // Manejar clic en botón de detalle
        $(document).on('click', '.detalle-movimiento-btn', function () {
            var $btn = $(this);
            var tr = $btn.closest('tr');
            var idFactura = $btn.data('id');
            
            // Si ya está expandido, colapsar
            if (tr.hasClass('detalle-abierto')) {
                tr.next('.child').remove();
                tr.removeClass('detalle-abierto');
                $btn.removeClass('expanded');
                totalizarMovimientos();
            } else {
                // Expandir
                $btn.addClass('expanded');
                tr.addClass('detalle-abierto');
                
                // Agregar fila de carga
                var loadingRow = $('<tr class="child"><td colspan="7"><div class="detalle-factura-wrapper">Cargando detalles...</div></td></tr>');
                tr.after(loadingRow);
                
                // Cargar detalles
                $.getJSON('../compartido/ajax-detalle-factura-estudiante.php', { idFactura: idFactura })
                    .done(function (resp) {
                        loadingRow.remove();
                        if (resp && resp.success) {
                            var detailRow = $('<tr class="child"><td colspan="7">' + resp.html + '</td></tr>');
                            tr.after(detailRow);
                        } else {
                            var errorRow = $('<tr class="child"><td colspan="7"><div class="detalle-factura-wrapper">No se encontraron detalles para esta factura.</div></td></tr>');
                            tr.after(errorRow);
                        }
                        totalizarMovimientos();
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        loadingRow.remove();
                        console.error('Error AJAX:', textStatus, errorThrown);
                        var errorRow = $('<tr class="child"><td colspan="7"><div class="detalle-factura-wrapper">Error al cargar los detalles. Intenta nuevamente.</div></td></tr>');
                        tr.after(errorRow);
                        totalizarMovimientos();
                    });
            }
        });
        
        // Función para calcular y actualizar totales
        function totalizarMovimientos() {
            var totalFacturado = 0;
            var totalAbonado = 0;
            var totalPorCobrar = 0;
            
            $('.movimiento-row').each(function() {
                var $row = $(this);
                if ($row.hasClass('child')) {
                    return; // Saltar filas de detalles
                }
                
                var $totalNeto = $row.find('td[data-total-neto]');
                var $abonos = $row.find('td[data-abonos]');
                var $porCobrar = $row.find('td[data-por-cobrar]');
                
                if ($totalNeto.length) {
                    var total = parseFloat($totalNeto.attr('data-total-neto')) || 0;
                    totalFacturado += total;
                }
                
                if ($abonos.length) {
                    var abonos = parseFloat($abonos.attr('data-abonos')) || 0;
                    totalAbonado += abonos;
                }
                
                if ($porCobrar.length) {
                    var porCobrar = parseFloat($porCobrar.attr('data-por-cobrar')) || 0;
                    totalPorCobrar += Math.max(0, porCobrar);
                }
            });
            
            var saldo = totalAbonado - totalFacturado;
            var saldoColor = saldo >= 0 ? 'var(--success-color)' : 'var(--danger-color)';
            
            // Actualizar cards
            $('#cardTotalFacturado').text('$' + numberFormat(totalFacturado, 0, ',', '.'));
            $('#cardTotalAbonado').text('$' + numberFormat(totalAbonado, 0, ',', '.'));
            $('#cardSaldo').text('$' + numberFormat(saldo, 0, ',', '.')).css('color', saldoColor);
        }
        
        // Calcular totales al cargar
        totalizarMovimientos();
    </script>
    <!-- end js include path -->
</body>

</html>