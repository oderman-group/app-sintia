<?php 
include("session.php");
$idPaginaInterna = 'DC0034';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("../compartido/head.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");

// Calcular porcentajes para uso en JavaScript
$sumaIndicadores = Indicadores::consultarSumaIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajePermitido = 100 - $sumaIndicadores[0];
$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
</head>
<style>
    /* ============================================
       VARIABLES Y ESTILOS GLOBALES
       ============================================ */
    :root {
        --primary-color: #2d3e50;
        --secondary-color: #41c1ba;
        --accent-color: #f39c12;
        --success-color: #27ae60;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --info-color: #3498db;
        --light-bg: #f8f9fa;
        --card-shadow: 0 2px 12px rgba(0,0,0,0.08);
        --card-shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --skeleton-base: #e0e0e0;
        --skeleton-shine: #f5f5f5;
    }

    /* Header Moderno */
    .indicadores-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a252f 100%);
        border-radius: 15px;
        padding: 25px 30px;
        margin-bottom: 25px;
        color: white;
        box-shadow: var(--card-shadow);
        position: relative;
        overflow: visible;
        z-index: 1;
    }

    .indicadores-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(65, 193, 186, 0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
        z-index: -1;
    }

    .indicadores-header-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }

    .indicadores-title {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }

    .indicadores-subtitle {
        font-size: 14px;
        opacity: 0.9;
        margin-top: 5px;
    }

    /* Dropdowns en Header */
    .indicadores-header .dropdown {
        position: relative;
        z-index: 2;
    }

    .indicadores-header .dropdown-menu {
        z-index: 1000 !important;
        max-height: 400px;
        overflow-y: auto;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        position: absolute !important;
    }

    .indicadores-header .dropdown.show {
        z-index: 1000 !important;
    }

    .indicadores-header .dropdown-menu .dropdown-item {
        padding: 10px 15px;
        transition: var(--transition);
    }

    .indicadores-header .dropdown-menu .dropdown-item:hover {
        background: #f8f9fa;
        padding-left: 20px;
    }

    /* Botón Configurar */
    .indicadores-header .btn-secondary-modern {
        transition: var(--transition);
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer !important;
        pointer-events: auto !important;
        position: relative;
        z-index: 10 !important;
    }

    .indicadores-header .btn-secondary-modern:hover {
        background: rgba(255,255,255,0.3) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        color: white !important;
        text-decoration: none !important;
        cursor: pointer !important;
    }

    .indicadores-header .btn-secondary-modern:focus {
        box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
        outline: none;
    }

    .indicadores-header .btn-secondary-modern:active {
        transform: translateY(0);
    }

    .indicadores-header .dropdown-menu .dropdown-item small {
        font-size: 11px;
        margin-top: 2px;
    }

    /* Tabs Modernos */
    .modern-tabs {
        background: white;
        border-radius: 15px;
        padding: 10px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .modern-tab {
        flex: 1;
        min-width: 200px;
        padding: 15px 25px;
        background: #f8f9fa;
        border: 2px solid transparent;
        border-radius: 10px;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
        font-weight: 600;
        color: var(--primary-color);
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        position: relative;
        overflow: hidden;
    }

    .modern-tab::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .modern-tab:hover {
        background: white;
        border-color: var(--secondary-color);
        color: var(--secondary-color);
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .modern-tab.active {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        color: white;
        border-color: var(--secondary-color);
        box-shadow: 0 4px 15px rgba(65, 193, 186, 0.3);
    }

    .modern-tab.active::before {
        transform: scaleX(1);
    }

    .modern-tab i {
        font-size: 18px;
    }

    /* Tab Content */
    .tab-content-modern {
        min-height: 400px;
    }

    .tab-pane-modern {
        display: none;
        animation: fadeInUp 0.4s ease-out;
    }

    .tab-pane-modern.active {
        display: block;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ============================================
       SKELETON LOADERS
       ============================================ */
    .skeleton-loader {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        margin-bottom: 20px;
    }

    .skeleton {
        background: linear-gradient(90deg, var(--skeleton-base) 25%, var(--skeleton-shine) 50%, var(--skeleton-base) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
    }

    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }

    .skeleton-header {
        height: 40px;
        width: 60%;
        margin-bottom: 20px;
    }

    .skeleton-line {
        height: 20px;
        margin-bottom: 15px;
    }

    .skeleton-line:last-child {
        width: 80%;
    }

    .skeleton-card {
        height: 150px;
        margin-bottom: 15px;
    }

    .skeleton-table-row {
        height: 50px;
        margin-bottom: 10px;
    }

    .skeleton-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    /* ============================================
       INDICADORES - ESTILO DE TABLA MODERNA
       ============================================ */
    .indicadores-table-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        margin-top: 20px;
        position: relative;
    }

    .indicadores-table-container .table-responsive {
        position: relative;
        overflow-x: auto;
        overflow-y: visible;
    }
    
    @media (min-width: 769px) {
        .indicadores-table-container .table-responsive {
            overflow-y: visible !important;
        }
    }

    .indicadores-table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .indicadores-table-modern thead {
        background: linear-gradient(135deg, var(--primary-color), #1a252f);
    }

    .indicadores-table-modern thead th {
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid rgba(255,255,255,0.2);
    }

    .indicadores-table-modern thead th:first-child {
        border-top-left-radius: 10px;
    }

    .indicadores-table-modern thead th:last-child {
        border-top-right-radius: 10px;
    }

    .indicadores-table-modern tbody tr {
        transition: var(--transition);
        border-bottom: 1px solid #f0f0f0;
        position: relative;
        z-index: 1;
    }

    .indicadores-table-modern tbody tr:hover {
        background: #f8f9fa;
        z-index: 2;
    }

    .indicadores-table-modern tbody tr:has(.btn-group.open) {
        z-index: 1000;
    }

    .indicadores-table-modern tbody td {
        padding: 15px;
        font-size: 14px;
        color: #555;
        vertical-align: middle;
        position: relative;
    }

    .indicadores-table-modern tbody td:nth-child(5) {
        position: relative;
        z-index: 100;
    }

    .indicadores-table-modern tbody td:nth-child(3) {
        color: var(--primary-color);
        line-height: 1.5;
    }

    .indicadores-table-modern tfoot {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .indicadores-table-modern tfoot td {
        padding: 15px;
        font-weight: 700;
        font-size: 15px;
        color: var(--primary-color);
        border-top: 2px solid #e0e6ed;
    }

    .indicadores-table-modern tfoot td:first-child {
        border-bottom-left-radius: 10px;
    }

    .indicadores-table-modern tfoot td:last-child {
        border-bottom-right-radius: 10px;
    }

    .indicadores-table-modern .btn-group {
        position: static;
    }

    .indicadores-table-modern tbody tr {
        position: relative;
        z-index: 1;
    }

    .indicadores-table-modern tbody tr:hover {
        z-index: 2;
    }

    .indicadores-table-modern tbody tr:has(.btn-group.open),
    .indicadores-table-modern tbody tr .btn-group.open {
        z-index: 1000;
    }
    
    .indicadores-table-modern tbody tr[style*="z-index: 1000"] {
        z-index: 1000 !important;
    }
    
    .indicadores-table-modern tbody tr[style*="z-index: 1000"] td:nth-child(5) {
        z-index: 1001;
    }

    .indicadores-table-modern .btn-group .btn {
        border-radius: 8px;
        padding: 8px 15px;
        font-size: 13px;
        font-weight: 600;
        transition: var(--transition);
        position: relative;
    }

    .indicadores-table-modern .btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .indicadores-table-modern .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        border: none;
        padding: 5px 0;
        position: absolute !important;
        z-index: 1001 !important;
        margin-top: 5px;
    }

    .indicadores-table-modern .dropdown-menu li a {
        padding: 8px 15px;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .indicadores-table-modern .dropdown-menu li a:hover {
        background: #f8f9fa;
        padding-left: 20px;
    }

    .indicadores-table-modern .dropdown-menu li a i {
        width: 18px;
        text-align: center;
    }

    /* Botones de Acción */
    .actions-bar {
        background: white;
        border-radius: 15px;
        padding: 20px 25px;
        margin-bottom: 20px;
        box-shadow: var(--card-shadow);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }

    .btn-primary-modern {
        padding: 12px 24px;
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(65, 193, 186, 0.4);
        text-decoration: none;
        color: white;
    }

    .btn-secondary-modern {
        padding: 12px 24px;
        background: white;
        color: var(--primary-color);
        border: 2px solid #e0e6ed;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .btn-secondary-modern:hover {
        background: #f8f9fa;
        border-color: var(--secondary-color);
        color: var(--secondary-color);
        transform: translateY(-2px);
        text-decoration: none;
    }

    /* Alertas Modernas */
    .alert-modern {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-warning-modern {
        background: #fff3cd;
        color: #856404;
        border-left: 4px solid #ffc107;
    }

    .alert-info-modern {
        background: #d1ecf1;
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }

    /* Estadísticas del Porcentaje */
    .porcentaje-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .porcentaje-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--card-shadow);
        text-align: center;
    }

    .porcentaje-label {
        font-size: 12px;
        color: #7f8c8d;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    .porcentaje-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--secondary-color);
    }

    .porcentaje-progress {
        height: 6px;
        background: #f0f0f0;
        border-radius: 3px;
        margin-top: 10px;
        overflow: hidden;
    }

    .porcentaje-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
        border-radius: 3px;
        transition: width 0.6s ease;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
    }

    .empty-state i {
        font-size: 64px;
        color: #bdc3c7;
        margin-bottom: 20px;
    }

    .empty-state h4 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #7f8c8d;
        margin-bottom: 20px;
    }

    /* Asegurar que el menú lateral tenga prioridad */
    .sidebar, .main-sidebar, .nav-sidebar, .sidebar-menu {
        z-index: 99999 !important;
        position: relative !important;
    }
    
    .sidebar .nav-item, .main-sidebar .nav-item, .nav-sidebar .nav-item {
        z-index: 99999 !important;
        position: relative !important;
    }
    
    .sidebar .nav-link, .main-sidebar .nav-link, .nav-sidebar .nav-link {
        z-index: 99999 !important;
        position: relative !important;
        pointer-events: auto !important;
    }
    
    /* Específicamente para los primeros elementos del menú */
    .sidebar .nav-item:first-child,
    .sidebar .nav-item:nth-child(2),
    .main-sidebar .nav-item:first-child,
    .main-sidebar .nav-item:nth-child(2),
    .nav-sidebar .nav-item:first-child,
    .nav-sidebar .nav-item:nth-child(2) {
        z-index: 999999 !important;
        position: relative !important;
    }
    
    .sidebar .nav-item:first-child .nav-link,
    .sidebar .nav-item:nth-child(2) .nav-link,
    .main-sidebar .nav-item:first-child .nav-link,
    .main-sidebar .nav-item:nth-child(2) .nav-link,
    .nav-sidebar .nav-item:first-child .nav-link,
    .nav-sidebar .nav-item:nth-child(2) .nav-link {
        z-index: 999999 !important;
        position: relative !important;
        pointer-events: auto !important;
        cursor: pointer !important;
    }
    
    /* Eliminar cualquier overlay invisible */
    * {
        pointer-events: auto !important;
    }
    
    .indicadores-header,
    .indicadores-header *,
    .indicadores-header::before,
    .indicadores-header::after {
        pointer-events: none !important;
    }
    
    .indicadores-header .dropdown,
    .indicadores-header .dropdown * {
        pointer-events: auto !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .indicadores-table-container {
            padding: 15px;
            overflow-x: auto;
        }

        .indicadores-table-modern {
            font-size: 12px;
        }

        .indicadores-table-modern thead th,
        .indicadores-table-modern tbody td {
            padding: 10px 8px;
        }

        .modern-tabs {
            flex-direction: column;
        }

        .modern-tab {
            width: 100%;
        }

        .indicadores-header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .actions-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary-modern,
        .btn-secondary-modern {
            justify-content: center;
            width: 100%;
        }
    }

    /* Loader GIF Override */
    #gifCarga {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
    }

    #gifCarga.active {
        display: flex;
    }

    /* Estilos para Tab de Notas (inyectados globalmente) */
    .notas-table-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        overflow-x: auto;
    }

    .notas-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .notas-table thead th {
        background: linear-gradient(135deg, var(--primary-color), #1a252f);
        color: white;
        padding: 15px 10px;
        text-align: center;
        font-weight: 600;
        font-size: 12px;
        position: sticky;
        top: 0;
        z-index: 10;
        white-space: nowrap;
    }

    .notas-table thead th:first-child {
        border-top-left-radius: 10px;
    }

    .notas-table thead th:last-child {
        border-top-right-radius: 10px;
    }

    .notas-table tbody tr {
        transition: var(--transition);
    }

    .notas-table tbody tr:hover {
        background: #f8f9fa;
    }

    .notas-table tbody td {
        padding: 15px 10px;
        border-bottom: 1px solid #f0f0f0;
        text-align: center;
        font-size: 14px;
    }

    .notas-table tbody td:first-child {
        font-weight: 600;
        color: var(--primary-color);
    }

    .notas-table tbody .estudiante-cell {
        text-align: left;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .estudiante-foto {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e0e0e0;
        flex-shrink: 0;
    }

    .estudiante-nombre {
        font-weight: 600;
        color: var(--primary-color);
    }

    .nota-link {
        padding: 6px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
        display: inline-block;
    }

    .nota-aprobado {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .nota-reprobado {
        background: #ffebee;
        color: #c62828;
    }

    .nota-pendiente {
        background: #f5f5f5;
        color: #616161;
    }

    .nota-link:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-decoration: none;
    }

    .definitiva-cell {
        font-weight: 700;
        font-size: 16px;
    }

    .indicador-header-cell {
        width: 200px;
        min-width: 200px;
        max-width: 200px;
        word-wrap: break-word;
        line-height: 1.3;
    }

    .indicador-header-cell small {
        display: block;
        font-size: 10px;
        opacity: 0.8;
        margin-top: 4px;
    }

    .indicador-nombre-truncado {
        display: block;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        line-height: 1.3;
        max-width: 100%;
    }
</style>
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
                    <?php include(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>

                    <!-- Header Moderno -->
                    <div class="indicadores-header">
                        <div class="indicadores-header-content">
                            <div>
                                <h1 class="indicadores-title">
                                    <i class="fa fa-chart-line mr-2"></i>
                                    <?=$frases[63][$datosUsuarioActual['uss_idioma']];?>
                                </h1>
                                <p class="indicadores-subtitle">
                                    <?= strtoupper($datosCargaActual['mat_nombre']); ?> • 
                                    <?= strtoupper($datosCargaActual['gra_nombre'] . " " . $datosCargaActual['gru_nombre']); ?> • 
                                    Periodo <?= $periodoConsultaActual; ?>
                                </p>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <!-- Selectores de Carga y Periodo -->
                                <div class="dropdown">
                                    <button class="btn-secondary-modern dropdown-toggle" type="button" id="dropdownCarga" 
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white;">
                                        <i class="fa fa-exchange-alt mr-2"></i>
                                        Cambiar Carga
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownCarga" style="max-height: 400px; overflow-y: auto;">
                                        <?php
                                        require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
                                        $cCargas = CargaAcademica::traerCargasDocentes($config, $_SESSION["id"]);
                                        while ($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)) {
                                            $estiloActivo = ($rCargas['car_id'] == $cargaConsultaActual) ? 'background: #f0f8ff; font-weight: 700; color: #2196f3;' : '';
                                            $iconoDG = ($rCargas['car_director_grupo'] == 1) ? '<i class="fa fa-star text-warning mr-1"></i>' : '';
                                        ?>
                                            <a class="dropdown-item" 
                                               href="<?= $_SERVER['PHP_SELF']; ?>?carga=<?= base64_encode($rCargas['car_id']); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>&get=<?= base64_encode(100); ?>"
                                               style="<?= $estiloActivo; ?>">
                                                <?= $iconoDG; ?>
                                                <?= strtoupper($rCargas['mat_nombre']); ?>
                                                <small class="d-block text-muted"><?= strtoupper($rCargas['gra_nombre'] . " " . $rCargas['gru_nombre']); ?></small>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="dropdown">
                                    <button class="btn-secondary-modern dropdown-toggle" type="button" id="dropdownPeriodo" 
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white;">
                                        <i class="fa fa-calendar mr-2"></i>
                                        Periodo <?= $periodoConsultaActual; ?>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownPeriodo">
                                        <?php
                                        require_once(ROOT_PATH."/main-app/class/Grados.php");
                                        for ($i = 1; $i <= $datosCargaActual['gra_periodos']; $i++) {
                                            $periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $i);
                                            $porcentajeGrado = !empty($periodosCursos['gvp_valor']) ? $periodosCursos['gvp_valor'] : 25;
                                            $estiloActivo = ($i == $periodoConsultaActual) ? 'background: #f0f8ff; font-weight: 700; color: #2196f3;' : '';
                                            $badge = ($i == $datosCargaActual['car_periodo']) ? '<span class="badge badge-success ml-2">Actual</span>' : '';
                                        ?>
                                            <a class="dropdown-item" 
                                               href="<?= $_SERVER['PHP_SELF']; ?>?carga=<?= base64_encode($cargaConsultaActual); ?>&periodo=<?= base64_encode($i); ?>&get=<?= base64_encode(100); ?>"
                                               style="<?= $estiloActivo; ?>">
                                                Periodo <?= $i; ?> (<?= $porcentajeGrado; ?>%)
                                                <?= $badge; ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>

                                <!-- Botón Configurar -->
                                <a href="cargas-configurar.php?carga=<?= base64_encode($cargaConsultaActual); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>" 
                                   class="btn-secondary-modern" 
                                   style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white; text-decoration: none; cursor: pointer; pointer-events: auto; position: relative; z-index: 10;"
                                   onclick="window.location.href=this.href; return false;">
                                    <i class="fa fa-cog mr-2"></i>
                                    Configurar
                                </a>

                                <?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Modernos -->
                    <div class="modern-tabs">
                        <a class="modern-tab active" id="tab-indicadores" data-tab="indicadores" href="javascript:void(0);">
                            <i class="fa fa-list"></i>
                            <span>Indicadores</span>
                        </a>
                        <a class="modern-tab" id="tab-notas" data-tab="notas" href="javascript:void(0);">
                            <i class="fa fa-chart-bar"></i>
                            <span>Notas por Indicador</span>
                        </a>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content-modern">
                        <!-- Tab Indicadores -->
                        <div class="tab-pane-modern active" id="content-indicadores">
                            <!-- Skeleton Loader -->
                            <div class="skeleton-loader" id="skeleton-indicadores">
                                <div class="skeleton skeleton-header"></div>
                                <div class="skeleton-grid">
                                    <div class="skeleton skeleton-card"></div>
                                    <div class="skeleton skeleton-card"></div>
                                    <div class="skeleton skeleton-card"></div>
                                    <div class="skeleton skeleton-card"></div>
                                </div>
                            </div>
                            
                            <!-- Contenido Real -->
                            <div id="indicadores-content" style="display: none;"></div>
                        </div>

                        <!-- Tab Notas por Indicador -->
                        <div class="tab-pane-modern" id="content-notas">
                            <!-- Skeleton Loader -->
                            <div class="skeleton-loader" id="skeleton-notas">
                                <div class="skeleton skeleton-header"></div>
                                <?php for($i = 0; $i < 5; $i++): ?>
                                    <div class="skeleton skeleton-table-row"></div>
                                <?php endfor; ?>
                            </div>
                            
                            <!-- Contenido Real -->
                            <div id="notas-content" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- ============================================
                         MODALES GLOBALES
                         ============================================ -->
                    
                    <!-- Modal Agregar Indicador -->
                    <div class="modal fade" id="modalAgregarIndicador" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" style="border-radius: 15px; border: none;">
                                <div class="modal-header" style="background: linear-gradient(135deg, var(--secondary-color), var(--accent-color)); color: white; border-radius: 15px 15px 0 0;">
                                    <h5 class="modal-title" style="font-weight: 700;">
                                        <i class="fa fa-plus-circle mr-2"></i>Agregar Indicador
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.9;">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" style="padding: 25px;">
                                    <form id="formAgregarIndicador">
                                        <input type="hidden" name="configInd" value="<?= $datosCargaActual['car_valor_indicador']; ?>">
                                        
                                        <div class="form-group">
                                            <label style="font-weight: 600; color: var(--primary-color);">
                                                <i class="fa fa-align-left mr-2"></i>Descripción del Indicador
                                            </label>
                                            <textarea name="contenido" class="form-control" rows="3" placeholder="Ej: Identifica los elementos de una narración..." required style="border-radius: 10px;"></textarea>
                                            <small class="form-text text-muted">Escribe una descripción clara y específica del indicador</small>
                                        </div>

                                        <?php if($datosCargaActual['car_valor_indicador']==1){?>
                                            <div class="alert-modern alert-info-modern" style="margin-bottom: 15px;">
                                                <i class="fa fa-info-circle"></i>
                                                <span><b>Valor máximo restante:</b> <?= $porcentajeRestante; ?>%. Si superas este valor, el sistema lo ajustará automáticamente.</span>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label style="font-weight: 600; color: var(--primary-color);">
                                                    <i class="fa fa-percent mr-2"></i>Valor Porcentual (%)
                                                </label>
                                                <input type="number" name="valor" class="form-control" placeholder="Ej: 10" min="1" max="<?= $porcentajeRestante; ?>" required style="border-radius: 10px;">
                                                <small class="form-text text-muted">Ingresa el porcentaje que representa este indicador</small>
                                            </div>
                                        <?php }?>
                                        
                                        <?php if($datosCargaActual['car_saberes_indicador']==1){?>
                                            <div class="form-group">
                                                <label style="font-weight: 600; color: var(--primary-color);">
                                                    <i class="fa fa-graduation-cap mr-2"></i>Tipo de Evaluación
                                                </label>
                                                <select class="form-control" name="saberes" required style="border-radius: 10px;">
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1">Saber saber (55%)</option>
                                                    <option value="2">Saber hacer (35%)</option>
                                                    <option value="3">Saber ser (10%)</option>
                                                </select>
                                            </div>
                                        <?php }else{?>
                                            <input type="hidden" name="saberes" value="0">
                                        <?php }?>
                                    </form>
                                </div>
                                <div class="modal-footer" style="border-top: 1px solid #f0f0f0; padding: 20px 25px;">
                                    <button type="button" class="btn-secondary-modern" data-dismiss="modal">
                                        <i class="fa fa-times mr-2"></i>Cancelar
                                    </button>
                                    <button type="button" onclick="guardarIndicador()" class="btn-primary-modern" id="btnGuardarIndicador">
                                        <i class="fa fa-save mr-2"></i>Guardar Indicador
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Editar Indicador -->
                    <div class="modal fade" id="modalEditarIndicador" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" style="border-radius: 15px; border: none;">
                                <div class="modal-header" style="background: linear-gradient(135deg, #2196f3, #1976d2); color: white; border-radius: 15px 15px 0 0;">
                                    <h5 class="modal-title" style="font-weight: 700;">
                                        <i class="fa fa-edit mr-2"></i>Editar Indicador
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.9;">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" style="padding: 25px;">
                                    <form id="formEditarIndicador">
                                        <input type="hidden" name="idR" id="edit_idR">
                                        <input type="hidden" name="idInd" id="edit_idInd">
                                        <input type="hidden" name="valorIndicador" id="edit_valorIndicador">
                                        
                                        <div class="form-group">
                                            <label style="font-weight: 600; color: var(--primary-color);">
                                                <i class="fa fa-align-left mr-2"></i>Descripción del Indicador
                                            </label>
                                            <textarea name="contenido" id="edit_contenido" class="form-control" rows="3" required style="border-radius: 10px;"></textarea>
                                        </div>

                                        <?php if($datosCargaActual['car_valor_indicador']==1){?>
                                            <div class="alert-modern alert-info-modern" style="margin-bottom: 15px;" id="edit_porcentaje_info">
                                                <i class="fa fa-info-circle"></i>
                                                <span id="edit_porcentaje_text"></span>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label style="font-weight: 600; color: var(--primary-color);">
                                                    <i class="fa fa-percent mr-2"></i>Valor Porcentual (%)
                                                </label>
                                                <input type="number" name="valor" id="edit_valor" class="form-control" min="1" required style="border-radius: 10px;">
                                            </div>
                                        <?php }else{?>
                                            <div class="form-group">
                                                <label style="font-weight: 600; color: var(--primary-color);">
                                                    <i class="fa fa-percent mr-2"></i>Valor Porcentual (%) <span style="background: #ffc107; padding: 2px 8px; border-radius: 4px; font-size: 11px;">Automático</span>
                                                </label>
                                                <input type="text" id="edit_valor_readonly" class="form-control" readonly style="border-radius: 10px; background: #f8f9fa;">
                                            </div>
                                        <?php }?>
                                        
                                        <?php if($datosCargaActual['car_saberes_indicador']==1){?>
                                            <div class="form-group">
                                                <label style="font-weight: 600; color: var(--primary-color);">
                                                    <i class="fa fa-graduation-cap mr-2"></i>Tipo de Evaluación
                                                </label>
                                                <select class="form-control" name="saberes" id="edit_saberes" required style="border-radius: 10px;">
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1">Saber saber (55%)</option>
                                                    <option value="2">Saber hacer (35%)</option>
                                                    <option value="3">Saber ser (10%)</option>
                                                </select>
                                            </div>
                                        <?php }else{?>
                                            <input type="hidden" name="saberes" id="edit_saberes" value="0">
                                        <?php }?>
                                    </form>
                                </div>
                                <div class="modal-footer" style="border-top: 1px solid #f0f0f0; padding: 20px 25px;">
                                    <button type="button" class="btn-secondary-modern" data-dismiss="modal">
                                        <i class="fa fa-times mr-2"></i>Cancelar
                                    </button>
                                    <button type="button" onclick="actualizarIndicador()" class="btn-primary-modern" id="btnActualizarIndicador">
                                        <i class="fa fa-save mr-2"></i>Actualizar Indicador
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>

    <!-- GIF de Carga -->
    <div id="gifCarga" class="gif-carga">
        <div style="text-align: center; color: white;">
            <i class="fa fa-spinner fa-spin" style="font-size: 48px; margin-bottom: 15px;"></i>
            <p style="font-size: 16px; font-weight: 600;">Procesando...</p>
        </div>
    </div>

    <!-- start js include path -->
	<script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js"></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js"></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->

    <script>
        // ============================================
        // VARIABLES GLOBALES
        // ============================================
        
        const CARGA = '<?php echo isset($cargaConsultaActual) ? base64_encode($cargaConsultaActual) : ''; ?>';
        const PERIODO = '<?php echo isset($periodoConsultaActual) ? base64_encode($periodoConsultaActual) : ''; ?>';
        const PORCENTAJE_RESTANTE_GLOBAL = <?php echo isset($porcentajeRestante) ? (float)$porcentajeRestante : 0; ?>;
        const VALOR_INDICADOR_CONFIG = <?php echo isset($datosCargaActual['car_valor_indicador']) ? (float)$datosCargaActual['car_valor_indicador'] : 0; ?>;
        
        // Debug: Verificar variables
        console.log('CARGA:', CARGA);
        console.log('PERIODO:', PERIODO);
        console.log('PORCENTAJE_RESTANTE_GLOBAL:', PORCENTAJE_RESTANTE_GLOBAL);
        console.log('VALOR_INDICADOR_CONFIG:', VALOR_INDICADOR_CONFIG);
        
        // Validar que las variables críticas no estén vacías
        if (!CARGA || !PERIODO) {
            console.error('❌ Error: Variables CARGA o PERIODO están vacías');
            console.error('CARGA:', CARGA);
            console.error('PERIODO:', PERIODO);
        }
        
        // ============================================
        // FUNCIONES DE MODALES - DEFINIDAS GLOBALMENTE
        // ============================================
        
        window.abrirModalAgregar = function() {
            $('#formAgregarIndicador')[0].reset();
            $('#modalAgregarIndicador').modal('show');
        };
        
        window.abrirModalEditar = function(ipcId) {
            // Mostrar loading en el modal
            $('#btnActualizarIndicador').html('<i class="fa fa-spinner fa-spin mr-2"></i>Cargando...').prop('disabled', true);
            $('#modalEditarIndicador').modal('show');
            
            // Cargar datos del indicador
            $.ajax({
                url: 'indicadores-obtener-datos.php',
                type: 'GET',
                data: { idR: window.btoa(ipcId) },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const indicador = response.data;
                        
                        // Llenar formulario
                        $('#edit_idR').val(indicador.ipc_id);
                        $('#edit_idInd').val(indicador.ipc_indicador);
                        $('#edit_valorIndicador').val(indicador.ipc_valor);
                        $('#edit_contenido').val(indicador.ind_nombre);
                        $('#edit_saberes').val(indicador.ipc_evaluacion);
                        
                        if (VALOR_INDICADOR_CONFIG == 1) {
                            $('#edit_valor').val(indicador.ipc_valor);
                            const porcentajeRestanteEdit = PORCENTAJE_RESTANTE_GLOBAL + parseFloat(indicador.ipc_valor);
                            $('#edit_porcentaje_text').html('<b>Valor máximo restante:</b> ' + porcentajeRestanteEdit + '%. Si superas este valor, el sistema lo ajustará automáticamente.');
                            $('#edit_valor').attr('max', porcentajeRestanteEdit);
                        } else {
                            $('#edit_valor_readonly').val(indicador.ipc_valor);
                        }
                        
                        $('#btnActualizarIndicador').html('<i class="fa fa-save mr-2"></i>Actualizar Indicador').prop('disabled', false);
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: response.message || 'No se pudieron cargar los datos del indicador',
                            position: 'top-right',
                            loaderBg: '#e74c3c',
                            icon: 'error',
                            hideAfter: 3000
                        });
                        $('#modalEditarIndicador').modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    $.toast({
                        heading: 'Error',
                        text: 'Error al cargar los datos del indicador',
                        position: 'top-right',
                        loaderBg: '#e74c3c',
                        icon: 'error',
                        hideAfter: 3000
                    });
                    $('#modalEditarIndicador').modal('hide');
                }
            });
        };
        
        window.guardarIndicador = function() {
            const form = $('#formAgregarIndicador')[0];
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const formData = new FormData(form);
            const btnGuardar = $('#btnGuardarIndicador');
            const originalText = btnGuardar.html();
            
            // Deshabilitar botón y mostrar loading
            btnGuardar.html('<i class="fa fa-spinner fa-spin mr-2"></i>Guardando...').prop('disabled', true);
            
            // Enviar petición AJAX
            $.ajax({
                url: `indicadores-guardar.php?carga=${encodeURIComponent(CARGA)}&periodo=${encodeURIComponent(PERIODO)}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#modalAgregarIndicador').modal('hide');
                    
                    $.toast({
                        heading: '¡Éxito!',
                        text: 'Indicador agregado correctamente',
                        position: 'top-right',
                        loaderBg: '#41c1ba',
                        icon: 'success',
                        hideAfter: 3000
                    });
                    
                    // Recargar indicadores
                    setTimeout(() => {
                        window.recargarInclude();
                    }, 500);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    $.toast({
                        heading: 'Error',
                        text: 'No se pudo guardar el indicador',
                        position: 'top-right',
                        loaderBg: '#e74c3c',
                        icon: 'error',
                        hideAfter: 3000
                    });
                },
                complete: function() {
                    btnGuardar.html(originalText).prop('disabled', false);
                }
            });
        };
        
        window.actualizarIndicador = function() {
            const form = $('#formEditarIndicador')[0];
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const formData = new FormData(form);
            const btnActualizar = $('#btnActualizarIndicador');
            const originalText = btnActualizar.html();
            
            // Deshabilitar botón y mostrar loading
            btnActualizar.html('<i class="fa fa-spinner fa-spin mr-2"></i>Actualizando...').prop('disabled', true);
            
            // Enviar petición AJAX
            $.ajax({
                url: `indicadores-actualizar.php?carga=${encodeURIComponent(CARGA)}&periodo=${encodeURIComponent(PERIODO)}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#modalEditarIndicador').modal('hide');
                    
                    $.toast({
                        heading: '¡Actualizado!',
                        text: 'Indicador actualizado correctamente',
                        position: 'top-right',
                        loaderBg: '#41c1ba',
                        icon: 'success',
                        hideAfter: 3000
                    });
                    
                    // Recargar indicadores
                    setTimeout(() => {
                        window.recargarInclude();
                    }, 500);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    $.toast({
                        heading: 'Error',
                        text: 'No se pudo actualizar el indicador',
                        position: 'top-right',
                        loaderBg: '#e74c3c',
                        icon: 'error',
                        hideAfter: 3000
                    });
                },
                complete: function() {
                    btnActualizar.html(originalText).prop('disabled', false);
                }
            });
        };
        
        window.eliminarIndicador = function(ipcId, indicadorId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará el indicador y todas sus calificaciones asociadas.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loader
                    const row = document.getElementById('indicador-row-' + ipcId);
                    if (row) {
                        row.style.opacity = '0.5';
                        row.style.pointerEvents = 'none';
                    }

                    // Hacer petición AJAX
                    const url = `indicadores-eliminar.php?idR=${window.btoa(ipcId)}&idIndicador=${window.btoa(indicadorId)}&carga=${encodeURIComponent(CARGA)}&periodo=${encodeURIComponent(PERIODO)}`;
                    
                    fetch(url, {
                        method: 'GET',
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Eliminar el elemento con animación
                        if (row) {
                            row.style.transform = 'translateX(-20px)';
                            row.style.opacity = '0';
                            
                            setTimeout(() => {
                                row.remove();
                                
                                // Verificar si hay más indicadores
                                const remainingRows = document.querySelectorAll('.indicadores-table-modern tbody tr').length;
                                if (remainingRows === 0) {
                                    // Recargar para mostrar empty state
                                    window.recargarInclude();
                                } else {
                                    // Recargar para actualizar estadísticas y totales
                                    window.recargarInclude();
                                }
                            }, 300);
                        }
                        
                        $.toast({
                            heading: '¡Eliminado!',
                            text: 'El indicador ha sido eliminado correctamente',
                            position: 'top-right',
                            loaderBg: '#e74c3c',
                            icon: 'success',
                            hideAfter: 3000
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (row) {
                            row.style.opacity = '1';
                            row.style.pointerEvents = 'auto';
                        }
                        
                        $.toast({
                            heading: 'Error',
                            text: 'No se pudo eliminar el indicador',
                            position: 'top-right',
                            loaderBg: '#e74c3c',
                            icon: 'error',
                            hideAfter: 3000
                        });
                    });
                }
            });
        };
        
        // ============================================
        // SISTEMA DE TABS MODERNO
        // ============================================
        
        let currentTab = 'indicadores';
        let indicadoresLoaded = false;
        let notasLoaded = false;

        // Cambiar de Tab
        function switchTab(tabName) {
            currentTab = tabName;
            
            // Actualizar tabs activos
            document.querySelectorAll('.modern-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById('tab-' + tabName).classList.add('active');
            
            // Actualizar contenido activo
            document.querySelectorAll('.tab-pane-modern').forEach(pane => {
                pane.classList.remove('active');
            });
            document.getElementById('content-' + tabName).classList.add('active');
            
            // Cargar contenido si no se ha cargado
            if (tabName === 'indicadores' && !indicadoresLoaded) {
                cargarIndicadores();
            } else if (tabName === 'notas' && !notasLoaded) {
                cargarNotas();
            }
        }

        // Event listeners para tabs
        document.querySelectorAll('.modern-tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const tabName = this.getAttribute('data-tab');
                switchTab(tabName);
            });
        });

        // ============================================
        // CARGAR INDICADORES
        // ============================================
        
        function cargarIndicadores() {
            const skeleton = document.getElementById('skeleton-indicadores');
            const content = document.getElementById('indicadores-content');
            
            // Mostrar skeleton
            skeleton.style.display = 'block';
            content.style.display = 'none';
            
            // Cargar vía AJAX
            $.ajax({
                url: 'listar-indicadores.php',
                type: 'GET',
                success: function(response) {
                    setTimeout(() => {
                        content.innerHTML = response;
                        skeleton.style.display = 'none';
                        content.style.display = 'block';
                        indicadoresLoaded = true;
                        
                        // Inicializar funcionalidades
                        inicializarIndicadores();
                    }, 300);
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar indicadores:', error);
                    content.innerHTML = `
                        <div class="alert-modern alert-warning-modern">
                            <i class="fa fa-exclamation-triangle"></i>
                            <span>Error al cargar los indicadores. Por favor, recarga la página.</span>
                        </div>
                    `;
                    skeleton.style.display = 'none';
                    content.style.display = 'block';
                }
            });
        }

        // ============================================
        // CARGAR NOTAS POR INDICADOR
        // ============================================
        
        function cargarNotas() {
            const skeleton = document.getElementById('skeleton-notas');
            const content = document.getElementById('notas-content');
            
            // Mostrar skeleton
            skeleton.style.display = 'block';
            content.style.display = 'none';
            
            // Cargar vía AJAX
            $.ajax({
                url: 'listar-notas-indicadores.php',
                type: 'GET',
                success: function(response) {
                    setTimeout(() => {
                        content.innerHTML = response;
                        skeleton.style.display = 'none';
                        content.style.display = 'block';
                        notasLoaded = true;
                        
                        // Inicializar popovers si existen
                        if (typeof $('[data-toggle="popover"]').popover === 'function') {
                            $('[data-toggle="popover"]').popover();
                        }
                        
                        // Inicializar tooltips para los encabezados de indicadores
                        if (typeof $('.indicador-header-cell[data-toggle="tooltip"]').tooltip === 'function') {
                            $('.indicador-header-cell[data-toggle="tooltip"]').tooltip({
                                html: true,
                                placement: 'top',
                                container: 'body'
                            });
                        }
                    }, 300);
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar notas:', error);
                    content.innerHTML = `
                        <div class="alert-modern alert-warning-modern">
                            <i class="fa fa-exclamation-triangle"></i>
                            <span>Error al cargar las notas. Por favor, recarga la página.</span>
                        </div>
                    `;
                    skeleton.style.display = 'none';
                    content.style.display = 'block';
                }
            });
        }

        // ============================================
        // INICIALIZAR FUNCIONALIDADES DE INDICADORES
        // ============================================
        
        function inicializarIndicadores() {
            // Manejar z-index de dropdowns en la tabla
            const table = $('.indicadores-table-modern');
            if (!table.length) return;
            
            // Usar delegación de eventos para manejar todos los dropdowns
            table.on('show.bs.dropdown', '.btn-group', function(e) {
                const btnGroup = $(this);
                const row = btnGroup.closest('tr');
                
                // Cerrar todos los otros dropdowns abiertos
                table.find('.btn-group.open').each(function() {
                    if (this !== btnGroup[0]) {
                        $(this).removeClass('open').find('.dropdown-toggle').attr('aria-expanded', 'false');
                        $(this).find('.dropdown-menu').removeClass('show');
                    }
                });
                
                // Asegurar que esta fila tenga el z-index más alto
                table.find('tbody tr').css('z-index', '1');
                row.css('z-index', '1000');
                btnGroup.addClass('open');
            });
            
            table.on('hide.bs.dropdown', '.btn-group', function(e) {
                const btnGroup = $(this);
                const row = btnGroup.closest('tr');
                
                row.css('z-index', '');
                btnGroup.removeClass('open');
            });
            
            // También manejar clicks fuera del dropdown para cerrar
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.btn-group').length) {
                    table.find('.btn-group.open').each(function() {
                        const row = $(this).closest('tr');
                        $(this).removeClass('open');
                        row.css('z-index', '');
                    });
                }
            });
            
            console.log('Indicadores inicializados correctamente');
        }

        // ============================================
        // FUNCIONES GLOBALES (para compatibilidad)
        // ============================================
        
        window.recargarInclude = function() {
            indicadoresLoaded = false;
            cargarIndicadores();
        };

        window.generarIndicadores = function() {
            var cantidad = document.getElementById('maxidicadores').value;
            var asignatura = document.getElementById('asignatura').value;
            var curso = document.getElementById('curso').value;
            
            if (parseInt(cantidad) > 0 && parseInt(cantidad) < 8) {
                var buscar = "regalame una lista de " + cantidad + " indicadores para la asignatura " + asignatura + " del curso " + curso + " en colombia el resultado en formato JSON, con un nodo con nombre indicadores y cada indicador en un nodo con nombre descripcion";
                
                document.getElementById("gifCarga").classList.add('active');
                
                var data = {
                    'metodo': '<?php echo TEXT_TO_TEXT ?>',
                    'valor': buscar
                };
                
                fetch('../openAi/metodos.php', {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json'
                    },
                })
                .then((res) => res.json())
                .catch((error) => console.error('Error:', error))
                .then(function(response) {
                    if (response["ok"]) {
                        var data = {
                            'valor': response["valor"]
                        };
                        fetch('indicadores-guardar-fetch.php', {
                            method: 'POST',
                            body: JSON.stringify(data),
                            headers: {
                                'Content-Type': 'text/html'
                            },
                        }).then(function(response) {
                            window.recargarInclude();
                            document.getElementById("gifCarga").classList.remove('active');
                            
                            $.toast({
                                heading: '¡Éxito!',
                                text: 'Indicadores generados correctamente',
                                position: 'top-right',
                                loaderBg: '#41c1ba',
                                icon: 'success',
                                hideAfter: 3000
                            });
                        });
                    }
                });
            } else {
                Swal.fire({
                    position: "top-end",
                    icon: "warning",
                    title: 'El rango para crear indicadores es de una cantidad entre 1 a 7',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        };

        // ============================================
        // INICIALIZACIÓN
        // ============================================
        
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener tab desde URL
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            
            if (tab == 2) {
                switchTab('notas');
            } else {
                switchTab('indicadores');
            }
        });

        console.log('✨ Sistema de indicadores cargado correctamente');
        console.log('🔧 Funciones globales registradas correctamente');
    </script>
	
</body>

</html>