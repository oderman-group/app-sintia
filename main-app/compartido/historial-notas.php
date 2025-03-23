<?php
include_once("session-compartida.php");
require_once(ROOT_PATH . "/main-app/class/componentes/Excel/ExcelUtil.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/compartido/overlay.php");
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

  $tiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year)->fetch_all(MYSQLI_ASSOC); //obenemos los tipos de notas
  $listaCursoEstudiantes = Vista_cursos_estudiante::listarCursosEstudiates($estudiantes);//obenemos los cursos  de cada estudiante en caso de que alguno tenga registros en otros cursos el mismo año 
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
      font-size: 12px !important;
      margin: 0;
      padding: 0;
      background-image: url(./../../config-general/assets-login-2023/img/bg-login.png);
      grid-template-columns: 100%;
    }

    .progress-indicador {
      display: flex;
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
      max-height: 1000px;
      /* Ajusta esto según el contenido */
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
  <!-- <link rel="stylesheet" href="../../config-general/assets/plugins/steps/steps.css"> -->


  <!--Bootstrap-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"  integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous"> -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" /> -->


</head>

<body style="font-family:Arial;">
  <script src="funciones.js"></script>



  <?php
  include_once("sintia-funciones.php");
  function retornarColor($valor, bool $recuperado = false)
  {
    $color = "";
    $valor ??= 0;

    if ($valor <= 5) {
      $color = "bg-danger";
    } else if ($valor > 5 && $valor < 50) {
      $color = "bg-warning";
    } elseif ($valor > 50 && $valor < 99) {
      $color = "";
    } elseif ($valor >= 100) {
      $color = "bg-success";
    }
    if ($recuperado) {
      $color = "bg-success";
    }

    return $color;
  }
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
                <td rowspan="3" width='120px;'> <img class="mr-3" src="<?= $estudiante["foto"] ?>" class='img-thumbnail'
                    height='140px;'></td>
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
                $curso_active = false;
                $llave_cruso_default = $estudiante["mat_id"] . '-' . $estudiante["year"] . '-' . $estudiante["gra_id"] . '-' . $estudiante["gru_id"];
                $llave_cruso = $estudiante["mat_id"] . '-' . $curso["year"] . '-' . $curso["curso"] . '-' . $curso["grupo"];
                if ($llave_cruso == $llave_cruso_default) {
                  $curso_active = true;
                }
                ?>
                <a class="list-group-item list-group-item-action <?php if ($curso_active) {
                  echo "active";
                } ?>" id="tab-<?= $llave_cruso ?>" data-bs-toggle="list"
                  onclick="selecionarCurso('<?= $estudiante['mat_id'] ?>','<?= $curso['year'] ?>','<?= $curso['curso'] ?>','<?= $curso['grupo'] ?>')"
                  href="#contend-<?= $llave_cruso ?>-1" role="tab" aria-controls="contend-<?= $llave_cruso ?>-1">
                  <?= $curso["gra_nombre"] . " - " . $curso["gru_nombre"]; ?>
                </a>

              <?php } ?>
            </div>
          </div>
          <div class="col-10" style="padding-right: 0px;padding-left: 0px;">
            <ul class="nav nav-tabs" role="tablist">
              <?php foreach ($estudiante["periodos"] as $periodo) {
                $llave_curso_periodo_defaul = $estudiante["mat_id"] . "-" . $year . "-" . $estudiante["gra_id"] . "-" . $estudiante["gru_id"] . "-1";
                $llave_curso_periodo = $estudiante["mat_id"] . "-" . $estudiante["year"] . '-' . $estudiante["gra_id"] . '-' . $estudiante["gru_id"] . "-" . $periodo["periodo"];
                $llave_curso_final = $estudiante["mat_id"] . "-" . $year . "-" . $grado . "-" . $grupo;
                ?>
                <li class="nav-item" role="presentation">
                  <button class="nav-link <?php if ($llave_curso_periodo == $llave_curso_periodo_defaul) {
                    echo "active";
                  } ?>" id="btn-periodos-<?= $estudiante["mat_id"] . "-" . $periodo["periodo"] ?>" data-bs-toggle="tab"
                    data-bs-target="#contend-<?= $llave_curso_periodo ?>" type="button" role="tab"
                    aria-controls="contend-<?= $llave_curso_periodo ?>" aria-selected="true"> Periodo
                    <?= $periodo["periodo"] ?> (<?= $periodo["porcentaje_periodo"] ?>%)
                  </button>
                </li>
              <?php } ?>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="btn-<?= $llave_curso_final ?>-final" data-bs-toggle="tab"
                  data-bs-target="#contend-<?= $llave_curso_final ?>-final" type="button" role="tab"
                  aria-controls="contend-<?= $llave_curso_final ?>-final" aria-selected="true">FINAL</button>
              </li>
            </ul>
            <div id="tab-content-<?= $estudiante["mat_id"] ?>" class="tab-content">



              <?php include(ROOT_PATH . "/main-app/compartido/historial-notas-periodos.php"); ?>



              <div class="tab-pane panel-<?= $estudiante["mat_id"] ?> fade show" id="contend-<?= $llave_curso ?>-final" role="tabpanel"
                aria-labelledby="btn-<?= $llave_curso ?>-final">
                <?php if ($llave_curso == $estudiante["mat_id"] . "-" . $year . "-" . $grado . "-" . $grupo) { ?>
                  <?php include(ROOT_PATH . "/main-app/compartido/historial-notas-final.php"); ?>
                <?php } ?>
              </div>

            </div>
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

    async function selecionarCurso(estudiante, year, curso, grupo) {
      console.log('-Estudainte:' + estudiante + ' -Año:' + year + ' -Cruso:' + curso + ' Grupo:' + grupo);

      const elements = document.querySelectorAll(`[id*="btn-periodos-${estudiante}"]`);


      elements.forEach(element => {
        const target = element.getAttribute('data-bs-target');
        const llaves = target.split("-");



        let periodo = llaves[5];
        if (target.includes(estudiante)) {
          let newPart = '#contend-' + estudiante + '-' + year + '-' + curso + '-' + grupo + '-' + periodo;
          element.setAttribute('data-bs-target', newPart)
        }
        const btnFinal = document.getElementById(`btn-${estudiante}-${year}-${curso}-${grupo}-final`);
        //targe de la nota final
        if (btnFinal) {
          const targetFinal = btnFinal.getAttribute('data-bs-target');
          if (targetFinal.includes(estudiante)) {
            let newPart = '#contend-' + estudiante + '-' + year + '-' + curso + '-' + grupo + '-final';
            btnFinal.setAttribute('data-bs-target', newPart)
          }

        }

      });

      // Simula un clic en el enlace de la pestaña
      let otrosTabs = document.querySelectorAll(".panel-"+ estudiante);
      otrosTabs.forEach(el => {
        el.classList.remove("show", "active");
      });

      const tabLink = document.getElementById(`btn-periodos-${estudiante}-1`);
      // Simula un clic en el enlace de la pestaña
      if (tabLink) {
        tabLink.click();
      }

      contendP1 = document.getElementById("contend-" + estudiante + '-' + year + '-' + curso + '-' + grupo + '-1');
      if (contendP1 !== null && contendP1.children.length > 0) {
        console.log("El contend tiene contenido.");
      } else {        
        try {
          var overlay = document.getElementById("overlay");

          if (overlay) {
            document.getElementById("overlay").style.display = "flex";
          }
          var data = {
            "estudiante": estudiante,
            "year": year,
            "grado": curso,
            "grupo": grupo
          };

          resultado = await metodoFetchAsync("historial-notas-ajax.php", data, 'json', false);

          resultData = resultado["data"];
          if (resultData["ok"]) {
            contend = document.getElementById("tab-content-"+ estudiante);
            var sendData = {
              "estudiante": estudiante,
              "periodos": <?php echo $config['conf_periodos_maximos'] ?>,
              "year": year,
              "grado": curso,
              "grupo": grupo,
              "data": resultData["data"]
            };
            resultHtml = await metodoFetchAsync("historial-notas-periodos.php", sendData, 'html', false);
            contend.insertAdjacentHTML("beforeend", resultHtml["data"]);
            contendP1 = document.getElementById("contend-" + estudiante + '-' + year + '-' + curso + '-' + grupo + '-1');
            contendP1.classList.add("show", "active");
            if (overlay) {
              document.getElementById("overlay").style.display = "none";
            }
            /*
              contendFinal = document.getElementById(`contend-${estudiante}-${year}-${curso}-${grupo}-final`);
  
  
              resultHtml = await metodoFetchAsync("historial-notas-final.php", sendData, 'html', false);
              contendFinal.innerHTML = resultHtml["data"];*/
          }


        } catch (error) {
          if (overlay) {
            document.getElementById("overlay").style.display = "none";
          }
        }
      }


    }

    document.addEventListener("DOMContentLoaded", function () {
      var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
      var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
    integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
    integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13"
    crossorigin="anonymous"></script>

  <!--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
    integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
    crossorigin="anonymous"></script> -->

</body>