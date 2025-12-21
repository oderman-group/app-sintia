
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
                <i class="fa fa-home"></i>
                Configuración General
            </header>
            <div class="panel-body">
                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
                    <input type="hidden" name="configDEV" value="<?= $configDEV; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id']; ?>">
                    <input type="hidden" name="configTab" value="<?=BDT_Configuracion::CONFIG_SISTEMA_GENERAL;?>">

                    <!-- Año Actual -->
                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-calendar"></i>
                                Año Actual
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="agno" class="form-control" value="<?=$year;?>" readonly <?=$disabledPermiso;?>>
                            </div>
                        </div>
                    </div>

                    <!-- Periodo Actual -->
                    <div class="form-group-modern">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">
                                <i class="fa fa-clock-o"></i>
                                Periodo Actual 
                                <span class="required-indicator">*</span>
                                <button type="button" class="info-tooltip" data-toggle="tooltip" data-placement="right" title="Este valor solo se verá reflejado en los informes que obtienen los directivos.">
                                    <i class="fa fa-info"></i>
                                </button>    
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="periodo" required <?=$disabledPermiso;?>>
                                    <option value="">Seleccione una opción</option>
                                    <?php
                                    $p = 1;
                                    $pFinal = $config['conf_periodos_maximos'] + 1;
                                    while($p <= $pFinal){
                                        $label = 'Periodo '.$p;
                                        if($p == $pFinal) {
                                            $label = 'AÑO FINALIZADO';
                                        }

                                        if($p == $datosConfiguracion['conf_periodo'])
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
                        <?php $botones = new botonesGuardar("dev-instituciones.php",Modulos::validarPermisoEdicion() || $datosUsuarioActual['uss_tipo'] == TIPO_DEV); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>