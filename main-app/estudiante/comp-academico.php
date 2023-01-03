<?php include("session.php");?>
<?php
$_SESSION["bd"] = 2022;
?>
<?php include("verificar-usuario.php");?>
<?php include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'ES0047';?>
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
                                <div class="page-title">Compromiso académico</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                       						

						<div class="col-sm-9">
						    <div class="card card-box">
						        <div class="card-head">
						            <header>Compromiso académico</header>
						        </div>
						        <div class="card-body " id="bar-parent6">

                                <p style="text-align: center;"><strong>INSTITUTO COLOMBO VENEZOLANO</strong></p>
<p style="text-align: center;"><strong><u>COMPROMISO ACAD&Eacute;MICO </u></strong></p>
<p style="text-align: justify;">FECHA: <b><?=date("d/M/Y");?></b></p>
<p style="text-align: justify;">NOMBRE COMPLETO: <b><?= $datosEstudianteActual["mat_primer_apellido"]." ".$datosEstudianteActual["mat_segundo_apellido"]." ".$datosEstudianteActual["mat_nombres"]; ?></b> GRADO: <b><?= $datosEstudianteActual["gra_nombre"]; ?></b></p>

<p style='margin:0cm;margin-bottom:.0001pt;font-size:15px;font-family:"Calibri",sans-serif;text-align:justify;'><span style="font-size:16px;">Con el &aacute;nimo de aportar al crecimiento y desarrollo del estudiante, e interesados en que se obtengan los mejores resultados acad&eacute;micos para el a&ntilde;o en curso, le solicitamos que en compa&ntilde;&iacute;a de su acudiente realice el siguiente compromiso, el cual tiene como objetivo analizar y aportar estrategias de manejo para su rendimiento acad&eacute;mico. Diligencien este documento a conciencia, teniendo en cuenta que ha de ser un par&aacute;metro para medir el grado de inter&eacute;s en las actividades acad&eacute;micas. Se har&aacute; un seguimiento a su proceso acad&eacute;mico, el cual servir&aacute; para decidir su permanencia en la instituci&oacute;n.</span></p>
<p style='margin:0cm;margin-bottom:.0001pt;font-size:15px;font-family:"Calibri",sans-serif;text-align:justify;'><strong><span style="font-size:16px;">COMPROMISO DEL ESTUDIANTE:</span></strong><span style="font-size:16px;">&nbsp;Responsabilizarse de realizar todas las actividades acad&eacute;micas con calidad y a tiempo, ser puntual al momento de llegar a la instituci&oacute;n y a las diferentes clases, prepararse para las evaluaciones en el momento oportuno.</span></p>
<p style='margin:0cm;margin-bottom:.0001pt;font-size:15px;font-family:"Calibri",sans-serif;text-align:justify;'><strong><span style="font-size:16px;">COMPROMISO DEL ACUDIENTE:</span></strong><span style="font-size:16px;">&nbsp;Responsabilizarse en hacer un acompa&ntilde;amiento permanente y oportuno al acudido durante el a&ntilde;o acad&eacute;mico, buscar asesor&iacute;as externas en donde se pueda nivelar de los contenidos y desempe&ntilde;os que hasta la fecha no ha visto.</span></p>
<p style='margin:0cm;margin-bottom:.0001pt;font-size:15px;font-family:"Calibri",sans-serif;text-align:justify;'><span style="font-size:16px;">El acudiente debe presentarse cada mes en la coordinaci&oacute;n acad&eacute;mica para llevar un seguimiento en los avances o aspectos a reforzar en el estudiante.</span></p>
<p style='margin:0cm;margin-bottom:.0001pt;font-size:15px;font-family:"Calibri",sans-serif;text-align:justify;'><span style="font-size:16px;">Con la firma de la presente acta, el estudiante se compromete a cumplir cabalmente con las responsabilidades establecidas en el Manual de Convivencia, desarrollando en forma responsable sus deberes escolares tales como: lecciones, trabajos de consulta, talleres, pr&aacute;cticas, evaluaciones, etc.&nbsp;</span></p>
<p style='margin:0cm;margin-bottom:.0001pt;font-size:15px;font-family:"Calibri",sans-serif;text-align:justify;'><span style="font-size:16px;">De igual manera, el acudiente cumplir&aacute; con las funciones establecidas: acompa&ntilde;ar el proceso educativo en el cumplimiento de sus responsabilidades como primeros educadores de sus hijos, para mejorar la orientaci&oacute;n personal y el desarrollo de valores ciudadanos, del mismo Manual. En caso de no cumplir con los deberes acad&eacute;micos, la instituci&oacute;n se reserva el derecho de permanencia del estudiante para el siguiente a&ntilde;o.</span></p>

<p>&nbsp;</p>


<p style="text-align: justify;">Direcci&oacute;n: <b><?= $datosEstudianteActual["mat_direccion"];?></b>&nbsp; Barrio: <b><?= $datosEstudianteActual["mat_barrio"];?></b></p>
<p style="text-align: justify;">Tel&eacute;fono Casa: <b><?= $datosEstudianteActual["mat_telefono"];?></b> Celular: <b><?= $datosEstudianteActual["mat_celular"];?></b></p>
<p style="text-align: justify;">Correo electr&oacute;nico: <b><?= $datosEstudianteActual["uss_email"];?></b>&nbsp;&nbsp;</p>





						            <form action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
						                <input type="hidden" name="id" value="23">

						                

						                <?php if ($datosEstudianteActual["mat_pagare"] == 1 AND $datosEstudianteActual["mat_compromiso_academico"] == '0') { ?>
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