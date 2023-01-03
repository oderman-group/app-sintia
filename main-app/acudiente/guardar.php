<?php include("session.php");?>
<?php
if(isset($_POST["id"])){
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha, hil_ip, hil_so)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Acciones POST - ".$_SERVER['HTTP_REFERER']."', now(), '".$_SERVER["REMOTE_ADDR"]."', '".$_SERVER['HTTP_USER_AGENT']."')");
		
}elseif(isset($_GET["get"])){
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha, hil_ip, hil_so)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Acciones GET - ".$_SERVER['HTTP_REFERER']."', now(), '".$_SERVER["REMOTE_ADDR"]."', '".$_SERVER['HTTP_USER_AGENT']."')");
		
}else{
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha, hil_ip, hil_so)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Acciones DESCONOCIDA - ".$_SERVER['HTTP_REFERER']."', now(), '".$_SERVER["REMOTE_ADDR"]."', '".$_SERVER['HTTP_USER_AGENT']."')");
	
}
?>

<?php
//SOLICITUD DE DESBLOQUEO
if($_POST["id"]==1){	
	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".general_solicitudes(soli_id_recurso, soli_remitente, soli_fecha, soli_mensaje, soli_estado, soli_tipo, soli_institucion)
	VALUES('".$_POST["idRecurso"]."', '".$_SESSION["id"]."', now(), '".$_POST["contenido"]."', 1, 1, '".$config['conf_id_institucion']."')");
	
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=110";</script>';
	exit();
}
//ENCUESTA RESERVA DE CUPO
if($_POST["id"]==2){	
	mysqli_query($conexion, "INSERT INTO general_encuestas(genc_estudiante, genc_fecha, genc_respuesta, genc_comentario)
	VALUES('".$_POST["idEstudiante"]."', now(), '".$_POST["respuesta"]."', '".$_POST["motivo"]."')");
	
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=111";</script>';
	exit();
}
//FIRMAR ASPECTOS
if($_POST["id"]==3){

	mysqli_query($conexion, "UPDATE disiplina_nota SET dn_aprobado=1, dn_fecha_aprobado=now()
    WHERE dn_cod_estudiante=" . $_POST["estudiante"] . " AND dn_periodo='" . $_POST["periodo"] . "'");

	

	echo '<script type="text/javascript">window.location.href="'.$_SERVER["HTTP_REFERER"].'";</script>';

	exit();

}

//========================================== GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET  GET GET GET GET GET GET GET GET GET GET GET GET GET ======================================================
//FIRMA DIGITAL DE LOS REPORTES
if($_GET["get"]==1){
	mysqli_query($conexion, "UPDATE disciplina_reportes SET dr_aprobacion_acudiente=1, dr_aprobacion_acudiente_fecha=now(), dr_comentario='".$_GET["comentario"]."' WHERE dr_id='".$_GET["id"]."'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="estudiantes.php";</script>';
	exit();
}
?>