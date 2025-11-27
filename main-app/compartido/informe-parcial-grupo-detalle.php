<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0229';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
  echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
  exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");

// Obtener período
$cPeriodo = $config[2];
if (isset($_REQUEST["periodo"])) {
  $cPeriodo = $_REQUEST["periodo"];
}

// Validar parámetros requeridos
if (empty($_REQUEST["curso"]) || empty($_REQUEST["grupo"])) {
  echo '<script type="text/javascript">alert("Error: Faltan parámetros requeridos (curso o grupo)"); window.close();</script>';
  exit();
}

$filtroAdicional = "AND mat_grado='" . $_REQUEST["curso"] . "' AND mat_grupo='" . $_REQUEST["grupo"] . "' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
$cursoActual = GradoServicios::consultarCurso($_REQUEST["curso"]);
$matriculadosPorCurso = Estudiantes::listarEstudiantesEnGrados($filtroAdicional, "", $cursoActual, $_REQUEST["grupo"]);

// Pre-cargar todos los estudiantes en un array y obtener sus IDs
$listaEstudiantes = [];
$idsEstudiantes = [];
$filtroORComun = '';
$esGradoIndividual = ($cursoActual["gra_tipo"] == GRADO_INDIVIDUAL);

// Guardar estudiantes y determinar si todos tienen el mismo filtro OR
while ($est = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {
  $listaEstudiantes[] = $est;
  $idsEstudiantes[] = $est['mat_id'];
  
  // Si es grado individual, construir filtro OR para este estudiante
  if ($esGradoIndividual) {
    $filtroOREst = " OR (car_curso='" . $est['matcur_id_curso'] . "' AND car_grupo='" . $est['matcur_id_grupo'] . "')";
    // Si es el primer estudiante, guardar el filtro como común
    if (empty($filtroORComun)) {
      $filtroORComun = $filtroOREst;
    } elseif ($filtroORComun != $filtroOREst) {
      // Si el filtro es diferente, marcar que no se puede optimizar
      $filtroORComun = null;
      break; // No podemos optimizar si los filtros son diferentes
    }
  }
}

// Pre-cargar todas las cargas de todos los estudiantes en una sola consulta
// Solo si todos tienen el mismo filtro OR (o no es grado individual)
$cargasMapa = [];
if (!empty($idsEstudiantes) && ($filtroORComun !== null || !$esGradoIndividual)) {
  $cargasMapa = CargaAcademica::traerInformeParcialTodasMapa(
    $config,
    $idsEstudiantes,
    $_REQUEST["curso"],
    $_REQUEST["grupo"],
    $cPeriodo,
    $filtroORComun ?? '',
    ""
  );
}

// Pre-cargar notas cualitativas si están habilitadas
$notasCualitativasCache = [];
if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
  $consultaNotasTipo = mysqli_query($conexion, 
    "SELECT notip_desde, notip_hasta, notip_nombre 
     FROM ".BD_ACADEMICA.".academico_notas_tipos 
     WHERE notip_categoria='".$config['conf_notas_categoria']."' 
     AND institucion=".$config['conf_id_institucion']." 
     AND year='".$_SESSION["bd"]."'
     ORDER BY notip_desde ASC");
  
  while ($notaTipo = mysqli_fetch_array($consultaNotasTipo, MYSQLI_BOTH)) {
    for ($i = $notaTipo['notip_desde']; $i <= $notaTipo['notip_hasta']; $i += 0.1) {
      $key = number_format((float)$i, 1, '.', '');
      if (!isset($notasCualitativasCache[$key])) {
        $notasCualitativasCache[$key] = $notaTipo['notip_nombre'];
      }
    }
  }
}

// Función auxiliar para obtener color de nota
function obtenerColorNota($nota, $notaMinima) {
  if ($nota < $notaMinima) {
    return '#dc3545'; // Rojo para perdida
  }
  return '#28a745'; // Verde para ganada
}

// Función auxiliar para obtener nota cualitativa
function obtenerNotaCualitativa($nota, $cache, $config, $conexion) {
  if ($config['conf_forma_mostrar_notas'] != CUALITATIVA) {
    return $nota;
  }
  
  $notaRedondeada = number_format((float)$nota, 1, '.', '');
  
  if (isset($cache[$notaRedondeada])) {
    return $cache[$notaRedondeada];
  }
  
  // Si no está en el cache, devolver la nota numérica
  // (el cache debería tener todos los valores posibles)
  return $nota;
}
?>

<head>
  <title>SINTIA - INFORME PARCIAL GRUPO</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>

<body style="font-family:Arial;">
  <?php
  $nombreInforme = "INFORME PARCIAL " . "<br>" . " PERIODO:" . Utilidades::getToString($cPeriodo) . "<br>" . Utilidades::getToString($config["conf_fecha_parcial"]);
  include("../compartido/head-informes.php") 
  ?>
  
  <!-- Aclaración sobre las notas -->
  <div align="center" style="margin:20px 0; padding:12px 15px; background:#fff3cd; border-left:4px solid #ffc107; border-radius:4px; font-size:11px; font-style:italic; color:#555; max-width:900px; margin-left:auto; margin-right:auto;">
    <strong style="color:#856404; font-style:normal;">⚠️ Importante:</strong> Las notas mostradas en este informe están basadas únicamente en las actividades calificadas por el docente que lleva actualmente el estudiante. <strong style="color:#856404; font-style:normal;">No se consideran recuperaciones de períodos ni nivelaciones de asignaturas.</strong>
  </div>

  <?php
  $hayEstudiantes = false;
  foreach ($listaEstudiantes as $matriculadosDatos) {
    $hayEstudiantes = true;
    
    // Obtener cargas del mapa pre-cargado o hacer consulta individual si no se pudo optimizar
    $cargasEstudiante = [];
    if (!empty($cargasMapa) && isset($cargasMapa[$matriculadosDatos['mat_id']])) {
      // Usar datos del mapa optimizado
      $cargasEstudiante = $cargasMapa[$matriculadosDatos['mat_id']];
    } else {
      // Fallback: consulta individual (para grados individuales con filtros diferentes)
      $filtroOR = '';
      if ($esGradoIndividual) {
        $filtroOR = " OR (car_curso='" . $matriculadosDatos['matcur_id_curso'] . "' AND car_grupo='" . $matriculadosDatos['matcur_id_grupo'] . "')";
      }
      
      $cCargas = CargaAcademica::consultaInformeParcialTodas(
        $config, 
        $matriculadosDatos['mat_id'], 
        $matriculadosDatos['mat_grado'], 
        $matriculadosDatos['mat_grupo'], 
        $cPeriodo,
        $filtroOR
      );
      
      while ($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)) {
        $cargasEstudiante[] = $rCargas;
      }
    }
    
    if (!empty($cargasEstudiante)) {
      // Inicializar variables para este estudiante
      $materiasDividir = 0;
      $promedioG = 0;
      $todasLasCargas = [];
      
      // Procesar todas las cargas del estudiante
      foreach ($cargasEstudiante as $rCargas) {
        $colorDefinitiva = obtenerColorNota($rCargas['nota'], $config['conf_nota_minima_aprobar']);
        $definitivaFinal = obtenerNotaCualitativa($rCargas['nota'], $notasCualitativasCache, $config, $conexion);
        
        $todasLasCargas[] = [
          'car_id' => $rCargas['car_id'],
          'docente' => UsuariosPadre::nombreCompletoDelUsuario($rCargas),
          'materia' => $rCargas['mat_nombre'],
          'porcentaje' => $rCargas['porcentaje'],
          'nota' => $definitivaFinal,
          'nota_numerica' => $rCargas['nota'],
          'color' => $colorDefinitiva
        ];
        
        // Solo contar materias con porcentaje > 0 y que sumen al promedio
        if ($rCargas['porcentaje'] > 0 && (!empty($rCargas['mat_sumar_promedio']) && $rCargas['mat_sumar_promedio'] == SI)) {
          $materiasDividir++;
          $promedioG += $rCargas['nota'];
        }
      }
      
      // Calcular promedio para este estudiante
      if ($materiasDividir > 0) {
        $promedioG = round(($promedioG / $materiasDividir), $config['conf_decimales_notas']);
      }
      
      $promedioGFinal = obtenerNotaCualitativa($promedioG, $notasCualitativasCache, $config, $conexion);
      $colorPromedio = obtenerColorNota($promedioG, $config['conf_nota_minima_aprobar']);
  ?>
    <div align="center" style="margin-bottom:20px; margin-top:30px;">
      <strong>ESTUDIANTE: <?= Estudiantes::NombreCompletoDelEstudiante($matriculadosDatos); ?></strong>
    </div>
    
    <!-- BEGIN TABLE DATA -->
    <table width="100%" cellspacing="2" cellpadding="2" rules="all" style="border:solid; border-color:<?= $Plataforma->colorUno; ?>; font-size:10px; margin-bottom:30px;">
      <tr style="font-weight:bold; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
        <th style="text-align:center;">Cod</th>
        <th style="text-align:center;">Docente</th>
        <th style="text-align:center;">Asignatura</th>
        <th style="text-align:center;">%</th>
        <th style="text-align:center;">Nota</th>
      </tr>
      <tbody>
        <?php
        foreach ($todasLasCargas as $carga) {
        ?>
          <tr id="data1" class="odd gradeX">
            <td style="text-align:center;"><?= htmlspecialchars($carga['car_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?= htmlspecialchars($carga['docente'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?= htmlspecialchars($carga['materia'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="text-align:center;"><?= number_format((float)$carga['porcentaje'], 0); ?>%</td>
            <td style="color:<?= $carga['color']; ?>; text-align:center; font-weight:bold;"><?= htmlspecialchars($carga['nota'], ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
        <?php
        }
        ?>
        <!-- Fila de promedio -->
        <?php if ($materiasDividir > 0): ?>
        <tr style="font-weight:bold; background:#f0f0f0;">
          <td colspan="4" style="text-align:right; padding-right:10px;">PROMEDIO:</td>
          <td style="color:<?= $colorPromedio; ?>; text-align:center;"><?= htmlspecialchars($promedioGFinal, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  <?php
    } else {
      // Mostrar mensaje si no hay cargas para este estudiante
  ?>
    <div align="center" style="margin-bottom:20px; margin-top:30px;">
      <strong>ESTUDIANTE: <?= Estudiantes::NombreCompletoDelEstudiante($matriculadosDatos); ?></strong>
    </div>
    <div align="center" style="margin-bottom:30px; color:#888; font-style:italic;">
      No hay calificaciones registradas para este período
    </div>
  <?php
    }
  }
  
  if (!$hayEstudiantes) {
    echo '<div align="center" style="margin:30px; color:#888;">No se encontraron estudiantes para este curso y grupo.</div>';
  }
  ?>
  
  <?php
  include("../compartido/footer-informes.php");
  include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php"); 
  ?>
</body>

</html>
