<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
// Migrado a PDO - Consultas preparadas
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');

try{
	$sql = "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
	        WHERE dn_cod_estudiante=? AND dn_id_carga=? AND dn_periodo=? AND institucion=? AND year=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $_POST["codEst"], PDO::PARAM_STR);
	$stmt->bindParam(2, $_POST["carga"], PDO::PARAM_STR);
	$stmt->bindParam(3, $_POST["periodo"], PDO::PARAM_INT);
	$stmt->bindParam(4, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt->bindParam(5, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt->execute();
	$numRows = $stmt->rowCount();
	
	if($numRows==0){
		$idInsercion=Utilidades::generateCode("DN");
		if(isset($_POST["nota"])){
			$sqlInsert = "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(
			    dn_id, dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo, institucion, year
			) VALUES (?, ?, ?, ?, now(), ?, ?, ?)";
			$stmtInsert = $conexionPDO->prepare($sqlInsert);
			$stmtInsert->bindParam(1, $idInsercion, PDO::PARAM_STR);
			$stmtInsert->bindParam(2, $_POST["codEst"], PDO::PARAM_STR);
			$stmtInsert->bindParam(3, $_POST["carga"], PDO::PARAM_STR);
			$stmtInsert->bindParam(4, $_POST["nota"], PDO::PARAM_STR);
			$stmtInsert->bindParam(5, $_POST["periodo"], PDO::PARAM_INT);
			$stmtInsert->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
			$stmtInsert->bindParam(7, $_SESSION["bd"], PDO::PARAM_INT);
			$stmtInsert->execute();
		}else{
			$sqlInsert = "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(
			    dn_id, dn_cod_estudiante, dn_id_carga, dn_observacion, dn_fecha, dn_periodo, institucion, year
			) VALUES (?, ?, ?, ?, now(), ?, ?, ?)";
			$stmtInsert = $conexionPDO->prepare($sqlInsert);
			$stmtInsert->bindParam(1, $idInsercion, PDO::PARAM_STR);
			$stmtInsert->bindParam(2, $_POST["codEst"], PDO::PARAM_STR);
			$stmtInsert->bindParam(3, $_POST["carga"], PDO::PARAM_STR);
			$stmtInsert->bindParam(4, $_POST["observacion"], PDO::PARAM_STR);
			$stmtInsert->bindParam(5, $_POST["periodo"], PDO::PARAM_INT);
			$stmtInsert->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
			$stmtInsert->bindParam(7, $_SESSION["bd"], PDO::PARAM_INT);
			$stmtInsert->execute();
		}
	}else{
		if(isset($_POST["nota"])){
			$sqlUpdate = "UPDATE ".BD_DISCIPLINA.".disiplina_nota 
			              SET dn_nota=?, dn_fecha=now() 
			              WHERE dn_cod_estudiante=? AND dn_id_carga=? AND dn_periodo=? AND institucion=? AND year=?";
			$stmtUpdate = $conexionPDO->prepare($sqlUpdate);
			$stmtUpdate->bindParam(1, $_POST["nota"], PDO::PARAM_STR);
			$stmtUpdate->bindParam(2, $_POST["codEst"], PDO::PARAM_STR);
			$stmtUpdate->bindParam(3, $_POST["carga"], PDO::PARAM_STR);
			$stmtUpdate->bindParam(4, $_POST["periodo"], PDO::PARAM_INT);
			$stmtUpdate->bindParam(5, $config['conf_id_institucion'], PDO::PARAM_INT);
			$stmtUpdate->bindParam(6, $_SESSION["bd"], PDO::PARAM_INT);
			$stmtUpdate->execute();
		}else{
			$sqlUpdate = "UPDATE ".BD_DISCIPLINA.".disiplina_nota 
			              SET dn_observacion=?, dn_fecha=now() 
			              WHERE dn_cod_estudiante=? AND dn_id_carga=? AND dn_periodo=? AND institucion=? AND year=?";
			$stmtUpdate = $conexionPDO->prepare($sqlUpdate);
			$stmtUpdate->bindParam(1, $_POST["observacion"], PDO::PARAM_STR);
			$stmtUpdate->bindParam(2, $_POST["codEst"], PDO::PARAM_STR);
			$stmtUpdate->bindParam(3, $_POST["carga"], PDO::PARAM_STR);
			$stmtUpdate->bindParam(4, $_POST["periodo"], PDO::PARAM_INT);
			$stmtUpdate->bindParam(5, $config['conf_id_institucion'], PDO::PARAM_INT);
			$stmtUpdate->bindParam(6, $_SESSION["bd"], PDO::PARAM_INT);
			$stmtUpdate->execute();
		}
	}
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}


?>
	<script type="text/javascript">
		function notifica(){
			var unique_id = $.gritter.add({
				// (string | mandatory) the heading of the notification
				title: 'Correcto',
				// (string | mandatory) the text inside the notification
				text: 'Los cambios se han guardado correctamente!',
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
		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Los cambios se han guardado correctamente!.
	</div>
<?php	
	exit();

?>