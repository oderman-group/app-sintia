
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
            <header class="panel-heading panel-heading-purple">Comportamiento del sistema</header>
            <div class="panel-body">
                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
                    <input type="hidden" name="configDEV" value="<?= $configDEV; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id']; ?>">
                    <input type="hidden" name="configTab" value="<?=BDT_Configuracion::CONFIG_SISTEMA_COMPORTAMIENTO;?>">

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">
                            Periodos a trabajar <span style="color: red;">(*)</span> 
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Las instituciones normalmente manejan 4 periodos. Los colegios semestralizados o de bachillerato acelerado manejan 2 periodos."><i class="fa fa-info"></i></button>
                        </label>
                        <div class="col-sm-8">
                            <input 
                                type="number" 
                                name="periodoTrabajar" 
                                class="form-control col-sm-2" 
                                value="<?=$datosConfiguracion['conf_periodos_maximos'];?>" 
                                required 
                                pattern="[0-9]+" 
                                <?php 
                                if(!empty($disabledPermiso)) 
                                    echo $disabledPermiso; 
                                else 
                                    echo $disabledCamposConfiguracion;
                                ?>
                            >
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Rango de las notas (Desde - Hasta) <span style="color: red;">(*)</span></label>
                        <div class="col-sm-9">
                            <input 
                                type="number" 
                                style="margin-top: 20px;" 
                                name="desde" 
                                class="form-control col-sm-2" 
                                value="<?=$datosConfiguracion['conf_nota_desde'];?>" 
                                <?php 
                                if(!empty($disabledPermiso)) 
                                    echo $disabledPermiso; 
                                else 
                                    echo $disabledCamposConfiguracion;
                                ?>
                            >
                            <input 
                                type="number" 
                                style="margin-top: 20px;" 
                                name="hasta" 
                                class="form-control col-sm-2" 
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

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Nota minima para aprobar <span style="color: red;">(*)</span></label>
                        <div class="col-sm-2">
                            <input 
                                type="text" 
                                name="notaMinima" 
                                class="form-control" 
                                value="<?=$datosConfiguracion['conf_nota_minima_aprobar'];?>" 
                                <?php 
                                if(!empty($disabledPermiso)) 
                                    echo $disabledPermiso; 
                                else 
                                    echo $disabledCamposConfiguracion;
                                ?>
                            >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Cantidad de decimales en las notas <span style="color: red;">(*)</span>
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Indica cuántos decimales aparecerán en los cálculos de las notas."><i class="fa fa-info"></i></button> 
                        </label>
                        <div class="col-sm-2">
                            <input type="number" name="decimalesNotas" class="form-control" value="<?=$datosConfiguracion['conf_decimales_notas'];?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Asignar porcentaje a las asignaturas <span style="color: red;">(*)</span>
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Indica si las asignaturas tendrán un porcentaje diferente dentro del área al momento de calcular las notas en el boletín."><i class="fa fa-info"></i></button> 
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control" name="porcenAsigna" <?=$disabledPermiso;?>>
                                <option value="SI" <?php if($datosConfiguracion['conf_agregar_porcentaje_asignaturas']=='SI'){ echo "selected";} ?>>SI</option>
                                <option value="NO" <?php if($datosConfiguracion['conf_agregar_porcentaje_asignaturas']=='NO'){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Estilo de calificación <span style="color: red;">(*)</span></label>
                        <div class="col-sm-4">
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

                    <?php if (array_key_exists(Modulos::MODULO_CUALITATIVO, $arregloModulos) && Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_CUALITATIVO)) { ?>
                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Forma para mostrar las notas <span style="color: red;">(*)</span>
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta opción mostrará a los usuarios las notas en formato numérico o con frases de desempeño que corresponden a las notas numéricas, dependiendo la opción que seleccione."><i class="fa fa-info"></i></button> </label>
                        <div class="col-sm-4">
                            <select class="form-control" name="formaNotas" <?=$disabledPermiso;?>>
                                <option value="<?=CUALITATIVA?>" <?php if($datosConfiguracion['conf_forma_mostrar_notas'] == CUALITATIVA){ echo "selected";} ?>>CUALITATIVA (sin numéros)</option>
                                <option value="<?=CUANTITATIVA?>" <?php if($datosConfiguracion['conf_forma_mostrar_notas'] == CUANTITATIVA){ echo "selected";} ?>>CUANTITATIVA (con números)</option>
                            </select>
                        </div>
                    </div>
                    <?php }?>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Generación de informes <span style="color: red;">(*)</span>
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta opción define el comportamiento a la hora de generar los informes por parte de los docentes o directivos. Escoja la configuración deseada. Esto aplica para todas las cargas académicas."><i class="fa fa-info"></i></button></label>
                        <div class="col-sm-4">
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
                                <option value="1" <?php if($datosConfiguracion['conf_porcentaje_completo_generar_informe']==1){ echo "selected";} ?>>Es necesario que todos los estudiantes tengan el 100% de sus notas registradas</option>
                                <option value="2" <?php if($datosConfiguracion['conf_porcentaje_completo_generar_informe']==2){ echo "selected";} ?>>El sistema omitirá los estudiantes que no tengan el 100% de sus notas registradas</option>
                                <option value="3" <?php if($datosConfiguracion['conf_porcentaje_completo_generar_informe']==3){ echo "selected";} ?>>Registrar la definitiva con el porcentaje que tengan en ese momento.</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Observaciones multiples en comportamiento <span style="color: red;">(*)</span>
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Permitirá a los docentes colocar o escoger solo una observación para los estudiantes que aparecerá en los boletines, o seleccionar múltiples de ellas."><i class="fa fa-info"></i></button></label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="observacionesMultiples" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_observaciones_multiples_comportamiento']==1){ echo "selected";} ?>>SI</option>

                                <option value="0" <?php if($datosConfiguracion['conf_observaciones_multiples_comportamiento'] == 0 || $datosConfiguracion['conf_observaciones_multiples_comportamiento'] == null){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Peso máximo de los archivos (MB) <span style="color: red;">(*)</span>
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Es el peso máximo, en MB, que debe tener el archivo que suben los usuarios en los diferentes lugares de la plataforma. Tenga en cuenta que entre más pese el archivo más tiempo puede tomar el proceso de carga y descarga del mismo."><i class="fa fa-info"></i></button> </label>
                        <div class="col-sm-2">
                            <input type="number" name="pesoArchivos" class="form-control" value="<?=$datosConfiguracion['conf_max_peso_archivos'];?>" <?=$disabledPermiso;?>>
                        </div>
                    </div>

                    <?php $botones = new botonesGuardar("dev-instituciones.php", Modulos::validarPermisoEdicion() || $datosUsuarioActual['uss_tipo'] == TIPO_DEV); ?>
                </form>
            </div>
        </div>
    </div>
</div>