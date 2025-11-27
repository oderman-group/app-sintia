<?php include("session.php");?>
<?php
// $_SESSION["bd"] = date("Y");
?>
<?php include("verificar-usuario.php");?>
<?php include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'ES0010';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php require_once("../class/servicios/CargaServicios.php"); ?>
<?php require_once("../class/servicios/MediaTecnicaServicios.php"); ?>
<?php require_once("../class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php"); ?>
<?php
$cargaE="";
if(!empty($_GET["carga"])){ $cargaE=base64_decode($_GET["carga"]);}

$periodoE="";
if(!empty($_GET["periodo"])){ $periodoE=base64_decode($_GET["periodo"]);}

if(!empty($cargaE)){
	setcookie("cargaE",$cargaE);
	setcookie("periodoE",$periodoE);
	
	$enlaceNext = 'calificaciones.php';
	if($config['conf_sin_nota_numerica']==1){
		$enlaceNext = 'matricula.php';
	}
	if($config['conf_mostrar_calificaciones_estudiantes']!=1){
		$enlaceNext = 'matricula.php';
	}
	echo '<script type="text/javascript">window.location.href="'.$enlaceNext.'?carga='.base64_encode($cargaE).'&periodo='.base64_encode($periodoE).'";</script>';
	exit();
}
?>

<?php
if($config['conf_activar_encuesta']==1){
	$respuesta = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_encuestas 
	WHERE genc_estudiante='".$datosEstudianteActual['mat_id']."' AND genc_institucion={$config['conf_id_institucion']} AND genc_year={$_SESSION["bd"]}"));
	if($respuesta==0 and $datosEstudianteActual['mat_grado']!=11){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=214";</script>';
		exit();	
	}
}
?>

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

    /* Estilos del Header Mejorado - Más compacto y menos protagonista */
    .page-header-modern {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a252f 100%);
        border-radius: 12px;
        padding: 18px 25px;
        margin-bottom: 20px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.08);
        color: white;
        opacity: 0.95;
    }

    .stats-container {
        display: flex;
        gap: 12px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .stat-card {
        flex: 1;
        min-width: 120px;
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(5px);
        border-radius: 8px;
        padding: 12px 16px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.15);
        transition: var(--transition);
    }

    .stat-card:hover {
        background: rgba(255,255,255,0.12);
        transform: translateY(-1px);
    }

    .stat-number {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 4px;
        color: rgba(255,255,255,0.95);
    }

    .stat-label {
        font-size: 11px;
        opacity: 0.85;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
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
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 2px solid transparent;
        cursor: pointer;
    }

    .carga-card-modern:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-5px);
        border-color: var(--secondary-color);
    }

    .carga-card-modern.selected {
        border-color: var(--secondary-color);
        background: linear-gradient(to bottom, rgba(65, 193, 186, 0.05) 0%, white 100%);
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
        margin-top: 8px;
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

    .badge-periodo {
        background: linear-gradient(135deg, #16a085, #1abc9c);
        color: white;
    }

    .badge-media-tecnica {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
        color: white;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 8px 0;
        font-size: 14px;
        color: #7f8c8d;
    }

    .info-item i {
        color: var(--secondary-color);
        width: 20px;
        text-align: center;
    }

    .definitiva-display {
        margin: 15px 0;
        padding: 15px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        text-align: center;
        border: 2px solid #e0e6ed;
    }

    .definitiva-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #7f8c8d;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .definitiva-value {
        font-size: 32px;
        font-weight: 700;
        line-height: 1;
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

    .ultimo-acceso {
        font-size: 11px;
        color: #95a5a6;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #ecf0f1;
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

    /* Sección Media Técnica */
    .media-tecnica-section {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 3px solid #e0e6ed;
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
        padding-left: 15px;
        border-left: 4px solid var(--secondary-color);
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
            min-width: 140px;
            padding: 10px 14px;
        }

        .stat-number {
            font-size: 22px;
        }

        .stat-label {
            font-size: 10px;
        }
    }

    @media (max-width: 768px) {
        .page-header-modern {
            padding: 15px 18px;
        }

        .page-header-modern h2 {
            font-size: 20px !important;
        }

        .stat-number {
            font-size: 20px;
        }

        .stat-label {
            font-size: 10px;
        }

        .stats-container {
            gap: 8px;
            margin-top: 12px;
        }

        .stat-card {
            min-width: calc(50% - 4px);
            padding: 10px 12px;
        }

        .search-filter-container {
            padding: 15px;
        }

        .filter-chips {
            justify-content: center;
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
                        $cCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosEstudianteActual['mat_grado'], $datosEstudianteActual['mat_grupo']);
                        $nCargas = mysqli_num_rows($cCargas);
                        $mensajeCargas = new Cargas;
                        $mensajeCargas->verificarNumCargas($nCargas, $datosUsuarioActual['uss_idioma']);
                        
                        // Preparar datos para estadísticas
                        $listaCargas = [];
                        $index = 0;
                        $totalCargasActivas = 0;
                        mysqli_data_seek($cCargas, 0);
                        
                        while($cargaTemp = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
                            // Verificar si el estudiante está matriculado en cursos de extensión o complementarios
                            if($cargaTemp['car_curso_extension']==1){
                                $cursoExt = CargaAcademica::validarCursosComplementario($conexion, $config, $datosEstudianteActual['mat_id'], $cargaTemp['car_id']);
                                if($cursoExt==0){continue;}
                            }
                            $listaCargas[$index] = $cargaTemp;
                            $totalCargasActivas++;
                            $index++;
                        }
                        
                        mysqli_data_seek($cCargas, 0);
                    ?>

                    <!-- Header con estadísticas -->
                    <div class="page-header-modern" id="header-resumen">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h2 class="mb-1" style="font-size: 22px; font-weight: 600;">
                                    <i class="fa fa-book mr-2"></i>
                                    <?=$frases[73][$datosUsuarioActual['uss_idioma']];?>
                                </h2>
                                <p class="mb-0" style="opacity: 0.85; font-size: 13px;">Consulta y accede a tus materias académicas</p>
                            </div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                        
                        <?php if ($totalCargasActivas > 0) { ?>
                        <div class="stats-container">
                            <div class="stat-card">
                                <div class="stat-number"><?= $totalCargasActivas; ?></div>
                                <div class="stat-label">Materias Activas</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?= $datosEstudianteActual['gra_nombre']; ?></div>
                                <div class="stat-label">Grado</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?= $datosEstudianteActual['gru_nombre']; ?></div>
                                <div class="stat-label">Grupo</div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <?php if ($totalCargasActivas > 0) { ?>
                    <!-- Buscador y Filtros -->
                    <div class="search-filter-container">
                        <div class="search-box-wrapper">
                            <input 
                                type="text" 
                                id="searchInput" 
                                class="search-input-modern" 
                                placeholder="Buscar por materia o docente..."
                                autocomplete="off"
                            >
                            <i class="fa fa-search search-icon"></i>
                        </div>
                    </div>
                    <?php } ?>

                    <!-- Contenedor de tarjetas -->
                    <div class="row" id="cargas-container">
                        <?php
                        while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
										//Verificar si el estudiante está matriculado en cursos de extensión o complementarios
										if($rCargas['car_curso_extension']==1){
											$cursoExt = CargaAcademica::validarCursosComplementario($conexion, $config, $datosEstudianteActual['mat_id'], $rCargas['car_id']);
											if($cursoExt==0){continue;}
										}
									    
                            $ultimoAcceso = 'Nunca';
                            $seleccionado = false;
                            
                            $consultaHistorial = CargaAcademica::accesoCargasEstudiante($conexion, $config, $rCargas['car_id'], $datosEstudianteActual['mat_id']);
                            $cargaHistorial = mysqli_fetch_array($consultaHistorial, MYSQLI_BOTH);
                            if(!empty($cargaHistorial['carpa_id'])){
                                $ultimoAcceso = "(".$cargaHistorial['carpa_cantidad'].") ".$cargaHistorial['carpa_ultimo_acceso'];
                            }
                            if(!empty($_COOKIE["cargaE"]) && $rCargas['car_id']==$_COOKIE["cargaE"]){
                                $seleccionado = true;
                            }
                            
                            //DEFINITIVAS
                            $carga = $rCargas['car_id'];
                            $periodo = $rCargas['car_periodo'];
                            $estudiante = $datosEstudianteActual['mat_id'];
                            include("../definitivas.php");

                            $definitivaFinal=$definitiva;
                            if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                $estiloNotaDefinitiva = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
                                $definitivaFinal= !empty($estiloNotaDefinitiva['notip_nombre']) ? $estiloNotaDefinitiva['notip_nombre'] : "";
                            }
                            
                            // Determinar color de progreso
                            $progressClass = 'progress-danger';
                            if ($porcentajeActual >= 90) $progressClass = 'progress-excellent';
                            elseif ($porcentajeActual >= 70) $progressClass = 'progress-good';
                            elseif ($porcentajeActual >= 50) $progressClass = 'progress-warning';
                            
                            $colorDefinitivaDisplay = $colorDefinitiva ?? '#7f8c8d';
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4 carga-item" 
                             data-materia="<?= strtolower($rCargas['mat_nombre']); ?>"
                             data-docente="<?= strtolower(UsuariosPadre::nombreCompletoDelUsuario($rCargas)); ?>">
                            
                            <div class="carga-card-modern <?= $seleccionado ? 'selected' : ''; ?>" 
                                 onclick="window.location.href='cargas.php?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($rCargas['car_periodo']);?>'">
                                
                                <!-- Header de la tarjeta -->
                                <div class="card-header-modern">
                                    <a href="cargas.php?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($rCargas['car_periodo']);?>"
                                       class="materia-title" title="Click para entrar">
                                        <i class="fa fa-book mr-2"></i><?= strtoupper($rCargas['mat_nombre']); ?>
                                    </a>
                                    <div class="curso-info">
                                        <i class="fa fa-calendar"></i>
                                        <?=$frases[101][$datosUsuarioActual['uss_idioma']];?>: <?=$rCargas['car_periodo'];?>
                                    </div>
                                </div>

                                <!-- Body de la tarjeta -->
                                <div class="card-body-modern">
                                    <!-- Badges -->
                                    <div class="badges-container">
                                        <span class="badge-modern badge-periodo">
                                            <i class="fa fa-calendar"></i> Periodo <?=$rCargas['car_periodo'];?>
                                        </span>
                                    </div>

                                    <!-- Información del docente -->
                                    <div class="info-item">
                                        <i class="fa fa-user"></i>
                                        <span><strong><?=$frases[28][$datosUsuarioActual['uss_idioma']];?>:</strong> <?=UsuariosPadre::nombreCompletoDelUsuario($rCargas);?></span>
                                    </div>

                                    <!-- Progreso de notas -->
                                    <?php if($datosUsuarioActual['uss_bloqueado'] != 1 && $config['conf_sin_nota_numerica'] != 1 && $config['conf_mostrar_calificaciones_estudiantes'] == 1 && $porcentajeActual > 0){ ?>
                                    <div class="progress-section">
                                        <div class="progress-label">
                                            <span>Progreso de Calificaciones</span>
                                            <span><strong><?= round($porcentajeActual, 1); ?>%</strong></span>
                                        </div>
                                        <div class="progress-bar-modern">
                                            <div class="progress-fill <?= $progressClass; ?>" 
                                                 style="width: <?= min($porcentajeActual, 100); ?>%"></div>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <!-- Definitiva -->
                                    <?php if($datosUsuarioActual['uss_bloqueado'] != 1 && $config['conf_sin_nota_numerica'] != 1 && $config['conf_mostrar_calificaciones_estudiantes'] == 1 && !empty($definitivaFinal)){ ?>
                                    <div class="definitiva-display">
                                        <div class="definitiva-label">Nota Definitiva</div>
                                        <div class="definitiva-value" style="color: <?=$colorDefinitivaDisplay;?>">
                                            <?=$definitivaFinal;?>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <!-- Último acceso -->
                                    <div class="ultimo-acceso">
                                        <i class="fa fa-clock-o"></i> 
                                        <?=$ultimoAcceso;?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }?> 
	                    
                    </div>

                    <!-- Estado vacío cuando no hay resultados de búsqueda -->
                    <div class="empty-state" id="emptyState">
                        <div class="empty-state-icon">
                            <i class="fa fa-search"></i>
                        </div>
                        <div class="empty-state-title">No se encontraron materias</div>
                        <div class="empty-state-text">Intenta con otros términos de búsqueda</div>
                    </div>

                    <!-- Sección Media Técnica -->
                    <?php if (array_key_exists(10, $arregloModulos)) { ?>
                        <?php
                        $parametros = [
                            'matcur_id_matricula' => $datosEstudianteActual["mat_id"],
                            'matcur_id_institucion' => $config['conf_id_institucion'],
                            'matcur_years' => $config['conf_agno']
                        ];
                        $listaCursosMediaTecnica = MediaTecnicaServicios::listar($parametros);
                        if(!empty($listaCursosMediaTecnica)){
                        ?>
                        <div class="media-tecnica-section">
                            <?php
                            foreach ($listaCursosMediaTecnica as $dato) {
                                $cursoMediaTecnica = GradoServicios::consultarCurso($dato["matcur_id_curso"]); 
                            ?>
                            <div class="section-title">
                                <i class="fa fa-bookmark mr-2"></i>
                                <?= $cursoMediaTecnica["gra_nombre"]; ?>
                            </div>
                            
                            <div class="row" id="cargas-media-tecnica-container-<?=$dato["matcur_id_curso"];?>">
                                <?php
                                $parametros = [
                                    'matcur_id_matricula' => $datosEstudianteActual["mat_id"],
                                    'matcur_id_curso'     => $dato["matcur_id_curso"],
                                    'matcur_id_grupo'     => $dato["matcur_id_grupo"],
                                    'matcur_id_institucion' => $config['conf_id_institucion'],
                                    'matcur_years' => $config['conf_agno']
                                ];
                                $listacargaMediaTecnica = MediaTecnicaServicios::listarMaterias($parametros);
                                if ($listacargaMediaTecnica != null) { 
                                    foreach ($listacargaMediaTecnica as $cargaMediaTecnica) {
                                        $seleccionadoMT = false;
                                        if(!empty($_COOKIE["cargaE"]) && $cargaMediaTecnica["car_id"]==$_COOKIE["cargaE"]){
                                            $seleccionadoMT = true;
                                        }
                                        
                                        //DEFINITIVAS
                                        $carga = $cargaMediaTecnica["car_id"];
                                        $periodo = $cargaMediaTecnica["car_periodo"];
                                        $estudiante = $datosEstudianteActual['mat_id'];
                                        include("../definitivas.php");

                                        $definitivaFinalMT=$definitiva;
                                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                            $estiloNotaDefinitiva = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
                                            $definitivaFinalMT= !empty($estiloNotaDefinitiva['notip_nombre']) ? $estiloNotaDefinitiva['notip_nombre'] : "";
                                        }
                                        
                                        // Determinar color de progreso
                                        $progressClassMT = 'progress-danger';
                                        if ($porcentajeActual >= 90) $progressClassMT = 'progress-excellent';
                                        elseif ($porcentajeActual >= 70) $progressClassMT = 'progress-good';
                                        elseif ($porcentajeActual >= 50) $progressClassMT = 'progress-warning';
                                        
                                        $colorDefinitivaDisplayMT = $colorDefinitiva ?? '#7f8c8d';
                                    ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4 carga-item carga-media-tecnica" 
                                         data-materia="<?= strtolower($cargaMediaTecnica['mat_nombre']); ?>"
                                         data-docente="<?= strtolower(UsuariosPadre::nombreCompletoDelUsuario($cargaMediaTecnica)); ?>">
                                        
                                        <div class="carga-card-modern <?= $seleccionadoMT ? 'selected' : ''; ?>" 
                                             onclick="window.location.href='cargas.php?carga=<?=base64_encode($cargaMediaTecnica["car_id"]);?>&periodo=<?=base64_encode($cargaMediaTecnica["car_periodo"]);?>'">
                                            
                                            <!-- Header de la tarjeta -->
                                            <div class="card-header-modern">
                                                <a href="cargas.php?carga=<?=base64_encode($cargaMediaTecnica["car_id"]);?>&periodo=<?=base64_encode($cargaMediaTecnica["car_periodo"]);?>"
                                                   class="materia-title" title="Click para entrar">
                                                    <i class="fa fa-book mr-2"></i><?= strtoupper($cargaMediaTecnica['mat_nombre']); ?>
                                                </a>
                                                <div class="curso-info">
                                                    <i class="fa fa-calendar"></i>
                                                    <?=$frases[101][$datosUsuarioActual['uss_idioma']];?>: <?=$cargaMediaTecnica['car_periodo'];?>
                                                </div>
                                            </div>

                                            <!-- Body de la tarjeta -->
                                            <div class="card-body-modern">
                                                <!-- Badges -->
                                                <div class="badges-container">
                                                    <span class="badge-modern badge-periodo">
                                                        <i class="fa fa-calendar"></i> Periodo <?=$cargaMediaTecnica['car_periodo'];?>
                                                    </span>
                                                    <span class="badge-modern badge-media-tecnica">
                                                        <i class="fa fa-bookmark"></i> Media Técnica
                                                    </span>
                                                </div>

                                                <!-- Información del docente -->
                                                <div class="info-item">
                                                    <i class="fa fa-user"></i>
                                                    <span><strong><?=$frases[28][$datosUsuarioActual['uss_idioma']];?>:</strong> <?=UsuariosPadre::nombreCompletoDelUsuario($cargaMediaTecnica);?></span>
                                                </div>

                                                <!-- Progreso de notas -->
                                                <?php if($datosUsuarioActual['uss_bloqueado'] != 1 && $config['conf_sin_nota_numerica'] != 1 && $config['conf_mostrar_calificaciones_estudiantes'] == 1 && $porcentajeActual > 0){ ?>
                                                <div class="progress-section">
                                                    <div class="progress-label">
                                                        <span>Progreso de Calificaciones</span>
                                                        <span><strong><?= round($porcentajeActual, 1); ?>%</strong></span>
                                                    </div>
                                                    <div class="progress-bar-modern">
                                                        <div class="progress-fill <?= $progressClassMT; ?>" 
                                                             style="width: <?= min($porcentajeActual, 100); ?>%"></div>
                                                    </div>
                                                </div>
                                                <?php } ?>

                                                <!-- Definitiva -->
                                                <?php if($datosUsuarioActual['uss_bloqueado'] != 1 && $config['conf_sin_nota_numerica'] != 1 && $config['conf_mostrar_calificaciones_estudiantes'] == 1 && !empty($definitivaFinalMT)){ ?>
                                                <div class="definitiva-display">
                                                    <div class="definitiva-label">Nota Definitiva</div>
                                                    <div class="definitiva-value" style="color: <?=$colorDefinitivaDisplayMT;?>">
                                                        <?=$definitivaFinalMT;?>
                                                    </div>
                                                </div>
                                                <?php } ?>

                                                <!-- Último acceso -->
                                                <div class="ultimo-acceso">
                                                    <i class="fa fa-clock-o"></i> 
                                                    <?=$ultimoAcceso;?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                    }
                                } else {
                                    echo '
                                    <div class="col-12">
                                        <div class="alert alert-danger">
                                            <i class="fa fa-exclamation-circle"></i> <strong>INFORMACIÓN:</strong> El curso de <b>' . $cursoMediaTecnica["gra_nombre"] . '</b> no tiene cargas académicas asignadas aún.
                                        </div>
                                    </div>
                                    ';
                                } 
                                ?>
                            </div>
                            <?php 
                            }
                            ?>
                        </div>
                        <?php 
                        }
                        ?>
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
        // FUNCIONALIDAD DE BÚSQUEDA
        // ============================================
        
        const searchInput = document.getElementById('searchInput');
        const cargasContainer = document.getElementById('cargas-container');
        const emptyState = document.getElementById('emptyState');
        const allCards = document.querySelectorAll('.carga-item');
        
        let currentSearchTerm = '';
        
        // Función de búsqueda
        function searchCargas() {
            let visibleCount = 0;
            
            if (allCards) {
                allCards.forEach(card => {
                    const materia = card.getAttribute('data-materia') || '';
                    const docente = card.getAttribute('data-docente') || '';
                    
                    // Verificar búsqueda
                    const searchText = currentSearchTerm.toLowerCase();
                    const matchesSearch = !searchText || 
                        materia.includes(searchText) || 
                        docente.includes(searchText);
                    
                    // Mostrar/ocultar
                    if (matchesSearch) {
                        card.style.display = '';
                        card.style.animation = 'fadeInUp 0.5s ease-out';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            // Mostrar estado vacío si no hay resultados
            if (emptyState) {
                if (visibleCount === 0 && currentSearchTerm !== '') {
                    emptyState.classList.add('show');
                    if (cargasContainer) cargasContainer.style.minHeight = '0';
                } else {
                    emptyState.classList.remove('show');
                    if (cargasContainer) cargasContainer.style.minHeight = 'auto';
                }
            }
        }
        
        // Event listener para búsqueda
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                currentSearchTerm = e.target.value;
                searchCargas();
            });
        }
        
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
                searchCargas();
                searchInput.blur();
            }
        });
        
        console.log('✨ Sistema de cargas académicas para estudiantes cargado correctamente');
        if (allCards) {
            console.log('📚 Total de cargas:', allCards.length);
        }
        
        // ============================================
        // SCROLL AUTOMÁTICO A CARGAS ACADÉMICAS
        // ============================================
        $(document).ready(function() {
            // Esperar un momento para que la página se cargue completamente
            setTimeout(function() {
                const cargasContainer = document.getElementById('cargas-container');
                const searchFilterContainer = document.querySelector('.search-filter-container');
                
                // Si existe el contenedor de cargas o el buscador, hacer scroll hacia ahí
                const targetElement = searchFilterContainer || cargasContainer;
                
                if (targetElement) {
                    // Calcular posición considerando cualquier header fijo
                    const headerOffset = 80; // Altura aproximada de headers fijos
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    // Hacer scroll suave
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }, 300); // Pequeño delay para asegurar que todo esté renderizado
        });
    </script>
  </body>

</html>