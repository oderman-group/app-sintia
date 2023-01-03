<?php
include("../modelo/conexion.php");
$consultaHoras=mysqli_query($conexion, "SELECT TIMESTAMPDIFF(HOUR, NOW(), eva_hasta) FROM academico_actividad_evaluaciones 
WHERE eva_id='".$_POST["eva"]."' AND eva_estado=1");
$horas = mysqli_fetch_array($consultaHoras, MYSQLI_BOTH);
$consultaMinutos=mysqli_query($conexion, "SELECT MOD(TIMESTAMPDIFF(MINUTE, NOW(), eva_hasta),60) FROM academico_actividad_evaluaciones 
WHERE eva_id='".$_POST["eva"]."' AND eva_estado=1");
$minutos = mysqli_fetch_array($consultaMinutos, MYSQLI_BOTH);
$consultaSegundos=mysqli_query($conexion, "SELECT MOD(TIMESTAMPDIFF(SECOND, NOW(), eva_hasta),60) FROM academico_actividad_evaluaciones 
WHERE eva_id='".$_POST["eva"]."' AND eva_estado=1");
$segundos = mysqli_fetch_array($consultaSegundos, MYSQLI_BOTH);

if($horas[0]==0){
	if($minutos[0]>20){$colorm='green';}
	if($minutos[0]>5 and $minutos[0]<=20){$colorm='orange';}
	if($minutos[0]>=0 and $minutos[0]<=5){$colorm='red';}
}

if($horas[0]==0 and $minutos[0]<1){
	if($segundos[0]>30){$color='green';}
	if($segundos[0]>15 and $segundos[0]<=30){$color='orange';}
	if($segundos[0]>=0 and $segundos[0]<=15){$color='red';}
}else{
	$color='green';
}


if($horas[0]==0 and $minutos[0]==4 and $segundos[0]==59){
?>
	<script type="text/javascript">
		function avisoFive(){
    	  $.toast({
    		  	heading: 'Tiempo restante',  
			  	text: 'Te quedan 5 minutos para finalizar la evaluación y enviarla.',
			  	position: 'bottom-center',
				loaderBg:'#FFD913',
				icon: 'warning',
    		    hideAfter: false
    		})
    	}
		setTimeout('avisoFive()',1000);
	   //alert('Te quedan 5 minutos para finalizar la evaluación y enviarla.');
	</script>
	<audio src="../../files-general/main-app/sonidos/alerta1.mp3" autoplay></audio>
<?php
}

if($horas[0]==0 and $minutos[0]==1 and $segundos[0]==59){
?>
	<script type="text/javascript">
		function aviso(){
    	  $.toast({
    		  	heading: 'Tiempo restante',  
			  	text: 'Te quedan 2 minutos para finalizar la evaluación y enviarla. Te recomendamos rectificar las preguntas rápidamente y enviar la evaluación. La evaluación se enviará automáticamente con las respuestas seleccionadas.',
			  	position: 'bottom-center',
				loaderBg:'#FFD913',
				icon: 'warning',
    		    hideAfter: false
    		})
    	}
		setTimeout('aviso()',1000);
	   //alert('Te quedan 2 minutos para finalizar la evaluación y enviarla. Te recomendamos rectificar las preguntas rápidamente y enviar la evaluación.');
	</script>
	<audio src="../../files-general/main-app/sonidos/alerta1.mp3" autoplay></audio>
<?php
}

if($horas[0]==0 and $minutos[0]==0 and $segundos[0]==0){
?>
	<script type="text/javascript">
	   function modalZero(){
		   document.getElementById('btnEvaluacion').style.display="none";
		   $("#mostrarmodalZero").modal("show");
	   }
	   setInterval('modalZero()',100);
	</script>
	<audio src="../../files-general/main-app/sonidos/alerta2.mp3" autoplay></audio>
<?php	
}
//ENVIARMOS LA EVALUACIÓN AUTOMÁTICAMENTE Y REDIRECCIONAMOS CUANDO ACABA EL TIEMPO
if($horas[0]==0 and $minutos[0]==0 and $segundos[0]=="-10"){
?>	
	<script type="text/javascript">
		document.getElementById('envioauto').value=1;
		document.evaluacionEstudiante.submit();
		//window.location.href="page-info.php?idmsg=104";
	</script>
<?php	
	exit();
}

if($_POST["time"]==1){echo $horas[0];}

if($_POST["time"]==2){
	echo "<span style='color:".$colorm."'>".number_format($minutos[0],0,",",".")."</span>";
}

if($_POST["time"]==3){
	if($segundos[0]>=0)
		echo "<span style='color:".$color."'>".number_format($segundos[0],0,",",".")."</span>";
	else
		echo "<span style='color:".$color."'>0</span>";
}
?>