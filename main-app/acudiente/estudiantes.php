<?php include("session.php");?>
<?php //include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'AC0005';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once("../class/Estudiantes.php");
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
                                <div class="page-title"><?=$frases[71][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
									
								<div class="col-md-12">
									
									<?php if(!empty($_GET["req"]) && $_GET["req"]==1){?>
										<div class="card card-topline-red">
											<div class="card-head">
												<header><?=$frases[269][$datosUsuarioActual['uss_idioma']];?></header>
											</div>
											<div class="card-body">
												<p><?=$frases[273][$datosUsuarioActual['uss_idioma']];?></p>
												<form class="form-horizontal" action="solicitud-desbloqueo.php" method="post">
													<input type="hidden" name="idRecurso" value="<?=base64_decode($_GET["idE"]);?>">
													<div class="form-group row">
														<div class="col-sm-12">
															<textarea name="contenido" class="form-control" rows="3" placeholder="<?=$frases[274][$datosUsuarioActual['uss_idioma']];?>" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
														</div>
													</div>

													<div class="form-group">
														<div class="offset-md-3 col-md-9">
															<button type="submit" class="btn btn-info"><?=$frases[271][$datosUsuarioActual['uss_idioma']];?></button>
															<button type="reset" class="btn btn-default"><?=$frases[171][$datosUsuarioActual['uss_idioma']];?></button>
														</div>
													</div>
												</form>
											</div>
										</div>
									<?php }?>
									
									<?php if(!empty($_GET["req"]) && $_GET["req"]==2){?>
										<div class="card card-topline-green">
											<div class="card-head">
												<header><?=$frases[277][$datosUsuarioActual['uss_idioma']];?></header>
											</div>
											<div class="card-body">
												<p><?=$frases[278][$datosUsuarioActual['uss_idioma']];?></p>
												<form name="formularioCupo" class="form-horizontal" action="encuesta-reservar-cupo.php" method="post">
													<input type="hidden" name="idEstudiante" value="<?=base64_decode($_GET["idE"]);?>">
													
													<div class="col-sm-12">
														<input type="radio" name="respuesta" value="1" onClick="cupoNo(1)" /><?=$frases[275][$datosUsuarioActual['uss_idioma']];?>
													</div>
													
													<div class="col-sm-12">
														<input type="radio" name="respuesta" value="2" onClick="cupoNo(2)" /><?=$frases[276][$datosUsuarioActual['uss_idioma']];?>
													</div>
													
													<div id="motivoNo" style="display: none;">
													<p><?=$frases[279][$datosUsuarioActual['uss_idioma']];?></p>
													<div class="form-group row">
														<div class="col-sm-12">
															<textarea name="motivo" class="form-control" rows="3" placeholder="<?=$frases[280][$datosUsuarioActual['uss_idioma']];?>..." style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
														</div>
													</div>
													</div>	

													<div class="form-group">
														<div class="offset-md-3 col-md-9">
															<button type="submit" class="btn btn-info"><?=$frases[272][$datosUsuarioActual['uss_idioma']];?></button>
															<button type="reset" class="btn btn-default"><?=$frases[171][$datosUsuarioActual['uss_idioma']];?></button>
														</div>
													</div>
												</form>
											</div>
										</div>
									<?php }?>
									
                                    <div class="card card-topline-purple" data-hint="Listado de todos los acudidos que tienes registrados.">
                                        <div class="card-head">
                                            <header><?=$frases[71][$datosUsuarioActual['uss_idioma']];?></header>
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
                                                        <th><?=$frases[186][$datosUsuarioActual['uss_idioma']];?></th>
														<th>ID Mat.</th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[138][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[26][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = Estudiantes::listarEstudiantesParaAcudientes($datosUsuarioActual['uss_id']);
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $genero = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_id='".$resultado['mat_genero']."'"), MYSQLI_BOTH);

														 $aspectos1 = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
                    										WHERE dn_cod_estudiante='" . $resultado['mat_id'] . "' AND dn_periodo=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}"), MYSQLI_BOTH);

															$aspectos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
															WHERE dn_cod_estudiante='" . $resultado['mat_id'] . "' AND dn_periodo='" . $config['conf_periodo'] . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}"), MYSQLI_BOTH);

															$numReportesDis = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disciplina_reportes dr
															INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat.mat_id_usuario=dr.dr_estudiante AND mat.mat_acudiente='".$_SESSION["id"]."' AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
															WHERE dr.dr_aprobacion_acudiente=0 AND dr.institucion={$config['conf_id_institucion']} AND dr.year={$_SESSION["bd"]}
															AND dr.dr_estudiante='".$resultado['mat_id_usuario']."'"));

															$iconoReportesDisciplinario = '';
															if($numReportesDis > 0) {
																$iconoReportesDisciplinario = '<i class="fa fa-warning text-warning" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Reporte disciplinario pendiente por firmar ('.$numReportesDis.')"></i> ';
															}
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
                                                        <td><?php echo $resultado['uss_usuario'];?></td>
														<td><?php echo $resultado['mat_id'];?></td>
														<td><?=$iconoReportesDisciplinario."".Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>
														<td><?php if(!empty($genero[1])) echo $genero[1];?></td>
														<td><?=strtoupper($resultado['gra_nombre']." ".$resultado['gru_nombre']);?></td>
														<td>
															<?php 
															$respuesta =0;
														 	if($config['conf_activar_encuesta']==1){
																$respuesta = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_encuestas 
																WHERE genc_estudiante='".$resultado['mat_id']."' AND genc_institucion={$config['conf_id_institucion']} AND genc_year={$_SESSION["bd"]}"));
															}
														 
														 if($config['conf_activar_encuesta']!=1 or $respuesta>0){	
														 	if($datosUsuarioActual['uss_bloqueado']!=1){
																if($resultado['uss_bloqueado']!=1){		
															?>
																<div class="btn-group" data-hint="Despliegue el botón de acciones para ver todas las posibilidades por cada uno de sus acudidos.">
																	  <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
																	  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																		  <i class="fa fa-angle-down"></i>
																	  </button>
																	  <ul class="dropdown-menu" role="menu">
																		  
																	  		<?php 
																			if(!empty($resultado['mat_id_usuario'])) {
																				if(
																					array_key_exists(Modulos::MODULO_ACADEMICO, $arregloModulos) && Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_ACADEMICO) && 
																					$config['conf_calificaciones_acudientes']==1 
																				){?>
																					<?php if($config['conf_sin_nota_numerica']!=1){?>
																							<li><a href="periodos-resumen.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>"><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></a></li>
																					<?php }?>
																					<li><a href="notas-actuales.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>"><?=$frases[242][$datosUsuarioActual['uss_idioma']];?></a></li>
																				<?php }?>

																			<?php if (array_key_exists(Modulos::MODULO_DISCIPLINARIO, $arregloModulos) && Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_DISCIPLINARIO)) { ?>
																			<li><a href="reportes-disciplinarios.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>"><?=$frases[105][$datosUsuarioActual['uss_idioma']];?></a></li>
																			<li><a href="aspectos.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>&periodo=<?=base64_encode($config[2]);?>"><?=$frases[264][$datosUsuarioActual['uss_idioma']];?></a></li>
																			<?php }?>
																		  <?php }?>
																		  
																		<?php 
																			if($config['conf_permiso_descargar_boletin'] == 1){
																				if(!empty($aspectos1["dn_aprobado"]) && !empty($aspectos["dn_aprobado"]) && $aspectos1["dn_aprobado"] == 1 and $aspectos["dn_aprobado"] == 1){ 
																		?>
																		<li><a href="../compartido/matricula-boletin-curso-<?=$resultado['gra_formato_boletin'];?>.php?id=<?=base64_encode($resultado["mat_id"]);?>&periodo=<?=base64_encode($config[2]);?>" target="_blank" ><?=$frases[267][$datosUsuarioActual['uss_idioma']];?></a></li>

																		<?php
																				}
																			}

																		  if($config['conf_informe_parcial']==1){?>
																		  	<li><a href="../compartido/informe-parcial.php?estudiante=<?=base64_encode($resultado["mat_id"]);?>&acu=1" target="_blank" ><?=$frases[265][$datosUsuarioActual['uss_idioma']];?></a></li>
																		  <?php }

																		  if( $config['conf_ficha_estudiantil']==1 && !empty($resultado['mat_id_usuario']) ){?>
																		  	<li><a href="ficha-estudiantil.php?idR=<?=base64_encode($resultado["mat_id_usuario"]);?>"><?=$frases[266][$datosUsuarioActual['uss_idioma']];?></a></li>
																		  <?php }?>

																		  <?php if( !isset($_SESSION['admin']) && !empty($resultado['mat_id_usuario']) ){?>
																		  	<li><a href="auto-login.php?user=<?=base64_encode($resultado['mat_id_usuario']);?>">Autologin</a></li>
																		  <?php }?>

																	  </ul>
																  </div>
															<?php
																	} else {
																		$consultaSolicitudes = mysqli_query($conexion, "SELECT * FROM ".BD_GENERAL.".general_solicitudes 
																		LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=soli_remitente AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
																		LEFT JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat.mat_id=soli_id_recurso AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
																		WHERE soli_institucion='".$config['conf_id_institucion']."' 
																		AND soli_year='".$_SESSION["bd"]."' AND soli_id_recurso='{$resultado['mat_id']}' AND soli_estado!=3");
																		$solicitudPendiente = mysqli_fetch_array($consultaSolicitudes, MYSQLI_BOTH);
																		
																		if( !empty($solicitudPendiente) ) {
															?>
																			<span style='color:darkblue;'>Solicitud de desbloqueo <b><?=$estadosSolicitudes[$solicitudPendiente['soli_estado']];?></b></span>
															<?php

																		} else {
																			echo "
																			<span style='color:red;'>".$frases[268][$datosUsuarioActual['uss_idioma']]."</span><br>
																			<a href='".$_SERVER['PHP_SELF']."?req=1&idE=".base64_encode($resultado['mat_id'])."&nameE=".base64_encode($resultado['uss_nombre'])."' style='text-decoration:underline;'>".$frases[269][$datosUsuarioActual['uss_idioma']]."</a>
																			";
																		}
																	}	
																}
																else{}
															}else{
																echo "
																<a href='".$_SERVER['PHP_SELF']."?req=2&idE=".base64_encode($resultado['mat_id'])."&nameE=".base64_encode($resultado['uss_nombre'])."' style='text-decoration:underline;'>".$frases[270][$datosUsuarioActual['uss_idioma']]."</a>
																";	
															}
															?>
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