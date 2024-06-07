<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
require_once("../class/Estudiantes.php");
require_once("../class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
?>
<head>
	<title>Estudiantes</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    INFORME DE ESTUDIANTES</br>
</div>   
<table width="100%" cellspacing="5" cellpadding="5" rules="all" 
  style="
  border:solid; 
  border-color:#6017dc; 
  font-size:11px;
  ">
  <tr style="font-weight:bold; height:30px; background:#6017dc; color:#FFF;">
        <th>ID</th>
        <th>Documento</th>
        <th>Estudiante</th>
        <th>Grado</th>
        <th>Grupo</th>
        <th>Fecha de nacimiento</th>
        <th>Acudiente</th>
  </tr>
  <?php
  if(isset($_GET["grado"]) and isset($_GET["grupo"])) $adicional = "mat_grado='".$_GET["grado"]."' AND mat_grupo='".$_GET["grupo"]."' AND "; else $adicional = "";
  $cont=1;
  $consulta = Estudiantes::listarEstudiantes(0, $adicional, '');
  while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	$acudiente = UsuariosPadre::sesionUsuario($resultado['mat_acudiente']);
  
  $grados = Grados::traerGradosGrupos($config, $resultado['mat_grado'], $resultado['mat_grupo']);
  ?>
  <tr style="font-size:13px;">
      <td><?=$resultado['mat_id'];?></td>
      <td><?=$resultado['mat_documento'];?></td>
      <td><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>
      <td><?=$grados["gra_nombre"];?></td>
      <td><?=$grados["gru_nombre"];?></td>
      <td><?=$resultado['mat_fecha_nacimiento'];?></td>
     <td><?=strtoupper($acudiente['uss_nombre'].' '.$acudiente["uss_nombre2"].' '.$acudiente["uss_apellido1"].' '.$acudiente["uss_apellido2"]);?></td> 
</tr>
  <?php
  $cont++;
  }//Fin mientras que
  ?>
  </table>

	<div style="font-size:10px; margin-top:10px; text-align:center;">
      <img src="https://main.plataformasintia.com/app-sintia/main-app/sintia-logo-2023.png" width="150"><br>
      PLATAFORMA EDUCATIVA SINTIA - <?=date("l, d-M-Y");?>
     </div>
</body>
</html>


