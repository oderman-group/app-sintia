<?php
session_start();
//Si otro usuario de mayor rango entra como él
if(isset($_SESSION["idO"]) and $_SESSION["idO"]!=""){$idSession = $_SESSION["idO"];}else{$idSession = $_SESSION["id"];}
if($idSession==""){
	header("Location:../controlador/salir.php");
}
else
{
	include("../../config-general/config.php");
	include("../../config-general/idiomas.php");
	include("../../config-general/consulta-usuario-actual.php");
	include("../../config-general/verificar-usuario-bloqueado.php");

	if($datosUsuarioActual[3]!=5 && $datosUsuarioActual[3]!=1)
	{
		echo "
		Usted no tiene permisos para acceder a esta opci&oacute;n:<br>
		Usuario: ".$datosUsuarioActual[0]."<br>
		Tipo: ".$datosUsuarioActual[3]."
		";
		exit();		
	}
	//ESTADOS DE ANIMO
	/*
	$estadoAnimo = mysql_num_rows(mysql_query("SELECT * FROM usuarios_estados_animo WHERE uean_usuario='".$_SESSION["id"]."' AND DATEDIFF(now(),uean_fecha)=0 ORDER BY uean_id DESC",$conexion));
	if($datosUsuarioActual['uss_preguntar_animo']==1 and $estadoAnimo==0){
?>		
		<script type="text/javascript">
			window.location.href="page-ea.php";
		</script>
<?php			
	}
	*/
	
}
?>
