<div class="panel">
    <header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
    <div class="panel-body">
        <form name="formularioGuardar" action="usuarios-update.php" method="post" enctype="multipart/form-data">

            <input type="hidden" value="<?=$datosEditar['uss_id'];?>" name="idR">
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
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Tipo de usuario</label>
                <div class="col-sm-3">
                    <?php
                    try{
                        $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_perfiles");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    ?>
                    <select id="tipoUsuario" class="form-control  select2" name="tipoUsuario" onchange="validarCantidadUsuarios(this)" required <?=$disabledPermiso;?>>
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
                    <input type="password" name="clave" id="clave" class="form-control" disabled <?=$disabledPermiso;?>>
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
                    <input type="text" name="nombre" class="form-control" value="<?=$datosEditar['uss_nombre'];?>" <?=$readOnly;?> pattern="^[A-Za-zñÑ]+$" <?=$disabledPermiso;?>>
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
                    <input type="text" name="apellido1" class="form-control" value="<?=$datosEditar['uss_apellido1'];?>" <?=$readOnly;?> pattern="^[A-Za-zñÑ]+$" <?=$disabledPermiso;?>>
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
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 control-label">Email</label>
                <div class="col-sm-4">
                    <input type="email" name="email" class="form-control" value="<?=$datosEditar['uss_email'];?>" <?=$disabledPermiso;?>>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 control-label">Celular</label>
                <div class="col-sm-4">
                    <input type="text" name="celular" class="form-control" data-mask="(999) 999-9999" value="<?=$datosEditar['uss_celular'];?>" <?=$disabledPermiso;?>>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Teléfono</label>
                <div class="col-sm-4">
                    <input type="text" name="telefono" class="form-control"  data-mask="999-9999"value="<?=$datosEditar['uss_telefono'];?>" <?=$disabledPermiso;?>>
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
                    <input type="text" name="lExpedicion" class="form-control" value="<?=$datosEditar['uss_lugar_expedicion'];?>" <?=$disabledPermiso;?>>
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
                    <input type="number" name="intentosFallidos" class="form-control" value="<?=$datosEditar['uss_intentos_fallidos'];?>" <?=$disabledPermiso;?>>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Usuario bloqueado</label>
                <div class="col-sm-1">
                    <input type="number" name="bloqueado" class="form-control" value="<?=$datosEditar['uss_bloqueado'];?>" readonly>
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

            <?php $botones = new botonesGuardar("usuarios.php?cantidad=10",Modulos::validarPermisoEdicion()); ?>
        </form>
    </div>
</div>