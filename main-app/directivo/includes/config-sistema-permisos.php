
<div class="row">
    <div class="col-sm-12">
        <?php
        include("../../config-general/mensajes-informativos.php");
        if($idPaginaInterna == 'DV0032'){
            include("includes/barra-superior-dev-instituciones-configuracion-informacion.php");
        }
        ?>
        
        <div class="panel-modern">
            <header class="panel-heading">
                <i class="fa fa-shield"></i>
                Control de Permisos
            </header>
            <div class="panel-body">
                
                <div class="alert-modern alert-info">
                    <i class="fa fa-lock"></i>
                    <div>
                        <strong>Información:</strong> Configura los permisos y accesos de los diferentes tipos de usuarios en la plataforma.
                    </div>
                </div>
                
                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
                    <input type="hidden" name="configDEV" value="<?= $configDEV; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id']; ?>">
                    <input type="hidden" name="configTab" value="<?=BDT_Configuracion::CONFIG_SISTEMA_PERMISOS;?>">

                    <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DEV){ ?>
                    <!-- Sección: Directivos -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-user-md"></i>
                        Permisos para Directivos
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-user"></i>
                                Cambiar usuario de acceso 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="cambiarNombreUsuario" style="max-width: 200px;">
                                    <option value="SI" <?php if ($datosConfiguracion['conf_cambiar_nombre_usuario'] == 'SI') { echo "selected"; } ?>>✓ SÍ, permitir</option>
                                    <option value="NO" <?php if ($datosConfiguracion['conf_cambiar_nombre_usuario'] == 'NO') { echo "selected"; } ?>>✗ NO, bloquear</option>
                                </select>
                                <small class="form-text text-muted">Permite que los directivos cambien su nombre de usuario</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-history"></i>
                                Editar años anteriores 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta acción permite a los directivos editar registros en años anteriores al actual.">
                                    <i class="fa fa-info"></i>
                                </button>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="editarInfoYears" style="max-width: 200px;">
                                    <option value="1" <?php if($datosConfiguracion['conf_permiso_edicion_years_anteriores']==1){ echo "selected";} ?>>✓ SÍ, permitir edición</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_permiso_edicion_years_anteriores']==0){ echo "selected";} ?>>✗ NO, solo consulta</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-search"></i>
                                Doble buscador 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta acción permite que las instituciones tengan doble buscador en páginas donde se listan los registros.">
                                    <i class="fa fa-info"></i>
                                </button>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="dobleBuscador" style="max-width: 200px;">
                                    <option value="SI" <?php if ($datosConfiguracion['conf_doble_buscador'] == 'SI') { echo "selected"; } ?>>✓ SÍ, activar</option>
                                    <option value="NO" <?php if ($datosConfiguracion['conf_doble_buscador'] == 'NO') { echo "selected"; } ?>>✗ NO, un solo buscador</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-calculator"></i>
                                Actualizar consolidado final 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta acción permite o no actualizar las definitivas, en el consolidado final, en cualquier momento.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="permisoConsolidado" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_editar_definitivas_consolidado']==1){ echo "selected";} ?>>✓ SÍ, permitir</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_editar_definitivas_consolidado']==0){ echo "selected";} ?>>✗ NO, bloquear</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-trash"></i>
                                Eliminar cargas académicas 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="permisoEliminarCargas" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="SI" <?php if($datosConfiguracion['conf_permiso_eliminar_cargas'] == 'SI'){ echo "selected";} ?>>✓ SÍ, permitir eliminación</option>
                                    <option value="NO" <?php if($datosConfiguracion['conf_permiso_eliminar_cargas'] == 'NO'){ echo "selected";} ?>>✗ NO, solo archivar</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php }else{ ?>
                        <input type="hidden" name="cambiarNombreUsuario" value="<?= $datosConfiguracion['conf_cambiar_nombre_usuario']; ?>">
                        <input type="hidden" name="editarInfoYears" value="<?= $datosConfiguracion['conf_permiso_edicion_years_anteriores']; ?>">
                        <input type="hidden" name="dobleBuscador" value="<?= $datosConfiguracion['conf_doble_buscador']; ?>">
                        <input type="hidden" name="permisoEliminarCargas" value="<?= $datosConfiguracion['conf_permiso_eliminar_cargas']; ?>">
                    <?php } ?>

                    <!-- Sección: Docentes -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-graduation-cap"></i>
                        Permisos para Docentes
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-list-ol"></i>
                                Ver puestos en sábanas 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta acción permite o no a los docentes ver el listado de los puestos de los estudiantes, por periodo, en el informe de sábanas.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="permisoDocentesPuestosSabanas" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_ver_promedios_sabanas_docentes']==1){ echo "selected";} ?>>✓ SÍ, mostrar puestos</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_ver_promedios_sabanas_docentes']==0){ echo "selected";} ?>>✗ NO, ocultar puestos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección: Acudientes -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-users"></i>
                        Permisos para Acudientes
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-eye"></i>
                                Mostrar calificaciones 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="caliAcudientes" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_calificaciones_acudientes']==1){ echo "selected";} ?>>✓ SÍ, ver calificaciones</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_calificaciones_acudientes']==0){ echo "selected";} ?>>✗ NO, ocultar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-file-text"></i>
                                Descargar informe parcial 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="informeParcial" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_informe_parcial']==1){ echo "selected";} ?>>✓ SÍ, permitir descarga</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_informe_parcial']==0){ echo "selected";} ?>>✗ NO, bloquear</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-download"></i>
                                Descargar boletín 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta acción permite a los acudientes descargar el boletín de sus acudidos.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="descargarBoletin" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_permiso_descargar_boletin']==1){ echo "selected";} ?>>✓ SÍ, permitir descarga</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_permiso_descargar_boletin']==0){ echo "selected";} ?>>✗ NO, bloquear</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-check-square-o"></i>
                                Encuesta reserva de cupos 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta acción permite a los acudientes responder si desean reservar o no el cupo para sus acudidos para el siguiente año.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="activarEncuestaReservaCupo" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_activar_encuesta']==1){ echo "selected";} ?>>✓ SÍ, activar encuesta</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_activar_encuesta']==0){ echo "selected";} ?>>✗ NO, desactivar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Estudiantes -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-user"></i>
                        Permisos para Estudiantes
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-bar-chart"></i>
                                Mostrar calificaciones 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="caliEstudiantes" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_mostrar_calificaciones_estudiantes']==1){ echo "selected";} ?>>✓ SÍ, ver calificaciones</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_mostrar_calificaciones_estudiantes']==0){ echo "selected";} ?>>✗ NO, ocultar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-key"></i>
                                Cambiar su contraseña 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="cambiarClaveEstudiantes" style="max-width: 200px;">
                                    <option value="SI" <?php if ($datosConfiguracion['conf_cambiar_clave_estudiantes'] == 'SI') { echo "selected"; } ?>>✓ SÍ, permitir cambio</option>
                                    <option value="NO" <?php if ($datosConfiguracion['conf_cambiar_clave_estudiantes'] == 'NO') { echo "selected"; } ?>>✗ NO, bloquear</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-list-ul"></i>
                                Paso a paso de matrícula 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta acción permite ver a los estudiantes el paso a paso del proceso de matrícula.">
                                    <i class="fa fa-info"></i>
                                </button>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="pasosMatricula" style="max-width: 200px;">
                                    <option value="1" <?php if ($datosConfiguracion['conf_mostrar_pasos_matricula'] == 1) { echo "selected"; } ?>>✓ SÍ, mostrar pasos</option>
                                    <option value="0" <?php if ($datosConfiguracion['conf_mostrar_pasos_matricula'] == 0) { echo "selected"; } ?>>✗ NO, ocultar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="save-button-container">
                        <?php $botones = new botonesGuardar("dev-instituciones.php",Modulos::validarPermisoEdicion() || $datosUsuarioActual['uss_tipo'] == TIPO_DEV); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
