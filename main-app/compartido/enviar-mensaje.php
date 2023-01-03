<?php
//include("verificar_session.php");
include("secciones.php");
//include("../controller/funciones/notificacion.php");
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<?php Head();?>
<!-- styles -->
<link href="../css/bootstrap.css" rel="stylesheet">
<link href="../css/bootstrap-responsive.css" rel="stylesheet">
<link rel="stylesheet" href="../css/font-awesome.css">
<!--[if IE 7]>
<link rel="stylesheet" href="css/font-awesome-ie7.min.css">
<![endif]-->
<link href="../css/chosen.css" rel="stylesheet">
<link href="../css/styles.css" rel="stylesheet">
<link href="../css/theme-wooden.css" rel="stylesheet">

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="css/ie/ie7.css" />
<![endif]-->
<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="css/ie/ie8.css" />
<![endif]-->
<!--[if IE 9]>
<link rel="stylesheet" type="text/css" href="css/ie/ie9.css" />
<![endif]-->
<link href='http://fonts.googleapis.com/css?family=Dosis' rel='stylesheet' type='text/css'>
<!--fav and touch icons -->
<link rel="shortcut icon" href="../files-sgpa/images/jacademico-icono.png">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="../ico/apple-touch-icon-57-precomposed.png">
<!--============ javascript ===========-->
<script src="../js/jquery.js"></script>
<script src="../js/jquery-ui-1.10.1.custom.min.js"></script>
<script src="../js/bootstrap.js"></script>
<script src="../js/bootstrap-fileupload.js"></script>
<script src="../js/accordion.nav.js"></script>
<script src="../js/jquery.tagsinput.js"></script>
<script src="../js/chosen.jquery.js"></script>
<script src="../js/bootstrap-colorpicker.js"></script>
<script src="../js/bootstrap-datetimepicker.min.js"></script>
<script src="../js/date.js"></script>
<script src="../js/daterangepicker.js"></script>
<script src="../js/custom.js"></script>
<script src="../js/respond.min.js"></script>
<script src="../js/ios-orientationchange-fix.js"></script>
<script type="../text/javascript">
    /*====TAGS INPUT====*/
    $(function () {
        $('#tags_1').tagsInput({
            width: 'auto'
        });
        $('#tags_2').tagsInput({
            width: 'auto',
            onChange: function (elem, elem_tags) {
                var languages = ['php', 'ruby', 'javascript'];
                $('.tag', elem_tags).each(function () {
                    if ($(this).text().search(new RegExp('\\b(' + languages.join('|') + ')\\b')) >= 0) $(this).css('background-color', 'yellow');
                });
            }
        });
    });
    /*====Select Box====*/
    $(function () {
        $(".chzn-select").chosen();
        $(".chzn-select-deselect").chosen({
            allow_single_deselect: true
        });
    });
    /*====Color Picker====*/
    $(function () {
        $('.colorpicker').colorpicker({
            format: 'hex'
        });
        $('.pick-color').colorpicker();
    });
    /*====DATE Time Picker====*/
    $(function () {
        $('#datetimepicker1').datetimepicker({
            language: 'pt-BR'
        });
    });
    $(function () {
        $('#datetimepicker3').datetimepicker({
            pickDate: false
        });
    });
    $(function () {
        $('#datetimepicker4').datetimepicker({
            pickTime: false
        });
    });
    /*DATE RANGE PICKER*/
    $(function () {
        $('#reportrange').daterangepicker({
            ranges: {
                'Today': ['today', 'today'],
                'Yesterday': ['yesterday', 'yesterday'],
                'Last 7 Days': [Date.today().add({
                    days: -6
                }), 'today'],
                'Last 30 Days': [Date.today().add({
                    days: -29
                }), 'today'],
                'This Month': [Date.today().moveToFirstDayOfMonth(), Date.today().moveToLastDayOfMonth()],
                'Last Month': [Date.today().moveToFirstDayOfMonth().add({
                    months: -1
                }), Date.today().moveToFirstDayOfMonth().add({
                    days: -1
                })]
            },
            opens: 'left',
            format: 'MM/dd/yyyy',
            separator: ' to ',
            startDate: Date.today().add({
                days: -29
            }),
            endDate: Date.today(),
            minDate: '01/01/2012',
            maxDate: '12/31/2013',
            locale: {
                applyLabel: 'Submit',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom Range',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            },
            showWeekNumbers: true,
            buttonClasses: ['btn-danger']
        },
        function (start, end) {
            $('#reportrange span').html(start.toString('MMMM d, yyyy') + ' - ' + end.toString('MMMM d, yyyy'));
        });
        //Set the initial state of the picker label
        $('#reportrange span').html(Date.today().add({
            days: -29
        }).toString('MMMM d, yyyy') + ' - ' + Date.today().toString('MMMM d, yyyy'));
    });
    $(function () {
        $('#reservation').daterangepicker();
    });
</script>

</head>
<body>
<div class="layout">
	<!-- Navbar
    ================================================== -->
	<?php Top();?>

	<div class="main-wrapper">
		<div class="container-fluid">
			<div class="row-fluid ">
				<div class="span12">
					<div class="primary-head">


			<h3 class="page-header">Enviar mensaje</h3>

              <?php
include("../modell/conexion.php");

//======================== ENVIAR EL PEDIDO ===================================
if(isset($_POST["enviar"]))
{


	if(trim($_POST["mensaje"])=="" || trim($_POST["asunto"])=="")
	{
		echo "<font color='black'>Debe diligenciar todos los campos requeridos correctamente!<br><br>
		<a href='javascript:history.go(-1);' class='button'>Regresar</a></font>";
		exit();
	}
	

switch($_GET["opcion"]){
	case 0:
	$consulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id='".$_GET["para"]."'");
	break;
	
	case 1:
	$consulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE email!=''");
	break;
	
	case 2:
	$consulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id_perfil=2 AND email!=''");
	break;
	
	case 3:
	$consulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id_perfil=3 AND email!=''");
	break;
}

while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	if($resultado[6]=="")
		continue;
	else{	
		mysqli_query($conexion, "INSERT INTO emails(ema_de, ema_para, ema_asunto, ema_contenido, ema_fecha, ema_visto)VALUES('".$_GET["de"]."', '".$resultado[0]."', '".$_POST["asunto"]."', '".$_POST["mensaje"]."', now(), 0)");
		
	}	
}
	

	
		$fin =  '<html><body>';
		$fin .= '
		<table width="80%" align="center" border="1" style="font-family:Arial;" rules="groups" cellpadding="3" cellspacing="3">
	
    <tr>
    	<td style="background:#CC0000; color:#FFFFFF; text-align:center;" colspan="2">
        	WOLFSYSTEM - SGPA<br>
            <img src="http://wolfsystem.co/es/images/imagenes/logowolf.png" alt="Space Invaders" width="100" />
        </td>
    </tr>
    
    <tr>
    	<td style="background:#000000; color:#FFFFFF; text-align:right;">FECHA</td>
        <td style="background:#CCCCCC; color:#000000; text-align:left;">&nbsp;'.date("d/M/Y - h:i:s A").'</td>
    </tr>
    
    <tr>
    	<td style="background:#000000; color:#FFFFFF; text-align:right;">NOMBRE</td>
        <td style="background:#CCCCCC; color:#000000; text-align:left;">&nbsp;'.$_POST["name"].'</td>
    </tr>
    
    <tr>
    	<td style="background:#000000; color:#FFFFFF; text-align:right;">EMAIL</td>
        <td style="background:#CCCCCC; color:#000000; text-align:left;">&nbsp;'.$_POST["email"].'</td>
    </tr>
    
    <tr>
    	<td style="background:#000000; color:#FFFFFF; text-align:right;">TEL&Eacute;FONO</td>
        <td style="background:#CCCCCC; color:#000000; text-align:left;">&nbsp;'.$_POST["phone"].'</td>
    </tr>
    
    <tr>
    	<td style="background:#000000; color:#FFFFFF; text-align:right;">ASUNTO</td>
        <td style="background:#CCCCCC; color:#000000; text-align:left;">&nbsp;'.$_POST["asunto"].'</td>
    </tr>
    
    <tr>
    	<td style="background:#CC0000; color:#FFFFFF; text-align:center;" colspan="2">MENSAJE</td>
    </tr>
    
    <tr height="50">
    	<td style="background:#FFFFFF; color:#000000; text-align:left; font-style:italic;" colspan="2">'.$_POST["message"].'</td>
    </tr>
    
    <tr>
    	<td style="background:#FFFFFF; color:#000000; text-align:center; font-size:10px;" colspan="2">
        	� 2013 WolfSystem - SGPA de <span style="color:#FF6600; font-weight:bold;">jAcad&eacute;mico, Soluci&oacute;n integral</span><br>
			Sistema Gestor de Procesos Acad&eacute;micos<br>
			Universidades | Colegios | Institutos<br>
			info@wolfsystem.co<br>
			<a href="http://www.wolfsystem.co" target="_blank" style="text-decoration:underline;">www.wolfsystem.co</a> - <a href="http://www.jacademico.com" target="_blank" style="text-decoration:underline;">www.jAcademico.com</a><br>
			(4) 585 3755 - 318 347 9394<br>
			<img src="http://sgpa.unividafup.com/files-sgpa/images/jAcademico.png" height="70" width="250"><br>
        </td>
    </tr>
    
</table>
		';
		
		
		$fin .='';
			
		$fin .=  '<html><body>';
		

		$sfrom = $_POST["de"]; //LA CUETA DEL QUE ENVIA EL MENSAJE

		$sdestinatario = $_POST["para"]; //CUENTA DEL QUE RECIBE EL MENSAJE

		$ssubject = $_POST["asunto"]; //ASUNTO DEL MENSAJE 

		$shtml = $fin; //MENSAJE EN SI

		$sheader="From:".$sfrom."\nReply-To:".$sfrom."\n"; 

		$sheader=$sheader."X-Mailer:PHP/".phpversion()."\n"; 

		$sheader=$sheader."Mime-Version: 1.0\n"; 

		$sheader=$sheader."Content-Type: text/html; charset=UTF-8\r\n"; 

		@mail($sdestinatario,$ssubject,$shtml,$sheader);
				

		echo "<font color='black'>Su mensaje fue enviado correctamente.<br>

		<a href='javascript:history.go(-1);' class='button'>Regresar</a></font>";

		exit();

}
$consultaRemite=mysqli_query($conexion, "SELECT * FROM usuarios WHERE id='".$_GET["de"]."'");
$remite = mysqli_fetch_array($consultaRemite, MYSQLI_BOTH);

switch($_GET["opcion"]){
	case 0:
	$consulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id='".$_GET["para"]."'");
	break;
	
	case 1:
	$consulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE email!=''");
	break;
	
	case 2:
	$consulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id_perfil=2 AND email!=''");
	break;
	
	case 3:
	$consulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id_perfil=3 AND email!=''");
	break;
}

$num = mysql_num_rows($consulta);
$con=1;
while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	if($resultado[6]=="")
		continue;
	else{	
		if($con<$num)
			$para .= $resultado[6].",";
		else
			$para .= $resultado[6];
	}	
	$con++;
}
?>         

			<div class="row-fluid">
				<div class="span12">
                	<?php  //Mensajes($_GET["msj"]);?>
					<div class="content-widgets gray">
						<div class="widget-head blue">
							<h3>Enviar Mensaje</h3>
						</div>
						<div class="widget-container">
							<form class="form-horizontal" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?para=<?=$_GET["para"];?>&de=<?=$_GET["de"];?>&opcion=<?=$_GET["opcion"];?>" name="msj">
								
                                <div class="control-group">
									<label class="control-label">Remitente</label>
									<div class="controls">
                                    	<input name="deVisible" class="span8" type="text" value="<?=strtoupper($remite[4]." ".$remite[5])." (".$remite[6].")";?>" readonly/>
                                        <input name="de" class="span4" type="hidden" value="<?=$remite[6];?>" readonly/>
                                    </div>
								</div>
                                
                                <div class="control-group">
									<label class="control-label">Destinatario</label>
									<div class="controls">
                                    	<input name="paraVisible" class="span8" type="text" value="<?=$para;?>" readonly/>
                                        <input name="para" class="span4" type="hidden" value="<?=$para;?>" readonly/>
                                    </div>
								</div>
                                
                                <div class="control-group">
									<label class="control-label">Asunto</label>
									<div class="controls"><input name="asunto" class="span8" type="text" placeholder="Escriba el asunto" required/></div>
								</div>
                                
                                
                                
                            		<div class="control-group">
                                        <label class="control-label">Enviarme una copia</label>
                                        <div class="controls">
                                            <input type="checkbox" value="1" name="copia">
										</div>
           							</div>
                                    
                                    <script type="text/javascript">
										function controles(){
											n = document.getElementById("mensaje")
											if(document.msj.N.checked){
												n.style.fontWeight="bold";
											}else{
												n.style.fontWeight="normal";
											}
											
											k = document.getElementById("mensaje")
											if(document.msj.K.checked){
												k.style.fontStyle="italic";
											}else{
												k.style.fontStyle="normal";
											}
											
											s = document.getElementById("mensaje")
											if(document.msj.S.checked){
												s.style.textDecoration="underline";
											}else{
												s.style.textDecoration="none";
											}
											
											c = document.getElementById("mensaje")
											c.style.color=document.msj.color.value;
											
											f = document.getElementById("mensaje")
											f.style.background=document.msj.fondo.value;
											
											
											es = document.getElementById("mensaje")
											for(i=0;i<document.msj.e.length;i++){
												if(document.msj.e[i].checked) {
													marcado=i;
												}
											}
											if(document.msj.e[marcado].value=='M'){
												es.style.textTransform="uppercase";
											}else if(document.msj.e[marcado].value=='m'){
												es.style.textTransform="lowercase";
											}else{
												es.style.textTransform="capitalize";
											}
										}	
									</script>
                                    
                                    
                                    <div class="control-group">
                                		<label class="control-label">Estilos</label>
                                        <div class="controls">
                                             <input type="checkbox" value="1" name="N"> Negrilla<br>
                                             <input type="checkbox" value="1" name="K"> Kursiva<br>
                                             <input type="checkbox" value="1" name="S"> Subrayada<br>
                                             <input type="radio" value="M" name="e" id="e"> Mayuscula&nbsp;&nbsp;&nbsp;
                                             <input type="radio" value="m" name="e" id="e"> Minuscula&nbsp;&nbsp;&nbsp;
                                             <input type="radio" value="ca" name="e" id="e"> La primeras en mayusculas<br><br>
                                             Color Letra
                                             <select name="color">
                                             	<option value="white">Blanco</option>
                                             	<option value="black" selected>Negro</option>
                                             	<option value="yellow">Amarillo</option>
                                                <option value="blue">Azul</option>
                                             </select> <br><br>
                                             Color Fondo
                                             <select name="fondo">
                                             	<option value="white" selected>Blanco</option>
                                                <option value="black">Negro</option>
                                             	<option value="yellow">Amarillo</option>
                                                <option value="blue">Azul</option>
                                             </select> <br><br>
                                             <input type="button" class="btn btn-warning" value="Aplicar estilos" name="ap" onClick="controles()">
                                        </div>
									</div>
                                    
                                    <div class="control-group">
                                		<label class="control-label">Mensaje</label>
                                        <div class="controls">
                                            <textarea name="mensaje" rows="15" cols="80" id="mensaje" required="required" style="width:60%;"></textarea>
                                        </div>
									</div>
                                    
                                    
                                	<?php
									if(trim($para)==""){
										echo "
										<span style='color:red; text-transform:uppercase;'>Los usuarios seleccionados no tienen email registrado en el sistema</span><br>
										<a href='#' onClick='window.close();'>[Cerrar Ventana]</a>
										";
									}else{
									?>	
                            		<div class="form-actions">
										<input type="submit" class="btn btn-info" value="Enviar mensaje" name="enviar">
									</div>
                                    <?php
									}
									?>
                                    
								</form>
                                </div></div>
                          </div> </div>  
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php Pie();?>
</div>
</body>
</html>
