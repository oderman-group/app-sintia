<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0248';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
  echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
  exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/App/Academico/boletin/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/App/Academico/Notas_tipo.php");
$estudiante = "";
if (!empty($_GET["estudiante"])) {
  $estudiante = base64_decode($_GET["estudiante"]);
}
if (!empty($_POST["estudiante"])) {
  $estudiante = $_POST["estudiante"];
}
$year = date("Y");
$cPeriodo = $config["conf_periodo"];
if (isset($_GET["periodo"])) {
  $cPeriodo = $_GET["periodo"];
}
if (isset($_POST["periodo"])) {
  $cPeriodo = $_POST["periodo"];
}
$periodoActual = $cPeriodo;

if (!empty($estudiante)) {
  $filtro = " AND mat_id='" . $estudiante . "'";
  $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
  $estudiante = $matriculadosPorCurso->fetch_assoc();
  if (!empty($estudiante)) {
    $idEstudiante = $estudiante["mat_id"];
    $grado = $estudiante["mat_grado"];
    $grupo = $estudiante["mat_grupo"];
  }
}


$tiposNotas = Notas_tipo::listarTipoDeNotas($config["conf_notas_categoria"], $year);


if (!empty($grado) && !empty($grupo) && !empty($cPeriodo) && !empty($year)) {
  $periodos = [];
  for ($i = 1; $i <= $cPeriodo; $i++) {
    $periodos[$i] = $i;
  }
  $listaEstudiantes = Academico_boletin::datosBoletin($grado, $grupo, $periodos, $year, $idEstudiante);
}

$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
?>

<head>
  <title>SINTIA - INFORME PARCIAL</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="../sintia-icono.png" />
  <link href="../css/ButomDowloadPdf.css" rel="stylesheet" type="text/css" />
  <script src="../js/ButomDowloadPdf.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
  <!-- notifications -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="font-family:Arial;">
  <?php foreach ($listaEstudiantes as $estudiante) { ?>
    <div align="center" style="margin-bottom:20px;">
      <?= $informacion_inst["info_nombre"] ?><br>
      INFORME PARCIAL - PERIODO: <?= $cPeriodo; ?><br>
      <?= $config["conf_fecha_parcial"]; ?><br>
      <?php
      $tamano = 'height="100" width="150"';
      if ($config['conf_id_institucion'] == ICOLVEN) {
        $tamano = 'width="100%"';
      }
      ?>
      <img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" <?= $tamano ?>><br>
      <?= $config["conf_descripcion_parcial"]; ?><br>
      ESTUDIANTE: <?= $estudiante["nombre"] ?></br>
    </div>

    <!-- BEGIN TABLE DATA -->
    <table width="100%" cellspacing="5" cellpadding="5" rules="all"
      style="border:solid; border-color:#6017dc; font-size:11px;">
      <tr style="font-weight:bold; height:30px; background:#6017dc; color:#FFF;">
        <th style="text-align:center;">Cod</th>
        <th style="text-align:center;">Docente</th>
        <th style="text-align:center;">Asignatura</th>
        <th style="text-align:center;">%</th>
        <th style="text-align:center;">Nota</th>
      </tr>
      <?php foreach ($estudiante["areas"] as $area) { ?>
        <?php foreach ($area["cargas"] as $carga) {
           ?>
          <tr id="data1" class="odd gradeX">
            <td style="text-align:center;"><?= $carga['car_id']; ?></td>
            <td><?= UsuariosPadre::nombreCompletoDelUsuario($carga["docente"]); ?></td>
            <td><?= $carga['mat_nombre']; ?></td>
            <td style="text-align:center;"><?= empty($carga["periodos"][$periodoActual]['bol_nota'])?'0':'100' ?>%</td>
            <td style="color:<?= Boletin::colorNota($carga["periodos"][$periodoActual]['bol_nota']); ?>; text-align:center; font-weight:bold;"><?=Boletin::formatoNota($carga["periodos"][$periodoActual]['bol_nota'], $tiposNotas); ?></td>
          </tr>
        <?php } ?>
      <?php } ?>
       <!-- END -->
    <tfoot>
      <tr style="font-weight:bold;">
        <td colspan="4" style="text-align:right;">PROMEDIO GENERAL</td>
        <td style="color:<?= Boletin::colorNota($estudiante["promedios_generales"][$periodoActual]['nota_materia_promedio']); ?>; text-align:center; font-weight:bold;"><?=Boletin::formatoNota($estudiante["promedios_generales"][$periodoActual]['nota_materia_promedio'], $tiposNotas); ?></td>
      </tr>
    </tfoot>
    </table>

    <p>&nbsp;</p>
    <div style="float:left; margin-left:20px; position:relative; max-width:200px; margin-top:-20px; font-size:12px;"
      align="center">
      _________________________<br>
      Coordinador(a) Acad&eacute;mico(a)
    </div>

    <div style="position:relative; float:right; margin-right:20px; max-width:200px; margin-top:-20px; font-size:12px;"
      align="center">
      _________________________<br>
      Director(a) De Grupo
    </div>

    <div style="position:relative; margin-top:60px; font-size:12px;" align="center">
      Yo__________________________________________________________________<br>

      Doy constancia de haber recibido del <?= $informacion_inst["info_nombre"] ?> el<br>
      informe acad&eacute;mico parcial de mi acudido y a la vez la citaci&oacute;n<br>
      respectiva para la reuni&oacute;n en donde se me informar&aacute; las causas y<br>
      recomendaciones del bajo demsempe&ntilde;o, establecidas pora la comisi&oacute;n de<br>
      evaluaci&oacute;n y promocion.
    </div>

    <div style="margin-top:10px; position:relative; font-size:12px;" align="center">
      _______________________________<br>
      Firma Del Padre Y/O Acudiente
    </div>

    <div align="center" style="margin-top:20px; font-size:12px;">En el Se&ntilde;or, pon tu confianza. Salmos 11:01</div>


    <div align="center" style="font-size:10px; margin-top:10px;">
      <img src="https://main.plataformasintia.com/app-sintia/main-app/sintia-logo-2023.png" width="150"><br>
      SINTIA - SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?= date("l, d-M-Y"); ?>
    </div>
  <?php } ?>
</body>
<?php
include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
?>

</html>