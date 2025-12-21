<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0136';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $idNota = base64_decode($_GET["id"]);
    $sql = "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_nota=NULL WHERE dn_id=? AND institucion=? AND year=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $idNota, PDO::PARAM_STR);
    $stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="comportamiento.php?error=ER_DT_3";</script>';
exit();