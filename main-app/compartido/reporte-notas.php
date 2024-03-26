<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0238';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
$carga='';
$grado='';
$grupo='';
$periodo='';
if(!empty($_GET['carga'])){
	$carga=base64_decode($_GET['carga']);
	$grado=base64_decode($_GET['grado']);
	$grupo=base64_decode($_GET['grupo']);
	$periodo=base64_decode($_GET['per']);
}
if(!empty($_POST['carga'])){
	$carga=$_POST['carga'];
	$grado=$_POST['grado'];
	$grupo=$_POST['grupo'];
	$periodo=$_POST['per'];
}
require_once("../class/servicios/GradoServicios.php");
?>
<head>
	<title>Notas</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial;">
<?php
$nombreInforme = "INFORME DE NOTAS - PERIODO:";
include("../compartido/head-informes.php") ?>
  <table width="100%" border="1" style=" border:solid;border-color:<?=$Plataforma->colorUno;?>;" rules="all" align="center">    
			<tr style="font-weight:bold; height:30px; background:<?=$Plataforma->colorUno;?>; color:#FFF;">
        <th>Cod</th>
        <th>Des</th>
        <th>Fecha</th>
        <th>Valor</th>
        <th>Indicador</th>
        <th>Estado</th>
        <th>#ET/#EC.</th>
  </tr>
  <?php
  //ESTUDIANTES ACTUALES
  $filtroAdicional= "AND mat_grado='".$grado."' AND mat_grupo='".$grupo."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
  $cursoActual=GradoServicios::consultarCurso($grado);
  $consultaNumEstudiantes =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$cursoActual,$grupo);
	$numEstudiantes = mysqli_num_rows($consultaNumEstudiantes);
  $cont=1;
  $consulta = Actividades::consultaActividadesCarga($config, $carga, $periodo);
  while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
  $consultaInd=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores WHERE ind_id='".$resultado['act_id_tipo']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	$ind = mysqli_fetch_array($consultaInd, MYSQLI_BOTH);
	if($resultado['act_registrada']==1) $estado = "REGISTRADA"; else $estado = "PENDIENTE";
  $consultaNumCalificados=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_calificaciones WHERE cal_id_actividad='".$resultado['act_id']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	$numCalificados = mysqli_num_rows($consultaNumCalificados);
	if($numEstudiantes!=$numCalificados) $bg = '#FCC'; else $bg = '#FFF';
  ?>
  <tr style="text-transform: uppercase; border-color: <?=$Plataforma->colorDos;?>">
      <td align="center"><?=$resultado['act_id'];?></td>
      <td><?=$resultado['act_descripcion'];?></td>
      <td align="center"><?=$resultado['act_fecha'];?></td>
      <td align="center"><?=$resultado['act_valor'];?>%</td>
      <td><?=$ind['ind_nombre'];?></td>
      <td><?=$estado;?></td> 
      <td style="text-align:center; background:<?=$bg;?>"><?=$numEstudiantes;?>/<?=$numCalificados;?></td>  
</tr>
  <?php
  $cont++;
  }//Fin mientras que
  ?>
  </table>
  <?php include("../compartido/footer-informes.php");
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
</body>
</html>


