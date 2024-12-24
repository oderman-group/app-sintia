<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0084';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php include("includes/variables-estudiantes-agregar.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}?>
<?php require_once("../class/servicios/GradoServicios.php"); ?>
    
	<!-- steps -->
	<link rel="stylesheet" href="../../config-general/assets/plugins/steps/steps.css"> 
	

	<!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">

	<script type="application/javascript">
		function nuevoEstudiante(enviada){
			var nDoct = enviada.value;

			if(nDoct!=""){
				$('#nDocu').empty().hide().html("Validado documento...").show(1);

				datos = "nDoct="+(nDoct);
					$.ajax({
					type: "POST",
					url: "ajax-estudiantes-agregar.php",
					data: datos,
					success: function(data){
						$('#nDocu').empty().hide().html(data).show(1);
					}

				});

			}
		}

		function lugarNacimiento(data) {
			var idEnviado = data.id;
			var idDisabled = 'ciudadPro';

			if(idEnviado === 'ciudadPro') {
				idDisabled = 'lNacM';
			}

			if(data.value !== null && data.value !== "") {
				document.getElementById(idDisabled).disabled='disabled';
				document.getElementById(idDisabled).value='';
			} else {
				document.getElementById(idDisabled).disabled='';
			}
		}

		function habilitarCiudadProcedencia() {
			// Habilitar el campo de entrada con id "ciudadPro"
			document.getElementById("ciudadPro").disabled = false;

			// Obtener una referencia al elemento select con id "lNacM"
			const selectElement = document.getElementById("lNacM");

			// Verificar si hay una opción seleccionada
			if (selectElement.selectedIndex !== -1) {
				// Eliminar la opción seleccionada del select
				selectElement.remove(selectElement.selectedIndex);
			}
		}

		function mostrarCursosAdicionales(enviada) {
			var valor = enviada.value;
			if (valor == '<?=GRADO_INDIVIDUAL;?>') {
				document.getElementById("divCursosAdicionales").style.display='block';
			} else {
				document.getElementById("divCursosAdicionales").style.display='none';
			}
		}
	</script>

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
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="estudiantes.php" onClick="deseaRegresar(this)">Matrículas</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Crear matrículas</li>
                            </ol>
                        </div>
                    </div>

					
					<div class="card-body">

                        <div class="row" style="margin-bottom: 10px;">
                           	<div class="col-sm-12" align="center">
	                         	<p style="color: darkblue;"></p>
	                        </div>
                        </div>
						<span style="color: blue; font-size: 15px;" id="nDocu"></span>
                         
                    <!-- wizard with validation-->
                    <div class="row">
                    	<div class="col-sm-12">
							<?php include("../../config-general/mensajes-informativos.php"); ?>
                             <div class="card-box">
                                 <div class="card-head">
                                     <header>Matrículas</header>
                                 </div>

								 <div class="card-body">

                                    

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
													<select class="form-control  select2" name="tipoD" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php while($o = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
															if($o['ogen_id']==$datosMatricula['tipoD'])
															echo '<option value="'.$o['ogen_id'].'" selected>'.$o['ogen_nombre'].'</option>';
														else
															echo '<option value="'.$o['ogen_id'].'">'.$o['ogen_nombre'].'</option>';	
														}?>
													</select>
												</div>
											</div>

											
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Número de documento <span style="color: red;">(*)</span></label>
												<div class="col-sm-4">
													<input type="text" id="nDoc" name="nDoc" required class="form-control" autocomplete="off"  tabindex="<?=$contReg;?>" onChange="nuevoEstudiante(this)" value="<?=$datosMatricula['documento'];?>" <?=$disabledPermiso;?>>
												</div>

											</div>	
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de expedición</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="lugarD" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
														<option value="<?=$opg['ciu_id'];?>" <?php if($opg['ciu_id']==$datosMatricula['lugarEx']){echo "selected";}?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
											
											<?php if($config['conf_id_institucion'] == ICOLVEN){ ?>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Folio</label>
												<div class="col-sm-2">
													<input type="text" name="folio" class="form-control" autocomplete="off" value="<?=$datosMatricula['folio'];?>" <?=$disabledPermiso;?>>
												</div>
												
												<label class="col-sm-2 control-label">Codigo Tesoreria</label>
												<div class="col-sm-2">
													<input type="text" name="codTesoreria" class="form-control" autocomplete="off" value="<?=$datosMatricula['tesoreria'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
											<?php }?>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Primer apellido <span style="color: red;">(*)</span></label>
												<div class="col-sm-4">
													<input type="text" id="apellido1" name="apellido1" class="form-control" autocomplete="off" required value="<?=$datosMatricula['apellido1'];?>" <?=$disabledPermiso;?> style="text-transform: uppercase;">
												</div>
												
												<label class="col-sm-2 control-label">Segundo apellido</label>
												<div class="col-sm-4">
													<input type="text" id="apellido2" name="apellido2" class="form-control" autocomplete="off" value="<?=$datosMatricula['apellido2'];?>" <?=$disabledPermiso;?> style="text-transform: uppercase;">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Primer Nombre <span style="color: red;">(*)</span></label>
												<div class="col-sm-4">
													<input type="text" id="nombres" name="nombres" class="form-control" autocomplete="off" required value="<?=$datosMatricula['nombre'];?>" <?=$disabledPermiso;?> style="text-transform: uppercase;">
												</div>

												<label class="col-sm-2 control-label">Otro Nombre</label>
												<div class="col-sm-4">
													<input type="text" name="nombre2" class="form-control" autocomplete="off" value="<?=$datosMatricula['nombre2'];?>" <?=$disabledPermiso;?> style="text-transform: uppercase;">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Email</label>
												<div class="col-sm-6">
													<input type="text" name="email" class="form-control" value="<?=$datosMatricula['email'];?>" autocomplete="off" <?=$disabledPermiso;?> style="text-transform: lowercase;">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Fecha de nacimiento</label>
												<div class="col-sm-4">
													<div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
													<input class="form-control" size="16" type="text" value="<?=$datosMatricula['nacimiento'];?>" <?=$disabledPermiso;?>>
													<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
													</div>
												</div>
												<input type="hidden" id="dtp_input1" name="fNac">
											</div>

											<div style="text-align: center; padding: 10px;"><mark>Escoja una opción del listado ó escriba la ciudad de procedencia si el estudiante es extranjero</mark></div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de Nacimiento</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="lNacM" id="lNacM" onChange="lugarNacimiento(this)" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
														<option value="<?=$opg['ciu_id'];?>" <?php if($opg['ciu_id']==$datosMatricula['lugarNac']){echo "selected";}?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>
												
												<label class="col-sm-2 control-label">Ciudad de Procedencia</label>
												<div class="col-sm-4" id="ciudadPro2">
													<input type="text" name="ciudadPro" id="ciudadPro" onChange="lugarNacimiento(this)" ondblClick="habilitarCiudadProcedencia()" class="form-control" autocomplete="off" <?=$disabledPermiso;?>>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Género</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="genero" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
										  				$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
														while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o['ogen_id']==$datosMatricula['genero'])
																echo '<option value="'.$o['ogen_id'].'" selected>'.$o['ogen_nombre'].'</option>';
															else
																echo '<option value="'.$o['ogen_id'].'">'.$o['ogen_nombre'].'</option>';	
														}?>
													</select>
												</div>
											</div>

											<?php if($config['conf_id_institucion'] == ICOLVEN){ ?>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Grupo Sanguineo</label>
												<div class="col-sm-2">
													<input type="text" name="tipoSangre" class="form-control" autocomplete="off" value="<?=$datosMatricula['tipoSangre'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">EPS</label>
												<div class="col-sm-2">
													<input type="text" name="eps" class="form-control" autocomplete="off" value="<?=$datosMatricula['eps'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estudiante de Inclusión</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="inclusion" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosMatricula['inclusion']==1){echo "selected";}?>>Si</option>
														<option value="0"<?php if ($datosMatricula['inclusion']==0){echo "selected";}?>>No</option>
													</select>
												</div>
												
												
												<label class="col-sm-2 control-label">Religi&oacute;n</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="religion" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
										  				$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=2");
														while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o['ogen_id']==$datosMatricula['religion'])
																echo '<option value="'.$o['ogen_id'].'" selected>'.$o['ogen_nombre'].'</option>';
															else
																echo '<option value="'.$o['ogen_id'].'">'.$o['ogen_nombre'].'</option>';	
														}?>
													</select>
												</div>
											</div>
											<?php }?>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Extranjero?</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="extran" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<option value="1"<?php if ($datosMatricula['extran']==1){echo "selected";}?>>Si</option>
														<option value="0"<?php if ($datosMatricula['extran']==0){echo "selected";}?>>No</option>
													</select>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Grupo étnico?</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="grupoEtnico" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<option value="1">Ninguno</option>
                                                        <option value="2">Negro, mulato, afrocolombiano o afrodescendiente</option>
                                                        <option value="3">Raizal del archipielago de San Andrés, providencia y Santa Catalina</option>
                                                        <option value="4">Indigenas</option>
                                                        <option value="5">Rom (Gitano)</option>
                                                        <option value="6">Palenquero de San Basilio</option>
													</select>
												</div>
											</div>
											
											<?php
											$discapacidades = [
												1 => 'Ninguna',
												2 => 'Fisica',
												3 => 'Auditiva',
												4 => 'Visual',
												5 => 'Sordoceguera',
												6 => 'Intelectual/Cognitiva',
												7 => 'Psicosocial (mental)',
												8 => 'Multiple',
												9 => 'Autismo (transtorno del espectro autista - TEA) *',
												10 => 'Transtornos específicos de aprendizaje o del comportamiento',
												11 => 'Sordomudo *',
											];
											?>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Limitación o discapacidad</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="discapacidad" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach ($discapacidades as $id => $discapacidad) :?>
                                                            <option value="<?php echo $id;?>"><?php echo $discapacidad;?></option>
                                                        <?php endforeach;?>
													</select>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo de situación</label>
												<div class="col-sm-2">
													<select class="form-control" name="tipoSituacion" required <?=$disabledPermiso;?>>
                                                        <option value="1">Ninguna</option>
                                                        <option value="2">Desplazado, victima del conflicto</option>
                                                        <option value="3">Desmovilizado del conflicto armado</option>
                                                    </select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Direcci&oacute;n</label>
												<div class="col-sm-4">
													<input type="text" name="direccion" class="form-control" autocomplete="off" value="<?=$datosMatricula['direcion'];?>" <?=$disabledPermiso;?>>
												</div>
												<div class="col-sm-4">
													<input type="text" name="barrio" class="form-control" placeholder="Barrio" autocomplete="off" value="<?=$datosMatricula['barrio'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Ciudad de residencia</label>
												<div class="col-sm-4">
													<select class="form-control  select2" name="ciudadR" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento 
														ORDER BY ciu_nombre
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
															$selected='';
															$opg['ciu_codigo'] = trim($opg['ciu_codigo']);
															if($opg['ciu_codigo']==$datosMatricula['ciudadR']){
																$selected='selected';
															}
	
															?>
															<option value="<?=$opg['ciu_codigo'];?>" <?=$selected;?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
															<?php }?>
													</select>
												</div>
											</div>
											<?php if($config['conf_id_institucion'] == ICOLVEN){ ?>	
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estrato</label>
												<div class="col-sm-2">
													<select class="form-control  select2" name="estrato" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
															$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=3");
														while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o['ogen_id']==$datosMatricula['estrato'])
																echo '<option value="'.$o['ogen_id'].'" selected>'.$o['ogen_nombre'].'</option>';
															else
																echo '<option value="'.$o['ogen_id'].'">'.$o['ogen_nombre'].'</option>';	
														}?>
													</select>
												</div>
											</div>
											<?php }?>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Contactos</label>
												<div class="col-sm-2">
													<input type="text" name="telefono" class="form-control" placeholder="Telefono" <?=$_SESSION['idInstitucion'] != ICOLVEN ? 'data-mask="999-9999"' : "";?> autocomplete="off" value="<?=$datosMatricula['telefono'];?>" <?=$disabledPermiso;?>>
												</div>
												<div class="col-sm-2">
													<input type="text" name="celular" class="form-control" placeholder="celular" <?=$_SESSION['idInstitucion'] != ICOLVEN ? 'data-mask="(999) 999-9999"' : "";?> autocomplete="off" value="<?=$datosMatricula['celular'];?>" <?=$disabledPermiso;?>>
												</div>
												<div class="col-sm-2">
													<input type="text" name="celular2" class="form-control" placeholder="celular #2" <?=$_SESSION['idInstitucion'] != ICOLVEN ? 'data-mask="(999) 999-9999"' : "";?> autocomplete="off" value="<?=$datosMatricula['celular2'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>								   
									       
										</fieldset>
										
									    <h3>Información académica</h3>
									    <fieldset>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Curso <span style="color: red;">(*)</span></label>
												<div class="col-sm-4">
													<select class="form-control" name="grado" required <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
                                                		$opcionesConsulta = Grados::traerGradosInstitucion($config, GRADO_GRUPAL);
														while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
															if($opcionesDatos['gra_id']==$datosMatricula['grado'])
																echo '<option value="'.$opcionesDatos['gra_id'].'" selected>'.$opcionesDatos['gra_nombre'].'</option>';
															else
																echo '<option value="'.$opcionesDatos['gra_id'].'">'.$opcionesDatos['gra_nombre'].'</option>';	
														}?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Grupo</label>
												<div class="col-sm-2">
													<select class="form-control" name="grupo" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
                        								$opcionesConsulta = Grupos::listarGrupos();
														while($rv = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
															if($rv['gru_id']==$datosMatricula['grupo'])
																echo '<option value="'.$rv['gru_id'].'" selected>'.$rv['gru_nombre'].'</option>';
															else
																echo '<option value="'.$rv['gru_id'].'">'.$rv['gru_nombre'].'</option>';	
														}?>
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
													<select class="form-control" name="tipoEst" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
														while($o = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
															if($o['ogen_id']==$datosMatricula['tipoE'])
																echo '<option value="'.$o['ogen_id'].'" selected>'.$o['ogen_nombre'].'</option>';
															else
																echo '<option value="'.$o['ogen_id'].'">'.$o['ogen_nombre'].'</option>';	
														}?>
													</select>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Estado Matricula</label>
												<div class="col-sm-4">
													<select class="form-control" name="matestM" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php foreach( $estadosMatriculasEstudiantes as $clave => $valor ) {?>
															<option value="<?=$clave;?>"><?=$valor;?></option>
														<?php } ?>
													</select>
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Valor Matricula</label>
												<div class="col-sm-2">
													<input type="text" name="va_matricula" class="form-control" autocomplete="off" value="<?=$datosMatricula['vaMatricula'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
											<?php if (array_key_exists(10, $arregloModulos)) { ?>
												<div class="form-group row">
													<label class="col-sm-2 control-label"> Puede estar en multiples cursos? </label>
													<div class="col-sm-2">
														<select class="form-control" onchange="mostrarCursosAdicionales(this)" name="tipoMatricula">
															<option value="<?=GRADO_GRUPAL;?>" selected>NO</option>
															<option value="<?=GRADO_INDIVIDUAL;?>">SI</option>
														</select>
													</div>
												</div>

												<div id="divCursosAdicionales" style="display: none;">
													<div class="form-group row">
														<label class="col-sm-2 control-label" >Cursos adicionales</label>
														<div class="col-sm-4" >
															<?php
															$parametros = ['gra_tipo' => GRADO_INDIVIDUAL, 'gra_estado' => 1, 'institucion' => $config['conf_id_institucion'], 'year' => $_SESSION["bd"]];
															$listaIndividuales = GradoServicios::listarCursos($parametros);
															?>
															<select id="cursosAdicionales" class="form-control select2-multiple" style="width: 100% !important" name="cursosAdicionales[]" onchange="mostrarGrupoCursosAdicionales(this)" multiple>
																<option value="">Seleccione una opción</option>
																<?php
																foreach ($listaIndividuales as $clave => $dato) {
																	$disabled = '';
																	if ($dato['gra_estado'] == '0') {
																		$disabled = 'disabled';
																	};
																	echo '<option value="' . $dato["gra_id"] . '" ' . $disabled . '>' . $dato['gra_id'] . '.' . strtoupper($dato['gra_nombre']) . '</option>';
																}
																?>
															</select>
														</div>
													</div>
			
													<script type="application/javascript">
														$(document).ready(mostrarGrupoCursosAdicionales(document.getElementById("cursosAdicionales")))
														function mostrarGrupoCursosAdicionales(enviada) {
															var valor = enviada.value;
															if (valor != '') {
																document.getElementById("divGradoMT").style.display='block';
															} else {
																document.getElementById("divGradoMT").style.display='none';
															}
														}
													</script>
													<div id="divGradoMT" style="display: none;">
														<div class="form-group row" >
															<label class="col-sm-2 control-label">Grupo Cursos Adicionales</label>
															<div class="col-sm-4">
																<select class="form-control" name="grupoMT">
																<?php
                        										$cv = Grupos::listarGrupos(); 
																while($rv = mysqli_fetch_array($cv, MYSQLI_BOTH)){
																	echo '<option value="'.$rv['gru_id'].'">'.$rv['gru_nombre'].'</option>';
																}?>
																</select>
															</div>
														</div>
													</div>
												</div>
											<?php } ?>
											
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
													<select class="form-control" name="tipoDAcudiente" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
														while($o = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
															if($o['ogen_id']==$datosMatricula['tipoDocA'])
															echo '<option value="'.$o['ogen_id'].'" selected>'.$o['ogen_nombre'].'</option>';
														else
															echo '<option value="'.$o['ogen_id'].'">'.$o['ogen_nombre'].'</option>';	
														}?>
													</select>
												</div>
                        
												<label class="col-sm-2 control-label">Documento <span style="color: red;">(*)</span></label>
												<div class="col-sm-3">
                          
                        <div class="cargando row">       
                        <div class="d-flex justify-content-center">
                          <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Verificando Documento, Espere Por Favor!</span>
                          </div>
                        </div>
                      </div>

													<input type="text" name="documentoA" id="doc" onblur="buscar_datos();" class="form-control"  required value="<?=$datosMatricula['documentoA'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
												
											<div class="form-group row">
												<label class="col-sm-2 control-label">Lugar de expedición</label>
												<div class="col-sm-4">
													<select class="form-control" id="lugardE" name="lugarDa" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
														INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
														");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
														<option value="<?=$opg['ciu_id'];?>" <?php if($opg['ciu_id']==$datosMatricula['expedicionA']){echo "selected";}?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
														<?php }?>
													</select>
												</div>

												<?php if($config['conf_id_institucion'] ==  ICOLVEN){ ?>
												<label class="col-sm-2 control-label">Ocupaci&oacute;n</label>
												<div class="col-sm-3">
													<input type="text" name="ocupacionA" class="form-control" autocomplete="off" value="<?=$datosMatricula['ocupacionA'];?>" <?=$disabledPermiso;?>>
												</div>
												<?php }?>

											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Primer Apellido</label>
												<div class="col-sm-3">
													<input type="text" name="apellido1A" id="apellido1A" class="form-control"  value="<?=$datosMatricula['apellido1A'];?>" <?=$disabledPermiso;?>>
												</div>
																							
												<label class="col-sm-2 control-label">Segundo Apellido</label>
												<div class="col-sm-3">
													<input type="text" name="apellido2A" id="apellido2A" class="form-control"  value="<?=$datosMatricula['apellido2A'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>

											<div class="form-group row">												
												<label class="col-sm-2 control-label">Nombre <span style="color: red;">(*)</span></label>
												<div class="col-sm-3">
													<input type="text" name="nombresA" id="nombresA" class="form-control"  required value="<?=$datosMatricula['nombreA'];?>" <?=$disabledPermiso;?>>
												</div>
																								
												<label class="col-sm-2 control-label">Otro Nombre</label>
												<div class="col-sm-3">
													<input type="text" name="nombre2A" id="nombre2A" class="form-control"  value="<?=$datosMatricula['documentoA'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>	
												
											<?php if($config['conf_id_institucion'] == ICOLVEN){ ?>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Fecha de nacimiento</label>
												<div class="col-sm-3">
													<div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
													<input class="form-control" size="16" type="text" <?=$disabledPermiso;?>>
													<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
													</div>
												</div>
												<input type="hidden" id="dtp_input1" name="fechaNA">

												<label class="col-sm-2 control-label">Genero</label>
												<div class="col-sm-3">
													<select class="form-control select2" name="generoA" <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<?php
										  				$op = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
														while($o = mysqli_fetch_array($op, MYSQLI_BOTH)){
															if($o['ogen_id']==$datosMatricula['generoA'])
																echo '<option value="'.$o['ogen_id'].'" selected>'.$o['ogen_nombre'].'</option>';
															else
																echo '<option value="'.$o['ogen_id'].'">'.$o['ogen_nombre'].'</option>';	
														}?>
													</select>
												</div>
											</div>
											<?php }?>									   
									       
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
  <script type="text/javascript">
  $(document).ready(function(){
        $('.cargando').hide();
      });  
  function buscar_datos()
  {
    doc = $("#doc").val();
    var parametros = 
    {
      "buscar": "1",
      "uss_usuario" : doc
    };
    $.ajax(
    {
      data:  parametros,
      dataType: 'json',
      url:   'ajax-comprobar-acudiente.php',
      type:  'post',
      beforeSend: function() 
      {
        $('.cargando').show();
      }, 
      error: function()
      {alert("Error");},
      complete: function() 
      {
        $('.cargando').hide();
      },
      success:  function (valores) 
      {
         $("#apellido1A").val(valores.apellido1);
          $("#apellido2A").val(valores.apellido2);
          $("#nombresA").val(valores.nombre1);
          $("#nombre2A").val(valores.nombre2);
          $("#lugardE").val(valores.lugardE);
      }
    }) 
  }
  </script>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
	<script src="../../config-general/assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
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