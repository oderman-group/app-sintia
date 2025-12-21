
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
                <i class="fa fa-paint-brush"></i>
                Estilos y Apariencia
            </header>
            <div class="panel-body">
                
                <div class="alert-modern alert-info">
                    <i class="fa fa-palette"></i>
                    <div>
                        <strong>Personalización Visual:</strong> Configura los colores y estilos visuales que se utilizarán en la plataforma para mejorar la experiencia visual.
                    </div>
                </div>
                
                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
                    <input type="hidden" name="configDEV" value="<?= $configDEV; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id']; ?>">
                    <input type="hidden" name="configTab" value="<?=BDT_Configuracion::CONFIG_SISTEMA_ESTILOS;?>">

                    <!-- Sección: Colores de Calificaciones -->
                    <h4 style="color: #667eea; font-weight: 700; margin: 25px 0 20px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-tint"></i>
                        Colores de Calificaciones
                    </h4>

                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-eyedropper"></i>
                                Color de notas 
                                <span class="required-indicator">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div style="display: flex; gap: 30px; align-items: start;">
                                    <!-- Nota Perdida -->
                                    <div style="flex: 1; max-width: 250px;">
                                        <div style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; text-align: center;">
                                            <div style="font-size: 14px; color: #6b7280; font-weight: 600; margin-bottom: 15px;">
                                                ✗ NOTAS PERDIDAS
                                            </div>
                                            <input 
                                                type="color" 
                                                name="perdida" 
                                                class="form-control" 
                                                value="<?=$datosConfiguracion['conf_color_perdida'];?>" 
                                                style="height: 80px; cursor: pointer; border: none;"
                                                <?=$disabledPermiso;?>
                                            >
                                            <div style="margin-top: 12px; font-size: 13px; color: #9ca3af;">
                                                Usado para notas <strong>menores</strong> a la nota de aprobación
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Separador -->
                                    <div style="display: flex; align-items: center; padding-top: 50px;">
                                        <i class="fa fa-exchange" style="font-size: 24px; color: #667eea;"></i>
                                    </div>

                                    <!-- Nota Ganada -->
                                    <div style="flex: 1; max-width: 250px;">
                                        <div style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; text-align: center;">
                                            <div style="font-size: 14px; color: #6b7280; font-weight: 600; margin-bottom: 15px;">
                                                ✓ NOTAS GANADAS
                                            </div>
                                            <input 
                                                type="color" 
                                                name="ganada" 
                                                class="form-control" 
                                                value="<?=$datosConfiguracion['conf_color_ganada'];?>" 
                                                style="height: 80px; cursor: pointer; border: none;"
                                                <?=$disabledPermiso;?>
                                            >
                                            <div style="margin-top: 12px; font-size: 13px; color: #9ca3af;">
                                                Usado para notas <strong>mayores o iguales</strong> a la nota de aprobación
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text text-muted" style="margin-top: 20px;">
                                    <i class="fa fa-info-circle"></i> 
                                    Estos colores se aplicarán en: boletines, reportes de calificaciones, sábanas y todos los informes académicos
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Vista Previa de Ejemplo -->
                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-eye"></i>
                                Vista Previa
                            </label>
                            <div class="col-sm-9">
                                <div style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); border-radius: 12px; padding: 25px;">
                                    <div style="font-size: 14px; color: #6b7280; margin-bottom: 15px; font-weight: 600;">
                                        Ejemplo de cómo se verán las notas:
                                    </div>
                                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                                        <div style="background: white; border-radius: 8px; padding: 15px 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Matemáticas</div>
                                            <div id="preview-perdida" style="font-size: 24px; font-weight: 700; color: <?=$datosConfiguracion['conf_color_perdida'];?>">
                                                2.5
                                            </div>
                                        </div>
                                        <div style="background: white; border-radius: 8px; padding: 15px 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Español</div>
                                            <div id="preview-ganada" style="font-size: 24px; font-weight: 700; color: <?=$datosConfiguracion['conf_color_ganada'];?>">
                                                4.5
                                            </div>
                                        </div>
                                        <div style="background: white; border-radius: 8px; padding: 15px 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Ciencias</div>
                                            <div style="font-size: 24px; font-weight: 700; color: <?=$datosConfiguracion['conf_color_ganada'];?>">
                                                3.8
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- JavaScript para actualizar vista previa en tiempo real -->
                    <script>
                        document.querySelector('input[name="perdida"]').addEventListener('change', function(e) {
                            document.getElementById('preview-perdida').style.color = e.target.value;
                        });
                        
                        document.querySelector('input[name="ganada"]').addEventListener('change', function(e) {
                            document.getElementById('preview-ganada').style.color = e.target.value;
                            document.querySelectorAll('#preview-ganada').forEach(el => {
                                el.style.color = e.target.value;
                            });
                        });
                    </script>

                    <!-- Save Button -->
                    <div class="save-button-container">
                        <?php $botones = new botonesGuardar("dev-instituciones.php",Modulos::validarPermisoEdicion() || $datosUsuarioActual['uss_tipo'] == TIPO_DEV); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
