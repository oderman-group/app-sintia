<?php
include("session.php");
require_once("../class/Usuarios.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require '../../librerias/Excel/vendor/autoload.php';

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
			$claves_validar = array('mat_tipo_documento', 'mat_documento', 'mat_nombres', 'mat_primer_apellido', 'mat_grado');
			$tiposDocumento = [
				'RC'   => '108', 'CC'   => '105', 'CE'   => '109', 'TI'   => '107', 'PP'   => '110', 'PE'   => '139', 'NUIP' => '106', 'PPT'   => '139'
			];
			$tiposGenero = [
				'M'   => '126', 'F' => '127'
			];
			$estratosArray = array("", 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125);
			$sql = "INSERT INTO ".BD_ACADEMICA.".academico_matriculas(mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_id_usuario, mat_acudiente, mat_documento, mat_tipo_documento, mat_grupo, mat_direccion, mat_genero, mat_fecha_nacimiento, mat_barrio, mat_celular, mat_email, mat_estrato, mat_tipo_sangre, mat_eps, mat_nombre2, institucion, year) VALUES";
			
			$estudiantesCreados      = array();
			$estudiantesActualizados = array();
			$estudiantesNoCreados    = array();

			$acudientesCreados       = array();
			$acudientesExistentes    = array();
			$acudientesNoCreados     = array();

			$contE = 1;
			while($f<=$numFilas){
				
				/*
				***************ACUDIENTE********************
				*/
				$idAcudiente = '0000';

				//Validamos que el documento y el nombre del acudiente no venga vacío
				if(!empty($hojaActual->getCell('R'.$f)->getValue()) && !empty($hojaActual->getCell('S'.$f)->getValue())) {
					$datosAcudiente = [
						'uss_usuario' => $hojaActual->getCell('R'.$f)->getValue(),
						'uss_clave'   => $clavePorDefectoUsuarios,
						'uss_tipo'    => 3,
						'uss_nombre'  => $hojaActual->getCell('S'.$f)->getValue(),
					];

					$numUsuarioAcudiente  = Usuarios::validarExistenciaUsuario($datosAcudiente['uss_usuario']);
					if($numUsuarioAcudiente > 0) {
						$datosAcudienteExistente  = Usuarios::obtenerDatosUsuario($datosAcudiente['uss_usuario']);
						$idAcudiente = $datosAcudienteExistente['uss_id'];
						$acudientesExistentes["FILA_".$f] = $datosAcudienteExistente['uss_usuario'];
					} else {
						$idAcudiente = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_idioma, institucion, year, uss_id", [$datosAcudiente['uss_usuario'], $datosAcudiente['uss_clave'], $datosAcudiente['uss_tipo'], $datosAcudiente['uss_nombre'], 1, $config['conf_id_institucion'], $_SESSION["bd"]]);
						
						$acudientesCreados["FILA_".$f] = $datosAcudiente['uss_usuario'];
					}
				} else {
					$acudientesNoCreados[] = "FILA ".$f;
				}

				/*
				***************ESTUDIANTE********************
				*/
				$todoBien = true;

				$arrayIndividual = [
					'mat_matricula'		   => (strtotime("now")+$f),
					'mat_tipo_documento'   => $hojaActual->getCell('A'.$f)->getValue(),
					'mat_documento'        => $hojaActual->getCell('B'.$f)->getValue(),
					'mat_nombres'          => $hojaActual->getCell('C'.$f)->getValue(),
					'mat_nombre2'          => $hojaActual->getCell('D'.$f)->getValue(),
					'mat_primer_apellido'  => $hojaActual->getCell('E'.$f)->getValue(),
					'mat_segundo_apellido' => $hojaActual->getCell('F'.$f)->getValue(),
					'mat_genero'           => $hojaActual->getCell('G'.$f)->getValue(),
					'mat_fecha_nacimiento' => $hojaActual->getCell('H'.$f)->getFormattedValue(),
					'mat_grado'            => $hojaActual->getCell('I'.$f)->getValue(),
					'mat_grupo'            => $hojaActual->getCell('J'.$f)->getValue(),
					'mat_direccion'        => $hojaActual->getCell('K'.$f)->getValue(),
					'mat_barrio'           => $hojaActual->getCell('L'.$f)->getValue(),
					'mat_celular'          => $hojaActual->getCell('M'.$f)->getValue(),
					'mat_email'            => $hojaActual->getCell('N'.$f)->getValue(),
					'mat_estrato'          => $hojaActual->getCell('O'.$f)->getValue(),
					'mat_tipo_sangre'      => $hojaActual->getCell('P'.$f)->getValue(),
					'mat_eps'              => $hojaActual->getCell('Q'.$f)->getValue(),
					'mat_acudiente'        => $idAcudiente,
					
				];

				//Validamos que los campos más importantes no vengan vacios
				foreach ($claves_validar as $clave) {
					if (empty($arrayIndividual[$clave])) {
						$todoBien = false;
					}
				}

				$tipoDocumento = $tiposDocumento[$arrayIndividual['mat_tipo_documento']];

				$genero = $tiposGenero[$arrayIndividual['mat_genero']];

				$grado = "";
				if(!empty($arrayIndividual['mat_grado'])) {
					$datos = Grados::obtenerGradoPorNombre($config, $arrayIndividual['mat_grado']);
					if(!empty($datos['gra_id'])){
						$grado = $datos['gra_id'];
					}
					
				}
				
				$grupo = 1;
				if(!empty($arrayIndividual['mat_grupo'])) {
					switch($arrayIndividual['mat_grupo']){
						case 'A';
							$grupo = 1;
						break;

						case 'B';
							$grupo = 2;
						break;

						case 'C';
							$grupo = 3;
						break;
					}
				}

				//Si los campos están completos entonces ordenamos los datos del estudiante
				if($todoBien) {

					$numMatricula = Estudiantes::validarExistenciaEstudiante($arrayIndividual['mat_documento']);

					if($numMatricula > 0) {

						$datosEstudianteExistente = Estudiantes::obtenerDatosEstudiante($arrayIndividual['mat_documento']);

						try {
							
							$camposActualizar = "";
							if(!empty($_POST['actualizarCampo'])) {
							
								$camposFormulario = count($_POST['actualizarCampo']);

								if($camposFormulario > 0) {
									$cont = 0;
									while ($cont < $camposFormulario) {
										if($_POST['actualizarCampo'][$cont] == 1) {
											$camposActualizar .= ", mat_grado='".$grado."'";
										}

										if($_POST['actualizarCampo'][$cont] == 2) {
											$camposActualizar .= ", mat_grupo='".$grupo."'";
										}

										if($_POST['actualizarCampo'][$cont] == 3) {
											$camposActualizar .= ", mat_tipo_documento='".$tipoDocumento."'";
										}

										if($_POST['actualizarCampo'][$cont] == 4) {
											$camposActualizar .= ", mat_acudiente='".$idAcudiente."'";
										}

										if($_POST['actualizarCampo'][$cont] == 5) {
											$camposActualizar .= ", mat_nombre2='".$hojaActual->getCell('D'.$f)->getValue()."'";
										}

										if($_POST['actualizarCampo'][$cont] == 6) {

											$matFechaNacimiento=$hojaActual->getCell('H'.$f)->getFormattedValue();
											$fNacimiento = "0000-00-00";
											if(!empty($matFechaNacimiento)) {
												$arrayBuscar = array('-', '.', ' ', '.-');
												$arrayReemplazar = array('/', '/', '/', '/');
												$fechaReplace = str_replace($arrayBuscar, $arrayReemplazar, $matFechaNacimiento);							
												$fecha = explode ("/", $fechaReplace);

												$dia   = $fecha[2];  
												$mes = $fecha[1];  
												$year  = $fecha[0];
												$fNacimiento = $year.'-'.$mes.'-'.$dia;
											}

											$camposActualizar .= ", mat_fecha_nacimiento='".$fNacimiento."'";
										}
										
										$cont ++;
									}
								}
							}

							//Actualizamos el acudiente y los datos del formulario
							$update = "mat_matricula=mat_matricula {$camposActualizar}";
							Estudiantes::actualizarMatriculasPorId($config, $datosEstudianteExistente['mat_id'], $update);

							//Verificamos que el array no venga vacio y adicionalmente que tenga el campo acudiente seleccionado para actualizarce
							if (!empty($_POST['actualizarCampo']) && in_array(4, $_POST['actualizarCampo'])) {
								//Borramos si hay alguna asociación igual y creamos la nueva
								try{
									mysqli_query($conexion, "DELETE FROM ".BD_GENERAL.".usuarios_por_estudiantes WHERE upe_id_usuario='".$idAcudiente."' AND upe_id_estudiante='".$datosEstudianteExistente['mat_id']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
								} catch (Exception $e) {
									include("../compartido/error-catch-to-report.php");
								}

								$idInsercion = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'usuarios_por_estudiantes');
								try{
									mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios_por_estudiantes(upe_id, upe_id_usuario, upe_id_estudiante, institucion, year)VALUES('" .$idInsercion . "', '".$idAcudiente."', '".$datosEstudianteExistente['mat_id']."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
								} catch (Exception $e) {
									include("../compartido/error-catch-to-report.php");
								}
							}

							$estudiantesActualizados["FILA_".$f] = $datosEstudianteExistente['mat_documento'];

						} catch (Exception $e) {
							echo "Excepción catpurada: ".$e->getMessage();
							exit();
						}

					} else {

						$fNacimiento = "0000-00-00";
						if(!empty($arrayIndividual['mat_fecha_nacimiento'])) {
							$arrayBuscar = array('-', '.', ' ', '.-');
							$arrayReemplazar = array('/', '/', '/', '/');
							$fechaReplace = str_replace($arrayBuscar, $arrayReemplazar, $arrayIndividual['mat_fecha_nacimiento']);							
							$fecha = explode ("/", $fechaReplace);

							$dia   = $fecha[2];  
							$mes = $fecha[1];  
							$year  = $fecha[0];
							$fNacimiento = $year.'-'.$mes.'-'.$dia;
						}
						
						$estrato = 116;
						if(!empty($arrayIndividual['mat_estrato'])) {
							$estrato = $estratosArray[$arrayIndividual['mat_estrato']];
						}

						$email = $arrayIndividual['mat_matricula'].'@plataformasintia.com';
						if(!empty($arrayIndividual['mat_email'])) {
							$email = strtolower($arrayIndividual['mat_email']);
						}

						$arrayTodos[$f] = $arrayIndividual;

						$idUsuarioEstudiante = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_idioma, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_intentos_fallidos, uss_tipo_documento, uss_apellido1, uss_apellido2, uss_nombre2,uss_documento, institucion, year, uss_id", [$arrayIndividual['mat_documento'], $clavePorDefectoUsuarios, 4, $arrayIndividual['mat_nombres'], 0, 1, 0, date("Y-m-d H:i:s"), $_SESSION["id"], 0, $tipoDocumento, $arrayIndividual['mat_primer_apellido'], $arrayIndividual['mat_segundo_apellido'], $arrayIndividual['mat_nombre2'], $arrayIndividual['mat_documento'], $config['conf_id_institucion'], $_SESSION["bd"]]);

						$codigoMAT = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_matriculas').$contE;

						$sql .= "('".$codigoMAT."', '".$arrayIndividual['mat_matricula']."', NOW(), '".$arrayIndividual['mat_primer_apellido']."', '".$arrayIndividual['mat_segundo_apellido']."', '".$arrayIndividual['mat_nombres']."', '".$grado."', '".$idUsuarioEstudiante."', '".$idAcudiente."', '".$arrayIndividual['mat_documento']."', '".$tipoDocumento."', '".$grupo."', '".$arrayIndividual['mat_direccion']."', '".$genero."', '".$fNacimiento."', '".$arrayIndividual['mat_barrio']."', '".$arrayIndividual['mat_celular']."', '".$email."', '".$estrato."', '".$arrayIndividual['mat_tipo_sangre']."', '".$arrayIndividual['mat_eps']."', '".$arrayIndividual['mat_nombre2']."', {$config['conf_id_institucion']}, {$_SESSION["bd"]}),";

						$estudiantesCreados["FILA_".$f] = $arrayIndividual['mat_documento'];

						$contE++;

					}
				} else {
					$estudiantesNoCreados[] = "FILA ".$f;
				}

				$f++;
			}
			
			$numeroEstudiantesCreados = 0;
			if(!empty($estudiantesCreados)){
				$numeroEstudiantesCreados = count($estudiantesCreados);
			}

			$numeroEstudiantesActualizados = 0;
			if(!empty($estudiantesActualizados)){
				$numeroEstudiantesActualizados = count($estudiantesActualizados);
			}

			$numeroEstudiantesNoCreados = 0;
			if(!empty($estudiantesNoCreados)){
				$numeroEstudiantesNoCreados = count($estudiantesNoCreados);
			}

			$numeroAcudientesCreados = 0;
			if(!empty($acudientesCreados)){
				$numeroAcudientesCreados = count($acudientesCreados);
			}

			$numeroAcudientesExistentes = 0;
			if(!empty($acudientesExistentes)){
				$numeroAcudientesExistentes = count($acudientesExistentes);
			}

			$numeroAcudientesNoCreados = 0;
			if(!empty($acudientesNoCreados)){
				$numeroAcudientesNoCreados = count($acudientesNoCreados);
			}

			$respuesta = [
				"summary" => "
					Resumen del proceso:<br>
					- Total filas leidas: {$numFilas}<br><br>
					- Estudiantes creados nuevos: {$numeroEstudiantesCreados}<br>
					- Estudiantes que ya estaban creados y se les actualizó alguna información seleccionada: {$numeroEstudiantesActualizados}<br>
					- Estudiantes que les faltó algun campo obligatorio: {$numeroEstudiantesNoCreados}<br><br>

					- Acudientes creados nuevos: {$numeroAcudientesCreados}<br>
					- Acudientes que ya estaban creados y no hubo necesidad de volverlos a crear: {$numeroAcudientesExistentes}<br>
					- Acudientes que les faltó el documento o el nombre: {$numeroAcudientesNoCreados}<br><br>
				"
			];

			$summary = http_build_query($respuesta);

			if(!empty($estudiantesCreados) && count($estudiantesCreados) > 0) {
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
			
			echo '<script type="text/javascript">window.location.href="estudiantes.php?cantidad=10&success=SC_DT_4&'.$summary.'";</script>';
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
			echo '<script type="text/javascript">window.location.href="estudiantes-importar-excel.php?error=ER_DT_7&msj='.$message.'";</script>';
			exit();
		}
	}else{
		echo '<script type="text/javascript">window.location.href="estudiantes-importar-excel.php?error=ER_DT_8";</script>';
		exit();
	}	
}else{
	$message = "Este archivo no es admitido, por favor verifique que el archivo a importar sea un excel (.xlsx)";
	echo '<script type="text/javascript">window.location.href="estudiantes-importar-excel.php?error=ER_DT_7&msj='.$message.'";</script>';
	exit();
}