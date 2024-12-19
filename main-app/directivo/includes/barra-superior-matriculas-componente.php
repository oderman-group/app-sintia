<?php
require_once(ROOT_PATH."/main-app/class/componentes/ComponenteFiltros.php");
$curso = '';
$cursoActual=null;

if (!empty($_GET['curso'])) {
  $curso = base64_decode($_GET['curso']);
  $filtro .= " AND mat_grado='" . $curso . "'";
  $cursoActual=GradoServicios::consultarCurso($curso);
  if (!empty($cursoActual) && $cursoActual["gra_tipo"] == GRADO_INDIVIDUAL) {
    $filtro = "";
  }
}

$estadoM = '';
if (!empty($_GET['estadoM'])) {
  $estadoM = base64_decode($_GET['estadoM']);
  $filtro .= " AND mat_estado_matricula='" . $estadoM . "'";  
}

$grupo = "";
if (!empty($_GET['grupo'])) {
  $grupo = base64_decode($_GET['grupo']);
  $filtro .= " AND mat_grupo ='" .  $grupo . "'";
}

$opciones[0] = [
  ComponenteFiltro::COMPB_OPCIONES_TEXTO   => 'Promedios estudiantiles',
  ComponenteFiltro::COMPB_OPCIONES_URL     => 'estudiantes-promedios.php',
  ComponenteFiltro::COMPB_OPCIONES_PERMISO => Modulos::validarSubRol(['DT0002'])
];

$opciones[1] = [
  ComponenteFiltro::COMPB_OPCIONES_TEXTO   => 'Menú matriculas',
  ComponenteFiltro::COMPB_OPCIONES_PERMISO => Modulos::validarSubRol(['DT0077', 'DT0080', 'DT0075']),
  ComponenteFiltro::COMPB_OPCIONES_PAGINAS => $paginas = [
    [
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO   => 'Importar matrículas desde Excel',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL     => 'estudiantes-importar-excel.php',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_PERMISO => Modulos::validarSubRol(['DT0077'])
    ],
    [
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO   => 'Consolidado final',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL     => 'estudiantes-consolidado-final.php',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_PERMISO => Modulos::validarSubRol(['DT0080'])
    ],
    [
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO   => 'Nivelaciones',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL     => 'estudiantes-nivelaciones.php',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_PERMISO => Modulos::validarSubRol(['DT0075'])
    ]
  ]
];
$opciones[3] = [
  ComponenteFiltro::COMPB_OPCIONES_TEXTO   => 'Más opciones',
  ComponenteFiltro::COMPB_OPCIONES_PERMISO => Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0212', 'DT0213', 'DT0214', 'DT0215', 'DT0175', 'DT0216', 'DT0149']),
  ComponenteFiltro::COMPB_OPCIONES_PAGINAS => $paginas = [
    [
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO   => 'Matricular a todos',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL     => 'javascript:void(0);',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_PERMISO => Modulos::validarSubRol(['DT0212']),
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_ACTION  => 'sweetConfirmacion("Alerta!","Deseas ejecutar esta accion?","question","estudiantes-matricular-todos.php")'
    ],
    [
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO   => 'Cancelar a todos',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL     => 'javascript:void(0);',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_PERMISO => Modulos::validarSubRol(['DT0213']),
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_ACTION  => 'sweetConfirmacion("Alerta!","Deseas ejecutar esta accion?","question","estudiantes-matriculas-cancelar.php")'
    ],
    [
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO   => 'Asignar a todos al grupo A',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL     => 'javascript:void(0);',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_PERMISO => Modulos::validarSubRol(['DT0214']),
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_ACTION  => 'sweetConfirmacion("Alerta!","Deseas ejecutar esta accion?","question","estudiantes-grupoa-todos.php")',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_DIVIDER => 'S'
    ],
    [
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO   => 'Remover estudiantes Inactivos este año',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL     => 'javascript:void(0);',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_PERMISO => Modulos::validarSubRol(['DT0215']),
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_ACTION  => 'sweetConfirmacion("Alerta!","Esta opción removerá a todos lo estudiantes que no estén en estado Matriculado, desea continuar?","question","estudiantes-inactivos-remover.php")',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_DIVIDER => 'S'
    ],
    [
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO   => 'Colocar documento como usuario de acceso',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL     => 'javascript:void(0);',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_PERMISO => Modulos::validarSubRol(['DT0175']),
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_ACTION  => 'sweetConfirmacion("Alerta!","Deseas ejecutar esta accion?","question","estudiantes-documento-usuario-actualizar.php")'
    ],
    [
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO   => 'Verificar y generar credenciales a estudiantes',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL     => 'javascript:void(0);',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_PERMISO => Modulos::validarSubRol(['DT0216']),
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_ACTION  => 'sweetConfirmacion("Alerta!","Deseas ejecutar esta accion?","question","estudiantes-crear-usuarios.php")'
    ],
    [
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO   => 'Generar Folios',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL     => 'filtro-general-folio.php',
      ComponenteFiltro::COMPB_OPCIONES_PAGINAS_PERMISO => Modulos::validarSubRol(['DT0149'])
    ]

  ]
];
$grados = Grados::listarGrados(1);
$count = 0;
while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
  $filtroCurso[$count] = [
    ComponenteFiltro::COMPB_FILTRO_LISTA_ID    => $grado['gra_id'],
    ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => $grado['gra_nombre'],
    ComponenteFiltro::COMPB_FILTRO_LISTA_URL   => $_SERVER['PHP_SELF'] . "?estadoM=" . base64_encode($estadoM) . "&curso=" . base64_encode($grado['gra_id'])."&grupo=". base64_encode($grupo) 
  ];
  $count++;
}
$filtroCurso[$count] = [
  ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => 'VER TODOS',
  ComponenteFiltro::COMPB_FILTRO_LISTA_URL   => $_SERVER['PHP_SELF']. "?estadoM=" . base64_encode($estadoM)."&grupo=". base64_encode($grupo)  . "&curso=",
  ComponenteFiltro::COMPB_FILTRO_LISTA_STYLE => 'font-weight: bold; text-align: center;'
];

$grupos = Grupos::listarGrupos();
$count = 0;
while ($gru = mysqli_fetch_array($grupos, MYSQLI_BOTH)) {
    $filtroGrupo[$count] = [
        ComponenteFiltro::COMPB_FILTRO_LISTA_ID => $gru['gru_id'],
        ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => $gru['gru_nombre'],
        ComponenteFiltro::COMPB_FILTRO_LISTA_URL => $_SERVER['PHP_SELF'] . "?estadoM=" . base64_encode($estadoM) . "&curso=" . base64_encode($curso)."&grupo=" . base64_encode($gru['gru_id']) 
    ];
    $count++;
}
$filtroGrupo[$count] = [
    ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => 'VER TODOS',
    ComponenteFiltro::COMPB_FILTRO_LISTA_URL => $_SERVER['PHP_SELF'] . "?estadoM=" . base64_encode($estadoM) . "&curso=" . base64_encode($curso) ."&grupo="
];

$count = 0;
$estadoSelect=null;
foreach ($estadosMatriculasEstudiantes as $clave => $valor) {
  $listaEstado[$count] = [
    ComponenteFiltro::COMPB_FILTRO_LISTA_ID    => $clave,
    ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => $valor,
    ComponenteFiltro::COMPB_FILTRO_LISTA_URL   => $_SERVER['PHP_SELF'] . "?estadoM=" . base64_encode($clave) ."&grupo=". base64_encode($grupo) . "&curso=" . base64_encode($curso)    
  ];
  $count++;
}
$listaEstado[$count] = [
  ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => 'VER TODOS',
  ComponenteFiltro::COMPB_FILTRO_LISTA_URL   => $_SERVER['PHP_SELF']. "?estadoM=&curso=" . base64_encode($curso)."&grupo=". base64_encode($grupo) ,
  ComponenteFiltro::COMPB_FILTRO_LISTA_STYLE => 'font-weight: bold; text-align: center;'
];


$filtros[0] = [
  ComponenteFiltro::COMPB_FILTRO_GET   => 'curso',
  ComponenteFiltro::COMPB_FILTRO_TEXTO => 'Filtrar por curso',
  ComponenteFiltro::COMPB_FILTRO_LISTA => $filtroCurso,
];

$filtros[1] = [
  ComponenteFiltro::COMPB_FILTRO_GET => 'grupo',
  ComponenteFiltro::COMPB_FILTRO_TEXTO => 'Grupo',
  ComponenteFiltro::COMPB_FILTRO_LISTA => $filtroGrupo
];

$filtros[2] = [
  ComponenteFiltro::COMPB_FILTRO_GET   => 'estadoM',
  ComponenteFiltro::COMPB_FILTRO_TEXTO => 'Filtrar por estados',
  ComponenteFiltro::COMPB_FILTRO_LISTA => $listaEstado,
];

$barraSuperior = new ComponenteFiltro('matriculas', 'filter-matriculas.php', 'matriculas-tbody.php', $filtros, $opciones);
$barraSuperior->generarComponente();
