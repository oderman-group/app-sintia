<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0227';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$Plataforma = new Plataforma;

$year=$_SESSION["bd"];
if(isset($_POST["year"])){
	$year=$_POST["year"];
}
if(isset($_GET["year"])){
	$year=base64_decode($_GET["year"]);
}

$periodoActual = 4;
if(isset($_POST["periodo"])){
	$periodoActual=$_POST["periodo"];
}
if(isset($_GET["periodo"])){
	$periodoActual=base64_decode($_GET["periodo"]);
}

switch($periodoActual){
	case 1:
		$periodoActuales = "Primero";
		break;
	case 2:
		$periodoActuales = "Segundo";
		break;
	case 3:
		$periodoActuales = "Tercero";
		break;
	case 4:
		$periodoActuales = "Final";
		break;
	case 5:
		$periodoActual = 4;
		$periodoActuales = "Final";
		break;
}
//CONSULTA ESTUDIANTES MATRICULADOS
$curso='';
if(isset($_POST["curso"])){
	$curso=$_POST["curso"];
}
if(isset($_GET["curso"])){
	$curso=base64_decode($_GET["curso"]);
}

$id='';
if(isset($_POST["id"])){
	$id=$_POST["id"];
}
if(isset($_GET["id"])){
	$id=base64_decode($_GET["id"]);
}

$filtro = '';
if(!empty($_REQUEST["curso"])){$filtro .= " AND mat_grado='".$curso."'";}

if(!empty($_REQUEST["id"])){$filtro .= " AND mat_id='".$id."'";}

$grupo="";
if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".$_REQUEST["grupo"]."'"; $grupo=$_REQUEST["grupo"];}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<?php

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
//contador materias
$contPeriodos=0;
$contadorIndicadores=0;
$materiasPerdidas=0;
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
$numUsr=mysqli_num_rows($usr);

if ($numUsr == 0) {

	$url= UsuariosPadre::verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'page-info.php?idmsg=306');
	echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
	exit();

}
$datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
$idGrado=$datosUsr["mat_grado"];
$idGrupo=$datosUsr["mat_grupo"];
if($matriculadosDatos["mat_tipo_matricula"]==GRADO_INDIVIDUAL){
	$idGrado=$matriculadosDatos["matcur_id_curso"];
	$idGrupo=$matriculadosDatos["matcur_id_grupo"];
}
$nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);

$contadorPeriodos=0;
?>
<!doctype html>
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
	<link href="../../config-general/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
#saltoPagina
{
	PAGE-BREAK-AFTER: always;
}
@media print {
	@page {
		size: landscape;
	}
}
</style>
</head>

<body style="font-family:Arial;">
<?php
//CONSULTA QUE ME TRAE EL DESEMPEÑO
$consultaDesempeno = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
//CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
$consultaMatAreaEst = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $idGrado, $idGrupo, $year);
$numeroPeriodos=$config["conf_periodo"];

$nombreInforme = "REGISTRO DE VALORACIÓN";
if($config['conf_mostrar_encabezado_informes'] == 1){
	include("../compartido/head-informes.php");
}else{
?>
<div align="center" style="margin-bottom:10px; font-weight:bold;">
		<img class="img-thumbnail" src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" width="100%"><br><br>
		<b><?=$nombreInforme?></b>
</div>
<?php } ?>
<div>&nbsp;</div>



<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:10px;">
    <tr>
    	<td>C&oacute;digo: <b><?=$datosUsr["mat_matricula"];?></b></td>
        <td>Nombre: <b><?=$nombre?></b></td>   
        <td>Matricula: <b><?=$datosUsr["mat_numero_matricula"];?></b></td>   
    </tr>
    
    <tr>
    	<td>Grado: <b><?=$matriculadosDatos["gra_nombre"]." ".$matriculadosDatos["gru_nombre"];?></b></td>
        <td>Periodo: <b><?=strtoupper($periodoActuales);?></b></td>
        <td>Folio: <b><?=$datosUsr["mat_folio"];?></b></td>
    </tr>
</table>
<br>
<table width="100%" align="left">
<tr style="border:solid; font-weight:bold; color:#000; font-size:10px;border-color:<?=$Plataforma->colorUno;?>;">
    <td width="20%" align="center">AREAS/ ASIGNATURAS</td>
    <td width="2%" align="center">I.H</td>
    <td width="4%" align="center">DEF</td>
    <td width="8%" align="center">DESEMPE&Ntilde;O</td>   
    <td width="2%" align="center">AUS</td>
    <td width="15%" align="center">OBSERVACIONES</td>
</tr> 

    <tr>
    	<td class="area" colspan="<?=$columnas;?>" style="font-size:10px; font-weight:bold;"></td>
    </tr>

        <?php 
		$ultimoPeriodoAreas = $config['conf_periodos_maximos'];
		while($fila = mysqli_fetch_array($consultaMatAreaEst, MYSQLI_BOTH)){
		
		$condicion="1,2,3,4";
		$condicion2="4";
		
//CONSULTA QUE ME TRAE EL NOMBRE Y EL PROMEDIO DEL AREA
$consultaNotdefArea = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

//CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
$consultaAMat = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

//CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
$consultaAMatPer = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

$resultadoNotArea=mysqli_fetch_array($consultaNotdefArea, MYSQLI_BOTH);
$numfilasNotArea=mysqli_num_rows($consultaNotdefArea);
$totalPromedio = 0;
if(!empty($resultadoNotArea["suma"])){
	$totalPromedio = round($resultadoNotArea["suma"],1);
}

if (!empty($resultadoNotArea['periodo']) && $resultadoNotArea['periodo'] < $config['conf_periodos_maximos']){
	$ultimoPeriodoAreas = $resultadoNotArea['periodo'];
}


if($totalPromedio==1)	$totalPromedio="1.0";	if($totalPromedio==2)	$totalPromedio="2.0";		if($totalPromedio==3)	$totalPromedio="3.0";	if($totalPromedio==4)	$totalPromedio="4.0";	if($totalPromedio==5)	$totalPromedio="5.0";
	if($numfilasNotArea>0){
			?>
  <tr style="font-size:10px;">
            <td style="font-size:10px; font-weight:bold;"><?php echo $resultadoNotArea["ar_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:10px;"></td>
        <td align="center" style="font-weight:bold;"><?php 
		
		if($datosUsr["mat_grado"]>11){
				$notaFA = ceil($totalPromedio);
				switch($notaFA){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
				}
				}else{
		echo $totalPromedio;
				}
		
		?></td>
         <td align="center" style="font-weight:bold;"></td>
          <td align="center" style="font-weight:bold;"></td>
	</tr>
<?php

while($fila2=mysqli_fetch_array($consultaAMat, MYSQLI_BOTH)){ 
	$contadorPeriodos=0;
	mysqli_data_seek($consultaAMatPer,0);
	//CONSULTAR NOTA POR PERIODO
	while($fila3=mysqli_fetch_array($consultaAMatPer, MYSQLI_BOTH)){
		if($fila2["mat_id"]==$fila3["mat_id"]){
			$contadorPeriodos++;
			$notaBoletin=0;
			if(!empty($fila3["bol_nota"])){
				$notaBoletin=$fila3["bol_nota"];
			}
			$notaPeriodo=round($notaBoletin,1);
			if($notaPeriodo==1)$notaPeriodo="1.0";	if($notaPeriodo==2)$notaPeriodo="2.0";	if($notaPeriodo==3)$notaPeriodo="3.0";	if($notaPeriodo==4)$notaPeriodo="4.0";	if($notaPeriodo==5)$notaPeriodo="5.0";
			$notas[$contadorPeriodos] =$notaPeriodo;
		}
	}//FIN FILA3
?>
 <tr style="font-size:10px;">
            <td style="font-size:10px;"><?php echo $fila2["mat_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:10px;"><?php echo $fila2["car_ih"];?></td>
<?php
	  $totalPromedio2=round( $fila2["suma"],1);
	   
	   if($totalPromedio2==1)	$totalPromedio2="1.0";	if($totalPromedio2==2)	$totalPromedio2="2.0";		if($totalPromedio2==3)	$totalPromedio2="3.0";	if($totalPromedio2==4)	$totalPromedio2="4.0";	if($totalPromedio2==5)	$totalPromedio2="5.0";
	   //if($totalPromedio2<$rDesempeno["desbasdesde"]){$materiasPerdidas++;}
	    $msj='';
	   if($totalPromedio2<$config[5]){
			$consultaNivelaciones = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);
			$numNiv=mysqli_num_rows($consultaNivelaciones);
			if($numNiv>0){
				$nivelaciones = mysqli_fetch_array($consultaNivelaciones, MYSQLI_BOTH);
				if($nivelaciones['niv_definitiva']<$config[5]){
					$materiasPerdidas++;
				}else{
					$totalPromedio2 = $nivelaciones['niv_definitiva'];
					$msj='Niv';
				}
			}		   
		}
	   ?>
       
        <td align="center" style="font-weight:bold; "><?php 
		
					if($datosUsr["mat_grado"]>11){
				$notaFI = ceil($totalPromedio2);
				switch($notaFI){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
				}
				}else{
					echo $totalPromedio2;
				}
		
		?></td>
        <td align="center" style="font-weight:bold;"><?php //DESEMPEÑO
		while($rDesempeno=mysqli_fetch_array($consultaDesempeno, MYSQLI_BOTH)){
			if($totalPromedio2>=$rDesempeno["notip_desde"] && $totalPromedio2<=$rDesempeno["notip_hasta"]){
				if($datosUsr["mat_grado"]>11){
					$notaFD = ceil($totalPromedio2);
				switch($notaFD){
					case 1: echo "BAJO"; break;
					case 2: echo "BAJO"; break;
					case 3: echo "B&Aacute;SICO"; break;
					case 4: echo "ALTO"; break;
					case 5: echo "SUPERIOR"; break;
				}

				}else{
					
						echo $rDesempeno["notip_nombre"];
					}
				}
			}
			mysqli_data_seek($consultaDesempeno,0);
			$matmaxaus=0;
			if(!empty($fila2["matmaxaus"])){
				$matmaxaus=$fila2["matmaxaus"];
			}
		 ?></td>
        <td align="center" style="font-weight:bold; "><?php if(!empty($rAusencias[0]) && $rAusencias[0]>0){ echo $rAusencias[0]."/".$matmaxaus;} else{ echo "0.0/".$matmaxaus;}?></td>
        
        <td align="center">_______________________________________</td>
	
	</tr>
<?php
}//while fin materias
?>  
<?php 
}}//while fin areas

//MEDIA TECNICA
if (array_key_exists(10, $_SESSION["modulos"]) && $matriculadosDatos["mat_tipo_matricula"]!=GRADO_INDIVIDUAL){
	$consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config,$year,$matriculadosDatos['mat_id']);
	while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
		if(!empty($datosEstudianteActualMT)){

//CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
$consultaMatAreaEst = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $year);
while($fila = mysqli_fetch_array($consultaMatAreaEst, MYSQLI_BOTH)){

$condicion="1,2,3,4";
$condicion2="4";

//CONSULTA QUE ME TRAE EL NOMBRE Y EL PROMEDIO DEL AREA
$consultaNotdefArea = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

//CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
$consultaAMat = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

//CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
$consultaAMatPer = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

$resultadoNotArea=mysqli_fetch_array($consultaNotdefArea, MYSQLI_BOTH);
$numfilasNotAreaMT=mysqli_num_rows($consultaNotdefArea);
$totalPromedio = 0;
if(!empty($resultadoNotArea["suma"])){
$totalPromedio = round($resultadoNotArea["suma"],1);
}


if($totalPromedio==1)	$totalPromedio="1.0";	if($totalPromedio==2)	$totalPromedio="2.0";		if($totalPromedio==3)	$totalPromedio="3.0";	if($totalPromedio==4)	$totalPromedio="4.0";	if($totalPromedio==5)	$totalPromedio="5.0";
if($numfilasNotAreaMT>0){
	?>
<tr style="font-size:10px;">
	<td style="font-size:10px; font-weight:bold;"><?php echo $resultadoNotArea["ar_nombre"];?></td> 
	<td align="center" style="font-weight:bold; font-size:10px;"></td>
<td align="center" style="font-weight:bold;"><?php 

if($datosUsr["mat_grado"]>11){
		$notaFA = ceil($totalPromedio);
		switch($notaFA){
			case 1: echo "D"; break;
			case 2: echo "I"; break;
			case 3: echo "A"; break;
			case 4: echo "S"; break;
			case 5: echo "E"; break;
		}
		}else{
echo $totalPromedio;
		}

?></td>
 <td align="center" style="font-weight:bold;"></td>
  <td align="center" style="font-weight:bold;"></td>
</tr>
<?php

while($fila2=mysqli_fetch_array($consultaAMat, MYSQLI_BOTH)){ 
$contadorPeriodos=0;
mysqli_data_seek($consultaAMatPer,0);
//CONSULTAR NOTA POR PERIODO
while($fila3=mysqli_fetch_array($consultaAMatPer, MYSQLI_BOTH)){
if($fila2["mat_id"]==$fila3["mat_id"]){
	$contadorPeriodos++;
	$notaPeriodo=round($fila3["bol_nota"],1);
	if($notaPeriodo==1)$notaPeriodo="1.0";	if($notaPeriodo==2)$notaPeriodo="2.0";	if($notaPeriodo==3)$notaPeriodo="3.0";	if($notaPeriodo==4)$notaPeriodo="4.0";	if($notaPeriodo==5)$notaPeriodo="5.0";
	$notas[$contadorPeriodos] =$notaPeriodo;
}
}//FIN FILA3
?>
<tr style="font-size:10px;">
	<td style="font-size:10px;"><?php echo $fila2["mat_nombre"];?></td> 
	<td align="center" style="font-weight:bold; font-size:10px;"><?php echo $fila2["car_ih"];?></td>
<?php 
$totalPromedio2=round( $fila2["suma"],1);

if($totalPromedio2==1)	$totalPromedio2="1.0";	if($totalPromedio2==2)	$totalPromedio2="2.0";		if($totalPromedio2==3)	$totalPromedio2="3.0";	if($totalPromedio2==4)	$totalPromedio2="4.0";	if($totalPromedio2==5)	$totalPromedio2="5.0";
//if($totalPromedio2<$rDesempeno["desbasdesde"]){$materiasPerdidas++;}
$msj='';
if($totalPromedio2<$config[5]){
	$consultaNivelaciones = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);
	$numNiv=mysqli_num_rows($consultaNivelaciones);
	if($numNiv>0){
		$nivelaciones = mysqli_fetch_array($consultaNivelaciones, MYSQLI_BOTH);
		if($nivelaciones['niv_definitiva']<$config[5]){
			$materiasPerdidas++;
		}else{
			$totalPromedio2 = $nivelaciones['niv_definitiva'];
			$msj='Niv';
		}
	}		   
}
?>

<td align="center" style="font-weight:bold; "><?php 

			if($datosUsr["mat_grado"]>11){
		$notaFI = ceil($totalPromedio2);
		switch($notaFI){
			case 1: echo "D"; break;
			case 2: echo "I"; break;
			case 3: echo "A"; break;
			case 4: echo "S"; break;
			case 5: echo "E"; break;
		}
		}else{
			echo $totalPromedio2;
		}

?></td>
<td align="center" style="font-weight:bold;"><?php //DESEMPEÑO
while($rDesempeno=mysqli_fetch_array($consultaDesempeno, MYSQLI_BOTH)){
	if($totalPromedio2>=$rDesempeno["notip_desde"] && $totalPromedio2<=$rDesempeno["notip_hasta"]){
		if($datosUsr["mat_grado"]>11){
			$notaFD = ceil($totalPromedio2);
		switch($notaFD){
			case 1: echo "BAJO"; break;
			case 2: echo "BAJO"; break;
			case 3: echo "B&Aacute;SICO"; break;
			case 4: echo "ALTO"; break;
			case 5: echo "SUPERIOR"; break;
		}

		}else{
			
				echo $rDesempeno["notip_nombre"];
			}
		}
	}
	mysqli_data_seek($consultaDesempeno,0);
	$matmaxaus=0;
	if(!empty($fila2["matmaxaus"])){
		$matmaxaus=$fila2["matmaxaus"];
	}
 ?></td>
<td align="center" style="font-weight:bold; "><?php if(!empty($rAusencias[0]) && $rAusencias[0]>0){ echo $rAusencias[0]."/".$matmaxaus;} else{ echo "0.0/".$matmaxaus;}?></td>

<td align="center">_______________________________________</td>

</tr>
<?php
}//while fin materias
?>  
<?php 
}}//while fin areas
}}}
?>	 

    
</table>

<p>&nbsp;</p>


</div>
<?php 
$msj = "";
if($periodoActual==4 && $numfilasNotArea > 0){
	if($materiasPerdidas>=$config["conf_num_materias_perder_agno"]){
		$msj = "EL (LA) ESTUDIANTE ".$nombre." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
	}elseif($materiasPerdidas<$config["conf_num_materias_perder_agno"] and $materiasPerdidas>0){
		$msj = "EL (LA) ESTUDIANTE ".$nombre." DEBE NIVELAR LAS MATERIAS PERDIDAS";
	}else{
		$msj = "EL (LA) ESTUDIANTE ".$nombre." FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
	}

	if ($matriculadosDatos['mat_id'] == CANCELADO && $ultimoPeriodoAreas < $config["conf_periodos_maximos"]) {
		$msj = "EL(LA) ESTUDIANTE FUE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
	}
}
?>
<p align="left">
	<div style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-style:italic; font-size:10px;"><?=$msj;?></div>
</p>


<table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
	<tr>
		<td align="center">
			<?php
				$rector = UsuariosPadre::sesionUsuario($informacion_inst["info_rector"], "", $config['conf_id_institucion'], $year);
				$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
				if(!empty($rector["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $rector['uss_firma'])){
					echo '<img src="../files/fotos/'.$rector["uss_firma"].'" width="200"><br>';
				}else{
					echo '<p>&nbsp;</p>
						<p>&nbsp;</p>
						<p>&nbsp;</p>';
				}
			?>
			_________________________________<br>
			<p>&nbsp;</p>
			<?=$nombreRector?><br>
			Rector(a)
		</td>
		<td align="center">
			<?php
				$secretario = UsuariosPadre::sesionUsuario($informacion_inst["info_secretaria_academica"], "", $config['conf_id_institucion'], $year);
				$nombreScretario = UsuariosPadre::nombreCompletoDelUsuario($secretario);
				if(!empty($secretario["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $secretario['uss_firma'])){
					echo '<img src="../files/fotos/'.$secretario["uss_firma"].'" width="100"><br>';
				}else{
					echo '<p>&nbsp;</p>
						<p>&nbsp;</p>
						<p>&nbsp;</p>';
				}
			?>
			_________________________________<br>
			<p>&nbsp;</p>
			<?=$nombreScretario?><br>
			Secretario(a) Académico
		</td>
    </tr>
</table> 

</div>	

<div id="saltoPagina"></div>

<?php
	}// FIN DE TODOS LOS MATRICULADOS
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
<script type="application/javascript">
print();
</script>
</body>
</html>