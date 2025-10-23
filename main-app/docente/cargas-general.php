<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0068';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");?>
</head>
<style>
    /* Variables CSS */
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
    }

    /* Header Moderno */
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

    /* Buscador y Filtros */
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
    .carga-general-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        margin-bottom: 20px;
        border: 2px solid transparent;
    }

    .carga-general-card:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-3px);
        border-color: var(--secondary-color);
    }

    .carga-header {
        display: flex;
        justify-content: between;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f8f9fa;
    }

    .carga-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
        line-height: 1.3;
    }

    .carga-subtitle {
        font-size: 14px;
        color: #7f8c8d;
        font-weight: 500;
    }

    .carga-periodo {
        background: linear-gradient(135deg, var(--secondary-color), #16a085);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Métricas Grid */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .metric-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: var(--transition);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
    }

    .metric-card:hover {
        background: white;
        border-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .metric-icon {
        font-size: 24px;
        margin-bottom: 10px;
        color: var(--secondary-color);
    }

    .metric-number {
        font-size: 28px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
    }

    .metric-label {
        font-size: 12px;
        color: #7f8c8d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-link {
        text-decoration: none;
        color: inherit;
        display: block;
        width: 100%;
        height: 100%;
    }

    .metric-link:hover {
        text-decoration: none;
        color: inherit;
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

    /* Animaciones */
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

    .carga-general-card {
        animation: fadeInUp 0.5s ease-out backwards;
    }

    .carga-general-card:nth-child(1) { animation-delay: 0.1s; }
    .carga-general-card:nth-child(2) { animation-delay: 0.15s; }
    .carga-general-card:nth-child(3) { animation-delay: 0.2s; }
    .carga-general-card:nth-child(4) { animation-delay: 0.25s; }
    .carga-general-card:nth-child(5) { animation-delay: 0.3s; }

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

        .filter-chips {
            justify-content: center;
        }

        .carga-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .metrics-grid {
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
        }

        .metric-card {
            padding: 15px;
        }

        .metric-number {
            font-size: 24px;
        }
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
        text-decoration: none;
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
                    <?php include("../../config-general/mensajes-informativos.php"); ?>

                    <?php
                    // Obtener datos de cargas
                    $consulta = CargaAcademica::traerCargasDocentes($config, $_SESSION["id"]);
                    $listaCargas = [];
                    $totalCargas = 0;
                    $totalIndicadores = 0;
                    $totalCalificaciones = 0;
                    $totalEvaluaciones = 0;
                    $totalClases = 0;
                    $totalCronogramas = 0;
                    $totalForos = 0;
                    $totalTareas = 0;
                    
                    while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                        $consultaNumerosCargas = mysqli_query($conexion, "SELECT
                        (SELECT COUNT(ipc_id) FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga='".$resultado['car_id']."' AND ipc_periodo='".$resultado['car_periodo']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
                        (SELECT COUNT(act_id) FROM ".BD_ACADEMICA.".academico_actividades WHERE act_id_carga='".$resultado['car_id']."' AND act_periodo='".$resultado['car_periodo']."' AND act_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
                        (SELECT COUNT(eva_id) FROM ".BD_ACADEMICA.".academico_actividad_evaluaciones WHERE eva_id_carga='".$resultado['car_id']."' AND eva_periodo='".$resultado['car_periodo']."' AND eva_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
                        (SELECT COUNT(cls_id) FROM ".BD_ACADEMICA.".academico_clases WHERE cls_id_carga='".$resultado['car_id']."' AND cls_periodo='".$resultado['car_periodo']."' AND cls_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
                        (SELECT COUNT(cro_id) FROM ".BD_ACADEMICA.".academico_cronograma WHERE cro_id_carga='".$resultado['car_id']."' AND cro_periodo='".$resultado['car_periodo']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
                        (SELECT COUNT(foro_id) FROM ".BD_ACADEMICA.".academico_actividad_foro WHERE foro_id_carga='".$resultado['car_id']."' AND foro_periodo='".$resultado['car_periodo']."' AND foro_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
                        (SELECT COUNT(tar_id) FROM ".BD_ACADEMICA.".academico_actividad_tareas WHERE tar_id_carga='".$resultado['car_id']."' AND tar_periodo='".$resultado['car_periodo']."' AND tar_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]})");
                        $numerosCargas = mysqli_fetch_array($consultaNumerosCargas, MYSQLI_BOTH);
                        
                        $cargaData = [
                            'car_id' => $resultado['car_id'],
                            'car_periodo' => $resultado['car_periodo'],
                            'mat_nombre' => $resultado['mat_nombre'],
                            'gra_nombre' => $resultado['gra_nombre'],
                            'gru_nombre' => $resultado['gru_nombre'],
                            'indicadores' => intval($numerosCargas[0]),
                            'calificaciones' => intval($numerosCargas[1]),
                            'evaluaciones' => intval($numerosCargas[2]),
                            'clases' => intval($numerosCargas[3]),
                            'cronogramas' => intval($numerosCargas[4]),
                            'foros' => intval($numerosCargas[5]),
                            'tareas' => intval($numerosCargas[6])
                        ];
                        
                        $listaCargas[] = $cargaData;
                        $totalCargas++;
                        $totalIndicadores += $cargaData['indicadores'];
                        $totalCalificaciones += $cargaData['calificaciones'];
                        $totalEvaluaciones += $cargaData['evaluaciones'];
                        $totalClases += $cargaData['clases'];
                        $totalCronogramas += $cargaData['cronogramas'];
                        $totalForos += $cargaData['foros'];
                        $totalTareas += $cargaData['tareas'];
                    }
                    ?>

                    <!-- Header con estadísticas -->
                    <div class="page-header-modern">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h2 class="mb-2" style="font-size: 28px; font-weight: 700;">
                                    <i class="fa fa-chart-bar mr-2"></i>
                                    <?= $frases[12][$datosUsuarioActual['uss_idioma']] ?? 'Vista General de Cargas'; ?>
                                </h2>
                                <p class="mb-0" style="opacity: 0.9;">Resumen completo de todas tus cargas académicas y actividades</p>
                            </div>
                        </div>
                        
                        <?php if ($totalCargas > 0) { ?>
                        <div class="stats-container">
                            <div class="stat-card">
                                <div class="stat-number"><?= $totalCargas; ?></div>
                                <div class="stat-label">Cargas Totales</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?= $totalIndicadores; ?></div>
                                <div class="stat-label">Indicadores</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?= $totalCalificaciones; ?></div>
                                <div class="stat-label">Calificaciones</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?= $totalEvaluaciones + $totalClases + $totalCronogramas + $totalForos + $totalTareas; ?></div>
                                <div class="stat-label">Actividades</div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <?php if ($totalCargas > 0) { ?>
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
                            <div class="filter-chip" data-filter="periodo-1">
                                <i class="fa fa-calendar"></i> Periodo 1
                            </div>
                            <div class="filter-chip" data-filter="periodo-2">
                                <i class="fa fa-calendar"></i> Periodo 2
                            </div>
                            <div class="filter-chip" data-filter="periodo-3">
                                <i class="fa fa-calendar"></i> Periodo 3
                            </div>
                            <div class="filter-chip" data-filter="periodo-4">
                                <i class="fa fa-calendar"></i> Periodo 4
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="quick-actions">
                        <span style="font-weight: 600; color: #7f8c8d;">Acciones rápidas:</span>
                        <a href="cargas.php" class="quick-action-btn">
                            <i class="fa fa-th-large"></i> Vista de Tarjetas
                        </a>
                        <a href="../compartido/planilla-docentes.php?docente=<?= base64_encode($_SESSION["id"]); ?>" 
                           target="_blank" class="quick-action-btn">
                            <i class="fa fa-print"></i> Imprimir Planillas
                        </a>
                        <a href="javascript:void(0);"
                           onClick="fetchGeneral('../compartido/progreso-docentes.php?modal=1', 'Progreso de los docentes')"
                           class="quick-action-btn">
                            <i class="fa fa-chart-line"></i> Ver Progreso
                        </a>
                    </div>
                    <?php } ?>

                    <!-- Contenedor de tarjetas -->
                    <div id="cargasContainer">
                        <?php foreach ($listaCargas as $index => $carga) { ?>
                        <div class="carga-general-card" 
                             data-materia="<?= strtolower($carga['mat_nombre']); ?>"
                             data-curso="<?= strtolower($carga['gra_nombre']); ?>"
                             data-grupo="<?= strtolower($carga['gru_nombre']); ?>"
                             data-periodo="<?= $carga['car_periodo']; ?>">
                            
                            <!-- Header de la tarjeta -->
                            <div class="carga-header">
                                <div>
                                    <div class="carga-title">
                                        <i class="fa fa-book mr-2"></i><?= strtoupper($carga['mat_nombre']); ?>
                                    </div>
                                    <div class="carga-subtitle">
                                        <i class="fa fa-users mr-1"></i><?= strtoupper($carga['gra_nombre'] . " " . $carga['gru_nombre']); ?>
                                    </div>
                                </div>
                                <div class="carga-periodo">
                                    <i class="fa fa-calendar mr-1"></i>Periodo <?= $carga['car_periodo']; ?>
                                </div>
                            </div>

                            <!-- Métricas Grid -->
                            <div class="metrics-grid">
                                <!-- Indicadores -->
                                <div class="metric-card">
                                    <a href="indicadores.php?carga=<?=base64_encode($carga['car_id']);?>&periodo=<?=base64_encode($carga['car_periodo']);?>&get=<?=base64_encode(100);?>" 
                                       class="metric-link">
                                        <div class="metric-icon">
                                            <i class="fa fa-chart-line"></i>
                                        </div>
                                        <div class="metric-number"><?= $carga['indicadores']; ?></div>
                                        <div class="metric-label">Indicadores</div>
                                    </a>
                                </div>

                                <!-- Calificaciones -->
                                <div class="metric-card">
                                    <a href="calificaciones.php?carga=<?=base64_encode($carga['car_id']);?>&periodo=<?=base64_encode($carga['car_periodo']);?>&get=<?=base64_encode(100);?>" 
                                       class="metric-link">
                                        <div class="metric-icon">
                                            <i class="fa fa-graduation-cap"></i>
                                        </div>
                                        <div class="metric-number"><?= $carga['calificaciones']; ?></div>
                                        <div class="metric-label">Calificaciones</div>
                                    </a>
                                </div>

                                <?php if(array_key_exists(12, $arregloModulos)){ ?>
                                <!-- Evaluaciones -->
                                <div class="metric-card">
                                    <a href="evaluaciones.php?carga=<?=base64_encode($carga['car_id']);?>&periodo=<?=base64_encode($carga['car_periodo']);?>&get=<?=base64_encode(100);?>" 
                                       class="metric-link">
                                        <div class="metric-icon">
                                            <i class="fa fa-clipboard-check"></i>
                                        </div>
                                        <div class="metric-number"><?= $carga['evaluaciones']; ?></div>
                                        <div class="metric-label">Evaluaciones</div>
                                    </a>
                                </div>
                                <?php } ?>

                                <?php if(array_key_exists(11, $arregloModulos)){ ?>
                                <!-- Clases -->
                                <div class="metric-card">
                                    <a href="clases.php?carga=<?=base64_encode($carga['car_id']);?>&periodo=<?=base64_encode($carga['car_periodo']);?>&get=<?=base64_encode(100);?>" 
                                       class="metric-link">
                                        <div class="metric-icon">
                                            <i class="fa fa-chalkboard-teacher"></i>
                                        </div>
                                        <div class="metric-number"><?= $carga['clases']; ?></div>
                                        <div class="metric-label">Clases</div>
                                    </a>
                                </div>
                                <?php } ?>

                                <?php if(array_key_exists(15, $arregloModulos)){ ?>
                                <!-- Cronograma -->
                                <div class="metric-card">
                                    <a href="cronograma-calendario.php?carga=<?=base64_encode($carga['car_id']);?>&periodo=<?=base64_encode($carga['car_periodo']);?>&get=<?=base64_encode(100);?>" 
                                       class="metric-link">
                                        <div class="metric-icon">
                                            <i class="fa fa-calendar-alt"></i>
                                        </div>
                                        <div class="metric-number"><?= $carga['cronogramas']; ?></div>
                                        <div class="metric-label">Cronograma</div>
                                    </a>
                                </div>
                                <?php } ?>

                                <?php if(array_key_exists(13, $arregloModulos)){ ?>
                                <!-- Foros -->
                                <div class="metric-card">
                                    <a href="foros.php?carga=<?=base64_encode($carga['car_id']);?>&periodo=<?=base64_encode($carga['car_periodo']);?>&get=<?=base64_encode(100);?>" 
                                       class="metric-link">
                                        <div class="metric-icon">
                                            <i class="fa fa-comments"></i>
                                        </div>
                                        <div class="metric-number"><?= $carga['foros']; ?></div>
                                        <div class="metric-label">Foros</div>
                                    </a>
                                </div>
                                <?php } ?>

                                <?php if(array_key_exists(14, $arregloModulos)){ ?>
                                <!-- Tareas -->
                                <div class="metric-card">
                                    <a href="actividades.php?carga=<?=base64_encode($carga['car_id']);?>&periodo=<?=base64_encode($carga['car_periodo']);?>&get=<?=base64_encode(100);?>" 
                                       class="metric-link">
                                        <div class="metric-icon">
                                            <i class="fa fa-tasks"></i>
                                        </div>
                                        <div class="metric-number"><?= $carga['tareas']; ?></div>
                                        <div class="metric-label">Tareas</div>
                                    </a>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
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
    <!-- end js include path -->

<script>
    // ============================================
    // FUNCIONALIDAD DE BÚSQUEDA Y FILTROS
    // ============================================
    
    const searchInput = document.getElementById('searchInput');
    const filterChips = document.querySelectorAll('.filter-chip');
    const cargasContainer = document.getElementById('cargasContainer');
    const emptyState = document.getElementById('emptyState');
    const allCards = document.querySelectorAll('.carga-general-card');
    
    let currentFilter = 'all';
    let currentSearchTerm = '';
    
    // Función de búsqueda
    function searchAndFilter() {
        let visibleCount = 0;
        
        allCards.forEach(card => {
            const materia = card.getAttribute('data-materia') || '';
            const curso = card.getAttribute('data-curso') || '';
            const grupo = card.getAttribute('data-grupo') || '';
            const periodo = card.getAttribute('data-periodo') || '';
            
            // Verificar búsqueda
            const searchText = currentSearchTerm.toLowerCase();
            const matchesSearch = !searchText || 
                materia.includes(searchText) || 
                curso.includes(searchText) || 
                grupo.includes(searchText);
            
            // Verificar filtro
            let matchesFilter = true;
            if (currentFilter.startsWith('periodo-')) {
                const filterPeriodo = currentFilter.split('-')[1];
                matchesFilter = periodo === filterPeriodo;
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
                cargasContainer.style.minHeight = '0';
            } else {
                emptyState.classList.remove('show');
                cargasContainer.style.minHeight = 'auto';
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
            
            // Actualizar filtro activo
            filterChips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            
            currentFilter = filterType;
            searchAndFilter();
        });
    });
    
    // ============================================
    // ANIMACIONES DE ENTRADA
    // ============================================
    
    // Animar métricas al cargar
    window.addEventListener('load', () => {
        const metricCards = document.querySelectorAll('.metric-card');
        metricCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease-out';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
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
    
    // ============================================
    // EFECTOS HOVER MEJORADOS
    // ============================================
    
    // Efecto hover en métricas
    document.querySelectorAll('.metric-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    console.log('✨ Sistema de vista general de cargas cargado correctamente');
    console.log('📊 Total de cargas:', allCards.length);
</script>
</body>

</html>