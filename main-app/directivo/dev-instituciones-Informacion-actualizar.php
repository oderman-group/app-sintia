<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DV0035';
include("../compartido/historial-acciones-guardar.php");

	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["rectorI"]) == "" or trim($_POST["secretarioI"]) == "" or trim($_POST["nitI"]) == "" or trim($_POST["nomInstI"]) == "" or trim($_POST["direccionI"]) == "" or trim($_POST["telI"]) == "" or trim($_POST["calseI"]) == "" or trim($_POST["caracterI"]) == "" or trim($_POST["calendarioI"]) == "" or trim($_POST["jornadaI"]) == "" or trim($_POST["horarioI"]) == "" or trim($_POST["nivelesI"]) == "" or trim($_POST["modalidadI"]) == "" or trim($_POST["propietarioI"]) == "" or trim($_POST["coordinadorI"]) == "" or trim($_POST["tesoreroI"]) == "") {
		include("../compartido/guardar-historial-acciones.php");
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	if ($_FILES['logo']['name'] != "") {
		$archivo = $_FILES['logo']['name'];
		$archivoAnt = $_POST["logoAnterior"];
		$destino = "../files/images/logo/";
		@unlink($destino . "/" . $archivoAnt);
		move_uploaded_file($_FILES['logo']['tmp_name'], $destino . "/" . $archivo);
	} else {
		$archivo = $_POST["logoAnterior"];
	}

	// Migrado a PDO - Consulta preparada
	try{
		require_once(ROOT_PATH."/main-app/class/Conexion.php");
		$conexionPDO = Conexion::newConnection('PDO');
		$sql = "UPDATE ".$baseDatosServicios.".general_informacion SET 
		        info_rector=?, info_secretaria_academica=?, info_logo=?, info_nit=?, info_nombre=?, 
		        info_direccion=?, info_telefono=?, info_clase=?, info_caracter=?, info_calendario=?, 
		        info_jornada=?, info_horario=?, info_niveles=?, info_modalidad=?, info_propietario=?, 
		        info_coordinador_academico=?, info_tesorero=?
		        WHERE info_institucion=? AND info_year=?";
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
		$stmt->bindParam(18, $_POST["id"], PDO::PARAM_STR);
		$stmt->bindParam(19, $_POST["year"], PDO::PARAM_STR);
		$stmt->execute();
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="dev-instituciones-Informacion.php?success=SC_DT_2&id='.base64_encode($_POST["id"]).'&year='.base64_encode($_POST["year"]).'";</script>';
	exit();