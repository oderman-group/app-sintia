
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
                <i class="fa fa-file-text"></i>
                Informes y Reportes
            </header>
            <div class="panel-body">
                
                <div class="alert-modern alert-info">
                    <i class="fa fa-info-circle"></i>
                    <div>
                        <strong>Información:</strong> Configura los formatos y estilos de los documentos académicos que se generan en la plataforma.
                    </div>
                </div>
                
                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
                    <input type="hidden" name="configDEV" value="<?= $configDEV; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id']; ?>">
                    <input type="hidden" name="configTab" value="<?=BDT_Configuracion::CONFIG_SISTEMA_INFORMES;?>">

                    <!-- Sección: Documentos Académicos -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-file-pdf-o"></i>
                        Documentos Académicos
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-file-text-o"></i>
                                Formato de boletín 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <select class="form-control" id="formatoBoletin" name="formatoBoletin" onChange="cambiarTipoBoletin()" <?=$disabledPermiso;?> style="max-width: 300px;">
                                        <?php 
                                        $consultaBoletin = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".opciones_generales WHERE ogen_grupo=15");
                                        while ($datosBoletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH)) {
                                        ?>
                                            <option value="<?=$datosBoletin['ogen_id']; ?>" <?php if($datosConfiguracion['conf_formato_boletin'] == $datosBoletin['ogen_id']){ echo "selected";} ?>><?=$datosBoletin['ogen_nombre'];?></option>
                                        <?php }?>
                                    </select>
                                    <button type="button" title="Ver formato boletín" class="btn btn-primary btn-sm" data-toggle="popover_boletin" style="padding: 10px 20px;">
                                        <i class="fa fa-eye"></i> Vista Previa
                                    </button>
                                </div>
                                <script>
                                    $(document).ready(function() {
                                    $('[data-toggle="popover_boletin"]').popover({
                                        html: true,
                                        content: function () {
                                            valorB = document.getElementById("formatoBoletin");
                                            return '<div id="myPopoverBol" class="popover-content"><label id="lbl_tipo_bol">Estilo Boletín '+valorB.value+'</label>'+
                                            '<img id="img-boletin-true" src="../files/images/boletines/tipo'+valorB.value+'.png" class="w-100" />'+'</div>';}
                                        });
                                    });
                                    function cambiarTipoBoletin() {
                                        var imagen_boletin = document.getElementById('img-boletin-true'); 
                                        if (imagen_boletin) {
                                            var valor    = document.getElementById("formatoBoletin");  
                                            var lbl_tipo = document.getElementById('lbl_tipo_bol');
                                            imagen_boletin.src ="../files/images/boletines/tipo"+valor.value+".png";
                                            lbl_tipo.textContent='Estilo Boletín '+valor.value;
                                        }
                                    }
                                </script>
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
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <select class="form-control" id="tipoCertificado" name="certificado" onChange="cambiarTipo()" <?=$disabledPermiso;?> style="max-width: 300px;">
                                        <option value="1" <?php if($datosConfiguracion['conf_certificado']==1){ echo "selected";} ?>>Certificado 1</option>
                                        <option value="2" <?php if($datosConfiguracion['conf_certificado']==2){ echo "selected";} ?>>Certificado 2</option>
                                        <option value="3" <?php if($datosConfiguracion['conf_certificado']==3){ echo "selected";} ?>>Certificado 3</option>
                                    </select>
                                    <button type="button" title="Ver formato certificado" class="btn btn-primary btn-sm" data-toggle="popover" style="padding: 10px 20px;">
                                        <i class="fa fa-eye"></i> Vista Previa
                                    </button>
                                </div>
                                <script>
                                    $(document).ready(function() {
                                    $('[data-toggle="popover"]').popover({
                                        html: true,
                                        content: function () {
                                            valor = document.getElementById("tipoCertificado");
                                            return '<div id="myPopover" class="popover-content"><label id="lbl_tipo">Estilo Certificado '+valor.value+'</label>'+
                                            '<img id="img-boletin" src="../files/images/certificados/tipo'+valor.value+'.png" class="w-100" />'+'</div>';}
                                        });
                                    });
                                    function cambiarTipo() {
                                        var imagen_boletin = document.getElementById('img-boletin');
                                        if (imagen_boletin) {
                                            var valor    = document.getElementById("tipoCertificado");
                                            var lbl_tipo = document.getElementById('lbl_tipo');
                                            imagen_boletin.src ="../files/images/certificados/tipo"+valor.value+".png";
                                            lbl_tipo.textContent='Estilo Certificado '+valor.value;
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-ticket"></i>
                                Estampilla de pago en certificados 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta opción permite agregar un referente o estampilla de pago a los certificados.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="estampilla" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="<?=SI?>" <?php if($datosConfiguracion['conf_estampilla_certificados'] == SI){ echo "selected";} ?>>SÍ, agregar estampilla</option>
                                    <option value="<?=NO?>" <?php if($datosConfiguracion['conf_estampilla_certificados'] == NO){ echo "selected";} ?>>NO, sin estampilla</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-book"></i>
                                Estilo de Libro Final 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <select class="form-control" id="tipoLibroFinal" name="libroFinal" onchange="cambiarTipoLibro()" <?=$disabledPermiso;?> style="max-width: 300px;">
                                        <option value="1" <?php if($datosConfiguracion['conf_libro_final']==1){ echo "selected";} ?>>Formato libro final 1</option>
                                        <option value="2" <?php if($datosConfiguracion['conf_libro_final']==2){ echo "selected";} ?>>Formato libro final 2</option>
                                        <option value="3" <?php if($datosConfiguracion['conf_libro_final']==3){ echo "selected";} ?>>Formato libro final 3 (1 Fast)</option>
                                        <option value="4" <?php if($datosConfiguracion['conf_libro_final']==4){ echo "selected";} ?>>Formato libro final 4 (2 Fast)</option>
                                    </select>
                                    <button type="button" title="Ver formato libro final" class="btn btn-primary btn-sm" data-toggle="popover_2" style="padding: 10px 20px;">
                                        <i class="fa fa-eye"></i> Vista Previa
                                    </button>
                                </div>
                                <script>
                                    $(document).ready(function(){
                                    $('[data-toggle="popover_2"]').popover({
                                        html: true,
                                        content: function () {
                                            valor = document.getElementById("tipoLibroFinal");
                                        return '<div id="myPopover" class="popover-content"><label id="lbl_tipo_libro">Estilo libro final '+valor.value+'</label>'+
                                        '<img id="img-libro" src="../files/images/libros/tipo'+valor.value+'.png" class="w-100" />'+                                                       
                                        '</div>';}
                                        });                                                    
                                    });
                                    function cambiarTipoLibro(){  
                                        var imagen_libro = document.getElementById('img-libro'); 
                                        if(imagen_libro){                                                     
                                        var valor = document.getElementById("tipoLibroFinal");  
                                        var lbl_tipo_libro = document.getElementById('lbl_tipo_libro');
                                        imagen_libro.src ="../files/images/libros/tipo"+valor.value+".png";
                                        lbl_tipo_libro.textContent='Estilo libro final '+valor.value;
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-calculator"></i>
                                Promedio en libro final 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta acción permite escoger como promediar la definitiva de los estudiantes retirados en el libro final.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="promedioLibroFinal" <?=$disabledPermiso;?>>
                                    <option value="<?=BDT_Configuracion::TODOS_PERIODOS;?>" <?php if($datosConfiguracion['conf_promedio_libro_final'] == BDT_Configuracion::TODOS_PERIODOS){ echo "selected";} ?>>📅 Por todos los periodos</option>
                                    <option value="<?=BDT_Configuracion::PERIODOS_CURSADOS;?>" <?php if($datosConfiguracion['conf_promedio_libro_final'] == BDT_Configuracion::PERIODOS_CURSADOS){ echo "selected";} ?>>✓ Solo periodos cursados</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Personalización Visual -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-image"></i>
                        Personalización Visual
                    </h4>

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
                                        <input type="number" name="logoAncho" class="form-control" value="<?=$datosConfiguracion['conf_ancho_imagen'];?>" <?=$disabledPermiso;?>>
                                    </div>
                                    <span style="margin-top: 25px;">×</span>
                                    <div style="flex: 1; max-width: 150px;">
                                        <label style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Alto (px)</label>
                                        <input type="number" name="logoAlto" class="form-control" value="<?=$datosConfiguracion['conf_alto_imagen'];?>" <?=$disabledPermiso;?>>
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
                                <select class="form-control" name="mostrarNombre" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_mostrar_nombre']==1){ echo "selected";} ?>>✓ SÍ, mostrar en encabezado</option>
                                    <option value="2" <?php if($datosConfiguracion['conf_mostrar_nombre']==2){ echo "selected";} ?>>✗ NO, solo logo</option>
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
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta acción permite ver el encabezado general de los informes o solo el logo.">
                                    <i class="fa fa-info"></i>
                                </button>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="mostrarEncabezadoInformes" style="max-width: 200px;">
                                    <option value="1" <?php if ($datosConfiguracion['conf_mostrar_encabezado_informes'] == 1) { echo "selected"; } ?>>✓ Encabezado completo</option>
                                    <option value="0" <?php if ($datosConfiguracion['conf_mostrar_encabezado_informes'] == 0) { echo "selected"; } ?>>✗ Solo logo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Reportes y Cálculos -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-table"></i>
                        Reportes y Cálculos
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-list-alt"></i>
                                Calcular notas en sábana 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta acción permite calcular en el reporte de sábanas, las notas por indicador o no.">
                                    <i class="fa fa-info"></i>
                                </button>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="notasReporteSabanas" style="max-width: 200px;">
                                    <option value="1" <?php if ($datosConfiguracion['conf_reporte_sabanas_nota_indocador'] == 1) { echo "selected"; } ?>>✓ Calcular por indicadores</option>
                                    <option value="0" <?php if ($datosConfiguracion['conf_reporte_sabanas_nota_indocador'] == 0) { echo "selected"; } ?>>✗ No calcular</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Informes Parciales -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-file-o"></i>
                        Informes Parciales
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-calendar"></i>
                                Fecha del próximo informe 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="date" name="fechapa" class="form-control" value="<?=$datosConfiguracion['conf_fecha_parcial'];?>" style="max-width: 250px;" <?=$disabledPermiso;?>>
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
                                <textarea cols="80" id="editor1" name="descrip" rows="10" <?=$disabledPermiso;?>><?=$datosConfiguracion['conf_descripcion_parcial'];?></textarea>
                                <small class="form-text text-muted">Este texto aparecerá en el encabezado del informe parcial</small>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Asistencia y Firmas -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-check-square-o"></i>
                        Asistencia y Firmas
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-pencil-square"></i>
                                Firma del estudiante 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta opción permite dar un espacio para que el estudiante firme en el reporte de asistencia a la entrega de informes.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="firmaEstudiante" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_firma_estudiante_informe_asistencia']==1){ echo "selected";} ?>>✓ SÍ, mostrar campo</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_firma_estudiante_informe_asistencia']==0){ echo "selected";} ?>>✗ NO mostrar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-list"></i>
                                Columnas de inasistencia 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Esta opción permite mostrar campo para la firma del docente e inasistencias en planilla de docentes con notas.">
                                    <i class="fa fa-info"></i>
                                </button> 
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="firmaAsistencia" style="max-width: 200px;" <?=$disabledPermiso;?>>
                                    <option value="<?=SI?>" <?=$datosConfiguracion['conf_firma_inasistencia_planilla_notas_doc'] == SI ? "selected" : ""; ?>>✓ SÍ, mostrar columnas</option>
                                    <option value="<?=NO?>" <?=$datosConfiguracion['conf_firma_inasistencia_planilla_notas_doc'] == NO ? "selected" : ""; ?>>✗ NO mostrar</option>
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
