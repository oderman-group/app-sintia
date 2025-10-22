<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0032';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
$Plataforma = new Plataforma;

Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
?>

<!-- SortableJS for drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<!-- Font Awesome (if not already included) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* ============================================
   ESTILOS GENERALES Y VARIABLES
   ============================================ */
:root {
    --color-primary: #5567ff;
    --color-secondary: #f4516c;
    --color-success: #34bfa3;
    --color-info: #36a3f7;
    --color-warning: #ffb822;
    --color-danger: #f4516c;
    --color-dark: #282a3c;
    --color-light: #f8f9fa;
    --border-radius: 8px;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.08);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
    --shadow-lg: 0 8px 24px rgba(0,0,0,0.15);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ============================================
   HEADER Y TÍTULO
   ============================================ */
.page-visual-title {
    background: linear-gradient(135deg, var(--color-primary) 0%, #667eea 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-md);
}

.page-visual-title h2 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-visual-title p {
    margin: 0.5rem 0 0 0;
    opacity: 0.95;
    font-size: 0.95rem;
}

/* ============================================
   TOOLBAR Y CONTROLES
   ============================================ */
.visual-toolbar {
    background: white;
    padding: 1.25rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
    border: 1px solid #e8e8e8;
}

.view-switcher {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.view-btn {
    padding: 0.625rem 1.25rem;
    border: 2px solid #e0e0e0;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    transition: var(--transition);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.view-btn:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}

.view-btn.active {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: white;
    box-shadow: var(--shadow-sm);
}

.filter-group {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

.filter-select {
    min-width: 200px;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    transition: var(--transition);
}

.filter-select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(85, 103, 255, 0.1);
}

/* ============================================
   VISTA POR DOCENTES
   ============================================ */
.docentes-container {
    display: grid;
    gap: 1.5rem;
}

.docente-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    border: 1px solid #e8e8e8;
    transition: var(--transition);
}

.docente-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.docente-header {
    background: linear-gradient(135deg, #667eea 0%, var(--color-primary) 100%);
    color: white;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    position: relative;
}

.docente-header:hover {
    background: linear-gradient(135deg, #7685eb 0%, #6678ff 100%);
}

.docente-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 600;
    border: 3px solid rgba(255,255,255,0.3);
}

.docente-info {
    flex: 1;
}

.docente-info h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.docente-info p {
    margin: 0.25rem 0 0 0;
    opacity: 0.9;
    font-size: 0.85rem;
}

.collapse-toggle {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    font-size: 1.2rem;
}

.collapse-toggle:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
}

.collapse-toggle i {
    transition: transform 0.3s ease;
}

.collapse-toggle.collapsed i {
    transform: rotate(-180deg);
}

.cargas-list {
    padding: 1.25rem;
    min-height: 100px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.cargas-list.collapsed {
    max-height: 0;
    padding: 0 1.25rem;
    opacity: 0;
}

.cargas-list.empty {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-style: italic;
    background: #f8f9fa;
    border: 2px dashed #ddd;
    border-radius: 6px;
    margin: 0.5rem;
}

/* ============================================
   TARJETAS DE CARGA (DRAGGABLES)
   ============================================ */
.carga-item {
    background: white;
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    cursor: move;
    transition: var(--transition);
    position: relative;
}

.carga-item:last-child {
    margin-bottom: 0;
}

.carga-item:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-sm);
    transform: translateX(4px);
}

.carga-item.sortable-ghost {
    opacity: 0.4;
    background: #f0f4ff;
    border-color: var(--color-primary);
}

.carga-item.sortable-drag {
    opacity: 0.8;
    box-shadow: var(--shadow-lg);
    transform: rotate(2deg);
}

.carga-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 0.75rem;
}

.carga-title {
    font-weight: 600;
    color: var(--color-dark);
    font-size: 1rem;
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.carga-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-periodo {
    background: #e3f2fd;
    color: #1976d2;
}

.badge-ih {
    background: #f3e5f5;
    color: #7b1fa2;
}

.badge-activa {
    background: #e8f5e9;
    color: #388e3c;
}

.badge-inactiva {
    background: #ffebee;
    color: #d32f2f;
}

.carga-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #666;
}

.carga-detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.carga-detail i {
    color: var(--color-primary);
    width: 16px;
}

.drag-handle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #ccc;
    font-size: 1.25rem;
    cursor: grab;
}

.drag-handle:active {
    cursor: grabbing;
}

/* ============================================
   VISTA POR CURSOS
   ============================================ */
.cursos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.curso-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    border: 1px solid #e8e8e8;
    transition: var(--transition);
}

.curso-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.curso-header {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 1.25rem;
}

.curso-header h3 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.curso-stats {
    margin-top: 0.5rem;
    display: flex;
    gap: 1rem;
    font-size: 0.85rem;
    opacity: 0.95;
}

.curso-body {
    padding: 1.25rem;
}

.grupos-container {
    display: grid;
    gap: 1rem;
}

.grupo-section {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 1rem;
    border: 1px solid #e0e0e0;
    transition: var(--transition);
}

.grupo-section:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-sm);
}

.grupo-title {
    font-weight: 600;
    color: var(--color-dark);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    flex-wrap: wrap;
}

.cargas-grupo-list {
    min-height: 60px;
    padding: 0.5rem;
}

.cargas-grupo-list.empty {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-style: italic;
    background: white;
    border: 2px dashed #ddd;
    border-radius: 6px;
    padding: 1.5rem;
    transition: var(--transition);
}

.cargas-grupo-list.empty:hover {
    border-color: var(--color-primary);
    background: #f0f4ff;
}

/* ============================================
   VISTA MATRICIAL
   ============================================ */
.matrix-view {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    border: 1px solid #e8e8e8;
}

.matrix-controls {
    padding: 1.25rem;
    border-bottom: 1px solid #e8e8e8;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
}

.matrix-search {
    flex: 1;
    min-width: 250px;
    max-width: 400px;
}

.matrix-search input {
    width: 100%;
    padding: 0.625rem 1rem 0.625rem 2.5rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: var(--transition);
}

.matrix-search input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(85, 103, 255, 0.1);
}

.matrix-search {
    position: relative;
}

.matrix-search i {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.matrix-pagination {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.matrix-pagination select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    cursor: pointer;
}

.pagination-info {
    color: #666;
    font-size: 0.9rem;
}

.pagination-buttons {
    display: flex;
    gap: 0.25rem;
}

.pagination-btn {
    padding: 0.5rem 0.75rem;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    transition: var(--transition);
}

.pagination-btn:hover:not(:disabled) {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-btn.active {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.matrix-table-wrapper {
    overflow-x: auto;
}

.matrix-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 1000px;
}

.matrix-table thead th {
    background: linear-gradient(135deg, #667eea 0%, var(--color-primary) 100%);
    color: white;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
    border-bottom: 2px solid rgba(255,255,255,0.2);
    cursor: pointer;
    user-select: none;
}

.matrix-table thead th:hover {
    background: linear-gradient(135deg, #7685eb 0%, #6678ff 100%);
}

.matrix-table thead th .sort-icon {
    margin-left: 0.5rem;
    font-size: 0.75rem;
    opacity: 0.6;
}

.matrix-table thead th.sorted-asc .sort-icon::before {
    content: "▲";
    opacity: 1;
}

.matrix-table thead th.sorted-desc .sort-icon::before {
    content: "▼";
    opacity: 1;
}

.matrix-table tbody tr {
    transition: var(--transition);
}

.matrix-table tbody tr:hover {
    background: #f8f9fa;
}

.matrix-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid #e8e8e8;
    vertical-align: middle;
}

.matrix-cell-docente {
    font-weight: 600;
    color: var(--color-dark);
    min-width: 200px;
}

.matrix-cell-curso {
    color: #666;
}

.matrix-cell-asignatura {
    font-weight: 500;
}

.matrix-actions {
    display: flex;
    gap: 0.5rem;
}

.matrix-btn {
    padding: 0.375rem 0.75rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.matrix-btn:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}

.matrix-btn-edit {
    background: var(--color-info);
    color: white;
}

.matrix-btn-delete {
    background: var(--color-danger);
    color: white;
}

/* ============================================
   ESTADÍSTICAS Y CONTADORES
   ============================================ */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    border: 1px solid #e8e8e8;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: var(--transition);
}

.stat-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.blue {
    background: rgba(85, 103, 255, 0.1);
    color: var(--color-primary);
}

.stat-icon.green {
    background: rgba(52, 191, 163, 0.1);
    color: var(--color-success);
}

.stat-icon.orange {
    background: rgba(255, 184, 34, 0.1);
    color: var(--color-warning);
}

.stat-icon.red {
    background: rgba(244, 81, 108, 0.1);
    color: var(--color-danger);
}

.stat-content h4 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--color-dark);
}

.stat-content p {
    margin: 0.25rem 0 0 0;
    color: #666;
    font-size: 0.85rem;
}

/* ============================================
   LOADING Y ESTADOS VACÍOS
   ============================================ */
.loading-container {
    text-align: center;
    padding: 4rem 2rem;
}

.loading-spinner {
    display: inline-block;
    width: 48px;
    height: 48px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--color-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #999;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    margin: 1rem 0 0.5rem 0;
    color: #666;
}

/* ============================================
   MODAL DE CAMBIO DE DOCENTE
   ============================================ */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9998;
    backdrop-filter: blur(4px);
}

.modal-overlay.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, var(--color-primary) 100%);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.3rem;
}

.modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: var(--transition);
}

.modal-close:hover {
    background: rgba(255,255,255,0.2);
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #e8e8e8;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.btn {
    padding: 0.625rem 1.5rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: var(--color-primary);
    color: white;
}

.btn-primary:hover {
    background: #4355e8;
    box-shadow: var(--shadow-sm);
}

.btn-secondary {
    background: #e0e0e0;
    color: #666;
}

.btn-secondary:hover {
    background: #d0d0d0;
}

/* ============================================
   NOTIFICACIONES TOAST
   ============================================ */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 300px;
    animation: toastSlideIn 0.3s ease-out;
}

@keyframes toastSlideIn {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.toast-notification.success {
    border-left: 4px solid var(--color-success);
}

.toast-notification.error {
    border-left: 4px solid var(--color-danger);
}

.toast-notification.info {
    border-left: 4px solid var(--color-info);
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 768px) {
    .page-visual-title h2 {
        font-size: 1.4rem;
    }
    
    .view-switcher {
        flex-direction: column;
    }
    
    .view-btn {
        width: 100%;
        justify-content: center;
    }
    
    .cursos-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-cards {
        grid-template-columns: 1fr;
    }
    
    .filter-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-select {
        width: 100%;
    }
}

/* ============================================
   UTILIDADES
   ============================================ */
.hidden {
    display: none !important;
}

.text-center {
    text-align: center;
}

.mt-3 {
    margin-top: 1.5rem;
}

.mb-3 {
    margin-bottom: 1.5rem;
}

/* ============================================
   BOTÓN FLOTANTE SCROLL TO TOP
   ============================================ */
.scroll-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: var(--color-primary);
    color: white;
    border: none;
    border-radius: 50%;
    box-shadow: var(--shadow-lg);
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    transition: var(--transition);
    z-index: 999;
}

.scroll-to-top:hover {
    background: #4355e8;
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.2);
}

.scroll-to-top.show {
    display: flex;
}

/* ============================================
   BOTONES DE ACCIÓN RÁPIDA EN CARGAS
   ============================================ */
.carga-quick-actions {
    display: flex;
    gap: 0.25rem;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f0f0f0;
}

.quick-action-btn {
    padding: 0.375rem 0.625rem;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.75rem;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.quick-action-btn:hover {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.quick-action-btn i {
    font-size: 0.875rem;
}

/* Buscador en vistas */
.view-search {
    margin-bottom: 1.5rem;
    position: relative;
}

.view-search input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: var(--transition);
}

.view-search input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(85, 103, 255, 0.1);
}

.view-search i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 1.125rem;
}

/* Zonas de auto-scroll durante drag */
.scroll-zone-indicator {
    position: fixed;
    left: 0;
    right: 0;
    height: 100px;
    pointer-events: none;
    display: none;
    z-index: 998;
    transition: opacity 0.3s ease;
}

.scroll-zone-top {
    top: 0;
    background: linear-gradient(to bottom, rgba(85, 103, 255, 0.15), transparent);
    border-bottom: 2px dashed rgba(85, 103, 255, 0.3);
}

.scroll-zone-bottom {
    bottom: 0;
    background: linear-gradient(to top, rgba(85, 103, 255, 0.15), transparent);
    border-top: 2px dashed rgba(85, 103, 255, 0.3);
}

.scroll-zone-indicator.active {
    display: block;
}

.scroll-zone-indicator::after {
    content: '';
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(85, 103, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}

.scroll-zone-top::after {
    top: 30px;
    content: '↑';
    font-size: 24px;
    color: var(--color-primary);
    line-height: 40px;
    text-align: center;
}

.scroll-zone-bottom::after {
    bottom: 30px;
    content: '↓';
    font-size: 24px;
    color: var(--color-primary);
    line-height: 40px;
    text-align: center;
}

/* Indicador de drag activo */
body.dragging-active {
    cursor: grabbing !important;
}

body.dragging-active * {
    cursor: grabbing !important;
}
</style>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>

<div class="page-wrapper">
    <?php include("../compartido/encabezado.php");?>
    <?php include("../compartido/panel-color.php");?>
    
    <div class="page-container">
        <?php include("../compartido/menu.php");?>
        
        <div class="page-content-wrapper">
            <div class="page-content">
                
                <!-- Título de la página -->
                <div class="page-visual-title">
                    <h2>
                        <i class="fa fa-th-large"></i>
                        Gestión Visual de Cargas Académicas
                    </h2>
                    <p>Organiza y gestiona las cargas de docentes mediante drag & drop. Arrastra las tarjetas para cambiar docentes o reorganizar asignaciones.</p>
                </div>

                <!-- Estadísticas -->
                <div class="stats-cards" id="statsCards">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fa fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-content">
                            <h4 id="totalDocentes">0</h4>
                            <p>Docentes con Cargas</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fa fa-book"></i>
                        </div>
                        <div class="stat-content">
                            <h4 id="totalCargas">0</h4>
                            <p>Cargas Activas</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <div class="stat-content">
                            <h4 id="totalCursos">0</h4>
                            <p>Cursos</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i class="fa fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h4 id="periodoActual"><?= $config['conf_periodo'] ?></h4>
                            <p>Periodo Actual</p>
                        </div>
                    </div>
                </div>

                <!-- Toolbar -->
                <div class="visual-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="view-switcher">
                                <button class="view-btn active" data-view="docentes">
                                    <i class="fa fa-user-tie"></i>
                                    Por Docentes
                                </button>
                                <button class="view-btn" data-view="cursos">
                                    <i class="fa fa-school"></i>
                                    Por Cursos
                                </button>
                                <button class="view-btn" data-view="matriz">
                                    <i class="fa fa-table"></i>
                                    Vista Matricial
                                </button>
                                <button class="view-btn" id="btnCollapseAll" style="display: none;" title="Colapsar/Expandir todas las tarjetas">
                                    <i class="fa fa-compress-alt"></i>
                                    Colapsar Todo
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="filter-group" style="justify-content: flex-end;">
                                <select class="filter-select" id="filterPeriodo">
                                    <option value="">Todos los periodos</option>
                                    <?php for($i=1; $i<=$config['conf_periodos_maximos']; $i++){ ?>
                                        <option value="<?=$i;?>" <?= $i == $config['conf_periodo'] ? 'selected' : '' ?>>Periodo <?=$i;?></option>
                                    <?php } ?>
                                </select>
                                <select class="filter-select" id="filterCurso">
                                    <option value="">Todos los cursos</option>
                                    <?php
                                    $grados = Grados::listarGrados(1);
                                    while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
                                    ?>
                                        <option value="<?=$grado['gra_id'];?>"><?=$grado['gra_nombre'];?></option>
                                    <?php } ?>
                                </select>
                                <button class="btn btn-secondary" onclick="resetFilters()">
                                    <i class="fa fa-redo"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading -->
                <div class="loading-container" id="loadingContainer">
                    <div class="loading-spinner"></div>
                    <p class="mt-3">Cargando datos...</p>
                </div>

                <!-- Buscador para Vista por Docentes -->
                <div class="view-search hidden" id="searchDocentes">
                    <i class="fa fa-search"></i>
                    <input type="text" id="inputSearchDocentes" placeholder="Buscar por docente, asignatura, curso o grupo...">
                </div>

                <!-- Vista por Docentes -->
                <div class="docentes-container" id="vistaDocentes"></div>

                <!-- Buscador para Vista por Cursos -->
                <div class="view-search hidden" id="searchCursos">
                    <i class="fa fa-search"></i>
                    <input type="text" id="inputSearchCursos" placeholder="Buscar por curso, grupo, docente o asignatura...">
                </div>

                <!-- Vista por Cursos -->
                <div class="cursos-grid hidden" id="vistaCursos"></div>

                <!-- Vista Matricial -->
                <div class="matrix-view hidden" id="vistaMatriz"></div>

                <!-- Empty State -->
                <div class="empty-state hidden" id="emptyState">
                    <i class="fa fa-inbox"></i>
                    <h3>No se encontraron cargas</h3>
                    <p>No hay cargas académicas que coincidan con los filtros seleccionados.</p>
                </div>

                <!-- Botón Flotante Scroll to Top -->
                <button class="scroll-to-top" id="scrollToTop" title="Volver arriba">
                    <i class="fa fa-arrow-up"></i>
                </button>

                <!-- Indicadores de Zonas de Auto-Scroll -->
                <div class="scroll-zone-indicator scroll-zone-top" id="scrollZoneTop"></div>
                <div class="scroll-zone-indicator scroll-zone-bottom" id="scrollZoneBottom"></div>

            </div>
        </div>
    </div>
    
    <?php include("../compartido/footer.php");?>
</div>

<!-- Modal para cambiar docente -->
<div class="modal-overlay" id="modalCambiarDocente">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa fa-exchange-alt"></i> Cambiar Docente</h3>
            <button class="modal-close" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p style="margin-bottom: 1rem; color: #666;">
                Selecciona el nuevo docente para esta carga:
            </p>
            <div class="form-group">
                <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Docente:</label>
                <select class="form-control filter-select" id="selectNuevoDocente" style="width: 100%;">
                    <option value="">Seleccione un docente...</option>
                    <?php
                    $consultaDocentes = mysqli_query($conexion, "SELECT uss_id, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2 
                        FROM ".BD_GENERAL.".usuarios 
                        WHERE uss_tipo=".TIPO_DOCENTE." AND institucion={$config['conf_id_institucion']} AND year={$_SESSION['bd']}
                        ORDER BY uss_nombre ASC");
                    
                    while ($doc = mysqli_fetch_array($consultaDocentes, MYSQLI_BOTH)) {
                        $nombreCompleto = UsuariosPadre::nombreCompletoDelUsuario($doc);
                    ?>
                        <option value="<?=$doc['uss_id'];?>"><?=$nombreCompleto;?></option>
                    <?php } ?>
                </select>
            </div>
            <input type="hidden" id="cargaIdCambio">
            <input type="hidden" id="docenteOrigenId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal()">
                <i class="fa fa-times"></i> Cancelar
            </button>
            <button class="btn btn-primary" onclick="confirmarCambioDocente()">
                <i class="fa fa-check"></i> Confirmar Cambio
            </button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>

<script>
// ============================================
// VARIABLES GLOBALES
// ============================================
let cargasData = [];
let cargasDataFiltrada = [];
let currentView = 'docentes';
let currentFilters = {
    periodo: '',
    curso: ''
};

// Variables para vista matricial
let matrixPage = 1;
let matrixPerPage = 25;
let matrixSortColumn = null;
let matrixSortDirection = 'asc';
let matrixSearchTerm = '';

// ============================================
// COLAPSAR/EXPANDIR TARJETAS DE DOCENTES
// ============================================
let allCollapsed = false;

function toggleCollapseDocente(event, docenteId) {
    event.stopPropagation(); // Evitar que se propague al header
    
    let cargasList = $('#docente-' + docenteId);
    let toggleBtn = $(event.currentTarget);
    let icon = toggleBtn.find('i');
    
    if (cargasList.hasClass('collapsed')) {
        // Expandir
        cargasList.removeClass('collapsed');
        toggleBtn.removeClass('collapsed');
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    } else {
        // Colapsar
        cargasList.addClass('collapsed');
        toggleBtn.addClass('collapsed');
        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    }
}

function inicializarCollapse() {
    // Mostrar/ocultar botón de colapsar todo según la vista
    if (currentView === 'docentes') {
        $('#btnCollapseAll').show();
    } else {
        $('#btnCollapseAll').hide();
    }
}

function colapsarExpandirTodas() {
    let btn = $('#btnCollapseAll');
    let icon = btn.find('i');
    
    if (allCollapsed) {
        // Expandir todas
        $('.cargas-list').removeClass('collapsed');
        $('.collapse-toggle').removeClass('collapsed');
        $('.collapse-toggle i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        icon.removeClass('fa-expand-alt').addClass('fa-compress-alt');
        btn.html('<i class="fa fa-compress-alt"></i> Colapsar Todo');
        allCollapsed = false;
    } else {
        // Colapsar todas
        $('.cargas-list').addClass('collapsed');
        $('.collapse-toggle').addClass('collapsed');
        $('.collapse-toggle i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        icon.removeClass('fa-compress-alt').addClass('fa-expand-alt');
        btn.html('<i class="fa fa-expand-alt"></i> Expandir Todo');
        allCollapsed = true;
    }
}

// ============================================
// INICIALIZACIÓN
// ============================================
$(document).ready(function() {
    cargarDatos();
    
    // Event listeners para cambio de vista
    $('.view-btn').on('click', function() {
        $('.view-btn').removeClass('active');
        $(this).addClass('active');
        currentView = $(this).data('view');
        renderizarVista();
        
        // Actualizar visibilidad del botón "Colapsar Todo"
        setTimeout(function() {
            if (currentView === 'docentes') {
                $('#btnCollapseAll').fadeIn(200);
                allCollapsed = false;
                $('#btnCollapseAll').html('<i class="fa fa-compress-alt"></i> Colapsar Todo');
            } else {
                $('#btnCollapseAll').fadeOut(200);
            }
        }, 100);
    });
    
    // Event listeners para filtros
    $('#filterPeriodo, #filterCurso').on('change', function() {
        currentFilters.periodo = $('#filterPeriodo').val();
        currentFilters.curso = $('#filterCurso').val();
        aplicarFiltros();
    });
    
    // Event listener para botón "Colapsar Todo"
    $('#btnCollapseAll').on('click', function() {
        colapsarExpandirTodas();
    });
});

// ============================================
// FUNCIONES DE CARGA DE DATOS
// ============================================
function cargarDatos() {
    $('#loadingContainer').removeClass('hidden');
    $('#vistaDocentes, #vistaCursos, #vistaMatriz, #emptyState').addClass('hidden');
    
    $.ajax({
        url: 'ajax-obtener-cargas-visual.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                cargasData = response.data;
                actualizarEstadisticas(response.stats);
                renderizarVista();
            } else {
                mostrarError('Error al cargar datos: ' + response.message);
            }
            $('#loadingContainer').addClass('hidden');
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            mostrarError('Error de conexión al cargar los datos');
            $('#loadingContainer').addClass('hidden');
        }
    });
}

function aplicarFiltros() {
    renderizarVista();
}

function resetFilters() {
    $('#filterPeriodo, #filterCurso').val('');
    currentFilters = { periodo: '', curso: '' };
    aplicarFiltros();
}

// ============================================
// FUNCIONES DE RENDERIZADO
// ============================================
function renderizarVista() {
    // Filtrar datos
    let datosFiltrados = filtrarDatos(cargasData);
    
    // Ocultar todas las vistas
    $('#vistaDocentes, #vistaCursos, #vistaMatriz, #emptyState').addClass('hidden');
    
    // Mostrar vista correspondiente
    if (datosFiltrados.length === 0) {
        $('#emptyState').removeClass('hidden');
        return;
    }
    
    switch(currentView) {
        case 'docentes':
            renderizarVistaDocentes(datosFiltrados);
            break;
        case 'cursos':
            renderizarVistaCursos(datosFiltrados);
            break;
        case 'matriz':
            renderizarVistaMatriz(datosFiltrados);
            break;
    }
}

function filtrarDatos(datos) {
    return datos.filter(carga => {
        let cumplePeriodo = !currentFilters.periodo || carga.car_periodo == currentFilters.periodo;
        let cumpleCurso = !currentFilters.curso || carga.car_curso == currentFilters.curso;
        return cumplePeriodo && cumpleCurso;
    });
}

function renderizarVistaDocentes(datos) {
    // Agrupar por docente
    let porDocente = {};
    datos.forEach(carga => {
        let docenteId = carga.car_docente;
        if (!porDocente[docenteId]) {
            porDocente[docenteId] = {
                id: docenteId,
                nombre: carga.docente_nombre,
                cargas: []
            };
        }
        porDocente[docenteId].cargas.push(carga);
    });
    
    let html = '';
    Object.values(porDocente).forEach(docente => {
        let iniciales = obtenerIniciales(docente.nombre);
        let colorAvatar = obtenerColorPorNombre(docente.nombre);
        
        html += `
            <div class="docente-card">
                <div class="docente-header" style="background: ${colorAvatar};" data-docente-id="${docente.id}">
                    <div class="docente-avatar">${iniciales}</div>
                    <div class="docente-info">
                        <h3>${docente.nombre}</h3>
                        <p><i class="fa fa-book"></i> ${docente.cargas.length} carga(s) asignada(s)</p>
                    </div>
                    <button class="collapse-toggle" onclick="toggleCollapseDocente(event, '${docente.id}')" title="Colapsar/Expandir">
                        <i class="fa fa-chevron-up"></i>
                    </button>
                </div>
                <div class="cargas-list" id="docente-${docente.id}" data-docente-id="${docente.id}">
                    ${renderizarCargasDocente(docente.cargas)}
                </div>
            </div>
        `;
    });
    
    $('#vistaDocentes').html(html).removeClass('hidden');
    inicializarDragAndDrop();
    inicializarCollapse();
}

function renderizarCargasDocente(cargas) {
    if (cargas.length === 0) {
        return '<div class="cargas-list empty">Sin cargas asignadas</div>';
    }
    
    return cargas.map(carga => `
        <div class="carga-item" data-carga-id="${carga.car_id}" data-docente-id="${carga.car_docente}">
            <div class="carga-header">
                <div class="carga-title">
                    <i class="fa fa-book"></i>
                    ${carga.asignatura_nombre}
                </div>
                <div>
                    <span class="carga-badge badge-periodo">P${carga.car_periodo}</span>
                    <span class="carga-badge badge-ih">${carga.car_ih}h</span>
                    <span class="carga-badge ${carga.car_activa == 1 ? 'badge-activa' : 'badge-inactiva'}">
                        ${carga.car_activa == 1 ? 'Activa' : 'Inactiva'}
                    </span>
                </div>
            </div>
            <div class="carga-details">
                <div class="carga-detail">
                    <i class="fa fa-school"></i>
                    <span>${carga.curso_nombre}</span>
                </div>
                <div class="carga-detail">
                    <i class="fa fa-users"></i>
                    <span>Grupo ${carga.grupo_nombre}</span>
                </div>
                ${carga.car_director_grupo == 1 ? '<div class="carga-detail"><i class="fa fa-star"></i><span>Director de Grupo</span></div>' : ''}
            </div>
            <div class="carga-quick-actions">
                <button class="quick-action-btn" onclick="abrirModalCambioDocente('${carga.car_id}', '${carga.car_docente}')" title="Cambiar docente">
                    <i class="fa fa-exchange-alt"></i> Cambiar Docente
                </button>
            </div>
            <i class="fas fa-grip-vertical drag-handle"></i>
        </div>
    `).join('');
}

function renderizarVistaCursos(datos) {
    // Agrupar por curso
    let porCurso = {};
    datos.forEach(carga => {
        let cursoId = carga.car_curso;
        if (!porCurso[cursoId]) {
            porCurso[cursoId] = {
                id: cursoId,
                nombre: carga.curso_nombre,
                grupos: {}
            };
        }
        
        let grupoId = carga.car_grupo;
        if (!porCurso[cursoId].grupos[grupoId]) {
            porCurso[cursoId].grupos[grupoId] = {
                id: grupoId,
                nombre: carga.grupo_nombre,
                cargas: []
            };
        }
        porCurso[cursoId].grupos[grupoId].cargas.push(carga);
    });
    
    let html = '';
    Object.values(porCurso).forEach(curso => {
        let totalCargas = Object.values(curso.grupos).reduce((sum, grupo) => sum + grupo.cargas.length, 0);
        
        html += `
            <div class="curso-card">
                <div class="curso-header">
                    <h3><i class="fa fa-graduation-cap"></i> ${curso.nombre}</h3>
                    <div class="curso-stats">
                        <span><i class="fa fa-users"></i> ${Object.keys(curso.grupos).length} grupo(s)</span>
                        <span><i class="fa fa-book"></i> ${totalCargas} carga(s)</span>
                    </div>
                </div>
                <div class="curso-body">
                    <div class="grupos-container">
                        ${renderizarGruposCurso(curso.grupos, curso.id)}
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#vistaCursos').html(html).removeClass('hidden');
    inicializarDragAndDropCursos();
}

function renderizarGruposCurso(grupos, cursoId) {
    return Object.values(grupos).map(grupo => `
        <div class="grupo-section grupo-drop-zone" data-curso-id="${cursoId}" data-grupo-id="${grupo.id}">
            <div class="grupo-title">
                <i class="fa fa-users"></i>
                Grupo ${grupo.nombre} (${grupo.cargas.length} carga(s))
                <small style="opacity: 0.7; font-weight: normal; margin-left: 8px;">
                    <i class="fas fa-arrows-alt"></i> Arrastra cargas aquí
                </small>
            </div>
            <div class="cargas-grupo-list ${grupo.cargas.length === 0 ? 'empty' : ''}">
                ${grupo.cargas.length > 0 ? grupo.cargas.map(carga => `
                    <div class="carga-item" data-carga-id="${carga.car_id}" data-docente-id="${carga.car_docente}" data-curso-id="${carga.car_curso}" data-grupo-id="${carga.car_grupo}">
                        <div class="carga-header">
                            <div class="carga-title">
                                <i class="fa fa-book"></i>
                                ${carga.asignatura_nombre}
                            </div>
                            <div>
                                <span class="carga-badge badge-periodo">P${carga.car_periodo}</span>
                                <span class="carga-badge badge-ih">${carga.car_ih}h</span>
                            </div>
                        </div>
                        <div class="carga-details">
                            <div class="carga-detail">
                                <i class="fa fa-chalkboard-teacher"></i>
                                <span>${carga.docente_nombre}</span>
                            </div>
                            <div class="carga-detail">
                                <i class="fa fa-clock"></i>
                                <span>${carga.car_ih} horas</span>
                            </div>
                            ${carga.car_director_grupo == 1 ? '<div class="carga-detail"><i class="fa fa-star"></i><span>Director de Grupo</span></div>' : ''}
                        </div>
                        <i class="fas fa-grip-vertical drag-handle"></i>
                    </div>
                `).join('') : 'Arrastra cargas aquí'}
            </div>
        </div>
    `).join('');
}

function renderizarVistaMatriz(datos) {
    let html = `
        <table class="matrix-table">
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Curso</th>
                    <th>Grupo</th>
                    <th>Asignatura</th>
                    <th>Periodo</th>
                    <th>I.H</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${datos.map(carga => `
                    <tr>
                        <td class="matrix-cell-docente">${carga.docente_nombre}</td>
                        <td class="matrix-cell-curso">${carga.curso_nombre}</td>
                        <td>${carga.grupo_nombre}</td>
                        <td class="matrix-cell-asignatura">${carga.asignatura_nombre}</td>
                        <td><span class="carga-badge badge-periodo">P${carga.car_periodo}</span></td>
                        <td>${carga.car_ih}h</td>
                        <td>
                            <span class="carga-badge ${carga.car_activa == 1 ? 'badge-activa' : 'badge-inactiva'}">
                                ${carga.car_activa == 1 ? 'Activa' : 'Inactiva'}
                            </span>
                        </td>
                        <td class="matrix-actions">
                            <button class="matrix-btn matrix-btn-edit" onclick="abrirModalCambioDocente('${carga.car_id}', '${carga.car_docente}')">
                                <i class="fa fa-exchange-alt"></i> Cambiar
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    
    $('#vistaMatriz').html(html).removeClass('hidden');
}

// ============================================
// DRAG AND DROP CON AUTO-SCROLL
// ============================================
let autoScrollInterval = null;
let scrollSpeed = 0;

function inicializarDragAndDrop() {
    $('.cargas-list').each(function() {
        if (!$(this).hasClass('empty')) {
            new Sortable(this, {
                group: 'cargas',
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                handle: '.drag-handle',
                scroll: true, // Habilitar scroll automático
                scrollSensitivity: 80, // Área sensible (px desde el borde)
                scrollSpeed: 10, // Velocidad del scroll
                bubbleScroll: true, // Scroll en contenedores padres
                onStart: function(evt) {
                    $('body').addClass('dragging-active');
                    iniciarAutoScroll();
                },
                onEnd: function(evt) {
                    $('body').removeClass('dragging-active');
                    detenerAutoScroll();
                    ocultarZonasScroll();
                    
                    let cargaId = $(evt.item).data('carga-id');
                    let nuevoDocenteId = $(evt.to).data('docente-id');
                    let antiguoDocenteId = $(evt.from).data('docente-id');
                    
                    if (nuevoDocenteId !== antiguoDocenteId) {
                        confirmarCambioDocenteDrag(cargaId, nuevoDocenteId, antiguoDocenteId, evt);
                    }
                },
                onMove: function(evt) {
                    // Calcular posición del mouse respecto a la ventana
                    let mouseY = evt.originalEvent.clientY;
                    let windowHeight = window.innerHeight;
                    
                    // Zona superior (primeros 100px)
                    if (mouseY < 100) {
                        scrollSpeed = -15; // Scroll hacia arriba
                        mostrarZonaScroll('top');
                    }
                    // Zona inferior (últimos 100px)
                    else if (mouseY > windowHeight - 100) {
                        scrollSpeed = 15; // Scroll hacia abajo
                        mostrarZonaScroll('bottom');
                    }
                    // Zona media (sin scroll)
                    else {
                        scrollSpeed = 0;
                        ocultarZonasScroll();
                    }
                }
            });
        }
    });
}

function mostrarZonaScroll(zone) {
    if (zone === 'top') {
        $('#scrollZoneTop').addClass('active');
        $('#scrollZoneBottom').removeClass('active');
    } else if (zone === 'bottom') {
        $('#scrollZoneBottom').addClass('active');
        $('#scrollZoneTop').removeClass('active');
    }
}

function ocultarZonasScroll() {
    $('#scrollZoneTop, #scrollZoneBottom').removeClass('active');
}

function iniciarAutoScroll() {
    // Limpiar interval existente si lo hay
    if (autoScrollInterval) {
        clearInterval(autoScrollInterval);
    }
    
    // Crear nuevo interval para el auto-scroll
    autoScrollInterval = setInterval(function() {
        if (scrollSpeed !== 0) {
            window.scrollBy(0, scrollSpeed);
        }
    }, 20); // Actualizar cada 20ms para scroll suave
}

function detenerAutoScroll() {
    if (autoScrollInterval) {
        clearInterval(autoScrollInterval);
        autoScrollInterval = null;
    }
    scrollSpeed = 0;
}

function confirmarCambioDocenteDrag(cargaId, nuevoDocenteId, antiguoDocenteId, evt) {
    // Mostrar confirmación
    if (confirm('¿Deseas cambiar esta carga al nuevo docente?')) {
        actualizarCarga(cargaId, { car_docente: nuevoDocenteId }, function(success) {
            if (success) {
                mostrarExito('Carga actualizada exitosamente');
                cargarDatos(); // Recargar para reflejar cambios
            } else {
                // Revertir cambio visual
                $(evt.from).append(evt.item);
                mostrarError('Error al actualizar la carga');
            }
        });
    } else {
        // Revertir cambio visual
        $(evt.from).append(evt.item);
    }
}

// Drag and Drop específico para vista de cursos CON AUTO-SCROLL
function inicializarDragAndDropCursos() {
    $('.cargas-grupo-list').each(function() {
        // Inicializar Sortable incluso en listas vacías para poder recibir elementos
        new Sortable(this, {
            group: 'cargas-cursos',
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            handle: '.drag-handle',
            emptyInsertThreshold: 20,
            scroll: true,
            scrollSensitivity: 80,
            scrollSpeed: 10,
            bubbleScroll: true,
            onStart: function(evt) {
                $('body').addClass('dragging-active');
                iniciarAutoScroll();
            },
            onEnd: function(evt) {
                $('body').removeClass('dragging-active');
                detenerAutoScroll();
                ocultarZonasScroll();
                
                let cargaId = $(evt.item).data('carga-id');
                let cursoAntiguo = $(evt.item).data('curso-id');
                let grupoAntiguo = $(evt.item).data('grupo-id');
                
                // Obtener nuevo curso y grupo desde el contenedor de destino
                let grupoContainer = $(evt.to).closest('.grupo-drop-zone');
                let nuevoCurso = grupoContainer.data('curso-id');
                let nuevoGrupo = grupoContainer.data('grupo-id');
                
                // Verificar si hubo cambio
                if (cursoAntiguo != nuevoCurso || grupoAntiguo != nuevoGrupo) {
                    confirmarCambioCursoGrupo(cargaId, nuevoCurso, nuevoGrupo, cursoAntiguo, grupoAntiguo, evt);
                }
            },
            onMove: function(evt) {
                // Calcular posición del mouse respecto a la ventana
                let mouseY = evt.originalEvent.clientY;
                let windowHeight = window.innerHeight;
                
                // Zona superior (primeros 100px)
                if (mouseY < 100) {
                    scrollSpeed = -15;
                    mostrarZonaScroll('top');
                }
                // Zona inferior (últimos 100px)
                else if (mouseY > windowHeight - 100) {
                    scrollSpeed = 15;
                    mostrarZonaScroll('bottom');
                }
                // Zona media (sin scroll)
                else {
                    scrollSpeed = 0;
                    ocultarZonasScroll();
                }
            }
        });
    });
}

function confirmarCambioCursoGrupo(cargaId, nuevoCurso, nuevoGrupo, cursoAntiguo, grupoAntiguo, evt) {
    let mensaje = '';
    let cambios = {};
    
    if (cursoAntiguo != nuevoCurso && grupoAntiguo != nuevoGrupo) {
        mensaje = '¿Deseas mover esta carga a un nuevo curso y grupo?';
        cambios = { car_curso: nuevoCurso, car_grupo: nuevoGrupo };
    } else if (cursoAntiguo != nuevoCurso) {
        mensaje = '¿Deseas mover esta carga a un nuevo curso?';
        cambios = { car_curso: nuevoCurso, car_grupo: nuevoGrupo };
    } else if (grupoAntiguo != nuevoGrupo) {
        mensaje = '¿Deseas mover esta carga a un nuevo grupo?';
        cambios = { car_grupo: nuevoGrupo };
    }
    
    if (confirm(mensaje)) {
        actualizarCarga(cargaId, cambios, function(success) {
            if (success) {
                mostrarExito('Carga movida exitosamente');
                // Actualizar data attributes del elemento
                $(evt.item).data('curso-id', nuevoCurso);
                $(evt.item).data('grupo-id', nuevoGrupo);
                // Recargar para reflejar cambios completos
                setTimeout(function() {
                    cargarDatos();
                }, 1000);
            } else {
                // Revertir cambio visual
                $(evt.from).append(evt.item);
                mostrarError('Error al mover la carga');
            }
        });
    } else {
        // Revertir cambio visual
        $(evt.from).append(evt.item);
    }
}

// ============================================
// MODAL Y CAMBIOS
// ============================================
function abrirModalCambioDocente(cargaId, docenteActualId) {
    $('#cargaIdCambio').val(cargaId);
    $('#docenteOrigenId').val(docenteActualId);
    $('#selectNuevoDocente').val('');
    $('#modalCambiarDocente').addClass('active');
}

function cerrarModal() {
    $('#modalCambiarDocente').removeClass('active');
}

function confirmarCambioDocente() {
    let cargaId = $('#cargaIdCambio').val();
    let nuevoDocenteId = $('#selectNuevoDocente').val();
    
    if (!nuevoDocenteId) {
        alert('Por favor selecciona un docente');
        return;
    }
    
    actualizarCarga(cargaId, { car_docente: nuevoDocenteId }, function(success) {
        if (success) {
            cerrarModal();
            mostrarExito('Docente cambiado exitosamente');
            cargarDatos();
        } else {
            mostrarError('Error al cambiar el docente');
        }
    });
}

// ============================================
// ACTUALIZACIÓN DE DATOS
// ============================================
function actualizarCarga(cargaId, datos, callback) {
    $.ajax({
        url: 'ajax-actualizar-carga-visual.php',
        type: 'POST',
        data: {
            carga_id: cargaId,
            datos: JSON.stringify(datos)
        },
        dataType: 'json',
        success: function(response) {
            callback(response.success);
        },
        error: function() {
            callback(false);
        }
    });
}

// ============================================
// UTILIDADES
// ============================================
function actualizarEstadisticas(stats) {
    $('#totalDocentes').text(stats.docentes || 0);
    $('#totalCargas').text(stats.cargas || 0);
    $('#totalCursos').text(stats.cursos || 0);
}

function obtenerIniciales(nombre) {
    if (!nombre) return '??';
    let partes = nombre.trim().split(' ');
    if (partes.length >= 2) {
        return (partes[0][0] + partes[1][0]).toUpperCase();
    }
    return nombre.substring(0, 2).toUpperCase();
}

function obtenerColorPorNombre(nombre) {
    const colores = [
        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
        'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
        'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)'
    ];
    let hash = 0;
    for (let i = 0; i < nombre.length; i++) {
        hash = nombre.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colores[Math.abs(hash) % colores.length];
}

function mostrarExito(mensaje) {
    $.toast({
        heading: 'Éxito',
        text: mensaje,
        position: 'top-right',
        loaderBg: '#34bfa3',
        icon: 'success',
        hideAfter: 3000
    });
}

function mostrarError(mensaje) {
    $.toast({
        heading: 'Error',
        text: mensaje,
        position: 'top-right',
        loaderBg: '#f4516c',
        icon: 'error',
        hideAfter: 4000
    });
}

// Cerrar modal al hacer clic fuera
$('#modalCambiarDocente').on('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});

// ============================================
// BOTÓN SCROLL TO TOP
// ============================================
$(window).on('scroll', function() {
    if ($(this).scrollTop() > 300) {
        $('#scrollToTop').addClass('show');
    } else {
        $('#scrollToTop').removeClass('show');
    }
});

$('#scrollToTop').on('click', function() {
    $('html, body').animate({ scrollTop: 0 }, 600);
});

// ============================================
// BÚSQUEDA EN VISTAS
// ============================================
$('#inputSearchDocentes').on('keyup', function() {
    let searchTerm = $(this).val().toLowerCase();
    
    $('.docente-card').each(function() {
        let text = $(this).text().toLowerCase();
        if (text.includes(searchTerm)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

$('#inputSearchCursos').on('keyup', function() {
    let searchTerm = $(this).val().toLowerCase();
    
    $('.curso-card').each(function() {
        let text = $(this).text().toLowerCase();
        if (text.includes(searchTerm)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

// ============================================
// VISTA MATRICIAL CON PAGINACIÓN Y ORDENAMIENTO
// ============================================
function renderizarVistaMatrizMejorada(datos) {
    cargasDataFiltrada = datos;
    
    let html = `
        <div class="matrix-controls">
            <div class="matrix-search">
                <i class="fa fa-search"></i>
                <input type="text" id="matrixSearchInput" placeholder="Buscar en la tabla..." value="${matrixSearchTerm}">
            </div>
            <div class="matrix-pagination">
                <select id="matrixPerPageSelect">
                    <option value="10" ${matrixPerPage == 10 ? 'selected' : ''}>10 por página</option>
                    <option value="25" ${matrixPerPage == 25 ? 'selected' : ''}>25 por página</option>
                    <option value="50" ${matrixPerPage == 50 ? 'selected' : ''}>50 por página</option>
                    <option value="100" ${matrixPerPage == 100 ? 'selected' : ''}>100 por página</option>
                </select>
            </div>
        </div>
        <div class="matrix-table-wrapper">
            <table class="matrix-table" id="matrixTable">
                <thead>
                    <tr>
                        <th data-column="docente_nombre">Docente <span class="sort-icon"></span></th>
                        <th data-column="curso_nombre">Curso <span class="sort-icon"></span></th>
                        <th data-column="grupo_nombre">Grupo <span class="sort-icon"></span></th>
                        <th data-column="asignatura_nombre">Asignatura <span class="sort-icon"></span></th>
                        <th data-column="car_periodo">Periodo <span class="sort-icon"></span></th>
                        <th data-column="car_ih">I.H <span class="sort-icon"></span></th>
                        <th data-column="car_activa">Estado <span class="sort-icon"></span></th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="matrixTableBody">
                </tbody>
            </table>
        </div>
        <div class="matrix-controls">
            <div class="pagination-info" id="matrixPaginationInfo"></div>
            <div class="pagination-buttons" id="matrixPaginationButtons"></div>
        </div>
    `;
    
    $('#vistaMatriz').html(html).removeClass('hidden');
    aplicarFiltrosMatriz();
    
    // Event listeners
    $('#matrixSearchInput').on('keyup', function() {
        matrixSearchTerm = $(this).val();
        matrixPage = 1;
        aplicarFiltrosMatriz();
    });
    
    $('#matrixPerPageSelect').on('change', function() {
        matrixPerPage = parseInt($(this).val());
        matrixPage = 1;
        aplicarFiltrosMatriz();
    });
    
    // Ordenamiento por columnas
    $('#matrixTable thead th[data-column]').on('click', function() {
        let column = $(this).data('column');
        
        if (matrixSortColumn === column) {
            matrixSortDirection = matrixSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            matrixSortColumn = column;
            matrixSortDirection = 'asc';
        }
        
        aplicarFiltrosMatriz();
    });
}

function aplicarFiltrosMatriz() {
    // Filtrar datos
    let datosFiltrados = cargasDataFiltrada.filter(carga => {
        if (!matrixSearchTerm) return true;
        let searchLower = matrixSearchTerm.toLowerCase();
        return (
            (carga.docente_nombre && carga.docente_nombre.toLowerCase().includes(searchLower)) ||
            (carga.curso_nombre && carga.curso_nombre.toLowerCase().includes(searchLower)) ||
            (carga.grupo_nombre && carga.grupo_nombre.toLowerCase().includes(searchLower)) ||
            (carga.asignatura_nombre && carga.asignatura_nombre.toLowerCase().includes(searchLower))
        );
    });
    
    // Ordenar datos
    if (matrixSortColumn) {
        datosFiltrados.sort((a, b) => {
            let aVal = a[matrixSortColumn] || '';
            let bVal = b[matrixSortColumn] || '';
            
            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }
            
            if (matrixSortDirection === 'asc') {
                return aVal > bVal ? 1 : -1;
            } else {
                return aVal < bVal ? 1 : -1;
            }
        });
        
        // Actualizar clases de ordenamiento
        $('#matrixTable thead th').removeClass('sorted-asc sorted-desc');
        $('#matrixTable thead th[data-column="' + matrixSortColumn + '"]')
            .addClass('sorted-' + matrixSortDirection);
    }
    
    // Paginación
    let totalFiltered = datosFiltrados.length;
    let totalPages = Math.ceil(totalFiltered / matrixPerPage);
    let start = (matrixPage - 1) * matrixPerPage;
    let end = start + matrixPerPage;
    let datosPagina = datosFiltrados.slice(start, end);
    
    // Renderizar filas
    let tbody = '';
    datosPagina.forEach(carga => {
        tbody += `
            <tr>
                <td class="matrix-cell-docente">${carga.docente_nombre}</td>
                <td class="matrix-cell-curso">${carga.curso_nombre}</td>
                <td>${carga.grupo_nombre}</td>
                <td class="matrix-cell-asignatura">${carga.asignatura_nombre}</td>
                <td><span class="carga-badge badge-periodo">P${carga.car_periodo}</span></td>
                <td>${carga.car_ih}h</td>
                <td>
                    <span class="carga-badge ${carga.car_activa == 1 ? 'badge-activa' : 'badge-inactiva'}">
                        ${carga.car_activa == 1 ? 'Activa' : 'Inactiva'}
                    </span>
                </td>
                <td class="matrix-actions">
                    <button class="matrix-btn matrix-btn-edit" onclick="abrirModalCambioDocente('${carga.car_id}', '${carga.car_docente}')">
                        <i class="fa fa-exchange-alt"></i> Cambiar
                    </button>
                </td>
            </tr>
        `;
    });
    
    $('#matrixTableBody').html(tbody);
    
    // Actualizar info de paginación
    let showingStart = totalFiltered > 0 ? start + 1 : 0;
    let showingEnd = Math.min(end, totalFiltered);
    $('#matrixPaginationInfo').html(`Mostrando ${showingStart} a ${showingEnd} de ${totalFiltered} registros`);
    
    // Renderizar botones de paginación
    renderizarPaginacionMatriz(totalPages);
}

function renderizarPaginacionMatriz(totalPages) {
    let html = '';
    
    // Botón anterior
    html += `<button class="pagination-btn" ${matrixPage <= 1 ? 'disabled' : ''} onclick="cambiarPaginaMatriz(${matrixPage - 1})">
        <i class="fa fa-chevron-left"></i>
    </button>`;
    
    // Números de página
    let startPage = Math.max(1, matrixPage - 2);
    let endPage = Math.min(totalPages, matrixPage + 2);
    
    if (startPage > 1) {
        html += `<button class="pagination-btn" onclick="cambiarPaginaMatriz(1)">1</button>`;
        if (startPage > 2) {
            html += `<span style="padding: 0.5rem;">...</span>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<button class="pagination-btn ${i === matrixPage ? 'active' : ''}" onclick="cambiarPaginaMatriz(${i})">${i}</button>`;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<span style="padding: 0.5rem;">...</span>`;
        }
        html += `<button class="pagination-btn" onclick="cambiarPaginaMatriz(${totalPages})">${totalPages}</button>`;
    }
    
    // Botón siguiente
    html += `<button class="pagination-btn" ${matrixPage >= totalPages ? 'disabled' : ''} onclick="cambiarPaginaMatriz(${matrixPage + 1})">
        <i class="fa fa-chevron-right"></i>
    </button>`;
    
    $('#matrixPaginationButtons').html(html);
}

function cambiarPaginaMatriz(newPage) {
    matrixPage = newPage;
    aplicarFiltrosMatriz();
    $('html, body').animate({ scrollTop: $('#vistaMatriz').offset().top - 100 }, 400);
}

// ============================================
// ACTUALIZAR RENDERIZADO DE VISTA MATRICIAL
// ============================================
// Guardar la función original
let renderizarVistaMatrizOriginal = renderizarVistaMatriz;

// Sobrescribir con la nueva versión
renderizarVistaMatriz = function(datos) {
    renderizarVistaMatrizMejorada(datos);
};

// ============================================
// MOSTRAR/OCULTAR BUSCADORES SEGÚN VISTA
// ============================================
function actualizarVisibilidadBuscadores() {
    $('#searchDocentes, #searchCursos').addClass('hidden');
    
    if (currentView === 'docentes') {
        $('#searchDocentes').removeClass('hidden');
    } else if (currentView === 'cursos') {
        $('#searchCursos').removeClass('hidden');
    }
}

// Actualizar al cambiar de vista
$('.view-btn[data-view]').on('click', function() {
    setTimeout(actualizarVisibilidadBuscadores, 100);
});
</script>

</body>
</html>

