<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0124';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once("../class/SubRoles.php");

$parametrosObligatorios =["id"];

Utilidades::validarParametros($_GET,$parametrosObligatorios);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$id="";
if(!empty($_GET["id"])){ $id=base64_decode($_GET["id"]);}

$datosEditar = UsuariosPadre::sesionUsuario($id, " AND uss_id!='{$_SESSION["id"]}'");

if( empty($datosEditar) ){
	echo '<script type="text/javascript">window.location.href="usuarios.php?error=ER_DT_16";</script>';
	exit();
}


if ($datosEditar['uss_tipo'] == TIPO_DEV && $datosUsuarioActual['uss_tipo'] != TIPO_DEV) {
	echo '<script type="text/javascript">window.location.href="usuarios.php?error=ER_DT_2&usuario='.$_GET["id"].'";</script>';
	exit();
}

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
                                <div class="page-title">Editar usuarios</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="usuarios.php?cantidad=10" onClick="deseaRegresar(this)">Usuarios</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar usuarios</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">

					
						
						
						
                        <div class="col-sm-12">
                        <span style="color: blue; font-size: 15px;" id="respuestaUsuario"></span>
                        <span style="color: blue; font-size: 15px;" id="respuestaUsuario2"></span>
						<?php include("../../config-general/mensajes-informativos.php"); ?>

						<nav>
							<div class="nav nav-tabs" id="nav-tab" role="tablist">
								<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Información básica</a>

								<?php if($datosEditar['uss_tipo'] == TIPO_DOCENTE) {?>
									<a class="nav-item nav-link" id="nav-cargas-tab" data-toggle="tab" href="#nav-cargas" role="tab" aria-controls="nav-cargas" aria-selected="false" onClick="listarInformacion('async-cargas-listar.php?docente=<?=$datosEditar['uss_id'];?>', 'nav-cargas')">Cargas académicas</a>
								<?php }?>
								
							</div>
						</nav>

						<div class="tab-content" id="nav-tabContent">
							<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
								<?php include 'includes/usuarios-editar-info-basica.php'; ?>
							</div>
							<div class="tab-pane fade" id="nav-cargas" role="tabpanel" aria-labelledby="nav-profile-tab"></div>
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
    <script src="../js/Usuarios.js" ></script>
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
    
    <script>
    // Inicializar estado de validación al cargar la página
    $(document).ready(function() {
        // Asegurar que todos los campos estén habilitados al cargar (a menos que haya validaciones pendientes)
        if (typeof habilitarCamposFormularioEditar === 'function') {
            // Solo habilitar si no hay validaciones que indiquen duplicados
            usuarioValidadoEditar = true;
            documentoValidadoEditar = true;
            habilitarCamposFormularioEditar(true);
        }
        
        // Asegurar que el campo de contraseña esté deshabilitado al cargar la página
        const campoClave = document.getElementById('clave');
        const checkbox = document.getElementById('cambiarClave');
        
        if (campoClave) {
            campoClave.disabled = true;
            campoClave.setAttribute('readonly', 'readonly');
            campoClave.classList.add('bg-light');
        }
        
        // Si el checkbox existe, asegurar que esté desmarcado inicialmente
        if (checkbox) {
            checkbox.checked = false;
        }
    });
    
    // Función para manejar el desbloqueo de usuario
    function manejarDesbloqueo(checkbox) {
        const bloqueadoInput = document.getElementById('bloqueado');
        const bloqueadoActual = bloqueadoInput.value;
        
        if (bloqueadoActual == '1') {
            // Usuario está bloqueado
            if (!checkbox.checked) {
                // Switch desactivado = desbloquear (establecer bloqueado = 0)
                bloqueadoInput.value = '0';
                console.log('Usuario será desbloqueado');
            } else {
                // Si está activado, mantener bloqueado
                bloqueadoInput.value = '1';
            }
        } else {
            // Usuario está desbloqueado - no permitir bloquear
            checkbox.checked = false;
            checkbox.disabled = true;
            alert('No se puede bloquear un usuario desde esta página. El bloqueo debe realizarse desde la lista de usuarios.');
        }
    }
    
    // Función para habilitar/deshabilitar el campo de contraseña
    function habilitarClave() {
        const checkbox = document.getElementById('cambiarClave');
        const campoClave = document.getElementById('clave');
        
        if (checkbox && campoClave) {
            if (checkbox.checked) {
                // Si el switch está activado, habilitar el campo
                campoClave.disabled = false;
                campoClave.removeAttribute('readonly');
                campoClave.classList.remove('bg-light');
                campoClave.focus();
            } else {
                // Si el switch está desactivado, deshabilitar el campo y limpiar su valor
                campoClave.disabled = true;
                campoClave.setAttribute('readonly', 'readonly');
                campoClave.classList.add('bg-light');
                campoClave.value = '';
            }
        }
    }
    
    // Función para validar número de celular (solo números, 10 dígitos)
    function validarNumeroCelular(input) {
        // Remover cualquier carácter que no sea número
        let valor = input.value.replace(/[^0-9]/g, '');
        
        // Limitar a 10 dígitos
        if (valor.length > 10) {
            valor = valor.substring(0, 10);
        }
        
        // Actualizar el valor del input
        input.value = valor;
        
        // Validar y mostrar mensaje
        const $validacion = $('#validacion_celular');
        if (valor.length === 0) {
            $validacion.html('').removeClass('text-success text-danger');
        } else if (valor.length === 10) {
            $validacion.html('<i class="fa fa-check"></i> Número válido').removeClass('text-danger').addClass('text-success');
        } else {
            $validacion.html('<i class="fa fa-exclamation-triangle"></i> Debe tener 10 dígitos').removeClass('text-success').addClass('text-danger');
        }
    }
    
    // Función para validar número de teléfono (solo números, 7 dígitos)
    function validarNumeroTelefono(input) {
        // Remover cualquier carácter que no sea número
        let valor = input.value.replace(/[^0-9]/g, '');
        
        // Limitar a 7 dígitos
        if (valor.length > 7) {
            valor = valor.substring(0, 7);
        }
        
        // Actualizar el valor del input
        input.value = valor;
        
        // Validar y mostrar mensaje
        const $validacion = $('#validacion_telefono');
        if (valor.length === 0) {
            $validacion.html('').removeClass('text-success text-danger');
        } else if (valor.length === 7) {
            $validacion.html('<i class="fa fa-check"></i> Número válido').removeClass('text-danger').addClass('text-success');
        } else {
            $validacion.html('<i class="fa fa-exclamation-triangle"></i> Debe tener 7 dígitos').removeClass('text-success').addClass('text-danger');
        }
    }
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>