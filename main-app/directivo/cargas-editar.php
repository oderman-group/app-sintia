<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0049';?>
<?php include("verificar-permiso-pagina.php");?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
$datosEditar = mysql_fetch_array(mysql_query("SELECT * FROM academico_cargas
INNER JOIN usuarios ON uss_id=car_responsable
WHERE car_id='".$_GET["idR"]."'",$conexion));
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
                                <div class="page-title"><?=$frases[165][$datosUsuarioActual[8]];?> <?=$frases[12][$datosUsuarioActual[8]];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="cargas.php" onClick="deseaRegresar(this)"><?=$frases[12][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[165][$datosUsuarioActual[8]];?> <?=$frases[12][$datosUsuarioActual[8]];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">


                        </div>
						
                        <div class="col-sm-9">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="guardar.php" method="post">
										<input type="hidden" value="17" name="id">
										<input type="hidden" value="<?=$datosEditar['car_id'];?>" name="idR">

										<div class="form-group row">
											<label class="col-sm-2 control-label">ID</label>
											<div class="col-sm-2">
												<input type="text" name="idCarga" class="form-control" value="<?=$datosEditar['car_id'];?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Docente</label>
                                            <div class="col-sm-10">
												<?php
												$opcionesConsulta = mysql_query("SELECT * FROM usuarios
												WHERE uss_tipo=2
												ORDER BY uss_nombre
												",$conexion);
												?>
                                                <select class="form-control  select2" name="docente" required>
                                                    <option value="">Seleccione una opci??n</option>
													<?php
													while($opcionesDatos = mysql_fetch_array($opcionesConsulta)){
														$select = '';
														$disabled = '';
														if($opcionesDatos[0]==$datosEditar['car_docente']) $select = 'selected';
														if($opcionesDatos['uss_bloqueado']==1) $disabled = 'disabled';
													?>
                                                    	<option value="<?=$opcionesDatos[0];?>" <?=$select;?> <?=$disabled;?>><?=$opcionesDatos['uss_usuario']." - ".strtoupper($opcionesDatos['uss_nombre']);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Curso</label>
                                            <div class="col-sm-10">
												<?php
												$opcionesConsulta = mysql_query("SELECT * FROM academico_grados
												ORDER BY gra_vocal
												",$conexion);
												?>
                                                <select class="form-control  select2" name="curso" required>
                                                    <option value="">Seleccione una opci??n</option>
													<?php
													while($opcionesDatos = mysql_fetch_array($opcionesConsulta)){
														$select = '';
														$disabled = '';
														if($opcionesDatos[0]==$datosEditar['car_curso']) $select = 'selected';
														if($opcionesDatos['gra_estado']=='0') $disabled = 'disabled';
													?>
                                                    	<option value="<?=$opcionesDatos[0];?>" <?=$select;?> <?=$disabled;?>><?=$opcionesDatos['gra_id'].". ".strtoupper($opcionesDatos['gra_nombre']);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Grupo</label>
                                            <div class="col-sm-10">
												<?php
												$opcionesConsulta = mysql_query("SELECT * FROM academico_grupos
												",$conexion);
												?>
                                                <select class="form-control  select2" name="grupo" required>
                                                    <option value="">Seleccione una opci??n</option>
													<?php
													while($opcionesDatos = mysql_fetch_array($opcionesConsulta)){
														$select = '';
														if($opcionesDatos[0]==$datosEditar['car_grupo']) $select = 'selected';
													?>
                                                    	<option value="<?=$opcionesDatos[0];?>" <?=$select;?> <?=$disabled;?>><?=$opcionesDatos['gru_id'].". ".strtoupper($opcionesDatos['gru_nombre']);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Asignatura - ??rea</label>
                                            <div class="col-sm-10">
												<?php
												$opcionesConsulta = mysql_query("SELECT * FROM academico_materias
												INNER JOIN academico_areas ON ar_id=mat_area
												ORDER BY mat_nombre
												",$conexion);
												?>
                                                <select class="form-control  select2" name="asignatura" required>
                                                    <option value="">Seleccione una opci??n</option>
													<?php
													while($opcionesDatos = mysql_fetch_array($opcionesConsulta)){
														$select = '';
														if($opcionesDatos[0]==$datosEditar['car_materia']) $select = 'selected';
													?>
                                                    	<option value="<?=$opcionesDatos[0];?>" <?=$select;?> <?=$disabled;?>><?=$opcionesDatos['mat_id'].". ".strtoupper($opcionesDatos['mat_nombre']." - ".$opcionesDatos['ar_nombre']);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Periodo</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="periodo" required>
                                                    <option value="">Seleccione una opci??n</option>
													<?php
													$p = 1;
													while($p<=$config[19]){
														if($p==$datosEditar[5])
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
                                            <label class="col-sm-2 control-label">Director de grupo</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="dg" required>
                                                    <option value="">Seleccione una opci??n</option>
													<option value="1" <?php if($datosEditar["car_director_grupo"]==1){echo 'selected';} ?>>SI</option>
													<option value="0" <?php if($datosEditar["car_director_grupo"]=='0'){echo 'selected';} ?>>NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Intensidad H.</label>
											<div class="col-sm-2">
												<input type="text" name="ih" class="form-control" value="<?=$datosEditar['car_ih'];?>">
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Max. Indicadores</label>
											<div class="col-sm-2">
												<input type="text" name="maxIndicadores" class="form-control" value="<?=$datosEditar['car_maximos_indicadores'];?>">
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Max. Actividades</label>
											<div class="col-sm-2">
												<input type="text" name="maxActividades" class="form-control" value="<?=$datosEditar['car_maximas_calificaciones'];?>">
											</div>
										</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Estado</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="estado" required>
                                                    <option value="">Seleccione una opci??n</option>
													<option value="1" <?php if($datosEditar["car_activa"]==1){echo 'selected';} ?>>Activa</option>
													<option value="0" <?php if($datosEditar["car_activa"]=='0'){echo 'selected';} ?>>Inactiva</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">% Actividades</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="valorActividades">
                                                    <option value="">Seleccione una opci??n</option>
													<option value="1" <?php if($datosEditar["car_configuracion"]==1){echo 'selected';} ?>>Manual</option>
													<option value="0" <?php if($datosEditar["car_configuracion"]=='0'){echo 'selected';} ?>>Autom??tico</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">% Indicadores</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="valorIndicadores">
                                                    <option value="">Seleccione una opci??n</option>
													<option value="1" <?php if($datosEditar["car_valor_indicador"]==1){echo 'selected';} ?>>Manual</option>
													<option value="0" <?php if($datosEditar["car_valor_indicador"]=='0'){echo 'selected';} ?>>Autom??tico</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Permiso para generar informe</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="permiso1">
                                                    <option value="">Seleccione una opci??n</option>
													<option value="1" <?php if($datosEditar["car_permiso1"]==1){echo 'selected';} ?>>SI</option>
													<option value="0" <?php if($datosEditar["car_permiso1"]=='0' or $datosEditar["car_permiso1"]==''){echo 'selected';} ?>>NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Permiso para editar en periodos anteriores</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="permiso2">
                                                    <option value="">Seleccione una opci??n</option>
													<option value="1" <?php if($datosEditar["car_permiso2"]==1){echo 'selected';} ?>>SI</option>
													<option value="0" <?php if($datosEditar["car_permiso2"]=='0'){echo 'selected';} ?>>NO</option>
                                                </select>
                                            </div>
										</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Indicador autom??tico </label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="indicadorAutomatico">
                                                    <option value="">Seleccione una opci??n</option>
													<option value="1" <?php if($datosEditar["car_indicador_automatico"]==1){echo 'selected';} ?>>SI</option>
													<option value="0" <?php if($datosEditar["car_indicador_automatico"]==0){echo 'selected';} ?>>NO</option>
                                                </select>

                                                <span class="text-info">Si selecciona SI, el docente no llenar?? indicadores; solo las calificaciones. Habr?? un solo indicador definitivo con el 100%.</span>

                                            </div>
                                            
                                        </div>
										
										
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Creada</label>
											<div class="col-sm-4">
												<input type="text" name="creada" class="form-control" value="<?=$datosEditar['car_fecha_creada'];?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Responsable</label>
											<div class="col-sm-4">
												<input type="text" name="responsable" class="form-control" value="<?=strtoupper($datosEditar['uss_nombre']);?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Primer acceso docente</label>
											<div class="col-sm-4">
												<input type="text" name="primerAcceso" class="form-control" value="<?=$datosEditar['car_primer_acceso_docente'];?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">??ltimo acceso docente</label>
											<div class="col-sm-4">
												<input type="text" name="ultimoAcceso" class="form-control" value="<?=$datosEditar['car_ultimo_acceso_docente'];?>" readonly>
											</div>
										</div>


										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
										
										<a href="#" name="cargas.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
                                    </form>
                                </div>
                            </div>
                        </div>
						
                    </div>

                </div>
                <!-- end page content -->
             <?php include("../compartido/panel-configuracion.php");?>
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