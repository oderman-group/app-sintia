<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0223';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/compartido/head.php");
?>
<!doctype html>
<html>

<head></head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Informes SINTIA</title>
<link rel="shortcut icon" href="../../files/images/ico.png">
</head>

<body style="font-family:Arial; font-size: 13px;">
  <div align="center" style="margin-bottom:20px; margin-top: 20px;">
    <img src="../../files/images/logo/<?= $informacion_inst["info_logo"] ?>" width="200"><br>
    <div>&nbsp;</div>
    <?= $informacion_inst["info_nombre"] ?><br>
    <b>MATRICULAS REPETIDAS</b>
    </br>
  </div>

  <div style="margin: 20px;">
    <table width="100%" border="1" rules="all" align="center" style="border-color:#6017dc;">
      <tr style="font-weight:bold; font-size:12px; height:30px; text-align: center; text-transform: uppercase; background:#6017dc; color:#FFF;">
        <td>Nº</td>
        <td>Documento</td>
        <td>Nombre</td>
        <td>Cantidad (IDs)</td>
        <td>Nº Matriculas</td>
        <td>Grados</td>
      </tr>
      <?php
        $i = 1;
        $consulta = Estudiantes::listarMatriculasRepetidas($config);
        while ($datos = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
          $nombreCompleto=Estudiantes::NombreCompletoDelEstudiante($datos);
      ?>
        <tr>
          <td align="center"><?= $i; ?></td>
          <td><?= $datos['mat_documento']; ?></td>
          <td><?= $nombreCompleto; ?></td>
          <td><?= $datos['duplicados']; ?> (<?= $datos['mat_id']; ?>)</td>
          <td>(<?= $datos['mat_matricula']; ?>)</td>
          <td>(<?= $datos['gra_nombre']; ?>)</td>
        </tr>
      <?php
          $i++;
        }
      ?>
    </table>
  </div>
  <div style="font-size:10px; margin-top:10px; text-align:center;">
    <img src="https://main.plataformasintia.com/app-sintia/main-app/sintia-logo-2023.png" width="150"><br>
    PLATAFORMA EDUCATIVA SINTIA - <?= date("l, d-M-Y"); ?>
  </div>
</body>
<?php 
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
</html>