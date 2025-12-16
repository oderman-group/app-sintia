<div class="panel">
    <header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
    <div class="panel-body">
        <form name="formularioGuardar" action="usuarios-update.php" method="post" enctype="multipart/form-data">

            <input type="hidden" value="<?=$datosEditar['uss_id'];?>" name="idR">
            <?php echo Csrf::campoHTML(); ?>
            <?php 
            $rutaFoto = "../files/fotos/{$datosEditar['uss_foto']}";
            if(Utilidades::ArchivoExiste($rutaFoto)) {?>
            <div class="form-group row">
                <div class="col-sm-4">
                    <div class="item">
                        <img src="<?=$rutaFoto;?>" width="100"/>
                    </div>
                </div>
            </div>
            <?php }?>
            
            <div class="form-group row">
                <label class="col-sm-2 control-label"><?=$frases[219][$datosUsuarioActual['uss_idioma']];?></label>
                <div class="col-sm-4">
                    <input type="file" name="fotoUss" onChange="validarPesoArchivo(this)" class="form-control" accept=".png, .jpg, .jpeg" <?=$disabledPermiso;?>>
                    <span style="color: #6017dc;">La foto debe estar en formato JPG, JPEG o PNG.</span>
                </div>
            </div>
            <hr>

            <div class="form-group row">
                <label class="col-sm-2 control-label">ID</label>
                <div class="col-sm-2">
                    <input type="text" name="idRegistro" class="form-control" value="<?=$datosEditar['uss_id'];?>" readonly <?=$disabledPermiso;?>>
                </div>
            </div>

            <?php
            $readonlyUsuario = 'readonly';
            if($config['conf_cambiar_nombre_usuario'] == 'SI') {
                $readonlyUsuario = '';
            }
            ?>
            <div class="form-group row">
                <label class="col-sm-2 control-label">Usuario</label>
                <div class="col-sm-4">
                    <input type="text" name="usuario" id="usuario" class="form-control" data-id-usuario="<?=$datosEditar['id_nuevo'];?>" oninput="validarUsuario(this)" value="<?=$datosEditar['uss_usuario'];?>" <?=$readonlyUsuario;?> <?=$disabledPermiso;?>>
                    <div id="alerta_usuario_existente_editar" class="alert alert-danger mt-2" style="display: none;">
                        <i class="fa fa-exclamation-triangle"></i> 
                        <strong>Usuario duplicado:</strong> Este usuario de acceso ya está registrado para otro usuario. 
                        Por favor, elige un nombre de usuario diferente.
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Tipo de usuario</label>
                <div class="col-sm-3">
                    <?php
                    try{
                        $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_perfiles WHERE pes_disponible = 1");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    ?>
                    <select id="tipoUsuario" class="form-control  select2" onchange="validarCantidadUsuarios(this)" required readonly disabled style="background-color: #e9ecef; cursor: not-allowed;">
                        <option value="">Seleccione una opción</option>
                        <?php
                        while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                            if(
                            ($opcionesDatos[0] == 1 || $opcionesDatos[0] == 6) 
                            and $datosUsuarioActual['uss_tipo']==5){continue;}
                            $select = '';
                            if($opcionesDatos[0]==$datosEditar['uss_tipo']) $select = 'selected';
                        ?>
                            <option value="<?=$opcionesDatos[0];?>" <?=$select;?> ><?=$opcionesDatos['pes_nombre'];?></option>
                        <?php }?>
                    </select>
                    <input type="hidden" name="tipoUsuario" value="<?=$datosEditar['uss_tipo'];?>">
                </div>
            </div>
            <?php if ( array_key_exists(16, $arregloModulos) ) { ?>
            <div id="subRoles" >
                <div class="form-group row"  >
                                <label class="col-sm-2 control-label" >Sub Roles</label>
                                <div class="col-sm-4" >
                                    <?php
                                    $parametrosBuscar = array(
                                        "institucion" =>$config['conf_id_institucion']
                                    );	
                                    $listaRoles=SubRoles::listar($parametrosBuscar);
                                    $listaRolesUsuarios=SubRoles::listarRolesUsuarios($datosEditar['uss_id']);
                                    ?>
                                    <select   class="form-control select2-multiple" style="width: 100% !important" name="subroles[]" multiple>
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
            </div>

            <script>
            $(document).ready(mostrarSubroles(document.getElementById("tipoUsuario")));
            </script>

            <?php }?>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Contraseña</label>
                <div class="col-sm-4">
                    <input type="password" name="clave" id="clave" class="form-control bg-light" disabled readonly placeholder="Active el switch para cambiar la contraseña" <?=$disabledPermiso;?>>
                </div>
                <?php if(Modulos::validarPermisoEdicion()){?>
                    <div class="col-sm-2">
                        <div class="input-group spinner col-sm-10">
                            <label class="switchToggle">
                                <input type="checkbox" name="cambiarClave" id="cambiarClave" value="1" onchange="habilitarClave()">
                                <span class="slider red round"></span>
                            </label>
                            <label class="col-sm-2 control-label">Cambiar Contraseña</label>
                        </div>
                    </div>
                <?php }?>
            </div>
            <hr>
            
            <?php
            $readOnly = '';
            $leyenda = '';
            if($datosEditar['uss_tipo'] == TIPO_ESTUDIANTE && Modulos::validarSubRol(['DT0078'])){
                $readOnly='readonly'; 
                $leyenda = 'El nombre de los estudiantes solo es editable desde la matrícula. <a href="estudiantes-editar.php?idUsuario='.base64_encode($datosEditar['uss_id']).'" style="text-decoration:underline;">IR A LA MATRÍCULA</a>';
            } elseif($datosEditar['uss_tipo'] == TIPO_ESTUDIANTE) {
                $readOnly='readonly'; 
                $leyenda = 'El nombre de los estudiantes solo es editable desde la matrícula.';
            }
            ?>
            
            <div class="form-group row">
                <label class="col-sm-2 control-label">Nombre</label>
                <div class="col-sm-4">
                    <input type="text" name="nombre" class="form-control" value="<?=$datosEditar['uss_nombre'];?>" <?=$readOnly;?> <?=$disabledPermiso;?>>
                <span style="color: tomato;"><?=$leyenda;?></span>
                </div>
                
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Otro Nombre</label>
                <div class="col-sm-4">
                    <input type="text" name="nombre2" class="form-control" value="<?=$datosEditar['uss_nombre2'];?>" <?=$readOnly;?> <?=$disabledPermiso;?>>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Primer Apellido</label>
                <div class="col-sm-4">
                    <input type="text" name="apellido1" class="form-control" value="<?=$datosEditar['uss_apellido1'];?>" <?=$readOnly;?> <?=$disabledPermiso;?>>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Segundo Apellido</label>
                <div class="col-sm-4">
                    <input type="text" name="apellido2" class="form-control" value="<?=$datosEditar['uss_apellido2'];?>" <?=$readOnly;?> <?=$disabledPermiso;?>>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Tipo de documento</label>
                <div class="col-sm-4">
                    <?php
                    try{
                        $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales
                        WHERE ogen_grupo=1");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    ?>
                    <select class="form-control  select2" name="tipoD" <?=$disabledPermiso;?>>
                        <option value="">Seleccione una opción</option>
                        <?php while($o = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                            if($o[0]==$datosEditar['uss_tipo_documento'])
                            echo '<option value="'.$o[0].'" selected>'.$o[1].'</option>';
                        else
                            echo '<option value="'.$o[0].'">'.$o[1].'</option>';	
                        }?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Documento</label>
                <div class="col-sm-4">
                    <input type="text" name="documento" id="documento" class="form-control" data-id-usuario="<?=$datosEditar['id_nuevo'];?>" oninput="validarDocumento(this)" value="<?=$datosEditar['uss_documento'];?>" <?=$readOnly;?> <?=$disabledPermiso;?>>
                    <div id="alerta_documento_existente_editar" class="alert alert-danger mt-2" style="display: none;">
                        <i class="fa fa-exclamation-triangle"></i> 
                        <strong>Documento duplicado:</strong> Este documento ya está registrado para otro usuario. 
                        Por favor, verifica el número de documento o contacta al administrador.
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 control-label">Email</label>
                <div class="col-sm-4">
                    <input type="email" name="email" class="form-control" value="<?=$datosEditar['uss_email'];?>" <?=$disabledPermiso;?>>
                </div>
            </div>
            
            <?php
            // Limpiar formato de celular y teléfono (solo números)
            $celularLimpio = !empty($datosEditar['uss_celular']) ? preg_replace('/[^0-9]/', '', $datosEditar['uss_celular']) : '';
            $telefonoLimpio = !empty($datosEditar['uss_telefono']) ? preg_replace('/[^0-9]/', '', $datosEditar['uss_telefono']) : '';
            ?>
            <div class="form-group row">
                <label class="col-sm-2 control-label">Celular</label>
                <div class="col-sm-4">
                    <input type="text" name="celular" id="celular" class="form-control" 
                           pattern="[0-9]{10}" 
                           maxlength="10"
                           oninput="validarNumeroCelular(this)"
                           placeholder="Ej: 3001234567"
                           value="<?=$celularLimpio;?>" <?=$disabledPermiso;?>>
                    <small class="form-text text-muted">
                        <i class="fa fa-info-circle"></i> Ingrese solo números (10 dígitos)
                    </small>
                    <small id="validacion_celular" class="form-text"></small>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Teléfono</label>
                <div class="col-sm-4">
                    <input type="text" name="telefono" id="telefono" class="form-control" 
                           pattern="[0-9]{7}" 
                           maxlength="7"
                           oninput="validarNumeroTelefono(this)"
                           placeholder="Ej: 1234567"
                           value="<?=$telefonoLimpio;?>" <?=$disabledPermiso;?>>
                    <small class="form-text text-muted">
                        <i class="fa fa-info-circle"></i> Ingrese solo números (7 dígitos)
                    </small>
                    <small id="validacion_telefono" class="form-text"></small>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Dirección</label>
                <div class="col-sm-4">
                    <input type="text" name="direccion" class="form-control" value="<?=$datosEditar['uss_direccion'];?>" <?=$disabledPermiso;?>>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Ocupacion</label>
                <div class="col-sm-4">
                    <input type="text" name="ocupacion" class="form-control" value="<?=$datosEditar['uss_ocupacion'];?>" <?=$disabledPermiso;?>>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Lugar de expedición del documento</label>
                <div class="col-sm-4">
                    <select class="form-control select2" name="lExpedicion" <?=$disabledPermiso;?>>
                        <option value="">Seleccione una ciudad</option>
                        <?php
                        $ciudadesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
                        INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
                        ORDER BY ciu_nombre ASC
                        ");
                        while($ciudad = mysqli_fetch_array($ciudadesConsulta, MYSQLI_BOTH)){
                        ?>
                        <option value="<?=$ciudad['ciu_id'];?>" <?php if($ciudad['ciu_id']==$datosEditar['uss_lugar_expedicion']){echo "selected";}?>><?=$ciudad['ciu_nombre'].", ".$ciudad['dep_nombre'];?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            
            
            
            <div class="form-group row">
                <label class="col-sm-2 control-label">Género</label>
                <div class="col-sm-3">
                    <?php
                    try{
                        $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    ?>
                    <select class="form-control  select2" name="genero" required <?=$disabledPermiso;?>>
                        <option value="">Seleccione una opción</option>
                        <?php
                        while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                            $select = '';
                            if($opcionesDatos[0]==$datosEditar['uss_genero']) $select = 'selected';
                        ?>
                            <option value="<?=$opcionesDatos[0];?>" <?=$select;?> ><?=$opcionesDatos['ogen_nombre'];?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            
            
            
            <hr>
            <div class="form-group row">
                <label class="col-sm-2 control-label">Intentos de acceso fallidos</label>
                <div class="col-sm-1">
                    <input type="number" name="intentosFallidos" class="form-control" value="<?=$datosEditar['uss_intentos_fallidos'];?>" <?=$disabledPermiso;?> min="0" step="1">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Usuario bloqueado</label>
                <div class="col-sm-4">
                    <?php if(Modulos::validarPermisoEdicion()){?>
                        <div class="input-group spinner col-sm-10">
                            <label class="switchToggle">
                                <input type="checkbox" name="desbloquearUsuario" id="desbloquearUsuario" value="1" 
                                    <?=($datosEditar['uss_bloqueado'] == 1) ? 'checked' : '';?> 
                                    <?=($datosEditar['uss_bloqueado'] == 0) ? 'disabled' : '';?>
                                    onchange="manejarDesbloqueo(this)">
                                <span class="slider <?=($datosEditar['uss_bloqueado'] == 1) ? 'red' : 'green';?> round"></span>
                            </label>
                            <label class="col-sm-2 control-label">
                                <?=($datosEditar['uss_bloqueado'] == 1) ? 'Desbloquear Usuario' : 'Usuario Desbloqueado';?>
                            </label>
                        </div>
                        <input type="hidden" name="bloqueado" id="bloqueado" value="<?=$datosEditar['uss_bloqueado'];?>">
                        <small class="form-text text-muted">
                            <?php if($datosEditar['uss_bloqueado'] == 1): ?>
                                <i class="fa fa-info-circle"></i> El usuario está bloqueado. Desactiva el switch para desbloquearlo.
                            <?php else: ?>
                                <i class="fa fa-info-circle"></i> El usuario está desbloqueado. No se puede bloquear desde aquí.
                            <?php endif; ?>
                        </small>
                    <?php } else { ?>
                        <input type="text" class="form-control" value="<?=($datosEditar['uss_bloqueado'] == 1) ? 'Bloqueado' : 'Desbloqueado';?>" readonly>
                    <?php } ?>
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
            
            <?php
            // Verificar si se puede hacer autologin (mismas condiciones que en usuarios.php)
            $puedeAutologin = false;
            $tieneMatricula = false;
            
            // Verificar si el usuario es estudiante y tiene matrícula
            if ($datosEditar['uss_tipo'] == TIPO_ESTUDIANTE) {
                try {
                    $consultaMatricula = mysqli_query($conexion, 
                        "SELECT COUNT(*) as cantidad 
                         FROM ".BD_ACADEMICA.".academico_matriculas 
                         WHERE mat_id_usuario = '".mysqli_real_escape_string($conexion, $datosEditar['uss_id'])."' 
                         AND institucion = {$config['conf_id_institucion']} 
                         AND year = {$_SESSION["bd"]} 
                         AND mat_eliminado = 0 
                         AND (mat_estado_matricula = 1 OR mat_estado_matricula = 2)");
                    if ($consultaMatricula) {
                        $datosMatricula = mysqli_fetch_array($consultaMatricula, MYSQLI_BOTH);
                        $tieneMatricula = !empty($datosMatricula['cantidad']) && $datosMatricula['cantidad'] > 0;
                    }
                } catch (Exception $e) {
                    $tieneMatricula = false;
                }
            }
            
            // Condiciones para autologin (igual que en usuarios.php)
            if (
                ($datosUsuarioActual['uss_tipo'] == TIPO_DEV && $datosEditar['uss_tipo'] != TIPO_DEV) ||
                ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && $datosEditar['uss_tipo'] != TIPO_DEV && $datosEditar['uss_tipo'] != TIPO_DIRECTIVO && !isset($_SESSION['admin']) && !isset($_SESSION['devAdmin']))
            ) {
                if (($datosEditar['uss_tipo'] == TIPO_ESTUDIANTE && $tieneMatricula) || $datosEditar['uss_tipo'] != TIPO_ESTUDIANTE) {
                    $puedeAutologin = true;
                }
            }
            ?>
            
            <?php if ($puedeAutologin) { ?>
            <hr>
            <div class="form-group row">
                <label class="col-sm-2 control-label">Acciones</label>
                <div class="col-sm-4">
                    <a href="auto-login.php?user=<?= base64_encode($datosEditar['uss_id']); ?>&tipe=<?= base64_encode($datosEditar['uss_tipo']); ?>" 
                       class="btn btn-primary">
                        <i class="fa fa-sign-in"></i> Autologin
                    </a>
                    <small class="form-text text-muted">
                        <i class="fa fa-info-circle"></i> Iniciar sesión como este usuario en una nueva pestaña.
                    </small>
                </div>
            </div>
            <?php } ?>
            
            <hr>
            <h5 class="mb-3" style="color: #6017dc;"><i class="fa fa-info-circle"></i> Datos Adicionales del Usuario</h5>
            
            <?php
            // Obtener nombre del responsable del registro si existe
            $nombreResponsable = 'No registrado';
            if (!empty($datosEditar['uss_responsable_registro'])) {
                try {
                    $idResponsable = mysqli_real_escape_string($conexion, $datosEditar['uss_responsable_registro']);
                    $idInstitucion = mysqli_real_escape_string($conexion, $config['conf_id_institucion']);
                    $year = mysqli_real_escape_string($conexion, $_SESSION["bd"]);
                    
                    $consultaResponsable = mysqli_query($conexion, 
                        "SELECT uss_nombre, uss_apellido1, uss_apellido2, uss_usuario 
                         FROM ".BD_GENERAL.".usuarios 
                         WHERE uss_id = '".$idResponsable."' 
                         AND institucion = '".$idInstitucion."' 
                         AND year = '".$year."' 
                         LIMIT 1");
                    if ($consultaResponsable && mysqli_num_rows($consultaResponsable) > 0) {
                        $datosResponsable = mysqli_fetch_array($consultaResponsable, MYSQLI_BOTH);
                        $nombreResponsable = trim(($datosResponsable['uss_nombre'] ?? '') . ' ' . 
                                                 ($datosResponsable['uss_apellido1'] ?? '') . ' ' . 
                                                 ($datosResponsable['uss_apellido2'] ?? ''));
                        if (empty(trim($nombreResponsable))) {
                            $nombreResponsable = $datosResponsable['uss_usuario'] ?? 'Usuario ID: ' . $datosEditar['uss_responsable_registro'];
                        } else {
                            $nombreResponsable .= ' (' . ($datosResponsable['uss_usuario'] ?? '') . ')';
                        }
                    }
                } catch (Exception $e) {
                    $nombreResponsable = 'Usuario ID: ' . $datosEditar['uss_responsable_registro'];
                }
            }
            
            // Formatear fecha de registro
            $fechaRegistroFormateada = 'No registrada';
            if (!empty($datosEditar['uss_fecha_registro'])) {
                $fechaRegistroFormateada = date('d/m/Y H:i:s', strtotime($datosEditar['uss_fecha_registro']));
            }
            ?>
            
            <div class="form-group row">
                <label class="col-sm-2 control-label">Fecha de Registro</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" value="<?=$fechaRegistroFormateada;?>" readonly>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 control-label">Responsable del Registro</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" value="<?=$nombreResponsable;?>" readonly>
                </div>
            </div>

            <?php $botones = new botonesGuardar("usuarios.php?cantidad=10",Modulos::validarPermisoEdicion()); ?>
        </form>
    </div>
</div>