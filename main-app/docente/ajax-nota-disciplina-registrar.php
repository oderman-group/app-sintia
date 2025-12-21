// Add logging for debugging SQL injection vulnerabilities
error_log("ajax-nota-disciplina-registrar.php - Inputs: codEst=" . $_POST["codEst"] . ", carga=" . $_POST["carga"] . ", periodo=" . $_POST["periodo"] . ", nota=" . $_POST["nota"] . ", observacion=" . $_POST["observacion"]);
<?php include("../../config-general/config.php");?>
<?php
// Input validation and sanitization functions
function validateInteger($value) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

function sanitizeString($value) {
    return filter_var($value, FILTER_SANITIZE_STRING);
}

// Validate and sanitize inputs
$codEst = isset($_POST["codEst"]) ? (validateInteger($_POST["codEst"]) ? $_POST["codEst"] : null) : null;
$carga = isset($_POST["carga"]) ? (validateInteger($_POST["carga"]) ? $_POST["carga"] : null) : null;
$periodo = isset($_POST["periodo"]) ? (validateInteger($_POST["periodo"]) ? $_POST["periodo"] : null) : null;
$nota = isset($_POST["nota"]) ? sanitizeString($_POST["nota"]) : null;
$observacion = isset($_POST["observacion"]) ? sanitizeString($_POST["observacion"]) : null;

if (!$codEst || !$carga || !$periodo) {
    echo "<span style='color:red; font-size:16px;'>Datos inválidos</span>";
    exit();
}
include("../modelo/conexion.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

// Input validation and sanitization
$codEst = filter_var($_POST["codEst"], FILTER_VALIDATE_INT);
$carga = filter_var($_POST["carga"], FILTER_VALIDATE_INT);
$periodo = filter_var($_POST["periodo"], FILTER_VALIDATE_INT);
$nota = isset($_POST["nota"]) ? filter_var($_POST["nota"], FILTER_SANITIZE_STRING) : null;
$observacion = isset($_POST["observacion"]) ? filter_var($_POST["observacion"], FILTER_SANITIZE_STRING) : null;

if (!$codEst || !$carga || !$periodo) {
    echo "<span style='color:red; font-size:16px;'>Datos inválidos</span>";
    exit();
}

$sql = "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante=? AND dn_id_carga=? AND dn_periodo=? AND institucion=? AND year=?";
$parametros = [$codEst, $carga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
$cdnota = BindSQL::prepararSQL($sql, $parametros);

if(mysqli_num_rows($cdnota)==0){
	$idInsercion=Utilidades::generateCode("DN");
	if(isset($_POST["nota"])){
        $sql = "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo, institucion, year) VALUES (?, ?, ?, ?, now(), ?, ?, ?)";
        $parametros = [$idInsercion, $codEst, $carga, $nota, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        BindSQL::prepararSQL($sql, $parametros);
	}else{
        $sql = "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_observacion, dn_fecha, dn_periodo, institucion, year) VALUES (?, ?, ?, ?, now(), ?, ?, ?)";
        $parametros = [$idInsercion, $codEst, $carga, $observacion, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        BindSQL::prepararSQL($sql, $parametros);
		}

}else{
	if(isset($_POST["nota"])){
        $sql = "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_nota=?, dn_fecha=now() WHERE dn_cod_estudiante=? AND dn_id_carga=? AND dn_periodo=? AND institucion=? AND year=?";
        $parametros = [$nota, $codEst, $carga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        BindSQL::prepararSQL($sql, $parametros);
	}else{
        $sql = "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_observacion=?, dn_fecha=now() WHERE dn_cod_estudiante=? AND dn_id_carga=? AND dn_periodo=? AND institucion=? AND year=?";
        $parametros = [$observacion, $codEst, $carga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        BindSQL::prepararSQL($sql, $parametros);
		}

	}


?>
	<script type="text/javascript">
		function notifica(){
			var unique_id = $.gritter.add({
				// (string | mandatory) the heading of the notification
				title: 'Correcto',
				// (string | mandatory) the text inside the notification
				text: 'Los cambios se ha guardado correctamente!',
				// (string | optional) the image to display on the left
				image: 'files/iconos/Accept-Male-User.png',
				// (bool | optional) if you want it to fade out on its own or just sit there
				sticky: false,
				// (int | optional) the time you want it to be alive for before fading out
				time: '3000',
				// (string | optional) the class name you want to apply to that specific message
				class_name: 'my-sticky-class'
			});
		}
		
		setTimeout ("notifica()", 100);	
	</script>
    <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Los cambios se ha guardado correctamente!.
	</div>
<?php	
	exit();

?>