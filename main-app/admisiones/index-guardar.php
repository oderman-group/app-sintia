<?php
include("bd-conexion.php");
include("php-funciones.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Inscripciones.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_aspirante.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

$idInst = "";

if (!empty($_REQUEST["idInst"])) { 
    $idInst = base64_decode($_REQUEST["idInst"]);
}

Inscripciones::iniciarTransacion();

$year = $config["cfgi_year_inscripcion"];

//DATOS SECRETARIA(O)
$ussQuery = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id = :idSecretaria AND institucion= :idInstitucion AND year= :year";
$uss      = $pdoI->prepare($ussQuery);

$uss->bindParam(':idSecretaria', $datosInfo['info_secretaria_academica'], PDO::PARAM_STR);
$uss->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$uss->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
$uss->execute();

$datosUss  = $uss->fetch();
$nombreUss = strtoupper($datosUss['uss_nombre']." ".$datosUss['uss_apellido1']);

// Validar documento del estudiante en aspirantes para el año específico de la inscripción
$estQuery = "SELECT * FROM aspirantes 
WHERE asp_documento = :documento 
AND asp_institucion = :institucion 
AND asp_agno = :year
AND asp_oculto = ".BDT_Aspirante::ESTADO_OCULTO_FALSO;

$est = $pdo->prepare($estQuery);
$est->bindParam(':documento', $_POST['documento'], PDO::PARAM_STR);
$est->bindParam(':institucion', $idInst, PDO::PARAM_INT);
$est->bindParam(':year', $year, PDO::PARAM_INT);
$est->execute();

if ($est->rowCount() > 0) {
    header("Location:index.php?error=".base64_encode(2)."&documento=" . $_POST['documento']);
    exit();
}

if (md5($_POST['idInst']) != $_POST['iditoken']) {
    redireccionMal('index.php', 1);
}

$nombreCompleto          = $_POST['nombreEstudiante'].' '.$_POST['nombreEstudiante2'].' '.$_POST['apellido1'].' '.$_POST['apellido2'];
$nombreCompletoAcudiente = $_POST['nombre1Acudiente'].' '.$_POST['nombre2Acudiente'].' '.$_POST['apellido1Acudiente'].' '.$_POST['apellido2Acudiente'];

// Insertar registro en aspirantes (siempre se crea nuevo para el año de inscripción)
$sql = "INSERT INTO aspirantes(asp_institucion, asp_tipo_documento, asp_documento, asp_nombre, asp_email_acudiente, asp_nombre_acudiente, asp_celular_acudiente, asp_agno, asp_estado_solicitud, asp_documento_acudiente, asp_grado, asp_hizo_proceso_antes)VALUES(:institucion, :tipoDocumento, :documento, :nombreEstudiante, :email, :nombreAcudiente, :celular, '".$year."', 8, :documentoAcudiente, :grado, :hizoProceso)";
$stmt = $pdo->prepare($sql);

$stmt->bindParam(':institucion', $idInst, PDO::PARAM_INT);
$stmt->bindParam(':tipoDocumento', $_POST['tipoDocumento'], PDO::PARAM_INT);
$stmt->bindParam(':documento', $_POST['documento'], PDO::PARAM_STR);
$stmt->bindParam(':nombreEstudiante', $nombreCompleto, PDO::PARAM_STR);
$stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
$stmt->bindParam(':nombreAcudiente', $nombreCompletoAcudiente, PDO::PARAM_STR);
$stmt->bindParam(':celular', $_POST['celular'], PDO::PARAM_STR);
$stmt->bindParam(':documentoAcudiente', $_POST['documentoAcudiente'], PDO::PARAM_STR);
$stmt->bindParam(':grado', $_POST['grado'], PDO::PARAM_STR);
$stmt->bindParam(':hizoProceso', $_POST['procesoAdmisionAntes'], PDO::PARAM_INT);

$stmt->execute();

$newId = $pdo->lastInsertId();

if ($newId > 0) {

    //Guardar información en SINTIA COLEGIOS

    //Estudiante - Verificar si ya existe
    $estuId = '';
    $estudianteExisteEnYear = isset($_POST['estudianteExisteEnYear']) && $_POST['estudianteExisteEnYear'] === '1';
    $estudianteIdExistente = isset($_POST['estudianteIdExistente']) ? $_POST['estudianteIdExistente'] : '';
    $estudianteMatIdExistente = isset($_POST['estudianteMatIdExistente']) ? $_POST['estudianteMatIdExistente'] : '';
    
    if (!empty($estudianteIdExistente) && $estudianteExisteEnYear) {
        // Estudiante existe en el año, actualizar información
        $estuId = $estudianteIdExistente;
        
        $estuUpdateQuery = "UPDATE ".BD_GENERAL.".usuarios 
                           SET uss_nombre = :ussNombres,
                               uss_apellido1 = :ussApellido1,
                               uss_apellido2 = :apellido2,
                               uss_nombre2 = :nombre2
                           WHERE uss_id = :estuId 
                           AND institucion = :idInstitucion 
                           AND year = :year";
        
        $estuUpdate = $pdoI->prepare($estuUpdateQuery);
        $estuUpdate->bindParam(':ussNombres', $_POST['nombreEstudiante'], PDO::PARAM_STR);
        $estuUpdate->bindParam(':ussApellido1', $_POST['apellido1'], PDO::PARAM_STR);
        $estuUpdate->bindParam(':apellido2', $_POST['apellido2'], PDO::PARAM_STR);
        $estuUpdate->bindParam(':nombre2', $_POST['nombreEstudiante2'], PDO::PARAM_STR);
        $estuUpdate->bindParam(':estuId', $estuId, PDO::PARAM_STR);
        $estuUpdate->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $estuUpdate->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
        $estuUpdate->execute();
    } else {
        // Estudiante no existe o existe en otro año, crear nuevo registro para este año
        $estuQuery = "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_permiso1, uss_foto, uss_portada, uss_idioma, uss_tema, uss_tipo_documento, uss_apellido1, institucion, year, uss_documento, uss_nombre2, uss_apellido2, uss_tema_sidebar, uss_tema_header, uss_tema_logo)VALUES(:codigo, :ussDocumento, SHA1('12345678'), 4, :ussNombres, 0, 0, 'default.png', 'default.png', 1, 'green', :ussTipoDocumento, :ussApellido1, :idInstitucion, :year, :ussDNI, :nombre2, :apellido2, 'white-sidebar-color', 'header-white', 'logo-white')";

        $estuId = Utilidades::generateCode("USS");
        $estu   = $pdoI->prepare($estuQuery);

        $estu->bindParam(':codigo', $estuId, PDO::PARAM_STR);
        $estu->bindParam(':ussDocumento', $_POST['documento'], PDO::PARAM_STR);
        $estu->bindParam(':ussNombres', $_POST['nombreEstudiante'], PDO::PARAM_STR);
        $estu->bindParam(':ussTipoDocumento', $_POST['tipoDocumento'], PDO::PARAM_INT);
        $estu->bindParam(':ussApellido1', $_POST['apellido1'], PDO::PARAM_STR);
        $estu->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $estu->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
        $estu->bindParam(':ussDNI', $_POST['documento'], PDO::PARAM_STR);
        $estu->bindParam(':nombre2', $_POST['nombreEstudiante2'], PDO::PARAM_STR);
        $estu->bindParam(':apellido2', $_POST['apellido2'], PDO::PARAM_STR);
        $estu->execute();
    }

    //Acudiente - Verificar si ya existe
    $acuId = '';
    if (!empty($_POST['acudienteIdExistente'])) {
        // Acudiente existe, actualizar información
        $acuId = $_POST['acudienteIdExistente'];
        
        $acudienteUpdateQuery = "UPDATE ".BD_GENERAL.".usuarios 
                                 SET uss_nombre = :nombre,
                                     uss_email = :email,
                                     uss_celular = :celular,
                                     uss_nombre2 = :nombre2,
                                     uss_apellido1 = :apellido1,
                                     uss_apellido2 = :apellido2
                                 WHERE uss_id = :acuId 
                                 AND institucion = :idInstitucion 
                                 AND year = :year";
        
        $acudienteUpdate = $pdoI->prepare($acudienteUpdateQuery);
        $acudienteUpdate->bindParam(':nombre', $_POST['nombre1Acudiente'], PDO::PARAM_STR);
        $acudienteUpdate->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $acudienteUpdate->bindParam(':celular', $_POST['celular'], PDO::PARAM_STR);
        $acudienteUpdate->bindParam(':nombre2', $_POST['nombre2Acudiente'], PDO::PARAM_STR);
        $acudienteUpdate->bindParam(':apellido1', $_POST['apellido1Acudiente'], PDO::PARAM_STR);
        $acudienteUpdate->bindParam(':apellido2', $_POST['apellido2Acudiente'], PDO::PARAM_STR);
        $acudienteUpdate->bindParam(':acuId', $acuId, PDO::PARAM_STR);
        $acudienteUpdate->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $acudienteUpdate->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
        $acudienteUpdate->execute();
    } else {
        // Acudiente no existe, crear nuevo
        $acudienteQuery = "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_email, uss_celular, institucion, year, uss_documento, uss_nombre2, uss_apellido1, uss_apellido2, uss_tipo_documento, uss_idioma, uss_tema_sidebar, uss_tema_header, uss_tema_logo)VALUES(:codigo, :usuario, SHA1('12345678'), 3, :nombre, :email, :celular, :idInstitucion, :year, :ussDNI, :nombre2, :apellido1, :apellido2, :ussTipoDocumento, 1, 'white-sidebar-color', 'header-white', 'logo-white')";

        $acuId     = Utilidades::generateCode("USS");
        $acudiente = $pdoI->prepare($acudienteQuery);

        $acudiente->bindParam(':codigo', $acuId, PDO::PARAM_STR);
        $acudiente->bindParam(':usuario', $_POST['documentoAcudiente'], PDO::PARAM_STR);
        $acudiente->bindParam(':nombre', $_POST['nombre1Acudiente'], PDO::PARAM_STR);
        $acudiente->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $acudiente->bindParam(':celular', $_POST['celular'], PDO::PARAM_STR);
        $acudiente->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $acudiente->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
        $acudiente->bindParam(':ussDNI', $_POST['documentoAcudiente'], PDO::PARAM_STR);
        $acudiente->bindParam(':nombre2', $_POST['nombre2Acudiente'], PDO::PARAM_STR);
        $acudiente->bindParam(':apellido1', $_POST['apellido1Acudiente'], PDO::PARAM_STR);
        $acudiente->bindParam(':apellido2', $_POST['apellido2Acudiente'], PDO::PARAM_STR);
        $acudiente->bindParam(':ussTipoDocumento', $_POST['tipoDocumentoAcudiente'], PDO::PARAM_INT);
        $acudiente->execute();
    }

    //Padre (Se hace la relación en la tabla para luego actualizar)
    $padreQuery = "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_tipo, institucion, year, uss_nombre, uss_estado)VALUES(:codigo, 3, :idInstitucion, :year, 'PADRE_TMP', 0)";
    $padreId    = Utilidades::generateCode("USS");
    $padre      = $pdoI->prepare($padreQuery);

    $padre->bindParam(':codigo', $padreId, PDO::PARAM_STR);
    $padre->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
    $padre->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
    $padre->execute();

    //Madre (Se hace la relación en la tabla para luego actualizar)
    $madreQuery = "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_tipo, institucion, year, uss_nombre, uss_estado)VALUES(:codigo, 3, :idInstitucion, :year, 'MADRE_TMP', 0)";
    $madreId    = Utilidades::generateCode("USS");
    $madre      = $pdoI->prepare($madreQuery);

    $madre->bindParam(':codigo', $madreId, PDO::PARAM_STR);
    $madre->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
    $madre->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
    $madre->execute();

    //Matriculas - Verificar si ya existe en el año
    $idEstudiante = null;
    
    if (!empty($estudianteMatIdExistente) && $estudianteExisteEnYear) {
        // Matrícula existe en el año, actualizar información
        $idEstudiante = $estudianteMatIdExistente;
        
        $matUpdateQuery = "UPDATE ".BD_ACADEMICA.".academico_matriculas 
                          SET mat_nombres = :nombres,
                              mat_nombre2 = :nombre2,
                              mat_primer_apellido = :apellido1,
                              mat_segundo_apellido = :apellido2,
                              mat_grado = :grado,
                              mat_estado_matricula = :estado
                          WHERE mat_id = :matId 
                          AND institucion = :idInstitucion 
                          AND year = :year";
        
        $matUpdate = $pdoI->prepare($matUpdateQuery);
        $matUpdate->bindParam(':nombres', $_POST['nombreEstudiante'], PDO::PARAM_STR);
        $matUpdate->bindParam(':nombre2', $_POST['nombreEstudiante2'], PDO::PARAM_STR);
        $matUpdate->bindParam(':apellido1', $_POST['apellido1'], PDO::PARAM_STR);
        $matUpdate->bindParam(':apellido2', $_POST['apellido2'], PDO::PARAM_STR);
        $matUpdate->bindParam(':grado', $_POST['grado'], PDO::PARAM_INT);
        $matUpdate->bindParam(':estado', Estudiantes::ESTADO_EN_INSCRIPCION, PDO::PARAM_INT);
        $matUpdate->bindParam(':matId', $idEstudiante, PDO::PARAM_INT);
        $matUpdate->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $matUpdate->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
        $matUpdate->execute();
    } else {
        // Matrícula no existe o existe en otro año, crear nueva para este año
        $codigoMAT  = strtotime("now");

        $data = [
            'tipoD'         => $_POST['tipoDocumento'],
            'nDoc'          => $_POST['documento'],
            'solicitudInsc' => $newId,
            'apellido1'     => $_POST['apellido1'],
            'apellido2'     => $_POST['apellido2'],
            'nombres'       => $_POST['nombreEstudiante'],
            'padre'         => $padreId,
            'madre'         => $madreId,
            'grado'         => $_POST['grado'],
            'matestM'       => Estudiantes::ESTADO_EN_INSCRIPCION
        ];

        //Insertamos la matrícula
        $idEstudiante = Estudiantes::insertarEstudiantes(
            $pdoI,
            $data,
            $estuId,
            $codigoMAT,
            '',
            $acuId,
            Estudiantes::AUTO_INSCRIPCION,
            $config['conf_id_institucion'],
            $config['conf_agno']
        );
    }

    //Documentos (Se hace la relación en la tabla para luego actualizar)
    Inscripciones::guardarDocumentos($pdoI, $config, $idEstudiante);
    Inscripciones::finalizarTransacion();

    //Mensaje para correo

	$data = [
		'solicitud_id'        => $newId,
		'solicitud_nombre'    => $nombreCompleto,
		'solicitud_documento' => $_POST['documento'],
		'usuario_email'       => $_POST['email'],
		'usuario_nombre'      => strtoupper($nombreCompletoAcudiente),
        'usuario2_email'      => $datosUss['uss_email'],
        'usuario2_nombre'     => $nombreUss,
        'institucion_id'      => $datosInfo['info_institucion'],
        'usuario_id'          => $datosUss['uss_id']
        
	];

	$asunto            = 'Solicitud de admisión ' . $newId;
	$bodyTemplateRoute = ROOT_PATH.'/config-general/template-email-index-inscripcion.php';

	EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);

    // Mostrar página de confirmación moderna
    $urlConsulta = "consultar-estado.php?solicitud=".base64_encode($newId)."&documento=".base64_encode($_POST['documento'])."&idInst=".$_REQUEST['idInst'];
    include("confirmation-template.php");
    exit();
} else {
    redireccionMal('index.php', 3);
}
