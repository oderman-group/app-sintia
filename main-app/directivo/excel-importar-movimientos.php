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
	if(trim($data->sheets[0]['cells'][$i][1])!=""){

		$saldo = abs($data->sheets[0]['cells'][$i][2]);

		//Si es por documento
		if($_POST["datoID"]==1){
			$consultaDatosUsuario=mysqli_query($conexion, "SELECT * FROM usuarios
			WHERE uss_usuario='".$data->sheets[0]['cells'][$i][1]."'");
			$datosUsuario = mysqli_fetch_array($consultaDatosUsuario, MYSQLI_BOTH);	
			
			$idUsuario = $datosUsuario['uss_id'];
			
		}
		//Si es por código de tesorería
		elseif($_POST["datoID"]==2){
			$consultaDatosUsuario=mysqli_query($conexion, "SELECT * FROM academico_matriculas
			WHERE mat_codigo_tesoreria='".$data->sheets[0]['cells'][$i][1]."' AND mat_eliminado=0");
			$datosUsuario = mysqli_fetch_array($consultaDatosUsuario, MYSQLI_BOTH);	
			
			$idUsuario = $datosUsuario['mat_id_usuario'];	
		}
		
		
		if($idUsuario!=""){
			mysqli_query($conexion, "DELETE FROM finanzas_cuentas WHERE fcu_usuario='".$idUsuario."'");

			//No hacer nada
			if($_POST["accion"]==1){
				
				if($data->sheets[0]['cells'][$i][4] == 1){
					$tipo = 3;
				}else{
					$tipo = 4;
				}
				
			}
			//Bloquear a los que deben
			elseif($_POST["accion"]==2){
				if($data->sheets[0]['cells'][$i][4] == 1){
					$tipo = 3;
					mysqli_query($conexion, "UPDATE usuarios SET uss_bloqueado=1 WHERE uss_id='".$idUsuario."'");
				}else{
					$tipo = 4;
					mysqli_query($conexion, "UPDATE usuarios SET uss_bloqueado='0' WHERE uss_id='".$idUsuario."'");
				}
			}

			//echo $saldo." - ".$tipo." - ".$idUsuario."<br>";

			$datosInsert .="(
			now(),
			'".$_POST["detalle"]."',
			'".$saldo."',
			'".$tipo."',
			'".$data->sheets[0]['cells'][$i][3]."',
			'".$idUsuario."',
			0
			),";
		}	
	}
}

$datosInsert = substr($datosInsert,0,-1);
		
mysqli_query($conexion, "INSERT INTO finanzas_cuentas(fcu_fecha, fcu_detalle, fcu_valor, fcu_tipo, fcu_observaciones, fcu_usuario, fcu_anulado)VALUES ".$datosInsert."");
$lineaError = __LINE__;
include("../compartido/reporte-errores.php");

echo '<script type="text/javascript">window.location.href="movimientos.php";</script>';
exit();


//print_r($data);
//print_r($data->formatRecords);
?>
