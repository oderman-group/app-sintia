<?php include("session.php");?>
<?php include("../../config-general/config.php");?>
<?php
if(trim($_POST["nota"])==""){
    echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";
	exit();
}
if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<1) $_POST["nota"] = 1;
include("../modelo/conexion.php");
$consulta = mysqli_query($conexion, "SELECT * FROM academico_nivelaciones WHERE niv_cod_estudiante='".$_POST["codEst"]."' AND niv_id_asg='".$_COOKIE["carga"]"'");

$num = mysqli_num_rows($consulta);
$rB = mysqli_fetch_array($consulta, MYSQLI_BOTH);
if($num==0){
	mysqli_query($conexion, "DELETE FROM academico_nivelaciones WHERE niv_id='".$rB[0]."'");
	
	mysqli_query($conexion, "INSERT INTO academico_nivelaciones(niv_id_asg, niv_cod_estudiante, niv_definitiva, niv_fecha)VALUES('".$_COOKIE["carga"]."','".$_POST["codEst"]."','".$_POST["nota"]."',now())");
	
}else{
	mysqli_query($conexion, "UPDATE academico_nivelaciones SET niv_definitiva='".$_POST["nota"]."', niv_fecha=now() WHERE niv_id=".$rB[0]);
	
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
