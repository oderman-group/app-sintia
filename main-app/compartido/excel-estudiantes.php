<?php
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=Estudiantes_".date("d/m/Y")."-SINTIA.xls");
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
?>

<head>
	<title>Excel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php
$consulta=mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_eliminado=0 ORDER BY mat_primer_apellido");
?>
<div align="center">  
<table  width="100%" border="1" rules="all">
    <thead>
    	<tr>
        	<th colspan="20" style="background:#060; color:#FFF;">MATRICULAS ACTUALES <?=date('Y');?></th>
        </tr>
    	<tr>
            <th scope="col" align="center">No.</th>
            <th scope="col" align="center">Primer apellido</th>
            <th scope="col" align="center">Segundo apellido</th>
            <th scope="col" align="center">Nombres</th>
            <th scope="col" align="center">Estudiante</th>
            <th scope="col" align="center">Grado</th>
            <th scope="col" align="center">Grupo</th>
            <th scope="col" align="center">Estado</th>
            <th scope="col" align="center">Fecha Nacimiento</th>
            <th scope="col" align="center">Lugar Nacimiento</th>
            <th scope="col" align="center">Documento</th>            
        	<th scope="col" align="center">Lugar Expedicion</th>
            <th scope="col" align="center">Direccion</th>
            <th scope="col" align="center">Barrio</th>
            <th scope="col" align="center">Telefono</th>
            <th scope="col" align="center">Email</th>
            <th scope="col" align="center">Folio</th>
            <th scope="col" align="center">Cod. Tesoreria</th>
            <th scope="col" align="center">Documento Acudiente</th>
            <th scope="col" align="center">Nombre Acudiente</th>
        </tr>
    </thead>
    <tbody>
<?php 
$conta=1;
while($resultado=mysqli_fetch_array($consulta, MYSQLI_BOTH))
{
    $consultaDatosA=mysqli_query($conexion, "SELECT gra_nombre, gru_nombre, uss_usuario, uss_nombre FROM academico_grados, usuarios, academico_grupos WHERE gra_id='".$resultado[6]."' AND gru_id='".$resultado[7]."' AND uss_id='".$resultado['mat_acudiente']."'");
	$datosA = mysqli_fetch_array($consultaDatosA, MYSQLI_BOTH);
	switch($resultado['mat_estado_matricula']){
		case 1:
		$estadoM='Matriculado';
		break;
		
		case 2:
		$estadoM='Asistente';
		break;
		
		case 3:
		$estadoM='Cancelado';
		break;
		
		case 4:
		$estadoM='No Matriculado';
		break;
	}	
?>    
    	<tr>	
            <td align="center"><?=$conta;?></td>
            <td><?=$resultado[3];?></td>
            <td><?=$resultado[4];?></td>
            <td><?=$resultado[5];?></td>
            <td><?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?></td>
            <td align="center"><?=$datosA[0];?></td>
            <td align="center"><?=$datosA[1];?></td>
            <td align="center"><?=$estadoM;?></td>
            <td align="center"><?=$resultado[9];?></td>
            <td align="center"><?=$resultado[10];?></td>
            <td align="center"><?=$resultado[12];?></td>
            <td align="center"><?=$resultado[13];?></td>
            <td align="center"><?=$resultado[15];?></td>
            <td align="center"><?=$resultado[16];?></td>
            <td align="center"><?=$resultado[17];?></td>
            <td align="center"><?=strtolower($resultado[25]);?></td>
			<td align="center"><?=$resultado[34];?></td>
            <td align="center"><?=$resultado[35];?></td>
            <td align="center"><?=$datosA[2];?></td>
            <td><?=strtoupper($datosA[3]);?></td>
        </tr>   

<?php
	$conta++;
}
?>        
    </tbody>
</table>