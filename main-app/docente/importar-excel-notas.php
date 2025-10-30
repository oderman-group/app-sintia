<?php include("session.php");?>
<?php include("verificar-carga.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
if($_FILES['planilla']['name']!=""){
	$archivo = $_FILES['planilla']['name']; $destino = "../files/excel";
	move_uploaded_file($_FILES['planilla']['tmp_name'], $destino ."/".$archivo);
}
?>
<?php
//set_time_limit (0);

// Test CVS
require_once '../../librerias/Excel/reader.php';


// ExcelFile($filename, $encoding);
$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('CP1251');

//Read File
$data->read('../files/excel/'.$archivo);

error_reporting(E_ALL ^ E_NOTICE);
/*
for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
		echo $data->sheets[0]['cells'][$i][$j].", ";	
	}
	echo "<br>";
}
*/

// Migrado a PDO - Consultas preparadas para importación Excel
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');

$sqlCheck = "SELECT cal_id_actividad, cal_id_estudiante FROM ".BD_ACADEMICA.".academico_calificaciones 
             WHERE cal_id_actividad=? AND cal_id_estudiante=? AND institucion=? AND year=?";
$stmtCheck = $conexionPDO->prepare($sqlCheck);

$sqlUpdate = "UPDATE ".BD_ACADEMICA.".academico_calificaciones 
              SET cal_nota=?, cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1 
              WHERE cal_id_actividad=? AND cal_id_estudiante=? AND institucion=? AND year=?";
$stmtUpdate = $conexionPDO->prepare($sqlUpdate);

$sqlInsert = "INSERT INTO ".BD_ACADEMICA.".academico_calificaciones(
    cal_id, cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, 
    cal_cantidad_modificaciones, cal_observaciones, institucion, year
) VALUES (?, ?, ?, ?, now(), 0, ?, ?, ?)";
$stmtInsert = $conexionPDO->prepare($sqlInsert);

for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
	if(trim($data->sheets[0]['cells'][$i][2])!="" and trim($data->sheets[0]['cells'][$i][4])!=""){
		try {
			$estudianteId = $data->sheets[0]['cells'][$i][2];
			$nota = $data->sheets[0]['cells'][$i][4];
			$observaciones = $data->sheets[0]['cells'][$i][5] ?? '';
			
			$stmtCheck->bindParam(1, $_POST["idR"], PDO::PARAM_STR);
			$stmtCheck->bindParam(2, $estudianteId, PDO::PARAM_STR);
			$stmtCheck->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
			$stmtCheck->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
			$stmtCheck->execute();
			$numE = $stmtCheck->rowCount();
			
			if($numE==0){
				// Insertar nueva calificación
				$codigo = Utilidades::generateCode("CAL");
				$stmtInsert->bindParam(1, $codigo, PDO::PARAM_STR);
				$stmtInsert->bindParam(2, $estudianteId, PDO::PARAM_STR);
				$stmtInsert->bindParam(3, $nota, PDO::PARAM_STR);
				$stmtInsert->bindParam(4, $_POST["idR"], PDO::PARAM_STR);
				$stmtInsert->bindParam(5, $observaciones, PDO::PARAM_STR);
				$stmtInsert->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
				$stmtInsert->bindParam(7, $_SESSION["bd"], PDO::PARAM_INT);
				$stmtInsert->execute();
			}else{
				// Actualizar calificación existente
				$stmtUpdate->bindParam(1, $nota, PDO::PARAM_STR);
				$stmtUpdate->bindParam(2, $_POST["idR"], PDO::PARAM_STR);
				$stmtUpdate->bindParam(3, $estudianteId, PDO::PARAM_STR);
				$stmtUpdate->bindParam(4, $config['conf_id_institucion'], PDO::PARAM_INT);
				$stmtUpdate->bindParam(5, $_SESSION["bd"], PDO::PARAM_INT);
				$stmtUpdate->execute();
			}
		} catch (Exception $e) {
			$lineaError = __LINE__;
			include("../compartido/reporte-errores.php");
		}
	}
}

Actividades::marcarActividadRegistrada($config, $_POST["idR"]);

echo '<script type="text/javascript">window.location.href="calificaciones.php";</script>';
exit();


//print_r($data);
//print_r($data->formatRecords);
?>
