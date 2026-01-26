<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0037';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademicaOptimizada.php");


Utilidades::validarParametros($_GET,["id"]);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<!-- dropzone -->
    <link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
    <!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
    <!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    
    <style>
    /* Estilos mejorados para radio buttons y checkboxes */
    .form-group .radio,
    .form-group .checkbox {
        margin-bottom: 12px;
        padding: 8px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    
    .form-group .radio:hover,
    .form-group .checkbox:hover {
        background-color: #f8f9fa;
    }
    
    .radio input[type="radio"],
    .checkbox input[type="checkbox"] {
        width: 20px !important;
        height: 20px !important;
        margin: 0 10px 0 0 !important;
        cursor: pointer !important;
        position: relative;
        opacity: 1 !important;
        -webkit-appearance: checkbox !important;
        -moz-appearance: checkbox !important;
        appearance: checkbox !important;
        border: 2px solid #667eea !important;
        border-radius: 3px;
        flex-shrink: 0;
    }
    
    .radio input[type="radio"] {
        -webkit-appearance: radio !important;
        -moz-appearance: radio !important;
        appearance: radio !important;
        border-radius: 50% !important;
        border: 2px solid #667eea !important;
    }
    
    .radio label,
    .checkbox label {
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        margin-bottom: 0 !important;
        font-weight: normal;
        padding: 0 !important;
        user-select: none;
    }
    
    .radio label:hover,
    .checkbox label:hover {
        color: #667eea;
    }
    
    /* Estados checked - muy visibles */
    input[type="radio"]:checked {
        background-color: #667eea !important;
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2) !important;
    }
    
    input[type="checkbox"]:checked {
        background-color: #667eea !important;
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2) !important;
    }
    
    /* Marca de verificación para checkboxes */
    input[type="checkbox"]:checked::after {
        content: '✓' !important;
        display: block !important;
        color: white !important;
        font-weight: bold !important;
        font-size: 14px !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        line-height: 1;
    }
    
    /* Punto central para radio buttons */
    input[type="radio"]:checked::after {
        content: '' !important;
        display: block !important;
        width: 8px !important;
        height: 8px !important;
        background: white !important;
        border-radius: 50% !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
    }
    
    /* Contenedor de cargas y períodos */
    #cargasEspecificasContainer {
        margin-top: 15px;
    }
    
    #cargasEspecificasContainer .checkbox label,
    .form-group .checkbox label {
        padding-left: 0 !important;
    }
    
    /* Mejorar visibilidad del contenedor */
    #cargasEspecificasContainer > div {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
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
                            <div class=" pull-left">
                                <div class="page-title">Editar Indicadores</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cargas-indicadores-obligatorios.php" onClick="deseaRegresar(this)">Indicadores Obligatorios</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar Indicadores</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
                        <div class="col-sm-12">


								<div class="panel">
									<header class="panel-heading panel-heading-purple">Editar Indicadores</header>
                                	<div class="panel-body">
                                    <?php 
                                    $rCargas = Indicadores::traerIndicadoresDatos(base64_decode($_GET["id"]));
                                    $idIndicador = base64_decode($_GET["id"]);
                                    
                                    // Verificar si está en uso
                                    $verificacionUso = Indicadores::verificarIndicadorEnUso($config, $idIndicador);
                                    $enUso = $verificacionUso['enUso'];
                                    $disabledEdicion = $enUso ? 'readonly' : '';
                                    
                                    // Obtener cargas y períodos asignados con información de uso
                                    $sqlAsignaciones = "SELECT DISTINCT aic.ipc_carga, aic.ipc_periodo, 
                                                       CONCAT_WS(' - ', gra.gra_nombre, gru.gru_nombre, am.mat_nombre) as carga_nombre,
                                                       COUNT(DISTINCT aa.act_id) as total_actividades,
                                                       COUNT(DISTINCT aac.cal_id) as total_calificaciones
                                                       FROM ".BD_ACADEMICA.".academico_indicadores_carga aic
                                                       INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car.car_id=aic.ipc_carga AND car.institucion=aic.institucion AND car.year=aic.year
                                                       INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra.gra_id=car.car_curso AND gra.institucion=car.institucion AND gra.year=car.year
                                                       INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car.car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
                                                       INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car.car_materia AND am.institucion=car.institucion AND am.year=car.year
                                                       LEFT JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id_tipo=aic.ipc_indicador AND aa.act_id_carga=aic.ipc_carga AND aa.act_periodo=aic.ipc_periodo AND aa.act_estado=1 AND aa.institucion=aic.institucion AND aa.year=aic.year
                                                       LEFT JOIN ".BD_ACADEMICA.".academico_calificaciones aac ON aac.cal_id_actividad=aa.act_id AND aac.institucion=aa.institucion AND aac.year=aa.year
                                                       WHERE aic.ipc_indicador=? AND aic.ipc_creado=0 AND aic.institucion=? AND aic.year=?
                                                       GROUP BY aic.ipc_carga, aic.ipc_periodo
                                                       ORDER BY aic.ipc_carga, aic.ipc_periodo";
                                    $parametrosAsignaciones = [$idIndicador, $config['conf_id_institucion'], $_SESSION["bd"]];
                                    $consultaAsignaciones = BindSQL::prepararSQL($sqlAsignaciones, $parametrosAsignaciones);
                                    
                                    // Obtener cargas disponibles para asignar (solo las que tienen car_indicadores_directivo=1)
                                    $selectSql = [
                                        "car.car_id",
                                        "car.car_periodo",
                                        "gra.gra_nombre",
                                        "gru.gru_nombre",
                                        "am.mat_nombre",
                                        "CONCAT_WS(' ', uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2) as docente_nombre"
                                    ];
                                    $cargasDisponiblesConsulta = CargaAcademicaOptimizada::listarCargasOptimizado($conexion, $config, "", "AND car.car_activa=1 AND car.car_indicadores_directivo=1", "gra.gra_nombre, gru.gru_nombre, am.mat_nombre", "", "", array(), $selectSql);
                                    $cargasDisponibles = [];
                                    while ($carga = mysqli_fetch_array($cargasDisponiblesConsulta, MYSQLI_BOTH)) {
                                        $cargasDisponibles[] = $carga;
                                    }
                                    
                                    // Obtener períodos máximos
                                    $periodosMaximos = $config['conf_periodos_maximos'] ?? 4;
                                    
                                    // Obtener IDs de cargas ya asignadas para este indicador
                                    $cargasAsignadasIds = [];
                                    mysqli_data_seek($consultaAsignaciones, 0);
                                    while($asignacion = mysqli_fetch_array($consultaAsignaciones, MYSQLI_BOTH)) {
                                        $cargasAsignadasIds[] = $asignacion['ipc_carga'];
                                    }
                                    mysqli_data_seek($consultaAsignaciones, 0);
                                    ?>

                                   
									<form name="formularioGuardar" action="cargas-indicadores-obligatorios-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" value="<?=$idIndicador?>" name="idI">
                                        <?php if($enUso): ?>
                                        <!-- Campos hidden con valores originales cuando está en uso -->
                                        <input type="hidden" name="nombre" value="<?=htmlspecialchars($rCargas['ind_nombre']);?>">
                                        <input type="hidden" name="valor" value="<?=htmlspecialchars($rCargas['ind_valor']);?>">
                                        <?php endif; ?>
										
                                        <?php if($enUso): ?>
                                        <div class="alert alert-warning">
                                            <strong>Advertencia:</strong> Este indicador está en uso en al menos una de las cargas asignadas. El nombre y el valor no pueden ser modificados. Solo se pueden asignar nuevas cargas. <?=$verificacionUso['mensaje'];?>
                                        </div>
                                        <?php endif; ?>
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre</label>
                                            <div class="col-sm-10">
                                                <?php if($enUso): ?>
                                                    <input type="text" id="campoNombre" class="form-control" value="<?=htmlspecialchars($rCargas['ind_nombre']);?>" readonly disabled style="background-color: #f5f5f5; cursor: not-allowed;">
                                                    <small class="text-muted"><i class="fa fa-info-circle"></i> No se puede modificar porque el indicador está en uso.</small>
                                                <?php else: ?>
                                                    <input type="text" id="campoNombre" name="nombre" class="form-control" value="<?=htmlspecialchars($rCargas['ind_nombre']);?>">
                                                <?php endif; ?>
                                            </div>
                                        </div>	
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Valor</label>
                                            <div class="col-sm-2">
                                                <?php if($enUso): ?>
                                                    <input type="text" id="campoValor" class="form-control" value="<?=htmlspecialchars($rCargas['ind_valor']);?>" readonly disabled style="background-color: #f5f5f5; cursor: not-allowed;">
                                                    <small class="text-muted"><i class="fa fa-info-circle"></i> No se puede modificar porque el indicador está en uso.</small>
                                                <?php else: ?>
                                                    <input type="text" id="campoValor" name="valor" class="form-control" value="<?=htmlspecialchars($rCargas['ind_valor']);?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-sm-8">
                                                <span style="color:#F06; font-size:11px;">Estos valores m&aacute;s la suma de los indicadores que crear&aacute; el docente debe ser igual a 100.</span>
                                            </div>
                                        </div>	

                                        <hr>
                                        <h4>Asignaciones Actuales</h4>
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Cargas y Períodos Asignados</label>
                                            <div class="col-sm-10">
                                                <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                                    <?php 
                                                    $hayAsignaciones = false;
                                                    $totalAsignaciones = 0;
                                                    $totalEnUso = 0;
                                                    mysqli_data_seek($consultaAsignaciones, 0);
                                                    while($asignacion = mysqli_fetch_array($consultaAsignaciones, MYSQLI_BOTH)): 
                                                        $hayAsignaciones = true;
                                                        $totalAsignaciones++;
                                                        $totalActividades = !empty($asignacion['total_actividades']) ? (int)$asignacion['total_actividades'] : 0;
                                                        $totalCalificaciones = !empty($asignacion['total_calificaciones']) ? (int)$asignacion['total_calificaciones'] : 0;
                                                        $estaEnUso = $totalActividades > 0 || $totalCalificaciones > 0;
                                                        if ($estaEnUso) {
                                                            $totalEnUso++;
                                                        }
                                                    ?>
                                                        <div class="alert <?=$estaEnUso ? 'alert-warning' : 'alert-info';?>" style="padding: 10px; margin-bottom: 8px;">
                                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                <div>
                                                                    <i class="fa <?=$estaEnUso ? 'fa-exclamation-triangle' : 'fa-check-circle';?>"></i> 
                                                                    <strong><?=$asignacion['carga_nombre'];?></strong> - Período <?=$asignacion['ipc_periodo'];?>
                                                                    <?php if ($estaEnUso): ?>
                                                                        <span class="label label-danger" style="margin-left: 10px;">EN USO</span>
                                                                    <?php else: ?>
                                                                        <span class="label label-success" style="margin-left: 10px;">Disponible</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <?php if ($estaEnUso): ?>
                                                                <div style="margin-top: 5px; font-size: 12px; color: #856404;">
                                                                    <?php if ($totalActividades > 0): ?>
                                                                        <i class="fa fa-tasks"></i> <?=$totalActividades;?> actividad(es) registrada(s)
                                                                    <?php endif; ?>
                                                                    <?php if ($totalCalificaciones > 0): ?>
                                                                        <?php if ($totalActividades > 0): ?> | <?php endif; ?>
                                                                        <i class="fa fa-file-text"></i> <?=$totalCalificaciones;?> calificación(es) asociada(s)
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endwhile; ?>
                                                    <?php if (!$hayAsignaciones): ?>
                                                        <p class="text-muted">Este indicador no está asignado a ninguna carga académica.</p>
                                                    <?php else: ?>
                                                        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #ddd;">
                                                            <strong>Resumen:</strong>
                                                            <ul style="margin-bottom: 0;">
                                                                <li>Total de asignaciones: <strong><?=$totalAsignaciones;?></strong></li>
                                                                <li>Asignaciones en uso: <strong style="color: #856404;"><?=$totalEnUso;?></strong></li>
                                                                <li>Asignaciones disponibles: <strong style="color: #155724;"><?=$totalAsignaciones - $totalEnUso;?></strong></li>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">Estas son las cargas y períodos donde este indicador está actualmente asignado. Las marcadas como "EN USO" tienen actividades o calificaciones relacionadas.</small>
                                            </div>
                                        </div>

                                        <hr>
                                        <h4>Asignar a Nuevas Cargas</h4>
                                        <?php if($enUso): ?>
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> Puede asignar este indicador a nuevas cargas aunque esté en uso.
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Asignar a</label>
                                            <div class="col-sm-10">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="asignacionTipo" value="todas" checked onclick="toggleCargasEspecificas()">
                                                        Todas las cargas académicas disponibles
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="asignacionTipo" value="especificas" onclick="toggleCargasEspecificas()">
                                                        Cargas académicas específicas
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row" id="cargasEspecificasContainer" style="display:none;">
                                            <label class="col-sm-2 control-label">Seleccionar Cargas</label>
                                            <div class="col-sm-10">
                                                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                                    <?php if (count($cargasDisponibles) > 0): ?>
                                                        <?php foreach ($cargasDisponibles as $carga): ?>
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="cargas[]" value="<?=$carga['car_id'];?>" <?=in_array($carga['car_id'], $cargasAsignadasIds) ? 'disabled' : '';?>>
                                                                    <?=$carga['gra_nombre'];?> - <?=$carga['gru_nombre'];?> - <?=$carga['mat_nombre'];?> (<?=$carga['docente_nombre'];?>)
                                                                    <?php if(in_array($carga['car_id'], $cargasAsignadasIds)): ?>
                                                                        <span class="text-muted">(Ya asignada)</span>
                                                                    <?php endif; ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted">No hay cargas académicas disponibles para asignar.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Períodos</label>
                                            <div class="col-sm-10">
                                                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                                    <?php for ($p = 1; $p <= $periodosMaximos; $p++): ?>
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" name="periodos[]" value="<?=$p;?>">
                                                                Período <?=$p;?>
                                                            </label>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                                <small class="text-muted">Seleccione los períodos en los que se aplicará este indicador.</small>
                                            </div>
                                        </div>

                                        <script>
                                        function toggleCargasEspecificas() {
                                            var tipo = document.querySelector('input[name="asignacionTipo"]:checked').value;
                                            var container = document.getElementById('cargasEspecificasContainer');
                                            if (tipo === 'especificas') {
                                                container.style.display = 'block';
                                            } else {
                                                container.style.display = 'none';
                                                // Desmarcar todos los checkboxes de cargas
                                                var checkboxes = container.querySelectorAll('input[type="checkbox"]:not([disabled])');
                                                checkboxes.forEach(function(cb) {
                                                    cb.checked = false;
                                                });
                                            }
                                        }
                                        
                                        // Validación antes de enviar el formulario (solo si está en uso)
                                        <?php if($enUso): ?>
                                        var indicadorEnUso = true;
                                        var nombreOriginal = '<?=addslashes($rCargas['ind_nombre']);?>';
                                        var valorOriginal = '<?=addslashes($rCargas['ind_valor']);?>';
                                        
                                        document.querySelector('form[name="formularioGuardar"]').addEventListener('submit', function(e) {
                                            // Verificar que los valores hidden sean los originales (protección adicional)
                                            var nombreHidden = document.querySelector('input[name="nombre"][type="hidden"]');
                                            var valorHidden = document.querySelector('input[name="valor"][type="hidden"]');
                                            
                                            if (nombreHidden && nombreHidden.value.trim() !== nombreOriginal) {
                                                e.preventDefault();
                                                alert('Este indicador está en uso y no se pueden modificar el nombre ni el valor. Solo se pueden asignar nuevas cargas.');
                                                nombreHidden.value = nombreOriginal;
                                                return false;
                                            }
                                            
                                            if (valorHidden && Math.abs(parseFloat(valorHidden.value) - parseFloat(valorOriginal)) > 0.0001) {
                                                e.preventDefault();
                                                alert('Este indicador está en uso y no se pueden modificar el nombre ni el valor. Solo se pueden asignar nuevas cargas.');
                                                valorHidden.value = valorOriginal;
                                                return false;
                                            }
                                        });
                                        <?php endif; ?>
                                        </script>

										
                                    <?php 
                                    // El botón siempre debe estar habilitado para permitir asignar nuevas cargas
                                    // incluso cuando el indicador está en uso
                                    $botones = new botonesGuardar("cargas-indicadores-obligatorios.php", true); 
                                    ?>
                                    </form>
                                </div>
                            </div>
                        </div>
						
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
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>