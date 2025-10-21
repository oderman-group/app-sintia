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
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">

                                <div class="card-body">

                                           
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
                                </div> 
                                    

                                <?php
                                    $filtro="";
                                    include(ROOT_PATH."/config-general/config-admisiones.php");
                                    include(ROOT_PATH."/config-general/mensajes-informativos.php");
                                    include("includes/barra-superior-inscripciones-componente.php");
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
                                                    
                                                    $data =$barraSuperior->builderArray($consulta);
													
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