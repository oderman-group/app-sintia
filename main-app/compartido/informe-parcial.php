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

//ESTUDIANTE ACTUAL
$datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($estudiante);

// OPTIMIZACIÓN: Pre-cargar notas cualitativas si están habilitadas
$notasCualitativasCache = [];
if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
  // Consultar todas las notas cualitativas de una vez
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

// Función auxiliar para obtener nota cualitativa (optimizada con cache)
function obtenerNotaCualitativa($nota, $cache, $config, $conexion) {
  if ($config['conf_forma_mostrar_notas'] != CUALITATIVA) {
    return $nota;
  }
  
  $notaRedondeada = number_format((float)$nota, 1, '.', '');
  
  // Intentar desde cache primero
  if (isset($cache[$notaRedondeada])) {
    return $cache[$notaRedondeada];
  }
  
  // Si no está en cache, buscar directamente
  $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota);
  return !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : $nota;
}

// Función auxiliar para procesar cargas y evitar duplicación de código
function procesarCargas($cCargas, $config, $notasCualitativasCache, &$promedioG, &$materiasDividir, $conexion) {
  $resultados = [];
  
  if (!empty($cCargas)) {
    while ($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)) {
      $colorDefinitiva = obtenerColorNota($rCargas['nota'], $config['conf_nota_minima_aprobar']);
      
      $definitivaFinal = obtenerNotaCualitativa($rCargas['nota'], $notasCualitativasCache, $config, $conexion);
      
      // Determinar si esta materia suma al promedio
      $sumaPromedio = ($rCargas['porcentaje'] > 0 && $rCargas['mat_sumar_promedio'] == SI);
      
      $resultados[] = [
        'car_id' => $rCargas['car_id'],
        'docente' => UsuariosPadre::nombreCompletoDelUsuario($rCargas),
        'materia' => $rCargas['mat_nombre'],
        'porcentaje' => $rCargas['porcentaje'],
        'nota' => $definitivaFinal,
        'nota_numerica' => $rCargas['nota'], // Guardar nota numérica original para el promedio
        'color' => $colorDefinitiva,
        'sumar_promedio' => $rCargas['mat_sumar_promedio'] == SI,
        'no_suma_promedio' => !$sumaPromedio // Flag para aplicar estilo visual
      ];
      
      // CORRECCIÓN: SOLO SE SUMAN Y CUENTAN LAS MATERIAS QUE TIENEN NOTAS (porcentaje > 0) 
      // Y QUE DEBEN SUMARSE AL PROMEDIO (mat_sumar_promedio == SI)
      // Ambas condiciones deben cumplirse
      if ($rCargas['porcentaje'] > 0 && $rCargas['mat_sumar_promedio'] == SI) {
        $materiasDividir++;
        $promedioG += $rCargas['nota'];
      }
    }
    $cCargas->free();
  }
  
  return $resultados;
}

// OPTIMIZACIÓN: Procesar todas las cargas
$todasLasCargas = [];
$promedioG = 0;
$materiasDividir = 0;

// Cargas normales
$cCargas = CargaAcademica::consultaInformeParcialTodas($config, $datosEstudianteActual['mat_id'], $datosEstudianteActual['mat_grado'], $datosEstudianteActual['mat_grupo'], $cPeriodo);
$cargasNormales = procesarCargas($cCargas, $config, $notasCualitativasCache, $promedioG, $materiasDividir, $conexion);
$todasLasCargas = array_merge($todasLasCargas, $cargasNormales);

// MEDIA TECNICA (si existe el módulo)
if (array_key_exists(10, $_SESSION["modulos"])) {
  require_once(ROOT_PATH . "/main-app/class/servicios/MediaTecnicaServicios.php");
  $consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config, $year, $estudiante);
  
  if (!empty($consultaEstudianteActualMT) && mysqli_num_rows($consultaEstudianteActualMT) > 0) {
    while ($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)) {
      if (!empty($datosEstudianteActualMT)) {
        $cCargasMT = CargaAcademica::consultaInformeParcialTodas($config, $datosEstudianteActualMT['mat_id'], $datosEstudianteActualMT['matcur_id_curso'], $datosEstudianteActualMT['matcur_id_grupo'], $cPeriodo);
        $cargasMT = procesarCargas($cCargasMT, $config, $notasCualitativasCache, $promedioG, $materiasDividir, $conexion);
        $todasLasCargas = array_merge($todasLasCargas, $cargasMT);
      }
    }
    $consultaEstudianteActualMT->free();
  }
}

// Calcular promedio final
if ($materiasDividir > 0) {
  $promedioG = round(($promedioG / $materiasDividir), 1);
}

$promedioGFinal = obtenerNotaCualitativa($promedioG, $notasCualitativasCache, $config, $conexion);
$colorPromedio = obtenerColorNota($promedioG, $config['conf_nota_minima_aprobar']);

// Formatear fecha en español
$meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$fechaHoy = date("d") . " de " . $meses[date("n")-1] . " de " . date("Y");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SINTIA - INFORME PARCIAL</title>
  <link rel="shortcut icon" href="../sintia-icono.png" />
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 12px;
      line-height: 1.6;
      color: #333;
      background: #f5f5f5;
      padding: 20px;
    }
    
    .informe-container {
      max-width: 900px;
      margin: 0 auto;
      background: #fff;
      padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-radius: 8px;
    }
    
    .informe-header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 3px solid #6017dc;
    }
    
    .informe-header h1 {
      font-size: 22px;
      color: #6017dc;
      font-weight: 700;
      margin-bottom: 10px;
    }
    
    .informe-header .subtitulo {
      font-size: 16px;
      color: #666;
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .informe-header .fecha {
      font-size: 13px;
      color: #888;
      margin-bottom: 15px;
    }
    
    .logo-container {
      margin: 15px 0;
    }
    
    .logo-container img {
      max-height: 120px;
      max-width: 100%;
      object-fit: contain;
    }
    
    .logo-container-full {
      width: 100%;
    }
    
    .descripcion-parcial {
      font-size: 13px;
      color: #555;
      line-height: 1.8;
      margin: 15px 0;
      padding: 15px;
      background: #f8f9fa;
      border-left: 4px solid #6017dc;
      border-radius: 4px;
    }
    
    .estudiante-info {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      padding: 15px 20px;
      border-radius: 6px;
      margin: 20px 0;
      font-size: 14px;
      font-weight: 600;
    }
    
    .table-container {
      margin: 25px 0;
      overflow-x: auto;
    }
    
    table.informe-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    table.informe-table thead {
      background: linear-gradient(135deg, #6017dc 0%, #764ba2 100%);
      color: #fff;
    }
    
    table.informe-table th {
      padding: 12px 10px;
      text-align: center;
      font-weight: 600;
      font-size: 12px;
      border: 1px solid rgba(255,255,255,0.2);
    }
    
    table.informe-table tbody tr {
      border-bottom: 1px solid #e0e0e0;
      transition: background-color 0.2s;
    }
    
    table.informe-table tbody tr:nth-child(even) {
      background-color: #f8f9fa;
    }
    
    table.informe-table tbody tr:hover {
      background-color: #e8f4f8;
    }
    
    /* Estilo para materias que NO suman al promedio */
    table.informe-table tbody tr.no-suma-promedio {
      opacity: 0.7;
      background-color: #f5f5f5;
      border-left: 4px solid #ffc107;
    }
    
    table.informe-table tbody tr.no-suma-promedio:hover {
      background-color: #eeeeee;
    }
    
    .badge-no-promedio {
      display: block;
      font-size: 9px;
      padding: 3px 8px;
      margin-top: 4px;
      background: #ffc107;
      color: #000;
      border-radius: 12px;
      font-weight: 600;
      text-transform: uppercase;
      width: fit-content;
      white-space: nowrap;
    }
    
    table.informe-table td {
      padding: 10px;
      border: 1px solid #e0e0e0;
    }
    
    table.informe-table td:nth-child(1) {
      text-align: center;
      font-weight: 600;
      color: #6017dc;
      width: 60px;
    }
    
    table.informe-table td:nth-child(2) {
      width: 30%;
    }
    
    table.informe-table td:nth-child(3) {
      width: 35%;
      font-weight: 500;
    }
    
    table.informe-table td:nth-child(4) {
      text-align: center;
      width: 80px;
      font-weight: 600;
    }
    
    table.informe-table td:nth-child(5) {
      text-align: center;
      font-size: 14px;
      font-weight: 700;
      width: 100px;
    }
    
    table.informe-table tfoot {
      background: #f8f9fa;
      border-top: 3px solid #6017dc;
    }
    
    table.informe-table tfoot td {
      padding: 12px;
      font-weight: 700;
      font-size: 13px;
    }
    
    table.informe-table tfoot td:first-child {
      text-align: right;
      font-size: 14px;
      color: #555;
    }
    
    .firmas-section {
      margin-top: 40px;
      padding-top: 30px;
      border-top: 2px dashed #ccc;
    }
    
    .firmas-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 50px;
    }
    
    .firma-box {
      text-align: center;
      max-width: 250px;
      font-size: 12px;
    }
    
    .firma-line {
      border-top: 2px solid #333;
      margin: 10px 0 5px 0;
      padding-top: 5px;
    }
    
    .texto-acudiente {
      margin: 30px 0;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 6px;
      font-size: 12px;
      line-height: 1.8;
    }
    
    .texto-acudiente .linea-firma {
      border-top: 2px solid #333;
      margin-top: 10px;
      padding-top: 5px;
      text-align: center;
      font-weight: 600;
    }
    
    .versiculo {
      text-align: center;
      margin: 20px 0;
      font-size: 13px;
      font-style: italic;
      color: #666;
    }
    
    .footer-sintia {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #ddd;
      font-size: 10px;
      color: #888;
    }
    
    .footer-sintia img {
      max-width: 150px;
      margin-bottom: 10px;
    }
    
    .botones-accion {
      text-align: center;
      margin-bottom: 20px;
      padding: 15px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .btn-accion {
      display: inline-block;
      padding: 10px 20px;
      margin: 0 5px;
      background: #6017dc;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      font-weight: 600;
      transition: all 0.3s;
      border: none;
      cursor: pointer;
      font-size: 13px;
    }
    
    .btn-accion:hover {
      background: #4a12b3;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .btn-accion.secondary {
      background: #6c757d;
    }
    
    .btn-accion.secondary:hover {
      background: #5a6268;
    }
    
    /* Estilos para impresión */
    @media print {
      body {
        background: #fff;
        padding: 0;
      }
      
      .informe-container {
        box-shadow: none;
        padding: 20px;
        max-width: 100%;
      }
      
      .botones-accion {
        display: none;
      }
      
      table.informe-table {
        box-shadow: none;
      }
      
      table.informe-table tbody tr {
        page-break-inside: avoid;
      }
      
      .firmas-section {
        page-break-inside: avoid;
      }
      
      @page {
        margin: 1cm;
      }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      body {
        padding: 10px;
      }
      
      .informe-container {
        padding: 15px;
      }
      
      .firmas-row {
        flex-direction: column;
        gap: 30px;
      }
      
      .firma-box {
        max-width: 100%;
      }
      
      table.informe-table {
        font-size: 11px;
      }
      
      table.informe-table th,
      table.informe-table td {
        padding: 8px 5px;
      }
    }
  </style>
</head>
<body>
  <div class="botones-accion no-print">
    <button class="btn-accion" onclick="window.print()">
      <i class="fa fa-print"></i> Imprimir
    </button>
    <button class="btn-accion secondary" onclick="window.close()">
      <i class="fa fa-times"></i> Cerrar
    </button>
  </div>
  
  <div class="informe-container">
    <div class="informe-header">
      <h1><?= htmlspecialchars($informacion_inst["info_nombre"], ENT_QUOTES, 'UTF-8'); ?></h1>
      <div class="subtitulo">INFORME PARCIAL - PERÍODO <?= $cPeriodo; ?></div>
      <div class="fecha">Fecha del informe: <?= $config["conf_fecha_parcial"]; ?></div>
      
      <div class="logo-container <?= ($config['conf_id_institucion'] == ICOLVEN) ? 'logo-container-full' : ''; ?>">
        <img src="../files/images/logo/<?= htmlspecialchars($informacion_inst["info_logo"], ENT_QUOTES, 'UTF-8'); ?>" 
             alt="Logo de la institución"
             onerror="this.style.display='none'">
      </div>
      
      <?php if (!empty($config["conf_descripcion_parcial"])): ?>
        <div class="descripcion-parcial">
          <?= nl2br(htmlspecialchars($config["conf_descripcion_parcial"], ENT_QUOTES, 'UTF-8')); ?>
        </div>
      <?php endif; ?>
      
      <div class="estudiante-info">
        <i class="fa fa-user"></i> ESTUDIANTE: <?= htmlspecialchars(Estudiantes::NombreCompletoDelEstudiante($datosEstudianteActual), ENT_QUOTES, 'UTF-8'); ?>
      </div>
    </div>

    <div class="table-container">
      <table class="informe-table">
        <thead>
          <tr>
            <th>Código</th>
            <th>Docente</th>
            <th>Asignatura</th>
            <th>%</th>
            <th>Nota</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($todasLasCargas)): ?>
            <tr>
              <td colspan="5" style="text-align: center; padding: 20px; color: #888;">
                No hay calificaciones registradas para este período
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($todasLasCargas as $carga): ?>
              <tr class="<?= $carga['no_suma_promedio'] ? 'no-suma-promedio' : ''; ?>">
                <td><?= !empty($carga['car_id']) ? htmlspecialchars($carga['car_id'], ENT_QUOTES, 'UTF-8') : '&nbsp;'; ?></td>
                <td><?= htmlspecialchars($carga['docente'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                  <?= htmlspecialchars($carga['materia'], ENT_QUOTES, 'UTF-8'); ?>
                  <?php if ($carga['no_suma_promedio']): ?>
                    <br><span class="badge-no-promedio" title="Esta materia no se incluye en el promedio general">
                      <i class="fa fa-info-circle"></i> No suma al promedio
                    </span>
                  <?php endif; ?>
                </td>
                <td><?= number_format((float)$carga['porcentaje'], 0); ?>%</td>
                <td style="color: <?= $carga['color']; ?>;">
                  <?= htmlspecialchars($carga['nota'], ENT_QUOTES, 'UTF-8'); ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4" style="text-align: right;">PROMEDIO GENERAL</td>
            <td style="color: <?= $colorPromedio; ?>; font-size: 15px; text-align: center;">
              <?= htmlspecialchars($promedioGFinal, ENT_QUOTES, 'UTF-8'); ?>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="firmas-section">
      <div class="firmas-row">
        <div class="firma-box">
          <div class="firma-line"></div>
          Coordinador(a) Académico(a)
        </div>
        <div class="firma-box">
          <div class="firma-line"></div>
          Director(a) De Grupo
        </div>
      </div>
    </div>

    <div class="texto-acudiente">
      <p style="margin-bottom: 15px;">
        Yo _______________________________________________________________
      </p>
      <p>
        Doy constancia de haber recibido del <strong><?= htmlspecialchars($informacion_inst["info_nombre"], ENT_QUOTES, 'UTF-8'); ?></strong> el
        informe académico parcial de mi acudido y a la vez la citación
        respectiva para la reunión en donde se me informará las causas y
        recomendaciones del bajo desempeño, establecidas por la comisión de
        evaluación y promoción.
      </p>
      <div class="linea-firma">
        Firma Del Padre Y/O Acudiente
      </div>
    </div>

    <div class="versiculo">
      En el Señor, pon tu confianza. Salmos 11:01
    </div>

    <div class="footer-sintia">
      <img src="https://main.plataformasintia.com/app-sintia/main-app/sintia-logo-2023.png" 
           alt="SINTIA"
           onerror="this.style.display='none'"><br>
      SINTIA - SISTEMA INTEGRAL DE GESTIÓN INSTITUCIONAL<br>
      <?= $fechaHoy; ?>
    </div>
  </div>

  <script>
    // Mejorar la experiencia de impresión
    function imprimirInforme() {
      window.print();
    }
    
    // Atajo de teclado para imprimir
    document.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        window.print();
      }
    });
  </script>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
</html>

<?php
include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
?>
