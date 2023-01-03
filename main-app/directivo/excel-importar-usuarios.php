<?php include("session.php");?>
<?php
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

$datosInsert = '';
for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

	//Campos obligatorios para llenar el registro
	if(trim($data->sheets[0]['cells'][$i][1])!="" and trim($data->sheets[0]['cells'][$i][2])!="" and trim($data->sheets[0]['cells'][$i][3])!=""){
		
		//Cuadramos los generos vacíos
		if(trim($data->sheets[0]['cells'][$i][5])=="" or !is_numeric($data->sheets[0]['cells'][$i][5])){
			$data->sheets[0]['cells'][$i][5] = 126;
		}
	}	
	

		mysqli_query($conexion, "INSERT INTO usuarios(uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_idioma, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_email)VALUES(
			'".$data->sheets[0]['cells'][$i][1]."', 
			'".$data->sheets[0]['cells'][$i][1]."', 
			'".$data->sheets[0]['cells'][$i][6]."', 
			'".$data->sheets[0]['cells'][$i][2]."', 
			1, 
			0, 
			now(), 
			'".$_SESSION["id"]."', 
			'".$data->sheets[0]['cells'][$i][3]."'
			)");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
		$idRegistro = mysqli_insert_id($conexion);


}
//exit();
	

echo '<script type="text/javascript">window.location.href="usuarios.php";</script>';
exit();


//print_r($data);
//print_r($data->formatRecords);
?>
