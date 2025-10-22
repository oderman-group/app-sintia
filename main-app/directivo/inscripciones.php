<?php
include("session.php");
$idPaginaInterna = 'DT0102';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Inscripciones.php");

$configAdmisiones=Inscripciones::configuracionAdmisiones($conexion,$baseDatosAdmisiones,$config['conf_id_institucion'],$_SESSION["bd"]);

$urlInscripcion=REDIRECT_ROUTE.'/admisiones/';
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css"/>
	<!-- select2 -->
	<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
	<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
	<style>
		.expand-btn {
			transition: all 0.3s ease;
			padding: 4px 8px;
			font-size: 14px;
		}
		
		.expand-btn:hover {
			text-decoration: none;
			transform: scale(1.2);
		}
		
		.expand-btn:focus {
			outline: none;
			box-shadow: none;
		}
		
		.expandable-row {
			background-color: #f8f9fa !important;
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
                                <div class="page-title"><?=$frases[390][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descripción de la página -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p class="text-muted" style="font-size: 14px; line-height: 1.6;">
                                <i class="fa fa-info-circle text-info"></i> 
                                Administra las solicitudes de inscripción de nuevos estudiantes. Aquí puedes revisar, aprobar o rechazar solicitudes, gestionar la documentación y realizar el proceso de admisión. 
                                Utiliza los filtros para buscar por estado, año o grado. Expande cada registro para ver toda la información del aspirante y su acudiente.
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">
                                           
                                <!-- Mensajes informativos colapsables -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-warning text-white" style="cursor: pointer; padding: 10px 15px;" data-toggle="collapse" data-target="#alertaDisco">
                                                <i class="fa fa-database"></i> 
                                                <strong>Libera espacio en el disco</strong>
                                                <i class="fa fa-chevron-down pull-right"></i>
                                            </div>
                                            <div id="alertaDisco" class="collapse">
                                                <div class="card-body">
                                                    <p class="mb-2">Recomendamos descargar la documentación y comprobante de pago de cada aspirante y luego borrar esa documentación del sistema para evitar que el disco se llene más rápido.</p>
                                                    <p class="mb-0"><strong>Instrucción:</strong> En cada aspirante en estado <span class="badge badge-success">Aprobado</span>, ve al botón <strong>Acciones</strong> → <strong>Borrar documentación</strong>.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-success text-white" style="cursor: pointer; padding: 10px 15px;" data-toggle="collapse" data-target="#alertaEnlace">
                                                <i class="fa fa-link"></i> 
                                                <strong>Enlace de inscripción</strong>
                                                <i class="fa fa-chevron-down pull-right"></i>
                                            </div>
                                            <div id="alertaEnlace" class="collapse">
                                                <div class="card-body">
                                                    <p class="mb-2">Para ir al formulario de inscripción <a href="<?=$urlInscripcion?>" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-external-link"></i> Abrir formulario</a></p>
                                                    <label class="mb-1"><strong>Copiar enlace para enviar:</strong></label>
                                                    <div class="input-group">
                                                        <input type="text" id="enlaceInscripcion" class="form-control" value="<?=$urlInscripcion?>" readonly>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="copiarEnlace()" title="Copiar enlace">
                                                                <i class="fa fa-copy"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    $filtro="";
                                    include(ROOT_PATH."/config-general/config-admisiones.php");
                                    include(ROOT_PATH."/config-general/mensajes-informativos.php");
                                    // Barra superior antigua - removida
                                    // include("includes/barra-superior-inscripciones-componente.php");
                                    
                                    // Asegurar que las variables necesarias estén disponibles
                                    if (!isset($estadosSolicitud)) {
                                        $estadosSolicitud = [
                                            1 => 'Pendiente',
                                            2 => 'En proceso',
                                            3 => 'En revisión',
                                            4 => 'Rechazada',
                                            5 => 'Cancelada',
                                            6 => 'Aprobada',
                                            7 => 'Otro'
                                        ];
                                    }
                                ?>

                                    <?php if (isset($_GET["msg"]) and base64_decode($_GET["msg"]) == 1) { ?>
                                    <div class="alert alert-block alert-success">
                                        <h4 class="alert-heading">Documentación eliminada!</h4>
                                        <p>La documentación del aspirante se ha borrado correctamente.</p>
                                    </div>
                                    <?php } ?>

                                    <?php if (isset($_GET["msg"]) and base64_decode($_GET["msg"]) == 2) { ?>
                                    <div class="alert alert-block alert-success">
                                        <h4 class="alert-heading">Apisrante eliminado!</h4>
                                        <p>El aspirante se ha borrado correctamente.</p>
                                    </div>
                                    <?php } ?>

                                    <?php if (isset($_GET["msg"]) and base64_decode($_GET["msg"]) == 3) { ?>
                                    <div class="alert alert-block alert-success">
                                        <h4 class="alert-heading">Apisrante ocultado!</h4>
                                        <p>El aspirante se ha ocultado correctamente.</p>
                                    </div>
                                    <?php } ?>
                                    
                                    <!-- Barra de herramientas superior -->
                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <div class="d-flex justify-content-end align-items-center">
                                                <!-- Botón de filtros -->
                                                <button type="button" class="btn btn-outline-secondary" id="btnToggleFiltrosInscripciones">
                                                    <i class="fa fa-filter"></i> Filtros y Búsqueda
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Panel de Filtros Colapsable -->
                                    <div class="card card-topline-purple mb-3" id="cardFiltrosInscripciones" style="display: none;">
                                        <div class="card-body">
                                            <h5 class="mb-3"><i class="fa fa-filter"></i> Filtros Avanzados y Búsqueda</h5>
                                            
                                            <!-- Buscador General -->
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label><i class="fa fa-search"></i> Búsqueda General</label>
                                                        <div class="input-group">
                                                            <input type="text" id="filtro_inscripciones_busqueda" class="form-control" placeholder="Buscar por nombre, documento, email del acudiente...">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-primary" type="button" id="btnBuscarInscripciones">
                                                                    <i class="fa fa-search"></i> Buscar
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            Busca por: nombres del aspirante, documento, email del acudiente, nombre del acudiente
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <hr>
                                            
                                            <!-- Filtros por categoría -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><i class="fa fa-graduation-cap"></i> Grado</label>
                                                        <select id="filtro_inscripciones_grado" class="form-control select2-multiple-inscripciones" multiple="multiple" style="width: 100%;">
                                                            <?php
                                                            $grados = Grados::listarGrados(1);
                                                            while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
                                                            ?>
                                                                <option value="<?=$grado['gra_id'];?>"><?=$grado['gra_nombre'];?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><i class="fa fa-info-circle"></i> Estado de Solicitud</label>
                                                        <select id="filtro_inscripciones_estado" class="form-control select2-multiple-inscripciones" multiple="multiple" style="width: 100%;">
                                                            <?php foreach($estadosSolicitud as $key => $value) {?>
                                                                <option value="<?=$key;?>"><?=$value;?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><i class="fa fa-calendar"></i> Año</label>
                                                        <select id="filtro_inscripciones_anio" class="form-control select2-multiple-inscripciones" multiple="multiple" style="width: 100%;">
                                                            <?php 
                                                            $anioActual = date('Y');
                                                            for($i = $anioActual; $i >= $anioActual - 3; $i--) {
                                                            ?>
                                                                <option value="<?=$i;?>"><?=$i;?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12 text-right">
                                                    <button type="button" class="btn btn-secondary" id="btnLimpiarFiltrosInscripciones">
                                                        <i class="fa fa-eraser"></i> Limpiar Filtros
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[390][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        
                                        <div class="table">
                                    		<table  id="example1" class="display" style="width:100%;">
                                            <div id="gifCarga" class="gif-carga">
										        <img   alt="Cargando...">
									        </div>
												<thead>
													<tr>
                                                        <th></th>
                                                        <th>No.</th>
                                                        <th>ID Matrícula</th>
                                                        <th>#Solicitud</th>
                                                        <th>Fecha</th>
                                                        <th>Documento</th>
                                                        <th>Aspirante</th>
                                                        <th>Año</th>
                                                        <th>Estado</th>
                                                        <th>Comprobante</th>
                                                        <th>Grado</th>
                                                        <th>Acciones</th>
													</tr>
												</thead>
                                                <tbody id="inscripciones_result">
                                                <?php
                                                    include("includes/consulta-paginacion-inscripciones.php");
                                                    $selectSql = ["mat_id","mat_documento","gra_nombre",
																  "asp_observacion","asp_nombre_acudiente","asp_celular_acudiente",
																  "asp_documento_acudiente","asp_id","asp_fecha","asp_comprobante","mat_nombres",
																  "asp_agno","asp_email_acudiente","asp_estado_solicitud", "mat_nombre2", "mat_primer_apellido", "mat_segundo_apellido",
																  "mat.*", "asp.*"];

                                                    $filtroLimite = '';
                                                    
                                                    $consulta = Estudiantes::listarMatriculasAspirantes($config, $filtro, $filtroLimite,"",$selectSql);
                                                    
                                                    // Construir array de datos manualmente
                                                    $data = ["data" => []];
                                                    if (!empty($consulta)) {
                                                        while ($fila = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                                                            $data["data"][] = $fila;
                                                        }
                                                    }
													
                                                    include("../class/componentes/result/inscripciones-tbody.php");
                                                 ?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                      				    <!-- <?php include("enlaces-paginacion.php");?> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                function crearDatos(dato) {
                    console.log(dato);
            };
            </script>
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
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- select2 -->
	<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
    <!-- end js include path -->

    <script>
		$(function () {
			$('[data-toggle="popover"]').popover();
		});

		$('.popover-dismiss').popover({trigger: 'focus'});
		
		// Función para copiar el enlace de inscripción
		function copiarEnlace() {
			var enlaceInput = document.getElementById('enlaceInscripcion');
			enlaceInput.select();
			enlaceInput.setSelectionRange(0, 99999); // Para móviles
			
			try {
				document.execCommand('copy');
				$.toast({
					heading: 'Copiado',
					text: 'Enlace copiado al portapapeles',
					position: 'top-right',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 2000
				});
			} catch (err) {
				alert('Error al copiar: ' + err);
			}
		}
		
		// Cambiar icono del chevron al expandir/colapsar
		$('[data-toggle="collapse"]').on('click', function() {
			var icon = $(this).find('.fa-chevron-down, .fa-chevron-up');
			setTimeout(function() {
				if (icon.hasClass('fa-chevron-down')) {
					icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
				} else {
					icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
				}
			}, 10);
		});
		
		// === Filtros Avanzados para Inscripciones ===
		
		// Toggle del panel de filtros
		$('#btnToggleFiltrosInscripciones').on('click', function() {
			const card = $('#cardFiltrosInscripciones');
			const icon = $(this).find('i');
			
			if (card.is(':visible')) {
				card.slideUp(300);
				icon.removeClass('fa-chevron-up').addClass('fa-filter');
				$(this).removeClass('btn-primary').addClass('btn-outline-secondary');
			} else {
				card.slideDown(300);
				icon.removeClass('fa-filter').addClass('fa-chevron-up');
				$(this).removeClass('btn-outline-secondary').addClass('btn-primary');
			}
		});
		
		// Inicializar Select2 en los filtros
		$('.select2-multiple-inscripciones').select2({
			placeholder: "Seleccione una o más opciones",
			allowClear: true,
			language: {
				noResults: function() {
					return "No se encontraron resultados";
				},
				searching: function() {
					return "Buscando...";
				}
			}
		});
		
		// Función para aplicar filtros de inscripciones
		function aplicarFiltrosInscripciones() {
			const grados = $('#filtro_inscripciones_grado').val() || [];
			const estados = $('#filtro_inscripciones_estado').val() || [];
			const anios = $('#filtro_inscripciones_anio').val() || [];
			const busqueda = $('#filtro_inscripciones_busqueda').val() || '';
			
			console.log('Aplicando filtros inscripciones:', { grados, estados, anios, busqueda });
			
			// Mostrar loader
			$('#inscripciones_result').html('<tr><td colspan="12" class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Cargando...</td></tr>');
			
			// Enviar AJAX
			$.ajax({
				url: 'ajax-filtrar-inscripciones.php',
				type: 'POST',
				data: {
					grados: grados,
					estados: estados,
					anios: anios,
					busqueda: busqueda
				},
				dataType: 'json',
				success: function(response) {
					console.log('Respuesta del filtro inscripciones:', response);
					
					if (response.success) {
						// Insertar el HTML
						$('#inscripciones_result').html(response.html);
						
						// Mensaje dinámico
						let mensaje = 'Se encontraron ' + response.total + ' inscripción/inscripciones';
						if (busqueda.trim() !== '') {
							mensaje += ' para: "' + busqueda + '"';
						}
						
						// Mostrar mensaje de resultados
						$.toast({
							heading: 'Filtros Aplicados',
							text: mensaje,
							position: 'top-right',
							loaderBg: '#26c281',
							icon: 'success',
							hideAfter: 3000
						});
					} else {
						console.error('Error del servidor:', response.error);
						
						$.toast({
							heading: 'Error',
							text: response.error || 'Error al aplicar filtros',
							position: 'top-right',
							loaderBg: '#bf441d',
							icon: 'error',
							hideAfter: 5000
						});
						
						$('#inscripciones_result').html('<tr><td colspan="12" class="text-center text-danger">Error al cargar los datos</td></tr>');
					}
				},
				error: function(xhr, status, error) {
					console.error('Error AJAX inscripciones:', status, error);
					console.error('Response:', xhr.responseText);
					
					$.toast({
						heading: 'Error de Conexión',
						text: 'No se pudo conectar con el servidor',
						position: 'top-right',
						loaderBg: '#bf441d',
						icon: 'error',
						hideAfter: 5000
					});
					
					$('#inscripciones_result').html('<tr><td colspan="12" class="text-center text-danger">Error de conexión</td></tr>');
				}
			});
		}
		
		// Botón de buscar
		$('#btnBuscarInscripciones').on('click', function() {
			aplicarFiltrosInscripciones();
		});
		
		// Enter en el campo de búsqueda
		$('#filtro_inscripciones_busqueda').on('keypress', function(e) {
			if (e.which === 13) { // Enter key
				e.preventDefault();
				aplicarFiltrosInscripciones();
			}
		});
		
		// Limpiar filtros de inscripciones
		$('#btnLimpiarFiltrosInscripciones').on('click', function() {
			$('#filtro_inscripciones_grado').val(null).trigger('change');
			$('#filtro_inscripciones_estado').val(null).trigger('change');
			$('#filtro_inscripciones_anio').val(null).trigger('change');
			$('#filtro_inscripciones_busqueda').val('');
			
			// Recargar la página para mostrar todas las inscripciones
			location.reload();
		});
		
		// Aplicar filtros automáticamente al cambiar las opciones
		$('.select2-multiple-inscripciones').on('change', function() {
			clearTimeout(window.filtroInscripcionesTimeout);
			window.filtroInscripcionesTimeout = setTimeout(function() {
				aplicarFiltrosInscripciones();
			}, 500);
		});
	</script>
	
	<style>
	/* Estilos para las alertas colapsables */
	.card-header[data-toggle="collapse"] {
		transition: background-color 0.3s ease;
	}
	
	.card-header[data-toggle="collapse"]:hover {
		opacity: 0.9;
	}
	
	.card-header .fa-chevron-down,
	.card-header .fa-chevron-up {
		transition: transform 0.3s ease;
	}
	
	#alertaDisco .card-body,
	#alertaEnlace .card-body {
		border-top: 2px solid rgba(0,0,0,0.1);
	}
	</style>
</body>

</html>