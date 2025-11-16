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
	
	<!-- 📝 Estilos mejorados para formulario de estudiante -->
	<link href="../css/formulario-estudiante.css?v=<?=time()?>" rel="stylesheet" type="text/css" />

	<!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
    <!-- Fix para iconos de calendario -->
    <link href="../../config-general/assets/css/datepicker-fontawesome-fix.css" rel="stylesheet" type="text/css" />

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
                                    <form name="example_advanced_form" id="example-advanced-form" action="estudiantes-guardar.php" method="post" novalidate>
									  <?php echo Csrf::campoHTML(); ?>
									  
										<h3>Información personal</h3>
									    <fieldset>
								<div class="row"><div class="col-sm-12"><h4 class="section-toggle"><i class="fa fa-id-card"></i> Identificación <span class="toggle-indicator">▼</span></h4></div></div>
									

											
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
												
								<div class="row"><div class="col-sm-12"><h4 class="section-toggle"><i class="fa fa-user"></i> Nombres y datos básicos <span class="toggle-indicator">▼</span></h4></div></div>
								
								<!-- ✅ NOMBRES - Lo más importante después del documento -->
								<div class="form-group row">
												<label class="col-sm-2 control-label"><i class="fa fa-user-circle"></i> Primer apellido <span style="color: red;">(*)</span></label>
												<div class="col-sm-4">
													<input type="text" id="apellido1" name="apellido1" class="form-control" autocomplete="off" required value="<?=$datosMatricula['apellido1'];?>" <?=$disabledPermiso;?> style="text-transform: uppercase;">
												</div>
												
												<label class="col-sm-2 control-label">Segundo apellido</label>
												<div class="col-sm-4">
													<input type="text" id="apellido2" name="apellido2" class="form-control" autocomplete="off" value="<?=$datosMatricula['apellido2'];?>" <?=$disabledPermiso;?> style="text-transform: uppercase;">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label"><i class="fa fa-user"></i> Primer Nombre <span style="color: red;">(*)</span></label>
												<div class="col-sm-4">
													<input type="text" id="nombres" name="nombres" class="form-control" autocomplete="off" required value="<?=$datosMatricula['nombre'];?>" <?=$disabledPermiso;?> style="text-transform: uppercase;">
												</div>

												<label class="col-sm-2 control-label">Otro Nombre</label>
												<div class="col-sm-4">
													<input type="text" name="nombre2" class="form-control" autocomplete="off" value="<?=$datosMatricula['nombre2'];?>" <?=$disabledPermiso;?> style="text-transform: uppercase;">
												</div>
											</div>
											
											<!-- Lugar de expedición del documento -->
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
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Email</label>
												<div class="col-sm-6">
													<input type="text" name="email" class="form-control" value="<?=$datosMatricula['email'];?>" autocomplete="off" <?=$disabledPermiso;?> style="text-transform: lowercase;">
												</div>
											</div>
											
							<div class="form-group row" id="fNacGroup">
												<label class="col-sm-2 control-label">Fecha de nacimiento</label>
												<div class="col-sm-4">
									<div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd" data-date-enddate="<?=date('Y-m-d', strtotime('-1 year'));?>">
									<input class="form-control" size="16" type="text" value="<?=$datosMatricula['nacimiento'];?>" <?=$disabledPermiso;?> readonly aria-describedby="fNacError" aria-invalid="false">
													<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
													</div>
									<small id="fNacError" class="text-danger" style="display:none;">La fecha de nacimiento no puede ser futura ni menor de 1 año.</small>
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
											
								<div class="row"><div class="col-sm-12"><h4 class="section-toggle"><i class="fa fa-map-marker"></i> Residencia y contacto <span class="toggle-indicator">▼</span></h4></div></div>
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
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Contactos</label>
												<div class="col-sm-2">
													<input type="text" name="telefono" class="form-control" placeholder="Telefono" autocomplete="off" value="<?=$datosMatricula['telefono'];?>" <?=$disabledPermiso;?>>
												</div>
												<div class="col-sm-2">
													<input type="text" name="celular" class="form-control" placeholder="celular" autocomplete="off" value="<?=$datosMatricula['celular'];?>" <?=$disabledPermiso;?>>
												</div>
												<div class="col-sm-2">
													<input type="text" name="celular2" class="form-control" placeholder="celular #2" autocomplete="off" value="<?=$datosMatricula['celular2'];?>" <?=$disabledPermiso;?>>
												</div>
											</div>
											
											<!-- ============================================= -->
											<!-- CAMPOS ADICIONALES DEL ESTUDIANTE            -->
											<!-- Solo visible para instituciones específicas  -->
											<!-- ============================================= -->
											<?php if($config['conf_id_institucion'] == ICOLVEN){ ?>
											<div class="conditional-section">
												<div class="conditional-section-header" onclick="$(this).next('.conditional-section-body').slideToggle(300); $(this).find('.toggle-indicator').text(function(i,txt){return txt==='▼'?'▶':'▼';});">
													<i class="fa fa-cog"></i>
													<strong>Campos Adicionales del Estudiante</strong>
													<span class="conditional-badge">Institución Específica</span>
													<span class="toggle-indicator">▼</span>
												</div>
												
												<div class="conditional-section-body" style="display: none;">
													<div class="form-group row">
														<label class="col-sm-2 control-label">Folio</label>
														<div class="col-sm-2">
															<input type="text" name="folio" class="form-control" autocomplete="off" value="<?=$datosMatricula['folio'];?>" <?=$disabledPermiso;?>>
														</div>
														
														<label class="col-sm-2 control-label">Código Tesorería</label>
														<div class="col-sm-2">
															<input type="text" name="codTesoreria" class="form-control" autocomplete="off" value="<?=$datosMatricula['tesoreria'];?>" <?=$disabledPermiso;?>>
														</div>
													</div>
													
													<div class="form-group row">
														<label class="col-sm-2 control-label">Grupo Sanguíneo</label>
														<div class="col-sm-2">
															<input type="text" name="tipoSangre" class="form-control" autocomplete="off" value="<?=$datosMatricula['tipoSangre'];?>" <?=$disabledPermiso;?>>
														</div>
														
														<label class="col-sm-2 control-label">EPS</label>
														<div class="col-sm-2">
															<input type="text" name="eps" class="form-control" autocomplete="off" value="<?=$datosMatricula['eps'];?>" <?=$disabledPermiso;?>>
														</div>
													</div>
													
													<div class="form-group row">
														<label class="col-sm-2 control-label">Estudiante de Inclusión</label>
														<div class="col-sm-2">
															<select class="form-control select2" name="inclusion" <?=$disabledPermiso;?>>
																<option value="">Seleccione una opción</option>
																<option value="1"<?php if ($datosMatricula['inclusion']==1){echo " selected";}?>>Si</option>
																<option value="0"<?php if ($datosMatricula['inclusion']==0){echo " selected";}?>>No</option>
															</select>
														</div>
														
														<label class="col-sm-2 control-label">Religión</label>
														<div class="col-sm-2">
															<select class="form-control select2" name="religion" <?=$disabledPermiso;?>>
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
													
													<div class="form-group row">
														<label class="col-sm-2 control-label">Estrato</label>
														<div class="col-sm-2">
															<select class="form-control select2" name="estrato" <?=$disabledPermiso;?>>
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
												</div>
											</div>
											<?php } ?>								   
									       
							</fieldset>
										
									    <h3>Información académica</h3>
									    <fieldset>
								<div class="row"><div class="col-sm-12"><h4 class="section-toggle"><i class="fa fa-graduation-cap"></i> Curso y grupo <span class="toggle-indicator">▼</span></h4></div></div>

								<div class="row"><div class="col-sm-12"><h4 class="section-toggle"><i class="fa fa-list-alt"></i> Tipo y estado académico <span class="toggle-indicator">▼</span></h4></div></div>
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
												
								<div class="row"><div class="col-sm-12"><h4 class="section-toggle"><i class="fa fa-dollar"></i> Información de pagos <span class="toggle-indicator">▼</span></h4></div></div>
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
							<div class="row"><div class="col-sm-12"><h4 class="section-toggle"><i class="fa fa-users"></i> Identificación del acudiente <span class="toggle-indicator">▼</span></h4></div></div>
							<div class="row"><div class="col-sm-12"><h4 class="section-toggle"><i class="fa fa-id-badge"></i> Datos del acudiente <span class="toggle-indicator">▼</span></h4></div></div>
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
													<div style="position: relative;">
														<input type="text" name="documentoA" id="doc" onblur="buscar_datos();" class="form-control"  required value="<?=$datosMatricula['documentoA'];?>" <?=$disabledPermiso;?>>
														<span class="cargando" style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
															<i class="fa fa-spinner fa-spin" style="color: #667eea; font-size: 16px;"></i>
														</span>
													</div>
													<small class="form-text text-muted" style="display: none;" id="verificando-text">
														<i class="fa fa-info-circle"></i> Verificando si el acudiente ya existe...
													</small>
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
												
											<div class="form-group row">
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
											
											<!-- ============================================= -->
											<!-- CAMPOS ADICIONALES DEL ACUDIENTE             -->
											<!-- Solo visible para instituciones específicas  -->
											<!-- ============================================= -->
											<?php if($config['conf_id_institucion'] == ICOLVEN){ ?>
											<div class="conditional-section">
												<div class="conditional-section-header" onclick="$(this).next('.conditional-section-body').slideToggle(300); $(this).find('.toggle-indicator').text(function(i,txt){return txt==='▼'?'▶':'▼';});">
													<i class="fa fa-cog"></i>
													<strong>Campos Adicionales del Acudiente</strong>
													<span class="conditional-badge">Institución Específica</span>
													<span class="toggle-indicator">▼</span>
												</div>
												
												<div class="conditional-section-body" style="display: none;">
													<div class="form-group row">
														<label class="col-sm-2 control-label">Ocupación</label>
														<div class="col-sm-3">
															<input type="text" name="ocupacionA" class="form-control" autocomplete="off" value="<?=$datosMatricula['ocupacionA'];?>" <?=$disabledPermiso;?>>
														</div>
													</div>
													
													<div class="form-group row">
														<label class="col-sm-2 control-label">Fecha de nacimiento</label>
														<div class="col-sm-3" id="fNacAGroup">
															<div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd" data-date-enddate="<?=date('Y-m-d', strtotime('-14 year'));?>">
																<input class="form-control" size="16" type="text" <?=$disabledPermiso;?> readonly aria-describedby="fNacAError" aria-invalid="false">
																<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
															</div>
															<small id="fNacAError" class="text-danger" style="display:none;">El acudiente debe tener al menos 14 años.</small>
														</div>
														<input type="hidden" id="dtp_input2" name="fechaNA">
													</div>
												</div>
											</div>
											<?php } ?>									   
									       
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
    
    // Solo buscar si hay documento
    if (!doc || doc.trim() === '') {
      return;
    }
    
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
        $('.cargando').fadeIn(200);
        $('#verificando-text').fadeIn(200);
      }, 
      error: function()
      {
        console.error("Error al verificar acudiente");
      },
      complete: function() 
      {
        $('.cargando').fadeOut(200);
        $('#verificando-text').fadeOut(200);
      },
      success:  function (valores) 
      {
         if (valores && valores.apellido1) {
           $("#apellido1A").val(valores.apellido1);
           $("#apellido2A").val(valores.apellido2);
           $("#nombresA").val(valores.nombre1);
           $("#nombre2A").val(valores.nombre2);
           $("#lugardE").val(valores.lugardE);
           
           // Feedback visual sutil
           $("#doc").parent().find('small').remove();
           $("#doc").parent().append('<small class="form-text text-success"><i class="fa fa-check-circle"></i> Acudiente encontrado en el sistema</small>');
           setTimeout(function() {
             $("#doc").parent().find('small.text-success').fadeOut(300, function() { $(this).remove(); });
           }, 3000);
         }
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
    <!-- Fix para iconos de calendario -->
    <script src="../../config-general/assets/js/datepicker-fontawesome-fix.js"></script>
    <script src="../../config-general/assets/plugins/jquery-validation/js/additional-methods.min.js"></script>
    <div id="submitOverlay" style="display:none; position:fixed; inset:0; background:rgba(255,255,255,0.7); z-index: 2000;">
    	<div style="position:absolute; top:40%; left:50%; transform:translate(-50%,-50%); width:320px;">
    		<div class="progress">
    			<div id="submitProgress" class="progress-bar progress-bar-striped active" role="progressbar" style="width: 30%">Procesando…</div>
    		</div>
    		<div style="text-align:center; margin-top:8px; color:#333;">Guardando matrícula, por favor espere…</div>
    	</div>
    </div>
    <div id="successModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    	<div class="modal-dialog" role="document">
    		<div class="modal-content">
    			<div class="modal-header">
    				<h5 class="modal-title">Estudiante creado</h5>
    				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    			</div>
    			<div class="modal-body">
    				<p>El estudiante se creó correctamente. ¿Qué deseas hacer?</p>
    			</div>
    			<div class="modal-footer">
    				<a href="estudiantes.php" class="btn btn-info"><i class="fa fa-list"></i> Ver Listado</a>
    				<button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnAddAnother">Agregar otro</button>
    				<a href="#" class="btn btn-primary" id="btnGoEdit">Ir a editar</a>
    			</div>
    		</div>
    	</div>
    </div>
    <script type="text/javascript">
    (function(){
    	var today = new Date();
    	var maxDate = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate());
    	// Aplica sólo al datepicker enlazado al campo oculto dtp_input1 (fecha de nacimiento del estudiante)
    	var $picker = $(".form_date[data-link-field='dtp_input1']");
    	if ($picker.length && typeof $picker.datetimepicker === 'function') {
    		$picker.datetimepicker('setEndDate', maxDate);
		// Validación inmediata al cambiar la fecha (incluye edición programática)
		var $hidden = $("#dtp_input1");
		var $input = $picker.find('input.form-control');
		var $error = $("#fNacError");
		var $group = $("#fNacGroup");
		var showError = function(msg){
			if(msg){ $error.text(msg); }
			$error.show();
			$input.attr('aria-invalid','true');
			$group.addClass('has-error');
		};
		var clearError = function(){
			$error.hide();
			$input.attr('aria-invalid','false');
			$group.removeClass('has-error');
		};
		var validateDate = function(){
			var val = $hidden.val();
			if(!val){ clearError(); return; }
			var parts = val.split('-');
			if(parts.length !== 3){
				$hidden.val('');
				$input.val('');
				showError('Fecha de nacimiento inválida.');
				return;
			}
			var selected = new Date(parseInt(parts[0],10), parseInt(parts[1],10)-1, parseInt(parts[2],10));
			var todayLocal = new Date();
			var max = new Date(todayLocal.getFullYear() - 1, todayLocal.getMonth(), todayLocal.getDate());
			if(selected > max){
				$hidden.val('');
				$input.val('');
				showError('La fecha de nacimiento no puede ser futura ni menor de 1 año.');
				return;
			}
			clearError();
		};
		$hidden.on('change', validateDate);
		$picker.on('changeDate', validateDate);
    	}

	// Validación para fecha de nacimiento del acudiente (mínimo 14 años)
	var $pickerA = $(".form_date[data-link-field='dtp_input2']");
	if ($pickerA.length && typeof $pickerA.datetimepicker === 'function') {
		var todayA = new Date();
		var maxDateA = new Date(todayA.getFullYear() - 14, todayA.getMonth(), todayA.getDate());
		$pickerA.datetimepicker('setEndDate', maxDateA);
		var $hiddenA = $("#dtp_input2");
		var $inputA = $pickerA.find('input.form-control');
		var $errorA = $("#fNacAError");
		var $groupA = $("#fNacAGroup");
		var showErrorA = function(msg){ if(msg){ $errorA.text(msg); } $errorA.show(); $inputA.attr('aria-invalid','true'); $groupA.addClass('has-error'); };
		var clearErrorA = function(){ $errorA.hide(); $inputA.attr('aria-invalid','false'); $groupA.removeClass('has-error'); };
		var validateDateA = function(){
			var val = $hiddenA.val();
			if(!val){ clearErrorA(); return; }
			var parts = val.split('-');
			if(parts.length !== 3){ $hiddenA.val(''); $inputA.val(''); showErrorA('Fecha inválida.'); return; }
			var selected = new Date(parseInt(parts[0],10), parseInt(parts[1],10)-1, parseInt(parts[2],10));
			var todayLocal = new Date();
			var max = new Date(todayLocal.getFullYear() - 14, todayLocal.getMonth(), todayLocal.getDate());
			if(selected > max){ $hiddenA.val(''); $inputA.val(''); showErrorA('El acudiente debe tener al menos 14 años.'); return; }
			clearErrorA();
		};
		$hiddenA.on('change', validateDateA);
		$pickerA.on('changeDate', validateDateA);
	}
    })();
    </script>
    <script type="text/javascript">
    (function(){
    	var $form = $("#example-advanced-form");
    	// Asegurar navegación por TAB natural con tabindex secuencial
    	$form.find('input, select, textarea, button').each(function(index){
    		if(!this.hasAttribute('tabindex')){ this.setAttribute('tabindex', (index+1)); }
    	});

    	// Configurar jQuery Validate visual
    	if ($.fn.validate) {
    		$form.validate({
    			errorClass: 'text-danger',
    			errorElement: 'small',
    			highlight: function(element){
    				$(element).attr('aria-invalid','true').closest('.form-group').addClass('has-error');
    			},
    			unhighlight: function(element){
    				$(element).attr('aria-invalid','false').closest('.form-group').removeClass('has-error');
    			},
    			errorPlacement: function(error, element){
    				var $container = element.closest('.col-sm-4, .col-sm-3, .col-sm-2, .col-sm-6');
    				if($container.length){ error.appendTo($container); } else { error.insertAfter(element); }
    			},
    			ignore: ':hidden:not(#dtp_input1)',
    			rules: {
    				nDoc: { required: true },
    				apellido1: { required: true },
    				nombres: { required: true },
    				grado: { required: true },
							documentoA: { required: true },
							email: { email: true }
    			},
    			messages: {
    				nDoc: 'Número de documento es obligatorio.',
    				apellido1: 'Primer apellido es obligatorio.',
    				nombres: 'Primer nombre es obligatorio.',
							grado: 'Curso es obligatorio.',
							documentoA: 'Documento del acudiente es obligatorio.',
							email: 'Ingresa un correo electrónico válido.'
    			}
    		});
    	}

    	// Interceptar submit y enviar vía fetch con progreso
    	$form.on('submit', function(ev){
    		ev.preventDefault();
    		if ($.fn.validate && !$form.valid()) { return; }
    		var $overlay = $('#submitOverlay');
    		var $bar = $('#submitProgress');
    		$overlay.show();
    		var grow = 35;
    		var timer = setInterval(function(){
    			grow = Math.min(95, grow + Math.random()*7);
    			$bar.css('width', grow+'%');
    		}, 300);
    		var formData = new FormData($form[0]);
        fetch($form.attr('action'), {
    			method: 'POST',
    			body: formData,
    			headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    			credentials: 'same-origin'
        }).then(function(r){
            var ct = r.headers.get('content-type') || '';
            if (ct.indexOf('application/json') !== -1) { return r.json(); }
            return r.text().then(function(t){ return { ok:false, message:'Respuesta inesperada del servidor', raw:t }; });
    		}).then(function(json){
    			if (json && json.ok) {
    				$bar.css('width','100%');
    				setTimeout(function(){
    					clearInterval(timer); $overlay.hide();
    					$('#btnGoEdit').attr('href', json.editUrl);
    					$('#successModal').modal('show');
    					$('#btnAddAnother').off('click').on('click', function(){ window.location.href = 'estudiantes-agregar.php'; });
    				}, 300);
            } else {
    				clearInterval(timer); $overlay.hide();
                var message = (json && json.message) ? json.message : 'No se pudo guardar. Verifique los datos.';
                // Mostrar error inline en el paso actual
                try {
                    var $currentBody = $form.find('.body:visible').first();
                    var $existing = $currentBody.find('.server-error');
                    var html = '<div class="server-error alert alert-danger" role="alert" style="margin-bottom:15px;">'+ message +'</div>';
                    if ($existing.length) { $existing.replaceWith(html); } else { $currentBody.prepend(html); }
                } catch(e) { alert(message); }
    				if (json && json.field) {
    					var $el = $('[name="'+json.field+'"]');
    					if ($el.length) { $el.focus(); }
    				}
    			}
        }).catch(function(err){
            clearInterval(timer); $overlay.hide();
            try {
                var $currentBody = $form.find('.body:visible').first();
                var $existing = $currentBody.find('.server-error');
                var html = '<div class="server-error alert alert-danger" role="alert" style="margin-bottom:15px;">Ocurrió un error de red. Intente nuevamente.</div>';
                if ($existing.length) { $existing.replaceWith(html); } else { $currentBody.prepend(html); }
            } catch(e) { alert('Ocurrió un error de red. Intente nuevamente.'); }
        });
        });

        // Tooltips de ayuda en campos
        try {
            var helpTexts = {
                tipoD: 'Selecciona el tipo de documento del estudiante.',
                nDoc: 'Escribe el número de documento del estudiante (único).',
                lugarD: 'Ciudad donde fue expedido el documento del estudiante.',
                folio: 'Número de folio si aplica.',
                codTesoreria: 'Código interno de tesorería si aplica.',
                apellido1: 'Primer apellido del estudiante (obligatorio).',
                apellido2: 'Segundo apellido del estudiante (opcional).',
                nombres: 'Primer nombre del estudiante (obligatorio).',
                nombre2: 'Segundo nombre del estudiante (opcional).',
                email: 'Correo del estudiante para notificaciones.',
                fNac: 'Fecha de nacimiento del estudiante (mínimo 1 año).',
                lNacM: 'Lugar de nacimiento del estudiante.',
                ciudadPro: 'Si es extranjero, escribe su ciudad de procedencia.',
                genero: 'Selecciona el género del estudiante.',
                tipoSangre: 'Grupo sanguíneo del estudiante.',
                eps: 'Entidad de salud (EPS) del estudiante.',
                inclusion: 'Indica si el estudiante es de inclusión.',
                religion: 'Religión del estudiante.',
                extran: 'Indica si el estudiante es extranjero.',
                grupoEtnico: 'Grupo étnico del estudiante.',
                discapacidad: 'Limitación o discapacidad del estudiante.',
                tipoSituacion: 'Situación especial (si aplica).',
                direccion: 'Dirección de residencia del estudiante.',
                barrio: 'Barrio donde reside el estudiante.',
                ciudadR: 'Ciudad de residencia del estudiante.',
                estrato: 'Estrato socioeconómico.',
                telefono: 'Teléfono fijo de contacto.',
                celular: 'Celular principal de contacto.',
                celular2: 'Celular alterno de contacto.',
                grado: 'Curso al que ingresará el estudiante.',
                grupo: 'Grupo asignado (si aplica).',
                tipoEst: 'Tipo de estudiante (nuevo, antiguo, etc.).',
                matestM: 'Estado de la matrícula.',
                va_matricula: 'Valor de la matrícula.',
                tipoMatricula: 'Permitir múltiples cursos si aplica.',
                cursosAdicionales: 'Cursos individuales adicionales.',
                grupoMT: 'Grupo para cursos adicionales.',
                tipoDAcudiente: 'Tipo de documento del acudiente.',
                documentoA: 'Número de documento del acudiente (obligatorio).',
                lugarDa: 'Lugar de expedición del documento del acudiente.',
                ocupacionA: 'Ocupación del acudiente.',
                apellido1A: 'Primer apellido del acudiente.',
                apellido2A: 'Segundo apellido del acudiente.',
                nombresA: 'Nombres del acudiente (obligatorio).',
                nombre2A: 'Segundo nombre del acudiente (opcional).',
                fechaNA: 'Fecha de nacimiento del acudiente (mínimo 14 años).',
                generoA: 'Género del acudiente.'
            };
            function addHelpIconFor(fieldName){
                var txt = helpTexts[fieldName];
                if (!txt) return;
                var $field = $form.find('[name="'+fieldName+'"]');
                if (!$field.length) return;
                var $group = $field.closest('.form-group');
                var $label = $group.find('> label').first();
                if (!$label.length) return;
                if ($label.find('.help-icon').length) return;
                var $icon = $('<span class="help-icon fa fa-question-circle text-muted" tabindex="0" data-toggle="tooltip" data-placement="right" title="'+txt+'" style="margin-left:6px;"></span>');
                $label.append($icon);
            }
            Object.keys(helpTexts).forEach(addHelpIconFor);
				// Inicializar tooltips Bootstrap
				$('[data-toggle="tooltip"]').tooltip({ container: 'body', trigger: 'hover focus' });
				
				// Validación inmediata de email al escribir
				var $emailField = $form.find('[name="email"]');
				if ($emailField.length) {
					var $emailGroup = $emailField.closest('.form-group');
					var $emailError = $('<small class="text-danger" style="display:none;">Ingresa un correo electrónico válido.</small>');
					$emailField.after($emailError);
					
					var validateEmail = function() {
						var val = $emailField.val().trim();
						if (val === '') {
							$emailError.hide();
							$emailField.removeClass('is-invalid').attr('aria-invalid', 'false');
							$emailGroup.removeClass('has-error');
							return;
						}
						var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
						if (!emailRegex.test(val)) {
							$emailError.show();
							$emailField.addClass('is-invalid').attr('aria-invalid', 'true');
							$emailGroup.addClass('has-error');
						} else {
							$emailError.hide();
							$emailField.removeClass('is-invalid').attr('aria-invalid', 'false');
							$emailGroup.removeClass('has-error');
						}
					};
					
					$emailField.on('input blur', validateEmail);
				}
			} catch(e){}

    // Colapsado de secciones por subtítulo h4.section-toggle con persistencia en localStorage y default móvil
    var $toggles = $("h4.section-toggle");
    var storagePrefix = 'estudiantes-agregar:section:';
    function getSectionKey($h){
    	var text = $.trim($h.clone().children().remove().end().text());
    	return storagePrefix + text.toLowerCase();
    }
    function getSectionElements($h){
    	var $content = $h.parent().parent(); // .col-sm-12 -> .row
    	var $siblings = $content.nextAll();
    	var toToggle = [];
    	$siblings.each(function(){
    		var $el = $(this);
    		if ($el.find('h4.section-toggle').length) { return false; }
    		toToggle.push(this);
    	});
    	return $(toToggle);
    }
    var isMobile = window.matchMedia && window.matchMedia('(max-width: 575.98px)').matches;
    $toggles.each(function(){
    	var $h = $(this);
    	var key = getSectionKey($h);
    	var $ind = $h.find('.toggle-indicator');
    	var $targets = getSectionElements($h);
    	// Estado inicial: usa preferencia guardada; si no existe y es móvil, colapsar por defecto
    	var stored = localStorage.getItem(key);
    	var collapsed = stored === '1' || (stored === null && isMobile);
    	if (collapsed) {
    		$targets.hide();
    		$ind.text('▲');
    	}
    	$h.on('click', function(){
    		$targets = getSectionElements($h); // recalcular por si el DOM cambió
    		var willCollapse = $targets.is(':visible');
    		$targets.slideToggle(300); // Animación suave de 300ms
    		$ind.text(willCollapse ? '▲' : '▼');
    		localStorage.setItem(key, willCollapse ? '1' : '0');
    	});
    });
    })();
    </script>
    <!-- end js include path -->
</body>
</html>