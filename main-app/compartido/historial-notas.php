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
    body {
      font-family: 'revert-layer' !important, sans-serif;
      font-size: x-small !important;
      margin: 0;
      padding: 0;
      background-image: url(./../../config-general/assets-login-2023/img/bg-login.png);
      grid-template-columns: 100%;
    }

    .progress-indicador {
    display: flex
;
    height: 0.6rem;
    overflow: hidden;
    font-size: .5rem;
    background-color: #e9ecef;
    border-radius: .25rem;
}

    .collapse-container {
  overflow: hidden;
  max-height: 0;
  transition: max-height 0.5s ease;
}
.collapse-container.show {
  max-height: 1000px; /* Ajusta esto según el contenido */
}

    .form-control:disabled,
    .form-control[readonly] {
      background-color: #ffffff !important;
      opacity: 1 !important;

    }

    .form-control {
      text-align: center;
    }

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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"  integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous"> -->
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
         
          <div class="media-body">
            <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all">
              <tr>
              <td rowspan="3"  width='120px;'> <img class="mr-3" src="<?= $estudiante["foto"] ?>" class='img-thumbnail' height='140px;'></td>
              <td><b>C&oacute;digo:</b><br>
                
                  <?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?>
                </td>
                <td><b>Nombre:</b><br> <?= $estudiante["nombre"] ?></td>
              </tr>

              <tr>
                <td><b>Jornada:</b><br> Mañana</td>
                <td><b>Sede:</b><br> <?= $informacion_inst["info_nombre"] ?></td>

                <!-- <td>Puesto Colegio:<br> &nbsp;</td>   -->
              </tr>
              <tr>
                <td><b>Grado:</b><br> <?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></td>
                <td colspan="2"><b>Periodos:</b><br> <?= $cPeriodo . " (" . $year . ")"; ?></td>
              </tr>
            </table>
          </div>
        </div>
        <p>&nbsp;</p>
        <div class="row">
          <div class="col-2">
            <a class="nav-link disabled" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home"
            aria-selected="true">Curso / Periodo</a>
            <div class="list-group" id="list-tab" role="tablist">
            <?php foreach ($estudiante["cursos"] as $curso) {
              $curso_active        = false;
              $llave_cruso_default = $estudiante["mat_id"].'-'.$estudiante["gra_id"] . '-' . $estudiante["gru_id"];
              $llave_cruso         = $estudiante["mat_id"].'-'.$curso["curso"] . '-' . $curso["grupo"];
              if (  $llave_cruso  == $llave_cruso_default ) {
                $curso_active = true;
              }
              ?>
              <a class="list-group-item list-group-item-action <?php if ($curso_active) { echo "active";} ?>" " 
                id="tab-<?=$llave_cruso?>" 
                data-bs-toggle="list" 
                href="#contend-<?=$llave_cruso?>-1"
                role="tab" 
                aria-controls="list-home">
                <?= $curso["gra_nombre"] . "- " . $curso["gru_nombre"]; ?>
              </a>
              
              <?php } ?>
            </div>
             <!-- <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                <a class="nav-link disabled" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home"
                  aria-selected="true">Curso / Periodo</a>
                <?php foreach ($estudiante["cursos"] as $curso) {
                  $curso_active = false;
                  if ($estudiante["gra_id"] . '-' . $estudiante["gru_id"] == $curso["curso"] . '-' . $curso["grupo"]) {
                    $curso_active = true;
                  }

                  ?>
                  <a class="nav-link <?php if ($curso_active) { echo "active";} ?>" id="tab-<?= $estudiante["mat_id"] . "-" . $curso["curso"] . "-" . $curso["grupo"] ?>"
                    data-toggle="pill"
                    onclick="selecionarCurso('<?= $estudiante['mat_id'] ?>','<?= $curso['curso'] ?>','<?= $curso['grupo'] ?>')"
                    href="#contend-<?= $estudiante["mat_id"] . "-" . $curso["curso"] . "-" . $curso["grupo"] . "-1" ?>"
                    role="tab" aria-controls="v-pills-profile"
                    aria-selected="false"><?= $curso["gra_nombre"] . "- " . $curso["gru_nombre"]; ?></a>
                <?php } ?>
              </div>-->
          </div>
          <div class="col-8" style="padding-right: 0px;padding-left: 0px;">
            <ul class="nav nav-tabs"  role="tablist">
                  <?php foreach ($periodos as $periodo) {
                    $llave_curso_periodo_defaul = $estudiante["mat_id"]."-".$estudiante["gra_id"]."-".$estudiante["gru_id"] . "-1";
                    $llave_curso_periodo        = $estudiante["mat_id"].'-'.$estudiante["gra_id"].'-'.$estudiante["gru_id"] ."-" . $periodo;
                    ?>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link <?php if ($llave_curso_periodo == $llave_curso_periodo_defaul) { echo "active";} ?>" id="btn-<?=$llave_curso_periodo?>" data-bs-toggle="tab" data-bs-target="#contend-<?=$llave_curso_periodo?>" type="button" role="tab" aria-controls="home" aria-selected="true"> Periodo <?= $periodo ?> </button>
                    </li>
                    <?php } ?>
              </ul>
           <!-- <nav>
              <div class="nav nav-tabs" role="tablist">
                <?php foreach ($periodos as $periodo) { ?>
                  <a class="nav-item nav-link " id="h-<?= $estudiante["mat_id"] . "-" . $periodo ?>" data-toggle="tab"
                    href="#contend-<?= $estudiante["mat_id"] . "-" . $estudiante["gra_id"] . "-" . $estudiante["gru_id"] . "-" . $periodo ?>"
                    role="tab" aria-controls="nav-home" aria-selected="true">Periodo <?= $periodo ?></a>
                <?php } ?>
              </div>
            </nav>-->
            <div class="tab-content">
           
              <?php foreach ($estudiante["cursos"] as $curso) { ?>
                <?php foreach ($periodos as $periodo) {                  
                  $llave_curso_periodo_defaul = $estudiante["mat_id"] . "-" . $estudiante["gra_id"] . "-" . $estudiante["gru_id"] . "-1";
                  $llave_curso_periodo        = $estudiante["mat_id"] . "-" . $curso["curso"] . "-" . $curso["grupo"] . "-" . $periodo;
                  ?>
                  <div class="tab-pane fade show <?php if ($llave_curso_periodo == $llave_curso_periodo_defaul) { echo "active";} ?>" id="contend-<?= $llave_curso_periodo ?>" role="tabpanel" aria-labelledby="btn-<?=$llave_curso_periodo?>">
                     Home <?= $periodo ?> <?=$llave_curso_periodo?>
                     
                      <table class="table table-striped table-bordered">
                        <thead>
                          <tr class="table-active" style="border-style: hidden;">
                            <th scope="col" weight="10%"
                                style="font-weight:bold; text-align:center;vertical-align: middle;">
                            AREAS
                            </th>
                            <th scope="col"weight="70%"
                                style="font-weight:bold; font-size: 16px;text-align:center;vertical-align: middle;">
                            ASIGNATURAS
                            </th>
                            <th scope="col" weight="20%"
                                style="font-weight:bold; font-size: 16px;text-align:center;vertical-align: middle;">
                            NOTA
                            </th>
                          </tr>
                        </thead>
                        <?php if ($llave_curso_periodo == $estudiante["mat_id"] . "-" . $grado . "-" . $grupo . "-" . $periodo) { ?>
                          <tbody>
                            <!-- AREAS -->
                            <?php foreach ($estudiante["areas"] as $area) { 
                              $llave_curso_periodo_area=$llave_curso_periodo."-".$area["ar_id"];
                              ?>
                              <tr>
                               <td class=" col-3"
                                        style="font-weight:bold; font-size: 13px;text-align:left;vertical-align: middle;"
                                        scope="row">
                                <?= strtoupper($area['ar_nombre']); ?>
                                      
                                </td>
                                <td class="col-7">
                                  <!-- ASIGNATURAS -->
                                  <div class="card">
                                    <?php foreach ($area["cargas"] as $carga) { 
                                      $llave_curso_periodo_area_carga=$llave_curso_periodo_area."-".$carga["car_id"];
                                      ?>
                                    
                                        <div style="cursor:pointer;font-weight:bold;font-size: 12px"
                                                    class="card-header list-group-item d-flex justify-content-between">

                                          <span class="col-2 toggle-collapse" 
                                                data-target="#carga_<?= $llave_curso_periodo_area_carga ?>">
                                            <?= $carga['mat_valor'] ?>%
                                          </span>
                                          <span class="col-7 toggle-collapse"
                                                data-target="#carga_<?= $llave_curso_periodo_area_carga ?>">
                                            <?= strtoupper($carga['mat_nombre']); ?>
                                            <div class="progress" style="height: 3px;margin-right: 20px;">
                                              <div class="progress-bar" role="progressbar" style="width:  <?= $carga["progreso_carga"] ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <!-- <div class="progress" style="margin-right: 20px;" >
                                                  <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">10%</div>
                                            </div> -->
                                          </span>
                                          <?php if(!empty($carga['periodos'][$periodo]["bol_tipo"]) && $carga['periodos'][$periodo]["bol_tipo"]=="2") {?>
                                            <div class="input-group">
                                              <?php if(!empty($carga['periodos'][$periodo]["bol_nota_anterior"])) {?>
                                                 <input type="number" class="form-control"
                                                        style="font-size: 11px;color:red" readonly title="Nota anterior"
                                                        value="<?= $carga['periodos'][$periodo]["bol_nota_anterior"] ?>" />
                                              <?php }?>
                                              <input type="number"  class="form-control"
                                                     style="font-size: 11px;color:blue" readonly title="Nota Recuperada"
                                                     value="<?= $carga['periodos'][$periodo]["bol_nota"] ?>" />
                                            </div>
                                          <?php }else {?>
                                            <span class="col-3">
                                              <input type="number" width="200px" class="form-control"
                                                     style="height: 30px;font-size: 14px;" readonly 
                                                     value="<?= $carga['periodos'][$periodo]["bol_nota"] ?>" />
                                              </span>
                                          <?php }?>
                                        </div>
                                          <div class="collapse-container" id="carga_<?= $llave_curso_periodo_area_carga ?>" >
                                            <!-- INDICADORES -->
                                            <table class="table" width="100%" cellspacing="5" cellpadding="5" rules="all">
                                              <thead  class="toggle-collapse"
                                                      data-target="#carga_<?= $llave_curso_periodo_area_carga ?>">
                                                <tr class="table-active" style="height: 30px; font-weight: bold;font-size: 11px;text-align: center;padding: 5px;cursor:pointer;border-style: hidden;">
                                                  <td > INDICADORES</td>
                                               </tr>
                                              </thead>
                                              <tbody>
                                                <tr style="font-weight: bold;font-size: 11px;">
                                                  <td >
                                                     <?php foreach ($carga['periodos'][$periodo]["indicadores"] as $indicador) { 
                                                           $llave_curso_periodo_area_carga_indicador=$llave_curso_periodo_area_carga."-".$indicador["ind_id"];?>
                                                      <div style="cursor:pointer"
                                                           class="card-header list-group-item d-flex justify-content-between align-items-center">
                                                               
                                                                <span class="col-2 toggle-collapse"  data-target="#indicador_<?= $llave_curso_periodo_area_carga_indicador ?>" ><?= $indicador['ipc_valor'] ?>%</span>
                                                                <span class="col-7 toggle-collapse"  data-target="#indicador_<?= $llave_curso_periodo_area_carga_indicador ?>"
                                                                  style="font-weight: lighter;font-size: 11px;">
                                                                  <?= strtolower($indicador["ind_nombre"]);?>
                                                                  
                                                                   <div class="progress-indicador" style="margin-right: 20px;" >
                                                                    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: <?= $indicador["progreso_indicador"] ?>%" aria-valuenow="<?= $indicador["progreso_indicador"] ?>" aria-valuemin="0" aria-valuemax="100"><?= $indicador["progreso_indicador"] ?>%</div>
                                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= 100-$indicador["progreso_indicador"]?>%" aria-valuenow="<?= 100-$indicador["progreso_indicador"] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                  </div>
                                                                </span>
                                                                <span class="col-3">
                                                                <?php if($indicador['recuperado']) {?>
                                                                    <div class="input-group">
                                                                      <input type="number" class="form-control" readonly
                                                                            style="height: 25px;font-size: 10px;color:red"
                                                                            value="<?= $indicador['nota_indicador'] ?>" />
                                                                      <input type="number" class="form-control" readonly
                                                                            style="height: 25px;font-size: 10px;color:blue""
                                                                            value="<?= $indicador['nota_indicador_recuperado'] ?>" />
                                                                    </div>
                                                                  <?php }else {?>
                                                                    <input type="number" class="form-control" readonly
                                                                         style="height: 25px;font-size: 13px;"
                                                                         value="<?= $indicador['nota_indicador'] ?>" />
                                                                  <?php }?> 
                                                                  
                                                                </span>
                                                          </div>
                                                          <div class="collapse-container" id="indicador_<?= $llave_curso_periodo_area_carga_indicador ?>" >
                                                             <!-- ACTIVIDADES -->
                                                             <table class="table table-striped table-bordered"  style="border: darkgray;" width="100%"
                                                                  rules="all">
                                                                  <thead  class="toggle-collapse" data-target="#indicador_<?= $llave_curso_periodo_area_carga_indicador ?>">
                                                                    <tr class="table-active" style="font-weight: bold;font-size: 11px;text-align: center;padding: 5px;cursor:pointer;border-style: hidden;">
                                                                      <td colspan="3"> ACTIVIDADES</td>
                                                                    </tr>
                                                                  </thead>
                                                                  <?php foreach ($indicador['actividades'] as $actividad) { 
                                                                    ?>
                                                                    <tr style="height: 30px;font-size: 11px; ">
                                                                      <td>
                                                                        <span class="col-2"><?= $actividad['act_valor'] ?>%</span>
                                                                      </td>
                                                                      <td width="400px"
                                                                        style="height: 30px;font-weight: lighter;font-size: 10px">
                                                                        <?= $actividad['act_descripcion'] ?>
                                                                      </td>
                                                                      <td width="100px">
                                                                        <input type="number" class="form-control" readonly
                                                                          style="height: 25px;font-size: 12px;"
                                                                          value="<?= $actividad['cal_nota'] ?>">
                                                                      </td>
                                                                    </tr>
                                                                  <?php } ?>
                                                                </table>

                                                          </div>
                                                        <?php } ?>
                                                      </td>
                                                    </tr>
                                                  </tbody>                                                  
                                                </table>
                                          </div>
                                        
                                    <?php } ?>
                                  </div> 
                                </td>
                                <td class="col-2"
                                        style="height: 30px; font-weight: bold;font-size: 16px;vertical-align: middle;">
                                        <input type="number" class="form-control"
                                          style="height: 60px;font-size: 30px;" readonly
                                          placeholder="<?= $area['periodos'][$periodo]["porcentaje_periodo"] ?>"
                                          value="<?= $area['periodos'][$periodo]["porcentaje_periodo"] ?>">
                                      </td>
                              </tr>

                            <?php } ?>

                          </tbody>
                        <?php } ?>
                      </table>
                      
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
document.addEventListener('click', function (event) { 
  const toggler = event.target.closest('.toggle-collapse');
  
  if (toggler) {
    event.preventDefault();
    const targetId = toggler.getAttribute('data-target');
    const targetElement = document.querySelector(targetId);

    if (!targetElement) return;

    // Alterna la clase 'show' para activar la animación
    targetElement.classList.toggle('show');
  }
});
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
 <!--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
    integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
    crossorigin="anonymous"></script> -->

</body>