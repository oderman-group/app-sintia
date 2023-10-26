<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
require_once("../class/Estudiantes.php");
$filtro = '';
$busqueda='';
if(!empty($_GET["busqueda"])){
  $busqueda=$_GET["busqueda"];
  $filtro = " AND mat_nombres LIKE '%" . $busqueda . "%' OR mat_primer_apellido LIKE '%" . $busqueda . "%' OR mat_documento LIKE '%" . $busqueda . "%'";
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<head>
  <title>Observador</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>

<body style="font-family:Arial;">
  <?php
  $nombreInforme = "REPORTE OBSERVADOR" . "<br><p class=" . "mb-2 mt-2" . ">" . "<a href=" . "reporte-ver-observador.php" . ">VER TODO</a>" . "</p>";
  include("../compartido/head-informes.php") ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm">
        <form action="reporte-ver-observador.php" method="GET" class="form-inline">
          <div class="form-group mx-sm-4 mb-2">
            <input type="text" class="form-control" name="busqueda" value="<?= $busqueda; ?>" placeholder="Búsqueda de estudiante...">
          </div>
          <button type="submit" class="btn btn-primary mb-2">Buscar</button>
        </form>
      </div>
    </div>

    <table width="100%" cellspacing="5" cellpadding="5" rules="all" style="
  border:solid; 
  border-color:<?= $Plataforma->colorUno; ?>; 
  font-size:11px;
  ">
      <tr style="font-weight:bold; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
        <th>ID</th>
        <th>Documento</th>
        <th>Estudiante</th>
        <th>Grado</th>
        <th>Periodo</th>
        <th>Ultima vista</th>
        <th>Firmado</th>
        <th>Fecha firmado</th>
      </tr>
      <?php
      $cont = 1;
      $ordenado = 'mat_primer_apellido, mat_segundo_apellido ASC';
      if (!empty($_GET["orden"])) {
        $ordenado = $_GET["orden"] . " DESC";
      }

      $consulta = Estudiantes::listarEstudiantes(0, $filtro, '');
      while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
        $colorProceso = 'tomato';
        if (!empty($resultado["dn_aprobado"]) && $resultado["dn_aprobado"] == 1) {
          $colorProceso = '';
        }
      ?>
       <tr style="border-color:<?=$Plataforma->colorDos;?>;">

          <td><?= $resultado['mat_id']; ?></td>
          <td><?= $resultado[12]; ?></td>
          <td><a href="../directivo/estudiantes-editar.php?id=<?= $resultado[0]; ?>" target="_blank"><?= strtoupper($resultado['mat_primer_apellido'] . " " . $resultado['mat_segundo_apellido'] . " " . $resultado['mat_nombres'] . " " . $resultado['mat_nombre2']); ?></a></td>
          <td><?= $resultado["gra_nombre"]; ?></td>
          <td style="text-align:center;"><?php if(!empty($resultado["dn_periodo"])) echo $resultado["dn_periodo"]; ?></td>
          <td align="center"><?php if(!empty($resultado["dn_ultima_lectura"])) echo $resultado["dn_ultima_lectura"]; ?></td>
          <td align="center" style="background-color: <?= $colorProceso; ?> ;"><?php  if(!empty($resultado["dn_aprobado"])) echo $resultado["dn_aprobado"]; ?></td>
          <td align="center"><?php  if(!empty($resultado["dn_fecha_aprobado"])) echo $resultado["dn_fecha_aprobado"]; ?></td>
        </tr>
      <?php
        $cont++;
      } //Fin mientras que
      ?>
    </table>

    <?php include("../compartido/footer-informes.php") ?>;

  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</body>

</html>