<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0032';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$Plataforma = new Plataforma;

Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

require_once("../class/Estudiantes.php");
require_once("../class/Sysjobs.php");
$jQueryTable = '';
if($config['conf_doble_buscador'] == 1) {
	$jQueryTable = 'id="example1"';
}
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
	<div id="overlayInforme">
		<div id="loader"></div>
		<div id="loading-text">Generando informe…</div>
	</div>
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
                                <div class="page-title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								
								
								<?php 
								$filtro = '';
								$curso = '';
								if(!empty($_GET["curso"])){ $curso = base64_decode($_GET['curso']); $filtro .= " AND car_curso='".$curso."'";}
								if(!empty($_GET["grupo"])){$filtro .= " AND car_grupo='".base64_decode($_GET["grupo"])."'";}
								if(!empty($_GET["docente"])){$filtro .= " AND car_docente='".base64_decode($_GET["docente"])."'";}
								if(!empty($_GET["asignatura"])){$filtro .= " AND car_materia='".base64_decode($_GET["asignatura"])."'";}

								//include("includes/cargas-filtros.php");
								?>
								
								<div class="col-md-12">
								<?php
									include("../../config-general/mensajes-informativos.php");
									include("includes/barra-superior-cargas-componente.php");									
								?>

                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="btn-group">
														<?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0052'])) { ?>
                                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#nuevaCargModal" class="btn deepPink-bgcolor">
														   <?=$frases[231][$datosUsuarioActual['uss_idioma']];?> <i class="fa fa-plus"></i>
                                                        </a>
                                                        <?php
                                                        $idModal = "nuevaCargModal";
                                                        $contenido = "../directivo/cargas-agregar-modal.php";
                                                        include("../compartido/contenido-modal.php");
                                                        } ?>
													</div>
												</div>
											</div>
											
                                        <div>
                                    		<table id="example1" class="display"  style="width:100%;">
												<div id="gifCarga" class="gif-carga">
													<img  alt="Cargando...">
												</div>
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Docente</th>
														<th>Curso</th>
														<th>Asignatura</th>
														<th>I.H</th>
														<th>Periodo Actual</th>
                                        				<th style="text-align:center;">NOTAS<br>Declaradas - Registradas</th>
														<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
														</tr>
													</thead>
													<tbody id="cargas_result">
													<?php
													$filtroLimite = '';
													$selectSql = ["car_id","car_periodo","car_curso","car_ih","car_permiso2",
																	"car_indicador_automatico","car_maximos_indicadores",
																	"car_docente","gra_tipo","am.mat_id",
																	"car_maximas_calificaciones","car_director_grupo","uss_nombre",
																	"uss_nombre2","uss_apellido1","uss_apellido2","gra_id","gra_nombre",
																	"gru_nombre","mat_nombre","mat_valor","car_grupo","car_director_grupo"];
													$busqueda = CargaAcademica::listarCargas($conexion, $config, "", $filtro, "car_id", $filtroLimite,"",array(),$selectSql);
    												$contReg = 1;
													$index = 0;
													$arraysDatos = array();																									
													while ($fila = $busqueda->fetch_assoc()) {
														$arraysDatos[$index] = $fila;
														$index++;
													}
													$lista = $arraysDatos;
													$data["data"] =$lista;
													include("../class/componentes/result/cargas-tbody.php");
													?>
                            </tbody>
                          </table>
                          </div>
                      </div>
                      </div>                    
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
	</script>
</body>

</html>