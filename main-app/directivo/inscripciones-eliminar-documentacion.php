<?php
include("session.php");
include("../modelo/conexion.php");

	$consultaDocumentos=mysqli_query($conexion, "SELECT * FROM academico_matriculas_documentos WHERE matd_matricula='".$_GET["matricula"]."'");
	$documentos = mysqli_fetch_array($consultaDocumentos, MYSQLI_BOTH);
	

	$ruta = '../admisiones/files/otros';
	if(file_exists($ruta."/".$documentos['matd_pazysalvo'])){	unlink($ruta."/".$documentos['matd_pazysalvo']);	}
	if(file_exists($ruta."/".$documentos['matd_observador'])){	unlink($ruta."/".$documentos['matd_observador']);	}
	if(file_exists($ruta."/".$documentos['matd_eps'])){	unlink($ruta."/".$documentos['matd_eps']);	}
	if(file_exists($ruta."/".$documentos['matd_recomendacion'])){	unlink($ruta."/".$documentos['matd_recomendacion']);	}
	if(file_exists($ruta."/".$documentos['matd_vacunas'])){	unlink($ruta."/".$documentos['matd_vacunas']);	}
	if(file_exists($ruta."/".$documentos['matd_boletines_actuales'])){	unlink($ruta."/".$documentos['matd_boletines_actuales']);	}
	if(file_exists($ruta."/".$documentos['matd_documento_identidad'])){	unlink($ruta."/".$documentos['matd_documento_identidad']);	}
	if(file_exists($ruta."/".$documentos['matd_certificados'])){	unlink($ruta."/".$documentos['matd_certificados']);	}

	mysqli_query($conexion, "UPDATE academico_matriculas_documentos SET matd_fecha_eliminados=now(), matd_usuario_elimados='".$_SESSION["id"]."' WHERE matd_matricula='".$_GET["matricula"]."'");
	
	
	echo '<script type="text/javascript">window.location.href="inscripciones.php?msg=1";</script>';
	exit();