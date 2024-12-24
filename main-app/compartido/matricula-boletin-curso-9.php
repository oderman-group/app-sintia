<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

$year=$_SESSION["bd"];
if(isset($_GET["year"])){
$year=base64_decode($_GET["year"]);
}

$tamañoLogo = $_SESSION['idInstitucion'] == ICOLVEN ? 100 : 50;

$modulo = 1;
if(empty($_GET["periodo"])){
	$periodoActual = 1;
}else{
	$periodoActual = base64_decode($_GET["periodo"]);
}
//$periodoActual=2;
if($periodoActual==1) $periodoActuales = "Primero";
if($periodoActual==2) $periodoActuales = "Segundo";
if($periodoActual==3) $periodoActuales = "Tercero";
if($periodoActual==4) $periodoActuales = "Final";?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<?php
$filtro = '';
if (!empty($_GET["id"])) {
    $filtro .= " AND mat_id='" . base64_decode($_GET["id"]) . "'";
}
if (!empty($_REQUEST["curso"])) {
    $filtro .= " AND mat_grado='" . base64_decode($_REQUEST["curso"]) . "'";
}
if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
Utilidades::validarInfoBoletin($matriculadosPorCurso);
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
//contador materias
$cont_periodos=0;
$contador_indicadores=0;
$materiasPerdidas=0;
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
$datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
$nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);
$num_usr=mysqli_num_rows($usr);
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
	<img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="<?=$tamañoLogo?>%"><br>
    <!-- <?=$informacion_inst["info_nombre"]?><br>
    BOLETÍN DE CALIFICACIONES<br> -->
</div> 

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:12px;">
    <tr>
    	<td>C&oacute;digo: <b><?=$datosUsr["mat_matricula"];?></b></td>
        <td>Nombre: <b><?=$nombre?></b></td>   
    </tr>
    
    <tr>
    	<td>Grado: <b><?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></b></td>
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
	$cargasConsulta = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
	$i=1;
	while($cargas = mysqli_fetch_array($cargasConsulta, MYSQLI_BOTH)){
		//DIRECTOR DE GRUPO
		if($cargas["car_director_grupo"]==1){
			$idDirector=$cargas["car_docente"];
		}
		$indicadores = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargas['car_id'], $periodoActual, $year);
		
		$observacion = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $datosUsr['mat_id'], $cargas['car_id'], $year);
		
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
				<?=$observacion['bol_observaciones_boletin'] ?? "";?>
			</p>
		</td>
		<td width="2%" align="center"><?=$cargas['car_ih'];?></td>
	</tr>
	<?php $i++;}?>
</table>
	<p>&nbsp;</p>
<?php 
$cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
WHERE dn_cod_estudiante='".$matriculadosDatos['mat_id']."' AND dn_periodo<='".$periodoActual."' AND institucion={$config['conf_id_institucion']} AND year={$year}
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
        <td>Observaciones</td>
    </tr>
<?php while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){
?>
    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">
        <td><?=$rndisiplina["dn_periodo"]?></td>
        <td align="left"><?=$rndisiplina["dn_observacion"]?></td>
    </tr>
<?php }?>
</table>
<?php }?>
	

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>   
<!--******FIRMAS******-->   

<table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
	<tr>
		<td align="center">
			<?php
				$directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
				$nombreDirectorGrupo = UsuariosPadre::nombreCompletoDelUsuario($directorGrupo);
				if(!empty($directorGrupo["uss_firma"])){
					echo '<img src="../files/fotos/'.$directorGrupo["uss_firma"].'" width="100"><br>';
				}else{
					echo '<p>&nbsp;</p>
						<p>&nbsp;</p>
						<p>&nbsp;</p>';
				}
			?>
			<p style="height:0px;"></p>_________________________________<br>
			<p>&nbsp;</p>
			<?=$nombreDirectorGrupo?><br>
			Director(a) de grupo
		</td>
		<td align="center">
			<?php
				$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
				$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
				if(!empty($rector["uss_firma"])){
					echo '<img src="../files/fotos/'.$rector["uss_firma"].'" width="100"><br>';
				}else{
					echo '<p>&nbsp;</p>
						<p>&nbsp;</p>
						<p>&nbsp;</p>';
				}
			?>
			<p style="height:0px;"></p>_________________________________<br>
			<p>&nbsp;</p>
			<?=$nombreRector?><br>
			Rector(a)
		</td>
	</tr>
</table>
		
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
