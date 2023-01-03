<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
switch($_POST["documento"]){
	case 1: $titulo = "CONTRATO DE PRESTACIÓN DE SERVICIO EDUCATIVO"; break;
	case 2: $titulo = "COLEGIO CELCO DEL MPIO DE PAZ DE ARIPORO"; break;
	case 3: $titulo = "COLEGIO CELCO DE PAZ DE ARIPORO<br>COMPROMISO ESPECÍFICO DE INGRESO<br>AÑO 2018"; break;
	case 4: $titulo = "FICHA DE SALUD 2018"; break;
	case 5: $titulo = "COLEGIO CELCO PAZ DE ARIPORO<br>JORNADA UNICA<br>SEGUIMIENTO DEL EDUCANDO<br>AÑO 2018<br>Seguimiento 1"; break;
	case 6: $titulo = "COLEGIO CELCO PAZ DE ARIPORO<br>JORNADA UNICA<br>SEGUIMIENTO DEL EDUCANDO<br>AÑO 2018<br>Seguimiento 2"; break;
	case 7: $titulo = "OBSERVADOR DEL ESTUDIANTE 2018"; break;
}

$consultaDatos=mysqli_query($conexion, "SELECT * FROM academico_matriculas
INNER JOIN academico_grados ON gra_id=mat_grado
INNER JOIN academico_grupos ON gru_id=mat_grupo
INNER JOIN usuarios ON uss_id=mat_acudiente
WHERE mat_id='".$_POST["estudiante"]."'");
$datos = mysqli_fetch_array($consultaDatos, MYSQLI_BOTH);

$consultaDg=mysqli_query($conexion, "SELECT * FROM academico_cargas
INNER JOIN usuarios ON uss_id=car_docente
WHERE car_curso='".$datos['mat_grado']."' AND car_grupo='".$datos['mat_grupo']."' AND car_director_grupo=1");
$dg = mysqli_fetch_array($consultaDg, MYSQLI_BOTH);

$consultaAcudiente2=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$datos["mat_acudiente2"]."'");
$acudiente2 = mysqli_fetch_array($consultaAcudiente2, MYSQLI_BOTH);

$meses=array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
?>
<head>
	<title><?=$titulo;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial; font-size:11px;">

<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="800"><br>
    <h2 style="font-weight:bold;"><?=$titulo;?></h2></br>
</div>   

<?php
if($_POST["documento"]==1){
?>
<div style="margin:5px; line-height:20px;">
Entre los suscritos a saber <b>COLEGIO CELCO DEL MPIO DE PAZ DE ARIPORO</b> identificado con NIT.: 844.002.546-0, establecimiento educativo representado legalmente por <b>WILLIAM ALEXANDER VARELA GARCIA</b> persona mayor de edad, domiciliada en esta ciudad, identificado con cédula de ciudadanía No 1.115.851.447 de Paz de Ariporo o a quien represente sus derechos  y para todos los efectos del presente contrato se denominará <b>COLEGIO CELCO</b> y de otra parte <b><?=strtoupper($datos['uss_nombre']);?></b> persona mayor de edad, domiciliado en esta ciudad Paz de Ariporo, residenciado en <b><?=strtoupper($datos['uss_direccion']);?></b> identificado con cédula de ciudadanía No. <b><?=strtoupper($datos['uss_usuario']);?></b> de <b><?=strtoupper($datos['uss_lugar_expedicion']);?></b> Y <b><?=strtoupper($acudiente2['uss_nombre']);?></b>, persona mayor de edad domiciliada en esta ciudad Paz de Ariporo, residenciada en <b><?=strtoupper($acudiente2['uss_direccion']);?></b> identificada con cédula de ciudadanía No <b><?=strtoupper($acudiente2['uss_usuario']);?></b> de <b><?=strtoupper($acudiente2['uss_lugar_expedicion']);?></b>, quienes actúan en nombre y representación del estudiante <b><?=strtoupper($datos["mat_primer_apellido"].' '.$datos["mat_segundo_apellido"].' '.$datos["mat_nombres"]);?></b> identificado con la T. I  ó NUIP <b><?=strtoupper($datos['mat_documento']);?></b> en calidad de padre (s) o acudiente (s), quienes en adelante y para los efectos del presente contrato se denominaran  los <b>ACUDIENTES</b> , manifiestan que han celebrado, <b>CONTRATO DE PRESTACIÓN DE SERVICIO EDUCATIVO</b>, el cual se rige por las siguientes cláusulas:
<b>PRIMERA.- FUNDAMENTOS QUE ORIENTAN LA EDUCACIÓN  EN COLOMBIA</b>.- La constitución política de Colombia de 1991 establece en sus artículos 26, 27, 42, 44, 45, 68, y 70, los derechos que le asisten a los niños y jóvenes de recibir una educación integral adecuada, señalando igualmente los deberes y derechos que le asisten  a los padres y centros educativos para proporcionarla.

<b>SEGUNDA</b>.- EL presente  es un contrato de servicio educativo que obedece a las disposiciones constitucionales en las cuales se establece una responsabilidad compartida de la educación, en donde ocurren obligaciones de los educadores, los educandos y padres de familia o acudientes, tendientes a hacer efectiva la prestación del servicio público educativo en función social por parte de los colegios privados, de manera que el incumplimiento de cualquiera de las obligaciones adquiridas por los contratantes, hace imposible la consecución del fin común. Por lo tanto, las obligaciones que se adquieren en el presente contrato son correlativas y esenciales para el fin común. 
<b>TERCERA.- OBJETO DEL CONTRATO:</b> Es el de conseguir la reciproca complementación de esfuerzos entre los padres  del beneficiario y el colegio  para obtener un rendimiento académico satisfactorio del programa curricular correspondiente al grado <b><?=strtoupper($datos['gra_nombre']);?></b> aprobado  por el Ministerio de Educación Nacional y el Proyecto Educativo Institucional, en orden a conseguir su educación integral. 

<b>CUARTA.- NATURALEZA JURÍDICA:</b> El presente contrato es de  carácter civil y tiene todos sus efectos, según lo estipulado en el artículo 95 y 201 de la ley 115/94. 

<b>QUINTA.- CARÁCTER JURÍDICO DEL ESTABLECIMIENTO EDUCATIVO:</b> EL COLEGIO CELCO DEL MPIO DE PAZ DE ARIPORO es de carácter privado (Art. 60 C. N y Art. 138 ley 115/94) de confesión religiosa evangélica Luterana, credo y principios en los cuales los padres  o acudientes  desean, expresan y  tienen plena conciencia que su hijo (a) sea formado en esta según Art 68 C. N. 

<b>SEXTA.-  OBLIGACIONES ESENCIALES DEL CONTRATO:</b> Marco de la ley 115 de 1994.son obligaciones de la esencia de este contrato las siguientes:

<p>
<b>1    Por parte del Beneficiario – Estudiante:</b><br>
a.	Asistir y cumplir las pautas de Promoción Académica.  El incumplimiento de ésta obligación imputable a los Padres de Familia;<br>
b.	Cumplir las disposiciones consagradas en el Proyecto Educativo Institucional y en el Reglamento o Manual de Convivencia del Colegio;<br>
c.	Colocar todas sus capacidades en el logro de los objetivos promociónales de su grado.<br>
d.	Convivir en sana armonía tolerancia y respeto con los demás miembros de la comunidad.<br><br>

<b>2.   Por parte del Colegio.</b><br>
a.	Impartir la Enseñanza contratada; a través de personal idóneo para ello<br>
b.	Cumplir y hacer cumplir el Reglamento o Manual de Convivencia del Colegio;<br>
c.	Colocar todas sus capacidades en el logro de los objetivos promociónales de su grado.<br><br>

<b>3.   Por parte de los Padres</b> <br>
a.	El Pago Oportuno del Costo de Servicio Educativo (Pensiones), dentro de los diez (10) primeros días de cada mes anticipado <br>
b.	A renovar la Matrícula del Educando, para cada Período Académico en los días y horas señalados por el Colegio;<br>
c.	A cumplir con las disposiciones señaladas en el Proyecto educativo Institucional, Reglamento o Manual de Convivencia del Colegio, la Constitución Nacional y la Ley;<br>
d.	Las demás obligaciones consagradas en el Art. 7 Ley 115/94.<br>
e.	Solicitar, obtener y suscribir con el colegio CELCO un pagare, que cubrirá el  valor insoluto por concepto de matriculas y pensiones<br>
f.	Cumplir con la documentación exigida por la secretaría  de educación de Casanare y el COLEGIO CELCO, las que deberá aportarse al momento de la matricula.<br>
g.	Asistir cumplidamente a las citaciones que hagan las directivas del colegio.<br>
h.	velar porque su acudido, cumpla con los deberes académicos  y disciplinarios dentro del COLEGIO CELCO.
</p>

<b>SEPTIMA: OBLIGATORIEDAD:</b> Las obligaciones descritas anteriormente son de estricto cumplimiento,  la violación de algunos de estas por parte de los padres de familia o acudientes implicará que la institución educativa  disolverá el contrato  y como consecuencia y efecto se cancelará la matricula al educando agotando los procedimientos para este fin.  


<b>OCTAVA.- DURACIÓN:</b> El presente contrato tiene como vigencia el año lectivo 2018 el cual se extiende  del 01 de Febrero del 2018 al 30 de Noviembre del mismo año. Su ejecución es sucesiva por periodos mensuales.

<b>NOVENA.- RENOVACIÓN:</b> Según lo estipulado en el Artículo 201 de la Ley 115/94 “EL COLEGIO CELCO condicionará la Renovación del Contrato de Servicios Educativos o Matrícula a:<br>
a.)	Incumplimiento de las Obligaciones Contractuales;<br>
b.)	Incumplimiento de las Normas Internas del Colegio;<br>
c.)	Que los Padres de Familia o Acudientes no efectúen la Matrícula en la fecha señalada por “EL COLEGIO”.<br>
d.)	Incumplimiento de las obligaciones como responsables del Educando.<br><br>

<b>DECIMA.- INCUMPLIMIENTO DEL CONTRATO:</b> Los efectos del incumplimiento del contrato serán: Perdida del cupo, o cancelación de matrícula en los siguientes casos: a. El retraso en dos o más meses de pensión durante el año lectivo e incumplimiento de las  fechas establecidas de pensiones, en la sección sexta, numeral  tres, literal a del que habla este contrato.  b.- El no matricular al hijo(a) en la fecha establecida por COLEGIO;  c.- La no asistencia a dos reuniones o citaciones  de las directivas del COLEGIO CELCO; d.-El no velar por el cumplimiento de los deberes de su hijo(a). e.- El incumplimiento permanente por parte de alumno(a), padres o acudientes, de las obligaciones educativas y/o disciplinarias establecidas por la institución en el manual de convivencia. f-. Por faltar al respeto a los miembros de la comunidad educativa; g-. Las demás causales que trae el proyecto educativo institucional, la ley o las disposiciones educativas en general. 

<b>DECIMA PRIMERA.- VALOR DEL CONTRATO:</b> El presente Contrato tiene un costo o tarifa anual ___________________________________________ (<b>$<?=number_format($datos['gra_valor_matricula'],0,",",".");?></b>), según el incremento autorizado por el M.E.N. ; Los cuales serán cancelados dentro de la Vigencia del Contrato, por los Padres y/o Acudientes, así  PENSION: _______________________________________     (<b>$<?=number_format($datos['gra_valor_pension'],0,",",".");?></b>), que serán pagaderas dentro de los diez (10) primeros días de cada mes o período al cual corresponda.  El valor de la Matrícula será igual a la Tarifa Mensual determinada por El Colegio CELCO atendiendo a lo dispuesto por costos aprobados en Secretaría de Educación Departamental.  PARÁGRAFO 1: El retardo en el pago de la Pensión dará derecho a exigir costos por extemporaneidad de acuerdo con las disposiciones legales y vigentes de costos educativos y de acuerdo con las fechas y valores asignados para el año 2018.  En cuanto a cheques devueltos por cualquier concepto el COLEGIO cobrará el recargo normal legal que esté reglamentado en el momento de su liquidación.  El Valor Anual se ajustará cada año según las reglamentaciones gubernamentales respectivas.  

<b>DECIMA SEGUNDA.- CONSULTA Y REPORTE EN LAS CENTRALES DE INFORMACIÓN FINANCIERA:</b> Si no se realiza el pago oportuno de las pensiones, la obligación entrara en mora y serán reportados en las centrales de riesgo aquellos Padres de Familia y/o acudientes responsables económicamente de los estudiantes. 

<b>DECIMA TERCERA.- NOTIFICACIONES:</b> Autorizo que cualquier notificación judicial, extrajudicial, departamento de cartera o institucional será realizada e informada en primera instancia en la plataforma del SGPA, en segunda instancia seré informado a través de circulares, llamadas telefónicas, mensajes de texto, correo electrónico y correo postal. 

<b>DECIMA CUARTA.- RESPALDO DEL PAGO:</b> Para respaldar el pago de la obligación nombrada  en la  sección sexta, numeral  tres, literal a, del que habla este contrato, he firmado un pagaré con espacios en blanco, el cual podrá ser firmado por cualquier tenedor legitimo que se entiende altamente facultado para tal fin, de conformidad con las condiciones pactadas en este contrato. 

<p>
El presente contrato rige a partir de la fecha de su firma por parte de los interesados, en dos ejemplares del mismo tenor en Paz de Ariporo, a los <b><?=date("d");?></b> días del mes de <b><?php echo $meses[date("m")];?></b> del año <b><?=date("Y");?></b>.
</p>

<table width="100%">
<tr>
	<td>
    _______________________________<br>
    WILLIAM ALEXANDER VARELA GARCIA<br>                                                                            
	C. C. 1.115.851.447<br>
    Representante Legal 
    </td>
    
    <td>
    ______________________________<br>
    RESPONSABLE ECONOMICO<br>
    C.C. No.<br>
    (Debe ser la misma firma del pagare) 
    </td>
    
    <td>
    	HUELLA
    </td>
</tr>
</table>                                                                                           

<p>
PADRES/ ACUDIENTES. <br><br>                                                                                     
__________________________ <br>    
C. C. No.  <b><?=strtoupper($datos['uss_usuario']);?></b>
</p>



____________________<br>
ESTUDIANTE<br>
T. I. – NUIP: <b><?=strtoupper($datos['mat_documento']);?></b>
</div>

<?php }
if($_POST["documento"]==2){
?>
<div style="margin:5px; line-height:20px;">
PAGARE No. _____________<br><br>

LUGAR Y FECHA DE CREACIÓN: Paz de Ariporo __________________________________________________<br><br>

ACREEDOR: COLEGIO CELCO DEL MPIO DE PAZ DE ARIPORO <br><br>

DEUDOR:	 <b><?=strtoupper($datos['uss_nombre']);?></b> C.C <b><?=strtoupper($datos['uss_usuario']);?></b><br><br>

FECHA DE VENCIMIENTO DE LA OBLIGACIÓN: __________________________________<br><br>

CUANTIA: ($______________________) __________________________________________________________<br><br>

OBJETO INVERSIÓN EDUCATIVA DE: ______________________________________<br><br>

Yo  <b><?=strtoupper($datos['uss_nombre']);?></b>, <b><?=strtoupper($acudiente2['uss_nombre']);?></b> personas mayor de edad, identificados con cédula de ciudadanía No.<b><?=strtoupper($datos['uss_usuario']);?></b> y <b><?=strtoupper($acudiente2['uss_usuario']);?></b> respectivamente, domiciliados en la ciudad de Paz de Ariporo, obrando como padres y/o acudientes de nuestro menor hijo, expresamos en forma expresa las siguientes: 

<b>PRIMERO.- OBJETO</b>. Que por virtud del presente título valor PAGARE incondicionalmente en dinero efectivo, a la orden del COLEGIO CELCO DEL MPIO DE PAZ DE ARIPORO, reconocida con Resolución N° 001702 del 29 de Octubre de 1998 con domicilio principal en Paz de Ariporo, representada legalmente por el señor WILLIAM ALEXANDER VARELA GARCIA persona mayor de edad, domiciliada en esta ciudad, identificado con cédula de ciudadanía No 1.115.851.447 de Paz de Ariporo o a quien represente sus derechos,  a la que en adelante se llamara COLEGIO CELCO, en la ciudad de Paz de Ariporo en las fechas de amortización, la suma de___________________________________ más los intereses señalados en la cláusula segunda de este documento.
 
<b>SEGUNDO.- PLAZO.</b> Que por virtud del presente título los suscritos en forma indivisible y solidaria nos comprometemos en forma ilimitada nuestra responsabilidad y nos obligamos a pagar incondicionalmente a la orden de  COLEGIO CELCO o quien represente sus derechos, la suma de ___________________________ ($___________) ___________ cuotas iguales, mensuales y sucesivas de ________________________ más el valor con recargo señalados en la cláusula siguiente de este documento.

<b>TERCERA. RECARGO:</b> En caso de mora en el pago de la suma que se adeude reconoceré (mos) recargos por extemporaneidad así:<br>
a.	Del once (11) al veinte (20) de cada mes, el valor de (1.5%) _______________________<br>
b.	Del veintiuno (21) al treinta (30) de cada mes, el valor de (2%) _____________________<br><br>

<b>CUARTA: CLAUSULA ACELERATORIA:</b> En caso de incumplimiento  en los pagos el tenedor podrá declarar vencidos los plazos de esta obligación o de las cuotas que constituyen el saldo incluidos el valor de los intereses legales moratorios, costos, gastos de cobranza y honorarios de abogado, sin necesidad de requerimiento previo y exigir su pago inmediato judicial o extrajudicial en los siguientes casos:<br>
a.	Cuando el (los) deudor (es) incumpla(n) cualquiera de las obligaciones derivadas del presente documento.<br>
b.	Si fueran demandados extrajudicial o judicialmente.<br>
c.	Cuando los deudores se declaren en estado de quiebra, se sometan a proceso concordatorio o convoquen a concurso de acreedores.<br>
Parágrafo: Cuando EL COLEGIO CELCO declare válidamente extinguido o insubsistente el plazo faltante, los intereses moratorios serán liquidados a la tasa que establezca la Superintendencia Bancaria.<br><br>

<b>QUINTA.- GASTOS E IMPUESTOS:</b> Todos los gastos, expensas, tasas o impuestos de timbre de este documento que ocasione la legalización de este pagaré serán de nuestro cargo y autorizamos al COLEGIO para deducirlos a cargo del deudor (es). 

<b>SEXTA.- CONSULTA Y REPORTE EN LAS CENTRALES DE INFORMACIÓN FINANCIERA:</b> Si no se realiza el pago oportuno de las pensiones, la obligación entrara en mora y serán reportados en las centrales de riesgo aquellos Padres de Familia y/o acudientes responsables económicamente de los estudiantes.

<b>SEPTIMO.- NOTIFICACIONES:</b> Autorizo que cualquier notificación judicial, extrajudicial, departamento de cartera o institucional será realizada e informada en primera instancia en la plataforma del SGPA, en segunda instancia seré informado a través de circulares, llamadas telefónicas, mensajes de texto, correo electrónico y correo postal.

<b>OCTAVO.- RESPALDO DEL PAGO:</b> Para respaldar el pago de la obligación nombrada  en la  sección sexta, numeral  tres, literal a, del que habla el contrato de prestación de servicio educativo, he firmado este pagaré con espacios en blanco, el cual podrá ser firmado por cualquier tenedor legitimo que se entiende altamente facultado para tal fin. 

<p>
Para constancia de lo anterior, se firma en la ciudad de Paz de Ariporo a los <b><?=date("d");?></b> días del mes de <b><?php echo $meses[date("m")];?></b> del año <b><?=date("Y");?></b>.
</p>

 

<table width="100%">
<tr>
	<td>
    OTORGANTES<br><br>
    _______________________________<br>
    WILLIAM ALEXANDER VARELA GARCIA<br>                                                                            
	C. C. 1.115.851.447<br>
    Representante Legal 
    </td>
    
    <td>
    DEUDOR (Responsable Económico)<br><br>
    ______________________________<br>
    C.C. No.
    </td>
    
    <td>
    	HUELLA
    </td>
</tr>
</table>
</div>				 
                                                                                                                                  


<?php }
if($_POST["documento"]==3){
?>
<div style="margin:5px; line-height:20px;">
<p>
Yo <b><?=strtoupper($datos["mat_primer_apellido"].' '.$datos["mat_segundo_apellido"].' '.$datos["mat_nombres"]);?></b> en mi calidad de estudiante matriculado en el grado <b><?=strtoupper($datos['gra_nombre']);?></b> ACEPTO LIBREMENTE desde el primer día de ingreso a la institución  el Manual de Convivencia EN TODA SU NORMATIVIDAD, de acuerdo a la filosofía de LA IGLESIA EVANGELICA LUTERANA propietaria del COLEGIO CELCO y me comprometo a cumplir durante el año 2018 TODAS las normas, deberes y compromisos con el apoyo y colaboración de mis padres y/o acudientes <b><?=strtoupper($datos['uss_nombre']);?></b> y <b><?=strtoupper($acudiente2['uss_nombre']);?></b> identificados con c.c. No. <b><?=strtoupper($datos['uss_usuario']);?></b> y <b><?=strtoupper($acudiente2['uss_usuario']);?></b> respectivamente quienes están plenamente de acuerdo con lo establecido con el Manual de Convivencia del Colegio. 
</p>

<p>
Algunas de las faltas convicenciales que se incumplen con mayor  frecuencia y a las que me comprometo cumplir a cabalidad son: <br><br>

	&raquo; Mantener el corte de cabello como lo estipula el Manual de Convivencia<br>

	&raquo; No usar cortes ni peinados extravagantes, ni tintes de cabello. Las niñas la cara despejada.<br>

	&raquo; Tener un vocabulario adecuado portando mi uniforme<br>

	&raquo; Permanecer dentro del aula de clase, pedir permiso y boleta de circulación al docente de clase.<br>

	&raquo; Portar el uniforme según el horario <br>

	&raquo; No maquillarme, ni mantener las uñas pintadas.<br>

	&raquo; No permanecer en cafetería, baños, enfermería y otras dependencias en horas de clase.<br>

	&raquo; No llegar tarde a la institución, en caso de mi retardo me hago responsable de la nota que se evalúa en la primera hora y que no tendré derecho a superación.<br>
	
    &raquo; No utilizar pearcing dentro de la institución.<br>

	&raquo; No utilizar celular, audífonos, ipod, y juegos, durante las horas de clase, formaciones o eventos institucionales.<br>
    
	&raquo; No usar prendas diferentes a las del uniforme de diario y de educación física. Zapatos tenis totalmente blancos para educación física y zapato de color negro colegial de amarrar, suela color negro y cordones negros para el uniforme de diario.
</p>


<p>En  constancia firmo.</p>


<table width="100%">
<tr>
	<td>
    _____________________<br>
    Estudiante<br>                                                                            
	T.I. ó  C.C. 
    </td>
    
    <td>
    _____________________<br>
    Madre de Familia <br>                                                                       
	C.C. 
    </td>
    
    <td>
    _____________________<br>
    Padre de Familia <br>                                                                       
	C.C. 
    </td>
</tr>
</table>
</div>

<?php }
if($_POST["documento"]==4){
?>
<div>
<table width="100%" rules="all" border="1">
	<tr><td colspan="3">Nombres y apellidos: <b><?=strtoupper($datos["mat_primer_apellido"].' '.$datos["mat_segundo_apellido"].' '.$datos["mat_nombres"]);?></b></td></tr>
    <tr>
    	<td colspan="2">EPS:</td>
        <td>RH:</td>
    </tr>
    <tr>
    	<td>Fecha de nacimiento: <b><?=strtoupper($datos["mat_fecha_nacimiento"]);?></b></td>
        <td>Curso: <b><?=strtoupper($datos['gra_nombre']);?></b></td>
        <td>Edad:</td>
    </tr>
    <tr>
    	<td>Nombre del padre: <b><?=strtoupper($datos['uss_nombre']);?></b></td>
        <td>Teléfono: <b><?=strtoupper($datos['uss_telefono']);?></b></td>
        <td>Celular: <b><?=strtoupper($datos['uss_celular']);?></b></td>
    </tr>
    
    <tr>
    	<td>Nombre de la madre:</td>
        <td>Teléfono:</td>
        <td>Celular:</td>
    </tr>
    
    <tr>
    	<td>Tiene hermanos en otro curso:<br><br>&nbsp;</td>
        <td>Nombres:<br><br>&nbsp;</td>
        <td>En que Curso:<br><br>&nbsp;</td>
    </tr>
    
    <tr>
    	<td colspan="2">Nombre de otra persona que podamos contactar en caso de no ubicar a los padres:<br><br>&nbsp;</td>
        <td>Teléfono:<br><br>&nbsp;</td>
    </tr>
    
    <tr>
    	<td colspan="3">Preferencia para avisar: </td>
    </tr>
    
    <tr>
    	<td colspan="3">Correo electrónico Madre: </td>
    </tr>
    
    <tr>
    	<td colspan="3">Correo electrónico Padre: </td>
    </tr>
</table>
<p>&nbsp;</p>
<b>Problemas de salud (marque con una X): </b>
<table width="100%">
    <tr>
    	<td>Asma<br></td>
        <td>Migraña<br></td>
        <td>Problemas cardiacos<br></td>
        <td>Diabetes<br></td>
    </tr>
    <tr>
    	<td>Epilepsia <br></td>
        <td colspan="3">Trastorno por déficit de atención <br></td>
    </tr>
    <tr>
        <td colspan="4">Otras<br><br>&nbsp;</td>
    </tr>
    <tr>
    	<td colspan="2"><b>Alergias</b><br></td>
        <td>SI<br></td>
        <td>NO<br></td>
    </tr>
    <tr>
    	<td colspan="2">A medicamentos, alimentos, otras (plantas, metales, látex,…): <br></td>
        <td colspan="2">Especifique:<br><br>&nbsp;</td>
    </tr>
    <tr>
    	<td colspan="4"><b>Vacunación</b></td>
    </tr>
    
    <tr>
    	<td colspan="4">¿Carné con esquema de  Vacunación al día? </td>
    </tr>
    <tr>
        <td colspan="2">SI<br></td>
        <td colspan="2">NO<br></td>
    </tr> 
    <tr>
    	<td colspan="4">Especificar la que falta y motivo:<br><br>&nbsp;</td>
    </tr> 
    
    <tr>
    	<td colspan="4"><b>Medicación </b></td>
    </tr>
    <tr>
    	<td colspan="4">¿Toma medicación a diario?</td>
    </tr>
    <tr>
        <td>NO<br></td>
        <td>Sí, en casa<br></td>
        <td>Sí, en el colegio<br></td>
        <td>Especifique:<br><br>&nbsp;</td>
    </tr> 
    <tr>
    	<td colspan="4">¿Trae medicación de urgencia al colegio?</td>
    </tr>
    <tr>
        <td>SI<br></td>
        <td>NO<br></td>
        <td colspan="2">Especifique medicamento y motivo:<br><br>&nbsp;</td>
    </tr>                                         
</table>
</div>

<?php }
if($_POST["documento"]==5){
?>
<div style="margin:5px; line-height:20px;">
<p align="center">
<b><?=strtoupper($datos["mat_primer_apellido"].' '.$datos["mat_segundo_apellido"].' '.$datos["mat_nombres"]);?></b><br>
____________________________________________________<br>
NOMBRE Y APELLIDOS DEL ESTUDIANTE 
</p>

<p>
<b>DATOS DEL EDUCANDO</b><br>
Documento de Identidad: <b><?=$datos["mat_documento"];?></b><br>
Lugar y fecha de nacimiento: <b><?=strtoupper($datos["mat_fecha_nacimiento"]).", ".$datos["mat_lugar_nacimiento"];?></b><br>
Dirección: <b><?=$datos["mat_direccion"];?></b>   Barrio: <b><?=$datos["mat_barrio"];?></b><br>
Teléfono fijo: <b><?=$datos["mat_telefono"];?></b> Teléfono Celular: <b><?=$datos["mat_celular"];?></b><br>
Afiliación EPS: ____________________ Grupo sanguíneo:______________ RH: ______________<br>
Enfermedades padecidas: __________________________________________________________<br>
Alérgico (a): _____________________________________________________________________<br>
Apto para realizar ejercicios físicos:     SI  _______    NO ______<br>
Porque: ________________________________________________________________________<br>
Antecedentes psicológicos: ______________________________________________________________<br>
__________________________________________________________________________<br>
</p>

<p>
<b>DATOS DE LOS PADRES</b><br>
Nombre del Padre: <b><?=strtoupper($datos['uss_nombre']);?></b><br>
Teléfono fijo: <b><?=strtoupper($datos['uss_telefono']);?></b> Teléfono celular: <b><?=strtoupper($datos['uss_celular']);?></b><br>
Nombre de la Madre: _____________________________________________________________<br>
Teléfono fijo: ____________________________ Teléfono celular: _________________________<br>
</p>

<p>
<b>DATOS DE ACUDIENTE SI ES DIFERENTE A LOS PADRES</b><br> 
Nombre del Acudiente: ___________________________________________________________<br>
Teléfono fijo: ____________________________ Teléfono celular: ________________________<br>
Parentesco con el estudiante: ______________________________________________________<br>
</p>



<b>CONFORMACION DE LA FAMILIA</b><br>
__________________________________________________________________________________________________________________________<br>
__________________________________________________________________________________________________________________________<br>
__________________________________________________________________________________________________________________________

</div>                                             

<?php }
if($_POST["documento"]==6){
?>
<div style="margin:5px; line-height:20px;">

Nosotros <b><?=strtoupper($datos['uss_nombre']);?></b> y <b><?=strtoupper($acudiente2['uss_nombre']);?></b>
Identificados con C.C. No. <b><?=strtoupper($datos['uss_usuario']);?></b> y <b><?=strtoupper($acudiente2['uss_usuario']);?></b> expedidas en <b><?=strtoupper($datos['uss_lugar_expedicion']);?></b>
y <b><?=strtoupper($acudiente2['uss_lugar_expedicion']);?></b> Padres o acudientes del estudiante <b><?=strtoupper($datos["mat_primer_apellido"].' '.$datos["mat_segundo_apellido"].' '.$datos["mat_nombres"]);?></b>
del Grado <b><?=strtoupper($datos['gra_nombre']);?></b>. <b>ACEPTAMOS</b> la normatividad contemplada en el manual de convivencia del Colegio CELCO y nos comprometemos a cumplir cabalmente como sujeto de derechos, TODA LA NORMATIVIDAD DE ACUERDO A NUESTRA LIBRE ELECCION DE COLEGIO EN BUSCA DE LA MEJOR EDUCACION INTEGRAL DE NUESTRO ACUDIDO;  y apoyarlo en todos los procesos relacionados con su formación ESPIRITUAL, ACADEMICA Y CONVIVENCIAL.<br> 

<p>En constancia firman:</p>

<table width="100%">
<tr>
	<td>
    _____________________<br>
    Nombre<br>                                                                            
	C.C. 
    </td>
    
    <td>
    _____________________<br>
    Nombre <br>                                                                       
	C.C. 
    </td>
    
</tr>
</table>

<p>
Yo  <b><?=strtoupper($datos["mat_primer_apellido"].' '.$datos["mat_segundo_apellido"].' '.$datos["mat_nombres"]);?></b> en mi calidad de estudiante <b>ACEPTO LIBREMENTE</b> el presente Manual de Convivencia EN TODA SU NORMATIVIDAD, comprometiéndome a cumplir cabalmente los deberes, las normas y derechos que aquí se encuentran consignados que permiten el desarrollo de mi libre personalidad; de acuerdo a la formación de mi familia en busca de una educación integral tanto ESPIRITUAL, ACADEMICA Y CONVIVENCIAL. 
</p>

<p>En constancia firmo: </p>

__________________________<br>
Nombre:<br>
T.I.

<p align="center">
<b>REGISTRO DE FIRMAS AUTORIZADAS<br>
PADRE, MADRE Y ACUDIENTE</b>

<table width="100%" border="1" rules="all" style="margin-top:20px;">
    <tr style="text-align:center; font-weight:bold;">
        <td>FIRMA DEL PADRE</td>
        <td>FIRMA DE LA MADRE</td>
        <td>FIRMA DEL ACUDIENTE</td>
    </tr>
    
    <tr style="height:50px;">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>

</table>
		
</p>

<b>NOTA:</b> Solo se tendrá en cuenta estas firmas para los permisos de salida, citaciones, justificación de Inasistencia y demás procedimientos internos.   

</div>
<?php }
if($_POST["documento"]==7){
?>
<div style="margin:5px; line-height:20px;">

	<table width="100%" border="1" rules="all">
		<tr style="height: 40px;">
			<td colspan="2">NOMBRE DEL ESTUDIANTE: <b><?=strtoupper($datos["mat_primer_apellido"].' '.$datos["mat_segundo_apellido"].' '.$datos["mat_nombres"]);?></b></td>
			<td>IDENTIFICACIÓN: <b><?=$datos["mat_documento"];?></b></td>
			<td>FECHA DE NACIMIENTO: <b><?=$datos["mat_fecha_nacimiento"];?></b></td>
			<td rowspan="3" style="height: 100px;" align="center">ESPACIO<br>FOTO</td>
		</tr>
		
		<tr style="height: 40px;">
			<td colspan="2">TELÉFONOS: <b><?=$datos["mat_telefono"];?></b></td>
			<td colspan="2">PADRES: <b><?=strtoupper($datos['uss_nombre']);?></b>, <b><?=strtoupper($acudiente2['uss_nombre']);?></b></td>
		</tr>
		
		<tr style="height: 40px;">
			<td>DIRECTOR DE GRADO: <b><?=$dg['uss_nombre'];?></b></td>
			<td>GRADO: <b><?=strtoupper($datos['gra_nombre']);?></b></td>
			<td>ACUDIENTE: <b><?=strtoupper($datos['uss_nombre']);?></b></td>
			<td>DIRECCIÓN: <b><?=strtoupper($datos['uss_direccion']);?></b></td>
		</tr>
	</table>
	<p>&nbsp;</p>
	<table width="100%" border="1" rules="all">
		<tr align="center" style="font-weight: bold;">
			<td>FECHA</td>
			<td>OBSERVACIONES</td>
			<td>NORMA (NUMERAL, S.T., DECINCENTIVO - CORRECTIVO</td>
			<td>COMPROMISO DEL ESTUDIANTE</td>
			<td>FIRMA DEL PADRE DE FAMILA</td>
			<td>FIRMA DEL ESTUDIANTE</td>
		</tr>
		
		<?php for($f=1; $f<=5; $f++){?>
		<tr style="height: 150px;">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<?php }?>
		
	</table>
 

</div>
<?php }?>


</div>

<div align="center" style="margin-top:20px;">
<hr>
Calle 9 Nº 4 – 55   Barrio Los Centauros – Paz de Ariporo Casanare<br>
Teléfono 6374248  3123104073 - colegioluteranopza@gmail.com   www.iecelco.com 
</div>

<script>
print();
</script>
<!--
<div align="center" style="font-size:10px; margin-top:10px;">
    <img src="../files/images/sintia.png" height="50" width="100"><br>
    SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
</div>
-->
     
</body>
</html>


