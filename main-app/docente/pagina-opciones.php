<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0055';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
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
        position: relative;
        overflow: hidden;
    }

    .page-header-modern::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(65, 193, 186, 0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .carga-info {
        position: relative;
        z-index: 2;
    }

    .carga-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .carga-subtitle {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 20px;
    }

    .periodo-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 8px 20px;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid rgba(255,255,255,0.3);
    }

    /* Categorías de Opciones */
    .options-categories {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .category-section {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
    }

    .category-section:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-2px);
    }

    .category-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f8f9fa;
    }

    .category-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }

    .category-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
    }

    .category-subtitle {
        font-size: 13px;
        color: #7f8c8d;
        margin: 0;
    }

    /* Tarjetas de Opciones Modernas */
    .option-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: var(--transition);
        cursor: pointer;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .option-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .option-card:hover {
        background: white;
        border-color: var(--secondary-color);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .option-card:hover::before {
        transform: scaleX(1);
    }

    .option-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .option-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        flex-shrink: 0;
    }

    .option-text {
        flex: 1;
    }

    .option-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .option-description {
        font-size: 13px;
        color: #7f8c8d;
        margin: 0;
    }

    .option-arrow {
        color: #bdc3c7;
        font-size: 16px;
        transition: var(--transition);
    }

    .option-card:hover .option-arrow {
        color: var(--secondary-color);
        transform: translateX(5px);
    }

    /* Colores específicos para cada categoría */
    .category-academic .category-icon {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }

    .category-evaluation .category-icon {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
    }

    .category-activities .category-icon {
        background: linear-gradient(135deg, #f39c12, #e67e22);
    }

    .category-management .category-icon {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
    }

    .category-tools .category-icon {
        background: linear-gradient(135deg, #1abc9c, #16a085);
    }

    /* Colores específicos para opciones */
    .option-indicadores .option-icon { background: linear-gradient(135deg, #27ae60, #2ecc71); }
    .option-calificaciones .option-icon { background: linear-gradient(135deg, #f39c12, #f1c40f); }
    .option-resumen-notas .option-icon { background: linear-gradient(135deg, #3498db, #5dade2); }
    .option-clases .option-icon { background: linear-gradient(135deg, #e91e63, #f06292); }
    .option-evaluaciones .option-icon { background: linear-gradient(135deg, #9c27b0, #ba68c8); }
    .option-tareas .option-icon { background: linear-gradient(135deg, #ff9800, #ffb74d); }
    .option-foros .option-icon { background: linear-gradient(135deg, #4caf50, #81c784); }
    .option-resumen-periodos .option-icon { background: linear-gradient(135deg, #2196f3, #64b5f6); }
    .option-carpetas .option-icon { background: linear-gradient(135deg, #f44336, #ef5350); }
    .option-estudiantes .option-icon { background: linear-gradient(135deg, #3f51b5, #7986cb); }
    .option-cronograma .option-icon { background: linear-gradient(135deg, #ffc107, #ffd54f); }
    .option-importar .option-icon { background: linear-gradient(135deg, #00bcd4, #4dd0e1); }

    /* Estadísticas Rápidas */
    .quick-stats {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: var(--card-shadow);
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }

    .quick-stat {
        text-align: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        transition: var(--transition);
    }

    .quick-stat:hover {
        background: var(--secondary-color);
        color: white;
        transform: translateY(-2px);
    }

    .quick-stat-number {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
    }

    .quick-stat:hover .quick-stat-number {
        color: white;
    }

    .quick-stat-label {
        font-size: 12px;
        color: #7f8c8d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .quick-stat:hover .quick-stat-label {
        color: rgba(255,255,255,0.9);
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

    .category-section {
        animation: fadeInUp 0.5s ease-out backwards;
    }

    .category-section:nth-child(1) { animation-delay: 0.1s; }
    .category-section:nth-child(2) { animation-delay: 0.2s; }
    .category-section:nth-child(3) { animation-delay: 0.3s; }
    .category-section:nth-child(4) { animation-delay: 0.4s; }
    .category-section:nth-child(5) { animation-delay: 0.5s; }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header-modern {
            padding: 20px;
        }

        .carga-title {
            font-size: 22px;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .options-categories {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .category-section {
            padding: 20px;
        }

        .quick-stats {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .option-card {
            padding: 15px;
        }

        .option-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .option-title {
            font-size: 14px;
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

    /* Botón de Cambiar Carga destacado */
    .quick-actions button.quick-action-btn {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        color: white;
        border: 2px solid var(--secondary-color) !important;
        font-weight: 700;
        position: relative;
        overflow: hidden;
    }

    .quick-actions button.quick-action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }

    .quick-actions button.quick-action-btn:hover::before {
        left: 100%;
    }

    .quick-actions button.quick-action-btn:hover {
        background: linear-gradient(135deg, var(--accent-color), var(--secondary-color));
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(65, 193, 186, 0.5);
    }

    /* Modal Selector de Cargas */
    .modal-cargas-selector .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }

    .modal-cargas-selector .modal-header {
        background: linear-gradient(135deg, var(--primary-color), #1a252f);
        color: white;
        border-radius: 15px 15px 0 0;
        border-bottom: none;
        padding: 20px 25px;
    }

    .modal-cargas-selector .modal-title {
        font-weight: 700;
        font-size: 18px;
    }

    .modal-cargas-selector .close {
        color: white;
        opacity: 0.8;
        font-size: 24px;
    }

    .modal-cargas-selector .close:hover {
        opacity: 1;
    }

    .modal-cargas-selector .modal-body {
        padding: 25px;
    }

    /* Carga Actual */
    .carga-actual-info {
        margin-bottom: 25px;
    }

    .carga-actual-card {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid var(--secondary-color);
    }

    .carga-actual-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        font-weight: 600;
        color: var(--primary-color);
    }

    .carga-actual-details strong {
        display: block;
        font-size: 16px;
        color: var(--primary-color);
        margin-bottom: 5px;
    }

    .carga-actual-meta {
        font-size: 14px;
        color: #7f8c8d;
    }

    /* Otras Cargas */
    .otras-cargas-section h6 {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f8f9fa;
    }

    .loading-spinner {
        text-align: center;
        padding: 40px 20px;
        color: var(--secondary-color);
    }

    .loading-spinner i {
        font-size: 24px;
        margin-bottom: 10px;
        display: block;
    }

    .carga-item {
        background: white;
        border: 2px solid #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: var(--transition);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .carga-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .carga-item:hover {
        border-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .carga-item:hover::before {
        transform: scaleX(1);
    }

    .carga-item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .carga-item-title {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 16px;
    }

    .carga-item-periodo {
        background: var(--secondary-color);
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }

    .carga-item-details {
        color: #7f8c8d;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .carga-item-stats {
        display: flex;
        gap: 15px;
        font-size: 13px;
    }

    .carga-item-stat {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #7f8c8d;
    }

    .carga-item-stat i {
        color: var(--secondary-color);
    }

    .carga-item-arrow {
        color: #bdc3c7;
        font-size: 16px;
        transition: var(--transition);
    }

    .carga-item:hover .carga-item-arrow {
        color: var(--secondary-color);
        transform: translateX(5px);
    }

    /* Responsive para el modal */
    @media (max-width: 768px) {
        .modal-cargas-selector .modal-dialog {
            margin: 10px;
        }

        .modal-cargas-selector .modal-body {
            padding: 20px;
        }

        .quick-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .quick-action-btn {
            width: 100%;
            justify-content: center;
            margin-bottom: 10px;
        }
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

                    <!-- Header con información de la carga -->
                    <div class="page-header-modern">
                        <div class="carga-info">
                            <div class="carga-title">
                                <i class="fa fa-book"></i>
                                <?= strtoupper($datosCargaActual['mat_nombre']); ?>
                            </div>
                            <div class="carga-subtitle">
                                <i class="fa fa-users mr-2"></i>
                                <?= strtoupper($datosCargaActual['gra_nombre'] . " " . $datosCargaActual['gru_nombre']); ?>
                            </div>
                            <div class="periodo-badge">
                                <i class="fa fa-calendar mr-2"></i>
                                Periodo <?= $periodoConsultaActual; ?>
                            </div>
                        </div>
                        <?php include("../compartido/texto-manual-ayuda.php"); ?>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="quick-stats">
                        <div class="quick-stat">
                            <div class="quick-stat-number"><?= $cantidadEstudiantesParaDocentes; ?></div>
                            <div class="quick-stat-label">Estudiantes</div>
                        </div>
                        <div class="quick-stat">
                            <div class="quick-stat-number"><?= $datosCargaActual['car_periodo']; ?></div>
                            <div class="quick-stat-label">Periodo Actual</div>
                        </div>
                        <div class="quick-stat">
                            <div class="quick-stat-number"><?= $datosCargaActual['gra_periodos'] ?? '4'; ?></div>
                            <div class="quick-stat-label">Total Periodos</div>
                        </div>
                        <div class="quick-stat">
                            <div class="quick-stat-number"><?= $datosCargaActual['mat_valor'] ?? '0'; ?></div>
                            <div class="quick-stat-label">Horas Semanales</div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="quick-actions">
                        <span style="font-weight: 600; color: #7f8c8d;">Acciones rápidas:</span>
                        <button onclick="abrirSelectorCargas()" class="quick-action-btn" style="border: 2px solid #e0e6ed;">
                            <i class="fa fa-exchange-alt"></i> Cambiar Carga
                        </button>
                        <a href="cargas.php" class="quick-action-btn">
                            <i class="fa fa-th-large"></i> Mis Cargas
                        </a>
                        <a href="cargas-general.php" class="quick-action-btn">
                            <i class="fa fa-chart-bar"></i> Vista General
                        </a>
                        <a href="../compartido/planilla-docentes.php?docente=<?= base64_encode($_SESSION["id"]); ?>" 
                           target="_blank" class="quick-action-btn">
                            <i class="fa fa-print"></i> Imprimir Planillas
                        </a>
                    </div>

                    <!-- Modal Selector de Cargas -->
                    <div class="modal fade" id="modalSelectorCargas" tabindex="-1" role="dialog" aria-labelledby="modalSelectorCargasLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content modal-cargas-selector">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalSelectorCargasLabel">
                                        <i class="fa fa-exchange-alt mr-2"></i>Cambiar Carga Académica
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="carga-actual-info">
                                        <div class="carga-actual-card">
                                            <div class="carga-actual-header">
                                                <i class="fa fa-book text-primary"></i>
                                                <span>Carga Actual</span>
                                            </div>
                                            <div class="carga-actual-details">
                                                <strong><?= strtoupper($datosCargaActual['mat_nombre']); ?></strong>
                                                <div class="carga-actual-meta">
                                                    <?= strtoupper($datosCargaActual['gra_nombre'] . " " . $datosCargaActual['gru_nombre']); ?> • Periodo <?= $periodoConsultaActual; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="otras-cargas-section">
                                        <h6><i class="fa fa-list mr-2"></i>Otras Cargas Disponibles</h6>
                                        <div class="cargas-loading" id="cargasLoading">
                                            <div class="loading-spinner">
                                                <i class="fa fa-spinner fa-spin"></i>
                                                <span>Cargando cargas académicas...</span>
                                            </div>
                                        </div>
                                        <div class="cargas-list" id="cargasList" style="display: none;">
                                            <!-- Las cargas se cargarán aquí via AJAX -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        function url(url){
                            location.href = url;
                        }
                    </script>
					
                    <!-- Opciones Organizadas por Categorías -->
                    <div class="options-categories">
                        
                        <!-- Categoría Académica -->
                        <div class="category-section category-academic">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fa fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <h3 class="category-title">Gestión Académica</h3>
                                    <p class="category-subtitle">Indicadores y calificaciones</p>
                                </div>
                            </div>
                            
                            <?php if($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null){?>
                                <div class="option-card option-indicadores" onclick="url('indicadores.php')">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <i class="fa fa-chart-line"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Indicadores/Logros</div>
                                            <div class="option-description">Gestionar indicadores y logros académicos</div>
                                        </div>
                                        <div class="option-arrow">
                                            <i class="fa fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="option-card option-indicadores" onclick="url('../compartido/planilla-definitivas-docentes.php?curso=<?=base64_encode($datosCargaActual['car_curso']);?>&grupo=<?=base64_encode($datosCargaActual['car_grupo']);?>&per=<?=base64_encode($periodoConsultaActual);?>')">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <i class="fa fa-file-text"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Planilla Definitivas</div>
                                            <div class="option-description">Ver planillas definitivas del periodo</div>
                                        </div>
                                        <div class="option-arrow">
                                            <i class="fa fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="option-card option-calificaciones" onclick="url('calificaciones.php')">
                                <div class="option-content">
                                    <div class="option-icon">
                                        <i class="fa fa-check-square-o"></i>
                                    </div>
                                    <div class="option-text">
                                        <div class="option-title">Calificaciones</div>
                                        <div class="option-description">Registrar y gestionar calificaciones</div>
                                    </div>
                                    <div class="option-arrow">
                                        <i class="fa fa-chevron-right"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="option-card option-resumen-notas" onclick="url('calificaciones.php?tab=2')">
                                <div class="option-content">
                                    <div class="option-icon">
                                        <i class="fa fa-bar-chart"></i>
                                    </div>
                                    <div class="option-text">
                                        <div class="option-title">Resumen de Notas</div>
                                        <div class="option-description">Ver resumen estadístico de notas</div>
                                    </div>
                                    <div class="option-arrow">
                                        <i class="fa fa-chevron-right"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="option-card option-resumen-periodos" onclick="url('calificaciones.php?tab=4')">
                                <div class="option-content">
                                    <div class="option-icon">
                                        <i class="fa fa-chart-bar"></i>
                                    </div>
                                    <div class="option-text">
                                        <div class="option-title">Resumen por Periodos</div>
                                        <div class="option-description">Análisis comparativo entre periodos</div>
                                    </div>
                                    <div class="option-arrow">
                                        <i class="fa fa-chevron-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Categoría Actividades -->
                        <div class="category-section category-activities">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fa fa-tasks"></i>
                                </div>
                                <div>
                                    <h3 class="category-title">Actividades</h3>
                                    <p class="category-subtitle">Clases, evaluaciones y tareas</p>
                                </div>
                            </div>

                            <?php if(array_key_exists(11, $arregloModulos)){ ?>
                                <div class="option-card option-clases" onclick="url('clases.php')">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <i class="fa fa-chalkboard-teacher"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Clases</div>
                                            <div class="option-description">Gestionar clases y contenido multimedia</div>
                                        </div>
                                        <div class="option-arrow">
                                            <i class="fa fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if(array_key_exists(12, $arregloModulos)){ ?>
                                <div class="option-card option-evaluaciones" onclick="url('evaluaciones.php')">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <i class="fa fa-laptop"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Evaluaciones Virtuales</div>
                                            <div class="option-description">Crear y gestionar evaluaciones online</div>
                                        </div>
                                        <div class="option-arrow">
                                            <i class="fa fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if(array_key_exists(14, $arregloModulos)){ ?>
                                <div class="option-card option-tareas" onclick="url('actividades.php')">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <i class="fa fa-home"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Tareas en Casa</div>
                                            <div class="option-description">Asignar y revisar tareas para el hogar</div>
                                        </div>
                                        <div class="option-arrow">
                                            <i class="fa fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if(array_key_exists(13, $arregloModulos)){ ?>
                                <div class="option-card option-foros" onclick="url('foros.php')">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <i class="fa fa-comments"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Foros</div>
                                            <div class="option-description">Moderar discusiones y debates</div>
                                        </div>
                                        <div class="option-arrow">
                                            <i class="fa fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Categoría Gestión -->
                        <div class="category-section category-management">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div>
                                    <h3 class="category-title">Gestión</h3>
                                    <p class="category-subtitle">Estudiantes y cronograma</p>
                                </div>
                            </div>

                            <div class="option-card option-estudiantes" onclick="url('estudiantes.php')">
                                <div class="option-content">
                                    <div class="option-icon">
                                        <i class="fa fa-user-graduate"></i>
                                    </div>
                                    <div class="option-text">
                                        <div class="option-title">Estudiantes</div>
                                        <div class="option-description">Gestionar información de estudiantes</div>
                                    </div>
                                    <div class="option-arrow">
                                        <i class="fa fa-chevron-right"></i>
                                    </div>
                                </div>
                            </div>

                            <?php if(array_key_exists(15, $arregloModulos)){ ?>
                                <div class="option-card option-cronograma" onclick="url('cronograma-calendario.php')">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <i class="fa fa-calendar-alt"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Cronograma</div>
                                            <div class="option-description">Planificar actividades y fechas importantes</div>
                                        </div>
                                        <div class="option-arrow">
                                            <i class="fa fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Categoría Herramientas -->
                        <div class="category-section category-tools">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fa fa-tools"></i>
                                </div>
                                <div>
                                    <h3 class="category-title">Herramientas</h3>
                                    <p class="category-subtitle">Utilidades y recursos</p>
                                </div>
                            </div>

                            <?php if(array_key_exists(19, $arregloModulos)){ ?>
                                <div class="option-card option-carpetas" onclick="url('cargas-carpetas.php')">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <i class="fa fa-folder"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Carpetas</div>
                                            <div class="option-description">Organizar documentos y archivos</div>
                                        </div>
                                        <div class="option-arrow">
                                            <i class="fa fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if(array_key_exists(21, $arregloModulos)){ ?>
                                <div class="option-card option-importar" onclick="url('importar-info.php')">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <i class="fa fa-cloud-upload"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Importar Información</div>
                                            <div class="option-description">Cargar datos desde archivos externos</div>
                                        </div>
                                        <div class="option-arrow">
                                            <i class="fa fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
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
    <script src="../../config-general/assets/plugins/sparkline/jquery.sparkline.js" ></script>
	<script src="../../config-general/assets/js/pages/sparkline/sparkline-data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
    <script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
    <!-- material -->
    <script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- chart js -->
    <script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js" ></script>
    <script src="../../config-general/assets/plugins/chart-js/utils.js" ></script>
    <script src="../../config-general/assets/js/pages/chart/chartjs/home-data.js" ></script>
    <!-- summernote -->
    <script src="../../config-general/assets/plugins/summernote/summernote.js" ></script>
    <script src="../../config-general/assets/js/pages/summernote/summernote-data.js" ></script>
    <!-- end js include path -->

<script>
    // ============================================
    // ANIMACIONES Y EFECTOS INTERACTIVOS
    // ============================================
    
    // Animar estadísticas al cargar
    window.addEventListener('load', () => {
        const quickStats = document.querySelectorAll('.quick-stat');
        quickStats.forEach((stat, index) => {
            setTimeout(() => {
                stat.style.opacity = '0';
                stat.style.transform = 'translateY(20px)';
                stat.style.transition = 'all 0.5s ease-out';
                
                setTimeout(() => {
                    stat.style.opacity = '1';
                    stat.style.transform = 'translateY(0)';
                }, 50);
            }, index * 150);
        });
    });
    
    // Efectos hover mejorados en tarjetas de opciones
    document.querySelectorAll('.option-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
            
            // Efecto ripple
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(65, 193, 186, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.width = '20px';
            ripple.style.height = '20px';
            ripple.style.marginLeft = '-10px';
            ripple.style.marginTop = '-10px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // CSS para animación ripple
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Efecto de escritura en el título
    function typeWriter(element, text, speed = 100) {
        let i = 0;
        element.innerHTML = '';
        
        function type() {
            if (i < text.length) {
                element.innerHTML += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }
        
        type();
    }
    
    // Aplicar efecto de escritura al título principal
    window.addEventListener('load', () => {
        const titleElement = document.querySelector('.carga-title');
        if (titleElement) {
            const originalText = titleElement.textContent.trim();
            setTimeout(() => {
                typeWriter(titleElement, originalText, 50);
            }, 500);
        }
    });
    
    // Contador animado para estadísticas
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        
        function updateCounter() {
            start += increment;
            if (start < target) {
                element.textContent = Math.floor(start);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        }
        
        updateCounter();
    }
    
    // Animar contadores cuando son visibles
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const numberElement = entry.target.querySelector('.quick-stat-number');
                const target = parseInt(numberElement.textContent);
                if (!isNaN(target)) {
                    animateCounter(numberElement, target);
                }
                observer.unobserve(entry.target);
            }
        });
    });
    
    document.querySelectorAll('.quick-stat').forEach(stat => {
        observer.observe(stat);
    });
    
    // Efecto parallax sutil en el header
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const header = document.querySelector('.page-header-modern');
        if (header) {
            const rate = scrolled * -0.5;
            header.style.transform = `translateY(${rate}px)`;
        }
    });
    
    // Sonido de click (opcional, solo si el usuario lo permite)
    document.querySelectorAll('.option-card').forEach(card => {
        card.addEventListener('click', function() {
            // Crear efecto visual de click
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    // Atajos de teclado
    document.addEventListener('keydown', (e) => {
        // Ctrl/Cmd + números para acceder rápido a opciones
        if ((e.ctrlKey || e.metaKey) && e.key >= '1' && e.key <= '9') {
            e.preventDefault();
            const optionCards = document.querySelectorAll('.option-card');
            const index = parseInt(e.key) - 1;
            if (optionCards[index]) {
                optionCards[index].click();
            }
        }
        
        // Escape para volver a cargas
        if (e.key === 'Escape') {
            window.location.href = 'cargas.php';
        }
    });
    
    // Mostrar tooltips informativos
    document.querySelectorAll('.option-card').forEach((card, index) => {
        card.setAttribute('title', `Atajo: Ctrl+${index + 1}`);
    });
    
    // ============================================
    // SELECTOR DE CARGAS ACADÉMICAS
    // ============================================
    
    function abrirSelectorCargas() {
        $('#modalSelectorCargas').modal('show');
        cargarOtrasCargas();
    }
    
    function cargarOtrasCargas() {
        const loadingDiv = document.getElementById('cargasLoading');
        const cargasListDiv = document.getElementById('cargasList');
        
        // Mostrar loading
        loadingDiv.style.display = 'block';
        cargasListDiv.style.display = 'none';
        
        // Hacer petición AJAX para obtener otras cargas
        $.ajax({
            url: 'ajax-obtener-cargas.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    mostrarCargas(response.data);
                } else {
                    mostrarError('No se pudieron cargar las cargas académicas');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar cargas:', error);
                mostrarError('Error al cargar las cargas académicas');
            },
            complete: function() {
                loadingDiv.style.display = 'none';
                cargasListDiv.style.display = 'block';
            }
        });
    }
    
    function mostrarCargas(cargas) {
        const cargasListDiv = document.getElementById('cargasList');
        
        if (cargas.length === 0) {
            cargasListDiv.innerHTML = `
                <div class="text-center py-4">
                    <i class="fa fa-info-circle text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-3">No hay otras cargas académicas disponibles</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        cargas.forEach(carga => {
            const esMediaTecnica = carga.gra_tipo == 2 ? '<i class="fa fa-bookmark text-warning mr-1" title="Media técnica"></i>' : '';
            const esDirectorGrupo = carga.car_director_grupo == 1 ? '<i class="fa fa-star text-info mr-1" title="Director de grupo"></i>' : '';
            
            html += `
                <div class="carga-item" onclick="cambiarCarga('${carga.car_id}', '${carga.car_periodo}')">
                    <div class="carga-item-header">
                        <div class="carga-item-title">
                            ${esMediaTecnica}${esDirectorGrupo}${carga.mat_nombre.toUpperCase()}
                        </div>
                        <div class="carga-item-periodo">Periodo ${carga.car_periodo}</div>
                    </div>
                    <div class="carga-item-details">
                        ${carga.gra_nombre.toUpperCase()} ${carga.gru_nombre.toUpperCase()} • ${carga.mat_valor}% • ${carga.car_ih} horas
                    </div>
                    <div class="carga-item-stats">
                        <div class="carga-item-stat">
                            <i class="fa fa-users"></i>
                            <span>${carga.cantidad_estudiantes || 0} estudiantes</span>
                        </div>
                        <div class="carga-item-stat">
                            <i class="fa fa-calendar"></i>
                            <span>${carga.car_periodo}/${carga.gra_periodos || 4} periodos</span>
                        </div>
                    </div>
                    <div class="carga-item-arrow">
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </div>
            `;
        });
        
        cargasListDiv.innerHTML = html;
    }
    
    function mostrarError(mensaje) {
        const cargasListDiv = document.getElementById('cargasList');
        cargasListDiv.innerHTML = `
            <div class="text-center py-4">
                <i class="fa fa-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                <p class="text-muted mt-3">${mensaje}</p>
                <button class="btn btn-primary btn-sm" onclick="cargarOtrasCargas()">
                    <i class="fa fa-refresh mr-1"></i>Reintentar
                </button>
            </div>
        `;
    }
    
    function cambiarCarga(cargaId, periodo) {
        // Mostrar confirmación
        if (confirm('¿Estás seguro de que quieres cambiar a esta carga académica?')) {
            // Crear URL con parámetros
            const url = `pagina-opciones.php?carga=${btoa(cargaId)}&periodo=${btoa(periodo)}&get=${btoa(100)}`;
            
            // Mostrar loading
            const cargasListDiv = document.getElementById('cargasList');
            cargasListDiv.innerHTML = `
                <div class="text-center py-4">
                    <i class="fa fa-spinner fa-spin text-primary" style="font-size: 48px;"></i>
                    <p class="text-muted mt-3">Cambiando carga académica...</p>
                </div>
            `;
            
            // Redirigir
            setTimeout(() => {
                window.location.href = url;
            }, 1000);
        }
    }
    
    // Cerrar modal con Escape
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#modalSelectorCargas').hasClass('show')) {
            $('#modalSelectorCargas').modal('hide');
        }
    });
    
    // Inicializar tooltip del botón flotante
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    
    console.log('✨ Sistema de opciones de carga cargado correctamente');
    console.log('🎯 Total de opciones:', document.querySelectorAll('.option-card').length);
    console.log('⌨️ Atajos disponibles: Ctrl+1-9 para acceso rápido');
    console.log('🔄 Selector de cargas académicas disponible en Acciones Rápidas');
</script>
</body>

</html>