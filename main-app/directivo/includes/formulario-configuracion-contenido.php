<?php
require_once(ROOT_PATH."/main-app/class/categoriasNotas.php");

$year = $_SESSION["bd"];
if (!empty($_GET['year'])) {
    $year = base64_decode($_GET['year']);
}
$id = $_SESSION["idInstitucion"];
if (!empty($_GET['id'])) {
    $id = base64_decode($_GET['id']);
}
try{
    $consultaConfiguracion = mysqli_query($conexion, "SELECT configuracion.*, ins_siglas, ins_years FROM " . $baseDatosServicios . ".configuracion 
    INNER JOIN " . $baseDatosServicios . ".instituciones ON ins_id=conf_id_institucion
    WHERE conf_id_institucion='" . $id . "' AND conf_agno='" . $year . "'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$datosConfiguracion = mysqli_fetch_array($consultaConfiguracion, MYSQLI_BOTH);

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion() && $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO){
	$disabledPermiso = "disabled";
}

$configDEV =0;
$institucion ='';
if($idPaginaInterna == 'DV0032'){ $configDEV =1; $institucion = "de <b>".$datosConfiguracion['ins_siglas']."</b> (". $year .")"; }
?>
<div class="page-bar">
    <div class="page-title-breadcrumb">
        <div class=" pull-left">
            <div class="page-title"><?= $frases[17][$datosUsuarioActual['uss_idioma']]; ?> del Sistema <?=$institucion?></div>
            <?php include("../compartido/texto-manual-ayuda.php"); ?>
        </div>
        <ol class="breadcrumb page-breadcrumb pull-right">
            <?php
                if($idPaginaInterna == 'DV0032'){
                    echo '<li><a class="parent-item" href="javascript:void(0);" name="dev-instituciones.php" onClick="deseaRegresar(this)">Insituciones</a>&nbsp;<i class="fa fa-angle-right"></i></li>';
                }
            ?>
            <li class="active"><?= $frases[17][$datosUsuarioActual['uss_idioma']]; ?> del Sistema <?=$institucion?></li>
        </ol>
    </div>
</div>
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
            <header class="panel-heading panel-heading-purple"><?= $frases[17][$datosUsuarioActual['uss_idioma']]; ?> <?=$institucion?></header>
            <div class="panel-body">
                <form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
                    <input type="hidden" name="configDEV" value="<?= $configDEV; ?>">
                    <input type="hidden" name="id" value="<?= $datosConfiguracion['conf_id']; ?>">

                    <p class="h3">General</p>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Año Actual</label>
                        <div class="col-sm-8">
                            <input type="text" name="agno" class="form-control col-sm-2" value="<?=$year;?>" readonly <?=$disabledPermiso;?>>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">
                            Periodos a trabajar <span style="color: red;">(*)</span> 
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Las instituciones normalmente manejan 4 periodos. Los colegios semestralizados o de bachillerato acelerado manejan 2 periodos."><i class="fa fa-question"></i></button>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" name="periodoTrabajar" class="form-control col-sm-2" value="<?=$datosConfiguracion['conf_periodos_maximos'];?>" required pattern="[0-9]+" <?=$disabledPermiso;?>>
                        </div>
                    </div>



                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Periodo Actual <span style="color: red;">(*)</span>
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Este valor solo se verá reflejado en los informes que obtienen los directivos."><i class="fa fa-question"></i></button>    
                        </label>
                        <div class="col-sm-3">
                            <select class="form-control  select2" name="periodo" required <?=$disabledPermiso;?>>
                                <option value="">Seleccione una opción</option>
                                <?php
                                $p = 1;
                                $pFinal = $config['conf_periodos_maximos'] + 1;
                                while($p <= $pFinal){
                                    $label = 'Periodo '.$p;
                                    if($p == $pFinal) {
                                        $label = 'AÑO FINALIZADO';
                                    }

                                    if($p==$datosConfiguracion['conf_periodo'])
                                        echo '<option value="'.$p.'" selected>'.$label.'</option>';
                                    else
                                        echo '<option value="'.$p.'">'.$label.'</option>';	
                                    $p++;
                                }
                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Rango de las notas (Desde - Hasta) <span style="color: red;">(*)</span></label>
                        <div class="col-sm-10">
                            <input type="text"style="margin-top: 20px;" name="desde" class="col-sm-1" value="<?=$datosConfiguracion['conf_nota_desde'];?>" <?=$disabledPermiso;?>>
                            <input type="text"style="margin-top: 20px;" name="hasta" class="col-sm-1" value="<?=$datosConfiguracion['conf_nota_hasta'];?>" <?=$disabledPermiso;?>>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Nota minima para aprobar <span style="color: red;">(*)</span></label>
                        <div class="col-sm-2">
                            <input type="text" name="notaMinima" class="form-control" value="<?=$datosConfiguracion['conf_nota_minima_aprobar'];?>" <?=$disabledPermiso;?>>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Asignar porcentaje a las asignaturas? 
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Indica si las asignaturas tendrán un porcentaje diferente dentro del área al momento de calcular las notas en el boletín."><i class="fa fa-question"></i></button> 
                        </label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="porcenAsigna" <?=$disabledPermiso;?>>
                                <option value="SI" <?php if($datosConfiguracion['conf_agregar_porcentaje_asignaturas']=='SI'){ echo "selected";} ?>>SI</option>
                                <option value="NO" <?php if($datosConfiguracion['conf_agregar_porcentaje_asignaturas']=='NO'){ echo "selected";} ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <p class="h3">Estilos y apariencia</p>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Estilo de calificación <span style="color: red;">(*)</span></label>
                        <div class="col-sm-4">
                            <select class="form-control  select2" name="estiloNotas" required <?=$disabledPermiso;?>>
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

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Cantidad de decimales en las notas <span style="color: red;">(*)</span>
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Indica cuántos decimales aparecerán en los cálculos de las notas."><i class="fa fa-question"></i></button> 
                        </label>
                        <div class="col-sm-2">
                            <input type="number" name="decimalesNotas" class="form-control" value="<?=$datosConfiguracion['conf_decimales_notas'];?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Estilo de certificado</label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" id="tipoCertificado" name="certificado" onchange="cambiarTipo()" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_certificado']==1){ echo "selected";} ?>>Certificado 1</option>
                                <option value="2" <?php if($datosConfiguracion['conf_certificado']==2){ echo "selected";} ?>>Certificado 2</option>
                                <option value="3" <?php if($datosConfiguracion['conf_certificado']==3){ echo "selected";} ?>>Certificado 3</option>
                            </select>
                        </div>
                        <button type="button" titlee="Ver formato certificado" class="btn btn-sm" data-toggle="popover" ><i class="fa fa-eye"></i></button>
                        <script>
                                $(document).ready(function(){
                                $('[data-toggle="popover"]').popover({
                                    html: true, // Habilitar contenido HTML
                                    content: function () {
                                        valor = document.getElementById("tipoCertificado");
                                    return '<div id="myPopover" class="popover-content"><label id="lbl_tipo">Estilo Certificado '+valor.value+'</label>'+
                                    '<img id="img-boletin" src="../files/images/certificados/tipo'+valor.value+'.png" class="w-100" />'+                                                       
                                    '</div>';}
                                    });                                                    
                                });
                                function cambiarTipo(){  
                                    var imagen_boletin = document.getElementById('img-boletin'); 
                                    if(imagen_boletin){                                                     
                                    var valor = document.getElementById("tipoCertificado");  
                                    var lbl_tipo = document.getElementById('lbl_tipo');
                                    imagen_boletin.src ="../files/images/certificados/tipo"+valor.value+".png";
                                    lbl_tipo.textContent='Estilo Certificado '+valor.value;
                                    }
                                }
                        </script>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Estampilla de pago en certificados
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta opción permite agregar un referente o estampilla de pago a los certificados."><i class="fa fa-question"></i></button> 
                        </label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="estampilla" <?=$disabledPermiso;?>>
                                <option value="<?=SI?>" <?php if($datosConfiguracion['conf_estampilla_certificados'] == SI){ echo "selected";} ?>>SI</option>
                                <option value="<?=NO?>" <?php if($datosConfiguracion['conf_estampilla_certificados'] == NO){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Estilo de Libro Final</label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" id="tipoLibroFinal" name="libroFinal" onchange="cambiarTipoLibro()" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_libro_final']==1){ echo "selected";} ?>>Formato libro final 1</option>
                                <option value="2" <?php if($datosConfiguracion['conf_libro_final']==2){ echo "selected";} ?>>Formato libro final 2</option>
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
                        <label class="col-sm-2 control-label">Color de las notas (Perdidas -  Ganadas) <span style="color: red;">(*)</span></label>
                        <div class="col-sm-10">
                            <input type="color"style="margin-top: 20px;" name="perdida" class="col-sm-1" value="<?=$datosConfiguracion['conf_color_perdida'];?>" <?=$disabledPermiso;?>>
                            <input type="color"style="margin-top: 20px;" name="ganada" class="col-sm-1" value="<?=$datosConfiguracion['conf_color_ganada'];?>" <?=$disabledPermiso;?>>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">En qué orden desea ver el nombre de los estudiantes?</label>
                        <div class="col-sm-4">
                            <select class="form-control  select2" name="ordenEstudiantes" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_orden_nombre_estudiantes']==1){ echo "selected";} ?>>Nombres y Apellidos (Andres David Arias Pertuz)</option>
                                <option value="2" <?php if($datosConfiguracion['conf_orden_nombre_estudiantes']==2){ echo "selected";} ?>>Apellidos y nombres (Arias Pertuz Andres David)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Numero de registros en listados
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta opción permite escoger la cantidad de registros que desea que se listen al entrar, por ejemplo, a matrículas, cargas académicas o usuarios."><i class="fa fa-question"></i></button> 
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control col-sm-2 select2" name="numRegistros" <?=$disabledPermiso;?>>
                                <option value="20" <?php if($datosConfiguracion['conf_num_registros']==20){ echo "selected";} ?>>20</option>
                                <option value="30" <?php if($datosConfiguracion['conf_num_registros']==30){ echo "selected";} ?>>30</option>
                                <option value="50" <?php if($datosConfiguracion['conf_num_registros']==50){ echo "selected";} ?>>50</option>
                                <option value="100" <?php if($datosConfiguracion['conf_num_registros']==100){ echo "selected";} ?>>100</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Medidas del Logo en los informes (Ancho -  Alto)</label>
                        <div class="col-sm-10">
                            <input type="text"style="margin-top: 20px;" name="logoAncho" class="col-sm-1" value="<?=$datosConfiguracion['conf_ancho_imagen'];?>" <?=$disabledPermiso;?>>
                            <input type="text"style="margin-top: 20px;" name="logoAlto" class="col-sm-1" value="<?=$datosConfiguracion['conf_alto_imagen'];?>" <?=$disabledPermiso;?>>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Mostrar Nombre del colegio en el encabezado de los informes</label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="mostrarNombre" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_mostrar_nombre']==1){ echo "selected";} ?>>SI</option>
                                <option value="2" <?php if($datosConfiguracion['conf_mostrar_nombre']==2){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <?php if (array_key_exists(23, $arregloModulos) && Modulos::validarModulosActivos($conexion, 23)) { ?>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Forma para mostrar las notas <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta opción mostrará a los usuarios las notas en formato numérico o con frases de desempeño que corresponden a las notas numéricas, dependiendo la opción que seleccione."><i class="fa fa-question"></i></button> </label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="formaNotas" <?=$disabledPermiso;?>>
                                <option value="<?=CUALITATIVA?>" <?php if($datosConfiguracion['conf_forma_mostrar_notas'] == CUALITATIVA){ echo "selected";} ?>>CUALITATIVA (sin numéros)</option>
                                <option value="<?=CUANTITATIVA?>" <?php if($datosConfiguracion['conf_forma_mostrar_notas'] == CUANTITATIVA){ echo "selected";} ?>>CUANTITATIVA (con números)</option>
                            </select>
                        </div>
                    </div>
                    <?php }?>


                    <p class="h3">Permisos</p>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Mostrar calificaciones a los acudientes?</label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="caliAcudientes" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_calificaciones_acudientes']==1){ echo "selected";} ?>>SI</option>
                                <option value="0" <?php if($datosConfiguracion['conf_calificaciones_acudientes']==0){ echo "selected";} ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Mostrar calificaciones a los estudiantes?</label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="caliEstudiantes" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_mostrar_calificaciones_estudiantes']==1){ echo "selected";} ?>>SI</option>
                                <option value="0" <?php if($datosConfiguracion['conf_mostrar_calificaciones_estudiantes']==0){ echo "selected";} ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Permiso de actualizar las definitivas en consolidado final?
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite o no actualizar las definitivas, en el consolidado final, en cualquier momento."><i class="fa fa-question"></i></button> 
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control col-sm-2 select2" name="permisoConsolidado" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_editar_definitivas_consolidado']==1){ echo "selected";} ?>>SI</option>
                                <option value="0" <?php if($datosConfiguracion['conf_editar_definitivas_consolidado']==0){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Observaciones multiples en comportamiento?</label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="observacionesMultiples" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_observaciones_multiples_comportamiento']==1){ echo "selected";} ?>>SI</option>

                                <option value="0" <?php if($datosConfiguracion['conf_observaciones_multiples_comportamiento'] == 0 || $datosConfiguracion['conf_observaciones_multiples_comportamiento'] == null){ echo "selected";} ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Permitir descargar informe parcial a acudientes</label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="informeParcial" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_informe_parcial']==1){ echo "selected";} ?>>SI</option>
                                <option value="0" <?php if($datosConfiguracion['conf_informe_parcial']==0){ echo "selected";} ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Generar informes con estudiantes con menos de 100%?</label>
                        <div class="col-sm-4">
                            <select class="form-control  select2" name="generarInforme" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_porcentaje_completo_generar_informe']==1){ echo "selected";} ?>>No generar informe hasta que todos estén al 100%</option>
                                <option value="2" <?php if($datosConfiguracion['conf_porcentaje_completo_generar_informe']==2){ echo "selected";} ?>>Omitir los estudiantes que no tengan el 100%</option>
                                <option value="3" <?php if($datosConfiguracion['conf_porcentaje_completo_generar_informe']==3){ echo "selected";} ?>>Registrar la definitiva con el porcentaje actual</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Permitir a acudientes descargar el boletín?
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite a los acudientes descargar el boletín de sus acudidos."><i class="fa fa-question"></i></button> 
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control col-sm-2 select2" name="descargarBoletin" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_permiso_descargar_boletin']==1){ echo "selected";} ?>>SI</option>
                                <option value="0" <?php if($datosConfiguracion['conf_permiso_descargar_boletin']==0){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Permitir a docentes ver puestos en el informe de sábanas?
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite o no a los docentes ver el listado de los puestos de los estudiantes, por periodo, en el informe de sábanas."><i class="fa fa-question"></i></button> 
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control col-sm-2 select2" name="permisoDocentesPuestosSabanas" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_ver_promedios_sabanas_docentes']==1){ echo "selected";} ?>>SI</option>
                                <option value="0" <?php if($datosConfiguracion['conf_ver_promedios_sabanas_docentes']==0){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Activar encuesta de reserva de cupos?
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite a los acudientes responder si desean reservar o no el cupo para sus acudidos para el siguiente año."><i class="fa fa-question"></i></button> 
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control col-sm-2 select2" name="activarEncuestaReservaCupo" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_activar_encuesta']==1){ echo "selected";} ?>>SI</option>
                                <option value="0" <?php if($datosConfiguracion['conf_activar_encuesta']==0){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Permitir a estudiantes cambiar su clave</label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="cambiarClaveEstudiantes">
                                <option value="SI" <?php if ($datosConfiguracion['conf_cambiar_clave_estudiantes'] == 'SI') {
                                                        echo "selected";
                                                    } ?>>SI</option>
                                <option value="NO" <?php if ($datosConfiguracion['conf_cambiar_clave_estudiantes'] == 'NO') {
                                                        echo "selected";
                                                    } ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DEV){ ?>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Permitir cambiar el usuario de acceso</label>
                            <div class="col-sm-2">
                                <select class="form-control  select2" name="cambiarNombreUsuario">
                                    <option value="SI" <?php if ($datosConfiguracion['conf_cambiar_nombre_usuario'] == 'SI') {
                                                            echo "selected";
                                                        } ?>>SI</option>
                                    <option value="NO" <?php if ($datosConfiguracion['conf_cambiar_nombre_usuario'] == 'NO') {
                                                            echo "selected";
                                                        } ?>>NO</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Editar información en años anteriores?
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite a los directivos editar registros en años anteriores al actual."><i class="fa fa-question"></i></button></label>
                            <div class="col-sm-8">
                                <select class="form-control col-sm-2 select2" name="editarInfoYears">
                                    <option value="1" <?php if($datosConfiguracion['conf_permiso_edicion_years_anteriores']==1){ echo "selected";} ?>>SI</option>
                                    <option value="0" <?php if($datosConfiguracion['conf_permiso_edicion_years_anteriores']==0){ echo "selected";} ?>>NO</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Doble buscador en listado de registros?
                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite que las instituciones tengan doble buscador en paginas donde se listan los registros."><i class="fa fa-question"></i></button></label>
                            <div class="col-sm-2">
                                <select class="form-control  select2" name="dobleBuscador">
                                    <option value="SI" <?php if ($datosConfiguracion['conf_doble_buscador'] == 'SI') {
                                                            echo "selected";
                                                        } ?>>SI</option>
                                    <option value="NO" <?php if ($datosConfiguracion['conf_doble_buscador'] == 'NO') {
                                                            echo "selected";
                                                        } ?>>NO</option>
                                </select>
                            </div>
                        </div>
                    <?php }else{ ?>
                        <input type="hidden" name="cambiarNombreUsuario" value="<?= $datosConfiguracion['conf_cambiar_nombre_usuario']; ?>">

                        <input type="hidden" name="editarInfoYears" value="<?= $datosConfiguracion['conf_permiso_edicion_years_anteriores']; ?>">

                        <input type="hidden" name="dobleBuscador" value="<?= $datosConfiguracion['conf_doble_buscador']; ?>">
                    <?php } ?>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Mostrar el encabezado en los informes?
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite ver el encabezado general de los informes o solo el logo."><i class="fa fa-question"></i></button></label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="mostrarEncabezadoInformes">
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
                        <label class="col-sm-2 control-label">Mostrar paso a paso de matricula?
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite ver a los estudiantes el paso a paso del proceso de matricula."><i class="fa fa-question"></i></button></label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="pasosMatricula">
                                <option value="1" <?php if ($datosConfiguracion['conf_mostrar_pasos_matricula'] == 1) {
                                                        echo "selected";
                                                    } ?>>SI</option>
                                <option value="0" <?php if ($datosConfiguracion['conf_mostrar_pasos_matricula'] == 0) {
                                                        echo "selected";
                                                    } ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Calcular notas en reporte de sabana por indicadores?
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta acción permite calcular en el reporte de sabanas, las notas por indicador o no."><i class="fa fa-question"></i></button></label>
                        <div class="col-sm-2">
                            <select class="form-control  select2" name="notasReporteSabanas">
                                <option value="1" <?php if ($datosConfiguracion['conf_reporte_sabanas_nota_indocador'] == 1) {
                                                        echo "selected";
                                                    } ?>>SI</option>
                                <option value="0" <?php if ($datosConfiguracion['conf_reporte_sabanas_nota_indocador'] == 0) {
                                                        echo "selected";
                                                    } ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <p class="h3">Otras</p>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Fecha que aparecerá en el proximo Informe Parcial</label>
                        <div class="col-sm-2">
                            <input type="text" name="fechapa" class="form-control" value="<?=$datosConfiguracion['conf_fecha_parcial'];?>" <?=$disabledPermiso;?>>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Texto de arriba del informe parcial</label>
                        <div class="col-sm-10">
                            <textarea cols="80" id="editor1" name="descrip" rows="10" <?=$disabledPermiso;?>><?=$datosConfiguracion['conf_descripcion_parcial'];?></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">Mostrar campo para firma del estudiante en reporte de asistencia?
                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta opción permite dar un espacio para que el estudiante firme en el reporte de asistencia a la entrega de informes."><i class="fa fa-question"></i></button> 
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control col-sm-2 select2" name="firmaEstudiante" <?=$disabledPermiso;?>>
                                <option value="1" <?php if($datosConfiguracion['conf_firma_estudiante_informe_asistencia']==1){ echo "selected";} ?>>SI</option>
                                <option value="0" <?php if($datosConfiguracion['conf_firma_estudiante_informe_asistencia']==0){ echo "selected";} ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <?php $botones = new botonesGuardar("dev-instituciones.php",Modulos::validarPermisoEdicion() || $datosUsuarioActual['uss_tipo'] == TIPO_DEV); ?>
                </form>
            </div>
        </div>
    </div>
</div>