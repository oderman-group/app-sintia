<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0078';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
//ESTUDIANTE ACTUAL
$consultaEstudianteActual = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_id='".$_GET["id"]."'");

$numEstudianteActual = mysqli_num_rows($consultaEstudianteActual);
$datosEstudianteActual = mysqli_fetch_array($consultaEstudianteActual, MYSQLI_BOTH);
?>
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
                                <div class="page-title">Editar matrículas</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="estudiantes.php?cantidad=10" onClick="deseaRegresar(this)">Matrículas</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar matrículas</li>
                            </ol>
                        </div>
                    </div>

                         
                    <!-- wizard with validation-->
                    <div class="row">
                    	<div class="col-sm-12">
							<?php
								if(isset($_GET['msgsion']) AND $_GET['msgsion']!=''){
								$aler='alert-success';
								if($_GET['stadsion']!=true){
									$aler='alert-danger';
								}
										?>
							<div class="alert alert-block <?=$aler;?>">
								<button type="button" class="close" data-dismiss="alert">×</button>
								<h4 class="alert-heading">SION!</h4>
								<p><?=$_GET['msgsion'];?></p>
							</div>
							<?php 
							}
							if(isset($_GET['msgsintia'])){
										?>
							<div class="alert alert-block alert-success">
								<button type="button" class="close" data-dismiss="alert">×</button>
								<h4 class="alert-heading">SINTIA!</h4>
								<p>La información del estudiante se actualizo correctamente en SINTIA.</p>
							</div>
							<?php }?>
                             <div class="card-box">
                                 <div class="card-head">
                                     <header>Matrículas</header>
                                 </div>
                                 <div class="card-body">
                                 	<form name="example_advanced_form" id="example-advanced-form" action="estudiantes-actualizar.php" method="post">
									<input type="hidden" name="id" value="<?=$_GET["id"];?>">
									<input type="hidden" name="idU" value="<?=$datosEstudianteActual["mat_id_usuario"];?>">
									  
										<h3>Información personal</h3>
									    <fieldset>
											

											<div class="form-group row">
												<label class="col-sm-2 control-label">Código del Sistema</label>
												<div class="col-sm-2">
													<input type="text" name="matricula" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual[1];?>" >
												</div>
												
												<label class="col-sm-2 control-label">Fecha de Matr&iacute;cula</label>
												<div class="col-sm-2">
													<input type="text" name="matricula" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual[2];?>" disabled>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Número de matrícula</label>
												<div class="col-sm-4">
													<input type="text" name="NumMatricula" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual["mat_numero_matricula"];?>">
												</div>
											</div>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo de documento</label>
												<div class="col-sm-2">
													<?php
													$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales
													WHERE ogen_grupo=1
													");
													?>
													<select class="form-control  select2" name="tipoD">
														<option value="">Seleccione una opción</option>
														<?php while($o = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
															if($o[0]==$datosEstudianteActual[11])
															echo '<option value="'.$o[0].'" selected>'.$o[1].'</option>';
														else
															echo '<option value="'.$o[0].'">'.$o[1].'</option>';	
														}?>
													</select>
												</div>
												
												<label class="col-sm-2 control-label">Número de documento</label>
												<div class="col-sm-2">
													<input type="text" name="nDoc" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual[12];?>">
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
														ORDER BY ciu_nombre
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
														<option value="<?=$opg['ciu_id'];?>" <?php if($opg['ciu_id']==$datosEstudianteActual["mat_lugar_expedicion"]){echo "selected";}?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Folio Y Tesorer&iacute;a</label>
												<div class="col-sm-2">
													<input type="text" name="folio" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual[34];?>">
												</div>
												
												<label class="col-sm-2 control-label">Codigo Tesoreria</label>
												<div class="col-sm-2">
													<input type="text" name="codTesoreria" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual[35];?>">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Primer apellido</label>
												<div class="col-sm-2">
													<input type="text" name="apellido1" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual[3];?>">
												</div>
												
												<label class="col-sm-2 control-label">Segundo apellido</label>
												<div class="col-sm-2">
													<input type="text" name="apellido2" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual[4];?>">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Nombres</label>
												<div class="col-sm-2">
													<input type="text" name="nombres" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual[5];?>">
												</div>

												<label class="col-sm-2 control-label">Otro Nombre</label>
												<div class="col-sm-2">
													<input type="text" name="nombre2" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_nombre2'];?>">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Email</label>
												<div class="col-sm-6">
													<input type="text" name="email" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual['mat_email'];?>">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Fecha de nacimiento</label>
												<div class="col-sm-4">
													<div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
													<input class="form-control" size="16" type="text" value="<?=$datosEstudianteActual['mat_fecha_nacimiento'];?>">
													<span class="input-group-addon"><span class="fa fa-calendar"></span>
													</div>
												</div>
												<input type="hidden" id="dtp_input1" name="fNac">
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de Nacimiento</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="lNac">
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento 
														ORDER BY ciu_nombre
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
														<option value="<?=$opg['ciu_id'];?>" <?php if($opg['ciu_id']==$datosEstudianteActual[10]){echo "selected";}?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Genero</label>
												<?php
												$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
												?>
												<div class="col-sm-4">
													<select class="form-control  select2" name="genero">
														<option value="">Seleccione una opción</option>
														<?php while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o[0]==$datosEstudianteActual[8])
																echo '<option value="'.$o[0].'" selected>'.$o[1].'</option>';
															else
																echo '<option value="'.$o[0].'">'.$o[1].'</option>';	
														}?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Grupo Sanguineo</label>
												<div class="col-sm-2">
													<input type="text" name="tipoSangre" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual["mat_tipo_sangre"];?>">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">EPS</label>
												<div class="col-sm-2">
													<input type="text" name="eps" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual["mat_eps"];?>">
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estudiante de Inclusión</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="inclusion">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual[37]==1){echo "selected";}?>>Si</option>
														<option value="0"<?php if ($datosEstudianteActual[37]==0){echo "selected";}?>>No</option>
													</select>
												</div>
												
												<label class="col-sm-2 control-label">Extranjero?</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="extran">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual[39]==1){echo "selected";}?>>Si</option>
														<option value="0"<?php if ($datosEstudianteActual[39]==0){echo "selected";}?>>No</option>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Religi&oacute;n</label>
												<?php
												$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=2");
												?>
												<div class="col-sm-2">
													<select class="form-control  select2" name="religion">
														<option value="">Seleccione una opción</option>
														<?php while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o[0]==$datosEstudianteActual[14])
																echo '<option value="'.$o[0].'" selected>'.$o[1].'</option>';
															else
																echo '<option value="'.$o[0].'">'.$o[1].'</option>';	
														}?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Direcci&oacute;n</label>
												<div class="col-sm-4">
													<input type="text" name="direccion" class="form-control" autocomplete="off" value="<?=$datosEstudianteActual[15];?>">
												</div>
												<div class="col-sm-4">
													<input type="text" name="barrio" class="form-control" placeholder="Barrio" autocomplete="off" value="<?=$datosEstudianteActual[16];?>">
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
														ORDER BY ciu_nombre ");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														$selected='';
														$opg['ciu_codigo'] = trim($opg['ciu_codigo']);
														if($opg['ciu_codigo']==$datosEstudianteActual['mat_ciudad_residencia']){
															$selected='selected';
														}

														?>
														<option value="<?=$opg['ciu_codigo'];?>" <?=$selected;?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estrato</label>
												<?php
												$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=3");
												?>
												<div class="col-sm-2">
													<select class="form-control  select2" name="estrato">
														<option value="">Seleccione una opción</option>
														<?php while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o[0]==$datosEstudianteActual[19])
																echo '<option value="'.$o[0].'" selected>'.$o[1].'</option>';
															else
																echo '<option value="'.$o[0].'">'.$o[1].'</option>';	
														}?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Contactos</label>
												<div class="col-sm-2">
													<input type="text" name="telefono" class="form-control" placeholder="Telefono" autocomplete="off" value="<?=$datosEstudianteActual[17];?>">
												</div>
												<div class="col-sm-2">
													<input type="text" name="celular" class="form-control" placeholder="celular" autocomplete="off" value="<?=$datosEstudianteActual[18];?>">
												</div>
												<div class="col-sm-2">
													<input type="text" name="celular2" class="form-control" placeholder="celular #2" autocomplete="off" value="<?=$datosEstudianteActual['mat_celular2'];?>">
												</div>
											</div>	

											<hr>
											<hr>
											<h2><b>Proceso de matrícula</b></h2>
											
											<p>
												<b>Listo:</b> Sígnifica que el estudiante ya hizo este paso o que no tiene que pasar por el.<br>
												<b>Pendiente:</b> Sígnifica que el estudiante está pendiente de realizar este paso.<br>
											</p>	
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Puede iniciar el proceso?</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="iniciarProceso">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_iniciar_proceso'] == 1){echo "selected";}?>>SI</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_iniciar_proceso'] == '0'){echo "selected";}?>>NO</option>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">1. Actualizar datos</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="actualizarDatos">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_actualizar_datos'] == 1){echo "selected";}?>>Listo</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_actualizar_datos'] == '0'){echo "selected";}?>>Pendiente</option>
													</select>
													<?php if($datosEstudianteActual["mat_actualizar_datos"] == 1){?><i class="icon-ok"></i><?php }?>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">2. Pago de matrícula</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="pagoMatricula">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_pago_matricula'] == 1){echo "selected";}?>>Listo</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_pago_matricula'] == '0'){echo "selected";}?>>Pendiente</option>
													</select>
													<?php if($datosEstudianteActual["mat_pago_matricula"] == 1){?><i class="icon-ok"></i><?php }?>   

													<?php if($datosEstudianteActual["mat_pago_matricula"] == 1 and $datosEstudianteActual["mat_soporte_pago"] != ""){?>  
													<a href="https://plataformasintia.com/main-app/v2.0/files/comprobantes/<?=$datosEstudianteActual["mat_soporte_pago"];?>" target="_blank"><?=$datosEstudianteActual["mat_soporte_pago"];?></a>   
													<?php }?> 
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">3. Contrato</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="contrato">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_contrato'] == 1){echo "selected";}?>>Listo</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_contrato'] == '0'){echo "selected";}?>>Pendiente</option>
													</select>
													<?php if($datosEstudianteActual["mat_contrato"] == 1){?><i class="icon-ok"></i><?php }?>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">4. Pagaré</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="pagare">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_pagare'] == 1){echo "selected";}?>>Listo</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_pagare'] == '0'){echo "selected";}?>>Pendiente</option>
													</select>  

													<?php if($datosEstudianteActual["mat_contrato"] == 1){?><i class="icon-ok"></i><?php }?>           
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">5. Compromiso académico</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="compromisoA">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_compromiso_academico'] == 1){echo "selected";}?>>Listo</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_compromiso_academico'] == '0'){echo "selected";}?>>Pendiente</option>
													</select>  

													<?php if($datosEstudianteActual["mat_compromiso_academico"] == 1){?><i class="icon-ok"></i><?php }?>          
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">6. Compromiso de convivencia</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="compromisoC">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_compromiso_convivencia'] == 1){echo "selected";}?>>Listo</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_compromiso_convivencia'] == '0'){echo "selected";}?>>Pendiente</option>
													</select> 
												</div>
												
												<div class="col-sm-2">
													<select class="form-control  select2" name="compromisoOpcion">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_compromiso_convivencia_opcion'] == 1){echo "selected";}?>>Nuevos</option>
														<option value="2"<?php if ($datosEstudianteActual['mat_compromiso_convivencia_opcion'] == 2){echo "selected";}?>>Antiguos</option>
														<option value="3"<?php if ($datosEstudianteActual['mat_compromiso_convivencia_opcion'] == 3){echo "selected";}?>>Matrícula condicional</option>
													</select>  
												</div>

												<?php if($datosEstudianteActual["mat_compromiso_convivencia"] == 1){?><i class="icon-ok"></i><?php }?>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">7. Manual de convivencia</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="manual">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_manual'] == 1){echo "selected";}?>>Listo</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_manual'] == '0'){echo "selected";}?>>Pendiente</option>
													</select>   

													<?php if($datosEstudianteActual["mat_manual"] == 1){?><i class="icon-ok"></i><?php }?>                
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">8. Contrato mayores de 14 años</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="contrato14">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_mayores14'] == 1){echo "selected";}?>>Listo</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_mayores14'] == '0'){echo "selected";}?>>Pendiente</option>
													</select> 

													<?php if($datosEstudianteActual["mat_mayores14"] == 1){?><i class="icon-ok"></i><?php }?>                       
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">9. Firma hoja matrícula</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="contrato14">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_hoja_firma'] == 1){echo "selected";}?>>Listo</option>
														<option value="0"<?php if ($datosEstudianteActual['mat_hoja_firma'] == '0'){echo "selected";}?>>Pendiente</option>
													</select> 

													<?php if($datosEstudianteActual["mat_hoja_firma"] == 1){?><i class="icon-ok"></i><?php }?>   

													<?php if($datosEstudianteActual["mat_hoja_firma"] == 1 and $datosEstudianteActual["mat_firma_adjunta"] != ""){?>  
													<a href="https://plataformasintia.com/main-app/v2.0/files/comprobantes/<?=$datosEstudianteActual["mat_firma_adjunta"];?>" target="_blank"><?=$datosEstudianteActual["mat_firma_adjunta"];?></a>   
													<?php }?>                       
												</div>
											</div>
									       
										</fieldset>
										
										<h3>Información académica</h3>
										<fieldset>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Curso</label>
												<div class="col-sm-4">
													<?php
													$cv = mysqli_query($conexion, "SELECT * FROM academico_grados");
													?>
													<select class="form-control" name="grado">
														<option value="">Seleccione una opción</option>
														<?php while($rv = mysqli_fetch_array($cv, MYSQLI_BOTH)){
															if($rv[0]==$datosEstudianteActual[6])
																echo '<option value="'.$rv[0].'" selected>'.$rv[2].'</option>';
															else
																echo '<option value="'.$rv[0].'">'.$rv[2].'</option>';	
														}?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Grupo</label>
												<div class="col-sm-2">
													<?php
													$cv = mysqli_query($conexion, "SELECT gru_id, gru_nombre FROM academico_grupos");
													?>
													<select class="form-control" name="grupo">
													<?php while($rv = mysqli_fetch_array($cv, MYSQLI_BOTH)){
														if($rv[0]==$datosEstudianteActual[7])
															echo '<option value="'.$rv[0].'" selected>'.$rv[1].'</option>';
														else
															echo '<option value="'.$rv[0].'">'.$rv[1].'</option>';	
													}?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo estudiante</label>
												<div class="col-sm-4">
													<?php
													$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=5");
													?>
													<select class="form-control" name="tipoEst">
														<option value="">Seleccione una opción</option>
														<?php while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o[0]==$datosEstudianteActual[21])
																echo '<option value="'.$o[0].'" selected>'.$o[1].'</option>';
															else
																echo '<option value="'.$o[0].'">'.$o[1].'</option>';	
														}?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estado Matricula</label>
												<div class="col-sm-4">
													<select class="form-control" name="matestM">
														<option value="">Seleccione una opción</option>
														<option value="1"  <?php if(1==$datosEstudianteActual["mat_estado_matricula"]) echo 'selected'?>>Matriculado</option>
														<option value="2"  <?php if(2==$datosEstudianteActual["mat_estado_matricula"]) echo 'selected'?>>Asistente </option>
														<option value="3"  <?php if(3==$datosEstudianteActual["mat_estado_matricula"]) echo 'selected'?>>Cancelado </option>
														<option value="4"  <?php if(4==$datosEstudianteActual["mat_estado_matricula"]) echo 'selected'?>>No matriculado </option>
													</select>
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Valor Matricula</label>
												<div class="col-sm-2">
													<input type="text" name="va_matricula" class="form-control" autocomplete="off">
												</div>
											</div>	
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estado del año</label>
												<div class="col-sm-4">
													<select class="form-control" name="estadoAgno">
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosEstudianteActual['mat_estado_agno']==1){echo "selected";}?>>Ganado</option>
														<option value="2"<?php if ($datosEstudianteActual['mat_estado_agno']==2){echo "selected";}?>>Perdido</option>
													</select>
												</div>
											</div>
											
										</fieldset>
											
										<h3>Información del Acudiente</h3>
										<fieldset>
   
   
											<?php if(isset($acudiente[0])&&$acudiente[0]==""){?>
												<span style="color:#F03;">Si este estudiante no tiene acudiente, primero debe crearlo desde la opción <b>Usuarios->Acudientes</b> y asociarlo allá mismo.</span>
											<?php }else{?>
												<span style="color:#009;">Esta opción es solo para actualizar los datos del acudiente.<br>
												En caso de que el acudiente de este estudiante sea otro, debe hacerlo desde la opción <b>Usuarios->Acudientes</b> en el icono correspondiente.</span>
											<?php }
											$consultaAcudiente=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$datosEstudianteActual["mat_acudiente"]."'");
											$acudiente = mysqli_fetch_array($consultaAcudiente, MYSQLI_BOTH);
											?>
                                                          
											<h2><b>ACUDIENTE 1</b></h2>
											<p>
												<a href="usuarios-editar.php?id=<?php if(isset($acudiente[0])){ echo $acudiente[0];}?>" target="_blank" class="btn btn-info">Editar información del acudiente</a>
											</p>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo de documento</label>
												<div class="col-sm-3">
													<?php
													$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=1");
													?>
													<select class="form-control" name="tipoDAcudiente">
														<?php while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o[0]==$acudiente["uss_tipo_documento"])
															echo '<option value="'.$o[0].'" selected>'.$o[1].'</option>';
														else
															echo '<option value="'.$o[0].'">'.$o[1].'</option>';	
														}?>
													</select>
												</div>
												
												<label class="col-sm-2 control-label">ID Acudiente</label>
												<div class="col-sm-3">
													<input type="text" name="documentoA" class="form-control" autocomplete="off" value="<?php if(isset($acudiente['uss_usuario'])){ echo $acudiente['uss_usuario'];}?>">
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de expedición</label>
												<div class="col-sm-3">
													<select class="form-control" name="lugardA">
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento 
														ORDER BY ciu_nombre
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
														<option value="<?=$opg['ciu_id'];?>" <?php if(isset($acudiente["uss_lugar_expedicion"])&&$opg['ciu_id']==$acudiente["uss_lugar_expedicion"]){echo "selected";}?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>	

												<label class="col-sm-2 control-label">Ocupaci&oacute;n</label>
												<div class="col-sm-3">
													<input type="text" name="ocupacionA" class="form-control" autocomplete="off" value="<?php if(isset($acudiente["uss_ocupacion"])){ echo $acudiente["uss_ocupacion"];}?>">
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Primer Apellido</label>
												<div class="col-sm-3">
													<input type="text" name="apellido1A" class="form-control" autocomplete="off" value="<?php if(isset($acudiente["uss_apellido1"])){ echo $acudiente["uss_apellido1"];}?>">
												</div>
																							
												<label class="col-sm-2 control-label">Segundo Apellido</label>
												<div class="col-sm-3">
													<input type="text" name="apellido2A" class="form-control" autocomplete="off" value="<?php if(isset($acudiente["uss_apellido2"])){ echo $acudiente["uss_apellido2"];}?>">
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Nombre</label>
												<div class="col-sm-3">
													<input type="text" name="nombreA" class="form-control" autocomplete="off" value="<?php if(isset($acudiente["uss_nombre"])){ echo $acudiente["uss_nombre"];}?>">
												</div>
																								
												<label class="col-sm-2 control-label">Otro Nombre</label>
												<div class="col-sm-3">
													<input type="text" name="nombre2A" class="form-control" autocomplete="off" value="<?php if(isset($acudiente["uss_nombre2"])){ echo $acudiente["uss_nombre2"];}?>">
												</div>
											</div>	
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Genero</label>
												<div class="col-sm-3">
													<?php
													$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
													?>
													<select class="form-control" name="generoA">
														<option value="">Seleccione una opción</option>
														<?php while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o[0]==$acudiente[16])
																echo '<option value="'.$o[0].'" selected>'.$o[1].'</option>';
															else
																echo '<option value="'.$o[0].'">'.$o[1].'</option>';	
														}?>
													</select>
												</div>
											</div>

											<hr>
											<hr>
                                            
											<?php
												$consultaAcudiente2=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$datosEstudianteActual["mat_acudiente2"]."'");
												$acudiente2 = mysqli_fetch_array($consultaAcudiente2, MYSQLI_BOTH);
											?>  
											<h2><b>ACUDIENTE 2</b></h2>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo de documento</label>
												<div class="col-sm-3">
													<?php
													$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=1");
													?>
													<select class="form-control" name="tipoDAcudiente2">
														<?php while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o[0]==$acudiente2["uss_tipo_documento"])
															echo '<option value="'.$o[0].'" selected>'.$o[1].'</option>';
														else
															echo '<option value="'.$o[0].'">'.$o[1].'</option>';	
														}?>
													</select>
												</div>
												
												<label class="col-sm-2 control-label">ID Acudiente</label>
												<div class="col-sm-3">
													<input type="text" name="documentoA2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2[0])){ echo $acudiente2[0];}?>">
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de expedición</label>
												<div class="col-sm-3">
													<select class="form-control" name="lugardA2">
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento 
														ORDER BY ciu_nombre
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
														<option value="<?=$opg['ciu_id'];?>" <?php if(isset($acudiente2["uss_lugar_expedicion"])&&$opg['ciu_id']==$acudiente2["uss_lugar_expedicion"]){echo "selected";}?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>	

												<label class="col-sm-2 control-label">Ocupaci&oacute;n</label>
												<div class="col-sm-3">
													<input type="text" name="ocupacionA2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2["uss_ocupacion"])){ echo $acudiente2["uss_ocupacion"];}?>">
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Primer Apellido</label>
												<div class="col-sm-3">
													<input type="text" name="apellido1A2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2["uss_apellido1"])){ echo $acudiente2["uss_apellido1"];}?>">
												</div>
																							
												<label class="col-sm-2 control-label">Segundo Apellido</label>
												<div class="col-sm-3">
													<input type="text" name="apellido2A2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2["uss_apellido2"])){ echo $acudiente2["uss_apellido2"];}?>">
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Nombre</label>
												<div class="col-sm-3">
													<input type="text" name="nombreA2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2["uss_nombre"])){ echo $acudiente2["uss_nombre"];}?>">
												</div>
																								
												<label class="col-sm-2 control-label">Otro Nombre</label>
												<div class="col-sm-3">
													<input type="text" name="nombre2A2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2["uss_nombre2"])){ echo $acudiente2["uss_nombre2"];}?>">
												</div>
											</div>	
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Genero</label>
												<div class="col-sm-3">
													<?php
													$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
													?>
													<select class="form-control" name="generoA2">
														<option value="">Seleccione una opción</option>
														<?php while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o[0]==$acudiente2[16])
																echo '<option value="'.$o[0].'" selected>'.$o[1].'</option>';
															else
																echo '<option value="'.$o[0].'">'.$o[1].'</option>';	
														}?>
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