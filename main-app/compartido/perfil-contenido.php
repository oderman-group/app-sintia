						<?php
                        $usuarioPerfil = UsuariosPadre::sesionUsuario($_SESSION["id"]);
                        ?>
                        <div class="col-sm-3">
							<div class="panel">
								<header class="panel-heading panel-heading-blue"><?=$frases[219][$datosUsuarioActual['uss_idioma']];?></header>
								<div class="panel-body">
									<div class="item">
	                                    <img src="../files/fotos/<?=$usuarioPerfil['uss_foto'];?>" />
	                                </div>
									<h4>Especificaciones</h4>
									<p><b>1.</b> La foto deber ser cuadrada en cualquier tamaño <mark>(Ejemplo: 600 x 600)</mark> y en cualquier formato (PNG, JPG, JPEG); de lo contrario (o sea, si la foto no es cuadrada) la foto debe estar en formato: <mark>(JPG ó  JPEG)</mark> para poder recortarla.</p>
									<p><b>2.</b> Buena resolución.</p>
									<p><b>3.</b> El peso debe ser menor a 1 MB.</p>
								</div>
							</div>

							<div class="panel">
								<header class="panel-heading panel-heading-blue">Firma digital</header>
								<div class="panel-body">
									<div class="item">
	                                    <img src="../files/fotos/<?=$usuarioPerfil['uss_firma'];?>" />
	                                </div>
								</div>
							</div>
						</div>	

						<div class="col-sm-9">
							<div style="background-color: yellow; color:black; padding: 10px;">
								<h4 style="font-weight: bold;">NOTA IMPORTANTE</h4>
								Le solicitamos, por favor, llenar los datos faltantes de este formulario y corregir los que están erroneos. Esto es muy importante para usted, para sus acudidos y para la Institución.<br>
								Muchas gracias!
							</div>
                            <div class="card card-box">
                                <div class="card-head">
                                    <header><?=$frases[10][$datosUsuarioActual['uss_idioma']];?></header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form name="formularioGuardar" action="../compartido/perfil-actualizar.php" method="post" enctype="multipart/form-data">
										<input type="hidden" name="tipoUsuario" value="<?=$usuarioPerfil['uss_tipo'];?>">
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$usuarioPerfil["uss_id"];?>" name="usuarioID" class="form-control" disabled>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[186][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$usuarioPerfil["uss_usuario"];?>" name="usuario" class="form-control" disabled>
                                            </div>
                                        </div>

                                        <?php if(array_key_exists(Modulos::MODULO_SUB_ROLES, $arregloModulos) && Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_SUB_ROLES) && $usuarioPerfil['uss_tipo'] == TIPO_DIRECTIVO) {?>

                                            <div class="form-group row"  >
                                                <label class="col-sm-2 control-label">Mis Sub Roles</label>
                                                <div class="col-sm-4" >
                                                    <?php
                                                    $parametrosBuscar = array(
                                                        "institucion" =>$config['conf_id_institucion']
                                                    );	
                                                    $listaRoles=SubRoles::listar($parametrosBuscar);
                                                    $listaRolesUsuarios=SubRoles::listarRolesUsuarios($usuarioPerfil['uss_id']);
                                                    ?>
                                                    <select   class="form-control select2-multiple" style="width: 100% !important" name="subroles[]" multiple disabled>
                                                        <option value="">Seleccione una opción</option>
                                                        <?php
                                                        while ($subRol = mysqli_fetch_array($listaRoles, MYSQLI_BOTH)) {
                                                            $selected = '';
                                                            if (!empty($listaRolesUsuarios)) {
                                                                $selecionado = array_key_exists($subRol["subr_id"], $listaRolesUsuarios);
                                                                if ($selecionado) {
                                                                    $selected = 'selected';
                                                                }
                                                            }
                                                            
                                                            echo '<option value="' . $subRol["subr_id"] . '" ' . $selected . '>' . $subRol["subr_nombre"] . '.' . strtoupper($dato['gra_nombre']) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[219][$datosUsuarioActual['uss_idioma']];?> <mark>(Cuadrada)</mark></label>
                                            <div class="col-sm-4">
                                                <input type="file" name="fotoPerfil" onChange="validarPesoArchivo(this)" accept=".png, .jpg, .jpeg" class="form-control">
                                            </div>
                                        </div>

										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Firma digital</label>
                                            <div class="col-sm-4">
                                                <input type="file" name="firmaDigital" onChange="validarPesoArchivo(this)" accept=".png, .jpg, .jpeg" class="form-control">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[187][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$usuarioPerfil["uss_nombre"];?>" name="nombre" class="form-control" <?php if($usuarioPerfil['uss_tipo']==4) echo "readonly"; else echo "required";?> style="text-transform: uppercase;">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Otro Nombre</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$usuarioPerfil["uss_nombre2"];?>" name="nombre2" class="form-control" style="text-transform: uppercase;">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Primer Apellido <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$usuarioPerfil["uss_apellido1"];?>" name="apellido1" class="form-control" <?php if($usuarioPerfil['uss_tipo']==4) echo "readonly"; else echo "required";?> style="text-transform: uppercase;">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Segundo Apellido</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$usuarioPerfil["uss_apellido2"];?>" name="apellido2" class="form-control" style="text-transform: uppercase;">
                                            </div>
                                        </div>

                                        <?php if ($usuarioPerfil['uss_tipo'] == TIPO_DIRECTIVO) {?>
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Documento</label>
                                                <div class="col-sm-4">
                                                    <input 
                                                        type="text" 
                                                        name="documento" 
                                                        id="documento" 
                                                        class="form-control" 
                                                        value="<?=$usuarioPerfil['uss_documento'];?>"
                                                    >
                                                </div>
                                            </div>
                                        <?php }?>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[181][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <input type="email" value="<?=$usuarioPerfil["uss_email"];?>" name="email" class="form-control" style="text-transform: lowercase;">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[188][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$usuarioPerfil["uss_celular"];?>" name="celular" data-mask="(999) 999-9999" class="form-control">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[182][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$usuarioPerfil["uss_telefono"];?>" name="telefono" class="form-control">
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[138][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="genero" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
													while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_genero"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[189][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
                                                <input class="form-control" size="16" type="text" value="<?=$usuarioPerfil["uss_fecha_nacimiento"];?>">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                            	</div>
                                            </div>
											<input type="hidden" id="dtp_input1" value="<?=$usuarioPerfil["uss_fecha_nacimiento"];?>" name="fechaN" required>
                                        </div>
										
										<div class="form-group row">
                                                <label class="col-sm-2 control-label">Mostrar edad</label>
												<div class="input-group spinner col-sm-10">
											<label class="switchToggle">
                                                <input type="checkbox" name="mostrarEdad" value="1" <?php if($usuarioPerfil["uss_mostrar_edad"]==1){echo "checked";}?>>
                                                <span class="slider aqua round"></span>
                                            </label>
												</div>
                                           </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[190][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="lNacimiento" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
													INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
													");
													while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['ciu_id'];?>" <?php if($opg['ciu_id']==$usuarioPerfil["uss_lugar_nacimiento"]){echo "selected";}?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<?php if($usuarioPerfil["uss_tipo"]!=4){?>
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[191][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="nAcademico" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=7");
													while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_nivel_academico"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Área de desempeño <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="profesion" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_profesiones_categorias
													");
													while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['catp_id'];?>" <?php if($opg['catp_id']==$usuarioPerfil["uss_profesion"]){echo "selected";}?>><?=$opg['catp_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<script type="application/javascript">
											function empresario(datos){
												var eLaboral = datos.value;
                                                var empresarioDiv = document.getElementById("empresario");
                                                var camposEmpresario = empresarioDiv.querySelectorAll("select");

												if(eLaboral == 165){
													empresarioDiv.style.visibility="visible";
                                                    // Hacer que los campos sean requeridos
                                                    camposEmpresario.forEach(function(campo) {
                                                        campo.required = true;
                                                    });
												}else{
													empresarioDiv.style.visibility="hidden";

                                                    // Hacer que los campos sean requeridos
                                                    camposEmpresario.forEach(function(campo) {
                                                        campo.required = false;
                                                    });
                                                }
											}
										</script>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Estado laboral <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="eLaboral" required onChange="empresario(this)">
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=9");
													while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_estado_laboral"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										
										<div id="empresario" style="visibility:visible; border: thin; border-style: solid; border-color: blueviolet; padding: 5px; margin: 10px;">
											<p style="color: tomato;">En caso de que sea dueño de negocio, llene esta información.</p>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Tipo de negocio <span style="color: red;">(*)</span></label>
												<div class="col-sm-10">
													<select class="form-control  select2" name="tipoNegocio">
														<option value="">Seleccione una opción</option>
														<?php
														$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=10");
														while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
														?>
															<option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_tipo_negocio"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Sitio web</label>
												<div class="col-sm-10">
													<input type="text" value="<?=$usuarioPerfil["uss_sitio_web_negocio"];?>" name="web" class="form-control" placeholder="https://www.tunegocio.com">
												</div>
											</div>
											
										</div>
										
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Religión <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="religion" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=2");
													while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_religion"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Estado civil <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="eCivil" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=8");
													while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_estado_civil"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Direccion <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" value="<?=$usuarioPerfil["uss_direccion"];?>" name="direccion" class="form-control" required>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Estrato <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="estrato" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=3");
													while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_estrato"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Tipo de vivienda <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="tipoVivienda" required>
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=12");
													while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_tipo_vivienda"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Medio de transporte usual</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="medioTransporte">
                                                    <option value="">Seleccione una opción</option>
													<?php
													$opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=13");
													while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
													?>
														<option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_medio_transporte"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>
										
										
										
										
										
										<?php
										$numHijos = $usuarioPerfil["uss_numero_hijos"];
										if($usuarioPerfil["uss_numero_hijos"]=="") $numHijos = '0';
										?>
										
                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label"><?=$frases[192][$datosUsuarioActual['uss_idioma']];?></label>
                                                    <div class="input-group spinner col-sm-4">
                                                        <span class="input-group-btn">
														<button class="btn btn-info" data-dir="dwn" type="button">
															<span class="fa fa-minus"></span>
                                                        </button>
                                                        </span>
                                                        <input type="text" class="form-control text-center" value="<?=$numHijos;?>" name="numeroHijos"> 
														<span class="input-group-btn">
														<button class="btn btn-danger" data-dir="up" type="button">
															<span class="fa fa-plus"></span>
                                                        </button>
                                                        </span>
                                                    </div>
                                                </div>
										<?php }?>
											
											<div class="form-group row">
                                                <label class="col-sm-2 control-label">Recibir <?=$frases[218][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="input-group spinner col-sm-10">
											<label class="switchToggle">
                                                <input type="checkbox" name="notificaciones" value="1" <?php if($usuarioPerfil["uss_notificacion"]==1){echo "checked";}?>>
                                                <span class="slider red round"></span>
                                            </label>
												</div>
                                           </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Última actualización</label>
                                            <div class="col-sm-4">
                                                <input type="text" value="<?=$usuarioPerfil["uss_ultima_actualizacion"];?>" class="form-control" disabled>
                                            </div>
                                        </div>

										<a href="#" name="index.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
										<button type="submit" class="btn  btn-info">
                                            <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>