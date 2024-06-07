<?php
include("session-compartida.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
$idPaginaInterna = 'DT0221';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<head>
  <title>Pasos matrícula</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
</head>

<body style="font-family:Arial;">
<div class="container-fluid">
<?php
$filtro = '';
$busqueda='';
if(!empty($_GET["busqueda"])){
  $busqueda=$_GET["busqueda"];
  $filtro .= " AND mat_nombres LIKE '%" . $busqueda . "%' OR mat_primer_apellido LIKE '%" . $busqueda . "%' OR mat_documento LIKE '%" . $busqueda . "%'";
}
$nombreInforme =  "INFORME DE MATRÍCULAS<br>PASO A PASO";
include("../compartido/head-informes.php");
?>
  <div align="center" style="margin-bottom:20px;">
      <p class="mb-2 mt-2">
      <a href="reporte-pasos.php">VER TODO</a>&nbsp;|&nbsp;
      <a href="excel-pasos.php" target="_blank">EXPORTAR A EXCEL</a>

    </p>

      <div class="row">
        <div class="col-sm">
          <form action="reporte-pasos.php" method="GET" class="form-inline">
            <div class="form-group mx-sm-4 mb-2">
              <input type="text" class="form-control" name="busqueda" value="<?= $busqueda; ?>" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?> de estudiante...">
            </div>
            <button type="submit" class="btn btn-primary mb-2"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
          </form>
        </div>
      </div>
      
    </div>

  <table bgcolor="#FFFFFF" width="100%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?=$Plataforma->colorUno;?>;" align="center">
    <tr style="font-weight:bold; height:30px; background:<?=$Plataforma->colorUno;?>; color:#FFF;">
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
    $estadoMatricula = array("", "Matriculado", "No matriculado", "No matriculado", "No matriculado", "No matriculado");
    $cont = 1;
    $ordenado = 'mat_primer_apellido, mat_segundo_apellido ASC';
    if (!empty($_GET["orden"])) {
      $ordenado = $_GET["orden"]." DESC";
    }

    $consulta = Estudiantes::listarPasosMatricula($config, $filtro, $ordenado);
    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
      $colorProceso = 'tomato';
      if ($resultado["mat_iniciar_proceso"] == 1) {
        $colorProceso = '';
      }

      $colorFirma = '';
      if ($resultado["mat_hoja_firma"] == 1) {
        $colorFirma = 'aquamarine';
      }

      $modalidad = 0;
      if (!empty($resultado["mat_modalidad_estudio"])) {
        $modalidad = $resultado["mat_modalidad_estudio"];
      }
      if (empty($resultado["mat_iniciar_proceso"])) {
        $resultado["mat_iniciar_proceso"]=0;
      }
      if (empty($resultado["mat_actualizar_datos"])) {
        $resultado["mat_actualizar_datos"]=0;
      }
      if (empty($resultado["mat_pago_matricula"])) {
        $resultado["mat_pago_matricula"]=0;
      }
      if (empty($resultado["mat_contrato"])) {
        $resultado["mat_contrato"]=0;
      }
      if (empty($resultado["mat_pagare"])) {
        $resultado["mat_pagare"]=0;
      }
      if (empty($resultado["mat_compromiso_academico"])) {
        $resultado["mat_compromiso_academico"]=0;
      }
      if (empty($resultado["mat_compromiso_convivencia"])) {
        $resultado["mat_compromiso_convivencia"]=0;
      }
      if (empty($resultado["mat_manual"])) {
        $resultado["mat_manual"]=0;
      }
      if (empty($resultado["mat_mayores14"])) {
        $resultado["mat_mayores14"]=0;
      }
      if (empty($resultado["mat_hoja_firma"])) {
        $resultado["mat_hoja_firma"]=0;
      }
      $estadoM = 0;
      if (!empty($resultado["mat_estado_matricula"])) {
        $estadoM = $resultado["mat_estado_matricula"];
      }
    ?>
      <tr style="font-size:13px;">
        <td><?= $resultado['mat_id']; ?></td>
        <td><?= $resultado['mat_documento']; ?></td>
        <td><a href="../directivo/estudiantes-editar.php?id=<?= $resultado['mat_id']; ?>" target="_blank"><?= strtoupper($resultado['mat_primer_apellido']." ".$resultado['mat_segundo_apellido']." ".$resultado['mat_nombres']." ".$resultado['mat_nombre2']); ?></a></td>
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
        <td align="center"><?= $modalidadEstudio[$modalidad]; ?></td>
        <td align="center"><?= $estadoMatricula[$estadoM]; ?></td>
      </tr>
    <?php
      $cont++;
    } //Fin mientras que
    ?>
  </table>
  </center>
  <?php include("../compartido/footer-informes.php") ?>	
  
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</body>
<?php 
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
</html>