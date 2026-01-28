<?php 
include("session.php");
$idPaginaInterna = 'DT0032';
require_once(ROOT_PATH."/main-app/class/CargaAcademicaOptimizada.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");

// Verificar permisos
if(!Modulos::validarSubRol([$idPaginaInterna])){
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

// Obtener nombre de la institución
try {
    $consultaInfo = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".general_informacion 
        WHERE info_institucion='" . $config['conf_id_institucion'] . "' AND info_year='" . $_SESSION["bd"] . "'");
    $datosInfo = mysqli_fetch_array($consultaInfo, MYSQLI_BOTH);
    $nombreInstitucion = !empty($datosInfo['info_nombre']) ? $datosInfo['info_nombre'] : 'Institución Educativa';
} catch (Exception $e) {
    $nombreInstitucion = 'Institución Educativa';
}

// Obtener todas las cargas académicas
$selectSql = [
    "car.car_id",
    "car.car_periodo",
    "car.car_curso",
    "car.car_ih",
    "car.car_docente",
    "gra.gra_nombre",
    "gru.gru_nombre",
    "am.mat_nombre",
    "car.car_grupo",
    "uss.uss_nombre",
    "uss.uss_nombre2",
    "uss.uss_apellido1",
    "uss.uss_apellido2"
];

$busqueda = CargaAcademicaOptimizada::listarCargasOptimizado($conexion, $config, "", "", "gra.gra_id, gru.gru_nombre, uss.uss_apellido1, uss.uss_apellido2, am.mat_nombre", "", "", array(), $selectSql);

$cargas = [];
$cargasProcesadas = []; // Para evitar duplicados por car_id

while ($fila = $busqueda->fetch_assoc()) {
    $cargaId = $fila['car_id'];
    
    // Evitar procesar la misma carga dos veces
    if (isset($cargasProcesadas[$cargaId])) {
        continue;
    }
    
    $periodo = $fila['car_periodo'];
    
    // Calcular porcentajes de progreso
    $datos = CargaAcademicaOptimizada::obtenerDatosAdicionalesCarga($config, $cargaId, $periodo);
    
    $fila['actividades_declaradas'] = round($datos['actividades_totales'], 1);
    $fila['actividades_registradas'] = round($datos['actividades_registradas'], 1);
    $fila['docente_nombre'] = trim(UsuariosPadre::nombreCompletoDelUsuario($fila));
    
    // Marcar como procesada usando car_id como clave única
    $cargasProcesadas[$cargaId] = true;
    $cargas[] = $fila;
}

// Agrupar por docente para calcular totales
$docentes = [];
$docenteIndexMap = []; // Mapea: nombre_normalizado => índice_en_docentes
$indexCounter = 0;

foreach ($cargas as $carga) {
    // Validar que la carga tenga docente ID válido
    if (empty($carga['car_docente'])) {
        continue; // Solo saltar si no hay ID de docente
    }
    
    // Construir nombre del docente si no existe o está vacío
    $docenteNombre = trim($carga['docente_nombre']);
    if (empty($docenteNombre)) {
        // Construir desde los campos individuales
        $nombreParts = [];
        if (!empty($carga['uss_nombre'])) $nombreParts[] = trim($carga['uss_nombre']);
        if (!empty($carga['uss_nombre2'])) $nombreParts[] = trim($carga['uss_nombre2']);
        if (!empty($carga['uss_apellido1'])) $nombreParts[] = trim($carga['uss_apellido1']);
        if (!empty($carga['uss_apellido2'])) $nombreParts[] = trim($carga['uss_apellido2']);
        $docenteNombre = !empty($nombreParts) ? implode(' ', $nombreParts) : 'DOCENTE SIN NOMBRE';
    }
    
    // Normalizar nombre para agrupación (mayúsculas, sin espacios extra)
    $nombreNormalizado = strtoupper(trim(preg_replace('/\s+/', ' ', $docenteNombre)));
    $docenteId = trim($carga['car_docente']);
    
    // Buscar si este docente ya existe
    // Primero buscar por ID del docente
    $docenteIndex = null;
    foreach ($docentes as $idx => $doc) {
        if ($doc['id'] === $docenteId) {
            $docenteIndex = $idx;
            // Actualizar nombre si está mejor
            if (empty($doc['nombre']) || $doc['nombre'] === 'DOCENTE SIN NOMBRE') {
                $docentes[$idx]['nombre'] = $docenteNombre;
                $docentes[$idx]['nombre_normalizado'] = $nombreNormalizado;
            }
            break;
        }
    }
    
    // Si no se encontró por ID, buscar por nombre normalizado
    if ($docenteIndex === null && isset($docenteIndexMap[$nombreNormalizado])) {
        $docenteIndex = $docenteIndexMap[$nombreNormalizado];
        // Verificar que el ID coincida también
        if ($docentes[$docenteIndex]['id'] !== $docenteId) {
            $docenteIndex = null; // No es el mismo docente
        }
    }
    
    // Si no existe, crear nuevo docente
    if ($docenteIndex === null) {
        $docenteIndex = $indexCounter++;
        $docenteIndexMap[$nombreNormalizado] = $docenteIndex;
        $docentes[$docenteIndex] = [
            'id' => $docenteId,
            'nombre' => $docenteNombre,
            'nombre_normalizado' => $nombreNormalizado,
            'cargas' => [],
            'total_declaradas' => 0,
            'total_registradas' => 0,
            'cargas_incompletas' => 0
        ];
    }
    
    // Verificar que esta carga no esté ya en la lista del docente
    $cargaYaExiste = false;
    foreach ($docentes[$docenteIndex]['cargas'] as $cargaExistente) {
        if ($cargaExistente['car_id'] === $carga['car_id']) {
            $cargaYaExiste = true;
            break;
        }
    }
    
    if (!$cargaYaExiste) {
        $docentes[$docenteIndex]['cargas'][] = $carga;
        
        // Sumar porcentajes
        if ($carga['actividades_declaradas'] > 0) {
            $docentes[$docenteIndex]['total_declaradas'] += $carga['actividades_declaradas'];
            $docentes[$docenteIndex]['total_registradas'] += $carga['actividades_registradas'];
            
            // Contar cargas incompletas
            if ($carga['actividades_registradas'] < 100) {
                $docentes[$docenteIndex]['cargas_incompletas']++;
            }
        }
    }
}

// Calcular promedio por docente
foreach ($docentes as $docenteIndex => &$docente) {
    $numCargas = count($docente['cargas']);
    if ($numCargas > 0) {
        $docente['promedio_declaradas'] = round($docente['total_declaradas'] / $numCargas, 1);
        $docente['promedio_registradas'] = round($docente['total_registradas'] / $numCargas, 1);
    } else {
        $docente['promedio_declaradas'] = 0;
        $docente['promedio_registradas'] = 0;
    }
}

// Headers para imprimir
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Progreso de Calificaciones - <?=$nombreInstitucion;?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            body { margin: 0; padding: 10px; }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2d3e50;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #2d3e50;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .header h2 {
            font-size: 18px;
            color: #555;
            font-weight: 600;
        }
        
        .header .fecha {
            font-size: 14px;
            color: #777;
            margin-top: 10px;
        }
        
        .resumen {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .resumen-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .resumen-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        
        .resumen-item strong {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }
        
        .resumen-item .valor {
            font-size: 28px;
            font-weight: 700;
            color: #2d3e50;
        }
        
        .table-container {
            overflow-x: auto;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        
        thead {
            background: #2d3e50;
            color: white;
        }
        
        thead th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            border: 1px solid #1a252f;
        }
        
        tbody td {
            padding: 10px 8px;
            border: 1px solid #dee2e6;
            font-size: 11px;
        }
        
        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        tbody tr:hover {
            background: #e9ecef;
        }
        
        .incompleto {
            background-color: #fff3cd !important;
            font-weight: 600;
        }
        
        .incompleto .porcentaje {
            color: #d63031;
        }
        
        .completo .porcentaje {
            color: #27ae60;
        }
        
        .porcentaje {
            font-weight: 700;
            font-size: 12px;
        }
        
        .docente-resumen {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        
        .docente-resumen.incompleto {
            background: #fff3cd;
            border-left-color: #f39c12;
        }
        
        .docente-resumen strong {
            font-size: 13px;
            color: #2d3e50;
        }
        
        .actions {
            text-align: center;
            margin-bottom: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            background: #2d3e50;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .btn-print {
            background: #41c1ba;
        }
        
        .btn-print:hover {
            background: #35a39d;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            border-left: 4px solid;
        }
        
        .alert-warning {
            background: #fff3cd;
            border-left-color: #f39c12;
            color: #856404;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #2d3e50;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 INFORME DE PROGRESO DE CALIFICACIONES</h1>
            <h2><?=$nombreInstitucion;?></h2>
            <div class="fecha">
                Generado el <?=date('d/m/Y');?> a las <?=date('H:i:s');?>
            </div>
        </div>
        
        <div class="actions no-print">
            <button onclick="window.print()" class="btn btn-print">
                <i class="fa fa-print"></i> Imprimir
            </button>
            <a href="cargas.php" class="btn">
                <i class="fa fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <?php
        // Calcular estadísticas generales
        $totalCargas = count($cargas);
        $cargasIncompletas = 0;
        $totalDocentes = count($docentes);
        $docentesIncompletos = 0;
        
        foreach ($cargas as $carga) {
            if ($carga['actividades_registradas'] < 100) {
                $cargasIncompletas++;
            }
        }
        
        foreach ($docentes as $docente) {
            if ($docente['cargas_incompletas'] > 0) {
                $docentesIncompletos++;
            }
        }
        ?>
        
        <div class="resumen">
            <div class="resumen-grid">
                <div class="resumen-item">
                    <strong>Total de Cargas Académicas</strong>
                    <div class="valor"><?=$totalCargas;?></div>
                    <div style="font-size: 10px; color: #777; margin-top: 5px;">Todas las asignaturas</div>
                </div>
                <div class="resumen-item">
                    <strong>Cargas Completas (100%)</strong>
                    <div class="valor" style="color: #27ae60;"><?=($totalCargas - $cargasIncompletas);?></div>
                    <div style="font-size: 10px; color: #777; margin-top: 5px;">Con notas registradas completas</div>
                </div>
                <div class="resumen-item">
                    <strong>Cargas Incompletas</strong>
                    <div class="valor" style="color: #e74c3c;"><?=$cargasIncompletas;?></div>
                    <div style="font-size: 10px; color: #777; margin-top: 5px;">Asignaturas sin completar notas</div>
                </div>
                <div class="resumen-item">
                    <strong>Total de Docentes</strong>
                    <div class="valor"><?=$totalDocentes;?></div>
                    <div style="font-size: 10px; color: #777; margin-top: 5px;">Con cargas asignadas</div>
                </div>
                <div class="resumen-item">
                    <strong>Docentes Pendientes</strong>
                    <div class="valor" style="color: #f39c12;"><?=$docentesIncompletos;?></div>
                    <div style="font-size: 10px; color: #777; margin-top: 5px;">Con al menos 1 carga incompleta</div>
                </div>
            </div>
        </div>
        
        <?php if ($cargasIncompletas > 0) { ?>
        <div class="alert alert-warning">
            <strong>⚠️ Atención:</strong> Se encontraron <strong><?=$cargasIncompletas;?></strong> carga(s) académica(s) que no han completado el 100% de notas registradas, 
            distribuidas en <strong><?=$docentesIncompletos;?></strong> docente(s). 
            <em>(Nota: Un docente puede tener múltiples cargas incompletas)</em>
        </div>
        <?php } ?>
        
        <h2 class="section-title">📊 RESUMEN GENERAL</h2>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Docente</th>
                        <th>Curso</th>
                        <th>Grupo</th>
                        <th>Asignatura</th>
                        <th>Periodo</th>
                        <th class="text-center">Declaradas</th>
                        <th class="text-center">Registradas</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $contReg = 1;
                    foreach ($cargas as $carga) {
                        $completa = $carga['actividades_registradas'] >= 100;
                        $claseFila = $completa ? '' : 'incompleto';
                    ?>
                    <tr class="<?=$claseFila;?>">
                        <td><?=$contReg;?></td>
                        <td><strong><?=strtoupper((string)($carga['docente_nombre'] ?? ''));?></strong></td>
                        <td><?=strtoupper((string)($carga['gra_nombre'] ?? ''));?></td>
                        <td><?=strtoupper((string)($carga['gru_nombre'] ?? ''));?></td>
                        <td><?=strtoupper((string)($carga['mat_nombre'] ?? ''));?></td>
                        <td class="text-center"><?=$carga['car_periodo'];?></td>
                        <td class="text-center"><?=$carga['actividades_declaradas'];?>%</td>
                        <td class="text-center">
                            <span class="porcentaje <?=($completa ? 'completo' : 'incompleto');?>">
                                <?=$carga['actividades_registradas'];?>%
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ($completa) { ?>
                                <span style="color: #27ae60; font-weight: 600;">✓</span>
                            <?php } else { ?>
                                <span style="color: #d63031; font-weight: 600;">⚠</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                        $contReg++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #dee2e6; text-align: center; color: #777; font-size: 11px;">
            <p>Informe generado por el sistema SINTIA - <?=date('d/m/Y H:i:s');?></p>
        </div>
    </div>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</body>
</html>

