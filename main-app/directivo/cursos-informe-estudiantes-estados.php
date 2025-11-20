<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0062';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

// Asegurar que tenemos la conexión PDO
if(!isset($conexionPDO)){
    $conexionPDO = Conexion::newConnection('PDO');
}

// Estados de matrícula
$estadosMatriculas = [
    Estudiantes::ESTADO_MATRICULADO => 'Matriculado',
    Estudiantes::ESTADO_ASISTENTE => 'Asistente',
    Estudiantes::ESTADO_CANCELADO => 'Cancelado',
    Estudiantes::ESTADO_NO_MATRICULADO => 'No Matriculado',
    Estudiantes::ESTADO_EN_INSCRIPCION => 'En Inscripción'
];

// Obtener todos los cursos
$consultaCursos = Grados::listarGrados(1);
$cursos = [];
while ($curso = mysqli_fetch_array($consultaCursos, MYSQLI_BOTH)) {
    $cursos[] = $curso;
}

// Obtener estadísticas por curso
$estadisticas = [];
foreach ($cursos as $curso) {
    $cursoId = $curso['gra_id'];
    $cursoNombre = $curso['gra_nombre'];
    
    // Inicializar contadores por estado
    $contadores = [];
    foreach ($estadosMatriculas as $estadoId => $estadoNombre) {
        $contadores[$estadoId] = 0;
    }
    $total = 0;
    
    // Consultar estudiantes por estado para este curso
    // Filtrar por grado y unir con academico_grados para filtrar por institucion y year
    $sql = "SELECT mat.mat_estado_matricula, COUNT(*) AS cantidad
            FROM " . BD_ACADEMICA . ".academico_matriculas mat
            INNER JOIN " . BD_ACADEMICA . ".academico_grados gra ON gra.gra_id = mat.mat_grado
            WHERE mat.mat_grado = ? 
              AND mat.mat_eliminado = 0
              AND gra.institucion = ? 
              AND gra.year = ?
            GROUP BY mat.mat_estado_matricula";
    
    $stmt = $conexionPDO->prepare($sql);
    $stmt->execute([$cursoId, $config['conf_id_institucion'], $_SESSION["bd"]]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($resultados as $fila) {
        $estado = (int)$fila['mat_estado_matricula'];
        $cantidad = (int)$fila['cantidad'];
        if (isset($contadores[$estado])) {
            $contadores[$estado] = $cantidad;
            $total += $cantidad;
        }
    }
    
    $estadisticas[] = [
        'curso_id' => $cursoId,
        'curso_nombre' => $cursoNombre,
        'contadores' => $contadores,
        'total' => $total
    ];
}

// Calcular totales generales
$totalesGenerales = [];
foreach ($estadosMatriculas as $estadoId => $estadoNombre) {
    $totalesGenerales[$estadoId] = 0;
}
$totalGeneral = 0;

foreach ($estadisticas as $estadistica) {
    foreach ($estadistica['contadores'] as $estadoId => $cantidad) {
        $totalesGenerales[$estadoId] += $cantidad;
    }
    $totalGeneral += $estadistica['total'];
}

// Obtener información de la institución desde la sesión (ya cargada al autenticar)
$infoInstitucion = isset($_SESSION["informacionInstConsulta"]) ? $_SESSION["informacionInstConsulta"] : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Estudiantes por Estados - Cursos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header .institucion {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .header .fecha {
            font-size: 12px;
            color: #95a5a6;
        }
        
        .info-box {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .info-box p {
            margin: 5px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }
        
        thead {
            background: #3498db;
            color: white;
        }
        
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        
        th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        
        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        tbody tr:hover {
            background: #e8f4f8;
        }
        
        .curso-nombre {
            text-align: left;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .total-row {
            background: #2c3e50 !important;
            color: white;
            font-weight: bold;
        }
        
        .total-row td {
            border-color: #1a252f;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #95a5a6;
            font-style: italic;
        }
        
        .btn-print {
            text-align: center;
            margin: 20px 0;
        }
        
        .btn-print button {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-print button:hover {
            background: #2980b9;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                padding: 20px;
            }
            
            .btn-print {
                display: none;
            }
            
            thead {
                background: #3498db !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .total-row {
                background: #2c3e50 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Informe de Estudiantes por Estados por Curso</h1>
            <?php if (!empty($infoInstitucion['info_nombre'])) { ?>
                <div class="institucion"><?= htmlspecialchars($infoInstitucion['info_nombre']); ?></div>
            <?php } ?>
            <div class="fecha">Año: <?= $_SESSION["bd"]; ?> | Fecha de generación: <?= date('d/m/Y H:i:s'); ?></div>
        </div>
        
        <div class="info-box">
            <p><strong>Descripción:</strong> Este informe muestra la cantidad de estudiantes por cada estado de matrícula agrupados por curso. <em>Nota: Los estudiantes eliminados no se incluyen en el conteo.</em></p>
            <p><strong>Total de cursos:</strong> <?= count($cursos); ?> | <strong>Total de estudiantes (sin eliminados):</strong> <?= number_format($totalGeneral); ?></p>
        </div>
        
        <div class="btn-print">
            <button onclick="window.print()">
                <i class="fa fa-print"></i> Imprimir Informe
            </button>
        </div>
        
        <?php if (count($estadisticas) > 0) { ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 200px;">Curso</th>
                    <?php foreach ($estadosMatriculas as $estadoId => $estadoNombre) { ?>
                        <th><?= htmlspecialchars($estadoNombre); ?></th>
                    <?php } ?>
                    <th style="background: #27ae60;">Total</th>
                    <th style="background: #e67e22;">% del Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estadisticas as $estadistica) { 
                    // Calcular porcentaje del total general
                    $porcentaje = $totalGeneral > 0 ? ($estadistica['total'] / $totalGeneral) * 100 : 0;
                ?>
                    <tr>
                        <td class="curso-nombre"><?= htmlspecialchars($estadistica['curso_nombre']); ?></td>
                        <?php foreach ($estadosMatriculas as $estadoId => $estadoNombre) { ?>
                            <td><?= number_format($estadistica['contadores'][$estadoId]); ?></td>
                        <?php } ?>
                        <td style="font-weight: bold; background: #d5f4e6;"><?= number_format($estadistica['total']); ?></td>
                        <td style="font-weight: bold; background: #fdebd0;"><?= number_format($porcentaje, 2); ?>%</td>
                    </tr>
                <?php } ?>
                <tr class="total-row">
                    <td style="text-align: left;"><strong>TOTALES</strong></td>
                    <?php foreach ($estadosMatriculas as $estadoId => $estadoNombre) { ?>
                        <td><strong><?= number_format($totalesGenerales[$estadoId]); ?></strong></td>
                    <?php } ?>
                    <td><strong><?= number_format($totalGeneral); ?></strong></td>
                    <td><strong>100.00%</strong></td>
                </tr>
            </tbody>
        </table>
        <?php } else { ?>
            <div class="no-data">
                <p>No hay datos disponibles para generar el informe.</p>
            </div>
        <?php } ?>
    </div>
</body>
</html>

