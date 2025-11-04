<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0016';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
$usuariosClase = new UsuariosFunciones;

$idInsercion=Utilidades::generateCode("MATA");

// Migrado a PDO - Consulta preparada
try {
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    
    $positivos   = Utilidades::sanitizarTexto($_POST["positivos"] ?? '');
    $mejorar     = Utilidades::sanitizarTexto($_POST["mejorar"] ?? '');
    $tratamiento = Utilidades::sanitizarTexto($_POST["tratamiento"] ?? '');
    $descripcion = Utilidades::sanitizarTexto($_POST["descripcion"] ?? '');

    $sql = "INSERT INTO ".BD_ACADEMICA.".matriculas_aspectos(
        mata_id, mata_estudiante, mata_usuario, mata_fecha_evento, 
        mata_aspectos_positivos, mata_aspectos_mejorar, mata_tratamiento, 
        mata_descripcion, mata_periodo, institucion, year
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $idInsercion, PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["estudiante"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_SESSION['id'], PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST["fecha"], PDO::PARAM_STR);
    $stmt->bindParam(5, $positivos, PDO::PARAM_STR);
    $stmt->bindParam(6, $mejorar, PDO::PARAM_STR);
    $stmt->bindParam(7, $tratamiento, PDO::PARAM_STR);
    $stmt->bindParam(8, $descripcion, PDO::PARAM_STR);
    $stmt->bindParam(9, $_POST["periodo"], PDO::PARAM_INT);
    $stmt->bindParam(10, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(11, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
    
    $idRegistro = $conexionPDO->lastInsertId();
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'aspectos-estudiantiles.php?idR='.$_POST["idR"]."&success=SC_DT_1&id=".base64_encode($idRegistro));

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();