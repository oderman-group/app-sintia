<?php
require_once(ROOT_PATH . "/main-app/class/componentes/ComponenteFiltros.php");

$cargaGet = '';
if (!empty($_GET['carga'])) {
  $cargaGet = base64_decode($_GET['carga']);
  $filtro .= " AND dn_id_carga='" . $cargaGet . "'";
}

$filtros = array();
if (!empty($_GET['curso'])) {
  $cursoGet = base64_decode($_GET['curso']);
  $filtro .= " AND car_curso='" . $cursoGet . "'";

  $grupoGet = '';
  if (!empty($_GET['grupo'])) {
    $grupoGet = base64_decode($_GET['grupo']);
    $filtro .= " AND car_grupo='" . $grupoGet . "'";
  }

  $asignatura = '';
  if (!empty($_GET['asignatura'])) {
    $asignatura = base64_decode($_GET['asignatura']);
    $filtro .= " AND car_materia='" . $asignatura . "'";
  }

  $grupos = Grupos::listarGrupos();
  $count = 0;
  while ($grupo = mysqli_fetch_array($grupos, MYSQLI_BOTH)) {
    $filtroGrupo[$count] = [
      ComponenteFiltro::COMPB_FILTRO_LISTA_ID       => $grupo['gru_id'],
      ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO    => $grupo['gru_nombre'],
      ComponenteFiltro::COMPB_FILTRO_LISTA_URL      => $_SERVER['PHP_SELF'] . "?asignatura=" . base64_encode($asignatura) . "&grupo=" . base64_encode($grupo['gru_id'])
    ];
    $count++;
  }
  $filtroGrupo[$count] = [
    ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO      => 'VER TODOS',
    ComponenteFiltro::COMPB_FILTRO_LISTA_URL        => $_SERVER['PHP_SELF'] . "?asignatura=" . base64_encode($asignatura) . "&grupo=",
    ComponenteFiltro::COMPB_FILTRO_LISTA_STYLE      => 'font-weight: bold; text-align: center;'
  ];

  $materias = Asignaturas::consultarTodasAsignaturas($conexion, $config);
  $count = 0;
  while ($materia = mysqli_fetch_array($materias, MYSQLI_BOTH)) {
    $filtroAsignatura[$count] = [
      ComponenteFiltro::COMPB_FILTRO_LISTA_ID       => $materia['mat_id'],
      ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO    => $materia['mat_nombre'],
      ComponenteFiltro::COMPB_FILTRO_LISTA_URL      => $_SERVER['PHP_SELF'] . "?asignatura=" . base64_encode($materia['mat_id']) . "&grupo=" . base64_encode($grupoGet)
    ];
    $count++;
  }
  $filtroAsignatura[$count] = [
    ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO      => 'VER TODOS',
    ComponenteFiltro::COMPB_FILTRO_LISTA_URL        => $_SERVER['PHP_SELF'] . "?asignatura=&grupo=" . base64_encode($grupoGet),
    ComponenteFiltro::COMPB_FILTRO_LISTA_STYLE      => 'font-weight: bold; text-align: center;'
  ];


  $filtros[0] = [
    ComponenteFiltro::COMPB_FILTRO_GET              => 'grupo',
    ComponenteFiltro::COMPB_FILTRO_TEXTO            => 'Filtrar por Grupo',
    ComponenteFiltro::COMPB_FILTRO_LISTA            => $filtroGrupo,
  ];

  $filtros[1] = [
    ComponenteFiltro::COMPB_FILTRO_GET              => 'asignatura',
    ComponenteFiltro::COMPB_FILTRO_TEXTO            => 'Filtrar por Asignatura',
    ComponenteFiltro::COMPB_FILTRO_LISTA            => $filtroAsignatura,
  ];
}

$barraSuperior = new ComponenteFiltro('comportamiento', 'filter-comportamiento.php', 'comportamiento-tbody.php', $filtros, "", "", false);
$barraSuperior->generarComponente();