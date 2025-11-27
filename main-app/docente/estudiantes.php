<?php
include("session.php");
$idPaginaInterna = 'DC0010';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
include("../compartido/head.php");
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
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
                                <div class="page-title"><?=$frases[55][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <?php include("includes/barra-superior-informacion-actual.php"); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[55][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[241][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[138][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[118][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													// ===============================
													// PRE-CARGAR DATOS AUXILIARES
													// ===============================
													// 1) Mapa de géneros para evitar una consulta por estudiante
													$mapaGeneros = [];
													$consultaGeneros = mysqli_query(
														$conexion,
														"SELECT ogen_id, ogen_nombre FROM ".$baseDatosServicios.".opciones_generales"
													);
													while ($gen = mysqli_fetch_array($consultaGeneros, MYSQLI_BOTH)) {
														$mapaGeneros[$gen['ogen_id']] = $gen['ogen_nombre'];
													}

													// 2) Actividades de la carga y periodo actuales (una sola vez)
													$actividadesCarga = [];
													$carga   = $cargaConsultaActual;
													$periodo = $periodoConsultaActual;

													$cA = Actividades::traerActividadesCarga($config, $carga, $periodo);
													while ($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)) {
														$actividadesCarga[] = $rA;
													}

													// 3) Mapa de calificaciones [idEstudiante][idActividad] => fila calificación
													$calificacionesMapa = Calificaciones::traerCalificacionesCargaPeriodo(
														$config,
														$carga,
														$periodo
													);

													// 4) Consulta de estudiantes (una vez)
													$consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);

													$contReg = 1;

													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$fotoEstudiante = $usuariosClase->verificarFoto($resultado['uss_foto']);
														$idGenero       = $resultado['mat_genero'];

														$generoNombre = isset($mapaGeneros[$idGenero])
															? $mapaGeneros[$idGenero]
															: 'N/A';

														// ================================
														// CÁLCULO DE DEFINITIVA OPTIMIZADO
														// ================================
														$estudianteId = $resultado['mat_id'];

														$acumulaValor    = 0;
														$sumaNota        = 0;
														$definitiva      = 0;

														if (!empty($actividadesCarga)) {
															foreach ($actividadesCarga as $act) {
																$idActividad = $act['act_id'];

																$notaFila = $calificacionesMapa[$estudianteId][$idActividad] ?? null;
																if (isset($notaFila['cal_nota']) && $notaFila['cal_nota'] !== "") {
																	$porNuevo       = ($act['act_valor'] / 100);
																	$acumulaValor  += $porNuevo;
																	$sumaNota      += ((float)$notaFila['cal_nota'] * $porNuevo);
																}
															}

															if ($acumulaValor > 0) {
																$definitiva = round(($sumaNota / $acumulaValor), $config['conf_decimales_notas']);
															}
														}

														// Color de nota y ajustes de presentación
														if($definitiva < $config['conf_nota_minima_aprobar'] && $definitiva !== 0){
															$colorNota = $config['conf_color_perdida'];
														} elseif($definitiva >= $config['conf_nota_minima_aprobar'] && $definitiva !== 0){
															$colorNota = $config['conf_color_ganada'];
														} else {
															$colorNota  = 'black';
															$definitiva = '';
														}

														$colorEstudiante = '#000;';
														if($resultado['mat_inclusion']==1){$colorEstudiante = 'blue;';} 
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['mat_id'];?></td>
														<td>
															<?=$resultado['mat_documento'];?>
															
														</td>
														<td style="color: <?=$colorEstudiante;?>">
															<img src="<?=$fotoEstudiante;?>" width="50">
															<?=Estudiantes::NombreCompletoDelEstudiante($resultado);?>
														</td>
														<td><?=$generoNombre;?></td>
														<td><a href="calificaciones-estudiante.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>&periodo=<?=base64_encode($periodoConsultaActual);?>&carga=<?=base64_encode($cargaConsultaActual);?>" style="text-decoration:underline; color:<?=$colorNota;?>;"><?=$definitiva;?></a></td>
														<td>
														
															<div class="btn-group">
																<button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>

																<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	<i class="fa fa-angle-down"></i>
																</button>

																<ul class="dropdown-menu" role="menu">
																	<?php if(!isset($_SESSION['admin'])){?>
																		<li><a href="auto-login.php?user=<?=base64_encode($resultado['mat_id_usuario']);?>">Autologin</a></li>
																	<?php }?>

																	<?php if($datosCargaActual['car_director_grupo']==1){?>
																		<li><a href="reportes-lista.php?est=<?=base64_encode($resultado['mat_id_usuario']);?>&filtros=<?=base64_encode(1);?>">R. Disciplina</a></li>
																		<li><a href="aspectos-estudiantiles.php?idR=<?=base64_encode($resultado['mat_id_usuario']);?>">Ficha estudiantil</a></li>
																	<?php }?>

																	<li><a href="matriculas-adjuntar-documentos.php?id=<?= base64_encode($resultado['mat_id_usuario']); ?>&idMatricula=<?= base64_encode($resultado['mat_id']); ?>"><?=$frases[434][$datosUsuarioActual['uss_idioma']];?></a></li>
																</ul>
															</div>

								
														</td>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
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
</body>

</html>