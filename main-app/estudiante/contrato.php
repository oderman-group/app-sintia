<?php include("session.php");?>
<?php
$_SESSION["bd"] = 2022;
?>
<?php include("verificar-usuario.php");?>
<?php include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'ES0044';?>
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
                                <div class="page-title">Contrato</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                       						

						<div class="col-sm-9">
						    <div class="card card-box">
						        <div class="card-head">
						            <header>Contrato</header>
						        </div>
						        <div class="card-body " id="bar-parent6">

<h2 style="text-align: justify;">CONTRATO DE PRESTACI&Oacute;N DE SERVICIOS EDUCATIVOS</h2>
<p style="text-align: justify;"><strong>&nbsp;</strong></p>
<p style="text-align: justify;"><strong>&nbsp;</strong></p>
<p style="text-align: justify;"><strong>ESTUDIANTE: <?= $datosEstudianteActual["mat_primer_apellido"]." ".$datosEstudianteActual["mat_segundo_apellido"]." ".$datosEstudianteActual["mat_nombres"]; ?></strong></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>GRADO: <?= $datosEstudianteActual["gra_nombre"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </strong></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Entre los suscritos a saber Luciamparo Cely Quiroz, mayor de edad, vecino del Municipio de Medell&iacute;n, portador de la C&eacute;dula de Ciudadan&iacute;a No. 46.362.031 en mi calidad de Rectora y Representante Legal del INSTITUTO COLOMBO VENEZOLANO, instituci&oacute;n de educaci&oacute;n Preescolar, B&aacute;sica Primaria, Secundaria y Media con personer&iacute;a jur&iacute;dica emitida por el Ministerio de Educaci&oacute;n Nacional mediante Resoluci&oacute;n No. 6173 del 19 de diciembre 1958 y 018561 del 22 de diciembre de 2016, entidad que en adelante se denominar&aacute; Instituto Colombo Venezolano, por una parte. Y por la otra parte, el (la) se&ntilde;or(a) <b><?= $datosEstudianteActual["uss_nombre"];?></b>, mayor de edad, identificado(a) con la C&eacute;dula de Ciudadan&iacute;a No.<b><?= $datosEstudianteActual["uss_usuario"];?></b> Expedida en <b><?= $datosEstudianteActual["uss_lugar_expedicion"];?></b>&nbsp;&nbsp;&nbsp;&nbsp; , actuando en su nombre propio y en su condici&oacute;n de padre o acudiente del (la) estudiante, quien para efecto de este contrato se denominar&aacute; EL ACUDIENTE; se ha convenido celebrar el presente contrato de cooperaci&oacute;n educativa, que se regir&aacute; por las siguientes cl&aacute;usulas: <strong>PRIMERA-Misi&oacute;n. </strong>El INSTITUTO COLOMBO VENEZOLANO se define como un plantel educativo sin &aacute;nimo de lucro, que desarrolla su labor enmarcada en el servicio a Dios, a la comunidad y a la sociedad en general. El trabajo del conocimiento se fundamenta en tres pilares: la formaci&oacute;n integral, la cultura investigativa y la excelencia en el servicio, donde el hombre es el centro del proceso educativo que persigue el desarrollo arm&oacute;nico de los aspectos f&iacute;sicos, mentales y espirituales. La misi&oacute;n se desarrollar&aacute; en procura de altos niveles de calidad educativa, a trav&eacute;s de un personal calificado y con profundo sentido de compromiso, apoyado en el uso &oacute;ptimo de los recursos f&iacute;sicos, financieros y tecnol&oacute;gicos. La misi&oacute;n se cumplir&aacute; con el compromiso y la acci&oacute;n de todos los estamentos institucionales tanto en el aula como en las actividades que se ejecuten fuera de ella y que propendan por la formaci&oacute;n integral. <strong>SEGUNDA-Definici&oacute;n y naturaleza</strong> <strong>del contrato.</strong> El presente contrato formaliza la vinculaci&oacute;n del educando al servicio educativo que ofrece el INSTITUTO COLOMBO VENEZOLANO y compromete a las partes y al educando en las obligaciones legales pedag&oacute;gicas y formativas, tendientes a hacer efectiva la prestaci&oacute;n del servicio privado educativo, obligaciones que son solidarias, colectivas y esenciales para la consecuci&oacute;n del objeto y de los fines comunes, toda vez que el derecho a la educaci&oacute;n representa un derecho-deber. <strong>TERCERA</strong>. <strong>Objeto.</strong> El objeto del presente contrato es procurar la formaci&oacute;n integral del educando en sus aspectos cognitivos, humanos y espirituales, mediante los esfuerzos solidarios y de corresponsabilidad del Estudiante, de los padres y/o acudiente y del Colegio, en la b&uacute;squeda del desarrollo de la personalidad, autonom&iacute;a y dignidad humana del Educando, con el prop&oacute;sito de que este obtenga un rendimiento acad&eacute;mico de calidad y una formaci&oacute;n con principios y valores humanos, a trav&eacute;s del ejercicio del programa curricular que &iacute;ntegra el Proyecto Educativo Institucional (PEI) del Colegio. <strong>CUARTA</strong>&ndash; Los padres y /o acudientes y Estudiante, se obligan a aceptar y cumplir con el Proyecto Educativo Institucional del Colegio, las normas acad&eacute;micas vigentes y el Manual de Convivencia del Colegio INSTITUTO COLOMBO VENEZOLANO, los cuales desde ya declaran conocer y aceptar de manera expresa y que forman parte integral del presente contrato. <strong>QUINTA</strong> &ndash; Los padres y/o acudiente y Estudiante, declaran haber escogido libremente al INSTITUTO COLOMBO VENEZOLANO de Medell&iacute;n, por su formaci&oacute;n integral (acad&eacute;mica, espiritual y moral), ya que desea que sea esta la educaci&oacute;n que imparta a su acudido. Por lo tanto solicitan y aceptan expresamente la formaci&oacute;n cristiana con sus implicaciones sociales, aceptando como parte de dicha formaci&oacute;n todas las actividades programadas por el COLEGIO para tal fin. <strong>SEXTA</strong>&ndash; Los padres y/o acudientes y estudiantes, se obligan a consultar todo lo relacionado con el desarrollo acad&eacute;mico, bolet&iacute;n de calificaciones de cada per&iacute;odo, circulares y dem&aacute;s informaci&oacute;n, en la p&aacute;gina web y Plataforma Virtual de la instituci&oacute;n (www.plataformasintia.com/icolven). <strong>S&Eacute;PTIMA</strong> &ndash; Los Padres y/o acudientes, se comprometen a asistir a las reuniones de Padres de Familia tanto las oficiales como de talleres, as&iacute; como acudir diligentemente a las dem&aacute;s citaciones elevadas por los profesores o por las autoridades del Colegio y desde ya autorizan al Colegio a que no obstante el derecho a la identidad de los Estudiantes menores de edad, estos pueden ser sujetos de entrevistas, de fotograf&iacute;as, filmaciones, videos y de cualquier otro medio de transmisi&oacute;n o publicitario por parte del Colegio, con fines estrictamente de divulgaci&oacute;n educativa, pedag&oacute;gica, deportiva, de investigaci&oacute;n acad&eacute;mica y en fin, de inter&eacute;s de la Comunidad del INSTITUTO COLOMBO VENEZOLANO.<strong> OCTAVA</strong> &ndash; Los Padres y/o acudientes, se obligan a cancelar el valor correspondiente de la Matr&iacute;cula, Pensi&oacute;n y Otros costos y a pagar la pensi&oacute;n mensual dentro de los diez (10) primeros d&iacute;as de cada mes, mes anticipado, de febrero a noviembre del presente a&ntilde;o y todos los pagos que por los dem&aacute;s conceptos se causen. Igualmente aceptan cancelar los intereses moratorios a la tasa m&aacute;xima Legal vigente conforme a lo autorizado por la Superintendencia Financiera por falta de pago, as&iacute; como los gastos y/o honorarios de cobranza extrajudicial y judicial si hubiere lugar a ella; Tambi&eacute;n Los Padres y/o acudientes, y Estudiantes, aceptamos cancelar todo da&ntilde;o f&iacute;sico o material causado por el Estudiante a las instalaciones, muebles y material did&aacute;ctico puesto al servicio de sus hijos (as). <strong>NOVENA</strong> &ndash; Los Padres y/o acudientes, se obligan a constituir y aceptar un pagar&eacute; en blanco, con firma y huella para el pago de las obligaciones insolutas derivadas del presente contrato, de conformidad con el art. 622 del C&oacute;digo de Comercio, de acuerdo a las instrucciones insertas en el pagar&eacute; as&iacute;: Los Padres y /o Acudientes autorizan en forma irrevocable al COLEGIO para llenar el pagar&eacute; otorgado a su favor y suscrito el d&iacute;a de la Matr&iacute;cula con los espacios relativos a la cuant&iacute;a intereses y fecha de vencimiento en blanco en cualquier tiempo y sin previo aviso, de acuerdo con las siguientes instrucciones: 1) La cuant&iacute;a ser&aacute; igual al monto de todas las sumas adeudadas al COLEGIO, hasta el d&iacute;a en que sea llenado el Pagar&eacute;.2) Los intereses ser&aacute;n los moratorios a la tasa m&aacute;xima legal vigente, debidamente certificados por la Superintendencia Financiera. 3) La fecha de exigibilidad del Pagar&eacute; ser&aacute; la del d&iacute;a en que el t&iacute;tulo sea llenado. En caso de mora en la cancelaci&oacute;n de cualquiera de estas obligaciones a cargo de cualquiera de los aceptantes del Pagar&eacute;, ser&aacute;n exigibles inmediatamente las obligaciones existentes a la fecha sin necesidad de requerimiento extrajudiciales ni judiciales, ni constituci&oacute;n en mora, a lo cual renuncia y desde ya autoriza al COLEGIO INSTITUTO COLOMBO VENEZOLANO de Medell&iacute;n para exigir de inmediato el pago de todos y cada uno de los cr&eacute;ditos a cargo de los obligados en el Pagar&eacute;, por v&iacute;a ejecutiva o cualquier otro medio legal.<strong> D&Eacute;CIMA</strong>&ndash; La no cancelaci&oacute;n de la totalidad de las obligaciones contra&iacute;das con el COLEGIO en el a&ntilde;o acad&eacute;mico 2022 y anteriores, dar&aacute; derecho a la Instituci&oacute;n Educativa para no conceder la matr&iacute;cula del (la) Estudiante en el siguiente a&ntilde;o escolar 2022, sin perjuicio de las acciones legales, pertinentes para el cobro jur&iacute;dico de los valores a su cargo. <strong>D&Eacute;CIMA PRIMERA</strong> &ndash; Desvinculaci&oacute;n: El v&iacute;nculo con la instituci&oacute;n cesa al t&eacute;rmino de la vigencia del contrato de servicios educativos por: A. Decisi&oacute;n de retiro aut&oacute;nomo y libre de los padres de familia, o por decisi&oacute;n de las directivas de la Instituci&oacute;n, o cuando se configuren una de las siguientes causales de terminaci&oacute;n del contrato: 1. Ocultamiento, tergiversaci&oacute;n y cualquier forma de falsedad comprobada en la informaci&oacute;n y documentaci&oacute;n requerida por la Instituci&oacute;n; 2. No alcanzar los logros acad&eacute;micos seg&uacute;n los criterios de desempe&ntilde;o y promoci&oacute;n vigentes en la Instituci&oacute;n; 3. Falta de compromiso constatado y/o por intromisi&oacute;n indebida, permanente y de mala fe de los padres de familia o acudientes en la ejecuci&oacute;n cotidiana de las funciones educativas institucionales; 4. Desacato frecuente a la autoridad acad&eacute;mica y a sus decisiones; 5. Desconocimiento mal intencionado de los procesos, normas, requerimientos y pactos establecidos entre las directivas, los padres de familia y/o acudientes y estudiantes; 6. Instigar al desconocimiento de los compromisos pactados; a la intolerancia o difamaci&oacute;n en contra de las personas, la tradici&oacute;n y la &eacute;tica institucional; 7. Uso comprobado de an&oacute;nimos de contenido difamatorio hacia la instituci&oacute;n o personas vinculadas a ella; 8. Suplantaci&oacute;n da&ntilde;ina del nombre de personas y/o familias del colegio. B. Expulsi&oacute;n o p&eacute;rdida del cupo del estudiante. <strong>DECIMA SEGUNDA</strong>. Para los fines fiscales, se autoriza al Colegio para reportar a la DIAN Y certificar los pagos derivados del presente contrato, en su orden: Padre de Familia (1), Acudiente (2). <strong>D&Eacute;CIMA</strong> <strong>TERCERA</strong>. Los firmantes, Padre de Familia (1) y/o Acudiente (2), manifiestan conocer a trav&eacute;s de la P&aacute;gina del Colegio (www.icolven.edu.co) el texto completo del presente contrato y del Manual de Convivencia. <strong>DECIMA CUARTA</strong>. &ndash; La vigencia del este contrato se extiende hasta la finalizaci&oacute;n del presente a&ntilde;o lectivo 2022 (Calendario A).</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Para constancia se firma en Medell&iacute;n por quienes intervinieron en este contrato a los <b><?= date("d");?></b> d&iacute;as del mes de <b><?= date("M");?></b> del a&ntilde;o <b><?= date("Y");?></b>.</p>





						            <form action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
						                <input type="hidden" name="id" value="20">

						                

						                <?php if ($datosEstudianteActual["mat_pago_matricula"] == 1 AND $datosEstudianteActual["mat_contrato"] == '0') { ?>
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