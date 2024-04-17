<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0232';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
require_once("../class/servicios/GradoServicios.php");
require_once("../class/servicios/MediaTecnicaServicios.php");
?>

<head>
	<title>Estudiantes</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
</head>
<body style="font-family:Arial;">
<?php
$nombreInforme = "INFORME DE ESTUDIANTES";
include("../compartido/head-informes.php") ?> 
<?php
$condicionw="";
$condicion="";
$curso=0;
if(!empty($_POST["cursosR"])){
	$curso=1;
	$cursoActual=GradoServicios::consultarCurso($_REQUEST["cursosR"]);
	$condicion=" AND gra_id=".$_POST["cursosR"];
}
if(!empty($_POST["gruposR"])){
	$condicion .= " AND gru_id=".$_POST["gruposR"];
} 
if(!empty($_POST["estadoR"])){
	$condicion .= " AND mat_estado_matricula=".$_POST["estadoR"];  
}  
if(!empty($_POST["tipoR"])){
	$condicion .= " AND mat_tipo=".$_POST["tipoR"];  
}

if(!empty($_POST["acudienteR"])){
	if($_POST["acudienteR"]==1){
		$condicion .= " AND mat_acudiente IS NOT NULL";
	}else{
		$condicion .= " AND mat_acudiente IS NULL";
	}
}

if(!empty($_POST["fotoR"])){
	if($_POST["fotoR"]==1){
		$condicion .= " AND mat_foto IS NOT NULL";
	}else{
		$condicion .= " AND mat_foto IS NULL";
	}
}
if(!empty($_POST["inclu"])){
	$condicion .= " AND mat_inclusion=".$_POST["inclu"];
}
if(!empty($_POST["extra"])){
	$condicion .= " AND mat_extranjero=".$_POST["extra"]; 
}
if(!empty($_POST["generoR"])){
	$condicion .= " AND mat_genero=".$_POST["generoR"];  
}

if(!empty($_POST["religionR"])){
	$condicion .= " AND mat_religion=".$_POST["religionR"];
}
if(!empty($_POST["estratoE"])){
	$condicion .= " AND mat_estrato=".$_POST["estratoE"];  
}
if(!empty($_POST["tdocumentoR"])){
	$condicion .= " AND mat_tipo_documento=".$_POST["tdocumentoR"];  
}

if($condicion!=""){
	$condicionw="WHERE";
}
if($curso=1 && (!empty($cursoActual) && $cursoActual["gra_tipo"]==GRADO_INDIVIDUAL)){
	$consultaMatriculaEst=MediaTecnicaServicios::reporteEstadoEstudiantesMT($config,$condicion);
}else{
	$where=$condicionw.$condicion;
	$consultaMatriculaEst=Estudiantes::reporteEstadoEstudiantes($condicion);
}

$numE=mysqli_num_rows($consultaMatriculaEst);
 ?>
 <div style="width:100%; margin-left:auto; margin-right:auto;">
 Total Estudiantes: <?=$numE;?>
 </div>
  <table width="100%" cellspacing="5" cellpadding="5" rules="all" 
  style="border:solid; border-color:<?=$Plataforma->colorUno;?>;font-size:11px;">
  <tr style="font-weight:bold; height:30px; background:<?=$Plataforma->colorUno;?>; color:#FFF;">
        <th>ID</th>
        <th>Nombre</th>
        <th>Curso</th>
        <th>Grupo</th>
        <th>Estado</th>
        <th>Tipo Estudainte</th>
        <th>Foto</th>
        <th>Genero</th>
        <th>Religion</th>
        <th>Inclusión</th>
        <th>Extranjero</th>
        <th>Estrato</th>
        <th>Tipo Documento</th>
        <th>Documento</th>
        <th>Acudiente</th>
        <th>Cedula Acudiente</th>
        <th>Celular</th>
        <th>Telefono</th>
        <th>Correo</th>
  </tr>
<?php
 while($resultado=mysqli_fetch_array($consultaMatriculaEst, MYSQLI_BOTH)){
	switch ($resultado["mat_inclusion"]) {
    	case 0:
        	$inclusion="No";
        break;
		case 1:
        	$inclusion="Si";
        break;
	}
	switch ($resultado["mat_extranjero"]) {
    	case 0:
        	$extran="No";
        break;
		case 1:
        	$extran="Si";
        break;
	}
	if ($resultado["mat_inclusion"]==1){
		$color="#00F";
	}else{
		$color="#000";
	}
  ?>
 <tr style="border-color:<?=$Plataforma->colorDos;?>;">
      <td><?=$resultado["mat_id"];?></td>
      <td><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>
      <td><?=$resultado["gra_nombre"];?></td>
      <td><?=$resultado["gru_nombre"];?></td>
      <td><?=$resultado["estado"];?></td>
     <td><?=$resultado["Tipo_est"];?></td>
     <td><?=$resultado["foto"];?></td>
     <td><?=$resultado["genero"];?></td>
     <td><?=$resultado["religion"];?></td>
     <td><?=$inclusion;?></td>
     <td><?=$extran;?></td>
     <td><?=$resultado["estrato"];?></td>
     <td><?=$resultado["tipoDoc"];?></td> 
     <td><?=$resultado["mat_documento"];?></td> 
     <td><?=$resultado["nom_acudiente"];?></td>
     <td><?=$resultado["uss_usuario"];?></td>
     <td><?=$resultado["uss_celular"];?></td>
     <td><?=$resultado["uss_telefono"];?></td>
     <td><?=$resultado["uss_email"];?></td>
</tr>
  <?php
   }//Fin mientras que
  ?>
  </table>

  <?php include("../compartido/footer-informes.php");
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); ?>
</body>
</html>


