<?php
// Configuraciones para reportes grandes
set_time_limit(300);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

include("session-compartida.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;

$idPaginaInterna = 'DT0234';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
    exit();
}

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

$filtro = '';

if (!empty($_GET["docente"])) {
    $filtro .=" AND car_docente='".mysqli_real_escape_string($conexion, $_GET["docente"])."'";
}

if (!empty($_GET["grado"])) {
    $filtro .=" AND car_curso='".mysqli_real_escape_string($conexion, $_GET["grado"])."'";
}

if (!empty($_GET["asignatura"])) {
    $filtro .=" AND car_materia='".mysqli_real_escape_string($conexion, $_GET["asignatura"])."'";
}

// Pre-cargar todas las cargas en un array para optimización
$consulta = CargaAcademica::listarCargas($conexion, $config, "", $filtro, "car_id", "");
$cargas = [];
if($consulta){
    while($row = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
        $cargas[] = $row;
    }
}
$totalCargas = count($cargas);
?>

<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Carga General de Docentes - SINTIA</title>
        <link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
        
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 11px;
                line-height: 1.5;
                color: #000;
                margin: 0;
                padding: 20px;
                background-color: #f5f5f5;
            }
            .container-cargas {
                max-width: 100%;
                margin: 0 auto;
                padding: 30px;
                background-color: #fff;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            
            /* Tabla */
            .tabla-cargas {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .tabla-cargas thead tr {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                font-weight: bold;
                height: 40px;
            }
            .tabla-cargas thead th {
                padding: 12px 8px;
                text-align: center;
                border: 1px solid rgba(255,255,255,0.2);
                font-size: 11px;
            }
            .tabla-cargas tbody tr {
                transition: background-color 0.2s ease;
            }
            .tabla-cargas tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .tabla-cargas tbody tr:hover {
                background-color: #e8f4f8;
            }
            .tabla-cargas tbody tr.director-grupo {
                background-color: #e3f2fd;
            }
            .tabla-cargas tbody tr.director-grupo:hover {
                background-color: #bbdefb;
            }
            .tabla-cargas tbody td {
                padding: 10px 8px;
                border: 1px solid #ddd;
                font-size: 11px;
            }
            .badge-director {
                background-color: #2196F3;
                color: white;
                padding: 3px 8px;
                border-radius: 3px;
                font-size: 10px;
                font-weight: bold;
            }
            
            /* Botones flotantes */
            .no-print {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                display: flex;
                gap: 10px;
            }
            .btn-print, .btn-close {
                padding: 12px 24px;
                border: none;
                border-radius: 5px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            }
            .btn-print {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            .btn-print:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
            }
            .btn-close {
                background: #f44336;
                color: white;
            }
            .btn-close:hover {
                background: #da190b;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(244, 67, 54, 0.4);
            }
            
            /* Estadísticas */
            .stats-info {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                margin: 20px 0;
                text-align: center;
                font-size: 14px;
                font-weight: 600;
            }
            
            /* Estilos de impresión */
            @media print {
                body {
                    margin: 0;
                    background-color: white;
                    padding: 0;
                }
                .container-cargas {
                    max-width: 100%;
                    box-shadow: none;
                    padding: 10px;
                }
                .no-print {
                    display: none !important;
                }
                @page {
                    size: landscape;
                    margin: 1cm;
                }
                .tabla-cargas {
                    page-break-inside: auto;
                    box-shadow: none;
                }
                .tabla-cargas thead {
                    display: table-header-group;
                }
                .tabla-cargas tr {
                    page-break-inside: avoid;
                    page-break-after: auto;
                }
                .tabla-cargas thead tr {
                    background: #667eea !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                .tabla-cargas tbody tr.director-grupo {
                    background-color: #e3f2fd !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
        </style>
    </head>

    <body style="font-family:Arial;">

        <!-- Botones de Acción -->
        <div class="no-print">
            <button class="btn-print" onclick="window.print();">
                <i class="fa fa-print"></i> Imprimir
            </button>
            <button class="btn-close" onclick="window.close();">
                <i class="fa fa-times"></i> Cerrar
            </button>
        </div>

        <div class="container-cargas">

        <?php
        $nombreInforme = "CARGA GENERAL DE DOCENTES";
        include("../compartido/head-informes.php");
        ?>
        
        <!-- Estadísticas -->
        <div class="stats-info">
            Total de Cargas Académicas: <b><?=$totalCargas?></b>
        </div>

        <?php if($totalCargas > 0){ ?>
            <table class="tabla-cargas">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>#CARGA</th>
                        <th>Docente</th>
                        <th>Grado</th>
                        <th>Grupo</th>
                        <th>Asignatura</th>
                        <th>D.G</th>
                        <th>I.H</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($cargas as $datos) {
                        $claseDirector = !empty($datos['car_director_grupo']) && $datos['car_director_grupo'] == 1 ? 'director-grupo' : '';
                        $badgeDirector = !empty($datos['car_director_grupo']) && $datos['car_director_grupo'] == 1 ? '<span class="badge-director">D.G</span>' : $opcionSINO[$datos['car_director_grupo']];
                    ?>
                    <tr class="<?=$claseDirector?>">
                        <td align="center"><?=$i?></td>
                        <td align="center"><?=htmlspecialchars(!empty($datos['car_id']) ? $datos['car_id'] : '')?></td>
                        <td><?=htmlspecialchars(!empty($datos['uss_nombre']) ? strtoupper($datos['uss_nombre']) : 'N/A')?></td>
                        <td><?=htmlspecialchars(!empty($datos['gra_nombre']) ? strtoupper($datos['gra_nombre']) : 'N/A')?></td>
                        <td align="center"><?=htmlspecialchars(!empty($datos['gru_nombre']) ? strtoupper($datos['gru_nombre']) : 'N/A')?></td>
                        <td><?=htmlspecialchars(!empty($datos['mat_nombre']) ? strtoupper($datos['mat_nombre']) : 'N/A')?></td>
                        <td align="center"><?=$badgeDirector?></td>
                        <td align="center"><?=!empty($datos['car_ih']) ? $datos['car_ih'] : 0?></td>
                    </tr>
                    <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div style="text-align: center; padding: 40px; color: #999;">
                <p style="font-size: 16px;">No se encontraron cargas académicas con los filtros aplicados.</p>
            </div>
        <?php } ?>

        </div> <!-- Cierre container-cargas -->

        <?php
        include("../compartido/footer-informes.php");
        include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); 
        ?>
        
        <script type="text/javascript">
            // Atajo de teclado para imprimir
            document.addEventListener('DOMContentLoaded', function() {
                document.addEventListener('keydown', function(e) {
                    if (e.ctrlKey && e.key === 'p') {
                        e.preventDefault();
                        window.print();
                    }
                });
            });
        </script>

    </body>
</html>