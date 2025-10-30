<?php
include("session.php");
$idPaginaInterna = 'DT0078';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");

Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
require_once("../class/Estudiantes.php");
require_once("../class/servicios/GradoServicios.php");

$idMatricula="";
if(!empty($_GET["id"])){
	$idMatricula=base64_decode($_GET["id"]);
	$datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($idMatricula);
} else if(!empty($_GET["idUsuario"])){ 
	$idUsuario=base64_decode($_GET["idUsuario"]);
	$datosEstudianteActual = Estudiantes::obtenerDatosEstudiantePorIdUsuario($idUsuario);
	$idMatricula=$datosEstudianteActual["mat_id"];
}

if( empty($datosEstudianteActual) ){
	echo '<script type="text/javascript">window.location.href="estudiantes.php?error=ER_DT_16";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
?>

	<!-- steps -->
	<link rel="stylesheet" href="../../config-general/assets/plugins/steps/steps.css"> 
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />

	<!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
	
	<style>
		/* ========================================
		   ESTILOS PARA BOTÓN DE ACCIONES
		   ======================================== */
		
		/* Botón de tres puntos verticales */
		.btn-acciones-menu {
			background: transparent;
			border: 1px solid #dee2e6;
			padding: 8px 12px;
			cursor: pointer;
			border-radius: 4px;
			transition: all 0.2s ease;
			font-size: 18px;
			color: #666;
		}
		
		.btn-acciones-menu:hover {
			background: #f5f5f5;
			color: #333;
			border-color: #667eea;
		}
		
		.btn-acciones-menu:active {
			background: #e0e0e0;
		}
		
		/* Panel flotante de acciones (estilo minimalista vertical) */
		.acciones-panel {
			display: none;
			position: fixed;
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 4px 20px rgba(0,0,0,0.15);
			border: 1px solid #e0e0e0;
			padding: 8px 0;
			min-width: 240px;
			max-width: 280px;
			max-height: 400px;
			overflow-y: auto;
			z-index: 10000;
			animation: slideIn 0.15s ease-out;
		}
		
		@keyframes slideIn {
			from {
				opacity: 0;
				transform: scale(0.95) translateY(-10px);
			}
			to {
				opacity: 1;
				transform: scale(1) translateY(0);
			}
		}
		
		.acciones-panel.show {
			display: block;
		}
		
		/* Lista vertical de opciones */
		.acciones-list {
			list-style: none;
			padding: 0;
			margin: 0;
		}
		
		/* Item de acción individual (estilo lista) */
		.accion-item {
			display: flex;
			align-items: center;
			padding: 12px 16px;
			cursor: pointer;
			transition: all 0.15s ease;
			text-decoration: none;
			color: #333;
			border-left: 3px solid transparent;
		}
		
		.accion-item:hover {
			background: #f8f9fa;
			border-left-color: #667eea;
			text-decoration: none;
			color: #333;
		}
		
		.accion-item:active {
			background: #e9ecef;
		}
		
		.accion-icon {
			width: 32px;
			height: 32px;
			border-radius: 6px;
			display: flex;
			align-items: center;
			justify-content: center;
			margin-right: 12px;
			font-size: 14px;
			color: #fff;
			flex-shrink: 0;
		}
		
		.accion-name {
			font-size: 14px;
			color: #333;
			font-weight: 400;
			line-height: 1.4;
			flex: 1;
		}
		
		.accion-item:hover .accion-name {
			font-weight: 500;
		}
		
		/* Overlay para cerrar el panel */
		.acciones-overlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 9999;
		}
		
		.acciones-overlay.show {
			display: block;
		}
		
		/* Scrollbar personalizado para el panel */
		.acciones-panel::-webkit-scrollbar {
			width: 6px;
		}
		
		.acciones-panel::-webkit-scrollbar-track {
			background: #f1f1f1;
			border-radius: 10px;
		}
		
		.acciones-panel::-webkit-scrollbar-thumb {
			background: #888;
			border-radius: 10px;
		}
		
		.acciones-panel::-webkit-scrollbar-thumb:hover {
			background: #555;
		}
	</style>

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">

    <script type="application/javascript">
		function validarEstudiante(enviada){
			var nDoct = enviada.value;
			var idEstudiante = <?php echo $datosEstudianteActual["mat_id"];?>;

			if(nDoct!=""){
				$('#nDocu').empty().hide().html("Validando documento...").show(1);

				datos = "nDoct="+(nDoct)+"&idEstudiante="+(idEstudiante);
					$.ajax({
					type: "POST",
					url: "ajax-estudiantes-editar.php",
					data: datos,
					success: function(data){
						$('#nDocu').empty().hide().html(data).show(1);
					}

				});

			}
		}
	</script>

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <!-- start header -->
		<?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
            <div class="page-content-wrapper">
                <div class="page-content">
                    <!-- Token CSRF para operaciones de eliminación -->
                    <?php echo Csrf::campoHTML(); ?>
                    
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">Editar matrículas</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="estudiantes.php" onClick="deseaRegresar(this)">Matrículas</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar matrículas</li>
                            </ol>
                        </div>
                    </div>

					<?php
					// Definir permisos para las acciones
					$permisoEditarEstudiante = Modulos::validarSubRol(['DT0078']);
					$permisoEditarUsuario = Modulos::validarSubRol(['DT0008']);
					$permisoCrearSion = Modulos::validarSubRol(['DT0280']);
					$permisoCambiarGrupo = Modulos::validarSubRol(['DT0083']);
					$permisoRetirar = Modulos::validarSubRol(['DT0074']);
					$permisoReservar = Modulos::validarSubRol(['DT0079']);
					$permisoEliminar = Modulos::validarSubRol(['DT0015']);
					$permisoCrearUsuario = Modulos::validarSubRol(['DT0017']);
					$permisoAutoLogin = Modulos::validarSubRol(['DT0006']);
					$permisoBoletines = Modulos::validarSubRol(['DT0101']);
					$permisoLibroMatricula = Modulos::validarSubRol(['DT0100']);
					$permisoInformeParcial = Modulos::validarSubRol(['DT0223']);
					$permisoHojaMatricula = Modulos::validarSubRol(['DT0099']);
					$permisoAspectos = Modulos::validarSubRol(['DT0122']);
					$permisoAdjuntarDocumento = Modulos::validarSubRol(['DT0292']);
					
					// Verificar si tiene grado y grupo
					$tieneGradoGrupo = !empty($datosEstudianteActual['mat_grado']) && !empty($datosEstudianteActual['mat_grupo']);
					
					// Determinar si es retirar o restaurar
					$retirarRestaurar = 'Retirar';
					if ($datosEstudianteActual['mat_estado_matricula'] == CANCELADO) {
						$retirarRestaurar = 'Restaurar';
					}
					?>
					
					<div class="row mb-3">
                    	<div class="col-sm-12">
							<div class="btn-group">
								<?php if(Modulos::validarPermisoEdicion()){?>
									<a href="estudiantes-agregar.php" id="addRow" class="btn deepPink-bgcolor">
										Agregar nuevo <i class="fa fa-plus"></i>
									</a>
								<?php }?>
								
								<!-- Botón de acciones del estudiante -->
								<button type="button" class="btn btn-info btn-acciones-menu" onclick="mostrarPanelAcciones(this, '<?= $datosEstudianteActual['mat_id']; ?>')" title="Más acciones">
									<i class="fa fa-ellipsis-v"></i> Acciones
								</button>
								
								<!-- Dropdown oculto con las opciones (para que el JS lo use) -->
								<div style="display: none;">
									<ul class="dropdown-menu" role="menu" id="Acciones_<?= $datosEstudianteActual['mat_id']; ?>">
										<?php if (Modulos::validarPermisoEdicion()) { ?>
											
											<?php if ($config['conf_id_institucion'] == ICOLVEN && $permisoCrearSion) { ?>
												<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Esta seguro que desea transferir este estudiante a SION?','question','estudiantes-crear-sion.php?id=<?= base64_encode($datosEstudianteActual['mat_id']); ?>')">Transferir a SION</a></li>
											<?php } ?>

											<?php if (!empty($datosEstudianteActual['uss_id']) && $permisoEditarUsuario) { ?>
												<li><a href="usuarios-editar.php?id=<?= base64_encode($datosEstudianteActual['uss_id']); ?>">Editar usuario</a></li>
											<?php } ?>

											<?php if ($tieneGradoGrupo && $permisoCambiarGrupo) { ?>
												<li><a href="javascript:void(0);" data-toggle="modal" onclick="cambiarGrupo('<?= base64_encode($datosEstudianteActual['mat_id']) ?>')">Cambiar de grupo</a></li>
											<?php } ?>
											
											<?php if ($permisoRetirar && !empty($datosEstudianteActual['mat_id'])) { ?>
												<li><a href="javascript:void(0);" data-toggle="modal" onclick="retirar('<?= base64_encode($datosEstudianteActual['mat_id']) ?>')"><?= $retirarRestaurar ?></a></li>
											<?php } ?>
											
											<?php if ($tieneGradoGrupo && $permisoReservar) { ?>
												<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Esta seguro que desea reservar el cupo para este estudiante?','question','estudiantes-reservar-cupo.php?idEstudiante=<?= base64_encode($datosEstudianteActual['mat_id']); ?>')">Reservar cupo</a></li>
											<?php } ?>

											<?php if ($permisoEliminar) { ?>
												<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Esta seguro de ejecutar esta acción?','question','estudiantes-eliminar.php?idE=<?= base64_encode($datosEstudianteActual["mat_id"]); ?>&idU=<?= base64_encode($datosEstudianteActual["uss_id"]); ?>')">Eliminar</a></li>
											<?php } ?>

											<?php if ($permisoCrearUsuario) { ?>
												<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Está seguro de ejecutar esta acción?','question','estudiantes-crear-usuario-estudiante.php?id=<?= base64_encode($datosEstudianteActual["mat_id"]); ?>')">Generar usuario</a></li>
											<?php } ?>

											<?php if (!empty($datosEstudianteActual['uss_usuario']) && $permisoAutoLogin) { ?>
												<li><a href="auto-login.php?user=<?= base64_encode($datosEstudianteActual['uss_id']); ?>&tipe=<?= base64_encode(4) ?>">Autologin</a></li>
											<?php } ?>

										<?php } ?>

										<?php if ($tieneGradoGrupo) { ?>
											<?php if ($permisoBoletines && ($datosEstudianteActual['mat_estado_matricula'] != NO_MATRICULADO && $datosEstudianteActual['mat_estado_matricula'] != EN_INSCRIPCION)) { ?>
												<?php 
												$formatoBoletin = !empty($datosEstudianteActual['gra_formato_boletin']) ? $datosEstudianteActual['gra_formato_boletin'] : 1;
												?>
												<li><a href="../compartido/matricula-boletin-curso-<?= $formatoBoletin; ?>.php?id=<?= base64_encode($datosEstudianteActual["mat_id"]); ?>&periodo=<?= base64_encode($config[2]); ?>" target="_blank">Boletín</a></li>
											<?php } ?>
											<?php if ($permisoLibroMatricula) { ?>
												<li><a href="../compartido/matricula-libro-curso-<?= $config['conf_libro_final'] ?>.php?id=<?= base64_encode($datosEstudianteActual["mat_id"]); ?>&periodo=<?= base64_encode($config[2]); ?>" target="_blank">Libro Final</a></li>
											<?php } ?>
											<?php if ($permisoInformeParcial) { ?>
												<li><a href="../compartido/informe-parcial.php?estudiante=<?= base64_encode($datosEstudianteActual["mat_id"]); ?>" target="_blank">Informe parcial</a></li>
											<?php } ?>
										<?php } ?>

										<?php if (!empty($datosEstudianteActual['mat_matricula']) && $permisoHojaMatricula) { ?>
											<li><a href="../compartido/matriculas-formato3.php?ref=<?= base64_encode($datosEstudianteActual["mat_matricula"]); ?>" target="_blank">Hoja de matrícula</a></li>
										<?php } ?>

										<?php if ($config['conf_id_institucion'] == ICOLVEN && !empty($datosEstudianteActual['mat_codigo_tesoreria'])) { ?>
											<li><a href="http://sion.icolven.edu.co/Services/ServiceIcolven.svc/GenerarEstadoCuenta/<?= $datosEstudianteActual['mat_codigo_tesoreria']; ?>/<?= date('Y'); ?>" target="_blank">SION - Estado de cuenta</a></li>
										<?php } ?>

										<?php if (!empty($datosEstudianteActual['uss_usuario'])) { ?>
											<?php if ($permisoAspectos) { ?>
												<li><a href="aspectos-estudiantiles.php?idR=<?= base64_encode($datosEstudianteActual['uss_id']); ?>">Ficha estudiantil</a></li>
											<?php } ?>
										<?php } ?>
										
										<?php if ($permisoAdjuntarDocumento) { ?>
											<li><a href="matriculas-adjuntar-documentos.php?id=<?= base64_encode($datosEstudianteActual['uss_id']); ?>&idMatricula=<?= base64_encode($datosEstudianteActual['mat_id']); ?>">Adjuntar documentos</a></li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</div>
					</div>

                    <span style="color: blue; font-size: 15px;" id="nDocu"></span>

                    <!-- Horizontal Tabs -->
                    <div class="row">
                    	<div class="col-sm-12">
       <?php include("../../config-general/mensajes-informativos.php"); ?>
       <?php
       if($config['conf_id_institucion'] == ICOLVEN){
        if(isset($_GET['msgsion']) AND $_GET['msgsion']!=''){
         $aler='alert-success';
         $mensajeSion=base64_decode($_GET['msgsion']);
         if(base64_decode($_GET['stadsion'])!=true){
          $aler='alert-danger';
         }
        ?>
         <div class="alert alert-block <?=$aler;?>">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <h4 class="alert-heading">SION!</h4>
          <p><?=$mensajeSion;?></p>
         </div>
        <?php
        }
       }
       if(isset($_GET['msgsintia'])){
        $aler='alert-success';
        if(base64_decode($_GET['stadsintia'])!=true){
        $aler='alert-danger';
        }
       ?>
       <div class="alert alert-block <?=$aler;?>">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <h4 class="alert-heading">SINTIA!</h4>
        <p><?=base64_decode($_GET['msgsintia']);?></p>
       </div>
       <?php }?>

                             <!-- Nav tabs -->
                             <ul class="nav nav-tabs" id="estudianteTabs" role="tablist">
                                 <li class="nav-item">
                                     <a class="nav-link active" id="formulario-tab" data-toggle="tab" href="#formulario" role="tab" aria-controls="formulario" aria-selected="true">Formulario</a>
                                 </li>
                                 <?php
                                 $mostrarMaterias = !empty($datosEstudianteActual['mat_grupo']) && in_array($datosEstudianteActual['mat_estado_matricula'], [1, 2]);
                                 if($mostrarMaterias){
                                 ?>
                                 <li class="nav-item">
                                     <a class="nav-link" id="materias-tab" data-toggle="tab" href="#materias" role="tab" aria-controls="materias" aria-selected="false">Materias</a>
                                 </li>
                                 <?php } ?>
                                 <?php if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_INSCRIPCIONES)){ ?>
                                 <li class="nav-item">
                                     <a class="nav-link" id="documentos-tab" data-toggle="tab" href="#documentos" role="tab" aria-controls="documentos" aria-selected="false">Documentos de Inscripción</a>
                                 </li>
                                 <?php } ?>
                             </ul>

                             <!-- Tab panes -->
                             <div class="tab-content" id="estudianteTabContent">
                                 <div class="tab-pane fade show active" id="formulario" role="tabpanel" aria-labelledby="formulario-tab">
                                     <div class="card-box">
                                         <div class="card-head">
                                             <header>Matrículas</header>
                                         </div>
                                         <div class="card-body">
                                         	<form name="example_advanced_form" id="example-advanced-form" action="estudiantes-actualizar.php" method="post" enctype="multipart/form-data">
           <input type="hidden" name="id" value="<?=$idMatricula;?>">
           <input type="hidden" name="idU" value="<?=$datosEstudianteActual["mat_id_usuario"];?>">

           <h3>Información personal</h3>
              <?php include("includes/info-personal.php");?>

           <h3>Información académica</h3>
           <?php include("includes/info-academica.php");?>

           <h3>Información del Acudiente</h3>
           <fieldset>
            <?php include("includes/acudiente-1.php");?>

            <?php include("includes/acudiente-2.php");?>

           </fieldset>

           </form>
                                         </div>
                                     </div>
                                 </div>

                                 <?php if($mostrarMaterias){ ?>
                                 <div class="tab-pane fade" id="materias" role="tabpanel" aria-labelledby="materias-tab">
                                     <div class="card-box">
                                         <div class="card-head">
                                             <header>Materias del Estudiante</header>
                                         </div>
                                         <div class="card-body">
                                             <div id="materias-content">
                                                 <div class="text-center">
                                                     <div class="spinner-border" role="status">
                                                         <span class="sr-only">Cargando...</span>
                                                     </div>
                                                     <p>Cargando materias...</p>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <?php } ?>

                                 <?php if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_INSCRIPCIONES)){ ?>
                                 <div class="tab-pane fade" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
                                     <div class="card-box">
                                         <div class="card-head">
                                             <header>Documentos de Inscripción</header>
                                         </div>
                                         <div class="card-body">
                                             <div id="documentos-content">
                                                 <div class="text-center">
                                                     <div class="spinner-border" role="status">
                                                         <span class="sr-only">Cargando...</span>
                                                     </div>
                                                     <p>Cargando documentos...</p>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <?php } ?>
                             </div>
                         </div>
                    </div>
					
					<div id="wizard" style="display: none;"></div>
                     
                </div>
            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <!-- start footer -->
        <?php include("../compartido/footer.php");?>
        <!-- end footer -->
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
	<script src="../../config-general/assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <!-- steps -->
    <script src="../../config-general/assets/plugins/steps/jquery.steps.js" ></script>
    <script src="../../config-general/assets/js/pages/steps/steps-data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>

	<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>

	<script>
		$(document).ready(function() {
			// Load subjects when the tab is clicked
			$('#materias-tab').on('shown.bs.tab', function (e) {
				loadMaterias();
			});

			function loadMaterias() {
			    var idEstudiante = '<?php echo base64_encode($idMatricula); ?>';
			    $('#materias-content').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Cargando...</span></div><p>Cargando materias...</p></div>');

			    $.ajax({
			        url: 'ajax-estudiantes-materias.php',
			        type: 'GET',
			        data: { idEstudiante: idEstudiante },
			        success: function(response) {
			            $('#materias-content').html(response);
			        },
			        error: function(xhr, status, error) {
			            $('#materias-content').html('<div class="alert alert-danger">Error al cargar las materias. Por favor, inténtelo de nuevo.</div>');
			            console.error('Error loading subjects:', error);
			        }
			    });
			}

			// Load documents when the tab is clicked
			$('#documentos-tab').on('shown.bs.tab', function (e) {
			    loadDocumentos();
			});

			window.loadDocumentos = function() {
			    var idEstudiante = '<?php echo base64_encode($idMatricula); ?>';
			    $('#documentos-content').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Cargando...</span></div><p>Cargando documentos...</p></div>');

			    $.ajax({
			        url: 'ajax-estudiantes-documentos.php',
			        type: 'GET',
			        data: { idEstudiante: idEstudiante },
			        success: function(response) {
			            var data = response;
			            if (data.success) {
			                var buttonHtml = '<button type="button" class="btn btn-primary mb-3" onclick="loadDocumentos()"><i class="fas fa-sync-alt"></i> Actualizar documentos</button>';
			                var tableHtml = '<table class="table table-striped table-bordered table-hover">';
			                tableHtml += '<thead><tr><th>Documento</th><th>Estado</th></tr></thead>';
			                tableHtml += '<tbody>';
			                for (var key in data.documentos) {
			                    var doc = data.documentos[key];
			                    tableHtml += '<tr>';
			                    tableHtml += '<td>' + doc.titulo + '</td>';
			                    if (doc.estado === 'Subido') {
			                        var fileUrl = (doc.titulo === "Comprobante de Pago") ? '../../main-app/admisiones/files/comprobantes/' + doc.archivo : '../../main-app/admisiones/files/otros/' + doc.archivo;
			                        tableHtml += '<td><a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-success">Descargar</a></td>';
			                    } else {
			                        tableHtml += '<td><span class="badge badge-warning">Pendiente</span></td>';
			                    }
			                    tableHtml += '</tr>';
			                }
			                tableHtml += '</tbody></table>';
			                $('#documentos-content').html(buttonHtml + tableHtml);
			            } else {
			                $('#documentos-content').html('<div class="alert alert-warning">' + data.message + '</div>');
			            }
			        },
			        error: function(xhr, status, error) {
			            alert('Error al cargar los documentos. Por favor, inténtelo de nuevo.');
			            console.error('Error loading documents:', error);
			        }
			    });
			}
});

	// ========================================
	// SISTEMA DE PANEL DE ACCIONES FLOTANTE
	// ========================================
	
	// Variable global para almacenar el panel actual
	window.currentAccionesPanel = null;
	
	// Función para mostrar el panel de acciones
	window.mostrarPanelAcciones = function(btn, estudianteId) {
		// Cerrar cualquier panel abierto
		cerrarPanelAcciones();
		
		// Crear el overlay
		var overlay = $('<div class="acciones-overlay show"></div>');
		$('body').append(overlay);
		
		// Obtener el contenido del dropdown correspondiente
		var dropdownMenu = $('#Acciones_' + estudianteId);
		if (!dropdownMenu.length) {
			console.error('No se encontró el menú de acciones para el estudiante:', estudianteId);
			return;
		}
		
		// Crear el panel
		var panel = $('<div class="acciones-panel show"></div>');
		var lista = $('<div class="acciones-list"></div>');
		
		// Mapeo de iconos y colores por acción (más sutiles)
		var accionesConfig = {
			'Editar matrícula': { icon: 'fa-edit', color: '#667eea' },
			'Edición rápida': { icon: 'fa-bolt', color: '#f5576c' },
			'Transferir a SION': { icon: 'fa-exchange-alt', color: '#00f2fe' },
			'Cambiar de grupo': { icon: 'fa-users', color: '#38f9d7' },
			'Editar usuario': { icon: 'fa-user-edit', color: '#fa709a' },
			'Retirar': { icon: 'fa-user-times', color: '#ee5a6f' },
			'Restaurar': { icon: 'fa-undo', color: '#96fbc4' },
			'Reservar cupo': { icon: 'fa-bookmark', color: '#fdbb2d' },
			'Eliminar': { icon: 'fa-trash', color: '#eb3349' },
			'Generar usuario': { icon: 'fa-user-plus', color: '#6a11cb' },
			'Autologin': { icon: 'fa-sign-in-alt', color: '#37ecba' },
			'Boletín': { icon: 'fa-file-alt', color: '#667eea' },
			'Libro Final': { icon: 'fa-book', color: '#a18cd1' },
			'Informe parcial': { icon: 'fa-chart-line', color: '#84fab0' },
			'Hoja de matrícula': { icon: 'fa-file-contract', color: '#ffecd2' },
			'SION - Estado de cuenta': { icon: 'fa-money-bill-wave', color: '#a1c4fd' },
			'Ficha estudiantil': { icon: 'fa-id-card', color: '#fccb90' },
			'Adjuntar documentos': { icon: 'fa-paperclip', color: '#e0c3fc' }
		};
		
		// Convertir los items del dropdown en items de lista vertical
		dropdownMenu.find('li').each(function() {
			var link = $(this).find('a');
			if (link.length) {
				var texto = link.text().trim();
				var href = link.attr('href');
				var onclick = link.attr('onclick');
				
				// Buscar configuración de icono
				var config = null;
				for (var key in accionesConfig) {
					if (texto.includes(key)) {
						config = accionesConfig[key];
						break;
					}
				}
				
				// Configuración por defecto si no se encuentra
				if (!config) {
					config = { icon: 'fa-cog', color: '#95a5a6' };
				}
				
				// Crear el item
				var item = $('<a class="accion-item"></a>');
				if (href && href !== 'javascript:void(0);') {
					item.attr('href', href);
					if (link.attr('target')) {
						item.attr('target', link.attr('target'));
					}
				} else if (onclick) {
					item.attr('href', 'javascript:void(0);');
					item.attr('onclick', onclick);
				}
				
				// Icono con color sólido
				var iconDiv = $('<div class="accion-icon"></div>').css('background', config.color);
				iconDiv.html('<i class="fa ' + config.icon + '"></i>');
				
				var nameSpan = $('<span class="accion-name"></span>').text(texto);
				
				item.append(iconDiv).append(nameSpan);
				
				// Al hacer clic, cerrar el panel
				item.on('click', function() {
					cerrarPanelAcciones();
				});
				
				lista.append(item);
			}
		});
		
		panel.append(lista);
		
		// Posicionar el panel cerca del botón
		var btnOffset = $(btn).offset();
		var btnHeight = $(btn).outerHeight();
		var btnWidth = $(btn).outerWidth();
		
		// Agregar el panel al body temporalmente para obtener sus dimensiones
		$('body').append(panel);
		
		var panelWidth = panel.outerWidth();
		var panelHeight = panel.outerHeight();
		var windowWidth = $(window).width();
		var windowHeight = $(window).height();
		
		// Calcular posición óptima (debajo del botón)
		var topPos = btnOffset.top + btnHeight + 5;
		var leftPos = btnOffset.left;
		
		// Ajustar si se sale por la derecha
		if (leftPos + panelWidth > windowWidth - 20) {
			leftPos = windowWidth - panelWidth - 20;
		}
		
		// Ajustar si se sale por la izquierda
		if (leftPos < 20) {
			leftPos = 20;
		}
		
		// Ajustar verticalmente si se sale por abajo
		if (topPos + panelHeight > windowHeight - 20) {
			topPos = btnOffset.top - panelHeight - 5;
		}
		
		// Ajustar verticalmente si se sale por arriba
		if (topPos < 20) {
			topPos = 20;
		}
		
		// Aplicar posición
		panel.css({
			top: topPos + 'px',
			left: leftPos + 'px'
		});
		
		// Guardar referencia
		window.currentAccionesPanel = panel;
		
		// Cerrar al hacer clic en el overlay
		overlay.on('click', cerrarPanelAcciones);
	};
	
	// Función para cerrar el panel
	window.cerrarPanelAcciones = function() {
		$('.acciones-overlay').remove();
		if (window.currentAccionesPanel) {
			window.currentAccionesPanel.remove();
			window.currentAccionesPanel = null;
		}
	};
	
	// Cerrar al hacer scroll
	$(window).on('scroll', function() {
		if (window.currentAccionesPanel) {
			cerrarPanelAcciones();
		}
	});
	
	// Cerrar al presionar ESC
	$(document).on('keydown', function(e) {
		if (e.key === 'Escape' && window.currentAccionesPanel) {
			cerrarPanelAcciones();
		}
	});
	
	// ========================================
	// FUNCIONES PARA ABRIR MODALES
	// ========================================
	
	// Función para abrir el modal de cambiar grupo
	function cambiarGrupo(idMatricula) {
		var titulo = 'Cambiar de Grupo';
		var url = 'estudiantes-cambiar-grupo-modal.php';
		var data = { id: idMatricula };
		abrirModal(titulo, url, data, null, '95%');
	}
	
	// Función para abrir el modal de retirar/restaurar estudiante
	function retirar(idMatricula) {
		var titulo = 'Retirar / Restaurar Estudiante';
		var url = 'estudiantes-retirar-modal.php';
		var data = { id: idMatricula };
		abrirModal(titulo, url, data, null, '800px');
	}
	</script>

	   <!-- end js include path -->
	   
	   <!-- Funciones JS globales -->
	   <script src="../compartido/funciones.js"></script>
	   
	   <!-- Modal Centralizado -->
	   <?php include("../compartido/modal-centralizado.php"); ?>

</body>

</html>