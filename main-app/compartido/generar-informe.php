<?php
session_start();

$acceso = explode("/", $_SERVER['HTTP_REFERER']);

if (count($acceso) > 5) {
    $carpeta_actual = $acceso[5] ;
}

if (empty($_GET["grado"]) || empty($_GET["grupo"]) || empty($_GET["carga"]) || empty($_GET["periodo"]) || empty($_GET["tipoGrado"])) {
?>
	<script language="javascript">
		window.location.href="../docente/page-info.php?idmsg=112";
	</script>
<?php
	exit();
}

$idPaginaInterna = 'CM0006';
include($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/config.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_academico_cargas.php");

$conexionPDO = Conexion::newConnection('PDO');

$grado     = base64_decode($_GET["grado"]);
$grupo     = base64_decode($_GET["grupo"]);
$carga     = base64_decode($_GET["carga"]);
$periodo   = base64_decode($_GET["periodo"]);
$tipoGrado = base64_decode($_GET["tipoGrado"]);

if ($config['conf_porcentaje_completo_generar_informe'] == Boletin::GENERAR_CON_PORCENTAJE_COMPLETO) {
	$consultaListaEstudantesError = Estudiantes::listarEstudiantesNotasFaltantes($carga, $periodo, $tipoGrado);

	//Verificamos que el estudiante no tenga notas faltantes para generar el informe
	if (mysqli_num_rows($consultaListaEstudantesError) > 0) {
?>
		<script language="javascript">
			window.location.href="../docente/page-info.php?idmsg=108&carga=<?=$_GET["carga"];?>&periodo=<?=$_GET["periodo"];?>";
		</script>
<?php		
		exit();
	}
}

$datosCarga = [
	'car_curso' => $grado,
	'car_grupo' => $grupo,
	'gra_tipo'  => $tipoGrado
];

//Consultamos los estudiantes del grado y grupo
$consulta = Estudiantes::listarEstudiantesConInfoBasica($datosCarga);

$contBol = 1;
while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
	//DEFINITIVAS
	$carga      = $carga;
	$periodo    = $periodo;
	$estudiante = $resultado['mat_id'];
	include("../definitivas.php");

	//Consultamos si tiene registros en el boletín
	$boletinDatos = Boletin::traerNotaBoletinCargaPeriodo($config, $periodo, $estudiante, $carga);

	//Validar la configuración que omite a los estudiantes que NO tienen el 100% de calificaciones
	//Verificamos si el procentaje actual es menor al mínimo permitido y que el estudiante NO tenga nota registrada previamente
	if (
		$config['conf_porcentaje_completo_generar_informe'] == Boletin::OMITIR_ESTUDIANTES_CON_PORCENTAJE_INCOMPLETO && 
		$porcentajeActual < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME && 
		empty($boletinDatos['bol_nota'])
	) {
		continue;
	}

	$caso = 1; //Inserta la definitiva que viene normal 

	if (!empty($boletinDatos['bol_id'])) {
		//Si ya existe un registro previo de definitiva TIPO 1
		if ($boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_NORMAL) {

			if($boletinDatos['bol_nota'] != $definitiva || $boletinDatos['bol_porcentaje'] != $porcentajeActual) {
				$caso = 2; //Se cambia la definitiva que tenía por la que viene. Sea menor o mayor.
			} else {
				//No se hacen cambios. Todo sigue igual
				continue;
			}

		} elseif ($boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_RECUPERACION_PERIODO) { //Si ya existe un registro previo de recuperación de periodo TIPO 2

			//Si la definitiva que viene está perdida 
			if ($definitiva < $config[5]) {
				//No se hacen cambios. Todo sigue igual
				continue;
			} else {
				$caso = 4; //Se reemplaza la nota de recuperación actual por la definitiva que viene. Igual está ganada y no requiere de recuperación.
			}

		} elseif (($boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_RECUPERACION_INDICADOR || $boletinDatos['bol_tipo'] == Boletin::BOLETIN_TIPO_NOTA_DIRECTIVA)) { //Si ya existe un registro previo de recuperación por Indicadores TIPO 3
			$caso = 5; //Se actualiza la definitiva que viene y se cambia la recuperación del Indicador a nota anterior. 
		}
	}

	if ($informacion_inst["info_institucion"] == ICOLVEN) {
		//Vamos a obtener las definitivas por cada indicador y la definitiva general de la asignatura
		$notasPorIndicador = Calificaciones::traerNotasPorIndicador($config, $carga, $resultado['mat_id'], $periodo);
		$sumaNotaIndicador = 0; 

		while ($notInd = mysqli_fetch_array($notasPorIndicador, MYSQLI_BOTH)) {
			$consultaNum = Indicadores::consultaRecuperacionIndicadorPeriodo($config, $notInd[1], $resultado['mat_id'], $carga, $periodo);
			$num         = mysqli_num_rows($consultaNum);

			$sumaNotaIndicador += $notInd[0];

			if ($num == 0) {
				Indicadores::eliminarRecuperacionIndicadorPeriodo($config, $notInd[1], $resultado['mat_id'], $carga, $periodo);				

				Indicadores::guardarRecuperacionIndicador($conexionPDO, $config, $resultado['mat_id'], $carga, $notInd[0], $notInd[1], $periodo, $notInd[2]);
			} else {
				Indicadores::actualizarRecuperacionIndicador($config, $resultado['mat_id'], $carga, $notInd[0], $notInd[1], $periodo, $notInd[2]);
			}
		} 

		$sumaNotaIndicador = round($sumaNotaIndicador, 1);
	} else {
		$sumaNotaIndicador = round($definitiva, 1);
	}

	if ($caso == 2 || $caso == 4 || $caso == 5) {

		if (!empty($boletinDatos['bol_historial_actualizacion']) && $boletinDatos['bol_historial_actualizacion'] != NULL) {
			$actualizacion = json_decode($boletinDatos['bol_historial_actualizacion'], true);
		} else {
			$actualizacion = [];
		}

		$fecha = $boletinDatos['bol_fecha_registro'];

		if (!empty($boletinDatos['bol_ultima_actualizacion']) && $boletinDatos['bol_ultima_actualizacion'] !=NULL ) {
			$fecha = $boletinDatos['bol_ultima_actualizacion'];
		}

		$nuevoArray = 
		[
			"nota_anterior" 		 => $boletinDatos['bol_nota'],
			"fecha_de_actualizacion" => $fecha,
			"porcentaje" 	         => $boletinDatos['bol_porcentaje'],
			"caso"					 => $caso
		];

		$numActualizacion                 = $boletinDatos['bol_actualizaciones'] + 1;
		$actualizacion[$numActualizacion] = $nuevoArray;

		$update = [
			'bol_nota_anterior'           => $boletinDatos['bol_nota'],
			'bol_nota'                    => $definitiva,
			'bol_nota_indicadores'        => $sumaNotaIndicador,
			'bol_tipo'                    => 1,
			'bol_observaciones'           => 'Reemplazada',
			'bol_porcentaje'              => $porcentajeActual,
			'bol_historial_actualizacion' => json_encode($actualizacion),
			'bol_estado'                  => Boletin::ESTADO_GENERADO,
		];

		Boletin::actualizarNotaBoletin($config, $boletinDatos['bol_id'], $update);

	} elseif ($caso == 1) {
		//Eliminamos por si acaso hay algún registro
		if (!empty($boletinDatos['bol_id'])) {
			Boletin::eliminarNotaBoletinID($config, $boletinDatos['bol_id']);
		}

		//INSERTAR LOS DATOS EN LA TABLA BOLETIN - Para el campo bol_id el código se genera automáticamente en el método guardarNotaBoletin
		Boletin::guardarNotaBoletin(
			$conexionPDO, 
			"
				bol_carga, 
				bol_estudiante, 
				bol_periodo, 
				bol_nota, 
				bol_tipo, 
				bol_fecha_registro, 
				bol_actualizaciones, 
				bol_nota_indicadores, 
				bol_porcentaje, 
				institucion, 
				year, 
				bol_estado, 
				bol_id
			", 
			[
				$carga, 
				$estudiante, 
				$periodo, 
				$definitiva, 
				1, 
				date("Y-m-d H:i:s"), 
				0, 
				$sumaNotaIndicador, 
				$porcentajeActual, 
				$config['conf_id_institucion'], 
				$_SESSION["bd"], 
				Boletin::ESTADO_GENERADO
			]
		);

		$contBol++;
	}
}

$periodoSiguiente = $periodo + 1;
$carHistoricoArray = [];

try {
    $predicado = [
        'car_id'      => $carga,
        'institucion' => $config['conf_id_institucion'],
        'year'        => $_SESSION["bd"],
    ];

    $campos = "car_periodo, car_estado, car_historico";

    $datosCargaActualConsulta = BDT_AcademicoCargas::select($predicado, $campos, BD_ACADEMICA);
    $datosCargaActual         = $datosCargaActualConsulta->fetchAll();
    $carHistoricoCampo        = $datosCargaActual[0]['car_historico'];

    if (!empty($carHistoricoCampo)) {

        $carHistoricoArray     = json_decode($carHistoricoCampo, true);
        $keys                  = array_keys($carHistoricoArray);
        $lastKey               = end($keys);
        $lastCarHistoricoArray = $carHistoricoArray[$lastKey];

        if (
            $datosCargaActual[0]['car_estado'] == BDT_AcademicoCargas::ESTADO_DIRECTIVO && 
            !empty($lastCarHistoricoArray['car_periodo_anterior']) && 
            $lastCarHistoricoArray['car_periodo_anterior'] != $datosCargaActual[0]['car_periodo']
        ) {
            $periodoSiguiente = $lastCarHistoricoArray['car_periodo_anterior'];
        }

    }

    $carHistoricoArray[$carga.':'.time()] = [
        'car_periodo_anterior' => $datosCargaActual[0]['car_periodo'],
        'car_estado_anterior'  => $datosCargaActual[0]['car_estado'],
        'car_forma_generacion' => BDT_AcademicoCargas::GENERACION_MANUAL,
    ];
} catch (PDOException $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$update = [
    'car_estado'    => BDT_AcademicoCargas::ESTADO_SINTIA,
    'car_periodo'   => $periodoSiguiente,
    'car_historico' => json_encode($carHistoricoArray),
];

CargaAcademica::actualizarCargaPorID($config, $carga, $update);

include("../compartido/guardar-historial-acciones.php");
?>
<script language="javascript">
	window.location.href="../<?=$carpeta_actual?>/page-info.php?idmsg=109&curso=<?=$_GET["grado"];?>&grupo=<?=$_GET["grupo"];?>&periodo=<?=$_GET["periodo"];?>";
</script>
<?php
exit();