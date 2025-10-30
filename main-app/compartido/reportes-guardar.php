<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0049';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH.'/main-app/class/EnviarEmail.php');

$usuariosClase = new UsuariosFunciones;

// Validar que el estudiante exista en el POST
if (empty($_POST["estudiante"])) {
    echo '<div class="alert alert-danger">
        <strong>Error:</strong> No se ha especificado un estudiante. Por favor, intenta nuevamente.
    </div>';
    exit();
}

$datosEstudiante = Estudiantes::obtenerDatosEstudiante($_POST["estudiante"]);

// Validar que se obtuvieron los datos del estudiante
if (empty($datosEstudiante)) {
    echo '<div class="alert alert-danger">
        <strong>Error:</strong> No se encontraron datos del estudiante con ID: ' . htmlspecialchars($_POST["estudiante"]) . '
    </div>';
    exit();
}

$nombre = trim(Estudiantes::NombreCompletoDelEstudiante($datosEstudiante));

$consultaDatosAcudiente = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$datosEstudiante['mat_acudiente']."'");
$datosAcudiente = mysqli_fetch_array($consultaDatosAcudiente, MYSQLI_BOTH);


$cont = !empty($_POST["faltas"]) ? count($_POST["faltas"]) : 0;

if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_NOTIFICACIONES_REPORTES_CONVIVENCIA) && !empty($datosAcudiente)) {
    //INICIO ENVÍO DE MENSAJE
    $tituloMsj    = 'Reporte disciplinario';
    $contenidoMsj = '
        <p style="color:navy;">
            Hola ' . strtoupper($datosAcudiente['uss_nombre']) . ', a tu acudido ' . $nombre . ' le han hecho un nuevo reporte disciplinario con '.$cont.' faltas.<br>
            Por favor ingresa a la plataforma y verificar dicho reporte. 
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

    try {
        EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
    } catch (Exception $e) {
        // Si falla el envío de correo, solo registramos el error pero continuamos
        error_log("❌ Error al enviar correo de notificación de reporte disciplinario: " . $e->getMessage());
    }
}

// Migrado a PDO - Consultas preparadas
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');

$sqlReporte = "INSERT INTO ".BD_DISCIPLINA.".disciplina_reportes(
    dr_id, dr_fecha, dr_estudiante, dr_falta, dr_usuario, dr_aprobacion_estudiante, 
    dr_aprobacion_acudiente, dr_observaciones, institucion, year
) VALUES (?, ?, ?, ?, ?, 0, 0, ?, ?, ?)";
$stmtReporte = $conexionPDO->prepare($sqlReporte);

$sqlAlerta = "INSERT INTO ".$baseDatosServicios.".general_alertas (
    alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, 
    alr_categoria, alr_importancia, alr_vista, alr_institucion, alr_year
) VALUES (?, ?, 2, ?, now(), 3, 2, 0, ?, ?)";
$stmtAlerta = $conexionPDO->prepare($sqlAlerta);

$sqlUpdate = "UPDATE ".$baseDatosServicios.".general_alertas SET alr_url_acceso=? WHERE alr_id=?";
$stmtUpdate = $conexionPDO->prepare($sqlUpdate);

$i = 0;
while ($i < $cont) {
    $idInsercion=Utilidades::generateCode("DR");
    
    try{
        $stmtReporte->bindParam(1, $idInsercion, PDO::PARAM_STR);
        $stmtReporte->bindParam(2, $_POST["fecha"], PDO::PARAM_STR);
        $stmtReporte->bindParam(3, $datosEstudiante['uss_id'], PDO::PARAM_STR);
        $stmtReporte->bindParam(4, $_POST["faltas"][$i], PDO::PARAM_STR);
        $stmtReporte->bindParam(5, $_POST["usuario"], PDO::PARAM_STR);
        $stmtReporte->bindParam(6, $_POST["contenido"], PDO::PARAM_STR);
        $stmtReporte->bindParam(7, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtReporte->bindParam(8, $_SESSION["bd"], PDO::PARAM_INT);
        $stmtReporte->execute();
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }

    try{
        $nombreAlerta1 = 'Reporte disciplinario';
        $descripcionAlerta1 = 'Te han hecho un nuevo reporte disciplinario - COD: ' . $_POST["faltas"][$i] . '.';
        $stmtAlerta->bindParam(1, $nombreAlerta1, PDO::PARAM_STR);
        $stmtAlerta->bindParam(2, $descripcionAlerta1, PDO::PARAM_STR);
        $stmtAlerta->bindParam(3, $datosEstudiante['uss_id'], PDO::PARAM_STR);
        $stmtAlerta->bindParam(4, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtAlerta->bindParam(5, $_SESSION["bd"], PDO::PARAM_INT);
        $stmtAlerta->execute();
        
        $idNotify = $conexionPDO->lastInsertId();
        
        $urlAcceso1 = 'reportes-disciplinarios.php?idNotify=' . $idNotify;
        $stmtUpdate->bindParam(1, $urlAcceso1, PDO::PARAM_STR);
        $stmtUpdate->bindParam(2, $idNotify, PDO::PARAM_INT);
        $stmtUpdate->execute();
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }

    try{
        $nombreAlerta2 = 'Reporte disciplinario - ' . $nombre;
        $descripcionAlerta2 = 'A tu acudido ' . $nombre . ' le han hecho un nuevo reporte disciplinario - COD: ' . $_POST["faltas"][$i] . '.';
        $stmtAlerta->bindParam(1, $nombreAlerta2, PDO::PARAM_STR);
        $stmtAlerta->bindParam(2, $descripcionAlerta2, PDO::PARAM_STR);
        $stmtAlerta->bindParam(3, $datosEstudiante['mat_acudiente'], PDO::PARAM_STR);
        $stmtAlerta->bindParam(4, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtAlerta->bindParam(5, $_SESSION["bd"], PDO::PARAM_INT);
        $stmtAlerta->execute();
        
        $idNotify = $conexionPDO->lastInsertId();
        
        $urlAcceso2 = 'reportes-disciplinarios.php?idNotify=' . $idNotify . '&usrEstud=' . $_POST["estudiante"];
        $stmtUpdate->bindParam(1, $urlAcceso2, PDO::PARAM_STR);
        $stmtUpdate->bindParam(2, $idNotify, PDO::PARAM_INT);
        $stmtUpdate->execute();
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
    $i++;
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'reportes-lista.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
exit();