<?php
include("session.php");
$idPaginaInterna = 'DT0145';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");

Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
    $disabledPermiso = "disabled";
}

// Determinar paso actual
$pasoActual = 1;
$paso1Completo = false;
$paso2Completo = false;
$paso3Completo = false;

if(!empty($_POST['escogioCursos'])){
    $paso1Completo = true;
    $pasoActual = 2;
}

if(!empty($_POST['relacionoMaterias'])){
    $paso1Completo = true;
    $paso2Completo = true;
    $pasoActual = 3;
}

// Si no relaciona cargas, saltar paso 2
if(!empty($_POST['escogioCursos']) && (empty($_POST['relacionCargas']) || $_POST['relacionCargas'] != 1)){
    $paso1Completo = true;
    $paso2Completo = true; // Se salta
    $pasoActual = 3;
}

$display = "none";
if(!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1){
    $display = "flex";
}

// Obtener nombres de cursos para mostrar
$nombreCursoDesde = '';
$nombreCursoPara = '';
if(!empty($_POST['desde'])){
    $consultaGrado = Grados::obtenerDatosGrados($_POST['desde']);
    $datosGrado = mysqli_fetch_array($consultaGrado, MYSQLI_BOTH);
    $nombreCursoDesde = $datosGrado['gra_nombre'] ?? '';
}
if(!empty($_POST['para'])){
    $consultaGrado = Grados::obtenerDatosGrados($_POST['para']);
    $datosGrado = mysqli_fetch_array($consultaGrado, MYSQLI_BOTH);
    $nombreCursoPara = $datosGrado['gra_nombre'] ?? '';
}

?>
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- SweetAlert2 -->
<link href="../../config-general/assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
<style>
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        position: relative;
    }
    .step-indicator::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e0e0e0;
        z-index: 0;
    }
    .step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: bold;
        transition: all 0.3s;
    }
    .step.active .step-circle {
        background: #2196F3;
        color: white;
        box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.2);
    }
    .step.completed .step-circle {
        background: #4CAF50;
        color: white;
    }
    .step.completed .step-circle::before {
        content: '✓';
        font-size: 20px;
    }
    .step-label {
        font-size: 12px;
        color: #666;
        font-weight: 500;
    }
    .step.active .step-label {
        color: #2196F3;
        font-weight: 600;
    }
    .step-content {
        display: none;
    }
    .step-content.active {
        display: block;
        animation: fadeIn 0.3s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .info-card {
        background: #f8f9fa;
        border-left: 4px solid #2196F3;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    .warning-card {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    .success-card {
        background: #d4edda;
        border-left: 4px solid #28a745;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    .btn-step {
        transition: all 0.3s ease;
    }
    .btn-step:disabled,
    .btn-step.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
    .btn-step {
        min-width: 120px;
        padding: 10px 20px;
        font-weight: 500;
    }
    .btn-back {
        background: #6c757d;
        border-color: #6c757d;
        color: white;
    }
    .btn-back:hover {
        background: #5a6268;
        border-color: #545b62;
        color: white;
    }
    .summary-box {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .summary-item:last-child {
        border-bottom: none;
    }
    .summary-label {
        font-weight: 600;
        color: #495057;
    }
    .summary-value {
        color: #2196F3;
        font-weight: 500;
    }
    /* Estilos para tabla de estudiantes - Paso 3 */
    #tablaEstudiantes_wrapper {
        width: 100%;
    }
    #tablaEstudiantes thead th:nth-child(4) {
        min-width: 300px !important;
    }
    #tablaEstudiantes tbody td:nth-child(4) {
        white-space: nowrap;
        min-width: 300px !important;
        max-width: none !important;
    }
    #tablaEstudiantes thead th:nth-child(5),
    #tablaEstudiantes tbody td:nth-child(5) {
        width: 150px !important;
        max-width: 150px !important;
        min-width: 150px !important;
    }
    #tablaEstudiantes tbody td:nth-child(5) select,
    #tablaEstudiantes tbody td:nth-child(5) .select2-container {
        width: 150px !important;
        max-width: 150px !important;
    }
</style>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>
    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <?php include("../compartido/texto-manual-ayuda.php");?>
                    <div class="page-title-breadcrumb">
                        <div class="pull-left">
                            <div class="page-title">Promocionar Estudiantes</div>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="cursos.php" onClick="deseaRegresar(this)">Cursos</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Promocionar Estudiantes</li>
                        </ol>
                    </div>
                </div>
                <?php include("../../config-general/mensajes-informativos.php"); ?>
                
                <!-- Indicador de Pasos -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="step-indicator">
                                    <div class="step <?= $pasoActual == 1 ? 'active' : ($paso1Completo ? 'completed' : '') ?>">
                                        <div class="step-circle"><?= $paso1Completo ? '' : '1' ?></div>
                                        <div class="step-label">Configuración<br>Inicial</div>
                                    </div>
                                    <div class="step <?= $pasoActual == 2 ? 'active' : ($paso2Completo ? 'completed' : '') ?>">
                                        <div class="step-circle"><?= $paso2Completo ? '' : '2' ?></div>
                                        <div class="step-label">Relacionar<br>Materias</div>
                                    </div>
                                    <div class="step <?= $pasoActual == 3 ? 'active' : ($paso3Completo ? 'completed' : '') ?>">
                                        <div class="step-circle"><?= $paso3Completo ? '' : '3' ?></div>
                                        <div class="step-label">Seleccionar<br>Estudiantes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Panel lateral con información -->
                    <div class="<?= $pasoActual == 1 ? 'col-sm-2' : ($pasoActual == 3 ? 'col-sm-0' : 'col-sm-3') ?>" <?= $pasoActual == 3 ? 'style="display: none;"' : '' ?>>
                        <div class="panel">
                            <header class="panel-heading panel-heading-blue">
                                <i class="fa fa-info-circle"></i> Información
                            </header>
                            <div class="panel-body">
                                <?php if($pasoActual == 1): ?>
                                    <div class="info-card">
                                        <h6><i class="fa fa-lightbulb-o"></i> Paso 1 de 3</h6>
                                        <p class="mb-0">Selecciona el curso de origen y destino, y decide si deseas relacionar las cargas académicas.</p>
                                    </div>
                                    <div class="warning-card">
                                        <h6><i class="fa fa-exclamation-triangle"></i> Importante</h6>
                                        <p class="mb-0"><small>Si activas "Relacionar Cargas", deberás seleccionar grupos específicos y relacionar cada materia manualmente.</p>
                                    </div>
                                <?php elseif($pasoActual == 2): ?>
                                    <div class="info-card">
                                        <h6><i class="fa fa-lightbulb-o"></i> Paso 2 de 3</h6>
                                        <p class="mb-0">Relaciona cada materia del curso origen con su correspondiente en el curso destino.</p>
                                    </div>
                                    <div class="summary-box">
                                        <h6 class="mb-3"><i class="fa fa-list"></i> Resumen</h6>
                                        <div class="summary-item">
                                            <span class="summary-label">Desde:</span>
                                            <span class="summary-value"><?= htmlspecialchars($nombreCursoDesde) ?></span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Hacia:</span>
                                            <span class="summary-value"><?= htmlspecialchars($nombreCursoPara) ?></span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Relacionar Cargas:</span>
                                            <span class="summary-value"><?= !empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1 ? 'Sí' : 'No' ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido principal -->
                    <div class="<?= $pasoActual == 1 ? 'col' : ($pasoActual == 3 ? 'col-sm-12' : 'col-sm-9') ?>">
                        <!-- PASO 1: Configuración Inicial -->
                        <div class="panel step-content <?= $pasoActual == 1 ? 'active' : '' ?>">
                            <header class="panel-heading panel-heading-purple">
                                <i class="fa fa-cog"></i> Paso 1: Configuración Inicial
                            </header>
                            <div class="panel-body">
                                <form action="cursos-promocionar-estudiantes-detalles.php" method="post" enctype="multipart/form-data" id="formPaso1">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Curso de Origen: <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <?php
                                            $opcionesConsulta = Grados::listarGrados();
                                            ?>
                                            <select class="form-control select2" name="desde" id="desde" required <?=$disabledPermiso;?>>
                                                <option value="">Seleccione el curso de origen</option>
                                                <?php
                                                while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                    $selected = (!empty($_POST['desde']) && $opcionesDatos['gra_id']==$_POST['desde']) ? 'selected' : '';
                                                    echo '<option value="'.$opcionesDatos['gra_id'].'" '.$selected.'>'.$opcionesDatos['gra_nombre'].'</option>';
                                                }?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Curso de Destino: <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <?php
                                            $opcionesConsulta = Grados::listarGrados();
                                            ?>
                                            <select class="form-control select2" name="para" id="para" required <?=$disabledPermiso;?>>
                                                <option value="">Seleccione el curso de destino</option>
                                                <?php
                                                while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                    $selected = (!empty($_POST['para']) && $opcionesDatos['gra_id']==$_POST['para']) ? 'selected' : '';
                                                    echo '<option value="'.$opcionesDatos['gra_id'].'" '.$selected.'>'.$opcionesDatos['gra_nombre'].'</option>';
                                                }?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Relacionar Cargas Académicas:</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <label class="switchToggle">
                                                    <input type="checkbox" name="relacionCargas" id="relacionCargas" 
                                                           <?=!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1 ? "checked" : ""?> 
                                                           onchange="relacionCargasGrupos(this)" value="1" <?=$disabledPermiso;?>>
                                                    <span class="slider green round"></span>
                                                </label>
                                                <span class="ml-3">
                                                    <button type="button" class="btn btn-sm btn-link p-0" data-toggle="tooltip" 
                                                            title="Si activas esta opción, podrás relacionar las cargas académicas del curso origen con las del curso destino. Esto permite transferir las calificaciones y definitivas de los estudiantes.">
                                                        <i class="fa fa-info-circle text-info"></i>
                                                    </button>
                                                </span>
                                            </div>
                                            <small class="form-text text-muted">Permite transferir calificaciones y definitivas entre cursos</small>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row" id="elementGroup" style="display: <?=$display?>;">
                                        <label class="col-sm-3 col-form-label">Grupo de Origen: <span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <select class="form-control select2" name="grupoDesde" id="grupoDesde" <?=$disabledPermiso;?>>
                                                <option value="">Seleccione un grupo</option>
                                                <?php
                                                $opcionesConsulta = Grupos::listarGrupos();
                                                while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                    $selected = (!empty($_POST['grupoDesde']) && $opcionesDatos['gru_id']==$_POST['grupoDesde']) ? 'selected' : '';
                                                    echo '<option value="'.$opcionesDatos['gru_id'].'" '.$selected.'>'.$opcionesDatos['gru_nombre'].'</option>';
                                                }?>
                                            </select>
                                        </div>
                                        
                                        <label class="col-sm-2 col-form-label">Grupo de Destino: <span class="text-danger">*</span></label>
                                        <div class="col-sm-3">
                                            <select class="form-control select2" name="grupoPara" id="grupoPara" <?=$disabledPermiso;?>>
                                                <option value="">Seleccione un grupo</option>
                                                <?php
                                                $opcionesConsulta = Grupos::listarGrupos();
                                                while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                    $selected = (!empty($_POST['grupoPara']) && $opcionesDatos['gru_id']==$_POST['grupoPara']) ? 'selected' : '';
                                                    echo '<option value="'.$opcionesDatos['gru_id'].'" '.$selected.'>'.$opcionesDatos['gru_nombre'].'</option>';
                                                }?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <?php if(Modulos::validarPermisoEdicion()): ?>
                                        <div class="form-group">
                                            <button type="submit" name="escogioCursos" value="1" class="btn btn-success btn-step">
                                                <i class="fa fa-arrow-right"></i> Continuar al Paso 2
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                        <!-- PASO 2: Relacionar Materias (solo si relacionCargas está activo) -->
                        <?php if(!empty($_POST['escogioCursos']) && (!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1)): 
                            $filtro='';
                            if(!empty($_POST['desde'])) {
                                $filtro .= " AND car_curso='".$_POST['desde']."'";
                            }
                            if(!empty($_POST['grupoDesde'])) {
                                $filtro .= " AND car_grupo='".$_POST['grupoDesde']."'";
                            }
                            $consultaCargas = CargaAcademica::listarCargas($conexion, $config, "", $filtro, "mat_id, car_grupo");
                            
                            $filtroPara='';
                            if(!empty($_POST['para'])) {
                                $filtroPara .= " AND car_curso='".$_POST["para"]."'";
                            }
                            if(!empty($_POST['grupoPara'])) {
                                $filtroPara .= " AND car_grupo='".$_POST['grupoPara']."'";
                            }
                        ?>
                            <div class="panel step-content active">
                                <header class="panel-heading panel-heading-purple">
                                    <i class="fa fa-link"></i> Paso 2: Relacionar Materias
                                </header>
                                <div class="panel-body">
                                    <form action="cursos-promocionar-estudiantes-detalles.php" method="post" enctype="multipart/form-data" id="formPaso2">
                                        <input type="hidden" name="desde" value="<?=$_POST["desde"];?>">
                                        <input type="hidden" name="para" value="<?=$_POST["para"];?>">
                                        <input type="hidden" name="grupoDesde" value="<?=$_POST["grupoDesde"];?>">
                                        <input type="hidden" name="grupoPara" value="<?=$_POST["grupoPara"];?>">
                                        <input type="hidden" name="relacionCargas" value="<?=$_POST["relacionCargas"];?>">
                                        <input type="hidden" name="escogioCursos" value="1">
                                        <div id="divCargas"></div>
                                        
                                        <div class="info-card mb-3">
                                            <p class="mb-0"><i class="fa fa-info-circle"></i> Relaciona cada materia del curso <strong><?= htmlspecialchars($nombreCursoDesde) ?></strong> con su correspondiente en el curso <strong><?= htmlspecialchars($nombreCursoPara) ?></strong>. Esto permitirá transferir las calificaciones y definitivas.</p>
                                        </div>
                                        
                                        <div class="card card-topline-purple">
                                            <div class="card-head">
                                                <header>Relacionar Materias</header>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="tablaMaterias" class="display" style="width:100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>COD</th>
                                                                <th>Carga Actual (Origen)</th>
                                                                <th>Carga a Relacionar (Destino)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $contReg=1;
                                                            while($datosCarga = mysqli_fetch_array($consultaCargas, MYSQLI_BOTH)){
                                                                $nombreDocente = UsuariosPadre::nombreCompletoDelUsuario($datosCarga);
                                                            ?>
                                                                <tr>
                                                                    <td><?= $contReg; ?></td>
                                                                    <td><?=$datosCarga['car_id'];?></td>
                                                                    <td>
                                                                        <strong>Grupo:</strong> <?=$datosCarga['gru_nombre'];?><br>
                                                                        <strong>Materia:</strong> <?=$datosCarga['mat_nombre'];?><br>
                                                                        <strong>Docente:</strong> <?=$nombreDocente;?>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control select2" onchange="crearInputCarga(this)" 
                                                                                id="carga<?=$datosCarga['car_id'];?>" 
                                                                                data-carga="<?=$datosCarga['car_id'];?>" <?=$disabledPermiso;?>>
                                                                            <option value="">Seleccione una carga</option>
                                                                            <?php
                                                                            $consultaCargasPara = CargaAcademica::listarCargas($conexion, $config, "", $filtroPara, "mat_id, car_grupo");
                                                                            while($opcionesDatos = mysqli_fetch_array($consultaCargasPara, MYSQLI_BOTH)){
                                                                                $nombreDocentePara = UsuariosPadre::nombreCompletoDelUsuario($opcionesDatos);
                                                                            ?>
                                                                                <option value="<?=$opcionesDatos['car_id'];?>">
                                                                                    Grupo: <?=$opcionesDatos['gru_nombre'];?> | Materia: <?=$opcionesDatos['mat_nombre'];?> | Docente: <?=$nombreDocentePara;?>
                                                                                </option>
                                                                            <?php }?>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                            <?php 
                                                            $contReg++;
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if(Modulos::validarPermisoEdicion()): ?>
                                            <div class="form-group mt-3">
                                                <button type="button" class="btn btn-secondary btn-step" onclick="volverPaso1()">
                                                    <i class="fa fa-arrow-left"></i> Volver
                                                </button>
                                                <button type="submit" name="relacionoMaterias" value="1" class="btn btn-success btn-step">
                                                    <i class="fa fa-arrow-right"></i> Continuar al Paso 3
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- PASO 3: Seleccionar Estudiantes -->
                        <?php if (!empty($_POST['relacionoMaterias']) || (!empty($_POST['escogioCursos']) && (empty($_POST['relacionCargas']) || $_POST['relacionCargas'] != 1))): 
                            $filtro = " AND (mat_promocionado=0 OR mat_promocionado=NULL)";
                            $curso='';
                            if(!empty($_POST['desde'])) {
                                $curso=$_POST["desde"];
                                $filtro .= " AND mat_grado='".$curso."'";
                            }
                            $grupo='';
                            if(!empty($_POST['grupoDesde'])) {
                                $grupo = $_POST["grupoDesde"];
                                $filtro .= " AND mat_grupo='".$grupo."'";
                            }
                            
                            $consultaEstudiantes = Estudiantes::listarEstudiantesEnGrados($filtro);
                            $numeroEstudiantes = mysqli_num_rows($consultaEstudiantes);
                        ?>
                            <div class="panel step-content active">
                                <header class="panel-heading panel-heading-purple">
                                    <i class="fa fa-users"></i> Paso 3: Seleccionar Estudiantes
                                    <div class="pull-right">
                                        <small class="text-white">
                                            <strong>Desde:</strong> <?= htmlspecialchars($nombreCursoDesde) ?> → 
                                            <strong>Hacia:</strong> <?= htmlspecialchars($nombreCursoPara) ?>
                                        </small>
                                    </div>
                                </header>
                                <div class="panel-body">
                                    <form action="cursos-promocionar-estudiantes.php" method="post" enctype="multipart/form-data" id="formPaso3">
                                        <input type="hidden" name="desde" value="<?=$_POST["desde"];?>">
                                        <input type="hidden" name="para" value="<?=$_POST["para"];?>">
                                        <input type="hidden" name="grupoDesde" value="<?=!empty($_POST["grupoDesde"]) ? $_POST["grupoDesde"] : "";?>">
                                        <input type="hidden" name="grupoPara" value="<?=!empty($_POST["grupoPara"]) ? $_POST["grupoPara"] : "";?>">
                                        <input type="hidden" name="relacionCargas" value="<?=!empty($_POST["relacionCargas"]) ? $_POST["relacionCargas"] : 0;?>">
                                        <div id="divEstudiante"></div>
                                        
                                        <?php
                                        if(!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1){ 
                                            $filtroCarga = " AND car_curso='".$curso."' AND car_grupo='".$grupo."'";
                                            $consultaCargas2 = CargaAcademica::listarCargas($conexion, $config, "", $filtroCarga, "mat_id, car_grupo");
                                            while($datosCarga2 = mysqli_fetch_array($consultaCargas2, MYSQLI_BOTH)){
                                                $fieldName = "carga".$datosCarga2['car_id'];
                                        ?>
                                            <input type="hidden" name="<?=$fieldName;?>" value="<?=$_POST['carga'.$datosCarga2['car_id']];?>">
                                        <?php }} ?>
                                        
                                        <div class="info-card mb-3">
                                            <p class="mb-0">
                                                <i class="fa fa-info-circle"></i> 
                                                Selecciona los estudiantes que deseas promocionar de <strong><?= htmlspecialchars($nombreCursoDesde) ?></strong> 
                                                a <strong><?= htmlspecialchars($nombreCursoPara) ?></strong>.
                                                <?php if(empty($_POST['relacionCargas']) || $_POST['relacionCargas'] != 1): ?>
                                                    Puedes cambiar el grupo de cada estudiante individualmente.
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        
                                        <div class="card card-topline-purple">
                                            <div class="card-head">
                                                <header>
                                                    Estudiantes Disponibles 
                                                    <span class="badge badge-primary" id="cantSeleccionadas">0</span> / <?= $numeroEstudiantes ?> seleccionados
                                                </header>
                                            </div>
                                            <div class="card-body" style="padding: 0;">
                                                <div class="table-responsive" style="max-height: none;">
                                                    <table id="tablaEstudiantes" class="display table-striped table-bordered" style="width:100%; margin: 0;">
                                                        <thead>
                                                            <tr>
                                                                <th width="40">#</th>
                                                                <th width="60">
                                                                    <div class="input-group spinner">
                                                                        <label class="switchToggle">
                                                                            <input type="checkbox" id="all" <?=$disabledPermiso;?>>
                                                                            <span class="slider green round"></span>
                                                                        </label>
                                                                    </div>
                                                                </th>
                                                                <th width="120">DOCUMENTO</th>
                                                                <th style="min-width: 300px;">NOMBRES Y APELLIDOS</th>
                                                                <?php if(empty($_POST['relacionCargas']) || $_POST['relacionCargas'] == 0): ?>
                                                                    <th width="150">GRUPO</th>
                                                                <?php endif; ?>
                                                                <th width="130">EST. MATRÍCULA</th>
                                                                <th width="180">CAMBIAR ESTADO<br>A MATRICULADO?</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $contReg=1;
                                                            while($datosEstudiante = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH)){
                                                                $nombre = Estudiantes::NombreCompletoDelEstudiante($datosEstudiante);
                                                            ?>
                                                                <tr>
                                                                    <td><?= $contReg; ?></td>
                                                                    <td>
                                                                        <div class="input-group spinner">
                                                                            <label class="switchToggle">
                                                                                <input type="checkbox" class="check" onchange="seleccionarEstudiantes(this)" 
                                                                                       value="<?=$datosEstudiante['mat_id'];?>" 
                                                                                       data-grupo="<?=$datosEstudiante['mat_grupo'];?>" <?=$disabledPermiso;?>>
                                                                                <span class="slider green round"></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td><?=$datosEstudiante['mat_documento'];?></td>
                                                                    <td style="white-space: nowrap; min-width: 300px;"><?=$nombre;?></td>
                                                                    <?php if(empty($_POST['relacionCargas']) || $_POST['relacionCargas'] == 0): ?>
                                                                        <td style="width: 150px;">
                                                                            <select class="form-control select2" 
                                                                                    onchange="crearInputGrupoEstudiante(this, '<?=$datosEstudiante['mat_id'];?>', 'noGrupo')" 
                                                                                    id="grupo<?=$datosEstudiante['mat_id'];?>" 
                                                                                    style="width: 100%; max-width: 150px;" <?=$disabledPermiso;?>>
                                                                                <option value="">Seleccione un grupo</option>
                                                                                <?php
                                                                                $opcionesConsulta = Grupos::listarGrupos();
                                                                                while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                                                    $selected = ($datosEstudiante['mat_grupo']==$opcionesDatos['gru_id']) ? 'selected' : '';
                                                                                    echo '<option value="'.$opcionesDatos['gru_id'].'" '.$selected.'>'.$opcionesDatos['gru_id'].'. '.strtoupper($opcionesDatos['gru_nombre']).'</option>';
                                                                                }?>
                                                                            </select>
                                                                        </td>
                                                                    <?php endif; ?>
                                                                    <?php 
                                                                    $estadoMatricula = !empty($datosEstudiante['mat_estado_matricula']) ? $datosEstudiante['mat_estado_matricula'] : '';
                                                                    $claseEstado = isset($estadosEtiquetasMatriculas[$estadoMatricula]) ? $estadosEtiquetasMatriculas[$estadoMatricula] : '';
                                                                    $textoEstado = isset($estadosMatriculasEstudiantes[$estadoMatricula]) ? $estadosMatriculasEstudiantes[$estadoMatricula] : 'No definido';
                                                                    ?>
                                                                    <td class="<?=$claseEstado;?>">
                                                                        <?=$textoEstado;?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if($datosEstudiante['mat_estado_matricula'] != MATRICULADO): ?>
                                                                            <div class="input-group spinner">
                                                                                <label class="switchToggle">
                                                                                    <input type="checkbox" id="cambiarEstado<?=$datosEstudiante['mat_id'];?>" 
                                                                                           data-id-estudiante="<?=$datosEstudiante['mat_id'];?>" 
                                                                                           onchange="crearInputEstadoEstudiante(this)" value="1">
                                                                                    <span class="slider green round"></span>
                                                                                </label>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-success">Ya está matriculado</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                            <?php 
                                                            $contReg++;
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if($numeroEstudiantes > 0 && Modulos::validarPermisoEdicion()): ?>
                                            <div class="form-group mt-3">
                                                <button type="button" class="btn btn-secondary btn-step" onclick="volverPasoAnterior()">
                                                    <i class="fa fa-arrow-left"></i> Volver
                                                </button>
                                                <button type="button" class="btn btn-success btn-step" id="btnRealizarPromocion" onclick="confirmarPromocion()" disabled>
                                                    <i class="fa fa-check"></i> Realizar Promoción
                                                </button>
                                            </div>
                                        <?php elseif($numeroEstudiantes == 0): ?>
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle"></i> No hay estudiantes disponibles para promocionar en este curso/grupo.
                                            </div>
                                        <?php endif; ?>
                                        
                                        <select id="estudiantesSeleccionados" style="width: 100% !important" name="estudiantes[]" multiple hidden></select>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end page container -->
<?php include("../compartido/footer.php"); ?>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../js/Cursos.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- data tables -->
<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
<script src="../../config-general/assets/js/pages/table/table_data.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
<!-- SweetAlert2 -->
<script src="../../config-general/assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar DataTables
    if ($.fn.DataTable.isDataTable('#tablaMaterias')) {
        $('#tablaMaterias').DataTable().destroy();
    }
    if ($('#tablaMaterias').length) {
        $('#tablaMaterias').DataTable({
            "language": {
                "url": "../../config-general/assets/plugins/datatables/Spanish.json"
            },
            "pageLength": 10,
            "order": [[0, "asc"]]
        });
    }
    
    if ($.fn.DataTable.isDataTable('#tablaEstudiantes')) {
        $('#tablaEstudiantes').DataTable().destroy();
    }
    if ($('#tablaEstudiantes').length) {
        var tieneGrupo = $('#tablaEstudiantes thead th').length > 6;
        var columnDefs = [
            { "width": "40px", "targets": 0 },
            { "width": "60px", "targets": 1 },
            { "width": "120px", "targets": 2 },
            { "width": "auto", "targets": 3 } // NOMBRES Y APELLIDOS - ancho automático para usar el espacio disponible
        ];
        
        if (tieneGrupo) {
            columnDefs.push(
                { "width": "150px", "targets": 4 }, // GRUPO - ancho fijo
                { "width": "130px", "targets": 5 },
                { "width": "180px", "targets": 6 }
            );
        } else {
            columnDefs.push(
                { "width": "130px", "targets": 4 },
                { "width": "180px", "targets": 5 }
            );
        }
        
        $('#tablaEstudiantes').DataTable({
            "language": {
                "url": "../../config-general/assets/plugins/datatables/Spanish.json"
            },
            "pageLength": 25,
            "order": [[0, "asc"]],
            "autoWidth": false,
            "columnDefs": columnDefs,
            "scrollX": false
        });
        
        // Asegurar que los select2 en la columna GRUPO tengan ancho fijo después de inicializar
        setTimeout(function() {
            $('select[id^="grupo"]').each(function() {
                $(this).css('width', '100%').css('max-width', '150px');
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).next('.select2-container').css('width', '150px');
                }
            });
        }, 500);
        
        // Re-aplicar después de cada redibujado de la tabla
        $('#tablaEstudiantes').on('draw.dt', function() {
            setTimeout(function() {
                $('select[id^="grupo"]').each(function() {
                    $(this).css('width', '100%').css('max-width', '150px');
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).next('.select2-container').css('width', '150px');
                    }
                });
            }, 100);
        });
    }
    
    // Validación del formulario paso 1
    $('#formPaso1').on('submit', function(e) {
        var desde = $('#desde').val();
        var para = $('#para').val();
        var relacionCargas = $('#relacionCargas').is(':checked');
        
        if (!desde || !para) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Campos Requeridos',
                text: 'Por favor, selecciona el curso de origen y el curso de destino.',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        if (desde === para) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de Selección',
                text: 'El curso de origen y destino no pueden ser el mismo.',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        if (relacionCargas) {
            var grupoDesde = $('#grupoDesde').val();
            var grupoPara = $('#grupoPara').val();
            
            if (!grupoDesde || !grupoPara) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Grupos Requeridos',
                    text: 'Si activas "Relacionar Cargas", debes seleccionar un grupo de origen y un grupo de destino.',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
        }
    });
    
    // Validación del formulario paso 2
    $('#formPaso2').on('submit', function(e) {
        var todasRelacionadas = true;
        $('select[id^="carga"]').each(function() {
            if ($(this).val() === '') {
                todasRelacionadas = false;
                return false;
            }
        });
        
        if (!todasRelacionadas) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Relaciones Incompletas',
                text: 'Por favor, relaciona todas las materias antes de continuar.',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
    });
});

function volverPaso1() {
    Swal.fire({
        title: '¿Volver al paso anterior?',
        text: 'Los datos del paso actual se perderán.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, volver',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario para mantener los datos del paso 1
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'cursos-promocionar-estudiantes-detalles.php';
            form.style.display = 'none';
            
            <?php if(!empty($_POST['desde'])): ?>
            var inputDesde = document.createElement('input');
            inputDesde.type = 'hidden';
            inputDesde.name = 'desde';
            inputDesde.value = '<?= addslashes($_POST["desde"]) ?>';
            form.appendChild(inputDesde);
            <?php endif; ?>
            
            <?php if(!empty($_POST['para'])): ?>
            var inputPara = document.createElement('input');
            inputPara.type = 'hidden';
            inputPara.name = 'para';
            inputPara.value = '<?= addslashes($_POST["para"]) ?>';
            form.appendChild(inputPara);
            <?php endif; ?>
            
            <?php if(!empty($_POST['relacionCargas'])): ?>
            var inputRelacion = document.createElement('input');
            inputRelacion.type = 'hidden';
            inputRelacion.name = 'relacionCargas';
            inputRelacion.value = '<?= addslashes($_POST["relacionCargas"]) ?>';
            form.appendChild(inputRelacion);
            <?php endif; ?>
            
            <?php if(!empty($_POST['grupoDesde'])): ?>
            var inputGrupoDesde = document.createElement('input');
            inputGrupoDesde.type = 'hidden';
            inputGrupoDesde.name = 'grupoDesde';
            inputGrupoDesde.value = '<?= addslashes($_POST["grupoDesde"]) ?>';
            form.appendChild(inputGrupoDesde);
            <?php endif; ?>
            
            <?php if(!empty($_POST['grupoPara'])): ?>
            var inputGrupoPara = document.createElement('input');
            inputGrupoPara.type = 'hidden';
            inputGrupoPara.name = 'grupoPara';
            inputGrupoPara.value = '<?= addslashes($_POST["grupoPara"]) ?>';
            form.appendChild(inputGrupoPara);
            <?php endif; ?>
            
            // NO agregar escogioCursos para que vuelva al paso 1
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function volverPasoAnterior() {
    Swal.fire({
        title: '¿Volver al paso anterior?',
        text: 'La selección de estudiantes se perderá.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, volver',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario temporal para volver
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'cursos-promocionar-estudiantes-detalles.php';
            
            <?php if(!empty($_POST['desde'])): ?>
            var inputDesde = document.createElement('input');
            inputDesde.type = 'hidden';
            inputDesde.name = 'desde';
            inputDesde.value = '<?= addslashes($_POST["desde"]) ?>';
            form.appendChild(inputDesde);
            <?php endif; ?>
            
            <?php if(!empty($_POST['para'])): ?>
            var inputPara = document.createElement('input');
            inputPara.type = 'hidden';
            inputPara.name = 'para';
            inputPara.value = '<?= addslashes($_POST["para"]) ?>';
            form.appendChild(inputPara);
            <?php endif; ?>
            
            <?php if(!empty($_POST['relacionCargas'])): ?>
            var inputRelacion = document.createElement('input');
            inputRelacion.type = 'hidden';
            inputRelacion.name = 'relacionCargas';
            inputRelacion.value = '<?= addslashes($_POST["relacionCargas"]) ?>';
            form.appendChild(inputRelacion);
            <?php endif; ?>
            
            <?php if(!empty($_POST['grupoDesde'])): ?>
            var inputGrupoDesde = document.createElement('input');
            inputGrupoDesde.type = 'hidden';
            inputGrupoDesde.name = 'grupoDesde';
            inputGrupoDesde.value = '<?= addslashes($_POST["grupoDesde"]) ?>';
            form.appendChild(inputGrupoDesde);
            <?php endif; ?>
            
            <?php if(!empty($_POST['grupoPara'])): ?>
            var inputGrupoPara = document.createElement('input');
            inputGrupoPara.type = 'hidden';
            inputGrupoPara.name = 'grupoPara';
            inputGrupoPara.value = '<?= addslashes($_POST["grupoPara"]) ?>';
            form.appendChild(inputGrupoPara);
            <?php endif; ?>
            
            // Determinar a qué paso volver
            <?php if(!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1): ?>
            // Si relacionCargas está activo, volver al paso 2 (relacionar materias)
            var inputEscogio = document.createElement('input');
            inputEscogio.type = 'hidden';
            inputEscogio.name = 'escogioCursos';
            inputEscogio.value = '1';
            form.appendChild(inputEscogio);
            <?php else: ?>
            // Si no relaciona cargas, volver al paso 1 (configuración inicial)
            // No agregamos escogioCursos para que vuelva al paso 1
            <?php endif; ?>
            
            form.style.display = 'none';
            document.body.appendChild(form);
            try {
                form.submit();
            } catch(e) {
                console.error('Error al enviar formulario:', e);
                // Fallback: redirección directa con parámetros GET
                var params = [];
                <?php if(!empty($_POST['desde'])): ?>
                params.push('desde=<?= urlencode($_POST["desde"]) ?>');
                <?php endif; ?>
                <?php if(!empty($_POST['para'])): ?>
                params.push('para=<?= urlencode($_POST["para"]) ?>');
                <?php endif; ?>
                var url = 'cursos-promocionar-estudiantes-detalles.php' + (params.length > 0 ? '?' + params.join('&') : '');
                window.location.href = url;
            }
        }
    });
}

function confirmarPromocion() {
    var selectEstudiantes = document.getElementById('estudiantesSeleccionados');
    var estudiantesSeleccionados = selectEstudiantes ? selectEstudiantes.selectedOptions.length : 0;
    
    // Verificar también los checkboxes marcados como respaldo
    var checkboxesMarcados = document.querySelectorAll('.check:checked').length;
    
    if (estudiantesSeleccionados === 0 && checkboxesMarcados === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Ningún Estudiante Seleccionado',
            text: 'Por favor, selecciona al menos un estudiante para promocionar.',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Asegurar que los IDs estén en el select antes de enviar
    if (estudiantesSeleccionados === 0 && checkboxesMarcados > 0) {
        // Si hay checkboxes marcados pero no en el select, agregarlos
        document.querySelectorAll('.check:checked').forEach(function(checkbox) {
            var idEstudiante = checkbox.value;
            if (idEstudiante && idEstudiante > 0) {
                var existe = false;
                for (var i = 0; i < selectEstudiantes.options.length; i++) {
                    if (selectEstudiantes.options[i].value == idEstudiante) {
                        existe = true;
                        break;
                    }
                }
                if (!existe) {
                    var nuevaOpcion = document.createElement('option');
                    nuevaOpcion.value = idEstudiante;
                    nuevaOpcion.textContent = idEstudiante;
                    nuevaOpcion.selected = true;
                    selectEstudiantes.appendChild(nuevaOpcion);
                }
            }
        });
        estudiantesSeleccionados = selectEstudiantes.selectedOptions.length;
    }
    
    var desde = '<?= htmlspecialchars($nombreCursoDesde) ?>';
    var para = '<?= htmlspecialchars($nombreCursoPara) ?>';
    
    // Validar que hay IDs válidos (alfanuméricos permitidos)
    var idsValidos = [];
    for (var i = 0; i < selectEstudiantes.selectedOptions.length; i++) {
        var id = selectEstudiantes.selectedOptions[i].value;
        if (id && id.trim() !== '' && id.trim() !== '0') {
            idsValidos.push(id.trim());
        }
    }
    
    if (idsValidos.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error en la Selección',
            text: 'No se encontraron IDs válidos de estudiantes. Por favor, recarga la página y selecciona los estudiantes nuevamente.',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    Swal.fire({
        title: '¿Confirmar Promoción?',
        html: '<div class="text-left">' +
              '<p><strong>Estudiantes a promocionar:</strong> ' + idsValidos.length + '</p>' +
              '<p><strong>Desde:</strong> ' + desde + '</p>' +
              '<p><strong>Hacia:</strong> ' + para + '</p>' +
              '<p class="text-danger mt-3"><i class="fa fa-exclamation-triangle"></i> Esta acción no se puede deshacer fácilmente. ¿Estás seguro?</p>' +
              '</div>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, promocionar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Procesando...',
                html: 'Promocionando estudiantes. Por favor espera.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar formulario
            $('#formPaso3').submit();
        }
    });
}
</script>
<!-- end js include path -->

</body>

</html>