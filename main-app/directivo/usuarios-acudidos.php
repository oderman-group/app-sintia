<?php
include("session.php");
$idPaginaInterna = 'DT0137';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once("../class/Estudiantes.php");
require_once("../class/servicios/UsuarioServicios.php");
require_once("../class/servicios/MatriculaServicios.php");
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
                            <div class="page-title">Acudidos</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="#" name="usuarios.php?cantidad=10&tipo=3" onClick="deseaRegresar(this)">Usuarios</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Acudidos</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?php include("../../config-general/mensajes-informativos.php"); ?>
                    </div>
                    <?php $acudienteActural = UsuarioServicios::consultar($_GET['id']); ?>
                    <div class="col-sm-3">
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple"><b>Datos del acudiente</b></header>
                            <div class="panel-body">
                                <div class="form-group row">
                                    <label class="col-sm-3 control-label "><b>Nombre: </b></label>
                                    <label class="col-sm-9 control-label"><?= UsuarioServicios::nombres($acudienteActural) ?></label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 control-label"><b>Apellido:</b></label>
                                    <label class="col-sm-9 control-label"><?= UsuarioServicios::apellidos($acudienteActural) ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-9">
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple"><b>Acudidos</b></header>
                            <div class="panel-body">
                                <form name="formularioGuardar" action="usuarios-acudidos-actualizar.php" method="post">
                                    <input type="hidden" value="<?=$_GET['id'];?>" name="id">
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label"><b>Estudiantes: </b></label>
                                        <div class="col-sm-10">
                                            <?php
                                            $parametros = array("upe_id_usuario"=>$acudienteActural["uss_id"]);
                                            $listaAcudidos = UsuarioServicios::listarUsuariosEstudiante($parametros);
                                            ?>
                                            <select id="select_estudiante" class="form-control  select2-multiple"  name="acudidos[]" required multiple>
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                foreach($listaAcudidos as $idEstudiante){
                                                    $matricualaEstudiante=MatriculaServicios::consultar($idEstudiante["upe_id_estudiante"]);
                                                    if(!is_null($matricualaEstudiante)){
                                                    $nombre = Estudiantes::NombreCompletoDelEstudiante($matricualaEstudiante);
                                                 ?>
                                                 <option value="<?= $matricualaEstudiante['mat_id']; ?>" selected><?= $nombre; ?></option>
                                                 <?php }} ?>
                                               
                                            </select>
                                        </div>
                                    </div>
                                        <input type="submit" class="btn btn-primary" value="Guardar Cambios">&nbsp;

                                        <a href="#" name="usuarios.php?cantidad=10&tipo=3" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
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
        <script>          
                $(document).ready(function() {
          		$('#select_estudiante').select2({
					placeholder: 'Seleccione el estudiante...',
					theme: "bootstrap",
                    multiple: true,
					ajax: {
						type: 'GET',
						url: 'ajax-listar-estudiantes.php',
						processResults: function(data) {
                            data = JSON.parse(data);
							return {
								results: $.map(data, function(item) {                                  
									return {
										id: item.value,
										text: item.label
									}
								})
							};
						}
					}
				});
			});
        </script>
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
        </body>

        <!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
        </html>