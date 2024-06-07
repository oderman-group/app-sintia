<?php
include("session.php");
$idPaginaInterna = 'DT0014';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Inscripciones.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$cfg=Inscripciones::configuracionAdmisiones($conexion,$baseDatosAdmisiones,$config['conf_id_institucion'],$_SESSION["bd"]);

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
?>

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<!-- dropzone -->
    <link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
    <!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
    <!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?> de admisiones</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li class="active"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?> de admisiones</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
                        <div class="col-sm-12">
                                
                            <div class="panel">
                                <header class="panel-heading panel-heading-purple"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?> </header>
                                <div class="panel-body">

									<form name="formularioGuardar" action="configuracion-admisiones-guardar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?=$cfg['cfgi_id'];?>">
                                        <input type="hidden" name="cfgi_politicas_adjunto" value="<?=$cfg['cfgi_politicas_adjunto'];?>">
                                        <input type="hidden" name="cfgi_banner_inicial" value="<?=$cfg['cfgi_banner_inicial'];?>">

                                        <p class="h3">General</p>

                                        <div class="form-group row">
											<label class="col-sm-2 control-label">Año Actual</label>
											<div class="col-sm-8">
												<input type="text" name="agno" class="form-control col-sm-2" value="<?=$cfg['cfgi_year'];?>" readonly <?=$disabledPermiso;?>>
											</div>
										</div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Año Para Inscripciones <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Este será el año lectivo en el que quedaran inscritos los nuevos estudiantes."><i class="fa fa-question"></i></button></label>
                                            <div class="col-sm-8">
                                                <input type="number" name="yearInscripcion" class="form-control col-sm-2" value="<?=$cfg['cfgi_year_inscripcion'];?>" <?=$disabledPermiso;?>>
                                            </div>
                                        </div>

                                        <div class="form-group row">
											<label class="col-sm-2 control-label">Valor de la inscripción</label>
											<div class="col-sm-8">
												<input type="number" name="valorInscripcion" class="form-control col-sm-2" value="<?=$cfg['cfgi_valor_inscripcion'];?>" <?=$disabledPermiso;?>>
											</div>
										</div>

                                        <p class="h3">Estilos y apariencia</p>

										<div class="form-group row">
											<label class="col-sm-2 control-label">Color del fondo<span style="color: red;">(*)</span> <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Este será el color principal que verán los usuarios el modulo de inscripciones. (En la barra superior, botones, etc.)"><i class="fa fa-question"></i></button></label>
											<div class="col-sm-10">
												<input type="color" style="margin-top: 20px;" name="colorFondo" class="col-sm-1" value="<?=$cfg['cfgi_color_barra_superior'];?>" <?=$disabledPermiso;?>>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="col-sm-2 control-label">Color del texto<span style="color: red;">(*)</span> <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Este será el color de los textos sobre el color principal. Asegurese de escoger un color que combine y que no se pueda distinguir del color principal."><i class="fa fa-question"></i></button></label>
											<div class="col-sm-10">
												<input type="color" style="margin-top: 20px;" name="colorTexto" class="col-sm-1" value="<?=$cfg['cfgi_color_texto'];?>" <?=$disabledPermiso;?>>
											</div>
										</div>

                                        <p class="h3">Otras</p>

										<div class="form-group row">
											<label class="col-sm-2 control-label">Habilitar proceso de inscripción</label>
											<div class="col-sm-2">
                                                <select class="form-control  select2" name="habilitarInscripcion" <?=$disabledPermiso;?>>
                                                    <option value="1" <?php if($cfg['cfgi_inscripciones_activas']==1){ echo "selected";} ?>>SI</option>
                                                    <option value="0" <?php if($cfg['cfgi_inscripciones_activas']==0){ echo "selected";} ?>>NO</option>
                                                </select>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="col-sm-2 control-label">Habilitar botón de pagar prematricula <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Cuando el estudiante esté en estado aprobado le aparecerá el botón de pago de prematricula, si usted desea."><i class="fa fa-question"></i></button></label>
											<div class="col-sm-2">
                                                <select class="form-control  select2" name="habilitarPagoPrematricula" <?=$disabledPermiso;?>>
                                                    <option value="1" <?php if($cfg['cfgi_activar_boton_pagar_prematricula']==1){ echo "selected";} ?>>SI</option>
                                                    <option value="0" <?php if($cfg['cfgi_activar_boton_pagar_prematricula']==0){ echo "selected";} ?>>NO</option>
                                                </select>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="col-sm-2 control-label">Link del botón de pagar prematricula <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Es link de alguna entidad externa o pasarela de pagos donde harán el pago de la prematricula."><i class="fa fa-question"></i></button></label>
											<div class="col-sm-8">
												<input type="url" name="linkPagoPrematricula" class="form-control col-sm-8" value="<?=$cfg['cfgi_link_boton_pagar_prematricula'];?>" <?=$disabledPermiso;?>>
											</div>
										</div>
										
                                        <div class="form-group row">
											<label class="col-sm-2 control-label">Banner inicial <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Si usted desea, puede subir un banner resaltando o mostrando alguna aviso que quiere que vean los usuarios cuando vayan a hacer el proceso de inscripción."><i class="fa fa-question"></i></button></label>
											<div class="col-sm-8">
												<input type="file" name="bannerInicio" class="form-control col-sm-6" <?=$disabledPermiso;?>>
                                                <?php
                                                    if(!empty($cfg['cfgi_banner_inicial']) && file_exists('../files/imagenes-generales/'.$cfg['cfgi_banner_inicial'])){
                                                        $activa=empty($cfg['cfgi_mostrar_banner']) ?"0":"1"; $check=($activa=="0")?"":"checked";
                                                ?>
                                                    <div class="input-group col-sm-6">
                                                        <div style="padding:10px; width: 70%">
                                                            <img src="../files/imagenes-generales/<?=$cfg['cfgi_banner_inicial'];?>" width="100%" />
                                                        </div>
                                                        <div class="input-group spinner" style="width: 30%; align-items: center;">
                                                            <label class="switchToggle">
                                                                <input type="checkbox" name="mostrarBanner" <?=$check?>>
                                                                <span class="slider red round"></span>
                                                            </label>
                                                            <label class="col-sm-2 control-label">Mostrar Banner?</label>
                                                        </div>
                                                    </div>
                                                <?php }?>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-sm-2 control-label">Texto informativo inicial <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Es un texto informativo que verán los usuarios al momento de hacer la inscripción."><i class="fa fa-question"></i></button></label>
											<div class="col-sm-10">
                                                <textarea cols="80" id="editor1" name="textoInicial" rows="10" <?=$disabledPermiso;?>><?=$cfg['cfgi_texto_inicial'];?></textarea>
											</div>
										</div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Texto para datos de cuenta <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title='Un texto con nombre del banco y numero de cuenta, este texto lo vera el usuario al consultar el estado y estar el proceso en "VERIFICACIÓN DE PAGO".'><i class="fa fa-question"></i></button></label>
                                            <div class="col-sm-10">
                                                <textarea cols="80" id="editor3" name="datosCuenta" rows="10" <?=$disabledPermiso;?>><?=$cfg['cfgi_texto_info_cuenta'];?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
											<label class="col-sm-2 control-label">Archivo sobre las políticas <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Este archivo lo podrán descargar los usuarios antes de enviar el formulario de inscripción."><i class="fa fa-question"></i></button></label>
											<div class="col-sm-8">
												<input type="file" name="politicasArchivo" class="form-control col-sm-6" <?=$disabledPermiso;?>>
                                                <?php if(!empty($cfg['cfgi_politicas_adjunto']) && file_exists('../files/imagenes-generales/'.$cfg['cfgi_politicas_adjunto'])){?>
                                                    <div style="padding:10px;">
                                                        <a href="../files/imagenes-generales/<?=$cfg['cfgi_politicas_adjunto'];?>" target="_blank"><?=$cfg['cfgi_politicas_adjunto'];?></a>
                                                        <a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Va a eliminar este archivo. Esta acción es irreversible. Desea continuar?','question','configuracion-eliminar-politicas.php?id=<?=base64_encode($cfg['cfgi_id']);?>&archivo=<?=base64_encode($cfg['cfgi_politicas_adjunto']);?>')" style="margin-left:20px;" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="right" title="Eliminar archivo de politicas.">X</a>
                                                    </div>
                                                <?php }?>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="col-sm-2 control-label">Texto sobre las Políticas <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Este texto lo podrán leer los usuarios antes de enviar el formulario de inscripción."><i class="fa fa-question"></i></button></label>
											<div class="col-sm-10">
                                                <textarea cols="80" id="editor2" name="politicas" rows="10" <?=$disabledPermiso;?>><?=$cfg['cfgi_politicas_texto'];?></textarea>
											</div>
										</div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Que mostrar en politicas?<button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Escoge si deseas mostrar el documento adjunto o el texto."><i class="fa fa-question"></i></button></label>
                                            <div class="col-sm-2">
                                                <select class="form-control  select2" name="mostrarPoliticas" <?=$disabledPermiso;?>>
                                                    <option value="1" <?php if($cfg['cfgi_mostrar_politicas']==1){ echo "selected";} ?>>Archivo sobre politicas</option>
                                                    <option value="2" <?php if($cfg['cfgi_mostrar_politicas']==2){ echo "selected";} ?>>Texto sobre politicas</option>
                                                </select>
                                            </div>
                                        </div>

                                        <?php $botones = new botonesGuardar("",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
    <script src="../ckeditor/ckeditor.js"></script>

    <script>
        // Replace the <textarea id="editor1"> with a CKEditor 4
        // instance, using default configuration.
        CKEDITOR.replace( 'editor1' );
        CKEDITOR.replace( 'editor2' );
        CKEDITOR.replace( 'editor3' );
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>