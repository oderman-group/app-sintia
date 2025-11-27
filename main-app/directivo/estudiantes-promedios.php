<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0002';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once("../class/Estudiantes.php");

Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$Plataforma = new Plataforma;
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
                                <div class="page-title"><?=$frases[247][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
							
                            <div class="row">
								
								<div class="col-md-12">
								<?php include("includes/barra-superior-promedios.php");?>

                                    <div class="panel mt-3">
										<header class="panel-heading panel-heading-blue">PROMEDIOS GENERALES</header>
										<div class="panel-body">
											<?php
											// ===============================
											// CONSULTA ÚNICA DE PROMEDIOS
											// ===============================
											$listaDestacados   = [];
											$totalEstudiantes  = 0;
											$sumaPromedios     = 0;
											$aprobados         = 0;
											$reprobados        = 0;
											$mejorPromedio     = null;
											$peorPromedio      = null;

											try{
												switch ($config['conf_orden_nombre_estudiantes']) {
													case '1':
														$odenNombres = "mat_nombres,mat_nombre2,mat_primer_apellido,mat_segundo_apellido";
														break;
													case '2':
													default:
														$odenNombres = "mat_primer_apellido,mat_segundo_apellido,mat_nombres,mat_nombre2";
														break;
												 }

												$sqlPromedios = "
													SELECT 
														AVG(bol_nota) AS promedio,
														bol_estudiante,
														mat_nombres,
														mat_primer_apellido,
														mat_segundo_apellido,
														mat_grado,
														mat_grupo,
														gra.gra_nombre,
														gru.gru_nombre
													FROM ".BD_ACADEMICA.".academico_boletin bol
													INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat 
														ON mat_id = bol_estudiante 
														AND mat.institucion = {$config['conf_id_institucion']} 
														AND mat.year = {$_SESSION['bd']} 
														$filtro 
														AND mat_eliminado = 0
														AND (mat_estado_matricula = ".MATRICULADO." OR mat_estado_matricula = ".ASISTENTE.")
													LEFT JOIN ".BD_ACADEMICA.".academico_grados gra 
														ON gra.gra_id = mat.mat_grado
														AND gra.institucion = {$config['conf_id_institucion']}
														AND gra.year = {$_SESSION['bd']}
													LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru 
														ON gru.gru_id = mat.mat_grupo
														AND gru.institucion = {$config['conf_id_institucion']}
														AND gru.year = {$_SESSION['bd']}
													WHERE bol_id = bol_id 
														AND bol.institucion = {$config['conf_id_institucion']} 
														AND bol.year = {$_SESSION['bd']} 
														$filtroBoletin
													GROUP BY bol_estudiante
													ORDER BY promedio $filtroOrden, $odenNombres
													$filtroLimite
												";

												$destacados = mysqli_query($conexion, $sqlPromedios);

												while($dest = mysqli_fetch_array($destacados, MYSQLI_BOTH)){
													$promedioBruto = empty($dest['promedio']) ? 0 : (float)$dest['promedio'];
													$nota          = number_format($promedioBruto, $config['conf_decimales_notas']);
													$porcentaje    = ($config['conf_nota_hasta'] > 0)
														? ($promedioBruto / $config['conf_nota_hasta']) * 100
														: 0;

													$esAprobado = ($promedioBruto >= $config['conf_nota_minima_aprobar']);

													$totalEstudiantes++;
													$sumaPromedios += $promedioBruto;
													if ($esAprobado) {
														$aprobados++;
													} else {
														$reprobados++;
													}

													if ($mejorPromedio === null || $promedioBruto > $mejorPromedio) {
														$mejorPromedio = $promedioBruto;
													}
													if ($peorPromedio === null || $promedioBruto < $peorPromedio) {
														$peorPromedio = $promedioBruto;
													}

													$listaDestacados[] = [
														'datos'        => $dest,
														'promedio'     => $promedioBruto,
														'nota'         => $nota,
														'porcentaje'   => $porcentaje,
														'es_aprobado'  => $esAprobado,
													];
												}
											} catch (Exception $e) {
												include("../compartido/error-catch-to-report.php");
											}

											// Cálculos finales para las cards
											$promedioGeneral    = ($totalEstudiantes > 0) ? ($sumaPromedios / $totalEstudiantes) : 0;
											$porcAprobados      = ($totalEstudiantes > 0) ? round(($aprobados / $totalEstudiantes) * 100) : 0;
											$porcReprobados     = ($totalEstudiantes > 0) ? round(($reprobados / $totalEstudiantes) * 100) : 0;
											$mejorPromedioFmt   = $mejorPromedio !== null ? number_format((float)$mejorPromedio, $config['conf_decimales_notas']) : '0';
											$peorPromedioFmt    = $peorPromedio !== null ? number_format((float)$peorPromedio, $config['conf_decimales_notas']) : '0';
											$promedioGeneralFmt = number_format($promedioGeneral, $config['conf_decimales_notas']);
											?>

											<?php if ($totalEstudiantes > 0) { ?>
												<!-- Cards de resumen de promedios -->
												<div class="row mb-3">
													<div class="col-md-3 col-sm-6 mb-2">
														<div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); background: linear-gradient(135deg,#4e73df,#224abe); color: #fff;">
															<div class="card-body" style="padding: 14px 16px;">
																<div style="font-size: 11px; text-transform: uppercase; opacity: 0.9;">Promedio general</div>
																<div style="font-size: 26px; font-weight: 700; margin-top: 4px;">
																	<?= $promedioGeneralFmt; ?>
																</div>
																<div style="font-size: 11px; opacity: 0.9; margin-top: 4px;">
																	Sobre <?= $config['conf_nota_hasta']; ?>
																</div>
															</div>
														</div>
													</div>

													<div class="col-md-3 col-sm-6 mb-2">
														<div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
															<div class="card-body" style="padding: 14px 16px;">
																<div style="font-size: 11px; text-transform: uppercase; color: #7f8c8d;">Estudiantes en el ranking</div>
																<div style="font-size: 26px; font-weight: 700; color: #2c3e50; margin-top: 4px;">
																	<?= $totalEstudiantes; ?>
																</div>
																<div style="font-size: 11px; color: #95a5a6; margin-top: 4px;">
																	Según filtros seleccionados
																</div>
															</div>
														</div>
													</div>

													<div class="col-md-3 col-sm-6 mb-2">
														<div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
															<div class="card-body" style="padding: 14px 16px;">
																<div style="font-size: 11px; text-transform: uppercase; color: #7f8c8d;">Aprobados</div>
																<div style="font-size: 22px; font-weight: 700; color: #16a085; margin-top: 4px;">
																	<?= $aprobados; ?> <span style="font-size: 13px; color:#7f8c8d;">(<?= $porcAprobados; ?>%)</span>
																</div>
																<div style="font-size: 11px; color: #95a5a6; margin-top: 4px;">
																	Nota &ge; <?= $config['conf_nota_minima_aprobar']; ?>
																</div>
															</div>
														</div>
													</div>

													<div class="col-md-3 col-sm-6 mb-2">
														<div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
															<div class="card-body" style="padding: 14px 16px;">
																<div style="font-size: 11px; text-transform: uppercase; color: #7f8c8d;">Reprobados</div>
																<div style="font-size: 22px; font-weight: 700; color: #c0392b; margin-top: 4px;">
																	<?= $reprobados; ?> <span style="font-size: 13px; color:#7f8c8d;">(<?= $porcReprobados; ?>%)</span>
																</div>
																<div style="font-size: 11px; color: #95a5a6; margin-top: 4px;">
																	Por debajo de la nota mínima
																</div>
															</div>
														</div>
													</div>
												</div>

												<div class="row mb-3">
													<div class="col-md-6 col-sm-6 mb-2">
														<div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
															<div class="card-body" style="padding: 12px 14px;">
																<div style="font-size: 11px; text-transform: uppercase; color: #7f8c8d;">Mejor promedio</div>
																<div style="font-size: 22px; font-weight: 700; color: #27ae60; margin-top: 4px;">
																	<?= $mejorPromedioFmt; ?>
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-6 col-sm-6 mb-2">
														<div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
															<div class="card-body" style="padding: 12px 14px;">
																<div style="font-size: 11px; text-transform: uppercase; color: #7f8c8d;">Promedio más bajo</div>
																<div style="font-size: 22px; font-weight: 700; color: #e74c3c; margin-top: 4px;">
																	<?= $peorPromedioFmt; ?>
																</div>
															</div>
														</div>
													</div>
												</div>

												<hr>

												<!-- Lista de estudiantes con barra de progreso -->
												<?php
												$contP = 1;
												foreach ($listaDestacados as $item) {
													$dest        = $item['datos'];
													$nota        = $item['nota'];
													$porcentaje  = $item['porcentaje'];
													$esAprobado  = $item['es_aprobado'];
													$colorGrafico= $esAprobado ? 'info' : 'danger';

													// Curso y grupo (nombre legible)
													$nombreCurso = !empty($dest['gra_nombre']) ? $dest['gra_nombre'] : $dest['mat_grado'];
													$nombreGrupo = !empty($dest['gru_nombre']) ? $dest['gru_nombre'] : $dest['mat_grupo'];
												?>
													<div class="work-monitor work-progress" style="margin-bottom: 10px;">
														<div class="states" style="padding: 8px 10px; border-radius: 6px; border: 1px solid #ecf0f1;">
															<div class="info">
																<div class="desc pull-left">
																	<?="<b>".$contP.".</b> ".Estudiantes::NombreCompletoDelEstudianteParaInformes($dest, $config['conf_orden_nombre_estudiantes']);?>:
																	<b data-toggle="tooltip" title="<?=$dest['promedio'];?>"><?=$nota;?></b>
																	<br>
																	<small style="color:#7f8c8d;">
																		<span class="fa fa-graduation-cap"></span>
																		<?= htmlspecialchars($nombreCurso); ?> 
																		<?php if (!empty($nombreGrupo)) { ?>
																			- Grupo <?= htmlspecialchars($nombreGrupo); ?>
																		<?php } ?>
																	</small>
																</div>
																<div class="percent pull-right" style="font-weight: 600; color: #7f8c8d;">
																	<?=round($porcentaje);?>%
																</div>
															</div>

															<div class="progress progress-xs" style="margin-top: 6px; height: 8px; border-radius: 4px; overflow: hidden;">
																<div class="progress-bar progress-bar-<?=$colorGrafico;?> progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentaje;?>%">
																	<span class="sr-only"><?=$porcentaje;?>%</span>
																</div>
															</div>
														</div>
													</div>
												<?php
													$contP++;
												}
												?>
											<?php } else { ?>
												<div class="alert alert-info">
													<i class="fa fa-info-circle"></i>
													No se encontraron promedios para los filtros seleccionados.
												</div>
											<?php } ?>
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