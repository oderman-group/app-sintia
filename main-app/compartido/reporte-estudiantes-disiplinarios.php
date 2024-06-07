<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");?>
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
$c_matricEst = Estudiantes::listarMatriculasReportes($config, $condicion);
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

