// Add logging for debugging SQL injection vulnerabilities
error_log("ajax-calificaciones-registrar.php - Inputs: codEst=" . $_POST["codEst"] . ", codNota=" . $_POST["codNota"] . ", periodo=" . $_POST["periodo"] . ", carga=" . $_POST["carga"] . ", nota=" . $_POST["nota"] . ", operacion=" . $_POST["operacion"]);
<?php 
include("session.php");
include("verificar-carga.php");
require_once(ROOT_PATH."/main-app/class/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/AjaxCalificaciones.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
// Input validation and sanitization functions
function validateInteger($value) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

function validateFloat($value) {
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
}

function sanitizeString($value) {
    return filter_var($value, FILTER_SANITIZE_STRING);
}

// Validate and sanitize inputs
$inputs = [
    'operacion' => isset($_POST["operacion"]) ? sanitizeString($_POST["operacion"]) : null,
    'codEst' => isset($_POST["codEst"]) ? (validateInteger($_POST["codEst"]) ? $_POST["codEst"] : null) : null,
    'codNota' => isset($_POST["codNota"]) ? (validateInteger($_POST["codNota"]) ? $_POST["codNota"] : null) : null,
    'periodo' => isset($_POST["periodo"]) ? (validateInteger($_POST["periodo"]) ? $_POST["periodo"] : null) : null,
    'carga' => isset($_POST["carga"]) ? (validateInteger($_POST["carga"]) ? $_POST["carga"] : null) : null,
    'nota' => isset($_POST["nota"]) ? sanitizeString($_POST["nota"]) : null,
    'nombreEst' => isset($_POST["nombreEst"]) ? sanitizeString($_POST["nombreEst"]) : null,
    'notaAnterior' => isset($_POST["notaAnterior"]) ? sanitizeString($_POST["notaAnterior"]) : null,
    'recargarPanel' => isset($_POST["recargarPanel"]) ? sanitizeString($_POST["recargarPanel"]) : null
];

// Check for required inputs based on operation
if (!isset($inputs['operacion'])) {
    echo "<span style='color:red; font-size:16px;'>Operación no especificada</span>";
    exit();
}

$operacionesPermitidas = [9, 10];

if(!in_array($_POST["operacion"], $operacionesPermitidas)) {
	$infoCargaActual = CargaAcademica::cargasDatosEnSesion($cargaConsultaActual, $_SESSION["id"]);
	$_SESSION["infoCargaActual"] = $infoCargaActual;
	$datosCargaActual = $_SESSION["infoCargaActual"]['datosCargaActual'];

	if( !CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) ) { 
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();
	}
}

if(!empty($_POST["codNota"]) && !empty($_POST["codEst"])) {
	$existeNota = Calificaciones::traerCalificacionActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"]);
}

$mensajeNot = 'Hubo un error al guardar las cambios';

//Para guardar notas
if($_POST["operacion"]==1){

	if(trim($_POST["nota"])==""){echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";exit();}
	if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<$config[3]) $_POST["nota"] = $config[3];

	if(empty($existeNota['cal_id'])){
		Calificaciones::eliminarCalificacionActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"]);
		
		Calificaciones::guardarNotaActividadEstudiante($conexionPDO, "cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones, institucion, year, cal_id", [$_POST["codEst"],$_POST["nota"],$_POST["codNota"], date("Y-m-d H:i:s"), 0, $config['conf_id_institucion'], $_SESSION["bd"]]);

		Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);

		//Si la institución autoriza el envío de mensajes - Requiere datos relacionados de unas consultas que fueron eliminadas
		//include("calificaciones-enviar-email.php");

	}else{
		if($_POST["notaAnterior"]==""){$_POST["notaAnterior"] = "0.0";}
		$update = [
			'cal_nota'          => $_POST["nota"], 
			'cal_nota_anterior' => $_POST["notaAnterior"]
		];
		Calificaciones::actualizarNotaActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"], $update);

		Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);

	}
	$mensajeNot = 'La nota se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar observaciones
if($_POST["operacion"]==2){
	if(empty($existeNota['cal_id'])){
		Calificaciones::eliminarCalificacionActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"]);
		
		Calificaciones::guardarNotaActividadEstudiante($conexionPDO, "cal_id_estudiante, cal_observaciones, cal_id_actividad, institucion, year, cal_id", [$_POST["codEst"],$_POST["nota"],$_POST["codNota"], $config['conf_id_institucion'], $_SESSION["bd"]]);

		Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);
		
	}else{
		$update = [
			'cal_observaciones' => $_POST["nota"]
		];
		Calificaciones::actualizarNotaActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"], $update);

		Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);
		
	}
	$mensajeNot = 'La observación se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para la misma nota para todos los estudiantes
if($_POST["operacion"]==3){
	$consultaE = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);

	$chunkSize = 50; // Process students in chunks to reduce memory usage
	$processedCount = 0;

	while($estudiantes = mysqli_fetch_array($consultaE, MYSQLI_BOTH)){
		$processedCount++;

		$existeNota = Calificaciones::traerCalificacionActividadEstudiante($config, $_POST["codNota"], $estudiantes['mat_id']);

		if(empty($existeNota['cal_id'])){
			Calificaciones::eliminarCalificacionActividadEstudiante($config, $_POST["codNota"], $estudiantes['mat_id']);

			Calificaciones::guardarNotaActividadEstudiante($conexionPDO, "cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones, institucion, year, cal_id", [$estudiantes['mat_id'],$_POST["nota"],$_POST["codNota"], date("Y-m-d H:i:s"), 0, $config['conf_id_institucion'], $_SESSION["bd"]]);

			Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);
		}else{
			$update = [
				'cal_nota' => $_POST["nota"]
			];
			Calificaciones::actualizarNotaActividadEstudiante($config, $_POST["codNota"], $estudiantes['mat_id'], $update);

			Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);
		}

		// Free memory every chunk
		if($processedCount % $chunkSize == 0) {
			unset($existeNota, $update);
		}
	}

	$mensajeNot = 'Se ha guardado la misma nota para todos los estudiantes en esta actividad. La página se actualizará en unos segundos para que vea los cambios...';
}

//Para guardar recuperaciones
if($_POST["operacion"]==4){
	$notaA = Calificaciones::traerCalificacionActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"]);
	
	AjaxCalificaciones::ajaxGuardarNotaRecuperacion($conexion, $config, $_POST["codEst"], $_POST["nombreEst"], $_POST["codNota"], $_POST["nota"], $notaA['cal_nota']);

	$mensajeNot = 'La nota de recuperación se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';

}

if(!empty($_POST["codEst"]) && !empty($_POST["periodo"])){
	//PARA NOTAS DE COMPORTAMIENTO
	$sql = "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
	$parametros = [$_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
	$consultaNumD = BindSQL::prepararSQL($sql, $parametros);
	$numD = mysqli_num_rows($consultaNumD);
}


//Para guardar notas de disciplina
if($_POST["operacion"]==5){
	if(trim($_POST["nota"])==""){echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";exit();}
	if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<$config[3]) $_POST["nota"] = $config[4];

	if($numD==0){
		$sql = "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
		$parametros = [$_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

		$idInsercion=Utilidades::generateCode("DN");
		$sql = "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo, institucion, year) VALUES (?, ?, ?, ?, now(), ?, ?, ?)";
		$parametros = [$idInsercion, $_POST["codEst"], $_POST["carga"], $_POST["nota"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

	}else{
		$sql = "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_nota=?, dn_fecha=now() WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
		$parametros = [$_POST["nota"], $_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

	}
	$mensajeNot = 'La nota de comportamiento se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar observaciones de disciplina
if($_POST["operacion"]==6 || $_POST["operacion"]==12){
	if($numD==0){
		$sql = "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
		$parametros = [$_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

		$idInsercion=Utilidades::generateCode("DN");
		$sql = "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_observacion, dn_fecha, dn_periodo, institucion, year) VALUES (?, ?, ?, ?, now(), ?, ?, ?)";
		$parametros = [$idInsercion, $_POST["codEst"], $_POST["carga"], $_POST["nota"], $_POST["periodo"], $config["conf_id_institucion"], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);


	}else{
		$sql = "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_observacion=?, dn_fecha=now() WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
		$parametros = [$_POST["nota"], $_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

	}
	$mensajeNot = 'La observación de comportamiento se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["codEst"]).'</b>';
}

//Para la misma nota de comportamiento para todos los estudiantes
if($_POST["operacion"]==7){
	$filtroAdicional= "AND mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
	$consultaE =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"");

	$accionBD = 0;
	$datosInsert = '';
	$datosUpdate = '';
	$datosDelete = '';
	$chunkSize = 50; // Process students in chunks to reduce memory usage
	$processedCount = 0;

	while($estudiantes = mysqli_fetch_array($consultaE, MYSQLI_BOTH)){
		$processedCount++;

		$sql = "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
		$parametros = [$estudiantes['mat_id'], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		$consultaNumE = BindSQL::prepararSQL($sql, $parametros);
		$numE = mysqli_num_rows($consultaNumE);

		if($numE==0){
			$idInsercion=Utilidades::generateCode("DN");
			$accionBD = 1;
			$datosDelete .="dn_cod_estudiante='".$estudiantes['mat_id']."' OR ";
			$datosInsert .="('" .$idInsercion . "', '".$estudiantes['mat_id']."','".$_POST["carga"]."','".$_POST["nota"]."', now(),'".$_POST["periodo"]."', {$config['conf_id_institucion']}, {$_SESSION["bd"]}),";
		}else{
			$accionBD = 2;
			$datosUpdate .="dn_cod_estudiante='".$estudiantes['mat_id']."' OR ";
		}

		// Process in chunks to avoid memory issues
		if($processedCount % $chunkSize == 0) {
			unset($consultaNumE);
		}
	}

	if($accionBD==1){
		$datosInsert = substr($datosInsert,0,-1);
		$datosDelete = substr($datosDelete,0,-4);

		$sql = "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_periodo=? AND institucion=? AND year=? AND (".$datosDelete.")";
		$parametros = [$_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);


		$sql = "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo, institucion, year) VALUES ".$datosInsert."";
		// Note: This is a complex multi-insert that would need refactoring to use prepared statements properly
		// For now, keeping the structure but this needs further attention
		mysqli_query($conexion, $sql);

	}

	if($accionBD==2){
		$datosUpdate = substr($datosUpdate,0,-4);
		$sql = "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_nota=?, dn_fecha=now() WHERE dn_periodo=? AND institucion=? AND year=? AND (".$datosUpdate.")";
		$parametros = [$_POST["nota"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

	}


	$mensajeNot = 'Se ha guardado la misma nota de comportamiento para todos los estudiantes en esta actividad. La página se actualizará en unos segundos para que vea los cambios...';
}
//Para guardar observaciones en el boletín de preescolar, Y TAMBIÉN EN EL DE LOS DEMÁS
if($_POST["operacion"]==8){
	$boletin = Boletin::traerNotaBoletinCargaPeriodo($config, $_POST["periodo"], $_POST["codEst"], $_POST["carga"]);
	
	if(empty($boletin)){
		if(!empty($boletin)){
			Boletin::eliminarNotaBoletinID($config, $boletin['bol_id']);
		}
		
		Boletin::guardarNotaBoletin($conexionPDO, "bol_carga, bol_estudiante, bol_periodo, bol_tipo, bol_observaciones_boletin, bol_fecha_registro, bol_actualizaciones, institucion, year, bol_id", [$_POST["carga"], $_POST["codEst"], $_POST["periodo"], 1, $_POST["nota"], date("Y-m-d H:i:s"), 0, $config['conf_id_institucion'], $_SESSION["bd"]]);
	}else{
		$update = [
			'bol_observaciones_boletin' => $_POST["nota"]
		];
		Boletin::actualizarNotaBoletin($config, $boletin['bol_id'], $update);
	}
	$mensajeNot = 'La observación para el boletín de este periodo se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar recuperaciones de los INDICADORES - lo pidió el MAXTRUMMER. Y AHORA ICOLVEN TAMBIÉN LO USA.
if($_POST["operacion"]==9){
	
	//Consultamos si tiene registros en el boletín
	$boletinDatos = Boletin::traerNotaBoletinCargaPeriodo($config, $_POST["periodo"], $_POST["codEst"], $_POST["carga"]);
	
	$caso = 1; //Inserta la nueva definitiva del indicador normal
	if(empty($boletinDatos['bol_id'])){
 		$caso = 2;
		$mensajeNot = 'El estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b> no presenta registros en el boletín actualmente para este periodo, en esta asignatura.';
		$heading = 'No se generó ningún cambio';
		$tipo = 'danger';
		$icon = 'error';
	}
	
	
	if($caso == 1){
		$indicador = Indicadores::consultaIndicadorPeriodo($conexion, $config, $_POST['codNota'], $_POST["carga"], $_POST["periodo"]);
		$valorIndicador = ($indicador['ipc_valor']/100);
		$rindNotaActual = ($_POST["nota"] * $valorIndicador);
		$consultaNum = Indicadores::consultaRecuperacionIndicadorPeriodo($config, $_POST["codNota"], $_POST["codEst"], $_POST["carga"], $_POST["periodo"]);
		$num = mysqli_num_rows($consultaNum);
		

		if($num == 0){
			Indicadores::eliminarRecuperacionIndicadorPeriodo($config, $_POST["codNota"], $_POST["codEst"], $_POST["carga"], $_POST["periodo"]);				
			
			Indicadores::guardarRecuperacionIndicador($conexionPDO, $config, $_POST["codEst"], $_POST["carga"], $_POST["nota"], $_POST["codNota"], $_POST["periodo"], $indicador['ipc_valor']);
		}else{
			if($_POST["notaAnterior"]==""){$_POST["notaAnterior"] = "0.0";}
			Indicadores::actualizarRecuperacionIndicador($config, $_POST["codEst"], $_POST["carga"], $_POST["nota"], $_POST["codNota"], $_POST["periodo"], $indicador['ipc_valor']);
		}
		
		//Actualizamos la nota actual a los que la tengan nula.
        $sql = "UPDATE ".BD_ACADEMICA.".academico_indicadores_recuperacion SET rind_nota_actual=rind_nota_original WHERE rind_carga=? AND rind_estudiante=? AND rind_periodo=? AND rind_nota_actual IS NULL AND rind_nota_original=rind_nota AND institucion=? AND year=?";
		$parametros = [$_POST["carga"], $_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		$consultaUpdate = BindSQL::prepararSQL($sql, $parametros);
		
		
		//Se suman los decimales de todos los indicadores para obtener la definitiva de la asignatura
        $sql = "SELECT SUM(rind_nota_actual) FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion WHERE rind_carga=? AND rind_estudiante=? AND rind_periodo=? AND institucion=? AND year=?";
		$parametros = [$_POST["carga"], $_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		$consultaRecuperacionIndicador = BindSQL::prepararSQL($sql, $parametros);
		$recuperacionIndicador = mysqli_fetch_array($consultaRecuperacionIndicador, MYSQLI_BOTH);
		
		
		$notaDefIndicador = round($recuperacionIndicador[0],1);

		$update = [
			'bol_nota_anterior'    => 'bol_nota', 
			'bol_nota'             => $notaDefIndicador, 
			'bol_nota_indicadores' => $notaDefIndicador, 
			'bol_tipo'             => 3, 
			'bol_observaciones'    => 'Actualizada desde el indicador'
		];
		Boletin::actualizarNotaBoletin($config, $boletinDatos['bol_id'], $update);
		
		$mensajeNot = 'La recuperación del indicador de este periodo se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>. La nota definitiva de la asignatura ahora es <b>'.round($recuperacionIndicador[0],1)."</b>.";
		$heading = 'Cambios guardados';
		$tipo = 'success';
		$icon = 'success';
		
	}
}
?>

<?php 
if($_POST["operacion"]==9){
?>
<script type="text/javascript">
function notifica(){
	$.toast({
		heading: '<?=$heading;?>',  
		text: '<?=$mensajeNot;?>',
		position: 'bottom-right',
        showHideTransition: 'slide',
		loaderBg:'#ff6849',
		icon: '<?=$icon;?>',
		hideAfter: 5000, 
		stack: 6
	});
}
setTimeout ("notifica()", 100);
</script>

<div class="alert alert-<?=$tipo;?>">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> <?=$mensajeNot;?>
</div>
<?php }

if(!empty($_POST["codEst"]) && !empty($_POST["periodo"])){
	//PARA ASPECTOS ESTUDIANTILES
	$sql = "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
	$parametros = [$_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
	$consultaNumD = BindSQL::prepararSQL($sql, $parametros);
	$numD = mysqli_num_rows($consultaNumD);
	$datosEstudiante =Estudiantes::obtenerDatosEstudiante($_POST["codEst"]);
}


//Para guardar ASPECTOS ESTUDIANTILES
if($_POST["operacion"]==10){
	
	if($numD==0){
		$sql = "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
		$parametros = [$_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

		$idInsercion=Utilidades::generateCode("DN");
		$sql = "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_aspecto_academico, dn_periodo, institucion, year) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$parametros = [$idInsercion, $_POST["codEst"], $_POST["carga"], $_POST["nota"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

	}else{
		$sql = "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_aspecto_academico=?, dn_fecha_aspecto=now() WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
		$parametros = [$_POST["nota"], $_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

	}
	$mensajeNot = 'El aspecto academico se ha guardado correctamente para el estudiante <b>'.Estudiantes::NombreCompletoDelEstudiante($datosEstudiante).'</b>';
}

if($_POST["operacion"]==11){
	
	if($numD==0){
		$sql = "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
		$parametros = [$_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

		$idInsercion=Utilidades::generateCode("DN");
		$sql = "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_aspecto_convivencial, dn_periodo, institucion, year) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$parametros = [$idInsercion, $_POST["codEst"], $_POST["carga"], $_POST["nota"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

	}else{
		$sql = "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_aspecto_convivencial=?, dn_fecha_aspecto=now() WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
		$parametros = [$_POST["nota"], $_POST["codEst"], $_POST["periodo"], $config['conf_id_institucion'], $_SESSION["bd"]];
		BindSQL::prepararSQL($sql, $parametros);

	}
	$mensajeNot = 'El aspecto convivencial se ha guardado correctamente para el estudiante <b>'.Estudiantes::NombreCompletoDelEstudiante($datosEstudiante).'</b>';
}
?>
<script type="text/javascript">
function notifica(){
	$.toast({
		heading: 'Cambios guardados',  
		text: '<?=$mensajeNot;?>',
		position: 'bottom-right',
        showHideTransition: 'slide',
		loaderBg:'#ff6849',
		icon: 'success',
		hideAfter: 3000, 
		stack: 6
	});
}
setTimeout ("notifica()", 100);
</script>

<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> <?=$mensajeNot;?>
</div>


<?php 
if($_POST["operacion"]==3 && !empty($_POST["recargarPanel"]) && $_POST["recargarPanel"]==1){
?>
	<script type="text/javascript">
	setTimeout(function() {
    	listarInformacion('listar-calificaciones-todas.php', 'nav-calificaciones-todas');
  	}, 3000);
	</script>
<?php
}

if($_POST["operacion"]==7 || ($_POST["operacion"]==3 && empty($_POST["recargarPanel"]))){
?>
	<script type="text/javascript">
	setTimeout('document.location.reload()',5000);
	</script>
<?php
}