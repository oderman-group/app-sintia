<?php
include("session.php");
$idPaginaInterna = 'DC0033';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once("../class/UsuariosPadre.php");
require_once("../class/Estudiantes.php");
require_once("../class/Sysjobs.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");

$datosCargaActual = null;

if (!empty($_SESSION["infoCargaActual"])) {
    $datosCargaActual = $_SESSION["infoCargaActual"]['datosCargaActual'];
}
?>
</head>
<style>
    /* Variables CSS */
    :root {
        --primary-color: #2d3e50;
        --secondary-color: #41c1ba;
        --accent-color: #f39c12;
        --danger-color: #e74c3c;
        --success-color: #27ae60;
        --warning-color: #f39c12;
        --info-color: #3498db;
        --light-bg: #f8f9fa;
        --card-shadow: 0 2px 12px rgba(0,0,0,0.08);
        --card-shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Estilos del Header Mejorado */
    .page-header-modern {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a252f 100%);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: var(--card-shadow);
        color: white;
    }

    .stats-container {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .stat-card {
        flex: 1;
        min-width: 200px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.2);
        transition: var(--transition);
    }

    .stat-card:hover {
        background: rgba(255,255,255,0.15);
        transform: translateY(-2px);
    }

    .stat-number {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--secondary-color);
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Buscador y Filtros Modernos */
    .search-filter-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: var(--card-shadow);
    }

    .search-box-wrapper {
        position: relative;
        margin-bottom: 20px;
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

    .filter-chips {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-chip {
        padding: 8px 16px;
        border-radius: 25px;
        border: 2px solid #e0e6ed;
        background: white;
        cursor: pointer;
        transition: var(--transition);
        font-size: 14px;
        font-weight: 500;
    }

    .filter-chip:hover {
        border-color: var(--secondary-color);
        background: rgba(65, 193, 186, 0.1);
    }

    .filter-chip.active {
        background: var(--secondary-color);
        border-color: var(--secondary-color);
        color: white;
    }

    /* Tarjetas de Carga Modernas */
    .carga-card-modern {
        background: white;
        border-radius: 16px;
        padding: 0;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: visible;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 2px solid transparent;
        cursor: grab;
        position: relative;
        z-index: 1;
    }

    .carga-card-modern:active {
        cursor: grabbing;
    }

    .carga-card-modern:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-5px);
        border-color: var(--secondary-color);
        z-index: 10;
    }

    .carga-card-modern.selected {
        border-color: var(--secondary-color);
        background: linear-gradient(to bottom, rgba(65, 193, 186, 0.05) 0%, white 100%);
    }
    
    /* Asegurar que el dropdown se muestre sobre otras tarjetas */
    .carga-card-modern .btn-group.open,
    .carga-card-modern .btn-group.show {
        position: relative;
        z-index: 1000;
    }

    .card-header-modern {
        background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
        padding: 20px;
        color: white;
        position: relative;
        border-radius: 16px 16px 0 0;
        overflow: hidden;
    }

    .card-header-modern::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
    }

    .materia-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
        line-height: 1.3;
        text-decoration: none !important;
        color: white;
        display: block;
        transition: var(--transition);
    }

    .materia-title:hover {
        color: var(--secondary-color);
        transform: translateX(5px);
    }

    .curso-info {
        font-size: 14px;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body-modern {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .badges-container {
        display: flex;
        gap: 8px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .badge-director {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
    }

    .badge-media-tecnica {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
        color: white;
    }

    .badge-periodo {
        background: linear-gradient(135deg, #16a085, #1abc9c);
        color: white;
    }

    .progress-section {
        margin: 15px 0;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #7f8c8d;
    }

    .progress-bar-modern {
        height: 8px;
        background: #ecf0f1;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }

    .progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 1s ease-in-out;
        position: relative;
    }

    .progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .progress-excellent {
        background: linear-gradient(90deg, #27ae60, #2ecc71);
    }

    .progress-good {
        background: linear-gradient(90deg, #f39c12, #f1c40f);
    }

    .progress-warning {
        background: linear-gradient(90deg, #e67e22, #f39c12);
    }

    .progress-danger {
        background: linear-gradient(90deg, #c0392b, #e74c3c);
    }

    .sabanas-section {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin: 15px 0;
        justify-content: center;
    }

    .sabana-link {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: white;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: var(--transition);
    }

    .sabana-link:hover {
        background: var(--secondary-color);
        color: white;
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .card-actions {
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid #ecf0f1;
    }

    .btn-modern {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: var(--transition);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-generate {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
    }

    .btn-generate:hover {
        background: linear-gradient(135deg, #c0392b, #a93226);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
    }

    .alert-modern {
        padding: 15px;
        border-radius: 10px;
        font-size: 13px;
        margin-top: 15px;
        border: none;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        line-height: 1.5;
    }

    .alert-modern i {
        font-size: 18px;
        margin-top: 2px;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(192, 57, 43, 0.05));
        color: #c0392b;
        border-left: 4px solid #e74c3c;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(46, 204, 113, 0.05));
        color: #27ae60;
        border-left: 4px solid #2ecc71;
    }

    .alert-warning {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.1), rgba(241, 196, 15, 0.05));
        color: #d68910;
        border-left: 4px solid #f39c12;
    }

    .alert-warning-select {
        background: linear-gradient(135deg, rgba(243, 196, 15, 0.15), rgba(243, 156, 18, 0.05));
        color: #4f3e0d;
        border-left: 4px solid #f5c426;
    }

    .alert-info {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.05));
        color: #2980b9;
        border-left: 4px solid #3498db;
    }

    /* Acciones Rápidas */
    .quick-actions {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: var(--card-shadow);
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
    }

    .quick-action-btn {
        padding: 10px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 2px solid #e0e6ed;
        color: var(--primary-color);
        background: white;
    }

    .quick-action-btn:hover {
        background: var(--secondary-color);
        color: white;
        border-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(65, 193, 186, 0.3);
    }

    /* Estado Vacío */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
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

    /* Animaciones de entrada */
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

    .carga-card-modern {
        animation: fadeInUp 0.5s ease-out backwards;
    }

    .carga-card-modern:nth-child(1) { animation-delay: 0.1s; }
    .carga-card-modern:nth-child(2) { animation-delay: 0.15s; }
    .carga-card-modern:nth-child(3) { animation-delay: 0.2s; }
    .carga-card-modern:nth-child(4) { animation-delay: 0.25s; }
    .carga-card-modern:nth-child(5) { animation-delay: 0.3s; }
    .carga-card-modern:nth-child(6) { animation-delay: 0.35s; }

    /* Responsive */
    @media (max-width: 1200px) {
        .stat-card {
            min-width: 150px;
        }
    }

    @media (max-width: 768px) {
        .page-header-modern {
            padding: 20px;
        }

        .stat-number {
            font-size: 28px;
        }

        .stats-container {
            gap: 10px;
        }

        .stat-card {
            min-width: calc(50% - 5px);
        }

        .search-filter-container {
            padding: 15px;
        }

        .quick-actions {
            flex-direction: column;
        }

        .quick-action-btn {
            width: 100%;
            justify-content: center;
        }

        .filter-chips {
            justify-content: center;
        }
    }

    /* Overlay de carga */
    #overlayInforme {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    #overlayInforme.active {
        display: flex;
    }

    #loader {
        border: 5px solid #f3f3f3;
        border-top: 5px solid var(--secondary-color);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    #loading-text {
        color: white;
        margin-top: 20px;
        font-size: 18px;
        font-weight: 600;
    }

    /* Dropdown mejorado */
    .dropdown-menu-modern {
        border: none;
        box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        border-radius: 10px;
        padding: 10px;
        margin-top: 5px;
        z-index: 1050 !important;
        position: absolute !important;
    }

    .dropdown-menu-modern li a {
        padding: 10px 15px;
        border-radius: 8px;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333 !important;
        text-decoration: none;
        background: white !important;
    }

    .dropdown-menu-modern li a:hover {
        background: #f8f9fa !important;
        color: #333 !important;
        text-decoration: none;
        opacity: 1 !important;
    }
    
    .dropdown-menu-modern li a:focus {
        color: #333 !important;
        background: #f8f9fa !important;
        opacity: 1 !important;
    }
    
    /* Asegurar que el texto siempre sea visible */
    .dropdown-menu-modern li a {
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    /* Evitar que los tooltips interfieran */
    .dropdown-menu-modern .tooltip {
        display: none !important;
    }
    
    .dropdown-menu-modern:hover .tooltip {
        display: none !important;
    }
    
    /* Asegurar que el btn-group tenga posición relativa */
    .card-actions .btn-group {
        position: relative;
    }
    
    /* Cuando el dropdown está abierto, elevar la tarjeta completa */
    .carga-card-modern:has(.dropdown-menu-modern.show),
    .carga-card-modern:has(.btn-group.open) {
        z-index: 100 !important;
    }
    
    /* Asegurar que los sortable-items no tengan overflow */
    .sortable-item {
        overflow: visible !important;
    }

    /* Posición draggable indicator */
    .position-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        background: rgba(255,255,255,0.2);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
    }

    .elemento-draggable {
        cursor: grab;
    }

    .elemento-draggable:active {
        cursor: grabbing;
        opacity: 0.7;
    }
</style>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>

<div id="overlayInforme">
    <div id="loader"></div>
    <div id="loading-text">Generando informe…</div>
</div>

<div class="page-wrapper">

    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">

        <?php include("../compartido/menu.php"); ?>

        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <?php include("../../config-general/mensajes-informativos.php"); ?>

                <?php
                // Obtener datos de cargas
                $filtro      = " AND car_docente = '" . $_SESSION['id'] . "'";
                $order       = "CAST(car_posicion_docente AS SIGNED),car_curso, car_grupo, am.mat_nombre";
                $cCargas     = CargaAcademica::listarCargas($conexion, $config, "", $filtro, $order);
                $contReg     = 1;
                $index       = 0;
                $listaCargas = [];
                $totalEstudiantes = 0;
                $totalActividades = 0;
                $promedioDeclaradas = 0;
                $promedioRegistradas = 0;
                
                while ($fila = $cCargas->fetch_assoc()) {
                    $listaCargas[$index] = $fila;
                    $totalEstudiantes += intval($fila['cantidad_estudiantes'] ?? 0);
                    $promedioDeclaradas += floatval($fila['actividades'] ?? 0);
                    $promedioRegistradas += floatval($fila['actividades_registradas'] ?? 0);
                    $index++;
                }

                $nCargas = count($listaCargas);
                if ($nCargas > 0) {
                    $promedioDeclaradas = round($promedioDeclaradas / $nCargas, 1);
                    $promedioRegistradas = round($promedioRegistradas / $nCargas, 1);
                }

                $mensajeCargas = new Cargas;
                $mensajeCargas->verificarNumCargas($nCargas);
                ?>

                <!-- Header con estadísticas -->
                <div class="page-header-modern">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-2" style="font-size: 28px; font-weight: 700;">
                                <i class="fa fa-graduation-cap mr-2"></i>
                                <?= $frases[12][$datosUsuarioActual['uss_idioma']] ?? 'Mis Cargas Académicas'; ?>
                            </h2>
                            <p class="mb-0" style="opacity: 0.9;">Gestiona y administra todas tus cargas académicas</p>
                        </div>
                        <?php include("../compartido/texto-manual-ayuda.php"); ?>
                    </div>
                    
                    <?php if ($nCargas > 0) { ?>
                    <div class="stats-container">
                        <div class="stat-card">
                            <div class="stat-number"><?= $nCargas; ?></div>
                            <div class="stat-label">Cargas Activas</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $totalEstudiantes; ?></div>
                            <div class="stat-label">Total Estudiantes</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $promedioDeclaradas; ?>%</div>
                            <div class="stat-label">Actividades Declaradas</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $promedioRegistradas; ?>%</div>
                            <div class="stat-label">Notas Registradas</div>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <?php if ($nCargas > 0) { ?>
                <!-- Buscador y Filtros -->
                <div class="search-filter-container">
                    <div class="search-box-wrapper">
                        <input 
                            type="text" 
                            id="searchInput" 
                            class="search-input-modern" 
                            placeholder="Buscar por materia, curso o grupo..."
                            autocomplete="off"
                        >
                        <i class="fa fa-search search-icon"></i>
                    </div>
                    <div class="filter-chips">
                        <span style="font-weight: 600; color: #7f8c8d; margin-right: 10px;">Filtrar por:</span>
                        <div class="filter-chip active" data-filter="all">
                            <i class="fa fa-list-ul"></i> Todos
                        </div>
                        <div class="filter-chip" data-filter="director">
                            <i class="fa fa-star"></i> Director de Grupo
                        </div>
                        <div class="filter-chip" data-filter="media-tecnica">
                            <i class="fa fa-bookmark"></i> Media Técnica
                        </div>
                        <div class="filter-chip" data-sort="posicion">
                            <i class="fa fa-sort-numeric-asc"></i> Por Posición
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="quick-actions">
                    <span style="font-weight: 600; color: #7f8c8d;">Acciones rápidas:</span>
                    <a href="../compartido/planilla-docentes.php?docente=<?= base64_encode($_SESSION["id"]); ?>"
                        target="_blank" class="quick-action-btn">
                        <i class="fa fa-print"></i> Imprimir Planillas
                    </a>
                    <a href="../compartido/planilla-docentes-notas.php?docente=<?= base64_encode($_SESSION["id"]); ?>"
                        target="_blank" class="quick-action-btn">
                        <i class="fa fa-file-text"></i> Planillas con Notas
                    </a>
                    <a href="cargas-general.php" class="quick-action-btn">
                        <i class="fa fa-th-large"></i> Vista General
                    </a>
                    <a href="javascript:void(0);"
                        onClick="fetchGeneral('../compartido/progreso-docentes.php?modal=1', 'Progreso de los docentes')"
                        class="quick-action-btn">
                        <i class="fa fa-chart-line"></i> Ver Progreso
                    </a>
                </div>
                <?php } ?>
                <!-- Contenedor de tarjetas -->
                <div class="row" id="sortable-container">
                    <?php 
                    $cargasCont = 1;
                    foreach ($listaCargas as $carga) {
                        $ultimoAcceso     = 'Nunca';
                        $seleccionado     = false;

                        if (!empty($carga['car_ultimo_acceso_docente'])) {
                            $ultimoAcceso = $carga['car_ultimo_acceso_docente'];
                        }

                        if (!empty($_COOKIE["carga"]) && $carga['car_id'] == $_COOKIE["carga"]) {
                            $seleccionado = true;
                        }

                        $induccionEntrar = '';
                        $induccionSabanas = '';
                        if ($cargasCont == 1) {
                            $induccionEntrar  = 'data-hint="Haciendo click puedes entrar a administrar esta carga académica."';
                            $induccionSabanas = 'data-hint="Puedes ver las sábanas de cada uno de los periodos pasados."';
                        }

                        $esDirectorGrupo = ($carga['car_director_grupo'] == 1);
                        $esMediaTecnica = ($carga['gra_tipo'] == GRADO_INDIVIDUAL);
                        
                        if ($esMediaTecnica) {
                            $cantidadEstudiantes = $carga['cantidad_estudiantes_mt'];
                        } else {
                            $cantidadEstudiantes = $carga["cantidad_estudiantes"];
                        }

                        // Determinar color de progreso
                        $actividadesDeclaradas = floatval($carga['actividades']);
                        $actividadesRegistradas = floatval($carga['actividades_registradas']);
                        
                        $progressClass = 'progress-danger';
                        if ($actividadesDeclaradas >= 80) $progressClass = 'progress-excellent';
                        elseif ($actividadesDeclaradas >= 60) $progressClass = 'progress-good';
                        elseif ($actividadesDeclaradas >= 40) $progressClass = 'progress-warning';

                        $progressClassRegistradas = 'progress-danger';
                        if ($actividadesRegistradas >= 80) $progressClassRegistradas = 'progress-excellent';
                        elseif ($actividadesRegistradas >= 60) $progressClassRegistradas = 'progress-good';
                        elseif ($actividadesRegistradas >= 40) $progressClassRegistradas = 'progress-warning';

                        $verMsj = false;
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 sortable-item elemento-draggable mb-4"
                        draggable="true" 
                        id="carga-<?= $carga['car_id']; ?>"
                        data-materia="<?= strtolower($carga['mat_nombre']); ?>"
                        data-curso="<?= strtolower($carga['gra_nombre']); ?>"
                        data-grupo="<?= strtolower($carga['gru_nombre']); ?>"
                        data-director="<?= $esDirectorGrupo ? '1' : '0'; ?>"
                        data-media-tecnica="<?= $esMediaTecnica ? '1' : '0'; ?>"
                        data-posicion="<?= $carga['car_posicion_docente']; ?>">
                        
                        <div class="carga-card-modern <?= $seleccionado ? 'selected' : ''; ?>">
                            <!-- Header de la tarjeta -->
                            <div class="card-header-modern">
                                <div class="position-indicator"><?= $carga['car_posicion_docente']; ?></div>
                                <a href="cargas-seleccionar.php?carga=<?= base64_encode($carga['car_id']); ?>&periodo=<?= base64_encode($carga['car_periodo']); ?>"
                                   class="materia-title" <?= $induccionEntrar; ?> title="Click para entrar">
                                    <i class="fa fa-book mr-2"></i><?= strtoupper($carga['mat_nombre']); ?>
                                </a>
                                <div class="curso-info">
                                    <i class="fa fa-users"></i>
                                    <?= strtoupper($carga['gra_nombre'] . " " . $carga['gru_nombre']); ?>
                                    <span style="margin-left: auto;">
                                        <i class="fa fa-user-graduate"></i> <?= $cantidadEstudiantes; ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Body de la tarjeta -->
                            <div class="card-body-modern">
                                <!-- Badges -->
                                <div class="badges-container">
                                    <?php if ($esDirectorGrupo) { ?>
                                    <span class="badge-modern badge-director" data-toggle="tooltip" title="Director de grupo">
                                        <i class="fa fa-star"></i> Director
                                    </span>
                                    <?php } ?>
                                    <?php if ($esMediaTecnica) { ?>
                                    <span class="badge-modern badge-media-tecnica" data-toggle="tooltip" title="Media técnica">
                                        <i class="fa fa-bookmark"></i> M. Técnica
                                    </span>
                                    <?php } ?>
                                    <span class="badge-modern badge-periodo">
                                        <i class="fa fa-calendar"></i> Periodo <?= $carga['car_periodo']; ?>
                                    </span>
                                </div>

                                <!-- Progreso de actividades -->
                                <div class="progress-section">
                                    <div class="progress-label">
                                        <span>Actividades Declaradas</span>
                                        <span><strong><?= $actividadesDeclaradas; ?>%</strong></span>
                                    </div>
                                    <div class="progress-bar-modern">
                                        <div class="progress-fill <?= $progressClass; ?>" 
                                             style="width: <?= $actividadesDeclaradas; ?>%"></div>
                                    </div>
                                </div>

                                <div class="progress-section">
                                    <div class="progress-label">
                                        <span>Notas Registradas</span>
                                        <span><strong><?= $actividadesRegistradas; ?>%</strong></span>
                                    </div>
                                    <div class="progress-bar-modern">
                                        <div class="progress-fill <?= $progressClassRegistradas; ?>" 
                                             style="width: <?= $actividadesRegistradas; ?>%"></div>
                                    </div>
                                </div>

                                <!-- Sábanas de periodos anteriores -->
                                <?php if ($carga["car_periodo"] > 1) { ?>
                                <div class="sabanas-section" <?= $induccionSabanas; ?>>
                                    <small style="width: 100%; text-align: center; color: #7f8c8d; font-weight: 600; margin-bottom: 5px; display: block;">
                                        Sábanas anteriores
                                    </small>
                                    <?php for ($i = 1; $i < $carga["car_periodo"]; $i++) { ?>
                                        <a href="../compartido/reportes-sabanas.php?curso=<?= base64_encode($carga["car_curso"]); ?>&grupo=<?= base64_encode($carga["car_grupo"]); ?>&per=<?= base64_encode($i); ?>"
                                           target="_blank" 
                                           class="sabana-link"
                                           data-toggle="tooltip"
                                           title="Ver sábana periodo <?= $i; ?>">
                                            <?= $i; ?>
                                        </a>
                                    <?php } ?>
                                </div>
                                <?php } ?>

                                                <!-- Sección de generación de informes -->
                                                <div class="card-actions" id="mensajeI<?= $carga['car_id'] ?>">
                                                    <?php
                                                    $generarInforme = false;
                                                    $jobsEncontrado = empty($carga["job_id"]) ? false : true;
                                                    $msj  = "";
                                                    $configGenerarJobs = $config['conf_porcentaje_completo_generar_informe'];
                                                    $numSinNotas = $carga["cantidad_estudiantes_sin_nota"];
                                                    $tipoAlerta = 'alert-danger';
                                                    $calificarFaltantes = false;
                                                    
                                                    if ($actividadesDeclaradas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
                                                        $generarInforme = false;
                                                    } else if ($actividadesRegistradas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
                                                        $generarInforme = false;
                                                    } else if ($carga["car_permiso1"] == 0){
                                                        $generarInforme = false;
                                                        $msj = "Sin permiso para generar.";
                                                        $verMsj = true;
                                                    } else {
                                                        $generarInforme = true;
                                                    }

                                                    if ($jobsEncontrado) { 
                                                        $generarInforme = false;
                                                        $intento = intval($carga["job_intentos"]);
                                                        switch ($carga["job_estado"]) {
                                                            case JOBS_ESTADO_ERROR:
                                                                $msj = $carga["job_mensaje"];
                                                                if ($configGenerarJobs == 1) {                                                              
                                                                   $tipoAlerta = "alert-danger";  
                                                                } else {
                                                                   $tipoAlerta = "alert-info";  
                                                                   $generarInforme = true;
                                                                }                                                                                                                      
                                                                $verMsj = true;
                                                                break;
                        
                                                            case JOBS_ESTADO_PENDIENTE:
                                                                $msj = $carga["job_mensaje"];
                                                                $tipoAlerta = "alert-success";
                                                                if ($intento > 0 && $seleccionado) {
                                                                    $tipoAlerta = "alert-warning-select";  
                                                                    $msj .= "<br><br>(La plataforma ha hecho <b>$intento</b> intentos.)";
                                                                } elseif ($intento > 0) {
                                                                    $tipoAlerta = "alert-warning";  
                                                                    $msj .= "<br><br>(La plataforma ha hecho <b>$intento</b> intentos.)";
                                                                }
                                                                $verMsj = true;
                                                                break;
                        
                                                            case JOBS_ESTADO_PROCESO:
                                                                $msj = "El informe está en proceso.";
                                                                $tipoAlerta = "alert-success";
                                                                $verMsj = true;
                                                                break;
                                                            case JOBS_ESTADO_PROCESADO:
                                                                $msj = "El informe ya fue procesado.";
                                                                $tipoAlerta = "alert-success";
                                                                $verMsj = true;
                                                                break;
                        
                                                            default:
                                                                $generarInforme = true;
                                                                break;
                                                        }
                                                    }
                                                    if ($generarInforme) {
                                                        switch (intval($configGenerarJobs)) {
                                                            case 1:
                                                                if ($actividadesRegistradas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
                                                                    $generarInforme = false;
                                                                    $msj = "Hay $numSinNotas estudiantes sin notas. El informe no se puede generar, coloque las notas a todos los estudiantes para generar el informe.";
                                                                    $tipoAlerta = "alert-danger";
                                                                    $calificarFaltantes = true;
                                                                    $verMsj = true;
                                                                    break;
                                                                }
                                                                break;
                                                        }
                                                    }
                                                    
                                                    if ($generarInforme) { 
                                                        $parametros = '?carga='.base64_encode($carga["car_id"]).
                                                        '&periodo='.base64_encode($carga["car_periodo"]).
                                                        '&grado='.base64_encode($carga["car_curso"]).
                                                        '&grupo='.base64_encode($carga["car_grupo"]).
                                                        '&tipoGrado='.base64_encode($carga["gra_tipo"]).
                                                        '&area='.base64_encode($carga["mat_area"]).
                                                        '&valorAsignatura='.base64_encode($carga["mat_valor"]);
                                                    ?>
                                                        <div class="btn-group" style="width: 100%;">
                                                            <button type="button" class="btn-modern btn-generate" style="border-radius: 10px 0 0 10px;">
                                                                <i class="fa fa-file-pdf"></i> Generar Informe
                                                            </button>
                                                            <button type="button" class="btn-modern btn-generate dropdown-toggle" 
                                                                    data-toggle="dropdown" 
                                                                    style="border-radius: 0 10px 10px 0; width: auto; padding: 12px 15px;">
                                                                <i class="fa fa-angle-down"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-modern" role="menu" style="width: 100%;">
                                                                <li>
                                                                    <a rel="<?=$configGenerarJobs.'-'.$numSinNotas.'-1';?>" 
                                                                       data-toggle="tooltip" 
                                                                       data-placement="right" 
                                                                       title="Lo hará usted manualmente como siempre." 
                                                                       href="javascript:void(0);" 
                                                                       name="../compartido/generar-informe.php<?=$parametros?>" 
                                                                       onclick="mensajeGenerarInforme(this)">
                                                                        <i class="fa fa-hand-pointer"></i> Manualmente
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    <?php } ?>
                                                    
                                                    <?php if ($verMsj) { ?>
                                                        <div class="alert-modern <?= $tipoAlerta ?>">
                                                            <i class="fa <?= $tipoAlerta == 'alert-danger' ? 'fa-exclamation-circle' : ($tipoAlerta == 'alert-success' ? 'fa-check-circle' : 'fa-info-circle'); ?>"></i>
                                                            <div>
                                                                <?php if ($calificarFaltantes) { ?>
                                                                    <a target="_blank" 
                                                                       href="calificaciones-faltantes.php?carga=<?=base64_encode($carga["car_id"])?>&periodo=<?=base64_encode($carga["car_periodo"])?>&get=<?=base64_encode(100)?>"
                                                                       style="color: inherit; text-decoration: underline;">
                                                                        <?=$msj?>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <?=$msj?>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    
                                                    <?php if ($carga["car_periodo"] > $carga["gra_periodos"]) { ?>
                                                        <div class="alert-modern alert-info">
                                                            <i class="fa fa-flag-checkered"></i>
                                                            <div>Carga terminada</div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                        $cargasCont++;
                                    } 
                                    ?>
                                </div>

                                <!-- Estado vacío cuando no hay resultados de búsqueda -->
                                <div class="empty-state" id="emptyState">
                                    <div class="empty-state-icon">
                                        <i class="fa fa-search"></i>
                                    </div>
                                    <div class="empty-state-title">No se encontraron cargas</div>
                                    <div class="empty-state-text">Intenta con otros términos de búsqueda o filtros</div>
                                </div>
            </div>
        </div>
        <!-- end page content -->
        <?php // include("../compartido/panel-configuracion.php"); ?>
    </div>
    <!-- end page container -->
    <?php include("../compartido/footer.php"); ?>
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="../../config-general/assets/plugins/sparkline/jquery.sparkline.js"></script>
<script src="../../config-general/assets/js/pages/sparkline/sparkline-data.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!-- chart js -->
<script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js"></script>
<script src="../../config-general/assets/plugins/chart-js/utils.js"></script>
<script src="../../config-general/assets/js/pages/chart/chartjs/home-data.js"></script>
<!-- summernote -->
<script src="../../config-general/assets/plugins/summernote/summernote.js"></script>
<script src="../../config-general/assets/js/pages/summernote/summernote-data.js"></script>
<!-- end js include path -->

<script>
    // Inicializar tooltips de Bootstrap
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Manejar z-index del dropdown para que siempre se muestre encima
    $(document).on('show.bs.dropdown', '.carga-card-modern .btn-group', function() {
        // Elevar z-index de la tarjeta padre cuando se abre el dropdown
        $(this).closest('.carga-card-modern').css('z-index', '100');
        $(this).closest('.sortable-item').css('z-index', '100');
        
        // Deshabilitar tooltips temporalmente cuando el dropdown está abierto
        $('[data-toggle="tooltip"]').tooltip('disable');
    });

    $(document).on('hide.bs.dropdown', '.carga-card-modern .btn-group', function() {
        // Restaurar z-index cuando se cierra el dropdown
        setTimeout(() => {
            $(this).closest('.carga-card-modern').css('z-index', '');
            $(this).closest('.sortable-item').css('z-index', '');
            
            // Rehabilitar tooltips después de cerrar el dropdown
            $('[data-toggle="tooltip"]').tooltip('enable');
        }, 300); // Pequeño delay para que termine la animación
    });
    
    // También deshabilitar tooltips específicamente en los enlaces del dropdown
    $(document).on('mouseenter', '.dropdown-menu-modern a', function() {
        $(this).tooltip('disable');
    });
    
    $(document).on('mouseleave', '.dropdown-menu-modern a', function() {
        $(this).tooltip('enable');
    });

    // ============================================
    // FUNCIONALIDAD DE BÚSQUEDA Y FILTROS
    // ============================================
    
    const searchInput = document.getElementById('searchInput');
    const filterChips = document.querySelectorAll('.filter-chip');
    const sortableContainer = document.getElementById('sortable-container');
    const emptyState = document.getElementById('emptyState');
    const allCards = document.querySelectorAll('.sortable-item');
    
    let currentFilter = 'all';
    let currentSearchTerm = '';
    
    // Función de búsqueda
    function searchAndFilter() {
        let visibleCount = 0;
        
        allCards.forEach(card => {
            const materia = card.getAttribute('data-materia') || '';
            const curso = card.getAttribute('data-curso') || '';
            const grupo = card.getAttribute('data-grupo') || '';
            const isDirector = card.getAttribute('data-director') === '1';
            const isMediaTecnica = card.getAttribute('data-media-tecnica') === '1';
            
            // Verificar búsqueda
            const searchText = currentSearchTerm.toLowerCase();
            const matchesSearch = !searchText || 
                materia.includes(searchText) || 
                curso.includes(searchText) || 
                grupo.includes(searchText);
            
            // Verificar filtro
            let matchesFilter = true;
            if (currentFilter === 'director') {
                matchesFilter = isDirector;
            } else if (currentFilter === 'media-tecnica') {
                matchesFilter = isMediaTecnica;
            }
            
            // Mostrar/ocultar
            if (matchesSearch && matchesFilter) {
                card.style.display = '';
                card.style.animation = 'fadeInUp 0.5s ease-out';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Mostrar estado vacío si no hay resultados
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.add('show');
                sortableContainer.style.minHeight = '0';
            } else {
                emptyState.classList.remove('show');
                sortableContainer.style.minHeight = 'auto';
            }
        }
    }
    
    // Event listener para búsqueda
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearchTerm = e.target.value;
            searchAndFilter();
        });
    }
    
    // Event listeners para filtros
    filterChips.forEach(chip => {
        chip.addEventListener('click', () => {
            const filterType = chip.getAttribute('data-filter');
            const sortType = chip.getAttribute('data-sort');
            
            if (sortType) {
                // Ordenar por posición
                sortCardsByPosition();
                return;
            }
            
            // Actualizar filtro activo
            filterChips.forEach(c => {
                if (c.getAttribute('data-filter')) {
                    c.classList.remove('active');
                }
            });
            chip.classList.add('active');
            
            currentFilter = filterType;
            searchAndFilter();
        });
    });
    
    // Función para ordenar por posición
    function sortCardsByPosition() {
        const cardsArray = Array.from(allCards);
        cardsArray.sort((a, b) => {
            const posA = parseInt(a.getAttribute('data-posicion')) || 0;
            const posB = parseInt(b.getAttribute('data-posicion')) || 0;
            return posA - posB;
        });
        
        // Reorganizar en el DOM
        cardsArray.forEach(card => {
            sortableContainer.appendChild(card);
        });
        
        // Mostrar notificación
        $.toast({
            heading: 'Ordenamiento aplicado',
            text: 'Las cargas se han ordenado por posición',
            position: 'top-right',
            loaderBg: '#41c1ba',
            icon: 'success',
            hideAfter: 3000,
            stack: 6
        });
    }
    
    // ============================================
    // FUNCIONALIDAD DRAG & DROP
    // ============================================
    
    let draggedItem = null;
    let fromIndex, toIndex;
    let idCarga;
    let target;
    let docente = '<?= $_SESSION["id"]; ?>';
    
    if (sortableContainer) {
        sortableContainer.addEventListener("dragstart", (e) => {
            if (!e.target.classList.contains('sortable-item')) return;
            
            draggedItem = e.target;
            fromIndex = Array.from(sortableContainer.children).indexOf(draggedItem);
            idCarga = e.target.id.split('-')[1];
            target = e.target;
            
            setTimeout(() => {
                e.target.style.opacity = '0.5';
            }, 0);
        });
        
        sortableContainer.addEventListener("dragend", (e) => {
            if (e.target.classList.contains('sortable-item')) {
                e.target.style.opacity = '1';
            }
        });
        
        sortableContainer.addEventListener("dragover", (e) => {
            e.preventDefault();
            const afterElement = getDragAfterElement(sortableContainer, e.clientY);
            if (afterElement == null) {
                sortableContainer.appendChild(draggedItem);
            } else {
                sortableContainer.insertBefore(draggedItem, afterElement);
            }
        });
        
        sortableContainer.addEventListener("drop", (e) => {
            e.preventDefault();
            toIndex = Array.from(sortableContainer.children).indexOf(draggedItem);
            
            if (fromIndex !== toIndex && toIndex > -1) {
                const newPosition = toIndex + 1;
                cambiarPosicion(idCarga, newPosition, docente);
                
                // Actualizar el indicador de posición
                const positionIndicator = draggedItem.querySelector('.position-indicator');
                if (positionIndicator) {
                    positionIndicator.textContent = newPosition;
                    draggedItem.setAttribute('data-posicion', newPosition);
                }
                
                // Mostrar notificación
                $.toast({
                    heading: 'Posición actualizada',
                    text: 'La posición de la carga se ha actualizado correctamente',
                    position: 'top-right',
                    loaderBg: '#41c1ba',
                    icon: 'success',
                    hideAfter: 3000,
                    stack: 6
                });
            }
        });
    }
    
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.sortable-item:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
    
    // Prevenir eventos por defecto
    document.addEventListener("dragover", (e) => {
        e.preventDefault();
    });
    
    // ============================================
    // ANIMACIONES DE PROGRESO
    // ============================================
    
    // Animar barras de progreso al cargar
    window.addEventListener('load', () => {
        const progressBars = document.querySelectorAll('.progress-fill');
        progressBars.forEach((bar, index) => {
            setTimeout(() => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 50);
            }, index * 100);
        });
    });
    
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
            searchAndFilter();
            searchInput.blur();
        }
    });
    
    console.log('✨ Sistema de cargas académicas cargado correctamente');
    console.log('📚 Total de cargas:', allCards.length);
    
    // ============================================
    // GENERACIÓN ASÍNCRONA DE INFORMES
    // ============================================
    window.mensajeGenerarInforme = async function(elemento) {
        const href = elemento.getAttribute('name');
        
        // Extraer parámetros de la URL (puede ser relativa o absoluta)
        let params;
        try {
            // Si la URL tiene parámetros, extraerlos directamente del string
            if (href.includes('?')) {
                const queryString = href.split('?')[1];
                params = new URLSearchParams(queryString);
            } else {
                // Intentar parsear como URL completa
                const url = new URL(href, window.location.origin);
                params = new URLSearchParams(url.search);
            }
        } catch (e) {
            // Si falla, intentar extraer manualmente
            console.warn('Error parseando URL, intentando método alternativo:', e);
            const match = href.match(/\?(.+)$/);
            if (match) {
                params = new URLSearchParams(match[1]);
            } else {
                params = new URLSearchParams();
            }
        }
        
        let carga = params.get('carga');
        let periodo = params.get('periodo');
        let grado = params.get('grado');
        let grupo = params.get('grupo');
        let tipoGrado = params.get('tipoGrado');
        let area = params.get('area');
        let valorAsignatura = params.get('valorAsignatura');
        
        // Validar que todos los parámetros críticos estén presentes
        if (!carga || !periodo || !grado || !grupo || !tipoGrado) {
            console.error('❌ Parámetros faltantes:', {
                carga: carga ? 'OK' : 'FALTA',
                periodo: periodo ? 'OK' : 'FALTA',
                grado: grado ? 'OK' : 'FALTA',
                grupo: grupo ? 'OK' : 'FALTA',
                tipoGrado: tipoGrado ? 'OK' : 'FALTA',
                href: href
            });
            
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                html: `<p style="color: #e74c3c;">Faltan parámetros necesarios para generar el informe.<br>Por favor, intente nuevamente.</p>`,
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#e74c3c'
            });
            return;
        }
        
        console.log('🔵 Iniciando generación asíncrona de informe...');
        console.log('📊 Parámetros:', {
            carga: carga ? 'OK' : 'FALTA',
            periodo: periodo ? 'OK' : 'FALTA',
            grado: grado ? 'OK' : 'FALTA',
            grupo: grupo ? 'OK' : 'FALTA',
            tipoGrado: tipoGrado ? 'OK' : 'FALTA'
        });
        
        Swal.fire({
            title: '⏳ Generando Informe',
            html: '<div style="text-align: center;"><div style="font-size: 48px; margin: 20px 0;"><i class="fa fa-spinner fa-spin" style="color: #3498db;"></i></div><p style="font-size: 16px; color: #7f8c8d; margin-top: 20px;">Procesando estudiantes...<br><strong>Por favor espera</strong></p></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        try {
            // Construir body con todos los parámetros necesarios
            const bodyParams = new URLSearchParams();
            bodyParams.append('carga', carga);
            bodyParams.append('periodo', periodo);
            bodyParams.append('grado', grado);
            bodyParams.append('grupo', grupo);
            bodyParams.append('tipoGrado', tipoGrado);
            if (area) bodyParams.append('area', area);
            if (valorAsignatura) bodyParams.append('valorAsignatura', valorAsignatura);
            
            const response = await fetch('ajax-generar-informe-docente.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: bodyParams.toString()
            });
            
            // Verificar si la respuesta es OK antes de parsear JSON
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Error HTTP ${response.status}: ${errorText || 'Error desconocido del servidor'}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                const { insertados, actualizados, omitidos, errores, sin_cambios, total_procesados, total_omitidos, total_errores, total_sin_cambios, carga_actualizada, nuevo_periodo, carga_id } = data.data;
                
                // Función auxiliar para crear accordions
                function crearAccordion(titulo, items, color, icono) {
                    if (!items || items.length === 0) return '';
                    
                    const id = 'accordion_' + titulo.replace(/\s+/g, '_').toLowerCase();
                    const nombreEstudiantes = items.map(est => {
                        let detalle = '';
                        if (est.nota !== undefined) {
                            detalle = `Nota: <strong style="color: ${color};">${est.nota}</strong>`;
                            if (est.nota_anterior) {
                                detalle += ` <span style="color: #7f8c8d;">(antes: ${est.nota_anterior})</span>`;
                            }
                            if (est.porcentaje !== undefined) {
                                detalle += ` | Porcentaje: <strong>${est.porcentaje}%</strong>`;
                            }
                        } else if (est.porcentaje !== undefined) {
                            detalle = `Porcentaje: <strong style="color: ${color};">${est.porcentaje}%</strong>`;
                        }
                        if (est.caso) {
                            detalle += ` <small style="color: #7f8c8d;">(Caso ${est.caso})</small>`;
                        }
                        if (est.razon) {
                            detalle += `<br><small style="color: #7f8c8d;">Razón: ${est.razon}</small>`;
                        }
                        if (est.error) {
                            detalle += `<br><small style="color: #e74c3c;">${est.error}</small>`;
                        }
                        return `<div style="padding: 8px; border-bottom: 1px solid #ecf0f1; font-size: 13px;">• <strong>${est.nombre}</strong><br>${detalle}</div>`;
                    }).join('');
                    
                    return `
                        <div class="accordion-item-custom" style="margin-bottom: 10px; border: 1px solid #e0e6ed; border-radius: 8px; overflow: hidden;">
                            <div class="accordion-header-custom" onclick="toggleAccordion('${id}')" style="background: ${color}15; padding: 12px 15px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s;">
                                <div>
                                    <strong style="color: ${color}; font-size: 14px;">${icono} ${titulo} (${items.length})</strong>
                                </div>
                                <i class="fa fa-chevron-down" id="icon_${id}" style="color: ${color}; transition: transform 0.3s;"></i>
                            </div>
                            <div id="${id}" class="accordion-content-custom" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out;">
                                <div style="padding: 10px 15px; background: #ffffff; max-height: 250px; overflow-y: auto;">
                                    ${nombreEstudiantes}
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                let htmlResumen = '<div style="text-align: left; padding: 10px;">';
                
                // Resumen general
                htmlResumen += '<div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">';
                htmlResumen += `<div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total de estudiantes procesados:</div>`;
                htmlResumen += `<div style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">${total_procesados || 0}</div>`;
                if (total_omitidos > 0 || total_errores > 0 || total_sin_cambios > 0) {
                    htmlResumen += `<div style="margin-top: 10px; font-size: 12px; opacity: 0.85;">`;
                    if (total_omitidos > 0) htmlResumen += `⏭️ Omitidos: ${total_omitidos} `;
                    if (total_errores > 0) htmlResumen += `❌ Errores: ${total_errores} `;
                    if (total_sin_cambios > 0) htmlResumen += `✓ Sin cambios: ${total_sin_cambios}`;
                    htmlResumen += `</div>`;
                }
                if (carga_actualizada && nuevo_periodo) {
                    htmlResumen += `<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3);">`;
                    htmlResumen += `<div style="font-size: 12px; opacity: 0.9;">✓ Carga actualizada al período ${nuevo_periodo}</div>`;
                    htmlResumen += `</div>`;
                }
                htmlResumen += '</div>';
                
                // Detalles por categoría con accordions
                htmlResumen += '<div style="max-height: 400px; overflow-y: auto; padding-right: 5px;">';
                htmlResumen += crearAccordion('Insertados', insertados, '#27ae60', '✅');
                htmlResumen += crearAccordion('Actualizados', actualizados, '#3498db', '🔄');
                htmlResumen += crearAccordion('Sin cambios', sin_cambios, '#95a5a6', '✓');
                htmlResumen += crearAccordion('Omitidos', omitidos, '#f39c12', '⏭️');
                htmlResumen += crearAccordion('Errores', errores, '#e74c3c', '❌');
                htmlResumen += '</div>';
                
                htmlResumen += '</div>';
                
                // Registrar función para accordions antes de mostrar el modal
                if (typeof window.toggleAccordion === 'undefined') {
                    window.toggleAccordion = function(id) {
                        const content = document.getElementById(id);
                        const icon = document.getElementById('icon_' + id);
                        if (content && icon) {
                            if (content.style.maxHeight === '0px' || !content.style.maxHeight) {
                                content.style.maxHeight = content.scrollHeight + 'px';
                                icon.style.transform = 'rotate(180deg)';
                            } else {
                                content.style.maxHeight = '0px';
                                icon.style.transform = 'rotate(0deg)';
                            }
                        }
                    };
                }
                
                Swal.fire({
                    icon: 'success',
                    title: '✅ Informe Generado Exitosamente',
                    html: htmlResumen,
                    width: '750px',
                    confirmButtonText: 'Cerrar y actualizar',
                    confirmButtonColor: '#3498db',
                    customClass: {
                        popup: 'swal2-popup-modern'
                    },
                    didClose: () => {
                        // Recargar la página para reflejar los cambios en la carga
                        if (carga_actualizada) {
                            window.location.reload();
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && carga_actualizada) {
                        // Ocultar el botón de generar informe para esta carga
                        if (carga_id) {
                            const cargaElement = document.getElementById('carga-' + carga_id);
                            if (cargaElement) {
                                const btnGroup = cargaElement.querySelector('.btn-group');
                                if (btnGroup) {
                                    btnGroup.style.display = 'none';
                                }
                            }
                        }
                        // Recargar página para ver cambios
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    }
                });
            } else {
                throw new Error(data.message || 'Error desconocido');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                html: `<p style="color: #e74c3c;">${error.message || 'Ocurrió un error al generar el informe'}</p>`,
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#e74c3c'
            });
        }
    };
    
    console.log('✅ Sistema de generación asíncrona activado');
</script>
</body>

</html>