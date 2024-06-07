<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0138';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

try {
    mysqli_query($conexion, "DELETE FROM ".BD_GENERAL.".usuarios_por_estudiantes WHERE upe_id_usuario='".$_POST["id"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
Estudiantes::eliminarMatriculasAcudiente($config, $_POST["id"]);

$numero = (count($_POST["acudidos"]));
$contador = 0;
while ($contador < $numero) {

    $update = "mat_acudiente=".$_POST["id"]."";
    Estudiantes::actualizarMatriculasPorId($config, $_POST["acudidos"][$contador], $update);	

    $idInsercion=Utilidades::generateCode("UPE");
    try {
        mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios_por_estudiantes(upe_id, upe_id_usuario, upe_id_estudiante, institucion, year)VALUES('" .$idInsercion . "', '".$_POST["id"]."', '".$_POST["acudidos"][$contador]."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    $contador++;
}
include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="usuarios-acudidos.php?id='.base64_encode($_POST["id"]).'&success=SC_DT_2";</script>';
exit();