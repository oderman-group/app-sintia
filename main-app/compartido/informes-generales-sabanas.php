<?php
include("session-compartida.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/servicios/GradoServicios.php");

$curso = '';
if (!empty($_GET["curso"])) {
  $curso = base64_decode($_GET["curso"]);
}
$grupo = '';
if (!empty($_GET["grupo"])) {
  $grupo = base64_decode($_GET["grupo"]);
}
$per = '';
if (!empty($_GET["per"])) {
  $per = base64_decode($_GET["per"]);
}

$grados = Grados::traerGradosGrupos($config, $curso, $grupo);
?>

<head>
  <title>Sabanas</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>

<body style="font-family:Arial;">

  <div style="margin: 10px;">
    <img src="../../files-general/instituciones/informes/sabanas.jpg" style="width: 100%;">
  </div>

  <div align="center" style="margin-bottom:20px;">
    <?= $informacion_inst["info_nombre"] ?><br>
    PERIODO: <?= $per; ?></br>
    <b><?= strtoupper($grados["gra_nombre"] . " " . $grados["gru_nombre"]); ?></b><br>

    <?php if ($informacion_inst["info_institucion"] == ICOLVEN) { ?>
      <p><a href="reportes-sabanas-indicador.php?curso=<?= $_GET["curso"]; ?>&grupo=<?= $_GET["grupo"]; ?>&per=<?= $_GET["per"]; ?>" target="_blank">VER SABANAS CON INDICADORES</a></p>
    <?php } ?>
  </div>
  <div style="margin: 10px;">
    <table bgcolor="#FFFFFF" width="100%" cellspacing="5" cellpadding="5" rules="all" border="<?= $config[13] ?>" style="border:solid; border-color:<?= $config[11] ?>;" align="center">
      <tr style="font-weight:bold; font-size:12px; height:30px; background:#6017dc; color:white;">
        <td align="center">No</b></td>
        <td align="center">C&oacute;digo</td>
        <td align="center">Estudiante</td>
        <?php
        $materias1 = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $curso, $grupo);
        while ($mat1 = mysqli_fetch_array($materias1, MYSQLI_BOTH)) {
        ?>
          <td align="center"><?= strtoupper($mat1['mat_siglas']); ?></td>
        <?php
        }
        ?>
        <td align="center" style="font-weight:bold;">PROM</td>
      </tr>
      <?php
      $cont = 1;
      $filtroAdicional = "AND mat_grado='" . $curso . "' AND mat_grupo='" . $grupo . "' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
      $asig =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$grados);
      while ($fila = mysqli_fetch_array($asig, MYSQLI_BOTH)) {
        $nombre = Estudiantes::NombreCompletoDelEstudiante($fila);

      ?>
        <tr style="font-size:13px;">
          <td align="center"> <?= $cont; ?></td>
          <td align="center"> <?= $fila['mat_id']; ?></td>
          <td><?= $nombre ?></td>
          <?php
          $suma = 0;
          $materias1 = CargaAcademica::consultaInformeSabanas($fila['mat_id'], $per, $config, $curso, $grupo);
          $numero = mysqli_num_rows($materias1);
          $def = '0.0';
          while ($mat1 = mysqli_fetch_array($materias1, MYSQLI_BOTH)) {
            $defini = 0;
            if (!empty($mat1['bol_nota'])) {
              $defini = $mat1['bol_nota'];
              $suma = ($suma + $defini);
            }
            if ($defini < $config[5]) $color = 'red';
            else $color = 'blue';

            $notaEstudiante = "";
            if (!empty($mat1['bol_nota'])) {
              $notaEstudiante = $mat1['bol_nota'];
            }

            $notaEstudianteFinal = $notaEstudiante;
            $title = '';
            if ($notaEstudiante != "" && $config['conf_forma_mostrar_notas'] == CUALITATIVA) {
              $title = 'title="Nota Cuantitativa: ' . $notaEstudiante . '"';
              $notaEstudianteFinal = !empty($mat1['notip_nombre']) ? $mat1['notip_nombre'] : "";
            }
          ?>
            <td align="center" style="color:<?= $color; ?>;" <?= $title; ?>><?= $notaEstudianteFinal ?></td>
          <?php
          }
          if ($numero > 0) {
            $def = round(($suma / $numero), 2);
          }
          if ($def == 1)  $def = "1.0";
          if ($def == 2)  $def = "2.0";
          if ($def == 3)  $def = "3.0";
          if ($def == 4)  $def = "4.0";
          if ($def == 5)  $def = "5.0";
          if ($def < $config[5]) $color = 'red';
          else $color = 'blue';
          $notas1[$cont] = $def;
          $grupo1[$cont] = $nombre;

          $defFinal = $def;
          $title = '';
          if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
            $title = 'title="Nota Cuantitativa: ' . $def . '"';
            $estiloDef = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $def);
            $defFinal = !empty($estiloDef['notip_nombre']) ? $estiloDef['notip_nombre'] : "";
          }
          ?>
          <td align="center" style="font-weight:bold; color:<?= $color; ?>;" <?= $title; ?>><?= $defFinal; ?></td>
        </tr>
      <?php
        $cont++;
      } //Fin mientras que
      ?>
    </table>

    <?php
    if (($config['conf_ver_promedios_sabanas_docentes'] == 1 && $datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE) || ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV)) {

      $puestos = Boletin::consultarPuestosBoletin($config, $curso, $grupo, $per);
    ?>
      <p>&nbsp;</p>
      <table width="100%" cellspacing="5" cellpadding="5" rules="all" style="border:solid; border-color:<?= $Plataforma->colorUno; ?>; font-size:11px;">
        <tr style="font-weight:bold; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
          <td colspan="3" align="center">PUESTOS</td>
        </tr>

        <tr style="font-weight:bold; font-size:14px; height:40px;">
          <td align="center">Puesto</b></td>
          <td align="center">Estudiante</td>
          <td align="center">Promedio</td>
        </tr>
        <?php
        $j = 1;
        $cambios = 0;
        $valor = 0;
        if (!empty($notas1)) {
          arsort($notas1);
          foreach ($notas1 as $key => $val) {
            if ($val != $valor) {
              $valor = $val;
              $cambios++;
            }
            if ($cambios == 1) {
              $color = '#CCFFCC';
            }
            if ($cambios == 2) {
              $color = '#CCFFFF';
            }
            if ($cambios == 3) {
              $color = '#FFFFCC';
            }
            if ($cambios == 4) {
              $color = '#FFFFFF';
            }

            $valTotal = $val;
            $title = '';
            if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
              $title = 'title="Nota Cuantitativa: ' . $val . '"';
              $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $val);
              $valTotal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
            }
        ?>
            <tr style="border-color:#41c4c4; background-color:<?= $color; ?>">
              <td align="center"><?= $j; ?></td>
              <td><?= $grupo1[$key]; ?></td>
              <td align="center" <?= $title; ?>><?= $valTotal; ?></td>
            </tr>
        <?php
            $j++;
          }
        }
        ?>

      </table>
    <?php } ?>

  </div>

</body>

</html>