<?php
include("session.php");
$idPaginaInterna = 'DC0005';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
require_once("../class/Estudiantes.php");
include("../compartido/head.php");
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

<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
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
                                <div class="page-title"><?=$frases[234][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
					<?php include(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                    <?php include("includes/barra-superior-informacion-actual.php"); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
									
								<div class="col-md-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[234][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
										
										
									
										
                                        <div class="card-body">
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12" align="center">
													<p style="color: darkblue;">Utilice esta casilla para colocar la misma nota a todos los estudiantes. Esta opción <mark>reemplazará las notas existentes</mark> de comportamiento para este periodo.</p>
													<input type="text" style="text-align: center; font-weight: bold;" name="<?=$cargaConsultaActual;?>" title="<?=$periodoConsultaActual;?>" maxlength="3" size="10" onChange="notasMasivaDisciplina(this)">
												</div>
											</div>
											
											
										<span style="color: blue; font-size: 15px;" id="respRCT"></span>
											
											
                                        <div class="table-responsive">
                                            <table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[108][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[109][$datosUsuarioActual['uss_idioma']];?></th>
														<?php if($config['conf_observaciones_multiples_comportamiento'] == '1'){?>
														<th>Guardar</th>
														<?php }?>
                                                    </tr>
                                                </thead>
												<tbody>
													<?php
													$consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
													$contReg = 1;

													// ============================================
													// PRE-CARGAR NOTAS DE COMPORTAMIENTO
													// PARA EVITAR UNA CONSULTA POR ESTUDIANTE
													// ============================================
													$notasDisciplinaMapa = [];
													$consultaNotasTodos = mysqli_query(
														$conexion,
														"SELECT * FROM " . BD_DISCIPLINA . ".disiplina_nota 
														 WHERE dn_id_carga='" . $cargaConsultaActual . "' 
														   AND dn_periodo='" . $periodoConsultaActual . "' 
														   AND institucion={$config['conf_id_institucion']} 
														   AND year={$_SESSION['bd']}"
													);
													while ($filaNota = mysqli_fetch_array($consultaNotasTodos, MYSQLI_BOTH)) {
														$notasDisciplinaMapa[$filaNota['dn_cod_estudiante']] = $filaNota;
													}

													// ============================================
													// PRE-CARGAR OBSERVACIONES SEGÚN CONFIGURACIÓN
													// ============================================
													$observacionesInstitucionales = [];
													$bancoFrases = [];

													if ($config['conf_observaciones_multiples_comportamiento'] == '1') {
														$consultaObservacionesGlobal = mysqli_query(
															$conexion,
															"SELECT * FROM " . $baseDatosServicios . ".observaciones 
															 WHERE obser_id_institucion=" . $config['conf_id_institucion'] . " 
															   AND obser_years=" . $config['conf_agno'] . " 
															 ORDER BY obser_categoria"
														);
														while ($obs = mysqli_fetch_array($consultaObservacionesGlobal, MYSQLI_BOTH)) {
															$observacionesInstitucionales[] = $obs;
														}
													} else {
														$consultaFrases = mysqli_query(
															$conexion,
															"SELECT DISTINCT dn_observacion 
															 FROM " . BD_DISCIPLINA . ".disiplina_nota 
															 WHERE dn_id_carga='" . $cargaConsultaActual . "' 
															   AND dn_observacion IS NOT NULL 
															   AND dn_observacion<>''
															   AND institucion={$config['conf_id_institucion']} 
															   AND year={$_SESSION['bd']}"
														);
														while ($frase = mysqli_fetch_array($consultaFrases, MYSQLI_BOTH)) {
															$bancoFrases[] = $frase['dn_observacion'];
														}
													}

													while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
														$colorNota = "black";
														$notas     = $notasDisciplinaMapa[$resultado['mat_id']] ?? null;

														if (!empty($notas['dn_nota']) && $notas['dn_nota'] < $config[5]) {
															$colorNota = $config[6];
														} elseif (!empty($notas['dn_nota']) && $notas['dn_nota'] >= $config[5]) {
															$colorNota = $config[7];
														}

														$observacion = "";
														$numDatos    = 0;
														$explode     = [];
														if (!empty($notas['dn_observacion'])) {
															$observacion = $notas['dn_observacion'];
															$explode     = explode(",", $notas['dn_observacion']);
															$numDatos    = count($explode);
															if (ctype_digit($explode[0])) {
																$observacion = "";
															}
														}
													?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td width="30%">
															<img src="../files/fotos/<?=$resultado['uss_foto'];?>" width="50">
															<?=Estudiantes::NombreCompletoDelEstudiante($resultado);?>
														</td>
														<td width="15%">
															<input type="text" style="text-align: center; color:<?=$colorNota;?>" size="5" maxlength="3" value="<?php if(!empty($notas['dn_nota'])){ echo $notas['dn_nota'];}?>" name="<?=$cargaConsultaActual;?>" title="<?=$periodoConsultaActual;?>" id="<?=$resultado['mat_id'];?>" alt="<?=$resultado['mat_nombres'];?>" onChange="notasDisciplina(this)" tabindex="<?=$contReg;?>">
															<?php if(!empty($notas['dn_nota'])){?>
															<a href="#" name="comportamiento-nota-eliminar.php?id=<?=base64_encode($notas['dn_id']);?>" onClick="deseaEliminar(this)">X</a>
															<?php }?>
														</td>
														<td width="50%">
														<?php if($config['conf_observaciones_multiples_comportamiento'] == '1'){?>
															<p>
																<select class="form-control  select2-multiple" name="Ob<?=$resultado['mat_id'];?>[]" id="Ob<?=$resultado['mat_id'];?>" multiple>
																	<option value="0" disabled>--Observaciones Institucionales--</option>
																	<?php
																	foreach($observacionesInstitucionales as $observaciones){
																		$selected="";
																		for($i=0;$i<$numDatos;$i++){
																			if($observaciones['obser_id']==$explode[$i] && $notas['dn_cod_estudiante']==$resultado['mat_id']){
																				$selected="selected";
																			}
																		}
																	?>
																		<option value="<?=$observaciones['obser_id'];?>" <?=$selected?>><?="[".$observaciones['obser_id']."] - ".$observaciones['obser_descripcion'];?></option>
																	<?php }?>
																</select>
															</p>
														<?php } else {?>	
															
															<p>
															<select class="form-control  select2" name="O<?=$contReg;?>" step="<?=$cargaConsultaActual;?>" title="<?=$periodoConsultaActual;?>" id="<?=$resultado['mat_id'];?>" alt="0" onChange="observacionDisciplina(this)">
																<option value="">Seleccione una opción</option>
																<option value="0" selected>--Banco de frases--</option>
																<?php
																foreach($bancoFrases as $fraseBanco){
																?>
																	<option value="<?=$fraseBanco;?>"><?=$fraseBanco;?></option>
																<?php }?>
															</select>
															</p>
															<textarea rows="7" cols="80" name="O<?=$contReg;?>" step="<?=$cargaConsultaActual;?>" title="<?=$periodoConsultaActual;?>" id="<?=$resultado['mat_id'];?>" alt="0" onChange="observacionDisciplina(this)"><?=$observacion?></textarea>

															<?php }?>
														</td>
                                                        <?php if($config['conf_observaciones_multiples_comportamiento'] == '1'){?>
														<td style="text-align: center; padding: 10px;">
                                                            <button class="btn deepPink-bgcolor" type="submit" name="Ob<?=$resultado['mat_id'];?>" step="<?=$cargaConsultaActual;?>" title="<?=$periodoConsultaActual;?>" id="<?=$resultado['mat_id'];?>" alt="1" onclick="observacionDisciplina(this)"><i class="fa fa-check"></i></button>
                                                        </td>
														<?php }?>
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
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
<!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>

</html>