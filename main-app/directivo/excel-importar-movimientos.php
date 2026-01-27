<?php
include("session.php");
require_once("../class/Usuarios.php");
require_once("../class/Estudiantes.php");
require '../../librerias/Excel/vendor/autoload.php';
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");

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
			$sql = "INSERT INTO ".BD_FINANCIERA.".finanzas_cuentas(fcu_id, fcu_fecha, fcu_detalle, fcu_valor, fcu_tipo, fcu_observaciones, fcu_usuario, fcu_anulado, fcu_consecutivo, fcu_status, fcu_created_by, fcu_origen, institucion, year) VALUES ";

			$movimientosCreados     = array();
			$movimientosNoCreados   = array();
			$usuariosBloqueados    	= array();

			// Crear conexión PDO para las eliminaciones y consultas
			$conexionPDO = Conexion::newConnection('PDO');
			$conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Siguiente consecutivo por tipo (ingreso=1, egreso=2) para institución y año
			$nextIngreso = (int)($config['conf_inicio_recibos_ingreso'] ?? 1);
			$nextEgreso  = (int)($config['conf_inicio_recibos_egreso'] ?? 1);
			$inst = (int)$config['conf_id_institucion'];
			$year = $_SESSION['bd'];
			foreach ([1 => 'nextIngreso', 2 => 'nextEgreso'] as $tipoVal => $var) {
				$stmtMax = $conexionPDO->prepare("SELECT COALESCE(MAX(fcu_consecutivo), 0) AS m FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_tipo=? AND institucion=? AND year=?");
				$stmtMax->execute([$tipoVal, $inst, $year]);
				$row = $stmtMax->fetch(PDO::FETCH_ASSOC);
				$max = $row ? (int)$row['m'] : 0;
				if ($max > 0) {
					if ($tipoVal === 1) $nextIngreso = $max + 1;
					else $nextEgreso = $max + 1;
				}
			}

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

				// Normalizar y validar el tipo de movimiento del Excel
				$tipoMovimiento = null;
				if ($todoBien) {
					$tipoExcel = trim(strtoupper($arrayIndividual['fcu_tipo'] ?? ''));
					
					// Mapear según el valor del Excel: DEUDA -> tipo 1 (venta), saldo a favor/A FAVOR -> tipo 2 (compra)
					if (stripos($tipoExcel, 'DEUDA') !== false) {
						$tipoMovimiento = FACTURA_VENTA; // tipo 1
					} elseif (stripos($tipoExcel, 'A FAVOR') !== false || stripos($tipoExcel, 'SALDO A FAVOR') !== false) {
						$tipoMovimiento = FACTURA_COMPRA; // tipo 2
					} else {
						// Si no coincide con ninguno, marcarlo como error
						$todoBien = false;
						$movimientosNoCreados[] = "FILA ".$f." - Tipo de movimiento inválido: ".$arrayIndividual['fcu_tipo']." (solo se permiten DEUDA o saldo a favor/A FAVOR)";
					}
				}

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
							// Primero obtener los fcu_id de las facturas que se van a eliminar
							$sqlFacturas = "SELECT fcu_id FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario=:idUsuario AND institucion=:institucion AND year=:year";
							$stmtFacturas = $conexionPDO->prepare($sqlFacturas);
							$stmtFacturas->bindParam(':idUsuario', $idUsuario, PDO::PARAM_STR);
							$stmtFacturas->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
							$stmtFacturas->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
							$stmtFacturas->execute();
							
							$fcuIds = [];
							while ($factura = $stmtFacturas->fetch(PDO::FETCH_ASSOC)) {
								$fcuIds[] = (int)$factura['fcu_id'];
							}
							
							if (!empty($fcuIds)) {
								// Construir placeholders para la consulta IN
								$placeholders = implode(',', array_fill(0, count($fcuIds), '?'));
								
								// Eliminar primero los transaction_items relacionados
								$sqlItems = "DELETE FROM ".BD_FINANCIERA.".transaction_items WHERE id_transaction IN ({$placeholders}) AND institucion=? AND year=?";
								$stmtItems = $conexionPDO->prepare($sqlItems);
								$paramsItems = array_merge($fcuIds, [$config['conf_id_institucion'], $_SESSION["bd"]]);
								for ($i = 0; $i < count($paramsItems); $i++) {
									$stmtItems->bindValue($i + 1, $paramsItems[$i], is_int($paramsItems[$i]) ? PDO::PARAM_INT : PDO::PARAM_STR);
								}
								$stmtItems->execute();
								
								// Eliminar los pagos relacionados (payments_invoiced)
								$sqlPagos = "DELETE FROM ".BD_FINANCIERA.".payments_invoiced WHERE invoiced IN ({$placeholders}) AND institucion=? AND year=?";
								$stmtPagos = $conexionPDO->prepare($sqlPagos);
								$paramsPagos = array_merge($fcuIds, [$config['conf_id_institucion'], $_SESSION["bd"]]);
								for ($i = 0; $i < count($paramsPagos); $i++) {
									$stmtPagos->bindValue($i + 1, $paramsPagos[$i], is_int($paramsPagos[$i]) ? PDO::PARAM_INT : PDO::PARAM_STR);
								}
								$stmtPagos->execute();
							}
							
							// Ahora sí eliminar las facturas
							$sqlEliminar = "DELETE FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario=:idUsuario AND institucion=:institucion AND year=:year";
							$stmtEliminar = $conexionPDO->prepare($sqlEliminar);
							$stmtEliminar->bindParam(':idUsuario', $idUsuario, PDO::PARAM_STR);
							$stmtEliminar->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
							$stmtEliminar->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
							$stmtEliminar->execute();
						} catch (Exception $e) {
							include("../compartido/error-catch-to-report.php");
						}

					// Usar directamente el tipo de movimiento (1 o 2)
					$tipo = $tipoMovimiento;
					$consecutivo = ($tipo == 1) ? $nextIngreso++ : $nextEgreso++;

					if($_POST["accion"]==2){//Bloquear a los que deben
						// Solo bloquear si es tipo 1 (FACTURA_VENTA/DEUDA)
						if($tipoMovimiento == FACTURA_VENTA){
							$update = ['uss_bloqueado' => 1];
							UsuariosPadre::actualizarUsuarios($config, $idUsuario, $update);
							$usuariosBloqueados[] = "FILA ".$f;
						}else{
							// Desbloquear si tiene saldo a favor
							$update = ['uss_bloqueado' => '0'];
							UsuariosPadre::actualizarUsuarios($config, $idUsuario, $update);
						}
					}

						$idInsercion=Utilidades::getNextIdSequence($conexionPDO, BD_FINANCIERA, 'finanzas_cuentas');
						// Escapar valores para prevenir SQL injection
						$detalleEscapado = mysqli_real_escape_string($conexion, $_POST["detalle"]);
						$valorEscapado = mysqli_real_escape_string($conexion, $arrayIndividual['fcu_valor']);
						$observacionesEscapadas = mysqli_real_escape_string($conexion, $arrayIndividual['fcu_observaciones'] ?? '');
						$createdByEscapado = mysqli_real_escape_string($conexion, $_SESSION["id"]);
						$origen = 'NORMAL';
						$sql .="('" .$idInsercion . "', now(), '".$detalleEscapado."', '".$valorEscapado."', '".$tipo."', '".$observacionesEscapadas."', '".$idUsuario."', 0, ".(int)$consecutivo.", '".POR_COBRAR."', '".$createdByEscapado."', '".$origen."', {$config['conf_id_institucion']}, {$_SESSION["bd"]}),";

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