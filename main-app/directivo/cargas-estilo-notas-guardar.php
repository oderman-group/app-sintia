<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/categoriasNotas.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0182';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

if (trim($_POST["nombre"]) == "") {
	include("../compartido/guardar-historial-acciones.php");
	echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.<br>
	<a href='javascript:history.go(-1)'>[Volver al formulario]</a></samp>";
	exit();
}

$codigo = categoriasNota::guardarCategoriaNota($conexionPDO, "catn_nombre, institucion, year, catn_id", [$_POST["nombre"], $config['conf_id_institucion'], $_SESSION["bd"]]);

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="cargas-estilo-notas.php?success=SC_DT_1&id='.base64_encode($codigo).'"</script>';
exit();