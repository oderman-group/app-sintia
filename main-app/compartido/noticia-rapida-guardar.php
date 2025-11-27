<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0005';
include("../compartido/historial-acciones-guardar.php");

$estado = 1;
if ($datosUsuarioActual['uss_tipo'] == 4) {
    $estado = 0;
}

$destinatarios = "1,2,3,4,5";

// Obtener contenido desde POST (para compatibilidad con el feed moderno)
$contenido = isset($_POST["contenido"]) ? $_POST["contenido"] : (isset($_GET["contenido"]) ? $_GET["contenido"] : '');

// Validar que el contenido no esté vacío
if (empty(trim($contenido))) {
    echo "error"; // Retornar error si no hay contenido
    exit();
}

try{
    mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".social_noticias(not_usuario, not_descripcion, not_fecha, not_estado, not_para, not_institucion, not_year,not_imagen)VALUES('" . $_SESSION["id"] . "','" . mysqli_real_escape_string($conexion, $contenido) . "',now(), '" . $estado . "', '" . $destinatarios . "','" . $config['conf_id_institucion'] . "','" . $_SESSION["bd"] . "','')");
    $idRegistro = mysqli_insert_id($conexion);
    echo $idRegistro;
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
include("../compartido/guardar-historial-acciones.php");