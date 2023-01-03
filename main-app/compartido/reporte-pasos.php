<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<head>
  <title>Pasos matrícula</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="../files/images/ico.png">
</head>

<body style="font-family:Arial;">
<div class="container-fluid">
  <div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" height="150" width="250"><br>
    <?= $informacion_inst["info_nombre"] ?><br>
    INFORME DE MATRÍCULAS PASO A PASO</br>

    

    
    <p class="mb-2 mt-2">
      <a href="reporte-pasos.php">VER TODO</a>&nbsp;|&nbsp;
      <a href="excel-pasos.php" target="_blank">EXPORTAR A EXCEL</a>

    </p>

      <div class="row">
        <div class="col-sm">
          <form action="reporte-pasos.php" method="GET" class="form-inline">
            <div class="form-group mx-sm-4 mb-2">
              <input type="text" class="form-control" name="busqueda" value="<?= $_GET["busqueda"]; ?>" placeholder="Búsqueda de estudiante...">
            </div>
            <button type="submit" class="btn btn-primary mb-2">Buscar</button>
          </form>
        </div>
      </div>
      
    </div>

  <table bgcolor="#FFFFFF" width="100%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
    <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
      <th>ID</th>
      <th>Documento</th>
      <th>Estudiante</th>
      <th>Grado</th>
      <th>Proceso</th>
      <th>A. Datos</th>
      <th><a href="reporte-pasos.php?orden=mat_pago_matricula" style="color: black; text-decoration: underline;">Pago M.</a></th>
      <th>Contrato</th>
      <th>Pagaré</th>
      <th>C. Académico</th>
      <th>C. Convivencia</th>
      <th>Manual</th>
      <th>Mayores de 14</th>
      <th><a href="reporte-pasos.php?orden=mat_hoja_firma" style="color: black; text-decoration: underline;">Firma</a></th>
      <th><a href="reporte-pasos.php?orden=mat_modalidad_estudio" style="color: black; text-decoration: underline;">Modalidad</a></th>
      <th>Estado</th>
    </tr>
    <?php
    $iniciaProceso = array("NO", "SI");
    $estadoProceso = array("Pendiente", "Listo");
    $modalidadEstudio = array("", "Virtual", "Presencial- alternancia");
    $estadoMatricula = array("", "Matriculado", "No matriculado", "No matriculado", "No matriculado");
    $cont = 1;
    $filtro = '';
    if ($_GET["busqueda"] != "") {
      $filtro = " AND mat_nombres LIKE '%" . $_GET["busqueda"] . "%' OR mat_primer_apellido LIKE '%" . $_GET["busqueda"] . "%' OR mat_documento LIKE '%" . $_GET["busqueda"] . "%'";
    }

    $ordenado = 'mat_primer_apellido, mat_segundo_apellido ASC';
    if ($_GET["orden"] != "") {
      $ordenado = $_GET["orden"]." DESC";
    }

    $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
  LEFT JOIN academico_grados ON gra_id=mat_grado
  WHERE  mat_eliminado=0 $filtro
  ORDER BY $ordenado");
    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
      $colorProceso = 'tomato';
      if ($resultado["mat_iniciar_proceso"] == 1) {
        $colorProceso = '';
      }

      $colorFirma = '';
      if ($resultado["mat_hoja_firma"] == 1) {
        $colorFirma = 'aquamarine';
      }
    ?>
      <tr style="font-size:13px;">
        <td><?= $resultado['mat_id']; ?></td>
        <td><?= $resultado[12]; ?></td>
        <td><a href="../directivo/estudiantes-editar.php?id=<?= $resultado[0]; ?>" target="_blank"><?= strtoupper($resultado['mat_primer_apellido']." ".$resultado['mat_segundo_apellido']." ".$resultado['mat_nombres']." ".$resultado['mat_nombre2']); ?></a></td>
        <td><?= $resultado["gra_nombre"]; ?></td>
        <td align="center" style="background-color: <?= $colorProceso; ?> ;"><?= $iniciaProceso[$resultado["mat_iniciar_proceso"]]; ?></td>
        <td align="center"><?= $estadoProceso[$resultado["mat_actualizar_datos"]]; ?></td>
        <td align="center">
          <?= $estadoProceso[$resultado["mat_pago_matricula"]]; ?>
          <?php if ($resultado["mat_soporte_pago"] != "") { ?>
            <br> <a href="https://plataformasintia.com/main-app/v2.0/files/comprobantes/<?= $resultado["mat_soporte_pago"]; ?>" target="_blank" style="color:blue;">Soporte</a>
          <?php } ?>
        </td>
        <td align="center"><?= $estadoProceso[$resultado["mat_contrato"]];?></td>
        <td align="center"><?= $estadoProceso[$resultado["mat_pagare"]]; ?></td>
        <td align="center"><?= $estadoProceso[$resultado["mat_compromiso_academico"]]; ?></td>
        <td align="center"><?= $estadoProceso[$resultado["mat_compromiso_convivencia"]]; ?></td>
        <td align="center"><?= $estadoProceso[$resultado["mat_manual"]]; ?></td>
        <td align="center"><?= $estadoProceso[$resultado["mat_mayores14"]]; ?></td>
        <td align="center" style="background-color: <?= $colorFirma; ?> ;">
          <?= $estadoProceso[$resultado["mat_hoja_firma"]]; ?>
          <?php if ($resultado["mat_hoja_firma"] == 1 and $resultado["mat_firma_adjunta"] != "") { ?>
            <br> <a href="https://plataformasintia.com/main-app/v2.0/files/comprobantes/<?= $resultado["mat_firma_adjunta"]; ?>" target="_blank" style="color:blue;">Firma</a>
          <?php } ?>
        </td>
        <td align="center"><?= $modalidadEstudio[$resultado["mat_modalidad_estudio"]]; ?></td>
        <td align="center"><?= $estadoMatricula[$resultado["mat_estado_matricula"]]; ?></td>
      </tr>
    <?php
      $cont++;
    } //Fin mientras que
    ?>
  </table>
  </center>
  <div align="center" style="font-size:10px; margin-top:10px;">
    <img src="../files/images/sintia.png" height="50" width="100"><br>
    SINTIA - SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?= date("l, d-M-Y"); ?>
  </div>

  
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</body>

</html>