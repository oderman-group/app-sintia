<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0229';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
  echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
  exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
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

$grado = '';

$idEstudiante = '';
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

if (isset($_POST["curso"])) {
  $grado = $_POST["curso"];
}
if (isset($_POST["grupo"])) {
  $grupo = $_POST["grupo"];
}

$tiposNotas = Notas_tipo::listarTipoDeNotas($config["conf_notas_categoria"], $year);


if (!empty($grado) && !empty($grupo) && !empty($cPeriodo) && !empty($year)) {
  $periodos = [];
  for ($i = 1; $i <= $cPeriodo; $i++) {
    $periodos[$i] = $i;
  }
  $andString=[
    "AND" => "bol_nota < ".$config['conf_nota_minima_aprobar']." AND bol_periodo =".$cPeriodo
  ];
  $listaEstudiantes = Academico_boletin::datosBoletin($grado, $grupo, $periodos, $year, $idEstudiante,false,$andString);
}
?>

<head>
  <title>SINTIA - INFORME PARCIAL</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>

<body style="font-family:Arial;">
  <?php
  $nombreInforme = "INFORME PARCIAL MATERIAS PERDIDAS" . "<br>" . " PERIODO:" . Utilidades::getToString($config[2]) . "<br>" . Utilidades::getToString($config["conf_fecha_parcial"]);
  include("../compartido/head-informes.php") ?>


  <?php foreach ($listaEstudiantes as $estudiante) { ?>
    <div align="center" style="margin-bottom:20px;">
      ESTUDIANTE: <?= $estudiante["nombre"] ?></br>
    </div>  
    <!-- BEGIN TABLE DATA -->
  <table width="100%" cellspacing="2" cellpadding="2" rules="all"
    style="border:solid; border-color:<?= $Plataforma->colorUno; ?>; font-size:10px;">
    <tr style="font-weight:bold; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
      <th style="text-align:center;">Cod</th>
      <th style="text-align:center;">Docente</th>
      <th style="text-align:center;">Asignatura</th>
      <th style="text-align:center;">%</th>
      <th style="text-align:center;">Nota</th>
    </tr>
    <!-- END -->
    <!-- BEGIN -->
    <tbody>


      <?php foreach ($estudiante["areas"] as $area) { ?>
        <?php foreach ($area["cargas"] as $carga) {
          ?>
          <tr id="data1" class="odd gradeX">
            <td style="text-align:center;"><?= $carga['car_id']; ?></td>
            <td><?= UsuariosPadre::nombreCompletoDelUsuario($carga["docente"]); ?></td>
            <td><?= $carga['mat_nombre']; ?></td>
            <td style="text-align:center;"><?= empty($carga["periodos"][$periodoActual]['bol_nota']) ? '0' : '100' ?>%</td>
            <td
              style="color:<?= Boletin::colorNota($carga["periodos"][$periodoActual]['bol_nota']); ?>; text-align:center; font-weight:bold;">
              <?= Boletin::formatoNota($carga["periodos"][$periodoActual]['bol_nota'], $tiposNotas); ?></td>
          </tr>
        <?php } ?>
      <?php } ?>

    </tbody>
  </table>

  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <?php } ?>

  <?php
  include("../compartido/footer-informes.php");
  include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php"); ?>
</body>

</html>