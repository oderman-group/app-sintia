<?php
include_once("session-compartida.php");
require_once(ROOT_PATH . "/main-app/class/componentes/Excel/ExcelUtil.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");

require_once(ROOT_PATH . "/main-app/class/App/Academico/boletin/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/App/Academico/Notas_tipo.php");
require_once(ROOT_PATH . "/main-app/class/App/Academico/Calificacion/Vista_historial_calificaiones.php");
require_once(ROOT_PATH . "/main-app/class/App/Academico/Calificacion/Vista_cursos_estudiante.php");

require_once("../class/Informes.php");

$num = 0;
try {
  $grado = $_POST["grado"];
  $grupo = $_POST["grupo"];
  $formato = $_POST["formato"];
  $estudiantes = $_POST["estudiantes"];
  $cPeriodo = $config['conf_periodos_maximos'];
  $year = $_SESSION["bd"];

  $periodos = [];

  for ($i = 1; $i <= $cPeriodo; $i++) {
    $periodos[$i] = $i;
  }

  $listaCursoEstudiantes = Vista_cursos_estudiante::listarCursosEstudiates($estudiantes);
  $listaCalificaionesEstudiantes = Vista_historial_calificaciones::listarHistorialCalificaiones($grado, $grupo, $estudiantes);
  $listaEstudiantes = [];

  foreach ($listaCalificaionesEstudiantes as $item1) {
    foreach ($listaCursoEstudiantes as $item2) {
      if ($item1['mat_id'] == $item2['mat_id']) {
        $item1['cursos'] = $item2['cursos'];
      }
    }
    $listaEstudiantes[$item1['mat_id']] = $item1;
  }



} catch (Exception $e) {
  echo "Excepción catpurada: " . $e->getMessage();
  exit();
}
?>

<head>
  <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">

  <title>Historial de Calificaicones</title>

  <style>
    #saltoPagina {

      PAGE-BREAK-AFTER: always;

    }

    .list-group-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .list-group-item input {
      order: -1;
      margin-right: auto;
    }
  </style>
  <link rel="stylesheet" href="../../config-general/assets/plugins/steps/steps.css">


  <!--Bootstrap-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />


</head>

<body style="font-family:Arial;">




  <?php
  include_once("sintia-funciones.php");

  //Instancia de Clases generales
  foreach ($listaEstudiantes as $estudiante) {
    ?>
    <br>
    <div class="row justify-content-md-center">

      <div class="col-10">
        <div class="media">
          <img class="mr-3" src="<?= $estudiante["foto"] ?>" class='img-thumbnail' width='100px;' height='140px;'>
          <div class="media-body">
            <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all">
              <tr>
                <td>C&oacute;digo:<br>
                  <?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?>
                </td>
                <td>Nombre:<br> <?= $estudiante["nombre"] ?></td>
              </tr>

              <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?= $informacion_inst["info_nombre"] ?></td>

                <!-- <td>Puesto Colegio:<br> &nbsp;</td>   -->
              </tr>
              <tr>
                <td>Grado:<br> <?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></td>
                <td colspan="2">Periodos:<br> <b><?= $cPeriodo . " (" . $year . ")"; ?></b></td>
              </tr>
            </table>
          </div>
        </div>
        <p>&nbsp;</p>
        <div class="row">
          <div class="col-2">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
              <a class="nav-link disabled" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab"
                aria-controls="v-pills-home" aria-selected="true">Curso / Periodo</a>
              <?php foreach ($estudiante["cursos"] as $curso) {
                $curso_active = false;
                if ($estudiante["gra_id"] . '-' . $estudiante["gru_id"] == $curso["curso"] . '-' . $curso["grupo"]) {
                  $curso_active = true;
                }

                ?>
                <a class="nav-link <?php if ($curso_active) {
                  echo "active";
                } ?>" id="tab-<?= $estudiante["mat_id"] . "-" . $curso["curso"] . "-" . $curso["grupo"] ?>"
                  data-toggle="pill"
                  onclick="selecionarCurso('<?= $estudiante['mat_id'] ?>','<?= $curso['curso'] ?>','<?= $curso['grupo'] ?>')"
                  href="#contend-<?= $estudiante["mat_id"] . "-" . $curso["curso"] . "-" . $curso["grupo"] . "-1" ?>"
                  role="tab" aria-controls="v-pills-profile"
                  aria-selected="false"><?= $curso["gra_nombre"] . "- " . $curso["gru_nombre"]; ?></a>
              <?php } ?>
            </div>
          </div>
          <div class="col-8">
            <nav>
              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <?php foreach ($periodos as $periodo) { ?>
                  <a class="nav-item nav-link " id="h-<?= $estudiante["mat_id"] . "-" . $periodo ?>" data-toggle="tab"
                    href="#contend-<?= $estudiante["mat_id"] . "-" . $estudiante["gra_id"] . "-" . $estudiante["gru_id"] . "-" . $periodo ?>"
                    role="tab" aria-controls="nav-home" aria-selected="true">Periodo <?= $periodo ?></a>
                <?php } ?>
              </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
              <?php foreach ($estudiante["cursos"] as $curso) { ?>
                <?php foreach ($periodos as $periodo) {
                  $llave = $estudiante["mat_id"] . "-" . $curso["curso"] . "-" . $curso["grupo"] . "-" . $periodo;
                  ?>

                  <div class="tab-pane fade show " id="contend-<?= $llave ?>" role="tabpanel" aria-labelledby="nav-home-tab">

                    <div class="col-12">
                      <!-- <?= $estudiante["mat_id"] . "- Curso:" . $curso["curso"] . "- Grupo:" . $curso["grupo"] . "- Periodo:" . $periodo ?> -->
                      <section class="py-6">
                        <div class="container">
                          <div class="table-responsive border rounded">
                            <table class="table table-striped table-bordered mb-0">
                              <thead>
                                <tr class="table-active">
                                  <th scope="col">
                                    AREAS
                                  </th>
                                  <th scope="col" colspan="2">
                                    ASIGNATURAS
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <!-- AREAS -->
                                <?php foreach ($estudiante["areas"] as $area) { ?>
                                  <tr>
                                    <td class="table-active col-4"
                                      style="font-weight:bold; font-size: 16px;text-align:center;vertical-align: middle;" scope="row">
                                      <?= strtoupper($area['ar_nombre']); ?>
                                    </td>
                                    <td class="col-6">
                                      <div id="accordion">
                                        <div class="card">
                                          <!-- ASIGNATURAS -->
                                          <?php foreach ($area["cargas"] as $carga) { ?>
                                            <div style="cursor:pointer;font-weight:bold;"
                                              class="card-header list-group-item d-flex justify-content-between align-items-center"
                                              data-toggle="collapse" href="#div_<?= $llave ?>_carga_<?= $carga['car_id'] ?>">
                                              <span class="col-2"><?= $area['periodos'][$periodo]["porcentaje_periodo"] ?></span>
                                              <?= strtoupper($carga['mat_nombre']); ?>
                                              <span class="col-3"><input type="number" width="200px" class="form-control"
                                                  value="<?= $area['periodos'][$periodo]["porcentaje_periodo"] ?>" /></span>
                                            </div>
                                            <div id="div_<?= $llave ?>_carga_<?= $carga['car_id'] ?>" class="collapse "
                                              data-parent="#accordion">
                                              <!-- INDICADORES -->
                                              <!-- <div class="card-body" style="padding: 5px;"> -->
                                              <table width="100%" cellspacing="5" cellpadding="5" rules="all">
                                                <tbody>
                                                  <tr data-toggle="collapse"
                                                    href="#div_<?= $llave ?>_carga_<?= $carga['car_id'] ?>"
                                                    style="height: 30px; font-weight: bold;font-size: 11px;text-align: center;padding: 5px;cursor:pointer">
                                                    <td colspan="2"> INDICADORES</td>
                                                  </tr>
                                                  <tr style="height: 30px; font-weight: bold;font-size: 11px;">
                                                    <td colspan="2">
                                                      <div id="accordion">
                                                        <div class="card">
                                                          <?php foreach ($carga['periodos'][$periodo]["indicadores"] as $indicador) { ?>

                                                            <div style="cursor:pointer"
                                                              class="card-header list-group-item d-flex justify-content-between align-items-center"
                                                              data-toggle="collapse"
                                                              href="#multiCollapseExample1<?= $indicador["ind_id"] ?>">
                                                              <span
                                                                class="col-2"><?= $area['periodos'][$periodo]["porcentaje_periodo"] ?></span>
                                                              <?= $indicador["ind_nombre"] ?>
                                                              <span class="col-3"><input type="number" class="form-control"
                                                                  value="<?= $indicador['nota_final'] ?>" /></span>
                                                            </div>
                                                            <div id="multiCollapseExample1<?= $indicador["ind_id"] ?>"
                                                              class="collapse " data-parent="#accordion">
                                                              <!-- ACTIVIDADES -->
                                                              <table width="100%" cellspacing="5" cellpadding="5" border="1"
                                                                rules="all">
                                                                <tr data-toggle="collapse"
                                                                  href="#multiCollapseExample1<?= $indicador["ind_id"] ?>"
                                                                  style="height: 30px; font-weight: bold;font-size: 11px;text-align: center;padding: 5px;cursor:pointer">
                                                                  <td colspan="3"> ACTIVIDADES</td>
                                                                </tr>
                                                                <?php foreach ($indicador['actividades'] as $actividad) { ?>
                                                                  <tr style="height: 30px; font-weight: bold;font-size: 11px; ">
                                                                    <td>
                                                                      <span
                                                                        class="col-2"><?= $area['periodos'][$periodo]["porcentaje_periodo"] ?></span>
                                                                    </td>
                                                                    <td width="400px">
                                                                      <?= $actividad['act_descripcion'] ?>
                                                                    </td>
                                                                    <td width="100px">
                                                                      <input type="number" class="form-control" readonly
                                                                        value="<?= $actividad['cal_nota'] ?>">
                                                                    </td>
                                                                  </tr>
                                                                <?php } ?>
                                                              </table>
                                                              <br>
                                                            </div>

                                                          <?php } ?>
                                                        </div>

                                                      </div>
                                                    </td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                              <!-- </div> -->
                                            </div>
                                          <?php } ?>


                                        </div>
                                      </div>

                                    </td>
                                    <td class="col-2"
                                      style="height: 30px; font-weight: bold;font-size: 16px;vertical-align: middle;">
                                      <input type="number" class="form-control"
                                        placeholder="<?= $area['periodos'][$periodo]["porcentaje_periodo"] ?>"
                                        value="<?= $area['periodos'][$periodo]["porcentaje_periodo"] ?>">
                                    </td>
                                  </tr>
                                <?php } ?>


                              </tbody>
                            </table>
                          </div>
                        </div>
                      </section>


                    </div>
                  </div>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
          <div class="col-2">
            <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
              <thead>
                <tr style="font-weight:bold; text-align:center; background-color: #74cc82;">
                  <td>ASING</td>
                  <td>ACUM</td>
                  <td>DEF</td>
                </tr>
              </thead>
            </table>
          </div>
        </div>


        <p>&nbsp;</p>
      </div>
    </div>
    <?php ?>
  <?php } ?>
  <script>
    function selecionarCurso(estudiante, curso, grupo) {
      console.log('-Estudainte:' + estudiante + '-Cruso:' + curso + '-Grupo:' + grupo);
      const elements = document.querySelectorAll(`[id*="h-${estudiante}"]`);
      elements.forEach(element => {

        const llaves = element.href.split("-");
        let periodo = llaves[7];
        if (element.href.includes(estudiante)) {
          let newPart = '#contend-' + estudiante + '-' + curso + '-' + grupo + '-' + periodo;
          element.href = element.href = newPart;
        }
      });

      const tabLink = document.getElementById(`h-${estudiante}-1`);
      // Simula un clic en el enlace de la pestaña
      if (tabLink) {
        tabLink.click();
      }
    }
  </script>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"
    integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
    integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
    crossorigin="anonymous"></script>

</body>