<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0018';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Areas.php");

$parametrosObligatorios =["id"];

Utilidades::validarParametros($_GET,$parametrosObligatorios);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}

$rCargas = Areas::traerDatosArea($config, base64_decode($_GET["id"]));
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
                                <div class="page-title">Editar Areas</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="areas.php" onClick="deseaRegresar(this)"><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar Areas</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
                        <div class="col-sm-12">
                                <?php include("../../config-general/mensajes-informativos.php"); ?>


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">
									<form name="formularioGuardar" action="areas-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" value="<?=base64_decode($_GET["id"])?>" name="idA">
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre del Areas</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="nombreA" class="form-control" value="<?=$rCargas["ar_nombre"] ?>" <?=$disabledPermiso;?> required>
                                            </div>
                                        </div>	
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Posición</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="posicionA" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
                                                    $cPosicionA = Areas::traerAreasInstitucion($config, $rCargas["ar_id"]);
                                                    $numDatos=mysqli_num_rows($cPosicionA);
                                                    $cont=0;
                                                    while($rPos=mysqli_fetch_array($cPosicionA, MYSQLI_BOTH)){
                                                        $cont++;
                                                        $posciones[$cont]=$rPos["ar_posicion"];
                                                    }
                                                    $cond=0;
                                                    $exist=0;
                                                    for($i=1;$i<=(20+$cond);$i++){
                                                        if($numDatos>0){
                                                            for($j=0;$j<=count($posciones);$j++){
                                                                if($i==$posciones[$j]){
                                                                    $exist=1;
                                                                } 
                                                            }
                                                        }
                                                        if($exist!=1){
                                                            if($rCargas["ar_posicion"]==$i){
                                                            echo '<option value="'.$i.'" selected>'.$i.'</option>';	
                                                            }else{
                                                               echo '<option value="'.$i.'">'.$i.'</option>';  
                                                            }
                                                        }else{
                                                        $cond++;
                                                        }
                                                        $exist=0;
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                    <?php $botones = new botonesGuardar("areas.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
						
						<!-- Panel de Materias del Área -->
						<div class="col-sm-12">
							<div class="panel">
								<header class="panel-heading panel-heading-blue">Materias de esta Área</header>
								<div class="panel-body">
									<div class="row">
										<!-- Materias del Área Actual -->
										<div class="col-md-6">
											<h6 class="text-primary mb-3">
												<i class="fa fa-book"></i> Materias en "<?= $rCargas["ar_nombre"]; ?>"
											</h6>
											<div id="materias-area-actual" class="materias-container border rounded p-3" 
												 style="min-height: 300px; background-color: #f8f9fa;"
												 data-area-id="<?= htmlspecialchars(base64_decode($_GET["id"]), ENT_QUOTES); ?>">
												<p class="text-muted text-center" id="mensaje-vacio-actual">
													<i class="fa fa-spinner fa-spin"></i> Cargando materias...
												</p>
											</div>
										</div>
										
										<!-- Materias de Otras Áreas -->
										<div class="col-md-6">
											<h6 class="text-success mb-3">
												<i class="fa fa-exchange"></i> Materias de Otras Áreas
											</h6>
											<div class="form-group">
												<label>Filtrar por área:</label>
												<select class="form-control" id="filtro-area">
													<option value="">Todas las áreas</option>
													<?php
													$areasConsulta = Areas::traerAreasInstitucion($config);
													while($area = mysqli_fetch_array($areasConsulta, MYSQLI_BOTH)) {
														if ($area['ar_id'] != base64_decode($_GET["id"])) {
															echo '<option value="'.$area['ar_id'].'">'.$area['ar_nombre'].'</option>';
														}
													}
													?>
												</select>
											</div>
											<div id="materias-otras-areas" class="materias-container border rounded p-3" 
												 style="min-height: 300px; background-color: #fff;">
												<p class="text-muted text-center" id="mensaje-vacio-otras">
													<i class="fa fa-spinner fa-spin"></i> Cargando materias...
												</p>
											</div>
										</div>
									</div>
									
									<div class="alert alert-info mt-3">
										<i class="fa fa-info-circle"></i> 
										<strong>Instrucciones:</strong> 
										<ul class="mb-0">
											<li>Arrastra materias desde la <strong>derecha</strong> hacia la <strong>izquierda</strong> para agregarlas a esta área.</li>
											<li>Arrastra materias desde la <strong>izquierda</strong> hacia la <strong>derecha</strong> para quitarlas de esta área (volverán a su área original).</li>
											<li>Los cambios se guardan automáticamente al soltar la materia.</li>
											<li>Cada materia solo puede pertenecer a un área a la vez.</li>
										</ul>
									</div>
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
    
    <!-- Sortable.js para drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <style>
    .materia-item {
        background: white;
        padding: 12px 15px;
        margin-bottom: 10px;
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        cursor: move;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .materia-item:hover {
        border-color: #007bff;
        box-shadow: 0 4px 8px rgba(0,123,255,0.15);
        transform: translateY(-2px);
    }
    
    .materia-item.sortable-ghost {
        opacity: 0.4;
        background: #f0f0f0;
    }
    
    .materia-item.sortable-drag {
        opacity: 1;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    
    .materia-item .materia-nombre {
        flex: 1;
        font-weight: 500;
        color: #333;
    }
    
    .materia-item .materia-codigo {
        background: #e3f2fd;
        color: #1976d2;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.85em;
        margin-right: 10px;
        font-weight: 600;
    }
    
    .materia-item .materia-area {
        background: #f3e5f5;
        color: #7b1fa2;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8em;
        margin-left: 10px;
    }
    
    .materia-item .drag-handle {
        color: #999;
        margin-right: 10px;
        cursor: move;
    }
    
    .materias-container {
        transition: background-color 0.3s ease;
    }
    
    .materias-container.drag-over {
        background-color: #e3f2fd !important;
        border-color: #007bff !important;
    }
    
    .mensaje-vacio {
        text-align: center;
        padding: 40px;
        color: #999;
    }
    </style>
    
    <script>
    var areaActualId = '<?= base64_decode($_GET["id"]); ?>';
    var materiasPorArea = {};
    
    // Cargar materias al iniciar
    $(document).ready(function() {
        cargarMateriasAreaActual();
        cargarMateriasOtrasAreas();
        
        // Event listener para el filtro de áreas
        $('#filtro-area').on('change', function() {
            cargarMateriasOtrasAreas();
        });
    });
    
    // Función para cargar materias del área actual
    function cargarMateriasAreaActual() {
        console.log('Cargando materias del área:', areaActualId);
        $.ajax({
            url: 'ajax-obtener-materias-area.php',
            method: 'POST',
            data: { area_id: areaActualId },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta materias área actual:', response);
                if (response.success) {
                    renderizarMaterias(response.materias, '#materias-area-actual', 'actual');
                    inicializarDragAndDrop();
                } else {
                    $('#materias-area-actual').html('<p class="mensaje-vacio">Error: ' + (response.message || 'Error desconocido') + '</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar materias área actual:', error);
                console.log('Respuesta del servidor:', xhr.responseText);
                $('#materias-area-actual').html('<p class="mensaje-vacio">Error de conexión: ' + error + '</p>');
            }
        });
    }
    
    // Función para cargar materias de otras áreas
    function cargarMateriasOtrasAreas() {
        var filtroArea = $('#filtro-area').val();
        console.log('Cargando materias de otras áreas, filtro:', filtroArea);
        
        $.ajax({
            url: 'ajax-obtener-materias-area.php',
            method: 'POST',
            data: { 
                area_id: areaActualId, 
                otras_areas: true,
                filtro_area: filtroArea
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta materias otras áreas:', response);
                if (response.success) {
                    renderizarMaterias(response.materias, '#materias-otras-areas', 'otras');
                    inicializarDragAndDrop();
                } else {
                    $('#materias-otras-areas').html('<p class="mensaje-vacio">Error: ' + (response.message || 'Error desconocido') + '</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar materias otras áreas:', error);
                console.log('Respuesta del servidor:', xhr.responseText);
                $('#materias-otras-areas').html('<p class="mensaje-vacio">Error de conexión: ' + error + '</p>');
            }
        });
    }
    
    // Función para renderizar materias
    function renderizarMaterias(materias, contenedor, tipo) {
        var html = '';
        
        if (materias.length === 0) {
            if (tipo === 'actual') {
                html = '<p class="mensaje-vacio"><i class="fa fa-info-circle"></i> No hay materias en esta área. Arrastra materias desde la derecha para agregarlas.</p>';
            } else {
                html = '<p class="mensaje-vacio"><i class="fa fa-info-circle"></i> No hay materias en otras áreas.</p>';
            }
        } else {
            materias.forEach(function(materia) {
                html += '<div class="materia-item" data-materia-id="' + materia.mat_id + '" data-area-id="' + materia.mat_area + '">';
                html += '   <i class="fa fa-bars drag-handle"></i>';
                html += '   <span class="materia-codigo">' + materia.mat_id + '</span>';
                html += '   <span class="materia-nombre">' + materia.mat_nombre + '</span>';
                if (materia.ar_nombre) {
                    html += '   <span class="materia-area">' + materia.ar_nombre + '</span>';
                }
                html += '</div>';
            });
        }
        
        $(contenedor).html(html);
    }
    
    // Función para inicializar drag and drop con Sortable.js
    function inicializarDragAndDrop() {
        // Destruir instancias anteriores si existen
        if (window.sortableActual) window.sortableActual.destroy();
        if (window.sortableOtras) window.sortableOtras.destroy();
        
        var contenedorActual = document.getElementById('materias-area-actual');
        var contenedorOtras = document.getElementById('materias-otras-areas');
        
        if (contenedorActual && contenedorOtras) {
            // Configuración común para ambos contenedores
            var opcionesComunes = {
                animation: 200,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                handle: '.drag-handle',
                group: 'materias',
                onEnd: function(evt) {
                    moverMateria(evt);
                }
            };
            
            window.sortableActual = Sortable.create(contenedorActual, opcionesComunes);
            window.sortableOtras = Sortable.create(contenedorOtras, opcionesComunes);
        }
    }
    
    // Función para mover materia entre áreas
    function moverMateria(evt) {
        var materiaId = evt.item.getAttribute('data-materia-id');
        var areaOriginalMateria = evt.item.getAttribute('data-area-id'); // El área que tiene la materia actualmente
        
        console.log('Moviendo materia ID:', materiaId);
        console.log('Área original de la materia:', areaOriginalMateria);
        console.log('Desde contenedor:', evt.from.id);
        console.log('Hacia contenedor:', evt.to.id);
        
        // Si es el mismo contenedor, no hacer nada
        if (evt.from === evt.to) {
            console.log('Mismo contenedor, no hacer nada');
            return;
        }
        
        var nuevaAreaId;
        
        // Si se mueve AL área actual (de derecha a izquierda)
        if (evt.to.id === 'materias-area-actual') {
            nuevaAreaId = areaActualId;
            console.log('Moviendo materia AL área actual:', nuevaAreaId);
        } 
        // Si se mueve DESDE el área actual a otras áreas (de izquierda a derecha)
        else if (evt.from.id === 'materias-area-actual') {
            // Al mover fuera del área actual, la materia queda sin área (NULL) o vuelve a su área original
            // Por ahora vamos a dejarlo sin área (NULL)
            nuevaAreaId = null;
            console.log('Moviendo materia FUERA del área actual, quedará sin área');
            
            // Advertir al usuario
            if (!confirm('¿Deseas quitar esta materia del área actual? La materia quedará sin área asignada.')) {
                // Recargar para revertir el cambio visual
                cargarMateriasAreaActual();
                cargarMateriasOtrasAreas();
                return;
            }
        }
        
        // Mostrar indicador de carga
        $(evt.item).css('opacity', '0.5');
        
        // Actualizar en la base de datos
        $.ajax({
            url: 'ajax-mover-materia-area.php',
            method: 'POST',
            data: {
                materia_id: materiaId,
                nueva_area_id: nuevaAreaId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Mostrar notificación de éxito
                    $.toast({
                        heading: 'Éxito',
                        text: response.message || 'Materia reasignada correctamente',
                        position: 'top-right',
                        loaderBg: '#26c281',
                        icon: 'success',
                        hideAfter: 3000
                    });
                    
                    // Recargar ambas listas
                    cargarMateriasAreaActual();
                    cargarMateriasOtrasAreas();
                } else {
                    // Mostrar error y revertir
                    $.toast({
                        heading: 'Error',
                        text: response.message || 'No se pudo reasignar la materia',
                        position: 'top-right',
                        loaderBg: '#bf441d',
                        icon: 'error',
                        hideAfter: 5000
                    });
                    
                    // Recargar listas para revertir cambio visual
                    cargarMateriasAreaActual();
                    cargarMateriasOtrasAreas();
                }
            },
            error: function() {
                $.toast({
                    heading: 'Error',
                    text: 'Error de conexión. Intente nuevamente.',
                    position: 'top-right',
                    loaderBg: '#bf441d',
                    icon: 'error',
                    hideAfter: 5000
                });
                
                // Recargar listas
                cargarMateriasAreaActual();
                cargarMateriasOtrasAreas();
            }
        });
    }
    </script>
    
    <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>