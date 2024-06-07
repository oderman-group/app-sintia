<?php
include("session-compartida.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$idPaginaInterna = 'DT0146';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
?>
<!doctype html>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Informes SINTIA</title>
<link rel="shortcut icon" href="../files/images/ico.png">
</head>

<body style="font-family:Arial; font-size: 13px;">
  <div align="center" style="margin-bottom:20px; margin-top: 20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="100" width="200"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    CARGAS REPETIDAS
    </br>
  </div>

  <div style="margin: 20px;">
    <table width="100%" border="1" rules="all" align="center" style="border-color:#6017dc;">
      <tr
        style="font-weight:bold; font-size:12px; height:30px; text-align: center; text-transform: uppercase; background:#6017dc; color:#FFF;">
        <td>No</td>
        <td>Docente</td>
        <td>Curso</td>
        <td>Grupo</td>
        <td>Asignatura</td>
        <td>Cantidad (ID Cargas)</td>
      </tr>
      <?php
			$i=1;
      $consulta = CargaAcademica::consultaCargasRepetidas($config);
			while($datos = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
       
			?>
      <tr>
        <td align="center"><?=$i;?></td>
        <td><?=$datos['uss_nombre'];?></td>
        <td align="center"><?=$datos['gra_nombre'];?></td>
        <td align="center"><?=$datos['gru_nombre'];?></td>
        <td><?=$datos['mat_nombre'];?></td>
        <td> <?=$datos['duplicados'];?> (<?=$datos['car_id'];?>)</td>
      </tr>
      <?php	
				$i++;
			}
			?>
    </table>
  </div>
  <div style="font-size:10px; margin-top:10px; text-align:center;">
    <img src="https://main.plataformasintia.com/app-sintia/main-app/sintia-logo-2023.png" width="150"><br>
    PLATAFORMA EDUCATIVA SINTIA - <?=date("l, d-M-Y");?>
  </div>
</body>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
</html>