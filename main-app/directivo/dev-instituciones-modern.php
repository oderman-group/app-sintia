<?php
include("session.php");

$idPaginaInterna = 'DV0005';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

// Obtener planes para el filtro
$consultaPlanes = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".planes_sintia ORDER BY plns_nombre ASC");

include("../compartido/head.php");
?>

<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.instituciones-modern-container {
    padding: 20px;
    min-height: calc(100vh - 100px);
}

/* Header Section */
.page-header-modern {
    background: var(--primary-gradient);
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.page-header-modern h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header-modern p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}

/* Stats Cards */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.stat-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-icon.total { background: var(--primary-gradient); }
.stat-icon.activas { background: var(--success-gradient); }
.stat-icon.inactivas { background: #6b7280; }
.stat-icon.bloqueadas { background: var(--danger-gradient); }

.stat-number {
    font-size: 32px;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #6b7280;
    font-weight: 600;
}

/* Filters Section */
.filters-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.filters-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.filters-header h4 {
    margin: 0;
    color: #1f2937;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    font-size: 13px;
    font-weight: 600;
    color: #4b5563;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-select, .search-input-modern {
    padding: 10px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.filter-select:focus, .search-input-modern:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 15px;
    color: #9ca3af;
    font-size: 16px;
}

.search-input-modern {
    padding-left: 45px;
    width: 100%;
}

.filter-actions {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}

.btn-filter-reset {
    padding: 10px 20px;
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-filter-reset:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(107, 114, 128, 0.3);
}

/* Institutions Table */
.table-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}

.table-modern {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.table-modern thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.table-modern thead th {
    padding: 15px 12px;
    text-align: left;
    font-weight: 700;
    color: white;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.table-modern tbody tr {
    border-bottom: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background: #f9fafb;
}

.table-modern tbody td {
    padding: 15px 12px;
    vertical-align: middle;
}

.badge-status {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

.badge-activo {
    background: #d1fae5;
    color: #065f46;
}

.badge-inactivo {
    background: #fee2e2;
    color: #991b1b;
}

.badge-bloqueado {
    background: #fef3c7;
    color: #92400e;
}

/* Toggle Switch */
.switch-toggle {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #10b981;
    transition: 0.3s;
    border-radius: 24px;
}

.switch-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}

.switch-toggle input:checked + .switch-slider {
    background-color: #ef4444;
}

.switch-toggle input:checked + .switch-slider:before {
    transform: translateX(26px);
}

/* Actions Dropdown */
.btn-actions {
    padding: 8px 16px;
    background: var(--primary-gradient);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-actions:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.dropdown-menu-modern {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border-radius: 10px;
    padding: 8px;
    margin-top: 5px;
}

.dropdown-menu-modern li a {
    padding: 10px 15px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.2s ease;
    color: #1f2937;
    font-size: 14px;
}

.dropdown-menu-modern li a:hover {
    background: #f3f4f6;
    text-decoration: none;
}

.dropdown-menu-modern li a i {
    width: 20px;
    text-align: center;
}

/* Pagination */
.pagination-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 2px solid #e5e7eb;
}

.pagination-info {
    color: #6b7280;
    font-size: 14px;
}

.pagination-buttons {
    display: flex;
    gap: 8px;
}

.pagination-btn {
    padding: 8px 16px;
    border: 2px solid #e5e7eb;
    background: white;
    color: #4b5563;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.pagination-btn:hover:not(:disabled) {
    border-color: #667eea;
    color: #667eea;
    transform: translateY(-2px);
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-btn.active {
    background: var(--primary-gradient);
    color: white;
    border-color: transparent;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 10000;
}

.loading-overlay.active {
    display: flex;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Toast */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    display: none;
    align-items: center;
    gap: 15px;
    z-index: 10001;
}

.toast-notification.toast-success {
    border-left: 4px solid #10b981;
}

.toast-notification.toast-error {
    border-left: 4px solid #ef4444;
}

.toast-notification i {
    font-size: 24px;
}

.toast-success i { color: #10b981; }
.toast-error i { color: #ef4444; }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #1f2937;
    margin-bottom: 10px;
}

.empty-state p {
    color: #6b7280;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .table-card {
        overflow-x: auto;
    }
    
    .pagination-container {
        flex-direction: column;
        gap: 15px;
    }
}
</style>

</head>
<?php include("../compartido/body.php"); ?>

<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>
    <?php include("../compartido/panel-color.php"); ?>
    
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        
        <div class="page-content-wrapper">
            <div class="page-content instituciones-modern-container">
                
                <!-- Header -->
                <div class="page-header-modern">
                    <h2>
                        <i class="fas fa-building"></i>
                        Gestión de Instituciones
                    </h2>
                    <p>Administra todas las instituciones registradas en la plataforma</p>
                </div>
                
                <!-- Stats -->
                <div class="stats-container" id="statsContainer">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon total">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="stat-number" id="statTotal">0</div>
                        <div class="stat-label">Total Instituciones</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon activas">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="stat-number" id="statActivas">0</div>
                        <div class="stat-label">Activas</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon inactivas">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                        <div class="stat-number" id="statInactivas">0</div>
                        <div class="stat-label">Inactivas</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon bloqueadas">
                                <i class="fas fa-ban"></i>
                            </div>
                        </div>
                        <div class="stat-number" id="statBloqueadas">0</div>
                        <div class="stat-label">Bloqueadas</div>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="filters-card">
                    <div class="filters-header">
                        <h4>
                            <i class="fas fa-filter"></i>
                            Filtros y Búsqueda
                        </h4>
                        <button class="btn-filter-reset" onclick="resetearFiltros()">
                            <i class="fas fa-redo"></i>
                            Resetear
                        </button>
                    </div>
                    
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">Búsqueda</label>
                            <div class="search-input-wrapper">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" 
                                       id="busqueda" 
                                       class="search-input-modern" 
                                       placeholder="Buscar por nombre, ID, contacto...">
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Plan</label>
                            <select id="filtroPlan" class="filter-select">
                                <option value="todos">Todos los planes</option>
                                <?php while ($plan = mysqli_fetch_array($consultaPlanes, MYSQLI_BOTH)) { ?>
                                    <option value="<?= $plan['plns_id']; ?>"><?= $plan['plns_nombre']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Estado</label>
                            <select id="filtroEstado" class="filter-select">
                                <option value="todos">Todos</option>
                                <option value="1">Activos</option>
                                <option value="0">Inactivos</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Bloqueado</label>
                            <select id="filtroBloqueado" class="filter-select">
                                <option value="todos">Todos</option>
                                <option value="0">No bloqueados</option>
                                <option value="1">Bloqueados</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Table -->
                <div class="table-card">
                    <div style="overflow-x: auto;">
                        <table class="table-modern" id="tablaInstituciones">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Bloq</th>
                                    <th>Siglas</th>
                                    <th>Institución</th>
                                    <th>Contacto</th>
                                    <th>Plan</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaBody">
                                <!-- Cargado dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Empty State -->
                    <div class="empty-state" id="emptyState" style="display: none;">
                        <i class="fas fa-inbox"></i>
                        <h3>No se encontraron instituciones</h3>
                        <p>Intenta ajustar los filtros de búsqueda</p>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="pagination-container" id="paginationContainer">
                        <div class="pagination-info" id="paginationInfo"></div>
                        <div class="pagination-buttons" id="paginationButtons"></div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <?php include("../compartido/footer.php"); ?>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<!-- Toast -->
<div class="toast-notification" id="toastNotification"></div>

<!-- Scripts -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>

<script src="../js/instituciones-listado.js"></script>

</body>
</html>

