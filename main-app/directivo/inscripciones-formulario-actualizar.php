<?php
include("session.php");
$idPaginaInterna = 'DT0029';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include(ROOT_PATH.'/main-app/compartido/historial-acciones-guardar.php');
include(ROOT_PATH.'/main-app/admisiones/php-funciones.php');
require_once(ROOT_PATH.'/main-app/class/EnviarEmail.php');

//DATOS SECRETARIA(O)
$ussQuery = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id = :idSecretaria AND institucion= :idInstitucion AND year= :year";
$uss = $conexionPDO->prepare($ussQuery);
$uss->bindParam(':idSecretaria', $datosInfo['info_secretaria_academica'], PDO::PARAM_STR);
$uss->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$uss->bindParam(':year', $_SESSION["bd"], PDO::PARAM_STR);
$uss->execute();
$datosUss = $uss->fetch();
$nombreUss=strtoupper($datosUss['uss_nombre']." ".$datosUss['uss_apellido1']);

$seAdjuntoArchivo = 0;
if (!empty($_FILES['archivo1']['name'])) {
	$destino = ROOT_PATH.'/main-app/admisiones/files/adjuntos';
    $explode = explode(".", $_FILES['archivo1']['name']);
	$extension = end($explode);
	$archivo1 = uniqid('a1_') . "." . $extension;
	@unlink($destino . "/" . $archivo1);
    move_uploaded_file($_FILES['archivo1']['tmp_name'], $destino . "/" . $archivo1);
    $adjunto1 = '<p><a href="'.REDIRECT_ROUTE.'/admisiones/files/adjuntos/'.$archivo1.'">Descargar archivo 1</a></p>';
    $seAdjuntoArchivo ++;
} else {
    $archivo1 = $_POST['archivo1A'];
    $adjunto1 = '';
}

if (!empty($_FILES['archivo2']['name'])) {
	$destino = ROOT_PATH.'/main-app/admisiones/files/adjuntos';
    $explode = explode(".", $_FILES['archivo2']['name']);
	$extension = end($explode);
	$archivo2 = uniqid('a2_') . "." . $extension;
	@unlink($destino . "/" . $archivo2);
    move_uploaded_file($_FILES['archivo2']['tmp_name'], $destino . "/" . $archivo2);
    $adjunto2 = '<p><a href="'.REDIRECT_ROUTE.'/admisiones/files/adjuntos/'.$archivo2.'">Descargar archivo 2</a></p>';
    $seAdjuntoArchivo++;
} else {
    $archivo2 = $_POST['archivo2A'];
    $adjunto2 = '';
}

// Verificar el estado actual del aspirante
$sqlVerificar = "SELECT asp_estado_solicitud, asp_agno FROM ".$baseDatosAdmisiones.".aspirantes WHERE asp_id = :asp_id";
$stmtVerificar = $conexionPDO->prepare($sqlVerificar);
$stmtVerificar->bindParam(':asp_id', $_POST['solicitud'], PDO::PARAM_INT);
$stmtVerificar->execute();
$aspiranteActual = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

// Si el aspirante ya está en estado Aprobado (6), no permitir cambios de estado
if (!empty($aspiranteActual) && $aspiranteActual['asp_estado_solicitud'] == 6) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="inscripciones.php?error=ER_DT_8";</script>';
    exit();
}

//Actualiza datos en aspirantes
$aspQuery = 'UPDATE '.$baseDatosAdmisiones.'.aspirantes SET asp_estado_solicitud = :estado, asp_observacion = :observacion, asp_fecha_observacion = now(), asp_usuario_observacion = :sesion, asp_observacion_enviada = :envioCorreo, asp_archivo1 = :archivo1, asp_archivo2 = :archivo2 WHERE asp_id = :id';
$asp = $conexionPDO->prepare($aspQuery);
$asp->bindParam(':id', $_POST['solicitud'], PDO::PARAM_INT);
$asp->bindParam(':estado', $_POST['estadoSolicitud'], PDO::PARAM_INT);
$asp->bindParam(':observacion', $_POST['observacion'], PDO::PARAM_STR);
$asp->bindParam(':envioCorreo', $_POST['enviarCorreo'] , PDO::PARAM_INT);
$asp->bindParam(':sesion', $_SESSION["id"] , PDO::PARAM_INT);
$asp->bindParam(':archivo1', $archivo1, PDO::PARAM_STR);
$asp->bindParam(':archivo2', $archivo2, PDO::PARAM_STR);
$asp->execute();

// Si el estado cambió a Aprobado (6) y el año de inscripción coincide con el año actual de la institución
// Actualizar el estado de la matrícula a No matriculado (4)
if ($_POST['estadoSolicitud'] == 6 && !empty($aspiranteActual['asp_agno']) && $aspiranteActual['asp_agno'] == $_SESSION["bd"]) {
    $sqlMatricula = "UPDATE ".BD_ACADEMICA.".academico_matriculas 
                    SET mat_estado_matricula = 4
                    WHERE mat_solicitud_inscripcion = :asp_id
                    AND institucion = :institucion
                    AND year = :year";
    
    $stmtMatricula = $conexionPDO->prepare($sqlMatricula);
    $stmtMatricula->bindParam(':asp_id', $_POST['solicitud'], PDO::PARAM_INT);
    $stmtMatricula->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmtMatricula->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
    $stmtMatricula->execute();
}

//INSERTAR EN EL HISTORIAL DE OBSERVACIONES
$sql = "INSERT INTO ".$baseDatosAdmisiones.".historial_observaciones(hiso_id_institucion, hiso_year, hiso_id_solicitud, hiso_estado, hiso_envio_correo, hiso_observacion, hiso_adjuntos, hiso_resposable)VALUES(:institucion, :agno, :solicitud, :estado, :envio_correo, :observacion, :adjuntos, :responsable)";
$stmt = $conexionPDO->prepare($sql);

$agno = date("Y");
$idInstitucion = base64_decode($_POST['idInst']);

$stmt->bindParam(':institucion', $idInstitucion, PDO::PARAM_INT);
$stmt->bindParam(':agno', $agno, PDO::PARAM_INT);
$stmt->bindParam(':solicitud', $_POST['solicitud'], PDO::PARAM_INT);
$stmt->bindParam(':estado', $_POST['estadoSolicitud'], PDO::PARAM_INT);
$stmt->bindParam(':envio_correo', $_POST['enviarCorreo'], PDO::PARAM_INT);
$stmt->bindParam(':observacion', $_POST['observacion'], PDO::PARAM_STR);
$stmt->bindParam(':adjuntos', $seAdjuntoArchivo, PDO::PARAM_INT);
$stmt->bindParam(':responsable', $_SESSION["id"], PDO::PARAM_INT);

$stmt->execute();

if($_POST['enviarCorreo'] == 1){

    $archivos = array();
    if(!empty($archivo1) and file_exists(ROOT_PATH.'/main-app/admisiones/files/adjuntos/'.$archivo1)){
        $archivos[1] = ROOT_PATH.'/main-app/admisiones/files/adjuntos/'.$archivo1;
    }

    if(!empty($archivo2) and file_exists(ROOT_PATH.'/main-app/admisiones/files/adjuntos/'.$archivo2)){
        $archivos[2] = ROOT_PATH.'/main-app/admisiones/files/adjuntos/'.$archivo2;
    }

    $data = [
        'usuario_email'   => $_POST['emailAcudiente'],
        'usuario_nombre'  => $_POST['nombreAcudiente'],
        'usuario2_email'  => $datosUss['uss_email'],
        'usuario2_nombre' => $nombreUss,
        'solicitud_id'    => $_POST["solicitud"],
        'observaciones'   => $_POST['observacion'],
        'institucion_id'  => $config['conf_id_institucion'],
        'id_aspirante'    => $_POST['documentoAspirante']
    ];

    $asunto = 'Actualización de solicitud de admisión '.$_POST["solicitud"];
    $bodyTemplateRoute = ROOT_PATH.'/config-general/template-email-formulario-inscripcion.php';

    EnviarEmail::enviar($data,$asunto,$bodyTemplateRoute,null,$archivos);
}

include(ROOT_PATH.'/main-app/compartido/guardar-historial-acciones.php');

echo '<script type="text/javascript">window.location.href="inscripciones-formulario-editar.php?msg='.base64_encode(3).'&token='.md5($_POST["solicitud"]).'&id='.base64_encode($_POST["solicitud"]).'&idInst='.$_REQUEST['idInst'].'";</script>';
exit;
