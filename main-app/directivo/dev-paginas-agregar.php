<?php
include("session.php");
require_once("../class/SubRoles.php");
require_once("../class/Paginas.php");

$idPaginaInterna = 'DV0017';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");
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
        function nuevaPagina(enviada){
            var dato = enviada.value;

            if(dato!=""){
                $('#resp').empty().hide().html("Validado Dato...").show(1);

                datos = "dato="+(dato);
                    $.ajax({
                    type: "POST",
                    url: "ajax-dev-paginas-agregar.php",
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
                                <div class="page-title">Agregar Paginas</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="dev-paginas.php" onClick="deseaRegresar(this)"><?=$frases[403][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Agregar Paginas</li>
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

                                   
									<form name="formularioGuardar" action="dev-paginas-guardar.php" method="post" enctype="multipart/form-data">
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Codigo<span style="color: red;">(*)</span></label>
                                            <div class="col-sm-2">
                                                <input type="text" name="codigoPagina" class="form-control" id="codigo" onChange="nuevaPagina(this)" required>
                                            </div>
                                        </div>
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre Pagina<span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="text" name="nombrePagina" class="form-control" id="nombrePagina" required>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Tipo de Usuario</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="tipoUsuario" id="tipoUsuario">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    try{
                                                        $consultaUsuarios = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_perfiles");
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
                                                    while($tipoUsuarios=mysqli_fetch_array($consultaUsuarios, MYSQLI_BOTH)){
                                                        echo'<option value="'.$tipoUsuarios["pes_id"].'">'.$tipoUsuarios["pes_nombre"].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Modulo</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="modulo" id="modulo">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    try{
                                                        $consultaModulos=mysqli_query($conexion, "SELECT mod_id, mod_nombre FROM ".$baseDatosServicios.".modulos WHERE mod_estado=1");
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
                                                    while($modulos=mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)){
                                                        echo'<option value="'.$modulos["mod_id"].'">'.$modulos["mod_nombre"].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
										
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Ruta Pagina<span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="text" name="rutaPagina" class="form-control" onChange="nuevaPagina(this)" id="rutaPagina" required>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Navegable?</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="navegable" id="navegable">
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1">SI</option>
                                                    <option value="0">NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Asignable a SubRoles?</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="subroles" id="subroles">
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="<?=SI?>">SI</option>
                                                    <option value="<?=NO?>">NO</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">CRUD</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="crud" id="crud">
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="CREATE">CREATE</option>
                                                    <option value="READ">READ</option>
                                                    <option value="UPDATE">UPDATE</option>
                                                    <option value="DELETE">DELETE</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Pagina Padre</label>
                                            <div class="col-sm-3">
                                                <select class="form-control  select2" name="paginaPadre" id="paginaPadre">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    try{
                                                        $consultaPaginas = SubRoles::listarPaginas();
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
                                                    while($pagina=mysqli_fetch_array($consultaPaginas, MYSQLI_BOTH)){
                                                        echo'<option value="'.$pagina["pagp_id"].'">['.$pagina["pagp_id"].'] '.$pagina["pagp_pagina"].'</option>';
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
                                                        $filtro="AND pagp_tipo_usuario=5";
                                                        $consultaPaginas = Paginas::listarPaginas($filtro);
                                                        while($pagina=mysqli_fetch_array($consultaPaginas, MYSQLI_BOTH)){
                                                            echo'<option value="'.$pagina["pagp_id"].'">['.$pagina["pagp_id"].'] '.$pagina["pagp_pagina"].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Palabras Claves</label>
											<div class="col-sm-6">
                                                <textarea cols="80" id="editor1" name="palabrasClaves" rows="10" id="palabrasClaves"></textarea>
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
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>