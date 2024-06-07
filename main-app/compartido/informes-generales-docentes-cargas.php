<?php
include("session-compartida.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$idPaginaInterna = 'DT0234';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
$filtro = '';
if(!empty($_GET["docente"])){$filtro .=" AND car_docente='".$_GET["docente"]."'";}
if(!empty($_GET["grado"])){$filtro .=" AND car_curso='".$_GET["grado"]."'";}
if(!empty($_GET["asignatura"])){$filtro .=" AND car_materia='".$_GET["asignatura"]."'";}

$consulta = CargaAcademica::listarCargas($conexion, $config, "", $filtro, "car_id", "");
?>
<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Informes SINTIA</title>
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
</head>

<body style="font-family:Arial; font-size: 10px;">
	
<?php
$nombreInforme = "CARGA GENERAL DE DOCENTES";
include("../compartido/head-informes.php") ?>
	
	<div style="margin: 10px;">
		<table width="100%" border="1" style=" border:solid;border-color:<?=$Plataforma->colorUno;?>;" rules="all" align="center">    
			<tr style="font-weight:bold; height:30px; background:<?=$Plataforma->colorUno;?>; color:#FFF;">
				<td>No</td>
				<td>#CARGA</td>
				<td>Docente</td>
				<td>Grado</td>
				<td>Grupo</td>
				<td>Asignatura</td>
				<td>D.G</td>
				<td>I.H</td>
			</tr> 
			<?php
			$i=1;
			while($datos = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
				$colorFondo = '';
				if($datos[5]==1) $colorFondo = '#4086f48a';
			?>
			<tr style="text-transform: uppercase; border-color: <?=$Plataforma->colorDos;?>">
				<td align="center"><?=$i;?></td>
				<td align="center"><?=$datos[0];?></td>
				<td><?=$datos[1];?></td>
				<td><?=$datos[2];?></td>
				<td  align="center"><?=$datos[3];?></td>
				<td><?=$datos[4];?></td>
				<td align="center"><?=$opcionSINO[$datos[5]];?></td>
				<td align="center"><?=$datos[6];?></td>
			</tr>
			<?php	
				$i++;
			}
			?>  
		</table>
	</div>
	<?php include("../compartido/footer-informes.php");
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); ?>

</div>	

</body>
</html>