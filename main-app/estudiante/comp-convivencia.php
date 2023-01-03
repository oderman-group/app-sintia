<?php include("session.php");?>
<?php
$_SESSION["bd"] = 2022;
?>
<?php include("verificar-usuario.php");?>
<?php include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'ES0046';?>
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
                                <div class="page-title">Compromiso de convivencia</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                       						

						<div class="col-sm-9">
						    <div class="card card-box">
						        <div class="card-head">
						            <header>Compromiso de convivencia</header>
						        </div>
						        <div class="card-body " id="bar-parent6">

                                
                                <?php if($datosEstudianteActual["mat_compromiso_convivencia_opcion"] == 1){?>

                                    <p style="text-align: center;"><strong><u>COMPROMISO DISCIPLINARIO &ndash; Nuevo Ingreso</u></strong></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Nombres y apellidos del estudiante: <b><?= $datosEstudianteActual["mat_primer_apellido"]." ".$datosEstudianteActual["mat_segundo_apellido"]." ".$datosEstudianteActual["mat_nombres"]; ?></b>.</p>
<p style="text-align: justify;">Estudiante aspirante al grado <b><?= $datosEstudianteActual["gra_nombre"]; ?></b>. Una vez que he tomado conocimiento de las pol&iacute;ticas institucionales, normas, reglamentos y disposiciones contenidas en el Manual de Convivencia, reconozco que la instituci&oacute;n me est&aacute; recibiendo en procura de ayudarme en las dificultades y/o actitudes de comportamiento en las situaciones que a continuaci&oacute;n se enumeran y que me comprometo a cumplir:</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><br />A la vez doy constancia de que tengo suficiente informaci&oacute;n relacionada con el tema del COMPROMISO DISCIPLINARIO, que est&aacute; probando mi permanencia en la Instituci&oacute;n; por tal raz&oacute;n junto con mi acudiente firmo este documento como constancia de entrevista dentro del proceso de admisi&oacute;n, las cuales llenan las condiciones de un compromiso disciplinario y reconozco que cada una de sus pautas y normas deben ser cumplidas.&nbsp;</p>
<p style="text-align: justify;">En mi calidad de acudiente, una vez que he tomado conocimiento de las pol&iacute;ticas institucionales, normas, reglamentos y disposiciones contenidas en el Manual de Convivencia, me comprometo a cumplir en forma permanente el control, orientaci&oacute;n diaria, reflexi&oacute;n permanente con mi acudido, a fin de mantener el prestigio del colegio en el campo disciplinario y acad&eacute;mico, que adem&aacute;s en el contexto de mayor importancia, beneficiar&aacute; el bienestar familiar y personal de mi acudido, evitando sanciones por la inobservancia a cualquiera de las obligaciones indicadas en el Manual de Convivencia.</p>
<p style="text-align: justify;">Sabemos que como instituci&oacute;n est&aacute;n en la obligaci&oacute;n de seguir un debido proceso disciplinario y a&uacute;n el retiro del plantel, si el Consejo Directivo lo cree necesario por la inobservancia a cualquiera de las obligaciones indicadas en el Manual de Convivencia.</p>
<p style="text-align: justify;">Para mayor constancia se firma a los d&iacute;as <b><?= date("d");?></b> d&iacute;as del mes de <b><?= date("M");?></b> del a&ntilde;o <b><?= date("Y");?></b>.</p>
<p style="text-align: justify;"><br />&nbsp;</p>

                                <?php }?>




                                <?php if($datosEstudianteActual["mat_compromiso_convivencia_opcion"] == 2){?>
                                <p style="text-align: center;"><strong><u>COMPROMISO DE CONVIVENCIA</u></strong></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Nombres y apellidos del estudiante: <b><?= $datosEstudianteActual["mat_primer_apellido"]." ".$datosEstudianteActual["mat_segundo_apellido"]." ".$datosEstudianteActual["mat_nombres"]; ?></b>.</p>
<p style="text-align: justify;">Estudiante aspirante al grado <b><?= $datosEstudianteActual["gra_nombre"]; ?></b>. Una vez que he tomado conocimiento de las pol&iacute;ticas institucionales, normas, reglamentos y disposiciones contenidas en el Manual de Convivencia, reconozco que la instituci&oacute;n me est&aacute; recibiendo en procura de ayudarme en las dificultades y/o actitudes de comportamiento en las situaciones que a continuaci&oacute;n se enumeran y que me comprometo a cumplir:</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><br />En mi calidad de acudiente, una vez que he tomado conocimiento de las pol&iacute;ticas institucionales, normas, reglamentos y disposiciones contenidas en el Manual de Convivencia, me comprometo a cumplir en forma permanente el control, orientaci&oacute;n diaria, reflexi&oacute;n permanente con mi acudido, a fin de mantener el prestigio del colegio en el campo disciplinario y acad&eacute;mico, que adem&aacute;s en el contexto de mayor importancia, beneficiar&aacute; el bienestar familiar y personal de mi acudido, evitando sanciones por la inobservancia a cualquiera de las obligaciones indicadas en el Manual de Convivencia.A la vez doy constancia de que tengo suficiente informaci&oacute;n relacionada con el tema del COMPROMISO DISCIPLINARIO, que est&aacute; probando mi permanencia en la Instituci&oacute;n; por tal raz&oacute;n junto con mi acudiente firmo este documento como constancia de que asumo todas las normas y reglamentos de la Instituci&oacute;n contenidos en el Manual de Convivencia. Debido a que he incurrido en situaciones disciplinarias reprochables por la normatividad de la Instituci&oacute;n, comprendo que este requisito cumple con las condiciones de un compromiso disciplinario que me comprometo a cumplir y respetar en todos los espacios exigidos y esperados por el Instituto.</p>
<p style="text-align: justify;">Sabemos que como instituci&oacute;n est&aacute;n en la obligaci&oacute;n de seguir un debido proceso disciplinario y a&uacute;n el retiro del plantel, si el Consejo Directivo lo cree necesario por la inobservancia a cualquiera de las obligaciones indicadas en el Manual de Convivencia.</p>
<p style="text-align: justify;">Para mayor constancia se firma a los d&iacute;as <b><?= date("d");?></b> d&iacute;as del mes de <b><?= date("M");?></b> del a&ntilde;o <b><?= date("Y");?></b>.</p>
<p style="text-align: justify;"><br />&nbsp;</p>
<?php }?>




<?php if($datosEstudianteActual["mat_compromiso_convivencia_opcion"] == 3){?>
    <p style="text-align: center;"><strong>INSTITUTO COLOMBO VENEZOLANO</strong></p>
<p style="text-align: center;"><strong><u>COMPROMISO DISCIPLINARIO ESPECIAL </u></strong></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Nombres y apellidos del estudiante: <b><?= $datosEstudianteActual["mat_primer_apellido"]." ".$datosEstudianteActual["mat_segundo_apellido"]." ".$datosEstudianteActual["mat_nombres"]; ?></b>. Estudiante aspirante al grado <b><?= $datosEstudianteActual["gra_nombre"]; ?></b>. Una vez que he tomado conocimiento de las pol&iacute;ticas institucionales, normas, reglamentos y disposiciones contenidas en el Manual de Convivencia, reconozco que la instituci&oacute;n me est&aacute; recibiendo en condici&oacute;n normativa como &uacute;ltima oportunidad en este lugar. En procura de ayudarme en las dificultades y/o actitudes de comportamiento en las situaciones que a continuaci&oacute;n se enumeran, me comprometo a cumplir:</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><br />A la vez doy constancia de que tengo suficiente informaci&oacute;n relacionada con el tema del COMPROMISO DISCIPLINARIO ESPECIAL, que est&aacute; probando mi permanencia en la Instituci&oacute;n; por tal raz&oacute;n&nbsp; junto con mi acudiente firmo este documento como constancia de que asumo todas las normas y reglamentos de la Instituci&oacute;n, debido a que he incurrido en situaciones disciplinarias reprochables por la normatividad de la Instituci&oacute;n. Comprendo que este requisito cumple con las condiciones de un compromiso disciplinario que me comprometo a cumplir y respetar en todos los espacios exigidos y esperados por el Instituto.&nbsp;</p>
<p style="text-align: justify;">En mi calidad de acudiente, una vez que he tomado conocimiento de las pol&iacute;ticas institucionales, normas, reglamentos y disposiciones contenidas en el Manual de Convivencia, me comprometo a cumplir en forma permanente el control, orientaci&oacute;n diaria, reflexi&oacute;n permanente con mi acudido, a fin de mantener el prestigio del colegio en el campo disciplinario y acad&eacute;mico, que adem&aacute;s en el contexto de mayor importancia, beneficiar&aacute; el bienestar familiar y personal de mi acudido, evitando sanciones por la inobservancia a cualquiera de las obligaciones indicadas en el Manual de Convivencia.</p>
<p style="text-align: justify;">Sabemos que como instituci&oacute;n est&aacute;n en la obligaci&oacute;n de seguir un debido proceso disciplinario y a&uacute;n el retiro del plantel, si el Consejo Directivo lo cree necesario por la inobservancia a cualquiera de las obligaciones indicadas en el Manual de Convivencia.</p>
<p style="text-align: justify;">Que al estudiante se le hizo el debido proceso como reposa en el Manual de Convivencia y en la Ficha de seguimiento que se le lleva al alumno en la oficina de Coordinaci&oacute;n de Convivencia, que como &uacute;ltima instancia el proceso fue llevado al Consejo de Profesores y al Consejo Directivo, &nbsp;&nbsp;</p>
<p style="text-align: justify;"><strong>&nbsp;</strong></p>
<p style="text-align: justify;"><strong>RESUELVEN</strong></p>
<ol style="text-align: justify;">
<li><strong> ART&Iacute;CULO PRIMERO: </strong>Establecer el contrato por prestaci&oacute;n de servicios educativos para el alumno <b><?= $datosEstudianteActual["mat_primer_apellido"]." ".$datosEstudianteActual["mat_segundo_apellido"]." ".$datosEstudianteActual["mat_nombres"]; ?></b> con D.I. <b><?= $datosEstudianteActual["mat_documento"]; ?></b> para el presente a&ntilde;o lectivo, sujeto a la condici&oacute;n de cumplirse cabalmente con los compromisos pactados anteriormente, de no incurrir en situaciones de indisciplina antes mencionadas, o desacato de las normas institucionales al Manual de Convivencia. El Contrato de matr&iacute;cula est&aacute; determinado como &uacute;ltima oportunidad para el alumno. Se revisar&aacute; su caso cada per&iacute;odo acad&eacute;mico para determinar la continuidad de &eacute;l. Su comportamiento debe ser por lo menos alto en cada per&iacute;odo. De no tener la nota en Alto o superior en cada per&iacute;odo, su permanencia como alumno ser&aacute; cancelada, y quedar&aacute; sujeto a determinaci&oacute;n del Consejo Directivo. El no cumplimiento de las condiciones, seg&uacute;n cada per&iacute;odo observado, ocasionar&aacute; la cancelaci&oacute;n del Contrato de Matr&iacute;cula de manera irrevocable por el Consejo Directivo.</li>
</ol>
<p style="text-align: justify;">Para mayor constancia se firma a los d&iacute;as <b><?= date("d");?></b> d&iacute;as del mes de <b><?= date("M");?></b> del a&ntilde;o <b><?= date("Y");?></b>.</p>
<p style="text-align: justify;"><br />&nbsp;</p>
<?php }?>





						            <form action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
						                <input type="hidden" name="id" value="27">

						                

						                <?php if ($datosEstudianteActual["mat_compromiso_academico"] == 1 AND $datosEstudianteActual["mat_compromiso_convivencia"] == '0') { ?>
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