<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0084';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
    <!-- Material Design Lite CSS -->
	<link rel="stylesheet" href="../../config-general/assets/plugins/material/material.min.css">
	<link rel="stylesheet" href="../../config-general/assets/css/material_style.css">
	<!-- steps -->
	<link rel="stylesheet" href="../../config-general/assets/plugins/steps/steps.css"> 
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/theme/light/theme_style.css" rel="stylesheet" id="rt_style_components" type="text/css" />
    <link href="../../config-general/assets/css/theme/light/style.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/css/responsive.css" rel="stylesheet" type="text/css" />
	<link href="../../config-general/assets/css/theme/light/theme-color.css" rel="stylesheet" type="text/css" />
	<!-- favicon -->
    <link rel="shortcut icon" href="http://radixtouch.in/templates/admin/smart/source/assets/img/favicon.ico" />

	<!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <!-- start header -->
		<?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">Crear matrículas</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="estudiantes.php?cantidad=10" onClick="deseaRegresar(this)">Matrículas</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Crear matrículas</li>
                            </ol>
                        </div>
                    </div>

                         
                    <!-- wizard with validation-->
                    <div class="row">
                    	<div class="col-sm-12">
                             <div class="card-box">
                                 <div class="card-head">
                                     <header>Matrículas</header>
                                 </div>
                                 <div class="card-body">
                                 	<form name="example_advanced_form" id="example-advanced-form" action="estudiantes-guardar.php" method="post">
									  
										<h3>Información personal</h3>
									    <fieldset>
											

											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo de documento</label>
												<div class="col-sm-4">
													<?php
													$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales
													WHERE ogen_grupo=1");
													?>
													<select class="form-control  select2" name="tipoD">
														<option value="">Seleccione una opción</option>
														<?php
														while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$opcionesDatos[0];?>"><?=$opcionesDatos['ogen_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Número de documento</label>
												<div class="col-sm-4">
													<input type="text" name="nDoc" class="form-control" autocomplete="off">
												</div>
											</div>	
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de expedición</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="lugarD">
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
															<option value="<?=$opg['ciu_id'];?>" ><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Folio Y Tesorer&iacute;a</label>
												<div class="col-sm-2">
													<input type="text" name="folio" class="form-control" autocomplete="off">
												</div>
												
												<label class="col-sm-2 control-label">Codigo Tesoreria</label>
												<div class="col-sm-2">
													<input type="text" name="codTesoreria" class="form-control" autocomplete="off">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Primer apellido</label>
												<div class="col-sm-2">
													<input type="text" name="apellido1" class="form-control" autocomplete="off">
												</div>
												
												<label class="col-sm-2 control-label">Segundo apellido</label>
												<div class="col-sm-2">
													<input type="text" name="apellido2" class="form-control" autocomplete="off">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Primer Nombre</label>
												<div class="col-sm-2">
													<input type="text" name="nombres" class="form-control" autocomplete="off">
												</div>

												<label class="col-sm-2 control-label">Otro Nombre</label>
												<div class="col-sm-2">
													<input type="text" name="nombre2" class="form-control" autocomplete="off">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Email</label>
												<div class="col-sm-6">
													<input type="text" name="email" class="form-control" value="notiene@notiene.com" autocomplete="off">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Fecha de nacimiento</label>
												<div class="col-sm-4">
													<div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
													<input class="form-control" size="16" type="text">
													<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
													</div>
												</div>
												<input type="hidden" id="dtp_input1" name="fNac">
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de Nacimiento</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="lNacM">
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
															<option value="<?=$opg['ciu_id'];?>" ><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Genero</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="genero">
														<option value="">Seleccione una opción</option>
														<?php
										  				$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
														while($opg = mysqli_fetch_array($op, MYSQLI_BOTH)){
														?>
															<option value="<?=$opg[0];?>" ><?=$opg[1];?></option>
														<?php }?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Grupo Sanguineo</label>
												<div class="col-sm-2">
													<input type="text" name="tipoSangre" class="form-control" autocomplete="off">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">EPS</label>
												<div class="col-sm-2">
													<input type="text" name="eps" class="form-control" autocomplete="off">
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estudiante de Inclusión</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="inclusion">
														<option value="">Seleccione una opción</option>
														<option value="1">Si</option>
														<option value="0">No</option>
													</select>
												</div>
												
												<label class="col-sm-2 control-label">Extranjero?</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="extran">
														<option value="">Seleccione una opción</option>
														<option value="1">Si</option>
														<option value="0">No</option>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Religi&oacute;n</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="religion">
														<option value="">Seleccione una opción</option>
														<?php
										  				$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=2");
														while($opg = mysqli_fetch_array($op, MYSQLI_BOTH)){
														?>
															<option value="<?=$opg[0];?>" ><?=$opg[1];?></option>
														<?php }?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Direcci&oacute;n</label>
												<div class="col-sm-4">
													<input type="text" name="direccion" class="form-control" autocomplete="off">
												</div>
												<div class="col-sm-4">
													<input type="text" name="barrio" class="form-control" placeholder="Barrio" autocomplete="off">
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Ciudad de residencia</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="ciudadR">
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento 
														ORDER BY ciu_nombre
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
															$opg['ciu_codigo'] = trim($opg['ciu_codigo']);
														?>
                                                        	<option value="<?=$opg['ciu_codigo'];?>"><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estrato</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="estrato">
														<option value="">Seleccione una opción</option>
														<?php
															$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=3");
														while($opg = mysqli_fetch_array($op, MYSQLI_BOTH)){
														?>
															<option value="<?=$opg[0];?>" ><?=$opg[1];?></option>
														<?php }?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Contactos</label>
												<div class="col-sm-2">
													<input type="text" name="telefono" class="form-control" placeholder="Telefono" autocomplete="off">
												</div>
												<div class="col-sm-2">
													<input type="text" name="celular" class="form-control" placeholder="celular" autocomplete="off">
												</div>
												<div class="col-sm-2">
													<input type="text" name="celular2" class="form-control" placeholder="celular #2" autocomplete="off">
												</div>
											</div>								   
									       
										</fieldset>
										
									    <h3>Información académica</h3>
									    <fieldset>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Curso</label>
												<div class="col-sm-4">
													<?php
													$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM academico_grados
													WHERE gra_estado=1
													");
													?>
													<select class="form-control" name="grado">
														<option value="">Seleccione una opción</option>
														<?php
														while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$opcionesDatos[0];?>"><?=$opcionesDatos['gra_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Grupo</label>
												<div class="col-sm-2">
													<?php
													$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM academico_grupos
													");
													?>
													<select class="form-control" name="grupo">
														<option value="">Seleccione una opción</option>
														<?php
														while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$opcionesDatos[0];?>"><?=$opcionesDatos['gru_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo estudiante</label>
												<div class="col-sm-4">
													<?php
													$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales
													WHERE ogen_grupo=5
													");
													?>
													<select class="form-control" name="tipoEst">
														<option value="">Seleccione una opción</option>
														<?php
														
														while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$opcionesDatos[0];?>"><?=$opcionesDatos['ogen_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Valor Matricula</label>
												<div class="col-sm-2">
													<input type="text" name="va_matricula" class="form-control" autocomplete="off">
												</div>
											</div>	
											
									    </fieldset>
										   
										<h3>Información del Acudiente</h3>
										<fieldset>


											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo de documento</label>
												<div class="col-sm-3">
													<?php
													$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales
													WHERE ogen_grupo=1
													");
													?>
													<select class="form-control" name="tipoDAcudiente">
														<option value="">Seleccione una opción</option>
														<?php
														while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														?>
															<option value="<?=$opcionesDatos[0];?>"><?=$opcionesDatos['ogen_nombre'];?></option>
														<?php }?>
													</select>
												</div>
												
												<label class="col-sm-2 control-label">ID Acudiente</label>
												<div class="col-sm-3">
													<input type="text" name="documentoA" class="form-control" autocomplete="off">
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de expedición</label>
												<div class="col-sm-3">
													<select class="form-control" name="lugarDa">
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
															<option value="<?=$opg['ciu_id'];?>" ><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>	

												<label class="col-sm-2 control-label">Ocupaci&oacute;n</label>
												<div class="col-sm-3">
													<input type="text" name="ocupacionA" class="form-control" autocomplete="off">
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Primer Apellido</label>
												<div class="col-sm-3">
													<input type="text" name="apellido1A" class="form-control" autocomplete="off">
												</div>
																							
												<label class="col-sm-2 control-label">Segundo Apellido</label>
												<div class="col-sm-3">
													<input type="text" name="apellido2A" class="form-control" autocomplete="off">
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Nombre</label>
												<div class="col-sm-3">
													<input type="text" name="nombresA" class="form-control" autocomplete="off">
												</div>
																								
												<label class="col-sm-2 control-label">Otro Nombre</label>
												<div class="col-sm-3">
													<input type="text" name="nombre2A" class="form-control" autocomplete="off">
												</div>
											</div>	
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Fecha de nacimiento</label>
												<div class="col-sm-3">
													<div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
													<input class="form-control" size="16" type="text">
													<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
													</div>
												</div>
												<input type="hidden" id="dtp_input1" name="fechaNA">

												<label class="col-sm-2 control-label">Genero</label>
												<div class="col-sm-3">
													<select class="form-control" name="generoA">
														<option value="">Seleccione una opción</option>
														<?php
															$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
														while($opg = mysqli_fetch_array($op, MYSQLI_BOTH)){
														?>
															<option value="<?=$opg[0];?>" ><?=$opg[1];?></option>
														<?php }?>
													</select>
												</div>
											</div>									   
									       
									    </fieldset>
										
									</form>
                                 </div>
                             </div>
                         </div>
                    </div>
					
					<div id="wizard" style="display: none;"></div>
                     
                </div>
            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <!-- start footer -->
        <?php include("../compartido/footer.php");?>
        <!-- end footer -->
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
	<script src="../../config-general/assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <!-- steps -->
    <script src="../../config-general/assets/plugins/steps/jquery.steps.js" ></script>
    <script src="../../config-general/assets/js/pages/steps/steps-data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>

	<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- end js include path -->

</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/wizard.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:55 GMT -->
</html>