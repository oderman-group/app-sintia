<?php include("session.php");?>
<?php
$_SESSION["bd"] = 2022;
?>
<?php include("verificar-usuario.php");?>
<?php include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'ES0041';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>

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
                                <div class="page-title">Manual de convivencia</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                       						

						<div class="col-sm-9">
						    <div class="card card-box">
						        <div class="card-head">
						            <header>Manual de convivencia</header>
						        </div>
						        <div class="card-body " id="bar-parent6">

                                <h2 style="text-align: center;"><strong>Nota Jur&iacute;dica final:</strong></h2>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Yo, <b><?= $datosEstudianteActual["uss_nombre"];?></b>, fungiendo y actuando como, tutor(a) apoderado (a)&nbsp; y representante legal que me se&ntilde;ala como acudiente del educando:</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><?= $datosEstudianteActual["mat_primer_apellido"]." ".$datosEstudianteActual["mat_segundo_apellido"]." ".$datosEstudianteActual["mat_nombres"]; ?>, Declaro de manera libre, abierta y responsable, que he le&iacute;do y abordado el presente manual de convivencia y que acepto y ratifico con mi firma, el cumplimiento del mismo, y brindar&eacute; al acatamiento Por convicci&oacute;n y de manera inexorable, inaplazable y obligatoria al contenido total del presente documento, a todos sus art&iacute;culos, par&aacute;grafos y notas y declaro mediante firma, que los acepto en su integridad, porque refleja las normas, c&aacute;nones y&nbsp; la informaci&oacute;n completa que comparto y asumo como parte fundamental de la educaci&oacute;n integral, curricular, cognitiva, psicosocial y espiritual que espero como acudiente y como responsable, como padre de familia y que buscaba del INSTITUTO COLOMBO VENEZOLANO- ICOLVEN, al momento de suscribir presente matr&iacute;cula para mi HIJO(A), motivo por el cual, al firmar el presente documento, <u>renuncio a cualquier tipo de demanda o acci&oacute;n de tutela o recurso similar en contra del presente texto y sus directrices, las cuales reconozco y acato en su integridad.</u></p>


<p><a href="http://www.icolven.edu.co/wp-content/uploads/2017/05/Manual-de-Convivencia-2022-1.pdf" target="_blank">LINK PARA DESCARGAR EL MANUAL DE CONVIVENCIA</a></p>





						            <form action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
						                <input type="hidden" name="id" value="26">

						                

						                <?php if ($datosEstudianteActual["mat_compromiso_convivencia"] == 1 AND $datosEstudianteActual["mat_manual"] == '0') { ?>
						                    <input type="submit" class="btn btn-primary" value="Aceptar y continuar">&nbsp;
						                <?php }?>

						            </form>
						        </div>
						    </div>
						</div>

						<div class="col-sm-3">

                            <?php include("../compartido/matricula-pasos.php"); ?>

						    <?php include("../compartido/modulo-frases-lateral.php"); ?>

						    <?php //include("../compartido/publicidad-lateral.php"); ?>
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
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>