<?php
include("session.php");
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=notas".$_GET["idR"].".xls");
include("../modelo/conexion.php");
?><head>
	<title>Planilla de notas</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php
$consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
WHERE mat_grado='".$_GET["curso"]."' AND mat_grupo='".$_GET["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido, mat_segundo_apellido");
?>



<div align="center">  
<table  width="100%" border="1" rules="all">
    <thead
    	<tr>
            <th>#</th>
			<th>ID</th>
			<th>Estudiante</th>
			<th>Nota</th>
			<th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
<?php 
$contReg = 1;
$colorNota = "black";
while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	if($calificacion['act_registrada']==1){
		//Consulta de calificaciones si ya la tienen puestas.
		$consultaNotas=mysqli_query($conexion, "SELECT * FROM academico_calificaciones WHERE cal_id_estudiante=".$resultado[0]." AND cal_id_actividad='".$_GET["idR"]."'");
		$notas = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);
		if($notas[3]<$config[5] and $notas[3]!="") $colorNota = $config[6]; elseif($notas[3]>=$config[5]) $colorNota = $config[7];
	}	
?>    
    	<tr>	
            <td><?=$contReg;?></td>
			<td><?=$resultado[0];?></td>
			<td><?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?></td>
			<td style="text-align: center; color:<?=$colorNota;?>"><?=$notas['cal_nota'];?></td>
			<td><?=$notas['cal_observaciones'];?></td>
        </tr>   

<?php
	$contReg++;
}
?>        
    </tbody>
</table>