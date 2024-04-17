<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0049';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
try{
	$consultaDatos=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
	INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_responsable AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]} WHERE car_id='".base64_decode($_GET["idR"])."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
$datosEditar = mysqli_fetch_array($consultaDatos, MYSQLI_BOTH);

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
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
                                <div class="page-title"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?> <?=$frases[12][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cargas.php" onClick="deseaRegresar(this)"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?> <?=$frases[12][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
                        <div class="col-sm-8">

						<?php include("../../config-general/mensajes-informativos.php"); ?>

								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="cargas-actualizar.php" method="post">

										<input type="hidden" value="<?=$datosEditar['car_id'];?>" name="idR">
										<input type="hidden" value="<?=$datosEditar['car_periodo'];?>" name="periodoActual">
										<input type="hidden" value="<?=$datosEditar['car_docente'];?>" name="docenteActual">
										<input type="hidden" value="<?=$datosEditar['car_curso'];?>" name="cursoActual">
										<input type="hidden" value="<?=$datosEditar['car_grupo'];?>" name="grupoActual">
										<input type="hidden" value="<?=$datosEditar['car_materia'];?>" name="asignaturaActual">

										<div class="form-group row">
											<label class="col-sm-2 control-label">ID</label>
											<div class="col-sm-4">
												<input type="text" name="idCarga" class="form-control" value="<?=$datosEditar['car_id'];?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Docente <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
												<?php
												$opcionesConsulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=2 ORDER BY uss_nombre");
												?>
                                                <select class="form-control  select2" name="docente" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														$select = '';
														$disabled = '';
														if($opcionesDatos['uss_id']==$datosEditar['car_docente']) $select = 'selected';
														if($opcionesDatos['uss_bloqueado']==1) $disabled = 'disabled';
													?>
                                                    	<option value="<?=$opcionesDatos['uss_id'];?>" <?=$select;?> <?=$disabled;?>><?=$opcionesDatos['uss_usuario']." - ".UsuariosPadre::nombreCompletoDelUsuario($opcionesDatos);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Curso <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
												<?php
												try{
													$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_grados WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} ORDER BY gra_vocal");
												} catch (Exception $e) {
													include("../compartido/error-catch-to-report.php");
												}
												?>
                                                <select class="form-control  select2" name="curso" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														$select = '';
														$disabled = '';
														if($opcionesDatos['gra_id']==$datosEditar['car_curso']) $select = 'selected';
														if($opcionesDatos['gra_estado']=='0') $disabled = 'disabled';
													?>
                                                    	<option value="<?=$opcionesDatos['gra_id'];?>" <?=$select;?> <?=$disabled;?>><?=$opcionesDatos['gra_id'].". ".strtoupper($opcionesDatos['gra_nombre']);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Grupo <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
                                                <select class="form-control  select2" name="grupo" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
                        							$opcionesConsulta = Grupos::traerGrupos($conexion, $config);
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														$select = '';
														if($opcionesDatos['gru_id']==$datosEditar['car_grupo']) $select = 'selected';
													?>
                                                    	<option value="<?=$opcionesDatos['gru_id'];?>" <?=$select;?>><?=$opcionesDatos['gru_id'].". ".strtoupper($opcionesDatos['gru_nombre']);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Asignatura (Área) <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
                                                <select class="form-control  select2" name="asignatura" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesConsulta = Asignaturas::consultarTodasAsignaturas($conexion, $config);
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														$select = '';
														if($opcionesDatos['mat_id']==$datosEditar['car_materia']) $select = 'selected';
													?>
                                                    	<option value="<?=$opcionesDatos['mat_id'];?>" <?=$select;?>><?=$opcionesDatos['mat_id'].". ".strtoupper($opcionesDatos['mat_nombre']." (".$opcionesDatos['ar_nombre'].")");?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Periodo <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="periodo" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$p = 1;
													while($p<=$config[19]){
														if($p==$datosEditar['car_periodo'])
															echo '<option value="'.$p.'" selected>Periodo '.$p.'</option>';
														else
															echo '<option value="'.$p.'">Periodo '.$p.'</option>';	
														$p++;
													}
													?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Director de grupo <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="dg" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" <?php if($datosEditar["car_director_grupo"]==1){echo 'selected';} ?>>SI</option>
													<option value="0" <?php if($datosEditar["car_director_grupo"]=='0'){echo 'selected';} ?>>NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Intensidad H. <span style="color: red;">(*)</span></label>
											<div class="col-sm-2">
												<input type="text" name="ih" class="form-control" value="<?=$datosEditar['car_ih'];?>" <?=$disabledPermiso;?>>
											</div>
										</div>
										
										<hr>
										<div class="form-group row">
											<label class="col-sm-2 control-label">Max. Indicadores</label>
											<div class="col-sm-2">
												<input type="text" name="maxIndicadores" class="form-control" value="<?=$datosEditar['car_maximos_indicadores'];?>" <?=$disabledPermiso;?>>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Max. Actividades</label>
											<div class="col-sm-2">
												<input type="text" name="maxActividades" class="form-control" value="<?=$datosEditar['car_maximas_calificaciones'];?>" <?=$disabledPermiso;?>>
											</div>
										</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Estado</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="estado" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" <?php if($datosEditar["car_activa"]==1){echo 'selected';} ?>>Activa</option>
													<option value="0" <?php if($datosEditar["car_activa"]=='0'){echo 'selected';} ?>>Inactiva</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">% Actividades</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="valorActividades" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" <?php if($datosEditar["car_configuracion"]==1){echo 'selected';} ?>>Manual</option>
													<option value="0" <?php if($datosEditar["car_configuracion"]=='0'){echo 'selected';} ?>>Automático</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">% Indicadores</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="valorIndicadores" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" <?php if($datosEditar["car_valor_indicador"]==1){echo 'selected';} ?>>Manual</option>
													<option value="0" <?php if($datosEditar["car_valor_indicador"]=='0'){echo 'selected';} ?>>Automático</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Permiso para generar informe</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="permiso1" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" <?php if($datosEditar["car_permiso1"]==1){echo 'selected';} ?>>SI</option>
													<option value="0" <?php if($datosEditar["car_permiso1"]=='0' or $datosEditar["car_permiso1"]==''){echo 'selected';} ?>>NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Permiso para editar en periodos anteriores</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="permiso2" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" <?php if($datosEditar["car_permiso2"]==1){echo 'selected';} ?>>SI</option>
													<option value="0" <?php if($datosEditar["car_permiso2"]=='0'){echo 'selected';} ?>>NO</option>
                                                </select>
                                            </div>
										</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Indicador automático </label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="indicadorAutomatico" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" <?php if($datosEditar["car_indicador_automatico"]==1){echo 'selected';} ?>>SI</option>
													<option value="0" <?php if($datosEditar["car_indicador_automatico"]==0){echo 'selected';} ?>>NO</option>
                                                </select>

                                                <span class="text-info">Si selecciona SI, el docente no llenará indicadores; solo las calificaciones. Habrá un solo indicador definitivo con el 100%.</span>

                                            </div>
                                            
                                        </div>

										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Observaciones en el boletin de los estudiantes ? </label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="observacionesBoletin" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" <?php if($datosEditar["car_observaciones_boletin"]==1){echo 'selected';} ?>>SI</option>
													<option value="0" <?php if($datosEditar["car_observaciones_boletin"]==0){echo 'selected';} ?>>NO</option>
                                                </select>

                                                <span class="text-info">Si selecciona SI, el docente podrá colocar observaciones que aparecerán en el boletín de los estudiantes.</span>

                                            </div>
                                            
                                        </div>
										
										
										<hr>
										<div class="form-group row">
											<label class="col-sm-2 control-label">Creada</label>
											<div class="col-sm-4">
												<input type="text" name="creada" class="form-control" value="<?=$datosEditar['car_fecha_creada'];?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Responsable</label>
											<div class="col-sm-4">
												<input type="text" name="responsable" class="form-control" value="<?=UsuariosPadre::nombreCompletoDelUsuario($datosEditar);?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Primer acceso docente</label>
											<div class="col-sm-4">
												<input type="text" name="primerAcceso" class="form-control" value="<?=$datosEditar['car_primer_acceso_docente'];?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Último acceso docente</label>
											<div class="col-sm-4">
												<input type="text" name="ultimoAcceso" class="form-control" value="<?=$datosEditar['car_ultimo_acceso_docente'];?>" readonly>
											</div>
										</div>

										<?php require_once("../class/componentes/botones-guardar.php");
                            				$botones = new botonesGuardar("cargas.php",Modulos::validarPermisoEdicion()); ?>
									 </form>
                                </div>
                            </div>
                        </div>

						<div class="col-sm-4">
							<div class="panel">
								<header class="panel-heading panel-heading-purple">Cargas relacionadas</header>
								<div class="panel-body">
									<p>&nbsp;</p>
									<ul class="list-group list-group-unbordered">
										<?php
										$consulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
										INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
										INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion={$config['conf_id_institucion']} AND am.year={$_SESSION["bd"]}
										INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]} {$filtroMT}
										INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion={$config['conf_id_institucion']} AND gru.year={$_SESSION["bd"]}
										WHERE car.institucion={$config['conf_id_institucion']} 
										AND car.year={$_SESSION["bd"]} 
										AND (car_docente='{$datosEditar["car_docente"]}' OR car_curso='{$datosEditar["car_curso"]}')
										ORDER BY car_docente
										");
										while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
											$resaltaItem = $Plataforma->colorDos;
											if($resultado['car_id']==base64_decode($_GET["idR"])){$resaltaItem = $Plataforma->colorUno;}

										?>
										<li class="list-group-item">
											<a href="cargas-editar.php?idR=<?=base64_encode($resultado['car_id']);?>" style="color:<?=$resaltaItem;?>; text-decoration:<?=$tachaItem;?>;"><?=$resultado['gra_nombre']." ".$resultado['gru_nombre']." - ".$resultado['mat_nombre']." - ".UsuariosPadre::nombreCompletoDelUsuario($resultado);?></a> 
											<div class="profile-desc-item pull-right">&nbsp;</div>
										</li>
										<?php }?>
									</ul>

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