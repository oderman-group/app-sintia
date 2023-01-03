<?php include("../../config-general/config.php");?>
<?php
$consultaDatosCargas=mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_id='".$_POST["carga"]."' AND car_activa=1");
$datosCargaActual = mysqli_fetch_array($consultaDatosCargas, MYSQLI_BOTH);
?>
<?php
if(trim($_POST["nota"])==""){
    echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";
	exit();
}
if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<1) $_POST["nota"] = 1;
include("../modelo/conexion.php");
$consulta = mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante=".$_POST["codEst"]." AND bol_carga=".$_POST["carga"]." AND bol_periodo=".$_POST["per"]);

$num = mysqli_num_rows($consulta);
$rB = mysqli_fetch_array($consulta, MYSQLI_BOTH);
//echo $num; exit();
if($num==0){
	mysqli_query($conexion, "DELETE FROM academico_boletin WHERE bol_id='".$rB[0]."'");
	
	mysqli_query($conexion, "INSERT INTO academico_boletin(bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo, bol_observaciones)VALUES('".$_POST["carga"]."','".$_POST["codEst"]."','".$_POST["per"]."','".$_POST["nota"]."',1, 'Colocada desde la parte Directiva.')");
	
}else{
	mysqli_query($conexion, "UPDATE academico_boletin SET bol_nota='".$_POST["nota"]."', bol_observaciones='Colocada desde la parte Directiva', bol_tipo=1 WHERE bol_id=".$rB[0]);
	
}	


	if($_POST["nota"]>$config[5]){
		$consultaUsuarioResponsable=mysqli_query($conexion, "SELECT * FROM usuarios_por_estudiantes WHERE upe_id_estudiante='".$_POST["codEst"]."'");
		$usuarioResponsable = mysqli_fetch_array($consultaUsuarioResponsable, MYSQLI_BOTH);
		if($usuarioResponsable[1]=="") $usuarioResponsable[1]=0;
		mysqli_query($conexion, "INSERT INTO general_alertas(alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, alr_vista, alr_categoria, alr_importancia)VALUES('Recuperación de periodo','El estudiante ".$_POST["codEst"]." ha obtenido una nota de recuperacion de ".$_POST["nota"]."',1,'".$usuarioResponsable[1]."',now(),0,1,2)");
		
		$consultaEstudiantes=mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_id='".$_POST["codEst"]."'");
		$estudiante = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH);
		$nombreCompleto = strtoupper($estudiante[3]." ".$estudiante[4]." ".$estudiante[5]);
		$consultaMateria=mysqli_query($conexion, "SELECT car_id, car_materia, mat_id, mat_nombre FROM academico_cargas, academico_materias WHERE car_id='".$datosCargaActual[0]."' AND mat_id=car_materia");
		$materia = mysqli_fetch_array($consultaMateria, MYSQLI_BOTH);
		$consultaAcudiente=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$usuarioResponsable[1]."'");
		$acudiente = mysqli_fetch_array($consultaAcudiente, MYSQLI_BOTH);
		//include("../compartido/email-alertas.php");
		$fin =  '<html><body>';
						$fin .= '
						Nos complace informarle que su acudido <b>'.$nombreCompleto.'</b>, ha recuperado el periodo '.$_POST["per"].' de la asignatura de <b>'.$materia[3].'</b>. Por favor ingrese a la plataforma SINTIA&reg; para revisarla.<br>
						Le sugerimos que felicite a su acudido y lo motive para siga obteniendo buenas calificaciones.<br>
						<b>RESALATAR LAS COSAS BUENAS DE LAS PERSONAS AUNQUE SEAN POCAS LOS MOTIVA A MEJORAR INCONSCIENTEMENTE</b>.<br>
						<table width="80%" align="center" border="1" style="font-family:Verdana, Arial, Helvetica, sans-serif;" rules="groups" cellpadding="3" cellspacing="3">
					
					<tr>
						<td style="background:#1fbba6; color:#FFFFFF; text-align:center;" colspan="2">
							<h2>SINTIA&reg; - CONTRIBUYE A LA EXCELENCIA EDUCATIVA</h2>
						</td>
					</tr>
					
					<tr>
						<td style="background:#ffd300; color:#FFFFFF; text-align:right;">FECHA</td>
						<td style="background:#F6F6F6; color:#000000; text-align:left;">&nbsp;'.date("d/M/Y").'</td>
					</tr>
					
					<tr>
						<td style="background:#ffd300; color:#FFFFFF; text-align:right;">TIPO DE NOTIFICACIÓN</td>
						<td style="background:#F6F6F6; color:#000000; text-align:left;">&nbsp;FELICITACIONES: RECUPERACION DE PERIODO!</td>
					</tr>
					
					<tr>
						<td style="background:#ffd300; color:#FFFFFF; text-align:right;">ESTUDIANTE</td>
						<td style="background:#F6F6F6; color:#000000; text-align:left;">&nbsp;'.$nombreCompleto.'</td>
					</tr>
					
					<tr>
						<td style="background:#ffd300; color:#FFFFFF; text-align:right;">ASIGNATURA</td>
						<td style="background:#F6F6F6; color:#000000; text-align:left;">&nbsp;'.$materia[3].'</td>
					</tr>
					
					<tr>
						<td style="background:#FFFFFF; color:#000000; text-align:center; font-size:10px;" colspan="2">
							<span style="font-size:16px;">SINTIA&reg; - CONTRIBUYE A LA EXCELENCIA EDUCATIVA</span><br>
							info@plataformasintia.com<br>
							(4) 585 3755 - 313 591 2073
						</td>
					</tr>
					
				</table>
						';
						
						
						
						$fin .='';
							
						$fin .=  '<html><body>';
						
				
						$sfrom="notificacion@plataformasintia.com"; //LA CUETA DEL QUE ENVIA EL MENSAJE
				
						$sdestinatario="notificacion@plataformasintia.com,".$acudiente[12]; //CUENTA DEL QUE RECIBE EL MENSAJE
				
						$ssubject="FELICITACIONES: RECUPERACION DE PERIODO!"; //ASUNTO DEL MENSAJE 
				
						$shtml=$fin; //MENSAJE EN SI
				
						$sheader="From:".$sfrom."\nReply-To:".$sfrom."\n"; 
				
						$sheader=$sheader."X-Mailer:PHP/".phpversion()."\n"; 
				
						$sheader=$sheader."Mime-Version: 1.0\n"; 
				
						$sheader=$sheader."Content-Type: text/html; charset=UTF-8\r\n"; 
				
						//@mail($sdestinatario,$ssubject,$shtml,$sheader);
	}
?>
	<script type="text/javascript">
		function notifica(){
			var unique_id = $.gritter.add({
				// (string | mandatory) the heading of the notification
				title: 'Correcto',
				// (string | mandatory) the text inside the notification
				text: 'Los cambios se ha guardado correctamente!',
				// (string | optional) the image to display on the left
				image: 'files/iconos/Accept-Male-User.png',
				// (bool | optional) if you want it to fade out on its own or just sit there
				sticky: false,
				// (int | optional) the time you want it to be alive for before fading out
				time: '3000',
				// (string | optional) the class name you want to apply to that specific message
				class_name: 'my-sticky-class'
			});
		}
		
		setTimeout ("notifica()", 100);	
	</script>
    <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Los cambios se ha guardado correctamente!.
	</div>
<?php	
	exit();

?>