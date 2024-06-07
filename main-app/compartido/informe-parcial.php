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
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
$estudiante = "";
if (!empty($_GET["estudiante"])) {
  $estudiante = base64_decode($_GET["estudiante"]);
}
if (!empty($_POST["estudiante"])) {
  $estudiante = $_POST["estudiante"];
}
$year = date("Y");
$cPeriodo = $config[2];
if (isset($_GET["periodo"])) {
  $cPeriodo = $_GET["periodo"];
}
if (isset($_POST["periodo"])) {
  $cPeriodo = $_POST["periodo"];
}
?>

<head>
  <title>SINTIA - INFORME PARCIAL</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="../sintia-icono.png" />
</head>

<body style="font-family:Arial;">
  <div align="center" style="margin-bottom:20px;">

    <?php
    //ESTUDIANTE ACTUAL
    $datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($estudiante);
    ?>

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
    ESTUDIANTE: <?= Estudiantes::NombreCompletoDelEstudiante($datosEstudianteActual); ?></br>
  </div>




  <!-- BEGIN TABLE DATA -->
  <table width="100%" cellspacing="5" cellpadding="5" rules="all" style="border:solid; border-color:#6017dc; font-size:11px;">
    <tr style="font-weight:bold; height:30px; background:#6017dc; color:#FFF;">
      <th style="text-align:center;">Cod</th>
      <th style="text-align:center;">Docente</th>
      <th style="text-align:center;">Asignatura</th>
      <th style="text-align:center;">%</th>
      <th style="text-align:center;">Nota</th>
    </tr>
    <!-- END -->
    <!-- BEGIN -->
    <tbody>
      <?php
      $cCargas = CargaAcademica::consultaInformeParcialTodas($config, $datosEstudianteActual['mat_id'], $datosEstudianteActual['mat_grado'], $datosEstudianteActual['mat_grupo']);
      $nCargas = mysqli_num_rows($cCargas);
      $materiasDividir = 0;
      $promedioG = 0;
      while ($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)) {
        //COLOR DE LA BARRA Y DE LAS DEFINITIVAS
        if($rCargas['nota'] < $config['conf_nota_minima_aprobar']){
          $colorDefinitiva =  $config['conf_color_perdida'];
        }	
        if($rCargas['nota'] == $config['conf_nota_minima_aprobar']){
          $colorDefinitiva =  $config['conf_color_ganada'];
        }	
        if($rCargas['nota'] > $config['conf_nota_minima_aprobar']){	
          $colorDefinitiva =  $config['conf_color_ganada'];
        }
        //SOLO SE CUENTAN LAS MATERIAS QUE TIENEN NOTAS.
        if ($rCargas['porcentaje'] > 0 && $rCargas['mat_sumar_promedio'] == SI) {
          $materiasDividir++;
        }

        $definitivaFinal = $rCargas['nota'];
        if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
          $estiloNotaDefinitiva = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $rCargas['nota']);
          $definitivaFinal = !empty($estiloNotaDefinitiva['notip_nombre']) ? $estiloNotaDefinitiva['notip_nombre'] : "";
        }
      ?>
        <tr id="data1" class="odd gradeX">
          <td style="text-align:center;"><?= $rCargas['car_id']; ?></td>
          <td><?= UsuariosPadre::nombreCompletoDelUsuario($rCargas); ?></td>
          <td><?= $rCargas['mat_nombre']; ?></td>
          <td style="text-align:center;"><?= $rCargas['porcentaje']; ?>%</td>
          <td style="color:<?= $colorDefinitiva; ?>; text-align:center; font-weight:bold;"><?= $definitivaFinal; ?></td>
        </tr>
        <?php
        if ($rCargas['mat_sumar_promedio'] == SI) {
          $promedioG += $rCargas['nota'];
        }
      }
      if ($materiasDividir > 0) {
        $promedioG = round(($promedioG / $materiasDividir), 1);
      }
      //MEDIA TECNICA
      if (array_key_exists(10, $_SESSION["modulos"])) {
        $consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config, $year, $estudiante);
        while ($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)) {
          if (!empty($datosEstudianteActualMT)) {
            $cCargas = CargaAcademica::consultaInformeParcialTodas($config, $datosEstudianteActualMT['mat_id'], $datosEstudianteActualMT['mat_grado'], $datosEstudianteActualMT['mat_grupo']);
            $nCargas = mysqli_num_rows($cCargas);
            while ($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)) {
              //COLOR DE LA BARRA Y DE LAS DEFINITIVAS
              if($rCargas['nota'] < $config['conf_nota_minima_aprobar']){
                $colorDefinitiva =  $config['conf_color_perdida'];
              }	
              if($rCargas['nota'] == $config['conf_nota_minima_aprobar']){
                $colorDefinitiva =  $config['conf_color_ganada'];
              }	
              if($rCargas['nota'] > $config['conf_nota_minima_aprobar']){	
                $colorDefinitiva =  $config['conf_color_ganada'];
              }
              $definitivaFinal = $rCargas['nota'];
              if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
                $estiloNotaDefinitiva = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $rCargas['nota']);
                $definitivaFinal = !empty($estiloNotaDefinitiva['notip_nombre']) ? $estiloNotaDefinitiva['notip_nombre'] : "";
              }
        ?>
              <tr id="data1" class="odd gradeX">
                <td style="text-align:center;"><?= $rCargas['car_id']; ?></td>
                <td><?= UsuariosPadre::nombreCompletoDelUsuario($rCargas); ?></td>
                <td><?= $rCargas['mat_nombre']; ?></td>
                <td style="text-align:center;"><?= $rCargas['porcentaje']; ?>%</td>
                <td style="color:<?= $colorDefinitiva; ?>; text-align:center; font-weight:bold;"><?= $definitivaFinal; ?></td>
              </tr>
      <?php
            }
          }
        }
      }
      $promedioGFinal = $promedioG;
      if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
        $estiloNotaPromedioG = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioG);
        $promedioGFinal = !empty($estiloNotaPromedioG['notip_nombre']) ? $estiloNotaPromedioG['notip_nombre'] : "";
      }
      ?>
    </tbody>
    <!-- END -->
    <tfoot>
      <tr style="font-weight:bold;">
        <td colspan="4" style="text-align:right;">PROMEDIO GENERAL</td>
        <td style="text-align:center;"><?= $promedioGFinal; ?></td>
      </tr>
    </tfoot>
  </table>


  <p>&nbsp;</p>
  <div style="float:left; margin-left:20px; position:relative; max-width:200px; margin-top:-20px; font-size:12px;" align="center">
    _________________________<br>
    Coordinador(a) Acad&eacute;mico(a)
  </div>

  <div style="position:relative; float:right; margin-right:20px; max-width:200px; margin-top:-20px; font-size:12px;" align="center">
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

</body>
<?php
include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
?>

</html>