<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0022';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");?>

<style>
    :root {
        --primary-color: #41c1ba;
        --secondary-color: #2c3e50;
        --success-color: #27ae60;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --info-color: #3498db;
        --light-color: #ecf0f1;
        --dark-color: #2c3e50;
        --card-shadow: 0 4px 20px rgba(0,0,0,0.1);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --border-radius: 12px;
    }

    /* Header Moderno */
    .importar-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a252f 100%);
        border-radius: var(--border-radius);
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        box-shadow: var(--card-shadow);
        position: relative;
        overflow: hidden;
    }

    .importar-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(65, 193, 186, 0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    .importar-header-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .importar-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .importar-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-top: 8px;
        margin-bottom: 0;
    }

    /* Pasos del Proceso */
    .importar-steps {
        background: white;
        border-radius: var(--border-radius);
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: var(--card-shadow);
    }

    .steps-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        position: relative;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
        z-index: 2;
    }

    .step-number {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 10px;
        transition: var(--transition);
        border: 3px solid #e0e0e0;
    }

    .step.active .step-number {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        transform: scale(1.1);
    }

    .step.completed .step-number {
        background: var(--success-color);
        color: white;
        border-color: var(--success-color);
    }

    .step-title {
        font-weight: 600;
        color: #333;
        text-align: center;
        font-size: 0.9rem;
    }

    .step.active .step-title {
        color: var(--primary-color);
    }

    .step.completed .step-title {
        color: var(--success-color);
    }

    .steps-line {
        position: absolute;
        top: 25px;
        left: 0;
        right: 0;
        height: 3px;
        background: #e0e0e0;
        z-index: 1;
    }

    .steps-progress {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        width: 0%;
        transition: width 0.5s ease;
    }

    /* Formulario Moderno */
    .importar-form {
        background: white;
        border-radius: var(--border-radius);
        padding: 30px;
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
    }

    .form-section {
        margin-bottom: 30px;
        padding: 25px;
        border-radius: var(--border-radius);
        background: #f8f9fa;
        border-left: 4px solid var(--primary-color);
    }

    .form-section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group-modern {
        margin-bottom: 25px;
    }

    .form-label-modern {
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 8px;
        display: block;
        font-size: 1rem;
    }

    .form-control-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: var(--transition);
        background: white;
    }

    .form-control-modern:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(65, 193, 186, 0.1);
    }

    /* Checkboxes Modernos */
    .checkbox-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .checkbox-item {
        background: white;
        border-radius: 8px;
        padding: 20px;
        border: 2px solid #e0e0e0;
        transition: var(--transition);
        cursor: pointer;
        position: relative;
    }

    .checkbox-item:hover {
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .checkbox-item.checked {
        border-color: var(--primary-color);
        background: rgba(65, 193, 186, 0.05);
    }

    .checkbox-item input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .checkbox-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .checkbox-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .checkbox-icon.indicadores { background: linear-gradient(135deg, #3498db, #2980b9); }
    .checkbox-icon.calificaciones { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    .checkbox-icon.clases { background: linear-gradient(135deg, #f39c12, #e67e22); }
    .checkbox-icon.actividades { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
    .checkbox-icon.evaluaciones { background: linear-gradient(135deg, #1abc9c, #16a085); }
    .checkbox-icon.cronograma { background: linear-gradient(135deg, #34495e, #2c3e50); }

    .checkbox-text {
        flex: 1;
    }

    .checkbox-title {
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 5px;
        font-size: 1.1rem;
    }

    .checkbox-description {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .checkbox-count {
        background: var(--primary-color);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 5px;
        display: inline-block;
    }

    /* Botones Modernos */
    .btn-modern {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, var(--primary-color), #16a085);
        color: white;
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(65, 193, 186, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-modern {
        background: #6c757d;
        color: white;
    }

    .btn-secondary-modern:hover {
        background: #5a6268;
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    .btn-success-modern {
        background: linear-gradient(135deg, var(--success-color), #229954);
        color: white;
    }

    .btn-success-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Barra de Progreso */
    .progress-container {
        background: white;
        border-radius: var(--border-radius);
        padding: 30px;
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
        display: none;
    }

    .progress-container.active {
        display: block;
        animation: slideInUp 0.5s ease;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .progress-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--secondary-color);
        margin: 0;
    }

    .progress-percentage {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .progress-bar-container {
        background: #e0e0e0;
        border-radius: 10px;
        height: 20px;
        overflow: hidden;
        margin-bottom: 15px;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        border-radius: 10px;
        transition: width 0.3s ease;
        position: relative;
    }

    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }

    .progress-status {
        font-size: 1rem;
        color: #666;
        text-align: center;
    }

    .progress-details {
        margin-top: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .progress-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .progress-item:last-child {
        border-bottom: none;
    }

    .progress-item-name {
        font-weight: 600;
        color: var(--secondary-color);
    }

    .progress-item-status {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .progress-item-status.pending {
        background: #f8f9fa;
        color: #666;
    }

    .progress-item-status.processing {
        background: #fff3cd;
        color: #856404;
    }

    .progress-item-status.completed {
        background: #d4edda;
        color: #155724;
    }

    .progress-item-status.error {
        background: #f8d7da;
        color: #721c24;
    }

    /* Resultados */
    .results-container {
        background: white;
        border-radius: var(--border-radius);
        padding: 30px;
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
        display: none;
    }

    .results-container.active {
        display: block;
        animation: slideInUp 0.5s ease;
    }

    .results-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .results-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
    }

    .results-icon.success {
        background: linear-gradient(135deg, var(--success-color), #229954);
    }

    .results-icon.error {
        background: linear-gradient(135deg, var(--danger-color), #c0392b);
    }

    .results-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 10px;
    }

    .results-subtitle {
        color: #666;
        font-size: 1.1rem;
    }

    .results-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        border-left: 4px solid var(--primary-color);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-weight: 600;
    }

    .results-actions {
        text-align: center;
        margin-top: 30px;
    }

    /* Animaciones */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .importar-header-content {
            flex-direction: column;
            text-align: center;
        }

        .importar-title {
            font-size: 1.5rem;
        }

        .steps-container {
            flex-direction: column;
            gap: 20px;
        }

        .steps-line {
            display: none;
        }

        .checkbox-group {
            grid-template-columns: 1fr;
        }

        .results-stats {
            grid-template-columns: repeat(2, 1fr);
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
                <?php include(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>

                <!-- Header Moderno -->
                <div class="importar-header">
                    <div class="importar-header-content">
                        <div>
                            <h1 class="importar-title">
                                <i class="fa fa-download"></i>
                                <?=$frases[167][$datosUsuarioActual['uss_idioma']];?>
                            </h1>
                            <p class="importar-subtitle">
                                Importa información de otras cargas académicas y períodos de manera rápida y segura
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.9rem; opacity: 0.8;">
                                Carga Actual: <strong><?= strtoupper($datosCargaActual['mat_nombre']); ?></strong><br>
                                Período: <strong><?= $periodoConsultaActual; ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pasos del Proceso -->
                <div class="importar-steps">
                    <div class="steps-container">
                        <div class="steps-line">
                            <div class="steps-progress" id="stepsProgress"></div>
                        </div>
                        <div class="step active" id="step1">
                            <div class="step-number">1</div>
                            <div class="step-title">Seleccionar Origen</div>
                        </div>
                        <div class="step" id="step2">
                            <div class="step-number">2</div>
                            <div class="step-title">Elegir Contenido</div>
                        </div>
                        <div class="step" id="step3">
                            <div class="step-number">3</div>
                            <div class="step-title">Importar</div>
                        </div>
                        <div class="step" id="step4">
                            <div class="step-number">4</div>
                            <div class="step-title">Resultados</div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Importación -->
                <div class="importar-form" id="importForm">
                    <form id="importFormData">
                        <!-- Paso 1: Seleccionar Origen -->
                        <div class="form-section" id="section1">
                            <h3 class="form-section-title">
                                <i class="fa fa-map-marker-alt"></i>
                                Seleccionar Carga y Período de Origen
                            </h3>
                            <p style="color: #666; margin-bottom: 25px;">
                                <?=$frases[376][$datosUsuarioActual['uss_idioma']];?>
                            </p>
                            
                            <div class="form-group-modern">
                                <label class="form-label-modern"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></label>
                                <select class="form-control-modern" name="cargaImportar" id="cargaImportar" required>
                                    <option value="">Seleccione una carga académica</option>
                                    <?php
                                    $consulta = CargaAcademica::traerCargasDocentes($config, $_SESSION["id"]);
                                    while($datos = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                        $infoActual = '';
                                        if($datos['car_id']==$cargaConsultaActual) $infoActual = ' - Actualmente estás en esta carga.';
                                    ?>
                                        <option value="<?=$datos['car_id'];?>"><?=strtoupper($datos['mat_nombre']." (".$datos['gra_nombre']." ".$datos['gru_nombre']).")".$infoActual;?></option>
                                    <?php }?>
                                </select>
                            </div>
                            
                            <div class="form-group-modern">
                                <label class="form-label-modern"><?=$frases[27][$datosUsuarioActual['uss_idioma']];?></label>
                                <select class="form-control-modern" name="periodoImportar" id="periodoImportar" required>
                                    <option value="">Seleccione un período</option>
                                    <?php
                                    $p=1;
                                    while($p<=$datosCargaActual['gra_periodos']){
                                        $infoActual = '';
                                        if($p==$periodoConsultaActual) $infoActual = ' - Actualmente estás en este período.';
                                    ?>
                                        <option value="<?=$p;?>"><?="PERÍODO ".$p."".$infoActual;?></option>
                                    <?php $p++;}?>
                                </select>
                            </div>
                        </div>

                        <!-- Paso 2: Elegir Contenido -->
                        <div class="form-section" id="section2" style="display: none;">
                            <h3 class="form-section-title">
                                <i class="fa fa-check-square"></i>
                                Seleccionar Contenido a Importar
                            </h3>
                            <p style="color: #666; margin-bottom: 25px;">
                                <?=$frases[377][$datosUsuarioActual['uss_idioma']];?>
                            </p>
                            
                            <div class="checkbox-group">
                                <div class="checkbox-item" data-type="indicadores">
                                    <div class="checkbox-content">
                                        <div class="checkbox-icon indicadores">
                                            <i class="fa fa-chart-line"></i>
                                        </div>
                                        <div class="checkbox-text">
                                            <div class="checkbox-title"><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></div>
                                            <div class="checkbox-description">Importar indicadores de evaluación y sus porcentajes</div>
                                            <div class="checkbox-count" id="countIndicadores">0 elementos</div>
                                        </div>
                                    </div>
                                    <input type="checkbox" name="indicadores" value="1">
                                </div>

                                <div class="checkbox-item" data-type="calificaciones">
                                    <div class="checkbox-content">
                                        <div class="checkbox-icon calificaciones">
                                            <i class="fa fa-graduation-cap"></i>
                                        </div>
                                        <div class="checkbox-text">
                                            <div class="checkbox-title"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></div>
                                            <div class="checkbox-description">Importar calificaciones y actividades de evaluación</div>
                                            <div class="checkbox-count" id="countCalificaciones">0 elementos</div>
                                        </div>
                                    </div>
                                    <input type="checkbox" name="calificaciones" value="1">
                                </div>

                                <?php if(array_key_exists(11, $arregloModulos)){?>
                                <div class="checkbox-item" data-type="clases">
                                    <div class="checkbox-content">
                                        <div class="checkbox-icon clases">
                                            <i class="fa fa-chalkboard-teacher"></i>
                                        </div>
                                        <div class="checkbox-text">
                                            <div class="checkbox-title"><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></div>
                                            <div class="checkbox-description">Importar clases y contenido académico</div>
                                            <div class="checkbox-count" id="countClases">0 elementos</div>
                                        </div>
                                    </div>
                                    <input type="checkbox" name="clases" value="1">
                                </div>
                                <?php }?>

                                <div class="checkbox-item" data-type="actividades">
                                    <div class="checkbox-content">
                                        <div class="checkbox-icon actividades">
                                            <i class="fa fa-tasks"></i>
                                        </div>
                                        <div class="checkbox-text">
                                            <div class="checkbox-title">Actividades</div>
                                            <div class="checkbox-description">Importar actividades y tareas asignadas</div>
                                            <div class="checkbox-count" id="countActividades">0 elementos</div>
                                        </div>
                                    </div>
                                    <input type="checkbox" name="actividades" value="1">
                                </div>

                                <div class="checkbox-item" data-type="evaluaciones">
                                    <div class="checkbox-content">
                                        <div class="checkbox-icon evaluaciones">
                                            <i class="fa fa-clipboard-check"></i>
                                        </div>
                                        <div class="checkbox-text">
                                            <div class="checkbox-title">Evaluaciones</div>
                                            <div class="checkbox-description">Importar evaluaciones y exámenes</div>
                                            <div class="checkbox-count" id="countEvaluaciones">0 elementos</div>
                                        </div>
                                    </div>
                                    <input type="checkbox" name="evaluaciones" value="1">
                                </div>

                                <div class="checkbox-item" data-type="cronograma">
                                    <div class="checkbox-content">
                                        <div class="checkbox-icon cronograma">
                                            <i class="fa fa-calendar-alt"></i>
                                        </div>
                                        <div class="checkbox-text">
                                            <div class="checkbox-title">Cronograma</div>
                                            <div class="checkbox-description">Importar cronograma y fechas importantes</div>
                                            <div class="checkbox-count" id="countCronograma">0 elementos</div>
                                        </div>
                                    </div>
                                    <input type="checkbox" name="cronograma" value="1">
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div style="text-align: center; margin-top: 30px;">
                            <button type="button" class="btn-modern btn-secondary-modern" id="btnBack" style="display: none;">
                                <i class="fa fa-arrow-left"></i>
                                Anterior
                            </button>
                            <button type="button" class="btn-modern btn-primary-modern" id="btnNext">
                                Siguiente
                                <i class="fa fa-arrow-right"></i>
                            </button>
                            <button type="button" class="btn-modern btn-success-modern" id="btnImport" style="display: none;">
                                <i class="fa fa-download"></i>
                                Iniciar Importación
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Barra de Progreso -->
                <div class="progress-container" id="progressContainer">
                    <div class="progress-header">
                        <h3 class="progress-title">Importando Información...</h3>
                        <div class="progress-percentage" id="progressPercentage">0%</div>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" id="progressBar"></div>
                    </div>
                    <div class="progress-status" id="progressStatus">Preparando importación...</div>
                    <div class="progress-details" id="progressDetails"></div>
                </div>

                <!-- Resultados -->
                <div class="results-container" id="resultsContainer">
                    <div class="results-header">
                        <div class="results-icon success" id="resultsIcon">
                            <i class="fa fa-check"></i>
                        </div>
                        <h2 class="results-title" id="resultsTitle">¡Importación Completada!</h2>
                        <p class="results-subtitle" id="resultsSubtitle">La información se ha importado exitosamente</p>
                    </div>
                    
                    <div class="results-stats" id="resultsStats"></div>
                    
                    <div class="results-actions">
                        <a href="indicadores.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" class="btn-modern btn-primary-modern">
                            <i class="fa fa-eye"></i>
                            Ver Resultados
                        </a>
                        <button type="button" class="btn-modern btn-secondary-modern" onclick="location.reload()">
                            <i class="fa fa-refresh"></i>
                            Nueva Importación
                        </button>
                    </div>
                </div>

            </div>
            <!-- end page content -->
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>

    <!-- Scripts -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../config-general/assets/plugins/popper/popper.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
    <script src="../../config-general/assets/js/app.js"></script>
    <script src="../../config-general/assets/js/layout.js"></script>
    <script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>

    <script>
        $(document).ready(function() {
            let currentStep = 1;
            let importData = {};
            let progressInterval;

            // Inicializar Select2
            $('.form-control-modern').select2({
                theme: 'bootstrap'
            });

            // Manejar checkboxes
            $('.checkbox-item').click(function() {
                const checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', !checkbox.prop('checked'));
                
                if (checkbox.prop('checked')) {
                    $(this).addClass('checked');
                } else {
                    $(this).removeClass('checked');
                }
            });

            // Botón Siguiente
            $('#btnNext').click(function() {
                if (currentStep === 1) {
                    const carga = $('#cargaImportar').val();
                    const periodo = $('#periodoImportar').val();
                    
                    if (!carga || !periodo) {
                        $.toast({
                            heading: 'Error',
                            text: 'Por favor selecciona una carga y período de origen',
                            icon: 'error',
                            position: 'top-right'
                        });
                        return;
                    }
                    
                    importData.cargaImportar = carga;
                    importData.periodoImportar = periodo;
                    
                    // Cargar conteos
                    loadContentCounts(carga, periodo);
                    
                    showStep(2);
                } else if (currentStep === 2) {
                    const selectedItems = getSelectedItems();
                    if (selectedItems.length === 0) {
                        $.toast({
                            heading: 'Error',
                            text: 'Por favor selecciona al menos un elemento para importar',
                            icon: 'error',
                            position: 'top-right'
                        });
                        return;
                    }
                    
                    importData.selectedItems = selectedItems;
                    showStep(3);
                }
            });

            // Botón Anterior
            $('#btnBack').click(function() {
                if (currentStep > 1) {
                    showStep(currentStep - 1);
                }
            });

            // Botón Importar
            $('#btnImport').click(function() {
                startImport();
            });

            function showStep(step) {
                currentStep = step;
                
                // Actualizar pasos
                $('.step').removeClass('active completed');
                for (let i = 1; i <= 4; i++) {
                    if (i < step) {
                        $('#step' + i).addClass('completed');
                    } else if (i === step) {
                        $('#step' + i).addClass('active');
                    }
                }
                
                // Actualizar progreso de pasos
                const progress = ((step - 1) / 3) * 100;
                $('#stepsProgress').css('width', progress + '%');
                
                // Mostrar/ocultar secciones
                $('.form-section').hide();
                $('#section' + step).show();
                
                // Mostrar/ocultar botones
                $('#btnBack').toggle(step > 1);
                $('#btnNext').toggle(step < 3);
                $('#btnImport').toggle(step === 3);
            }

            function getSelectedItems() {
                const selected = [];
                $('.checkbox-item input[type="checkbox"]:checked').each(function() {
                    selected.push($(this).attr('name'));
                });
                return selected;
            }

            function loadContentCounts(carga, periodo) {
                $.ajax({
                    url: 'ajax-importar-conteos.php',
                    method: 'POST',
                    data: {
                        carga: carga,
                        periodo: periodo
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#countIndicadores').text(response.indicadores + ' elementos');
                            $('#countCalificaciones').text(response.calificaciones + ' elementos');
                            $('#countClases').text(response.clases + ' elementos');
                            $('#countActividades').text(response.actividades + ' elementos');
                            $('#countEvaluaciones').text(response.evaluaciones + ' elementos');
                            $('#countCronograma').text(response.cronograma + ' elementos');
                        }
                    },
                    error: function() {
                        $.toast({
                            heading: 'Error',
                            text: 'Error al cargar los conteos de elementos',
                            icon: 'error',
                            position: 'top-right'
                        });
                    }
                });
            }

            function startImport() {
                // Ocultar formulario y mostrar progreso
                $('#importForm').hide();
                $('#progressContainer').addClass('active');
                
                // Actualizar paso final
                showStep(4);
                
                // Iniciar importación AJAX
                $.ajax({
                    url: 'ajax-importar-proceso.php',
                    method: 'POST',
                    data: {
                        cargaActual: '<?=$cargaConsultaActual;?>',
                        periodoActual: '<?=$periodoConsultaActual;?>',
                        cargaImportar: importData.cargaImportar,
                        periodoImportar: importData.periodoImportar,
                        selectedItems: importData.selectedItems
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showResults(response.data);
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function() {
                        showError('Error en la comunicación con el servidor');
                    }
                });
            }

            function showResults(data) {
                $('#progressContainer').removeClass('active');
                $('#resultsContainer').addClass('active');
                
                // Actualizar estadísticas
                let statsHtml = '';
                if (data.indicadores > 0) {
                    statsHtml += `<div class="stat-card">
                        <div class="stat-number">${data.indicadores}</div>
                        <div class="stat-label">Indicadores</div>
                    </div>`;
                }
                if (data.calificaciones > 0) {
                    statsHtml += `<div class="stat-card">
                        <div class="stat-number">${data.calificaciones}</div>
                        <div class="stat-label">Calificaciones</div>
                    </div>`;
                }
                if (data.clases > 0) {
                    statsHtml += `<div class="stat-card">
                        <div class="stat-number">${data.clases}</div>
                        <div class="stat-label">Clases</div>
                    </div>`;
                }
                if (data.actividades > 0) {
                    statsHtml += `<div class="stat-card">
                        <div class="stat-number">${data.actividades}</div>
                        <div class="stat-label">Actividades</div>
                    </div>`;
                }
                if (data.evaluaciones > 0) {
                    statsHtml += `<div class="stat-card">
                        <div class="stat-number">${data.evaluaciones}</div>
                        <div class="stat-label">Evaluaciones</div>
                    </div>`;
                }
                if (data.cronograma > 0) {
                    statsHtml += `<div class="stat-card">
                        <div class="stat-number">${data.cronograma}</div>
                        <div class="stat-label">Cronograma</div>
                    </div>`;
                }
                
                $('#resultsStats').html(statsHtml);
            }

            function showError(message) {
                $('#progressContainer').removeClass('active');
                $('#resultsContainer').addClass('active');
                
                $('#resultsIcon').removeClass('success').addClass('error').html('<i class="fa fa-times"></i>');
                $('#resultsTitle').text('Error en la Importación');
                $('#resultsSubtitle').text(message);
            }
        });
    </script>
</body>
</html>