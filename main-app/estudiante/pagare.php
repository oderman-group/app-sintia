<?php include("session.php");?>
<?php
$_SESSION["bd"] = 2022;
?>
<?php include("verificar-usuario.php");?>
<?php include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'ES0039';?>
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
                                <div class="page-title">Pagaré</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                       						

						<div class="col-sm-9">
						    <div class="card card-box">
						        <div class="card-head">
						            <header>Pagaré</header>
						        </div>
						        <div class="card-body " id="bar-parent6">

<p style="text-align: justify;"><strong> <em> PAGARE N&deg;_________ </em> </strong></p>
<p style="text-align: justify;"><strong> <em> &nbsp; </em> </strong></p>
<p style="text-align: justify;"><strong> <em> PAGARE POR $------------------------- </em> </strong></p>
<p style="text-align: justify;"><strong> <em> &nbsp; </em> </strong></p>
<p style="text-align: justify;"><strong> <em> &nbsp; </em> </strong></p>
<p style="text-align: justify;">Yo <b><?= $datosEstudianteActual["uss_nombre"];?></b> mayor de edad, vecino (a) de Medell&iacute;n, identificado (a) con la c&eacute;dula de ciudadan&iacute;a n&uacute;mero <b><?= $datosEstudianteActual["uss_usuario"];?></b> de <b><?= $datosEstudianteActual["uss_lugar_expedicion"];?></b> por el presente documento me constituyo deudor (a) del INSTITUTO COLOMBO VENEZOLANO con NIT. 890.901.933-2, con domicilio en Medell&iacute;n por la suma de __________________________________________________________________</p>
<p style="text-align: justify;">_________________________($____________) m&aacute;s intereses sobre la suma debida se plazo a la tasa de _______% mensual,&nbsp; declarando que PAGARE A MI ACREEDOR O A SU ORDEN, en _____________________y que pagar&eacute; a partir del d&iacute;a ______de _______hasta el total de la suma debida; quedando establecido que&nbsp; la mora en el pago de una de las cuotas estipuladas facultar&aacute; al ACREEDOR&nbsp; para dar por terminado el plazo convenido y exigir en forma inmediata la totalidad del valor pendiente.&nbsp; En caso de mora reconocer&eacute; intereses sobre el saldo pendiente a la tasa m&aacute;xima legal mensual vigente, determinada por la Superintendencia Financiera, siendo de mi cargo el pago de las costas y gastos del cobro judicial o extrajudicial a que haya lugar.</p>
<p style="text-align: justify;">Autorizo expresamente al INSTITUTO COLOMBO VENEZOLANO para que la informaci&oacute;n suministrada en el presente documento sea consultada, verificada, usada y puesta en circulaci&oacute;n con terceras personas incluyendo bancos, bases de datos, con fines estrictamente comerciales.&nbsp;&nbsp; Igualmente autorizo expresamente para que en caso de incumplimiento de la obligaci&oacute;n sea reportado al banco de datos o a cualquier otro.</p>
<p style="text-align: justify;">Que PAGAR&Eacute; el capital indicado en (___) cuotas de la siguiente manera:</p>
<p style="text-align: justify;">Cuotas mensuales de $___________que se cancelar&aacute;n a m&aacute;s tardar el &uacute;ltimo d&iacute;a de cada mes. Este titulo valor presenta merito ejecutivo.</p>
<p style="text-align: justify;">Renunciamos expresamente a constituci&oacute;n en mora.&nbsp; En todos los casos de &eacute;ste t&iacute;tulo valor PAGAR&Eacute; y para todos los efectos, ser&aacute; suficiente prueba de incumplimiento, la simple manifestaci&oacute;n al respecto por parte del acreedor, sin necesidad de requerimiento alguno.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Para constancia firmo el presente &ldquo;TITULO VALOR&rdquo; en la ciudad de Medell&iacute;n a los <b><?=date("d");?></b> d&iacute;as del mes de <b><?=date("M");?></b></p>
<p style="text-align: justify;">&nbsp;</p>




						            <form action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
						                <input type="hidden" name="id" value="21">

						                

						                <?php if ($datosEstudianteActual["mat_contrato"] == 1 AND $datosEstudianteActual["mat_pagare"] == '0') { ?>
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