<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<?php

?>
<head>
	<title>REPORTES DISCIPLINARIOS</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/logoodermanp.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    REPORTES DISCIPLINARIOS</br>
</div>   
  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
        <th>Código</th>
        <th>Nombre</th>
        <th>Curso</th>
        <th>Grupo</th>
        <th>Tipo Estudainte</th>
        <th>Tipo de falta</th>
        <th>Falta</th>
        <th>Fecha</th>
  </tr>
  <?php
  $condicionw="";
  $condicion="";
  if($_POST["cursosR"]!=""){
	  $condicion="gra_id=".$_POST["cursosR"];
	  }
	  
	 if($_POST["gruposR"]!=""){
	  if($condicion!=""){
	  $condicion=$condicion." AND gru_id=".$_POST["gruposR"];
	  }else{
		  $condicion=$condicion." gru_id=".$_POST["gruposR"];
		  }
	  }
	    
	  if($_POST["tipoR"]!=""){
	  if($condicion!=""){
	  $condicion=$condicion." AND ogen_id=".$_POST["tipoR"];
	  }else{
	  $condicion=$condicion." ogen_id=".$_POST["tipoR"];  
		  }
	  }
	  
	   if($_POST["faltaR"]!=""){
	  if($condicion!=""){
	  $condicion=$condicion." AND dr_tipo=".$_POST["faltaR"];
	  }else{
	  $condicion=$condicion." dr_tipo=".$_POST["faltaR"];  
		  }
	  }
	if($condicion!=""){
		$condicionw="WHERE ";
		}
		$c_matricEst=mysqli_query($conexion, "SELECT mat_matricula,mat_primer_apellido,mat_segundo_apellido,mat_nombres,gru_nombre,gra_nombre,ogen_nombre,dr_fecha, dr_estudiante, dr_falta,
CASE dr_tipo WHEN 1 THEN 'Leve' WHEN 2 THEN 'Grave' WHEN 3 THEN 'Gravísima' END as tipo_falta
FROM academico_matriculas am INNER JOIN academico_grupos ag ON am.mat_grupo=ag.gru_id
INNER JOIN academico_grados agr ON agr.gra_id=am.mat_grado
INNER JOIN opciones_generales og ON og.ogen_id=am.mat_tipo
INNER JOIN disciplina_reportes dr ON dr.dr_estudiante=am.mat_id ".$condicionw.$condicion."
ORDER BY mat_primer_apellido;");
 while($resultado=mysqli_fetch_array($c_matricEst)){
  ?>
  <tr style="font-size:13px;">
      <td><?=$resultado["mat_matricula"];?></td>
      <td><?=strtoupper($resultado["mat_primer_apellido"]." ".$resultado["mat_segundo_apellido"]." ".$resultado["mat_nombres"]);?></td>
      <td><?=$resultado["gra_nombre"];?></td>
      <td><?=$resultado["gru_nombre"];?></td>
      <td><?=$resultado["ogen_nombre"];?></td>
     <td><?=$resultado["tipo_falta"];?></td> 
     <td><?=$resultado["dr_falta"];?></td> 
     <td><?=$resultado["dr_fecha"];?></td> 
</tr>
  <?php
   }//Fin mientras que
  ?>
  </table>
  </center>
	<div align="center" style="font-size:10px; margin-top:10px;">
                                        <img src="../files/images/sintia.png" height="50" width="100"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>
</body>
</html>

