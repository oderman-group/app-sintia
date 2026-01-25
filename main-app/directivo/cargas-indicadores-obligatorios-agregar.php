<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0038';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademicaOptimizada.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Obtener solo las cargas activas que tienen car_indicadores_directivo=1
$selectSql = [
    "car.car_id",
    "car.car_periodo",
    "car.car_indicadores_directivo",
    "gra.gra_nombre",
    "gru.gru_nombre",
    "am.mat_nombre",
    "CONCAT_WS(' ', uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2) as docente_nombre"
];

$cargasConsulta = CargaAcademicaOptimizada::listarCargasOptimizado($conexion, $config, "", "AND car.car_activa=1 AND car.car_indicadores_directivo=1", "gra.gra_nombre, gru.gru_nombre, am.mat_nombre", "", "", array(), $selectSql);
$cargasLista = [];
while ($carga = mysqli_fetch_array($cargasConsulta, MYSQLI_BOTH)) {
    $cargasLista[] = $carga;
}

// Obtener períodos máximos
$periodosMaximos = $config['conf_periodos_maximos'] ?? 4;
?>

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
                                <div class="page-title">Crear Indicadores</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cargas-indicadores-obligatorios.php" onClick="deseaRegresar(this)">Indicadores Obligatorios</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Crear Indicadores</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
                        <div class="col-sm-12">


								<div class="panel">
									<header class="panel-heading panel-heading-purple">Crear Indicadores</header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="cargas-indicadores-obligatorios-guardar.php" method="post" enctype="multipart/form-data">
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="nombre" class="form-control" value="">
                                            </div>
                                        </div>	
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Valor</label>
                                            <div class="col-sm-2">
                                                <input type="text" name="valor" class="form-control" value="">
                                            </div>
                                            <span style="color:#F06; font-size:11px;">Estos valores m&aacute;s la suma de los indicadores que crear&aacute; el docente debe ser igual a 100.</span>
                                        </div>	

                                        <hr>
                                        <h4>Asignación a Cargas Académicas</h4>
                                        
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
                                                    <?php if (count($cargasLista) > 0): ?>
                                                        <?php foreach ($cargasLista as $carga): ?>
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="cargas[]" value="<?=$carga['car_id'];?>">
                                                                    <?=$carga['gra_nombre'];?> - <?=$carga['gru_nombre'];?> - <?=$carga['mat_nombre'];?> (<?=$carga['docente_nombre'];?>)
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted">No hay cargas académicas disponibles.</p>
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
                                                                <input type="checkbox" name="periodos[]" value="<?=$p;?>" checked>
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
                                                var checkboxes = container.querySelectorAll('input[type="checkbox"]');
                                                checkboxes.forEach(function(cb) {
                                                    cb.checked = false;
                                                });
                                            }
                                        }
                                        </script>

										
                                    <?php $botones = new botonesGuardar("cargas-indicadores-obligatorios.php",true); ?>
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