<?php
session_start();
include("../modelo/conexion.php");
include("../../config-general/config.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

if(trim($_POST["nota"])==""){
    echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";
	exit();
}
if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<1) $_POST["nota"] = 1;

$rB = Boletin::traerNotaBoletinCargaPeriodo($config, $_POST["per"], $_POST["codEst"], $_POST["carga"]);

if(empty($rB['bol_id'])){
	if(!empty($rB['bol_id'])){
		Boletin::eliminarNotaBoletinID($config, $rB['bol_id']);
	}

	Boletin::guardarNotaBoletin($conexionPDO, "bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo, bol_observaciones, institucion, year, bol_id", [$_POST["carga"],$_POST["codEst"],$_POST["per"],$_POST["nota"], 4, 'Colocada DEF. por docente.', $config['conf_id_institucion'], $_SESSION["bd"]]);
}else{
	$update = "bol_nota_anterior=bol_nota, bol_nota='".$_POST["nota"]."', bol_observaciones='Colocada DEF. por docente.', bol_tipo=4";
	Boletin::actualizarNotaBoletin($config, $rB['bol_id'], $update);
}
?>
    <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Los cambios se ha guardado correctamente!.
 	</div>
<?php	
	exit();
?>