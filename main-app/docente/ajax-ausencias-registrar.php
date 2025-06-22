<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");
require_once(ROOT_PATH."/main-app/class/App/Academico/Clase.php");
require_once(ROOT_PATH."/main-app/class/App/Academico/Carga.php");
require_once(ROOT_PATH."/main-app/class/App/Academico/Materia.php");
require_once(ROOT_PATH.'/main-app/class/EnviarEmail.php');

$rC = Ausencias::traerAusenciasClaseEstudiante($config, $_POST["codNota"], $_POST["codEst"]);
if (empty($rC)) {

	if (!empty($rC['aus_id'])) {
		Ausencias::eliminarAusenciasID($config, $rC['aus_id']);
	}

	Ausencias::guardarAusencia($conexionPDO, "aus_id_estudiante, aus_ausencias, aus_id_clase, institucion, year, aus_id", [$_POST["codEst"],$_POST["nota"],$_POST["codNota"], $config['conf_id_institucion'], $_SESSION["bd"]]);
	
	Clases::registrarAusenciaClase($conexion, $config, $_POST);

	if ($_POST["nota"] > 0 && ($config['conf_id_institucion'] == ICOLVEN || $config['conf_id_institucion'] == DEVELOPER)) {

		$predicadoClase= [
			'cls_id'      => $_POST["codNota"],
			'institucion' => $config['conf_id_institucion'],
			'year'        => $_SESSION["bd"]
		];

		Carga::foreignKey(Carga::INNER, [
			'car_id'      => 'cls_id_carga',
			'institucion' => Clase::$tableAs.'.institucion',
			'year'        => Clase::$tableAs.'.year'
		]);

		Materia::foreignKey(Materia::INNER, [
			'mat_id'      => 'car_materia',
			'institucion' => Carga::$tableAs.'.institucion',
			'year'        => Carga::$tableAs.'.year'
		]);

		$datosActividad = Clase::SelectJoin($predicadoClase, '*', [Carga::class, Materia::class]);

		$datosEstudiante = Estudiantes::obtenerDatosEstudiante($_POST["codEst"]);
		$nombre = trim(Estudiantes::NombreCompletoDelEstudiante($datosEstudiante));

		$consultaDatosAcudiente = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$datosEstudiante['mat_acudiente']."'");
		$datosAcudiente = mysqli_fetch_array($consultaDatosAcudiente, MYSQLI_BOTH);

		//INICIO ENVÍO DE MENSAJE
		$tituloMsj    = 'Inasistencia para ' .$nombre;
		$contenidoMsj = '
			<p style="color:navy;">
				Hola ' . strtoupper($datosAcudiente['uss_nombre']) . ', a tu acudido ' . $nombre . ' le han colocado inasistencia en la asignatura de ' .$datosActividad[0]["mat_nombre"]. '.<br>
				Por favor ingresa a la plataforma y verifica. 
			</p>
		';

		$data = [
			'contenido_msj'  => $contenidoMsj,
			'usuario_email'  => $datosAcudiente['uss_email'],
			'usuario_nombre' => $datosAcudiente['uss_nombre'],
			'institucion_id' => $config['conf_id_institucion'],
			'usuario_id'     => $datosAcudiente['uss_id']
		];

		$asunto            = $tituloMsj;
		$bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-2.php';
		
		EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
	}

}else{
	$update = [
		"aus_ausencias" => $_POST["nota"]
	];
	Ausencias::actualizarAusencia($config, $rC['aus_id'], $update);
	
	Clases::registrarAusenciaClase($conexion, $config, $_POST);
	
}	
?>
	<script type="text/javascript">
		function notifica(){
			var unique_id = $.gritter.add({
				// (string | mandatory) the heading of the notification
				title: 'Correcto',
				// (string | mandatory) the text inside the notification
				text: 'Los cambios se ha guardado correctamente!',
				// (string | optional) the image to display on the left
				image: 'files/iconos/Accept-Male-User.png',
				// (bool | optional) if you want it to fade out on its own or just sit there
				sticky: false,
				// (int | optional) the time you want it to be alive for before fading out
				time: '3000',
				// (string | optional) the class name you want to apply to that specific message
				class_name: 'my-sticky-class'
			});
		}
		
		setTimeout ("notifica()", 100);	
	</script>
    <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Los cambios se ha guardado correctamente!.
	</div>
