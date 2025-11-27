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

	// Migrado a PDO - Consultas preparadas
	try{
		require_once(ROOT_PATH."/main-app/class/Conexion.php");
		$conexionPDO = Conexion::newConnection('PDO');
		
		$sql = "UPDATE ".$baseDatosServicios.".general_informacion SET 
		        info_rector=?, info_secretaria_academica=?, info_logo=?, info_nit=?, info_nombre=?, 
		        info_direccion=?, info_telefono=?, info_clase=?, info_caracter=?, info_calendario=?, 
		        info_jornada=?, info_horario=?, info_niveles=?, info_modalidad=?, info_propietario=?, 
		        info_coordinador_academico=?, info_tesorero=?, info_ciudad=?, info_dane=?, 
		        info_resolucion=?, info_decreto_plan_estudio=?
		        WHERE info_id=?";
		$stmt = $conexionPDO->prepare($sql);
		$stmt->bindParam(1, $_POST["rectorI"], PDO::PARAM_STR);
		$stmt->bindParam(2, $_POST["secretarioI"], PDO::PARAM_STR);
		$stmt->bindParam(3, $archivo, PDO::PARAM_STR);
		$stmt->bindParam(4, $_POST["nitI"], PDO::PARAM_STR);
		$stmt->bindParam(5, $_POST["nomInstI"], PDO::PARAM_STR);
		$stmt->bindParam(6, $_POST["direccionI"], PDO::PARAM_STR);
		$stmt->bindParam(7, $_POST["telI"], PDO::PARAM_STR);
		$stmt->bindParam(8, $_POST["calseI"], PDO::PARAM_STR);
		$stmt->bindParam(9, $_POST["caracterI"], PDO::PARAM_STR);
		$stmt->bindParam(10, $_POST["calendarioI"], PDO::PARAM_STR);
		$stmt->bindParam(11, $_POST["jornadaI"], PDO::PARAM_STR);
		$stmt->bindParam(12, $_POST["horarioI"], PDO::PARAM_STR);
		$stmt->bindParam(13, $_POST["nivelesI"], PDO::PARAM_STR);
		$stmt->bindParam(14, $_POST["modalidadI"], PDO::PARAM_STR);
		$stmt->bindParam(15, $_POST["propietarioI"], PDO::PARAM_STR);
		$stmt->bindParam(16, $_POST["coordinadorI"], PDO::PARAM_STR);
		$stmt->bindParam(17, $_POST["tesoreroI"], PDO::PARAM_STR);
		$stmt->bindParam(18, $_POST["ciudad"], PDO::PARAM_STR);
		$stmt->bindParam(19, $_POST["dane"], PDO::PARAM_STR);
		$stmt->bindParam(20, $_POST["resolucion"], PDO::PARAM_STR);
		$stmt->bindParam(21, $_POST["decretos"], PDO::PARAM_STR);
		$stmt->bindParam(22, $_POST["idCI"], PDO::PARAM_STR);
		$stmt->execute();
		
		$sqlInfo = "SELECT * FROM ".$baseDatosServicios.".general_informacion
		            LEFT JOIN ".$baseDatosServicios.".localidad_ciudades ON ciu_id=info_ciudad
		            LEFT JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
		            WHERE info_institucion=? AND info_year=?";
		$stmtInfo = $conexionPDO->prepare($sqlInfo);
		$stmtInfo->bindParam(1, $config['conf_id_institucion'], PDO::PARAM_INT);
		$stmtInfo->bindParam(2, $_SESSION["bd"], PDO::PARAM_INT);
		$stmtInfo->execute();
		$informacion_inst = $stmtInfo->fetch(PDO::FETCH_ASSOC);
		$_SESSION["informacionInstConsulta"] = $informacion_inst;
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();