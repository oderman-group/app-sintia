<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");
require_once(ROOT_PATH."/main-app/class/App/Academico/Clase.php");
require_once(ROOT_PATH."/main-app/class/App/Academico/Carga.php");
require_once(ROOT_PATH."/main-app/class/App/Academico/Materia.php");
require_once(ROOT_PATH.'/main-app/class/EnviarEmail.php');
require_once(ROOT_PATH."/main-app/class/Modulos.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

$rC = Ausencias::traerAusenciasClaseEstudiante($config, $_POST["codNota"], $_POST["codEst"]);
if (empty($rC)) {

	if (!empty($rC['aus_id'])) {
		Ausencias::eliminarAusenciasID($config, $rC['aus_id']);
	}

	Ausencias::guardarAusencia($conexionPDO, "aus_id_estudiante, aus_ausencias, aus_id_clase, institucion, year, aus_id", [$_POST["codEst"],$_POST["nota"],$_POST["codNota"], $config['conf_id_institucion'], $_SESSION["bd"]]);
	
	Clases::registrarAusenciaClase($conexion, $config, $_POST);

	// Verificar si el módulo de notificaciones está activo antes de enviar email
	if ($_POST["nota"] > 0 && Modulos::verificarModulosDeInstitucion(Modulos::MODULO_NOTIFICACIONES_NOTAS_BAJAS)) {

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
		// Solo intentar enviar si el acudiente tiene un correo válido
		if (!empty($datosAcudiente) && !empty($datosAcudiente['uss_email'])) {
			try {
				// Preparar nombre completo del acudiente
				$nombreCompleto = $datosAcudiente['uss_nombre'];
				if (!empty($datosAcudiente['uss_apellido1'])) {
					$nombreCompleto .= ' ' . $datosAcudiente['uss_apellido1'];
				}
				
				// Obtener nombre del docente (de la sesión actual)
				$nombreDocente = 'No asignado';
				if (!empty($_SESSION["datosUsuario"]['uss_nombre'])) {
					$nombreDocente = trim($_SESSION["datosUsuario"]['uss_nombre'] . ' ' . 
										($_SESSION["datosUsuario"]['uss_apellido1'] ?? ''));
				}
				
				// Preparar datos para el template moderno
				$dataCorreo = [
					'nombre_acudiente'  => $nombreCompleto,
					'nombre_estudiante' => $nombre,
					'nombre_materia'    => $datosActividad[0]["mat_nombre"] ?? 'la materia',
					'numero_ausencias'  => $_POST["nota"],
					'tema_clase'        => $datosActividad[0]["cls_tema"] ?? 'Clase',
					'fecha_clase'       => !empty($datosActividad[0]["cls_fecha"]) ? date('d/m/Y', strtotime($datosActividad[0]["cls_fecha"])) : date('d/m/Y'),
					'nombre_docente'    => $nombreDocente,
					'curso'             => $datosActividad[0]["gra_nombre"] ?? '',
					'grupo'             => $datosActividad[0]["gru_nombre"] ?? '',
					'ausencias_totales' => $_POST["nota"], // Por ahora igual al número de ausencias de esta clase
					'usuario_email'     => $datosAcudiente['uss_email'],
					'usuario_nombre'    => $datosAcudiente['uss_nombre'],
					'institucion_id'    => $config['conf_id_institucion'],
					'usuario_id'        => $datosAcudiente['uss_id']
				];

				$asunto = '📅 Notificación de Ausencia - ' . $nombre;
				$bodyTemplateRoute = ROOT_PATH.'/config-general/template-email-ausencias.php';
				
				EnviarEmail::enviar($dataCorreo, $asunto, $bodyTemplateRoute, null, null);
				
				error_log("✅ Email de ausencia enviado a: " . $datosAcudiente['uss_email'] . " | Estudiante: " . $nombre . " | Ausencias: " . $_POST["nota"]);
				
			} catch (Exception $e) {
				// Si falla el envío de correo, solo registramos el error pero continuamos
				error_log("❌ Error al enviar correo de notificación de ausencia: " . $e->getMessage());
			}
		} else {
			// Registrar que no se pudo enviar por falta de datos del acudiente
			error_log("⚠️ No se pudo enviar notificación de ausencia para el estudiante {$_POST['codEst']}: acudiente sin correo electrónico");
		}
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
