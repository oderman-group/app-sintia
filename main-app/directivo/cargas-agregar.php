<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0052';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}?>

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
                                <div class="page-title"><?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[12][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cargas.php" onClick="deseaRegresar(this)"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[12][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
                        <div class="col-sm-12">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="cargas-guardar.php" method="post">
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Docente <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
												<?php
                                                $opcionesConsulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo = ".TIPO_DOCENTE." ORDER BY uss_nombre");
												?>
                                                <select class="form-control  select2" name="docente" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														$disabled = '';
														if($opcionesDatos['uss_bloqueado']==1) $disabled = 'disabled';
													?>
                                                    	<option value="<?=$opcionesDatos['uss_id'];?>" <?=$disabled;?>><?=$opcionesDatos['uss_usuario']." - ".UsuariosPadre::nombreCompletoDelUsuario($opcionesDatos);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Curso <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
                                                <select id="multiple" class="form-control  select2-multiple" name="curso[]" required multiple <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
                                                    $opcionesConsulta = Grados::traerGradosInstitucion($config);
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														$disabled = '';
														if($opcionesDatos['gra_estado']=='0') $disabled = 'disabled';
													?>
                                                    	<option value="<?=$opcionesDatos['gra_id'];?>" <?=$disabled;?>><?=$opcionesDatos['gra_id'].". ".strtoupper($opcionesDatos['gra_nombre']);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Grupo <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
												<?php
                                                try{
                                                    $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_grupos WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
                                                } catch (Exception $e) {
                                                    include("../compartido/error-catch-to-report.php");
                                                }
												?>
                                                <select id="multiple" class="form-control select2-multiple" name="grupo[]" multiple <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
													?>
                                                    	<option value="<?=$opcionesDatos['gru_id'];?>"><?=$opcionesDatos['gru_id'].". ".strtoupper($opcionesDatos['gru_nombre']);?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Asignatura (Área) <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
                                                <select id="multiple"  class="form-control  select2-multiple" name="asignatura[]" required multiple <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
                                                    $opcionesConsulta = Asignaturas::consultarTodasAsignaturas($conexion, $config);
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
													?>
                                                    	<option value="<?=$opcionesDatos['mat_id'];?>"><?=$opcionesDatos['mat_id'].". ".strtoupper($opcionesDatos['mat_nombre']." (".$opcionesDatos['mat_valor']."%) (".$opcionesDatos['ar_nombre'].")");?></option>
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
                                                    <option value="0">Seleccione una opción</option>
													<option value="1">SI</option>
													<option value="0" selected>NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Intensidad H. <span style="color: red;">(*)</span></label>
											<div class="col-sm-2">
												<input type="text" name="ih" class="form-control" <?=$disabledPermiso;?>>
											</div>
										</div>

										<div style="display:none">
                                        <hr>

										<div class="form-group row">
											<label class="col-sm-2 control-label">Max. Indicadores</label>
											<div class="col-sm-2">
												<input type="text" name="maxIndicadores" class="form-control" value="10" <?=$disabledPermiso;?>>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Max. Actividades</label>
											<div class="col-sm-2">
												<input type="text" name="maxActividades" class="form-control" value="100" <?=$disabledPermiso;?>>
											</div>
										</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Estado</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="estado" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" selected>Activa</option>
													<option value="0">Inactiva</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">% Actividades</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="valorActividades" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1">Manual</option>
													<option value="0" selected>Automático</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">% Indicadores</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="valorIndicadores" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1">Manual</option>
													<option value="0" selected>Automático</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Permiso para generar informe</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="permiso1" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" selected>SI</option>
													<option value="0">NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Permiso para editar en periodos anteriores</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="permiso2" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1">SI</option>
													<option value="0" selected>NO</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Indicador automático </label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="indicadorAutomatico" <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1">SI</option>
													<option value="0" selected>NO</option>
                                                </select>

                                                <span class="text-info">Si selecciona SI, el docente no llenará indicadores; solo las calificaciones. Habrá un solo indicador definitivo con el 100%.</span>

                                            </div>
                                            
                                        </div>

                                        </div>


                                        <?php $botones = new botonesGuardar("cargas.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
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