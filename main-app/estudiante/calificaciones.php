<?php include("session.php");?>
<?php include("verificar-usuario.php");?>
<?php $idPaginaInterna = 'ES0020';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-pagina-bloqueada.php");?>
<?php include("../compartido/head.php");?>

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

    /* Botón flotante de filtros */
    .filter-fab {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, var(--secondary-color) 0%, #35a39d 100%);
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(65, 193, 186, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1000;
        transition: var(--transition);
        border: none;
        color: white;
        font-size: 24px;
    }

    .filter-fab:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(65, 193, 186, 0.6);
    }

    .filter-fab:active {
        transform: scale(0.95);
    }

    /* Overlay para cerrar sidebar */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1098;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
    }

    .sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* Sidebar de filtros */
    .filter-sidebar {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        max-width: 90vw;
        height: 100vh;
        background: white;
        box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
        z-index: 1099;
        overflow-y: auto;
        transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .filter-sidebar.active {
        right: 0;
    }

    .filter-sidebar-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .filter-sidebar-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }

    .filter-sidebar-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        font-size: 18px;
    }

    .filter-sidebar-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .filter-sidebar-body {
        padding: 20px;
        flex: 1;
    }

    .filter-section {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e0e6ed;
    }

    .filter-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .filter-section-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-section-title i {
        color: var(--secondary-color);
    }

    .filter-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .filter-list-item {
        margin-bottom: 8px;
    }

    .filter-link {
        display: block;
        padding: 10px 15px;
        border-radius: 8px;
        color: #7f8c8d;
        text-decoration: none;
        transition: var(--transition);
        border-left: 3px solid transparent;
        font-size: 14px;
    }

    .filter-link:hover {
        background: rgba(65, 193, 186, 0.1);
        color: var(--primary-color);
        border-left-color: var(--secondary-color);
        transform: translateX(5px);
    }

    .filter-link.active {
        background: linear-gradient(90deg, rgba(65, 193, 186, 0.1), rgba(65, 193, 186, 0.05));
        color: var(--secondary-color);
        border-left-color: var(--secondary-color);
        font-weight: 600;
    }

    .filter-sidebar-footer {
        padding: 20px;
        border-top: 1px solid #e0e6ed;
        background: #f8f9fa;
        position: sticky;
        bottom: 0;
    }

    .btn-apply-filters {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, var(--secondary-color) 0%, #35a39d 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-apply-filters:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(65, 193, 186, 0.4);
    }

    /* Diseño moderno del contenido */
    .page-header-modern {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a252f 100%);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: var(--card-shadow);
        color: white;
    }

    .grades-card-modern {
        background: white;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .grades-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px 30px;
        color: white;
    }

    .grades-card-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }

    .grades-card-body {
        padding: 0;
    }

    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-modern thead {
        background: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 5;
    }

    .table-modern thead th {
        padding: 15px;
        text-align: left;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--primary-color);
        border-bottom: 2px solid #e0e6ed;
    }

    .table-modern tbody tr {
        transition: var(--transition);
        border-bottom: 1px solid #f0f0f0;
    }

    .table-modern tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
    }

    .table-modern tbody td {
        padding: 15px;
        font-size: 14px;
        color: #555;
    }

    .table-modern tfoot {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .table-modern tfoot td {
        padding: 15px;
        font-weight: 700;
        font-size: 15px;
        color: var(--primary-color);
    }

    .badge-indicador {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        background: rgba(52, 152, 219, 0.1);
        color: var(--info-color);
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .filter-sidebar {
            width: 100vw;
            max-width: 100vw;
        }

        .filter-fab {
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .page-header-modern {
            padding: 20px;
        }
    }
</style>

<?php
//Temporal para que el estudiante no vea notas ni nada de eso.
if($config['conf_servidor']==1){
    echo '<script type="text/javascript">window.location.href="cargas.php";</script>';
    exit();
}
?>

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
             	<?php include("../compartido/calificaciones-estudiante-contenido.php");?>
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
    
    <?php
    // Solo mostrar filtros para estudiantes
    if($datosUsuarioActual['uss_tipo']==TIPO_ESTUDIANTE){
    ?>
    <!-- Botón flotante de filtros -->
    <button class="filter-fab" id="filterFab" title="Filtros">
        <i class="fa fa-filter"></i>
    </button>
    
    <!-- Overlay para cerrar sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar de filtros -->
    <div class="filter-sidebar" id="filterSidebar">
        <div class="filter-sidebar-header">
            <h3><i class="fa fa-filter mr-2"></i> Filtros</h3>
            <button class="filter-sidebar-close" id="filterSidebarClose">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="filter-sidebar-body">
            <?php
            require_once("../class/servicios/CargaServicios.php");
            require_once("../class/servicios/MediaTecnicaServicios.php");
            require_once("../class/servicios/GradoServicios.php");
            require_once("../class/CargaAcademica.php");
            
            // Sección de Cargas Normales
            $cCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosEstudianteActual['mat_grado'], $datosEstudianteActual['mat_grupo']);
            $nCargas = mysqli_num_rows($cCargas);
            if($nCargas > 0){
                $hasItems = false;
                while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
                    if($rCargas['car_curso_extension']==1){
                        $cursoExt = CargaAcademica::validarCursosComplementario($conexion, $config, $datosEstudianteActual['mat_id'], $rCargas['car_id']);
                        if($cursoExt==0){continue;}
                    }
                    if(!$hasItems){
                        echo '<div class="filter-section">';
                        echo '<div class="filter-section-title"><i class="fa fa-book"></i> '.$frases[73][$datosUsuarioActual['uss_idioma']].'</div>';
                        echo '<ul class="filter-list">';
                        $hasItems = true;
                    }
                    $isActive = ($rCargas['car_id']==$cargaConsultaActual) ? 'active' : '';
                    echo '<li class="filter-list-item">';
                    echo '<a href="'.$_SERVER['PHP_SELF'].'?carga='.base64_encode($rCargas['car_id']).'&periodo='.base64_encode($periodoConsultaActual).'" class="filter-link '.$isActive.'">';
                    echo strtoupper($rCargas['mat_nombre']);
                    echo '</a></li>';
                }
                if($hasItems){
                    echo '</ul></div>';
                }
            }
            
            // Sección de Media Técnica
            if (array_key_exists(10, $arregloModulos)) {
                $parametros = [
                    'matcur_id_matricula' => $datosEstudianteActual["mat_id"],
                    'matcur_id_institucion' => $config['conf_id_institucion'],
                    'matcur_years' => $config['conf_agno']
                ];
                $listaCursosMediaTecnica = MediaTecnicaServicios::listar($parametros);
                if(!empty($listaCursosMediaTecnica)){
                    foreach ($listaCursosMediaTecnica as $dato) {
                        $cursoMediaTecnica = GradoServicios::consultarCurso($dato["matcur_id_curso"]);
                        echo '<div class="filter-section">';
                        echo '<div class="filter-section-title"><i class="fa fa-bookmark"></i> '.$cursoMediaTecnica['gra_nombre'].'</div>';
                        echo '<ul class="filter-list">';
                        
                        $parametros = [
                            'matcur_id_matricula' => $datosEstudianteActual["mat_id"],
                            'matcur_id_curso' => $dato["matcur_id_curso"],
                            'matcur_id_institucion' => $config['conf_id_institucion'],
                            'matcur_years' => $config['conf_agno']
                        ];
                        $listacargaMediaTecnica = MediaTecnicaServicios::listarMaterias($parametros);
                        if ($listacargaMediaTecnica != null) {
                            foreach ($listacargaMediaTecnica as $cargaMediaTecnica) {
                                $isActive = ($cargaMediaTecnica['car_id']==$cargaConsultaActual) ? 'active' : '';
                                echo '<li class="filter-list-item">';
                                echo '<a href="'.$_SERVER['PHP_SELF'].'?carga='.base64_encode($cargaMediaTecnica['car_id']).'&periodo='.$periodoConsultaActual.'" class="filter-link '.$isActive.'">';
                                echo strtoupper($cargaMediaTecnica['mat_nombre']);
                                echo '</a></li>';
                            }
                        } else {
                            echo '<li class="filter-list-item" style="padding: 10px 15px; color: #95a5a6; font-size: 12px;">No tiene cargas académicas.</li>';
                        }
                        
                        echo '</ul></div>';
                    }
                }
            }
            ?>
        </div>
        <div class="filter-sidebar-footer">
            <button class="btn-apply-filters" id="applyFilters">
                <i class="fa fa-check"></i> Aplicar Filtros
            </button>
        </div>
    </div>
    <?php } ?>
    
    <script>
        // Funcionalidad del sidebar de filtros
        const filterFab = document.getElementById('filterFab');
        const filterSidebar = document.getElementById('filterSidebar');
        const filterSidebarClose = document.getElementById('filterSidebarClose');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const applyFiltersBtn = document.getElementById('applyFilters');
        
        // Abrir sidebar
        if (filterFab && filterSidebar) {
            filterFab.addEventListener('click', () => {
                filterSidebar.classList.add('active');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }
        
        // Cerrar sidebar
        function closeSidebar() {
            if (filterSidebar) {
                filterSidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
        
        if (filterSidebarClose) {
            filterSidebarClose.addEventListener('click', closeSidebar);
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }
        
        // Aplicar filtros - cierra el sidebar
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', () => {
                closeSidebar();
            });
        }
        
        // Cerrar sidebar automáticamente al hacer click en un filtro
        document.addEventListener('DOMContentLoaded', () => {
            if (filterSidebar) {
                const links = filterSidebar.querySelectorAll('.filter-link');
                links.forEach(link => {
                    // Marcar como activo
                    const currentUrl = window.location.href;
                    if (link.href === currentUrl || link.href.includes('<?=isset($cargaConsultaActual) ? base64_encode($cargaConsultaActual) : "";?>')) {
                        link.classList.add('active');
                    }
                    
                    // Cerrar sidebar al hacer click
                    link.addEventListener('click', (e) => {
                        // Permitir que la navegación continúe
                        setTimeout(() => {
                            closeSidebar();
                        }, 100);
                    });
                });
            }
        });
    </script>
</body>

</html>