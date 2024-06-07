<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0231';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
?>
<head>
	<title>PLANILLA DE ESTUDIANTES</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
</head>
<body style="font-family:Arial;">
<?php
  $year=$_SESSION["bd"];
  if(isset($_REQUEST["agno"])){
    $year=$_REQUEST["agno"];
	}
	if(!empty($_REQUEST["grado"]) && !empty($_REQUEST["grupo"])){
    $grados = Grados::traerGradosGrupos($config, $_REQUEST["grado"], $_REQUEST["grupo"], $year);
	}elseif(!empty($_REQUEST["grado"])){
    $grados = Grados::traerGradosGrupos($config, $_REQUEST["grado"], "", $year);
	}else{
    $grados = Grados::traerGradosGrupos($config, "", "", $year);
	}
?>
<?php
$subNombre="";
 if((!empty($_REQUEST["grado"])) && (!empty($_REQUEST["grupo"]))){
$subNombre=$grados["gra_nombre"]." ".$grados["gru_nombre"]."<br>".$year;
}elseif(!empty($_REQUEST["grado"])) {
  $subNombre=$grados["gra_nombre"]."<br>".$year;
}
$nombreInforme =  "PLANILLA DE ESTUDIANTES ".$subNombre;
include("../compartido/head-informes.php") ?>

<table width="100%" cellspacing="5" cellpadding="5" rules="all" 
  style="
  border:solid; 
  border-color:#6017dc; 
  font-size:11px;
  ">
  <tr style="font-weight:bold; height:30px; background:#6017dc; color:#FFF;">
        <th rowspan="2">Documento</th>
        <th rowspan="2">Estudiante</th>
        <th rowspan="2">Grado</th>
        <th rowspan="2">Grupo</th>
        <th rowspan="2">Detalles</th>
  </tr>

  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
  </tr>

  <?php
  $grupo='';
  if(!empty($_REQUEST["grado"]) && !empty($_REQUEST["grupo"])){
    $grupo=$_REQUEST["grupo"];
		$adicional = "AND mat_grado='".$_REQUEST["grado"]."' AND mat_grupo='".$_REQUEST["grupo"]."'";
  }elseif(!empty($_REQUEST["grado"])) {
		$adicional = "AND mat_grado='".$_REQUEST["grado"]."'";
	}else{
		$adicional = "";
	}
  $cont=1;
  $filtroAdicional= $adicional." AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
  $consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$grados,$grupo,$year);
  $numE=mysqli_num_rows($consulta);
  while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
    $nombre = Estudiantes::NombreCompletoDelEstudiante($resultado);
  ?>
  <tr style="
  border-color:#41c4c4;
  ">
      <td><?=$resultado['mat_documento'];?></td>
      <td><?=$nombre?></td>
      <td><?=$resultado["gra_nombre"];?></td>
      <td><?=$resultado["gru_nombre"];?></td>
      <td>&nbsp;</td>
</tr>
  <?php
  $cont++;
  }//Fin mientras que
  ?>
  </table>
  Total Estudiantes:<?=$numE;?>
  <?php include("../compartido/footer-informes.php");
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); ?>
</body>
<script type="application/javascript">
print();
</script> 
</html>


