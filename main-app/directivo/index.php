<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0004';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery Toast -->
<link href="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.css" rel="stylesheet" type="text/css" />

<style>
/* ========================================
   DASHBOARD DIRECTORES - DISEÑO MODERNO
   ======================================== */

/* Variables CSS para consistencia */
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #17a2b8;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --border-radius: 12px;
    --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

/* Mantener estructura original pero mejorar contenido */
.page-content {
    background: #f8f9fa;
    padding: 20px;
}

/* Header del Dashboard */
.dashboard-header {
    background: white;
    border-radius: var(--border-radius);
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: var(--box-shadow);
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.dashboard-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 8px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.dashboard-subtitle {
    color: #6c757d;
    font-size: 1rem;
    margin-bottom: 0;
}

/* Cards de estadísticas */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--card-color, var(--primary-color));
}

.stat-card.primary::before { background: var(--primary-color); }
.stat-card.success::before { background: var(--success-color); }
.stat-card.warning::before { background: var(--warning-color); }
.stat-card.danger::before { background: var(--danger-color); }
.stat-card.info::before { background: var(--info-color); }

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    margin-bottom: 12px;
    background: var(--card-color, var(--primary-color));
}

.stat-card.primary .stat-icon { background: var(--primary-color); }
.stat-card.success .stat-icon { background: var(--success-color); }
.stat-card.warning .stat-icon { background: var(--warning-color); }
.stat-card.danger .stat-icon { background: var(--danger-color); }
.stat-card.info .stat-icon { background: var(--info-color); }

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 4px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.stat-change {
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.stat-change.positive { color: var(--success-color); }
.stat-change.negative { color: var(--danger-color); }
.stat-change.neutral { color: #6c757d; }

/* Contenedor de acciones rápidas */
.quick-actions-container {
    margin-bottom: 25px;
}

/* Charts container */
.charts-container {
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
    box-shadow: var(--box-shadow);
}

.chart-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-title i {
    color: var(--primary-color);
}

/* Sidebar de acciones rápidas */
.quick-actions {
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
    box-shadow: var(--box-shadow);
}

.quick-actions-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.quick-actions-title i {
    color: var(--success-color);
}

.action-item {
    display: flex;
    align-items: center;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 8px;
    transition: var(--transition);
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.action-item:hover {
    background: var(--light-color);
    transform: translateX(3px);
    text-decoration: none;
    color: inherit;
}

.action-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 14px;
    color: white;
}

.action-text {
    flex: 1;
}

.action-title {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 2px;
    font-size: 0.9rem;
}

.action-description {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Tabla de actividades recientes */
.recent-activities {
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
    box-shadow: var(--box-shadow);
    margin-bottom: 25px;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f1f3f4;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 14px;
    color: white;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 2px;
    font-size: 0.9rem;
}

.activity-description {
    font-size: 0.8rem;
    color: #6c757d;
}

.activity-time {
    font-size: 0.75rem;
    color: #6c757d;
}

/* Responsive */
@media (max-width: 1200px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .page-content {
        padding: 10px;
    }
    
    .dashboard-title {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .stat-card {
        padding: 15px;
    }
    
    .stat-number {
        font-size: 1.8rem;
    }
}

/* Animaciones suaves */
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

.stat-card {
    animation: fadeInUp 0.5s ease forwards;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class="pull-left">
                            <div class="page-title">
                                <i class="fa fa-tachometer-alt"></i>
                                Dashboard Directivo
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Header del Dashboard -->
                <div class="dashboard-header">
                    <h1 class="dashboard-title">
                        <i class="fa fa-tachometer-alt"></i>
                        Dashboard Directivo
                    </h1>
                    <p class="dashboard-subtitle">
                        Bienvenido, <?= $datosUsuarioActual['uss_nombre'] ?> <?= $datosUsuarioActual['uss_apellido1'] ?>
                    </p>
                </div>

                <!-- Estadísticas Principales -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="stat-number" id="totalEstudiantes">-</div>
                        <div class="stat-label">Total Estudiantes</div>
                        <div class="stat-change positive" id="cambioEstudiantes">
                            <i class="fa fa-arrow-up"></i>
                            <span>Cargando...</span>
                        </div>
                    </div>

                    <div class="stat-card success">
                        <div class="stat-icon">
                            <i class="fa fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-number" id="totalDocentes">-</div>
                        <div class="stat-label">Total Docentes</div>
                        <div class="stat-change positive" id="cambioDocentes">
                            <i class="fa fa-arrow-up"></i>
                            <span>Cargando...</span>
                        </div>
                    </div>

                    <div class="stat-card warning">
                        <div class="stat-icon">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <div class="stat-number" id="totalGrados">-</div>
                        <div class="stat-label">Grados Activos</div>
                        <div class="stat-change neutral" id="cambioGrados">
                            <i class="fa fa-minus"></i>
                            <span>Cargando...</span>
                        </div>
                    </div>

                    <div class="stat-card info">
                        <div class="stat-icon">
                            <i class="fa fa-book"></i>
                        </div>
                        <div class="stat-number" id="totalCargas">-</div>
                        <div class="stat-label">Cargas Académicas</div>
                        <div class="stat-change positive" id="cambioCargas">
                            <i class="fa fa-arrow-up"></i>
                            <span>Cargando...</span>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="quick-actions-container">
                    <div class="quick-actions">
                        <h3 class="quick-actions-title">
                            <i class="fa fa-bolt"></i>
                            Acciones Rápidas
                        </h3>
                        
                        <a href="estudiantes.php" class="action-item">
                            <div class="action-icon" style="background: var(--primary-color);">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="action-text">
                                <div class="action-title">Gestionar Estudiantes</div>
                                <div class="action-description">Ver y administrar estudiantes</div>
                            </div>
                        </a>

                        <a href="cargas.php" class="action-item">
                            <div class="action-icon" style="background: var(--success-color);">
                                <i class="fa fa-book"></i>
                            </div>
                            <div class="action-text">
                                <div class="action-title">Cargas Académicas</div>
                                <div class="action-description">Administrar materias y docentes</div>
                            </div>
                        </a>

                        <a href="usuarios.php" class="action-item">
                            <div class="action-icon" style="background: var(--warning-color);">
                                <i class="fa fa-user-cog"></i>
                            </div>
                            <div class="action-text">
                                <div class="action-title">Usuarios del Sistema</div>
                                <div class="action-description">Gestionar usuarios y permisos</div>
                            </div>
                        </a>

                        <a href="usuarios-importar-excel.php" class="action-item">
                            <div class="action-icon" style="background: var(--info-color);">
                                <i class="fa fa-file-excel"></i>
                            </div>
                            <div class="action-text">
                                <div class="action-title">Importar Usuarios</div>
                                <div class="action-description">Importar desde Excel</div>
                            </div>
                        </a>

                        <a href="informes-todos.php" class="action-item">
                            <div class="action-icon" style="background: var(--danger-color);">
                                <i class="fa fa-chart-bar"></i>
                            </div>
                            <div class="action-text">
                                <div class="action-title">Reportes</div>
                                <div class="action-description">Generar reportes académicos</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Actividades Recientes -->
                <div class="recent-activities">
                    <h3 class="chart-title">
                        <i class="fa fa-history"></i>
                        Actividades Recientes
                    </h3>
                    <div id="recentActivities">
                        <!-- Se llenará dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
        <!-- end page content -->
        <?php include("../compartido/panel-configuracion.php");?>
    </div>
    <!-- end page container -->
    <?php include("../compartido/footer.php");?>
</div>

<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- end js include path -->

<script>
// Variables globales
let academicChart = null;

// Inicializar dashboard cuando el DOM esté listo
$(document).ready(function() {
    console.log('Dashboard Directivo - Inicializando...');
    
    // Cargar estadísticas
    cargarEstadisticas();
    
    // Cargar actividades recientes
    cargarActividadesRecientes();
    
    // Actualizar cada 5 minutos
    setInterval(cargarEstadisticas, 300000);
});

// Cargar estadísticas del dashboard
function cargarEstadisticas() {
    console.log('Cargando estadísticas...');
    
    $.ajax({
        url: 'ajax-dashboard-estadisticas.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Actualizar números
                $('#totalEstudiantes').text(response.estudiantes || 0);
                $('#totalDocentes').text(response.docentes || 0);
                $('#totalGrados').text(response.grados || 0);
                $('#totalCargas').text(response.cargas || 0);
                
                // Actualizar cambios
                actualizarCambio('cambioEstudiantes', response.cambioEstudiantes);
                actualizarCambio('cambioDocentes', response.cambioDocentes);
                actualizarCambio('cambioGrados', response.cambioGrados);
                actualizarCambio('cambioCargas', response.cambioCargas);
                
                console.log('Estadísticas cargadas:', response);
            } else {
                console.error('Error cargando estadísticas:', response.message);
                mostrarError('Error al cargar estadísticas');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            mostrarError('Error de conexión al cargar estadísticas');
        }
    });
}

// Actualizar indicador de cambio
function actualizarCambio(elementId, cambio) {
    const element = $('#' + elementId);
    const icon = element.find('i');
    const span = element.find('span');
    
    if(cambio > 0) {
        element.removeClass('negative neutral').addClass('positive');
        icon.removeClass('fa-arrow-down fa-minus').addClass('fa-arrow-up');
        span.text(`+${cambio} este mes`);
    } else if(cambio < 0) {
        element.removeClass('positive neutral').addClass('negative');
        icon.removeClass('fa-arrow-up fa-minus').addClass('fa-arrow-down');
        span.text(`${cambio} este mes`);
    } else {
        element.removeClass('positive negative').addClass('neutral');
        icon.removeClass('fa-arrow-up fa-arrow-down').addClass('fa-minus');
        span.text('Sin cambios');
    }
}


// Cargar actividades recientes
function cargarActividadesRecientes() {
    console.log('Cargando actividades recientes...');
    
    $.ajax({
        url: 'ajax-dashboard-actividades.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta actividades:', response);
            
            if(response.success) {
                console.log('Actividades recibidas:', response.actividades.length);
                mostrarActividades(response.actividades);
            } else {
                console.error('Error cargando actividades:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX actividades:', error);
            console.error('XHR:', xhr);
        }
    });
}

// Mostrar actividades en el DOM
function mostrarActividades(actividades) {
    const container = $('#recentActivities');
    container.empty();
    
    if(actividades.length === 0) {
        container.html('<p class="text-muted text-center">No hay actividades recientes</p>');
        return;
    }
    
    actividades.forEach(actividad => {
        const item = $(`
            <div class="activity-item">
                <div class="activity-icon" style="background: ${actividad.color};">
                    <i class="fa ${actividad.icono}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${actividad.titulo}</div>
                    <div class="activity-description">${actividad.descripcion}</div>
                </div>
                <div class="activity-time">${actividad.tiempo}</div>
            </div>
        `);
        container.append(item);
    });
}

// Mostrar error con toast
function mostrarError(mensaje) {
    $.toast({
        heading: 'Error',
        text: mensaje,
        showHideTransition: 'slide',
        icon: 'error',
        position: 'top-right',
        hideAfter: 5000
    });
}

// Mostrar éxito con toast
function mostrarExito(mensaje) {
    $.toast({
        heading: 'Éxito',
        text: mensaje,
        showHideTransition: 'slide',
        icon: 'success',
        position: 'top-right',
        hideAfter: 3000
    });
}
</script>

</body>
</html>