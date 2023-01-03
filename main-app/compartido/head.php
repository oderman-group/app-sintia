<?php
if(isset($idPaginaInterna)){
	$numOPConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_ubicacion
	INNER JOIN ".$baseDatosServicios.".publicidad ON pub_id=pubxub_id_publicidad AND pub_estado=1
	WHERE pubxub_ubicacion=3 AND pubxub_id_institucion='".$config['conf_id_institucion']."' AND pubxub_id_pagina='".$idPaginaInterna."'");
	$numOP = mysqli_num_rows($numOPConsulta);
	if($numOP>0){
		$numOP --;
	}
	$empezar = rand(0,$numOP);

	$publicidadPopUpConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_ubicacion
	INNER JOIN ".$baseDatosServicios.".publicidad ON pub_id=pubxub_id_publicidad AND pub_estado=1
	WHERE pubxub_ubicacion=3 AND pubxub_id_institucion='".$config['conf_id_institucion']."' AND pubxub_id_pagina='".$idPaginaInterna."'
	LIMIT ".$empezar.",1
	");
	$publicidadPopUp = mysqli_fetch_array($publicidadPopUpConsulta, MYSQLI_BOTH);

	if(isset($publicidadPopUp['pub_id'])){
		$numMostrarPopUpConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_estadisticas 
		WHERE pest_publicidad='".$publicidadPopUp['pub_id']."' AND pest_institucion='".$config['conf_id_institucion']."' AND pest_usuario='".$_SESSION["id"]."' AND pest_ubicacion=3");
		$numMostrarPopUp = mysqli_num_rows($numMostrarPopUpConsulta);
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD -->

<head>
	<?php include("../../config-general/analytics/instituciones.php");?>
	<?php //include("../../config-general/chatDrift.php");?>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta name="description" content="Plataforma Educativa SINTIA | Para Colegios y Universidades" />
    <meta name="author" content="ODERMAN" />
    <title><?=$frases[102][$datosUsuarioActual['uss_idioma']];?></title>
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
	<!-- icons -->
    <link href="../../config-general/fonts/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../config-general/fonts/material-design-icons/material-icon.css" rel="stylesheet" type="text/css" />
	<!--bootstrap -->
	<link href="../../config-general/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="../../config-general/assets/plugins/summernote/summernote.css" rel="stylesheet">
	<!-- morris chart -->
    <link href="../../config-general/assets/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
    <!-- Material Design Lite CSS -->
	<link rel="stylesheet" href="../../config-general/assets/plugins/material/material.min.css">
	<link rel="stylesheet" href="../../config-general/assets/css/material_style.css">
	<!-- inbox style -->
    <link href="../../config-general/assets/css/pages/inbox.min.css" rel="stylesheet" type="text/css" />
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/theme/light/theme_style.css" rel="stylesheet" id="rt_style_components" type="text/css" />
    <link href="../../config-general/assets/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/css/theme/light/style.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/css/theme/light/theme-color.css" rel="stylesheet" type="text/css" />
	<!-- Owl Carousel Assets -->
    <link href="../../config-general/assets/plugins/owl-carousel/owl.carousel.css" rel="stylesheet">
    <link href="../../config-general/assets/plugins/owl-carousel/owl.theme.css" rel="stylesheet">
	<!-- favicon -->
    <link rel="shortcut icon" href="../files/images/plataforma-sintia-logo.png" />
	<!-- Jquery Toast css -->
	<link rel="stylesheet" href="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.css">
	
	<?php 
	//SE INCLUYE PARA EL FORMULARIO QUE SOLICITA LOS DATOS
	if($datosUsuarioActual['uss_solicitar_datos']==1){
	?>
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
	<?php }?>
	
	<!-- Para la vista guiada -->
    <link href="../../librerias/vista-tour/introjs.css" rel="stylesheet">
	<script type="text/javascript" src="../../librerias/vista-tour/intro.js"></script>
	
	
	
	<script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
	
	
	<!-- Firebase App (the core Firebase SDK) is always required and must be listed first -->
	<script src="https://www.gstatic.com/firebasejs/6.2.0/firebase-app.js"></script>	
	<script src="https://www.gstatic.com/firebasejs/6.2.0/firebase-database.js"></script>
	<script src="https://www.gstatic.com/firebasejs/6.2.0/firebase-auth.js"></script>
	<!-- mis funciones de Firebase -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/node-uuid/1.4.7/uuid.min.js"></script>
	<script src="../modelo/conexion-firebase.js"></script>
	<script src="../compartido/firebase-funciones.js"></script>
	
	
	<!-- Axios -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.js"></script>
	
	
	
	<?php 
	include("sintia-funciones-js.php");
	?>

	<?php 
	include("sintia-funciones.php");
	//Instancia de Clases generales
	$usuariosClase = new Usuarios();
	?>
	
	<!-- Es necesaria para el loading de la pagina -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

	<style type="text/css">
	/*	
	.loader {
		position: fixed;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		z-index: 9999;
		background: url('../files/images/sintiaGif.gif') 50% 50% no-repeat rgb(249,249,249);
		opacity: .96;
	}
	*/
	
	/* para redimensionar los videos */	
	.iframe-container{
	  position: relative;
	  width: 100%;
	  padding-bottom: 56.25%; 
	  height: 0;
	}
	.iframe-container iframe{
	  position: absolute;
	  top:0;
	  left: 0;
	  width: 100%;
	  height: 100%;
	}
	</style>

	<script type="text/javascript">
	$(window).load(function() {
		$(".loader").fadeOut("slow");
	});
	</script>

	
	