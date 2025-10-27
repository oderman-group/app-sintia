<?php
include("session.php");
$idPaginaInterna = 'DC0067';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("../compartido/head.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");

$valores = Actividades::consultarValores($config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajeRestante = 100 - $valores[0];
include("../compartido/sintia-funciones-js.php");
?>
<title>Resumen de Notas - Vista Rápida</title>

<!-- Modern CSS Framework -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="assets/css/calificaciones-modern.css" rel="stylesheet">

<style>
:root {
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --light-bg: #f8fafc;
    --card-bg: #ffffff;
    --border-color: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

* {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    margin: 0;
    padding: 0;
}

.main-container {
    background: var(--light-bg);
    min-height: 100vh;
    padding: 2rem 0;
}

.header-section {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: var(--text-secondary);
    font-size: 1rem;
    margin-bottom: 0;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-modern {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 500;
    font-size: 0.875rem;
    border: none;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary-modern {
    background: var(--primary-color);
    color: white;
}

.btn-primary-modern:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
    color: white;
}

.btn-secondary-modern {
    background: var(--secondary-color);
    color: white;
}

.btn-secondary-modern:hover {
    background: #475569;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
    color: white;
}

.table-container {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 0;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.table-modern {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    font-size: 0.875rem;
}

.table-modern thead {
    background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
    color: white;
}

.table-modern thead th {
    padding: 1rem 0.75rem;
    font-weight: 600;
    text-align: center;
    border: none;
    position: sticky;
    top: 0;
    z-index: 100;
    font-size: 0.8rem;
    background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
}

.table-modern tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid var(--border-color);
}

.table-modern tbody tr:hover {
    background: #f8fafc;
    transform: scale(1.001);
}

.table-modern tbody tr.highlighted {
    background: #fef3c7;
    border-left: 4px solid var(--warning-color);
}

.table-modern tbody td {
    padding: 0.75rem;
    border: none;
    vertical-align: middle;
}

.student-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border-color);
}

.student-name {
    font-weight: 500;
    color: var(--text-primary);
    margin: 0;
}

.student-id {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin: 0;
}

.inclusion-badge {
    background: var(--primary-color);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 500;
}

.activity-header {
    text-align: center;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.activity-title {
    font-weight: 600;
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
}

.activity-description {
    font-size: 0.7rem;
    opacity: 0.9;
    margin-bottom: 0.25rem;
    line-height: 1.2;
}

.activity-percentage {
    font-size: 0.7rem;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    display: inline-block;
}

.activity-actions {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
    margin-top: 0.5rem;
}

.btn-action {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    border: none;
    font-size: 0.7rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-edit {
    background: var(--success-color);
    color: white;
}

.btn-edit:hover {
    background: #059669;
    transform: scale(1.05);
    color: white;
}

.btn-delete {
    background: var(--danger-color);
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
    transform: scale(1.05);
    color: white;
}

.input-modern {
    width: 100%;
    padding: 0.5rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 0.8rem;
    text-align: center;
    font-weight: 500;
    transition: all 0.2s ease;
    background: white;
}

.input-modern:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.input-modern:disabled {
    background: #f1f5f9;
    color: var(--text-secondary);
    cursor: not-allowed;
}

.grade-cell {
    text-align: center;
    position: relative;
}

.grade-value {
    font-weight: 600;
    font-size: 0.9rem;
}

.grade-qualitative {
    font-size: 0.7rem;
    margin-top: 0.25rem;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    display: inline-block;
}

.grade-actions {
    margin-top: 0.5rem;
    display: flex;
    gap: 0.25rem;
    justify-content: center;
}

.btn-grade-action {
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    border: none;
    font-size: 0.7rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-recovery {
    background: var(--warning-color);
    color: white;
}

.btn-recovery:hover {
    background: #d97706;
    color: white;
}

.btn-remove {
    background: var(--danger-color);
    color: white;
}

.btn-remove:hover {
    background: #dc2626;
    color: white;
}

.summary-section {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 1.5rem;
    margin-top: 2rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
}

.summary-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.summary-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    padding: 1rem;
    border-radius: 12px;
    text-align: center;
    border: 1px solid var(--border-color);
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.responsive-table {
    overflow-x: auto;
    overflow-y: auto;
    max-height: 75vh;
    -webkit-overflow-scrolling: touch;
    position: relative;
}

@media (max-width: 768px) {
    .main-container {
        padding: 1rem 0;
    }
    
    .header-section {
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: stretch;
    }
    
    .table-modern thead th {
        padding: 0.75rem 0.5rem;
        font-size: 0.7rem;
    }
    
    .table-modern tbody td {
        padding: 0.5rem;
    }
    
    .student-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .student-avatar {
        width: 32px;
        height: 32px;
    }
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.notification-area {
    position: fixed;
    top: 2rem;
    right: 2rem;
    z-index: 1000;
    max-width: 400px;
}

.toast-modern {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    box-shadow: var(--shadow-lg);
    padding: 1rem;
    margin-bottom: 1rem;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Animación para actualización de valores */
@keyframes pulseUpdate {
    0%, 100% {
        transform: scale(1);
        background-color: transparent;
    }
    50% {
        transform: scale(1.05);
        background-color: rgba(37, 99, 235, 0.1);
    }
}

.valor-actualizado {
    animation: pulseUpdate 0.6s ease-in-out;
}

/* Estilos mejorados para la fila de promedios */
.fila-promedios {
    background: linear-gradient(135deg, #f0f4ff, #dbe4ff) !important;
    border-top: 4px solid var(--primary-color) !important;
    border-bottom: 4px solid var(--primary-color) !important;
    font-weight: 700 !important;
}

.fila-promedios td {
    padding: 1.25rem 0.75rem !important;
    font-size: 0.95rem !important;
    background: rgba(255, 255, 255, 0.5);
}

.fila-promedios td:first-child {
    background: linear-gradient(90deg, rgba(37, 99, 235, 0.1), transparent);
}

/* Overlay de bloqueo mientras se guarda */
#overlay-guardando-nota {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(30, 41, 59, 0.85);
	z-index: 99999;
	backdrop-filter: blur(6px);
}

.overlay-content-nota {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background: white;
	padding: 45px 50px;
	border-radius: 20px;
	text-align: center;
	box-shadow: 0 20px 60px rgba(0,0,0,0.4);
	min-width: 320px;
}

.overlay-content-nota .spinner {
	width: 70px;
	height: 70px;
	border: 5px solid #e2e8f0;
	border-top-color: #667eea;
	border-radius: 50%;
	margin: 0 auto 25px;
	animation: spin 1s linear infinite;
}

@keyframes spin {
	to { transform: rotate(360deg); }
}

.overlay-content-nota h3 {
	color: #2d3748;
	margin: 0 0 10px 0;
	font-size: 22px;
	font-weight: 700;
}

.overlay-content-nota p {
	color: #718096;
	margin: 0;
	font-size: 15px;
}
</style>

</head>

<body>

<!-- Overlay de bloqueo mientras se guarda la nota -->
<div id="overlay-guardando-nota">
	<div class="overlay-content-nota">
		<div class="spinner"></div>
		<h3>💾 Guardando Nota...</h3>
		<p>Por favor espera, no cierres esta ventana</p>
	</div>
</div>

    <div class="main-container">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="header-section">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="page-title">
                            <i class="fas fa-chart-line me-2"></i>
                            Resumen de Notas
                        </h1>
                        <p class="page-subtitle">
                            Vista rápida de calificaciones - <?=$datosCargaActual['gra_nombre']?> <?=$datosCargaActual['gru_nombre']?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="action-buttons justify-content-md-end">
                            <a href="calificaciones.php?tab=2" class="btn-modern btn-primary-modern">
                                <i class="fas fa-arrow-left"></i>
                                Regresar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Area -->
            <div class="notification-area">
                <span id="respRCT"></span>
            </div>

            <?php 
            // Verificar si el periodo es anterior para que no modifique notas
            $habilitado = 'disabled';
            $deleteOculto = 'style="display:none;"';
            if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){
                $habilitado = '';
                $deleteOculto = 'style="display:block;"';
            }
            ?>

            <!-- Table Container -->
            <div class="table-container">
                <div class="responsive-table">
                    <table class="table-modern" id="tabla_notas">
                        <thead>
                            <tr>
                                <th style="width: 60px;">
                                    <i class="fas fa-hashtag"></i>
                                </th>
                                <th style="width: 80px;">
                                    <i class="fas fa-id-card"></i>
                                    ID
                                </th>
                                <th style="min-width: 250px;">
                                    <i class="fas fa-user-graduate"></i>
                                    <?=$frases[61][$datosUsuarioActual['uss_idioma']];?>
                                </th>
                                <?php
                                $cA = Actividades::traerActividadesCarga($config, $cargaConsultaActual, $periodoConsultaActual);
                                while($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)){
                                    echo '<th style="min-width: 120px;">';
                                    echo '<div class="activity-header">';
                                    echo '<div class="activity-title">';
                                    echo '<a href="calificaciones-editar.php?idR='.base64_encode($rA['act_id']).'" ';
                                    echo 'title="'.$rA['act_descripcion'].'" class="text-white text-decoration-none">';
                                    echo $rA['act_id'];
                                    echo '</a>';
                                    echo '</div>';
                                    echo '<div class="activity-description">'.$rA['act_descripcion'].'</div>';
                                    echo '<div class="activity-percentage">('.$rA['act_valor'].'%)</div>';
                                    echo '</div>';
                                    
                                    echo '<div class="activity-actions">';
                                    echo '<a href="#" ';
                                    echo 'name="calificaciones-eliminar.php?idR='.base64_encode($rA['act_id']).'&idIndicador='.base64_encode($rA['act_id_tipo']).'&carga='.base64_encode($cargaConsultaActual).'&periodo='.base64_encode($periodoConsultaActual).'" ';
                                    echo 'onClick="deseaEliminar(this)" '.$deleteOculto.' ';
                                    echo 'class="btn-action btn-delete" title="Eliminar actividad">';
                                    echo '<i class="fas fa-trash"></i>';
                                    echo '</a>';
                                    echo '</div>';
                                    
                                    echo '<input type="text" ';
                                    echo 'class="input-modern" ';
                                    echo 'placeholder="Nota masiva" ';
                                    echo 'data-tooltip="Colocar la misma nota a todos los estudiantes" ';
                                    echo 'title="0" ';
                                    echo 'name="'.$rA['act_id'].'" ';
                                    echo 'onChange="notasMasiva(this)" ';
                                    echo $habilitado;
                                    echo '>';
                                    echo '</th>';
                                }
                                ?>
                                <th style="width: 80px;">
                                    <i class="fas fa-percentage"></i>
                                    %
                                </th>
                                <th style="width: 100px;">
                                    <i class="fas fa-trophy"></i>
                                    <?=$frases[118][$datosUsuarioActual['uss_idioma']];?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $contReg = 1; 
                            $consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
                            while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                $nombreCompleto = Estudiantes::NombreCompletoDelEstudiante($resultado);
                                
                                // DEFINITIVAS
                                $carga = $cargaConsultaActual;
                                $periodo = $periodoConsultaActual;
                                $estudiante = $resultado['mat_id'];
                                include("../definitivas.php");
                                
                                $colorEstudiante = '#000';
                                if($resultado['mat_inclusion']==1){$colorEstudiante = 'blue';}
                                
                                $highlightClass = '';
                                if(!empty($_GET["idEst"]) && $resultado['mat_id']==$_GET["idEst"]){
                                    $highlightClass = 'highlighted';
                                }
                            ?>
                            <tr class="<?=$highlightClass;?>" id="fila_<?=$resultado['mat_id'];?>">
                                <td class="text-center">
                                    <span class="badge bg-primary"><?=$contReg;?></span>
                                </td>
                                <td class="text-center">
                                    <code><?=$resultado['mat_id'];?></code>
                                </td>
                                <td>
                                    <div class="student-info">
                                        <img src="<?=$usuariosClase->verificarFoto($resultado['uss_foto']);?>" 
                                             class="student-avatar" 
                                             alt="Foto del estudiante">
                                        <div>
                                            <p class="student-name" style="color: <?=$colorEstudiante;?>">
                                                <?=$nombreCompleto?>
                                            </p>
                                            <?php if($resultado['mat_inclusion']==1): ?>
                                                <span class="inclusion-badge">Inclusión</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <?php
                                $cA = Actividades::traerActividadesCarga($config, $cargaConsultaActual, $periodoConsultaActual);
                                while($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)){
                                    // LAS CALIFICACIONES
                                    $notasResultado = Calificaciones::traerCalificacionActividadEstudiante($config, $rA['act_id'], $resultado['mat_id']);
                                    
                                    $arrayEnviar = [
                                        "tipo"=>5, 
                                        "descripcionTipo"=>"Para ocultar la X y limpiar valor, cuando son diferentes actividades.", 
                                        "idInput"=>$resultado['mat_id']."-".$rA['act_id']
                                    ];
                                    $arrayDatos = json_encode($arrayEnviar);
                                    $objetoEnviar = htmlentities($arrayDatos);

                                    if(!empty($notasResultado) && $notasResultado['cal_nota']<$config[5]) $colorNota= $config[6]; 
                                    elseif(!empty($notasResultado) && $notasResultado['cal_nota']>=$config[5]) $colorNota= $config[7]; 
                                    else $colorNota= "black";
                        
                                    $estiloNotaFinal="";
                                    if(!empty($notasResultado) && $config['conf_forma_mostrar_notas'] == CUALITATIVA){		
                                        $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasResultado['cal_nota']);
                                        $estiloNotaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                    }
                                ?>

                                <?php include("td-calificaciones.php");?>

                                <?php		
                                }

                                include("td-porcentaje-definitiva.php");
                                ?>

                            </tr>
                            <?php
                                $contReg++;
                            }
                            ?>
                            
                            <!-- FILA DE PROMEDIOS -->
                            <tr class="fila-promedios" style="background: linear-gradient(135deg, #f8fafc, #e2e8f0); border-top: 3px solid var(--primary-color); font-weight: 600;">
                                <td class="text-center" colspan="3" style="padding: 1rem; font-size: 1rem; color: var(--primary-color);">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    <strong>PROMEDIOS</strong>
                                </td>
                                <?php
                                // Crear celdas vacías para cada actividad
                                $cA = Actividades::traerActividadesCarga($config, $cargaConsultaActual, $periodoConsultaActual);
                                $numActividades = mysqli_num_rows($cA);
                                for ($i = 0; $i < $numActividades; $i++) {
                                    echo '<td class="text-center" style="padding: 0.75rem;">-</td>';
                                }
                                ?>
                                <td class="text-center" style="padding: 0.75rem;">-</td>
                                <td class="text-center" style="padding: 0.75rem;">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="summary-section">
                <h3 class="summary-title">
                    <i class="fas fa-chart-pie me-2"></i>
                    Resumen del Período
                </h3>
                <div class="summary-stats">
                    <div class="stat-card">
                        <div class="stat-value"><?=$contReg-1;?></div>
                        <div class="stat-label">Total Estudiantes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?=mysqli_num_rows($cA);?></div>
                        <div class="stat-label">Actividades</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?=$valores[0];?>%</div>
                        <div class="stat-label">Progreso Total</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?=$porcentajeRestante;?>%</div>
                        <div class="stat-label">Pendiente</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
    <script src="assets/js/calificaciones-modern.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Smooth scrolling for table - NO modificar el transform del thead
            // El sticky position ya maneja esto automáticamente
            
            // Add loading states to buttons
            $('.btn-modern').on('click', function() {
                var $btn = $(this);
                var originalText = $btn.html();
                $btn.html('<span class="loading-spinner"></span> Cargando...');
                $btn.prop('disabled', true);
                
                setTimeout(function() {
                    $btn.html(originalText);
                    $btn.prop('disabled', false);
                }, 2000);
            });
            
            // Enhanced notification system
            function showNotification(message, type = 'info') {
                var icon = 'fas fa-info-circle';
                var bgColor = 'var(--primary-color)';
                
                switch(type) {
                    case 'success':
                        icon = 'fas fa-check-circle';
                        bgColor = 'var(--success-color)';
                        break;
                    case 'warning':
                        icon = 'fas fa-exclamation-triangle';
                        bgColor = 'var(--warning-color)';
                        break;
                    case 'error':
                        icon = 'fas fa-times-circle';
                        bgColor = 'var(--danger-color)';
                        break;
                }
                
                var notification = $(`
                    <div class="toast-modern" style="border-left: 4px solid ${bgColor}">
                        <div class="d-flex align-items-center">
                            <i class="${icon} me-2" style="color: ${bgColor}"></i>
                            <span>${message}</span>
                        </div>
                    </div>
                `);
                
                $('#respRCT').html(notification);
                
                setTimeout(function() {
                    notification.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 5000);
            }
            
            // Override the original notification function
            window.showNotification = showNotification;
            
            // Mejorar la función notasMasiva para asegurar recarga
            var originalNotasMasiva = window.notasMasiva;
            if (originalNotasMasiva) {
                window.notasMasiva = function(enviada) {
                    // Llamar a la función original
                    originalNotasMasiva(enviada);
                    
                    // Asegurar que la página se recargue después de 5 segundos
                    setTimeout(function() {
                        console.log('Recargando página después de nota masiva...');
                        window.location.reload();
                    }, 5000);
                };
            }
            
            // Recalcular promedios después de cargar la página
            setTimeout(function() {
                if (typeof recalcularPromedios === 'function') {
                    console.log('🔄 Calculando promedios iniciales...');
                    recalcularPromedios();
                } else {
                    console.warn('⚠️ Función recalcularPromedios no disponible aún');
                }
            }, 1500);
            
            // También recalcular cuando se cargue completamente la página
            $(window).on('load', function() {
                setTimeout(function() {
                    if (typeof recalcularPromedios === 'function') {
                        console.log('🔄 Recalculando promedios después de carga completa...');
                        recalcularPromedios();
                    }
                }, 800);
            });
            
            // Agregar evento para recalcular al cambiar cualquier input de nota
            $(document).on('change', 'input[data-cod-estudiante][data-valor-nota]', function() {
                const codEst = $(this).attr('data-cod-estudiante');
                console.log('📝 Nota modificada para estudiante:', codEst);
                
                // Recalcular después de que se guarde
                setTimeout(function() {
                    if (typeof recalcularPromedios === 'function') {
                        recalcularPromedios();
                    }
                }, 300);
            });
        });
    </script>
</body>
</html>