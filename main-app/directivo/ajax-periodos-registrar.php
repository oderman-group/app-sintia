<?php include("session.php");?>
<?php
$consultaDatosCargaActual=mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_id='".$_POST["carga"]."' AND car_activa=1");
$datosCargaActual = mysqli_fetch_array($consultaDatosCargaActual, MYSQLI_BOTH);
?>
<?php
if(trim($_POST["nota"])==""){
    echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";
	exit();
}
if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<1) $_POST["nota"] = 1;
include("../modelo/conexion.php");
$consulta = mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante='".$_POST["codEst"]."' AND bol_carga='".$_COOKIE["carga"]."' AND bol_periodo='".$_POST["per"]"'");

$num = mysqli_num_rows($consulta);
$rB = mysqli_fetch_array($consulta, MYSQLI_BOTH);
if($num==0){
	mysqli_query($conexion, "DELETE FROM academico_boletin WHERE bol_id='".$rB[0]."'");
	
	mysqli_query($conexion, "INSERT INTO academico_boletin(bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo, bol_observaciones)VALUES('".$_COOKIE["carga"]."','".$_POST["codEst"]."','".$_POST["per"]."','".$_POST["nota"]."',2, '')");
	
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$idSession."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Inserción de notas en el periodo', now())");
	
}else{
	mysqli_query($conexion, "UPDATE academico_boletin SET bol_nota='".$_POST["nota"]."', bol_observaciones='', bol_tipo=2 WHERE bol_id=".$rB[0]);
	
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$idSession."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Actualización de notas en el periodo', now())");
	
}	

?>
<script type="text/javascript">
function notifica(){
	$.toast({
		heading: 'Cambios guardados',  
		text: 'Los cambios se ha guardado correctamente!.',
		position: 'botom-left',
		loaderBg:'#ff6849',
		icon: 'success',
		hideAfter: 3000, 
		stack: 6
	});
}
setTimeout ("notifica()", 100);
</script>

    <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Los cambios se ha guardado correctamente!.
	</div>