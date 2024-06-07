<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0241';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

$datos = Grados::traerGradosGrupos($config, $_REQUEST["grado"], $_REQUEST["grupo"]);
?>

<head>
  <title>Estudiantes</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>

<body style="font-family:Arial;">
  <?php
  $nombreInforme = "REPORTES DISCIPLINARIOS" . "<br>" . strtoupper(Utilidades::getToString($datos['gra_nombre']). " " . Utilidades::getToString($datos['gru_nombre'])) . "<br> DESDE " . $_POST["desde"] . " HASTA " . $_POST["hasta"];;
  include("../compartido/head-informes.php") ?>

  <table width="100%" cellspacing="5" cellpadding="5" rules="all" style="
  border:solid; 
  border-color:<?= $Plataforma->colorUno; ?>; 
  font-size:11px;
  ">

    <tr style="font-weight:bold; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
      <th>#</th>
      <th>Fecha</th>
      <th>Estudiante</th>
      <th>Curso</th>
      <th>Categoría</th>
      <th>Cod</th>
      <th>Observaciones</th>
      <th>Usuario</th>
      <th title="Firma y aprobación del estudiante">F.E</th>
      <th title="Firma y aprobación del acudiente">F.A</th>
    </tr>
    <?php
    $cont = 1;
    $filtro = '';
    if (!empty($_POST["est"])) {
      $filtro .= " AND dr_estudiante='" . $_POST["est"] . "'";
    }
    if (!empty($_POST["falta"])) {
      $filtro .= " AND dr_falta='" . $_POST["falta"] . "'";
    }
    if (!empty($_POST["usuario"])) {
      $filtro .= " AND dr_usuario='" . $_POST["usuario"] . "'";
    }

    $filtroMat = '';
    if (!empty($_POST["grado"])) {
      $filtroMat .= " AND mat_grado='" . $_POST["grado"] . "'";
    }
    if (!empty($_POST["grupo"])) {
      $filtroMat .= " AND mat_grupo='" . $_POST["grupo"] . "'";
    }

    if($datos['gra_tipo']==GRADO_GRUPAL){
      $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disciplina_reportes dr
      INNER JOIN ".BD_DISCIPLINA.".disciplina_faltas ON dfal_id=dr_falta AND dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]}
      INNER JOIN ".BD_DISCIPLINA.".disciplina_categorias ON dcat_id=dfal_id_categoria AND dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$_SESSION["bd"]}
      INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat_id_usuario=dr_estudiante AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]} $filtroMat
      LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat_grado AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]}
      LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat_grupo AND gru.institucion={$config['conf_id_institucion']} AND gru.year={$_SESSION["bd"]}
      INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=dr_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
      WHERE dr_fecha>='" . $_POST["desde"] . "' AND dr_fecha<='" . $_POST["hasta"] . "' AND dr.institucion={$config['conf_id_institucion']} AND dr.year={$_SESSION["bd"]} $filtro
      ");
    }else{
      $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disciplina_reportes dr
      INNER JOIN ".BD_DISCIPLINA.".disciplina_faltas ON dfal_id=dr_falta AND dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]}
      INNER JOIN ".BD_DISCIPLINA.".disciplina_categorias ON dcat_id=dfal_id_categoria AND dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$_SESSION["bd"]}
      INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat_id_usuario=dr_estudiante AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
      INNER JOIN ".$baseDatosServicios.".mediatecnica_matriculas_cursos ON matcur_id_matricula=mat_id AND matcur_id_curso='" . $_POST["grado"] . "' AND matcur_estado='".ACTIVO."' AND matcur_id_institucion={$config['conf_id_institucion']} AND matcur_years={$_SESSION["bd"]}
      LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=matcur_id_curso AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]}
      LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=matcur_id_grupo AND gru.institucion={$config['conf_id_institucion']} AND gru.year={$_SESSION["bd"]}
      INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=dr_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
      WHERE dr_fecha>='" . $_POST["desde"] . "' AND dr_fecha<='" . $_POST["hasta"] . "' AND dr.institucion={$config['conf_id_institucion']} AND dr.year={$_SESSION["bd"]} $filtro
      ");
    }
    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
    ?>

      <tr style="border-color:<?= $Plataforma->colorDos; ?>;">
        <td><?= $cont; ?></td>
        <td><?= $resultado['dr_fecha']; ?></td>
        <td><?= Estudiantes::NombreCompletoDelEstudiante($resultado); ?></td>
        <td><?= $resultado['gra_nombre'] . " " . $resultado['gru_nombre']; ?></td>
        <td><?= $resultado['dcat_nombre']; ?></td>
        <td><?= $resultado['dfal_codigo']; ?></td>
        <td><?= $resultado['dr_observaciones']; ?></td>
        <td><?= $resultado['uss_nombre']; ?></td>
        <td>
          <?php if ($resultado['dr_aprobacion_estudiante'] == 0) {
            echo "-";
          } else { ?>
            <i class="fa fa-check-circle" title="<?= $resultado['dr_aprobacion_estudiante_fecha']; ?>">OK</i>
          <?php } ?>
        </td>
        <td>
          <?php if ($resultado['dr_aprobacion_acudiente'] == 0) {
            echo "-";
          } else { ?>
            <i class="fa fa-check-circle" title="<?= $resultado['dr_aprobacion_acudiente_fecha']; ?>">OK</i>
          <?php } ?>
        </td>
      </tr>
    <?php
      $cont++;
    } //Fin mientras que
    ?>
  </table>
  </center>
</body>
<?php include("../compartido/footer-informes.php");
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); ?>
</html>