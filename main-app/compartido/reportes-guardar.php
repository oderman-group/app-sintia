<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0049';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
$usuariosClase = new UsuariosFunciones;

$datosEstudiante = Estudiantes::obtenerDatosEstudiante($_POST["estudiante"]);
$nombre = trim(Estudiantes::NombreCompletoDelEstudiante($datosEstudiante));


$cont = !empty($_POST["faltas"]) ? count($_POST["faltas"]) : 0;
$i = 0;
while ($i < $cont) {
    $idInsercion=Utilidades::generateCode("DR");
    try{
        mysqli_query($conexion, "INSERT INTO ".BD_DISCIPLINA.".disciplina_reportes(dr_id, dr_fecha, dr_estudiante, dr_falta, dr_usuario, dr_aprobacion_estudiante, dr_aprobacion_acudiente, dr_observaciones, institucion, year)VALUES('" .$idInsercion . "', '" . $_POST["fecha"] . "', '" . $datosEstudiante['uss_id'] . "', '" . $_POST["faltas"][$i] . "','" . $_POST["usuario"] . "', 0, 0,'" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }

    try{
        mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".general_alertas (alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, alr_categoria, alr_importancia, alr_vista, alr_institucion, alr_year)
        VALUES('Reporte disciplinario', 'Te han hecho un nuevo reporte disciplinario - COD: " . $_POST["faltas"][$i] . ".', 2, '" . $datosEstudiante['uss_id'] . "', now(), 3, 2, 0,'" . $config['conf_id_institucion'] . "','" . $_SESSION["bd"] . "')");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
    $idNotify = mysqli_insert_id($conexion);

    try{
        mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".general_alertas SET alr_url_acceso='reportes-disciplinarios.php?idNotify=" . $idNotify . "' WHERE alr_id='" . $idNotify . "'");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }

    try{
        mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".general_alertas (alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, alr_categoria, alr_importancia, alr_vista, alr_institucion, alr_year)
        VALUES('Reporte disciplinario - " . $nombre . "', 'A tu acudido " . $nombre . " le han hecho un nuevo reporte disciplinario - COD: " . $_POST["faltas"][$i] . ".', 2, '" . $datosEstudiante['mat_acudiente'] . "', now(), 3, 2, 0,'" . $config['conf_id_institucion'] . "','" . $_SESSION["bd"] . "')");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
    $idNotify = mysqli_insert_id($conexion);

    try{
        mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".general_alertas SET alr_url_acceso='reportes-disciplinarios.php?idNotify=" . $idNotify . "&usrEstud=" . $_POST["estudiante"] . "' WHERE alr_id='" . $idNotify . "'");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
    $i++;
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'reportes-lista.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
exit();