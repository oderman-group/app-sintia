<?php include("session.php");?>
<?php //include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'AC0005';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
	<!-- full calendar -->
    <link href="../../config-general/assets/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
<?php
require_once("../class/Estudiantes.php");
require_once("../compartido/sintia-funciones.php");
?>
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

    /* Header Moderno */
    .estudiantes-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a252f 100%);
        border-radius: 15px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
        color: white;
    }

    .estudiantes-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .estudiantes-header p {
        margin: 8px 0 0 0;
        opacity: 0.9;
        font-size: 15px;
    }

    /* Buscador Moderno */
    .search-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
    }

    .search-input-wrapper {
        position: relative;
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

    /* Cards de Estudiantes */
    .estudiantes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }

    .estudiante-card {
        background: white;
        border-radius: 16px;
        overflow: visible;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        border: 2px solid transparent;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }

    .estudiante-card:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-5px);
        border-color: var(--secondary-color);
    }

    .estudiante-card:has(.dropdown.show) {
        z-index: 1000;
    }

    .estudiante-card-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
        padding: 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 15px;
        position: relative;
    }

    .estudiante-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,0.3);
        flex-shrink: 0;
        background: rgba(255,255,255,0.1);
    }

    .estudiante-info-header {
        flex: 1;
        min-width: 0;
    }

    .estudiante-nombre {
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 5px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .estudiante-grado-grupo {
        font-size: 14px;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .estudiante-badge-warning {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(243, 156, 18, 0.9);
        color: white;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        z-index: 10;
    }

    .estudiante-card-body {
        padding: 20px;
    }

    .estudiante-info-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        font-size: 14px;
        color: #555;
    }

    .estudiante-info-row:last-child {
        margin-bottom: 0;
    }

    .estudiante-info-row i {
        color: var(--secondary-color);
        width: 20px;
        text-align: center;
        font-size: 16px;
    }

    .estudiante-info-label {
        font-weight: 600;
        color: #7f8c8d;
        min-width: 80px;
    }

    .estudiante-info-value {
        color: var(--primary-color);
        flex: 1;
    }

    .estudiante-card-footer {
        padding: 15px 20px;
        background: #f8f9fa;
        border-top: 1px solid #e0e6ed;
        display: flex;
        gap: 10px;
        position: relative;
        z-index: 10;
    }

    .estudiante-card-body {
        overflow: visible;
    }

    .estudiante-card-header {
        overflow: visible;
    }

    .btn-action-card {
        flex: 1;
        padding: 10px 15px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-action-primary {
        background: linear-gradient(135deg, var(--secondary-color), #35a39d);
        color: white;
    }

    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(65, 193, 186, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-action-secondary {
        background: white;
        color: var(--primary-color);
        border: 2px solid #e0e6ed;
    }

    .btn-action-secondary:hover {
        background: #f8f9fa;
        border-color: var(--secondary-color);
        color: var(--secondary-color);
        transform: translateY(-2px);
        text-decoration: none;
    }

    /* Dropdown de Acciones */
    .dropdown-actions {
        position: static;
        display: inline-block;
    }

    .estudiante-card .dropdown-actions {
        position: relative;
    }

    .dropdown-menu-modern {
        min-width: 250px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border: none;
        padding: 8px 0;
        margin-top: 5px;
        z-index: 1001 !important;
        position: absolute !important;
    }

    .dropdown.show .dropdown-menu-modern {
        display: block !important;
    }

    .estudiante-card .dropdown-menu-modern {
        right: 0;
        left: auto;
    }

    .dropdown-menu-modern li {
        margin: 0;
    }

    .dropdown-menu-modern a {
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #555;
        transition: var(--transition);
        text-decoration: none;
        font-size: 14px;
    }

    .dropdown-menu-modern a:hover {
        background: #f8f9fa;
        color: var(--secondary-color);
        padding-left: 25px;
    }

    .dropdown-menu-modern a i {
        width: 20px;
        text-align: center;
        color: var(--secondary-color);
    }

    /* Estado Vacío */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
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

    /* Alerta de Solicitud */
    .alert-solicitud {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-left: 4px solid #f39c12;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .alert-solicitud i {
        font-size: 24px;
        color: #f39c12;
    }

    /* Estado Bloqueado */
    .estado-bloqueado {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
        border-left: 4px solid #e74c3c;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
    }

    .estado-bloqueado .btn {
        margin-top: 10px;
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

    .estudiante-card {
        animation: fadeInUp 0.5s ease-out backwards;
    }

    .estudiante-card:nth-child(1) { animation-delay: 0.1s; }
    .estudiante-card:nth-child(2) { animation-delay: 0.15s; }
    .estudiante-card:nth-child(3) { animation-delay: 0.2s; }
    .estudiante-card:nth-child(4) { animation-delay: 0.25s; }
    .estudiante-card:nth-child(5) { animation-delay: 0.3s; }
    .estudiante-card:nth-child(6) { animation-delay: 0.35s; }

    /* Responsive */
    @media (max-width: 768px) {
        .estudiantes-grid {
            grid-template-columns: 1fr;
        }

        .estudiantes-header {
            padding: 20px;
        }

        .estudiantes-header h1 {
            font-size: 22px;
        }

        .estudiante-card-header {
            flex-direction: column;
            text-align: center;
        }

        .estudiante-card-footer {
            flex-direction: column;
        }

        .btn-action-card {
            width: 100%;
        }

        .estudiante-card .dropdown-menu-modern {
            left: 0;
            right: auto;
            min-width: 200px;
        }
    }
    
    /* Modal de Cronograma */
    .modal-cronograma .modal-dialog {
        max-width: 95%;
        width: 95%;
        margin: 20px auto;
        height: 90vh;
    }
    .modal-cronograma .modal-content {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .modal-cronograma .modal-header {
        flex-shrink: 0;
    }
    .modal-cronograma .modal-body {
        flex: 1;
        overflow: hidden;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }
    .modal-cronograma .modal-footer {
        flex-shrink: 0;
    }
    #calendarModal {
        flex: 1;
        min-height: 500px;
        width: 100%;
    }
    
    /* Asegurar que FullCalendar se renderice correctamente */
    .modal-cronograma .fc {
        direction: ltr;
        text-align: left;
    }
    
    .modal-cronograma .fc table {
        border-collapse: collapse;
        border-spacing: 0;
    }
    
    .modal-cronograma .fc-header {
        margin-bottom: 1em;
    }
    
    .modal-cronograma .fc-button {
        position: relative;
        display: inline-block;
        padding: 0 .6em;
        overflow: hidden;
        line-height: 1.9em;
        white-space: nowrap;
        cursor: pointer;
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
                    
                    <!-- Formularios de Solicitud -->
                    <?php if(!empty($_GET["req"]) && $_GET["req"]==1 && Modulos::verificarModulosDeInstitucion(Modulos::MODULO_USUARIOS)){?>
                        <div class="alert-solicitud">
                            <i class="fa fa-exclamation-triangle"></i>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 10px 0; color: #856404;"><?=$frases[269][$datosUsuarioActual['uss_idioma']];?></h4>
                                <p style="margin: 0 0 15px 0; color: #856404;"><?=$frases[273][$datosUsuarioActual['uss_idioma']];?></p>
                                <form class="form-horizontal" action="solicitud-desbloqueo.php" method="post">
                                    <input type="hidden" name="idRecurso" value="<?=base64_decode($_GET["idE"]);?>">
                                    <div class="form-group">
                                        <textarea name="contenido" class="form-control" rows="3" placeholder="<?=$frases[274][$datosUsuarioActual['uss_idioma']];?>" required style="border-radius: 8px; resize: none;"></textarea>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <button type="submit" class="btn btn-warning" style="border-radius: 8px;">
                                            <i class="fa fa-paper-plane"></i> <?=$frases[271][$datosUsuarioActual['uss_idioma']];?>
                                        </button>
                                        <a href="<?=$_SERVER['PHP_SELF'];?>" class="btn btn-default" style="border-radius: 8px;">
                                            <i class="fa fa-times"></i> <?=$frases[171][$datosUsuarioActual['uss_idioma']];?>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php }?>
                    
                    <?php if(!empty($_GET["req"]) && $_GET["req"]==2 && Modulos::verificarModulosDeInstitucion(Modulos::MODULO_RESERVA_CUPO)){?>
                        <div class="alert-solicitud" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left-color: #28a745;">
                            <i class="fa fa-check-circle"></i>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 10px 0; color: #155724;"><?=$frases[277][$datosUsuarioActual['uss_idioma']];?></h4>
                                <p style="margin: 0 0 15px 0; color: #155724;"><?=$frases[278][$datosUsuarioActual['uss_idioma']];?></p>
                                <form name="formularioCupo" class="form-horizontal" action="encuesta-reservar-cupo.php" method="post">
                                    <input type="hidden" name="idEstudiante" value="<?=base64_decode($_GET["idE"]);?>">
                                    <div class="form-group">
                                        <div class="form-check" style="margin-bottom: 10px;">
                                            <input type="radio" name="respuesta" value="1" id="cupoSii" onClick="cupoNo(1)" class="form-check-input" />
                                            <label class="form-check-label" for="cupoSii" style="color: #155724; font-weight: 500;"><?=$frases[275][$datosUsuarioActual['uss_idioma']];?></label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="respuesta" value="2" id="cupoNoo" onClick="cupoNo(2)" class="form-check-input" />
                                            <label class="form-check-label" for="cupoNoo" style="color: #155724; font-weight: 500;"><?=$frases[276][$datosUsuarioActual['uss_idioma']];?></label>
                                        </div>
                                    </div>
                                    <div id="motivoNo" style="display: none; margin-top: 15px;">
                                        <p style="color: #155724; margin-bottom: 10px;"><?=$frases[279][$datosUsuarioActual['uss_idioma']];?></p>
                                        <textarea name="motivo" class="form-control" rows="3" placeholder="<?=$frases[280][$datosUsuarioActual['uss_idioma']];?>..." required style="border-radius: 8px; resize: none;"></textarea>
                                    </div>
                                    <div class="form-group" style="margin-top: 15px; margin-bottom: 0;">
                                        <button type="submit" class="btn btn-success" style="border-radius: 8px;">
                                            <i class="fa fa-check"></i> <?=$frases[272][$datosUsuarioActual['uss_idioma']];?>
                                        </button>
                                        <a href="<?=$_SERVER['PHP_SELF'];?>" class="btn btn-default" style="border-radius: 8px;">
                                            <i class="fa fa-times"></i> <?=$frases[171][$datosUsuarioActual['uss_idioma']];?>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php }?>

                    <!-- Header -->
                    <div class="estudiantes-header">
                        <h1>
                            <i class="fa fa-users"></i>
                            <?=$frases[71][$datosUsuarioActual['uss_idioma']];?>
                        </h1>
                        <p>Gestiona y consulta la información de tus acudidos</p>
                        <?php include("../compartido/texto-manual-ayuda.php"); ?>
                    </div>

                    <?php 
                    $consulta = Estudiantes::listarEstudiantesParaAcudientes($datosUsuarioActual['uss_id']);
                    $numEstudiantes = mysqli_num_rows($consulta);
                    
                    if($numEstudiantes > 0) { 
                        mysqli_data_seek($consulta, 0);
                    ?>
                        <!-- Buscador -->
                        <div class="search-container">
                            <div class="search-input-wrapper">
                                <input 
                                    type="text" 
                                    id="searchInput" 
                                    class="search-input-modern" 
                                    placeholder="Buscar estudiante por nombre, usuario o grado..."
                                    autocomplete="off"
                                >
                                <i class="fa fa-search search-icon"></i>
                            </div>
                        </div>

                        <!-- Grid de Estudiantes -->
                        <div class="estudiantes-grid" id="estudiantes-grid">
                            <?php
                            $contReg = 1;
                            while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                $genero = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_id='".$resultado['mat_genero']."'"), MYSQLI_BOTH);

                                $aspectos1 = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
                                    WHERE dn_cod_estudiante='" . $resultado['mat_id'] . "' AND dn_periodo=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}"), MYSQLI_BOTH);

                                $aspectos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
                                    WHERE dn_cod_estudiante='" . $resultado['mat_id'] . "' AND dn_periodo='" . $config['conf_periodo'] . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}"), MYSQLI_BOTH);

                                $numReportesDis = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disciplina_reportes dr
                                    INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat.mat_id_usuario=dr.dr_estudiante AND mat.mat_acudiente='".$_SESSION["id"]."' AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
                                    WHERE dr.dr_aprobacion_acudiente=0 AND dr.institucion={$config['conf_id_institucion']} AND dr.year={$_SESSION["bd"]}
                                    AND dr.dr_estudiante='".$resultado['mat_id_usuario']."'"));

                                // Obtener foto del estudiante
                                $fotoEstudiante = '';
                                if(!empty($resultado['mat_foto']) && file_exists(ROOT_PATH.'/main-app/files/fotos/'.$resultado['mat_foto'])){
                                    $fotoEstudiante = BASE_URL.'/main-app/files/fotos/'.$resultado['mat_foto'];
                                } else {
                                    $fotoEstudiante = BASE_URL.'/main-app/files/fotos/default.png';
                                }

                                $nombreCompleto = Estudiantes::NombreCompletoDelEstudiante($resultado);
                                
                                // Determinar estado del estudiante
                                $respuesta = 0;
                                if($config['conf_activar_encuesta']==1){
                                    $respuesta = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_encuestas 
                                    WHERE genc_estudiante='".$resultado['mat_id']."' AND genc_institucion={$config['conf_id_institucion']} AND genc_year={$_SESSION["bd"]}"));
                                }
                                
                                $estudianteBloqueado = ($resultado['uss_bloqueado'] == 1);
                                $acudienteBloqueado = ($datosUsuarioActual['uss_bloqueado'] == 1);
                                $mostrarAcciones = ($config['conf_activar_encuesta']!=1 or $respuesta>0) && !$acudienteBloqueado && !$estudianteBloqueado;
                            ?>
                                <div class="estudiante-card" 
                                     data-nombre="<?= strtolower($nombreCompleto); ?>"
                                     data-usuario="<?= strtolower($resultado['uss_usuario']); ?>"
                                     data-grado="<?= strtolower($resultado['gra_nombre']); ?>"
                                     data-grupo="<?= strtolower($resultado['gru_nombre']); ?>">
                                    
                                    <!-- Header de la Card -->
                                    <div class="estudiante-card-header">
                                        <?php if($numReportesDis > 0) { ?>
                                            <div class="estudiante-badge-warning" data-toggle="tooltip" title="Reporte disciplinario pendiente por firmar (<?=$numReportesDis;?>)">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                <?=$numReportesDis;?> Pendiente
                                            </div>
                                        <?php } ?>
                                        
                                        <img src="<?=$fotoEstudiante;?>" 
                                             alt="<?=$nombreCompleto;?>" 
                                             class="estudiante-avatar"
                                             onerror="this.onerror=null; this.src='<?=BASE_URL;?>/main-app/files/fotos/default.png';">
                                        
                                        <div class="estudiante-info-header">
                                            <h3 class="estudiante-nombre"><?=$nombreCompleto;?></h3>
                                            <p class="estudiante-grado-grupo">
                                                <i class="fa fa-graduation-cap"></i>
                                                <?=strtoupper($resultado['gra_nombre']." ".$resultado['gru_nombre']);?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Body de la Card -->
                                    <div class="estudiante-card-body">
                                        <div class="estudiante-info-row">
                                            <i class="fa fa-user"></i>
                                            <span class="estudiante-info-label">Usuario:</span>
                                            <span class="estudiante-info-value"><?=$resultado['uss_usuario'];?></span>
                                        </div>
                                        
                                        <div class="estudiante-info-row">
                                            <i class="fa fa-id-card"></i>
                                            <span class="estudiante-info-label">ID Matrícula:</span>
                                            <span class="estudiante-info-value"><?=$resultado['mat_id'];?></span>
                                        </div>
                                        
                                        <?php if(!empty($genero[1])) { ?>
                                        <div class="estudiante-info-row">
                                            <i class="fa fa-<?=$resultado['mat_genero']==1 ? 'mars' : 'venus';?>"></i>
                                            <span class="estudiante-info-label">Género:</span>
                                            <span class="estudiante-info-value"><?=$genero[1];?></span>
                                        </div>
                                        <?php } ?>
                                    </div>

                                    <!-- Footer con Acciones -->
                                    <div class="estudiante-card-footer">
                                        <?php if($mostrarAcciones) { ?>
                                            <?php if(!empty($resultado['mat_id_usuario'])) { 
                                                // Determinar la primera acción principal (Calificaciones)
                                                $accionPrincipal = '';
                                                $accionPrincipalLink = '#';
                                                $accionPrincipalIcon = 'fa-graduation-cap';
                                                
                                                if(array_key_exists(Modulos::MODULO_CALIFICACIONES, $arregloModulos) && 
                                                   Modulos::verificarModulosDeInstitucion(Modulos::MODULO_CALIFICACIONES) && 
                                                   $config['conf_calificaciones_acudientes']==1) {
                                                    if($config['conf_sin_nota_numerica']!=1){
                                                        $accionPrincipal = $frases[84][$datosUsuarioActual['uss_idioma']];
                                                        $accionPrincipalLink = 'periodos-resumen.php?usrEstud='.base64_encode($resultado['mat_id_usuario']);
                                                    } else {
                                                        $accionPrincipal = $frases[242][$datosUsuarioActual['uss_idioma']];
                                                        $accionPrincipalLink = 'notas-actuales.php?usrEstud='.base64_encode($resultado['mat_id_usuario']);
                                                    }
                                                }
                                            ?>
                                                <?php if($accionPrincipal != '') { ?>
                                                <a href="<?=$accionPrincipalLink;?>" class="btn-action-card btn-action-primary">
                                                    <i class="fa <?=$accionPrincipalIcon;?>"></i>
                                                    <?=$accionPrincipal;?>
                                                </a>
                                                <?php } ?>
                                                
                                                <div class="dropdown dropdown-actions">
                                                    <button type="button" class="btn-action-card btn-action-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
                                                        <i class="fa fa-ellipsis-h"></i>
                                                        Más
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-modern" role="menu" aria-labelledby="dropdownMenuButton">
                                                        <?php 
                                                        if(!empty($resultado['mat_id_usuario'])) {
                                                            if(array_key_exists(Modulos::MODULO_CALIFICACIONES, $arregloModulos) && 
                                                               Modulos::verificarModulosDeInstitucion(Modulos::MODULO_CALIFICACIONES) && 
                                                               $config['conf_calificaciones_acudientes']==1) {?>
                                                                <?php if($config['conf_sin_nota_numerica']!=1){?>
                                                                    <li><a href="periodos-resumen.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>">
                                                                        <i class="fa fa-calendar-alt"></i> <?=$frases[84][$datosUsuarioActual['uss_idioma']];?>
                                                                    </a></li>
                                                                <?php }?>
                                                                <li><a href="notas-actuales.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>">
                                                                    <i class="fa fa-file-alt"></i> <?=$frases[242][$datosUsuarioActual['uss_idioma']];?>
                                                                </a></li>
                                                            <?php }?>

                                                            <?php if (array_key_exists(Modulos::MODULO_DISCIPLINARIO, $arregloModulos) && Modulos::verificarModulosDeInstitucion(Modulos::MODULO_DISCIPLINARIO)) { ?>
                                                            <li><a href="reportes-disciplinarios.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>">
                                                                <i class="fa fa-exclamation-triangle"></i> <?=$frases[105][$datosUsuarioActual['uss_idioma']];?>
                                                            </a></li>
                                                            <li><a href="aspectos.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>&periodo=<?=base64_encode($config[2]);?>">
                                                                <i class="fa fa-list-check"></i> <?=$frases[264][$datosUsuarioActual['uss_idioma']];?>
                                                            </a></li>
                                                            <?php }?>
                                                            
                                                            <?php 
                                                            if($config['conf_permiso_descargar_boletin'] == 1 && Modulos::verificarModulosDeInstitucion(Modulos::MODULO_INFORMES_ACADEMICOS_BASICOS)){
                                                                if(!empty($aspectos1["dn_aprobado"]) && !empty($aspectos["dn_aprobado"]) && $aspectos1["dn_aprobado"] == 1 and $aspectos["dn_aprobado"] == 1){ 
                                                            ?>
                                                            <li><a href="../compartido/matricula-boletin-curso-<?=$resultado['gra_formato_boletin'];?>.php?id=<?=base64_encode($resultado["mat_id"]);?>&periodo=<?=base64_encode($config[2]);?>" target="_blank">
                                                                <i class="fa fa-file-pdf"></i> <?=$frases[267][$datosUsuarioActual['uss_idioma']];?>
                                                            </a></li>
                                                            <?php }}?>

                                                            <?php if($config['conf_informe_parcial']==1 && Modulos::verificarModulosDeInstitucion(Modulos::MODULO_INFORMES_ACADEMICOS_BASICOS)){?>
                                                            <li><a href="../compartido/informe-parcial.php?estudiante=<?=base64_encode($resultado["mat_id"]);?>&acu=1" target="_blank">
                                                                <i class="fa fa-file-alt"></i> <?=$frases[265][$datosUsuarioActual['uss_idioma']];?>
                                                            </a></li>
                                                            <?php }?>

                                                            <?php if( $config['conf_ficha_estudiantil']==1 && !empty($resultado['mat_id_usuario']) ){?>
                                                            <li><a href="ficha-estudiantil.php?idR=<?=base64_encode($resultado["mat_id_usuario"]);?>">
                                                                <i class="fa fa-id-card"></i> <?=$frases[266][$datosUsuarioActual['uss_idioma']];?>
                                                            </a></li>
                                                            <?php }?>

                                                            <?php if( !isset($_SESSION['admin']) && !empty($resultado['mat_id_usuario']) ){?>
                                                            <li><a href="auto-login.php?user=<?=base64_encode($resultado['mat_id_usuario']);?>">
                                                                <i class="fa fa-sign-in-alt"></i> Autologin
                                                            </a></li>
                                                            <?php }?>

                                                            <?php if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_ADJUNTAR_DOCUMENTOS)) {?>
                                                            <li><a href="matriculas-adjuntar-documentos.php?id=<?= base64_encode($resultado['mat_id_usuario']); ?>&idMatricula=<?= base64_encode($resultado['mat_id']); ?>">
                                                                <i class="fa fa-paperclip"></i> <?=$frases[434][$datosUsuarioActual['uss_idioma']];?>
                                                            </a></li>
                                                            <?php }?>
                                                            
                                                            <?php if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_CRONOGRAMA)) {?>
                                                            <li><a href="#" onclick="abrirCronogramaModal('<?=base64_encode($resultado['mat_id_usuario']);?>', '<?=addslashes($nombreCompleto);?>'); return false;">
                                                                <i class="fa fa-calendar-alt"></i> Ver Cronograma
                                                            </a></li>
                                                            <?php }?>
                                                        <?php }?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        <?php } else if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_USUARIOS) && $estudianteBloqueado) {
                                            $consultaSolicitudes = mysqli_query($conexion, "SELECT * FROM ".BD_GENERAL.".general_solicitudes 
                                                LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=soli_remitente AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
                                                WHERE soli_institucion='".$config['conf_id_institucion']."' 
                                                AND soli_year='".$_SESSION["bd"]."' AND soli_id_recurso='{$resultado['mat_id_usuario']}' AND soli_estado!=3");
                                            $solicitudPendiente = mysqli_fetch_array($consultaSolicitudes, MYSQLI_BOTH);
                                            
                                            if(!empty($solicitudPendiente)) {
                                                global $estadosSolicitudes;
                                        ?>
                                                <div class="estado-bloqueado" style="flex: 1; margin-top: 0;">
                                                    <strong style="color: #c62828;">Estado del estudiante:</strong><br>
                                                    <span style="color: #1565c0;">Solicitud de desbloqueo <b><?=$estadosSolicitudes[$solicitudPendiente['soli_estado']];?></b></span>
                                                </div>
                                        <?php } else { ?>
                                                <div class="estado-bloqueado" style="flex: 1; margin-top: 0;">
                                                    <strong style="color: #c62828;"><?=$frases[268][$datosUsuarioActual['uss_idioma']];?></strong><br>
                                                    <a href="<?=$_SERVER['PHP_SELF'];?>?req=1&idE=<?=base64_encode($resultado['mat_id_usuario']);?>&nameE=<?=base64_encode($resultado['uss_nombre']);?>" 
                                                       class="btn btn-warning" style="margin-top: 10px; border-radius: 8px;">
                                                        <i class="fa fa-unlock"></i> <?=$frases[269][$datosUsuarioActual['uss_idioma']];?>
                                                    </a>
                                                </div>
                                        <?php }
                                        } else if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_RESERVA_CUPO) && $respuesta == 0) { ?>
                                                <div class="estado-bloqueado" style="flex: 1; margin-top: 0; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left-color: #2196f3;">
                                                    <strong style="color: #1565c0;">Reserva de cupo pendiente</strong><br>
                                                    <a href="<?=$_SERVER['PHP_SELF'];?>?req=2&idE=<?=base64_encode($resultado['mat_id']);?>&nameE=<?=base64_encode($resultado['uss_nombre']);?>" 
                                                       class="btn btn-info" style="margin-top: 10px; border-radius: 8px;">
                                                        <i class="fa fa-check-circle"></i> <?=$frases[270][$datosUsuarioActual['uss_idioma']];?>
                                                    </a>
                                                </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php 
                                $contReg++;
                            }
                            ?>
                        </div>

                        <!-- Estado Vacío para Búsqueda -->
                        <div class="empty-state" id="emptyState">
                            <div class="empty-state-icon">
                                <i class="fa fa-search"></i>
                            </div>
                            <div class="empty-state-title">No se encontraron estudiantes</div>
                            <div class="empty-state-text">Intenta con otros términos de búsqueda</div>
                        </div>
                    <?php } else { ?>
                        <!-- Estado Vacío cuando no hay estudiantes -->
                        <div class="empty-state show">
                            <div class="empty-state-icon">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="empty-state-title">No tienes estudiantes registrados</div>
                            <div class="empty-state-text">Contacta con la institución para asociar estudiantes a tu cuenta</div>
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
    
    <!-- Modal para Cronograma del Estudiante -->
    <div class="modal fade modal-cronograma" id="modalCronograma" tabindex="-1" role="dialog" aria-labelledby="modalCronogramaLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalCronogramaLabel">
                        <i class="fa fa-calendar-alt"></i> Cronograma de <span id="nombreEstudianteModal"></span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="calendarModal" class="has-toolbar"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para Detalles de Actividad -->
    <div class="modal fade" id="modalDetalleActividad" tabindex="-1" role="dialog" aria-labelledby="modalDetalleActividadLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h4 class="modal-title" id="modalDetalleActividadLabel">
                        <i class="fa fa-info-circle"></i> Detalles de la Actividad
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="contenidoDetalleActividad" style="padding: 25px;">
                    <div class="text-center" style="padding: 50px;">
                        <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                        <p style="margin-top: 20px; color: #666;">Cargando información...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <!-- calendar -->
    <script src="../../config-general/assets/plugins/moment/moment.min.js" ></script>
    <script src="../../config-general/assets/plugins/fullcalendar/fullcalendar.min.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    
    <script>
        // ============================================
        // FUNCIONALIDAD DE BÚSQUEDA
        // ============================================
        const searchInput = document.getElementById('searchInput');
        const estudiantesGrid = document.getElementById('estudiantes-grid');
        const emptyState = document.getElementById('emptyState');
        const allCards = document.querySelectorAll('.estudiante-card');
        
        let currentSearchTerm = '';
        
        function searchEstudiantes() {
            let visibleCount = 0;
            
            if (allCards && allCards.length > 0) {
                allCards.forEach(card => {
                    const nombre = card.getAttribute('data-nombre') || '';
                    const usuario = card.getAttribute('data-usuario') || '';
                    const grado = card.getAttribute('data-grado') || '';
                    const grupo = card.getAttribute('data-grupo') || '';
                    
                    // Verificar búsqueda
                    const searchText = currentSearchTerm.toLowerCase();
                    const matchesSearch = !searchText || 
                        nombre.includes(searchText) || 
                        usuario.includes(searchText) ||
                        grado.includes(searchText) ||
                        grupo.includes(searchText);
                    
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
                    if (estudiantesGrid) estudiantesGrid.style.display = 'none';
                } else {
                    emptyState.classList.remove('show');
                    if (estudiantesGrid) estudiantesGrid.style.display = 'grid';
                }
            }
        }
        
        // Event listener para búsqueda
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                currentSearchTerm = e.target.value;
                searchEstudiantes();
            });
        }
        
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
                searchEstudiantes();
                searchInput.blur();
            }
        });
        
        // ============================================
        // FUNCIÓN PARA RESERVA DE CUPO
        // ============================================
        function cupoNo(valor) {
            const motivoNo = document.getElementById('motivoNo');
            if (motivoNo) {
                if (valor == 2) {
                    motivoNo.style.display = 'block';
                } else {
                    motivoNo.style.display = 'none';
                }
            }
        }
        
        // ============================================
        // INICIALIZACIÓN DE TOOLTIPS Y DROPDOWNS
        // ============================================
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
            
            // Manejar z-index de cards cuando se abre un dropdown
            $(document).on('show.bs.dropdown', '.dropdown-actions', function() {
                // Cerrar otros dropdowns abiertos
                $('.dropdown-actions .dropdown').not($(this).closest('.dropdown')).removeClass('show');
                
                // Elevar z-index de la card contenedora
                const card = $(this).closest('.estudiante-card');
                card.css('z-index', '1000');
            });
            
            $(document).on('hide.bs.dropdown', '.dropdown-actions', function() {
                // Restaurar z-index de la card
                const card = $(this).closest('.estudiante-card');
                card.css('z-index', '1');
            });
            
            // Cerrar dropdowns al hacer clic fuera
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown-actions').length) {
                    $('.dropdown-actions .dropdown').removeClass('show');
                    $('.estudiante-card').css('z-index', '1');
                }
            });
        });
        
        console.log('✨ Sistema de estudiantes para acudientes cargado correctamente');
        if (allCards) {
            console.log('👥 Total de estudiantes:', allCards.length);
        }
        
        // ============================================
        // FUNCIÓN PARA ABRIR MODAL DE CRONOGRAMA
        // ============================================
        var calendarioModalInicializado = false;
        var usrEstudActual = ''; // Variable global para almacenar el usrEstud actual
        
        function abrirCronogramaModal(usrEstudEncoded, nombreEstudiante) {
            usrEstudActual = usrEstudEncoded; // Guardar el usrEstud actual
            $('#nombreEstudianteModal').text(nombreEstudiante);
            
            // Limpiar calendario anterior si existe
            if (calendarioModalInicializado && $('#calendarModal').fullCalendar) {
                $('#calendarModal').fullCalendar('destroy');
                calendarioModalInicializado = false;
            }
            
            // Mostrar loading
            $('#calendarModal').html('<div class="text-center" style="padding: 50px;"><i class="fa fa-spinner fa-spin fa-3x"></i><p style="margin-top: 20px;">Cargando calendario...</p></div>');
            
            $('#modalCronograma').modal('show');
            
            // Cargar el calendario cuando el modal esté completamente visible
            $('#modalCronograma').one('shown.bs.modal', function() {
                cargarCalendarioEstudiante(usrEstudEncoded);
            });
            
            // Limpiar cuando se cierre el modal
            $('#modalCronograma').on('hidden.bs.modal', function() {
                if (calendarioModalInicializado && $('#calendarModal').fullCalendar) {
                    $('#calendarModal').fullCalendar('destroy');
                    calendarioModalInicializado = false;
                }
            });
        }
        
        // ============================================
        // FUNCIÓN PARA CARGAR CALENDARIO DEL ESTUDIANTE
        // ============================================
        function cargarCalendarioEstudiante(usrEstudEncoded) {
            console.log('Cargando calendario para estudiante:', usrEstudEncoded);
            
            // Cargar eventos del estudiante vía AJAX
            $.ajax({
                url: '../estudiante/ajax-calendario-estudiante.php',
                type: 'GET',
                data: { usrEstud: usrEstudEncoded },
                dataType: 'json',
                timeout: 30000, // 30 segundos de timeout
                success: function(response) {
                    console.log('Respuesta recibida:', response);
                    
                    if (response && response.success) {
                        if (response.eventos && Array.isArray(response.eventos)) {
                            console.log('Eventos recibidos:', response.eventos.length);
                            
                            // Verificar que los eventos tengan las propiedades necesarias
                            if (response.eventos.length > 0) {
                                console.log('Primer evento de ejemplo:', response.eventos[0]);
                                console.log('¿Tiene URL?', response.eventos[0].url);
                                console.log('¿Tiene tipo?', response.eventos[0].tipo);
                            }
                            
                            inicializarCalendarioModal(response.eventos);
                        } else {
                            console.warn('No hay eventos o el formato es incorrecto:', response);
                            $('#calendarModal').html('<div class="alert alert-info">No hay actividades programadas para este estudiante.</div>');
                        }
                    } else {
                        var mensaje = response && response.message ? response.message : 'No se pudieron cargar los eventos del calendario.';
                        console.error('Error en la respuesta:', mensaje);
                        $('#calendarModal').html('<div class="alert alert-warning">' + mensaje + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });
                    
                    var mensajeError = 'Error al cargar el calendario. ';
                    if (xhr.status === 0) {
                        mensajeError += 'No se pudo conectar al servidor.';
                    } else if (xhr.status === 404) {
                        mensajeError += 'El archivo no se encontró.';
                    } else if (xhr.status === 500) {
                        mensajeError += 'Error del servidor.';
                    } else {
                        mensajeError += 'Por favor, intente nuevamente.';
                    }
                    
                    $('#calendarModal').html('<div class="alert alert-danger">' + mensajeError + '</div>');
                }
            });
        }
        
        // ============================================
        // FUNCIÓN PARA INICIALIZAR CALENDARIO EN MODAL
        // ============================================
        function inicializarCalendarioModal(eventos) {
            // Verificar que FullCalendar esté disponible
            if (typeof jQuery === 'undefined' || !jQuery().fullCalendar) {
                console.error('FullCalendar no está disponible');
                $('#calendarModal').html('<div class="alert alert-danger">FullCalendar no está disponible. Por favor, recargue la página.</div>');
                return;
            }
            
            // Limpiar el contenido anterior
            $('#calendarModal').empty();
            
            // Esperar un momento para asegurar que el DOM esté listo
            setTimeout(function() {
                try {
                    var r = {
                        left: "prev,next,today",
                        center: "title",
                        right: "month,agendaWeek,agendaDay"
                    };
                    
                    // Verificar que eventos sea un array válido
                    if (!Array.isArray(eventos)) {
                        console.error('Los eventos no son un array válido:', eventos);
                        $('#calendarModal').html('<div class="alert alert-warning">No se pudieron cargar los eventos del calendario.</div>');
                        return;
                    }
                    
                    console.log('Inicializando calendario con', eventos.length, 'eventos');
                    
                    $('#calendarModal').fullCalendar({
                        header: r,
                        defaultView: "month",
                        slotMinutes: 15,
                        editable: false,
                        droppable: false,
                        events: eventos,
                        eventClick: function(calEvent, jsEvent, view) {
                            // Prevenir el comportamiento por defecto
                            jsEvent.preventDefault();
                            
                            // Log para depuración
                            console.log('Evento clickeado en FullCalendar:', calEvent);
                            
                            // Abrir modal con detalles en lugar de nueva pestaña
                            mostrarDetalleActividad(calEvent);
                            return false;
                        },
                        eventRender: function(event, element) {
                            // Asegurar que los eventos se rendericen correctamente
                            element.css({
                                'cursor': 'pointer'
                            });
                            
                            // Log para verificar que los eventos tienen URL
                            if (event.url) {
                                console.log('Evento renderizado con URL:', event.title, 'URL:', event.url);
                            } else {
                                console.warn('Evento sin URL:', event.title, 'Tipo:', event.tipo);
                            }
                        }
                    });
                    
            calendarioModalInicializado = true;
            console.log('Calendario inicializado correctamente');
        } catch (error) {
            console.error('Error al inicializar el calendario:', error);
            $('#calendarModal').html('<div class="alert alert-danger">Error al inicializar el calendario: ' + error.message + '</div>');
        }
    }, 100);
        }
        
        // ============================================
        // FUNCIÓN PARA MOSTRAR DETALLES DE ACTIVIDAD
        // ============================================
        function mostrarDetalleActividad(calEvent) {
            console.log('Evento clickeado:', calEvent);
            
            // Obtener tipo y detalle del evento
            var tipo = calEvent.tipo || '';
            var detalle = calEvent.detalle || null;
            
            // Si el detalle ya está en el evento, usarlo directamente
            if (detalle) {
                $('#modalDetalleActividadLabel').html('<i class="fa fa-info-circle"></i> ' + (detalle.titulo || calEvent.title || 'Actividad'));
                $('#contenidoDetalleActividad').html('<div class="text-center" style="padding: 50px;"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p style="margin-top: 20px; color: #666;">Cargando información...</p></div>');
                $('#modalDetalleActividad').modal('show');
                
                // Mostrar el contenido inmediatamente (ya está cargado)
                setTimeout(function() {
                    mostrarContenidoDetalle(detalle, tipo);
                }, 100);
            } else {
                // Fallback: si no hay detalle, intentar obtenerlo vía AJAX
                console.warn('El evento no tiene detalle precargado, intentando obtenerlo vía AJAX');
                $('#modalDetalleActividadLabel').html('<i class="fa fa-info-circle"></i> ' + (calEvent.title || 'Actividad'));
                $('#contenidoDetalleActividad').html('<div class="text-center" style="padding: 50px;"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p style="margin-top: 20px; color: #666;">Cargando información...</p></div>');
                $('#modalDetalleActividad').modal('show');
                
                // Intentar obtener detalles vía AJAX como respaldo
                var url = calEvent.url || '';
                var params = {};
                if (url) {
                    try {
                        var urlParts = url.split('?');
                        if (urlParts.length > 1) {
                            var queryString = urlParts[1];
                            var pairs = queryString.split('&');
                            pairs.forEach(function(pair) {
                                var keyValue = pair.split('=');
                                if (keyValue.length >= 2) {
                                    var key = keyValue[0];
                                    var value = decodeURIComponent(keyValue.slice(1).join('=').replace(/\+/g, ' '));
                                    params[key] = value;
                                }
                            });
                        }
                    } catch(e) {
                        console.error('Error al extraer parámetros:', e);
                    }
                }
                
                var usrEstud = usrEstudActual || '';
                if (!params.usrEstud && usrEstud) {
                    params.usrEstud = usrEstud;
                }
                
                $.ajax({
                    url: '../estudiante/ajax-detalle-actividad.php',
                    type: 'GET',
                    data: {
                        tipo: tipo,
                        params: JSON.stringify(params),
                        usrEstud: usrEstud
                    },
                    dataType: 'json',
                    timeout: 15000,
                    success: function(response) {
                        if (response && response.success) {
                            mostrarContenidoDetalle(response.detalle, tipo);
                        } else {
                            var mensaje = response && response.message ? response.message : 'No se pudieron cargar los detalles de la actividad.';
                            $('#contenidoDetalleActividad').html('<div class="alert alert-warning">' + mensaje + '</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', error);
                        $('#contenidoDetalleActividad').html('<div class="alert alert-danger">Error al cargar los detalles. Por favor, intente nuevamente.</div>');
                    }
                });
            }
        }
        
        // ============================================
        // FUNCIÓN PARA MOSTRAR CONTENIDO DEL DETALLE
        // ============================================
        function mostrarContenidoDetalle(detalle, tipo) {
            console.log('Mostrando detalle:', detalle, 'Tipo:', tipo);
            
            var html = '';
            
            html += '<div class="card" style="border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
            html += '<div class="card-body" style="padding: 25px;">';
            
            // Título
            if (detalle.titulo) {
                html += '<h4 style="color: #2d3e50; margin-bottom: 25px; font-weight: 600;">';
                html += '<i class="fa fa-info-circle" style="color: #41c1ba; margin-right: 10px;"></i>';
                html += detalle.titulo;
                html += '</h4>';
            }
            
            // Descripción (si tiene)
            if (detalle.descripcion && detalle.descripcion.trim() !== '') {
                html += '<div class="mb-4">';
                html += '<h6 class="text-muted mb-2" style="font-weight: 600; color: #7f8c8d;">';
                html += '<i class="fa fa-align-left" style="margin-right: 8px;"></i>Descripción:';
                html += '</h6>';
                html += '<p style="background: #f8f9fa; padding: 15px; border-radius: 8px; line-height: 1.6; color: #555; margin: 0;">';
                html += detalle.descripcion;
                html += '</p>';
                html += '</div>';
            }
            
            // Fecha
            if (detalle.fecha && detalle.fecha.trim() !== '') {
                html += '<div class="mb-4">';
                html += '<h6 class="text-muted mb-2" style="font-weight: 600; color: #7f8c8d;">';
                html += '<i class="fa fa-calendar" style="margin-right: 8px;"></i>Fecha:';
                html += '</h6>';
                html += '<p style="background: #f8f9fa; padding: 15px; border-radius: 8px; color: #555; margin: 0; font-size: 16px; font-weight: 500;">';
                html += detalle.fecha;
                html += '</p>';
                html += '</div>';
            }
            
            // Recursos (solo para cronograma)
            if (tipo === 'cronograma' && detalle.recursos && detalle.recursos.trim() !== '') {
                html += '<div class="mb-3">';
                html += '<h6 class="text-muted mb-2" style="font-weight: 600; color: #7f8c8d;">';
                html += '<i class="fa fa-bookmark" style="margin-right: 8px;"></i>Recursos:';
                html += '</h6>';
                html += '<p style="background: #f8f9fa; padding: 15px; border-radius: 8px; color: #555; margin: 0; line-height: 1.6;">';
                html += detalle.recursos;
                html += '</p>';
                html += '</div>';
            }
            
            html += '</div>';
            html += '</div>';
            
            $('#contenidoDetalleActividad').html(html);
        }
    </script>
    <!-- end js include path -->
</body>

</html>