<?php
include("session.php");
require_once("../class/SubRoles.php");
require_once("../class/Paginas.php");

$idPaginaInterna = 'DV0020';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");

try{
    $consulta=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".paginas_publicidad WHERE pagp_id='".base64_decode($_GET["idP"])."'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$datosPaginas=mysqli_fetch_array($consulta, MYSQLI_BOTH);

$disabled='';
if(!Modulos::validarPaginasHijasSubRol(base64_decode($_GET["idP"]))){
    $disabled='disabled';
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

    <script type="application/javascript">
        function actualizarPagina(enviada){
            var tipoUss = document.getElementById('tipoUsuario');
            var idPagina = enviada.alt;
            var dato = enviada.value;
            if(dato!=""){
                $('#resp').empty().hide().html("Validado Dato...").show(1);

                datos = "idPagina="+(idPagina)+
                        "&dato="+(dato)+
                        "&tipoUss="+(tipoUss);
                    $.ajax({
                    type: "POST",
                    url: "ajax-dev-paginas-editar.php",
                    data: datos,
                    success: function(data){
                        $('#resp').empty().hide().html(data).show(1);
                    }

                });

            }
        }
    </script>
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
                                <div class="page-title">Editar Paginas</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="dev-paginas.php" onClick="deseaRegresar(this)"><?=$frases[403][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar Paginas</li>
                            </ol>
                        </div>
                    </div>
                    <span style="color: blue; font-size: 15px;" id="resp"></span>
                    <div class="row">
						
                        <div class="col-sm-12">
                                <?php include("../../config-general/mensajes-informativos.php"); ?>


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="dev-paginas-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="codigoPagina" class="form-control" value="<?=$datosPaginas['pagp_id'];?>">
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Codigo<span style="color: red;">(*)</span></label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control" value="<?=$datosPaginas['pagp_id'];?>" readonly>
                                            </div>
                                        </div>
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre Pagina<span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="text" name="nombrePagina" class="form-control" id="nombrePagina" value="<?=$datosPaginas['pagp_pagina'];?>" required <?=$disabled;?>>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Tipo de Usuario</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="tipoUsuario" id="tipoUsuario" <?=$disabled;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    try{
                                                        $consultaUsuarios = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_perfiles");
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
                                                    while($tipoUsuarios=mysqli_fetch_array($consultaUsuarios, MYSQLI_BOTH)){
                                                        $selected="";
                                                        if($datosPaginas['pagp_tipo_usuario']==$tipoUsuarios["pes_id"]){
                                                            $selected="selected";
                                                        }
                                                        echo'<option value="'.$tipoUsuarios["pes_id"].'" '.$selected.'>'.$tipoUsuarios["pes_nombre"].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Modulo</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="modulo" id="modulo" <?=$disabled;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    try{
                                                        $consultaModulos=mysqli_query($conexion, "SELECT mod_id, mod_nombre FROM ".$baseDatosServicios.".modulos WHERE mod_estado=1");
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
                                                    while($modulos=mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)){
                                                        $selected="";
                                                        if($datosPaginas['pagp_modulo']==$modulos["mod_id"]){
                                                            $selected="selected";
                                                        }
                                                        echo'<option value="'.$modulos["mod_id"].'" '.$selected.'>'.$modulos["mod_nombre"].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Ruta Pagina<span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="text" name="rutaPagina" class="form-control" onChange="actualizarPagina(this)" alt="<?=base64_decode($_GET["idP"]);?>" id="rutaPagina" value="<?=$datosPaginas['pagp_ruta'];?>" required <?=$disabled;?>>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Navegable?</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="navegable" id="navegable" <?=$disabled;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1" <?php if($datosPaginas['pagp_navegable']==1){ echo "selected";} ?>>SI</option>
                                                    <option value="0" <?php if($datosPaginas['pagp_navegable']==0){ echo "selected";} ?>>NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Asignable a SubRoles?</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="subroles" id="subroles">
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="<?=SI?>" <?php if($datosPaginas['pagp_asignable_subroles'] == SI){ echo "selected";} ?>>SI</option>
                                                    <option value="<?=NO?>" <?php if($datosPaginas['pagp_asignable_subroles'] == NO){ echo "selected";} ?>>NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">CRUD</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="crud" id="crud" <?=$disabled;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="CREATE" <?php if($datosPaginas['pagp_crud']=='CREATE'){ echo "selected";} ?>>CREATE</option>
                                                    <option value="READ" <?php if($datosPaginas['pagp_crud']=='READ'){ echo "selected";} ?>>READ</option>
                                                    <option value="UPDATE" <?php if($datosPaginas['pagp_crud']=='UPDATE'){ echo "selected";} ?>>UPDATE</option>
                                                    <option value="DELETE" <?php if($datosPaginas['pagp_crud']=='DELETE'){ echo "selected";} ?>>DELETE</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Pagina Padre</label>
                                            <div class="col-sm-3">
                                                <select class="form-control select2" name="paginaPadre" id="paginaPadre" <?=$disabled;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    try{
                                                        $consultaPaginas = SubRoles::listarPaginas();
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
                                                    while($pagina=mysqli_fetch_array($consultaPaginas, MYSQLI_BOTH)){
                                                        $selected="";
                                                        if($datosPaginas['pagp_pagina_padre']==$pagina["pagp_id"]){
                                                            $selected="selected";
                                                        }
                                                        echo'<option value="'.$pagina["pagp_id"].'" '.$selected.'>'.$pagina["pagp_pagina"].'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Paginas de Dependencia</label>
                                            <div class="col-sm-6">
                                                <select class="form-control  select2" multiple name="paginaDependencia[]" id="paginaDependencia">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                        $paginasBD=!empty($datosPaginas['pagp_paginas_dependencia'])?explode(',',$datosPaginas['pagp_paginas_dependencia']):"";
                                                        $filtro="AND pagp_tipo_usuario=5 AND pagp_id!='".$datosPaginas['pagp_id']."'";
                                                        $consultaPaginas = Paginas::listarPaginas($filtro);
                                                        while($pagina=mysqli_fetch_array($consultaPaginas, MYSQLI_BOTH)){
                                                            $selected=($paginasBD!="" && in_array($pagina["pagp_id"], $paginasBD))?"selected":"";
                                                            echo'<option value="'.$pagina["pagp_id"].'" '.$selected.'>['.$pagina["pagp_id"].'] '.$pagina["pagp_pagina"].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Url para youtube</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="urlYoutube" class="form-control" id="urlYoutube" value="<?=$datosPaginas['pagp_url_youtube'];?>" <?=$disabled;?>>
                                            </div>
                                        </div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Palabras Claves</label>
											<div class="col-sm-6">
                                                <textarea cols="80" id="editor1" name="palabrasClaves" rows="10" <?=$disabled;?>><?=$datosPaginas['pagp_palabras_claves'];?></textarea>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Descripción</label>
											<div class="col-sm-6">
                                                <textarea cols="80" id="editor2" name="descripcion" rows="10" <?=$disabled;?>><?=$datosPaginas['pagp_descripcion'];?></textarea>
											</div>
										</div>

										<?php $botones = new botonesGuardar("dev-paginas.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
						
                    </div>

                </div>
                <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
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
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>