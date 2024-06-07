<?php
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=Usuarios_".date("d/m/Y")."-SINTIA.xls");
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
$consulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios();
?>
<div align="center">  
<table  width="100%" border="1" rules="all">
    <thead>
    	<tr>
        	<th colspan="4" style="background:#060; color:#FFF;">MATRICULAS ACTUALES <?=date('Y');?></th>
        </tr>
    	<tr>
            <th scope="col" align="center">No.</th>
            <th scope="col" align="center">Nombre</th>
            <th scope="col" align="center"><?=$frases[53][$datosUsuarioActual['uss_idioma']];?></th>
            <th scope="col" align="center">Email</th>
        </tr>
    </thead>
    <tbody>
<?php 
$conta=1;
while($resultado=mysqli_fetch_array($consulta, MYSQLI_BOTH))
{	
?>    
    	<tr>	
            <td align="center"><?=$conta;?></td>
            <td><?=UsuariosPadre::nombreCompletoDelUsuario($resultado);?></td>
            <td><?=$resultado['pes_nombre'];?></td>
            <td><?=strtolower($resultado['uss_email']);?></td>
        </tr>   

<?php
	$conta++;
}
?>        
    </tbody>
</table>