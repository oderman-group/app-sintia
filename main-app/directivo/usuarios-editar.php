<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0124';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaDatos=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$_GET["id"]."'");
$datosEditar = mysqli_fetch_array($consultaDatos, MYSQLI_BOTH);
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
                                <div class="page-title">Editar usuarios</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="usuarios.php?cantidad=10" onClick="deseaRegresar(this)">Usuarios</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar usuarios</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						
						
                        <div class="col-sm-9">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="guardar.php" method="post">
										<input type="hidden" value="16" name="id">
										<input type="hidden" value="<?=$datosEditar['uss_id'];?>" name="idR">

										<div class="form-group row">
											<label class="col-sm-2 control-label">ID</label>
											<div class="col-sm-2">
												<input type="text" name="idRegistro" class="form-control" value="<?=$datosEditar['uss_id'];?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Usuario</label>
											<div class="col-sm-4">
												<input type="text" name="usuario" class="form-control" value="<?=$datosEditar['uss_usuario'];?>">
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Contraseña</label>
											<div class="col-sm-4">
												<input type="text" name="clave" class="form-control" value="<?=$datosEditar['uss_clave'];?>">
											</div>
										</div>
										
										<?php
										$readOnly = '';
										$leyenda = '';
										if($datosEditar['uss_tipo']==4){
											$readOnly='readonly'; 
											$leyenda = 'El nombre de los estudiantes solo es editable desde la matrícula. <a href="estudiantes-editar.php?idR='.$datosEditar['uss_id'].'" style="text-decoration:underline;">IR A LA MATRÍCULA</a>';
										}
										?>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Nombre</label>
											<div class="col-sm-4">
												<input type="text" name="nombre" class="form-control" value="<?=$datosEditar['uss_nombre'];?>" <?=$readOnly;?>>
												<span style="color: tomato;"><?=$leyenda;?></span>
											</div>
											
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Email</label>
											<div class="col-sm-4">
												<input type="text" name="email" class="form-control" value="<?=$datosEditar['uss_email'];?>">
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Celular</label>
											<div class="col-sm-4">
												<input type="text" name="celular" class="form-control" value="<?=$datosEditar['uss_celular'];?>">
											</div>
										</div>

										<div class="form-group row">
											<label class="col-sm-2 control-label">Teléfono</label>
											<div class="col-sm-4">
												<input type="text" name="telefono" class="form-control" value="<?=$datosEditar['uss_telefono'];?>">
											</div>
										</div>

										<div class="form-group row">
											<label class="col-sm-2 control-label">Dirección</label>
											<div class="col-sm-4">
												<input type="text" name="direccion" class="form-control" value="<?=$datosEditar['uss_direccion'];?>">
											</div>
										</div>

										<div class="form-group row">
											<label class="col-sm-2 control-label">Ocupacion</label>
											<div class="col-sm-4">
												<input type="text" name="ocupacion" class="form-control" value="<?=$datosEditar['uss_ocupacion'];?>">
											</div>
										</div>

										<div class="form-group row">
											<label class="col-sm-2 control-label">Lugar de expedición del documento</label>
											<div class="col-sm-4">
												<input type="text" name="lExpedicion" class="form-control" value="<?=$datosEditar['uss_lugar_expedicion'];?>">
											</div>
										</div>
										
										
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Género</label>
                                            <div class="col-sm-4">
												<?php
												$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
												?>
                                                <select class="form-control  select2" name="genero" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														$select = '';
														if($opcionesDatos[0]==$datosEditar['uss_genero']) $select = 'selected';
													?>
                                                    	<option value="<?=$opcionesDatos[0];?>" <?=$select;?> <?=$disabled;?>><?=$opcionesDatos['ogen_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Tipo de usuario</label>
                                            <div class="col-sm-4">
												<?php
												$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM perfiles");
												?>
                                                <select class="form-control  select2" name="tipoUsuario" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														$select = '';
														if($opcionesDatos[0]==$datosEditar['uss_tipo']) $select = 'selected';
													?>
                                                    	<option value="<?=$opcionesDatos[0];?>" <?=$select;?> <?=$disabled;?>><?=$opcionesDatos['pes_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Última actualización</label>
											<div class="col-sm-4">
												<input type="text"  class="form-control" value="<?=$datosEditar['uss_ultima_actualizacion'];?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Último ingreso</label>
											<div class="col-sm-4">
												<input type="text"  class="form-control" value="<?=$datosEditar['uss_ultimo_ingreso'];?>" readonly>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Última salida</label>
											<div class="col-sm-4">
												<input type="text"  class="form-control" value="<?=$datosEditar['uss_ultima_salida'];?>" readonly>
											</div>
										</div>


										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
										
										<a href="#" name="usuarios.php?cantidad=10" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
                                    </form>
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-3"></div>
						
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