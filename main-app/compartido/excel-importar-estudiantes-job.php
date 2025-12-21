<?php

$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(dirname(dirname(__FILE__))));
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once ROOT_PATH . "/main-app/class/Conexion.php";

$conexionPDO = Conexion::newConnection('PDO');
$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion);

/**
 * Esta variable $clavePorDefectoUsuarios están en el archivo config.php
 * pero la vamos a dejar aquí por ahora porque el config llama a otros
 * archivos que tienen ruta relativa.
 */
$clavePorDefectoUsuarios = SHA1('12345678');

require_once(ROOT_PATH . "/main-app/class/Sysjobs.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");
require ROOT_PATH . '/librerias/Excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$parametrosBuscar = [
    'tipo'   => JOBS_TIPO_IMPORTAR_ESTUDIANTES_EXCEL,
    'estado' => JOBS_ESTADO_PENDIENTE,
];

$listadoCrobjobs = SysJobs::listar($parametrosBuscar);

while ($resultadoJobs = mysqli_fetch_array($listadoCrobjobs, MYSQLI_BOTH)) {
    $datos = [
        'id'     => $resultadoJobs['job_id'],
        'estado' => JOBS_ESTADO_PROCESO,
    ];

    SysJobs::actualizar($datos);

    // Fecha de inicio del proceso
    $fechaInicio   = new DateTime();
    $finalizado    = false;
    $parametros    = json_decode($resultadoJobs['job_parametros'], true);
    $institucionId = $resultadoJobs['job_id_institucion'];
    $anio          = $resultadoJobs['job_year'];
    $intento       = intval($resultadoJobs['job_intentos']);

    $_SESSION['id']            = $resultadoJobs['job_responsable'];
    $_SESSION['bd']            = $resultadoJobs['job_year'];
    $_SESSION['idInstitucion'] = $resultadoJobs['job_id_institucion'];

    $nombreArchivo   = $parametros['nombreArchivo'];
    $filaFinal       = $parametros['filaFinal'];
    $actualizarCampo = $parametros['actualizarCampo'];

    if (empty($config)) {
        $stmt = mysqli_prepare($conexion, 'SELECT * FROM ' . BD_ADMIN . '.configuracion WHERE conf_id_institucion = ? AND conf_agno = ?');
        mysqli_stmt_bind_param($stmt, 'si', $institucionId, $anio);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $config = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }

	   try {
	       $archivo = explode('..', $nombreArchivo);
	       $direccionArchivo = ROOT_PATH . '/main-app' . $archivo[1];

	       $documento  = IOFactory::load($direccionArchivo);
	       $totalHojas = $documento->getSheetCount();
	       $hojaActual = $documento->getSheet(0);
	       $numFilas   = $hojaActual->getHighestDataRow();

	       if ($filaFinal > 0) {
	           $numFilas = $filaFinal;
	       }

	       $letraColumnas = $hojaActual->getHighestDataColumn();
	       $f = 3;
	       $arrayTodos = [];
	       $claves_validar = ['mat_tipo_documento', 'mat_documento', 'mat_nombres', 'mat_primer_apellido', 'mat_grado'];

	       // Mapas de tipos de documento y género para validación
	       $tiposDocumento = [
	           'RC'   => '108',
	           'CC'   => '105',
	           'CE'   => '109',
	           'TI'   => '107',
	           'PP'   => '110',
	           'PE'   => '139',
	           'NUIP' => '106',
	           'PPT'  => '139',
	       ];
	       $tiposGenero = [
	           'M' => '126',
	           'F' => '127',
	       ];
	       $estratosArray = ['', 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125];

	       // SQL base para inserción de estudiantes
	       $sql = 'INSERT INTO ' . BD_ACADEMICA . '.academico_matriculas(mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_id_usuario, mat_acudiente, mat_documento, mat_tipo_documento, mat_grupo, mat_direccion, mat_genero, mat_fecha_nacimiento, mat_barrio, mat_celular, mat_email, mat_estrato, mat_tipo_sangre, mat_eps, mat_nombre2, institucion, year, mat_forma_creacion) VALUES';

	       // Arrays para contar resultados
	       $estudiantesCreados      = [];
	       $estudiantesActualizados = [];
	       $estudiantesNoCreados    = [];
	       $acudientesCreados       = [];
	       $acudientesExistentes    = [];
	       $acudientesNoCreados     = [];
	       $contE = 1;

		      // Procesar cada fila del Excel
		      while ($f <= $numFilas) {
		          /*
		          ***************ACUDIENTE********************
		          */
		          $idAcudiente = '0000';

		          // Validamos que el documento no venga vacío
		          $documentoAcudiente = trim($hojaActual->getCell('R' . $f)->getValue());
		          if (!empty($documentoAcudiente)) {
		              $datosAcudiente = [
		                  'uss_usuario' => $documentoAcudiente,
		                  'uss_clave'   => $clavePorDefectoUsuarios,
		                  'uss_tipo'    => 3,
		                  'uss_nombre'  => trim($hojaActual->getCell('S' . $f)->getValue()),
		              ];

		              $numUsuarioAcudiente = Usuarios::validarExistenciaUsuario($datosAcudiente['uss_usuario']);

		              if ($numUsuarioAcudiente > 0) {
		                  $datosAcudienteExistente = Usuarios::obtenerDatosUsuario($datosAcudiente['uss_usuario']);
		                  $idAcudiente = $datosAcudienteExistente['uss_id'];
		                  $acudientesExistentes['FILA_' . $f] = $datosAcudienteExistente['uss_usuario'];
		              } else {
		                  if (!empty($datosAcudiente['uss_nombre'])) {
		                      try {
		                          $idAcudiente = UsuariosPadre::guardarUsuario($conexionPDO, 'uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_idioma, institucion, year, uss_id', [$datosAcudiente['uss_usuario'], $datosAcudiente['uss_clave'], $datosAcudiente['uss_tipo'], $datosAcudiente['uss_nombre'], 1, $config['conf_id_institucion'], $anio]);
		                      } catch (Exception $e) {
		                          SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, $e->getMessage());
		                      }

		                      $acudientesCreados['FILA_' . $f] = $datosAcudiente['uss_usuario'];
		                  } else {
		                      $acudientesNoCreados[] = 'FILA ' . $f;
		                  }
		              }
		          } else {
		              $acudientesNoCreados[] = 'FILA ' . $f;
		          }

			         /*
			         ***************ESTUDIANTE********************
			         */
			         $todoBien = true;
			         // Extraer y sanitizar datos del estudiante desde Excel
			         $arrayIndividual = [
			             'mat_matricula'         => (strtotime('now') + $f),
			             'mat_tipo_documento'    => trim($hojaActual->getCell('A' . $f)->getValue()),
			             'mat_documento'         => trim($hojaActual->getCell('B' . $f)->getValue()),
			             'mat_nombres'           => strtoupper(trim($hojaActual->getCell('C' . $f)->getValue())),
			             'mat_nombre2'           => strtoupper(trim($hojaActual->getCell('D' . $f)->getValue())),
			             'mat_primer_apellido'   => strtoupper(trim($hojaActual->getCell('E' . $f)->getValue())),
			             'mat_segundo_apellido'  => strtoupper(trim($hojaActual->getCell('F' . $f)->getValue())),
			             'mat_genero'            => trim($hojaActual->getCell('G' . $f)->getValue()),
			             'mat_fecha_nacimiento'  => trim($hojaActual->getCell('H' . $f)->getFormattedValue()),
			             'mat_grado'             => trim($hojaActual->getCell('I' . $f)->getValue()),
			             'mat_grupo'             => trim($hojaActual->getCell('J' . $f)->getValue()),
			             'mat_direccion'         => trim($hojaActual->getCell('K' . $f)->getValue()),
			             'mat_barrio'            => trim($hojaActual->getCell('L' . $f)->getValue()),
			             'mat_celular'           => trim($hojaActual->getCell('M' . $f)->getValue()),
			             'mat_email'             => strtolower(trim($hojaActual->getCell('N' . $f)->getValue())),
			             'mat_estrato'           => trim($hojaActual->getCell('O' . $f)->getValue()),
			             'mat_tipo_sangre'       => trim($hojaActual->getCell('P' . $f)->getValue()),
			             'mat_eps'               => trim($hojaActual->getCell('Q' . $f)->getValue()),
			             'mat_acudiente'         => $idAcudiente,
			         ];

			         // Validamos que los campos más importantes no vengan vacíos
			         foreach ($claves_validar as $clave) {
			             if (empty($arrayIndividual[$clave])) {
			                 $todoBien = false;
			                 SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, "Campo obligatorio vacío: {$clave} en fila {$f}");
			             }
			         }

			         // Validar tipo de documento
			         if (!array_key_exists($arrayIndividual['mat_tipo_documento'], $tiposDocumento)) {
			             $todoBien = false;
			             SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, "Tipo de documento inválido: {$arrayIndividual['mat_tipo_documento']} en fila {$f}");
			         }

			         // Validar género
			         if (!array_key_exists($arrayIndividual['mat_genero'], $tiposGenero)) {
			             $todoBien = false;
			             SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, "Género inválido: {$arrayIndividual['mat_genero']} en fila {$f}");
			         }

			         // Validar email si no está vacío
			         if (!empty($arrayIndividual['mat_email']) && !filter_var($arrayIndividual['mat_email'], FILTER_VALIDATE_EMAIL)) {
			             $todoBien = false;
			             SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, "Email inválido: {$arrayIndividual['mat_email']} en fila {$f}");
			         }

			         // Validar celular (10 dígitos numéricos)
			         if (!empty($arrayIndividual['mat_celular']) && (!is_numeric($arrayIndividual['mat_celular']) || strlen($arrayIndividual['mat_celular']) !== 10)) {
			             $todoBien = false;
			             SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, "Celular inválido (debe ser 10 dígitos): {$arrayIndividual['mat_celular']} en fila {$f}");
			         }

			         // Validar fecha de nacimiento (formato yyyy-mm-dd)
			         if (!empty($arrayIndividual['mat_fecha_nacimiento'])) {
			             $fechaFormateada = $arrayIndividual['mat_fecha_nacimiento'];
			             if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFormateada) || !strtotime($fechaFormateada)) {
			                 $todoBien = false;
			                 SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, "Fecha de nacimiento inválida (debe ser yyyy-mm-dd): {$fechaFormateada} en fila {$f}");
			             }
			         }

			         // Obtener tipo de documento y género mapeados
			         $tipoDocumento = $tiposDocumento[$arrayIndividual['mat_tipo_documento']] ?? '';
			         $genero = $tiposGenero[$arrayIndividual['mat_genero']] ?? '';

			         // Obtener ID del grado por nombre y validar existencia
			         $grado = '';
			         if (!empty($arrayIndividual['mat_grado'])) {
			             $datos = Grados::obtenerGradoPorNombre($config, $arrayIndividual['mat_grado'], $anio);
			             if (!empty($datos['gra_id'])) {
			                 $grado = $datos['gra_id'];
			             } else {
			                 $todoBien = false;
			                 SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, "Curso no encontrado: {$arrayIndividual['mat_grado']} en fila {$f}");
			             }
			         }

			         // Determinar grupo basado en la letra y validar
			         $grupo = 1;
			         if (!empty($arrayIndividual['mat_grupo'])) {
			             $gruposMap = ['A' => 1, 'B' => 2, 'C' => 3];
			             if (!array_key_exists($arrayIndividual['mat_grupo'], $gruposMap)) {
			                 $todoBien = false;
			                 SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, "Grupo inválido (debe ser A, B o C): {$arrayIndividual['mat_grupo']} en fila {$f}");
			             } else {
			                 $grupo = $gruposMap[$arrayIndividual['mat_grupo']];
			             }
			         }

			         // Si los campos están completos entonces ordenamos los datos del estudiante
			         if ($todoBien) {
			             $numMatricula = Estudiantes::validarExistenciaEstudiante($arrayIndividual['mat_documento']);
			             if ($numMatricula > 0) {
			                 $datosEstudianteExistente = Estudiantes::obtenerDatosEstudiante($arrayIndividual['mat_documento']);
			                 try {
			                     $update = [
			                         'mat_matricula' => 'mat_matricula',
			                     ];

			                     if (!empty($actualizarCampo)) {
			                         $camposFormulario = count($actualizarCampo);
			                         if ($camposFormulario > 0) {
			                             $cont = 0;

			                             while ($cont < $camposFormulario) {
			                                 if ($actualizarCampo[$cont] == 1) {
			                                     $update['mat_grado'] = $grado;
			                                 }

			                                 if ($actualizarCampo[$cont] == 2) {
			                                     $update['mat_grupo'] = $grupo;
			                                 }
			                                 if ($actualizarCampo[$cont] == 3) {
			                                     $update['mat_tipo_documento'] = $tipoDocumento;
			                                 }
			                                 if ($actualizarCampo[$cont] == 4) {
			                                     $update['mat_acudiente'] = $idAcudiente;
			                                 }
			                                 if ($actualizarCampo[$cont] == 5) {
			                                     $update['mat_nombre2'] = trim($hojaActual->getCell('D' . $f)->getValue());
			                                 }
			                                 if ($actualizarCampo[$cont] == 6) {
			                                     $matFechaNacimiento = trim($hojaActual->getCell('H' . $f)->getFormattedValue());
			                                     $fNacimiento = '0000-00-00';
			                                     if (!empty($matFechaNacimiento)) {
			                                         // Validar formato yyyy-mm-dd
			                                         if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $matFechaNacimiento) && strtotime($matFechaNacimiento)) {
			                                             $fNacimiento = $matFechaNacimiento;
			                                         } else {
			                                             SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, "Fecha de nacimiento inválida en actualización: {$matFechaNacimiento} en fila {$f}");
			                                             continue 2; // Saltar a la siguiente fila
			                                         }
			                                     }
			                                     $update['mat_fecha_nacimiento'] = $fNacimiento;
			                                 }
			                                 $cont++;
			                             }
			                         }
			                     }
			                     // Actualizamos el acudiente y los datos del formulario
			                     try {
			                         Estudiantes::actualizarMatriculasPorId($config, $datosEstudianteExistente['mat_id'], $update, $anio);
			                     } catch (Exception $e) {
			                         SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, $e->getMessage());
			                     }
			                     // Verificamos que el array no venga vacío y adicionalmente que tenga el campo acudiente seleccionado para actualizarse
			                     if (!empty($actualizarCampo) && in_array(4, $actualizarCampo)) {
			                         // Borramos si hay alguna asociación igual y creamos la nueva
			                         try {
			                             $stmt = mysqli_prepare($conexion, 'DELETE FROM ' . BD_GENERAL . '.usuarios_por_estudiantes WHERE upe_id_usuario = ? AND upe_id_estudiante = ? AND institucion = ? AND year = ?');
			                             mysqli_stmt_bind_param($stmt, 'siii', $idAcudiente, $datosEstudianteExistente['mat_id'], $config['conf_id_institucion'], $anio);
			                             mysqli_stmt_execute($stmt);
			                             mysqli_stmt_close($stmt);
			                         } catch (Exception $e) {
			                             SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, $e->getMessage());
			                         }
			                         $idInsercion = Utilidades::generateCode('UPE');
			                         try {
			                             $stmt = mysqli_prepare($conexion, 'INSERT INTO ' . BD_GENERAL . '.usuarios_por_estudiantes(upe_id, upe_id_usuario, upe_id_estudiante, institucion, year) VALUES (?, ?, ?, ?, ?)');
			                             mysqli_stmt_bind_param($stmt, 'ssiii', $idInsercion, $idAcudiente, $datosEstudianteExistente['mat_id'], $config['conf_id_institucion'], $anio);
			                             mysqli_stmt_execute($stmt);
			                             mysqli_stmt_close($stmt);
			                         } catch (Exception $e) {
			                             SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, $e->getMessage());
			                         }
			                     }
			                     $estudiantesActualizados['FILA_' . $f] = $datosEstudianteExistente['mat_documento'];
			                 } catch (Exception $e) {
			                     SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, $e->getMessage());
			                     // Continuar con el siguiente estudiante en lugar de salir
			                     continue;
			                 }
				            } else {
				                // Procesar fecha de nacimiento (ya validada arriba)
				                $fNacimiento = '0000-00-00';
				                if (!empty($arrayIndividual['mat_fecha_nacimiento'])) {
				                    $fNacimiento = $arrayIndividual['mat_fecha_nacimiento'];
				                }

				                // Determinar estrato
				                $estrato = 116;
				                if (!empty($arrayIndividual['mat_estrato'])) {
				                    $estrato = $estratosArray[$arrayIndividual['mat_estrato']] ?? 116;
				                }

				                // Sanitizar email
				                $email = null;
				                if (!empty($arrayIndividual['mat_email'])) {
				                    $email = strtolower(trim($arrayIndividual['mat_email']));
				                }
				                $arrayTodos[$f] = $arrayIndividual;

				                try {
				                    $responsableRegistro = 0;
				                    if (!empty($_SESSION['id'])) {
				                        $responsableRegistro = $_SESSION['id'];
				                    }
				                    $idUsuarioEstudiante = UsuariosPadre::guardarUsuario($conexionPDO, 'uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_idioma, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_intentos_fallidos, uss_tipo_documento, uss_apellido1, uss_apellido2, uss_nombre2,uss_documento, institucion, year, uss_id', [$arrayIndividual['mat_documento'], $clavePorDefectoUsuarios, 4, $arrayIndividual['mat_nombres'], 0, 1, 0, date('Y-m-d H:i:s'), $responsableRegistro, 0, $tipoDocumento, $arrayIndividual['mat_primer_apellido'], $arrayIndividual['mat_segundo_apellido'], $arrayIndividual['mat_nombre2'], $arrayIndividual['mat_documento'], $config['conf_id_institucion'], $anio]);
				                } catch (Exception $e) {
				                    SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, $e->getMessage());
				                }

				                $codigoMAT = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_matriculas') . $contE;

				                $sql .= "('" . $codigoMAT . "', '" . $arrayIndividual['mat_matricula'] . "', NOW(), '" . $arrayIndividual['mat_primer_apellido'] . "', '" . $arrayIndividual['mat_segundo_apellido'] . "', '" . $arrayIndividual['mat_nombres'] . "', '" . $grado . "', '" . $idUsuarioEstudiante . "', '" . $idAcudiente . "', '" . $arrayIndividual['mat_documento'] . "', '" . $tipoDocumento . "', '" . $grupo . "', '" . $arrayIndividual['mat_direccion'] . "', '" . $genero . "', '" . $fNacimiento . "', '" . $arrayIndividual['mat_barrio'] . "', '" . $arrayIndividual['mat_celular'] . "', '" . $email . "', '" . $estrato . "', '" . $arrayIndividual['mat_tipo_sangre'] . "', '" . $arrayIndividual['mat_eps'] . "', '" . $arrayIndividual['mat_nombre2'] . "', {$config['conf_id_institucion']}, {$anio}, '" . Estudiantes::IMPORTAR_EXCEL_JOB . "'),";

				                $contE++;

				                // Borramos si hay alguna asociación igual y creamos la nueva
				                try {
				                    $stmt = mysqli_prepare($conexion, 'DELETE FROM ' . BD_GENERAL . '.usuarios_por_estudiantes WHERE upe_id_usuario = ? AND upe_id_estudiante = ? AND institucion = ? AND year = ?');
				                    mysqli_stmt_bind_param($stmt, 'siii', $idAcudiente, $codigoMAT, $config['conf_id_institucion'], $_SESSION['bd']);
				                    mysqli_stmt_execute($stmt);
				                    mysqli_stmt_close($stmt);
				                } catch (Exception $e) {
				                    include('../compartido/error-catch-to-report.php');
				                }

				                $idInsercion = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'usuarios_por_estudiantes');
				                try {
				                    $stmt = mysqli_prepare($conexion, 'INSERT INTO ' . BD_GENERAL . '.usuarios_por_estudiantes(upe_id, upe_id_usuario, upe_id_estudiante, institucion, year) VALUES (?, ?, ?, ?, ?)');
				                    mysqli_stmt_bind_param($stmt, 'ssiii', $idInsercion, $idAcudiente, $codigoMAT, $config['conf_id_institucion'], $_SESSION['bd']);
				                    mysqli_stmt_execute($stmt);
				                    mysqli_stmt_close($stmt);
				                } catch (Exception $e) {
				                    include('../compartido/error-catch-to-report.php');
				                }

				                $estudiantesCreados['FILA_' . $f] = $arrayIndividual['mat_documento'];
				            }
				        } else {
				            $estudiantesNoCreados[] = 'FILA ' . $f;
				        }
				        $f++;
				    }
		      // Contar resultados del procesamiento
		      $numeroEstudiantesCreados = count($estudiantesCreados);
		      $numeroEstudiantesActualizados = count($estudiantesActualizados);
		      $numeroEstudiantesNoCreados = count($estudiantesNoCreados);
		      $numeroAcudientesCreados = count($acudientesCreados);
		      $numeroAcudientesExistentes = count($acudientesExistentes);
		      $numeroAcudientesNoCreados = count($acudientesNoCreados);

		      $respuesta = '<br>Resumen del proceso:<br>
		      -Total filas leídas: ' . $numFilas . '<br><br>
		              - Estudiantes creados nuevos: ' . $numeroEstudiantesCreados . '<br>
		              - Estudiantes que ya estaban creados y se les actualizó alguna información seleccionada: ' . $numeroEstudiantesActualizados . '<br>
		              - Estudiantes que les faltó algún campo obligatorio: ' . $numeroEstudiantesNoCreados . '<br><br>
		              - Acudientes creados nuevos: ' . $numeroAcudientesCreados . '<br>
		              - Acudientes que ya estaban creados y no hubo necesidad de volverlos a crear: ' . $numeroAcudientesExistentes . '<br>
		              - Acudientes que les faltó el documento o el nombre: ' . $numeroAcudientesNoCreados . '<br><br>';

		      if (!empty($estudiantesCreados) && count($estudiantesCreados) > 0) {
		          $sql = substr($sql, 0, -1);
		          try {
		              mysqli_query($conexion, $sql);
		          } catch (Exception $e) {
		              print_r($sql);
		              SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, $e->getMessage());
		              // Continuar sin salir, pero marcar como error
		              $finalizado = false;
		          }
		      }
		      if (file_exists($nombreArchivo)) {
		          unlink($nombreArchivo);
		      }
		      $finalizado = true;

		      // Fecha final del proceso
		      $fechaFinal = new DateTime();
		      $tiempoTranscurrido = minutosTranscurridos($fechaInicio, $fechaFinal);
		      $mensaje = $tiempoTranscurrido . '!' . $respuesta;
		      $estadoFinal = $finalizado ? JOBS_ESTADO_FINALIZADO : JOBS_ESTADO_ERROR;
		      $datos = [
		          'id'      => $resultadoJobs['job_id'],
		          'mensaje' => $mensaje,
		          'estado'  => $estadoFinal,
		      ];
		      SysJobs::actualizar($datos);
		      SysJobs::enviarMensaje($resultadoJobs['job_responsable'], $mensaje, $resultadoJobs['job_id'], JOBS_TIPO_IMPORTAR_ESTUDIANTES_EXCEL, $estadoFinal);
		  } catch (Exception $e) {
		      SysJobs::actualizarMensaje($resultadoJobs['job_id'], $intento, $e->getMessage());
		  }
	
	

}

exit();

/**
 * Calcula el tiempo transcurrido entre dos fechas.
 *
 * @param DateTime $fecha_i Fecha de inicio.
 * @param DateTime $fecha_f Fecha de fin.
 * @return string Tiempo transcurrido en minutos y segundos.
 */
function minutosTranscurridos($fecha_i, $fecha_f)
{
    $intervalo = $fecha_i->diff($fecha_f);
    $minutos = $intervalo->i;
    $segundos = $intervalo->s;
    return ' Finalizó en: ' . $minutos . ' Min y ' . $segundos . ' Seg.';
}