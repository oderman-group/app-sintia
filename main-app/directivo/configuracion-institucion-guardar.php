<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0186';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["rectorI"]) == "" or trim($_POST["secretarioI"]) == "" or trim($_POST["nitI"]) == "" or trim($_POST["nomInstI"]) == "" or trim($_POST["direccionI"]) == "" or trim($_POST["telI"]) == "" or trim($_POST["calseI"]) == "" or trim($_POST["caracterI"]) == "" or trim($_POST["calendarioI"]) == "" or trim($_POST["jornadaI"]) == "" or trim($_POST["horarioI"]) == "" or trim($_POST["nivelesI"]) == "" or trim($_POST["modalidadI"]) == "" or trim($_POST["propietarioI"]) == "" or trim($_POST["coordinadorI"]) == "" or trim($_POST["tesoreroI"]) == "") {
		include("../compartido/guardar-historial-acciones.php");
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}

	if ($_FILES['logo']['name'] != "") {
		$explode = explode(".", $_FILES['logo']['name']);
		$extension = end($explode);
		$archivo = uniqid('logo_') . "." . $extension;
		$archivoAnt = $_POST["logoAnterior"];
		$destino = "../files/images/logo/";
		@unlink($destino . "/" . $archivoAnt);
		move_uploaded_file($_FILES['logo']['tmp_name'], $destino . "/" . $archivo);
	} else {
		$archivo = $_POST["logoAnterior"];
	}

	try{
		mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".general_informacion SET info_rector='" . $_POST["rectorI"] . "', info_secretaria_academica='" . $_POST["secretarioI"] . "', info_logo='" . $archivo . "', info_nit='" . $_POST["nitI"] . "', info_nombre='" . $_POST["nomInstI"] . "', info_direccion='" . $_POST["direccionI"] . "', info_telefono='" . $_POST["telI"] . "', info_clase='" . $_POST["calseI"] . "', info_caracter='" . $_POST["caracterI"] . "',info_calendario='" . $_POST["calendarioI"] . "', info_jornada='" . $_POST["jornadaI"] . "', info_horario='" . $_POST["horarioI"] . "', info_niveles='" . $_POST["nivelesI"] . "', info_modalidad='" . $_POST["modalidadI"] . "', info_propietario='" . $_POST["propietarioI"] . "', info_coordinador_academico='" . $_POST["coordinadorI"] . "', info_tesorero='" . $_POST["tesoreroI"] . "', info_ciudad='" . $_POST["ciudad"] . "', info_dane='" . $_POST["dane"] . "', info_resolucion='" . $_POST["resolucion"] . "', info_decreto_plan_estudio='" . $_POST["decretos"] . "'
		WHERE info_id=" . $_POST["idCI"] . ";");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	try{
		$informacionInstConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_informacion
		LEFT JOIN ".$baseDatosServicios.".localidad_ciudades ON ciu_id=info_ciudad
		LEFT JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
		WHERE info_institucion='" . $config['conf_id_institucion'] . "' AND info_year='" . $_SESSION["bd"] . "'");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
	$informacion_inst = mysqli_fetch_array($informacionInstConsulta, MYSQLI_BOTH);
	$_SESSION["informacionInstConsulta"] = $informacion_inst;

	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();