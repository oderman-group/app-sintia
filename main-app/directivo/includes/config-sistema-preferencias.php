
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
                <i class="fa fa-heart"></i>
                Preferencias del Sistema
            </header>
            <div class="panel-body">
                
                <div class="alert-modern alert-info">
                    <i class="fa fa-info-circle"></i>
                    <div>
                        <strong>Información:</strong> Estas preferencias te permiten personalizar la experiencia de usuario y visualización de datos en la plataforma.
                    </div>
                </div>
                
                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
                    <input type="hidden" name="configDEV" value="<?= $configDEV; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id']; ?>">
                    <input type="hidden" name="configTab" value="<?=BDT_Configuracion::CONFIG_SISTEMA_PREFERENCIAS;?>">

                    <!-- Sección: Visualización de Datos -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-eye"></i>
                        Visualización de Datos
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-user"></i>
                                Orden del nombre de estudiantes 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="ordenEstudiantes" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_orden_nombre_estudiantes']==1){ echo "selected";} ?>>📝 Nombres y Apellidos (Andrés David Arias Pertuz)</option>
                                    <option value="2" <?php if($datosConfiguracion['conf_orden_nombre_estudiantes']==2){ echo "selected";} ?>>📋 Apellidos y Nombres (Arias Pertuz Andrés David)</option>
                                </select>
                                <small class="form-text text-muted">Esta configuración afecta la forma en que se muestran los nombres en listados y reportes</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-list"></i>
                                Registros por página 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta opción permite escoger la cantidad de registros que desea que se listen al entrar, por ejemplo, a matrículas, cargas académicas o usuarios.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="numRegistros" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="20" <?php if($datosConfiguracion['conf_num_registros']==20){ echo "selected";} ?>>20 registros</option>
                                    <option value="30" <?php if($datosConfiguracion['conf_num_registros']==30){ echo "selected";} ?>>30 registros</option>
                                    <option value="50" <?php if($datosConfiguracion['conf_num_registros']==50){ echo "selected";} ?>>50 registros</option>
                                    <option value="100" <?php if($datosConfiguracion['conf_num_registros']==100){ echo "selected";} ?>>100 registros</option>
                                </select>
                                <small class="form-text text-muted">Afecta el rendimiento de carga de páginas con listados</small>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Estudiantes y Reportes -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-graduation-cap"></i>
                        Estudiantes y Reportes
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-user-times"></i>
                                Mostrar estudiantes cancelados 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="mostrarEstudiantesCancelados" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="SI" <?php if($datosConfiguracion['conf_mostrar_estudiantes_cancelados'] == 'SI'){ echo "selected";} ?>>✓ SÍ, mostrar en informes</option>
                                    <option value="NO" <?php if($datosConfiguracion['conf_mostrar_estudiantes_cancelados'] == 'NO'){ echo "selected";} ?>>✗ NO, ocultar de informes</option>
                                </select>
                                <small class="form-text text-muted">Define si los estudiantes con matrícula cancelada aparecen en los informes</small>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Panel de Estudiantes -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-columns"></i>
                        Panel de Estudiantes y Acudientes
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-bar-chart-o"></i>
                                Mostrar notas en panel lateral 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="mostrarNotasPanelLateral" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_ocultar_panel_lateral_notas_estudiantes'] == 1){ echo "selected";} ?>>✓ SÍ, mostrar notas y desempeño</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_ocultar_panel_lateral_notas_estudiantes'] == '0'){ echo "selected";} ?>>✗ NO, ocultar información</option>
                                </select>
                                <small class="form-text text-muted">Controla la visibilidad de las notas en el panel lateral para estudiantes y acudientes</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-users"></i>
                                Solicitar segundo acudiente 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="solicitarAcudiente2" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="SI" <?php if(($datosConfiguracion['conf_solicitar_acudiente_2'] ?? 'NO') == 'SI'){ echo "selected";} ?>>✓ SÍ, solicitar segundo acudiente</option>
                                    <option value="NO" <?php if(($datosConfiguracion['conf_solicitar_acudiente_2'] ?? 'NO') == 'NO'){ echo "selected";} ?>>✗ NO, solo primer acudiente</option>
                                </select>
                                <small class="form-text text-muted">Define si se debe solicitar información de un segundo acudiente en los formularios de matrícula</small>
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
