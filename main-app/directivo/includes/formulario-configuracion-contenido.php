
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
                <i class="fa fa-cog"></i>
                Configuración Completa del Sistema
                <span style="font-size: 14px; font-weight: normal; color: #718096; margin-left: 10px;">
                    Institución: <?= $datosConfiguracion['ins_siglas'] ?? 'N/A'; ?> - Año: <?= $year; ?>
                </span>
            </header>
            <div class="panel-body">
                <!-- Selector de Año -->
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #667eea;">
                    <div class="form-group row" style="margin-bottom: 0;">
                        <label class="col-sm-3 control-label" style="font-weight: 600; color: #1e40af; display: flex; align-items: center; gap: 10px;">
                            <i class="fa fa-calendar-check-o"></i>
                            Seleccionar Año
                        </label>
                        <div class="col-sm-9">
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <select 
                                    id="selectorAno" 
                                    class="form-control" 
                                    style="max-width: 200px; font-weight: 600;"
                                    onchange="cambiarAno(this.value)"
                                >
                                    <?php
                                    if (isset($yearsDisponibles) && !empty($yearsDisponibles)) {
                                        foreach ($yearsDisponibles as $anoDisponible) {
                                            $selected = ($anoDisponible == $year) ? 'selected' : '';
                                            echo '<option value="' . $anoDisponible . '" ' . $selected . '>' . $anoDisponible . '</option>';
                                        }
                                    } else {
                                        echo '<option value="' . $year . '" selected>' . $year . '</option>';
                                    }
                                    ?>
                                </select>
                                <span style="color: #64748b; font-size: 14px;">
                                    <i class="fa fa-info-circle"></i>
                                    Selecciona el año para ver/editar su configuración
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                function cambiarAno(anoSeleccionado) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const idInstitucion = urlParams.get('id') || '';
                    // Codificar el año en base64 para mantener consistencia con el sistema
                    const yearEncoded = btoa(anoSeleccionado.toString());
                    const nuevaUrl = '<?= $_SERVER['PHP_SELF']; ?>?id=' + idInstitucion + '&year=' + yearEncoded;
                    window.location.href = nuevaUrl;
                }
                </script>

                <?php if (empty($datosConfiguracion['conf_id'])) { ?>
                <div class="alert-modern alert-info" style="margin-bottom: 30px;">
                    <i class="fa fa-info-circle"></i>
                    <div>
                        <strong>Información:</strong> No existe configuración para el año <strong><?= $year; ?></strong>. 
                        Puedes crear una nueva configuración completando el formulario y guardando los datos.
                    </div>
                </div>
                <?php } ?>

                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post" id="formConfiguracionCompleta">
                    <input type="hidden" name="configDEV" value="<?= $configDEV ?? 1; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id'] ?? ''; ?>">
                    <input type="hidden" name="idInstitucion" value="<?= $id ?? ''; ?>">
                    <input type="hidden" name="agno" value="<?= $year; ?>">
                    <input type="hidden" name="configTab" value="<?=BDT_Configuracion::CONFIG_SISTEMA_GENERAL;?>">
                    
                    <!-- ============================================ -->
                    <!-- SECCIÓN 1: CONFIGURACIÓN GENERAL -->
                    <!-- ============================================ -->
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                        <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fa fa-home"></i>
                            Configuración General
                        </h3>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-calendar"></i>
                                Año Actual
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="agno" class="form-control" value="<?=$year;?>" readonly style="max-width: 150px;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-clock-o"></i>
                                Periodo Actual 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="periodo" required style="max-width: 200px;">
                                    <option value="">Seleccione una opción</option>
                                    <?php
                                    $p = 1;
                                    $pFinal = ($config['conf_periodos_maximos'] ?? 4) + 1;
                                    while($p <= $pFinal){
                                        $label = 'Periodo '.$p;
                                        if($p == $pFinal) {
                                            $label = 'AÑO FINALIZADO';
                                        }
                                        if($p == ($datosConfiguracion['conf_periodo'] ?? ''))
                                            echo '<option value="'.$p.'" selected>'.$label.'</option>';
                                        else
                                            echo '<option value="'.$p.'">'.$label.'</option>';	
                                        $p++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-upload"></i>
                                Peso máximo archivos (MB) 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input 
                                    type="number" 
                                    name="pesoArchivos" 
                                    class="form-control" 
                                    value="<?=$datosConfiguracion['conf_max_peso_archivos'] ?? 10;?>" 
                                    style="max-width: 150px;"
                                    min="1"
                                    max="100"
                                >
                                <small class="form-text text-muted">Rango recomendado: 5 a 20 MB</small>
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 40px 0; border: 1px solid #e2e8f0;">

                    <!-- ============================================ -->
                    <!-- SECCIÓN 2: COMPORTAMIENTO DEL SISTEMA -->
                    <!-- ============================================ -->
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                        <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fa fa-sliders"></i>
                            Comportamiento del Sistema
                        </h3>
                    </div>


                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-calendar-o"></i>
                                Periodos a trabajar 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input 
                                    type="number" 
                                    name="periodoTrabajar" 
                                    class="form-control" 
                                    value="<?=$datosConfiguracion['conf_periodos_maximos'] ?? 4;?>" 
                                    required 
                                    style="max-width: 150px;"
                                >
                                <small class="form-text text-muted">Ejemplo: 4 (para trimestral o bimestral)</small>
                            </div>
                        </div>
                    </div>

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
                                            value="<?=$datosConfiguracion['conf_nota_desde'] ?? 1;?>" 
                                            step="0.1"
                                        >
                                    </div>
                                    <span style="margin-top: 25px;">→</span>
                                    <div style="flex: 1; max-width: 150px;">
                                        <label style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Hasta</label>
                                        <input 
                                            type="number" 
                                            name="hasta" 
                                            class="form-control" 
                                            value="<?=$datosConfiguracion['conf_nota_hasta'] ?? 5;?>" 
                                            step="0.1"
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
                                    value="<?=$datosConfiguracion['conf_nota_minima_aprobar'] ?? 3.0;?>" 
                                    style="max-width: 150px;"
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
                            </label>
                            <div class="col-sm-9">
                                <input 
                                    type="number" 
                                    name="decimalesNotas" 
                                    class="form-control" 
                                    value="<?=$datosConfiguracion['conf_decimales_notas'] ?? 1;?>"
                                    min="0"
                                    max="4"
                                    style="max-width: 150px;"
                                >
                                <small class="form-text text-muted">Rango: 0 a 4</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-percent"></i>
                                Porcentaje en asignaturas 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="porcenAsigna" style="max-width: 200px;">
                                    <option value="SI" <?php if(($datosConfiguracion['conf_agregar_porcentaje_asignaturas'] ?? 'NO')=='SI'){ echo "selected";} ?>>SÍ, asignar porcentajes</option>
                                    <option value="NO" <?php if(($datosConfiguracion['conf_agregar_porcentaje_asignaturas'] ?? 'NO')=='NO'){ echo "selected";} ?>>NO, misma ponderación</option>
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
                                <select class="form-control" name="estiloNotas" required style="max-width: 300px;">
                                    <option value="">Seleccione una opción</option>
                                    <?php
                                    if(isset($config) && function_exists('categoriasNota::traerCategoriasNotasInstitucion')){
                                        $opcionesGeneralesConsulta = categoriasNota::traerCategoriasNotasInstitucion($config);
                                        while($opcionesGeneralesDatos = mysqli_fetch_array($opcionesGeneralesConsulta, MYSQLI_BOTH)){
                                            if(($datosConfiguracion['conf_notas_categoria'] ?? '')==$opcionesGeneralesDatos['catn_id'])
                                                echo '<option value="'.$opcionesGeneralesDatos['catn_id'].'" selected>'.$opcionesGeneralesDatos['catn_nombre'].'</option>';
                                            else
                                                echo '<option value="'.$opcionesGeneralesDatos['catn_id'].'">'.$opcionesGeneralesDatos['catn_nombre'].'</option>';	
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?php if (defined('CUALITATIVA') && defined('CUANTITATIVA')) { ?>
                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-eye"></i>
                                Formato de notas 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="formaNotas" style="max-width: 200px;">
                                    <option value="<?=CUALITATIVA?>" <?php if(($datosConfiguracion['conf_forma_mostrar_notas'] ?? CUANTITATIVA) == CUALITATIVA){ echo "selected";} ?>>CUALITATIVA (sin números)</option>
                                    <option value="<?=CUANTITATIVA?>" <?php if(($datosConfiguracion['conf_forma_mostrar_notas'] ?? CUANTITATIVA) == CUANTITATIVA){ echo "selected";} ?>>CUANTITATIVA (con números)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php }?>

                    <hr style="margin: 40px 0; border: 1px solid #e2e8f0;">

                    <!-- ============================================ -->
                    <!-- SECCIÓN 3: PREFERENCIAS -->
                    <!-- ============================================ -->
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                        <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fa fa-heart"></i>
                            Preferencias del Sistema
                        </h3>
                    </div>


                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-user"></i>
                                Orden del nombre de estudiantes 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="ordenEstudiantes" style="max-width: 300px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_orden_nombre_estudiantes'] ?? 1)==1){ echo "selected";} ?>>Nombres y Apellidos (Andrés David Arias Pertuz)</option>
                                    <option value="2" <?php if(($datosConfiguracion['conf_orden_nombre_estudiantes'] ?? 1)==2){ echo "selected";} ?>>Apellidos y Nombres (Arias Pertuz Andrés David)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-list"></i>
                                Registros por página 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="numRegistros" style="max-width: 200px;">
                                    <option value="20" <?php if(($datosConfiguracion['conf_num_registros'] ?? 20)==20){ echo "selected";} ?>>20 registros</option>
                                    <option value="30" <?php if(($datosConfiguracion['conf_num_registros'] ?? 20)==30){ echo "selected";} ?>>30 registros</option>
                                    <option value="50" <?php if(($datosConfiguracion['conf_num_registros'] ?? 20)==50){ echo "selected";} ?>>50 registros</option>
                                    <option value="100" <?php if(($datosConfiguracion['conf_num_registros'] ?? 20)==100){ echo "selected";} ?>>100 registros</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-user-times"></i>
                                Mostrar estudiantes cancelados 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="mostrarEstudiantesCancelados" style="max-width: 200px;">
                                    <option value="SI" <?php if(($datosConfiguracion['conf_mostrar_estudiantes_cancelados'] ?? 'NO') == 'SI'){ echo "selected";} ?>>SÍ, mostrar en informes</option>
                                    <option value="NO" <?php if(($datosConfiguracion['conf_mostrar_estudiantes_cancelados'] ?? 'NO') == 'NO'){ echo "selected";} ?>>NO, ocultar de informes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-bar-chart-o"></i>
                                Mostrar notas en panel lateral 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="mostrarNotasPanelLateral" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_ocultar_panel_lateral_notas_estudiantes'] ?? 1) == 1){ echo "selected";} ?>>SÍ, mostrar notas y desempeño</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_ocultar_panel_lateral_notas_estudiantes'] ?? 1) == '0'){ echo "selected";} ?>>NO, ocultar información</option>
                                </select>
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
                                <select class="form-control" name="solicitarAcudiente2" style="max-width: 200px;">
                                    <option value="SI" <?php if(($datosConfiguracion['conf_solicitar_acudiente_2'] ?? 'NO') == 'SI'){ echo "selected";} ?>>SÍ, solicitar segundo acudiente</option>
                                    <option value="NO" <?php if(($datosConfiguracion['conf_solicitar_acudiente_2'] ?? 'NO') == 'NO'){ echo "selected";} ?>>NO, solo primer acudiente</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 40px 0; border: 1px solid #e2e8f0;">

                    <!-- ============================================ -->
                    <!-- SECCIÓN 4: INFORMES Y REPORTES -->
                    <!-- ============================================ -->
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                        <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fa fa-file-text"></i>
                            Informes y Reportes
                        </h3>
                    </div>


                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-file-text-o"></i>
                                Formato de boletín 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="formatoBoletin" style="max-width: 300px;">
                                    <?php 
                                    if(isset($conexion) && defined('BD_ADMIN')){
                                        $consultaBoletin = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".opciones_generales WHERE ogen_grupo=15");
                                        while ($datosBoletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH)) {
                                    ?>
                                        <option value="<?=$datosBoletin['ogen_id']; ?>" <?php if(($datosConfiguracion['conf_formato_boletin'] ?? '') == $datosBoletin['ogen_id']){ echo "selected";} ?>><?=$datosBoletin['ogen_nombre'];?></option>
                                    <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-certificate"></i>
                                Estilo de certificado 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="certificado" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_certificado'] ?? 1)==1){ echo "selected";} ?>>Certificado 1</option>
                                    <option value="2" <?php if(($datosConfiguracion['conf_certificado'] ?? 1)==2){ echo "selected";} ?>>Certificado 2</option>
                                    <option value="3" <?php if(($datosConfiguracion['conf_certificado'] ?? 1)==3){ echo "selected";} ?>>Certificado 3</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?php if(defined('SI') && defined('NO')) { ?>
                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-ticket"></i>
                                Estampilla de pago en certificados 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="estampilla" style="max-width: 200px;">
                                    <option value="<?=SI?>" <?php if(($datosConfiguracion['conf_estampilla_certificados'] ?? NO) == SI){ echo "selected";} ?>>SÍ, agregar estampilla</option>
                                    <option value="<?=NO?>" <?php if(($datosConfiguracion['conf_estampilla_certificados'] ?? NO) == NO){ echo "selected";} ?>>NO, sin estampilla</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-book"></i>
                                Estilo de Libro Final 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="libroFinal" style="max-width: 300px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_libro_final'] ?? 1)==1){ echo "selected";} ?>>Formato libro final 1</option>
                                    <option value="2" <?php if(($datosConfiguracion['conf_libro_final'] ?? 1)==2){ echo "selected";} ?>>Formato libro final 2</option>
                                    <option value="3" <?php if(($datosConfiguracion['conf_libro_final'] ?? 1)==3){ echo "selected";} ?>>Formato libro final 3 (1 Fast)</option>
                                    <option value="4" <?php if(($datosConfiguracion['conf_libro_final'] ?? 1)==4){ echo "selected";} ?>>Formato libro final 4 (2 Fast)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-calculator"></i>
                                Promedio en libro final 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="promedioLibroFinal" style="max-width: 250px;">
                                    <option value="<?=BDT_Configuracion::TODOS_PERIODOS;?>" <?php if(($datosConfiguracion['conf_promedio_libro_final'] ?? BDT_Configuracion::TODOS_PERIODOS) == BDT_Configuracion::TODOS_PERIODOS){ echo "selected";} ?>>Por todos los periodos</option>
                                    <option value="<?=BDT_Configuracion::PERIODOS_CURSADOS;?>" <?php if(($datosConfiguracion['conf_promedio_libro_final'] ?? '') == BDT_Configuracion::PERIODOS_CURSADOS){ echo "selected";} ?>>Solo periodos cursados</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-expand"></i>
                                Medidas del Logo (Ancho - Alto) 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div style="display: flex; gap: 15px; align-items: center;">
                                    <div style="flex: 1; max-width: 150px;">
                                        <label style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Ancho (px)</label>
                                        <input type="number" name="logoAncho" class="form-control" value="<?=$datosConfiguracion['conf_ancho_imagen'] ?? 150;?>">
                                    </div>
                                    <span style="margin-top: 25px;">×</span>
                                    <div style="flex: 1; max-width: 150px;">
                                        <label style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Alto (px)</label>
                                        <input type="number" name="logoAlto" class="form-control" value="<?=$datosConfiguracion['conf_alto_imagen'] ?? 150;?>">
                                    </div>
                                </div>
                                <small class="form-text text-muted">Recomendado: 150px × 150px</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-building-o"></i>
                                Mostrar nombre del colegio 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="mostrarNombre" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_mostrar_nombre'] ?? 1)==1){ echo "selected";} ?>>SÍ, mostrar en encabezado</option>
                                    <option value="2" <?php if(($datosConfiguracion['conf_mostrar_nombre'] ?? 1)==2){ echo "selected";} ?>>NO, solo logo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-header"></i>
                                Encabezado completo 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="mostrarEncabezadoInformes" style="max-width: 200px;">
                                    <option value="1" <?php if (($datosConfiguracion['conf_mostrar_encabezado_informes'] ?? 1) == 1) { echo "selected"; } ?>>Encabezado completo</option>
                                    <option value="0" <?php if (($datosConfiguracion['conf_mostrar_encabezado_informes'] ?? 1) == 0) { echo "selected"; } ?>>Solo logo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-list-alt"></i>
                                Calcular notas en sábana 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="notasReporteSabanas" style="max-width: 200px;">
                                    <option value="1" <?php if (($datosConfiguracion['conf_reporte_sabanas_nota_indocador'] ?? 1) == 1) { echo "selected"; } ?>>Calcular por indicadores</option>
                                    <option value="0" <?php if (($datosConfiguracion['conf_reporte_sabanas_nota_indocador'] ?? 1) == 0) { echo "selected"; } ?>>No calcular</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-cog"></i>
                                Generación de informes 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="generarInforme" style="max-width: 300px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_porcentaje_completo_generar_informe'] ?? 1)==1){ echo "selected";} ?>>Requiere 100% de notas registradas</option>
                                    <option value="2" <?php if(($datosConfiguracion['conf_porcentaje_completo_generar_informe'] ?? 1)==2){ echo "selected";} ?>>Omitir estudiantes sin 100% de notas</option>
                                    <option value="3" <?php if(($datosConfiguracion['conf_porcentaje_completo_generar_informe'] ?? 1)==3){ echo "selected";} ?>>Usar porcentaje actual disponible</option>
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
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="observacionesMultiples" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_observaciones_multiples_comportamiento'] ?? 0)==1){ echo "selected";} ?>>SÍ, permitir múltiples observaciones</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_observaciones_multiples_comportamiento'] ?? 0) == 0){ echo "selected";} ?>>NO, solo una observación</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-calendar"></i>
                                Fecha del próximo informe 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="date" name="fechapa" class="form-control" value="<?=$datosConfiguracion['conf_fecha_parcial'] ?? '';?>" style="max-width: 250px;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-align-left"></i>
                                Texto de encabezado 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <textarea cols="80" id="editor1" name="descrip" rows="10"><?=$datosConfiguracion['conf_descripcion_parcial'] ?? '';?></textarea>
                                <small class="form-text text-muted">Este texto aparecerá en el encabezado del informe parcial</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-pencil-square"></i>
                                Firma del estudiante 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="firmaEstudiante" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_firma_estudiante_informe_asistencia'] ?? 1)==1){ echo "selected";} ?>>SÍ, mostrar campo</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_firma_estudiante_informe_asistencia'] ?? 1)==0){ echo "selected";} ?>>NO mostrar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?php if(defined('SI') && defined('NO')) { ?>
                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-list"></i>
                                Columnas de inasistencia 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="firmaAsistencia" style="max-width: 200px;">
                                    <option value="<?=SI?>" <?=($datosConfiguracion['conf_firma_inasistencia_planilla_notas_doc'] ?? NO) == SI ? "selected" : ""; ?>>SÍ, mostrar columnas</option>
                                    <option value="<?=NO?>" <?=($datosConfiguracion['conf_firma_inasistencia_planilla_notas_doc'] ?? NO) == NO ? "selected" : ""; ?>>NO mostrar</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <hr style="margin: 40px 0; border: 1px solid #e2e8f0;">

                    <!-- ============================================ -->
                    <!-- SECCIÓN 5: PERMISOS -->
                    <!-- ============================================ -->
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                        <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fa fa-shield"></i>
                            Control de Permisos
                        </h3>
                    </div>


                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-user"></i>
                                Cambiar usuario de acceso 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="cambiarNombreUsuario" style="max-width: 200px;">
                                    <option value="SI" <?php if (($datosConfiguracion['conf_cambiar_nombre_usuario'] ?? 'NO') == 'SI') { echo "selected"; } ?>>SÍ, permitir</option>
                                    <option value="NO" <?php if (($datosConfiguracion['conf_cambiar_nombre_usuario'] ?? 'NO') == 'NO') { echo "selected"; } ?>>NO, bloquear</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-history"></i>
                                Editar años anteriores 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="editarInfoYears" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_permiso_edicion_years_anteriores'] ?? 0)==1){ echo "selected";} ?>>SÍ, permitir edición</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_permiso_edicion_years_anteriores'] ?? 0)==0){ echo "selected";} ?>>NO, solo consulta</option>
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
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="dobleBuscador" style="max-width: 200px;">
                                    <option value="SI" <?php if (($datosConfiguracion['conf_doble_buscador'] ?? 'NO') == 'SI') { echo "selected"; } ?>>SÍ, activar</option>
                                    <option value="NO" <?php if (($datosConfiguracion['conf_doble_buscador'] ?? 'NO') == 'NO') { echo "selected"; } ?>>NO, un solo buscador</option>
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
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="permisoConsolidado" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_editar_definitivas_consolidado'] ?? 0)==1){ echo "selected";} ?>>SÍ, permitir</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_editar_definitivas_consolidado'] ?? 0)==0){ echo "selected";} ?>>NO, bloquear</option>
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
                                <select class="form-control" name="permisoEliminarCargas" style="max-width: 200px;">
                                    <option value="SI" <?php if(($datosConfiguracion['conf_permiso_eliminar_cargas'] ?? 'NO') == 'SI'){ echo "selected";} ?>>SÍ, permitir eliminación</option>
                                    <option value="NO" <?php if(($datosConfiguracion['conf_permiso_eliminar_cargas'] ?? 'NO') == 'NO'){ echo "selected";} ?>>NO, solo archivar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-list-ol"></i>
                                Ver puestos en sábanas 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="permisoDocentesPuestosSabanas" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_ver_promedios_sabanas_docentes'] ?? 1)==1){ echo "selected";} ?>>SÍ, mostrar puestos</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_ver_promedios_sabanas_docentes'] ?? 1)==0){ echo "selected";} ?>>NO, ocultar puestos</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-eye"></i>
                                Mostrar calificaciones (Acudientes) 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="caliAcudientes" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_calificaciones_acudientes'] ?? 1)==1){ echo "selected";} ?>>SÍ, ver calificaciones</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_calificaciones_acudientes'] ?? 1)==0){ echo "selected";} ?>>NO, ocultar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-file-text"></i>
                                Descargar informe parcial (Acudientes) 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="informeParcial" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_informe_parcial'] ?? 1)==1){ echo "selected";} ?>>SÍ, permitir descarga</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_informe_parcial'] ?? 1)==0){ echo "selected";} ?>>NO, bloquear</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-download"></i>
                                Descargar boletín (Acudientes) 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="descargarBoletin" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_permiso_descargar_boletin'] ?? 1)==1){ echo "selected";} ?>>SÍ, permitir descarga</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_permiso_descargar_boletin'] ?? 1)==0){ echo "selected";} ?>>NO, bloquear</option>
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
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="activarEncuestaReservaCupo" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_activar_encuesta'] ?? 0)==1){ echo "selected";} ?>>SÍ, activar encuesta</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_activar_encuesta'] ?? 0)==0){ echo "selected";} ?>>NO, desactivar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-bar-chart"></i>
                                Mostrar calificaciones (Estudiantes) 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="caliEstudiantes" style="max-width: 200px;">
                                    <option value="1" <?php if(($datosConfiguracion['conf_mostrar_calificaciones_estudiantes'] ?? 1)==1){ echo "selected";} ?>>SÍ, ver calificaciones</option>
                                    <option value="0" <?php if(($datosConfiguracion['conf_mostrar_calificaciones_estudiantes'] ?? 1)==0){ echo "selected";} ?>>NO, ocultar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-key"></i>
                                Cambiar su contraseña (Estudiantes) 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="cambiarClaveEstudiantes" style="max-width: 200px;">
                                    <option value="SI" <?php if (($datosConfiguracion['conf_cambiar_clave_estudiantes'] ?? 'NO') == 'SI') { echo "selected"; } ?>>SÍ, permitir cambio</option>
                                    <option value="NO" <?php if (($datosConfiguracion['conf_cambiar_clave_estudiantes'] ?? 'NO') == 'NO') { echo "selected"; } ?>>NO, bloquear</option>
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
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="pasosMatricula" style="max-width: 200px;">
                                    <option value="1" <?php if (($datosConfiguracion['conf_mostrar_pasos_matricula'] ?? 0) == 1) { echo "selected"; } ?>>SÍ, mostrar pasos</option>
                                    <option value="0" <?php if (($datosConfiguracion['conf_mostrar_pasos_matricula'] ?? 0) == 0) { echo "selected"; } ?>>NO, ocultar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 40px 0; border: 1px solid #e2e8f0;">

                    <!-- ============================================ -->
                    <!-- SECCIÓN 6: ESTILOS Y APARIENCIA -->
                    <!-- ============================================ -->
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                        <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fa fa-paint-brush"></i>
                            Estilos y Apariencia
                        </h3>
                    </div>


                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-eyedropper"></i>
                                Color de notas perdidas 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input 
                                    type="color" 
                                    name="perdida" 
                                    class="form-control" 
                                    value="<?=$datosConfiguracion['conf_color_perdida'] ?? '#ef4444';?>" 
                                    style="height: 60px; width: 150px; cursor: pointer; border: none;"
                                >
                                <small class="form-text text-muted">Usado para notas menores a la nota de aprobación</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-eyedropper"></i>
                                Color de notas ganadas 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input 
                                    type="color" 
                                    name="ganada" 
                                    class="form-control" 
                                    value="<?=$datosConfiguracion['conf_color_ganada'] ?? '#10b981';?>" 
                                    style="height: 60px; width: 150px; cursor: pointer; border: none;"
                                >
                                <small class="form-text text-muted">Usado para notas mayores o iguales a la nota de aprobación</small>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="save-button-container" style="margin-top: 40px; padding: 30px 0; border-top: 2px solid #e2e8f0;">
                        <?php $botones = new botonesGuardar("dev-instituciones.php", true); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Script para manejar el guardado de todas las secciones
document.getElementById('formConfiguracionCompleta').addEventListener('submit', function(e) {
    // El formulario se enviará con todos los campos
    // El backend procesará según el configTab que corresponda
    // Por ahora, usamos CONFIG_SISTEMA_GENERAL como principal
    // pero el backend deberá procesar todos los campos
});
</script>
