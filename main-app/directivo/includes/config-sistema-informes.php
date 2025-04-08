
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
            <header class="panel-heading panel-heading-purple">Informes y reportes</header>
            <div class="panel-body">
                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
                    <input type="hidden" name="configDEV" value="<?= $configDEV; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id']; ?>">
                    <input type="hidden" name="configTab" value="<?=BDT_Configuracion::CONFIG_SISTEMA_INFORMES;?>">

                    <p class="h3">Académicos</p>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Formato de boletin <span style="color: red;">(*)</span></label>
                        <div class="col-sm-2">
                            <select class="form-control" id="formatoBoletin" name="formatoBoletin" onChange="cambiarTipoBoletin()" <?=$disabledPermiso;?>>
                                <?php 
                                $consultaBoletin = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".opciones_generales WHERE ogen_grupo=15");
                                while ($datosBoletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH)) {
                                ?>
                                    <option value="<?=$datosBoletin['ogen_id']; ?>" <?php if($datosConfiguracion['conf_formato_boletin'] == $datosBoletin['ogen_id']){ echo "selected";} ?>><?=$datosBoletin['ogen_nombre'];?></option>
                                <?php }?>
                            </select>
                        </div>
                        <button type="button" title="Ver formato boletin" class="btn btn-sm" data-toggle="popover_boletin" ><i class="fa fa-eye"></i></button>
                        <script>
                                $(document).ready(function() {
                                $('[data-toggle="popover_boletin"]').popover({
                                    html: true, // Habilitar contenido HTML
                                    content: function () {
                                        valorB = document.getElementById("formatoBoletin");
                                        return '<div id="myPopoverBol" class="popover-content"><label id="lbl_tipo_bol">Estilo Boletin '+valorB.value+'</label>'+
                                        '<img id="img-boletin-true" src="../files/images/boletines/tipo'+valorB.value+'.png" class="w-100" />'+'</div>';}
                                    });
                                });
                                function cambiarTipoBoletin() {
                                    var imagen_boletin = document.getElementById('img-boletin-true'); 
                                    if (imagen_boletin) {
                                        var valor    = document.getElementById("formatoBoletin");  
                                        var lbl_tipo = document.getElementById('lbl_tipo_bol');
                                        imagen_boletin.src ="../files/images/boletines/tipo"+valor.value+".png";
                                        lbl_tipo.textContent='Estilo Boletin '+valor.value;
                                    }
                                }
                        </script>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Estilo de certificado <span style="color: red;">(*)</span></label>
                        <div class="col-sm-2">
                            <select class="form-control" id="tipoCertificado" name="certificado" onChange="cambiarTipo()" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_certificado']==1){ echo "selected";} ?>>Certificado 1</option>
                                <option value="2" <?php if($datosConfiguracion['conf_certificado']==2){ echo "selected";} ?>>Certificado 2</option>
                                <option value="3" <?php if($datosConfiguracion['conf_certificado']==3){ echo "selected";} ?>>Certificado 3</option>
                            </select>
                        </div>
                        <button type="button" title="Ver formato certificado" class="btn btn-sm" data-toggle="popover" ><i class="fa fa-eye"></i></button>
                        <script>
                                $(document).ready(function() {
                                $('[data-toggle="popover"]').popover({
                                    html: true, // Habilitar contenido HTML
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

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Estampilla de pago en certificados <span style="color: red;">(*)</span>
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta opción permite agregar un referente o estampilla de pago a los certificados."><i class="fa fa-info"></i></button> 
                        </label>
                        <div class="col-sm-2">
                            <select class="form-control" name="estampilla" <?=$disabledPermiso;?>>
                                <option value="<?=SI?>" <?php if($datosConfiguracion['conf_estampilla_certificados'] == SI){ echo "selected";} ?>>SI</option>
                                <option value="<?=NO?>" <?php if($datosConfiguracion['conf_estampilla_certificados'] == NO){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Estilo de Libro Final <span style="color: red;">(*)</span></label>
                        <div class="col-sm-2">
                            <select class="form-control" id="tipoLibroFinal" name="libroFinal" onchange="cambiarTipoLibro()" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_libro_final']==1){ echo "selected";} ?>>Formato libro final 1</option>
                                <option value="2" <?php if($datosConfiguracion['conf_libro_final']==2){ echo "selected";} ?>>Formato libro final 2</option>
                                <option value="3" <?php if($datosConfiguracion['conf_libro_final']==3){ echo "selected";} ?>>Formato libro final 3 (1 Fast)</option>
                                <option value="4" <?php if($datosConfiguracion['conf_libro_final']==4){ echo "selected";} ?>>Formato libro final 4 (2 Fast)</option>
                            </select>
                        </div>
                        <button type="button" titlee="Ver formato libro final" class="btn btn-sm" data-toggle="popover_2" ><i class="fa fa-eye"></i></button>
                        <script>
                                $(document).ready(function(){
                                $('[data-toggle="popover_2"]').popover({
                                    html: true, // Habilitar contenido HTML
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

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Promediar difinitiva a estudiantes retirados en libro final <span style="color: red;">(*)</span>
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite escoger como promediar la definitiva de los estudiantes retirados en el libro final ."><i class="fa fa-info"></i></button> 
                        </label>
                        <div class="col-sm-2">
                            <select class="form-control" name="promedioLibroFinal" <?=$disabledPermiso;?>>
                                <option value="<?=BDT_Configuracion::TODOS_PERIODOS;?>" <?php if($datosConfiguracion['conf_promedio_libro_final'] == BDT_Configuracion::TODOS_PERIODOS){ echo "selected";} ?>>POR TODOS LOS PERIODOS</option>
                                <option value="<?=BDT_Configuracion::PERIODOS_CURSADOS;?>" <?php if($datosConfiguracion['conf_promedio_libro_final'] == BDT_Configuracion::PERIODOS_CURSADOS){ echo "selected";} ?>>POR PERIODOS CURSADOS</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Medidas del Logo en los informes (Ancho -  Alto) <span style="color: red;">(*)</span></label>
                        <div class="col-sm-9">
                            <input type="number"style="margin-top: 20px;" name="logoAncho" class="col-sm-1" value="<?=$datosConfiguracion['conf_ancho_imagen'];?>" <?=$disabledPermiso;?>> PX
                            <input type="number"style="margin-top: 20px;" name="logoAlto" class="col-sm-1" value="<?=$datosConfiguracion['conf_alto_imagen'];?>" <?=$disabledPermiso;?>> PX
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Mostrar nombre del colegio en el encabezado de los informes <span style="color: red;">(*)</span></label>
                        <div class="col-sm-2">
                            <select class="form-control" name="mostrarNombre" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_mostrar_nombre']==1){ echo "selected";} ?>>SI</option>
                                <option value="2" <?php if($datosConfiguracion['conf_mostrar_nombre']==2){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Desea mostrar el encabezado completo en los informes? <span style="color: red;">(*)</span>
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite ver el encabezado general de los informes o solo el logo."><i class="fa fa-info"></i></button></label>
                        <div class="col-sm-2">
                            <select class="form-control" name="mostrarEncabezadoInformes">
                                <option value="1" <?php if ($datosConfiguracion['conf_mostrar_encabezado_informes'] == 1) {
                                                        echo "selected";
                                                    } ?>>SI</option>
                                <option value="0" <?php if ($datosConfiguracion['conf_mostrar_encabezado_informes'] == 0) {
                                                        echo "selected";
                                                    } ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Calcular notas en reporte de sabana por indicadores <span style="color: red;">(*)</span>
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite calcular en el reporte de sabanas, las notas por indicador o no."><i class="fa fa-info"></i></button></label>
                        <div class="col-sm-2">
                            <select class="form-control" name="notasReporteSabanas">
                                <option value="1" <?php if ($datosConfiguracion['conf_reporte_sabanas_nota_indocador'] == 1) {
                                                        echo "selected";
                                                    } ?>>SI</option>
                                <option value="0" <?php if ($datosConfiguracion['conf_reporte_sabanas_nota_indocador'] == 0) {
                                                        echo "selected";
                                                    } ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Fecha que aparecerá en el proximo Informe Parcial <span style="color: red;">(*)</span></label>
                        <div class="col-sm-2">
                            <input type="date" name="fechapa" class="form-control" value="<?=$datosConfiguracion['conf_fecha_parcial'];?>" <?=$disabledPermiso;?>>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Texto de encabezado del informe parcial <span style="color: red;">(*)</span></label>
                        <div class="col-sm-9">
                            <textarea cols="80" id="editor1" name="descrip" rows="10" <?=$disabledPermiso;?>><?=$datosConfiguracion['conf_descripcion_parcial'];?></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Mostrar campo para firma del estudiante en reporte de asistencia <span style="color: red;">(*)</span>
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta opción permite dar un espacio para que el estudiante firme en el reporte de asistencia a la entrega de informes."><i class="fa fa-info"></i></button> 
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control col-sm-3" name="firmaEstudiante" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_firma_estudiante_informe_asistencia']==1){ echo "selected";} ?>>SI</option>
                                <option value="0" <?php if($datosConfiguracion['conf_firma_estudiante_informe_asistencia']==0){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label">Mostrar campo para firma e inasistencia en planilla de docentes con notas<span style="color: red;">(*)</span>
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta opción permite mostrar campo para firma e inasistencia en planilla de docentes con notas."><i class="fa fa-info"></i></button> 
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control col-sm-3" name="firmaAsistencia" <?=$disabledPermiso;?>>
                                <option value="<?=SI?>" <?=$datosConfiguracion['conf_firma_inasistencia_planilla_notas_doc'] == SI ? "selected" : ""; ?> >SI</option>
                                <option value="<?=NO?>" <?=$datosConfiguracion['conf_firma_inasistencia_planilla_notas_doc'] == NO ? "selected" : ""; ?> >NO</option>
                            </select>
                        </div>
                    </div>

                    <?php $botones = new botonesGuardar("dev-instituciones.php",Modulos::validarPermisoEdicion() || $datosUsuarioActual['uss_tipo'] == TIPO_DEV); ?>
                </form>
            </div>
        </div>
    </div>
</div>