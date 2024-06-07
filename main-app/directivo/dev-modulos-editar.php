<?php
include("session.php");

$idPaginaInterna = 'DV0009';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");

try{
    $consulta=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".modulos WHERE mod_id=".base64_decode($_GET["id"]).";");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$datosModulo=mysqli_fetch_array($consulta, MYSQLI_BOTH);
$clientes = explode(",", $datosModulo['mod_types_customer']);
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
                                <div class="page-title">Editar Modulos</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="dev-modulos.php" onClick="deseaRegresar(this)"><?=$frases[402][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar Modulos</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
                        <div class="col-sm-12">
                                <?php include("../../config-general/mensajes-informativos.php"); ?>


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="dev-modulos-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?=base64_decode($_GET["id"])?>">
										
                                        <?php 
                                            $rutaFoto = "../files/modulos/{$datosModulo['mod_imagen']}";
                                            if(!empty($datosModulo['mod_imagen']) && Utilidades::ArchivoExiste($rutaFoto)) {
                                        ?>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <div class="item">
                                                        <img src="<?=$rutaFoto;?>" width="100"/>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }?>
                                            
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Portada Módulo</label>
                                            <div class="col-sm-4">
                                                <input type="file" name="portada" class="form-control">
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="form-group row">
                                            <label class="col-sm-1 control-label">Modulo<span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="text" name="nombreModulo" class="form-control" value="<?=$datosModulo["mod_nombre"]?>" required>
                                            </div>
                                            
                                            <label class="col-sm-1 control-label">Modulo Padre</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="moduloPadre">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    try{
                                                        $consultaModulos=mysqli_query($conexion, "SELECT mod_id, mod_nombre FROM ".$baseDatosServicios.".modulos WHERE mod_estado=1");
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
                                                    while($modulos=mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)){
                                                        $selected="";
                                                        if($modulos["mod_id"]==$datosModulo["mod_padre"]){
                                                            $selected="selected";
                                                        }
                                                        echo'<option value="'.$modulos["mod_id"].'" '.$selected.'>'.$modulos["mod_nombre"].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-1 control-label">Estado</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="moduloEstado">
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1" <?php if($datosModulo['mod_estado']==1){echo "selected";}?>>Activo</option>
                                                    <option value="0" <?php if($datosModulo['mod_estado']==0){echo "selected";}?>>Inactivo</option>
                                                </select>
                                            </div>
                                            
                                            <label class="col-sm-1 control-label">Namespace</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="namespace" class="form-control" value="<?=$datosModulo["mod_namespace"]?>" required>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-1 control-label">Precio</label>
                                            <div class="col-sm-4">
                                                <input type="number" min="0" name="precio" class="form-control" value="<?=$datosModulo["mod_precio"]?>" required>
                                            </div>
                                            
                                            <label class="col-sm-1 control-label">Orden</label>
                                            <div class="col-sm-4">
                                                <input type="number" min="0" name="order" class="form-control" value="<?=$datosModulo["mod_order"]?>" required>
                                            </div>
                                        </div>
										
                                        <div class="form-group row">
                                            <label class="col-sm-1 control-label">Tipos de Clientes</label>
                                            <div class="col-sm-9">
                                                <select class="form-control  select2-multiple" multiple name="clientes[]">
                                                    <option value="">Escoja una opción</option>
                                                    <option value="<?=SCHOOL?>" <?=in_array(SCHOOL, $clientes) ? "selected": "";?>><?=SCHOOL?></option>
                                                    <option value="<?=INSTITUTE?>" <?=in_array(INSTITUTE, $clientes) ? "selected": "";?>><?=INSTITUTE?></option>
                                                    <option value="<?=UNIVERSITY?>" <?=in_array(UNIVERSITY, $clientes) ? "selected": "";?>><?=UNIVERSITY?></option>
                                                    <option value="<?=COMPANY?>" <?=in_array(COMPANY, $clientes) ? "selected": "";?>><?=COMPANY?></option>
                                                    <option value="<?=KINDERGARTEN?>" <?=in_array(KINDERGARTEN, $clientes) ? "selected": "";?>><?=KINDERGARTEN?></option>
                                                    <option value="<?=PEOPLE?>" <?=in_array(PEOPLE, $clientes) ? "selected": "";?>><?=PEOPLE?></option>
                                                </select>
                                            </div>
                                        </div>
										
                                        <div class="form-group row">
                                            <label class="col-sm-1 control-label">Descripción</label>
                                            <div class="col-sm-9">
                                                <textarea cols="80" id="editor1" name="descripcion" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"><?=$datosModulo['mod_description'];?></textarea>
                                            </div>
                                        </div>

										<?php $botones = new botonesGuardar("dev-modulos.php",Modulos::validarPermisoEdicion()); ?>
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
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>