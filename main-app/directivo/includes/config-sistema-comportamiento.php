
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
                <i class="fa fa-sliders"></i>
                Comportamiento del Sistema
            </header>
            <div class="panel-body">
                
                <?php if($hayRegistroEnCalificaciones): ?>
                <div class="alert-modern alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <div>
                        <strong>Atención:</strong> Algunos campos están bloqueados porque ya existen calificaciones registradas en el sistema. 
                        Modificar estos valores podría afectar los cálculos existentes.
                    </div>
                </div>
                <?php endif; ?>
                
                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
                    <input type="hidden" name="configDEV" value="<?= $configDEV; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id']; ?>">
                    <input type="hidden" name="configTab" value="<?=BDT_Configuracion::CONFIG_SISTEMA_COMPORTAMIENTO;?>">

                    <!-- Sección: Configuración de Periodos -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-calendar-check-o"></i>
                        Configuración de Periodos
                    </h4>
                    
                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-calendar-o"></i>
                                Periodos a trabajar 
                                <span class="required-indicator">*</span> 
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Las instituciones normalmente manejan 4 periodos. Los colegios semestralizados o de bachillerato acelerado manejan 2 periodos.">
                                    <i class="fa fa-info"></i>
                                </button>
                            </label>
                            <div class="col-sm-9">
                                <input 
                                    type="number" 
                                    name="periodoTrabajar" 
                                    class="form-control" 
                                    value="<?=$datosConfiguracion['conf_periodos_maximos'];?>" 
                                    required 
                                    pattern="[0-9]+" 
                                    style="max-width: 150px;"
                                    <?php 
                                    if(!empty($disabledPermiso)) 
                                        echo $disabledPermiso; 
                                    else 
                                        echo $disabledCamposConfiguracion;
                                    ?>
                                >
                                <small class="form-text text-muted">Ejemplo: 4 (para trimestral o bimestral)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Sistema de Calificaciones -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-bar-chart"></i>
                        Sistema de Calificaciones
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-arrows-h"></i>
                                Rango de notas (Desde - Hasta) 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div style="display: flex; gap: 15px; align-items: center;">
                                    <div style="flex: 1; max-width: 150px;">
                                        <label style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Desde</label>
                                        <input 
                                            type="number" 
                                            name="desde" 
                                            class="form-control" 
                                            value="<?=$datosConfiguracion['conf_nota_desde'];?>" 
                                            <?php 
                                            if(!empty($disabledPermiso)) 
                                                echo $disabledPermiso; 
                                            else 
                                                echo $disabledCamposConfiguracion;
                                            ?>
                                        >
                                    </div>
                                    <span style="margin-top: 25px;">→</span>
                                    <div style="flex: 1; max-width: 150px;">
                                        <label style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Hasta</label>
                                        <input 
                                            type="number" 
                                            name="hasta" 
                                            class="form-control" 
                                            value="<?=$datosConfiguracion['conf_nota_hasta'];?>" 
                                            <?php 
                                            if(!empty($disabledPermiso)) 
                                                echo $disabledPermiso; 
                                            else 
                                                echo $disabledCamposConfiguracion;
                                            ?>
                                        >
                                    </div>
                                </div>
                                <small class="form-text text-muted">Ejemplo: Desde 1.0 hasta 5.0</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-check-circle"></i>
                                Nota mínima para aprobar 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input 
                                    type="text" 
                                    name="notaMinima" 
                                    class="form-control" 
                                    value="<?=$datosConfiguracion['conf_nota_minima_aprobar'];?>" 
                                    style="max-width: 150px;"
                                    <?php 
                                    if(!empty($disabledPermiso)) 
                                        echo $disabledPermiso; 
                                    else 
                                        echo $disabledCamposConfiguracion;
                                    ?>
                                >
                                <small class="form-text text-muted">Ejemplo: 3.0</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-calculator"></i>
                                Decimales en las notas 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Indica cuántos decimales aparecerán en los cálculos de las notas.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <div style="display: flex; gap: 20px; align-items: flex-start;">
                                    <div style="flex: 0 0 150px;">
                                        <input 
                                            type="number" 
                                            name="decimalesNotas" 
                                            id="decimalesNotasInput"
                                            class="form-control" 
                                            value="<?=$datosConfiguracion['conf_decimales_notas'];?>"
                                            min="0"
                                            max="4"
                                            onchange="actualizarEjemploDecimales(this.value)"
                                            oninput="actualizarEjemploDecimales(this.value)"
                                        >
                                        <small class="form-text text-muted">Rango: 0 a 4</small>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 15px; border-radius: 8px; border-left: 3px solid #41c4c4;">
                                            <div style="font-size: 12px; color: #64748b; margin-bottom: 8px; font-weight: 600;">
                                                <i class="fa fa-eye"></i> Vista Previa en Informes:
                                            </div>
                                            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                                <div>
                                                    <span style="font-size: 11px; color: #94a3b8;">Boletines:</span>
                                                    <div style="font-size: 18px; font-weight: 700; color: #667eea; font-family: monospace;" id="ejemploBoletin">4.5</div>
                                                </div>
                                                <div>
                                                    <span style="font-size: 11px; color: #94a3b8;">Sábanas:</span>
                                                    <div style="font-size: 18px; font-weight: 700; color: #667eea; font-family: monospace;" id="ejemploSabana">4.5</div>
                                                </div>
                                                <div>
                                                    <span style="font-size: 11px; color: #94a3b8;">Libros:</span>
                                                    <div style="font-size: 18px; font-weight: 700; color: #667eea; font-family: monospace;" id="ejemploLibro">4.5</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <script>
                                function actualizarEjemploDecimales(decimales) {
                                    decimales = parseInt(decimales) || 0;
                                    if (decimales < 0) decimales = 0;
                                    if (decimales > 4) decimales = 4;
                                    
                                    // Actualizar el input si está fuera de rango
                                    document.getElementById('decimalesNotasInput').value = decimales;
                                    
                                    // Generar nota de ejemplo según decimales
                                    const notaBase = 4.456789;
                                    const notaFormateada = notaBase.toFixed(decimales);
                                    
                                    // Actualizar ejemplos
                                    document.getElementById('ejemploBoletin').textContent = notaFormateada;
                                    document.getElementById('ejemploSabana').textContent = notaFormateada;
                                    document.getElementById('ejemploLibro').textContent = notaFormateada;
                                }
                                
                                // Inicializar al cargar
                                document.addEventListener('DOMContentLoaded', function() {
                                    const valorActual = document.getElementById('decimalesNotasInput').value;
                                    actualizarEjemploDecimales(valorActual);
                                });
                                </script>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-percent"></i>
                                Porcentaje en asignaturas 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Indica si las asignaturas tendrán un porcentaje diferente dentro del área al momento de calcular las notas en el boletín.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="porcenAsigna" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="SI" <?php if($datosConfiguracion['conf_agregar_porcentaje_asignaturas']=='SI'){ echo "selected";} ?>>SÍ, asignar porcentajes</option>
                                    <option value="NO" <?php if($datosConfiguracion['conf_agregar_porcentaje_asignaturas']=='NO'){ echo "selected";} ?>>NO, misma ponderación</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-list-alt"></i>
                                Estilo de calificación 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="estiloNotas" required <?=$disabledPermiso;?>>
                                    <option value="">Seleccione una opción</option>
                                    <?php
                                        $opcionesGeneralesConsulta = categoriasNota::traerCategoriasNotasInstitucion($config);
                                        while($opcionesGeneralesDatos = mysqli_fetch_array($opcionesGeneralesConsulta, MYSQLI_BOTH)){
                                            if($datosConfiguracion['conf_notas_categoria']==$opcionesGeneralesDatos['catn_id'])
                                                echo '<option value="'.$opcionesGeneralesDatos['catn_id'].'" selected>'.$opcionesGeneralesDatos['catn_nombre'].'</option>';
                                            else
                                                echo '<option value="'.$opcionesGeneralesDatos['catn_id'].'">'.$opcionesGeneralesDatos['catn_nombre'].'</option>';	
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?php if (array_key_exists(Modulos::MODULO_CUALITATIVO, $arregloModulos) && Modulos::verificarModulosDeInstitucion(Modulos::MODULO_CUALITATIVO)) { ?>
                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-eye"></i>
                                Formato de notas 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta opción mostrará a los usuarios las notas en formato numérico o con frases de desempeño que corresponden a las notas numéricas, dependiendo la opción que seleccione.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="formaNotas" <?=$disabledPermiso;?>>
                                    <option value="<?=CUALITATIVA?>" <?php if($datosConfiguracion['conf_forma_mostrar_notas'] == CUALITATIVA){ echo "selected";} ?>>CUALITATIVA (sin números)</option>
                                    <option value="<?=CUANTITATIVA?>" <?php if($datosConfiguracion['conf_forma_mostrar_notas'] == CUANTITATIVA){ echo "selected";} ?>>CUANTITATIVA (con números)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php }?>

                    <!-- Sección: Informes y Comportamiento -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-file-text-o"></i>
                        Informes y Comportamiento
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-cog"></i>
                                Generación de informes 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta opción define el comportamiento a la hora de generar los informes por parte de los docentes o directivos. Escoja la configuración deseada. Esto aplica para todas las cargas académicas.">
                                    <i class="fa fa-info"></i>
                                </button>
                            </label>
                            <div class="col-sm-9">
                                <select 
                                    class="form-control" 
                                    name="generarInforme" 
                                    <?php 
                                    if(!empty($disabledPermiso)) 
                                        echo $disabledPermiso; 
                                    else 
                                        echo $disabledCamposConfiguracion;
                                    ?>
                                >
                                    <option value="1" <?php if($datosConfiguracion['conf_porcentaje_completo_generar_informe']==1){ echo "selected";} ?>>✓ Requiere 100% de notas registradas</option>
                                    <option value="2" <?php if($datosConfiguracion['conf_porcentaje_completo_generar_informe']==2){ echo "selected";} ?>>⊘ Omitir estudiantes sin 100% de notas</option>
                                    <option value="3" <?php if($datosConfiguracion['conf_porcentaje_completo_generar_informe']==3){ echo "selected";} ?>>≈ Usar porcentaje actual disponible</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-comments-o"></i>
                                Observaciones múltiples 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Permitirá a los docentes colocar o escoger solo una observación para los estudiantes que aparecerá en los boletines, o seleccionar múltiples de ellas.">
                                    <i class="fa fa-info"></i>
                                </button>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="observacionesMultiples" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_observaciones_multiples_comportamiento']==1){ echo "selected";} ?>>✓ SÍ, permitir múltiples observaciones</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_observaciones_multiples_comportamiento'] == 0 || $datosConfiguracion['conf_observaciones_multiples_comportamiento'] == null){ echo "selected";} ?>>✗ NO, solo una observación</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Archivos y Multimedia -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-file-o"></i>
                        Archivos y Multimedia
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-upload"></i>
                                Peso máximo archivos (MB) 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Es el peso máximo, en MB, que debe tener el archivo que suben los usuarios en los diferentes lugares de la plataforma. Tenga en cuenta que entre más pese el archivo más tiempo puede tomar el proceso de carga y descarga del mismo.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <input 
                                    type="number" 
                                    name="pesoArchivos" 
                                    class="form-control" 
                                    value="<?=$datosConfiguracion['conf_max_peso_archivos'];?>" 
                                    style="max-width: 150px;"
                                    min="1"
                                    max="100"
                                    <?=$disabledPermiso;?>
                                >
                                <small class="form-text text-muted">Rango recomendado: 5 a 20 MB</small>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="save-button-container">
                        <?php $botones = new botonesGuardar("dev-instituciones.php", Modulos::validarPermisoEdicion() || $datosUsuarioActual['uss_tipo'] == TIPO_DEV); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
