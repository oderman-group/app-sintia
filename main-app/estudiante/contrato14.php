<?php include("session.php");?>
<?php
$_SESSION["bd"] = 2022;
?>
<?php include("verificar-usuario.php");?>
<?php include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'ES0043';?>
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
                                <div class="page-title">Contrato mayores de 14 años</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                       						

						<div class="col-sm-9">
						    <div class="card card-box">
						        <div class="card-head">
						            <header>Contrato mayores de 14 años</header>
						        </div>
						        <div class="card-body " id="bar-parent6">

                                <p style="text-align: justify;">&nbsp;</p>
<h4 style="text-align: center;">ACTA DE ACEPTACI&Oacute;N Y DE EXENCI&Oacute;N.</h4>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">A trav&eacute;s de mi firma y aceptaci&oacute;n de la presente acta conexa al Contrato de la Matricula de mi acudido(a):</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><b><?= $datosEstudianteActual["mat_primer_apellido"]." ".$datosEstudianteActual["mat_segundo_apellido"]." ".$datosEstudianteActual["mat_nombres"]; ?></b> Del Grado: <b><?= $datosEstudianteActual["gra_nombre"]; ?></b></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Yo, <b><?= $datosEstudianteActual["uss_nombre"];?></b> Cedula: <b><?= $datosEstudianteActual["uss_usuario"];?></b></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Eximo, a las Directivas, Rector(a), Coordinadores, Docentes y Consejo Directivo de la Instituci&oacute;n Educativa, <strong>INSTITUTO COLOMBO VENEZOLANO / ICOLVEN</strong>, respecto de cualquier tipo de responsabilidad penal, civil, disciplinaria, administrativa, que emerja como resultado de las actuaciones er&oacute;tico &ndash; sexuales protagonizadas por mi acudido(a).</p>
<p style="text-align: justify;">Quien al tenor de los art&iacute;culos 14&ordm;; art&iacute;culo 139&ordm; de ley 1098 de 2006, y art&iacute;culos 12 al 15 de Ley 1146&ordm; de 2007, y en armon&iacute;a con el art&iacute;culo 25&ordm; y 209&ordm; del C&oacute;digo Penal Colombiano, es enteramente Judicializable.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Puesto, que a trav&eacute;s de la presente, declaro, que he sido informado(a) y conozco, que dentro del Manual de Convivencia Escolar, est&aacute; clarificado taxativamente, estipulado y definido, que al momento de presentarse una situaci&oacute;n tipo III, es decir un hecho punible en materia de <strong>ACTOS SEXUALES ABUSIVOS</strong>, cuando presuntamente mi acudido(a), desarrolle actuaciones er&oacute;tico sexuales con un(a) menor de catorce (14) a&ntilde;os de edad, o EN PRESENCIA de los(las) menores de catorce (14) a&ntilde;os escolarizados, dentro de las instalaciones del Plantel, o por fuera del mismo portando el uniforme, su proceder, ser&aacute; inmediatamente denunciado ante las autoridades pertinentes.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Que que mediatamente denunciado ante las autoridades pertinentes.enores de catorce a&ntilde;os&nbsp; que mi acudido(a), desarrolle actuaciones eajustado al debido proceso pertinente, <strong>ello, NO corresponde a una situaci&oacute;n o actuaci&oacute;n de discriminaci&oacute;n,</strong> sino que es armoniosa con la exigencia de la normativa penal en Colombia, <em>(Art. 25&ordm; C&oacute;digo Penal; Articulo 44 numeral 9 Ley 1098 de 2006; Art&iacute;culos 12, 13, 14, 15 de Ley 1146 de 2007, art&iacute;culo 209&ordm; del C&oacute;digo Penal). </em><strong>Que mucho menos corresponde a una situaci&oacute;n o actuaci&oacute;n de inducci&oacute;n al suicidio o de hostigamiento</strong> sino que es en estricto acato al debido proceso, que corresponde a las consecuencias propias del proceder y actuaciones &ndash;presuntamente- irregulares de mi acudido(a):</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><b><?= $datosEstudianteActual["mat_primer_apellido"]." ".$datosEstudianteActual["mat_segundo_apellido"]." ".$datosEstudianteActual["mat_nombres"]; ?></b> Del Grado: <b><?= $datosEstudianteActual["gra_nombre"]; ?></b></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Que tengo pleno conocimiento, de que el proceder de mi acudido(a) en lo er&oacute;tico &ndash; sexual, dada su calidad de adolescente Mayor de Catorce (14) a&ntilde;os de edad, y enteramente Judicializable, debe ser decorosa, mesurada, digna y ejercida de una manera respetuosa, responsable y consciente, adem&aacute;s madura de acuerdo a su calidad cognitiva y volitiva. <strong>Que por ning&uacute;n motivo, debe sostener, mantener o protagonizar actos sexuales, caricias o besos con menores de catorce (14) a&ntilde;os o desarrollarlos con mayores de catorce a&ntilde;os, EN PRESENCIA DE MENORES DE CATORCE (14) A&Ntilde;OS, que al tenor del C&oacute;digo Penal Colombiano, se tipifican como ACTOS SEXUALES ABUSIVOS. </strong></p>
<p style="text-align: justify;"><strong>&nbsp;</strong></p>
<p style="text-align: justify;">Que le brindar&aacute; una calidad de respeto y de mesura a sus actividades inherentes a su noviazgo, con pares Mayores de Catorce (14) a&ntilde;os, y que responder&aacute; penalmente por sus actuaciones contrarias a la normativa jur&iacute;dica, independiente de su condici&oacute;n heterosexual, homosexual, l&eacute;sbica, bisexual, intersexual u otra.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Que yo, como acudiente y como representante legal, he sido informado(a) de lo anterior, y que ello, me hace penal, civil, disciplinaria y administrativamente responsable como parte de mi corresponsabilidad parental. (Art&iacute;culo 14&ordm; de ley 1098 de 2006).</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">De manera, que me acojo a los lineamientos, c&aacute;nones y directrices contenidos dentro del manual de convivencia escolar del INSTITUTO COLOMBO VENEZOLANO / ICOLVEN, a ese respecto y siempre actuando acorde al debido proceso y a las normas jur&iacute;dico legales vigentes.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Que he sido enteramente, informado(a), de que en el momento que mi acudido(a), incurra en un presunto delito o infracci&oacute;n de ley, o situaci&oacute;n Tipo III, el hecho ser&aacute; denunciado a las autoridades pertinentes.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Que adem&aacute;s, soy consciente, de que en todos los casos, en que la Instituci&oacute;n Educativa, desarrolle de manera integral, proporcional, imparcial y de manera licita el debido proceso, y elabore las respectivas actas del mismo, para dejar registro documental, yo, como acudiente, acudir&eacute; a acatar, respetar, participar y avalar ese debido proceso, y eximir&eacute; a las Directivas, Rector(a), Coordinadores, Docentes y Consejo Directivo de la Instituci&oacute;n Educativa, <strong>INSTITUTO COLOMBO VENEZOLANO / ICOLVEN</strong>, respecto de cualquier tipo de responsabilidad penal, civil, disciplinaria, administrativa, que emerja como resultado de las actuaciones er&oacute;tico &ndash; sexuales protagonizadas por mi acudido(a).</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Que lo anterior, obedece a que me acojo a lo que taxativamente se&ntilde;ala el manual de convivencia, cuando en armon&iacute;a con la Jurisprudencia se&ntilde;ala:</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><em>&ldquo;El proceso educativo exige no solamente el cabal y constante ejercicio de la funci&oacute;n docente y formativa por parte del establecimiento, sino la colaboraci&oacute;n del propio alumno y el concurso de sus padres o acudientes.&nbsp; </em></p>
<p style="text-align: justify;"><em>Estos tienen la obligaci&oacute;n, prevista en el art&iacute;culo 67&ordm; de la Constituci&oacute;n, de concurrir a la formaci&oacute;n moral, intelectual y f&iacute;sica del menor y del adolescente, pues "el Estado, la sociedad y la familia son responsables de la educaci&oacute;n". No contribuye el padre de familia a la formaci&oacute;n de la personalidad ni a la estructuraci&oacute;n del car&aacute;cter de su hijo cuando, so pretexto de una mal entendida protecci&oacute;n paterna -que en realidad significa cohonestar sus faltas-, obstruye la labor que adelantan los educadores cuando lo corrigen, menos todav&iacute;a si ello se refleja en una actitud agresiva e irrespetuosa&rdquo;. (Sentencia T- 366 de 1997)</em></p>
<p style="text-align: justify;"><em>&nbsp;</em></p>
<p style="text-align: justify;">Que adem&aacute;s de lo anterior, soy consciente de que acudo a matricular a mi acudido(a) y que ello, me obliga a aceptar el Canon que est&aacute; contenido en el manual de convivencia del <strong>INSTITUTO COLOMBO VENEZOLANO / ICOLVEN</strong>, como lo indica la Jurisprudencia cuando se&ntilde;ala:</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><em>Sentencia T- 612 de 1992. Contrato de Matr&iacute;cula: Que "Al momento de matricularse una persona en un Centro Educativo celebra por ese acto un Contrato de Naturaleza Civil; un contrato es un acuerdo de voluntades para crear obligaciones".</em></p>
<p style="text-align: justify;"><em>&nbsp;</em></p>
<p style="text-align: justify;"><em>Sentencia C &ndash; 555 de 1994. Que <strong>"La exigibilidad de esas reglas m&iacute;nimas al alumno resulta acorde con sus propios derechos y perfectamente leg&iacute;tima cuando se encuentran consignadas en el Manual de Convivencia que &eacute;l y sus acudientes, firman al momento de establecer la vinculaci&oacute;n educativa</strong>. Nadie obliga al aspirante a suscribir ese documento, pero concedida la oportunidad de estudio, si reiteradamente incumple pautas m&iacute;nimas y denota desinter&eacute;s o grave indisciplina puede ser tomado en cuenta como motivo de exclusi&oacute;n. (Negrilla Fuera del Texto).</em></p>
<p style="text-align: justify;"><em>&nbsp;</em></p>
<p style="text-align: justify;"><em>Sentencia T- 527 de 1995. Que "La funci&oacute;n social que cumple la Educaci&oacute;n hace que dicha garant&iacute;a se entienda como un derecho - deber <strong>que genera para el educador como para los educandos y para sus progenitores un conjunto de obligaciones rec&iacute;procas que no pueden sustraerse;</strong> ello implica que los Planteles Educativos puedan y deban establecer una serie de normas o reglamentos en donde se viertan las pautas de comportamiento que deben seguir las partes del proceso Educativo". (Negrilla Fuera del Texto).</em></p>
<p style="text-align: justify;"><em>&nbsp;</em></p>
<p style="text-align: justify;">Y por ello, a trav&eacute;s de la presente, acudo a reafirmar, que asumo las consecuencias penales, civiles, administrativas y disciplinarias que correspondan a las actuaciones, omisiones y situaciones protagonizadas por mi acudido(a); dado que acudo como corresponsable de las mismas.</p>
<p style="text-align: justify;">Adem&aacute;s de que soy abierto(a) conocedor(a) de que su Libre Desarrollo de la Personalidad, est&aacute; condicionado o limitado a que NO afecte negativamente a terceros, como lo indica la Jurisprudencia as&iacute;:</p>
<p style="text-align: justify;"><em>&nbsp;</em></p>
<p style="text-align: justify;"><em>&ldquo;</em><em>A</em><em>l&nbsp;&nbsp; interpretar el&nbsp;&nbsp; art&iacute;culo 16 de la Constituci&oacute;n que consagra el derecho al libre desarrollo de la personalidad, la corte constitucional y la doctrina han entendido que: ―ese derecho consagra una protecci&oacute;n general de la capacidad que la Constituci&oacute;n reconoce a las personas para auto determinarse, <strong>esto es, a darse sus propias normas y desarrollar planes propios de vida, siempre y cuando no afecten derechos de terceros&rdquo;.&nbsp; </strong>Sentencia C-481 de 1998. (Negrilla Fuera de Texto).</em></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><em>&ldquo;</em><em>La aplicaci&oacute;n de la disciplina en el establecimiento educativo no implica de suyo la violaci&oacute;n de derechos fundamentales. Pero los profesores y directivas est&aacute;n obligados a respetar la dignidad del estudiante: La Corte Constitucional insiste en que toda comunidad requiere de un m&iacute;nimo de orden y del imperio de la autoridad para que pueda subsistir en ella una civilizada convivencia, <strong>evitando el caos que podr&iacute;a generarse si cada individuo, sin atender reglas ni preceptos, hiciera su absoluta voluntad, aun en contrav&iacute;a de los intereses comunes, en un mal entendido concepto del derecho al libre desarrollo de la personalidad&rdquo;. </strong>Sentencia T-366 de 1992. (Negrilla Fuera del Texto).</em></p>
<p style="text-align: justify;"><em>&nbsp;</em></p>
<p style="text-align: justify;"><em>"</em><em>La disciplina, que es indispensable en toda organizaci&oacute;n social para asegurar el logro de sus fines dentro de un orden m&iacute;nimo, resulta inherente a la educaci&oacute;n, en cuanto hace parte insustituible de la formaci&oacute;n del&nbsp; &nbsp;individuo. <strong>Pretender que, por una err&oacute;nea concepci&oacute;n del derecho al libre desarrollo de la personalidad, las instituciones educativas renuncien a exigir de sus alumnos comportamientos acordes con un r&eacute;gimen disciplinario al que est&aacute;n obligados desde su ingreso, </strong>equivale a contrariar los objetivos propios de la funci&oacute;n formativa que cumple la educaci&oacute;n". (Sentencia T- 037 de 1995). (Negrilla Fuera del Texto).</em></p>
<p style="text-align: justify;"><em>&nbsp;</em></p>
<p style="text-align: justify;">Que firmo la presente acta en conexidad con la Matricula de mi hijo(a), como una muestra indefectible de mi acato, respeto y aceptaci&oacute;n de las normas que se me imponen como acudiente y representante legal, y que se le imponen y se le exigen a mi acudido(a) al momento de matricularse en EL INSTITUTO COLOMBO VENEZOLANO / ICOLVEN<strong>, </strong>dado que su condici&oacute;n de mayor de catorce (14) a&ntilde;os, lo hace penalmente responsable por sus acciones, omisiones y dem&aacute;s actuaciones que violen o desconozcan el C&oacute;digo penal Colombiano.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">Acepto y declaro, que estoy abierta y debidamente informado(a).</p>
<p style="text-align: justify;">&nbsp;</p>





						            <form action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
						                <input type="hidden" name="id" value="24">

						                

						                <?php if ($datosEstudianteActual["mat_manual"] == 1 AND $datosEstudianteActual["mat_mayores14"] == '0') { ?>
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