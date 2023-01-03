<?php
if(!isset($idPaginaInterna)){$idPaginaInterna = 1;}
	
//PERMISOS BLOQUEADOS POR PAGINA DE LOS USUARIOS DENTRO DE CADA INSTITUCIÓN
//Esto cambio de módulos a páginas y de perfil a usuarios
$permiso=mysqli_query($conexion, "SELECT * FROM permisos_modulos WHERE pm_id_perfil='".$_SESSION["id"]."' AND pm_id_modulo='".$idPaginaInterna."'");
$consultaPermiso = mysqli_num_rows($permiso);
if($consultaPermiso>0)
{
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301&idPagina='.$idPaginaInterna.'";</script>';
	exit();		
}
?>