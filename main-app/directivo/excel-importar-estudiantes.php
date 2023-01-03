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
	if(trim($data->sheets[0]['cells'][$i][1])!="" and trim($data->sheets[0]['cells'][$i][2])!="" and trim($data->sheets[0]['cells'][$i][3])!="" and trim($data->sheets[0]['cells'][$i][4])!="" and trim($data->sheets[0]['cells'][$i][5])!="" and trim($data->sheets[0]['cells'][$i][9])!=""){
		
		//Cuadramos los generos vacíos
		if(trim($data->sheets[0]['cells'][$i][6])=="" or !is_numeric($data->sheets[0]['cells'][$i][6])){
			$data->sheets[0]['cells'][$i][6] = 126;
		}

		//Cuadramos las fechas de nacimientos vacías
		if(trim($data->sheets[0]['cells'][$i][7])==""){
			$data->sheets[0]['cells'][$i][7] = date("Y-m-d");
		}

		//Cuadramos los tipos de documentos vacíos
		if(trim($data->sheets[0]['cells'][$i][8])=="" or !is_numeric($data->sheets[0]['cells'][$i][8])){
			$data->sheets[0]['cells'][$i][8] = 108;
		}
		
		$nombreCompleto = $data->sheets[0]['cells'][$i][1]." ".$data->sheets[0]['cells'][$i][2]." ".$data->sheets[0]['cells'][$i][3];

		mysqli_query($conexion, "INSERT INTO usuarios(uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_idioma, uss_bloqueado, uss_fecha_registro, uss_responsable_registro)VALUES('".$data->sheets[0]['cells'][$i][9]."', '".$data->sheets[0]['cells'][$i][9]."', 4, '".$nombreCompleto."', 1, 0, now(), '".$_SESSION["id"]."')");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
		$idRegistro = mysql_insert_id();


		$datosInsert .="(
		now(),
		1,
		'".$data->sheets[0]['cells'][$i][1]."', 
		'".$data->sheets[0]['cells'][$i][2]."', 
		'".$data->sheets[0]['cells'][$i][3]."', 
		'".$data->sheets[0]['cells'][$i][4]."', 
		'".$data->sheets[0]['cells'][$i][5]."', 
		'".$data->sheets[0]['cells'][$i][6]."', 
		'".$data->sheets[0]['cells'][$i][7]."', 
		'".$data->sheets[0]['cells'][$i][8]."', 
		'".$data->sheets[0]['cells'][$i][9]."', 
		'".$data->sheets[0]['cells'][$i][10]."',
		'".$data->sheets[0]['cells'][$i][11]."',
		'".$data->sheets[0]['cells'][$i][12]."',
		'".$data->sheets[0]['cells'][$i][13]."',
		'".$data->sheets[0]['cells'][$i][14]."',
		'".$data->sheets[0]['cells'][$i][15]."',
		'".$data->sheets[0]['cells'][$i][16]."',
		'".$data->sheets[0]['cells'][$i][17]."',
		'".$idRegistro."'
		),";
		
		
	}
}
//exit();

$datosInsert = substr($datosInsert,0,-1);
		
mysqli_query($conexion, "INSERT INTO academico_matriculas(mat_fecha, mat_estado_matricula, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_email, mat_inclusion, mat_extranjero, mat_id_usuario)VALUES
".$datosInsert."");
$lineaError = __LINE__;
include("../compartido/reporte-errores.php");	


echo '<script type="text/javascript">window.location.href="estudiantes.php";</script>';
exit();


//print_r($data);
//print_r($data->formatRecords);
?>
