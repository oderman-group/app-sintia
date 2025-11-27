<?php 
include("session.php");
require_once '../class/Plataforma.php';
require_once ROOT_PATH."/main-app/class/Modulos.php";

$Plataforma = new Plataforma;

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DV0015';
include("../compartido/historial-acciones-guardar.php");

if (trim($_POST["nombreInstitucion"]) == "") {
	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="dev-instituciones-editar.php?error=ER_DT_4";</script>';
	exit();
}
$fechaRenovacion="";
if(!empty($_POST["fechaRenovacion"])){
	$fechaRenovacion="ins_fecha_renovacion='" . $_POST["fechaRenovacion"] . "', ";
}

try {
	mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".instituciones SET 
	ins_nit='" . $_POST["nit"] . "', 
	ins_nombre='" . $_POST["nombreInstitucion"] . "', 
	ins_siglas='" . $_POST["siglas"] . "', 
	ins_telefono_principal='" . $_POST["telefonoPrincipal"] . "', 
	ins_email_institucion='" . $_POST["emailPrincipal"] . "', 
	ins_ciudad='" . $_POST["ciudad"] . "', 
	$fechaRenovacion
	ins_contacto_principal='" . $_POST["contactoPrincipal"] . "',
	ins_cargo_contacto='" . $_POST["cargo"] . "',
	ins_celular_contacto='" . $_POST["celular"] . "', 
	ins_email_contacto='" . $_POST["email"] . "',
	ins_estado='" . $_POST["estado"] . "',
	ins_id_plan='" . $_POST["plan"] . "', 
	ins_deuda='" . $_POST["deuda"] . "',
	ins_valor_deuda='" . $_POST["valorDeuda"] . "',
	ins_concepto_deuda='" . $_POST["conceptoDeuda"] . "'
	WHERE ins_id='".$_POST['id']."'");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

$numModulos = 0;
if (!empty($_POST["modulos"])) {
	$numModulos = count($_POST["modulos"]);
}

// Migrado a PDO - Consulta preparada
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$sql = "DELETE FROM ".$baseDatosServicios.".instituciones_modulos WHERE ipmod_institucion=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $_POST['id'], PDO::PARAM_STR);
	$stmt->execute();
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

if($numModulos>0){
	$contModulos = 0;
	while ($contModulos < $numModulos) {
		if($_POST["modulos"][$contModulos] == Modulos::MODULO_INSCRIPCIONES) {

			try{
				$colorBG = $Plataforma->colorUno;
				$yearInscription = $_SESSION["bd"]+1;

				$sql = "INSERT INTO {$baseDatosAdmisiones}.config_instituciones(cfgi_id_institucion,
				cfgi_year,
				cfgi_color_barra_superior,
				cfgi_inscripciones_activas,
				cfgi_politicas_texto,
				cfgi_color_texto,
				cfgi_mostrar_banner,
				cfgi_year_inscripcion) VALUES (?, ?, ?, '0', 'Loremp ipsum...', 'white', '0', ?) 
				ON DUPLICATE KEY UPDATE cfgi_year_inscripcion = VALUES(cfgi_year_inscripcion)";

				$stmt = mysqli_prepare($conexion, $sql);

				if (!$stmt) {
					die("Error al preparar la consulta.");
				}

				// Vincular los parámetros
				mysqli_stmt_bind_param($stmt, "iisi", $_POST['id'], $_SESSION["bd"], $colorBG, $yearInscription);

				// Ejecutar la consulta
				$resultado = mysqli_stmt_execute($stmt);

				if (!$resultado) {
					die("Error al ejecutar la consulta.");
				}

				} catch (Exception $e) {
					include("../compartido/error-catch-to-report.php");
				}
			
		}

		// Migrado a PDO - Consulta preparada para módulos
		try{
			require_once(ROOT_PATH."/main-app/class/Conexion.php");
			$conexionPDO = Conexion::newConnection('PDO');
			$sqlMod = "INSERT INTO ".$baseDatosServicios.".instituciones_modulos (ipmod_institucion, ipmod_modulo) VALUES (?, ?)";
			$stmtMod = $conexionPDO->prepare($sqlMod);
			$stmtMod->bindParam(1, $_POST['id'], PDO::PARAM_STR);
			$stmtMod->bindParam(2, $_POST["modulos"][$contModulos], PDO::PARAM_STR);
			$stmtMod->execute();
		} catch (Exception $e) {
			include("../compartido/error-catch-to-report.php");
		}
		$contModulos++;
	}
}	

include("../compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="dev-instituciones.php";</script>';
exit();
