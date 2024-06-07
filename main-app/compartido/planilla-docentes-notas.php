<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0237';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
require_once("../class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
?>

<head>
  <meta charset="utf-8">
  <title>Planilla Docentes con Notas</title>
  <style>
    #saltoPagina {
      PAGE-BREAK-AFTER: always;
    }

    .vertical {
      writing-mode: vertical-lr;
      /* o vertical-lr */
      text-orientation: mixed;
      /* para que los caracteres se roten correctamente */
      transform: rotate(180deg);
      padding-top: 5px;
    }
  </style>
</head>
<body style="font-family:Arial;">
  <?php
  $filtro = '';
  if (!empty($_REQUEST["carga"])) {
    $carga = base64_decode($_REQUEST["carga"]);
    $filtro .= " AND car_id='" . $carga . "'";
  }
  if (!empty($_REQUEST["docente"])) {
    $docente = base64_decode($_REQUEST["docente"]);
    $filtro .= " AND car_docente='" . $docente . "'";
  }
  if (!empty($_REQUEST["grado"])) {
    $filtro .= " AND car_curso='" . $_REQUEST["grado"] . "'";
  }
  if (!empty($_REQUEST["grupo"])) {
    $filtro .= " AND car_grupo='" . $_REQUEST["grupo"] . "'";
  }
  if (!empty($_REQUEST["periodo"])) {
    $filtro .= " AND car_periodo='" . $_REQUEST["periodo"] . "'";
  }
  $consultaCargas = CargaAcademica::listarCargas($conexion, $config, "", $filtro);
  while ($resultadoCargas = mysqli_fetch_array($consultaCargas, MYSQLI_BOTH)) {
    $materia=strtoupper($resultadoCargas['mat_nombre']);
    $materiaSiglas=strtoupper($resultadoCargas['mat_siglas']);
    $periodoActual=($resultadoCargas['car_periodo']-1);

    switch($periodoActual){
        case 1:
            $acomulado=0.25;
            break;
        case 2:
            $acomulado=0.50;
            break;
        case 3:
            $acomulado=0.75;
            break;
        case 4:
            $acomulado=0.10;
            break;
    }
?>
    <div align="center" style="margin-bottom:20px;">
      <b>
        <?= $informacion_inst["info_nombre"] ?>
      </b><br>
      <b>Evaluaci&oacute;n e Inasistencia
      </b><br>
    </div>

    <table width="100%" cellspacing="5" cellpadding="5" border="0" rules="none">
      <tr>
        <td><strong>DOCENTE:</strong><br> <?= UsuariosPadre::nombreCompletoDelUsuario($resultadoCargas) ?></td>
        <td><strong>GRADO:</strong><br> <?= $resultadoCargas["gra_nombre"]; ?> <?= $resultadoCargas["gru_nombre"]; ?></td>
        <td>&nbsp;</td>
      </tr>

      <tr>
        <td><strong>ASIGNATURA:</strong><br> <?= $materia ?></td>
        <td><strong>PERIODO:</strong><br> <?php echo $resultadoCargas['car_periodo'] . " (" . date("Y") . ")"; ?></td>
        <td><strong>Fecha Impresión:</strong><br> <?= date("d/m/Y H:i:s"); ?></td>
      </tr>
    </table>
    <p>&nbsp;</p>

    <table bgcolor="#FFFFFF" width="100%" cellspacing="0" cellpadding="0" rules="all" border="1">
      <tr style="font-weight:bold; background:<?=$Plataforma->colorUno;?>; border-color:#4c9858; height:20px; color:#FFF; font-size:12px;">
        <td align="center" width="15%" colspan="3">Información del Estudiante</td>
        <td width="10%" colspan="5" align="center">Resumen de Periodos</td>
        <td width="10%" colspan="15" align="center">TEMAS</td>
        <td align="center" width="3%">Auto</td>
        <td align="center" width="3%">Coo</td>
        <td colspan="7" width="2%"></td>
      </tr>

      <tr style="height:150px; font-weight:bold; font-size:12px;">
        <td align="center" style="font-weight:bold; background:<?=$Plataforma->colorUno;?>; color:#FFF; font-size:12px;" rowspan="2">No</b></td>
        <td align="center" style="font-weight:bold; background:<?=$Plataforma->colorUno;?>; color:#FFF; font-size:12px;" rowspan="2">C&oacute;digo</td>
        <td align="center" style="font-weight:bold; background:<?=$Plataforma->colorUno;?>; color:#FFF; font-size:12px;" rowspan="2">Estudiante</td>
      <?php
        for($i=1;$i<=4;$i++){
      ?>
        <td rowspan="2" class="vertical" style="background:<?=$Plataforma->colorTres;?>; height:20px;" width="2%"><?= $i.". ".$materiaSiglas; ?></td>
      <?php
        }
      ?>
        <td rowspan="2" class="vertical" style="background:<?=$Plataforma->colorTres;?>; height:20px;" width="2%">FINAL <?= $materiaSiglas; ?></td>
      <?php
        for($i=1;$i<=17;$i++){
      ?>
        <td align="center" width="2%">&nbsp;</td>
      <?php
        }
      ?>
        <td colspan="7" align="center">______________________<br> Firma Docente</td>
      </tr>

      <tr style="font-weight:bold; font-size:12px; height:35px; background:<?=$Plataforma->colorUno;?>; border-color:#4c9858; color:#FFF;">
        <td align="center" colspan="17" width="2%">NOTAS</td>
        <td align="center" colspan="7">Inasistencia</td>
        <?php
          $estudiantes = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($resultadoCargas);

          $n = 1;

          while ($e = mysqli_fetch_array($estudiantes, MYSQLI_BOTH)) {
        ?>

      <tr style="font-size:10px; height:25px;">
        <td align="center" width="2%"><?= $n; ?></td>
        <td align="center" width="5%"><?= $e['mat_id']; ?></td>
        <td width="20%"><?= Estudiantes::NombreCompletoDelEstudiante($e)?></td>
      <?php
        $acomuladoNota=0;
        for($i=1;$i<=4;$i++){
          $nota = Boletin::traerNotaBoletinCargaPeriodo($config, $i, $e['mat_id'], $resultadoCargas['car_id']);

          $notaEstudiante="";
          if(!empty($nota['bol_nota'])){
            $notaEstudiante=round($nota['bol_nota'], $config['conf_decimales_notas']);
            $acomuladoNota+=$notaEstudiante;
          }
          
          $estiloNota='style="background:'.$Plataforma->colorTres.';"';
          if($notaEstudiante!="" AND $notaEstudiante<$config['conf_nota_minima_aprobar']){
              $estiloNota='style="font-weight:bold; color:#FFF; background:'.$Plataforma->colorDos.';"';
          }

          $notaEstudianteFinal=$notaEstudiante;
          $title='';
          if($notaEstudiante!="" && $config['conf_forma_mostrar_notas'] == CUALITATIVA){
            $title='title="Nota Cuantitativa: '.$notaEstudiante.'"';
            $estiloNotaEstudiante = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaEstudiante);
            $notaEstudianteFinal= !empty($estiloNotaEstudiante['notip_nombre']) ? $estiloNotaEstudiante['notip_nombre'] : "";
          }
      ?>
        <td align="center" <?=$estiloNota?> <?=$title;?> width="3%"><?=$notaEstudianteFinal?></td>
      <?php
          // $acomuladoNota+=$notaEstudiante;
        }
        //ACOMULADO PARA LAS MATERIAS
        $totalAcomuladoNota=$acomuladoNota*$acomulado;
        $totalAcomuladoNota= round($totalAcomuladoNota, 1);

        $totalAcomuladoNotaFinal=$totalAcomuladoNota;
        $title='';
        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
          $title='title="Nota Cuantitativa: '.$totalAcomuladoNota.'"';
          $estiloTotalAcomuladoNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $totalAcomuladoNota);
          $totalAcomuladoNotaFinal= !empty($estiloTotalAcomuladoNota['notip_nombre']) ? $estiloTotalAcomuladoNota['notip_nombre'] : "Bajo";
        }
      ?>
        <td align="center" style="background:<?=$Plataforma->colorTres;?>;" width="3%" <?=$title;?>><?=$totalAcomuladoNotaFinal?></td>
      <?php
        for($i=1;$i<=17;$i++){
      ?>
        <td align="center" width="3%">&nbsp;</td>
      <?php
        }
      ?>
      <?php
        for($i=1;$i<=7;$i++){
      ?>
        <td width="1%">&nbsp;</td>
      <?php
        }
      ?>
      </tr>

    <?php
          $n++;
        } //fin estudiantes
    ?>
    </table>



    <div align="center" style="font-size:10px; margin-top:5px; margin-bottom: 10px;">

      <img src="https://plataformasintia.com/images/logo.png" height="50"><br>

      ESTE DOCUMENTO FUE GENERADO POR:<br>

      SINTIA - SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL

    </div>



    <div id="saltoPagina"></div>

  <?php

  } //Fin de las cargas

  ?>

  </center>

  <script type="application/javascript">
    print();
  </script>

</body>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
</html>