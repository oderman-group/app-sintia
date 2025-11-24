<?php
include("../modelo/conexion.php");
require_once("../class/Modulos.php");


$tienePermiso = Modulos::verificarPermisosPaginas($idPaginaInterna);

if (!$tienePermiso && $idPaginaInterna!='DT0107') {
	if (empty($usuariosClase)) {
		require_once("sintia-funciones.php");
		$usuariosClase = new UsuariosFunciones;
	}
	$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'page-info.php');
	echo '<script type="text/javascript">window.location.href="'.$url.'?idmsg=302&idPagina='.$idPaginaInterna.'";</script>';
	exit();	
}

$datosPaginaActual = Modulos::datosPaginaActual($idPaginaInterna);