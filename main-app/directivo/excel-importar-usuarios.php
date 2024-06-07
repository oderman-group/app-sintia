<?php
include("session.php");
require_once("../class/Usuarios.php");
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
			$claves_validar = array('uss_tipo', 'uss_documento', 'uss_nombre', 'uss_apellido1', 'uss_genero');
			$tiposDocumento = [
				'RC'   => '108', 'CC'   => '105', 'CE'   => '109', 'TI'   => '107', 'PP'   => '110', 'PE'   => '139', 'NUIP' => '106', 'PPT'   => '139'
			];
			$tiposUsuarios = [
				'Docente'   => '2', 'Acudiente'   => '3', 'Directivo'   => '5'
			];
			$tiposGenero = [
				'M'   => '126', 'F' => '127'
			];

			$sql = "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_email, uss_celular, uss_genero, uss_foto, uss_portada, uss_idioma, uss_tema, uss_permiso1, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_intentos_fallidos, uss_tema_sidebar,
			uss_tema_header, uss_tema_logo, uss_tipo_documento, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, year) VALUES";
			
			$usuariosCreados      = array();
			$usuariosActualizados = array();
			$usuariosNoCreados    = array();

			while($f<=$numFilas){
				$todoBien = true;

				$arrayIndividual = [
					'uss_tipo'   				=> $hojaActual->getCell('A'.$f)->getValue(),
					'uss_tipo_documento'        => $hojaActual->getCell('B'.$f)->getValue(),
					'uss_documento'          	=> $hojaActual->getCell('C'.$f)->getValue(),
					'uss_nombre'          		=> $hojaActual->getCell('D'.$f)->getValue(),
					'uss_nombre2'  				=> $hojaActual->getCell('E'.$f)->getValue(),
					'uss_apellido1' 			=> $hojaActual->getCell('F'.$f)->getValue(),
					'uss_apellido2'           	=> $hojaActual->getCell('G'.$f)->getValue(),
					'uss_genero' 				=> $hojaActual->getCell('H'.$f)->getValue(),
					'uss_celular'            	=> $hojaActual->getCell('I'.$f)->getValue(),
					'uss_email'            		=> $hojaActual->getCell('J'.$f)->getValue(),
				];

				//Validamos que los campos más importantes no vengan vacios
				foreach ($claves_validar as $clave) {
					if (empty($arrayIndividual[$clave])) {
						$todoBien = false;
					}
				}

				$tipoDocumento = $tiposDocumento[$arrayIndividual['uss_tipo_documento']];

				$tipoUsuario = $tiposUsuarios[$arrayIndividual['uss_tipo']];

				$genero = $tiposGenero[$arrayIndividual['uss_genero']];

				//Si los campos están completos entonces ordenamos los datos del usuario
				if($todoBien) {

					$numUsuarios = Usuarios::validarExistenciaUsuario($arrayIndividual['uss_documento']);

					if($numUsuarios > 0) {

						$datosUsuariosExistente = Usuarios::obtenerDatosUsuario($arrayIndividual['uss_documento']);

						try {
							
							$camposActualizar = "";
							if(!empty($_POST['actualizarCampo'])) {
							
								$camposFormulario = count($_POST['actualizarCampo']);

								if($camposFormulario > 0) {
									$cont = 0;
									while ($cont < $camposFormulario) {
										if($_POST['actualizarCampo'][$cont] == 1) {
											$camposActualizar .= ", uss_tipo_documento='".$tipoDocumento."'";
										}

										if($_POST['actualizarCampo'][$cont] == 2) {
											$camposActualizar .= ", uss_nombre2='".$arrayIndividual['uss_nombre2']."'";
										}

										if($_POST['actualizarCampo'][$cont] == 3) {
											$camposActualizar .= ", uss_apellido2='".$arrayIndividual['uss_apellido2']."'";
										}

										if($_POST['actualizarCampo'][$cont] == 4) {
											$camposActualizar .= ", uss_genero='".$genero."'";
										}

										if($_POST['actualizarCampo'][$cont] == 5) {
											$camposActualizar .= ", uss_celular='".$arrayIndividual['uss_celular']."'";
										}

										if($_POST['actualizarCampo'][$cont] == 6) {
											$camposActualizar .= ", uss_email='".$arrayIndividual['uss_email']."'";
										}
										
										$cont ++;
									}
								}
							}

							//Actualizamos el acudiente y los datos del formulario
							$update = "uss_tipo=uss_tipo {$camposActualizar}";
							UsuariosPadre::actualizarUsuarios($config, $datosUsuariosExistente['uss_id'], $update);

							$usuariosActualizados["FILA_".$f] = $datosUsuariosExistente['uss_documento'];

						} catch (Exception $e) {
							echo "Excepción catpurada: ".$e->getMessage();
							exit();
						}

					} else {

						$idUsuarioEstudiante=Utilidades::generateCode("USS");
						$sql .= "('".$idUsuarioEstudiante."', '".$arrayIndividual['uss_documento']."', '".$clavePorDefectoUsuarios."', '".$tipoUsuario."', '".$arrayIndividual['uss_nombre']."', 0, '".$arrayIndividual['uss_email']."', '".$arrayIndividual['uss_celular']."', '".$genero."', 'default.png', 'default.png', 1, 'green', 1, 0, now(), '".$_SESSION["id"]."', 0, 'cyan-sidebar-color', 'header-indigo', 'logo-indigo', '".$tipoDocumento."', '".$arrayIndividual['uss_apellido1']."', '".$arrayIndividual['uss_apellido2']."', '".$arrayIndividual['uss_nombre2']."', '".$arrayIndividual['uss_documento']."', {$config['conf_id_institucion']}, {$_SESSION["bd"]}),";

						$usuariosCreados["FILA_".$f] = $arrayIndividual['uss_documento'];

					}
				} else {
					$usuariosNoCreados[] = "FILA ".$f;
				}

				$f++;
			}
			
			$numeroUsuariosCreados = 0;
			if(!empty($usuariosCreados)){
				$numeroUsuariosCreados = count($usuariosCreados);
			}

			$numeroUsuariosActualizados = 0;
			if(!empty($usuariosActualizados)){
				$numeroUsuariosActualizados = count($usuariosActualizados);
			}

			$numeroUsuariosNoCreados = 0;
			if(!empty($usuariosNoCreados)){
				$numeroUsuariosNoCreados = count($usuariosNoCreados);
			}

			$respuesta = "
					Resumen del proceso:<br>
					- Total filas leidas: {$numFilas}<br><br>
					- Usuarios creados nuevos: {$numeroUsuariosCreados}<br>
					- Usuarios que ya estaban creados y se les actualizó alguna información seleccionada: {$numeroUsuariosActualizados}<br>
					- Usuarios que les faltó algun campo obligatorio: {$numeroUsuariosNoCreados}<br><br>
				";

			if(!empty($usuariosCreados) && count($usuariosCreados) > 0) {
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
				window.location.href="usuarios.php?success=SC_DT_4&summary="+parametro;
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
			echo '<script type="text/javascript">window.location.href="usuarios-importar-excel.php?error=ER_DT_7&msj='.$message.'";</script>';
			exit();
		}
	}else{
		echo '<script type="text/javascript">window.location.href="usuarios-importar-excel.php?error=ER_DT_8";</script>';
		exit();
	}	
}else{
	$message = "Este archivo no es admitido, por favor verifique que el archivo a importar sea un excel (.xlsx)";
	echo '<script type="text/javascript">window.location.href="usuarios-importar-excel.php?error=ER_DT_7&msj='.$message.'";</script>';
	exit();
}