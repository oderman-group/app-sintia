<?php
include("session.php");
require_once("../class/Usuarios.php");
require_once("../class/Estudiantes.php");
require '../../librerias/Excel/vendor/autoload.php';
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

use PhpOffice\PhpSpreadsheet\IOFactory;

$temName=$_FILES['planilla']['tmp_name'];
$archivo = $_FILES['planilla']['name'];
$destino = "../files/excel/";
$explode = explode(".", $archivo);
$extension = end($explode);
$fullArchivo = uniqid('importado_').".".$extension;
$nombreArchivo= $destino.$fullArchivo;

if($extension == 'xlsx'){

	if (move_uploaded_file($temName, $nombreArchivo)) {		
		
		if ($_FILES['planilla']['error'] === UPLOAD_ERR_OK){

			$documento= IOFactory::load($nombreArchivo);
			$totalHojas= $documento->getSheetCount();

			$hojaActual = $documento->getSheet(0);
			$numFilas = $hojaActual->getHighestDataRow();
			if($_POST["filaFinal"] > 0){
				$numFilas = $_POST["filaFinal"];
			}
			$letraColumnas= $hojaActual->getHighestDataColumn();
			$f=3;
			$arrayTodos = [];
			$claves_validar = array('fcu_usuario', 'fcu_valor', 'fcu_tipo');
			$tiposMovimientos = ['DEUDA'   => '1', 'A FAVOR'   => '2'];
			$sql = "INSERT INTO ".BD_FINANCIERA.".finanzas_cuentas(fcu_id, fcu_fecha, fcu_detalle, fcu_valor, fcu_tipo, fcu_observaciones, fcu_usuario, fcu_anulado, institucion, year)VALUES";
			
			$movimientosCreados     = array();
			$movimientosNoCreados   = array();
			$usuariosBloqueados    	= array();

			while($f<=$numFilas){

				$todoBien = true;

				$arrayIndividual = [
					'fcu_usuario'   		=> $hojaActual->getCell('A'.$f)->getValue(),
					'fcu_valor'        		=> $hojaActual->getCell('B'.$f)->getValue(),
					'fcu_observaciones'     => $hojaActual->getCell('C'.$f)->getValue(),
					'fcu_tipo'          	=> $hojaActual->getCell('D'.$f)->getValue(),
				];

				//Validamos que los campos más importantes no vengan vacios
				foreach ($claves_validar as $clave) {
					if (empty($arrayIndividual[$clave])) {
						$todoBien = false;
					}
				}

				$tipoMovimiento = $tiposMovimientos[$arrayIndividual['fcu_tipo']];

				//Si los campos están completos entonces ordenamos los datos del usuario
				if($todoBien) {

					if($_POST["datoID"]==1){//Si es por documento

						$datosUsuario  = Usuarios::obtenerDatosUsuario($arrayIndividual['fcu_usuario']);						
						$idUsuario = $datosUsuario['uss_id'];

					}elseif($_POST["datoID"]==2){//Si es por código de tesorería


						$consultaDatosUsuario=Estudiantes::obtenerListadoDeEstudiantes(" AND mat_codigo_tesoreria='".$arrayIndividual['fcu_usuario']."' AND mat_eliminado=0");
						$datosUsuario = mysqli_fetch_array($consultaDatosUsuario, MYSQLI_BOTH);
						$idUsuario = $datosUsuario['mat_id_usuario'];

					}

					if(!empty($idUsuario)){
						try{
							mysqli_query($conexion, "DELETE FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='".$idUsuario."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
						} catch (Exception $e) {
							include("../compartido/error-catch-to-report.php");
						}

						if($_POST["accion"]==1){//No hacer nada
							
							if($tipoMovimiento == 1){
								$tipo = 3;
							}else{
								$tipo = 4;
							}
							
						}elseif($_POST["accion"]==2){//Bloquear a los que deben
							if($tipoMovimiento == 1){
								$tipo = 3;
								$update = "uss_bloqueado=1";
								UsuariosPadre::actualizarUsuarios($config, $idUsuario, $update);
								$usuariosBloqueados[] = "FILA ".$f;
							}else{
								$tipo = 4;
								$update = "uss_bloqueado='0'";
								UsuariosPadre::actualizarUsuarios($config, $idUsuario, $update);
							}
						}

						$idInsercion=Utilidades::generateCode("FCU");
						$sql .="('" .$idInsercion . "', now(), '".$_POST["detalle"]."', '".$arrayIndividual['fcu_valor']."', '".$tipo."', '".$arrayIndividual['fcu_observaciones']."', '".$idUsuario."', 0, {$config['conf_id_institucion']}, {$_SESSION["bd"]}),";

						$movimientosCreados["FILA_".$f] = $arrayIndividual['fcu_usuario'];
					} else {
						$movimientosNoCreados[] = "FILA ".$f;
					}

				} else {
					$movimientosNoCreados[] = "FILA ".$f;
				}

				$f++;
			}
			
			$numeroMovimientosCreados = 0;
			if(!empty($movimientosCreados)){
				$numeroMovimientosCreados = count($movimientosCreados);
			}

			$numeroMovimientosNoCreados = 0;
			if(!empty($movimientosNoCreados)){
				$numeroMovimientosNoCreados = count($movimientosNoCreados);
			}
			
			$numeroUsuariosBloqueados = 0;
			if(!empty($usuariosBloqueados)){
				$numeroUsuariosBloqueados = count($usuariosBloqueados);
			}

			$respuesta =  "
					Resumen del proceso:<br>
					- Total filas leidas: {$numFilas}<br><br>
					- Movimientos creados nuevos: {$numeroMovimientosCreados}<br>
					- Movimientos que les faltó algun campo obligatorio: {$numeroMovimientosNoCreados}<br><br>
					- Usuarios bloqueados por deuda: {$numeroUsuariosBloqueados}
				"
			;

			if(!empty($movimientosCreados) && count($movimientosCreados) > 0) {
				$sql = substr($sql, 0, -1);
				try {
					mysqli_query($conexion, $sql);
				} catch(Exception $e){
					print_r($sql);
					echo "<br>Hubo un error al guardar todo los datos: ".$e->getMessage();
					exit();
				}
			}

			if(file_exists($nombreArchivo)){
				unlink($nombreArchivo);
			}
?>	
			<script type="text/javascript">
				var mensajeEncriptado = "<?php echo base64_encode($respuesta); ?>";
				var parametro = encodeURIComponent(mensajeEncriptado);
				window.location.href="movimientos.php?success=SC_DT_4&summary="+parametro;
			</script>		
<?php			
			exit();

		}else{
			switch ($_FILES['planilla']['error']) {
				case UPLOAD_ERR_INI_SIZE:
					$message = "El fichero subido excede la directiva upload_max_filesize de php.ini.";
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$message = "El fichero subido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML.";
					break;
		
				case UPLOAD_ERR_PARTIAL:
					$message = "El fichero fue sólo parcialmente subido.";
					break;
		
				case UPLOAD_ERR_NO_FILE:
					$message = "No se subió ningún fichero.";
					break;
		
				case UPLOAD_ERR_NO_TMP_DIR:
					$message = "Falta la carpeta temporal.";
					break;
		
				case UPLOAD_ERR_CANT_WRITE:
					$message = "No se pudo escribir el fichero en el disco.";
					break;
				case UPLOAD_ERR_EXTENSION:
					$message = "Una extensión de PHP detuvo la subida de ficheros. PHP no proporciona una forma de determinar la extensión que causó la parada de la subida de ficheros; el examen de la lista de extensiones cargadas con phpinfo() puede ayudar.";
					break;
			}
			echo '<script type="text/javascript">window.location.href="movimientos-importar.php?error=ER_DT_7&msj='.$message.'";</script>';
			exit();
		}
	}else{
		echo '<script type="text/javascript">window.location.href="movimientos-importar.php?error=ER_DT_8";</script>';
		exit();
	}	
}else{
	$message = "Este archivo no es admitido, por favor verifique que el archivo a importar sea un excel (.xlsx)";
	echo '<script type="text/javascript">window.location.href="movimientos-importar.php?error=ER_DT_7&msj='.$message.'";</script>';
	exit();
}