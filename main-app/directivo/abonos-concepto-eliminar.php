<?php
include("session.php");
$idPaginaInterna = 'DT0304';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

try{
    mysqli_query($conexion, "DELETE FROM ".BD_FINANCIERA.".payments_invoiced WHERE id='".$_GET["idR"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="movimientos.php?error=ER_DT_3";</script>';
exit();