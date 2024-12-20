
<div class="row">

    <div class="col-sm-12">
        <?php
        include("../../config-general/mensajes-informativos.php");
        if($idPaginaInterna == 'DV0032'){
            include("includes/barra-superior-dev-instituciones-configuracion-informacion.php");
        }
        ?>
        <br>
        <div class="panel">
            <header class="panel-heading panel-heading-purple">Preferencias</header>
            <div class="panel-body">
                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
                    <input type="hidden" name="configDEV" value="<?= $configDEV; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id']; ?>">
                    <input type="hidden" name="configTab" value="<?=BDT_Configuracion::CONFIG_SISTEMA_PREFERENCIAS;?>">

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">¿En qué orden desea ver el nombre de los estudiantes? <span style="color: red;">(*)</span></label>
                        <div class="col-sm-4">
                            <select class="form-control" name="ordenEstudiantes" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_orden_nombre_estudiantes']==1){ echo "selected";} ?>>Nombres y Apellidos (Andres David Arias Pertuz)</option>
                                <option value="2" <?php if($datosConfiguracion['conf_orden_nombre_estudiantes']==2){ echo "selected";} ?>>Apellidos y nombres (Arias Pertuz Andres David)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Número de registros por cada página <span style="color: red;">(*)</span>
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta opción permite escoger la cantidad de registros que desea que se listen al entrar, por ejemplo, a matrículas, cargas académicas o usuarios."><i class="fa fa-info"></i></button> 
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control col-sm-2" name="numRegistros" <?=$disabledPermiso;?>>
                                <option value="20" <?php if($datosConfiguracion['conf_num_registros']==20){ echo "selected";} ?>>20</option>
                                <option value="30" <?php if($datosConfiguracion['conf_num_registros']==30){ echo "selected";} ?>>30</option>
                                <option value="50" <?php if($datosConfiguracion['conf_num_registros']==50){ echo "selected";} ?>>50</option>
                                <option value="100" <?php if($datosConfiguracion['conf_num_registros']==100){ echo "selected";} ?>>100</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                            <label class="col-sm-3 control-label">Mostrar estudiantes cancelados en los informes <span style="color: red;">(*)</span></label>
                            <div class="col-sm-8">
                                <select class="form-control col-sm-2 select2" name="mostrarEstudiantesCancelados" <?=$disabledPermiso;?>>
                                    <option value="SI" <?php if($datosConfiguracion['conf_mostrar_estudiantes_cancelados'] == 'SI'){ echo "selected";} ?>>SI</option>
                                    <option value="NO" <?php if($datosConfiguracion['conf_mostrar_estudiantes_cancelados'] == 'NO'){ echo "selected";} ?>>NO</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Mostrar nota y desempeño en el panel lateral a los estudiantes y acudientes <span style="color: red;">(*)</span></label>
                            <div class="col-sm-8">
                                <select class="form-control col-sm-2 select2" name="mostrarNotasPanelLateral" <?=$disabledPermiso;?>>
                                    <option value="1" <?php if($datosConfiguracion['conf_ocultar_panel_lateral_notas_estudiantes'] == 1){ echo "selected";} ?>>SI</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_ocultar_panel_lateral_notas_estudiantes'] == '0'){ echo "selected";} ?>>NO</option>
                                </select>
                            </div>
                        </div>


                    <?php $botones = new botonesGuardar("dev-instituciones.php",Modulos::validarPermisoEdicion() || $datosUsuarioActual['uss_tipo'] == TIPO_DEV); ?>
                </form>
            </div>
        </div>
    </div>
</div>