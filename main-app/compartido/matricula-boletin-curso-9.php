<?php include("../directivo/session.php");?>
<?php include("../../config-general/config.php");?>
<?php
$modulo = 1;
if($_GET["periodo"]==""){
	$periodoActual = 1;
}else{
	$periodoActual = $_GET["periodo"];
}
//$periodoActual=2;
if($periodoActual==1) $periodoActuales = "Primero";
if($periodoActual==2) $periodoActuales = "Segundo";
if($periodoActual==3) $periodoActuales = "Tercero";
if($periodoActual==4) $periodoActuales = "Final";?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<?php
if (is_numeric($_GET["id"])) {
    $filtro .= " AND mat_id='" . $_GET["id"] . "'";
}
if (is_numeric($_REQUEST["curso"])) {
    $filtro .= " AND mat_grado='" . $_REQUEST["curso"] . "'";
}

$matriculadosPorCurso = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
WHERE mat_eliminado=0 AND mat_estado_matricula=1 $filtro 
GROUP BY mat_id
ORDER BY mat_grupo, mat_primer_apellido");
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
//contador materias
$cont_periodos=0;
$contador_indicadores=0;
$materiasPerdidas=0;
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr=mysqli_query($conexion, "SELECT * FROM academico_matriculas am
INNER JOIN academico_grupos ON mat_grupo=gru_id
INNER JOIN academico_grados ON mat_grado=gra_id WHERE mat_id=".$matriculadosDatos[0]);
$num_usr=mysqli_num_rows($usr);
$datos_usr=mysqli_fetch_array($usr, MYSQLI_BOTH);
if($num_usr==0)
{
?>
	<script type="text/javascript">
		window.close();
	</script>
<?php
	exit();
}

$contador_periodos=0;
?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
	<title>Boletín Preescolar</title>
<style>
#saltoPagina
{
	PAGE-BREAK-AFTER: always;
}
</style>
</head>

<body style="font-family:Arial;">

<div align="center" style="margin-bottom:20px;">
    <img src="enca.png"><br>
    <!--<?=$informacion_inst["info_nombre"]?><br>
    BOLET&Iacute;N DE CALIFICACIONES<br>-->
</div> 

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:12px;">
    <tr>
    	<td>C&oacute;digo: <b><?=$datos_usr["mat_matricula"];?></b></td>
        <td>Nombre: <b><?=strtoupper($datos_usr[3]." ".$datos_usr[4]." ".$datos_usr["mat_nombres"]);?></b></td>   
    </tr>
    
    <tr>
    	<td>Grado: <b><?=$datos_usr["gra_nombre"]." ".$datos_usr["gru_nombre"];?></b></td>
        <td>Periodo: <b><?=strtoupper($periodoActuales);?></b></td>    
    </tr>
</table>
<br>
<table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
	<tr style="font-weight:bold; background:#4c9858; border-color:#000; height:20px; color:#000; font-size:12px;">
		<td width="1%" align="center">No.</td>
		<td width="92%" align="center">DIMENSIONES</td>
		<td width="2%" align="center">I.H</td>
	</tr>
	
	<?php
	$cargasConsulta = mysqli_query($conexion, "SELECT * FROM academico_cargas
	INNER JOIN academico_materias ON mat_id=car_materia
	WHERE car_curso='".$datos_usr["mat_grado"]."' AND car_grupo='".$datos_usr["mat_grupo"]."'");
	$i=1;
	while($cargas = mysqli_fetch_array($cargasConsulta, MYSQLI_BOTH)){
		$indicadores = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga
		INNER JOIN academico_indicadores ON ind_id=ipc_indicador
		WHERE ipc_carga='".$cargas['car_id']."' AND ipc_periodo='".$_GET["periodo"]."'
		");
		
		$consultaObservacion=mysqli_query($conexion, "SELECT * FROM academico_boletin
		WHERE bol_carga='".$cargas['car_id']."' AND bol_periodo='".$_GET["periodo"]."' AND bol_estudiante='".$datos_usr["mat_id"]."'");
		$observacion = mysqli_fetch_array($consultaObservacion, MYSQLI_BOTH);
		
		$colorFondo = '#FFF;';
		if($i%2==0){$colorFondo = '#e0e0153b';}
	?>
	<tr style="background-color: <?=$colorFondo;?>">
		<td width="1%" align="center"><?=$i;?></td>
		<td width="92%">
			<b><?=$cargas['mat_nombre'];?></b><br>
			<?php
			while($ind = mysqli_fetch_array($indicadores, MYSQLI_BOTH)){
				echo "- ".$ind['ind_nombre']."<br>";
			}
			?>
			<hr>
			<h5 align="center">Observaciones</h5>
			<p style="margin-left: 5px;">
				<?=$observacion['bol_observaciones_boletin'];?>
			</p>
		</td>
		<td width="2%" align="center"><?=$cargas['car_ih'];?></td>
	</tr>
	<?php $i++;}?>
</table>
	<p>&nbsp;</p>
<?php 
$cndisiplina = mysqli_query($conexion, "SELECT * FROM disiplina_nota 
WHERE dn_cod_estudiante='".$matriculadosDatos[0]."' AND dn_periodo<='".$_GET["periodo"]."'
GROUP BY dn_cod_estudiante, dn_periodo
ORDER BY dn_id
");
if(@mysqli_num_rows($cndisiplina)>0){
?>
<table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">

    <tr style="font-weight:bold; background:#4c9858; border-color:#036; height:40px; font-size:12px; text-align:center">
    	<td colspan="3">OBSERVACIONES DE CONVIVENCIA</td>
    </tr>
    
    <tr style="font-weight:bold; background:#e0e0153b; height:25px; font-size:12px; text-align:center">
        <td width="8%">Periodo</td>
        <!--<td width="8%">Nota</td>-->
        <td>Observaciones</td>
    </tr>
<?php while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){
$consultaDesempenoND=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos WHERE notip_categoria='".$config[22]."' AND ".$rndisiplina["dn_nota"].">=notip_desde AND ".$rndisiplina["dn_nota"]."<=notip_hasta");
$desempenoND = mysqli_fetch_array($consultaDesempenoND, MYSQLI_BOTH);
?>
    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">
        <td><?=$rndisiplina["dn_periodo"]?></td>
        <!--<td><?=$desempenoND[1]?></td>-->
        <td align="left"><?="[".$rndisiplina["dn_id"]."] ".$rndisiplina["dn_observacion"]?></td>
    </tr>
<?php }?>
</table>
<?php }?>
	
	<p>&nbsp;</p>
	<div align="center"><img src="../files/firmas/firmalucy.jpeg" height="120"></div>
		
 <div id="saltoPagina"></div>
                                    
<?php
 }// FIN DE TODOS LOS MATRICULADOS
?>
<script type="application/javascript">
print();
</script>                                    
                          
</body>
</html>
