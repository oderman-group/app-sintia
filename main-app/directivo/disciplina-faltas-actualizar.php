<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0097';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $sql = "UPDATE ".BD_DISCIPLINA.".disciplina_faltas 
            SET dfal_codigo=?, dfal_nombre=?, dfal_id_categoria=? 
            WHERE dfal_id_nuevo=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_POST["codigo"], PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_POST["categoria"], PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST["idRNuevo"], PDO::PARAM_STR);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="disciplina-faltas-editar.php?success=SC_DT_2&idR='.base64_encode($_POST["idR"]).'&id='.base64_encode($_POST["idR"]).'";</script>';
exit();