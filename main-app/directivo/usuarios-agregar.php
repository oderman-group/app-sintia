<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0123';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
$datosUsuario = [
	'usuario'     => '',
	'nombre'      => '',
	'nombre2'      => '',
	'apellido1'      => '',
	'apellido2'      => '',
	'documento'      => '',
	'email'       => '',
	'celular'    => '',
	'genero'      => '',
	'tipoUsuario' => ''
];
if(isset($_GET['usuario'])){
	$datosUsuario['usuario'] = $_GET['usuario'];
}
if(isset($_GET['nombre'])){
	$datosUsuario['nombre'] = $_GET['nombre'];
}
if(isset($_GET['nombre2'])){
	$datosUsuario['nombre2'] = $_GET['nombre2'];
}
if(isset($_GET['apellido1'])){
	$datosUsuario['apellido1'] = $_GET['apellido1'];
}
if(isset($_GET['apellido2'])){
	$datosUsuario['apellido2'] = $_GET['apellido2'];
}
if(isset($_GET['tipoD'])){
	$datosUsuario['tipoD'] = $_GET['tipoD'];
}
if(isset($_GET['documento'])){
	$datosUsuario['documento'] = $_GET['documento'];
}
if(isset($_GET['email'])){
	$datosUsuario['email'] = $_GET['email'];
}
if(isset($_GET['celular'])){
	$datosUsuario['celular'] = $_GET['celular'];
}
if(isset($_GET['genero'])){
	$datosUsuario['genero'] = $_GET['genero'];
}
if(isset($_GET['tipoUsuario'])){
	$datosUsuario['tipoUsuario'] = $_GET['tipoUsuario'];
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
?>

<!--bootstrap -->
<link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
    rel="stylesheet" media="screen">
<link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet"
    media="screen">
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!-- dropzone -->
<link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
    type="text/css" />

<script src="js/jquery-2.2.4.min.js" type="text/javascript"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
<script type="application/javascript">
$(document).ready(function() {
    /*
    Comentado temporalmente mientras se coloca configurable

    $("#email").on("blur", function() {
        var email = $("#email").val();
        var dataString = 'email=' + email;

        $.ajax({
            url: 'ajax-comprobar-email.php',
            type: "GET",
            data: dataString,
            dataType: "JSON",

            success: function(datos) {
                if (datos.success == 1) {
                    $("#respuestaEmail").html(datos.message);
                    $("input").attr('disabled', true); 
                    $("input#email").attr('disabled',
                        false); 
                    $("#btnEnviar").attr('disabled', true); 
                } else {
                    $("#respuestaEmail").html(datos.message);
                    $("input").attr('disabled', false); 
                    $("#btnEnviar").attr('disabled', false); 

                }
            }
        });
    });
    */
});
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
                            <div class="page-title">Agregar usuarios</div>
                            <?php include("../compartido/texto-manual-ayuda.php");?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="usuarios.php?cantidad=10"
                                    onClick="deseaRegresar(this)">Usuarios</a>&nbsp;<i class="fa fa-angle-right"></i>
                            </li>
                            <li class="active">Agregar usuarios</li>
                        </ol>
                    </div>
                </div>
     
                <div class="row">

                <div class="col-sm-9">
                    <span style="color: blue; font-size: 15px;" id="respuestaEmail"></span>
                    <span style="color: blue; font-size: 15px;" id="respuestaUsuario"></span>
                    <span style="color: blue; font-size: 15px;" id="respuestaUsuario2"></span>
                        <?php include("../../config-general/mensajes-informativos.php"); ?>

                        <div class="panel">
                            <header class="panel-heading panel-heading-purple">
                                <?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                            <div class="panel-body">


                                <form name="formularioGuardar" id="myForm" action="usuarios-guardar.php" method="post">
                                
                                <h4>Credenciales de acceso a la plataforma</h4>
                                <div class="form-group row">
                                        <label class="col-sm-2 control-label">Tipo de usuario <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                            <?php
                                            try{
												$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_perfiles");
                                            } catch (Exception $e) {
                                                include("../compartido/error-catch-to-report.php");
                                            }
                                            ?>
                                            <select class="form-control  select2" name="tipoUsuario" id="tipoUsuario" onchange="validarCantidadUsuarios(this)" required <?=$disabledPermiso;?>>
                                                <option value="">Seleccione una opción</option>
                                                <?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														if(
														($opcionesDatos['pes_id'] == TIPO_DEV || $opcionesDatos['pes_id'] == TIPO_ESTUDIANTE ) 
														&& $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
                                                            continue;
                                                        }
														$select = '';
														if($opcionesDatos['pes_id']==$datosUsuario['tipoUsuario']) $select = 'selected';
													?>
                                                <option value="<?=$opcionesDatos['pes_id'];?>" <?=$select;?>>
                                                    <?=$opcionesDatos['pes_nombre'];?></option>
                                                <?php }?>
                                            </select>
                                            <i class="fa fa-info"></i> <span style="color: #6017dc;"> <b>IMPORTANTE:</b> Este dato define los permisos dentro de la plataforma.</span>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="form-group row">
                                        <label id="" class="col-sm-2 control-label">Usuario de acceso <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="usuario" id="usuario" data-id-usuario="0" oninput="validarUsuario(this)" autofocus class="form-control" value="<?=$datosUsuario['usuario'];?>" required pattern="[A-Za-z0-9]+" <?=$disabledPermiso;?>> <i class="fa fa-info"></i> <span style="color: #6017dc;">Puedes usar letras, números o combinarlos. Pero no se permiten caracteres especiales o espacios en blanco.</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Contraseña <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="clave" class="form-control" value="<?=CLAVE_SUGERIDA;?>" required <?=$disabledPermiso;?>>
                                            <i class="fa fa-info"></i> <span style="color: #6017dc;">La contraseña debe ser de 8 caracteres como mínimo y 20 como máximo.</span>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Tipo de documento</label>
                                        <div class="col-sm-4">
                                            <?php
                                            try{
                                                $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales
                                                WHERE ogen_grupo=1");
                                            } catch (Exception $e) {
                                                include("../compartido/error-catch-to-report.php");
                                            }
                                            ?>
                                            <select class="form-control  select2" name="tipoD" <?=$disabledPermiso;?>>
                                                <option value="">Seleccione una opción</option>
                                                <?php while($o = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                    if($o['ogen_id']==$datosUsuario['tipoD'])
                                                    echo '<option value="'.$o['ogen_id'].'" selected>'.$o['ogen_nombre'].'</option>';
                                                else
                                                    echo '<option value="'.$o['ogen_id'].'">'.$o['ogen_nombre'].'</option>';	
                                                }?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Documento</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="documento" id="documento" class="form-control" data-id-usuario="0" oninput="validarDocumento(this)" value="<?=$datosUsuario['documento'];?>" <?=$disabledPermiso;?>>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Nombre <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="nombre" class="form-control"
                                                value="<?=$datosUsuario['nombre'];?>" required pattern="^[A-Za-zñÑ]+$" <?=$disabledPermiso;?>>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Otro Nombre</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="nombre2" class="form-control" value="<?=$datosUsuario['nombre2'];?>" <?=$disabledPermiso;?>>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Primer Apellido</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="apellido1" class="form-control" value="<?=$datosUsuario['apellido1'];?>" pattern="^[A-Za-zñÑ]+$" <?=$disabledPermiso;?>>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Segundo Apellido</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="apellido2" class="form-control" value="<?=$datosUsuario['apellido2'];?>" <?=$disabledPermiso;?>>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Email</label>
                                        <div class="col-sm-4">
                                            <input type="email" name="email" id="email" autofocus class="form-control" value="<?=$datosUsuario['email'];?>" <?=$disabledPermiso;?>>
                                            
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Celular</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="celular" class="form-control" data-mask="(999) 999-9999" value="<?=$datosUsuario['celular'];?>" <?=$disabledPermiso;?>>
                                        </div>
                                    </div>



                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Género <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                        <?php
                                        try{
                                            $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales 
                                            WHERE ogen_grupo=4");
                                        } catch (Exception $e) {
                                            include("../compartido/error-catch-to-report.php");
                                        }
										?>
                                        <select class="form-control  select2" name="genero" required <?=$disabledPermiso;?>>
                                                <option value="">Seleccione una opción</option>
                                                <?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														$select = '';
														if($opcionesDatos[0]==$datosUsuario['genero']) $select = 'selected';
													?>
                                                <option value="<?=$opcionesDatos[0];?>" <?=$select;?>>
                                                    <?=$opcionesDatos['ogen_nombre'];?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php $botones = new botonesGuardar("usuarios.php?cantidad=10",Modulos::validarPermisoEdicion()); ?>
                                    
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3"></div>

                </div>

            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    <script src="../js/Usuarios.js" ></script>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../config-general/assets/plugins/popper/popper.js"></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" charset="UTF-8">
    </script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"
        charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"
        charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"
        charset="UTF-8"></script>
    <!-- Common js-->
    <script src="../../config-general/assets/js/app.js"></script>
    <script src="../../config-general/assets/js/layout.js"></script>
    <script src="../../config-general/assets/js/theme-color.js"></script>
    <!-- notifications -->
    <script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
    <!-- Material -->
    <script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js"></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js"></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
    <!-- end js include path -->
    </body>

    <!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

    </html>